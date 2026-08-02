#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Der taegliche Lauf.
 *
 * | Seit | Was |
 * |---|---|
 * | A0 | abgelaufene Anmeldungen entfernen (§3 Regel 6, Verfallszeit 30 Tage) |
 * | A1 | `leads.source_ip` nach 30 Tagen leeren, faellige Anfragen loeschen (§15.1) |
 *
 * Der Ueberfaelligkeitslauf gehoert nach A2 (`REIHENFOLGE.md`) und wird hier nicht
 * vorbereitet.
 *
 * Ein Fehler in einem Schritt darf die uebrigen nicht verhindern: Wer den Lauf einmal
 * ueberspringt, verschiebt eine Loeschfrist um einen Tag — wer ihn ganz abbrechen laesst,
 * verschiebt sie unbegrenzt.
 */

use Sartu\Data\SitzungsSpeicher;
use Sartu\Services\Loeschlauf;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/app/bootstrap.php';
restore_exception_handler();

$fehler = 0;

try {
    $entfernt = (new SitzungsSpeicher())->abgelaufeneAufraeumen();
    fwrite(STDOUT, sprintf('Abgelaufene Anmeldungen entfernt: %d%s', $entfernt, PHP_EOL));
} catch (\Throwable $ausnahme) {
    ++$fehler;
    fwrite(STDERR, 'Anmeldungen aufraeumen fehlgeschlagen: ' . $ausnahme->getMessage() . PHP_EOL);
}

try {
    $stand = (new Loeschlauf())->ausfuehren();
    fwrite(STDOUT, sprintf(
        'Herkunftsadressen geleert: %d, Anfragen nach Fristablauf geloescht: %d%s',
        $stand['ip_geleert'],
        $stand['geloescht'],
        PHP_EOL,
    ));
} catch (\Throwable $ausnahme) {
    ++$fehler;
    fwrite(STDERR, 'Loeschlauf fehlgeschlagen: ' . $ausnahme->getMessage() . PHP_EOL);
}

exit($fehler === 0 ? 0 : 1);
