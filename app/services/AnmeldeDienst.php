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

    private const VORMERK_ZEIT = '_anmeldung_seit';

    /**
     * Wie lange der Vormerk zwischen Passwort und Code gilt.
     *
     * Ohne Frist bleibt er fuer die Lebensdauer der Sitzung stehen. Wer einmal das richtige
     * Passwort hatte, koennte danach beliebig lange Codes probieren.
     */
    private const VORMERK_SEKUNDEN = 300;

    public const SITZUNGSTOKEN = 'sitzung_token';

    /** §3 Regel 4 sinngemaess: die Anmeldung ist keine Probierflaeche. */
    private const VERSUCHE_JE_KONTO = 5;

    private const VERSUCHE_JE_IP = 20;

    /**
     * Versuche fuer den zweiten Faktor.
     *
     * Ohne eigenen Zaehler ist der zweite Faktor keiner: Der Zaehler aus Schritt 1 schlaegt
     * einmal an, und danach laesst sich der sechsstellige Code beliebig oft probieren.
     * §3 Regel 10 verlangt aber, dass die Zweifaktor-Anmeldung Pflicht ist — eine Pflicht,
     * die sich durchprobieren laesst, ist keine.
     */
    private const VERSUCHE_ZWEITER_FAKTOR = 5;

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
        private readonly ?VerbrauchteCodes $verbrauchteCodes = null,
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
        $_SESSION[self::VORMERK_ZEIT] = time();

        return true;
    }

    public function vorgemerkterBenutzer(): ?string
    {
        $wert = $_SESSION[self::VORMERK] ?? null;
        $seit = $_SESSION[self::VORMERK_ZEIT] ?? null;

        if (!is_string($wert) || $wert === '' || !is_int($seit)) {
            return null;
        }

        if (time() - $seit > self::VORMERK_SEKUNDEN) {
            $this->vormerkVerwerfen();

            return null;
        }

        return $wert;
    }

    private function vormerkVerwerfen(): void
    {
        unset($_SESSION[self::VORMERK], $_SESSION[self::VORMERK_ZEIT]);
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

        // Eigener Zaehler fuer den zweiten Faktor. Der aus Schritt 1 hilft hier nicht: Er
        // schlaegt beim Passwort an, und der Code kaeme danach ungezaehlt durch.
        $schluessel = 'zweifaktor:' . $benutzerId;

        if (!$this->begrenzung()->erlaubt($schluessel, self::VERSUCHE_ZWEITER_FAKTOR, self::FENSTER_SEKUNDEN)) {
            $this->vormerkVerwerfen();

            return null;
        }

        $this->begrenzung()->vermerken($schluessel, self::FENSTER_SEKUNDEN);

        $konto = $this->konten()->adminNachId($benutzerId);
        $geheimnis = is_array($konto) ? ($konto['totp_secret_enc'] ?? null) : null;

        if (!is_array($konto) || !is_string($geheimnis) || $geheimnis === '') {
            return null;
        }

        $zeitschritt = Zweifaktor::zeitschrittZumCode(
            $this->verschluesselung()->entschluesseln($geheimnis),
            $code
        );

        if ($zeitschritt === null) {
            $this->auditProtokoll()->schreiben(
                aktion: 'anmeldung_fehlgeschlagen',
                objektart: 'users',
                objektId: $benutzerId,
                detail: ['schritt' => 'zweifaktor'],
                ip: $ip,
            );

            return null;
        }

        // RFC 6238 §5.2: Ein angenommener Code gilt kein zweites Mal. Sonst laesst sich ein
        // mitgelesener Code innerhalb seiner dreissig Sekunden erneut einloesen.
        // Entwertet wird der Zeitschritt, zu dem der Code WIRKLICH gehoert — nicht der
        // gerade laufende. Sonst liesse sich ein Code aus dem vorigen Schritt im naechsten
        // ein zweites Mal einloesen.
        if (!$this->verbrauchteCodes()->einloesen($benutzerId, $zeitschritt)) {
            $this->auditProtokoll()->schreiben(
                aktion: 'anmeldung_fehlgeschlagen',
                objektart: 'users',
                objektId: $benutzerId,
                detail: ['schritt' => 'zweifaktor', 'grund' => 'code_bereits_verwendet'],
                ip: $ip,
            );

            return null;
        }

        $this->vormerkVerwerfen();

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
        $this->begrenzung()->zuruecksetzen($schluessel);

        return $sitzung['token'];
    }

    /**
     * Gilt die Anmeldung noch — serverseitig?
     *
     * §3 Regel 6 verlangt Sitzungen, die serverseitig gespeichert sind und bei der Abmeldung
     * serverseitig geloescht werden. Das ist nur dann mehr als ein Eintrag in einer Tabelle,
     * wenn ihn auch jemand liest: Ohne diese Pruefung waere eine Anmeldung nicht
     * zurueckziehbar, solange das PHP-Cookie gilt.
     */
    public function sitzungGueltig(): bool
    {
        $token = $_SESSION[self::SITZUNGSTOKEN] ?? null;

        if (!is_string($token) || $token === '') {
            return false;
        }

        $eintrag = $this->sitzungsSpeicher()->finden($token);

        if ($eintrag === null) {
            return false;
        }

        return (string) $eintrag['user_id'] === (string) Sitzung::wert(Sitzung::BENUTZER);
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

    private function verbrauchteCodes(): VerbrauchteCodes
    {
        return $this->verbrauchteCodes ?? new VerbrauchteCodes();
    }
}
