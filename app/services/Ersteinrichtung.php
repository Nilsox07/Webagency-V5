<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\Admin\AdminBenutzer;
use Sartu\Data\Admin\AdminNachweis;
use Sartu\Data\AuditProtokoll;
use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\Db;
use Sartu\Data\Migrator;
use Sartu\Helpers\Env;
use Sartu\Helpers\Http;
use Sartu\Helpers\Validate;

/**
 * Die gefuehrte Ersteinrichtung — Portal-Lastenheft §1.5, acht Schritte.
 *
 * Die Reihenfolge ist keine Geschmacksfrage. Die Fassung vom 30.07.2026 war nicht
 * ausfuehrbar, weil sie in drei Punkten falsch herum stand: Sie erhob kein Passwort,
 * erzeugte ENC_KEY erst NACH dem verschluesselten TOTP-Geheimnis und legte
 * `operator_settings` an, ohne eines der sieben Pflichtfelder erhoben zu haben. Deshalb:
 *
 *   1 Umgebung · 2 Datenbank · 3 Schluessel · 4 Migrationen · 5 Mailversand
 *   6 Betreiberdaten · 7 Adminkonto · 8 Abschluss
 *
 * Der Fortschritt wird nicht mitgeschrieben, sondern aus dem tatsaechlichen Zustand
 * abgeleitet. Ein Abbruch mitten in der Strecke fuehrt damit an genau die Stelle zurueck,
 * an der es weitergeht — auch wenn die Sitzung dazwischen verloren ging.
 */
final class Ersteinrichtung
{
    public const SCHRITTE = [
        1 => 'Umgebung',
        2 => 'Datenbank',
        3 => 'Schlüssel',
        4 => 'Migrationen',
        5 => 'Mailversand',
        6 => 'Betreiberdaten',
        7 => 'Erstes Adminkonto',
        8 => 'Abschluss',
    ];

    private const MAIL_BESTAETIGT = '_setup_mail_bestaetigt';

    public function __construct(
        private readonly string $wurzel,
        private readonly ?InstallationsSperre $sperre = null,
    ) {
    }

    // ------------------------------------------------------------------ Zugang

    /**
     * Die HTTP-Ausnahme aus §1.5 — eng begrenzt, sonst blockiert sich die Entwicklung selbst.
     *
     * Ueber HTTP laeuft die Einrichtung NUR, wenn alle drei Bedingungen gleichzeitig
     * zutreffen. Trifft eine nicht zu, wird abgebrochen: kein Bestaetigungsdialog, kein
     * „trotzdem fortfahren".
     *
     * Ausdruecklich KEIN Nachweis sind X-Forwarded-Proto und X-Forwarded-For — beide sind
     * frei setzbar, solange keine Liste vertrauenswuerdiger Zwischenstellen konfiguriert ist.
     * Diese Liste gibt es in Stufe 0 nicht, also werden die Kopfzeilen ignoriert
     * (Testfälle 70, 71, 72).
     */
    public static function zugangErlaubt(): bool
    {
        if (Http::istHttps()) {
            return true;
        }

        return Env::appEnv() === 'local'
            && Http::istLoopback()
            && Http::istLokalerHostname();
    }

    // ------------------------------------------------------------------ Fortschritt

    public function aktuellerSchritt(): int
    {
        if (!$this->umgebungInOrdnung()) {
            return 1;
        }

        if (!$this->datenbankErreichbar()) {
            return 2;
        }

        if (!$this->schluesselVorhanden()) {
            return 3;
        }

        if (!$this->schemaVollstaendig()) {
            return 4;
        }

        if (!$this->mailBestaetigt()) {
            return 5;
        }

        if (!$this->betreiberdaten()->vorhanden()) {
            return 6;
        }

        if (!$this->adminVorhanden()) {
            return 7;
        }

        return 8;
    }

    // ------------------------------------------------- Schritt 1: Umgebung

    /**
     * @return list<array{punkt:string,erfuellt:bool,hinweis:string}>
     */
    public function umgebungspruefung(): array
    {
        $ergebnis = [];

        $phpPasst = PHP_VERSION_ID >= 80300;
        $ergebnis[] = [
            'punkt'    => 'PHP 8.3 oder neuer',
            'erfuellt' => $phpPasst,
            'hinweis'  => 'Gefunden: PHP ' . PHP_VERSION,
        ];

        foreach (['pdo_mysql', 'sodium', 'mbstring', 'intl', 'fileinfo', 'openssl'] as $erweiterung) {
            $ergebnis[] = [
                'punkt'    => 'Erweiterung ' . $erweiterung,
                'erfuellt' => extension_loaded($erweiterung),
                'hinweis'  => extension_loaded($erweiterung) ? 'vorhanden' : 'fehlt',
            ];
        }

        $speicher = $this->speicherverzeichnis();
        $schreibbar = is_dir($speicher) && is_writable($speicher);
        $ergebnis[] = [
            'punkt'    => 'Schreibrechte auf dem Speicherverzeichnis',
            'erfuellt' => $schreibbar,
            'hinweis'  => $speicher,
        ];

        // §1.3: Nur /public ist ueber den Webserver erreichbar. Liegt das
        // Speicherverzeichnis darin, waeren Uploads direkt abrufbar.
        $ausserhalb = !str_starts_with(
            rtrim($this->pfadAufloesen($speicher), '/') . '/',
            rtrim($this->pfadAufloesen($this->wurzel . '/public'), '/') . '/'
        );
        $ergebnis[] = [
            'punkt'    => 'Speicherverzeichnis liegt außerhalb des Webroots',
            'erfuellt' => $ausserhalb,
            'hinweis'  => $ausserhalb ? 'außerhalb von /public' : 'liegt in /public — das darf nicht sein',
        ];

        $envSchreibbar = is_writable(Env::fileExists() ? Env::envPath() : dirname(Env::envPath()));
        $ergebnis[] = [
            'punkt'    => 'Konfigurationsdatei .env ist schreibbar',
            'erfuellt' => $envSchreibbar,
            'hinweis'  => Env::envPath(),
        ];

        return $ergebnis;
    }

    public function umgebungInOrdnung(): bool
    {
        foreach ($this->umgebungspruefung() as $punkt) {
            if (!$punkt['erfuellt']) {
                return false;
            }
        }

        return true;
    }

    // ------------------------------------------------- Schritt 2: Datenbank

    /**
     * Verbindung sofort testen, Zeichensatz und Kollation pruefen, erst dann speichern.
     *
     * @return list<string> Klartextfehler. Leer bedeutet: gespeichert.
     */
    public function datenbankSpeichern(
        string $host,
        string $port,
        string $name,
        string $benutzer,
        string $passwort,
    ): array {
        try {
            $pdo = Db::oeffnen($host, $port === '' ? '3306' : $port, $name, $benutzer, $passwort);
        } catch (\PDOException $fehler) {
            // §1.5: Zugangsdaten werden nie in eine Fehlermeldung geschrieben.
            return ['Die Verbindung zur Datenbank kam nicht zustande: ' . $this->ohneZugangsdaten($fehler->getMessage(), $passwort)];
        }

        $migrator = new Migrator($pdo, $this->wurzel . '/migrations');
        $fehler = $migrator->vorpruefung(leereDatenbankVerlangt: true);

        if ($fehler !== []) {
            return $fehler;
        }

        Env::write([
            'DB_HOST' => $host,
            'DB_PORT' => $port === '' ? '3306' : $port,
            'DB_NAME' => $name,
            'DB_USER' => $benutzer,
            'DB_PASS' => $passwort,
        ]);

        Db::setzen(null);

        return [];
    }

    public function datenbankErreichbar(): bool
    {
        return Db::erreichbar();
    }

    // ------------------------------------------------- Schritt 3: Schlüssel

    /**
     * Beide Schluessel entstehen hier — VOR allem, was verschluesselt wird. Das TOTP-
     * Geheimnis in Schritt 7 haengt daran.
     */
    public function schluesselErzeugen(): void
    {
        $werte = [];

        if (!Validate::gefuellt(Env::get('SESSION_SECRET'))) {
            $werte['SESSION_SECRET'] = base64_encode(random_bytes(32));
        }

        if (!Validate::gefuellt(Env::get('ENC_KEY'))) {
            $werte['ENC_KEY'] = Verschluesselung::schluesselErzeugen();
        }

        if ($werte !== []) {
            Env::write($werte);
        }
    }

    public function schluesselVorhanden(): bool
    {
        return Validate::gefuellt(Env::get('SESSION_SECRET')) && Validate::gefuellt(Env::get('ENC_KEY'));
    }

    // ------------------------------------------------- Schritt 4: Migrationen

    public function migrator(): Migrator
    {
        return new Migrator(Db::verbindung(), $this->wurzel . '/migrations');
    }

    /**
     * Legt zuerst das Protokoll an, spielt dann jede Migration einzeln ein und traegt sie
     * unmittelbar nach Erfolg ein. Ein erneuter Aufruf setzt bei der ersten nicht
     * eingetragenen Migration fort (Testfall 69).
     *
     * @return list<string> eingespielte Versionen
     */
    public function migrationenEinspielen(): array
    {
        $migrator = $this->migrator();

        $fehler = $migrator->vorpruefung(leereDatenbankVerlangt: !$migrator->protokolltabelleVorhanden());
        if ($fehler !== []) {
            throw new \RuntimeException(implode(' ', $fehler));
        }

        $migrator->protokolltabelleAnlegen();

        return $migrator->offeneEinspielen();
    }

    public function schemaVollstaendig(): bool
    {
        try {
            return $this->migrator()->offene() === [];
        } catch (\Throwable) {
            return false;
        }
    }

    // ------------------------------------------------- Schritt 5: Mailversand

    /**
     * Sendet mit den gerade eingegebenen Zugangsdaten — noch bevor sie in der .env stehen.
     * Erst wenn der Empfang bestaetigt ist, werden sie gespeichert.
     *
     * @return list<string> Klartextfehler
     */
    public function testmailSenden(
        string $host,
        string $port,
        string $benutzer,
        string $passwort,
        string $absender,
        string $an,
    ): array {
        if (!Validate::email($an)) {
            return ['Die Empfängeradresse ist nicht vollständig.'];
        }

        if (!Validate::email($absender)) {
            return ['Die Absenderadresse ist nicht vollständig.'];
        }

        $zugang = new SmtpZugang(
            $host,
            (int) ($port === '' ? '25' : $port),
            $benutzer,
            $passwort,
            $absender,
            Env::get('MAIL_FROM_NAME', 'SARTU') ?? 'SARTU',
        );

        try {
            (new Mailversand($zugang))->senden(
                $an,
                'Testnachricht aus der Einrichtung',
                "Guten Tag,\n\nder Mailversand funktioniert. Diese Nachricht wurde bei der Einrichtung "
                . "Ihres Kundenbereichs verschickt.\n\nFreundliche Grüße\nSARTU\n",
            );
        } catch (MailversandFehler $fehler) {
            return ['Die Nachricht ging nicht raus: ' . $this->ohneZugangsdaten($fehler->getMessage(), $passwort)];
        }

        Env::write([
            'SMTP_HOST' => $host,
            'SMTP_PORT' => $port === '' ? '25' : $port,
            'SMTP_USER' => $benutzer,
            'SMTP_PASS' => $passwort,
            'MAIL_FROM' => $absender,
        ]);

        return [];
    }

    /** §1.5 Schritt 5: „Empfang muss bestaetigt werden, bevor es weitergeht." */
    public function mailBestaetigen(): void
    {
        $_SESSION[self::MAIL_BESTAETIGT] = '1';
    }

    public function mailBestaetigt(): bool
    {
        return ($_SESSION[self::MAIL_BESTAETIGT] ?? null) === '1';
    }

    // ------------------------------------------------- Schritt 6: Betreiberdaten

    /**
     * @param array<string,string> $eingabe
     * @return list<string> Klartextfehler
     */
    public function betreiberdatenAnlegen(array $eingabe): array
    {
        $dienst = new BetreiberdatenDienst();
        $fehler = $dienst->pruefen($eingabe);

        if ($fehler !== []) {
            return $fehler;
        }

        $speicher = $this->betreiberdaten();

        if ($speicher->vorhanden()) {
            return [];
        }

        $speicher->anlegen($dienst->aufbereiten($eingabe));

        (new AuditProtokoll())->schreiben(
            aktion: 'betreiberdaten_angelegt',
            objektart: 'operator_settings',
            grund: 'Ersteinrichtung, Schritt 6',
            ip: Http::gegenstelle(),
        );

        return [];
    }

    // ------------------------------------------------- Schritt 7: Adminkonto

    /**
     * E-Mail, Name, selbst vergebenes Passwort (Argon2id, mindestens 12 Zeichen), TOTP
     * einrichten und einen Code bestaetigen. Kein Vorgabepasswort, kein Standardkonto.
     *
     * @return list<string> Klartextfehler
     */
    public function adminAnlegen(
        string $email,
        string $vorname,
        string $nachname,
        string $passwort,
        string $passwortWiederholung,
        string $totpGeheimnis,
        string $code,
    ): array {
        $fehler = [];

        if (!Validate::email($email)) {
            $fehler[] = 'Die E-Mail-Adresse ist nicht vollständig.';
        }

        if (!Validate::gefuellt($vorname) || !Validate::gefuellt($nachname)) {
            $fehler[] = 'Bitte tragen Sie Vor- und Nachnamen ein.';
        }

        if (!Validate::passwort($passwort)) {
            $fehler[] = 'Das Passwort braucht mindestens zwölf Zeichen.';
        }

        if ($passwort !== $passwortWiederholung) {
            $fehler[] = 'Die beiden Passwörter stimmen nicht überein.';
        }

        if (!Validate::gefuellt($totpGeheimnis)) {
            $fehler[] = 'Der Schlüssel für die Authenticator-App fehlt. Bitte laden Sie den Schritt neu.';
        } elseif (!Zweifaktor::pruefen($totpGeheimnis, $code)) {
            $fehler[] = 'Der Code stimmt nicht. Er wechselt alle 30 Sekunden — bitte den aktuellen eintragen.';
        }

        if ($fehler !== []) {
            return $fehler;
        }

        $nachweis = AdminNachweis::fuerErsteinrichtung(true);
        $benutzer = new AdminBenutzer($nachweis);

        if ($benutzer->anzahlAdmins() > 0) {
            return ['Es gibt bereits ein Adminkonto. Die Einrichtung legt kein zweites an.'];
        }

        $id = $benutzer->adminAnlegen(
            $email,
            trim($vorname),
            trim($nachname),
            password_hash($passwort, PASSWORD_ARGON2ID),
            (new Verschluesselung())->verschluesseln($totpGeheimnis),
        );

        (new AuditProtokoll())->schreiben(
            aktion: 'adminkonto_angelegt',
            objektart: 'users',
            objektId: $id,
            grund: 'Ersteinrichtung, Schritt 7',
            ip: Http::gegenstelle(),
        );

        return [];
    }

    public function adminVorhanden(): bool
    {
        try {
            return (new AdminBenutzer(AdminNachweis::fuerErsteinrichtung(true)))->anzahlAdmins() > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    // ------------------------------------------------- Schritt 8: Abschluss

    /** Der Befehl zum Kopieren. Eintragen muss ihn der Mensch beim Anbieter (§1.5). */
    public function cronBefehl(): string
    {
        return sprintf('0 3 * * * %s %s/bin/cron.php', PHP_BINARY, $this->wurzel);
    }

    public function abschliessen(): void
    {
        $this->installationssperre()->setzen();

        (new AuditProtokoll())->schreiben(
            aktion: 'ersteinrichtung_abgeschlossen',
            objektart: 'operator_settings',
            grund: 'Ersteinrichtung, Schritt 8',
            ip: Http::gegenstelle(),
        );
    }

    public function installationssperre(): InstallationsSperre
    {
        return $this->sperre ?? new InstallationsSperre();
    }

    // ------------------------------------------------- Hilfsmittel

    private function betreiberdaten(): BetreiberdatenSpeicher
    {
        return new BetreiberdatenSpeicher();
    }

    private function speicherverzeichnis(): string
    {
        return Env::get('STORAGE_DIR', $this->wurzel . '/storage') ?? $this->wurzel . '/storage';
    }

    private function pfadAufloesen(string $pfad): string
    {
        $echt = realpath($pfad);

        return $echt === false ? $pfad : $echt;
    }

    /** Sicherheitsnetz: Ein Passwort taucht in keiner Meldung auf, auch nicht teilweise. */
    private function ohneZugangsdaten(string $meldung, string $passwort): string
    {
        if ($passwort === '') {
            return $meldung;
        }

        return str_replace($passwort, '[entfernt]', $meldung);
    }
}
