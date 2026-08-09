<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\RechtstexteSpeicher;
use Sartu\Helpers\Csrf;
use Sartu\Router;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Startsperre;
use Sartu\Services\Wartungsmodus;
use Sartu\Services\Zahlungsschluessel;

/**
 * Der Menüpunkt „Ersteinrichtung" im Adminbereich.
 *
 * Er entsteht aus `Startsperre::hindernisse()` und verschwindet, sobald `starterlaubt()`
 * wahr ist. Die **Seite** bleibt erreichbar — auf ihr steht das einzige Formular für den
 * Zahlungsschlüssel (`18_BELEGE_UND_ZAHLUNG.md` §6), und der wird gewechselt, wenn die
 * Sperre längst offen ist.
 *
 * **Dies ist nicht der neunte Schritt der Ersteinrichtung.** `/admin/setup` bleibt bei acht
 * und ist danach dauerhaft 404; diese Seite liegt hinter der Adminanmeldung.
 */
final class ErsteinrichtungMenueTest extends Datenbankfall
{
    private string $adminId;

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
        $_POST = [];

        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);

        $this->adminId = $this->adminAnlegen();
        $this->alsAdmin($this->adminId);
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_SESSION = [];

        parent::tearDown();
    }

    /** Solange etwas fehlt, steht der Punkt im Kopfband. */
    public function testDerMenuepunktStehtImKopfbandSolangeDieSperreGreift(): void
    {
        // Ohne Betreiberdaten ist die Sperre auf jeden Fall zu.
        $this->assertFalse((new Startsperre(new BetreiberdatenSpeicher($this->pdo)))->starterlaubt());

        $antwort = $this->router()->behandeln('GET', '/admin');

        $this->assertSame(200, $antwort->status);
        $this->assertStringContainsString('href="/admin/ersteinrichtung"', $antwort->rumpf);
        $this->assertStringContainsString('Ersteinrichtung', $antwort->rumpf);
    }

    /**
     * Sobald der Start frei ist, verschwindet der Punkt **aus dem Kopfband** — aber nicht
     * die Seite.
     */
    public function testDerMenuepunktVerschwindetSobaldDerStartFreiIst(): void
    {
        $this->startFreigeben();

        $this->assertTrue((new Startsperre())->starterlaubt());

        $antwort = $this->router()->behandeln('GET', '/admin/rechnungen');

        $this->assertSame(200, $antwort->status);

        // Im Kopfband steht er nicht mehr. Geprüft wird an der Navigation, nicht am ganzen
        // Rumpf — die Übersicht verlinkt die Seite dauerhaft, und das ist Absicht.
        $kopfband = $this->kopfband($antwort->rumpf);

        $this->assertNotSame('', $kopfband, 'Das Kopfband wurde nicht gefunden.');
        $this->assertStringNotContainsString('/admin/ersteinrichtung', $kopfband);
        $this->assertStringContainsString('/admin/rechnungen', $kopfband);
    }

    /** Die Seite bleibt erreichbar, auch wenn der Start frei ist. */
    public function testDieSeiteBleibtErreichbarWennDerStartFreiIst(): void
    {
        $this->startFreigeben();

        $antwort = $this->router()->behandeln('GET', '/admin/ersteinrichtung');

        $this->assertSame(200, $antwort->status);
        $this->assertStringContainsString('Zahlungsschlüssel', $antwort->rumpf);
    }

    /** Die Übersicht verlinkt sie dauerhaft — sonst wäre sie nach dem Onlinegang verloren. */
    public function testDieUebersichtVerlinktDieSeiteDauerhaft(): void
    {
        $this->startFreigeben();

        $antwort = $this->router()->behandeln('GET', '/admin');

        $this->assertStringContainsString('href="/admin/ersteinrichtung"', $antwort->rumpf);
        $this->assertStringNotContainsString('/admin/ersteinrichtung', $this->kopfband($antwort->rumpf));
    }

    /** Die Seite nennt jedes offene Hindernis im Wortlaut der Startsperre. */
    public function testDieSeiteNenntJedesOffeneHindernis(): void
    {
        $hindernisse = (new Startsperre())->hindernisse();

        $this->assertNotSame([], $hindernisse);

        $antwort = $this->router()->behandeln('GET', '/admin/ersteinrichtung');

        foreach ($hindernisse as $hindernis) {
            $this->assertStringContainsString(
                htmlspecialchars($hindernis, ENT_QUOTES, 'UTF-8'),
                $antwort->rumpf,
            );
        }
    }

    /**
     * Der fehlende Zahlungsschlüssel steht unter „Weitere Punkte" und **nicht** unter den
     * Hindernissen.
     *
     * §1.4a zählt abschließend auf, was die Veröffentlichung anhält. Ein Betrieb, der seine
     * Rechnungen per Überweisung stellt, braucht keinen Zahlungsdienst und darf trotzdem
     * online gehen.
     */
    public function testDerFehlendeZahlungsschluesselHaeltDieVeroeffentlichungNichtAn(): void
    {
        $this->startFreigeben();

        $sperre = new Startsperre();

        $this->assertSame([], $sperre->hindernisse());
        $this->assertTrue($sperre->starterlaubt());

        $punkte = $sperre->weiterePunkte();

        $this->assertNotSame([], $punkte);
        $this->assertStringContainsString('Zahlungsschlüssel', implode(' ', $punkte));
    }

    /** Ist der Schlüssel hinterlegt, verschwindet der Punkt aus der zweiten Liste. */
    public function testEinHinterlegterSchluesselVerschwindetAusDerListe(): void
    {
        $this->startFreigeben();

        $vorher = (new Startsperre())->weiterePunkte();

        $this->assertStringContainsString('Zahlungsschlüssel', implode(' ', $vorher));

        $this->schluesselHinterlegen('test_einhinreichendlangertestschluessel');

        $nachher = (new Startsperre())->weiterePunkte();

        $this->assertStringNotContainsString('Zahlungsschlüssel', implode(' ', $nachher));
    }

    /** Der Schlüssel wird über das Formular hinterlegt — und erscheint danach nirgends. */
    public function testDerSchluesselWirdUeberDasFormularHinterlegtUndNieAngezeigt(): void
    {
        $schluessel = 'test_einhinreichendlangertestschluessel';

        $this->startFreigeben();

        $antwort = $this->absenden('/admin/ersteinrichtung/zahlungsschluessel', [
            'feld'       => Zahlungsschluessel::FELD_TEST,
            'schluessel' => $schluessel,
        ]);

        $this->assertSame(200, $antwort->status);
        $this->assertStringContainsString('hinterlegt', $antwort->rumpf);

        // Fall 93: nicht in der Ansicht. Höchstens die letzten vier Zeichen.
        $this->assertStringNotContainsString($schluessel, $antwort->rumpf);
        $this->assertStringNotContainsString('test_einhin', $antwort->rumpf);
        $this->assertStringContainsString('…ssel', $antwort->rumpf);

        // Und nicht im Protokoll.
        $zeilen = $this->pdo->query('SELECT * FROM audit_events')->fetchAll();

        $this->assertNotSame([], $zeilen);

        foreach ($zeilen as $zeile) {
            $this->assertStringNotContainsString($schluessel, json_encode($zeile) ?: '');
        }

        $this->assertSame(1, $this->auditZaehlen('zahlungsschluessel_hinterlegt'));
    }

    /** Ein zu kurzer Schlüssel wird abgewiesen, ohne in der Meldung zu erscheinen. */
    public function testEinZuKurzerSchluesselErscheintNichtInDerMeldung(): void
    {
        $this->startFreigeben();

        $antwort = $this->absenden('/admin/ersteinrichtung/zahlungsschluessel', [
            'feld'       => Zahlungsschluessel::FELD_TEST,
            'schluessel' => 'test_kurz',
        ]);

        $this->assertSame(200, $antwort->status);
        $this->assertStringNotContainsString('test_kurz', $antwort->rumpf);
        $this->assertFalse((new Zahlungsschluessel())->hinterlegt(Zahlungsschluessel::FELD_TEST));
    }

    /** Entfernen ist eine eigene, protokollierte Handlung. */
    public function testDasEntfernenIstEineEigeneProtokollierteHandlung(): void
    {
        $this->startFreigeben();
        $this->schluesselHinterlegen('test_einhinreichendlangertestschluessel');

        $this->assertTrue((new Zahlungsschluessel())->hinterlegt(Zahlungsschluessel::FELD_TEST));

        $this->absenden('/admin/ersteinrichtung/zahlungsschluessel-entfernen', [
            'feld' => Zahlungsschluessel::FELD_TEST,
        ]);

        $this->assertFalse((new Zahlungsschluessel())->hinterlegt(Zahlungsschluessel::FELD_TEST));
        $this->assertSame(1, $this->auditZaehlen('zahlungsschluessel_entfernt'));
    }

    /**
     * `/admin/setup` bleibt bei **acht** Schritten — diese Seite ist kein neunter.
     *
     * Gezählt wird an `Ersteinrichtung::SCHRITTE` und nicht an den Routen: Die Strecke hat
     * mehr POST-Wege als Schritte — `mail-bestaetigen` gehört zum fünften — und eine Zählung
     * über Routen würde bei jeder Zwischenbestätigung falsch anschlagen.
     *
     * Dazu die zweite Hälfte: `/admin/setup` ist bei gesetzter Sperre 404, während
     * `/admin/ersteinrichtung` antwortet. Die eine Strecke ist abgeschlossen, die andere
     * nicht — sie sind nicht dieselbe.
     */
    public function testDieErsteinrichtungIstKeinNeunterSetupSchritt(): void
    {
        $this->assertCount(8, \Sartu\Services\Ersteinrichtung::SCHRITTE);
        $this->assertSame('Abschluss', \Sartu\Services\Ersteinrichtung::SCHRITTE[8]);

        $this->assertSame(404, $this->router()->behandeln('GET', '/admin/setup')->status);
        $this->assertSame(200, $this->router()->behandeln('GET', '/admin/ersteinrichtung')->status);
    }

    // ------------------------------------------------------------------ Hilfsmittel

    /** Betreiberdaten vollständig, Rechtstexte freigegeben — der Start ist frei. */
    private function startFreigeben(): void
    {
        (new BetreiberdatenSpeicher($this->pdo))->anlegen([
            'firmenname'                => 'SARTU',
            'strasse'                   => 'Strasse 1',
            'plz'                       => '01067',
            'ort'                       => 'Dresden',
            'land'                      => 'DE',
            'email'                     => 'betreiber@example.org',
            'inhaltlich_verantwortlich' => 'Verantwortlich',
            'steuernummer'              => '337/5804/1234',
        ]);

        $speicher = new RechtstexteSpeicher($this->pdo);

        foreach ($speicher->nichtFreigegebene() as $slug) {
            $speicher->anlegen($slug, "Platzhalter für den Test.\n\nZweiter Absatz.", 'oeffentlich');
            $speicher->zustandSetzen($slug, 'freigegeben', 'Testkanzlei');
        }
    }

    private function schluesselHinterlegen(string $klartext): void
    {
        $this->assertSame(
            [],
            (new Zahlungsschluessel())->hinterlegen(Zahlungsschluessel::FELD_TEST, $klartext),
        );
    }

    /** @param array<string,string> $werte */
    private function absenden(string $pfad, array $werte): \Sartu\Antwort
    {
        $_POST = $werte + [Csrf::FELD => Csrf::token()];

        $antwort = $this->router()->behandeln('POST', $pfad);

        $_POST = [];

        return $antwort;
    }

    /** Nur der `<nav>`-Block des Kopfbands. */
    private function kopfband(string $rumpf): string
    {
        if (preg_match('#<nav aria-label="Interner Bereich">(.*?)</nav>#s', $rumpf, $treffer) !== 1) {
            return '';
        }

        return $treffer[1];
    }

    private function auditZaehlen(string $aktion): int
    {
        $anweisung = $this->pdo->prepare('SELECT COUNT(*) FROM audit_events WHERE action = ?');
        $anweisung->execute([$aktion]);

        return (int) $anweisung->fetchColumn();
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
