<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Der Schreibweg fuer Anfragen aus dem Bedarfsscheck — Portal-Lastenheft §4b.
 *
 * Absichtlich schmal, wie `AnmeldeKonten`: Diese Klasse wird vom **oeffentlichen**
 * Formular aus benutzt, also ohne jede Anmeldung. Sie kann genau zwei Dinge — eine Anfrage
 * anlegen und pruefen, ob eine `submission_id` schon bekannt ist.
 *
 * Sie kennt **keine Liste**, keine Suche und keinen Statuswechsel. Wer Anfragen ansehen oder
 * bearbeiten will, geht ueber `Data\Admin\AdminAnfragen` und braucht dafuer einen Nachweis.
 */
final class AnfrageSpeicher
{
    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    /** §4b.3: `submission_id` ist eindeutig. Doppelklick, Neuladen und Zurueck-Taste. */
    public function kenntEinreichung(string $submissionId): bool
    {
        $anweisung = $this->pdo()->prepare('SELECT COUNT(*) FROM leads WHERE submission_id = ?');
        $anweisung->execute([$submissionId]);

        return (int) $anweisung->fetchColumn() > 0;
    }

    /**
     * @param array<string,scalar|null> $werte
     */
    public function anlegen(array $werte): string
    {
        $id = Uuid::v4();

        $spalten = array_keys($werte);
        $platzhalter = array_fill(0, count($spalten), '?');

        $anweisung = $this->pdo()->prepare(sprintf(
            'INSERT INTO leads (id, %s) VALUES (?, %s)',
            implode(', ', $spalten),
            implode(', ', $platzhalter),
        ));
        $anweisung->execute([$id, ...array_values($werte)]);

        return $id;
    }

    /**
     * §15.1: `source_ip` wird nach 30 Tagen geleert, der uebrige Datensatz bleibt.
     *
     * `source_ip IS NOT NULL` steht in der Bedingung, damit der Lauf jeden Tag nur die
     * Zeilen anfasst, bei denen sich etwas aendert — und damit `rowCount()` die Zahl
     * meldet, die im Protokoll steht.
     *
     * @return int Wie viele Zeilen geleert wurden.
     */
    public function herkunftsadressenLeeren(string $grenzeUtc): int
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE leads SET source_ip = NULL WHERE source_ip IS NOT NULL AND submitted_at < ?'
        );
        $anweisung->execute([$grenzeUtc]);

        return $anweisung->rowCount();
    }

    /**
     * §4b.4: Umgewandelte Anfragen sind Teil der Kundenakte und werden **nie** automatisch
     * geloescht. Die Bedingung steht hier in der Abfrage und nicht als `if` im Dienst —
     * eine Bedingung im Code laesst sich wegnehmen, ohne dass jemand es merkt.
     *
     * @return list<string>
     */
    public function faelligeIds(string $heute): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT id FROM leads WHERE converted_organization_id IS NULL AND delete_after <= ?'
        );
        $anweisung->execute([$heute]);

        return array_map(static fn ($z) => (string) $z, $anweisung->fetchAll(\PDO::FETCH_COLUMN));
    }

    /** §4b.4 und §15.1: echtes DELETE, ausdrueckliche Ausnahme von §3 Regel 13. */
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
