<?php

declare(strict_types=1);

namespace Sartu\Helpers;

/**
 * Zugriff auf die Konfiguration.
 *
 * Zwei Quellen, bewusst getrennt:
 *
 *   server()  liest ausschliesslich die Serverumgebung. Portal-Lastenheft §1.5 verlangt das
 *             fuer APP_ENV — die HTTP-Ausnahme der Ersteinrichtung darf sich nie auf eine
 *             Datei stuetzen, die es zu diesem Zeitpunkt noch gar nicht gibt.
 *   get()     liest die Serverumgebung und faellt auf die .env zurueck.
 *
 * Es gibt keinen Weg, einen Wert aus einer Anfrage zu setzen.
 */
final class Env
{
    /** @var array<string,string>|null */
    private static ?array $file = null;

    private static ?string $path = null;

    public static function bootstrap(string $envPath): void
    {
        self::$path = $envPath;
        self::$file = null;
    }

    /** Nur die Serverumgebung. Fehlt der Wert, kommt null — kein Rueckfall auf die Datei. */
    public static function server(string $key): ?string
    {
        $value = getenv($key);

        return ($value === false || $value === '') ? null : $value;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = self::server($key);
        if ($value !== null) {
            return $value;
        }

        $file = self::file();

        return $file[$key] ?? $default;
    }

    /** Wie get(), bricht aber ab statt still einen Vorgabewert zu benutzen. */
    public static function require(string $key): string
    {
        $value = self::get($key);
        if ($value === null || $value === '') {
            throw new \RuntimeException(sprintf('Konfigurationswert %s fehlt.', $key));
        }

        return $value;
    }

    /**
     * Produktiv ist die Vorgabe. Portal-Lastenheft §1.5: Fehlt APP_ENV in der Serverumgebung,
     * gilt produktiv — also HTTPS-Zwang. Nie umgekehrt.
     */
    public static function appEnv(): string
    {
        $value = self::server('APP_ENV');

        return in_array($value, ['local', 'staging', 'production'], true) ? $value : 'production';
    }

    public static function isLocal(): bool
    {
        return self::appEnv() === 'local';
    }

    public static function envPath(): string
    {
        return self::$path ?? dirname(__DIR__, 2) . '/.env';
    }

    public static function fileExists(): bool
    {
        return is_file(self::envPath());
    }

    /**
     * Schreibt Werte in die .env, ohne vorhandene Zeilen und Kommentare zu verlieren.
     * Die Ersteinrichtung legt die Datei damit schrittweise an (§1.5 Schritte 2, 3, 5).
     *
     * @param array<string,string> $values
     */
    public static function write(array $values): void
    {
        $path = self::envPath();
        $lines = is_file($path) ? file($path, FILE_IGNORE_NEW_LINES) : [];

        foreach ($values as $key => $value) {
            $line = $key . '=' . self::quote($value);
            $found = false;

            foreach ($lines as $index => $existing) {
                if (preg_match('/^' . preg_quote($key, '/') . '=/', $existing) === 1) {
                    $lines[$index] = $line;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $lines[] = $line;
            }
        }

        $written = file_put_contents($path, implode("\n", $lines) . "\n", LOCK_EX);
        if ($written === false) {
            throw new \RuntimeException('Die Datei .env konnte nicht geschrieben werden.');
        }

        chmod($path, 0600);
        self::$file = null;
    }

    private static function quote(string $value): string
    {
        if ($value === '' || preg_match('/^[A-Za-z0-9_\/.:@+-]+$/', $value) === 1) {
            return $value;
        }

        return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $value) . '"';
    }

    /** @return array<string,string> */
    private static function file(): array
    {
        if (self::$file !== null) {
            return self::$file;
        }

        $path = self::envPath();
        if (!is_file($path)) {
            return self::$file = [];
        }

        $parsed = \Dotenv\Dotenv::parse((string) file_get_contents($path));

        return self::$file = array_map(static fn ($v) => (string) $v, array_filter($parsed, static fn ($v) => $v !== null));
    }
}
