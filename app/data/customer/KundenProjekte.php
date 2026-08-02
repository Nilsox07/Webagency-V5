<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\Db;

/**
 * Kundenseitiger Zugriff auf `projects`.
 *
 * Jede Abfrage filtert nach `$bereich->organisationId` — dem Wert aus der Sitzung. Es gibt
 * keine Methode ohne diesen Filter und keinen Parameter, der ihn ersetzt.
 *
 * §4a: „In Stufe 0 hat eine Organisation genau ein aktives Projekt." Die Oberflaeche zeigt
 * immer das juengste nicht archivierte.
 */
final class KundenProjekte
{
    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return array<string,mixed>|null */
    public function aktuelles(): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM projects WHERE organization_id = ? AND archived_at IS NULL'
            . ' ORDER BY created_at DESC LIMIT 1'
        );
        $anweisung->execute([$this->bereich->organisationId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /**
     * §3 Regel 2: existiert es UND gehoert es zur Sitzungsorganisation? Sonst null — und der
     * Aufrufer macht daraus 404, nicht 403.
     *
     * @return array<string,mixed>|null
     */
    public function finden(string $projektId): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM projects WHERE id = ? AND organization_id = ? AND archived_at IS NULL'
        );
        $anweisung->execute([$projektId, $this->bereich->organisationId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** @return list<array<string,mixed>> */
    public function liste(): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM projects WHERE organization_id = ? AND archived_at IS NULL ORDER BY created_at DESC'
        );
        $anweisung->execute([$this->bereich->organisationId]);

        return $anweisung->fetchAll();
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
