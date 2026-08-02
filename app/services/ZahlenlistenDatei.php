<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Speicher;

/**
 * Eine Liste ganzer Zahlen je Schlüssel, als Datei in `/storage`.
 *
 * Zwei Dinge in A0 brauchen genau das und sonst nichts von einer Datenbank: die
 * Ratenbegrenzung (Zeitstempel der Versuche) und die Wiederholungssperre für TOTP-Codes
 * (verbrauchte Zeitschritte).
 *
 * **Warum nicht in einer Tabelle:** Das Datenmodell in §4 kennt keine, und eine zu erfinden
 * verstößt gegen „nichts erfinden". Dazu muss die Ratenbegrenzung schon **während** der
 * Ersteinrichtung greifen — zu einem Zeitpunkt, an dem es noch kein Schema gibt.
 *
 * Was hier steht, ist nur die Ablage. Was eine Zahl bedeutet und wann sie verfällt,
 * entscheiden die beiden Aufrufer für sich.
 */
final class ZahlenlistenDatei
{
    public function __construct(
        private readonly string $bereich,
        private readonly ?string $speicherverzeichnis = null,
    ) {
    }

    /** @return list<int> */
    public function lesen(string $schluessel): array
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

    /** @param list<int> $werte */
    public function schreiben(string $schluessel, array $werte): void
    {
        Speicher::sicherstellen($this->verzeichnis());

        file_put_contents($this->datei($schluessel), json_encode($werte, JSON_THROW_ON_ERROR), LOCK_EX);
    }

    public function loeschen(string $schluessel): void
    {
        $datei = $this->datei($schluessel);

        if (is_file($datei)) {
            unlink($datei);
        }
    }

    private function datei(string $schluessel): string
    {
        return $this->verzeichnis() . '/' . hash('sha256', $schluessel) . '.json';
    }

    private function verzeichnis(): string
    {
        return Speicher::verzeichnis($this->speicherverzeichnis) . '/' . $this->bereich;
    }
}
