<?php

declare(strict_types=1);

namespace Sartu\Helpers;

/**
 * Serverseitige Pruefungen.
 *
 * Portal-Lastenheft §4 zu operator_settings: NOT NULL erlaubt eine leere Zeichenkette.
 * Fuer jedes Pflichtfeld gilt deshalb zusaetzlich: nach trim() mindestens ein Zeichen.
 * Diese Regel steht hier an einer Stelle, damit die Startsperre nach derselben prueft.
 */
final class Validate
{
    public static function gefuellt(?string $wert): bool
    {
        return $wert !== null && trim($wert) !== '';
    }

    public static function email(?string $wert): bool
    {
        return self::gefuellt($wert) && filter_var(trim((string) $wert), FILTER_VALIDATE_EMAIL) !== false;
    }

    /** §4: fuenf Ziffern bei land = 'DE'. */
    public static function plz(?string $wert, string $land): bool
    {
        if (!self::gefuellt($wert)) {
            return false;
        }

        $wert = trim((string) $wert);

        if (strtoupper($land) === 'DE') {
            return preg_match('/^\d{5}$/', $wert) === 1;
        }

        return mb_strlen($wert) <= 10;
    }

    /** ISO 3166-1 alpha-2: zwei Grossbuchstaben. */
    public static function land(?string $wert): bool
    {
        return self::gefuellt($wert) && preg_match('/^[A-Z]{2}$/', trim((string) $wert)) === 1;
    }

    /** §4: Landespraefix und Ziffernfolge. Keine Abfrage beim Bundeszentralamt. */
    public static function ustId(?string $wert): bool
    {
        if (!self::gefuellt($wert)) {
            return false;
        }

        $wert = strtoupper(str_replace(' ', '', trim((string) $wert)));

        return preg_match('/^[A-Z]{2}[0-9A-Z]{2,13}$/', $wert) === 1;
    }

    public static function steuernummer(?string $wert): bool
    {
        if (!self::gefuellt($wert)) {
            return false;
        }

        return preg_match('/^[0-9\/ .-]{5,30}$/', trim((string) $wert)) === 1;
    }

    /** §4: Pruefziffer nach ISO 7064 rechnen, nicht nur die Laenge zaehlen. */
    public static function iban(?string $wert): bool
    {
        if (!self::gefuellt($wert)) {
            return false;
        }

        $iban = strtoupper(preg_replace('/\s+/', '', trim((string) $wert)) ?? '');

        if (preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{10,30}$/', $iban) !== 1) {
            return false;
        }

        $umgestellt = substr($iban, 4) . substr($iban, 0, 4);
        $ziffern = '';
        foreach (str_split($umgestellt) as $zeichen) {
            $ziffern .= ctype_digit($zeichen) ? $zeichen : (string) (ord($zeichen) - 55);
        }

        $rest = 0;
        foreach (str_split($ziffern, 7) as $block) {
            $rest = (int) (((string) $rest . $block) % 97);
        }

        return $rest === 1;
    }

    /** §1.5 Schritt 7: mindestens 12 Zeichen. */
    public static function passwort(?string $wert): bool
    {
        return $wert !== null && mb_strlen($wert) >= 12;
    }
}
