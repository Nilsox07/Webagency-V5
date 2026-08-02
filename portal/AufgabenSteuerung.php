<?php

declare(strict_types=1);

namespace Sartu\Portal;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Customer\KundenAngebote;
use Sartu\Data\Customer\KundenAufgaben;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenDateien;
use Sartu\Data\Customer\KundenFreigaben;
use Sartu\Data\Customer\KundenProjekte;
use Sartu\Helpers\Http;
use Sartu\Services\Aufgabendienst;
use Sartu\Services\Uploaddienst;
use Sartu\Sitzung;

/**
 * `/portal/aufgaben` und die Dateiausspielung — Portal-Lastenheft §8.3 und §11.
 *
 * **Die Ausspielroute ist die eigentliche Sicherheitsstelle.** §11: „Auslieferung nur über
 * eine Route, die Session und Organisationszugehörigkeit prüft." Beides passiert hier:
 * die Sitzung zentral im Router, die Organisation über `KundenDateien`, dessen Abfrage die
 * eigene Spalte **und** den Verbund prüft.
 *
 * Ausgeliefert wird **immer** als Download, nie im Fenster. §11 nennt das für SVG
 * (Skriptrisiko) — die Regel gilt hier für jede Datei, weil eine Sonderregel für einen Typ
 * eine Sonderregel ist, die jemand vergisst.
 */
final class AufgabenSteuerung
{
    /** @param array<string,string> $parameter */
    public function liste(array $parameter = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();

        return Antwort::html(Ansicht::seite('portal', 'portal-aufgaben', [
            'titel'      => 'Ihre Aufgaben',
            'angemeldet' => true,
            'aufgaben'   => (new KundenAufgaben($bereich))->liste(),
        ]));
    }

    /**
     * @param array<string,string> $parameter
     * @param list<string> $fehler
     */
    public function einzeln(array $parameter = [], array $fehler = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $aufgabe = (new KundenAufgaben($bereich))->finden((string) ($parameter['id'] ?? ''));

        // §3 Regel 2: gibt es nicht ODER gehört nicht mir — 404, nicht 403.
        if ($aufgabe === null) {
            return Antwort::nichtGefunden();
        }

        $projektId = (string) $aufgabe['project_id'];
        $istFreigabe = (string) $aufgabe['kind'] === 'freigabe';

        return Antwort::html(Ansicht::seite('portal', 'portal-aufgabe', [
            'titel'       => (string) $aufgabe['title'],
            'angemeldet'  => true,
            'aufgabe'     => $aufgabe,
            'dateien'     => (new KundenDateien($bereich))->jeAufgabe((string) $aufgabe['id']),
            'fehler'      => $fehler,
            'offenePflicht' => $istFreigabe
                ? (new KundenAufgaben($bereich))->offenePflichtaufgaben($projektId)
                : 0,
            'bisher'      => $istFreigabe
                ? (new KundenAufgaben($bereich))->erledigteMitAntwort($projektId)
                : [],
            'freigabe'    => $istFreigabe
                ? (new KundenFreigaben($bereich))->finden($projektId, KundenFreigaben::INHALTE)
                : null,
            'angebot'     => $istFreigabe ? (new KundenAngebote($bereich))->aktuelles() : null,
            'projekt'     => (new KundenProjekte($bereich))->finden($projektId),
        ]));
    }

    /** @param array<string,string> $parameter */
    public function abschliessen(array $parameter = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();

        $fehler = (new Aufgabendienst($bereich))->abschliessen(
            (string) ($parameter['id'] ?? ''),
            $_POST,
            (string) Sitzung::wert(Sitzung::BENUTZER),
            self::ip(),
        );

        return $fehler === []
            ? Antwort::weiter('/portal/aufgaben', 303)
            : $this->einzeln($parameter, $fehler);
    }

    /** §8.3, `kind = upload` — §11 prüft im Dienst, nicht hier. */
    public function hochladen(array $parameter = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $aufgabeId = (string) ($parameter['id'] ?? '');

        // Erst prüfen, ob die Aufgabe überhaupt zu dieser Organisation gehört — sonst
        // legte ein Upload eine Datei an einer fremden Aufgabe an.
        if ((new KundenAufgaben($bereich))->finden($aufgabeId) === null) {
            return Antwort::nichtGefunden();
        }

        $ergebnis = (new Uploaddienst($bereich))->annehmen(
            $_FILES['datei'] ?? [],
            $aufgabeId,
            ($_POST['rights_confirmed'] ?? null) === '1',
            (string) Sitzung::wert(Sitzung::BENUTZER),
        );

        return $ergebnis['fehler'] === null
            ? Antwort::weiter('/portal/aufgaben/' . $aufgabeId, 303)
            : $this->einzeln($parameter, [$ergebnis['fehler']]);
    }

    /**
     * Liefert eine Datei aus — §11.
     *
     * @param array<string,string> $parameter
     */
    public function datei(array $parameter = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $datei = (new KundenDateien($bereich))->finden((string) ($parameter['id'] ?? ''));

        if ($datei === null) {
            return Antwort::nichtGefunden();
        }

        $pfad = (new Uploaddienst($bereich))->pfadZu((string) $datei['stored_name']);

        if (!is_file($pfad)) {
            return Antwort::nichtGefunden();
        }

        return Antwort::datei(
            (string) file_get_contents($pfad),
            (string) $datei['mime_type'],
            (string) $datei['original_name'],
        );
    }

    private static function ip(): ?string
    {
        $ip = Http::gegenstelle();

        return $ip === '' ? null : $ip;
    }
}
