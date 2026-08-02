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

    public static function starten(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

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
        session_regenerate_id(true);

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
