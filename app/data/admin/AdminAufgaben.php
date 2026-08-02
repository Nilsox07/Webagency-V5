<?php

declare(strict_types=1);

namespace Sartu\Data\Admin;

use Sartu\Data\Db;
use Sartu\Data\Uuid;

/**
 * Adminseitiger Zugriff auf `tasks` und `support_messages` — Portal-Lastenheft §4, §8.3.
 *
 * Eigene Klasse neben `Customer\KundenAufgaben`, kein gemeinsamer Codepfad (§3 Regel 2).
 */
final class AdminAufgaben
{
    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return list<array<string,mixed>> */
    public function jeProjekt(string $projektId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM tasks WHERE project_id = ? AND archived_at IS NULL'
            . ' ORDER BY sort_order ASC, created_at ASC'
        );
        $anweisung->execute([$projektId]);

        return $anweisung->fetchAll();
    }

    /** @param array<string,scalar|null> $werte */
    public function anlegen(array $werte): string
    {
        $id = Uuid::v4();
        $spalten = array_keys($werte);

        $anweisung = $this->pdo()->prepare(sprintf(
            'INSERT INTO tasks (id, %s) VALUES (?, %s)',
            implode(', ', $spalten),
            implode(', ', array_fill(0, count($spalten), '?')),
        ));
        $anweisung->execute([$id, ...array_values($werte)]);

        return $id;
    }

    /** @return list<array<string,mixed>> */
    public function offeneNachrichten(): array
    {
        return $this->pdo()->query(
            'SELECT s.*, o.legal_name FROM support_messages s'
            . ' JOIN organizations o ON o.id = s.organization_id'
            . ' WHERE s.answered_at IS NULL AND s.archived_at IS NULL ORDER BY s.created_at ASC'
        )->fetchAll();
    }

    public function nachrichtBeantworten(string $nachrichtId, string $antwort): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE support_messages SET answer_text = ?, answered_at = ? WHERE id = ?'
        );
        $anweisung->execute([$antwort, Db::jetzt(), $nachrichtId]);
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
