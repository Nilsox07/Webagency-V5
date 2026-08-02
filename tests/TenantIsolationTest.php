<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminOrganisationen;
use Sartu\Data\Customer\KundenBenutzer;
use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenOrganisationen;
use Sartu\Data\FehlendeOrganisation;
use Sartu\Route;
use Sartu\Router;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Wartungsmodus;

/**
 * Portal-Lastenheft §3 Regel 1: „Der Test tests/TenantIsolationTest.php ist unantastbar:
 * nie loeschen, nie abschwaechen, um gruen zu werden."
 *
 * Der Test waechst mit jeder Etappe mit. Stand A1: Die Datenzugriffsschicht filtert nach
 * organization_id aus der Sitzung · Admin hat organization_id IS NULL · die Pruefbedingung
 * greift · jede Kundenroute verlangt eine angemeldete Sitzung · fremde Projekte und
 * Angebote sind unsichtbar, auch bei gezieltem Aufruf ihrer Kennung.
 *
 * Testfälle: 1 · 3 · 5 · 5a · 5b · 42 · 43 · 44 · 45 · 48
 */
final class TenantIsolationTest extends Datenbankfall
{
    /**
     * Fall 5a — die vollstaendige Routenliste des Kundenbereichs, nicht eine Auswahl.
     *
     * Die Liste hat in A0 angeschlagen, als sie leer war, und sie schlaegt bei jeder
     * weiteren Route wieder an. Genau das ist ihre Aufgabe: Eine Kundenroute, die dieser
     * Test nicht kennt, ist eine ungeprueft ausgelieferte Kundenroute.
     *
     * Die drei offenen Routen (`/login`, `/login/{token}`) tragen `ohneAnmeldung` — sie
     * existieren, BEVOR jemand angemeldet ist. Die uebrigen fuenf sind geschuetzt, und
     * `testJedeGeschuetzteKundenrouteVerlangtEineAngemeldeteSitzung` faehrt sie einzeln an.
     */
    public function testRoutenlisteDesKundenbereichsIstVollstaendigBekannt(): void
    {
        $bekannt = [
            'GET /login',
            'GET /login/{token}',
            'GET /portal',
            'GET /portal/angebot',
            'GET /willkommen/{nummer}',
            'POST /login',
            'POST /portal/abmelden',
            'POST /willkommen/fertig',
        ];

        $this->assertSame(
            $bekannt,
            $this->router()->schluessel(Route::BEREICH_PORTAL),
            'Es gibt eine Kundenroute, die dieser Test nicht kennt. Tragen Sie sie hier ein '
            . 'und pruefen Sie sie — nicht umgekehrt.'
        );
    }

    /**
     * Jede geschuetzte Kundenroute verlangt eine angemeldete Sitzung — einzeln geprueft.
     *
     * Nicht stichprobenartig: §3 Regel 2a verlangt, dass die Pruefung zentral greift und
     * keine Route sie umgeht. Der Test faehrt deshalb die Liste ab, die der Test darueber
     * als vollstaendig festhaelt.
     */
    public function testJedeGeschuetzteKundenrouteVerlangtEineAngemeldeteSitzung(): void
    {
        $_SESSION = [];
        $geprueft = 0;

        foreach ($this->router()->routen() as $route) {
            if ($route->bereich !== Route::BEREICH_PORTAL || $route->ohneAnmeldung) {
                continue;
            }

            $antwort = $this->router()->behandeln($route->methode, str_replace('{nummer}', '1', $route->pfad));
            ++$geprueft;

            $this->assertSame(302, $antwort->status, $route->schluessel());
            $this->assertSame('/login', $antwort->kopfzeilen['Location'] ?? null, $route->schluessel());
        }

        $this->assertGreaterThanOrEqual(5, $geprueft);
    }

    /**
     * Fall 45 — ein Admin erreicht den Kundenbereich NICHT.
     *
     * Das ist keine Formalie: Ein Admin hat bewusst keine `organization_id` (§3 Regel 2a).
     * Kaeme er durch, muesste irgendwo ein Zweig stehen, der den Organisationsfilter fuer
     * ihn weglaesst — und genau der ist verboten.
     */
    public function testAngemeldeterAdminErreichtKeineKundenroute(): void
    {
        $this->alsAdmin($this->adminAnlegen());

        foreach (['/portal', '/portal/angebot', '/willkommen/1'] as $pfad) {
            $antwort = $this->router()->behandeln('GET', $pfad);

            $this->assertSame(302, $antwort->status, $pfad);
            $this->assertSame('/login', $antwort->kopfzeilen['Location'] ?? null, $pfad);
        }
    }

    /**
     * Faelle 1, 3 und 5 — fremde Projekte und Angebote gibt es fuer Kunde A nicht.
     *
     * Geprueft wird ueber die Datenzugriffsschicht und nicht ueber die Oberflaeche: Dort
     * entscheidet sich, ob ein fremder Datensatz sichtbar wird. Eine Ansicht, die ihn
     * ausblendet, hat ihn trotzdem geladen.
     */
    public function testKundeSiehtWederFremdeProjekteNochFremdeAngebote(): void
    {
        $meine = $this->organisationAnlegen('Betrieb A', 'a@example.org');
        $fremde = $this->organisationAnlegen('Betrieb B', 'b@example.org');

        $meinProjekt = $this->projektAnlegen($meine, 'Website A');
        $fremdesProjekt = $this->projektAnlegen($fremde, 'Website B');

        $meinAngebot = $this->angebotAnlegen($meinProjekt, 'AN-2026-001');
        $fremdesAngebot = $this->angebotAnlegen($fremdesProjekt, 'AN-2026-002');

        $this->alsKunde($meine, $this->kundeAnlegen($meine, 'kunde-a@example.org'));

        $bereich = \Sartu\Data\Customer\KundenBereich::ausSitzung();
        $projekte = new \Sartu\Data\Customer\KundenProjekte($bereich);
        $angebote = new \Sartu\Data\Customer\KundenAngebote($bereich);

        // Fall 5 — die Liste enthaelt ausschliesslich eigene Datensaetze.
        $this->assertCount(1, $projekte->liste());
        $this->assertSame($meinProjekt, (string) $projekte->liste()[0]['id']);
        $this->assertCount(1, $angebote->liste());

        // Fall 1 und 3 — der gezielte Aufruf einer fremden Kennung findet nichts.
        $this->assertNull($projekte->finden($fremdesProjekt), 'Ein fremdes Projekt war sichtbar.');
        $this->assertNull($angebote->finden($fremdesAngebot), 'Ein fremdes Angebot war sichtbar.');

        // Und die eigenen sind es sehr wohl — sonst prueft der Test nur, dass nichts geht.
        $this->assertNotNull($projekte->finden($meinProjekt));
        $this->assertNotNull($angebote->finden($meinAngebot));
    }

    /** §5.2: Ein Angebot im Zustand `entwurf` ist fuer den Kunden unsichtbar. */
    public function testEntwurfEinesAngebotsIstFuerDenKundenUnsichtbar(): void
    {
        $organisation = $this->organisationAnlegen('Betrieb A', 'a@example.org');
        $projekt = $this->projektAnlegen($organisation, 'Website A');
        $entwurf = $this->angebotAnlegen($projekt, 'AN-2026-003', 'entwurf');

        $this->alsKunde($organisation, $this->kundeAnlegen($organisation, 'kunde-a@example.org'));

        $angebote = new \Sartu\Data\Customer\KundenAngebote(
            \Sartu\Data\Customer\KundenBereich::ausSitzung()
        );

        $this->assertNull($angebote->finden($entwurf), 'Ein Entwurf war fuer den Kunden sichtbar.');
        $this->assertSame([], $angebote->liste());
    }

    /** Fall 5b — eine Kundenabfrage ohne Organisation in der Sitzung wirft. */
    public function testKundenabfrageOhneOrganisationWirftUndLiefertNichtAlles(): void
    {
        $eine = $this->organisationAnlegen('Betrieb A', 'a@example.org');
        $andere = $this->organisationAnlegen('Betrieb B', 'b@example.org');

        $this->assertNotSame($eine, $andere);

        $_SESSION = [];

        $this->expectException(FehlendeOrganisation::class);

        KundenBereich::ausSitzung();
    }

    /** Fall 5b, zweite Haelfte: auch ein leerer Wert gilt als fehlend. */
    public function testLeereOrganisationInDerSitzungGiltAlsFehlend(): void
    {
        $_SESSION[\Sartu\Sitzung::ORGANISATION] = '';

        $this->expectException(FehlendeOrganisation::class);

        KundenBereich::ausSitzung();
    }

    /** Die Kundenschicht liefert ausschliesslich die eigene Organisation. */
    public function testKundeSiehtNurDieEigeneOrganisation(): void
    {
        $a = $this->organisationAnlegen('Betrieb A', 'a@example.org');
        $b = $this->organisationAnlegen('Betrieb B', 'b@example.org');

        $this->alsKunde($a, $this->kundeAnlegen($a, 'kunde-a@example.org'));

        $organisationen = new KundenOrganisationen(KundenBereich::ausSitzung());

        $eigene = $organisationen->eigene();
        $this->assertIsArray($eigene);
        $this->assertSame($a, $eigene['id']);

        $liste = $organisationen->liste();
        $this->assertCount(1, $liste);
        $this->assertSame($a, $liste[0]['id']);

        $this->assertFalse($organisationen->gehoertMir($b));
    }

    /** Ein fremder Benutzer ist nicht auffindbar — null bedeutet 404, nicht 403. */
    public function testKundeFindetKeinenBenutzerEinerFremdenOrganisation(): void
    {
        $a = $this->organisationAnlegen('Betrieb A', 'a@example.org');
        $b = $this->organisationAnlegen('Betrieb B', 'b@example.org');

        $meiner = $this->kundeAnlegen($a, 'kunde-a@example.org');
        $fremder = $this->kundeAnlegen($b, 'kunde-b@example.org');

        $this->alsKunde($a, $meiner);

        $benutzer = new KundenBenutzer(KundenBereich::ausSitzung());

        $this->assertIsArray($benutzer->finden($meiner));
        $this->assertNull($benutzer->finden($fremder), 'Ein fremder Benutzer war auffindbar.');
        $this->assertCount(1, $benutzer->liste());
    }

    /**
     * Die Kundenschicht hat keinen Weg, die Organisation von aussen zu setzen.
     *
     * Das ist der eigentliche Schutz: Ein Formularfeld kann nichts umbiegen, was es als
     * Parameter nicht gibt.
     */
    public function testKundenBereichLaesstSichNichtVonAussenSetzen(): void
    {
        $spiegel = new \ReflectionClass(KundenBereich::class);

        $this->assertTrue(
            $spiegel->getConstructor()?->isPrivate(),
            'Der Konstruktor von KundenBereich muss privat bleiben.'
        );

        $oeffentlich = array_filter(
            $spiegel->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_STATIC),
            static fn (\ReflectionMethod $m) => $m->getDeclaringClass()->getName() === KundenBereich::class,
        );

        $namen = array_map(static fn (\ReflectionMethod $m) => $m->getName(), $oeffentlich);
        sort($namen);

        $this->assertSame(
            ['ausSitzung'],
            array_values($namen),
            'KundenBereich hat eine zweite Fabrik bekommen. Jede davon ist ein Weg an der Sitzung vorbei.'
        );

        $this->assertSame(
            0,
            $spiegel->getMethod('ausSitzung')->getNumberOfParameters(),
            'ausSitzung() darf keinen Parameter annehmen.'
        );
    }

    /** Fall 48 — die Datenbankbedingung greift: Kunde ohne Organisation. */
    public function testKundeOhneOrganisationLaesstSichNichtAnlegen(): void
    {
        $this->expectException(\PDOException::class);

        $anweisung = $this->pdo->prepare(
            'INSERT INTO users (id, organization_id, email, role) VALUES (?, NULL, ?, ?)'
        );
        $anweisung->execute([\Sartu\Data\Uuid::v4(), 'ohne@example.org', 'kunde']);
    }

    /** Fall 48 — und die Gegenrichtung: Admin MIT Organisation. */
    public function testAdminMitOrganisationLaesstSichNichtAnlegen(): void
    {
        $organisation = $this->organisationAnlegen('Betrieb A', 'a@example.org');

        $this->expectException(\PDOException::class);

        $anweisung = $this->pdo->prepare(
            'INSERT INTO users (id, organization_id, email, role) VALUES (?, ?, ?, ?)'
        );
        $anweisung->execute([\Sartu\Data\Uuid::v4(), $organisation, 'admin2@example.org', 'admin']);
    }

    /** Ein angelegter Admin hat organization_id IS NULL. */
    public function testAdminHatKeineOrganisation(): void
    {
        $id = $this->adminAnlegen();

        $anweisung = $this->pdo->prepare('SELECT organization_id FROM users WHERE id = ?');
        $anweisung->execute([$id]);

        $this->assertNull($anweisung->fetchColumn());
    }

    /**
     * Fall 43 — ein abgemeldeter Benutzer erreicht KEINE Adminroute.
     *
     * Geprueft ueber die vollstaendige Adminroutenliste, nicht ueber eine Stichprobe
     * (§3 Regel 2a). Ausgenommen sind nur die Routen, die ohne Anmeldung erreichbar sein
     * MUESSEN: die Ersteinrichtung und die Anmeldung selbst.
     */
    public function testAbgemeldeterBenutzerErreichtKeineAdminroute(): void
    {
        $_SESSION = [];
        $router = $this->router();

        $geprueft = 0;

        foreach ($router->routen() as $route) {
            if ($route->bereich !== Route::BEREICH_ADMIN || $route->ohneAnmeldung) {
                continue;
            }

            $antwort = $router->behandeln($route->methode, $route->pfad);
            ++$geprueft;

            $this->assertSame(
                302,
                $antwort->status,
                sprintf('Die Route %s war ohne Anmeldung erreichbar.', $route->schluessel())
            );
            $this->assertSame('/admin/anmelden', $antwort->kopfzeilen['Location'] ?? null);
        }

        $this->assertGreaterThan(0, $geprueft, 'Es wurde keine geschuetzte Adminroute geprueft.');
    }

    /**
     * Fall 44 — ein Admin ohne bestaetigtes TOTP erreicht keine Adminroute.
     *
     * Rolle allein genuegt nicht. Zwischen Passwort und Code ist niemand angemeldet.
     */
    public function testAdminOhneBestaetigtesTotpErreichtKeineAdminroute(): void
    {
        $id = $this->adminAnlegen();

        $_SESSION = [
            \Sartu\Sitzung::BENUTZER => $id,
            \Sartu\Sitzung::ROLLE    => 'admin',
            // TOTP_BESTAETIGT fehlt bewusst.
        ];

        $this->assertNull(AdminNachweis::ausSitzung());

        $router = $this->router();

        foreach ($router->routen() as $route) {
            if ($route->bereich !== Route::BEREICH_ADMIN || $route->ohneAnmeldung) {
                continue;
            }

            $antwort = $router->behandeln($route->methode, $route->pfad);

            $this->assertSame(
                302,
                $antwort->status,
                sprintf('Die Route %s war ohne bestaetigtes TOTP erreichbar.', $route->schluessel())
            );
        }
    }

    /**
     * Die Adminschicht laesst sich ohne Nachweis nicht bauen — auch nicht versehentlich.
     *
     * §3 Regel 2a verbietet den gemeinsamen Codepfad mit optionalem Filter. Diese Pruefung
     * haelt die Trennung an der Signatur fest, nicht an der Absicht.
     */
    public function testAdminschichtVerlangtEinenNachweis(): void
    {
        foreach ([AdminOrganisationen::class, \Sartu\Data\Admin\AdminBenutzer::class] as $klasse) {
            $ersterParameter = (new \ReflectionClass($klasse))->getConstructor()?->getParameters()[0] ?? null;

            $this->assertNotNull($ersterParameter, $klasse . ' hat keinen Konstruktorparameter.');
            $this->assertSame(
                AdminNachweis::class,
                (string) $ersterParameter->getType(),
                $klasse . ' verlangt keinen AdminNachweis.'
            );
        }
    }

    /** Kein Codepfad mit abschaltbarem Organisationsfilter (§3 Regel 2a, ausdrueckliches Verbot). */
    public function testKeinGemeinsamerCodepfadMitAbschaltbaremFilter(): void
    {
        $verdaechtig = [];

        foreach ($this->quelldateien() as $datei) {
            // Kommentare zaehlen nicht: Das Verbot steht dort ausdruecklich zitiert, und
            // ein Test, der seine eigene Begruendung anschlaegt, ist kein Test.
            $inhalt = $this->ohneKommentare((string) file_get_contents($datei));

            if (preg_match('/organization_id\s*=\s*\?\s*OR\s/i', $inhalt) === 1
                || preg_match('/OR\s+\?\s+IS\s+TRUE/i', $inhalt) === 1
                || preg_match('/organization_id\s*=\s*\$\w+\s*OR/i', $inhalt) === 1) {
                $verdaechtig[] = $datei;
            }
        }

        $this->assertSame([], $verdaechtig, 'Ein Organisationsfilter laesst sich abschalten.');
    }

    private function projektAnlegen(string $organisationId, string $titel): string
    {
        $id = \Sartu\Data\Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO projects (id, organization_id, title, package, included_feedback_rounds,'
            . ' protection_level, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([$id, $organisationId, $titel, 'wachstum', 2, 'm', 'angebot_offen']);

        return $id;
    }

    private function angebotAnlegen(string $projektId, string $nummer, string $zustand = 'gesendet'): string
    {
        $id = \Sartu\Data\Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO offers (id, project_id, number, status, package, summary, sitemap, inclusions,'
            . ' exclusions, included_feedback_rounds, delivery_days_min, delivery_days_max,'
            . ' delivery_start_condition, one_time_net_cents, protection_level,'
            . ' protection_monthly_net_cents, protection_min_term_months, first_year_net_cents,'
            . ' payment_plan, rights_text, domain_text, valid_until)'
            . ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            $id, $projektId, $nummer, $zustand, 'wachstum', 'Zusammenfassung', 'Seitenstruktur',
            'Enthalten', 'Nicht enthalten', 2, 10, 15, 'Bedingung', 390000, 'm', 12900, 12,
            390000 + 12 * 12900, '50_50', 'Rechte', 'Domain', '2027-12-31',
        ]);

        return $id;
    }

    private function router(): Router
    {
        // Die Einrichtung gilt als abgeschlossen: sonst leitet jede Route auf /admin/setup
        // und der Test prueft die Weiterleitung statt die Adminsperre.
        $sperre = new InstallationsSperre(null, $this->arbeitsverzeichnis);
        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);

        $wartung = new Wartungsmodus($this->arbeitsverzeichnis . '/ohne-wartung');

        return new Router(require SARTU_WURZEL . '/app/routes.php', $sperre, $wartung);
    }

    private function ohneKommentare(string $quelltext): string
    {
        $ausgabe = '';

        foreach (token_get_all($quelltext) as $marke) {
            if (is_array($marke) && in_array($marke[0], [T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }

            $ausgabe .= is_array($marke) ? $marke[1] : $marke;
        }

        return $ausgabe;
    }

    /** @return list<string> */
    private function quelldateien(): array
    {
        $dateien = [];

        foreach (['app', 'admin', 'portal', 'api'] as $verzeichnis) {
            $pfad = SARTU_WURZEL . '/' . $verzeichnis;

            if (!is_dir($pfad)) {
                continue;
            }

            $lauf = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($pfad));

            foreach ($lauf as $datei) {
                if ($datei instanceof \SplFileInfo && $datei->getExtension() === 'php') {
                    $dateien[] = $datei->getPathname();
                }
            }
        }

        return $dateien;
    }
}
