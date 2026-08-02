<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\AnmeldeKonten;
use Sartu\Data\AuditProtokoll;
use Sartu\Data\SitzungsSpeicher;
use Sartu\Sitzung;

/**
 * Adminanmeldung — Portal-Lastenheft §2: E-Mail + Passwort (Argon2id) + TOTP.
 *
 * Zwei Schritte, und der zweite ist nicht optional. §3 Regel 10: „Admin-2FA ist Pflicht,
 * auch lokal nicht abschaltbar." Zwischen Schritt 1 und 2 ist der Benutzer NICHT angemeldet
 * — die Sitzung traegt nur einen Vormerk. AdminNachweis::ausSitzung() liefert bis zum
 * bestaetigten Code nichts (Testfall 44).
 *
 * §3 Regel 9: Anmeldung und fehlgeschlagene Anmeldung werden protokolliert. Nach aussen ist
 * die Meldung in beiden Fehlerfaellen dieselbe — welcher Teil nicht stimmte, geht niemanden
 * etwas an.
 */
final class AnmeldeDienst
{
    private const VORMERK = '_anmeldung_benutzer';

    public const SITZUNGSTOKEN = 'sitzung_token';

    /** §3 Regel 4 sinngemaess: die Anmeldung ist keine Probierflaeche. */
    private const VERSUCHE_JE_KONTO = 5;

    private const VERSUCHE_JE_IP = 20;

    private const FENSTER_SEKUNDEN = 3600;

    /**
     * Ein gueltiger Argon2id-Hash, der zu keinem Passwort passt. password_verify() rechnet
     * dagegen, wenn es das Konto nicht gibt — sonst verriete die Laufzeit, welche Adressen
     * existieren.
     */
    private const BLINDHASH = '$argon2id$v=19$m=65536,t=4,p=1$dHZpdmdSUlpVTzNQNjk4TA$'
        . '7ZYQrYyZPLnZljYUCwF+Y1DWxlD0mc50Q5wckapgsvs';

    public function __construct(
        private readonly ?AnmeldeKonten $konten = null,
        private readonly ?AuditProtokoll $audit = null,
        private readonly ?SitzungsSpeicher $sitzungen = null,
        private readonly ?Ratenbegrenzung $begrenzung = null,
        private readonly ?Verschluesselung $verschluesselung = null,
    ) {
    }

    public function gesperrt(string $email, ?string $ip): bool
    {
        $begrenzung = $this->begrenzung();

        return !$begrenzung->erlaubt($this->kontoSchluessel($email), self::VERSUCHE_JE_KONTO, self::FENSTER_SEKUNDEN)
            || !$begrenzung->erlaubt('anmeldung-ip:' . (string) $ip, self::VERSUCHE_JE_IP, self::FENSTER_SEKUNDEN);
    }

    /** Schritt 1. Bei Erfolg steht ein Vormerk in der Sitzung — noch keine Anmeldung. */
    public function passwortPruefen(string $email, string $passwort, ?string $ip): bool
    {
        $this->begrenzung()->vermerken($this->kontoSchluessel($email), self::FENSTER_SEKUNDEN);
        $this->begrenzung()->vermerken('anmeldung-ip:' . (string) $ip, self::FENSTER_SEKUNDEN);

        $konto = $this->konten()->adminNachEmail($email);
        $hash = is_array($konto) && is_string($konto['password_hash'] ?? null) ? $konto['password_hash'] : null;

        $stimmt = password_verify($passwort, $hash ?? self::BLINDHASH);

        if (!is_array($konto) || $hash === null || !$stimmt) {
            $this->auditProtokoll()->schreiben(
                aktion: 'anmeldung_fehlgeschlagen',
                objektart: 'users',
                objektId: is_array($konto) ? (string) $konto['id'] : null,
                detail: ['schritt' => 'passwort'],
                ip: $ip,
            );

            return false;
        }

        $_SESSION[self::VORMERK] = (string) $konto['id'];

        return true;
    }

    public function vorgemerkterBenutzer(): ?string
    {
        $wert = $_SESSION[self::VORMERK] ?? null;

        return is_string($wert) && $wert !== '' ? $wert : null;
    }

    /**
     * Schritt 2. Erst hier entsteht eine Anmeldung.
     *
     * @return string|null das Sitzungstoken bei Erfolg
     */
    public function codePruefen(string $code, ?string $ip, ?string $benutzerkennung): ?string
    {
        $benutzerId = $this->vorgemerkterBenutzer();

        if ($benutzerId === null) {
            return null;
        }

        $konto = $this->konten()->adminNachId($benutzerId);
        $geheimnis = is_array($konto) ? ($konto['totp_secret_enc'] ?? null) : null;

        if (!is_array($konto) || !is_string($geheimnis) || $geheimnis === '') {
            return null;
        }

        if (!Zweifaktor::pruefen($this->verschluesselung()->entschluesseln($geheimnis), $code)) {
            $this->auditProtokoll()->schreiben(
                aktion: 'anmeldung_fehlgeschlagen',
                objektart: 'users',
                objektId: $benutzerId,
                detail: ['schritt' => 'zweifaktor'],
                ip: $ip,
            );

            return null;
        }

        unset($_SESSION[self::VORMERK]);

        $sitzung = $this->sitzungsSpeicher()->anlegen($benutzerId, $benutzerkennung, $ip);

        // Ein Admin hat bewusst keine organization_id (§3 Regel 2a).
        Sitzung::anmelden($benutzerId, 'admin', null);
        Sitzung::totpBestaetigen();
        $_SESSION[self::SITZUNGSTOKEN] = $sitzung['token'];

        $this->konten()->anmeldungVermerken($benutzerId);

        $this->auditProtokoll()->schreiben(
            aktion: 'anmeldung',
            objektart: 'users',
            objektId: $benutzerId,
            akteurBenutzerId: $benutzerId,
            ip: $ip,
        );

        $this->begrenzung()->zuruecksetzen($this->kontoSchluessel((string) $konto['email']));

        return $sitzung['token'];
    }

    public function abmelden(?string $ip): void
    {
        $token = $_SESSION[self::SITZUNGSTOKEN] ?? null;
        $benutzerId = Sitzung::wert(Sitzung::BENUTZER);

        if (is_string($token) && $token !== '') {
            $this->sitzungsSpeicher()->loeschen($token);
        }

        if ($benutzerId !== null) {
            $this->auditProtokoll()->schreiben(
                aktion: 'abmeldung',
                objektart: 'users',
                objektId: $benutzerId,
                akteurBenutzerId: $benutzerId,
                ip: $ip,
            );
        }

        Sitzung::abmelden();
    }

    private function kontoSchluessel(string $email): string
    {
        return 'anmeldung:' . mb_strtolower(trim($email));
    }

    private function konten(): AnmeldeKonten
    {
        return $this->konten ?? new AnmeldeKonten();
    }

    private function auditProtokoll(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll();
    }

    private function sitzungsSpeicher(): SitzungsSpeicher
    {
        return $this->sitzungen ?? new SitzungsSpeicher();
    }

    private function begrenzung(): Ratenbegrenzung
    {
        return $this->begrenzung ?? new Ratenbegrenzung();
    }

    private function verschluesselung(): Verschluesselung
    {
        return $this->verschluesselung ?? new Verschluesselung();
    }
}
