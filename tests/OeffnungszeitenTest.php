<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Admin\OeffnungszeitenSteuerung;
use Sartu\Data\Admin\AdminOeffnungszeiten;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenOeffnungszeiten;
use Sartu\Data\Uuid;
use Sartu\Router;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Oeffnungszeitendienst;
use Sartu\Services\Projektstatus;
use Sartu\Services\Wartungsmodus;

/**
 * Die eine Pflegefunktion des Kunden — Stufe B, Portal-Lastenheft §8.7.
 *
 * Testfall 19: „Öffnungszeiten mit Bis vor Von werden abgelehnt."
 *
 * Geprüft wird die Ablehnung **und** dass sie nichts schreibt. Ein Formular, das den Fehler
 * meldet und die sechs richtigen Tage trotzdem speichert, hat den Testfall bestanden und die
 * Absicht verfehlt.
 */
final class OeffnungszeitenTest extends Datenbankfall
{
    private string $adminId;

    private string $organisationId;

    private string $kundeId;

    private string $projektId;

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
        $_POST = [];
        $_GET = [];

        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);

        $this->adminId = $this->adminAnlegen();
        $this->organisationId = $this->organisationAnlegen('Mustermann Sanitär GmbH', 'erika@example.org');
        $this->kundeId = $this->kundeAnlegen($this->organisationId, 'erika@example.org');
        $this->projektId = $this->projektAnlegen();

        $this->alsKunde($this->organisationId, $this->kundeId);
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_GET = [];

        parent::tearDown();
    }

    /** Testfall 19 — `Bis` vor `Von` wird abgelehnt, und es wird nichts geschrieben. */
    public function testBisVorVonWirdAbgelehnt(): void
    {
        $eingabe = $this->woche();
        $eingabe['tage'][2]['open_time'] = '17:00';
        $eingabe['tage'][2]['close_time'] = '09:00';

        $fehler = $this->dienst()->einreichen($eingabe);

        $this->assertSame(['Die Bis-Zeit muss nach der Von-Zeit liegen.'], $fehler);
        $this->assertSame([], $this->speicher()->wochentage(), 'Es wurde trotz Fehler geschrieben.');
    }

    /** §8.7: „muss **nach** der Von-Zeit liegen" — gleich ist nicht nach. */
    public function testGleicheZeitenWerdenAbgelehnt(): void
    {
        $eingabe = $this->woche();
        $eingabe['tage'][0]['open_time'] = '08:00';
        $eingabe['tage'][0]['close_time'] = '08:00';

        $this->assertSame(
            ['Die Bis-Zeit muss nach der Von-Zeit liegen.'],
            $this->dienst()->einreichen($eingabe),
        );
    }

    /** §8.7, zweite Meldung: ein geöffneter Tag ohne Zeiten. */
    public function testGeoeffneterTagOhneZeitenWirdAbgelehnt(): void
    {
        $eingabe = $this->woche();
        $eingabe['tage'][4]['open_time'] = '';
        $eingabe['tage'][4]['close_time'] = '';

        $this->assertSame(
            ['Bitte geben Sie für geöffnete Tage eine Von- und eine Bis-Zeit an.'],
            $this->dienst()->einreichen($eingabe),
        );
    }

    /** Dieselbe Meldung an drei Tagen wird einmal gezeigt, nicht dreimal. */
    public function testDieselbeMeldungErscheintNurEinmal(): void
    {
        $eingabe = $this->woche();

        foreach ([0, 1, 2] as $tag) {
            $eingabe['tage'][$tag]['close_time'] = '';
        }

        $this->assertCount(1, $this->dienst()->einreichen($eingabe));
    }

    public function testEineGepflegteWocheWirdGespeichertUndWartetAufVeroeffentlichung(): void
    {
        $this->assertSame([], $this->dienst()->einreichen($this->woche()));

        $tage = $this->speicher()->wochentage();

        $this->assertCount(7, $tage);
        $this->assertSame(0, (int) $tage[0]['weekday']);
        $this->assertSame('08:00:00', (string) $tage[0]['open_time']);
        $this->assertSame('17:00:00', (string) $tage[0]['close_time']);

        // Samstag und Sonntag stehen als geschlossen da — ohne Zeiten.
        $this->assertSame(1, (int) $tage[6]['closed']);
        $this->assertNull($tage[6]['open_time']);

        $this->assertTrue($this->speicher()->wartetAufVeroeffentlichung());
    }

    /** Ein zweiter Absendevorgang ersetzt den Stand, er legt keinen zweiten Montag an. */
    public function testZweitesEinreichenErsetztDenStand(): void
    {
        $this->dienst()->einreichen($this->woche());

        $zweite = $this->woche();
        $zweite['tage'][0]['open_time'] = '09:30';

        $this->assertSame([], $this->dienst()->einreichen($zweite));

        $tage = $this->speicher()->wochentage();

        $this->assertCount(7, $tage);
        $this->assertSame('09:30:00', (string) $tage[0]['open_time']);
    }

    public function testAusnahmenWerdenGespeichertUndLeereZeilenVerworfen(): void
    {
        $eingabe = $this->woche();
        $eingabe['ausnahmen'] = [
            ['date' => '2026-12-24', 'closed' => '1', 'label' => 'Heiligabend'],
            ['date' => '2026-12-31', 'closed' => '0', 'open_time' => '09:00',
             'close_time' => '12:00', 'label' => 'Silvester'],
            ['date' => '', 'closed' => '1', 'label' => ''],
        ];

        $this->assertSame([], $this->dienst()->einreichen($eingabe));

        $ausnahmen = $this->speicher()->ausnahmen();

        $this->assertCount(2, $ausnahmen);
        $this->assertSame('2026-12-24', (string) $ausnahmen[0]['date']);
        $this->assertSame('Heiligabend', (string) $ausnahmen[0]['label']);
        $this->assertSame('09:00:00', (string) $ausnahmen[1]['open_time']);
    }

    /** Eine Ausnahme mit `Bis` vor `Von` wird genauso abgelehnt wie ein Wochentag. */
    public function testAusnahmeMitBisVorVonWirdAbgelehnt(): void
    {
        $eingabe = $this->woche();
        $eingabe['ausnahmen'] = [
            ['date' => '2026-12-24', 'closed' => '0', 'open_time' => '14:00', 'close_time' => '10:00'],
        ];

        $this->assertSame(
            ['Die Bis-Zeit muss nach der Von-Zeit liegen.'],
            $this->dienst()->einreichen($eingabe),
        );

        $this->assertSame([], $this->speicher()->ausnahmen());
    }

    /** Eine gestrichene Ausnahme verschwindet — der Absendevorgang trägt den ganzen Stand. */
    public function testGestricheneAusnahmeVerschwindet(): void
    {
        $eingabe = $this->woche();
        $eingabe['ausnahmen'] = [['date' => '2026-12-24', 'closed' => '1', 'label' => 'Heiligabend']];
        $this->dienst()->einreichen($eingabe);

        $this->assertCount(1, $this->speicher()->ausnahmen());

        $this->dienst()->einreichen($this->woche());

        $this->assertSame([], $this->speicher()->ausnahmen());
    }

    // ---------------------------------------------------------------- die Seite

    /** §8.7: Vor dem Onlinegang zeigt die Seite den Leerzustand, kein Formular. */
    public function testVorDemOnlinegangGibtEsKeinFormular(): void
    {
        $antwort = $this->router()->behandeln('GET', '/portal/inhalte');

        $this->assertSame(200, $antwort->status);

        $html = (string) $antwort->rumpf;

        $this->assertStringContainsString(Oeffnungszeitendienst::VOR_DEM_START, $html);
        $this->assertStringNotContainsString('name="tage[0][open_time]"', $html);
    }

    /** Und die Sperre steht nicht nur in der Ansicht: das Formular selbst wird abgewiesen. */
    public function testVorDemOnlinegangWirdDasFormularAbgewiesen(): void
    {
        $_POST = $this->woche() + ['_token' => \Sartu\Helpers\Csrf::token()];

        $antwort = $this->router()->behandeln('POST', '/portal/inhalte');

        $this->assertSame(404, $antwort->status);
        $this->assertSame([], $this->speicher()->wochentage());
    }

    public function testNachDemOnlinegangStehtDasFormularUndDerBannerErscheint(): void
    {
        $this->projektStatusSetzen(Projektstatus::LIVE);

        $html = (string) $this->router()->behandeln('GET', '/portal/inhalte')->rumpf;

        $this->assertStringContainsString('name="tage[0][open_time]"', $html);
        $this->assertStringNotContainsString(Oeffnungszeitendienst::BANNER_OFFEN, $html);

        $this->dienst()->einreichen($this->woche());

        $html = (string) $this->router()->behandeln('GET', '/portal/inhalte')->rumpf;

        $this->assertStringContainsString(Oeffnungszeitendienst::BANNER_OFFEN, $html);
    }

    // ---------------------------------------------------------------- der Adminknopf

    /** §9.2: `Als veröffentlicht markieren` setzt die Marke zurück und meldet es dem Kunden. */
    public function testAdminMarkiertAlsVeroeffentlicht(): void
    {
        $this->projektStatusSetzen(Projektstatus::LIVE);
        $this->dienst()->einreichen($this->woche());

        $this->assertTrue($this->speicher()->wartetAufVeroeffentlichung());

        $this->alsAdmin($this->adminId);

        $antwort = (new OeffnungszeitenSteuerung())->veroeffentlichen(['id' => $this->projektId]);

        $this->assertSame(200, $antwort->status);
        $this->assertFalse(
            (new AdminOeffnungszeiten($this->nachweis()))
                ->alsVeroeffentlichtMarkieren($this->organisationId),
            'Ein zweiter Lauf markiert noch einmal — dann ginge auch eine zweite Mail raus.',
        );

        $ereignis = $this->pdo->query(
            "SELECT COUNT(*) FROM audit_events WHERE action = 'oeffnungszeiten_veroeffentlicht'"
        )->fetchColumn();

        $this->assertSame(1, (int) $ereignis);

        $this->alsKunde($this->organisationId, $this->kundeId);
        $this->assertFalse($this->speicher()->wartetAufVeroeffentlichung());
    }

    /** §3 Regel 1: Ein fremder Betrieb sieht die Zeiten nicht. */
    public function testFremdeOeffnungszeitenSindUnerreichbar(): void
    {
        $this->projektStatusSetzen(Projektstatus::LIVE);
        $this->dienst()->einreichen($this->woche());

        $fremdeOrganisation = $this->organisationAnlegen('Betrieb B', 'b@example.org');
        $fremderKunde = $this->kundeAnlegen($fremdeOrganisation, 'b@example.org');

        $this->alsKunde($fremdeOrganisation, $fremderKunde);

        $this->assertSame([], (new KundenOeffnungszeiten(KundenBereich::ausSitzung()))->wochentage());
        $this->assertFalse((new KundenOeffnungszeiten(KundenBereich::ausSitzung()))
            ->wartetAufVeroeffentlichung());
    }

    // ---------------------------------------------------------------- Hilfsmittel

    /**
     * Eine gültige Woche: Montag bis Freitag 8 bis 17 Uhr, Wochenende geschlossen.
     *
     * @return array<string,mixed> wie `$_POST` es liefert — alle Werte sind Zeichenketten
     */
    private function woche(): array
    {
        $tage = [];

        foreach (array_keys(KundenOeffnungszeiten::TAGE) as $nummer) {
            $tage[$nummer] = $nummer >= 5
                ? ['closed' => '1', 'open_time' => '', 'close_time' => '', 'note' => '']
                : ['open_time' => '08:00', 'close_time' => '17:00', 'note' => ''];
        }

        return ['tage' => $tage, 'ausnahmen' => []];
    }

    private function dienst(): Oeffnungszeitendienst
    {
        return new Oeffnungszeitendienst(KundenBereich::ausSitzung());
    }

    private function speicher(): KundenOeffnungszeiten
    {
        return new KundenOeffnungszeiten(KundenBereich::ausSitzung());
    }

    private function nachweis(): AdminNachweis
    {
        $nachweis = AdminNachweis::ausSitzung();

        $this->assertNotNull($nachweis);

        return $nachweis;
    }

    private function projektAnlegen(): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO projects (id, organization_id, title, package, included_feedback_rounds,'
            . ' protection_level, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([$id, $this->organisationId, 'Website Mustermann', 'wachstum', 2, 'm',
            Projektstatus::PRODUKTION]);

        return $id;
    }

    private function projektStatusSetzen(string $status): void
    {
        $anweisung = $this->pdo->prepare('UPDATE projects SET status = ? WHERE id = ?');
        $anweisung->execute([$status, $this->projektId]);
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
