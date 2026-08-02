<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Alle öffentlichen Adressen mit Indexierung und Priorität — Website-Lastenheft §16.
 *
 * ## Eine Liste, drei Verwender
 *
 * `sitemap.xml`, `robots.txt` und `llms.txt` lesen von hier. Drei Listen wären drei
 * Gelegenheiten, eine Adresse zu vergessen — und §14a Bedingung 3 bricht die
 * Veröffentlichung ab, wenn eine `noindex`-Seite in der Sitemap steht.
 *
 * ## Was hier **nicht** steht
 *
 * | Adresse | Warum sie fehlt |
 * |---|---|
 * | `/briefing/1` … `/briefing/n` | §16: `noindex`. Sie stehen deshalb weder in der Sitemap noch in dieser Liste |
 * | `/agb` | §14: „Nur live und verlinkt, wenn anwaltlich final. Sonst gar nicht verlinken und `noindex`." Der Text steht auf `entwurf` |
 * | Danke-Seiten, 404 | §16: `noindex` |
 * | Jede Ortsseite | §17: „**Keine** Ortsseite in der produktiven Veröffentlichung — auch nicht als unverlinkter Entwurf", solange `[GESCHAEFTSADRESSE_STATUS]` offen ist |
 *
 * ## `/impressum` und `/datenschutz` stehen drin, obwohl sie zurzeit 404 liefern
 *
 * Sie sind Launch-Adressen mit Priorität 0.3 (§16). Dass sie heute 404 antworten, liegt am
 * Zustand `entwurf` in `legal_texts` — und genau dagegen sperrt §14a die produktive
 * Veröffentlichung. Die Sitemap prüft das nicht; die Startsperre tut es.
 */
final class Launchadressen
{
    /**
     * §16, Spalte „Priorität Sitemap".
     *
     * @return array<string,string> Adresse => Priorität
     */
    public static function alle(): array
    {
        $adressen = [
            '/'           => '1.0',
            '/preise'     => '1.0',
            '/leistungen' => '0.9',
            '/ablauf'     => '0.8',
            '/briefing'   => '0.7',
            '/ueber-uns'  => '0.6',
            '/kontakt'    => '0.6',
        ];

        foreach (array_keys(Leistungsseiten::alle()) as $schluessel) {
            $adressen['/leistung-' . $schluessel] = '0.7';
        }

        // §10a: Branchenseiten sind vollständige Zielseiten. §16 führt sie nicht eigens auf —
        // sie stehen deshalb auf derselben Stufe wie die Leistungsseiten, denen sie
        // entsprechen.
        foreach (array_keys(Branchenseiten::alle()) as $schluessel) {
            $adressen['/website-' . $schluessel] = '0.7';
        }

        // §16: Transparenzseiten 0.9, Vergleichsartikel 0.7. Die Unterscheidung steht in
        // §11a und §12 — dort, wo die Artikel selbst stehen.
        $adressen['/ratgeber'] = '0.9';

        foreach (array_keys(Ratgeber::alle()) as $schluessel) {
            $adressen['/ratgeber/' . $schluessel] = in_array($schluessel, self::TRANSPARENZ, true)
                ? '0.9'
                : '0.7';
        }

        $adressen['/lexikon'] = '0.5';

        foreach (array_keys(Lexikon::alle()) as $schluessel) {
            $adressen['/lexikon/' . $schluessel] = '0.5';
        }

        $adressen['/impressum'] = '0.3';
        $adressen['/datenschutz'] = '0.3';

        return $adressen;
    }

    /** §11a — die drei Transparenzseiten. Sie tragen die höchste Priorität nach `/preise`. */
    public const TRANSPARENZ = [
        'was-kostet-eine-firmenwebsite',
        'was-nicht-enthalten-ist',
        'was-der-betrieb-kostet',
    ];
}
