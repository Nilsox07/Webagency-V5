<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Der Wortlaut von `/leistungen`, `/preise` und `/ablauf` —
 * Website-Lastenheft §6, §7 und §8.
 *
 * Dieselbe Trennung wie bei der Startseite: Text hier, Ausgabe in der Ansicht, Zahlen in
 * `Preise`. Was auf mehreren Seiten steht, steht einmal in `Websitetexte` oder
 * `Leistungszeilen`.
 */
final class Unterseitentexte
{
    // ============================================================ /leistungen (§6)

    public const LEISTUNGEN_TITEL = 'Leistungen: Webdesign, Texte, SEO und Betrieb | SARTU';

    public const LEISTUNGEN_BESCHREIBUNG = 'Webdesign, Website-Texte, SEO-Grundlage, Domain '
        . 'und Launch, Kundenbereich und laufender Betrieb — bei SARTU als ein Ergebnis zum '
        . 'Festpreis statt als Einzeloptionen.';

    public const LEISTUNGEN_H1 = 'Website, Texte, Sichtbarkeit und Betrieb als ein System.';

    public const LEISTUNGEN_LEAD = 'Sie bekommen kein Bündel einzelner Leistungen, sondern ein '
        . 'Ergebnis: eine Website, die Ihr Angebot erklärt, Anfragen erzeugt und danach '
        . 'zuverlässig betrieben wird.';

    /** §6 Sektion 2 — das Antwortmodul, 45 Wörter. */
    public const LEISTUNGEN_KURZ = 'SARTU verbindet Strategie, Texte, Design, Programmierung, '
        . 'SEO-Grundlage, Domain und Launch, Kundenbereich und Betrieb zu einem '
        . 'Festpreis-Ergebnis. Was Ihr Projekt davon in welcher Tiefe braucht, entscheiden wir '
        . 'im Angebot — nicht Sie im Bestellformular.';

    /** §6 Sektion 5 — Ergebnis je Stufe, keine Häkchenliste. */
    public static function tiefeJeStufe(): array
    {
        return [
            'start' => 'Eine Seite, die Ihr Angebot, Ihr Gebiet und Ihren Kontaktweg erklärt. '
                . 'Ein Thema, eine Zielgruppe.',
            'wachstum' => 'Je Leistung eine eigene Seite mit eigenem Text und eigenem '
                . 'Seitenthema. Damit beantwortet jede Seite genau eine Suchanfrage.',
            'platzhirsch' => 'Zusätzlich Seiten je Ort und ein Bereich für Bewerbungen. Der '
                . 'Betrieb ist damit für Kunden und für Bewerber auffindbar.',
            'sonderprojekt' => 'Umfang nach technischer Vorprüfung. Wir sagen ab, wenn wir das '
                . 'Ergebnis nicht verantworten können.',
        ];
    }

    /** @return list<array{frage:string,antwort:string}> §6 Sektion 6. */
    public static function leistungenFragen(): array
    {
        return [
            ['frage'   => 'Kann ich einzelne Leistungen dazubuchen?',
             'antwort' => 'Nein. Es gibt keine Aufpreisliste. Jedes Angebot enthält Strategie, '
                . 'Texte, Design, Programmierung, SEO-Grundlage, Kundenbereich und Betrieb — '
                . 'wir gewichten sie nach Ihrem Ziel.'],
            ['frage'   => 'Was ist, wenn ich später mehr brauche?',
             'antwort' => 'Dann bekommen Sie dafür ein eigenes Angebot mit eigenem Festpreis. '
                . 'Der bestehende Preis ändert sich dadurch nicht.'],
            ['frage'   => 'Übernehmen Sie auch bestehende Websites?',
             'antwort' => 'Wir bauen neu, statt eine fremde Installation weiterzupflegen. Ihre '
                . 'Inhalte, Ihre Domain und Ihre E-Mail-Adressen nehmen wir dabei mit.'],
        ];
    }

    // ============================================================ /preise (§7)

    public const PREISE_TITEL = 'Preise: Firmenwebsite ab 1.490 € netto | SARTU';

    public const PREISE_BESCHREIBUNG = 'Start 1.490 €, Wachstum 3.900 €, Platzhirsch 7.900 € '
        . 'netto — jeweils mit festem Betriebspaket. Erstjahreskosten und Zahlungsplan '
        . 'transparent aufgeschlüsselt.';

    public const PREISE_H1 = 'Klare Preise. Wir prüfen, was wirklich passt.';

    public const PREISE_LEAD = 'Sie müssen kein Paket auswählen. Die kurze Bedarfseinschätzung '
        . 'zeigt, welcher Umfang wahrscheinlich passt; wir prüfen das Ergebnis persönlich, '
        . 'bevor Sie ein Angebot bekommen.';

    /** §7 Sektion 5 — der Rundum-Schutz, Rahmen zwingend nach §2. */
    public const SCHUTZ_H2 = 'Keine Wartung für Sie.';

    public const SCHUTZ_ENTHALTEN = [
        'Hosting',
        'SSL',
        'tägliche Sicherungen',
        'Überwachung',
        'technische Aktualisierungen',
        'technische Suchgesundheit',
        'Formularprüfung',
        'Zugang zum Kundenbereich',
    ];

    public const SCHUTZ_NICHT_ENTHALTEN = [
        'unbegrenzte Text- oder Designänderungen',
        'neue Seiten',
        'neue Ziele',
    ];

    public const SCHUTZ_SELBST = [
        'Öffnungszeiten',
        'Kontaktdaten',
        'vorhandene Einträge',
        'Seitenstatus',
    ];

    /** §7 Sektion 6. */
    public const DOMAIN_H2 = 'Die Domain gehört Ihnen.';

    public const DOMAIN_TEXT = 'Wir verwalten sie technisch, Inhaber bleiben Sie. Eine normale '
        . 'Domain bis 30 € netto im Jahr ist im Betrieb enthalten. Vor jeder Umstellung sichern '
        . 'wir Ihre bestehenden E-Mail-Einträge.';

    /** §7 Sektion 7. */
    public const ZAHLUNG_H2 = 'Zahlungsziel 10 Tage. Zahlung im Kundenbereich.';

    public const ZAHLUNG_PLAENE = [
        'Start und Wachstum' => '50 % bei Auftrag, 50 % nach Abnahme',
        'Platzhirsch'        => '40 % bei Auftrag, 30 % zur Vorschau, 30 % nach Abnahme',
    ];

    public const ZAHLUNG_SLOT = 'Der Produktionsplatz wird nach der ersten Zahlung vergeben.';

    /** @return list<array{frage:string,antwort:string}> §7 Sektion 8, sechs Fragen. */
    public static function preiseFragen(): array
    {
        return [
            ['frage'   => 'Sind die Preise netto oder brutto?',
             'antwort' => 'Netto. SARTU arbeitet ausschließlich für Unternehmer; die '
                . 'Umsatzsteuer weisen wir auf der Rechnung aus.'],
            ['frage'   => 'Kann ich später erweitern?',
             'antwort' => 'Ja. Sie bekommen dafür ein eigenes Angebot mit eigenem Festpreis. '
                . 'Der laufende Betrieb wird dabei neu berechnet, wenn die Website wächst.'],
            ['frage'   => 'Warum ist der Betrieb verpflichtend?',
             'antwort' => 'Weil wir für die Website geradestehen. Ohne Zugriff auf Server, '
                . 'Sicherungen und Aktualisierungen könnten wir das nicht — und Sie müssten '
                . 'sich selbst darum kümmern.'],
            ['frage'   => 'Gibt es versteckte Kosten?',
             'antwort' => 'Nein. Ein Standardangebot endet exakt beim genannten Festpreis. Was '
                . 'nicht hineinpasst, bekommt ein eigenes Angebot — vorher, nicht hinterher.'],
            ['frage'   => 'Was kostet die Domain?',
             'antwort' => 'Eine normale Domain bis 30 € netto im Jahr ist im Betrieb enthalten. '
                . 'Teurere Endungen rechnen wir zum Einkaufspreis weiter.'],
            ['frage'   => 'Und wenn ich eine Sonderfunktion brauche?',
             'antwort' => 'Shop, Kundenlogin, Buchung oder eine Schnittstelle sind ein '
                . 'Sonderprojekt. Sie bekommen dafür einen Festpreis vor Ihrer Entscheidung — '
                . 'oder eine Absage, wenn wir es nicht verantworten können.'],
        ];
    }

    // ============================================================ /ablauf (§8)

    public const ABLAUF_TITEL = 'Ablauf: vom Bedarfsscheck zur fertigen Website | SARTU';

    public const ABLAUF_BESCHREIBUNG = 'So läuft ein SARTU-Projekt: kurzer Bedarfsscheck, '
        . 'geprüftes Festpreisangebot, Fragen im Kundenbereich, Produktion, Vorschau und '
        . 'Freigabe, Launch und laufender Betrieb.';

    public const ABLAUF_H1 = 'Ein Websiteprojekt ohne Termin-Marathon.';

    public const ABLAUF_LEAD = 'Standardprojekte laufen bei SARTU über den Kundenbereich. Ein '
        . 'Gespräch ist jederzeit möglich, aber nicht Pflicht: Angebot, Fragen, Zahlungen, '
        . 'Domain, Vorschau und Freigaben liegen dort an einem Ort.';

    /** §8 Sektion 2 — der Vergleich, ohne Prozentangaben. */
    public const VERGLEICH = [
        'Erstgespräch als Pflichttermin'                 => 'Bedarfsscheck in etwa 3 Minuten',
        'Kickoff-Termin und Fragebogen'                  => 'Der Kundenbereich übernimmt bekannte Fakten und fragt nur Lücken',
        'Rückmeldungen verteilt über E-Mails und Anrufe' => 'Rückmeldungen gesammelt an einer Stelle',
        'Viele offene Entscheidungen beim Kunden'        => 'Struktur, Design und Technik entscheiden wir',
        'Preis wird im Verlauf konkret'                  => 'Festpreis steht vor Auftrag',
    ];

    /**
     * §8 Sektion 3 — acht Schritte, je zwei bis drei Sätze.
     *
     * @return list<array{titel:string,text:string}>
     */
    public static function achtSchritte(): array
    {
        return [
            ['titel' => 'Bedarfsscheck',
             'text'  => 'Sie beantworten wenige Fragen zu Ihrem Betrieb, Ihrem Ziel und Ihrer '
                . 'Domain. Am Ende sehen Sie eine vorläufige Empfehlung mit Preis. Das dauert '
                . 'etwa drei Minuten und verpflichtet zu nichts.'],
            ['titel' => 'Unsere Prüfung',
             'text'  => 'Wir sehen uns Ihre Angaben an und prüfen die Empfehlung nach. Passt ein '
                . 'kleinerer Umfang, empfehlen wir den kleineren.'],
            ['titel' => 'Angebot im Kundenbereich',
             'text'  => 'Sie bekommen einen Anmeldelink per E-Mail. Im Angebot stehen Umfang, '
                . 'Seitenstruktur, Preis, Zahlungsplan und was nicht enthalten ist. Es gilt '
                . '30 Kalendertage.'],
            ['titel' => 'Annahme und erste Zahlung',
             'text'  => 'Sie bestätigen vier Punkte und tippen Ihren Namen. Danach stellen wir '
                . 'die Anzahlungsrechnung — Zahlungsziel 10 Tage.'],
            ['titel' => 'Domain und Ihre Angaben',
             'text'  => 'Wir tragen ein, was wir schon wissen, und fragen den Rest im '
                . 'Kundenbereich. Sie laden Logo, Bilder und Unterlagen hoch und bestätigen die '
                . 'Fakten. Erst danach beginnt der Lieferzeitraum.'],
            ['titel' => 'Produktion',
             'text'  => 'Wir bauen die Website und schreiben die Texte aus Ihren Fakten. KI hilft '
                . 'beim Entwurf; geprüft und freigegeben wird von uns.'],
            ['titel' => 'Vorschau, Rückmeldung, Abnahme',
             'text'  => 'Sie sehen die fertige Website unter einer eigenen Adresse. Ihre '
                . 'Rückmeldungen sammeln Sie und schicken sie in einem Durchgang. Zum Schluss '
                . 'nehmen Sie ab — mit Ankreuzen und getipptem Namen.'],
            ['titel' => 'Launch und Betrieb',
             'text'  => 'Wir verbinden die Domain, schalten live und übernehmen den Betrieb. Ab '
                . 'diesem Datum läuft die Erstlaufzeit von 12 Monaten.'],
        ];
    }

    /** §8 Sektion 5. */
    public const KUNDE_TUT = [
        'Fakten bestätigen',
        'Material hochladen',
        'Domain bestätigen',
        'Vorschau prüfen',
        'freigeben',
    ];

    /** §8 Sektion 6. */
    public const SARTU_ENTSCHEIDET = [
        'Empfehlung',
        'Seitenstruktur',
        'Nutzerführung',
        'Design',
        'SEO-Grundlage',
        'Technik',
        'Hosting',
        'DNS-Plan',
    ];

    /** §8 Sektion 7 — der Zeitrahmen. Die Zahlen kommen aus `Angebotstexte::lieferkorridor()`. */
    public const ZEITRAHMEN_BEDINGUNG = 'Der Zeitraum beginnt, wenn Zahlung, Angaben und '
        . 'Material vorliegen.';
}
