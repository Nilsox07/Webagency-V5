<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Serverseitige Sitzungen — Portal-Lastenheft §3 Regel 6.
 *
 * Gespeichert wird nur der Hash, nie das Token selbst. Wer die Datenbank liest, kann sich
 * damit nicht anmelden. Verfallszeit 30 Tage; bei Abmeldung wird der Eintrag serverseitig
 * geloescht — Sitzungen sind keine fachlichen Datensaetze, fuer die §3 Regel 13 gilt.
 */
final class SitzungsSpeicher
{
    public const GUELTIGKEIT_TAGE = 30;

    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    /** @return array{token:string,id:string} Das Klartexttoken verlaesst die Methode genau einmal. */
    public function anlegen(string $benutzerId, ?string $benutzerkennung, ?string $ip): array
    {
        $token = bin2hex(random_bytes(32));
        $id = Uuid::v4();

        $ablauf = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
            ->modify('+' . self::GUELTIGKEIT_TAGE . ' days')
            ->format('Y-m-d H:i:s');

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO sessions (id, user_id, token_hash, expires_at, user_agent, ip) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([$id, $benutzerId, $this->hash($token), $ablauf, $benutzerkennung, $ip]);

        return ['token' => $token, 'id' => $id];
    }

    /** @return array<string,mixed>|null */
    public function finden(string $token): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM sessions WHERE token_hash = ? AND expires_at > ?'
        );
        $anweisung->execute([$this->hash($token), Db::jetzt()]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    public function loeschen(string $token): void
    {
        $anweisung = $this->pdo()->prepare('DELETE FROM sessions WHERE token_hash = ?');
        $anweisung->execute([$this->hash($token)]);
    }

    public function abgelaufeneAufraeumen(): int
    {
        $anweisung = $this->pdo()->prepare('DELETE FROM sessions WHERE expires_at <= ?');
        $anweisung->execute([Db::jetzt()]);

        return $anweisung->rowCount();
    }

    private function hash(string $token): string
    {
        return hash('sha256', $token);
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
