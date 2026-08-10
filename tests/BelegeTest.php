<?php

declare(strict_types=1);

namespace Sartu\Tests;

use horstoeko\zugferd\ZugferdDocumentPdfReader;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\Admin\AdminRechnungen;
use Sartu\Data\Belege;
use Sartu\Data\Uuid;
use Sartu\Helpers\Speicher;
use Sartu\Services\Belegerzeugung;
use Sartu\Services\InstallationsSperre;
use Sartu\Services\Projektstatus;
use Sartu\Services\Rechnungsdienst;
use Sartu\Services\Zahlungsstatus;

/**
 * Der Beleg selbst — `18_BELEGE_UND_ZAHLUNG.md` Abschnitte 2, 3, 5 und 7.
 *
 * Testfälle: 86 · 87 · 88 · 89 · 94
 *
 * **Warum hier gegen das erzeugte Dokument geprüft wird und nicht gegen den Aufbau.**
 * Abschnitt 3 verlangt „PDF/A-3 mit eingebettetem XML"; ein Test, der nur den Bauer
 * befragt, bestätigt, dass die Absicht stimmt. Was auf der Platte liegt, ist eine
 * andere Aussage — und der Kunde bekommt die Datei, nicht die Absicht.
 */
final class BelegeTest extends Datenbankfall
{
    private string $adminId;

    private string $organisationId;

    private string $projektId;

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];

        touch($this->arbeitsverzeichnis . '/' . InstallationsSperre::DATEINAME);

        $this->betreiberdatenAnlegen();

        $this->adminId = $this->adminAnlegen();
        $this->organisationId = $this->organisationAnlegen('Mustermann Sanitär GmbH', 'erika@example.org');
        $this->kundeAnlegen($this->organisationId, 'erika@example.org');

        // Anschrift des Kunden: Ohne sie fehlt eine Pflichtangabe nach § 14 Abs. 4 UStG,
        // und Fall 89 träfe schon beim ersten Beleg zu.
        $anweisung = $this->pdo->prepare(
            'UPDATE organizations SET street = ?, postal_code = ?, city = ? WHERE id = ?'
        );
        $anweisung->execute(['Bahnhofstrasse 7', '48268', 'Greven', $this->organisationId]);

        $this->projektId = $this->projektAnlegen();

        $this->alsAdmin($this->adminId);
    }

    protected function tearDown(): void
    {
        $_SESSION = [];

        parent::tearDown();
    }

    /**
     * Testfall 87 — alle Pflichtangaben nach § 14 Abs. 4 UStG stehen im **strukturierten**
     * Teil, nicht nur auf dem Blatt.
     *
     * Abschnitt 2 zählt sie auf. Geprüft wird deshalb am XML: Was dort fehlt, kann keine
     * Software lesen, auch wenn es im PDF sichtbar ist.
     */
    public function testAllePflichtangabenStehenImStrukturiertenTeil(): void
    {
        $postfach = new Postfach();
        $rechnungId = $this->rechnungAnlegen($postfach);

        $this->assertSame([], $this->dienst($postfach)->senden($rechnungId, null));

        $xml = $this->eingebettetesXml($rechnungId);

        // Aussteller — Name, vollständige Anschrift, Steuernummer
        $this->assertStringContainsString('SARTU', $xml);
        $this->assertStringContainsString('Strasse 1', $xml);
        $this->assertStringContainsString('01067', $xml);
        $this->assertStringContainsString('Dresden', $xml);
        $this->assertStringContainsString('337/5804/1234', $xml);

        // Empfänger — Name und vollständige Anschrift
        $this->assertStringContainsString('Mustermann Sanitär GmbH', $xml);
        $this->assertStringContainsString('Bahnhofstrasse 7', $xml);
        $this->assertStringContainsString('48268', $xml);
        $this->assertStringContainsString('Greven', $xml);

        $rechnung = $this->rechnungen()->finden($rechnungId);
        $this->assertIsArray($rechnung);

        // Fortlaufende Nummer, Ausstellungsdatum, Leistungsbeschreibung
        $this->assertStringContainsString((string) $rechnung['number'], $xml);

        // Die Norm schreibt für Datumsangaben das Format 102 vor — `JJJJMMTT`, ohne Trenner.
        $this->assertStringContainsString(
            str_replace('-', '', substr((string) $rechnung['issued_at'], 0, 10)),
            $xml,
        );
        $this->assertStringContainsString('Anzahlung', $xml);

        // Entgelt, Steuersatz und Steuerbetrag — getrennt ausgewiesen
        $this->assertStringContainsString('<ram:BasisAmount>1000.00</ram:BasisAmount>', $xml);
        $this->assertStringContainsString('<ram:CalculatedAmount>190.00</ram:CalculatedAmount>', $xml);
        $this->assertStringContainsString('<ram:GrandTotalAmount>1190.00</ram:GrandTotalAmount>', $xml);
        $this->assertStringContainsString('<ram:RateApplicablePercent>19.00</ram:RateApplicablePercent>', $xml);
        $this->assertStringContainsString('<ram:InvoiceCurrencyCode>EUR</ram:InvoiceCurrencyCode>', $xml);
    }

    /**
     * Testfall 88 — die abgelegte Datei ist ein PDF mit eingebettetem XML, und das XML
     * besteht die Prüfung gegen EN 16931.
     */
    public function testDerBelegIstEinPdfMitEingebettetemUndGueltigemXml(): void
    {
        $postfach = new Postfach();
        $rechnungId = $this->rechnungAnlegen($postfach);

        $this->assertSame([], $this->dienst($postfach)->senden($rechnungId, null));

        $belege = (new Belege($this->pdo))->zuRechnung($rechnungId);

        $this->assertCount(1, $belege);
        $this->assertSame('rechnung', (string) $belege[0]['kind']);
        $this->assertSame('pdfa3-zugferd', (string) $belege[0]['format']);

        $inhalt = (new Belegerzeugung(pdo: $this->pdo))->inhalt($belege[0]);

        $this->assertStringStartsWith('%PDF-', $inhalt);
        $this->assertStringContainsString(Belegerzeugung::XML_NAME, $inhalt);

        // Das XML wird aus dem PDF **zurückgelesen** — nicht neu gebaut. Nur so ist geprüft,
        // was in der Datei steckt, und nicht, was der Aufbau gerade wieder herstellen würde.
        $xml = $this->eingebettetesXml($rechnungId);

        $pruefer = new \horstoeko\zugferd\ZugferdXsdValidator(
            \horstoeko\zugferd\ZugferdDocumentReader::readAndGuessFromContent($xml)
        );
        $pruefer->validate();

        $this->assertTrue(
            $pruefer->validationPased(),
            'Das eingebettete XML besteht die Prüfung nicht: ' . implode(' | ', $pruefer->validationErrors()),
        );
    }

    /**
     * Testfall 89 — ein ungültiger Beleg wird nicht versendet.
     *
     * Abschnitt 3: „Fällt die Prüfung durch, bricht der Versand ab. Es gibt **keinen** Weg,
     * eine ungültige Rechnung zu senden — auch nicht mit Bestätigung."
     *
     * Der Anlass hier ist ein fehlendes Ausstellungsdatum in einer von Hand gebauten Zeile:
     * Ohne es gibt es keine Rechnung im Sinne der Norm. Geprüft wird nicht die Fehlermeldung,
     * sondern **die drei Spuren, die nicht entstehen dürfen**: keine Datei, keine Zeile in
     * `documents`, kein Zustandswechsel.
     */
    public function testEinUngueltigerBelegEntstehtNichtUndWirdNichtVersendet(): void
    {
        $postfach = new Postfach();
        $rechnungId = $this->rechnungAnlegen($postfach);

        // Die Betreiberdaten verschwinden — damit fehlt dem Beleg der Aussteller.
        $this->pdo->exec('DELETE FROM operator_settings');

        $fehler = $this->dienst($postfach)->senden($rechnungId, null);

        $this->assertNotSame([], $fehler);

        $rechnung = $this->rechnungen()->finden($rechnungId);

        $this->assertIsArray($rechnung);
        $this->assertSame(Zahlungsstatus::ENTWURF, (string) $rechnung['status']);
        $this->assertNull($rechnung['issued_at']);
        $this->assertSame([], (new Belege($this->pdo))->zuRechnung($rechnungId));
        $this->assertSame([], $this->dateienInDerAblage());
        $this->assertSame([], $postfach->mails);
    }

    /**
     * Derselbe Fall, an der Norm statt an den Betreiberdaten: Eine Position ohne Bezeichnung
     * fällt bei der Prüfung durch — und auch dann liegt hinterher nichts auf der Platte.
     */
    public function testAuchEinNormverstossHinterlaesstKeineDatei(): void
    {
        $erzeugung = new Belegerzeugung(pdo: $this->pdo);

        $ergebnis = $erzeugung->rechnung(
            [
                'id'                 => Uuid::v4(),
                'number'             => 'RE-2026-0001',
                'milestone'          => 'anzahlung',
                'issued_at'          => '2026-08-09 10:00:00',
                'net_cents'          => 100000,
                'vat_cents'          => 19000,
                'gross_cents'        => 119000,
                'paid_cents'         => 0,
                'due_date'           => '2026-08-19',
                'cancels_invoice_id' => null,
            ],
            // Ein Aussteller ohne Namen und ohne Anschrift — die Norm verlangt beides.
            ['firmenname' => '', 'strasse' => '', 'plz' => '', 'ort' => '', 'land' => '', 'steuernummer' => ''],
            ['legal_name' => '', 'street' => '', 'postal_code' => '', 'city' => ''],
            null,
        );

        $this->assertNotSame([], $ergebnis['fehler']);
        $this->assertNull($ergebnis['id']);
        $this->assertNull($ergebnis['pfad']);
        $this->assertSame([], $this->dateienInDerAblage());
    }

    /**
     * Testfall 86 — eine **versendete** Rechnung lässt sich nur über eine Stornorechnung mit
     * eigener Nummer aufheben.
     */
    public function testEineVersendeteRechnungWirdNurUeberEineStornorechnungAufgehoben(): void
    {
        $postfach = new Postfach();
        $rechnungId = $this->rechnungAnlegen($postfach);
        $dienst = $this->dienst($postfach);

        $this->assertSame([], $dienst->senden($rechnungId, null));

        $rechnung = $this->rechnungen()->finden($rechnungId);
        $this->assertIsArray($rechnung);

        $this->assertSame([], $dienst->stornieren($rechnungId, 'Leistung nicht erbracht.', null));

        $aufgehoben = $this->rechnungen()->finden($rechnungId);

        $this->assertIsArray($aufgehoben);
        $this->assertSame(Zahlungsstatus::STORNIERT, (string) $aufgehoben['status']);

        // Die aufgehobene Rechnung bleibt, wie sie war — Nummer und Beträge unverändert.
        $this->assertSame((string) $rechnung['number'], (string) $aufgehoben['number']);
        $this->assertSame(119000, (int) $aufgehoben['gross_cents']);

        $storno = $this->stornoZu($rechnungId);

        $this->assertIsArray($storno);
        $this->assertNotSame((string) $rechnung['number'], (string) $storno['number']);
        $this->assertStringStartsWith('ST-', (string) $storno['number']);
        $this->assertSame(-119000, (int) $storno['gross_cents']);
        $this->assertSame(-100000, (int) $storno['net_cents']);
        $this->assertSame(-19000, (int) $storno['vat_cents']);
        $this->assertNull($storno['due_date']);

        // Beide Belege bleiben abrufbar — gelöscht wird nichts.
        $this->assertCount(1, (new Belege($this->pdo))->zuRechnung($rechnungId));
        $this->assertCount(1, (new Belege($this->pdo))->zuRechnung((string) $storno['id']));

        $betreffe = array_column($postfach->mails, 'betreff');

        $this->assertContains('Stornorechnung zu ' . (string) $rechnung['number'], $betreffe);
    }

    /**
     * Die andere Hälfte von Abschnitt 5: Ein **nie versendeter** Entwurf wird verworfen, und
     * seine Nummer bleibt vergeben.
     */
    public function testEinEntwurfWirdVerworfenUndBehaeltSeineNummer(): void
    {
        $postfach = new Postfach();
        $rechnungId = $this->rechnungAnlegen($postfach);

        $vorher = $this->rechnungen()->finden($rechnungId);
        $this->assertIsArray($vorher);

        $this->assertSame([], $this->dienst($postfach)->stornieren($rechnungId, 'Falsch angelegt.', null));

        $nachher = $this->rechnungen()->finden($rechnungId);

        $this->assertIsArray($nachher);
        $this->assertSame(Zahlungsstatus::VERWORFEN, (string) $nachher['status']);
        $this->assertSame((string) $vorher['number'], (string) $nachher['number']);

        // Kein Beleg, keine Stornorechnung, keine Mail — er ist nie hinausgegangen.
        $this->assertSame([], (new Belege($this->pdo))->zuRechnung($rechnungId));
        $this->assertNull($this->stornoZu($rechnungId));
        $this->assertSame([], $postfach->mails);

        // Die nächste Rechnung bekommt die **folgende** Nummer, nicht dieselbe.
        $zweite = $this->rechnungAnlegen($postfach);
        $zweiteZeile = $this->rechnungen()->finden($zweite);

        $this->assertIsArray($zweiteZeile);
        $this->assertNotSame((string) $vorher['number'], (string) $zweiteZeile['number']);
    }


    /**
     * Eine Stornorechnung ist kein zweiter Rechnungslauf.
     *
     * Beide Wege standen bis zum 10.08.2026 offen, weil eine Stornorechnung eine gewöhnliche
     * Zeile mit dem Zustand `gesendet` ist:
     *
     * - **Zahlung eintragen:** `Zahlungsstatus::ausBetrag(0, -119000, false)` rechnet
     *   `0 >= -119000` und gab `bezahlt` zurück — die Gutschrift stand sofort auf bezahlt,
     *   ohne dass ein Cent geflossen war
     * - **Stornieren:** Der Vorgang lief durch und erzeugte einen Beleg mit **positiven**
     *   Beträgen, der „Stornorechnung" hieß — eine Rechnung unter falschem Namen
     */
    public function testEineStornorechnungNimmtWederZahlungNochStornoAn(): void
    {
        $postfach = new Postfach();
        $rechnungId = $this->rechnungAnlegen($postfach);
        $dienst = $this->dienst($postfach);

        $this->assertSame([], $dienst->senden($rechnungId, null));
        $this->assertSame([], $dienst->stornieren($rechnungId, 'Leistung entfällt.', null));

        $storno = $this->stornoZu($rechnungId);

        $this->assertIsArray($storno);
        $this->assertTrue(\Sartu\Services\Rechnungsdienst::istStorno($storno));

        $stornoId = (string) $storno['id'];

        // Keine Zahlung.
        $fehler = $dienst->zahlungEintragen($stornoId, 0, 'Kontoauszug 08/2026', null);

        $this->assertNotSame([], $fehler);
        $this->assertStringContainsString('nimmt keine Zahlung auf', $fehler[0]);

        // Kein Storno auf den Storno.
        $zweites = $dienst->stornieren($stornoId, 'Doch wieder berechnen.', null);

        $this->assertNotSame([], $zweites);
        $this->assertStringContainsString('lässt sich nicht aufheben', $zweites[0]);

        // Der Zustand der Gutschrift ist unverändert — insbesondere **nicht** `bezahlt`.
        $unveraendert = $this->rechnungen()->finden($stornoId);

        $this->assertIsArray($unveraendert);
        $this->assertSame(Zahlungsstatus::GESENDET, (string) $unveraendert['status']);
        $this->assertSame(0, (int) $unveraendert['paid_cents']);
        $this->assertNull($this->stornoZu($stornoId));
    }

    /**
     * Testfall 94 — die Prüfsumme weist den abgelegten Beleg als unverändert nach.
     *
     * Abschnitt 7: „Weicht sie beim Abruf ab, ist das ein **Fehler** und keine Warnung."
     */
    public function testDiePruefsummeWeistDenBelegAlsUnveraendertNach(): void
    {
        $postfach = new Postfach();
        $rechnungId = $this->rechnungAnlegen($postfach);

        $this->assertSame([], $this->dienst($postfach)->senden($rechnungId, null));

        $beleg = (new Belege($this->pdo))->zuRechnung($rechnungId)[0];
        $erzeugung = new Belegerzeugung(pdo: $this->pdo);

        $inhalt = $erzeugung->inhalt($beleg);

        $this->assertSame(hash('sha256', $inhalt), (string) $beleg['checksum']);

        // Jetzt wird die Datei von aussen verändert — der Abruf muss abbrechen.
        $pfad = Speicher::verzeichnis() . '/belege/' . (string) $beleg['path'];

        file_put_contents($pfad, $inhalt . "\n%veraendert");

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('weicht von seiner Prüfsumme ab');

        $erzeugung->inhalt($beleg);
    }

    /** Fehlt die Datei ganz, ist das derselbe Fehler und keine leere Antwort. */
    public function testEinFehlenderBelegIstEinFehlerUndKeineLeereAntwort(): void
    {
        $postfach = new Postfach();
        $rechnungId = $this->rechnungAnlegen($postfach);

        $this->assertSame([], $this->dienst($postfach)->senden($rechnungId, null));

        $beleg = (new Belege($this->pdo))->zuRechnung($rechnungId)[0];

        unlink(Speicher::verzeichnis() . '/belege/' . (string) $beleg['path']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('fehlt in der Ablage');

        (new Belegerzeugung(pdo: $this->pdo))->inhalt($beleg);
    }

    // ------------------------------------------------------------------ Hilfsmittel

    private function dienst(Postfach $postfach): Rechnungsdienst
    {
        return new Rechnungsdienst($this->nachweis(), mail: $postfach, pdo: $this->pdo);
    }

    private function rechnungen(): AdminRechnungen
    {
        return new AdminRechnungen($this->nachweis(), $this->pdo);
    }

    private function rechnungAnlegen(Postfach $postfach): string
    {
        $angelegt = $this->dienst($postfach)->anlegen(
            $this->projektId,
            ['milestone' => 'anzahlung', 'net_cents' => 100000],
            null,
        );

        $this->assertSame([], $angelegt['fehler']);

        return (string) $angelegt['id'];
    }

    /** @return array<string,mixed>|null */
    private function stornoZu(string $rechnungId): ?array
    {
        $anweisung = $this->pdo->prepare('SELECT * FROM invoices WHERE cancels_invoice_id = ?');
        $anweisung->execute([$rechnungId]);

        $zeile = $anweisung->fetch();

        return is_array($zeile) ? $zeile : null;
    }

    /** Das XML, wie es **im abgelegten PDF** steht. */
    private function eingebettetesXml(string $rechnungId): string
    {
        $beleg = (new Belege($this->pdo))->zuRechnung($rechnungId)[0];
        $inhalt = (new Belegerzeugung(pdo: $this->pdo))->inhalt($beleg);

        // `getXmlFromContent` gibt den **eingebetteten Rohtext**, nicht eine Neuausgabe aus
        // dem eingelesenen Objekt. Nur der Rohtext beantwortet die Frage, was in der Datei
        // steht — eine Neuausgabe zeigt, was der Leser daraus wieder herstellen würde.
        $xml = ZugferdDocumentPdfReader::getXmlFromContent($inhalt);

        $this->assertNotSame('', $xml, 'Im PDF steckt kein eingebettetes XML.');

        return $xml;
    }

    /** @return list<string> */
    private function dateienInDerAblage(): array
    {
        $verzeichnis = Speicher::verzeichnis() . '/belege';

        if (!is_dir($verzeichnis)) {
            return [];
        }

        return array_values(array_map(
            'basename',
            array_filter((array) glob($verzeichnis . '/*'), 'is_file'),
        ));
    }

    private function nachweis(): AdminNachweis
    {
        $nachweis = AdminNachweis::ausSitzung();

        $this->assertNotNull($nachweis);

        return $nachweis;
    }

    private function projektAnlegen(): string
    {
        $id = Uuid::v4();

        $anweisung = $this->pdo->prepare(
            'INSERT INTO projects (id, organization_id, title, package, included_feedback_rounds,'
            . ' protection_level, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $anweisung->execute([$id, $this->organisationId, 'Website Mustermann', 'wachstum', 2, 'm',
            Projektstatus::ZAHLUNG_OFFEN]);

        return $id;
    }
}
