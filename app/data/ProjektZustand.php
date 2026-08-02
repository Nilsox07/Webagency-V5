<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Der Schreibweg fuer `projects.status` — Portal-Lastenheft §5.1a.
 *
 * **Warum diese Klasse ausserhalb beider Zugriffsschichten liegt:** Ein Zustandswechsel
 * wird von beiden Seiten ausgeloest. Drei der Wechsel gehoeren dem Kunden (§5.1a:
 * Angebotsannahme, Faktenfreigabe, Abnahme), alle uebrigen dem Admin. Ueber die Adminschicht
 * zu gehen hiesse, fuer eine Kundenhandlung einen Adminnachweis zu erfinden — und genau
 * das ist der gemeinsame Codepfad, den §3 Regel 2 verbietet.
 *
 * Stattdessen: eine absichtlich schmale Klasse, wie `AnmeldeKonten`. Sie kann drei Dinge und
 * sonst nichts.
 *
 * **Der Mandantenfilter steckt im Parameter, nicht in der Disziplin des Aufrufers.**
 * `$organisationId` ist auf **jedem** Schreibaufruf Pflicht und geht in die `WHERE`-Bedingung.
 * Ein Kundendienst uebergibt die Organisation aus der Sitzung; ein Admindienst uebergibt die
 * des Projekts, das er zuvor ueber seine eigene Schicht gelesen hat. Es gibt keine
 * Ueberladung ohne Filter.
 */
final class ProjektZustand
{
    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    /** @return array<string,mixed>|null */
    public function finden(string $projektId, string $organisationId): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM projects WHERE id = ? AND organization_id = ? AND archived_at IS NULL'
        );
        $anweisung->execute([$projektId, $organisationId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /**
     * Setzt den Zustand — mit Organisationsfilter und mit Bedingung auf den Ausgangswert.
     *
     * `AND status = ?` ist keine Zierde: Zwei gleichzeitige Klicks auf „Verbindlich
     * freigeben" wuerden sonst beide durchlaufen. Der zweite findet die Zeile nicht mehr
     * und bekommt `false`.
     *
     * @return bool ob genau eine Zeile geaendert wurde
     */
    public function setzen(
        string $projektId,
        string $organisationId,
        string $von,
        string $nach,
        ?string $herkunftsstatus,
    ): bool {
        $anweisung = $this->pdo()->prepare(
            'UPDATE projects SET status = ?, paused_from_status = ?'
            . ' WHERE id = ? AND organization_id = ? AND status = ? AND archived_at IS NULL'
        );
        $anweisung->execute([$nach, $herkunftsstatus, $projektId, $organisationId, $von]);

        return $anweisung->rowCount() === 1;
    }

    public function pauseGrundSetzen(string $projektId, string $organisationId, ?string $grund): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE projects SET pause_reason = ? WHERE id = ? AND organization_id = ?'
        );
        $anweisung->execute([$grund, $projektId, $organisationId]);
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
