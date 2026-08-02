<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Admin\AdminAnfragen;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Helpers\Http;
use Sartu\Services\Anfragebearbeitung;
use Sartu\Services\Bedarfsscheck;
use Sartu\Services\Umwandlung;

/**
 * `/admin/anfragen` — Portal-Lastenheft §4b.5.
 *
 * **Eine Liste mit vier Zuständen und einem Umwandlungsknopf, mehr nicht.** §0.3a zieht die
 * Grenze und begründet sie: Was automatisch nachfasst, bewertet oder verteilt, ist ein
 * Vertriebssystem. Hier gibt es deshalb keine Punktevergabe, keine Priorität, keine
 * Zuweisung und keinen Trichter.
 *
 * **`In Kunde und Projekt umwandeln`** legt `organizations`, `users` und `projects` an und
 * verschickt die Einladung. §4b.5 verlangt einen Bestätigungsdialog davor, „weil dabei ein
 * Zugang entsteht" — ohne JavaScript ist das eine eigene Seite mit einem zweiten Klick,
 * kein `confirm()`.
 *
 * Anfragen gehören **keiner** Organisation. Sie entstehen, bevor es einen Kunden gibt. Die
 * Mandantentrennung berührt sie damit nicht — die zentrale Adminprüfung im Router schon.
 */
final class AnfragenSteuerung
{
    /** @param array<string,string> $parameter */
    public function liste(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        // §4b.5: Filter nach Status. Ein unbekannter Wert filtert nicht — er ist kein
        // Fehler, sondern eine Adresse, die jemand von Hand getippt hat.
        $zustand = $_GET['zustand'] ?? null;
        $zustand = is_string($zustand) && in_array($zustand, AdminAnfragen::ZUSTAENDE, true) ? $zustand : null;

        // §4b.7: Filtern nach Herkunft — „aus der sich die Frage ‚welche Kampagne brachte
        // Aufträge?' von Hand beantworten lässt."
        $quelle = $_GET['quelle'] ?? null;
        $quelle = is_string($quelle) && trim($quelle) !== '' ? trim($quelle) : null;

        $anfragen = (new AdminAnfragen($nachweis))->alle($zustand);

        if ($quelle !== null) {
            $anfragen = array_values(array_filter(
                $anfragen,
                static fn (array $a) => ($a['utm_source'] ?? null) === $quelle,
            ));
        }

        return Antwort::html(Ansicht::seite('admin', 'admin-anfragen', [
            'titel'      => 'Anfragen',
            'angemeldet' => true,
            'anfragen'   => $anfragen,
            'zustand'    => $zustand,
            'quelle'     => $quelle,
            'quellen'    => self::quellen($nachweis),
        ]));
    }

    /** @param array<string,string> $parameter */
    public function einzeln(array $parameter = [], array $fehler = [], array $hinweise = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $anfrage = (new AdminAnfragen($nachweis))->finden((string) ($parameter['id'] ?? ''));

        if ($anfrage === null) {
            return Antwort::nichtGefunden();
        }

        return Antwort::html(Ansicht::seite('admin', 'admin-anfrage', [
            'titel'      => 'Anfrage von ' . (string) $anfrage['company'],
            'angemeldet' => true,
            'anfrage'    => $anfrage,
            'antworten'  => Bedarfsscheck::klartext(self::payload($anfrage)),
            'fehler'     => $fehler,
            'hinweise'   => $hinweise,
        ]));
    }

    /** @param array<string,string> $parameter */
    public function zustand(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, function (Anfragebearbeitung $dienst, string $id): ?string {
            return $dienst->zustandSetzen(
                $id,
                Http::getrimmteEingabe('zustand'),
                Http::getrimmteEingabe('notiz'),
                Http::gegenstelle(),
            );
        }, 'Der Zustand ist gespeichert.');
    }

    /** @param array<string,string> $parameter */
    public function notiz(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, function (Anfragebearbeitung $dienst, string $id): ?string {
            return $dienst->notizSpeichern($id, Http::getrimmteEingabe('notiz'), Http::gegenstelle());
        }, 'Die Notiz ist gespeichert.');
    }

    /**
     * Der Bestätigungsschritt vor der Umwandlung — §4b.5.
     *
     * Eine eigene Seite und kein Browserdialog: §9.5a-Denkweise, hier auf den Adminbereich
     * übertragen — was ohne JavaScript nicht geht, gibt es nicht. Und ein Dialog, den man
     * wegklickt, ist keine Bestätigung.
     *
     * @param array<string,string> $parameter
     */
    public function umwandelnFragen(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $anfrage = (new AdminAnfragen($nachweis))->finden((string) ($parameter['id'] ?? ''));

        if ($anfrage === null) {
            return Antwort::nichtGefunden();
        }

        return Antwort::html(Ansicht::seite('admin', 'admin-anfrage-umwandeln', [
            'titel'      => 'Anfrage umwandeln',
            'angemeldet' => true,
            'anfrage'    => $anfrage,
        ]));
    }

    /** @param array<string,string> $parameter */
    public function umwandeln(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $ergebnis = (new Umwandlung($nachweis))->ausfuehren(
            (string) ($parameter['id'] ?? ''),
            Http::getrimmteEingabe('paket'),
            Http::gegenstelle(),
        );

        if ($ergebnis['fehler'] !== null) {
            return $this->einzeln($parameter, [$ergebnis['fehler']]);
        }

        // Ein gescheiterter Mailversand rollt nichts zurueck: Der Zugang steht, und §6.3
        // haelt den Notweg genau fuer diesen Fall bereit. Der Admin erfaehrt es auf der
        // Projektseite.
        return Antwort::weiter(
            '/admin/projekte/' . $ergebnis['projektId'] . ($ergebnis['mailFehler'] ? '?einladung=fehler' : ''),
            303,
        );
    }

    /**
     * `Endgültig löschen` — §4b.4.
     *
     * Danach gibt es die Anfrage nicht mehr, also auch keine Detailseite, auf die sich
     * zurückleiten liesse. Der Weg führt deshalb zur Liste.
     *
     * @param array<string,string> $parameter
     */
    public function loeschen(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $id = (string) ($parameter['id'] ?? '');

        $fehler = (new Anfragebearbeitung($nachweis))
            ->endgueltigLoeschen($id, Http::getrimmteEingabe('grund'), Http::gegenstelle());

        if ($fehler !== null) {
            return $this->einzeln($parameter, [$fehler]);
        }

        return Antwort::weiter('/admin/anfragen', 303);
    }

    /**
     * `Datensatz exportieren` — alles, was gespeichert ist (§4b.4 Betroffenenrechte).
     *
     * Als JSON und nicht als Seite: Der Export ist ein Auskunftsdokument, das jemand
     * weitergibt. Er soll vollständig sein und nicht so aussehen, als sei er es.
     *
     * @param array<string,string> $parameter
     */
    public function exportieren(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $anfrage = (new AdminAnfragen($nachweis))->finden((string) ($parameter['id'] ?? ''));

        if ($anfrage === null) {
            return Antwort::nichtGefunden();
        }

        $anfrage['payload'] = self::payload($anfrage);

        return Antwort::datei(
            json_encode($anfrage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
            'application/json; charset=utf-8',
            'anfrage-' . $anfrage['id'] . '.json',
        );
    }

    // ------------------------------------------------------------------ intern

    /** @param array<string,string> $parameter */
    private function aktion(array $parameter, \Closure $tun, string $hinweis): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $id = (string) ($parameter['id'] ?? '');
        $fehler = $tun(new Anfragebearbeitung($nachweis), $id);

        return $fehler === null
            ? $this->einzeln($parameter, [], [$hinweis])
            : $this->einzeln($parameter, [$fehler]);
    }

    /** @return array<string,mixed> */
    private static function payload(array $anfrage): array
    {
        $roh = $anfrage['payload'] ?? null;

        if (!is_string($roh) || $roh === '') {
            return [];
        }

        try {
            $werte = json_decode($roh, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            // Ein unlesbares Feld ist kein Grund, die ganze Anfrage nicht anzuzeigen.
            return [];
        }

        return is_array($werte) ? $werte : [];
    }

    /** @return list<string> Die tatsaechlich vorkommenden Kampagnenquellen, ohne Doppelte. */
    private static function quellen(AdminNachweis $nachweis): array
    {
        $quellen = [];

        foreach ((new AdminAnfragen($nachweis))->alle() as $anfrage) {
            $quelle = $anfrage['utm_source'] ?? null;

            if (is_string($quelle) && $quelle !== '') {
                $quellen[$quelle] = true;
            }
        }

        $namen = array_keys($quellen);
        sort($namen);

        return $namen;
    }
}
