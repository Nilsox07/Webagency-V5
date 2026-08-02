<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\AngebotAnnahme;
use Sartu\Data\AuditProtokoll;
use Sartu\Data\Customer\KundenAngebote;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\AnmeldeKonten;
use Sartu\Helpers\Format;
use Sartu\Helpers\Validate;

/**
 * Der Kunde nimmt sein Angebot an — Portal-Lastenheft §8.2 und §5.1a.
 *
 * ## Vier Bestätigungen und ein getippter Name, nicht drei
 *
 * §8.2 nennt vier Kästchen. Sie sind **einzeln** Pflicht (Testfall 11), weil jedes eine
 * andere Aussage trägt — die zweite grenzt Sonderfunktionen aus, die vierte ist die
 * kostenpflichtige Beauftragung. Ein gemeinsames „Ich stimme zu" wäre eine andere Erklärung.
 *
 * ## Die drei Sperren
 *
 * | Sperre | Quelle | Testfall |
 * |---|---|---|
 * | Ein angenommenes Angebot lässt sich nicht erneut annehmen | §8.2, „ab hier schreibgeschützt" | 12 |
 * | Ein abgelaufenes Angebot lässt sich nicht annehmen | §4 Prüfregel Annahme | 13 |
 * | Ein unvollständiges Angebot ebenfalls nicht | §4, §8.2 | — |
 *
 * Die erste steckt zusätzlich in der `WHERE`-Bedingung des `UPDATE`
 * (`Data\AngebotAnnahme`): Zwei gleichzeitige Klicks können nicht beide durchlaufen.
 *
 * ## Was mit übernommen wird
 *
 * §8.2: „Zugleich werden ins Projekt übernommen: `included_feedback_rounds`,
 * `protection_level` und `package`." Ohne diese Übernahme stünde im Projekt weiter das
 * Paket aus der Umwandlung — und die Korrekturrunden, gegen die später gezählt wird, kämen
 * aus der falschen Quelle (Testfall 24).
 */
final class Angebotsannahme
{
    /** §8.2 — die vier Bestätigungen, Wortlaut gebunden. */
    public const BESTAETIGUNGEN = [
        'bestaetigung_bedarf' => 'Die aufgeführten Ziele, Seitenbereiche und Funktionen entsprechen '
            . 'meinem Bedarf.',
        'bestaetigung_umfang' => 'Nicht aufgeführte Sonderfunktionen wie Shop, Kundenlogin, '
            . 'Schnittstellen oder komplexe Buchung sind nicht beauftragt.',
        'bestaetigung_neues'  => 'Neue Anforderungen werden vor Umsetzung getrennt angeboten.',
        'bestaetigung_auftrag' => 'Ich handle für mein Unternehmen und beauftrage SARTU '
            . 'kostenpflichtig zu den angezeigten Preisen, Laufzeiten und Zahlungsbedingungen.',
    ];

    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?AngebotAnnahme $annahme = null,
        private readonly ?Projektwechsel $wechsel = null,
        private readonly ?AuditProtokoll $audit = null,
        private readonly ?Versender $mail = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * @param array<string,mixed> $eingabe
     *
     * @return list<string> leer bei Erfolg
     */
    public function annehmen(string $angebotId, array $eingabe, string $benutzerId, ?string $ip): array
    {
        $angebot = (new KundenAngebote($this->bereich, $this->pdo))->finden($angebotId);

        // §3 Regel 2: gibt es nicht ODER gehört nicht mir — derselbe Ausgang.
        if ($angebot === null) {
            return ['Dieses Angebot gibt es nicht.'];
        }

        if ((string) $angebot['status'] === 'angenommen') {
            return ['Dieses Angebot ist bereits angenommen.'];
        }

        if ((string) $angebot['status'] !== 'gesendet') {
            return ['Dieses Angebot lässt sich nicht mehr annehmen.'];
        }

        if (self::abgelaufen($angebot)) {
            return ['Dieses Angebot ist am ' . \Sartu\Helpers\Format::datum((string) $angebot['valid_until'])
                . ' abgelaufen. Schreiben Sie uns — wir stellen es neu aus.'];
        }

        $fehler = [];

        foreach (array_keys(self::BESTAETIGUNGEN) as $feld) {
            if (($eingabe[$feld] ?? null) !== '1') {
                // §8.2, Wortlaut gebunden — eine Meldung für alle vier, nicht vier Meldungen.
                $fehler[] = 'Bitte bestätigen Sie alle vier Punkte, um fortzufahren.';
                break;
            }
        }

        $name = is_string($eingabe['accepted_name'] ?? null) ? trim($eingabe['accepted_name']) : '';

        if (!Validate::gefuellt($name)) {
            $fehler[] = 'Bitte geben Sie Ihren Namen an.';
        }

        if ($fehler !== []) {
            return $fehler;
        }

        $projektId = (string) $angebot['project_id'];

        // Der bedingte UPDATE ist die eigentliche Sperre gegen den Doppelklick.
        if (!$this->annahme()->annehmen($angebotId, $this->bereich->organisationId, $benutzerId, $name, $ip)) {
            return ['Dieses Angebot ist bereits angenommen.'];
        }

        // §8.2: Umfang, Schutzstufe und Korrekturrunden gehen ins Projekt (Testfall 24).
        $this->annahme()->angebotswerteUebernehmen($projektId, $this->bereich->organisationId, [
            'package'                  => (string) $angebot['package'],
            'protection_level'         => (string) $angebot['protection_level'],
            'included_feedback_rounds' => (int) $angebot['included_feedback_rounds'],
        ]);

        $this->audit()->schreiben(
            aktion: 'angebot_angenommen',
            objektart: 'offer',
            objektId: $angebotId,
            akteurBenutzerId: $benutzerId,
            organisationId: $this->bereich->organisationId,
            alterWert: 'gesendet',
            neuerWert: 'angenommen',
            grund: 'Kostenpflichtige Beauftragung durch ' . $name,
            detail: ['angebotsnummer' => (string) $angebot['number']],
            ip: $ip,
        );

        // §5.1a: `angebot_offen` → `angebot_angenommen`, ausgelöst vom Kunden.
        $wechselfehler = $this->wechsel()->wechseln(
            $projektId,
            $this->bereich->organisationId,
            Projektstatus::ANGEBOT_ANGENOMMEN,
            Projektstatus::KUNDE,
            $benutzerId,
            null,
            $ip,
        );

        if ($wechselfehler !== null) {
            // Die Annahme steht bereits und wird nicht zurückgenommen — sie ist eine
            // Erklärung des Kunden. Der Zustand zieht nach, sobald der Admin ihn setzt.
            return [];
        }

        $this->bestaetigungSenden($benutzerId, $name);

        return [];
    }

    /** §4 Prüfregel Annahme: `valid_until` darf nicht in der Vergangenheit liegen. */
    public static function abgelaufen(array $angebot): bool
    {
        $bis = $angebot['valid_until'] ?? null;

        if (!is_string($bis) || $bis === '') {
            return true;
        }

        return $bis < Format::heute();
    }

    /** §8.2: „Der Annahmeblock erscheint nur bei `status = gesendet` und `valid_until >= heute`." */
    public static function annehmbar(?array $angebot): bool
    {
        return $angebot !== null
            && (string) $angebot['status'] === 'gesendet'
            && !self::abgelaufen($angebot);
    }

    /** §10, Zeile „Angebot angenommen (an Kunde)" — Wortlaut gebunden. */
    private function bestaetigungSenden(string $benutzerId, string $name): void
    {
        $benutzer = (new AnmeldeKonten($this->pdo))->kundeNachId($benutzerId);

        if ($benutzer === null) {
            return;
        }

        try {
            $this->mailversand()->senden(
                (string) $benutzer['email'],
                'Bestätigung Ihrer Beauftragung',
                'Guten Tag ' . $name . ",\n\n"
                . "Danke für Ihre Beauftragung. Als Nächstes erhalten Sie die Anzahlungsrechnung\n"
                . "in Ihrem Bereich.\n\n"
                . "Freundliche Grüße\nSARTU\n",
            );
        } catch (\Throwable) {
            // Eine gescheiterte Bestätigungsmail nimmt keine Beauftragung zurück.
        }
    }

    private function annahme(): AngebotAnnahme
    {
        return $this->annahme ?? new AngebotAnnahme($this->pdo);
    }

    private function wechsel(): Projektwechsel
    {
        return $this->wechsel ?? new Projektwechsel(pdo: $this->pdo);
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll($this->pdo);
    }

    private function mailversand(): Mailversand
    {
        return $this->mail ?? new Mailversand();
    }
}
