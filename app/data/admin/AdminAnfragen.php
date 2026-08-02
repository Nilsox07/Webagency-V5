<?php

declare(strict_types=1);

namespace Sartu\Data\Admin;

use Sartu\Data\Db;

/**
 * Adminseitiger Zugriff auf `leads` — Portal-Lastenheft §4b.5.
 *
 * Anfragen gehoeren keiner Organisation: Sie entstehen, bevor es einen Kunden gibt. Die
 * Mandantentrennung beruehrt sie damit nicht — wohl aber die Adminpruefung. Ohne Nachweis
 * gibt es diese Klasse nicht.
 *
 * `endgueltigLoeschen()` ist die eine ausdrueckliche Ausnahme von §3 Regel 13 (keine harte
 * Loeschung). §4b.4 verlangt sie als Betroffenenrecht — und der Loeschvorgang wird
 * protokolliert, **ohne** die geloeschten Inhalte.
 */
final class AdminAnfragen
{
    public const ZUSTAENDE = ['neu', 'in_pruefung', 'angebot_erstellt', 'abgelehnt'];

    public const ZUSTANDS_BESCHRIFTUNGEN = [
        'neu'              => 'Neu',
        'in_pruefung'      => 'In Prüfung',
        'angebot_erstellt' => 'Angebot erstellt',
        'abgelehnt'        => 'Abgelehnt',
    ];

    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return list<array<string,mixed>> */
    public function alle(?string $zustand = null): array
    {
        if ($zustand === null) {
            return $this->pdo()->query('SELECT * FROM leads ORDER BY submitted_at DESC')->fetchAll();
        }

        $anweisung = $this->pdo()->prepare('SELECT * FROM leads WHERE status = ? ORDER BY submitted_at DESC');
        $anweisung->execute([$zustand]);

        return $anweisung->fetchAll();
    }

    /** @return array<string,mixed>|null */
    public function finden(string $id): ?array
    {
        $anweisung = $this->pdo()->prepare('SELECT * FROM leads WHERE id = ?');
        $anweisung->execute([$id]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    public function zustandSetzen(string $id, string $zustand): void
    {
        if (!in_array($zustand, self::ZUSTAENDE, true)) {
            throw new \InvalidArgumentException('Unbekannter Zustand fuer eine Anfrage.');
        }

        $anweisung = $this->pdo()->prepare('UPDATE leads SET status = ? WHERE id = ?');
        $anweisung->execute([$zustand, $id]);
    }

    public function notizSetzen(string $id, string $notiz): void
    {
        $anweisung = $this->pdo()->prepare('UPDATE leads SET admin_note = ? WHERE id = ?');
        $anweisung->execute([$notiz, $id]);
    }

    /** Umwandlung vermerken: die Loeschfrist entfaellt, die Anfrage wird Teil der Kundenakte. */
    public function alsUmgewandeltVermerken(string $id, string $organisationId): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE leads SET converted_organization_id = ?, status = ? WHERE id = ?'
        );
        $anweisung->execute([$organisationId, 'angebot_erstellt', $id]);
    }

    /** §4b.4 Betroffenenrecht: echtes DELETE, ausdrueckliche Ausnahme von §3 Regel 13. */
    public function endgueltigLoeschen(string $id): void
    {
        $anweisung = $this->pdo()->prepare('DELETE FROM leads WHERE id = ?');
        $anweisung->execute([$id]);
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
