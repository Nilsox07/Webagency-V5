# LASTENHEFT — SARTU MARKETING-WEBSITE + EIGEN-ADMIN (Auftrag an Codex)

Dieses Dokument ist die **einzige Wahrheitsquelle**. Steht etwas hier nicht, wird es **nicht dazuerfunden**, sondern als offener Punkt im Schluss-Report markiert. Wo eine Entscheidung „offen" ist, triffst **du** sie begründet selbst — du fragst **nicht** nach.

> **Scope-Grenze (wichtig):** Dieses Lastenheft umfasst die **öffentliche Marketing-Website von Sartu** plus einen **Eigen-Admin**, mit dem der Betreiber (eine Person) Inhalte/SEO/Preise selbst pflegt. Das **Kundenportal** (Login-Bereich für Sartu-Kunden, Projektabwicklung, Zahlungen) ist ein **separates Projekt** und NICHT Teil dieses Auftrags. Baue hier keine Kunden-Accounts, keine Projektverwaltung, keine Zahlungsabwicklung.

---

## 0. ARBEITSWEISE (zuerst lesen, bindend)

- **Lauf komplett durch. Keine Rückfragen, keine Zwischenstopps, keine Freigabe abwarten.** Triff alle Entscheidungen selbst und sinnvoll. Bau die ganze Seite fertig.
- **Beantworte alle deine eigenen Fragen selbst.** Wenn dir eine Information fehlt, die nicht in diesem Dokument steht, triff eine begründete, branchenübliche Annahme, kennzeichne sie im Schluss-Report und mach weiter. Stoppe nie, um zu fragen.
- **Einzige Dokumentation vor dem Bauen:** Schreib in ein paar Sätzen deine Design-Entscheidungen nieder (Typo-Wahl, Layout-Richtung, ob du Scroll-Animationen einsetzt und warum, Sektions-Aufbau der Startseite) — mit kurzer Begründung, warum das zur Zielgruppe (siehe Abschnitt 1) passt. Danach **ohne Unterbrechung** weiterbauen.
- **Prüfe deine eigene Arbeit fortlaufend und aktiv:** Nach jeder fertigen Seite durchdenken — lädt sie? Tote Links? Sendet das Formular? Stimmt die Optik auf Mobil? Genau 1 H1? Gefundene Fehler **sofort selbst beheben**, nicht ankündigen, nicht fragen.
- **Beweis-Report am Ende (Pflicht):** Liste auf, was gebaut wurde — pro Punkt ein Beleg (Datei + Zeilen, `grep`-Treffer oder Commit-Hash). „Fertig" ohne Beleg zählt nicht.
- **Selbst-Audit vor dem Schluss (Pflicht):** Geh die Abnahme-Checkliste (Abschnitt 14) Punkt für Punkt durch UND suche darüber hinaus aktiv nach Lücken (fehlende Seite, toter Link, ungeschütztes Formular, vergessener Rechts-Baustein, Preis hart kodiert statt aus zentraler Quelle). Behebe alles Gefundene sofort selbst. **Wiederhole die Audit-Runde, bis jeder Punkt mit Beleg grün ist und keine neue Lücke mehr auftaucht.** Erst dann gilt das Projekt als fertig.
- **Was du nicht live testen kannst (SMTP-Mailversand, FTP-Upload), klar kennzeichnen:** Code sauber bauen, im Report „nicht live getestet — nach Upload zu prüfen" vermerken, in der README eine Test-Anleitung geben. Nicht „funktioniert" behaupten, was nicht getestet werden konnte.
- **Schluss-Zusammenfassung:** Was ist fertig, was ist offen, was ist das **schwächste Teil** des Ergebnisses.

---

## 1. WAS SARTU IST (Marke, Zielgruppe, Haltung) — FESTE FAKTEN, NICHT RECHERCHIEREN

Anders als bei einem generischen Auftrag steht hier alles fest. Du recherchierst **nicht** die Branche — du baust die Sartu-Marke nach den folgenden Vorgaben.

**Sartu** ist eine **Festpreis-Webagentur** für kleine lokale Unternehmen in Deutschland (Handwerk, Gastronomie, Praxen, lokaler Handel, Dienstleister, Immobilien, Coaching). Betreiber: Nils, Raum Dresden, Einzelunternehmer.

- **Zielgruppe:** lokale Kleinunternehmer, oft 40+, **technikfern**, abgeschreckt von teuren Agenturen (Fachsprache, Stundenzettel) und von Baukästen (selbst machen, sieht billig aus). Schmerzpunkte: Angst vor versteckten Kosten, keine Zeit, kein Design-Wissen, will „dass es einfach gemacht wird".
- **Haupt-Gerät der Besucher:** überwiegend **Smartphone** → Mobile-first ist Pflicht, Desktop danach.
- **Eine Haupt-Handlung:** Besucher soll das **Briefing starten (Lumi, siehe Abschnitt 6)** oder zur **Preisseite** gehen und am Ende eine **unverbindliche Angebotsanfrage** abschicken. Kein Kauf, kein Vertrag auf der Seite.
- **Tonalität:** **du-Form, Klartext**, ehrlich, ermutigend, nie drängend. Kein Agentur-Jargon, kein Hype, keine Buzzwords. „Trabi statt Ferrari" — solide, transparent, ohne Brimborium.
- **Kernversprechen, die überall durchscheinen müssen:**
  1. „Du lieferst nur Stichpunkte — **alle Texte schreiben wir**."
  2. „**Festpreis** — keine Überraschungen, keine Stundenfallen."
  3. „**Kontaktlos** — ohne Pflichttermine, alles digital."
  4. „**Deutsche Datenhaltung, DSGVO-konform.**"
  5. „**Geld-zurück-Garantie** auf den ersten Entwurf."
- **Vertrauenssignale prominent:** Festpreis-Versprechen, „wir schreiben die Texte", Geld-zurück-Garantie, deutsche Server/DSGVO, fester Ansprechpartner.

**Verbotene Wörter** (rechtlich/markenseitig, nie verwenden): „rechtssicher", „abmahnsicher", „geprüft" / „Audit" (als Werbeversprechen), „angefangene Stunde" / „pro angefangene Stunde".

---

## 2. INHALT & SEITENSTRUKTUR

**Sitemap (Soll):**
- **Startseite** (`/`)
- **Leistungen** (Übersicht, `/leistungen`)
- **Preise** (`/preise`)
- **Ablauf** (`/ablauf` — wie eine Zusammenarbeit abläuft, Schritt für Schritt)
- **Über uns** (`/ueber-uns`)
- **Kontakt** (`/kontakt`)
- **Briefing** (`/briefing` — der Lumi-Funnel, Abschnitt 6)
- **Leistungs-Unterseiten** (je eigene SEO-Landingpage):
  - `/leistung-webdesign` (Website-Erstellung)
  - `/leistung-seo` (SEO-Betreuung)
  - `/leistung-lokales-seo` (lokale Sichtbarkeit / Google-Unternehmensprofil)
  - `/leistung-texte` (Texterstellung)
  - `/leistung-logo` (Logo & Branding)
  - `/leistung-wartung` (Rundum-Schutz / Care)
- **Ratgeber/Blog** (SEO-Content, im Admin pflegbar):
  - `/ratgeber` (Übersicht)
  - Start-Artikel: `/ratgeber-website-kosten`, `/ratgeber-foerderung`, `/ratgeber-bfsg`, `/ratgeber-onepager`
- **Rechtliches:** `/impressum`, `/datenschutz`, `/agb`

**Regeln zur Struktur:**
- **Sprechende URLs ohne Endung:** `/preise`, nicht `/preise.php`. Umsetzung über `.htaccess` (mod_rewrite). Ist mod_rewrite aus → im Report melden, NICHT still auf `.php`-URLs ausweichen.
- Jede Seite hat **genau ein klares Ziel** und einen sichtbaren Call-to-Action (Richtung Briefing oder Preise).
- **Eigene gestaltete 404-Seite** mit Link zurück, über `.htaccess` eingebunden.
- **Favicon** in den nötigen Größen anlegen und einbinden.
- Es gibt eine Vorgängerseite. **Alte URLs** (Liste liefere ich beim Go-live nach — Platzhalter in `.htaccess` mit Kommentar anlegen) per **301-Weiterleitung** auf die neuen lenken. Falls beim Bau keine Altliste vorliegt: Mechanik vorbereiten, im Report als „Altlinks nachzutragen" vermerken.
- **Leistungs-Unterseiten-Template (5 Abschnitte):** Hero (eine H1 mit Keyword + kurze Stichwort-Chips) → „Was du bekommst" (Antwort-zuerst-Absatz, in dem Keyword + Sartu + ab-Preis im ersten Satz stehen, + eine „Auf einen Blick"-Karte) → Detail-Abschnitt → Paket-Brücke (Verweis/Link auf Preise) → FAQ (mit FAQPage-JSON-LD) + Abschluss-CTA.
- Platzhalter-Texte **realistisch lang** schreiben (keine „Lorem Ipsum"-Blöcke), inhaltlich an den Sartu-Fakten orientiert — der Betreiber ersetzt sie später im Admin.

---

## 3. DESIGN-SYSTEM

- **Farben sind vorgegeben** (Wiedererkennung der Marke), alles andere wählst du:
  - Dunkel-/Navy-Basis: `#0a0f1c` (Haupt-Dunkel), `#0b1426` (Navy), `#0d1424` (Dunkel-Variante)
  - Signal-Akzent (Lime): `#aef000` (Haupt-Akzent), `#9fdb00` (kräftigere Variante)
  - Hell-Flächen: `#f4f6f8`, `#eef1f4`, Weiß `#ffffff`
  - Text dunkel `#0d1320`, Text hell `#c4ccd8`, gedämpft `#6b7686` / `#8a94a6`
  - Rahmen hell `#e3e7ec`
  - **Charakter:** dunkles Navy + kräftiger Lime-Akzent als Signatur, viel Weißraum, hohe Lesbarkeit, premium-schlicht. Du darfst die Abstufungen verfeinern/erweitern, aber **Navy + Lime als Wiedererkennung bleibt**.
- **Du wählst** Schriften, Schriftgrößen-Skala, Layout-Raster, Sektions-Aufbau, Bildsprache, Button-Stil — passend zur Zielgruppe (seriös, klar, vertrauenswürdig, nicht verspielt-effekthascherisch; die Zielgruppe ist 40+ und technikfern). Begründe die Wahl kurz vorab.
- **Scroll-Animationen / Bewegungseffekte: deine Entscheidung.** Wenn du sie einsetzt, dann dezent, performant und barrierearm (`prefers-reduced-motion` respektieren). Begründe vorab, ob und warum.
- **Technisch zwingend:** **alle** Farben, Schriftarten, Schriftgrößen, Abstände, Button-Stile, Eckenradien, max. Inhaltsbreite als **CSS-Variablen in EINER zentralen Datei** (`assets/theme.css` bzw. `:root{}`). Ein Wert dort geändert → ganze Seite ändert sich. Diese Werte sind später **im Admin** über Felder editierbar (Abschnitt 4).
- **Mobile-first** entwickeln, dann auf Desktop hoch. Keine festen Pixelbreiten, die auf dem Handy brechen.
- Kontraste **WCAG-tauglich** (Text/Hintergrund mind. 4.5:1) — auch wegen der älteren Zielgruppe.
- **Keine schweren Frameworks** (kein Bootstrap-Vollpaket, kein jQuery). Eigenes, schlankes CSS. Kein CSS/JS-Framework, das einen Build-Schritt braucht.

---

## 4. EIGEN-ADMIN (Betreiber pflegt die Seite selbst — eine Person)

Zweck: Der Betreiber soll **ohne Entwickler/KI** Inhalte, SEO und zentrale Werte ändern können. Das ist KEIN Mehrbenutzer-System, KEIN Kundenbereich.

**Funktionsumfang:**
- **Inhalts-Editor pro Seite** (WYSIWYG + HTML-Ansicht): Überschriften, Fließtexte, Button-Texte, Bilder, Links jeder Seite bearbeiten. Liefere einen bewährten, schlanken Editor als **lokale Datei** mit (kein Composer/Node, kein CDN). 
- **Design-Werte über einfache Felder:** die CSS-Variablen aus Abschnitt 3 (Farben, Schriftwahl aus einer kuratierten Liste, Basis-Schriftgröße, Eckenradius, max. Breite, Button-Farbe + Hover) editierbar. Vorschau wünschenswert.
- **Zentrale Preis-/Leistungsdaten editierbar** (siehe Abschnitt 5): Pakete, Care-Stufen, Add-ons, SEO-Stufen — Name + Preis. Diese Daten sind die **einzige Quelle** für alle Preisangaben auf der Seite UND im Lumi-Funnel. (Kein Preis darf irgendwo im HTML hart stehen.)
- **Blog/Ratgeber-Verwaltung:** Artikel anlegen/bearbeiten/löschen (Titel, Inhalt, SEO-Felder, Veröffentlichungsdatum).
- **Bilder hochladen/austauschen** (Medienverwaltung, einfach).
- **SEO pro Seite** setzbar: `<title>`, Meta Description, Canonical, `noindex`-Schalter, OG-Titel/-Beschreibung/-Vorschaubild, JSON-LD (siehe Abschnitt 9).
- **Kontakt-/Impressumsdaten** zentral pflegbar (Name, Adresse, Telefon, E-Mail, USt-ID) — speist Impressum, Footer, LocalBusiness-JSON-LD.

**Grenze (für Einfachheit + Sicherheit):** voller **Inhalts**-Editor ja, aber **kein** freier Drag-&-Drop-Layout-Baukasten. Das Seitengerüst (Aufbau, Navigation, Sektions-Reihenfolge) steht im Code; der Betreiber füllt/ändert Inhalte und Design-Variablen, verschiebt aber keine Bausteine frei.

**Sicherheit des Admin (Pflicht — öffentlich erreichbar auf Shared-Hosting):**
- Login mit **gehashtem** Passwort (`password_hash`/`password_verify`), nie Klartext.
- **Admin nur über HTTPS**, HTTPS erzwingen.
- **Brute-Force-Bremse** (Login-Versuche begrenzen, z. B. Verzögerung/Sperre nach X Fehlversuchen).
- **CSRF-Token** bei allen Admin-Formularen.
- **HTML-Editor absichern (kritisch):** gespeichertes HTML serverseitig durch einen **Whitelist-Filter** (erlaubte Tags/Attribute) laufen lassen, bevor es ausgegeben wird — sonst ist der Editor ein XSS-Tor. Niemals ungefiltertes HTML speichern/ausgeben.
- **Datei-Uploads:** nur erlaubte Bildtypen, Größe begrenzt, Speicherung außerhalb des ausführbaren Pfads, keine ausführbaren Dateien.
- **Speicherung als Flat-File (JSON)** — keine Datenbank für die Webseiteninhalte nötig (Statistik bringt ggf. ihre eigene mit, Abschnitt 7). JSON-Dateien per `.htaccess` vor direktem Abruf schützen.
- Admin-Pfad nicht trivial erraten lassen (z. B. nicht `/admin` als einzige Hürde — Login bleibt die echte Sicherung, aber dokumentiere den Pfad in der README).

---

## 5. PREISE / PAKETE / LEISTUNGEN (FESTE FAKTEN — als zentrale Datenquelle anlegen)

Lege diese Daten als **eine zentrale JSON-Datei** an (im Admin editierbar). **Alle** Preisangaben auf der Seite und im Lumi-Funnel ziehen ausschließlich daraus. Alle Preise **netto**, Hinweis „zzgl. MwSt." wo Preise gezeigt werden.

### 5.1 Pakete (einmalig, Festpreis) + Pflicht-Care
| Paket | Einmalig | Pflicht-Care/Monat | Umfang | Korrekturrunden |
|---|---|---|---|---|
| **Start** | 1.290 € | Care S 49 € | One-Pager (1 Seite) | 2 |
| **Wachstum** | 2.990 € | Care M 99 € | bis 8 Seiten | 3 |
| **Platzhirsch** | 5.990 € | Care L 249 € | bis 20 Seiten (inkl. Team/Jobs) | 4 |
| **Sonderprojekte** | ab 9.990 €, individuell | individuell | nach Angebot | — |

**In JEDEM Paket inklusive:** komplette Texterstellung aus Stichpunkten (300–500 Wörter/Seite, je 2 Korrekturschleifen) · datenschutzkonforme Besucher-Statistik · mobil-optimiert · DSGVO-konform.
**Ab Wachstum:** Inhalte selbst änderbar.
**Platzhirsch zusätzlich:** lokale Einrichtung + Google-Unternehmensprofil, Newsletter inklusive, Neuigkeiten-/Projekte-Bereich.
**Querregeln:** jede weitere Seite **199 € inkl. Text** (Deckel 20 Seiten → darüber Sonderprojekt) · Zahlung in Meilensteinen · **Geld-zurück-Garantie auf den 1. Entwurf** · Care-Mindestlaufzeit 12 Monate (Jahreszahlung), danach monatlich kündbar.

### 5.2 Rundum-Schutz (Care, monatlich, Pflicht zu jeder Website)
- **Care S — 49 €/Mon.:** Hosting, Sicherheits-Updates, Backups, technischer Betrieb.
- **Care M — 99 €/Mon.:** wie S + **30 Änderungsminuten/Monat**, schnellere Reaktion.
- **Care L — 249 €/Mon.:** wie M + **90 Änderungsminuten/Monat**, Priorität.
Positionierung: „gehört dazu". Minuten verfallen monatlich, zählen je Sprachversion.

### 5.3 Add-ons / Extras (einmalig, sofern nicht anders vermerkt)
**Branding (3 Stufen, schließen sich gegenseitig aus — druckfertige Dateien, KEIN Druck/Versand):**
- **Logo — 490 €:** 3 Entwürfe, 2 Korrekturrunden, alle Formate (SVG/EPS/PDF/PNG/JPG), Mini-Styleguide, volle Rechte.
- **Logo + Marke — 890 €:** 4 Entwürfe, 3 Runden, + Farbsystem + Schriften + mehrseitiger Styleguide.
- **Marken-Paket — 1.490 €:** wie Logo + Marke + Visitenkarte + Briefbogen + Social-Media-Profilbild/Header als druckfertige Dateien + kompletter Marken-Styleguide.

**Weitere Extras:**
- **Online-Terminbuchung — 290 €** (Buchungstool, 1 Kalender/Mitarbeiter, Bestätigungs-/Erinnerungsmail).
- **KI-Chat-Assistent — 990 € einmalig + 79 €/Mon.** (trainiert auf Website-Inhalte, Fair Use 500 Gespräche/Mon., 12 Mon. Mindestlaufzeit; verbindliche Auskünfte bestätigt der Kunde persönlich).
- **Mehrsprachigkeit — +40 % je Sprache** (komplette Übersetzung, Sprachumschalter, hreflang; Rechtstexte bleiben deutsch).
- **Express-Lieferung — +50 % (mind. 390 €)** (One-Pager in 5, Einzelseite/Text in 2 Werktagen ab vollständiger Lieferung).
- **Newsletter-Anmeldung — 290 €** (im Platzhirsch inklusive).

**„Festpreis im Angebot"-Wünsche** (ohne öffentlichen Preis, nur als anfragbar darstellen): Geschützter Kundenbereich/Login · Shop-Funktionen · Schnittstellen/Anbindungen.

### 5.4 Über Kontingent
- Zusätzliche/nachträgliche Texte: **120 € je Seite** (300–500 Wörter, 2 Korrekturschleifen).
- Sonstige Arbeit: **150 €/Std**, minutengenau im **5-Minuten-Takt** (nie „angefangene Stunde").

### 5.5 SEO-Betreuung (monatlich, nur für Sartu-Websites, Mindestlaufzeit 3 Monate, KEINE Ranking-Garantie)
| | **Lite 149 €** | **Pro 390 €** | **Premium 790 €** |
|---|---|---|---|
| Google-Profil-Pflege (1 Beitrag/Mon., Bewertungsantwort ≤ 2 Werktage) | ✓ | ✓ | ✓ |
| Title/Meta alle Seiten · Klartext-Monatsreport | ✓ | ✓ | ✓ |
| Keyword-Tracking | 20 | 50 | 100 |
| Seiten-Refresh | 1/Quartal | 2/Monat | 4/Monat |
| KI-Suche-Optimierung | – | ✓ | + Monitoring |
| Neue Seite inkl. Text | – | 1/Monat | bis 2/Monat |
| Schriftlicher Plan (KEINE Pflicht-Calls) | – | Quartals-Strategieplan | monatl. Maßnahmenplan |
| Sichtbarkeits-Empfehlungen schriftlich + Anschreiben | – | – | ✓ |
Kein Linkaufbau (gehört in „nicht enthalten"). Profil-Einrichtung bei Start inklusive.

> **Hinweis für dich:** Es gibt KEINE „Nur-Design"-Produkte, KEINEN separaten günstigen Chatbot, KEINE Domain-Umzug-Gebühr als Extra (Umzug ist hier nicht Teil des Angebots — nicht erfinden), KEINE losen Texte-Pakete. Nimm ausschließlich die oben gelisteten Posten.

---

## 6. BRIEFING-FUNNEL „LUMI" (Ziel vorgegeben, Umsetzung offen — du entwirfst)

**Was Lumi leisten muss** (das Ziel, nicht der Weg):
- Ein als freundlicher Assistent inszenierter, **geführter Klick-Dialog** auf `/briefing`, der einen technikfernen Besucher Schritt für Schritt durch ein kurzes Website-Briefing führt und am Ende eine **Paket-Empfehlung mit transparentem Festpreis** zeigt, gefolgt von einem **unverbindlichen Kontaktformular** (Angebotsanfrage, kein Vertrag).
- **Du entwirfst Ablauf, Schrittfolge, Fragen und Interaktionsform selbst** — orientiert an dem, was die Zielgruppe braucht, um ohne Überforderung zu einer passenden Empfehlung zu kommen. Dokumentiere deinen Entwurf vorab kurz.
- **Inhaltliche Bausteine, die der Funnel sinnvoll abdecken sollte** (Reihenfolge/Gruppierung dir überlassen): Branche, Ziel der Website, gewünschter Umfang (führt zur Paketgröße), gewünschte Funktionen/Add-ons, grobe Design-/Farbrichtung, vorhandenes Material (Logo/Texte/Fotos), Zeitrahmen, optional laufende SEO-Betreuung. Mündet in Empfehlung + Kontaktanfrage.
- **Eiserne Regeln für Lumi:**
  - Preise rechnen **live aus der zentralen Preisdatensquelle** (Abschnitt 5) — nie hart kodiert.
  - **Kein Add-on und keine SEO-Stufe ist vorausgewählt.** Empfehlungen erscheinen nur als deutlich gekennzeichneter Hinweis/Badge mit Begründung, der Nutzer hakt selbst an.
  - Die Branding-Stufen (Logo / Logo + Marke / Marken-Paket) schließen sich gegenseitig aus.
  - Der gezeigte Preis darf das Festpreis-Versprechen nie untergraben (transparent, „unverbindliche Übersicht, kein Vertrag").
  - Am Ende wird ein **strukturiertes Briefing** per Kontaktformular versendet (an Sartu + Bestätigung an den Absender). Inhalt: alle Antworten + Kontaktdaten.
- Eigene, fokussierte Optik für den Funnel ist erlaubt (darf vom restlichen Seitenlayout abweichen), bleibt aber im Marken-Farbsystem.
- **Keine echte KI/kein LLM nötig** — die „Intelligenz" sind deine vordefinierten Schritte und Verzweigungen.

---

## 7. RECHT (Deutschland — Pflicht, nicht optional)

- **Impressum** nach § 5 DDG, eigene Seite, von überall (Footer) verlinkt, aus den zentralen Kontaktdaten gespeist.
- **Datenschutzerklärung** (DSGVO), eigene Seite.
- **AGB**, eigene Seite (Platzhalter-Struktur; finaler Text kommt vom Betreiber/Kanzlei — Hinweis im Report).
- **Besucher-Statistik (Pflicht, cookielos, ohne Einwilligungsbanner):**
  - **Zuerst prüfen**, ob das Hosting-Panel bereits eine Logfile-Statistik (z. B. AWStats) bietet → wenn ja, nutzen, null Code, null Banner; in README beschreiben, wo sie im Panel liegt.
  - **Falls nicht ausreichend:** **Matomo selbstgehostet** (läuft als PHP auf demselben Server), konfiguriert: **keine Tracking-Cookies, IP-Anonymisierung an, DoNotTrack respektieren, kurze Aufbewahrung** → so ist kein Consent-Banner nötig.
  - **Kein Google Analytics**, kein US-Dienst, nichts, das Node braucht.
  - Hinweis zur Statistik **trotzdem in die Datenschutzerklärung**.
- **Cookie-Banner nur, wenn echte Cookies/Tracking gesetzt werden.** Bei cookieloser Statistik → **kein Banner**. Erst wenn externe Einbindungen (Maps, Videos, Fremd-Schriften) dazukommen → Consent mit Opt-in **vor** dem Laden.
- **Kontaktformular:** DSGVO-Einwilligungs-Checkbox + Hinweis/Link auf Datenschutz.
- **Barrierefreiheit (BFSG, seit 28.06.2025):** Sartu-Seite ist eine Firmen-/Dienstleistungsseite mit Kontaktanbahnung, ohne Online-Vertragsschluss/Shop → BFSG greift **vermutlich nicht zwingend**, aber WCAG 2.1 AA wird ohnehin angestrebt (Abschnitt 8). Beantworte die Frage explizit im Report (ja/nein/offen mit Begründung), rate nicht.
- Schriften/Bilder/Icons nur lizenzfrei oder selbst lizenziert, **lokal eingebunden** (kein Fremd-CDN — DSGVO + Geschwindigkeit + Ausfallsicherheit).

---

## 8. BARRIEREFREIHEIT / ZUGÄNGLICHKEIT

- Semantisches HTML (`<header> <nav> <main> <footer>`, echte Überschriften-Hierarchie).
- Alle Bilder mit `alt`-Text, Formularfelder mit `<label>`.
- Per Tastatur bedienbar, **sichtbarer Fokus**.
- **Skip-Link** („Zum Inhalt springen") am Seitenanfang.
- Ausreichende Kontraste (Abschnitt 3).
- `prefers-reduced-motion` respektieren, falls du Animationen einsetzt.

---

## 9. SEO-GRUNDLAGEN

- Pro Seite eigener `<title>` und `<meta description>` — **im Admin pro Seite editierbar.**
- **Canonical-Tag** pro Seite (zeigt auf die kanonische eigene URL; verhindert Duplicate Content bei `www`/Slash/`index`).
- **Meta-Robots pro Seite im Admin steuerbar:** Standard indexierbar; pro Seite auf **`noindex`** schaltbar (Danke-/Testseiten). Kein pauschales „index,follow" überall.
- `<html lang="de">`.
- Sprechende URLs (Abschnitt 2).
- `sitemap.xml` (automatisch aus vorhandenen Seiten/Artikeln) und `robots.txt` anlegen.
- **Open-Graph-Tags** (OG-Titel, -Beschreibung, -Vorschaubild) — **pro Seite im Admin setzbar.**
- **Strukturierte Daten (JSON-LD):** `LocalBusiness`/`Organization` (Name, Adresse, Telefon — aus zentralen Kontaktdaten) global; **pro Seite ergänzbar** (z. B. `FAQPage` auf Leistungsseiten, `Article` auf Ratgeber). FAQ-Sichtbartext MUSS mit FAQPage-JSON-LD übereinstimmen.
- **Genau eine `<h1>` pro Seite**, sinnvolle Überschriftenstruktur.
- **Aussagekräftige Link-Texte** (kein „hier klicken"), sinnvolle interne Verlinkung (Leistungsseiten ↔ Preise ↔ Briefing).
- **Breadcrumb-Navigation** mit `BreadcrumbList`-JSON-LD.
- Bilder: `alt`, sprechende Dateinamen, komprimiert (Abschnitt 6).
- **`llms.txt` im Root** (Markdown-Liste der Seiten für KI-Agenten). **Ehrlicher Hinweis:** bringt aktuell **keinen** messbaren SEO-/KI-Ranking-Effekt — billige Zukunftswette, nicht als Ranking-Maßnahme verstehen.
- **Nach Go-Live (Aktion für den Betreiber, in README):** Seite in **Google Search Console** eintragen, `sitemap.xml` einreichen; optional Bing Webmaster Tools.

---

## 10. KONTAKTFORMULAR / MAILVERSAND

- Versand über **SMTP** (PHPMailer als lokale Datei mitliefern), **nicht** über `mail()`.
- Server-seitige Validierung **und** Spam-Schutz (Honeypot; Captcha nur wenn nötig).
- Eingaben gegen XSS/Injection bereinigen.
- **Bestätigungsmail an den Absender** + **Eingangs-Mail an Sartu**.
- Gilt auch für den Lumi-Funnel-Abschluss (strukturiertes Briefing per Mail).

---

## 11. TECHNIK / HOSTING-TAUGLICHKEIT (eigener Server / Shared-Hosting)

- **Stack:** **PHP** + HTML + CSS + sparsam Vanilla-JavaScript.
- Zielumgebung: **PHP-Shared-/eigener Server** (z. B. All-Inkl-Klasse) → **keine** Abhängigkeiten, die SSH/Composer/Node auf dem Server brauchen. Nötige Bibliotheken als **fertige Dateien** mitliefern.
- **PHP-Version-Annahme nennen** (z. B. „lauffähig ab PHP 8.1") und nichts nutzen, was das bricht.
- **Zugangsdaten** (SMTP, Admin) **nicht** im Code, sondern in einer **separaten config-Datei außerhalb des Web-Roots** bzw. einer `.env`-ähnlichen, nicht ausgelieferten Datei.
- **Keine externen CDNs** für kritische Dinge (Schriften lokal) — DSGVO + Geschwindigkeit + Ausfallsicherheit.

---

## 12. PERFORMANCE (Billigrechner & langsames Handy-Netz)

- Bilder optimiert (**WebP**, passende Größen, `loading="lazy"`, **responsive `srcset`**).
- **Feste `width`/`height` an jedem Bild** (verhindert Layout-Springen, CLS).
- **Core Web Vitals im Blick:** schneller Hauptinhalt (LCP), kein Springen (CLS), schnelle Reaktion (INP). Nach Bau mit Lighthouse-Logik gegenprüfen.
- CSS/JS minimal, möglichst gebündelt, kein Render-Blocking.
- **GZIP/Brotli** + Browser-Caching über `.htaccess`.
- Ziel: **Startseite lädt sichtbar in unter ~2 Sekunden auf Mobil.**

---

## 13. SICHERHEIT (allgemein)

- Alle Eingaben validieren und escapen (XSS).
- Falls doch eine Datenbank genutzt wird: ausschließlich **Prepared Statements**.
- Fehlermeldungen im Live-Betrieb **nicht im Klartext** (kein Stacktrace nach außen).
- `.htaccess` schützt sensible Dateien (config, JSON-Daten, Uploads-Verzeichnislisting) vor direktem Abruf.
- **Security-Header:** `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` (+ sinnvolle `Content-Security-Policy`, soweit ohne Fremd-CDN umsetzbar).

---

## 14. ÜBERGABE & WARTBARKEIT

- Klare Ordnerstruktur, an wichtigen Stellen **deutsch kommentiert**.
- **README / Kurzanleitung:** wie ändere ich Theme/Design, wie logge ich mich in den Admin ein, wo liegen die Zugangsdaten, **wie spiele ich es per FTP auf den Server** (welche Ordner, welche Dateien, welche Reihenfolge).
- **Go-Live-Checkliste in der README:** Admin-Passwort gesetzt? SMTP-Zugang eingetragen? Impressum/Datenschutz/AGB gefüllt? HTTPS aktiv? Kontaktformular getestet? Alt-URLs/301 eingetragen? Search Console + Sitemap eingereicht?
- Welche Werte muss der Betreiber vor Go-live ausfüllen (SMTP, Admin-Passwort, Impressumsdaten, Alt-URL-Liste).
- **Backup-Hinweis:** was sichern (Code + JSON-Daten + Uploads).
- **Pflege nach Go-Live:** PHP-Seite mit Login/Editor (+ ggf. Matomo) braucht gelegentlich Sicherheits-Updates (PHP-Version, Editor-Bibliothek, Matomo). Einmalbau macht Codex; laufende Pflege beim Betreiber. Kurz festhalten, was wann zu prüfen ist.

---

## 15. ABNAHME-CHECKLISTE (am Ende abarbeiten, mit Belegen melden)

- [ ] Alle Seiten erreichbar, kein toter Link, kein versehentlicher 404.
- [ ] URLs ohne `.php`/`.html`, sprechend; eigene 404-Seite aktiv.
- [ ] Auf Handy **und** Desktop sauber (mobile-first geprüft).
- [ ] Theme-Variablen funktionieren (eine Farbe ändern → ganze Seite ändert sich) UND sind im Admin editierbar.
- [ ] **Navy + Lime** als Marken-Farben erkennbar umgesetzt.
- [ ] Admin-Login sicher (gehasht, Brute-Force-Bremse, nur HTTPS, CSRF).
- [ ] Inhalts-Editor funktioniert (jeder Text/Button/jedes Bild jeder Seite editierbar) UND HTML wird per **Whitelist gefiltert** (kein XSS).
- [ ] **Alle Preise stammen aus der zentralen Datenquelle** (kein Preis hart im HTML) — stichprobenartig belegt.
- [ ] Pakete/Care/Add-ons/SEO-Stufen exakt wie Abschnitt 5 (Beträge geprüft), keine Altlast-Produkte erfunden.
- [ ] Lumi-Funnel: führt zur Empfehlung + unverbindlicher Anfrage, Preise live aus Datenquelle, **keine Vorauswahl** von Add-ons/SEO, Branding-Stufen exklusiv, versendet strukturiertes Briefing.
- [ ] Verbotene Wörter kommen nirgends vor (geprüft).
- [ ] Alte URLs per 301 vorbereitet/weitergeleitet — oder als „Liste nachzutragen" vermerkt.
- [ ] Kontaktformular sendet per SMTP (getestet bzw. Test-Anleitung beschrieben), Bestätigung + Eingangsmail.
- [ ] Impressum + Datenschutz + AGB vorhanden und verlinkt.
- [ ] BFSG-Frage beantwortet (ja/nein/offen mit Begründung).
- [ ] Keine externen CDN-Abhängigkeiten für kritische Dinge; Schriften lokal.
- [ ] Läuft ohne Composer/Node auf reinem PHP-Hosting.
- [ ] SEO pro Seite im Admin: Title, Meta, Canonical, noindex, OG-Bild, JSON-LD.
- [ ] Genau 1 H1 pro Seite; FAQ-Sichtbartext == FAQPage-JSON-LD.
- [ ] Besucher-Statistik eingerichtet (Panel-Statistik oder Matomo cookielos), kein Banner, Hinweis in Datenschutz.
- [ ] Performance: WebP/srcset, feste Bildmaße, GZIP/Caching, Ziel < 2 s mobil.
- [ ] Security-Header + `.htaccess`-Schutz sensibler Dateien aktiv.
- [ ] README + Go-Live-Checkliste + Backup-Hinweis vorhanden.

---

**Schwächster Punkt dieses Lastenhefts (ehrlich):** Der Eigen-Admin mit HTML-Editor ist die größte Angriffsfläche und der wartungsintensivste Teil — er steht und fällt mit dem serverseitigen HTML-Whitelist-Filter. Wird der lückenhaft, ist die Seite über den Editor angreifbar (XSS). Zweiter Punkt: Der Lumi-Funnel ist bewusst offen spezifiziert — kommt dein Entwurf hier nicht überzeugend, leidet die zentrale Conversion. Beide Punkte im Selbst-Audit besonders hart prüfen.
