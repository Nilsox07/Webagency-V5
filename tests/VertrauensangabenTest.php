<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Helpers\Format;
use Sartu\Router;
use Sartu\Services\Auftragslage;
use Sartu\Services\Gruenderangaben;
use Sartu\Services\Gruenderbild;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Uploaddienst;
use Sartu\Services\Wartungsmodus;

/**
 * `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4c — Sperren datengesteuert statt codegesteuert.
 *
 * ## Was dieser Test festnagelt
 *
 * §4c hat eine ganze Fehlerklasse abgeschafft: Ausgaben, die es im Code nicht gibt, weil ein
 * Feld leer sein könnte. An ihre Stelle tritt eine Bedingung auf den Daten — und eine
 * Bedingung, die niemand prüft, ist beim nächsten Umbau eine Zeile, die jemand streicht.
 *
 * **Beide Richtungen werden geprüft, jede einzeln.** Ein Test, der nur „mit Daten erscheint
 * es" sagt, geht auch dann durch, wenn die Sektion immer erscheint — und dann steht der
 * leere Rahmen da, den §5 ausdrücklich verbietet.
 */
final class VertrauensangabenTest extends Datenbankfall
{
    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
        $_POST = [];
        $_GET = [];

        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);
        $this->betreiberdatenAnlegen();
    }

    // ------------------------------------------------- Sektion 6, „Wer dahintersteckt"

    /** §4c: Ohne die drei Angaben entfällt die Sektion — auf beiden Seiten. */
    public function testOhneGruenderangabenEntfaelltDieSektionVollstaendig(): void
    {
        foreach (['/', '/ueber-uns'] as $pfad) {
            $html = (string) $this->router()->behandeln('GET', $pfad)->rumpf;

            $this->assertStringNotContainsString('id="dahinter"', $html, $pfad);
            $this->assertStringNotContainsString(Gruenderangaben::H2, $html, $pfad);
            $this->assertStringNotContainsString(Gruenderangaben::BILD_PFAD, $html, $pfad);
        }
    }

    /**
     * §4c: Zwei von drei genügen nicht.
     *
     * Das ist der Fall, der von selbst eintritt — der Betreiber trägt Name und Text ein und
     * lädt das Bild später hoch. Stünde die Sektion dann schon da, stünde dort ein Absatz
     * ohne Gesicht: der halbe Rahmen, den §5 verbietet.
     */
    public function testZweiVonDreiAngabenGenuegenNicht(): void
    {
        $faelle = [
            'nur Name'             => ['gruender_name' => 'Nils', 'gruender_text' => null, 'bild' => null],
            'Name und Text'        => ['gruender_name' => 'Nils', 'gruender_text' => 'Darum.', 'bild' => null],
            'Name und Bild'        => ['gruender_name' => 'Nils', 'gruender_text' => null, 'bild' => 'a.jpg'],
            'Text und Bild'        => ['gruender_name' => null, 'gruender_text' => 'Darum.', 'bild' => 'a.jpg'],
            'Leerzeichen als Text' => ['gruender_name' => 'Nils', 'gruender_text' => '   ', 'bild' => 'a.jpg'],
        ];

        foreach ($faelle as $bezeichnung => $fall) {
            $this->gruenderSetzen($fall['gruender_name'], $fall['gruender_text'], $fall['bild']);

            $html = (string) $this->router()->behandeln('GET', '/')->rumpf;

            $this->assertStringNotContainsString('id="dahinter"', $html,
                'Die Sektion steht da, obwohl nur „' . $bezeichnung . '" vorliegt.');
        }
    }

    /** §4c: Liegen alle drei vor, läuft alles — Sektion, Bild, Textlink. */
    public function testMitAllenDreiAngabenStehtDieSektion(): void
    {
        $this->gruenderSetzen('Nils Beispiel', "Erster Absatz.\n\nZweiter Absatz.", 'bild.webp');

        $html = (string) $this->router()->behandeln('GET', '/')->rumpf;

        $this->assertStringContainsString('id="dahinter"', $html);
        $this->assertStringContainsString(Gruenderangaben::H2, $html);
        $this->assertStringContainsString('Nils Beispiel', $html);
        $this->assertStringContainsString('src="' . Gruenderangaben::BILD_PFAD . '"', $html);
        $this->assertStringContainsString(Gruenderangaben::LINK_TEXT, $html);

        // Zwei Absätze, nicht eine Wand: `absaetze()` trennt an der Leerzeile.
        $this->assertStringContainsString('<p>Erster Absatz.</p>', $html);
        $this->assertStringContainsString('<p>Zweiter Absatz.</p>', $html);
    }

    /** Auf `/ueber-uns` steht der Abschnitt ohne Link auf die Seite, die man gerade liest. */
    public function testAufUeberUnsStehtKeinLinkAufDieEigeneSeite(): void
    {
        $this->gruenderSetzen('Nils Beispiel', 'Darum gibt es SARTU.', 'bild.webp');

        $html = (string) $this->router()->behandeln('GET', '/ueber-uns')->rumpf;

        $this->assertStringContainsString('id="dahinter"', $html);
        $this->assertStringNotContainsString(Gruenderangaben::LINK_TEXT, $html);
    }

    // ------------------------------------------------- die Ausspielroute des Bildes

    /** Ohne hinterlegtes Bild gibt es die Adresse nicht — kein Ersatzbild (§5). */
    public function testDieBildrouteAntwortetOhneBildMit404(): void
    {
        $this->assertSame(404, $this->router()->behandeln('GET', Gruenderangaben::BILD_PFAD)->status);
    }

    /** Mit Bild: der Inhalt, der Typ aus der Datei und kein Anhang. */
    public function testDieBildrouteLiefertDasBildZumAnzeigen(): void
    {
        $dienst = new Gruenderbild(new BetreiberdatenSpeicher($this->pdo), $this->arbeitsverzeichnis);
        $ergebnis = $dienst->annehmen($this->pngHochladen());

        $this->assertNull($ergebnis['fehler']);

        $antwort = $this->router()->behandeln('GET', Gruenderangaben::BILD_PFAD);

        $this->assertSame(200, $antwort->status);
        $this->assertSame('image/png', $antwort->kopfzeilen['Content-Type']);
        $this->assertSame('nosniff', $antwort->kopfzeilen['X-Content-Type-Options']);
        $this->assertArrayNotHasKey('Content-Disposition', $antwort->kopfzeilen,
            'Ein Anhang füllt kein <img>. Die Regel gilt für Kundenuploads, nicht hier.');
    }

    /** §11 und §4c: SVG kommt nicht herein — es ist die eine Bildart, die Skript trägt. */
    public function testSvgWirdAlsGruenderbildAbgelehnt(): void
    {
        $pfad = $this->arbeitsverzeichnis . '/marke.svg';
        file_put_contents($pfad, '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');

        $ergebnis = Uploaddienst::bildPruefen([
            'error' => UPLOAD_ERR_OK, 'tmp_name' => $pfad, 'size' => filesize($pfad), 'name' => 'marke.svg',
        ]);

        $this->assertNotNull($ergebnis['fehler']);
    }

    /** §11: Endung UND Inhalt. Ein PNG, das `bild.jpg` heisst, wird abgewiesen. */
    public function testEndungUndInhaltMuessenZusammenpassen(): void
    {
        $pfad = $this->arbeitsverzeichnis . '/falsch.jpg';
        file_put_contents($pfad, (string) file_get_contents($this->pngErzeugen()));

        $ergebnis = Uploaddienst::bildPruefen([
            'error' => UPLOAD_ERR_OK, 'tmp_name' => $pfad, 'size' => filesize($pfad), 'name' => 'falsch.jpg',
        ]);

        $this->assertNotNull($ergebnis['fehler']);
    }

    /** Ein Austausch lässt keine verwaiste Datei zurück. */
    public function testDerAustauschRaeumtDieVorigeDateiWeg(): void
    {
        $dienst = new Gruenderbild(new BetreiberdatenSpeicher($this->pdo), $this->arbeitsverzeichnis);

        $erstes = $dienst->annehmen($this->pngHochladen());
        $alterPfad = $dienst->pfadZu((string) $erstes['name']);
        $this->assertFileExists($alterPfad);

        $zweites = $dienst->annehmen($this->pngHochladen());

        $this->assertNotSame($erstes['name'], $zweites['name']);
        $this->assertFileDoesNotExist($alterPfad);
        $this->assertFileExists($dienst->pfadZu((string) $zweites['name']));
    }

    // ------------------------------------------------- strukturierte Daten (§4c c und d)

    /** §4c: Ohne Anschrift bleibt es `Organization` ohne Adressfeld. */
    public function testOhneAnschriftGibtEsKeinLocalBusiness(): void
    {
        $this->anschriftLeeren();

        $graph = $this->strukturdatenDerStartseite();

        $this->assertSame('Organization', $graph[0]['@type']);
        $this->assertArrayNotHasKey('address', $graph[0]);
    }

    /** §4c: Mit Anschrift wird `LocalBusiness` daraus — die `@id` bleibt dieselbe. */
    public function testMitAnschriftEntstehtLocalBusiness(): void
    {
        $graph = $this->strukturdatenDerStartseite();

        $this->assertSame('LocalBusiness', $graph[0]['@type']);
        $this->assertSame('Dresden', $graph[0]['address']['addressLocality']);
        $this->assertStringEndsWith('/#organisation', $graph[0]['@id']);
    }

    /** §4c d: Logo, Gründer und sameAs — jedes nur bei gefülltem Feld. */
    public function testGruenderUndProfileStehenNurBeiGefuelltenFeldern(): void
    {
        $graph = $this->strukturdatenDerStartseite();

        $this->assertArrayHasKey('logo', $graph[0], 'Die Logodateien liegen, also gehört das Feld hin.');
        $this->assertArrayNotHasKey('founder', $graph[0]);
        $this->assertArrayNotHasKey('sameAs', $graph[0]);

        $this->gruenderSetzen('Nils Beispiel', 'Darum gibt es SARTU.', 'bild.webp');
        $this->profileSetzen("https://example.org/sartu\nhttps://example.com/sartu");

        $graph = $this->strukturdatenDerStartseite();

        $this->assertSame('Person', $graph[0]['founder']['@type']);
        $this->assertSame('Nils Beispiel', $graph[0]['founder']['name']);
        $this->assertCount(2, $graph[0]['sameAs']);
    }

    /** Eine Zeile, die keine Adresse ist, macht den ganzen Block ungültig — also fliegt sie raus. */
    public function testUngueltigeProfilzeilenErscheinenNichtInSameAs(): void
    {
        $this->profileSetzen("https://example.org/sartu\nInstagram: sartu\nhttp://example.net/unsicher");

        $graph = $this->strukturdatenDerStartseite();

        $this->assertSame(['https://example.org/sartu'], $graph[0]['sameAs']);
    }

    /** §16, Punkt 3 der Fundliste: `Article` trägt Erstdatum, Autor und Bild. */
    public function testDasArtikelSchemaTraegtErstdatumAutorUndBild(): void
    {
        $this->gruenderSetzen('Nils Beispiel', 'Darum gibt es SARTU.', 'bild.webp');

        $html = (string) $this->router()->behandeln('GET', '/ratgeber/was-kostet-eine-firmenwebsite')->rumpf;
        $artikel = $this->ersterKnoten($html, 'Article');

        $this->assertArrayHasKey('datePublished', $artikel);
        $this->assertArrayHasKey('dateModified', $artikel);
        $this->assertSame('Person', $artikel['author']['@type']);
        $this->assertSame('Nils Beispiel', $artikel['author']['name']);
        $this->assertArrayHasKey('image', $artikel);
    }

    /** Ohne Gründerangabe bleibt der Autor die Organisation — wahr statt erfunden. */
    public function testOhneGruenderIstDerAutorDieOrganisation(): void
    {
        $html = (string) $this->router()->behandeln('GET', '/ratgeber/was-kostet-eine-firmenwebsite')->rumpf;
        $artikel = $this->ersterKnoten($html, 'Article');

        $this->assertSame('Organization', $artikel['author']['@type']);
    }

    // ------------------------------------------------- die Kapazitätszeile (Punkt 9)

    /** Ohne Datum trägt `knapp` keinen Text mehr — die Zeile entfällt. */
    public function testKnappOhneDatumZeigtNichts(): void
    {
        $this->assertNull(Auftragslage::anzeige(Auftragslage::KNAPP, null));
        $this->assertNull(Auftragslage::anzeige(Auftragslage::KNAPP, ''));
    }

    /** Mit Datum steht der Monat da — überprüfbar statt „wenige Plätze". */
    public function testMitDatumStehtDerNaechsteProjektstart(): void
    {
        $kuenftig = Format::inTagen(60);
        $anzeige = Auftragslage::anzeige(Auftragslage::KNAPP, $kuenftig);

        $this->assertNotNull($anzeige);
        $this->assertSame('Nächster Projektstart ab ' . Format::monatJahr($kuenftig), $anzeige['text']);
        $this->assertStringNotContainsString('wenige', $anzeige['text']);
    }

    /** Ein vergangener Termin wird nicht angezeigt — er ist nachweislich falsch. */
    public function testEinVergangenerProjektstartWirdNichtAngezeigt(): void
    {
        $vergangen = (new \DateTimeImmutable(Format::heute()))->modify('-2 months')->format('Y-m-d');

        $this->assertNull(Auftragslage::anzeige(Auftragslage::KNAPP, $vergangen));
        $this->assertSame('Freie Kapazitäten', Auftragslage::anzeige(Auftragslage::OFFEN, $vergangen)['text']);
    }

    /** Der laufende Monat gilt noch — verglichen wird auf den Monat, nicht auf den Tag. */
    public function testDerLaufendeMonatGiltNoch(): void
    {
        $erster = substr(Format::heute(), 0, 8) . '01';

        $this->assertNotNull(Auftragslage::anzeige(Auftragslage::KNAPP, $erster));
    }

    /** Bei `ausgebucht` steht der Monat zusätzlich, nicht anstelle der Aussage. */
    public function testAusgebuchtNenntDenMonatZusaetzlich(): void
    {
        $anzeige = Auftragslage::anzeige(Auftragslage::AUSGEBUCHT, Format::inTagen(90));

        $this->assertStringContainsString('ausgebucht', $anzeige['text']);
        $this->assertStringContainsString('Projektstart ab', $anzeige['text']);
        $this->assertSame(Auftragslage::KNOPF_WARTELISTE, $anzeige['knopf']);
    }

    // ------------------------------------------------- Hilfsmittel

    private function gruenderSetzen(?string $name, ?string $text, ?string $bild): void
    {
        $anweisung = $this->pdo->prepare(
            'UPDATE operator_settings SET gruender_name = ?, gruender_text = ?, gruender_bild = ? WHERE singleton = 1'
        );
        $anweisung->execute([$name, $text, $bild]);
    }

    private function profileSetzen(string $adressen): void
    {
        $anweisung = $this->pdo->prepare('UPDATE operator_settings SET profil_adressen = ? WHERE singleton = 1');
        $anweisung->execute([$adressen]);
    }

    private function anschriftLeeren(): void
    {
        // `strasse`, `plz` und `ort` sind NOT NULL — die leere Zeichenkette ist der Zustand
        // „nicht ausgefüllt" (§4a: NOT NULL erlaubt '').
        $this->pdo->exec("UPDATE operator_settings SET strasse = '', plz = '', ort = '' WHERE singleton = 1");
    }

    /** @return list<array<string,mixed>> */
    private function strukturdatenDerStartseite(): array
    {
        $html = (string) $this->router()->behandeln('GET', '/')->rumpf;

        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $html, $treffer);
        $daten = json_decode($treffer[1], true);

        return $daten['@graph'];
    }

    /** @return array<string,mixed> */
    private function ersterKnoten(string $html, string $typ): array
    {
        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $html, $treffer);
        $daten = json_decode($treffer[1], true);

        foreach ($daten['@graph'] ?? [$daten] as $knoten) {
            if (($knoten['@type'] ?? null) === $typ) {
                return $knoten;
            }
        }

        self::fail('Kein Knoten vom Typ ' . $typ . ' in den strukturierten Daten.');
    }

    /** @return array<string,mixed> */
    private function pngHochladen(): array
    {
        $pfad = $this->pngErzeugen();

        return ['error' => UPLOAD_ERR_OK, 'tmp_name' => $pfad, 'size' => filesize($pfad), 'name' => 'portrait.png'];
    }

    /** Ein echtes PNG, kein umbenanntes Textstück — `bildPruefen()` liest den Inhalt. */
    private function pngErzeugen(): string
    {
        $bild = imagecreatetruecolor(4, 5);
        $pfad = $this->arbeitsverzeichnis . '/' . uniqid('probe', true) . '.png';
        imagepng($bild, $pfad);
        imagedestroy($bild);

        return $pfad;
    }

    private function router(): Router
    {
        return new Router(
            require SARTU_WURZEL . '/app/routes.php',
            new InstallationsSperre(new BetreiberdatenSpeicher($this->pdo), $this->arbeitsverzeichnis),
            new Wartungsmodus($this->arbeitsverzeichnis . '/ohne-wartung'),
        );
    }
}
