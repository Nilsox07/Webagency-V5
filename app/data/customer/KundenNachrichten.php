<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\Db;
use Sartu\Data\Uuid;

/**
 * Kundenseitiger Zugriff auf `support_messages` — Portal-Lastenheft §4, §8.9.
 *
 * Hier filtert die eigene Spalte: §4 laesst `project_id` ausdruecklich leer — eine Frage
 * kann vor dem ersten Projekt kommen. Ohne `support_messages.organization_id` gaebe es
 * fuer diese Nachrichten keinen Filter.
 */
final class KundenNachrichten
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
            'SELECT * FROM support_messages WHERE organization_id = ? AND archived_at IS NULL'
            . ' ORDER BY created_at DESC'
        );
        $anweisung->execute([$this->bereich->organisationId]);

        return $anweisung->fetchAll();
    }

    public function anlegen(string $text, ?string $projektId, string $benutzerId): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO support_messages (id, organization_id, project_id, body, created_by_user_id)'
            . ' VALUES (?, ?, ?, ?, ?)'
        );
        $anweisung->execute([$id, $this->bereich->organisationId, $projektId, $text, $benutzerId]);

        return $id;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
