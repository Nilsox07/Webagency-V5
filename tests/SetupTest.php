<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\MigrationFehler;
use Sartu\Data\Migrator;
use Sartu\Data\Uuid;
use Sartu\Route;
use Sartu\Router;
use Sartu\Services\Ersteinrichtung;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Wartungsmodus;

/**
 * Ersteinrichtung — Portal-Lastenheft §1.5.
 *
 * Testfälle: 67 · 68 · 69 · 70 · 71 · 72 · 73
 */
final class SetupTest extends Datenbankfall
{
    private string $arbeitsverzeichnis;

    private ?string $appEnvVorher = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->appEnvVorher = getenv('APP_ENV') === false ? null : (string) getenv('APP_ENV');

        $this->arbeitsverzeichnis = sys_get_temp_dir() . '/sartu-setup-' . bin2hex(random_bytes(4));
        mkdir($this->arbeitsverzeichnis . '/migrations', 0770, true);
        mkdir($this->arbeitsverzeichnis . '/storage', 0770, true);

        $_SERVER = [];
    }

    protected function tearDown(): void
    {
        if ($this->appEnvVorher === null) {
            putenv('APP_ENV');
        } else {
            putenv('APP_ENV=' . $this->appEnvVorher);
        }

        $this->verzeichnisLoeschen($this->arbeitsverzeichnis);

        parent::tearDown();
    }

    // ------------------------------------------------------------ Fall 67

    /**
     * Fall 67 — die Einrichtung gegen eine NICHT leere Datenbank bricht vor der ersten
     * Migration ab. Sie migriert nicht in fremden Bestand hinein.
     */
    public function testEinrichtungGegenNichtLeereDatenbankBrichtAb(): void
    {
        // Das Schema aus setUp() steht bereits — die Datenbank ist also nicht leer.
        $migrator = new Migrator($this->pdo, SARTU_WURZEL . '/migrations');

        $fehler = $migrator->vorpruefung(leereDatenbankVerlangt: true);

        $this->assertNotSame([], $fehler);
        $this->assertStringContainsString('nicht leer', $fehler[0]);
        $this->assertStringContainsString('organizations', $fehler[0], 'Die Meldung nennt die vorhandenen Tabellen nicht.');
    }

    public function testVorpruefungGegenLeereDatenbankGehtDurch(): void
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($this->tabellen() as $tabelle) {
            $this->pdo->exec('DROP TABLE IF EXISTS `' . $tabelle . '`');
        }
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        $migrator = new Migrator($this->pdo, SARTU_WURZEL . '/migrations');

        $this->assertSame([], $migrator->vorpruefung(leereDatenbankVerlangt: true));
    }

    // ------------------------------------------------------------ Fall 68

    /**
     * Fall 68 — eine nachtraeglich geaenderte Migrationsdatei loest einen
     * Pruefsummenabbruch aus, mit Nennung der Datei.
     */
    public function testGeaenderteMigrationsdateiBrichtMitNennungDerDateiAb(): void
    {
        $verzeichnis = $this->eigenesMigrationsverzeichnis();
        file_put_contents($verzeichnis . '/001_probe.sql', 'CREATE TABLE probe (id INT PRIMARY KEY);');

        $migrator = new Migrator($this->pdo, $verzeichnis);
        $migrator->protokolltabelleAnlegen();
        $migrator->offeneEinspielen();

        $this->assertArrayHasKey('001_probe', $migrator->eingetragene());

        // Jemand fasst die ausgelieferte Datei nachtraeglich an.
        file_put_contents($verzeichnis . '/001_probe.sql', 'CREATE TABLE probe (id INT PRIMARY KEY, extra INT);');

        try {
            $migrator->pruefsummenPruefen();
            $this->fail('Der Pruefsummenabgleich hat nicht angeschlagen.');
        } catch (MigrationFehler $fehler) {
            $this->assertStringContainsString('001_probe.sql', $fehler->getMessage());
            $this->assertStringContainsString('geaendert', $fehler->getMessage());
        }
    }

    public function testFehlendeMigrationsdateiBrichtEbenfallsAb(): void
    {
        $verzeichnis = $this->eigenesMigrationsverzeichnis();
        file_put_contents($verzeichnis . '/001_probe.sql', 'CREATE TABLE probe (id INT PRIMARY KEY);');

        $migrator = new Migrator($this->pdo, $verzeichnis);
        $migrator->protokolltabelleAnlegen();
        $migrator->offeneEinspielen();

        unlink($verzeichnis . '/001_probe.sql');

        $this->expectException(MigrationFehler::class);
        $migrator->pruefsummenPruefen();
    }

    // ------------------------------------------------------------ Fall 69

    /**
     * Fall 69 — nach einem Abbruch mitten in den Migrationen setzt der erneute Aufruf bei
     * der ERSTEN nicht eingetragenen Migration fort und wiederholt keine bereits
     * eingetragene.
     *
     * Der Abbruch wird echt erzeugt: Die zweite Datei enthaelt fehlerhaftes SQL.
     */
    public function testWiederanlaufSetztBeiDerErstenNichtEingetragenenFort(): void
    {
        $verzeichnis = $this->eigenesMigrationsverzeichnis();
        file_put_contents($verzeichnis . '/001_eins.sql', 'CREATE TABLE eins (id INT PRIMARY KEY);');
        file_put_contents($verzeichnis . '/002_zwei.sql', 'CREATE TABL zwei (id INT PRIMARY KEY);');
        file_put_contents($verzeichnis . '/003_drei.sql', 'CREATE TABLE drei (id INT PRIMARY KEY);');

        $migrator = new Migrator($this->pdo, $verzeichnis);
        $migrator->protokolltabelleAnlegen();

        try {
            $migrator->offeneEinspielen();
            $this->fail('Die fehlerhafte Migration ist nicht aufgefallen.');
        } catch (MigrationFehler $fehler) {
            $this->assertSame('002_zwei', $fehler->version);
            $this->assertStringContainsString('002_zwei.sql', $fehler->getMessage());
        }

        // Genau eine Migration steht im Protokoll — nicht drei, nicht null.
        $this->assertSame(['001_eins'], array_keys($migrator->eingetragene()));
        $this->assertSame(['002_zwei', '003_drei'], array_column($migrator->offene(), 'version'));

        // Reparieren und erneut aufrufen.
        file_put_contents($verzeichnis . '/002_zwei.sql', 'CREATE TABLE zwei (id INT PRIMARY KEY);');

        $eingespielt = $migrator->offeneEinspielen();

        $this->assertSame(['002_zwei', '003_drei'], $eingespielt, 'Der Wiederanlauf hat nicht dort fortgesetzt.');
        $this->assertSame(1, $this->anzahlImProtokoll('001_eins'), '001 wurde ein zweites Mal eingetragen.');
    }

    // ------------------------------------------------------ Fälle 70, 71, 72

    /** Fall 70 — HTTP mit APP_ENV=production von 127.0.0.1 bricht ab. */
    public function testHttpMitProduktivUmgebungBrichtAuchVonLoopbackAb(): void
    {
        putenv('APP_ENV=production');
        $this->anfrage(https: false, gegenstelle: '127.0.0.1', host: 'localhost');

        $this->assertFalse(Ersteinrichtung::zugangErlaubt());
    }

    /** Fall 71 — HTTP mit APP_ENV=local von einer NICHT loopback-Adresse bricht ab. */
    public function testHttpAusLocalVonFremderAdresseBrichtAb(): void
    {
        putenv('APP_ENV=local');
        $this->anfrage(https: false, gegenstelle: '203.0.113.7', host: 'localhost');

        $this->assertFalse(Ersteinrichtung::zugangErlaubt());
    }

    /** Die Ausnahme greift nur bei allen drei Bedingungen zugleich. */
    public function testHttpAusLocalVonLoopbackIstErlaubt(): void
    {
        putenv('APP_ENV=local');
        $this->anfrage(https: false, gegenstelle: '127.0.0.1', host: 'localhost:8080');

        $this->assertTrue(Ersteinrichtung::zugangErlaubt());
    }

    /** Ein Hostname, der `localhost` nur enthaelt, ist nicht `localhost`. */
    public function testAehnlicherHostnameGenuegtNicht(): void
    {
        putenv('APP_ENV=local');
        $this->anfrage(https: false, gegenstelle: '127.0.0.1', host: 'localhost.angreifer.example');

        $this->assertFalse(Ersteinrichtung::zugangErlaubt());
    }

    /**
     * Fall 72 — X-Forwarded-Proto: https bei tatsaechlichem HTTP wird ignoriert, solange
     * keine vertrauenswuerdige Zwischenstelle konfiguriert ist. Die gibt es in Stufe 0
     * nicht.
     */
    public function testWeiterleitungsKopfzeileWirdIgnoriert(): void
    {
        putenv('APP_ENV=production');
        $this->anfrage(https: false, gegenstelle: '127.0.0.1', host: 'sartu.example');
        $_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.1';

        $this->assertFalse(Ersteinrichtung::zugangErlaubt(), 'X-Forwarded-Proto wurde als Nachweis gewertet.');
    }

    /** Fehlt APP_ENV in der Serverumgebung, gilt produktiv — nicht lokal. */
    public function testFehlendesAppEnvGiltAlsProduktiv(): void
    {
        putenv('APP_ENV');
        $this->anfrage(https: false, gegenstelle: '127.0.0.1', host: 'localhost');

        $this->assertFalse(Ersteinrichtung::zugangErlaubt());
    }

    // ------------------------------------------------------------ Fall 73

    /**
     * Fall 73 — nach Abschluss liefert /admin/setup 404, auch nach Loeschen EINER der
     * beiden Sperren.
     *
     * Das ist der Kern des Befunds vom 01.08.2026: „Einer genuegt, sonst hebt ein
     * geloeschtes Lockfile die Sperre auf."
     */
    public function testNurDateisperreGenuegtFuer404(): void
    {
        $speicher = $this->arbeitsverzeichnis . '/storage';
        touch($speicher . '/' . InstallationsSperre::DATEINAME);

        // In der Datenbank steht nichts: setup_completed_at ist NULL.
        $sperre = new InstallationsSperre(new BetreiberdatenSpeicher($this->pdo), $speicher);

        $this->assertTrue($sperre->dateiGesetzt());
        $this->assertFalse($sperre->datenbankGesetzt());
        $this->assertTrue($sperre->gesperrt());
        $this->assertSame(404, $this->setupStatus($sperre));
    }

    public function testNurDatenbanksperreGenuegtFuer404(): void
    {
        $this->betreiberzeileAnlegen();
        (new BetreiberdatenSpeicher($this->pdo))->einrichtungAbschliessen();

        // Die Sperrdatei fehlt bewusst.
        $sperre = new InstallationsSperre(new BetreiberdatenSpeicher($this->pdo), $this->arbeitsverzeichnis . '/storage');

        $this->assertFalse($sperre->dateiGesetzt());
        $this->assertTrue($sperre->datenbankGesetzt());
        $this->assertTrue($sperre->gesperrt());
        $this->assertSame(404, $this->setupStatus($sperre));
    }

    public function testOhneBeideSperrenIstDieEinrichtungOffen(): void
    {
        $sperre = new InstallationsSperre(new BetreiberdatenSpeicher($this->pdo), $this->arbeitsverzeichnis . '/storage');

        $this->assertFalse($sperre->gesperrt());
        $this->assertSame(200, $this->setupStatus($sperre));
    }

    /** Jeder Aufruf ausser der Einrichtung leitet auf /admin/setup, solange sie offen ist. */
    public function testSolangeOffenLeitetJederAufrufAufDieEinrichtung(): void
    {
        $sperre = new InstallationsSperre(new BetreiberdatenSpeicher($this->pdo), $this->arbeitsverzeichnis . '/storage');
        $this->anfrage(https: false, gegenstelle: '127.0.0.1', host: 'localhost');
        putenv('APP_ENV=local');

        $antwort = $this->router($sperre)->behandeln('GET', '/impressum');

        $this->assertSame(302, $antwort->status);
        $this->assertSame('/admin/setup', $antwort->kopfzeilen['Location'] ?? null);
    }

    // ------------------------------------------------------------ Hilfsmittel

    /**
     * Ein leeres Migrationsverzeichnis MIT leerem Protokoll.
     *
     * Ohne das Leeren stuenden die acht echten Migrationen im Protokoll, ihre Dateien
     * lägen aber nicht in diesem Verzeichnis — der Pruefsummenabgleich schluege dann aus
     * dem falschen Grund an.
     */
    private function eigenesMigrationsverzeichnis(): string
    {
        $this->pdo->exec('DROP TABLE IF EXISTS schema_migrations');

        return $this->arbeitsverzeichnis . '/migrations';
    }

    private function setupStatus(InstallationsSperre $sperre): int
    {
        putenv('APP_ENV=local');
        $this->anfrage(https: false, gegenstelle: '127.0.0.1', host: 'localhost');

        return $this->router($sperre)->behandeln('GET', '/admin/setup')->status;
    }

    private function router(InstallationsSperre $sperre): Router
    {
        return new Router(
            require SARTU_WURZEL . '/app/routes.php',
            $sperre,
            new Wartungsmodus($this->arbeitsverzeichnis . '/ohne-wartung'),
        );
    }

    private function anfrage(bool $https, string $gegenstelle, string $host): void
    {
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REMOTE_ADDR'    => $gegenstelle,
            'HTTP_HOST'      => $host,
        ];

        if ($https) {
            $_SERVER['HTTPS'] = 'on';
        }
    }

    private function betreiberzeileAnlegen(): void
    {
        (new BetreiberdatenSpeicher($this->pdo))->anlegen([
            'firmenname'                => 'Vorläufig',
            'strasse'                   => 'Vorläufig 1',
            'plz'                       => '01067',
            'ort'                       => 'Dresden',
            'land'                      => 'DE',
            'email'                     => 'betreiber@example.org',
            'inhaltlich_verantwortlich' => 'Vorläufig',
            'steuernummer'              => '000/000/00000',
        ]);
    }

    private function anzahlImProtokoll(string $version): int
    {
        $anweisung = $this->pdo->prepare('SELECT COUNT(*) FROM schema_migrations WHERE version = ?');
        $anweisung->execute([$version]);

        return (int) $anweisung->fetchColumn();
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
