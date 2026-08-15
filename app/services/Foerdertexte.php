<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Der Wortlaut von `/foerderung` — `17_SEITEN_SARTU.md` §6, neun Blöcke.
 *
 * ## Warum die Seite mit einer Absage anfängt
 *
 * §6, Berichtigung vom 09.08.2026: „**Die Kernaussage der Seite hat sich umgedreht.** Eine
 * reine Firmenwebsite ist in der Regel **nicht** förderfähig … Block 4 führt deshalb mit
 * dieser Antwort, nicht mit einer Verheissung."
 *
 * Wer „Förderung Website" sucht, ist kaufnah — er will wissen, ob es Geld gibt, **bevor** er
 * bestellt. Eine Seite, die ihn erst auf Seite drei enttäuscht, hat ihn zweimal verloren.
 *
 * ## Die vier Grenzen aus §6
 *
 * **Keine fremden Förderbeträge** · **keine Zusage, dass ein Antrag bewilligt wird** ·
 * **keine Rechtsberatung** · **keine Angstmache**. Sie sind der Grund, warum hier keine
 * Summe steht, obwohl `FOERDERUNG_KONZEPT.md` alle sechzehn kennt.
 *
 * ## Warum der Fliesstext kein einzelnes Land nennt
 *
 * **Nicht wegen einer Sperre** — die frühere Begründung an dieser Stelle berief sich auf §0
 * und war schon am 14.08.2026 überholt; §1 gibt Ortsnamen im Fliesstext seit dem 01.08.2026
 * frei. Der Grund ist ein anderer: Ein einzeln herausgegriffenes Land liest sich als Aussage
 * über das eigene Arbeitsgebiet, und `FOERDERUNG_KONZEPT.md` §7 hat am 09.08.2026
 * **bundesweit** entschieden. In der Übersicht steht jedes Land — sechzehn von sechzehn sind
 * keine Auswahl.
 */
final class Foerdertexte
{
    public const PFAD = '/foerderung';

    public const TITEL = 'Förderung für eine Firmenwebsite: was gilt | SARTU';

    public const BESCHREIBUNG = 'Eine reine Firmenwebsite ist in der Regel nicht förderfähig. '
        . 'Was die sechzehn Landesprogramme verlangen, warum der Antrag vor dem Auftrag stehen '
        . 'muss und was Förderung an Zeit kostet.';

    public const H1 = 'Gibt es Förderung für eine Firmenwebsite?';

    /**
     * Der Förderhinweis unter der Empfehlung — `17_SEITEN_SARTU.md` §2.3.
     *
     * **Zwei Sätze, das ist die gebundene Obergrenze.** Die vier Grenzen derselben Stelle:
     * keine Summe, keine Förderquote, kein Bundesland, keine Zusage. Und ausdrücklich
     * **nicht** nahelegen, Förderung sei der Regelfall — nach der Recherche vom 09.08.2026
     * ist sie es nicht.
     *
     * Der zweite Satz ist der eigentliche Zweck: Wer erst nach der Beauftragung von der
     * Förderung erfährt, verliert den Anspruch.
     */
    public const HINWEIS_ERGEBNIS = 'Eine Förderung greift nur in bestimmten Fällen. Wenn '
        . 'Sie eine planen, muss der Antrag vor der Beauftragung gestellt sein.';

    public const VORZEILE = 'Förderung';

    /**
     * Block 2 — der Antwortabsatz. §6 bindet **40 bis 60 Wörter** und verlangt darin die
     * Reihenfolgeregel. Gezählt am 14.08.2026: 54 Wörter.
     */
    public const KURZ = 'Meistens nicht. Die Landesprogramme fördern Digitalisierung, die '
        . 'betriebliche Abläufe verändert — eine Website, die den Betrieb darstellt, fällt in '
        . 'der Regel heraus. Wo doch etwas geht, entscheidet die Reihenfolge alles: erst die '
        . 'Bewilligung, dann Ihr Auftrag. Wer zuerst beauftragt, verliert den Anspruch.';

    // ------------------------------------------------------------ 3 Antrag vor Auftrag

    public const REIHENFOLGE_H2 = 'Antrag vor Auftrag.';

    /** @return list<string> */
    public const REIHENFOLGE = [
        'Fast alle Programme verlangen, dass das Vorhaben bei Antragstellung noch nicht '
            . 'begonnen hat. Als Beginn gilt in der Regel die Beauftragung — nicht der erste '
            . 'Arbeitstag und nicht die erste Rechnung.',
        'Mehrere Länder schreiben es wörtlich in die Bewilligung: Vor dem Eingang der '
            . 'Bestätigungsmail darf das Vorhaben nicht beginnen. Bayern und Brandenburg '
            . 'verlangen den Antrag ebenfalls vor Beginn.',
        'Für Sie heisst das: erst Angebot einholen, dann Antrag stellen, dann Bewilligung '
            . 'abwarten, dann beauftragen. Ein Angebot bekommen Sie von uns unabhängig davon — '
            . 'es ist die Unterlage, die der Antrag braucht.',
    ];

    // ------------------------------------------------------------ 4 Warum meistens nicht

    public const WARUM_H2 = 'Warum eine Firmenwebsite meistens nicht gefördert wird.';

    /** @return list<string> */
    public const WARUM = [
        'Der Grund ist über alle sechzehn Länder derselbe: Gefördert wird, was Abläufe '
            . 'verändert. Nicht, was ein Unternehmen darstellt.',
        'Bayern schreibt den Ausschluss aus: Standard-Webseiten ohne tiefe funktionelle '
            . 'Einbindung in die betrieblichen Abläufe sind nicht förderfähig, ebenso '
            . 'Suchmaschinenoptimierung sowie grafische und redaktionelle Leistungen. Andere '
            . 'Länder schliessen Websites ohne Geschäftsintegration aus. Der Rest verlangt '
            . 'dasselbe in anderen Worten — Technologiebezug, Innovationsgehalt, '
            . 'Prozessveränderung.',
    ];

    public const DOCH_H3 = 'Die Fälle, in denen doch etwas geht.';

    /** @return list<string> */
    public const DOCH = [
        'Wenn die Website Teil eines grösseren Vorhabens ist, das Abläufe verändert — etwa '
            . 'eine Anbindung an Warenwirtschaft, Terminvergabe oder Auftragsabwicklung.',
        'Wenn das Programm Beratung fördert statt Umsetzung. Dann ist nicht die Website der '
            . 'Gegenstand, sondern die Analyse davor.',
        'Wenn das Land ein Gutscheinverfahren führt, in dem externe Fachleute beauftragt '
            . 'werden — dort zählt die Leistung, nicht ihr Ergebnis.',
    ];

    public const DOCH_GRENZE = 'Ob Ihr Vorhaben darunterfällt, entscheidet die Förderbank im '
        . 'Einzelfall. Wir sagen es Ihnen nicht zu, und niemand sonst kann es Ihnen zusagen.';

    // ------------------------------------------------------------ 5 Die Übersicht

    public const LAENDER_H2 = 'Alle sechzehn Länder.';

    public const LAENDER_EINLEITUNG = 'Kein Land ist weggelassen. Ob Ihr Betrieb '
        . 'antragsberechtigt ist, prüfen Sie an der Bedingung — sie ist der Inhalt dieser '
        . 'Übersicht.';

    /**
     * §6, Grenze 1: keine Summen. Der Satz sagt, warum — er ist die Begründung, die die
     * Übersicht glaubwürdig macht.
     */
    public const LAENDER_OHNE_BETRAEGE = 'Beträge und Fördersätze stehen hier nicht. Sie '
        . 'ändern sich, und eine veraltete Zahl auf einer Website ist schlimmer als keine. '
        . 'Nach der aktuellen Höhe fragen Sie die zuständige Stelle.';

    // ------------------------------------------------------------ 6 Was Förderung kostet

    public const KOSTET_H2 = 'Was Förderung Sie kostet.';

    public const KOSTET_EINLEITUNG = 'Förderung ist nicht umsonst. Wer schnell zu einer Website '
        . 'kommen will, für den ist sie ein Nachteil.';

    /** @return array<string,string> */
    public const KOSTET = [
        'Zeit'    => 'Antrag, Bewilligung, dann erst Start. Das können Monate sein, in denen '
            . 'nichts entsteht.',
        'Bindung' => 'Vorhabensdauer, Verwendungsnachweis, Aufbewahrungspflichten. Zwölf '
            . 'Monate ohne Verlängerung sind ein üblicher Rahmen.',
        'Sperre'  => 'Mehrere Länder erlauben eine Förderung nur in einem Abstand von Jahren. '
            . 'Wer sie für die Website verbraucht, hat sie für die Warenwirtschaft nicht mehr.',
        'Beratung' => 'Wenn Sie einen Fördermittelberater einschalten, kostet das eigenes Geld.',
    ];

    // ------------------------------------------------------------ 7 Was SARTU tut

    public const SARTU_H2 = 'Was wir dabei tun und was nicht.';

    /** @return list<array{was:string,text:string,gilt:string}> */
    public static function stufen(): array
    {
        return [
            ['was'  => 'Unterlagen liefern',
             'gilt' => 'Im Festpreis enthalten',
             'text' => 'Sie bekommen das Angebot in der Form, die ein Antrag braucht: '
                . 'Leistungsbeschreibung, Nettobetrag, Zeitraum, Anbieterdaten.'],
            ['was'  => 'Auf die Reihenfolge achten',
             'gilt' => 'Im Festpreis enthalten',
             /* **Nicht „sagen Sie es im Bedarfsscheck".** Ein Förderfeld gibt es dort
                noch nicht — `FOERDERUNG_KONZEPT.md` §7 führt es als offene Frage 3. Ein
                Satz, der auf ein Feld verweist, das es nicht gibt, ist eine Zusage ohne
                Deckung. Über den Rückfrageweg geht es heute schon. */
             'text' => 'Sagen Sie uns bei der Anfrage, dass Sie eine Förderung planen. Dann '
                . 'sieht das Angebot keinen Start vor der Bewilligung vor, und seine '
                . 'Gültigkeit reicht über den Bewilligungsweg hinaus.'],
            ['was'  => 'Antrag ausfüllen und Verwendungsnachweis',
             'gilt' => 'Machen wir nicht',
             'text' => 'Das ist eine eigene Leistung mit eigenem Risiko. Dafür gibt es '
                . 'Fördermittelberater und Steuerberater.'],
        ];
    }

    /** `06_RECHT.md`: Pflichthinweis, wörtlich. */
    public const RECHTSHINWEIS = 'SARTU leistet keine Rechtsberatung.';

    public const ZUSAGE = 'Wir sagen Ihnen keine Bewilligung zu. Niemand kann das.';

    // ------------------------------------------------------------ 9 Drei Fragen

    /** @return list<array{frage:string,antwort:string}> */
    public static function fragen(): array
    {
        return [
            ['frage'   => 'Ich habe schon beauftragt. Geht noch etwas?',
             'antwort' => 'Für dieses Vorhaben in aller Regel nicht. Der Beginn vor der '
                . 'Antragstellung schliesst die Förderung in den meisten Programmen aus. Fragen '
                . 'Sie trotzdem bei der zuständigen Stelle nach — sie entscheidet, nicht wir.'],
            ['frage'   => 'Können Sie den Antrag für mich stellen?',
             'antwort' => 'Nein. Wir liefern die Unterlagen, die er braucht, und achten auf die '
                . 'Reihenfolge. Den Antrag selbst stellen Sie oder ein Berater, den Sie '
                . 'beauftragen.'],
            ['frage'   => 'Lohnt sich der Aufwand?',
             'antwort' => 'Das hängt daran, wie eilig Sie es haben. Wenn die Website in wenigen '
                . 'Wochen stehen soll, ist der Antragsweg der falsche. Wenn sie Teil eines '
                . 'grösseren Digitalisierungsvorhabens ist, kann er sich rechnen.'],
        ];
    }

    public const ABSCHLUSS_H2 = 'Welcher Umfang passt zu Ihrem Betrieb?';
}
