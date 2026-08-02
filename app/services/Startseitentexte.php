<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Der Wortlaut der Startseite — Website-Lastenheft §5, Sektion 1 bis 10.
 *
 * ## Warum die Texte hier stehen und nicht in der Ansicht
 *
 * §1.3 des Portal-Lastenhefts: „Eine Seite = Layout + Partials + Komponenten." Ein Text in
 * der Ansicht lässt sich nicht zählen, nicht prüfen und nicht wiederverwenden. Der
 * Prüfbericht aus `SARTU_TEXTREGELN.md` §2 braucht die laufende Prosa an einer Stelle.
 *
 * ## Zwei Sektionen fehlen, und beide fehlen mit Grund
 *
 * | Sektion | Warum sie nicht gebaut ist |
 * |---|---|
 * | **6 — Wer dahintersteckt** | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5: Foto und Name des Gründers stehen auf `offen`. „Fehlt es, entfällt Sektion 8 der Startseite vollständig — kein leerer Rahmen an einer Vertrauensstelle." Die Startsperre §14a Bedingung 4a hält sie zusätzlich zurück |
 * | **8 — Musterprojekte** | Dieselbe Datei, §5: „Ein bis zwei gekennzeichnete Demoprojekte — offen, zu entscheiden." §5 Sektion 8 des Lastenhefts: „Bis dahin bleibt die Sektion ungebaut — eine Musterprojekt-Sektion ohne Musterprojekte ist schlechter als keine" |
 *
 * Beide sind gemeldet, nicht ersetzt. Es steht kein Platzhalter dort und keine abgeschwächte
 * Fassung — §0.3b verbietet „kommt bald"-Bereiche.
 *
 * ## Ein Ortsname fehlt, und das ist kein Versehen
 *
 * §5 Sektion 9, Frage 1 nennt im Lastenheft „Unser Sitz ist im Raum Dresden". §0 desselben
 * Dokuments verbietet **Ortsnamen im Fließtext**, solange `[GESCHAEFTSADRESSE_STATUS]` in
 * `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1 auf `offen` steht — und das tut es. Nach der Rangfolge
 * in `UEBERGABE_DATEILISTE.md` steht diese Datei auf Rang 1, das Website-Lastenheft auf
 * Rang 5. Die Sperre gewinnt.
 *
 * **Die Aussage bleibt trotzdem vollständig:** Die Antwort trägt die Reichweite („bundesweit")
 * und die Begründung („keine Abstimmungstermine"). Nur der Ortsname fehlt. Der Wortlaut ist
 * Klasse 2 — Aussage gebunden, Formulierung frei.
 */
final class Startseitentexte
{
    public const TITEL = 'Firmenwebsite zum Festpreis für regionale Betriebe | SARTU';

    public const BESCHREIBUNG = 'Firmenwebsite zum Festpreis, bundesweit und ohne einen '
        . 'einzigen Termin. Geplant, geschrieben, programmiert und betrieben von SARTU. '
        . 'Ab 1.490 € netto.';

    public const H1 = 'Individuell programmierte Firmenwebsites zum Festpreis.';

    // -------------------------------------------------------------- 1 Aufmacher

    public const EYEBROW = 'Webdesign-Agentur für Firmenwebsites';

    public const LEAD = 'Sie erzählen uns, was Ihr Betrieb macht und für wen. Den Rest bauen '
        . 'und betreiben wir. Dafür ist kein einziger Termin nötig.';

    /** §5 Sektion 1 — vier Punkte, gebundener Wortlaut. */
    public const VERTRAUENSPUNKTE = [
        'Festpreis vorab',
        'Texte inklusive',
        'Bundesweit, ohne Termin',
        'SEO-Basis ab Launch',
    ];

    /**
     * §5 Sektion 1 — die vier Branchen.
     *
     * Sie sind **nicht anklickbar** und dürfen es auch nicht aussehen. §5 nennt zwei
     * zulässige Auflösungen und legt fest: „Solange keine Branchenseiten gebaut sind, gilt 1"
     * — reine Typografie mit Trennzeichen. Ab Welle 1 gibt es Branchenseiten, aber nicht für
     * diese vier: Gebaut sind Sanitär-Heizung-Klima, Elektrotechnik und Dachdecker.
     */
    public const BRANCHEN = ['Handwerk', 'Praxen', 'Kanzleien', 'Ladengeschäfte'];

    // -------------------------------------------------------------- 2 Kundenbereich

    public const S2_H2 = Websitetexte::OHNE_TERMIN;

    public const S2_ANTWORT = 'Bei SARTU gibt es keine Abstimmungstermine. Alles läuft über '
        . 'Ihren Kundenbereich. Sie beantworten die Fragen zu Ihrem Betrieb, wann es Ihnen '
        . 'passt. Was dort geht, steht unten — vollständig.';

    /**
     * §5 Sektion 2 — elf Punkte, nicht vierzehn.
     *
     * Die drei gestrichenen Zeilen — Bilder tauschen, Team- und Projekteinträge pflegen,
     * Anfragen von Ihrer Website einsehen — sind am 01.08.2026 dauerhaft entfallen
     * (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5a). Für zwei fehlt die Tabelle im Datenmodell, die
     * dritte steht unter „Nicht bauen". Elf ist der Endstand, keine Sicherung.
     *
     * **Die Liste wird nicht gekürzt und nicht zu „unter anderem" zusammengefasst.** Der
     * Leser hat noch nie einen Kundenbereich bei einer Agentur gehabt — es gibt dort keinen.
     * Er hat kein Vorstellungsbild, das eine Andeutung füllen könnte.
     *
     * @return array{'Vor dem Start':list<string>,'Nach dem Start':list<string>}
     */
    public static function kundenbereich(): array
    {
        return [
            'Vor dem Start' => [
                'Angebot ansehen und annehmen',
                'Fragen zu Ihrem Betrieb beantworten, wann es Ihnen passt',
                'Logo, Bilder und Unterlagen hochladen',
                'Sehen, was gerade ansteht und was erledigt ist',
                'Die fertige Vorschau ansehen',
                'Änderungen sammeln und in einem Durchgang schicken',
                'Freigeben',
            ],
            'Nach dem Start' => [
                'Öffnungszeiten und Kontaktdaten ändern',
                'Rechnungen und Laufzeit einsehen',
                'Änderungswünsche stellen',
                'Domainstatus einsehen',
            ],
        ];
    }

    public const S2_UNTERSCHIED = 'Kein Terminkalender-Pingpong. Kein Suchen in alten E-Mails. '
        . 'Kein Anruf, um den Stand zu erfahren.';

    // -------------------------------------------------------------- 3 Ablauf

    public const S3_H2 = 'Sie liefern die Fakten. Alles andere machen wir.';

    /**
     * §5 Sektion 3 — sechs Schritte, je ein Satz.
     *
     * @return list<array{titel:string,satz:string,bild:bool}>
     */
    public static function ablauf(): array
    {
        return [
            ['titel' => 'Bedarfsscheck', 'bild' => true,
             'satz'  => 'Wenige Fragen zu Unternehmen, Ziel, Umfang und Domain.'],
            ['titel' => 'Geprüftes Angebot', 'bild' => true,
             'satz'  => 'Sie bekommen Umfang, Preis und Zahlungsplan schriftlich.'],
            ['titel' => 'Ihre Angaben', 'bild' => false,
             'satz'  => 'Was wir schon wissen, tragen wir ein. Den Rest fragen wir Sie im Kundenbereich.'],
            ['titel' => 'Produktion', 'bild' => false,
             'satz'  => 'Wir bauen die Website. KI hilft, geprüft und freigegeben wird von uns.'],
            ['titel' => 'Vorschau und Freigabe', 'bild' => true,
             'satz'  => 'Sie sehen die fertige Website und sammeln Ihre Änderungen.'],
            ['titel' => 'Start und Betrieb', 'bild' => false,
             'satz'  => 'Wir schalten live und halten die Seite am Laufen.'],
        ];
    }

    public const S3_IHR_ANTEIL = 'Ihr Anteil: was Ihr Betrieb macht, für wen und in welchem '
        . 'Gebiet. Dazu Bilder und Freigaben.';

    public const S3_UNSER_ANTEIL = 'Unser Anteil: alles andere. Auch die Verantwortung, wenn '
        . 'etwas nicht funktioniert.';

    // -------------------------------------------------------------- 4 Preise

    public const S4_H2 = 'Sie wählen kein Paket. Wir sagen Ihnen, welcher Umfang passt.';

    public const S4_SUBLINE = 'Eine Empfehlung. Vier mögliche Ergebnisse.';

    public const S4_EINLEITUNG = 'Sie müssen nicht wissen, wie viele Seiten Sie brauchen. Der '
        . 'Bedarfsscheck zeigt, welcher Umfang voraussichtlich passt. Wir prüfen das '
        . 'anschließend selbst nach.';

    /**
     * §5 Sektion 4, Vorgabe 3 aus dem UX-Audit: die Monatspauschale wird aufgeschlüsselt.
     *
     * „Deckt Betrieb, Pflege und Support" beantwortet die teuerste offene Frage der Seite
     * nicht: Wofür zahle ich jeden Monat?
     */
    public const MONATSPAUSCHALE = [
        'Hosting',
        'technische Pflege',
        'Sicherheitsaktualisierungen',
        'Sicherungen',
        'Überwachung',
        'Support',
        'Kundenbereich',
    ];

    /** §5 Sektion 4, Vorgabe 5: die SEO-Grundlage wird von späterer SEO-Arbeit abgegrenzt. */
    public const SEO_GRUNDLAGE = 'SEO-Grundlage ab Livegang: Struktur, Titel, Metadaten, '
        . 'interne Verlinkung, indexierbare Inhalte. Die laufende Weiterentwicklung ist ein '
        . 'eigenes Thema.';

    // -------------------------------------------------------------- 5 Die Zusage

    public const S5_ZUSAGE = Websitetexte::EIN_PREIS;

    // -------------------------------------------------------------- 7 Leistungen

    public const S7_H2 = 'Es gibt keine Aufpreisliste.';

    public const S7_EINLEITUNG = 'Das alles steckt in jedem Angebot — Sie stellen es nicht '
        . 'selbst zusammen und zahlen nichts davon extra. Wir gewichten die Bausteine passend '
        . 'zu Ihrem Ziel.';

    public const S7_SEO_H2 = 'Ihre Website ist ab dem ersten Tag für Suchmaschinen vorbereitet.';

    public const S7_SEO_TEXT = 'Jede SARTU-Website startet mit klaren Seitenthemen, sprechenden '
        . 'Adressen, sauberer interner Verlinkung, Metadaten, strukturierten Daten und einer '
        . 'soliden Performance-Grundlage. Späterer Ausbau baut auf echten Suchdaten auf — '
        . 'nicht auf pauschalen SEO-Paketen.';

    /** @return list<array{titel:string,satz:string}> */
    public static function seoSpalten(): array
    {
        return [
            ['titel' => 'Menschen verstehen',
             'satz'  => 'Klare Antworten, Preise, Ablauf und Grenzen stehen sichtbar auf der Seite.'],
            ['titel' => 'Suchmaschinen erfassen',
             'satz'  => 'Sauberes HTML, Sitemap, Canonicals, strukturierte Daten, Ladezeit.'],
            ['titel' => 'KI-Antworten einordnen',
             'satz'  => 'Konsistente Unternehmensfakten, FAQ und Definitionen statt Textwüsten.'],
        ];
    }

    // -------------------------------------------------------------- 9 Häufige Fragen

    /**
     * §5 Sektion 9 — die Einwände, in dieser Reihenfolge.
     *
     * Frage 1 steht bewusst zuerst: Sie entscheidet, ob der Leser sich überhaupt
     * angesprochen fühlt. Der Ortsname aus dem Lastenheft fehlt — Begründung im
     * Klassenkommentar oben.
     *
     * @return list<array{frage:string,antwort:string}>
     */
    public static function fragen(): array
    {
        return [
            ['frage'   => 'Arbeiten Sie bundesweit?',
             'antwort' => 'Ja. Weil es keine Abstimmungstermine gibt, spielt die Entfernung '
                . 'keine Rolle. Der Ablauf ist überall derselbe.'],
            ['frage'   => 'Muss ich mir selbst ein Paket aussuchen?',
             'antwort' => 'Nein. Sie beschreiben Ihr Unternehmen und Ihr Ziel; wir empfehlen '
                . 'genau einen Umfang und begründen ihn. Wenn ein kleinerer reicht, empfehlen '
                . 'wir den kleineren.'],
            ['frage'   => 'Schreiben Sie die Texte?',
             'antwort' => 'Ja. Sie liefern Fakten, Stichpunkte und vorhandene Unterlagen — wir '
                . 'schreiben daraus die Website-Texte. Erfundene Belege oder ungeprüfte '
                . 'Fachaussagen gibt es nicht.'],
            ['frage'   => 'Warum gibt es keine Liste mit Zusatzoptionen?',
             'antwort' => 'Weil Zusatzlisten den Preis unklar machen. Ein Standardangebot endet '
                . 'exakt beim genannten Festpreis. Passt eine Anforderung nicht hinein, '
                . 'bekommen Sie dafür ein eigenes Angebot mit eigenem Festpreis.'],
            ['frage'   => 'Was passiert mit meiner Domain und meinen E-Mail-Adressen?',
             'antwort' => 'Die Domain gehört Ihnen — auch wenn wir sie technisch verwalten. Vor '
                . 'jeder Änderung sichern wir Ihre bestehenden Einträge, damit Ihre '
                . 'E-Mail-Adressen beim Umschalten erreichbar bleiben.'],
            ['frage'   => 'Kann ich später selbst etwas ändern?',
             'antwort' => 'Öffnungszeiten und Kontaktdaten pflegen Sie selbst im Kundenbereich. '
                . 'Texte, Bilder und Seitenstruktur ändern wir für Sie — schreiben Sie uns '
                . 'einfach, das ist im Betrieb enthalten.'],
            ['frage'   => 'Ist SEO enthalten?',
             'antwort' => 'Die Grundlage ja, ab dem ersten Tag: Seitenthemen, Metadaten, '
                . 'strukturierte Daten, interne Verlinkung, Ladezeit. Ein späterer Ausbau folgt '
                . 'echten Suchdaten und ist ein eigenes Angebot.'],
            ['frage'   => 'Warum kein WordPress?',
             'antwort' => 'Weil Sie sich dann um Updates, Plugins und Sicherheitslücken kümmern '
                . 'müssten. Wir programmieren die Website ohne diese Abhängigkeiten und '
                . 'betreiben sie selbst.'],
            ['frage'   => 'Können Sie eine bestimmte Google-Position zusichern?',
             'antwort' => 'Nein, und niemand kann das seriös. Wir bauen das technische und '
                . 'inhaltliche Fundament und halten es im Betrieb sauber.'],
        ];
    }

    // -------------------------------------------------------------- 10 Bedarfsscheck

    public const S10_H2 = 'Welche Website passt zu Ihrem Unternehmen?';

    public const S10_TEXT = 'Der Bedarfsscheck fragt nicht nach Seitenzahlen, Farben oder '
        . 'SEO-Stufen. Sie beantworten wenige Fragen zu Ihrem Geschäft und sehen sofort eine '
        . 'vorläufige Empfehlung mit Preis. Danach prüfen wir persönlich.';

    /** Nur Anzeige, nicht anklickbar — §5 Sektion 10. */
    public const S10_CHIPS = ['Branche', 'Region', 'Ziel', 'Umfang', 'Domain', 'Besonderheiten'];

    public const S10_VERTRAUEN = [
        'Dauert etwa 3 Minuten',
        'Preis vor Kontaktdaten',
        'Kein Pflichttermin',
        'Unverbindlich',
    ];
}
