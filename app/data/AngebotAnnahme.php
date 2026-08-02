<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Der Schreibweg fuer die Angebotsannahme — Portal-Lastenheft §8.2.
 *
 * Wie `ProjektZustand` bewusst schmal und ausserhalb beider Zugriffsschichten: Die Annahme
 * ist eine Kundenhandlung, die in `offers` und `projects` schreibt. Beide Tabellen haengen
 * ueber `project_id` an der Organisation, und der Filter steht in JEDER Anweisung.
 *
 * `AND status = 'gesendet'` ist die eigentliche Sperre gegen den Doppelklick (Testfall 12).
 * Eine vorgelagerte Abfrage „ist es schon angenommen?" liesse zwei gleichzeitige Klicks
 * beide durch — zwischen Lesen und Schreiben liegt Zeit.
 */
final class AngebotAnnahme
{
    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    /** @return bool ob genau eine Zeile geaendert wurde */
    public function annehmen(
        string $angebotId,
        string $organisationId,
        string $benutzerId,
        string $name,
        ?string $ip,
    ): bool {
        $anweisung = $this->pdo()->prepare(
            'UPDATE offers o JOIN projects p ON p.id = o.project_id'
            . " SET o.status = 'angenommen', o.accepted_at = ?, o.accepted_by_user_id = ?,"
            . ' o.accepted_ip = ?, o.accepted_name = ?'
            . " WHERE o.id = ? AND p.organization_id = ? AND o.status = 'gesendet'"
        );
        $anweisung->execute([Db::jetzt(), $benutzerId, $ip, $name, $angebotId, $organisationId]);

        return $anweisung->rowCount() === 1;
    }

    /**
     * §8.2: „Zugleich werden ins Projekt uebernommen: `included_feedback_rounds`,
     * `protection_level` und `package`."
     *
     * @param array{package:string,protection_level:string,included_feedback_rounds:int} $werte
     */
    public function angebotswerteUebernehmen(string $projektId, string $organisationId, array $werte): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE projects SET package = ?, protection_level = ?, included_feedback_rounds = ?'
            . ' WHERE id = ? AND organization_id = ?'
        );
        $anweisung->execute([
            $werte['package'],
            $werte['protection_level'],
            $werte['included_feedback_rounds'],
            $projektId,
            $organisationId,
        ]);
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
