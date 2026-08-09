<?php

declare(strict_types=1);

namespace Sartu\Data;

/**
 * Der Weg, den eine eingehende Zahlungsbenachrichtigung nimmt —
 * `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6.
 *
 * **Warum ausserhalb beider Zugriffsschichten.** Ein Webhook hat keinen Akteur. Er kommt von
 * einem fremden Rechner, traegt keine Sitzung und gehoert zu keiner Organisation, die
 * irgendwo in einer Sitzung staende. Ihn durch die Adminschicht zu fuehren hiesse, einen
 * `AdminNachweis` zu erfinden, den niemand erbracht hat — dieselbe Ueberlegung wie bei
 * `Faelligkeiten` und `AnmeldeKonten`, und aus demselben Grund: §3 Regel 2 verbietet den
 * gemeinsamen Codepfad mit abschaltbarer Pruefung, nicht die eigene schmale Klasse daneben.
 *
 * Die Klasse ist deshalb absichtlich eng: Sie kann eine Benachrichtigung festhalten, eine
 * Rechnung **ueber die Kennung des Zahlungsdienstes** finden und einen geprueften Eingang
 * buchen. Sie kann nicht suchen, nicht auflisten und nichts loeschen.
 */
final class Zahlungseingaenge
{
    /** Was aus einer Benachrichtigung wurde — steht in `payment_events.result`. */
    public const ERGEBNIS_GEBUCHT     = 'gebucht';
    public const ERGEBNIS_UNVERAENDERT = 'unveraendert';
    public const ERGEBNIS_ABWEICHUNG  = 'abweichung';
    public const ERGEBNIS_UNBEKANNT   = 'unbekannt';

    public function __construct(private readonly ?\PDO $pdo = null)
    {
    }

    /**
     * Haelt eine eingegangene Benachrichtigung fest — **vor** der Verarbeitung.
     *
     * §6: „Jede eingegangene Benachrichtigung wird mit ihrer Kennung in `payment_events`
     * festgehalten, **bevor** verarbeitet wird."
     *
     * Der Weg ist **einfuegen und scheitern lassen**, nicht vorher fragen. Eine Abfrage
     * „gibt es die Kennung schon?" haette zwischen Lesen und Schreiben ein Fenster, in dem
     * zwei gleichzeitige Zustellungen beide durchkaemen — und Mollie stellt bei ausbleibender
     * Antwort erneut zu, also ist gleichzeitig der Normalfall und nicht der Sonderfall.
     *
     * @return string|null die Kennung der Zeile, oder `null`, wenn es sie schon gab
     */
    public function festhalten(string $kennung, string $rumpfHash): ?string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo()->prepare(
            'INSERT INTO payment_events (id, provider_event_id, received_at, payload_hash)'
            . ' VALUES (?, ?, ?, ?)'
        );

        try {
            $anweisung->execute([$id, $kennung, Db::jetzt(), $rumpfHash]);
        } catch (\PDOException $fehler) {
            // 23000 ist die Verletzung des `UNIQUE` — also die zweite Zustellung derselben
            // Nachricht. Das ist der erwartete Ausgang und kein Fehler.
            if ($fehler->getCode() === '23000') {
                return null;
            }

            throw $fehler;
        }

        return $id;
    }

    /** Schliesst die Verarbeitung ab. `processed_at` bleibt sonst leer — und das ist sichtbar. */
    public function abschliessen(string $ereignisId, string $ergebnis, ?string $rechnungId): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE payment_events SET processed_at = ?, result = ?, invoice_id = ? WHERE id = ?'
        );
        $anweisung->execute([Db::jetzt(), $ergebnis, $rechnungId, $ereignisId]);
    }

    /** @return array<string,mixed>|null */
    public function ereignis(string $kennung): ?array
    {
        $anweisung = $this->pdo()->prepare('SELECT * FROM payment_events WHERE provider_event_id = ?');
        $anweisung->execute([$kennung]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /**
     * Die Rechnung zu einer Zahlungskennung.
     *
     * Ueber `payment_provider_id`, nicht ueber eine Referenz aus dem Rumpf: Die Kennung hat
     * der Server selbst beim Anlegen der Zahlung gespeichert. Was in der Benachrichtigung
     * steht, ist eine Behauptung von aussen.
     *
     * @return array<string,mixed>|null
     */
    public function rechnungZurKennung(string $zahlungskennung): ?array
    {
        $anweisung = $this->pdo()->prepare('SELECT * FROM invoices WHERE payment_provider_id = ?');
        $anweisung->execute([$zahlungskennung]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** Merkt die Zahlungskennung und die Zahlungsadresse an der Rechnung — beim Versand. */
    public function zahlungHinterlegen(string $rechnungId, string $kennung, ?string $adresse): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE invoices SET payment_provider_id = ?, mollie_payment_url = ? WHERE id = ?'
        );
        $anweisung->execute([$kennung, $adresse, $rechnungId]);
    }

    /**
     * Bucht einen **geprueften** Eingang.
     *
     * `marked_paid_by_user_id` bleibt leer: Es war kein Mensch. Wer im Protokoll nach dem
     * Akteur sucht, findet das Ereignis mit der Aktion `zahlung_abgeglichen` und die Zeile in
     * `payment_events` — und nicht den zuletzt angemeldeten Admin, der nichts getan hat.
     */
    public function buchen(string $rechnungId, int $bezahltCent, string $zustand, ?string $bezahltAm): void
    {
        $anweisung = $this->pdo()->prepare(
            'UPDATE invoices SET paid_cents = ?, status = ?, paid_at = ?, marked_paid_by_user_id = NULL'
            . ' WHERE id = ?'
        );
        $anweisung->execute([$bezahltCent, $zustand, $bezahltAm, $rechnungId]);
    }

    /**
     * Die Organisation zu einer Rechnung — fuer den Protokolleintrag.
     *
     * Der Abgleich braucht sie, hat aber keine Sitzung, aus der sie kaeme. Sie steht hier
     * und nicht im Dienst, weil §1.3 SQL ausschliesslich in `/app/data` erlaubt.
     */
    public function organisationZurRechnung(string $rechnungId): ?string
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT p.organization_id FROM invoices i JOIN projects p ON p.id = i.project_id WHERE i.id = ?'
        );
        $anweisung->execute([$rechnungId]);

        $wert = $anweisung->fetchColumn();

        return is_string($wert) ? $wert : null;
    }

    /**
     * Das Projekt zu einer Rechnung — fuer die Benachrichtigung des Kunden.
     *
     * @return array<string,mixed>|null
     */
    public function projektZurRechnung(string $rechnungId): ?array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT p.* FROM projects p JOIN invoices i ON i.project_id = p.id WHERE i.id = ?'
        );
        $anweisung->execute([$rechnungId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /**
     * Die Vorgaenge, die nichts bewirkt haben — §6, „im Adminbereich sichtbar gemacht".
     *
     * @return list<array<string,mixed>>
     */
    public function abweichungen(int $grenze = 50): array
    {
        $anweisung = $this->pdo()->prepare(
            'SELECT e.*, i.number FROM payment_events e'
            . ' LEFT JOIN invoices i ON i.id = e.invoice_id'
            . ' WHERE e.result IN (?, ?) ORDER BY e.received_at DESC LIMIT ' . max(1, $grenze)
        );
        $anweisung->execute([self::ERGEBNIS_ABWEICHUNG, self::ERGEBNIS_UNBEKANNT]);

        return $anweisung->fetchAll();
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
