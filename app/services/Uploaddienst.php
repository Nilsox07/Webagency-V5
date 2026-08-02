<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\Customer\KundenBereich;
use Sartu\Data\Customer\KundenDateien;
use Sartu\Data\Uuid;
use Sartu\Helpers\Speicher;

/**
 * Uploads — Portal-Lastenheft §11.
 *
 * ## Die sechs Prüfungen, in dieser Reihenfolge
 *
 * | # | Prüfung | Grenze | Warum sie vor der nächsten steht |
 * |---|---|---|---|
 * | 1 | Rechte bestätigt | Pflichthäkchen | Ohne Bestätigung wird gar nicht erst gelesen (Testfall 17) |
 * | 2 | Freier Platz auf dem Server | 1 GB | §11: „statt eines abgebrochenen Schreibvorgangs" |
 * | 3 | Größe je Datei | 20 MB | billig, fängt das meiste ab |
 * | 4 | Anzahl je Aufgabe | 10 | dito |
 * | 5 | Speicher je Organisation | 500 MB | Testfall 79 |
 * | 6 | Endung **und** MIME-Typ | acht Arten | teuerste Prüfung zuletzt |
 *
 * ## Endung UND MIME-Typ, nicht eines von beiden
 *
 * §11: „Prüfung von Endung **und** MIME-Typ; bei Abweichung ablehnen." Der Browser schickt
 * den MIME-Typ mit — er ist eine Behauptung des Absenders, keine Feststellung. Deshalb wird
 * er zusätzlich am Dateiinhalt geprüft (`finfo`), und beide müssen zur Endung passen.
 *
 * ## Wo die Dateien liegen
 *
 * §11: „Speicherung unter `UPLOAD_DIR` mit UUID-Dateinamen, **außerhalb** des öffentlich
 * ausgelieferten Verzeichnisses." Der Originalname wird gespeichert, aber **nie** als Pfad
 * benutzt — er kommt vom Kunden.
 *
 * ## SVG
 *
 * §11: „SVG werden **nicht** inline eingebettet, sondern nur als Download angeboten
 * (Skriptrisiko)." Ein SVG ist XML und kann Skript enthalten. Die Ausspielroute setzt
 * deshalb bei **jeder** Datei `Content-Disposition: attachment`, nicht nur bei SVG — eine
 * Sonderregel für einen Typ ist eine Sonderregel, die jemand vergisst.
 */
final class Uploaddienst
{
    /** §11: acht erlaubte Arten. */
    public const ERLAUBT = [
        'jpg'  => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png'  => ['image/png'],
        'webp' => ['image/webp'],
        'svg'  => ['image/svg+xml', 'text/plain', 'text/xml', 'application/xml'],
        'pdf'  => ['application/pdf'],
        'docx' => [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
        ],
        'zip'  => ['application/zip', 'application/x-zip-compressed'],
    ];

    public const MAX_BYTES_JE_DATEI = 20 * 1024 * 1024;

    public const MAX_DATEIEN_JE_AUFGABE = 10;

    public const MAX_BYTES_JE_ORGANISATION = 500 * 1024 * 1024;

    /** §11: Unter 1 GB freiem Platz wird abgelehnt, statt einen Schreibvorgang abzubrechen. */
    public const MIN_FREIER_PLATZ = 1024 * 1024 * 1024;

    public function __construct(
        private readonly KundenBereich $bereich,
        private readonly ?KundenDateien $dateien = null,
        private readonly ?string $ablage = null,
        private readonly ?\PDO $pdo = null,
    ) {
    }

    /**
     * Nimmt eine hochgeladene Datei an.
     *
     * @param array<string,mixed> $datei ein Eintrag aus `$_FILES`
     *
     * @return array{fehler:?string,id:?string}
     */
    public function annehmen(array $datei, string $aufgabeId, bool $rechteBestaetigt, ?string $benutzerId): array
    {
        // 1 — ohne bestätigte Rechte wird nicht gelesen (Testfall 17).
        if (!$rechteBestaetigt) {
            return self::fehler('Bitte bestätigen Sie die Bildrechte.');
        }

        if ((int) ($datei['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return self::fehler('Bitte wählen Sie mindestens eine Datei aus.');
        }

        $pfad = (string) ($datei['tmp_name'] ?? '');
        $groesse = (int) ($datei['size'] ?? 0);

        if ($pfad === '' || $groesse <= 0) {
            return self::fehler('Bitte wählen Sie mindestens eine Datei aus.');
        }

        // 2 — freier Platz auf dem Server.
        if (!$this->genugPlatzAufDemServer()) {
            return self::fehler('Wir können die Datei gerade nicht annehmen. Bitte versuchen Sie '
                . 'es in einer Stunde erneut oder schreiben Sie uns.');
        }

        // 3 — Größe je Datei.
        if ($groesse > self::MAX_BYTES_JE_DATEI) {
            return self::fehler('Die Datei ist zu groß. Bitte höchstens 20 MB je Datei.');
        }

        // 4 — Anzahl je Aufgabe.
        if ($this->dateien()->anzahlJeAufgabe($aufgabeId) >= self::MAX_DATEIEN_JE_AUFGABE) {
            return self::fehler('Zu dieser Aufgabe sind bereits 10 Dateien hinterlegt. '
                . 'Bitte fassen Sie weitere in einem ZIP-Archiv zusammen.');
        }

        // 5 — Speicher je Organisation (Testfall 79).
        if ($this->dateien()->belegterSpeicher() + $groesse > self::MAX_BYTES_JE_ORGANISATION) {
            return self::fehler('Ihr Speicher ist voll (500 MB). Bitte schreiben Sie uns — '
                . 'wir schaffen Platz.');
        }

        // 6 — Endung UND MIME-Typ (Testfall 46).
        $originalname = self::sicherername((string) ($datei['name'] ?? ''));
        $endung = mb_strtolower(pathinfo($originalname, PATHINFO_EXTENSION));

        if (!isset(self::ERLAUBT[$endung])) {
            return self::fehler(self::TYPFEHLER);
        }

        $tatsaechlich = self::mimeTypAusInhalt($pfad);

        if ($tatsaechlich === null || !in_array($tatsaechlich, self::ERLAUBT[$endung], true)) {
            return self::fehler(self::TYPFEHLER);
        }

        $gespeichert = Uuid::v4();

        if (!$this->ablegen($pfad, $gespeichert)) {
            return self::fehler('Wir konnten die Datei nicht speichern. Bitte versuchen Sie es '
                . 'in einem Moment erneut.');
        }

        $id = $this->dateien()->anlegen([
            'task_id'            => $aufgabeId,
            'original_name'      => mb_substr($originalname, 0, 255),
            'stored_name'        => $gespeichert,
            'mime_type'          => $tatsaechlich,
            'size_bytes'         => $groesse,
            'rights_confirmed'   => 1,
            'uploaded_by_user_id' => $benutzerId,
        ]);

        return ['fehler' => null, 'id' => $id];
    }

    /** §11, Fehlermeldung im Wortlaut. */
    private const TYPFEHLER = 'Diese Dateiart können wir nicht verarbeiten. Erlaubt sind Bilder, '
        . 'PDF, Word-Dateien und ZIP-Archive.';

    public function ablageverzeichnis(): string
    {
        // §11: ausserhalb des oeffentlich ausgelieferten Verzeichnisses. `Speicher` löst
        // /storage an einer Stelle auf — dieselbe wie die Ersteinrichtung prüft.
        return $this->ablage ?? Speicher::verzeichnis() . '/uploads';
    }

    public function pfadZu(string $gespeicherterName): string
    {
        return $this->ablageverzeichnis() . '/' . $gespeicherterName;
    }

    /**
     * Der MIME-Typ aus dem **Inhalt**, nicht aus der Angabe des Browsers.
     *
     * `$_FILES['...']['type']` kommt vom Absender und ist frei setzbar. Wer ihm glaubt,
     * prüft nichts.
     */
    private static function mimeTypAusInhalt(string $pfad): ?string
    {
        if (!is_file($pfad)) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if ($finfo === false) {
            return null;
        }

        $typ = finfo_file($finfo, $pfad);
        finfo_close($finfo);

        return is_string($typ) && $typ !== '' ? $typ : null;
    }

    /**
     * Nur der Dateiname, ohne Pfadanteile.
     *
     * `basename()` allein genügt nicht: Ein Name wie `..\\..\\datei.png` behält unter Linux
     * seine Backslashes. Beide Trennzeichen werden deshalb ersetzt, bevor `basename` läuft.
     */
    private static function sicherername(string $name): string
    {
        return basename(str_replace('\\', '/', $name));
    }

    private function genugPlatzAufDemServer(): bool
    {
        $verzeichnis = $this->ablageverzeichnis();
        Speicher::sicherstellen($verzeichnis);

        $frei = @disk_free_space($verzeichnis);

        // Lässt sich der Platz nicht ermitteln, wird nicht geraten — dann gilt er als
        // ausreichend, weil sonst kein Upload mehr ginge. Der Schreibfehler unten fängt
        // den echten Fall ab.
        return $frei === false || $frei >= self::MIN_FREIER_PLATZ;
    }

    private function ablegen(string $quelle, string $gespeicherterName): bool
    {
        $verzeichnis = $this->ablageverzeichnis();
        Speicher::sicherstellen($verzeichnis);

        $ziel = $verzeichnis . '/' . $gespeicherterName;

        // `move_uploaded_file` nur, wenn es wirklich ein Upload ist — im Testlauf gibt es
        // keinen, dort wird kopiert. Die Unterscheidung steht hier und nicht im Aufrufer.
        $erfolg = is_uploaded_file($quelle)
            ? move_uploaded_file($quelle, $ziel)
            : copy($quelle, $ziel);

        if ($erfolg) {
            @chmod($ziel, 0640);
        }

        return $erfolg;
    }

    /** @return array{fehler:string,id:null} */
    private static function fehler(string $meldung): array
    {
        return ['fehler' => $meldung, 'id' => null];
    }

    private function dateien(): KundenDateien
    {
        return $this->dateien ?? new KundenDateien($this->bereich, $this->pdo);
    }
}
