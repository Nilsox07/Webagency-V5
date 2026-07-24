# SARTU – Masterkonzept (final, umsetzbar)

**Erstellt von:** Claude (Opus) · **Stand:** 24.07.2026
**Grundlage:** alle Konzeptdateien in `konzepte/` (Wahrheitsquelle: `GESCHAEFTSMODELL.md`, konkretisiert durch `SARTU_ANGEBOT_PORTAL_DETAILKONZEPT.md`, `SARTU_KONTAKTLOSER_VERTRIEB_LUMI_PORTAL.md`, `SARTU_DESIGNSYSTEM_PORTAL_ARCHITEKTUR.md`, `SARTU_WEBSEITENKONZEPT_FINAL_SEO_GEO.md`).

> Dieses Dokument ist die konsolidierte, widerspruchsbereinigte Bauvorlage für Website **und** Portal. Wo die Quelldateien sich widersprechen (drei Preisstände, drei Tech-Stacks, mehrere Paletten), trifft dieses Dokument eine Entscheidung und begründet sie. Die kritische Herleitung steht in `CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md`.

---

## 0. Wichtigste Korrektur vorab (bitte zuerst lesen)

Das kanonische Sartu-Modell ist **positionierungsseitig stark** und marktfähig. Die größte Gefahr ist **nicht** die Preis- oder Angebotslogik, sondern der Anspruch, das **komplette Control-Plane-Portal** (Lumi, Angebote, Mollie-Abo, INWX-Domainlebenszyklus, KI-Produktions-Orchestrierung, QA-Gates, Deployments, Rollback, SEO-/GEO-Flotte, Admin-Finanzen) **vollständig vor dem ersten Standardverkauf** zu bauen.

Für ein Ein-Personen-/Kleinstteam ist das ein zweites Produktunternehmen und ein Launch-Blocker. **Dieses Masterkonzept dreht die Reihenfolge um:**

1. **Zuerst verkaufen und liefern** (2–3 echte Referenzkunden, Website manuell + KI-assistiert, minimales Portal).
2. **Dann härten** (Angebot/Annahme, Mollie-Abo, strukturierte Selbstpflege).
3. **Dann teilautomatisieren** (Spec → assistierter Build → QA).
4. **Dann skalieren** (Provider-Adapter, Rollback, programmatische Ortsseiten).

Alles Weitere in diesem Dokument ist mit dieser Stufung kompatibel.

---

## 1. Finales Geschäftsmodell

**Kurzform:** Sartu ist eine **produktisierte B2B-Webdesign-Agentur** für regionale kleine und mittlere Unternehmen. Der Kunde beschreibt sein Geschäft; Sartu **empfiehlt eine Lösung, nennt einen Gesamtfestpreis, plant, textet, programmiert und betreibt** die Website. Der Kunde trifft Geschäftsentscheidungen, Sartu trifft Design-, Struktur-, Technik-, Paket- und Anbieterentscheidungen.

**Vier wirtschaftliche Hebel (unverändert übernommen, weil richtig):**
1. Wiederverwendbares, versioniertes **Designsystem** statt jedes Projekt neu erfinden.
2. **KI-gestützte** Code-, Struktur- und Textproduktion mit menschlicher Abnahme.
3. Ein **Portal** für Vertrieb, Projekt, Betrieb und Verwaltung (in Stufen, s. Abschnitt 8/23).
4. **Feste Grenzen** statt frei kombinierbarer Extras und unbegrenzter Handarbeit.

**Zwei Umsatzarten:**
- **Einmal:** Website-Erstellung (Festpreis pro Paket).
- **Wiederkehrend:** „Rundum-Schutz" (Betrieb/Hosting/Pflege) – der eigentliche Deckungsbeitrags-Motor.

**Leitentscheidung:** *Der Kunde entscheidet, was sein Unternehmen erreichen soll. Sartu entscheidet, wie die Website das erreicht.*

---

## 2. Finale Positionierung

**Öffentliches Kernversprechen (Hero):**
> Individuell programmierte Firmenwebsites zum Festpreis. Sartu plant, textet, programmiert und betreibt Ihre Website. Sie beantworten nur die Fragen zu Ihrem Unternehmen; Struktur, Design, Technik und SEO-/GEO-Basis übernehmen wir.

**USP in vier Worten:** *Festpreis. Portal. Kein WordPress. SEO-/GEO-Basis ab Start.*

**Ehrliche Einordnung des USP (wichtig für die Kommunikation):**
- „Kein WordPress" ist **kein Kundennutzen an sich** – Kunden interessiert kein CMS. Es ist ein **Beleg** für „keine Update-/Plugin-/Sicherheitslast bei Ihnen" und „schnell & wartungsarm". → Immer als *Entlastung*, nie als Technik-Feature verkaufen.
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

**In jedem Paket enthalten:** Bedarfsprüfung + begründete Empfehlung, strategische Sitemap, individuelles Design im Sartu-Designsystem, KI-gestützte (menschlich geprüfte) Programmierung ohne WordPress, responsive + Barrierefreiheits-Basis + Performance, Website-Texte aus bestätigten Fakten, SEO-/GEO-Startsystem, Kontaktweg/Formular, technische Einbindung freigegebener Rechtstexte + Consent, Vorschau + gebündeltes Feedback + Korrekturrunden, Domainprüfung/-verbindung/Launch, Portalzugang.

**Platzhirsch zusätzlich bedarfsgerecht:** Team-/Karrierebereich, Projekt-/Referenz-/Neuigkeitenstruktur, stärkere lokale Struktur, **genau ein** Conversion-Modul (qualifiziertes Anfrageformular *oder* einfache Ein-Kalender-Buchung *oder* einfaches Bewerbungsformular).

**Bewusst NICHT im Erstangebot** (Scope-Schutz): Add-on-Liste, Extraseiten-Preise, SEO-Stufen, Änderungsminuten, Logo-Pakete, Express, Newsletter/Tracking als Häkchen. Ein Standardangebot endet **exakt** beim veröffentlichten Paketpreis. Neue Ziele nach Auftrag → **ein** konsolidiertes Folgeangebot mit Festpreis, keine Einzelpreisliste.

**„Rundum-Schutz" (fest zugeordnet, keine Kundenauswahl, keine Änderungsminuten):**

| Stufe | netto/Mon. | Inhalt |
|---|---:|---|
| **Schutz S** | 59 € | Managed Hosting DE/EU, SSL, tägl. externe Backups, 30 Tage Versionen, Uptime-/Sicherheitsmonitoring, technische Updates, Portal-Selbstpflege, Erstreaktion 2 Werktage |
| **Schutz M** | 129 € | alles aus S, 90 Tage Versionen, erweiterte Formular-/Deploymentprüfung, monatl. Technik-/Suchstatus, Erstreaktion 1 Werktag |
| **Schutz L** | 249 € | alles aus M, 180 Tage Versionen, engmaschiger SEO-/GEO-/Conversion-Technikcheck, priorisierte Störungsbearbeitung, Erstreaktion binnen 8 Geschäftsstunden |

Erstlaufzeit 12 Monate ab produktivem Betrieb, danach 30 Tage zum Monatsende kündbar, monatlich im Voraus. **Reaktionszeit ≠ Fertigstellungszeit.** Statt Änderungsminuten pflegt der Kunde definierte Geschäftsdaten selbst (s. Abschnitt 8).

---

## 5. Zahlungsmodell

| Paket | Staffelung |
|---|---|
| Start / Wachstum | 50 % bei Auftrag, 50 % nach Abnahme vor Onlinegang |
| Platzhirsch | 40 % Auftrag, 30 % nach Leitseiten-/Systemvorschau, 30 % nach Abnahme vor Onlinegang |
| Sonderprojekt | Standard 40/30/30, im Angebot ggf. abweichend |

- **Zahlungsziel 10 Kalendertage.** Produktionsslot **erst nach erster Zahlung** verbindlich. Alle Meilensteine vor dem Onlinegang bezahlt.
- **Schlusszahlung** ist an **Abnahme/Fertigstellung** gekoppelt, nicht an einen frei verschiebbaren Onlinegang.
- **Mollie** (Zahlungsdienstleister, **nicht** Buchhaltung): Sartu-System erzeugt Rechnung + Forderung; Kunde startet den Mollie-Checkout im Portal. **Zahlungswahrheit** = serverseitig authentifiziert abgerufener Status nach Webhook, **niemals** der Browser-Redirect. Webhooks idempotent, eindeutige Idempotency Keys, jede Zahlung gegen interne Rechnung/Betrag/Währung/Metadaten geprüft.
- **Schutz-Abo:** Beim ersten wiederkehrungsfähigen Bezahlvorgang bestätigt der Kunde das **Mandat** ausdrücklich; danach monatlicher Voraus-Einzug. Start des Schutzes = produktiver Betrieb (Sonderregel bei kundenverschuldeter Onlinegang-Verzögerung: spätestens 14 Kalendertage nach betriebsfertiger Bereitstellung, nach Hinweis – Formulierung anwaltlich mit AGB abstimmen).
- **Buchhaltung nicht selbst bauen:** Rechnungen deterministisch über lexoffice **oder** sevDesk (Entscheidung offen, s. Abschnitt 25); Mollie nur für Zahlungslinks/Abo. Rechnungszahlen dürfen nie von KI erzeugt werden.

---

## 6. Domain-, Hosting- und E-Mail-Regelung

**Grundsatz:** Der Kunde entscheidet den **Domainnamen** und bleibt **Domaininhaber**. Sartu entscheidet und verwaltet die **technische Infrastruktur** (Registrar, DNS, Deployment).

**Neue Domain:** Kunde nennt Wunschname oder bittet um Vorschläge → Sartu zeigt **max. 3** geprüfte, markennahe Vorschläge (bevorzugt `.de`) → Portal prüft Verfügbarkeit/Preis über **INWX** (Reseller-/JSON-RPC-API, hinter Provider-Adapter) → Kunde bestätigt genau einen Namen + Inhaberdaten → **letzte** Echtzeitprüfung → Registrierung **erst nach erster Zahlung** und mit **kundeneigenem Inhaberkontakt** (kein pauschaler Sartu-Registrant außer OT&E). Eine normale Domain bis **30 € netto/Jahr** ist bei Sartu-Verwaltung im Schutz enthalten; Premiumdomains/Sonderendungen ausgeschlossen (→ Alternativvorschläge).

**Vorhandene Domain:** Transfer bevorzugt, wenn ohne Betriebsrisiko möglich; sonst nur DNS anbinden. **Vor jeder Änderung** A/AAAA/CNAME/MX/SPF/DKIM/DMARC + Subdomains/Verifizierungsrecords dokumentieren (Snapshot + Rollbackplan). **Bestehende E-Mail darf durch den Launch nie ausfallen.**

**E-Mail-Postfächer** sind ein **eigener Drittanbieterdienst** (nicht Websitebetrieb). Vorhandene Postfächer werden erhalten. Bei Erstbedarf: **eine** Ja/Nein-Frage, dann Empfehlung **genau eines** Standardanbieters + Nennung der Fremdkosten; DNS-Einrichtung ist im Websiteprojekt enthalten. Kein Anbieterkarussell.

**Hosting (Kundenseiten):** statische Auslieferung (s. Abschnitt 10) über Managed Hosting in **DE/EU**. Der Kunde wählt kein Hosting.

**Kundenfragen (nur diese):** 1) Domain vorhanden? 2) Wenn ja: welche + wer hat Zugriff? 3) E-Mail mit dieser Domain? 4) Wenn neu: Wunschname oder Vorschläge? 5) Finalen Namen + Inhaberdaten bestätigen.

---

## 7. Kundenablauf (Ende zu Ende)

1. **Lumi-Bedarfsscheck** (5 Themen, ~3 Min., Preis vor Kontaktdaten).
2. **Sartu-Prüfung** (Standardfall Ziel 10–15 Min., höchstens eine gebündelte Rückfrage).
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
- **Orange → Sartu-Prüfung + kurzes Fachmodul:** mehrere Sprachen, mehrere Standorte einer Marke, unklare Buchung, > 1 Conversionpfad, knappe Frist, Freitext nennt Sonderfunktion ohne Auswahl.
- **Gelb → eine Rückfrage:** Widersprüche, „unklar" an paketentscheidender Stelle, Altwebsite unerreichbar/komplex, Domain/Rechte ungeklärt.
- **Standard:** Start / Wachstum / Platzhirsch nach notwendigem Umfang (nicht nach höherem Umsatz). **Platzhirsch** bei ≥ 2 starken Signalen (mehrere Leistungen, mehrere Regionen, Recruiting, Projekte, lokale Auffindbarkeit + mehrere Suchthemen, zentraler Conversionweg); bei besonders starkem Einzelsignal begründet möglich, **nie** nur weil es das Hauptprodukt ist.

**Ergebnis vor Kontaktdaten:** vorläufige Empfehlung + 2–4 kundenspezifische Gründe + Einmalpreis + Schutzpreis + Erstjahreswert + Hinweis auf persönliche Prüfung. Keine Paketwechsel-Buttons, keine Add-ons, keine SEO-Auswahl.

**Kurze Fachmodule vor Angebot** (nur das betroffene): Buchung · Shop/Zahlung · Login · Schnittstelle · Standorte/Marken · Sprache/Barrierefreiheit (je 3–4 Fragen).

**Was der Kunde beantwortet vs. was Sartu selbst recherchiert:**
- **Kunde (nur echte Geschäftsfakten):** Angebot/Leistungen/Kontakt/Öffnungszeiten, Zielgruppe/Einzugsgebiet, echte Belege/Team/Projekte, Bild-/Nutzungsrechte, Domaininhaber/E-Mail-Nutzung, freigegebene Rechtstexte, finale Fakten-/Design-/Textfreigabe.
- **Sartu recherchiert/entscheidet selbst:** Branche aus Beschreibung/Altwebsite ableiten, vorhandene Inhalte extrahieren, Relaunch-/Domainrisiken erkennen, Paketempfehlung + Sitemap, Suchintentionen + Informationshierarchie, Farbrollen/Typografie/Layout/Komponenten, Technik/Hosting/Registrar/Deployment/Monitoring, konkrete SEO-Metadaten + strukturierte Daten. **KI darf keine Fakten, Referenzen, Rechts- oder Fachaussagen erfinden.**

---

## 9. Portal-Vision und Funktionsarchitektur (Kundenportal)

Das Portal ist **kein CMS und kein Website-Baukasten**. Es verwaltet strukturierte Unternehmensdaten, Status, Freigaben und Produktionsaufträge. Es ist der **sichtbare USP** (geführter Prozess statt E-Mail-Chaos).

**Kundenportal-Navigation (8 Punkte):** Übersicht · Angebot & Vertrag · Projekt · Inhalte · Anfragen · Sichtbarkeit · Rechnungen · Hilfe. (Domain, Briefing, Vorschau, Launch erscheinen kontextuell im Projekt.)

**Funktionsbewertung** (Nutzen Kunde / Nutzen Sartu / Aufwand / Baukasten-Risiko / Start-Pflicht). Nur bewertete Kernfunktionen, keine Spielereien:

| Funktion | Kunde | Sartu | Aufwand | Baukasten-Risiko | Wann |
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

**Selbstpflege-Prinzip (ersetzt Änderungsminuten):** Der Kunde bearbeitet **typisierte Datensätze** (`BusinessHours`, `ContactPoint`, `Location`, `Person`, `JobPosting`, `ProjectReference`, `SocialLink`, `PagePublicationState`, `MediaReplacement`) – **nicht** Layout, Farben, Schriften, Komponenten, URLs, Navigation, SEO-Felder, Formulare, Code oder freien Text. Jede Änderung läuft über **Vorschau → Validierung → Version → Bearbeiter**. Öffnungszeit-Änderung aktualisiert nach Freigabe sichtbare Zeiten + Footer + `LocalBusiness`-Schema. Seiten werden **nie hart gelöscht** (raus aus Navigation/Sitemap, interner Redirect oder Archivstatus, reaktivierbar).

**Bewusste Nicht-Funktionen:** kein Drag-and-drop, keine freie Layout-/Farb-/Schriftwahl, keine Plugins/Themes/freien Integrationen, keine freie URL-/Navigationsbearbeitung, kein Quellcodezugriff, keine harte Seitenlöschung.

---

## 10. KI-/Automatisierungslogik

**Drei-Produkt-Architektur:** (1) Sartu-Vertriebswebsite, (2) Sartu-Control-Plane (Portal), (3) Kundenseiten (getrennte, versionierte Codeprojekte).

**Kundenseiten-Tech:** **static-first (Astro empfohlen)**, 1 Repository pro Kunde, gemeinsamer versionierter **Sartu-Starter** + versioniertes **Designsystem-Paket**. Strukturierte Inhalte statt freiem HTML. Formulare/Dynamik über eng begrenzte Portal-APIs. Jede Produktion reproduzierbar, testbar, **exportierbar, rückrollbar**. Der Kundenstand muss **auch nach Vertragsende baubar** sein (notwendige Komponenten eingefroren/vendort; Master-Designsystem/Generatoren/Prompts bleiben intern).

**Produktionspipeline (Zielbild, ab Ausbaustufe 2/3):**
1. Portal friert **versionierte Spezifikation** ein (`site-spec.json`, `business.json`, `services.json`, `proof.json`, `content-plan.json`, `brand.json`, `legal.json`, `seo.json`, `design-manifest.json`, `acceptance.json` – jeweils mit Schema-Version, Projekt-ID, Quelle, Freigabestatus).
2. Isolierter Worker legt Kundenrepo aus Starter + Designsystemversion an.
3. **Anbieteradapter** `WebsiteBuildJob` → **Codex `exec`** (primär) oder **Claude Code (headless)** (Vertrag nennt kein garantiertes Modell).
4. Agent erzeugt Code/Texte/Tests **nur** im Kundenrepo.
5. **Automatische QA-Gates** (14 Pflichtgates: Schema, Build, Code/Lint/Secret-Scan, Links, Formulare, Responsive-Screenshots 360/768/1280/1440, Visuell, Barrierefreiheit, Performance/CWV, SEO, strukturierte Daten, Inhalt/keine Platzhalter/keine verbotenen Garantien, Recht/Consent, Regression).
6. **Menschliche Pflichtprüfung** (passt es zum Unternehmen statt nur zum Template? Botschaft in Sekunden klar? Aussagen = bestätigte Quellen? Platzhirsch sichtbar hochwertiger? keine internen Notizen/Prompts veröffentlicht?).
7. Adminfreigabe → versionierte Kundenvorschau → Abnahme → **separater** Produktions-Launch → versioniert, rückrollbar.

**Harte Sicherheitsgrenzen:** kein Agentenlauf im öffentlichen Webrequest; ephemerer Container pro Job; Schreibzugriff nur auf das eine Kundenrepo + Artefaktordner; **keine** Mollie-/Registrar-/Portal-/Produktions-Zugangsdaten im Agentencontainer; Netzwerk standardmäßig gesperrt (Allowlist); kurzlebige Git-Credentials nur für den Job-Branch; Laufzeit-/Kosten-/Turn-Limit + Abbruch; **Kundenfreitext & externe Websites = nicht vertrauenswürdige Eingaben**. **Kritische Aktionen (Zahlung, Domainregistrierung, DNS, Produktion) führen nur autorisierte Sartu-Dienste/Menschen aus, nie der Agent.**

**Realismus-Hinweis (wichtig):** Vollautonome Website-Erzeugung aus Spec ist der **fragilste** Teil. In Stufe 0/1 gilt: **KI assistiert, Mensch baut/prüft** aus dem Designsystem. Erst wenn das Designsystem stabil, komponentenreich und getestet ist, lohnt echte Orchestrierung. Die internen Std-Obergrenzen (Start 16 h, Wachstum 32 h, Platzhirsch 50 h) sind nur mit starkem Designsystem haltbar.

---

## 11. Adminportal

**Admin-Navigation (12 Punkte):** Cockpit · Anfragen · Angebote · Kunden · Projekte · Websites · Agentenjobs · Sichtbarkeit · Domains · Finanzen · Support · System.

**Kernmodule:** Leads/Lumi-Ergebnisse/Empfehlungsprüfung · Angebots-/Scope-Versionierung/Annahmen/Ausschlüsse · Kunden/Rollen/Einwilligungen/**Audit-Log** · Rechnungen/Mollie/Mandate/Mahnstatus/Schutz · Domains/Kontakte/Verfügbarkeit/Registrierung/Transfers/DNS/Erneuerung · Briefings/Dateien/Rechte/Faktenfreigaben · Projekte/Aufgaben/Fristen/Pausen/Korrekturen/Abnahmen · Repositories/Designsystemversionen/Vorschauen/Deployments/Rollbacks · Codex-/Claude-Jobs (Kosten/Logs/Diffs/QA/Freigaben) · **SEO-/GEO-Flottenzentrale** + vorbereitete Patches · Leads/Support/Störung · Kennzahlen (Anfrage/Abschluss/Produktionszeit/Marge/Support/Betrieb).

**Sicherheit:** rollenbasierte Zugriffe, **Admin-2FA**, Mandantentrennung (Filter nach `kunde_id` aus **Session**, nie aus Request), CSRF auf jedem POST, Rate-Limit auf Auth, gehashte Magic-Link-Tokens (15 Min, einmalig), Upload-Pfade als UUID, Audit-Log bei kritischen Statuswechseln, harte Löschung nur wo gesetzlich/betrieblich nötig.

---

## 12. Zentrales Datenmodell (grobe Skizze)

- **Identität:** `organizations`, `users`, `memberships`, `roles`, `consents`, `audit_events`
- **Vertrieb:** `leads`, `lumi_assessments`, `recommendations`, `clarifications`, `offers`, `offer_versions`, `acceptances`
- **Finanzen:** `invoices`, `invoice_lines`, `payments`, `mandates`, `subscriptions`, `refunds`, `webhook_events`
- **Domain:** `domains`, `domain_contacts`, `domain_quotes`, `registrations`, `transfers`, `dns_snapshots`, `dns_change_sets`
- **Projekt/Inhalt:** `projects`, `brief_versions`, `tasks`, `project_records`, `assets`, `asset_rights`, `feedback_threads`, `approvals`, `content_records`, `page_states`
- **Website-Produktion:** `sites`, `repositories`, `site_versions`, `design_system_versions`, `agent_jobs`, `qa_runs`, `previews`, `deployments`, `rollbacks`
- **Betrieb/Wachstum:** `form_submissions`, `support_cases`, `uptime_events`, `search_properties`, `search_metrics`, `seo_issues`, `seo_patches`, `recommendations`

Alle fachlich wichtigen Statuswechsel erzeugen ein **Audit-Ereignis**. Jobstatus: `queued → preparing → running → validating → admin_review → customer_preview → approved → deploying → live` (+ Fehlerzustände `needs_input`, `qa_failed`, `agent_failed`, `deployment_failed`, `rolled_back`, `cancelled`).

**Tech-Stack-Entscheidung (aufgelöst):** Control-Plane = **Node + PostgreSQL + Redis (Queue/Locks) + S3-kompatibler Objektspeicher DE/EU**, Portal-UI **Next.js + shadcn/ui** (`dashboard-01`, MIT). Kundenseiten = **Astro** static (Basis: eigener Umbau oder **ScrewFast (MIT)**; **nicht** Folex Lite wegen Redistribution-Lizenz). → Die **Supabase/Vercel-Variante** (Juni-Prototyp) und der **PHP-/Flat-File-Ansatz** (`lastenheft_webseite.md`) sind **abgelöst**; Supabase ist als Postgres+Auth+Storage-DE höchstens Übergangslösung (s. Datei 1 / Abschnitt 25).

---

## 13. Website-Struktur (Sitemap in Tiers)

**Kernseiten (Launch):** `/` · `/leistungen` · `/preise` · `/ablauf` · `/briefing` (Lumi) · `/kontakt` · `/ueber-uns` · `/ratgeber` · `/lexikon` · `/impressum` · `/datenschutz` · `/agb` (Platzhalter bis anwaltlich final, dann indexierbar).

**Leistungsseiten (7, Launch):** `/leistung-webdesign` · `/leistung-texte` · `/leistung-seo` · `/leistung-lokales-seo` · `/leistung-wartung` · `/leistung-domain-launch` · `/leistung-portal`.

**Kommerzielle Hubs (nach Launch):** `/website-erstellen-lassen` · `/firmenwebsite-erstellen-lassen` · `/webdesign-agentur` · `/website-relaunch` · `/webdesign-ohne-wordpress`.

**Branchen-Hubs (nur mit echtem Branchentext):** `/webdesign-handwerker` · `/webdesign-praxen` · `/webdesign-kanzleien` · `/webdesign-gastronomie` · `/webdesign-dienstleister`.

**Orts-/Regionsseiten (nur mit Qualitätsgate):** Region-Hub `/webdesign-sachsen`; Tier-1 `/webdesign-dresden`, `/webdesign-leipzig`; Tier-2 `/webdesign-chemnitz`; Region `/webdesign-lausitz`. **Keine sitewide Footer-Ortsliste**, keine Doorway-Massenseiten.

**Ortsseiten-Publikationsgate (auf `index` erst wenn):** Sartu bedient die Region realistisch · Ort kommerziell relevant · klare Suchintention · ≥ 5 ortsspezifische, nicht austauschbare Abschnitte (≥ ~800–1200 Wörter echter Inhalt) · keine Duplicate Titles/Descriptions · Schema behauptet keine falsche Niederlassung · sinnvolle interne Links · redaktionelle Freigabe. Status-Stufen: `draft → noindex_preview → ready_for_review → indexable → retire_or_merge`.

---

## 14. Startseitenstruktur (`/`)

Dunkler, ruhiger Produkt-Hero (kein Stockfoto, kein KI-Gradient). Am unteren Rand ist bereits der nächste helle Abschnitt sichtbar.

1. **Hero** (Deep Ink, zweispaltig): Eyebrow `Webdesign-Agentur für Firmenwebsites` · H1 `Individuell programmierte Firmenwebsites zum Festpreis.` · Lead (Sartu plant/textet/programmiert/betreibt …) · Primär `Bedarf prüfen lassen` · Sekundär `Preise ansehen` · Netto-Hinweis. **Rechte Spalte = echtes/ nachgebautes Portal-Mockup** (Statuskarte „Nächster Schritt: Domain bestätigen", Aufgabenliste, Mini-Preisbox „Platzhirsch – 7.900 € netto", Leiste `Festpreis · 40/30/30 · Schutz L`; Badge **„Musteransicht"**, solange nicht produktionsreif). Trust-Zeile: `Kein WordPress · Texte inklusive · Portal statt E-Mail-Chaos · SEO-/GEO-Basis ab Launch`.
2. **Problem & Entlastung** (Paper): „Eine Website darf nicht Ihr zweiter Job werden." + 3 Boxen: `Sie liefern Fakten` / `Sartu entscheidet` / `Das Portal führt`.
3. **Platzhirsch als Hauptangebot** (Mist): „Drei Website-Ergebnisse. Eine klare Empfehlung." Platzhirsch groß (Badge, Preis, Erstjahr, Signale, CTA `Bedarf prüfen lassen`); Start/Wachstum kompakt (Button `Einschätzen lassen`, **nicht** „auswählen"); Sonderprojekt als schmaler Hinweis.
4. **Leistungslandkarte** (8 breite Service-Zeilen mit Tags, **keine** Kachelwand, **kein** „ab X €/dazubuchen"): Strategie & Struktur · Webdesign & Code · Texte · SEO-/GEO-Basis · Lokale Sichtbarkeit · Domain & Launch · Portal & Freigaben · Rundum-Schutz.
5. **Portal als USP** (Screenshot + `Im Portal` / `Nicht im Portal`-Listen).
6. **SEO/GEO eingebaut** (3 Spalten: Menschen verstehen / Suchmaschinen crawlen / KI-Sucherlebnisse einordnen + Garantie-Disclaimer).
7. **Ablauf** (6-Schritt-Timeline).
8. **Lumi-Einstieg** (Chips: Branche/Region/Ziel/Domainstatus/Umfangssignale/Sonderfunktion).
9. **FAQ** (8 Pflichtfragen; `FAQPage`-Schema nur für sichtbare Fragen).
10. **Abschluss-CTA** (`Bedarf prüfen lassen` / `Preise ansehen` + Unverbindlichkeits-/Netto-Hinweis).

---

## 15. Leistungsseitenstruktur

**Gemeinsames GEO-Template pro Leistungsseite** (Antwort-zuerst, für Menschen + KI): `Kurz gesagt` (1 Absatz mit Kernaussage + Preisanker) → `Für wen passt das?` → `Was ist enthalten?` → `Was ist nicht enthalten?` → `Was kostet es?` (aus zentralem Preisstand) → `Wie läuft es ab?` → `Welche Entscheidung nimmt Sartu ab?` → `FAQ` → CTA. Genau **eine H1**, `Service`-Schema, `FAQPage` nur bei sichtbaren Fragen, Breadcrumb.

**Die 7 Leistungsseiten (H1 + Kernaussage):**
1. `/leistung-webdesign` – „Webdesign für Firmenwebsites, die nicht wie Baukasten aussehen." (individuell programmiert ab 1.490 € netto; kein WordPress).
2. `/leistung-texte` – „Website-Texte aus Stichpunkten, Fakten und echten Belegen." (Sartu erfindet keine Belege; Rechtstexte nicht enthalten).
3. `/leistung-seo` – „SEO-/GEO-Basis für Firmenwebsites, die gefunden und verstanden werden." (Pflichttext: keine Ranking-/KI-Nennungsgarantie).
4. `/leistung-lokales-seo` – „Lokale Sichtbarkeit ohne dünne Ortsseiten." (keine Doorways, echte NAP).
5. `/leistung-wartung` – „Rundum-Schutz für Ihre Website, ohne WordPress-Wartungsstress." (Betrieb, keine Content-/Design-Flatrate, keine Änderungsminuten).
6. `/leistung-domain-launch` – „Domain, E-Mail und Launch ohne Technikstress." (Kunde bleibt Inhaber; bestehende E-Mail wird geschützt).
7. `/leistung-portal` – „Ein Projektportal für Freigaben und kleine Pflege, kein Website-Baukasten." (`Im Portal` / `Nicht im Portal`).

**`/leistungen` (Übersicht):** H1 „Website, Texte, Sichtbarkeit und Betrieb als ein klares System." + Antwortmodul (kein Extra-Baukasten) + Leistungslandkarte (10 Zeilen) + „Was Sie nicht entscheiden müssen" + Tiefe je Paket (Tabelle) + Portal-Brücke + FAQ (`Kann ich einzelne Leistungen dazubuchen?` → im Erstangebot nein).

---

## 16. SEO-/GEO-Strategie

**Grundhaltung (belegt durch Google-Doku):** GEO ist **kein** magischer Zusatz und **kein** Spezial-Schema. Gute KI-Sichtbarkeit = Fortsetzung guter SEO: crawlbare, hilfreiche, konsistente, entitätsklare Inhalte. **Keine** Garantie auf Rankings/Anfragen/Umsatz/KI-Nennungen. `llms.txt` wird angelegt, aber **nicht** als Rankingfaktor beworben.

**SEO-/GEO-Startsystem (im Websitepreis, ab Launch):** Suchintention + Thema je Seite · Antwort-zuerst-Texte aus bestätigten Fakten · sprechende URLs (Bindestriche, keine Umlaute) · genau eine H1, saubere Überschriften · interne Links als echte Links · Title/Description/Canonical/OG/Robots · Breadcrumb + `BreadcrumbList` · `Organization`+`WebSite` global, `Service`/`FAQPage`/`Article`/`DefinedTerm` seitenweise (nur bei sichtbarer Entsprechung) · XML-Sitemap, robots.txt, 404, Redirect-Plan · echte NAP, `LocalBusiness` **nur** bei berechtigtem Standort · Performance (CWV: LCP < 2,5 s, INP < 200 ms, CLS < 0,1; AVIF/WebP + srcset, Hero nicht lazy + `fetchpriority=high`, self-hosted WOFF2 `font-display:swap`) · Bild-SEO (echte Bilder, sinnvolle Alt-Texte, sprechende Dateinamen) · Search Console + Bing Webmaster + Sitemap einreichen, IndexNow optional.

**Laufender Schutz (in Schutz S/M/L):** technische Suchgesundheit – Erreichbarkeit, Crawlbarkeit, Sitemap, Links, Canonicals, Schema aus bestätigten Fakten, technische Regressionen. **Kein** stillschweigender Content-Auftrag.

**Späterer Sichtbarkeitsausbau (datenbasiert, ein Folgeangebot):** schwache Seiten anhand echter Suchanfragen verbessern, veraltete Aussagen aktualisieren, interne Verlinkung schärfen, belegbare neue Themen/Regionen aufbauen, Antworten auf echte Kundenfragen ergänzen. **Kein** SEO-Menü, keine Stufen, keine Minuten.

**SEO-/GEO-Flottenzentrale (Admin):** Datenquellen (eigener Crawler, Search Console API, Performance/Uptime, Portalfakten, Conversion-Events nach Einwilligung). Prüfgruppen `critical/warning/opportunity/information`. **Automatisch reparierbar** (deterministisch): Sitemap neu erzeugen, interne Links nach Deaktivierung anpassen, technische Canonical-/Robots-/Metadatenverletzungen gegen feste Regeln, strukturierte Daten aus bestätigten Fakten, defekte Bildableitungen. **Nur als Entwurf** (Freigabe nötig): neue/geänderte Texte, neue Orts-/Leistungs-/Ratgeberseiten, Aussagen zu Preis/Qualifikation/Gesundheit/Recht/Ergebnis, Wettbewerbsvergleiche.

---

## 17. Lexikon- und Ratgeber-Konzept

**Ratgeber (`/ratgeber`)** – Informationssuchen abholen, in kommerzielle Seiten führen. Jeder Artikel: H1 mit Suchintention · **Kurzantwort sofort oben** · Update-Datum · Autor/Prüfhinweis · Beispiele · Tabellen/Entscheidungslogik · interne Links · CTA zu Lumi/Leistung · `Article`-Schema. **Startartikel (3–6):** Was kostet eine Firmenwebsite? (→ `/preise`) · Website erstellen lassen – Ablauf (→ `/ablauf`) · One-Pager oder mehrseitig? · Website ohne WordPress (→ `/leistung-webdesign`) · Lokales SEO für Unternehmen · Domainwechsel ohne E-Mail-Ausfall. **Nicht:** tägliche KI-Artikel ohne eigene Perspektive, Themen außerhalb der Zielgruppe, erfundene Statistiken, erzwungene Wortzahl.

**Lexikon (`/lexikon`)** – kuratierter Begriffs-Hub für Entitäten-/GEO-Aufbau, **kein** SEO-Begriffsfriedhof. **Start 40–60 Begriffe** (nicht 300). Hub: Suchfeld · alphabetische Navigation · Kategorien (Website & Struktur / SEO & GEO / Technik & Performance / Domain & E-Mail / Portal & Projekt / Betrieb & Sicherheit) · Begriffsliste · CTA. **Begriffseite (8 Teile):** H1 = Begriff · Kurzdefinition (2–3 Sätze) · Warum wichtig für Firmenwebsites? · Beispiel aus Sartu-Sicht · Typischer Fehler · Wie Sartu damit umgeht · Verwandte Begriffe · Link zur passenden Leistungsseite. Schema `DefinedTerm`/`DefinedTermSet`, sonst `Article`/`WebPage`. **Startbegriffe** u. a.: Firmenwebsite, One-Pager, Landingpage, Relaunch, SEO, GEO, Local SEO, Suchintention, Title Tag, Meta Description, Canonical, Sitemap, robots.txt, noindex, 301, Core Web Vitals, LCP/INP/CLS, Lazy Loading, Schema.org, LocalBusiness, FAQPage, Breadcrumb, Domain, DNS, Registrar, MX/SPF/DKIM/DMARC, Hosting, SSL, Backup, Monitoring, WordPress, CMS, statische Website, Designsystem, Briefing, Abnahme, Korrekturrunde, Festpreis, Scope.

---

## 18. Content-Cluster (interne Verlinkung)

**Cluster 1 – Kaufabsicht (Money):** `/preise`, `/leistung-*`, kommerzielle Hubs, Orts-/Branchen-Hubs. Ziel-Keywords: „Website erstellen lassen", „Firmenwebsite … Festpreis", „Webdesign ohne WordPress", „Webdesign {Ort}".
**Cluster 2 – Beratung (Ratgeber):** Kosten, Ablauf, One-Pager-vs-mehrseitig, ohne-WordPress, lokales SEO, Domainwechsel → verlinken **immer** auf ≥ 1 Money-Seite.
**Cluster 3 – Entität/Definition (Lexikon):** verlinkt auf Leistungen + verwandte Begriffe + passende Ratgeber.
**Cluster 4 – Vertrauen/Marke:** `/ueber-uns`, `/ablauf`, `/leistung-portal`, echte Referenzen (sobald vorhanden).

**Verlinkungsregeln:** jede kommerzielle Seite → `/briefing` + `/preise`; jede Leistung → passende Ratgeber-/Lexikonseiten; Ratgeber → ≥ 1 kommerzielle Seite; Startseite verlinkt **max. 3–5** wichtigste Orte/Regionen (über Region-Hubs), **kein** Footer-Ortsverzeichnis. Gute Ankertexte (`Website-Pakete ansehen`), keine `hier/mehr/klicken`, keine Keyword-Ketten.

---

## 19. Bild- und Screenshot-Konzept

**Grundsatz:** Bilder machen Sartu **glaubwürdiger**, nicht dekorativ. Prioritätsreihenfolge: 1) echte **Portal-Screens** · 2) echte **Muster-Kundenseiten** · 3) echtes Foto von Nils/Arbeitsplatz · 4) neutrale lizenzierte Fotos nur wenn konkret hilfreich · 5) KI-Bilder nur für neutrale abstrakte Motive, **nie** als Kundenbeweis. **Verboten:** austauschbare Handschlag-/Laptop-/Callcenter-Stockbilder, dunkle weichgezeichnete Atmosphärenbilder, Fake-Logowolken, Fake-KPI-Dashboards, Gradient-Orbs.

**Benötigte Portal-Screens (als `Musteransicht` markiert bis produktionsreif):** `sartu-portal-cockpit-muster.webp` (Hero) · `-briefing-` · `-domain-` · `-vorschau-feedback-` · `-zahlung-` · `-pflege-`. Leistungen: Systemdiagramm/UI-Modul statt Stockfoto. Ablauf: Portal-Timeline. Über uns: echtes Foto von Nils (kein Fake-Teamfoto; Platzhalter nie als echtes Foto tarnen). Ratgeber: einfache Diagramme/Tabellen/Checklisten.

**Bildregeln:** WebP/AVIF, responsive Quellen, feste `width`/`height`, Hero nicht lazy, restliche lazy, keine Textinfo nur im Bild, echter Alt-Text.

---

## 20. Designprinzipien (aufgelöste Entscheidung)

**Was „schön" bei Sartu heißt:** klare visuelle Hierarchie, konsequentes Raster, ruhige Typografie, wenige gezielte Farben, Bilder nur wo sie den Prozess erklären, konkrete Angebotslogik statt generischer Benefits, Portal als Arbeitswerkzeug statt Marketinggrafik.

**Farb-Entscheidung (Widerspruch aufgelöst):** Es gibt **eine** Markenpalette und **eine** funktionale Portal-Palette.

| Rolle | Wert | Verwendung |
|---|---|---|
| Ink (Marke) | `#14181D` | Text, dunkle Flächen, Navigation |
| Deep Ink | `#0E1216` | Hero-Hintergrund |
| Paper / Ivory | `#FFFFFF` / `#F6F4EF` | Hauptflächen (warmes Ivory statt kaltes Weiß = editorial) |
| Mist | `#F3F6F4` | ruhige Bänder, Tabellenwechsel |
| Line | `#D8DFDC` | Linien/Felder |
| **Sartu Teal** | `#0B7F73` | **Markenfarbe**, aktive Zustände, wichtige Akzente |
| **Oxide / Rostrot** | `#B55E2D` | **Signatur-Akzent** (der „Prozessschnitt" aus dem Logo), sparsam |
| Signal Blue | `#2F6FED` | Links/Info (nur Portal-UI) |
| Amber | `#A8660A` | Hinweis/Handlungsbedarf (Portal-UI) |
| Red | `#B63A3A` | Fehler/kritisch (Portal-UI) |

**Bewusst verworfen:** das **Neon-Signal-Grün `#A8E000`** (widerspricht der Anti-KI-/Editorial-Haltung, wirkt generisch-SaaS) und **Navy + Lime** (Altstand). CTA-Akzent = **Oxide/Rostrot** (Markenanker aus dem Logo) auf ruhigem Grund, nicht Neongrün. Keine Verläufe, Leuchtflecken oder dekorativen Orbs.

**Logo:** Wortmarke „SARTU" mit einem erinnerbaren Detail (Favoriten aus den Boards: **T-02 „geschnittener Beam"** oder **S-02 „konstruiertes Portal-S"** – schlicht, abstrakt, favicon-tauglich; roter „Prozessschnitt" als wiederkehrendes Markendetail/Pattern). Ohne brauchbares Kundenlogo → hochwertige typografische Wortmarke (keine erzwungene Logoentwicklung im Standard).

**Typografie:** klare Grotesk für UI/Fließtext (Empfehlung: **Inter** oder **Instrument Sans**, self-hosted), H1 kräftig aber nicht aufgeblasen, tabellarische Ziffern für Preise, keine negative Laufweite, Fließtext ~18 px Desktop / ~16 px mobil. Editorial-Charakter primär über **Raster, Weißraum und Ivory**, nicht über verspielte Display-Schriften. (Optional dezente editorial Serif nur für H1 – s. offene Entscheidung 25.)

**Form:** Radius 6–8 px, Buttons rechteckig (nicht pillenförmig als Standard), Karten nur für Pakete/FAQ/Portalmodule/wiederholte Datensätze, Abschnitte als volle Bänder, Icons aus einer Bibliothek (Lucide), keine „Karten in Karten".

**Template-Basis:** Website = **Astro**, eigener Umbau **oder ScrewFast (MIT)** (nicht Folex Lite – Lizenz). Portal = **Next.js + shadcn/ui `dashboard-01` (MIT)**. Kein Tailwind-Studio-Klon.

---

## 21. Trust-Elemente

- **Festpreis + Erstjahreswert** transparent (senkt Kaufangst).
- **Portal-Screens** als Produktbeweis (der stärkste ehrliche Beweis am Start).
- **Klartext-Grenzen:** „Was ist enthalten / nicht enthalten / was entscheidet Sartu" auf jeder Leistungsseite.
- **Ehrliche Disclaimer:** keine Ranking-/KI-Nennungs-/Umsatzgarantie, „Sartu leistet keine Rechtsberatung", „KI wird genutzt, Ergebnisse werden geprüft".
- **Echte Referenzen/Case Studies**, sobald 2–3 Projekte live sind (→ Kaltstart-Priorität, s. Abschnitt 24). **Keine** Fake-Referenzen/-Bewertungen/-Logos/-Adressen.
- **Verantwortlichkeit:** echtes Foto/Name (Nils), klare Haltung auf `/ueber-uns`.
- **FAQ** als Einwandbehandlung (Paket selbst wählen? Texte? warum keine Add-ons? Domain/E-Mail? später selbst ändern? SEO enthalten? warum kein WordPress? Rankinggarantie?).

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
- **Rechte/Export:** nach vollständiger Zahlung Nutzungsrechte am konkreten Website-Stand + Sartu-Texten + kundenspezifischem Design; Domaininhaber = Kunde; dokumentiert **baubarer** Export ohne Abhängigkeit vom privaten Sartu-Master; **Exportweg vor erstem Verkauf praktisch testen** (sonst nicht mit „problemlosem Umzug" werben).

**Scope-Creep-Verhinderung:** Empfehlung + Sitemap stehen vor Auftrag fest; Standardpreis wird nicht mit „notwendigen Extras" aufgeweicht; Feedback wird gebündelt (parallele E-Mail/Telefon/Messenger zählen nicht als eigene Kanäle); neue Ziele werden getrennt vom Mangel behandelt (ein Folge-Festpreisprojekt); Selbstpflege ersetzt Änderungsminuten; Agentenjobs haben Kosten-/Zeit-/Werkzeuggrenzen.

---

## 23. MVP vs. spätere Ausbaustufen

> Kernprinzip: **Nachfrage und Lieferfähigkeit zuerst beweisen, Automatisierung zuletzt.** Das kanonische „alles vor Marktstart" wird als **Zielbild** beibehalten, aber in liefernde Stufen zerlegt.

**Stufe 0 – Manuell liefern & Referenzen erzeugen (jetzt, Wochen, nicht Monate):**
- Öffentliche Website (Kernseiten + 7 Leistungsseiten + Ratgeber-Start + Lexikon-Start) auf Astro, launchfähig.
- **Minimal-Portal:** Anfrage-Inbox (Lumi-Payload), Projektstatus/Timeline, Datei-Upload, Vorschau-Link, Rechnung + **Mollie-Zahlungslink** (Zahlungslinks reichen, noch kein Abo-Automatismus). *(Der bereits gebaute Supabase-Stufe-1-Stand kann diese Rolle übergangsweise erfüllen – s. offene Entscheidung.)*
- Produktion **manuell + KI-assistiert** (Mensch baut aus Designsystem). Domain/DNS via INWX **manuell**. Buchhaltung via lexoffice/sevDesk.
- **Ziel: 2–3 echte Referenzkunden** live → echte Case Studies + echte Portal-Screens (die die Website ohnehin braucht).

**Stufe 1 – Portal härten:** Angebot/Annahme im Portal, **Mollie-Abo/Mandat** für Schutz (E2E getestet), adaptives Onboarding, Lead-Inbox, strukturierte Selbstpflege (Öffnungszeiten/Kontakt/Seitenstatus), Support, Audit-Log, Admin-2FA, Mandantentrennung. Migration/Konsolidierung auf den Ziel-Stack (Node/PG).

**Stufe 2 – Produktion teilautomatisieren:** versionierte Spec → **assistierter** Build → automatische QA-Gates → Adminfreigabe → versionierte Vorschau/Deployment/Rollback. Selbstpflege Team/Stellen/Projekte. SEO-/GEO-Flotte (technische Checks + Patch-Entwürfe). INWX-Lifecycle im Portal.

**Stufe 3 – Skalieren:** echter Anbieteradapter (Codex/Claude headless orchestriert), programmatische Ortsseiten mit noindex-Stage + Freigabegate, Branchen-Hubs, SEO-Ausbau nach Search-Console-Daten, weitere Regionen.

**Nie Teil des Marktstarts:** autonome Werbekampagnen, freie Kundenseiten-Erstellung, Wettbewerber-Scraping ohne Zweck, Umsatzprognosen, Social-Redaktionssystem, Plugin-Marktplatz.

---

## 24. Konkrete nächste Umsetzungsschritte

**A. Fundament klären (diese Woche):**
1. **Einen** Preis-/Scope-Stand als Single Source of Truth festlegen (`pricing.json`/`prices.js`) und `sartupaketepreise.md` + `sartulastenheftwebsite.md` in `konzepte/_archiv/` verschieben (als **veraltet** markieren) – gegen Wiederverwendung.
2. Stack-Entscheidung dokumentieren: Website **Astro**, Portal **Next.js/shadcn**, Control-Plane **Node/PostgreSQL**; Umgang mit dem Supabase-Prototyp entscheiden (behalten als PG-Backend vs. migrieren – s. 25).
3. **Eine** Palette + Ansprache („Sie") + Logo-Favorit fixieren; verbotene Wörter-/Anti-KI-Regeln als Lint/QA-Check.

**B. Website launchen (2–4 Wochen):**
4. Astro-Basis aufsetzen (ScrewFast/eigener Umbau), globales Layout + Design-Tokens + `sartu-web-v1`-Komponenten.
5. Kernseiten + 7 Leistungsseiten (GEO-Template) + 3–6 Ratgeber + 20–40 Lexikonbegriffe bauen; **echte** NAP/Impressum (nach Kanzlei), sitemap/robots/llms.txt/OG, Search Console + Bing.
6. Portal-Screens als **Musteransicht** produzieren (aus dem echten Minimal-Portal, nicht Fake).
7. ENDKONTROLLE-Profil **SARTU-PUBLIC** vollständig grün (keine Add-on-/Minuten-/Alt-Preis-Reste, keine Privatkunden-Formulierungen).

**C. Verkaufen & liefern (parallel/danach):**
8. Minimal-Portal live (Anfrage → Angebot → Mollie-Zahlungslink → Upload/Vorschau).
9. **Lead-Gen-Plan** (fehlt bisher!): Local SEO + Google-Unternehmensprofil + gezielte SEA auf „Website erstellen lassen {Ort}" + Empfehlungen/Netzwerk. Das Modell erzeugt keine Nachfrage von selbst.
10. 2–3 Referenzkunden manuell durch den vollen Prozess führen → Case Studies + Screens.

**D. Vor „echten" Standardkunden (Zielbild-Gates):** Mollie E2E (Zahlung/Mandat/Wiederholung/Fehlschlag/Erstattung), INWX OT&E (Registrierung/Transfer/DNS-Snapshot/Übergabe), Export + Rollback praktisch getestet, AGB/AVV/Datenschutz/KI-Verarbeitung anwaltlich, ein Musterkunde Ende-zu-Ende.

---

## 25. Offene Entscheidungen (nur die wirklich nötigen)

1. **Supabase-Prototyp behalten oder migrieren?** Der Juni-Stand (Supabase Frankfurt: Auth/PostgreSQL/Storage, RLS, live getestet) erfüllt „PostgreSQL + Identität + Storage in DE/EU" bereits. **Empfehlung:** für Stufe 0/1 **behalten** (schneller live, Sicherheit aus RLS), Ziel-Node-Control-Plane erst ab Stufe 2, wenn Queues/Worker/Agentenjobs wirklich gebraucht werden. **Nicht** parallel zwei Portale pflegen.
2. **Buchhaltung: lexoffice oder sevDesk?** (API-Anbindung, GoBD/E-Rechnung). Kaufmännische Entscheidung, vor Stufe 1.
3. **Typografie final:** reine Grotesk (Inter/Instrument Sans) vs. Grotesk + dezente editorial Serif für H1. Empfehlung: mit Grotesk starten, Serif optional testen.
4. **Startregion & echte NAP:** Dresden/Sachsen bestätigt? Öffentliche Geschäftsanschrift vorhanden (für `LocalBusiness`/Impressum) oder nur Kontaktanschrift?
5. **Solo vs. kleines Team – ehrliche Selbstdarstellung** und daraus abgeleitete **Kapazität/Projekte-pro-Monat** (bestimmt, ob der Portal-Vollausbau realistisch neben der Produktion läuft oder Hilfe/Outsourcing braucht).
6. **AGB/Garantie:** ob überhaupt eine (sauber formulierte) Zufriedenheitszusage als Verkaufsargument gewünscht ist – sonst weglassen.

---

*Ende Masterkonzept. Die kritische Bewertung, Marktanalyse mit Quellen und Herleitung dieser Entscheidungen steht in `CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md`.*
