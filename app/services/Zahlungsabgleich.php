<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\AuditProtokoll;
use Sartu\Data\Db;
use Sartu\Data\Zahlungseingaenge;
use Sartu\Helpers\Format;

/**
 * Was geschieht, wenn es klingelt — `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6, Fälle 90 bis 92.
 *
 * ## Die sechs Schritte, in dieser Reihenfolge
 *
 * 1. Die Benachrichtigung wird mit ihrer Kennung festgehalten — **vor** allem anderen
 * 2. Gab es die Kennung schon: Bestätigung ohne Wirkung, kein Fehler (Fall 91)
 * 3. Der Server ruft den Zustand **selbst** beim Dienst ab (Fall 90)
 * 4. Die Rechnung wird über die gespeicherte Zahlungskennung gefunden
 * 5. Betrag und Währung werden gegen die Rechnung geprüft (Fall 92)
 * 6. Erst danach ändert sich der Zustand, mit Audit-Ereignis
 *
 * ## Warum diese Klasse nichts entgegennimmt ausser einer Kennung
 *
 * §6: „Mollie ruft die Webhook-Adresse auf und übergibt **nur eine Kennung**. Der Server
 * ruft mit dieser Kennung den Zahlungsstatus **selbst** bei Mollie ab."
 *
 * `verarbeiten()` nimmt deshalb eine Zeichenkette und sonst nichts. Es gibt keinen
 * Parameter für einen Betrag, keinen für einen Zustand und keinen für eine Rechnung — auch
 * nicht optional. **Ein Parameter, den es nicht gibt, kann nicht gefälscht werden.** Wer
 * diesen Endpunkt mit einer erfundenen Kennung aufruft, erreicht genau eines: dass der
 * Server Mollie nach einer Zahlung fragt, die es nicht gibt, und nichts tut.
 *
 * ## Warum eine Abweichung keinen Fehler ergibt
 *
 * §6: „Eine bereits verarbeitete Kennung führt zu einer Bestätigung ohne Wirkung — nicht zu
 * einem Fehler. Mollie wiederholt sonst." Dasselbe gilt für eine Abweichung: Sie ändert
 * nichts, wird protokolliert und im Adminbereich sichtbar — aber eine Fehlerantwort würde
 * dieselbe Nachricht endlos wiederholen lassen, und die Abweichung wäre danach hundertmal
 * protokolliert.
 *
 * ## Teilzahlung ist keine Abweichung
 *
 * §6: „Eine Teilzahlung ist **keine** Abweichung, sondern ein eigener Zustand. Eine
 * Überzahlung ist eine Abweichung." Mollie bezahlt eine angelegte Zahlung allerdings immer
 * vollständig oder gar nicht; eine Teilzahlung entsteht dort, wo ein Kunde überweist. Der
 * Vergleich unten ist deshalb `abgerufen > brutto` — darunter wird gebucht und der Zustand
 * gerechnet, nicht gesetzt.
 */
final class Zahlungsabgleich
{
    /** Die Zustände von Mollie, bei denen Geld geflossen ist. */
    public const BEZAHLT = ['paid', 'authorized'];

    /**
     * Wie eine Zahlungskennung aussehen darf.
     *
     * ## Warum das keine Kosmetik ist
     *
     * Dieser Endpunkt ist der **einzige** der Anwendung, den jemand ohne Sitzung erreicht.
     * Ohne Formprüfung tut er für **jede** eingehende Zeichenkette zwei Dinge: Er schreibt
     * eine Zeile in `payment_events`, und er ruft den Zahlungsdienst. Wer das in einer
     * Schleife aufruft, füllt die Tabelle und erzeugt genauso viele ausgehende Anfragen —
     * aus einem billigen Aufruf wird eine teure Handlung.
     *
     * Die Prüfung steht **vor** dem Festhalten. Was nicht wie eine Kennung aussieht, kann
     * keine sein: Mollie vergibt `tr_` und danach Buchstaben und Ziffern. Eine echte
     * Benachrichtigung besteht sie immer, eine erfundene meistens nicht.
     *
     * Sie ersetzt keine Prüfung des Inhalts — die gibt es hier bewusst nicht, der Server
     * fragt selbst nach. Sie hält nur das fern, was schon der Form nach nichts sein kann.
     */
    public const KENNUNG_MUSTER = '/^[A-Za-z]{2,10}_[A-Za-z0-9]{5,80}$/';

    /**
     * Wie viele Benachrichtigungen von **einer** Gegenstelle je Stunde angenommen werden.
     *
     * Grosszügig gewählt: Bei einstelligen Rechnungszahlen je Monat liegt der echte Bedarf
     * bei einer Handvoll am Tag, und Mollie wiederholt eine Zustellung nur wenige Male.
     * Die Grenze trifft deshalb keinen echten Aufruf und bremst eine Schleife trotzdem.
     *
     * **Beim Anschlagen wird 429 geantwortet, nicht 200.** Eine Bestätigung würde eine
     * abgewiesene Nachricht als erledigt ausweisen — Mollie stellt dann nie wieder zu.
     */
    public const AUFRUFE_JE_STUNDE = 120;

    public function __construct(
        private readonly Zahlungsdienst $dienst,
        private readonly ?Zahlungseingaenge $eingaenge = null,
        private readonly ?AuditProtokoll $audit = null,
        private readonly ?Versender $mail = null,
        private readonly ?Ratenbegrenzung $begrenzung = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * Verarbeitet eine Benachrichtigung.
     *
     * @return array{status:int,text:string,ergebnis:string}
     */
    public function verarbeiten(string $kennung, string $rumpf, ?string $ip = null): array
    {
        $kennung = trim($kennung);

        if ($kennung === '') {
            return ['status' => 400, 'text' => 'Es fehlt die Kennung.', 'ergebnis' => ''];
        }

        // Schritt 0 — was der Form nach keine Kennung ist, kostet nichts.
        //
        // Vor dem Festhalten und vor dem Abruf: Sonst schreibt jede erfundene Zeichenkette
        // eine Zeile und löst eine ausgehende Anfrage aus. Auch die Länge hängt daran — die
        // Spalte fasst 100 Zeichen, und ein längerer Wert wäre sonst ein Fehler 500.
        if (preg_match(self::KENNUNG_MUSTER, $kennung) !== 1) {
            return ['status' => 400, 'text' => 'Die Kennung hat kein gültiges Format.', 'ergebnis' => 'ungueltig'];
        }

        // Schritt 0b — dieselbe Überlegung für die Menge.
        if ($ip !== null && $ip !== '' && !$this->begrenzung()->erlaubt(
            'zahlungswebhook:' . $ip,
            self::AUFRUFE_JE_STUNDE,
            3600,
        )) {
            // 429 und nicht 200: Eine Bestätigung würde diese Nachricht als erledigt
            // ausweisen, und der Dienst stellte sie nie wieder zu.
            return ['status' => 429, 'text' => 'Zu viele Aufrufe.', 'ergebnis' => 'begrenzt'];
        }

        if ($ip !== null && $ip !== '') {
            $this->begrenzung()->vermerken('zahlungswebhook:' . $ip, 3600);
        }

        // Schritt 1 und 2 — festhalten, bevor irgendetwas geschieht.
        $ereignisId = $this->eingaenge()->festhalten($kennung, hash('sha256', $rumpf));

        if ($ereignisId === null) {
            // Fall 91: Dieselbe Nachricht ein zweites Mal. Der Zustand bleibt, wie er ist,
            // und die Antwort ist eine Bestätigung — sonst stellt Mollie weiter zu.
            return ['status' => 200, 'text' => 'Bereits verarbeitet.', 'ergebnis' => 'wiederholung'];
        }

        // Schritt 3 — der Server fragt selbst.
        try {
            $zahlung = $this->dienst->zahlungLesen($kennung);
        } catch (ZahlungsdienstFehler $fehler) {
            // Der Abruf ging schief. Die Zeile bleibt mit leerem `processed_at` stehen, und
            // die Antwort ist ein Fehler — **hier** ist eine Wiederholung erwünscht, denn
            // verarbeitet wurde nichts.
            $this->protokollieren($kennung, null, 'zahlung_abruf_gescheitert', $fehler->getMessage(), null, $ip);

            return ['status' => 502, 'text' => 'Der Abruf beim Zahlungsdienst ist gescheitert.',
                'ergebnis' => 'abruf_gescheitert'];
        }

        // Schritt 4 — über die gespeicherte Kennung, nicht über eine Angabe von aussen.
        $rechnung = $this->eingaenge()->rechnungZurKennung($zahlung['kennung']);

        if ($rechnung === null) {
            $this->eingaenge()->abschliessen($ereignisId, Zahlungseingaenge::ERGEBNIS_UNBEKANNT, null);
            $this->protokollieren(
                $kennung,
                null,
                'zahlung_ohne_rechnung',
                'Zu dieser Zahlungskennung gibt es keine Rechnung.',
                null,
                $ip,
            );

            return ['status' => 200, 'text' => 'Angenommen.', 'ergebnis' => Zahlungseingaenge::ERGEBNIS_UNBEKANNT];
        }

        $rechnungId = (string) $rechnung['id'];
        $brutto = (int) $rechnung['gross_cents'];
        $abgerufen = $zahlung['betrag_cents'];

        // Schritt 5 — Betrag und Währung.
        $abweichung = null;

        if ($zahlung['waehrung'] !== ERechnung::WAEHRUNG) {
            $abweichung = 'Die abgerufene Währung ' . $zahlung['waehrung'] . ' passt nicht zur Rechnung.';
        } elseif ($abgerufen > $brutto) {
            $abweichung = 'Der abgerufene Betrag ' . Format::euro($abgerufen)
                . ' übersteigt den Rechnungsbetrag ' . Format::euro($brutto) . '.';
        }

        if ($abweichung !== null) {
            // Fall 92: Es ändert sich **nichts** am Zustand.
            $this->eingaenge()->abschliessen($ereignisId, Zahlungseingaenge::ERGEBNIS_ABWEICHUNG, $rechnungId);
            $this->protokollieren($kennung, $rechnungId, 'zahlung_abweichung', $abweichung, [
                'abgerufen_cents' => $abgerufen,
                'rechnung_cents'  => $brutto,
                'waehrung'        => $zahlung['waehrung'],
                'zustand_dienst'  => $zahlung['zustand'],
            ], $ip);

            return ['status' => 200, 'text' => 'Angenommen.', 'ergebnis' => Zahlungseingaenge::ERGEBNIS_ABWEICHUNG];
        }

        if (!in_array($zahlung['zustand'], self::BEZAHLT, true) || $abgerufen <= 0) {
            // Angelegt, abgebrochen, abgelaufen — es ist kein Geld geflossen. Die Nachricht
            // ist trotzdem angekommen und gilt als verarbeitet; wiederholen bringt nichts.
            $this->eingaenge()->abschliessen($ereignisId, Zahlungseingaenge::ERGEBNIS_UNVERAENDERT, $rechnungId);

            return ['status' => 200, 'text' => 'Angenommen.', 'ergebnis' => Zahlungseingaenge::ERGEBNIS_UNVERAENDERT];
        }

        // Schritt 6 — erst jetzt.
        $vorher = (string) $rechnung['status'];
        $ueberfaellig = Rechnungsdienst::istUeberfaellig(['paid_cents' => $abgerufen] + $rechnung);
        $zustand = Zahlungsstatus::ausBetrag($abgerufen, $brutto, $ueberfaellig);
        $bezahltAm = $zustand === Zahlungsstatus::BEZAHLT ? Db::jetzt() : null;

        $this->eingaenge()->buchen($rechnungId, $abgerufen, $zustand, $bezahltAm);

        $this->audit()->schreiben(
            aktion: 'zahlung_abgeglichen',
            objektart: 'invoice',
            objektId: $rechnungId,
            // Kein Akteur: Es war kein Mensch. Ein eingetragener Admin wäre eine Behauptung
            // über jemanden, der nichts getan hat.
            akteurBenutzerId: null,
            organisationId: $this->organisation($rechnungId),
            alterWert: $vorher,
            neuerWert: $zustand,
            // §12 verlangt bei Geld einen Grundlagentext. Er ist hier der Nachweis des
            // Abrufs, nicht ein Freitext — der Abruf **ist** die Prüfung.
            grund: 'Serverseitiger Abruf beim Zahlungsdienst, Zahlung ' . $zahlung['kennung'],
            detail: [
                'zahlungskennung' => $zahlung['kennung'],
                'abgerufen_cents' => $abgerufen,
                'zustand_dienst'  => $zahlung['zustand'],
            ],
            ip: $ip,
        );

        $this->eingaenge()->abschliessen($ereignisId, Zahlungseingaenge::ERGEBNIS_GEBUCHT, $rechnungId);

        $this->kundeBenachrichtigen($rechnungId, $zustand, $abgerufen, $brutto);

        return ['status' => 200, 'text' => 'Angenommen.', 'ergebnis' => Zahlungseingaenge::ERGEBNIS_GEBUCHT];
    }

    // ---------------------------------------------------------------- intern

    /** @param array<string,mixed>|null $detail */
    private function protokollieren(
        string $kennung,
        ?string $rechnungId,
        string $aktion,
        string $grund,
        ?array $detail,
        ?string $ip,
    ): void {
        $this->audit()->schreiben(
            aktion: $aktion,
            objektart: 'invoice',
            objektId: $rechnungId,
            akteurBenutzerId: null,
            organisationId: $rechnungId === null ? null : $this->organisation($rechnungId),
            grund: $grund,
            detail: ($detail ?? []) + ['zahlungskennung' => $kennung],
            ip: $ip,
        );
    }

    private function organisation(string $rechnungId): ?string
    {
        return $this->eingaenge()->organisationZurRechnung($rechnungId);
    }

    private function kundeBenachrichtigen(string $rechnungId, string $zustand, int $bezahlt, int $brutto): void
    {
        $projekt = $this->eingaenge()->projektZurRechnung($rechnungId);

        if ($projekt === null) {
            return;
        }

        // Derselbe Wortlaut wie bei der Buchung von Hand (§10). Der Kunde soll nicht daran
        // ablesen können, ob ein Mensch oder der Abgleich gebucht hat — für ihn ist es
        // dasselbe Ereignis.
        $mail = new Projektmail($this->mail, $this->pdo);

        if ($zustand === Zahlungsstatus::BEZAHLT) {
            $mail->anKunden($projekt, 'Zahlungseingang bestätigt', "Wir haben Ihre Zahlung erhalten. Vielen Dank.\n");

            return;
        }

        $mail->anKunden($projekt, 'Teilzahlung erhalten',
            'Wir haben ' . Format::euro($bezahlt) . ' erhalten. Offen sind noch '
            . Format::euro(Zahlungsstatus::restbetrag($bezahlt, $brutto)) . ".\n");
    }

    private function eingaenge(): Zahlungseingaenge
    {
        return $this->eingaenge ?? new Zahlungseingaenge($this->pdo);
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll($this->pdo);
    }

    private function begrenzung(): Ratenbegrenzung
    {
        return $this->begrenzung ?? new Ratenbegrenzung();
    }
}
