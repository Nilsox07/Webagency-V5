<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\Admin\AdminAnfragen;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\AnfrageSpeicher;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\Uuid;
use Sartu\Helpers\Csrf;
use Sartu\Router;
use Sartu\Services\Anfragebearbeitung;
use Sartu\Services\AnfrageService;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Loeschlauf;
use Sartu\Services\Wartungsmodus;

/**
 * Die Anfrageliste im Adminbereich und der tägliche Löschlauf.
 *
 * §0.3a zieht die Grenze: **eine Liste mit vier Zuständen und einem Umwandlungsknopf.**
 * Dieser Test prüft die Liste — und an einer Stelle auch, dass sie nicht mehr ist.
 *
 * Testfälle: 40 · 80
 */
final class AnfragelisteTest extends Datenbankfall
{
    private string $adminId;

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
        $_POST = [];
        $_GET = [];

        $this->adminId = $this->adminAnlegen();
        $this->alsAdmin($this->adminId);

        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_GET = [];

        parent::tearDown();
    }

    // ---------------------------------------------------------------- Liste und Detail

    public function testDieListeZeigtDieSiebenSpaltenAusParagraf4b5(): void
    {
        $this->anfrageAnlegen(company: 'Mustermann Sanitär GmbH');

        $html = $this->router()->behandeln('GET', '/admin/anfragen')->rumpf;

        foreach (['Eingang', 'Firma', 'Name', 'Empfohlener Umfang', 'Kennzeichen', 'Zustand', 'Löschdatum'] as $spalte) {
            $this->assertStringContainsString('>' . $spalte . '<', $html, 'Spalte fehlt: ' . $spalte);
        }

        $this->assertStringContainsString('Mustermann Sanitär GmbH', $html);
        $this->assertStringContainsString('Platzhirsch', $html);
    }

    /** §0.3b: Ein leerer Bereich sagt, wann dort etwas erscheint. Kein „Keine Daten". */
    public function testDieLeereListeErklaertSichSelbst(): void
    {
        $html = $this->router()->behandeln('GET', '/admin/anfragen')->rumpf;

        $this->assertStringContainsString('Sobald jemand den Bedarfsscheck abschickt', $html);
        $this->assertStringNotContainsString('Keine Daten', $html);
    }

    public function testFilterNachZustandUndKampagne(): void
    {
        $offen = $this->anfrageAnlegen(company: 'Offener Betrieb');
        $abgelehnt = $this->anfrageAnlegen(company: 'Abgelehnter Betrieb', utmSource: 'anzeigentest');

        (new AdminAnfragen($this->nachweis()))->zustandSetzen($abgelehnt, 'abgelehnt');

        $_GET = ['zustand' => 'abgelehnt'];
        $html = $this->router()->behandeln('GET', '/admin/anfragen')->rumpf;

        $this->assertStringContainsString('Abgelehnter Betrieb', $html);
        $this->assertStringNotContainsString('Offener Betrieb', $html);

        $_GET = ['quelle' => 'anzeigentest'];
        $html = $this->router()->behandeln('GET', '/admin/anfragen')->rumpf;

        $this->assertStringContainsString('Abgelehnter Betrieb', $html);
        $this->assertStringNotContainsString('Offener Betrieb', $html);

        // Ein erfundener Filterwert filtert nicht — er ist kein Fehler.
        $_GET = ['zustand' => 'erfunden'];
        $html = $this->router()->behandeln('GET', '/admin/anfragen')->rumpf;

        $this->assertStringContainsString('Offener Betrieb', $html);
        $this->assertStringContainsString('Abgelehnter Betrieb', $html);

        $this->assertNotSame('', $offen);
    }

    /** §4b.5 — „alle Antworten in Klartext als Frage → Antwort, nicht als Rohdaten". */
    public function testDieDetailansichtZeigtFrageUndAntwortInKlartext(): void
    {
        $id = $this->anfrageAnlegen();

        $html = $this->router()->behandeln('GET', '/admin/anfragen/' . $id)->rumpf;

        $this->assertStringContainsString('Was bietet Ihr Unternehmen an?', $html);
        $this->assertStringContainsString('Wir arbeiten in mehreren Regionen oder an mehreren Standorten', $html);

        // Der Systemwert steht nirgends — weder als Schlüssel noch als Antwort.
        $this->assertStringNotContainsString('mehrere_regionen', $html);
        $this->assertStringNotContainsString('umfangssignale', $html);
    }

    /** §4b.2 — ein später dazugekommenes Feld verschwindet nicht aus der Detailansicht. */
    public function testEinUnbekanntesFeldTauchtInDerDetailansichtAuf(): void
    {
        $id = $this->anfrageAnlegen(zusatz: ['lieblingsfarbe' => 'Lindgrün']);

        $html = $this->router()->behandeln('GET', '/admin/anfragen/' . $id)->rumpf;

        $this->assertStringContainsString('lieblingsfarbe', $html);
        $this->assertStringContainsString('Lindgrün', $html);
    }

    public function testUnbekannteAnfrageLiefert404(): void
    {
        $antwort = $this->router()->behandeln('GET', '/admin/anfragen/' . Uuid::v4());

        $this->assertSame(404, $antwort->status);
    }

    // ---------------------------------------------------------------- Zustände

    /** §4b.5 — „Als abgelehnt markieren **mit Pflichtnotiz**". */
    public function testAblehnenOhneNotizWirdAbgewiesen(): void
    {
        $id = $this->anfrageAnlegen();

        $fehler = (new Anfragebearbeitung($this->nachweis()))->zustandSetzen($id, 'abgelehnt', '', null);

        $this->assertNotNull($fehler);
        $this->assertSame('neu', (string) $this->anfrage($id)['status']);
    }

    /**
     * §4b.4 und §15.1 — die Ablehnung verkürzt die Frist von zwölf auf sechs Monate.
     *
     * „Die kürzere Frist gilt für den engeren Fall." Beim Anlegen gilt die längere.
     */
    public function testAblehnenVerkuerztDieLoeschfristAufSechsMonate(): void
    {
        $id = $this->anfrageAnlegen();
        $eingang = (string) $this->anfrage($id)['submitted_at'];

        $this->assertSame(
            (new \DateTimeImmutable($eingang))->modify('+12 months')->format('Y-m-d'),
            (string) $this->anfrage($id)['delete_after'],
        );

        $fehler = (new Anfragebearbeitung($this->nachweis()))
            ->zustandSetzen($id, 'abgelehnt', 'Kein Unternehmen, Privatperson.', '127.0.0.1');

        $this->assertNull($fehler);

        $nachher = $this->anfrage($id);

        $this->assertSame('abgelehnt', (string) $nachher['status']);
        $this->assertSame(
            (new \DateTimeImmutable($eingang))->modify('+6 months')->format('Y-m-d'),
            (string) $nachher['delete_after'],
        );
        $this->assertSame('Kein Unternehmen, Privatperson.', (string) $nachher['admin_note']);
    }

    /** §3 Regel 9: Ein Zustandswechsel erzeugt ein Audit-Ereignis mit Akteur und Grund. */
    public function testZustandswechselErzeugtEinAuditEreignisMitGrund(): void
    {
        $id = $this->anfrageAnlegen();

        (new Anfragebearbeitung($this->nachweis()))
            ->zustandSetzen($id, 'in_pruefung', 'Rückfrage zur Domain offen.', '127.0.0.1');

        $ereignis = $this->letztesEreignis('anfrage_zustand_geaendert');

        $this->assertSame($id, (string) $ereignis['entity_id']);
        $this->assertSame($this->adminId, (string) $ereignis['actor_user_id']);
        $this->assertSame('neu', (string) $ereignis['old_value']);
        $this->assertSame('in_pruefung', (string) $ereignis['new_value']);
        $this->assertSame('Rückfrage zur Domain offen.', (string) $ereignis['reason']);
    }

    // ---------------------------------------------------------------- Fall 40 und 80

    /**
     * Fall 40, zweite Hälfte — `Endgültig löschen` entfernt den Datensatz und hinterlässt
     * ein Audit-Ereignis **ohne** die gelöschten Inhalte.
     */
    public function testEndgueltigLoeschenHinterlaesstKeinenInhaltImProtokoll(): void
    {
        $id = $this->anfrageAnlegen(company: 'Geheimbetrieb GmbH', email: 'chef@geheimbetrieb.de');

        $fehler = (new Anfragebearbeitung($this->nachweis()))
            ->endgueltigLoeschen($id, 'Löschverlangen des Betroffenen.', '127.0.0.1');

        $this->assertNull($fehler);
        $this->assertNull($this->anfrage($id));

        $ereignis = $this->letztesEreignis('anfrage_endgueltig_geloescht');
        $alles = implode(' ', array_map(static fn ($w) => (string) $w, $ereignis));

        $this->assertSame($id, (string) $ereignis['entity_id']);
        $this->assertStringNotContainsString('Geheimbetrieb', $alles, 'Der Firmenname steht im Protokoll.');
        $this->assertStringNotContainsString('geheimbetrieb.de', $alles, 'Die Adresse steht im Protokoll.');
    }

    public function testLoeschenOhneGrundWirdAbgewiesen(): void
    {
        $id = $this->anfrageAnlegen();

        $fehler = (new Anfragebearbeitung($this->nachweis()))->endgueltigLoeschen($id, '   ', null);

        $this->assertNotNull($fehler);
        $this->assertNotNull($this->anfrage($id));
    }

    /**
     * Fall 40, erste Hälfte — `source_ip` ist nach 30 Tagen geleert, der übrige Datensatz
     * unverändert.
     */
    public function testHerkunftsadresseWirdNachDreissigTagenGeleert(): void
    {
        $frisch = $this->anfrageAnlegen(company: 'Frisch GmbH');
        $alt = $this->anfrageAnlegen(company: 'Alt GmbH');

        $this->eingangVerschieben($alt, Loeschlauf::IP_TAGE + 1);

        $vorher = $this->anfrage($alt);
        $stand = (new Loeschlauf())->ausfuehren();
        $nachher = $this->anfrage($alt);

        $this->assertSame(1, $stand['ip_geleert']);
        $this->assertNull($nachher['source_ip'], 'Die Adresse steht noch da.');
        $this->assertNotNull($this->anfrage($frisch)['source_ip'], 'Eine frische Anfrage wurde mit geleert.');

        // Der übrige Datensatz bleibt — ausdrücklich, nicht nebenbei.
        foreach (['company', 'email', 'first_name', 'payload', 'recommended_package', 'flag'] as $feld) {
            $this->assertSame($vorher[$feld], $nachher[$feld], 'Feld verändert: ' . $feld);
        }

        // Ein zweiter Lauf am selben Tag findet nichts mehr.
        $this->assertSame(0, (new Loeschlauf())->ausfuehren()['ip_geleert']);
    }

    /** Fall 80 — eine nicht umgewandelte Anfrage wird nach zwölf Monaten gelöscht. */
    public function testNichtUmgewandelteAnfrageWirdNachAblaufDerFristGeloescht(): void
    {
        $faellig = $this->anfrageAnlegen(company: 'Abgelaufen GmbH');
        $offen = $this->anfrageAnlegen(company: 'Noch gültig GmbH');

        $this->fristVerschieben($faellig, '-1 day');

        $stand = (new Loeschlauf())->ausfuehren();

        $this->assertSame(1, $stand['geloescht']);
        $this->assertNull($this->anfrage($faellig));
        $this->assertNotNull($this->anfrage($offen));

        $ereignis = $this->letztesEreignis('anfrage_frist_geloescht');
        $alles = implode(' ', array_map(static fn ($w) => (string) $w, $ereignis));

        $this->assertSame($faellig, (string) $ereignis['entity_id']);
        $this->assertStringNotContainsString('Abgelaufen GmbH', $alles);
    }

    /** §4b.4 — eine umgewandelte Anfrage ist Teil der Kundenakte und bleibt. */
    public function testUmgewandelteAnfrageWirdNieAutomatischGeloescht(): void
    {
        $id = $this->anfrageAnlegen();
        $organisation = $this->organisationAnlegen('Kunde GmbH', 'kunde@example.org');

        (new AdminAnfragen($this->nachweis()))->alsUmgewandeltVermerken($id, $organisation);
        $this->fristVerschieben($id, '-400 days');

        $stand = (new Loeschlauf())->ausfuehren();

        $this->assertSame(0, $stand['geloescht']);
        $this->assertNotNull($this->anfrage($id), 'Eine umgewandelte Anfrage wurde gelöscht.');
    }

    // ---------------------------------------------------------------- Export

    /** §4b.4 Betroffenenrecht: der Export enthält alles, was gespeichert ist. */
    public function testDerExportEnthaeltAllesUndKommtAlsDatei(): void
    {
        $id = $this->anfrageAnlegen(company: 'Mustermann Sanitär GmbH');

        $antwort = $this->router()->behandeln('GET', '/admin/anfragen/' . $id . '/export');

        $this->assertSame(200, $antwort->status);
        $this->assertStringContainsString('application/json', $antwort->kopfzeilen['Content-Type']);
        $this->assertStringContainsString('attachment', $antwort->kopfzeilen['Content-Disposition']);

        $daten = json_decode($antwort->rumpf, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('Mustermann Sanitär GmbH', $daten['company']);
        $this->assertSame('platzhirsch', $daten['recommended_package']);
        // Der Bestand kommt als Struktur, nicht als Zeichenkette in einer Zeichenkette.
        $this->assertIsArray($daten['payload']);
        $this->assertSame('48431', $daten['payload']['einsatzort']);
    }

    // ---------------------------------------------------------------- die Grenze aus §0.3a

    /**
     * §0.3a — was hier NICHT stehen darf.
     *
     * Der Test ist bewusst grob: Er sucht Wörter, die zu einem Vertriebssystem gehören.
     * Taucht eines auf, ist die Grenze entweder überschritten oder es steht eine Erklärung
     * daneben, die hier nicht hingehört.
     */
    public function testDieListeIstKeinVertriebssystem(): void
    {
        $this->anfrageAnlegen();

        $liste = $this->router()->behandeln('GET', '/admin/anfragen')->rumpf;
        $detail = $this->router()->behandeln('GET', '/admin/anfragen/' . $this->ersteId())->rumpf;

        foreach (['Pipeline', 'Kanban', 'Trichter', 'Bewertung', 'Punkte', 'Priorität',
                  'Nachfassen', 'Kampagne starten', 'Zuweisen'] as $verboten) {
            $this->assertStringNotContainsString($verboten, $liste, 'In der Liste: ' . $verboten);
            $this->assertStringNotContainsString($verboten, $detail, 'In der Detailansicht: ' . $verboten);
        }
    }

    // ---------------------------------------------------------------- Hilfsmittel

    /**
     * @param array<string,mixed> $zusatz
     */
    private function anfrageAnlegen(
        string $company = 'Mustermann Sanitär GmbH',
        string $email = 'erika@example.org',
        ?string $utmSource = null,
        array $zusatz = [],
    ): string {
        $eingabe = [
            'submission_id'      => Uuid::v4(),
            'form_started_at'    => (string) (time() - 60),
            'first_name'         => 'Erika',
            'last_name'          => 'Mustermann',
            'company'            => $company,
            'email'              => $email,
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
        ] + $zusatz;

        $ergebnis = (new AnfrageService(null, new \Sartu\Services\Ratenbegrenzung($this->arbeitsverzeichnis)))
            ->anlegen($eingabe, $utmSource === null ? [] : ['utm_source' => $utmSource], '198.51.100.7');

        $this->assertTrue($ergebnis->wurdeGespeichert(), 'Die Testanfrage liess sich nicht anlegen.');

        return (string) $ergebnis->anfrageId;
    }

    /** Verschiebt den Eingang in die Vergangenheit, ohne den Rest anzufassen. */
    private function eingangVerschieben(string $id, int $tage): void
    {
        $anweisung = $this->pdo->prepare(
            'UPDATE leads SET submitted_at = DATE_SUB(submitted_at, INTERVAL ? DAY) WHERE id = ?'
        );
        $anweisung->execute([$tage, $id]);
    }

    private function fristVerschieben(string $id, string $verschiebung): void
    {
        $neu = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->modify($verschiebung)->format('Y-m-d');

        $anweisung = $this->pdo->prepare('UPDATE leads SET delete_after = ? WHERE id = ?');
        $anweisung->execute([$neu, $id]);
    }

    /** @return array<string,mixed>|null */
    private function anfrage(string $id): ?array
    {
        $anweisung = $this->pdo->prepare('SELECT * FROM leads WHERE id = ?');
        $anweisung->execute([$id]);

        $zeile = $anweisung->fetch(\PDO::FETCH_ASSOC);

        return is_array($zeile) ? $zeile : null;
    }

    private function ersteId(): string
    {
        return (string) $this->pdo->query('SELECT id FROM leads LIMIT 1')->fetchColumn();
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
}
