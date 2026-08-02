<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\Db;

/**
 * Kundenseitiger Zugriff auf `tasks` — Portal-Lastenheft §4, §5.4, §8.3.
 *
 * Wie bei den Rechnungen geht der Filter ueber `projects`. Er steht in jeder einzelnen
 * Abfrage, nicht in einer gemeinsamen Hilfsmethode mit optionalem Parameter.
 */
final class KundenAufgaben
{
    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** §8.3: sortiert nach `sort_order`, erledigte rutschen nach unten. */
    public function liste(): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT t.* FROM tasks t'
            . ' JOIN projects p ON p.id = t.project_id'
            . ' WHERE p.organization_id = ? AND t.archived_at IS NULL'
            . " ORDER BY t.status = 'erledigt', t.sort_order ASC, t.created_at ASC"
        );
        $anweisung->execute([$this->bereich->organisationId]);

        return $anweisung->fetchAll();
    }

    /** @return array<string,mixed>|null */
    public function finden(string $aufgabeId): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT t.* FROM tasks t'
            . ' JOIN projects p ON p.id = t.project_id'
            . ' WHERE t.id = ? AND p.organization_id = ? AND t.archived_at IS NULL'
        );
        $anweisung->execute([$aufgabeId, $this->bereich->organisationId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /**
     * §8.3 Sperre: Die Freigabeaufgabe ist erst abschliessbar, wenn ALLE Pflichtaufgaben
     * erledigt sind (Testfall 26).
     *
     * Gezaehlt wird ohne die Freigabeaufgabe selbst — sonst blockierte sie sich gegenseitig.
     */
    public function offenePflichtaufgaben(string $projektId): int
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT COUNT(*) FROM tasks t'
            . ' JOIN projects p ON p.id = t.project_id'
            . ' WHERE t.project_id = ? AND p.organization_id = ?'
            . " AND t.required = 1 AND t.status = 'offen' AND t.kind <> 'freigabe'"
            . ' AND t.archived_at IS NULL'
        );
        $anweisung->execute([$projektId, $this->bereich->organisationId]);

        return (int) $anweisung->fetchColumn();
    }

    public function offeneGesamt(): int
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT COUNT(*) FROM tasks t'
            . ' JOIN projects p ON p.id = t.project_id'
            . " WHERE p.organization_id = ? AND t.status = 'offen' AND t.archived_at IS NULL"
        );
        $anweisung->execute([$this->bereich->organisationId]);

        return (int) $anweisung->fetchColumn();
    }

    /** Fuer die Freigabeanzeige: was der Kunde bereits beantwortet hat. */
    public function erledigteMitAntwort(string $projektId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT t.title, t.answer_text FROM tasks t'
            . ' JOIN projects p ON p.id = t.project_id'
            . ' WHERE t.project_id = ? AND p.organization_id = ?'
            . " AND t.status = 'erledigt' AND t.kind <> 'freigabe' AND t.archived_at IS NULL"
            . ' ORDER BY t.sort_order ASC'
        );
        $anweisung->execute([$projektId, $this->bereich->organisationId]);

        return $anweisung->fetchAll();
    }

    public function abschliessen(string $aufgabeId, ?string $antwort, string $benutzerId): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE tasks t JOIN projects p ON p.id = t.project_id'
            . " SET t.status = 'erledigt', t.answer_text = ?, t.completed_at = ?, t.completed_by_user_id = ?"
            . ' WHERE t.id = ? AND p.organization_id = ?'
        );
        $anweisung->execute([$antwort, Db::jetzt(), $benutzerId, $aufgabeId, $this->bereich->organisationId]);
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
