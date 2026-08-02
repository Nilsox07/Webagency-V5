<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Die acht Leistungszeilen — Website-Lastenheft §5 Sektion 7 und §6 Sektion 3.
 *
 * **Eine Quelle für zwei Seiten.** §6 Sektion 3: „dieselben acht Zeilen wie Startseite, aber
 * je 3–4 Sätze statt einem." Die Startseite nimmt `satz`, `/leistungen` nimmt `ausfuehrlich`.
 * Stünden sie zweimal da, liefe eine der beiden Fassungen auseinander — und die Startseite
 * ist die, die niemand nachpflegt.
 *
 * `ziel` ist leer, wo es keine Leistungsseite gibt. §0.3b: keine toten Verweise.
 */
final class Leistungszeilen
{
    /**
     * @return list<array{titel:string,satz:string,tags:list<string>,ziel:?string,ausfuehrlich:list<string>}>
     */
    public static function alle(): array
    {
        return [
            [
                'titel' => 'Strategie und Seitenstruktur',
                'satz'  => 'Wir legen fest, welche Seiten Ihr Ziel wirklich brauchen — und welche nicht.',
                'tags'  => ['Sitemap', 'Nutzerführung', 'Suchintention'],
                'ziel'  => null,
                'ausfuehrlich' => [
                    'Vor der ersten Zeile Text steht die Seitenkarte.',
                    'Wir prüfen, welche Suchanfragen in Ihrem Fach gestellt werden, und geben '
                        . 'jeder genau eine Seite. Zwei Absichten auf einer Seite bedienen '
                        . 'beide schlecht.',
                    'Was keine Frage beantwortet, wird nicht gebaut. Eine Seite weniger kostet '
                        . 'Sie weniger und ist besser auffindbar.',
                ],
            ],
            [
                'titel' => 'Webdesign und Programmierung',
                'satz'  => 'Individuell aus unserem Designsystem programmiert, ohne WordPress und ohne Baukasten.',
                'tags'  => ['kein WordPress', 'responsive', 'schnell'],
                'ziel'  => '/leistung-webdesign',
                'ausfuehrlich' => [
                    'Ihre Website entsteht als eigener Programmcode.',
                    'Farben, Schriften und Formen kommen aus einem festen System. Deshalb '
                        . 'sieht jede Seite gleich aus, und keine muss neu erfunden werden.',
                    'Es gibt keine Erweiterungen, die Sie aktualisieren müssten, und keine '
                        . 'Vorlage, die tausend andere Betriebe auch haben.',
                ],
            ],
            [
                'titel' => 'Website-Texte',
                'satz'  => 'Wir schreiben die Texte aus Ihren Fakten und Stichpunkten — Sie liefern keinen fertigen Webtext.',
                'tags'  => ['aus Stichpunkten', 'Faktenprüfung'],
                'ziel'  => '/leistung-texte',
                'ausfuehrlich' => [
                    'Sie schreiben nichts.',
                    'Wir fragen ab, was Ihr Betrieb macht, für wen und in welchem Gebiet. '
                        . 'Daraus entstehen die Texte jeder Seite.',
                    'Jede Fachaussage geht vor der Veröffentlichung zurück an Sie. Was Sie '
                        . 'nicht bestätigen, steht nicht auf der Seite.',
                ],
            ],
            [
                'titel' => 'SEO-Grundlage',
                'satz'  => 'Jede Seite startet mit klarem Thema, sauberen Metadaten und strukturierten Daten.',
                'tags'  => ['Titles', 'Schema', 'interne Links'],
                'ziel'  => '/leistung-seo-lokal',
                'ausfuehrlich' => [
                    'Die Grundlage ist ab dem Livegang da.',
                    'Dazu gehören Seitenthemen, Titel, Beschreibungen, interne Verlinkung, '
                        . 'strukturierte Daten und eine Ladezeit, die im Labor gemessen wurde.',
                    'Was danach kommt, folgt echten Suchdaten aus Ihrer Search Console — und '
                        . 'ist ein eigenes Angebot.',
                ],
            ],
            [
                'titel' => 'Lokale Sichtbarkeit',
                'satz'  => 'Echte Unternehmensdaten statt dünner Ortsseiten mit ausgetauschtem Stadtnamen.',
                'tags'  => ['Local SEO', 'konsistente Daten'],
                'ziel'  => '/leistung-seo-lokal',
                'ausfuehrlich' => [
                    'Ihr Name, Ihre Anschrift und Ihre Telefonnummer stehen überall gleich.',
                    'Das klingt nach einer Kleinigkeit und ist der häufigste Grund, warum ein '
                        . 'Betrieb im Kartenbereich schlechter steht als der Nachbar.',
                    'Ortsseiten, bei denen nur der Stadtname getauscht ist, bauen wir nicht.',
                ],
            ],
            [
                'titel' => 'Domain und Launch',
                'satz'  => 'Wir prüfen, verbinden und schalten live — Ihre bestehende E-Mail bleibt dabei erreichbar.',
                'tags'  => ['DNS', 'E-Mail-Schutz', 'Weiterleitungen'],
                'ziel'  => null,
                'ausfuehrlich' => [
                    'Die Domain gehört Ihnen, auch wenn wir sie technisch verwalten.',
                    'Vor jeder Umstellung sichern wir Ihre bestehenden Einträge. Ihre '
                        . 'E-Mail-Adressen bleiben beim Umschalten erreichbar.',
                    'Alte Adressen leiten wir weiter, damit vorhandene Verweise nicht ins Leere '
                        . 'laufen.',
                ],
            ],
            [
                'titel' => 'Kundenbereich und Freigaben',
                'satz'  => 'Angebot, Fragen, Vorschau und Rückmeldungen laufen an einem Ort statt in E-Mail-Ketten.',
                'tags'  => ['Fragen', 'Rückmeldung', 'Pflege'],
                'ziel'  => '/leistung-portal',
                'ausfuehrlich' => [
                    'Sie melden sich mit einem Link an, den wir Ihnen schicken. Ein Passwort '
                        . 'gibt es nicht.',
                    'Dort liegen Angebot, Rechnungen, die Fragen zu Ihrem Betrieb, Ihre '
                        . 'hochgeladenen Dateien, die Vorschau und der Domainstand.',
                    'Ihre Rückmeldungen zur Vorschau sammeln Sie und schicken sie in einem '
                        . 'Durchgang. Wir arbeiten sie gebündelt ein.',
                ],
            ],
            [
                'titel' => 'Rundum-Schutz',
                'satz'  => 'Wir betreiben die Website danach: Hosting, Sicherheit, Sicherungen, Überwachung.',
                'tags'  => ['Betrieb', 'Sicherungen', 'Überwachung'],
                'ziel'  => '/leistung-wartung',
                'ausfuehrlich' => [
                    'Nach dem Livegang übernehmen wir den Betrieb.',
                    'Enthalten sind Hosting, SSL, tägliche Sicherungen, Überwachung, technische '
                        . 'Aktualisierungen und Ihr Zugang zum Kundenbereich.',
                    'Es gibt kein Konto mit Änderungsminuten. Was am Text zu ändern ist, '
                        . 'schreiben Sie uns.',
                ],
            ],
        ];
    }

    /** §6 Sektion 4 — was der Kunde nicht entscheiden muss. */
    public const NICHT_ENTSCHEIDEN = [
        'System und Technik',
        'Seitenzahl',
        'Designstil',
        'SEO-Stufe',
        'Hosting',
        'Registrar',
        'Wartungsminuten',
    ];

    /** §7 Sektion 4 — was jedes Projekt enthält. */
    public const IN_JEDEM_PROJEKT = [
        'Strategie',
        'Texte',
        'Design',
        'Programmierung',
        'SEO-Grundlage',
        'Kundenbereich',
        'Domainverbindung',
        'Launch',
    ];
}
