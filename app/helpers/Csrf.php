<?php

declare(strict_types=1);

namespace Sartu\Helpers;

/**
 * Portal-Lastenheft §3 Regel 3: CSRF-Token bei jedem POST. Kein Token, keine Ausnahme.
 *
 * Das Token haengt an der PHP-Sitzung. Es gibt bewusst keinen Weg, die Pruefung fuer eine
 * einzelne Route abzuschalten — die Pruefung sitzt zentral im Dispatcher.
 */
final class Csrf
{
    private const SCHLUESSEL = '_csrf';

    public const FELD = '_token';

    public static function token(): string
    {
        // Gepruft wird auf $_SESSION, nicht auf session_status(): Im Testlauf ueber die
        // Befehlszeile gibt es keine PHP-Sitzung, wohl aber denselben Sitzungsspeicher.
        // Ohne diesen Speicher gaebe es kein Token — und genau das faengt die Ausnahme ab.
        if (!isset($_SESSION) || !is_array($_SESSION)) {
            throw new \RuntimeException('CSRF-Token ohne Sitzungsspeicher angefordert.');
        }

        if (!isset($_SESSION[self::SCHLUESSEL]) || !is_string($_SESSION[self::SCHLUESSEL])) {
            $_SESSION[self::SCHLUESSEL] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SCHLUESSEL];
    }

    public static function feld(): string
    {
        return '<input type="hidden" name="' . self::FELD . '" value="' . Html::e(self::token()) . '">';
    }

    public static function pruefen(?string $eingereicht): bool
    {
        if ($eingereicht === null || $eingereicht === '') {
            return false;
        }

        if (!isset($_SESSION) || !is_array($_SESSION)) {
            return false;
        }

        $erwartet = $_SESSION[self::SCHLUESSEL] ?? null;
        if (!is_string($erwartet) || $erwartet === '') {
            return false;
        }

        return hash_equals($erwartet, $eingereicht);
    }
}
