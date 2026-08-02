<?php

declare(strict_types=1);

namespace Sartu;

use Sartu\Helpers\Env;

/**
 * Strukturierte Daten nach Website-Lastenheft §16.
 *
 * ## `Organization` ohne Adressfeld — und `LocalBusiness` gar nicht
 *
 * §0, wörtlich, solange `[GESCHAEFTSADRESSE_STATUS]` in `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1
 * auf `offen` steht:
 *
 * > „**Kein** `LocalBusiness` in strukturierten Daten. Stattdessen `Organization` **ohne**
 * > Adressfeld."
 *
 * §16 wiederholt es: „`LocalBusiness` wird erst ausgeliefert, wenn der Standort entschieden
 * und real ist." Es gibt hier deshalb keine Methode dafür — auch keine ungenutzte. Eine
 * Methode, die nur noch aufgerufen werden müsste, ist eine Zeile Arbeit vom Verstoß entfernt.
 *
 * ## Keine erfundenen Angaben
 *
 * `Organization` trägt Namen und Adresse der Website, sonst nichts. Keine Gründungszahl,
 * keine Bewertungen, keine Mitarbeiterzahl, kein Logo, das es nicht gibt. §17: „Keine
 * Fake-Referenzen, -Logos, -Bewertungen, -Adressen."
 *
 * ## `FAQPage` fehlt mit Grund
 *
 * §16: Google hat FAQ-Rich-Results eingestellt. Das Markup schadet nicht, bringt aber keine
 * Sichtbarkeit — und „wer `FAQPage` trotzdem ausliefert, darf es nicht als Maßnahme führen".
 * Es wird deshalb nicht ausgeliefert.
 */
final class Strukturdaten
{
    public static function organisationUndWebsite(): string
    {
        $basis = self::basis();

        return self::json([
            '@context' => 'https://schema.org',
            '@graph'   => [
                [
                    '@type' => 'Organization',
                    '@id'   => $basis . '/#organisation',
                    'name'  => 'SARTU',
                    'url'   => $basis . '/',
                ],
                [
                    '@type'     => 'WebSite',
                    '@id'       => $basis . '/#website',
                    'name'      => 'SARTU',
                    'url'       => $basis . '/',
                    'publisher' => ['@id' => $basis . '/#organisation'],
                    'inLanguage' => 'de-DE',
                ],
            ],
        ]);
    }

    /** @param list<array{0:string,1:string}> $krumen je [Adresse, Beschriftung] */
    public static function brotkrumen(array $krumen): string
    {
        $basis = self::basis();
        $punkte = [[
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => 'Start',
            'item'     => $basis . '/',
        ]];

        foreach ($krumen as $nummer => $krume) {
            $punkte[] = [
                '@type'    => 'ListItem',
                'position' => $nummer + 2,
                'name'     => $krume[1],
                'item'     => $basis . $krume[0],
            ];
        }

        return self::json([
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $punkte,
        ]);
    }

    /** §16: `Service` auf den Leistungs- und Preisseiten. */
    public static function dienstleistung(string $name, string $beschreibung, string $pfad): string
    {
        $basis = self::basis();

        return self::json([
            '@context'    => 'https://schema.org',
            '@type'       => 'Service',
            'name'        => $name,
            'description' => $beschreibung,
            'url'         => $basis . $pfad,
            'provider'    => ['@type' => 'Organization', 'name' => 'SARTU', 'url' => $basis . '/'],
            'areaServed'  => ['@type' => 'Country', 'name' => 'Deutschland'],
        ]);
    }

    /** §16: `Article` auf Ratgeber- und Transparenzseiten. */
    public static function artikel(string $titel, string $beschreibung, string $pfad, string $stand): string
    {
        $basis = self::basis();

        return self::json([
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => $titel,
            'description'   => $beschreibung,
            'url'           => $basis . $pfad,
            'dateModified'  => $stand,
            'inLanguage'    => 'de-DE',
            'author'        => ['@type' => 'Organization', 'name' => 'SARTU'],
            'publisher'     => ['@type' => 'Organization', 'name' => 'SARTU'],
        ]);
    }

    /** §16: `DefinedTerm` im Lexikon. */
    public static function begriff(string $begriff, string $definition, string $pfad): string
    {
        $basis = self::basis();

        return self::json([
            '@context'    => 'https://schema.org',
            '@type'       => 'DefinedTerm',
            'name'        => $begriff,
            'description' => $definition,
            'url'         => $basis . $pfad,
            'inDefinedTermSet' => [
                '@type' => 'DefinedTermSet',
                'name'  => 'Website-Lexikon',
                'url'   => $basis . '/lexikon',
            ],
        ]);
    }

    /**
     * Fügt zwei fertige Blöcke zusammen — §16 nennt je Seite bis zu zwei Typen.
     *
     * Zwei `<script>`-Zeilen wären auch zulässig; ein Graph ist die Form, die Google für
     * mehrere Typen auf einer Seite dokumentiert.
     */
    public static function verbinden(string ...$bloecke): string
    {
        $graph = [];

        foreach ($bloecke as $block) {
            $teil = json_decode($block, true);

            if (!is_array($teil)) {
                continue;
            }

            unset($teil['@context']);
            $graph[] = $teil;
        }

        return self::json(['@context' => 'https://schema.org', '@graph' => $graph]);
    }

    private static function basis(): string
    {
        return rtrim((string) Env::get('BASE_URL', ''), '/');
    }

    /**
     * @param array<string,mixed> $daten
     *
     * `JSON_HEX_TAG` und `JSON_HEX_AMP` sind hier kein Schmuck: Ohne sie beendet ein `</` in
     * einem Wert das `<script>`-Element vorzeitig, und der Rest landet als Markup auf der
     * Seite.
     */
    private static function json(array $daten): string
    {
        return (string) json_encode(
            $daten,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP,
        );
    }
}
