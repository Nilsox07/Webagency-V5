<?php

declare(strict_types=1);

namespace Sartu\Portal;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenNachrichten;
use Sartu\Data\Customer\KundenProjekte;
use Sartu\Data\Customer\KundenRechnungen;
use Sartu\Helpers\Http;
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
            'projekt'    => $projekt,
        ]));
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
