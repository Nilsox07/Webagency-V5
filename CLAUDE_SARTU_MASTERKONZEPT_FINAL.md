# SARTU – Masterkonzept (final, umsetzbar)

**Erstellt von:** Claude (Opus) · **Stand:** 24.07.2026
**Grundlage:** alle Konzeptdateien in `konzepte/` (Wahrheitsquelle: `GESCHAEFTSMODELL.md`, konkretisiert durch `SARTU_ANGEBOT_PORTAL_DETAILKONZEPT.md`, `SARTU_KONTAKTLOSER_VERTRIEB_LUMI_PORTAL.md`, `SARTU_DESIGNSYSTEM_PORTAL_ARCHITEKTUR.md`, `SARTU_WEBSEITENKONZEPT_FINAL_SEO_GEO.md`).

> Dieses Dokument ist die konsolidierte, widerspruchsbereinigte Bauvorlage für Website **und** Portal. Wo die Quelldateien sich widersprechen (drei Preisstände, drei Tech-Stacks, mehrere Paletten), trifft dieses Dokument eine Entscheidung und begründet sie. Die kritische Herleitung steht in `CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md`.

---

## 0. Wichtigste Korrektur vorab (bitte zuerst lesen)

Das kanonische SARTU-Modell ist **positionierungsseitig stark** und marktfähig. Die größte Gefahr ist **nicht** die Preis- oder Angebotslogik, sondern der Anspruch, das **komplette Control-Plane-Portal** (Lumi, Angebote, Mollie-Abo, INWX-Domainlebenszyklus, KI-Produktions-Orchestrierung, QA-Gates, Deployments, Rollback, SEO-/GEO-Flotte, Admin-Finanzen) **vollständig vor dem ersten Standardverkauf** zu bauen.

Für ein Ein-Personen-/Kleinstteam ist das ein zweites Produktunternehmen und ein Launch-Blocker. **Dieses Masterkonzept dreht die Reihenfolge um:**

1. **Zuerst verkaufen und liefern** (2–3 echte Referenzkunden, Website manuell + KI-assistiert, **sichtbares** Portal).
2. **Dann härten** (Angebot/Annahme, Mollie-Abo, strukturierte Selbstpflege).
3. **Dann teilautomatisieren** (Spec → assistierter Build → QA).
4. **Dann skalieren** (Provider-Adapter, Rollback, programmatische Ortsseiten).

> **Wichtige Präzisierung (nach Codex-Review):** Gestuft wird die **Automatisierungstiefe**, *nicht* die Sichtbarkeit des Portals. Wenn „Portal statt E-Mail-Chaos" der USP ist, darf Stufe 0 **keine** unsichtbare Upload-Hülle sein. Es braucht ab Launch eine **echte, klickbare Portal-Erfahrung** – die Mechanik dahinter darf manuell sein.
>
> **Formel: sichtbares Portal sofort, tiefe Automatisierung später.**

Alles Weitere in diesem Dokument ist mit dieser Stufung kompatibel.

---

## 1. Finales Geschäftsmodell

**Kurzform:** SARTU ist eine **produktisierte B2B-Webdesign-Agentur** für regionale kleine und mittlere Unternehmen. Der Kunde beschreibt sein Geschäft; SARTU **empfiehlt eine Lösung, nennt einen Gesamtfestpreis, plant, textet, programmiert und betreibt** die Website. Der Kunde trifft Geschäftsentscheidungen, SARTU trifft Design-, Struktur-, Technik-, Paket- und Anbieterentscheidungen.

**Vier wirtschaftliche Hebel (unverändert übernommen, weil richtig):**
1. Wiederverwendbares, versioniertes **Designsystem** statt jedes Projekt neu erfinden.
2. **KI-gestützte** Code-, Struktur- und Textproduktion mit menschlicher Abnahme.
3. Ein **Portal** für Vertrieb, Projekt, Betrieb und Verwaltung (in Stufen, s. Abschnitt 8/23).
4. **Feste Grenzen** statt frei kombinierbarer Extras und unbegrenzter Handarbeit.

**Zwei Umsatzarten:**
- **Einmal:** Website-Erstellung (Festpreis pro Paket).
- **Wiederkehrend:** „Rundum-Schutz" (Betrieb/Hosting/Pflege) – der eigentliche Deckungsbeitrags-Motor.

**Leitentscheidung:** *Der Kunde entscheidet, was sein Unternehmen erreichen soll. SARTU entscheidet, wie die Website das erreicht.*

---

## 2. Finale Positionierung

**Öffentliches Kernversprechen (Hero):**
> Individuell programmierte Firmenwebsites zum Festpreis. SARTU plant, textet, programmiert und betreibt Ihre Website. Sie beantworten nur die Fragen zu Ihrem Unternehmen; Struktur, Design, Technik und SEO-/GEO-Basis übernehmen wir.

**USP in vier Worten:** *Festpreis. Portal. Kein WordPress. SEO-/GEO-Basis ab Start.*

**Ehrliche Einordnung des USP (wichtig für die Kommunikation):**
- „Kein WordPress" ist **kein primärer Kaufgrund** – Kunden interessiert kein CMS. Aber „nie wieder Plugin-Updates, gehackte Seite, Agenturabhängigkeit" ist ein **realer Schmerz**. → Nicht als Headline, sondern als **Beweis direkt unter dem Hauptnutzen**, enttechnisiert: *„Ohne WordPress-Wartungsstress: keine Plugin-Updates, kein Baukasten, keine Sicherheitsbaustelle bei Ihnen."* **Nie „wartungsarm/wartungsfrei"** (entwertet den Rundum-Schutz, s. Abschnitt 4).
- „Festpreis" ist der stärkste externe USP (senkt Kaufangst).
- „Portal" ist der eigentliche **Differenzierer** gegenüber Freelancern/kleinen Agenturen: geführter Prozess statt E-Mail-Chaos.
- „KI-gestützt" wird **transparent im Produktionsabschnitt** genannt, aber **nicht** zum Hauptnutzen gemacht (KI-Optik = Vertrauensrisiko). Der Kunde kauft Ergebnis, Verantwortung, Einfachheit.

**Abgrenzung nach außen:** kein Baukasten, kein WordPress-Hoster, keine Billig-KI-Seitenschleuder, kein Privat-/Hobby-Anbieter.

**Tonalität:** klar, ruhig, direkt, kompetent, nicht marktschreierisch. **Ansprache: „Sie"** (Entscheidung – s. Abschnitt 25; passt zu B2B, Nettopreisen und Preisniveau; die alte „du"-Marke wird umgestellt).

---

## 3. Zielgruppe

**Primär (regional, B2B, ohne eigene Webabteilung):**
- Handwerk, lokale Dienstleister, Praxen, Kanzleien, Gastronomie, Immobilien, Beratungen.
- 1–50 Mitarbeitende, inhabergeführt.
- Bedarf: Vertrauen, lokale Anfragen, Recruiting, klare Leistungsdarstellung.

**Bewusst NICHT Standardzielgruppe:**
- Privatpersonen und Hobbyprojekte (→ eigener, rechtlich getrennter Prozess mit Bruttopreisen/Widerruf, aktuell **nicht** anbieten).
- Kunden, die selbst Layouts/Plugins/CMS verwalten wollen.
- Shops, SaaS, Mitgliederbereiche, komplexe Schnittstellen als **Standardfall** (→ „Sonderprojekt" oder ablehnen).
- Unternehmen mit ständig wechselndem Produkt ohne stabile Angebotsstruktur.

**Ausschluss Privatkunden ist Pflicht**, weil die gesamte Preis- und Rechtskommunikation auf **Netto/B2B** aufsetzt (s. Abschnitt 22). Bei Annahme bestätigt der Auftraggeber die Unternehmereigenschaft.

---

## 4. Pakete und Preise (verbindlicher, einziger Preisstand)

> **Dies ist der einzige gültige Preisstand.** Alle abweichenden Zahlen aus `sartupaketepreise.md` (Basis/Pro/Platin/Enterprise) und `sartulastenheftwebsite.md` (1.290/2.990/5.990) sind **veraltet und ungültig**.

| Paket | Einmalpreis netto | Ergebnis | Umfangsgrenze | Korrekturrunden | Betrieb | Erstes Jahr netto |
|---|---:|---|---|---:|---|---:|
| **Start** | **1.490 €** | fokussierter One-Pager | 1 Seite, ~1.200 Wörter | 1 | Schutz S, 59 €/Mon. | **2.198 €** |
| **Wachstum** | **3.900 €** | vollständige Firmenwebsite | ≤ 8 Seiten, ~3.500 Wörter | 2 | Schutz M, 129 €/Mon. | **5.448 €** |
| **Platzhirsch** | **7.900 €** | regionales Vertriebs-/Vertrauens-/Recruiting-System | ≤ 16 Seiten, ~6.500 Wörter | 2 | Schutz L, 249 €/Mon. | **10.888 €** |
| **Sonderprojekt** | **ab 12.500 €** | Shop, Login, komplexe Buchung, Schnittstellen, Mehrmarken | individuell | individuell | mind. Schutz L | **ab 15.488 €** |

**Darstellung:** Platzhirsch ist **sichtbar die Empfehlung** (größte Fläche, Badge „Empfehlung"), Start/Wachstum kleiner ohne gleichstarke CTA. **Ein** Hauptbutton: `Bedarf prüfen lassen`. Keine `auswählen`-Buttons, keine Extra-Checkboxen.

**In jedem Paket enthalten:** Bedarfsprüfung + begründete Empfehlung, strategische Sitemap, individuelles Design im SARTU-Designsystem, KI-gestützte (menschlich geprüfte) Programmierung ohne WordPress, responsive + Barrierefreiheits-Basis + Performance, Website-Texte aus bestätigten Fakten, SEO-/GEO-Startsystem, Kontaktweg/Formular, technische Einbindung freigegebener Rechtstexte + Consent, Vorschau + gebündeltes Feedback + Korrekturrunden, Domainprüfung/-verbindung/Launch, Portalzugang.

**Platzhirsch zusätzlich bedarfsgerecht:** Team-/Karrierebereich, Projekt-/Referenz-/Neuigkeitenstruktur, stärkere lokale Struktur, **genau ein** Conversion-Modul (qualifiziertes Anfrageformular *oder* einfache Ein-Kalender-Buchung *oder* einfaches Bewerbungsformular).

**Bewusst NICHT im Erstangebot** (Scope-Schutz): Add-on-Liste, Extraseiten-Preise, SEO-Stufen, Änderungsminuten, Logo-Pakete, Express, Newsletter/Tracking als Häkchen. Ein Standardangebot endet **exakt** beim veröffentlichten Paketpreis. Neue Ziele nach Auftrag → **ein** konsolidiertes Folgeangebot mit Festpreis, keine Einzelpreisliste.

**„Rundum-Schutz" (fest zugeordnet, keine Kundenauswahl, keine Änderungsminuten):**

| Stufe | netto/Mon. | Inhalt |
|---|---:|---|
| **Schutz S** | 59 € | Managed Hosting DE/EU, SSL, tägl. externe Backups, 30 Tage Versionen, Uptime-/Sicherheitsmonitoring, technische Updates, Portal-Selbstpflege, Erstreaktion 2 Werktage |
| **Schutz M** | 129 € | alles aus S, 90 Tage Versionen, erweiterte Formular-/Deploymentprüfung, monatl. Technik-/Suchstatus, Erstreaktion 1 Werktag |
| **Schutz L** | 249 € | alles aus M, 180 Tage Versionen, engmaschiger SEO-/GEO-/Conversion-Technikcheck, priorisierte Störungsbearbeitung, Erstreaktion binnen 8 Geschäftsstunden |

Erstlaufzeit 12 Monate ab produktivem Betrieb, danach 30 Tage zum Monatsende kündbar, monatlich im Voraus. **Reaktionszeit ≠ Fertigstellungszeit.** Statt Änderungsminuten pflegt der Kunde definierte Geschäftsdaten selbst (s. Abschnitt 8).

### Wie der Rundum-Schutz kommuniziert wird (kritisch!)

Der häufigste Kommunikationsfehler wäre, die Website als „wartungsarm" zu bewerben – dann fragt der Kunde sofort: *„Warum zahle ich dann 59/129/249 € im Monat?"* Das entwertet die wichtigste Umsatzquelle.

- **Nicht sagen:** „Unsere Websites brauchen kaum Wartung." · „wartungsfrei" · „wartungsarm".
- **Sagen:** „**Keine Wartung für Sie.** SARTU betreibt Ihre Website laufend."
- **Kernunterschied:** Die Website ist wartungsarm **für den Kunden** – nicht wartungsfrei und nicht kostenlos zu betreiben.

**Der Schutz bezahlt:** Betrieb, Verantwortung, Verfügbarkeit, Hosting, SSL, Backups, Monitoring, technische Pflege, technische Suchgesundheit, **Formularprüfung**, Versionsstand, Portalzugang (inkl. Sichtbarkeit von SARTU-Rechnungs- und Zahlungsstatus) und Reaktionsbereitschaft.
*Klarstellung:* „Zahlungsprüfung" meint **den SARTU-Rechnungs-/Mollie-Status im Portal** – **nicht** Zahlungs- oder Shopfunktionen auf der Kundenwebsite. Solche Funktionen gibt es nur in Sonderprojekten und sind dort gesondert beauftragt.
**Der Schutz bezahlt nicht:** unbegrenzte Design-/Content-Flatrate, SEO-Redaktion, beliebige neue Seiten, neue Ziele, individuelle Sonderfunktionen.

**Website-Formulierung (empfohlen):**
> Ohne WordPress-Wartungsstress für Sie. SARTU betreibt Ihre Website laufend: Hosting, SSL, Backups, Monitoring, technische Suchgesundheit, Formularprüfung, Versionen und Portalzugang sind im Rundum-Schutz enthalten.

So kauft der Kunde **Entlastung und Verantwortung** – nicht „Wartungsaufwand".

---

## 5. Zahlungsmodell

| Paket | Staffelung |
|---|---|
| Start / Wachstum | 50 % bei Auftrag, 50 % nach Abnahme vor Onlinegang |
| Platzhirsch | 40 % Auftrag, 30 % nach Leitseiten-/Systemvorschau, 30 % nach Abnahme vor Onlinegang |
| Sonderprojekt | Standard 40/30/30, im Angebot ggf. abweichend |

- **Zahlungsziel 10 Kalendertage.** Produktionsslot **erst nach erster Zahlung** verbindlich. Alle Meilensteine vor dem Onlinegang bezahlt.
- **Schlusszahlung** ist an **Abnahme/Fertigstellung** gekoppelt, nicht an einen frei verschiebbaren Onlinegang.
- **Mollie** (Zahlungsdienstleister, **nicht** Buchhaltung): SARTU-System erzeugt Rechnung + Forderung; Kunde startet den Mollie-Checkout im Portal. **Zahlungswahrheit** = serverseitig authentifiziert abgerufener Status nach Webhook, **niemals** der Browser-Redirect. Webhooks idempotent, eindeutige Idempotency Keys, jede Zahlung gegen interne Rechnung/Betrag/Währung/Metadaten geprüft.
- **Schutz-Abo:** Beim ersten wiederkehrungsfähigen Bezahlvorgang bestätigt der Kunde das **Mandat** ausdrücklich; danach monatlicher Voraus-Einzug. Start des Schutzes = produktiver Betrieb (Sonderregel bei kundenverschuldeter Onlinegang-Verzögerung: spätestens 14 Kalendertage nach betriebsfertiger Bereitstellung, nach Hinweis – Formulierung anwaltlich mit AGB abstimmen).
- **Buchhaltung nicht selbst bauen:** Rechnungen deterministisch über lexoffice **oder** sevDesk (Entscheidung offen, s. Abschnitt 25); Mollie nur für Zahlungslinks/Abo. Rechnungszahlen dürfen nie von KI erzeugt werden.

### E-Rechnung (B2B-Pflichtthema, nicht optional)

Seit **01.01.2025** gelten für **inländische B2B-Umsätze** neue E-Rechnungsregeln mit Übergangsfristen ([BMF-FAQ](https://www.bundesfinanzministerium.de/Content/DE/FAQ/e-rechnung.html)). Da SARTU ausschließlich B2B verkauft, ist das kein Randthema:

- **Empfangen können ist sofort Pflicht:** SARTU muss strukturierte E-Rechnungen (XRechnung/ZUGFeRD nach EN 16931) **entgegennehmen und revisionssicher archivieren** können – auch wenn selbst noch anders ausgestellt wird.
- **Ausstellen:** je nach Umsatz und Übergangsfrist wird die strukturierte Ausstellung verpflichtend. Die Rechnungsstellung muss darauf vorbereitet sein.
- **Auswahlkriterium für lexoffice/sevDesk:** Das Tool wird **nur** gewählt, wenn es **XRechnung/ZUGFeRD/EN 16931** sowie GoBD-konforme Archivierung, Storno/Gutschrift, korrekte USt-Behandlung und einen sauberen Mollie-Abgleich beherrscht.
- **Verboten:** selbstgebaute PDFs als alleinige Buchhaltung; PDF allein ist **keine** E-Rechnung.
- Rechnungsarchiv, Aufbewahrungsfristen und Nummernkreise laufen im Buchhaltungstool, nicht im Portal. Das Portal zeigt nur Status und Zahlungslink.

---

## 6. Domain-, Hosting- und E-Mail-Regelung

**Grundsatz:** Der Kunde entscheidet den **Domainnamen** und bleibt **Domaininhaber**. SARTU entscheidet und verwaltet die **technische Infrastruktur** (Registrar, DNS, Deployment).

**Neue Domain:** Kunde nennt Wunschname oder bittet um Vorschläge → SARTU zeigt **max. 3** geprüfte, markennahe Vorschläge (bevorzugt `.de`) → Portal prüft Verfügbarkeit/Preis über **INWX** (Reseller-/JSON-RPC-API, hinter Provider-Adapter) → Kunde bestätigt genau einen Namen + Inhaberdaten → **letzte** Echtzeitprüfung → Registrierung **erst nach erster Zahlung** und mit **kundeneigenem Inhaberkontakt** (kein pauschaler SARTU-Registrant außer OT&E). Eine normale Domain bis **30 € netto/Jahr** ist bei SARTU-Verwaltung im Schutz enthalten; Premiumdomains/Sonderendungen ausgeschlossen (→ Alternativvorschläge).

**Vorhandene Domain:** Transfer bevorzugt, wenn ohne Betriebsrisiko möglich; sonst nur DNS anbinden. **Vor jeder Änderung** A/AAAA/CNAME/MX/SPF/DKIM/DMARC + Subdomains/Verifizierungsrecords dokumentieren (Snapshot + Rollbackplan). **Bestehende E-Mail darf durch den Launch nie ausfallen.**

**E-Mail-Postfächer** sind ein **eigener Drittanbieterdienst** (nicht Websitebetrieb). Vorhandene Postfächer werden erhalten. Bei Erstbedarf: **eine** Ja/Nein-Frage, dann Empfehlung **genau eines** Standardanbieters + Nennung der Fremdkosten; DNS-Einrichtung ist im Websiteprojekt enthalten. Kein Anbieterkarussell.

**Hosting (Kundenseiten):** statische Auslieferung (s. Abschnitt 10) über Managed Hosting in **DE/EU**. Der Kunde wählt kein Hosting.

**Kundenfragen (nur diese):** 1) Domain vorhanden? 2) Wenn ja: welche + wer hat Zugriff? 3) E-Mail mit dieser Domain? 4) Wenn neu: Wunschname oder Vorschläge? 5) Finalen Namen + Inhaberdaten bestätigen.

### Vertragsende, Zahlungsverzug und Domain-Übergabe (Pflichtregeln)

Diese Fälle **müssen** vor dem ersten Verkauf in Vertrag/AGB **und** im Portal geregelt sein – sonst entstehen später schwere Support- und Haftungsfälle:

| Fall | Regel (Vorschlag, anwaltlich zu prüfen) |
|---|---|
| **Kündigung des Schutzes** | Domain bleibt Eigentum des Kunden. SARTU stellt auf Anforderung **Auth-Code/AuthInfo** + dokumentierte DNS-Übergabe bereit (einmalig, innerhalb einer definierten Frist, z. B. 10 Werktage nach letzter erfüllter Zahlung). |
| **Wer zahlt Verlängerung nach Kündigung?** | Ab Vertragsende trägt der Kunde die Domainkosten selbst. SARTU verlängert **nicht** stillschweigend weiter; Kunde wird rechtzeitig auf den Transfer hingewiesen. |
| **Erinnerungen vor Ablauf** | Mindestens **drei** dokumentierte Hinweise (z. B. 60, 30 und 7 Tage vor Ablauf) im Portal **und** per E-Mail. Ablaufdatum ist im Portal dauerhaft sichtbar. |
| **Premiumdomain / fehlgeschlagener Transfer / Redemption** | Fremdkosten trägt der Kunde und werden **vorher** ausgewiesen. Redemption-/Wiederherstellungsgebühren sind nie in der 30-€-Pauschale enthalten. |
| **Kunde hat keinen Zugriff auf Altdomain/-E-Mail** | Kein Blindflug: Projekt läuft auf Vorschau-/Übergangsdomain weiter; Launch-Gate bleibt zu, bis Inhaberschaft/Zugriff nachgewiesen ist. Aufwand für Recherche/Recovery ist **nicht** im Festpreis enthalten. |
| **Betriebsende ohne Transfer** | Nach dokumentierter Frist und Hinweisen darf SARTU die Verwaltung beenden; Verantwortung geht an den Kunden über – **keine Löschung und kein Ablaufenlassen ohne vorherige Ankündigung**. |

#### Domain-Schutzregel (Zahlungsverzug ≠ Domainverlust)

Ein Domainablauf ist **nicht** wie „Support pausiert": Website, E-Mail, Marke, Google-Signale und Kundenvertrauen können gleichzeitig ausfallen. Weil SARTU technischer Verwalter ist, entsteht daraus Eskalations- und Haftungspotenzial. Deshalb wird die **Domainverlängerung ausdrücklich aus dem normalen Zahlungsverzugs-Prozess herausgelöst**:

1. **Domainverlust wird nie als Druckmittel eingesetzt.** Offene Rechnungen werden über den normalen Mahnweg verfolgt – nicht über die Domain.
2. **Bei laufendem Schutzvertrag** verlängert SARTU eine ablaufende Domain zur **Schadensvermeidung** und berechnet die Fremdkosten nach, statt sie auslaufen zu lassen.
3. **Vor jedem Ablauf** mindestens drei dokumentierte Hinweise (60/30/7 Tage) mit klarer Konsequenz.
4. **Bei Kündigung oder kündigungsnahem Status** wird **rechtzeitig vor Ablauf** aktiv Auth-Code und Transfer angeboten, damit der Kunde selbst übernehmen kann.
5. **Keine Verlängerung auf SARTU-Kosten** nach beendetem Vertrag – aber dann muss der Übergabeweg nachweislich offen gestanden haben.
6. Diese Regel gehört **wortgleich in Vertrag/AGB und Portal** und ist anwaltlich zu prüfen.

---

## 7. Kundenablauf (Ende zu Ende)

1. **Lumi-Bedarfsscheck** (5 Themen, ~3 Min., Preis vor Kontaktdaten).
2. **SARTU-Prüfung** (Standardfall Ziel 10–15 Min., höchstens eine gebündelte Rückfrage).
3. **Geprüftes Festpreisangebot** im Portal (Empfehlung, Sitemap, Scope, Ausschlüsse, Preis, Betrieb, Zahlungsplan, Terminrahmen; 14 Tage gültig).
4. **Annahme** (Rechnungsdaten, B2B-Bestätigung, Scope-Bestätigung, eindeutig kostenpflichtiger Button).
5. **Erste Zahlung** (Mollie) → danach Slot + Domainregistrierung.
6. **Adaptives Onboarding** (Aufgaben statt Fragebogen; bekannte Fakten übernommen, nur Lücken geschlossen; Domain/E-Mail geklärt).
7. **Produktionsspezifikation** einfrieren (Website-Datensatz, Seitenstruktur, versionierte Spec aus Briefing + Projektfakten + Designsystem).
8. **KI-gestützte Produktion** (assistiert/orchestriert je Ausbaustufe) + Pflicht-QA + Adminprüfung.
9. **Kundenvorschau** (versioniert) → gebündeltes Feedback → **Abnahme** → Schlussrate frei.
10. **Launch** (erst nach bezahlten Meilensteinen + aktiver Kundendomain; Monitoring, Search Console, Formulare, Rollback geprüft).
11. **Betrieb** (Schutz, strukturierte Selbstpflege, Suchgesundheit, **max. eine** begründete Wachstumsempfehlung).

**Lieferkorridore ab vollständigem Start:** Start 7–10 WT, Wachstum 10–15 WT, Platzhirsch 15–25 WT. Fehlt Mitwirkung > 14 Tage → Projekt nach Hinweis pausierbar; fertige Meilensteine bleiben fällig (rechtlich sauber regeln, keine erfundene Strafgebühr).

---

## 8. Konfigurator-Fragenlogik (Lumi)

Lumi ist **kein** Konfigurator und **keine** Paketwahl. Es sammelt vor den Kontaktdaten nur, was **Machbarkeit, Paket oder Preis** beeinflusst. Ziel: **8–12 leichte Eingaben**, ~3 Minuten. Keine Frage nach Paket, Seitenzahl, Sitemap, Farbe, Schrift, Designstil, SEO-Stufe, Hosting, Registrar, System.

**Fünf Themen (Kernfragen):**
1. **Unternehmen:** Was bieten Sie an? (Freitext 1–3 Sätze) · Wo tätig? (Ort/PLZ + optional Einzugsgebiet) · Bereits eine Website? (Ja+URL / Nein / unsicher)
2. **Ziel:** wichtigstes Ziel der neuen Website? (Anfragen / lokal gefunden werden / Recruiting / Vertrauen / Termine-Bewerbungen / anderes) · Wen erreichen? (Privatkunden / Unternehmen / Bewerber / mehrere / unklar)
3. **Umfangssignale (Mehrfach):** ein Hauptangebot / mehrere eigenständige Leistungen / mehrere Regionen-Standorte / regelmäßig offene Stellen / Projekte-Referenzen-News pflegen / nichts davon.
4. **Sonderrisiken (harte Gates):** normale Anfrage/Bewerbung · einfache Terminbuchung · **Shop/Zahlung** · **Login/geschützt** · **Schnittstelle** · **mehrere Sprachen/Marken** · **besondere Daten/formaler Nachweis** · nichts davon. („Nichts davon" nicht mit Sonderfunktion kombinierbar.)
5. **Domain & Termin:** Domainstatus (vorhanden/neu/unsicher) · fester Termin? · optionaler Freitext „auf keinen Fall übersehen".

**Deterministische Ampel (Regelwerk entscheidet, KI formuliert nur):**
- **Rot → Sonderprojekt:** Shop/Zahlung, Login/Rollen, individuelle Schnittstelle, komplexe Mehr-Ressourcen-Buchung, mehrere Marken/Domains, sensible Uploads, formaler Spezialaudit.
- **Orange → SARTU-Prüfung + kurzes Fachmodul:** mehrere Sprachen, mehrere Standorte einer Marke, unklare Buchung, > 1 Conversionpfad, knappe Frist, Freitext nennt Sonderfunktion ohne Auswahl.
- **Gelb → eine Rückfrage:** Widersprüche, „unklar" an paketentscheidender Stelle, Altwebsite unerreichbar/komplex, Domain/Rechte ungeklärt.
- **Standard:** Start / Wachstum / Platzhirsch nach notwendigem Umfang (nicht nach höherem Umsatz). **Platzhirsch** bei ≥ 2 starken Signalen (mehrere Leistungen, mehrere Regionen, Recruiting, Projekte, lokale Auffindbarkeit + mehrere Suchthemen, zentraler Conversionweg); bei besonders starkem Einzelsignal begründet möglich, **nie** nur weil es das Hauptprodukt ist.

**Ergebnis vor Kontaktdaten:** vorläufige Empfehlung + 2–4 kundenspezifische Gründe + Einmalpreis + Schutzpreis + Erstjahreswert + Hinweis auf persönliche Prüfung. Keine Paketwechsel-Buttons, keine Add-ons, keine SEO-Auswahl.

**Kurze Fachmodule vor Angebot** (nur das betroffene): Buchung · Shop/Zahlung · Login · Schnittstelle · Standorte/Marken · Sprache/Barrierefreiheit (je 3–4 Fragen).

**Was der Kunde beantwortet vs. was SARTU selbst recherchiert:**
- **Kunde (nur echte Geschäftsfakten):** Angebot/Leistungen/Kontakt/Öffnungszeiten, Zielgruppe/Einzugsgebiet, echte Belege/Team/Projekte, Bild-/Nutzungsrechte, Domaininhaber/E-Mail-Nutzung, freigegebene Rechtstexte, finale Fakten-/Design-/Textfreigabe.
- **SARTU recherchiert/entscheidet selbst:** Branche aus Beschreibung/Altwebsite ableiten, vorhandene Inhalte extrahieren, Relaunch-/Domainrisiken erkennen, Paketempfehlung + Sitemap, Suchintentionen + Informationshierarchie, Farbrollen/Typografie/Layout/Komponenten, Technik/Hosting/Registrar/Deployment/Monitoring, konkrete SEO-Metadaten + strukturierte Daten. **KI darf keine Fakten, Referenzen, Rechts- oder Fachaussagen erfinden.**

---

## 9. Portal-Vision und Funktionsarchitektur (Kundenportal)

Das Portal ist **kein CMS und kein Website-Baukasten**. Es verwaltet strukturierte Unternehmensdaten, Status, Freigaben und Produktionsaufträge. Es ist der **sichtbare USP** (geführter Prozess statt E-Mail-Chaos).

**Kundenportal-Navigation (8 Punkte):** Übersicht · Angebot & Vertrag · Projekt · Inhalte · Anfragen · Sichtbarkeit · Rechnungen · Hilfe. (Domain, Briefing, Vorschau, Launch erscheinen kontextuell im Projekt.)

**Funktionsbewertung** (Nutzen Kunde / Nutzen SARTU / Aufwand / Baukasten-Risiko / Start-Pflicht). Nur bewertete Kernfunktionen, keine Spielereien:

| Funktion | Kunde | SARTU | Aufwand | Baukasten-Risiko | Wann |
|---|---|---|---|---|---|
| Cockpit „genau ein nächster Schritt" | hoch | hoch | mittel | keins | **Start-Pflicht** |
| Angebot ansehen/annehmen (Scope, Preis) | hoch | hoch | mittel | keins | **Start-Pflicht** |
| Rechnungen + Mollie-Zahlung + Status | hoch | hoch | hoch | keins | **Start-Pflicht** |
| Adaptives Briefing + Upload + Rechte | hoch | hoch | hoch | keins | **Start-Pflicht** |
| Domainname/Inhaber/Status | mittel | hoch | mittel | keins | **Start-Pflicht** (Aktion manuell) |
| Vorschau + gebündeltes Feedback + Freigabe | hoch | hoch | hoch | keins | **Start-Pflicht** |
| Lead-Inbox (Formulare der Kundenseite) | hoch | mittel | mittel | keins | Stufe 1 |
| Selbstpflege: Öffnungs-/Feiertagszeiten | hoch | hoch | mittel | niedrig (typisiert) | Stufe 1 |
| Selbstpflege: Kontakt/Telefon/Anschrift/Links | hoch | mittel | niedrig | niedrig | Stufe 1 |
| Selbstpflege: Team/Stellen/Projekte/Referenzen | mittel | mittel | hoch | **mittel** | Stufe 2 |
| Bildtausch in vorhandenem Bildplatz (Rechte!) | mittel | mittel | mittel | **mittel** | Stufe 2 |
| Seite **deaktivieren/reaktivieren** (nie hart löschen) | mittel | hoch | mittel | niedrig | Stufe 1–2 |
| Basis-Statistik + Suchgesundheit (ohne Umsatzversprechen) | mittel | mittel | mittel | keins | Stufe 2 |
| „Nächster sinnvoller Schritt" / **eine** Wachstumsempfehlung | mittel | hoch | mittel | keins | Stufe 2 |
| Support/Störung/Änderungsanfrage | hoch | hoch | niedrig | keins | Stufe 1 |
| Saisonale Hinweise (z. B. Feiertagszeiten anpassen) | niedrig | mittel | niedrig | keins | Stufe 3 / später |

### Portal-Innovationen, die SARTU wirklich unterscheidbar machen

Diese Funktionen heben das Portal über ein normales Kundenportal, **ohne** WordPress-artig zu werden (kein freier Editor, alles typisiert + freigabepflichtig):

| Idee | Was es tut | Nutzen Kunde / SARTU | Wann |
|---|---|---|---|
| **Website-Aktualitätsradar** | Öffnungszeiten, Team, Leistungen, Preise, Jobs, Referenzen, Rechtstexte mit „zuletzt bestätigt am" | hoch / hoch (verhindert veraltete Fakten, erzeugt Pflegeanlässe) | Stufe 2 |
| **Proof Locker** | Kunde hinterlegt Meisterbrief, Zertifikate, echte Projektbilder, Freigaben, Pressestimmen | hoch / **sehr hoch** (Trust-Content ohne Nachfragen) | Stufe 1–2 |
| **Local Visibility Board** | Google-Unternehmensprofil, NAP-Konsistenz, Bewertungen, regionale Suchthemen, technische Suchgesundheit | hoch / hoch | Stufe 2 |
| **Saisonkalender** | Feiertage, Betriebsurlaub, Sommer-/Winterleistungen, Recruiting-Saison | mittel / mittel | Stufe 2–3 |
| **Change Preview** | zeigt vor Freigabe, **wo** eine Pflegeänderung wirkt: Seite, Footer, Schema, Kontaktkarte | hoch / hoch (verhindert Angst vor Änderungen) | Stufe 2 |
| **Ein-Folgeangebot-Logik** | sammelt Wünsche und macht daraus **genau eine** begründete Empfehlung statt Add-on-Liste | mittel / **hoch** (Scope-Schutz + Umsatz) | Stufe 2 |
| **Lead-Qualitätsansicht** | Anfragen nach Thema/Qualität/Quelle sortiert statt nur gezählt | hoch / mittel | Stufe 2 |
| **Referenzgenerator** | nach erfolgreichem Projekt: „Daraus können wir eine Referenzseite machen" inkl. Freigabeworkflow | mittel / **sehr hoch** (löst das Kaltstart-Referenzproblem dauerhaft) | Stufe 1–2 |
| **Competitor Watch light** | monatlicher Hinweis „3 Wettbewerber haben neue Stellen/Leistungen/Standortseiten" – kein aggressives Scraping | mittel / mittel | Stufe 3 |
| **Aussagen-Verfallsdatum** | „Diese Aussage wurde seit 180 Tagen nicht bestätigt" – als Qualitätscheck, nicht als Panikmache | mittel / hoch | Stufe 2–3 |

**Selbstpflege-Prinzip (ersetzt Änderungsminuten):** Der Kunde bearbeitet **typisierte Datensätze** (`BusinessHours`, `ContactPoint`, `Location`, `Person`, `JobPosting`, `ProjectReference`, `SocialLink`, `PagePublicationState`, `MediaReplacement`) – **nicht** Layout, Farben, Schriften, Komponenten, URLs, Navigation, SEO-Felder, Formulare, Code oder freien Text. Jede Änderung läuft über **Vorschau → Validierung → Version → Bearbeiter**. Öffnungszeit-Änderung aktualisiert nach Freigabe sichtbare Zeiten + Footer + `LocalBusiness`-Schema. Seiten werden **nie hart gelöscht** (raus aus Navigation/Sitemap, interner Redirect oder Archivstatus, reaktivierbar).

**Bewusste Nicht-Funktionen:** kein Drag-and-drop, keine freie Layout-/Farb-/Schriftwahl, keine Plugins/Themes/freien Integrationen, keine freie URL-/Navigationsbearbeitung, kein Quellcodezugriff, keine harte Seitenlöschung.

---

## 10. KI-/Automatisierungslogik

**Drei-Produkt-Architektur:** (1) SARTU-Vertriebswebsite, (2) SARTU-Control-Plane (Portal), (3) Kundenseiten (getrennte, versionierte Codeprojekte).

**Kundenseiten-Tech:** **static-first (Astro empfohlen)**, 1 Repository pro Kunde, gemeinsamer versionierter **SARTU-Starter** + versioniertes **Designsystem-Paket**. Strukturierte Inhalte statt freiem HTML. Formulare/Dynamik über eng begrenzte Portal-APIs. Jede Produktion reproduzierbar, testbar, **exportierbar, rückrollbar**. Der Kundenstand muss **auch nach Vertragsende baubar** sein (notwendige Komponenten eingefroren/vendort; Master-Designsystem/Generatoren/Prompts bleiben intern).

**Produktionspipeline (Zielbild, ab Ausbaustufe 2/3):**
1. Portal friert **versionierte Spezifikation** ein (`site-spec.json`, `business.json`, `services.json`, `proof.json`, `content-plan.json`, `brand.json`, `legal.json`, `seo.json`, `design-manifest.json`, `acceptance.json` – jeweils mit Schema-Version, Projekt-ID, Quelle, Freigabestatus).
2. Isolierter Worker legt Kundenrepo aus Starter + Designsystemversion an.
3. **Anbieteradapter** `WebsiteBuildJob` → **Codex `exec`** (primär) oder **Claude Code (headless)** (Vertrag nennt kein garantiertes Modell).
4. Agent erzeugt Code/Texte/Tests **nur** im Kundenrepo.
5. **Automatische QA-Gates** (14 Pflichtgates: Schema, Build, Code/Lint/Secret-Scan, Links, Formulare, Responsive-Screenshots 360/768/1280/1440, Visuell, Barrierefreiheit, Performance/CWV, SEO, strukturierte Daten, Inhalt/keine Platzhalter/keine verbotenen Garantien, Recht/Consent, Regression).
6. **Menschliche Pflichtprüfung** (passt es zum Unternehmen statt nur zum Template? Botschaft in Sekunden klar? Aussagen = bestätigte Quellen? Platzhirsch sichtbar hochwertiger? keine internen Notizen/Prompts veröffentlicht?).
7. Adminfreigabe → versionierte Kundenvorschau → Abnahme → **separater** Produktions-Launch → versioniert, rückrollbar.

**Harte Sicherheitsgrenzen:** kein Agentenlauf im öffentlichen Webrequest; ephemerer Container pro Job; Schreibzugriff nur auf das eine Kundenrepo + Artefaktordner; **keine** Mollie-/Registrar-/Portal-/Produktions-Zugangsdaten im Agentencontainer; Netzwerk standardmäßig gesperrt (Allowlist); kurzlebige Git-Credentials nur für den Job-Branch; Laufzeit-/Kosten-/Turn-Limit + Abbruch; **Kundenfreitext & externe Websites = nicht vertrauenswürdige Eingaben**. **Kritische Aktionen (Zahlung, Domainregistrierung, DNS, Produktion) führen nur autorisierte SARTU-Dienste/Menschen aus, nie der Agent.**

**Realismus-Hinweis (wichtig):** Vollautonome Website-Erzeugung aus Spec ist der **fragilste** Teil. In Stufe 0/1 gilt: **KI assistiert, Mensch baut/prüft** aus dem Designsystem. Erst wenn das Designsystem stabil, komponentenreich und getestet ist, lohnt echte Orchestrierung. Die internen Std-Obergrenzen (Start 16 h, Wachstum 32 h, Platzhirsch 50 h) sind nur mit starkem Designsystem haltbar.

### 10a. Arbeitsverteilung Codex ↔ Claude Code (verbindlich)

Die frühere Formulierung „Codex `exec` primär oder Claude Code headless" war zu dünn — sie beschreibt einen Adapter, aber keine Arbeitsteilung. Die beiden Werkzeuge sind **nicht austauschbar**, und zwei Werkzeuge, die dieselbe Datei final schreiben, erzeugen widersprüchliche Code- und Designentscheidungen.

> **Eiserne Regel: Pro Repository schreibt genau ein Werkzeug final.** Das jeweils andere liefert Entwurf, Review oder Gegencheck — nie parallel denselben Produktionsstand.

| Aufgabe | Führt | Zweite Stimme | Begründung |
|---|---|---|---|
| Strategie, Geschäftsmodell, Positionierung | **Claude Code** | Codex prüft auf Umsetzbarkeit | Bewertung, Abwägung, Gegenargumente |
| Marktanalyse, Wettbewerb, Preislogik | **Claude Code** | – | Recherche + kritische Einordnung |
| SEO-/GEO-Strategie, Keyword-/Cluster-Planung | **Claude Code** | Codex setzt technisch um | Strategie vor Implementierung |
| Designrichtung, Art-Direction, Designsystem-Entwurf | **Claude Code** | Codex baut Tokens/Komponenten | Alternativen und Bewertung |
| Copy und Textvarianten | **Claude Code** | Mensch gibt frei | Sprache, Tonalität, Einwandbehandlung |
| Konzeptkritik, Gegenreview, Risikoprüfung | **Claude Code** | – | ausdrücklich als zweite Stimme |
| **SARTU-Website (Frontend, Build)** | **Codex** | Claude Code reviewt Diff | Dateien, Repo, Tests, Screenshots |
| **Portal-Frontend** | **Codex** | Claude Code reviewt UX/Copy | dito |
| **Portal-Backend, Rollen, Audit** | **Codex** | Claude Code reviewt Sicherheitslogik | reproduzierbare Implementierung |
| **Mollie, Webhooks, Idempotenz** | **Codex** | Claude Code reviewt Fehlerfälle | Testbarkeit, echte Testkonten |
| **Rechnungen, E-Rechnung, Buchhaltungsanbindung** | **Codex** | Mensch prüft Zahlen | Zahlen dürfen nie halluziniert werden |
| **Domains, DNS, INWX-Adapter** | **Codex** | Mensch führt kritische Aktion aus | Live-Aktionen bleiben menschlich freigegeben |
| **QA-Gates, Tests, Screenshots** | **Codex** | – | lokal ausführbar, reproduzierbar |
| **Deployment, Rollback** | **Codex** | Mensch gibt frei | Produktion ist Freigabeschritt |
| **Kundenwebsite-Produktion** (ab Stufe 2) | **Codex** | Claude Code als Entwurfsquelle für Struktur/Text | ein Repo, ein schreibendes Werkzeug |

**Konsequenzen:**
- Claude Code headless bleibt ein **optionaler** Adapter, **kein gleichwertiger Produktionsstandard** — erst nach 2–3 realen Projekten und erfolgreichem Test in echten Kundenrepos.
- Der Produktionsadapter wird **nicht vor** diesen 2–3 Projekten automatisiert.
- **Im Kundenvertrag wird nie ein Modell oder Anbieter garantiert.** Verkauft wird das Ergebnis, nicht „gebaut mit X".
- Wechselt die Führung für ein Repo, wird das dokumentiert (Datum, Grund) — kein stiller Wechsel mitten im Projekt.

---

## 11. Adminportal

**Admin-Navigation (12 Punkte):** Cockpit · Anfragen · Angebote · Kunden · Projekte · Websites · Agentenjobs · Sichtbarkeit · Domains · Finanzen · Support · System.

**Kernmodule:** Leads/Lumi-Ergebnisse/Empfehlungsprüfung · Angebots-/Scope-Versionierung/Annahmen/Ausschlüsse · Kunden/Rollen/Einwilligungen/**Audit-Log** · Rechnungen/Mollie/Mandate/Mahnstatus/Schutz · Domains/Kontakte/Verfügbarkeit/Registrierung/Transfers/DNS/Erneuerung · Briefings/Dateien/Rechte/Faktenfreigaben · Projekte/Aufgaben/Fristen/Pausen/Korrekturen/Abnahmen · Repositories/Designsystemversionen/Vorschauen/Deployments/Rollbacks · Codex-/Claude-Jobs (Kosten/Logs/Diffs/QA/Freigaben) · **SEO-/GEO-Flottenzentrale** + vorbereitete Patches · Leads/Support/Störung · Kennzahlen (Anfrage/Abschluss/Produktionszeit/Marge/Support/Betrieb).

**Sicherheit:** rollenbasierte Zugriffe, **Admin-2FA**, Mandantentrennung (Filter nach `kunde_id` aus **Session**, nie aus Request), CSRF auf jedem POST, Rate-Limit auf Auth, gehashte Magic-Link-Tokens (15 Min, einmalig), Upload-Pfade als UUID, Audit-Log bei kritischen Statuswechseln, harte Löschung nur wo gesetzlich/betrieblich nötig.

---

## 12. Zentrales Datenmodell (grobe Skizze)

- **Identität:** `organizations`, `users`, `memberships`, `roles`, `consents`, `audit_events`
- **Vertrieb:** `leads`, `lumi_assessments`, `offer_recommendations`, `clarifications`, `offers`, `offer_versions`, `acceptances`
- **Finanzen:** `invoices`, `invoice_lines`, `payments`, `mandates`, `subscriptions`, `refunds`, `webhook_events`
- **Domain:** `domains`, `domain_contacts`, `domain_quotes`, `registrations`, `transfers`, `dns_snapshots`, `dns_change_sets`
- **Projekt/Inhalt:** `projects`, `brief_versions`, `tasks`, `project_records`, `assets`, `asset_rights`, `feedback_threads`, `approvals`, `content_records`, `page_states`
- **Website-Produktion:** `sites`, `repositories`, `site_versions`, `design_system_versions`, `agent_jobs`, `qa_runs`, `previews`, `deployments`, `rollbacks`
- **Betrieb/Wachstum:** `form_submissions`, `support_cases`, `uptime_events`, `search_properties`, `search_metrics`, `seo_issues`, `seo_patches`, `growth_recommendations`

> Hinweis: Vertriebs- und Wachstumsempfehlungen sind **getrennte Tabellen** (`offer_recommendations` vs. `growth_recommendations`) – gleiche Bezeichnung für zwei Fachobjekte wäre im Datenmodell eine Fehlerquelle.

Alle fachlich wichtigen Statuswechsel erzeugen ein **Audit-Ereignis**. Jobstatus: `queued → preparing → running → validating → admin_review → customer_preview → approved → deploying → live` (+ Fehlerzustände `needs_input`, `qa_failed`, `agent_failed`, `deployment_failed`, `rolled_back`, `cancelled`).

**Tech-Stack-Entscheidung (aufgelöst):** Control-Plane = **Node + PostgreSQL + Redis (Queue/Locks) + S3-kompatibler Objektspeicher DE/EU**. Kundenseiten = **static-first** (Astro o. gleichwertig) aus versioniertem Designsystem. Der **PHP-/Flat-File-Ansatz** (`lastenheft_webseite.md`) ist abgelöst; **Supabase** (Postgres+Auth+Storage, Frankfurt) ist als Stufe-0/1-Basis zulässig (s. Abschnitt 25).

> **Template-Altlasten bewusst verworfen:** Folex Lite, ScrewFast, AstroWind, Studio Admin, Tailwind-Studio, Lume und shadcn-`dashboard-01` waren **Recherche-/Prototypenstände, keine Vorgabe**. Die Design-/Basisentscheidung wird frei und neu getroffen – siehe `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`.

### Deployment-Realität: was per FTP geht und was nicht

Ein früher geäußerter Wunsch war „am Ende alles per FTP hochladen". Das muss klar getrennt werden, sonst entstehen unhaltbare Versprechen:

| Baustein | Deployment | Begründung |
|---|---|---|
| **SARTU-Marketingwebsite** | statisch baubar → **FTP/CDN möglich** | reines HTML/CSS/JS-Ergebnis |
| **Kundenseiten** | static-first, **aber** Formulare/Leads brauchen ein Backend (Portal-API) | statische Auslieferung + eng begrenzte API |
| **SARTU-Portal** | **kein FTP/Shared-Hosting** – braucht echte App-Umgebung: HTTPS, Backend, Datenbank, Sessions/Auth, Mollie-**Webhooks**, Worker/Queues, später Agentenjobs | Webhooks und Hintergrundprozesse sind auf reinem FTP-Webspace nicht sinnvoll betreibbar |

**Konsequenz:** Wird „nur FTP" zur harten Vorgabe, muss das Portal drastisch vereinfacht werden – und mehrere Versprechen (Mollie-Abo, Lead-Inbox, Vorschau/Freigabe-Workflow, Agentenjobs) fallen weg. Empfehlung: Website FTP-fähig halten, Portal auf einer echten App-Plattform betreiben.

---

## 13. Eigene SARTU-Website – ausgelagert (Autoritätsregel)

> **Für die eigene SARTU-Verkaufswebsite gilt ausschließlich `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`.**
> Dort stehen verbindlich: Sitemap, Startseiten-Dramaturgie, Seitenkonzepte, Leistungsseiten, Ratgeber-/Lexikon-Startumfang, Content-Cluster, Bild- und Screenshotliste, Designrichtungen, Website-Designsystem, Trust/FAQ und Abnahmekriterien.
>
> Dieses Masterkonzept regelt **nur noch**: Geschäftsmodell, Angebot/Preise, Zahlung, Domain/Hosting/E-Mail, Portal, KI-/Produktionslogik, Technik, Recht, Markteintritt und Ausbaustufen. Die früheren Website-Detailabschnitte wurden hier **entfernt**, weil zwei Dateien nicht dieselbe Sache regeln dürfen.

**Nur zur Orientierung (nicht maßgeblich):** Launch = Kernseiten (`/`, `/leistungen`, `/preise`, `/ablauf`, `/briefing`, `/ueber-uns`, `/kontakt`, Pflichtseiten) + **5** Leistungsseiten + **3** Ratgeber + **10–15** Lexikonbegriffe. Kommerzielle Hubs, Branchen-Hubs, Ortsseiten und ein Lexikonausbau auf 40–60 Begriffe sind **Stufe 2** – erst nach Search-Console-Daten.

---

## 14.–15. (verschoben) Startseite und Leistungsseiten

Siehe `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`, Abschnitte 4–6.

---

## 16. SEO-/GEO-Strategie (Produktleistung für Kundenwebsites)

> Dieser Abschnitt beschreibt, **was der Kunde kauft**. Die SEO-/GEO-Struktur der *eigenen* SARTU-Website steht in der Website-Datei, Abschnitt 9.

**Grundhaltung (belegt durch Google-Doku):** GEO ist **kein** magischer Zusatz und **kein** Spezial-Schema. Gute KI-Sichtbarkeit = Fortsetzung guter SEO: crawlbare, hilfreiche, konsistente, entitätsklare Inhalte. **Keine** Garantie auf Rankings/Anfragen/Umsatz/KI-Nennungen. `llms.txt` wird angelegt, aber **nicht** als Rankingfaktor beworben.

**SEO-/GEO-Startsystem (im Websitepreis, ab Launch):** Suchintention + Thema je Seite · Antwort-zuerst-Texte aus bestätigten Fakten · sprechende URLs (Bindestriche, keine Umlaute) · genau eine H1, saubere Überschriften · interne Links als echte Links · Title/Description/Canonical/OG/Robots · Breadcrumb + `BreadcrumbList` · `Organization`+`WebSite` global, `Service`/`FAQPage`/`Article`/`DefinedTerm` seitenweise (nur bei sichtbarer Entsprechung) · XML-Sitemap, robots.txt, 404, Redirect-Plan · echte NAP, `LocalBusiness` **nur** bei berechtigtem Standort · Performance (CWV: LCP < 2,5 s, INP < 200 ms, CLS < 0,1; AVIF/WebP + srcset, Hero nicht lazy + `fetchpriority=high`, self-hosted WOFF2 `font-display:swap`) · Bild-SEO · Search Console + Bing Webmaster + Sitemap einreichen, IndexNow optional.

**Laufender Schutz (in Schutz S/M/L):** technische Suchgesundheit – Erreichbarkeit, Crawlbarkeit, Sitemap, Links, Canonicals, Schema aus bestätigten Fakten, technische Regressionen. **Kein** stillschweigender Content-Auftrag.

**Späterer Sichtbarkeitsausbau (datenbasiert, ein Folgeangebot):** schwache Seiten anhand echter Suchanfragen verbessern, veraltete Aussagen aktualisieren, interne Verlinkung schärfen, belegbare neue Themen/Regionen aufbauen, Antworten auf echte Kundenfragen ergänzen. **Kein** SEO-Menü, keine Stufen, keine Minuten.

**SEO-/GEO-Flottenzentrale (Admin):** Datenquellen (eigener Crawler, Search Console API, Performance/Uptime, Portalfakten, Conversion-Events nach Einwilligung). Prüfgruppen `critical/warning/opportunity/information`. **Automatisch reparierbar** (deterministisch): Sitemap neu erzeugen, interne Links nach Deaktivierung anpassen, technische Canonical-/Robots-/Metadatenverletzungen gegen feste Regeln, strukturierte Daten aus bestätigten Fakten, defekte Bildableitungen. **Nur als Entwurf** (Freigabe nötig): neue/geänderte Texte, neue Orts-/Leistungs-/Ratgeberseiten, Aussagen zu Preis/Qualifikation/Gesundheit/Recht/Ergebnis, Wettbewerbsvergleiche.

**Ortsseiten** — für SARTU **und** für Kundenwebsites gilt ausschließlich das Indexierungs-Gate in §16a. Keine Doorway-Massenseiten.

### 16a. Programmatic Local SEO — was geht und was die Domain verbrennt

> **Geprüfte Idee:** „Für jeden Ort mit mehr als 5.000 Einwohnern eine eigene Local-Landingpage bauen." **Bewertung: nicht umsetzen.** Begründung unten.

**Warum das riskant ist (belegt):** Googles Spam-Richtlinien definieren *Doorway Abuse* wörtlich als „*Having multiple domain names or pages targeted at specific regions or cities that funnel users to one page*" — also exakt das beschriebene Muster. *Scaled Content Abuse* ist definiert als „*many pages are generated for the primary purpose of manipulating search rankings and not helping users*" und gilt ausdrücklich **auch dann, wenn KI die Seiten erzeugt**. Quelle: [Google Spam Policies](https://developers.google.com/search/docs/essentials/spam-policies), [Core Update & Spam Policies 03/2024](https://developers.google.com/search/blog/2024/03/core-update-spam-policies) (geprüft 25.07.2026).

**Verschärfend im konkreten Fall:** Webdesign ist keine ortsgebundene Leistung. Bei Dachdecker, Zahnarzt oder Restaurant rechtfertigt die physische Nähe eine Ortsseite. Bei einer remote arbeitenden Webagentur ist der lokale Mehrwert **erst zu beweisen** — genau deshalb steht eine solche Seite unter höherem Rechtfertigungsdruck, nicht unter geringerem.

**Warum „Google merkt das nicht" die falsche Annahme ist:** Bewertet werden Muster — Seitenähnlichkeit, Nutzwert, interne Struktur, Indexierungsverhalten. Es geht nicht um exakte Duplikate. Und **20 % Indexierungsquote ist kein Erfolg**: 80 % ignorierte Seiten belasten Crawlbudget, verwässern interne Linkkraft und drücken das Qualitätsurteil über die gesamte Domain.

#### Stattdessen: gestufte, datengetriebene Struktur

| Stufe | Umfang | Bedingung |
|---|---|---|
| 0 | *nichts* | Startregion ist **nicht** entschieden → keine Ortsseite, kein `LocalBusiness`, kein GBP |
| 1 | **1 Haupt-Ortsseite** `/webdesign-{startort}` + **1 Region-Hub** `/webdesign-region-{region}` | Standort entschieden, echte NAP-Daten vorhanden |
| 2 | **10–20 priorisierte Ortsseiten** | je einzeln durch das Gate unten |
| 3 | **Branche × Region** statt Kleinstadtseiten, z. B. `/website-handwerker-{region}` | erst nach Search-Console-/SEA-Daten |
| 4 | weiterer Ausbau | nur für Orte mit belegten Impressionen oder Leads |

**Priorisierung der 10–20 Orte** (nicht nach Einwohnerzahl): Hauptstandort · echte Nachbarstädte im Einzugsgebiet · wirtschaftlich relevante Orte mit passendem Betriebsbesatz · Orte mit belegtem Suchvolumen oder SEA-Signal · Orte, für die konkrete Beispiele, Referenzen oder echte lokale Recherche vorliegen.

**Programmatic ja — aber mit `noindex`-Stufe:** Weitere Orte dürfen als Entwurf generiert werden (`draft → noindex_preview → ready_for_review → indexable → retire_or_merge`). Sie gehen **nie automatisch** auf `index`.

#### Indexierungs-Gate (alle Punkte müssen erfüllt sein)

- [ ] eigene Suchintention, eigener Title und eigene H1 — **nicht** nur der getauschte Ortsname
- [ ] mindestens **5 echte lokale Abschnitte**, die auf keiner anderen Ortsseite so stehen
- [ ] konkreter Bezug zur lokalen Betriebs-/Branchenstruktur (welche Betriebe gibt es dort wirklich?)
- [ ] sinnvolle Nachbarorte/Einzugsgebiet benannt
- [ ] echte lokale FAQ (Fragen, die jemand aus diesem Ort tatsächlich stellt)
- [ ] interne Links vom Region-Hub, von Leistungsseiten und passenden Ratgebern
- [ ] **keine** erfundene Nähe, **keine** Fake-Referenz, **keine** behauptete lokale Erfahrung
- [ ] `LocalBusiness` **nur** bei berechtigtem Standort — sonst weglassen
- [ ] Prüffrage: *Wäre diese Seite für einen echten Interessenten aus diesem Ort nützlich, auch ohne Google?* Nein → nicht indexieren.

**Gilt gleichermaßen für Kundenwebsites.** Das Gate ist Teil des Produkts, nicht nur der eigenen Seite — es schützt Kunden vor Abstrafung und SARTU vor Haftungsdiskussionen.

### 16b. SEO-/GEO-Taktiken: was SARTU nutzt und was nicht

| Taktik | Einstufung | Regel |
|---|---|---|
| Suchintention je Seite, Antwort-zuerst-Aufbau | **Whitehat** | Standard in jedem Projekt |
| Saubere Technik: Canonicals, Sitemap, Robots, Statuscodes, Redirects | **Whitehat** | Standard |
| Strukturierte Daten passend zu sichtbaren Inhalten | **Whitehat** | Standard |
| Core Web Vitals / Performance | **Whitehat** | Standard — trägt zum Sucherfolg bei, ist aber kein alleiniger Rankinghebel |
| Interne Verlinkung mit beschreibenden Ankertexten | **Whitehat** | Standard |
| Konsistente Unternehmensdaten (NAP), gepflegtes Unternehmensprofil | **Whitehat** | Standard, sobald Standort feststeht |
| Echte Inhalte zu echten Kundenfragen (Ratgeber, Lexikon, FAQ) | **Whitehat** | Standard, kuratiert statt Masse |
| Digitale PR, echte Erwähnungen, Branchenverzeichnisse mit Prüfung | **Whitehat** | erlaubt, wenn redaktionell verdient |
| `llms.txt` anlegen | **Whitehat, aber wirkungslos** | anlegen ja — **nie** als Rankingfaktor bewerben |
| Viele Orts-/Varianten-Seiten „leicht abgewandelt" | **Greyhat → real riskant** | **verboten** (Doorway/Scaled Content, s. 16a) |
| Programmatic Pages ohne Qualitätsgate | **Greyhat → riskant** | nur mit `noindex`-Stufe und Freigabe |
| Gastbeiträge primär für Links, Linktausch, gekaufte Links | **Blackhat** | **verboten** (Link Spam) |
| Keyword-Dichte-Optimierung, Keyword-Stuffing | **Blackhat** | **verboten** |
| Cloaking, Text nur für Crawler, versteckter Text | **Blackhat** | **verboten** |
| Fake-Bewertungen, Fake-Erwähnungen, erfundene Referenzen | **Blackhat + wettbewerbsrechtlich riskant** | **verboten** |
| „GEO-Hacks", Spezial-Markup für KI-Antworten | **wirkungslos** | Google nennt **kein** Spezial-Schema für AI Features; gute SEO bleibt die Grundlage |
| Seiten für jede denkbare Suchvariante | **Blackhat-nah** | Google warnt ausdrücklich davor |

**Leitsatz:** Schnellere Sichtbarkeit wird über **Fokus** erkauft (wenige starke Seiten, klare Entitäten, echte Antworten), nicht über Menge. Jede Taktik, die nur funktioniert, solange sie unentdeckt bleibt, ist für ein Geschäftsmodell mit laufendem Betrieb ein Eigentor: Der Schaden trifft später die Kunden — und damit den wiederkehrenden Umsatz.

---

## 17.–19. (verschoben) Ratgeber, Lexikon, Content-Cluster, Bild-/Screenshotkonzept

Siehe `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`, Abschnitte 7 und 9.
**Korrigierter Startumfang: 3 Ratgeber und 10–15 Lexikonbegriffe.** Die früher genannten 40–60 Begriffe bzw. 3–6 Ratgeber gelten **nicht** mehr für den Launch, sondern sind Stufe 2.

**Weiterhin im Master gültig – Bildprinzip für Kundenwebsites:** echte Betriebs-, Team-, Projekt- und Produktbilder haben Vorrang; gezielt lizenzierte Bilder nur, wenn reale Motive fehlen; KI-Bilder nie als Dokumentation des Unternehmens. Keine austauschbaren Handschlag-/Laptop-/Callcenter-Stockbilder. Bildrechte und zulässige Verwendung werden **pro Datei im Portal bestätigt**.

---

## 20. Designprinzipien (Kundenwebsites)

> Die **Markenpalette, Typografie und Designrichtung der SARTU-Website** sind ausgelagert: `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`, Abschnitt 3 (drei bewertete Richtungen zur finalen Entscheidung) und Abschnitt 10 (Website-Designsystem).

Für die **Kundenwebsites** (das verkaufte Produkt) gilt das versionierte SARTU-Designsystem:

**Unveränderliche Grundlagen:** 4-/8-Pixel-Abstandslogik · stabile Container und responsive Raster · Radius 0–8 px · klare Fokus-/Hoverzustände · semantisches HTML · barrierearme Formulare und Navigation · Bildkomponenten mit festen Seitenverhältnissen und responsiven Quellen · begrenztes JavaScript-/Animationsbudget · keine überlappenden Texte, Layoutsprünge oder abgeschnittenen Bedienelemente.

**Variable Tokens (SARTU entscheidet, nicht der Kunde):** Markenfarben als **Rollen** statt Hexwerte in Komponenten · eine Hauptschrift, optional eine Akzentschrift · Inhaltsdichte `compact`/`balanced`/`editorial` · Formcharakter `precise`/`human`/`bold` · Bildverhältnisse und Inhaltsrhythmus passend zur Branche · Bewegungsintensität `none`/`subtle`/`expressive`.

**Varianten statt Einheitswebsite:** pro wichtiger Komponente wenige getestete Varianten (z. B. drei Hero-Kompositionen, drei Leistungsdarstellungen, zwei Navigationsmuster). Der Agent darf nur freigegebene Varianten kombinieren; neue Varianten durchlaufen Designsystem-Review, Dokumentation, Tests und Versionierung – sie werden **nie** heimlich in einem Kundenrepository erfunden.

---

## 21. Trust-Elemente

> Die Trust-/FAQ-/Einwandbehandlung der SARTU-Website steht in der Website-Datei, Abschnitt 8.

Geschäftsseitig verbindlich bleibt:

- **Keine** Fake-Referenzen, Fake-Bewertungen, Fake-Logos, Fake-Adressen, Fake-Teamfotos – auf SARTU- **und** Kundenwebsites.
- **Ehrliche Disclaimer:** keine Ranking-/KI-Nennungs-/Umsatzgarantie; „SARTU leistet keine Rechtsberatung"; „KI wird genutzt, Ergebnisse werden geprüft".
- **Echte Referenzen/Case Studies**, sobald 2–3 Projekte live sind – Bild-/Namensrechte **vor** Projektstart schriftlich klären (s. Abschnitt 23a).

**Ehrlichkeitsregel Solo/Team:** solange faktisch Ein-Personen-/Gründer-geführt, **nicht** „großes Team" behaupten. „Gründer-geführt, kleines Team, klare Verantwortung" ist glaubwürdiger und rechtlich sauberer als ein Fake-Teamfoto.

---

## 22. Rechtliche und vertragliche Punkte

**Vor Verkauf anwaltlich (IT-Recht) prüfen/erstellen lassen** – nicht selbst formulieren:
- **B2B/Netto:** Alle öffentlichen Preise „netto zzgl. gesetzlicher USt., ausschließlich für Unternehmer"; B2B-Bestätigung vor Annahme (PAngV-konform nur bei echtem B2B-Prozess). Privatkunden ausschließen.
- **Werkvertrag/Abnahme/Fälligkeit:** Prüffrist, Mängel vs. neue Wünsche, **Abnahmefiktion**, **Mitwirkungsverzug**, Projektpause – §§ 640/641 BGB, konkret vertraglich ausgestalten.
- **AGB + Leistungsbeschreibung** mit klaren Scope-Grenzen (Korrekturrunde-Definition, „eine Seite", „ein Änderungsvorgang", enthalten/nicht enthalten je Position).
- **AVV (Auftragsverarbeitung)** mit Kunde **und** mit Subunternehmern – insbesondere **KI-Anbieter (OpenAI/Anthropic)**, Hoster, Mollie, INWX: Datenkategorien, Speicherfristen, Training, Löschkonzept transparent.
- **KI-Datenverarbeitung** transparent, wo personenbezogene Daten betroffen sind; sensible Uploads nur in freigegebenem Prozess; Agenten erhalten keine Produktions-/Zahlungs-/Registrar-Schlüssel.
- **DSGVO Kundenseite:** Impressum (§ 5 DDG), Datenschutzerklärung, Cookie-Consent **nur** bei zustimmungspflichtigen Diensten (datensparsam bauen → oft kein Banner nötig), Kontaktformular-Einwilligung, **keine** pauschale „rechtssicher/DSGVO-konform"-Garantie.
- **Rechtstexte:** technisch eingebunden, **nicht** rechtlich erstellt (RDG-Risiko → Wording „automatisiert generiert, keine Rechtsberatung"; besser: Kunde bringt Kanzlei-/Generator-Texte).
- **Keine Garantie** auf Rankings, Anfragen, Umsätze, KI-Nennungen, vollständige Rechtskonformität. **Zufriedenheits-/Geld-zurück-Garantie** aus Altmaterial **nicht** übernehmen, solange nicht sauber formuliert (EuGH C-133/22: auch Zufriedenheitsgarantie = gewerbliche Garantie mit Pflichtangaben).
- **BFSG (seit 28.06.2025):** reine B2B-Firmen-/Visitenkarten-Websites und Kleinstunternehmen (Dienstleistungen: < 10 MA und ≤ 2 Mio. € Umsatz) sind i. d. R. **nicht** verpflichtet; sobald Shop/Buchung/Online-Vertragsschluss (B2C) dabei ist, **greift** WCAG 2.1 AA. → Barrierefreiheits-**Basis** immer bauen (Qualität), BFSG-Pflicht **fallweise** prüfen, **nicht** pauschal als „Pflicht für alle" verkaufen. (Rechtsstand vor Verkauf verifizieren.)
- **§ 312k BGB Kündigungsbutton** betrifft Verbraucher – bei reinem B2B-Abo grundsätzlich entschärft, dennoch faire Kündigung/Laufzeit sauber regeln.
- **Rechte/Export:** nach vollständiger Zahlung Nutzungsrechte am konkreten Website-Stand + SARTU-Texten + kundenspezifischem Design; Domaininhaber = Kunde; dokumentiert **baubarer** Export ohne Abhängigkeit vom privaten SARTU-Master; **Exportweg vor erstem Verkauf praktisch testen** (sonst nicht mit „problemlosem Umzug" werben).

**Scope-Creep-Verhinderung:** Empfehlung + Sitemap stehen vor Auftrag fest; Standardpreis wird nicht mit „notwendigen Extras" aufgeweicht; Feedback wird gebündelt (parallele E-Mail/Telefon/Messenger zählen nicht als eigene Kanäle); neue Ziele werden getrennt vom Mangel behandelt (ein Folge-Festpreisprojekt); Selbstpflege ersetzt Änderungsminuten; Agentenjobs haben Kosten-/Zeit-/Werkzeuggrenzen.

---

## 23. MVP vs. spätere Ausbaustufen

> Kernprinzip: **Nachfrage und Lieferfähigkeit zuerst beweisen, Automatisierung zuletzt.** Das kanonische „alles vor Marktstart" wird als **Zielbild** beibehalten, aber in liefernde Stufen zerlegt.

**Stufe 0 – Sichtbares Portal + manuell liefern & Referenzen erzeugen (jetzt, Wochen, nicht Monate):**
- Öffentliche Website (Kernseiten + **5** Leistungsseiten + 3 Ratgeber + 10–15 Lexikonbegriffe) auf statischem Stack, launchfähig.
- Produktion **manuell + KI-assistiert** (Mensch baut aus Designsystem). Domain/DNS via INWX **manuell**. Buchhaltung via lexoffice/sevDesk.
- **Ziel: 2–3 echte Referenzkunden** live → echte Case Studies + echte Portal-Screens (die die Website ohnehin braucht).

**Stufe-0-Portal – verbindlicher Sichtbarkeitsumfang (nicht verhandelbar, weil USP):**

| Muss sichtbar/klickbar sein | Mechanik dahinter darf sein |
|---|---|
| Login (oder mind. geschützter, nicht ratbarer Projektlink) | einfache Auth, manuell angelegte Konten |
| **Cockpit mit genau einem nächsten Schritt** | Status manuell gesetzt |
| Angebot mit Scope, Preis, Zahlungsplan + digitale **Annahme** | PDF/Ansicht + protokollierte Zustimmung |
| Rechnung + Zahlungsstatus mit **Mollie-Zahlungslink** | Link manuell erzeugt, kein Abo-Automatismus |
| Material-/Faktenaufgaben (Aufgabenliste + Upload) | Aufgaben manuell aus Vorlage erzeugt |
| **Vorschau-Link + gebündeltes Feedback** | Preview manuell deployed |
| **Freigabe/Abnahme** mit Zeitstempel | manuell bestätigt, aber protokolliert |
| Domain-/E-Mail-Status | manuell gepflegter Statuswert |
| **Mindestens eine echte Pflegefunktion nach Launch** (Öffnungszeiten *oder* Kontaktdaten) | Änderung löst manuellen Rebuild aus |

**Ausdrücklich NICHT in Stufe 0:** automatische Domainregistrierung, Mollie-Abo-Automatik, KI-Agenten-Orchestrierung, SEO-Flotte, Rollback-Automation, automatische Builds aus dem Portal.

**Regel für Screenshots:** Portal-Screens für die Website stammen aus **dieser echten UI** – niemals aus gezeichneten Fake-Dashboards. Bis ein echtes Kundenprojekt gezeigt werden darf, werden sie als „Musteransicht" gekennzeichnet.

**Stufe 1 – Portal härten:** Angebot/Annahme im Portal, **Mollie-Abo/Mandat** für Schutz (E2E getestet), adaptives Onboarding, Lead-Inbox, strukturierte Selbstpflege (Öffnungszeiten/Kontakt/Seitenstatus), Support, Audit-Log, Admin-2FA, Mandantentrennung. Migration/Konsolidierung auf den Ziel-Stack (Node/PG).

**Stufe 2 – Produktion teilautomatisieren:** versionierte Spec → **assistierter** Build → automatische QA-Gates → Adminfreigabe → versionierte Vorschau/Deployment/Rollback. Selbstpflege Team/Stellen/Projekte. SEO-/GEO-Flotte (technische Checks + Patch-Entwürfe). INWX-Lifecycle im Portal.

**Stufe 3 – Skalieren:** echter Anbieteradapter (Codex/Claude headless orchestriert), programmatische Ortsseiten mit noindex-Stage + Freigabegate, Branchen-Hubs, SEO-Ausbau nach Search-Console-Daten, weitere Regionen.

**Nie Teil des Marktstarts:** autonome Werbekampagnen, freie Kundenseiten-Erstellung, Wettbewerber-Scraping ohne Zweck, Umsatzprognosen, Social-Redaktionssystem, Plugin-Marktplatz.

---

## 23a. Markteintritt, Nachfrage und Kaltstart-Pricing

> Diese Lücke war der schwerwiegendste Mangel des bisherigen Konzepts: Es beschreibt lückenlos, wie eine Anfrage **verarbeitet** wird – aber nicht, wie sie **entsteht**.

### A. Startregion statt „deutschlandweit"

> ### ⛔ BLOCKIERENDE ENTSCHEIDUNG: Startregion ist **nicht** entschieden
>
> Frühere Unterlagen nennen Dresden/Sachsen, andere Entwürfe einen abweichenden Standort. **Solange der echte Unternehmensstandort/Startmarkt nicht bestätigt ist, gilt keine Region als gesetzt.**
>
> **Blockiert, bis entschieden ist** (nichts davon vorher bauen):
> - Ortsseiten (`/webdesign-{ort}`) und regionale Hubs
> - `LocalBusiness`-Schema
> - Google-Unternehmensprofil
> - lokale SEA-Kampagnen und lokale Keyword-Struktur
> - NAP-Daten in Impressum/Footer
> - lokale Referenzkunden-Ansprache und Website-Texte wie „Webdesign {Ort}"
>
> **Es gibt keinen Dresden-/Sachsen-Default.** Alle früheren Ortsnennungen in den Quelldateien sind bis zur Bestätigung als **Platzhalter** zu behandeln und aus Texten fernzuhalten.

„Deutschlandweit" ist keine Startstrategie, sondern eine diffuse Fläche. **Eine** Kernregion wählen (welche, ist offen – s. o.), dort lokal sichtbar und referenzierbar werden, erst danach ausweiten. Alle Ortsseiten, das Google-Unternehmensprofil und der Outreach folgen dann dieser einen Region.

**Google-Unternehmensprofil (GBP):** nur anlegen, wenn die Voraussetzungen **regelkonform** erfüllt sind. Bei Betrieben ohne Kundenverkehr am Standort ist ein **Service-Area-Business** korrekt – dann muss die Adresse laut Google-Richtlinie **verborgen** werden ([Google-Richtlinie](https://support.google.com/business/answer/3038177)). Keine Fake-/Briefkastenadresse – das riskiert Profilsperrung und widerspricht der eigenen Anti-Fake-Regel.

### B. Pilot-Outreach (die ersten Kunden kommen nicht über SEO)

SEO und Content wirken erst nach Monaten. Die ersten 2–3 Kunden kommen über **Direktansprache**:

1. **Zielkundenliste: 30–50 konkrete Betriebe** in der Startregion (Handwerk, Praxen, Kanzleien, lokale Dienstleister) mit sichtbar schwacher/veralteter/nicht mobiler Website.
2. Pro Kontakt ein **konkreter Aufhänger** (nicht „Ich mache Websites"), z. B.: veraltete Inhalte, fehlende mobile Darstellung, kein Impressum/Datenschutz, langsame Ladezeit, kein Google-Profil.
3. **Kanäle – verbindliche Reihenfolge nach Rechtsrisiko** (Werberecht in DE ist auch im B2B streng; vgl. [IHK Stuttgart](https://www.ihk.de/stuttgart/fuer-unternehmen/recht-und-steuern/wettbewerbsrecht/richtig-werben/was-ist-erlaubt-684868), [IHK München](https://www.ihk-muenchen.de/ratgeber/recht/werbung-fairer-wettbewerb/marketing-per-email-telefon-brief-etc/)):
   1. **Netzwerk und Empfehlungen** (risikoärmster und wirksamster Kanal)
   2. **persönliche Kontakte** / bestehende Geschäftsbeziehungen
   3. **Google Ads / eingehende Suchanfragen** (Interessent meldet sich selbst)
   4. **postalische Anschreiben** (Brief ist deutlich risikoärmer als E-Mail/Telefon)
   5. **LinkedIn/Xing sehr zurückhaltend** – individuell, **nie** automatisiert oder in Serie
   6. **Telefon nur bei konkretem sachlichem Anlass** und dokumentiertem Geschäftsbezug (mutmaßliche Einwilligung) – **kein** Streuanruf
   7. **Keine kalten Massen-E-Mails.** B2B-Werbe-E-Mail ohne Einwilligung ist in Deutschland grundsätzlich unzulässig und abmahnfähig.
   > Die Zielkundenliste ist eine **Rechercheliste für passende Ansprache**, kein Verteiler für Massenwerbung.
4. **Angebots-Skript für Gründerkunden:** Ausgangslage benennen → SARTU-Ablauf in 3 Sätzen → Festpreis + Erstjahr → Pilotkondition + Gegenleistung → nächster Schritt (Bedarfsscheck).

### C. Kaltstart-Pricing: Preise oben halten, Pilotslots verdeckt

**Regel: Öffentliche Preise NICHT senken.** Ein Rückfall auf alte Niedrigpreise würde SARTU dauerhaft billig ankern und einen späteren Preissprung fast unmöglich machen (Interessenten vergleichen dich gegen dich selbst).

- **Öffentlich:** 1.490 / 3.900 / 7.900 / ab 12.500 € netto – unverändert.
- **Verdeckt im Direktvertrieb:** 2–3 **„Gründer-/Referenzslots"**, nicht prominent auf der Website.
- **Rabatt nur gegen echte Gegenleistung:** Case Study mit Namen, Testimonial, Screenshot-/Bildfreigabe, Google-Bewertung, zugesagte schnelle Mitwirkung.
- **Beispiel Platzhirsch:** öffentlich 7.900 € → Pilot **5.900–6.500 €** *oder* – besser – **7.900 € mit Zusatzwert** (z. B. zusätzliche Referenzseite, verlängerte Erstbetreuung) statt sichtbarem Rabatt. Zusatzwert schützt den Preisanker stärker als ein Nachlass.
- **Schriftlich festhalten:** Pilotkondition ist einmalig und an die Gegenleistung gebunden (sonst Erwartung auf Dauerrabatt).

### D. Case-Study-Template (ab dem ersten Projekt mitdenken)

Jedes Pilotprojekt erzeugt eine Referenzseite nach festem Muster: **Ausgangslage** → **Ziel** → **Ablauf im Portal** → **Ergebnis** (konkret, ohne erfundene Zahlen) → **echter Screenshot** → **Kundenzitat** → CTA. Bild-/Namensrechte werden **vor** Projektstart schriftlich geklärt.

### E. Bezahlte Sichtbarkeit: klein testen, hart messen

- **SEA-Test** auf enge, kaufnahe Begriffe in der Startregion („website erstellen lassen {Ort}", „webdesign {Ort}", „firmenwebsite {Branche}").
- **Definiertes Testbudget** und feste Abbruchkriterien **vorher** festlegen (z. B. Kosten pro qualifizierter Anfrage; Abbruch, wenn nach X Anfragen keine in ein Angebot mündet).
- Erfolg wird an **qualifizierten Anfragen** gemessen, nicht an Klicks.

### F. Kennzahlen für den Markteintritt

Angesprochene Betriebe → Antwortquote → gestartete Bedarfsschecks → versendete Angebote → **Annahmequote** → Median-Produktionsstunden → Anzahl aktiver Schutzverträge (MRR). Diese sechs Zahlen entscheiden, ob skaliert oder nachjustiert wird.

---

## 24. Konkrete nächste Umsetzungsschritte

**A. Fundament klären (diese Woche):**
1. **Einen** Preis-/Scope-Stand als Single Source of Truth festlegen (`pricing.json`/`prices.js`) und `sartupaketepreise.md` + `sartulastenheftwebsite.md` in `konzepte/_archiv/` verschieben (als **veraltet** markieren) – gegen Wiederverwendung.
2. Stack-Entscheidung dokumentieren: Website **static-first**; **Portal: echte App-Umgebung, Framework final offen** (shadcn/ui-Komponenten optional als Baustein – **kein** `dashboard-01`-Template als Vorgabe); Control-Plane **Node/PostgreSQL**; Umgang mit dem Supabase-Prototyp entscheiden (behalten als PG-Backend vs. migrieren – s. 25).
3. **Eine** Palette + Ansprache („Sie") + Logo-Favorit fixieren; verbotene Wörter-/Anti-KI-Regeln als Lint/QA-Check.

**B. Website launchen (2–4 Wochen):**
4. **Designrichtung final wählen** (3 Varianten in `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`), dann Basis aufsetzen: eigenes Layout **oder** neu begründete Template-Basis; globales Layout + Design-Tokens + Komponenten.
5. Kernseiten + **5** Leistungsseiten (GEO-Template) + **3** Ratgeber + **10–15** Lexikonbegriffe bauen; **echte** NAP/Impressum (nach Kanzlei), sitemap/robots/llms.txt/OG, Search Console + Bing.
6. Portal-Screens als **Musteransicht** produzieren – aus der **echten** Stufe-0-UI, nie als gezeichnetes Fake-Dashboard.
7. ENDKONTROLLE-Profil **SARTU-PUBLIC** vollständig grün (keine Add-on-/Minuten-/Alt-Preis-Reste, keine Privatkunden-Formulierungen, kein „wartungsarm").

**C. Verkaufen & liefern (parallel/danach):**
8. **Stufe-0-Portal live** im verbindlichen Sichtbarkeitsumfang (Abschnitt 23): Login/geschützter Zugang, Cockpit mit einem nächsten Schritt, Angebot + Annahme, Rechnung + Mollie-Link, Aufgaben/Upload, Vorschau + Feedback, Freigabe, Domainstatus, **eine** echte Pflegefunktion.
9. **Markteintritt aktiv starten** (Abschnitt 23a): Startregion fixieren, GBP regelkonform anlegen, Zielkundenliste 30–50 Betriebe, Pilot-Outreach, SEA-Test mit Abbruchkriterien.
10. 2–3 **Referenzkunden mit Case-Study-Rechten** (Pilotkondition gegen Gegenleistung) durch den vollen Prozess führen → Case Studies + echte Screens.

**D. Vor „echten" Standardkunden (Zielbild-Gates):** Mollie E2E (Zahlung/Mandat/Wiederholung/Fehlschlag/Erstattung), INWX OT&E (Registrierung/Transfer/DNS-Snapshot/Übergabe), Export + Rollback praktisch getestet, AGB/AVV/Datenschutz/KI-Verarbeitung anwaltlich, ein Musterkunde Ende-zu-Ende.

---

## 25. Offene Entscheidungen (nur die wirklich nötigen)

1. **Supabase-Prototyp behalten oder migrieren?** Der Juni-Stand (Supabase Frankfurt: Auth/PostgreSQL/Storage, RLS, live getestet) erfüllt „PostgreSQL + Identität + Storage in DE/EU" bereits. **Empfehlung:** für Stufe 0/1 **behalten** (schneller live, Sicherheit aus RLS), Ziel-Node-Control-Plane erst ab Stufe 2, wenn Queues/Worker/Agentenjobs wirklich gebraucht werden. **Nicht** parallel zwei Portale pflegen.
2. **Buchhaltung: lexoffice oder sevDesk?** (API-Anbindung, GoBD/E-Rechnung). Kaufmännische Entscheidung, vor Stufe 1.
3. **Typografie final:** reine Grotesk (Inter/Instrument Sans) vs. Grotesk + dezente editorial Serif für H1. Empfehlung: mit Grotesk starten, Serif optional testen.
4. **⛔ Startregion & echte NAP (BLOCKIEREND):** Welcher Standort/Startmarkt gilt wirklich? Öffentliche Geschäftsanschrift vorhanden (für `LocalBusiness`/Impressum/GBP) oder nur Kontaktanschrift (→ Service-Area-Business mit verborgener Adresse)? **Ohne diese Entscheidung dürfen Ortsseiten, `LocalBusiness`, GBP, lokale SEA und NAP-Texte nicht gebaut werden** (s. Abschnitt 23a).
5. **Solo vs. kleines Team – ehrliche Selbstdarstellung** und daraus abgeleitete **Kapazität/Projekte-pro-Monat** (bestimmt, ob der Portal-Vollausbau realistisch neben der Produktion läuft oder Hilfe/Outsourcing braucht).
6. **AGB/Garantie:** ob überhaupt eine (sauber formulierte) Zufriedenheitszusage als Verkaufsargument gewünscht ist – sonst weglassen.
7. **Designrichtung final:** eine der drei Varianten aus `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md` – oder eigenes Layout vs. neu recherchierte Template-Basis.
8. **Pilotkonditionen:** Werden 2–3 verdeckte Referenzslots angeboten – als Rabatt (5.900–6.500 € statt 7.900 €) oder als Zusatzwert zum vollen Preis?
9. **Portal-Betriebsumgebung:** Supabase/Vercel für Stufe 0/1 zulässig, oder muss es von Anfang an auf eigenem Server laufen? (Bestimmt Tempo **und** ob „nur FTP" endgültig ausgeschlossen ist.)
10. **Eigenes finales Website-Lastenheft** vor dem Bau erstellen (empfohlen: ja – `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md` ist die Grundlage dafür).

---

*Ende Masterkonzept. Die kritische Bewertung, Marktanalyse mit Quellen und Herleitung dieser Entscheidungen steht in `CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md`.*
