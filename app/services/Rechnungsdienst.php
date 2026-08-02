<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminProjekte;
use Sartu\Data\Admin\AdminRechnungen;
use Sartu\Data\AuditProtokoll;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\Db;
use Sartu\Helpers\Format;
use Sartu\Helpers\Validate;

/**
 * Rechnungen anlegen, senden und den Zahlungsstatus **von Hand** setzen —
 * Portal-Lastenheft §4, §5.3 und §12.
 *
 * ## Die eiserne Regel
 *
 * > „Der Zahlungsstatus wird **niemals** aus der Rückkehr des Browsers abgeleitet. Es gibt
 * > in Stufe 0 keine automatische Statusänderung durch den Zahlungsdienst."
 *
 * Dieser Dienst hat deshalb **keine** Methode, die aus einer Rückkehr-URL, einem
 * Zahlungsdienst-Kennzeichen oder einem GET-Parameter einen Zustand ableitet. Der einzige
 * Weg nach `bezahlt` führt über `zahlungEintragen()` — mit Betrag, Adminnachweis und
 * Grundlagentext.
 *
 * ## Der Grundlagentext ist kein Kommentarfeld
 *
 * §12: „**Pflichtfeld** `Grundlage der Prüfung` (Freitext, mindestens 3 Zeichen)" — und:
 * „Ohne Grundlagentext lässt sich keine dieser Änderungen speichern." Er landet als `reason`
 * im Audit-Ereignis und ist damit der Nachweis, worauf sich die Buchung stützt.
 *
 * Das gilt **für jede** Änderung an Geld und Fristen, nicht nur für „bezahlt": Stornierung,
 * Rücknahme, Änderung von `due_date`.
 *
 * ## Die Rücknahme ist eine eigene Handlung
 *
 * §12: „Ein einmal auf `bezahlt` gesetzter Status lässt sich **nicht stillschweigend**
 * zurücknehmen — die Rücknahme ist eine eigene protokollierte Aktion mit eigenem
 * Grundlagentext und erzeugt eine Benachrichtigung an den Kunden."
 */
final class Rechnungsdienst
{
    /** §4a: Zahlungsziel 10 Kalendertage ab Rechnungsdatum, als Vorbelegung. */
    public const ZAHLUNGSZIEL_TAGE = 10;

    /** §12: mindestens drei Zeichen. */
    public const GRUNDLAGE_MINDESTLAENGE = 3;

    public const MEILENSTEINE = [
        'anzahlung'    => 'Anzahlung',
        'zwischenrate' => 'Zwischenrate',
        'schlussrate'  => 'Schlussrate',
        'betrieb'      => 'Betrieb',
    ];

    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?AdminRechnungen $rechnungen = null,
        private readonly ?AuditProtokoll $audit = null,
        private readonly ?Mailversand $mail = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * Legt eine Rechnung als `entwurf` an.
     *
     * @param array<string,mixed> $eingabe
     *
     * @return array{fehler:list<string>,id:?string}
     */
    public function anlegen(string $projektId, array $eingabe, ?string $ip): array
    {
        $projekt = (new AdminProjekte($this->nachweis, $this->pdo))->finden($projektId);

        if ($projekt === null) {
            return ['fehler' => ['Dieses Projekt gibt es nicht.'], 'id' => null];
        }

        $fehler = [];
        $nummer = self::text($eingabe, 'number');
        $meilenstein = self::text($eingabe, 'milestone');
        $netto = self::zahl($eingabe, 'net_cents');

        if (preg_match('/^RE-\d{4}-\d{3,}$/', $nummer) !== 1) {
            // §4a Nummernkreis. In Stufe 0 vom Admin eingegeben.
            $fehler[] = 'Die Rechnungsnummer hat das Format RE-JJJJ-NNN, zum Beispiel RE-2026-001.';
        }

        if (!isset(self::MEILENSTEINE[$meilenstein])) {
            $fehler[] = 'Bitte wählen Sie, worauf sich die Rechnung bezieht.';
        }

        if ($netto <= 0) {
            $fehler[] = 'Der Nettobetrag muss größer als null sein.';
        }

        if ($fehler !== []) {
            return ['fehler' => $fehler, 'id' => null];
        }

        // §19 UStG: Steht `kleinunternehmer` auf ja, wird keine Umsatzsteuer ausgewiesen.
        // Der Wert kommt aus den Betreiberdaten — keine Bauentscheidung.
        $ust = Zahlungsstatus::umsatzsteuer($netto, $this->kleinunternehmer());

        $faellig = self::text($eingabe, 'due_date');

        if ($faellig === '') {
            $faellig = (new \DateTimeImmutable('now', new \DateTimeZone('Europe/Berlin')))
                ->modify('+' . self::ZAHLUNGSZIEL_TAGE . ' days')
                ->format('Y-m-d');
        }

        $id = $this->rechnungen()->anlegen([
            'project_id'         => $projektId,
            'number'             => $nummer,
            'milestone'          => $meilenstein,
            'status'             => Zahlungsstatus::ENTWURF,
            'net_cents'          => $netto,
            'vat_cents'          => $ust,
            'gross_cents'        => $netto + $ust,
            'due_date'           => $faellig,
            'mollie_payment_url' => self::textOderNull($eingabe, 'mollie_payment_url'),
            'note'               => self::textOderNull($eingabe, 'note'),
        ]);

        $this->audit()->schreiben(
            aktion: 'rechnung_angelegt',
            objektart: 'invoice',
            objektId: $id,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            organisationId: (string) $projekt['organization_id'],
            neuerWert: Zahlungsstatus::ENTWURF,
            grund: 'Rechnung ' . $nummer . ' über ' . Format::euro($netto + $ust) . ' angelegt',
            ip: $ip,
        );

        return ['fehler' => [], 'id' => $id];
    }

    /**
     * Sendet die Rechnung — §5.1a, `angebot_angenommen` → `zahlung_offen`.
     *
     * @return list<string> leer bei Erfolg
     */
    public function senden(string $rechnungId, ?string $ip): array
    {
        $rechnung = $this->rechnungen()->finden($rechnungId);

        if ($rechnung === null) {
            return ['Diese Rechnung gibt es nicht.'];
        }

        if ((string) $rechnung['status'] !== Zahlungsstatus::ENTWURF) {
            return ['Diese Rechnung ist bereits gesendet.'];
        }

        $projekt = (new AdminProjekte($this->nachweis, $this->pdo))->finden((string) $rechnung['project_id']);

        if ($projekt === null) {
            return ['Zu dieser Rechnung gibt es kein Projekt.'];
        }

        $this->rechnungen()->zustandSetzen($rechnungId, Zahlungsstatus::GESENDET);

        $this->audit()->schreiben(
            aktion: 'rechnung_gesendet',
            objektart: 'invoice',
            objektId: $rechnungId,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            organisationId: (string) $projekt['organization_id'],
            alterWert: Zahlungsstatus::ENTWURF,
            neuerWert: Zahlungsstatus::GESENDET,
            grund: 'Rechnung ' . (string) $rechnung['number'] . ' an den Kunden gesendet',
            ip: $ip,
        );

        // §5.1a: Nur die Anzahlung bewegt das Projekt. Eine Zwischen- oder Betriebsrechnung
        // laesst den Zustand, wo er ist — dafuer gibt es in der Tabelle keine Zeile.
        if ((string) $rechnung['milestone'] === 'anzahlung') {
            (new Projektwechsel(pdo: $this->pdo))->wechseln(
                (string) $projekt['id'],
                (string) $projekt['organization_id'],
                Projektstatus::ZAHLUNG_OFFEN,
                Projektstatus::ADMIN,
                $this->nachweis->adminBenutzerId,
                'Anzahlungsrechnung ' . (string) $rechnung['number'] . ' gesendet',
                $ip,
            );
        }

        $this->kundenmailSenden($projekt, 'Ihre Rechnung ' . (string) $rechnung['number'],
            'Ihre Rechnung liegt in Ihrem Bereich und ist bis zum '
            . Format::datum((string) $rechnung['due_date']) . " fällig. Sie können direkt dort bezahlen.\n");

        return [];
    }

    /**
     * Trägt einen geprüften Zahlungseingang ein — **der einzige Weg** zu `bezahlt`.
     *
     * @return list<string> leer bei Erfolg
     */
    public function zahlungEintragen(string $rechnungId, int $bezahltCent, string $grundlage, ?string $ip): array
    {
        $rechnung = $this->rechnungen()->finden($rechnungId);

        if ($rechnung === null) {
            return ['Diese Rechnung gibt es nicht.'];
        }

        if (mb_strlen(trim($grundlage)) < self::GRUNDLAGE_MINDESTLAENGE) {
            // §12, Pflichtfeld. Ohne Grundlage keine Buchung.
            return ['Bitte halten Sie fest, worauf sich die Prüfung stützt — zum Beispiel '
                . '„Mollie-Zahlung tr_xxx vom 04.08.2026" oder „Überweisung Kontoauszug 12/2026".'];
        }

        if ($bezahltCent < 0) {
            return ['Ein negativer Betrag ist keine Zahlung.'];
        }

        $vorher = (string) $rechnung['status'];
        $brutto = (int) $rechnung['gross_cents'];

        $zustand = Zahlungsstatus::ausBetrag($bezahltCent, $brutto, self::istUeberfaellig($rechnung));
        $bezahltAm = $zustand === Zahlungsstatus::BEZAHLT ? Db::jetzt() : null;

        $this->rechnungen()->zahlungSetzen(
            $rechnungId,
            $bezahltCent,
            $zustand,
            $bezahltAm,
            $bezahltCent > 0 ? $this->nachweis->adminBenutzerId : null,
        );

        $projekt = (new AdminProjekte($this->nachweis, $this->pdo))->finden((string) $rechnung['project_id']);

        $this->audit()->schreiben(
            aktion: 'zahlungsstatus_geaendert',
            objektart: 'invoice',
            objektId: $rechnungId,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            organisationId: $projekt === null ? null : (string) $projekt['organization_id'],
            alterWert: $vorher,
            neuerWert: $zustand,
            grund: trim($grundlage),
            detail: [
                'paid_cents'   => $bezahltCent,
                'gross_cents'  => $brutto,
                'ueberzahlung' => Zahlungsstatus::ueberzahlung($bezahltCent, $brutto),
            ],
            ip: $ip,
        );

        if ($projekt !== null) {
            $this->nachZahlungBenachrichtigen($projekt, $rechnung, $vorher, $zustand, $bezahltCent, $brutto);
            $this->nachAnzahlungWeiterschalten($projekt, $rechnung, $zustand, trim($grundlage), $ip);
        }

        return [];
    }

    /**
     * §5.1a: `zahlung_offen` → `briefing`, ausgeloest vom **Admin, von Hand**.
     *
     * „Nie aus der Rueckkehr des Browsers abgeleitet. Audit mit `reason` als Pflichtfeld."
     * Der Grundlagentext der Buchung ist genau dieser `reason` — er wird durchgereicht und
     * nicht durch einen zweiten, allgemeineren ersetzt.
     *
     * Zugleich entsteht die Aufgabenliste (§8.3). Ohne sie sieht der Kunde nach der Zahlung
     * einen leeren Bereich und ruft an.
     *
     * @param array<string,mixed> $projekt
     * @param array<string,mixed> $rechnung
     */
    private function nachAnzahlungWeiterschalten(
        array $projekt,
        array $rechnung,
        string $zustand,
        string $grundlage,
        ?string $ip,
    ): void {
        if ($zustand !== Zahlungsstatus::BEZAHLT || (string) $rechnung['milestone'] !== 'anzahlung') {
            return;
        }

        $fehler = (new Projektwechsel(pdo: $this->pdo))->wechseln(
            (string) $projekt['id'],
            (string) $projekt['organization_id'],
            Projektstatus::BRIEFING,
            Projektstatus::ADMIN,
            $this->nachweis->adminBenutzerId,
            $grundlage,
            $ip,
        );

        if ($fehler === null) {
            Aufgabenvorlage::anlegen($this->nachweis, (string) $projekt['id'], $this->pdo);
        }
    }

    /**
     * Storniert — §12, eigene protokollierte Aktion mit eigenem Grundlagentext.
     *
     * @return list<string> leer bei Erfolg
     */
    public function stornieren(string $rechnungId, string $grundlage, ?string $ip): array
    {
        $rechnung = $this->rechnungen()->finden($rechnungId);

        if ($rechnung === null) {
            return ['Diese Rechnung gibt es nicht.'];
        }

        if (mb_strlen(trim($grundlage)) < self::GRUNDLAGE_MINDESTLAENGE) {
            return ['Bitte halten Sie fest, warum die Rechnung storniert wird.'];
        }

        $vorher = (string) $rechnung['status'];

        $this->rechnungen()->zustandSetzen($rechnungId, Zahlungsstatus::STORNIERT);

        $projekt = (new AdminProjekte($this->nachweis, $this->pdo))->finden((string) $rechnung['project_id']);

        $this->audit()->schreiben(
            aktion: 'zahlungsstatus_geaendert',
            objektart: 'invoice',
            objektId: $rechnungId,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            organisationId: $projekt === null ? null : (string) $projekt['organization_id'],
            alterWert: $vorher,
            neuerWert: Zahlungsstatus::STORNIERT,
            grund: trim($grundlage),
            ip: $ip,
        );

        return [];
    }

    public function zahlungslinkSetzen(string $rechnungId, string $adresse, ?string $ip): array
    {
        $rechnung = $this->rechnungen()->finden($rechnungId);

        if ($rechnung === null) {
            return ['Diese Rechnung gibt es nicht.'];
        }

        $adresse = trim($adresse);

        if ($adresse !== '' && !str_starts_with($adresse, 'https://')) {
            return ['Der Zahlungslink muss mit https:// beginnen.'];
        }

        $this->rechnungen()->zahlungslinkSetzen($rechnungId, $adresse === '' ? null : $adresse);

        $this->audit()->schreiben(
            aktion: 'zahlungslink_gesetzt',
            objektart: 'invoice',
            objektId: $rechnungId,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            ip: $ip,
        );

        return [];
    }

    // ------------------------------------------------------------------ intern

    /** §5.3: überfällig heisst `due_date < heute` und Restbetrag offen. */
    public static function istUeberfaellig(array $rechnung): bool
    {
        $faellig = $rechnung['due_date'] ?? null;

        if (!is_string($faellig) || $faellig === '') {
            return false;
        }

        return $faellig < (new \DateTimeImmutable('now', new \DateTimeZone('Europe/Berlin')))->format('Y-m-d')
            && (int) ($rechnung['paid_cents'] ?? 0) < (int) ($rechnung['gross_cents'] ?? 0);
    }

    /** @param array<string,mixed> $projekt @param array<string,mixed> $rechnung */
    private function nachZahlungBenachrichtigen(
        array $projekt,
        array $rechnung,
        string $vorher,
        string $nachher,
        int $bezahlt,
        int $brutto,
    ): void {
        // §10 und §12 — drei Fälle, drei Wortlaute.
        if ($vorher === Zahlungsstatus::BEZAHLT && $nachher !== Zahlungsstatus::BEZAHLT) {
            $this->kundenmailSenden($projekt, 'Korrektur zu Rechnung ' . (string) $rechnung['number'],
                'Wir haben den Zahlungsstatus der Rechnung ' . (string) $rechnung['number']
                . " korrigiert. Bitte prüfen Sie den Stand in Ihrem Bereich.\n");

            return;
        }

        if ($nachher === Zahlungsstatus::BEZAHLT) {
            $this->kundenmailSenden($projekt, 'Zahlungseingang bestätigt',
                "Wir haben Ihre Zahlung erhalten. Vielen Dank.\n");

            return;
        }

        if ($bezahlt > 0) {
            $this->kundenmailSenden($projekt, 'Teilzahlung erhalten',
                'Wir haben ' . Format::euro($bezahlt) . ' erhalten. Offen sind noch '
                . Format::euro(Zahlungsstatus::restbetrag($bezahlt, $brutto)) . ".\n");
        }
    }

    /** @param array<string,mixed> $projekt */
    private function kundenmailSenden(array $projekt, string $betreff, string $kern): void
    {
        $empfaenger = $this->kundenadresse((string) $projekt['organization_id']);

        if ($empfaenger === null) {
            return;
        }

        try {
            ($this->mail ?? new Mailversand())->senden(
                $empfaenger,
                $betreff,
                "Guten Tag,\n\n" . $kern . "\nFreundliche Grüße\nSARTU\n",
            );
        } catch (\Throwable) {
            // Eine gescheiterte Mail nimmt keine Buchung zurück. §6.3 haelt den Notweg bereit.
        }
    }

    private function kundenadresse(string $organisationId): ?string
    {
        $organisation = (new \Sartu\Data\Admin\AdminOrganisationen($this->nachweis, $this->pdo))
            ->finden($organisationId);

        $adresse = $organisation['contact_email'] ?? null;

        return is_string($adresse) && Validate::email($adresse) ? $adresse : null;
    }

    private function kleinunternehmer(): bool
    {
        try {
            $daten = (new BetreiberdatenSpeicher($this->pdo))->lesen();
        } catch (\Throwable) {
            return false;
        }

        return (int) ($daten['kleinunternehmer'] ?? 0) === 1;
    }

    private static function text(array $eingabe, string $feld): string
    {
        $wert = $eingabe[$feld] ?? '';

        return is_scalar($wert) ? trim((string) $wert) : '';
    }

    private static function textOderNull(array $eingabe, string $feld): ?string
    {
        $wert = self::text($eingabe, $feld);

        return $wert === '' ? null : $wert;
    }

    private static function zahl(array $eingabe, string $feld): int
    {
        $wert = $eingabe[$feld] ?? 0;

        return is_numeric($wert) ? (int) $wert : 0;
    }

    private function rechnungen(): AdminRechnungen
    {
        return $this->rechnungen ?? new AdminRechnungen($this->nachweis, $this->pdo);
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll($this->pdo);
    }
}
