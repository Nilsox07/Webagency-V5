<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * `/ueber-uns` und `/kontakt` — Website-Lastenheft §11.
 *
 * ## `/ueber-uns` steht ohne Foto und ohne Namen
 *
 * §11 verlangt „Hero mit **echtem Foto** (kein Fake-Teamfoto)".
 * `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5 führt Foto und `[GRUENDER_NAME]` als **offen** und
 * verbietet zugleich den Platzhalter, der wie ein Foto wirkt.
 *
 * §5.1 derselben Datei regelt den Fall abschließend: „Solange nichts entschieden ist, gilt:
 * Der Name erscheint **nur** im Impressum, nirgends sonst — auch nicht in
 * Bildbeschreibungen."
 *
 * **Die Seite entsteht deshalb ohne den Hero-Block**, nicht mit einem leeren Rahmen. Alles
 * andere, was §11 nennt, steht da: die vier Gründe, die vier Abgrenzungen, die Arbeitsweise
 * und der Verantwortungssatz. Die Startsperre §14a Bedingung 4a bleibt zusätzlich aktiv.
 *
 * ## „gründergeführt", nie „unser Team"
 *
 * §11, Ehrlichkeitsregel. Solange eine Einzelperson arbeitet, ist jedes „wir" im Sinne von
 * Mannschaft eine Übertreibung. Das „wir" für den Betrieb bleibt zulässig — es ist die
 * Firma, nicht die Belegschaft.
 */
final class Firmenseitentexte
{
    // ============================================================ /ueber-uns

    public const UEBER_TITEL = 'Über SARTU — Festpreis, Kundenbereich, klare Grenzen | SARTU';

    public const UEBER_BESCHREIBUNG = 'SARTU baut Firmenwebsites zum Festpreis: klarer Ablauf, '
        . 'geführter Kundenbereich, keine WordPress-Pflege und KI-gestützte Produktion mit '
        . 'menschlicher Prüfung.';

    public const UEBER_H1 = 'Webdesign mit klaren Grenzen, festen Preisen und Verantwortung.';

    public const UEBER_LEAD = 'SARTU ist gründergeführt und 2026 gestartet. Wir bauen '
        . 'Firmenwebsites für Betriebe mit 3 bis 30 Beschäftigten — zum Festpreis, ohne '
        . 'Abstimmungstermine und mit Betrieb danach.';

    /** @return list<array{titel:string,text:string}> §11, vier Punkte. */
    public static function warumAnders(): array
    {
        return [
            ['titel' => 'Festpreis statt Stundenfalle',
             'text'  => 'Der Preis steht vor Ihrer Entscheidung. Was nicht hineinpasst, bekommt '
                . 'ein eigenes Angebot — vorher, nicht auf der Schlussrechnung.'],
            ['titel' => 'Kundenbereich statt E-Mail-Chaos',
             'text'  => 'Angebot, Fragen, Vorschau, Rückmeldungen und Rechnungen liegen an '
                . 'einem Ort. Sie suchen keinen Anhang in einer Kette von vierzig Mails.'],
            ['titel' => 'Fakten statt Geschmacksdiskussionen',
             'text'  => 'Sie liefern, was Ihr Betrieb macht und für wen. Struktur, Gestaltung '
                . 'und Technik entscheiden wir — und haften für das Ergebnis.'],
            ['titel' => 'KI als Werkzeug, nicht als Ersatz',
             'text'  => 'KI hilft beim Entwurf. Geprüft und freigegeben wird von einem Menschen, '
                . 'und jede Fachaussage geht vorher an Sie zurück.'],
        ];
    }

    /** §11 und §5 Sektion 6 — vier Abgrenzungen, wörtlich. */
    public const NICHT = [
        'kein Baukasten',
        'kein WordPress-Hoster',
        'keine Billig-Seitenschleuder',
        'kein Anbieter für Privat- und Hobbyseiten',
    ];

    /** §11 — die Arbeitsweise in fünf Schritten. */
    public const ARBEITSWEISE = [
        'Sie beschreiben Ihren Betrieb im Bedarfsscheck.',
        'Wir prüfen nach und schicken ein Angebot mit Festpreis.',
        'Sie beantworten die Fragen in Ihrem Bereich, wann es Ihnen passt.',
        'Wir bauen die Website und legen sie Ihnen als Vorschau vor.',
        'Nach Ihrer Abnahme schalten wir live und übernehmen den Betrieb.',
    ];

    /** §11, Verantwortung — dieser Satz gehört hierher und **nicht** auf die Startseite (§5). */
    public const VERANTWORTUNG = 'Veröffentlicht wird nur, was wir geprüft und freigegeben haben.';

    // ============================================================ /kontakt

    public const KONTAKT_TITEL = 'Kontakt — Rückfrage oder Bedarf prüfen lassen | SARTU';

    public const KONTAKT_BESCHREIBUNG = 'Stellen Sie SARTU eine Rückfrage oder starten Sie den '
        . 'kurzen Bedarfsscheck für Ihre Firmenwebsite. Antwort in der Regel innerhalb eines '
        . 'Werktags.';

    public const KONTAKT_H1 = 'Kontakt zu SARTU.';

    /**
     * §11, Pflichtabschnitt „Wo wir arbeiten".
     *
     * **Die Ortsliste fehlt, und die Reihenfolge ist genau deshalb erhalten geblieben.** §11
     * schreibt vor: „erst bundesweit, dann der Umkreis. Umgekehrt liest ein Betrieb aus
     * Kassel ‚Dresden' und geht." Der erste Teil steht hier vollständig.
     *
     * Der zweite Teil — Sitz und Umkreisliste — ist gesperrt: §0 verbietet Ortsnamen im
     * Fließtext, solange `[GESCHAEFTSADRESSE_STATUS]` auf `offen` steht, und
     * `SARTU_ENTSCHEIDUNGEN_OFFEN.md` steht in der Rangfolge über dem Website-Lastenheft.
     * Die Sperre nennt als Zweck ausdrücklich den Kartenbereich — und der ist ohne
     * entschiedene Adresse ohnehin nicht zu haben.
     */
    public const WO_H2 = 'Wo wir arbeiten';

    public const WO_TEXT = 'Bundesweit. Weil es keine Abstimmungstermine gibt, spielt die '
        . 'Entfernung keine Rolle — der Ablauf ist überall derselbe.';

    public const WO_ZUSATZ = 'Auf Wunsch sprechen wir per Video oder kommen zu Ihnen. Nötig ist '
        . 'weder das eine noch das andere.';
}
