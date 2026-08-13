<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\AuditProtokoll;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Helpers\Http;
use Sartu\Services\BetreiberdatenDienst;
use Sartu\Services\Gruenderbild;
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
            // Getrennt gelesen: Bei einem Fehler im Formular traegt `$werte` die Eingabe des
            // Betreibers, das Bild aber steht schon in der Datenbank. Aus `$werte` gelesen
            // waere es nach einem abgewiesenen Formular verschwunden, obwohl es liegt.
            'bild'       => (new Gruenderbild())->name(),
        ]));
    }

    /**
     * Nimmt das Gründerbild an — `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4c.
     *
     * **Eigene Route statt eines Feldes im Betriebsformular.** Ein Upload braucht
     * `enctype="multipart/form-data"`; das ganze Betriebsformular darauf umzustellen hiesse,
     * bei jedem Speichern der Anschrift eine Dateiannahme mitlaufen zu lassen.
     *
     * @param array<string,string> $parameter
     */
    public function gruenderbild(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $dienst = new Gruenderbild();
        $vorher = $dienst->name();
        $ergebnis = $dienst->annehmen($_FILES['bild'] ?? []);

        if ($ergebnis['fehler'] !== null) {
            return $this->formular([], [$ergebnis['fehler']]);
        }

        // §1.4a: Jede Aenderung an den Betreiberdaten wird protokolliert. Der Grund ist hier
        // die Handlung selbst — ein Pflichtfeld dafuer waere eine zweite Huerde vor einem
        // Vorgang, der nichts Rechtliches verschiebt.
        $this->protokollieren($nachweis->adminBenutzerId, $vorher, $ergebnis['name'], 'Bild hinterlegt');

        return $this->formular([], [], ['Das Bild ist hinterlegt. Die Sektion „Wer dahintersteckt" '
            . 'erscheint, sobald auch Name und Text stehen.']);
    }

    /** @param array<string,string> $parameter */
    public function gruenderbildEntfernen(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $dienst = new Gruenderbild();
        $vorher = $dienst->name();

        if ($vorher === null) {
            return $this->formular([], ['Es ist kein Bild hinterlegt.']);
        }

        $dienst->entfernen();
        $this->protokollieren($nachweis->adminBenutzerId, $vorher, null, 'Bild entfernt');

        return $this->formular([], [], ['Das Bild ist entfernt. Die Sektion „Wer dahintersteckt" '
            . 'entfällt damit wieder.']);
    }

    private function protokollieren(string $adminBenutzerId, ?string $alt, ?string $neu, string $grund): void
    {
        (new AuditProtokoll())->schreiben(
            aktion: 'betreiberdaten_geaendert',
            objektart: 'operator_settings',
            objektId: null,
            akteurBenutzerId: $adminBenutzerId,
            alterWert: $alt,
            neuerWert: $neu,
            grund: $grund,
            detail: ['feld' => 'gruender_bild'],
            ip: Http::gegenstelle(),
        );
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
