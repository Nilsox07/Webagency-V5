<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\Admin\AdminBenutzer;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\AnmeldeKonten;
use Sartu\Data\SitzungsSpeicher;
use Sartu\Route;
use Sartu\Router;
use Sartu\Services\AnmeldeDienst;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Ratenbegrenzung;
use Sartu\Services\VerbrauchteCodes;
use Sartu\Services\Verschluesselung;
use Sartu\Services\Wartungsmodus;
use Sartu\Services\Zweifaktor;
use Sartu\Sitzung;

/**
 * Adminanmeldung mit Passwort und TOTP — Portal-Lastenheft §2, §3 Regeln 4, 6, 9 und 10.
 *
 * Diese Datei ist nach der Sicherheitsprüfung vom 02.08.2026 entstanden. Sie hält drei
 * Befunde fest, die alle dieselbe Form hatten: Eine Zusage stand im Lastenheft, der Code
 * hielt sie **fast**, und kein Test hat den Unterschied bemerkt.
 */
final class AnmeldungTest extends Datenbankfall
{
    private const PASSWORT = 'einlangespasswort';

    private string $arbeitsverzeichnis;

    private string $geheimnis;

    private string $adminId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->arbeitsverzeichnis = sys_get_temp_dir() . '/sartu-anmeldung-' . bin2hex(random_bytes(4));
        mkdir($this->arbeitsverzeichnis, 0770, true);

        $this->geheimnis = Zweifaktor::geheimnisErzeugen();

        $this->adminId = (new AdminBenutzer(AdminNachweis::fuerErsteinrichtung($this->offeneSperre())))
            ->adminAnlegen(
                'admin@example.org',
                'Test',
                'Betreiber',
                password_hash(self::PASSWORT, PASSWORD_ARGON2ID),
                (new Verschluesselung($this->schluessel()))->verschluesseln($this->geheimnis),
            );

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
    }

    protected function tearDown(): void
    {
        $this->verzeichnisLoeschen($this->arbeitsverzeichnis);

        parent::tearDown();
    }

    // ------------------------------------------------------------ Der gute Weg

    public function testPasswortUndCodeFuehrenZurAnmeldung(): void
    {
        $dienst = $this->dienst();

        $this->assertTrue($dienst->passwortPruefen('admin@example.org', self::PASSWORT, '127.0.0.1'));
        $this->assertFalse(Sitzung::istAngemeldeterAdmin(), 'Nach dem Passwort darf noch niemand angemeldet sein.');
        $this->assertNull(AdminNachweis::ausSitzung());

        $token = $dienst->codePruefen($this->code(), '127.0.0.1', 'Testlauf');

        $this->assertIsString($token);
        $this->assertTrue(Sitzung::istAngemeldeterAdmin());
        $this->assertNotNull(AdminNachweis::ausSitzung());
        $this->assertTrue($dienst->sitzungGueltig());
    }

    public function testFalschesPasswortWirdProtokolliert(): void
    {
        $this->assertFalse($this->dienst()->passwortPruefen('admin@example.org', 'falsch-aber-lang', '127.0.0.1'));

        $anweisung = $this->pdo->prepare('SELECT COUNT(*) FROM audit_events WHERE action = ?');
        $anweisung->execute(['anmeldung_fehlgeschlagen']);

        $this->assertSame(1, (int) $anweisung->fetchColumn());
    }

    // ---------------------------------------- Befund 1: der zweite Faktor war ungezaehlt

    /**
     * Der Code lässt sich nicht durchprobieren.
     *
     * Vorher zählte nur Schritt 1. Wer das Passwort hatte — aus einem Datenleck, aus
     * Wiederverwendung —, brauchte einen Versuch dafür und konnte danach beliebig oft
     * sechsstellige Codes senden. Damit war der zweite Faktor keiner.
     */
    public function testZweiterFaktorHatEinenEigenenZaehler(): void
    {
        $dienst = $this->dienst();
        $dienst->passwortPruefen('admin@example.org', self::PASSWORT, '127.0.0.1');

        for ($versuch = 0; $versuch < 5; ++$versuch) {
            $this->assertNull($dienst->codePruefen('000000', '127.0.0.1', 'Testlauf'));
        }

        // Ab hier ist Schluss — auch mit dem RICHTIGEN Code.
        $this->assertNull(
            $dienst->codePruefen($this->code(), '127.0.0.1', 'Testlauf'),
            'Nach fünf Fehlversuchen ging der zweite Faktor noch durch.'
        );
        $this->assertFalse(Sitzung::istAngemeldeterAdmin());
    }

    /** Der Vormerk zwischen Passwort und Code läuft ab. */
    public function testVormerkVerfaellt(): void
    {
        $dienst = $this->dienst();
        $dienst->passwortPruefen('admin@example.org', self::PASSWORT, '127.0.0.1');

        $this->assertSame($this->adminId, $dienst->vorgemerkterBenutzer());

        // Fünf Minuten und eine Sekunde zurückdatieren.
        $_SESSION['_anmeldung_seit'] = time() - 301;

        $this->assertNull($dienst->vorgemerkterBenutzer(), 'Der Vormerk gilt unbegrenzt.');
        $this->assertNull($dienst->codePruefen($this->code(), '127.0.0.1', 'Testlauf'));
    }

    /** Ohne Passwortschritt geht der Code gar nicht erst los. */
    public function testCodeOhneVormerkFuehrtZuNichts(): void
    {
        $this->assertNull($this->dienst()->codePruefen($this->code(), '127.0.0.1', 'Testlauf'));
    }

    // ---------------------------------------- Befund 2: ein Code galt zweimal

    /**
     * Ein angenommener Code gilt kein zweites Mal — RFC 6238 §5.2.
     *
     * Ohne das lässt sich ein mitgelesener Code innerhalb seiner dreißig Sekunden erneut
     * einlösen: aus einem geteilten Bildschirm, einer Zwischenstelle, einer zweiten App.
     */
    public function testEinCodeLaesstSichNichtZweimalEinloesen(): void
    {
        $code = $this->code();

        $ersteSitzung = $this->dienst();
        $ersteSitzung->passwortPruefen('admin@example.org', self::PASSWORT, '127.0.0.1');
        $this->assertIsString($ersteSitzung->codePruefen($code, '127.0.0.1', 'Testlauf'));

        // Zweiter Anlauf, andere Sitzung, derselbe Code im selben Zeitfenster.
        $_SESSION = [];
        $zweiteSitzung = $this->dienst();
        $zweiteSitzung->passwortPruefen('admin@example.org', self::PASSWORT, '127.0.0.1');

        $this->assertNull(
            $zweiteSitzung->codePruefen($code, '127.0.0.1', 'Testlauf'),
            'Derselbe Code ging ein zweites Mal durch.'
        );
        $this->assertFalse(Sitzung::istAngemeldeterAdmin());
    }

    /**
     * Ein Code aus dem vorigen Zeitschritt gilt — und wird unter SEINEM Schritt entwertet.
     *
     * Das ist der Feinschliff, den die erste Fassung der Wiederholungssperre nicht hatte:
     * Sie hakte den gerade laufenden Schritt ab. Ein Code aus dem Schritt davor wäre damit
     * unter der falschen Nummer vermerkt gewesen und im nächsten Schritt erneut gültig.
     */
    public function testCodeAusDemVorigenSchrittWirdUnterSeinemEigenenSchrittEntwertet(): void
    {
        $jetzt = time();
        $vorher = $jetzt - 30;

        $codeVorher = \OTPHP\TOTP::createFromSecret($this->geheimnis)->at($vorher);

        $this->assertSame(
            Zweifaktor::zeitschritt($vorher),
            Zweifaktor::zeitschrittZumCode($this->geheimnis, $codeVorher, $jetzt),
            'Der Code wurde dem falschen Zeitschritt zugeordnet.'
        );
    }

    /** Zwei Schritte zurück gilt nicht mehr. */
    public function testCodeAusZweiSchrittenZurueckGiltNicht(): void
    {
        $jetzt = time();
        $zuAlt = \OTPHP\TOTP::createFromSecret($this->geheimnis)->at($jetzt - 61);

        $this->assertNull(Zweifaktor::zeitschrittZumCode($this->geheimnis, $zuAlt, $jetzt));
    }

    /** Ein Code, der nicht aus sechs Ziffern besteht, wird gar nicht erst gerechnet. */
    public function testUnsinnigerCodeWirdAbgewiesen(): void
    {
        foreach (['', '12345', '1234567', 'abcdef', '12 34 56 78'] as $unsinn) {
            $this->assertNull(Zweifaktor::zeitschrittZumCode($this->geheimnis, $unsinn));
        }
    }

    /** Der Zeitschritt ist dreißig Sekunden lang — die Rechnung dahinter. */
    public function testZeitschrittRechnetInDreissigSekunden(): void
    {
        $this->assertSame(100, Zweifaktor::zeitschritt(3000));
        $this->assertSame(100, Zweifaktor::zeitschritt(3029));
        $this->assertSame(101, Zweifaktor::zeitschritt(3030));
    }

    // ---------------------------------------- Befund 3: Sitzungen waren nicht widerrufbar

    /**
     * Eine Abmeldung wirkt serverseitig — §3 Regel 6.
     *
     * Vorher wurde die Zeile in `sessions` zwar geschrieben und beim Abmelden gelöscht, aber
     * nie gelesen. Damit war eine Anmeldung nicht zurückziehbar: Der Zustand hing allein am
     * PHP-Cookie.
     */
    public function testGeloeschteSitzungMachtDenAdminbereichUnerreichbar(): void
    {
        $dienst = $this->dienst();
        $dienst->passwortPruefen('admin@example.org', self::PASSWORT, '127.0.0.1');
        $token = $dienst->codePruefen($this->code(), '127.0.0.1', 'Testlauf');

        $this->assertIsString($token);
        $this->assertSame(200, $this->router()->behandeln('GET', '/admin')->status);

        // Jemand entzieht die Anmeldung serverseitig — der Sitzungszustand bleibt unberührt.
        (new SitzungsSpeicher($this->pdo))->loeschen($token);

        $this->assertTrue(Sitzung::istAngemeldeterAdmin(), 'Der Sitzungszustand soll unverändert bleiben.');
        $this->assertNotNull(AdminNachweis::ausSitzung());

        $antwort = $this->router()->behandeln('GET', '/admin');

        $this->assertSame(302, $antwort->status, 'Die entzogene Anmeldung galt weiter.');
        $this->assertSame('/admin/anmelden', $antwort->kopfzeilen['Location'] ?? null);
    }

    /** Eine abgelaufene Sitzung gilt ebenso wenig. */
    public function testAbgelaufeneSitzungGiltNicht(): void
    {
        $dienst = $this->dienst();
        $dienst->passwortPruefen('admin@example.org', self::PASSWORT, '127.0.0.1');
        $dienst->codePruefen($this->code(), '127.0.0.1', 'Testlauf');

        $this->pdo->exec("UPDATE sessions SET expires_at = '2020-01-01 00:00:00'");

        $this->assertFalse($dienst->sitzungGueltig());
        $this->assertSame(302, $this->router()->behandeln('GET', '/admin')->status);
    }

    /** Abmelden löscht die Zeile und protokolliert. */
    public function testAbmeldenLoeschtDieSitzungServerseitig(): void
    {
        $dienst = $this->dienst();
        $dienst->passwortPruefen('admin@example.org', self::PASSWORT, '127.0.0.1');
        $dienst->codePruefen($this->code(), '127.0.0.1', 'Testlauf');

        $this->assertSame(1, (int) $this->pdo->query('SELECT COUNT(*) FROM sessions')->fetchColumn());

        $dienst->abmelden('127.0.0.1');

        $this->assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM sessions')->fetchColumn());

        $anweisung = $this->pdo->prepare('SELECT COUNT(*) FROM audit_events WHERE action = ?');
        $anweisung->execute(['abmeldung']);
        $this->assertSame(1, (int) $anweisung->fetchColumn());
    }

    // ------------------------------------------------------------ Hilfsmittel

    private function code(): string
    {
        return \OTPHP\TOTP::createFromSecret($this->geheimnis)->now();
    }

    private function dienst(): AnmeldeDienst
    {
        return new AnmeldeDienst(
            new AnmeldeKonten($this->pdo),
            new \Sartu\Data\AuditProtokoll($this->pdo),
            new SitzungsSpeicher($this->pdo),
            new Ratenbegrenzung($this->arbeitsverzeichnis),
            new Verschluesselung($this->schluessel()),
            new VerbrauchteCodes($this->arbeitsverzeichnis),
        );
    }

    private function router(): Router
    {
        return new Router(
            require SARTU_WURZEL . '/app/routes.php',
            $this->gesperrteSperre(),
            new Wartungsmodus($this->arbeitsverzeichnis . '/ohne-wartung'),
            $this->dienst(),
        );
    }

    private function offeneSperre(): InstallationsSperre
    {
        return new InstallationsSperre(
            new \Sartu\Data\BetreiberdatenSpeicher($this->pdo),
            $this->arbeitsverzeichnis . '/offen',
        );
    }

    private function gesperrteSperre(): InstallationsSperre
    {
        $verzeichnis = $this->arbeitsverzeichnis . '/zu';

        if (!is_dir($verzeichnis)) {
            mkdir($verzeichnis, 0770, true);
        }

        touch($verzeichnis . '/' . InstallationsSperre::DATEINAME);

        return new InstallationsSperre(new \Sartu\Data\BetreiberdatenSpeicher($this->pdo), $verzeichnis);
    }

    private function schluessel(): string
    {
        static $schluessel = null;

        return $schluessel ??= Verschluesselung::schluesselErzeugen();
    }

    private function verzeichnisLoeschen(string $pfad): void
    {
        if (!is_dir($pfad)) {
            return;
        }

        $lauf = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($pfad, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        foreach ($lauf as $eintrag) {
            $eintrag->isDir() ? rmdir($eintrag->getPathname()) : unlink($eintrag->getPathname());
        }

        rmdir($pfad);
    }
}
