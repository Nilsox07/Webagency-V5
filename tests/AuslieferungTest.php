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
     * **Keine normale öffentliche Seite setzt ein Cookie** — die Gegenprobe über die
     * vollständige Routenliste.
     *
     * ## Was hier bis zum 16.08.2026 stand — und warum es falsch war
     *
     * An dieser Stelle prüfte ein Test, dass die Startseite eine Sitzung **bekommt**. Die
     * Begründung war Portal-Lastenheft §4b.7: `Herkunft::merken()` braucht Landeseite und
     * Kampagnenkennzeichen der ersten aufgerufenen Seite.
     *
     * **Das war eine technische Notwendigkeit, aber keine rechtliche Rechtfertigung.**
     * § 25 Abs. 2 Nr. 2 TDDDG erlaubt die Speicherung auf dem Endgerät ohne Einwilligung
     * nur, „soweit sie unbedingt erforderlich ist, damit der Anbieter … einen vom Nutzer
     * ausdrücklich gewünschten Dienst zur Verfügung stellen kann". Eine Zuordnung, die dem
     * Anbieter nützt und dem Besucher nicht, ist kein solcher Dienst. Die
     * seitenübergreifende Zuordnung ist deshalb aufgegeben; erfasst wird ab dem Einstieg in
     * den Bedarfsscheck.
     *
     * ## Die Ausnahmen sind zwei, und sie stehen hier namentlich
     *
     * | Adresse | Warum sie eine Sitzung bekommt |
     * |---|---|
     * | `/briefing` und die ganze Strecke | Zwischenstand über 24 Stunden und ein CSRF-Token je Formular |
     * | `/kontakt` | zeigt das Rückfrageformular; ohne Sitzung kein Token |
     * | die drei Branchenseiten | §10a bettet den Bedarfsscheck als Formular ein — derselbe Endpunkt, dasselbe Token |
     *
     * `/briefing/ergebnis`, `/briefing/kontakt` und `/briefing/danke` gehören zur Strecke.
     * Sie leiten ohne Zwischenstand auf den Einstieg zurück — und **auch die Umleitung**
     * setzt das Cookie, weil sie ohne Sitzung nicht wüsste, dass es keinen Zwischenstand
     * gibt.
     *
     * Wer eine dritte hinzufügt, trägt sie hier ein **und** begründet sie. Ein stiller
     * Zuwachs ist genau das, was diese Prüfung verhindern soll.
     */
    public function testKeineNormaleOeffentlicheSeiteSetztEinCookie(): void
    {
        $mitSitzung = ['/briefing', '/briefing/ergebnis', '/briefing/kontakt',
            '/briefing/danke', '/kontakt',
            '/website-sanitaer-heizung-klima', '/website-elektrotechnik', '/website-dachdecker'];
        $geprueft = 0;

        foreach ($this->oeffentlicheGetPfade() as $pfad) {
            $kopfzeilen = $this->kopfzeilen($pfad);
            ++$geprueft;

            if (in_array($pfad, $mitSitzung, true)) {
                $this->assertArrayHasKey('set-cookie', $kopfzeilen,
                    $pfad . ' braucht eine Sitzung (Formular mit CSRF-Token) und bekommt keine.');

                continue;
            }

            $this->assertArrayNotHasKey('set-cookie', $kopfzeilen,
                $pfad . ' setzt ein Cookie. § 25 Abs. 2 Nr. 2 TDDDG trägt das nur für einen '
                . 'ausdrücklich gewünschten Dienst — eine Leseseite ist keiner. Wenn die '
                . 'Adresse wirklich eine Sitzung braucht, gehört sie in die Liste oben, mit '
                . 'Grund.');
        }

        $this->assertGreaterThan(20, $geprueft, 'Die Routenliste ist zu klein — wird sie gelesen?');
    }

    /**
     * Das Sitzungscookie trägt die drei Schutzangaben.
     *
     * `HttpOnly` gegen Auslesen per Skript, `SameSite=Lax` gegen das Mitschicken bei fremden
     * Formularen. `Secure` fehlt **lokal absichtlich**: Ohne TLS würde der Browser das
     * Cookie nie senden, und die Anmeldung wäre unbenutzbar. In Produktion setzt
     * `Sitzung::starten()` es.
     */
    public function testDasSitzungscookieIstAbgesichert(): void
    {
        $kopfzeilen = $this->kopfzeilen('/briefing');

        $this->assertArrayHasKey('set-cookie', $kopfzeilen);
        $cookie = $kopfzeilen['set-cookie'][0];

        $this->assertStringContainsString('HttpOnly', $cookie);
        $this->assertStringContainsString('SameSite=Lax', $cookie);
        $this->assertStringStartsWith('PHPSESSID=', $cookie);
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
     * Die Cachepolitik folgt der Sitzung, nicht einer Pfadliste.
     *
     * | Seite | erwartet | warum |
     * |---|---|---|
     * | cookiefreie öffentliche Seite | `public` | Sie trägt nichts Persönliches. Ein Zwischenspeicher darf sie halten — das ist der Gewinn daraus, dass sie cookiefrei ist |
     * | `/briefing`, `/kontakt` | `private` | Sie tragen ein Sitzungscookie und dürfen in keinen **gemeinsamen** Zwischenspeicher |
     */
    public function testDieCachepolitikFolgtDerSitzung(): void
    {
        foreach (['/', '/preise', '/ratgeber'] as $pfad) {
            $wert = $this->eine($pfad, 'cache-control');

            $this->assertStringContainsString('public', $wert, $pfad . ': ' . $wert);
            $this->assertStringNotContainsString('no-store', $wert, $pfad . ': ' . $wert);
        }

        foreach (['/briefing', '/kontakt'] as $pfad) {
            $wert = $this->eine($pfad, 'cache-control');

            $this->assertStringContainsString('private', $wert, $pfad . ': ' . $wert);
        }
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

    /**
     * Jede öffentliche GET-Route ohne Platzhalter — aus `app/routes.php`, nicht aus einer
     * Kopie davon.
     *
     * Eine Kopie liefe auseinander, und dann prüfte der Test sich selbst.
     *
     * @return list<string>
     */
    private function oeffentlicheGetPfade(): array
    {
        /** @var list<\Sartu\Route> $routen */
        $routen = require SARTU_WURZEL . '/app/routes.php';
        $pfade = [];

        foreach ($routen as $route) {
            if ($route->bereich !== \Sartu\Route::BEREICH_OEFFENTLICH || $route->methode !== 'GET') {
                continue;
            }

            if (str_contains($route->pfad, '{')) {
                continue;
            }

            $pfade[] = $route->pfad;
        }

        return $pfade;
    }
}
