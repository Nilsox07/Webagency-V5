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
     * **Wer die Sitzung startet, entscheidet der Router an der Route** — seit 16.08.2026.
     *
     * Hier stand bis dahin `OHNE_SITZUNG`: eine Negativliste aus drei Wurzeldateien und
     * `/api/`. Alles andere bekam eine Sitzung, auch jede Leseseite. Die Liste ist ersetzt
     * durch `Route::$sitzung` und `Router::brauchtSitzung()` — eine Positiventscheidung, bei
     * der eine neue Leseseite von sich aus cookiefrei ist.
     *
     * § 25 Abs. 2 Nr. 2 TDDDG ist der Grund: Die Speicherung auf dem Endgerät ist ohne
     * Einwilligung nur zulässig, soweit sie für einen ausdrücklich gewünschten Dienst
     * unbedingt erforderlich ist.
     */
    public static function starten(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        /*
         * **Auf der Befehlszeile gibt es niemanden, dem ein Cookie gehören könnte.**
         *
         * Der Testlauf ruft `Router::behandeln()` direkt auf und legt vorher an, was in der
         * Sitzung stehen soll — angemeldeter Benutzer, Zwischenstand des Bedarfsschecks,
         * CSRF-Token. `session_start()` **ersetzt** `$_SESSION` durch den Inhalt der
         * Sitzungsdatei und würde genau das verwerfen.
         *
         * Bis zum 16.08.2026 stellte sich die Frage nicht: Die Sitzung startete in
         * `public/index.php`, und die läuft im Test nicht. Seit der Start an der Route hängt,
         * läuft er auch im Test — deshalb diese Zeile.
         *
         * **Sie schwächt nichts ab.** Über den Webserver ist `PHP_SAPI` `cli-server`,
         * `apache2handler`, `fpm-fcgi` oder `cgi-fcgi` — nie `cli`. `AuslieferungTest` misst
         * die Cookies deshalb über HTTP und nicht über den Router.
         */
        if (PHP_SAPI === 'cli') {
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

        /*
         * **`use_strict_mode` gegen untergeschobene Sitzungskennungen** — gesetzt am
         * 16.08.2026.
         *
         * Ohne diese Zeile übernimmt PHP eine Kennung, die im Cookie steht, auch wenn es
         * dazu keine Sitzung gibt. Wer einem Opfer vorher `PHPSESSID=abc` unterschiebt,
         * kennt nach dessen Anmeldung eine gültige Kennung. `session_regenerate_id(true)`
         * bei der Anmeldung fängt den Regelfall ab; diese Zeile schliesst das Fenster davor.
         *
         * `php.ini` setzt den Wert seit PHP 7.1 nicht von sich aus — die Voreinstellung ist
         * `0`, und darauf darf sich der Code nicht verlassen.
         */
        ini_set('session.use_strict_mode', '1');

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
