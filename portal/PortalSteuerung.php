<?php

declare(strict_types=1);

namespace Sartu\Portal;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Customer\KundenAngebote;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenProjekte;
use Sartu\Services\KundenAnmeldung;
use Sartu\Services\Preise;
use Sartu\Services\Projektstatus;
use Sartu\Sitzung;

/**
 * Der Kundenbereich — Portal-Lastenheft §8.
 *
 * **Jede Abfrage geht über `KundenBereich::ausSitzung()`.** Die Klasse hat genau eine Fabrik
 * ohne Parameter; es gibt keinen Weg, die Organisation von aussen zu setzen (§3 Regel 1).
 * Dass hier keine Kennung aus der Adresse gelesen wird, ist deshalb keine Disziplin, sondern
 * eine Signatur.
 *
 * ## Was in A1 sichtbar ist — und was nicht
 *
 * §8 nennt neun Navigationspunkte. In dieser Etappe gibt es `projects` und `offers`, mehr
 * nicht: `invoices`, `tasks` und `feedback_rounds` entstehen in A2 und A3
 * (`REIHENFOLGE.md`). Die Navigation zeigt deshalb **Übersicht** und **Angebot**.
 *
 * §8 sagt „Menüpunkte, für die es noch nichts gibt, werden angezeigt und erklärt". §0.3b
 * sagt „Was nicht existiert, ist nicht sichtbar — auch nicht ausgegraut". Beide haben eine
 * Begründung, und sie meinen Verschiedenes:
 *
 * | Regel | Gemeint ist |
 * |---|---|
 * | §8 | ein Bereich, den es **gibt**, in dem für **diesen Kunden** noch nichts steht — Leerzustand |
 * | §0.3b | eine Funktion, die es **nicht gibt** — kein Menüpunkt |
 *
 * `Rechnungen` gehört heute in die zweite Zeile und morgen in die erste. Es erscheint,
 * sobald A2 es baut.
 */
final class PortalSteuerung
{
    /** @param array<string,string> $parameter */
    public function uebersicht(array $parameter = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $projekt = (new KundenProjekte($bereich))->aktuelles();
        $angebot = (new KundenAngebote($bereich))->aktuelles();

        return Antwort::html(Ansicht::seite('portal', 'portal-uebersicht', [
            'titel'        => 'Übersicht',
            'angemeldet'   => true,
            'projekt'      => $projekt,
            'angebot'      => $angebot,
            'naechsterSchritt' => self::naechsterSchritt($projekt, $angebot),
        ]));
    }

    /** @param array<string,string> $parameter */
    public function angebot(array $parameter = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $angebot = (new KundenAngebote($bereich))->aktuelles();

        return Antwort::html(Ansicht::seite('portal', 'portal-angebot', [
            'titel'      => 'Ihr Angebot',
            'angemeldet' => true,
            'angebot'    => $angebot,
            'preise'     => $angebot === null ? null : Preise::zeile((string) $angebot['package']),
        ]));
    }

    // ------------------------------------------------------------------ §7 Willkommen

    /** @param array<string,string> $parameter */
    public function willkommen(array $parameter = []): Antwort
    {
        $nummer = (int) ($parameter['nummer'] ?? '0');

        if ($nummer < 1 || $nummer > 3) {
            return Antwort::nichtGefunden();
        }

        return Antwort::html(Ansicht::seite('portal', 'willkommen-' . $nummer, [
            'titel'      => 'Willkommen',
            'angemeldet' => false,
            'vorname'    => self::vorname(),
        ]));
    }

    /**
     * §7: „Nach dem letzten Bildschirm oder bei ‚Überspringen': `welcome_seen_at` setzen."
     *
     * Ein `POST`, weil sich dabei etwas ändert — und damit die Strecke ohne JavaScript
     * funktioniert, ist es ein normales Formular.
     *
     * @param array<string,string> $parameter
     */
    public function willkommenFertig(array $parameter = []): Antwort
    {
        (new KundenAnmeldung())->willkommenGesehen((string) Sitzung::wert(Sitzung::BENUTZER));

        return Antwort::weiter('/portal', 303);
    }

    // ------------------------------------------------------------------ intern

    /**
     * Block 1 aus §8.1 — der eine nächste Schritt.
     *
     * §5.6 leitet ihn aus dem Zustand ab, solange `next_step_text` leer ist. In A1 gibt es
     * genau zwei Fälle: Ein Angebot liegt bereit, oder es ist nichts zu tun.
     *
     * @param array<string,mixed>|null $projekt
     * @param array<string,mixed>|null $angebot
     *
     * @return array{text:string,ziel:?string,knopf:?string}
     */
    private static function naechsterSchritt(?array $projekt, ?array $angebot): array
    {
        $gesetzt = $projekt['next_step_text'] ?? null;

        if (is_string($gesetzt) && trim($gesetzt) !== '') {
            $ziel = $projekt['next_step_url'] ?? null;

            return [
                'text'  => trim($gesetzt),
                'ziel'  => is_string($ziel) && $ziel !== '' ? $ziel : null,
                'knopf' => is_string($ziel) && $ziel !== '' ? 'Ansehen' : null,
            ];
        }

        if ($angebot !== null && (string) $angebot['status'] === 'gesendet') {
            return [
                'text'  => 'Ihr Angebot liegt bereit. Sehen Sie es sich in Ruhe an.',
                'ziel'  => '/portal/angebot',
                'knopf' => 'Angebot ansehen',
            ];
        }

        // §8.1: „Wenn nichts zu tun ist" — der Wortlaut steht dort.
        return ['text' => 'Nichts zu tun — wir melden uns, sobald etwas ansteht.', 'ziel' => null, 'knopf' => null];
    }

    private static function vorname(): string
    {
        $bereich = KundenBereich::ausSitzung();
        $benutzer = (new \Sartu\Data\Customer\KundenBenutzer($bereich))
            ->finden((string) Sitzung::wert(Sitzung::BENUTZER));

        $vorname = $benutzer['first_name'] ?? null;

        return is_string($vorname) && trim($vorname) !== '' ? trim($vorname) : '';
    }

    /** Für die Ansicht: die sieben Stationen mit ihrem Zustand. */
    public static function stationen(string $status): array
    {
        $aktuelle = Projektstatus::station($status);
        $erreicht = true;
        $stationen = [];

        foreach (Projektstatus::STATIONEN as $station) {
            if ($aktuelle === null) {
                // §8.1: Bei `pausiert` wird KEINE Station markiert.
                $stationen[] = ['name' => $station, 'stand' => 'offen'];
                continue;
            }

            if ($station === $aktuelle) {
                $stationen[] = ['name' => $station, 'stand' => 'hier'];
                $erreicht = false;
                continue;
            }

            $stationen[] = ['name' => $station, 'stand' => $erreicht ? 'erledigt' : 'offen'];
        }

        return $stationen;
    }
}
