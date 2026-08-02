<?php

declare(strict_types=1);

namespace Sartu\Services;

use Sartu\Helpers\Env;

/**
 * Merkt sich, welcher TOTP-Zeitschritt für ein Konto schon eingelöst wurde.
 *
 * RFC 6238 §5.2: Ein angenommener Code gilt kein zweites Mal. Ohne das lässt sich ein
 * mitgelesener Code innerhalb seiner dreißig Sekunden erneut verwenden — aus einem geteilten
 * Bildschirm, einer Zwischenstelle oder einer zweiten Authenticator-App.
 *
 * **Warum als Datei und nicht als Spalte:** Das Datenmodell in §4 kennt für `users` kein Feld
 * dafür, und eines zu erfinden verstößt gegen „nichts erfinden". Die Ablage folgt deshalb
 * demselben Weg wie die Ratenbegrenzung: eine Datei je Konto in `/storage`, außerhalb des
 * Webroots.
 *
 * **Falls stattdessen ein Feld `users.totp_last_step` gewünscht ist**, ist das eine Änderung
 * am Datenmodell und damit eine Entscheidung des Betreibers, nicht meine.
 */
final class VerbrauchteCodes
{
    /** Länger als zwei Zeitschritte muss nichts aufgehoben werden. */
    private const HALTBARKEIT_SEKUNDEN = 120;

    public function __construct(private readonly ?string $speicherverzeichnis = null)
    {
    }

    /**
     * Löst einen Zeitschritt ein.
     *
     * @return bool true, wenn er noch frei war. false bedeutet: schon verwendet.
     */
    public function einloesen(string $benutzerId, int $zeitschritt): bool
    {
        $datei = $this->datei($benutzerId);
        $gespeichert = $this->lesen($datei);

        if (in_array($zeitschritt, $gespeichert, true)) {
            return false;
        }

        $jetzt = time();
        $behalten = array_values(array_filter(
            $gespeichert,
            static fn (int $s) => $s * 30 > $jetzt - self::HALTBARKEIT_SEKUNDEN
        ));
        $behalten[] = $zeitschritt;

        $this->schreiben($datei, $behalten);

        return true;
    }

    /** @return list<int> */
    private function lesen(string $datei): array
    {
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

    /** @param list<int> $zeitschritte */
    private function schreiben(string $datei, array $zeitschritte): void
    {
        $verzeichnis = dirname($datei);

        if (!is_dir($verzeichnis) && !mkdir($verzeichnis, 0770, true) && !is_dir($verzeichnis)) {
            throw new \RuntimeException(sprintf('Das Verzeichnis %s liess sich nicht anlegen.', $verzeichnis));
        }

        file_put_contents($datei, json_encode($zeitschritte, JSON_THROW_ON_ERROR), LOCK_EX);
    }

    private function datei(string $benutzerId): string
    {
        $basis = $this->speicherverzeichnis
            ?? Env::get('STORAGE_DIR', dirname(__DIR__, 2) . '/storage')
            ?? dirname(__DIR__, 2) . '/storage';

        return rtrim($basis, '/') . '/zweifaktor/' . hash('sha256', $benutzerId) . '.json';
    }
}
