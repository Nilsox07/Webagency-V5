<?php

declare(strict_types=1);

/**
 * Der einzige Einstieg. /public ist das einzige ueber den Webserver erreichbare Verzeichnis
 * (Portal-Lastenheft §1.3).
 */

use Sartu\Helpers\Http;
use Sartu\Router;

$wurzel = require dirname(__DIR__) . '/app/bootstrap.php';

/*
 * **Hier startet keine Sitzung mehr** — geändert am 16.08.2026.
 *
 * Bis zum 15.08.2026 stand hier ein unbedingtes `Sitzung::starten()`, danach eines mit einer
 * Negativliste aus drei Wurzeldateien. In beiden Fassungen setzte jede öffentliche HTML-Seite
 * ein `PHPSESSID`-Cookie — die Startseite, `/preise`, das Impressum.
 *
 * § 25 Abs. 2 Nr. 2 TDDDG erlaubt die Speicherung auf dem Endgerät ohne Einwilligung nur,
 * soweit sie für einen **ausdrücklich gewünschten Dienst** unbedingt erforderlich ist. Eine
 * Leseseite ist keiner. Die Entscheidung steht deshalb jetzt an der Route (`Route::$sitzung`)
 * und wird im Router getroffen, sobald bekannt ist, welche Route gemeint ist.
 *
 * **`Herkunft::merken()` ist damit ebenfalls hier verschwunden.** Es brauchte die Sitzung auf
 * der ersten aufgerufenen Seite und war der einzige Grund, warum eine Leseseite eine bekam.
 * Aufgerufen wird es jetzt beim Start des Bedarfsschecks; was das an Zuordnung kostet, steht
 * im Kopf von `Herkunft`.
 */

$routen = require $wurzel . '/app/routes.php';

(new Router($routen))
    ->behandeln(Http::methode(), Http::pfad())
    ->senden();
