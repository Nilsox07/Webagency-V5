<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Die fünf Themen des Bedarfsschecks — Website-Lastenheft §9.2, feldgenau.
 *
 * Labels, Hilfetexte und Fehlermeldungen stehen dort **final**. Sie sind hier wörtlich
 * übernommen, nicht formuliert. Wer sie ändert, ändert eine abgestimmte Vorgabe.
 *
 * **Eine Abweichung, entschieden am 02.08.2026:** Thema 4 hatte eine Auswahl
 * „Mehrere Sprachen oder getrennte Marken". Das Regelwerk (Masterkonzept §8) stuft Sprachen
 * orange und Marken rot ein — eine Auswahl für zwei Ampelfarben, und dazwischen liegen
 * 8.600 € Einmalpreis. Der Betreiber hat entschieden, sie aufzuteilen.
 *
 * **Warum eine Datenstruktur und keine fünf Ansichtsdateien:** Der Bedarfsscheck muss ohne
 * JavaScript vollständig durchlaufbar sein (§9.5a) — als echte Seiten `/briefing/1` bis
 * `/briefing/5`, je Schritt ein `POST`. Fünf fast gleiche Ansichten wären fünf Stellen, an
 * denen eine Fehlermeldung fehlen kann.
 */
final class Bedarfsscheck
{
    public const SCHRITTE = 5;

    /** Ablauf der kurzlebigen Sitzung mit dem Zwischenstand (§9.5a). */
    public const ZWISCHENSTAND_STUNDEN = 24;

    /**
     * Die freiwillige Frage aus §9.5b: „Wie sind Sie auf uns aufmerksam geworden?"
     *
     * **Kein Pflichtfeld** — das Lastenheft begründet es selbst: „eine unbeantwortete Frage
     * ist besser als eine erzwungene Falschangabe." Sie steht im letzten Schritt, also auf
     * der Kontaktseite, und wird deshalb nicht von `schrittPruefen()` erfasst.
     */
    public const HERKUNFTSANGABEN = [
        'suchmaschine' => 'Suchmaschine',
        'empfehlung'   => 'Empfehlung',
        'angesprochen' => 'Direkt angesprochen worden',
        'anzeige'      => 'Anzeige',
        'sonstiges'    => 'Sonstiges',
    ];

    /**
     * @return array{titel:string,felder:list<array<string,mixed>>}
     */
    public static function thema(int $nummer): array
    {
        return self::themen()[$nummer] ?? throw new \InvalidArgumentException('Dieses Thema gibt es nicht.');
    }

    /** @return array<int,array{titel:string,felder:list<array<string,mixed>>}> */
    public static function themen(): array
    {
        return [
            1 => [
                'titel'  => 'Ihr Unternehmen',
                'felder' => [
                    [
                        'name'    => 'angebot',
                        'label'   => 'Was bietet Ihr Unternehmen an?',
                        'art'     => 'textarea',
                        'pflicht' => true,
                        'hilfe'   => 'Zum Beispiel: Wir sanieren Bäder und Heizungen für Privatkunden im Umkreis von 40 km.',
                        'fehler'  => 'Bitte beschreiben Sie Ihr Angebot in ein bis drei Sätzen.',
                    ],
                    [
                        'name'    => 'einsatzort',
                        'label'   => 'Wo arbeiten Sie hauptsächlich?',
                        'art'     => 'text',
                        'pflicht' => true,
                        'hilfe'   => 'Ort oder Postleitzahl genügt.',
                        'fehler'  => 'Bitte geben Sie Ort oder Postleitzahl an.',
                    ],
                    [
                        'name'    => 'einzugsgebiet',
                        'label'   => 'Größeres Einzugsgebiet?',
                        'art'     => 'text',
                        'pflicht' => false,
                        'hilfe'   => 'Optional, z. B. Umkreis oder Region.',
                    ],
                    [
                        'name'      => 'bestehende_website',
                        'label'     => 'Gibt es bereits eine Website?',
                        'art'       => 'radio',
                        'pflicht'   => true,
                        'optionen'  => ['ja' => 'Ja', 'nein' => 'Nein', 'unsicher' => 'Bin unsicher'],
                        'fehler'    => 'Bitte wählen Sie eine Antwort.',
                    ],
                    [
                        'name'         => 'website_adresse',
                        'label'        => 'Adresse der bestehenden Website',
                        'art'          => 'text',
                        'pflicht'      => false,
                        'pflicht_wenn' => ['bestehende_website' => 'ja'],
                        'hilfe'        => 'Auch eine alte oder unfertige Seite hilft uns.',
                        'fehler'       => 'Bitte geben Sie eine gültige Internetadresse an, z. B. beispiel.de',
                    ],
                ],
            ],
            2 => [
                'titel'  => 'Ihr Ziel',
                'felder' => [
                    [
                        'name'     => 'hauptziel',
                        'label'    => 'Was soll die neue Website vor allem erreichen?',
                        'art'      => 'radio',
                        'pflicht'  => true,
                        'hilfe'    => 'Wählen Sie das Ziel, das in den nächsten zwölf Monaten den größten Unterschied machen würde.',
                        'fehler'   => 'Bitte wählen Sie ein Hauptziel.',
                        'optionen' => [
                            'anfragen'   => 'Mehr passende Anfragen',
                            'gefunden'   => 'Besser gefunden werden',
                            'recruiting' => 'Neue Mitarbeitende gewinnen',
                            'vertrauen'  => 'Vertrauen und Professionalität stärken',
                            'termine'    => 'Termine oder Bewerbungen vereinfachen',
                            'anderes'    => 'Etwas anderes',
                        ],
                    ],
                    [
                        'name'     => 'zielgruppe',
                        'label'    => 'Wen möchten Sie vor allem erreichen?',
                        'art'      => 'radio',
                        'pflicht'  => true,
                        'fehler'   => 'Bitte wählen Sie eine Antwort.',
                        'optionen' => [
                            'privatkunden' => 'Privatkunden',
                            'unternehmen'  => 'Unternehmen',
                            'bewerber'     => 'Bewerberinnen und Bewerber',
                            'mehrere'      => 'Mehrere Gruppen',
                            'unklar'       => 'Noch unklar',
                        ],
                    ],
                ],
            ],
            3 => [
                'titel'  => 'Umfang',
                'felder' => [
                    [
                        'name'         => 'umfangssignale',
                        'label'        => 'Was trifft auf Ihr Unternehmen zu?',
                        'art'          => 'checkbox',
                        'pflicht'      => true,
                        'fehler'       => 'Bitte wählen Sie mindestens eine Antwort.',
                        'allein'       => Empfehlung::SIGNAL_NICHTS_DAVON,
                        'fehler_allein' => '„Nichts davon" lässt sich nicht mit anderen Angaben kombinieren. '
                            . 'Bitte wählen Sie das eine oder das andere.',
                        'optionen'     => [
                            Empfehlung::SIGNAL_HAUPTANGEBOT       => 'Wir haben ein klares Hauptangebot',
                            Empfehlung::SIGNAL_MEHRERE_LEISTUNGEN => 'Wir bieten mehrere eigenständige Leistungen an',
                            Empfehlung::SIGNAL_MEHRERE_REGIONEN   => 'Wir arbeiten in mehreren Regionen oder an mehreren Standorten',
                            Empfehlung::SIGNAL_RECRUITING         => 'Wir suchen regelmäßig Mitarbeitende',
                            Empfehlung::SIGNAL_PROJEKTE_AKTUELL   => 'Projekte, Referenzen oder Neuigkeiten sollen aktuell bleiben',
                            Empfehlung::SIGNAL_NICHTS_DAVON       => 'Nichts davon / bin unsicher',
                        ],
                        'hilfen' => [
                            Empfehlung::SIGNAL_MEHRERE_LEISTUNGEN => 'Gemeint sind Angebote, nach denen Kunden getrennt '
                                . 'suchen oder für die sie eine eigene Erklärung brauchen.',
                        ],
                    ],
                ],
            ],
            4 => [
                'titel'  => 'Besondere Anforderungen',
                'felder' => [
                    [
                        'name'         => 'sonderfunktionen',
                        'label'        => 'Muss die Website etwas Besonderes können?',
                        'art'          => 'checkbox',
                        'pflicht'      => true,
                        'fehler'       => 'Bitte wählen Sie mindestens eine Antwort.',
                        'allein'       => Empfehlung::GATE_NICHTS_DAVON,
                        'fehler_allein' => '„Nichts davon" lässt sich nicht mit einer Sonderfunktion kombinieren. '
                            . 'Bitte wählen Sie das eine oder das andere.',
                        'optionen'     => [
                            Empfehlung::GATE_FORMULAR         => 'Normale Anfrage oder Bewerbung über ein Formular',
                            Empfehlung::GATE_TERMINBUCHUNG    => 'Einfache Terminbuchung',
                            Empfehlung::GATE_SHOP             => 'Produkte verkaufen oder Zahlungen annehmen',
                            Empfehlung::GATE_LOGIN            => 'Kundenlogin oder geschützter Bereich',
                            Empfehlung::GATE_SCHNITTSTELLE    => 'Verbindung zu anderer Software',
                            Empfehlung::GATE_MEHRERE_SPRACHEN => 'Mehrere Sprachen',
                            Empfehlung::GATE_GETRENNTE_MARKEN => 'Getrennte Marken oder eigene Domains',
                            Empfehlung::GATE_BESONDERE_DATEN  => 'Besondere Daten oder ein formaler Nachweis',
                            Empfehlung::GATE_NICHTS_DAVON     => 'Nichts davon, eine normale Firmenwebsite',
                        ],
                        'hilfen' => [
                            Empfehlung::GATE_TERMINBUCHUNG    => 'Zum Beispiel ein Kalender, in dem Kunden selbst einen Termin wählen.',
                            Empfehlung::GATE_SHOP             => 'Zum Beispiel ein Warenkorb oder eine Bezahlung auf der Seite.',
                            Empfehlung::GATE_LOGIN            => 'Zum Beispiel ein Bereich, den nur angemeldete Kunden sehen.',
                            Empfehlung::GATE_SCHNITTSTELLE    => 'Zum Beispiel Warenwirtschaft, CRM oder eine eigene Schnittstelle.',
                            Empfehlung::GATE_MEHRERE_SPRACHEN => 'Zum Beispiel dieselbe Seite auf Deutsch und Englisch.',
                            Empfehlung::GATE_GETRENNTE_MARKEN => 'Zum Beispiel zwei Firmennamen mit je eigener Domain.',
                            Empfehlung::GATE_BESONDERE_DATEN  => 'Zum Beispiel Gesundheitsdaten oder ein vorgeschriebener Nachweis.',
                        ],
                    ],
                ],
            ],
            5 => [
                'titel'  => 'Domain und Termin',
                'felder' => [
                    [
                        'name'     => 'domainstatus',
                        'label'    => 'Wie ist Ihr Domainstatus?',
                        'art'      => 'radio',
                        'pflicht'  => true,
                        'fehler'   => 'Bitte wählen Sie eine Antwort.',
                        'optionen' => [
                            'vorhanden' => 'Domain vorhanden',
                            'neu'       => 'Neue Domain nötig',
                            'unsicher'  => 'Bin unsicher',
                        ],
                    ],
                    [
                        'name'     => 'fester_termin',
                        'label'    => 'Gibt es einen festen Termin, der eingehalten werden muss?',
                        'art'      => 'radio',
                        'pflicht'  => true,
                        'fehler'   => 'Bitte wählen Sie eine Antwort.',
                        'optionen' => [
                            'nein' => 'Nein, der normale Zeitrahmen passt',
                            'ja'   => 'Ja',
                        ],
                    ],
                    [
                        'name'         => 'termin_datum',
                        'label'        => 'Datum und Grund',
                        'art'          => 'text',
                        'pflicht'      => false,
                        'pflicht_wenn' => ['fester_termin' => 'ja'],
                        'hilfe'        => 'Ein Wunschdatum ist noch keine Zusage — wir bestätigen die Machbarkeit im Angebot.',
                        'fehler'       => 'Bitte nennen Sie Datum und Grund.',
                    ],
                    [
                        'name'    => 'nicht_uebersehen',
                        'label'   => 'Gibt es etwas, das auf keinen Fall übersehen werden darf?',
                        'art'     => 'textarea',
                        'pflicht' => false,
                        'hilfe'   => 'Zum Beispiel: Unsere bestehenden E-Mail-Adressen müssen weiterlaufen.',
                    ],
                ],
            ],
        ];
    }

    /**
     * Prüft einen Schritt serverseitig.
     *
     * @param array<string,mixed> $eingabe
     * @return array<string,string> Fehler je Feldname. Leer bedeutet: der Schritt trägt.
     */
    public static function schrittPruefen(int $nummer, array $eingabe): array
    {
        $fehler = [];

        foreach (self::thema($nummer)['felder'] as $feld) {
            $name = (string) $feld['name'];
            $wert = $eingabe[$name] ?? null;

            if (($feld['art'] ?? '') === 'checkbox') {
                $gewaehlt = is_array($wert) ? array_values(array_filter($wert, 'is_string')) : [];

                if (($feld['pflicht'] ?? false) && $gewaehlt === []) {
                    $fehler[$name] = (string) $feld['fehler'];
                    continue;
                }

                // „Nichts davon" ist nicht mit anderen Angaben kombinierbar (§9.2).
                $allein = $feld['allein'] ?? null;

                if (is_string($allein) && in_array($allein, $gewaehlt, true) && count($gewaehlt) > 1) {
                    $fehler[$name] = (string) $feld['fehler_allein'];
                }

                continue;
            }

            $text = is_string($wert) ? trim($wert) : '';
            $pflicht = (bool) ($feld['pflicht'] ?? false);

            // Bedingte Pflicht: das Feld erscheint nur bei einer bestimmten Antwort.
            foreach ((array) ($feld['pflicht_wenn'] ?? []) as $anderesFeld => $wertDavon) {
                $anderes = $eingabe[$anderesFeld] ?? null;
                $pflicht = $pflicht || (is_string($anderes) && $anderes === $wertDavon);
            }

            if ($pflicht && $text === '') {
                $fehler[$name] = (string) ($feld['fehler'] ?? 'Bitte füllen Sie dieses Feld aus.');
                continue;
            }

            if ($text !== '' && ($feld['optionen'] ?? null) !== null
                && !array_key_exists($text, (array) $feld['optionen'])) {
                $fehler[$name] = (string) ($feld['fehler'] ?? 'Bitte wählen Sie eine Antwort.');
            }
        }

        return $fehler;
    }
}
