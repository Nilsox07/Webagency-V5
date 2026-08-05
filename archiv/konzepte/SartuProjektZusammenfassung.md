# Sartu — Projekt-Zusammenfassung & Aufgabenstand

**Stand: Juni 2026** · Zusammenfassung des gesamten Beratungs- und Aufbau-Chats
Marke: **Sartu** (Webdesign-Agentur, kleines Team, Raum Dresden, deutschlandweit)
Live (Vorschau): `webagency-v3.vercel.app` · Geplante Domain: `sartu.de`

---

## 0. Worum es geht (Kurzüberblick)

Sartu ist eine Festpreis-Webdesign-Agentur für lokale Kleinunternehmer, Selbstständige, Handwerker und KMU (keine reinen Onlineshops). Die Website wird per Vibe Coding (Claude Code) gebaut, statisch (HTML/CSS/JS) und auf Vercel deployed. USP: transparente Festpreise, kontaktlos/asynchron, KI-Briefing-Assistent „Lumi", Zufriedenheitsgarantie.

In diesem Chat haben wir vier große Themenblöcke bearbeitet:
1. **Preis-, Kapazitäts- und Rechtsprüfung** der gesamten Angebotsstruktur
2. **Leistungsbeschreibung** geschärft (V2, mit konkreten Mengen/Limits + SEO/GEO-Websätzen)
3. **Lumi-Briefing & Konfigurator** analysiert und Verbesserungen definiert
4. **Kundenportal + Admin-Bereich** mit Supabase aufgebaut (Stufe 1, größtenteils live)

---

## 1. Was wir analysiert und entschieden haben

### 1.1 Preisstruktur-Prüfung (marktgerecht / lieferbar / rechtlich)
Ergebnis: Struktur überwiegend marktgerecht bis leicht zu günstig. Wichtigste Punkte:
- **Care L** war mit 179 € zu günstig für die enthaltene Arbeitszeit → **auf 249 € angehoben** (umgesetzt).
- **SEO Pro/Premium** lassen Geld liegen (Markt 800–3.000 €) → Anhebung auf ~490 €/~990 € als Option offen (kaufmännische Entscheidung, noch nicht umgesetzt).
- **Kapazitätsrisiko**: Mit kleinem Team entspannt (nicht solo). SLA „Reaktion 1 Werktag" ist mit Team haltbar.
- **Rechtliche Punkte** (alle vor Go-live über IT-Recht-Kanzlei zu prüfen):
  - Zufriedenheitsgarantie: nach EuGH C-133/22 ist auch eine Zufriedenheitsgarantie eine „gewerbliche Garantie" → Pflichtangaben nötig, sauber formulieren.
  - eRecht24/Rechtstexte: RDG-Risiko → Wording „automatisiert generiert, keine Rechtsberatung".
  - Care-Pflichtkopplung: AGB-/Verbraucherrecht (faire Verbraucherverträge, Kündigungsbutton § 312k BGB).
  - SEO: keine Rankinggarantie.

### 1.2 Wording-Korrekturen auf der Website (als Prompt geliefert)
- Solo → **Team** (Über-uns, Startseite).
- „Direkt online buchen" → **„Angebot anfordern"** (Startseite + Ablauf).
- Wartungs-FAQ: alte Werte (69 €/Std, „Plus/Premium") → **99 €/Std, Care S/M/L**.
- „Sachsen" raus → deutschlandweit.
- Logo-FAQ inhaltlich korrigiert.
- JSON-LD-Schema (FAQPage + Service) ergänzen, keine erfundenen Bewertungen.

### 1.3 Onepager-Direktkauf
Entscheidung: **vorerst NICHT**. Scheitert nicht am Bauaufwand, sondern an Inhalte-Beschaffung + Fernabsatz-Widerruf. Später als zweistufiger Direktkauf (Zahlung entkoppelt vom Liefer-Countdown), erst nach Go-live mit Zahlung + Rechtstexten.

---

## 2. Leistungsbeschreibung V2 (geschärft)

Komplett überarbeitet mit **konkreten Mengen/Limits** statt schwammiger Begriffe, marktgestützt recherchiert. Enthält:
- **10 Abgrenzungsklauseln** (Korrekturrunde, Kontingent-Verfall, „eine Änderung", „eine Seite", Mitwirkung, Inhalts-Verantwortung, SEO-Ebenen, Reaktionszeit, Drittkosten, Rechteübergang) — gehören in die AGB.
- Pro Position ein **Answer-First-Websatz** (SEO/GEO-Keywords + Preis im ersten Satz) zum 1:1-Übertrag auf die Leistungsseiten.
- Pro Position: **Enthalten / Nicht enthalten / Voraussetzungen** mit harten Zahlen.

**Wichtige neue Festlegungen (Auszug):**
- Basis: bis 6 Sektionen, bis 10 Bilder. Pro: bis 30 Bilder. Platin: einmaliges Local-SEO-Setup + Core-Web-Vitals-Ziele (LCP < 2,5 s).
- Care S: tägliche externe Backups, 30 Tage, Monitoring alle 5 Min. Care M: 30 Min/Monat. Care L: 90 Min/Monat.
- SEO: Keyword-Tracking 10/30/50; Premium max. 2 Backlinks/Monat, 1 Städte-Landingpage/Monat.
- Texte: 300–500 Wörter/Seite, 2 Korrekturschleifen.
- **Stundensatz-Takt von 15 auf 5 Minuten geändert** (rechtlich zwingend: BGH IX ZR 140/19; OLG Düsseldorf 24 U 65/22 — 15-Min-Aufrundung auch B2B unwirksam).
- Care-Wording **stack-neutral** (kein WordPress-Vokabular, passend zu statischen Seiten).

**Status:** V2-Volltext fertig + als Übertrag-Prompt für Claude Code aufbereitet (Leistungsseiten + pricing.js).
**Offen:** Übertrag-Prompt an Claude Code geben und ausführen lassen.

---

## 3. Lumi-Briefing & Konfigurator

### 3.1 Architektur (analysiert, sauber gebaut)
- EIN Tool, ZWEI Pfade: A (Konfigurator direkt) / B (8-Schritte-Beratung → selber Konfigurator).
- Live-Preisleiste (getrennt einmalig/monatlich), Pflicht-Wartung mit Floor-Logik, Enterprise-Abzweig, Farb-Vorschau-Mockup (entfernbares Modul).
- `pricing.js` = Single Source of Truth. Abschluss = unverbindliche Angebotsanfrage (kein Vertrag).

### 3.2 Identifizierte Schwachpunkte + Verbesserungs-Prompt (9 Punkte, geliefert)
1. Mobiler Horizontal-Overflow (war auf allen Seiten außer Startseite — Ursache Breadcrumb/Eyebrow auf Unterseiten; **gefixt**).
2. Stilwahl auf Single-Select.
3. Farb-Vorschau-Mockup UNTER die Farbkacheln (Aktion/Reaktion zusammen).
4. Stil-Reaktion im Mockup deutlich verstärken.
5. Farben auf ~12 reduzieren (Dubletten raus).
6. Auto-Advance bei reinen Single-Choice-Fragen.
7. Tipp-Indikator („Lumi schreibt…") dosiert, nur an erzählenden Stellen.
8. Sanftes Fade-in beim Schrittwechsel.
9. Ausgewählte Farbe stärker hervorheben (Häkchen).

**Entscheidung gegen HSL-Farbwähler:** würde Baukasten-Gefühl erzeugen (gegen USP) und das „ich wollte genau DAS Grün"-Problem verstärken statt lösen. HEX-Feld deckt Sonderfall ab; exakte Töne kommen in Stufe-2-Onboarding.

**Status:** Verbesserungs-Prompt geliefert. **Offen:** an Claude Code geben/umsetzen (außer Overflow, der ist erledigt).

---

## 4. Kundenportal + Admin-Bereich (Supabase) — Hauptarbeit dieser Session

### 4.1 Architektur-Entscheidungen
- **Backend: Supabase** (Auth + Postgres + Storage), Region **Frankfurt/eu-central-1** (DSGVO).
- **Frontend bleibt statisch** (portal.html, admin.html, login.html, auth-callback.html) auf Vercel.
- **Nur anon key im Frontend**, service_role key niemals.
- **Sicherheit aus Row Level Security (RLS)**, nicht aus Frontend-Logik.
- **Magic Link / OTP** statt Passwort (einfacher für die Zielgruppe, kein Reset-Support).

### 4.2 Was bereits funktioniert (live getestet)
- ✅ Supabase-Projekt in **Frankfurt** angelegt (erst versehentlich Irland — neu gemacht).
- ✅ **schema.sql ausgeführt**: 7 Tabellen (profiles, projects, briefings, uploads, feedback_rounds, care_entries, documents), alle mit RLS = true, Policies aktiv. (Der „policy already exists"-Fehler kam nur vom doppelten Ausführen — harmlos.)
- ✅ **Admin-Profil angelegt** (profiles-Insert mit role='admin') und per Trigger mit dem Auth-User verknüpft (user_id gesetzt).
- ✅ **Login-Flow repariert**: Ursache war die Supabase Site URL auf der Startseite ohne Session-Handling. Lösung: dedizierte `auth-callback.html` nimmt das Token auf (detectSessionInUrl/persistSession/autoRefreshToken), Rollen-Weiche (Admin → admin.html, Kunde → portal.html). Redirect-URL `https://webagency-v3.vercel.app/**` in Supabase eingetragen.
- ✅ **Admin-Login funktioniert** — Admin-Bereich (Anfragen/Projekte/Kunden) erreichbar.
- ✅ **Lumi-Anbindung an Supabase**: Test-Anfrage über Lumi landet in der briefings-Tabelle und erscheint im Admin-Bereich.

### 4.3 Datenstruktur (Tabellen)
- `profiles` (Kunden/Admins, role customer|admin), `projects` (Phase-Timeline), `briefings` (Lumi-Anfragen, komplettes payload-JSON), `uploads`/`feedback_rounds` (Stufe 2, nur Schema), `care_entries`/`documents` (Stufe 3, nur Schema).
- Besonderheit: `notiz_intern` per Spalten-Grant vor Kunden geschützt; Kunden lesen über View `projects_customer`, Admin über `admin_projects()`.

---

## 5. OFFENE AUFGABEN (priorisiert)

### 5.1 SOFORT / als Nächstes
- [ ] **5 RLS-Sicherheitstests durchführen** (NOCH NICHT GEMACHT, wichtig!). Besonders: als Test-Kunde einloggen und prüfen, dass er KEINE fremden Projekte und KEINE briefings sieht; dass notiz_intern nie abrufbar ist. Vor echten Kundendaten zwingend mit eigenen Augen verifizieren.
- [ ] **Admin-Detailansicht für Anfragen** bauen (Prompt geliefert): beim Klick auf eine Anfrage alle Lumi-Infos lesbar (Branche, Ziele, Paket, Add-ons, Summen, Farben…), value→Label-Übersetzung.
- [ ] **„In Projekt umwandeln"-Button testen**: legt er Kunde+Projekt an? Login-Einladung für den Kunden klären (Edge Function ODER manueller Dashboard-Weg „Invite user" — für den Anfang manuell empfohlen).

### 5.2 Inhalt/Website (Prompts liegen bereit)
- [ ] **Leistungsbeschreibung V2 übertragen** (Leistungsseiten + pricing.js): Answer-First-Websätze, Enthalten/Nicht-enthalten/Voraussetzungen, 15→5-Min-Takt überall, Care-Wording stack-neutral.
- [ ] **Lumi-Verbesserungen** (8 verbleibende Punkte) an Claude Code geben.
- [ ] **Wording-Korrekturen** (Team/Schritt3/Wartungs-FAQ/Sachsen/Logo-FAQ/JSON-LD) umsetzen, falls noch offen.

### 5.3 Go-live-Paket (alles domainabhängig — in EINEM Rutsch)
- [ ] **Domain `sartu.de` registrieren** + DNS-Zugriff.
- [ ] **Platzhalter füllen** (~150: [DOMAIN] 143×, [OG-IMAGE] 31×, NAP/Kontakt, Social-URLs, Gründer-/Teamfoto).
- [ ] **Impressum** (rechtlich Pflicht, sobald öffentlich), Datenschutz, AGB — über IT-Recht-Kanzlei.
- [ ] **vercel.json `cleanUrls`-Bug**: Canonicals + sitemap zeigen auf .html, Vercel liefert ohne → SEO-Schaden. Beim Go-live geraderücken.
- [ ] **Eigener Mailversand (SMTP)**: Login-/Einladungsmails von `@sartu.de` statt supabase.io. Braucht Domain + Mail-Dienst (z. B. Resend) + SPF/DKIM-DNS-Einträge. Supabase → Authentication → SMTP Settings.
- [ ] **Supabase URL-Config** auf echte Domain umstellen (Site URL + Redirect URLs).
- [ ] **AV-Vertrag mit Supabase** + Datenschutzerklärung um Portal/Supabase (Frankfurt) ergänzen.

### 5.4 Später / Stufe 2 & 3 des Portals
- [ ] Stufe 2: Inhalte-Upload (Onboarding) + Feedback-Formular mit Runden-Zähler.
- [ ] Stufe 3: Care-Minuten-Tracking, Dokumente, Kündigungsbutton.
- [ ] Rechnungen/Zahlung: NICHT selbst bauen. lexoffice ODER sevDesk (Entscheidung offen) per API anbinden + Mollie für Zahlungslinks/Anzahlungs-Meilensteine. Mahnwesen/GoBD/E-Rechnung übernimmt das Buchhaltungstool.
- [ ] Code-Aufräumen (beaufsichtigt): totes CSS selektorweise, onboarding-stage2.js aushängen, Dev-Dateien aus Web-Root, console.logs raus. Header/Footer-Duplikate bewusst liegen lassen.
- [ ] SEO Pro/Premium + Pro-Paket Preisanhebung (kaufmännische Entscheidung).
- [ ] Referenzen/Arbeitsproben ergänzen (größter Vertrauenshebel, sobald 2–3 Projekte da).

---

## 6. Wichtige Prinzipien & Entscheidungen (zum Festhalten)

- **Datenbank/Auth bleibt dauerhaft bei Supabase** (Frankfurt). Frontend kann später auf eigenen Server ziehen, ohne dass die Supabase-Arbeit verloren geht — nur URL-Config anpassen. NICHT selbst auf MariaDB nachbauen (Sicherheitsrisiko, kein Mehrwert).
- **Anfrage ≠ Kunde**: Lumi-Anfragen landen automatisch in der Inbox; ein Kunde/Login entsteht erst durch bewussten Admin-Klick („In Projekt umwandeln"). Kein Auto-Anlegen von Konten bei jeder Anfrage.
- **Rechnungen nicht von KI „schreiben" lassen** (Zahlen dürfen nicht halluziniert werden) — deterministisch über Buchhaltungstool.
- **Vor Go-live anwaltlich prüfen**: Garantie, AGB (Care-Kopplung, Kündigung), RDG/eRecht24, SEO-Wording.
- **Vibe-Coding-Regel**: bei sicherheitskritischen Teilen (RLS, Auth, service_role key) selbst testen, nicht blind „sieht fertig aus" vertrauen. Login-/Session-Bugs sind die fummeligste Ecke — ein bis zwei Iterationen sind normal.
- **Mailversand-Limit**: Supabase-Standardversand ist stark limitiert + Absender supabase.io → nur zum Testen ok, vor echten Kunden eigener SMTP nötig.

---

## 7. Direkt griffbereite Prompts (in diesem Chat geliefert)

1. **Leistungsbeschreibung-V2-Übertrag** (Leistungsseiten + pricing.js) — als Textdatei `sartu-leistungen-uebertrag-prompt.txt`.
2. **Lumi-Verbesserungen** (9 Punkte) — im Chat.
3. **Wording-Korrekturen** (Team/Buchen/FAQ/Sachsen/Logo/Schema) — im Chat.
4. **Admin-Anfragen-Detailansicht** (alle Lumi-Infos lesbar) — im Chat.
5. **Login-/Session-Fix** — bereits umgesetzt.

---

*Diese Zusammenfassung spiegelt den Stand am Ende des Chats. Nächster sinnvoller Schritt: die 5 RLS-Tests, dann die Admin-Detailansicht — beides ohne Domain möglich. Das domainabhängige Go-live-Paket (Platzhalter, Impressum, Mailversand, cleanUrls) in einem späteren Rutsch zusammen erledigen.*
