<?php

declare(strict_types=1);

namespace Sartu\Tests;

use PHPUnit\Framework\TestCase;
use Sartu\Antwort;
use Sartu\Sitzung;

/**
 * Was **über die Leitung** geht: Inhaltstyp, Cookie, Cachepolitik.
 *
 * ## Warum es diesen Test gibt
 *
 * Gemessen am 15.08.2026 am Stand `7fafead`: `/sitemap.xml`, `/robots.txt` und `/llms.txt`
 * gingen mit `Content-Type: text/html` hinaus, dazu ein `Set-Cookie: PHPSESSID` und
 * `Cache-Control: no-store, no-cache, must-revalidate`.
 *
 * | Was | Warum es zählt |
 * |---|---|
 * | Sitemap als `text/html` | Kein Suchdienst liest sie dann als Sitemap |
 * | Cookie auf `robots.txt` | Ein Crawler bekommt bei jedem Abruf eine neue Sitzung gesetzt |
 * | `no-store` auf allem | Auch die öffentliche Startseite war damit nie zwischenspeicherbar |
 *
 * **Zwei Ursachen, beide klein.** `Antwort::html()` fügt den Inhaltstyp mit `+` hinzu, und
 * `+` behält den linken Schlüssel — eine übergebene Kopfzeile wird verworfen. Und
 * `public/index.php` startete die Sitzung vor der Route; `session_start()` stempelt mit dem
 * Vorgabewert `session.cache_limiter = nocache` die drei Cachekopfzeilen auf jede Antwort.
 *
 * ## Warum er über HTTP misst und nicht über den Router
 *
 * Cookie und Cachepolitik entstehen erst im Zusammenspiel von `public/index.php`,
 * `session_start()` und dem Router. Ein Test, der nur `Router::behandeln()` aufruft, sieht
 * genau die zwei Kopfzeilen nicht, um die es geht. **Fehlt der Webserver, schlägt der Test
 * an — er wird nicht übersprungen** (dieselbe Entscheidung wie in `OberflaecheTest`).
 */
final class AuslieferungTest extends TestCase
{
    private const BASIS = 'http://localhost:8080';

    /**
     * Die drei Dateien für Maschinen — je erwarteter Inhaltstyp.
     *
     * @var array<string,string>
     */
    private const WURZELDATEIEN = [
        '/sitemap.xml' => 'application/xml; charset=utf-8',
        '/robots.txt'  => 'text/plain; charset=utf-8',
        '/llms.txt'    => 'text/plain; charset=utf-8',
    ];

    /**
     * Die Kopfzeilen einer Adresse, in Kleinschreibung.
     *
     * @return array<string,list<string>>
     */
    private function kopfzeilen(string $pfad): array
    {
        $rohdaten = @get_headers(self::BASIS . $pfad, true);

        $this->assertIsArray($rohdaten, 'Der Webserver antwortet nicht unter ' . self::BASIS
            . '. Dieser Test misst, was über die Leitung geht, und kann das ohne ihn nicht. '
            . 'Start: `PHP_CLI_SERVER_WORKERS=8 php -S 0.0.0.0:8080 -t public <router>`.');

        $kopfzeilen = [];

        foreach ($rohdaten as $name => $wert) {
            if (!is_string($name)) {
                continue;
            }

            $kopfzeilen[strtolower($name)] = is_array($wert) ? $wert : [$wert];
        }

        return $kopfzeilen;
    }

    private function eine(string $pfad, string $name): string
    {
        $kopfzeilen = $this->kopfzeilen($pfad);

        return $kopfzeilen[$name][0] ?? '';
    }

    // ---------------------------------------------------------------- Inhaltstyp

    /** Jede der drei Wurzeldateien trägt ihren eigenen Inhaltstyp, nicht `text/html`. */
    public function testJedeWurzeldateiTraegtIhrenInhaltstyp(): void
    {
        foreach (self::WURZELDATEIEN as $pfad => $erwartet) {
            $this->assertSame($erwartet, $this->eine($pfad, 'content-type'),
                $pfad . ' geht mit dem falschen Inhaltstyp hinaus. Als `text/html` '
                . 'ausgeliefert liest kein Suchdienst die Datei als das, was sie ist.');
        }
    }

    /** `nosniff` bleibt — der Browser soll den Typ nicht erraten. */
    public function testJedeWurzeldateiTraegtNosniff(): void
    {
        foreach (array_keys(self::WURZELDATEIEN) as $pfad) {
            $this->assertSame('nosniff', $this->eine($pfad, 'x-content-type-options'),
                $pfad . ' erlaubt dem Browser, den Inhaltstyp selbst zu erraten.');
        }
    }

    // ---------------------------------------------------------------- Cookie

    /** Keine der drei Dateien setzt ein Sitzungscookie. */
    public function testKeineWurzeldateiSetztEinCookie(): void
    {
        foreach (array_keys(self::WURZELDATEIEN) as $pfad) {
            $this->assertArrayNotHasKey('set-cookie', $this->kopfzeilen($pfad),
                $pfad . ' setzt ein Cookie. Ein Suchdienst holt die Datei regelmässig ab und '
                . 'legt dabei jedes Mal eine Sitzung an, die niemand je benutzt.');
        }
    }

    /**
     * Eine öffentliche Seite bekommt weiterhin eine Sitzung.
     *
     * **Das ist kein Widerspruch, sondern die Bedingung.** Portal-Lastenheft §4b.7 verlangt
     * Landeseite, verweisenden Host und Kampagnenkennzeichen aus der **ersten** aufgerufenen
     * Seite. Die ist bei fast jedem Besucher eine öffentliche — ohne Sitzung dort stünde
     * später als Landeseite `/briefing`.
     */
    public function testEineOeffentlicheSeiteBekommtEineSitzung(): void
    {
        $this->assertArrayHasKey('set-cookie', $this->kopfzeilen('/'),
            'Die Startseite legt keine Sitzung mehr an. §4b.7 kann die Herkunft dann nicht '
            . 'mehr beim ersten Aufruf merken.');
    }

    // ---------------------------------------------------------------- Cachepolitik

    /** Die drei Dateien dürfen zwischengespeichert werden. */
    public function testJedeWurzeldateiDarfZwischengespeichertWerden(): void
    {
        foreach (array_keys(self::WURZELDATEIEN) as $pfad) {
            $wert = $this->eine($pfad, 'cache-control');

            $this->assertStringContainsString('public', $wert,
                $pfad . ' ist nicht zwischenspeicherbar (Cache-Control: ' . $wert . ').');
            $this->assertStringNotContainsString('no-store', $wert,
                $pfad . ' trägt weiterhin `no-store`.');
        }
    }

    /**
     * Eine öffentliche Seite ist `private`, nicht `no-store` und nicht `public`.
     *
     * Sie trägt ein Sitzungscookie und darf deshalb in keinen gemeinsamen Zwischenspeicher.
     * `max-age=0` heisst „nachfragen", nicht „nie speichern" — der Browser darf mit `304`
     * antworten.
     */
    public function testEineOeffentlicheSeiteIstPrivatZwischenspeicherbar(): void
    {
        $wert = $this->eine('/', 'cache-control');

        $this->assertStringContainsString('private', $wert, 'Cache-Control: ' . $wert);
        $this->assertStringNotContainsString('no-store', $wert,
            'Die Startseite trägt weiterhin `no-store`. Das kam von `session_start()`; '
            . '`Sitzung::starten()` setzt `session_cache_limiter` seit dem 15.08.2026 leer.');
    }

    /** Was hinter einer Anmeldung liegt, wird nicht zwischengespeichert. */
    public function testGeschuetzteBereicheBleibenOhneZwischenspeicher(): void
    {
        foreach (['/portal', '/admin'] as $pfad) {
            $this->assertStringContainsString('no-store', $this->eine($pfad, 'cache-control'),
                $pfad . ' darf zwischengespeichert werden. Dort steht, was nur eine Person '
                . 'sehen darf.');
        }
    }

    // ---------------------------------------------------------------- die Fabriken selbst

    /**
     * Die Falle, aus der der Fehler kam: `+` behält den linken Schlüssel.
     *
     * Der Test steht hier, damit niemand die drei Fabriken wieder auf `html()` mit
     * abweichender Kopfzeile zurückbaut — sie sähe im Quelltext richtig aus.
     */
    public function testEineUebergebeneKopfzeileGewinntNichtGegenHtml(): void
    {
        $antwort = Antwort::html('x', 200, ['Content-Type' => 'application/xml']);

        $this->assertSame('text/html; charset=utf-8', $antwort->kopfzeilen['Content-Type'],
            'Wenn `html()` eine übergebene Kopfzeile übernimmt, ist dieser Test überflüssig — '
            . 'dann aber prüfen, ob `datei()` und `bild()` noch das tun, was ihr Kopf sagt.');
    }

    /** Die beiden neuen Fabriken setzen Typ und `nosniff` selbst. */
    public function testDieNeuenFabrikenSetzenTypUndNosniff(): void
    {
        foreach ([
            [Antwort::xml('<a/>'), 'application/xml; charset=utf-8'],
            [Antwort::klartext('x'), 'text/plain; charset=utf-8'],
        ] as [$antwort, $typ]) {
            $this->assertSame($typ, $antwort->kopfzeilen['Content-Type']);
            $this->assertSame('nosniff', $antwort->kopfzeilen['X-Content-Type-Options']);
        }
    }

    // ---------------------------------------------------------------- Installation

    /**
     * Jede Erweiterung, die eine Abhängigkeit verlangt, steht in `composer.json`.
     *
     * ## Warum das kein Formalismus ist
     *
     * Gemessen am 15.08.2026: `setasign/fpdf` — über `horstoeko/zugferd` eingezogen — führt
     * `ext-gd` als Pflicht, und `composer.json` nannte sie nicht. Auf einem Server ohne GD
     * bricht damit ein frisches `composer install` ab. **In der Entwicklung fällt das nie
     * auf**, weil der Container alle Erweiterungen hat; aufgefallen wäre es beim Livegang.
     *
     * Insgesamt fehlten sechs: `ctype`, `dom`, `gd`, `iconv`, `simplexml`, `zlib`.
     *
     * Nicht geprüft werden `filter`, `hash` und `pcre`. Sie lassen sich in PHP 8 nicht
     * abschalten; eine Zeile dafür in `composer.json` wäre Zierde.
     */
    public function testJedeVerlangteErweiterungStehtInComposerJson(): void
    {
        $json = json_decode((string) file_get_contents(SARTU_WURZEL . '/composer.json'), true);
        $lock = json_decode((string) file_get_contents(SARTU_WURZEL . '/composer.lock'), true);

        $this->assertIsArray($json);
        $this->assertIsArray($lock);

        /** @var array<string,mixed> $eigene */
        $eigene = $json['require'] ?? [];
        $immerDa = ['ext-filter', 'ext-hash', 'ext-pcre'];

        foreach (($lock['packages'] ?? []) as $paket) {
            foreach (array_keys($paket['require'] ?? []) as $verlangt) {
                if (!is_string($verlangt) || !str_starts_with($verlangt, 'ext-')) {
                    continue;
                }

                if (in_array($verlangt, $immerDa, true)) {
                    continue;
                }

                $this->assertArrayHasKey($verlangt, $eigene,
                    $paket['name'] . ' verlangt ' . $verlangt . ', composer.json nennt es nicht. '
                    . 'Ein frisches `composer install` bricht damit auf jedem Server ab, dem '
                    . 'die Erweiterung fehlt — und der Container hier hat sie alle.');
            }
        }
    }

    /** Die Liste der sitzungsfreien Adressen ist die, die der Kommentar beschreibt. */
    public function testDieSitzungsfreienAdressenSindDieDreiWurzeldateienUndApi(): void
    {
        foreach (array_keys(self::WURZELDATEIEN) as $pfad) {
            $this->assertFalse(Sitzung::wirdGebraucht($pfad), $pfad . ' braucht keine Sitzung.');
        }

        $this->assertFalse(Sitzung::wirdGebraucht('/api/zahlung/webhook'),
            'Ein fremder Server hat keine Sitzung und soll keine bekommen.');

        foreach (['/', '/preise', '/briefing', '/portal', '/admin'] as $pfad) {
            $this->assertTrue(Sitzung::wirdGebraucht($pfad), $pfad . ' braucht eine Sitzung.');
        }
    }
}
