<?php

declare(strict_types=1);

namespace Sartu\Helpers;

/**
 * Anzeigeformate nach Portal-Lastenheft §4a.
 *
 * Gespeichert wird in UTC (§4.0), angezeigt in Europe/Berlin. Die Umrechnung geschieht
 * ausschliesslich hier — nicht in der Datenbank und nicht in einer Ansicht.
 */
final class Format
{
    public const ANZEIGE_ZEITZONE = 'Europe/Berlin';

    /** §4a: nie null, „–" oder „undefined" anzeigen. */
    public const LEER = 'Noch nicht hinterlegt';

    public static function datum(?string $utc): string
    {
        $lokal = self::lokal($utc);

        return $lokal === null ? self::LEER : $lokal->format('d.m.Y');
    }

    public static function datumZeit(?string $utc): string
    {
        $lokal = self::lokal($utc);

        return $lokal === null ? self::LEER : $lokal->format('d.m.Y, H:i') . ' Uhr';
    }

    /** §4a: Speicherung als integer in Cent, Anzeige deutsch mit Leerzeichen vor dem Zeichen. */
    public static function euro(?int $cent): string
    {
        if ($cent === null) {
            return self::LEER;
        }

        return number_format($cent / 100, 2, ',', '.') . ' €';
    }

    /**
     * Der heutige Tag in `Y-m-d` — **in Anzeigezeit**, nicht in UTC.
     *
     * Fristen sind Kalendertage, keine Zeitpunkte: Eine Rechnung, die am 3. fällig ist, ist
     * am 3. deutscher Zeit fällig. Zwischen 00:00 und 02:00 MESZ steht in UTC noch der
     * Vortag — ohne diese Zeile wäre jede nachts angelegte Frist einen Tag zu früh.
     *
     * Sie stand vorher an fünf Stellen ausgeschrieben. Die fünfte hätte irgendwann UTC
     * genommen, und niemand hätte es gemerkt.
     */
    public static function heute(): string
    {
        return self::inTagen(0);
    }

    /** Ein Kalendertag in der Zukunft, gerechnet ab heute in Anzeigezeit. */
    public static function inTagen(int $tage): string
    {
        $tag = new \DateTimeImmutable('now', new \DateTimeZone(self::ANZEIGE_ZEITZONE));

        return ($tage === 0 ? $tag : $tag->modify('+' . $tage . ' days'))->format('Y-m-d');
    }

    public static function text(?string $wert): string
    {
        $wert = $wert === null ? '' : trim($wert);

        return $wert === '' ? self::LEER : $wert;
    }

    private static function lokal(?string $utc): ?\DateTimeImmutable
    {
        if ($utc === null || trim($utc) === '') {
            return null;
        }

        try {
            $zeit = new \DateTimeImmutable($utc, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }

        return $zeit->setTimezone(new \DateTimeZone(self::ANZEIGE_ZEITZONE));
    }
}
