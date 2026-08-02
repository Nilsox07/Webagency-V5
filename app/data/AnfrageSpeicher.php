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

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
