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
     * @return array<string,array{fuer_wen:string,merkmale:list<string>,knopf:string,empfehlung:bool}>
     */
    public static function alle(): array
    {
        return [
            'start' => [
                'fuer_wen' => 'Für Betriebe mit einem Angebot und einem Einzugsgebiet: '
                    . 'Handwerk, Praxis, Ladengeschäft.',
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
                'fuer_wen' => 'Für Betriebe mit mehreren Leistungen oder mehreren Zielgruppen, '
                    . 'die einzeln erklärt werden müssen.',
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
                'fuer_wen' => 'Für Betriebe, die in ihrer Region als erste Adresse auftreten '
                    . 'wollen — sichtbar für Kunden und für Bewerber.',
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
                'fuer_wen' => 'Für Shop, Kundenlogin, komplexe Buchung, Schnittstellen zu '
                    . 'vorhandener Software oder mehrere Marken unter einem Dach.',
                'merkmale' => [
                    'Festpreis vor Ihrer Entscheidung',
                    'keine offene Stundenabrechnung',
                    'Absage, wenn wir es nicht verantworten können',
                ],
                'knopf'      => 'Sonderprojekt besprechen',
                'empfehlung' => false,
            ],
        ];
    }
}
