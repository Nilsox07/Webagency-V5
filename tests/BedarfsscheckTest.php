<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Helpers\Csrf;
use Sartu\Router;
use Sartu\Services\Anfragebenachrichtigung;
use Sartu\Services\AnfrageService;
use Sartu\Services\Bedarfsscheck;
use Sartu\Services\Empfehlung;
use Sartu\Services\Herkunft;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Ratenbegrenzung;
use Sartu\Services\Wartungsmodus;

/**
 * Der Bedarfsscheck von der Einstiegsseite bis zur Danke-Seite.
 *
 * **Ohne eine Zeile JavaScript** — genau so, wie Website-Lastenheft §9.5a es verlangt und
 * wie dieser Test es fährt: fünf `POST`, jeder beantwortet mit einer Weiterleitung auf den
 * nächsten Schritt. Ein Browser käme hier zu keinem anderen Ergebnis.
 *
 * Testfälle: 29 · 30 · 31 · 32 · 33 · 34 · 35 · 36 · 37 · 38 · 39 · 40a · 40b
 */
final class BedarfsscheckTest extends Datenbankfall
{
    /** Eine Antwortkombination, die `platzhirsch` mit Ampel `standard` ergibt. */
    private const ANTWORTEN = [
        1 => [
            'angebot'            => 'Wir sanieren Bäder und Heizungen für Privatkunden.',
            'einsatzort'         => '48431',
            'einzugsgebiet'      => '',
            'bestehende_website' => 'nein',
            'website_adresse'    => '',
        ],
        2 => [
            'hauptziel'  => 'anfragen',
            'zielgruppe' => 'privatkunden',
        ],
        3 => [
            'umfangssignale' => [
                Empfehlung::SIGNAL_MEHRERE_LEISTUNGEN,
                Empfehlung::SIGNAL_MEHRERE_REGIONEN,
                Empfehlung::SIGNAL_RECRUITING,
            ],
        ],
        4 => [
            'sonderfunktionen' => [Empfehlung::GATE_FORMULAR],
        ],
        5 => [
            'domainstatus'     => 'vorhanden',
            'fester_termin'    => 'nein',
            'termin_datum'     => '',
            'nicht_uebersehen' => '',
        ],
    ];

    private const KONTAKT = [
        'first_name'        => 'Erika',
        'last_name'         => 'Mustermann',
        'company'           => 'Mustermann Sanitär GmbH',
        'email'             => 'Erika@Mustermann-Sanitaer.de',
        'phone'             => '',
        'preferred_contact' => 'email',
        'b2b_confirmed'     => '1',
        'privacy_confirmed' => '1',
        'hp_website'        => '',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '198.51.100.7', 'HTTP_HOST' => 'localhost'];
        $_POST = [];
        $_GET = [];

        // Die Einrichtung ist abgeschlossen — sonst leitet der Router jeden Aufruf dorthin.
        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_GET = [];

        parent::tearDown();
    }

    // ---------------------------------------------------------------- der ganze Weg

    /**
     * §9.5a — der Bedarfsscheck läuft ohne JavaScript vollständig durch.
     *
     * Der Test fährt genau das, was ein Browser ohne Skript tut: `GET` holen, `POST`
     * schicken, der Weiterleitung folgen. Fünfmal, dann Ergebnis, dann Kontaktdaten.
     */
    public function testOhneJavaScriptFuehrtDerBedarfsscheckBisZurDankeSeite(): void
    {
        $router = $this->router();

        $einstieg = $router->behandeln('GET', '/briefing');
        $this->assertSame(200, $einstieg->status);
        $this->assertStringContainsString('Bedarf prüfen lassen', $einstieg->rumpf);

        $start = $this->post($router, '/briefing/start');
        $this->assertSame(303, $start->status);
        $this->assertSame('/briefing/1', $start->kopfzeilen['Location']);

        foreach (self::ANTWORTEN as $nummer => $eingabe) {
            $seite = $router->behandeln('GET', '/briefing/' . $nummer);
            $this->assertSame(200, $seite->status, 'Schritt ' . $nummer . ' ist nicht erreichbar.');
            $this->assertStringContainsString('Thema ' . $nummer . ' von 5', $seite->rumpf);

            $antwort = $this->post($router, '/briefing/' . $nummer, $eingabe);

            $this->assertSame(303, $antwort->status, 'Schritt ' . $nummer . ' wurde abgewiesen.');
            $this->assertSame(
                $nummer < Bedarfsscheck::SCHRITTE ? '/briefing/' . ($nummer + 1) : '/briefing/ergebnis',
                $antwort->kopfzeilen['Location'],
            );
        }

        // §9.3 — das Ergebnis steht VOR den Kontaktdaten, mit dem Preis aus der Preistabelle.
        $ergebnis = $router->behandeln('GET', '/briefing/ergebnis');
        $this->assertSame(200, $ergebnis->status);
        $this->assertStringContainsString('Unsere vorläufige Empfehlung: Platzhirsch', $ergebnis->rumpf);
        $this->assertStringContainsString('7.900,00 € einmalig + 249,00 €/Monat', $ergebnis->rumpf);
        $this->assertStringContainsString('10.888,00 € netto', $ergebnis->rumpf);
        $this->assertStringContainsString(
            'Alle Preise netto zzgl. gesetzlicher Umsatzsteuer.',
            $ergebnis->rumpf,
            'Der Pflichthinweis fehlt unter der Preisnennung.',
        );
        $this->assertStringContainsString('noindex', $ergebnis->rumpf);

        $kontakt = $router->behandeln('GET', '/briefing/kontakt');
        $this->assertSame(200, $kontakt->status);

        $this->zeitregelErfuellen();
        $absenden = $this->post($router, '/briefing/absenden', self::KONTAKT);

        $this->assertSame(303, $absenden->status, 'Das Absenden hat nicht zur Danke-Seite geführt.');
        $this->assertSame('/briefing/danke', $absenden->kopfzeilen['Location']);

        $danke = $router->behandeln('GET', '/briefing/danke');
        $this->assertSame(200, $danke->status);
        $this->assertStringContainsString('Danke — wir haben Ihre Angaben.', $danke->rumpf);

        $lead = $this->einzigerLead();
        $this->assertSame('platzhirsch', $lead['recommended_package']);
        $this->assertSame('standard', $lead['flag']);
        $this->assertSame('neu', $lead['status']);
        // §4b: die Adresse wird kleingeschrieben abgelegt.
        $this->assertSame('erika@mustermann-sanitaer.de', $lead['email']);
    }

    /** Fall 29 — ein abgeschickter Bedarfsscheck erzeugt **nur** einen `lead`. */
    public function testAbgeschickterBedarfsscheckErzeugtNurEinenLead(): void
    {
        $this->durchlaufen();

        $this->assertSame(1, $this->zaehlen('leads'));
        $this->assertSame(0, $this->zaehlen('organizations'), 'Es entstand eine Organisation.');
        $this->assertSame(0, $this->zaehlen('users'), 'Es entstand ein Benutzer.');
        $this->assertSame(0, $this->zaehlen('projects'), 'Es entstand ein Projekt.');
        $this->assertSame(0, $this->zaehlen('offers'), 'Es entstand ein Angebot.');
    }

    /** Fall 30 — `POST /briefing/absenden` ohne CSRF-Feld wird abgelehnt. */
    public function testAbsendenOhneCsrfFeldWirdAbgelehnt(): void
    {
        $router = $this->router();
        $this->bisZumKontakt($router);
        $this->zeitregelErfuellen();

        // Alles wie beim echten Absenden — nur ohne Token.
        $_POST = self::KONTAKT;
        $antwort = $router->behandeln('POST', '/briefing/absenden');

        $this->assertSame(419, $antwort->status);
        $this->assertSame(0, $this->zaehlen('leads'));
    }

    /** Fall 31 — das Rate-Limit greift ab dem 11. Versuch je IP und Stunde. */
    public function testRateLimitGreiftAbDemElftenVersuchJeIp(): void
    {
        $dienst = new AnfrageService(null, new Ratenbegrenzung($this->arbeitsverzeichnis));

        for ($versuch = 1; $versuch <= AnfrageService::VERSUCHE_JE_IP; $versuch++) {
            $ergebnis = $dienst->anlegen($this->rohdaten(), [], '203.0.113.5');

            $this->assertTrue($ergebnis->wurdeGespeichert(), 'Versuch ' . $versuch . ' wurde abgewiesen.');
        }

        $elfter = $dienst->anlegen($this->rohdaten(), [], '203.0.113.5');

        $this->assertFalse($elfter->wurdeGespeichert());
        $this->assertFalse($elfter->dankeSeite, 'Die Begrenzung darf nicht als Erfolg aussehen.');
        $this->assertStringContainsString('in einer Stunde', (string) $elfter->meldung);
        $this->assertSame(AnfrageService::VERSUCHE_JE_IP, $this->zaehlen('leads'));

        // Eine andere Gegenstelle ist davon nicht betroffen.
        $andere = $dienst->anlegen($this->rohdaten(), [], '203.0.113.6');
        $this->assertTrue($andere->wurdeGespeichert());
    }

    /** Fall 32 — ein ausgefülltes Honigtopffeld führt zur Danke-Seite, ohne Datensatz. */
    public function testHonigtopfFuehrtZurDankeSeiteOhneDatensatz(): void
    {
        $router = $this->router();
        $this->bisZumKontakt($router);
        $this->zeitregelErfuellen();

        $antwort = $this->post($router, '/briefing/absenden', ['hp_website' => 'https://spam.example'] + self::KONTAKT);

        $this->assertSame(303, $antwort->status);
        $this->assertSame('/briefing/danke', $antwort->kopfzeilen['Location']);
        $this->assertSame(0, $this->zaehlen('leads'));
    }

    /** Fall 33 — Absenden unter drei Sekunden führt zur Danke-Seite, ohne Datensatz. */
    public function testAbsendenUnterDreiSekundenErzeugtKeinenDatensatz(): void
    {
        $router = $this->router();
        $this->bisZumKontakt($router);

        // `form_started_at` bleibt der echte Startzeitpunkt — also gerade eben.
        $antwort = $this->post($router, '/briefing/absenden', self::KONTAKT);

        $this->assertSame(303, $antwort->status);
        $this->assertSame('/briefing/danke', $antwort->kopfzeilen['Location']);
        $this->assertSame(0, $this->zaehlen('leads'));
    }

    /** Fall 34 — dieselbe `submission_id` zweimal ergibt genau einen Datensatz. */
    public function testDieselbeEinreichungZweimalErgibtEinenDatensatz(): void
    {
        $dienst = new AnfrageService(null, new Ratenbegrenzung($this->arbeitsverzeichnis));
        $rohdaten = $this->rohdaten();

        $erste = $dienst->anlegen($rohdaten, [], '198.51.100.7');
        $zweite = $dienst->anlegen($rohdaten, [], '198.51.100.7');

        $this->assertTrue($erste->wurdeGespeichert());
        $this->assertFalse($zweite->wurdeGespeichert());
        $this->assertTrue($zweite->dankeSeite, 'Die Doppeleinreichung darf nichts verraten.');
        $this->assertSame(1, $this->zaehlen('leads'));
    }

    /** Fall 35 — fehlende Bestätigung zeigt den Schritt erneut und legt nichts an. */
    public function testFehlendeBestaetigungZeigtDenSchrittErneut(): void
    {
        foreach (['b2b_confirmed', 'privacy_confirmed'] as $bestaetigung) {
            $this->schemaNeuAufbauen();

            $router = $this->router();
            $this->bisZumKontakt($router);
            $this->zeitregelErfuellen();

            $eingabe = self::KONTAKT;
            $eingabe[$bestaetigung] = '';

            $antwort = $this->post($router, '/briefing/absenden', $eingabe);

            $this->assertSame(200, $antwort->status, $bestaetigung);
            $this->assertStringContainsString('Geschäftliche E-Mail-Adresse', $antwort->rumpf, $bestaetigung);
            $this->assertSame(0, $this->zaehlen('leads'), $bestaetigung);
            // Die Angaben bleiben erhalten (§9.5b).
            $this->assertStringContainsString('Mustermann Sanitär GmbH', $antwort->rumpf, $bestaetigung);
        }
    }

    /** Fall 36 — Formulardaten über 64 KB werden abgewiesen. */
    public function testZuGrosseFormulardatenWerdenAbgewiesen(): void
    {
        $dienst = new AnfrageService(null, new Ratenbegrenzung($this->arbeitsverzeichnis));

        $rohdaten = $this->rohdaten();
        $rohdaten['nicht_uebersehen'] = str_repeat('a', AnfrageService::MAX_BYTES);

        $ergebnis = $dienst->anlegen($rohdaten, [], '198.51.100.7');

        $this->assertFalse($ergebnis->wurdeGespeichert());
        $this->assertFalse($ergebnis->dankeSeite);
        $this->assertSame(0, $this->zaehlen('leads'));
    }

    /**
     * Fall 37 — keine Fehlermeldung nennt Feldwerte, interne Kennungen oder Datenbankmeldungen.
     *
     * Geprüft wird gegen die Werte, die der Absender selbst geschickt hat: Eine Meldung, die
     * seine Eingabe zurückwirft, wird zum Kanal für fremden Inhalt.
     */
    public function testFehlermeldungenNennenWederWerteNochInterna(): void
    {
        $dienst = new AnfrageService(null, new Ratenbegrenzung($this->arbeitsverzeichnis));

        $rohdaten = $this->rohdaten();
        $rohdaten['email'] = 'kein-at-zeichen';
        $rohdaten['company'] = '';
        $rohdaten['preferred_contact'] = 'brieftaube';

        $ergebnis = $dienst->anlegen($rohdaten, [], '198.51.100.7');
        $meldungen = implode(' ', $ergebnis->feldfehler);

        $this->assertNotSame('', $meldungen, 'Es kam gar keine Meldung.');
        $this->assertStringNotContainsString('kein-at-zeichen', $meldungen);
        $this->assertStringNotContainsString('brieftaube', $meldungen);
        $this->assertStringNotContainsString($rohdaten['submission_id'], $meldungen);

        foreach (['SQLSTATE', 'PDO', 'leads', 'INSERT', 'Exception', 'Stack trace'] as $interna) {
            $this->assertStringNotContainsString($interna, $meldungen, $interna . ' steht in der Meldung.');
        }
    }

    /** Fall 38 — ein unbekanntes Zusatzfeld landet in `payload` und wird nicht abgewiesen. */
    public function testUnbekanntesFeldLandetInPayloadUndWirdNichtAbgewiesen(): void
    {
        $dienst = new AnfrageService(null, new Ratenbegrenzung($this->arbeitsverzeichnis));

        $rohdaten = $this->rohdaten();
        $rohdaten['spaeter_dazugekommen'] = 'ein Wert aus einer künftigen Fassung';

        $ergebnis = $dienst->anlegen($rohdaten, [], '198.51.100.7');

        $this->assertTrue($ergebnis->wurdeGespeichert());

        $payload = json_decode((string) $this->einzigerLead()['payload'], true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('ein Wert aus einer künftigen Fassung', $payload['spaeter_dazugekommen']);
        // Spamabwehr und Token gehören nicht ins Antwortarchiv.
        $this->assertArrayNotHasKey('hp_website', $payload);
        $this->assertArrayNotHasKey('form_started_at', $payload);
        $this->assertArrayNotHasKey('submission_id', $payload);
    }

    /** Fall 39 — Empfehlung und Ampel kommen vom Server, nicht aus dem Formular. */
    public function testEmpfehlungUndAmpelLassenSichNichtUeberDasFormularSetzen(): void
    {
        $router = $this->router();
        $this->bisZumKontakt($router);
        $this->zeitregelErfuellen();

        $manipuliert = [
            'recommended_package' => 'start',
            'flag'                => 'rot',
            'status'              => 'angebot_erstellt',
        ] + self::KONTAKT;

        $this->post($router, '/briefing/absenden', $manipuliert);

        $lead = $this->einzigerLead();

        $this->assertSame('platzhirsch', $lead['recommended_package'], 'Das Paket kam aus dem Formular.');
        $this->assertSame('standard', $lead['flag'], 'Die Ampel kam aus dem Formular.');
        $this->assertSame('neu', $lead['status'], 'Der Zustand kam aus dem Formular.');
    }

    /** Fall 40a — die Herkunft wird beim ERSTEN Aufruf gemerkt und landet später im `lead`. */
    public function testHerkunftWirdBeimErstenAufrufGemerktUndLandetImLead(): void
    {
        // Der erste Aufruf trägt die Kennzeichen, die Schritte danach nicht mehr.
        Herkunft::merken(
            ['utm_source' => 'test', 'utm_medium' => 'audit', 'gclid' => 'ABC123'],
            ['REQUEST_URI' => '/briefing?utm_source=test', 'HTTP_REFERER' => 'https://www.google.de/search?q=geheim'],
        );

        Herkunft::merken(['utm_source' => 'spaeter'], ['REQUEST_URI' => '/briefing/3']);

        $this->durchlaufen();

        $lead = $this->einzigerLead();

        $this->assertSame('test', $lead['utm_source'], 'Ein späterer Aufruf hat die Herkunft überschrieben.');
        $this->assertSame('audit', $lead['utm_medium']);
        $this->assertSame('gclid:ABC123', $lead['click_id'], 'Die Art der Klickkennung fehlt.');
    }

    /** Fall 40b — nur der Hostname, nur der Pfad. Keine vollständigen Adressen. */
    public function testHerkunftSpeichertNurHostUndPfad(): void
    {
        Herkunft::merken(
            [],
            [
                'REQUEST_URI'  => '/briefing?utm_source=test&suchwort=vertraulich',
                'HTTP_REFERER' => 'https://www.google.de/search?q=badsanierung+greven',
            ],
        );

        $this->durchlaufen();

        $lead = $this->einzigerLead();

        $this->assertSame('/briefing', $lead['landing_page']);
        $this->assertSame('www.google.de', $lead['referrer_host']);
        $this->assertStringNotContainsString('suchwort', (string) $lead['landing_page']);
        $this->assertStringNotContainsString('badsanierung', (string) $lead['referrer_host']);
        $this->assertStringNotContainsString('?', (string) $lead['referrer_host']);
    }

    // ---------------------------------------------------------------- Sprungschutz

    /** Ohne Angaben gibt es keine Empfehlung — sonst stünde dort eine auf leeren Feldern. */
    public function testErgebnisIstOhneVollstaendigeAngabenNichtErreichbar(): void
    {
        $router = $this->router();

        $ergebnis = $router->behandeln('GET', '/briefing/ergebnis');

        $this->assertSame(303, $ergebnis->status);
        $this->assertSame('/briefing/1', $ergebnis->kopfzeilen['Location']);

        $kontakt = $router->behandeln('GET', '/briefing/kontakt');

        $this->assertSame(303, $kontakt->status);
        $this->assertSame('/briefing/1', $kontakt->kopfzeilen['Location']);
    }

    /** Wer Schritt 1 nicht beantwortet hat, kommt nicht auf Schritt 4. */
    public function testVorwaertsSpringenLandetBeimErstenOffenenSchritt(): void
    {
        $router = $this->router();

        $this->post($router, '/briefing/start');
        $this->post($router, '/briefing/1', self::ANTWORTEN[1]);

        $antwort = $router->behandeln('GET', '/briefing/4');

        $this->assertSame(303, $antwort->status);
        $this->assertSame('/briefing/2', $antwort->kopfzeilen['Location']);
    }

    /** §9.2 — „Nichts davon" lässt sich nicht mit einer anderen Angabe kombinieren. */
    public function testNichtsDavonLaesstSichNichtKombinieren(): void
    {
        $router = $this->router();

        $this->post($router, '/briefing/start');
        $this->post($router, '/briefing/1', self::ANTWORTEN[1]);
        $this->post($router, '/briefing/2', self::ANTWORTEN[2]);

        $antwort = $this->post($router, '/briefing/3', [
            'umfangssignale' => [Empfehlung::SIGNAL_HAUPTANGEBOT, Empfehlung::SIGNAL_NICHTS_DAVON],
        ]);

        $this->assertSame(200, $antwort->status, 'Die Kombination wurde durchgelassen.');
        $this->assertStringContainsString('lässt sich nicht mit anderen Angaben kombinieren', $antwort->rumpf);
    }

    /** §9.5 — der Fehler steht am Feld, und das erste fehlerhafte Feld bekommt den Fokus. */
    public function testFehlerStehtAmFeldUndDasErsteBekommtDenFokus(): void
    {
        $router = $this->router();

        $this->post($router, '/briefing/start');
        $antwort = $this->post($router, '/briefing/1', ['angebot' => '', 'einsatzort' => '']);

        $this->assertSame(200, $antwort->status);
        $this->assertStringContainsString('Bitte beschreiben Sie Ihr Angebot in ein bis drei Sätzen.', $antwort->rumpf);
        $this->assertStringContainsString('Bitte geben Sie Ort oder Postleitzahl an.', $antwort->rumpf);
        $this->assertSame(1, substr_count($antwort->rumpf, 'autofocus'), 'Der Fokus steht nicht genau einmal.');
        $this->assertStringContainsString('id="feld-angebot-fehler"', $antwort->rumpf);
    }

    /** §9.5b — nach dem Absenden steht kein erneut absendbares Formular mehr da. */
    public function testNachDemAbsendenGibtEsKeinFormularMehr(): void
    {
        $router = $this->router();
        $this->durchlaufen($router);

        $erneut = $router->behandeln('GET', '/briefing/kontakt');

        $this->assertSame(303, $erneut->status);
        $this->assertSame('/briefing/1', $erneut->kopfzeilen['Location']);
    }

    // ---------------------------------------------------------------- Benachrichtigung

    /**
     * §9.5b — die Benachrichtigung an SARTU ist eine Kurzmeldung, kein Datenauszug.
     *
     * §10 nennt drei Angaben: Unternehmen, empfohlener Umfang, Ampelkennzeichen. Was der
     * Interessent geschrieben hat, gehört nicht in ein Postfach.
     */
    public function testBenachrichtigungEnthaeltNurDieDreiAngabenAusParagraf10(): void
    {
        $nachricht = Anfragebenachrichtigung::nachricht('Mustermann Sanitär GmbH', 'platzhirsch', 'standard');

        $this->assertStringContainsString('Mustermann Sanitär GmbH', $nachricht);
        $this->assertStringContainsString('Platzhirsch', $nachricht);
        $this->assertStringContainsString('/admin/anfragen', $nachricht);

        foreach ([self::KONTAKT['email'], self::KONTAKT['first_name'], self::ANTWORTEN[1]['angebot']] as $inhalt) {
            $this->assertStringNotContainsString(
                $inhalt,
                $nachricht,
                'Die Benachrichtigung gibt Inhalte weiter, die §10 nicht nennt.',
            );
        }

        $this->assertSame(
            'Neue Anfrage: Mustermann Sanitär GmbH',
            Anfragebenachrichtigung::betreff('Mustermann Sanitär GmbH'),
        );
    }

    /**
     * Ohne hinterlegten Empfänger geht keine Mail — und es wird auch keiner erfunden.
     *
     * `ADMIN_NOTIFY_EMAIL` steht in §1.5 unter „Erforderliche Werte", wird aber in keinem
     * Einrichtungsschritt erhoben (`OFFENE_PRUEFUNGEN.md`). Der Ersatz durch die
     * Impressumsadresse wäre eine Festlegung, die niemand getroffen hat.
     */
    public function testOhneHinterlegtenEmpfaengerGehtKeineBenachrichtigungRaus(): void
    {
        $vorher = getenv('ADMIN_NOTIFY_EMAIL') === false ? null : (string) getenv('ADMIN_NOTIFY_EMAIL');
        putenv('ADMIN_NOTIFY_EMAIL=');

        try {
            $this->assertFalse((new Anfragebenachrichtigung())->senden('Betrieb', 'start', 'standard'));
        } finally {
            $vorher === null ? putenv('ADMIN_NOTIFY_EMAIL') : putenv('ADMIN_NOTIFY_EMAIL=' . $vorher);
        }
    }

    /** `unklar` ist kein Paket — es bekommt keinen ersatzweise angezeigten Paketnamen. */
    public function testUnklarBekommtKeinenPaketnamen(): void
    {
        $this->assertSame('Noch offen', \Sartu\Services\Preise::name('unklar'));
        $this->assertNull(\Sartu\Services\Preise::preiszeile('unklar'));
    }

    // ---------------------------------------------------------------- Hilfsmittel

    private function durchlaufen(?Router $router = null): void
    {
        $router ??= $this->router();

        $this->bisZumKontakt($router);
        $this->zeitregelErfuellen();
        $this->post($router, '/briefing/absenden', self::KONTAKT);
    }

    private function bisZumKontakt(Router $router): void
    {
        $this->post($router, '/briefing/start');

        foreach (self::ANTWORTEN as $nummer => $eingabe) {
            $this->post($router, '/briefing/' . $nummer, $eingabe);
        }
    }

    /**
     * §4b.3 verlangt drei Sekunden zwischen Start und Absenden.
     *
     * Der Test wartet sie nicht ab, sondern setzt den Startzeitpunkt zurück — sonst kostete
     * jeder dieser Fälle drei Sekunden Laufzeit für eine Erkenntnis, die er nicht gewinnt.
     */
    private function zeitregelErfuellen(): void
    {
        $_SESSION['_bedarfsscheck']['form_started_at'] =
            (string) (time() - AnfrageService::MINDESTDAUER_SEKUNDEN - 1);
    }

    /** @param array<string,mixed> $eingabe */
    private function post(Router $router, string $pfad, array $eingabe = []): \Sartu\Antwort
    {
        $_POST = $eingabe + [Csrf::FELD => Csrf::token()];

        $antwort = $router->behandeln('POST', $pfad);

        $_POST = [];

        return $antwort;
    }

    /** Rohdaten für den Dienst — ohne Oberfläche, mit eigener `submission_id`. */
    private function rohdaten(): array
    {
        $antworten = [];

        foreach (self::ANTWORTEN as $eingabe) {
            $antworten += $eingabe;
        }

        return self::KONTAKT + $antworten + [
            'submission_id'   => \Sartu\Data\Uuid::v4(),
            'form_started_at' => (string) (time() - 60),
        ];
    }

    private function router(): Router
    {
        return new Router(
            require SARTU_WURZEL . '/app/routes.php',
            new InstallationsSperre(new BetreiberdatenSpeicher($this->pdo), $this->arbeitsverzeichnis),
            new Wartungsmodus($this->arbeitsverzeichnis . '/ohne-wartung'),
        );
    }

    /** @return array<string,mixed> */
    private function einzigerLead(): array
    {
        $zeilen = $this->pdo->query('SELECT * FROM leads')->fetchAll(\PDO::FETCH_ASSOC);

        $this->assertCount(1, $zeilen, 'Es gibt nicht genau einen Datensatz.');

        return $zeilen[0];
    }

    private function zaehlen(string $tabelle): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM `' . $tabelle . '`')->fetchColumn();
    }

}
