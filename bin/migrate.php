#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Nachtraegliche Migrationen — Portal-Lastenheft §1.5a.
 *
 * Die Ersteinrichtung bricht bei einer nicht leeren Datenbank ab. Das ist richtig fuer die
 * Erstinstallation und waere ohne diesen zweiten Weg das Ende jeder spaeteren Migration.
 *
 * Kein `up` ueber das Netz: kein Webaufruf, kein Endpunkt unter /api/, keine Schaltflaeche
 * im Adminbereich. Wer migrieren darf, hat ohnehin Dateizugriff.
 *
 * Kein `down`. Schemabefehle loesen in MySQL ein implizites Commit aus — ein Rueckwaerts-
 * befehl waere ein Versprechen, das die Datenbank nicht haelt. Vorwaerts ist ein Befehl,
 * rueckwaerts ist eine Sicherung.
 */

use Sartu\Data\AuditProtokoll;
use Sartu\Data\Db;
use Sartu\Data\MigrationFehler;
use Sartu\Data\Migrator;
use Sartu\Services\Wartungsmodus;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$wurzel = require dirname(__DIR__) . '/app/bootstrap.php';
restore_exception_handler();

$befehl = $argv[1] ?? 'status';
$sicherung = null;

foreach (array_slice($argv, 2) as $argument) {
    if (str_starts_with($argument, '--backup=')) {
        $sicherung = substr($argument, strlen('--backup='));
    }
}

function melden(string $zeile): void
{
    fwrite(STDOUT, $zeile . PHP_EOL);
}

function abbrechen(string $zeile): never
{
    fwrite(STDERR, $zeile . PHP_EOL);
    exit(1);
}

try {
    $migrator = new Migrator(Db::verbindung(), $wurzel . '/migrations');
} catch (Throwable $fehler) {
    abbrechen('Keine Verbindung zur Datenbank: ' . $fehler->getMessage());
}

switch ($befehl) {
    case 'status':
        $eingetragene = $migrator->eingetragene();
        $offene = $migrator->offene();

        melden(sprintf('Eingespielt: %d · offen: %d', count($eingetragene), count($offene)));

        foreach ($eingetragene as $version => $eintrag) {
            melden(sprintf('  [x] %s  %s', $version, $eintrag['applied_at']));
        }

        foreach ($offene as $datei) {
            melden(sprintf('  [ ] %s', $datei['version']));
        }

        break;

    case 'verify':
        try {
            $migrator->pruefsummenPruefen();
        } catch (MigrationFehler $fehler) {
            abbrechen($fehler->getMessage());
        }

        melden('Alle Pruefsummen stimmen mit den Dateien ueberein.');
        break;

    case 'up':
        if (!$migrator->protokolltabelleVorhanden()) {
            abbrechen(
                'In dieser Datenbank gibt es keine Tabelle schema_migrations. Fuer die Erstinstallation '
                . 'ist /admin/setup zustaendig, nicht dieser Befehl.'
            );
        }

        // §1.5a: `up` verlangt eine vorherige Sicherung. Ohne Angabe: Abbruch.
        if ($sicherung === null || trim($sicherung) === '') {
            abbrechen('Bitte den Pfad der Sicherungsdatei angeben: --backup=/pfad/zur/sicherung.sql');
        }

        if (!is_file($sicherung) || filesize($sicherung) === 0) {
            abbrechen(sprintf('Die Sicherungsdatei %s fehlt oder ist leer.', $sicherung));
        }

        $wartung = new Wartungsmodus();
        $wartung->einschalten('Migration laeuft');

        $start = gmdate('Y-m-d H:i:s');

        try {
            $eingespielt = $migrator->offeneEinspielen(static function (string $version): void {
                melden('  eingespielt: ' . $version);
            });
        } catch (MigrationFehler $fehler) {
            // Nach Abbruch bleibt der Wartungsmodus bestehen (§1.5a).
            (new AuditProtokoll())->schreiben(
                aktion: 'migration_abgebrochen',
                objektart: 'schema_migrations',
                neuerWert: $fehler->version,
                grund: $fehler->getMessage(),
                detail: ['start' => $start, 'ende' => gmdate('Y-m-d H:i:s')],
            );

            abbrechen($fehler->getMessage() . PHP_EOL . 'Der Wartungsmodus bleibt aktiv.');
        }

        (new AuditProtokoll())->schreiben(
            aktion: 'migration_gelaufen',
            objektart: 'schema_migrations',
            grund: 'Sicherung: ' . $sicherung,
            detail: ['start' => $start, 'ende' => gmdate('Y-m-d H:i:s'), 'versionen' => $eingespielt],
        );

        $wartung->ausschalten();

        melden($eingespielt === []
            ? 'Es gab nichts einzuspielen.'
            : sprintf('%d Migrationen eingespielt. Der Wartungsmodus ist aufgehoben.', count($eingespielt)));

        break;

    default:
        abbrechen('Bekannte Unterbefehle: status | up --backup=DATEI | verify');
}
