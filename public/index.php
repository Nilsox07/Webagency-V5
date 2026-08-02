<?php

declare(strict_types=1);

/**
 * Der einzige Einstieg. /public ist das einzige ueber den Webserver erreichbare Verzeichnis
 * (Portal-Lastenheft §1.3).
 */

use Sartu\Helpers\Http;
use Sartu\Router;
use Sartu\Services\Herkunft;
use Sartu\Sitzung;

$wurzel = require dirname(__DIR__) . '/app/bootstrap.php';

Sitzung::starten();

// Portal-Lastenheft §4b.7: Landeseite, verweisender Host und die Kampagnenkennzeichen stehen
// in der Adresse der ERSTEN aufgerufenen Seite. Wer sie erst beim Absenden des Bedarfsschecks
// ausliest, liest nichts mehr. `merken()` schreibt nur beim ersten Aufruf.
Herkunft::merken($_GET, $_SERVER);

$routen = require $wurzel . '/app/routes.php';

(new Router($routen))
    ->behandeln(Http::methode(), Http::pfad())
    ->senden();
