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
 * Stand A1: Der Kundenbereich hat weiterhin KEINE Route. Der Bedarfsscheck ist oeffentlich
 * und die Anfrageliste ist Adminbereich — beides beruehrt die Mandantentrennung nicht, weil
 * eine Anfrage entsteht, BEVOR es einen Kunden gibt. Die Kundenanmeldung kommt als naechstes;
 * sobald sie die erste Portalroute anlegt, schlaegt der Isolationstest an.
 */

use Sartu\Admin\AnfragenSteuerung;
use Sartu\Admin\AnmeldeSteuerung;
use Sartu\Admin\BetriebSteuerung;
use Sartu\Admin\OeffnungszeitenSteuerung;
use Sartu\Admin\ProjekteSteuerung;
use Sartu\Admin\RechnungenSteuerung;
use Sartu\Admin\RechtstexteSteuerung;
use Sartu\Admin\SetupSteuerung;
use Sartu\Admin\TestmailSteuerung;
use Sartu\Admin\VorschauSteuerung as AdminVorschauSteuerung;
use Sartu\BedarfsscheckSteuerung;
use Sartu\OeffentlicheSeiten;
use Sartu\Website;
use Sartu\Wurzeldateien;
use Sartu\Portal\AnmeldeSteuerung as KundenAnmeldeSteuerung;
use Sartu\Portal\AngebotSteuerung;
use Sartu\Portal\AufgabenSteuerung;
use Sartu\Portal\InhalteSteuerung;
use Sartu\Portal\PortalSteuerung;
use Sartu\Portal\RechnungenSteuerung as KundenRechnungenSteuerung;
use Sartu\Portal\VorschauSteuerung;
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

    // ---------------------------------------------------------- oeffentliche Website (Stufe B)
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/', [Website::class, 'start']),
    // Wurzeldateien (§16). Erzeugt aus der Adressliste, nicht als Datei abgelegt.
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/sitemap.xml', [Wurzeldateien::class, 'sitemap']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/robots.txt', [Wurzeldateien::class, 'robots']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/llms.txt', [Wurzeldateien::class, 'llms']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/leistungen', [Website::class, 'leistungen']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/preise', [Website::class, 'preise']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/ablauf', [Website::class, 'ablauf']),
    // Die fuenf Leistungsseiten (§10). Feste Adressen aus §16, einzeln eingetragen.
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/leistung-webdesign', [Website::class, 'webdesign']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/leistung-texte', [Website::class, 'texte']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/leistung-seo-lokal', [Website::class, 'seoLokal']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/leistung-wartung', [Website::class, 'wartung']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/leistung-portal', [Website::class, 'portal']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/ueber-uns', [Website::class, 'ueberUns']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/kontakt', [Website::class, 'kontakt']),
    new Route(Route::BEREICH_OEFFENTLICH, 'POST', '/kontakt', [Website::class, 'kontaktSenden']),
    // Branchenseiten, Welle 1 (§10a). Vollstaendige Zielseiten mit eingebettetem Konfigurator.
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/website-sanitaer-heizung-klima', [Website::class, 'shk']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/website-elektrotechnik', [Website::class, 'elektro']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/website-dachdecker', [Website::class, 'dachdecker']),
    // Ratgeber und Lexikon (§11a, §12, §13). Feste Pfade vor dem Muster.
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/ratgeber', [Website::class, 'ratgeber']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/ratgeber/{schluessel}', [Website::class, 'ratgeberArtikel']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/lexikon', [Website::class, 'lexikon']),
    new Route(Route::BEREICH_OEFFENTLICH, 'GET', '/lexikon/{schluessel}', [Website::class, 'lexikonBegriff']),

    // ---------------------------------------------------------- Rechtstexte
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
    // Anfragen (§4b.5). Auch hier stehen die festen Pfade vor dem Muster: `{id}` wuerde
    // sonst auf jede Unterseite passen.
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/anfragen', [AnfragenSteuerung::class, 'liste']),
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/anfragen/{id}/export', [AnfragenSteuerung::class, 'exportieren']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/anfragen/{id}/zustand', [AnfragenSteuerung::class, 'zustand']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/anfragen/{id}/notiz', [AnfragenSteuerung::class, 'notiz']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/anfragen/{id}/loeschen', [AnfragenSteuerung::class, 'loeschen']),
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/anfragen/{id}/umwandeln', [AnfragenSteuerung::class, 'umwandelnFragen']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/anfragen/{id}/umwandeln', [AnfragenSteuerung::class, 'umwandeln']),
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/anfragen/{id}', [AnfragenSteuerung::class, 'einzeln']),
    // Projekte und Angebote (§4, §4c, §5.1a).
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/projekte', [ProjekteSteuerung::class, 'liste']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/projekte/{id}/angebot', [ProjekteSteuerung::class, 'angebotAnlegen']),
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/projekte/{id}', [ProjekteSteuerung::class, 'einzeln']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/angebote/{id}/senden', [ProjekteSteuerung::class, 'angebotSenden']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/projekte/{id}/rechnung', [RechnungenSteuerung::class, 'anlegen']),
    // Vorschau, Korrekturrunden, Domainlage und Onlinegang (§5.6a, §5.7, §8.7).
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/projekte/{id}/vorschau', [AdminVorschauSteuerung::class, 'vorschauBereitstellen']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/projekte/{id}/runde', [AdminVorschauSteuerung::class, 'rundeAbschliessen']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/projekte/{id}/zusatzrunde', [AdminVorschauSteuerung::class, 'zusaetzlicheRunde']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/projekte/{id}/abnahme', [AdminVorschauSteuerung::class, 'zurAbnahme']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/projekte/{id}/livegang', [AdminVorschauSteuerung::class, 'livegang']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/projekte/{id}/betriebsbeginn', [AdminVorschauSteuerung::class, 'betriebsbeginn']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/projekte/{id}/domain', [AdminVorschauSteuerung::class, 'domain']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/projekte/{id}/zeiten', [OeffnungszeitenSteuerung::class, 'veroeffentlichen']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/projekte/{id}/pausieren', [AdminVorschauSteuerung::class, 'pausieren']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/projekte/{id}/fortsetzen', [AdminVorschauSteuerung::class, 'fortsetzen']),
    // Rechnungen (§12). Der Zahlungsstatus wird von Hand gesetzt — es gibt hier bewusst
    // KEINE Rueckkehrroute vom Zahlungsdienst.
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/rechnungen', [RechnungenSteuerung::class, 'liste']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/rechnungen/{id}/senden', [RechnungenSteuerung::class, 'senden']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/rechnungen/{id}/zahlung', [RechnungenSteuerung::class, 'zahlungEintragen']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/rechnungen/{id}/stornieren', [RechnungenSteuerung::class, 'stornieren']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/rechnungen/{id}/zahlungslink', [RechnungenSteuerung::class, 'zahlungslink']),
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/rechnungen/{id}', [RechnungenSteuerung::class, 'einzeln']),
    new Route(Route::BEREICH_ADMIN, 'GET', '/admin/testmail', [TestmailSteuerung::class, 'formular']),
    new Route(Route::BEREICH_ADMIN, 'POST', '/admin/testmail', [TestmailSteuerung::class, 'senden']),

    // ---------------------------------------------------------- Kundenanmeldung (§6)
    // ohneAnmeldung: Wer sich anmelden will, ist noch nicht angemeldet. Die Strecke hat
    // stattdessen ihre eigene Begrenzung — 5 Versuche je Adresse und Stunde (§3 Regel 4).
    new Route(Route::BEREICH_PORTAL, 'GET', '/login', [KundenAnmeldeSteuerung::class, 'formular'], true),
    new Route(Route::BEREICH_PORTAL, 'POST', '/login', [KundenAnmeldeSteuerung::class, 'anfordern'], true),
    new Route(Route::BEREICH_PORTAL, 'GET', '/login/{token}', [KundenAnmeldeSteuerung::class, 'einloesen'], true),

    // ---------------------------------------------------------- Kundenbereich
    // Ab hier greift die zentrale Kundenpruefung im Router: Rolle `kunde` UND eine
    // Organisation in der Sitzung UND eine serverseitig gueltige Anmeldung (§3 Regel 1
    // und 2a). Testfaelle 5a, 42 und 45 pruefen diese Liste vollstaendig.
    new Route(Route::BEREICH_PORTAL, 'POST', '/portal/abmelden', [KundenAnmeldeSteuerung::class, 'abmelden']),
    new Route(Route::BEREICH_PORTAL, 'GET', '/portal', [PortalSteuerung::class, 'uebersicht']),
    new Route(Route::BEREICH_PORTAL, 'GET', '/portal/angebot', [PortalSteuerung::class, 'angebot']),
    new Route(Route::BEREICH_PORTAL, 'POST', '/portal/angebot/{id}/annehmen', [AngebotSteuerung::class, 'annehmen']),
    // Aufgaben und Dateien (§8.3, §11). Feste Pfade vor dem Muster.
    new Route(Route::BEREICH_PORTAL, 'GET', '/portal/aufgaben', [AufgabenSteuerung::class, 'liste']),
    new Route(Route::BEREICH_PORTAL, 'POST', '/portal/aufgaben/{id}/abschliessen', [AufgabenSteuerung::class, 'abschliessen']),
    new Route(Route::BEREICH_PORTAL, 'POST', '/portal/aufgaben/{id}/datei', [AufgabenSteuerung::class, 'hochladen']),
    new Route(Route::BEREICH_PORTAL, 'GET', '/portal/aufgaben/{id}', [AufgabenSteuerung::class, 'einzeln']),
    // §11: Auslieferung nur ueber eine Route, die Sitzung UND Organisation prueft.
    new Route(Route::BEREICH_PORTAL, 'GET', '/portal/dateien/{id}', [AufgabenSteuerung::class, 'datei']),
    // Vorschau, Korrekturrunden und Abnahme (§8.4). Die Projektkennung kommt aus der
    // Sitzungsorganisation, nie aus der Adresse — deshalb kein `{projekt}` im Pfad.
    new Route(Route::BEREICH_PORTAL, 'GET', '/portal/vorschau', [VorschauSteuerung::class, 'vorschau']),
    new Route(Route::BEREICH_PORTAL, 'POST', '/portal/vorschau/rueckmeldung', [VorschauSteuerung::class, 'rueckmeldung']),
    new Route(Route::BEREICH_PORTAL, 'POST', '/portal/vorschau/einreichen', [VorschauSteuerung::class, 'einreichen']),
    new Route(Route::BEREICH_PORTAL, 'POST', '/portal/vorschau/abnehmen', [VorschauSteuerung::class, 'abnehmen']),
    new Route(Route::BEREICH_PORTAL, 'GET', '/portal/rechnungen', [KundenRechnungenSteuerung::class, 'liste']),
    new Route(Route::BEREICH_PORTAL, 'GET', '/portal/domain', [VorschauSteuerung::class, 'domain']),
    // Oeffnungszeiten — die eine Pflegefunktion (§8.7), ab Stufe B.
    new Route(Route::BEREICH_PORTAL, 'GET', '/portal/inhalte', [InhalteSteuerung::class, 'formular']),
    new Route(Route::BEREICH_PORTAL, 'POST', '/portal/inhalte', [InhalteSteuerung::class, 'speichern']),
    new Route(Route::BEREICH_PORTAL, 'GET', '/portal/vertrag', [PortalSteuerung::class, 'vertrag']),
    new Route(Route::BEREICH_PORTAL, 'GET', '/portal/hilfe', [KundenRechnungenSteuerung::class, 'hilfe']),
    new Route(Route::BEREICH_PORTAL, 'POST', '/portal/hilfe', [KundenRechnungenSteuerung::class, 'nachrichtSenden']),
    new Route(Route::BEREICH_PORTAL, 'POST', '/willkommen/fertig', [PortalSteuerung::class, 'willkommenFertig']),
    new Route(Route::BEREICH_PORTAL, 'GET', '/willkommen/{nummer}', [PortalSteuerung::class, 'willkommen']),
];
