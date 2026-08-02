<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Transparenzseiten (§11a) und Vergleichsartikel (§12).
 *
 * ## Warum beide in einer Klasse und unter einem Hub stehen
 *
 * §12: „Der Hub listet Ratgeber **und** Transparenzseiten (§11a), weil sie für Leser
 * dasselbe sind." Die Abgrenzung dazwischen ist eine Frage des Inhalts, nicht der Ablage:
 * Wo **Zahlen** im Mittelpunkt stehen, ist es eine Transparenzseite; wo es um eine
 * **Entscheidung zwischen Optionen** geht, ein Vergleichsartikel.
 *
 * ## Die harten Regeln aus §11a
 *
 * | Regel | Wie sie hier eingehalten wird |
 * |---|---|
 * | Jede Zahl stammt aus dem **eigenen** Angebot | Preise kommen aus `Preise::tabelle()`, nicht aus dem Text |
 * | **Keine** Marktdurchschnitte, Studien, Wettbewerberpreise | Über fremde Anbieter steht nur, **woraus** ihr Preis entsteht — nie **wie hoch** er ist |
 * | Über fremde Anbieter nur in **Kategorien** | „Baukasten", „Freelancer", „Agentur". Kein Name |
 * | **Preise als Text, nie als Bild** | Es gibt auf diesen Seiten kein Bild |
 * | **Antwort zuerst** — die ersten 40–60 Wörter mit Zahl | Feld `kurzantwort`, steht direkt unter der H1 |
 * | Vergleiche als **Tabelle** | Feld `tabelle` |
 * | Sichtbares Aktualisierungsdatum | Feld `stand`, in der Ansicht sichtbar |
 *
 * ## Was nach dem Launch kommt
 *
 * §11a und §12 nennen sechs weitere Artikel für später. Sie werden **nicht** vorbereitend
 * angelegt — ein Hub mit leeren Einträgen ist ein „kommt bald"-Bereich (§0.3b).
 */
final class Ratgeber
{
    public const STAND = '2026-08-02';

    public const HUB_TITEL = 'Ratgeber für Firmenwebsites | SARTU';

    public const HUB_BESCHREIBUNG = 'Was eine Firmenwebsite kostet, was der Betrieb kostet, '
        . 'was nicht enthalten ist — und wann Agentur, Freelancer oder Baukasten passt. Mit '
        . 'SARTUs eigenen Zahlen.';

    public const HUB_H1 = 'Ratgeber für Firmenwebsites';

    public const HUB_INTRO = 'Hier stehen die Fragen, die vor jedem Auftrag kommen — mit '
        . 'unseren eigenen Zahlen beantwortet. Fremde Preise nennen wir nicht, weil wir sie '
        . 'nicht kennen.';

    /**
     * @return array<string,array{
     *     titel:string, h1:string, beschreibung:string, kurzantwort:string,
     *     abschnitte:list<array{h2:string,absaetze:list<string>}>,
     *     tabelle:array{kopf:list<string>,zeilen:list<list<string>>}|null,
     *     verweise:list<array{0:string,1:string}>, ziel:array{0:string,1:string}
     * }>
     */
    public static function alle(): array
    {
        return [
            'was-kostet-eine-firmenwebsite' => [
                'titel' => 'Was kostet eine Firmenwebsite? Zahlen statt Spannen | SARTU',
                'h1'    => 'Was kostet eine Firmenwebsite?',
                'beschreibung' => 'Eine Firmenwebsite kostet bei SARTU 1.490 bis 7.900 € netto '
                    . 'einmalig plus 59 bis 249 € netto im Monat. Woraus sich der Preis '
                    . 'zusammensetzt und was laufend dazukommt.',
                'kurzantwort' => 'Bei SARTU kostet eine Firmenwebsite 1.490 € netto einmalig für '
                    . 'eine Seite, 3.900 € für bis zu acht und 7.900 € für bis zu sechzehn. '
                    . 'Dazu kommen 59, 129 oder 249 € netto im Monat für den Betrieb. Im ersten '
                    . 'Jahr sind das 2.198, 5.448 oder 10.888 € netto.',
                'abschnitte' => [
                    ['h2' => 'Woraus der Preis entsteht',
                     'absaetze' => [
                        'Ein Websitepreis besteht aus vier Teilen: Planung, Texte, '
                            . 'Programmierung und Betrieb. Wer nur drei davon bezahlt, zahlt den '
                            . 'vierten später — meist in eigener Arbeitszeit.',
                        'Die Planung legt fest, welche Seiten es gibt. Die Texte entstehen aus '
                            . 'Ihren Fakten. Die Programmierung baut daraus die Seite. Der '
                            . 'Betrieb hält sie danach am Laufen.',
                        'Bei SARTU stecken alle vier im Festpreis. Es gibt keine Position, die '
                            . 'später dazukommt.',
                     ]],
                    ['h2' => 'Was einmalig kostet und was laufend',
                     'absaetze' => [
                        'Der Einmalpreis deckt alles bis zum Livegang. Der Monatsbetrag deckt '
                            . 'alles danach.',
                        'Beide Zahlen stehen im Angebot, bevor Sie entscheiden. Die '
                            . 'Erstlaufzeit beträgt 12 Monate ab dem Tag, an dem die Website '
                            . 'online geht.',
                     ]],
                    ['h2' => 'Warum SARTU keine Preisspannen nennt',
                     'absaetze' => [
                        'Eine Spanne über zwei Größenordnungen beantwortet die Frage nicht. Sie '
                            . 'verschiebt die Antwort auf ein Gespräch.',
                        'Deshalb stehen hier drei feste Zahlen und ein Startwert für '
                            . 'Sonderprojekte. Was in Ihrem Fall gilt, sagt der Bedarfsscheck '
                            . 'in etwa drei Minuten.',
                     ]],
                    ['h2' => 'Was wir über andere Anbieter sagen — und was nicht',
                     'absaetze' => [
                        'Wir nennen keine fremden Preise. Wir kennen sie nicht, und eine '
                            . 'geschätzte Zahl wäre eine erfundene Zahl.',
                        'Was sich sagen lässt, ist, **woraus** der Preis bei den einzelnen '
                            . 'Anbieterarten entsteht. Das steht in der Tabelle.',
                     ]],
                ],
                'tabelle' => [
                    'kopf'   => ['Anbieterart', 'Woraus der Preis entsteht', 'Wer die Texte schreibt', 'Wer danach pflegt'],
                    'zeilen' => [
                        ['Baukasten', 'Softwaregebühr je Monat plus Ihre eigene Arbeitszeit', 'Sie', 'Sie'],
                        ['Freelancer', 'Stundensatz mal Stunden', 'meist Sie', 'oft niemand'],
                        ['Agentur', 'Stundensatz mal Stunden, häufig ohne Obergrenze', 'unterschiedlich', 'gegen Aufpreis'],
                        ['SARTU', 'Festpreis vor Ihrer Entscheidung', 'wir', 'wir, ab 59 € netto im Monat'],
                    ],
                ],
                'verweise' => [
                    ['/preise', 'Alle Preise im Einzelnen'],
                    ['/ratgeber/was-der-betrieb-kostet', 'Was der Betrieb kostet'],
                    ['/ratgeber/was-nicht-enthalten-ist', 'Was nicht enthalten ist'],
                ],
                'ziel' => ['/preise', 'Preise ansehen'],
            ],

            'was-nicht-enthalten-ist' => [
                'titel' => 'Was bei SARTU nicht enthalten ist | SARTU',
                'h1'    => 'Was bei SARTU nicht enthalten ist',
                'beschreibung' => 'Die vollständige Ausschlussliste im Klartext: Rechtstexte, '
                    . 'Fotografie, Shop, Buchung, unbegrenzte Änderungen — und warum es keine '
                    . 'Zusatzoptionen gibt.',
                'kurzantwort' => 'Nicht enthalten sind Rechtstexte und Fotoaufnahmen. Ebenso '
                    . 'Shop, Kundenlogin, Buchung und Schnittstellen zu Ihrer Software. Dazu '
                    . 'unbegrenzte Text- und Designänderungen und laufende SEO-Arbeit nach dem '
                    . 'Livegang. Alles davon gibt es — mit eigenem Festpreis.',
                'abschnitte' => [
                    ['h2' => 'Rechtstexte',
                     'absaetze' => [
                        'Impressum, Datenschutzerklärung und AGB kommen von Ihnen oder Ihrer '
                            . 'Kanzlei. Wir binden freigegebene Texte technisch ein.',
                        'Der Grund ist nicht Bequemlichkeit: Ein Rechtstext, der plausibel '
                            . 'klingt und falsch ist, ist gefährlicher als keiner.',
                     ]],
                    ['h2' => 'Bilder und Aufnahmen',
                     'absaetze' => [
                        'Fotos Ihres Betriebs machen wir nicht. Sie liefern Bilder, an denen Sie '
                            . 'die Rechte haben; wir bereiten sie auf.',
                        'Bestandsfotos setzen wir nicht ein.',
                     ]],
                    ['h2' => 'Funktionen, die ein Sonderprojekt sind',
                     'absaetze' => [
                        'Shop, Kundenlogin, Terminbuchung, Zahlungsabwicklung und '
                            . 'Schnittstellen zu Ihrer Software gehören nicht in den Festpreis '
                            . 'einer Standardstufe.',
                        'Sie bekommen dafür ein eigenes Angebot ab 12.500 € netto — mit '
                            . 'Festpreis vor Ihrer Entscheidung. Oder eine Absage, wenn wir das '
                            . 'Ergebnis nicht verantworten können.',
                     ]],
                    ['h2' => 'Warum es keine Zusatzoptionen gibt',
                     'absaetze' => [
                        'Eine Aufpreisliste macht den Preis unklar. Wer sie hat, verkauft die '
                            . 'Grundleistung unter Preis und den Rest einzeln.',
                        'Bei SARTU endet ein Standardangebot exakt beim genannten Festpreis. '
                            . 'Passt eine Anforderung nicht hinein, sagen wir das vorher — nicht '
                            . 'auf der Schlussrechnung.',
                     ]],
                ],
                'tabelle' => [
                    'kopf'   => ['Nicht enthalten', 'Was stattdessen gilt'],
                    'zeilen' => [
                        ['Rechtstexte', 'Sie liefern sie, wir binden sie ein'],
                        ['Fotoaufnahmen', 'Sie liefern Bilder mit Rechten'],
                        ['Shop, Login, Buchung', 'Sonderprojekt ab 12.500 € netto'],
                        ['Unbegrenzte Änderungen', 'Kleine Anpassungen im Betrieb enthalten'],
                        ['Neue Seiten nach dem Launch', 'Eigenes Angebot mit eigenem Festpreis'],
                        ['Laufende SEO-Arbeit', 'Eigenes Angebot, nach echten Suchdaten'],
                    ],
                ],
                'verweise' => [
                    ['/preise', 'Was enthalten ist'],
                    ['/leistung-wartung', 'Der Rundum-Schutz'],
                ],
                'ziel' => ['/briefing', Auftragslage::KNOPF],
            ],

            'was-der-betrieb-kostet' => [
                'titel' => 'Was der Betrieb einer Website kostet | SARTU',
                'h1'    => 'Was der Betrieb einer Website kostet',
                'beschreibung' => 'Der Rundum-Schutz kostet 59, 129 oder 249 € netto im Monat. '
                    . 'Was darin steckt, was nicht — und was am Vertragsende mit Domain und '
                    . 'Website passiert.',
                'kurzantwort' => 'Der Betrieb kostet 59 € netto im Monat bei Start, 129 € bei '
                    . 'Wachstum, 249 € bei Platzhirsch. Enthalten sind Hosting, SSL, tägliche '
                    . 'Sicherungen, Überwachung, technische Aktualisierungen und Ihr Zugang '
                    . 'zum Kundenbereich. Die Erstlaufzeit beträgt 12 Monate.',
                'abschnitte' => [
                    ['h2' => 'Was in dem Betrag steckt',
                     'absaetze' => [
                        'Sieben Posten, die sonst einzeln anfallen: Server, Zertifikat, '
                            . 'Sicherungen, Überwachung, Aktualisierungen, technische '
                            . 'Suchgesundheit und der Kundenbereich.',
                        'Dazu kommt der Teil, den man nicht kaufen kann: Es gibt jemanden, der '
                            . 'anfasst, wenn etwas nicht funktioniert.',
                     ]],
                    ['h2' => 'Was nicht darin steckt',
                     'absaetze' => [
                        'Der Betrag ist kein Konto mit Änderungsminuten. Unbegrenzte Text- oder '
                            . 'Designänderungen, neue Seiten und neue Ziele sind nicht enthalten.',
                        'Kleine Anpassungen am Text schreiben Sie uns — das ist enthalten. Wo '
                            . 'die Grenze liegt, sagen wir Ihnen, bevor Aufwand entsteht.',
                     ]],
                    ['h2' => 'Die Domain',
                     'absaetze' => [
                        'Eine normale Domain bis 30 € netto im Jahr ist im Betrieb enthalten. '
                            . 'Inhaber bleiben Sie.',
                        'Teurere Endungen rechnen wir zum Einkaufspreis weiter.',
                     ]],
                    ['h2' => 'Was am Vertragsende passiert',
                     'absaetze' => [
                        'Die Domain bleibt Ihre. Sie geht auf Ihren Wunsch zu jedem anderen '
                            . 'Anbieter.',
                        'Den Programmcode und die Inhalte bekommen Sie ausgehändigt. Was Sie '
                            . 'damit tun, ist Ihre Entscheidung. Betreiben lässt sich die Seite '
                            . 'bei jedem Anbieter mit PHP und Datenbank.',
                        'Die Erstlaufzeit von 12 Monaten läuft ab dem Tag des Livegangs.',
                     ]],
                ],
                'tabelle' => null,
                'verweise' => [
                    ['/leistung-wartung', 'Der Rundum-Schutz im Einzelnen'],
                    ['/ratgeber/was-nicht-enthalten-ist', 'Was nicht enthalten ist'],
                ],
                'ziel' => ['/preise', 'Preise ansehen'],
            ],

            'agentur-freelancer-baukasten' => [
                'titel' => 'Agentur, Freelancer oder Baukasten? Der ehrliche Vergleich | SARTU',
                'h1'    => 'Website erstellen lassen: Agentur, Freelancer oder Baukasten?',
                'beschreibung' => 'Wann ein Baukasten reicht, was am Freelancer-Modell '
                    . 'schiefgehen kann und warum Agenturen selten Festpreise nennen. Mit einer '
                    . 'Entscheidungshilfe in fünf Fragen.',
                'kurzantwort' => 'Ein Baukasten lohnt sich, wenn Sie selbst pflegen wollen und '
                    . 'wenig Anspruch an Struktur haben. Ein Freelancer kostet wenig und ist '
                    . 'ein Ausfallrisiko. Eine Agentur liefert Verlässlichkeit — meist ohne '
                    . 'Festpreis und mit mehr Terminen, als Ihnen lieb ist.',
                'abschnitte' => [
                    ['h2' => 'Wann ein Baukasten wirklich reicht',
                     'absaetze' => [
                        'Wenn Sie eine Seite brauchen, sie selbst füllen wollen und Zeit dafür '
                            . 'haben, ist ein Baukasten die richtige Wahl. Er ist an einem Abend '
                            . 'aufgebaut.',
                        'Danach stehen die Textfelder leer da. Bis jemand sie füllt, geht die '
                            . 'Seite nicht online. Genau dort bleiben die meisten '
                            . 'Baukastenseiten hängen.',
                        'Wer das weiß und trotzdem will, soll einen nehmen. Wir sagen das '
                            . 'lieber hier als nach dem Auftrag.',
                     ]],
                    ['h2' => 'Was am Freelancer-Modell schiefgehen kann',
                     'absaetze' => [
                        'Eine Person baut, und dieselbe Person ist auch der Betrieb danach. '
                            . 'Fällt sie aus, fällt die Website mit aus.',
                        'Der zweite Punkt ist die Abrechnung: Ohne Festpreis wächst der Betrag '
                            . 'mit dem Aufwand, und den kennt vorher niemand.',
                     ]],
                    ['h2' => 'Warum Agenturen selten Festpreise nennen',
                     'absaetze' => [
                        'Weil sie den Umfang vorher nicht kennen. Wer nicht weiß, wie viele '
                            . 'Seiten es werden, kann keinen Preis nennen — und rechnet deshalb '
                            . 'nach Stunden.',
                        'SARTU dreht das um: Erst legen wir den Umfang fest, dann steht der '
                            . 'Preis. Was nicht hineinpasst, bekommt ein eigenes Angebot.',
                     ]],
                    ['h2' => 'Für wen SARTU nicht passt',
                     'absaetze' => [
                        'Wenn Sie Layout und Seitenstruktur selbst bestimmen wollen, sind Sie '
                            . 'bei uns falsch. Diese Entscheidungen treffen wir.',
                        'Wenn Sie eine private Seite oder ein Hobbyprojekt bauen lassen '
                            . 'wollen, ebenfalls. SARTU arbeitet ausschließlich für '
                            . 'Unternehmer.',
                        'Und wenn Sie Ihre Inhalte täglich selbst ändern wollen, brauchen Sie '
                            . 'ein Redaktionssystem — das ist ein anderes Produkt.',
                     ]],
                    ['h2' => 'Entscheidungshilfe in fünf Fragen',
                     'absaetze' => [
                        'Haben Sie Zeit, Texte selbst zu schreiben? Wollen Sie selbst '
                            . 'aktualisieren? Brauchen Sie einen festen Preis vorab? Muss '
                            . 'jemand erreichbar sein, wenn etwas ausfällt? Sollen mehrere '
                            . 'Leistungen einzeln gefunden werden?',
                        'Dreimal Nein bei den ersten beiden und dreimal Ja bei den letzten drei: '
                            . 'Dann passt ein Festpreisangebot mit Betrieb.',
                     ]],
                ],
                'tabelle' => [
                    'kopf'   => ['Anbieterart', 'Woraus der Preis entsteht', 'Ihr Zeitaufwand', 'Risiko', 'Pflege danach'],
                    'zeilen' => [
                        ['Baukasten', 'Softwaregebühr plus Eigenleistung', 'hoch', 'Sie tragen es', 'Sie'],
                        ['Freelancer', 'Stundensatz mal Stunden', 'mittel', 'Ausfall einer Person', 'oft ungeklärt'],
                        ['Agentur', 'Stundensatz mal Stunden', 'mittel bis hoch', 'offener Endbetrag', 'gegen Aufpreis'],
                        ['SARTU', 'Festpreis vor der Entscheidung', 'gering', 'Festpreis begrenzt ihn', 'im Betrieb enthalten'],
                    ],
                ],
                'verweise' => [
                    ['/preise', 'Preise ansehen'],
                    ['/ratgeber/was-kostet-eine-firmenwebsite', 'Was eine Firmenwebsite kostet'],
                ],
                'ziel' => ['/preise', 'Preise ansehen'],
            ],

            'webdesign-ohne-wordpress' => [
                'titel' => 'Firmenwebsite ohne WordPress — Vorteile und Grenzen | SARTU',
                'h1'    => 'Firmenwebsite ohne WordPress: Wann sich das lohnt',
                'beschreibung' => 'Ohne WordPress entfallen Plugin-Updates, Sicherheitslücken '
                    . 'und Kompatibilitätsprobleme. Der Preis dafür ist weniger Selbstbedienung.',
                'kurzantwort' => 'Ohne WordPress entfallen Plugin-Updates, Sicherheitslücken und '
                    . 'Kompatibilitätsprobleme. Der Preis dafür ist weniger Selbstbedienung — '
                    . 'Inhalte ändert nicht mehr jeder selbst.',
                'abschnitte' => [
                    ['h2' => 'Warum WordPress so verbreitet ist',
                     'absaetze' => [
                        'Es ist kostenlos, es gibt Vorlagen für jeden Zweck, und fast jeder '
                            . 'Dienstleister kennt es.',
                        'Für eine Seite, die jemand regelmäßig selbst pflegt, ist das eine '
                            . 'gute Grundlage.',
                     ]],
                    ['h2' => 'Was daran im Alltag Arbeit macht',
                     'absaetze' => [
                        'Eine WordPress-Installation besteht aus dem Kern, einem Design und '
                            . 'mehreren Erweiterungen. Jedes Teil bekommt eigene '
                            . 'Aktualisierungen.',
                        'Wer sie nicht einspielt, sammelt Sicherheitslücken. Wer sie einspielt, '
                            . 'riskiert, dass zwei Teile nicht mehr zusammenpassen.',
                        'Beides ist Arbeit, die jemand machen muss — und in vielen Betrieben '
                            . 'macht sie niemand.',
                     ]],
                    ['h2' => 'Welche Alternativen es gibt',
                     'absaetze' => [
                        'Ein Baukasten nimmt Ihnen die Technik ab und gibt Ihnen die Texte '
                            . 'zurück. Eine statische Seite ist schnell und lässt sich schwer '
                            . 'ändern. Eine individuell programmierte Seite gibt Ihnen beides '
                            . 'in fremde Hand — Technik und Änderungen.',
                     ]],
                    ['h2' => 'Was man dabei aufgibt',
                     'absaetze' => [
                        'Sie können Seiten nicht selbst anlegen und das Layout nicht selbst '
                            . 'umbauen.',
                        'Bei SARTU pflegen Sie Öffnungszeiten und Kontaktdaten selbst. Texte, '
                            . 'Bilder und Struktur ändern wir für Sie. Das ist im Betrieb '
                            . 'enthalten und dauert meist einen Werktag.',
                     ]],
                    ['h2' => 'Wie SARTU es löst',
                     'absaetze' => [
                        'Wir programmieren die Seite ohne Fremdsystem darunter. Es gibt keine '
                            . 'Erweiterung, die aktualisiert werden müsste, und keine Vorlage, '
                            . 'die tausend andere Betriebe auch haben.',
                        'Der Betrieb ab 59 € netto im Monat übernimmt alles, was danach '
                            . 'anfällt.',
                     ]],
                ],
                'tabelle' => [
                    'kopf'   => ['Für wen', 'Was passt', 'Warum'],
                    'zeilen' => [
                        ['Sie pflegen täglich selbst', 'Redaktionssystem', 'Sie brauchen den Zugriff'],
                        ['Sie pflegen selten selbst', 'individuell programmiert', 'Kein Aktualisierungsaufwand für Sie'],
                        ['Sie wollen alles selbst machen', 'Baukasten', 'Niedrigste laufende Kosten'],
                        ['Sie wollen nichts damit zu tun haben', 'Festpreis mit Betrieb', 'Jemand ist zuständig'],
                    ],
                ],
                'verweise' => [
                    ['/leistung-webdesign', 'Webdesign bei SARTU'],
                    ['/leistung-wartung', 'Was der Betrieb übernimmt'],
                ],
                'ziel' => ['/leistung-webdesign', 'Webdesign ansehen'],
            ],
        ];
    }

    /** @return array<string,mixed>|null */
    public static function finden(string $schluessel): ?array
    {
        return self::alle()[$schluessel] ?? null;
    }
}
