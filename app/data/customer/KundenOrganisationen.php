<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\Db;

/**
 * Kundenseitiger Zugriff auf `organizations`.
 *
 * Jede Abfrage hier filtert nach $bereich->organisationId — der Wert stammt aus der Sitzung
 * und kann nicht von aussen gesetzt werden. Es gibt keine Methode ohne diesen Filter.
 */
final class KundenOrganisationen
{
    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return array<string,mixed>|null */
    public function eigene(): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM organizations WHERE id = ? AND archived_at IS NULL'
        );
        $anweisung->execute([$this->bereich->organisationId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /**
     * §3 Regel 2: Existiert das Objekt UND gehoert es zur Sitzungsorganisation? Sonst 404,
     * nicht 403 — 403 verriete die Existenz.
     */
    public function gehoertMir(string $organisationId): bool
    {
        return hash_equals($this->bereich->organisationId, $organisationId);
    }

    /** @return list<array<string,mixed>> */
    public function liste(): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM organizations WHERE id = ? AND archived_at IS NULL ORDER BY legal_name'
        );
        $anweisung->execute([$this->bereich->organisationId]);

        return $anweisung->fetchAll();
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
