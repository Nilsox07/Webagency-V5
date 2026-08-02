<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\Kontaktadresse;
use Sartu\Helpers\Validate;

/**
 * Der eine Weg, auf dem eine Projektmail rausgeht — Portal-Lastenheft §10.
 *
 * ## Warum das eine Klasse ist und keine Methode je Dienst
 *
 * §10 schreibt **allen** Mails denselben Rahmen vor: Anrede, Grußformel, „Fußzeile mit
 * Impressumsangaben und dem Hinweis `Diese Nachricht bezieht sich auf Ihr Projekt
 * „{Projekttitel}".`" Stand der Rahmen in jedem Dienst noch einmal, wäre er beim vierten
 * Dienst schon nicht mehr derselbe — und die Fußzeile fehlte im `Rechnungsdienst`
 * tatsächlich bereits.
 *
 * ## Eine gescheiterte Mail nimmt nie einen Vorgang zurück
 *
 * Jede Methode gibt `false` zurück und wirft nicht. Der Onlinegang ist vollzogen, auch wenn
 * der Mailserver gerade nicht erreichbar ist; ein geworfener Fehler würde den Wechsel
 * scheinbar rückgängig machen, den die Datenbank längst geschrieben hat.
 *
 * ## Zwei Empfänger, zwei Regeln
 *
 * Der Kunde bekommt die Mail an `organizations.contact_email`. Der Betreuer bekommt sie an
 * `operator_settings.benachrichtigung_email` — ist die leer, unterbleibt **nur** diese eine
 * Benachrichtigung (Entscheidung vom 02.08.2026). Kein erfundener Vorgabewert.
 */
final class Projektmail
{
    public function __construct(
        private readonly ?Mailversand $versand = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * @param array<string,mixed> $projekt braucht `organization_id` und `title`
     * @return bool `false`, wenn keine Adresse hinterlegt ist oder der Versand scheitert
     */
    public function anKunden(array $projekt, string $betreff, string $kern): bool
    {
        $empfaenger = $this->kundenadresse((string) ($projekt['organization_id'] ?? ''));

        if ($empfaenger === null) {
            return false;
        }

        return $this->raus($empfaenger, $betreff, "Guten Tag,\n\n" . $kern . $this->fuss($projekt));
    }

    /**
     * Die interne Kurzmeldung — §10, Zeilen „(an Admin)".
     *
     * @param array<string,mixed> $projekt
     */
    public function anBetreuer(array $projekt, string $betreff, string $kern): bool
    {
        $empfaenger = (new Anfragebenachrichtigung($this->versand, $this->pdo))->empfaenger();

        if ($empfaenger === null) {
            return false;
        }

        return $this->raus($empfaenger, $betreff, "Guten Tag,\n\n" . $kern . $this->fuss($projekt));
    }

    // ------------------------------------------------------------------ intern

    private function raus(string $empfaenger, string $betreff, string $text): bool
    {
        try {
            ($this->versand ?? new Mailversand())->senden($empfaenger, $betreff, $text);
        } catch (\Throwable) {
            return false;
        }

        return true;
    }

    /** @param array<string,mixed> $projekt */
    private function fuss(array $projekt): string
    {
        $titel = trim((string) ($projekt['title'] ?? ''));

        $fuss = "\nFreundliche Grüße\nSARTU\n";

        $anschrift = $this->anschrift();

        if ($anschrift !== '') {
            $fuss .= "\n" . $anschrift . "\n";
        }

        if ($titel !== '') {
            $fuss .= "\nDiese Nachricht bezieht sich auf Ihr Projekt „" . $titel . "\".\n";
        }

        return $fuss;
    }

    /**
     * Die Impressumsangaben aus den Betreiberdaten — nie aus dem Quelltext.
     *
     * Fehlt die Zeile (etwa in einem Test ohne Ersteinrichtung), bleibt die Fußzeile ohne
     * Anschrift. Eine erfundene Anschrift wäre der schlimmere Fehler.
     */
    private function anschrift(): string
    {
        try {
            $daten = (new BetreiberdatenSpeicher($this->pdo))->lesen();
        } catch (\Throwable) {
            return '';
        }

        if ($daten === null) {
            return '';
        }

        $zeilen = array_filter([
            trim((string) ($daten['firmenname'] ?? '')),
            trim((string) ($daten['strasse'] ?? '')),
            trim((string) ($daten['plz'] ?? '') . ' ' . (string) ($daten['ort'] ?? '')),
            trim((string) ($daten['email'] ?? '')),
        ], static fn (string $zeile) => $zeile !== '');

        return implode("\n", $zeilen);
    }

    private function kundenadresse(string $organisationId): ?string
    {
        $adresse = (new Kontaktadresse($this->pdo))->finden($organisationId);

        return $adresse !== null && Validate::email($adresse) ? $adresse : null;
    }
}
