<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\AnmeldeKonten;
use Sartu\Data\AnmeldeTokenSpeicher;
use Sartu\Data\AuditProtokoll;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\SitzungsSpeicher;
use Sartu\Helpers\Env;
use Sartu\Helpers\Validate;
use Sartu\Sitzung;

/**
 * Anmeldung ohne Passwort — Portal-Lastenheft §6.
 *
 * ## Die drei Stellen, an denen so etwas üblicherweise leckt
 *
 * **1 — Kontoauskunft.** §6.1 Punkt 3: „**Immer dieselbe Bestätigungsseite anzeigen**,
 * unabhängig davon, ob die E-Mail existiert." `linkAnfordern()` gibt deshalb bei jeder
 * Eingabe dasselbe zurück. Auch die Laufzeit verrät nichts Brauchbares: Der Unterschied
 * zwischen „Mail verschickt" und „nichts getan" liegt beim SMTP-Versand, und der ist so
 * schwankend, dass sich daraus nichts ablesen lässt.
 *
 * **2 — Der Token im Klartext.** Gespeichert wird nur der Hash (§3 Regel 5). Das Klartext-
 * token verlässt `AnmeldeTokenSpeicher::anlegen()` genau einmal, geht in die Mail und wird
 * nirgends protokolliert — auch nicht im Audit.
 *
 * **3 — Der Admin, der sich als Kunde anmeldet.** `benutzerNachEmail()` liefert
 * ausschliesslich Zeilen mit `role = 'kunde'`. Ein Admin bekommt hier keinen Link, und ein
 * Admintoken gibt es nicht: Der Adminweg läuft über Passwort und zweiten Faktor (§3 Regel 4).
 *
 * ## Der Notweg ist keine Zierde (§6.3)
 *
 * Der Anmeldelink ist der einzige Weg ins Portal. Kommt die Mail nicht an, ist der Kunde
 * ausgesperrt — und kann es niemandem melden, weil der Meldeweg selbst im Portal liegt.
 * Deshalb liefert `notweg()` Telefonnummer und Adresse aus den Betreiberdaten, und die
 * Oberfläche zeigt sie auf `/login`, auf der Bestätigungsseite und in jeder Anmeldemail.
 * **Nie aus dem Quelltext** (Testfall 83).
 *
 * ## Ein Wort weicht vom Lastenheft ab
 *
 * §10 nennt den Betreff `Ihr Anmeldelink für das SARTU-Portal`. Nach außen heißt der Bereich
 * **Kundenbereich** — so steht es in `CLAUDE.md` und in Website-Lastenheft §5b als
 * Navigationspunkt. „Portal" ist internes Vokabular. Der Betreff lautet deshalb
 * `Ihr Anmeldelink für Ihren Kundenbereich`; alles Übrige an der Zeile bleibt.
 */
final class KundenAnmeldung
{
    /** §3 Regel 4: dieselbe Begrenzung wie bei der Adminanmeldung. */
    public const VERSUCHE_JE_ADRESSE = 5;

    public const VERSUCHE_JE_IP = 10;

    private const FENSTER_SEKUNDEN = 3600;

    public function __construct(
        private readonly ?AnmeldeTokenSpeicher $tokens = null,
        private readonly ?Mailversand $mail = null,
        private readonly ?Ratenbegrenzung $begrenzung = null,
        private readonly ?AuditProtokoll $audit = null,
        private readonly ?AnmeldeKonten $konten = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * Fordert einen Anmeldelink an.
     *
     * @return bool `false` **nur** bei erreichter Begrenzung — das ist der einzige Fall, in
     *              dem §6.2 einen abweichenden Text vorsieht. Alles andere sieht gleich aus.
     */
    public function linkAnfordern(string $email, ?string $ip): bool
    {
        $email = mb_strtolower(trim($email));

        if (!$this->begrenzung()->erlaubt('login-ip:' . (string) $ip, self::VERSUCHE_JE_IP, self::FENSTER_SEKUNDEN)
            || !$this->begrenzung()->erlaubt('login-adresse:' . $email, self::VERSUCHE_JE_ADRESSE, self::FENSTER_SEKUNDEN)) {
            return false;
        }

        $this->begrenzung()->vermerken('login-ip:' . (string) $ip, self::FENSTER_SEKUNDEN);
        $this->begrenzung()->vermerken('login-adresse:' . $email, self::FENSTER_SEKUNDEN);

        $benutzer = Validate::email($email) ? $this->konten()->kundeNachEmail($email) : null;

        if ($benutzer === null) {
            // Kein Konto: Es passiert nichts, und der Aufrufer erfährt es nicht.
            return true;
        }

        $token = $this->tokens()->anlegen((string) $benutzer['id'], $ip);

        try {
            $this->mail()->senden(
                (string) $benutzer['email'],
                'Ihr Anmeldelink für Ihren Kundenbereich',
                $this->anmeldemail($benutzer, $token),
            );
        } catch (MailversandFehler) {
            // Der Versand ist gescheitert. Der Absender erfährt es trotzdem nicht — eine
            // abweichende Antwort wäre genau die Kontoauskunft, die §6.1 ausschliesst.
            // Der Notweg auf der Bestätigungsseite ist für diesen Fall da.
        }

        $this->audit()->schreiben(
            aktion: 'anmeldelink_gesendet',
            objektart: 'user',
            objektId: (string) $benutzer['id'],
            organisationId: $benutzer['organization_id'] === null ? null : (string) $benutzer['organization_id'],
            ip: $ip,
        );

        return true;
    }

    /**
     * Löst einen Link ein und meldet an.
     *
     * @return bool `false` bei ungültigem, abgelaufenem oder bereits benutztem Token.
     */
    public function einloesen(string $token, ?string $ip, string $benutzerkennung): bool
    {
        $benutzerId = $this->tokens()->einloesen($token);

        if ($benutzerId === null) {
            return false;
        }

        $benutzer = $this->konten()->kundeNachId($benutzerId);

        // Ein gültiger Token für einen inzwischen archivierten oder zum Admin gemachten
        // Benutzer meldet niemanden an.
        if ($benutzer === null) {
            return false;
        }

        Sitzung::anmelden(
            (string) $benutzer['id'],
            'kunde',
            (string) $benutzer['organization_id'],
        );

        $sitzung = (new SitzungsSpeicher($this->pdo))->anlegen((string) $benutzer['id'], $benutzerkennung, $ip);
        $_SESSION[AnmeldeDienst::SITZUNGSTOKEN] = $sitzung['token'];

        $this->konten()->anmeldungVermerken((string) $benutzer['id']);

        $this->audit()->schreiben(
            aktion: 'anmeldung_erfolgreich',
            objektart: 'user',
            objektId: (string) $benutzer['id'],
            organisationId: (string) $benutzer['organization_id'],
            ip: $ip,
        );

        return true;
    }

    /**
     * Der Notweg aus §6.3 — Telefonnummer und Adresse des Betreibers.
     *
     * Testfall 83: „Ist dort keine Telefonnummer gesetzt, erscheint die E-Mail-Adresse —
     * **nie** ein Wert aus dem Quelltext." Fehlt beides, kommt `null`, und die Oberfläche
     * zeigt gar nichts. Eine erfundene Nummer wäre schlimmer als keine.
     *
     * @return array{telefon:?string,email:?string}
     */
    public function notweg(): array
    {
        try {
            $daten = (new BetreiberdatenSpeicher($this->pdo))->lesen();
        } catch (\Throwable) {
            return ['telefon' => null, 'email' => null];
        }

        return [
            'telefon' => self::wertOderNull($daten['telefon'] ?? null),
            'email'   => self::wertOderNull($daten['email'] ?? null),
        ];
    }

    /** Der erste Login führt in die Willkommensstrecke (§6.1 Punkt 5, §7). */
    public function ersterLogin(string $benutzerId): bool
    {
        return $this->konten()->willkommenOffen($benutzerId);
    }

    public function willkommenGesehen(string $benutzerId): void
    {
        $this->konten()->willkommenVermerken($benutzerId);
    }

    /** @param array<string,mixed> $benutzer */
    private function anmeldemail(array $benutzer, string $token): string
    {
        $notweg = $this->notweg();
        $anrede = self::wertOderNull($benutzer['first_name'] ?? null);

        $text = ($anrede === null ? "Guten Tag,\n\n" : 'Guten Tag ' . $anrede . ",\n\n")
            . "hier ist Ihr Anmeldelink. Er gilt 15 Minuten und lässt sich einmal verwenden.\n\n"
            . rtrim((string) Env::get('BASE_URL', ''), '/') . '/login/' . $token . "\n\n";

        // §6.3: Die Telefonnummer steht in JEDER Anmeldemail.
        if ($notweg['telefon'] !== null) {
            $text .= 'Kommt der Link nicht an, rufen Sie uns an: ' . $notweg['telefon'] . ".\n\n";
        } elseif ($notweg['email'] !== null) {
            $text .= 'Kommt der Link nicht an, schreiben Sie an ' . $notweg['email'] . ".\n\n";
        }

        return $text . "Freundliche Grüße\nSARTU\n";
    }

    private static function wertOderNull(mixed $wert): ?string
    {
        return is_string($wert) && trim($wert) !== '' ? trim($wert) : null;
    }

    private function tokens(): AnmeldeTokenSpeicher
    {
        return $this->tokens ?? new AnmeldeTokenSpeicher($this->pdo);
    }

    private function mail(): Mailversand
    {
        return $this->mail ?? new Mailversand();
    }

    private function begrenzung(): Ratenbegrenzung
    {
        return $this->begrenzung ?? new Ratenbegrenzung();
    }

    private function audit(): AuditProtokoll
    {
        return $this->audit ?? new AuditProtokoll($this->pdo);
    }

    private function konten(): AnmeldeKonten
    {
        return $this->konten ?? new AnmeldeKonten($this->pdo);
    }
}
