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

$pfad = Http::pfad();

/*
 * **Nicht jede Adresse bekommt eine Sitzung** — geändert am 15.08.2026.
 *
 * Bis dahin stand hier ein unbedingtes `Sitzung::starten()`. Gemessen: `robots.txt`,
 * `sitemap.xml` und `llms.txt` gingen mit `Set-Cookie: PHPSESSID` und `Cache-Control:
 * no-store` hinaus. Ein Suchdienst bekommt damit bei jedem Abruf ein Cookie und darf die
 * Datei nie zwischenspeichern.
 *
 * Welche Adressen ohne auskommen und warum es **nicht** nur Bedarfsscheck, Anmeldung,
 * Portal und Admin sind, steht an `Sitzung::OHNE_SITZUNG` — Kurzfassung: §4b.7 braucht die
 * **erste** aufgerufene Seite, und die ist bei den meisten Besuchern eine öffentliche.
 */
if (Sitzung::wirdGebraucht($pfad)) {
    Sitzung::starten();

    // Portal-Lastenheft §4b.7: Landeseite, verweisender Host und die Kampagnenkennzeichen
    // stehen in der Adresse der ERSTEN aufgerufenen Seite. Wer sie erst beim Absenden des
    // Bedarfsschecks ausliest, liest nichts mehr. `merken()` schreibt nur beim ersten Aufruf.
    Herkunft::merken($_GET, $_SERVER);
}

$routen = require $wurzel . '/app/routes.php';

(new Router($routen))
    ->behandeln(Http::methode(), $pfad)
    ->senden();
