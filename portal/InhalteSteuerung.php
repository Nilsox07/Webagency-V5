<?php

declare(strict_types=1);

namespace Sartu\Portal;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenOeffnungszeiten;
use Sartu\Data\Customer\KundenProjekte;
use Sartu\Services\Oeffnungszeitendienst;
use Sartu\Services\Projektstatus;

/**
 * `/portal/inhalte` — Öffnungszeiten, die eine Pflegefunktion (§8.7).
 *
 * **Vor dem Onlinegang zeigt die Seite den Leerzustand**, kein Formular. §8.7 sagt dazu:
 * „Sobald Ihre Website online ist, können Sie hier Ihre Öffnungszeiten selbst pflegen." Ein
 * Formular, dessen Eingaben nirgendwo erscheinen, wäre ein Versprechen ohne Deckung — und
 * §0.3b verbietet genau das.
 *
 * Die Organisation kommt aus der Sitzung, das Projekt aus der Organisation. Es gibt keine
 * Kennung in der Adresse.
 */
final class InhalteSteuerung
{
    /**
     * @param array<string,string> $parameter
     * @param list<string> $fehler
     * @param list<string> $hinweise
     */
    public function formular(array $parameter = [], array $fehler = [], array $hinweise = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $projekt = (new KundenProjekte($bereich))->aktuelles();
        $speicher = new KundenOeffnungszeiten($bereich);

        $gepflegt = [];

        foreach ($speicher->wochentage() as $tag) {
            $gepflegt[(int) $tag['weekday']] = $tag;
        }

        return Antwort::html(Ansicht::seite('portal', 'portal-inhalte', [
            'titel'      => 'Öffnungszeiten',
            'angemeldet' => true,
            // §8.7: erst ab `live`. Davor der Leerzustand, kein Formular.
            'freigegeben' => $projekt !== null && (string) $projekt['status'] === Projektstatus::LIVE,
            'tage'       => $gepflegt,
            'ausnahmen'  => $speicher->ausnahmen(),
            'wartet'     => $speicher->wartetAufVeroeffentlichung(),
            'fehler'     => $fehler,
            'hinweise'   => $hinweise,
        ]));
    }

    /** @param array<string,string> $parameter */
    public function speichern(array $parameter = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $projekt = (new KundenProjekte($bereich))->aktuelles();

        if ($projekt === null || (string) $projekt['status'] !== Projektstatus::LIVE) {
            // Die Sperre steht hier und nicht nur in der Ansicht. Eine Ansicht, die einen
            // Knopf weglässt, hält niemanden davon ab, das Formular selbst zu schicken.
            return Antwort::nichtGefunden();
        }

        $fehler = (new Oeffnungszeitendienst($bereich))->einreichen($_POST);

        return $this->formular(
            $parameter,
            $fehler,
            $fehler === [] ? [Oeffnungszeitendienst::HINWEIS_EINGEREICHT] : [],
        );
    }
}
