<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Admin\AdminBelege;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Zahlungseingaenge;
use Sartu\Helpers\Format;
use Sartu\Helpers\Http;
use Sartu\Services\Belegerzeugung;
use Sartu\Services\Belegversand;
use Sartu\Services\Steuerexport;

/**
 * Belege im internen Bereich — `18_BELEGE_UND_ZAHLUNG.md` Abschnitte 6, 8 und 9.
 *
 * Drei Dinge: den Beleg abrufen, ihn per Mail schicken, den Zeitraum an den Steuerberater
 * übergeben. Dazu die Liste der Zahlungsvorgänge, die nichts bewirkt haben — §6 verlangt,
 * dass Abweichungen „im Adminbereich sichtbar gemacht" werden, und eine Zeile in einer
 * Tabelle, die niemand ansieht, ist nicht sichtbar.
 *
 * **Der Betreiber sieht jeden Beleg**, nicht nur die einer Organisation. Das ist keine
 * ausgelassene Prüfung, sondern der Zweck: §8 übergibt den Ausgang des Betriebs. Der Filter
 * fehlt hier nicht — er gehört in die andere Klasse, `Customer\KundenBelege`, und steht dort.
 */
final class BelegeSteuerung
{
    /**
     * @param array<string,string> $parameter
     * @param list<string> $fehler
     * @param list<string> $hinweise
     */
    public function uebersicht(array $parameter = [], array $fehler = [], array $hinweise = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        // Vorbelegt auf den laufenden Monat — der häufigste Zeitraum, und ein leeres
        // Datumsfeld ist die häufigste Ursache für einen leeren Export.
        $heute = Format::heute();
        $monatsbeginn = substr($heute, 0, 7) . '-01';

        return Antwort::html(Ansicht::seite('admin', 'admin-belege', [
            'titel'        => 'Belege und Übergabe',
            'angemeldet'   => true,
            'von'          => $monatsbeginn,
            'bis'          => $heute,
            'abweichungen' => (new Zahlungseingaenge())->abweichungen(),
            'fehler'       => $fehler,
            'hinweise'     => $hinweise,
        ]));
    }

    /**
     * Der Beleg als Datei.
     *
     * Über `Belegerzeugung::inhalt()` und damit durch den Prüfsummenvergleich (§7). Weicht
     * die Datei ab, gibt es keinen Download, sondern einen Fehler auf der Übersicht — der
     * Betreiber ist der Einzige, der etwas dagegen tun kann.
     *
     * @param array<string,string> $parameter
     */
    public function datei(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $beleg = (new AdminBelege($nachweis))->finden((string) ($parameter['id'] ?? ''));

        if ($beleg === null) {
            return Antwort::nichtGefunden();
        }

        try {
            $inhalt = (new Belegerzeugung())->inhalt($beleg);
        } catch (\RuntimeException $ausnahme) {
            return $this->uebersicht([], [$ausnahme->getMessage()]);
        }

        $art = (string) $beleg['kind'] === 'storno' ? 'Stornorechnung' : 'Rechnung';

        return Antwort::datei($inhalt, 'application/pdf', $art . '-' . (string) $beleg['number'] . '.pdf');
    }

    /**
     * §9: „Ein Knopf je Beleg, der die Mail mit dem Beleg im Anhang verschickt."
     *
     * @param array<string,string> $parameter
     */
    public function senden(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $belegId = (string) ($parameter['id'] ?? '');
        $beleg = (new AdminBelege($nachweis))->finden($belegId);

        if ($beleg === null) {
            return Antwort::nichtGefunden();
        }

        $fehler = (new Belegversand($nachweis))->senden($belegId, Http::gegenstelle());
        $ziel = '/admin/rechnungen/' . (string) $beleg['invoice_id'];

        if ($fehler !== []) {
            return (new RechnungenSteuerung())->einzeln(['id' => (string) $beleg['invoice_id']], $fehler);
        }

        return Antwort::weiter($ziel, 303);
    }

    /**
     * Die Übergabe an den Steuerberater — §8.
     *
     * Ein POST und kein GET, obwohl nichts geschrieben wird: Der Export trägt jede
     * Kundenanschrift und jeden Betrag des Zeitraums. Eine Adresse, die man weitergeben oder
     * versehentlich in einen Verlauf schreiben kann, ist dafür der falsche Träger.
     *
     * @param array<string,string> $parameter
     */
    public function export(array $parameter = []): Antwort
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            return Antwort::weiter('/admin/anmelden');
        }

        $ergebnis = (new Steuerexport($nachweis))->erzeugen(
            Http::getrimmteEingabe('von'),
            Http::getrimmteEingabe('bis'),
        );

        if ($ergebnis['fehler'] !== []) {
            return $this->uebersicht([], $ergebnis['fehler']);
        }

        if ($ergebnis['zeilen'] === 0) {
            // Keine leere Datei ausliefern: Sie sieht aus wie ein Ergebnis und ist eine
            // Fehlbedienung — meist ein Zeitraum, in dem nichts ausgestellt wurde.
            return $this->uebersicht([], [], ['In diesem Zeitraum wurde keine Rechnung ausgestellt.']);
        }

        return Antwort::datei((string) $ergebnis['inhalt'], 'text/csv; charset=utf-8', (string) $ergebnis['dateiname']);
    }
}
