<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Was neben der Zahl steht — Website-Lastenheft §5 Sektion 4.
 *
 * **Die Zahlen stehen nicht hier.** Sie kommen aus `Preise::tabelle()`, und die kommt aus der
 * Preistabelle im Masterkonzept. Hier stehen nur Zielgruppe, Merkmale und Knopfbeschriftung
 * — der Text um die Zahl herum.
 *
 * Die Trennung ist keine Ordnungsliebe: Wer beides in eine Datei schreibt, pflegt beim
 * nächsten Preiswechsel den Text mit und übersieht eine der vier Zeilen.
 *
 * **`Platzhirsch` wird erklärt** (UX-Audit, Vorgabe 4), sonst klingt der Name großspurig.
 * Und die Erklärung enthält **keine** Ranking-Zusage — „als erste Adresse auftreten wollen"
 * beschreibt die Absicht des Betriebs, nicht ein Ergebnis bei Google.
 */
final class Preisstufen
{
    /**
     * Drei Textstellen je Stufe, und jede hat eine andere Aufgabe.
     *
     * | | |
     * |---|---|
     * | `kicker` | **für wen**, in höchstens fünf Wörtern. Steht über dem Namen, damit der Leser die Zeile überspringen kann, die ihn nicht meint |
     * | `satz`   | **was dabei herauskommt**, ein Satz. Nicht der Umfang — der steht als Zahl in der Liste |
     * | `merkmale` | **vier Punkte**, gleich viele je Stufe. Die ersten beiden tragen die gebundenen Zahlen aus `02_PREISE_UND_ZAHLUNG.md` |
     *
     * Bis zum 13.08.2026 stand hier ein einziges Feld `fuer_wen` mit zwei bis drei Zeilen
     * Fließtext. In der Karte lief es über vier Zeilen und schob die Zahlen der Nachbarkarten
     * auseinander — vergleichen ließ sich dann nichts mehr. Zielgruppe und Ergebnis sind
     * zwei Aussagen; sie stehen jetzt getrennt und jede so lang, wie sie sein muss.
     *
     * @return array<string,array{kicker:string,satz:string,merkmale:list<string>,knopf:string,empfehlung:bool}>
     */
    public static function alle(): array
    {
        return [
            'start' => [
                'kicker'   => 'Für ein Angebot',
                'satz'     => 'Eine Adresse im Netz, die Ihre Leistung vollständig erklärt.',
                'merkmale' => [
                    '1 Seite, rund 1.200 Wörter',
                    '1 Korrekturrunde',
                    'Kontakt- und Anfahrtsweg',
                    'Betrieb und Sicherungen enthalten',
                ],
                'knopf'      => 'Einschätzen lassen',
                'empfehlung' => false,
            ],
            'wachstum' => [
                'kicker'   => 'Für mehrere Leistungen',
                'satz'     => 'Jede Leistung bekommt ihre eigene Seite.',
                'merkmale' => [
                    'bis zu 8 Seiten, rund 3.500 Wörter',
                    '2 Korrekturrunden',
                    'eigene Seite je Leistung',
                    'SEO-Grundlage je Seite',
                ],
                'knopf'      => 'Einschätzen lassen',
                'empfehlung' => false,
            ],
            'platzhirsch' => [
                'kicker'   => 'Für die erste Adresse am Ort',
                'satz'     => 'Sichtbar für Kunden — und für Bewerber.',
                'merkmale' => [
                    'bis zu 16 Seiten, rund 6.500 Wörter',
                    '2 Korrekturrunden',
                    'eigene Seite je Leistung und Ort',
                    'Karriere- und Bewerbungsbereich',
                ],
                'knopf'      => Auftragslage::KNOPF,
                'empfehlung' => true,
            ],
            'sonderprojekt' => [
                'kicker'   => 'Kein Paket, sondern eine Vorprüfung',
                'satz'     => 'Für Vorhaben, die über eine Firmenwebsite hinausgehen.',
                'merkmale' => [
                    'Shop, Kundenlogin oder Buchung',
                    'Anbindung an vorhandene Software',
                    'Festpreis erst nach technischer Prüfung',
                    'Absage, wenn wir es nicht verantworten können',
                ],
                'knopf'      => 'Sonderprojekt besprechen',
                'empfehlung' => false,
            ],
        ];
    }
}
