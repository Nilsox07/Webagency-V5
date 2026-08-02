<?php

declare(strict_types=1);

namespace Sartu\Tests;

use PHPUnit\Framework\TestCase;
use Sartu\Data\Db;
use Sartu\Data\Migrator;
use Sartu\Helpers\Env;
use Sartu\Sitzung;

/**
 * Grundlage aller Datenbanktests.
 *
 * Das Schema entsteht ueber den ECHTEN Migrator aus den echten Migrationsdateien — nicht
 * ueber ein eigenes Testschema. Sonst prueft der Test etwas anderes, als die Produktion
 * anlegt, und genau diese Abweichung faellt nie auf.
 */
abstract class Datenbankfall extends TestCase
{
    protected \PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = Db::oeffnen(
            Env::get('DB_HOST_TEST', 'db_test') ?? 'db_test',
            Env::get('DB_PORT', '3306') ?? '3306',
            Env::get('DB_NAME_TEST', 'sartu_test') ?? 'sartu_test',
            Env::require('DB_USER'),
            Env::get('DB_PASS', '') ?? '',
        );

        Db::setzen($this->pdo);

        $this->schemaNeuAufbauen();

        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        Db::setzen(null);
        $_SESSION = [];

        parent::tearDown();
    }

    protected function schemaNeuAufbauen(): void
    {
        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($this->tabellen() as $tabelle) {
            $this->pdo->exec('DROP TABLE IF EXISTS `' . $tabelle . '`');
        }

        $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        $migrator = new Migrator($this->pdo, SARTU_WURZEL . '/migrations');
        $migrator->protokolltabelleAnlegen();
        $migrator->offeneEinspielen();
    }

    /** @return list<string> */
    protected function tabellen(): array
    {
        $zeilen = $this->pdo->query(
            'SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()'
        )->fetchAll(\PDO::FETCH_NUM);

        return array_map(static fn (array $z) => (string) $z[0], $zeilen);
    }

    protected function organisationAnlegen(string $name, string $email): string
    {
        $id = \Sartu\Data\Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO organizations (id, legal_name, contact_email) VALUES (?, ?, ?)'
        );
        $anweisung->execute([$id, $name, $email]);

        return $id;
    }

    protected function kundeAnlegen(string $organisationId, string $email): string
    {
        $id = \Sartu\Data\Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO users (id, organization_id, email, role) VALUES (?, ?, ?, ?)'
        );
        $anweisung->execute([$id, $organisationId, $email, 'kunde']);

        return $id;
    }

    protected function adminAnlegen(string $email = 'admin@example.org'): string
    {
        $id = \Sartu\Data\Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO users (id, organization_id, email, role, password_hash) VALUES (?, NULL, ?, ?, ?)'
        );
        $anweisung->execute([$id, $email, 'admin', password_hash('einlangespasswort', PASSWORD_ARGON2ID)]);

        return $id;
    }

    /** Versetzt die Sitzung in den Zustand „Kunde angemeldet". */
    protected function alsKunde(string $organisationId, string $benutzerId): void
    {
        $_SESSION[Sitzung::BENUTZER]     = $benutzerId;
        $_SESSION[Sitzung::ROLLE]        = 'kunde';
        $_SESSION[Sitzung::ORGANISATION] = $organisationId;
    }

    /**
     * Versetzt die Sitzung in den Zustand „Admin angemeldet, TOTP bestaetigt".
     *
     * Dazu gehoert eine echte Zeile in `sessions`: Der Router prueft nicht nur den
     * Sitzungszustand, sondern auch, dass die Anmeldung serverseitig noch gilt
     * (§3 Regel 6). Ohne die Zeile ist der Zustand kein angemeldeter.
     */
    protected function alsAdmin(string $benutzerId): void
    {
        $_SESSION[Sitzung::BENUTZER]        = $benutzerId;
        $_SESSION[Sitzung::ROLLE]           = 'admin';
        $_SESSION[Sitzung::ORGANISATION]    = null;
        $_SESSION[Sitzung::TOTP_BESTAETIGT] = Db::jetzt();

        $sitzung = (new \Sartu\Data\SitzungsSpeicher($this->pdo))->anlegen($benutzerId, 'Testlauf', '127.0.0.1');
        $_SESSION[\Sartu\Services\AnmeldeDienst::SITZUNGSTOKEN] = $sitzung['token'];
    }
}
