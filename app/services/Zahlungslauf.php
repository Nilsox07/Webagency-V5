<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\AuditProtokoll;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\Faelligkeiten;
use Sartu\Helpers\Format;

/**
 * Der tägliche Lauf für Fristen und Erinnerungen — Portal-Lastenheft §5.3 und §5.3a.
 *
 * ## Drei Aufgaben, die getrennt laufen
 *
 * **Überfälligkeit** (§5.3): `ueberfaellig` wird gesetzt, wenn `due_date < heute` und
 * `paid_cents < gross_cents`. Zustandsänderung ohne Mail.
 *
 * **Erinnerung** (§5.3a): eine Mail bei Überschreitung, eine zweite nach sieben Tagen,
 * **danach keine weitere** — „ab hier entscheidet ein Mensch". Mail ohne Zustandsänderung.
 *
 * **Abgelaufene Angebote** (§5.2): `gesendet` → `abgelaufen`, sobald `valid_until` vorbei ist.
 *
 * Sie stehen hier zusammen, weil sie derselbe Lauf sind, und laufen getrennt, weil ein
 * Fehler in der einen die anderen nicht kosten darf.
 *
 * ## Warum zwei Erinnerungsfelder
 *
 * §4: „**Zwei Erinnerungen brauchen zwei Felder** — mit nur einem hätte die zweite Mail ab
 * Tag 7 **jeden Tag** ausgelöst, weil ihre Bedingung dauerhaft wahr bleibt." Gefunden bei
 * der externen Prüfung am 01.08.2026. Testfall 78 prüft genau das.
 *
 * ## Warum der Lauf mit dem Restbetrag rechnet, nicht mit dem Zustand
 *
 * §5.3: „Maßgeblich für die Erinnerung ist der Restbetrag, nicht der Status." Eine angezahlte
 * Rechnung nach Fälligkeit ist `ueberfaellig` **und** teilweise bezahlt — wer auf den Zustand
 * filtert, übersieht einen der beiden Fälle.
 *
 * ## Ohne Akteur, ohne Adminnachweis
 *
 * Der Lauf greift über alle Organisationen und läuft nachts über die Befehlszeile. Er nutzt
 * deshalb `Data\Faelligkeiten` — eine schmale Klasse außerhalb beider Zugriffsschichten. Ein
 * erfundener Adminnachweis wäre die Ausnahme, aus der §3 Regel 2 die typische Datenpanne
 * entstehen sieht.
 */
final class Zahlungslauf
{
    /** §5.3a: die zweite Erinnerung kommt sieben Tage nach der ersten. */
    public const ABSTAND_TAGE = 7;

    public function __construct(
        private readonly ?Faelligkeiten $faelligkeiten = null,
        private readonly ?AuditProtokoll $audit = null,
        private readonly ?Mailversand $mail = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /** @return array{ueberfaellig:int,erinnerung1:int,erinnerung2:int,angebote:int} */
    public function ausfuehren(?\DateTimeImmutable $jetzt = null): array
    {
        $jetzt ??= new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $heute = $jetzt->setTimezone(new \DateTimeZone('Europe/Berlin'))->format('Y-m-d');

        return [
            'ueberfaellig' => $this->ueberfaelligSetzen($heute),
            'erinnerung1'  => $this->ersteErinnerungen($heute),
            'erinnerung2'  => $this->zweiteErinnerungen($jetzt),
            'angebote'     => $this->daten()->abgelaufeneAngeboteSetzen($heute),
        ];
    }

    /** §5.3, Testfall 15. */
    private function ueberfaelligSetzen(string $heute): int
    {
        $rechnungen = $this->daten()->ueberfaellige($heute);

        foreach ($rechnungen as $rechnung) {
            $vorher = (string) $rechnung['status'];

            $this->daten()->zustandSetzen((string) $rechnung['id'], Zahlungsstatus::UEBERFAELLIG);

            // §3: Ein Wechsel an Geld und Fristen erzeugt ein Audit-Ereignis. Akteur ist
            // hier der Lauf und kein Mensch — das steht als Grund dabei, statt einen
            // Benutzer zu erfinden.
            $this->audit()->schreiben(
                aktion: 'zahlungsstatus_geaendert',
                objektart: 'invoice',
                objektId: (string) $rechnung['id'],
                alterWert: $vorher,
                neuerWert: Zahlungsstatus::UEBERFAELLIG,
                grund: 'Zahlungsziel ' . Format::datum((string) $rechnung['due_date'])
                    . ' überschritten, Restbetrag offen (täglicher Lauf, §5.3)',
            );
        }

        return count($rechnungen);
    }

    private function ersteErinnerungen(string $heute): int
    {
        $gesendet = 0;

        foreach ($this->daten()->ersteErinnerungFaellig($heute) as $rechnung) {
            $rest = Zahlungsstatus::restbetrag((int) $rechnung['paid_cents'], (int) $rechnung['gross_cents']);

            $this->kundenmail(
                $rechnung,
                'Erinnerung: Rechnung ' . (string) $rechnung['number'] . ' ist fällig',
                'Die Rechnung ' . (string) $rechnung['number'] . ' über ' . Format::euro($rest)
                . ' war am ' . Format::datum((string) $rechnung['due_date']) . " fällig. Sie können\n"
                . "direkt in Ihrem Bereich bezahlen. Haben Sie bereits überwiesen, ist diese\n"
                . "Nachricht gegenstandslos.\n",
            );

            // Vermerkt wird auch dann, wenn die Mail scheiterte: Sonst läuft der Versuch am
            // nächsten Tag erneut, und aus einer vorübergehenden Störung wird eine Serie.
            $this->daten()->ersteErinnerungVermerken((string) $rechnung['id']);
            ++$gesendet;
        }

        return $gesendet;
    }

    private function zweiteErinnerungen(\DateTimeImmutable $jetzt): int
    {
        $grenze = $jetzt->modify('-' . self::ABSTAND_TAGE . ' days')->format('Y-m-d H:i:s');
        $gesendet = 0;

        foreach ($this->daten()->zweiteErinnerungFaellig($grenze) as $rechnung) {
            $rest = Zahlungsstatus::restbetrag((int) $rechnung['paid_cents'], (int) $rechnung['gross_cents']);

            $this->kundenmail(
                $rechnung,
                'Zweite Erinnerung: Rechnung ' . (string) $rechnung['number'],
                'Die Rechnung ' . (string) $rechnung['number'] . ' über ' . Format::euro($rest)
                . ' war am ' . Format::datum((string) $rechnung['due_date']) . " fällig. Sie können\n"
                . "direkt in Ihrem Bereich bezahlen. Haben Sie bereits überwiesen, ist diese\n"
                . "Nachricht gegenstandslos.\n\n"
                . "Bitte melden Sie sich bei uns, wenn etwas unklar ist.\n",
            );

            // §5.3a: „parallel Hinweis an den Admin".
            $this->adminhinweis($rechnung, $rest);

            $this->daten()->zweiteErinnerungVermerken((string) $rechnung['id']);
            ++$gesendet;
        }

        return $gesendet;
    }

    /** @param array<string,mixed> $rechnung */
    private function kundenmail(array $rechnung, string $betreff, string $kern): void
    {
        $adresse = $rechnung['contact_email'] ?? null;

        if (!is_string($adresse) || trim($adresse) === '') {
            return;
        }

        try {
            $this->mailversand()->senden(
                trim($adresse),
                $betreff,
                "Guten Tag,\n\n" . $kern . "\nFreundliche Grüße\nSARTU\n",
            );
        } catch (\Throwable) {
            // Siehe oben: der Vermerk wird trotzdem gesetzt.
        }
    }

    /** @param array<string,mixed> $rechnung */
    private function adminhinweis(array $rechnung, int $rest): void
    {
        $empfaenger = $this->benachrichtigungsadresse();

        if ($empfaenger === null) {
            return;
        }

        try {
            $this->mailversand()->senden(
                $empfaenger,
                'Zweite Erinnerung verschickt: ' . (string) $rechnung['number'],
                "Guten Tag,\n\nzur Rechnung " . (string) $rechnung['number'] . ' sind '
                . Format::euro($rest) . " offen. Die zweite Erinnerung ist raus.\n"
                . "Ab hier entscheidet ein Mensch — automatisch geht nichts weiter.\n\n"
                . "Freundliche Grüße\nSARTU\n",
            );
        } catch (\Throwable) {
        }
    }

    private function benachrichtigungsadresse(): ?string
    {
        try {
            $daten = (new BetreiberdatenSpeicher($this->pdo))->lesen();
        } catch (\Throwable) {
            return null;
        }

        $adresse = $daten['benachrichtigung_email'] ?? null;

        return is_string($adresse) && trim($adresse) !== '' ? trim($adresse) : null;
    }

    private function daten(): Faelligkeiten
    {
        return $this->faelligkeiten ?? new Faelligkeiten($this->pdo);
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll($this->pdo);
    }

    private function mailversand(): Mailversand
    {
        return $this->mail ?? new Mailversand();
    }
}
