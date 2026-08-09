<?php

declare(strict_types=1);

namespace Sartu\Data\Admin;

use Sartu\Data\Db;

/**
 * Adminseitiger Zugriff auf `documents` — `18_BELEGE_UND_ZAHLUNG.md` Abschnitte 8 und 9.
 *
 * Eine EIGENE Klasse neben `Customer\KundenBelege`. Nicht dieselbe mit abschaltbarem Filter
 * (§3 Regel 2) — der Unterschied ist hier besonders greifbar: Die Kundenabfrage **muss**
 * ueber den Verbund filtern, die Adminabfrage **darf es nicht**, weil der Steuerexport den
 * Ausgang des Betriebs abbildet und nicht den eines Kunden. Ein gemeinsamer Codepfad haette
 * genau ein `if` dazwischen.
 *
 * Sie verlangt einen `AdminNachweis` im Konstruktor — ohne ihn gibt es sie nicht.
 *
 * **Kein Schreiben.** Belege entstehen in `Services\Belegerzeugung` und nirgends sonst; §7
 * verbietet das Ueberschreiben. Diese Klasse liest.
 */
final class AdminBelege
{
    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return array<string,mixed>|null */
    public function finden(string $id): ?array
    {
        $anweisung = $this->pdo()->prepare('SELECT * FROM documents WHERE id = ?');
        $anweisung->execute([$id]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** @return list<array<string,mixed>> */
    public function zuRechnung(string $rechnungId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM documents WHERE invoice_id = ? ORDER BY created_at ASC'
        );
        $anweisung->execute([$rechnungId]);

        return $anweisung->fetchAll();
    }

    /**
     * Die Zeilen des Steuerexports — Abschnitt 8.
     *
     * ## Warum die Rechnung fuehrt und nicht das Dokument
     *
     * Der Export enthaelt „jede Rechnung, jede Stornorechnung und jeden Zahlungseingang des
     * Zeitraums **genau einmal**" (Fall 95). Wer ueber `documents` liefe, bekaeme eine
     * Rechnung zweimal, sobald ihr Beleg ein zweites Mal erzeugt wurde — §7 laesst das
     * ausdruecklich zu („eine zweite Erzeugung ist ein zweites Dokument").
     *
     * Deshalb: eine Zeile je Rechnung. Der **zuletzt** erzeugte Beleg wird als Verweis
     * mitgegeben; die aelteren bleiben in der Ablage und sind ueber `zuRechnung()` erreichbar.
     *
     * ## Warum ueber `issued_at` und nicht ueber `created_at`
     *
     * Das Ausstellungsdatum ist der steuerlich massgebliche Zeitpunkt. Ein Entwurf, der im
     * Dezember angelegt und im Januar versendet wurde, gehoert in den Januar.
     *
     * Entwuerfe und verworfene Entwuerfe haben kein `issued_at` und fallen damit heraus —
     * sie sind nie hinausgegangen.
     *
     * @return list<array<string,mixed>>
     */
    public function export(string $von, string $bis): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT i.id, i.number, i.status, i.milestone, i.issued_at, i.due_date,'
            . ' i.net_cents, i.vat_cents, i.gross_cents, i.paid_cents, i.paid_at,'
            . ' i.cancels_invoice_id, o.id AS organization_id, o.legal_name,'
            . ' (SELECT d.path FROM documents d WHERE d.invoice_id = i.id'
            . '   ORDER BY d.created_at DESC LIMIT 1) AS document_path,'
            . ' (SELECT d.checksum FROM documents d WHERE d.invoice_id = i.id'
            . '   ORDER BY d.created_at DESC LIMIT 1) AS document_checksum'
            . ' FROM invoices i'
            . ' JOIN projects p ON p.id = i.project_id'
            . ' JOIN organizations o ON o.id = p.organization_id'
            . ' WHERE i.issued_at IS NOT NULL AND i.issued_at >= ? AND i.issued_at < ?'
            . ' ORDER BY i.issued_at ASC, i.number ASC'
        );
        $anweisung->execute([$von . ' 00:00:00', $bis . ' 23:59:59']);

        return $anweisung->fetchAll();
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
