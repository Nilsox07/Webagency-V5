<?php

declare(strict_types=1);

namespace Sartu\Data\Admin;

use Sartu\Data\Db;
use Sartu\Data\Uuid;

/**
 * Adminseitiger Zugriff auf `users`.
 *
 * Zaehlen und Anmelden stehen NICHT hier, sondern in AnmeldeKonten: Beides braucht keinen
 * Nachweis, und zwei Fassungen derselben Abfrage laufen irgendwann auseinander.
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

    /**
     * Legt ein Kundenkonto an — §4b.5, „In Kunde und Projekt umwandeln".
     *
     * Spiegelbild zu `adminAnlegen()`: `organization_id` ist hier **Pflicht**, und es gibt
     * weder Passwort noch TOTP-Geheimnis. Der Kunde meldet sich ausschliesslich per
     * Anmeldelink an (§6) — ein Passwortfeld waere eine zweite Angriffsflaeche fuer einen
     * Weg, den es nicht gibt.
     *
     * §5 „Nicht bauen": mehrere Benutzer je Kunde. Diese Methode legt genau einen an; die
     * Eindeutigkeit der Adresse erzwingt `uq_users_email`.
     */
    public function kundeAnlegen(
        string $organisationId,
        string $email,
        string $vorname,
        string $nachname,
    ): string {
        $id = Uuid::v4();

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO users (id, organization_id, email, first_name, last_name, role)'
            . ' VALUES (?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([$id, $organisationId, mb_strtolower(trim($email)), $vorname, $nachname, 'kunde']);

        return $id;
    }

    /** Fuer die Umwandlung: Gibt es zu dieser Adresse schon ein Konto? */
    public function kennteEmail(string $email): bool
    {
        $anweisung = $this->pdo()->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $anweisung->execute([mb_strtolower(trim($email))]);

        return (int) $anweisung->fetchColumn() > 0;
    }

    /** @return array<string,mixed>|null */
    public function finden(string $id): ?array
    {
        $anweisung = $this->pdo()->prepare('SELECT * FROM users WHERE id = ?');
        $anweisung->execute([$id]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
