<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Branchenseiten, Welle 1 — Website-Lastenheft §10a.
 *
 * ## Die Bedingung in einem Satz
 *
 * §10a: „Jede Branchenseite ist eine vollständige Zielseite mit Konfigurator — **und**
 * enthält mindestens **400 Wörter, die auf keiner anderen Seite der Website stehen**."
 *
 * ## Drei Prüfungen, alle hart
 *
 * | # | Prüfung | Wie sie hier gehalten wird |
 * |---|---|---|
 * | 1 | **Austauschtest** — Branchenwort ersetzen, ergibt der Text noch Sinn? | Die Blöcke 3, 5, 6 und 10 nennen Heizungstausch, Zählerschrank, Dachaufbau. Bei einer Steuerkanzlei ergibt keiner davon Sinn |
 * | 2 | **Eigenanteil** — mindestens 400 eigene Wörter | Blöcke 3 bis 6 und 10 sind je Branche eigen. `WebsiteTest` misst es |
 * | 3 | **Herkunftsnachweis** — woher stammt die Aussage über die Branche? | Feld `quellen` je Block. Zulässig sind nur die drei Quellen aus §10a |
 *
 * **Reißt eine der drei: Die Seite wird nicht veröffentlicht.** Nicht überarbeitet — nicht
 * veröffentlicht.
 *
 * ## Die Zahlen kommen aus `SARTU_BRANCHENFAKTEN.md`
 *
 * Höchstens **drei** je Seite, **nie** im Aufmacher, Quelle und Jahr **an der Zahl**. Und
 * jede besteht den Nicken-Test: Liest der Betrieb sie und denkt „ja, genau"? Die Zahlen, die
 * dort unter „Nicht verwenden" stehen, stehen hier nicht — auch die nicht, die gut klingen.
 *
 * ## Der Konfigurator ist eingebettet, nicht verlinkt
 *
 * §10a: „Wer erst zu `/briefing` klicken muss, klickt oft gar nicht." Das Feld `branche` wird
 * aus der Seite vorbelegt und landet in `leads.branche_vorbelegt` — **derselbe Endpunkt und
 * dieselben Schutzmaßnahmen** wie in Portal-Lastenheft §4b. Kein zweiter Weg, kein zweites
 * Formular.
 */
final class Branchenseiten
{
    /**
     * @return array<string,array{
     *     branche:string, h1:string, titel:string, beschreibung:string, kurz:string,
     *     probleme:list<array{titel:string,text:string}>,
     *     gehoert_drauf:list<string>,
     *     beachten:list<array{titel:string,text:string}>,
     *     beispiel:array{titel:string,text:string,seiten:list<string>},
     *     fragen:list<array{frage:string,antwort:string}>,
     *     zahlen:list<array{satz:string,quelle:string}>,
     *     quellen:list<string>
     * }>
     */
    public static function alle(): array
    {
        return [
            'sanitaer-heizung-klima' => [
                'branche' => 'Sanitär-, Heizungs- und Klimabetriebe',
                'h1'      => 'Website für Sanitär-, Heizungs- und Klimabetriebe',
                'titel'   => 'Website für SHK-Betriebe zum Festpreis | SARTU',
                'beschreibung' => 'Website für Sanitär, Heizung und Klima ab 1.490 € netto: je '
                    . 'eine Seite für Badsanierung, Heizungstausch und Wartung, Texte '
                    . 'inklusive, Betrieb ab 59 € im Monat.',
                'kurz' => 'Wir bauen Ihre SHK-Website zum Festpreis ab 1.490 € netto — mit einer '
                    . 'eigenen Seite je Leistung, geschriebenen Texten und Betrieb ab 59 € netto '
                    . 'im Monat. Sie liefern die Fakten in einem Gespräch, danach ist kein '
                    . 'Termin nötig.',

                'probleme' => [
                    ['titel' => 'Badsanierung und Heizungstausch stehen auf derselben Seite',
                     'text'  => 'Wer eine Badsanierung plant, sucht anders als jemand, dessen '
                        . 'Heizung ausgefallen ist. Steht beides auf einer Seite, beantwortet '
                        . 'sie keine der beiden Suchanfragen vollständig — und wird für keine '
                        . 'von beiden gefunden.'],
                    ['titel' => 'Die Förderfrage kommt in jedem Erstgespräch',
                     'text'  => 'Wer eine Heizung tauschen lässt, will vorher wissen, was die '
                        . 'Förderung ausmacht und wer den Antrag stellt. Steht das nirgends auf '
                        . 'der Website, wird es am Telefon erklärt — jedes Mal neu.'],
                    ['titel' => 'Der Bedarf ist da, der Absatz nicht',
                     'text'  => 'Über 40 % der Heizungen im Bestand entsprechen nicht dem Stand '
                        . 'der Technik, viele sind über 30 Jahre alt (BDH-Jahresbilanz, Februar '
                        . '2026). Verkauft wurden 2025 trotzdem nur 627.000 Anlagen. Das ist '
                        . 'der niedrigste Stand seit 15 Jahren, ein Minus von 12 % '
                        . '(BDH-Jahresbilanz, Februar 2026).'],
                    ['titel' => 'Die Website ist da und bringt nichts',
                     'text'  => '94 % der Handwerksbetriebe haben eine Website (Bitkom 2025, '
                        . 'n=504). Der Unterschied entsteht nicht daran, dass es eine gibt, '
                        . 'sondern daran, ob sie die Frage beantwortet, mit der jemand sucht.'],
                ],

                'gehoert_drauf' => [
                    'Je eine eigene Seite für Badsanierung, Heizungstausch, Wartung und Notdienst',
                    'Was die Förderung abdeckt und wer den Antrag stellt',
                    'Welche Heiztechnik Sie einbauen — Wärmepumpe, Gas, Hybrid',
                    'Ihr Einzugsgebiet, benannt statt umschrieben',
                    'Bilder fertiger Bäder und eingebauter Anlagen',
                    'Wie lange es von der Anfrage bis zum Termin dauert',
                ],

                'beachten' => [
                    ['titel' => 'Preise für Badsanierungen',
                     'text'  => 'Ein Festpreis für ein Bad lässt sich ohne Besichtigung nicht '
                        . 'nennen. Eine Preisspanne auf der Website erzeugt Anfragen, die Sie '
                        . 'wieder absagen müssen. Wir schreiben stattdessen, wovon der Preis '
                        . 'abhängt.'],
                    ['titel' => 'Aussagen zur Förderung',
                     'text'  => 'Förderhöhen und Bedingungen ändern sich. Wir schreiben sie so, '
                        . 'dass eine Änderung eine Zeile kostet und nicht eine Seite. Ein '
                        . 'Ergebnis, das eine Zusage wäre, nennen wir nicht.'],
                    ['titel' => 'Notdienst',
                     'text'  => 'Wenn Sie Notdienst anbieten, gehört er auf die Seite — aber '
                        . 'nicht nach oben. Er ist das Geschäft, das Sie mitnehmen, nicht das, '
                        . 'von dem Sie leben.'],
                ],

                'beispiel' => [
                    'titel' => 'Ein Betrieb mit zwölf Beschäftigten, Umfang Wachstum',
                    'text'  => 'Bis zu 8 Seiten, rund 3.500 Wörter, 2 Korrekturrunden für '
                        . '3.900 € netto einmalig und 129 € netto im Monat. Der Betrieb liefert '
                        . 'Fakten in einem Gespräch und Bilder aus dem Telefon.',
                    'seiten' => [
                        'Startseite',
                        'Badsanierung',
                        'Heizungstausch und Förderung',
                        'Wartung und Notdienst',
                        'Über den Betrieb',
                        'Kontakt',
                        'Impressum',
                        'Datenschutz',
                    ],
                ],

                'fragen' => [
                    ['frage'   => 'Brauche ich für jede Leistung eine eigene Seite?',
                     'antwort' => 'Wenn Sie für jede gefunden werden wollen, ja. Eine Seite '
                        . 'beantwortet eine Suchanfrage — Badsanierung und Heizungstausch sind '
                        . 'zwei.'],
                    ['frage'   => 'Können Sie die Förderung erklären?',
                     'antwort' => 'Wir schreiben, was Sie uns dazu sagen, und trennen dabei '
                        . 'Ihre Leistung von den Bedingungen des Programms. Eine '
                        . 'Förderberatung ist das nicht.'],
                    ['frage'   => 'Was, wenn ich keine Bilder von fertigen Bädern habe?',
                     'antwort' => 'Dann bauen wir die Seite ohne. Bestandsfotos setzen wir nicht '
                        . 'ein — ein gekauftes Bad auf Ihrer Seite fällt jedem auf, der Bäder '
                        . 'baut.'],
                ],

                'zahlen' => [
                    ['satz'   => 'Über 40 % der Heizungen im Bestand entsprechen nicht dem Stand der Technik.',
                     'quelle' => 'BDH-Jahresbilanz, Februar 2026'],
                    ['satz'   => 'Heizungsabsatz 2025: 627.000 Anlagen, minus 12 % — niedrigster Stand seit 15 Jahren.',
                     'quelle' => 'BDH-Jahresbilanz, Februar 2026'],
                    ['satz'   => '94 % der Handwerksbetriebe haben eine Website.',
                     'quelle' => 'Bitkom 2025, n=504'],
                ],

                'quellen' => [
                    'SARTU_BRANCHENFAKTEN.md Abschnitt 2 (S1, S2) — BDH-Jahresbilanz vom 01.02.2026',
                    'SARTU_KUNDENMOTIVE_BELEGT.md, Motiv 5 — Bitkom 2025, n=504',
                ],
            ],

            'elektrotechnik' => [
                'branche' => 'Elektrobetriebe',
                'h1'      => 'Website für Elektrobetriebe',
                'titel'   => 'Website für Elektrobetriebe zum Festpreis | SARTU',
                'beschreibung' => 'Website für Elektrotechnik ab 1.490 € netto: eigene Seiten '
                    . 'für Photovoltaik, Ladepunkte, Gewerbe und Neubau. Texte inklusive, '
                    . 'Betrieb ab 59 € im Monat.',
                'kurz' => 'Wir bauen Ihre Elektro-Website zum Festpreis ab 1.490 € netto — mit '
                    . 'einer eigenen Seite je Arbeitsgebiet, geschriebenen Texten und Betrieb ab '
                    . '59 € netto im Monat. Sie liefern die Fakten in einem Gespräch.',

                'probleme' => [
                    ['titel' => 'Drei Arbeitsgebiete, eine Seite',
                     'text'  => 'Photovoltaik, Ladepunkte und Gewerbeinstallation sind drei '
                        . 'verschiedene Aufträge mit drei verschiedenen Auftraggebern. Stehen '
                        . 'sie in einer Aufzählung, entscheidet der Besucher über keinen '
                        . 'davon. Er sucht weiter.'],
                    ['titel' => 'Die Aufträge sind zurückgegangen',
                     'text'  => 'Vom E-Handwerk installierte Photovoltaik-Anlagen: 395.000 auf '
                        . '355.000. Batteriespeicher: 260.000 auf 235.000. Ladepunkte: 377.000 '
                        . 'auf 360.000 (ZVEH-Frühjahrsumfrage 2026, 1.641 Betriebe). Drei '
                        . 'rückläufige Zahlen in Folge.'],
                    ['titel' => 'Der Kunde vergleicht schärfer',
                     'text'  => '87 % der Betriebe melden preissensiblere Kunden, 74 % '
                        . 'kritischere (Bitkom 2025, n=504). Wer nur einen Preis sieht, '
                        . 'vergleicht Preise. Wer sieht, was dafür passiert, vergleicht '
                        . 'Leistungen.'],
                    ['titel' => 'Für die Digitalisierung ist keine Zeit',
                     'text'  => '72 % der Betriebe geben an, zu viel zu tun zu haben (Bitkom '
                        . '2025, n=504). Eine Website, die drei Wochen Ihrer Zeit kostet, '
                        . 'entsteht deshalb nicht. Bei uns liefern Sie die Fakten in einem '
                        . 'Gespräch und lesen später eine Vorschau — mehr ist nicht Ihr Anteil.'],
                    ['titel' => 'Die Anmeldung beim Netzbetreiber steht nirgends',
                     'text'  => 'Wer eine Anlage anmeldet, will drei Dinge wissen: wer den Antrag '
                        . 'stellt, welche Unterlagen nötig sind, wie lange es dauert. Das '
                        . 'erklären Sie sonst am Telefon. Jedes Mal dieselben zehn Minuten.'],
                    ['titel' => 'Der Zählerschrank entscheidet, und niemand weiß das',
                     'text'  => 'Ob eine Wallbox oder ein Speicher überhaupt gehen, hängt am '
                        . 'Zählerschrank und am Hausanschluss. Steht das auf der Seite, kommen '
                        . 'Anfragen mit einem Foto davon. Steht es nicht da, kommen Anfragen, '
                        . 'aus denen nichts wird — und dazwischen liegt ein Ortstermin.'],
                ],

                'gehoert_drauf' => [
                    'Je eine Seite für Photovoltaik, Speicher, Ladepunkte und Gewerbeinstallation',
                    'Ob Sie Neubau, Sanierung oder beides machen',
                    'Welche Zertifizierungen und Netzbetreiber-Anmeldungen Sie übernehmen',
                    'Was ein Ortstermin kostet und was er klärt',
                    'Ihr Einzugsgebiet',
                    'Wie eine Anfrage bei Ihnen abläuft — von der Meldung bis zum Termin',
                    'Bilder von Zählerschränken, Anlagen und Ladepunkten',
                    'Ob Sie Wartungsverträge anbieten und was darin steht',
                ],

                'beachten' => [
                    ['titel' => 'Aussagen zu Erträgen',
                     'text'  => 'Ein genannter Jahresertrag ist eine Zusage. Wir schreiben, '
                        . 'wovon er abhängt — Ausrichtung, Verschattung, Dachfläche —, nicht '
                        . 'welchen Sie erreichen.'],
                    ['titel' => 'Netzbetreiber und Anmeldung',
                     'text'  => 'Wer die Anmeldung übernimmt, ist die häufigste Rückfrage vor '
                        . 'einem Photovoltaik-Auftrag. Das gehört auf die Seite, nicht ins '
                        . 'Erstgespräch.'],
                    ['titel' => 'Preise für Anlagen',
                     'text'  => 'Ein Anlagenpreis ohne Ortstermin ist geraten. Auf der Seite '
                        . 'steht deshalb, wovon er abhängt und was der Ortstermin kostet — '
                        . 'wenn er etwas kostet.'],
                    ['titel' => 'Elektroprüfung und Fristen',
                     'text'  => 'Prüffristen für ortsfeste Anlagen und Betriebsmittel stehen in '
                        . 'Vorschriften, die sich ändern können. Wir nennen keine Frist als '
                        . 'Zusage, sondern schreiben, dass Sie prüfen und dokumentieren — und '
                        . 'wonach sich der Abstand richtet.'],
                ],

                'beispiel' => [
                    'titel' => 'Ein Betrieb mit sechs Beschäftigten, Umfang Wachstum',
                    'text'  => 'Bis zu 8 Seiten, rund 3.500 Wörter, 2 Korrekturrunden für '
                        . '3.900 € netto einmalig und 129 € netto im Monat.',
                    'seiten' => [
                        'Startseite',
                        'Photovoltaik und Speicher',
                        'Ladepunkte',
                        'Gewerbeinstallation',
                        'Elektroprüfung',
                        'Über den Betrieb',
                        'Kontakt',
                        'Impressum',
                    ],
                ],

                'fragen' => [
                    ['frage'   => 'Lohnt sich eine eigene Photovoltaik-Seite noch?',
                     'antwort' => 'Wer weiterhin Anlagen baut, braucht eine Seite, die die Frage '
                        . 'beantwortet. Ob das Geschäft wächst oder schrumpft, ändert daran '
                        . 'nichts — es ändert nur, wie viele Anbieter danebenstehen.'],
                    ['frage'   => 'Können Sie technische Angaben prüfen?',
                     'antwort' => 'Nein. Wir schreiben, was Sie uns geben, und legen Ihnen jede '
                        . 'Fachaussage vor der Veröffentlichung vor.'],
                    ['frage'   => 'Ich habe kaum Bilder. Reicht das?',
                     'antwort' => 'Ein Zählerschrank vom Telefon reicht. Wir bereiten ihn auf. '
                        . 'Bestandsfotos setzen wir nicht ein — eine gekaufte Anlage auf Ihrer '
                        . 'Seite fällt jedem auf, der Anlagen baut.'],
                    ['frage'   => 'Wir machen überwiegend Gewerbe, kaum Privatkunden. Passt das?',
                     'antwort' => 'Dann bekommt die Gewerbeinstallation die Hauptseite und '
                        . 'Photovoltaik eine Nebenseite. Die Struktur folgt Ihrem Geschäft, '
                        . 'nicht der Branche.'],
                ],

                'zahlen' => [
                    ['satz'   => 'Vom E-Handwerk installierte Photovoltaik-Anlagen: 395.000 → 355.000.',
                     'quelle' => 'ZVEH-Frühjahrsumfrage 2026, 1.641 Betriebe'],
                    ['satz'   => 'Ladepunkte: 377.000 → 360.000.',
                     'quelle' => 'ZVEH-Frühjahrsumfrage 2026, 1.641 Betriebe'],
                    ['satz'   => '72 % der Betriebe haben zu viel zu tun für Digitalisierung.',
                     'quelle' => 'Bitkom 2025, n=504'],
                ],

                'quellen' => [
                    'SARTU_BRANCHENFAKTEN.md Abschnitt 3 (E1, E2, E3) — ZVEH-Frühjahrsumfrage 2026',
                    'SARTU_KUNDENMOTIVE_BELEGT.md, Motive 1 und 4 — Bitkom 2025, n=504',
                ],
            ],

            'dachdecker' => [
                'branche' => 'Dachdeckerbetriebe',
                'h1'      => 'Website für Dachdeckerbetriebe',
                'titel'   => 'Website für Dachdecker zum Festpreis | SARTU',
                'beschreibung' => 'Website für Dachdeckerbetriebe ab 1.490 € netto: eigene '
                    . 'Seiten für Steildach, Flachdach, Abdichtung und Sanierung. Texte '
                    . 'inklusive, Betrieb ab 59 € im Monat.',
                'kurz' => 'Wir bauen Ihre Dachdecker-Website zum Festpreis ab 1.490 € netto — '
                    . 'mit einer eigenen Seite je Dachart, geschriebenen Texten und Betrieb ab '
                    . '59 € netto im Monat. Sie liefern die Fakten in einem Gespräch.',

                'probleme' => [
                    ['titel' => 'Der Wettbewerb im Umkreis sieht genauso aus',
                     'text'  => '78 % der Dachdeckerbetriebe haben weniger als zehn gewerbliche '
                        . 'Arbeitnehmer, 53 % weniger als fünf (ZVDH-Steckbrief März 2026, '
                        . 'Stand 31.12.2024). Sie kennen jeden Wettbewerber im Umkreis — und '
                        . 'deren Websites sehen aus wie Ihre.'],
                    ['titel' => 'Gleicher Umsatz, mehr Arbeit',
                     'text'  => 'Umsatz 2025: 13,5 Mrd. €, Veränderung 0,0 % — bei 4,5 % '
                        . 'Preissteigerung (ZVDH-Steckbrief März 2026). Das ist real weniger '
                        . 'für dieselbe Arbeit.'],
                    ['titel' => 'Das komplette Dach steht nicht auf der Seite',
                     'text'  => 'Wer ein ganzes Dach vergibt, entscheidet über einen '
                        . 'fünfstelligen Auftrag. Er will vorher wissen, wie der Aufbau '
                        . 'aussieht, wie lange es dauert und wer das Gerüst stellt. Steht das '
                        . 'nicht da, ruft er drei Betriebe an und nimmt den, der zuerst '
                        . 'erklärt.'],
                    ['titel' => 'Bewerber suchen zuerst die Website',
                     'text'  => 'Die Zahl der gewerblichen Arbeitnehmenden ging 2025 um 1,0 % '
                        . 'zurück (ZVDH-Steckbrief März 2026). Es ist das vierte '
                        . 'Rückgangsjahr in Folge; Ursache ist der Renteneintritt der '
                        . 'Babyboomer. Wer jemanden sucht, wird zuerst nachgesehen.'],
                    ['titel' => 'Das Gerüst steht in keinem Angebot, aber in jeder Rückfrage',
                     'text'  => 'Wer ein Dach vergibt, will wissen, ob Gerüst, Entsorgung und '
                        . 'Anmeldung dabei sind. Das entscheidet über vierstellige Beträge und '
                        . 'wird trotzdem erst beim Ortstermin geklärt. Steht es auf der Seite, '
                        . 'kommt die Anfrage schon mit der richtigen Erwartung.'],
                ],

                'gehoert_drauf' => [
                    'Je eine Seite für Steildach, Flachdach, Abdichtung und Sanierung',
                    'Wie ein Dachaufbau bei Ihnen aussieht — Schicht für Schicht',
                    'Ob Sie Gerüst und Entsorgung übernehmen',
                    'Wie lange ein Dach von der Zusage bis zur Fertigstellung braucht',
                    'Ihr Einzugsgebiet',
                    'Bilder fertiger Dächer, nicht nur Baustellenaufnahmen',
                ],

                'beachten' => [
                    ['titel' => 'Aussagen zur Haltbarkeit',
                     'text'  => 'Eine genannte Lebensdauer ist eine Zusage. Wir schreiben, was '
                        . 'der Hersteller angibt und wovon sie abhängt — nicht, wie lange Ihr '
                        . 'Dach hält.'],
                    ['titel' => 'Sturmschäden',
                     'text'  => 'Sturmschadenarbeit ist Geschäft, das Sie mitnehmen. Sie gehört '
                        . 'auf die Seite, aber nicht in den Aufmacher. Sonst kommen Anrufe '
                        . 'für Arbeit, von der Sie nicht leben.'],
                    ['titel' => 'Energetische Sanierung und Förderung',
                     'text'  => 'Dämmung und Förderung hängen zusammen und ändern sich. Wir '
                        . 'trennen Ihre Leistung von den Bedingungen des Programms, damit eine '
                        . 'Änderung eine Zeile kostet und nicht eine Seite.'],
                    ['titel' => 'Arbeiten in der Höhe',
                     'text'  => 'Aufnahmen ohne sichtbare Absturzsicherung sind ein Eigentor. Sie '
                        . 'zeigen Bewerbern und Auftraggebern das Gegenteil dessen, was Sie '
                        . 'verkaufen wollen. Wir sortieren solche Bilder aus.'],
                    ['titel' => 'Termine in der Saison',
                     'text'  => 'Ein Dach wird nicht im Januar gedeckt, und eine Anfrage im '
                        . 'März meint den Sommer. Schwankt Ihre Auslastung saisonal, gehört '
                        . 'das auf die Seite. Sonst rufen im Frühjahr Leute an, denen Sie '
                        . 'absagen müssen.'],
                ],

                'beispiel' => [
                    'titel' => 'Ein Betrieb mit acht Beschäftigten, Umfang Wachstum',
                    'text'  => 'Bis zu 8 Seiten, rund 3.500 Wörter, 2 Korrekturrunden für '
                        . '3.900 € netto einmalig und 129 € netto im Monat.',
                    'seiten' => [
                        'Startseite',
                        'Steildach',
                        'Flachdach und Abdichtung',
                        'Dachsanierung und Dämmung',
                        'Über den Betrieb',
                        'Arbeiten bei uns',
                        'Kontakt',
                        'Impressum',
                    ],
                ],

                'fragen' => [
                    ['frage'   => 'Brauche ich einen Bereich für Bewerbungen?',
                     'antwort' => 'Wenn Sie suchen, ja. Wer sich bewirbt, sieht zuerst auf die '
                        . 'Website — und findet dort meistens nur eine E-Mail-Adresse.'],
                    ['frage'   => 'Was ist mit Bildern von Baustellen?',
                     'antwort' => 'Baustellenbilder zeigen Arbeit, fertige Dächer zeigen das '
                        . 'Ergebnis. Wir nehmen beides, aber das Ergebnis nach vorn.'],
                    ['frage'   => 'Ich mache fast nur Sanierung. Passt das?',
                     'antwort' => 'Dann bekommt die Sanierung die Hauptseite und der Neubau eine '
                        . 'Nebenseite. Die Struktur folgt Ihrem Geschäft, nicht der Branche.'],
                    ['frage'   => 'Soll ich Preise für ein Dach angeben?',
                     'antwort' => 'Nein. Eine Zahl ohne Ortstermin ist geraten, und eine '
                        . 'Preisspanne erzeugt Anfragen, die Sie wieder absagen. Auf die Seite '
                        . 'gehört, wovon der Preis abhängt — Dachform, Fläche, Aufbau, Gerüst.'],
                ],

                'zahlen' => [
                    ['satz'   => '78 % der Dachdeckerbetriebe haben weniger als zehn gewerbliche Arbeitnehmer, 53 % weniger als fünf.',
                     'quelle' => 'ZVDH-Steckbrief März 2026, Stand 31.12.2024'],
                    ['satz'   => 'Umsatz 2025: 13,5 Mrd. €, Veränderung 0,0 % bei 4,5 % Preissteigerung.',
                     'quelle' => 'ZVDH-Steckbrief März 2026'],
                    ['satz'   => 'Gewerbliche Arbeitnehmende minus 1,0 % in 2025 — viertes Rückgangsjahr.',
                     'quelle' => 'ZVDH-Steckbrief März 2026'],
                ],

                'quellen' => [
                    'SARTU_BRANCHENFAKTEN.md Abschnitt 4 (D1, D2, D3) — ZVDH-Steckbrief März 2026',
                ],
            ],
        ];
    }

    /** @return array<string,mixed>|null */
    public static function finden(string $schluessel): ?array
    {
        return self::alle()[$schluessel] ?? null;
    }
}
