<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Format;

/**
 * Die Statusanzeige der Auftragslage — Website-Lastenheft §5a.
 *
 * ## Vier Zustände, und der vierte zeigt nichts
 *
 * §5a führt „nicht gesetzt" als eigene Zeile: **nichts wird angezeigt.** Deshalb gibt es
 * hier keinen Vorgabewert. Eine Anzeige „Freie Kapazitäten", die niemand gesetzt hat, ist
 * eine Aussage über den Betrieb, die niemand getroffen hat.
 *
 * ## Der Zustand ändert bei `ausgebucht` die Handlung
 *
 * Nur dort. Bei `offen` und `knapp` bleibt der Knopf `Bedarf prüfen lassen`; bei
 * `ausgebucht` heißt er `Auf die Warteliste`, und die Zeile steht **über** dem Knopf statt
 * darunter. §5a: Eine Anfrage wäre dann eine Sackgasse.
 *
 * ## Nie allein über Farbe
 *
 * Jeder Zustand hat eine eigene Füllung des Punktes **und** einen eigenen Text. Ein Zustand,
 * den man nur an der Farbe erkennt, ist für einen Teil der Leser kein Zustand (§2a).
 *
 * ## Was hier bewusst fehlt — und was seit dem 13.08.2026 dasteht
 *
 * Keine Zahl, kein Pulsieren. Ein pulsierender Punkt behauptet Echtzeitüberwachung für einen
 * Wert, der sich vielleicht monatlich ändert, und „3 Plätze frei" wäre eine ungeprüfte Zusage.
 *
 * **Der Monat steht jetzt da.** `Nur noch wenige Plätze` sagte nichts, was sich nachprüfen
 * ließe — weder ob es stimmt noch wann es sich ändert. Genau das macht eine Knappheitsangabe
 * zur Behauptung, und der Texter-Skill verwirft `wenige` an derselben Stelle, an der er
 * `passend` verwirft. Betreiberentscheidung vom 13.08.2026.
 *
 * **Das widerspricht `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` §5a**, wörtlich: „Keine
 * Zahlen, keine Termine. Weder ,3 Plätze frei' noch ,ab Q3'." Diese Datei ist
 * Begründungsarchiv, keine Bauvorlage (`CLAUDE.md`, Rangfolge); `spezifikation/` bindet zur
 * Kapazitätszeile nur ihre Stelle und ihr Gewicht, keinen Wortlaut und kein Terminverbot.
 * Vermerkt in `OFFENE_PRUEFUNGEN.md`.
 *
 * Die Sorge des Archivs bleibt trotzdem berücksichtigt: Es steht ein **Monat**, kein Tag —
 * ein Starttermin für die Arbeit, keine Zusage über die Fertigstellung. Und der Wert kommt
 * aus `operator_settings`, nicht aus dieser Datei.
 *
 * ## Ohne Datum entfällt `knapp` vollständig
 *
 * §4c: „Feld leer → nichts wird ausgeliefert." Für `offen` und `ausgebucht` gibt es einen
 * Text, der ohne Datum trägt — für `knapp` gab es nur den, der ersetzt wurde. Steht der
 * Monat nicht da, bleibt die Zeile deshalb leer, statt auf die alte Fassung zurückzufallen.
 */
final class Auftragslage
{
    public const OFFEN      = 'offen';
    public const KNAPP      = 'knapp';
    public const AUSGEBUCHT = 'ausgebucht';

    /** Die Knopfbeschriftung im Regelfall — gebundener Wortlaut. */
    public const KNOPF = 'Bedarf prüfen lassen';

    /** Nur bei `ausgebucht` — §5a. */
    public const KNOPF_WARTELISTE = 'Auf die Warteliste';

    /** Die drei Werte, die `chk_operator_settings_auftragslage` zulässt. */
    public const WERTE = [self::OFFEN, self::KNAPP, self::AUSGEBUCHT];

    /**
     * Die Zustände ohne Datum. `text` ist `null`, wo ein Text ohne Monat nicht trägt.
     *
     * @return array<string,array{text:?string,fuellung:string,gewicht:string,knopf:string}>
     */
    public static function zustaende(): array
    {
        return [
            self::OFFEN => [
                'text'     => 'Freie Kapazitäten',
                'fuellung' => 'voll',
                'gewicht'  => 'leise',
                'knopf'    => self::KNOPF,
            ],
            self::KNAPP => [
                // Ohne Monat gibt es hier nichts zu sagen — siehe Klassenkopf.
                'text'     => null,
                'fuellung' => 'halb',
                'gewicht'  => 'leise',
                'knopf'    => self::KNOPF,
            ],
            self::AUSGEBUCHT => [
                'text'     => 'Zurzeit ausgebucht — Warteliste möglich',
                'fuellung' => 'ring',
                'gewicht'  => 'betont',
                'knopf'    => self::KNOPF_WARTELISTE,
            ],
        ];
    }

    /**
     * @param ?string $projektstart Kalendertag in `Y-m-d` aus `operator_settings`
     * @return array<string,string>|null `null` heißt: nichts anzeigen.
     */
    public static function anzeige(?string $wert, ?string $projektstart = null): ?array
    {
        if ($wert === null || $wert === '') {
            return null;
        }

        $zustand = self::zustaende()[$wert] ?? null;

        if ($zustand === null) {
            return null;
        }

        $monat = self::monat($projektstart);

        if ($monat !== null) {
            $zustand['text'] = $wert === self::AUSGEBUCHT
                ? 'Zurzeit ausgebucht — nächster Projektstart ab ' . $monat
                : 'Nächster Projektstart ab ' . $monat;
        }

        // §4c: Feld leer, also nichts ausliefern. Der Knopf bleibt davon unberührt —
        // er hängt am Zustand, nicht am Text.
        return $zustand['text'] === null ? null : $zustand;
    }

    /**
     * Der Monat, wenn er in der Zukunft liegt.
     *
     * **Ein vergangener Starttermin wird nicht angezeigt.** Er ist der Fall, der von selbst
     * eintritt: Der Betreiber trägt „ab September" ein und vergisst es. Im November liest
     * der Besucher dann eine Angabe, die nachweislich nicht mehr stimmt — und eine falsche
     * überprüfbare Angabe ist schlechter als gar keine.
     */
    private static function monat(?string $projektstart): ?string
    {
        if ($projektstart === null || trim($projektstart) === '') {
            return null;
        }

        try {
            $start = new \DateTimeImmutable(trim($projektstart));
        } catch (\Exception) {
            return null;
        }

        // Verglichen wird auf den Monat, nicht auf den Tag: Wer den 1. September einträgt,
        // meint den September — und der ist am 20. September noch nicht vorbei.
        if ($start->format('Y-m') < substr(Format::heute(), 0, 7)) {
            return null;
        }

        return Format::monatJahr($projektstart);
    }

    /** Die Beschriftung des Hauptknopfs — auch dann richtig, wenn nichts gesetzt ist. */
    public static function knopf(?string $wert): string
    {
        return self::anzeige($wert)['knopf'] ?? self::KNOPF;
    }
}
