<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\RechtstexteSpeicher;
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
    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
        putenv('APP_ENV=local');
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
        $geprueft = 0;

        foreach ($this->seiten() as $bezeichnung => $html) {
            preg_match_all('/<h([1-6])[\s>]/i', $html, $treffer);
            $geprueft += count($treffer[1]);

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

        $this->assertGreaterThan(0, $geprueft, 'Es wurde keine Überschrift geprüft.');
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

    /**
     * Jedes Formularfeld traegt seinen eigenen Namen.
     *
     * Dieser Test steht hier wegen eines Fehlers, den keiner der anderen 81 Tests bemerkt
     * hat: `Ansicht::teil()` hiess sein erster Parameter `$name`, und `extract(EXTR_SKIP)`
     * ueberschreibt Vorhandenes nicht — also hiess jedes Feld `components/feld`. Die Seite
     * sah dabei vollkommen richtig aus. Aufgefallen ist es erst im Browser.
     */
    public function testFormularfelderTragenIhrenEigenenNamen(): void
    {
        $html = $this->router(gesperrt: true)->behandeln('GET', '/admin/anmelden')->rumpf;

        $this->assertStringContainsString('name="email"', $html);
        $this->assertStringContainsString('name="passwort"', $html);
        $this->assertStringContainsString('id="feld-email"', $html);
        $this->assertStringContainsString('id="feld-passwort"', $html);

        $this->assertStringNotContainsString(
            'components/feld',
            $html,
            'Der Ansichtspfad ist in ein Feldattribut geraten.'
        );
    }

    /** Jedes Eingabefeld hat eine Beschriftung, die auf seine Kennung zeigt. */
    public function testJedesEingabefeldHatEineBeschriftung(): void
    {
        foreach ($this->seiten() as $bezeichnung => $html) {
            preg_match_all('/<input[^>]*\bid="([^"]+)"/', $html, $felder);
            preg_match_all('/<label[^>]*\bfor="([^"]+)"/', $html, $beschriftungen);

            foreach ($felder[1] as $kennung) {
                if (str_starts_with($kennung, 'feld-')) {
                    $this->assertContains(
                        $kennung,
                        $beschriftungen[1],
                        sprintf('%s: das Feld %s hat keine Beschriftung.', $bezeichnung, $kennung)
                    );
                }
            }
        }
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

        // Zustand 2b: der Bedarfsscheck, Schritt fuer Schritt bis zur Danke-Seite.
        // Er wird hier vollstaendig durchlaufen, weil seine Seiten sonst als einzige
        // oeffentliche Strecke ungeprueft blieben — und er ist der einzige Weg zu einem
        // Angebot (Website-Lastenheft §9.5a).
        foreach ($this->bedarfsscheckSeiten($fertig) as $bezeichnung => $rumpf) {
            $seiten[$bezeichnung] = $rumpf;
        }

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

    /**
     * Der Bedarfsscheck als Seitenfolge — Einstieg, fuenf Themen, Ergebnis, Kontakt, Danke.
     *
     * Der Durchlauf ist derselbe wie in `BedarfsscheckTest`; hier interessiert nicht das
     * Ergebnis, sondern die Auszeichnung jeder einzelnen Seite.
     *
     * @return array<string,string>
     */
    private function bedarfsscheckSeiten(Router $router): array
    {
        $antworten = [
            1 => ['angebot' => 'Wir sanieren Bäder.', 'einsatzort' => '48268', 'bestehende_website' => 'nein'],
            2 => ['hauptziel' => 'anfragen', 'zielgruppe' => 'privatkunden'],
            3 => ['umfangssignale' => [\Sartu\Services\Empfehlung::SIGNAL_HAUPTANGEBOT]],
            4 => ['sonderfunktionen' => [\Sartu\Services\Empfehlung::GATE_FORMULAR]],
            5 => ['domainstatus' => 'vorhanden', 'fester_termin' => 'nein'],
        ];

        $seiten = ['GET /briefing' => $router->behandeln('GET', '/briefing')->rumpf];

        $_POST = [\Sartu\Helpers\Csrf::FELD => \Sartu\Helpers\Csrf::token()];
        $router->behandeln('POST', '/briefing/start');

        foreach ($antworten as $nummer => $eingabe) {
            $seiten['GET /briefing/' . $nummer] = $router->behandeln('GET', '/briefing/' . $nummer)->rumpf;

            $_POST = $eingabe + [\Sartu\Helpers\Csrf::FELD => \Sartu\Helpers\Csrf::token()];
            $router->behandeln('POST', '/briefing/' . $nummer);
        }

        $seiten['GET /briefing/ergebnis'] = $router->behandeln('GET', '/briefing/ergebnis')->rumpf;
        $seiten['GET /briefing/kontakt'] = $router->behandeln('GET', '/briefing/kontakt')->rumpf;

        // Die Danke-Seite erscheint nur nach einem echten Absenden.
        $_SESSION['_bedarfsscheck']['form_started_at'] = (string) (time() - 60);
        $_POST = [
            'first_name' => 'Erika', 'last_name' => 'Mustermann', 'company' => 'Mustermann GmbH',
            'email' => 'erika@example.org', 'preferred_contact' => 'email',
            'b2b_confirmed' => '1', 'privacy_confirmed' => '1',
            \Sartu\Helpers\Csrf::FELD => \Sartu\Helpers\Csrf::token(),
        ];
        $router->behandeln('POST', '/briefing/absenden');
        $_POST = [];

        $seiten['GET /briefing/danke'] = $router->behandeln('GET', '/briefing/danke')->rumpf;

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
