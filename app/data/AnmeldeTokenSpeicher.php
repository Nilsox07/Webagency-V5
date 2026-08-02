<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Anmeldelinks — Portal-Lastenheft §4 `login_tokens` und §3 Regel 5.
 *
 * „Kryptografisch zufaellig (>= 32 Byte), **nur als Hash gespeichert**, gueltig **15
 * Minuten**, **einmalig** verwendbar, an die E-Mail gebunden."
 *
 * Die Bindung an die E-Mail entsteht dadurch, dass der Token an einer `user_id` haengt und
 * die Mail nur an die hinterlegte Adresse geht. Ein Token fuer eine andere Adresse gibt es
 * nicht — er waere ein Token fuer einen anderen Benutzer (Testfall 8).
 *
 * Wie `AnmeldeKonten` liegt diese Klasse ausserhalb beider Zugriffsschichten: Sie wird
 * gebraucht, bevor jemand angemeldet ist.
 */
final class AnmeldeTokenSpeicher
{
    public const GUELTIGKEIT_MINUTEN = 15;

    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    /** @return string das Klartexttoken — es verlaesst die Methode genau einmal, fuer die Mail */
    public function anlegen(string $benutzerId, ?string $ip): string
    {
        $token = bin2hex(random_bytes(32));

        $ablauf = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
            ->modify('+' . self::GUELTIGKEIT_MINUTEN . ' minutes')
            ->format('Y-m-d H:i:s');

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO login_tokens (id, user_id, token_hash, expires_at, requested_ip) VALUES (?, ?, ?, ?, ?)'
        );
        $anweisung->execute([Uuid::v4(), $benutzerId, $this->hash($token), $ablauf, $ip]);

        return $token;
    }

    /**
     * Loest einen Token ein — genau einmal.
     *
     * Das Markieren laeuft als bedingtes UPDATE: Nur wer `used_at IS NULL` vorfindet,
     * bekommt die Zeile. Zwei gleichzeitige Klicks auf denselben Link koennen sich damit
     * nicht beide anmelden (Testfall 6).
     *
     * @return string|null die `user_id` bei Erfolg
     */
    public function einloesen(string $token): ?string
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE login_tokens SET used_at = ? WHERE token_hash = ? AND used_at IS NULL AND expires_at > ?'
        );
        $anweisung->execute([Db::jetzt(), $this->hash($token), Db::jetzt()]);

        if ($anweisung->rowCount() !== 1) {
            return null;
        }

        $lesen = $this->pdo()->prepare('SELECT user_id FROM login_tokens WHERE token_hash = ?');
        $lesen->execute([$this->hash($token)]);

        $benutzerId = $lesen->fetchColumn();

        return is_string($benutzerId) ? $benutzerId : null;
    }

    /** Fuer den Adminbereich (§6.3): Wann ging der letzte Link raus, und wurde er benutzt? */
    public function letzterFuerBenutzer(string $benutzerId): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT created_at, used_at, expires_at FROM login_tokens WHERE user_id = ?'
            . ' ORDER BY created_at DESC LIMIT 1'
        );
        $anweisung->execute([$benutzerId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    public function abgelaufeneAufraeumen(): int
    {
        $anweisung = $this->pdo()->prepare('DELETE FROM login_tokens WHERE expires_at <= ?');
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
