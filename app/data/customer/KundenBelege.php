<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\Db;

/**
 * Kundenseitiger Zugriff auf `documents` — `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 9.
 *
 * **`documents` hat keine `organization_id`.** Ein Beleg gehoert zu einer Rechnung, die zu
 * einem Projekt gehoert, das zu einer Organisation gehoert. Der Filter laeuft deshalb ueber
 * den Verbund — anders als bei `task_files`, wo §4 die redundante Spalte ausdruecklich
 * vorsieht.
 *
 * Die Pruefung ist trotzdem **doppelt** im Sinne von §9: Gibt es den Beleg, **und** gehoert
 * er zur Sitzungsorganisation? Beides steht in **einer** Abfrage — nicht als zwei Schritte,
 * zwischen denen jemand eine Verzweigung einbaut. Findet sie nichts, ist die Antwort
 * **404**, nicht 403: 403 verriete, dass es den Beleg gibt.
 *
 * `organizations` kommt aus `KundenBereich` und damit ausschliesslich aus der Sitzung
 * (§3 Regel 1). Es gibt keinen Weg, hier eine andere Organisation zu setzen.
 */
final class KundenBelege
{
    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * Alle Belege der eigenen Organisation — Rechnungen und Stornorechnungen.
     *
     * @return list<array<string,mixed>>
     */
    public function liste(): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT d.*, i.status, i.gross_cents, i.issued_at, i.due_date, i.milestone'
            . ' FROM documents d'
            . ' JOIN invoices i ON i.id = d.invoice_id'
            . ' JOIN projects p ON p.id = i.project_id'
            . ' WHERE p.organization_id = ? AND i.archived_at IS NULL'
            . ' ORDER BY d.created_at DESC'
        );
        $anweisung->execute([$this->bereich->organisationId]);

        return $anweisung->fetchAll();
    }

    /** @return list<array<string,mixed>> */
    public function zuRechnung(string $rechnungId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT d.* FROM documents d'
            . ' JOIN invoices i ON i.id = d.invoice_id'
            . ' JOIN projects p ON p.id = i.project_id'
            . ' WHERE d.invoice_id = ? AND p.organization_id = ?'
            . ' ORDER BY d.created_at ASC'
        );
        $anweisung->execute([$rechnungId, $this->bereich->organisationId]);

        return $anweisung->fetchAll();
    }

    /**
     * Ein einzelner Beleg — die Abfrage hinter dem Abruf.
     *
     * @return array<string,mixed>|null `null` heisst 404, in beiden Faellen
     */
    public function finden(string $belegId): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT d.* FROM documents d'
            . ' JOIN invoices i ON i.id = d.invoice_id'
            . ' JOIN projects p ON p.id = i.project_id'
            . ' WHERE d.id = ? AND p.organization_id = ?'
        );
        $anweisung->execute([$belegId, $this->bereich->organisationId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
