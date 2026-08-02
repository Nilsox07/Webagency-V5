<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminVorschau;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenFreigaben;
use Sartu\Data\Customer\KundenVorschau;
use Sartu\Data\Uuid;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Format;
use Sartu\Router;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Projektstatus;
use Sartu\Services\Projektwechsel;
use Sartu\Services\Vorschaudienst;
use Sartu\Services\Wartungsmodus;
use Sartu\Admin\VorschauSteuerung as AdminVorschauSteuerung;

/**
 * Die Strecke, an der Stufe A3 gemessen wird.
 *
 * `REIHENFOLGE.md`, „Fertig, wenn": *„Ein Projekt erreicht `live`."*
 *
 * Testfälle: 18 · 25 · 28 · 53b · 56 · 63.
 *
 * Der erste Test läuft die ganze Strecke ab `produktion`: Vorschau, Rückmeldung, Einreichen,
 * Einarbeiten, zweite Vorschau, Abnahme, Onlinegang. Er endet auf `live` — das ist die
 * Bedingung der Etappe, und sie wird nicht in Einzelteilen behauptet, sondern einmal am
 * Stück gefahren.
 */
final class LivegangTest extends Datenbankfall
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
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_GET = [];

        parent::tearDown();
    }

    // ---------------------------------------------------------------- die ganze Strecke

    public function testVonDerProduktionUeberVorschauUndAbnahmeBisLive(): void
    {
        // 1 — der Admin stellt die Vorschau bereit. §5.6a Punkt 1: die Runde öffnet sich mit.
        $this->vorschauBereitstellen('https://vorschau.example.org/mustermann');

        $this->assertSame(Projektstatus::VORSCHAU, $this->projektstatus());
        $this->assertSame('https://vorschau.example.org/mustermann', (string) $this->projekt()['preview_url']);

        $runde = $this->aktuelleRunde();
        $this->assertSame(1, (int) $runde['number']);
        $this->assertSame('offen', (string) $runde['status']);
        $this->assertSame(1, (int) $runde['included']);

        // 2 — der Kunde meldet zurück und reicht gebündelt ein.
        $this->alsKunde($this->organisationId, $this->kundeId);
        $dienst = new Vorschaudienst($this->bereich());

        $this->assertSame([], $dienst->rueckmeldungSenden(
            $this->projektId,
            ['body' => 'Auf der Startseite fehlt die Telefonnummer.', 'page_hint' => 'Startseite'],
            $this->kundeId,
        ));

        $this->assertSame([], $dienst->einreichen($this->projektId, $this->kundeId, '127.0.0.1'));
        $this->assertSame(Projektstatus::KORREKTUR, $this->projektstatus());
        $this->assertSame('eingereicht', (string) $this->aktuelleRunde()['status']);

        // §5.6a Punkt 3: „Danach sind in dieser Runde keine weiteren Einträge möglich."
        $fehler = $dienst->rueckmeldungSenden(
            $this->projektId,
            ['body' => 'Und noch etwas.'],
            $this->kundeId,
        );

        $this->assertNotSame([], $fehler);
        $this->assertSame(1, (new KundenVorschau($this->bereich()))->anzahlRueckmeldungen((string) $runde['id']));

        // 3 — der Admin arbeitet ein und stellt die zweite Vorschau bereit.
        $this->alsAdmin($this->adminId);
        $_POST = ['runde' => (string) $runde['id']];
        $this->assertSame(200, $this->steuerung()->rundeAbschliessen(['id' => $this->projektId])->status);
        $this->assertSame('bearbeitet', (string) $this->runde((string) $runde['id'])['status']);

        $this->vorschauBereitstellen('https://vorschau.example.org/mustermann-2');
        $this->assertSame(Projektstatus::VORSCHAU, $this->projektstatus());
        $this->assertSame(2, (int) $this->aktuelleRunde()['number']);

        // 4 — keine weiteren Änderungen: zur Abnahme.
        $this->alsAdmin($this->adminId);
        $_POST = [];
        $this->assertSame(200, $this->steuerung()->zurAbnahme(['id' => $this->projektId])->status);
        $this->assertSame(Projektstatus::ABNAHME, $this->projektstatus());

        // 5 — der Kunde nimmt ab. Testfall 18.
        $this->alsKunde($this->organisationId, $this->kundeId);

        $this->assertSame([], (new Vorschaudienst($this->bereich()))->abnehmen(
            $this->projektId,
            ['bestaetigung' => '1', 'granted_name' => 'Erika Mustermann'],
            $this->kundeId,
            '127.0.0.1',
        ));

        $this->assertSame(Projektstatus::LAUNCH_VORBEREITUNG, $this->projektstatus());

        // 6 — der Onlinegang. Testfall 28.
        $this->alsAdmin($this->adminId);
        $_POST = ['live_url' => 'https://mustermann-sanitaer.example', 'protection_started_on' => '2026-08-02'];
        $this->assertSame(200, $this->steuerung()->livegang(['id' => $this->projektId])->status);

        $this->assertSame(Projektstatus::LIVE, $this->projektstatus());

        $projekt = $this->projekt();
        $this->assertSame('https://mustermann-sanitaer.example', (string) $projekt['live_url']);
        $this->assertNotNull($projekt['launched_at']);
        $this->assertSame('2026-08-02', (string) $projekt['protection_started_on']);
        $this->assertSame('2027-08-02', (string) $projekt['protection_min_term_until']);
    }

    // ---------------------------------------------------------------- Einzelfälle

    /**
     * Testfall 18 — die Abnahme erzeugt `approvals` mit `kind = abnahme` **und** ein
     * Audit-Ereignis.
     */
    public function testAbnahmeErzeugtApprovalUndAudit(): void
    {
        $this->projektStatusSetzen(Projektstatus::ABNAHME);
        $this->alsKunde($this->organisationId, $this->kundeId);

        $this->assertSame([], (new Vorschaudienst($this->bereich()))->abnehmen(
            $this->projektId,
            ['bestaetigung' => '1', 'granted_name' => 'Erika Mustermann'],
            $this->kundeId,
            '127.0.0.1',
        ));

        $eintrag = (new KundenFreigaben($this->bereich()))->finden($this->projektId, KundenFreigaben::ABNAHME);

        $this->assertNotNull($eintrag, 'Es gibt keinen Eintrag in approvals.');
        $this->assertSame('abnahme', (string) $eintrag['kind']);
        $this->assertSame('Erika Mustermann', (string) $eintrag['granted_name']);
        $this->assertSame('127.0.0.1', (string) $eintrag['granted_ip']);
        $this->assertNotNull($eintrag['granted_at']);

        $ereignis = $this->letztesEreignis('abnahme_erklaert');
        $this->assertSame($this->kundeId, (string) $ereignis['actor_user_id']);
        $this->assertStringContainsString('Erika Mustermann', (string) $ereignis['reason']);

        // §4: „Eine Erklärung ist einmalig." Der eindeutige Schlüssel entscheidet, nicht eine Abfrage.
        $zweite = (new KundenFreigaben($this->bereich()))
            ->erklaeren($this->projektId, KundenFreigaben::ABNAHME, $this->kundeId, 'Jemand anders', null);

        $this->assertFalse($zweite);
    }

    /**
     * §8.1 Block 3, dritte Zeile — die ausstehende Freigabe.
     *
     * Gemeint ist die Abnahme: Sie ist keine Aufgabe und stünde sonst nirgends im Cockpit.
     * Sobald sie erklärt ist, verschwindet die Zeile — ein offener Punkt, der erledigt ist,
     * bleibt kein offener Punkt.
     */
    public function testCockpitZeigtDieAusstehendeAbnahmeAlsOffenenPunkt(): void
    {
        $this->projektStatusSetzen(Projektstatus::ABNAHME);
        $this->alsKunde($this->organisationId, $this->kundeId);

        $rumpf = $this->router()->behandeln('GET', '/portal')->rumpf;

        $this->assertStringContainsString('Offene Punkte', $rumpf);
        $this->assertStringContainsString('Ihre Abnahme steht noch aus', $rumpf);
        $this->assertStringContainsString('/portal/vorschau', $rumpf);

        $this->assertSame([], (new Vorschaudienst($this->bereich()))->abnehmen(
            $this->projektId,
            ['bestaetigung' => '1', 'granted_name' => 'Erika Mustermann'],
            $this->kundeId,
            '127.0.0.1',
        ));

        $rumpf = $this->router()->behandeln('GET', '/portal')->rumpf;

        $this->assertStringNotContainsString('Ihre Abnahme steht noch aus', $rumpf);
    }

    /** Die Abnahme ohne Ankreuzen und ohne getippten Namen scheitert — §8.4. */
    public function testAbnahmeOhneBestaetigungUndNamenScheitert(): void
    {
        $this->projektStatusSetzen(Projektstatus::ABNAHME);
        $this->alsKunde($this->organisationId, $this->kundeId);

        $fehler = (new Vorschaudienst($this->bereich()))
            ->abnehmen($this->projektId, ['granted_name' => '  '], $this->kundeId, '127.0.0.1');

        $this->assertCount(2, $fehler);
        $this->assertSame(Projektstatus::ABNAHME, $this->projektstatus());
        $this->assertNull((new KundenFreigaben($this->bereich()))
            ->finden($this->projektId, KundenFreigaben::ABNAHME));
    }

    /**
     * Testfall 25 — die zweite Runde bei Paket **Start** ist `included = false`.
     *
     * Start bringt **eine** enthaltene Korrekturrunde mit. Die zweite steht damit ausserhalb
     * des Festpreises. Geprüft wird beides: das Kennzeichen in der Zeile **und** dass der
     * Kunde es liest — §5.6a verlangt, dass die Grenze sichtbar wird, nicht dass sie sperrt.
     */
    public function testZweiteRundeBeiPaketStartIstNichtEnthalten(): void
    {
        $this->paketSetzen('start', 1);

        $this->vorschauBereitstellen('https://vorschau.example.org/start-1');
        $this->assertSame(1, (int) $this->aktuelleRunde()['included']);

        // Runde 1 durchlaufen: einreichen, einarbeiten.
        $this->alsKunde($this->organisationId, $this->kundeId);
        $dienst = new Vorschaudienst($this->bereich());
        $dienst->rueckmeldungSenden($this->projektId, ['body' => 'Eine Rückmeldung.'], $this->kundeId);
        $dienst->einreichen($this->projektId, $this->kundeId, '127.0.0.1');

        $this->alsAdmin($this->adminId);
        $_POST = ['runde' => (string) $this->aktuelleRunde()['id']];
        $this->steuerung()->rundeAbschliessen(['id' => $this->projektId]);

        // Die zweite Vorschau öffnet Runde 2 — sie ist nicht mehr enthalten.
        $this->vorschauBereitstellen('https://vorschau.example.org/start-2');

        $zweite = $this->aktuelleRunde();
        $this->assertSame(2, (int) $zweite['number']);
        $this->assertSame(0, (int) $zweite['included'], 'Die zweite Runde gilt als enthalten.');

        // §5.6a: Das Portal blockiert nichts. Die Runde läuft wie jede andere.
        $this->alsKunde($this->organisationId, $this->kundeId);
        $this->assertSame([], (new Vorschaudienst($this->bereich()))->rueckmeldungSenden(
            $this->projektId,
            ['body' => 'Auch in der zusätzlichen Runde.'],
            $this->kundeId,
        ));

        // Und der Kunde sieht den Hinweis im Klartext, bevor er einreicht.
        $seite = $this->router()->behandeln('GET', '/portal/vorschau');
        $this->assertSame(200, $seite->status);
        $this->assertStringContainsString(
            'im Festpreis nicht mehr enthalten',
            strip_tags((string) $seite->rumpf),
        );
    }

    /**
     * Testfall 28 — `protection_started_on` beim Wechsel auf `live`,
     * `protection_min_term_until` genau zwölf Monate später.
     *
     * Ohne getipptes Datum ist es der heutige Tag (§5.7 „Vorbelegung: heutiges Datum") — in
     * **Anzeigezeit**, nicht in UTC. Nach Mitternacht deutscher Zeit wäre es sonst der Vortag.
     */
    public function testOnlinegangSetztBetriebsbeginnUndMindestlaufzeit(): void
    {
        $this->projektStatusSetzen(Projektstatus::LAUNCH_VORBEREITUNG);
        $this->alsAdmin($this->adminId);

        $_POST = ['live_url' => 'https://mustermann-sanitaer.example'];
        $this->assertSame(200, $this->steuerung()->livegang(['id' => $this->projektId])->status);

        $heute = Format::heute();
        $projekt = $this->projekt();

        $this->assertSame(Projektstatus::LIVE, (string) $projekt['status']);
        $this->assertSame($heute, (string) $projekt['protection_started_on']);
        $this->assertSame(
            (new \DateTimeImmutable($heute))->modify('+12 months')->format('Y-m-d'),
            (string) $projekt['protection_min_term_until'],
        );
    }

    /** §5.7: Eine Adresse ohne https bricht den Onlinegang ab — ohne Teileffekt. */
    public function testOnlinegangOhneHttpsWirdAbgewiesen(): void
    {
        $this->projektStatusSetzen(Projektstatus::LAUNCH_VORBEREITUNG);
        $this->alsAdmin($this->adminId);

        $_POST = ['live_url' => 'http://mustermann-sanitaer.example'];
        $this->steuerung()->livegang(['id' => $this->projektId]);

        $projekt = $this->projekt();
        $this->assertSame(Projektstatus::LAUNCH_VORBEREITUNG, (string) $projekt['status']);
        $this->assertNull($projekt['live_url']);
        $this->assertNull($projekt['protection_started_on']);
    }

    /**
     * Testfall 53b — die Änderung von `protection_started_on` erzeugt ein Audit-Ereignis mit
     * Grundlagentext, und ohne Grundlagentext lässt sie sich nicht speichern (§12).
     */
    public function testBetriebsbeginnAendernBrauchtEinenGrundlagentext(): void
    {
        $this->projektStatusSetzen(Projektstatus::LIVE);
        $this->betriebsbeginnSetzen('2026-08-02');
        $this->alsAdmin($this->adminId);

        // Ohne Grund: keine Änderung.
        $_POST = ['protection_started_on' => '2026-07-01'];
        $this->steuerung()->betriebsbeginn(['id' => $this->projektId]);

        $this->assertSame('2026-08-02', (string) $this->projekt()['protection_started_on']);

        // Mit Grund: Änderung, Audit-Ereignis, neu gerechnete Mindestlaufzeit.
        $_POST = [
            'protection_started_on' => '2026-07-01',
            'grund' => 'Website war am 01.07.2026 abgenommen und bereitgestellt; '
                . 'Onlinegang vom Kunden verzögert, schriftlich angekündigt am 20.06.2026.',
        ];
        $this->assertSame(200, $this->steuerung()->betriebsbeginn(['id' => $this->projektId])->status);

        $projekt = $this->projekt();
        $this->assertSame('2026-07-01', (string) $projekt['protection_started_on']);
        $this->assertSame('2027-07-01', (string) $projekt['protection_min_term_until']);

        $ereignis = $this->letztesEreignis('betriebsbeginn_geaendert');
        $this->assertSame($this->adminId, (string) $ereignis['actor_user_id']);
        $this->assertSame('2026-08-02', (string) $ereignis['old_value']);
        $this->assertSame('2026-07-01', (string) $ereignis['new_value']);
        $this->assertStringContainsString('schriftlich angekündigt', (string) $ereignis['reason']);
        $this->assertSame('127.0.0.1', (string) $ereignis['ip']);
    }

    /**
     * Testfall 63 — `live → korrektur` wird abgewiesen.
     *
     * Das Paar steht nicht in §5.1a. Geprüft wird zusätzlich, dass **nichts** geschrieben
     * wurde: kein Status, kein Audit-Ereignis (§5.1a „kein Teileffekt").
     */
    public function testVonLiveGehtEsNichtZurueckInDieKorrektur(): void
    {
        $this->projektStatusSetzen(Projektstatus::LIVE);

        $vorher = $this->anzahlEreignisse();

        $fehler = (new Projektwechsel())->wechseln(
            $this->projektId,
            $this->organisationId,
            Projektstatus::KORREKTUR,
            Projektstatus::ADMIN,
            $this->adminId,
            'Der Kunde möchte doch noch etwas ändern.',
            '127.0.0.1',
        );

        $this->assertSame('Dieser Schritt ist an dieser Stelle nicht vorgesehen.', $fehler);
        $this->assertSame(Projektstatus::LIVE, $this->projektstatus());
        $this->assertSame($vorher, $this->anzahlEreignisse());

        // §5.1a kennt auch keine Pause aus `live` heraus.
        $this->assertNotNull((new Projektwechsel())->pausieren(
            $this->projektId,
            $this->organisationId,
            'Ein Grund.',
            $this->adminId,
            '127.0.0.1',
        ));

        $this->assertSame(Projektstatus::LIVE, $this->projektstatus());
    }

    /**
     * Testfall 56 — alle Kernabläufe funktionieren mit deaktiviertem JavaScript.
     *
     * Geprüft wird nicht „es gibt kein Skript", sondern die Bedingung, die das Lastenheft
     * meint: **jede Handlung ist ein normales Formular**. Konkret drei Dinge, die JavaScript
     * voraussetzen würden — ein `on…`-Attribut, ein Knopf ausserhalb eines Formulars und ein
     * Verweis auf `href="#"` oder `javascript:`.
     *
     * Dazu die Gegenprobe: Jedes `POST`-Formular trägt ein CSRF-Feld (§3 Regel 7). Ein
     * Formular, dessen Feld erst ein Skript nachträgt, wäre ohne JavaScript unbedienbar.
     */
    public function testKernablaeufeBrauchenKeinJavaScript(): void
    {
        $this->vorschauBereitstellen('https://vorschau.example.org/mustermann');
        $this->alsKunde($this->organisationId, $this->kundeId);

        foreach ([
            '/portal', '/portal/angebot', '/portal/aufgaben', '/portal/vorschau',
            '/portal/rechnungen', '/portal/domain', '/portal/vertrag', '/portal/hilfe',
        ] as $pfad) {
            $antwort = $this->router()->behandeln('GET', $pfad);

            $this->assertSame(200, $antwort->status, $pfad . ' antwortet nicht.');

            $html = (string) $antwort->rumpf;

            $this->assertSame(0, preg_match('/\son[a-z]+\s*=/i', $html), $pfad . ' hat ein on…-Attribut.');
            $this->assertStringNotContainsString('javascript:', $html, $pfad . ' hat einen javascript:-Verweis.');
            $this->assertSame(0, preg_match('/href\s*=\s*"#"/', $html), $pfad . ' hat einen Verweis ins Leere.');

            // Jeder Knopf steht in einem Formular — ausserhalb tut er ohne Skript nichts.
            $ohneFormulare = preg_replace('/<form\b.*?<\/form>/is', '', $html) ?? $html;

            $this->assertStringNotContainsString(
                '<button',
                $ohneFormulare,
                $pfad . ' hat einen Knopf ausserhalb eines Formulars.',
            );

            preg_match_all('/<form\b[^>]*method\s*=\s*"post"[^>]*>(.*?)<\/form>/is', $html, $treffer);

            foreach ($treffer[1] as $inhalt) {
                $this->assertStringContainsString(
                    Csrf::FELD,
                    $inhalt,
                    $pfad . ' hat ein POST-Formular ohne CSRF-Feld.',
                );
            }
        }
    }

    // ---------------------------------------------------------------- Hilfsmittel

    private function bereich(): KundenBereich
    {
        return KundenBereich::ausSitzung();
    }

    private function nachweis(): AdminNachweis
    {
        $nachweis = AdminNachweis::ausSitzung();

        $this->assertNotNull($nachweis);

        return $nachweis;
    }

    private function steuerung(): AdminVorschauSteuerung
    {
        return new AdminVorschauSteuerung();
    }

    private function vorschauBereitstellen(string $adresse): void
    {
        if ($this->projektstatus() === Projektstatus::ANGEBOT_OFFEN) {
            $this->projektStatusSetzen(Projektstatus::PRODUKTION);
        }

        $this->alsAdmin($this->adminId);
        $_POST = ['preview_url' => $adresse];

        $antwort = $this->steuerung()->vorschauBereitstellen(['id' => $this->projektId]);

        $this->assertSame(200, $antwort->status);
        $_POST = [];
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

    private function paketSetzen(string $paket, int $runden): void
    {
        $anweisung = $this->pdo->prepare(
            'UPDATE projects SET package = ?, included_feedback_rounds = ? WHERE id = ?'
        );
        $anweisung->execute([$paket, $runden, $this->projektId]);
    }

    private function betriebsbeginnSetzen(string $datum): void
    {
        $anweisung = $this->pdo->prepare(
            'UPDATE projects SET protection_started_on = ?, protection_min_term_until = ? WHERE id = ?'
        );
        $anweisung->execute([$datum, AdminVorschau::mindestlaufzeitEnde($datum), $this->projektId]);
    }

    private function projektStatusSetzen(string $status): void
    {
        $anweisung = $this->pdo->prepare('UPDATE projects SET status = ? WHERE id = ?');
        $anweisung->execute([$status, $this->projektId]);
    }

    private function projektstatus(): string
    {
        return (string) $this->projekt()['status'];
    }

    /** @return array<string,mixed> */
    private function projekt(): array
    {
        $anweisung = $this->pdo->prepare('SELECT * FROM projects WHERE id = ?');
        $anweisung->execute([$this->projektId]);

        return (array) $anweisung->fetch(\PDO::FETCH_ASSOC);
    }

    /** @return array<string,mixed> */
    private function aktuelleRunde(): array
    {
        $anweisung = $this->pdo->prepare(
            'SELECT * FROM feedback_rounds WHERE project_id = ? ORDER BY number DESC LIMIT 1'
        );
        $anweisung->execute([$this->projektId]);

        $zeile = $anweisung->fetch(\PDO::FETCH_ASSOC);

        $this->assertIsArray($zeile, 'Es ist keine Korrekturrunde offen.');

        return $zeile;
    }

    /** @return array<string,mixed> */
    private function runde(string $id): array
    {
        $anweisung = $this->pdo->prepare('SELECT * FROM feedback_rounds WHERE id = ?');
        $anweisung->execute([$id]);

        return (array) $anweisung->fetch(\PDO::FETCH_ASSOC);
    }

    private function anzahlEreignisse(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM audit_events')->fetchColumn();
    }

    /** @return array<string,mixed> */
    private function letztesEreignis(string $aktion): array
    {
        $anweisung = $this->pdo->prepare(
            'SELECT * FROM audit_events WHERE action = ? ORDER BY created_at DESC, id DESC LIMIT 1'
        );
        $anweisung->execute([$aktion]);

        $zeile = $anweisung->fetch(\PDO::FETCH_ASSOC);

        $this->assertIsArray($zeile, 'Kein Audit-Ereignis fuer ' . $aktion . '.');

        return $zeile;
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
