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

    /**
     * Alle Nachrichten, unbeantwortete zuerst — §9.1 `/admin/nachrichten`.
     *
     * Beantwortete bleiben sichtbar: Eine Antwort, die man nach dem Absenden nicht mehr
     * nachlesen kann, ist im Streitfall keine.
     *
     * @return list<array<string,mixed>>
     */
    public function alleNachrichten(): array
    {
        return $this->pdo()->query(
            'SELECT s.*, o.legal_name, p.title AS project_title FROM support_messages s'
            . ' JOIN organizations o ON o.id = s.organization_id'
            . ' LEFT JOIN projects p ON p.id = s.project_id'
            . ' WHERE s.archived_at IS NULL'
            . ' ORDER BY s.answered_at IS NOT NULL, s.created_at ASC'
        )->fetchAll();
    }

    /** @return array<string,mixed>|null */
    public function nachricht(string $nachrichtId): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT s.*, o.legal_name FROM support_messages s'
            . ' JOIN organizations o ON o.id = s.organization_id'
            . ' WHERE s.id = ? AND s.archived_at IS NULL'
        );
        $anweisung->execute([$nachrichtId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /**
     * Trägt die Antwort ein — **genau einmal**.
     *
     * `answered_at IS NULL` steht in der Bedingung, nicht nur im Wert. Ohne sie überschriebe
     * ein zweiter Klick die erste Antwort, und der Kunde bekäme eine zweite Mail zu einer
     * Nachricht, die er längst beantwortet gesehen hat.
     *
     * @return bool `false`, wenn die Nachricht bereits beantwortet war
     */
    public function nachrichtBeantworten(string $nachrichtId, string $antwort): bool
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE support_messages SET answer_text = ?, answered_at = ?'
            . ' WHERE id = ? AND answered_at IS NULL'
        );
        $anweisung->execute([$antwort, Db::jetzt(), $nachrichtId]);

        return $anweisung->rowCount() === 1;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
