<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Speicher;

/**
 * Portal-Lastenheft §1.5a: Waehrend `migrate.php up` liefern Kunden- und Adminbereich 503
 * mit Klartext. Nach Erfolg wird der Modus automatisch aufgehoben, nach Abbruch NICHT —
 * ein halb migriertes System soll niemanden hereinlassen (Testfall 76).
 */
final class Wartungsmodus
{
    public const DATEINAME = 'maintenance.lock';

    public function __construct(private readonly ?string $speicherverzeichnis = null)
    {
    }

    public function aktiv(): bool
    {
        return is_file($this->datei());
    }

    public function einschalten(string $grund): void
    {
        Speicher::sicherstellen($this->verzeichnis());

        file_put_contents($this->datei(), $grund . "\n" . gmdate('Y-m-d H:i:s') . " UTC\n", LOCK_EX);
    }

    public function ausschalten(): void
    {
        if (is_file($this->datei())) {
            unlink($this->datei());
        }
    }

    public function datei(): string
    {
        return rtrim($this->verzeichnis(), '/') . '/' . self::DATEINAME;
    }

    private function verzeichnis(): string
    {
        return Speicher::verzeichnis($this->speicherverzeichnis);
    }
}
