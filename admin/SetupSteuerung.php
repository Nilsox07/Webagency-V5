<?php

declare(strict_types=1);

namespace Sartu\Admin;

use Sartu\Ansicht;
use Sartu\Antwort;
use Sartu\Data\MigrationFehler;
use Sartu\Helpers\Env;
use Sartu\Helpers\Http;
use Sartu\Services\Ersteinrichtung;
use Sartu\Services\Zweifaktor;

/**
 * Die Routen der Ersteinrichtung — Portal-Lastenheft §1.5.
 *
 * Jeder POST fuehrt genau einen Schritt aus und leitet danach auf /admin/setup zurueck.
 * Der naechste Aufruf leitet den Fortschritt aus dem tatsaechlichen Zustand ab. Damit gibt
 * es keinen Zustand, der zwischen Formular und Datenbank auseinanderlaufen koennte.
 *
 * Die Sperre nach Abschluss und der HTTPS-Zwang stehen NICHT hier, sondern zentral im
 * Router — sonst waeren sie je Route wiederholt, und eine Wiederholung wird irgendwann
 * vergessen.
 */
final class SetupSteuerung
{
    private const TOTP_VORMERK = '_setup_totp';

    private Ersteinrichtung $einrichtung;

    public function __construct()
    {
        $this->einrichtung = new Ersteinrichtung(dirname(__DIR__));
    }

    /** @param array<string,string> $parameter */
    public function zeigen(array $parameter = []): Antwort
    {
        $schritt = $this->einrichtung->aktuellerSchritt();

        return $this->seite($schritt, $this->daten($schritt));
    }

    /**
     * Jeder POST gehoert zu genau einem Schritt — und laeuft nur, wenn die Strecke auch dort
     * steht.
     *
     * Ohne diese Pruefung genuegt EIN unangemeldeter Aufruf von `/admin/setup/abschluss`, um
     * eine frische Installation dauerhaft unbrauchbar zu machen: Die Sperre waere gesetzt,
     * ein Adminkonto gaebe es nicht, und aufheben laesst sie sich ueber das Netz nicht — das
     * ist ihr Zweck. Dasselbe gilt fuer `/admin/setup/admin`: Wer dort vor dem Betreiber
     * ankommt, hat das einzige Adminkonto.
     *
     * Die Strecke ist bewusst ohne Anmeldung erreichbar (es gibt noch kein Konto). Genau
     * deshalb muss die Reihenfolge selbst die Sperre sein.
     */
    private function nurInSchritt(int $erwartet): ?Antwort
    {
        $schritt = $this->einrichtung->aktuellerSchritt();

        if ($schritt === $erwartet) {
            return null;
        }

        return Antwort::weiter('/admin/setup');
    }

    /** Schritt 1 hat nichts zu speichern — der Knopf geht weiter, sobald alles in Ordnung ist. */
    public function umgebungBestaetigen(array $parameter = []): Antwort
    {
        return Antwort::weiter('/admin/setup');
    }

    public function datenbank(array $parameter = []): Antwort
    {
        if (($halt = $this->nurInSchritt(2)) !== null) {
            return $halt;
        }

        $fehler = $this->einrichtung->datenbankSpeichern(
            Http::getrimmteEingabe('db_host'),
            Http::getrimmteEingabe('db_port'),
            Http::getrimmteEingabe('db_name'),
            Http::getrimmteEingabe('db_user'),
            Http::eingabe('db_pass') ?? '',
        );

        if ($fehler !== []) {
            return $this->seite(2, [
                'fehler' => $fehler,
                'werte'  => [
                    'db_host' => Http::getrimmteEingabe('db_host'),
                    'db_port' => Http::getrimmteEingabe('db_port'),
                    'db_name' => Http::getrimmteEingabe('db_name'),
                    'db_user' => Http::getrimmteEingabe('db_user'),
                ],
            ]);
        }

        return Antwort::weiter('/admin/setup');
    }

    public function schluessel(array $parameter = []): Antwort
    {
        if (($halt = $this->nurInSchritt(3)) !== null) {
            return $halt;
        }

        $this->einrichtung->schluesselErzeugen();

        return Antwort::weiter('/admin/setup');
    }

    public function migrationen(array $parameter = []): Antwort
    {
        if (($halt = $this->nurInSchritt(4)) !== null) {
            return $halt;
        }

        try {
            $this->einrichtung->migrationenEinspielen();
        } catch (MigrationFehler $fehler) {
            return $this->seite(4, ['fehler' => [$fehler->getMessage()], ...$this->daten(4)]);
        } catch (\RuntimeException $fehler) {
            return $this->seite(4, ['fehler' => [$fehler->getMessage()], ...$this->daten(4)]);
        }

        return Antwort::weiter('/admin/setup');
    }

    public function mail(array $parameter = []): Antwort
    {
        if (($halt = $this->nurInSchritt(5)) !== null) {
            return $halt;
        }

        $werte = [
            'smtp_host' => Http::getrimmteEingabe('smtp_host'),
            'smtp_port' => Http::getrimmteEingabe('smtp_port'),
            'smtp_user' => Http::getrimmteEingabe('smtp_user'),
            'mail_from' => Http::getrimmteEingabe('mail_from'),
            'an'        => Http::getrimmteEingabe('an'),
        ];

        $fehler = $this->einrichtung->testmailSenden(
            $werte['smtp_host'],
            $werte['smtp_port'],
            $werte['smtp_user'],
            Http::eingabe('smtp_pass') ?? '',
            $werte['mail_from'],
            $werte['an'],
        );

        return $this->seite(5, [
            'fehler'   => $fehler,
            'werte'    => $werte,
            'gesendet' => $fehler === [],
        ]);
    }

    public function mailBestaetigen(array $parameter = []): Antwort
    {
        if (($halt = $this->nurInSchritt(5)) !== null) {
            return $halt;
        }

        $this->einrichtung->mailBestaetigen();

        return Antwort::weiter('/admin/setup');
    }

    public function betrieb(array $parameter = []): Antwort
    {
        if (($halt = $this->nurInSchritt(6)) !== null) {
            return $halt;
        }

        $eingabe = $this->betriebsEingabe();
        $fehler = $this->einrichtung->betreiberdatenAnlegen($eingabe);

        if ($fehler !== []) {
            return $this->seite(6, ['fehler' => $fehler, 'werte' => $eingabe]);
        }

        return Antwort::weiter('/admin/setup');
    }

    public function admin(array $parameter = []): Antwort
    {
        if (($halt = $this->nurInSchritt(7)) !== null) {
            return $halt;
        }

        $werte = [
            'vorname'  => Http::getrimmteEingabe('vorname'),
            'nachname' => Http::getrimmteEingabe('nachname'),
            'email'    => Http::getrimmteEingabe('email'),
        ];

        $geheimnis = Http::getrimmteEingabe('totp_geheimnis');

        $fehler = $this->einrichtung->adminAnlegen(
            $werte['email'],
            $werte['vorname'],
            $werte['nachname'],
            Http::eingabe('passwort') ?? '',
            Http::eingabe('passwort_wiederholung') ?? '',
            $geheimnis,
            Http::getrimmteEingabe('code'),
        );

        if ($fehler !== []) {
            // Dasselbe Geheimnis weiterreichen: Ein neues wuerde die gerade eingerichtete
            // App entwerten und den Benutzer in eine Schleife schicken.
            $_SESSION[self::TOTP_VORMERK] = $geheimnis;

            return $this->seite(7, ['fehler' => $fehler, 'werte' => $werte]);
        }

        unset($_SESSION[self::TOTP_VORMERK]);

        return Antwort::weiter('/admin/setup');
    }

    public function abschluss(array $parameter = []): Antwort
    {
        if (($halt = $this->nurInSchritt(8)) !== null) {
            return $halt;
        }

        $this->einrichtung->abschliessen();

        return Antwort::weiter('/admin/anmelden');
    }

    // ------------------------------------------------------------------

    /** @param array<string,mixed> $daten */
    private function seite(int $schritt, array $daten): Antwort
    {
        return Antwort::html(Ansicht::seite('setup', 'setup-' . $schritt, [
            'titel'   => Ersteinrichtung::SCHRITTE[$schritt] . ' — Einrichtung',
            'schritt' => $schritt,
            'fehler'  => [],
            'werte'   => [],
            ...$daten,
        ]));
    }

    /** @return array<string,mixed> */
    private function daten(int $schritt): array
    {
        return match ($schritt) {
            1 => [
                'pruefungen' => $this->einrichtung->umgebungspruefung(),
                'bereit'     => $this->einrichtung->umgebungInOrdnung(),
            ],
            2 => ['werte' => ['db_host' => Env::get('DB_HOST', 'db') ?? 'db', 'db_name' => Env::get('DB_NAME', '') ?? '']],
            4 => [
                'offene'       => $this->einrichtung->migrator()->offene(),
                'eingetragene' => $this->einrichtung->migrator()->eingetragene(),
            ],
            5 => [
                'gesendet' => false,
                'werte'    => ['smtp_host' => Env::get('SMTP_HOST', '') ?? '', 'smtp_port' => Env::get('SMTP_PORT', '587') ?? '587'],
            ],
            7 => $this->totpDaten(),
            8 => ['cronBefehl' => $this->einrichtung->cronBefehl()],
            default => [],
        };
    }

    /** @return array<string,mixed> */
    private function totpDaten(): array
    {
        $geheimnis = $_SESSION[self::TOTP_VORMERK] ?? null;

        if (!is_string($geheimnis) || $geheimnis === '') {
            $geheimnis = Zweifaktor::geheimnisErzeugen();
            $_SESSION[self::TOTP_VORMERK] = $geheimnis;
        }

        $konto = Env::get('MAIL_FROM', 'SARTU') ?? 'SARTU';

        return [
            'geheimnis' => $geheimnis,
            'lesbar'    => Zweifaktor::lesbaresGeheimnis($geheimnis),
            'adresse'   => $konto,
        ];
    }

    /** @return array<string,string> */
    private function betriebsEingabe(): array
    {
        $felder = [
            'firmenname', 'rechtsform', 'strasse', 'plz', 'ort', 'land', 'telefon', 'email',
            'ust_id', 'steuernummer', 'inhaltlich_verantwortlich', 'kleinunternehmer',
        ];

        $eingabe = [];
        foreach ($felder as $feld) {
            $eingabe[$feld] = Http::getrimmteEingabe($feld);
        }

        return $eingabe;
    }
}
