# SARTU – Website-Lastenheft (baufinal)

**Stand:** 24.07.2026 · **Zweck:** Umsetzungsreifes Briefing für die **eigene SARTU-Website**. Wer dieses Dokument hat, kann bauen — ohne Rückfragen zu Texten, Struktur, Feldern oder Verhalten.

**Gilt zusammen mit:**
- `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md` – Architektur, Seitenkonzepte, Begründungen
- `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` – Geschäftsmodell, Preise, Portal, Recht
- `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` – wie die visuelle Ebene recherchiert und ausgewählt wird

> **Es gibt keine vorgegebene Designrichtung.** Frühere Entwürfe unter `design/_verworfen/` sind
> **verworfen** und weder Vorgabe noch Anregung. Farben, Schriften und Formen entstehen ausschließlich
> über das Design-Briefing.

---

## 0. Standortneutral baufertig — mit zwei Gates

| # | Entscheidung | Status |
|---|---|---|
| 1 | **Designrichtung** | **offen — wird recherchiert**, nicht vorgegeben. Vorgehen: `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`. Struktur, Copy und Anforderungen in diesem Dokument sind davon unabhängig und vollständig. |
| 2 | **Startregion / Standort** | ⏸ **offen** — siehe `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1. Blockiert **nicht** den Bau, blockiert die lokale Ebene |

**Was das heißt:** Alle Seiten, Texte, Felder und Abnahmekriterien in diesem Dokument sind vollständig und **standortneutral umsetzbar**. Gesperrt sind nur zwei Dinge:

| Gate | Blockiert | Blockiert **nicht** |
|---|---|---|
| **Designrichtung** | den Vollausbau über die 2–3 Startseitenvarianten hinaus | Struktur, Copy, Bedarfsscheck, SEO-Grundlage |
| **Standort** | die lokale Ebene: Ortsseiten, `LocalBusiness`, Unternehmensprofil, Ortsnamen in Titeln | den gesamten übrigen Bau |

**Nicht baufrei ist der lokale Launch** — und mit ihm der schnellste Kundenkanal überhaupt
(`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §0.4). Die Form des Unternehmensprofils hängt an der Art der
Adresse: echtes Büro → sichtbare Adresse, reine Postadresse → Service-Area ohne sichtbare Adresse
(Masterkonzept §23a.1).

**Zu 2 (offen):** Die Startregion ist **nicht entschieden**. Werte und Sperren stehen in
`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1.

**Was das bedeutet — Sperren, solange `[GESCHAEFTSADRESSE_STATUS]` auf `offen` steht:**
- **Kein** `LocalBusiness` in strukturierten Daten. Stattdessen `Organization` **ohne** Adressfeld
- **Kein** Google-Unternehmensprofil, auch nicht vorbereitend
- **Keine** Ortsseiten in der produktiven Veröffentlichung, auch nicht unverlinkt
- **Keine** Ortsnamen in Title, H1, Meta-Description, URL oder Fließtext
- **Keine** NAP-Aussage — es gibt noch keine Anschrift

**Was trotzdem vollständig gebaut wird:** alles andere. Startseite, Preise, Bedarfsscheck, Ablauf,
fünf Leistungsseiten, Ratgeber, Lexikon, Kundenbereich, SEO-Grundlage. Die lokale Ebene ist eine
spätere Ergänzung, kein Fundament — deshalb ist dieses Lastenheft **standortneutral baufertig**.

**Sprachregelung bis zur Entscheidung:** überregional formulieren. Zulässig sind Aussagen über die
Arbeitsweise („persönlich erreichbar", „auf Wunsch beim Kunden vor Ort"), **nicht** über Orte.

**Terminregel für alle Texte:** Standard ist **kein Termin**. Auf Wunsch Video oder beim Kunden vor
Ort. Auf der Website wird **kein Besuchstermin beworben** — eine Adresse im Impressum ist keine
Einladung.

---

## 1. Technischer Rahmen

> **SARTU ist ein Projekt, nicht zwei.** Öffentliche Website, Kundenbereich (`/portal/`),
> Adminbereich (`/admin/`) und Serverfunktionen (`/api/`) liegen in **einem** modularen PHP-Projekt
> unter **einer** Domain. Die verbindliche Architektur, Verzeichnisstruktur und
> Hosting-Anforderung stehen in `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` §1 — dieser Abschnitt
> ergänzt nur, was die **öffentlichen Seiten** betrifft.

- **PHP 8.3+, serverseitig gerendert.** Kein CMS, kein Vollframework, kein SPA-Framework, kein Node als Zielsystem, kein Build-Schritt fürs Frontend
- **Öffentliche Seiten sind cachebar:** Sie hängen nicht an einer Sitzung und dürfen als statische Antwort ausgeliefert werden (Server- oder Dateicache). Wo Inhalte fest sind, dürfen sie beim Ausrollen als HTML vorgeneriert werden
- **Kein** externes CDN für Schriften, CSS oder JS. Schriften selbst gehostet als WOFF2, `font-display: swap`
- **JavaScript budgetiert statt verboten:** **≤ 75 KB gzip auf der Startseite, ≤ 40 KB auf Unterseiten.** Pflichtfunktionen: mobile Navigation, FAQ-Akkordeon, Komfort im Bedarfsscheck. Darüber hinaus **maximal zwei bewusste Markenmomente pro Seite**
- **Ohne JavaScript vollständig nutzbar:** Inhalte lesbar, Links funktionieren, **beide Formulare absendbar**, Bedarfsscheck vollständig durchlaufbar (§9.5a). **Kein Inhalt darf erst durch eine Scroll-Animation sichtbar werden**
- **`prefers-reduced-motion: reduce` ist Pflicht:** alle nicht-essenziellen Bewegungen aus, Inhalte sofort sichtbar
- **Bibliotheken:** CSS zuerst. Erlaubt bei Bedarf: Lenis (~3 KB), GSAP + ScrollTrigger (~34 KB, seit 04/2025 vollständig kostenlos inkl. SplitText/MorphSVG). **Nicht erlaubt:** Vanta.js, Three.js als Deko, Barba.js
- **Ziele (Labormessung):** LCP < 2,5 s · TBT < 200 ms · CLS < 0,1 · mobil zuerst entwickelt. **Animationen dürfen CLS nicht verschlechtern**
- **`html lang="de"`**, semantische Landmarks (`header`, `nav`, `main`, `footer`), sichtbarer Fokus, Skip-Link „Zum Inhalt springen"
- **Breakpoints:** ≤ 599 px mobil · 600–1023 px Tablet · ≥ 1024 px Desktop. Maximale Inhaltsbreite 1280 px, Fließtext max. 68 Zeichen
- **Designsystem:** Farben, Schriften und Radien werden nach `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` recherchiert und vorgelegt. Verbindlich ist nur: **alle Werte als zentrale Variablen**, Komponenten nutzen **nie** rohe Farbwerte, Radius **einheitlich**, keine „Karten in Karten"
- **Wiederverwendung ist Pflicht:** Layouts, Partials und Komponenten aus `/app/views` werden von öffentlichen Seiten und Kundenbereich **gemeinsam** genutzt. Kein kopiertes Markup zwischen Seiten

> **Was „keine externen Verbindungen" bedeutet — und was nicht.**
> **Verboten sind Fremdanbieter zur Laufzeit:** Schrift-, Skript- und Stil-CDNs, Analyse- und Tracking-Dienste, eingebettete Karten, Videoportale, Chat-Widgets, Werbe- und Rätselbild-Dienste, externe Bildhoster. Kein Netzwerkaufruf des Browsers darf eine fremde Domain treffen.
> **Kein Sonderfall mehr sind die Formulare:** Sie laufen im selben Programm. Es gibt **kein** gemeinsames Geheimnis und **keinen** Aufruf über das Netz (Portal-Lastenheft §4b.1).

## 2. Globale Sprach- und Inhaltsregeln

**Ansprache:** durchgängig **„Sie"**. Marke immer **`SARTU`** (Versalien), auch im Fließtext.

**Verbotene Wörter und Aussagen** (gelten für alle Seiten, prüfbar per Suche):

| Verboten | Grund | Stattdessen |
|---|---|---|
| „wartungsarm", „wartungsfrei", „kaum Wartung" | entwertet den Rundum-Schutz | „keine Wartung **für Sie**" |
| „rechtssicher", „abmahnsicher", „DSGVO-konform" (absolut) | Rechtsberatungs-/Haftungsrisiko | „datensparsam umgesetzt", „Rechtstexte technisch eingebunden" |
| „garantiert Platz 1", „garantierte Sichtbarkeit", „garantierte KI-Nennung" | unhaltbar | „Fundament für Sichtbarkeit, ohne Rankinggarantie" |
| „spart 80 % Zeit" o. ä. Prozentwerte | keine eigenen Daten | qualitativ formulieren (§5, Abschnitt „Ablauf") |
| „Paket wählen", „konfigurieren", „Extras hinzufügen", „SEO buchen" | widerspricht Angebotslogik | „Bedarf prüfen lassen", „einschätzen lassen" |
| „günstig", „billig", „Schnäppchen" | falsche Positionierung | „Festpreis", „klarer Gesamtpreis" |
| „unser Team" (solange Einzelperson) | Ehrlichkeit | „gründergeführt", „ich" / „wir" nur wenn zutreffend |

**Pflichthinweis bei jeder Preisnennung:**
> Alle Preise netto zzgl. gesetzlicher Umsatzsteuer. Ausschließlich für Unternehmer.

**Keine** Fake-Referenzen, -Bewertungen, -Logos, -Adressen, -Teamfotos. Portal-Screens tragen den Vermerk **„Musteransicht"**, solange sie kein freigegebenes echtes Kundenprojekt zeigen.

---

## 2a. Visuelle Ebene — nicht in diesem Dokument festgelegt

> **Farbwelt, Schriften, Radien, Textur, Logo und Bewegungsdetails sind hier bewusst NICHT vorgegeben.**
> Die ausführende KI stellt sie nach **`CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`** aus echten Bibliotheken und Referenzen zusammen und legt **2–3 Vorschläge** zur Entscheidung vor. Erst danach wird gebaut.

Frühere Designentwürfe unter `design/_verworfen/` sind **nicht** zu verwenden — auch nicht als Anregung.

**Was dieses Lastenheft weiterhin verbindlich vorgibt** (Anforderungen, keine Gestaltung):

- **Struktur und Inhalt** jeder Seite: Sektionsreihenfolge, Copy, Überschriften, Feldlabels, Fehlermeldungen (§3 ff.)
- **Leistung:** ≤ 75 KB gzip JS Startseite, ≤ 40 KB Unterseiten; **vor Livegang im Labor** LCP < 2,5 s · TBT < 200 ms · CLS < 0,1. **Echtes INP** ist ein Felddatenwert und wird erst nach dem Livegang gemessen (§17a)
- **Barrierefreiheit:** Kontrast ≥ 4,5:1, sichtbarer Fokus, volle Tastaturbedienung, Skip-Link, `prefers-reduced-motion` wirksam, Zustände nie allein über Farbe
- **Ohne JavaScript nutzbar** — kein Inhalt erscheint erst durch eine Scroll-Animation
- **Bewegung budgetiert:** höchstens zwei bewusste Markenmomente pro Seite, keine Animation über Text oder CTA
- **Lizenzpflicht:** jedes eingesetzte Teil muss kommerzielle Nutzung **und** Weitergabe im Kundenprojekt erlauben (SARTU verkauft Websites weiter)
- **Keine externen Verbindungen zur Laufzeit** — Schriften, Icons, Skripte selbst gehostet
- **Positionierungsschutz:** die Seite darf nicht erkennbar aus einem Template stammen; keine Farbverläufe, Leuchtflächen, schwebenden Dashboard-Karten, Karten in Karten, Emoji-Marker, Handschlag-Stockfotos, generischen WebGL-Hintergründe
- **Inhaltliche Wahrheit:** keine erfundenen Logos, Bewertungen, Referenzen oder Kundenzahlen; Portal-Ansichten aus echter Oberfläche, gekennzeichnet als „Musteransicht"


## 3. Header-Navigation (final)

**Desktop (≥ 1024 px):**
`[SARTU-Wortmarke]` — links · `Leistungen · Preise · Ablauf · Ratgeber · Lexikon` — Mitte · `Kontakt` (Textlink) + **`Bedarf prüfen lassen`** (Button) — rechts

- Höhe 72–80 px. Beim Scrollen kompakt sticky (Höhe 56–60 px), Hintergrund deckend, dünne Trennlinie unten.
- Aktiver Menüpunkt wird markiert (Unterstrich oder Farbe, keine Fettung-Verschiebung).
- **`Über uns`** ist bewusst **nicht** in der Hauptnavigation — Link steht im Footer und in der Startseiten-Sektion „Verantwortung". Begründung: die Navigation bleibt bei 5 Punkten, Vertrauen wird über Inhalte transportiert.

**Mobil (≤ 1023 px):**
- Links Wortmarke, rechts Menü-Icon (44 × 44 px Trefferfläche).
- Menü öffnet als **Vollbild-Overlay**, nicht als Dropdown. Reihenfolge: Leistungen · Preise · Ablauf · Ratgeber · Lexikon · Kontakt · Über uns · dann großflächiger CTA `Bedarf prüfen lassen`.
- Schließen per X, `Esc` und Klick außerhalb. Fokus wird im Overlay gehalten, beim Schließen zurück auf das Menü-Icon.
- **Kein** sticky Bottom-CTA-Balken (verdeckt Inhalte, wirkt aufdringlich).

---

## 4. Footer (final)

Fünf Spalten auf Desktop, gestapelt auf Mobil (Reihenfolge wie unten).

| Spalte | Inhalt |
|---|---|
| **1 – Marke** | SARTU-Wortmarke · Kurzpositionierung: „Individuell programmierte Firmenwebsites zum Festpreis. Geplant, geschrieben, programmiert und betrieben." |
| **2 – Leistungen** | Webdesign · Website-Texte · Sichtbarkeit (SEO/GEO) · Rundum-Schutz · Portal |
| **3 – Wissen** | Ratgeber · Lexikon |
| **4 – Unternehmen** | Ablauf · Preise · Über uns · Kontakt |
| **5 – Rechtliches** | Impressum · Datenschutz *(AGB nur, wenn anwaltlich final)* |

**Fußzeile darunter:** `© 2026 SARTU` · `Alle Preise netto zzgl. USt. · Ausschließlich für Unternehmer`

**Verboten im Footer:** Ortslisten, Keyword-Linklisten, Social-Icons ohne echte gepflegte Profile, „Made with"-Hinweise.

---

## 5. Startseite `/`

**Title (58 Z.):** `Firmenwebsite zum Festpreis, ohne WordPress | SARTU`
**Meta Description (152 Z.):** `SARTU plant, textet, programmiert und betreibt Ihre Firmenwebsite zum Festpreis. Geführtes Portal statt E-Mail-Chaos, SEO-Basis ab Launch, kein WordPress.`
**H1:** `Individuell programmierte Firmenwebsites zum Festpreis.`
**Zielumfang:** 750–950 Wörter · **Schema:** `Organization`, `WebSite` · `FAQPage` optional (bringt keine Rich Results mehr, s. §16)

### Sektion 1 — Hero

- **Eyebrow:** `Webdesign-Agentur für Firmenwebsites`
- **H1:** `Individuell programmierte Firmenwebsites zum Festpreis.`
- **Lead (38 W.):**
  > SARTU plant, textet, programmiert und betreibt Ihre Website. Sie beantworten nur die Fragen zu Ihrem Unternehmen — Struktur, Design, Technik und die SEO-Grundlage übernehmen wir und verantworten das Ergebnis.
- **Primär-CTA:** `Bedarf prüfen lassen` → `/briefing`
- **Sekundär-CTA:** `Preise ansehen` → `/preise`
- **Preishinweis (klein, direkt unter den Buttons):** `Alle Preise netto zzgl. USt. Ausschließlich für Unternehmer.`
- **Trust-Zeile (4 Punkte):** `Festpreis vorab` · `Texte inklusive` · `Portal statt E-Mail-Chaos` · `SEO-Basis ab Launch`
- **Visual rechts (Desktop) / darunter (Mobil):** Portal-Cockpit-Screenshot, Badge „Musteransicht".

**Verhalten:** Desktop zweispaltig (Text 55 %, Visual 45 %). Mobil einspaltig — **H1 zuerst, Buttons direkt darunter, Visual danach**, Trust-Zeile als 2 × 2-Raster. Das Visual scrollt **nicht** horizontal. Unterer Rand des ersten Viewports zeigt bereits einen Anschnitt der nächsten Sektion.

### Sektion 2 — Problem und Entlastung

- **H2:** `Eine Website darf nicht Ihr zweiter Job werden.`
- **Text (55 W.):**
  > Die meisten Firmenwebsites scheitern nicht am Design, sondern an offenen Entscheidungen: Welche Seiten? Welche Texte? Welches System? Welche SEO-Einstellungen? SARTU nimmt Ihnen diese Entscheidungen gebündelt ab und fragt nur die Fakten ab, die wirklich aus Ihrem Unternehmen kommen müssen.
- **Drei Blöcke:**
  1. **`Sie liefern Fakten`** — Leistungen, Zielgruppen, Einzugsgebiet, Belege, Freigaben.
  2. **`SARTU entscheidet`** — Struktur, Design, Technik, SEO-Grundlage, Hosting, Domainprozess.
  3. **`Das Portal führt`** — Angebot, Zahlung, Briefing, Dateien, Vorschau, Freigabe, spätere Pflege.
- **Textlink:** `So läuft ein Projekt ab` → `/ablauf`

### Sektion 3 — Drei Ergebnisse, eine Empfehlung

- **H2:** `Sie wählen kein Paket. Wir empfehlen eines.`
- **Einleitung (32 W.):**
  > Sie müssen nicht wissen, wie viele Seiten oder welche Technik Sie brauchen. Die kurze Bedarfseinschätzung zeigt, welche Lösung voraussichtlich passt — geprüft wird sie anschließend persönlich.
- **Platzhirsch (hervorgehoben):** Badge `Empfehlung` · `Platzhirsch` · `7.900 € einmalig` · `+ 249 €/Monat Rundum-Schutz` · `Erstes Jahr: 10.888 € netto`
  > Für Unternehmen mit mehreren Leistungen, regionalem Wettbewerb oder Recruitingbedarf. Wir entwickeln daraus ein Vertriebs-, Vertrauens- und Recruiting-System mit bis zu 16 strategischen Seiten.
  Merkmale: `stärkere Leistungs- und Regionsstruktur` · `Team, Karriere, Projekte — wenn sinnvoll` · `ein passender Anfrage-, Buchungs- oder Bewerbungsweg` · `SEO-Grundlage pro Seite`
  CTA: `Bedarf prüfen lassen`
- **Start (kompakt):** `1.490 € + 59 €/Monat` — „Ein fokussierter One-Pager für ein klares Angebot." · CTA `Einschätzen lassen`
- **Wachstum (kompakt):** `3.900 € + 129 €/Monat` — „Eine vollständige Firmenwebsite mit bis zu acht strategischen Seiten." · CTA `Einschätzen lassen`
- **Sonderprojekt (eine Zeile):**
  > Shop, Login, komplexe Buchung, Schnittstellen oder mehrere Marken prüfen wir als Sonderprojekt ab 12.500 €.

**Verhalten:** Desktop Platzhirsch deutlich größer (ca. 55 % Breite oder doppelte Höhe). Mobil **Platzhirsch zuerst**, dann Start/Wachstum. Die Buttons von Start/Wachstum sind visuell schwächer als der Platzhirsch-CTA.

### Sektion 4 — Leistungslandkarte

- **H2:** `Alles, was eine Firmenwebsite braucht — aber nicht als Add-on-Liste.`
- **Einleitung (30 W.):**
  > Diese Leistungen sind Bausteine eines sinnvollen Ergebnisses. Sie buchen sie nicht einzeln; wir ordnen sie im Angebot passend zu Ihrem Ziel ein.
- **Acht breite Zeilen** (Titel · ein Satz · Tags), **keine** Kachelwand, **keine** Preise:

| Titel | Satz | Tags |
|---|---|---|
| Strategie und Seitenstruktur | Wir legen fest, welche Seiten Ihr Ziel wirklich brauchen — und welche nicht. | Sitemap · Nutzerführung · Suchintention |
| Webdesign und Programmierung | Individuell aus unserem Designsystem programmiert, ohne WordPress und ohne Baukasten. | kein WordPress · responsive · schnell |
| Website-Texte | Wir schreiben die Texte aus Ihren Fakten und Stichpunkten — Sie liefern keinen fertigen Webtext. | aus Stichpunkten · Faktenprüfung |
| SEO-Grundlage | Jede Seite startet mit klarem Thema, sauberen Metadaten und strukturierten Daten. | Titles · Schema · interne Links |
| Lokale Sichtbarkeit | Echte Unternehmensdaten statt dünner Ortsseiten mit ausgetauschtem Stadtnamen. | Local SEO · konsistente Daten |
| Domain und Launch | Wir prüfen, verbinden und schalten live — Ihre bestehende E-Mail bleibt dabei erreichbar. | DNS · E-Mail-Schutz · Weiterleitungen |
| Portal und Freigaben | Angebot, Briefing, Vorschau und Feedback laufen an einem Ort statt in E-Mail-Ketten. | Briefing · Feedback · Pflege |
| Rundum-Schutz | Wir betreiben die Website danach: Hosting, Sicherheit, Backups, Monitoring. | Betrieb · Backups · Monitoring |

- **CTA:** `Alle Leistungen im Überblick` → `/leistungen`

### Sektion 5 — Das Portal

- **H2:** `Ihr Projekt bleibt an einem Ort.`
- **Text (48 W.):**
  > Kein loses Formular, keine endlose E-Mail-Kette, kein WordPress-Editor. Im SARTU-Portal sehen Sie den nächsten Schritt, bestätigen Fakten, laden Material hoch, prüfen die Vorschau, geben frei — und pflegen später Ihre Öffnungszeiten und Kontaktdaten selbst.
- **Zwei Listen nebeneinander:**
  - **`Im Portal`** — Angebot und Annahme · Rechnungen und Zahlung · Domainstatus · Briefing-Aufgaben · Uploads und Bildrechte · Vorschau und Feedback · Öffnungszeiten, Kontakt, Seitenstatus nach Launch
  - **`Nicht im Portal`** — Layout selbst bauen · Plugins installieren · SEO-Felder frei verstellen · Navigation oder URLs umbauen · Code bearbeiten · Seiten hart löschen
- **Bild:** Portal-Screenshot „Briefingaufgaben", Badge „Musteransicht".
- **CTA:** `Das Portal ansehen` → `/leistung-portal`

### Sektion 6 — SEO und GEO sind eingebaut

- **H2:** `Gefunden werden ist kein späteres Extra.`
- **Text (42 W.):**
  > Jede SARTU-Website startet mit klaren Seitenthemen, sprechenden Adressen, sauberer interner Verlinkung, Metadaten, strukturierten Daten und einer soliden Performance-Grundlage. Späterer Ausbau baut auf echten Suchdaten auf — nicht auf pauschalen SEO-Paketen.
- **Drei Spalten:**
  - `Menschen verstehen` — klare Antworten, Preise, Ablauf und Grenzen stehen sichtbar auf der Seite.
  - `Suchmaschinen erfassen` — sauberes HTML, Sitemap, Canonicals, strukturierte Daten, Ladezeit.
  - `KI-Antworten einordnen` — konsistente Unternehmensfakten, FAQ und Definitionen statt Textwüsten.
- **Pflichthinweis:**
  > Rankings, Anfragen oder Nennungen in KI-Systemen kann niemand garantieren. Wir bauen das Fundament und halten die technische Suchgesundheit im Betrieb im Blick.

### Sektion 7 — Ablauf

- **H2:** `Von wenigen Angaben zur fertigen Website.`
- **Sechs Schritte** (nummeriert, weil es eine echte Reihenfolge ist):
  1. **Bedarfsscheck** — Wenige Fragen zu Unternehmen, Ziel, Umfang und Domain.
  2. **Geprüftes Angebot** — Wir bestätigen Empfehlung, Seitenstruktur, Preis und Zahlungsplan.
  3. **Portal-Onboarding** — Bekannte Fakten übernehmen wir, offene klären wir gezielt.
  4. **Produktion** — Wir bauen die Website; KI unterstützt, geprüft und freigegeben wird von uns.
  5. **Vorschau und Freigabe** — Sie sehen die Website und geben gebündelt Feedback.
  6. **Launch und Betrieb** — Domain, Monitoring, Backups, technische Suchgesundheit.
- **CTA:** `Ablauf im Detail` → `/ablauf`

### Sektion 8 — Bedarfsscheck-Einstieg

- **H2:** `Welche Website passt zu Ihrem Unternehmen?`
- **Text (36 W.):**
  > Der Bedarfsscheck fragt nicht nach Seitenzahlen, Farben oder SEO-Stufen. Sie beantworten wenige Fragen zu Ihrem Geschäft und sehen sofort eine vorläufige Empfehlung mit Preis. Danach prüfen wir persönlich.
- **Chips (nur Anzeige):** `Branche` · `Region` · `Ziel` · `Umfang` · `Domain` · `Besonderheiten`
- **Vertrauenszeile:** `Dauert etwa 3 Minuten` · `Preis vor Kontaktdaten` · `Kein Pflichttermin` · `Unverbindlich`
- **CTA:** `Bedarf prüfen lassen` → `/briefing`

### Sektion 9 — FAQ (8 Fragen, sichtbar, Akkordeon)

1. **Muss ich mir selbst ein Paket aussuchen?**
   > Nein. Sie beschreiben Ihr Unternehmen und Ihr Ziel; wir empfehlen genau eine Lösung und begründen sie. Wenn eine kleinere Lösung reicht, empfehlen wir die kleinere.
2. **Schreiben Sie die Texte?**
   > Ja. Sie liefern Fakten, Stichpunkte und vorhandene Unterlagen — wir schreiben daraus die Website-Texte. Erfundene Belege oder ungeprüfte Fachaussagen gibt es nicht.
3. **Warum gibt es keine Liste mit Zusatzoptionen?**
   > Weil Zusatzlisten den Preis unklar machen. Ein Standardangebot endet exakt beim genannten Festpreis. Passt eine Anforderung nicht hinein, bekommen Sie dafür ein eigenes Angebot mit eigenem Festpreis.
4. **Was passiert mit meiner Domain und meinen E-Mail-Adressen?**
   > Die Domain gehört Ihnen — auch wenn wir sie technisch verwalten. Vor jeder Änderung sichern wir Ihre bestehenden Einträge, damit Ihre E-Mail-Adressen beim Umschalten erreichbar bleiben.
5. **Kann ich später selbst etwas ändern?**
   > Ja, die Dinge, die sich wirklich ändern: Öffnungszeiten, Kontaktdaten, Team- und Projekteinträge, Bilder in vorhandenen Bildplätzen, und ob eine Seite sichtbar ist. Layout, Technik und Adressen bleiben bei uns.
6. **Ist SEO enthalten?**
   > Die Grundlage ja, ab dem ersten Tag: Seitenthemen, Metadaten, strukturierte Daten, interne Verlinkung, Ladezeit. Ein späterer Ausbau folgt echten Suchdaten und ist ein eigenes Angebot.
7. **Warum kein WordPress?**
   > Weil Sie sich dann um Updates, Plugins und Sicherheitslücken kümmern müssten. Wir programmieren die Website ohne diese Abhängigkeiten und betreiben sie selbst.
8. **Können Sie eine bestimmte Google-Position zusichern?**
   > Nein, und niemand kann das seriös. Wir bauen das technische und inhaltliche Fundament und halten es im Betrieb sauber.

### Sektion 10 — Abschluss-CTA

- **H2:** `Wenige Angaben reichen für den ersten Schritt.`
- **Text:** `Sie beschreiben Ihr Unternehmen, Ziel und Ihren Domainstatus. Danach sehen Sie eine vorläufige Empfehlung mit Preis.`
- **Buttons:** `Bedarf prüfen lassen` (primär) · `Preise ansehen` (sekundär)
- **Hinweis:** `Unverbindlich bis zum geprüften Angebot. Alle Preise netto zzgl. USt. Ausschließlich für Unternehmer.`

---

## 6. `/leistungen`

**Title:** `Leistungen: Webdesign, Texte, SEO und Betrieb | SARTU`
**Meta (155 Z.):** `Webdesign, Website-Texte, SEO-Grundlage, Domain und Launch, Portal und laufender Betrieb — bei SARTU als ein Ergebnis zum Festpreis statt als Einzeloptionen.`
**H1:** `Website, Texte, Sichtbarkeit und Betrieb als ein System.`
**Umfang:** 700–850 Wörter · **Schema:** `Service`, `BreadcrumbList` (`FAQPage` optional, s. §16)

**Sektionen:**
1. **Hero** — H1 + Lead: „Sie bekommen kein Bündel einzelner Leistungen, sondern ein Ergebnis: eine Website, die Ihr Angebot erklärt, Anfragen erzeugt und danach zuverlässig betrieben wird." + CTA.
2. **Kurz gesagt** (Antwortmodul, 45 W.) — „SARTU verbindet Strategie, Texte, Design, Programmierung, SEO-Grundlage, Domain und Launch, Portal und Betrieb zu einem Festpreis-Ergebnis. Was Ihr Projekt davon in welcher Tiefe braucht, entscheiden wir im Angebot — nicht Sie im Bestellformular."
3. **Leistungslandkarte** — dieselben acht Zeilen wie Startseite, aber je 3–4 Sätze statt einem, plus Link auf die jeweilige Leistungsseite (soweit vorhanden).
4. **Was Sie nicht entscheiden müssen** — Liste: System und Technik · Seitenzahl · Designstil · SEO-Stufe · Hosting · Registrar · Wartungsminuten.
5. **Wie tief es je Lösung geht** — Tabelle Start / Wachstum / Platzhirsch / Sonderprojekt mit Ergebnis-Spalte (nicht Feature-Häkchen).
6. **FAQ (3):** „Kann ich einzelne Leistungen dazubuchen?" · „Was ist, wenn ich später mehr brauche?" · „Übernehmen Sie auch bestehende Websites?"
7. **CTA-Band.**

---

## 7. `/preise`

**Title:** `Preise: Firmenwebsite ab 1.490 € netto | SARTU`
**Meta (149 Z.):** `Start 1.490 €, Wachstum 3.900 €, Platzhirsch 7.900 € netto — jeweils mit festem Betriebspaket. Erstjahreskosten und Zahlungsplan transparent aufgeschlüsselt.`
**H1:** `Klare Preise. Wir prüfen, was wirklich passt.`
**Umfang:** 650–800 Wörter · **Schema:** `Service`, `BreadcrumbList` (`FAQPage` optional, s. §16)

**Sektionen:**
1. **Hero** — H1 + Lead: „Sie müssen kein Paket auswählen. Die kurze Bedarfseinschätzung zeigt, welche Lösung wahrscheinlich passt; wir prüfen das Ergebnis persönlich, bevor Sie ein Angebot bekommen." + CTA.
2. **Preisübersicht** — Platzhirsch groß, Start/Wachstum kompakt, Sonderprojekt als Abzweig.
3. **Erstjahrestabelle** (tabellarische Ziffern, mobil horizontal scrollbar):

| Lösung | Einmalig netto | Betrieb / Monat | Erstes Jahr netto |
|---|---:|---:|---:|
| Start | 1.490 € | 59 € | 2.198 € |
| Wachstum | 3.900 € | 129 € | 5.448 € |
| **Platzhirsch** | **7.900 €** | **249 €** | **10.888 €** |
| Sonderprojekt | ab 12.500 € | ab 249 € | ab 15.488 € |

4. **Was jedes Projekt enthält** — Strategie · Texte · Design · Programmierung · SEO-Grundlage · Portal · Domainverbindung · Launch.
5. **Der Rundum-Schutz** — Framing zwingend nach §2: „Keine Wartung für Sie." Enthalten: Hosting, SSL, Backups, Monitoring, technische Updates, technische Suchgesundheit, Formularprüfung, Portalzugang. **Nicht** enthalten: unbegrenzte Text- oder Designänderungen, neue Seiten, neue Ziele. Selbst pflegbar: Öffnungszeiten, Kontaktdaten, vorhandene Einträge, Seitenstatus.
6. **Domain und E-Mail** — Kunde bleibt Inhaber; eine normale Domain bis 30 € netto/Jahr ist im Betrieb enthalten; bestehende E-Mail wird geschützt.
7. **Zahlung** — Start/Wachstum 50/50 · Platzhirsch 40/30/30 · Zahlungsziel 10 Tage · Zahlung im Portal · Produktionsslot nach erster Zahlung.
8. **FAQ (6):** netto oder brutto? · später erweitern? · warum ist der Betrieb verpflichtend? · versteckte Kosten? · Domainkosten? · Sonderfunktionen?
9. **CTA-Band.**

**Bildregel:** keine großen Fotos — Preise brauchen Scanbarkeit. Erlaubt: kleine Portal-Zahlungsansicht als Muster, **ohne** realistische Rechnungsnummern oder echte Namen.

---

## 8. `/ablauf`

**Title:** `Ablauf: vom Bedarfsscheck zur fertigen Website | SARTU`
**Meta (151 Z.):** `So läuft ein SARTU-Projekt: kurzer Bedarfsscheck, geprüftes Festpreisangebot, Portal-Onboarding, Produktion, Vorschau und Freigabe, Launch und laufender Betrieb.`
**H1:** `Ein Websiteprojekt ohne Termin-Marathon.`
**Umfang:** 700–850 Wörter · **Schema:** `BreadcrumbList` (`FAQPage` optional, s. §16)

**Sektionen:**
1. **Hero** — Lead: „Standardprojekte laufen bei SARTU digital und gebündelt. Ein Gespräch ist jederzeit möglich, aber nicht Pflicht: Angebot, Briefing, Zahlungen, Domain, Vorschau und Freigaben laufen im Portal."
2. **Vergleich** (Tabelle, zwei Spalten — **ohne** Prozentangaben):

| Klassisches Projekt | Bei SARTU |
|---|---|
| Erstgespräch als Pflichttermin | Bedarfsscheck in etwa 3 Minuten |
| Kickoff-Termin und Fragebogen | Portal übernimmt bekannte Fakten, fragt nur Lücken |
| Feedback verteilt über E-Mails und Anrufe | gebündeltes Feedback an einer Stelle |
| Viele offene Entscheidungen beim Kunden | Struktur, Design und Technik entscheiden wir |
| Preis wird im Verlauf konkret | Festpreis steht vor Auftrag |

3. **Acht Schritte** — ausführlich, je 2–3 Sätze: Bedarfsscheck · unsere Prüfung · Angebot im Portal · Annahme und erste Zahlung · Domain und Onboarding · Produktion mit QA · Vorschau, Feedback, Abnahme · Launch und Betrieb.
4. **Portal-Screens** — fünf Ansichten mit Bildunterschrift (Angebot · Briefing · Domain · Vorschau/Feedback · Pflege).
5. **Was Sie wirklich tun müssen** — Fakten bestätigen · Material hochladen · Domain bestätigen · Vorschau prüfen · freigeben.
6. **Was wir entscheiden** — Empfehlung · Seitenstruktur · Nutzerführung · Design · SEO-Grundlage · Technik · Hosting · DNS-Plan.
7. **Zeitrahmen** — „Nach vollständigem Start: Start 7–10, Wachstum 10–15, Platzhirsch 15–25 Werktage. Der Zeitraum beginnt, wenn Zahlung, Briefing und Material vorliegen."
8. **CTA-Band.**

---

## 9. `/briefing` — Bedarfsscheck (Lumi), feldgenau

**Title:** `Bedarf prüfen lassen — unverbindliche Empfehlung | SARTU`
**Meta (147 Z.):** `Beantworten Sie wenige Fragen zu Ihrem Unternehmen und sehen Sie sofort eine vorläufige Empfehlung mit Festpreis. Unverbindlich, ohne Termin, in etwa drei Minuten.`
**H1:** `Welche Website passt wirklich zu Ihrem Unternehmen?`
**Schema:** `BreadcrumbList` · **Indexierung:** `index` (Einstiegsseite), Ergebnisschritte `noindex`

### 9.1 Einstiegsbildschirm

- Lead: „Sie müssen weder Paket noch Seitenzahl, Designrichtung oder SEO-Stufe kennen. Beantworten Sie wenige Fragen zu Ihrem Geschäft — danach sehen Sie eine vorläufige Empfehlung mit Preis."
- Vertrauenspunkte: `Dauert etwa 3 Minuten` · `Preis vor Kontaktdaten` · `Kein Pflichttermin` · `Keine Auswahl von Zusatzoptionen` · `Unverbindlich bis zum geprüften Angebot`
- Button: `Bedarf prüfen lassen`
- Fortschritt: `Thema 1 von 5` (nicht „Frage 1 von 10")

### 9.2 Felder (Labels, Hilfetexte, Validierung — final)

**Thema 1 — Ihr Unternehmen**

| Feld | Label | Typ | Pflicht | Hilfetext | Fehlermeldung |
|---|---|---|---|---|---|
| 1.1 | Was bietet Ihr Unternehmen an? | Textarea, 1–3 Sätze | ja | „Zum Beispiel: Wir sanieren Bäder und Heizungen für Privatkunden im Umkreis von 40 km." | „Bitte beschreiben Sie Ihr Angebot in ein bis drei Sätzen." |
| 1.2 | Wo arbeiten Sie hauptsächlich? | Text (Ort oder PLZ) | ja | „Ort oder Postleitzahl genügt." | „Bitte geben Sie Ort oder Postleitzahl an." |
| 1.3 | Größeres Einzugsgebiet? | Text | nein | „Optional, z. B. Umkreis oder Region." | – |
| 1.4 | Gibt es bereits eine Website? | Radio: Ja / Nein / Bin unsicher | ja | – | „Bitte wählen Sie eine Antwort." |
| 1.5 | Adresse der bestehenden Website | URL, erscheint nur bei „Ja" | ja (bedingt) | „Auch eine alte oder unfertige Seite hilft uns." | „Bitte geben Sie eine gültige Internetadresse an, z. B. beispiel.de" |

**Thema 2 — Ihr Ziel**

| Feld | Label | Typ | Pflicht | Optionen |
|---|---|---|---|---|
| 2.1 | Was soll die neue Website vor allem erreichen? | Radio (eine Wahl) | ja | Mehr passende Anfragen · Besser gefunden werden · Neue Mitarbeitende gewinnen · Vertrauen und Professionalität stärken · Termine oder Bewerbungen vereinfachen · Etwas anderes |
| 2.2 | Wen möchten Sie vor allem erreichen? | Radio | ja | Privatkunden · Unternehmen · Bewerberinnen und Bewerber · Mehrere Gruppen · Noch unklar |

Hilfetext 2.1: „Wählen Sie das Ziel, das in den nächsten zwölf Monaten den größten Unterschied machen würde."
Fehlermeldung: „Bitte wählen Sie ein Hauptziel."

**Thema 3 — Umfang**

| Feld | Label | Typ | Pflicht | Optionen |
|---|---|---|---|---|
| 3.1 | Was trifft auf Ihr Unternehmen zu? | Checkbox (mehrere) | ja | Wir haben ein klares Hauptangebot · Wir bieten mehrere eigenständige Leistungen an · Wir arbeiten in mehreren Regionen oder an mehreren Standorten · Wir suchen regelmäßig Mitarbeitende · Projekte, Referenzen oder Neuigkeiten sollen aktuell bleiben · Nichts davon / bin unsicher |

Hilfetext bei „mehrere eigenständige Leistungen": „Gemeint sind Angebote, nach denen Kunden getrennt suchen oder für die sie eine eigene Erklärung brauchen."
Regel: „Nichts davon / bin unsicher" ist **nicht** mit anderen Optionen kombinierbar.
Fehlermeldung Kombination: „‚Nichts davon' lässt sich nicht mit anderen Angaben kombinieren. Bitte wählen Sie das eine oder das andere."

**Thema 4 — Besondere Anforderungen (Gates)**

| Feld | Label | Typ | Pflicht | Optionen |
|---|---|---|---|---|
| 4.1 | Muss die Website etwas Besonderes können? | Checkbox (mehrere) | ja | Normale Anfrage oder Bewerbung über ein Formular · Einfache Terminbuchung · Produkte verkaufen oder Zahlungen annehmen · Kundenlogin oder geschützter Bereich · Verbindung zu anderer Software · Mehrere Sprachen oder getrennte Marken · Besondere Daten oder ein formaler Nachweis · Nichts davon, eine normale Firmenwebsite |

Je Option ein Beispielsatz als Hilfetext (z. B. bei „Verbindung zu anderer Software": „Zum Beispiel Warenwirtschaft, CRM oder eine eigene Schnittstelle.").
Regel: „Nichts davon" nicht mit einer Sonderfunktion kombinierbar (gleiche Fehlermeldung wie 3.1).

**Thema 5 — Domain und Termin**

| Feld | Label | Typ | Pflicht | Optionen / Hilfetext |
|---|---|---|---|---|
| 5.1 | Wie ist Ihr Domainstatus? | Radio | ja | Domain vorhanden · Neue Domain nötig · Bin unsicher |
| 5.2 | Gibt es einen festen Termin, der eingehalten werden muss? | Radio | ja | Nein, der normale Zeitrahmen passt · Ja (Datum + kurzer Grund) |
| 5.3 | Datum und Grund | Datum + Text, nur bei „Ja" | ja (bedingt) | Hilfetext: „Ein Wunschdatum ist noch keine Zusage — wir bestätigen die Machbarkeit im Angebot." |
| 5.4 | Gibt es etwas, das auf keinen Fall übersehen werden darf? | Textarea | nein | Platzhalter: „Zum Beispiel: Unsere bestehenden E-Mail-Adressen müssen weiterlaufen." |

### 9.3 Ergebnis **vor** Kontaktdaten

Anzeige (Beispiel Platzhirsch):
> **Unsere vorläufige Empfehlung: Platzhirsch**
> Sie erklären mehrere Leistungen, arbeiten in mehr als einer Region und suchen regelmäßig Mitarbeitende. Dafür reicht eine einzelne Seite nicht — hier lohnt sich eine Struktur, die Leistungen, Region und Recruiting getrennt bedient.
> **7.900 € einmalig + 249 €/Monat Rundum-Schutz** · Erstes Jahr: **10.888 € netto**

Darunter immer:
> Alle Preise netto zzgl. gesetzlicher Umsatzsteuer. Ausschließlich für Unternehmer. **Verbindlich ist erst das von SARTU geprüfte Angebot.**

- Button: `Empfehlung unverbindlich prüfen lassen`
- **Keine** Paketwechsel-Buttons, **keine** Zusatzoptionen, **keine** SEO-Auswahl.
- Bei Sonderprojekt-Gate: „Ihr Vorhaben enthält eine besondere Funktion. Solche Projekte beginnen bei 12.500 € einmalig zzgl. Betrieb. Sie erhalten dazu ein kurzes Fachmodul und danach einen geprüften Gesamtpreis."
- Bei Unklarheit: „Ihr Bedarf passt voraussichtlich in eine unserer drei Lösungen. Eine Angabe entscheidet noch über den Umfang — nach dem Absenden stellen wir Ihnen höchstens eine gebündelte Rückfrage."

### 9.4 Kontaktdaten (erst danach)

| Feld | Label | Pflicht | Fehlermeldung |
|---|---|---|---|
| Name | Vor- und Nachname | ja | „Bitte geben Sie Ihren Namen an." |
| Unternehmen | Unternehmen | ja | „Bitte geben Sie Ihr Unternehmen an." |
| E-Mail | Geschäftliche E-Mail-Adresse | ja | „Bitte geben Sie eine gültige E-Mail-Adresse an, z. B. name@firma.de" |
| Telefon | Telefon (optional) | nein | – |
| Kontaktweg | Bevorzugter Kontakt: E-Mail / Portal | ja | „Bitte wählen Sie, wie wir Sie erreichen sollen." |
| B2B | Checkbox: „Ich handle für mein Unternehmen bzw. in Ausübung meiner beruflichen oder gewerblichen Tätigkeit." | ja | „Bitte bestätigen Sie, dass Sie als Unternehmen anfragen. SARTU arbeitet ausschließlich für Unternehmer." |
| Datenschutz | Checkbox mit Link auf `/datenschutz` | ja | „Bitte bestätigen Sie die Datenschutzhinweise." |

**Kein** Newsletter-Häkchen. **Keine** Pflicht-Telefonnummer. Honeypot-Feld + serverseitige Validierung.

### 9.5 Verhalten

- **Autosave** im Browserspeicher, Wiederaufnahme möglich („Sie können später fortsetzen.").
- **Zurück** jederzeit möglich, ohne Datenverlust.
- Bei reinen Einfachauswahlen darf automatisch weitergeblättert werden; bei Mehrfachauswahl **nicht**.
- Fehler erscheinen **am Feld**, nicht als Sammelmeldung oben; erstes fehlerhaftes Feld erhält den Fokus.
- Mobil: ein Sachverhalt pro Bildschirm, Buttons in Daumenreichweite, Tastaturtyp passend (E-Mail, Telefon, Zahl).
### 9.5a Bedienbarkeit ohne JavaScript (verbindlich, keine Kann-Regel)

Der Bedarfsscheck ist der einzige Weg zu einem Angebot. Er darf an abgeschaltetem JavaScript **nicht scheitern**. Gebaut wird deshalb **zuerst** die Fassung ohne JavaScript; die Komfortfunktionen kommen darüber.

| | **Ohne JavaScript** (Grundfassung, muss funktionieren) | **Mit JavaScript** (Komfort obendrauf) |
|---|---|---|
| Schritte | echte Seiten `/briefing/1` … `/briefing/n`, je Schritt ein `POST`, Server antwortet mit dem nächsten Schritt | dieselben Schritte ohne Neuladen |
| Zwischenstand | serverseitig in einer kurzlebigen Sitzung (**Ablauf 24 Stunden**, nur Formulardaten, keine Kennung im Klartext in der URL) | zusätzlich im Browserspeicher |
| Zurück | normaler Link auf den vorigen Schritt, Angaben bleiben erhalten | ohne Neuladen |
| Bedingte Fragen | der Server entscheidet, welcher Schritt als Nächstes kommt | dieselbe Regel im Browser |
| Ergebnis vor Kontaktdaten | eigene Seite, serverseitig berechnet | gleiche Anzeige |
| Fehler | Neuanzeige des Schritts, Meldung am Feld, erstes fehlerhaftes Feld erhält den Fokus | ohne Neuladen |
| Fortschritt | `Schritt 3 von 8` als Text | zusätzlich Fortschrittsbalken |

**Die Empfehlungsregel liegt auf dem Server.** Der Browser darf sie spiegeln, aber die verbindliche Berechnung erfolgt serverseitig — sonst weichen beide Fassungen voneinander ab.

**Kein** Ersatz durch „Schreiben Sie uns einfach eine E-Mail". Eine Kontaktalternative steht zusätzlich da, ersetzt den Bedarfsscheck aber nicht.

### 9.5b Wohin die Anfrage geht (verbindlich)

Maßgeblich ist `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md`, Abschnitt **„4b. Anfrageeingang vom
Bedarfsscheck"**. Bei Abweichungen gilt dort.

| Punkt | Festlegung |
|---|---|
| **Ziel** | `POST /briefing/absenden` — eigene Domain, normales Formular |
| **Weg** | Formularannahme → `AnfrageService::anlegen()` → Tabelle `leads`. **Kein** Netzaufruf, **kein** gemeinsames Geheimnis, **keine** Tokenprüfung (ein Projekt, §1) |
| **CSRF** | Pflicht |
| **`submission_id`** | entsteht beim **Start** des Bedarfsschecks und bleibt über alle Schritte gleich. Doppelklick, Neuladen und Zurück-Taste erzeugen dadurch keinen zweiten Datensatz |
| **Empfehlung und Ampel** | **serverseitig** berechnet, **nie** aus dem abgeschickten Formular übernommen — sonst könnte sie von außen gesetzt werden |
| **Erfolg** | Weiterleitung (`303`) auf die Danke-Seite §9.6. Nie ein erneut absendbares Formular anzeigen |
| **Fehler** | Angaben bleiben erhalten, Meldung **am Feld**. Bei Serverfehler: `Wir konnten Ihre Anfrage gerade nicht speichern. Bitte versuchen Sie es in einem Moment erneut oder schreiben Sie uns an {E-Mail}.` **Keine** technischen Details |
| **Zusätzlich** | Benachrichtigungs-E-Mail an SARTU |

**Spamabwehr:** Honigtopffeld `hp_website` (unsichtbar, `aria-hidden="true"` und `tabindex="-1"`),
Zeitregel (Absenden unter 3 Sekunden wird stillschweigend verworfen, Danke-Seite erscheint trotzdem),
serverseitige Prüfung aller Felder, Rate-Limit 10 je IP und Stunde. **Kein** Rätselbild und **kein**
Fremddienst zum Start — beides wäre eine externe Verbindung mit eigener Datenschutzfolge.

> **Das allgemeine Kontaktformular** (§11) erzeugt **keinen** Datensatz. Es versendet ausschließlich
> eine E-Mail. Honigtopf, Zeitregel und Rate-Limit gelten dort gleichermaßen.

### 9.6 Danke-Seite (`noindex`)

> **Danke — wir haben Ihre Angaben.**
> Wir prüfen Ihre Anfrage persönlich und melden uns schriftlich, in der Regel innerhalb eines Werktags. Wenn etwas unklar ist, stellen wir genau eine gebündelte Rückfrage.
> Danach erhalten Sie ein Angebot mit Empfehlung, Seitenstruktur, Festpreis, Zahlungsplan und Zeitrahmen.

Kein weiteres Angebot, kein Upsell, keine Zusatz-CTAs.

---

## 10. Die fünf Leistungsseiten

**Gemeinsames Template (verbindlich, in dieser Reihenfolge):**
`H1` → `Kurz gesagt` (40–60 W. Antwortabsatz mit Preisanker) → `Für wen das passt` → `Was enthalten ist` → `Was nicht enthalten ist` → `Was es kostet` → `Wie es abläuft` → `Welche Entscheidung wir Ihnen abnehmen` → `FAQ (3)` → `CTA`
**Umfang je Seite:** 450–650 Wörter · **Schema:** `Service` + `BreadcrumbList` · genau eine H1 (`FAQPage` optional, s. §16)

| # | URL | H1 | Title | Kernaussage („Kurz gesagt") |
|---|---|---|---|---|
| 1 | `/leistung-webdesign` | Webdesign, das nicht nach Baukasten aussieht. | `Webdesign für Firmenwebsites ohne WordPress \| SARTU` | Wir programmieren Ihre Firmenwebsite individuell aus unserem Designsystem — ab 1.490 € netto, ohne WordPress, ohne Baukasten und ohne Plugins, die Sie pflegen müssten. |
| 2 | `/leistung-texte` | Website-Texte aus Ihren Fakten, nicht aus Floskeln. | `Website-Texte schreiben lassen \| SARTU` | Sie liefern Stichpunkte, Unterlagen und Fakten — wir schreiben daraus die Texte Ihrer Website. Erfundene Belege, ungeprüfte Fachaussagen und Rechtstexte sind ausgeschlossen. |
| 3 | `/leistung-seo-lokal` | Gefunden werden — regional und in KI-Antworten. | `SEO-Grundlage und lokale Sichtbarkeit \| SARTU` | Jede SARTU-Website startet mit einer belastbaren SEO-Grundlage: klare Seitenthemen, saubere Metadaten, strukturierte Daten, interne Verlinkung und echte Unternehmensdaten. Ohne Rankinggarantie und ohne dünne Ortsseiten. |
| 4 | `/leistung-wartung` | Keine Wartung für Sie. | `Rundum-Schutz: Betrieb Ihrer Website \| SARTU` | Ab 59 € netto im Monat übernehmen wir den Betrieb: Hosting, SSL, tägliche Backups, Monitoring, technische Updates, technische Suchgesundheit und Ihren Portalzugang. Kein Änderungsminuten-Konto. |
| 5 | `/leistung-portal` | Ein Projektportal, kein Website-Baukasten. | `Das SARTU-Portal: Freigaben und Pflege \| SARTU` | Im Portal laufen Angebot, Zahlung, Briefing, Dateien, Domain, Vorschau, Freigabe und spätere kleine Pflege an einem Ort. Layout, Code und Adressen bleiben bei uns. |

**Pflichtsätze je Seite:**
- 3: „Rankings, Anfragen oder Nennungen in KI-Systemen kann niemand garantieren." + „Wir erstellen keine Ortsseiten, bei denen nur der Stadtname ausgetauscht ist."
- 4: „Der Rundum-Schutz bezahlt Betrieb, Sicherheit und Verantwortung — er ist keine unbegrenzte Text- oder Design-Flatrate."
- 2: „Rechtstexte wie Impressum, Datenschutz und AGB sind nicht enthalten; wir binden freigegebene Texte technisch ein."
- 5: Zwei Listen `Sie können` / `Sie müssen nicht` (Wortlaut wie Startseite Sektion 5).

**Verschoben auf Stufe 2:** `/leistung-domain-launch` sowie die Aufteilung von 3 in getrennte SEO- und Local-SEO-Seiten.

---

## 11. `/ueber-uns` und `/kontakt`

### `/ueber-uns`
**Title:** `Über SARTU — Festpreis, Portal, klare Grenzen | SARTU`
**Meta:** `SARTU baut Firmenwebsites zum Festpreis: klarer Ablauf, geführtes Portal, keine WordPress-Pflege und KI-gestützte Produktion mit menschlicher Prüfung.`
**H1:** `Webdesign mit klaren Grenzen, festen Preisen und Verantwortung.`
**Umfang:** 400–550 Wörter

Sektionen: Hero mit **echtem Foto** (kein Fake-Teamfoto) · „Warum SARTU anders arbeitet" (4 Punkte: Festpreis statt Stundenfalle · Portal statt E-Mail-Chaos · Fakten statt Geschmacksdiskussionen · KI als Werkzeug, nicht als Ersatz) · „Was SARTU bewusst nicht ist" (kein Baukasten · kein WordPress-Hoster · keine Billig-Seitenschleuder · kein Anbieter für Privat- und Hobbyseiten) · Arbeitsweise in 5 Schritten · Verantwortung („Veröffentlicht wird nur, was wir geprüft und freigegeben haben.") · CTA.

**Ehrlichkeitsregel:** Solange Einzelperson — „gründergeführt" schreiben, nicht „unser Team". Kein Platzhalterfoto, das wie ein echtes Foto wirkt.

### `/kontakt`
**Title:** `Kontakt — Rückfrage oder Bedarf prüfen lassen | SARTU`
**Meta:** `Stellen Sie SARTU eine Rückfrage oder starten Sie den kurzen Bedarfsscheck für Ihre Firmenwebsite. Antwort in der Regel innerhalb eines Werktags.`
**H1:** `Kontakt zu SARTU.`
**Umfang:** 250–350 Wörter

Zwei Karten: **`Websitebedarf prüfen`** (primär → `/briefing`) und **`Rückfrage stellen`** (Anker zum Formular).

**Formularfelder:** Name (Pflicht) · Unternehmen (Pflicht) · E-Mail (Pflicht) · Telefon (optional) · Anliegen (Auswahl: Websiteprojekt · Bestehendes Angebot · Domain und Launch · Allgemeine Rückfrage) · Nachricht (Pflicht, min. 20 Zeichen) · Datenschutz-Checkbox (Pflicht) · Honeypot.
**Fehlermeldung Nachricht:** „Bitte beschreiben Sie Ihr Anliegen in ein bis zwei Sätzen."
**Bestätigung:** „Danke — Ihre Nachricht ist angekommen. Wir antworten schriftlich, in der Regel innerhalb eines Werktags." (`noindex`)
**Kein** Dateiupload, **keine** Pflicht-Telefonnummer.

---

## 11a. Transparenzseiten — Pflichtblock

**Maßgeblich ist `SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §3.4.** Diese Seiten sind kein Beiwerk: Sie
sind der Grund, warum SARTU in Suchergebnissen und KI-Antworten überhaupt zitiert wird. Fast jede
Agentur schreibt „Preis auf Anfrage" — SARTU nennt Zahlen. Das ist die Lücke.

**Zum Launch verbindlich:**

| URL | Kern | Was nachprüfbar drinstehen muss |
|---|---|---|
| `/ratgeber/was-kostet-eine-firmenwebsite` | die häufigste Frage im Markt | **SARTUs Zahlen konkret.** Fremde Anbieterarten nur als **Kostenbestandteile und Entscheidungslogik** — keine Markt-, Wettbewerber- oder Preisspannen. Laufende Kosten getrennt ausweisen |
| `/ratgeber/was-nicht-enthalten-ist` | schreibt sonst niemand | vollständige Ausschlussliste im Klartext, plus Begründung, warum es keine Zusatzoptionen gibt |
| `/ratgeber/was-der-betrieb-kostet` | zweithäufigste Rückfrage | was in 59/129/249 € enthalten ist, was nicht, was bei Vertragsende mit Domain und Website passiert |

**Nach dem Launch** (Reihenfolge in der Keywordstrategie §6): `/ratgeber/wie-lange-dauert-eine-website` ·
`/ratgeber/website-festpreis-erkennen` · `/ratgeber/was-eine-korrekturrunde-ist`.

**Harte Regeln:**
- Jede Zahl stammt aus dem **eigenen** Angebot und stimmt. **Keine** Marktdurchschnitte, **keine** Studien, **keine** Wettbewerberpreise
- Über fremde Anbieter nur in **Kategorien** („Baukasten", „Freelancer", „Agentur") — nie mit Namen, nie mit konkreten Preisen
- **Preise als Text, nie als Bild.** Ein Preis in einer Grafik existiert für Suchmaschinen und KI-Systeme nicht
- **Antwort zuerst:** die ersten 40–60 Wörter beantworten die Titelfrage direkt und mit Zahl
- Vergleiche als **Tabelle**, nicht als Fließtext
- Sichtbares Aktualisierungsdatum auf jeder Seite

**Technische Pflicht:** Alle Preise, Umfangsgrenzen, Korrekturrunden und Lieferkorridore stehen an
**einer** Stelle im Code und werden von dort auf allen Seiten ausgegeben. Nie doppelt pflegen.
Eine veraltete Preisangabe ist schlimmer als keine — sie wird zitiert und dann gegen SARTU verwendet.

---

## 12. Ratgeber — zwei Vergleichsartikel mit Gliederung

> **Abgrenzung zu §11a:** Wo **Zahlen** im Mittelpunkt stehen, ist es eine Transparenzseite. Hier
> geht es um **Entscheidungen** zwischen Optionen. `was-kostet-eine-firmenwebsite` steht deshalb in
> §11a und **nicht** hier.

**Hub `/ratgeber`:** H1 `Ratgeber für Firmenwebsites` · Kurzintro (2 Sätze) · Artikelliste mit Titel, Kurzantwort, Datum · **kein** Kategorienfilter bei wenigen Artikeln. Der Hub listet Ratgeber **und** Transparenzseiten (§11a), weil sie für Leser dasselbe sind.
**Je Artikel:** H1 mit Suchintention · **Kurzantwort in den ersten 2 Sätzen** · Aktualisierungsdatum sichtbar · Tabelle oder Entscheidungslogik · mindestens 2 interne Links · CTA · `Article`-Schema. **Umfang 900–1.300 Wörter.**

**1. `/ratgeber/agentur-freelancer-baukasten`**
H1: `Website erstellen lassen: Agentur, Freelancer oder Baukasten?` · Title: `Agentur, Freelancer oder Baukasten? Der ehrliche Vergleich | SARTU`
Kurzantwort: Ein Baukasten lohnt sich, wenn Sie selbst pflegen wollen und wenig Anspruch an Struktur haben. Ein Freelancer ist günstig, aber ein Ausfallrisiko. Eine Agentur liefert Verlässlichkeit — meist ohne Festpreis und mit mehr Terminen, als Ihnen lieb ist.
Gliederung: Die vier Anbieterarten in einer Tabelle (**wie sich die Kosten zusammensetzen** — nicht welche Beträge andere verlangen —, Zeitaufwand für Sie, Risiko, Pflege danach) → Wann ein Baukasten wirklich reicht → Was am Freelancer-Modell schiefgehen kann → Warum Agenturen selten Festpreise nennen → **Für wen SARTU nicht passt** → Entscheidungshilfe in fünf Fragen → CTA `/preise`.
**Pflicht:** ehrlich bleiben, und **keine fremden Preise erfinden**. Die Spalte beschreibt, *woraus* der Preis bei der jeweiligen Anbieterart entsteht (Stundensatz × Stunden, Softwaregebühr + Eigenleistung, Festpreis), nicht *wie hoch* er ist. SARTUs eigene Zahlen stehen konkret daneben — sie sind die einzigen belegten auf der Seite. Wenn ein Baukasten für einen Fall reicht, steht das so da. Genau das macht die Seite glaubwürdig.

**2. `/ratgeber/webdesign-ohne-wordpress`**
H1: `Firmenwebsite ohne WordPress: Wann sich das lohnt` · Title: `Firmenwebsite ohne WordPress — Vorteile und Grenzen | SARTU`
Kurzantwort: Ohne WordPress entfallen Plugin-Updates, Sicherheitslücken und Kompatibilitätsprobleme. Der Preis dafür ist weniger Selbstbedienung — Inhalte ändert nicht mehr jeder selbst.
Gliederung: Warum WordPress so verbreitet ist → Was daran im Alltag Arbeit macht → Alternativen (Baukasten, statisch, individuell) → **Was man dabei aufgibt** → Für wen sich was eignet (Entscheidungstabelle) → Wie SARTU es löst → CTA `/leistung-webdesign`.

**Nach dem Launch** (Reihenfolge in `SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §6):
`agentur-auswaehlen-kriterien` · `relaunch-sinnvoll` · `website-handwerker-fehler` · `bfsg-firmenwebsite`.

> **Bei „Agentur auswählen" später:** Kriterien nennen, **keine** Rangliste. Eine Seite, auf der SARTU
> sich selbst zur besten Wahl erklärt, ist unglaubwürdig und wettbewerbsrechtlich riskant.

---

## 13. Lexikon — 8 Startbegriffe (final sortiert)

**Hub `/lexikon`:** H1 `Website-Lexikon` · Kurzintro · alphabetische Liste mit Ein-Satz-Definition · **kein** Suchfeld bei 8 Begriffen (erst ab ca. 40).
**Begriffsseite (8 Teile, verbindlich):** H1 = Begriff · Kurzdefinition (2–3 Sätze) · Warum es für Firmenwebsites wichtig ist · Beispiel · Typischer Fehler · Wie SARTU damit umgeht · Verwandte Begriffe (2–4) · Link zur passenden Leistungsseite.
**Umfang:** 250–400 Wörter · **Schema:** `DefinedTerm` (Fallback `Article`) · `BreadcrumbList`

| # | Begriff | URL | Verweist auf |
|---|---|---|---|
| 1 | Firmenwebsite | `/lexikon/firmenwebsite` | `/leistung-webdesign` |
| 2 | Festpreis | `/lexikon/festpreis` | `/preise` |
| 3 | Hosting | `/lexikon/hosting` | `/leistung-wartung` |
| 4 | Domain | `/lexikon/domain` | `/leistung-wartung` |
| 5 | Relaunch | `/lexikon/relaunch` | `/leistung-webdesign` |
| 6 | Barrierefreiheit | `/lexikon/barrierefreiheit` | `/leistung-webdesign` |
| 7 | Local SEO | `/lexikon/local-seo` | `/leistung-seo-lokal` |
| 8 | GEO (KI-Suche) | `/lexikon/geo-ki-suche` | `/leistung-seo-lokal` |

**Auswahlregel:** nur Begriffe, die in einem echten Verkaufsgespräch vorkommen und bei denen ein
Missverständnis Geld kostet. **Nicht** jeder Fachbegriff, den es gibt.

**Stufe 2 nach Search-Console-Daten** (nicht zum Launch): Backup · Canonical · Core Web Vitals ·
DNS · One-Pager · Schema.org · Suchintention · Weiterleitung (301) · SEO · CMS · SSL · Sitemap.


Ausbau auf 40–60 Begriffe erst in Stufe 2, gesteuert über Search-Console-Daten.

---

## 14. Pflicht- und Systemseiten

| Seite | Regel |
|---|---|
| `/impressum` | Vollständig nach § 5 DDG, **keine** Platzhalter live. Daten identisch zu Footer und strukturierten Daten. Bis zur Standortentscheidung nicht öffentlich. |
| `/datenschutz` | Hosting, Serverlogs, Kontaktformular, Bedarfsscheck, Portal-Verweis, KI-Verarbeitung (soweit personenbezogen), Statistik, eingebundene Dienste. **Keine** Aussage „rechtssicher". |
| `/agb` | Nur live und verlinkt, wenn anwaltlich final. Sonst **gar nicht** verlinken und `noindex`. |
| 404 | H1 `Diese Seite gibt es nicht.` · Text: „Vielleicht wurde die Adresse geändert oder eine alte Seite ist umgezogen." · Links: Startseite, Leistungen, Preise, Bedarf prüfen lassen · echter 404-Status · `noindex`. |
| Danke-Seiten | `noindex`, klare nächste Erwartung, keine weiteren Angebote. |

### 14a. Startsperre — die Veröffentlichung muss scheitern, nicht warnen

Ein Platzhalter, der versehentlich live geht, ist bei Impressum und Datenschutz ein **Rechtsverstoß**,
kein Schönheitsfehler. Eine Warnung im Protokoll reicht nicht — sie wird überlesen.

**Der Veröffentlichungsvorgang für Produktion (`APP_ENV=production`) bricht mit Fehler ab, wenn
eine dieser Bedingungen zutrifft:**

1. `/impressum` oder `/datenschutz` enthält die Platzhaltermarkierung `[[PLATZHALTER]]` oder ist kürzer als 500 Zeichen
2. `/agb` existiert als Seite **und** ist irgendwo verlinkt **und** enthält die Platzhaltermarkierung
3. Eine Seite mit `noindex` steht in der `sitemap.xml`
4. Ein Bildplatz für Portal-Screenshots ist noch leer oder trägt die Markierung `[[SCREENSHOT-FEHLT]]`
5. Eine Zeichenkette aus der Verbotsliste §2 kommt im ausgelieferten Text vor
6. Eine Datei außerhalb von `/public` ist über den Webserver erreichbar
7. Ein Ortsname erscheint in Title, H1 oder URL, obwohl `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1 auf `offen` steht

**Fehlermeldung** (Beispiel, muss die Ursache benennen):
```
VEROEFFENTLICHUNG ABGEBROCHEN: /datenschutz enthaelt noch [[PLATZHALTER]].
Rechtstexte muessen final sein, bevor die Seite oeffentlich geht.
```

**Der Staging-Vorgang bricht nicht ab**, sondern listet dieselben Punkte als Warnung auf. So lässt sich
alles vorbereiten und ansehen, ohne dass etwas Unfertiges live gehen kann.

Alle Platzhalter tragen **eine** einheitliche, suchbare Markierung: `[[PLATZHALTER]]` beziehungsweise
`[[SCREENSHOT-FEHLT]]`. Keine freien Formulierungen wie „TODO" oder „Lorem ipsum".

---

## 15. Bild- und Screenshot-Liste (mit Maßen)

**Alle Bilder:** WebP (AVIF optional), `srcset` mit 1×/2×, feste `width`/`height`, echter Alt-Text, sprechender Dateiname. Hero **nicht** lazy (`fetchpriority="high"`), alles darunter `loading="lazy"`.

| Datei | Verwendung | Ausgabemaß (1×) | Seitenverhältnis | Alt-Text |
|---|---|---|---|---|
| `sartu-portal-cockpit-muster.webp` | Startseite Hero | 720 × 540 | 4:3 | Musteransicht des SARTU-Portals mit Projektstatus und nächstem Schritt |
| `sartu-portal-briefing-muster.webp` | Startseite Portal-Sektion, `/leistung-texte` | 960 × 600 | 8:5 | Musteransicht der Briefing-Aufgaben im SARTU-Portal |
| `sartu-portal-angebot-muster.webp` | `/ablauf`, `/preise` | 960 × 600 | 8:5 | Musteransicht eines Angebots mit Umfang, Preis und Zahlungsplan |
| `sartu-portal-domain-muster.webp` | `/ablauf` | 960 × 600 | 8:5 | Musteransicht der Domainvorschläge mit drei geprüften Optionen |
| `sartu-portal-vorschau-muster.webp` | `/ablauf`, `/leistung-portal` | 960 × 600 | 8:5 | Musteransicht von Vorschau und gebündeltem Feedback |
| `sartu-portal-pflege-muster.webp` | `/leistung-wartung` | 960 × 600 | 8:5 | Musteransicht der Öffnungszeiten-Pflege im Portal |
| `sartu-leistungslandkarte.svg` | `/leistungen` | 1200 × 520 | – | Übersicht der SARTU-Leistungen von Strategie bis Betrieb |
| `sartu-portrait.webp` | `/ueber-uns` | 640 × 800 | 4:5 | Nils Haake von SARTU bei der Arbeit |
| `sartu-og-standard.webp` | Open Graph global | 1200 × 630 | 1.91:1 | – |

**Screenshot-Crops:** Portal-Screens werden **ohne** Browser-Chrome aufgenommen, mit 24 px Innenabstand um den Inhalt, im Verhältnis 8:5. Sichtbare Daten sind Musterdaten — keine realistischen Rechnungsnummern, keine echten Personennamen, keine echten Kundenlogos. Badge „Musteransicht" wird **im Layout** gesetzt, nicht ins Bild gebrannt (bleibt so übersetzbar und barrierefrei).

---

## 16. SEO-Übersicht aller Launch-URLs

| URL | Index | Schema | Priorität Sitemap |
|---|---|---|---|
| `/` | index | Organization, WebSite | 1.0 |
| `/leistungen` | index | Service, Breadcrumb | 0.9 |
| `/preise` | index | Service, Breadcrumb | **1.0** |
| `/ablauf` | index | Breadcrumb | 0.8 |
| `/briefing` (Einstiegsseite) | index | Breadcrumb | 0.7 |
| `/briefing/1` … `/briefing/n` (Schritte) | **noindex** | – | – |
| `/leistung-*` (5, s. §11) | index | Service, Breadcrumb | 0.7 |
| `/ratgeber` + 3 Transparenzseiten (§11a) | index | Article, Breadcrumb | **0.9** |
| `/ratgeber` + 2 Vergleichsartikel (§12) | index | Article, Breadcrumb | 0.7 |
| `/ueber-uns` | index | Person oder Organization, Breadcrumb | 0.6 |
| `/kontakt` | index | Breadcrumb | 0.6 |
| `/lexikon` + 8 Begriffe | index | DefinedTerm, Breadcrumb | 0.5 |
| `/impressum`, `/datenschutz` | index | – | 0.3 |
| `/agb` | noindex bis final | – | – |
| Danke-Seiten, 404 | noindex | – | – |

> **Warum `FAQPage` nicht mehr in der Tabelle steht:** Google hat FAQ-Rich-Results im September 2023
> auf staatliche und medizinische Seiten beschränkt und laut Dokumentationsstand vom **15.06.2026**
> ganz eingestellt. Das Markup schadet nicht, bringt aber **keine** Sichtbarkeit mehr. **Die
> FAQ-Blöcke selbst bleiben Pflicht** — als Inhalt für Leser und als zitierfähiger Absatz für
> KI-Antworten, nicht wegen des Schemas. Wer `FAQPage` trotzdem ausliefert, darf es nicht als
> Maßnahme führen.

> **Warum `/preise` und die Transparenzseiten die höchste Priorität haben:** Sie tragen den einzigen
> Sichtbarkeitsvorteil, den SARTU gegenüber etablierten Agenturen hat — veröffentlichte, überprüfbare
> Zahlen in einem Markt, der „Preis auf Anfrage" schreibt (`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §3.4).

**Root-Dateien:** `/sitemap.xml` · `/robots.txt` · `/llms.txt` (ohne Ranking-Behauptung) · `/favicon.ico` + PNG-Größen · OG-Bild.
**Nach Go-live:** Search Console und Bing Webmaster Tools einrichten, Sitemap einreichen.
**`LocalBusiness` wird erst ausgeliefert, wenn der Standort entschieden und real ist.**

---

## 17. Definition of Done

Die Website ist fertig, wenn **alle** Punkte erfüllt sind:

**Inhalt und Aussagen**
- [ ] Keine verbotenen Wörter aus §2 auffindbar (Volltextsuche: „wartungsarm", „rechtssicher", „garantiert", „Paket wählen").
- [ ] Preishinweis „netto zzgl. USt. · ausschließlich für Unternehmer" auf jeder preisführenden Seite.
- [ ] Platzhirsch ist sichtbar die Empfehlung; kein Paket direkt kaufbar; keine Add-on-Liste; keine Änderungsminuten.
- [ ] Keine Fake-Referenzen, -Logos, -Bewertungen, -Adressen; Portal-Screens als „Musteransicht" markiert.
- [ ] Keine Ortsangabe, kein `LocalBusiness`, keine Ortsseite, keine Ortsnamen in Title/H1/URL — solange `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1 auf `offen` steht. Strukturierte Daten nutzen `Organization` **ohne** Adressfeld.

**Technik und SEO**
- [ ] Jede Seite: Status 200, genau eine H1, eigener Title und Description, Canonical auf sich selbst, Breadcrumb.
- [ ] Keine toten internen Links; Sitemap enthält nur 200er-URLs; robots.txt korrekt.
- [ ] Alle Bilder mit Alt-Text, festen Maßen, WebP und `srcset`; Hero nicht lazy.
- [ ] **Laborwerte** im Zielbereich, mobil gemessen: LCP < 2,5 s · INP-Ersatzmessung (Total Blocking Time) < 200 ms · CLS < 0,1. Werkzeug und Version nennen. *Echte Core Web Vitals sind Felddaten und existieren erst nach Wochen im Livebetrieb — sie sind kein Abnahmekriterium, sondern eine Nachmessung (§17a).*
- [ ] Seite ohne JavaScript grundlegend nutzbar; kein Inhalt erscheint erst durch Scroll-Animation.
- [ ] **JS-Budget eingehalten:** ≤ 75 KB gzip Startseite, ≤ 40 KB Unterseiten (gemessen, nicht geschätzt).
- [ ] **`prefers-reduced-motion` getestet:** alle nicht-essenziellen Bewegungen aus, Inhalte sofort sichtbar.
- [ ] Maximal zwei bewusste Markenmomente pro Seite; keine Animation über Text oder CTA.
- [ ] Keine Ortsseite ohne bestandenes Indexierungs-Gate (Masterkonzept §16a).

**Bedienung**
- [ ] Mobil und Desktop geprüft; kein horizontales Scrollen des Seitenkörpers.
- [ ] Tastaturbedienung vollständig, Fokus sichtbar, Skip-Link vorhanden, mobiles Menü mit Fokusfalle und `Esc`.
- [ ] Kontrast mindestens 4,5:1 für Fließtext.
- [ ] Bedarfsscheck: alle Fehlermeldungen erscheinen am Feld, Autosave funktioniert, „Nichts davon"-Regel greift.
- [ ] Beide Formulare senden nachweislich; Bestätigungsseiten sind `noindex`.

**Formulare und Schnittstelle**
- [ ] Bedarfsscheck **vollständig ohne JavaScript** durchlaufbar (§9.5a) — mit abgeschaltetem JS getestet, nicht nur behauptet.
- [ ] Doppelklick, Neuladen und Zurück-Taste erzeugen **keinen** zweiten Datensatz (`submission_id`, §9.5b).
- [ ] Empfehlung und Ampelkennzeichen entstehen **serverseitig**; ein manipuliertes Formularfeld ändert sie nicht.
- [ ] **Nur `/public` ist über den Webserver erreichbar** — `/app`, `/storage`, `.env` liefern 403 oder 404.
- [ ] Honigtopf und Zeitregel greifen; ein gefülltes Honigtopffeld erzeugt trotzdem die normale Danke-Seite.
- [ ] Kein Netzwerkaufruf des Browsers geht an eine **fremde** Domain — im Netzwerkprotokoll geprüft.

**Ortsseiten**
- [ ] **Keine** Ortsseite in der produktiven Veröffentlichung — auch nicht als unverlinkter Entwurf.
- [ ] Falls Prototypen existieren: nur in Staging, mit `noindex`, **nicht** in der Sitemap, **nicht** intern verlinkt, `robots.txt` schließt sie aus.
- [ ] Veröffentlichung erst nach dem Gate in Masterkonzept §16a — die Entscheidung trifft ein Mensch, nicht der Bau.

**Recht**
- [ ] Impressum und Datenschutz final und vollständig (keine Platzhalter).
- [ ] AGB entweder final oder nicht verlinkt und `noindex`.
- [ ] Consent-Banner nur, wenn zustimmungspflichtige Dienste eingebunden sind — sonst keiner.
- [ ] **Startsperre nachgewiesen (§14a):** Die produktive Veröffentlichung bricht bei einem Platzhalter in Impressum oder Datenschutz nachweislich ab — einmal absichtlich provoziert und im Bericht belegt.

---

## 17a. Was erst nach dem Livegang geprüft wird

Diese Punkte gehören **nicht** in die Abnahme, weil sie vor dem Livegang gar nicht messbar sind. Sie
werden 4 und 12 Wochen nach dem Start nachgehalten:

| Nach 4 Wochen | Nach 12 Wochen |
|---|---|
| Echte Core Web Vitals aus Felddaten (Search Console / CrUX), sofern genug Zugriffe | dieselben, belastbarer |
| Indexierungsstand aller Launch-URLs | Suchanfragen, für die die Seite erscheint |
| Fehler in der Search Console | Verhältnis Zugriffe → gestartete Bedarfsschecks → abgeschickte Bedarfsschecks |
| Tatsächliche Spamlast am Formular | Entscheidung, ob Schutzmaßnahmen nachgerüstet werden müssen |

**Wichtig:** Bleiben die Felddaten hinter den Laborwerten zurück, ist das ein Auftrag zur
Nachbesserung — kein Grund, die Abnahme rückwirkend infrage zu stellen.

---

*Ende Lastenheft. Offene Punkte sind ausschließlich die zwei Entscheidungen aus §0.*
