<?php

declare(strict_types=1);

namespace Sartu\Tests;

use PHPUnit\Framework\TestCase;
use Sartu\Route;
use Sartu\Router;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Launchadressen;
use Sartu\Services\Musterprojekte;

/**
 * **Dieser Test ist unantastbar: nie löschen, nie abschwächen, um grün zu werden.**
 *
 * Er ist das Gegenstück zu `TenantIsolationTest` für die öffentliche Oberfläche. Dort hält
 * die vollständige Routenliste seit A0 jede Sicherheitsregression auf; hier fehlte bis zum
 * 14.08.2026 jedes Gegenstück — mit dem Ergebnis, dass behobene Fehler in vier Runden
 * zurückkamen:
 *
 * | Was zurückkam | Wie oft |
 * |---|---|
 * | Der Geräterahmen wurde gelöscht und wiederhergestellt | zweimal |
 * | Die Tastatur des Geräts wurde nie übernommen | drei Runden |
 * | Die Gattung stand doppelt über der Überschrift | drei Runden |
 * | Eine gemeldete Falschaussage kam in neuem Text zurück | einmal |
 *
 * **Jeder dieser Fehler wäre von einer der neun Prüfungen unten erwischt worden.**
 *
 * ## Wie er läuft
 *
 * Vier Prüfungen lesen das ausgelieferte Markup und brauchen nichts weiter. Fünf brauchen
 * einen **Browser**, weil sie berechnete Layoutwerte prüfen — Überlauf, Höhe,
 * Sprungabstand, Seitenverhältnis und Farbfläche stehen nirgends im HTML. Sie rufen
 * `tools/oberflaeche.mjs` gegen den laufenden Webserver auf.
 *
 * **Fehlt der Webserver oder der Browser, schlägt der Test an — er wird nicht übersprungen.**
 * Dieselbe Entscheidung wie in `SecurityHeadersTest` für Fall 49: Was nicht geprüft werden
 * konnte, gilt nicht als geprüft.
 *
 * ## Wenn eine Grenze reißt
 *
 * **Die Grenze wird nicht angehoben.** Entweder die Ursache wird behoben, oder es kommt eine
 * begründete Zeile daneben — so wie es sie unten für die vier Seiten schon gibt, die über
 * der Regelhöhe für Unterseiten liegen.
 */
final class OberflaecheTest extends TestCase
{
    private const BASIS = 'http://localhost:8080';

    /** Die Breiten, an denen gemessen wird — dieselben wie in jedem Bericht seit dem 13.08.2026. */
    private const BREITEN = [1920, 1440, 1024, 768, 390, 320];

    /**
     * Die vollständige Liste der öffentlichen GET-Adressen ohne Platzhalter.
     *
     * **Kommt eine dazu, die hier nicht steht, schlägt der Test an.** Sie einzutragen ist
     * der Auftrag — nicht, die Prüfung zu lockern. Adressen mit `{platzhalter}` und die
     * Dateiausgaben (`robots.txt`, `sitemap.xml`, `llms.txt`, `/bild/gruender`) stehen
     * nicht hier: Sie liefern kein Seitenlayout und haben nichts zu messen.
     *
     * @var list<string>
     */
    private const SEITEN = [
        '/',
        '/ablauf',
        '/agb',
        '/briefing',
        '/briefing/danke',
        '/briefing/ergebnis',
        '/briefing/kontakt',
        '/datenschutz',
        '/foerderung',
        '/impressum',
        '/kontakt',
        '/leistung-portal',
        '/leistung-seo-lokal',
        '/leistung-texte',
        '/leistung-wartung',
        '/leistung-webdesign',
        '/leistungen',
        '/lexikon',
        '/musterprojekte',
        '/preise',
        '/ratgeber',
        '/ueber-uns',
        '/website-dachdecker',
        '/website-elektrotechnik',
        '/website-sanitaer-heizung-klima',
    ];

    /**
     * Adressen, die `16_SEO_GEO_SARTU.md` als Launch-Adresse führt und die es **noch nicht
     * gibt** — je mit Fundstelle und Grund.
     *
     * **Diese Liste ist eine Schuld, kein Freibrief.** Sie steht hier, damit eine fehlende
     * Seite nicht dasselbe ist wie eine vergessene. `/foerderung` hat sie einen Tag lang
     * geführt: Die Adresse stand seit dem 09.08.2026 mit Priorität 0.9 in der Spezifikation
     * und lieferte 404, ohne dass es je gemeldet worden wäre. Aufgefallen ist es erst, als
     * diese Prüfung die Adressliste gegen die Spezifikation hielt — und behoben am Tag
     * darauf.
     *
     * @var array<string,string>
     */
    private const NOCH_NICHT_GEBAUT = [
        // Leer seit dem 14.08.2026. `/foerderung` stand hier einen Tag lang und ist gebaut:
        // neun Blöcke nach `17_SEITEN_SARTU.md` §6, sechzehn Länder ohne eine einzige Summe.
    ];

    /**
     * Die Obergrenze der Gesamthöhe je Adresse, in Pixeln.
     *
     * **Regel: Unterseiten 6.000 px, Startseite 10.000, mobil bei 390 px höchstens 16.000.**
     * Sieben Adressen liegen darüber und tragen deshalb eine eigene Zeile mit Begründung —
     * genau die Bauform, die der Auftrag verlangt: die Grenze wird nicht angehoben, sie wird
     * begründet.
     *
     * Die Werte liegen rund 8 % über dem Messwert vom 14.08.2026. Eine Seite, die darüber
     * hinauswächst, schlägt an, bevor jemand es beim Ansehen merkt.
     *
     * @var array<string,array{0:int,1:int}> Adresse => [Grenze bei 1440 px, Grenze bei 390 px]
     */
    private const HOEHENGRENZE = [
        // Die Startseite trägt zehn Sektionen — sie hat ihre eigene Grenze aus dem Auftrag.
        '/'                               => [10_000, 16_000],

        // Über der Regelhöhe, je mit Grund:
        // `/musterprojekte` ist die Langfassung von drei Fällen; §4a bindet 700–1.000 Wörter.
        '/musterprojekte'                 => [8_100, 9_600],
        // Die drei Branchenseiten tragen den Vierschritt, acht Blöcke und einen Preisblock.
        '/website-dachdecker'             => [8_600, 12_100],
        '/website-elektrotechnik'         => [8_600, 12_100],
        '/website-sanitaer-heizung-klima' => [8_600, 12_100],
        // `/preise` und `/leistungen` tragen die vollständige Preistabelle bzw. acht Leistungen.
        '/preise'                         => [6_600, 9_300],
        '/leistungen'                     => [6_500, 10_000],
        /*
         * `/foerderung` trägt eine Tabelle über **alle sechzehn** Bundesländer.
         * `17_SEITEN_SARTU.md` §6 Block 5: „Kein Land wird weggelassen" — und die Bedingung
         * je Land ist ausdrücklich der Inhalt, nicht Beiwerk („Die Bedingungen sind sogar
         * der eigentliche Inhalt", `FOERDERUNG_KONZEPT.md` §4a). Sechzehn Zeilen mit je
         * einem Bedingungssatz sind bei 1440 px rund 2.900 px; darunter liegt die Seite
         * nicht, ohne dass die Übersicht ihren Zweck verliert.
         *
         * Gemessen am 14.08.2026: 8.479 px bei 1440, 10.915 px bei 390 — Füllgrad 22
         * beziehungsweise 57 %. Kein waagerechter Überlauf bei keiner der sechs Breiten.
         */
        '/foerderung'                     => [9_200, 11_800],
    ];

    private const REGELHOEHE = [6_000, 16_000];

    /**
     * Adressen, die **ohne Vorbedingung** kein Seitenlayout ausliefern — je mit Grund.
     *
     * Sie bleiben in `SEITEN` und damit in Prüfung 1: Sie sind bekannt, sie sind nicht
     * vergessen. Nur gemessen werden sie nicht, weil es an ihnen nichts zu messen gibt,
     * solange die Vorbedingung fehlt.
     *
     * **Das ist keine Ausnahme vom Prüfen, sondern eine Ausnahme vom Messen.** Wer eine
     * Adresse hier einträgt, um eine gerissene Grenze loszuwerden, umgeht den Test.
     *
     * @var array<string,string>
     */
    private const OHNE_LAYOUT = [
        '/agb'                => 'Liefert 404, solange der Rechtstext `agb` nicht freigegeben '
            . 'ist (`06_RECHT.md`). Der Zustand ist gewollt: Ein Rechtstext im Entwurf geht '
            . 'nicht nach aussen.',
        '/impressum'          => 'Wie `/agb`: liefert 404, solange `legal_texts.impressum` '
            . 'nicht freigegeben ist. Die Startsperre haelt die Veroeffentlichung ohnehin an.',
        '/datenschutz'        => 'Wie `/agb`: liefert 404, solange `legal_texts.datenschutz` '
            . 'nicht freigegeben ist. Die Startsperre haelt die Veroeffentlichung ohnehin an.',
        '/briefing/danke'     => 'Leitet ohne abgeschlossenen Bedarfsscheck auf den Einstieg '
            . 'zurück (303). Ohne Sitzung gibt es die Seite nicht.',
        '/briefing/ergebnis'  => 'Wie `/briefing/danke`: leitet ohne laufende Sitzung auf den '
            . 'Einstieg zurueck. Die Strecke wird von `BedarfsscheckTest` durchlaufen.',
        '/briefing/kontakt'   => 'Wie `/briefing/danke`: leitet ohne laufende Sitzung auf den '
            . 'Einstieg zurueck. Die Strecke wird von `BedarfsscheckTest` durchlaufen.',
    ];

    /**
     * Die Adressen, an denen wirklich gemessen wird.
     *
     * @return list<string>
     */
    private function messbareSeiten(): array
    {
        return array_values(array_filter(
            self::SEITEN,
            static fn (string $pfad): bool => !isset(self::OHNE_LAYOUT[$pfad])
        ));
    }

    /** Rang 1, Farbsystem Fassung 3: Lime ist Fläche für Knöpfe, Badges und kleine Blöcke. */
    private const LIME_HOECHSTFLAECHE = 40_000;

    /** @var array<string,mixed>|null */
    private static ?array $messung = null;

    // ---------------------------------------------------------------- 1 Adressliste

    /**
     * Prüfung 1 — die vollständige Adressliste ist bekannt.
     *
     * Nach dem Muster von `TenantIsolationTest::testRoutenlisteDesKundenbereichsIstVollstaendigBekannt`:
     * Eine öffentliche Seite, die dieser Test nicht kennt, ist eine ungeprüft ausgelieferte
     * Seite.
     */
    public function testDieAdresslisteIstVollstaendigBekannt(): void
    {
        $router = new Router(require SARTU_WURZEL . '/app/routes.php', new InstallationsSperre());

        $gebaut = [];

        foreach ($router->schluessel(Route::BEREICH_OEFFENTLICH) as $schluessel) {
            if (!str_starts_with($schluessel, 'GET ')) {
                continue;
            }

            $pfad = substr($schluessel, 4);

            // Platzhalterrouten und Dateiausgaben liefern kein Seitenlayout.
            if (str_contains($pfad, '{') || str_contains($pfad, '.') || str_starts_with($pfad, '/bild/')) {
                continue;
            }

            $gebaut[] = $pfad;
        }

        sort($gebaut);
        $bekannt = self::SEITEN;
        sort($bekannt);

        $this->assertSame(
            $bekannt,
            $gebaut,
            'Es gibt eine öffentliche Seite, die dieser Test nicht kennt — oder eine, die er '
            . 'kennt und die es nicht mehr gibt. Tragen Sie sie in OberflaecheTest::SEITEN ein '
            . 'und prüfen Sie sie, nicht umgekehrt.'
        );
    }

    /**
     * Prüfung 1b — jede in `16_SEO_GEO_SARTU.md` spezifizierte Launch-Adresse ist gebaut
     * oder als Schuld eingetragen.
     *
     * `/foerderung` steht dort seit dem 09.08.2026 mit Priorität 0.9 und lieferte fünf Tage
     * lang einen 404, ohne dass es je gemeldet wurde. Genau diese Lücke schließt die Prüfung.
     */
    public function testJedeSpezifizierteAdresseIstGebautOderAlsSchuldEingetragen(): void
    {
        $spezifiziert = $this->adressenAusDerSpezifikation();

        $this->assertNotSame([], $spezifiziert,
            'Die Adresstabelle in 16_SEO_GEO_SARTU.md wurde nicht gefunden. Wenn sie umgebaut '
            . 'wurde, muss diese Prüfung mitgezogen werden — nicht entfallen.');

        /*
         * „Gebaut" heisst: Es gibt eine Route. **Nicht:** Sie steht in der Sitemap.
         * `/agb` ist gebaut und steht bewusst nicht in `Launchadressen` — dieselbe Datei
         * fuehrt ihn als „noindex bis final". Der erste Bau dieser Pruefung mass gegen die
         * Sitemap und meldete ihn prompt als fehlend.
         */
        $gebaut = array_flip(array_merge(self::SEITEN, array_keys(Launchadressen::alle())));

        foreach ($spezifiziert as $adresse) {
            if (isset($gebaut[$adresse])) {
                continue;
            }

            $this->assertArrayHasKey(
                $adresse,
                self::NOCH_NICHT_GEBAUT,
                sprintf(
                    '%s steht in 16_SEO_GEO_SARTU.md als Launch-Adresse, ist aber nicht gebaut '
                    . 'und nicht als Schuld eingetragen. Bauen oder mit Fundstelle und Grund in '
                    . 'OberflaecheTest::NOCH_NICHT_GEBAUT aufnehmen.',
                    $adresse
                )
            );
        }
    }

    /** Jede eingetragene Ausnahme trägt einen Grund — sonst ist sie eine stille Ausnahme. */
    public function testJedeSchuldTraegtEineBegruendung(): void
    {
        foreach (self::OHNE_LAYOUT as $adresse => $grund) {
            $this->assertGreaterThan(60, strlen($grund),
                $adresse . ': die Begründung ist zu kurz, um eine zu sein.');
        }

        foreach (self::NOCH_NICHT_GEBAUT as $adresse => $grund) {
            $this->assertMatchesRegularExpression('/\.md/', $grund,
                $adresse . ': die Begründung nennt keine Fundstelle.');
            $this->assertGreaterThan(60, strlen($grund),
                $adresse . ': die Begründung ist zu kurz, um eine zu sein.');
        }
    }

    // ---------------------------------------------------------------- 2 Überlauf

    /** Prüfung 2 — keine Seite läuft an einer der sechs Breiten waagerecht über. */
    public function testKeineSeiteLaeuftWaagerechtUeber(): void
    {
        foreach ($this->messung() as $pfad => $seite) {
            foreach (self::BREITEN as $breite) {
                $m = $seite['breiten'][(string) $breite];

                $this->assertLessThanOrEqual(
                    0,
                    $m['ueberlauf'],
                    sprintf(
                        '%s läuft bei %d px um %d px über (scrollWidth %d). Verursacher: %s',
                        $pfad,
                        $breite,
                        $m['ueberlauf'],
                        $m['scrollWidth'],
                        $this->taeterliste($m['taeter'])
                    )
                );
            }
        }
    }

    // ---------------------------------------------------------------- 3 Höhe

    /** Prüfung 3 — keine Seite wächst über ihre eingetragene Obergrenze. */
    public function testKeineSeiteWaechstUeberIhreObergrenze(): void
    {
        foreach ($this->messung() as $pfad => $seite) {
            [$grenzeGross, $grenzeMobil] = self::HOEHENGRENZE[$pfad] ?? self::REGELHOEHE;

            foreach ([1440 => $grenzeGross, 390 => $grenzeMobil] as $breite => $grenze) {
                $hoehe = $seite['breiten'][(string) $breite]['hoehe'];

                $this->assertLessThanOrEqual(
                    $grenze,
                    $hoehe,
                    sprintf(
                        '%s ist bei %d px %d px hoch, die Grenze steht auf %d. **Die Grenze '
                        . 'wird nicht angehoben.** Entweder die Ursache beheben oder in '
                        . 'OberflaecheTest::HOEHENGRENZE eine begründete Zeile eintragen.',
                        $pfad,
                        $breite,
                        $hoehe,
                        $grenze
                    )
                );
            }
        }
    }

    // ---------------------------------------------------------------- 4 Sprungziele

    /**
     * Prüfung 4 — jedes Sprungziel hält Abstand zur klebenden Kopfzeile.
     *
     * Am 13.08.2026 standen alle sechs Ziele der Hauptnavigation auf `scroll-margin-top: 0`,
     * und der Kopf ist 93 px hoch. Jeder Klick schob die Überschrift dahinter.
     */
    public function testJedesSprungzielHaeltAbstandZurKopfzeile(): void
    {
        $geprueft = 0;

        foreach ($this->messung() as $pfad => $seite) {
            $m = $seite['breiten']['1440'];
            $kopf = (int) $m['kopfhoehe'];

            foreach ($m['sprungziele'] as $ziel) {
                ++$geprueft;

                $this->assertGreaterThan(
                    $kopf,
                    (int) $ziel['abstand'],
                    sprintf(
                        '%s: #%s hat scroll-margin-top %d px, die Kopfzeile ist %d px hoch. '
                        . 'Beim Sprung verschwindet die Überschrift dahinter.',
                        $pfad,
                        $ziel['id'],
                        $ziel['abstand'],
                        $kopf
                    )
                );
            }
        }

        $this->assertGreaterThan(10, $geprueft, 'Es wurden zu wenige Sprungziele geprüft.');
    }

    // ---------------------------------------------------------------- 5 Bildplätze

    /**
     * Prüfung 5 — jeder Bildplatz trägt das Seitenverhältnis seines späteren Bildes.
     *
     * Ohne `aspect-ratio` richtet er sich nach seinem Text: gemessen am 13.08.2026 einer
     * 427 px hoch, wo das Bild 213 px braucht, andere 42 px. **Das Layout springt an jeder
     * dieser Stellen, sobald die Aufnahme kommt.**
     */
    public function testJederBildplatzTraegtEinSeitenverhaeltnis(): void
    {
        $geprueft = 0;

        foreach ($this->messung() as $pfad => $seite) {
            foreach ($seite['breiten']['1440']['bildplaetze'] as $platz) {
                ++$geprueft;

                $this->assertNotSame('auto', $platz['verhaeltnis'],
                    $pfad . ': ein Bildplatz steht auf aspect-ratio: auto und nimmt die Höhe '
                    . 'seines Textes an statt die seines Bildes.');

                $this->assertNotNull($platz['erklaert'],
                    $pfad . ': ein Bildplatz trägt kein data-verhaeltnis. Ohne das Attribut '
                    . 'greift keine Regel in website.css.');

                // Und das erklärte Verhältnis stimmt mit dem gemessenen überein.
                [$b, $h] = array_map('intval', explode('-', (string) $platz['erklaert']));
                $soll = round($platz['breite'] * $h / $b);

                $this->assertEqualsWithDelta($soll, $platz['hoehe'], 2.0,
                    sprintf('%s: ein Bildplatz ist %d px hoch, sein Verhältnis %s verlangt %d.',
                        $pfad, $platz['hoehe'], (string) $platz['erklaert'], $soll));
            }
        }

        $this->assertGreaterThanOrEqual(8, $geprueft, 'Es wurden zu wenige Bildplätze geprüft.');
    }

    // ---------------------------------------------------------------- 6 Satzanfänge

    /**
     * Prüfung 6 — kein Satzanfang öfter als dreimal je Seite.
     *
     * **Beschriftungen und Pflichthinweise sind ausgenommen, und das ist keine Lockerung.**
     * Sie *müssen* identisch bleiben: der Pflichthinweis nach Klasse 1 des Texter-Skills,
     * die wiederkehrenden Beschriftungen nach der Auflage in Klasse 2. Ein Ziel, das sie
     * mitzählt, kann nur erreicht werden, indem eine andere Regel bricht.
     */
    public function testKeinSatzanfangKommtOefterAlsDreimalVor(): void
    {
        foreach ($this->messbareSeiten() as $pfad) {
            $anfaenge = $this->satzanfaenge($this->prosa($this->markup($pfad)));

            foreach ($anfaenge as $wort => $anzahl) {
                $this->assertLessThanOrEqual(
                    3,
                    $anzahl,
                    sprintf('%s: „%s" eröffnet %d Sätze in laufender Prosa. Grenze ist drei.',
                        $pfad, $wort, $anzahl)
                );
            }
        }
    }

    // ---------------------------------------------------------------- 7 Markdown-Reste

    /**
     * Prüfung 7 — kein Auszeichnungsrest im ausgelieferten Text.
     *
     * Doppelte Sternchen und Backticks stammen aus den Spezifikationsdateien; sie sind
     * zweimal in ausgelieferten Text gerutscht, weil ein Satz von dort übernommen wurde.
     * Eckige Klammern sind erlaubt, aber **nur** als Platzhaltermarke — und die steht seit
     * dem 13.08.2026 im Attribut, nicht im Text.
     */
    public function testKeinAuszeichnungsrestImAusgeliefertenText(): void
    {
        foreach ($this->messbareSeiten() as $pfad) {
            $text = $this->nurText($this->markup($pfad));

            $this->assertStringNotContainsString('**', $text, $pfad . ': doppelte Sternchen.');
            $this->assertStringNotContainsString('`', $text, $pfad . ': Backtick.');
            $this->assertStringNotContainsString('[[', $text,
                $pfad . ': eine Platzhaltermarke steht im sichtbaren Text statt im Attribut.');
            $this->assertDoesNotMatchRegularExpression('/\[[^\]]{1,60}\]\(/', $text,
                $pfad . ': ein Markdown-Link im Text.');
        }
    }

    // ---------------------------------------------------------------- 8 Grammatik

    /**
     * Prüfung 8 — keine grammatisch zusammengebaute Nennung.
     *
     * `Musterprojekte::bildsatz()` setzte bis zum 14.08.2026
     * `sprintf('Später die Startseite dieses %s.', $gattung)` und erzeugte damit sechs
     * falsche Sätze: *dieses Malerbetrieb*, *dieses Physiotherapiepraxis*, *dieses
     * Arbeitsrechtskanzlei*. Ein Artikel vor einem Substantiv unbekannten Geschlechts ist
     * keine Formatierung, sondern geratene Grammatik.
     *
     * Der Satz steht jetzt je Projekt in den Daten. Diese Prüfung hält beides fest: dass er
     * dort steht **und** dass er die Gattung nicht wiederholt, die einen Zeilenabstand
     * darunter als Überschrift steht.
     */
    public function testKeineZusammengebauteNennung(): void
    {
        $quelle = (string) file_get_contents(SARTU_WURZEL . '/app/services/Musterprojekte.php');

        $this->assertDoesNotMatchRegularExpression(
            '/sprintf\([^)]*(dieses|dieser|diese|des|der|dem)\s+%s/u',
            $quelle,
            'Ein Artikel wird per sprintf vor ein Substantiv gesetzt, dessen Geschlecht der '
            . 'Code nicht kennt. Der ganze Satz gehört in die Daten.'
        );

        foreach (Musterprojekte::alle() as $schluessel => $projekt) {
            $satz = Musterprojekte::bildsatz($schluessel);

            $this->assertNotSame('', $satz, $schluessel . ': kein Bildsatz in den Daten.');
            $this->assertStringNotContainsString(
                (string) $projekt['gattung'],
                $satz,
                $schluessel . ': der Bildsatz wiederholt die Gattung, die direkt darunter als '
                . 'Überschrift steht.'
            );
        }

        // Und die Gattung steht auf beiden Seiten genau einmal als eigener Textknoten.
        foreach (['/', Musterprojekte::PFAD] as $pfad) {
            $html = $this->markup($pfad);

            foreach (Musterprojekte::alle() as $projekt) {
                $this->assertSame(1, substr_count($html, '>' . $projekt['gattung'] . '<'),
                    $pfad . ': „' . $projekt['gattung'] . '" steht mehr als einmal.');
            }
        }
    }

    // ---------------------------------------------------------------- 10 Das Gerät

    /**
     * Prüfung 10 — das Gerät im Aufmacher hat eine aufgeklappte Basis.
     *
     * `design/geraet.html` nennt sie als **grössten Einzeleffekt**: „Fehlt sie, sieht das
     * Gerät aus wie ein Bildschirm auf einem Stiel — genau daran ist die erste Fassung
     * gescheitert."
     *
     * Der Rahmen ist in vier Runden zweimal gelöscht und wiederhergestellt worden, und die
     * Tastatur wurde nie übernommen. Diese Prüfung hält beides fest — samt dem Vermerk, der
     * an den Rand des Geräts gehört und nicht frei darunter.
     */
    public function testDasGeraetHatEineAufgeklappteBasis(): void
    {
        $html = $this->markup('/');

        $this->assertStringContainsString('class="geraet"', $html,
            'Der Geräterahmen fehlt. §4b (Rang 1) hält ihn fest, bis das gerenderte Mockup '
            . 'vorliegt — er wurde in vier Runden zweimal gelöscht.');

        // Die vier Teile der Basis, je an ihrem Kommentar im Markup.
        foreach ([
            'aufgeklappte Basis' => 'Ohne sie steht der Schirm auf einem Stiel.',
            'Tastenfeld'         => 'Fünf Reihen zu vierzehn Tasten.',
            'Leertaste'          => 'Sie sitzt vor der untersten Reihe.',
            'Trackpad'           => 'Mittig unter dem Tastenfeld, 30 % breit.',
            'Vorderkante'        => 'Mit Griffmulde, als eigene Fläche unter der Basis.',
        ] as $teil => $wozu) {
            $this->assertStringContainsString($teil, $html,
                'Am Gerät fehlt: ' . $teil . '. ' . $wozu);
        }

        // Das Tastenfeld ist gezeichnet, nicht angedeutet: 5 × 14 Tasten plus Leertaste.
        $this->assertGreaterThanOrEqual(
            71,
            substr_count($html, 'var(--geraet-taste)'),
            'Das Tastenfeld hat weniger Tasten als die fünf Reihen zu vierzehn, die '
            . 'design/geraet.html zeichnet.'
        );

        /*
         * Das Trackpad steht mittig — nachgerechnet, nicht nach Augenmaß.
         *
         * Die Basis ist ein Trapez (hinten 92…832, vorn 10…918); ihre Mittellinie läuft
         * deshalb von x=462 nach x=464 und **nicht** senkrecht. Der erste Bau setzte das
         * Trackpad bei 30…54 % der Breite — sichtbar links der Mitte, mit der Begründung,
         * das angeschnittene Telefon verdecke die rechte Vorderfläche. Nachgerechnet
         * stimmte das nicht: Das Telefon beginnt bei x=756, die rechte Kante des mittigen
         * Trackpads liegt bei x≈575. `design/geraet.html` setzt `margin: 16px auto 0`.
         */
        $this->assertSame(1, preg_match(
            '#<path d="M([\d.]+) [\d.]+ L([\d.]+) [\d.]+[^"]*"\s+fill="var\(--geraet-trackpad\)"#',
            $html,
            $pfad
        ), 'Das Trackpad ist kein gezeichneter Pfad mehr.');

        $mitte = ((float) $pfad[1] + (float) $pfad[2]) / 2;

        $this->assertEqualsWithDelta(463.0, $mitte, 6.0,
            'Das Trackpad steht nicht mehr mittig auf der Basis (Oberkante bei x=' . $mitte
            . ', erwartet 463). design/geraet.html setzt `margin: 16px auto 0`; links der '
            . 'Mitte liest es sich als Zeichenfehler, nicht als Perspektive.');

        // Der Vermerk sitzt am Gerät, nicht frei darunter.
        $css = (string) file_get_contents(SARTU_WURZEL . '/public/assets/css/website.css');

        $this->assertSame(1, preg_match('#\.geraet__marke \{(.*?)\}#s', $css, $regel));
        $this->assertStringContainsString('position: absolute', $regel[1],
            'Der Vermerk `Musteransicht` schwebt frei unter dem Gerät. Er gehört an dessen '
            . 'Rand — design/geraet.html setzt ihn absolut an die obere linke Ecke.');
    }

    // ---------------------------------------------------------------- 9 Lime

    /**
     * Prüfung 9 — keine vollflächige Lime-Bahn.
     *
     * `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §3, Farbsystem Fassung 3 (Rang 1): Lime bleibt auf
     * Knöpfe, Badges, Textmarker und kleine Blöcke. Am 13.08.2026 stand ein Feld von
     * 1.268 × 476 px in `--lime-soft` auf der Startseite — 603.568 Quadratpixel.
     */
    public function testKeineVollflaechigeLimeBahn(): void
    {
        foreach ($this->messung() as $pfad => $seite) {
            foreach ($seite['breiten']['1440']['limeflaechen'] as $flaeche) {
                $this->assertLessThanOrEqual(
                    self::LIME_HOECHSTFLAECHE,
                    $flaeche['flaeche'],
                    sprintf(
                        '%s: %s trägt %d Quadratpixel Lime. Grenze ist %d — Lime bleibt auf '
                        . 'Knöpfe, Badges, Textmarker und kleine Blöcke.',
                        $pfad,
                        $flaeche['wer'],
                        $flaeche['flaeche'],
                        self::LIME_HOECHSTFLAECHE
                    )
                );
            }
        }
    }

    // ---------------------------------------------------------------- Werkzeug

    /**
     * Die Browsermessung, einmal je Testlauf.
     *
     * Sie kostet rund eine Minute für 24 Seiten an sechs Breiten. Ein Aufruf je Prüfung
     * wären neun Minuten — deshalb statisch zwischengespeichert.
     *
     * @return array<string,mixed>
     */
    private function messung(): array
    {
        if (self::$messung !== null) {
            return self::$messung;
        }

        $befehl = sprintf(
            'node %s %s %s 2>&1',
            escapeshellarg(SARTU_WURZEL . '/tools/oberflaeche.mjs'),
            escapeshellarg(self::BASIS),
            implode(' ', array_map('escapeshellarg', $this->messbareSeiten()))
        );

        exec($befehl, $zeilen, $status);
        $ausgabe = implode("\n", $zeilen);

        $this->assertSame(
            0,
            $status,
            "Die Oberflächenmessung ist nicht gelaufen. Was nicht geprüft werden konnte, gilt "
            . "nicht als geprüft.\n"
            . "Voraussetzungen: der Webserver antwortet unter " . self::BASIS . ", `node` und "
            . "Playwright sind erreichbar (NODE_PATH), Chromium liegt unter SARTU_CHROMIUM.\n"
            . "Ausgabe:\n" . substr($ausgabe, 0, 600)
        );

        $daten = json_decode($ausgabe, true);

        $this->assertIsArray($daten, 'Die Messung lieferte kein JSON: ' . substr($ausgabe, 0, 300));

        foreach ($this->messbareSeiten() as $pfad) {
            $this->assertArrayHasKey($pfad, $daten, $pfad . ' fehlt in der Messung.');
            $this->assertSame(200, $daten[$pfad]['status'],
                $pfad . ' antwortet mit ' . $daten[$pfad]['status'] . ' statt 200.');
        }

        return self::$messung = $daten;
    }

    /** Das ausgelieferte Markup einer Adresse. */
    private function markup(string $pfad): string
    {
        static $zwischenlager = [];

        if (isset($zwischenlager[$pfad])) {
            return $zwischenlager[$pfad];
        }

        /*
         * `ignore_errors` ist noetig: Ohne den Schalter liefert `file_get_contents` bei jedem
         * Status ausser 2xx ein `false`, und der Test meldete „Webserver antwortet nicht" fuer
         * eine Seite, die einwandfrei mit 404 antwortet. Der Unterschied zwischen „kein Server"
         * und „Seite gibt es nicht" muss im Test sichtbar bleiben.
         */
        $rumpf = @file_get_contents(self::BASIS . $pfad, false, stream_context_create([
            'http' => ['ignore_errors' => true, 'timeout' => 10],
        ]));

        $this->assertIsString($rumpf,
            'Der Webserver unter ' . self::BASIS . ' antwortet nicht. Ohne ihn lässt sich die '
            . 'Oberfläche nicht prüfen — und ungeprüft gilt sie nicht als geprüft.');

        return $zwischenlager[$pfad] = $rumpf;
    }

    /** Nur der `<main>`-Bereich als Klartext. */
    private function nurText(string $html): string
    {
        $treffer = [];

        if (preg_match('#<main.*?</main>#s', $html, $treffer) === 1) {
            $html = $treffer[0];
        }

        $html = (string) preg_replace('#<(script|style|svg)\b.*?</\1>#s', ' ', $html);

        return html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Klartext **ohne** Beschriftungen, Knöpfe, Überschriften und Listenpunkte.
     *
     * Das sind die Stellen, die identisch bleiben müssen — siehe Kopf von Prüfung 6.
     */
    private function prosa(string $html): string
    {
        $treffer = [];

        if (preg_match('#<main.*?</main>#s', $html, $treffer) === 1) {
            $html = $treffer[0];
        }

        $ohne = [
            '#<(dt|button|summary|h[1-6])\b.*?</\1>#is',
            '#<a\b[^>]*class="[^"]*knopf[^"]*".*?</a>#is',
            '#<p\b[^>]*class="[^"]*(vorzeile|marke|marken|preishinweis|preisrahmen'
                . '|bildplatz__kennung|musterkarte__umfang|stufe__kicker|kicker)[^"]*".*?</p>#is',
            '#<li\b[^>]*>.*?</li>#is',
            '#<(script|style|svg)\b.*?</\1>#is',
        ];

        $html = (string) preg_replace($ohne, ' ', $html);
        $html = (string) preg_replace('#</(p|h[1-6]|li|dd|dt|div|section|figcaption|a|article)>#i', '. ', $html);

        return html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Wie oft eröffnet welches Wort einen Satz?
     *
     * @return array<string,int>
     */
    private function satzanfaenge(string $text): array
    {
        $anfaenge = [];

        foreach (preg_split('/(?<=[.!?:])\s+/u', $text) ?: [] as $satz) {
            $satz = trim($satz, " .·—-\t\n\r");
            $woerter = [];

            preg_match_all('/[\p{L}\p{N}][\p{L}\p{N}€%.,–-]*/u', $satz, $woerter);

            if (count($woerter[0]) < 2) {
                continue;
            }

            $erstes = trim($woerter[0][0], '.,');
            $anfaenge[$erstes] = ($anfaenge[$erstes] ?? 0) + 1;
        }

        return $anfaenge;
    }

    /**
     * Die Launch-Adressen, wie `16_SEO_GEO_SARTU.md` sie führt.
     *
     * Gelesen wird die Tabelle, nicht eine Kopie davon: Eine Kopie im Test würde
     * auseinanderlaufen, und dann prüfte er sich selbst.
     *
     * @return list<string>
     */
    private function adressenAusDerSpezifikation(): array
    {
        $datei = (string) file_get_contents(SARTU_WURZEL . '/spezifikation/16_SEO_GEO_SARTU.md');
        $treffer = [];

        /*
         * Nur Zeilen, die **eine** Adresse nennen. Die Tabelle fuehrt auch Bereiche —
         * „`/briefing/1` … `/briefing/n` — die Schritte" — und Sammelzeilen wie „die **fuenf**
         * `/leistung-*`". Beides sind keine Adressen, und der erste Bau dieses Tests hat
         * `/briefing/1` prompt als fehlende Seite gemeldet.
         */
        preg_match_all('/^\|\s*`(\/[a-z0-9\/-]*)`\s*(?:\||,|\s—)/mu', $datei, $treffer);

        return array_values(array_filter(
            array_unique($treffer[1]),
            static fn (string $a): bool => !str_contains($a, '*')
        ));
    }

    /** @param list<array{wer:string,ueber:int,geclippt:bool}> $taeter */
    private function taeterliste(array $taeter): string
    {
        $offen = array_filter($taeter, static fn (array $t): bool => !$t['geclippt']);

        if ($offen === []) {
            return 'keiner ungeclippt — der Überlauf entsteht weiter oben';
        }

        return implode(' · ', array_map(
            static fn (array $t): string => $t['wer'] . ' +' . $t['ueber'] . ' px',
            $offen
        ));
    }
}
