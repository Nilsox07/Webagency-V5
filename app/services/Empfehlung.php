<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Die deterministische Ampel — Masterkonzept §8, Website-Lastenheft §9.2 und §9.3.
 *
 * Sie entscheidet zwei Dinge: welchen Umfang ein Interessent empfohlen bekommt, und ob
 * SARTU vor dem Angebot prüft. Beides hängt an Geld — zwischen `wachstum` (3.900 €) und
 * `sonderprojekt` (ab 12.500 €) liegt ein fünfstelliger Betrag.
 *
 * **Warum das hier steht und nicht im Browser** (Testfall 39, Website §9.5a): Die Regel läuft
 * ausschließlich auf dem Server. Der Browser darf sie spiegeln, aber ein manipuliertes
 * Formularfeld ändert das Ergebnis nicht — sonst könnte sich jeder seine Empfehlung selbst
 * setzen.
 *
 * ## Was hier NICHT gerechnet wird, und warum
 *
 * Masterkonzept §8 nennt drei Orange-Bedingungen, die sich aus dem Formular nicht ableiten
 * lassen. Sie werden **nicht geraten**:
 *
 * | Bedingung | Warum nicht |
 * |---|---|
 * | `> 1 Conversionpfad` | Es gibt kein Feld, das Conversionpfade zählt |
 * | `Freitext nennt Sonderfunktion ohne Auswahl` | Bräuchte eine Stichwortliste, die nirgends steht |
 * | `knappe Frist` | „Knapp" ist nirgends als Zahl festgelegt, und der Lieferkorridor entsteht erst im Angebot. Ein fester Termin setzt deshalb **gelb** — eine Rückfrage, keine erfundene Grenze |
 *
 * Alle drei sind in `OFFENE_PRUEFUNGEN.md` vermerkt.
 */
final class Empfehlung
{
    // ------------------------------------------------------- Thema 3: Umfangssignale

    public const SIGNAL_HAUPTANGEBOT      = 'hauptangebot';
    public const SIGNAL_MEHRERE_LEISTUNGEN = 'mehrere_leistungen';
    public const SIGNAL_MEHRERE_REGIONEN  = 'mehrere_regionen';
    public const SIGNAL_RECRUITING        = 'recruiting';
    public const SIGNAL_PROJEKTE_AKTUELL  = 'projekte_aktuell';
    public const SIGNAL_NICHTS_DAVON      = 'nichts_davon';

    /**
     * Die vier **starken** Signale aus Masterkonzept §8.
     *
     * Dort heißt es: „Platzhirsch bei ≥ 2 starken Signalen (mehrere Leistungen, mehrere
     * Regionen, Recruiting, Projekte …)". Genau diese vier sind vier der sechs
     * Auswahlmöglichkeiten in Thema 3. Die beiden übrigen — ein klares Hauptangebot und
     * „nichts davon" — sind keine Signale für mehr Umfang, sondern für weniger.
     */
    private const STARKE_SIGNALE = [
        self::SIGNAL_MEHRERE_LEISTUNGEN,
        self::SIGNAL_MEHRERE_REGIONEN,
        self::SIGNAL_RECRUITING,
        self::SIGNAL_PROJEKTE_AKTUELL,
    ];

    // ------------------------------------------------------- Thema 4: Sonderrisiken

    public const GATE_FORMULAR         = 'formular';
    public const GATE_TERMINBUCHUNG    = 'terminbuchung';
    public const GATE_SHOP             = 'shop';
    public const GATE_LOGIN            = 'login';
    public const GATE_SCHNITTSTELLE    = 'schnittstelle';
    public const GATE_MEHRERE_SPRACHEN = 'mehrere_sprachen';
    public const GATE_GETRENNTE_MARKEN = 'getrennte_marken';
    public const GATE_BESONDERE_DATEN  = 'besondere_daten';
    public const GATE_NICHTS_DAVON     = 'nichts_davon';

    /**
     * Rot — Sonderprojekt. Masterkonzept §8: „Shop/Zahlung, Login/Rollen, individuelle
     * Schnittstelle, komplexe Mehr-Ressourcen-Buchung, mehrere Marken/Domains, sensible
     * Uploads, formaler Spezialaudit."
     */
    private const ROTE_GATES = [
        self::GATE_SHOP,
        self::GATE_LOGIN,
        self::GATE_SCHNITTSTELLE,
        self::GATE_GETRENNTE_MARKEN,
        self::GATE_BESONDERE_DATEN,
    ];

    /**
     * Orange — SARTU-Prüfung mit kurzem Fachmodul.
     *
     * Zwei Einträge gehen auf Entscheidungen des Betreibers vom 02.08.2026 zurück, weil das
     * Regelwerk und die Formularoptionen unterschiedlich geschnitten waren:
     *
     * - **`mehrere_sprachen`** war mit `getrennte_marken` in **einer** Auswahl
     *   zusammengefasst. Das Regelwerk stuft Sprachen orange und Marken rot ein — eine
     *   Auswahl, zwei Farben. **Die Auswahl ist jetzt aufgeteilt** (Website §9.2 Thema 4).
     * - **`terminbuchung`** heißt im Formular „einfache Terminbuchung", das Regelwerk kennt
     *   nur „unklare Buchung" (orange) und „komplexe Buchung" (rot). Entschieden: orange.
     *   Ob eine Buchung wirklich einfach ist, weiß der Interessent beim Ausfüllen nicht —
     *   genau das meint „unklar".
     */
    private const ORANGE_GATES = [
        self::GATE_TERMINBUCHUNG,
        self::GATE_MEHRERE_SPRACHEN,
    ];

    // ------------------------------------------------------- Ergebnis

    /**
     * @param list<string> $umfangssignale  Thema 3
     * @param list<string> $sonderfunktionen Thema 4
     * @param bool $bestehendeWebsiteUnklar  Thema 1.4 = „Bin unsicher"
     * @param bool $zielgruppeUnklar         Thema 2.2 = „Noch unklar"
     * @param bool $domainUnklar             Thema 5.1 = „Bin unsicher"
     * @param bool $festerTermin             Thema 5.2 = „Ja"
     *
     * @return array{paket:string,ampel:string,gruende:list<string>}
     */
    public static function bestimmen(
        array $umfangssignale,
        array $sonderfunktionen,
        bool $bestehendeWebsiteUnklar = false,
        bool $zielgruppeUnklar = false,
        bool $domainUnklar = false,
        bool $festerTermin = false,
    ): array {
        $rot = array_values(array_intersect(self::ROTE_GATES, $sonderfunktionen));

        if ($rot !== []) {
            return [
                'paket'   => 'sonderprojekt',
                'ampel'   => 'rot',
                'gruende' => array_map(static fn (string $g) => self::gateName($g), $rot),
            ];
        }

        $orange = array_values(array_intersect(self::ORANGE_GATES, $sonderfunktionen));
        $starke = array_values(array_intersect(self::STARKE_SIGNALE, $umfangssignale));

        // §8: „unklar an paketentscheidender Stelle". Thema 3 entscheidet das Paket —
        // steht dort „nichts davon / bin unsicher", gibt es keine Empfehlung, sondern eine
        // Rückfrage (Website §9.3, „Bei Unklarheit").
        $umfangUnklar = in_array(self::SIGNAL_NICHTS_DAVON, $umfangssignale, true);

        $paket = match (true) {
            $umfangUnklar          => 'unklar',
            count($starke) >= 2    => 'platzhirsch',
            count($starke) === 1   => 'wachstum',
            default                => 'start',
        };

        if ($orange !== []) {
            return [
                'paket'   => $paket,
                'ampel'   => 'orange',
                'gruende' => array_map(static fn (string $g) => self::gateName($g), $orange),
            ];
        }

        $gelb = [];

        if ($umfangUnklar) {
            $gelb[] = 'Der Umfang ist noch offen.';
        }

        if ($bestehendeWebsiteUnklar) {
            $gelb[] = 'Der Stand der bestehenden Website ist unklar.';
        }

        if ($zielgruppeUnklar) {
            $gelb[] = 'Die Zielgruppe ist noch offen.';
        }

        if ($domainUnklar) {
            $gelb[] = 'Der Domainstatus ist ungeklärt.';
        }

        if ($festerTermin) {
            // §8 stuft eine „knappe Frist" orange ein. Wie knapp, steht nirgends als Zahl,
            // und der Lieferkorridor entsteht erst im Angebot. Ein fester Termin setzt
            // deshalb gelb — genau das, was er braucht: eine Rückfrage.
            $gelb[] = 'Es gibt einen festen Termin.';
        }

        if ($gelb !== []) {
            return ['paket' => $paket, 'ampel' => 'gelb', 'gruende' => $gelb];
        }

        return ['paket' => $paket, 'ampel' => 'standard', 'gruende' => []];
    }

    /** @return list<string> */
    public static function alleSignale(): array
    {
        return [
            self::SIGNAL_HAUPTANGEBOT,
            self::SIGNAL_MEHRERE_LEISTUNGEN,
            self::SIGNAL_MEHRERE_REGIONEN,
            self::SIGNAL_RECRUITING,
            self::SIGNAL_PROJEKTE_AKTUELL,
            self::SIGNAL_NICHTS_DAVON,
        ];
    }

    /** @return list<string> */
    public static function alleGates(): array
    {
        return [
            self::GATE_FORMULAR,
            self::GATE_TERMINBUCHUNG,
            self::GATE_SHOP,
            self::GATE_LOGIN,
            self::GATE_SCHNITTSTELLE,
            self::GATE_MEHRERE_SPRACHEN,
            self::GATE_GETRENNTE_MARKEN,
            self::GATE_BESONDERE_DATEN,
            self::GATE_NICHTS_DAVON,
        ];
    }

    private static function gateName(string $gate): string
    {
        return match ($gate) {
            self::GATE_TERMINBUCHUNG    => 'Terminbuchung',
            self::GATE_SHOP             => 'Verkauf oder Zahlungen',
            self::GATE_LOGIN            => 'Kundenlogin oder geschützter Bereich',
            self::GATE_SCHNITTSTELLE    => 'Verbindung zu anderer Software',
            self::GATE_MEHRERE_SPRACHEN => 'Mehrere Sprachen',
            self::GATE_GETRENNTE_MARKEN => 'Getrennte Marken oder Domains',
            self::GATE_BESONDERE_DATEN  => 'Besondere Daten oder formaler Nachweis',
            default                     => $gate,
        };
    }
}
