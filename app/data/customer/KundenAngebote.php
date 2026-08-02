<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\Db;

/**
 * Kundenseitiger Zugriff auf `offers`.
 *
 * `offers` traegt selbst keine `organization_id` — sie haengt ueber `projects` daran. Der
 * Filter laeuft deshalb als Verbund, aber er laeuft: Ein Angebot ohne eigenes Projekt ist
 * fuer den Kunden nicht auffindbar.
 *
 * §5.2: `entwurf` ist fuer Kunden unsichtbar. Das ist kein Anzeigedetail — ein Entwurf ist
 * ein noch nicht abgegebenes Angebot.
 */
final class KundenAngebote
{
    /** Zustaende, die ein Kunde ueberhaupt sehen darf (§5.2). */
    private const SICHTBAR = ['gesendet', 'angenommen', 'abgelaufen', 'zurueckgezogen'];

    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return array<string,mixed>|null Das juengste sichtbare Angebot des eigenen Projekts. */
    public function aktuelles(): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT o.* FROM offers o'
            . ' JOIN projects p ON p.id = o.project_id'
            . ' WHERE p.organization_id = ? AND p.archived_at IS NULL'
            . ' AND o.status IN (?, ?, ?, ?)'
            . ' ORDER BY o.created_at DESC LIMIT 1'
        );
        $anweisung->execute([$this->bereich->organisationId, ...self::SICHTBAR]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** @return array<string,mixed>|null null bedeutet 404, nicht 403. */
    public function finden(string $angebotId): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT o.* FROM offers o'
            . ' JOIN projects p ON p.id = o.project_id'
            . ' WHERE o.id = ? AND p.organization_id = ? AND p.archived_at IS NULL'
            . ' AND o.status IN (?, ?, ?, ?)'
        );
        $anweisung->execute([$angebotId, $this->bereich->organisationId, ...self::SICHTBAR]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** @return list<array<string,mixed>> */
    public function liste(): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT o.* FROM offers o'
            . ' JOIN projects p ON p.id = o.project_id'
            . ' WHERE p.organization_id = ? AND p.archived_at IS NULL'
            . ' AND o.status IN (?, ?, ?, ?)'
            . ' ORDER BY o.created_at DESC'
        );
        $anweisung->execute([$this->bereich->organisationId, ...self::SICHTBAR]);

        return $anweisung->fetchAll();
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
