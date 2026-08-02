<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Data\BetreiberdatenSpeicher;
use Sartu\Helpers\Env;

/**
 * Die Sperre der Ersteinrichtung — Portal-Lastenheft §1.5.
 *
 * Sie liegt an zwei Orten:
 *
 *   Datenbank  operator_settings.setup_completed_at
 *   Datei      /storage/installed.lock, ausserhalb des Webroots
 *
 * „/admin/setup liefert 404, sobald EINER von beiden gesetzt ist. Nicht beide — einer
 * genuegt, sonst hebt ein geloeschtes Lockfile die Sperre auf."
 *
 * Genau daran scheiterte die vorige Fassung: Sie nannte „Datei und Datenbank", ohne zu
 * sagen, ob und oder oder gemeint ist. Testfall 73 prueft die strengere Lesart.
 */
final class InstallationsSperre
{
    public const DATEINAME = 'installed.lock';

    public function __construct(
        private readonly ?BetreiberdatenSpeicher $betreiberdaten = null,
        private readonly ?string $speicherverzeichnis = null,
    ) {
    }

    public function gesperrt(): bool
    {
        return $this->dateiGesetzt() || $this->datenbankGesetzt();
    }

    public function dateiGesetzt(): bool
    {
        return is_file($this->sperrdatei());
    }

    public function datenbankGesetzt(): bool
    {
        try {
            return ($this->betreiberdaten ?? new BetreiberdatenSpeicher())->einrichtungAbgeschlossen();
        } catch (\Throwable) {
            // Keine Datenbankverbindung, keine Tabelle: Die Einrichtung hat noch nicht
            // stattgefunden. Die Dateisperre entscheidet dann allein.
            return false;
        }
    }

    /**
     * Zuerst die Datenbank, dann die Datei — die Reihenfolge ist nicht beliebig.
     *
     * Umgekehrt bliebe bei einem Fehler in der Datenbank die Sperrdatei liegen, und die
     * Einrichtung waere dauerhaft zu, ohne je fertig geworden zu sein. Aufheben liesse sie
     * sich nur mit Dateizugriff auf dem Server — das ist der Zweck der Sperre und waere hier
     * ein Eigentor.
     *
     * Andersherum ist der halbe Zustand harmlos: Ein gesetztes `setup_completed_at` ohne
     * Datei sperrt genauso (§1.5 — einer von beiden genuegt).
     */
    public function setzen(): void
    {
        ($this->betreiberdaten ?? new BetreiberdatenSpeicher())->einrichtungAbschliessen();

        $verzeichnis = $this->verzeichnis();

        if (!is_dir($verzeichnis) && !mkdir($verzeichnis, 0770, true) && !is_dir($verzeichnis)) {
            throw new \RuntimeException(sprintf('Das Verzeichnis %s liess sich nicht anlegen.', $verzeichnis));
        }

        $geschrieben = file_put_contents(
            $this->sperrdatei(),
            "Die Ersteinrichtung ist abgeschlossen.\nAngelegt: " . gmdate('Y-m-d H:i:s') . " UTC\n",
            LOCK_EX
        );

        if ($geschrieben === false) {
            throw new \RuntimeException('Die Sperrdatei liess sich nicht schreiben.');
        }
    }

    public function sperrdatei(): string
    {
        return rtrim($this->verzeichnis(), '/') . '/' . self::DATEINAME;
    }

    private function verzeichnis(): string
    {
        return $this->speicherverzeichnis
            ?? Env::get('STORAGE_DIR', dirname(__DIR__, 2) . '/storage')
            ?? dirname(__DIR__, 2) . '/storage';
    }
}
