<?php

declare(strict_types=1);

namespace Sartu\Data\Admin;

use Sartu\Data\Db;
use Sartu\Data\Uuid;

/**
 * Adminseitiger Zugriff auf `users`.
 *
 * `password_hash` und `totp_secret_enc` verlassen diese Klasse nur ueber die ausdruecklich
 * dafuer gebauten Methoden. Portal-Lastenheft §1.5: Zugangsdaten werden nie angezeigt, nie
 * protokolliert, nie in eine Fehlermeldung geschrieben — auch nicht teilweise.
 */
final class AdminBenutzer
{
    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * Legt ein Adminkonto an. `organization_id` bleibt NULL — die Pruefbedingung aus §4
     * erzwingt das ohnehin, und ein Admin mit Organisation waere ein Loch in der Trennung.
     */
    public function adminAnlegen(
        string $email,
        string $vorname,
        string $nachname,
        string $passwortHash,
        string $totpGeheimnisVerschluesselt,
    ): string {
        $id = Uuid::v4();

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO users (id, organization_id, email, first_name, last_name, role, password_hash, totp_secret_enc)'
            . ' VALUES (?, NULL, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            $id,
            mb_strtolower(trim($email)),
            $vorname,
            $nachname,
            'admin',
            $passwortHash,
            $totpGeheimnisVerschluesselt,
        ]);

        return $id;
    }

    /** @return array<string,mixed>|null */
    public function nachEmail(string $email): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM users WHERE email = ? AND role = ? AND archived_at IS NULL'
        );
        $anweisung->execute([mb_strtolower(trim($email)), 'admin']);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** @return array<string,mixed>|null */
    public function finden(string $id): ?array
    {
        $anweisung = $this->pdo()->prepare('SELECT * FROM users WHERE id = ?');
        $anweisung->execute([$id]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    public function anmeldungVermerken(string $benutzerId): void
    {
        $anweisung = $this->pdo()->prepare('UPDATE users SET last_login_at = ? WHERE id = ?');
        $anweisung->execute([Db::jetzt(), $benutzerId]);
    }

    public function anzahlAdmins(): int
    {
        $anweisung = $this->pdo()->prepare('SELECT COUNT(*) FROM users WHERE role = ?');
        $anweisung->execute(['admin']);

        return (int) $anweisung->fetchColumn();
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
