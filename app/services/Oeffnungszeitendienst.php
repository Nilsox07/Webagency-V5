<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenOeffnungszeiten;

/**
 * Die eine Pflegefunktion des Kunden — Portal-Lastenheft §8.7, Testfall 19.
 *
 * ## Zwei Prüfungen, beide serverseitig
 *
 * §8.7 nennt genau zwei Fehlermeldungen, und beide gehören hierher, nicht in die Ansicht:
 *
 * > `Bitte geben Sie für geöffnete Tage eine Von- und eine Bis-Zeit an.`
 * > `Die Bis-Zeit muss nach der Von-Zeit liegen.`
 *
 * Das `type="time"`-Feld im Browser prüft nichts davon — es prüft die *Form*, nicht das
 * Verhältnis der zwei Werte. Und mit abgeschaltetem JavaScript prüft es auch die Form nicht.
 *
 * **Die Grenze liegt nicht im Schema.** Eine Prüfbedingung auf `business_hours` würde die
 * Anweisung abweisen, und der Kunde bekäme einen Datenbankfehler statt eines Satzes
 * (§3 Regel 12). Deshalb steht sie hier.
 *
 * ## Gleich ist nicht „nach"
 *
 * `08:00` bis `08:00` ist keine Öffnungszeit von null Minuten, sondern ein Tippfehler. §8.7
 * sagt „muss **nach** der Von-Zeit liegen" — also `>`, nicht `>=`.
 *
 * ## Alles oder nichts
 *
 * Ein einziger fehlerhafter Tag verwirft den **ganzen** Absendevorgang. Sonst stünden sechs
 * neue Tage und ein alter nebeneinander, und der Kunde sähe nicht, welcher welcher ist.
 * `KundenOeffnungszeiten::ersetzen()` schreibt deshalb in einer Transaktion.
 */
final class Oeffnungszeitendienst
{
    /** §8.7, Wortlaut gebunden. */
    public const HINWEIS_EINGEREICHT = 'Danke — wir prüfen die Änderung und stellen sie auf Ihre '
        . 'Website. Sie bekommen Bescheid, sobald sie live ist.';

    /** §8.7, Wortlaut gebunden — solange etwas offen ist. */
    public const BANNER_OFFEN = 'Eine Änderung wartet auf Veröffentlichung.';

    /** §8.7, Leerzustand vor dem Onlinegang. */
    public const VOR_DEM_START = 'Sobald Ihre Website online ist, können Sie hier Ihre '
        . 'Öffnungszeiten selbst pflegen.';

    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?KundenOeffnungszeiten $speicher = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * Nimmt das Formular entgegen — §8.7.
     *
     * Erwartet `tage[{0..6}][closed|open_time|close_time|note]` und
     * `ausnahmen[][date|closed|open_time|close_time|label]`.
     *
     * @return list<string> leer bei Erfolg
     */
    public function einreichen(array $eingabe): array
    {
        $fehler = [];
        $tage = [];

        foreach (array_keys(KundenOeffnungszeiten::TAGE) as $nummer) {
            $zeile = $eingabe['tage'][$nummer] ?? [];
            $zeile = is_array($zeile) ? $zeile : [];

            $geschlossen = ($zeile['closed'] ?? null) === '1';
            $von = self::zeit($zeile['open_time'] ?? null);
            $bis = self::zeit($zeile['close_time'] ?? null);

            $meldung = self::zeitenPruefen($geschlossen, $von, $bis);

            if ($meldung !== null) {
                $fehler[] = $meldung;
            }

            $tage[] = [
                'weekday'    => $nummer,
                'closed'     => $geschlossen,
                // Ein geschlossener Tag trägt keine Zeiten. Sonst stünden Werte da, die
                // niemand sieht und die bei der nächsten Öffnung wieder auftauchen.
                'open_time'  => $geschlossen ? null : $von,
                'close_time' => $geschlossen ? null : $bis,
                'note'       => self::text($zeile['note'] ?? null),
            ];
        }

        $ausnahmen = [];
        $daten = [];

        foreach (self::zeilen($eingabe['ausnahmen'] ?? null) as $zeile) {
            $datum = self::text($zeile['date'] ?? null);

            if ($datum === null) {
                // Eine leere Zeile ist keine Ausnahme. §8.7 hat den Knopf
                // `Ausnahme hinzufügen` — wer ihn zweimal drückt und eine Zeile leer lässt,
                // bekommt keinen Fehler, sondern keine Zeile.
                continue;
            }

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $datum) !== 1) {
                $fehler[] = 'Bitte geben Sie bei jeder Ausnahme ein Datum an.';

                continue;
            }

            if (isset($daten[$datum])) {
                $fehler[] = 'Zum ' . $datum . ' gibt es zwei Ausnahmen. Bitte tragen Sie je Tag eine ein.';

                continue;
            }

            $daten[$datum] = true;

            $geschlossen = ($zeile['closed'] ?? null) === '1';
            $von = self::zeit($zeile['open_time'] ?? null);
            $bis = self::zeit($zeile['close_time'] ?? null);

            $meldung = self::zeitenPruefen($geschlossen, $von, $bis);

            if ($meldung !== null) {
                $fehler[] = $meldung;
            }

            $ausnahmen[] = [
                'date'       => $datum,
                'closed'     => $geschlossen,
                'open_time'  => $geschlossen ? null : $von,
                'close_time' => $geschlossen ? null : $bis,
                'label'      => self::text($zeile['label'] ?? null) ?? '',
            ];
        }

        if ($fehler !== []) {
            // Dieselbe Meldung kann an mehreren Tagen entstehen. Der Kunde soll sie einmal
            // lesen, nicht siebenmal.
            return array_values(array_unique($fehler));
        }

        $this->speicher()->ersetzen($tage, $ausnahmen);

        return [];
    }

    // ------------------------------------------------------------------ intern

    /**
     * Die zwei Prüfungen aus §8.7 — für einen Wochentag wie für eine Ausnahme dieselben.
     *
     * Sie standen zweimal da. Beim dritten Ort — etwa einer Sonderöffnung — wäre die dritte
     * Fassung die abweichende gewesen.
     *
     * @return string|null die Meldung im Wortlaut, oder `null`, wenn nichts zu melden ist
     */
    private static function zeitenPruefen(bool $geschlossen, ?string $von, ?string $bis): ?string
    {
        if ($geschlossen) {
            return null;
        }

        if ($von === null || $bis === null) {
            return 'Bitte geben Sie für geöffnete Tage eine Von- und eine Bis-Zeit an.';
        }

        // §8.7: „muss **nach** der Von-Zeit liegen" — gleich ist keine Öffnungszeit von null
        // Minuten, sondern ein Tippfehler.
        return $bis <= $von ? 'Die Bis-Zeit muss nach der Von-Zeit liegen.' : null;
    }

    /** @return list<array<string,mixed>> */
    private static function zeilen(mixed $wert): array
    {
        if (!is_array($wert)) {
            return [];
        }

        return array_values(array_filter($wert, static fn (mixed $zeile) => is_array($zeile)));
    }

    /** `HH:MM` oder `HH:MM:SS` — alles andere ist keine Zeit. */
    private static function zeit(mixed $wert): ?string
    {
        if (!is_string($wert)) {
            return null;
        }

        $wert = trim($wert);

        if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)(:[0-5]\d)?$/', $wert) !== 1) {
            return null;
        }

        return substr($wert, 0, 5);
    }

    private static function text(mixed $wert): ?string
    {
        if (!is_string($wert)) {
            return null;
        }

        $wert = trim($wert);

        return $wert === '' ? null : $wert;
    }

    private function speicher(): KundenOeffnungszeiten
    {
        return $this->speicher ?? new KundenOeffnungszeiten($this->bereich, $this->pdo);
    }
}
