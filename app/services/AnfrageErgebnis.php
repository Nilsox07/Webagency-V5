<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Das Ergebnis einer Anfrageannahme.
 *
 * Vier Ausgänge, und der Unterschied zwischen zweien davon ist der ganze Punkt:
 *
 * | Ausgang | Was der Absender sieht |
 * |---|---|
 * | `angelegt` | Danke-Seite |
 * | `verschickt` | Danke-Seite — Mail raus, **kein** Datensatz (§4b.6, Kontaktformular) |
 * | `stillVerworfen` | **dieselbe** Danke-Seite — Honigtopf, Zeitregel, Doppeleinreichung |
 * | `abgewiesen` | den Schritt erneut, Meldung am Feld |
 * | `begrenzt` | Hinweis mit Kontaktalternative, keine technischen Details |
 *
 * Ein stillschweigend verworfener Versuch sieht für den Absender aus wie ein Erfolg. Wer
 * erfährt, dass sein Versuch erkannt wurde, probiert den nächsten (§4b.2).
 */
final class AnfrageErgebnis
{
    /**
     * @param array<string,string> $feldfehler
     * @param list<string> $gruende
     */
    private function __construct(
        public readonly bool $dankeSeite,
        public readonly ?string $anfrageId = null,
        public readonly ?string $paket = null,
        public readonly ?string $ampel = null,
        public readonly array $gruende = [],
        public readonly array $feldfehler = [],
        public readonly ?string $meldung = null,
    ) {
    }

    /**
     * @param string|null $paket `null` bei einer Rückfrage über `/kontakt` — dort gab es
     *                           keinen Bedarfsscheck, also gibt es keine Empfehlung.
     * @param list<string> $gruende
     */
    public static function angelegt(string $id, ?string $paket, string $ampel, array $gruende): self
    {
        return new self(true, $id, $paket, $ampel, $gruende);
    }

    public static function stillVerworfen(): self
    {
        return new self(true);
    }

    /**
     * Verschickt, aber **nicht gespeichert** — das Kontaktformular (§4b.6).
     *
     * Es gibt keine Kennung, kein Paket und keine Ampel, weil es keinen Datensatz gibt.
     * `wurdeGespeichert()` bleibt deshalb `false` — und das ist die richtige Antwort, nicht
     * eine ungenaue: Wer danach fragt, will wissen, ob etwas in der Datenbank steht.
     */
    public static function verschickt(): self
    {
        return new self(true);
    }

    /** @param array<string,string> $fehler */
    public static function abgewiesenAmFeld(array $fehler): self
    {
        return new self(false, feldfehler: $fehler);
    }

    /** @param list<string> $meldungen */
    public static function abgewiesen(array $meldungen): self
    {
        return new self(false, meldung: implode(' ', $meldungen));
    }

    public static function begrenzt(): self
    {
        return new self(false, meldung:
            'Wir haben gerade sehr viele Anfragen. Bitte versuchen Sie es in einer Stunde erneut '
            . 'oder schreiben Sie uns.');
    }

    public function wurdeGespeichert(): bool
    {
        return $this->anfrageId !== null;
    }
}
