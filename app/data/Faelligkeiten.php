<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Die Abfragen des taeglichen Laufs — Portal-Lastenheft §5.3, §5.3a, §1.4.
 *
 * **Warum ausserhalb beider Zugriffsschichten:** Der Lauf ist eine Systemaufgabe, keine
 * Benutzeranfrage. Er laeuft nachts ueber die Befehlszeile, greift bewusst ueber alle
 * Organisationen und hat keinen Akteur. Ihn durch die Adminschicht zu fuehren hiesse, einen
 * Adminnachweis zu erfinden, den niemand erbracht hat — genau die Ausnahme, aus der §3
 * Regel 2 die typische Datenpanne entstehen sieht.
 *
 * Stattdessen dieselbe Loesung wie bei `AnmeldeKonten` und `ProjektZustand`: absichtlich
 * schmal, kein Zugriff ausser den vier Faellen, die §5.3 und §5.3a nennen.
 */
final class Faelligkeiten
{
    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    /**
     * §5.3: `ueberfaellig`, wenn `due_date < heute` und `paid_cents < gross_cents`.
     *
     * `teilweise_bezahlt` ist mit dabei — §5.3: „`teilweise_bezahlt` und `ueberfaellig`
     * schliessen sich nicht aus."
     *
     * @return list<array<string,mixed>>
     */
    public function ueberfaellige(string $heute): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT * FROM invoices'
            . " WHERE status IN ('gesendet','teilweise_bezahlt') AND due_date IS NOT NULL"
            . ' AND due_date < ? AND paid_cents < gross_cents AND archived_at IS NULL'
        );
        $anweisung->execute([$heute]);

        return $anweisung->fetchAll();
    }

    public function zustandSetzen(string $rechnungId, string $zustand): void
    {
        $anweisung = $this->pdo()->prepare('UPDATE invoices SET status = ? WHERE id = ?');
        $anweisung->execute([$zustand, $rechnungId]);
    }

    /** §5.3a, erste Erinnerung. Gefiltert wird ueber den Restbetrag, nicht ueber den Zustand. */
    public function ersteErinnerungFaellig(string $heute): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT i.*, o.contact_email FROM invoices i'
            . ' JOIN projects p ON p.id = i.project_id'
            . ' JOIN organizations o ON o.id = p.organization_id'
            . " WHERE i.status IN ('gesendet','teilweise_bezahlt','ueberfaellig')"
            . ' AND i.due_date IS NOT NULL AND i.due_date < ? AND i.paid_cents < i.gross_cents'
            . ' AND i.reminder_sent_at IS NULL AND i.archived_at IS NULL'
        );
        $anweisung->execute([$heute]);

        return $anweisung->fetchAll();
    }

    /** §5.3a, zweite Erinnerung: sieben Tage nach der ersten, Restbetrag weiterhin offen. */
    public function zweiteErinnerungFaellig(string $grenzeUtc): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT i.*, o.contact_email FROM invoices i'
            . ' JOIN projects p ON p.id = i.project_id'
            . ' JOIN organizations o ON o.id = p.organization_id'
            . " WHERE i.status IN ('gesendet','teilweise_bezahlt','ueberfaellig')"
            . ' AND i.paid_cents < i.gross_cents'
            . ' AND i.reminder_sent_at IS NOT NULL AND i.reminder_sent_at <= ?'
            . ' AND i.reminder2_sent_at IS NULL AND i.archived_at IS NULL'
        );
        $anweisung->execute([$grenzeUtc]);

        return $anweisung->fetchAll();
    }

    /**
     * `IS NULL` in der Bedingung, nicht nur im Wert.
     *
     * Ohne sie ueberschriebe ein zweiter Lauf den Zeitpunkt der ersten Erinnerung — und die
     * zweite haette ihre Sieben-Tage-Frist nie erreicht.
     *
     * **Zwei ausgeschriebene Anweisungen statt einer mit eingesetztem Spaltennamen.** Der
     * Name kaeme hier zwar aus einem festen Vergleich und nie von aussen — aber §1.2 sagt
     * „nie Zeichenketten zusammensetzen" ohne Ausnahme, und `PreparedStatementsTest` prueft
     * genau das im Quelltext. Eine Ausnahme, die heute sicher ist, ist die Vorlage fuer die
     * naechste, die es nicht ist.
     */
    public function ersteErinnerungVermerken(string $rechnungId): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE invoices SET reminder_sent_at = ? WHERE id = ? AND reminder_sent_at IS NULL'
        );
        $anweisung->execute([Db::jetzt(), $rechnungId]);
    }

    public function zweiteErinnerungVermerken(string $rechnungId): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE invoices SET reminder2_sent_at = ? WHERE id = ? AND reminder2_sent_at IS NULL'
        );
        $anweisung->execute([Db::jetzt(), $rechnungId]);
    }

    /** §5.2: Ein abgelaufenes Angebot ist nicht mehr annehmbar. */
    public function abgelaufeneAngeboteSetzen(string $heute): int
    {
        $anweisung = $this->pdo()->prepare(
            "UPDATE offers SET status = 'abgelaufen' WHERE status = 'gesendet' AND valid_until < ?"
        );
        $anweisung->execute([$heute]);

        return $anweisung->rowCount();
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
