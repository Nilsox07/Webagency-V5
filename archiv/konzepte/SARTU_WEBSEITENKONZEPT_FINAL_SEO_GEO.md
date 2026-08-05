# Sartu - finales Webseitenkonzept mit SEO- und GEO-Architektur

Stand: 22.07.2026

Dieses Dokument beschreibt die oeffentliche Sartu-Marketingwebsite so konkret, dass sie als Umsetzungsgrundlage fuer Design, Text, Struktur, SEO, GEO, Bilder, Portal-Screenshots, Lumi und Conversion dienen kann.

Massgebliche Quellen aus dem Projekt:

- `C:\Users\Nils Haake\Downloads\GESCHAEFTSMODELL.md`
- `C:\Users\Nils Haake\Downloads\SARTU_KONTAKTLOSER_VERTRIEB_LUMI_PORTAL.md`
- `C:\Users\Nils Haake\Downloads\SARTU_ANGEBOT_PORTAL_DETAILKONZEPT.md`
- `C:\Users\Nils Haake\Downloads\SARTU_DESIGNSYSTEM_PORTAL_ARCHITEKTUR.md`
- `C:\Users\Nils Haake\Downloads\sartu-lastenheft-website.md`
- `C:\Users\Nils Haake\Documents\Sartu\SARTU_WEBSITE_KONZEPT.md`
- `C:\Users\Nils Haake\Documents\Sartu\SARTU_ELEMENTPLAN_12VON10.md`

Bei Widerspruechen gilt die juengere Angebotslogik aus `GESCHAEFTSMODELL.md`: Start 1.490 EUR, Wachstum 3.900 EUR, Platzhirsch 7.900 EUR, Sonderprojekt ab 12.500 EUR, Schutz S/M/L mit 59/129/249 EUR monatlich, alle Preise netto zzgl. gesetzlicher Umsatzsteuer, B2B-Fokus, keine Add-on-Liste, keine Aenderungsminuten.

---

## 1. Kurzurteil

Sartu darf nicht wie eine klassische Webdesign-Agentur, ein KI-Webseitenbauer oder ein Baukastenanbieter wirken. Die Website muss ein klares Produkt verkaufen:

> Sartu plant, textet, programmiert und betreibt Firmenwebsites zum Festpreis. Der Kunde liefert Geschaeftsfakten und Freigaben; Sartu entscheidet Struktur, Design, Technik, SEO-/GEO-Basis und Betrieb.

Der eigentliche USP ist die Kombination aus:

- Festpreis statt Angebotsnebel.
- gefuehrtem Portal statt Meeting- und E-Mail-Chaos.
- wenigen Kundenentscheidungen statt Add-ons, SEO-Stufen und Seitenwahl.
- individuell programmierten Kundenseiten ohne WordPress.
- SEO-/GEO-Basis ab Launch.
- KI-gestuetzter Produktion mit menschlicher Pruefung.

Die Startseite darf deshalb nicht zuerst eine Agenturgeschichte erzaehlen. Sie muss in den ersten Sekunden zeigen:

1. Was Sartu baut.
2. Warum der Kunde wenig entscheiden muss.
3. Was es grob kostet.
4. Dass das Portal der besondere Ablauf ist.
5. Dass die Anfrage schnell und unverbindlich ist.

---

## 2. SEO-/GEO-Recherche: was fuer Top-SEO und GEO wirklich zaehlt

### 2.1 Wichtigste Erkenntnis

Google beschreibt Optimierung fuer generative AI-Suche im Kern als Fortsetzung guter SEO. Fuer AI Overviews und AI Mode gelten laut Google weiterhin die grundlegenden SEO-Best-Practices. Es gibt keine magische GEO-Schicht, kein Spezial-Schema und keine Pflicht zu `llms.txt` fuer Google.

Konsequenz fuer Sartu:

- GEO wird nicht als Extra verkauft.
- GEO wird als Antwort-, Fakten-, Entitaets-, Schema- und Inhaltsarchitektur umgesetzt.
- Es wird keine Garantie auf KI-Nennungen, Rankings, Anfragen oder Umsatz gegeben.
- Kein "AEO/GEO-Hack" wird beworben.

### 2.2 Verbindliche SEO-/GEO-Anforderungen

Die Website braucht:

1. **Crawlbare, indexierbare Seiten**
   - wichtige Inhalte als HTML-Text, nicht nur in Bildern oder clientseitig versteckt.
   - sprechende URLs.
   - interne Links als echte Links.
   - keine wichtigen Seiten hinter Login.

2. **People-first Content**
   - keine massenhaft erzeugten Austauschtexte.
   - eigene Sartu-Perspektive: Festpreis, Portal, kein WordPress, KI-Produktion, klare Scope-Grenzen.
   - konkrete Antworten auf Kundenfragen.
   - klare Abgrenzung: was enthalten ist, was nicht, was Sartu entscheidet.

3. **Entitaetsklarheit**
   - Sartu = Webdesign-Agentur fuer kleine und mittlere Unternehmen.
   - Zielgruppe = B2B, Unternehmer, Selbststaendige, lokale Firmen.
   - Angebot = Start, Wachstum, Platzhirsch, Sonderprojekt.
   - Betrieb = Schutz S/M/L.
   - Prozess = Lumi, Portal, Angebot, Zahlung, Briefing, Produktion, Vorschau, Launch, Betrieb.

4. **Suchintention pro Seite**
   - Jede oeffentliche Seite bekommt genau ein Hauptthema.
   - Jede Seite beantwortet oben sofort, worum es geht.
   - Keine Seite existiert nur, um ein Keyword abzudecken.

5. **Technische SEO-Grundlagen**
   - eindeutiger Title.
   - eindeutige Meta Description.
   - genau eine H1.
   - Canonical.
   - steuerbares `index/noindex`.
   - Open Graph.
   - Breadcrumbs.
   - XML-Sitemap.
   - robots.txt.
   - 404-Seite.
   - Redirect-Plan fuer alte URLs.

6. **Strukturierte Daten**
   - `Organization` oder passend `LocalBusiness` fuer Sartu, aber ohne Fake-Standorte.
   - `WebSite`.
   - `BreadcrumbList` auf Unterseiten.
   - `Service` auf Leistungsseiten.
   - `FAQPage` nur, wenn die Fragen sichtbar auf der Seite stehen.
   - `Article` auf Ratgeberartikeln.
   - `DefinedTerm` oder `DefinedTermSet` fuer Lexikonseiten, soweit technisch sauber moeglich.

7. **Local SEO**
   - echte NAP-Daten konsistent.
   - Google Business Profile nur, wenn die Voraussetzungen sauber erfuellt sind.
   - keine erfundenen Standorte.
   - Ortsseiten nur mit eigenem lokalen Nutzen.
   - keine Doorway-Seiten.

8. **Performance und UX**
   - schnelle statische Auslieferung.
   - mobile-first.
   - Core-Web-Vitals-orientiert: schneller Hauptinhalt, wenig Layoutshift, schnelle Interaktion.
   - Bilder in WebP/AVIF, passende Groessen, `width`/`height`, Hero-Bild nicht lazy.

9. **Bild-SEO**
   - reale oder aussagekraeftige Bilder.
   - Alt-Texte mit Bildinhalt, nicht Keyword-Spam.
   - sprechende Dateinamen.
   - responsive Bildquellen.

10. **Messung**
    - Google Search Console.
    - Bing Webmaster Tools.
    - Sitemap einreichen.
    - IndexNow optional fuer neue/geaenderte URLs.
    - Search Console Performance, URL Inspection und generative AI Performance beobachten.

### 2.3 Was ausdruecklich nicht geplant wird

- keine automatisch indexierten Massen-Ortsseiten.
- keine duennen Stadtseiten mit ausgetauschtem Ortsnamen.
- keine Fake-Bewertungen.
- keine Fake-Logos.
- keine Fake-Kunden.
- kein LocalBusiness-Schema fuer Orte ohne echten Standort.
- keine Rankinggarantie.
- keine KI-Nennungsgarantie.
- keine `llms.txt`-Werbeaussage als Google-Rankingfaktor.
- keine Add-on-, SEO-Stufen- oder Minuten-Auswahl auf der Website.

### 2.4 Quellen

Wichtige externe Quellen:

- Google, Optimizing your website for generative AI features on Google Search: https://developers.google.com/search/docs/fundamentals/ai-optimization-guide
- Google, AI features and your website: https://developers.google.com/search/docs/appearance/ai-features
- Google, SEO Starter Guide: https://developers.google.com/search/docs/fundamentals/seo-starter-guide
- Google, Creating helpful, reliable, people-first content: https://developers.google.com/search/docs/fundamentals/creating-helpful-content
- Google, Spam Policies: https://developers.google.com/search/docs/essentials/spam-policies
- Google, LocalBusiness structured data: https://developers.google.com/search/docs/appearance/structured-data/local-business
- Google, Breadcrumb structured data: https://developers.google.com/search/docs/appearance/structured-data/breadcrumb
- Google, Canonical URLs: https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls
- Google, Robots meta tags: https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag
- Google Search Console: https://search.google.com/search-console/about
- Bing Webmaster Guidelines: https://www.bing.com/webmasters/help/webmaster-guidelines-30fba23a
- Bing IndexNow: https://www.bing.com/indexnow/getstarted
- Google Business Profile policies: https://support.google.com/business/answer/7667250
- Google Business Profile eligibility: https://support.google.com/business/answer/13763036
- Google Image SEO: https://developers.google.com/search/docs/appearance/google-images
- web.dev, Web Vitals: https://web.dev/articles/vitals

---

## 3. Positionierung und Tonalitaet

### 3.1 Zielgruppe

Primaer:

- Handwerk.
- lokale Dienstleister.
- Praxen.
- Kanzleien.
- Gastronomie.
- Immobilien.
- Beratungen.
- kleine und mittlere Unternehmen ohne eigene Webabteilung.

Nicht Standardzielgruppe:

- Privatkunden.
- Hobbyprojekte.
- Kunden, die Layouts selbst bauen wollen.
- Kunden, die ein WordPress-CMS erwarten.
- Shops, SaaS, Logins, Mitgliederbereiche oder komplexe Schnittstellen als Standardfall.

### 3.2 Ton

Tonalitaet:

- klar.
- ruhig.
- direkt.
- kompetent.
- nicht marktschreierisch.
- nicht technisch ueberladen.
- nicht zu verspielt.

Ansprache:

- Die neueren Konzeptdateien verwenden eher "Sie".
- Fuer B2B, Preise netto und hoehere Paketpreise ist "Sie" empfehlenswert.
- Wenn die bestehende Marke unbedingt "du" behalten soll, muss das konsequent auf allen Seiten passieren.

Empfehlung:

> Fuer die neue serioese Preis- und Portalpositionierung Sartu auf "Sie" stellen.

### 3.3 Kernsaetze

Hero:

> Individuell programmierte Firmenwebsites zum Festpreis.

Unterzeile:

> Sartu plant, textet, programmiert und betreibt Ihre Website. Sie beantworten nur die Fragen zu Ihrem Unternehmen; Struktur, Design, Technik und SEO-/GEO-Basis uebernehmen wir.

USP kurz:

> Festpreis. Portal. Kein WordPress. SEO-/GEO-Basis ab Start.

Portal:

> Ihr Projekt laeuft nicht ueber endlose E-Mail-Ketten. Im Sartu-Portal sehen Sie Angebot, Zahlungen, offene Aufgaben, Domainstatus, Vorschau, Feedback und spaetere kleine Pflege.

Lumi:

> Beantworten Sie wenige Geschaeftsfragen. Sartu zeigt eine vorlaeufige Empfehlung mit Preis und prueft sie persoenlich, bevor ein Angebot entsteht.

SEO/GEO:

> Ihre Website wird so aufgebaut, dass Menschen, Google und KI-Sucherlebnisse schnell verstehen, wer Sie sind, was Sie anbieten, fuer wen es passt, wo Sie arbeiten und wie der naechste Schritt aussieht.

---

## 4. Website-Ziele

### 4.1 Geschaeftsziele

Die Website soll:

- qualifizierte B2B-Anfragen erzeugen.
- Platzhirsch als Hauptprodukt sichtbar ankern.
- Start und Wachstum als kleinere ehrliche Empfehlungen erhalten.
- keine Sonderwuensche durch Add-on-Listen erzeugen.
- Portal und Festpreis als USP verankern.
- Kompetenz fuer Webdesign, Texte, SEO/GEO, Domain, Launch und Betrieb zeigen.
- Sartu als klare, moderne, aber vertrauenswuerdige Agentur positionieren.

### 4.2 Conversion-Ziele

Primaere Conversion:

- `/briefing` starten.
- CTA-Text: `Bedarf pruefen lassen`.

Sekundaere Conversion:

- `/preise` ansehen.
- CTA-Text: `Preise ansehen`.

Tertiaer:

- Rueckfrage ueber `/kontakt`.
- CTA-Text: `Rueckfrage stellen`.

Nicht anbieten:

- Paket direkt kaufen.
- Add-ons buchen.
- SEO-Paket waehlen.
- Termin als Pflicht buchen.
- kostenlosen Strategiecall als Haupt-CTA.

### 4.3 SEO-/GEO-Ziele

Die Website soll sichtbar werden fuer:

- Webdesign Agentur.
- Website erstellen lassen.
- Firmenwebsite erstellen lassen.
- Webdesign zum Festpreis.
- Webdesign ohne WordPress.
- Website fuer Handwerker.
- Website fuer lokale Unternehmen.
- Website-Texte schreiben lassen.
- lokales SEO fuer Firmen.
- Website Wartung.
- Domain und Website Launch.
- Webdesign Dresden/Sachsen und spaeter weitere echte Zielorte.

---

## 5. Informationsarchitektur

### 5.1 Muss-Seiten zum Start

| URL | Rolle | Haupt-CTA |
|---|---|---|
| `/` | Marke, Angebot, USP, Einstieg | Bedarf pruefen lassen |
| `/leistungen` | Kompetenz zeigen ohne Add-on-Liste | Bedarf pruefen lassen |
| `/preise` | Pakete, Erstjahr, Schutz, Zahlungsplan | Bedarf pruefen lassen |
| `/ablauf` | Portal und Prozess erklaeren | Bedarf pruefen lassen |
| `/briefing` | Lumi-Bedarfsscheck | Empfehlung pruefen lassen |
| `/kontakt` | Rueckfragen, Direktkontakt | Rueckfrage senden |
| `/ueber-uns` | Vertrauen, Haltung, Verantwortlichkeit | Bedarf pruefen lassen |
| `/ratgeber` | Content-Hub | passende Artikel |
| `/lexikon` | Begriffs-Hub fuer GEO/Entity-Aufbau | passende Begriffe |
| `/impressum` | Pflichtseite | keiner |
| `/datenschutz` | Pflichtseite | keiner |
| `/agb` | Pflichtseite oder Platzhalter bis rechtlich final | keiner |

### 5.2 Leistungsseiten zum Start

| URL | Hauptthema |
|---|---|
| `/leistung-webdesign` | individuell programmierte Firmenwebsite |
| `/leistung-texte` | Website-Texte aus Fakten und Stichpunkten |
| `/leistung-seo` | SEO-/GEO-Basis beim Launch |
| `/leistung-lokales-seo` | lokale Sichtbarkeit ohne duenne Ortsseiten |
| `/leistung-wartung` | Rundum-Schutz, Hosting, Backups, Monitoring |
| `/leistung-domain-launch` | Domain, DNS, E-Mail-Schutz und Launch |
| `/leistung-portal` | Sartu-Portal, Freigaben und kleine Pflege |

### 5.3 Kommerzielle Hubs nach Start

Diese Seiten sind SEO-stark und sollten nach den Kernseiten folgen:

| URL | Zweck |
|---|---|
| `/website-erstellen-lassen` | breiter kommerzieller Hub |
| `/firmenwebsite-erstellen-lassen` | B2B-Fokus |
| `/webdesign-agentur` | Agenturvergleich und Positionierung |
| `/website-relaunch` | Relaunch, Weiterleitungen, SEO-Schutz |
| `/webdesign-ohne-wordpress` | Abgrenzung zu WordPress und Baukasten |

### 5.4 Branchen-Hubs

Nur bauen, wenn eigene Texte mit Branchenbezug entstehen:

| URL | Zweck |
|---|---|
| `/webdesign-handwerker` | Handwerk |
| `/webdesign-praxen` | Praxen |
| `/webdesign-kanzleien` | Kanzleien |
| `/webdesign-gastronomie` | Gastronomie |
| `/webdesign-dienstleister` | lokale Dienstleister |

### 5.5 Orts- und Regionsseiten

Startumfang:

- `/webdesign-dresden`
- `/webdesign-sachsen`
- optional `/webdesign-leipzig`
- optional `/webdesign-chemnitz`

Keine Massenproduktion. Jede Ortsseite braucht eigenen lokalen Nutzen und redaktionelle Freigabe.

---

## 6. Globales Layout und Designsystem

### 6.1 Visuelle Richtung

Sartu soll clean und serioes wirken, aber nicht langweilig. Die visuelle Energie entsteht nicht durch bunte Dekoration, sondern durch:

- starke Portal-Screens als Produktbeweis.
- klare Typografie.
- praezise Tabellen und Statusmodule.
- echte UI-Zustaende.
- wenige starke Farbakzente.
- gute Abschnittsdramaturgie.

Nicht verwenden:

- generische KI-Gradienten.
- bunte Orbs.
- Fake-KPI-Dashboards.
- Stockfotos mit Haendeschuetteln.
- Logowolken ohne echte Kunden.
- uebertriebene Animationen.
- Karten in Karten.

### 6.2 Farbkonzept

Empfohlene Rollen:

| Rolle | Farbe | Verwendung |
|---|---|---|
| Ink | `#14181D` | Haupttext, dunkle Flaechen |
| Deep Ink | `#0E1216` | Hero-Hintergrund |
| Paper | `#FFFFFF` | Hauptflaechen |
| Mist | `#F4F7F5` | ruhige Seitenbaender |
| Line | `#D8DFDC` | Linien, Borders |
| Sartu Teal | `#0B7F73` | Marke, aktive Zustaende |
| Signal Green | `#A8E000` | Haupt-CTA, Akzent |
| Signal Blue | `#2F6FED` | Links, Info |
| Oxide | `#B55E2D` | warmer Gegenakzent |
| Amber | `#A8660A` | Hinweis |
| Red | `#B63A3A` | Fehler |

Signal Green darf nicht die ganze Seite dominieren. Es ist ein Conversion- und Wiedererkennungsakzent.

### 6.3 Typografie

Empfehlung:

- Inter oder eine vergleichbare klare Grotesk.
- keine verspielte Display-Schrift.
- H1 gross, aber nicht aufgeblasen.
- Preise mit tabellarischen Ziffern.
- keine negative Laufweite.
- Fliesstext auf Desktop ca. 18 px, mobil ca. 16 px.

### 6.4 UI-Form

- Radius 6 bis 8 px.
- Buttons klar rechteckig, nicht pillenfoermig als Standard.
- Karten nur fuer Pakete, FAQs, wiederholte Inhalte und Portalmodule.
- Seitenabschnitte als volle Baender oder freie Layouts.
- Tabellen und Listen fuer Leistungen, Preise und Prozess.
- Icons aus einer konsistenten Bibliothek wie Lucide.

### 6.5 Globale Navigation

Desktop:

- links: Sartu-Wortmarke.
- Mitte: `Leistungen`, `Preise`, `Ablauf`, `Ratgeber`, `Lexikon`.
- rechts: Sekundaerlink `Kontakt`, Button `Bedarf pruefen lassen`.

Mobile:

- links Wortmarke.
- rechts Menu-Icon.
- im Menu: alle Links plus grosser CTA.

Sticky:

- Header darf beim Scrollen kompakt sticky bleiben.
- Auf Mobil muss der CTA im Menu deutlich bleiben, aber nicht den Inhalt verdecken.

### 6.6 Footer

Footer-Spalten:

1. Marke und Kurzpositionierung.
2. Leistungen.
3. Wissen: Ratgeber, Lexikon, SEO/GEO.
4. Unternehmen: Ablauf, Preise, Kontakt, Ueber uns.
5. Recht: Impressum, Datenschutz, AGB.

Footer darf keine lange Stadtliste enthalten. Ortslinks nur zu echten, freigegebenen Hubseiten.

---

## 7. Startseite `/`

### 7.1 SEO-Ziel

Primaer:

- Webdesign Agentur fuer Firmenwebsites.
- Website erstellen lassen zum Festpreis.
- Webdesign ohne WordPress.

Meta Title:

> Sartu - Firmenwebsite zum Festpreis, ohne WordPress

Meta Description:

> Sartu plant, textet, programmiert und betreibt Firmenwebsites zum Festpreis. Mit gefuehrtem Portal, SEO-/GEO-Basis und klarem Ablauf ohne Pflichttermine.

### 7.2 Desktop-Aufbau

#### Abschnitt 1: Hero

Position:

- direkt unter Header.
- dunkler Hintergrund `Deep Ink`.
- zweispaltig.
- links Text, rechts Portal-Mockup.
- unterer Rand zeigt bereits naechsten hellen Abschnitt.

Linke Spalte:

- Eyebrow: `Webdesign-Agentur fuer Firmenwebsites`
- H1: `Individuell programmierte Firmenwebsites zum Festpreis.`
- Lead:
  > Sartu plant, textet, programmiert und betreibt Ihre Website. Sie beantworten nur die Fragen zu Ihrem Unternehmen; Struktur, Design, Technik und SEO-/GEO-Basis uebernehmen wir.
- Primaerbutton: `Bedarf pruefen lassen`
- Sekundaerbutton: `Preise ansehen`
- Kleiner Hinweis:
  > Alle Preise netto zzgl. gesetzlicher Umsatzsteuer. Ausschliesslich fuer Unternehmer.

Trust-Zeile unter Buttons:

- `Kein WordPress`
- `Texte inklusive`
- `Portal statt E-Mail-Chaos`
- `SEO-/GEO-Basis ab Launch`

Rechte Spalte:

- grosses Portal-Mockup im shadcn/ui-inspirierten Stil.
- nicht bunt, aber nicht leer: konkrete Statusdaten.
- Header im Mockup: `Projektportal`
- Seitenleiste mit Icons:
  - Uebersicht
  - Angebot
  - Briefing
  - Vorschau
  - Rechnungen
  - Sichtbarkeit
- Hauptbereich:
  - Statuskarte: `Naechster Schritt: Domain bestaetigen`
  - Aufgabenliste:
    - `Unternehmensdaten bestaetigt`
    - `Leistungsfakten offen`
    - `Domainvorschlaege bereit`
    - `Vorschau folgt nach Briefingfreigabe`
  - kleine Preisbox: `Platzhirsch - 7.900 EUR netto`
  - Mini-Leiste: `Festpreis`, `40/30/30`, `Schutz L`

Bildtyp:

- kein Foto.
- echte oder nachgebaute Portal-UI als Produktbeweis.
- wenn noch nicht produktionsreif: Beschriftung `Musteransicht`.

Alt-Text:

> Musteransicht des Sartu-Portals mit Projektstatus, Aufgaben, Domain und Zahlung.

Mobile:

- H1 zuerst.
- Buttons direkt sichtbar.
- Portal-Mockup darunter als horizontal nicht scrollende kompakte Karte.
- Trust-Zeile als 2x2 Raster.

#### Abschnitt 2: Problem und Entlastung

Hintergrund:

- hell `Paper`.

Layout:

- links kurze Problemtexte.
- rechts 3 Entscheidungsboxen.

Ueberschrift:

> Eine Website darf nicht Ihr zweiter Job werden.

Text:

> Viele Firmenwebsites scheitern nicht am Design, sondern an offenen Entscheidungen: Welche Seiten? Welche Texte? Welches System? Welche SEO-Einstellungen? Sartu nimmt diese Entscheidungen gebuendelt ab und fragt nur die Fakten ab, die wirklich aus Ihrem Unternehmen kommen muessen.

Boxen:

1. `Sie liefern Fakten`
   - Leistungen, Zielgruppen, Region, Belege, Freigaben.
2. `Sartu entscheidet`
   - Struktur, Design, Technik, SEO-/GEO-Basis, Hosting, Domainprozess.
3. `Das Portal fuehrt`
   - Angebot, Zahlung, Briefing, Dateien, Vorschau, Feedback und kleine Pflege.

CTA:

- Textlink: `So laeuft ein Projekt ab`

#### Abschnitt 3: Platzhirsch als Hauptangebot

Hintergrund:

- `Mist`.

Layout:

- grosse Platzhirsch-Flaeche links oder oben.
- Start und Wachstum kleiner darunter/daneben.
- Sonderprojekt als schmaler Hinweis.

Ueberschrift:

> Drei Website-Ergebnisse. Eine klare Empfehlung.

Einleitung:

> Sie waehlen keine Einzelteile. Der Bedarfsscheck zeigt, welche Loesung voraussichtlich passt; Sartu prueft das Ergebnis persoenlich.

Platzhirsch-Karte:

- Badge: `Empfehlung`
- Titel: `Platzhirsch`
- Preis: `7.900 EUR einmalig`
- Schutz: `+ 249 EUR/Monat Schutz L`
- Erstes Jahr: `10.888 EUR netto`
- Text:
  > Fuer Unternehmen mit mehreren Leistungen, regionalem Wettbewerb oder Recruitingbedarf. Sartu baut daraus ein Vertriebs-, Vertrauens- und Recruiting-System mit bis zu 16 strategischen Seiten.
- Enthaltene Signale:
  - staerkere Leistungs- und Regionsstruktur.
  - Team, Karriere, Projekte oder Referenzen, wenn sinnvoll.
  - ein passendes Conversion-Modul.
  - SEO-/GEO-Basis pro Seite.
- Button: `Bedarf pruefen lassen`

Start und Wachstum:

- kompakter als Platzhirsch.
- Button nicht `auswaehlen`, sondern `Einschaetzen lassen`.
- Start: `1.490 EUR + 59 EUR/Monat`
- Wachstum: `3.900 EUR + 129 EUR/Monat`

Sonderprojekt:

> Shop, Login, komplexe Buchung, Schnittstellen oder mehrere Marken werden als Sonderprojekt ab 12.500 EUR geprueft.

#### Abschnitt 4: Leistungslandkarte

Ziel:

- zeigen, was Sartu kann, ohne Extras zu verkaufen.

Ueberschrift:

> Alles, was eine Firmenwebsite braucht, aber nicht als Add-on-Liste.

Einleitung:

> Diese Leistungen sind Bausteine eines sinnvollen Website-Ergebnisses. Sie muessen sie nicht einzeln buchen; Sartu ordnet sie im Angebot passend ein.

Darstellung:

- 8 breite Service-Zeilen statt Kachelwald.
- jede Zeile mit Icon, Titel, 2 Saetzen, Tags, Link.

Zeilen:

1. `Strategie und Seitenstruktur`
   - Tags: `Sitemap`, `Nutzerfuehrung`, `Suchintention`
2. `Webdesign und Code`
   - Tags: `kein WordPress`, `responsive`, `schnell`
3. `Website-Texte`
   - Tags: `aus Stichpunkten`, `Faktenpruefung`, `SEO-Basis`
4. `SEO-/GEO-Basis`
   - Tags: `Title`, `Schema`, `interne Links`
5. `Lokale Sichtbarkeit`
   - Tags: `Regionen`, `Local SEO`, `keine Doorways`
6. `Domain und Launch`
   - Tags: `DNS`, `E-Mail-Schutz`, `Redirects`
7. `Portal und Freigaben`
   - Tags: `Briefing`, `Feedback`, `kleine Pflege`
8. `Rundum-Schutz`
   - Tags: `Hosting`, `Backups`, `Monitoring`

CTA:

- `Leistungen im Ueberblick`

#### Abschnitt 5: Portal als USP

Hintergrund:

- dunkles Band oder sehr helles App-Band.

Layout:

- links Portal-Screenshot.
- rechts Erklaerung.

Ueberschrift:

> Ihr Websiteprojekt bleibt an einem Ort.

Text:

> Kein loses Formular, keine endlosen E-Mail-Ketten, kein WordPress-Editor. Im Sartu-Portal sehen Sie den naechsten Schritt, bestaetigen Fakten, laden Material hoch, pruefen Vorschauen, geben Feedback und pflegen spaeter kleine Geschaeftsdaten.

Liste `Im Portal`:

- Angebot und Annahme.
- Rechnungen und Zahlungen.
- Domainstatus.
- adaptive Briefingaufgaben.
- Uploads und Bildrechte.
- Vorschau und Feedback.
- Oeffnungszeiten, Kontakt und Seitenstatus nach Launch.

Liste `Nicht im Portal`:

- Layout selbst bauen.
- Plugins installieren.
- SEO-Felder frei verstellen.
- Navigation oder URLs umbauen.
- Code bearbeiten.
- Seiten hart loeschen.

Bild:

- Portal-Screenshot `Briefingaufgaben`.
- Musteransicht erlaubt.

#### Abschnitt 6: SEO/GEO ist eingebaut

Ueberschrift:

> SEO und GEO sind kein spaeteres Extra.

Text:

> Jede Sartu-Website startet mit klaren Seitenthemen, sprechenden URLs, sauberer interner Verlinkung, Metadaten, strukturierten Daten, Performance-Basis und lokalen Unternehmenssignalen. Spaeterer Sichtbarkeitsausbau baut auf echten Suchdaten auf, nicht auf pauschalen SEO-Paketen.

Aufbau:

- 3 Spalten:
  1. `Menschen verstehen`
  2. `Suchmaschinen crawlen`
  3. `KI-Sucherlebnisse einordnen`

Details:

- Menschen: klare Antworten, Preise, Ablauf, Grenzen.
- Suchmaschinen: HTML, Sitemap, Canonical, Schema, Performance.
- KI: konsistente Entitaeten, FAQ, Definitionen, hilfreiche Originalinhalte.

Hinweis:

> Keine Agentur kann Rankings oder KI-Nennungen garantieren. Sartu baut das Fundament und haelt die technische Suchgesundheit im Betrieb im Blick.

CTA:

- `SEO-/GEO-Basis ansehen`

#### Abschnitt 7: Ablauf

Ueberschrift:

> Von wenigen Angaben zur fertigen Website.

Timeline mit 6 Schritten:

1. `Bedarfsscheck`
   - wenige Fragen zu Unternehmen, Ziel, Region, Domain und Sonderfunktionen.
2. `Geprueftes Angebot`
   - Sartu bestaetigt Paket, Scope, Sitemap, Preis und Zahlungsplan.
3. `Portal-Onboarding`
   - bekannte Fakten werden uebernommen, nur Luecken werden geklaert.
4. `KI-gestuetzte Produktion`
   - Codex/Claude erzeugen Code und Texte im kontrollierten Kundenprojekt.
5. `Vorschau und Freigabe`
   - Feedback gebuendelt im Portal.
6. `Launch und Schutz`
   - Domain, Monitoring, Backups, SEO-/GEO-Technikcheck.

CTA:

- `Ablauf im Detail`

#### Abschnitt 8: Lumi-Einstieg

Hintergrund:

- Signal Green sehr sparsam als Akzent.

Ueberschrift:

> Welche Website passt zu Ihrem Unternehmen?

Text:

> Lumi fragt nicht nach Seitenzahlen, Farben oder SEO-Stufen. Sie beantworten wenige Geschaeftsfragen und sehen eine vorlaeufige Empfehlung mit Preis. Danach prueft Sartu persoenlich.

Mini-Fragen sichtbar als Chips:

- Branche.
- Region.
- Ziel.
- Domainstatus.
- Umfangssignale.
- Sonderfunktion.

Button:

- `Bedarf pruefen lassen`

#### Abschnitt 9: FAQ

Pflichtfragen:

1. `Muss ich ein Paket selbst auswaehlen?`
2. `Schreibt Sartu die Texte?`
3. `Warum gibt es keine Add-on-Liste?`
4. `Was passiert mit meiner Domain und E-Mail?`
5. `Kann ich spaeter selbst Inhalte aendern?`
6. `Ist SEO enthalten?`
7. `Warum kein WordPress?`
8. `Gibt es eine Rankinggarantie?`

FAQPage-JSON-LD nur fuer exakt sichtbare Fragen.

#### Abschnitt 10: Abschluss-CTA

Ueberschrift:

> Starten Sie mit wenigen Angaben. Den Rest pruefen wir.

Buttons:

- primaer `Bedarf pruefen lassen`
- sekundaer `Preise ansehen`

Hinweis:

> Unverbindlich bis zum geprueften Angebot. Ausschliesslich fuer Unternehmer. Alle Preise netto zzgl. Umsatzsteuer.

---

## 8. Seite `/leistungen`

### 8.1 Ziel

Die Seite darf nicht nur Pakete zeigen. Sonst weiss der Kunde nicht, ob Sartu Texte, SEO, Domain, Portal, Wartung oder Local SEO ueberhaupt beherrscht.

Sie zeigt alle Faehigkeiten, aber nicht als einzeln buchbare Add-ons.

H1:

> Website, Texte, Sichtbarkeit und Betrieb als ein klares System.

Meta Title:

> Leistungen von Sartu - Webdesign, Texte, SEO und Betrieb

Meta Description:

> Sartu erstellt Firmenwebsites mit Strategie, Texten, Design, Code, SEO-/GEO-Basis, Domain-Launch, Portal und Rundum-Schutz. Ohne Add-on-Liste.

### 8.2 Aufbau

1. **Hero**
   - H1 und Kurzantwort.
   - CTA `Bedarf pruefen lassen`.
   - Sekundaerlink `Preise ansehen`.

2. **Kurz gesagt**
   - Antwortmodul:
     > Sartu baut keine Sammlung einzelner Website-Extras. Jedes Projekt verbindet Strategie, Texte, Design, Programmierung, SEO-/GEO-Basis, Domain/Launch, Portal und Betrieb zu einem klaren Ergebnis.

3. **Leistungslandkarte**
   - 10 Zeilen:
     - Strategie und Sitemap.
     - Webdesign.
     - individuelle Programmierung.
     - Website-Texte.
     - SEO-/GEO-Basis.
     - lokales SEO.
     - Domain, DNS und Launch.
     - Portal.
     - Rundum-Schutz.
     - Relaunch und Weiterleitungen.

4. **Was Sie nicht entscheiden muessen**
   - System.
   - Seitenzahl.
   - Designstil.
   - SEO-Stufe.
   - Hosting.
   - Registrar.
   - Wartungsminuten.

5. **Wie tief es je Paket geht**
   - Tabelle Start/Wachstum/Platzhirsch/Sonderprojekt.
   - Fokus auf Ergebnis, nicht Featurehaken.

6. **Portal-Bruecke**
   - zeigt, wie Leistungen im Prozess zusammengefuehrt werden.

7. **FAQ**
   - `Kann ich einzelne Leistungen dazubuchen?`
   - Antwort: Im Erstangebot nein. Wenn ein Ziel nicht in den Standard passt, gibt es ein Sonderprojekt oder spaeter ein klar begruendetes Folgeangebot.

8. **CTA**
   - `Bedarf pruefen lassen`

### 8.3 Bild

Bildtyp:

- keine Stockfotos.
- breites Diagramm oder Portal-Screenshot mit Seitenstruktur und Aufgaben.

Alt-Text:

> Sartu-Leistungslandkarte mit Strategie, Text, Code, SEO, Portal und Betrieb.

---

## 9. Seite `/preise`

### 9.1 Ziel

Preisangst senken, Platzhirsch ankern und trotzdem klar machen: Der Kunde muss kein Paket selbst waehlen.

H1:

> Klare Website-Pakete. Sartu prueft, was wirklich passt.

Meta Title:

> Sartu Preise - Firmenwebsite ab 1.490 EUR netto

Meta Description:

> Website-Pakete von Sartu: Start ab 1.490 EUR, Wachstum 3.900 EUR, Platzhirsch 7.900 EUR und Sonderprojekte ab 12.500 EUR, jeweils netto zzgl. Umsatzsteuer.

### 9.2 Aufbau

1. **Hero**
   - H1.
   - Lead:
     > Sie muessen kein Paket selbst auswaehlen. Die kurze Bedarfseinschaetzung zeigt, welche Loesung wahrscheinlich passt; Sartu prueft das Ergebnis persoenlich vor dem Angebot.
   - CTA `Bedarf pruefen lassen`.

2. **Preisuebersicht**
   - Platzhirsch als grosse empfohlene Karte.
   - Start und Wachstum als kleinere Karten.
   - Sonderprojekt als eigener Abzweig.

3. **Erstjahreswerte**
   - Tabelle:
     - Start: 1.490 EUR + 12 x 59 EUR = 2.198 EUR netto.
     - Wachstum: 3.900 EUR + 12 x 129 EUR = 5.448 EUR netto.
     - Platzhirsch: 7.900 EUR + 12 x 249 EUR = 10.888 EUR netto.
     - Sonderprojekt: ab 12.500 EUR + mindestens 12 x 249 EUR = ab 15.488 EUR netto.

4. **Was jedes Projekt enthaelt**
   - Strategie.
   - Text.
   - Design.
   - Code.
   - SEO-/GEO-Basis.
   - Portal.
   - Domainverbindung.
   - Launch.

5. **Rundum-Schutz**
   - Schutz S/M/L als Betrieb, nicht als Minutenkontingent.
   - Keine Aenderungsminuten.
   - Selbst pflegbar: Oeffnungszeiten, Kontakt, Links, Seitenstatus, vorhandene Datensaetze.

6. **Domain und E-Mail**
   - Kunde bleibt Domaininhaber.
   - Sartu prueft und verwaltet technisch.
   - normale Domain bis 30 EUR netto/Jahr im Schutz enthalten, wenn ueber Sartu verwaltet.
   - bestehende E-Mail wird vor DNS-Aenderungen geschuetzt.

7. **Zahlungsplan**
   - Start/Wachstum: 50/50.
   - Platzhirsch: 40/30/30.
   - Zahlungsziel: 10 Kalendertage.
   - Zahlung im Portal ueber Mollie.
   - Produktionsslot nach erster Zahlung.

8. **FAQ**
   - `Sind die Preise brutto oder netto?`
   - `Kann ich Start buchen und spaeter alles erweitern?`
   - `Warum ist Schutz verpflichtend?`
   - `Gibt es versteckte Zusatzkosten?`
   - `Was ist mit Domainkosten?`
   - `Was passiert, wenn ich Sonderfunktionen brauche?`

### 9.3 Bild

Keine grossen Fotos. Preise brauchen Scanbarkeit.

Erlaubt:

- kleine Portal-Zahlungsansicht als Muster.
- keine Fake-Rechnungsnummern mit echten Daten.

---

## 10. Seite `/ablauf`

### 10.1 Ziel

Der Ablauf ist ein Haupt-USP. Diese Seite muss erklaeren, warum Sartu weniger Abstimmung braucht, ohne unserioese Stundenersparnis zu versprechen.

H1:

> Ein Websiteprojekt ohne Pflichttermin-Marathon.

Meta Title:

> Sartu Ablauf - vom Bedarfsscheck zur Website im Portal

Meta Description:

> So laeuft ein Sartu-Projekt: kurzer Bedarfsscheck, geprueftes Angebot, Portal-Onboarding, KI-gestuetzte Produktion, Vorschau, Launch und Rundum-Schutz.

### 10.2 Aufbau

1. **Hero**
   - H1.
   - Lead:
     > Standardprojekte laufen bei Sartu digital und gebuendelt. Gespraeche bleiben moeglich, aber Angebot, Briefing, Zahlungen, Domain, Vorschau, Feedback und Freigaben werden im Portal gefuehrt.

2. **Vergleich Klassische Agentur vs. Sartu**
   - Tabelle:
     - Erstgespraech vs. Bedarfsscheck.
     - Kickoff vs. adaptives Onboarding.
     - E-Mail-Feedback vs. gebuendeltes Portalfeedback.
     - offene Entscheidungen vs. Sartu-Entscheidungen.

3. **Schrittfolge**
   - 8 Schritte:
     1. Bedarfsscheck mit Lumi.
     2. Sartu-Pruefung.
     3. Angebot im Portal.
     4. Annahme und erste Zahlung.
     5. Domain und Onboarding.
     6. Produktion mit Codex/Claude und QA.
     7. Vorschau, Feedback, Abnahme.
     8. Launch und Betrieb.

4. **Portal-Screenshots**
   - 5 Screens:
     - Angebot.
     - Briefing.
     - Domain.
     - Vorschaufeedback.
     - kleine Pflege nach Launch.

5. **Was der Kunde wirklich tun muss**
   - Fakten bestaetigen.
   - Material hochladen.
   - Domain bestaetigen.
   - Vorschau pruefen.
   - Freigaben erteilen.

6. **Was Sartu entscheidet**
   - Paketempfehlung.
   - Sitemap.
   - UX.
   - Designsystem.
   - SEO-/GEO-Basis.
   - Technik.
   - Hosting.
   - DNS-Plan.

7. **CTA**
   - `Bedarf pruefen lassen`

### 10.3 Bild

Hero:

- Portal-Prozessansicht mit Timeline.

Alt-Text:

> Sartu-Projektablauf im Portal mit Angebot, Briefing, Vorschau und Launch.

---

## 11. Seite `/briefing` mit Lumi

### 11.1 Ziel

Lumi ist kein Konfigurator und keine Paketwahl. Lumi sammelt nur, was Machbarkeit, Paket oder Preis beeinflusst.

H1:

> Welche Website passt wirklich zu Ihrem Unternehmen?

Meta Title:

> Bedarf pruefen lassen - Sartu Website-Empfehlung

Meta Description:

> Beantworten Sie wenige Fragen zu Unternehmen, Ziel, Region, Domain und Sonderfunktionen. Sartu zeigt eine vorlaeufige Website-Empfehlung mit Preis.

### 11.2 Einstieg

Text:

> Sie muessen weder Paket, Seitenzahl, Designrichtung noch SEO-Stufe kennen. Beantworten Sie wenige Geschaeftsfragen. Danach sehen Sie eine vorlaeufige Empfehlung mit Preis; Sartu prueft sie persoenlich.

Trust-Hinweise:

- dauert meist etwa 3 Minuten.
- Preis vor Kontaktdaten.
- kein Pflichttermin.
- keine Add-on-Auswahl.
- unverbindlich bis zum geprueften Angebot.

Button:

- `Bedarf pruefen lassen`

### 11.3 Fragen vor Kontaktdaten

Wenn der Nutzer von einer Paketkarte kommt, darf das Paket intern als Kontext gespeichert werden, aber Lumi fragt trotzdem nicht: "Welches Paket wollen Sie kaufen?"

Thema 1: Unternehmen

1. `Was bietet Ihr Unternehmen an?`
   - Freitext, 1 bis 3 Saetze.
   - Hilfetext: `Zum Beispiel: Wir sanieren Baeder und Heizungen fuer Privatkunden im Raum Leipzig.`
2. `Wo ist Ihr Unternehmen hauptsaechlich taetig?`
   - Ort oder PLZ.
   - optional Einzugsgebiet.
3. `Gibt es bereits eine Website?`
   - Ja, URL.
   - Nein.
   - Unsicher.

Thema 2: Ziel

4. `Was ist aktuell das wichtigste Ziel der neuen Website?`
   - Mehr passende Anfragen.
   - Besser bei Google und in der Region gefunden werden.
   - Neue Mitarbeitende gewinnen.
   - Vertrauen und Professionalitaet staerken.
   - Termine oder Bewerbungen einfacher machen.
   - Etwas anderes.

5. `Wen moechten Sie vor allem erreichen?`
   - Privatkunden.
   - Unternehmen.
   - Bewerberinnen und Bewerber.
   - mehrere Gruppen.
   - noch unklar.

Thema 3: Umfangssignale

6. `Welche Aussagen passen zu Ihrem Unternehmen?`
   - ein klares Hauptangebot.
   - mehrere eigenstaendige Leistungen.
   - mehrere Regionen oder Standorte.
   - regelmaessig offene Stellen.
   - Projekte, Referenzen oder Neuigkeiten sollen sichtbar bleiben.
   - nichts davon / unsicher.

Thema 4: Sonderrisiken

7. `Muss die Website etwas Besonderes koennen?`
   - normale Anfrage oder Bewerbung.
   - einfache Terminbuchung.
   - Shop, Zahlung oder bezahlte Buchung.
   - Kundenlogin oder geschuetzter Bereich.
   - Schnittstelle zu anderer Software.
   - mehrere Sprachen oder getrennte Marken.
   - besondere Daten oder formaler Nachweis.
   - nichts davon / normale Firmenwebsite.

Thema 5: Domain und Termin

8. `Wie ist der Domainstatus?`
   - Domain vorhanden.
   - neue Domain benoetigt.
   - unsicher.

9. `Gibt es einen festen Termin?`
   - normaler Zeitrahmen passt.
   - ja, Datum und Grund.

10. `Gibt es etwas, das auf keinen Fall uebersehen werden darf?`
   - optionaler Freitext.

### 11.4 Ergebnis vor Kontaktdaten

Lumi zeigt:

- vorlaeufige Empfehlung.
- kurze Begruendung.
- Einmalpreis netto.
- Schutzpreis netto.
- Erstjahreswert netto.
- Hinweis auf persoenliche Pruefung.

Keine Paketwechsel-Buttons.
Keine Add-ons.
Keine SEO-Auswahl.

Button:

- `Empfehlung unverbindlich pruefen lassen`

### 11.5 Kontaktdaten

Felder:

- Vor- und Nachname.
- Unternehmen.
- geschaeftliche E-Mail.
- Telefon optional.
- bevorzugter Kontakt: E-Mail oder Portal.
- B2B-Bestaetigung.
- Datenschutz.

Keine Pflichttelefonnummer.
Kein Newsletter-Haken im Hauptweg.

---

## 12. Seite `/leistung-webdesign`

H1:

> Webdesign fuer Firmenwebsites, die nicht wie Baukasten aussehen.

Kurz gesagt:

> Sartu erstellt individuell programmierte Firmenwebsites ab 1.490 EUR netto. Struktur, Design, Texte, Code und SEO-/GEO-Basis werden als ein Ergebnis geplant, nicht als Sammlung einzelner Extras.

Aufbau:

1. Hero mit H1, Lead, Chips: `Festpreis`, `kein WordPress`, `Texte inklusive`, `Portal`.
2. Was Sie bekommen.
3. Wie Sartu Struktur und Nutzerfuehrung entscheidet.
4. Warum kein WordPress.
5. Paket-Bruecke.
6. FAQ.
7. CTA.

Bild:

- Muster-Kundenseite oder Designsystem-Komposition.
- kein generisches Laptopfoto.

---

## 13. Seite `/leistung-texte`

H1:

> Website-Texte aus Stichpunkten, Fakten und echten Belegen.

Kurz gesagt:

> Sartu schreibt Website-Texte aus bestaetigten Unternehmensfakten, vorhandenen Unterlagen, Altmaterial und Stichpunkten. Sie muessen keinen fertigen Webtext liefern.

Pflichtaussagen:

- Sartu erfindet keine Belege.
- fachliche Aussagen werden vom Kunden freigegeben.
- Rechtstexte sind nicht enthalten.
- SEO/GEO heisst klare Suchintention, nicht Keyword-Spam.

Bild:

- Portal-Aufgabe `Leistungsfakten bestaetigen`.

---

## 14. Seite `/leistung-seo`

H1:

> SEO-/GEO-Basis fuer Firmenwebsites, die gefunden und verstanden werden.

Kurz gesagt:

> Jede Sartu-Website startet mit einem SEO-/GEO-Fundament: klare Seitenthemen, HTML-Inhalte, Metadaten, interne Links, strukturierte Daten, Sitemap, Performance-Basis und konsistente Unternehmensfakten.

Abschnitte:

1. Was beim Launch enthalten ist.
2. Was GEO praktisch bedeutet.
3. Warum es keine SEO-Stufe im Anfrageformular gibt.
4. Warum spaeterer Ausbau datenbasiert ist.
5. Was nicht versprochen wird.
6. FAQ.

Pflichttext:

> Sartu garantiert keine Rankings, Anfragen, Umsaetze oder KI-Nennungen.

Bild:

- SEO/GEO-Pruefpanel aus Admin: Crawlbarkeit, Sitemap, Canonical, Schema, Performance.

---

## 15. Seite `/leistung-lokales-seo`

H1:

> Lokale Sichtbarkeit ohne duenne Ortsseiten.

Kurz gesagt:

> Sartu baut lokale Sichtbarkeit ueber echte Unternehmensfakten, klare Leistungsseiten, konsistente NAP-Daten, sinnvolle Regionen und nur solche Ortsseiten, die Besuchern wirklich helfen.

Abschnitte:

1. Local SEO fuer Firmenwebsites.
2. Google-Unternehmensprofil und Website-Signale.
3. Ortsseiten-Gate.
4. Was verboten ist.
5. Beispiel: gute vs. schlechte Ortsseite.
6. FAQ.

Pflichttext:

> Sartu erstellt keine Ortsseiten nur mit ausgetauschtem Stadtnamen.

Bild:

- regionale Hub-Struktur als Diagramm.

---

## 16. Seite `/leistung-wartung`

H1:

> Rundum-Schutz fuer Ihre Website, ohne WordPress-Wartungsstress.

Kurz gesagt:

> Der Sartu-Schutz verbindet Managed Hosting, SSL, Backups, Monitoring, technische Pflege, Portalzugang und Suchgesundheit. Er ist dem Websitepaket fest zugeordnet.

Abschnitte:

1. Warum Betrieb zur Website gehoert.
2. Schutz S/M/L.
3. Keine Aenderungsminuten.
4. Was Kunden selbst pflegen koennen.
5. Was nicht selbst pflegbar ist.
6. FAQ.

Pflichttext:

> Der Schutz bezahlt Betrieb, Sicherheit, Portal und technische Pflege. Er ist keine unbegrenzte Content- oder Design-Flatrate.

Bild:

- Portal-Screen `Oeffnungszeiten bearbeiten` oder `Seitenstatus`.

---

## 17. Seite `/leistung-domain-launch`

H1:

> Domain, E-Mail und Launch ohne Technikstress.

Kurz gesagt:

> Der Kunde entscheidet den Domainnamen und bleibt Domaininhaber. Sartu prueft Verfuegbarkeit, DNS, E-Mail-Eintraege, Redirects und Launch technisch.

Abschnitte:

1. Neue Domain.
2. Vorhandene Domain.
3. E-Mail-Schutz.
4. DNS-Snapshot.
5. Launchcheck.
6. FAQ.

Pflichttext:

> Bestehende E-Mail darf durch den Website-Launch nicht unterbrochen werden.

Bild:

- Portal-Screen `Domainvorschlaege` mit maximal drei Vorschlaegen.

---

## 18. Seite `/leistung-portal`

H1:

> Ein Projektportal fuer Freigaben und kleine Pflege, kein Website-Baukasten.

Kurz gesagt:

> Im Sartu-Portal laufen Angebot, Zahlung, Briefing, Dateien, Domain, Vorschau, Feedback, Abnahme, Anfragen, Rechnungen und spaetere kleine Pflege an einem Ort.

Abschnitte:

1. Warum Portal statt E-Mail-Kette.
2. Vor Auftrag.
3. Im Projekt.
4. Nach Launch.
5. Was der Kunde bearbeiten kann.
6. Was der Kunde nicht bearbeiten kann.
7. FAQ.

Bild:

- 3 bis 5 Portal-Screens als Galerie.
- alle Screens mit Badge `Musteransicht`, solange nicht produktionsreif.

---

## 18.1 Seite `/ueber-uns`

### Ziel

Diese Seite soll Vertrauen schaffen, ohne Sartu groesser oder lauter darzustellen als es ist. Sie erklaert Haltung, Arbeitsweise und Verantwortung.

H1:

> Webdesign mit klaren Grenzen, festen Preisen und Verantwortung.

Meta Title:

> Ueber Sartu - Webdesign-Agentur mit Festpreis und Portal

Meta Description:

> Sartu erstellt Firmenwebsites zum Festpreis: klarer Ablauf, gefuehrtes Portal, keine WordPress-Pflege und KI-gestuetzte Produktion mit menschlicher Pruefung.

### Aufbau

1. **Hero**
   - links H1 und Lead.
   - rechts echtes Foto von Nils oder ruhiges Arbeitsbild.
   - kein Fake-Teamfoto.

Lead:

> Sartu wurde gebaut, damit kleine und mittlere Unternehmen eine starke Website bekommen, ohne sich durch Technik, Seitenzahlen, Agentursprech oder WordPress-Pflege kaempfen zu muessen.

2. **Haltung**
   - Ueberschrift: `Warum Sartu anders arbeitet`
   - 4 Punkte:
     - Festpreis statt Stundenfalle.
     - Portal statt E-Mail-Chaos.
     - Fakten statt Geschmacksdiskussionen.
     - KI als Produktionshilfe, nicht als unbeaufsichtigter Ersatz.

3. **Was Sartu bewusst nicht ist**
   - kein Baukasten.
   - kein WordPress-Hoster.
   - keine Billig-KI-Seitenschleuder.
   - kein Anbieter fuer Privat- und Hobbyseiten.

4. **Arbeitsweise**
   - kurzer Ablauf mit 5 Schritten:
     - verstehen.
     - empfehlen.
     - strukturieren.
     - produzieren.
     - betreiben.

5. **Verantwortung**
   - Text:
     > KI kann bei Struktur, Text und Code helfen. Veroeffentlicht wird nur, was Sartu prueft, versioniert und freigibt.

6. **CTA**
   - `Bedarf pruefen lassen`
   - Sekundaer: `Ablauf ansehen`

### Bild

Pflicht:

- echtes Portrait oder echtes Arbeitsbild vor finalem Go-live.
- Wenn noch nicht vorhanden: Platzhalter nicht als echtes Foto tarnen.

Alt-Text:

> Nils Haake von Sartu bei der Arbeit am Website- und Portalprozess.

---

## 18.2 Seite `/kontakt`

### Ziel

Kontakt ist fuer Rueckfragen da, nicht als Hauptweg fuer die Website-Anfrage. Die Seite soll Sicherheit geben, aber den Nutzer klar zu Lumi fuehren, wenn er eine Website einschaetzen lassen will.

H1:

> Kontakt zu Sartu.

Meta Title:

> Kontakt zu Sartu - Rueckfrage stellen oder Websitebedarf pruefen

Meta Description:

> Stellen Sie Sartu eine Rueckfrage oder starten Sie den kurzen Bedarfsscheck fuer Ihre Firmenwebsite. Standardprojekte beginnen mit wenigen Angaben im Portal.

### Aufbau

1. **Hero**
   - H1.
   - Lead:
     > Wenn Sie schon wissen, dass Sie eine neue Firmenwebsite brauchen, starten Sie am besten direkt den Bedarfsscheck. Fuer alles andere koennen Sie hier eine Rueckfrage senden.
   - Primaerbutton: `Bedarf pruefen lassen`
   - Sekundaeranker: `Rueckfrage senden`

2. **Kontaktoptionen**
   - Karte 1: `Websitebedarf pruefen`
     - Text: `Wenige Angaben, vorlaeufige Empfehlung mit Preis, persoenliche Pruefung.`
     - Button: `Bedarf pruefen lassen`
   - Karte 2: `Rueckfrage stellen`
     - Text: `Wenn Sie vorher etwas klaeren moechten.`
     - Button/Anker zum Formular.

3. **Formular**
   - Felder:
     - Name.
     - Unternehmen.
     - E-Mail.
     - Telefon optional.
     - Anliegen als Auswahl:
       - Websiteprojekt.
       - bestehendes Angebot.
       - Domain/Launch.
       - allgemeine Rueckfrage.
     - Nachricht.
     - Datenschutz-Checkbox.
   - Kein Dateiupload im einfachen Kontaktformular.
   - Kein Pflichttelefon.
   - Honeypot und serverseitige Validierung.

4. **Kontaktdaten**
   - zentrale Sartu-Daten aus finalem Impressum.
   - keine Fake-Adresse.
   - wenn keine oeffentliche Besuchsadresse: klar als Kontakt-/Unternehmensanschrift darstellen, keine Laufkundschaft suggerieren.

5. **FAQ kurz**
   - `Muss ich vorher telefonieren?`
   - `Kann ich direkt ein Angebot bekommen?`
   - `Ist die Anfrage verbindlich?`

### Bild

Kein grosses dekoratives Foto notwendig. Besser:

- kleines Portal-/Nachrichtenmodul.
- oder ruhige Kontaktflaeche ohne Bild.

---

## 18.3 Pflichtseiten und Systemseiten

### Impressum `/impressum`

Inhalt:

- rechtlicher Name.
- Anschrift.
- Kontakt.
- USt-ID, falls vorhanden.
- Verantwortlicher.
- weitere Pflichtangaben nach finaler Rechtspruefung.

Regel:

- keine Platzhalter im Livebetrieb.
- Daten muessen mit Footer und strukturierten Daten konsistent sein.

### Datenschutz `/datenschutz`

Inhalt:

- Hosting.
- Serverlogs.
- Kontaktformular.
- Lumi-Anfrage.
- Portal-Verweis, soweit relevant.
- Mollie nur, wenn Zahlungsprozess auf oeffentlicher Website wirklich beruehrt wird, sonst im Portal/Vertrag.
- KI-Verarbeitung transparent, soweit personenbezogene Daten betroffen sind.
- Analytics/Statistik.
- externe Dienste nur, wenn wirklich eingebunden.

Regel:

- Consent-Banner nur, wenn zustimmungspflichtige Dienste geladen werden.
- keine pauschale Behauptung `rechtssicher`.

### AGB `/agb`

Solange nicht anwaltlich final:

- nicht als fertiger Rechtstext ausgeben.
- entweder nicht verlinken oder klar als Platzhalter im nicht indexierten Entwicklungsstand halten.

Vor Go-live:

- Angebot, Abnahme, Mitwirkung, Zahlung, Schutzbetrieb, Domain, Export, KI-Verarbeitung und Datenschutz anwaltlich abgleichen.

### 404-Seite

Aufbau:

- H1: `Diese Seite gibt es nicht mehr.`
- Text:
  > Vielleicht wurde die Adresse geaendert oder eine alte Seite umgezogen.
- Links:
  - Startseite.
  - Leistungen.
  - Preise.
  - Bedarf pruefen lassen.

SEO:

- echter 404-Status.
- keine Indexierung.

### Danke-/Bestaetigungsseiten

Fuer Kontakt und Lumi:

- `noindex`.
- klare naechste Erwartung.
- keine neuen Angebote.
- Hinweis: `Sartu prueft Ihre Anfrage und meldet sich schriftlich.`

---

## 19. Ratgeber

### 19.1 Rolle

Ratgeber holen Informationssuchen ab und fuehren in kommerzielle Seiten. Sie sind keine Textsammlung.

Jeder Artikel braucht:

- H1 mit Suchintention.
- kurze Antwort sofort oben.
- Update-Datum.
- Autor/Pruefhinweis.
- Beispiele.
- Tabellen oder Entscheidungslogik.
- interne Links.
- CTA zu Lumi oder passender Leistung.
- `Article`-Schema.

### 19.2 Startartikel

Prioritaet:

1. `/ratgeber/website-kosten`
   - H1: `Was kostet eine Firmenwebsite?`
   - Fuehrt zu `/preise`.
2. `/ratgeber/website-erstellen-lassen-ablauf`
   - Fuehrt zu `/ablauf`.
3. `/ratgeber/one-pager-oder-mehrseitige-website`
   - Fuehrt zu `/preise` und `/briefing`.
4. `/ratgeber/website-ohne-wordpress`
   - Fuehrt zu `/leistung-webdesign`.
5. `/ratgeber/lokales-seo-fuer-unternehmen`
   - Fuehrt zu `/leistung-lokales-seo`.
6. `/ratgeber/domain-wechsel-ohne-email-ausfall`
   - Fuehrt zu `/leistung-domain-launch`.

### 19.3 Nicht tun

- keine taeglichen KI-Artikel ohne eigene Perspektive.
- keine Themen ausserhalb der Zielgruppe.
- keine erfundenen Statistiken.
- keine beliebige Wortzahl erzwingen.

---

## 20. Lexikon fuer GEO und Entity-Aufbau

### 20.1 Entscheidung

Ein Lexikon ist fuer Sartu sinnvoll, aber nur kuratiert. Es ist gut fuer GEO, weil es Begriffe, Entitaeten und Zusammenhaenge klar erklaert. Es darf aber nicht zu einem duennen SEO-Begriffsfriedhof werden.

Startumfang:

- 40 bis 60 Begriffe.
- nicht 300 Begriffe auf einmal.

### 20.2 Lexikon-Hub `/lexikon`

Aufbau:

1. Hero:
   - H1: `Website-Lexikon fuer Firmenwebsites, SEO und Betrieb.`
   - Lead:
     > Kurze Erklaerungen zu Begriffen, die bei Websiteprojekten, SEO, Domain, Betrieb und dem Sartu-Portal wichtig sind.
2. Suchfeld.
3. Alphabetische Navigation.
4. Kategorien:
   - Website und Struktur.
   - SEO und GEO.
   - Technik und Performance.
   - Domain und E-Mail.
   - Portal und Projekt.
   - Betrieb und Sicherheit.
5. Begriffsliste.
6. CTA zu `/briefing`.

### 20.3 Begriffseite

URL:

- `/lexikon/core-web-vitals`
- `/lexikon/canonical`
- `/lexikon/local-seo`

Aufbau:

1. H1: Begriff.
2. Kurzdefinition in 2 bis 3 Saetzen.
3. Warum wichtig fuer Firmenwebsites?
4. Beispiel aus Sartu-Sicht.
5. Typischer Fehler.
6. Wie Sartu damit umgeht.
7. Verwandte Begriffe.
8. Link zur passenden Leistungsseite.

### 20.4 Startbegriffe

Beispiele:

- Firmenwebsite.
- One-Pager.
- Landingpage.
- Relaunch.
- SEO.
- GEO.
- Local SEO.
- Suchintention.
- Title Tag.
- Meta Description.
- Canonical.
- Sitemap.
- robots.txt.
- noindex.
- 301-Weiterleitung.
- Core Web Vitals.
- LCP.
- INP.
- CLS.
- Lazy Loading.
- Bildkomprimierung.
- Schema.org.
- LocalBusiness.
- FAQPage.
- Breadcrumb.
- Domain.
- DNS.
- Registrar.
- MX.
- SPF.
- DKIM.
- DMARC.
- Hosting.
- SSL.
- Backup.
- Monitoring.
- WordPress.
- CMS.
- statische Website.
- Designsystem.
- Briefing.
- Abnahme.
- Korrekturrunde.
- Festpreis.
- Scope.

---

## 21. Ortsseiten und regionale SEO

### 21.1 Grundsatz

Ortsseiten sind erlaubt, aber nur mit echtem lokalen Nutzen.

Nicht erlaubt:

- Ortsname austauschen und sonst gleichen Text verwenden.
- Fake-Adresse.
- Fake-Telefonnummer.
- Fake-Bewertung.
- Fake-Case-Study.
- hunderte Seiten gleichzeitig indexieren.
- `beste Agentur in X` ohne Beleg.

### 21.2 Publikationsgate

Eine Ortsseite darf erst auf `index`, wenn:

- Sartu die Region realistisch bedient.
- der Ort kommerziell relevant ist.
- die Suchintention klar ist.
- mindestens 5 Abschnitte ortsspezifisch sind.
- keine Duplicate Titles/Descriptions entstehen.
- der Text echten Nutzen hat.
- interne Links sinnvoll sind.
- Schema keine falsche Niederlassung behauptet.
- redaktionelle Freigabe erfolgt ist.

### 21.3 Template `/webdesign-{ort}`

Abschnitte:

1. Hero:
   - H1: `Webdesign fuer Unternehmen in {Ort}`
   - Lead: `Sartu erstellt individuell programmierte Firmenwebsites fuer Unternehmen in {Ort} und Umgebung.`
   - CTA: `Projekt in {Ort} einschaetzen lassen`
2. Kurz gesagt.
3. Fuer welche Unternehmen in {Ort} Sartu passt.
4. Warum Festpreis und Portal fuer lokale Betriebe helfen.
5. Leistungen und Pakete.
6. Lokale SEO-/GEO-Basis.
7. Ablauf remote und digital.
8. FAQ mit lokalen Fragen.
9. CTA.

### 21.4 Interne Verlinkung

- Startseite linkt maximal auf 3 bis 5 wichtigste Orte/Regionen.
- Ortsseiten linken auf Region, Leistungen, Preise, Ablauf und nahe Orte.
- Footer enthaelt keine riesige Ortsliste.

---

## 22. Bild- und Medienkonzept

### 22.1 Grundsatz

Bilder muessen Sartu glaubwuerdiger machen. Keine Bilder nur zur Dekoration.

Prioritaet:

1. echte Portal-Screens.
2. echte Muster-Kundenseiten.
3. echte Fotos von Nils/Arbeitsplatz, wenn vorhanden.
4. neutrale lizenzierte Fotos nur, wenn sie konkret helfen.
5. KI-Bilder nur fuer neutrale abstrakte Motive, nie als Kundenbeweis.

### 22.2 Benotigte Bildmotive

1. Hero:
   - Portal-Cockpit.
   - Bildname: `sartu-portal-cockpit-muster.webp`
   - Alt: `Musteransicht des Sartu-Portals mit Projektstatus und naechstem Schritt.`

2. Portal-Sektion:
   - `sartu-portal-briefing-muster.webp`
   - `sartu-portal-domain-muster.webp`
   - `sartu-portal-vorschau-feedback-muster.webp`
   - `sartu-portal-zahlung-muster.webp`
   - `sartu-portal-pflege-muster.webp`

3. Leistungen:
   - Systemdiagramm oder UI-Modul.
   - kein Stockfoto.

4. Ablauf:
   - Portal-Timeline.

5. Ueber uns:
   - echtes Foto von Nils.
   - wenn nicht vorhanden: Platzhalterflaeche mit Hinweis `Foto folgt`, aber nicht fuer finalen Go-live.

6. Ratgeber:
   - einfache Diagramme, Tabellen, Checklisten.
   - keine generischen Blogbilder.

### 22.3 Bildregeln

- WebP/AVIF.
- responsive Quellen.
- feste Breite/Hoehe.
- Hero-Bild nicht lazy.
- Bilder unterhalb des ersten Viewports lazy.
- keine Textinformationen nur im Bild.
- jedes Bild mit echtem Alt-Text.

---

## 23. Interne Verlinkung

### 23.1 Regeln

- Jede kommerzielle Seite linkt zu `/briefing` und `/preise`.
- Jede Leistung linkt zu passenden Ratgeber- und Lexikonseiten.
- Ratgeber linkt immer auf mindestens eine kommerzielle Seite.
- Lexikon verlinkt auf Leistungen, Ratgeber und verwandte Begriffe.
- Preise verlinkt auf Domain, Wartung, Portal, SEO und Ablauf.
- Ablauf verlinkt auf Portal, Lumi, Domain und Preise.

### 23.2 Gute Anchors

Gut:

- `Website-Pakete ansehen`
- `Ablauf einer Sartu-Website`
- `SEO-/GEO-Basis beim Launch`
- `Domain und Launch ohne E-Mail-Ausfall`
- `Webdesign fuer Handwerksbetriebe`

Schlecht:

- `hier`
- `mehr`
- `klicken`
- Keywordketten.

---

## 24. Schema- und Metadatenplan

### 24.1 Global

Auf allen Seiten:

- `html lang="de"`.
- Title.
- Meta Description.
- Canonical.
- Open Graph Title.
- Open Graph Description.
- Open Graph Image.
- Robots.
- Favicon.

Globales JSON-LD:

- `Organization` fuer Sartu.
- `WebSite`.

Wenn Sartu ein echtes lokales Businessprofil mit berechtigtem Standort fuehrt:

- `LocalBusiness` ergaenzen.
- keine falschen Niederlassungen.

### 24.2 Unterseiten

- Breadcrumb sichtbar.
- `BreadcrumbList` JSON-LD.
- genau eine H1.

### 24.3 Leistungsseiten

- `Service` JSON-LD.
- `FAQPage`, wenn FAQ sichtbar.
- sichtbare Preise nur, wenn sie aus zentralem Preisstand kommen.

### 24.4 Ratgeber

- `Article`.
- Autor/Organisation.
- Datum erstellt/geaendert.
- sichtbares Update-Datum.

### 24.5 Lexikon

- `DefinedTerm` / `DefinedTermSet`, sofern sauber implementiert.
- sonst mindestens Article/WebPage plus interne Begriffstruktur.

### 24.6 Nicht verwenden

- Fake-Review-Schema.
- AggregateRating ohne echte Bewertungen.
- LocalBusiness fuer Orte ohne Standort.
- FAQPage fuer unsichtbare Fragen.
- Preise im Schema, die nicht sichtbar identisch sind.

---

## 25. Technische SEO-/Performance-Anforderungen

### 25.1 Vor Go-live

Jede Seite:

- Statuscode 200.
- genau eine H1.
- Title eindeutig.
- Description eindeutig.
- Canonical auf finale URL.
- index/noindex bewusst gesetzt.
- Breadcrumb.
- interne Links pruefen.
- keine kaputten Links.
- keine alten Preise.
- keine Add-on- oder Minutenreste.
- keine alten Care S/M/L mit falschen Preisen.
- kein `noindex` auf Live-Kernseiten.

### 25.2 Dateien

Root:

- `/sitemap.xml`
- `/robots.txt`
- `/llms.txt`
- `/favicon.ico`
- Open Graph Bild.

Hinweis zu `llms.txt`:

- kann als Agenten-/Dokumentationsdatei angelegt werden.
- nicht als Google-Ranking- oder GEO-Garantie bewerben.

### 25.3 Performance

Ziele:

- statisch oder static-first.
- wenig JavaScript.
- Fonts lokal oder datenschutzsauber.
- CSS klein und kritisch optimiert.
- Bilder optimiert.
- LCP-Element sichtbar schnell.
- keine Layoutspruenge.
- mobile-first.

---

## 26. Conversion-Regeln

### 26.1 CTA-Hierarchie

Primaer:

- `Bedarf pruefen lassen`

Sekundaer:

- `Preise ansehen`

Tertiaer:

- `Rueckfrage stellen`

Nicht verwenden:

- `Jetzt kaufen`
- `Paket waehlen`
- `Extras hinzufuegen`
- `SEO buchen`
- `Kostenlosen Call buchen` als Haupt-CTA

### 26.2 Wiederkehrender CTA-Block

Ueberschrift:

> Wenige Angaben reichen fuer den ersten Schritt.

Text:

> Sartu fragt nicht nach Seitenzahl, Designstil oder SEO-Stufe. Sie beschreiben Ihr Unternehmen, Ziel, Region, Domainstatus und besondere Anforderungen. Danach erhalten Sie eine vorlaeufige Empfehlung mit Preis.

Buttons:

- `Bedarf pruefen lassen`
- `Preise ansehen`

### 26.3 Formularregeln

- Pflichttelefonnummer vermeiden.
- keine zu fruehe Kontaktpflicht.
- Empfehlung vor Kontakt zeigen.
- B2B-Bestaetigung.
- Datenschutz.
- klare Unverbindlichkeit bis Angebot.

---

## 27. Recht und Vertrauen

### 27.1 Pflichtklarheit

Auf der Website klar:

- alle Preise netto zzgl. gesetzlicher Umsatzsteuer.
- nur fuer Unternehmer.
- Sartu leistet keine Rechtsberatung.
- keine Ranking-, Umsatz- oder KI-Nennungsgarantie.
- Rechtstexte werden technisch eingebunden, nicht rechtlich erstellt.
- KI wird genutzt, aber Ergebnisse werden geprueft.

### 27.2 Keine alten Versprechen uebernehmen

Aus alten Dateien nicht uebernehmen:

- Geld-zurueck-Garantie, solange rechtlich nicht sauber formuliert.
- Add-ons.
- SEO-Stufen.
- Aenderungsminuten.
- Logo-Pakete.
- Express-Lieferung.
- Care-Minuten.
- "DSGVO-konform" als pauschale absolute Garantie.

Besser:

- `datensparsam umgesetzt`.
- `Rechtstexte technisch eingebunden`.
- `Consent-Loesung, soweit erforderlich`.
- `rechtliche Texte final durch Anbieter/Kanzlei`.

---

## 28. Umsetzungsetappen

### Etappe 1: Launchfaehige Kernwebsite

Bauen:

- Startseite.
- Leistungen.
- Preise.
- Ablauf.
- Briefing/Lumi.
- Kontakt.
- Ueber uns.
- Impressum.
- Datenschutz.
- AGB-Platzhalter oder rechtlich finaler Text.
- 7 Leistungsseiten.
- Ratgeber-Hub mit 3 bis 6 Startartikeln.
- Lexikon-Hub mit 20 bis 40 Begriffen als Start.

### Etappe 2: Autoritaetsausbau

Bauen:

- weitere Ratgeber.
- 40 bis 60 Lexikonbegriffe.
- 3 bis 5 Branchen-Hubs.
- 2 bis 4 starke Ortsseiten.
- echte Case Studies, sobald vorhanden.

### Etappe 3: Skalierung nur nach Daten

Erst nach Search-Console- und Lumi-Daten:

- weitere Orte.
- weitere Branchen.
- neue Ratgeber.
- Relaunch-Hub.
- Vergleichsseiten.

---

## 29. Abnahmekriterien fuer dieses Websitekonzept

Die Umsetzung gilt nur als konform, wenn:

- Startseite in den ersten Sekunden Festpreis, Portal, kein WordPress und SEO-/GEO-Basis zeigt.
- Platzhirsch sichtbar die Empfehlung ist.
- es keine Add-on-Liste gibt.
- es keine Aenderungsminuten gibt.
- der Kunde kein Paket selbst kaufen muss.
- Lumi vor Kontakt nur 8 bis 12 leichte Eingaben sammelt.
- Portal als USP frueh und konkret erklaert wird.
- Leistungen transparent gezeigt werden, ohne buchbare Extras zu erzeugen.
- Preise netto zzgl. MwSt. klar sind.
- Domaininhaberschaft beim Kunden klar ist.
- SEO/GEO als Startfundament erklaert wird.
- keine Ranking- oder KI-Nennungsgarantie existiert.
- Ortsseiten-Gate eingehalten wird.
- keine Fake-Referenzen, Fake-Bewertungen oder Fake-Logos erscheinen.
- alle Kernseiten eigene Titles, Descriptions, H1, Canonicals und Schema-Regeln haben.
- Ratgeber und Lexikon nicht als duenne Masseninhalte starten.
- Portal-Screens als Muster markiert sind, solange sie nicht produktionsreif sind.

---

## 30. Logikpruefung

### 30.1 Ist das Angebot auf der Website klar genug, obwohl es keine Extras gibt?

Ja, wenn `/leistungen` die Faehigkeiten transparent erklaert und zugleich sagt:

> Sie muessen diese Punkte nicht einzeln auswaehlen; Sartu ordnet sie im passenden Website-Ergebnis ein.

Dadurch weiss der Kunde, was Sartu kann, ohne sich durch Extras entscheiden zu muessen.

### 30.2 Muss SEO spaeter neu geschrieben werden?

Nein. SEO-/GEO-Basis ist ab Launch enthalten. Spaeterer Ausbau verbessert auf Basis echter Daten:

- schwache Seiten.
- neue Suchfragen.
- neue Leistungen.
- neue Regionen.
- interne Verlinkung.
- veraltete Inhalte.

Das ist kein pauschaler Neustart.

### 30.3 Wird das Portal zu WordPress?

Nein. Die Website muss das sehr klar zeigen:

Kunde kann:

- Fakten bestaetigen.
- Oeffnungszeiten aendern.
- Kontaktdaten aendern.
- vorhandene Datensaetze pflegen.
- Seiten deaktivieren/reaktivieren.
- Feedback geben.

Kunde kann nicht:

- Layout bauen.
- Seiten frei erstellen.
- Navigation umbauen.
- SEO-Felder wild bearbeiten.
- Plugins installieren.
- Code aendern.

### 30.4 Ist Platzhirsch als Empfehlung logisch?

Ja, wenn die Website zeigt:

- Platzhirsch ist fuer regionalen Wettbewerb, mehrere Leistungen, Recruiting und Vertrauensaufbau.
- Sartu empfiehlt trotzdem kleiner, wenn Start oder Wachstum ausreichen.
- Lumi ist keine erzwungene Upsell-Maschine.

### 30.5 Ist das mit wenig Kundenarbeit vereinbar?

Ja. Der Kunde beantwortet nur Geschaeftsfakten. Sartu nutzt Portal, KI, Altwebsite, Uploads und interne Regeln, um daraus Struktur, Texte, Design und Code zu erzeugen.

---

## 31. Direkte Textbausteine fuer die Umsetzung

### 31.1 Hero

> Individuell programmierte Firmenwebsites zum Festpreis.
>
> Sartu plant, textet, programmiert und betreibt Ihre Website. Sie beantworten nur die Fragen zu Ihrem Unternehmen; Struktur, Design, Technik und SEO-/GEO-Basis uebernehmen wir.

Buttons:

- `Bedarf pruefen lassen`
- `Preise ansehen`

### 31.2 Portal

> Ihr Projekt laeuft nicht ueber endlose E-Mail-Ketten. Im Sartu-Portal sehen Sie Angebot, Zahlungen, offene Aufgaben, Domainstatus, Vorschau, Feedback und spaetere kleine Pflege. Sie bearbeiten keine Website wie in WordPress; Sie bestaetigen Fakten, geben Feedback und behalten den naechsten Schritt im Blick.

### 31.3 Leistungen

> Sartu verkauft keine Website-Extras. Strategie, Texte, Design, Code, SEO-/GEO-Basis, Domain/Launch, Portal und Betrieb werden zu einem passenden Website-Ergebnis gebuendelt.

### 31.4 Preise

> Sie muessen kein Paket auswaehlen. Die kurze Bedarfseinschaetzung zeigt, welche Loesung wahrscheinlich passt. Sartu prueft das Ergebnis persoenlich vor dem Angebot.

### 31.5 SEO/GEO

> Ihre Website wird so aufgebaut, dass Menschen, Google und KI-Sucherlebnisse schnell verstehen, wer Sie sind, was Sie anbieten, fuer wen es passt, wo Sie arbeiten und wie der naechste Schritt aussieht.

### 31.6 Keine Garantie

> Sartu baut das technische und inhaltliche Fundament fuer Sichtbarkeit. Rankings, Anfragen, Umsaetze oder Nennungen in KI-Systemen koennen nicht garantiert werden.

---

## 32. Finale Empfehlung

Die Website sollte zuerst als starke Verkaufs- und Autoritaetsarchitektur gebaut werden, nicht als riesiges Content-Portal. Wichtig ist:

1. Kernseiten sauber und hochwertig.
2. Portal als sichtbarer USP.
3. Preise klar und ohne Auswahlstress.
4. Leistungen transparent, aber nicht als Add-ons.
5. Lumi kurz und vertrauensbildend.
6. SEO/GEO als Fundament, nicht als Hype.
7. Ratgeber und Lexikon kuratiert starten.
8. Ortsseiten erst nach Qualitaetsgate.

So entsteht eine Website, die gleichzeitig verkauft, erklaert, Vertrauen schafft und langfristig SEO-/GEO-Potenzial aufbaut, ohne das Angebotsmodell durch zu viel Auswahl wieder kaputtzumachen.
