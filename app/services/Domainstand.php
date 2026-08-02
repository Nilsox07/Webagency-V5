<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Die sechs Domainzustände aus Portal-Lastenheft §4 und §8.7.
 *
 * **Eine Liste, drei Verwender:** die Prüfung in `Admin\VorschauSteuerung`, das Auswahlfeld
 * im internen Bereich und der Kundentext auf `/portal/domain`. Vorher stand sie zweimal da,
 * und die zweite wäre bei der siebten Zeile stillschweigend die falsche gewesen.
 *
 * Die Prüfbedingung auf `domain_status.state` kennt dieselben sechs. Sie fängt den erfundenen
 * Wert ab; welcher Text dazugehört, weiss allein diese Klasse.
 *
 * Die Registrar-Anbindung ist Stufe C. Bis dahin setzt der Admin den Stand von Hand — die
 * Zustände sind trotzdem alle sechs vorhanden, weil eine Teiltabelle jetzt eine
 * Folgemigration später bedeutet (REIHENFOLGE.md).
 */
final class Domainstand
{
    /** Systemwert → Kundentext (§3 Regel 12: der Kunde sieht nie einen Systemcode). */
    public const KUNDENTEXTE = [
        'offen'              => 'Wir klären den Stand',
        'vorschlaege_bereit' => 'Wir haben Vorschläge für Sie',
        'bestaetigt'         => 'Die Domain ist bestätigt',
        'registriert'        => 'Die Domain ist registriert',
        'verbunden'          => 'Die Domain ist verbunden',
        'live'               => 'Ihre Website ist unter dieser Domain erreichbar',
    ];

    /** @return list<string> */
    public static function zustaende(): array
    {
        return array_keys(self::KUNDENTEXTE);
    }

    public static function erlaubt(string $zustand): bool
    {
        return isset(self::KUNDENTEXTE[$zustand]);
    }

    public static function kundentext(string $zustand): string
    {
        return self::KUNDENTEXTE[$zustand] ?? 'Wir klären den Stand';
    }
}
