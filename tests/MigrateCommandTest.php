<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Services\Wartungsmodus;

/**
 * Nachträgliche Migration über die Befehlszeile — Portal-Lastenheft §1.5a.
 *
 * Testfälle: 74 · 75 · 76
 *
 * Der Befehl wird als eigener Prozess gestartet, nicht nachgebildet. Ein nachgebauter
 * Ablauf prüft den Nachbau, nicht den Befehl, den später jemand auf dem Server eintippt.
 */
final class MigrateCommandTest extends Datenbankfall
{
    /** @var list<string> */
    private array $wegwerfskripte = [];

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        (new Wartungsmodus($this->arbeitsverzeichnis))->ausschalten();

        foreach ($this->wegwerfskripte as $skript) {
            @unlink($skript);
        }

        parent::tearDown();
    }

    // ------------------------------------------------------------ Fall 74

    /**
     * Fall 74 — `status` auf einer NICHT leeren Datenbank listet die offenen Migrationen
     * und verändert nichts.
     */
    public function testStatusListetUndVeraendertNichts(): void
    {
        $vorher = $this->tabellen();
        $vorherProtokoll = $this->protokollstand();

        [$code, $ausgabe] = $this->befehl(['status']);

        $this->assertSame(0, $code, $ausgabe);
        // Die Zahl kommt aus dem Verzeichnis, nicht aus dem Kopf: Sonst ist der Test bei
        // jeder neuen Migration rot, ohne dass etwas kaputt ist.
        $this->assertStringContainsString(
            sprintf('Eingespielt: %d · offen: 0', count((array) glob(SARTU_WURZEL . '/migrations/*.sql'))),
            $ausgabe
        );
        $this->assertStringContainsString('001_organizations', $ausgabe);

        $this->assertSame($vorher, $this->tabellen(), 'status hat das Schema verändert.');
        $this->assertSame($vorherProtokoll, $this->protokollstand(), 'status hat das Protokoll verändert.');
    }

    public function testVerifyPrueftNurDiePruefsummen(): void
    {
        [$code, $ausgabe] = $this->befehl(['verify']);

        $this->assertSame(0, $code, $ausgabe);
        $this->assertStringContainsString('Pruefsummen', $ausgabe);
    }

    // ------------------------------------------------------------ Fall 75

    /** Fall 75 — `up` ohne angegebene Sicherungsdatei bricht ab. */
    public function testUpOhneSicherungBrichtAb(): void
    {
        [$code, $ausgabe] = $this->befehl(['up']);

        $this->assertSame(1, $code);
        $this->assertStringContainsString('--backup=', $ausgabe);
    }

    /** Ebenso bei angegebener, aber fehlender Datei. */
    public function testUpMitFehlenderSicherungsdateiBrichtAb(): void
    {
        [$code, $ausgabe] = $this->befehl(['up', '--backup=' . $this->arbeitsverzeichnis . '/gibt-es-nicht.sql']);

        $this->assertSame(1, $code);
        $this->assertStringContainsString('fehlt oder ist leer', $ausgabe);
    }

    /** Und bei einer leeren Datei. */
    public function testUpMitLeererSicherungsdateiBrichtAb(): void
    {
        $datei = $this->arbeitsverzeichnis . '/leer.sql';
        touch($datei);

        [$code, $ausgabe] = $this->befehl(['up', '--backup=' . $datei]);

        $this->assertSame(1, $code);
        $this->assertStringContainsString('fehlt oder ist leer', $ausgabe);
    }

    // ------------------------------------------------------------ Fall 76

    /**
     * Fall 76 — während `up` liefern Kunden- und Adminbereich 503; nach Erfolg ist der
     * Wartungsmodus aufgehoben.
     */
    public function testNachErfolgreichemUpIstDerWartungsmodusAufgehoben(): void
    {
        $sicherung = $this->arbeitsverzeichnis . '/sicherung.sql';
        file_put_contents($sicherung, "-- Sicherung für den Test\n");

        [$code, $ausgabe] = $this->befehl(['up', '--backup=' . $sicherung]);

        $this->assertSame(0, $code, $ausgabe);
        $this->assertStringContainsString('nichts einzuspielen', $ausgabe);
        $this->assertFalse(
            (new Wartungsmodus($this->arbeitsverzeichnis))->aktiv(),
            'Der Wartungsmodus blieb nach einem erfolgreichen Lauf bestehen.'
        );
    }

    /** Nach einem Abbruch bleibt der Wartungsmodus bestehen — ein halbes Schema lässt niemanden herein. */
    public function testNachAbbruchBleibtDerWartungsmodusBestehen(): void
    {
        $sicherung = $this->arbeitsverzeichnis . '/sicherung.sql';
        file_put_contents($sicherung, "-- Sicherung für den Test\n");

        // Alle eingespielten Migrationen muessen im Verzeichnis liegen, sonst schlaegt
        // der Pruefsummenabgleich an — und zwar aus dem falschen Grund.
        $eigenes = $this->arbeitsverzeichnis . '/migrations';
        mkdir($eigenes, 0770, true);

        foreach ((array) glob(SARTU_WURZEL . '/migrations/*.sql') as $datei) {
            copy((string) $datei, $eigenes . '/' . basename((string) $datei));
        }

        // Und eine Migration, die scheitern muss: Die Tabelle gibt es bereits.
        file_put_contents($eigenes . '/009_kollision.sql', 'CREATE TABLE organizations (id INT PRIMARY KEY);');

        [$code, $ausgabe] = $this->befehl(['up', '--backup=' . $sicherung], $eigenes);

        $this->assertSame(1, $code);
        $this->assertStringContainsString('009_kollision.sql', $ausgabe);
        $this->assertStringContainsString('Wartungsmodus bleibt aktiv', $ausgabe);
        $this->assertTrue((new Wartungsmodus($this->arbeitsverzeichnis))->aktiv());

        foreach ((array) glob($eigenes . '/*.sql') as $datei) {
            unlink((string) $datei);
        }

        rmdir($eigenes);
    }

    /** §1.5a: Kein `up` über das Netz. Der Befehl läuft nur über die Befehlszeile. */
    public function testMigrationsbefehlIstUeberDasNetzNichtErreichbar(): void
    {
        $routen = require SARTU_WURZEL . '/app/routes.php';

        foreach ($routen as $route) {
            $this->assertStringNotContainsString(
                'migrate',
                strtolower($route->pfad),
                'Der Migrationsbefehl hat eine Route bekommen.'
            );
        }

        // Setup-Schritt 4 spielt die Migrationen ueber das Netz ein — das ist §1.5 und
        // ausdruecklich gewollt. Verboten ist der Weg NACH der Ersteinrichtung, und den
        // sperrt die Installationssperre: /admin/setup liefert danach 404.
        $inhalt = (string) file_get_contents(SARTU_WURZEL . '/app/Router.php');
        $this->assertStringContainsString('istEinrichtung', $inhalt);

        $inhalt = (string) file_get_contents(SARTU_WURZEL . '/bin/migrate.php');
        $this->assertStringContainsString("PHP_SAPI !== 'cli'", $inhalt);
    }

    // ------------------------------------------------------------

    /**
     * @param list<string> $argumente
     * @return array{0:int,1:string}
     */
    private function befehl(array $argumente, ?string $migrationsverzeichnis = null): array
    {
        $umgebung = [
            'PATH'        => getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin',
            'APP_ENV'     => 'local',
            'DB_HOST'     => \Sartu\Helpers\Env::get('DB_HOST_TEST', 'db_test'),
            'DB_PORT'     => \Sartu\Helpers\Env::get('DB_PORT', '3306'),
            'DB_NAME'     => \Sartu\Helpers\Env::get('DB_NAME_TEST', 'sartu_test'),
            'DB_USER'     => \Sartu\Helpers\Env::require('DB_USER'),
            'DB_PASS'     => \Sartu\Helpers\Env::get('DB_PASS', ''),
            'STORAGE_DIR' => $this->arbeitsverzeichnis,
        ];

        $skript = SARTU_WURZEL . '/bin/migrate.php';

        if ($migrationsverzeichnis !== null) {
            // Ein eigenes Verzeichnis wird über eine Kopie des Skripts erreicht, die auf
            // dieses Verzeichnis zeigt — der Befehl selbst bleibt unverändert.
            $skript = $this->skriptMitEigenemVerzeichnis($migrationsverzeichnis);
        }

        $befehl = escapeshellcmd(PHP_BINARY) . ' ' . escapeshellarg($skript);

        foreach ($argumente as $argument) {
            $befehl .= ' ' . escapeshellarg($argument);
        }

        $rohre = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $prozess = proc_open($befehl, $rohre, $offen, SARTU_WURZEL, $umgebung);

        if (!is_resource($prozess)) {
            $this->fail('Der Befehl liess sich nicht starten.');
        }

        $ausgabe = (string) stream_get_contents($offen[1]) . (string) stream_get_contents($offen[2]);
        fclose($offen[1]);
        fclose($offen[2]);

        return [proc_close($prozess), $ausgabe];
    }

    private function skriptMitEigenemVerzeichnis(string $verzeichnis): string
    {
        $quelle = (string) file_get_contents(SARTU_WURZEL . '/bin/migrate.php');
        $angepasst = str_replace(
            "\$wurzel . '/migrations'",
            var_export($verzeichnis, true),
            $quelle
        );

        // Die Kopie muss in /bin liegen: Das Skript ermittelt die Projektwurzel ueber
        // dirname(__DIR__). Von woanders aus zeigt es ins Leere.
        $ziel = SARTU_WURZEL . '/bin/.migrate-test-' . bin2hex(random_bytes(4)) . '.php';
        file_put_contents($ziel, $angepasst);
        $this->wegwerfskripte[] = $ziel;

        return $ziel;
    }

    /** @return array<string,string> */
    private function protokollstand(): array
    {
        $stand = [];

        foreach ($this->pdo->query('SELECT version, checksum FROM schema_migrations ORDER BY version')->fetchAll() as $zeile) {
            $stand[(string) $zeile['version']] = (string) $zeile['checksum'];
        }

        return $stand;
    }
}
