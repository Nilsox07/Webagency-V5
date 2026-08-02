<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Helpers\Http;
use Sartu\Services\BetreiberdatenDienst;
use Sartu\Services\Startsperre;

/**
 * Uebersicht und Betreiberdaten des internen Bereichs — Portal-Lastenheft §1.4a.
 *
 * Fuer `operator_settings` kennt der Adminbereich nur UPDATE: kein INSERT, kein DELETE.
 * Die eine Zeile hat die Ersteinrichtung angelegt.
 */
final class BetriebSteuerung
{
    /** @param array<string,string> $parameter */
    public function uebersicht(array $parameter = []): Antwort
    {
        return Antwort::html(Ansicht::seite('admin', 'admin-uebersicht', [
            'titel'        => 'Übersicht',
            'angemeldet'   => true,
            'hindernisse'  => (new Startsperre())->hindernisse(),
            'betreiber'    => (new BetreiberdatenSpeicher())->lesen(),
        ]));
    }

    /**
     * @param array<string,string> $parameter
     * @param list<string> $fehler
     * @param list<string> $hinweise
     * @param array<string,mixed>|null $werte
     */
    public function formular(array $parameter = [], array $fehler = [], array $hinweise = [], ?array $werte = null): Antwort
    {
        return Antwort::html(Ansicht::seite('admin', 'admin-betrieb', [
            'titel'      => 'Betreiberdaten',
            'angemeldet' => true,
            'fehler'     => $fehler,
            'hinweise'   => $hinweise,
            'werte'      => $werte ?? (new BetreiberdatenSpeicher())->lesen() ?? [],
        ]));
    }

    /** @param array<string,string> $parameter */
    public function speichern(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $eingabe = [];
        foreach (BetreiberdatenSpeicher::SCHREIBBARE_FELDER as $feld) {
            $eingabe[$feld] = Http::getrimmteEingabe($feld);
        }

        $dienst = new BetreiberdatenDienst();
        $fehler = $dienst->pruefen($eingabe);
        $grund = Http::getrimmteEingabe('grund');

        if ($grund === '') {
            $fehler[] = 'Tragen Sie einen Grund ein. Er steht später im Protokoll.';
        }

        if ($fehler !== []) {
            return $this->formular([], $fehler, [], $eingabe);
        }

        $hinweise = $dienst->speichern($eingabe, $grund, $nachweis->adminBenutzerId, Http::gegenstelle());
        $hinweise[] = 'Die Betreiberdaten sind gespeichert.';

        return $this->formular([], [], $hinweise);
    }
}
