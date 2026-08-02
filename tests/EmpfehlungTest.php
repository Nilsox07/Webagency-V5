<?php

declare(strict_types=1);

namespace Sartu\Tests;

use PHPUnit\Framework\TestCase;
use Sartu\Services\Empfehlung;

/**
 * Die deterministische Ampel — Masterkonzept §8, Website-Lastenheft §9.2 und §9.3.
 *
 * Teil von Testfall 39: „Empfehlung und Ampelkennzeichen werden serverseitig gesetzt — ein
 * manipuliertes Formularfeld ändert sie nicht." Der zweite Teil dieses Falls liegt im
 * `AnfrageService`; hier steht die Regel selbst.
 *
 * Diese Datei ist bewusst ausführlich. Zwischen `wachstum` und `sonderprojekt` liegen
 * 8.600 € Einmalpreis — ein falscher Zweig ist ein falsches Angebot.
 */
final class EmpfehlungTest extends TestCase
{
    // ------------------------------------------------------------ Standard: der Umfang

    public function testOhneStarkesSignalKommtStart(): void
    {
        $ergebnis = Empfehlung::bestimmen(
            [Empfehlung::SIGNAL_HAUPTANGEBOT],
            [Empfehlung::GATE_NICHTS_DAVON],
        );

        $this->assertSame('start', $ergebnis['paket']);
        $this->assertSame('standard', $ergebnis['ampel']);
    }

    public function testEinStarkesSignalGibtWachstum(): void
    {
        foreach ([
            Empfehlung::SIGNAL_MEHRERE_LEISTUNGEN,
            Empfehlung::SIGNAL_MEHRERE_REGIONEN,
            Empfehlung::SIGNAL_RECRUITING,
            Empfehlung::SIGNAL_PROJEKTE_AKTUELL,
        ] as $signal) {
            $ergebnis = Empfehlung::bestimmen([$signal], [Empfehlung::GATE_NICHTS_DAVON]);

            $this->assertSame('wachstum', $ergebnis['paket'], 'Signal: ' . $signal);
            $this->assertSame('standard', $ergebnis['ampel']);
        }
    }

    /** §8: „Platzhirsch bei ≥ 2 starken Signalen." */
    public function testZweiStarkeSignaleGebenPlatzhirsch(): void
    {
        $ergebnis = Empfehlung::bestimmen(
            [Empfehlung::SIGNAL_MEHRERE_LEISTUNGEN, Empfehlung::SIGNAL_RECRUITING],
            [Empfehlung::GATE_NICHTS_DAVON],
        );

        $this->assertSame('platzhirsch', $ergebnis['paket']);
        $this->assertSame('standard', $ergebnis['ampel']);
    }

    /** Das Beispiel aus Website §9.3 — drei Signale, Platzhirsch. */
    public function testDasBeispielAusDemLastenheft(): void
    {
        $ergebnis = Empfehlung::bestimmen(
            [
                Empfehlung::SIGNAL_MEHRERE_LEISTUNGEN,
                Empfehlung::SIGNAL_MEHRERE_REGIONEN,
                Empfehlung::SIGNAL_RECRUITING,
            ],
            [Empfehlung::GATE_FORMULAR],
        );

        $this->assertSame('platzhirsch', $ergebnis['paket']);
        $this->assertSame('standard', $ergebnis['ampel']);
    }

    /** „Ein klares Hauptangebot" ist kein starkes Signal — es zeigt nach unten, nicht nach oben. */
    public function testHauptangebotZaehltNichtAlsStarkesSignal(): void
    {
        $ergebnis = Empfehlung::bestimmen(
            [Empfehlung::SIGNAL_HAUPTANGEBOT, Empfehlung::SIGNAL_MEHRERE_LEISTUNGEN],
            [Empfehlung::GATE_NICHTS_DAVON],
        );

        $this->assertSame('wachstum', $ergebnis['paket'], 'Hauptangebot wurde als starkes Signal gezählt.');
    }

    // ------------------------------------------------------------ Rot: Sonderprojekt

    /** §8: Shop, Login, Schnittstelle, getrennte Marken, besondere Daten → Sonderprojekt. */
    public function testJedesRoteGateFuehrtZumSonderprojekt(): void
    {
        foreach ([
            Empfehlung::GATE_SHOP,
            Empfehlung::GATE_LOGIN,
            Empfehlung::GATE_SCHNITTSTELLE,
            Empfehlung::GATE_GETRENNTE_MARKEN,
            Empfehlung::GATE_BESONDERE_DATEN,
        ] as $gate) {
            $ergebnis = Empfehlung::bestimmen([Empfehlung::SIGNAL_HAUPTANGEBOT], [$gate]);

            $this->assertSame('sonderprojekt', $ergebnis['paket'], 'Gate: ' . $gate);
            $this->assertSame('rot', $ergebnis['ampel'], 'Gate: ' . $gate);
            $this->assertNotSame([], $ergebnis['gruende'], 'Rot ohne Begründung.');
        }
    }

    /** Rot schlägt alles — auch drei starke Signale, die sonst Platzhirsch ergäben. */
    public function testRotSchlaegtDenUmfang(): void
    {
        $ergebnis = Empfehlung::bestimmen(
            [
                Empfehlung::SIGNAL_MEHRERE_LEISTUNGEN,
                Empfehlung::SIGNAL_MEHRERE_REGIONEN,
                Empfehlung::SIGNAL_RECRUITING,
            ],
            [Empfehlung::GATE_SHOP, Empfehlung::GATE_MEHRERE_SPRACHEN],
        );

        $this->assertSame('sonderprojekt', $ergebnis['paket']);
        $this->assertSame('rot', $ergebnis['ampel']);
    }

    // ------------------------------------- Orange: die beiden Entscheidungen vom 02.08.2026

    /**
     * „Getrennte Marken" ist rot, „mehrere Sprachen" ist orange.
     *
     * Vor der Aufteilung standen beide in **einer** Auswahl. Das Regelwerk stuft sie
     * verschieden ein, und zwischen den Farben liegt der Sprung von 3.900 € auf 12.500 €.
     */
    public function testSprachenUndMarkenSindGetrennteFaelle(): void
    {
        $sprachen = Empfehlung::bestimmen(
            [Empfehlung::SIGNAL_HAUPTANGEBOT],
            [Empfehlung::GATE_MEHRERE_SPRACHEN],
        );

        $this->assertSame('orange', $sprachen['ampel']);
        $this->assertSame('start', $sprachen['paket'], 'Mehrere Sprachen sind kein Sonderprojekt.');

        $marken = Empfehlung::bestimmen(
            [Empfehlung::SIGNAL_HAUPTANGEBOT],
            [Empfehlung::GATE_GETRENNTE_MARKEN],
        );

        $this->assertSame('rot', $marken['ampel']);
        $this->assertSame('sonderprojekt', $marken['paket']);
    }

    /** Entschieden am 02.08.2026: Eine Terminbuchung geht in die persönliche Prüfung. */
    public function testTerminbuchungIstOrange(): void
    {
        $ergebnis = Empfehlung::bestimmen(
            [Empfehlung::SIGNAL_MEHRERE_LEISTUNGEN],
            [Empfehlung::GATE_TERMINBUCHUNG],
        );

        $this->assertSame('orange', $ergebnis['ampel']);
        $this->assertSame('wachstum', $ergebnis['paket'], 'Orange ändert die Prüfung, nicht den Umfang.');
    }

    /** Ein normales Anfrageformular ist keine Sonderfunktion. */
    public function testFormularIstKeineSonderfunktion(): void
    {
        $ergebnis = Empfehlung::bestimmen([Empfehlung::SIGNAL_HAUPTANGEBOT], [Empfehlung::GATE_FORMULAR]);

        $this->assertSame('standard', $ergebnis['ampel']);
    }

    // ------------------------------------------------------------ Gelb: eine Rückfrage

    /** §8: „unklar an paketentscheidender Stelle" — Thema 3 entscheidet das Paket. */
    public function testUnklarerUmfangGibtKeineEmpfehlung(): void
    {
        $ergebnis = Empfehlung::bestimmen(
            [Empfehlung::SIGNAL_NICHTS_DAVON],
            [Empfehlung::GATE_NICHTS_DAVON],
        );

        $this->assertSame('unklar', $ergebnis['paket']);
        $this->assertSame('gelb', $ergebnis['ampel']);
        $this->assertContains('Der Umfang ist noch offen.', $ergebnis['gruende']);
    }

    public function testJedeUnklarheitSetztGelb(): void
    {
        $faelle = [
            'bestehende Website' => ['bestehendeWebsiteUnklar' => true],
            'Zielgruppe'         => ['zielgruppeUnklar' => true],
            'Domain'             => ['domainUnklar' => true],
            'fester Termin'      => ['festerTermin' => true],
        ];

        foreach ($faelle as $bezeichnung => $zusatz) {
            $ergebnis = Empfehlung::bestimmen(
                [Empfehlung::SIGNAL_HAUPTANGEBOT],
                [Empfehlung::GATE_NICHTS_DAVON],
                ...$zusatz,
            );

            $this->assertSame('gelb', $ergebnis['ampel'], $bezeichnung);
            $this->assertCount(1, $ergebnis['gruende'], $bezeichnung);
            $this->assertSame('start', $ergebnis['paket'], $bezeichnung . ': Gelb ändert den Umfang nicht.');
        }
    }

    /** Rot und Orange stehen über Gelb — die strengere Prüfung gewinnt. */
    public function testStrengereAmpelGewinnt(): void
    {
        $ergebnis = Empfehlung::bestimmen(
            [Empfehlung::SIGNAL_NICHTS_DAVON],
            [Empfehlung::GATE_MEHRERE_SPRACHEN],
            domainUnklar: true,
        );

        $this->assertSame('orange', $ergebnis['ampel']);

        $ergebnis = Empfehlung::bestimmen(
            [Empfehlung::SIGNAL_NICHTS_DAVON],
            [Empfehlung::GATE_SHOP],
            domainUnklar: true,
        );

        $this->assertSame('rot', $ergebnis['ampel']);
    }

    // ------------------------------------------------------------ Vollständigkeit

    /** Jede Auswahlmöglichkeit aus Thema 4 ist genau einer Farbe zugeordnet. */
    public function testJedesGateHatGenauEineFarbe(): void
    {
        $farben = [];

        foreach (Empfehlung::alleGates() as $gate) {
            $farben[$gate] = Empfehlung::bestimmen([Empfehlung::SIGNAL_HAUPTANGEBOT], [$gate])['ampel'];
        }

        $this->assertSame([
            Empfehlung::GATE_FORMULAR         => 'standard',
            Empfehlung::GATE_TERMINBUCHUNG    => 'orange',
            Empfehlung::GATE_SHOP             => 'rot',
            Empfehlung::GATE_LOGIN            => 'rot',
            Empfehlung::GATE_SCHNITTSTELLE    => 'rot',
            Empfehlung::GATE_MEHRERE_SPRACHEN => 'orange',
            Empfehlung::GATE_GETRENNTE_MARKEN => 'rot',
            Empfehlung::GATE_BESONDERE_DATEN  => 'rot',
            Empfehlung::GATE_NICHTS_DAVON     => 'standard',
        ], $farben);
    }

    /** Das Ergebnis ist ein Wert aus der erlaubten Liste — sonst weist die Datenbank es ab. */
    public function testErgebnisPasstZurPruefbedingungDerTabelle(): void
    {
        foreach (Empfehlung::alleSignale() as $signal) {
            foreach (Empfehlung::alleGates() as $gate) {
                $ergebnis = Empfehlung::bestimmen([$signal], [$gate]);

                $this->assertContains(
                    $ergebnis['paket'],
                    ['start', 'wachstum', 'platzhirsch', 'sonderprojekt', 'unklar'],
                );
                $this->assertContains($ergebnis['ampel'], ['standard', 'gelb', 'orange', 'rot']);
            }
        }
    }
}
