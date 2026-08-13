<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Data\Uuid;
use Sartu\Helpers\Speicher;

/**
 * Das Bild der Person hinter SARTU — `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4c, 10.08.2026.
 *
 * ## Warum es nicht in `public/` liegt
 *
 * §11: Uploads liegen „**außerhalb** des öffentlich ausgelieferten Verzeichnisses". Das gilt
 * auch, wenn der Betreiber selbst hochlädt — ein beschreibbares Verzeichnis unter `/public`
 * ist ein beschreibbares Verzeichnis unter `/public`, unabhängig davon, wer es füllt.
 *
 * Ausgeliefert wird deshalb über die Route `/bild/gruender`, die den Inhalt streamt. Sie
 * setzt den Inhaltstyp aus der Datei, `nosniff` dazu und **keine** `Content-Disposition:
 * attachment` — anders als bei Kundendateien, denn dieses Bild gehört in ein `<img>`.
 *
 * Das ist tragbar, weil nur drei Bildarten hineinkommen (`Uploaddienst::BILDARTEN`) und
 * jede davon am Inhalt geprüft wird, nicht an der Endung. **SVG ist ausgeschlossen** — es
 * wäre die eine Art, die Skript tragen kann.
 *
 * ## Warum der alte Name überschrieben und die alte Datei gelöscht wird
 *
 * Es gibt genau ein Bild. Jede neue Aufnahme bekommt eine neue UUID, damit ein
 * zwischengespeicherter Browser nicht das alte Bild weiterzeigt; die vorige Datei wird
 * danach weggeräumt. **Das ist keine fachliche Löschung** — die Regel „keine harte Löschung
 * fachlicher Daten" schützt Vorgänge, nicht ersetzte Dateien.
 */
final class Gruenderbild
{
    /** Der Unterordner in der Ablage. Getrennt von `uploads`, das Kundendateien trägt. */
    public const ORDNER = 'betrieb';

    public function __construct(
        private readonly ?BetreiberdatenSpeicher $betrieb = null,
        private readonly ?string $ablage = null,
    ) {
    }

    /**
     * Nimmt eine Datei an und trägt sie ein.
     *
     * @param array<string,mixed> $datei ein Eintrag aus `$_FILES`
     * @return array{fehler:?string,name:?string}
     */
    public function annehmen(array $datei): array
    {
        $geprueft = Uploaddienst::bildPruefen($datei);

        if ($geprueft['fehler'] !== null) {
            return ['fehler' => $geprueft['fehler'], 'name' => null];
        }

        $verzeichnis = $this->verzeichnis();
        Speicher::sicherstellen($verzeichnis);

        // Der Ablagename traegt die Endung, damit `/bild/gruender` den Inhaltstyp ohne
        // zweite Pruefung kennt. Der Originalname des Betreibers wird nie zum Pfad.
        $name = Uuid::v4() . '.' . $geprueft['endung'];
        $ziel = $verzeichnis . '/' . $name;
        $quelle = (string) $datei['tmp_name'];

        $erfolg = is_uploaded_file($quelle)
            ? move_uploaded_file($quelle, $ziel)
            : copy($quelle, $ziel);

        if (!$erfolg) {
            return ['fehler' => 'Wir konnten das Bild nicht speichern. Bitte versuchen Sie es erneut.', 'name' => null];
        }

        @chmod($ziel, 0640);

        $vorher = $this->name();
        $this->speicher()->gruenderbildSetzen($name);
        $this->wegraeumen($vorher);

        return ['fehler' => null, 'name' => $name];
    }

    /** Entfernt Verweis und Datei. Danach entfällt die Sektion wieder — §4c. */
    public function entfernen(): void
    {
        $vorher = $this->name();
        $this->speicher()->gruenderbildSetzen(null);
        $this->wegraeumen($vorher);
    }

    /** Der eingetragene Ablagename, oder `null`. */
    public function name(): ?string
    {
        $wert = ($this->speicher()->lesen() ?? [])['gruender_bild'] ?? null;

        return is_string($wert) && $wert !== '' ? $wert : null;
    }

    /**
     * Inhalt und Typ für die Ausspielroute.
     *
     * @return array{inhalt:string,typ:string}|null `null`, wenn nichts hinterlegt ist oder
     *                                              die Datei fehlt
     */
    public function ausliefern(): ?array
    {
        $name = $this->name();

        if ($name === null) {
            return null;
        }

        $pfad = $this->pfadZu($name);

        if (!is_file($pfad)) {
            return null;
        }

        $inhalt = @file_get_contents($pfad);

        if (!is_string($inhalt)) {
            return null;
        }

        return ['inhalt' => $inhalt, 'typ' => self::typZu($name)];
    }

    public function pfadZu(string $name): string
    {
        // `basename` und nicht der Name selbst: Der Wert kommt zwar aus der eigenen
        // Datenbank, aber ein Pfad, der aus einem Feld zusammengesetzt wird, wird
        // an genau einer Stelle abgesichert — hier.
        return $this->verzeichnis() . '/' . basename(str_replace('\\', '/', $name));
    }

    public function verzeichnis(): string
    {
        return ($this->ablage ?? Speicher::verzeichnis()) . '/' . self::ORDNER;
    }

    /**
     * Der Inhaltstyp aus der Endung des **selbst erzeugten** Namens.
     *
     * Das ist hier zulässig, wo es beim Hochladen verboten wäre: Die Endung stammt aus
     * `Uploaddienst::bildPruefen()`, das sie zuvor gegen den tatsächlichen Inhalt gehalten
     * hat. Geraten wird nichts — `match` kennt nur die drei Fälle, die hineinkommen können.
     */
    private static function typZu(string $name): string
    {
        return match (mb_strtolower(pathinfo($name, PATHINFO_EXTENSION))) {
            'png'  => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }

    private function wegraeumen(?string $name): void
    {
        if ($name === null || $name === $this->name()) {
            return;
        }

        $pfad = $this->pfadZu($name);

        if (is_file($pfad)) {
            @unlink($pfad);
        }
    }

    private function speicher(): BetreiberdatenSpeicher
    {
        return $this->betrieb ?? new BetreiberdatenSpeicher();
    }
}
