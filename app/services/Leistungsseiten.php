<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Die fünf Leistungsseiten — Website-Lastenheft §10.
 *
 * **Ein Template, fünf Datensätze.** §10 gibt die Reihenfolge der Blöcke verbindlich vor und
 * für alle fünf dieselbe. Fünf Ansichtsdateien wären fünf Gelegenheiten, die Reihenfolge
 * unterschiedlich zu bauen — §1.3 verbietet kopiertes Markup zwischen Seiten.
 *
 * ## Die Pflichtsätze je Seite
 *
 * §10 nennt sie einzeln und im Wortlaut. Sie stehen deshalb als eigenes Feld `pflichtsaetze`
 * und nicht irgendwo im Fließtext — sonst fällt beim Kürzen einer weg, und genau das ist bei
 * Seite 3 und 4 ein Haftungsrisiko.
 *
 * ## Was hier nicht steht
 *
 * `/leistung-domain-launch` und die Aufteilung von Seite 3 in getrennte SEO- und
 * Local-SEO-Seiten sind laut §10 auf Stufe 2 verschoben. Sie werden nicht vorbereitend
 * angelegt.
 */
final class Leistungsseiten
{
    /**
     * @return array<string,array{
     *     h1:string, titel:string, beschreibung:string, kurz:string,
     *     fuer_wen:list<string>, enthalten:list<string>, nicht_enthalten:list<string>,
     *     kosten:string, ablauf:list<string>, abgenommen:list<string>,
     *     pflichtsaetze:list<string>, fragen:list<array{frage:string,antwort:string}>
     * }>
     */
    public static function alle(): array
    {
        return [
            'webdesign' => [
                'h1'    => 'Webdesign, das nicht nach Baukasten aussieht.',
                'titel' => 'Webdesign für Firmenwebsites ohne WordPress | SARTU',
                'beschreibung' => 'Individuell programmierte Firmenwebsite ab 1.490 € netto — '
                    . 'ohne WordPress, ohne Baukasten und ohne Erweiterungen, die Sie selbst '
                    . 'aktualisieren müssten.',
                'kurz' => 'Wir programmieren Ihre Firmenwebsite individuell aus unserem '
                    . 'Designsystem — ab 1.490 € netto, ohne WordPress, ohne Baukasten und ohne '
                    . 'Plugins, die Sie pflegen müssten.',
                'fuer_wen' => [
                    'Betriebe mit 3 bis 30 Beschäftigten, die eine eigene Website statt einer Vorlage wollen',
                    'Betriebe, deren vorhandene Seite seit Jahren niemand angefasst hat',
                    'Betriebe, die niemanden haben, der sich um Aktualisierungen kümmert',
                ],
                'enthalten' => [
                    'Seitenstruktur aus Ihrem Ziel abgeleitet',
                    'Gestaltung aus einem festen System — Farben, Schriften, Formen liegen fest',
                    'Programmierung als eigener Code, ohne Fremdsystem darunter',
                    'Bedienbar auf Telefon, Tablet und Rechner',
                    'Ladezeit im Labor gemessen, bevor die Seite live geht',
                ],
                'nicht_enthalten' => [
                    'Ein Redaktionssystem, in dem Sie das Layout selbst umbauen',
                    'Shop, Kundenlogin oder Buchung — das ist ein Sonderprojekt',
                    'Fotos und Videoaufnahmen',
                ],
                'kosten' => 'Ab 1.490 € netto einmalig, zuzüglich 59 € netto im Monat für den '
                    . 'Betrieb. Der Preis steht vor Ihrer Entscheidung fest.',
                'ablauf' => [
                    'Bedarfsscheck in etwa drei Minuten',
                    'Geprüftes Angebot mit Umfang, Preis und Zahlungsplan',
                    'Ihre Fakten im Kundenbereich',
                    'Produktion',
                    'Vorschau, Ihre gesammelte Rückmeldung, Abnahme',
                    'Livegang und Betrieb',
                ],
                'abgenommen' => [
                    'Welche Seiten Ihr Ziel braucht',
                    'Wie die Seiten aufgebaut sind',
                    'Welche Technik darunter läuft',
                    'Wo die Website liegt',
                ],
                'pflichtsaetze' => [],
                'fragen' => [
                    ['frage'   => 'Bekomme ich einen Entwurf zur Auswahl?',
                     'antwort' => 'Sie bekommen eine fertige Vorschau, keine Auswahl aus drei '
                        . 'Entwürfen. Gestaltungsfragen entscheiden wir — dafür haften wir auch '
                        . 'für das Ergebnis.'],
                    ['frage'   => 'Kann ich das Design später ändern lassen?',
                     'antwort' => 'Kleine Anpassungen sind im Betrieb enthalten. Ein neues '
                        . 'Erscheinungsbild ist ein neues Projekt mit eigenem Festpreis.'],
                    ['frage'   => 'Was passiert mit meiner alten Website?',
                     'antwort' => 'Wir bauen neu und leiten die alten Adressen weiter, damit '
                        . 'vorhandene Verweise nicht ins Leere laufen.'],
                ],
            ],

            'texte' => [
                'h1'    => 'Website-Texte aus Ihren Fakten, nicht aus Floskeln.',
                'titel' => 'Website-Texte schreiben lassen | SARTU',
                'beschreibung' => 'Sie liefern Stichpunkte und Unterlagen, wir schreiben daraus '
                    . 'die Texte Ihrer Website. Enthalten in jedem Angebot ab 1.490 € netto.',
                'kurz' => 'Sie liefern Stichpunkte, Unterlagen und Fakten — wir schreiben daraus '
                    . 'die Texte Ihrer Website. Erfundene Belege, ungeprüfte Fachaussagen und '
                    . 'Rechtstexte sind ausgeschlossen.',
                'fuer_wen' => [
                    'Betriebe, deren Website seit dem Aufbau leere Textfelder hat',
                    'Betriebe, die keine Zeit haben, Seitentexte selbst zu schreiben',
                    'Betriebe, die mehrere Leistungen einzeln erklären müssen',
                ],
                'enthalten' => [
                    'Alle Seitentexte, geschrieben aus Ihren Angaben',
                    'Titel und Beschreibung je Seite für Suchmaschinen',
                    'Beschriftungen, Knopftexte und Fehlermeldungen',
                    'Rückfrage bei jeder Fachaussage, bevor sie auf die Seite kommt',
                ],
                'nicht_enthalten' => [
                    'Rechtstexte',
                    'Fachgutachten oder Rechtsauskünfte',
                    'Laufende Beiträge nach dem Livegang',
                ],
                'kosten' => 'Die Texte stecken im Festpreis. Bei Start sind es rund 1.200 '
                    . 'Wörter, bei Wachstum rund 3.500, bei Platzhirsch rund 6.500.',
                'ablauf' => [
                    'Wir fragen ab, was Ihr Betrieb macht, für wen und in welchem Gebiet',
                    'Sie laden vorhandene Unterlagen hoch',
                    'Wir schreiben die Texte je Seite',
                    'Sie lesen die Vorschau und melden zurück',
                    'Sie geben frei',
                ],
                'abgenommen' => [
                    'Welche Seite welches Thema bekommt',
                    'Wie lang ein Text wird',
                    'Welche Wörter in Titel und Überschrift stehen',
                ],
                'pflichtsaetze' => [
                    'Rechtstexte wie Impressum, Datenschutz und AGB sind nicht enthalten; wir '
                        . 'binden freigegebene Texte technisch ein.',
                ],
                'fragen' => [
                    ['frage'   => 'Muss ich etwas vorschreiben?',
                     'antwort' => 'Nein. Stichpunkte reichen. Was Sie im Erstgespräch einem '
                        . 'Kunden erzählen würden, ist genau das, was wir brauchen.'],
                    ['frage'   => 'Nutzen Sie KI dafür?',
                     'antwort' => 'Ja, beim Entwurf. Geprüft und freigegeben wird von uns — und '
                        . 'jede Fachaussage geht vorher an Sie zurück.'],
                    ['frage'   => 'Was, wenn mir ein Text nicht gefällt?',
                     'antwort' => 'Dann melden Sie es in der Korrekturrunde zurück. Start hat '
                        . 'eine Runde, Wachstum und Platzhirsch haben zwei.'],
                ],
            ],

            'seo-lokal' => [
                'h1'    => 'Gefunden werden — regional und in KI-Antworten.',
                'titel' => 'SEO-Grundlage und lokale Sichtbarkeit | SARTU',
                'beschreibung' => 'Jede SARTU-Website startet mit Seitenthemen, Metadaten, '
                    . 'strukturierten Daten und interner Verlinkung. Ohne Rankinggarantie und '
                    . 'ohne dünne Ortsseiten.',
                'kurz' => 'Jede SARTU-Website startet mit einer belastbaren SEO-Grundlage: klare '
                    . 'Seitenthemen, saubere Metadaten, strukturierte Daten, interne Verlinkung '
                    . 'und echte Unternehmensdaten. Ohne Rankinggarantie und ohne dünne '
                    . 'Ortsseiten.',
                'fuer_wen' => [
                    'Betriebe, die im Kartenbereich schlechter stehen als der Nachbar',
                    'Betriebe mit mehreren Leistungen, von denen nur eine gefunden wird',
                    'Betriebe, die schon einmal ein SEO-Paket ohne nachvollziehbares Ergebnis gekauft haben',
                ],
                'enthalten' => [
                    'Ein Seitenthema je Seite, abgeleitet aus echten Suchanfragen',
                    'Titel und Beschreibung je Seite, keine zwei gleich',
                    'Strukturierte Daten, interne Verlinkung, Sitemap',
                    'Gleiche Unternehmensdaten überall — Name, Anschrift, Telefonnummer',
                    'Ladezeit im Labor gemessen',
                ],
                'nicht_enthalten' => [
                    'Laufende SEO-Betreuung nach dem Livegang',
                    'Gekaufte Verweise',
                    'Ortsseiten, bei denen nur der Stadtname getauscht ist',
                ],
                'kosten' => 'Die Grundlage steckt im Festpreis jeder Stufe. Der laufende Ausbau '
                    . 'folgt echten Suchdaten und bekommt ein eigenes Angebot.',
                'ablauf' => [
                    'Wir prüfen, wonach in Ihrem Fach gesucht wird',
                    'Jede Suchabsicht bekommt genau eine Seite',
                    'Titel, Beschreibung und Struktur entstehen mit dem Text',
                    'Nach dem Livegang melden wir die Seite bei Search Console an',
                ],
                'abgenommen' => [
                    'Welche Suchabsicht welche Seite bekommt',
                    'Welche Wörter in Titel und Überschrift stehen',
                    'Welche strukturierten Daten ausgeliefert werden',
                ],
                'pflichtsaetze' => [
                    'Rankings, Anfragen oder Nennungen in KI-Systemen kann niemand garantieren.',
                    'Wir erstellen keine Ortsseiten, bei denen nur der Stadtname ausgetauscht ist.',
                ],
                'fragen' => [
                    ['frage'   => 'Wann stehe ich auf Seite eins?',
                     'antwort' => 'Das kann Ihnen niemand seriös sagen. Wir bauen das Fundament '
                        . 'und halten es sauber; wann und wofür Google eine Seite zeigt, '
                        . 'entscheidet Google.'],
                    ['frage'   => 'Brauche ich eine Seite je Ort?',
                     'antwort' => 'Nur, wenn sich über den Ort etwas Eigenes schreiben lässt. '
                        . 'Eine Seite, bei der nur der Stadtname getauscht ist, verstößt gegen '
                        . 'Googles Richtlinien.'],
                    ['frage'   => 'Was ist mit ChatGPT und ähnlichen Systemen?',
                     'antwort' => 'Es gibt dafür keine Sonderauszeichnung — Google sagt das '
                        . 'selbst. Was hilft, sind nachprüfbare Fakten als Text: Preise, '
                        . 'Umfänge, Grenzen. Genau das steht auf jeder SARTU-Seite.'],
                ],
            ],

            'wartung' => [
                'h1'    => 'Keine Wartung für Sie.',
                'titel' => 'Rundum-Schutz: Betrieb Ihrer Website | SARTU',
                'beschreibung' => 'Ab 59 € netto im Monat übernehmen wir Hosting, SSL, tägliche '
                    . 'Sicherungen, Überwachung und technische Aktualisierungen Ihrer Website.',
                'kurz' => 'Ab 59 € netto im Monat übernehmen wir den Betrieb: Hosting, SSL, '
                    . 'tägliche Sicherungen, Überwachung, technische Aktualisierungen, '
                    . 'technische Suchgesundheit und Ihren Zugang zum Kundenbereich. Kein Konto '
                    . 'mit Änderungsminuten.',
                'fuer_wen' => [
                    'Betriebe ohne eigene IT',
                    'Betriebe, deren letzte Website an einem abgelaufenen Vertrag gescheitert ist',
                    'Betriebe, die nicht wissen wollen, was ein Sicherheitsupdate ist',
                ],
                'enthalten' => [
                    'Hosting in der EU',
                    'SSL-Zertifikat',
                    'Tägliche Sicherungen',
                    'Überwachung der Erreichbarkeit',
                    'Technische Aktualisierungen',
                    'Technische Suchgesundheit im Blick',
                    'Prüfung der Formulare',
                    'Zugang zum Kundenbereich',
                ],
                'nicht_enthalten' => [
                    'Unbegrenzte Text- oder Designänderungen',
                    'Neue Seiten',
                    'Neue Ziele',
                ],
                'kosten' => '59 € netto im Monat bei Start, 129 € bei Wachstum, 249 € bei '
                    . 'Platzhirsch. Erstlaufzeit 12 Monate ab dem Livegang.',
                'ablauf' => [
                    'Der Betrieb beginnt am Tag des Livegangs',
                    'Wir rechnen monatlich ab',
                    'Änderungen am Text schreiben Sie uns über Ihren Bereich',
                    'Öffnungszeiten und Kontaktdaten pflegen Sie selbst',
                ],
                'abgenommen' => [
                    'Wo die Website liegt',
                    'Wann aktualisiert wird',
                    'Wie oft gesichert wird',
                    'Wer bei einer Störung anfasst',
                ],
                'pflichtsaetze' => [
                    'Der Rundum-Schutz bezahlt Betrieb, Sicherheit und Verantwortung — er ist '
                        . 'keine unbegrenzte Text- oder Design-Flatrate.',
                ],
                'fragen' => [
                    ['frage'   => 'Warum ist der Betrieb verpflichtend?',
                     'antwort' => 'Weil wir für die Website geradestehen. Ohne Zugriff auf '
                        . 'Server, Sicherungen und Aktualisierungen ginge das nicht.'],
                    ['frage'   => 'Was passiert, wenn ich kündige?',
                     'antwort' => 'Die Domain bleibt Ihre. Den Programmcode und die Inhalte '
                        . 'bekommen Sie ausgehändigt. Der Betrieb endet zum vereinbarten Datum.'],
                    ['frage'   => 'Sind Textänderungen enthalten?',
                     'antwort' => 'Kleine Anpassungen ja — schreiben Sie uns einfach. Neue Seiten '
                        . 'oder ein neues Ziel bekommen ein eigenes Angebot.'],
                ],
            ],

            'portal' => [
                'h1'    => 'Ein Kundenbereich, kein Website-Baukasten.',
                'titel' => 'Der SARTU-Kundenbereich: Freigaben und Pflege | SARTU',
                'beschreibung' => 'Angebot, Zahlung, Fragen, Dateien, Domain, Vorschau und '
                    . 'Freigabe liegen an einem Ort. Ohne Abstimmungstermine, ohne E-Mail-Ketten.',
                'kurz' => 'In Ihrem Bereich laufen Angebot, Zahlung, die Fragen zu Ihrem Betrieb, '
                    . 'Dateien, Domain, Vorschau, Freigabe und spätere kleine Pflege an einem '
                    . 'Ort. Layout, Code und Adressen bleiben bei uns.',
                'fuer_wen' => [
                    'Betriebe, die abends um zehn Zeit haben und nicht um zehn Uhr morgens',
                    'Betriebe, die schon einmal einen Anhang in einer E-Mail-Kette gesucht haben',
                    'Betriebe, die wissen wollen, was gerade ansteht',
                ],
                'enthalten' => [
                    'Anmeldung ohne Passwort — Sie bekommen jedes Mal einen Link per E-Mail',
                    'Angebot ansehen und annehmen',
                    'Rechnungen und Laufzeit einsehen',
                    'Fragen zu Ihrem Betrieb beantworten',
                    'Logo, Bilder und Unterlagen hochladen',
                    'Vorschau ansehen und Rückmeldungen sammeln',
                    'Freigeben — mit Ankreuzen und getipptem Namen',
                    'Domainstand einsehen',
                    'Öffnungszeiten selbst pflegen',
                    'Nachrichten an Ihren Betreuer',
                ],
                'nicht_enthalten' => [
                    'Ein Baukasten, in dem Sie Seiten selbst zusammenstellen',
                    'Mehrere Zugänge je Betrieb',
                    'Zugriff auf Layout und Programmcode',
                ],
                'kosten' => 'Der Zugang steckt im Betrieb ab 59 € netto im Monat. Er kostet '
                    . 'nichts extra und lässt sich nicht einzeln buchen.',
                'ablauf' => [
                    'Nach dem Angebot bekommen Sie eine Einladung per E-Mail',
                    'Sie melden sich mit einem Link an, der 15 Minuten gilt',
                    'Drei Bildschirme erklären, was wo liegt',
                    'Danach sehen Sie immer Ihren nächsten Schritt',
                ],
                'abgenommen' => [
                    'Welche Fragen wann kommen',
                    'Wie die Vorschau bereitgestellt wird',
                    'Was eine Korrekturrunde umfasst',
                ],
                'pflichtsaetze' => [],
                'fragen' => [
                    ['frage'   => 'Brauche ich ein Passwort?',
                     'antwort' => 'Nein. Sie bekommen jedes Mal einen Anmeldelink per E-Mail. Er '
                        . 'gilt 15 Minuten und lässt sich einmal verwenden.'],
                    ['frage'   => 'Können mehrere Personen aus meinem Betrieb hinein?',
                     'antwort' => 'Zurzeit gibt es einen Zugang je Betrieb. Wer ihn nutzt, '
                        . 'entscheiden Sie.'],
                    ['frage'   => 'Was, wenn ich lieber anrufe?',
                     'antwort' => 'Sprechen können Sie trotzdem mit uns. Sie müssen nur nicht.'],
                ],
            ],
        ];
    }

    /** §10 Seite 5 — zwei Listen, Wortlaut wie Startseite Sektion 2. */
    public static function kundenbereichListen(): array
    {
        return Startseitentexte::kundenbereich();
    }

    /** @return array<string,mixed>|null */
    public static function finden(string $schluessel): ?array
    {
        return self::alle()[$schluessel] ?? null;
    }
}
