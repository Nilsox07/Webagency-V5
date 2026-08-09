<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Zugriff auf `documents` — `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 7.
 *
 * **Warum ausserhalb beider Zugriffsschichten.** Ein Beleg entsteht im Adminbereich, wird vom
 * Kunden abgerufen und laeuft im Steuerexport ueber alle Organisationen. Drei Wege, eine
 * Ablage. Die **Mandantenpruefung** steht deshalb nicht hier, sondern in
 * `Customer\KundenBelege` — die Klasse dort filtert ueber das Projekt, und nur ueber sie
 * kommt ein Kunde an einen Beleg.
 *
 * **Es gibt kein Aendern und kein Loeschen.** §7: „Ein abgelegter Beleg wird nie
 * ueberschrieben." Diese Klasse hat kein `UPDATE` und kein `DELETE` — nicht als Disziplin,
 * sondern weil es die Methoden nicht gibt.
 */
final class Belege
{
    public const ARTEN = ['angebot' => 'Angebot', 'rechnung' => 'Rechnung', 'storno' => 'Stornorechnung'];

    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    /**
     * Legt einen Beleg ab.
     *
     * @param array{kind:string,number:string,invoice_id:?string,offer_id:?string,path:string,checksum:string,format:string} $werte
     */
    public function anlegen(array $werte): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO documents (id, kind, number, invoice_id, offer_id, path, checksum, format)'
            . ' VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            $id,
            $werte['kind'],
            $werte['number'],
            $werte['invoice_id'],
            $werte['offer_id'],
            $werte['path'],
            $werte['checksum'],
            $werte['format'],
        ]);

        return $id;
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

    /** @return list<array<string,mixed>> */
    public function zuAngebot(string $angebotId): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM documents WHERE offer_id = ? ORDER BY created_at ASC'
        );
        $anweisung->execute([$angebotId]);

        return $anweisung->fetchAll();
    }

    /**
     * Alle Belege eines Zeitraums — fuer den Steuerexport (§8).
     *
     * Ueber alle Organisationen, absichtlich: Der Steuerberater bekommt den Ausgang des
     * Betriebs, nicht den eines Kunden.
     *
     * @return list<array<string,mixed>>
     */
    public function imZeitraum(string $von, string $bis): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM documents WHERE created_at >= ? AND created_at < ?'
            . ' ORDER BY number ASC'
        );
        $anweisung->execute([$von . ' 00:00:00', $bis . ' 23:59:59']);

        return $anweisung->fetchAll();
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
