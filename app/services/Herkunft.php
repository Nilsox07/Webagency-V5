<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Herkunft einer Anfrage — Portal-Lastenheft §4b.7.
 *
 * **Der Punkt, an dem es sonst schiefgeht:** Die Kennzeichen stehen in der Adresse der
 * **ersten** aufgerufenen Seite. Bis der Bedarfsscheck abgeschickt wird, sind sie längst
 * weg. Sie werden deshalb beim ersten Seitenaufruf in die Sitzung geschrieben und erst beim
 * Anlegen des `lead` übernommen (Testfall 40a).
 *
 * **Datensparsam, first-party:** `landing_page` nur der Pfad, `referrer_host` nur der
 * Hostname. Eine vollständige Adresse kann Suchbegriffe oder Kennungen enthalten — die
 * gehören SARTU nicht (Testfall 40b).
 */
final class Herkunft
{
    private const SCHLUESSEL = '_herkunft';

    private const UTM_FELDER = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];

    /** Die drei Klickkennungen, die Google vergibt. Wert **und** Art werden gespeichert. */
    private const KLICK_FELDER = ['gclid', 'gbraid', 'wbraid'];

    private const MAX_LAENGE = 100;

    /**
     * Beim ERSTEN Seitenaufruf merken — danach nie wieder überschreiben.
     *
     * Ohne diese Bedingung würde jeder weitere Aufruf die Herkunft ersetzen, und am Ende
     * stünde dort die letzte Seite vor dem Absenden statt der ersten.
     *
     * @param array<string,mixed> $abfrage  $_GET
     * @param array<string,mixed> $server   $_SERVER
     */
    public static function merken(array $abfrage, array $server): void
    {
        if (isset($_SESSION[self::SCHLUESSEL])) {
            return;
        }

        $werte = [
            'landing_page'  => self::nurPfad(is_string($server['REQUEST_URI'] ?? null) ? $server['REQUEST_URI'] : ''),
            'referrer_host' => self::nurHost(is_string($server['HTTP_REFERER'] ?? null) ? $server['HTTP_REFERER'] : ''),
            'click_id'      => null,
        ];

        foreach (self::UTM_FELDER as $feld) {
            $wert = $abfrage[$feld] ?? null;
            $werte[$feld] = is_string($wert) && trim($wert) !== ''
                ? mb_substr(trim($wert), 0, self::MAX_LAENGE)
                : null;
        }

        foreach (self::KLICK_FELDER as $art) {
            $wert = $abfrage[$art] ?? null;

            if (is_string($wert) && trim($wert) !== '') {
                // Wert UND Art — sonst lässt sich später nicht sagen, aus welchem Kanal die
                // Kennung stammt.
                $werte['click_id'] = $art . ':' . mb_substr(trim($wert), 0, self::MAX_LAENGE);
                break;
            }
        }

        $_SESSION[self::SCHLUESSEL] = $werte;
    }

    /** @return array<string,string|null> */
    public static function ausSitzung(): array
    {
        $werte = $_SESSION[self::SCHLUESSEL] ?? null;

        if (!is_array($werte)) {
            return self::leer();
        }

        return array_merge(self::leer(), array_map(
            static fn ($w) => is_string($w) && $w !== '' ? $w : null,
            $werte,
        ));
    }

    /** @return array<string,null> */
    private static function leer(): array
    {
        return [
            'landing_page'  => null,
            'referrer_host' => null,
            'utm_source'    => null,
            'utm_medium'    => null,
            'utm_campaign'  => null,
            'utm_term'      => null,
            'utm_content'   => null,
            'click_id'      => null,
        ];
    }

    /** Nur der Pfad — ohne Abfragezeichenfolge, die Suchbegriffe enthalten kann. */
    private static function nurPfad(string $adresse): ?string
    {
        if ($adresse === '') {
            return null;
        }

        $pfad = parse_url($adresse, PHP_URL_PATH);

        return is_string($pfad) && $pfad !== '' ? mb_substr($pfad, 0, 255) : null;
    }

    /** Nur der Hostname — nie die vollständige verweisende Adresse. */
    private static function nurHost(string $adresse): ?string
    {
        if ($adresse === '') {
            return null;
        }

        $host = parse_url($adresse, PHP_URL_HOST);

        return is_string($host) && $host !== '' ? mb_strtolower(mb_substr($host, 0, 255)) : null;
    }
}
