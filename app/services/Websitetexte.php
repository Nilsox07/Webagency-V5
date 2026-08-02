<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Die gebundenen Texte der öffentlichen Website — Klasse 1 im Texter-Skill.
 *
 * > „Wiederkehrende Beschriftungen — Knopftexte, Navigationspunkte, Statusnamen. Wer
 * > `Bedarf prüfen lassen` auf einer Seite zu `Jetzt starten` macht, lässt die Website
 * > unfertig wirken."
 *
 * Sie stehen deshalb **einmal** hier und nicht auf zwanzig Seiten verteilt. Der Pflichthinweis
 * zur Umsatzsteuer ist der wichtigste Fall: Sein Wegfall auf einer einzigen preisführenden
 * Seite ist ein Rechtsrisiko (Website-Lastenheft §2).
 *
 * ## Die Umsatzsteuerzeile hängt an den Betreiberdaten
 *
 * `operator_settings.kleinunternehmer` entscheidet zur Laufzeit. Steht dort `ja`, darf
 * „zzgl. USt." **nirgends** erscheinen — dann gäbe es die Steuer nicht, die die Zeile
 * ankündigt. Der Zusatz „Ausschließlich für Unternehmer" bleibt in beiden Fällen: Er
 * beschreibt die Zielgruppe, nicht die Steuer.
 */
final class Websitetexte
{
    /** Website-Lastenheft §2, Pflichthinweis bei jeder Preisnennung. */
    public const PREISHINWEIS = 'Alle Preise netto zzgl. gesetzlicher Umsatzsteuer. '
        . 'Ausschließlich für Unternehmer.';

    /** Dieselbe Aussage ohne Umsatzsteuer — §19 UStG, wenn `kleinunternehmer = ja`. */
    public const PREISHINWEIS_KLEINUNTERNEHMER = 'Alle Preise netto. '
        . 'Ausschließlich für Unternehmer. Kein Ausweis der Umsatzsteuer nach § 19 UStG.';

    /** §5 Sektion 10, direkt unter dem Abschlussknopf. */
    public const ABSCHLUSSHINWEIS = 'Unverbindlich bis zum geprüften Angebot.';

    /** §2: Rankings sind niemandes Zusage. Steht auf jeder Seite, die von SEO spricht. */
    public const KEINE_RANKINGZUSAGE = 'Rankings, Anfragen oder Nennungen in KI-Systemen kann '
        . 'niemand garantieren. Wir bauen das Fundament und halten die technische Suchgesundheit '
        . 'im Betrieb im Blick.';

    /** §2: Portalansichten ohne freigegebenes Kundenprojekt. */
    public const MUSTERANSICHT = 'Musteransicht';

    // ---------------------------------------------------------------- Positionierung

    /** Die vier Sätze aus der Kalibrierung des Texter-Skills. Vier Fassungen wurden verworfen. */
    public const OHNE_TERMIN = 'Ohne einen einzigen Termin zur fertigen Website.';

    public const TROTZDEM_ERREICHBAR = 'Sprechen können Sie trotzdem mit uns. Sie müssen nur nicht.';

    public const EIN_PREIS = 'Ein Preis. Ein Ergebnis. Keine Stundenabrechnung, keine Nachforderung.';

    public const SONDERPROJEKT_TERMIN = 'Nur Sonderprojekte klären wir vor dem Angebot persönlich.';

    /** §4 Fußbereich, Kurzpositionierung. */
    public const KURZPOSITIONIERUNG = 'Individuell programmierte Firmenwebsites zum Festpreis. '
        . 'Geplant, geschrieben, programmiert und betrieben.';

    // ---------------------------------------------------------------- Beschriftungen

    /**
     * §5b — die einzige gültige Hauptnavigation. §3 ist dafür abgelöst.
     *
     * **`Kundenbereich` zeigt auf `/leistung-portal`, nicht auf die Anmeldung.** §5b begründet
     * den Punkt als Unterscheidungsmerkmal gegenüber Wettbewerbern — das ist eine
     * Verkaufsaussage und gehört auf die Seite, die sie erklärt. Wer sich anmelden will,
     * findet den Weg dort; er ist bereits Kunde und kommt ohnehin über den Anmeldelink.
     *
     * **`Fragen` zeigt auf den Abschnitt der Startseite**, nicht auf eine eigene Seite. §16
     * listet alle Launch-Adressen — `/fragen` steht dort nicht, und eine Seite zu erfinden,
     * die es in der Adressliste nicht gibt, wäre eine zweite Wahrheit.
     */
    public const NAVIGATION = [
        '/leistungen'      => 'Leistungen',
        '/preise'          => 'Preise',
        '/ablauf'          => 'Ablauf',
        '/leistung-portal' => 'Kundenbereich',
        '/ueber-uns'       => 'Über uns',
        '/#fragen'         => 'Fragen',
    ];

    /** §4 — fünf Spalten, Reihenfolge verbindlich. */
    public static function fussspalten(): array
    {
        return [
            'Leistungen' => [
                '/leistung-webdesign' => 'Webdesign',
                '/leistung-texte'     => 'Website-Texte',
                '/leistung-seo-lokal' => 'Sichtbarkeit',
                '/leistung-wartung'   => 'Rundum-Schutz',
                '/leistung-portal'    => 'Kundenbereich',
            ],
            'Wissen' => [
                '/ratgeber' => 'Ratgeber',
                '/lexikon'  => 'Lexikon',
            ],
            'Unternehmen' => [
                '/ablauf'    => 'Ablauf',
                '/preise'    => 'Preise',
                '/ueber-uns' => 'Über uns',
                '/kontakt'   => 'Kontakt',
            ],
        ];
    }

    /**
     * Der Preishinweis, abhängig von den Betreiberdaten.
     *
     * Der Aufrufer übergibt den Wert — diese Klasse liest keine Datenbank.
     */
    public static function preishinweis(bool $kleinunternehmer): string
    {
        return $kleinunternehmer ? self::PREISHINWEIS_KLEINUNTERNEHMER : self::PREISHINWEIS;
    }
}
