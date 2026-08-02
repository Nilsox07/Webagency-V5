#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Der taegliche Lauf. In Stufe A0 raeumt er abgelaufene Sitzungen ab (§3 Regel 6:
 * Verfallszeit 30 Tage).
 *
 * Die Laeufe fuer IP-Loeschung und Loeschfristen gehoeren nach A1, der
 * Ueberfaelligkeitslauf nach A2 (`REIHENFOLGE.md`). Sie werden hier nicht vorbereitet.
 */

use Sartu\Data\SitzungsSpeicher;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/app/bootstrap.php';
restore_exception_handler();

$entfernt = (new SitzungsSpeicher())->abgelaufeneAufraeumen();

fwrite(STDOUT, sprintf('Abgelaufene Anmeldungen entfernt: %d%s', $entfernt, PHP_EOL));
