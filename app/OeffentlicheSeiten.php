<?php

declare(strict_types=1);

namespace Sartu;

use Sartu\Data\RechtstexteSpeicher;
use Sartu\Helpers\Format;

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

        /*
         * **Layout `dokument`, nicht `oeffentlich`** — geändert am 16.08.2026.
         *
         * `oeffentlich` ist das Gerüst der Systemseiten: keine Marke, keine Navigation, eine
         * 46-rem-Spalte, eine Fusszeile aus zwei Verweisen. Ein Impressum, das nicht
         * erkennbar zur Website gehört, erfüllt § 5 DDG schlechter, als es könnte — die
         * Angaben müssen „leicht erkennbar, unmittelbar erreichbar und ständig verfügbar"
         * sein, und dazu gehört, dass der Leser sieht, wessen Impressum er liest.
         *
         * **Der Änderungsstand kommt aus `updated_at`**, nicht aus dem Text. Eine
         * Datumsangabe im Rumpf müsste jemand bei jeder Änderung von Hand nachziehen — und
         * ein falscher Stand unter einem Rechtstext ist schlimmer als keiner.
         */
        return Antwort::html(Ansicht::seite('dokument', 'rechtstext', [
            'titel'        => RechtstexteSpeicher::beschriftung($slug),
            'beschriftung' => RechtstexteSpeicher::beschriftung($slug),
            'rumpf'        => (string) $text['body'],
            'stand'        => isset($text['updated_at'])
                ? Format::datum((string) $text['updated_at'])
                : null,
            'pfad'         => '/' . $slug,
        ]));
    }

    private function speicher(): RechtstexteSpeicher
    {
        return $this->speicher ?? new RechtstexteSpeicher();
    }
}
