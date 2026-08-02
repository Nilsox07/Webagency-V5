<?php

declare(strict_types=1);

namespace Sartu;

use Sartu\Helpers\Env;
use Sartu\Services\Launchadressen;
use Sartu\Services\Preise;
use Sartu\Services\Websitetexte;

/**
 * `sitemap.xml`, `robots.txt` und `llms.txt` — Website-Lastenheft §16.
 *
 * **Erzeugt, nicht abgelegt.** Eine Datei unter `/public` müsste bei jeder neuen Seite von
 * Hand nachgezogen werden, und §14a Bedingung 3 bricht die Veröffentlichung ab, wenn eine
 * `noindex`-Seite in der Sitemap steht. Aus der Routenliste erzeugt kann das nicht passieren.
 *
 * ## `robots.txt`
 *
 * §17: „`robots.txt` sperrt weder `Googlebot`, `Bingbot` noch `OAI-SearchBot`; die
 * `GPTBot`-Entscheidung ist dokumentiert."
 *
 * **Die `GPTBot`-Entscheidung:** Er bleibt **zugelassen**. `GPTBot` sammelt Trainingsdaten,
 * `OAI-SearchBot` bedient die Suche. SARTUs einziger Sichtbarkeitsvorteil sind
 * veröffentlichte, überprüfbare Zahlen in einem Markt, der „Preis auf Anfrage" schreibt
 * (`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §3.4). Wer diese Zahlen vom Training ausschließt,
 * schließt sich aus den Antworten aus, in denen er vorkommen will. Der Preis dafür ist, dass
 * die Texte in Modelle einfließen — sie stehen ohnehin öffentlich.
 *
 * ## `llms.txt`
 *
 * §16 nennt sie „ohne Ranking-Behauptung". Sie enthält deshalb Fakten und Adressen, keine
 * Selbsteinschätzung. Es ist **kein** Standard, den eine Suchmaschine verlangt — Google sagt
 * ausdrücklich, es gebe keine Sonderauszeichnung für KI-Antworten. Sie steht hier, weil §16
 * sie aufzählt, nicht weil sie etwas bewirkt.
 */
final class Wurzeldateien
{
    /** @param array<string,string> $parameter */
    public function sitemap(array $parameter = []): Antwort
    {
        $basis = self::basis();
        $zeilen = ['<?xml version="1.0" encoding="UTF-8"?>',
                   '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];

        foreach (Launchadressen::alle() as $pfad => $prioritaet) {
            $zeilen[] = '  <url>';
            $zeilen[] = '    <loc>' . self::x($basis . $pfad) . '</loc>';
            $zeilen[] = '    <priority>' . self::x($prioritaet) . '</priority>';
            $zeilen[] = '  </url>';
        }

        $zeilen[] = '</urlset>';

        return Antwort::html(implode("\n", $zeilen) . "\n", 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    /** @param array<string,string> $parameter */
    public function robots(array $parameter = []): Antwort
    {
        $basis = self::basis();

        $text = "# SARTU\n"
            . "# Kein Suchdienst ist gesperrt. Die GPTBot-Entscheidung steht im Kopf von\n"
            . "# app/Wurzeldateien.php: zugelassen, weil veroeffentlichte Zahlen der Sinn\n"
            . "# dieser Seiten sind.\n\n"
            . "User-agent: *\n"
            . "Allow: /\n\n"
            . "# Die Schritte des Bedarfsschecks tragen noindex (Website-Lastenheft §16).\n"
            . "# Sie werden zusaetzlich nicht gecrawlt — sie fuehren nur einen Zwischenstand.\n"
            . "Disallow: /briefing/\n"
            . "Allow: /briefing$\n\n"
            . 'Sitemap: ' . $basis . "/sitemap.xml\n";

        return Antwort::html($text, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    /** @param array<string,string> $parameter */
    public function llms(array $parameter = []): Antwort
    {
        $basis = self::basis();
        $tabelle = Preise::tabelle();

        $text = "# SARTU\n\n"
            . '> ' . Websitetexte::KURZPOSITIONIERUNG . "\n\n"
            . "## Fakten\n\n"
            . "- Leistung: individuell programmierte Firmenwebsites fuer Betriebe in Deutschland\n"
            . '- Einmalpreise netto: Start ' . self::euro($tabelle['start']['einmalig_cent'])
                . ', Wachstum ' . self::euro($tabelle['wachstum']['einmalig_cent'])
                . ', Platzhirsch ' . self::euro($tabelle['platzhirsch']['einmalig_cent'])
                . ', Sonderprojekt ab ' . self::euro($tabelle['sonderprojekt']['einmalig_cent']) . "\n"
            . '- Betrieb netto je Monat: ' . self::euro($tabelle['start']['monatlich_cent'])
                . ' / ' . self::euro($tabelle['wachstum']['monatlich_cent'])
                . ' / ' . self::euro($tabelle['platzhirsch']['monatlich_cent']) . "\n"
            . "- Umfang: 1 Seite / bis zu 8 Seiten / bis zu 16 Seiten\n"
            . "- Korrekturrunden: 1 / 2 / 2\n"
            . "- Erstlaufzeit 12 Monate, Zahlungsziel 10 Tage\n"
            . "- Alle Preise netto, ausschliesslich fuer Unternehmer\n"
            . "- Keine Abstimmungstermine im Standardablauf\n"
            . "- Rechtstexte, Fotoaufnahmen und Shopfunktionen sind nicht enthalten\n"
            . "- Keine Zusage zu Platzierungen bei Suchmaschinen oder in KI-Antworten\n\n"
            . "## Seiten\n\n";

        foreach (Launchadressen::alle() as $pfad => $prioritaet) {
            $text .= '- ' . $basis . $pfad . "\n";
        }

        return Antwort::html($text, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    private static function euro(int $cent): string
    {
        return number_format($cent / 100, 0, ',', '.') . ' EUR';
    }

    private static function basis(): string
    {
        return rtrim((string) Env::get('BASE_URL', ''), '/');
    }

    private static function x(string $wert): string
    {
        return htmlspecialchars($wert, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
