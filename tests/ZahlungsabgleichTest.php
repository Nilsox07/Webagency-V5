<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminRechnungen;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\Uuid;
use Sartu\Data\Zahlungseingaenge;
use Sartu\Route;
use Sartu\Router;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Projektstatus;
use Sartu\Services\Rechnungsdienst;
use Sartu\Services\Verschluesselung;
use Sartu\Services\Wartungsmodus;
use Sartu\Services\Zahlungsabgleich;
use Sartu\Services\Zahlungsschluessel;
use Sartu\Services\Zahlungsstatus;

/**
 * Die Anbindung an den Zahlungsdienst — `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6.
 *
 * Testfälle: 90 · 91 · 92 · 93
 *
 * **Der Dienst ist im Test eine Attrappe, der Ablauf ist echt.** Ein Test, der einen
 * Fremdanbieter braucht, wird beim ersten Ausfall abgeschaltet — und geprüft wird hier
 * nicht, ob Mollie antwortet, sondern ob der Server die Antwort **holt**, statt sie
 * entgegenzunehmen. Was gegen den echten Dienst offenbleibt, steht in
 * `OFFENE_PRUEFUNGEN.md`.
 */
final class ZahlungsabgleichTest extends Datenbankfall
{
    private const SCHLUESSEL = 'test_geheimnisdasniemandsehendarf99';

    private string $adminId;

    private string $organisationId;

    private string $projektId;

    private string $rechnungId;

    private ?Zahlungsschluessel $schluesselDienst = null;

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
        $_POST = [];

        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);

        $this->betreiberdatenAnlegen();

        $this->adminId = $this->adminAnlegen();
        $this->organisationId = $this->organisationAnlegen('Mustermann Sanitär GmbH', 'erika@example.org');
        $this->kundeAnlegen($this->organisationId, 'erika@example.org');
        $this->projektId = $this->projektAnlegen();
        $this->rechnungId = $this->rechnungAnlegen('tr_wf00001');

        $this->alsAdmin($this->adminId);
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_SESSION = [];

        parent::tearDown();
    }

    /**
     * Testfall 90 — der Zustand stammt aus dem **serverseitigen Abruf**.
     *
     * Zwei Rechnungen, zwei Zahlungen, **eine** Benachrichtigung im selben Wortlaut: Sie
     * behauptet `status=paid` und einen Betrag von 99.999 €.
     *
     * Bei der ersten Zahlung steht beim Dienst `open` — es passiert nichts, obwohl die
     * Nachricht „bezahlt" sagt. Bei der zweiten steht `paid` — es wird gebucht, und zwar
     * über den **abgerufenen** Betrag, nicht über den behaupteten.
     *
     * Damit ist beides gezeigt: Die Nachricht bewirkt nichts, und der Abruf bewirkt alles.
     */
    public function testDerZustandStammtAusDemAbrufUndNichtAusDerNachricht(): void
    {
        $zweite = $this->rechnungAnlegen('tr_wf00002', 'RE-2026-0002');

        $stelle = new Zahlungsstelle();
        $stelle->hinterlegen('tr_wf00001', 'open', 119000);
        $stelle->hinterlegen('tr_wf00002', 'paid', 119000);

        $behauptung = static fn (string $id): string => http_build_query(
            ['id' => $id, 'status' => 'paid', 'amount' => '99999.00', 'paid' => 'true']
        );

        $ergebnis = $this->abgleich($stelle)->verarbeiten('tr_wf00001', $behauptung('tr_wf00001'));

        $this->assertSame(200, $ergebnis['status']);
        $this->assertSame(Zahlungseingaenge::ERGEBNIS_UNVERAENDERT, $ergebnis['ergebnis']);
        $this->assertSame(Zahlungsstatus::GESENDET, $this->rechnung()['status']);
        $this->assertSame(0, (int) $this->rechnung()['paid_cents']);

        $zweites = $this->abgleich($stelle)->verarbeiten('tr_wf00002', $behauptung('tr_wf00002'));

        $this->assertSame(Zahlungseingaenge::ERGEBNIS_GEBUCHT, $zweites['ergebnis']);

        $gebucht = $this->rechnung($zweite);

        $this->assertSame(Zahlungsstatus::BEZAHLT, (string) $gebucht['status']);

        // Der abgerufene Betrag, nicht die behaupteten 99.999 €.
        $this->assertSame(119000, (int) $gebucht['paid_cents']);
        $this->assertNotNull($gebucht['paid_at']);

        // Und kein Mensch hat gebucht.
        $this->assertNull($gebucht['marked_paid_by_user_id']);
    }

    /**
     * Dieselbe Aussage von der anderen Seite: Eine Rückkehr in den Kundenbereich, die einen
     * Zustand behauptet, ändert nichts.
     *
     * Die Anwendung hat gar keine Rückkehrroute, die etwas läse — dieser Test hält fest,
     * dass ein Aufruf mit solchen Angaben folgenlos bleibt.
     */
    public function testEineRueckkehrMitBehauptetemZustandAendertNichts(): void
    {
        $_GET = ['status' => 'paid', 'rechnung' => $this->rechnungId, 'paid_cents' => '119000'];
        $_POST = [];

        $antwort = $this->router()->behandeln('GET', '/portal/rechnungen');

        // Der Kunde ist nicht angemeldet — die Seite leitet zur Anmeldung. Entscheidend ist
        // die Zeile danach.
        $this->assertContains($antwort->status, [200, 302]);
        $this->assertSame(Zahlungsstatus::GESENDET, $this->rechnung()['status']);
        $this->assertSame(0, (int) $this->rechnung()['paid_cents']);

        $_GET = [];
    }

    /**
     * Testfall 91 — derselbe Webhook zweimal ändert den Zustand **genau einmal** und
     * antwortet beim zweiten Mal ohne Fehler.
     */
    public function testDerselbeWebhookZweimalWirktGenauEinmal(): void
    {
        $stelle = new Zahlungsstelle();
        $stelle->hinterlegen('tr_wf00001', 'paid', 119000);

        $erstes = $this->abgleich($stelle)->verarbeiten('tr_wf00001', 'id=tr_wf00001');

        $this->assertSame(200, $erstes['status']);
        $this->assertSame(Zahlungseingaenge::ERGEBNIS_GEBUCHT, $erstes['ergebnis']);
        $this->assertSame(1, $stelle->abrufe);

        $zweites = $this->abgleich($stelle)->verarbeiten('tr_wf00001', 'id=tr_wf00001');

        // Kein Fehler — sonst stellt Mollie endlos erneut zu.
        $this->assertSame(200, $zweites['status']);
        $this->assertSame('wiederholung', $zweites['ergebnis']);

        // Und **kein zweiter Abruf**: Die Wiederholung wird erkannt, bevor irgendetwas
        // geschieht.
        $this->assertSame(1, $stelle->abrufe);

        $this->assertSame(1, $this->zaehlenMit('payment_events', 'provider_event_id', 'tr_wf00001'));
        $this->assertSame(1, $this->auditZaehlen('zahlung_abgeglichen'));
        $this->assertSame(119000, (int) $this->rechnung()['paid_cents']);
    }

    /**
     * Die Wiederholung wird auch dann genau einmal wirksam, wenn sich der Zustand beim
     * Dienst zwischendurch ändert. Die Kennung entscheidet, nicht der Inhalt.
     */
    public function testEineWiederholungWirktAuchNachEinerAenderungBeimDienstNicht(): void
    {
        $stelle = new Zahlungsstelle();
        $stelle->hinterlegen('tr_wf00001', 'paid', 119000);

        $this->abgleich($stelle)->verarbeiten('tr_wf00001', 'id=tr_wf00001');

        $stelle->hinterlegen('tr_wf00001', 'paid', 500);

        $this->abgleich($stelle)->verarbeiten('tr_wf00001', 'id=tr_wf00001');

        $this->assertSame(119000, (int) $this->rechnung()['paid_cents']);
    }

    /** Testfall 92 — ein abweichender Betrag ändert nichts und wird sichtbar. */
    public function testEinAbweichenderBetragAendertNichtsUndWirdSichtbar(): void
    {
        $stelle = new Zahlungsstelle();
        // Überzahlung — §6: eine Abweichung, anders als die Teilzahlung.
        $stelle->hinterlegen('tr_wf00001', 'paid', 200000);

        $ergebnis = $this->abgleich($stelle)->verarbeiten('tr_wf00001', 'id=tr_wf00001');

        $this->assertSame(200, $ergebnis['status']);
        $this->assertSame(Zahlungseingaenge::ERGEBNIS_ABWEICHUNG, $ergebnis['ergebnis']);

        $this->assertSame(Zahlungsstatus::GESENDET, $this->rechnung()['status']);
        $this->assertSame(0, (int) $this->rechnung()['paid_cents']);
        $this->assertSame(0, $this->auditZaehlen('zahlung_abgeglichen'));
        $this->assertSame(1, $this->auditZaehlen('zahlung_abweichung'));

        // §6: „im Adminbereich sichtbar gemacht".
        $sichtbar = (new Zahlungseingaenge($this->pdo))->abweichungen();

        $this->assertCount(1, $sichtbar);
        $this->assertSame('tr_wf00001', (string) $sichtbar[0]['provider_event_id']);
        $this->assertSame($this->rechnungId, (string) $sichtbar[0]['invoice_id']);
    }

    /** Testfall 92, zweite Hälfte — eine abweichende Währung ändert ebenso nichts. */
    public function testEineAbweichendeWaehrungAendertNichts(): void
    {
        $stelle = new Zahlungsstelle();
        $stelle->hinterlegen('tr_wf00001', 'paid', 119000, 'CHF');

        $ergebnis = $this->abgleich($stelle)->verarbeiten('tr_wf00001', 'id=tr_wf00001');

        $this->assertSame(Zahlungseingaenge::ERGEBNIS_ABWEICHUNG, $ergebnis['ergebnis']);
        $this->assertSame(Zahlungsstatus::GESENDET, $this->rechnung()['status']);
        $this->assertSame(0, (int) $this->rechnung()['paid_cents']);
    }

    /**
     * §6, ausdrücklich: Eine **Teilzahlung** ist keine Abweichung, sondern ein eigener
     * Zustand. Sie wird gebucht.
     */
    public function testEineTeilzahlungIstKeineAbweichungSondernEinZustand(): void
    {
        $stelle = new Zahlungsstelle();
        $stelle->hinterlegen('tr_wf00001', 'paid', 50000);

        $ergebnis = $this->abgleich($stelle)->verarbeiten('tr_wf00001', 'id=tr_wf00001');

        $this->assertSame(Zahlungseingaenge::ERGEBNIS_GEBUCHT, $ergebnis['ergebnis']);
        $this->assertSame(Zahlungsstatus::TEILWEISE_BEZAHLT, $this->rechnung()['status']);
        $this->assertSame(50000, (int) $this->rechnung()['paid_cents']);
        $this->assertNull($this->rechnung()['paid_at']);
    }

    /** Eine Kennung ohne Rechnung wird angenommen, protokolliert und bewegt nichts. */
    public function testEineUnbekannteZahlungBewegtNichts(): void
    {
        $stelle = new Zahlungsstelle();
        $stelle->hinterlegen('tr_fremd', 'paid', 119000);

        $ergebnis = $this->abgleich($stelle)->verarbeiten('tr_fremd', 'id=tr_fremd');

        $this->assertSame(200, $ergebnis['status']);
        $this->assertSame(Zahlungseingaenge::ERGEBNIS_UNBEKANNT, $ergebnis['ergebnis']);
        $this->assertSame(Zahlungsstatus::GESENDET, $this->rechnung()['status']);
        $this->assertSame(1, $this->auditZaehlen('zahlung_ohne_rechnung'));
    }

    /**
     * Ein gescheiterter Abruf lässt die Zeile unverarbeitet stehen und antwortet mit einem
     * Fehler — hier **ist** eine Wiederholung erwünscht.
     */
    public function testEinGescheiterterAbrufBleibtUnverarbeitetUndBittetUmWiederholung(): void
    {
        $ergebnis = $this->abgleich(new Zahlungsstelle(scheitert: true))
            ->verarbeiten('tr_wf00001', 'id=tr_wf00001');

        $this->assertSame(502, $ergebnis['status']);

        $ereignis = (new Zahlungseingaenge($this->pdo))->ereignis('tr_wf00001');

        $this->assertIsArray($ereignis);
        $this->assertNull($ereignis['processed_at'], 'Ein gescheiterter Abruf gilt als verarbeitet.');
        $this->assertSame(Zahlungsstatus::GESENDET, $this->rechnung()['status']);
    }

    // ------------------------------------------------------------------ Fall 93

    /**
     * Testfall 93 — der Schlüssel steht verschlüsselt in der Datenbank.
     *
     * Geprüft wird an der Spalte selbst: Was dort liegt, darf den Schlüssel nicht enthalten,
     * auch nicht als Teilzeichenkette.
     */
    public function testDerSchluesselStehtVerschluesseltInDerDatenbank(): void
    {
        $this->assertSame([], $this->schluessel()->hinterlegen(Zahlungsschluessel::FELD_TEST, self::SCHLUESSEL));

        $gespeichert = (string) $this->pdo
            ->query('SELECT mollie_key_test FROM operator_settings WHERE singleton = 1')
            ->fetchColumn();

        $this->assertNotSame('', $gespeichert);
        $this->assertStringNotContainsString(self::SCHLUESSEL, $gespeichert);
        $this->assertStringNotContainsString('test_geheimnis', $gespeichert);

        // Und er kommt heil wieder heraus — sonst wäre es kein Tresor, sondern ein Verlust.
        $this->assertSame(self::SCHLUESSEL, $this->schluessel()->klartext(Zahlungsschluessel::FELD_TEST));
    }

    /** Die Ansicht bekommt höchstens eine Spur, nie den Schlüssel. */
    public function testEineAnsichtBekommtHoechstensDieLetztenVierZeichen(): void
    {
        $this->schluessel()->hinterlegen(Zahlungsschluessel::FELD_TEST, self::SCHLUESSEL);

        $spur = $this->schluessel()->spur(Zahlungsschluessel::FELD_TEST);

        $this->assertSame('…rf99', $spur);
        $this->assertStringNotContainsString('test_', (string) $spur);
        $this->assertTrue($this->schluessel()->hinterlegt(Zahlungsschluessel::FELD_TEST));
    }

    /**
     * Der Schlüssel steht in **keiner** Fehlermeldung.
     *
     * Der abgewiesene Wert wird nicht zurückgegeben — eine Meldung wie „„xyz" ist zu kurz"
     * ist genau der Fall aus 93.
     */
    public function testKeineFehlermeldungWiederholtDenEingegebenenSchluessel(): void
    {
        $zuKurz = 'test_kurz';

        $fehler = $this->schluessel()->hinterlegen(Zahlungsschluessel::FELD_TEST, $zuKurz);

        $this->assertNotSame([], $fehler);

        foreach ($fehler as $zeile) {
            $this->assertStringNotContainsString($zuKurz, $zeile);
        }
    }

    /**
     * Der Schlüssel steht in **keiner** Protokollzeile — auch nicht in `detail`.
     *
     * Geprüft wird über den gesamten Protokollinhalt, nicht über eine Auswahl von Feldern:
     * `detail` ist JSON, und eine Prüfung je Feld übersieht genau das eine, das jemand
     * später dazuschreibt.
     */
    public function testKeineProtokollzeileEnthaeltDenSchluessel(): void
    {
        $this->schluessel()->hinterlegen(Zahlungsschluessel::FELD_TEST, self::SCHLUESSEL);

        $stelle = new Zahlungsstelle();
        $stelle->hinterlegen('tr_wf00001', 'paid', 119000);

        $this->abgleich($stelle)->verarbeiten('tr_wf00001', 'id=tr_wf00001');
        $this->abgleich(new Zahlungsstelle(scheitert: true))->verarbeiten('tr_anderes', 'id=tr_anderes');

        $zeilen = $this->pdo->query('SELECT * FROM audit_events')->fetchAll();

        $this->assertNotSame([], $zeilen);

        foreach ($zeilen as $zeile) {
            $this->assertStringNotContainsString(
                self::SCHLUESSEL,
                json_encode($zeile, JSON_UNESCAPED_UNICODE) ?: '',
                'Eine Protokollzeile enthält den Zahlungsschlüssel.',
            );
        }
    }

    /**
     * Welcher der beiden Schlüssel gilt, entscheidet `APP_ENV` — nicht ein Schalter.
     *
     * Fehlt `APP_ENV`, gilt produktiv. Das ist die vorsichtige Richtung: Wer sich vertut,
     * arbeitet mit einem fehlenden Produktivschlüssel und merkt es sofort.
     */
    public function testAppEnvEntscheidetWelcherSchluesselGilt(): void
    {
        $vorher = getenv('APP_ENV') === false ? null : (string) getenv('APP_ENV');

        putenv('APP_ENV=local');
        $this->assertSame(Zahlungsschluessel::FELD_TEST, Zahlungsschluessel::feld());

        putenv('APP_ENV=production');
        $this->assertSame(Zahlungsschluessel::FELD_LIVE, Zahlungsschluessel::feld());

        // Jeder andere Wert gilt ebenfalls als produktiv. Nur `local` schaltet auf den
        // Testschluessel um — eine Aufzaehlung „nicht production" haette bei einem Tippfehler
        // in `APP_ENV` still mit echtem Geld gearbeitet.
        putenv('APP_ENV=staging');
        $this->assertSame(Zahlungsschluessel::FELD_LIVE, Zahlungsschluessel::feld());

        $vorher === null ? putenv('APP_ENV') : putenv('APP_ENV=' . $vorher);
    }

    /** Ein Testschlüssel im Produktivfeld wird abgewiesen — und umgekehrt. */
    public function testEinSchluesselLandetNichtImFalschenFeld(): void
    {
        $this->assertNotSame(
            [],
            $this->schluessel()->hinterlegen(Zahlungsschluessel::FELD_LIVE, self::SCHLUESSEL),
        );
        $this->assertFalse($this->schluessel()->hinterlegt(Zahlungsschluessel::FELD_LIVE));
    }

    // ------------------------------------------------------------------ Versand

    /**
     * §6 Schritt 1 und 2: Beim Versand entsteht die Zahlung, und die Adresse steht danach an
     * der Rechnung.
     */
    public function testDerVersandLegtDieZahlungAnUndMerktDieAdresse(): void
    {
        $stelle = new Zahlungsstelle();
        $postfach = new Postfach();

        $entwurf = $this->rechnungAnlegenUeberDenDienst($stelle, $postfach);

        $this->assertSame([], (new Rechnungsdienst(
            $this->nachweis(),
            mail: $postfach,
            zahlungsdienst: $stelle,
            pdo: $this->pdo,
        ))->senden($entwurf, null));

        $rechnung = $this->rechnungen()->finden($entwurf);

        $this->assertIsArray($rechnung);
        $this->assertNotNull($rechnung['payment_provider_id']);
        $this->assertStringStartsWith('https://', (string) $rechnung['mollie_payment_url']);

        $this->assertCount(1, $stelle->angelegt);
        $this->assertSame((int) $rechnung['gross_cents'], $stelle->angelegt[0]['betrag']);
        $this->assertSame('EUR', $stelle->angelegt[0]['waehrung']);
        $this->assertSame((string) $rechnung['number'], $stelle->angelegt[0]['referenz']);
        $this->assertStringEndsWith('/api/zahlungen/mollie', $stelle->angelegt[0]['webhook']);
    }

    /**
     * Ein nicht erreichbarer Zahlungsdienst hält den Versand **nicht** an.
     *
     * Die Rechnung ist ausgestellt und trägt eine Nummer aus dem lückenlosen Kreis; sie
     * zurückzunehmen hieße, eine Lücke zu reißen. Auf der Rechnung steht die Bankverbindung
     * — sie ist auch ohne Zahlungslink vollständig.
     */
    public function testEinNichtErreichbarerZahlungsdienstHaeltDenVersandNichtAn(): void
    {
        $stelle = new Zahlungsstelle(scheitert: true);
        $postfach = new Postfach();

        $entwurf = $this->rechnungAnlegenUeberDenDienst($stelle, $postfach);

        $this->assertSame([], (new Rechnungsdienst(
            $this->nachweis(),
            mail: $postfach,
            zahlungsdienst: $stelle,
            pdo: $this->pdo,
        ))->senden($entwurf, null));

        $rechnung = $this->rechnungen()->finden($entwurf);

        $this->assertIsArray($rechnung);
        $this->assertSame(Zahlungsstatus::GESENDET, (string) $rechnung['status']);
        $this->assertNull($rechnung['payment_provider_id']);
        $this->assertSame(1, $this->auditZaehlen('zahlung_anlegen_gescheitert'));
    }

    // ------------------------------------------------------------------ Hilfsmittel

    private function abgleich(Zahlungsstelle $stelle): Zahlungsabgleich
    {
        return new Zahlungsabgleich($stelle, mail: new Postfach(), pdo: $this->pdo);
    }

    /**
     * **Ein** Tresor je Testfall, nicht einer je Aufruf.
     *
     * Mit einem neuen Zufallsschlüssel bei jedem Aufruf käme heraus, was ein verlorenes
     * `ENC_KEY` bewirkt: Der Kasten steht da und lässt sich nicht mehr öffnen. Das ist das
     * beabsichtigte Verhalten der Verschlüsselung und nicht das, was hier geprüft wird.
     */
    private function schluessel(): Zahlungsschluessel
    {
        return $this->schluesselDienst ??= new Zahlungsschluessel(
            new BetreiberdatenSpeicher($this->pdo),
            new Verschluesselung(Verschluesselung::schluesselErzeugen()),
        );
    }

    /** @return array<string,mixed> */
    private function rechnung(?string $id = null): array
    {
        $anweisung = $this->pdo->prepare('SELECT * FROM invoices WHERE id = ?');
        $anweisung->execute([$id ?? $this->rechnungId]);

        $zeile = $anweisung->fetch();

        $this->assertIsArray($zeile);

        return $zeile;
    }

    private function rechnungen(): AdminRechnungen
    {
        return new AdminRechnungen($this->nachweis(), $this->pdo);
    }

    private function rechnungAnlegenUeberDenDienst(Zahlungsstelle $stelle, Postfach $postfach): string
    {
        $angelegt = (new Rechnungsdienst(
            $this->nachweis(),
            mail: $postfach,
            zahlungsdienst: $stelle,
            pdo: $this->pdo,
        ))->anlegen($this->projektId, ['milestone' => 'zwischenrate', 'net_cents' => 50000], null);

        $this->assertSame([], $angelegt['fehler']);

        return (string) $angelegt['id'];
    }

    private function auditZaehlen(string $aktion): int
    {
        $anweisung = $this->pdo->prepare('SELECT COUNT(*) FROM audit_events WHERE action = ?');
        $anweisung->execute([$aktion]);

        return (int) $anweisung->fetchColumn();
    }

    private function zaehlenMit(string $tabelle, string $spalte, string $wert): int
    {
        $anweisung = $this->pdo->prepare('SELECT COUNT(*) FROM `' . $tabelle . '` WHERE `' . $spalte . '` = ?');
        $anweisung->execute([$wert]);

        return (int) $anweisung->fetchColumn();
    }

    private function nachweis(): AdminNachweis
    {
        $nachweis = AdminNachweis::ausSitzung();

        $this->assertNotNull($nachweis);

        return $nachweis;
    }

    private function router(): Router
    {
        return new Router(
            require SARTU_WURZEL . '/app/routes.php',
            new InstallationsSperre(new BetreiberdatenSpeicher($this->pdo), $this->arbeitsverzeichnis),
            new Wartungsmodus($this->arbeitsverzeichnis . '/ohne-wartung'),
        );
    }

    private function projektAnlegen(): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO projects (id, organization_id, title, package, included_feedback_rounds,'
            . ' protection_level, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([$id, $this->organisationId, 'Website Mustermann', 'wachstum', 2, 'm',
            Projektstatus::ZAHLUNG_OFFEN]);

        return $id;
    }

    /** Eine bereits versendete Rechnung mit hinterlegter Zahlungskennung. */
    private function rechnungAnlegen(string $zahlungskennung, string $nummer = 'RE-2026-0001'): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO invoices (id, project_id, number, milestone, status, issued_at, net_cents,'
            . ' vat_cents, gross_cents, due_date, payment_provider_id)'
            . ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            $id, $this->projektId, $nummer, 'anzahlung', Zahlungsstatus::GESENDET,
            '2026-08-09 10:00:00', 100000, 19000, 119000, '2027-01-01', $zahlungskennung,
        ]);

        return $id;
    }
}
