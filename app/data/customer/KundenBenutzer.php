<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\Db;

/**
 * Kundenseitiger Zugriff auf `users`. Filtert immer nach der Organisation aus der Sitzung.
 */
final class KundenBenutzer
{
    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return list<array<string,mixed>> */
    public function liste(): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM users WHERE organization_id = ? AND archived_at IS NULL ORDER BY email'
        );
        $anweisung->execute([$this->bereich->organisationId]);

        return $anweisung->fetchAll();
    }

    /** @return array<string,mixed>|null null bedeutet 404, nicht 403. */
    public function finden(string $benutzerId): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM users WHERE id = ? AND organization_id = ? AND archived_at IS NULL'
        );
        $anweisung->execute([$benutzerId, $this->bereich->organisationId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
