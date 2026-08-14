<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Die sechzehn Landesprogramme für `/foerderung` — `17_SEITEN_SARTU.md` §6, Block 5.
 *
 * ## Herkunft jeder Zeile
 *
 * `FOERDERUNG_KONZEPT.md` §4a: **alle sechzehn Länder am 09.08.2026 an der Primärquelle
 * geprüft** — Förderbank oder Landesportal, nicht Sekundärübersicht. Die Sperre, die
 * `17_SEITEN_SARTU.md` daneben führt („kein Programmname ist in der Primärquelle geprüft"),
 * stammt aus der Fassung **vor** dieser Recherche und ist damit erledigt; `CLAUDE.md` führt
 * `FOERDERUNG_KONZEPT.md` mit „alle 16 Länder an der Primärquelle geprüft".
 *
 * ## Was hier nicht steht, und warum
 *
 * **Keine Beträge.** §6 Grenze 1: „Block 5 nennt Programmname, Link und Status, **keine
 * Summen**." Die Beträge stehen im Konzept zur internen Orientierung; auf der Kundenseite
 * verfallen sie und werden dann gegen SARTU zitiert.
 *
 * **Keine Adressen der Förderbanken.** Der Aufbau verlangt einen Link je Land. In den
 * Unterlagen steht **keine einzige** Adresse — weder in `FOERDERUNG_KONZEPT.md` noch
 * sonstwo im Bestand. Eine erfundene Adresse führt einen Betrieb, der gerade investieren
 * will, ins Leere; das ist schlimmer als eine fehlende. Genannt wird deshalb die **Stelle**,
 * bei der der Antrag läuft. Der fehlende Link steht in `OFFENE_PRUEFUNGEN.md`.
 *
 * **Keine interne Einschätzung.** `FOERDERUNG_KONZEPT.md` führt vier Länder, in denen ein
 * SARTU-Vorhaben intern als unwahrscheinlich gilt. Ausdrücklich: „auf `/foerderung` steht sie
 * nicht, weil nur die Förderbank im Einzelfall entscheidet."
 */
final class Foerderprogramme
{
    /**
     * Der Tag, an dem jede Zeile an der Primärquelle geprüft wurde.
     *
     * §6, Pflichtangabe: **sichtbares Prüfdatum an Block 5**, nicht im Fußbereich. Eine
     * Förderübersicht ohne Datum ist eine Übersicht, deren Alter niemand kennt.
     */
    public const GEPRUEFT_AM = '2026-08-09';

    /**
     * Die vier Statusstufen — `FOERDERUNG_KONZEPT.md` §4a, „Vier Statusarten, nicht zwei".
     *
     * „läuft / läuft nicht" ist zu grob: Thüringen ist das Lehrstück — das Programm
     * existiert, zahlt aber gerade nicht. Eine Tabelle ohne diese Unterscheidung wäre formal
     * richtig und praktisch falsch.
     */
    public const STATUS = [
        'laeuft'        => 'läuft',
        'aufruf'        => 'Aufrufverfahren',
        'erschoepft'    => 'Mittel ausgeschöpft',
        'beendet'       => 'beendet',
        'ohneumsetzung' => 'Umsetzung nicht förderfähig',
    ];

    /**
     * Alle sechzehn Länder — **keines wird weggelassen.**
     *
     * §6, Block 5: „Ob er antragsberechtigt ist, prüft der Betrieb selbst an der Bedingung."
     * Eine frühere Fassung des Konzepts liess vier Länder ausscheiden; der Betreiber hat das
     * am 09.08.2026 zurückgenommen — die Bedingung je Land ist der eigentliche Inhalt.
     *
     * @return list<array{land:string,kuerzel:string,programm:string,stelle:string,bedingung:string,status:string}>
     */
    public static function alle(): array
    {
        return [
            ['kuerzel' => 'BW', 'land' => 'Baden-Württemberg',
             'programm' => 'Digitalisierungsprämie Plus', 'stelle' => 'L-Bank',
             'status' => 'laeuft',
             'bedingung' => 'Gewerbliche Unternehmen und Freiberufler bis 500 Beschäftigte. '
                . 'Investition im Land. Sperrfrist ein Jahr nach einer vorigen Förderung.'],

            ['kuerzel' => 'BY', 'land' => 'Bayern',
             'programm' => 'Digitalbonus Standard und Plus', 'stelle' => 'Bezirksregierungen',
             'status' => 'laeuft',
             'bedingung' => 'Kleine Unternehmen der gewerblichen Wirtschaft unter 50 '
                . 'Beschäftigten, Sitz in Bayern. Antrag vor Beginn. Standard-Websites und '
                . 'Suchmaschinenoptimierung sind ausdrücklich ausgeschlossen.'],

            ['kuerzel' => 'BE', 'land' => 'Berlin',
             'programm' => 'Transfer BONUS', 'stelle' => 'IBB',
             'status' => 'laeuft',
             'bedingung' => 'Technologieorientierte KMU oder Vorhaben mit ausgeprägtem '
                . 'Technologiebezug. Die Digitalprämie Berlin ist seit Ende 2023 nicht mehr '
                . 'beantragbar.'],

            ['kuerzel' => 'BB', 'land' => 'Brandenburg',
             'programm' => 'BIG-Digital', 'stelle' => 'ILB',
             'status' => 'laeuft',
             'bedingung' => 'KMU einschliesslich Handwerk. Gefördert wird die Analyse '
                . 'betrieblicher Abläufe auf Innovationspotenziale. Antrag vor Beginn.'],

            ['kuerzel' => 'HB', 'land' => 'Bremen',
             'programm' => 'Digitaler Mittelstand KI', 'stelle' => 'BAB',
             'status' => 'laeuft',
             'bedingung' => 'KMU und Soloselbstständige im Haupterwerb mit Sitz oder '
                . 'Betriebsstätte im Land Bremen. Schwerpunkt KI, automatisierte Prozesse, '
                . 'Cybersicherheit. Bearbeitung in Eingangsreihenfolge.'],

            ['kuerzel' => 'HH', 'land' => 'Hamburg',
             'programm' => 'Hamburg-Kredit Digital', 'stelle' => 'IFB',
             'status' => 'laeuft',
             'bedingung' => 'KMU nach EU-Definition mit Sitz oder Betriebsstätte in Hamburg. '
                . 'Es ist ein Darlehen mit Tilgungszuschuss, beantragt über die Hausbank, mit '
                . 'einer Mindesthöhe.'],

            ['kuerzel' => 'HE', 'land' => 'Hessen',
             'programm' => 'DIGI-Zuschuss', 'stelle' => 'WIBank',
             'status' => 'beendet',
             'bedingung' => 'Im Juni 2026 beendet, keine weiteren Aufrufe.'],

            ['kuerzel' => 'MV', 'land' => 'Mecklenburg-Vorpommern',
             'programm' => 'Digitalisierungsförderung Mittelstand', 'stelle' => 'TBI',
             'status' => 'laeuft',
             'bedingung' => 'KMU unter 100 Beschäftigten aus Produktion, Handwerk oder '
                . 'Tourismus. Betriebsstätte im Land, Vorhaben überwiegend im Land.'],

            ['kuerzel' => 'NI', 'land' => 'Niedersachsen',
             'programm' => 'Digitalbonus.Niedersachsen – innovativ', 'stelle' => 'NBank',
             'status' => 'beendet',
             'bedingung' => 'Ausgelaufen.'],

            ['kuerzel' => 'NW', 'land' => 'Nordrhein-Westfalen',
             'programm' => 'MID — Digitalisierung, Digitale Sicherheit, AssistentIn',
             'stelle' => 'Land Nordrhein-Westfalen', 'status' => 'laeuft',
             'bedingung' => 'Kleinst-, kleine und mittlere Unternehmen im Land. '
                . 'Gutscheinsystem — eingelöst wird er bei externen Fachleuten.'],

            ['kuerzel' => 'RP', 'land' => 'Rheinland-Pfalz',
             'programm' => 'DigiBoost, Beratungsprogramm, IBI-EFRE', 'stelle' => 'ISB',
             'status' => 'ohneumsetzung',
             'bedingung' => 'Kleine und mittlere gewerbliche Unternehmen mit Sitz im Land. '
                . 'Ein Sachverständiger beurteilt die Eignung im Antragsverfahren.'],

            ['kuerzel' => 'SL', 'land' => 'Saarland',
             'programm' => 'DigitalInvest KMU Basis und Plus', 'stelle' => 'nFMI-Portal',
             'status' => 'laeuft',
             'bedingung' => 'KMU mit Sitz oder Betriebsstätte im Saarland. Die Plus-Variante '
                . 'verlangt besonderen Innovationsgehalt.'],

            ['kuerzel' => 'SN', 'land' => 'Sachsen',
             'programm' => 'Digitalisierung in KMU (EFRE)', 'stelle' => 'SAB',
             'status' => 'laeuft',
             'bedingung' => 'Kleinstunternehmen, KMU und Angehörige der freien Berufe mit '
                . 'Betriebsstätte in Sachsen. Zwölf Monate ohne Verlängerung, Start nicht vor '
                . 'der Bestätigung. Websites ohne Geschäftsintegration sind ausgeschlossen.'],

            ['kuerzel' => 'ST', 'land' => 'Sachsen-Anhalt',
             'programm' => 'DIGITAL INNOVATION', 'stelle' => 'IB Sachsen-Anhalt',
             'status' => 'aufruf',
             'bedingung' => 'KMU mit Sitz oder Betriebsstätte im Land. Innovationsgehalt ist '
                . 'zwingend. Seit dem 15.07.2026 Direktantrag mit Mindestpunktzahl.'],

            ['kuerzel' => 'SH', 'land' => 'Schleswig-Holstein',
             'programm' => 'DKU — Digitalisierungsmassnahmen in kleinen Unternehmen',
             'stelle' => 'IB.SH', 'status' => 'laeuft',
             'bedingung' => 'Kleine Unternehmen. Gefördert werden Vorhaben, die in einem '
                . 'schriftlichen Beratungsbericht Lösungen erarbeiten. Richtlinie vom '
                . '05.06.2026, befristet bis 30.06.2027.'],

            ['kuerzel' => 'TH', 'land' => 'Thüringen',
             'programm' => 'Digitalbonus', 'stelle' => 'TAB',
             'status' => 'erschoepft',
             'bedingung' => 'Mittel erschöpft, eine Neuauflage ist nicht geplant.'],
        ];
    }

    /** Die Beschriftung einer Statusstufe. */
    public static function status(string $schluessel): string
    {
        return self::STATUS[$schluessel] ?? $schluessel;
    }
}
