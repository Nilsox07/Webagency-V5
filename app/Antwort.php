<?php

declare(strict_types=1);

namespace Sartu;

/**
 * Eine Antwort als Wert, nicht als Nebenwirkung.
 *
 * Damit laesst sich der Dispatcher im Test aufrufen, ohne einen Webserver zu starten und
 * ohne gegen die Arbeitsdatenbank zu laufen. Die Testfaelle 41, 43, 47, 49 und 73 pruefen
 * genau das, was der Browser bekommt.
 */
final class Antwort
{
    /** @param array<string,string> $kopfzeilen */
    private function __construct(
        public readonly int $status,
        public readonly array $kopfzeilen,
        public readonly string $rumpf,
    ) {
    }

    /** @param array<string,string> $kopfzeilen */
    public static function html(string $rumpf, int $status = 200, array $kopfzeilen = []): self
    {
        return new self($status, ['Content-Type' => 'text/html; charset=utf-8'] + $kopfzeilen, $rumpf);
    }

    /**
     * Ein Download — Portal-Lastenheft §4b.4, „Datensatz exportieren".
     *
     * Eine eigene Fabrik und nicht `html()` mit anderer Kopfzeile: `html()` setzt den
     * Inhaltstyp mit `+` und gewinnt damit gegen jede uebergebene Kopfzeile. Ein Export,
     * der als `text/html` ausgeliefert wird, oeffnet sich im Browser statt zu speichern —
     * und wird vom Browser als Auszeichnung gelesen.
     *
     * @param array<string,string> $kopfzeilen
     */
    public static function datei(string $rumpf, string $inhaltstyp, string $dateiname, array $kopfzeilen = []): self
    {
        return new self(200, [
            'Content-Type'        => $inhaltstyp,
            'Content-Disposition' => 'attachment; filename="' . str_replace('"', '', $dateiname) . '"',
            'X-Content-Type-Options' => 'nosniff',
        ] + $kopfzeilen, $rumpf);
    }

    /**
     * Ein Bild zum Anzeigen — `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4c, das Gruenderbild.
     *
     * **Eigene Fabrik und ausdruecklich nicht `datei()`.** Jene setzt
     * `Content-Disposition: attachment`, weil Kundenuploads auch SVG sein koennen und ein
     * SVG im Browser Skript ausfuehrt. Hier kommen nur JPG, PNG und WebP herein, jedes am
     * Inhalt geprueft — und ein Anhang fuellt kein `<img>`.
     *
     * `nosniff` bleibt: Der Browser soll den Typ nicht selbst erraten, sondern den nehmen,
     * den die Pruefung beim Hochladen festgestellt hat.
     *
     * @param array<string,string> $kopfzeilen
     */
    public static function bild(string $rumpf, string $inhaltstyp, array $kopfzeilen = []): self
    {
        return new self(200, [
            'Content-Type'           => $inhaltstyp,
            'X-Content-Type-Options' => 'nosniff',
        ] + $kopfzeilen, $rumpf);
    }

    /**
     * Die 404-Seite.
     *
     * Sie steht hier und nicht in drei Steuerungen: Ein abweichender Wortlaut verraet, dass
     * eine Route anders behandelt wird als die anderen — und §3 Regel 2 verlangt gerade,
     * dass „gibt es nicht" und „gehoert dir nicht" ununterscheidbar sind.
     */
    public static function nichtGefunden(): self
    {
        return self::html(Ansicht::seite('oeffentlich', 'fehler', [
            'titel'   => 'Diese Seite gibt es nicht',
            'meldung' => 'Der Link führt ins Leere. Vielleicht hat sich die Adresse geändert.',
            'kennung' => null,
        ]), 404);
    }

    /**
     * Eine Antwort fuer eine Maschine — `/api/`, `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6.
     *
     * Reiner Text und kein HTML: Am anderen Ende steht kein Browser, sondern der
     * Zahlungsdienst, und der liest nur den Statuscode. Eine Fehlerseite mit Layout waere
     * eine Antwort an niemanden — und der Rumpf einer Maschinenantwort ist die letzte
     * Stelle, an der man eine Ausnahme versehentlich nach draussen schreibt.
     */
    public static function text(string $rumpf, int $status = 200): self
    {
        return new self($status, ['Content-Type' => 'text/plain; charset=utf-8'], $rumpf);
    }

    public static function weiter(string $ziel, int $status = 302): self
    {
        return new self($status, ['Location' => $ziel], '');
    }

    /** @param array<string,string> $kopfzeilen */
    public function mitKopfzeilen(array $kopfzeilen): self
    {
        return new self($this->status, $kopfzeilen + $this->kopfzeilen, $this->rumpf);
    }

    public function senden(): void
    {
        http_response_code($this->status);
        foreach ($this->kopfzeilen as $name => $wert) {
            header($name . ': ' . $wert, true);
        }
        echo $this->rumpf;
    }
}
