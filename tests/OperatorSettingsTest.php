<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\RechtstexteSpeicher;
use Sartu\Data\Uuid;
use Sartu\Services\BetreiberdatenDienst;
use Sartu\Services\Startsperre;

/**
 * Betreiberdaten — Portal-Lastenheft §1.4a und §4 `operator_settings`.
 *
 * Testfälle: 64 · 65 · 66
 */
final class OperatorSettingsTest extends Datenbankfall
{
    /** @var array<string,string> */
    private const VOLLSTAENDIG = [
        'firmenname'                => 'Vorläufig',
        'strasse'                   => 'Vorläufig 1',
        'plz'                       => '01067',
        'ort'                       => 'Dresden',
        'land'                      => 'DE',
        'email'                     => 'betreiber@example.org',
        'inhaltlich_verantwortlich' => 'Vorläufig',
        'steuernummer'              => '000/000/00000',
    ];

    // ------------------------------------------------------------ Fall 64

    /**
     * Fall 64 — eine ZWEITE Zeile laesst sich nicht anlegen, weder mit anderem
     * singleton-Wert noch mit anderem Schluessel.
     */
    public function testZweiteZeileMitDemselbenSingletonWirdAbgewiesen(): void
    {
        $this->speicher()->anlegen(self::VOLLSTAENDIG);

        $this->expectException(\PDOException::class);

        $this->speicher()->anlegen(self::VOLLSTAENDIG);
    }

    public function testZweiteZeileMitAnderemSingletonWertWirdAbgewiesen(): void
    {
        $this->speicher()->anlegen(self::VOLLSTAENDIG);

        // Der Umweg ueber einen anderen singleton-Wert — genau den faengt die
        // Pruefbedingung ab, die der eindeutige Schluessel allein nicht abfangen wuerde.
        $this->expectException(\PDOException::class);

        $anweisung = $this->pdo->prepare(
            'INSERT INTO operator_settings'
            . ' (id, singleton, firmenname, strasse, plz, ort, land, email, inhaltlich_verantwortlich, steuernummer)'
            . ' VALUES (?, 2, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            Uuid::v4(), 'Zweiter', 'Weg 2', '01069', 'Dresden', 'DE', 'zwei@example.org', 'Zweiter', '111/111/11111',
        ]);
    }

    public function testEsBleibtBeiGenauEinerZeile(): void
    {
        $this->speicher()->anlegen(self::VOLLSTAENDIG);

        $this->assertSame(1, (int) $this->pdo->query('SELECT COUNT(*) FROM operator_settings')->fetchColumn());
    }

    // ------------------------------------------------------------ Fall 65

    /**
     * Fall 65 — `ust_id = ''` UND `steuernummer = ''` wird abgewiesen. Leer ist nicht
     * gesetzt: Die Pruefbedingung darf nicht nur auf NULL pruefen.
     */
    public function testBeideSteuerangabenLeerWerdenVonDerDatenbankAbgewiesen(): void
    {
        $this->expectException(\PDOException::class);

        $anweisung = $this->pdo->prepare(
            'INSERT INTO operator_settings'
            . ' (id, firmenname, strasse, plz, ort, land, email, inhaltlich_verantwortlich, ust_id, steuernummer)'
            . ' VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            Uuid::v4(), 'Vorläufig', 'Weg 1', '01067', 'Dresden', 'DE', 'a@example.org', 'Vorläufig', '', '',
        ]);
    }

    public function testBeideSteuerangabenNullWerdenAbgewiesen(): void
    {
        $this->expectException(\PDOException::class);

        $anweisung = $this->pdo->prepare(
            'INSERT INTO operator_settings'
            . ' (id, firmenname, strasse, plz, ort, land, email, inhaltlich_verantwortlich)'
            . ' VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([
            Uuid::v4(), 'Vorläufig', 'Weg 1', '01067', 'Dresden', 'DE', 'a@example.org', 'Vorläufig',
        ]);
    }

    /** Dieselbe Regel serverseitig — mit einer Meldung, die ein Mensch lesen kann. */
    public function testDienstWeistBeideSteuerangabenLeerAb(): void
    {
        $fehler = (new BetreiberdatenDienst())->pruefen(
            [...self::VOLLSTAENDIG, 'ust_id' => '', 'steuernummer' => '  ']
        );

        $this->assertContains(
            'Tragen Sie entweder die Umsatzsteuer-Identifikationsnummer oder die Steuernummer ein.',
            $fehler
        );
    }

    /** Ein Pflichtfeld aus lauter Leerzeichen ist leer — NOT NULL faengt das nicht ab. */
    public function testPflichtfeldAusLeerzeichenWirdAbgewiesen(): void
    {
        $fehler = (new BetreiberdatenDienst())->pruefen([...self::VOLLSTAENDIG, 'firmenname' => '   ']);

        $this->assertContains('Bitte füllen Sie das Feld „Firmenname" aus.', $fehler);
    }

    // ------------------------------------------------------------ Fall 66

    /**
     * Fall 66 — die Startsperre greift: Bei leerem Pflichtfeld ODER einem Rechtstext im
     * Zustand `entwurf` bricht die produktive Veroeffentlichung ab.
     */
    public function testStartsperreOhneBetreiberdaten(): void
    {
        $hindernisse = (new Startsperre(
            new BetreiberdatenSpeicher($this->pdo),
            new RechtstexteSpeicher($this->pdo),
        ))->hindernisse();

        $this->assertSame(['Die Betreiberdaten sind noch nicht angelegt.'], $hindernisse);
    }

    public function testStartsperreBeiLeeremPflichtfeld(): void
    {
        $this->speicher()->anlegen(self::VOLLSTAENDIG);
        $this->alleRechtstexteFreigeben();

        // Direkt in der Datenbank leeren: NOT NULL laesst '' zu — genau die Falle.
        $this->pdo->exec("UPDATE operator_settings SET ort = '' WHERE singleton = 1");

        $hindernisse = $this->startsperre()->hindernisse();

        $this->assertContains('Das Pflichtfeld „ort" der Betreiberdaten ist leer.', $hindernisse);
        $this->assertFalse($this->startsperre()->starterlaubt());
    }

    /**
     * Eine Steuerangabe aus lauter Leerzeichen wird ebenfalls abgewiesen.
     *
     * Der Grund ist eine Eigenheit von MySQL, die man kennen muss: In einer PAD-SPACE-
     * Kollation wie utf8mb4_unicode_ci ist '   ' gleich ''. Die Bedingung `<> ''` faengt
     * damit auch den Leerzeichenfall ab — nicht aus Absicht, sondern als Nebenwirkung.
     * Der Test haelt das fest, damit eine spaetere Kollationsaenderung auffaellt.
     */
    public function testSteuerangabeAusLeerzeichenWirdAbgewiesen(): void
    {
        $this->expectException(\PDOException::class);

        $this->speicher()->anlegen([...self::VOLLSTAENDIG, 'steuernummer' => '   ', 'ust_id' => null]);
    }

    /** Die Startsperre prueft nach derselben Regel wie das Speichern: nach trim(). */
    public function testStartsperreErkenntPflichtfeldAusLeerzeichen(): void
    {
        $this->speicher()->anlegen(self::VOLLSTAENDIG);
        $this->alleRechtstexteFreigeben();

        $this->pdo->exec("UPDATE operator_settings SET inhaltlich_verantwortlich = '   ' WHERE singleton = 1");

        $this->assertContains(
            'Das Pflichtfeld „inhaltlich_verantwortlich" der Betreiberdaten ist leer.',
            $this->startsperre()->hindernisse()
        );
    }

    public function testStartsperreIstFreiWennAllesVollstaendigIst(): void
    {
        $this->speicher()->anlegen(self::VOLLSTAENDIG);
        $this->alleRechtstexteFreigeben();

        $this->assertSame([], $this->startsperre()->hindernisse());
    }

    /** Jede Aenderung erzeugt einen Pruefeintrag mit altem Wert, neuem Wert und Grund. */
    public function testAenderungErzeugtVollstaendigenPruefeintrag(): void
    {
        $this->speicher()->anlegen(self::VOLLSTAENDIG);
        $admin = $this->adminAnlegen();

        $hinweise = (new BetreiberdatenDienst(new BetreiberdatenSpeicher($this->pdo)))
            ->speichern([...self::VOLLSTAENDIG, 'ort' => 'Bautzen'], 'Umzug', $admin, '127.0.0.1');

        $eintrag = $this->pdo->query(
            "SELECT * FROM audit_events WHERE action = 'betreiberdaten_geaendert'"
        )->fetch();

        $this->assertIsArray($eintrag);
        $this->assertSame('Dresden', $eintrag['old_value']);
        $this->assertSame('Bautzen', $eintrag['new_value']);
        $this->assertSame('Umzug', $eintrag['reason']);
        $this->assertSame($admin, $eintrag['actor_user_id']);
        $this->assertNotSame([], $hinweise, 'Der Hinweis auf Impressum und Rechnungen fehlt.');
    }

    public function testAenderungOhneGrundWirdNichtGespeichert(): void
    {
        $this->speicher()->anlegen(self::VOLLSTAENDIG);

        $this->expectException(\InvalidArgumentException::class);

        (new BetreiberdatenDienst(new BetreiberdatenSpeicher($this->pdo)))
            ->speichern([...self::VOLLSTAENDIG, 'ort' => 'Bautzen'], '   ', $this->adminAnlegen(), null);
    }

    private function speicher(): BetreiberdatenSpeicher
    {
        return new BetreiberdatenSpeicher($this->pdo);
    }

    private function startsperre(): Startsperre
    {
        return new Startsperre(new BetreiberdatenSpeicher($this->pdo), new RechtstexteSpeicher($this->pdo));
    }

    private function alleRechtstexteFreigeben(): void
    {
        $speicher = new RechtstexteSpeicher($this->pdo);

        foreach (RechtstexteSpeicher::SLUGS as $slug) {
            $speicher->anlegen($slug, 'Platzhalter für den Test. Kein Rechtstext.', 'oeffentlich');
            $speicher->zustandSetzen($slug, 'freigegeben', 'Testkanzlei');
        }
    }
}
