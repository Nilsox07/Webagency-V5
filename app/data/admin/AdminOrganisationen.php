<?php

declare(strict_types=1);

namespace Sartu\Data\Admin;

use Sartu\Data\Db;
use Sartu\Data\Uuid;

/**
 * Adminseitiger Zugriff auf `organizations` — organisationsuebergreifend.
 *
 * Das ist die einzige Schicht, die das darf. Sie ist bewusst eine eigene Klasse in einem
 * eigenen Verzeichnis und teilt keinen Codepfad mit der Kundenschicht. Der Weg
 * „WHERE organization_id = ? OR ? IS TRUE" ist in §3 Regel 2a ausdruecklich verboten —
 * aus genau diesem Muster entsteht die typische Datenpanne.
 */
final class AdminOrganisationen
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
            'SELECT * FROM organizations WHERE archived_at IS NULL ORDER BY legal_name'
        )->fetchAll();
    }

    /** @return array<string,mixed>|null */
    public function finden(string $id): ?array
    {
        $anweisung = $this->pdo()->prepare('SELECT * FROM organizations WHERE id = ?');
        $anweisung->execute([$id]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    public function anlegen(string $rechtsname, string $kontaktEmail): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO organizations (id, legal_name, contact_email) VALUES (?, ?, ?)'
        );
        $anweisung->execute([$id, $rechtsname, $kontaktEmail]);

        return $id;
    }

    public function nachweis(): AdminNachweis
    {
        return $this->nachweis;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
