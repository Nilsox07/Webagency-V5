<?php

declare(strict_types=1);

namespace Sartu\Tests;

use PHPUnit\Framework\TestCase;
use Sartu\Data\BetreiberdatenSpeicher;

/**
 * Portal-Lastenheft §1.2: „PDO mit vorbereiteten Anweisungen, ausnahmslos. Nie
 * Zeichenketten zusammensetzen."
 *
 * Testfall 50 — im Code nachgewiesen, keine zusammengesetzten SQL-Zeichenketten.
 *
 * Der Nachweis ist bewusst eine Quelltextpruefung und kein Laufzeittest: Eine Einschleusung
 * entsteht an der Stelle, an der jemand einen Wert in eine Zeichenkette schreibt. Die faellt
 * zur Laufzeit erst auf, wenn sie ausgenutzt wird.
 */
final class PreparedStatementsTest extends TestCase
{
    /**
     * Kein Datenbankzugriff ausserhalb von /app/data (§1.3).
     *
     * Geprueft wird auf die PDO-Aufrufe selbst, nicht auf SQL-Woerter im Quelltext: Eine
     * Ansicht mit einem <select>-Element ist kein Datenbankzugriff, und ein Test, der das
     * verwechselt, wird irgendwann entschaerft statt befolgt.
     */
    public function testDatenbankzugriffStehtNurInDerDatenzugriffsschicht(): void
    {
        $treffer = [];

        foreach ($this->quelldateien(['app', 'admin', 'portal', 'api']) as $datei) {
            if (str_contains($datei, '/app/data/')) {
                continue;
            }

            $inhalt = $this->ohneKommentare((string) file_get_contents($datei));

            if (preg_match('/->\s*(prepare|query|exec)\s*\(/', $inhalt) === 1) {
                $treffer[] = $datei;
            }
        }

        $this->assertSame([], $treffer, 'Datenbankzugriff außerhalb von /app/data gefunden.');
    }

    /** Und umgekehrt: SQL-Anweisungen stehen ausschliesslich in /app/data. */
    public function testSqlAnweisungenStehenNurInDerDatenzugriffsschicht(): void
    {
        $treffer = [];

        foreach ($this->quelldateien(['app', 'admin', 'portal', 'api']) as $datei) {
            if (str_contains($datei, '/app/data/') || str_contains($datei, '/app/views/')) {
                continue;
            }

            $inhalt = $this->ohneKommentare((string) file_get_contents($datei));

            if (preg_match("/'\\s*(SELECT |INSERT INTO |UPDATE |DELETE FROM |CREATE TABLE |ALTER TABLE )/i", $inhalt) === 1) {
                $treffer[] = $datei;
            }
        }

        $this->assertSame([], $treffer, 'SQL außerhalb von /app/data gefunden.');
    }

    /**
     * Keine SQL-Zeichenkette traegt einen Wert aus einer Variablen.
     *
     * Geprueft werden alle Zeichenketten, die an prepare(), query() oder exec() gehen: Sie
     * duerfen keine Variableninterpolation enthalten und nicht mit einer Variablen
     * verkettet sein.
     */
    public function testKeineSqlZeichenketteTraegtEinenWert(): void
    {
        $treffer = [];

        foreach ($this->quelldateien(['app/data']) as $datei) {
            $inhalt = $this->ohneKommentare((string) file_get_contents($datei));

            // Doppelte Anfuehrungszeichen mit $ darin: klassische Interpolation.
            if (preg_match('/(prepare|query|exec)\s*\(\s*"[^"]*\$/', $inhalt) === 1) {
                $treffer[] = $datei . ' — Variable in einer SQL-Zeichenkette';
            }

            // Verkettung mit einer Variablen direkt im Aufruf.
            if (preg_match('/(prepare|query|exec)\s*\(\s*\'[^\']*\'\s*\.\s*\$/', $inhalt) === 1) {
                $treffer[] = $datei . ' — SQL mit einer Variablen verkettet';
            }

            // Heredoc mit Interpolation.
            if (preg_match('/<<<"?[A-Z]+\s*\n[^;]*\$\w+[^;]*\n\s*[A-Z]+;/s', $inhalt) === 1) {
                $treffer[] = $datei . ' — Heredoc mit Interpolation';
            }
        }

        $this->assertSame([], $treffer);
    }

    /**
     * Die einzigen zusammengesetzten Anweisungen bauen Spalten-, keine Wertlisten — und die
     * Spaltennamen stammen aus einer Konstanten mit reinen Zeichenkettenliteralen.
     *
     * Das ist der Punkt, an dem eine pauschale Regel „nie zusammensetzen" praktisch scheitert:
     * Ein UPDATE ueber siebzehn Felder braucht eine Spaltenliste. Sie darf nur nicht aus einer
     * Anfrage stammen.
     */
    public function testZusammengesetzteSpaltenlistenStammenAusKonstanten(): void
    {
        foreach ([BetreiberdatenSpeicher::SCHREIBBARE_FELDER, BetreiberdatenSpeicher::PFLICHTFELDER] as $liste) {
            foreach ($liste as $feld) {
                $this->assertIsString($feld);
                $this->assertMatchesRegularExpression(
                    '/^[a-z_]+$/',
                    $feld,
                    'Ein Spaltenname in der Konstanten ist kein einfacher Bezeichner.'
                );
            }
        }

        $spiegel = new \ReflectionClass(BetreiberdatenSpeicher::class);

        foreach (['SCHREIBBARE_FELDER', 'PFLICHTFELDER'] as $name) {
            $this->assertTrue(
                $spiegel->getReflectionConstant($name)?->isPublic(),
                'Die Feldliste ist keine Konstante mehr.'
            );
        }
    }

    /** Jede vorbereitete Anweisung bekommt ihre Werte ueber execute(). */
    public function testJedePrepareAnweisungWirdMitParameternAusgefuehrt(): void
    {
        $ohneParameter = [];

        foreach ($this->quelldateien(['app/data']) as $datei) {
            $inhalt = $this->ohneKommentare((string) file_get_contents($datei));

            // execute() ohne Argument ist zulaessig, wenn die Anweisung keine Platzhalter
            // hat. Ein Fragezeichen im SQL ohne execute([...]) waere dagegen ein Fehler.
            preg_match_all('/->prepare\(\s*(.+?)\)\s*;/s', $inhalt, $treffer);

            foreach ($treffer[1] ?? [] as $anweisung) {
                if (str_contains($anweisung, '?') && !str_contains($inhalt, 'execute([')) {
                    $ohneParameter[] = $datei;
                }
            }
        }

        $this->assertSame([], $ohneParameter);
    }

    /** Die Nachbildung vorbereiteter Anweisungen im Treiber ist abgeschaltet. */
    public function testEchteVorbereiteteAnweisungen(): void
    {
        $inhalt = (string) file_get_contents(SARTU_WURZEL . '/app/data/Db.php');

        $this->assertStringContainsString('ATTR_EMULATE_PREPARES   => false', $inhalt);
    }

    /**
     * @param list<string> $verzeichnisse
     * @return list<string>
     */
    private function quelldateien(array $verzeichnisse): array
    {
        $dateien = [];

        foreach ($verzeichnisse as $verzeichnis) {
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

        sort($dateien);

        return $dateien;
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
}
