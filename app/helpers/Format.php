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
