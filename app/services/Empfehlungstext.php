<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Der Wortlaut der Ergebnisseite — Website-Lastenheft §9.3.
 *
 * **Warum das hier steht und nicht in der Ansicht:** §9.3 gibt drei Textbausteine gebunden
 * vor (Pflichthinweis, Sonderprojekt, Unklarheit) und zeigt die Begründung als **Beispiel**.
 * Ein Beispiel ist kein Wortlaut. Die Begründung wird deshalb aus den tatsächlich
 * angekreuzten Signalen gebaut — genauso, wie das Beispiel es vormacht:
 *
 * > „Sie erklären mehrere Leistungen, arbeiten in mehr als einer Region und suchen
 * > regelmäßig Mitarbeitende. Dafür reicht eine einzelne Seite nicht — …"
 *
 * Satz 1 zählt auf, was angekreuzt wurde. Satz 2 zieht die Folge für den Umfang.
 *
 * ## Zwei Stellen, an denen der Wortlaut aus §9.3 geändert wurde
 *
 * `SARTU_TEXTREGELN.md` steht in der Rangfolge auf **Rang 3** und regelt die Form jedes
 * Textes; das Website-Lastenheft steht auf **Rang 5**. Wo beide auseinandergehen, gewinnt
 * Rang 3.
 *
 * | §9.3 schreibt | Hier steht | Regel |
 * |---|---|---|
 * | „in eine unserer drei **Lösungen**" | „zu einem unserer drei **Umfänge**" | Regel 7 Liste C. Den Ersatz nennt die Regel selbst |
 * | H1 „Welche Website passt **wirklich** zu Ihrem Unternehmen?" | ohne „wirklich" | Regel 7 Liste A, „wirklich" als Verstärkung |
 *
 * Beide Änderungen stehen im Abgabebericht.
 *
 * ## Was hier NICHT passiert
 *
 * Es wird **keine Zahl** formuliert. Preise, Seitenzahlen und Korrekturrunden kommen aus
 * `Preise` und damit aus der Preistabelle im Masterkonzept.
 */
final class Empfehlungstext
{
    /** Pflichthinweis bei jeder Preisnennung (Website §2 und §9.3) — gebunden. */
    public const PFLICHTHINWEIS = 'Alle Preise netto zzgl. gesetzlicher Umsatzsteuer. '
        . 'Ausschließlich für Unternehmer. Verbindlich ist erst das von SARTU geprüfte Angebot.';

    /** §9.3, „Bei Sonderprojekt-Gate" — gebunden. Die Zahl steht in `Preise`. */
    private const SONDERPROJEKT = 'Ihr Vorhaben enthält eine besondere Funktion. '
        . 'Solche Projekte beginnen bei %s einmalig zzgl. Betrieb. '
        . 'Sie erhalten dazu ein kurzes Fachmodul und danach einen geprüften Gesamtpreis.';

    /** §9.3, „Bei Unklarheit" — mit dem Ersatz aus Regel 7 (siehe Klassenkopf). */
    private const UNKLAR = 'Ihr Bedarf passt voraussichtlich zu einem unserer drei Umfänge. '
        . 'Eine Angabe entscheidet noch über den Umfang — nach dem Absenden stellen wir Ihnen '
        . 'höchstens eine gebündelte Rückfrage.';

    /**
     * Je Signal aus Thema 3 ein Satzglied. Der Wortlaut folgt dem Beispiel in §9.3.
     *
     * `nichts_davon` steht hier nicht: Wer nichts ankreuzt, bekommt keine Aufzählung,
     * sondern die Rückfrage.
     */
    private const GLIEDER = [
        Empfehlung::SIGNAL_HAUPTANGEBOT       => 'haben ein klares Hauptangebot',
        Empfehlung::SIGNAL_MEHRERE_LEISTUNGEN => 'erklären mehrere Leistungen',
        Empfehlung::SIGNAL_MEHRERE_REGIONEN   => 'arbeiten in mehr als einer Region',
        Empfehlung::SIGNAL_RECRUITING         => 'suchen regelmäßig Mitarbeitende',
        Empfehlung::SIGNAL_PROJEKTE_AKTUELL   => 'halten Projekte und Neuigkeiten aktuell',
    ];

    /**
     * Die Folge für den Umfang, je Paket.
     *
     * Jeder Eintrag hat einen Satz unter sieben Wörtern (Regel 2) und nennt keine Zahl —
     * die Zahlen stehen darunter in der Merkmalszeile aus `Preise`.
     */
    private const FOLGE = [
        'start'       => 'Dafür trägt eine Seite. Sie zeigt Ihr Angebot und Ihren Kontaktweg.',
        'wachstum'    => 'Eine Seite reicht dafür nicht. Jede Leistung braucht eine eigene Seite mit eigenem Text.',
        'platzhirsch' => 'Dafür reicht eine einzelne Seite nicht. Leistungen, Regionen und Mitarbeitersuche '
            . 'brauchen je einen eigenen Platz.',
    ];

    /**
     * @param list<string> $umfangssignale die angekreuzten Signale aus Thema 3
     *
     * @return array{
     *     ueberschrift:string,
     *     aufzaehlung:list<string>,
     *     satz:?string,
     *     folge:?string,
     *     hinweis:string
     * }
     */
    public static function fuer(string $paket, array $umfangssignale): array
    {
        if ($paket === 'sonderprojekt') {
            return [
                'ueberschrift' => 'Ihr Vorhaben ist ein Sonderprojekt',
                'aufzaehlung'  => [],
                'satz'         => null,
                'folge'        => sprintf(self::SONDERPROJEKT, self::abPreis()),
                'hinweis'      => self::PFLICHTHINWEIS,
            ];
        }

        if ($paket === 'unklar') {
            return [
                'ueberschrift' => 'Eine Angabe fehlt uns noch',
                'aufzaehlung'  => [],
                'satz'         => null,
                'folge'        => self::UNKLAR,
                'hinweis'      => self::PFLICHTHINWEIS,
            ];
        }

        $glieder = [];

        foreach ($umfangssignale as $signal) {
            if (isset(self::GLIEDER[$signal])) {
                $glieder[] = self::GLIEDER[$signal];
            }
        }

        return [
            'ueberschrift' => 'Unsere vorläufige Empfehlung: ' . Preise::name($paket),
            // Regel 3: mehr als drei Glieder werden zur Liste, nicht zu einem längeren Satz.
            'aufzaehlung'  => count($glieder) > 3 ? $glieder : [],
            'satz'         => count($glieder) > 3 || $glieder === [] ? null : self::satz($glieder),
            'folge'        => self::FOLGE[$paket] ?? null,
            'hinweis'      => self::PFLICHTHINWEIS,
        ];
    }

    /** @param list<string> $glieder höchstens drei */
    private static function satz(array $glieder): string
    {
        $letztes = array_pop($glieder);

        $anfang = $glieder === [] ? '' : implode(', ', $glieder) . ' und ';

        return 'Sie ' . $anfang . $letztes . '.';
    }

    private static function abPreis(): string
    {
        $zeile = Preise::zeile('sonderprojekt');

        return \Sartu\Helpers\Format::euro((int) $zeile['einmalig_cent']);
    }
}
