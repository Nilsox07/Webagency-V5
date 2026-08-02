<?php

declare(strict_types=1);

namespace Sartu;

use Sartu\Data\RechtstexteSpeicher;

/**
 * Die oeffentlich erreichbaren Seiten, die A0 braucht.
 *
 * Das ist bewusst wenig: Die oeffentliche SARTU-Website entsteht erst nach Stufe B
 * (`REIHENFOLGE.md`, „Zwei Livegaenge"). Was hier steht, sind die drei Rechtstextrouten —
 * ohne sie liesse sich nicht pruefen, dass ein Text mit `audience = kunde` oeffentlich NICHT
 * abrufbar ist (Testfall 82).
 *
 * §0.3b: keine toten Menuepunkte, keine „kommt bald"-Bereiche. Deshalb gibt es hier keine
 * Startseite, die eine Website andeutet, die es noch nicht gibt.
 */
final class OeffentlicheSeiten
{
    public function __construct(private readonly ?RechtstexteSpeicher $speicher = null)
    {
    }

    /** @param array<string,string> $parameter */
    public function impressum(array $parameter = []): Antwort
    {
        return $this->rechtstext('impressum');
    }

    /** @param array<string,string> $parameter */
    public function datenschutz(array $parameter = []): Antwort
    {
        return $this->rechtstext('datenschutz');
    }

    /** @param array<string,string> $parameter */
    public function agb(array $parameter = []): Antwort
    {
        return $this->rechtstext('agb');
    }

    /**
     * Liefert nur `status = freigegeben` UND `audience = oeffentlich`.
     *
     * Alles andere ist hier ein 404 — nicht 403 und nicht „in Arbeit". Ein Entwurf, der
     * oeffentlich als vorhanden erkennbar ist, ist ein halb veroeffentlichter Rechtstext.
     */
    private function rechtstext(string $slug): Antwort
    {
        $text = $this->speicher()->oeffentlich($slug);

        if ($text === null) {
            return Antwort::nichtGefunden();
        }

        return Antwort::html(Ansicht::seite('oeffentlich', 'rechtstext', [
            'titel'        => RechtstexteSpeicher::beschriftung($slug),
            'beschriftung' => RechtstexteSpeicher::beschriftung($slug),
            'rumpf'        => (string) $text['body'],
        ]));
    }

    private function speicher(): RechtstexteSpeicher
    {
        return $this->speicher ?? new RechtstexteSpeicher();
    }
}
