<?php

declare(strict_types=1);

namespace Sartu\Helpers;

/**
 * Der eine Ort, an dem `/storage` aufgelöst wird — Portal-Lastenheft §1.3.
 *
 * Vorher stand dieselbe Auflösung an fünf Stellen, und **eine davon war schon
 * auseinandergelaufen**: Die Umgebungsprüfung der Ersteinrichtung fiel auf das
 * Projektverzeichnis zurück, die Ratenbegrenzung auf einen anderen Pfad. Setup-Schritt 1
 * prüfte damit die Schreibrechte auf einem Verzeichnis, in das später niemand schrieb.
 *
 * Genau so entstehen Fehler, die niemand sieht: Beide Zweige funktionieren für sich.
 */
final class Speicher
{
    public static function verzeichnis(?string $ueberschreibung = null): string
    {
        if ($ueberschreibung !== null && $ueberschreibung !== '') {
            return rtrim($ueberschreibung, '/');
        }

        $aus = Env::get('STORAGE_DIR');

        if ($aus !== null && $aus !== '') {
            return rtrim($aus, '/');
        }

        return dirname(__DIR__, 2) . '/storage';
    }

    /** Legt ein Verzeichnis an, falls es fehlt. Die zweite Prüfung fängt das Wettrennen ab. */
    public static function sicherstellen(string $verzeichnis): void
    {
        if (is_dir($verzeichnis)) {
            return;
        }

        if (!mkdir($verzeichnis, 0770, true) && !is_dir($verzeichnis)) {
            throw new \RuntimeException(sprintf('Das Verzeichnis %s liess sich nicht anlegen.', $verzeichnis));
        }
    }
}
