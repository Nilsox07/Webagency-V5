<?php

declare(strict_types=1);

/**
 * Der einzige Einstieg. /public ist das einzige ueber den Webserver erreichbare Verzeichnis
 * (Portal-Lastenheft §1.3).
 */

use Sartu\Helpers\Http;
use Sartu\Router;
use Sartu\Sitzung;

$wurzel = require dirname(__DIR__) . '/app/bootstrap.php';

Sitzung::starten();

$routen = require $wurzel . '/app/routes.php';

(new Router($routen))
    ->behandeln(Http::methode(), Http::pfad())
    ->senden();
