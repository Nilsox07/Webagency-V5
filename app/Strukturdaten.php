<?php

declare(strict_types=1);

namespace Sartu;

use Sartu\Helpers\Env;
use Sartu\Services\Gruenderangaben;
use Sartu\Services\Startsperre;

/**
 * Strukturierte Daten nach Website-Lastenheft §16.
 *
 * ## Die Sperre liegt an den Daten, nicht an einer fehlenden Methode
 *
 * **Bis zum 13.08.2026 stand hier das Gegenteil.** Der Kommentar begründete, warum es keine
 * `LocalBusiness`-Methode gibt — „eine Methode, die nur noch aufgerufen werden müsste, ist
 * eine Zeile Arbeit vom Verstoß entfernt". Die Vorgabe dahinter war richtig gelesen und die
 * Umsetzung trotzdem falsch:
 *
 * `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4c (Rang 1, 10.08.2026): *„Das ist übervorsichtig und
 * kostet mehr, als es schützt. Die Folge war: Der Betreiber trägt die Adresse ein, und
 * trotzdem passiert nichts, weil es die Ausgabe nicht gibt."*
 *
 * **Was §0 und §16 verbieten, ist die erfundene Adresse — nicht die Ausgabe einer echten.**
 * Solange `operator_settings` keine Anschrift trägt, liefert diese Klasse weiter
 * `Organization` **ohne** Adressfeld. Steht sie da, wird `LocalBusiness` daraus. Die Sperre
 * ist damit dieselbe wie vorher, sie hängt nur an einem anderen Ort — und der Betreiber
 * kann sie erreichen.
 *
 * ## Keine erfundenen Angaben — unverändert
 *
 * Keine Gründungszahl, keine Bewertungen, keine Mitarbeiterzahl. §17: „Keine
 * Fake-Referenzen, -Logos, -Bewertungen, -Adressen." **Jedes Feld hier hat eine Quelle:**
 * die Betreiberdaten oder eine Datei, die ausgeliefert ist. Was fehlt, fehlt in der Ausgabe.
 *
 * ## `FAQPage` fehlt mit Grund
 *
 * §16: Google hat FAQ-Rich-Results eingestellt. Das Markup schadet nicht, bringt aber keine
 * Sichtbarkeit — und „wer `FAQPage` trotzdem ausliefert, darf es nicht als Maßnahme führen".
 * Es wird deshalb nicht ausgeliefert.
 */
final class Strukturdaten
{
    /**
     * Die ausgelieferte Wortmarke. `Startsperre::logoAusgeliefert()` prüft dieselbe Datei —
     * ausgegeben wird sie nur, wenn sie wirklich liegt (§17, „keine Fake-Logos").
     */
    private const LOGO = '/assets/bild/sartu-logo-hell.svg';

    /**
     * @param array<string,mixed>|null $betrieb die eine Zeile aus `operator_settings`
     */
    public static function organisationUndWebsite(?array $betrieb = null): string
    {
        $basis = self::basis();

        return self::json([
            '@context' => 'https://schema.org',
            '@graph'   => [
                self::organisation($betrieb),
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

    /**
     * Der Betreiberknoten — `Organization`, oder `LocalBusiness`, sobald eine Anschrift da ist.
     *
     * **`LocalBusiness` ist eine Unterart von `Organization`.** Der Knoten behält deshalb
     * seine `@id`; alles, was auf `#organisation` zeigt, zeigt weiter richtig.
     *
     * @param array<string,mixed>|null $betrieb
     * @return array<string,mixed>
     */
    private static function organisation(?array $betrieb): array
    {
        $basis = self::basis();

        $knoten = [
            '@type' => 'Organization',
            '@id'   => $basis . '/#organisation',
            'name'  => 'SARTU',
            'url'   => $basis . '/',
        ];

        if (Startsperre::logoAusgeliefert()) {
            $knoten['logo'] = $basis . self::LOGO;
        }

        if ($betrieb === null) {
            return $knoten;
        }

        $strasse = self::wert($betrieb, 'strasse');
        $plz     = self::wert($betrieb, 'plz');
        $ort     = self::wert($betrieb, 'ort');

        // §4c: alle drei oder keines. Eine Anschrift ohne Ort ist keine Anschrift, und ein
        // halbes `PostalAddress` ist schlechter als gar keines — es sieht vollständig aus.
        if ($strasse !== null && $plz !== null && $ort !== null) {
            $knoten['@type'] = 'LocalBusiness';
            $knoten['address'] = [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $strasse,
                'postalCode'      => $plz,
                'addressLocality' => $ort,
                'addressCountry'  => self::wert($betrieb, 'land') ?? 'DE',
            ];
        }

        $telefon = self::wert($betrieb, 'telefon');

        if ($telefon !== null) {
            $knoten['telephone'] = $telefon;
        }

        $email = self::wert($betrieb, 'email');

        if ($email !== null) {
            $knoten['email'] = $email;
        }

        $gruender = self::wert($betrieb, 'gruender_name');

        if ($gruender !== null) {
            $person = ['@type' => 'Person', 'name' => $gruender];

            // Das Bild nur, wenn es wirklich hinterlegt ist — die Adresse antwortet sonst
            // mit 404, und ein Verweis ins Leere ist eine Angabe, die nicht stimmt.
            if (self::wert($betrieb, 'gruender_bild') !== null) {
                $person['image'] = $basis . Gruenderangaben::BILD_PFAD;
            }

            $knoten['founder'] = $person;
        }

        $profile = self::profile($betrieb);

        if ($profile !== []) {
            $knoten['sameAs'] = $profile;
        }

        return $knoten;
    }

    /**
     * Die Profiladressen aus `profil_adressen`, eine je Zeile.
     *
     * Geprüft wird hier ein zweites Mal, obwohl der Schreibweg dasselbe prüft: Der Wert kann
     * älter sein als die Prüfung, und `sameAs` mit einer Zeile, die keine Adresse ist, macht
     * den ganzen Block ungültig — nicht nur die Zeile.
     *
     * @param array<string,mixed> $betrieb
     * @return list<string>
     */
    private static function profile(array $betrieb): array
    {
        $roh = self::wert($betrieb, 'profil_adressen');

        if ($roh === null) {
            return [];
        }

        $adressen = [];

        foreach (preg_split('/\R+/', $roh) ?: [] as $zeile) {
            $zeile = trim((string) $zeile);

            if ($zeile !== '' && str_starts_with($zeile, 'https://') && filter_var($zeile, FILTER_VALIDATE_URL) !== false) {
                $adressen[] = $zeile;
            }
        }

        return $adressen;
    }

    /** @param array<string,mixed> $daten */
    private static function wert(array $daten, string $feld): ?string
    {
        $wert = $daten[$feld] ?? null;

        return is_string($wert) && trim($wert) !== '' ? trim($wert) : null;
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

    /**
     * §16: `Article` auf Ratgeber- und Transparenzseiten.
     *
     * ## Drei Angaben sind am 13.08.2026 dazugekommen
     *
     * | Feld | Warum es gefehlt hat und warum es jetzt dasteht |
     * |---|---|
     * | `datePublished` | Ohne sie steht neben `dateModified` nichts, wogegen sie sich messen liesse. Ein geaenderter Artikel ohne Erstdatum sieht aus wie ein neuer |
     * | `author` als **Person** | Stand da, aber als `Organization` — dieselbe Angabe wie `publisher`, also keine. §16 und die Bewertung vom 10.08.2026 zielen auf die **benennbare Person**; §4c liefert das Feld dafuer |
     * | `image` | Ein `Article` ohne Bild kann in keiner Bilddarstellung erscheinen |
     *
     * **Alle drei sind datengesteuert.** Steht kein Gruendername in den Betreiberdaten,
     * bleibt `author` die Organisation — das ist wahr und nicht erfunden. Liegt kein
     * Gruenderbild, entfaellt `image`; es gibt kein Ersatzbild.
     *
     * @param array<string,mixed>|null $betrieb die eine Zeile aus `operator_settings`
     */
    public static function artikel(
        string $titel,
        string $beschreibung,
        string $pfad,
        string $stand,
        ?string $veroeffentlicht = null,
        ?array $betrieb = null,
    ): string {
        $basis = self::basis();

        $block = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => $titel,
            'description'   => $beschreibung,
            'url'           => $basis . $pfad,
            'datePublished' => $veroeffentlicht ?? $stand,
            'dateModified'  => $stand,
            'inLanguage'    => 'de-DE',
            'author'        => self::autor($betrieb),
            'publisher'     => ['@id' => $basis . '/#organisation'],
        ];

        $bild = self::artikelbild($betrieb);

        if ($bild !== null) {
            $block['image'] = $bild;
        }

        return self::json($block);
    }

    /**
     * @param array<string,mixed>|null $betrieb
     * @return array<string,mixed>
     */
    private static function autor(?array $betrieb): array
    {
        $name = $betrieb === null ? null : self::wert($betrieb, 'gruender_name');

        if ($name === null) {
            return ['@type' => 'Organization', 'name' => 'SARTU'];
        }

        $person = ['@type' => 'Person', 'name' => $name];

        if (self::wert($betrieb ?? [], 'gruender_bild') !== null) {
            $person['image'] = self::basis() . Gruenderangaben::BILD_PFAD;
        }

        return $person;
    }

    /**
     * Das Bild zum Artikel.
     *
     * **Es gibt keins je Artikel** — die Ratgeberseiten tragen bewusst kein Bild („Preise als
     * Text, nie als Bild"). Was es gibt, ist das Gruenderbild und die ausgelieferte
     * Wortmarke. Beides ist wahr; erfunden ist keines davon.
     *
     * @param array<string,mixed>|null $betrieb
     */
    private static function artikelbild(?array $betrieb): ?string
    {
        if ($betrieb !== null && self::wert($betrieb, 'gruender_bild') !== null) {
            return self::basis() . Gruenderangaben::BILD_PFAD;
        }

        return Startsperre::logoAusgeliefert() ? self::basis() . self::LOGO : null;
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
