<?php

declare(strict_types=1);

/**
 * Die Routenliste — eine Quelle fuer den Dispatcher UND fuer den Isolationstest.
 *
 * Portal-Lastenheft §16 Fall 5a: „Der Test durchlaeuft die vollstaendige Routenliste des
 * Kundenbereichs, nicht eine Auswahl. Kommt eine Route hinzu, ohne dass der Test sie kennt,
 * scheitert der Test."
 *
 * Das geht nur mit einer Liste. Zwei Listen — eine zum Ausliefern, eine zum Pruefen — waeren
 * zwei Wahrheiten, und die auseinanderlaufende von beiden ist immer die im Test.
 *
 * Stufe A0: Der Kundenbereich hat noch KEINE Route. Die Kundenanmeldung entsteht in A1
 * (`REIHENFOLGE.md`). Die leere Liste ist deshalb richtig und wird vom Test genau so
 * erwartet — sobald A1 die erste Portalroute anlegt, schlaegt er an.
 */

use Sartu\Admin\AnmeldeSteuerung;
use Sartu\BedarfsscheckSteuerung;
use Sartu\Admin\BetriebSteuerung;
use Sartu\Admin\RechtstexteSteuerung;
use Sartu\Admin\SetupSteuerung;
use Sartu\Admin\TestmailSteuerung;
use Sartu\OeffentlicheSeiten;
use Sartu\Route;

return [
    // ---------------------------------------------------------- Bedarfsscheck (Website §9)
    //
    // Die Reihenfolge ist hier nicht beliebig: `finden()` nimmt den ERSTEN passenden
    // Eintrag, und `/briefing/{nummer}` wuerde auch auf `/briefing/ergebnis` passen. Die
    // festen Pfade stehen deshalb vor dem Muster.
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/briefing', [BedarfsscheckSteuerung::class, 'einstieg']),
    new Route(Route::BEREICH_OEFFENTLICH, 'POST', '/briefing/start', [BedarfsscheckSteuerung::class, 'starten']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/briefing/ergebnis', [BedarfsscheckSteuerung::class, 'ergebnis']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/briefing/kontakt', [BedarfsscheckSteuerung::class, 'kontakt']),
    new Route(Route::BEREICH_OEFFENTLICH, 'POST', '/briefing/absenden', [BedarfsscheckSteuerung::class, 'absenden']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/briefing/danke', [BedarfsscheckSteuerung::class, 'danke']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/briefing/{nummer}', [BedarfsscheckSteuerung::class, 'schritt']),
    new Route(Route::BEREICH_OEFFENTLICH, 'POST', '/briefing/{nummer}', [BedarfsscheckSteuerung::class, 'schrittSpeichern']),

    // ---------------------------------------------------------- oeffentlich
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/impressum', [OeffentlicheSeiten::class, 'impressum']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/datenschutz', [OeffentlicheSeiten::class, 'datenschutz']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/agb', [OeffentlicheSeiten::class, 'agb']),

    // ---------------------------------------------------------- Ersteinrichtung (§1.5)
    // ohneAnmeldung: Zu diesem Zeitpunkt gibt es kein Konto. Die Strecke hat stattdessen
    // ihre eigene, strengere Sperre — siehe InstallationsSperre und Router.
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/setup', [SetupSteuerung::class, 'zeigen'], true),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/setup', [SetupSteuerung::class, 'umgebungBestaetigen'], true),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/setup/datenbank', [SetupSteuerung::class, 'datenbank'], true),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/setup/schluessel', [SetupSteuerung::class, 'schluessel'], true),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/setup/migrationen', [SetupSteuerung::class, 'migrationen'], true),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/setup/mail', [SetupSteuerung::class, 'mail'], true),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/setup/mail-bestaetigen', [SetupSteuerung::class, 'mailBestaetigen'], true),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/setup/betrieb', [SetupSteuerung::class, 'betrieb'], true),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/setup/admin', [SetupSteuerung::class, 'admin'], true),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/setup/abschluss', [SetupSteuerung::class, 'abschluss'], true),

    // ---------------------------------------------------------- Anmeldung
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/anmelden', [AnmeldeSteuerung::class, 'formular'], true),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/anmelden', [AnmeldeSteuerung::class, 'anmelden'], true),
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/anmelden/code', [AnmeldeSteuerung::class, 'codeFormular'], true),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/anmelden/code', [AnmeldeSteuerung::class, 'code'], true),

    // ---------------------------------------------------------- interner Bereich
    // Ab hier greift die zentrale Adminpruefung im Router: Rolle `admin` UND abgeschlossene
    // Zweifaktor-Anmeldung (§3 Regel 2a). Testfälle 43 und 44 pruefen diese Liste vollstaendig.
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/abmelden', [AnmeldeSteuerung::class, 'abmelden']),
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin', [BetriebSteuerung::class, 'uebersicht']),
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/einstellungen/betrieb', [BetriebSteuerung::class, 'formular']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/einstellungen/betrieb', [BetriebSteuerung::class, 'speichern']),
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/rechtstexte', [RechtstexteSteuerung::class, 'liste']),
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/rechtstexte/{slug}', [RechtstexteSteuerung::class, 'einzeln']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/rechtstexte/{slug}', [RechtstexteSteuerung::class, 'speichern']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/rechtstexte/{slug}/freigabe', [RechtstexteSteuerung::class, 'freigabe']),
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/testmail', [TestmailSteuerung::class, 'formular']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/testmail', [TestmailSteuerung::class, 'senden']),

    // ---------------------------------------------------------- Kundenbereich
    // Leer bis A1. Siehe Kopf dieser Datei.
];
