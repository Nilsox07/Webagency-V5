<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Der eine Lesezugriff, den die Anmeldung braucht — und nicht mehr.
 *
 * Die Anmeldung kann nicht ueber die Adminschicht gehen: Die verlangt einen AdminNachweis,
 * und den stellt die Anmeldung ja gerade erst her. Statt den Nachweis dafuer aufzuweichen,
 * gibt es hier einen eigenen, absichtlich schmalen Weg:
 *
 *   - liest genau ein Konto, adressiert ueber E-Mail oder Schluessel
 *   - kennt kein „alle Benutzer", keine Liste, keinen Filter
 *   - schreibt nur last_login_at
 *
 * Damit bleibt §3 Regel 2a unangetastet: Die Adminschicht ist weiterhin die einzige, die
 * organisationsuebergreifend lesen darf.
 */
final class AnmeldeKonten
{
    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    /** @return array<string,mixed>|null */
    public function adminNachEmail(string $email): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT id, email, role, password_hash, totp_secret_enc FROM users'
            . ' WHERE email = ? AND role = ? AND archived_at IS NULL'
        );
        $anweisung->execute([mb_strtolower(trim($email)), 'admin']);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** @return array<string,mixed>|null */
    public function adminNachId(string $id): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT id, email, role, password_hash, totp_secret_enc FROM users'
            . ' WHERE id = ? AND role = ? AND archived_at IS NULL'
        );
        $anweisung->execute([$id, 'admin']);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    public function anmeldungVermerken(string $benutzerId): void
    {
        $anweisung = $this->pdo()->prepare('UPDATE users SET last_login_at = ? WHERE id = ?');
        $anweisung->execute([Db::jetzt(), $benutzerId]);
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
