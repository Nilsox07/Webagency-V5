<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Admin\AdminAngebote;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminOeffnungszeiten;
use Sartu\Data\Admin\AdminOrganisationen;
use Sartu\Data\Admin\AdminProjekte;
use Sartu\Data\Admin\AdminRechnungen;
use Sartu\Data\Admin\AdminVorschau;
use Sartu\Helpers\Http;
use Sartu\Services\AngebotDienst;

/**
 * Projekte und Angebote im internen Bereich — Portal-Lastenheft §4, §4c, §5.1a.
 *
 * **Der Adminbereich ist eine eigene Zugriffsschicht** (§3 Regel 2). Er liest über
 * `Data\Admin\*`, und jede dieser Klassen verlangt einen `AdminNachweis` im Konstruktor.
 * Es gibt keinen gemeinsamen Codepfad mit dem Kundenbereich, der den Organisationsfilter
 * je nach Rolle weglässt.
 *
 * **Das Angebot entsteht als `entwurf` und wird getrennt gesendet.** §5.2: Ein Entwurf ist
 * für den Kunden unsichtbar. Erst `senden` prüft ein zweites Mal gegen §4 und §4c — auch
 * gegen die BFSG-Sperre, die kein Bußgeldrisiko durchlässt, das beim Anlegen noch nicht
 * bestand.
 */
final class ProjekteSteuerung
{
    /** @param array<string,string> $parameter */
    public function liste(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $organisationen = [];

        foreach ((new AdminOrganisationen($nachweis))->alle() as $organisation) {
            $organisationen[(string) $organisation['id']] = (string) $organisation['legal_name'];
        }

        return Antwort::html(Ansicht::seite('admin', 'admin-projekte', [
            'titel'          => 'Projekte',
            'angemeldet'     => true,
            'projekte'       => (new AdminProjekte($nachweis))->alle(),
            'organisationen' => $organisationen,
        ]));
    }

    /**
     * @param array<string,string> $parameter
     * @param list<string> $fehler
     * @param list<string> $hinweise
     */
    public function einzeln(array $parameter = [], array $fehler = [], array $hinweise = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $projekt = (new AdminProjekte($nachweis))->finden((string) ($parameter['id'] ?? ''));

        if ($projekt === null) {
            return Antwort::nichtGefunden();
        }

        $angebote = (new AdminAngebote($nachweis))->jeProjekt((string) $projekt['id']);

        if (($_GET['einladung'] ?? null) === 'fehler') {
            $fehler[] = 'Die Einladung ging nicht raus. Der Zugang besteht trotzdem — der Kunde '
                . 'kann sich einen Anmeldelink selbst anfordern.';
        }

        return Antwort::html(Ansicht::seite('admin', 'admin-projekt', [
            'titel'       => 'Projekt ' . (string) $projekt['title'],
            'angemeldet'  => true,
            'projekt'     => $projekt,
            'angebote'    => $angebote,
            'rechnungen'  => (new AdminRechnungen($nachweis))->jeProjekt((string) $projekt['id']),
            'runden'      => $this->rundenMitRueckmeldungen($nachweis, (string) $projekt['id']),
            'freigaben'   => (new AdminVorschau($nachweis))->freigaben((string) $projekt['id']),
            'domainstand' => (new AdminVorschau($nachweis))->domainstand((string) $projekt['id']),
            'zeiten'      => (new AdminOeffnungszeiten($nachweis))
                ->wochentage((string) $projekt['organization_id']),
            'zeitausnahmen' => (new AdminOeffnungszeiten($nachweis))
                ->ausnahmen((string) $projekt['organization_id']),
            // Vorbelegt aus §4c — der Admin ändert, was er ändern will, aber er tippt die
            // drei festen Texte nicht ab.
            'vorbelegung' => $angebote === []
                ? (new AngebotDienst($nachweis))->vorbelegung((string) $projekt['package'])
                : null,
            'fehler'      => $fehler,
            'hinweise'    => $hinweise,
        ]));
    }

    /**
     * §9.2 „Feedback": die Rückmeldungen hängen an ihrer Runde.
     *
     * Sie werden hier zusammengesetzt und nicht in der Ansicht geholt — eine Ansicht liest
     * keine Daten nach (§1.3).
     *
     * @return list<array<string,mixed>>
     */
    private function rundenMitRueckmeldungen(AdminNachweis $nachweis, string $projektId): array
    {
        $vorschau = new AdminVorschau($nachweis);

        return array_map(
            static fn (array $runde) => $runde + ['rueckmeldungen' => $vorschau->rueckmeldungen((string) $runde['id'])],
            $vorschau->runden($projektId),
        );
    }

    /** @param array<string,string> $parameter */
    public function angebotAnlegen(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $ergebnis = (new AngebotDienst($nachweis))
            ->anlegen((string) ($parameter['id'] ?? ''), $_POST, Http::gegenstelle());

        return $ergebnis['fehler'] === []
            ? $this->einzeln($parameter, [], ['Das Angebot ist als Entwurf gespeichert.'])
            : $this->einzeln($parameter, $ergebnis['fehler']);
    }

    /**
     * `Angebot senden` — §5.1a, Zeile *(Anlage)* → `angebot_offen`.
     *
     * @param array<string,string> $parameter
     */
    public function angebotSenden(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $angebotId = (string) ($parameter['id'] ?? '');
        $angebot = (new AdminAngebote($nachweis))->finden($angebotId);

        if ($angebot === null) {
            return Antwort::nichtGefunden();
        }

        $fehler = (new AngebotDienst($nachweis))->senden($angebotId, Http::gegenstelle());

        return $this->einzeln(
            ['id' => (string) $angebot['project_id']],
            $fehler,
            $fehler === [] ? ['Das Angebot ist gesendet. Der Kunde sieht es in seinem Bereich.'] : [],
        );
    }
}
