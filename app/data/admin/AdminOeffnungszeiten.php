<?php

declare(strict_types=1);

namespace Sartu\Data\Admin;

use Sartu\Data\Db;

/**
 * Adminseitiger Zugriff auf `business_hours` — Portal-Lastenheft §9.2.
 *
 * Eigene Klasse neben `Customer\KundenOeffnungszeiten` (§3 Regel 2). Sie verlangt einen
 * Nachweis, und die Organisation kommt als Pflichtparameter vom Aufrufer, der sie ueber die
 * Adminschicht gelesen hat. Es gibt hier keinen Aufruf ohne Organisation und damit keine
 * Fassung, die „alle" liefert.
 */
final class AdminOeffnungszeiten
{
    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return list<array<string,mixed>> */
    public function wochentage(string $organisationId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM business_hours WHERE organization_id = ? ORDER BY weekday ASC'
        );
        $anweisung->execute([$organisationId]);

        return $anweisung->fetchAll();
    }

    /** @return list<array<string,mixed>> */
    public function ausnahmen(string $organisationId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM business_hours_exceptions WHERE organization_id = ? ORDER BY date ASC'
        );
        $anweisung->execute([$organisationId]);

        return $anweisung->fetchAll();
    }

    /**
     * §9.2 `Als veroeffentlicht markieren` — setzt `pending_publish = false`.
     *
     * @return bool ob ueberhaupt etwas wartete. `false` heisst: nichts zu veroeffentlichen,
     *              also geht auch keine Mail raus, die etwas anderes behauptet.
     */
    public function alsVeroeffentlichtMarkieren(string $organisationId): bool
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE business_hours SET pending_publish = 0'
            . ' WHERE organization_id = ? AND pending_publish = 1'
        );
        $anweisung->execute([$organisationId]);

        return $anweisung->rowCount() > 0;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
