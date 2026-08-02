<?php

declare(strict_types=1);

namespace Sartu;

/**
 * Der Ansichtszusammenbau — Portal-Lastenheft §1.3: „Eine Seite besteht aus Layout +
 * Partials + Komponenten. Kein Kopieren von Markup zwischen Seiten."
 *
 * Fachlogik gehoert nicht hierher und Datenbankzugriff schon gar nicht. Eine Ansicht
 * bekommt fertige Werte und gibt sie aus.
 */
final class Ansicht
{
    /** @param array<string,mixed> $daten */
    public static function seite(string $layout, string $seite, array $daten = []): string
    {
        $inhalt = self::teil('pages/' . $seite, $daten);

        return self::teil('layouts/' . $layout, $daten + ['inhalt' => $inhalt]);
    }

    /** @param array<string,mixed> $daten */
    public static function teil(string $name, array $daten = []): string
    {
        $pfad = __DIR__ . '/views/' . $name . '.php';

        if (!is_file($pfad)) {
            throw new \RuntimeException(sprintf('Die Ansicht %s gibt es nicht.', $name));
        }

        extract($daten, EXTR_SKIP);

        $ebene = ob_get_level();
        ob_start();

        try {
            require $pfad;
        } catch (\Throwable $fehler) {
            // Ohne dieses Aufraeumen bleibt bei einem Fehler in der Ansicht ein offener
            // Ausgabepuffer stehen. Der naechste Aufruf schreibt dann in ihn hinein.
            while (ob_get_level() > $ebene) {
                ob_end_clean();
            }

            throw $fehler;
        }

        return (string) ob_get_clean();
    }
}
