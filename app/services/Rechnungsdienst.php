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
 *
 * Sie heißt `zahlungZuruecknehmen()` und trägt eine **eigene** Protokollaktion. Bis zum
 * 09.08.2026 beschrieb dieser Kommentar sie, ohne dass es sie gab — wer sie brauchte, trug
 * eine Zahlung über null ein, und im Protokoll war das von einer Buchung nicht zu
 * unterscheiden. Dasselbe galt für `faelligkeitAendern()`.
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
        private readonly ?Versender $mail = null,
        private readonly ?Nummernkreis $nummernkreis = null,
        private readonly ?Belegerzeugung $belegerzeugung = null,
        private readonly ?Zahlungsdienst $zahlungsdienst = null,
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
        $meilenstein = self::text($eingabe, 'milestone');
        $netto = self::zahl($eingabe, 'net_cents');

        // **Die Nummer wird nicht mehr eingegeben.** Bis zum 09.08.2026 stand hier eine
        // Musterprüfung auf ein Feld aus dem Formular; `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 4
        // hat das abgelöst: „Bis heute tippte der Admin die Nummer ein. Das fällt."
        // Sie entsteht weiter unten, in derselben Transaktion wie die Zeile.

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
            $faellig = Format::inTagen(self::ZAHLUNGSZIEL_TAGE);
        }

        // §4: Nummer und Beleg entstehen in **einer** Transaktion. Die Klammer gibt die
        // Nummer nur nach innen — es gibt keinen Weg, sie zu bekommen und den Beleg später
        // anzulegen.
        $nummer = '';

        $id = $this->nummernkreis()->vergeben(
            'RE',
            function (string $vergeben) use (&$nummer, $projektId, $meilenstein, $netto, $ust, $faellig, $eingabe): string {
                $nummer = $vergeben;

                return $this->rechnungen()->anlegen([
                    'project_id'         => $projektId,
                    'number'             => $vergeben,
                    'milestone'          => $meilenstein,
                    'status'             => Zahlungsstatus::ENTWURF,
                    'net_cents'          => $netto,
                    'vat_cents'          => $ust,
                    'gross_cents'        => $netto + $ust,
                    'due_date'           => $faellig,
                    'mollie_payment_url' => self::textOderNull($eingabe, 'mollie_payment_url'),
                    'note'               => self::textOderNull($eingabe, 'note'),
                ]);
            },
        );

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

        // §2 und §3: Mit dem Versand entsteht das Ausstellungsdatum, und aus ihm der Beleg.
        // **Erst der Beleg, dann der Zustand.** Fällt die Prüfung nach EN 16931 durch, bleibt
        // die Rechnung ein Entwurf — §3: „Es gibt keinen Weg, eine ungültige Rechnung zu
        // senden, auch nicht mit Bestätigung."
        $ausgestellt = Db::jetzt();
        $beleg = $this->belegErzeugen(['issued_at' => $ausgestellt] + $rechnung, $projekt);

        if ($beleg['fehler'] !== []) {
            return $beleg['fehler'];
        }

        $this->rechnungen()->ausstellen($rechnungId, Zahlungsstatus::GESENDET, $ausgestellt);

        // §6 Schritt 1 und 2: Zahlung beim Dienst anlegen, Adresse an der Rechnung merken.
        $this->zahlungVorbereiten($rechnungId, $rechnung, $ip);

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

        if ($fehler !== null) {
            return;
        }

        $entstanden = Aufgabenvorlage::anlegen($this->nachweis, (string) $projekt['id'], $this->pdo);

        // §10, Zeile „Neue Aufgaben". Sie fehlte bis zum 02.08.2026 — der Kunde bekam eine
        // Aufgabenliste, von der ihm niemand erzählte.
        //
        // **An der Zahl, nicht am Zustandswechsel.** Entstand keine Aufgabe, gibt es nichts
        // zu erledigen, und eine Mail darüber wäre eine Aufforderung ins Leere.
        if ($entstanden > 0) {
            $this->kundenmailSenden($projekt, Mailtexte::AUFGABEN_BETREFF, Mailtexte::aufgaben());
        }
    }

    /**
     * Storniert — §12, eigene protokollierte Aktion mit eigenem Grundlagentext.
     *
     * @return list<string> leer bei Erfolg
     */
    /**
     * Hebt eine Rechnung auf — `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 5.
     *
     * ## Zwei Wege, und der Zustand entscheidet, welcher gilt
     *
     * | Zustand | Was geschieht |
     * |---|---|
     * | `entwurf` | **verworfen.** Die Nummer bleibt vergeben und wird nie wieder benutzt |
     * | versendet | **Stornorechnung** mit eigener Nummer und negativen Beträgen. Beide Belege bleiben abrufbar |
     *
     * §5: „Eine **versendete** Rechnung lässt sich nicht verwerfen." Bis zum 09.08.2026 setzte
     * diese Methode in beiden Fällen denselben Status — das genügt für einen Entwurf und
     * nicht für einen Beleg, den ein Kunde in der Hand hat.
     *
     * **Warum die Nummer eines verworfenen Entwurfs vergeben bleibt.** §4: „Ein vergebener
     * und dann verworfener Beleg ist **kein** Grund, die Nummer zu überspringen." Eine Lücke
     * im Nummernkreis ist bei einer Betriebsprüfung erklärungsbedürftig; ein verworfener
     * Beleg ist es nicht.
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
            return ['Bitte halten Sie fest, warum die Rechnung aufgehoben wird.'];
        }

        $vorher = (string) $rechnung['status'];

        if (in_array($vorher, [Zahlungsstatus::VERWORFEN, Zahlungsstatus::STORNIERT], true)) {
            return ['Diese Rechnung ist bereits aufgehoben.'];
        }

        return $vorher === Zahlungsstatus::ENTWURF
            ? $this->entwurfVerwerfen($rechnung, trim($grundlage), $ip)
            : $this->stornorechnungAnlegen($rechnung, trim($grundlage), $ip);
    }

    /**
     * Der erste Weg — ein Entwurf, den nie jemand gesehen hat.
     *
     * Kein neuer Beleg, kein Dokument, keine Mail. Er ist nie hinausgegangen.
     *
     * @param array<string,mixed> $rechnung
     * @return list<string>
     */
    private function entwurfVerwerfen(array $rechnung, string $grundlage, ?string $ip): array
    {
        $rechnungId = (string) $rechnung['id'];

        $this->rechnungen()->zustandSetzen($rechnungId, Zahlungsstatus::VERWORFEN);

        $projekt = (new AdminProjekte($this->nachweis, $this->pdo))->finden((string) $rechnung['project_id']);

        $this->audit()->schreiben(
            aktion: 'rechnung_verworfen',
            objektart: 'invoice',
            objektId: $rechnungId,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            organisationId: $projekt === null ? null : (string) $projekt['organization_id'],
            alterWert: Zahlungsstatus::ENTWURF,
            neuerWert: Zahlungsstatus::VERWORFEN,
            grund: $grundlage,
            // Die Nummer bleibt vergeben. Sie steht im Protokoll, damit später erklärbar
            // ist, warum sie in keiner Rechnung auftaucht.
            detail: ['nummer_bleibt_vergeben' => (string) $rechnung['number']],
            ip: $ip,
        );

        return [];
    }

    /**
     * Der zweite Weg — eine versendete Rechnung wird durch einen neuen Beleg aufgehoben.
     *
     * Die Stornorechnung ist eine **eigene Zeile** mit eigener Nummer aus dem Kreis `ST`,
     * negativen Beträgen und einem Verweis auf die aufgehobene Rechnung. Beide bleiben
     * abrufbar; gelöscht wird nichts.
     *
     * @param array<string,mixed> $rechnung
     * @return list<string>
     */
    private function stornorechnungAnlegen(array $rechnung, string $grundlage, ?string $ip): array
    {
        $projekt = (new AdminProjekte($this->nachweis, $this->pdo))->finden((string) $rechnung['project_id']);

        if ($projekt === null) {
            return ['Zu dieser Rechnung gibt es kein Projekt.'];
        }

        $rechnungId = (string) $rechnung['id'];
        $ausgestellt = Db::jetzt();

        // Nummer und Zeile in **einer** Transaktion (§4).
        $stornoId = $this->nummernkreis()->vergeben(
            'ST',
            fn (string $nummer): string => $this->rechnungen()->stornoAnlegen([
                'project_id'         => (string) $rechnung['project_id'],
                'number'             => $nummer,
                'milestone'          => (string) $rechnung['milestone'],
                'status'             => Zahlungsstatus::GESENDET,
                'issued_at'          => $ausgestellt,
                'cancels_invoice_id' => $rechnungId,
                'net_cents'          => -(int) $rechnung['net_cents'],
                'vat_cents'          => -(int) $rechnung['vat_cents'],
                'gross_cents'        => -(int) $rechnung['gross_cents'],
                'due_date'           => null,
                'note'               => 'Storno zu ' . (string) $rechnung['number'] . ': ' . $grundlage,
            ]),
        );

        $storno = $this->rechnungen()->finden($stornoId);

        // Der Beleg zur Stornorechnung — dieselbe Prüfung wie bei jeder Rechnung.
        if ($storno !== null) {
            $beleg = $this->belegErzeugen($storno, $projekt);

            if ($beleg['fehler'] !== []) {
                return $beleg['fehler'];
            }
        }

        $this->rechnungen()->zustandSetzen($rechnungId, Zahlungsstatus::STORNIERT);

        $this->audit()->schreiben(
            aktion: 'rechnung_storniert',
            objektart: 'invoice',
            objektId: $rechnungId,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            organisationId: (string) $projekt['organization_id'],
            alterWert: (string) $rechnung['status'],
            neuerWert: Zahlungsstatus::STORNIERT,
            grund: $grundlage,
            detail: [
                'stornorechnung'    => $storno === null ? null : (string) $storno['number'],
                'stornorechnung_id' => $stornoId,
            ],
            ip: $ip,
        );

        $this->kundenmailSenden(
            $projekt,
            'Stornorechnung zu ' . (string) $rechnung['number'],
            'Wir haben die Rechnung ' . (string) $rechnung['number'] . " aufgehoben.\n"
            . 'Die Stornorechnung ' . ($storno === null ? '' : (string) $storno['number'])
            . " liegt in Ihrem Bereich. Ein Betrag ist nicht zu zahlen.\n",
        );

        return [];
    }

    /**
     * Nimmt eine eingetragene Zahlung zurück — §12, Fall 54.
     *
     * ## Warum das nicht „Betrag 0 eintragen" ist
     *
     * Beides landete am selben Feld, und das ist genau der Fehler. §12: „Ein einmal auf
     * `bezahlt` gesetzter Status lässt sich **nicht stillschweigend** zurücknehmen — die
     * Rücknahme ist eine eigene protokollierte Aktion mit eigenem Grundlagentext und erzeugt
     * eine Benachrichtigung an den Kunden."
     *
     * Eine Rücknahme, die als Buchung über null erscheint, ist im Protokoll nicht von einer
     * Buchung zu unterscheiden. Sie trägt deshalb ihre eigene Aktion — wer das Protokoll
     * nach Rücknahmen durchsieht, findet sie, ohne Beträge lesen zu müssen.
     *
     * @return list<string> leer bei Erfolg
     */
    public function zahlungZuruecknehmen(string $rechnungId, string $grundlage, ?string $ip): array
    {
        $rechnung = $this->rechnungen()->finden($rechnungId);

        if ($rechnung === null) {
            return ['Diese Rechnung gibt es nicht.'];
        }

        if ((int) $rechnung['paid_cents'] === 0) {
            return ['Zu dieser Rechnung ist keine Zahlung eingetragen.'];
        }

        if (mb_strlen(trim($grundlage)) < self::GRUNDLAGE_MINDESTLAENGE) {
            return ['Bitte halten Sie fest, warum die Zahlung zurückgenommen wird — zum '
                . 'Beispiel „Rücklastschrift vom 09.08.2026" oder „Betrag doppelt gebucht".'];
        }

        $vorher = (string) $rechnung['status'];

        // Zurück auf null. Der Zustand wird gerechnet, nicht gesetzt — dieselbe Rechnung wie
        // bei der Buchung, damit eine überfällige Rechnung nach der Rücknahme wieder
        // überfällig ist und nicht `gesendet`.
        //
        // **Gerechnet wird mit dem Stand nach der Rücknahme, nicht mit dem davor.**
        // `istUeberfaellig()` verlangt `paid_cents < gross_cents`; solange die Zahlung noch
        // dasteht, ist das falsch, und eine längst überfällige Rechnung käme als `gesendet`
        // zurück. Die Kopie mit `paid_cents = 0` ist genau der Zustand, den wir schreiben.
        $ueberfaellig = self::istUeberfaellig(['paid_cents' => 0] + $rechnung);
        $zustand = Zahlungsstatus::ausBetrag(0, (int) $rechnung['gross_cents'], $ueberfaellig);

        $this->rechnungen()->zahlungSetzen($rechnungId, 0, $zustand, null, null);

        $projekt = (new AdminProjekte($this->nachweis, $this->pdo))->finden((string) $rechnung['project_id']);

        $this->audit()->schreiben(
            aktion: 'zahlung_zurueckgenommen',
            objektart: 'invoice',
            objektId: $rechnungId,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            organisationId: $projekt === null ? null : (string) $projekt['organization_id'],
            alterWert: $vorher,
            neuerWert: $zustand,
            grund: trim($grundlage),
            detail: ['zurueckgenommen_cents' => (int) $rechnung['paid_cents']],
            ip: $ip,
        );

        if ($projekt !== null) {
            // §10, Zeile „Zahlungsstatus zurückgenommen" — der Grundlagentext steht im
            // Wortlaut mit drin. Der Kunde erfährt sonst, **dass** korrigiert wurde, aber
            // nicht warum, und ruft an.
            $this->kundenmailSenden(
                $projekt,
                'Korrektur zu Rechnung ' . (string) $rechnung['number'],
                'Wir haben den Zahlungsstatus der Rechnung ' . (string) $rechnung['number']
                . ' korrigiert. Grund: ' . trim($grundlage) . ". Bitte prüfen Sie den Stand\n"
                . "in Ihrem Bereich.\n",
            );
        }

        return [];
    }

    /**
     * Verschiebt das Fälligkeitsdatum — §12, Fall 53a.
     *
     * **Kein neuer Beleg.** `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 5: „Das Fälligkeitsdatum
     * steht zwar auf der Rechnung, ist aber keine Pflichtangabe nach § 14." Eine
     * Stornorechnung dafür wäre ein zweiter Beleg ohne Anlass.
     *
     * Protokolliert wird trotzdem mit Grundlagentext — eine Frist ist Geld mit Datum.
     *
     * @return list<string> leer bei Erfolg
     */
    public function faelligkeitAendern(
        string $rechnungId,
        string $neuesDatum,
        string $grundlage,
        ?string $ip,
    ): array {
        $rechnung = $this->rechnungen()->finden($rechnungId);

        if ($rechnung === null) {
            return ['Diese Rechnung gibt es nicht.'];
        }

        if (mb_strlen(trim($grundlage)) < self::GRUNDLAGE_MINDESTLAENGE) {
            return ['Bitte halten Sie fest, warum die Frist verschoben wird.'];
        }

        $neuesDatum = trim($neuesDatum);

        if (!Validate::datum($neuesDatum)) {
            return ['Bitte geben Sie ein gültiges Datum an.'];
        }

        $vorher = is_string($rechnung['due_date'] ?? null) ? (string) $rechnung['due_date'] : '';

        if ($vorher === $neuesDatum) {
            return ['Dieses Datum steht bereits auf der Rechnung.'];
        }

        $this->rechnungen()->faelligkeitSetzen($rechnungId, $neuesDatum);

        $projekt = (new AdminProjekte($this->nachweis, $this->pdo))->finden((string) $rechnung['project_id']);

        $this->audit()->schreiben(
            aktion: 'faelligkeit_geaendert',
            objektart: 'invoice',
            objektId: $rechnungId,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            organisationId: $projekt === null ? null : (string) $projekt['organization_id'],
            alterWert: $vorher,
            neuerWert: $neuesDatum,
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

        return $faellig < Format::heute()
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

    /**
     * @param array<string,mixed> $projekt
     *
     * Der Rahmen steht in `Projektmail` — §10 schreibt ihn allen Mails gleich vor, und die
     * Fußzeile mit dem Projekttitel fehlte hier vorher.
     *
     * Eine gescheiterte Mail nimmt keine Buchung zurück; `Projektmail` wirft deshalb nicht.
     * §6.3 hält den Notweg bereit.
     */
    private function kundenmailSenden(array $projekt, string $betreff, string $kern): void
    {
        (new Projektmail($this->mail, $this->pdo))->anKunden($projekt, $betreff, $kern);
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

    /**
     * Erzeugt den Beleg zu einer Rechnung — Abschnitte 2, 3 und 7.
     *
     * @param array<string,mixed> $rechnung
     * @param array<string,mixed> $projekt
     * @return array{fehler:list<string>,id:?string,pfad:?string}
     */
    private function belegErzeugen(array $rechnung, array $projekt): array
    {
        $betreiber = (new BetreiberdatenSpeicher($this->pdo))->lesen();

        if ($betreiber === null) {
            return ['fehler' => ['Die Betreiberdaten fehlen. Ohne sie hat der Beleg keinen Aussteller.'],
                'id' => null, 'pfad' => null];
        }

        $organisation = (new \Sartu\Data\Admin\AdminOrganisationen($this->nachweis, $this->pdo))
            ->finden((string) $projekt['organization_id']);

        if ($organisation === null) {
            return ['fehler' => ['Zu diesem Projekt gibt es keine Organisation.'], 'id' => null, 'pfad' => null];
        }

        return $this->belege()->rechnung($rechnung, $betreiber, $organisation, $projekt);
    }

    /**
     * Legt beim Zahlungsdienst eine Zahlung an — §6 Schritt 1 und 2.
     *
     * ## Warum ein Fehlschlag den Versand nicht anhält
     *
     * Die Rechnung ist zu diesem Zeitpunkt ausgestellt, der Beleg liegt in der Ablage und
     * trägt eine Nummer aus dem lückenlosen Kreis. Sie danach wieder zurückzunehmen, weil
     * ein fremder Server nicht antwortet, hieße eine Lücke zu reißen — und die Rechnung ist
     * ohne Zahlungslink vollständig: Auf ihr steht die Bankverbindung.
     *
     * Der Fehlschlag wird protokolliert, nicht verschwiegen. Im Adminbereich steht die
     * Rechnung dann ohne Zahlungsadresse da, und der Betreiber kann sie nachtragen.
     *
     * ## Warum ohne hinterlegten Schlüssel gar nichts passiert
     *
     * Vor der Anbindung — und in jeder Umgebung ohne Schlüssel — soll der Versand genau so
     * laufen wie vorher. Ein Aufruf ins Leere, der jedes Mal einen Protokolleintrag über
     * einen fehlenden Schlüssel erzeugt, wäre Lärm.
     *
     * @param array<string,mixed> $rechnung
     */
    private function zahlungVorbereiten(string $rechnungId, array $rechnung, ?string $ip): void
    {
        $dienst = $this->zahlungsdienst;

        if ($dienst === null) {
            if (!(new Zahlungsschluessel())->hinterlegt()) {
                return;
            }

            $dienst = new Mollie();
        }

        try {
            $zahlung = $dienst->zahlungAnlegen(
                (int) $rechnung['gross_cents'],
                ERechnung::WAEHRUNG,
                (string) $rechnung['number'],
                Mollie::rueckkehrAdresse(),
                Mollie::webhookAdresse(),
            );
        } catch (ZahlungsdienstFehler $fehler) {
            $this->audit()->schreiben(
                aktion: 'zahlung_anlegen_gescheitert',
                objektart: 'invoice',
                objektId: $rechnungId,
                akteurBenutzerId: $this->nachweis->adminBenutzerId,
                // Der Text der Ausnahme nennt nie einen Schlüssel — dafür sorgt `Mollie`.
                grund: $fehler->getMessage(),
                ip: $ip,
            );

            return;
        }

        (new \Sartu\Data\Zahlungseingaenge($this->pdo))
            ->zahlungHinterlegen($rechnungId, $zahlung['kennung'], $zahlung['adresse']);
    }

    private function belege(): Belegerzeugung
    {
        return $this->belegerzeugung ?? new Belegerzeugung(pdo: $this->pdo);
    }

    private function nummernkreis(): Nummernkreis
    {
        return $this->nummernkreis ?? new Nummernkreis(pdo: $this->pdo);
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll($this->pdo);
    }
}
