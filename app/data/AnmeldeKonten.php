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

    /** Gibt es ueberhaupt schon ein Adminkonto? Eine Zahl, kein Datenzugriff auf Konten. */
    public function anzahlAdmins(): int
    {
        $anweisung = $this->pdo()->prepare('SELECT COUNT(*) FROM users WHERE role = ?');
        $anweisung->execute(['admin']);

        return (int) $anweisung->fetchColumn();
    }

    public function anmeldungVermerken(string $benutzerId): void
    {
        $anweisung = $this->pdo()->prepare('UPDATE users SET last_login_at = ? WHERE id = ?');
        $anweisung->execute([Db::jetzt(), $benutzerId]);
    }

    // ------------------------------------------------------------ Kundenanmeldung (§6)

    /**
     * Genau ein Kundenkonto, adressiert ueber die E-Mail.
     *
     * Drei Bedingungen, jede mit eigenem Grund:
     *
     *   role = 'kunde'          ein Admin bekommt keinen Anmeldelink (§3 Regel 4)
     *   archived_at IS NULL     ein archiviertes Konto meldet sich nicht mehr an (§3 Regel 13)
     *   organization_id NOT NULL  ohne Organisation gibt es keinen Kundenzugriff (§3 Regel 1)
     *
     * Die dritte ist bereits durch die Pruefbedingung auf `users` gesichert. Sie steht
     * trotzdem hier: Eine Sitzung ohne Organisation ist der Zustand, gegen den die ganze
     * Mandantentrennung gebaut ist, und sie darf hier nicht entstehen koennen.
     *
     * @return array<string,mixed>|null
     */
    public function kundeNachEmail(string $email): ?array
    {
        return $this->kunde('email', $email);
    }

    /** @return array<string,mixed>|null */
    public function kundeNachId(string $id): ?array
    {
        return $this->kunde('id', $id);
    }

    /** §7: Die Willkommensstrecke erscheint einmalig, solange `welcome_seen_at` leer ist. */
    public function willkommenOffen(string $benutzerId): bool
    {
        $anweisung = $this->pdo()->prepare('SELECT welcome_seen_at FROM users WHERE id = ?');
        $anweisung->execute([$benutzerId]);

        return $anweisung->fetchColumn() === null;
    }

    /**
     * `IS NULL` in der Bedingung, nicht nur im Wert: Zweimal „gesehen" darf den Zeitpunkt
     * des ersten Males nicht ueberschreiben.
     */
    public function willkommenVermerken(string $benutzerId): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE users SET welcome_seen_at = ? WHERE id = ? AND welcome_seen_at IS NULL'
        );
        $anweisung->execute([Db::jetzt(), $benutzerId]);
    }

    /** @return array<string,mixed>|null */
    private function kunde(string $spalte, string $wert): ?array
    {
        // $spalte kommt ausschliesslich aus den beiden Aufrufern oben, nie von aussen.
        $anweisung = $this->pdo()->prepare(
            'SELECT id, email, first_name, last_name, organization_id, welcome_seen_at FROM users'
            . ' WHERE ' . ($spalte === 'id' ? 'id' : 'email') . ' = ?'
            . " AND role = 'kunde' AND archived_at IS NULL AND organization_id IS NOT NULL"
        );
        $anweisung->execute([$wert]);

        $zeile = $anweisung->fetch(\PDO::FETCH_ASSOC);

        return is_array($zeile) ? $zeile : null;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
