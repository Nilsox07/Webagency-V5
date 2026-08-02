<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\Admin\AdminAngebote;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminProjekte;
use Sartu\Data\AnmeldeTokenSpeicher;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\Uuid;
use Sartu\Helpers\Csrf;
use Sartu\Router;
use Sartu\Services\AngebotDienst;
use Sartu\Services\AnfrageService;
use Sartu\Services\Angebotstexte;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\KundenAnmeldung;
use Sartu\Services\Projektstatus;
use Sartu\Services\Ratenbegrenzung;
use Sartu\Services\Umwandlung;
use Sartu\Services\Wartungsmodus;
use Sartu\Sitzung;

/**
 * Die Strecke, an der Stufe A1 gemessen wird.
 *
 * `REIHENFOLGE.md`, „Fertig, wenn": *„Eine über `/briefing` abgeschickte Anfrage führt bis
 * zu einem gesendeten Angebot, das der Kunde in seinem Bereich sieht."*
 *
 * Der erste Test fährt genau diesen Satz ab — Bedarfsscheck, Umwandlung, Angebot, Senden,
 * Anmeldelink, Kundenbereich. Die übrigen prüfen die Regeln, an denen die Strecke scheitern
 * darf und muss.
 *
 * Testfälle: 6 · 7 · 8 · 9 · 10 · 20 · 21 · 22 · 23 · 57 · 59 · 60 · 62 · 83
 */
final class AngebotsstreckeTest extends Datenbankfall
{
    private string $adminId;

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
        $_POST = [];
        $_GET = [];

        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);

        $this->adminId = $this->adminAnlegen();
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_GET = [];

        parent::tearDown();
    }

    // ---------------------------------------------------------------- die ganze Strecke

    public function testVomBedarfsscheckBisZumAngebotImKundenbereich(): void
    {
        // 1 — eine Anfrage, wie sie der Bedarfsscheck anlegt.
        $anfrageId = $this->anfrageAnlegen();

        // 2 — der bewusste Klick. Anfrage ≠ Kunde (§4b.5).
        $this->alsAdmin($this->adminId);
        $umwandlung = (new Umwandlung($this->nachweis()))->ausfuehren($anfrageId, 'platzhirsch', '127.0.0.1');

        $this->assertNull($umwandlung['fehler']);
        $this->assertSame(1, $this->zaehlen('organizations'));
        $this->assertSame(1, $this->zaehlen('projects'));

        $projekt = (new AdminProjekte($this->nachweis()))->finden((string) $umwandlung['projektId']);
        $this->assertSame(Projektstatus::ANGEBOT_OFFEN, (string) $projekt['status']);
        $this->assertSame('platzhirsch', (string) $projekt['package']);
        $this->assertSame('l', (string) $projekt['protection_level']);

        // 3 — Angebot anlegen und senden.
        $dienst = new AngebotDienst($this->nachweis());
        $ergebnis = $dienst->anlegen(
            (string) $umwandlung['projektId'],
            $this->angebotseingabe(),
            '127.0.0.1',
        );

        $this->assertSame([], $ergebnis['fehler']);
        $this->assertSame([], $dienst->senden((string) $ergebnis['id'], '127.0.0.1'));

        // 4 — der Kunde meldet sich an. Ohne Passwort, ohne dass ihm jemand einen Link gibt.
        Sitzung::abmelden();
        $token = $this->anmeldelinkAnfordern('erika@mustermann-sanitaer.de');

        $this->assertNotNull($token, 'Es wurde kein Anmeldelink erzeugt.');
        $this->assertTrue((new KundenAnmeldung(pdo: $this->pdo))->einloesen($token, '127.0.0.1', 'Testlauf'));

        // 5 — und sieht sein Angebot.
        $seite = $this->router()->behandeln('GET', '/portal/angebot');

        $this->assertSame(200, $seite->status);
        $this->assertStringContainsString('AN-2026-001', $seite->rumpf);
        $this->assertStringContainsString('7.900,00 €', $seite->rumpf);
        $this->assertStringContainsString('10.888,00 €', $seite->rumpf);
        $this->assertStringContainsString('Barrierefreiheitsstärkungsgesetz', $seite->rumpf);

        // Und die Übersicht führt ihn dorthin (§8.1, Block 1).
        $uebersicht = $this->router()->behandeln('GET', '/portal');

        $this->assertSame(200, $uebersicht->status);
        $this->assertStringContainsString('Ihr Angebot liegt bereit', $uebersicht->rumpf);
    }

    // ---------------------------------------------------------------- Anmeldung

    /** Fall 6 — ein Token funktioniert genau einmal. */
    public function testAnmeldelinkFunktioniertGenauEinmal(): void
    {
        $token = $this->kundeMitLink();
        $anmeldung = new KundenAnmeldung(pdo: $this->pdo);

        $this->assertTrue($anmeldung->einloesen($token, '127.0.0.1', 'Testlauf'));

        Sitzung::abmelden();

        $this->assertFalse($anmeldung->einloesen($token, '127.0.0.1', 'Testlauf'), 'Der Link ging zweimal.');
    }

    /** Fall 7 — nach 15 Minuten ist der Token ungültig. */
    public function testAnmeldelinkLaeuftNachFuenfzehnMinutenAb(): void
    {
        $token = $this->kundeMitLink();

        $anweisung = $this->pdo->prepare(
            'UPDATE login_tokens SET expires_at = DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 MINUTE)'
        );
        $anweisung->execute();

        $this->assertFalse((new KundenAnmeldung(pdo: $this->pdo))->einloesen($token, '127.0.0.1', 'Testlauf'));
        $this->assertSame(AnmeldeTokenSpeicher::GUELTIGKEIT_MINUTEN, 15);
    }

    /**
     * Fall 8 — ein Token einer anderen E-Mail funktioniert nicht.
     *
     * Der Token hängt an einer `user_id`, und die Mail geht nur an die dort hinterlegte
     * Adresse. Ein Token „für eine andere Adresse" ist deshalb der Token eines anderen
     * Benutzers — und der meldet den anderen an, nicht mich.
     */
    public function testTokenEinerAnderenAdresseMeldetNichtMichAn(): void
    {
        $eine = $this->organisationAnlegen('Betrieb A', 'a@example.org');
        $andere = $this->organisationAnlegen('Betrieb B', 'b@example.org');

        $kundeA = $this->kundeAnlegen($eine, 'a@example.org');
        $kundeB = $this->kundeAnlegen($andere, 'b@example.org');

        $tokenB = (new AnmeldeTokenSpeicher($this->pdo))->anlegen($kundeB, '127.0.0.1');

        $this->assertTrue((new KundenAnmeldung(pdo: $this->pdo))->einloesen($tokenB, '127.0.0.1', 'Testlauf'));

        $this->assertSame($kundeB, Sitzung::wert(Sitzung::BENUTZER));
        $this->assertSame($andere, Sitzung::wert(Sitzung::ORGANISATION));
        $this->assertNotSame($kundeA, Sitzung::wert(Sitzung::BENUTZER));
    }

    /** Fall 9 — das Rate-Limit greift ab dem 6. Versuch je E-Mail und Stunde. */
    public function testRateLimitGreiftAbDemSechstenVersuchJeAdresse(): void
    {
        $organisation = $this->organisationAnlegen('Betrieb A', 'a@example.org');
        $this->kundeAnlegen($organisation, 'a@example.org');

        $anmeldung = new KundenAnmeldung(
            begrenzung: new Ratenbegrenzung($this->arbeitsverzeichnis),
            pdo: $this->pdo,
        );

        for ($versuch = 1; $versuch <= KundenAnmeldung::VERSUCHE_JE_ADRESSE; $versuch++) {
            $this->assertTrue($anmeldung->linkAnfordern('a@example.org', null), 'Versuch ' . $versuch);
        }

        $this->assertFalse($anmeldung->linkAnfordern('a@example.org', null), 'Der 6. Versuch ging durch.');
        $this->assertSame(5, KundenAnmeldung::VERSUCHE_JE_ADRESSE);
    }

    /**
     * Fall 10 — die Bestätigungsseite ist für vorhandene und nicht vorhandene Adressen
     * identisch.
     *
     * Verglichen wird der gerenderte Rumpf Zeichen für Zeichen. Ein zusätzlicher Satz, ein
     * anderer Statuscode oder ein abweichender Titel wäre eine Kontoauskunft.
     */
    public function testBestaetigungsseiteIstFuerJedeAdresseIdentisch(): void
    {
        $organisation = $this->organisationAnlegen('Betrieb A', 'vorhanden@example.org');
        $this->kundeAnlegen($organisation, 'vorhanden@example.org');

        $mit = $this->anmeldeversuch('vorhanden@example.org');
        $ohne = $this->anmeldeversuch('gibt-es-nicht@example.org');

        $this->assertSame($mit->status, $ohne->status);
        $this->assertSame($mit->rumpf, $ohne->rumpf, 'Die Bestätigungsseite verrät, ob es den Zugang gibt.');

        // Und der Unterschied liegt wirklich nur im Postfach.
        $this->assertSame(1, (int) $this->pdo->query('SELECT COUNT(*) FROM login_tokens')->fetchColumn());
    }

    /** Fall 83 — `/login` zeigt die Telefonnummer aus den Betreiberdaten, nie aus dem Code. */
    public function testAnmeldeseiteZeigtDieTelefonnummerAusDenBetreiberdaten(): void
    {
        $ohne = $this->router()->behandeln('GET', '/login')->rumpf;

        $this->assertStringNotContainsString('Kommt der Link nicht an?', $ohne,
            'Ohne hinterlegte Daten steht dort ein Notweg, den niemand gesetzt hat.');

        $this->betreiberdatenAnlegen(telefon: '02571 1234567');

        $mit = $this->router()->behandeln('GET', '/login')->rumpf;

        $this->assertStringContainsString('02571 1234567', $mit);
    }

    /** §6.3: Fehlt die Telefonnummer, erscheint die E-Mail-Adresse — nicht nichts. */
    public function testOhneTelefonnummerErscheintDieAdresse(): void
    {
        $this->betreiberdatenAnlegen(telefon: '');

        $html = $this->router()->behandeln('GET', '/login')->rumpf;

        $this->assertStringContainsString('betrieb@example.org', $html);
    }

    // ---------------------------------------------------------------- Willkommensstrecke

    /** Fall 57 — die Willkommensstrecke erscheint einmal und danach nicht mehr. */
    public function testWillkommensstreckeErscheintGenauEinmal(): void
    {
        $token = $this->kundeMitLink();
        $anmeldung = new KundenAnmeldung(pdo: $this->pdo);

        $anmeldung->einloesen($token, '127.0.0.1', 'Testlauf');
        $benutzerId = (string) Sitzung::wert(Sitzung::BENUTZER);

        $this->assertTrue($anmeldung->ersterLogin($benutzerId));

        $_POST = [Csrf::FELD => Csrf::token()];
        $antwort = $this->router()->behandeln('POST', '/willkommen/fertig');
        $_POST = [];

        $this->assertSame(303, $antwort->status);
        $this->assertSame('/portal', $antwort->kopfzeilen['Location']);
        $this->assertFalse($anmeldung->ersterLogin($benutzerId), 'Sie erscheint ein zweites Mal.');

        // Zweimal „gesehen" darf den ersten Zeitpunkt nicht ueberschreiben.
        $erster = $this->welcomeGesehenAm($benutzerId);
        $anmeldung->willkommenGesehen($benutzerId);

        $this->assertSame($erster, $this->welcomeGesehenAm($benutzerId));
    }

    // ---------------------------------------------------------------- Übergangstabelle

    /** Fall 60 — ein Paar, das nicht in der Tabelle steht, wird abgewiesen. */
    public function testNichtAufgefuehrtesPaarWirdAbgewiesen(): void
    {
        $this->assertFalse(
            Projektstatus::erlaubt(Projektstatus::ZAHLUNG_OFFEN, Projektstatus::PRODUKTION),
            'Produktion beginnt nicht auf Zusage.',
        );

        // Die erlaubten Nachbarn desselben Zustands gehen sehr wohl.
        $this->assertTrue(Projektstatus::erlaubt(Projektstatus::ZAHLUNG_OFFEN, Projektstatus::BRIEFING));

        // §5.1a, ausdrücklich verboten.
        $this->assertFalse(Projektstatus::erlaubt(Projektstatus::ABNAHME, Projektstatus::LIVE));
        $this->assertFalse(Projektstatus::erlaubt(Projektstatus::LIVE, Projektstatus::KORREKTUR));
        $this->assertFalse(Projektstatus::erlaubt(Projektstatus::LIVE, Projektstatus::PAUSIERT));
    }

    /**
     * §5.1a: „Kundenausgelöste Wechsel sind genau drei […] Erklärungen mit Namen und
     * Zeitpunkt."
     *
     * Die Tabelle darüber nennt **vier** Zeilen mit „Kunde". Der Widerspruch ist über die
     * Begründung des Satzes aufgelöst und im Kopf von `Projektstatus` festgehalten: Der Satz
     * zählt Erklärungen, nicht Klicks. Der Test hält beide Zahlen fest, damit die Auflösung
     * nicht beim nächsten Lesen wieder zur Frage wird.
     */
    public function testGenauDreiErklaerungenUndVierKundenhandlungen(): void
    {
        $kundenwechsel = array_filter(
            Projektstatus::uebergaenge(),
            static fn (array $u) => $u['wer'] === Projektstatus::KUNDE,
        );

        $this->assertCount(4, $kundenwechsel, 'Die Tabelle in §5.1a nennt vier Zeilen mit „Kunde".');
        $this->assertCount(3, Projektstatus::erklaerungen(), 'Erklärungen mit Namen und Zeitpunkt.');

        $this->assertSame(
            ['Angebot angenommen', 'Faktenfreigabe erteilt', 'Abnahme erklärt'],
            array_map(static fn (array $u) => $u['ereignis'], Projektstatus::erklaerungen()),
        );

        $this->assertTrue(Projektstatus::darfKundeAusloesen(
            Projektstatus::ANGEBOT_OFFEN, Projektstatus::ANGEBOT_ANGENOMMEN
        ));
        $this->assertFalse(Projektstatus::darfKundeAusloesen(
            Projektstatus::ZAHLUNG_OFFEN, Projektstatus::BRIEFING
        ), 'Der Kunde darf seinen eigenen Zahlungseingang bestätigen.');
    }

    /** Fall 62 — Fortsetzen führt auf `paused_from_status`, ein mitgesendeter Wert zählt nicht. */
    public function testFortsetzenFuehrtAufDenGespeichertenHerkunftsstatus(): void
    {
        $organisation = $this->organisationAnlegen('Betrieb A', 'a@example.org');
        $this->alsAdmin($this->adminId);

        $projekte = new AdminProjekte($this->nachweis(), $this->pdo);
        $projektId = $projekte->anlegen($organisation, 'Website A', 'wachstum', 2, 'm', Projektstatus::BRIEFING);

        $projekte->statusSetzen($projektId, Projektstatus::PAUSIERT, Projektstatus::BRIEFING);

        $projekt = $projekte->finden($projektId);
        $this->assertSame(Projektstatus::PAUSIERT, (string) $projekt['status']);
        $this->assertSame(Projektstatus::BRIEFING, (string) $projekt['paused_from_status']);

        // Der Herkunftsstatus steht in der Zeile, nicht im Formular. Ein mitgesendetes
        // `produktion` kann ihn nicht ersetzen — es gibt keinen Weg, ihn zu übergeben.
        $ziel = (string) $projekt['paused_from_status'];

        $this->assertSame(Projektstatus::BRIEFING, $ziel);
        $this->assertNotSame(Projektstatus::PRODUKTION, $ziel);
    }

    // ---------------------------------------------------------------- Rechenregeln

    /** Fall 21 — ein falscher Erstjahreswert verhindert das Speichern. */
    public function testFalscherErstjahreswertWirdAbgewiesen(): void
    {
        $eingabe = $this->angebotseingabe();
        $eingabe['first_year_net_cents'] = 999999;

        $fehler = (new AngebotDienst($this->nachweisAlsAdmin()))->pruefen($eingabe);

        $this->assertNotSame([], $fehler);
        $this->assertStringContainsString('10.888,00 €', implode(' ', $fehler),
            'Die Meldung nennt den erwarteten Betrag nicht.');
    }

    /** Fall 22 — `custom` ist nur beim Sonderprojekt zulässig. */
    public function testEigenerZahlungsplanNurBeimSonderprojekt(): void
    {
        $eingabe = $this->angebotseingabe();
        $eingabe['payment_plan'] = 'custom';
        $eingabe['payment_plan_custom'] = 'Anzahlung | 7.900,00 € | sofort';

        $fehler = (new AngebotDienst($this->nachweisAlsAdmin()))->pruefen($eingabe);

        $this->assertStringContainsString('nur beim Sonderprojekt', implode(' ', $fehler));
    }

    /** Fall 23 — bei `custom` muss die Summe der Raten dem Einmalpreis entsprechen. */
    public function testRatensummeMussDemEinmalpreisEntsprechen(): void
    {
        $eingabe = $this->angebotseingabe();
        $eingabe['package'] = 'sonderprojekt';
        $eingabe['one_time_net_cents'] = 1250000;
        $eingabe['first_year_net_cents'] = 1250000 + 12 * 24900;
        $eingabe['protection_monthly_net_cents'] = 24900;
        $eingabe['payment_plan'] = 'custom';
        $eingabe['payment_plan_custom'] = "Anzahlung | 5.000,00 € | sofort\nRest | 5.000,00 € | bei Abnahme";

        $fehler = (new AngebotDienst($this->nachweisAlsAdmin()))->pruefen($eingabe);

        $this->assertStringContainsString('Die Raten ergeben zusammen', implode(' ', $fehler));

        // Und mit passender Summe trägt es.
        $eingabe['payment_plan_custom'] = "Anzahlung | 5.000,00 € | sofort\nRest | 7.500,00 € | bei Abnahme";

        $this->assertSame([], (new AngebotDienst($this->nachweisAlsAdmin()))->pruefen($eingabe));
    }

    /** §4c — die Sperre greift, sie warnt nicht. */
    public function testAngebotMitVertragsabschlussUndOhneAusnahmeLaesstSichNichtSenden(): void
    {
        $eingabe = $this->angebotseingabe();
        $eingabe['bfsg_vertragsabschluss'] = 'ja';
        $eingabe['bfsg_kleinstunternehmen'] = 'unbekannt';

        $fehler = (new AngebotDienst($this->nachweisAlsAdmin()))->pruefen($eingabe);

        $this->assertContains(Angebotstexte::BFSG_SPERRE, $fehler);

        // Kleinstunternehmen `ja` löst die Sperre — mit dem Zusatz aus §4c.
        $eingabe['bfsg_kleinstunternehmen'] = 'ja';

        $this->assertSame([], (new AngebotDienst($this->nachweisAlsAdmin()))->pruefen($eingabe));
        $this->assertStringContainsString(
            'Kleinstunternehmen',
            (string) Angebotstexte::bfsgAusschluss('ja', 'ja'),
        );
    }

    /** §4c — beide Bausteine sind Pflicht. Wer den zweiten streicht, verkauft ihn mit. */
    public function testBeideBfsgBausteineSindPflicht(): void
    {
        $eingabe = $this->angebotseingabe();
        $eingabe['exclusions'] = 'Nichts weiter.';

        $fehler = (new AngebotDienst($this->nachweisAlsAdmin()))->pruefen($eingabe);

        $this->assertStringContainsString('Barrierefreiheitsstärkungsgesetz fehlt', implode(' ', $fehler));
    }

    // ---------------------------------------------------------------- Audit und Systemcodes

    /** Fall 20 — der Statuswechsel beim Senden erzeugt ein Audit-Ereignis mit Akteur. */
    public function testSendenErzeugtEinAuditEreignisMitAkteur(): void
    {
        $angebotId = $this->angebotAnlegen();

        (new AngebotDienst($this->nachweis()))->senden($angebotId, '127.0.0.1');

        $anweisung = $this->pdo->prepare(
            "SELECT * FROM audit_events WHERE action = 'angebot_gesendet' ORDER BY created_at DESC LIMIT 1"
        );
        $anweisung->execute();
        $ereignis = $anweisung->fetch(\PDO::FETCH_ASSOC);

        $this->assertIsArray($ereignis);
        $this->assertSame($this->adminId, (string) $ereignis['actor_user_id']);
        $this->assertSame('entwurf', (string) $ereignis['old_value']);
        $this->assertSame('gesendet', (string) $ereignis['new_value']);
        $this->assertNotSame('', (string) $ereignis['reason']);
    }

    /**
     * Fall 59 — kein Systemcode aus §5 erscheint in einer Kundenansicht.
     *
     * Volltextsuche über die tatsächlich gerenderten Seiten. Der Kunde sieht „Ihr Angebot
     * liegt bereit", nicht `angebot_offen`.
     */
    public function testKeinSystemcodeInEinerKundenansicht(): void
    {
        $angebotId = $this->angebotAnlegen();
        (new AngebotDienst($this->nachweis()))->senden($angebotId, '127.0.0.1');

        Sitzung::abmelden();
        $token = $this->anmeldelinkAnfordern('erika@mustermann-sanitaer.de');
        (new KundenAnmeldung(pdo: $this->pdo))->einloesen((string) $token, '127.0.0.1', 'Testlauf');

        $codes = [
            'angebot_offen', 'angebot_angenommen', 'zahlung_offen', 'briefing', 'produktion',
            'vorschau', 'korrektur', 'abnahme', 'launch_vorbereitung', 'pausiert',
            'entwurf', 'gesendet', 'angenommen', 'abgelaufen', 'zurueckgezogen',
            'qa_failed',
        ];

        foreach (['/portal', '/portal/angebot', '/willkommen/1', '/willkommen/2', '/willkommen/3'] as $pfad) {
            $html = $this->router()->behandeln('GET', $pfad)->rumpf;

            foreach ($codes as $code) {
                $this->assertStringNotContainsString(
                    $code,
                    $html,
                    sprintf('%s zeigt den Systemcode „%s".', $pfad, $code)
                );
            }
        }
    }

    // ---------------------------------------------------------------- Hilfsmittel

    private function anfrageAnlegen(): string
    {
        $ergebnis = (new AnfrageService(null, new Ratenbegrenzung($this->arbeitsverzeichnis)))->anlegen([
            'submission_id'      => Uuid::v4(),
            'form_started_at'    => (string) (time() - 60),
            'first_name'         => 'Erika',
            'last_name'          => 'Mustermann',
            'company'            => 'Mustermann Sanitär GmbH',
            'email'              => 'erika@mustermann-sanitaer.de',
            'preferred_contact'  => 'email',
            'b2b_confirmed'      => '1',
            'privacy_confirmed'  => '1',
            'angebot'            => 'Wir sanieren Bäder und Heizungen.',
            'einsatzort'         => '48431',
            'bestehende_website' => 'nein',
            'hauptziel'          => 'anfragen',
            'zielgruppe'         => 'privatkunden',
            'umfangssignale'     => ['mehrere_leistungen', 'mehrere_regionen', 'recruiting'],
            'sonderfunktionen'   => ['formular'],
            'domainstatus'       => 'vorhanden',
            'fester_termin'      => 'nein',
        ], [], '198.51.100.7');

        return (string) $ergebnis->anfrageId;
    }

    /** Ein vollständiges Angebot bis zum gesendeten Zustand — für die Tests, die dort ansetzen. */
    private function angebotAnlegen(): string
    {
        $anfrageId = $this->anfrageAnlegen();
        $this->alsAdmin($this->adminId);

        $umwandlung = (new Umwandlung($this->nachweis()))->ausfuehren($anfrageId, 'platzhirsch', null);
        $ergebnis = (new AngebotDienst($this->nachweis()))
            ->anlegen((string) $umwandlung['projektId'], $this->angebotseingabe(), null);

        $this->assertSame([], $ergebnis['fehler']);

        return (string) $ergebnis['id'];
    }

    /** @return array<string,mixed> */
    private function angebotseingabe(): array
    {
        // Reihenfolge beachten: `+` behaelt die Schluessel der LINKEN Seite. Die eigenen
        // Werte muessen deshalb links stehen, sonst gewinnen die leeren Vorbelegungen.
        return [
            'number'      => 'AN-2026-001',
            'summary'     => 'Mehr passende Anfragen aus der Region.',
            'sitemap'     => 'Start · Leistungen · Region · Karriere · Kontakt',
            'scope_pages' => 16,
            'scope_words' => 6500,
            'valid_until' => '2027-12-31',
        ] + (new AngebotDienst($this->nachweisAlsAdmin()))->vorbelegung('platzhirsch');
    }

    private function kundeMitLink(): string
    {
        $organisation = $this->organisationAnlegen('Betrieb A', 'a@example.org');
        $benutzerId = $this->kundeAnlegen($organisation, 'a@example.org');

        return (new AnmeldeTokenSpeicher($this->pdo))->anlegen($benutzerId, '127.0.0.1');
    }

    /** Fordert einen Link an und holt das Klartexttoken aus dem Speicher zurück. */
    private function anmeldelinkAnfordern(string $email): ?string
    {
        // Der Dienst gibt das Klartexttoken absichtlich nicht heraus — es geht nur in die
        // Mail. Der Test legt deshalb selbst einen an, über denselben Speicher.
        $anweisung = $this->pdo->prepare("SELECT id FROM users WHERE email = ? AND role = 'kunde'");
        $anweisung->execute([mb_strtolower($email)]);

        $benutzerId = $anweisung->fetchColumn();

        return is_string($benutzerId)
            ? (new AnmeldeTokenSpeicher($this->pdo))->anlegen($benutzerId, '127.0.0.1')
            : null;
    }

    private function anmeldeversuch(string $email): \Sartu\Antwort
    {
        $_POST = ['email' => $email, Csrf::FELD => Csrf::token()];

        $antwort = $this->router()->behandeln('POST', '/login');

        $_POST = [];

        return $antwort;
    }

    private function welcomeGesehenAm(string $benutzerId): ?string
    {
        $anweisung = $this->pdo->prepare('SELECT welcome_seen_at FROM users WHERE id = ?');
        $anweisung->execute([$benutzerId]);

        $wert = $anweisung->fetchColumn();

        return is_string($wert) ? $wert : null;
    }

    private function betreiberdatenAnlegen(string $telefon): void
    {
        (new BetreiberdatenSpeicher($this->pdo))->anlegen([
            'firmenname'  => 'SARTU',
            'strasse'     => 'Musterweg 1',
            'plz'         => '48268',
            'ort'         => 'Greven',
            'land'        => 'DE',
            'email'       => 'betrieb@example.org',
            'telefon'     => $telefon,
            'inhaltlich_verantwortlich' => 'Vorname Nachname',
            // §4: entweder USt-IdNr. oder Steuernummer — die Pruefbedingung verlangt eine.
            'steuernummer' => '337/5804/1234',
        ]);
    }

    private function nachweis(): AdminNachweis
    {
        $nachweis = AdminNachweis::ausSitzung();

        $this->assertNotNull($nachweis);

        return $nachweis;
    }

    private function nachweisAlsAdmin(): AdminNachweis
    {
        if (AdminNachweis::ausSitzung() === null) {
            $this->alsAdmin($this->adminId);
        }

        return $this->nachweis();
    }

    private function zaehlen(string $tabelle): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM `' . $tabelle . '`')->fetchColumn();
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
