<?php

declare(strict_types=1);

namespace Sartu\Data\Admin;

use Sartu\Data\Db;
use Sartu\Data\Uuid;

/**
 * Adminseitiger Zugriff auf `offers`.
 *
 * Die drei Pruefregeln aus §4 — Erstjahreswert, Zahlungsplan, Annehmbarkeit — stehen im
 * `AngebotDienst`, nicht hier. Diese Klasse schreibt und liest; sie entscheidet nichts.
 * Die Datenbank haelt dieselben Regeln zusaetzlich als Bedingung fest, fuer den Fall, dass
 * irgendwann ein zweiter Schreibweg entsteht.
 */
final class AdminAngebote
{
    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return list<array<string,mixed>> */
    public function jeProjekt(string $projektId): array
    {
        $anweisung = $this->pdo()->prepare('SELECT * FROM offers WHERE project_id = ? ORDER BY created_at DESC');
        $anweisung->execute([$projektId]);

        return $anweisung->fetchAll();
    }

    /** @return array<string,mixed>|null */
    public function finden(string $id): ?array
    {
        $anweisung = $this->pdo()->prepare('SELECT * FROM offers WHERE id = ?');
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
            'INSERT INTO offers (id, %s) VALUES (?, %s)',
            implode(', ', $spalten),
            implode(', ', array_fill(0, count($spalten), '?')),
        ));
        $anweisung->execute([$id, ...array_values($werte)]);

        return $id;
    }

    public function alsGesendetVermerken(string $id): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE offers SET status = ?, sent_at = ? WHERE id = ? AND status = ?'
        );
        $anweisung->execute(['gesendet', Db::jetzt(), $id, 'entwurf']);
    }

    /** §5.2: Ein abgelaufenes Angebot ist nicht annehmbar. Der taegliche Lauf setzt das. */
    public function abgelaufeneSetzen(): int
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE offers SET status = ? WHERE status = ? AND valid_until < CURDATE()'
        );
        $anweisung->execute(['abgelaufen', 'gesendet']);

        return $anweisung->rowCount();
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
