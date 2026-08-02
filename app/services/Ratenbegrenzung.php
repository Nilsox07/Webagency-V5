<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Env;

/**
 * Portal-Lastenheft §1.5: „Rate-Limit auf jeden Schritt, damit die Strecke nicht als
 * Passwortprobierflaeche dient." §3 Regel 4 verlangt dasselbe fuer die Anmeldung.
 *
 * Die Zaehler liegen als Dateien in /storage und NICHT in einer Tabelle: Das Datenmodell
 * in §4 kennt keine Tabelle dafuer, und eine zu erfinden waere gegen die Regel „nichts
 * erfinden". Ausserdem muss die Begrenzung waehrend der Ersteinrichtung greifen — also zu
 * einem Zeitpunkt, an dem es noch gar kein Schema gibt.
 */
final class Ratenbegrenzung
{
    public function __construct(
        private readonly ?string $speicherverzeichnis = null,
        private readonly ?\Closure $uhr = null,
    ) {
    }

    public function erlaubt(string $schluessel, int $versuche, int $fensterSekunden): bool
    {
        return $this->verbleibend($schluessel, $versuche, $fensterSekunden) > 0;
    }

    public function verbleibend(string $schluessel, int $versuche, int $fensterSekunden): int
    {
        $jetzt = $this->jetzt();
        $zeitstempel = array_values(array_filter(
            $this->lesen($schluessel),
            static fn (int $z) => $z > $jetzt - $fensterSekunden
        ));

        return max(0, $versuche - count($zeitstempel));
    }

    public function vermerken(string $schluessel, int $fensterSekunden): void
    {
        $jetzt = $this->jetzt();
        $zeitstempel = array_values(array_filter(
            $this->lesen($schluessel),
            static fn (int $z) => $z > $jetzt - $fensterSekunden
        ));
        $zeitstempel[] = $jetzt;

        $this->schreiben($schluessel, $zeitstempel);
    }

    public function zuruecksetzen(string $schluessel): void
    {
        $datei = $this->datei($schluessel);
        if (is_file($datei)) {
            unlink($datei);
        }
    }

    /** @return list<int> */
    private function lesen(string $schluessel): array
    {
        $datei = $this->datei($schluessel);
        if (!is_file($datei)) {
            return [];
        }

        $inhalt = file_get_contents($datei);
        if ($inhalt === false || $inhalt === '') {
            return [];
        }

        $werte = json_decode($inhalt, true);

        return is_array($werte) ? array_values(array_map('intval', $werte)) : [];
    }

    /** @param list<int> $zeitstempel */
    private function schreiben(string $schluessel, array $zeitstempel): void
    {
        $verzeichnis = $this->verzeichnis();
        if (!is_dir($verzeichnis) && !mkdir($verzeichnis, 0770, true) && !is_dir($verzeichnis)) {
            throw new \RuntimeException(sprintf('Das Verzeichnis %s liess sich nicht anlegen.', $verzeichnis));
        }

        file_put_contents($this->datei($schluessel), json_encode($zeitstempel, JSON_THROW_ON_ERROR), LOCK_EX);
    }

    private function datei(string $schluessel): string
    {
        return $this->verzeichnis() . '/' . hash('sha256', $schluessel) . '.json';
    }

    private function verzeichnis(): string
    {
        $basis = $this->speicherverzeichnis
            ?? Env::get('STORAGE_DIR', dirname(__DIR__, 2) . '/storage')
            ?? dirname(__DIR__, 2) . '/storage';

        return rtrim($basis, '/') . '/ratenbegrenzung';
    }

    private function jetzt(): int
    {
        return $this->uhr === null ? time() : (int) ($this->uhr)();
    }
}
