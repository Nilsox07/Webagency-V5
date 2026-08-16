<?php

declare(strict_types=1);

namespace Sartu\Services;

/**
 * Herkunft einer Anfrage — Portal-Lastenheft §4b.7.
 *
 * ## Was sich am 16.08.2026 geändert hat — und warum
 *
 * Bis dahin schrieb `merken()` Landeseite, verweisenden Host, UTM-Werte und Klickkennung
 * beim **ersten** Seitenaufruf in die Sitzung. Das war fachlich das Richtige und
 * datenschutzrechtlich der teuerste Satz der ganzen Anwendung: Er war der **einzige** Grund,
 * warum jede öffentliche Leseseite ein Sitzungscookie setzen musste.
 *
 * § 25 Abs. 2 Nr. 2 TDDDG erlaubt die Speicherung auf dem Endgerät ohne Einwilligung nur,
 * soweit sie für einen vom Nutzer **ausdrücklich gewünschten Dienst** unbedingt erforderlich
 * ist. Eine Zuordnung, die SARTU nützt und dem Besucher nicht, ist keiner. Die Alternative
 * wäre eine Einwilligungslösung mit Ablehnen, Widerruf und Versionierung gewesen — für eine
 * Angabe, die niemand vermisst, der sie nicht bekommt.
 *
 * **Erfasst wird deshalb erst, was beim Start des Bedarfsschecks vorliegt.** Konkret:
 *
 * | Feld | vorher | jetzt |
 * |---|---|---|
 * | `landing_page` | die erste Seite des Besuchs | `/briefing` — der Einstieg in den Scheck |
 * | `referrer_host` | der Host, der auf die erste Seite verwies | der Host, der auf `/briefing` verwies. Bei einem Klick von der eigenen Startseite ist das die eigene Domain, und dann steht dort nichts |
 * | `utm_*`, `click_id` | von der ersten Seite | nur, wenn sie an `/briefing` hängen |
 *
 * **Was das kostet:** Wer mit `?utm_source=…` auf der Startseite landet und erst danach zum
 * Bedarfsscheck klickt, kommt ohne Kampagnenangabe an. Das ist der bewusst in Kauf genommene
 * Verlust. Die Gegenrechnung: `Bedarfsscheck::HERKUNFTSANGABEN` fragt im letzten Schritt
 * freiwillig „Wie sind Sie auf uns aufmerksam geworden?" — eine selbst berichtete Angabe,
 * die kein Cookie braucht und die der Besucher kennt.
 *
 * Will der Betreiber die seitenübergreifende Zuordnung zurück, ist das eine neue
 * Geschäftsentscheidung **mit** Einwilligungslösung, nicht eine Zeile Code.
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
     * Beim Start des Bedarfsschecks merken — danach nie wieder überschreiben.
     *
     * Die Bedingung bleibt: Ohne sie ersetzte jeder weitere Schritt die Herkunft, und am
     * Ende stünde dort `/briefing/5` statt des Einstiegs.
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
