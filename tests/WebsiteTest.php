<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Helpers\Csrf;
use Sartu\Router;
use Sartu\Services\Branchenseiten;
use Sartu\Services\InstallationsSperre;
use Sartu\Data\Uuid;
use Sartu\Services\AnfrageService;
use Sartu\Services\Kontaktanfrage;
use Sartu\Services\Projektmail;
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

        $this->betreiberdatenAnlegen();
    }

    /**
     * Eine Betreiberzeile mit hinterlegtem Empfänger.
     *
     * **Ohne sie kann `/kontakt` nichts** — §4b.6 nimmt dem Formular den Datensatz, und ohne
     * `benachrichtigung_email` geht auch keine Mail raus. Genau dieser Zustand hat den Test
     * beim ersten Lauf scheitern lassen, und er ist ein echter: Die Seite zeigt dann den
     * Ausweichweg statt des Formulars.
     */
    private function betreiberdatenAnlegen(): void
    {
        $anweisung = $this->pdo->prepare(
            'INSERT INTO operator_settings (id, firmenname, strasse, plz, ort, land, email,'
            . ' benachrichtigung_email, steuernummer, inhaltlich_verantwortlich)'
            . ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            \Sartu\Data\Uuid::v4(), 'Betreiber', 'Strasse 1', '01067', 'Ort', 'DE',
            'betreiber@example.org', 'eingang@example.org', '337/5804/1234', 'Verantwortlich',
        ]);
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

    // ---------------------------------------------------------------- §4b.6 Kontaktformular

    /**
     * §4b.6, der Kern: „erzeugt **keinen** Datensatz."
     *
     * Null Zeilen in `leads`, genau eine Mail. Die vorige Fassung legte die Rückfrage in
     * `leads` ab — dieser Test hätte das gefunden, wenn es ihn gegeben hätte.
     */
    public function testEineRueckfrageErzeugtKeinenDatensatzUndGenauEineMail(): void
    {
        $postfach = new Postfach();

        $ergebnis = (new Kontaktanfrage(mail: new Projektmail($postfach)))
            ->senden($this->kontakteingabe(), '127.0.0.1');

        $this->assertTrue($ergebnis->dankeSeite);
        $this->assertFalse($ergebnis->wurdeGespeichert(), 'Es wurde ein Datensatz gemeldet.');

        $this->assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn());
        $this->assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM support_messages')->fetchColumn());

        $this->assertCount(1, $postfach->mails, 'Es ging nicht genau eine Mail raus.');
    }

    /**
     * Die Mail trägt den vollständigen Inhalt.
     *
     * Ohne Datensatz ist sie der einzige Träger. Fehlt darin etwas, ist es weg — anders als
     * beim Bedarfsscheck, wo §10 bewusst eine Kurzmeldung ohne Datenauszug verlangt.
     */
    public function testDieMailTraegtDieGanzeRueckfrage(): void
    {
        $postfach = new Postfach();

        (new Kontaktanfrage(mail: new Projektmail($postfach)))
            ->senden($this->kontakteingabe(), '127.0.0.1');

        $mail = $postfach->mails[0];

        $this->assertStringContainsString('Domain und Launch', $mail['betreff']);

        foreach ([
            'Erika Mustermann',
            'mustermann-sanitaer.example',
            'erika@example.org',
            'Unsere Heizung soll auf die neue Website',
        ] as $inhalt) {
            $this->assertStringContainsString($inhalt, $mail['text'], $inhalt);
        }
    }

    /**
     * Geht die Mail nicht raus, sieht der Absender **keine** Bestätigung.
     *
     * Der einzige Fall im Projekt, in dem ein Mailfehler den Absender erreicht — und der
     * richtige: Es gibt keinen Datensatz, der die Rückfrage auffängt.
     */
    public function testEineGescheiterteMailWirdGemeldetUndNichtVerschwiegen(): void
    {
        $ergebnis = (new Kontaktanfrage(mail: new Projektmail(new Postfach(scheitert: true))))
            ->senden($this->kontakteingabe(), '127.0.0.1');

        $this->assertFalse($ergebnis->dankeSeite, 'Der Absender sieht eine Bestätigung, obwohl nichts ankam.');
        $this->assertNotNull($ergebnis->meldung);
        $this->assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn());
    }

    /** §11: die Nachricht braucht mindestens 20 Zeichen, mit dem Wortlaut aus §11. */
    public function testEineZuKurzeNachrichtWirdAmFeldAbgewiesen(): void
    {
        $eingabe = $this->kontakteingabe();
        $eingabe['nachricht'] = 'Zu kurz.';

        $postfach = new Postfach();
        $ergebnis = (new Kontaktanfrage(mail: new Projektmail($postfach)))->senden($eingabe, '127.0.0.1');

        $this->assertFalse($ergebnis->dankeSeite);
        $this->assertSame(
            'Bitte beschreiben Sie Ihr Anliegen in ein bis zwei Sätzen.',
            $ergebnis->feldfehler['nachricht'] ?? null,
        );
        $this->assertSame([], $postfach->mails, 'Es ging trotz Fehler eine Mail raus.');
    }

    /**
     * §4b.6: „Honigtopf, Zeitregel und Rate-Limit gelten dort gleichermaßen."
     *
     * Alle drei still — der Absender sieht die normale Bestätigung, und es geht keine Mail
     * raus. Dazu die Doppelklicksperre, die ohne Datensatz in der Sitzung liegt.
     */
    public function testHonigtopfZeitregelUndDoppelklickGreifenStill(): void
    {
        $postfach = new Postfach();
        $dienst = new Kontaktanfrage(mail: new Projektmail($postfach));

        $mitHonig = $this->kontakteingabe();
        $mitHonig['hp_website'] = 'https://spam.example';

        $this->assertTrue($dienst->senden($mitHonig, '127.0.0.1')->dankeSeite);

        $zuSchnell = $this->kontakteingabe();
        $zuSchnell['form_started_at'] = (string) time();

        $this->assertTrue($dienst->senden($zuSchnell, '127.0.0.1')->dankeSeite);
        $this->assertSame([], $postfach->mails, 'Honigtopf oder Zeitregel haben eine Mail durchgelassen.');

        // Doppelklick: dieselbe Kennung zweimal ergibt eine Mail, nicht zwei.
        $eingabe = $this->kontakteingabe();

        $dienst->senden($eingabe, '127.0.0.1');
        $dienst->senden($eingabe, '127.0.0.1');

        $this->assertCount(1, $postfach->mails);
    }

    /** §4b.6: das Rate-Limit greift, mit derselben Zahl wie beim Bedarfsscheck. */
    public function testDasRatelimitGreiftAbDemElftenVersuch(): void
    {
        $postfach = new Postfach();
        $dienst = new Kontaktanfrage(mail: new Projektmail($postfach));

        for ($i = 0; $i < AnfrageService::VERSUCHE_JE_IP; $i++) {
            $eingabe = $this->kontakteingabe();
            $eingabe['submission_id'] = Uuid::v4();

            $this->assertTrue($dienst->senden($eingabe, '127.0.0.1')->dankeSeite, 'Versuch ' . ($i + 1));
        }

        $eingabe = $this->kontakteingabe();
        $eingabe['submission_id'] = Uuid::v4();

        $ergebnis = $dienst->senden($eingabe, '127.0.0.1');

        $this->assertFalse($ergebnis->dankeSeite);
        $this->assertCount(AnfrageService::VERSUCHE_JE_IP, $postfach->mails);
    }

    /** §11: ohne die Datenschutz-Bestätigung geht nichts raus. */
    public function testOhneDatenschutzbestaetigungGehtNichtsRaus(): void
    {
        $eingabe = $this->kontakteingabe();
        unset($eingabe['privacy_confirmed']);

        $postfach = new Postfach();
        $ergebnis = (new Kontaktanfrage(mail: new Projektmail($postfach)))->senden($eingabe, '127.0.0.1');

        $this->assertFalse($ergebnis->dankeSeite);
        $this->assertArrayHasKey('privacy_confirmed', $ergebnis->feldfehler);
        $this->assertSame([], $postfach->mails);
    }

    /**
     * §11 zählt sieben Felder auf — die B2B-Bestätigung ist keins davon.
     *
     * Sie stand hier, weil `chk_leads_bestaetigungen` sie verlangte. Ohne `leads`-Zeile gibt
     * es die Prüfbedingung nicht, und ein Häkchen ohne Zweck ist eine Hürde ohne Grund.
     */
    public function testDasFormularVerlangtKeineB2bBestaetigungMehr(): void
    {
        $html = (string) $this->router()->behandeln('GET', '/kontakt')->rumpf;

        $this->assertStringNotContainsString('b2b_confirmed', $html);

        // Und die Prüfung dahinter verlangt sie auch nicht.
        $eingabe = $this->kontakteingabe();
        unset($eingabe['b2b_confirmed']);

        $this->assertTrue(
            (new Kontaktanfrage(mail: new Projektmail(new Postfach())))->senden($eingabe, '127.0.0.1')->dankeSeite,
        );
    }

    /** §11: das Formular läuft über die Route, mit CSRF und ohne JavaScript. */
    public function testDasFormularLaeuftUeberDieRouteUndBrauchtEinCsrfFeld(): void
    {
        $html = (string) $this->router()->behandeln('GET', '/kontakt')->rumpf;

        $this->assertStringContainsString('action="/kontakt"', $html);
        $this->assertStringContainsString(Csrf::FELD, $html);
        $this->assertSame(0, preg_match('/\son[a-z]+\s*=/i', $html), 'Ein on…-Attribut im Formular.');

        // Ohne CSRF-Feld: 419, und nichts geht raus.
        $_POST = $this->kontakteingabe();

        $this->assertSame(419, $this->router()->behandeln('POST', '/kontakt')->status);
        $this->assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn());
    }

    // ---------------------------------------------------------------- §17 Bestätigungsdatei

    /**
     * `KEYWORD_VALIDATION.md` führt **jede** Launch-Adresse — Website-Lastenheft §17.
     *
     * §17 nennt die Datei „vor dem Livegang zwingend"; ohne sie sind Titel, H1 und URL nicht
     * bestätigt. Eine Datei, die zwei Adressen später nicht mehr vollständig ist, hilft dabei
     * nicht. Dieser Test schlägt an, sobald eine Adresse dazukommt — dann wird
     * `php bin/keywords.php` neu ausgeführt, nicht die Datei nachgetippt.
     *
     * **Geprüft wird die Vollständigkeit, nicht die Bestätigung.** Ob eine Zeile bestätigt
     * ist, entscheidet ein Mensch (Keywordstrategie §1.1). Kein Test setzt ein Häkchen, das
     * ein Mensch setzen muss.
     */
    public function testDieBestaetigungsdateiFuehrtJedeLaunchadresse(): void
    {
        $datei = SARTU_WURZEL . '/KEYWORD_VALIDATION.md';

        $this->assertFileExists($datei, 'KEYWORD_VALIDATION.md fehlt — `php bin/keywords.php`.');

        $inhalt = (string) file_get_contents($datei);
        $fehlend = [];

        foreach (array_keys(Launchadressen::alle()) as $pfad) {
            if (!str_contains($inhalt, '| `' . $pfad . '` |')) {
                $fehlend[] = $pfad;
            }
        }

        $this->assertSame([], $fehlend, 'Adressen ohne Zeile — `php bin/keywords.php` erneut ausführen.');

        // Die Kennzeichnung aus §1.1 für den Fall ohne Volumenwerkzeug muss dastehen,
        // sonst liest sich eine leere Spalte wie ein gemessener Wert von null.
        $this->assertStringContainsString('ohne Volumendaten', $inhalt);
    }

    // ---------------------------------------------------------------- §3 Fokusfalle

    /**
     * Die Fokusfalle kommt aus einer eigenen Datei — und das Menü lebt ohne sie weiter.
     *
     * Website-Lastenheft §3 verlangt „Fokus wird im Overlay gehalten"; §1 verlangt volle
     * Nutzbarkeit ohne JavaScript. Beides gilt, weil das Menü ein `details` ist und das
     * Skript nur etwas **hinzufügt**.
     *
     * Geprüft wird deshalb nicht, was das Skript tut — das kann nur ein Browser sagen und
     * steht in `OFFENE_PRUEFUNGEN.md` —, sondern dass es **weglassbar** ist: Das Menü hat
     * kein `hidden`, keinen Öffnungsknopf ausserhalb eines `summary` und keinen einzigen
     * Verweis, der ohne Skript ins Leere ginge.
     */
    public function testDasMenueBleibtOhneSkriptBedienbar(): void
    {
        $mitMenue = 0;

        foreach ($this->seiten() as $pfad => $html) {
            $hatMenue = preg_match('#<details class="menue">(.*?)</details>#s', $html, $treffer) === 1;
            $hatSkript = preg_match('#<script src="/assets/js/menue\.js" defer></script>#', $html) === 1;

            // Beides oder keines. `/briefing` traegt das Layout `oeffentlich` ohne Kopfband
            // und braucht die Falle deshalb nicht — ein Skript ohne Menue waere Ballast.
            $this->assertSame($hatMenue, $hatSkript, $pfad . ': Menü und Fokusfalle passen nicht zusammen.');

            if (!$hatMenue) {
                continue;
            }

            ++$mitMenue;

            $this->assertStringContainsString('<summary', $treffer[1], $pfad . ' hat keinen Öffner.');

            // Ein Menüpunkt, der ohne Skript nirgends hinführt, wäre ein toter Punkt.
            $this->assertSame(0, preg_match('/href\s*=\s*"#"/', $treffer[1]), $pfad . ' hat einen Verweis ins Leere.');
            $this->assertSame(0, preg_match('/\son[a-z]+\s*=/i', $treffer[1]), $pfad . ' hat ein on…-Attribut.');
        }

        $this->assertGreaterThan(0, $mitMenue, 'Keine einzige Seite trägt das mobile Menü.');

        // Die Datei bleibt klein genug, dass sie niemand nachlädt statt sie zu lesen.
        $skript = SARTU_WURZEL . '/public/assets/js/menue.js';

        $this->assertFileExists($skript);
        $this->assertLessThan(2048, filesize($skript), 'Die Fokusfalle ist über 2 KB gewachsen.');
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
