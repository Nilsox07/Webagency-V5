<?php

declare(strict_types=1);

namespace Sartu\Helpers;

/**
 * Angaben zur laufenden Anfrage.
 *
 * Portal-Lastenheft §1.5: Weiterleitungs-Kopfzeilen sind frei setzbar und gelten deshalb
 * NICHT als Nachweis, solange keine Liste vertrauenswuerdiger Zwischenstellen konfiguriert
 * ist. Diese Liste gibt es in Stufe 0 nicht — also werden die Kopfzeilen ignoriert.
 */
final class Http
{
    public static function methode(): string
    {
        return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    }

    public static function pfad(): string
    {
        $ziel = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $pfad = parse_url($ziel, PHP_URL_PATH);
        $pfad = is_string($pfad) ? $pfad : '/';
        $pfad = rawurldecode($pfad);

        if ($pfad !== '/' && str_ends_with($pfad, '/')) {
            $pfad = rtrim($pfad, '/');
        }

        return $pfad === '' ? '/' : $pfad;
    }

    /** Ausschliesslich REMOTE_ADDR. X-Forwarded-For wird nicht gelesen. */
    public static function gegenstelle(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    }

    public static function istLoopback(): bool
    {
        return in_array(self::gegenstelle(), ['127.0.0.1', '::1'], true);
    }

    /** X-Forwarded-Proto wird bewusst nicht ausgewertet (§1.5). */
    public static function istHttps(): bool
    {
        $https = $_SERVER['HTTPS'] ?? '';

        return is_string($https) && $https !== '' && strtolower($https) !== 'off';
    }

    /** Vollstaendiger Vergleich, kein Teilstring: localhost.angreifer.de ist nicht localhost. */
    public static function hostname(): string
    {
        $host = (string) ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '');
        $host = strtolower(trim($host));

        if (str_starts_with($host, '[')) {
            $ende = strpos($host, ']');
            return $ende === false ? $host : substr($host, 0, $ende + 1);
        }

        $doppelpunkt = strrpos($host, ':');

        return $doppelpunkt === false ? $host : substr($host, 0, $doppelpunkt);
    }

    public static function istLokalerHostname(): bool
    {
        return in_array(self::hostname(), ['localhost', '127.0.0.1', '[::1]'], true);
    }

    public static function benutzerkennung(): string
    {
        return mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
    }

    public static function eingabe(string $feld): ?string
    {
        $wert = $_POST[$feld] ?? null;

        return is_string($wert) ? $wert : null;
    }

    public static function getrimmteEingabe(string $feld): string
    {
        return trim(self::eingabe($feld) ?? '');
    }

    public static function weiter(string $ziel, int $status = 302): never
    {
        header('Location: ' . $ziel, true, $status);
        exit;
    }
}
