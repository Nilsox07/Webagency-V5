# Lastenheft — Webseite (Auftrag an Claude Code)

Dieses Dokument ist die einzige Wahrheitsquelle. Wenn etwas hier nicht steht, wird es **nicht** dazuerfunden, sondern als offener Punkt markiert.

---

## 0. ARBEITSWEISE (zuerst lesen, bindend)

- **Lauf komplett durch. Keine Rückfragen, keine Zwischenstopps, keine Freigabe abwarten.** Triff alle Entscheidungen selbst und sinnvoll. Bau die ganze Seite fertig.
- **Einzige Ausnahme vor dem Bauen:** Dokumentiere kurz das Ergebnis deiner Themen-Recherche (siehe Abschnitt 1) — abgeleitete Zielgruppe, Haupt-Handlung, gewählte Farbwelt mit Begründung. Danach ohne Unterbrechung weiterbauen.
- Prüfe deine eigene Arbeit aktiv: jede neue Seite durchdenken (lädt sie? Links tot? Formular sendet?). Gefundene Fehler **sofort** selbst beheben, nicht ankündigen, nicht fragen.
- **Beweis-Report am Ende (Pflicht):** Liste auf, was gebaut wurde — pro Punkt ein Beleg (Datei + Zeilen, `grep`-Treffer oder Commit-Hash). „Fertig" ohne Beleg zählt nicht. Das ist keine Rückfrage, das ist Nachweis.
- **Selbst-Audit vor dem Schluss (Pflicht):** Geh am Ende die Abnahme-Checkliste (Abschnitt 13) Punkt für Punkt durch UND such darüber hinaus aktiv nach Lücken (fehlende Seite, toter Link, ungeschütztes Formular, vergessener Rechts-Baustein). Behebe alles Gefundene sofort selbst. Wiederhole diese Audit-Runde, bis **jeder** Punkt der Checkliste mit Beleg grün ist und keine neue Lücke mehr auftaucht. Erst dann gilt das Projekt als fertig. „Hab alles" ohne abgehakte Checkliste mit Belegen zählt nicht.
- **Was du nicht live testen kannst (SMTP-Mailversand, FTP-Upload), klar als solches kennzeichnen:** Code dafür sauber bauen, im Beweis-Report vermerken „nicht live getestet — von mir nach Upload zu prüfen" und in der README eine kurze Test-Anleitung dafür geben. Nicht „funktioniert" behaupten, was nicht getestet werden konnte.
- Am Schluss zusätzlich: was ist fertig, was ist offen, was ist das schwächste Teil des Ergebnisses.

---

## 1. THEMA & EIGENRECHERCHE  ← **Ich gebe nur das Thema, du baust alles darauf auf**

**Mein Thema (das Einzige, was ich vorgebe):**

> [HIER NUR DAS THEMA: z.B. „Eisdiele", „Steuerberater", „Hundeschule", „Sanitär-Notdienst"]

**Dein Auftrag:** Recherchiere dieses Thema selbst im Web (Branche, typische Anbieter, wie vergleichbare Seiten aussehen, was Besucher erwarten). Leite daraus eigenständig **alles** ab:
- **Zielgruppe** (wer, B2B/B2C, lokal/überregional, Alter, Technik-Affinität),
- **Haupt-Gerät** der Besucher (Smartphone/Desktop) → Layout danach ausrichten,
- **eine Haupt-Handlung**, die der Besucher tun soll,
- **Tonalität und Bildsprache**,
- **Farbwelt und Design-Richtung** passend zu Branche und Zielgruppe (siehe Abschnitt 3),
- die **Seitenstruktur und Inhalte** (welche Seiten, welche Abschnitte, welche Texte sinnvoll sind).

**Pflicht vor dem Bauen:** Schreib an den Anfang in ein paar Sätzen, was du recherchiert hast und welche Schlüsse du ziehst — Zielgruppe, Haupt-Handlung, gewählte Farbwelt + **kurze Begründung**, warum dieses Design zur Zielgruppe passt. Danach ohne Unterbrechung durchbauen. Im Zweifel: weniger, größer, klarer.

**Diese Kern-Fragen beantwortest du in deiner Recherche (alle, vor dem Bauen):**
1. Wer sucht dieses Angebot — Alter, B2B oder B2C, lokal oder überregional?
2. Welches Problem oder Bedürfnis hat der Besucher, wenn er auf die Seite kommt?
3. Welche **eine** Handlung soll er ausführen (anrufen, Formular, Termin, Route, kaufen)?
4. Sucht die Zielgruppe eher am Handy (unterwegs) oder am Desktop?
5. Wie technikaffin ist sie — wie einfach/groß/klar muss es sein?
6. Welche Infos erwartet ein Besucher bei dieser Branche zwingend (z.B. Öffnungszeiten, Preise, Anfahrt, Leistungen, Referenzen)?
7. Wie sieht die Konkurrenz aus — was machen vergleichbare Seiten gut, was schlecht?
8. Welche Tonalität passt (seriös, verspielt, handwerklich, premium)?
9. Welche Farbwelt und Bildsprache passt zur Branche und weckt Vertrauen?
10. Welche Seiten und Abschnitte braucht die Seite konkret?
11. Welche **branchenspezifischen Rechts-Besonderheiten** gibt es (z.B. Heilversprechen-Verbot bei Gesundheit, Preisangabenpflicht bei Verkauf, Erlaubnispflichten)?
12. Welche Vertrauenssignale zählen hier (Bewertungen, Zertifikate, Mitgliedschaften, Jahre Erfahrung)?

Beantworte diese zwölf Punkte kurz in deiner Vorab-Dokumentation. Sind Fakten branchenüblich unklar, triff eine begründete Annahme und kennzeichne sie — frag nicht nach.

---

## 2. INHALT & STRUKTUR

- **Seiten (Sitemap):** Startseite, Leistungen/Angebot, Über uns, Kontakt, Impressum, Datenschutz. [anpassen]
- **URLs sprechend und ohne Endung:** `/impressum`, `/kontakt`, `/leistungen` — **nicht** `/index.html`, **nicht** `/kontakt.php`. Umsetzung über `.htaccess` (mod_rewrite). Falls mod_rewrite auf dem Hosting aus ist → melden, nicht still auf `.php`-URLs ausweichen.
- Jede Seite hat genau **ein** klares Ziel und einen sichtbaren Handlungs-Button (Call-to-Action).
- **404-Seite** (eigene, gestaltete Fehlerseite mit Link zurück zur Startseite, über `.htaccess` eingebunden).
- **Favicon** in den nötigen Größen anlegen und einbinden.
- **Falls diese Seite eine bestehende ablöst:** alte URLs per **301-Weiterleitung** (`.htaccess`) auf die neuen lenken — sonst tote Links und Ranking-Verlust. (Wenn es keine Vorgänger-Seite gibt: entfällt, kurz vermerken.)
- Texte sind Platzhalter, die ich später ersetze — aber realistisch lang, nicht „Lorem Ipsum"-Blöcke.

---

## 3. DESIGN-SYSTEM

- **Du wählst** Farbwelt, Schriften und Stil passend zur recherchierten Zielgruppe und Branche (Abschnitt 1) und begründest die Wahl kurz. Ich gebe keine Farben vor.
- **Technisch** gehören **alle** Farben, Schriftarten, Schriftgrößen, Abstände, Button-Stile als **CSS-Variablen in EINE zentrale Datei** (`assets/theme.css` oder `:root{}`-Block). Ändere ich einen Wert dort, ändert sich die ganze Seite.
- Konkret als Variablen anlegen: Primärfarbe, Sekundärfarbe, Textfarbe, Hintergrund, Button-Farbe + Hover, Schriftfamilie Überschrift, Schriftfamilie Fließtext, Basis-Schriftgröße, Eckenradius, max. Inhaltsbreite.
- **Mobile-first** entwickeln, dann auf Desktop hoch. Keine festen Pixelbreiten, die auf dem Handy brechen.
- Kontraste WCAG-tauglich (Text/Hintergrund mind. 4.5:1) — auch aus Zielgruppen-Gründen (Lesbarkeit).
- Keine schweren CSS/JS-Frameworks (kein Bootstrap-Vollpaket, kein jQuery nötig). Eigenes, schlankes CSS.

---

## 4. ADMIN-BEREICH

Im Admin kann ich **jeden Inhalt jeder Seite** bearbeiten — Überschriften, Fließtexte, Button-Texte, Bilder, Links:
- **WYSIWYG-/HTML-Editor** (ähnlich WordPress) pro Seite für die Inhalte. Bewährten, schlanken Editor mitliefern (z.B. eine fertige Editor-Bibliothek als lokale Datei, kein Composer/Node nötig).
- Die Design-Werte (Farben, Schrift, Button-Stile) über einfache Felder (siehe Abschnitt 3).
- Bilder hochladen/austauschen.
- **SEO pro Seite** im Admin setzbar: Title, Meta Description, Canonical, noindex-Schalter, OG-Titel/-Beschreibung/-Vorschaubild, JSON-LD (siehe Abschnitt 9).
- Kontakt-/Impressum-Daten.

**Grenze (damit es einfach und sicher bleibt):** voller Inhalts-Editor **ja**, aber **kein** freier Drag-&-Drop-Layout-Baukasten. Das Seitengerüst (Aufbau, Navigation) steht; ich fülle und ändere Inhalte, verschiebe aber keine Bausteine frei.

Sicherheit des Admin (Pflicht, da auf Shared-Hosting öffentlich erreichbar):
- Login mit **gehashtem** Passwort (`password_hash`/`password_verify`), niemals Klartext.
- HTTPS erzwingen, Admin nur über HTTPS.
- Brute-Force-Bremse (Login-Versuche begrenzen).
- CSRF-Token bei allen Formularen im Admin.
- **HTML-Editor absichern (kritisch):** gespeichertes HTML serverseitig durch einen **Whitelist-Filter** (erlaubte Tags/Attribute) laufen lassen, bevor es ausgegeben wird — sonst ist der Editor ein offenes XSS-Tor. Kein ungefiltertes HTML speichern/ausgeben.
- Datei-Uploads: nur erlaubte Bildtypen, Größe begrenzt, außerhalb des ausführbaren Pfads speichern.
- Speicherung der Inhalte/Einstellungen als **Flat-File (JSON)** — keine eigene Datenbank für die Webseite nötig (Matomo bringt seine eigene mit, das ist davon getrennt; siehe Abschnitt 7).

---

## 5. TECHNIK / HOSTING-TAUGLICHKEIT (läuft auf jedem Billig-Server)

- **Stack:** PHP + HTML + CSS + (sparsam) Vanilla-JavaScript. Bevorzugt PHP.
- Zielumgebung: **Shared-Hosting wie All-Inkl** → **keine** Abhängigkeiten, die SSH/Composer/Node auf dem Server brauchen. Wenn eine Bibliothek nötig ist, wird sie als fertige Datei mitgeliefert.
- PHP-Version-Annahme nennen (z.B. „lauffähig ab PHP 8.1") und keine Funktionen nutzen, die das brechen.
- Zugangsdaten (Mail, Admin) **nicht** im Code, sondern in einer separaten `config`-Datei außerhalb des Web-Roots oder per `.env`-ähnlicher Datei, die nicht ausgeliefert wird.
- Keine externen CDNs für kritische Dinge (Schriften lokal einbinden) — wegen DSGVO **und** Geschwindigkeit **und** Ausfallsicherheit.

---

## 6. PERFORMANCE (Billigrechner & langsames Handy-Netz)

- Bilder optimiert (WebP, passende Größen, `loading="lazy"`, **responsive `srcset`** für Handy/Desktop).
- **Feste `width`/`height` an jedem Bild** — verhindert Layout-Springen beim Laden (Core-Web-Vitals-Faktor CLS).
- **Core Web Vitals im Blick:** schnelle Anzeige des Hauptinhalts (LCP), kein Springen (CLS), schnelle Reaktion (INP). Nach Bau einmal mit Lighthouse-Logik gegenprüfen.
- CSS/JS minimal, am besten gebündelt, kein Render-Blocking.
- **GZIP/Brotli-Kompression** und Browser-Caching über `.htaccess` aktivieren.
- Ziel: Startseite lädt sichtbar in unter ~2 Sekunden auf Mobil.

---

## 7. RECHT (Deutschland — Pflicht, nicht optional)

- **Impressum** nach § 5 DDG, eigene Seite, von überall verlinkt.
- **Datenschutzerklärung** (DSGVO) als eigene Seite.
- **Besucher-Statistik (gewünscht — Pflicht):** Ich will sehen, wie viele Besucher die Seite hat. Umsetzung **cookielos und ohne Einwilligungsbanner**:
  - **Zuerst prüfen**, ob das All-Inkl-Hosting-Panel bereits eine Logfile-Besucherstatistik (z.B. AWStats) bietet → wenn ja, das nutzen, null Code, null Banner. In der README beschreiben, wo ich sie im Panel finde.
  - **Falls nicht ausreichend:** Matomo selbstgehostet einrichten (läuft als PHP auf demselben Hosting), konfiguriert auf: keine Tracking-Cookies, IP-Anonymisierung an, DoNotTrack respektieren, kurze Datenaufbewahrung. So ist kein Consent-Banner nötig.
  - **Kein Google Analytics**, kein US-Dienst, keine Tools die Node brauchen (Umami/Plausible self-hosted scheiden auf diesem Hosting aus).
  - Hinweis zur Statistik-Nutzung **trotzdem in die Datenschutzerklärung** aufnehmen.
- **Cookie-Banner nur, wenn doch echte Cookies/Tracking gesetzt werden.** Bei der oben gewählten cookielosen Statistik → **kein Banner**. Erst wenn später externe Einbindungen (Maps, Videos, Schriften von fremden Servern) dazukommen → Consent mit Opt-in **vor** dem Laden.
- Kontaktformular: **DSGVO-Einwilligungs-Checkbox** + Hinweis auf Datenschutz.
- **Barrierefreiheit (BFSG, seit 28.06.2025):** prüfen, ob die Seite darunter fällt. Bei reiner Firmen-Visitenkarte ohne Online-Vertragsschluss meist nicht zwingend; sobald Shop/Buchung/Online-Vertrag dabei ist, **greift es** → dann WCAG 2.1 AA umsetzen. Im Zweifel als offenen Punkt melden, nicht raten.
- Schriften/Bilder/Icons nur lizenzfrei oder selbst lizenziert.

---

## 8. BARRIEREFREIHEIT / ZUGÄNGLICHKEIT (auch ohne BFSG sinnvoll)

- Semantisches HTML (`<header> <nav> <main> <footer>`, echte Überschriften-Hierarchie).
- Alle Bilder mit `alt`-Text. Formularfelder mit `<label>`.
- Per Tastatur bedienbar, sichtbarer Fokus.
- **Skip-Link** („Zum Inhalt springen") am Seitenanfang für Tastatur-/Screenreader-Nutzer.
- Ausreichende Kontraste (siehe Design).

---

## 9. SEO-GRUNDLAGEN

- Pro Seite eigener `<title>` und `<meta description>` — **im Backend pro Seite einzeln editierbar.**
- **Canonical-Tag** pro Seite (zeigt automatisch auf die kanonische eigene URL; verhindert Duplicate Content bei `www`/Slash/`index`-Varianten).
- **Meta-Robots pro Seite im Backend steuerbar:** Standard ist indexierbar (keine Extra-Angabe nötig). Pro Seite muss ich auf **`noindex`** stellen können (z.B. Danke-Seite, Test-Seiten). Kein pauschales „index,follow" auf jede Seite.
- `<html lang="de">` setzen.
- Sprechende URLs (siehe Punkt 2).
- `sitemap.xml` und `robots.txt` anlegen.
- Open-Graph-Tags (für Vorschau beim Teilen) — **OG-Titel, OG-Beschreibung und OG-Vorschaubild pro Seite im Backend setzbar.**
- Strukturierte Daten (JSON-LD: LocalBusiness/Organization mit Name, Adresse, Telefon) — **pro Seite ergänzbar (Rich Snippets).**
- Eine `<h1>` pro Seite, sinnvolle Überschriftenstruktur.
- **Aussagekräftige Link-Texte** (Ankertext beschreibt das Ziel, kein „hier klicken") und sinnvolle interne Verlinkung zwischen den Seiten.
- **Breadcrumb-Navigation** mit `BreadcrumbList`-JSON-LD (zeigt bei Google die Pfad-Anzeige im Suchergebnis — Rich Snippet).
- Bilder: `alt`-Text (siehe Abschnitt 8), sprechende Dateinamen, komprimiert (siehe Abschnitt 6).
- **`llms.txt` im Root anlegen** (Markdown-Liste der Seiten für KI-Agenten). Ehrlicher Hinweis: bringt aktuell **keinen** messbaren SEO- oder KI-Ranking-Effekt, kein großer Anbieter wertet sie aus — reine billige Zukunftswette, nicht als Ranking-Maßnahme verstehen.
- **Nach Go-Live (Aktion für mich, in README vermerken):** Seite in **Google Search Console** eintragen und `sitemap.xml` dort einreichen. Ohne das wirkt SEO langsamer und ich sehe keine Indexierungs-Fehler. Optional Bing Webmaster Tools.

---

## 10. KONTAKTFORMULAR / MAILVERSAND

- Versand über **SMTP** (z.B. PHPMailer mitgeliefert), nicht über die unzuverlässige PHP-`mail()`.
- Server-seitige Validierung **und** Spam-Schutz (Honeypot-Feld; Captcha nur wenn nötig).
- Eingaben gegen XSS/Injection bereinigen.
- Bestätigung für den Absender + Eingangs-Mail an mich.

---

## 11. SICHERHEIT (allgemein)

- Alle Eingaben validieren und escapen (XSS).
- Falls doch Datenbank: ausschließlich Prepared Statements (SQL-Injection).
- Fehlermeldungen im Live-Betrieb nicht im Klartext anzeigen (kein Stacktrace nach außen).
- `.htaccess` schützt sensible Dateien (config, JSON-Daten) vor direktem Abruf.
- Security-Header setzen (X-Content-Type-Options, X-Frame-Options, Referrer-Policy).

---

## 12. ÜBERGABE & WARTBARKEIT

- Klare Ordnerstruktur, im Code an wichtigen Stellen kommentiert (deutsch).
- Eine `README` / Kurzanleitung: wie ändere ich Theme, wie logge ich mich in den Admin ein, wo liegen die Zugangsdaten, **wie spiele ich es Schritt für Schritt per FTP auf All-Inkl** (welche Ordner, welche Dateien anpassen, welche Reihenfolge).
- **Go-Live-Checkliste in der README:** Admin-Passwort gesetzt? Mail-Zugang eingetragen? Impressum/Datenschutz gefüllt? HTTPS aktiv? Testlauf Kontaktformular gemacht?
- Hinweis, welche Werte ich vor dem Live-Gang ausfüllen muss (Mail-Zugang, Admin-Passwort, Impressum).
- **Backup-Hinweis:** was muss ich sichern (Code + JSON-Daten + Uploads), damit nichts verloren geht.
- **Pflege nach Go-Live (Hinweis für mich):** Eine PHP-Seite mit Login/Editor (und ggf. Matomo) braucht gelegentlich Sicherheits-Updates (PHP-Version, Editor-Bibliothek, Matomo). Einmalig bauen macht Claude Code; die laufende Pflege liegt danach bei mir. In README kurz festhalten, was wann zu prüfen ist.

---

## 13. ABNAHME-CHECKLISTE (am Ende abarbeiten und Ergebnis melden)

- [ ] Alle Seiten erreichbar, kein toter Link, kein 404.
- [ ] URLs ohne `.php`/`.html`, sprechend.
- [ ] Auf Handy **und** Desktop sauber (mobile-first geprüft).
- [ ] Theme-Variablen funktionieren (eine Farbe ändern → ändert die ganze Seite).
- [ ] Admin-Login sicher, Brute-Force-Bremse aktiv, nur über HTTPS.
- [ ] Inhalts-Editor funktioniert (jeden Text/Button/Bild jeder Seite editierbar) UND HTML wird per Whitelist gefiltert (kein XSS).
- [ ] Alte URLs (falls vorhanden) per 301 weitergeleitet — oder vermerkt, dass es keine gibt.
- [ ] Kontaktformular sendet wirklich (SMTP getestet bzw. Test-Code beschrieben).
- [ ] Impressum + Datenschutz vorhanden und verlinkt.
- [ ] BFSG-Frage beantwortet (fällt die Seite darunter? ja/nein/offen).
- [ ] Keine externen CDN-Abhängigkeiten für kritische Dinge.
- [ ] Läuft ohne Composer/Node auf reinem PHP-Shared-Hosting.
- [ ] SEO pro Seite: Title, Meta Description, Canonical, noindex-Schalter, OG-Bild, JSON-LD im Backend setzbar.
- [ ] Besucher-Statistik eingerichtet (Panel-Statistik oder Matomo cookielos), kein Banner nötig, Hinweis in Datenschutz.
- [ ] README + Backup-Hinweis vorhanden.

---

**Schwächster Punkt dieses Lastenhefts:** Code recherchiert das Thema selbst und baut alles auf einer selbstgewählten Zielgruppen-Annahme auf. Ist das Thema oben zu vage (nur „Essen" statt „Eisdiele, lokal"), rät Code die Richtung — und du siehst es erst am fertigen Ergebnis. Je präziser das eine Wort in Abschnitt 1, desto besser trifft alles andere.
