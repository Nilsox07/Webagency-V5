<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Portal-Lastenheft §3 Regel 9 und §4 `audit_events`.
 *
 * Nur INSERT. Es gibt hier bewusst kein UPDATE und kein DELETE — und weil eine Absicht im
 * Code kein Beleg ist, sagt zusaetzlich die Datenbank nein (Migrationen 005 und 006,
 * Testfall 55).
 *
 * REIHENFOLGE.md: „Eine schoene Ansicht darf warten. Die Eintraege nicht — was nicht
 * protokolliert wurde, ist rueckwirkend nicht rekonstruierbar."
 */
final class AuditProtokoll
{
    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    /** @param array<string,mixed>|null $detail */
    public function schreiben(
        string $aktion,
        string $objektart,
        ?string $objektId = null,
        ?string $akteurBenutzerId = null,
        ?string $organisationId = null,
        ?string $alterWert = null,
        ?string $neuerWert = null,
        ?string $grund = null,
        ?array $detail = null,
        ?string $ip = null,
    ): string {
        $id = Uuid::v4();

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO audit_events'
            . ' (id, actor_user_id, organization_id, action, entity_type, entity_id,'
            . '  old_value, new_value, reason, detail, ip)'
            . ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $anweisung->execute([
            $id,
            $akteurBenutzerId,
            $organisationId,
            $aktion,
            $objektart,
            $objektId,
            $alterWert,
            $neuerWert,
            $grund,
            $detail === null ? null : json_encode($detail, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            $ip,
        ]);

        return $id;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
