<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\AuditProtokoll;
use Sartu\Data\Customer\KundenAngebote;
use Sartu\Data\Customer\KundenAufgaben;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenDateien;
use Sartu\Data\Customer\KundenFreigaben;
use Sartu\Data\Customer\KundenProjekte;
use Sartu\Helpers\Validate;

/**
 * Aufgaben abschließen und Fakten freigeben — Portal-Lastenheft §8.3, §4 `approvals`.
 *
 * ## Vier Arten, vier Bedingungen
 *
 * | `kind` | Was fehlen darf, und was nicht |
 * |---|---|
 * | `bestaetigung` | „Stimmt so" genügt; eine Korrektur ist ein Text |
 * | `angabe` | Textfeld ist **Pflicht** (Testfall 16) |
 * | `upload` | mindestens **eine** Datei, Rechte bestätigt (Testfall 17) |
 * | `freigabe` | Häkchen **und** getippter Name — und alle Pflichtaufgaben erledigt (Testfall 26) |
 *
 * ## Die Freigabe ist keine Aufgabe wie die anderen
 *
 * §8.3: „Diese Aufgabe ist keine gewöhnliche Rückmeldung, sondern eine **protokollierte
 * Erklärung**." Sie erzeugt einen Eintrag in `approvals` mit `kind = inhalte`, ein
 * Audit-Ereignis und den Wechsel `briefing` → `produktion`. **Ab diesem Tag läuft der
 * Lieferkorridor** (§4c) — deshalb ist der Zeitpunkt beweisbar festzuhalten und nicht nur
 * abzuleiten.
 *
 * ## Die Sperre ist eine Sperre, kein Hinweis
 *
 * §8.3: „Die Freigabeaufgabe ist erst abschließbar, wenn **alle** Pflichtaufgaben mit
 * `required = true` erledigt sind. Sonst Hinweis **statt Button**." Der Knopf verschwindet,
 * und zusätzlich weist der Dienst ab — eine Oberfläche allein ist keine Regel.
 */
final class Aufgabendienst
{
    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?KundenAufgaben $aufgaben = null,
        private readonly ?KundenFreigaben $freigaben = null,
        private readonly ?Projektwechsel $wechsel = null,
        private readonly ?AuditProtokoll $audit = null,
        private readonly ?Versender $mail = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * Schließt eine Aufgabe ab.
     *
     * @param array<string,mixed> $eingabe
     *
     * @return list<string> leer bei Erfolg
     */
    public function abschliessen(string $aufgabeId, array $eingabe, string $benutzerId, ?string $ip): array
    {
        $aufgabe = $this->aufgaben()->finden($aufgabeId);

        // §3 Regel 2: gibt es nicht ODER gehört nicht mir — derselbe Ausgang.
        if ($aufgabe === null) {
            return ['Diese Aufgabe gibt es nicht.'];
        }

        if ((string) $aufgabe['status'] === 'erledigt') {
            return ['Diese Aufgabe ist bereits erledigt.'];
        }

        $art = (string) $aufgabe['kind'];
        $antwort = is_string($eingabe['answer_text'] ?? null) ? trim($eingabe['answer_text']) : '';

        if ($art === 'freigabe') {
            return $this->freigeben($aufgabe, $eingabe, $benutzerId, $ip);
        }

        if ($art === 'angabe' && !Validate::gefuellt($antwort)) {
            // §8.3, Wortlaut gebunden.
            return ['Bitte beantworten Sie die Frage, bevor Sie die Aufgabe abschließen.'];
        }

        if ($art === 'upload') {
            $anzahl = (new KundenDateien($this->bereich, $this->pdo))->anzahlJeAufgabe($aufgabeId);

            if ($anzahl === 0) {
                return ['Bitte wählen Sie mindestens eine Datei aus.'];
            }
        }

        $this->aufgaben()->abschliessen($aufgabeId, $antwort === '' ? null : $antwort, $benutzerId);

        $this->audit()->schreiben(
            aktion: 'aufgabe_erledigt',
            objektart: 'task',
            objektId: $aufgabeId,
            akteurBenutzerId: $benutzerId,
            organisationId: $this->bereich->organisationId,
            alterWert: 'offen',
            neuerWert: 'erledigt',
            ip: $ip,
        );

        return [];
    }

    /**
     * Die Faktenfreigabe — §8.3, §4 `approvals`, §5.1a.
     *
     * @param array<string,mixed> $aufgabe
     * @param array<string,mixed> $eingabe
     *
     * @return list<string>
     */
    private function freigeben(array $aufgabe, array $eingabe, string $benutzerId, ?string $ip): array
    {
        $projektId = (string) $aufgabe['project_id'];

        // Die Sperre aus §8.3 — vor allem anderen, auch vor der Prüfung der Eingaben.
        if ($this->aufgaben()->offenePflichtaufgaben($projektId) > 0) {
            return ['Bitte schließen Sie zuerst die noch offenen Aufgaben ab.'];
        }

        $fehler = [];

        if (($eingabe['bestaetigung'] ?? null) !== '1') {
            $fehler[] = 'Bitte bestätigen Sie die Freigabe.';
        }

        $name = is_string($eingabe['granted_name'] ?? null) ? trim($eingabe['granted_name']) : '';

        if (!Validate::gefuellt($name)) {
            $fehler[] = 'Bitte geben Sie Ihren Namen an.';
        }

        if ($fehler !== []) {
            return $fehler;
        }

        // §4: „Eine Erklärung ist einmalig." Der eindeutige Schlüssel entscheidet, nicht eine
        // vorgelagerte Abfrage — zwei gleichzeitige Klicks kämen sonst beide durch.
        $neu = $this->freigaben()->erklaeren(
            $projektId,
            KundenFreigaben::INHALTE,
            $benutzerId,
            $name,
            $ip,
        );

        if (!$neu) {
            return ['Diese Freigabe liegt bereits vor.'];
        }

        $this->aufgaben()->abschliessen((string) $aufgabe['id'], null, $benutzerId);

        $this->audit()->schreiben(
            aktion: 'faktenfreigabe_erteilt',
            objektart: 'project',
            objektId: $projektId,
            akteurBenutzerId: $benutzerId,
            organisationId: $this->bereich->organisationId,
            grund: 'Verbindliche Freigabe von Fakten und Umfang durch ' . $name,
            ip: $ip,
        );

        // §10, Zeile „Faktenfreigabe erfolgt (an beide)". Sie fehlte bis zum 02.08.2026 —
        // der Lieferkorridor begann unbemerkt, und genau seine Länge steht in der Mail.
        //
        // **Vor dem Zustandswechsel**, aus demselben Grund wie bei der Angebotsannahme: Die
        // Erklärung steht und wird nicht zurückgenommen. Ob der Zustand nachzieht, ändert an
        // der Freigabe nichts, darf aber die Nachricht darüber nicht kosten.
        $this->freigabemailsSenden($projektId, $name);

        // §5.1a: `briefing` → `produktion`, ausgelöst vom Kunden, `reason` Pflicht —
        // ab hier läuft der Lieferkorridor. Dass der Zustand nicht nachzog, sieht der Admin
        // am fehlenden Wechsel — nicht der Kunde an einem Fehler, den er nicht verursacht hat.
        $this->wechsel()->wechseln(
            $projektId,
            $this->bereich->organisationId,
            Projektstatus::PRODUKTION,
            Projektstatus::KUNDE,
            $benutzerId,
            'Faktenfreigabe durch ' . $name,
            $ip,
        );

        return [];
    }

    /**
     * Die zwei Mails zur Faktenfreigabe — §10 sagt „an beide".
     *
     * Der Lieferkorridor kommt aus dem **angenommenen Angebot**, nicht aus einer Konstante:
     * §4 macht `delivery_days_min` und `delivery_days_max` zu Pflichtfeldern des Angebots,
     * und §4c legt sie je Paket fest. Fehlt das Angebot, entfällt der Satz mit den Tagen —
     * eine erfundene Zahl in einer Fertigstellungszusage wäre der schlimmere Fehler.
     */
    private function freigabemailsSenden(string $projektId, string $name): void
    {
        $projekt = (new KundenProjekte($this->bereich, $this->pdo))->finden($projektId);

        if ($projekt === null) {
            return;
        }

        $angebot = (new KundenAngebote($this->bereich, $this->pdo))->aktuelles();
        $mail = new Projektmail($this->mail, $this->pdo);

        $mail->anKunden(
            $projekt,
            Mailtexte::FREIGABE_BETREFF,
            Mailtexte::freigabe(
                self::tage($angebot, 'delivery_days_min'),
                self::tage($angebot, 'delivery_days_max'),
            ),
        );

        $mail->anBetreuer(
            $projekt,
            Mailtexte::FREIGABE_BETREFF,
            Mailtexte::freigabeIntern($name, $projektId),
        );
    }

    /** @param array<string,mixed>|null $angebot */
    private static function tage(?array $angebot, string $feld): ?int
    {
        $wert = $angebot[$feld] ?? null;

        return is_numeric($wert) ? (int) $wert : null;
    }

    private function aufgaben(): KundenAufgaben
    {
        return $this->aufgaben ?? new KundenAufgaben($this->bereich, $this->pdo);
    }

    private function freigaben(): KundenFreigaben
    {
        return $this->freigaben ?? new KundenFreigaben($this->bereich, $this->pdo);
    }

    private function wechsel(): Projektwechsel
    {
        return $this->wechsel ?? new Projektwechsel(pdo: $this->pdo);
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll($this->pdo);
    }
}
