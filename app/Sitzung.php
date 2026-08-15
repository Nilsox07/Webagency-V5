<?php

declare(strict_types=1);

namespace Sartu;

/**
 * Der Sitzungszustand der Anwendung.
 *
 * Die Werte hier sind die einzige Quelle fuer „wer bin ich" und „zu welcher Organisation
 * gehoere ich". Portal-Lastenheft §3 Regel 1: organization_id kommt aus der Sitzung,
 * niemals aus einem Request-Parameter, Formularfeld oder URL-Segment. Es gibt deshalb
 * bewusst keine Methode, die einen dieser Werte aus einer Anfrage uebernimmt.
 */
final class Sitzung
{
    public const BENUTZER          = 'benutzer_id';
    public const ROLLE             = 'rolle';
    public const ORGANISATION      = 'organization_id';
    public const TOTP_BESTAETIGT   = 'totp_bestaetigt_am';

    /**
     * Adressen, die **ohne** Sitzung ausgeliefert werden — gemessen am 15.08.2026.
     *
     * Bis dahin startete `public/index.php` die Sitzung vor der Route. Ergebnis: `robots.txt`
     * trug ein `PHPSESSID`-Cookie und `Cache-Control: no-store`. Ein Crawler bekommt damit
     * bei jedem Abruf ein Cookie gesetzt und darf die Datei nie zwischenspeichern.
     *
     * **Warum nur diese drei und nicht „nur Bedarfsscheck, Anmeldung, Portal und Admin".**
     * Portal-Lastenheft §4b.7 verlangt Landeseite, verweisenden Host und Kampagnenkennzeichen
     * aus der Adresse der **ersten** aufgerufenen Seite. `Herkunft::merken()` schreibt sie
     * beim ersten Aufruf in die Sitzung. Startete sie erst am Bedarfsscheck, stünde dort als
     * Landeseite `/briefing` — für jeden Besucher, und genau davor warnt der Kommentar an
     * `Herkunft`. Die Abweichung von der Anweisung steht in `OFFENE_PRUEFUNGEN.md`.
     *
     * Ausgenommen sind deshalb die Adressen, die **keine** Person abruft: die drei
     * Wurzeldateien für Suchdienste. Der Zahlungs-Webhook unter `/api/` steht ebenfalls
     * hier — ein fremder Server hat keine Sitzung und soll keine bekommen.
     *
     * @var list<string>
     */
    public const OHNE_SITZUNG = ['/sitemap.xml', '/robots.txt', '/llms.txt'];

    /** Ein Pfad unter diesem Präfix bekommt ebenfalls keine Sitzung. */
    public const OHNE_SITZUNG_PRAEFIX = '/api/';

    /** Braucht dieser Pfad eine Sitzung? */
    public static function wirdGebraucht(string $pfad): bool
    {
        return !in_array($pfad, self::OHNE_SITZUNG, true)
            && !str_starts_with($pfad, self::OHNE_SITZUNG_PRAEFIX);
    }

    public static function starten(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        /*
         * **PHP stempelt sonst `no-store` auf jede Antwort.** Der Vorgabewert von
         * `session.cache_limiter` ist `nocache`; `session_start()` setzt damit
         * `Expires: Thu, 19 Nov 1981`, `Cache-Control: no-store, no-cache,
         * must-revalidate` und `Pragma: no-cache` — auf **jede** Seite, auch auf die
         * öffentliche Startseite.
         *
         * Leer gesetzt schweigt PHP, und die Cachepolitik entscheidet der Router je
         * Bereich. Das ist keine Lockerung: Angemeldete Bereiche bekommen dort weiterhin
         * `no-store`, und zwar ausdrücklich statt als Nebenwirkung.
         */
        session_cache_limiter('');

        // §3 Regel 6. secure gilt ueberall ausser lokal — dort gibt es kein TLS,
        // und ein Cookie, das nie gesendet wird, macht die Anmeldung unbenutzbar.
        session_set_cookie_params([
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => !Helpers\Env::isLocal(),
            'path'     => '/',
        ]);

        session_start();
    }

    public static function wert(string $schluessel): ?string
    {
        $wert = $_SESSION[$schluessel] ?? null;

        return is_string($wert) && $wert !== '' ? $wert : null;
    }

    public static function anmelden(string $benutzerId, string $rolle, ?string $organisationId): void
    {
        // Neue Kennung bei jeder Anmeldung — sonst laesst sich eine vorher untergeschobene
        // Sitzungskennung nach dem Anmelden weiterverwenden.
        //
        // Die Abfrage auf eine laufende Sitzung ist keine Abschwaechung: Ueber den Webserver
        // ist immer eine aktiv (public/index.php startet sie als Erstes). Nur im Testlauf
        // ueber die Befehlszeile gibt es keine, und dort gibt es auch nichts zu uebernehmen.
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        $_SESSION[self::BENUTZER]     = $benutzerId;
        $_SESSION[self::ROLLE]        = $rolle;
        $_SESSION[self::ORGANISATION] = $organisationId;
    }

    public static function totpBestaetigen(): void
    {
        $_SESSION[self::TOTP_BESTAETIGT] = Data\Db::jetzt();
    }

    public static function abmelden(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function istAdmin(): bool
    {
        return self::wert(self::ROLLE) === 'admin';
    }

    /** §3 Regel 2a: Rolle allein genuegt nicht — die Zweifaktor-Anmeldung muss abgeschlossen sein. */
    public static function istAngemeldeterAdmin(): bool
    {
        return self::istAdmin()
            && self::wert(self::BENUTZER) !== null
            && self::wert(self::TOTP_BESTAETIGT) !== null;
    }
}
