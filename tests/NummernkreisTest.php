<?php

declare(strict_types=1);

namespace Sartu\Tests;

use Sartu\Data\Nummernkreise;
use Sartu\Services\Nummernkreis;

/**
 * Der lückenlose Belegzähler — Fälle 84 und 85.
 *
 * `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 4: „Je Art und Jahr fortlaufend,
 * beginnend bei `001`, ohne Lücke." Und: „Zwei gleichzeitige Vorgänge bekommen nie dieselbe
 * Nummer und lassen nie eine aus."
 *
 * **Fall 85 läuft mit echten Prozessen.** Zwei Aufrufe hintereinander prüfen die Zeilensperre
 * nicht — sie streiten nie um dieselbe Zeile. Der Fall startet deshalb mehrere
 * Betriebssystemprozesse gleichzeitig; was genau dabei ungeprüft bleibt, steht in
 * `OFFENE_PRUEFUNGEN.md`.
 */
final class NummernkreisTest extends Datenbankfall
{
    /** So viele Prozesse laufen in Fall 85 gegeneinander. */
    private const GLEICHZEITIG = 8;

    /**
     * Fall 84 — die Nummer wird vergeben, nicht eingegeben. Form `RE-JJJJ-NNN`, im neuen
     * Jahr beginnt der Zähler wieder bei `001`.
     */
    public function testDieNummerWirdVergebenUndBeginntImNeuenJahrWiederBeiEins(): void
    {
        $dienst = new Nummernkreis(pdo: $this->pdo);

        $this->assertSame('RE-2026-001', $this->vergeben($dienst, 'RE', 2026));
        $this->assertSame('RE-2026-002', $this->vergeben($dienst, 'RE', 2026));
        $this->assertSame('RE-2026-003', $this->vergeben($dienst, 'RE', 2026));

        // Neues Jahr, neuer Zähler — und der alte bleibt, wo er war.
        $this->assertSame('RE-2027-001', $this->vergeben($dienst, 'RE', 2027));
        $this->assertSame('RE-2026-004', $this->vergeben($dienst, 'RE', 2026));

        // Jede Art zählt für sich. Ein gemeinsamer Zähler hätte in beiden Reihen Lücken.
        $this->assertSame('AN-2026-001', $this->vergeben($dienst, 'AN', 2026));
        $this->assertSame('ST-2026-001', $this->vergeben($dienst, 'ST', 2026));

        $this->assertSame(4, $dienst->stand('RE', 2026));
        $this->assertSame(1, $dienst->stand('AN', 2026));
    }

    /** Eine unbekannte Belegart wird abgewiesen, nicht angelegt. */
    public function testEineUnbekannteBelegartWirdAbgewiesen(): void
    {
        $this->expectException(\RuntimeException::class);

        (new Nummernkreis(pdo: $this->pdo))->vergeben('XX', static fn (string $n): string => $n, 2026);
    }

    /**
     * Ohne laufende Transaktion gibt es keine Nummer.
     *
     * Der Zähler wird sonst gelesen und freigegeben, bevor der Beleg steht — genau die
     * Lücke, die Abschnitt 4 verbietet. Geprüft wird die Sperre am Datenzugriff selbst,
     * weil nur dort jemand daran vorbeikäme.
     */
    public function testOhneTransaktionGibtEsKeineNummer(): void
    {
        $this->expectException(\RuntimeException::class);

        (new Nummernkreise($this->pdo))->naechste('RE', 2026);
    }

    /**
     * Scheitert das Anlegen, bleibt der Zähler stehen.
     *
     * Abschnitt 4: „Der Beleg entsteht erst mit der Nummer." Eine Nummer, die keinen Beleg
     * bekam, wäre eine Lücke — also wird sie zurückgerollt.
     */
    public function testEinGescheitertesAnlegenLaesstDenZaehlerStehen(): void
    {
        $dienst = new Nummernkreis(pdo: $this->pdo);

        $this->assertSame('RE-2026-001', $this->vergeben($dienst, 'RE', 2026));

        try {
            $dienst->vergeben('RE', static function (string $nummer): string {
                throw new \RuntimeException('Der Beleg lässt sich nicht anlegen.');
            }, 2026);

            $this->fail('Der Fehler kam nicht durch.');
        } catch (\RuntimeException) {
            // erwartet
        }

        $this->assertSame(1, $dienst->stand('RE', 2026), 'Der Zähler ist trotz Rollback gewandert.');
        $this->assertSame('RE-2026-002', $this->vergeben($dienst, 'RE', 2026));
    }

    /**
     * Fall 85 — mehrere **gleichzeitige** Vorgänge, verschiedene Nummern, keine ausgelassen.
     *
     * Acht Betriebssystemprozesse starten zusammen, jeder hält seine Transaktion 50 ms offen.
     * Ohne die Zeilensperre läsen alle denselben Stand und lieferten dieselbe Nummer — die
     * Pause ist genau dafür da, dass sie sich wirklich überschneiden.
     */
    public function testGleichzeitigeVergabenBekommenVerschiedeneNummernOhneLuecke(): void
    {
        $nummern = $this->gleichzeitigVergeben(self::GLEICHZEITIG);

        $this->assertCount(self::GLEICHZEITIG, $nummern, 'Nicht jeder Prozess hat eine Nummer bekommen.');
        $this->assertSame(
            self::GLEICHZEITIG,
            count(array_unique($nummern)),
            'Zwei Prozesse haben dieselbe Nummer bekommen: ' . implode(', ', $nummern),
        );

        // Lückenlos von 001 an, in der Reihenfolge der Zahlen — nicht der Prozesse.
        $erwartet = [];

        for ($i = 1; $i <= self::GLEICHZEITIG; ++$i) {
            $erwartet[] = sprintf('RE-2026-%03d', $i);
        }

        sort($nummern);

        $this->assertSame($erwartet, $nummern, 'Der Nummernkreis hat eine Lücke.');
        $this->assertSame(self::GLEICHZEITIG, (new Nummernkreis(pdo: $this->pdo))->stand('RE', 2026));
    }

    // ---------------------------------------------------------------- Hilfsmittel

    private function vergeben(Nummernkreis $dienst, string $art, int $jahr): string
    {
        return $dienst->vergeben($art, static fn (string $nummer): string => $nummer, $jahr);
    }

    /**
     * Startet `$anzahl` Prozesse **zusammen** und sammelt ihre Nummern ein.
     *
     * Erst werden alle geöffnet, dann wird gewartet. Umgekehrt liefe der erste durch, bevor
     * der zweite startet — und der Fall prüfte wieder nur ein Nacheinander.
     *
     * @return list<string>
     */
    private function gleichzeitigVergeben(int $anzahl): array
    {
        $programm = SARTU_WURZEL . '/tests/hilfsmittel/nummer-vergeben.php';

        $this->assertFileExists($programm);

        $laeufe = [];

        for ($i = 0; $i < $anzahl; ++$i) {
            $rohre = [];
            $prozess = proc_open(
                [PHP_BINARY, $programm, 'RE', '2026', '50000'],
                [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
                $rohre,
                SARTU_WURZEL,
            );

            $this->assertIsResource($prozess, 'Der Prozess liess sich nicht starten.');

            $laeufe[] = ['prozess' => $prozess, 'rohre' => $rohre];
        }

        $nummern = [];

        foreach ($laeufe as $lauf) {
            $ausgabe = trim((string) stream_get_contents($lauf['rohre'][1]));
            $fehler = trim((string) stream_get_contents($lauf['rohre'][2]));

            fclose($lauf['rohre'][1]);
            fclose($lauf['rohre'][2]);

            $stand = proc_close($lauf['prozess']);

            $this->assertSame(0, $stand, 'Ein Prozess ist gescheitert: ' . $fehler);

            $nummern[] = $ausgabe;
        }

        return $nummern;
    }
}
