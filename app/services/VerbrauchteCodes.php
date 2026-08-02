<?php

declare(strict_types=1);

namespace Sartu\Services;

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
    /** Laenger als zwei Zeitschritte muss nichts aufgehoben werden. */
    private const HALTBARKEIT_SEKUNDEN = 120;

    private ZahlenlistenDatei $ablage;

    public function __construct(?string $speicherverzeichnis = null)
    {
        $this->ablage = new ZahlenlistenDatei('zweifaktor', $speicherverzeichnis);
    }

    /**
     * Loest einen Zeitschritt ein.
     *
     * @return bool true, wenn er noch frei war. false bedeutet: schon verwendet.
     */
    public function einloesen(string $benutzerId, int $zeitschritt): bool
    {
        $gespeichert = $this->ablage->lesen($benutzerId);

        if (in_array($zeitschritt, $gespeichert, true)) {
            return false;
        }

        $grenze = time() - self::HALTBARKEIT_SEKUNDEN;
        $behalten = array_values(array_filter(
            $gespeichert,
            static fn (int $schritt) => $schritt * Zweifaktor::PERIODE_SEKUNDEN > $grenze
        ));
        $behalten[] = $zeitschritt;

        $this->ablage->schreiben($benutzerId, $behalten);

        return true;
    }
}
