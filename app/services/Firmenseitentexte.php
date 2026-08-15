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
     * Der eine Satz der randlos dunklen Fläche — ergänzt am 14.08.2026.
     *
     * Die Antwortfrist steht im Vorspann und ist eine Zahl; sie gehört nicht hierher. Hier
     * steht, was die Frist wert ist: dass überhaupt jemand antwortet, und zwar ein Mensch.
     */
    public const KONTAKT_ZUSAGE = 'Auf jede Rückfrage antwortet ein Mensch.';

    /**
     * §11, Pflichtabschnitt „Wo wir arbeiten".
     *
     * **Die Reihenfolge ist gebunden:** §11 schreibt vor „erst bundesweit, dann der
     * Umkreis. Umgekehrt liest ein Betrieb aus Kassel ‚Dresden' und geht." `WO_TEXT` trägt
     * den ersten Teil, `WO_EINZUGSGEBIET` darunter den zweiten.
     *
     * **Der zweite Teil hat bis zum 15.08.2026 gefehlt**, mit einer Begründung, die seit dem
     * 01.08.2026 nicht mehr galt: §0 sperre Ortsnamen im Fliesstext. Der Betreiber hat das
     * an jenem Tag entschieden — „alle Orte ins Profil und in den Fliesstext", und für
     * `/kontakt` ausdrücklich „ein Absatz, der die Orte namentlich nennt". Gesperrt bleiben
     * Google-Unternehmensprofil, `LocalBusiness` und die NAP-Aussage; eine Anschrift steht
     * hier nach wie vor nicht.
     */
    public const WO_H2 = 'Wo wir arbeiten';

    public const WO_TEXT = 'Bundesweit. Weil es keine Abstimmungstermine gibt, spielt die '
        . 'Entfernung keine Rolle — der Ablauf ist überall derselbe.';

    public const WO_ZUSATZ = 'Auf Wunsch sprechen wir per Video oder kommen zu Ihnen. Nötig ist '
        . 'weder das eine noch das andere.';

    /**
     * Das Einzugsgebiet mit Ortsnamen — `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1, Ebene 1.
     *
     * ## Warum er bis zum 15.08.2026 fehlte
     *
     * §0 sperrte jeden Ortsnamen im sichtbaren Text, solange `[GESCHAEFTSADRESSE_STATUS]`
     * offen ist. **Am 01.08.2026 hat der Betreiber das für den Fliesstext aufgehoben:**
     * „alle Orte ins Profil und in den Fliesstext", und ausdrücklich „`/kontakt` und die
     * Dresden-Seite: ein Absatz, der die Orte namentlich nennt — und den Satz, dass
     * bundesweit gearbeitet wird". Gesperrt blieben nur drei Dinge, und sie sind alle drei
     * benannt: Google-Unternehmensprofil, `LocalBusiness` und die NAP-Aussage.
     *
     * Der Code hat die alte Sperre danach zwei Wochen weiter begründet.
     *
     * ## Die Reihenfolge ist nicht beliebig
     *
     * §1: „Sitz im Raum Dresden nennen, **Arbeitsgebiet bundesweit**. Nicht ‚nur im Raum
     * Dresden'." `WO_TEXT` steht deshalb davor und sagt zuerst „Bundesweit"; dieser Absatz
     * ergänzt den Sitz, er ersetzt ihn nicht.
     *
     * **Keine Anschrift.** Sitz heisst hier Region, nicht Strasse — die Anschrift ist
     * weiterhin offen, und eine NAP-Aussage bleibt gesperrt.
     */
    public const WO_EINZUGSGEBIET = 'Unser Sitz liegt im Raum Dresden. Häufig arbeiten wir '
        . 'für Betriebe in Meißen, Radebeul, Coswig, Radeberg, Freital, Heidenau, Pirna, '
        . 'Dippoldiswalde, Bischofswerda, Bautzen und Sebnitz — nötig ist die Nähe nicht.';
}
