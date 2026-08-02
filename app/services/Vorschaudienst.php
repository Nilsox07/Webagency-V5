<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\AuditProtokoll;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenFreigaben;
use Sartu\Data\Customer\KundenProjekte;
use Sartu\Data\Customer\KundenVorschau;
use Sartu\Helpers\Validate;

/**
 * Vorschau, Korrekturrunden und Abnahme aus Kundensicht — §5.6a, §8.4.
 *
 * ## Die Korrekturrunden sind eine Scope-Grenze, keine Empfehlung
 *
 * §5.6a: „Die enthaltenen Runden sind eine **harte Scope-Grenze**, keine Empfehlung. Das
 * Portal muss sie sichtbar machen, sonst wird Feedback endlos."
 *
 * **Und zugleich:** „Das Portal **blockiert nichts** und berechnet nichts automatisch. Es
 * macht den Stand nur sichtbar. Über zusätzlichen Aufwand entscheidet immer ein Mensch."
 *
 * Beides steht nebeneinander und ist kein Widerspruch: Die Grenze wird **angezeigt**, nicht
 * durchgesetzt. Eine Runde mit `included = false` läuft genauso wie jede andere — der Kunde
 * sieht nur vorher, dass sie nicht im Festpreis steckt. Wer hier eine Sperre einbaut, hat
 * den zweiten Satz nicht gelesen.
 *
 * ## Einreichen ist endgültig
 *
 * §5.6a Punkt 3: „Danach sind in dieser Runde **keine** weiteren Einträge möglich." Das
 * steht als Bedingung im `INSERT` und im `UPDATE` (`Data\Customer\KundenVorschau`), nicht
 * als vorgelagerte Abfrage — zwischen Prüfung und Schreiben liegt sonst Zeit.
 *
 * ## Die Abnahme
 *
 * §8.4: Ankreuzen und getippter Name, Eintrag in `approvals` mit `kind = abnahme`,
 * Audit-Ereignis, Wechsel `abnahme` → `launch_vorbereitung`. Sie ist **einmalig** — der
 * eindeutige Schlüssel auf `approvals` entscheidet, nicht eine Abfrage.
 */
final class Vorschaudienst
{
    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?KundenVorschau $vorschau = null,
        private readonly ?KundenFreigaben $freigaben = null,
        private readonly ?Projektwechsel $wechsel = null,
        private readonly ?AuditProtokoll $audit = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * §5.6a Punkt 2 — eine Rückmeldung in der laufenden Runde.
     *
     * @return list<string> leer bei Erfolg
     */
    public function rueckmeldungSenden(string $projektId, array $eingabe, string $benutzerId): array
    {
        $projekt = (new KundenProjekte($this->bereich, $this->pdo))->finden($projektId);

        if ($projekt === null) {
            return ['Dieses Projekt gibt es nicht.'];
        }

        $text = is_string($eingabe['body'] ?? null) ? trim($eingabe['body']) : '';

        if (!Validate::gefuellt($text)) {
            return ['Bitte schreiben Sie, was Ihnen aufgefallen ist.'];
        }

        $runde = $this->vorschau()->aktuelleRunde($projektId);

        if ($runde === null) {
            return ['Aktuell läuft keine Korrekturrunde.'];
        }

        $seite = is_string($eingabe['page_hint'] ?? null) ? trim($eingabe['page_hint']) : '';

        $id = $this->vorschau()->rueckmeldungAnlegen(
            (string) $runde['id'],
            $projektId,
            $text,
            $seite === '' ? null : $seite,
            $benutzerId,
        );

        if ($id === null) {
            // §5.6a, Wortlaut gebunden.
            return ['Diese Korrekturrunde wurde bereits eingereicht. Wir arbeiten sie gerade ein '
                . 'und melden uns, sobald die neue Vorschau bereitsteht.'];
        }

        return [];
    }

    /**
     * §5.6a Punkt 3 — gebündelt einreichen.
     *
     * §8.4: „Der Button ist gesperrt, solange die Runde keine einzige Rückmeldung enthält."
     * Die Sperre steht hier und nicht nur in der Oberfläche.
     *
     * @return list<string> leer bei Erfolg
     */
    public function einreichen(string $projektId, string $benutzerId, ?string $ip): array
    {
        $projekt = (new KundenProjekte($this->bereich, $this->pdo))->finden($projektId);

        if ($projekt === null) {
            return ['Dieses Projekt gibt es nicht.'];
        }

        $runde = $this->vorschau()->aktuelleRunde($projektId);

        if ($runde === null || (string) $runde['status'] !== 'offen') {
            return ['Diese Korrekturrunde wurde bereits eingereicht. Wir arbeiten sie gerade ein '
                . 'und melden uns, sobald die neue Vorschau bereitsteht.'];
        }

        if ($this->vorschau()->anzahlRueckmeldungen((string) $runde['id']) === 0) {
            return ['Bitte geben Sie zuerst eine Rückmeldung ein.'];
        }

        if (!$this->vorschau()->einreichen((string) $runde['id'])) {
            return ['Diese Korrekturrunde wurde bereits eingereicht. Wir arbeiten sie gerade ein '
                . 'und melden uns, sobald die neue Vorschau bereitsteht.'];
        }

        $this->audit()->schreiben(
            aktion: 'korrekturrunde_eingereicht',
            objektart: 'feedback_round',
            objektId: (string) $runde['id'],
            akteurBenutzerId: $benutzerId,
            organisationId: $this->bereich->organisationId,
            neuerWert: 'eingereicht',
            detail: ['nummer' => (int) $runde['number'], 'enthalten' => (bool) $runde['included']],
            ip: $ip,
        );

        // §5.1a: `vorschau` → `korrektur`, ausgelöst vom Kunden.
        $this->wechsel()->wechseln(
            $projektId,
            $this->bereich->organisationId,
            Projektstatus::KORREKTUR,
            Projektstatus::KUNDE,
            $benutzerId,
            null,
            $ip,
        );

        // §10: `Korrekturrunde {Nummer} eingereicht: {Organisation}` — interne Kurzmeldung.
        (new Projektmail(pdo: $this->pdo))->anBetreuer(
            $projekt,
            'Korrekturrunde ' . (int) $runde['number'] . ' eingereicht: ' . (string) $projekt['title'],
            'Der Kunde hat Korrekturrunde ' . (int) $runde['number'] . ' mit '
            . $this->vorschau()->anzahlRueckmeldungen((string) $runde['id'])
            . " Rückmeldungen eingereicht.\n",
        );

        return [];
    }

    /**
     * §8.4 Abnahmeblock — die zweite der drei Erklärungen aus §5.1a.
     *
     * @return list<string> leer bei Erfolg
     */
    public function abnehmen(string $projektId, array $eingabe, string $benutzerId, ?string $ip): array
    {
        $projekt = (new KundenProjekte($this->bereich, $this->pdo))->finden($projektId);

        if ($projekt === null) {
            return ['Dieses Projekt gibt es nicht.'];
        }

        if ((string) $projekt['status'] !== Projektstatus::ABNAHME) {
            return ['Die Abnahme ist an dieser Stelle noch nicht dran.'];
        }

        $fehler = [];

        if (($eingabe['bestaetigung'] ?? null) !== '1') {
            $fehler[] = 'Bitte bestätigen Sie die Abnahme.';
        }

        $name = is_string($eingabe['granted_name'] ?? null) ? trim($eingabe['granted_name']) : '';

        if (!Validate::gefuellt($name)) {
            $fehler[] = 'Bitte geben Sie Ihren Namen an.';
        }

        if ($fehler !== []) {
            return $fehler;
        }

        if (!$this->freigaben()->erklaeren($projektId, KundenFreigaben::ABNAHME, $benutzerId, $name, $ip)) {
            return ['Die Abnahme liegt bereits vor.'];
        }

        $this->audit()->schreiben(
            aktion: 'abnahme_erklaert',
            objektart: 'project',
            objektId: $projektId,
            akteurBenutzerId: $benutzerId,
            organisationId: $this->bereich->organisationId,
            grund: 'Abnahme der Website durch ' . $name,
            ip: $ip,
        );

        // §5.1a: `abnahme` → `launch_vorbereitung`, `reason` Pflicht.
        $this->wechsel()->wechseln(
            $projektId,
            $this->bereich->organisationId,
            Projektstatus::LAUNCH_VORBEREITUNG,
            Projektstatus::KUNDE,
            $benutzerId,
            'Abnahme durch ' . $name,
            $ip,
        );

        // §10: `Abnahme bestätigt` — an beide.
        $mail = new Projektmail(pdo: $this->pdo);
        $mail->anKunden($projekt, 'Abnahme bestätigt', "Danke für die Abnahme. Wir bereiten den Start vor.\n");
        $mail->anBetreuer(
            $projekt,
            'Abnahme bestätigt',
            'Die Abnahme liegt vor, erklärt von ' . $name . ". Der Onlinegang kann vorbereitet werden.\n",
        );

        return [];
    }

    /**
     * Der Hinweistext aus §5.6a, wenn die Runde nicht mehr im Festpreis steckt.
     *
     * Er wird **vor** dem Einreichen gezeigt und blockiert nichts.
     */
    public static function hinweisZusatzrunde(int $enthaltene): string
    {
        return 'Diese Korrekturrunde ist im Festpreis nicht mehr enthalten. Ihre vereinbarten '
            . $enthaltene . ' Korrekturrunden sind bereits genutzt. Wir schauen uns Ihre '
            . 'Rückmeldung trotzdem an und melden uns, bevor Aufwand entsteht — Sie gehen '
            . 'damit keine Kosten ein.';
    }

    private function vorschau(): KundenVorschau
    {
        return $this->vorschau ?? new KundenVorschau($this->bereich, $this->pdo);
    }

    private function freigaben(): KundenFreigaben
    {
        return $this->freigaben ?? new KundenFreigaben($this->bereich, $this->pdo);
    }

    private function wechsel(): Projektwechsel
    {
        return $this->wechsel ?? new Projektwechsel(pdo: $this->pdo);
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll($this->pdo);
    }
}
