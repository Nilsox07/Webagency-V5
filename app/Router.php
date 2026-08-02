<?php

declare(strict_types=1);

namespace Sartu;

use Sartu\Data\Admin\AdminNachweis;
use Sartu\Helpers\Csrf;
use Sartu\Helpers\Http;
use Sartu\Services\AnmeldeDienst;
use Sartu\Services\Ersteinrichtung;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Wartungsmodus;

/**
 * Der Dispatcher. Er gibt eine Antwort zurueck, statt sie zu senden — damit laesst er sich
 * im Test aufrufen (siehe Antwort).
 *
 * Vier zentrale Vorpruefungen, absichtlich alle an EINER Stelle:
 *
 *   1. Wartungsmodus        §1.5a — 503 fuer Kunden- und Adminbereich
 *   2. Einrichtung offen    §1.5  — jeder Aufruf ausser der Einrichtung leitet dorthin
 *   3. CSRF bei jedem POST  §3 Regel 3 — kein Token, keine Ausnahme
 *   4. Adminpruefung        §3 Regel 2a — „vollstaendig durch eine einzige, zentrale
 *                           Vorpruefung geschuetzt, nicht Route fuer Route einzeln.
 *                           Faellt die Pruefung aus, ist die Route nicht erreichbar."
 *
 * Punkt 4 ist der Grund, warum die Adminpruefung hier und nicht in den Handlern steht: Wer
 * sie je Route schreibt, vergisst sie irgendwann bei einer.
 */
final class Router
{
    /** @param list<Route> $routen */
    public function __construct(
        private readonly array $routen,
        private readonly ?InstallationsSperre $sperre = null,
        private readonly ?Wartungsmodus $wartung = null,
        private readonly ?AnmeldeDienst $anmeldung = null,
    ) {
    }

    public function behandeln(string $methode, string $pfad): Antwort
    {
        $methode = strtoupper($methode);

        $treffer = $this->finden($methode, $pfad);
        $route = $treffer['route'] ?? null;
        $parameter = $treffer['parameter'] ?? [];
        $bereich = $route?->bereich ?? Route::BEREICH_OEFFENTLICH;

        // 1. Wartungsmodus (§1.5a)
        if ($this->wartungsmodus()->aktiv()
            && in_array($bereich, [Route::BEREICH_PORTAL, Route::BEREICH_ADMIN, Route::BEREICH_API], true)) {
            return $this->sicherheitskopfzeilen(Antwort::html(
                Ansicht::seite('oeffentlich', 'wartung', ['titel' => 'Wartung']),
                503,
            ));
        }

        // 2. Einrichtung offen (§1.5)
        $einrichtungOffen = !$this->installationssperre()->gesperrt();

        if ($einrichtungOffen && !$this->istEinrichtung($pfad)) {
            return $this->sicherheitskopfzeilen(Antwort::weiter('/admin/setup'));
        }

        // §1.5: Laeuft die Einrichtung ueber unverschluesseltes HTTP, wird sie abgebrochen —
        // nicht gewarnt. Die eine Ausnahme ist eng begrenzt und steht in Ersteinrichtung.
        // Der Abbruch kommt VOR jedem Formular, damit keine Zugangsdaten im Klartext ueber
        // die Leitung gehen (Testfälle 70, 71, 72).
        if ($einrichtungOffen && $this->istEinrichtung($pfad) && !Ersteinrichtung::zugangErlaubt()) {
            return $this->sicherheitskopfzeilen(Antwort::html(
                Ansicht::seite('setup', 'setup-abbruch', ['titel' => 'Die Einrichtung wurde abgebrochen']),
                403,
            ));
        }

        if (!$einrichtungOffen && $this->istEinrichtung($pfad)) {
            // §1.5: nach Abschluss dauerhaft 404. Nicht 403 — die Strecke soll nicht
            // einmal als vorhanden erkennbar sein (Testfall 73).
            return $this->nichtGefunden();
        }

        if ($route === null) {
            return $this->nichtGefunden();
        }

        // 3. Adminpruefung (§3 Regel 2a)
        //
        // Sie steht VOR der CSRF-Pruefung, nicht dahinter. Sonst beantwortet ein
        // unangemeldeter POST die Frage „ist mein Token gueltig" — und das ist eine
        // Auskunft an jemanden, der hier nichts zu suchen hat.
        if ($route->bereich === Route::BEREICH_ADMIN && !$route->ohneAnmeldung) {
            // Zwei Bedingungen, beide noetig:
            //
            //   1. Der Sitzungszustand traegt Rolle `admin` UND ein bestaetigtes TOTP.
            //   2. Die zugehoerige Zeile in `sessions` existiert und ist nicht abgelaufen.
            //
            // Punkt 2 ist der Unterschied zwischen „serverseitig gespeichert" und
            // „serverseitig durchgesetzt" (§3 Regel 6). Ohne ihn waere eine Anmeldung nicht
            // zurueckziehbar, solange das PHP-Cookie gilt — die geloeschte Zeile laege dann
            // ungelesen in der Datenbank.
            if (AdminNachweis::ausSitzung() === null || !$this->anmeldung()->sitzungGueltig()) {
                return $this->sicherheitskopfzeilen(Antwort::weiter('/admin/anmelden'));
            }
        }

        // 4. CSRF bei jedem POST (§3 Regel 3)
        if ($methode === 'POST' && !Csrf::pruefen(Http::eingabe(Csrf::FELD))) {
            return $this->sicherheitskopfzeilen(Antwort::html(
                Ansicht::seite('oeffentlich', 'fehler', [
                    'titel'    => 'Das Formular ist abgelaufen',
                    'meldung'  => 'Bitte laden Sie die Seite neu und schicken Sie das Formular noch einmal ab.',
                    'kennung'  => null,
                ]),
                419,
            ));
        }

        return $this->sicherheitskopfzeilen($this->aufrufen($route, $parameter));
    }

    /** @return list<Route> */
    public function routen(): array
    {
        return $this->routen;
    }

    /** @return list<string> Alle Routen eines Bereichs als „METHODE /pfad". */
    public function schluessel(string $bereich): array
    {
        $schluessel = [];
        foreach ($this->routen as $route) {
            if ($route->bereich === $bereich) {
                $schluessel[] = $route->schluessel();
            }
        }

        sort($schluessel);

        return $schluessel;
    }

    /** @return array{route:Route,parameter:array<string,string>}|null */
    private function finden(string $methode, string $pfad): ?array
    {
        $teile = explode('/', trim($pfad, '/'));

        foreach ($this->routen as $route) {
            if ($route->methode !== $methode) {
                continue;
            }

            if ($route->pfad === $pfad) {
                return ['route' => $route, 'parameter' => []];
            }

            if (!str_contains($route->pfad, '{')) {
                continue;
            }

            $muster = explode('/', trim($route->pfad, '/'));
            if (count($muster) !== count($teile)) {
                continue;
            }

            $parameter = [];
            $passt = true;

            foreach ($muster as $index => $abschnitt) {
                if (str_starts_with($abschnitt, '{') && str_ends_with($abschnitt, '}')) {
                    $parameter[trim($abschnitt, '{}')] = $teile[$index];
                    continue;
                }

                if ($abschnitt !== $teile[$index]) {
                    $passt = false;
                    break;
                }
            }

            if ($passt) {
                return ['route' => $route, 'parameter' => $parameter];
            }
        }

        return null;
    }

    private function istEinrichtung(string $pfad): bool
    {
        return str_starts_with($pfad, '/admin/setup');
    }

    /** @param array<string,string> $parameter */
    private function aufrufen(Route $route, array $parameter): Antwort
    {
        $handler = $route->handler;

        if (is_array($handler)) {
            [$klasse, $methode] = $handler;
            $objekt = new $klasse();

            return $objekt->{$methode}($parameter);
        }

        return $handler($parameter);
    }

    private function nichtGefunden(): Antwort
    {
        return $this->sicherheitskopfzeilen(Antwort::html(
            Ansicht::seite('oeffentlich', 'fehler', [
                'titel'   => 'Diese Seite gibt es nicht',
                'meldung' => 'Der Link führt ins Leere. Vielleicht hat sich die Adresse geändert.',
                'kennung' => null,
            ]),
            404,
        ));
    }

    /**
     * §3 Regel 11. In JEDER Antwort, auch in Fehlerantworten — Testfall 47 prueft „in allen
     * Antworten", nicht „in den erfolgreichen".
     */
    public function sicherheitskopfzeilen(Antwort $antwort): Antwort
    {
        $kopfzeilen = [
            'Content-Security-Policy'   => "default-src 'self'; script-src 'self'; style-src 'self'; "
                . "img-src 'self' data:; font-src 'self'; connect-src 'self'; form-action 'self'; "
                . "frame-ancestors 'none'; base-uri 'self'; object-src 'none'",
            'X-Content-Type-Options'    => 'nosniff',
            'Referrer-Policy'           => 'strict-origin-when-cross-origin',
            'X-Frame-Options'           => 'DENY',
            'Cross-Origin-Opener-Policy' => 'same-origin',
        ];

        if (Helpers\Env::appEnv() === 'production') {
            $kopfzeilen['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        return $antwort->mitKopfzeilen($kopfzeilen);
    }

    private function installationssperre(): InstallationsSperre
    {
        return $this->sperre ?? new InstallationsSperre();
    }

    private function wartungsmodus(): Wartungsmodus
    {
        return $this->wartung ?? new Wartungsmodus();
    }

    private function anmeldung(): AnmeldeDienst
    {
        return $this->anmeldung ?? new AnmeldeDienst();
    }
}
