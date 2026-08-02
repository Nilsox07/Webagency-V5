<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Validate;

/**
 * Die Abwehr, die vor **jedem** öffentlichen Formular steht — Portal-Lastenheft §4b.
 *
 * ## Warum das eine eigene Klasse ist
 *
 * Website-Lastenheft §17 verlangt für **beide** Formulare — Bedarfsscheck und Rückfrage —
 * dieselben Nachweise: Honigtopf greift, Zeitregel greift, dieselbe `submission_id` erzeugt
 * keinen zweiten Datensatz, Daten über 64 KB werden abgewiesen.
 *
 * Stünden diese vier Prüfungen zweimal da, wäre die zweite Fassung irgendwann die
 * schwächere — und die schwächere ist immer die, die niemand ansieht.
 *
 * ## Die Reihenfolge ist Teil der Regel
 *
 * | # | Prüfung | Warum sie vor der nächsten steht |
 * |---|---|---|
 * | 1 | Größe | Was zu groß ist, wird nicht erst zerlegt |
 * | 2 | Ratenbegrenzung | Die teuren Prüfungen laufen nicht für den, der schon zu oft da war |
 * | 3 | Honigtopf und Zeitregel | **Still.** Der Absender sieht die Danke-Seite, nicht den Grund |
 * | 4 | Doppeleinreichung | Deckt Doppelklick, Neuladen und die Zurück-Taste ab |
 * | 5 | Pflichtfelder | Erst ab hier sieht ein Mensch einen Fehler — am Feld |
 *
 * **Schritt 3 und 4 sind still.** Wer erfährt, dass sein Honigtopffeld aufgefallen ist,
 * lässt es beim nächsten Mal leer.
 */
final class Formularschutz
{
    /** §4b.3: unter drei Sekunden ist kein Mensch. */
    public const MINDESTDAUER_SEKUNDEN = 3;

    /** §4b.1: Formulardaten über 64 KB werden abgewiesen (Testfall 36). */
    public const MAX_BYTES = 65536;

    public const FENSTER_SEKUNDEN = 3600;

    /**
     * Gemessen wird die JSON-Darstellung, nicht die Summe der Feldlängen.
     *
     * Der Unterschied ist klein und trotzdem der richtige: §4b.1 spricht von
     * „Formulardaten über 64 KB". Ein Formular mit tausend leeren Feldern trägt kaum
     * Zeichen und ist trotzdem zu groß — die Struktur zählt mit.
     */
    public static function zuGross(array $eingabe): bool
    {
        return strlen((string) json_encode($eingabe, JSON_UNESCAPED_UNICODE)) > self::MAX_BYTES;
    }

    /** Ein gefülltes Honigtopffeld — still verwerfen, nicht melden. */
    public static function honigtopfGefuellt(array $eingabe, string $feld = 'hp_website'): bool
    {
        $wert = $eingabe[$feld] ?? null;

        return is_string($wert) && Validate::gefuellt($wert);
    }

    /**
     * §4b.3.
     *
     * @param string $begonnenAm Zeitstempel als Sekunden, aus dem versteckten Feld
     */
    public static function zeitregelErfuellt(string $begonnenAm): bool
    {
        if ($begonnenAm === '') {
            return false;
        }

        $start = (int) $begonnenAm;

        if ($start <= 0) {
            return false;
        }

        return (time() - $start) >= self::MINDESTDAUER_SEKUNDEN;
    }

    public static function istUuid(string $wert): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $wert) === 1;
    }
}
