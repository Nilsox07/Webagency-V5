#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Vergibt **eine** Belegnummer und schreibt sie nach STDOUT — für Testfall 85.
 *
 * ## Warum das ein eigenes Programm ist
 *
 * Fall 85 verlangt „geprüft unter Nebenläufigkeit, **nicht nacheinander**". Zwei Aufrufe
 * hintereinander im selben Prozess laufen nie gleichzeitig; sie prüfen die Zeilensperre
 * nicht, weil nie zwei Verbindungen um dieselbe Zeile streiten.
 *
 * `tests/NummernkreisTest.php` startet dieses Programm mehrfach **gleichzeitig** über
 * `proc_open`. Das sind echte Betriebssystemprozesse mit je eigener Datenbankverbindung —
 * genau die Lage, für die `SELECT ... FOR UPDATE` da ist.
 *
 * ## Die Pause im Rumpf ist Absicht
 *
 * Ohne sie wäre jeder Vorgang so kurz, dass sich die Prozesse zufällig nicht überschneiden
 * und der Test grün würde, obwohl die Sperre fehlt. Die Pause hält die Transaktion offen
 * und erzwingt das Aufeinandertreffen: Ohne Sperre läsen alle denselben Stand und lieferten
 * dieselbe Nummer.
 *
 * Aufruf: `php tests/hilfsmittel/nummer-vergeben.php RE 2026 50000`
 */

use Sartu\Data\Db;
use Sartu\Helpers\Env;
use Sartu\Services\Nummernkreis;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__, 2) . '/app/bootstrap.php';
restore_exception_handler();

$art = $argv[1] ?? 'RE';
$jahr = (int) ($argv[2] ?? date('Y'));
$pause = (int) ($argv[3] ?? 50000);

// **Die Testdatenbank, nie die Arbeitsdatenbank.** Dieselben Werte wie in
// `tests/Datenbankfall.php`; das Programm legt kein Schema an und räumt keines ab.
Db::setzen(Db::oeffnen(
    Env::get('DB_HOST_TEST', 'db_test') ?? 'db_test',
    Env::get('DB_PORT', '3306') ?? '3306',
    Env::get('DB_NAME_TEST', 'sartu_test') ?? 'sartu_test',
    Env::require('DB_USER'),
    Env::get('DB_PASS', '') ?? '',
));

try {
    $nummer = (new Nummernkreis())->vergeben($art, static function (string $vergeben) use ($pause): string {
        usleep($pause);

        return $vergeben;
    }, $jahr);

    fwrite(STDOUT, $nummer . PHP_EOL);
    exit(0);
} catch (\Throwable $fehler) {
    fwrite(STDERR, get_class($fehler) . ': ' . $fehler->getMessage() . PHP_EOL);
    exit(1);
}
