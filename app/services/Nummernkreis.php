<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\Db;
use Sartu\Data\Nummernkreise;
use Sartu\Helpers\Format;

/**
 * Belegnummern vergeben — `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 4.
 *
 * ## Warum die Vergabe eine Klammer ist und keine Methode, die eine Nummer zurueckgibt
 *
 * §4 verlangt die Nummer „**in einer Transaktion mit dem Beleg**". Eine Methode
 * `naechsteNummer()`, die eine Zeichenkette liefert, kann das nicht zusichern — der Aufrufer
 * bekaeme sie und legte den Beleg irgendwann danach an, vielleicht ausserhalb jeder
 * Transaktion.
 *
 * `vergeben()` dreht das um: Es oeffnet die Transaktion, sperrt den Zaehler, ruft den
 * Aufrufer **mit** der Nummer und schliesst erst danach ab. Wer die Nummer haben will, legt
 * den Beleg in derselben Klammer an — anders kommt er nicht an sie heran.
 *
 * ## Was bei einem Fehler geschieht
 *
 * Wirft der Aufrufer, wird zurueckgerollt: **auch der Zaehlerstand**. Das ist richtig so —
 * der Beleg ist nicht entstanden, und §4 sagt: „Der Beleg entsteht erst mit der Nummer."
 * Eine Nummer, die keinen Beleg bekam, waere die Luecke, die §4 verbietet.
 *
 * Ein **verworfener** Beleg ist etwas anderes: Er ist entstanden, seine Nummer bleibt
 * vergeben und wird nie wieder benutzt (§5).
 *
 * ## Das Jahr kommt aus der Anzeigezeit
 *
 * `Format::heute()` rechnet nach Europe/Berlin. Eine Rechnung, die am 1. Januar um 00:30 Uhr
 * entsteht, gehoert ins neue Jahr — in UTC waere sie noch im alten, und der Nummernkreis
 * haette zwei Reihen fuer denselben Tag.
 */
final class Nummernkreis
{
    public function __construct(
        private readonly ?Nummernkreise $kreise = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * Vergibt eine Nummer und legt in derselben Transaktion den Beleg an.
     *
     * @template T
     * @param \Closure(string):T $anlegen bekommt die vergebene Nummer
     * @return T was der Aufrufer zurueckgibt
     *
     * @throws \RuntimeException wenn bereits eine Transaktion laeuft
     */
    public function vergeben(string $art, \Closure $anlegen, ?int $jahr = null)
    {
        $pdo = $this->pdo();

        if ($pdo->inTransaction()) {
            // Verschachtelte Transaktionen kennt PDO nicht. Ein stilles Weiterlaufen haette
            // den aeusseren Commit zum Commit dieser Vergabe gemacht — mit einer Sperre, die
            // laenger haelt als noetig, und einem Rollback, der mehr zuruecknimmt als gedacht.
            throw new \RuntimeException('Eine Belegnummer wird nicht innerhalb einer fremden Transaktion vergeben.');
        }

        $jahr ??= (int) substr(Format::heute(), 0, 4);

        // **Vor** der Transaktion. Innerhalb erzeugt `INSERT IGNORE` auf einen belegten
        // Schluessel eine Luecken-Absichtssperre, und acht gleichzeitige Vorgaenge warten
        // danach kreuzweise auf dieselbe Zeile — der Nebenlaeufigkeitstest aus Fall 85 hat
        // genau das am 09.08.2026 als Deadlock ausgeworfen. Die Begruendung steht ausfuehrlich
        // in `Nummernkreise::sicherstellen()`.
        $this->kreise()->sicherstellen($art, $jahr);

        $pdo->beginTransaction();

        try {
            $nummer = $this->kreise()->naechste($art, $jahr);
            $ergebnis = $anlegen($nummer);

            $pdo->commit();

            return $ergebnis;
        } catch (\Throwable $fehler) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $fehler;
        }
    }

    /** Der aktuelle Stand je Art und Jahr — fuer die Anzeige im internen Bereich. */
    public function stand(string $art, ?int $jahr = null): int
    {
        return $this->kreise()->stand($art, $jahr ?? (int) substr(Format::heute(), 0, 4));
    }

    private function kreise(): Nummernkreise
    {
        return $this->kreise ?? new Nummernkreise($this->pdo);
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
