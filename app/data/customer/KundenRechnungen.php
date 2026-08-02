<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\Db;

/**
 * Kundenseitiger Zugriff auf `invoices` — Portal-Lastenheft §4, §5.3, §8.5.
 *
 * Rechnungen haengen an `projects`, nicht direkt an der Organisation. Der Filter geht
 * deshalb ueber einen Verbund — und zwar in JEDER Abfrage, auch in `finden()`. Ein
 * `SELECT * FROM invoices WHERE id = ?` waere genau der Aufruf, den Testfall 2 sucht.
 *
 * §5.3: `entwurf` ist fuer den Kunden unsichtbar. Was er nicht sehen darf, wird nicht
 * ausgeblendet, sondern nicht geladen.
 */
final class KundenRechnungen
{
    /** §5.3: sichtbare Zustaende. `entwurf` fehlt hier absichtlich. */
    public const SICHTBAR = ['gesendet', 'teilweise_bezahlt', 'bezahlt', 'ueberfaellig', 'storniert'];

    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return list<array<string,mixed>> */
    public function liste(): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT i.* FROM invoices i'
            . ' JOIN projects p ON p.id = i.project_id'
            . ' WHERE p.organization_id = ? AND i.archived_at IS NULL'
            . ' AND i.status IN (' . self::platzhalter() . ')'
            . ' ORDER BY i.created_at DESC'
        );
        $anweisung->execute([$this->bereich->organisationId, ...self::SICHTBAR]);

        return $anweisung->fetchAll();
    }

    /** @return array<string,mixed>|null */
    public function finden(string $rechnungId): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT i.* FROM invoices i'
            . ' JOIN projects p ON p.id = i.project_id'
            . ' WHERE i.id = ? AND p.organization_id = ? AND i.archived_at IS NULL'
            . ' AND i.status IN (' . self::platzhalter() . ')'
        );
        $anweisung->execute([$rechnungId, $this->bereich->organisationId, ...self::SICHTBAR]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** Fuer Block 3 des Cockpits (§8.1): die aelteste noch nicht beglichene Rechnung. */
    public function aeltesteOffene(): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT i.* FROM invoices i'
            . ' JOIN projects p ON p.id = i.project_id'
            . ' WHERE p.organization_id = ? AND i.archived_at IS NULL'
            . " AND i.status IN ('gesendet','teilweise_bezahlt','ueberfaellig')"
            . ' AND i.paid_cents < i.gross_cents'
            . ' ORDER BY i.due_date IS NULL, i.due_date ASC LIMIT 1'
        );
        $anweisung->execute([$this->bereich->organisationId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    private static function platzhalter(): string
    {
        return implode(',', array_fill(0, count(self::SICHTBAR), '?'));
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
