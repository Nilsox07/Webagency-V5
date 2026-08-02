#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Die Startsperre als Befehl — Website-Lastenheft §14a, Portal-Lastenheft §1.4a.
 *
 * ## Warum das ein Befehl sein muss
 *
 * §14a: „Die Veröffentlichung muss **scheitern**, nicht warnen." Bis zum 02.08.2026 gab es
 * `Startsperre` nur als Anzeige unter `/admin/einstellungen/betrieb`. Eine Liste, die man
 * ansehen **kann**, ist keine Sperre — sie ist eine Warnung mit mehr Schritten.
 *
 * Dieser Befehl gibt **1** zurück, sobald ein Hindernis steht. Wer ihn in den
 * Veröffentlichungsvorgang haengt, kann nicht mehr live gehen, ohne ihn zu bestehen.
 *
 * ## Was er prueft
 *
 * Genau das, was `Startsperre` prueft: leere Pflichtfelder der Betreiberdaten, fehlende
 * Steuernummer **und** Umsatzsteuer-Identifikationsnummer, nicht freigegebene Rechtstexte.
 * Dazu drei Dinge, die nur auf dem Zielserver zu sehen sind und dort am meisten schiefgehen:
 * `APP_ENV`, der Verschluesselungsschluessel und der Ort des Ablageverzeichnisses.
 *
 * ## Was er NICHT prueft
 *
 * TLS, HSTS, SPF, DKIM und den Cron-Eintrag. Das sind Einstellungen des Anbieters, keine
 * der Anwendung — sie stehen als Handgriffe in `LIVEGANG.md`. Ein Befehl, der so tut, als
 * haette er sie geprueft, waere schlimmer als keiner.
 *
 * Aufruf: `php bin/startklar.php`
 */

use Sartu\Helpers\Env;
use Sartu\Services\Startsperre;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$wurzel = require dirname(__DIR__) . '/app/bootstrap.php';
restore_exception_handler();

$hindernisse = (new Startsperre())->hindernisse();

// Drei Werte, die nur auf dem Zielserver stimmen koennen.
$umgebung = Env::get('APP_ENV');

if ($umgebung !== 'production') {
    $hindernisse[] = sprintf(
        'APP_ENV steht auf „%s", nicht auf „production". §1.5 liest den Wert aus der '
        . 'Serverumgebung, nicht aus der .env.',
        $umgebung === null || $umgebung === '' ? 'nichts' : $umgebung,
    );
}

$schluessel = Env::get('ENC_KEY');

if ($schluessel === null || trim($schluessel) === '') {
    $hindernisse[] = 'ENC_KEY ist leer. Ohne ihn laesst sich kein TOTP-Geheimnis mehr lesen.';
}

$ablage = Env::get('STORAGE_DIR');

if ($ablage === null || trim($ablage) === '') {
    $hindernisse[] = 'STORAGE_DIR ist nicht gesetzt.';
} elseif (str_starts_with(realpath($ablage) ?: $ablage, realpath($wurzel . '/public') ?: '')) {
    $hindernisse[] = sprintf(
        'STORAGE_DIR liegt unter dem oeffentlichen Verzeichnis (%s). Hochgeladene Dateien '
        . 'waeren damit ueber den Webserver erreichbar.',
        $ablage,
    );
}

if ($hindernisse === []) {
    fwrite(STDOUT, 'Startklar. Kein Hindernis.' . PHP_EOL);
    exit(0);
}

fwrite(STDERR, sprintf('Nicht startklar — %d Hindernisse:%s', count($hindernisse), PHP_EOL));

foreach ($hindernisse as $hindernis) {
    fwrite(STDERR, '  - ' . $hindernis . PHP_EOL);
}

exit(1);
