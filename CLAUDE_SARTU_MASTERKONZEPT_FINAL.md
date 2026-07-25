# SARTU – Masterkonzept (final, umsetzbar)

**Erstellt von:** Claude (Opus) · **Stand:** 24.07.2026
**Grundlage:** alle Konzeptdateien in `konzepte/` (Wahrheitsquelle: `GESCHAEFTSMODELL.md`, konkretisiert durch `SARTU_ANGEBOT_PORTAL_DETAILKONZEPT.md`, `SARTU_KONTAKTLOSER_VERTRIEB_LUMI_PORTAL.md`, `SARTU_DESIGNSYSTEM_PORTAL_ARCHITEKTUR.md`, `SARTU_WEBSEITENKONZEPT_FINAL_SEO_GEO.md`).

> Dieses Dokument ist die konsolidierte, widerspruchsbereinigte Bauvorlage für Website **und** Portal. Wo die Quelldateien sich widersprechen (drei Preisstände, drei Tech-Stacks, mehrere Paletten), trifft dieses Dokument eine Entscheidung und begründet sie. Die kritische Herleitung steht in `CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md`.

---

## 0. Wichtigste Korrektur vorab (bitte zuerst lesen)

Das kanonische SARTU-Modell ist **positionierungsseitig stark** und marktfähig. Die größte Gefahr ist **nicht** die Preis- oder Angebotslogik, sondern der Anspruch, den **kompletten Kundenbereich mit voller Automatisierung** (Lumi, Angebote, Mollie-Abo, INWX-Domainlebenszyklus, KI-Produktions-Orchestrierung, QA-Gates, Deployments, Rollback, SEO-/GEO-Flotte, Admin-Finanzen) **vollständig vor dem ersten Standardverkauf** zu bauen.

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
- **Kernunterschied:** Der Aufwand verschwindet **für den Kunden** – er verschwindet nicht, sondern liegt bei SARTU. Genau dafür ist die monatliche Pauschale da.

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
| **Geführte Einführung beim ersten Login** (§9a) | hoch | **hoch** (spart Support = Marge) | niedrig | keins | **Start-Pflicht** (Stufe 0: ein Bildschirm) |
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

## 9a. Erstkontakt im Portal — geführte Einführung

**Warum das kein Nice-to-have ist:** Die Zielgruppe ist nicht technikaffin und loggt sich selten ein. Ohne Einführung entstehen genau die Rückfragen („Wo ist mein Passwort?", „Muss ich die Seite jetzt selbst bauen?", „Was soll ich hier machen?"), die Supportzeit kosten — und Supportzeit ist im Festpreismodell direkt Marge. **Die Einführung ist Margenschutz, nicht Kosmetik.**

### 9a.1 Zwei Momente, nicht einer

| Moment | Situation | Was erklärt werden muss |
|---|---|---|
| **A — Erster Login** | Kunde sieht zum ersten Mal das Angebot und soll es annehmen und bezahlen | Wo bin ich? Was tue ich hier? Was muss ich *nicht* tun? Wie melde ich mich wieder an? |
| **B — Nach der ersten Zahlung** | Aufgabenbereich wird freigeschaltet, das Portal verändert seinen Charakter | Was steht jetzt an, wie lange dauert es, was passiert mit meinen Angaben? |

Moment A ist der wichtigere: Dort entscheidet sich ein Kauf. Reibung an dieser Stelle kostet Aufträge.

### 9a.2 Willkommensstrecke (Moment A) — drei Bildschirme, fertige Texte

Erscheint **einmalig** nach dem ersten Login, vor dem Cockpit. Überspringbar, jederzeit erneut aufrufbar.

**Bildschirm 1 — Wo Sie hier sind**
> ### Willkommen bei SARTU, {Vorname}.
> Das ist Ihr Projektportal. Hier läuft alles zu Ihrer Website an einem Ort: Angebot, Zahlung, offene Aufgaben, Vorschau und später kleine Änderungen.
> Keine E-Mail-Suche, keine verlorenen Anhänge, kein Rätselraten, wie weit das Projekt ist.

Buttons: `Weiter` · Textlink `Überspringen`

**Bildschirm 2 — Was Sie hier tun (und was nicht)**
Zwei Spalten nebeneinander (mobil untereinander):

| **Das machen Sie hier** | **Das müssen Sie nicht** |
|---|---|
| Angebot ansehen und annehmen | Technik verstehen |
| Rechnungen bezahlen | Seiten selbst bauen |
| Fragen zu Ihrem Betrieb beantworten | Webtexte schreiben |
| Bilder und Unterlagen hochladen | Wissen, wie viele Seiten Sie brauchen |
| Vorschau ansehen und freigeben | Irgendetwas installieren |
| Später Öffnungszeiten ändern | Sich um Updates oder Sicherheit kümmern |

> Struktur, Design, Technik und die Suchmaschinen-Grundlage übernehmen wir. Sie liefern die Fakten aus Ihrem Betrieb — den Rest machen wir.

Buttons: `Weiter` · `Zurück`

**Bildschirm 3 — So geht es weiter**
> ### Sie sehen immer genau einen nächsten Schritt.
> Oben im Portal steht, was gerade von Ihnen gebraucht wird. Mehr müssen Sie nicht im Blick behalten — wir melden uns, wenn etwas ansteht.
>
> **Anmelden ohne Passwort.** Sie bekommen jedes Mal einen Link per E-Mail. Es gibt kein Passwort, das verloren gehen kann.
>
> **Wenn etwas unklar ist**, nutzen Sie „Hilfe". Wir antworten schriftlich — meist am selben oder nächsten Werktag.

Button: `Portal öffnen`

**Der Hinweis zum passwortlosen Login ist Pflicht.** Kunden erwarten ein Passwort; ohne Erklärung entsteht der Eindruck, etwas sei kaputt oder unsicher.

### 9a.3 Kurzeinführung nach der ersten Zahlung (Moment B)

Kein zweiter Rundgang — **ein** Hinweisfeld über der neuen Aufgabenliste:

> ### Ihre Aufgaben stehen bereit.
> Wir haben vorausgefüllt, was wir schon über Ihr Unternehmen wissen. Sie bestätigen es oder korrigieren es — das ist meist in **{15–25} Minuten** erledigt.
> Sie müssen nicht alles auf einmal machen. Ihr Stand wird automatisch gespeichert.

Die Minutenangabe kommt aus dem Paket (Start/Wachstum/Platzhirsch) und ist als Spanne zu nennen, nie als Zusage.

### 9a.4 Erklärung dauerhaft statt Rundgang

**Entscheidung: keine klassische Tooltip-Tour.** Begründung: Kunden loggen sich selten ein und haben eine einmalige Tour beim zweiten Besuch vergessen. Tour-Overlays veralten außerdem bei jeder UI-Änderung. Stattdessen:

1. **Erklärender Leerzustand pro Modul.** Jedes Modul erklärt sich, solange es leer ist — z. B. Rechnungen: *„Hier erscheinen Ihre Rechnungen. Sie können direkt im Portal bezahlen; eine Kopie geht zusätzlich per E-Mail an Sie."*
2. **Einzeilige Erklärung an ungewöhnlichen Feldern** („Warum wir das brauchen") — bereits im Onboarding-Konzept verankert, gilt hier gleichermaßen.
3. **Ersthilfe je Modul, einmalig eingeblendet**, danach über das Fragezeichen dauerhaft abrufbar.
4. **Klartext-Status statt interner Codes:** `Wir prüfen` · `Ihre Freigabe fehlt` · `Bereit zur Veröffentlichung` — nie `qa_failed` oder `customer_preview`.

### 9a.5 Regeln

- **Überspringbar** — jederzeit, ohne Nachteil.
- **Wiederaufrufbar** unter Hilfe → „Einführung erneut ansehen".
- **Maximal drei Bildschirme.** Wird es länger, ist die Oberfläche zu kompliziert — dann wird die Oberfläche vereinfacht, nicht die Einführung verlängert.
- **Kein Pflichtvideo**, keine Gamification, keine Fortschrittsabzeichen. Die Zielgruppe will fertig werden, nicht spielen.
- **Mobil vollwertig** — viele Kunden öffnen den Link vom Handy. Ein Bildschirm pro Ansicht, Buttons in Daumenreichweite.
- **Barrierefrei:** Tastaturbedienung, Fokusfalle im Dialog, `Esc` schließt, `prefers-reduced-motion` respektiert.
- **Kein Zwang zur Vollständigkeit:** Wer direkt „Portal öffnen" klickt, darf alles trotzdem bedienen.

### 9a.6 Stufenzuordnung und Messung

| Stufe | Umfang |
|---|---|
| **Stufe 0** | **Alle drei Bildschirme** (§9a.2) + erklärende Leerzustände + Wiederaufruf unter Hilfe. Die Texte stehen fest und sind statisch — der Mehraufwand gegenüber einem Einzelbildschirm ist minimal, der Nutzen beim ersten Eindruck nicht. **Nicht** enthalten: Ersthilfe je Modul, Kurzeinführung nach Zahlung. |
| **Stufe 1** | Zusätzlich: Kurzeinführung nach Zahlung, Ersthilfe je Modul, personalisierte Inhalte |
| **Stufe 2** | Optional kurze Erklärvideos (max. 60 Sekunden) für Vorschau-Freigabe und Selbstpflege — nur, wenn die Praxis zeigt, dass Text nicht reicht |

**Messung:** Erfasst wird, welche Fragen trotz Einführung im Support ankommen. Jede wiederkehrende Frage ist ein Auftrag, die Einführung oder die Oberfläche zu verbessern — **nicht**, mehr Text hinzuzufügen.

---

## 10. KI-/Automatisierungslogik

**Zwei Codebasen, drei Produkte:**
1. **SARTU-Website** — öffentliche Seiten **und** Kundenbereich in **einem** PHP-Projekt (`/`, `/portal/`, `/admin/`, `/api/`)
2. **Kundenseiten** — je ein eigenes, versioniertes PHP-Projekt pro Kunde

> **Sprachregel:** Intern darf „Steuerungsebene" oder „Adminbereich" stehen. **Kundensichtbar** heißt es
> **Kundenbereich** — nie App, Software, SaaS, Plattform, Dashboard oder Control-Plane. Der Kunde soll
> denken „ich melde mich an und sehe mein Projekt", nicht „ich muss ein Werkzeug lernen".

**Kundenseiten-Tech:** **PHP, serverseitig gerendert, öffentliche Seiten cachebar** — dieselbe Bauweise wie SARTUs eigene Website. 1 Repository pro Kunde, gemeinsamer versionierter **SARTU-Starter** + versionierte **Designwerte**. Strukturierte Inhalte statt freiem HTML. Formulare/Dynamik über eng begrenzte Portal-APIs. Jede Produktion reproduzierbar, testbar, **exportierbar, rückrollbar**. Der Kundenstand muss **auch nach Vertragsende baubar** sein (notwendige Komponenten eingefroren/vendort; Master-Designsystem/Generatoren/Prompts bleiben intern).

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

**Tech-Stack-Entscheidung (final, ersetzt alle früheren Stände):** **Ein modulares PHP-Projekt** — öffentliche Seiten unter `/`, Kundenbereich unter `/portal/`, interner Bereich unter `/admin/`, Serverfunktionen unter `/api/`. PHP 8.3+, MySQL/MariaDB, serverseitig gerendert, kein Vollframework, kein SPA. Verbindlich: `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` §1.

> **Abgelöst und nicht mehr gültig:** Node + Fastify + EJS · PostgreSQL als Zielsystem · Redis · Supabase als Stufe-0/1-Basis · Astro für die Kundenseiten · shadcn/ui. Diese Stände stammen aus früheren Fassungen und aus Prototypen. Sie dürfen als **fachliche Referenz** dienen, sind aber **keine Zielarchitektur**.
>
> **Warum PHP:** SARTU ist eine Website mit geschütztem Kundenbereich, keine App. Ein Projekt statt zwei bedeutet ein Deployment, eine Betriebsumgebung, ein Abhängigkeitsstand und **keine** Schnittstelle mit gemeinsamem Geheimnis. Für einen Betrieb, der von einer Person gepflegt wird, ist das die belastbarere Lösung. Der Preis: bei Mollie-Rückrufen und Domainprozessen (Stufe 1+) braucht es eine Umgebung mit stabilen eingehenden Aufrufen — ein einfaches Hosting-Paket reicht dafür meist nicht (Portal-Lastenheft §1.4).

> **Template-Altlasten bewusst verworfen:** Folex Lite, ScrewFast, AstroWind, Studio Admin, Tailwind-Studio, Lume und shadcn-`dashboard-01` waren **Recherche-/Prototypenstände, keine Vorgabe**. Die Design-/Basisentscheidung wird frei und neu getroffen – siehe `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`.

### Deployment-Realität: was per FTP geht und was nicht

Ein früher geäußerter Wunsch war „am Ende alles per FTP hochladen". Das ist **teilweise erfüllbar** —
aber nur mit einer Klarstellung, die vorher gefehlt hat.

**Was „nur FTP" nicht heißt:** reines HTML. Sobald es eine Anmeldung, Uploads, Rechnungen, einen
Bedarfsscheck mit Schritten und einen Adminbereich gibt, braucht es serverseitige Logik. Das ist
keine Frage des Geschmacks, sondern des Funktionsumfangs.

**Was „nur FTP" heißen kann:** Das gesamte Projekt liegt als PHP-Dateien auf dem Server und wird per
SFTP oder Git dorthin gebracht. **Kein** Build-Schritt, **kein** Paketmanager auf dem Server,
**keine** Container, **kein** eigener Prozess, der laufen muss. Genau das leistet die
PHP-Architektur (Portal-Lastenheft §1) — und das war der eigentliche Kern des Wunsches.

| Baustein | Realistisch | Warum |
|---|---|---|
| **Öffentliche Seiten** | ja, cachebar bis hin zu vorgenerierten Dateien | hängen an keiner Sitzung |
| **Kundenbereich, Adminbereich, Bedarfsscheck** | ja **auf PHP-Hosting mit Datenbank** | serverseitig gerendert, kein Build, kein Dauerprozess |
| **Zeitgesteuerte Aufgaben** (Löschfristen, Überfälligkeit) | nur, wenn der Tarif Cron bietet | fehlt bei einfachen Paketen häufig |
| **Zuverlässiger Mailversand** | nur mit eigener Domain und SPF/DKIM/DMARC | Anmeldelinks im Spam = das Produkt funktioniert nicht |
| **Mollie-Rückrufe, Domainprozesse** (Stufe 1+) | **auf einfachem Shared-Hosting meist zu schwach** | brauchen stabile eingehende Aufrufe und längere Laufzeiten |

**Konsequenz:** Stufe 0 ist auf gutem PHP-Hosting betreibbar — vorausgesetzt, Cron und Mailversand
funktionieren wirklich (**vorher praktisch prüfen**, nicht der Anbieterbeschreibung glauben). Für
Stufe 1 ist der Wechsel auf einen kleinen eigenen Server der Normalfall, kein Scheitern. Die
Architektur bleibt dabei **dieselbe** — es ändert sich nur, wo sie läuft. Genau deshalb ist PHP hier
die belastbarere Wahl: Der Umzug ist ein Kopiervorgang, keine Neuentwicklung.

## 13. Eigene SARTU-Website – ausgelagert (Autoritätsregel)

> **Für die eigene SARTU-Verkaufswebsite gilt ausschließlich `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`.**
> Dort stehen verbindlich: Sitemap, Startseiten-Dramaturgie, Seitenkonzepte, Leistungsseiten, Ratgeber-/Lexikon-Startumfang, Content-Cluster, Bild- und Screenshotliste, Trust/FAQ und Abnahmekriterien. Die **fertigen Texte** stehen in `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md`.
>
> **Gestaltung ist in keinem dieser Dokumente vorgegeben.** Farben, Schriften und Formen entstehen ausschließlich über `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`.
>
> Dieses Masterkonzept regelt **nur noch**: Geschäftsmodell, Angebot/Preise, Zahlung, Domain/Hosting/E-Mail, Portal, KI-/Produktionslogik, Technik, Recht, Markteintritt und Ausbaustufen. Die früheren Website-Detailabschnitte wurden hier **entfernt**, weil zwei Dateien nicht dieselbe Sache regeln dürfen.

**Nur zur Orientierung (nicht maßgeblich):** Launch = Kernseiten (`/`, `/leistungen`, `/preise`, `/ablauf`, `/briefing`, `/ueber-uns`, `/kontakt`, Pflichtseiten) + **5** Leistungsseiten + **3 Transparenzseiten** + **2 Vergleichsartikel im Ratgeber-Bereich** + **8** Lexikonbegriffe. Kommerzielle Hubs, Branchen-Hubs, Ortsseiten und ein Lexikonausbau auf 40–60 Begriffe sind **Stufe 2** – erst nach Search-Console-Daten.

---

## 14.–15. (verschoben) Startseite und Leistungsseiten

Siehe `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`, Abschnitte 4–6.

---

## 16. SEO-/GEO-Strategie (Produktleistung für Kundenwebsites)

> Dieser Abschnitt beschreibt, **was der Kunde kauft**. Die SEO-/GEO-Struktur der *eigenen* SARTU-Website steht in der Website-Datei, Abschnitt 9.

**Grundhaltung (belegt durch Google-Doku):** GEO ist **kein** magischer Zusatz und **kein** Spezial-Schema. Gute KI-Sichtbarkeit = Fortsetzung guter SEO: crawlbare, hilfreiche, konsistente, entitätsklare Inhalte. **Keine** Garantie auf Rankings/Anfragen/Umsatz/KI-Nennungen. `llms.txt` wird angelegt, aber **nicht** als Rankingfaktor beworben.

**SEO-/GEO-Startsystem (im Websitepreis, ab Launch):** Suchintention + Thema je Seite · Antwort-zuerst-Texte aus bestätigten Fakten · sprechende URLs (Bindestriche, keine Umlaute) · genau eine H1, saubere Überschriften · interne Links als echte Links · Title/Description/Canonical/OG/Robots · Breadcrumb + `BreadcrumbList` · `Organization`+`WebSite` global, `Service`/`Article`/`DefinedTerm` seitenweise (nur bei sichtbarer Entsprechung; `FAQPage` optional — seit Juni 2026 ohne Rich Results) · XML-Sitemap, robots.txt, 404, Redirect-Plan · echte NAP, `LocalBusiness` **nur** bei berechtigtem Standort · Performance (**vor Livegang im Labor:** LCP < 2,5 s, TBT < 200 ms, CLS < 0,1 — **echte Core Web Vitals inkl. INP** erst als Feldmessung nach Livegang; AVIF/WebP + srcset, Hero nicht lazy + `fetchpriority=high`, self-hosted WOFF2 `font-display:swap`) · Bild-SEO · Search Console + Bing Webmaster + Sitemap einreichen, IndexNow optional.

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
| **–** | **Nichts davon**, solange `[GESCHAEFTSADRESSE_STATUS]` in `SARTU_ENTSCHEIDUNGEN_OFFEN.md` auf `offen` steht | **aktueller Stand.** Keine Ortsseite, kein Unternehmensprofil, keine Ortsnamen in Titeln |
| 0 | **Google-Unternehmensprofil** mit `[HAUPTORT]`-Geschäftsadresse | erst nach Standortentscheidung **und** nur, wenn die Adresse die Eignungsregel erfüllt (§23a.1). Dann schnellster lokaler Hebel, unabhängig von Ortsseiten |
| 1 | **Region-Hub** `/webdesign-region-[STARTREGION]` + **2–3 Tier-1-Orte** aus dem Umland + Heimatanker `/webdesign-[HEIMATORT]` | echte NAP-Daten, konsistent zu Impressum und Unternehmensprofil |
| 2 | **`/webdesign-[HAUPTORT]`** | erst wenn Referenzen und Bewertungen vorliegen — organisch härtester Wettbewerb (das Local Pack läuft bereits über Stufe 0) |
| 3 | weitere Tier-2-Orte | je einzeln durch das Gate unten |
| 4 | **Branche × Region** statt Kleinstadtseiten, z. B. `/website-handwerker-region-[STARTREGION]` | erst nach Search-Console-/SEA-Daten |
| 5 | weiterer Ausbau | nur für Orte mit belegten Impressionen oder Leads |

**Warum der Hauptort erst in Stufe 2:** Dort ist der Wettbewerb am härtesten und die Erfolgswahrscheinlichkeit ohne Referenzen am geringsten. Im Umland ist der Wettbewerb dünn, die Betriebsdichte in der Zielgruppe hoch und der Ortsbezug echt — dort entstehen die ersten Rankings und die ersten Referenzen, die den Hauptort später erst möglich machen. **Diese Reihenfolge gilt für jede Region** und ist der Grund, warum die Strategie ohne Standortentscheidung vollständig formulierbar ist.

**Priorisierung weiterer Orte** (nicht nach Einwohnerzahl): echte Nachbarstädte im Einzugsgebiet · wirtschaftlich relevante Orte mit passendem Betriebsbesatz · Orte mit belegtem Suchvolumen oder SEA-Signal · Orte, für die konkrete Beispiele, Referenzen oder echte lokale Recherche vorliegen.

> **Beweisleiter statt Einheitsregel:** Wie viel Beleg eine Ortsseite braucht, hängt von ihrer
> Entfernung zum Arbeitsort ab — echte lokale Recherche im Umland, ein belegtes Datensignal weiter
> draußen, eine freigegebene Fallstudie außerhalb des bedienbaren Umkreises. Vollständig in
> `SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §4.1, dort steht auch die Pflegeobergrenze von rund 180 Seiten.

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
**Korrigierter Startumfang: 3 Transparenzseiten, 2 Vergleichsartikel im Ratgeber-Bereich und 8 Lexikonbegriffe.** Die früher genannten 40–60 Begriffe bzw. 3–6 Ratgeber gelten **nicht** mehr für den Launch, sondern sind Stufe 2. **Die Transparenzseiten sind der Kern** — sie tragen den einzigen Sichtbarkeitsvorteil, den SARTU gegenüber etablierten Agenturen hat (`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §3.4).

**Weiterhin im Master gültig – Bildprinzip für Kundenwebsites:** echte Betriebs-, Team-, Projekt- und Produktbilder haben Vorrang; gezielt lizenzierte Bilder nur, wenn reale Motive fehlen; KI-Bilder nie als Dokumentation des Unternehmens. Keine austauschbaren Handschlag-/Laptop-/Callcenter-Stockbilder. Bildrechte und zulässige Verwendung werden **pro Datei im Portal bestätigt**.

---

## 20. Designprinzipien (Kundenwebsites)

> Die **Markenpalette, Typografie und Designrichtung der SARTU-Website** sind ausgelagert: `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`, Abschnitt 3 (drei bewertete Richtungen zur finalen Entscheidung) und Abschnitt 10 (Website-Designsystem).

Für die **Kundenwebsites** (das verkaufte Produkt) gilt das versionierte SARTU-Designsystem:

**Unveränderliche Grundlagen:** 4-/8-Pixel-Abstandslogik · stabile Container und responsive Raster · einheitlicher Radius je Projekt · klare Fokus-/Hoverzustände · semantisches HTML · barrierearme Formulare und Navigation · Bildkomponenten mit festen Seitenverhältnissen und responsiven Quellen · begrenztes JavaScript-/Animationsbudget · keine überlappenden Texte, Layoutsprünge oder abgeschnittenen Bedienelemente.

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
- Öffentliche Website (Kernseiten + **5** Leistungsseiten + **3 Transparenzseiten** + 2 Vergleichsartikel im Ratgeber-Bereich + 8 Lexikonbegriffe), launchfähig.
- Produktion **manuell + KI-assistiert** (Mensch baut aus Designsystem). Domain/DNS via INWX **manuell**. Buchhaltung via lexoffice/sevDesk.
- **Ziel: 2–3 echte Referenzkunden** live → echte Case Studies + echte Portal-Screens (die die Website ohnehin braucht).

**Stufe-0-Portal – verbindlicher Sichtbarkeitsumfang (nicht verhandelbar, weil USP):**

| Muss sichtbar/klickbar sein | Mechanik dahinter darf sein |
|---|---|
| Login (oder mind. geschützter, nicht ratbarer Projektlink) | einfache Auth, manuell angelegte Konten |
| **Willkommensstrecke beim ersten Login** (§9a.2, drei Bildschirme) | statisch — Inhalt fest, nicht personalisiert, überspringbar, wiederaufrufbar |
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

**Stufe 1 – Portal härten:** Angebot/Annahme im Portal, **Mollie-Abo/Mandat** für Schutz (E2E getestet), adaptives Onboarding, Lead-Inbox, strukturierte Selbstpflege (Öffnungszeiten/Kontakt/Seitenstatus), Support, Audit-Log, Admin-2FA, Mandantentrennung. Konsolidierung auf die Zielarchitektur aus Portal-Lastenheft §1.

**Stufe 2 – Produktion teilautomatisieren:** versionierte Spec → **assistierter** Build → automatische QA-Gates → Adminfreigabe → versionierte Vorschau/Deployment/Rollback. Selbstpflege Team/Stellen/Projekte. SEO-/GEO-Flotte (technische Checks + Patch-Entwürfe). INWX-Lifecycle im Portal.

**Stufe 3 – Skalieren:** echter Anbieteradapter (Codex/Claude headless orchestriert), programmatische Ortsseiten mit noindex-Stage + Freigabegate, Branchen-Hubs, SEO-Ausbau nach Search-Console-Daten, weitere Regionen.

**Nie Teil des Marktstarts:** autonome Werbekampagnen, freie Kundenseiten-Erstellung, Wettbewerber-Scraping ohne Zweck, Umsatzprognosen, Social-Redaktionssystem, Plugin-Marktplatz.

---

## 23a. Markteintritt, Nachfrage und Kaltstart-Pricing

> Diese Lücke war der schwerwiegendste Mangel des bisherigen Konzepts: Es beschreibt lückenlos, wie eine Anfrage **verarbeitet** wird – aber nicht, wie sie **entsteht**.

### A. Startregion statt „deutschlandweit"

> ### ⏸ OFFEN: Startregion und Geschäftsadresse
>
> Die konkrete Region ist **nicht entschieden**. Werte und Sperren stehen in
> `SARTU_ENTSCHEIDUNGEN_OFFEN.md`, Abschnitt 1. Solange dort `offen` steht, gilt:
> **keine** Ortsseiten, **kein** `LocalBusiness`, **kein** Unternehmensprofil, **keine** Ortsnamen
> in Titeln, H1 oder URLs.
>
> **Das blockiert den Bau nicht.** Die Website entsteht standortneutral und vollständig; die lokale
> Ebene ist eine spätere Ergänzung, kein Fundament.

**Die strategische Weichenstellung gilt unabhängig davon, welche Region es wird: nicht Kernstadt, sondern Kernstadt + Umland.**

| | `[HAUPTORT]` (Kernstadt) | Umland / Landkreise |
|---|---|---|
| Suchvolumen | hoch | gering bis mittel |
| Wettbewerb | **sehr hoch** (viele etablierte Agenturen) | **dünn** |
| Eigene Glaubwürdigkeit | eine von vielen | **echter Ortsbezug**, wenn man von dort kommt |
| Betriebsdichte Zielgruppe | Dienstleister, Kanzleien | **Handwerk, Praxen, lokale Betriebe** |
| Websitequalität im Bestand | überwiegend ordentlich | **oft veraltet oder keine** |

**Konsequenz:** Die Kernstadt ist der **Volumen-Anker** (dort wird gesucht), das Umland ist der
**Gewinn-Markt** (dünner Wettbewerb, glaubwürdige Nähe). Ein Agenturmitarbeiter aus der Großstadt
fährt ungern 30 km aufs Land — wer von dort kommt, hat genau dort den Vorteil.

**Ortsstruktur, sobald entschieden** (unter dem Gate aus §16a):
- **Region-Hub:** `/webdesign-region-[STARTREGION]` — Dach für die gesamte Region
- **Anker:** `/webdesign-[HAUPTORT]` — höchstes Volumen, härtester Wettbewerb, langfristiges Ziel
- **Tier 1:** 3–5 Orte im Umland mit echter Nähe, Wirtschaftskraft und dünnem Wettbewerb
- **Tier 2:** später, datengetrieben
- **`[HEIMATORT]`:** eigene Seite als Vertrauens- und Heimatanker, **nicht** als Traffic-Quelle

Reihenfolge: Region-Hub und 2–3 Tier-1-Orte zuerst — **nicht** die Kernstadt. Die kommt, wenn
Referenzen und Bewertungen stehen.

**Auswahlkriterien für Tier-1-Orte** (nicht nach Einwohnerzahl): echte Nachbarschaft zum Arbeitsort ·
wirtschaftlich relevanter Betriebsbesatz in der Zielgruppe · belegtes Suchvolumen oder SEA-Signal ·
Orte, für die konkrete Beispiele oder echte lokale Recherche vorliegen.

### 23a.1 Geschäftsadresse — welche Form welche Folgen hat

**Diese Entscheidung ist offen.** Sie ist trotzdem hier beschrieben, weil sie die
**Google-Profil-Form** bestimmt und deshalb vor dem lokalen Launch fallen muss — nicht vor dem Bau.

**Impressum:** Es braucht eine **ladungsfähige Anschrift**. Eine Geschäftsadresse erlaubt es, die
Wohnanschrift nicht zu veröffentlichen. *(Die exakte Anschrift wird beim Go-live ins Impressum
eingesetzt — nie in Konzeptdateien.)*

**Kundentermine — unabhängig von der Adresse ein USP:**
- Standard: **kein Termin nötig**
- Auf Wunsch: **Video** oder **beim Kunden vor Ort**
- Auf der Website wird **kein** Besuchstermin beworben — eine Adresse im Impressum ist keine Einladung

**Produktvorteil, der bleibt:** Das Konzept verlangt **echte Betriebsbilder statt Stockfotos**. Ein
Termin beim Kunden im Umland ist von einem Standort in der Region machbar — eine Agentur aus einer
anderen Großstadt kann das nicht liefern.

#### ⚠️ Was für eine Adresse es ist, entscheidet über das Google-Profil

Googles Kernregel lautet wörtlich: *„To qualify for a Business Profile, a business must make
in-person contact with customers during its stated hours."* Postfächer und Mailadressen an entfernten
Standorten sind ausdrücklich ausgeschlossen
([Eignungsrichtlinie](https://support.google.com/business/answer/13763036), geprüft 25.07.2026).

| Art der Adresse | Impressum | Google-Unternehmensprofil |
|---|---|---|
| **Eigenes/gemietetes Büro**, in dem tatsächlich gearbeitet wird und Kunden empfangen werden können | ✅ | ✅ **Sichtbare Adresse zulässig** — der stärkste Fall |
| **Coworking** mit fester, tatsächlich genutzter Fläche | ✅ | ⚠️ Grauzone — nur bei echter Präsenz; mehrere Firmen unter einer Adresse sind ein Risikosignal |
| **Virtuelles Büro / reine Postadresse** ohne Anwesenheit | ✅ (wenn ladungsfähig) | ❌ **Nicht zulässig** — Sperrrisiko. Dann Service-Area-Business ohne sichtbare Adresse, bezogen auf den echten Arbeitsort |

**Regel:** Die Adresse im Profil muss die Realität abbilden. Eine Adresse zu führen, an der niemand
erreichbar ist, riskiert die **Sperrung des Profils** — und trifft damit genau den Kanal, der lokal
am meisten bringt.

#### Zwei Hebel, die oft vermischt werden

- **Local Pack (Kartenergebnisse)** — hängt an einem verifizierten Profil, echter Adresse, Kategorie
  und **Bewertungen**. Mit echter Adresse in der Kernstadt **deutlich schneller gewinnbar** als
  organisches Ranking.
- **Organisches Ranking für „Webdesign `[HAUPTORT]`"** — bleibt hart umkämpft und braucht Zeit,
  unabhängig von der Adresse.

**Konsequenz für die Reihenfolge, sobald entschieden:** Unternehmensprofil **sofort** aufsetzen und
mit Bewertungen füttern. Die **Ortsseiten** starten trotzdem im Umland, weil dort organisch weniger
Widerstand ist. Beides parallel — sie konkurrieren nicht.

**Außendarstellung:** Geschäftsadresse im Impressum und in strukturierten Daten; in Texten
„Region `[STARTREGION]`", weil das den tatsächlichen Einzugsbereich beschreibt und im Umland
glaubwürdiger ist als reine Stadt-Rhetorik.

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

## 23b. Kanalstrategie — wie SARTU tatsächlich an Kunden kommt

> **Ehrliche Grundlage:** Für „Webdesign Agentur" oder „Website erstellen lassen" wird eine neue Domain auf absehbare Zeit **nicht ranken**. Das sind die umkämpftesten kommerziellen Begriffe der Branche. Die ersten Kunden kommen bei **keiner** kleinen Agentur über Google-Rankings — auch bei den etablierten Wettbewerbern nicht.

### 23b.1 Rollenklärung: Was die Website beim Start leistet

Das Konzept behandelt die Website implizit als Kundengewinnungs-Maschine. Beim Start ist sie das nicht.

| | Job der Website |
|---|---|
| **Monat 1–9** | **Conversion und Glaubwürdigkeit.** Wenn jemand von SARTU *hört* — Empfehlung, Anzeige, Multiplikator, Gespräch — macht die Website daraus einen Auftrag. |
| **ab Monat 9–18** | **zusätzlich Akquise**, über Long-Tail und lokale Suche. |

Daraus folgt: Die Website muss beim Launch nicht ranken, sondern **abschließen**. Genau dafür ist sie konzipiert (Festpreis sichtbar, Ablauf klar, Portal als Beweis, keine Rückfragen nötig). Kein Grund, den Launch für SEO zu verzögern.

### 23b.2 Kanäle nach Hebelwirkung

| Kanal | Wirkung ab | Aufwand | Bewertung |
|---|---|---|---|
| **Multiplikatoren** (23b.3) | Woche 2–8 | mittel | **höchster Hebel**, im Konzept bisher gar nicht vorhanden |
| **Verwaiste Bestandskunden** (23b.4) | Monat 1–6 | mittel | akuter Bedarf — aber **nur als Neubau**, keine Übernahme fremder Systeme |
| **Trigger-Events** (23b.5) | Woche 4+ | mittel | sehr effizient, weil Bedarf bereits existiert |
| **Google Ads** | Tag 1 | gering, kostet Geld | einziger sofort kaufbarer Kanal mit Kaufabsicht |
| **Netzwerk / Direktansprache** | Woche 1 | hoch | rechtlich gestaffelt (§23a) |
| **Google-Unternehmensprofil + Bewertungen** | Monat 2+ | gering | Hebel sind **Bewertungen**, nicht die Website |
| **Transparenzseiten** (veröffentlichte Preise) | Monat 3–9 | mittel | **stärkster eigener Hebel** — wirkt in Suche **und** KI-Antworten, weil der Markt „Preis auf Anfrage" schreibt |
| **SEO / Content** im Übrigen | Monat 9–18 | hoch, dauerhaft | richtig jetzt zu starten, falsch sich darauf zu verlassen |
| **Ortsseiten** | Monat 12–24 | hoch, **dauerhaft** | erst mit echter Referenz aus dem Ort |

> **Ausführlich:** `SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §0 (Kanäle mit Zeiträumen, Local Pack gegen
> Ortsseiten) und §3.4 (Transparenzseiten). Dort steht auch, warum `Webdesignagentur` als Zielbegriff
> ausscheidet und warum Ortsseiten einen echten Kunden im Ort voraussetzen (§4.1).
>
> **Zur früheren Einschätzung „GEO ist eine billige Wette, kein planbarer Kanal":** Das galt, solange
> GEO als Nebenprodukt gedacht war. Mit den Transparenzseiten wird es ein **gezielter** Hebel — nicht
> durch ein Format, sondern weil überprüfbare Zahlen zitierfähig sind und fast niemand sonst welche
> veröffentlicht. Planbar ist der Kanal damit immer noch nicht; **beeinflussbar** aber sehr wohl.

### 23b.3 Multiplikatoren — der unterschätzte Hauptkanal

Menschen, die mit der Zielgruppe reden, **genau wenn** eine Website gebraucht wird, und die selbst keine bauen:

| Multiplikator | Warum er der richtige Moment ist |
|---|---|
| Steuerberater, Buchhaltungsbüros | sprechen mit jedem Betrieb und jedem Gründer, kennen die Zahlen |
| Gründungs-/Unternehmensberater, IHK, Handwerkskammer | begleiten Gründung und Betriebsübernahme |
| IT-Dienstleister und Systemhäuser | haben die Kunden, machen aber kein Web |
| Werbetechniker, Druckereien, Fahrzeugbeschrifter | liefern Schild, Flyer, Beschriftung — Website fehlt regelmäßig |
| Fotografen für Unternehmensbilder | perfekte Vorstufe: erst Bilder, dann Website |
| Andere Agenturen und Freelancer | lehnen Kleinprojekte ab oder sind ausgelastet |

**Ziel: 5 verlässliche Multiplikatoren.** Fünf gute schlagen ein Jahr SEO.

**Modell:** Gegenseitigkeit vor Provision. Empfehlung gegen Empfehlung ist unbürokratisch und rechtlich unkritisch. **Provisionen an Steuerberater vorher berufsrechtlich prüfen lassen** — dort gelten Einschränkungen.

**Portalzugang für Multiplikatoren (Produktidee, Stufe 2):** Ein Multiplikator will keine Websites verkaufen — er will wissen, dass sein Mandant gut versorgt ist. Ein schlanker Lesezugang („Ihre empfohlenen Betriebe und deren Projektstatus") macht die Empfehlung für ihn kontrollierbar und bindet ihn. Kostet fast nichts, weil Rollen und Status ohnehin existieren.

### 23b.4 Verwaiste Bestandskunden — aber nur als Neubau

**Entschieden: SARTU übernimmt keine fremden Websites in den Betrieb.** Fremdsysteme (meist WordPress) bedeuten genau die Update-, Plugin- und Sicherheitsarbeit, die das Modell vermeidet — der Aufwand ist zu groß und die Marge kippt. Außerdem widerspräche es dem öffentlichen Versprechen „kein WordPress".

**Was trotzdem nutzbar bleibt:** Wenn ein Freelancer oder eine Kleinagentur aufhört, stehen deren Kunden ohne Ansprechpartner da. Das ist ein **Trigger-Event** (siehe 23b.5), kein Übernahmegeschäft. Die Ansprache lautet nicht „wir übernehmen Ihre Seite", sondern:

> Ihr bisheriger Dienstleister hört auf. Statt eine alte Website weiterzuschleppen, bekommen Sie eine neue zum Festpreis — und wir betreiben sie danach dauerhaft.

**Vorteil dieser Variante:** Der Kunde hat akuten Bedarf und bestehende Zahlungsbereitschaft, aber SARTU liefert das **eigene** Produkt statt fremder Altlasten. Der abgebende Dienstleister kann seine Kunden geordnet weiterempfehlen, ohne sie im Stich zu lassen — das ist auch für ihn gesichtswahrend.

**Regel:** Keine Ausnahme, kein „nur diese eine alte Seite mitbetreuen". Wer nur Betrieb ohne Neubau will, ist kein SARTU-Kunde.

### 23b.5 Trigger-Events statt Dauerwerbung

Werbung an alle ist teuer. Ansprache im richtigen Moment ist billig. Auslöser, bei denen fast immer eine Website gebraucht wird:

- **Betriebsnachfolge / Übergabe** (Handwerk steht massenhaft davor) — neue Inhaberdaten, oft neuer Auftritt
- **Neugründung nach Meisterprüfung** — Handwerkskammern, Meisterfeiern
- **Handelsregister-Neueintragungen** (öffentlich einsehbar)
- **Umzug oder zweiter Standort** — Adresse und Google-Profil müssen ohnehin angefasst werden
- **Firmenjubiläum**, Rebranding, Namensänderung
- **Der bisherige Dienstleister ist weg** (siehe 23b.4)
- **Akuter Schmerz:** gehackte oder offline gegangene WordPress-Seite, gesperrtes Google-Profil

Ansprache immer mit **konkretem Anlass**, in der rechtlich gestaffelten Reihenfolge aus §23a — Post und persönliche Ansprache sind hier klar im Vorteil.

### 23b.6 Website-Schnellcheck — dasselbe Werkzeug zweimal nutzen

**Die stärkste strukturelle Idee.** SARTU baut für die eigenen QA-Gates und die SEO-Flottenzentrale ohnehin einen Crawler, der Ladezeit, Mobilfreundlichkeit, Metadaten, SSL, Impressum, strukturierte Daten und Erreichbarkeit prüft. **Dieselbe Technik ist ein Akquise-Werkzeug.**

**Zwei Verwendungen, ein Werkzeug:**

1. **Öffentlich auf der Website:** „Website-Schnellcheck — Adresse eingeben, in 30 Sekunden sehen, was Besucher und Google sehen." Prüft: Ladezeit mobil, Mobildarstellung, SSL, Impressum vorhanden, Metadaten, ob Öffnungszeiten und Telefonnummer maschinenlesbar sind.
   → Das ist ein echter **Lead-Magnet**: nützlich, teilbar, verlinkbar, und er qualifiziert Interessenten selbst vor. Ein Steuerberater oder eine Kammer kann so etwas weiterleiten — eine Leistungsseite nicht.
2. **Als Gesprächsanlass:** Ein konkreter Befund („Ihre Seite lädt auf dem Handy 6 Sekunden, das Impressum fehlt") ist ein sachlicher Anlass — kein Werbespam.

**Regeln:** Ergebnis ehrlich und ohne Angstmache. Keine erfundenen „Sicherheitswarnungen". Keine Note, die jede Seite schlecht aussehen lässt — das durchschauen Unternehmer sofort. Kein automatischer E-Mail-Versand an ungeprüfte Adressen.

### 23b.7 Branchen-Spirale statt Streuung

Nicht breit akquirieren, sondern **eine Branche nach der anderen aufrollen**:

1. Erster Kunde in einer Branche → wird Referenz.
2. Der nächste Betrieb derselben Branche ist deutlich leichter zu gewinnen: gleiche Sprache, gleiche Einwände, vergleichbare Referenz.
3. **Nebeneffekt direkt auf die Marge:** Struktur, Texte, Designsystem-Varianten und Fragen wiederholen sich → die Stundenzahl pro Projekt sinkt. Genau der Hebel, den die Unit Economics brauchen (§11 der Marktanalyse).

Eine Branche sollte 3–5 Referenzen haben, bevor die nächste angefangen wird. Kandidaten mit stabilem Bedarf und wenig Sonderlogik: Sanitär/Heizung, Elektro, Dachdecker, Physiotherapie, Steuerkanzleien.

### 23b.8 Bewertungen als Produktfeature, nicht als Marketing-Aufgabe

Für lokale Sichtbarkeit sind **Bewertungen** der Hebel, nicht die Website. Der Fehler wäre, Wochen nach Launch eine Bitte per Mail zu schicken.

**Richtig:** Der Abnahme-Moment im Portal ist der Punkt höchster Zufriedenheit. Genau dort erscheint **genau einmal** die Bitte um eine Bewertung — mit direktem Link. Das ist ein Portal-Feature (Stufe 1), kein Marketing-Task. Bei Pilotkunden ist die Bewertung ohnehin Teil der vereinbarten Gegenleistung (§23a).

### 23b.9 „Zweitmeinung" als niedrigschwelliger Einstieg

Wer bereits ein Agenturangebot vorliegen hat, ist maximal kaufbereit — und maximal verunsichert. Angebot: **„Sie haben ein Angebot vorliegen? Wir sagen Ihnen, ob Umfang und Preis stimmen."**

Kostet ~15 Minuten, positioniert SARTU als ehrlichen Fachmann und funktioniert nur, weil SARTU **echte Festpreise** hat und deshalb vergleichen kann. Regel: ehrlich bleiben — wenn das fremde Angebot gut ist, sagt man das. Genau das erzeugt die Empfehlung.

### 23b.10 Was ausdrücklich NICHT getan wird

- **Keine sitewide Footer-Backlinks von Kundenwebsites** („Website von SARTU" auf jeder Seite jedes Kunden). Das ist der naheliegendste Gedanke und zugleich ein **Link-Spam-Risiko** — es schadet perspektivisch beiden Seiten. Zulässige Variante: ein Eintrag auf einer echten Referenzseite bei SARTU, und höchstens ein dezenter, nicht sitewide gesetzter Hinweis mit `rel="nofollow"` — nur mit Zustimmung des Kunden.
- Keine gekauften Links, keine Linktausch-Netzwerke, keine Verzeichnis-Massen-Einträge.
- Keine Fake-Bewertungen und keine Bewertungsanreize, die gegen Plattformregeln verstoßen.
- Keine Angstmache mit Recht (BFSG, DSGVO) als Verkaufshebel.
- Keine kalten Massen-E-Mails (§23a).

### 23b.11 Sequenz über zwölf Monate

| Zeitraum | Schwerpunkt | Messgröße |
|---|---|---|
| **Monat 1–2** | Standort entscheiden · 5 Multiplikatoren ansprechen · Google Ads klein starten · Website live | Multiplikator-Gespräche, erste Anfragen |
| **Monat 2–4** | 2–3 Pilotkunden liefern · Bewertungen einsammeln · Google-Unternehmensprofil aufbauen | abgeschlossene Projekte, Bewertungen |
| **Monat 4–6** | Case Studies veröffentlichen · aussteigende Dienstleister als Trigger nutzen · Schnellcheck live | Referenzen, laufende Betriebsverträge (MRR) |
| **Monat 6–9** | Branchen-Spirale in Branche 1 · SEA nach echten Daten optimieren | Kunden je Branche, Kosten je Anfrage |
| **Monat 9–12** | Lokales SEO ernsthaft (Region-Hub, Ortsseiten mit Gate) · Long-Tail-Content ausbauen | Search-Console-Impressionen, organische Anfragen |

**Kernkennzahl über allem:** Anzahl aktiver Betriebsverträge (wiederkehrender Umsatz) — nicht Website-Besucher.

---

## 24. Konkrete nächste Umsetzungsschritte

**A. Fundament klären (diese Woche):**
1. **Einen** Preis-/Scope-Stand als Single Source of Truth festlegen (`pricing.json`/`prices.js`) und `sartupaketepreise.md` + `sartulastenheftwebsite.md` in `konzepte/_archiv/` verschieben (als **veraltet** markieren) – gegen Wiederverwendung.
2. ~~Stack-Entscheidung~~ **entschieden:** ein modulares PHP-Projekt mit `/portal/` und `/admin/`, MySQL/MariaDB, serverseitig gerendert. Verbindlich: Portal-Lastenheft §1. Offen bleibt nur der konkrete Hosting-Anbieter (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4).
3. **Design-Briefing starten.** Jetzt schon festlegen: Ansprache „Sie", Verbotsliste und Anti-KI-Regeln als prüfbarer QA-Check. **Palette, Schrift und Logo erst nach der Variantenentscheidung** — sie vorher zu fixieren würde das Gate aushebeln, für das die Varianten überhaupt gebaut werden.

**B. Website launchen (2–4 Wochen):**
4. **Designrichtung final wählen** nach `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`: 2–3 klickbare Startseitenvarianten mit **echten** Texten werden vorgelegt, ein Mensch entscheidet. Danach **eigenes PHP-Layout** aufsetzen — globale Layouts, Partials, Komponenten, zentrale Design-Variablen. **Keine Template-Basis als Zielarchitektur** — SARTU verkauft „kein Baukasten“ und darf die eigene Seite nicht erkennbar zusammenstecken.
5. Kernseiten + **5** Leistungsseiten (GEO-Template) + **3 Transparenzseiten** + **2 Vergleichsartikel im Ratgeber-Bereich** + **8** Lexikonbegriffe bauen; **echte** NAP/Impressum (nach Kanzlei), sitemap/robots/llms.txt/OG, Search Console + Bing.
6. Portal-Screens als **Musteransicht** produzieren – aus der **echten** Stufe-0-UI, nie als gezeichnetes Fake-Dashboard.
7. ENDKONTROLLE-Profil **SARTU-PUBLIC** vollständig grün (keine Add-on-/Minuten-/Alt-Preis-Reste, keine Privatkunden-Formulierungen, kein „wartungsarm").

**C. Verkaufen & liefern (parallel/danach):**
8. **Stufe-0-Portal live** im verbindlichen Sichtbarkeitsumfang (Abschnitt 23): Login/geschützter Zugang, Cockpit mit einem nächsten Schritt, Angebot + Annahme, Rechnung + Mollie-Link, Aufgaben/Upload, Vorschau + Feedback, Freigabe, Domainstatus, **eine** echte Pflegefunktion.
9. **Markteintritt aktiv starten** (Abschnitt 23a): Startregion fixieren, GBP regelkonform anlegen, Zielkundenliste 30–50 Betriebe, Pilot-Outreach, SEA-Test mit Abbruchkriterien.
10. 2–3 **Referenzkunden mit Case-Study-Rechten** (Pilotkondition gegen Gegenleistung) durch den vollen Prozess führen → Case Studies + echte Screens.

**D. Vor „echten" Standardkunden (Zielbild-Gates):** Mollie E2E (Zahlung/Mandat/Wiederholung/Fehlschlag/Erstattung), INWX OT&E (Registrierung/Transfer/DNS-Snapshot/Übergabe), Export + Rollback praktisch getestet, AGB/AVV/Datenschutz/KI-Verarbeitung anwaltlich, ein Musterkunde Ende-zu-Ende.

---

## 25. Offene Entscheidungen (nur die wirklich nötigen)

1. ~~Supabase-Prototyp behalten oder migrieren?~~ **Entschieden: neu bauen in PHP.** Der Prototyp darf als **fachliche und visuelle Referenz** dienen — Ablauf, Felder, Texte, was sich als umständlich erwiesen hat. Sein **Code** wird nicht übernommen. Was daraus verwendet wird, steht begründet in `IMPLEMENTATION_PLAN.md` und `MIGRATION_NOTES.md`. **Nicht** parallel zwei Portale pflegen.
2. **Buchhaltung: lexoffice oder sevDesk?** (API-Anbindung, GoBD/E-Rechnung). Kaufmännische Entscheidung, vor Stufe 1.
3. **Typografie final:** reine Grotesk (Inter/Instrument Sans) vs. Grotesk + dezente editorial Serif für H1. Empfehlung: mit Grotesk starten, Serif optional testen.
4. **Startregion und Geschäftsadresse — offen.** Bestimmt Ortsseiten, `LocalBusiness`, Unternehmensprofil, lokale Keywords und die Impressumsanschrift. Werte und Sperren in `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1, Folgen in §23a.1. **Blockiert den Bau nicht**, blockiert den lokalen Launch.
5. **Solo vs. kleines Team – ehrliche Selbstdarstellung** und daraus abgeleitete **Kapazität/Projekte-pro-Monat** (bestimmt, ob der Portal-Vollausbau realistisch neben der Produktion läuft oder Hilfe/Outsourcing braucht).
6. **AGB/Garantie:** ob überhaupt eine (sauber formulierte) Zufriedenheitszusage als Verkaufsargument gewünscht ist – sonst weglassen.
7. **Designrichtung final:** eine der 2–3 Varianten aus dem Design-Briefing. **Keine** Template-Basis — die Umsetzung ist immer ein eigenes PHP-Layout mit zentralen Variablen (`CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`).
8. **Pilotkonditionen:** Werden 2–3 verdeckte Referenzslots angeboten – als Rabatt (5.900–6.500 € statt 7.900 €) oder als Zusatzwert zum vollen Preis?
9. **Hosting-Anbieter und Tarif:** offen (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4). Die Anforderungen stehen fest (Portal-Lastenheft §1.4). **Vor** der Umsetzung praktisch prüfen: Kommt eine Testmail im Posteingang an, nicht im Spam? Läuft ein Cronjob? Fehlt eines von beidem, ist der Tarif ungeeignet.
10. **Eigenes finales Website-Lastenheft** vor dem Bau erstellen (empfohlen: ja – `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md` ist die Grundlage dafür).

---

*Ende Masterkonzept. Die kritische Bewertung und die Herleitung mit Quellen — **Stand 24.07.2026, teils überholt, nicht maßgeblich für den Bau** — stehen in `CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md`.*
