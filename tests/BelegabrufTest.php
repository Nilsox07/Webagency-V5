<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminRechnungen;
use Sartu\Data\Belege;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\Customer\KundenBelege;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Uuid;
use Sartu\Router;
use Sartu\Services\Belegversand;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Projektstatus;
use Sartu\Services\Rechnungsdienst;
use Sartu\Services\Steuerexport;
use Sartu\Services\Wartungsmodus;

/**
 * Abruf, Versand und Übergabe — `18_BELEGE_UND_ZAHLUNG.md` Abschnitte 8 und 9.
 *
 * Testfall: 95. Dazu die Mandantenprüfung des Belegabrufs, die zur selben Bauzeit gehört:
 * §9 verlangt sie ausdrücklich doppelt und mit **404**, nicht 403.
 */
final class BelegabrufTest extends Datenbankfall
{
    private string $adminId;

    private string $organisationId;

    private string $projektId;

    private string $rechnungId;

    private string $belegId;

    private Postfach $postfach;

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
        $_POST = [];
        $_GET = [];

        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);

        $this->betreiberdatenAnlegen();
        $this->postfach = new Postfach();

        $this->adminId = $this->adminAnlegen();
        $this->organisationId = $this->organisationAnlegen('Mustermann Sanitär GmbH', 'erika@example.org');
        $this->kundeAnlegen($this->organisationId, 'erika@example.org');
        $this->anschriftSetzen($this->organisationId);

        $this->projektId = $this->projektAnlegen($this->organisationId, 'Website Mustermann');

        $this->alsAdmin($this->adminId);

        $this->rechnungId = $this->rechnungSenden(100000);
        $this->belegId = (string) (new Belege($this->pdo))->zuRechnung($this->rechnungId)[0]['id'];
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_GET = [];
        $_SESSION = [];

        parent::tearDown();
    }

    // ------------------------------------------------------------------ Fall 95

    /**
     * Testfall 95 — der Export enthält jede Rechnung, jede Stornorechnung und jeden
     * Zahlungseingang des Zeitraums **genau einmal**.
     *
     * Der Aufbau ist absichtlich der schwierige: eine bezahlte Rechnung, eine stornierte mit
     * ihrer Stornorechnung, eine Rechnung mit **zwei** Belegen und ein verworfener Entwurf.
     * Wer über `documents` exportiert, zählt die doppelt belegte Rechnung zweimal.
     */
    public function testDerExportEnthaeltJedenVorgangGenauEinmal(): void
    {
        $dienst = $this->dienst();

        // 1. Die Rechnung aus setUp() wird bezahlt.
        $this->assertSame([], $dienst->zahlungEintragen($this->rechnungId, 119000, 'Kontoauszug 08/2026', null));

        // 2. Eine zweite Rechnung, die storniert wird — sie und ihr Storno sind zwei Zeilen.
        $storniert = $this->rechnungSenden(50000);
        $this->assertSame([], $dienst->stornieren($storniert, 'Leistung entfällt.', null));

        // 3. Eine dritte Rechnung mit einem **zweiten** Beleg. §7 lässt das zu.
        $doppelt = $this->rechnungSenden(20000);
        $this->zweitenBelegAnlegen($doppelt);
        $this->assertCount(2, (new Belege($this->pdo))->zuRechnung($doppelt));

        // 4. Ein verworfener Entwurf — nie ausgestellt, gehört nicht in den Export.
        $entwurf = $this->rechnungAnlegen(9900);
        $this->assertSame([], $dienst->stornieren($entwurf, 'Falsch angelegt.', null));

        $export = (new Steuerexport($this->nachweis(), $this->pdo))->erzeugen('2026-01-01', '2099-12-31');

        $this->assertSame([], $export['fehler']);

        $zeilen = $this->zeilen((string) $export['inhalt']);
        $nummern = array_map(static fn (array $z): string => $z[1], $zeilen);

        // Vier ausgestellte Belege: drei Rechnungen und eine Stornorechnung.
        $this->assertCount(4, $nummern);
        $this->assertSame(array_unique($nummern), $nummern, 'Ein Vorgang steht mehr als einmal im Export.');

        // Der verworfene Entwurf fehlt — er ist nie hinausgegangen.
        $verworfen = $this->rechnungen()->finden($entwurf);
        $this->assertIsArray($verworfen);
        $this->assertNotContains((string) $verworfen['number'], $nummern);

        // Die Stornorechnung steht mit negativen Beträgen, die aufgehobene Rechnung
        // unverändert daneben. Aufgehoben wird durch die zweite Zeile, nicht durch Löschen.
        $stornoZeile = $this->zeileMitNummer($zeilen, 'ST-');
        $this->assertNotNull($stornoZeile);
        $this->assertSame('-500,00', $stornoZeile[4]);
        $this->assertSame('-595,00', $stornoZeile[7]);

        // Der Zahlungseingang steht als Spalte an seiner Rechnung — genau einmal, weil er
        // zu genau einer gehört.
        $bezahlte = $this->rechnungen()->finden($this->rechnungId);
        $this->assertIsArray($bezahlte);

        $bezahltZeile = $this->zeileMitNummer($zeilen, (string) $bezahlte['number']);
        $this->assertNotNull($bezahltZeile);
        $this->assertNotSame('', $bezahltZeile[8], 'Der Zahlungseingang fehlt im Export.');

        $mitZahldatum = array_filter($zeilen, static fn (array $z): bool => $z[8] !== '');
        $this->assertCount(1, $mitZahldatum);
    }

    /** Die Kopfzeile trägt die zehn Angaben aus §8, in der dort genannten Reihenfolge. */
    public function testDerExportTraegtDieZehnAngabenAusDerVorgabe(): void
    {
        $export = (new Steuerexport($this->nachweis(), $this->pdo))->erzeugen('2026-01-01', '2099-12-31');

        $kopf = $this->zeilen((string) $export['inhalt'], mitKopf: true)[0];

        $this->assertSame(Steuerexport::SPALTEN, $kopf);
        $this->assertCount(10, $kopf);
    }

    /** Ein Zeitraum ohne Ausstellung liefert keine leere Datei, sondern einen Hinweis. */
    public function testEinLeererZeitraumLiefertKeineDatei(): void
    {
        $antwort = $this->alsAngemeldeterAdmin(static fn (Router $r): \Sartu\Antwort
            => $r->behandeln('POST', '/admin/belege/export'), ['von' => '2020-01-01', 'bis' => '2020-12-31']);

        $this->assertSame(200, $antwort->status);
        $this->assertStringContainsString('text/html', $antwort->kopfzeilen['Content-Type'] ?? '');
        $this->assertStringContainsString('keine Rechnung ausgestellt', $antwort->rumpf);
    }

    /** Ein umgedrehter Zeitraum wird abgewiesen, statt still nichts zu liefern. */
    public function testEinUmgedrehterZeitraumWirdAbgewiesen(): void
    {
        $export = (new Steuerexport($this->nachweis(), $this->pdo))->erzeugen('2026-12-31', '2026-01-01');

        $this->assertNotSame([], $export['fehler']);
        $this->assertNull($export['inhalt']);
    }

    // ------------------------------------------------------------------ Abruf (§9)

    /** Der Kunde bekommt seinen Beleg — über die Route, mit Sitzung und Organisation. */
    public function testDerKundeBekommtSeinenBelegUeberDieRoute(): void
    {
        $benutzerId = $this->benutzerDerOrganisation($this->organisationId);

        $_SESSION = [];
        $this->alsKunde($this->organisationId, $benutzerId);

        $antwort = $this->router()->behandeln('GET', '/portal/belege/' . $this->belegId);

        $this->assertSame(200, $antwort->status);
        $this->assertSame('application/pdf', $antwort->kopfzeilen['Content-Type'] ?? null);
        $this->assertStringStartsWith('%PDF-', $antwort->rumpf);
        $this->assertStringContainsString('.pdf', $antwort->kopfzeilen['Content-Disposition'] ?? '');
    }

    /**
     * §9: „Der Kunde sieht ausschließlich Belege seiner Organisation. Die Prüfung ist
     * doppelt … Sonst **404**, nicht 403."
     */
    public function testEinFremderBelegLiefert404UndNicht403(): void
    {
        $fremde = $this->organisationAnlegen('Fremdbetrieb GmbH', 'fremd@example.org');
        $fremderBenutzer = $this->kundeAnlegen($fremde, 'fremd@example.org');

        $_SESSION = [];
        $this->alsKunde($fremde, $fremderBenutzer);

        $antwort = $this->router()->behandeln('GET', '/portal/belege/' . $this->belegId);

        $this->assertSame(404, $antwort->status);
        $this->assertStringNotContainsString('%PDF', $antwort->rumpf);

        // Und die Abfrage selbst findet ihn nicht — nicht nur die Route. `KundenBereich`
        // entsteht ausschliesslich aus der Sitzung; es gibt keinen Weg, ihn zu setzen (§3
        // Regel 1), und genau das ist hier die Aussage.
        $bereich = KundenBereich::ausSitzung();

        $this->assertSame($fremde, $bereich->organisationId);
        $this->assertNull((new KundenBelege($bereich, $this->pdo))->finden($this->belegId));
    }

    /** Ein Beleg, den es nicht gibt, sieht genauso aus wie ein fremder. */
    public function testEinUnbekannterBelegSiehtAusWieEinFremder(): void
    {
        $benutzerId = $this->benutzerDerOrganisation($this->organisationId);

        $_SESSION = [];
        $this->alsKunde($this->organisationId, $benutzerId);

        $unbekannt = $this->router()->behandeln('GET', '/portal/belege/' . Uuid::v4());

        $_SESSION = [];
        $this->alsKunde(
            $this->organisationAnlegen('Fremdbetrieb GmbH', 'fremd@example.org'),
            $this->kundeAnlegen($this->organisationAnlegen('Dritter', 'dritt@example.org'), 'dritt@example.org'),
        );

        $this->assertSame(404, $unbekannt->status);
    }

    // ------------------------------------------------------------------ Versand (§9)

    /** §9: ein Knopf je Beleg, die Mail trägt den Beleg im Anhang, der Versand wird protokolliert. */
    public function testDerVersandSchicktDenBelegImAnhangUndWirdProtokolliert(): void
    {
        $postfach = new Postfach();

        $fehler = (new Belegversand($this->nachweis(), mail: $postfach, pdo: $this->pdo))
            ->senden($this->belegId, null);

        $this->assertSame([], $fehler);
        $this->assertCount(1, $postfach->mails);

        $mail = $postfach->mails[0];

        $this->assertSame('erika@example.org', $mail['an']);
        $this->assertStringStartsWith('Rechnung RE-', $mail['betreff']);
        $this->assertIsArray($mail['anhang']);
        $this->assertSame('application/pdf', $mail['anhang']['typ']);
        $this->assertStringEndsWith('.pdf', $mail['anhang']['name']);
        $this->assertStringStartsWith('%PDF-', $mail['anhang']['inhalt']);

        $this->assertSame(1, $this->auditZaehlen('beleg_versendet'));
    }

    /** §9: „Ein zweiter Versand ist erlaubt und wird ebenfalls protokolliert." */
    public function testEinZweiterVersandIstErlaubtUndWirdEbenfallsProtokolliert(): void
    {
        $postfach = new Postfach();
        $versand = new Belegversand($this->nachweis(), mail: $postfach, pdo: $this->pdo);

        $this->assertSame([], $versand->senden($this->belegId, null));
        $this->assertSame([], $versand->senden($this->belegId, null));

        $this->assertCount(2, $postfach->mails);
        $this->assertSame(2, $this->auditZaehlen('beleg_versendet'));
    }

    /**
     * Weicht die abgelegte Datei von ihrer Prüfsumme ab, geht sie **nicht** hinaus.
     *
     * §7: eine Abweichung ist ein Fehler und keine Warnung — und eine veränderte Datei zu
     * versenden wäre der Fall, in dem sie folgenlos bliebe.
     */
    public function testEinVeraenderterBelegWirdNichtVersendet(): void
    {
        $beleg = (new Belege($this->pdo))->zuRechnung($this->rechnungId)[0];
        $pfad = \Sartu\Helpers\Speicher::verzeichnis() . '/belege/' . (string) $beleg['path'];

        file_put_contents($pfad, (string) file_get_contents($pfad) . 'veraendert');

        $postfach = new Postfach();

        $fehler = (new Belegversand($this->nachweis(), mail: $postfach, pdo: $this->pdo))
            ->senden($this->belegId, null);

        $this->assertNotSame([], $fehler);
        $this->assertSame([], $postfach->mails);
        $this->assertSame(0, $this->auditZaehlen('beleg_versendet'));
    }

    // ------------------------------------------------------------------ Hilfsmittel

    /**
     * @param array<string,string> $post
     * @param \Closure(Router):\Sartu\Antwort $tun
     */
    private function alsAngemeldeterAdmin(\Closure $tun, array $post = []): \Sartu\Antwort
    {
        $_POST = $post + [\Sartu\Helpers\Csrf::FELD => \Sartu\Helpers\Csrf::token()];

        return $tun($this->router());
    }

    /**
     * Die Datenzeilen des Exports, ohne Kopf.
     *
     * @return list<list<string>>
     */
    private function zeilen(string $inhalt, bool $mitKopf = false): array
    {
        // Der BOM voran gehört zur Datei, nicht zur ersten Spalte.
        $inhalt = str_replace("\u{FEFF}", '', $inhalt);

        $zeilen = [];

        foreach (explode("\r\n", trim($inhalt)) as $zeile) {
            if ($zeile === '') {
                continue;
            }

            $zeilen[] = array_map(
                static fn (string $feld): string => str_replace('""', '"', trim($feld, '"')),
                explode(Steuerexport::TRENNER, $zeile),
            );
        }

        return $mitKopf ? $zeilen : array_slice($zeilen, 1);
    }

    /**
     * @param list<list<string>> $zeilen
     * @return list<string>|null
     */
    private function zeileMitNummer(array $zeilen, string $anfang): ?array
    {
        foreach ($zeilen as $zeile) {
            if (str_starts_with($zeile[1], $anfang)) {
                return $zeile;
            }
        }

        return null;
    }

    private function dienst(): Rechnungsdienst
    {
        return new Rechnungsdienst($this->nachweis(), mail: $this->postfach, pdo: $this->pdo);
    }

    private function rechnungen(): AdminRechnungen
    {
        return new AdminRechnungen($this->nachweis(), $this->pdo);
    }

    private function rechnungAnlegen(int $nettoCent): string
    {
        $angelegt = $this->dienst()->anlegen(
            $this->projektId,
            ['milestone' => 'zwischenrate', 'net_cents' => $nettoCent],
            null,
        );

        $this->assertSame([], $angelegt['fehler']);

        return (string) $angelegt['id'];
    }

    private function rechnungSenden(int $nettoCent): string
    {
        $id = $this->rechnungAnlegen($nettoCent);

        $this->assertSame([], $this->dienst()->senden($id, null));

        return $id;
    }

    /**
     * Ein zweiter Beleg zu derselben Rechnung.
     *
     * Über die Datenschicht und nicht über eine zweite Erzeugung: Geprüft wird hier der
     * Export, nicht die Erzeugung — und der Fall, den er treffen muss, ist „zwei Zeilen in
     * `documents`, eine Rechnung".
     */
    private function zweitenBelegAnlegen(string $rechnungId): void
    {
        $erster = (new Belege($this->pdo))->zuRechnung($rechnungId)[0];

        (new Belege($this->pdo))->anlegen([
            'kind'       => 'rechnung',
            'number'     => (string) $erster['number'],
            'invoice_id' => $rechnungId,
            'offer_id'   => null,
            'path'       => (string) $erster['path'],
            'checksum'   => (string) $erster['checksum'],
            'format'     => 'pdfa3-zugferd',
        ]);
    }

    private function benutzerDerOrganisation(string $organisationId): string
    {
        $anweisung = $this->pdo->prepare('SELECT id FROM users WHERE organization_id = ? LIMIT 1');
        $anweisung->execute([$organisationId]);

        return (string) $anweisung->fetchColumn();
    }

    private function anschriftSetzen(string $organisationId): void
    {
        $anweisung = $this->pdo->prepare(
            'UPDATE organizations SET street = ?, postal_code = ?, city = ? WHERE id = ?'
        );
        $anweisung->execute(['Bahnhofstrasse 7', '48268', 'Greven', $organisationId]);
    }

    private function auditZaehlen(string $aktion): int
    {
        $anweisung = $this->pdo->prepare('SELECT COUNT(*) FROM audit_events WHERE action = ?');
        $anweisung->execute([$aktion]);

        return (int) $anweisung->fetchColumn();
    }

    private function nachweis(): AdminNachweis
    {
        $nachweis = AdminNachweis::ausSitzung();

        if ($nachweis === null) {
            $this->alsAdmin($this->adminId);

            $nachweis = AdminNachweis::ausSitzung();
        }

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

    private function projektAnlegen(string $organisationId, string $titel): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO projects (id, organization_id, title, package, included_feedback_rounds,'
            . ' protection_level, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([$id, $organisationId, $titel, 'wachstum', 2, 'm', Projektstatus::ZAHLUNG_OFFEN]);

        return $id;
    }
}
