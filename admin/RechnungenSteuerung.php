<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminProjekte;
use Sartu\Data\Admin\AdminRechnungen;
use Sartu\Helpers\Http;
use Sartu\Services\Rechnungsdienst;

/**
 * Rechnungen im internen Bereich — Portal-Lastenheft §12.
 *
 * **Jede Zustandsaenderung braucht den Grundlagentext.** Er steht im Formular als
 * Pflichtfeld und wird im Dienst geprueft — nicht nur dort, wo die Oberflaeche ihn
 * anzeigt. §12: „Ohne Grundlagentext laesst sich keine dieser Aenderungen speichern."
 *
 * **Es gibt keine Rueckkehrroute vom Zahlungsdienst.** Nicht als unbenutzte Methode, nicht
 * als auskommentierter Entwurf. Was nicht existiert, kann niemand versehentlich verdrahten.
 */
final class RechnungenSteuerung
{
    /** @param array<string,string> $parameter */
    public function liste(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $zustand = $_GET['zustand'] ?? null;
        $zustand = is_string($zustand) && $zustand !== '' ? $zustand : null;

        $projekte = [];

        foreach ((new AdminProjekte($nachweis))->alle() as $projekt) {
            $projekte[(string) $projekt['id']] = (string) $projekt['title'];
        }

        return Antwort::html(Ansicht::seite('admin', 'admin-rechnungen', [
            'titel'      => 'Rechnungen',
            'angemeldet' => true,
            'rechnungen' => (new AdminRechnungen($nachweis))->alle($zustand),
            'projekte'   => $projekte,
            'zustand'    => $zustand,
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

        $rechnung = (new AdminRechnungen($nachweis))->finden((string) ($parameter['id'] ?? ''));

        if ($rechnung === null) {
            return Antwort::nichtGefunden();
        }

        return Antwort::html(Ansicht::seite('admin', 'admin-rechnung', [
            'titel'      => 'Rechnung ' . (string) $rechnung['number'],
            'angemeldet' => true,
            'rechnung'   => $rechnung,
            'projekt'    => (new AdminProjekte($nachweis))->finden((string) $rechnung['project_id']),
            'fehler'     => $fehler,
            'hinweise'   => $hinweise,
        ]));
    }

    /** @param array<string,string> $parameter */
    public function anlegen(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $ergebnis = (new Rechnungsdienst($nachweis))
            ->anlegen((string) ($parameter['id'] ?? ''), $_POST, Http::gegenstelle());

        return $ergebnis['fehler'] === []
            ? Antwort::weiter('/admin/rechnungen/' . $ergebnis['id'], 303)
            : (new ProjekteSteuerung())->einzeln($parameter, $ergebnis['fehler']);
    }

    /** @param array<string,string> $parameter */
    public function senden(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, static fn (Rechnungsdienst $d, string $id): array
            => $d->senden($id, Http::gegenstelle()), 'Die Rechnung ist gesendet.');
    }

    /**
     * Der einzige Weg zu `bezahlt` — §12.
     *
     * @param array<string,string> $parameter
     */
    public function zahlungEintragen(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, static function (Rechnungsdienst $d, string $id): array {
            $betrag = Http::getrimmteEingabe('paid_cents');

            return $d->zahlungEintragen(
                $id,
                is_numeric($betrag) ? (int) $betrag : -1,
                Http::getrimmteEingabe('grundlage'),
                Http::gegenstelle(),
            );
        }, 'Der Zahlungsstand ist eingetragen und protokolliert.');
    }

    /** @param array<string,string> $parameter */
    public function stornieren(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, static fn (Rechnungsdienst $d, string $id): array
            => $d->stornieren($id, Http::getrimmteEingabe('grundlage'), Http::gegenstelle()),
            'Die Rechnung ist storniert und der Vorgang protokolliert.');
    }

    /** @param array<string,string> $parameter */
    public function zahlungslink(array $parameter = []): Antwort
    {
        return $this->aktion($parameter, static fn (Rechnungsdienst $d, string $id): array
            => $d->zahlungslinkSetzen($id, Http::getrimmteEingabe('mollie_payment_url'), Http::gegenstelle()),
            'Der Zahlungslink ist eingetragen.');
    }

    /** @param array<string,string> $parameter */
    private function aktion(array $parameter, \Closure $tun, string $hinweis): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $fehler = $tun(new Rechnungsdienst($nachweis), (string) ($parameter['id'] ?? ''));

        return $fehler === []
            ? $this->einzeln($parameter, [], [$hinweis])
            : $this->einzeln($parameter, $fehler);
    }
}
