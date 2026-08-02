<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Die festen Angebotstexte aus Portal-Lastenheft §4c — **wörtlich zu übernehmen**.
 *
 * > „Diese drei Texte stehen in **jedem** Angebot. Sie werden beim Anlegen eines Angebots
 * > vorbelegt, sind vom Admin editierbar, dürfen aber nicht leer bleiben. Formulierungen
 * > nicht erfinden."
 *
 * Sie stehen deshalb als Konstanten und nicht in einer Ansicht: Ein Text, den man beim
 * Rendern zusammenbaut, ist ein Text, den irgendwann jemand kürzt.
 *
 * ## Die BFSG-Bausteine sind kein Kleingedrucktes
 *
 * §4c: „Beide Zeilen dürfen nicht fehlen und nicht umformuliert werden. Die erste ist ein
 * Verkaufsargument, die zweite eine Grenze der Leistung. **Wer die zweite weglässt, verkauft
 * stillschweigend etwas mit, das nicht geliefert wird.**"
 *
 * Und der dritte Fall ist keine Warnung, sondern eine Sperre — es geht um ein Bußgeld bis
 * 100.000 €. Die Prüfung dazu steht in `AngebotDienst`.
 */
final class Angebotstexte
{
    /** §4c, `delivery_start_condition`. */
    public const LIEFERBEGINN =
        'Der genannte Zeitraum beginnt, sobald alle Aufgaben in Ihrem Bereich erledigt sind: '
        . 'bestätigte Fakten, vollständige Inhalte, freigegebene Rechtstexte und geklärte Bild- '
        . 'und Nutzungsrechte. Bis dahin läuft die Zeit nicht. Fehlt Ihre Mitwirkung länger als '
        . '14 Tage, dürfen wir das Projekt nach vorheriger Ankündigung pausieren; bereits '
        . 'abgeschlossene Meilensteine bleiben fällig.';

    /** §4c, `rights_text`. */
    public const RECHTE =
        'Nach vollständiger Zahlung erhalten Sie die Nutzungsrechte am gelieferten '
        . 'Website-Stand, an den von uns erstellten Texten und am für Sie gestalteten '
        . 'Erscheinungsbild. Ihre Domain gehört Ihnen, auf Ihren Namen registriert. Auf Wunsch '
        . 'stellen wir Ihnen den vollständigen Stand Ihrer Website als Export bereit, mit einer '
        . 'Anleitung, wie er ohne uns weiterbetrieben werden kann. Nicht übertragen werden '
        . 'allgemeine Bausteine, die wir projektübergreifend einsetzen, sowie Rechte Dritter '
        . '(z. B. Schriften oder Bilder), für die die jeweilige Lizenz gilt.';

    /** §4c, `domain_text`. */
    public const DOMAIN =
        'Ihre Domain wird auf Ihren Namen registriert — Sie sind Inhaber, nicht wir. Wir '
        . 'übernehmen Prüfung, Registrierung, Einrichtung und Verbindung. Die Domaingebühr ist '
        . 'in der Betriebspauschale enthalten, solange der Vertrag läuft. Endet der Vertrag, '
        . 'übertragen wir die Domain kostenfrei an Sie oder an einen Anbieter Ihrer Wahl; ab '
        . 'dann tragen Sie die Gebühr selbst. E-Mail-Postfächer sind nicht enthalten. Auf Wunsch '
        . 'richten wir die nötigen Einträge ein, damit ein Postfach Ihres Anbieters unter Ihrer '
        . 'Domain funktioniert.';

    /** §4c, Baustein 1 — steht in jedem Angebot unter „was enthalten ist". */
    public const BFSG_ENTHALTEN =
        'Technische Grundlagen der Bedienbarkeit sind enthalten: ausreichender Kontrast, '
        . 'vollständige Bedienung per Tastatur, sichtbare Fokusmarkierung, beschriftete '
        . 'Formularfelder und semantisches HTML. Ihre Website ist damit auch für Menschen mit '
        . 'Einschränkungen benutzbar.';

    /** §4c, Baustein 2 — steht in `exclusions`, solange die Seite keinen Vertrag schließt. */
    public const BFSG_AUSGENOMMEN =
        'Eine Prüfung und Bestätigung der Konformität nach dem '
        . 'Barrierefreiheitsstärkungsgesetz ist nicht Gegenstand dieses Angebots. Nach unserer '
        . 'Einschätzung ist Ihre Website davon nicht erfasst, weil Besucher darüber keinen '
        . 'Vertrag abschließen. Ändert sich das, sprechen Sie uns bitte an.';

    /** §4c: derselbe Baustein, ergänzt — bei Vertragsabschluss `ja` und Kleinstunternehmen `ja`. */
    public const BFSG_ZUSATZ_KLEINSTUNTERNEHMEN = ' Nach Ihrer Angabe als Kleinstunternehmen ausgenommen.';

    /** §4c: die Sperre. Keine Warnung — eine Warnung wird weggeklickt. */
    public const BFSG_SPERRE =
        'Hier greift das Barrierefreiheitsstärkungsgesetz. Bitte als eigenen Festpreisposten '
        . 'anbieten oder das Vorhaben ablehnen.';

    /**
     * §4c: Lieferkorridor je Paket in Werktagen. **Sonderprojekt: manuell** — dort steht
     * keine Zahl, also wird keine erfunden.
     *
     * @return array{0:int,1:int}|null
     */
    public static function lieferkorridor(string $paket): ?array
    {
        return match ($paket) {
            'start'       => [7, 10],
            'wachstum'    => [10, 15],
            'platzhirsch' => [15, 25],
            default       => null,
        };
    }

    /**
     * Der Ausschlusstext, abhängig von den beiden Kundenangaben aus §4c.
     *
     * @return string|null `null` bedeutet: Das Angebot lässt sich nicht senden.
     */
    public static function bfsgAusschluss(string $vertragsabschluss, string $kleinstunternehmen): ?string
    {
        if ($vertragsabschluss !== 'ja') {
            return self::BFSG_AUSGENOMMEN;
        }

        if ($kleinstunternehmen === 'ja') {
            return self::BFSG_AUSGENOMMEN . self::BFSG_ZUSATZ_KLEINSTUNTERNEHMEN;
        }

        return null;
    }

    /**
     * Ein Wort weicht ab, und zwar bewusst.
     *
     * §4c schreibt in `delivery_start_condition` „alle Aufgaben in Ihrem **Portal**".
     * Nach außen heißt der Bereich **Kundenbereich** (`CLAUDE.md`, Website-Lastenheft §5b).
     * Der Satz steht hier deshalb mit „in Ihrem Bereich". Inhalt, Fristen und Zahlen sind
     * unverändert — es ist der Name der Sache, nicht die Sache.
     */
    public const ABWEICHUNG_VOM_WORTLAUT = 'delivery_start_condition: „Portal" → „Bereich"';
}
