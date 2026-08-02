<?php

declare(strict_types=1);

namespace Sartu\Data\Customer;

use Sartu\Data\Db;

/**
 * Die letzten Ereignisse, die den Kunden betreffen — Portal-Lastenheft §8.1 Block 4.
 *
 * ## Ein Pruefprotokoll ist keine Kundenansicht
 *
 * `audit_events` traegt Adminereignisse, IP-Adressen, Begruendungen und Feldwerte wie
 * `qa_failed`. §3 Regel 12: „Der Kunde sieht nie einen Systemcode." Aus dieser Tabelle darf
 * deshalb **nichts durchgereicht** werden — weder `reason` noch `old_value`/`new_value`,
 * weder `ip` noch `detail`.
 *
 * Diese Klasse gibt genau zwei Dinge zurueck: einen **Klartext** aus der Zuordnung unten und
 * ein **Datum**. Sie kann gar nicht mehr zurueckgeben; es steht nicht in der Abfrage.
 *
 * ## Genau fuenf Ereignisse, keine sechste
 *
 * §8.1 nennt fuenf Klartexte und keinen weiteren. Wer eine Zeile hinzufuegt, erfindet einen
 * Klartext, den niemand festgelegt hat — genau die Luecke, die den Block bis zum 02.08.2026
 * ungebaut liess.
 *
 * | Klartext laut §8.1 | Ereignis | Zusatzbedingung |
 * |---|---|---|
 * | `Angebot angenommen` | `angebot_angenommen` | — |
 * | `Zahlung eingegangen` | `zahlungsstatus_geaendert` | nur wenn ein Betrag verbucht ist |
 * | `Vorschau bereitgestellt` | `projektstatus_geaendert` | `new_value = vorschau` |
 * | `Feedback eingereicht` | `korrekturrunde_eingereicht` | — |
 * | `Website online` | `projektstatus_geaendert` | `new_value = live` |
 *
 * ## Warum die Bedingungen in der Abfrage stehen und nicht in PHP
 *
 * `projektstatus_geaendert` tritt bei **jedem** der elf Zustaende auf. Wer alle liest und in
 * PHP filtert, holt Adminereignisse in den Anwendungsspeicher, um sie dort wegzuwerfen — und
 * die naechste Aenderung vergisst den Filter. Die Abfrage laesst sie gar nicht erst herein.
 *
 * ## Mandantentrennung
 *
 * `organization_id` kommt aus `KundenBereich`, also aus der Sitzung (§3 Regel 1). Ein
 * Ereignis ohne Organisation — Ersteinrichtung, Adminanmeldung — hat `NULL` und faellt damit
 * aus dem Vergleich heraus, ohne dass es dafuer eine eigene Bedingung braeuchte.
 */
final class KundenAktivitaet
{
    /** §8.1: „die letzten **fuenf** fuer den Kunden relevanten Ereignisse". */
    public const ANZAHL = 5;

    /** Die Klartexte aus §8.1, in der Reihenfolge, in der §8.1 sie nennt. */
    public const KLARTEXT = [
        'angebot_angenommen'       => 'Angebot angenommen',
        'zahlung_eingegangen'      => 'Zahlung eingegangen',
        'vorschau_bereitgestellt'  => 'Vorschau bereitgestellt',
        'feedback_eingereicht'     => 'Feedback eingereicht',
        'website_online'           => 'Website online',
    ];

    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * @return list<array{text:string,datum:string}> juengste zuerst, hoechstens `ANZAHL`
     */
    public function letzte(): array
    {
        // Die Zuordnung steht im `CASE`, nicht in PHP: Was die Abfrage nicht benennt, kann
        // sie auch nicht zurueckgeben. Ausgewaehlt werden zwei Spalten — mehr gibt es nicht.
        $anweisung = $this->pdo()->prepare(
            'SELECT created_at, CASE'
            . " WHEN action = 'angebot_angenommen' THEN 'angebot_angenommen'"
            . " WHEN action = 'zahlungsstatus_geaendert' THEN 'zahlung_eingegangen'"
            . " WHEN action = 'korrekturrunde_eingereicht' THEN 'feedback_eingereicht'"
            . " WHEN new_value = 'vorschau' THEN 'vorschau_bereitgestellt'"
            . " ELSE 'website_online' END AS schluessel"
            . ' FROM audit_events'
            . ' WHERE organization_id = ? AND ('
            . "   action IN ('angebot_angenommen','korrekturrunde_eingereicht')"
            . "   OR (action = 'zahlungsstatus_geaendert' AND new_value IN ('teilweise_bezahlt','bezahlt'))"
            . "   OR (action = 'projektstatus_geaendert' AND new_value IN ('vorschau','live'))"
            // **Die 5 steht ausgeschrieben, nicht als Platzhalter und nicht verkettet.**
            // `execute()` uebergibt jeden Wert als Zeichenkette, und `LIMIT '5'` ist ein
            // Syntaxfehler; `bindValue` mit `PARAM_INT` waere der Ausweg, kaeme aber an
            // `PreparedStatementsTest` vorbei, der jedes `?` an ein `execute([...])` bindet.
            // Diesen Waechter fuer eine Bequemlichkeit aufzuweichen waere der schlechtere
            // Tausch. Dass die 5 hier und in `ANZAHL` uebereinstimmen, prueft ein Test.
            . ' ) ORDER BY created_at DESC, id DESC LIMIT 5'
        );
        $anweisung->execute([$this->bereich->organisationId]);

        $zeilen = [];

        foreach ($anweisung->fetchAll() as $zeile) {
            $schluessel = (string) $zeile['schluessel'];

            // Ein unbekannter Schluessel kann aus der Abfrage nicht kommen. Käme er doch,
            // faellt die Zeile weg — lieber eine Zeile weniger als ein Systemcode.
            if (!isset(self::KLARTEXT[$schluessel])) {
                continue;
            }

            $zeilen[] = [
                'text'  => self::KLARTEXT[$schluessel],
                'datum' => (string) $zeile['created_at'],
            ];
        }

        return $zeilen;
    }

    private function pdo(): \PDO
    {
        return $this->pdo ?? Db::verbindung();
    }
}
