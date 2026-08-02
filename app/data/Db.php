<?php

declare(strict_types=1);

namespace Sartu\Data;

use Sartu\Helpers\Env;

/**
 * Die einzige Stelle, an der eine Datenbankverbindung entsteht.
 *
 * Portal-Lastenheft §4.0: DATETIME speichert keine Zeitzone. Damit CURRENT_TIMESTAMP in UTC
 * schreibt, setzt die Anwendung unmittelbar nach dem Verbindungsaufbau
 * SET time_zone = '+00:00'. Ohne diese Zeile landen Vorgabewerte in der lokalen Zeit des
 * Datenbankservers — falsch, aber unauffaellig. Deshalb steht sie hier und nirgends sonst.
 */
final class Db
{
    private static ?\PDO $verbindung = null;

    public static function verbindung(): \PDO
    {
        return self::$verbindung ??= self::oeffnen(
            Env::require('DB_HOST'),
            Env::get('DB_PORT', '3306') ?? '3306',
            Env::require('DB_NAME'),
            Env::require('DB_USER'),
            Env::get('DB_PASS', '') ?? '',
        );
    }

    public static function setzen(?\PDO $pdo): void
    {
        self::$verbindung = $pdo;
    }

    public static function oeffnen(
        string $host,
        string $port,
        string $name,
        string $benutzer,
        string $passwort,
    ): \PDO {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);

        $pdo = new \PDO($dsn, $benutzer, $passwort, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            // Echte vorbereitete Anweisungen, keine Nachbildung im Treiber (§3 Regel „nur PDO").
            \PDO::ATTR_EMULATE_PREPARES   => false,
            \PDO::ATTR_STRINGIFY_FETCHES  => false,
        ]);

        $pdo->exec("SET time_zone = '+00:00'");

        return $pdo;
    }

    /** Verbindung ohne Datenbanknamen — fuer die Vorpruefung der Ersteinrichtung (§1.5 Schritt 2). */
    public static function oeffnenOhneDatenbank(string $host, string $port, string $benutzer, string $passwort): \PDO
    {
        $pdo = new \PDO(
            sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $host, $port),
            $benutzer,
            $passwort,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_EMULATE_PREPARES => false],
        );

        $pdo->exec("SET time_zone = '+00:00'");

        return $pdo;
    }

    /**
     * Steht die Verbindung?
     *
     * Die Frage gehoert hierher und nicht in einen Dienst: §1.3 laesst Datenbankzugriff
     * ausschliesslich in /app/data zu, und ein `SELECT 1` ist einer.
     */
    public static function erreichbar(): bool
    {
        try {
            self::verbindung()->query('SELECT 1');

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public static function jetzt(): string
    {
        return (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d H:i:s');
    }
}
