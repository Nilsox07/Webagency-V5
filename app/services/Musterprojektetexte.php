<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Der Wortlaut von `/musterprojekte` — `17_SEITEN_SARTU.md` §4a.
 *
 * **Umfang 700–1.000 Wörter.** Die Seite ist die Langfassung von Sektion 8, keine
 * Wiederholung: dort drei Karten nebeneinander, hier drei Fälle nacheinander mit Begründung.
 *
 * ## Warum der dritte Block der wichtigste ist
 *
 * §4a Block 3 heisst „Warum es Muster sind und keine Kunden" und nennt ihn „den ehrlichen
 * Absatz, den die Startseite aus Platzgründen nicht führt". Die Karte auf der Startseite sagt
 * über die Fahne, dass es kein Kundenauftrag ist. Erst hier steht, **warum** — und das ist
 * die einzige Stelle, an der SARTU die Lücke selbst erklärt, statt sie zu umschreiben.
 *
 * ## Was hier schärfer gesperrt ist als auf der Startseite
 *
 * §4a: „Dieselben Sperren wie in §8, sie gelten hier schärfer, weil die Seite mehr Platz zum
 * Ausschmücken hätte." Keine erfundenen Firmennamen · keine erfundenen Zahlen, Ergebnisse
 * oder Steigerungen · kein nachgebauter Bildschirm.
 *
 * > „**Der Fall darf nicht zur Fallstudie werden.** Eine Fallstudie behauptet ein *Ergebnis*.
 * > Ein Musterprojekt beschreibt eine *Entscheidung*: dieser Ausgangslage entspricht diese
 * > Struktur."
 *
 * Deshalb steht in keinem der drei Abschnitte ein Wort über mehr Anfragen oder bessere
 * Sichtbarkeit — auch nicht im Konjunktiv.
 */
final class Musterprojektetexte
{
    public const TITEL = 'Musterprojekte: drei Betriebe, drei Seitenstrukturen | SARTU';

    public const BESCHREIBUNG = 'Drei ausgearbeitete Muster — Malerbetrieb, '
        . 'Physiotherapiepraxis, Arbeitsrechtskanzlei. Ausgangslage, empfohlener Umfang und '
        . 'Seitenstruktur je Fall. Keine Kundenaufträge.';

    /** §4a Block 1 — benennt, dass es Muster sind. **Kein** `Referenzen`, `Arbeiten`, `Projekte`. */
    public const H1 = 'Drei Musterprojekte, ausgearbeitet statt behauptet.';

    /** §4a Block 3 — der ehrliche Absatz. */
    public const WARUM_H2 = 'Warum hier keine Kunden stehen.';

    public const WARUM = [
        'SARTU nimmt seit 2026 Aufträge an. Wer heute Referenzen zeigen wollte, müsste sie '
            . 'sich ausdenken — und genau das ist der Grund, aus dem Referenzen auf vielen '
            . 'Agenturseiten nichts wert sind.',
        'Ein Musterprojekt behauptet kein Ergebnis. Es zeigt eine Entscheidung: Bei dieser '
            . 'Ausgangslage empfehlen wir diesen Umfang, und die Seite sieht dann so aus. '
            . 'Das lässt sich nachrechnen, ohne dass jemand einen Erfolg bestätigen muss.',
        'Sobald ein Kunde uns schriftlich freigibt, steht sein Projekt hier — mit Namen und '
            . 'ohne die Fahne darüber.',
    ];

    /** Die Beschriftung des Blocks, den nur diese Seite trägt (§4a). */
    public const WARUM_NICHT_KLEINER = 'Warum diese Stufe und nicht die nächstkleinere';

    /** Die Beschriftung des Gestaltungsblocks — §4a, „die drei müssen sichtbar verschieden aussehen". */
    public const GESTALTUNG = 'Wie die Seite aussehen wird';

    /** §4a Block 7 — Übergang zum Bedarfsscheck. */
    public const ABSCHLUSS_H2 = 'Welcher der drei Fälle kommt Ihrem Betrieb am nächsten?';

    public const ABSCHLUSS = 'Der Bedarfsscheck stellt dieselben Fragen, die wir uns bei '
        . 'diesen drei Fällen gestellt haben. Sie sehen danach, welcher Umfang zu Ihrem '
        . 'Betrieb passt — und die Begründung dazu.';

    /**
     * Die Einleitung mit den beiden gebundenen Zahlen.
     *
     * Sie steht wie auf der Startseite, und aus demselben Grund an derselben Quelle:
     * Gründungsjahr als Konstante, Anzahl gezählt.
     */
    public static function einleitung(): string
    {
        return sprintf(
            'SARTU ist %d gestartet. Diese %s Beispiele sind ausgearbeitete Muster, keine '
            . 'Kundenaufträge — Ausgangslage, empfohlener Umfang und Seitenstruktur je Fall.',
            Musterprojekte::GRUENDUNG,
            Musterprojekte::anzahlWort()
        );
    }
}
