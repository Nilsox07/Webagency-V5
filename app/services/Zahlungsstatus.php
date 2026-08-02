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
