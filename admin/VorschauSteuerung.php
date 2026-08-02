<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Antwort;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminProjekte;
use Sartu\Data\Admin\AdminVorschau;
use Sartu\Data\AuditProtokoll;
use Sartu\Helpers\Format;
use Sartu\Helpers\Http;
use Sartu\Helpers\Validate;
use Sartu\Services\Domainstand;
use Sartu\Services\Projektmail;
use Sartu\Services\Projektstatus;
use Sartu\Services\Projektwechsel;

/**
 * Vorschau, Korrekturrunden, Domainlage und Livegang im internen Bereich —
 * Portal-Lastenheft §5.6a, §5.7, §8.4, §8.7, §9.2.
 *
 * **Die Runde wird beim Bereitstellen der Vorschau geoeffnet** (§5.6a Punkt 1). Sie traegt
 * `included = false`, sobald die enthaltenen Runden verbraucht sind — das Portal blockiert
 * dann nichts, es zeigt den Stand nur an.
 *
 * **Der Livegang setzt den Betriebsbeginn** (§5.7). Die Mindestlaufzeit rechnet das System,
 * nicht der Admin: `protection_started_on + 12 Monate`. Ein von Hand getipptes Enddatum
 * waere eine zweite Wahrheit.
 *
 * **Der Betriebsbeginn laesst sich nachtraeglich verschieben** — §5.7 Sonderfall, wenn die
 * Website fertig bereitsteht und nur der Kunde den Onlinegang verzoegert. Das ist eine
 * Aenderung an einer Frist und deshalb nach §12 ohne Grundlagentext nicht speicherbar
 * (Testfall 53b).
 */
final class VorschauSteuerung
{
    /** §5.6a Punkt 1: Vorschau bereitstellen und Runde oeffnen. */
    public function vorschauBereitstellen(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, function (AdminNachweis $nachweis, array $projekt): array {
            $adresse = Http::getrimmteEingabe('preview_url');

            if (!str_starts_with($adresse, 'https://')) {
                return ['Die Adresse der Vorschau muss mit https:// beginnen.'];
            }

            $vorschau = new AdminVorschau($nachweis);
            $projektId = (string) $projekt['id'];

            $nummer = $vorschau->naechsteNummer($projektId);
            $enthalten = $nummer <= (int) $projekt['included_feedback_rounds'];

            // §5.1a: `produktion` → `vorschau` oder `korrektur` → `vorschau`. Zuerst der
            // Wechsel: Scheitert er, steht keine Adresse an einem Projekt, dessen Vorschau
            // gar nicht bereitsteht, und keine Runde ohne Anlass offen.
            $fehler = (new Projektwechsel())->wechseln(
                $projektId,
                (string) $projekt['organization_id'],
                Projektstatus::VORSCHAU,
                Projektstatus::ADMIN,
                $nachweis->adminBenutzerId,
                null,
                Http::gegenstelle(),
            );

            if ($fehler !== null) {
                return [$fehler];
            }

            $vorschau->vorschauSetzen($projektId, $adresse);
            $vorschau->rundeOeffnen($projektId, $nummer, $enthalten);

            // §10: `Ihre Vorschau ist bereit`.
            (new Projektmail())->anKunden(
                $projekt,
                'Ihre Vorschau ist bereit',
                "Sie können sich Ihre Website jetzt ansehen und Rückmeldung geben. Sammeln Sie in "
                . "Ruhe alles und reichen Sie es gebündelt ein.\n",
            );

            return [];
        }, 'Die Vorschau steht bereit, die Korrekturrunde ist geöffnet.');
    }

    /** §5.6a Punkt 4: eingearbeitet — die Runde wird geschlossen. */
    public function rundeAbschliessen(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, function (AdminNachweis $nachweis, array $projekt): array {
            $rundeId = Http::getrimmteEingabe('runde');

            if ($rundeId === '') {
                return ['Bitte wählen Sie die Runde.'];
            }

            if (!(new AdminVorschau($nachweis))->rundeAbschliessen($rundeId, (string) $projekt['id'])) {
                return ['Diese Korrekturrunde ist an diesem Projekt nicht offen.'];
            }

            // §10: `Ihre Änderungen sind eingearbeitet`.
            (new Projektmail())->anKunden(
                $projekt,
                'Ihre Änderungen sind eingearbeitet',
                "Wir haben Ihre Rückmeldungen umgesetzt. Die neue Fassung liegt in der Vorschau bereit.\n",
            );

            return [];
        }, 'Die Korrekturrunde ist als eingearbeitet vermerkt.');
    }

    /**
     * §9.2: `Zusätzliche Runde öffnen` — legt `included = false` an.
     *
     * Sie entsteht **ausserhalb** des Festpreises und deshalb nur auf ausdrückliche Handlung
     * eines Menschen. Das Portal rechnet dazu nichts ab; über den Aufwand entscheidet ein
     * Mensch (§5.6a).
     */
    public function zusaetzlicheRunde(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, function (AdminNachweis $nachweis, array $projekt): array {
            $vorschau = new AdminVorschau($nachweis);
            $projektId = (string) $projekt['id'];

            $vorschau->rundeOeffnen($projektId, $vorschau->naechsteNummer($projektId), false);

            return [];
        }, 'Die zusätzliche Korrekturrunde ist geöffnet. Der Kunde sieht, dass sie im Festpreis '
            . 'nicht enthalten ist.');
    }

    /** §5.1a: `vorschau` → `abnahme`. */
    public function zurAbnahme(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, function (AdminNachweis $nachweis, array $projekt): array {
            $fehler = (new Projektwechsel())->wechseln(
                (string) $projekt['id'],
                (string) $projekt['organization_id'],
                Projektstatus::ABNAHME,
                Projektstatus::ADMIN,
                $nachweis->adminBenutzerId,
                null,
                Http::gegenstelle(),
            );

            return $fehler === null ? [] : [$fehler];
        }, 'Der Kunde ist zur Abnahme aufgefordert.');
    }

    /** §5.1a und §5.7: der Onlinegang. */
    public function livegang(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, function (AdminNachweis $nachweis, array $projekt): array {
            $adresse = Http::getrimmteEingabe('live_url');

            if (!str_starts_with($adresse, 'https://')) {
                return ['Die Adresse der Website muss mit https:// beginnen.'];
            }

            // §5.7: „Vorbelegung: heutiges Datum." Ein leeres Feld ist deshalb kein Fehler.
            $betriebSeit = self::datumOderHeute(Http::getrimmteEingabe('protection_started_on'));

            $fehler = (new Projektwechsel())->wechseln(
                (string) $projekt['id'],
                (string) $projekt['organization_id'],
                Projektstatus::LIVE,
                Projektstatus::ADMIN,
                $nachweis->adminBenutzerId,
                'Onlinegang, Betrieb ab ' . $betriebSeit,
                Http::gegenstelle(),
            );

            if ($fehler !== null) {
                return [$fehler];
            }

            // §5.7: Erst nach dem Wechsel — sonst stuenden Betriebsdaten an einem Projekt,
            // das gar nicht live ging.
            (new AdminVorschau($nachweis))->livegangEintragen((string) $projekt['id'], $adresse, $betriebSeit);

            // §10: `Ihre Website ist online`.
            (new Projektmail())->anKunden(
                $projekt,
                'Ihre Website ist online',
                'Ihre Website ist erreichbar unter ' . $adresse
                . ". Ab jetzt übernehmen wir den laufenden Betrieb.\n",
            );

            return [];
        }, 'Die Website ist online. Der Betrieb läuft ab dem eingetragenen Datum.');
    }

    /**
     * §5.7 Sonderfall — der Betriebsbeginn wird verschoben. Testfall 53b.
     *
     * §12 nennt diese Änderung ausdrücklich neben `due_date`: „Ohne Grundlagentext lässt sich
     * keine dieser Änderungen speichern."
     */
    public function betriebsbeginn(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, function (AdminNachweis $nachweis, array $projekt): array {
            $datum = Http::getrimmteEingabe('protection_started_on');
            $grund = Http::getrimmteEingabe('grund');

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $datum) !== 1) {
                return ['Bitte geben Sie den Betriebsbeginn als Datum an.'];
            }

            if (!Validate::gefuellt($grund)) {
                return ['Bitte halten Sie fest, worauf sich die Verschiebung stützt. '
                    . 'Sie muss vorher schriftlich angekündigt worden sein.'];
            }

            $vorher = $projekt['protection_started_on'] === null
                ? null
                : (string) $projekt['protection_started_on'];

            if ($vorher === $datum) {
                return ['Dieses Datum steht bereits so da.'];
            }

            (new AdminVorschau($nachweis))->betriebsbeginnSetzen((string) $projekt['id'], $datum);

            (new AuditProtokoll())->schreiben(
                aktion: 'betriebsbeginn_geaendert',
                objektart: 'project',
                objektId: (string) $projekt['id'],
                akteurBenutzerId: $nachweis->adminBenutzerId,
                organisationId: (string) $projekt['organization_id'],
                alterWert: $vorher,
                neuerWert: $datum,
                grund: $grund,
                detail: ['mindestlaufzeit_bis' => AdminVorschau::mindestlaufzeitEnde($datum)],
                ip: Http::gegenstelle(),
            );

            return [];
        }, 'Der Betriebsbeginn ist geändert. Die Mindestlaufzeit rechnet sich neu.');
    }

    /** §8.7: die Domainlage von Hand pflegen. */
    public function domain(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, function (AdminNachweis $nachweis, array $projekt): array {
            $zustand = Http::getrimmteEingabe('state');

            if (!Domainstand::erlaubt($zustand)) {
                return ['Diesen Domainstand gibt es nicht.'];
            }

            (new AdminVorschau($nachweis))->domainstandSetzen((string) $projekt['id'], [
                'desired_name'    => self::leerAlsNull(Http::getrimmteEingabe('desired_name')),
                'confirmed_name'  => self::leerAlsNull(Http::getrimmteEingabe('confirmed_name')),
                'owner_confirmed' => Http::getrimmteEingabe('owner_confirmed') === '1',
                'state'           => $zustand,
                'email_note'      => self::leerAlsNull(Http::getrimmteEingabe('email_note')),
                'admin_note'      => self::leerAlsNull(Http::getrimmteEingabe('admin_note')),
            ]);

            return [];
        }, 'Der Domainstand ist gespeichert.');
    }

    /** Projekt pausieren und fortsetzen — §5.1a. */
    public function pausieren(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, function (AdminNachweis $nachweis, array $projekt): array {
            $grund = Http::getrimmteEingabe('grund');

            $fehler = (new Projektwechsel())->pausieren(
                (string) $projekt['id'],
                (string) $projekt['organization_id'],
                $grund,
                $nachweis->adminBenutzerId,
                Http::gegenstelle(),
            );

            if ($fehler !== null) {
                return [$fehler];
            }

            // §10: `Ihr Projekt pausiert` — mit dem Grund. Ohne diese Mail steht der Grund im
            // Kundenbereich und sieht ihn niemand.
            (new Projektmail())->anKunden(
                $projekt,
                'Ihr Projekt pausiert',
                "Wir haben Ihr Projekt vorübergehend angehalten. Grund: " . $grund
                . "\nSobald es weitergeht, melden wir uns.\n",
            );

            return [];
        }, 'Das Projekt pausiert. Der Kunde sieht den Grund.');
    }

    public function fortsetzen(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, function (AdminNachweis $nachweis, array $projekt): array {
            $fehler = (new Projektwechsel())->fortsetzen(
                (string) $projekt['id'],
                (string) $projekt['organization_id'],
                $nachweis->adminBenutzerId,
                Http::gegenstelle(),
            );

            if ($fehler !== null) {
                return [$fehler];
            }

            // §10: `Es geht weiter`.
            (new Projektmail())->anKunden(
                $projekt,
                'Es geht weiter',
                "Ihr Projekt läuft wieder. Ihren nächsten Schritt finden Sie in Ihrem Bereich.\n",
            );

            return [];
        }, 'Das Projekt läuft wieder.');
    }

    // ------------------------------------------------------------------ intern

    /** @param array<string,string> $parameter */
    private function aktion(array $parameter, \Closure $tun, string $hinweis): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $projekt = (new AdminProjekte($nachweis))->finden((string) ($parameter['id'] ?? ''));

        if ($projekt === null) {
            return Antwort::nichtGefunden();
        }

        $fehler = $tun($nachweis, $projekt);

        return (new ProjekteSteuerung())->einzeln($parameter, $fehler, $fehler === [] ? [$hinweis] : []);
    }

    private static function datumOderHeute(string $eingabe): string
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $eingabe) === 1 ? $eingabe : Format::heute();
    }

    private static function leerAlsNull(string $wert): ?string
    {
        return $wert === '' ? null : $wert;
    }
}
