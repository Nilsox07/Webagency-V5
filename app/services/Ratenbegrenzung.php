<?php

declare(strict_types=1);

namespace Sartu\Services;

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
    private ZahlenlistenDatei $ablage;

    public function __construct(
        ?string $speicherverzeichnis = null,
        private readonly ?\Closure $uhr = null,
    ) {
        $this->ablage = new ZahlenlistenDatei('ratenbegrenzung', $speicherverzeichnis);
    }

    public function erlaubt(string $schluessel, int $versuche, int $fensterSekunden): bool
    {
        return $this->verbleibend($schluessel, $versuche, $fensterSekunden) > 0;
    }

    public function verbleibend(string $schluessel, int $versuche, int $fensterSekunden): int
    {
        return max(0, $versuche - count($this->imFenster($schluessel, $fensterSekunden)));
    }

    public function vermerken(string $schluessel, int $fensterSekunden): void
    {
        $zeitstempel = $this->imFenster($schluessel, $fensterSekunden);
        $zeitstempel[] = $this->jetzt();

        $this->ablage->schreiben($schluessel, $zeitstempel);
    }

    public function zuruecksetzen(string $schluessel): void
    {
        $this->ablage->loeschen($schluessel);
    }

    /** @return list<int> */
    private function imFenster(string $schluessel, int $fensterSekunden): array
    {
        $grenze = $this->jetzt() - $fensterSekunden;

        return array_values(array_filter(
            $this->ablage->lesen($schluessel),
            static fn (int $zeitpunkt) => $zeitpunkt > $grenze
        ));
    }

    private function jetzt(): int
    {
        return $this->uhr === null ? time() : (int) ($this->uhr)();
    }
}
