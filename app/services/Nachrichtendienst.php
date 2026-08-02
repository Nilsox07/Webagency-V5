<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\Admin\AdminAufgaben;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminProjekte;
use Sartu\Helpers\Validate;

/**
 * Die Antwort auf eine Kundennachricht — Portal-Lastenheft §8.9, §9.1 und §10.
 *
 * ## Warum es diesen Dienst erst jetzt gibt
 *
 * §9.1 nennt `/admin/nachrichten` mit Antwortfeld, §10 nennt die Mail `Antwort auf Ihre
 * Nachricht`. Gebaut war bis zum 02.08.2026 nur die Kundenseite: Der Kunde konnte schreiben,
 * `AdminAufgaben::nachrichtBeantworten()` stand bereit — und **kein Aufrufer** rief sie.
 *
 * §8.9 verspricht dem Kunden: „Wir antworten schriftlich, in der Regel innerhalb eines
 * Werktags." Ein Versprechen ohne Weg dahin ist keines.
 *
 * ## Was hier NICHT passiert
 *
 * **Kein Audit-Eintrag.** §3 zählt auf, was protokolliert wird: Anmeldung, fehlgeschlagene
 * Anmeldung, Status- und Zahlungswechsel, Rechteänderung, Löschung. Eine Antwort auf eine
 * Frage ist keines davon, und die Antwort selbst steht mit Zeitpunkt in
 * `support_messages.answered_at`. Ein erfundenes Ereignis wäre eine Zeile im Nachweis, die
 * kein Dokument verlangt.
 *
 * ## Die Sperre gegen die zweite Mail
 *
 * `answered_at IS NULL` steckt in der `WHERE`-Bedingung des `UPDATE`. Zwei gleichzeitige
 * Klicks können nicht beide durchkommen, und die Mail hängt am Rückgabewert — nicht an einer
 * vorgelagerten Abfrage, die zwischen Lesen und Schreiben veralten kann.
 */
final class Nachrichtendienst
{
    /** §8.9 verlangt vom Kunden mindestens 10 Zeichen. Für die Antwort gilt dasselbe Maß. */
    public const MINDESTLAENGE = 10;

    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?AdminAufgaben $aufgaben = null,
        private readonly ?Versender $mail = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * @return list<string> leer bei Erfolg
     */
    public function beantworten(string $nachrichtId, string $antwort): array
    {
        $nachricht = $this->aufgaben()->nachricht($nachrichtId);

        if ($nachricht === null) {
            return ['Diese Nachricht gibt es nicht.'];
        }

        if ($nachricht['answered_at'] !== null) {
            return ['Diese Nachricht ist bereits beantwortet.'];
        }

        $antwort = trim($antwort);

        if (!Validate::gefuellt($antwort) || mb_strlen($antwort) < self::MINDESTLAENGE) {
            return ['Bitte schreiben Sie eine Antwort von mindestens '
                . self::MINDESTLAENGE . ' Zeichen.'];
        }

        if (!$this->aufgaben()->nachrichtBeantworten($nachrichtId, $antwort)) {
            return ['Diese Nachricht ist bereits beantwortet.'];
        }

        $this->mailSenden($nachricht, $antwort);

        return [];
    }

    /**
     * §10, Zeile „Antwort auf Nachricht".
     *
     * **Ohne Projekt geht sie trotzdem raus.** §4 lässt `support_messages.project_id`
     * ausdrücklich leer — eine Frage kann vor dem ersten Projekt kommen. `Projektmail`
     * braucht nur `organization_id`; die Fußzeile mit dem Projekttitel entfällt dann, weil
     * es keinen gibt.
     *
     * @param array<string,mixed> $nachricht
     */
    private function mailSenden(array $nachricht, string $antwort): void
    {
        $projektId = $nachricht['project_id'] ?? null;
        $projekt = is_string($projektId) && $projektId !== ''
            ? (new AdminProjekte($this->nachweis, $this->pdo))->finden($projektId)
            : null;

        (new Projektmail($this->mail, $this->pdo))->anKunden(
            $projekt ?? ['organization_id' => (string) $nachricht['organization_id'], 'title' => ''],
            Mailtexte::ANTWORT_BETREFF,
            Mailtexte::antwort($antwort),
        );
    }

    private function aufgaben(): AdminAufgaben
    {
        return $this->aufgaben ?? new AdminAufgaben($this->nachweis, $this->pdo);
    }
}
