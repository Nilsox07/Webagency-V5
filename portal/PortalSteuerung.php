<?php

declare(strict_types=1);

namespace Sartu\Portal;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\Customer\KundenAktivitaet;
use Sartu\Data\Customer\KundenAngebote;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenAufgaben;
use Sartu\Data\Customer\KundenFreigaben;
use Sartu\Data\Customer\KundenProjekte;
use Sartu\Data\Customer\KundenRechnungen;
use Sartu\Helpers\Format;
use Sartu\Services\KundenAnmeldung;
use Sartu\Services\Preise;
use Sartu\Services\Projektstatus;
use Sartu\Services\Zahlungsstatus;
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
 * §8 nennt neun Navigationspunkte. Nach A2 gibt es sechs davon: Übersicht, Angebot,
 * Aufgaben, Rechnungen und Hilfe. `Vorschau` und `Domain` entstehen in A3, `Inhalte` in B,
 * `Vertrag` mit den freigegebenen Rechtstexten ebenfalls.
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

        $offeneAufgaben = (new KundenAufgaben($bereich))->offeneGesamt();
        $offeneRechnung = (new KundenRechnungen($bereich))->aeltesteOffene();
        $freigabeOffen  = self::freigabeOffen($bereich, $projekt);

        return Antwort::html(Ansicht::seite('portal', 'portal-uebersicht', [
            'titel'        => 'Übersicht',
            'angemeldet'   => true,
            'projekt'      => $projekt,
            'angebot'      => $angebot,
            'offenePunkte' => self::offenePunkte($offeneAufgaben, $offeneRechnung, $freigabeOffen),
            'aktivitaet'   => (new KundenAktivitaet($bereich))->letzte(),
            'naechsterSchritt' => self::naechsterSchritt($projekt, $angebot, $offeneAufgaben, $offeneRechnung),
        ]));
    }

    /**
     * @param array<string,string> $parameter
     * @param list<string> $fehler
     */
    public function angebot(array $parameter = [], array $fehler = []): Antwort
    {
        $bereich = KundenBereich::ausSitzung();
        $angebot = (new KundenAngebote($bereich))->aktuelles();

        return Antwort::html(Ansicht::seite('portal', 'portal-angebot', [
            'titel'      => 'Ihr Angebot',
            'angemeldet' => true,
            'angebot'    => $angebot,
            'preise'     => $angebot === null ? null : Preise::zeile((string) $angebot['package']),
            'annehmbar'  => \Sartu\Services\Angebotsannahme::annehmbar($angebot),
            'fehler'     => $fehler,
        ]));
    }

    // ------------------------------------------------------------------ §7 Willkommen

    /** §8.6: `/vertrag` — die Rechtstexte mit `audience = kunde`. */
    public function vertrag(array $parameter = []): Antwort
    {
        return Antwort::html(Ansicht::seite('portal', 'portal-vertrag', [
            'titel'      => 'Vertrag',
            'angemeldet' => true,
            'texte'      => (new \Sartu\Data\RechtstexteSpeicher())->alleFuerKunden(),
        ]));
    }

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
     * Steht eine Freigabe des Kunden aus? — die dritte Zeile aus §8.1 Block 3.
     *
     * **Gemeint ist die Abnahme, nicht die Faktenfreigabe.** Die Faktenfreigabe ist selbst
     * eine Aufgabe (`tasks.kind = 'freigabe'`, §8.3) und steht damit bereits in der ersten
     * Zeile — sie ein zweites Mal aufzuführen hiesse, denselben Punkt doppelt zu zaehlen.
     * Die Abnahme ist keine Aufgabe: Sie haengt am Projektzustand `abnahme` (§5.1a) und
     * waere ohne diese Zeile nirgends im Cockpit zu sehen.
     *
     * @param array<string,mixed>|null $projekt
     */
    private static function freigabeOffen(KundenBereich $bereich, ?array $projekt): bool
    {
        if ($projekt === null || (string) $projekt['status'] !== Projektstatus::ABNAHME) {
            return false;
        }

        return (new KundenFreigaben($bereich))
            ->finden((string) $projekt['id'], KundenFreigaben::ABNAHME) === null;
    }

    /**
     * §8.1 Block 3 — „hoechstens drei Zeilen, jeweils mit Link".
     *
     * Der Wortlaut der ersten beiden Zeilen ist dort gebunden: `{n} offene Aufgaben` und
     * `Rechnung {Nummer} — zahlbar bis {Datum}`. Bei genau einer Aufgabe steht `Eine offene
     * Aufgabe` — „1 offene Aufgaben" waere kein deutscher Satz, und die Vorlage meint die
     * Zahl, nicht die Ziffer.
     *
     * **Der Hinweis bei knapper Frist** (drei Tage, `Zahlungsstatus::KNAPP_TAGE`) kommt als
     * eigener Satz **hinter** die gebundene Zeile, nicht statt ihr. Nummer und Datum bleiben
     * so an derselben Stelle stehen, egal wie nah die Frist ist.
     *
     * @param array<string,mixed>|null $offeneRechnung
     *
     * @return list<array{text:string,zusatz:?string,ziel:string}>
     */
    private static function offenePunkte(
        int $offeneAufgaben,
        ?array $offeneRechnung,
        bool $freigabeOffen,
    ): array {
        $punkte = [];

        if ($offeneAufgaben > 0) {
            $punkte[] = [
                'text'   => $offeneAufgaben === 1
                    ? 'Eine offene Aufgabe'
                    : $offeneAufgaben . ' offene Aufgaben',
                'zusatz' => null,
                'ziel'   => '/portal/aufgaben',
            ];
        }

        if ($offeneRechnung !== null) {
            $punkte[] = [
                'text'   => 'Rechnung ' . (string) $offeneRechnung['number']
                    . ' — zahlbar bis ' . Format::datum(
                        is_string($offeneRechnung['due_date'] ?? null) ? $offeneRechnung['due_date'] : null
                    ),
                'zusatz' => Zahlungsstatus::fristKnapp($offeneRechnung)
                    ? Zahlungsstatus::knapphinweis()
                    : null,
                'ziel'   => '/portal/rechnungen',
            ];
        }

        if ($freigabeOffen) {
            $punkte[] = [
                'text'   => 'Ihre Abnahme steht noch aus',
                'zusatz' => null,
                'ziel'   => '/portal/vorschau',
            ];
        }

        return $punkte;
    }

    /**
     * Block 1 aus §8.1 — der eine nächste Schritt.
     *
     * §5.6 leitet ihn aus dem Zustand ab, solange `next_step_text` leer ist.
     *
     * @param array<string,mixed>|null $projekt
     * @param array<string,mixed>|null $angebot
     * @param array<string,mixed>|null $offeneRechnung
     *
     * @return array{text:string,ziel:?string,knopf:?string}
     */
    private static function naechsterSchritt(
        ?array $projekt,
        ?array $angebot,
        int $offeneAufgaben = 0,
        ?array $offeneRechnung = null,
    ): array {
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

        if ($offeneRechnung !== null) {
            return [
                'text'  => 'Ihre Rechnung ' . (string) $offeneRechnung['number'] . ' ist offen.',
                'ziel'  => '/portal/rechnungen',
                'knopf' => 'Rechnung ansehen',
            ];
        }

        if ($offeneAufgaben > 0) {
            return [
                'text'  => $offeneAufgaben === 1
                    ? 'Eine Aufgabe wartet auf Sie.'
                    : $offeneAufgaben . ' Aufgaben warten auf Sie.',
                'ziel'  => '/portal/aufgaben',
                'knopf' => 'Aufgaben ansehen',
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
