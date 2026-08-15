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

    /**
     * @param array<string,string> $kopfzeilen
     *
     * **`+` behält den linken Schlüssel.** Ein `Content-Type` in `$kopfzeilen` gewinnt
     * deshalb **nicht** — er wird stillschweigend verworfen. Das ist hier gewollt (eine
     * HTML-Antwort ist HTML), und `datei()`, `bild()`, `xml()` und `klartext()` bestehen
     * genau deswegen als eigene Fabriken.
     *
     * Am 15.08.2026 gemessen, was passiert, wenn man es doch versucht: `sitemap.xml`,
     * `robots.txt` und `llms.txt` gingen als `text/html` hinaus, weil sie `html()` mit
     * einer abweichenden Kopfzeile aufriefen. Eine als HTML ausgelieferte Sitemap liest
     * kein Suchdienst als Sitemap.
     */
    public static function html(string $rumpf, int $status = 200, array $kopfzeilen = []): self
    {
        return new self($status, ['Content-Type' => 'text/html; charset=utf-8'] + $kopfzeilen, $rumpf);
    }

    /**
     * XML für Maschinen — `sitemap.xml`.
     *
     * Eigene Fabrik statt `html()` mit anderer Kopfzeile: siehe dort. `nosniff` gehört
     * dazu, damit der Browser den Typ nicht doch errät.
     *
     * @param array<string,string> $kopfzeilen
     */
    public static function xml(string $rumpf, array $kopfzeilen = []): self
    {
        return new self(200, [
            'Content-Type'           => 'application/xml; charset=utf-8',
            'X-Content-Type-Options' => 'nosniff',
        ] + $kopfzeilen, $rumpf);
    }

    /**
     * Reiner Text als **Seite** — `robots.txt`, `llms.txt`.
     *
     * Getrennt von `text()`: Jene ist die Antwort an den Zahlungsdienst und trägt einen
     * Statuscode als eigentliche Nachricht. Diese liefert eine Datei aus, die ein Crawler
     * abholt, und darf deshalb zwischengespeichert werden.
     *
     * @param array<string,string> $kopfzeilen
     */
    public static function klartext(string $rumpf, array $kopfzeilen = []): self
    {
        return new self(200, [
            'Content-Type'           => 'text/plain; charset=utf-8',
            'X-Content-Type-Options' => 'nosniff',
        ] + $kopfzeilen, $rumpf);
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
