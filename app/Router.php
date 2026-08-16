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
 *   3. Adminpruefung        §3 Regel 2a — „vollstaendig durch eine einzige, zentrale
 *                           Vorpruefung geschuetzt, nicht Route fuer Route einzeln.
 *                           Faellt die Pruefung aus, ist die Route nicht erreichbar."
 *   3b. Kundenpruefung      §3 Regel 1 und 2a — dieselbe Zusage fuer den Kundenbereich
 *   4. CSRF bei jedem POST  §3 Regel 3 — kein Token, keine Ausnahme
 *
 * Punkt 3 und 3b sind der Grund, warum beide Pruefungen hier und nicht in den Handlern
 * stehen: Wer sie je Route schreibt, vergisst sie irgendwann bei einer.
 *
 * **Sie sind zwei Bedingungen, nicht eine mit Verzweigung.** §3 Regel 2 verbietet den
 * gemeinsamen Codepfad, der den Organisationsfilter fuer Admins weglaesst. Dieselbe Regel
 * gilt fuer die Zugangspruefung: Ein `if ($istAdmin || $istKunde)` waere genau die Stelle,
 * an der spaeter ein Kunde in eine Adminroute rutscht.
 *
 * Die Reihenfolge — Zugang VOR CSRF — ist ebenfalls Absicht: Ein unangemeldeter POST soll
 * nicht erfahren, ob sein Token gueltig war.
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

    /**
     * Der eine Ausgang.
     *
     * Die Sicherheitskopfzeilen werden hier gesetzt und nirgends sonst. Vorher stand der
     * Aufruf an sieben Rueckgabestellen — Testfall 47 verlangt sie „in allen Antworten",
     * und sieben Wiederholungen sind sieben Gelegenheiten, eine zu vergessen.
     */
    public function behandeln(string $methode, string $pfad): Antwort
    {
        // Die Leiste markiert den aktiven Eintrag. Der Pfad kommt aus dem Dispatcher und
        // nicht aus einer Steuerung — siehe `Ansicht::$pfad`.
        Ansicht::pfadSetzen($pfad);

        $antwort = $this->sicherheitskopfzeilen($this->abwickeln($methode, $pfad));

        return $antwort->mitKopfzeilen([
            'Cache-Control' => $this->cachepolitik($methode, $pfad, $antwort->status),
        ]);
    }

    /**
     * Braucht diese Route eine Sitzung? Die drei Gründe stehen an `abwickeln()`.
     *
     * **Der Zahlungs-Webhook bekommt trotz `POST` keine.** Er trägt `ohneCsrf`, weil ein
     * fremder Server kein Token haben kann — und aus demselben Grund braucht er keine
     * Sitzung. Eine anzulegen hiesse, für jeden Aufruf des Zahlungsdienstes eine Datei im
     * Sitzungsverzeichnis zu schreiben, die nie jemand liest.
     */
    private function brauchtSitzung(Route $route): bool
    {
        if ($route->sitzung) {
            return true;
        }

        if ($route->methode === 'POST' && !$route->ohneCsrf) {
            return true;
        }

        return in_array(
            $route->bereich,
            [Route::BEREICH_PORTAL, Route::BEREICH_ADMIN],
            true,
        );
    }

    /**
     * Die Cachepolitik je Antwort — gebaut am 15.08.2026, geschärft am 16.08.2026.
     *
     * ## Warum es sie vorher nicht gab
     *
     * `session_start()` stempelte `Cache-Control: no-store, no-cache, must-revalidate`
     * auf **jede** Antwort — der Vorgabewert von `session.cache_limiter` ist `nocache`.
     * Das galt für die Startseite ebenso wie für `robots.txt`. `Sitzung::starten()` setzt
     * den Wert jetzt leer; entschieden wird hier.
     *
     * ## Die drei Stufen — jetzt an der Sitzung, nicht an einer Pfadliste
     *
     * | Wo | Was | Warum |
     * |---|---|---|
     * | öffentliche GET-Route **ohne** Sitzung | `public, max-age=3600` | Sie trägt kein Cookie und keine persönliche Angabe. Ein Zwischenspeicher darf sie halten — und genau das ist der Gewinn daraus, dass sie cookiefrei ist |
     * | öffentliche GET-Route **mit** Sitzung | `private, max-age=0, must-revalidate` | `/briefing`, `/kontakt`. Sie tragen ein Sitzungscookie und dürfen deshalb in keinen gemeinsamen Zwischenspeicher |
     * | alles andere | `no-store` | Kundenbereich, Adminbereich, `/api/`, jedes `POST` und jede Antwort ohne Route (404). Dort steht, was nur eine Person sehen darf |
     *
     * **Der Unterschied zur Fassung vom 15.08.2026:** Damals stand `public` an einer Liste
     * mit drei Pfaden, und jede andere öffentliche Seite bekam `private` — weil jede eine
     * Sitzung hatte. Jetzt entscheidet dieselbe Angabe beides, und eine neue Leseseite ist
     * von sich aus zwischenspeicherbar.
     *
     * ## Warum der Status mitentscheidet — gemessen am 16.08.2026
     *
     * `/agb` hat eine Route und lieferte trotzdem `404`, weil noch kein Rechtstext
     * freigegeben ist. Die Politik sah nur die Route und stempelte
     * `public, max-age=3600` auf diese Absage. **Damit hätte ein Zwischenspeicher die
     * Seite nach der Freigabe bis zu einer Stunde weiter als „gibt es nicht" ausgeliefert
     * — an dem Tag, an dem sie am dringendsten gebraucht wird.**
     *
     * Dieselbe Falle steckt in jeder Weiterleitung und in jeder Fehlerseite: Ihr Status
     * hängt am Zustand, nicht am Pfad. Zwischengespeichert wird deshalb nur, was
     * tatsächlich `200` ist.
     */
    private function cachepolitik(string $methode, string $pfad, int $status = 200): string
    {
        $route = ($this->finden(strtoupper($methode), $pfad)['route'] ?? null);

        if ($route === null || strtoupper($methode) !== 'GET'
            || $route->bereich !== Route::BEREICH_OEFFENTLICH) {
            return 'no-store';
        }

        if ($this->brauchtSitzung($route)) {
            return 'private, max-age=0, must-revalidate';
        }

        return $status === 200
            ? 'public, max-age=3600'
            : 'no-store';
    }

    private function abwickeln(string $methode, string $pfad): Antwort
    {
        $methode = strtoupper($methode);

        $treffer = $this->finden($methode, $pfad);
        $route = $treffer['route'] ?? null;
        $parameter = $treffer['parameter'] ?? [];
        $bereich = $route?->bereich ?? Route::BEREICH_OEFFENTLICH;

        /*
         * **Die Sitzung beginnt hier, nicht in `public/index.php`** — geändert am
         * 16.08.2026.
         *
         * Vorher startete der Einstieg sie vor der Route, und jede öffentliche Seite setzte
         * `PHPSESSID`. § 25 Abs. 2 Nr. 2 TDDDG erlaubt die Speicherung ohne Einwilligung
         * nur, „soweit sie unbedingt erforderlich ist, damit der Anbieter eines
         * Telemediendienstes einen vom Nutzer ausdrücklich gewünschten Dienst zur Verfügung
         * stellen kann". Eine Leseseite ist kein solcher Dienst.
         *
         * **Drei Gründe, aus denen eine Route eine Sitzung bekommt** — sonst keine:
         *
         * | Grund | Wo |
         * |---|---|
         * | `sitzung: true` an der Route | Bedarfsscheck, `/kontakt` (Formular mit Token) |
         * | jedes `POST` ausser dem Zahlungs-Webhook | der CSRF-Schutz braucht sie |
         * | Kundenbereich, Adminbereich, Ersteinrichtung | dort gibt es eine Anmeldung |
         *
         * Ein `404` bekommt keine. Das ist beabsichtigt: Wer eine Adresse errät, die es
         * nicht gibt, bekommt kein Cookie.
         */
        if ($route !== null && $this->brauchtSitzung($route)) {
            Sitzung::starten();
        }

        // 1. Wartungsmodus (§1.5a)
        if ($this->wartungsmodus()->aktiv()
            && in_array($bereich, [Route::BEREICH_PORTAL, Route::BEREICH_ADMIN, Route::BEREICH_API], true)) {
            return Antwort::html(Ansicht::seite('oeffentlich', 'wartung', ['titel' => 'Wartung']), 503);
        }

        // 2. Einrichtung offen (§1.5)
        $einrichtungOffen = !$this->installationssperre()->gesperrt();

        if ($einrichtungOffen && !$this->istEinrichtung($pfad)) {
            return Antwort::weiter('/admin/setup');
        }

        // §1.5: Laeuft die Einrichtung ueber unverschluesseltes HTTP, wird sie abgebrochen —
        // nicht gewarnt. Die eine Ausnahme ist eng begrenzt und steht in Ersteinrichtung.
        // Der Abbruch kommt VOR jedem Formular, damit keine Zugangsdaten im Klartext ueber
        // die Leitung gehen (Testfälle 70, 71, 72).
        if ($einrichtungOffen && $this->istEinrichtung($pfad) && !Ersteinrichtung::zugangErlaubt()) {
            return Antwort::html(
                Ansicht::seite('setup', 'setup-abbruch', ['titel' => 'Die Einrichtung wurde abgebrochen']),
                403,
            );
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
                return Antwort::weiter('/admin/anmelden');
            }
        }

        // 3b. Kundenpruefung (§3 Regel 1 und 2a)
        //
        // Eine EIGENE Bedingung, kein gemeinsamer Zweig mit Punkt 3. §3 Regel 2 verbietet
        // ausdruecklich den geteilten Codepfad, der den Filter fuer eine der beiden Rollen
        // weglaesst — „genau daraus entsteht die typische Datenpanne". Zwei Schichten
        // heisst hier: zwei Bedingungen, die sich nicht gegenseitig aufheben koennen.
        //
        // Verlangt wird: Rolle `kunde` UND eine Organisation in der Sitzung UND eine
        // serverseitig gueltige Anmeldung. Ein Admin kommt hier NICHT durch — er hat keine
        // Organisation, und ohne Organisation gibt es keinen Kundenzugriff (Testfall 45).
        if ($route->bereich === Route::BEREICH_PORTAL && !$route->ohneAnmeldung) {
            if (Sitzung::wert(Sitzung::ROLLE) !== 'kunde'
                || Sitzung::wert(Sitzung::ORGANISATION) === null
                || !$this->anmeldung()->sitzungGueltig()) {
                return Antwort::weiter('/login');
            }
        }

        // 4. CSRF bei jedem POST (§3 Regel 3)
        //
        // Die eine Ausnahme ist der Zahlungs-Webhook. Sie steht als Schalter an der Route,
        // ist dort begruendet und wird von `TenantIsolationTest` auf genau diese eine Route
        // festgenagelt. Sie greift zusaetzlich nur im Bereich `api` — zwei Bedingungen, damit
        // ein versehentliches `true` an einer Kunden- oder Adminroute wirkungslos bleibt.
        $ohneCsrf = $route->ohneCsrf && $route->bereich === Route::BEREICH_API;

        if ($methode === 'POST' && !$ohneCsrf && !Csrf::pruefen(Http::eingabe(Csrf::FELD))) {
            return Antwort::html(
                Ansicht::seite('oeffentlich', 'fehler', [
                    'titel'   => 'Das Formular ist abgelaufen',
                    'meldung' => 'Bitte laden Sie die Seite neu und schicken Sie das Formular noch einmal ab.',
                    'kennung' => null,
                ]),
                419,
            );
        }

        return $this->aufrufen($route, $parameter);
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

    /**
     * Die Fehlerseite — seit Stufe B im Rahmen der öffentlichen Website.
     *
     * Website-Lastenheft §14 verlangt für 404 einen gebundenen Wortlaut, vier Verweise,
     * echten 404-Status und `noindex`. Das leistet `Website::nichtGefunden()`.
     *
     * **Der Rückfall bleibt.** Solange die Betreiberdaten fehlen — vor der Ersteinrichtung —
     * gibt es keine Website und keinen Fußbereich. Dann antwortet die schlichte Fehlerseite
     * aus A0. Eine 404, die selbst einen Fehler wirft, ist die schlechteste aller Antworten.
     */
    private function nichtGefunden(): Antwort
    {
        try {
            return (new Website())->nichtGefunden();
        } catch (\Throwable) {
            return Antwort::nichtGefunden();
        }
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
            /*
             * **Was die Seite nicht braucht, darf sie nicht anfordern** — ergänzt am
             * 16.08.2026.
             *
             * SARTU nutzt weder Kamera noch Mikrofon noch Standort, und es gibt kein
             * Bezahlfenster im Browser: Der Zahlungsweg läuft über eine Weiterleitung zum
             * Dienst. Eine leere Erlaubnisliste sperrt die Schnittstellen für das Dokument
             * **und** für jeden eingebetteten Rahmen — auch für einen, der über eine
             * Sicherheitslücke hineinkäme.
             *
             * `interest-cohort` steht bewusst nicht dabei: Die Kennung ist zurückgezogen,
             * und eine Kopfzeile gegen etwas, das es nicht mehr gibt, ist Zierde.
             */
            'Permissions-Policy'        => 'camera=(), microphone=(), geolocation=(), '
                . 'payment=(), usb=(), midi=(), magnetometer=(), gyroscope=(), '
                . 'accelerometer=(), display-capture=()',
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
