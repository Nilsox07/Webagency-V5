<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Der lueckenlose Belegzaehler — `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 4.
 *
 * **Warum ausserhalb beider Zugriffsschichten.** Eine Belegnummer gehoert zu keiner
 * Organisation. Sie zaehlt ueber alle Kunden hinweg, weil ein Nummernkreis genau das ist:
 * eine Reihe, in der nichts fehlen darf. Ihn durch die Kundenschicht zu fuehren hiesse, ihn
 * je Organisation zu fuehren — und jede haette ihre eigene `RE-2026-001`.
 *
 * Dieselbe Loesung wie bei `Faelligkeiten` und `AnmeldeKonten`: absichtlich schmal, kein
 * Zugriff ausser den zwei Faellen, die Abschnitt 4 nennt.
 *
 * ## Die Zeilensperre ist der ganze Punkt
 *
 * §4: „Die Nummer entsteht **in einer Transaktion mit dem Beleg**, unter Zeilensperre auf dem
 * Zaehler. Zwei gleichzeitige Vorgaenge bekommen nie dieselbe Nummer und lassen nie eine aus."
 *
 * `SELECT ... FOR UPDATE` haelt die Zaehlerzeile bis zum Ende der Transaktion. Ein zweiter
 * Vorgang wartet dort — er liest nicht denselben Stand und rechnet nicht darauf weiter.
 *
 * **Ohne umgebende Transaktion greift die Sperre nicht.** Deshalb prueft `naechste()`, dass
 * eine laeuft, statt sich selbst eine zu oeffnen: Eine eigene Transaktion waere beim Commit
 * zu Ende, und der Beleg entstuende danach — genau die Luecke, die §4 verbietet.
 */
final class Nummernkreise
{
    /** Die drei Arten aus §4. `AN` Angebot, `RE` Rechnung, `ST` Storno. */
    public const ARTEN = ['AN' => 'Angebot', 'RE' => 'Rechnung', 'ST' => 'Storno'];

    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    /**
     * Die naechste Nummer der Art im Jahr — `AN-JJJJ-NNN`.
     *
     * Muss **innerhalb** einer laufenden Transaktion aufgerufen werden. Der Aufrufer legt in
     * derselben Transaktion den Beleg an; erst sein Commit gibt die Zeile wieder frei.
     *
     * @throws \RuntimeException wenn keine Transaktion laeuft oder die Art unbekannt ist
     */
    public function naechste(string $art, int $jahr): string
    {
        if (!isset(self::ARTEN[$art])) {
            throw new \RuntimeException('Unbekannte Belegart: ' . $art);
        }

        $pdo = $this->pdo();

        if (!$pdo->inTransaction()) {
            // Kein stilles Nachholen. Wer hier eine Transaktion eroeffnete, gaebe die Sperre
            // beim Commit frei, bevor der Beleg steht — und zwei Vorgaenge kaemen an
            // dieselbe Nummer.
            throw new \RuntimeException(
                'Eine Belegnummer entsteht nur innerhalb der Transaktion, die auch den Beleg anlegt.'
            );
        }

        // Die Sperre. Ab hier wartet jeder zweite Vorgang auf denselben Zaehler.
        $lesen = $pdo->prepare(
            'SELECT last_number FROM number_sequences WHERE kind = ? AND year = ? FOR UPDATE'
        );
        $lesen->execute([$art, $jahr]);

        $stand = $lesen->fetchColumn();

        if ($stand === false) {
            // Die Zeile muss **vor** der Transaktion entstehen, siehe `sicherstellen()`.
            throw new \RuntimeException('Der Zaehler ' . $art . '-' . $jahr . ' ist nicht angelegt.');
        }

        $naechste = (int) $stand + 1;

        $schreiben = $pdo->prepare(
            'UPDATE number_sequences SET last_number = ? WHERE kind = ? AND year = ?'
        );
        $schreiben->execute([$naechste, $art, $jahr]);

        return sprintf('%s-%04d-%03d', $art, $jahr, $naechste);
    }

    /** Der aktuelle Stand — fuer die Anzeige und fuer Tests. Ohne Sperre, ohne Vergabe. */
    public function stand(string $art, int $jahr): int
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT last_number FROM number_sequences WHERE kind = ? AND year = ?'
        );
        $anweisung->execute([$art, $jahr]);

        $stand = $anweisung->fetchColumn();

        return $stand === false ? 0 : (int) $stand;
    }

    /**
     * Legt die Zaehlerzeile an, falls es sie noch nicht gibt.
     *
     * `INSERT IGNORE` statt „nachsehen und dann anlegen": Zwei gleichzeitige Vorgaenge im
     * Januar wuerden sonst beide feststellen, dass die Zeile fehlt, und beide anlegen. Der
     * eindeutige Schluessel faengt den zweiten ab, und `IGNORE` macht daraus kein Ereignis.
     *
     * ## Warum das VOR der Transaktion laufen muss — gefunden am 09.08.2026
     *
     * Es stand zuerst **in** der Transaktion, direkt vor dem `SELECT ... FOR UPDATE`. Der
     * Nebenlaeufigkeitstest aus Fall 85 hat das mit acht gleichzeitigen Prozessen sofort
     * zerlegt: `SQLSTATE[40001] Deadlock found when trying to get lock`.
     *
     * **Warum es verklemmt.** `INSERT IGNORE` auf einen bereits belegten eindeutigen
     * Schluessel nimmt in InnoDB eine Absichtssperre auf die Luecke. Acht Vorgaenge, die
     * diese Sperre halten und danach dieselbe Zeile exklusiv lesen wollen, warten kreuzweise
     * aufeinander — ein Wartekreis, den der Server nur durch Abbruch aufloest.
     *
     * Ausserhalb der Transaktion ist jedes `INSERT IGNORE` eine eigene, sofort abgeschlossene
     * Anweisung. Es gibt keine Sperre, die ueber die naechste Anweisung hinaus gehalten wird,
     * und damit keinen Wartekreis. Geloescht wird ein Zaehler nie — zwischen dem Anlegen und
     * der Sperre kann er also nicht verschwinden.
     */
    public function sicherstellen(string $art, int $jahr): void
    {
        $anweisung = $this->pdo()->prepare(
            'INSERT IGNORE INTO number_sequences (id, kind, year, last_number) VALUES (?, ?, ?, 0)'
        );
        $anweisung->execute([Uuid::v4(), $art, $jahr]);
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
