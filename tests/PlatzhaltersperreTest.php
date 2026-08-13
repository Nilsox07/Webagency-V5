<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Services\Gruenderangaben;
use Sartu\Services\Musterprojekte;
use Sartu\Services\Platzhalterpruefung;

/**
 * Startsperre, Bedingung 4 und 4a — `10_WEBSITE_SARTU.md` §5.
 *
 * ## Warum es diese Datei gibt
 *
 * `partials/bildplatz.php` versicherte seit seinem Bau: *„Die Startsperre §14a Bedingung 4
 * sucht genau diese Markierung und bricht die produktive Veröffentlichung ab."*
 * **Das war nicht wahr.** `Startsperre` prüft ausschliesslich Betreiberdaten und Rechtstexte;
 * ihr eigener Klassenkopf sagt es sogar: „Sie prueft nicht auf Platzhalter in Vorlagen."
 *
 * Solange niemand die Markierung sucht, ist ein Bildplatz kein Zwischenstand, sondern ein
 * Risiko: Er sieht aus wie eine bewusste Zwischenstufe, und die Zusicherung daneben sorgt
 * dafür, dass niemand mehr nachsieht.
 *
 * Am 13.08.2026 sind zwei Sektionen dazugekommen, die beide von dieser Zusage leben —
 * Sektion 6 „Wer dahintersteckt" mit `[[FOTO-FEHLT]]` und Sektion 8 „Musterprojekte" mit
 * `[[SCREENSHOT-FEHLT]]`. Diese Datei prüft, dass die Zusage jetzt gedeckt ist.
 *
 * ## Geprüft wird das ausgelieferte Markup
 *
 * Nicht die Vorlage. Eine Suche über `app/views` fände `bildplatz.php` selbst und meldete den
 * Fehler für immer — auch wenn keine Seite den Bildplatz mehr einbindet.
 */
final class PlatzhaltersperreTest extends Datenbankfall
{
    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER = ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_HOST' => 'localhost'];
        putenv('APP_ENV=local');
    }

    /**
     * Bedingung 4a — die Sektion „Wer dahintersteckt" trägt `[[FOTO-FEHLT]]`, und die Sperre
     * findet es.
     *
     * Die drei Gründerfelder sind hier leer: Genau das ist der Zustand, in dem §4d den
     * gekennzeichneten Platzhalter erlaubt. Er darf sichtbar sein — er darf nicht live gehen.
     */
    public function testDieSperreFindetDasFehlendeGruenderfoto(): void
    {
        $this->betreiberdatenOhneGruender();

        $html = $this->rumpf('/');

        $this->assertStringContainsString(Gruenderangaben::MARKIERUNG, $html,
            'Die Sektion „Wer dahintersteckt" liefert keinen gekennzeichneten Platzhalter aus.');

        $hindernisse = (new Platzhalterpruefung(['/', '/ueber-uns']))->hindernisse();

        $this->assertNotSame([], $hindernisse, 'Die Sperre findet den Platzhalter nicht.');

        $treffer = array_values(array_filter(
            $hindernisse,
            static fn (string $h): bool => str_contains($h, Gruenderangaben::MARKIERUNG)
        ));

        $this->assertNotSame([], $treffer, 'Kein Hindernis nennt [[FOTO-FEHLT]].');

        // §5: „Die Fehlermeldung nennt die Ursache" — welche Adresse, welche Bedingung.
        $this->assertStringContainsString('/', $treffer[0]);

        // Beide Seiten mit der Sektion werden erwischt, nicht nur die erste.
        $adressen = array_map(
            static fn (string $h): string => explode(' ', $h)[0],
            $treffer
        );
        $this->assertContains('/ueber-uns', $adressen,
            'Nur die Startseite wird geprüft. Die Sektion steht auf zwei Seiten.');
    }

    /**
     * Bedingung 4 — die Musterprojekte tragen `[[SCREENSHOT-FEHLT]]`, und die Sperre findet es.
     *
     * Ein Bildplatz je Fall, auf der Startseite und auf `/musterprojekte`. Solange keine
     * Beispielseite gebaut ist, ist ein nachgebauter Bildschirm dort ausdrücklich verboten
     * (§8 und `17_SEITEN_SARTU.md` §4a) — der Platz bleibt also, und die Sperre trägt ihn.
     */
    public function testDieSperreFindetDieFehlendenMusterprojektaufnahmen(): void
    {
        $this->betreiberdatenOhneGruender();

        $start = $this->rumpf('/');
        $seite = $this->rumpf(Musterprojekte::PFAD);

        $anzahl = count(Musterprojekte::alle());

        $this->assertSame($anzahl, substr_count($start, '[[SCREENSHOT-FEHLT]] sartu-muster-'),
            'Auf der Startseite fehlt ein Bildplatz je Musterprojekt.');
        $this->assertSame($anzahl, substr_count($seite, '[[SCREENSHOT-FEHLT]] sartu-muster-'),
            'Auf /musterprojekte fehlt ein Bildplatz je Musterprojekt.');

        $hindernisse = (new Platzhalterpruefung(['/', Musterprojekte::PFAD]))->hindernisse();

        foreach (['/', Musterprojekte::PFAD] as $pfad) {
            $treffer = array_filter(
                $hindernisse,
                static fn (string $h): bool => str_starts_with($h, $pfad . ' ')
                    && str_contains($h, '[[SCREENSHOT-FEHLT]]')
            );

            $this->assertNotSame([], $treffer,
                sprintf('Die Sperre meldet %s nicht, obwohl dort ein Bildplatz steht.', $pfad));
        }
    }

    /**
     * Die Gegenprobe: Eine Adresse ohne Platzhalter ist kein Hindernis.
     *
     * Ohne sie wäre der Test von einer Sperre nicht zu unterscheiden, die einfach immer
     * meldet — und die wäre genauso wertlos wie gar keine.
     */
    public function testEineSeiteOhnePlatzhalterMeldetNichts(): void
    {
        $this->assertSame([], (new Platzhalterpruefung(['/preise', '/kontakt']))->hindernisse());
    }

    /** Alle drei Markierungen aus §5 stehen in der Liste — keine wird stillschweigend fallen gelassen. */
    public function testAlleDreiMarkierungenWerdenGesucht(): void
    {
        foreach (['[[PLATZHALTER]]', '[[SCREENSHOT-FEHLT]]', '[[FOTO-FEHLT]]'] as $marke) {
            $this->assertContains($marke, Platzhalterpruefung::MARKIERUNGEN);
        }

        // §5 Bedingung 4a nennt ihn ausdruecklich neben den Markierungen: Er traegt keine
        // Klammern und faellt deshalb durch jede Suche nach `[[`.
        $this->assertContains('Name wird nachgereicht', Platzhalterpruefung::GESPERRTER_WORTLAUT);
    }

    private function rumpf(string $pfad): string
    {
        return (string) (new \Sartu\Website(new BetreiberdatenSpeicher($this->pdo)))
            ->{$pfad === '/' ? 'start' : ($pfad === Musterprojekte::PFAD ? 'musterprojekte' : 'ueberUns')}()
            ->rumpf;
    }

    /**
     * Betreiberdaten ohne die drei Gründerfelder — der Zustand, für den §4d den Platzhalter
     * vorsieht.
     */
    private function betreiberdatenOhneGruender(): void
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
