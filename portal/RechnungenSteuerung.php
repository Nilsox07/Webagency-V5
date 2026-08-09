<?php

declare(strict_types=1);

namespace Sartu\Portal;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Customer\KundenBelege;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenNachrichten;
use Sartu\Data\Customer\KundenProjekte;
use Sartu\Data\Customer\KundenRechnungen;
use Sartu\Helpers\Http;
use Sartu\Services\Belegerzeugung;
use Sartu\Helpers\Validate;
use Sartu\Sitzung;

/**
 * `/portal/rechnungen` und `/portal/hilfe` — Portal-Lastenheft §8.5 und §8.9.
 *
 * **Der Knopf `Jetzt bezahlen` fuehrt zum Zahlungsdienst und kommt nicht zurueck.** §12:
 * „Der Zahlungsstatus wird niemals aus der Rueckkehr des Browsers abgeleitet." Es gibt
 * deshalb keine Rueckkehrroute, keinen `?zahlung=erfolgreich`-Parameter und keinen
 * Handler, der einen solchen lesen wuerde (Testfall 14).
 */
final class RechnungenSteuerung
{
    /** @param array<string,string> $parameter */
    public function liste(array $parameter = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $projekt = (new KundenProjekte($bereich))->aktuelles();

        return Antwort::html(Ansicht::seite('portal', 'portal-rechnungen', [
            'titel'      => 'Ihre Rechnungen',
            'angemeldet' => true,
            'rechnungen' => (new KundenRechnungen($bereich))->liste(),
            'belege'     => self::belegeJeRechnung($bereich),
            'projekt'    => $projekt,
        ]));
    }

    /**
     * Der Beleg zu einer Rechnung — `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 9.
     *
     * **Die Pruefung ist doppelt und steht in einer Abfrage** (`KundenBelege`): Gibt es den
     * Beleg, und gehoert er zur Sitzungsorganisation? Sonst **404**, nicht 403 — 403 verriete,
     * dass es ihn gibt.
     *
     * Die Datei kommt ueber `Belegerzeugung::inhalt()` und damit durch den
     * Pruefsummenvergleich (§7). Weicht sie ab, ist das ein Fehler und keine Warnung; der
     * Kunde bekommt dann keine veraenderte Datei, sondern eine Fehlerseite.
     *
     * @param array<string,string> $parameter
     */
    public function beleg(array $parameter = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $beleg = (new KundenBelege($bereich))->finden((string) ($parameter['id'] ?? ''));

        if ($beleg === null) {
            return Antwort::nichtGefunden();
        }

        try {
            $inhalt = (new Belegerzeugung())->inhalt($beleg);
        } catch (\RuntimeException) {
            // Kein Systemtext nach aussen (§3 Regel 12). Der Kunde kann an einer abweichenden
            // Pruefsumme nichts aendern; fuer ihn ist der Beleg gerade nicht abrufbar.
            return Antwort::html(Ansicht::seite('oeffentlich', 'fehler', [
                'titel'   => 'Der Beleg ist gerade nicht abrufbar',
                'meldung' => 'Bitte melden Sie sich kurz bei uns — wir schicken ihn Ihnen zu.',
                'kennung' => null,
            ]), 500);
        }

        $art = (string) $beleg['kind'] === 'storno' ? 'Stornorechnung' : 'Rechnung';

        return Antwort::datei($inhalt, 'application/pdf', $art . '-' . (string) $beleg['number'] . '.pdf');
    }

    /**
     * Die Belege, nach Rechnung geordnet — damit die Liste je Zeile weiss, ob es einen gibt.
     *
     * @return array<string,array<string,mixed>>
     */
    private static function belegeJeRechnung(KundenBereich $bereich): array
    {
        $nachRechnung = [];

        foreach ((new KundenBelege($bereich))->liste() as $beleg) {
            $rechnungId = (string) ($beleg['invoice_id'] ?? '');

            // Der **neueste** gewinnt: §7 laesst eine zweite Erzeugung ausdruecklich zu, und
            // die Liste ist absteigend sortiert.
            if ($rechnungId !== '' && !isset($nachRechnung[$rechnungId])) {
                $nachRechnung[$rechnungId] = $beleg;
            }
        }

        return $nachRechnung;
    }

    /**
     * @param array<string,string> $parameter
     * @param list<string> $fehler
     * @param list<string> $hinweise
     */
    public function hilfe(array $parameter = [], array $fehler = [], array $hinweise = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();

        return Antwort::html(Ansicht::seite('portal', 'portal-hilfe', [
            'titel'       => 'Hilfe',
            'angemeldet'  => true,
            'nachrichten' => (new KundenNachrichten($bereich))->liste(),
            'fehler'      => $fehler,
            'hinweise'    => $hinweise,
        ]));
    }

    /** @param array<string,string> $parameter */
    public function nachrichtSenden(array $parameter = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $text = Http::getrimmteEingabe('body');

        if (!Validate::gefuellt($text)) {
            return $this->hilfe([], ['Bitte schreiben Sie uns, worum es geht.']);
        }

        $projekt = (new KundenProjekte($bereich))->aktuelles();

        (new KundenNachrichten($bereich))->anlegen(
            $text,
            $projekt === null ? null : (string) $projekt['id'],
            (string) Sitzung::wert(Sitzung::BENUTZER),
        );

        return $this->hilfe([], [], ['Ihre Nachricht ist angekommen. Wir antworten schriftlich.']);
    }
}
