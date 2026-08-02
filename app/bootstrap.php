<?php

declare(strict_types=1);

/**
 * Start der Anwendung — Portal-Lastenheft §1.3: Autoload, Konfiguration, Sitzung,
 * Fehlerbehandlung.
 *
 * Diese Datei liegt in /app und ist damit nicht ueber den Webserver erreichbar. Einstieg
 * fuer alles ist /public/index.php.
 */

use Sartu\Helpers\Env;

$wurzel = dirname(__DIR__);

require $wurzel . '/vendor/autoload.php';

Env::bootstrap($wurzel . '/.env');

date_default_timezone_set('UTC');

// §3 Regel 12: nie Stacktraces nach aussen. Interne Kennung anzeigen, Details ins Log.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

set_exception_handler(static function (Throwable $fehler) use ($wurzel): void {
    $kennung = bin2hex(random_bytes(6));

    error_log(sprintf('[%s] %s in %s:%d', $kennung, $fehler->getMessage(), $fehler->getFile(), $fehler->getLine()));

    $antwort = Sartu\Antwort::html(Sartu\Ansicht::seite('oeffentlich', 'fehler', [
        'titel'   => 'Hier ist etwas schiefgegangen',
        'meldung' => 'Wir konnten die Seite nicht aufbauen. Bitte versuchen Sie es noch einmal.',
        'kennung' => $kennung,
    ]), 500);

    (new Sartu\Router([]))->sicherheitskopfzeilen($antwort)->senden();
});

return $wurzel;
