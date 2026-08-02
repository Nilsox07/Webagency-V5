<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Format;

/**
 * Welcher Zustand aus welchem Zahlbetrag folgt — Portal-Lastenheft §4 und §5.3.
 *
 * ## Die vier Regeln, wörtlich
 *
 * > - `paid_cents = 0` → Status bleibt `gesendet` oder `ueberfaellig`
 * > - `0 < paid_cents < gross_cents` → Status **`teilweise_bezahlt`**
 * > - `paid_cents >= gross_cents` → Status `bezahlt`, `paid_at` wird gesetzt
 * > - **Überzahlung wird nicht abgewiesen**, sondern gespeichert
 *
 * ## Der Fall, der beim Abkürzen als Erstes verlorengeht
 *
 * §5.3: „**`teilweise_bezahlt` und `ueberfaellig` schließen sich nicht aus.** Eine
 * angezahlte Rechnung nach Fälligkeit ist **beides**." Eine Spalte kann aber nur einen Wert
 * halten.
 *
 * Aufgelöst wie §5.3 es vorgibt: `ueberfaellig` gewinnt als **Zustand**, weil die Frist die
 * dringendere Aussage ist — und der Anzeigetext trägt beides:
 * `Überfällig seit {Datum} — offen: {Restbetrag}`. Der Satz danach begründet es selbst:
 * „Maßgeblich für die Erinnerung ist der Restbetrag, nicht der Status."
 *
 * Deshalb rechnet der Erinnerungslauf mit `restbetrag()` und nicht mit dem Zustand.
 *
 * ## Was hier NICHT passiert
 *
 * **Der Zustand wird nie aus einer Rückkehr-URL abgeleitet** (§12, eiserne Regel). Diese
 * Klasse bekommt einen Betrag, den ein Mensch eingetragen hat, nachdem er den Eingang im
 * Zahlungsdienst geprüft hat. Es gibt keinen Aufrufer aus einem Browserrücklauf, und es
 * gibt keine Methode, die einen Zustand ohne Betrag setzt.
 */
final class Zahlungsstatus
{
    public const ENTWURF           = 'entwurf';
    public const GESENDET          = 'gesendet';
    public const TEILWEISE_BEZAHLT = 'teilweise_bezahlt';
    public const BEZAHLT           = 'bezahlt';
    public const UEBERFAELLIG      = 'ueberfaellig';
    public const STORNIERT         = 'storniert';

    /**
     * Der Zustand, der zu einem Zahlbetrag gehört.
     *
     * @param bool $ueberfaellig ob `due_date` überschritten ist
     */
    public static function ausBetrag(int $bezahltCent, int $bruttoCent, bool $ueberfaellig): string
    {
        if ($bezahltCent >= $bruttoCent) {
            return self::BEZAHLT;
        }

        // Vor `teilweise_bezahlt`: Die Frist ist die dringendere Aussage, und der Rest steht
        // im Anzeigetext (§5.3).
        if ($ueberfaellig) {
            return self::UEBERFAELLIG;
        }

        return $bezahltCent > 0 ? self::TEILWEISE_BEZAHLT : self::GESENDET;
    }

    /** Was noch offen ist. Nie negativ — eine Überzahlung ist kein Guthaben (§4). */
    public static function restbetrag(int $bezahltCent, int $bruttoCent): int
    {
        return max(0, $bruttoCent - $bezahltCent);
    }

    public static function ueberzahlung(int $bezahltCent, int $bruttoCent): int
    {
        return max(0, $bezahltCent - $bruttoCent);
    }

    /**
     * Der Kundentext — §5.3, Wortlaut gebunden.
     *
     * Der Kunde sieht nie einen Systemcode (§3 Regel 12). `teilweise_bezahlt` ist ein
     * Feldwert, kein Text für Menschen.
     *
     * @param array<string,mixed> $rechnung
     */
    public static function kundentext(array $rechnung): string
    {
        $zustand = (string) ($rechnung['status'] ?? '');
        $bezahlt = (int) ($rechnung['paid_cents'] ?? 0);
        $brutto = (int) ($rechnung['gross_cents'] ?? 0);
        $rest = self::restbetrag($bezahlt, $brutto);

        return match ($zustand) {
            self::BEZAHLT => 'Bezahlt am ' . Format::datum(self::wert($rechnung, 'paid_at')),
            self::UEBERFAELLIG => $bezahlt > 0
                // §5.3: „Angezeigt wird dann `Überfällig seit {Datum} — offen: {Restbetrag}`."
                ? 'Überfällig seit ' . Format::datum(self::wert($rechnung, 'due_date'))
                    . ' — offen: ' . Format::euro($rest)
                : 'Überfällig seit ' . Format::datum(self::wert($rechnung, 'due_date')),
            self::TEILWEISE_BEZAHLT => 'Teilweise bezahlt — offen: ' . Format::euro($rest),
            self::STORNIERT         => 'Storniert',
            default                 => 'Offen — zahlbar bis ' . Format::datum(self::wert($rechnung, 'due_date')),
        };
    }

    /**
     * Die Schwelle, ab der eine Frist „knapp" ist — **drei Tage**.
     *
     * §8.1 nennt den Hinweis, ohne die Zahl. Sie stand deshalb bis zum 02.08.2026 als
     * offener Punkt in `OFFENE_ENTSCHEIDUNGEN.md`; der Betreiber hat sie entschieden.
     *
     * **Drei Tage, weil §10 dieselbe Zahl schon kennt:** „Angebot läuft in 3 Tagen ab."
     * Eine zweite Frist daneben wäre eine Zahl ohne Grund — und zwei Vorwarnzeiten im
     * selben Bereich lernt niemand.
     */
    public const KNAPP_TAGE = 3;

    /**
     * Ist die Zahlungsfrist knapp?
     *
     * **Nur bei offenen Rechnungen.** Eine bezahlte hat keine Frist mehr, eine überfällige
     * ist nicht knapp, sondern vorbei — dort steht bereits der schärfere Text.
     *
     * `heute` kommt als Parameter, nicht aus der Uhr: Ein Fristvergleich, der sich nicht
     * stellen lässt, ist ein Fristvergleich, den kein Test prüfen kann.
     *
     * @param array<string,mixed> $rechnung
     */
    public static function fristKnapp(array $rechnung, ?string $heute = null): bool
    {
        $zustand = (string) ($rechnung['status'] ?? '');

        if (!in_array($zustand, [self::GESENDET, self::TEILWEISE_BEZAHLT], true)) {
            return false;
        }

        $faellig = self::wert($rechnung, 'due_date');

        if ($faellig === null) {
            return false;
        }

        $heute ??= Format::heute();
        $grenze = (new \DateTimeImmutable($heute))
            ->modify('+' . self::KNAPP_TAGE . ' days')
            ->format('Y-m-d');

        // Bereits überfällig ist nicht knapp — das sagt der Zustand, nicht dieser Hinweis.
        return $faellig >= $heute && $faellig <= $grenze;
    }

    /**
     * §8.1, der Hinweis bei knapper Frist. Wortlaut hier, nicht in der Ansicht.
     *
     * **Ohne Datum, mit Absicht.** Der Hinweis steht in Block 3 hinter der gebundenen Zeile
     * `Rechnung {Nummer} — zahlbar bis {Datum}`. Das Datum ein zweites Mal danebenzusetzen
     * hiesse, den Leser zu fragen, ob es dieselbe Frist ist.
     */
    public static function knapphinweis(): string
    {
        return 'Diese Frist ist in wenigen Tagen erreicht.';
    }

    /** §4a: 19 % Umsatzsteuer, an einer Stelle. */
    public static function umsatzsteuer(int $nettoCent, bool $kleinunternehmer = false): int
    {
        // §19 UStG: Steht `kleinunternehmer` auf `ja`, wird keine Umsatzsteuer ausgewiesen.
        // Der Wert kommt aus `operator_settings` und ist keine Bauentscheidung.
        return $kleinunternehmer ? 0 : (int) round($nettoCent * Preise::UST_PROZENT / 100);
    }

    private static function wert(array $zeile, string $feld): ?string
    {
        $wert = $zeile[$feld] ?? null;

        return is_string($wert) && $wert !== '' ? $wert : null;
    }
}
