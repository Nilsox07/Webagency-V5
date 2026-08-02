<?php

declare(strict_types=1);

namespace Sartu;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Services\Auftragslage;
use Sartu\Services\Startseitentexte;
use Sartu\Services\Websitetexte;

/**
 * Die öffentliche SARTU-Website — Website-Lastenheft §5 ff.
 *
 * ## Kein Sitzungsbezug, deshalb cachebar
 *
 * §1: „Öffentliche Seiten hängen nicht an einer Sitzung und dürfen als statische Antwort
 * ausgeliefert werden." Diese Klasse liest deshalb **keine** Sitzung. Sie liest genau eine
 * Zeile aus der Datenbank — die Betreiberdaten — und daraus genau zwei Werte: die
 * Auftragslage (§5a) und `kleinunternehmer` (§19 UStG).
 *
 * ## Warum die zwei Werte trotzdem aus der Datenbank kommen
 *
 * Beide sind Aussagen über den Betrieb. §5a: „Der Wert wird im internen Bereich gepflegt und
 * nie im Quelltext erfunden." Und die Umsatzsteuerzeile ist ein Pflichthinweis, dessen
 * richtige Fassung davon abhängt, ob der Betreiber Kleinunternehmer ist.
 *
 * **Fehlt die Zeile, gilt: nichts anzeigen und mit Umsatzsteuer rechnen.** Ein fehlender
 * Wert ist keine Auftragslage, und die Regelbesteuerung ist der Fall, in dem der
 * Pflichthinweis stehen muss.
 */
final class Website
{
    public function __construct(private readonly ?BetreiberdatenSpeicher $betrieb = null)
    {
    }

    /** @param array<string,string> $parameter */
    public function start(array $parameter = []): Antwort
    {
        return $this->seite('website-start', [
            'titel'        => Startseitentexte::TITEL,
            'beschreibung' => Startseitentexte::BESCHREIBUNG,
            'pfad'         => '/',
            'schema'       => Strukturdaten::organisationUndWebsite(),
        ]);
    }

    // ------------------------------------------------------------------ intern

    /**
     * Baut eine öffentliche Seite mit allem, was §17 von jeder Seite verlangt.
     *
     * @param array<string,mixed> $werte
     */
    private function seite(string $ansicht, array $werte): Antwort
    {
        $daten = $this->betriebsdaten();

        return Antwort::html(Ansicht::seite('website', $ansicht, $werte + [
            'brotkrumen'       => [],
            'noindex'          => false,
            'schema'           => null,
            'auftragslage'     => Auftragslage::anzeige($daten['auftragslage']),
            'preishinweis'     => Websitetexte::preishinweis($daten['kleinunternehmer']),
            'kleinunternehmer' => $daten['kleinunternehmer'],
        ]));
    }

    /** @return array{auftragslage:?string,kleinunternehmer:bool} */
    private function betriebsdaten(): array
    {
        try {
            $zeile = ($this->betrieb ?? new BetreiberdatenSpeicher())->lesen();
        } catch (\Throwable) {
            // Ohne Datenbank steht die Seite trotzdem. Sie zeigt dann keine Auftragslage —
            // das ist der Zustand „nicht gesetzt", und der ist in §5a vorgesehen.
            $zeile = null;
        }

        $lage = $zeile['auftragslage'] ?? null;

        return [
            'auftragslage'     => is_string($lage) && $lage !== '' ? $lage : null,
            'kleinunternehmer' => (int) ($zeile['kleinunternehmer'] ?? 0) === 1,
        ];
    }
}
