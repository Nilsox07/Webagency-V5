# SARTU — Spezifikation

**Eine Datei. Zwei Seiten der Medaille, sauber getrennt:**
**Teil 1** = was der **Kunde kauft**. **Teil 2** = was **SARTU für sich selbst** baut.

Stand 03.08.2026 · Aufbau nach `KONSOLIDIERUNG_RUNDE1.md`

> **Diese Datei ist im Aufbau.** Teil 0 und Teil 1 sind zusammengeführt und geprüft.
> Teil 2 folgt in derselben Sitzung; bis dahin gelten dafür die Quelldateien unverändert.
> **Es ist noch nichts archiviert.** Die Quellen bleiben, bis Runde 3 die Vollständigkeit
> bestätigt hat.

---

## Warum diese Datei existiert

Die Vorgaben lagen in 39 Wurzeldateien mit 15.397 Zeilen, die sich gegenseitig referenzieren.
Die Trennung Kundenprodukt ↔ eigene Seite war darin **angelegt, aber nirgends durchgezogen** —
das Masterkonzept beschildert sie in §13 und §16, führt sie aber nicht durch. Daraus entstanden
die Widersprüche, die diese Sitzung mehrfach zutage gefördert hat.

**Nicht in dieser Datei** (bewusst, siehe Runde 1):

| | warum |
|---|---|
| `CLAUDE.md`, `UEBERGABE_DATEILISTE.md`, `REIHENFOLGE.md`, `CODEX_*`, `PROMPT_*`, `LIVEGANG.md`, `ENTWICKLUNGSUMGEBUNG.md` | sagen, **wie gearbeitet** wird, nicht was gebaut wird |
| `OFFENE_PRUEFUNGEN.md`, `IMPLEMENTATION_*`, `MESSUNGEN.md`, `STAND.md` | halten fest, **was passiert ist** — werden fortgeschrieben |
| `.claude/skills/sartu-texter/` | ist ein **Werkzeug**, kein Dokument |

---
---

# TEIL 0 — Gemeinsame Grundlage

Gilt für beides: für das Produkt **und** für die eigene Seite.

## 0.1 Geschäftsmodell

SARTU ist eine **produktisierte B2B-Webdesign-Agentur** für regionale kleine und mittlere
Unternehmen. Der Kunde beschreibt sein Geschäft; SARTU **empfiehlt eine Lösung, nennt einen
Gesamtfestpreis, plant, textet, programmiert und betreibt** die Website.

> **Leitentscheidung:** *Der Kunde entscheidet, was sein Unternehmen erreichen soll.
> SARTU entscheidet, wie die Website das erreicht.*

**Vier wirtschaftliche Hebel:**
1. Wiederverwendbares, versioniertes **Designsystem** statt jedes Projekt neu erfinden
2. **KI-gestützte** Code-, Struktur- und Textproduktion mit menschlicher Abnahme
3. Ein **Kundenbereich** für Vertrieb, Projekt, Betrieb und Verwaltung
4. **Feste Grenzen** statt frei kombinierbarer Extras und unbegrenzter Handarbeit

**Zwei Umsatzarten:**
- **Einmal:** Website-Erstellung, Festpreis je Paket
- **Wiederkehrend:** „Rundum-Schutz" — der eigentliche **Deckungsbeitrags-Motor**

## 0.2 Positionierung

**Öffentliches Kernversprechen:**
> Individuell programmierte Firmenwebsites zum Festpreis. SARTU plant, textet, programmiert und
> betreibt Ihre Website. Sie beantworten nur die Fragen zu Ihrem Unternehmen; Struktur, Design,
> Technik und SEO-/GEO-Basis übernehmen wir.

**USP in vier Worten:** *Festpreis. Portal. Kein WordPress. SEO-/GEO-Basis ab Start.*

**Ehrliche Einordnung — wichtig für jede Kommunikation:**

| Merkmal | Einordnung |
|---|---|
| **Festpreis** | der stärkste externe USP, senkt Kaufangst |
| **Portal** | der eigentliche **Differenzierer** gegenüber Freelancern: geführter Prozess statt E-Mail-Chaos |
| **Kein WordPress** | **kein** primärer Kaufgrund — Kunden interessiert kein CMS. Aber „nie wieder Plugin-Updates, gehackte Seite, Agenturabhängigkeit" ist ein realer Schmerz. Also **Beweis unter dem Hauptnutzen**, nicht Überschrift |
| **KI-gestützt** | transparent im Produktionsabschnitt genannt, **nie** zum Hauptnutzen gemacht. KI-Optik ist ein Vertrauensrisiko |

**Verboten:** „wartungsarm", „wartungsfrei" — entwertet den Rundum-Schutz (siehe 1.3).
**Richtig:** *„Keine Wartung für Sie. SARTU betreibt Ihre Website laufend."*

**Abgrenzung nach außen:** kein Baukasten · kein WordPress-Hoster · keine Billig-Seitenschleuder ·
kein Anbieter für Privat- und Hobbyseiten.

**Tonalität:** klar, ruhig, direkt, kompetent, nicht marktschreierisch. **Ansprache „Sie".**

## 0.3 Zielgruppe

**Primär** — regional, B2B, ohne eigene Webabteilung: Handwerk, lokale Dienstleister, Praxen,
Kanzleien, Gastronomie, Immobilien, Beratungen. 1–50 Mitarbeitende, inhabergeführt.
Bedarf: Vertrauen, lokale Anfragen, Recruiting, klare Leistungsdarstellung.

**Bewusst NICHT:**
- Privatpersonen und Hobbyprojekte — **Ausschluss ist Pflicht**, weil die gesamte Preis- und
  Rechtskommunikation auf Netto/B2B aufsetzt. Bei Annahme bestätigt der Auftraggeber die
  Unternehmereigenschaft
- Kunden, die selbst Layouts, Plugins oder ein CMS verwalten wollen
- Shops, SaaS, Mitgliederbereiche, komplexe Schnittstellen **als Standardfall** → Sonderprojekt
  oder Absage
- Unternehmen ohne stabile Angebotsstruktur

## 0.4 Preise — die einzige gültige Quelle jeder Zahl

> **Alle abweichenden Zahlen aus `sartupaketepreise.md` (Basis/Pro/Platin/Enterprise) und
> `sartulastenheftwebsite.md` (1.290/2.990/5.990) sind veraltet und ungültig.**

| Paket | Einmalpreis netto | Ergebnis | Umfangsgrenze | Korrekturrunden | Betrieb | Erstes Jahr netto |
|---|---:|---|---|---:|---|---:|
| **Start** | **1.490 €** | fokussierter One-Pager | 1 Seite, ~1.200 Wörter | 1 | Schutz S, 59 €/Mon. | **2.198 €** |
| **Wachstum** | **3.900 €** | vollständige Firmenwebsite | ≤ 8 Seiten, ~3.500 Wörter | 2 | Schutz M, 129 €/Mon. | **5.448 €** |
| **Platzhirsch** | **7.900 €** | regionales Vertriebs-/Vertrauens-/Recruiting-System | ≤ 16 Seiten, ~6.500 Wörter | 2 | Schutz L, 249 €/Mon. | **10.888 €** |
| **Sonderprojekt** | **ab 12.500 €** | Shop, Login, komplexe Buchung, Schnittstellen, Mehrmarken | individuell | individuell | mind. Schutz L | **ab 15.488 €** |

**Darstellung:** Platzhirsch ist **sichtbar die Empfehlung** (größte Fläche, Badge „Empfehlung"),
Start und Wachstum kleiner ohne gleichstarke Handlung. **Ein** Hauptknopf: `Bedarf prüfen lassen`.
Keine `auswählen`-Knöpfe, keine Extra-Häkchen.

**Geld als Zahl:** integer in Cent, Anzeige `7.900,00 €`. USt 19 % als Konstante an **einer**
Stelle.

## 0.5 Textregeln

**Nach außen:** *Kundenbereich · Ihr Bereich · Anmeldung · Ihr Projekt*
**Nach außen nie:** *App · Software · SaaS · Plattform · Tool · Dashboard · System · Instanz*
Intern darf „Adminbereich" stehen.

- Der Kunde sieht **nie** einen Systemcode (`qa_failed`), immer Klartext
- Leere Werte in der Oberfläche: `Noch nicht hinterlegt` — nie `null`, `–` oder `undefined`
- Datum in **Europe/Berlin**, Format `TT.MM.JJJJ, HH:MM Uhr`, nie ISO
- Zu jeder abgegebenen Seite gehört der **Prüfbericht mit Zahlen**

Vollständige Regeln: `SARTU_TEXTREGELN.md` und der Skill `.claude/skills/sartu-texter/`.

## 0.6 Gestaltung

- `design/tokens.css` wird **als Erstes** eingebunden, vor jedem Bauteil-CSS
- **Keine Zahl im Bauteil, wo eine Variable existiert.** `border-radius:30px` ist ein Abgabefehler
- Radienskala `--r-xs` (8 px) bis `--r-pill`, skaliert über `--rk`. **Keine achte Form daneben**
- **Eine** Akzentfarbe: Lime `--lime` `#a3e635`. Kein Rot für Fehler, kein Grün für Erfolg
- **Lime ist Fläche.** Auf hellem Grund nie Schriftfarbe (1,39 : 1). Auf Lime steht immer `--ink`.
  Jede Lime-Fläche auf hellem Grund braucht `1px --line` als Kante
- Kunden- und Adminbereich müssen visuell unterscheidbar sein
- **Kein Dunkelmodus**

## 0.7 Ehrlichkeit — gilt für SARTU- und Kundenwebsites

- **Keine** Fake-Referenzen, Fake-Bewertungen, Fake-Logos, Fake-Adressen, Fake-Teamfotos
- **Keine Garantie** auf Rankings, Anfragen, Umsätze, KI-Nennungen oder vollständige
  Rechtskonformität
- „SARTU leistet keine Rechtsberatung" · „KI wird genutzt, Ergebnisse werden geprüft"
- **Solange faktisch eine Person arbeitet: `gründergeführt`, nie `unser Team`.** Kein Wir, das
  größer tut, als es ist
- Echte Referenzen erst, wenn 2–3 Projekte live sind — Bild- und Namensrechte **vor**
  Projektstart schriftlich

---
---

# TEIL 1 — Was der Kunde kauft

Das verkaufte Produkt: **Kundenwebsites.** Nicht sartu.de — die steht in Teil 2.

## 1.1 In jedem Paket enthalten

Bedarfsprüfung + begründete Empfehlung · strategische Sitemap · individuelles Design im
SARTU-Designsystem · KI-gestützte, menschlich geprüfte Programmierung ohne WordPress ·
responsive + Barrierefreiheits-Basis + Performance · Website-Texte aus bestätigten Fakten ·
**SEO-/GEO-Startsystem** · Kontaktweg/Formular · technische Einbindung freigegebener Rechtstexte
+ Consent · Vorschau + gebündeltes Feedback + Korrekturrunden · Domainprüfung, -verbindung,
Launch · Zugang zum Kundenbereich.

**Platzhirsch zusätzlich bedarfsgerecht:** Team-/Karrierebereich · Projekt-, Referenz- und
Neuigkeitenstruktur · stärkere lokale Struktur · **genau ein** Conversion-Modul (qualifiziertes
Anfrageformular *oder* einfache Ein-Kalender-Buchung *oder* einfaches Bewerbungsformular).

## 1.2 Bewusst NICHT im Erstangebot — der Scope-Schutz

> Add-on-Liste · Extraseiten-Preise · SEO-Stufen · Änderungsminuten · Logo-Pakete · Express ·
> Newsletter/Tracking als Häkchen.

**Ein Standardangebot endet exakt beim veröffentlichten Paketpreis.** Neue Ziele nach Auftrag →
**ein** konsolidiertes Folgeangebot mit Festpreis, **keine Einzelpreisliste**.

Daraus folgt die Aussage auf der eigenen Startseite: *„Es gibt keine Aufpreisliste."* Sie ist
wörtlich wahr und beschreibt diese Geschäftsregel.

**Scope-Creep-Verhinderung:** Empfehlung und Sitemap stehen **vor** Auftrag fest · der
Standardpreis wird nicht mit „notwendigen Extras" aufgeweicht · Feedback wird **gebündelt**
(parallele E-Mail, Telefon, Messenger zählen nicht als eigene Kanäle) · neue Ziele werden
getrennt vom Mangel behandelt.

## 1.3 Rundum-Schutz — fest zugeordnet, keine Kundenauswahl

| Stufe | netto/Mon. | Inhalt |
|---|---:|---|
| **Schutz S** | 59 € | Managed Hosting DE/EU, SSL, tägl. externe Backups, 30 Tage Versionen, Uptime-/Sicherheitsmonitoring, technische Updates, Selbstpflege im Kundenbereich, Erstreaktion 2 Werktage |
| **Schutz M** | 129 € | alles aus S, 90 Tage Versionen, erweiterte Formular-/Deploymentprüfung, **monatl. Technik-/Suchstatus**, Erstreaktion 1 Werktag |
| **Schutz L** | 249 € | alles aus M, 180 Tage Versionen, **engmaschiger SEO-/GEO-/Conversion-Technikcheck**, priorisierte Störungsbearbeitung, Erstreaktion binnen 8 Geschäftsstunden |

Erstlaufzeit **12 Monate** ab produktivem Betrieb, danach 30 Tage zum Monatsende kündbar,
monatlich im Voraus. **Reaktionszeit ≠ Fertigstellungszeit.** Statt Änderungsminuten pflegt der
Kunde definierte Geschäftsdaten selbst.

**Der Schutz bezahlt:** Betrieb, Verantwortung, Verfügbarkeit, Hosting, SSL, Backups, Monitoring,
technische Pflege, technische Suchgesundheit, Formularprüfung, Versionsstand, Zugang zum
Kundenbereich inkl. Rechnungs- und Zahlungsstatus, Reaktionsbereitschaft.

> **Kommunikationsfehler, der das Modell entwertet:** die Website als „wartungsarm" bewerben.
> Dann fragt der Kunde sofort *„Warum zahle ich dann 59/129/249 € im Monat?"* — **Der Aufwand
> verschwindet für den Kunden, nicht in der Welt.** Genau dafür ist die Pauschale da.

## 1.4 Zahlungsmodell

| Paket | Staffelung |
|---|---|
| Start / Wachstum | 50 % bei Auftrag, 50 % nach Abnahme vor Onlinegang |
| Platzhirsch | 40 % Auftrag, 30 % nach Leitseiten-/Systemvorschau, 30 % nach Abnahme |
| Sonderprojekt | Standard 40/30/30, im Angebot ggf. abweichend |

- **Zahlungsziel 10 Kalendertage.** Produktionsslot **erst nach erster Zahlung** verbindlich
- **Schlusszahlung** hängt an **Abnahme/Fertigstellung**, nicht an einem verschiebbaren Onlinegang
- **Zahlungswahrheit** = serverseitig authentifiziert abgerufener Status nach Webhook,
  **niemals** der Browser-Redirect. Webhooks idempotent, jede Zahlung gegen interne
  Rechnung/Betrag/Währung geprüft
- **Schutz-Abo:** Mandat beim ersten wiederkehrungsfähigen Vorgang ausdrücklich bestätigt
- **Buchhaltung nicht selbst bauen.** Rechnungen über lexoffice **oder** sevDesk (Entscheidung
  offen). Rechnungszahlen dürfen **nie** von KI erzeugt werden

**E-Rechnung — B2B-Pflicht seit 01.01.2025, nicht optional:**
Empfangen und revisionssicher archivieren können ist **sofort** Pflicht (XRechnung/ZUGFeRD nach
EN 16931). Das Buchhaltungstool wird **nur** gewählt, wenn es EN 16931, GoBD-Archivierung,
Storno/Gutschrift, USt-Behandlung und Mollie-Abgleich beherrscht.
**Verboten:** selbstgebaute PDFs als alleinige Buchhaltung — ein PDF allein ist **keine**
E-Rechnung.

## 1.5 Domain, Hosting und E-Mail

**Grundsatz:** Der Kunde entscheidet den **Domainnamen** und bleibt **Domaininhaber**. SARTU
entscheidet und verwaltet die **technische Infrastruktur**.

- **Neue Domain:** max. **3** geprüfte Vorschläge (bevorzugt `.de`) → Verfügbarkeitsprüfung über
  INWX → Kunde bestätigt genau einen Namen → letzte Echtzeitprüfung → Registrierung **erst nach
  erster Zahlung**
- **Vorhandene Domain:** Transfer bevorzugt, sonst nur DNS anbinden. **Vor jeder Änderung**
  A/AAAA/CNAME/MX/SPF/DKIM/DMARC dokumentieren (Snapshot + Rollbackplan).
  **Bestehende E-Mail darf durch den Launch nie ausfallen**
- **E-Mail-Postfächer** sind ein eigener Drittanbieterdienst. Bei Erstbedarf **eine** Ja/Nein-Frage,
  dann Empfehlung **genau eines** Anbieters plus Fremdkosten. Kein Anbieterkarussell
- **Hosting:** statische Auslieferung über Managed Hosting in **DE/EU**. Der Kunde wählt kein Hosting

**Nur diese fünf Kundenfragen:** 1) Domain vorhanden? 2) Wenn ja: welche, wer hat Zugriff?
3) E-Mail mit dieser Domain? 4) Wenn neu: Wunschname oder Vorschläge? 5) Finalen Namen und
Inhaberdaten bestätigen.

### Domain-Schutzregel — Zahlungsverzug ≠ Domainverlust

1. **Domainverlust wird nie als Druckmittel eingesetzt.** Offene Rechnungen laufen über den
   normalen Mahnweg
2. **Bei laufendem Schutzvertrag** verlängert SARTU eine ablaufende Domain zur Schadensvermeidung
   und berechnet die Fremdkosten nach
3. **Vor jedem Ablauf** mindestens **drei** dokumentierte Hinweise — 60, 30 und 7 Tage
4. **Bei Kündigung** rechtzeitig aktiv Auth-Code und Transfer anbieten
5. **Keine Verlängerung auf SARTU-Kosten** nach beendetem Vertrag — der Übergabeweg muss
   nachweislich offen gestanden haben
6. Gehört **wortgleich** in Vertrag, AGB und Kundenbereich

Weitere Fälle (Kündigung, wer zahlt die Verlängerung, Premiumdomain, fehlender Kundenzugriff,
Betriebsende) sind in Masterkonzept §6 tabellarisch geregelt und anwaltlich zu prüfen.

## 1.6 Kundenablauf, Ende zu Ende

1. **Bedarfsscheck** — 5 Themen, ~3 Min., **Preis vor Kontaktdaten**
2. **SARTU-Prüfung** — Ziel 10–15 Min., höchstens **eine** gebündelte Rückfrage
3. **Geprüftes Festpreisangebot** im Kundenbereich — Empfehlung, Sitemap, Scope, Ausschlüsse,
   Preis, Betrieb, Zahlungsplan, Terminrahmen. **14 Tage gültig**
4. **Annahme** — Rechnungsdaten, B2B-Bestätigung, Scope-Bestätigung, eindeutig
   kostenpflichtiger Knopf
5. **Erste Zahlung** → danach Slot und Domainregistrierung
6. **Adaptives Onboarding** — Aufgaben statt Fragebogen; bekannte Fakten übernommen, nur Lücken
   geschlossen
7. **Produktionsspezifikation einfrieren** — versionierte Spec aus Briefing, Projektfakten,
   Designsystem
8. **KI-gestützte Produktion** + Pflicht-QA + Adminprüfung
9. **Kundenvorschau** (versioniert) → gebündeltes Feedback → **Abnahme** → Schlussrate frei
10. **Launch** — erst nach bezahlten Meilensteinen und aktiver Kundendomain
11. **Betrieb** — Schutz, Selbstpflege, Suchgesundheit, **max. eine** begründete
    Wachstumsempfehlung

**Lieferkorridore ab vollständigem Start:** Start 7–10 WT · Wachstum 10–15 WT ·
Platzhirsch 15–25 WT. Fehlt Mitwirkung > 14 Tage → Projekt nach Hinweis pausierbar; fertige
Meilensteine bleiben fällig.

## 1.7 SEO- und GEO-Startsystem — im Websitepreis, ab Launch

> **Grundhaltung, belegt durch Google-Doku:** GEO ist **kein** magischer Zusatz und **kein**
> Spezial-Schema. Gute KI-Sichtbarkeit ist die Fortsetzung guter SEO: crawlbare, hilfreiche,
> konsistente, entitätsklare Inhalte. **Keine** Garantie auf Rankings, Anfragen, Umsatz oder
> KI-Nennungen. `llms.txt` wird angelegt, aber **nicht** als Rankingfaktor beworben.

**Enthalten ab Launch:** Suchintention und Thema je Seite · **Antwort-zuerst-Texte aus
bestätigten Fakten** · sprechende URLs (Bindestriche, keine Umlaute) · genau **eine** H1 ·
interne Links als echte Links · Title/Description/Canonical/OG/Robots · Breadcrumb +
`BreadcrumbList` · `Organization` + `WebSite` global, `Service`/`Article`/`DefinedTerm`
seitenweise **nur bei sichtbarer Entsprechung** · XML-Sitemap, robots.txt, 404, Redirect-Plan ·
echte NAP, `LocalBusiness` **nur** bei berechtigtem Standort · Bild-SEO · Search Console + Bing
Webmaster + Sitemap eingereicht, IndexNow optional.

**Performance-Gate vor Livegang, im Labor:** LCP < 2,5 s · TBT < 200 ms · CLS < 0,1.
Echte Core Web Vitals inklusive INP erst als **Feldmessung nach** Livegang.
AVIF/WebP + srcset · Hero nicht lazy, `fetchpriority=high` · self-hosted WOFF2 mit
`font-display:swap`.

**`FAQPage`** ist optional — seit Juni 2026 ohne Rich Results.

**Später:** Sichtbarkeitsausbau als **ein** datenbasiertes Folgeangebot — schwache Seiten anhand
echter Suchanfragen verbessern, veraltete Aussagen aktualisieren, interne Verlinkung schärfen.
**Kein SEO-Menü, keine Stufen, keine Minuten.**

## 1.8 Designprinzipien für Kundenwebsites

**Unveränderlich:** 4-/8-Pixel-Abstandslogik · stabile Container, responsive Raster ·
**einheitlicher Radius je Projekt** · klare Fokus- und Hoverzustände · semantisches HTML ·
barrierearme Formulare und Navigation · Bildkomponenten mit festen Seitenverhältnissen und
responsiven Quellen · begrenztes JavaScript.

**Variable Tokens — SARTU entscheidet, nicht der Kunde:** Markenfarben als **Rollen** statt
Hexwerte in Komponenten · eine Hauptschrift, optional eine Akzentschrift · Inhaltsdichte
`compact`/`balanced`/`editorial` · Formcharakter `precise`/`human`/`bold` · Bildverhältnisse
und Rhythmus passend zur Branche · Bewegungsintensität `none`/`subtle`/`expressive`.

**Varianten statt Einheitswebsite:** je wichtiger Komponente wenige getestete Varianten — etwa
drei Hero-Kompositionen, drei Leistungsdarstellungen, zwei Navigationsmuster. Der Agent darf nur
**freigegebene** Varianten kombinieren; neue Varianten durchlaufen Review, Dokumentation, Tests
und Versionierung.

## 1.9 Recht und Vertrag

**Vor dem ersten Verkauf anwaltlich (IT-Recht) prüfen — nicht selbst formulieren:**

- **B2B/Netto:** alle Preise „netto zzgl. gesetzlicher USt., ausschließlich für Unternehmer";
  B2B-Bestätigung vor Annahme. Privatkunden ausschließen
- **Werkvertrag:** Prüffrist, Mängel vs. neue Wünsche, **Abnahmefiktion**, **Mitwirkungsverzug**,
  Projektpause — §§ 640/641 BGB
- **AGB + Leistungsbeschreibung** mit klaren Scope-Grenzen: Korrekturrunde, „eine Seite",
  „ein Änderungsvorgang", enthalten/nicht enthalten je Position
- **AVV** mit Kunde **und** Subunternehmern — insbesondere KI-Anbieter, Hoster, Mollie, INWX
- **Agenten erhalten keine Produktions-, Zahlungs- oder Registrar-Schlüssel**
- **DSGVO Kundenseite:** Impressum § 5 DDG, Datenschutzerklärung, Cookie-Consent **nur** bei
  zustimmungspflichtigen Diensten — datensparsam bauen, dann ist oft kein Banner nötig.
  **Keine** pauschale „rechtssicher"-Garantie
- **Rechtstexte** werden technisch eingebunden, **nicht** rechtlich erstellt (RDG-Risiko)
- **Zufriedenheits-/Geld-zurück-Garantie nicht übernehmen**, solange nicht sauber formuliert —
  EuGH C-133/22: auch eine Zufriedenheitsgarantie ist eine gewerbliche Garantie mit Pflichtangaben
- **BFSG seit 28.06.2025:** reine B2B-Firmenwebsites und Kleinstunternehmen (< 10 MA und
  ≤ 2 Mio. € Umsatz) sind i. d. R. **nicht** verpflichtet. Sobald Shop, Buchung oder
  B2C-Vertragsschluss dabei ist, **greift WCAG 2.1 AA**. Barrierefreiheits-**Basis** immer bauen
- **Rechte und Export:** nach vollständiger Zahlung Nutzungsrechte am konkreten Website-Stand,
  an den SARTU-Texten und am kundenspezifischen Design. Domaininhaber = Kunde. Dokumentiert
  **baubarer** Export ohne Abhängigkeit vom SARTU-Master — **Exportweg vor dem ersten Verkauf
  praktisch testen**, sonst nicht mit „problemlosem Umzug" werben

---
---

# TEIL 2 — Was SARTU für sich selbst baut

> **Noch nicht zusammengeführt.** Bis dahin gelten unverändert:
> `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` (1.585 Z.),
> `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` (2.140 Z.),
> `SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` (682 Z.).

Geplante Gliederung:

- **2a — Öffentliche Website** `sartu.de`: technischer Rahmen · Navigation · Fußbereich ·
  Startseite Sektion 1–10 · `/leistungen` `/preise` `/ablauf` `/briefing` · die fünf
  Leistungsseiten · Branchenseiten · `/ueber-uns` `/kontakt` · Transparenzseiten · Ratgeber ·
  Lexikon · Pflichtseiten · Bild- und Screenshotliste · SEO-URL-Liste · Definition of Done
- **2b — Kundenbereich:** Rollen und Rechte · eiserne Sicherheitsregeln · Datenmodell (20
  Tabellen) · Formate · Anfrageeingang · feste Angebotstexte · Statuslogik · Anmeldung ohne
  Passwort · Willkommensstrecke · Screen für Screen · E-Mails · Uploads · Zahlungen ·
  88 Testfälle
- **2c — Adminbereich**
- **2d — SEO und GEO der eigenen Seite:** sechs Suchintentionen · Seitenzuordnung ·
  Fragen statt Suchwörter · was **nicht** gebaut wird · Reihenfolge zum Launch · Messung

---

# TEIL 3 — Offene Entscheidungen und Platzhalter

> Noch nicht zusammengeführt. Es gilt `SARTU_ENTSCHEIDUNGEN_OFFEN.md` (587 Z.).
> **Wo dort `offen` steht, wird nichts gebaut und nichts erfunden.**
