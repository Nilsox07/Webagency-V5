<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Die drei Musterprojekte — `10_WEBSITE_SARTU.md` Sektion 8 und `17_SEITEN_SARTU.md` §4a.
 *
 * ## Warum sie jetzt gebaut werden dürfen
 *
 * Sektion 8 löst den scheinbaren Widerspruch des Lastenhefts („ehrlich beschrifteter
 * Bildplatz" gegen „bis dahin bleibt die Sektion ungebaut") über drei Stufen auf:
 *
 * | Stufe | Was vorliegt | Was gebaut wird |
 * |---|---|---|
 * | 0 | nichts — die Fälle sind nicht ausgeschrieben | **keine Sektion** |
 * | 1 | die drei Fälle sind ausgeschrieben | **Sektion mit ehrlich beschriftetem Bildplatz** |
 * | 2 | Demoprojekte gebaut (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5) | echte Aufnahmen |
 *
 * Die Fälle stehen ausgeschrieben im abgenommenen Entwurf (`design/startseite.html`,
 * `id="muster"`). **Damit ist Stufe 1 erreicht**, und der Betreiber hat sie am 13.08.2026
 * freigegeben (§4d).
 *
 * > „Der Bildplatz ist der Zusatz, nicht der Inhalt. Ein Leser, der liest *Vier Leistungen →
 * > eine eigene Seite je Leistung → das liefern Sie dazu*, weiß danach mehr als von einem
 * > Bildschirmfoto."
 *
 * ## Was hier nicht stehen darf
 *
 * **Keine erfundenen Firmennamen. Keine erfundenen Zahlen, Ergebnisse oder Steigerungen.**
 * Beides ist in Sektion 8 gesperrt und in §4a noch einmal geschärft: „Sobald ein Ergebnis
 * behauptet wird, ist es eine erfundene Fallstudie." Deshalb heißen die drei nach ihrer
 * **Gattung** — Malerbetrieb, Physiotherapiepraxis, Arbeitsrechtskanzlei — und keiner der
 * Sätze nennt einen Erfolg.
 *
 * Die Zahlen, die vorkommen, sind **keine erfundenen**: `rund 1.200 Wörter` ist die
 * Umfangsgrenze der Stufe Start aus `Preise::tabelle()` und wird von dort geholt, nicht
 * abgeschrieben.
 *
 * ## Das Wertetripel gehört dazu
 *
 * §4a bindet je Projekt Inhaltsdichte, Formcharakter und Bewegung aus den geschlossenen
 * Achsen in `03_KUNDENPRODUKT.md`. Es steht hier mit, weil die Begründung sonst beim Bau der
 * Beispielseiten neu erfunden würde — und weil es der Beleg dafür ist, dass die drei
 * **sichtbar verschieden** werden müssen: „Drei Musterprojekte, die sich nur im Text
 * unterscheiden, sind der Beleg dafür, dass es doch ein Baukasten ist."
 */
final class Musterprojekte
{
    /** Über jeder Karte und jedem Fall, `gebunden` — Sektion 8 und §4a. */
    public const FAHNE = 'Musterprojekt — kein Kundenauftrag';

    /** `gebunden` — Sektion 8 nennt den Knopf, §4a die Zielseite. */
    public const KNOPF = 'Alle Musterprojekte ansehen';

    public const PFAD = '/musterprojekte';

    /** `gebunden` — Gründungsjahr und Anzahl, Sektion 8 und §4a Block 2. */
    public const GRUENDUNG = 2026;

    /**
     * Die drei Gattungen in gebundener Reihenfolge.
     *
     * `ausfuehrlich` steht **nur** auf `/musterprojekte`, nicht auf der Karte. §4a verlangt
     * dort „dieselben fünf Bestandteile wie auf der Karte, nur **ausgeschrieben**" und einen
     * Umfang von 700 bis 1.000 Wörtern; die Karte trägt Stichzeilen, die Seite den Fall.
     *
     * @return array<string,array{
     *     gattung:string, paket:string, ausgangslage:string, empfohlen:string,
     *     struktur:string, liefern:string, warum_nicht_kleiner:string, ausfuehrlich:list<string>,
     *     dichte:string, form:string, bewegung:string, gestaltung:string
     * }>
     */
    public static function alle(): array
    {
        return [
            'malerbetrieb' => [
                'gattung'      => 'Malerbetrieb',
                'paket'        => 'wachstum',
                'ausgangslage' => 'Vier Leistungen, die von außen wie eine aussehen.',
                'empfohlen'    => 'Eine eigene Seite je Leistung, jede mit eigenem Text und '
                    . 'eigenem Anfrageweg.',
                'struktur'     => 'Start · vier Leistungsseiten · Über uns · Kontakt.',
                'liefern'      => 'Leistungsliste, Einzugsgebiet, Fotos eigener Arbeiten.',
                'warum_nicht_kleiner' => 'Auf einer Seite stünden vier Leistungen '
                    . 'untereinander. Wer nach einer davon sucht, landet auf einem Absatz '
                    . 'statt auf einer Seite — und die Suchmaschine hat nur ein Thema zu '
                    . 'vergeben, nicht vier.',
                'ausfuehrlich' => [
                    'Ein Malerbetrieb mit zwölf Leuten macht Innenanstrich, Fassade, '
                        . 'Bodenbeläge und Trockenbau. Auf der alten Seite steht das als '
                        . 'Aufzählung unter „Leistungen" — vier Zeilen, ein Absatz.',
                    'Wer nach Fassadensanierung sucht, sucht nach Fassadensanierung und nicht '
                        . 'nach einem Malerbetrieb. Er landet auf einer Seite, die von vier '
                        . 'Dingen handelt, und muss selbst herausfinden, ob das dritte davon '
                        . 'gemeint ist. Eine Suchmaschine hat dasselbe Problem: Sie kann einer '
                        . 'Seite ein Thema zuordnen, nicht vier.',
                    'Vier eigene Seiten lösen beides. Jede trägt ihr Thema, ihre Bilder und '
                        . 'ihren eigenen Anfrageweg — und der Betrieb kann eine davon '
                        . 'einzeln bewerben, ohne die anderen mitzuschleppen.',
                ],
                'dichte'    => 'compact',
                'form'      => 'bold',
                'bewegung'  => 'subtle',
                'gestaltung' => 'Die Arbeiten sind das Argument: große Flächen, wenig Text. '
                    . 'Gelesen wird oft mobil auf der Baustelle. Die eine sinnvolle Bewegung '
                    . 'ist der Vorher-nachher-Wechsel.',
            ],
            'physiotherapiepraxis' => [
                'gattung'      => 'Physiotherapiepraxis',
                'paket'        => 'start',
                'ausgangslage' => 'Eine Adresse, feste Zeiten, Terminanfragen laufen übers '
                    . 'Telefon.',
                'empfohlen'    => 'Eine Seite, die Leistung, Zeiten und den Weg zur '
                    . 'Terminanfrage zusammen trägt.',
                'struktur'     => 'Eine Seite mit vier Abschnitten: Leistung, Zeiten, Weg, '
                    . 'Kontakt.',
                'liefern'      => 'Leistungsspektrum, Öffnungszeiten, Kassenzulassung.',
                'warum_nicht_kleiner' => 'Kleiner geht nicht — Start ist die erste Stufe. '
                    . 'Größer wäre falsch: Eine zweite Seite müsste ein zweites Thema haben, '
                    . 'und das gibt es hier nicht.',
                'ausfuehrlich' => [
                    'Eine Praxis mit drei Behandlungsräumen, festen Sprechzeiten und einem '
                        . 'Einzugsgebiet von wenigen Kilometern. Termine werden angerufen. '
                        . 'Wer die Praxis sucht, sucht sie meist schon namentlich oder auf '
                        . 'Empfehlung.',
                    'Diese Seite muss drei Fragen beantworten und keine vierte: Was wird '
                        . 'behandelt, wann ist offen, wie komme ich zu einem Termin. Alles '
                        . 'andere — Team, Geschichte, Philosophie — steht auf solchen Seiten '
                        . 'meist deshalb, weil eine Seite sonst zu leer aussieht.',
                    'Deshalb bleibt es bei einer Seite mit vier Abschnitten. Eine zweite '
                        . 'Seite müsste ein eigenes Thema tragen; hier gäbe es keins, und '
                        . 'aufgeteilte Öffnungszeiten sind schlechter auffindbar als '
                        . 'zusammenstehende.',
                ],
                'dichte'    => 'balanced',
                'form'      => 'human',
                'bewegung'  => 'none',
                'gestaltung' => 'Eine Seite trägt Leistung, Zeiten und Terminweg zugleich — '
                    . 'sie braucht Gleichgewicht, keine Verdichtung. Weiche Formen, weil '
                    . 'Härte im Gesundheitskontext falsch wirkt. Ältere Zielgruppe, also Ruhe.',
            ],
            'arbeitsrechtskanzlei' => [
                'gattung'      => 'Arbeitsrechtskanzlei',
                'paket'        => 'platzhirsch',
                'ausgangslage' => 'Zwei Zielgruppen mit gegensätzlichem Anliegen: Arbeitgeber '
                    . 'und Arbeitnehmer.',
                'empfohlen'    => 'Getrennte Wege je Zielgruppe, Vertrauen über Personen und '
                    . 'Ablauf, eigene Stellenseite.',
                'struktur'     => 'Start · je Rechtsgebiet eine Seite · Team · Ablauf · '
                    . 'Karriere · Kontakt.',
                'liefern'      => 'Tätigkeitsschwerpunkte, Werdegang je Person, '
                    . 'Pflichtangaben der Kammer.',
                'warum_nicht_kleiner' => 'Wachstum trägt eine Seite je Leistung, aber keine '
                    . 'zwei Einstiege. Hier muss der Arbeitgeber einen anderen Weg finden als '
                    . 'der Arbeitnehmer — und die Kanzlei sucht daneben selbst Personal.',
                'ausfuehrlich' => [
                    'Eine Kanzlei mit vier Anwälten, Schwerpunkt Arbeitsrecht. Sie berät '
                        . 'Arbeitgeber bei Kündigungen und vertritt Arbeitnehmer, die '
                        . 'gekündigt wurden. Beide sollen sie finden, und keiner der beiden '
                        . 'soll den Eindruck bekommen, hier sei er auf der falschen Seite.',
                    'Das ist der Grund, aus dem eine Seite je Leistung hier nicht reicht. Es '
                        . 'sind nicht vier Leistungen nebeneinander, sondern zwei Wege '
                        . 'auseinander — jeder mit eigener Sprache, eigenen Beispielen und '
                        . 'eigenem ersten Schritt.',
                    'Dazu kommt ein dritter Leser, den die anderen beiden Fälle nicht haben: '
                        . 'der Bewerber. Kanzleien dieser Größe suchen dauerhaft '
                        . 'Referendare und Fachangestellte, und eine Karriereseite ohne '
                        . 'eigene Struktur wird zur Adresse einer Sammelmailbox.',
                ],
                'dichte'    => 'editorial',
                'form'      => 'precise',
                'bewegung'  => 'none',
                'gestaltung' => 'Verkauft über Text und Genauigkeit. Zwei Zielgruppen mit '
                    . 'gegensätzlichem Anliegen brauchen Gliederung, nicht Bewegung.',
            ],
        ];
    }

    /**
     * Die vier Zeilen der Karte, in gebundener Reihenfolge.
     *
     * Beschriftung und Feldname stehen zusammen, damit die Reihenfolge nicht an zwei Stellen
     * gepflegt wird — auf der Karte und auf `/musterprojekte` ist sie dieselbe.
     *
     * @return array<string,string> Feld => Beschriftung
     */
    public static function zeilen(): array
    {
        return [
            'ausgangslage' => 'Ausgangslage',
            'empfohlen'    => 'Empfohlen',
            'struktur'     => 'Seitenstruktur',
            'liefern'      => 'Sie liefern',
        ];
    }

    /**
     * Der empfohlene Umfang als Zeile — **an der Karte, nicht im Bildplatz**.
     *
     * Bis zum 13.08.2026 stand hier `Malerbetrieb, Umfang Wachstum` und war die
     * Bildunterschrift des Platzhalters. Direkt darunter folgte die Überschrift
     * `Malerbetrieb`: **dieselbe Gattung zweimal untereinander**, dreimal auf der Startseite
     * und dreimal auf `/musterprojekte`.
     *
     * Weggefallen ist die Nennung im Bildplatz, nicht die Angabe: Der Umfang ist die Aussage,
     * um die es geht — er steht jetzt neben dem Titel, wo er zur Karte gehört.
     *
     * Der Paketname kommt aus `Preise`, nicht aus einer zweiten Liste. Ein umbenanntes Paket
     * hiesse sonst auf dieser Seite weiter, wie es einmal hiess.
     */
    public static function umfangszeile(string $schluessel): string
    {
        $projekt = self::alle()[$schluessel] ?? null;

        return $projekt === null ? '' : 'Umfang ' . Preise::name($projekt['paket']);
    }

    /**
     * Was auf dem Bildplatz stehen wird — ohne die Gattung, die darunter als Überschrift
     * steht.
     *
     * Der abgenommene Entwurf schreibt an dieser Stelle `Platz für Ansicht` und einen Satz,
     * was dort hinkommt. Der Satz sagt, **was** zu sehen sein wird, nicht **wessen** Seite es
     * ist — das steht schon in der Überschrift.
     */
    public static function bildsatz(string $schluessel): string
    {
        $projekt = self::alle()[$schluessel] ?? null;

        if ($projekt === null) {
            return '';
        }

        return 'Die Startseite, sobald das Musterprojekt gebaut ist.';
    }

    /** Der Dateiname, den die spätere Aufnahme tragen wird — im Bildplatz sichtbar. */
    public static function bildname(string $schluessel): string
    {
        return 'sartu-muster-' . $schluessel;
    }

    /**
     * Die Anzahl als **Wort**, nicht als Ziffer — `08_TEXTREGELN.md`.
     *
     * Sie steht auf der Startseite und auf `/musterprojekte` und ist beide Male `gebunden`.
     * Gezählt statt abgeschrieben: Eine feste Drei wäre falsch, sobald ein vierter Fall
     * dazukommt, und niemand würde sie hier suchen.
     *
     * Über zwölf bleibt die Ziffer stehen. Ein Zahlwort für 13 zu bilden hiesse, eine Regel
     * zu erfinden, die in den Textregeln nicht steht.
     */
    public static function anzahlWort(): string
    {
        $woerter = [
            1 => 'ein', 2 => 'zwei', 3 => 'drei', 4 => 'vier', 5 => 'fünf', 6 => 'sechs',
            7 => 'sieben', 8 => 'acht', 9 => 'neun', 10 => 'zehn', 11 => 'elf', 12 => 'zwölf',
        ];

        $anzahl = count(self::alle());

        return $woerter[$anzahl] ?? (string) $anzahl;
    }
}
