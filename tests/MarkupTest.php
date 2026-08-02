<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\RechtstexteSpeicher;
use Sartu\Route;
use Sartu\Router;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Wartungsmodus;

/**
 * Auszeichnung der ausgelieferten Seiten.
 *
 * Testfall 58 — jede Seite hat genau eine <h1>.
 *
 * Geprueft werden die tatsaechlich gerenderten Seiten, nicht die Ansichtsdateien: Eine
 * Ueberschrift kann aus einem Layout, einem Partial oder der Seite kommen. Nur das Ergebnis
 * zaehlt.
 */
final class MarkupTest extends Datenbankfall
{
    private string $arbeitsverzeichnis;

    protected function setUp(): void
    {
        parent::setUp();

        $this->arbeitsverzeichnis = sys_get_temp_dir() . '/sartu-markup-' . bin2hex(random_bytes(4));
        mkdir($this->arbeitsverzeichnis, 0770, true);

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
        putenv('APP_ENV=local');
    }

    protected function tearDown(): void
    {
        @unlink($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);
        @rmdir($this->arbeitsverzeichnis);

        parent::tearDown();
    }

    /** Fall 58 — jede erreichbare GET-Seite hat genau eine <h1>. */
    public function testJedeSeiteHatGenauEineUeberschriftErsterOrdnung(): void
    {
        $geprueft = 0;

        foreach ($this->seiten() as $bezeichnung => $html) {
            $anzahl = preg_match_all('/<h1[\s>]/i', $html);
            ++$geprueft;

            $this->assertSame(1, $anzahl, sprintf('%s hat %d <h1> statt genau einer.', $bezeichnung, $anzahl));
        }

        $this->assertGreaterThanOrEqual(8, $geprueft, 'Es wurden zu wenige Seiten geprüft.');
    }

    /** Die Ueberschriftenhierarchie ist echte Struktur: keine h3 ohne h2 darueber. */
    public function testKeineUebersprungeneUeberschriftenebene(): void
    {
        foreach ($this->seiten() as $bezeichnung => $html) {
            preg_match_all('/<h([1-6])[\s>]/i', $html, $treffer);

            $vorherige = 0;
            foreach ($treffer[1] as $ebene) {
                $ebene = (int) $ebene;

                if ($vorherige > 0) {
                    $this->assertLessThanOrEqual(
                        $vorherige + 1,
                        $ebene,
                        sprintf('%s springt von h%d auf h%d.', $bezeichnung, $vorherige, $ebene)
                    );
                }

                $vorherige = $ebene;
            }
        }
    }

    /** Jede Seite deklariert Deutsch und bindet tokens.css vor dem Bauteil-CSS ein. */
    public function testJedeSeiteIstDeutschUndBindetDieGestaltungswerteZuerstEin(): void
    {
        foreach ($this->seiten() as $bezeichnung => $html) {
            $this->assertStringContainsString('<html lang="de">', $html, $bezeichnung);

            $tokens = strpos($html, '/assets/css/tokens.css');
            $bauteil = strpos($html, '/assets/css/anwendung.css');

            $this->assertIsInt($tokens, $bezeichnung . ': tokens.css fehlt.');
            $this->assertIsInt($bauteil, $bezeichnung . ': anwendung.css fehlt.');
            $this->assertLessThan(
                $bauteil,
                $tokens,
                $bezeichnung . ': tokens.css muss vor jedem Bauteil-CSS stehen.'
            );
        }
    }

    /**
     * Kein Bauteil-CSS enthaelt eine Farbe, einen Radius oder eine Abstandsstufe als Zahl.
     *
     * SARTU_DESIGNSYSTEM.md: „Wer im Bauteil eine Zahl schreibt statt eine Variable, bricht
     * das System." `border-radius:30px` ist ein Abgabefehler.
     */
    public function testBauteilCssBenutztNurVariablen(): void
    {
        $css = (string) file_get_contents(SARTU_WURZEL . '/public/assets/css/anwendung.css');

        // Kommentare raus: die Begruendung nennt die Werte, gegen die die Regel schuetzt.
        $css = (string) preg_replace('#/\*.*?\*/#s', '', $css);

        $verstoesse = [];

        if (preg_match_all('/#[0-9a-f]{3,8}\b/i', $css, $treffer) > 0) {
            $verstoesse[] = 'Farbwert im Bauteil: ' . implode(', ', $treffer[0]);
        }

        if (preg_match_all('/\brgba?\s*\(/i', $css, $treffer) > 0) {
            $verstoesse[] = 'Farbfunktion im Bauteil: ' . implode(', ', $treffer[0]);
        }

        if (preg_match_all('/border-radius\s*:\s*[0-9]/i', $css, $treffer) > 0) {
            $verstoesse[] = 'Radius als Zahl: ' . implode(', ', $treffer[0]);
        }

        $this->assertSame([], $verstoesse);
    }

    /** @return array<string,string> */
    private function seiten(): array
    {
        $seiten = [];

        // Zustand 1: die Einrichtung laeuft — alle acht Schritte sind erreichbar.
        $offen = $this->router(gesperrt: false);
        $seiten['GET /admin/setup (Einrichtung)'] = $offen->behandeln('GET', '/admin/setup')->rumpf;

        // Zustand 2: die Einrichtung ist abgeschlossen.
        $fertig = $this->router(gesperrt: true);

        $seiten['GET /admin/anmelden'] = $fertig->behandeln('GET', '/admin/anmelden')->rumpf;
        $seiten['GET /gibt-es-nicht (404)'] = $fertig->behandeln('GET', '/gibt-es-nicht')->rumpf;
        $seiten['POST ohne Token (419)'] = $fertig->behandeln('POST', '/admin/anmelden')->rumpf;

        $this->rechtstextFreigeben();
        $seiten['GET /impressum'] = $fertig->behandeln('GET', '/impressum')->rumpf;

        // Zustand 3: angemeldet.
        $this->alsAdmin($this->adminAnlegen());
        $this->betreiberdatenAnlegen();

        foreach (['/admin', '/admin/einstellungen/betrieb', '/admin/rechtstexte', '/admin/rechtstexte/impressum', '/admin/testmail'] as $pfad) {
            $seiten['GET ' . $pfad] = $fertig->behandeln('GET', $pfad)->rumpf;
        }

        // Zustand 4: Wartung.
        $wartung = new Router(
            require SARTU_WURZEL . '/app/routes.php',
            new InstallationsSperre(new BetreiberdatenSpeicher($this->pdo), $this->arbeitsverzeichnis),
            $this->wartungAktiv(),
        );
        $seiten['GET /admin (503)'] = $wartung->behandeln('GET', '/admin')->rumpf;

        return $seiten;
    }

    private function router(bool $gesperrt): Router
    {
        $datei = $this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME;

        $gesperrt ? touch($datei) : @unlink($datei);

        return new Router(
            require SARTU_WURZEL . '/app/routes.php',
            new InstallationsSperre(new BetreiberdatenSpeicher($this->pdo), $this->arbeitsverzeichnis),
            new Wartungsmodus($this->arbeitsverzeichnis . '/ohne-wartung'),
        );
    }

    private function wartungAktiv(): Wartungsmodus
    {
        $verzeichnis = $this->arbeitsverzeichnis . '/mit-wartung';
        $wartung = new Wartungsmodus($verzeichnis);
        $wartung->einschalten('Test');

        return $wartung;
    }

    private function rechtstextFreigeben(): void
    {
        $speicher = new RechtstexteSpeicher($this->pdo);
        $speicher->anlegen('impressum', "Platzhalter für den Test.\n\nZweiter Absatz.", 'oeffentlich');
        $speicher->zustandSetzen('impressum', 'freigegeben', 'Testkanzlei');
    }

    private function betreiberdatenAnlegen(): void
    {
        (new BetreiberdatenSpeicher($this->pdo))->anlegen([
            'firmenname'                => 'Vorläufig',
            'strasse'                   => 'Vorläufig 1',
            'plz'                       => '01067',
            'ort'                       => 'Dresden',
            'land'                      => 'DE',
            'email'                     => 'betreiber@example.org',
            'inhaltlich_verantwortlich' => 'Vorläufig',
            'steuernummer'              => '000/000/00000',
        ]);
    }
}
