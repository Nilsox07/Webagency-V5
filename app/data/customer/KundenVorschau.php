<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\Db;
use Sartu\Data\Uuid;

/**
 * Kundenseitiger Zugriff auf `feedback_rounds` und `feedback_items` — §4, §5.6a, §8.4.
 *
 * Der Filter geht ueber `projects`, in jeder einzelnen Abfrage.
 *
 * **Das Einreichen ist ein bedingtes UPDATE.** §5.6a: „Danach sind in dieser Runde KEINE
 * weiteren Eintraege moeglich." Eine vorgelagerte Abfrage „ist die Runde noch offen?"
 * liesse zwei gleichzeitige Klicks beide durch.
 */
final class KundenVorschau
{
    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return array<string,mixed>|null die laufende Runde, falls es eine gibt */
    public function aktuelleRunde(string $projektId): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT r.* FROM feedback_rounds r'
            . ' JOIN projects p ON p.id = r.project_id'
            . ' WHERE r.project_id = ? AND p.organization_id = ?'
            . ' ORDER BY r.number DESC LIMIT 1'
        );
        $anweisung->execute([$projektId, $this->bereich->organisationId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** @return list<array<string,mixed>> */
    public function runden(string $projektId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT r.* FROM feedback_rounds r'
            . ' JOIN projects p ON p.id = r.project_id'
            . ' WHERE r.project_id = ? AND p.organization_id = ? ORDER BY r.number DESC'
        );
        $anweisung->execute([$projektId, $this->bereich->organisationId]);

        return $anweisung->fetchAll();
    }

    /** @return list<array<string,mixed>> */
    public function rueckmeldungen(string $rundeId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT i.* FROM feedback_items i'
            . ' JOIN projects p ON p.id = i.project_id'
            . ' WHERE i.feedback_round_id = ? AND p.organization_id = ? ORDER BY i.created_at ASC'
        );
        $anweisung->execute([$rundeId, $this->bereich->organisationId]);

        return $anweisung->fetchAll();
    }

    public function anzahlRueckmeldungen(string $rundeId): int
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT COUNT(*) FROM feedback_items i'
            . ' JOIN projects p ON p.id = i.project_id'
            . ' WHERE i.feedback_round_id = ? AND p.organization_id = ?'
        );
        $anweisung->execute([$rundeId, $this->bereich->organisationId]);

        return (int) $anweisung->fetchColumn();
    }

    /**
     * Legt eine Rueckmeldung an — nur in einer OFFENEN Runde der eigenen Organisation.
     *
     * Die Bedingung steht in der `INSERT ... SELECT`-Anweisung selbst, nicht in einem `if`
     * davor. Damit gibt es kein Zeitfenster zwischen Pruefung und Schreiben.
     *
     * @return string|null die Kennung, oder `null`, wenn die Runde nicht (mehr) offen ist
     */
    public function rueckmeldungAnlegen(
        string $rundeId,
        string $projektId,
        string $text,
        ?string $seite,
        string $benutzerId,
    ): ?string {
        $id = Uuid::v4();

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO feedback_items (id, project_id, feedback_round_id, body, page_hint, created_by_user_id)'
            . ' SELECT ?, ?, r.id, ?, ?, ? FROM feedback_rounds r'
            . ' JOIN projects p ON p.id = r.project_id'
            . " WHERE r.id = ? AND r.project_id = ? AND p.organization_id = ? AND r.status = 'offen'"
        );
        $anweisung->execute([
            $id, $projektId, $text, $seite, $benutzerId,
            $rundeId, $projektId, $this->bereich->organisationId,
        ]);

        return $anweisung->rowCount() === 1 ? $id : null;
    }

    /** @return bool ob genau eine Runde eingereicht wurde */
    public function einreichen(string $rundeId): bool
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE feedback_rounds r JOIN projects p ON p.id = r.project_id'
            . " SET r.status = 'eingereicht', r.submitted_at = ?"
            . " WHERE r.id = ? AND p.organization_id = ? AND r.status = 'offen'"
        );
        $anweisung->execute([Db::jetzt(), $rundeId, $this->bereich->organisationId]);

        return $anweisung->rowCount() === 1;
    }

    /** @return array<string,mixed>|null */
    public function domainstand(string $projektId): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT d.* FROM domain_status d'
            . ' JOIN projects p ON p.id = d.project_id'
            . ' WHERE d.project_id = ? AND p.organization_id = ?'
        );
        $anweisung->execute([$projektId, $this->bereich->organisationId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
