<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Format;

/**
 * Die sechs Mailtexte, die §10 vorschreibt und die bis zum 02.08.2026 fehlten.
 *
 * ## Warum sie an einer Stelle stehen
 *
 * Die vorhandenen Mails tragen ihren Wortlaut im jeweiligen Dienst — beim `Rechnungsdienst`
 * und beim `Zahlungslauf` je zwei bis drei. Das ging, solange ein Dienst seine Mails allein
 * hatte. Diese sechs verteilen sich auf fünf Dienste, und **eine davon geht an beide
 * Empfänger**: Stünde ihr Text zweimal da, wäre er beim ersten Umformulieren zweierlei.
 *
 * Der zweite Grund ist prüfbar: §10 bindet den Wortlaut. Ein Test kann gegen diese Klasse
 * prüfen, ohne den Satz noch einmal abzutippen — und ein abgetippter Satz im Test prüft nur,
 * dass zweimal dasselbe getippt wurde.
 *
 * ## Ein Wort weicht ab, und zwar bewusst
 *
 * §10 schreibt „liegt im **Portal**" und „Portallink". Nach außen heißt der Bereich
 * **Kundenbereich** (`CLAUDE.md`, Website-Lastenheft §5b) — „Portal" steht auf der Liste der
 * Wörter, die ein Kunde nie zu lesen bekommt. Inhalt, Fristen und Zahlen sind unverändert;
 * es ist der Name der Sache, nicht die Sache.
 *
 * Dieselbe Abweichung ist in `Angebotstexte::ABWEICHUNG_VOM_WORTLAUT` schon einmal
 * festgehalten. Sie steht hier ein zweites Mal, weil sie ein zweites Mal auftritt.
 *
 * ## Was hier NICHT steht
 *
 * Kein Satz, der nicht in §10 steht. Wo §10 „interne Kurzmeldung" sagt, steht hier eine
 * Kurzmeldung aus **Feldwerten** — Nummer, Organisation, Betrag, Adresse. Keine Wertung,
 * keine Empfehlung, keine erfundene Zusicherung.
 */
final class Mailtexte
{
    /** Was gegenüber dem Wortlaut in §10 abweicht — und warum. */
    public const ABWEICHUNG_VOM_WORTLAUT = '§10: „Portal" → „Kundenbereich" (5 Stellen)';

    // ---------------------------------------------------------------- 1 Angebot gesendet

    public const ANGEBOT_GESENDET_BETREFF = 'Ihr Angebot von SARTU liegt bereit';

    /** §10: „Ihr Angebot mit Umfang, Preis und Zahlungsplan liegt im Portal. Gültig bis {Datum}." */
    public static function angebotGesendet(?string $gueltigBis): string
    {
        return 'Ihr Angebot mit Umfang, Preis und Zahlungsplan liegt in Ihrem Kundenbereich. '
            . 'Gültig bis ' . Format::datum($gueltigBis) . ".\n";
    }

    // ------------------------------------------------------- 2 Angebot angenommen (Admin)

    /** §10: `Angebot angenommen: {Organisation}` */
    public static function angebotAngenommenBetreff(string $organisation): string
    {
        return 'Angebot angenommen: ' . $organisation;
    }

    /**
     * §10: „interne Kurzmeldung". Vier Feldwerte, kein Satz darüber hinaus.
     */
    public static function angebotAngenommen(
        string $angebotsnummer,
        string $name,
        int $einmalNettoCent,
        string $projektId,
    ): string {
        return 'Angebot ' . $angebotsnummer . " wurde angenommen.\n"
            . 'Angenommen von: ' . $name . "\n"
            . 'Einmalpreis netto: ' . Format::euro($einmalNettoCent) . "\n"
            . "\n" . self::adresse('/admin/projekte/' . $projektId) . "\n";
    }

    // ---------------------------------------------------------------- 3 Neue Aufgaben

    public const AUFGABEN_BETREFF = 'Es liegen Aufgaben für Sie bereit';

    /** §10, Wortlaut gebunden — hier steht „Portal" nicht drin, also bleibt er unverändert. */
    public static function aufgaben(): string
    {
        return "Wir brauchen ein paar Angaben von Ihnen. Das dauert meist 15 bis 25 Minuten.\n"
            . "\n" . self::adresse('/portal/aufgaben') . "\n";
    }

    // ---------------------------------------------------------------- 4 Faktenfreigabe

    public const FREIGABE_BETREFF = 'Freigabe bestätigt — wir starten';

    /**
     * §10: „Danke für die Freigabe. Wir beginnen mit der Produktion. Fertigstellung
     * voraussichtlich in {min}–{max} Werktagen."
     *
     * **Fehlt der Korridor, entfällt der zweite Satz.** Eine erfundene Zahl in einer
     * Fertigstellungszusage ist schlimmer als eine fehlende Zeile — und §4 macht
     * `delivery_days_min` zum Pflichtfeld des Angebots, das heißt: Ist er leer, gibt es
     * kein gesendetes Angebot, und diese Mail dürfte es gar nicht geben.
     */
    public static function freigabe(?int $min, ?int $max): string
    {
        $text = "Danke für die Freigabe. Wir beginnen mit der Produktion.\n";

        if ($min !== null && $max !== null) {
            $text .= 'Fertigstellung voraussichtlich in ' . $min . '–' . $max . " Werktagen.\n";
        }

        return $text;
    }

    /** Dieselbe Freigabe, an SARTU. §10 sagt „an beide" — derselbe Anlass, zwei Blickwinkel. */
    public static function freigabeIntern(string $organisation, string $projektId): string
    {
        return $organisation . " hat Fakten und Umfang freigegeben. Die Produktion beginnt.\n"
            . "\n" . self::adresse('/admin/projekte/' . $projektId) . "\n";
    }

    // ---------------------------------------------------------------- 5 Antwort auf Nachricht

    public const ANTWORT_BETREFF = 'Antwort auf Ihre Nachricht';

    /** §10: „Antworttext + Portallink". */
    public static function antwort(string $antworttext): string
    {
        return trim($antworttext) . "\n\n" . self::adresse('/portal/hilfe') . "\n";
    }

    // ---------------------------------------------------------------- 6 Angebot läuft ab

    /** §10: `Ihr Angebot gilt noch bis {Datum}` */
    public static function ablaufBetreff(?string $gueltigBis): string
    {
        return 'Ihr Angebot gilt noch bis ' . Format::datum($gueltigBis);
    }

    /**
     * §10: „Ihr Angebot läuft am {Datum} ab. Danach stellen wir es Ihnen gern neu aus —
     * melden Sie sich einfach."
     */
    public static function ablauf(?string $gueltigBis): string
    {
        return 'Ihr Angebot läuft am ' . Format::datum($gueltigBis) . " ab. Danach stellen wir "
            . "es Ihnen gern neu aus — melden Sie sich einfach.\n"
            . "\n" . self::adresse('/portal/angebot') . "\n";
    }

    // ---------------------------------------------------------------- intern

    /**
     * Eine vollständige Adresse aus `BASE_URL`.
     *
     * Fehlt `BASE_URL`, steht der Pfad allein da. Ein erfundener Hostname in einer Mail
     * führt ins Nichts; ein Pfad ist wenigstens nachvollziehbar.
     */
    private static function adresse(string $pfad): string
    {
        return rtrim((string) \Sartu\Helpers\Env::get('BASE_URL', ''), '/') . $pfad;
    }
}
