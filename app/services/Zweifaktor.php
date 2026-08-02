<?php

declare(strict_types=1);

namespace Sartu\Services;

use OTPHP\TOTP;
use Sartu\Helpers\Env;

/**
 * TOTP fuer Adminkonten — Portal-Lastenheft §2 und §3 Regel 10.
 *
 * „Admin-2FA ist Pflicht, auch lokal nicht abschaltbar (im Entwicklungsmodus mit festem
 * Testschluessel, nicht deaktiviert)." Es gibt in dieser Klasse deshalb keinen Schalter,
 * der die Pruefung ueberspringt — auch keinen fuer APP_ENV=local.
 */
final class Zweifaktor
{
    /** Ein Schritt Toleranz in beide Richtungen: Uhren laufen auseinander. */
    private const FENSTER = 1;

    public static function geheimnisErzeugen(): string
    {
        return TOTP::generate()->getSecret();
    }

    public static function einrichtungsAdresse(string $geheimnis, string $konto): string
    {
        $totp = TOTP::createFromSecret($geheimnis);
        $totp->setLabel($konto);
        $totp->setIssuer(Env::get('ADMIN_TOTP_ISSUER', 'SARTU') ?? 'SARTU');

        return $totp->getProvisioningUri();
    }

    /** Der Wert, den man in eine Authenticator-App abtippt, in Vierergruppen. */
    public static function lesbaresGeheimnis(string $geheimnis): string
    {
        return trim(implode(' ', str_split($geheimnis, 4)));
    }

    public static function pruefen(string $geheimnis, string $code): bool
    {
        $code = preg_replace('/\s+/', '', $code) ?? '';

        if (preg_match('/^\d{6}$/', $code) !== 1) {
            return false;
        }

        return TOTP::createFromSecret($geheimnis)->verify($code, null, self::FENSTER);
    }
}
