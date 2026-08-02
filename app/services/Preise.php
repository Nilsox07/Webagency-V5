<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Format;

/**
 * Die Preistabelle — Masterkonzept, Abschnitt „Pakete".
 *
 * `CLAUDE.md`: „die Preistabelle ist die Quelle jeder Zahl". Sie steht deshalb an **einer**
 * Stelle. Wer einen Preis auf einer Seite schreibt, schreibt ihn irgendwann falsch; wer ihn
 * hier holt, kann es nicht.
 *
 * **Gespeichert wird in Cent** (§4a). `first_year_net_cents` ist keine zweite Wahrheit,
 * sondern gerechnet: Einmalpreis + zwölf Monatsbeträge. `migrations/012_offers.sql` erzwingt
 * dieselbe Gleichung als Prüfbedingung — die Zahlen aus dem Masterkonzept gehen genau auf.
 *
 * **Sonderprojekt ist ein Startwert, kein Preis.** Es steht „ab 12.500 €". Deshalb liefert
 * `zeile()` dort `ab_preis = true`, und jede Anzeige schreibt „ab" davor.
 */
final class Preise
{
    /** §4a: 19 % Umsatzsteuer an einer Stelle, nicht in jeder Rechnung neu. */
    public const UST_PROZENT = 19;

    /**
     * @return array<string,array{
     *     name:string,
     *     einmalig_cent:int,
     *     schutz:string,
     *     monatlich_cent:int,
     *     erstes_jahr_cent:int,
     *     seiten:string,
     *     korrekturrunden:int,
     *     ab_preis:bool
     * }>
     */
    public static function tabelle(): array
    {
        return [
            'start' => [
                'name'             => 'Start',
                'einmalig_cent'    => 149000,
                'schutz'           => 'Schutz S',
                'monatlich_cent'   => 5900,
                'erstes_jahr_cent' => 219800,
                'seiten'           => '1 Seite, rund 1.200 Wörter',
                'korrekturrunden'  => 1,
                'ab_preis'         => false,
            ],
            'wachstum' => [
                'name'             => 'Wachstum',
                'einmalig_cent'    => 390000,
                'schutz'           => 'Schutz M',
                'monatlich_cent'   => 12900,
                'erstes_jahr_cent' => 544800,
                'seiten'           => 'bis zu 8 Seiten, rund 3.500 Wörter',
                'korrekturrunden'  => 2,
                'ab_preis'         => false,
            ],
            'platzhirsch' => [
                'name'             => 'Platzhirsch',
                'einmalig_cent'    => 790000,
                'schutz'           => 'Schutz L',
                'monatlich_cent'   => 24900,
                'erstes_jahr_cent' => 1088800,
                'seiten'           => 'bis zu 16 Seiten, rund 6.500 Wörter',
                'korrekturrunden'  => 2,
                'ab_preis'         => false,
            ],
            'sonderprojekt' => [
                'name'             => 'Sonderprojekt',
                'einmalig_cent'    => 1250000,
                'schutz'           => 'mindestens Schutz L',
                'monatlich_cent'   => 24900,
                'erstes_jahr_cent' => 1548800,
                // Umfang und Korrekturrunden stehen im Masterkonzept als „individuell".
                // Eine Zahl steht dort nicht, also steht hier auch keine.
                'seiten'           => 'Umfang nach Prüfung',
                'korrekturrunden'  => 0,
                'ab_preis'         => true,
            ],
        ];
    }

    /**
     * @return array<string,mixed>|null `null` für `unklar` — dafür gibt es keine Zeile.
     */
    public static function zeile(string $paket): ?array
    {
        return self::tabelle()[$paket] ?? null;
    }

    /**
     * `unklar` ist kein Paket, sondern das Fehlen einer Entscheidung — die Preistabelle
     * hat dafür keine Zeile. Es bekommt deshalb einen eigenen Namen und nicht ersatzweise
     * den eines Pakets.
     */
    public static function name(string $paket): string
    {
        $zeile = self::zeile($paket);

        if ($zeile !== null) {
            return (string) $zeile['name'];
        }

        return $paket === 'unklar' ? 'Noch offen' : $paket;
    }

    /**
     * Die Preiszeile aus Website-Lastenheft §9.3, gebundene Form:
     * `7.900 € einmalig + 249 €/Monat Rundum-Schutz`.
     */
    public static function preiszeile(string $paket): ?string
    {
        $zeile = self::zeile($paket);

        if ($zeile === null) {
            return null;
        }

        return ($zeile['ab_preis'] ? 'ab ' : '')
            . Format::euro($zeile['einmalig_cent']) . ' einmalig + '
            . Format::euro($zeile['monatlich_cent']) . '/Monat Rundum-Schutz';
    }

    public static function erstesJahr(string $paket): ?string
    {
        $zeile = self::zeile($paket);

        if ($zeile === null) {
            return null;
        }

        return ($zeile['ab_preis'] ? 'ab ' : '') . Format::euro($zeile['erstes_jahr_cent']) . ' netto';
    }
}
