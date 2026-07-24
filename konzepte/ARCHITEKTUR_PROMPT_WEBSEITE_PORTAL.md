# Architektur-Prompt: Webseite & Portal (nachgereichte Prompt-Anweisung)

> **Kontext-Hinweis (vom Auftraggeber):** Diese Anweisung wurde beim Projektstart
> vergessen und betrifft die **Architektur von Webseite und Portal**. Sie ist in die
> Prüfung mit aufzunehmen.
>
> Der Prompt ist ursprünglich für ein **anderes** Projekt namens „Shopkonform"
> formuliert. **Shopkonform selbst ist NICHT Gegenstand dieses Projekts und wird
> nicht bearbeitet.** Relevant für SARTU ist ausschließlich die hier beschriebene
> **Architektur-Methodik**: saubere, modulare PHP-/HTML-Struktur, zentrale
> Layouts/Partials/Komponenten, datengetriebene Seiten-Templates, kein Framework,
> lauffähig auf einfachem Shared-Hosting, Erhalt von URLs/SEO. Wo „Shopkonform",
> `/module/`, `/branchen/`, `sk-v20`/`sk-v26`/`sk-v38` o. Ä. genannt sind, dient das
> nur als Beispielkontext des Ursprungsprojekts.

---

Du arbeitest im bestehenden Projekt „Shopkonform".

**Wichtige Klarstellung:**
Mit „Module" meine ich in dieser Aufgabe technische Code-Module, Templates, Komponenten, Partials, Services und Datenstrukturen. Die bestehenden Verkaufsseiten unter /module/ sind inhaltliche Lösungsseiten und müssen als URLs und Inhalte erhalten bleiben.

**Ziel:**
Refaktoriere die gewachsene Shopkonform-Webseite in eine saubere, wartbare, modulare PHP-/HTML-Struktur, ohne sichtbares Design, Inhalte, URLs, SEO, Datenbankanbindung, Formulare, Admin-Bereich oder API-Endpunkte zu zerstören.

**Aktueller Projektkontext:**
- Die Webseite besteht aus vielen HTML-Seiten und mehreren PHP-Bereichen.
- Wichtige Bereiche sind unter anderem:
  - /assets/
  - /api/
  - /admin/
  - /dashboard/
  - /module/
  - /branchen/
  - /blog/
  - /wissen/
  - /score/
  - /rechtliches/
- Es gibt bereits eine MySQL-Anbindung über /api/bootstrap.php und /api/db.php.
- Der Admin-Bereich nutzt bereits /admin/_layout.php.
- Viele öffentliche HTML-Seiten enthalten wiederholte Bereiche wie Header, Navigation, Footer, CTA-Blöcke, Meta-Daten, JSON-LD, Preisboxen, Karten, Hero-Bereiche und SEO-Blöcke.
- Diese Wiederholungen sollen nicht mehr auf hunderten Einzelseiten manuell gepflegt werden müssen.

**Arbeitsweise:**
1. Analysiere zuerst die bestehende Projektstruktur vollständig.
2. Erstelle eine Datei REFACTORING_PLAN.md mit:
   - aktueller Struktur
   - erkannter Wiederholung
   - vorgeschlagener Zielstruktur
   - Migrationsreihenfolge
   - Risiken
   - Testplan
3. Setze danach die Refaktorierung in kleinen, nachvollziehbaren Schritten um.
4. Mache keine komplette Neuentwicklung und kein Redesign.
5. Ändere keine Zugangsdaten, keine Live-Konfiguration und führe keinen Upload aus.
6. Bestehende öffentliche URLs müssen erreichbar bleiben.
7. Bestehende Canonical-URLs, Meta Titles, Meta Descriptions, OG-Daten, JSON-LD und H1-Struktur dürfen nicht versehentlich zerstört werden.
8. Bestehende API-Endpunkte und Admin-Funktionen müssen weiterhin funktionieren.
9. Die Datenbankanbindung über die vorhandenen API-Dateien muss erhalten bleiben.
10. Keine Secrets, Passwörter, Tokens oder Datenbankzugänge in das Repository schreiben.

**Gewünschte Zielstruktur:**
Erstelle eine modulare Struktur, zum Beispiel:

```
/app/
  bootstrap.php
  helpers/
    html.php
    url.php
    seo.php
  data/
    navigation.php
    footer.php
    modules.php
    industries.php
    prices.php
    pages.php
  views/
    layouts/
      frontend.php
      dashboard.php
    partials/
      head.php
      header.php
      footer.php
      mobile-nav.php
      cookie-banner.php
      cta-footer.php
    components/
      hero.php
      price-card.php
      solution-card.php
      industry-card.php
      faq.php
      seo-answer-block.php
      seal-card.php
      breadcrumb.php
    pages/
      home.php
      prices.php
      demo.php
      module-detail.php
      industry-detail.php
      blog-detail.php
      legal-page.php
```

Die genaue Struktur darfst du sinnvoll anpassen, aber sie muss klar getrennt sein nach:
- Layout
- wiederverwendbaren Komponenten
- Seitendaten
- Hilfsfunktionen
- API/Admin/Dashboard

**Wichtige Regeln für die Umsetzung:**
- /api/ darf nicht unnötig umgebaut werden. Nur verbessern, wenn es für die Modularisierung nötig ist.
- /admin/ hat bereits ein eigenes Layout. Dieses darf nur vorsichtig angepasst werden.
- /dashboard/ darf modularisiert werden, muss aber Login- und Sessionlogik behalten.
- /assets/style.css und /assets/script.js dürfen aufgeteilt werden, aber nur, wenn dadurch keine bestehenden Styles oder Funktionen verloren gehen.
- Bestehende Klassen wie sk-v20, sk-v26, sk-v38 usw. dürfen nicht einfach entfernt werden, solange sie für das Design benötigt werden.
- Header, Footer, Navigation, mobile Navigation, Footer-CTA, Cookie-Hinweis, Meta-Head und wiederkehrende Karten sollen zentral gepflegt werden.
- Die vielen Lösungsseiten unter /module/ sollen nach Möglichkeit aus einer zentralen Datenstruktur und einem gemeinsamen Template erzeugt oder gerendert werden.
- Die Branchenseiten unter /branchen/ sollen ebenfalls nach Möglichkeit aus Datenstruktur + Template bestehen.
- Preise, Mindest-Abo, CTA-Ziele, Modulnamen und Brancheninformationen sollen nicht mehr hart auf vielen Seiten verstreut sein.
- Keine bestehenden Inhalte kürzen oder inhaltlich umformulieren, außer es ist technisch notwendig.
- Keine rechtlichen Aussagen verschärfen oder neue Rechtsberatung formulieren.
- Keine neuen externen Abhängigkeiten einführen, wenn es mit einfachem PHP lösbar ist.
- Kein Laravel, Symfony, WordPress oder anderes Framework einführen, außer es ist absolut zwingend und vorher ausführlich begründet.
- Der Umbau soll auf normalem PHP-Hosting lauffähig bleiben.

**Besonderes Thema .html-URLs:**
Viele Seiten liegen aktuell als .html-Dateien vor. Die bestehenden .html-URLs müssen erhalten bleiben.
Prüfe zuerst, welcher Ansatz am sichersten ist:

- **Option A:** Öffentliche .html-Dateien bleiben bestehen und werden künftig aus modularen Templates generiert.
- **Option B:** Seiten werden intern über PHP gerendert, aber alte .html-URLs bleiben per Rewrite erreichbar.
- **Option C:** Ein anderer sauberer Ansatz, der keine SEO-Verluste und keine kaputten Links verursacht.

Wähle den sichersten Ansatz für dieses Projekt und dokumentiere die Entscheidung in REFACTORING_PLAN.md.

**Konkrete Refactoring-Aufgaben:**
1. Zentrale Frontend-Layout-Datei erstellen.
2. Zentrales Head-Partial erstellen: title, description, canonical, robots, theme-color, Open Graph, Favicon, Fonts, CSS-Version, JSON-LD.
3. Zentrales Header-Partial erstellen: Logo, Hauptnavigation, Login-Link, CTA, mobile Navigation, aktiver Navigationspunkt.
4. Zentrales Footer-Partial erstellen: Footer-CTA, Plattform-Links, Vertrauen-Links, Ökosystem-Links, Rechtliches, Copyright.
5. Wiederverwendbare Komponenten erstellen: Hero, Preisbox, Lösungskarte, Branchenkarte, FAQ-Block, SEO-Antwortblock, CTA-Leiste, Prüfsiegel-Karte, Breadcrumb.
6. Zentrale Datenstrukturen erstellen: Navigation, Footer, Preise, Lösungen unter /module/, Branchen unter /branchen/, wichtige Seiten-Metadaten.
7. Mindestens folgende Seitentypen als Templates abbilden: Startseite, Preisseite, Lösungsübersicht, Lösungsdetailseite, Branchenübersicht, Branchendetailseite, Blog-/Wissensseite, Rechtliche Seite.
8. Bestehende API- und Datenbanklogik erhalten: /api/bootstrap.php, /api/db.php, /api/auth.php, CSRF, Rate-Limit, Sessions, Login/Logout, Registrierung, Kontakt/Lead, Newsletter, Passwort-Reset.
9. Admin-Bereich nicht zerstören: /admin/_layout.php beibehalten oder kontrolliert verbessern, Admin-Navigation zentral lassen, Rollenlogik und Superadmin-Prüfung erhalten.
10. Dashboard modularisieren, aber Loginpflicht und Userdaten erhalten.

**Qualitätssicherung nach jedem größeren Schritt:**
- Führe php -l für alle PHP-Dateien aus.
- Prüfe, dass alle internen Links noch funktionieren.
- Prüfe, dass jede HTML/PHP-Seite genau eine H1 hat.
- Prüfe, dass sitemap.xml keine toten lokalen URLs enthält.
- Prüfe, dass robots.txt, llms.txt und sitemap.xml weiterhin sinnvoll sind.
- Prüfe, dass keine Zugangsdaten im Projekt liegen.
- Prüfe, dass Formulare weiterhin die richtigen Endpunkte nutzen.
- Prüfe, dass CSRF-Schutz nicht entfernt wurde.
- Prüfe, dass Startseite, Preisseite, Modulübersicht, Branchenseite, Demo, Login, Registrierung, Admin-Login und Dashboard aufrufbar bleiben.
- Prüfe mobile Navigation und Cookie-Hinweis.
- Prüfe, dass die Seite ohne JavaScript weiterhin grundlegend nutzbar bleibt.

**Erwartetes Ergebnis:**
- Die Webseite sieht nach außen möglichst gleich aus.
- Bestehende URLs bleiben erhalten.
- SEO-Daten bleiben erhalten oder werden zentral sauber gepflegt.
- Header, Footer, Navigation, CTA-Blöcke, Karten, Preisboxen und SEO-Blöcke sind nicht mehr auf vielen Seiten dupliziert.
- Neue Seiten können künftig über Datenstruktur + Template angelegt werden.
- Lösungsseiten und Branchenseiten können künftig viel einfacher gepflegt werden.
- Admin, API, Login, Dashboard und MySQL-Anbindung funktionieren weiterhin.
- Der Code ist verständlicher, besser wartbar und weniger fehleranfällig.

**Wichtig:**
Arbeite nicht blind alle Dateien gleichzeitig um. Beginne mit einem Prototyp:
1. Startseite
2. Preisseite
3. Modulübersicht
4. eine Moduldetailseite
5. Branchenübersicht
6. eine Branchendetailseite
7. eine rechtliche Seite

Wenn diese Prototypen stabil funktionieren, übertrage das Muster auf die restlichen Seiten.

Am Ende zusätzlich erstellen:
- REFACTORING_SUMMARY.md mit allen Änderungen
- MIGRATION_NOTES.md mit Hinweisen für spätere Pflege
- eine kurze Anleitung, wie künftig eine neue Lösung, eine neue Branche oder eine neue Inhaltsseite angelegt wird
- eine Liste aller Dateien, die geändert oder neu erstellt wurden
