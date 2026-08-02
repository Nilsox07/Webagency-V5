<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Helpers\Csrf;
use Sartu\Router;
use Sartu\Services\Branchenseiten;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Kontaktanfrage;
use Sartu\Services\Launchadressen;
use Sartu\Services\Preise;
use Sartu\Services\Wartungsmodus;

/**
 * Die öffentliche Website — Website-Lastenheft §17, „Definition of Done".
 *
 * Geprüft wird, was sich prüfen lässt, **ohne einen Browser**. Was einen echten Browser oder
 * eine Messung braucht — Laborwerte, Kontrast, Tastaturdurchlauf, mobiles Menü mit
 * Fokusfalle — steht in `OFFENE_PRUEFUNGEN.md` mit Prüfmittel und Zeitpunkt.
 *
 * **Was nicht ausgeführt wurde, wird nicht als geprüft gemeldet.**
 */
final class WebsiteTest extends Datenbankfall
{
    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
        $_POST = [];
        $_GET = [];

        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);
    }

    // ---------------------------------------------------------------- §17 Technik und SEO

    /** Jede Launch-Adresse antwortet mit 200. */
    public function testJedeLaunchadresseAntwortet(): void
    {
        foreach (array_keys(Launchadressen::alle()) as $pfad) {
            // `/impressum` und `/datenschutz` liefern 404, solange der Rechtstext auf
            // `entwurf` steht. Das ist §14a und kein Fehler dieser Seite.
            if (in_array($pfad, ['/impressum', '/datenschutz'], true)) {
                continue;
            }

            $this->assertSame(200, $this->router()->behandeln('GET', $pfad)->status, $pfad);
        }
    }

    /** Jede Seite: genau eine H1, eigener Titel, eigene Beschreibung, Canonical auf sich selbst. */
    public function testJedeSeiteHatEineH1EinenEigenenTitelUndEinCanonical(): void
    {
        $titel = [];
        $beschreibungen = [];

        foreach ($this->seiten() as $pfad => $html) {
            $this->assertSame(1, substr_count($html, '<h1'), $pfad . ' hat nicht genau eine H1.');

            preg_match('#<title>(.*?)</title>#s', $html, $treffer);
            $this->assertNotEmpty($treffer[1] ?? '', $pfad . ' hat keinen Titel.');

            preg_match('#<meta name="description" content="(.*?)">#s', $html, $beschreibung);
            $this->assertNotEmpty($beschreibung[1] ?? '', $pfad . ' hat keine Beschreibung.');

            $this->assertArrayNotHasKey($treffer[1], $titel, 'Zwei Seiten mit gleichem Titel: ' . $pfad);
            $this->assertArrayNotHasKey($beschreibung[1], $beschreibungen,
                'Zwei Seiten mit gleicher Beschreibung: ' . $pfad);

            $titel[$treffer[1]] = $pfad;
            $beschreibungen[$beschreibung[1]] = $pfad;

            $this->assertStringContainsString(
                '<link rel="canonical" href="http://localhost:8080' . ($pfad === '/' ? '/' : $pfad) . '">',
                $html,
                $pfad . ' hat kein Canonical auf sich selbst.',
            );
        }
    }

    /** §17: keine toten internen Verweise. */
    public function testKeinInternerVerweisFuehrtInsLeere(): void
    {
        $geprueft = [];

        foreach ($this->seiten() as $pfad => $html) {
            preg_match_all('~href="(/[^"#?]*)~', $html, $treffer);

            foreach (array_unique($treffer[1]) as $ziel) {
                if (isset($geprueft[$ziel]) || str_starts_with($ziel, '/assets/')) {
                    continue;
                }

                $geprueft[$ziel] = true;
                $status = $this->router()->behandeln('GET', $ziel)->status;

                $this->assertContains(
                    $status,
                    [200, 302, 303, 404],
                    $ziel . ' antwortet mit ' . $status . ' (verwiesen von ' . $pfad . ').',
                );

                // 404 ist nur fuer die zwei Rechtstexte zulaessig — sie stehen auf `entwurf`.
                if ($status === 404) {
                    $this->assertContains($ziel, ['/impressum', '/datenschutz'],
                        'Toter Verweis auf ' . $ziel . ' (von ' . $pfad . ').');
                }
            }
        }
    }

    /** §17: Die Sitemap enthält nur 200er-Adressen und keine `noindex`-Seite. */
    public function testDieSitemapEnthaeltKeineNoindexSeite(): void
    {
        $sitemap = (string) $this->router()->behandeln('GET', '/sitemap.xml')->rumpf;

        preg_match_all('#<loc>http://localhost:8080(/[^<]*)</loc>#', $sitemap, $treffer);

        $this->assertNotEmpty($treffer[1]);

        foreach ($treffer[1] as $pfad) {
            if (in_array($pfad, ['/impressum', '/datenschutz'], true)) {
                continue;
            }

            $antwort = $this->router()->behandeln('GET', $pfad);

            $this->assertSame(200, $antwort->status, $pfad . ' steht in der Sitemap und antwortet nicht.');
            $this->assertStringNotContainsString(
                'name="robots" content="noindex',
                (string) $antwort->rumpf,
                $pfad . ' steht in der Sitemap und traegt noindex (§14a Bedingung 3).',
            );
        }
    }

    /** §16: Die Schritte des Bedarfsschecks stehen nicht in der Sitemap. */
    public function testDieBedarfsscheckschritteStehenNichtInDerSitemap(): void
    {
        $sitemap = (string) $this->router()->behandeln('GET', '/sitemap.xml')->rumpf;

        $this->assertStringContainsString('/briefing<', $sitemap);
        $this->assertStringNotContainsString('/briefing/1', $sitemap);
    }

    // ---------------------------------------------------------------- §17 Inhalt und Aussagen

    /**
     * §17: Keine verbotenen Wörter aus §2 auffindbar.
     *
     * Geprüft wird der **sichtbare** Text, nicht das Markup: Ein Wort in einem Klassennamen
     * ist kein Wort, das jemand liest.
     */
    public function testKeinVerbotenesWortStehtAufEinerSeite(): void
    {
        // §2, Verbotstabelle. `wartungsarm` und `rechtssicher` nennt §17 als Suchbegriffe.
        $verboten = [
            'wartungsarm', 'wartungsfrei', 'kaum Wartung',
            'rechtssicher', 'abmahnsicher', 'DSGVO-konform',
            'garantiert Platz', 'garantierte Sichtbarkeit', 'garantierte KI-Nennung',
            'Paket wählen', 'konfigurieren', 'Extras hinzufügen', 'SEO buchen',
            'günstig', 'Schnäppchen',
            'unser Team',
        ];

        // §11 gibt „keine Billig-Seitenschleuder" woertlich vor — eine Abgrenzung, keine
        // Positionierung. Die Stelle faellt deshalb vor der Suche heraus, und `billig` bleibt
        // ueberall sonst verboten. Andersherum — das Wort ganz aus der Liste nehmen — waere
        // eine Abschwaechung.
        $gebunden = ['Billig-Seitenschleuder'];

        foreach ($this->seiten() as $pfad => $html) {
            $text = str_replace($gebunden, '', strip_tags($html));

            foreach ([...$verboten, 'billig'] as $wort) {
                $this->assertStringNotContainsStringIgnoringCase(
                    $wort,
                    $text,
                    $pfad . ' enthält das verbotene Wort „' . $wort . '" (§2).',
                );
            }
        }
    }

    /** §2 und §17: Der Preishinweis steht auf jeder preisführenden Seite. */
    public function testDerPreishinweisStehtAufJederPreisfuehrendenSeite(): void
    {
        $hinweis = 'Alle Preise netto zzgl. gesetzlicher Umsatzsteuer. Ausschließlich für Unternehmer.';

        foreach ($this->seiten() as $pfad => $html) {
            $text = strip_tags($html);

            // Preisführend ist eine Seite, auf der ein Eurobetrag im Text steht.
            if (preg_match('/\d{1,3}(\.\d{3})?,\d{2}\s*€/u', $text) !== 1) {
                continue;
            }

            $this->assertStringContainsString($hinweis, $text, $pfad . ' nennt Preise ohne den Pflichthinweis.');
        }
    }

    /** §17: Kein Paket ist direkt kaufbar, und Platzhirsch ist sichtbar die Empfehlung. */
    public function testPlatzhirschIstDieEmpfehlungUndNichtsIstDirektKaufbar(): void
    {
        $html = (string) $this->router()->behandeln('GET', '/preise')->rumpf;

        $this->assertStringContainsString('Empfehlung', strip_tags($html));

        foreach (['In den Warenkorb', 'Jetzt kaufen', 'Jetzt buchen', 'Paket wählen'] as $verboten) {
            $this->assertStringNotContainsString($verboten, $html, '/preise: ' . $verboten);
        }
    }

    /** §17: keine erfundenen Referenzen — Portalansichten tragen den Vermerk „Musteransicht". */
    public function testJedeMusteransichtIstAlsSolcheGekennzeichnet(): void
    {
        foreach ($this->seiten() as $pfad => $html) {
            preg_match_all('/sartu-portal-[a-z-]+/', $html, $treffer);

            if ($treffer[0] === []) {
                continue;
            }

            $this->assertStringContainsString('Musteransicht', $html,
                $pfad . ' zeigt eine Portalansicht ohne den Vermerk „Musteransicht".');
        }
    }

    // ---------------------------------------------------------------- §0 Ortssperre

    /**
     * §0 und §17: Kein Ortsname erscheint, solange `[GESCHAEFTSADRESSE_STATUS]` offen ist.
     *
     * Geprüft wird der **ganze** ausgelieferte Text, nicht nur Titel und H1. §0 nennt
     * ausdrücklich auch den Fließtext.
     */
    public function testKeinOrtsnameStehtAufDerWebsite(): void
    {
        // Die Orte aus SARTU_ENTSCHEIDUNGEN_OFFEN.md §1, Einzugsgebiet.
        $orte = ['Dresden', 'Meißen', 'Radebeul', 'Coswig', 'Radeberg', 'Pirna', 'Heidenau',
                 'Freital', 'Dippoldiswalde', 'Bischofswerda', 'Bautzen', 'Sebnitz', 'Sachsen'];

        foreach ($this->seiten() as $pfad => $html) {
            foreach ($orte as $ort) {
                $this->assertStringNotContainsString(
                    $ort,
                    $html,
                    $pfad . ' nennt „' . $ort . '", obwohl die Standortentscheidung offen ist (§0).',
                );
            }
        }
    }

    /** §17: keine Ortsseite in der produktiven Veröffentlichung, auch nicht unverlinkt. */
    public function testEsGibtKeineOrtsseite(): void
    {
        foreach (['/webdesign-dresden', '/webdesign-meissen', '/webdesign-pirna'] as $pfad) {
            $this->assertSame(404, $this->router()->behandeln('GET', $pfad)->status, $pfad);
        }
    }

    // ---------------------------------------------------------------- §10a Branchenseiten

    /**
     * §10a, Prüfung 2: Mindestens 400 Wörter je Branchenseite stehen auf keiner anderen.
     *
     * Gemessen an den eigenen Blöcken — Probleme, Was gehört drauf, Was beachten, Beispiel,
     * Fragen. Die geteilten Blöcke (Preise, Ablauf, Konfigurator) zählen nicht mit; sie
     * stehen auf jeder Seite und sind genau deshalb kein Eigenanteil.
     */
    public function testJedeBranchenseiteHatMindestensVierhundertEigeneWoerter(): void
    {
        $texte = [];

        foreach (Branchenseiten::alle() as $schluessel => $seite) {
            $eigen = [(string) $seite['kurz']];

            foreach ($seite['probleme'] as $punkt) {
                $eigen[] = $punkt['titel'] . ' ' . $punkt['text'];
            }

            foreach ($seite['beachten'] as $punkt) {
                $eigen[] = $punkt['titel'] . ' ' . $punkt['text'];
            }

            foreach ($seite['fragen'] as $punkt) {
                $eigen[] = $punkt['frage'] . ' ' . $punkt['antwort'];
            }

            $eigen[] = implode(' ', $seite['gehoert_drauf']);
            $eigen[] = $seite['beispiel']['titel'] . ' ' . $seite['beispiel']['text'];

            $texte[$schluessel] = implode(' ', $eigen);

            $this->assertGreaterThanOrEqual(
                400,
                str_word_count($texte[$schluessel], 0, 'äöüÄÖÜßéèà'),
                $schluessel . ' hat weniger als 400 eigene Wörter (§10a, Prüfung 2).',
            );
        }

        // Prüfung 2, zweite Hälfte: Die eigenen Blöcke dürfen sich nicht gegenseitig sein.
        foreach ($texte as $schluessel => $text) {
            foreach ($texte as $anderer => $andererText) {
                if ($schluessel === $anderer) {
                    continue;
                }

                $this->assertNotSame($text, $andererText, $schluessel . ' und ' . $anderer . ' sind gleich.');
            }
        }
    }

    /**
     * §10a, Prüfung 1 — der Austauschtest, maschinell so weit er geht.
     *
     * Vollständig prüfen kann ihn nur ein Mensch: „Ergibt der Text weiterhin Sinn?" ist
     * keine Zeichenkettenfrage. Was sich prüfen lässt: Jede Seite nennt Begriffe, die auf
     * keiner anderen Branchenseite vorkommen.
     */
    public function testJedeBranchenseiteNenntEigeneFachbegriffe(): void
    {
        $eigen = [
            'sanitaer-heizung-klima' => ['Badsanierung', 'Heizungstausch', 'Wärmepumpe'],
            'elektrotechnik'         => ['Photovoltaik', 'Ladepunkte', 'Zählerschrank'],
            'dachdecker'             => ['Steildach', 'Flachdach', 'Dachaufbau'],
        ];

        foreach ($eigen as $schluessel => $begriffe) {
            $html = (string) $this->router()->behandeln('GET', '/website-' . $schluessel)->rumpf;

            foreach ($begriffe as $begriff) {
                $this->assertStringContainsString($begriff, $html, $schluessel . ': ' . $begriff);
            }

            foreach ($eigen as $andererSchluessel => $andereBegriffe) {
                if ($andererSchluessel === $schluessel) {
                    continue;
                }

                foreach ($andereBegriffe as $fremd) {
                    $this->assertStringNotContainsString(
                        $fremd,
                        $html,
                        $schluessel . ' nennt „' . $fremd . '" — der Austauschtest greift (§10a).',
                    );
                }
            }
        }
    }

    /** §10a, Prüfung 3: Zu jeder Branchenseite gehört ein Herkunftsnachweis. */
    public function testJedeBranchenseiteHatEinenHerkunftsnachweis(): void
    {
        foreach (Branchenseiten::alle() as $schluessel => $seite) {
            $this->assertNotEmpty($seite['quellen'], $schluessel . ' hat keinen Herkunftsnachweis.');
            $this->assertLessThanOrEqual(3, count($seite['zahlen']),
                $schluessel . ' nennt mehr als drei Statistiken (SARTU_BRANCHENFAKTEN.md Abschnitt 1).');

            foreach ($seite['zahlen'] as $zahl) {
                $this->assertNotSame('', trim($zahl['quelle']),
                    $schluessel . ': eine Zahl ohne Quelle.');
            }
        }
    }

    /** §10a: Der Konfigurator ist eingebettet und nutzt denselben Endpunkt. */
    public function testDerKonfiguratorAufDerBranchenseiteNutztDenselbenEndpunkt(): void
    {
        $html = (string) $this->router()->behandeln('GET', '/website-dachdecker')->rumpf;

        $this->assertStringContainsString('action="/briefing/start"', $html);
        $this->assertStringContainsString('name="branche_vorbelegt" value="dachdecker"', $html);
    }

    // ---------------------------------------------------------------- Preise

    /** §11a, technische Pflicht: Preise stehen an einer Stelle und stimmen überall überein. */
    public function testJedePreisangabeStammtAusDerPreistabelle(): void
    {
        $erwartet = [];

        foreach (Preise::tabelle() as $zeile) {
            $erwartet[] = number_format($zeile['einmalig_cent'] / 100, 2, ',', '.') . ' €';
        }

        $html = strip_tags((string) $this->router()->behandeln('GET', '/preise')->rumpf);

        foreach ($erwartet as $betrag) {
            $this->assertStringContainsString($betrag, $html, '/preise nennt ' . $betrag . ' nicht.');
        }

        // Gegenprobe: Auf keiner Seite steht ein Eurobetrag, den die Preistabelle nicht kennt.
        $bekannt = $erwartet;

        foreach (Preise::tabelle() as $zeile) {
            foreach (['einmalig_cent', 'monatlich_cent', 'erstes_jahr_cent'] as $feld) {
                // Zwei Schreibweisen derselben Zahl: `1.490,00 €` im Fliesstext (§4a) und
                // `1.490 €` in Titel und Beschreibung, wo §7 den Wortlaut bindet. Beide
                // werden aus der Tabelle erzeugt, keine getippt.
                $bekannt[] = number_format($zeile[$feld] / 100, 2, ',', '.') . ' €';
                $bekannt[] = number_format($zeile[$feld] / 100, 0, ',', '.') . ' €';
            }
        }

        // Die Domaingrenze aus §7 Sektion 6 steht als Zahl im Text und ist keine Paketzahl.
        $bekannt[] = '30 €';

        foreach ($this->seiten() as $pfad => $seite) {
            preg_match_all('/\d{1,3}(?:\.\d{3})?(?:,\d{2})?\s?€/u', strip_tags($seite), $treffer);

            foreach (array_unique($treffer[0]) as $betrag) {
                $this->assertContains(
                    trim($betrag),
                    $bekannt,
                    $pfad . ' nennt ' . $betrag . ' — der Betrag steht nicht in der Preistabelle.',
                );
            }
        }
    }

    // ---------------------------------------------------------------- §11 Kontaktformular

    /**
     * §17: „Beide Formulare senden nachweislich."
     *
     * Der Bedarfsscheck ist in `BedarfsscheckTest` geprüft. Hier das zweite.
     */
    public function testDasKontaktformularLegtEinenDatensatzAn(): void
    {
        $ergebnis = (new Kontaktanfrage())->anlegen($this->kontakteingabe(), [], '127.0.0.1');

        $this->assertTrue($ergebnis->dankeSeite);
        $this->assertNotNull($ergebnis->anfrageId);

        $zeile = $this->pdo->query('SELECT * FROM leads')->fetch(\PDO::FETCH_ASSOC);

        $this->assertIsArray($zeile);
        $this->assertSame('Erika Mustermann', (string) $zeile['first_name']);
        $this->assertSame('mustermann-sanitaer.example', (string) $zeile['company']);
        // §11 kennt keinen Bedarfsscheck — also gibt es keine Empfehlung.
        $this->assertNull($zeile['recommended_package']);
        $this->assertSame('standard', (string) $zeile['flag']);

        $inhalt = json_decode((string) $zeile['payload'], true);

        $this->assertSame('kontakt', $inhalt['formular']);
        $this->assertSame('domain', $inhalt['anliegen']);
        $this->assertStringContainsString('Heizung', $inhalt['nachricht']);
    }

    /** §11: die Nachricht braucht mindestens 20 Zeichen, mit dem Wortlaut aus §11. */
    public function testEineZuKurzeNachrichtWirdAmFeldAbgewiesen(): void
    {
        $eingabe = $this->kontakteingabe();
        $eingabe['nachricht'] = 'Zu kurz.';

        $ergebnis = (new Kontaktanfrage())->anlegen($eingabe, [], '127.0.0.1');

        $this->assertFalse($ergebnis->dankeSeite);
        $this->assertSame(
            'Bitte beschreiben Sie Ihr Anliegen in ein bis zwei Sätzen.',
            $ergebnis->feldfehler['nachricht'] ?? null,
        );
        $this->assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn());
    }

    /**
     * §17: Honigtopf, Zeitregel und Doppeleinreichung greifen — und der Absender sieht
     * trotzdem die normale Bestätigung.
     */
    public function testHonigtopfZeitregelUndDoppeleinreichungGreifenStill(): void
    {
        $mitHonig = $this->kontakteingabe();
        $mitHonig['hp_website'] = 'https://spam.example';

        $ergebnis = (new Kontaktanfrage())->anlegen($mitHonig, [], '127.0.0.1');
        $this->assertTrue($ergebnis->dankeSeite, 'Der Absender sieht nicht die Bestätigungsseite.');
        $this->assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn());

        $zuSchnell = $this->kontakteingabe();
        $zuSchnell['form_started_at'] = (string) time();

        $this->assertTrue((new Kontaktanfrage())->anlegen($zuSchnell, [], '127.0.0.1')->dankeSeite);
        $this->assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn());

        // Doppeleinreichung: dieselbe `submission_id` zweimal ergibt einen Datensatz.
        $eingabe = $this->kontakteingabe();

        (new Kontaktanfrage())->anlegen($eingabe, [], '127.0.0.1');
        (new Kontaktanfrage())->anlegen($eingabe, [], '127.0.0.1');

        $this->assertSame(1, (int) $this->pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn());
    }

    /** §11 und §2: ohne die beiden Bestätigungen wird nichts gespeichert. */
    public function testOhneBestaetigungenWirdNichtsGespeichert(): void
    {
        foreach (['b2b_confirmed', 'privacy_confirmed'] as $feld) {
            $eingabe = $this->kontakteingabe();
            unset($eingabe[$feld]);

            $ergebnis = (new Kontaktanfrage())->anlegen($eingabe, [], '127.0.0.1');

            $this->assertFalse($ergebnis->dankeSeite, $feld);
            $this->assertArrayHasKey($feld, $ergebnis->feldfehler);
        }

        $this->assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn());
    }

    /** §11: das Formular läuft über die Route, mit CSRF und ohne JavaScript. */
    public function testDasFormularLaeuftUeberDieRouteUndBrauchtEinCsrfFeld(): void
    {
        $html = (string) $this->router()->behandeln('GET', '/kontakt')->rumpf;

        $this->assertStringContainsString('action="/kontakt"', $html);
        $this->assertStringContainsString(Csrf::FELD, $html);
        $this->assertSame(0, preg_match('/\son[a-z]+\s*=/i', $html), 'Ein on…-Attribut im Formular.');

        // Ohne CSRF-Feld: 419, kein Datensatz.
        $_POST = $this->kontakteingabe();

        $this->assertSame(419, $this->router()->behandeln('POST', '/kontakt')->status);
        $this->assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn());
    }

    // ---------------------------------------------------------------- Hilfsmittel

    /** @return array<string,mixed> eine gültige Rückfrage — §11, alle Pflichtfelder. */
    private function kontakteingabe(): array
    {
        return [
            'name'              => 'Erika Mustermann',
            'company'           => 'mustermann-sanitaer.example',
            'email'             => 'erika@example.org',
            'phone'             => '',
            'anliegen'          => 'domain',
            'nachricht'         => 'Unsere Heizung soll auf die neue Website. Wie lange dauert das?',
            'b2b_confirmed'     => '1',
            'privacy_confirmed' => '1',
            'hp_website'        => '',
            'form_started_at'   => (string) (time() - 60),
            'submission_id'     => '4f8a1c2e-9b3d-4a5f-8c7e-1d2b3a4c5d6e',
        ];
    }

    /**
     * Alle öffentlichen Seiten als gerendertes HTML.
     *
     * @return array<string,string>
     */
    private function seiten(): array
    {
        $seiten = [];

        foreach (array_keys(Launchadressen::alle()) as $pfad) {
            if (in_array($pfad, ['/impressum', '/datenschutz'], true)) {
                continue;
            }

            $seiten[$pfad] = (string) $this->router()->behandeln('GET', $pfad)->rumpf;
        }

        return $seiten;
    }

    private function router(): Router
    {
        return new Router(
            require SARTU_WURZEL . '/app/routes.php',
            new InstallationsSperre(new BetreiberdatenSpeicher($this->pdo), $this->arbeitsverzeichnis),
            new Wartungsmodus($this->arbeitsverzeichnis . '/ohne-wartung'),
        );
    }
}
