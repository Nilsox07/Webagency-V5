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
    /**
     * Wie viele Zeitschritte zurueck ein Code noch gilt.
     *
     * RFC 6238 §6 empfiehlt hoechstens einen. Ein Schritt sind dreissig Sekunden — genug
     * fuer eine Uhr, die etwas nachgeht, und wenig genug, dass ein abgefangener Code nicht
     * lange lebt.
     */
    private const SCHRITTE_ZURUECK = 1;

    public const PERIODE_SEKUNDEN = 30;

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
        return self::zeitschrittZumCode($geheimnis, $code) !== null;
    }

    /**
     * Prueft den Code und gibt den Zeitschritt zurueck, zu dem er **tatsaechlich** gehoert.
     *
     * Der Rueckgabewert ist der Punkt: Ohne ihn laesst sich ein Code nicht sauber
     * entwerten. Wer nur „gueltig ja/nein" weiss und dann den aktuellen Zeitschritt
     * abhakt, hat eine Luecke — ein Code aus dem vorigen Schritt wuerde unter dem
     * aktuellen vermerkt und liesse sich im naechsten erneut einloesen.
     *
     * Deshalb wird hier nicht mit einer Sekundenspanne gearbeitet, sondern Schritt fuer
     * Schritt verglichen. Der Vergleich laeuft ueber hash_equals.
     */
    public static function zeitschrittZumCode(string $geheimnis, string $code, ?int $jetzt = null): ?int
    {
        $code = preg_replace('/\s+/', '', $code) ?? '';

        if (preg_match('/^\d{6}$/', $code) !== 1) {
            return null;
        }

        $jetzt ??= time();
        $totp = TOTP::createFromSecret($geheimnis);

        for ($zurueck = 0; $zurueck <= self::SCHRITTE_ZURUECK; ++$zurueck) {
            $zeitpunkt = $jetzt - $zurueck * self::PERIODE_SEKUNDEN;

            if (hash_equals($totp->at($zeitpunkt), $code)) {
                return self::zeitschritt($zeitpunkt);
            }
        }

        return null;
    }

    /**
     * Der Zeitschritt zu einem Zeitpunkt.
     *
     * Welcher Schritt schon verbraucht ist, speichert der Aufrufer: Ein Feld dafuer gibt es
     * im Datenmodell (§4) nicht, und eines zu erfinden waere gegen die Regel.
     */
    public static function zeitschritt(?int $zeitpunkt = null): int
    {
        return intdiv($zeitpunkt ?? time(), self::PERIODE_SEKUNDEN);
    }
}
