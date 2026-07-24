# Sartu Endkontrolle fuer Webseiten

Stand: 18. Juli 2026.

Grundlage:

- Rohdatei: `C:\Users\Nils Haake\Documents\Codex\2026-07-14\ic\outputs\rohpruefpunkte-6000-10000-vibe-prueftool.md`
- Umfang der Rohdatei: 8.930 Einzelpunkte in 32 Themenbloecken.
- Sartu-Kontext: produktisierte B2B-Webdesign-Agentur, Festpreis, Portal, KI-gestuetzte Produktion, keine Add-on-Liste, keine Aenderungsminuten, keine WordPress-Pflege.

---

## 1. Kurzurteil

Die Rohdatei ist wertvoll, aber nicht als direkte Endkontroll-Checkliste nutzbar.

Sie ist eine breite Rohdatenbank fuer ein spaeteres Website-Prueftool. Fuer die taegliche Sartu-Endkontrolle ist sie viel zu granular, vielfach doppelt und teilweise fuer ganz andere Systeme gedacht: Portal, Datenbanken, OAuth, Kubernetes, MCP, E-Commerce, SaaS-Betrieb und Security-Governance.

**Entscheidung:**

- Die Rohpunkte werden nicht 1:1 in Kundenprojekte uebernommen.
- Sie werden zu wenigen, stabilen Sartu-Pruefprofilen verdichtet.
- Alles, was fuer normale Firmenwebsites nicht relevant ist, wandert in `Portal/Admin`, `Sonderprojekt` oder `spaeteres Prueftool`.
- Alte oder unprofessionelle SEO-Praktiken werden gestrichen.

**Praktischer Zielzustand:**

Eine Kundenwebsite darf erst live gehen, wenn sie technisch crawlbar, fachlich wahr, lokal sauber, schnell genug, barrierearm, rechtlich vorbereitet, visuell geprueft und im Portal sauber versioniert ist.

---

## 2. Pruefprofile

Sartu braucht vier Profile statt einer Monsterliste.

| Profil | Gilt fuer | Zweck |
|---|---|---|
| `SARTU-PUBLIC` | oeffentliche Sartu-Website | Verkauf, SEO/GEO, Preise, Portal-USP, Vertrauen |
| `CUSTOMER-WEB` | normale Kundenseiten Start/Wachstum/Platzhirsch | Launch-Qualitaet fuer Firmenwebsites |
| `CUSTOMER-LOCAL` | Kundenseiten mit lokaler Sichtbarkeit, Orte, Standorte, Servicegebiet | lokale Fakten, NAP, Google-Profil, Ortsseiten-Gate |
| `PORTAL-PROD` | Sartu-Portal, Admin, Zahlungen, Domains, Agentenworker | Security, Datenschutz, Mandanten, Mollie, INWX, KI-Produktion |

Sonderprojekte bekommen zusaetzliche Profile, wenn sie wirklich gebraucht werden:

- `ECOMMERCE`
- `BOOKING`
- `MEMBER-LOGIN`
- `MULTILINGUAL`
- `API-INTEGRATION`
- `REGULATED-CONTENT`

---

## 3. Rohkatalog: Was bleibt, was wird verschoben?

| Rohbereich | Entscheidung | Begruendung |
|---|---|---|
| Accessibility, UX, Mobile, Formulare | behalten und deduplizieren | relevant fuer jede Sartu- und Kundenseite |
| Bilder, Video, Assets, Lazy Loading | behalten | wichtig fuer Performance, Bildsuche, Nutzererfahrung |
| Crawling, Indexierung, Robots, Sitemaps | behalten | harter SEO-/GEO-Launchkern |
| GEO, AEO, AI Search | behalten, aber entmystifizieren | GEO ist gute SEO plus klare Fakten; keine Garantie |
| Informationsarchitektur und interne Links | behalten | bestimmt Verstaendlichkeit fuer Menschen und Maschinen |
| Keywordfinding, Briefing, Texte | behalten, aber ohne Keyword-Fetisch | wichtig fuer Suchintention und Conversion |
| Local SEO und Standortpruefung | behalten, mit Gate | sehr wichtig, aber riskant bei duennen Ortsseiten |
| Onpage, Metadata, Snippets, HTML | behalten | Standard-Endkontrolle |
| Performance und Core Web Vitals | behalten, messbar machen | wichtig, aber keine Lighthouse-100-Religion |
| Structured Data und Entitaeten | behalten, streng nach sichtbarer Wahrheit | stark fuer GEO, riskant bei Fake-Markup |
| URL, HTTP, Redirects, Canonicals | behalten und stark deduplizieren | viele Rohpunkte meinen dasselbe |
| Compliance, Legal, Privacy, Third Parties | behalten | Muss vor Launch geklaert sein |
| Copyright, Marken, Assets, Lizenzen | behalten | besonders bei KI- und Stock-Medien |
| Analytics, Monitoring, Release-QA | behalten | nach Launch und fuer Schutzbetrieb wichtig |
| Produkt-Governance, Scope, Kundenprojekt | behalten | passt direkt zu Sartu-Festpreis und Portal |
| Rendering, JavaScript, Client Apps | behalten, wenn JS genutzt wird | Sartu-Seiten duerfen nicht JS-blank sein |
| Infrastruktur, Cloud, DNS, CDN, Betrieb | teilweise behalten | Domain/DNS/Hosting ja; CDN/Edge nur wenn sinnvoll |
| Migration, Backup, Restore, DR | teilweise behalten | Relaunch und Betrieb ja; Enterprise-DR getrennt |
| AppSec, API, Auth, Sessions, Uploads | verschieben nach Portal oder Sonderprojekt | normale statische Website braucht nur Formular-/Upload-Basis |
| Payment, E-Commerce, Produktseiten | Sonderprojekt/Portal | nicht Teil normaler Kundenwebsite |
| Datenbankbereiche | Portal/Sonderprojekt | fuer statische Kundenwebsites meist irrelevant |
| AI-, LLM-, RAG-, Agentensicherheit | Portal/Agentenworker plus Content-Faktencheck | nicht jede Kundenseite braucht LLM-Security |
| MCP, Connectoren, Tool-Berechtigungen | Portal/Agentenworker | nicht in Website-Endkontrolle fuer Kunden verwischen |
| Scanner-Architektur, Evidence Vault | spaeteres Prueftool | wichtig fuer Produktaufbau, nicht fuer jeden Launch als Pflicht |
| Secrets, Kryptografie, Consent | splitten | Consent/Secrets ja; Kryptografie vor allem Portal |
| Supply Chain, SBOM, CI/CD, DevSecOps | Portal/Starter/Agentenworker | fuer produktive Pipeline wichtig, nicht als Kunden-Checkliste |
| Cybersecurity-Governance, Incident Response | Portal/Betrieb | fuer Sartu als Anbieter wichtig, nicht Kundenauswahl |
| International SEO/hreflang | bedingt | nur bei Mehrsprachigkeit oder mehreren Laendern |

---

## 4. Schweregrade

| Grad | Bedeutung |
|---|---|
| `BLOCKER` | kein Launch, bevor geloest oder bewusst aus dem Scope entfernt |
| `HIGH` | vor Launch loesen, ausser es gibt eine dokumentierte Ausnahme |
| `MEDIUM` | darf mit Ticket/Monitoring nach Launch folgen, wenn kein Kernrisiko |
| `OPTIONAL` | nur pruefen, wenn der Seitentyp oder die Funktion existiert |

Wichtig: Ein technischer Toolfehler ist nicht automatisch ein Kundenproblem. Ein Befund braucht immer URL, Screenshot oder Messwert, betroffene Seite, Auswirkung und konkrete Fix-Empfehlung.

---

## 5. Launch-Endkontrolle fuer Sartu- und Kundenseiten

Diese Liste ersetzt die Rohpunkte fuer normale Website-Launches.

### A. Build, HTML und Rendering

- `BLOCKER`: Build/Export laeuft ohne Fehler.
- `BLOCKER`: wichtigste Inhalte stehen im initialen HTML oder sind fuer Crawler verlaesslich renderbar.
- `BLOCKER`: keine leere oder stark abhaengige JS-Huelle fuer Seiten, die ranken sollen.
- `HIGH`: pro Seite genau eine klare H1.
- `HIGH`: semantische Struktur mit `main`, sinnvollen Sections, Listen und Tabellen fuer echte Daten.
- `HIGH`: keine sichtbaren Layoutfehler in Desktop, Tablet und Mobile.
- `MEDIUM`: keine relevanten Console-Fehler in Hauptflows.

### B. URL, Statuscodes, Canonicals und Redirects

- `BLOCKER`: Live-Domain, HTTPS und Canonical-Host sind eindeutig.
- `BLOCKER`: www-/non-www- und HTTP-/HTTPS-Varianten leiten sauber weiter.
- `BLOCKER`: Canonicals zeigen auf indexierbare 200-URLs.
- `BLOCKER`: keine Redirect-Loops, keine langen Redirect-Ketten.
- `HIGH`: 404 und 410 sind bewusst geregelt.
- `HIGH`: Relaunches haben ein Redirect-Mapping fuer alte relevante URLs.
- `HIGH`: Slugs sind stabil, lesbar und ohne unnoetige Parameter.

### C. Crawling, Indexierung, Sitemap und robots.txt

- `BLOCKER`: indexierbare Live-Seiten haben kein versehentliches `noindex`.
- `BLOCKER`: Staging, Previews, interne Suche, Danke-Seiten, Portal und Admin sind nicht oeffentlich indexierbar.
- `BLOCKER`: `robots.txt` blockiert keine wichtigen CSS-, JS-, Bild- oder Inhaltsressourcen.
- `HIGH`: XML-Sitemap enthaelt nur kanonische, indexierbare 200-URLs.
- `HIGH`: Sitemap ist in `robots.txt` verlinkt und in Google Search Console/Bing Webmaster Tools einreichbar.
- `HIGH`: wichtige Seiten sind intern verlinkt und nicht verwaist.
- `MEDIUM`: `lastmod` nur bei echten inhaltlichen Aenderungen setzen.

### D. Onpage, Snippets und GEO-Antwortstruktur

- `BLOCKER`: Title, Description, H1 und sichtbarer Seiteninhalt widersprechen sich nicht.
- `HIGH`: jede wichtige Seite hat eindeutigen Title und eindeutige Meta Description.
- `HIGH`: jede wichtige Seite beantwortet ihre Hauptfrage frueh und klar.
- `HIGH`: wichtige Seiten enthalten Antwortmodule wie `Kurz gesagt`, `Fuer wen passt das?`, `Was ist enthalten?`, `Was kostet es?`, `Wie laeuft es ab?` und `FAQ`, soweit passend.
- `HIGH`: Entitaeten werden konsistent benannt: Unternehmen, Leistungen, Orte, Preise, Zielgruppen.
- `HIGH`: keine Ranking-, Umsatz- oder KI-Nennungsgarantien.
- `HIGH`: kein commodity content ohne eigene Perspektive, konkrete Beispiele oder echte Fakten.
- `MEDIUM`: Open Graph und Social Preview sind vorhanden und stimmig.

### E. Strukturierte Daten

- `BLOCKER`: Schema.org beschreibt nur sichtbare, wahre und passende Inhalte.
- `BLOCKER`: keine Fake-Bewertungen, Fake-Standorte, Fake-Preise oder unsichtbare FAQ im Markup.
- `HIGH`: Organization oder LocalBusiness ist fachlich korrekt modelliert.
- `HIGH`: BreadcrumbList auf Unterseiten, wenn Breadcrumb sichtbar oder fachlich eindeutig ist.
- `HIGH`: Service-Markup fuer Leistungsseiten und lokale Leistungsseiten, wenn sichtbar passend.
- `HIGH`: Article-Markup nur fuer echte Ratgeber/Lexikonartikel.
- `HIGH`: FAQPage nur fuer sichtbare echte FAQ; nicht als Google-Rich-Result-Hebel verkaufen.
- `MEDIUM`: JSON-LD mit Rich Results Test und Schema Validator pruefen.
- `OPTIONAL`: JobPosting, Product, Event, VideoObject, QAPage, Dataset, FactCheck nur nutzen, wenn genau dieser Inhalt wirklich Hauptinhalt ist.

### F. Local SEO und Standortlogik

- `BLOCKER`: keine Fake-Adresse, keine Fake-Telefonnummer, keine Fake-Referenz, keine erfundene lokale Naehe.
- `BLOCKER`: Ortsseiten nur indexieren, wenn sie eigene lokale Suchintention und eigenen Nutzen haben.
- `HIGH`: Name, Adresse, Telefonnummer, E-Mail, Oeffnungszeiten und Servicegebiet sind sichtbar und konsistent.
- `HIGH`: LocalBusiness-Markup nur fuer echte Standortdaten oder sauber deklarierte Service-Area-Logik.
- `HIGH`: Google-Unternehmensprofil-Daten separat pruefen, wenn Sartu diese Einrichtung betreut.
- `HIGH`: Bewertungen nur echt, ohne Review-Gating oder irrefuehrende Anreize.
- `MEDIUM`: lokale Links/Erwaehnungen nur aus echten Beziehungen, kein lokaler Linkspam.

### G. Bilder, Medien und Performance

- `BLOCKER`: Hero-/LCP-Bild wird nicht lazy geladen.
- `HIGH`: wichtige Bilder haben passende `width`, `height`, `alt`, `loading`, `decoding` und responsive Quellen.
- `HIGH`: Bilder sind komprimiert, aber visuell nicht sichtbar kaputt.
- `HIGH`: wichtige Inhaltsbilder sind nicht nur CSS-Backgrounds.
- `HIGH`: CSS/JS fuer sichtbaren Inhalt wird nicht von robots.txt blockiert.
- `HIGH`: CLS durch feste Mediengroessen und stabile UI-Zustaende verhindern.
- `HIGH`: LCP, INP und CLS per Lighthouse/PageSpeed/WebPageTest pruefen; Feldwerte nach Launch beobachten.
- `MEDIUM`: ungenutztes CSS/JS reduzieren, Assets minifizieren und Brotli/Gzip aktivieren.
- `MEDIUM`: CDN nur einsetzen, wenn Datenschutz, Cache-Invalidierung und Messwertnutzen geklaert sind.

### H. Accessibility, Mobile UX und Formulare

- `BLOCKER`: Kontakt- und Anfrageformulare sind auf Mobile und Desktop nutzbar.
- `BLOCKER`: Formulare validieren Eingaben serverseitig und zeigen verstaendliche Fehlermeldungen.
- `HIGH`: Buttons, Links, Inputs und Icon-Controls haben accessible names.
- `HIGH`: Navigation, Modals, Tabs, Accordions und Formulare sind per Tastatur bedienbar.
- `HIGH`: Fokus ist sichtbar und nicht verdeckt.
- `HIGH`: Kontrast, Lesbarkeit, Touch-Ziele und Reflow werden geprueft.
- `HIGH`: ARIA nur nutzen, wenn native HTML-Elemente nicht reichen.
- `MEDIUM`: axe/Pa11y/Lighthouse Accessibility laufen automatisiert, manuelle Tastaturpruefung bleibt Pflicht.

### I. Recht, Datenschutz, Tracking und Medienrechte

- `BLOCKER`: Impressum und Datenschutz sind final eingebunden.
- `BLOCKER`: Tracking, Analytics, Maps, Fonts, Videos und Third-Party-Skripte sind rechtlich/technisch eingeordnet.
- `BLOCKER`: Consent greift vor Tracking oder nicht notwendigen Drittanbietern.
- `HIGH`: Bild-, Logo-, Font- und Stockrechte sind dokumentiert.
- `HIGH`: KI-generierte Bilder duerfen nicht wie echte Kundenreferenzen oder echte Arbeitsbelege wirken.
- `HIGH`: rechtlich sensible Fachbehauptungen sind vom Kunden oder Fachpartner bestaetigt.
- `MEDIUM`: Externe Links, Sponsored/Nofollow und Partnernennungen sind sauber gesetzt.

### J. Security-Basis fuer normale Websites

- `BLOCKER`: keine Secrets, API-Keys, internen Pfade oder Debugdaten im Frontend, HTML, JS, Sourcemaps oder Logs.
- `HIGH`: HTTPS, sichere Header und sichere Cookie-Einstellungen sind gesetzt, soweit die Seite Cookies nutzt.
- `HIGH`: Formulare haben Spam- und Missbrauchsschutz.
- `HIGH`: Datei-Uploads nur, wenn sie wirklich gebraucht werden; dann mit Typpruefung, Groessenlimit, Scan und getrennter Speicherung.
- `HIGH`: Admin-/Portalpfade sind nicht ueber die Kundenwebsite offen.
- `MEDIUM`: Abhaengigkeiten und Build-Artefakte werden vor Release auf bekannte Sicherheitsrisiken geprueft.

### K. Visual, Marke und Conversion

- `BLOCKER`: keine falschen Preise, alten Add-ons, Aenderungsminuten, alten SEO-Stufen oder widerspruechlichen Pakettexte.
- `HIGH`: Haupt-CTA ist klar und nicht gegen die Sartu-Logik formuliert.
- `HIGH`: Kunde muss keine Paket-, Add-on-, SEO-, Hosting- oder Technikentscheidung treffen.
- `HIGH`: Portal, Festpreis und Ablauf werden verstaendlich erklaert.
- `HIGH`: keine Fake-Testimonials, Fake-Logos oder nicht belegten Superlative.
- `MEDIUM`: Design wirkt hochwertig, ruhig, mobil sauber und nicht wie generische KI-Vorlage.

### L. Evidence und Abnahme

- `BLOCKER`: jede Live-Freigabe hat Build-Stand, URL, Datum, Pruefprofil und offene Risiken dokumentiert.
- `HIGH`: automatisierte Checks speichern Reports oder mindestens reproduzierbare Befunddaten.
- `HIGH`: Screenshots fuer Desktop und Mobile sind abgelegt.
- `HIGH`: offene `HIGH`- oder `BLOCKER`-Befunde haben Entscheidung: fixen, Scope entfernen oder Launch stoppen.
- `MEDIUM`: nach Launch werden Search Console, Bing, Monitoring, 404, Formulare und Core Web Vitals beobachtet.

---

## 6. Zusaetzliche Checks fuer Sartu-PUBLIC

Die Sartu-Website hat strengere inhaltliche Konsistenzanforderungen als normale Kundenseiten.

- `BLOCKER`: Preise entsprechen dem kanonischen Geschaeftsmodell: Start 1.490 EUR, Wachstum 3.900 EUR, Platzhirsch 7.900 EUR, Sonderprojekt ab 12.500 EUR, netto zzgl. USt.
- `BLOCKER`: Platzhirsch ist sichtbar empfohlen, aber nicht als falsche Pflicht verkauft.
- `BLOCKER`: keine Aenderungsminuten, keine Add-on-Preisliste, keine SEO-Stufen, kein Paket-Selbstkonfigurator.
- `BLOCKER`: kein Text behauptet, das Portal sei schon vollautomatisch produktionsreif, wenn nur Muster-/Prototyp-Screens gezeigt werden.
- `HIGH`: Portal-USP ist auf Startseite, Preise, Ablauf und Portal-Leistungsseite klar erklaert.
- `HIGH`: Domainregel stimmt: Kunde waehlt Namen und bleibt Inhaber, Sartu verwaltet technisch.
- `HIGH`: SEO/GEO wird als enthaltene Basis erklaert, nicht als garantierte KI-Nennung.
- `HIGH`: Lexikon, Ratgeber und Ortsseiten starten kuratiert, nicht massenhaft duenn.
- `HIGH`: `llms.txt` darf gepflegt werden, aber nicht als Google-Rankingfaktor darstellen.
- `HIGH`: alte Website-Reste wie Logo-Paket, alte Care-Minuten, alte Preislogik oder Privatkundenformulierungen sind entfernt.

---

## 7. Zusaetzliche Checks fuer CUSTOMER-WEB

- `BLOCKER`: alle Unternehmensfakten stammen aus bestaetigtem Briefing, Altwebsite, Unterlagen oder Kundenfreigabe.
- `BLOCKER`: KI darf keine Referenzen, Zertifikate, Standorte, Garantien, Fachbehauptungen oder Rechtsaussagen erfinden.
- `HIGH`: Paketgrenze ist eingehalten oder das Projekt wurde bewusst auf Sonderprojekt verschoben.
- `HIGH`: Texte, Struktur und SEO/GEO-Basis werden vor der Kundenvorschau intern geprueft.
- `HIGH`: Kundenpflege im Portal passt zur Website: Oeffnungszeiten, Kontaktdaten, Social-/Terminlinks, vorhandene Team-/Job-/Projektbereiche.
- `HIGH`: deaktivierte Seiten verschwinden aus Navigation und Sitemap und bekommen je nach Fall `noindex`, Redirect oder Archivstatus.
- `HIGH`: Domain-, DNS- und E-Mail-Snapshot ist vor Launch dokumentiert.
- `HIGH`: Formularzustellung und Spam-Schutz sind getestet.
- `MEDIUM`: nach Launch werden technische Suchgesundheit, 404, Verfuegbarkeit, Backups und Formulare im Schutzbetrieb ueberwacht.

---

## 8. Zusaetzliche Checks fuer CUSTOMER-LOCAL

- `BLOCKER`: keine indexierbare Ortsseite ohne echtes lokales Suchinteresse und eigenen Inhalt.
- `BLOCKER`: keine Stadt-/Regionstexte, bei denen nur der Ortsname getauscht wurde.
- `HIGH`: lokale Seite erklaert ehrlich, ob Sartu oder der Kunde dort einen Standort, Servicegebiet oder Remote-Leistung hat.
- `HIGH`: Ortsseite hat eigene Branchen-/Leistungsbeispiele, lokale FAQ und interne Links.
- `HIGH`: NAP-Daten auf Website, Schema und Google-Unternehmensprofil stimmen zusammen.
- `HIGH`: regionale Hubs werden bevorzugt, bevor viele kleine Stadtseiten live gehen.
- `MEDIUM`: Orte ohne Suchsignal bleiben `noindex_preview`, werden verbessert, zusammengelegt oder verworfen.

---

## 9. Zusaetzliche Checks fuer PORTAL-PROD

Diese Punkte gehoeren nicht in jede Kundenwebsite-Endkontrolle. Sie sind aber Pflicht, bevor Sartu das Portal produktiv fuer echte Kunden nutzt.

- `BLOCKER`: echte Authentifizierung, Admin-2FA, Rollen, Rechte und Audit-Log.
- `BLOCKER`: Mandantentrennung fuer Kundendaten, Dateien, Screenshots, Logs und Evidence.
- `BLOCKER`: PostgreSQL/Migrationen, Backups, Restore-Test und Aufbewahrungsregeln.
- `BLOCKER`: Mollie-Zahlungen, Webhooks, Mandate, Idempotenz, Fehlschlaege und Erstattungen Ende-zu-Ende getestet.
- `BLOCKER`: INWX-Domainlebenszyklus mit Kundendaten, Verfuegbarkeit, Transfer, DNS-Snapshot und Uebergabe getestet.
- `BLOCKER`: Uploads mit Typ-/Groessenlimit, Malware-Scan, Rechtebestaetigung und getrennter Speicherung.
- `BLOCKER`: Agentenworker isoliert; Codex/Claude bekommt nur Projektarbeitsbereich, strukturierte Daten und begrenzte Werkzeuge.
- `BLOCKER`: Agent kann keine Mollie-, Registrar-, Kundendatenbank- oder Produktionsserver-Aktionen direkt ausfuehren.
- `HIGH`: Prompt-Injection-, Tool-Abuse-, Data-Leakage- und Excessive-Agency-Risiken sind modelliert.
- `HIGH`: LLM-Ausgaben werden validiert und nie ungeprueft veroeffentlicht.
- `HIGH`: Logs enthalten keine unnoetigen personenbezogenen Daten oder Secrets.
- `HIGH`: Secret-Scanning, Dependency-Scanning, Update-Prozess und Release-Freigabe laufen.
- `HIGH`: QA-Findings haben Evidence, Toolversion, Zeitpunkt, Umgebung, URL/Commit und reproduzierbaren Fixweg.

---

## 10. Punkte, die gestrichen oder stark herabgestuft werden

Diese Themen sind nicht professioneller Standard fuer Sartu-Standardwebsites oder duerfen nicht als Pflicht verkauft werden.

- AMP als Standard fuer neue Seiten.
- Web Stories als Standard.
- Keyword-Dichte, fixe Wortzahlen oder reine Keyword-Varianten als Rankinglogik.
- Meta Keywords.
- Lighthouse 100 als Launchpflicht.
- `llms.txt` als Google-Rankingfaktor.
- FAQPage-Markup als Hauptziel fuer Google-Rich-Results.
- Schema fuer unsichtbare Inhalte.
- Fake-Reviews, Fake-Rating, Fake-Standorte, Fake-Autoren, Fake-Update-Datum.
- Doorway Pages oder massenhaft fast gleiche Ortsseiten.
- automatische KI-Content-Veroeffentlichung ohne Sartu-Freigabe.
- Garantie auf Rankings, Anfragen, Umsatz oder KI-Nennung.
- CDN, Edge-Caching, 103 Early Hints, Service Worker oder PWA als Pauschalpflicht.
- hreflang ohne echte Mehrsprachigkeit oder echte Laender-/Sprachversionen.
- Dataset, FactCheck, Product, Merchant, News- und Video-Sitemap ohne passenden Seitentyp.
- DB-Query-, GraphQL-, OAuth-, Kubernetes-, MariaDB-, MongoDB-, PCI- und MCP-Checks fuer einfache statische Kundenwebsites.
- komplexe SBOM-/Supply-Chain-Governance als Kunden-Endkontrolle; gehoert in Sartu-Pipeline und Portal.
- AI-Overview-Query-Messung als harter Launchblocker; hoechstens Trend- und Qualitaetssignal.

---

## 11. Automatisieren vs. manuell pruefen

### Automatisieren

- Build/Export.
- interne Links, Statuscodes, Redirects, Canonicals.
- Sitemap, robots.txt, noindex, indexierbare 200-URLs.
- Title, Description, H1, Duplicate-Metadata.
- grobe Schema-Validierung.
- Bilder: fehlende Masse, Alt, lazy/fetchpriority, Dateigroessen.
- Lighthouse/PageSpeed/WebPageTest-Labmessung.
- axe/Pa11y/Lighthouse Accessibility.
- Viewport-Screenshots und einfache Layout-Regressionen.
- Form-Smoke-Tests.
- verbotene Altbegriffe in Sartu: Add-ons, Aenderungsminuten, SEO-Stufen, alte Preise.
- Secrets im Repo, Frontend und Build-Artefakt.
- Dependency- und Package-Audit.

### Manuell pruefen

- Wahrheitsgehalt von Unternehmensfakten.
- Suchintention und Nutzwert.
- lokale Seitenqualitaet.
- rechtliche Texte und sensible Fachbehauptungen.
- Designqualitaet und visuelle Glaubwuerdigkeit.
- Bild-/Marken-/Lizenzrechte.
- ob eine Seite wie echte Erfahrung wirkt oder nur generische KI-Zusammenfassung ist.
- ob der CTA zur Sartu-Logik passt und Kunden nicht in Auswahlstress bringt.
- ob Portal- und Automationsversprechen ehrlich sind.

---

## 12. Minimaler QA-Stack fuer die erste Sartu-Version

Nicht zuerst ein riesiges Prueftool bauen. Erst diese 12 Gates stabil machen:

1. Build-Gate.
2. Crawl-Gate fuer Links, Statuscodes, Canonicals, Redirects.
3. Index-Gate fuer robots, noindex, Sitemap.
4. Metadata-Gate fuer Title, Description, H1, OG.
5. Sartu-Konsistenz-Gate fuer Preise, Platzhirsch, keine Add-ons, keine Minuten.
6. Schema-Gate fuer sichtbare, wahre, valide Daten.
7. Image-Gate fuer LCP, Alt, Masse, responsive Quellen.
8. Performance-Gate fuer LCP/INP/CLS-Labwerte und Asset-Budgets.
9. Accessibility-Gate mit axe/Pa11y plus manueller Tastaturprobe.
10. Form-Gate fuer Validierung, Versand, Spam-Schutz und Danke-/Fehlerstatus.
11. Legal-/Consent-Gate fuer Rechtstexte, Tracking und Drittanbieter.
12. Evidence-Gate mit Report, Screenshots, offenen Befunden und Launchentscheidung.

Danach kann daraus ein Sartu-internes Prueftool wachsen. Die Rohdatei bleibt dafuer nuetzlich, aber die Launchentscheidung darf nicht an 8.930 Einzelcheckboxen haengen.

---

## 13. Quellen

Die Entscheidungen orientieren sich an aktuellen Standards und offiziellen Leitlinien:

- Google Search Essentials: https://developers.google.com/search/docs/essentials
- Google Optimizing for Generative AI Search: https://developers.google.com/search/docs/fundamentals/ai-optimization-guide
- Google Helpful, reliable, people-first content: https://developers.google.com/search/docs/fundamentals/creating-helpful-content
- Google Spam Policies: https://developers.google.com/search/docs/essentials/spam-policies
- Google Structured Data Guidelines: https://developers.google.com/search/docs/appearance/structured-data/sd-policies
- Google Sitemaps: https://developers.google.com/search/docs/crawling-indexing/sitemaps/overview
- Google Core Web Vitals: https://developers.google.com/search/docs/appearance/core-web-vitals
- W3C WCAG 2.2: https://www.w3.org/TR/WCAG22/
- Bing Webmaster Guidelines: https://www.bing.com/webmasters/help/webmaster-guidelines-30fba23a
- Bing AI Performance: https://www.bing.com/webmasters/help/ai-performance-9f8e7d6c
