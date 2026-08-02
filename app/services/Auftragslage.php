<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Die Statusanzeige der Auftragslage — Website-Lastenheft §5a.
 *
 * ## Vier Zustände, und der vierte zeigt nichts
 *
 * §5a führt „nicht gesetzt" als eigene Zeile: **nichts wird angezeigt.** Deshalb gibt es
 * hier keinen Vorgabewert. Eine Anzeige „Freie Kapazitäten", die niemand gesetzt hat, ist
 * eine Aussage über den Betrieb, die niemand getroffen hat.
 *
 * ## Der Zustand ändert bei `ausgebucht` die Handlung
 *
 * Nur dort. Bei `offen` und `knapp` bleibt der Knopf `Bedarf prüfen lassen`; bei
 * `ausgebucht` heißt er `Auf die Warteliste`, und die Zeile steht **über** dem Knopf statt
 * darunter. §5a: Eine Anfrage wäre dann eine Sackgasse.
 *
 * ## Nie allein über Farbe
 *
 * Jeder Zustand hat eine eigene Füllung des Punktes **und** einen eigenen Text. Ein Zustand,
 * den man nur an der Farbe erkennt, ist für einen Teil der Leser kein Zustand (§2a).
 *
 * ## Was hier bewusst fehlt
 *
 * Keine Zahl, kein Termin, kein Pulsieren. §5a nennt alle drei einzeln: „3 Plätze frei" wäre
 * eine ungeprüfte Zusage, und ein pulsierender Punkt behauptet Echtzeitüberwachung für einen
 * Wert, der sich vielleicht monatlich ändert.
 */
final class Auftragslage
{
    public const OFFEN      = 'offen';
    public const KNAPP      = 'knapp';
    public const AUSGEBUCHT = 'ausgebucht';

    /** Die Knopfbeschriftung im Regelfall — gebundener Wortlaut. */
    public const KNOPF = 'Bedarf prüfen lassen';

    /** Nur bei `ausgebucht` — §5a. */
    public const KNOPF_WARTELISTE = 'Auf die Warteliste';

    /**
     * @return array<string,array{text:string,fuellung:string,gewicht:string,knopf:string}>
     */
    public static function zustaende(): array
    {
        return [
            self::OFFEN => [
                'text'     => 'Freie Kapazitäten',
                'fuellung' => 'voll',
                'gewicht'  => 'leise',
                'knopf'    => self::KNOPF,
            ],
            self::KNAPP => [
                'text'     => 'Nur noch wenige Plätze',
                'fuellung' => 'halb',
                'gewicht'  => 'leise',
                'knopf'    => self::KNOPF,
            ],
            self::AUSGEBUCHT => [
                'text'     => 'Zurzeit ausgebucht — Warteliste möglich',
                'fuellung' => 'ring',
                'gewicht'  => 'betont',
                'knopf'    => self::KNOPF_WARTELISTE,
            ],
        ];
    }

    /** @return array<string,string>|null `null` heißt: nichts anzeigen. */
    public static function anzeige(?string $wert): ?array
    {
        if ($wert === null || $wert === '') {
            return null;
        }

        return self::zustaende()[$wert] ?? null;
    }

    /** Die Beschriftung des Hauptknopfs — auch dann richtig, wenn nichts gesetzt ist. */
    public static function knopf(?string $wert): string
    {
        return self::anzeige($wert)['knopf'] ?? self::KNOPF;
    }
}
