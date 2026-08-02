<?php

declare(strict_types=1);

namespace Sartu\Data\Admin;

use Sartu\Data\Db;
use Sartu\Data\Uuid;

/**
 * Adminseitiger Zugriff auf `projects` — organisationsuebergreifend, mit Nachweis.
 *
 * Die Auswahl eines Kunden im Adminbereich schreibt die Sitzungsorganisation **nie** um
 * (§3 Regel 2a). Diese Klasse liest deshalb nach `organization_id` als **Parameter** — das
 * ist erlaubt, weil sie die Adminschicht ist. Die Kundenschicht darf das nicht, und kann es
 * auch nicht: dort gibt es den Parameter nicht.
 */
final class AdminProjekte
{
    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return list<array<string,mixed>> */
    public function alle(): array
    {
        return $this->pdo()->query(
            'SELECT p.*, o.legal_name FROM projects p'
            . ' JOIN organizations o ON o.id = p.organization_id'
            . ' WHERE p.archived_at IS NULL ORDER BY p.created_at DESC'
        )->fetchAll();
    }

    /** @return array<string,mixed>|null */
    public function finden(string $id): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT p.*, o.legal_name FROM projects p'
            . ' JOIN organizations o ON o.id = p.organization_id'
            . ' WHERE p.id = ?'
        );
        $anweisung->execute([$id]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** @return list<array<string,mixed>> */
    public function jeOrganisation(string $organisationId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM projects WHERE organization_id = ? AND archived_at IS NULL ORDER BY created_at DESC'
        );
        $anweisung->execute([$organisationId]);

        return $anweisung->fetchAll();
    }

    public function anlegen(
        string $organisationId,
        string $titel,
        string $paket,
        int $korrekturrunden,
        ?string $schutzstufe,
        string $status,
    ): string {
        $id = Uuid::v4();

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO projects (id, organization_id, title, package, included_feedback_rounds,'
            . ' protection_level, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([$id, $organisationId, $titel, $paket, $korrekturrunden, $schutzstufe, $status]);

        return $id;
    }

    /**
     * Setzt den Zustand. **Ob** das Paar erlaubt ist, prueft `Projektstatus` gegen die
     * Uebergangstabelle aus §5.1a — nicht diese Klasse. Hier steht nur das Schreiben.
     */
    public function statusSetzen(string $id, string $neu, ?string $herkunftsstatus = null): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE projects SET status = ?, paused_from_status = ? WHERE id = ?'
        );
        $anweisung->execute([$neu, $herkunftsstatus, $id]);
    }

    public function pauseGrundSetzen(string $id, ?string $grund): void
    {
        $anweisung = $this->pdo()->prepare('UPDATE projects SET pause_reason = ? WHERE id = ?');
        $anweisung->execute([$grund, $id]);
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
