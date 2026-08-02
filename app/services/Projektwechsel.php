<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\AuditProtokoll;
use Sartu\Data\ProjektZustand;
use Sartu\Helpers\Validate;

/**
 * Der eine Weg, auf dem sich `projects.status` ändert — Portal-Lastenheft §5.1a.
 *
 * > „Für jeden Wechsel gilt **ohne Ausnahme**: Prüfung serverseitig gegen **diese** Tabelle ·
 * > Audit-Ereignis mit `old_value`, `new_value` und handelndem Benutzer · bei Wechseln, die
 * > Geld oder Fristen betreffen, zusätzlich `reason`."
 *
 * **Ein Weg, nicht mehrere.** Die Rechnung, die Faktenfreigabe und der Onlinegang rufen alle
 * hier an. Jeder zusätzliche Ort, an dem `status` geschrieben wird, ist ein Ort ohne
 * Übergangsprüfung — und §5.1a nennt genau das als teuerste Fehlerstelle: „Produktion
 * startet vor Zahlungseingang."
 *
 * ## Drei Prüfungen in dieser Reihenfolge
 *
 * | # | Prüfung | Warum sie vor der nächsten steht |
 * |---|---|---|
 * | 1 | Ist das Paar in der Tabelle? | Ein erfundenes Ziel wird abgewiesen, **bevor** irgendetwas geschrieben wird |
 * | 2 | Darf dieser Akteur es auslösen? | §5.1a trennt Kunde und Admin. Ein Kunde, der `zahlung_offen → briefing` schickt, bestätigt seinen eigenen Zahlungseingang |
 * | 3 | Liegt bei Geld und Fristen ein Grund vor? | §12: „Ohne Grundlagentext lässt sich keine dieser Änderungen speichern" |
 *
 * **Kein Teileffekt.** Scheitert eine der drei, wird nichts geschrieben — kein Statuswert,
 * kein Audit-Ereignis, keine halbe Änderung (Testfall 60).
 *
 * ## Die Pause
 *
 * `paused_from_status` wird beim Anhalten gesetzt und beim Fortsetzen als **Ziel gelesen**.
 * Der Aufrufer kann kein Ziel übergeben — `fortsetzen()` hat dafür keinen Parameter. Genau
 * das prüft Testfall 62.
 *
 * ## Der Mandantenfilter
 *
 * `$organisationId` ist auf jedem Aufruf Pflicht und geht bis in die `WHERE`-Bedingung des
 * `UPDATE` (`Data\ProjektZustand`). Ein Kundendienst übergibt den Wert aus der Sitzung, ein
 * Admindienst den des Projekts, das er über seine eigene Schicht gelesen hat. Es gibt keine
 * Fassung ohne Filter — auch nicht für den Admin.
 */
final class Projektwechsel
{
    public function __construct(
        private readonly ?ProjektZustand $zustand = null,
        private readonly ?AuditProtokoll $audit = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * Führt einen Wechsel aus.
     *
     * @param string      $akteur    `Projektstatus::KUNDE` oder `::ADMIN`
     * @param string|null $benutzerId wer handelt — für das Audit
     *
     * @return string|null Fehlermeldung, oder `null` bei Erfolg.
     */
    public function wechseln(
        string $projektId,
        string $organisationId,
        string $ziel,
        string $akteur,
        ?string $benutzerId,
        ?string $grund = null,
        ?string $ip = null,
    ): ?string {
        $projekt = $this->zustand()->finden($projektId, $organisationId);

        if ($projekt === null) {
            return 'Dieses Projekt gibt es nicht.';
        }

        $von = (string) $projekt['status'];
        $uebergang = Projektstatus::uebergang($von, $ziel);

        // 1 — steht das Paar in der Tabelle?
        if ($uebergang === null) {
            return 'Dieser Schritt ist an dieser Stelle nicht vorgesehen.';
        }

        // 2 — darf dieser Akteur ihn auslösen?
        if ($uebergang['wer'] !== $akteur) {
            return 'Dieser Schritt liegt nicht bei Ihnen.';
        }

        // 3 — Geld oder Frist? Dann ist der Grund Pflicht (§12).
        if ($uebergang['grundPflicht'] && !Validate::gefuellt($grund)) {
            return 'Bitte halten Sie fest, worauf sich dieser Schritt stützt.';
        }

        $geschrieben = $this->zustand()->setzen(
            $projektId,
            $organisationId,
            $von,
            $ziel,
            $ziel === Projektstatus::PAUSIERT ? $von : null,
        );

        if (!$geschrieben) {
            // Zwischen Lesen und Schreiben hat jemand anders gewechselt. Kein Audit-Ereignis
            // fuer einen Wechsel, der nicht stattgefunden hat.
            return 'Der Stand hat sich inzwischen geändert. Bitte laden Sie die Seite neu.';
        }

        $this->audit()->schreiben(
            aktion: 'projektstatus_geaendert',
            objektart: 'project',
            objektId: $projektId,
            akteurBenutzerId: $benutzerId,
            organisationId: $organisationId,
            alterWert: $von,
            neuerWert: $ziel,
            grund: $grund === null ? null : trim($grund),
            detail: ['ereignis' => $uebergang['ereignis']],
            ip: $ip,
        );

        return null;
    }

    /**
     * Hält ein Projekt an — §5.1a, `reason` ist Pflicht und **wird dem Kunden angezeigt**.
     */
    public function pausieren(
        string $projektId,
        string $organisationId,
        string $grund,
        ?string $benutzerId,
        ?string $ip,
    ): ?string {
        $grund = trim($grund);

        if ($grund === '') {
            return 'Bitte halten Sie fest, warum das Projekt pausiert. Der Kunde sieht diesen Text.';
        }

        $fehler = $this->wechseln(
            $projektId,
            $organisationId,
            Projektstatus::PAUSIERT,
            Projektstatus::ADMIN,
            $benutzerId,
            $grund,
            $ip,
        );

        if ($fehler !== null) {
            return $fehler;
        }

        $this->zustand()->pauseGrundSetzen($projektId, $organisationId, $grund);

        return null;
    }

    /**
     * Setzt fort — auf den gespeicherten Herkunftsstatus.
     *
     * **Ohne Zielparameter.** §5.1a: „zurück auf `paused_from_status`, nicht auf einen frei
     * gewählten Wert." Ein Ziel, das sich übergeben lässt, ist ein Ziel, das sich
     * mitschicken lässt (Testfall 62).
     */
    public function fortsetzen(
        string $projektId,
        string $organisationId,
        ?string $benutzerId,
        ?string $ip,
    ): ?string {
        $projekt = $this->zustand()->finden($projektId, $organisationId);

        if ($projekt === null) {
            return 'Dieses Projekt gibt es nicht.';
        }

        if ((string) $projekt['status'] !== Projektstatus::PAUSIERT) {
            return 'Dieses Projekt pausiert nicht.';
        }

        $herkunft = $projekt['paused_from_status'] ?? null;

        if (!is_string($herkunft) || $herkunft === '') {
            // Die Prüfbedingung auf `projects` schliesst das aus. Steht es doch so da, ist
            // etwas an der Zeile kaputt — dann wird geraten oder abgebrochen, und geraten
            // wird nicht.
            return 'Zu dieser Pause fehlt der Ausgangszustand. Bitte melden Sie das.';
        }

        $this->zustand()->setzen($projektId, $organisationId, Projektstatus::PAUSIERT, $herkunft, null);
        $this->zustand()->pauseGrundSetzen($projektId, $organisationId, null);

        $this->audit()->schreiben(
            aktion: 'projektstatus_geaendert',
            objektart: 'project',
            objektId: $projektId,
            akteurBenutzerId: $benutzerId,
            organisationId: $organisationId,
            alterWert: Projektstatus::PAUSIERT,
            neuerWert: $herkunft,
            grund: 'Fortsetzung auf den gespeicherten Ausgangszustand',
            ip: $ip,
        );

        return null;
    }

    private function zustand(): ProjektZustand
    {
        return $this->zustand ?? new ProjektZustand($this->pdo);
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll($this->pdo);
    }
}
