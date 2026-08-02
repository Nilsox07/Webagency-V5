<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\Db;
use Sartu\Data\Uuid;

/**
 * Kundenseitiger Zugriff auf `task_files` — Portal-Lastenheft §4, §11.
 *
 * **Hier filtert die eigene Spalte, nicht ein Verbund.** §4 nennt `organization_id` in
 * `task_files` ausdruecklich „redundant, fuer die Mandantenpruefung". Der Grund steht in
 * Testfall 4: „Kunde A laedt Datei von B ueber direkte URL → 404." Diese Abfrage laeuft
 * bei jedem Dateiabruf, und ein Verbund ueber zwei Tabellen ist eine Gelegenheit mehr,
 * ihn falsch zu schreiben.
 *
 * Zusaetzlich wird der Verbund trotzdem geprueft: Die Datei muss zur Organisation gehoeren
 * UND ihre Aufgabe muss es auch. Eine Zeile mit falscher `organization_id` — durch einen
 * Fehler an anderer Stelle entstanden — kaeme sonst durch.
 */
final class KundenDateien
{
    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return list<array<string,mixed>> */
    public function jeAufgabe(string $aufgabeId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT f.* FROM task_files f'
            . ' JOIN tasks t ON t.id = f.task_id'
            . ' JOIN projects p ON p.id = t.project_id'
            . ' WHERE f.task_id = ? AND f.organization_id = ? AND p.organization_id = ?'
            . ' AND f.archived_at IS NULL ORDER BY f.created_at ASC'
        );
        $anweisung->execute([$aufgabeId, $this->bereich->organisationId, $this->bereich->organisationId]);

        return $anweisung->fetchAll();
    }

    public function anzahlJeAufgabe(string $aufgabeId): int
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT COUNT(*) FROM task_files WHERE task_id = ? AND organization_id = ? AND archived_at IS NULL'
        );
        $anweisung->execute([$aufgabeId, $this->bereich->organisationId]);

        return (int) $anweisung->fetchColumn();
    }

    /** @return array<string,mixed>|null */
    public function finden(string $dateiId): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT f.* FROM task_files f'
            . ' JOIN tasks t ON t.id = f.task_id'
            . ' JOIN projects p ON p.id = t.project_id'
            . ' WHERE f.id = ? AND f.organization_id = ? AND p.organization_id = ?'
            . ' AND f.archived_at IS NULL'
        );
        $anweisung->execute([$dateiId, $this->bereich->organisationId, $this->bereich->organisationId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** §11: Hoechstens 500 MB je Organisation insgesamt. */
    public function belegterSpeicher(): int
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT COALESCE(SUM(size_bytes), 0) FROM task_files'
            . ' WHERE organization_id = ? AND archived_at IS NULL'
        );
        $anweisung->execute([$this->bereich->organisationId]);

        return (int) $anweisung->fetchColumn();
    }

    /** @param array<string,scalar|null> $werte */
    public function anlegen(array $werte): string
    {
        $id = Uuid::v4();

        // `organization_id` kommt aus der Sitzung, nicht aus $werte. Es gibt keinen Weg,
        // eine Datei einer fremden Organisation zuzuordnen.
        $werte['organization_id'] = $this->bereich->organisationId;

        $spalten = array_keys($werte);

        $anweisung = $this->pdo()->prepare(sprintf(
            'INSERT INTO task_files (id, %s) VALUES (?, %s)',
            implode(', ', $spalten),
            implode(', ', array_fill(0, count($spalten), '?')),
        ));
        $anweisung->execute([$id, ...array_values($werte)]);

        return $id;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
