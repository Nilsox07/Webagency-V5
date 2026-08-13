<?php

declare(strict_types=1);

namespace Sartu\Helpers;

/**
 * Das Seitenverhältnis eines Bildes als gekürzter Bruch.
 *
 * ## Warum das eine Hilfsfunktion ist und kein Inline-Stil
 *
 * Ein Bildplatz muss heute den Raum einnehmen, den sein späteres Bild braucht — sonst
 * springt das Layout an dem Tag, an dem die Aufnahme kommt. Der naheliegende Weg wäre
 * `style="aspect-ratio: 1280 / 800"` am Element.
 *
 * **Das geht bei SARTU nicht.** Die eigene Sicherheitsrichtlinie führt `style-src 'self'`
 * ohne `unsafe-inline` (`14_SICHERHEIT.md`); ein Inline-Attribut wird verworfen und wirkt
 * nicht. Am 13.08.2026 hat genau dieser Fehler schon einmal 29 px weissen Rand über der
 * Portalleiste gekostet, und die Ursache war eine Stunde lang nicht zu finden.
 *
 * Der Wert steht deshalb als `data-verhaeltnis="8-5"` am Element, und `website.css` führt je
 * vorkommendem Verhältnis eine Regel. **Kommt ein drittes dazu, fällt es auf:** Der Platz
 * hätte dann kein `aspect-ratio`, und `MarkupTest` meldet es.
 */
final class Bildmasse
{
    /**
     * `1280 × 800` → `8-5`. Der Trenner ist ein Bindestrich, weil der Wert in einem
     * Attributselektor steht und dort ein Doppelpunkt zusätzlich maskiert werden müsste.
     */
    public static function verhaeltnis(int $breite, int $hoehe): string
    {
        if ($breite < 1 || $hoehe < 1) {
            return '';
        }

        $teiler = self::ggt($breite, $hoehe);

        return intdiv($breite, $teiler) . '-' . intdiv($hoehe, $teiler);
    }

    private static function ggt(int $a, int $b): int
    {
        while ($b !== 0) {
            [$a, $b] = [$b, $a % $b];
        }

        return $a;
    }
}
