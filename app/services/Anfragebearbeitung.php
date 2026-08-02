<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\Admin\AdminAnfragen;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\AuditProtokoll;

/**
 * Was ein Admin mit einer Anfrage tun darf — Portal-Lastenheft §4b.4 und §4b.5.
 *
 * **Warum das nicht in `AdminAnfragen` steht:** Dort liegt SQL, hier liegen die Regeln
 * (§1.3). Und es sind echte Regeln, keine Durchreiche:
 *
 * | Regel | Woher |
 * |---|---|
 * | Ablehnen verkürzt die Löschfrist von 12 auf 6 Monate | §4b.4, §15.1 |
 * | Ablehnen braucht eine Notiz | §4b.5 — „mit Pflichtnotiz" |
 * | Jeder Zustandswechsel erzeugt ein Audit-Ereignis | §3 Regel 9 |
 * | Die Löschung wird protokolliert, **ohne** die gelöschten Inhalte | §4b.4 |
 *
 * ## Die Löschung ist die eine Ausnahme — und sie muss eine bleiben
 *
 * §3 Regel 13 verbietet die harte Löschung fachlicher Daten. §4b.4 nimmt genau diesen einen
 * Fall aus, weil er ein Betroffenenrecht ist. Das Audit-Ereignis hält fest, **dass** gelöscht
 * wurde, und nennt weder Namen noch Adresse noch Antworttexte — sonst wäre die Löschung
 * keine.
 */
final class Anfragebearbeitung
{
    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?AdminAnfragen $anfragen = null,
        private readonly ?AuditProtokoll $audit = null,
    ) {
    }

    /**
     * Zustandswechsel.
     *
     * @param string|null $notiz Pflicht bei `abgelehnt` (§4b.5).
     *
     * @return string|null Fehlermeldung, oder `null` bei Erfolg.
     */
    public function zustandSetzen(string $id, string $zustand, ?string $notiz, ?string $ip): ?string
    {
        $anfrage = $this->anfragen()->finden($id);

        if ($anfrage === null) {
            return 'Diese Anfrage gibt es nicht.';
        }

        if (!in_array($zustand, AdminAnfragen::ZUSTAENDE, true)) {
            return 'Diesen Zustand gibt es nicht.';
        }

        $notiz = $notiz === null ? '' : trim($notiz);

        if ($zustand === 'abgelehnt' && $notiz === '') {
            return 'Bitte halten Sie fest, warum die Anfrage abgelehnt wird.';
        }

        $vorher = (string) $anfrage['status'];

        if ($notiz !== '') {
            $this->anfragen()->notizSetzen($id, $notiz);
        }

        $this->anfragen()->zustandSetzen($id, $zustand);

        // §4b.4: „die kürzere Frist gilt für den engeren Fall." Erst die Ablehnung
        // verkürzt — beim Anlegen gilt die längere.
        $frist = AnfrageService::frist((string) $anfrage['submitted_at'], $zustand);
        $this->anfragen()->loeschfristSetzen($id, $frist);

        $this->audit()->schreiben(
            aktion: 'anfrage_zustand_geaendert',
            objektart: 'lead',
            objektId: $id,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            alterWert: $vorher,
            neuerWert: $zustand,
            // Eine Frist ändert sich — §3 macht `reason` damit zum Pflichtfeld.
            grund: $notiz !== '' ? $notiz : 'Zustandswechsel im Adminbereich',
            detail: ['delete_after' => $frist],
            ip: $ip,
        );

        return null;
    }

    /** @return string|null Fehlermeldung, oder `null` bei Erfolg. */
    public function notizSpeichern(string $id, string $notiz, ?string $ip): ?string
    {
        if ($this->anfragen()->finden($id) === null) {
            return 'Diese Anfrage gibt es nicht.';
        }

        $this->anfragen()->notizSetzen($id, trim($notiz));

        $this->audit()->schreiben(
            aktion: 'anfrage_notiz_geaendert',
            objektart: 'lead',
            objektId: $id,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            ip: $ip,
        );

        return null;
    }

    /**
     * §4b.4 Betroffenenrecht: echtes `DELETE`.
     *
     * Das Audit-Ereignis nennt die Kennung und sonst nichts. Wer hier Firma oder Adresse
     * mitschreibt, hat die Anfrage nicht gelöscht, sondern verschoben.
     */
    public function endgueltigLoeschen(string $id, string $grund, ?string $ip): ?string
    {
        if ($this->anfragen()->finden($id) === null) {
            return 'Diese Anfrage gibt es nicht.';
        }

        $grund = trim($grund);

        if ($grund === '') {
            return 'Bitte halten Sie fest, warum der Datensatz gelöscht wird.';
        }

        $this->anfragen()->endgueltigLoeschen($id);

        $this->audit()->schreiben(
            aktion: 'anfrage_endgueltig_geloescht',
            objektart: 'lead',
            objektId: $id,
            akteurBenutzerId: $this->nachweis->adminBenutzerId,
            grund: $grund,
            ip: $ip,
        );

        return null;
    }

    private function anfragen(): AdminAnfragen
    {
        return $this->anfragen ?? new AdminAnfragen($this->nachweis);
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll();
    }
}
