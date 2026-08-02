<?php

declare(strict_types=1);

namespace Sartu\Data\Admin;

use Sartu\Data\Db;
use Sartu\Data\Uuid;

/**
 * Adminseitiger Zugriff auf `feedback_rounds`, `feedback_items` und `domain_status`.
 *
 * Eigene Klasse neben `Customer\KundenVorschau` (§3 Regel 2). Sie verlangt einen Nachweis.
 */
final class AdminVorschau
{
    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return list<array<string,mixed>> */
    public function runden(string $projektId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM feedback_rounds WHERE project_id = ? ORDER BY number DESC'
        );
        $anweisung->execute([$projektId]);

        return $anweisung->fetchAll();
    }

    public function naechsteNummer(string $projektId): int
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT COALESCE(MAX(number), 0) + 1 FROM feedback_rounds WHERE project_id = ?'
        );
        $anweisung->execute([$projektId]);

        return (int) $anweisung->fetchColumn();
    }

    /** §5.6a: `included = false`, sobald die enthaltenen Runden verbraucht sind. */
    public function rundeOeffnen(string $projektId, int $nummer, bool $enthalten): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO feedback_rounds (id, project_id, number, status, opened_at, included)'
            . " VALUES (?, ?, ?, 'offen', ?, ?)"
        );
        $anweisung->execute([$id, $projektId, $nummer, Db::jetzt(), $enthalten ? 1 : 0]);

        return $id;
    }

    /**
     * Schliesst eine Runde ab.
     *
     * `project_id` steht bewusst in der Bedingung: Die Rundenkennung kommt aus einem
     * Formularfeld. Ohne diese Spalte liesse sich damit die Runde eines **anderen** Projekts
     * schliessen — auch im Adminbereich ist ein Schluessel aus dem Request kein Nachweis.
     */
    public function rundeAbschliessen(string $rundeId, string $projektId): bool
    {
        $anweisung = $this->pdo()->prepare(
            "UPDATE feedback_rounds SET status = 'bearbeitet', completed_at = ?"
            . " WHERE id = ? AND project_id = ? AND status = 'eingereicht'"
        );
        $anweisung->execute([Db::jetzt(), $rundeId, $projektId]);

        return $anweisung->rowCount() === 1;
    }

    /** @return list<array<string,mixed>> die Rückmeldungen einer Runde — §9.2 „Feedback". */
    public function rueckmeldungen(string $rundeId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM feedback_items WHERE feedback_round_id = ? ORDER BY created_at ASC'
        );
        $anweisung->execute([$rundeId]);

        return $anweisung->fetchAll();
    }

    /** @return list<array<string,mixed>> die Erklärungen eines Projekts — §9.2, nur lesbar. */
    public function freigaben(string $projektId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM approvals WHERE project_id = ? ORDER BY granted_at ASC'
        );
        $anweisung->execute([$projektId]);

        return $anweisung->fetchAll();
    }

    public function vorschauSetzen(string $projektId, string $adresse): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE projects SET preview_url = ?, preview_published_at = ? WHERE id = ?'
        );
        $anweisung->execute([$adresse, Db::jetzt(), $projektId]);
    }

    /** §5.7: Beim Wechsel auf `live` setzt der Admin den Betriebsbeginn. */
    public function livegangEintragen(string $projektId, string $adresse, string $betriebSeit): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE projects SET live_url = ?, launched_at = ?, protection_started_on = ?,'
            . ' protection_min_term_until = ? WHERE id = ?'
        );
        $anweisung->execute([
            $adresse, Db::jetzt(), $betriebSeit, self::mindestlaufzeitEnde($betriebSeit), $projektId,
        ]);
    }

    /**
     * §5.7 Sonderfall — den Betriebsbeginn nachträglich verschieben.
     *
     * Die Mindestlaufzeit wird **mitgerechnet**, nie mitgetippt: Zwei Felder, die dasselbe
     * behaupten, laufen auseinander, und dann gilt keins von beiden.
     */
    public function betriebsbeginnSetzen(string $projektId, string $betriebSeit): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE projects SET protection_started_on = ?, protection_min_term_until = ? WHERE id = ?'
        );
        $anweisung->execute([$betriebSeit, self::mindestlaufzeitEnde($betriebSeit), $projektId]);
    }

    /** §5.7: `protection_started_on + 12 Monate`. */
    public static function mindestlaufzeitEnde(string $betriebSeit): string
    {
        return (new \DateTimeImmutable($betriebSeit))->modify('+12 months')->format('Y-m-d');
    }

    // ------------------------------------------------------------ Domainlage (§8.7)

    /** @return array<string,mixed>|null */
    public function domainstand(string $projektId): ?array
    {
        $anweisung = $this->pdo()->prepare('SELECT * FROM domain_status WHERE project_id = ?');
        $anweisung->execute([$projektId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** Legt an oder aktualisiert — `project_id` ist eindeutig. */
    public function domainstandSetzen(string $projektId, array $werte): void
    {
        if ($this->domainstand($projektId) === null) {
            $anweisung = $this->pdo()->prepare(
                'INSERT INTO domain_status (id, project_id) VALUES (?, ?)'
            );
            $anweisung->execute([Uuid::v4(), $projektId]);
        }

        $anweisung = $this->pdo()->prepare(
            'UPDATE domain_status SET desired_name = ?, confirmed_name = ?, owner_confirmed = ?,'
            . ' state = ?, email_note = ?, admin_note = ? WHERE project_id = ?'
        );
        $anweisung->execute([
            $werte['desired_name'] ?? null,
            $werte['confirmed_name'] ?? null,
            ($werte['owner_confirmed'] ?? false) ? 1 : 0,
            $werte['state'] ?? 'offen',
            $werte['email_note'] ?? null,
            $werte['admin_note'] ?? null,
            $projektId,
        ]);
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
