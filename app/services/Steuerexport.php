<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\Admin\AdminBelege;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Helpers\Format;
use Sartu\Helpers\Validate;

/**
 * Die Übergabe an den Steuerberater — `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 8, Fall 95.
 *
 * ## Was er ist
 *
 * Eine Tabelle je Zeitraum mit den zehn Angaben aus §8 — und dazu die Belegdateien selbst,
 * über den Verweis in der letzten Spalte.
 *
 * ## Was er ausdrücklich nicht ist
 *
 * §8: „Der Export **bucht nicht**. Er ordnet keine Konten zu, er ermittelt keine
 * Voranmeldung, er trifft keine steuerliche Aussage. Er übergibt Daten und nichts sonst."
 *
 * Deshalb steht hier kein Kontenrahmen, keine Summenzeile und keine Umsatzsteuerkennzahl.
 * Eine Summe unter einer Tabelle sieht harmlos aus und ist bereits eine Aussage darüber,
 * was zusammengehört.
 *
 * ## Warum CSV und kein DATEV-Format
 *
 * §8: „Welches Format der Steuerberater will, weiß nur er. Eine tabellarische Übergabe plus
 * Belege nimmt jeder; ein falsch geratenes Spezialformat nimmt keiner. **Die Formatfrage
 * wird mit dem Steuerberater geklärt, nicht vorweggenommen.**"
 *
 * ## Warum jeder Vorgang genau einmal erscheint
 *
 * Die Abfrage führt über `invoices`, nicht über `documents` — die Begründung steht an
 * `AdminBelege::export()`. Eine Stornorechnung ist eine eigene Zeile in `invoices` und
 * erscheint deshalb als eigene Zeile mit negativen Beträgen; die aufgehobene Rechnung bleibt
 * mit ihren ursprünglichen Beträgen stehen. Der Zahlungseingang ist keine eigene Zeile,
 * sondern die Spalte `Zahlungsdatum` an seiner Rechnung — er gehört zu genau einer.
 */
final class Steuerexport
{
    /** Die Spalten aus §8, in dieser Reihenfolge. */
    public const SPALTEN = [
        'Belegdatum',
        'Belegnummer',
        'Kundenkennung',
        'Kunde',
        'Netto',
        'Steuersatz',
        'Steuerbetrag',
        'Brutto',
        'Zahlungsdatum',
        'Belegdatei',
    ];

    /** Semikolon: Deutsche Tabellenkalkulationen lesen es als Trenner, Komma nicht. */
    public const TRENNER = ';';

    public function __construct(
        private readonly AdminNachweis $nachweis,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * @return array{fehler:list<string>,inhalt:?string,dateiname:?string,zeilen:int}
     */
    public function erzeugen(string $von, string $bis): array
    {
        $von = trim($von);
        $bis = trim($bis);

        if (!Validate::datum($von) || !Validate::datum($bis)) {
            return ['fehler' => ['Bitte geben Sie beide Daten an — Beginn und Ende des Zeitraums.'],
                'inhalt' => null, 'dateiname' => null, 'zeilen' => 0];
        }

        if ($bis < $von) {
            return ['fehler' => ['Das Ende des Zeitraums liegt vor seinem Beginn.'],
                'inhalt' => null, 'dateiname' => null, 'zeilen' => 0];
        }

        $zeilen = (new AdminBelege($this->nachweis, $this->pdo))->export($von, $bis);

        $ausgabe = self::zeile(self::SPALTEN);

        foreach ($zeilen as $zeile) {
            $ausgabe .= self::zeile([
                Format::datum(substr((string) $zeile['issued_at'], 0, 10)),
                (string) $zeile['number'],
                (string) $zeile['organization_id'],
                (string) $zeile['legal_name'],
                self::euro((int) $zeile['net_cents']),
                // Der Satz steht als Zahl, nicht als Text: 0 bei Kleinunternehmern, sonst 19.
                (string) ((int) $zeile['net_cents'] === 0 ? 0 : (int) round(
                    (int) $zeile['vat_cents'] * 100 / (int) $zeile['net_cents']
                )),
                self::euro((int) $zeile['vat_cents']),
                self::euro((int) $zeile['gross_cents']),
                is_string($zeile['paid_at'] ?? null)
                    ? Format::datum(substr((string) $zeile['paid_at'], 0, 10))
                    : '',
                is_string($zeile['document_path'] ?? null) ? (string) $zeile['document_path'] : '',
            ]);
        }

        return [
            'fehler'    => [],
            // BOM voran: Ohne ihn liest Excel `utf8` als Windows-1252, und aus „Müller"
            // wird „MÃ¼ller". Das ist keine Formatentscheidung, sondern eine Lesbarkeitsfrage.
            'inhalt'    => "\u{FEFF}" . $ausgabe,
            'dateiname' => 'sartu-belege-' . $von . '-bis-' . $bis . '.csv',
            'zeilen'    => count($zeilen),
        ];
    }

    /** @param list<string> $felder */
    private static function zeile(array $felder): string
    {
        $sicher = array_map(static function (string $feld): string {
            // Anführungszeichen im Feld werden verdoppelt — so schreibt es RFC 4180.
            return '"' . str_replace('"', '""', $feld) . '"';
        }, $felder);

        return implode(self::TRENNER, $sicher) . "\r\n";
    }

    /**
     * Betrag als Dezimalzahl mit Komma — ohne Währungszeichen.
     *
     * `Format::euro()` gibt `7.900,00 €` und ist für Menschen gedacht. In einer Tabelle
     * steht eine Zahl, die sich rechnen lässt; das Währungszeichen macht daraus Text.
     */
    private static function euro(int $cent): string
    {
        return number_format($cent / 100, 2, ',', '');
    }
}
