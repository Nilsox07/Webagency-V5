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

        // **Null ist erlaubt, jede andere Zahl nicht.**
        //
        // `0` ist kein Wert aus der Radienskala, sondern deren Abwesenheit — und die verlangt
        // der abgenommene Entwurf ausdruecklich: „Aussenkante rechtwinklig. Der Bereich fuellt
        // das Fenster; eine Rundung aussen gaebe es nicht zu sehen."
        // (`design/PORTAL_DESIGNSTAND.md`). Die Leiste steht deshalb auf
        // `border-radius: 0 var(--r-l) var(--r-l) 0`.
        //
        // Es gibt keine Variable fuer „keine Rundung", und eine zu erfinden hiesse, eine achte
        // Form neben die sieben zu stellen — genau das verbietet `tokens.css`.
        if (preg_match_all('/border-radius\s*:\s*[^;}]*/i', $css, $treffer) > 0) {
            foreach ($treffer[0] as $regel) {
                $werte = preg_split('/\s+/', trim(substr($regel, strpos($regel, ':') + 1))) ?: [];

                foreach ($werte as $wert) {
                    if ($wert !== '' && $wert !== '0' && !str_starts_with($wert, 'var(')) {
                        $verstoesse[] = 'Radius als Zahl: ' . trim($regel);

                        break 2;
                    }
                }
            }
        }

        $this->assertSame([], $verstoesse);
    }


    /**
     * Beide Bereiche tragen die **Seitenleiste**, nicht das alte waagerechte Band.
     *
     * Der Betreiber hat sie am 03.08.2026 entschieden; gebaut wurde sie am 10.08.2026. Dieser
     * Test haelt fest, dass sie bleibt — samt der drei Eigenschaften, an denen der Entwurf
     * haengt: dunkle Leiste links, Gruppen mit Zeichen, und die **rechtwinklige Aussenkante**
     * bei gerundeter Innenkante.
     */
    public function testBeideBereicheTragenDieSeitenleiste(): void
    {
        $css = (string) file_get_contents(SARTU_WURZEL . '/public/assets/css/anwendung.css');

        // Die Huelle: Leiste links, Flaeche rechts.
        $this->assertStringContainsString('.bereich {', $css);
        $this->assertStringContainsString('grid-template-columns: 17rem 1fr', $css);

        // Aussen rechtwinklig, innen gerundet — `design/PORTAL_DESIGNSTAND.md`.
        $this->assertStringContainsString('border-radius: 0 var(--r-l) var(--r-l) 0', $css);

        // Das alte Band gibt es nicht mehr. Beide Namen sind verschwunden, nicht nur einer.
        $this->assertStringNotContainsString('.kopfband', $css);
        $this->assertStringNotContainsString('.kundenband', $css);

        // Die Leiste des internen Bereichs steht auf --ink-2. Bis zum 10.08.2026 war der Wert
        // definiert und wurde von keiner Regel benutzt — genau daran war zu sehen, dass es die
        // Leiste nicht gab.
        $this->assertStringContainsString('.rail--intern { background: var(--ink-2); }', $css);
    }

    /**
     * Die 25 Zeichen stehen einmal je Seite und werden nur ueber `<use>` eingebunden.
     *
     * Entschieden am 10.08.2026 (`OFFENE_ENTSCHEIDUNGEN.md`, Punkt 9). Geprueft wird die
     * **Zahl**: Wer ein sechsundzwanzigstes braucht, zeichnet es im Konzept und uebertraegt es
     * — nicht umgekehrt.
     */
    public function testDieZeichenStehenVollstaendigUndNurEinmal(): void
    {
        $sprite = (string) file_get_contents(SARTU_WURZEL . '/app/views/partials/zeichen.php');

        $this->assertSame(25, preg_match_all('/<symbol id="i-[a-z0-9-]+"/', $sprite));

        // Jedes Zeichen, das eine Leiste benutzt, muss es auch geben.
        foreach (['kundenband', 'kopfband'] as $leiste) {
            $quelle = (string) file_get_contents(SARTU_WURZEL . '/app/views/partials/' . $leiste . '.php');

            preg_match_all('/#(i-[a-z0-9-]+)/', $quelle, $benutzt);

            foreach (array_unique($benutzt[1]) as $zeichen) {
                $this->assertStringContainsString(
                    '<symbol id="' . $zeichen . '"',
                    $sprite,
                    sprintf('%s benutzt das Zeichen %s, das es im Sprite nicht gibt.', $leiste, $zeichen),
                );
            }
        }
    }

    /**
     * Das Logo wird eingebunden — nicht nur ausgeliefert.
     *
     * Bis zum 10.08.2026 lagen die drei SVG unter `public/assets/bild/`, und **keine Ansicht
     * band sie ein**; Kopf und Fuss setzten das Wort als Text. `07_MARKE_UND_GESTALTUNG.md`
     * verlangt in der Kopfleiste Zeichen + Wortmarke.
     */
    public function testDasLogoStehtInKopfUndFuss(): void
    {
        $kopf = (string) file_get_contents(SARTU_WURZEL . '/app/views/partials/websiteband.php');
        $fuss = (string) file_get_contents(SARTU_WURZEL . '/app/views/partials/websitefuss.php');

        // Helle Fassung auf hellen Grund, dunkle auf dunklen.
        $this->assertStringContainsString('/assets/bild/sartu-logo-hell.svg', $kopf);
        $this->assertStringContainsString('/assets/bild/sartu-logo-dunkel.svg', $fuss);

        // Die Bildbeschreibung nennt die Marke, nicht das Wort „Logo".
        $this->assertStringContainsString('alt="SARTU"', $kopf);
        $this->assertStringNotContainsString('alt="Logo"', $kopf);
    }


    /**
     * Der Aufmacher trägt die neun Merkmale des abgenommenen Entwurfs.
     *
     * Am 10.08.2026 wurde aus „`tokens.css` byteweise gleich **und** Abschnittsfolge gleich"
     * geschlossen, die Startseite sei übertragen. **Der Schluss war falsch** — der Aufmacher
     * wich an neun Stellen ab. Dieser Test prüft die neun einzeln, damit derselbe Fehlschluss
     * nicht ein zweites Mal möglich ist.
     */
    /**
     * Der Geraeterahmen und sein Tausch gegen das gerenderte Mockup.
     *
     * ## Der Stand, den dieser Test festhaelt
     *
     * `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4b (Rang 1, 10.08.2026): „Im Aufmacher steht ein
     * **Gerät in leichter Schrägstellung** — Laptop mit angeschnittenem Telefon davor."
     *
     * Die Anweisung vom 13.08.2026 will den gezeichneten Rahmen durch
     * `geraet-aufmacher.webp` ersetzen und danach loeschen. **Die Datei liegt nicht im
     * Repository**, und dieselbe Anweisung regelt den Fall: „Liegt die Datei nicht im Repo,
     * ueberspring diesen Block und melde es."
     *
     * Am 13.08.2026 war der Rahmen einmal geloescht und ist am selben Tag wiederhergestellt
     * worden. **Ueberspringen heisst nicht tun, bis die Datei da ist** — nicht: die
     * Entscheidung vom 10.08.2026 zuruecknehmen. Ein Aufmacher ohne Geraet ist eine
     * Abweichung vom abgenommenen Entwurf, und dafuer gibt es keine Entscheidung.
     *
     * ## Was der Test prueft
     *
     * Beide Zustaende, damit weder der Rahmen verschwindet noch der Tausch vergessen wird.
     * Zwei Dinge gelten in **jedem** Zustand: die echte Aufnahme bleibt der Inhalt (§4b),
     * und der Vermerk steht neben dem Bild, nicht darauf.
     */
    public function testDerGeraeterahmenWirdGetauschtSobaldDasMockupVorliegt(): void
    {
        $mockup = SARTU_WURZEL . '/public/assets/bild/geraet-aufmacher.webp';
        $css = (string) file_get_contents(SARTU_WURZEL . '/public/assets/css/website.css');
        $tokens = (string) file_get_contents(SARTU_WURZEL . '/public/assets/css/tokens.css');
        $bild = (string) file_get_contents(SARTU_WURZEL . '/app/views/partials/aufmacherbild.php');

        // 1 — Der CSS-Block ist geloescht und bleibt geloescht. Anweisung vom 13.08.2026:
        //     „loesch den toten CSS-Block, statt ihn liegen zu lassen."
        foreach (['.geraet__laptop', '.geraet__deckel', '.geraet__sockel', '.geraet__telefon'] as $regel) {
            $this->assertStringNotContainsString($regel . ' {', $css,
                $regel . ' steht wieder in website.css. Das Geraet wird im Inline-SVG gezeichnet, '
                . 'nicht hier — Anweisung vom 13.08.2026.');
        }

        // 2 — Der Vermerk steht **neben** dem Bild, nicht darauf. Gilt in jedem Zustand.
        $this->assertStringContainsString('<figcaption class="geraet__marke">', $bild);
        $this->assertSame(1, preg_match('#\.geraet__marke \{(.*?)\}#s', $css, $treffer));
        $this->assertStringNotContainsString('position: absolute', $treffer[1],
            'Der Vermerk liegt wieder auf dem Bild. Angewiesen ist „neben dem Bild, nicht darauf".');

        if (!is_file($mockup)) {
            // 3 — Ohne Mockup steht das Geraet als Inline-SVG. §4b (Rang 1) verlangt es:
            //     „ein Geraet in leichter Schraegstellung — Laptop mit angeschnittenem
            //     Telefon davor", und nennt Inline-SVG ausdruecklich als Mittel.
            $this->assertStringContainsString('<svg class="geraet__bild"', $bild,
                'Ohne Mockup zeichnet das Inline-SVG das Geraet. §4b haelt es fest, bis die '
                . 'gerenderte Datei vorliegt.');

            // Zwei Bildschirme: Laptop und angeschnittenes Telefon — beide mit der echten
            // Aufnahme, kein nachgezeichneter Inhalt (§8).
            $this->assertSame(2, substr_count($bild, '<image href='),
                'Das Geraet traegt Laptop **und** angeschnittenes Telefon (§4b).');
            $this->assertStringContainsString('sartu-kundenbereich-muster.webp', $bild);

            // Die Grauwerte kommen aus tokens.css, auch im SVG — keine Zahl im Bauteil.
            foreach (['--geraet-rahmen', '--geraet-sockel', '--geraet-kerbe'] as $wert) {
                $this->assertStringContainsString('var(' . $wert . ')', $bild,
                    $wert . ' steht nicht mehr im SVG — dann steht dort eine Zahl.');
                $this->assertStringContainsString($wert . ':', $tokens,
                    $wert . ' fehlt in tokens.css, wird aber vom SVG gebraucht.');
            }

            // Kein externer Abruf: jede Adresse im SVG zeigt auf das eigene Verzeichnis.
            $this->assertSame(0, preg_match('#href="https?://#', $bild),
                'Das SVG ruft eine fremde Adresse ab. §1 verbietet jede externe Verbindung.');

            return;
        }

        // 4 — Liegt das Mockup, faellt auch das SVG: Rahmen und Aufnahme kommen dann aus
        //     einer Datei, und zwei Fassungen desselben Geraets waeren zwei Wahrheiten.
        $this->assertStringNotContainsString('<svg class="geraet__bild"', $bild,
            'Das Mockup liegt vor, und das SVG zeichnet das Geraet trotzdem noch.');
        $this->assertStringContainsString('geraet-aufmacher.webp', $bild,
            'Das Mockup liegt vor und wird nicht eingebunden.');

        foreach (['--geraet-rahmen:', '--geraet-sockel:', '--geraet-kerbe:'] as $wert) {
            $this->assertStringNotContainsString($wert, $tokens,
                $wert . ' steht noch in tokens.css, wird aber von nichts mehr gebraucht.');
        }
    }

    public function testDerAufmacherTraegtDieNeunMerkmaleDesEntwurfs(): void
    {
        $seite = (string) file_get_contents(SARTU_WURZEL . '/app/views/pages/website-start.php');
        $bild  = (string) file_get_contents(SARTU_WURZEL . '/app/views/partials/aufmacherbild.php');
        $block = (string) file_get_contents(SARTU_WURZEL . '/app/views/partials/handlungsblock.php');
        $kopf  = (string) file_get_contents(SARTU_WURZEL . '/app/views/partials/websiteband.php');
        $css   = (string) file_get_contents(SARTU_WURZEL . '/public/assets/css/website.css');

        // 1 — Gerät statt Bildplatz, mit echter Aufnahme und gebundenem Vermerk.
        $this->assertStringContainsString("partials/aufmacherbild", $seite);
        $this->assertStringNotContainsString('sartu-portal-cockpit-muster', $seite);
        $this->assertStringContainsString('sartu-kundenbereich-muster.webp', $bild);
        $this->assertStringContainsString('Websitetexte::MUSTERANSICHT', $seite);
        $this->assertFileExists(SARTU_WURZEL . '/public/assets/bild/sartu-kundenbereich-muster.webp');

        // 2 — Der Lime-Akzent kann nur existieren, wenn die H1 zweiteilig ist.
        $this->assertTrue(
            \Sartu\Services\Startseitentexte::h1Vollstaendig(),
            'Die beiden H1-Teile ergeben nicht mehr den gebundenen Wortlaut.',
        );
        $this->assertStringContainsString('class="akzent"', $seite);
        $this->assertStringContainsString('.akzent {', $css);

        // 3 — Lime als Textmarker, kein Unterstrich.
        $this->assertStringContainsString('main a:not(.knopf)', $css);
        $this->assertMatchesRegularExpression('/main a:not\(\.knopf\).*?background-image: linear-gradient\(var\(--lime\)/s', $css);

        // 4 — Pfeile an beiden Knöpfen.
        $this->assertSame(2, substr_count($block, 'class="pfeil"'));

        // 5 — Die Bänder, und sie tragen nichts: aria-hidden, kein Text.
        $this->assertStringContainsString('class="baender" aria-hidden="true"', $seite);
        $this->assertSame(3, substr_count($seite, 'class="band band--'));

        // 6 — Die Trennlinie über der Vertrauensliste.
        $this->assertStringContainsString('class="aufmacher__leiste"', $seite);
        $this->assertMatchesRegularExpression('/\.aufmacher__leiste \{[^}]*border-top: 1px solid var\(--line\)/s', $css);

        // 7 — Halbgeviertstrich statt Mittelpunkt.
        $this->assertStringNotContainsString('content: "· ";', $css);
        $this->assertMatchesRegularExpression('/\.vertrauenszeile li::before \{[^}]*content: "";/s', $css);

        // 8 — Das Logo wird eingebunden (siehe auch testDasLogoStehtInKopfUndFuss).
        $this->assertStringContainsString('sartu-logo-hell.svg', $kopf);

        // 9 — Die Navigation bricht nicht um.
        $this->assertMatchesRegularExpression('/\.hauptnavigation ul \{[^}]*flex-wrap: nowrap/s', $css);
    }

    /**
     * Die Bänder sind Zierde — sie tragen weder Text noch eine Handlung.
     *
     * `CLAUDE.md`: „Alle Kernabläufe funktionieren mit **deaktiviertem JavaScript**." Die
     * Bänder sind reines CSS und damit ohnehin unabhängig davon; dieser Test hält fest, dass
     * in ihnen auch nichts steht, das jemand bräuchte.
     */
    public function testDieBaenderTragenNichts(): void
    {
        $seite = (string) file_get_contents(SARTU_WURZEL . '/app/views/pages/website-start.php');

        $this->assertSame(1, preg_match('#<div class="baender"[^>]*>(.*?)</div>#s', $seite, $treffer));

        $inhalt = $treffer[1];

        // Nur die drei Wogen mit ihren Bändern — kein Text, kein Link, kein Knopf.
        $this->assertSame('', trim(strip_tags($inhalt)));
        $this->assertStringNotContainsString('<a ', $inhalt);
        $this->assertStringNotContainsString('<button', $inhalt);
    }

    /**
     * Keine Auszeichnungssprache im ausgelieferten Text.
     *
     * Am 13.08.2026 standen drei Stellen so da: `**woraus**` in einem Ratgeberabsatz und
     * zweimal Schrägstriche um eine Domain im Lexikon. Nichts davon wird irgendwo
     * ausgewertet — es erschien als Zeichen im Fließtext **und** in den Beschreibungen für
     * Suchmaschinen, weil dieselben Felder beides speisen.
     *
     * Geprüft werden die Textquellen selbst und nicht die fertige Seite: In der fertigen
     * Seite steht auch Markup, das dort hingehört.
     */
    public function testKeineAuszeichnungsspracheImAusgeliefertenText(): void
    {
        $quellen = glob(SARTU_WURZEL . '/app/services/*.php') ?: [];
        $geprueft = 0;

        foreach ($quellen as $datei) {
            $zeilen = explode("\n", (string) file_get_contents($datei));
            $imKommentar = false;

            foreach ($zeilen as $nummer => $zeile) {
                $sauber = trim($zeile);

                if (str_starts_with($sauber, '/*')) {
                    $imKommentar = true;
                }

                if ($imKommentar) {
                    if (str_contains($sauber, '*/')) {
                        $imKommentar = false;
                    }
                    continue;
                }

                if ($sauber === '' || str_starts_with($sauber, '*') || str_starts_with($sauber, '//')) {
                    continue;
                }

                // Nur Zeilen mit einer Zeichenkette — Auszeichnung im Quelltext selbst
                // (etwa ein regulaerer Ausdruck) ist kein ausgelieferter Text.
                if (!str_contains($zeile, "'")) {
                    continue;
                }

                $geprueft++;

                foreach (['**' => 'Fettung als Sternchen', '`' => 'Schrägstriche um ein Wort'] as $zeichen => $was) {
                    $this->assertStringNotContainsString(
                        $zeichen,
                        $zeile,
                        basename($datei) . ':' . ($nummer + 1) . ' trägt ' . $was
                            . ' in einer Zeichenkette. Das wird nirgends ausgewertet und '
                            . 'landet als Zeichen im Fliesstext.',
                    );
                }
            }
        }

        $this->assertGreaterThan(500, $geprueft, 'Der Test hat fast nichts gelesen — die Auswahl stimmt nicht.');
    }

    /**
     * §16 verlangt `/favicon.ico` samt PNG-Groessen und das Open-Graph-Bild.
     *
     * Beides fehlte bis zum 13.08.2026 vollstaendig — die Dateien **und** die Verweise.
     * Geprueft wird deshalb beides: dass die Dateien liegen und dass jede Seite sie nennt.
     */
    public function testJedeSeiteTraegtBildmarkeUndVorschaukarte(): void
    {
        foreach (['/favicon.ico', '/assets/bild/sartu-favicon-16.png', '/assets/bild/sartu-favicon-32.png',
                  '/assets/bild/sartu-favicon-180.png', '/assets/bild/sartu-og.png'] as $datei) {
            $this->assertFileExists(SARTU_WURZEL . '/public' . $datei);
        }

        foreach (['/', '/preise', '/ratgeber', '/lexikon', '/kontakt'] as $pfad) {
            $html = (string) $this->router(gesperrt: true)->behandeln('GET', $pfad)->rumpf;

            $this->assertStringContainsString('rel="icon" href="/favicon.ico"', $html, $pfad);
            $this->assertStringContainsString('rel="apple-touch-icon"', $html, $pfad);
            $this->assertStringContainsString('property="og:image"', $html, $pfad);
            $this->assertStringContainsString('name="twitter:card" content="summary_large_image"', $html, $pfad);

            // Die Karte traegt denselben Titel wie das Dokument — nicht eine zweite Fassung.
            $this->assertSame(1, preg_match('~<title>(.*?)</title>~', $html, $titel), $pfad);
            $this->assertStringContainsString(
                'property="og:title" content="' . $titel[1] . '"',
                $html,
                $pfad . ': og:title weicht vom <title> ab.',
            );
        }

        // Auch die geschlossenen Bereiche tragen das Zeichen — sonst ist der Tabstreifen leer.
        $anmeldung = (string) $this->router(gesperrt: true)->behandeln('GET', '/admin/anmelden')->rumpf;
        $this->assertStringContainsString('rel="icon" href="/favicon.ico"', $anmeldung);
    }

    /**
     * Das Menuezeichen behauptet nicht im offenen Zustand, es sei zu oeffnen.
     *
     * Bis zum 13.08.2026 stand `aria-label="Menü öffnen"` fest am `summary`. Ein
     * `<details>` kennt seinen Zustand ohne Skript nicht; ein Name, der eine Handlung
     * nennt, ist deshalb in genau der Haelfte der Faelle falsch. Der Zustand kommt vom
     * Browser ueber `aria-expanded` — der Name muss ihn nicht tragen.
     */
    public function testDasMenuezeichenBehauptetKeinenZustand(): void
    {
        $band = (string) file_get_contents(SARTU_WURZEL . '/app/views/partials/websiteband.php');

        $this->assertSame(1, preg_match('#<summary[^>]*>#', $band, $treffer));
        $this->assertStringNotContainsString('aria-label', $treffer[0],
            'Der Name des Menuezeichens darf keine Handlung nennen — er gilt in beiden Zustaenden.');

        $html = (string) $this->router(gesperrt: true)->behandeln('GET', '/')->rumpf;
        $this->assertStringNotContainsString('Menü öffnen', $html);
    }

    /**
     * Die drei Branchenseiten haben eingehende Verweise.
     *
     * Am 13.08.2026 hatten sie null — sie standen nur in der Sitemap. Eine Seite, auf die
     * nichts zeigt, wird als unwichtig gelesen, und der Test faellt sonst nicht auf: Sie
     * antwortet ja mit 200.
     */
    public function testJedeBranchenseiteHatEingehendeVerweise(): void
    {
        $quellen = ['/leistungen'];
        $treffer = [];

        foreach ($quellen as $quelle) {
            $html = (string) $this->router(gesperrt: true)->behandeln('GET', $quelle)->rumpf;

            foreach (array_keys(\Sartu\Services\Branchenseiten::alle()) as $schluessel) {
                $ziel = \Sartu\Services\Branchenseiten::pfad($schluessel);

                if (str_contains($html, 'href="' . $ziel . '"')) {
                    $treffer[$ziel] = true;
                }
            }
        }

        foreach (array_keys(\Sartu\Services\Branchenseiten::alle()) as $schluessel) {
            $ziel = \Sartu\Services\Branchenseiten::pfad($schluessel);

            $this->assertArrayHasKey($ziel, $treffer,
                'Auf ' . $ziel . ' zeigt kein interner Verweis. Sie steht dann nur in der Sitemap.');
        }
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
        $this->betreiberdatenVorlaeufigAnlegen();

        // Die Anfrageliste einmal leer und einmal gefuellt: Der Leerzustand ist eine eigene
        // Seite mit eigenem Text (§0.3b) und faellt sonst durch jede Pruefung.
        $seiten['GET /admin/anfragen (leer)'] = $fertig->behandeln('GET', '/admin/anfragen')->rumpf;
        $anfrageId = $this->anfrageAnlegen();

        foreach (['/admin', '/admin/einstellungen/betrieb', '/admin/rechtstexte', '/admin/rechtstexte/impressum',
                  '/admin/testmail', '/admin/anfragen', '/admin/anfragen/' . $anfrageId] as $pfad) {
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

    /** Eine Anfrage, damit die Detailansicht etwas anzuzeigen hat. */
    private function anfrageAnlegen(): string
    {
        $ergebnis = (new \Sartu\Services\AnfrageService(
            null,
            new \Sartu\Services\Ratenbegrenzung($this->arbeitsverzeichnis),
        ))->anlegen([
            'submission_id'      => \Sartu\Data\Uuid::v4(),
            'form_started_at'    => (string) (time() - 60),
            'first_name'         => 'Erika',
            'last_name'          => 'Mustermann',
            'company'            => 'Mustermann Sanitär GmbH',
            'email'              => 'erika@example.org',
            'preferred_contact'  => 'email',
            'b2b_confirmed'      => '1',
            'privacy_confirmed'  => '1',
            'angebot'            => 'Wir sanieren Bäder.',
            'einsatzort'         => '48431',
            'bestehende_website' => 'nein',
            'hauptziel'          => 'anfragen',
            'zielgruppe'         => 'privatkunden',
            'umfangssignale'     => ['hauptangebot'],
            'sonderfunktionen'   => ['formular'],
            'domainstatus'       => 'vorhanden',
            'fester_termin'      => 'nein',
        ], [], '203.0.113.9');

        return (string) $ergebnis->anfrageId;
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

    private function betreiberdatenVorlaeufigAnlegen(): void
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
