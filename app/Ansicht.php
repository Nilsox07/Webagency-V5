<?php

declare(strict_types=1);

namespace Sartu;

/**
 * Der Ansichtszusammenbau — Portal-Lastenheft §1.3: „Eine Seite besteht aus Layout +
 * Partials + Komponenten. Kein Kopieren von Markup zwischen Seiten."
 *
 * Fachlogik gehoert nicht hierher und Datenbankzugriff schon gar nicht. Eine Ansicht
 * bekommt fertige Werte und gibt sie aus.
 *
 * Die Parameter heissen absichtlich `$__ansicht` und `$__werte`.
 *
 * Der Grund ist eine Falle, in die dieser Code schon einmal gelaufen ist: `extract()` mit
 * EXTR_SKIP ueberschreibt vorhandene Variablen NICHT. Hiess der Parameter `$name`, gewann
 * er gegen den Wert `name` aus den uebergebenen Daten — und jedes Formularfeld hiess danach
 * `components/feld` statt `email`. Die Seite sah dabei vollkommen richtig aus, und kein Test
 * hat es bemerkt; aufgefallen ist es erst beim Aufruf im Browser.
 *
 * Zwei Unterstriche und ein Praefix, das in keiner Ansicht vorkommt, schliessen die Kollision
 * aus. `MarkupTest` prueft zusaetzlich die Feldnamen im gerenderten Formular.
 */
final class Ansicht
{
    /** @param array<string,mixed> $werte */
    public static function seite(string $layout, string $seite, array $werte = []): string
    {
        $inhalt = self::teil('pages/' . $seite, $werte);

        return self::teil('layouts/' . $layout, $werte + ['inhalt' => $inhalt]);
    }

    /** @param array<string,mixed> $__werte */
    public static function teil(string $__ansicht, array $__werte = []): string
    {
        $__pfad = __DIR__ . '/views/' . $__ansicht . '.php';

        if (!is_file($__pfad)) {
            throw new \RuntimeException(sprintf('Die Ansicht %s gibt es nicht.', $__ansicht));
        }

        extract($__werte, EXTR_SKIP);

        $__ebene = ob_get_level();
        ob_start();

        try {
            require $__pfad;
        } catch (\Throwable $__fehler) {
            // Ohne dieses Aufraeumen bleibt bei einem Fehler in der Ansicht ein offener
            // Ausgabepuffer stehen. Der naechste Aufruf schreibt dann in ihn hinein.
            while (ob_get_level() > $__ebene) {
                ob_end_clean();
            }

            throw $__fehler;
        }

        return (string) ob_get_clean();
    }
}
