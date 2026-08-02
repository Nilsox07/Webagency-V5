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
 * Umfang in Stufe A0 nach REIHENFOLGE.md: Die Datenzugriffsschicht filtert nach
 * organization_id aus der Sitzung · Admin hat organization_id IS NULL · die Pruefbedingung
 * greift. Der Test waechst mit jeder Etappe mit.
 *
 * Testfälle: 5a · 5b · 43 · 44 · 48
 */
final class TenantIsolationTest extends Datenbankfall
{
    /**
     * Fall 5a — die vollstaendige Routenliste des Kundenbereichs, nicht eine Auswahl.
     *
     * In A0 hat der Kundenbereich noch keine Route: Die Kundenanmeldung entsteht in A1.
     * Sobald dort die erste Portalroute dazukommt, schlaegt dieser Test an — und zwingt
     * dazu, sie hier einzutragen und zu pruefen. Genau das ist seine Aufgabe.
     */
    public function testRoutenlisteDesKundenbereichsIstVollstaendigBekannt(): void
    {
        $bekannt = [];

        $this->assertSame(
            $bekannt,
            $this->router()->schluessel(Route::BEREICH_PORTAL),
            'Es gibt eine Kundenroute, die dieser Test nicht kennt. Tragen Sie sie hier ein '
            . 'und pruefen Sie sie — nicht umgekehrt.'
        );
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

    private function router(): Router
    {
        // Die Einrichtung gilt als abgeschlossen: sonst leitet jede Route auf /admin/setup
        // und der Test prueft die Weiterleitung statt die Adminsperre.
        $sperre = new InstallationsSperre(null, sys_get_temp_dir() . '/sartu-test-gesperrt');
        @mkdir(sys_get_temp_dir() . '/sartu-test-gesperrt', 0770, true);
        touch(sys_get_temp_dir() . '/sartu-test-gesperrt/' . InstallationsSperre::DATEINAME);

        $wartung = new Wartungsmodus(sys_get_temp_dir() . '/sartu-test-ohne-wartung');

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
