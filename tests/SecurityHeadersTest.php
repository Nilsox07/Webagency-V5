<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Helpers\Csrf;
use Sartu\Route;
use Sartu\Router;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Wartungsmodus;

/**
 * Sicherheit auf der Leitung — Portal-Lastenheft §3 Regeln 3 und 11, §1.3.
 *
 * Testfälle: 41 · 47 · 49
 */
final class SecurityHeadersTest extends Datenbankfall
{
    protected function setUp(): void
    {
        parent::setUp();

        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    // ------------------------------------------------------------ Fall 41

    /** Fall 41 — ein POST ohne CSRF-Token wird abgelehnt. Kein Token, keine Ausnahme. */
    public function testPostOhneCsrfTokenWirdAbgelehnt(): void
    {
        $_POST = [];
        $geprueft = 0;

        foreach ($this->router()->routen() as $route) {
            if ($route->methode !== 'POST') {
                continue;
            }

            // Geschuetzte Routen faengt schon die Zugangspruefung ab — sie steht im Router
            // absichtlich VOR der CSRF-Pruefung, damit ein unangemeldeter POST nicht
            // erfaehrt, ob sein Token gueltig war. Fuer die CSRF-Pruefung sind deshalb die
            // offenen Routen der interessante Fall; dass die geschuetzten wirklich
            // abgefangen werden, prueft der Test darunter.
            if (!$route->ohneAnmeldung
                && in_array($route->bereich, [Route::BEREICH_ADMIN, Route::BEREICH_PORTAL], true)) {
                continue;
            }

            // Die Einrichtungsrouten gibt es nur, solange die Einrichtung offen ist —
            // danach liefern sie 404. Geprueft wird deshalb im jeweils gueltigen Zustand.
            $istEinrichtung = str_starts_with($route->pfad, '/admin/setup');
            $antwort = $this->router(gesperrt: !$istEinrichtung)->behandeln('POST', $route->pfad);
            ++$geprueft;

            $this->assertSame(
                419,
                $antwort->status,
                sprintf('Die Route %s hat einen POST ohne Token angenommen.', $route->schluessel())
            );
        }

        $this->assertGreaterThan(0, $geprueft);
    }

    /**
     * Die Kehrseite: Jede geschuetzte POST-Route weist einen unangemeldeten Aufruf ab —
     * ohne die Frage nach dem Token zu beantworten.
     *
     * Ohne diesen Test waere die Ausnahme oben ein Loch: Eine Route, die weder Anmeldung
     * noch Token prueft, faellt durch beide Raster.
     */
    public function testGeschuetztePostRoutenWeisenOhneAnmeldungAb(): void
    {
        $_POST = [];
        $_SESSION = [];
        $geprueft = 0;

        foreach ($this->router(gesperrt: true)->routen() as $route) {
            if ($route->methode !== 'POST' || $route->ohneAnmeldung) {
                continue;
            }

            if (!in_array($route->bereich, [Route::BEREICH_ADMIN, Route::BEREICH_PORTAL], true)) {
                continue;
            }

            $antwort = $this->router(gesperrt: true)->behandeln('POST', $route->pfad);
            ++$geprueft;

            $this->assertSame(
                302,
                $antwort->status,
                sprintf('Die Route %s wurde ohne Anmeldung ausgefuehrt.', $route->schluessel())
            );
            $this->assertSame(
                $route->bereich === Route::BEREICH_ADMIN ? '/admin/anmelden' : '/login',
                $antwort->kopfzeilen['Location'] ?? null,
                $route->schluessel()
            );
        }

        $this->assertGreaterThan(0, $geprueft);
    }

    public function testPostMitFalschemTokenWirdEbenfallsAbgelehnt(): void
    {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        $_POST = [Csrf::FELD => bin2hex(random_bytes(32))];

        $this->assertSame(419, $this->router()->behandeln('POST', '/admin/anmelden')->status);
    }

    public function testPostMitRichtigemTokenLaeuftDurch(): void
    {
        $_POST = [Csrf::FELD => Csrf::token(), 'email' => 'niemand@example.org', 'passwort' => 'falsch'];

        $antwort = $this->router()->behandeln('POST', '/admin/anmelden');

        $this->assertNotSame(419, $antwort->status);
    }

    // ------------------------------------------------------------ Fall 47

    /**
     * Fall 47 — die Sicherheitsheader sind in ALLEN Antworten gesetzt, nicht nur in den
     * erfolgreichen. Geprueft wird deshalb auch die 404-Antwort.
     */
    public function testSicherheitsheaderStehenInJederAntwort(): void
    {
        $erwartet = [
            'Content-Security-Policy',
            'X-Content-Type-Options',
            'Referrer-Policy',
            'X-Frame-Options',
        ];

        $antworten = [
            'GET /admin/anmelden'  => $this->router()->behandeln('GET', '/admin/anmelden'),
            'GET /gibt-es-nicht'   => $this->router()->behandeln('GET', '/gibt-es-nicht'),
            'GET /impressum'       => $this->router()->behandeln('GET', '/impressum'),
            'GET /admin/setup'     => $this->router()->behandeln('GET', '/admin/setup'),
            'POST ohne Token'      => $this->router()->behandeln('POST', '/admin/anmelden'),
        ];

        foreach ($antworten as $bezeichnung => $antwort) {
            foreach ($erwartet as $kopfzeile) {
                $this->assertArrayHasKey(
                    $kopfzeile,
                    $antwort->kopfzeilen,
                    sprintf('%s: die Kopfzeile %s fehlt.', $bezeichnung, $kopfzeile)
                );
            }
        }
    }

    /** §3 Regel 11: Content-Security-Policy ohne unsafe-inline fuer Skripte. */
    public function testCspErlaubtKeineEingebettetenSkripte(): void
    {
        $csp = $this->router()->behandeln('GET', '/admin/anmelden')->kopfzeilen['Content-Security-Policy'];

        $this->assertStringContainsString("script-src 'self'", $csp);
        $this->assertStringNotContainsString('unsafe-inline', $csp);
        $this->assertStringNotContainsString('unsafe-eval', $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
    }

    /**
     * Keine Ausgabe der Anwendung enthaelt ein eingebettetes Skript — sonst braeche die CSP.
     *
     * **Eine Ausnahme, und nur diese eine:** `<script type="application/ld+json">`. Das ist
     * kein Skript, sondern ein Datenblock — der Browser fuehrt ihn nie aus, und `script-src`
     * greift auf ihn nicht zu. Website-Lastenheft §16 verlangt strukturierte Daten, und es
     * gibt keine andere Form, sie auszuliefern.
     *
     * **Der Test wird dadurch schaerfer, nicht weicher.** Bisher pruefte er eine Zeichenkette.
     * Jetzt prueft er zusaetzlich, dass jeder erlaubte Datenblock **gueltiges JSON ohne rohe
     * spitze Klammern** enthaelt — die Stelle, an der ein `</` das `<script>` vorzeitig
     * beenden und den Rest als Markup auf die Seite bringen wuerde. Genau davor schuetzt
     * `JSON_HEX_TAG` in `Strukturdaten`, und genau das wird hier nachgewiesen.
     */
    public function testKeineAnsichtEnthaeltEingebettetesSkript(): void
    {
        $treffer = [];

        $lauf = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(SARTU_WURZEL . '/app/views')
        );

        foreach ($lauf as $datei) {
            if (!$datei instanceof \SplFileInfo || $datei->getExtension() !== 'php') {
                continue;
            }

            // Der eine erlaubte Datenblock faellt raus, jedes andere <script bleibt stehen.
            $inhalt = preg_replace(
                '/<script type="application\/ld\+json">/',
                '',
                (string) file_get_contents($datei->getPathname()),
            ) ?? '';

            if (preg_match('/<script\b/i', $inhalt) === 1 || preg_match('/\son[a-z]+\s*=\s*"/i', $inhalt) === 1) {
                $treffer[] = $datei->getPathname();
            }
        }

        $this->assertSame([], $treffer, 'Eine Ansicht enthält ein eingebettetes Skript oder einen Inline-Handler.');
    }

    /**
     * Der ausgelieferte Datenblock ist gueltiges JSON und enthaelt keine rohe spitze Klammer.
     *
     * Die Gegenprobe zur Ausnahme oben: Waere `JSON_HEX_TAG` in `Strukturdaten` nicht
     * gesetzt, koennte ein Wert das `<script>` beenden. Der Test faehrt die echte Seite an,
     * nicht die Klasse — geprueft wird, was der Browser bekommt.
     */
    public function testDerDatenblockDerStartseiteIstGueltigesJsonOhneMarkup(): void
    {
        $html = (string) $this->router()->behandeln('GET', '/')->rumpf;

        $this->assertSame(
            1,
            preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $treffer),
            'Die Startseite liefert keine strukturierten Daten (§16).',
        );

        $rumpf = $treffer[1];

        $this->assertStringNotContainsString('<', $rumpf, 'Im Datenblock steht eine rohe spitze Klammer.');
        $this->assertStringNotContainsString('>', $rumpf, 'Im Datenblock steht eine rohe spitze Klammer.');
        $this->assertIsArray(json_decode($rumpf, true), 'Der Datenblock ist kein gültiges JSON.');
    }

    // ------------------------------------------------------------ Fall 49

    /**
     * Fall 49 — kein Verzeichnis ausser /public ist ueber den Webserver erreichbar.
     * /app, /storage, /migrations und .env liefern 403 oder 404.
     *
     * Das wird gegen den LAUFENDEN Webserver geprueft, nicht gegen eine Vermutung ueber
     * seine Konfiguration. Ohne Webserver gibt es kein Ergebnis — und dann ist der Test
     * nicht bestanden, sondern nicht gelaufen.
     */
    public function testNurPublicIstUeberDenWebserverErreichbar(): void
    {
        $this->assertTrue(
            $this->webserverAntwortet(),
            'Der Webserver antwortet nicht. Dieser Testfall lässt sich ohne ihn nicht prüfen — '
            . '`docker compose up -d` und erneut laufen lassen.'
        );

        $pfade = [
            '/app/bootstrap.php',
            '/app/routes.php',
            '/app/data/Db.php',
            '/.env',
            '/.env.example',
            '/migrations/001_organizations.sql',
            '/storage/',
            '/composer.json',
            '/vendor/autoload.php',
        ];

        foreach ($pfade as $pfad) {
            $status = $this->statuscode($pfad);

            $this->assertContains(
                $status,
                [403, 404],
                sprintf('%s lieferte %d statt 403 oder 404.', $pfad, $status)
            );
        }
    }

    /** Die Gegenprobe: /public selbst liefert Inhalte aus. */
    public function testOeffentlicheDateienWerdenAusgeliefert(): void
    {
        $this->assertTrue($this->webserverAntwortet(), 'Der Webserver antwortet nicht.');

        $this->assertSame(200, $this->statuscode('/assets/css/tokens.css'));
    }

    /** tokens.css im Auslieferungsverzeichnis ist mit der Quelle identisch (kein Abdriften). */
    public function testAusgelieferteTokensSindMitDerQuelleIdentisch(): void
    {
        $this->assertFileEquals(
            SARTU_WURZEL . '/design/tokens.css',
            SARTU_WURZEL . '/public/assets/css/tokens.css',
            'public/assets/css/tokens.css weicht von design/tokens.css ab. Die Quelle ist design/tokens.css.'
        );
    }

    // ------------------------------------------------------------

    private function router(bool $gesperrt = true): Router
    {
        $datei = $this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME;
        $gesperrt ? touch($datei) : @unlink($datei);

        return new Router(
            require SARTU_WURZEL . '/app/routes.php',
            new InstallationsSperre(new BetreiberdatenSpeicher($this->pdo), $this->arbeitsverzeichnis),
            new Wartungsmodus($this->arbeitsverzeichnis . '/ohne-wartung'),
        );
    }

    private function webserverAntwortet(): bool
    {
        return $this->statuscode('/assets/css/tokens.css') > 0;
    }

    private function statuscode(string $pfad): int
    {
        $k = curl_init('http://127.0.0.1' . $pfad);
        curl_setopt_array($k, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOBODY         => true,
            CURLOPT_TIMEOUT        => 5,
        ]);
        curl_exec($k);
        $status = (int) curl_getinfo($k, CURLINFO_HTTP_CODE);
        curl_close($k);

        return $status;
    }
}
