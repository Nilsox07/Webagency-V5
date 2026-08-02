<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\RechtstexteSpeicher;
use Sartu\OeffentlicheSeiten;
use Sartu\Services\Startsperre;

/**
 * Rechtstexte mit Freigabezustand — Portal-Lastenheft §1.4a und §4 `legal_texts`.
 *
 * Testfälle: 81 · 82
 */
final class LegalTextsTest extends Datenbankfall
{
    // ------------------------------------------------------------ Fall 81

    /**
     * Fall 81 — `legal_texts` mit `slug = avv` im Zustand `entwurf` blockiert die
     * produktive Veroeffentlichung.
     */
    public function testAvvImEntwurfBlockiertDieVeroeffentlichung(): void
    {
        $this->betreiberdatenAnlegen();

        $speicher = $this->speicher();

        foreach (RechtstexteSpeicher::SLUGS as $slug) {
            $speicher->anlegen($slug, 'Platzhalter für den Test. Kein Rechtstext.', $slug === 'avv' || $slug === 'tom' ? 'kunde' : 'oeffentlich');
            $speicher->zustandSetzen($slug, 'freigegeben', 'Testkanzlei');
        }

        $this->assertSame([], $this->startsperre()->hindernisse());

        // Der AVV geht zurueck in den Entwurf.
        $speicher->entwurfSpeichern('avv', 'Überarbeitete Fassung.');

        $this->assertContains(
            'Der Rechtstext „Auftragsverarbeitungsvertrag" ist noch nicht freigegeben.',
            $this->startsperre()->hindernisse()
        );
        $this->assertFalse($this->startsperre()->starterlaubt());
    }

    /** Ein fehlender Text zaehlt wie ein Entwurf — sonst waere die Sperre durch Weglassen umgehbar. */
    public function testFehlenderRechtstextBlockiertEbenfalls(): void
    {
        $this->betreiberdatenAnlegen();

        $this->assertContains('Der Rechtstext „Auftragsverarbeitungsvertrag" ist noch nicht freigegeben.', $this->startsperre()->hindernisse());
        $this->assertContains('Der Rechtstext „Technische und organisatorische Maßnahmen" ist noch nicht freigegeben.', $this->startsperre()->hindernisse());
    }

    /** Eine Freigabe ohne Namen der pruefenden Stelle gibt es nicht (§1.4a). */
    public function testFreigabeOhneNamenDerPruefendenStelleWirdAbgewiesen(): void
    {
        $this->speicher()->anlegen('impressum', 'Platzhalter.', 'oeffentlich');

        $this->expectException(\InvalidArgumentException::class);

        $this->speicher()->zustandSetzen('impressum', 'freigegeben', '   ');
    }

    /** Jede inhaltliche Aenderung setzt den Zustand zurueck und zaehlt die Fassung hoch. */
    public function testAenderungSetztDenZustandZurueck(): void
    {
        $speicher = $this->speicher();
        $speicher->anlegen('agb', 'Erste Fassung.', 'oeffentlich');
        $speicher->zustandSetzen('agb', 'freigegeben', 'Testkanzlei');

        $vorher = $speicher->intern('agb');
        $this->assertSame('freigegeben', $vorher['status']);
        $this->assertSame(1, (int) $vorher['version']);

        $speicher->entwurfSpeichern('agb', 'Zweite Fassung.');

        $nachher = $speicher->intern('agb');
        $this->assertSame('entwurf', $nachher['status']);
        $this->assertSame(2, (int) $nachher['version']);
        $this->assertNull($nachher['released_at']);
        $this->assertNull($nachher['released_by']);
    }

    // ------------------------------------------------------------ Fall 82

    /**
     * Fall 82 — ein Rechtstext mit `audience = kunde` ist oeffentlich NICHT abrufbar und
     * angemeldet sichtbar.
     */
    public function testTextFuerKundenIstOeffentlichNichtAbrufbar(): void
    {
        $speicher = $this->speicher();
        $speicher->anlegen('avv', 'Auftragsverarbeitung, Platzhalter.', 'kunde');
        $speicher->zustandSetzen('avv', 'freigegeben', 'Testkanzlei');

        $this->assertNull($speicher->oeffentlich('avv'), 'Ein Text für Kunden war öffentlich abrufbar.');
        $this->assertIsArray($speicher->fuerKunden('avv'));
        $this->assertIsArray($speicher->intern('avv'));
    }

    /** Und die Gegenprobe ueber die oeffentliche Route. */
    public function testOeffentlicheRouteLiefertNurFreigegebeneUndOeffentlicheTexte(): void
    {
        $seiten = new OeffentlicheSeiten($this->speicher());
        $speicher = $this->speicher();

        // Noch nichts angelegt.
        $this->assertSame(404, $seiten->impressum()->status);

        // Entwurf: weiterhin nicht abrufbar.
        $speicher->anlegen('impressum', 'Platzhalter für den Test.', 'oeffentlich');
        $this->assertSame(404, $seiten->impressum()->status);

        // In Pruefung: immer noch nicht.
        $speicher->zustandSetzen('impressum', 'in_pruefung', null);
        $this->assertSame(404, $seiten->impressum()->status);

        // Erst freigegeben.
        $speicher->zustandSetzen('impressum', 'freigegeben', 'Testkanzlei');
        $antwort = $seiten->impressum();
        $this->assertSame(200, $antwort->status);
        $this->assertStringContainsString('Platzhalter für den Test.', $antwort->rumpf);
    }

    /** Ein Text mit audience = kunde bleibt auch dann 404, wenn er unter /impressum liegt. */
    public function testKundentextUnterOeffentlicherRouteBleibt404(): void
    {
        $speicher = $this->speicher();
        $speicher->anlegen('impressum', 'Platzhalter.', 'kunde');
        $speicher->zustandSetzen('impressum', 'freigegeben', 'Testkanzlei');

        $this->assertSame(404, (new OeffentlicheSeiten($speicher))->impressum()->status);
    }

    private function speicher(): RechtstexteSpeicher
    {
        return new RechtstexteSpeicher($this->pdo);
    }

    private function startsperre(): Startsperre
    {
        return new Startsperre(new BetreiberdatenSpeicher($this->pdo), new RechtstexteSpeicher($this->pdo));
    }

    private function betreiberdatenAnlegen(): void
    {
        (new BetreiberdatenSpeicher($this->pdo))->anlegen([
            'firmenname'                => 'Vorläufig',
            'strasse'                   => 'Vorläufig 1',
            'plz'                       => '01067',
            'ort'                       => 'Dresden',
            'land'                      => 'DE',
            'email'                     => 'betreiber@example.org',
            'inhaltlich_verantwortlich' => 'Vorläufig',
            'steuernummer'              => '000/000/00000',
        ]);
    }
}
