<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\Db;
use Sartu\Data\Uuid;

/**
 * Kundenseitiger Zugriff auf `business_hours` und `business_hours_exceptions` —
 * Portal-Lastenheft §4, §8.7.
 *
 * Beide Tabellen tragen `organization_id` selbst; der Filter steht in jeder Abfrage und
 * kommt aus `KundenBereich`, also aus der Sitzung (§3 Regel 1).
 *
 * **Ersetzen statt zusammenfuehren.** Das Formular reicht immer alle sieben Wochentage und
 * die vollstaendige Ausnahmeliste ein — es gibt keinen Teilstand. Deshalb loescht
 * `ersetzen()` die Ausnahmen der Organisation und schreibt sie neu, waehrend die Wochentage
 * ueber den eindeutigen Schluessel aktualisiert werden. Eine zeilenweise Zuordnung ueber
 * mitgeschickte Kennungen waere ein Schluessel aus dem Request — und der ist kein Nachweis.
 *
 * **Die Ausnahmen sind keine fachlichen Daten im Sinn der Loeschregel.** §4a verbietet die
 * harte Loeschung von Vorgaengen — Angebote, Rechnungen, Erklaerungen. Eine gestrichene
 * Feiertagszeile ist kein Vorgang, sondern ein Stand, den der Kunde selbst pflegt; ein
 * `archived_at` daran wuerde die Liste bei jeder Aenderung waschsen lassen, ohne dass
 * irgendwer je hineinsieht.
 */
final class KundenOeffnungszeiten
{
    /** §8.7 zaehlt Montag bis Sonntag auf — also ist 0 der Montag. */
    public const TAGE = [
        0 => 'Montag',
        1 => 'Dienstag',
        2 => 'Mittwoch',
        3 => 'Donnerstag',
        4 => 'Freitag',
        5 => 'Samstag',
        6 => 'Sonntag',
    ];

    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return list<array<string,mixed>> nach Wochentag sortiert */
    public function wochentage(): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM business_hours WHERE organization_id = ? ORDER BY weekday ASC'
        );
        $anweisung->execute([$this->bereich->organisationId]);

        return $anweisung->fetchAll();
    }

    /** @return list<array<string,mixed>> */
    public function ausnahmen(): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM business_hours_exceptions WHERE organization_id = ? ORDER BY date ASC'
        );
        $anweisung->execute([$this->bereich->organisationId]);

        return $anweisung->fetchAll();
    }

    public function wartetAufVeroeffentlichung(): bool
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT COUNT(*) FROM business_hours WHERE organization_id = ? AND pending_publish = 1'
        );
        $anweisung->execute([$this->bereich->organisationId]);

        return (int) $anweisung->fetchColumn() > 0;
    }

    /**
     * Schreibt den vollstaendigen Stand.
     *
     * @param list<array{weekday:int,closed:bool,open_time:?string,close_time:?string,note:?string}> $tage
     * @param list<array{date:string,closed:bool,open_time:?string,close_time:?string,label:string}> $ausnahmen
     */
    public function ersetzen(array $tage, array $ausnahmen): void
    {
        $pdo = $this->pdo();
        $pdo->beginTransaction();

        try {
            foreach ($tage as $tag) {
                $this->tagSchreiben($tag);
            }

            $loeschen = $pdo->prepare('DELETE FROM business_hours_exceptions WHERE organization_id = ?');
            $loeschen->execute([$this->bereich->organisationId]);

            $einfuegen = $pdo->prepare(
                'INSERT INTO business_hours_exceptions'
                . ' (id, organization_id, date, closed, open_time, close_time, label)'
                . ' VALUES (?, ?, ?, ?, ?, ?, ?)'
            );

            foreach ($ausnahmen as $ausnahme) {
                $einfuegen->execute([
                    Uuid::v4(),
                    $this->bereich->organisationId,
                    $ausnahme['date'],
                    $ausnahme['closed'] ? 1 : 0,
                    $ausnahme['open_time'],
                    $ausnahme['close_time'],
                    $ausnahme['label'],
                ]);
            }

            $pdo->commit();
        } catch (\Throwable $fehler) {
            $pdo->rollBack();

            throw $fehler;
        }
    }

    /**
     * Ein Wochentag, angelegt oder aktualisiert.
     *
     * Der eindeutige Schluessel `(organization_id, weekday)` entscheidet — nicht eine
     * vorgelagerte Abfrage „gibt es den Montag schon?". Zwischen so einer Abfrage und dem
     * Schreiben liegt Zeit, und zwei gleichzeitige Absendevorgaenge legten zwei Montage an.
     *
     * `pending_publish = 1` steht in beiden Zweigen: Jede Aenderung wartet auf die
     * Veroeffentlichung, auch die allererste (§4).
     */
    private function tagSchreiben(array $tag): void
    {
        $anweisung = $this->pdo()->prepare(
            'INSERT INTO business_hours'
            . ' (id, organization_id, weekday, closed, open_time, close_time, note, pending_publish)'
            . ' VALUES (?, ?, ?, ?, ?, ?, ?, 1)'
            . ' ON DUPLICATE KEY UPDATE closed = VALUES(closed), open_time = VALUES(open_time),'
            . ' close_time = VALUES(close_time), note = VALUES(note), pending_publish = 1'
        );
        $anweisung->execute([
            Uuid::v4(),
            $this->bereich->organisationId,
            $tag['weekday'],
            $tag['closed'] ? 1 : 0,
            $tag['open_time'],
            $tag['close_time'],
            $tag['note'],
        ]);
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
