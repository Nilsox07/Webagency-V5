<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\Db;
use Sartu\Data\Uuid;

/**
 * Kundenseitiger Zugriff auf `approvals` — Portal-Lastenheft §4.
 *
 * „Protokolliert ausschliesslich Erklaerungen des Kunden, die spaeter beweisbar sein
 * muessen." Deshalb gibt es hier kein Loeschen und kein Aendern: Eine Erklaerung, die sich
 * zuruecknehmen laesst, ist kein Beweis.
 */
final class KundenFreigaben
{
    public const INHALTE = 'inhalte';
    public const ABNAHME = 'abnahme';

    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return array<string,mixed>|null */
    public function finden(string $projektId, string $art): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT a.* FROM approvals a'
            . ' JOIN projects p ON p.id = a.project_id'
            . ' WHERE a.project_id = ? AND a.kind = ? AND p.organization_id = ?'
        );
        $anweisung->execute([$projektId, $art, $this->bereich->organisationId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /**
     * Legt die Erklaerung an — genau einmal.
     *
     * Der eindeutige Schluessel auf (project_id, kind) faengt den zweiten Versuch ab, auch
     * bei zwei gleichzeitigen Klicks. Hier wird die Ausnahme abgefangen und als „gibt es
     * schon" gemeldet, nicht als Fehler.
     *
     * @return bool `false`, wenn die Erklaerung bereits vorlag.
     */
    public function erklaeren(
        string $projektId,
        string $art,
        string $benutzerId,
        string $name,
        ?string $ip,
    ): bool {
        $anweisung = $this->pdo()->prepare(
            'INSERT INTO approvals (id, project_id, kind, granted_at, granted_by_user_id, granted_ip, granted_name)'
            . ' SELECT ?, ?, ?, ?, ?, ?, ? FROM projects WHERE id = ? AND organization_id = ?'
        );

        try {
            $anweisung->execute([
                Uuid::v4(), $projektId, $art, Db::jetzt(), $benutzerId, $ip, $name,
                $projektId, $this->bereich->organisationId,
            ]);
        } catch (\PDOException $fehler) {
            // 23000 = Verletzung einer Eindeutigkeitsbedingung: die Erklaerung liegt vor.
            if ($fehler->getCode() === '23000') {
                return false;
            }

            throw $fehler;
        }

        return $anweisung->rowCount() === 1;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
