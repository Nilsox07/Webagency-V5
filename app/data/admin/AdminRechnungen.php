<?php

declare(strict_types=1);

namespace Sartu\Data\Admin;

use Sartu\Data\Db;
use Sartu\Data\Uuid;

/**
 * Adminseitiger Zugriff auf `invoices` — Portal-Lastenheft §4, §5.3, §12.
 *
 * Eine EIGENE Klasse neben `Customer\KundenRechnungen`, kein gemeinsamer Codepfad mit
 * abschaltbarem Filter (§3 Regel 2). Sie verlangt einen `AdminNachweis` im Konstruktor —
 * ohne ihn gibt es sie nicht.
 *
 * Hier steht nur das Schreiben. **Welcher Zustand aus welchem `paid_cents` folgt, rechnet
 * `Services\Zahlungsstatus`** (§1.3: Fachlogik nicht in der Datenschicht). Diese Trennung
 * ist hier besonders wichtig: Die Regel aus §4 hat vier Faelle, und einer davon —
 * teilweise bezahlt UND ueberfaellig zugleich — geht beim Abkuerzen als Erstes verloren.
 */
final class AdminRechnungen
{
    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return list<array<string,mixed>> */
    public function alle(?string $zustand = null): array
    {
        if ($zustand === null) {
            return $this->pdo()->query(
                'SELECT * FROM invoices WHERE archived_at IS NULL ORDER BY created_at DESC'
            )->fetchAll();
        }

        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM invoices WHERE status = ? AND archived_at IS NULL ORDER BY created_at DESC'
        );
        $anweisung->execute([$zustand]);

        return $anweisung->fetchAll();
    }

    /** @return list<array<string,mixed>> */
    public function jeProjekt(string $projektId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM invoices WHERE project_id = ? AND archived_at IS NULL ORDER BY created_at ASC'
        );
        $anweisung->execute([$projektId]);

        return $anweisung->fetchAll();
    }

    /** @return array<string,mixed>|null */
    public function finden(string $id): ?array
    {
        $anweisung = $this->pdo()->prepare('SELECT * FROM invoices WHERE id = ?');
        $anweisung->execute([$id]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** @param array<string,scalar|null> $werte */
    public function anlegen(array $werte): string
    {
        $id = Uuid::v4();
        $spalten = array_keys($werte);

        $anweisung = $this->pdo()->prepare(sprintf(
            'INSERT INTO invoices (id, %s) VALUES (?, %s)',
            implode(', ', $spalten),
            implode(', ', array_fill(0, count($spalten), '?')),
        ));
        $anweisung->execute([$id, ...array_values($werte)]);

        return $id;
    }

    public function zustandSetzen(string $id, string $zustand): void
    {
        $anweisung = $this->pdo()->prepare('UPDATE invoices SET status = ? WHERE id = ?');
        $anweisung->execute([$zustand, $id]);
    }

    /**
     * Setzt Zahlbetrag, Zustand und Zahlzeitpunkt in EINER Anweisung.
     *
     * Getrennt geschrieben gaebe es einen Moment, in dem `paid_cents` schon steht und
     * `status` noch nicht — und der taegliche Lauf koennte genau dann dazwischenlaufen.
     */
    public function zahlungSetzen(string $id, int $bezahltCent, string $zustand, ?string $bezahltAm, ?string $adminId): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE invoices SET paid_cents = ?, status = ?, paid_at = ?, marked_paid_by_user_id = ? WHERE id = ?'
        );
        $anweisung->execute([$bezahltCent, $zustand, $bezahltAm, $adminId, $id]);
    }

    public function zahlungslinkSetzen(string $id, ?string $adresse): void
    {
        $anweisung = $this->pdo()->prepare('UPDATE invoices SET mollie_payment_url = ? WHERE id = ?');
        $anweisung->execute([$adresse, $id]);
    }

    /**
     * Setzt Zustand und Ausstellungsdatum in **einer** Anweisung — beim Versand.
     *
     * `issued_at` ist die Pflichtangabe nach § 14 Abs. 4 UStG und entsteht mit dem Versand,
     * nicht mit dem Entwurf. Getrennt geschrieben gaebe es einen Moment, in dem die Rechnung
     * `gesendet` heisst und kein Ausstellungsdatum hat — und genau in dem Moment koennte der
     * Beleg erzeugt werden.
     */
    public function ausstellen(string $id, string $zustand, string $ausgestelltAm): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE invoices SET status = ?, issued_at = ? WHERE id = ?'
        );
        $anweisung->execute([$zustand, $ausgestelltAm, $id]);
    }

    /**
     * Legt eine Stornorechnung an — §5.
     *
     * Sie ist eine Zeile in derselben Tabelle: eigene Nummer, eigenes Ausstellungsdatum,
     * negative Betraege, Verweis auf die aufgehobene Rechnung.
     *
     * @param array<string,mixed> $werte
     */
    public function stornoAnlegen(array $werte): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO invoices (id, project_id, number, milestone, status, issued_at,'
            . ' cancels_invoice_id, net_cents, vat_cents, gross_cents, due_date, note)'
            . ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            $id,
            $werte['project_id'],
            $werte['number'],
            $werte['milestone'],
            $werte['status'],
            $werte['issued_at'],
            $werte['cancels_invoice_id'],
            $werte['net_cents'],
            $werte['vat_cents'],
            $werte['gross_cents'],
            $werte['due_date'],
            $werte['note'],
        ]);

        return $id;
    }

    /**
     * Aendert das Faelligkeitsdatum — Fall 53a.
     *
     * Eigene Anweisung statt eines Anhaengsels an `zahlungSetzen`: Die Frist zu verschieben
     * ist kein Zahlungsvorgang, und ein gemeinsamer Schreibweg haette irgendwann beides
     * zugleich geaendert, ohne dass ein Protokolleintrag beides nennt.
     */
    public function faelligkeitSetzen(string $id, string $datum): void
    {
        $anweisung = $this->pdo()->prepare('UPDATE invoices SET due_date = ? WHERE id = ?');
        $anweisung->execute([$datum, $id]);
    }

    /**
     * Der taegliche Lauf steht NICHT hier, sondern in `Data\Faelligkeiten`.
     *
     * Er ist eine Systemaufgabe ohne Akteur und greift bewusst ueber alle Organisationen.
     * Ihn hier zu fuehren hiesse, ihm einen Adminnachweis zu geben, den niemand erbracht hat.
     */

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
