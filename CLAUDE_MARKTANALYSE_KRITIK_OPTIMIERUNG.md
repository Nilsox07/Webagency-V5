# SARTU – Marktanalyse, Kritik & Optimierung

**Erstellt von:** Claude (Opus) · **Stand:** 24.07.2026
**Grundlage:** alle Konzeptdateien in `konzepte/`. Umsetzungsentscheidungen im Schwesterdokument `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`.

**Kennzeichnung der Aussagen:** `[BELEGT]` = mit Quelle/Datei belegt · `[ANNAHME]` = plausible, nicht bewiesene Grundannahme · `[EINSCHÄTZUNG]` = meine fachliche Bewertung.

---

## 1. Executive Summary

Das SARTU-Konzept ist **positionierungsseitig überdurchschnittlich gut durchdacht** – klarer als der typische Agenturkonfigurator, mit einer sauberen Angebotslogik (Festpreis, wenig Auswahl, geführter Prozess, Betrieb inklusive) und einer bemerkenswert ehrlichen SEO/GEO-Haltung (keine Ranking-/KI-Garantien). Die schriftliche Substanz (GESCHÄFTSMODELL, LUMI, DESIGNSYSTEM, WEBSEITENKONZEPT) ist auf Berater-Niveau.

**Das Problem ist nicht die Idee, sondern der Umfang der geplanten Erstumsetzung und die Versionsdrift der Unterlagen.** Drei Punkte entscheiden über Erfolg oder Scheitern:

1. **Über-Engineering des Portals** ist der größte Fehler. Das kanonische Modell verlangt, das **komplette Control-Plane** (Mollie-Abo, INWX-Domainlebenszyklus, KI-Produktions-Orchestrierung mit isolierten Agenten-Workern, QA-Gates, Deployments, Rollback, SEO/GEO-Flotte, Admin-Finanzen) **vollständig vor dem ersten Standardkunden** zu bauen. Für ein Ein-Personen-/Kleinstteam ist das ein zweites Softwareprodukt und ein realer Launch-Blocker.
2. **Versionsdrift**: Es existieren **drei** widersprüchliche Preisstände, **drei** Tech-Stacks und **mehrere** Design-Paletten in den Unterlagen. Ohne eine einzige Wahrheitsquelle baut ein Umsetzer das Falsche.
3. **Kein Nachfrage-Motor**: Das Konzept beschreibt lückenlos, wie eine Anfrage *verarbeitet* wird, aber nirgends, wie eine Anfrage *entsteht*. Es fehlt der Vertriebs-/Lead-Gen-Plan und – am Start – jede echte Referenz.

**Empfehlung in einem Satz:** Die Positionierung so übernehmen, die Erstumsetzung radikal verschlanken (manuell liefern, Portal in Stufen, KI zunächst assistierend), **eine** Wahrheitsquelle erzwingen, und zuerst 2–3 echte Referenzkunden gewinnen, bevor irgendetwas automatisiert wird.

---

## 2. Hartes Gesamturteil

**Was wirklich gut ist:**
- Angebotslogik „wenig Auswahl, klare Empfehlung, Gesamtfestpreis" ist marktfähig und differenzierend. `[EINSCHÄTZUNG]`
- Netto/B2B-Fokus + Privatkundenausschluss ist rechtlich und kommunikativ sauber gedacht. `[BELEGT: GESCHAEFTSMODELL §3]`
- Streichung von Änderungsminuten zugunsten typisierter Selbstpflege ist klug (weniger Abrechnungsstreit, bessere Marge). `[BELEGT: DETAILKONZEPT §8]`
- SEO/GEO ehrlich, ohne Garantien, mit korrekter Google-Grundlage. `[BELEGT: WEBSEITENKONZEPT §2]`
- Sicherheitsdenken (Mollie-Serverstatus statt Redirect, isolierte Agenten, Audit-Log, Mandantentrennung) ist auf gutem Niveau. `[BELEGT: DESIGNSYSTEM §4/§14]`

**Was schonungslos gesagt werden muss:**
- **Das Konzept ist verliebt in das Portal und die KI-Automation, nicht in den ersten verkauften Euro.** `[EINSCHÄTZUNG]` Die Reihenfolge „erst das perfekte System, dann Kunden" ist die klassische Falle technischer Gründer. Wer 6–12 Monate ein Portal baut, bevor er verkauft, verbrennt Zeit und Geld ohne Marktvalidierung.
- **Die Unterlagen widersprechen sich massiv** (s. Abschnitt 4). Das ist nicht kosmetisch – ein Umsetzer, der `sartulastenheftwebsite.md` befolgt, baut ein anderes, veraltetes Unternehmen als eines, das `GESCHAEFTSMODELL.md` befolgt.
- **„Kein WordPress" ist als USP schwach.** `[EINSCHÄTZUNG]` Kein Zielkunde wacht mit dem Wunsch „bloß kein WordPress" auf. Es ist ein interner Effizienz- und Wartungsvorteil, kein Kaufgrund. Verkauft werden muss die *Entlastung*, nicht die Technik.
- **Bus-Faktor 1.** Bei Solo-Betrieb ist Krankheit/Ausfall existenzbedrohend – für SLAs („Erstreaktion 8 Geschäftsstunden") und für laufenden Betrieb von Kundenseiten. `[EINSCHÄTZUNG]`

Fazit: **Tragfähig – aber nur mit umgekehrter Reihenfolge und drastischer MVP-Verschlankung.** In der geplanten „alles zuerst"-Form droht ein technisch beeindruckendes System ohne Kunden.

---

## 3. Marktanalyse mit Quellen

> Hinweis: Dieser Abschnitt trennt belegte Quellen von Markteinschätzungen. Die DACH-Preisspannen sind in 3.6 mit Quellen hinterlegt; wo eine konkrete URL genannt ist, ist sie überprüfbar. Anbieterpreise ändern sich – vor Verwendung an der Quelle prüfen.

### 3.1 Marktsegmente & Preisniveaus (Deutschland, KMU-Firmenwebsite)

| Segment | Typischer Preis (einmalig) | Betrieb/laufend | Charakter |
|---|---|---|---|
| **Website-Baukästen** (Wix, Jimdo, IONOS, Squarespace) | 0 € Aufbau (Eigenleistung) | ~10–50 €/Mon. | Selbstbau, Vorlagen, Kunde macht alles selbst |
| **KI-Website-Builder** (Wix AI, Jimdo Dolphin, GoDaddy Airo, Hostinger, Durable, 10Web) | 0–gering | ~10–50 €/Mon. | KI generiert Grundgerüst, generischer Look |
| **Freelancer** (DACH) | ~800–4.000 € | oft keiner/gering | Preis-Leistung, aber Bus-Faktor 1, wenig Prozess |
| **Kleine/mittlere Agentur (WordPress)** | ~1.500–8.000 € (KMU) | Wartung ~30–150 €/Mon. | Marktstandard, WordPress-dominiert |
| **Größere Agentur / individuell** | ~8.000–25.000 €+ | höher | Umfangreiche Projekte, Enterprise |
| **SARTU (geplant)** | 1.490 / 3.900 / 7.900 / ab 12.500 € | 59 / 129 / 249 €/Mon. | individuell programmiert + Betrieb + Portal |

`[EINSCHÄTZUNG]` für die Spannen; konkrete Anbieterpreise/Quellen in Abschnitt 3.6.

### 3.2 Zahlungsmodelle im Markt `[BELEGT]`
- **50/50** bei kleineren Webprojekten und **40/30/30 / 30/40/30** bei größeren ist verbreitete Agenturlogik.
  - Conceptum, AGB Website-Erstellung (50/50): https://conceptum.at/allgemeine-geschaftsbedingungen-fur-die-website-erstellung/ (Projektunterlage, zit. Juli 2026)
  - XezMet (30/40/30): https://www.xezmet.at/ (Projektunterlage, zit. Juli 2026)
- Preisspannen-Indikatoren (2026): Rheinspace https://rheinspace.de/insights/website-erstellen-lassen-kosten/ · Wyreframe https://www.wyreframe.de/blog/was-kostet-eine-website · Adfera https://adfera.de/ratgeber/webdesign-kosten-rechner/ (alle als Marktindikator, zit. Juli 2026)
- **SARTU-Bewertung:** 50/50 und 40/30/30 sind marktkonform. Das **10-Tage-Zahlungsziel** ist straffer als übliche 14 Tage, aber durch digitale Mollie-Zahlung + Slot-nach-Zahlung vertretbar. `[EINSCHÄTZUNG]`

### 3.3 WordPress-Dominanz & „Kein-WordPress"-Positionierung
- WordPress ist im KMU-Markt das mit Abstand häufigste CMS (~43 % aller Websites, ~60 %+ CMS-Anteil; bei kleinen Agenturen/Freelancern noch höher). `[BELEGT: W3Techs, s. 3.6]`
- „Static-first / kein WordPress" ist technisch überlegen (Geschwindigkeit, Sicherheit, keine Update-Last), aber **kein Verkaufsargument beim Endkunden** – es ist ein Betreiber-Vorteil. `[EINSCHÄTZUNG]`
- **Konsequenz:** Nicht „ohne WordPress" plakatieren, sondern „keine Update-, Plugin- und Sicherheitssorgen für Sie; schnell und wartungsarm".

### 3.4 KI-Website-Builder als Wettbewerb & Bedrohung
- KI-Builder senken den Marktpreis für „irgendeine Website" gegen ~0 €. Das drückt v. a. auf das **Start-Paket (1.490 €)**. `[EINSCHÄTZUNG]`
- Gegenargument (SARTUs Chance): KI-Builder erzeugen **generische, austauschbare** Ergebnisse ohne Strategie, ohne echte Texte, ohne Betrieb, ohne Verantwortung. SARTUs Anti-KI-Optik + „echtes Ergebnis + Betrieb + eine Verantwortung" ist die richtige Antwort. `[EINSCHÄTZUNG]`
- **Risiko:** SARTU nutzt selbst KI zur Produktion. Wenn die Ergebnisse „nach KI aussehen", kollabiert die Differenzierung. Der Anti-KI-Design-Check ist deshalb kein Kosmetik-, sondern ein Überlebensthema. `[BELEGT: DESIGN_ANTI_KI_CHECK; EINSCHÄTZUNG zur Bedeutung]`

### 3.5 Wartungs-/Betriebsmodelle
- „Rundum-sorglos"-Wartung im deutschen KMU-Markt liegt grob bei **30–150 €/Mon.**, Premium-/Betreuungspakete bis **300 €+**. `[EINSCHÄTZUNG; s. 3.6]`
- SARTUs 59/129/249 € ist **marktkonform bis leicht premium** und – weil ohne Änderungsminuten – margenstark. `[EINSCHÄTZUNG]`
- Wiederkehrender Umsatz ist im Markt anerkannt „das eigentliche Geschäft" der Agentur (planbarer Cashflow, hohe Marge). `[EINSCHÄTZUNG; deckt sich mit sartupaketepreise.md-Notiz]`

### 3.6 Vertiefte Marktdaten

> Preise ändern sich; Anbieterpreise sind mit „Stand 2026, ca." zu lesen und vor Verwendung an der jeweiligen Preisseite zu verifizieren.

**CMS-/WordPress-Verteilung `[BELEGT]`**
- WordPress liegt seit Jahren bei ~43 % **aller** Websites und ~60 %+ **Marktanteil unter den CMS**. Quelle: W3Techs, https://w3techs.com/technologies/overview/content_management (laufend aktualisiert, zit. 07/2026).
- Bedeutung: Der Zielmarkt ist WordPress-gesättigt. „Ohne WordPress" ist eine **Betreiber-Entlastung**, kein Endkunden-Kaufgrund → entsprechend kommunizieren.

**Baukästen & KI-Builder (Monatsabo, ca. 2026) `[EINSCHÄTZUNG]`**
- Wix (inkl. AI/ADI) ~10–30 €/Mon.: https://de.wix.com/ · Squarespace ~16–49 €/Mon.: https://www.squarespace.com/pricing · Jimdo (inkl. „Dolphin"-KI) ~9–39 €/Mon.: https://www.jimdo.com/de/ · IONOS MyWebsite ~1–45 €/Mon.: https://www.ionos.de/ · GoDaddy Airo / Hostinger (KI) ab ~2,99 €/Mon.: https://www.hostinger.de/ · Durable/10Web/Framer/Webflow ~5–40 $/€ pro Monat.
- Muster: KI-Builder erzeugen in Minuten ein Grundgerüst zum Quasi-Nulltarif, aber **generisch, ohne Strategie/echte Texte/Betrieb/Verantwortung**. Das drückt Preis-Erwartungen v. a. beim Start-Segment und macht SARTUs Anti-KI-Optik + „echtes Ergebnis + Betrieb" zur Kern-Differenzierung.

**Freelancer & Festpreisangebote (DACH) `[EINSCHÄTZUNG/BELEGT-nah]`**
- IT-/Web-Freelancer-Durchschnittsstundensatz laut Preisindex ~90–100 €/h (gesamt IT); Webdesign speziell für KMU oft ~50–90 €/h. Quelle: freelancermap Preisindex, https://www.freelancermap.de/preisindex (laufend, zit. 07/2026).
- Festpreis-Firmenwebsites von Freelancern/kleinen Agenturen typischerweise ~800–4.000 €; „Website mieten/Abo" (monatlich statt einmalig) existiert, ist aber wegen Bindung/Gesamtkosten umstritten.
- SARTU-Einordnung: liegt bewusst **über** dem Freelancer-Preis, verkauft aber Prozess (Portal), Betrieb und „eine Verantwortung" statt Bus-Faktor-1-Freelancer.

**Wartungs-/Betriebsmodelle (DE) `[EINSCHÄTZUNG]`**
- KMU-Wartung grob 30–150 €/Mon.; „Rundum-sorglos"/Betreuung bis 300 €+; enthalten meist Hosting, SSL, Backups, Updates/Security, Monitoring, begrenztes Änderungskontingent. SARTUs 59/129/249 € ist marktkonform bis leicht premium, ohne Änderungsminuten margenstark.
- Belege für 50/50- und 40/30/30-Zahlungslogik + Preisspannen: conceptum.at, xezmet.at, rheinspace.de, wyreframe.de, adfera.de (Projektunterlagen §20, zit. 07/2026).

**B2B-/Handwerks-Erwartungen `[EINSCHÄTZUNG]`**
- Erwartet werden: schnelle mobile Darstellung, klarer Kontaktweg, lokale Auffindbarkeit (Google-Unternehmensprofil), Vertrauenssignale (Referenzen/Jahre/Qualifikationen), Ladezeit. Lokale Suche („… in der Nähe") ist für regionale Betriebe der wichtigste Sichtbarkeitskanal → Local SEO + Google-Unternehmensprofil sind Pflicht, nicht Kür.
- Entscheidungsverhalten: preissensibel, aber vertrauensgetrieben; Festpreis + klarer Ablauf senken Kaufangst stärker als das billigste Angebot.

**Recht `[BELEGT]`**
- BFSG (Barrierefreiheitsstärkungsgesetz), in Kraft seit 28.06.2025. Offizielles Portal: https://www.barrierefreiheit-dienstekonsolidierung.bund.de/ · Gesetzestext: https://www.gesetze-im-internet.de/bfsg/ (verifiziert 07/2026). Betrifft bestimmte an **Verbraucher** gerichtete Produkte/Dienstleistungen (u. a. E-Commerce/Online-Vertragsschluss). **Kleinstunternehmen** (< 10 Beschäftigte **und** ≤ 2 Mio. € Jahresumsatz/-bilanz) sind für **Dienstleistungen** von der Barrierefreiheitspflicht ausgenommen; **reine B2B-Angebote** fallen nicht unter das BFSG – **sofern klar erkennbar ist, dass sie sich nur an Unternehmen und nicht an Verbraucher richten**. → Für SARTUs typische B2B-Firmenwebsites meist **keine** BFSG-Pflicht (die ohnehin nötige klare B2B-Kennzeichnung stützt genau das); sobald eine Kundenseite Shop/Buchung/Online-Vertrag **für Verbraucher** enthält, greift WCAG 2.1 AA. Barrierefreiheits-Basis dennoch immer bauen (Qualität), nicht als „Pflicht für alle" verkaufen. Anwendbarkeit pro Kunde anwaltlich prüfen.

### 3.7 Rechtlicher Marktrahmen `[BELEGT]`
- **Netto-/B2B-Preisangaben:** PAngV https://www.gesetze-im-internet.de/pangv_2022/BJNR492110021.html ; IHK Wiesbaden (Verbraucherrecht) https://www.ihk.de/wiesbaden/recht/rechtsberatung/internetrecht-und-werbung/neues-verbraucherrecht-1255576
- **Abnahme/Fälligkeit (Werkvertrag):** BGB § 640 https://www.gesetze-im-internet.de/bgb/__640.html · § 641 https://www.gesetze-im-internet.de/bgb/__641.html
- **Zufriedenheits-/Garantie:** EuGH, Urteil v. 28.09.2023, **C-133/22** – auch eine „Zufriedenheitsgarantie" ist eine gewerbliche Garantie (Art. 2 Nr. 14 RL 2011/83/EU; § 479 BGB) und löst Informationspflichten aus. Diese Pflichten sind **verbraucherbezogen**; im reinen B2B ist die direkte Bindung geringer, die Garantie-Formulierung bleibt aber ein Haftungs-/Erwartungsrisiko. Quelle u. a. IT-Recht-Kanzlei https://www.it-recht-kanzlei.de/eugh-zufriedenheitsgarantie-ist-gewerbliche-garantie.html · Wettbewerbszentrale. `[BELEGT: Rechtsprechung verifiziert 07/2026]`
- **15-Minuten-Aufrundung unwirksam** (auch B2B) – in den Projektunterlagen zitiert als BGH IX ZR 140/19; OLG Düsseldorf 24 U 65/22. `[BELEGT als Zitat in SartuProjektZusammenfassung; vor Verkauf anwaltlich verifizieren]`
- **BFSG** (Barrierefreiheitsstärkungsgesetz, in Kraft seit 28.06.2025): betrifft bestimmte B2C-Produkte/-Dienstleistungen (u. a. E-Commerce); Kleinstunternehmen im Dienstleistungsbereich (< 10 MA und ≤ 2 Mio. € Umsatz) sowie reine B2B-Angebote sind i. d. R. ausgenommen. `[BELEGT: mehrfach in Unterlagen; genauer Anwendungsbereich in 3.6 mit offizieller Quelle]`
- **Mollie** (Zahlung/Webhooks/Mandate/Idempotenz): https://docs.mollie.com/reference/create-payment · /webhooks · /mandates-api · /api-idempotency `[BELEGT: GESCHAEFTSMODELL §20]`
- **INWX** (Domain-Reseller/API): https://www.inwx.com/de/customer-solutions/reseller · https://www.inwx.com/en/offer/api `[BELEGT]`
- **Google SEO/AI** (GEO-Grundlage): https://developers.google.com/search/docs/fundamentals/ai-optimization-guide · .../appearance/ai-features · .../structured-data/local-business `[BELEGT: WEBSEITENKONZEPT §2.4]`

---

## 4. Logikfehler & Widersprüche (die härtesten zuerst)

### 4.1 DREI widersprüchliche Preisstände `[BELEGT]`
| Quelle | Pakete | Betrieb | Modell |
|---|---|---|---|
| `GESCHAEFTSMODELL.md` (kanonisch, Juli 2026) | Start 1.490 / Wachstum 3.900 / Platzhirsch 7.900 / ab 12.500 | Schutz 59/129/249, **keine** Minuten | keine Add-ons |
| `sartupaketepreise.md` (Juni 2026) | Basis 1.290 / Pro 2.990 / Platin 5.990 / Enterprise ab 9.990 | Wartung 69/149/299, **mit** Minuten | **öffentliche Add-on-Liste**, Garantie |
| `sartulastenheftwebsite.md` | Start/Wachstum/Platzhirsch **mit alten Preisen** 1.290/2.990/5.990 | Care 49/99/249, **mit** 30/90 Min. | Add-ons, SEO-Stufen 149/390/790, Geld-zurück |

Drei Namensstände (Basis/Pro/Platin vs. Start/Wachstum/Platzhirsch), drei Preisstufen, drei Betriebsmodelle. **`ENDKONTROLLE_WEBSEITEN.md` markiert die alten Reste selbst als „zu entfernen".** → **Kritisch:** ohne eine einzige Preis-/Scope-Quelle (`pricing.json` + Diff-Test) baut ein Umsetzer das Falsche.

### 4.2 DREI Tech-Stacks `[BELEGT]`
- **Gebaut (Juni):** statisch HTML/CSS/JS auf **Vercel + Supabase** (Auth/PostgreSQL/Storage, Frankfurt), RLS, live getestet. `[SartuProjektZusammenfassung]`
- **Kanonisch spezifiziert (Juli):** **Node + PostgreSQL + Redis + S3 (DE/EU)** Control-Plane; Kundenseiten **Astro** static via isolierter Agenten-Worker. `[DESIGNSYSTEM §2]` (Portal-Template-Doku: **Next.js + shadcn**.)
- **Generisches Website-Lastenheft:** **PHP + Flat-File-JSON + Matomo** auf All-Inkl-Shared-Hosting mit **WordPress-ähnlichem WYSIWYG-Admin**. `[lastenheft_webseite.md]` – widerspricht direkt „kein freier Editor / individuell programmiert".
→ Der PHP-Ansatz und die Supabase-Variante sind gegenüber der Node/Astro-Spezifikation **abgelöst**. Aber es existiert bereits ein **gebautes Supabase-Portal** vs. ein **spezifiziertes Node-Portal** – ein echter Fork mit Sunk-Cost. (Auflösung: `MASTERKONZEPT §12/§25`.)

### 4.3 Widerspruch „kein freier Editor" vs. Lastenheft-WYSIWYG `[BELEGT]`
`lastenheft_webseite.md` (Abschnitt 4) verlangt einen **WordPress-ähnlichen Inhalts-Editor** pro Seite. Das kanonische Modell verbietet genau das („kein freier Editor, individuell programmiert, menschlich freigegeben"). Beide Dateien liegen im selben Set. → Der generische Lastenheft ist ein Fremdkörper/Altstand.

### 4.4 Mehrere widersprüchliche Design-Paletten `[BELEGT]`
- ANTI_KI_CHECK + Logo-Boards: **Beige/Schwarz/Rostrot** (+ Teal).
- DESIGNSYSTEM: **Teal-forward** (Ink/Paper/Mist/Teal/Signal-Blue/Amber/Red), kein Rostrot.
- WEBSEITENKONZEPT_FINAL + ELEMENTPLAN: **Teal + Neon-Signal-Grün `#A8E000` + Oxide-Orange**, und **ELEMENTPLAN verwirft Beige explizit** als „zu ruhig".
- lastenheft: **Navy + Lime `#aef000`**.
→ **Keine** Umsetzungsdatei bildet die „kanonische" Beige/Rostrot-Palette sauber ab. Das Neon-Grün widerspricht zudem der Anti-KI-/Editorial-Haltung. (Auflösung: `MASTERKONZEPT §20` – eine Palette: Ink/Ivory/Teal + Oxide/Rostrot als Akzent, Neongrün gestrichen.)

### 4.5 Heller vs. dunkler Hero `[BELEGT]`
WEBSITE_KONZEPT/DESIGN: „helle Oberflächen"; ELEMENTPLAN Prüfrunde 2 + WEBSEITENKONZEPT §7.2: **dunkler** Deep-Ink-Hero mit Portal-UI. Interner Widerspruch. (Entscheidung getroffen: dunkler Produkt-Hero, s. Masterkonzept.)

### 4.6 „Lumi" ist zwei verschiedene Dinge `[BELEGT]`
- Alt (SartuProjektZusammenfassung): **Konfigurator** mit zwei Pfaden, Farbwähler, Stilwahl, Live-Preisleiste, Paketwahl.
- Neu (LUMI_PORTAL, WEBSEITENKONZEPT): **geführter 5-Themen-Bedarfsscheck ohne** Paket-/Farb-/SEO-Wahl.
→ Der neue Ansatz ist besser und ersetzt den alten. Der alte Farbwähler wurde bewusst verworfen (Baukasten-Gefühl). `[BELEGT: SartuProjektZusammenfassung §3.2]`

### 4.7 Template-Empfehlung driftet am selben Tag `[BELEGT]`
`SARTU_TEMPLATE_AUSWAHL_2026.md` empfiehlt **Folex Lite** (Website) + **Studio Admin** (Portal). `SARTU_KOSTENLOSE_TEMPLATE_RECHERCHE.md` (gleiches Datum) stuft Folex wegen **Lizenz** (Resale verboten) zurück → **ScrewFast (MIT)**/eigener Umbau, Portal → **shadcn/ui dashboard-01 (MIT)**. Nicht final aufgelöst. (Entscheidung: MIT-Templates, s. Masterkonzept.)

### 4.8 Solo vs. Team `[BELEGT]`
`SartuProjektZusammenfassung §1.2` ändert Website-Wording bewusst **„Solo → Team"**; `WEBSEITENKONZEPT §18.1` will ein **echtes Foto von Nils** (Einzelperson); `GESCHAEFTSMODELL §2` sagt „kleines Team". → Ehrlichkeits- **und** Kapazitätsfrage. „Team" behaupten, wo faktisch eine Person arbeitet, ist ein Vertrauens- und ggf. Wettbewerbsrechtsrisiko. Kapazität/Projekte-pro-Monat hängen direkt daran (s. Unit Economics).

### 4.9 Markenname-/Repo-Churn `[BELEGT]`
Altname **„Klarweb"** (sartupaketepreise.md: „Seite sagt aktuell Klarweb → auf Sartu ändern"), Vorschau `webagency-v3.vercel.app`, Repo `Webagency-V5`, geplante Domain `sartu.de`. Mehrere Rebuilds signalisieren Konzept-Churn – Energie floss in Umbauten statt Verkauf. `[EINSCHÄTZUNG]`

---

## 5. Risiken

1. **Über-Engineering = Launch-Blocker (höchstes Risiko).** „Komplettes Portal vor erstem Kunden" bindet Monate. `[BELEGT: GESCHAEFTSMODELL §14/§20, DESIGNSYSTEM §16]` → MVP-Stufung erzwingen.
2. **Vollautonome KI-Produktion ist fragil.** Ein Agent, der eine ganze Kundenseite baut und QA-Gates durchläuft, ist aufwändig und fehleranfällig. `[EINSCHÄTZUNG]` Die internen Std-Caps (16/32/50) sind nur mit reifem Designsystem haltbar; anfangs ist das Handarbeit.
3. **Bus-Faktor 1 / Kapazität.** Solo: bei 30–50 h pro Platzhirsch realistisch nur wenige Projekte/Monat – und das Portal baut sich nicht nebenbei. SLA-Reaktionszeiten und laufender Betrieb sind bei Krankheit ungedeckt. `[EINSCHÄTZUNG]`
4. **Kein Nachfrage-Motor.** Kein Lead-Gen-/Vertriebsplan in den Unterlagen. Ein perfektes Anfrage-Verarbeitungssystem ohne Anfragen ist wertlos. `[EINSCHÄTZUNG]`
5. **Kaltstart ohne Referenzen.** Regeln verbieten (zu Recht) Fake-Referenzen; echte gibt es am Start nicht → Vertrauenslücke genau dort, wo Festpreis 7.900 € Vertrauen braucht. `[BELEGT: WEBSEITENKONZEPT §2.3; EINSCHÄTZUNG zur Wirkung]`
6. **Rechtliche Baustellen** (vor Verkauf zwingend): Abnahmefiktion/Mitwirkung, **AVV mit KI-Subunternehmern** (OpenAI/Anthropic), Domain-Datenschutz, Garantie-Formulierung (EuGH C-133/22), RDG bei Rechtstexten, BFSG-Anwendbarkeit. `[BELEGT: mehrere Dateien]`
7. **Mollie-Abo/Mandat-Komplexität** (SEPA-Mandat, Idempotenz, Fehlschlag/Erstattung/Mahnung) muss E2E getestet sein, bevor wiederkehrend eingezogen wird. `[BELEGT: DESIGNSYSTEM §14]`
8. **Scope-Creep über „Selbstpflege".** Kunden erwarten evtl. mehr als typisierte Datensätze („nur den einen Satz ändern"). Ohne klare Erwartung droht Support-Last. `[EINSCHÄTZUNG]`
9. **Content-Last mit Qualitätsgate.** 7 Leistungsseiten + Hubs + Branchen + Ortsseiten (je ≥ 5 nicht-austauschbare Abschnitte) + 40–60 Lexikonbegriffe + Ratgeber = hoher, kaum automatisierbarer Redaktionsaufwand. `[BELEGT: WEBSITE_KONZEPT, WEBSEITENKONZEPT]`
10. **Export-Versprechen ungetestet.** „Problemloser Umzug" darf nicht beworben werden, bevor der baubare Export praktisch getestet ist. `[BELEGT: GESCHAEFTSMODELL §16]`

---

## 6. Fehlende Bestandteile

- **Vertriebs-/Lead-Gen-Plan** (wie entstehen Anfragen: Local SEO, Google-Unternehmensprofil, gezielte SEA, Netzwerk/Empfehlung, Kaltakquise?). Komplett fehlend. `[EINSCHÄTZUNG]`
- **Echte Referenzen/Case-Study-Strategie** für den Kaltstart. `[EINSCHÄTZUNG]`
- **Ausfall-/Vertretungskonzept** (Krankheit, Urlaub) für Betrieb und SLA. `[EINSCHÄTZUNG]`
- **Finale Schriftwahl** – in keiner Datei festgelegt. `[BELEGT: fehlt]`
- **Eine** verbindliche Design-Palette + Ansprache („Sie") + Template-Basis – nicht final. `[BELEGT]`
- **Finale Rechtstexte** (Impressum/Datenschutz/AGB/AVV) + echte NAP-Daten. `[BELEGT: mehrere „offen"-Vermerke]`
- **Buchhaltungs-Entscheidung** (lexoffice vs. sevDesk). `[BELEGT: SartuProjektZusammenfassung §5.4]`
- **Preis-Roadmap** (wann/wie Preise nach Nachfrage erhöhen – im Modell nur angedeutet). `[EINSCHÄTZUNG]`

---

## 7. Preisbewertung

- **Start 1.490 €:** `[EINSCHÄTZUNG]` gegen KI-Builder (≈0 €) und Baukasten schwer zu verteidigen, wenn nur „One-Pager". Rechtfertigung muss über *Ergebnis + Betrieb + kein Selbstbau* laufen. Risiko: Preisanker wirkt für „nur eine Seite" hoch. **Empfehlung:** Start bewusst als „professioneller Einstieg mit Betrieb" positionieren, nicht als „billige Website"; ggf. als Ausnahmefall behandeln und Wachstum als eigentlichen Einstieg framen.
- **Wachstum 3.900 €:** marktgerecht für individuell programmierte Mehrseiter mit Texten + SEO-Basis + Betrieb. `[EINSCHÄTZUNG]` Solide Mitte.
- **Platzhirsch 7.900 €:** am oberen KMU-Rand, aber durch Umfang (bis 16 Seiten, Conversion-Modul, Recruiting) + Betrieb vertretbar. `[EINSCHÄTZUNG]` Hier liegt die Marge – zu Recht die Empfehlung.
- **Sonderprojekt ab 12.500 €:** sinnvolle Schwelle, hält Komplexität aus dem Standard. `[EINSCHÄTZUNG]`
- **Schutz 59/129/249 €:** marktkonform bis leicht premium; ohne Änderungsminuten margenstark. `[EINSCHÄTZUNG]` **Achtung:** Der SartuProjektZusammenfassung-Hinweis, SEO-Betreuung sei mit 490/990 € „unter Markt (800–3.000 €)", stammt aus dem alten Modell – im neuen Modell gibt es keine SEO-Stufen mehr; späterer Ausbau ist ein Einzel-Festpreisangebot. Das ist konsistenter, verschenkt aber ggf. planbaren SEO-Retainer-Umsatz. `[EINSCHÄTZUNG]`
- **Domain bis 30 €/Jahr im Schutz:** großzügig, aber für `.de` (Großhandel wenige €) unkritisch. `[EINSCHÄTZUNG]`

**Gesamt:** Preisarchitektur ist stimmig und eher zu günstig als zu teuer – **außer** Start, das gegen Gratis-Wettbewerb anargumentieren muss. Der geplante Preistest nach ≥ 10 Platzhirsch-Angeboten (≥ 30 % Annahme, ≤ 35 h Median → Preis anheben) ist methodisch gut. `[BELEGT: GESCHAEFTSMODELL §17]`

---

## 8. Angebotsbewertung

- **„Wenig Auswahl, eine Empfehlung":** stark und differenzierend. Die Deterministik (Rot/Orange/Gelb/Standard) mit Testfällen ist überdurchschnittlich sauber. `[BELEGT: LUMI_PORTAL §8/§9]`
- **Platzhirsch als sichtbare Empfehlung:** logisch, solange die Ampel bei kleinem Bedarf ehrlich kleiner empfiehlt (ist geregelt). `[BELEGT]` Risiko nur, wenn es in der Praxis doch zur Upsell-Maschine wird → Kennzahl „manuelle Paketkorrektur" überwachen.
- **Add-ons weggelassen:** richtig (kein Baukasten-Gefühl, kein Verhandlungsbasar). Die alte öffentliche Add-on-Liste war ein Fehler; ihre Streichung ist eine echte Verbesserung. `[EINSCHÄTZUNG]`
- **Änderungsminuten ersetzt durch Selbstpflege:** richtig – aber Erwartungsmanagement nötig (s. Risiken, Punkt 8).
- **Privatkunden ausschließen:** ja, zwingend (Netto-Kommunikation, Widerrufsrecht, andere Rechtslage). `[BELEGT: GESCHAEFTSMODELL §3]`
- **„Platzhirsch" als Name:** `[EINSCHÄTZUNG]` einprägsam und positiv besetzt (regionaler Marktführer). Logisch als Top-Paket. Kleine Gefahr: klingt leicht „großspurig" für sehr kleine Betriebe – aber die Ampel empfiehlt denen ja Start/Wachstum. Vertretbar.

---

## 9. Portalbewertung

- **Vision:** exzellent durchdacht (Datenmodell, Rollen, Audit, Job-Statuszustände, Selbstpflege-Typen, SEO-Flotte). `[BELEGT: DESIGNSYSTEM]`
- **Problem:** Der geforderte **Vollausbau vor Marktstart** ist unrealistisch für ein Kleinstteam und der Hauptgrund, warum SARTU vielleicht nie live geht. `[EINSCHÄTZUNG]`
- **Baukasten-Gefahr:** gut vermieden – typisierte Datensätze statt freiem Editor, Vorschau/Version/Freigabe. `[BELEGT]`
- **Überforderungs-Gefahr Kunde:** gering, weil „ein nächster Schritt" statt Aufgabenwand. Gut. `[BELEGT: DESIGNSYSTEM §8]`
- **Empfehlung:** Portal in **Stufen** (s. Masterkonzept §23). Minimal-Portal (Anfrage-Inbox, Angebot, Mollie-Link, Upload, Vorschau) reicht, um die ersten Kunden zu bedienen. Alles andere folgt datengetrieben.
- **Sunk-Cost Supabase:** Der gebaute Supabase-Stand (PostgreSQL + Auth + Storage in Frankfurt, RLS) erfüllt die Kernanforderung bereits. Ihn für Stufe 0/1 **behalten** statt sofort auf Node neu zu bauen. `[EINSCHÄTZUNG]`

---

## 10. SEO/GEO-Bewertung

- **Grundhaltung:** vorbildlich – „GEO = gute SEO", keine Garantien, `llms.txt` ehrlich als Nicht-Rankingfaktor, korrekte Google-Quellen. `[BELEGT: WEBSEITENKONZEPT §2]`
- **SEO ab Launch enthalten:** richtig und verkaufsstark; späterer Ausbau datenbasiert statt pauschalem Neuschreiben. `[BELEGT]`
- **Ortsseiten-Gate:** sehr gut (verhindert Doorway-Abstrafung). `[BELEGT: WEBSITE_KONZEPT]`
- **Lexikon/Ratgeber:** sinnvoll für Entitäten/GEO, aber **Aufwands- und Qualitätsrisiko** (Gefahr „Textfriedhof"). Der Start mit 40–60 kuratierten Begriffen statt 300 ist die richtige Bremse. `[BELEGT; EINSCHÄTZUNG zum Aufwand]`
- **Admin-SEO-Flotte:** technisch stark; die Grenze „deterministische Technik automatisch, Inhalt nur als Entwurf" ist genau richtig. `[BELEGT: DESIGNSYSTEM §12]`
- **Kritik:** Der laufende Content-Aufwand (Ratgeber, Lexikon, Ortsseiten mit echtem Inhalt) ist der versteckte Dauer-Zeitfresser und konkurriert mit der Produktion. `[EINSCHÄTZUNG]`

---

## 11. Unit Economics

**Interne Vorgaben `[BELEGT: GESCHAEFTSMODELL §17]`:** direkte Produktionskosten ≤ 35–40 % des Einmalpreises; Schutz ≥ 60 % Deckungsbeitrag; Std-Caps Start 16 h / Wachstum 32 h / Platzhirsch 50 h (Ziele 8–12 / 18–26 / 30–42 h).

**Grobe Rechnung `[EINSCHÄTZUNG]`** (kalkulatorischer Std-Wert ~90 €):

| Paket | Preis | Ziel-h | Ziel-Kosten | Roh-DB | Bewertung |
|---|---:|---:|---:|---:|---|
| Start | 1.490 € | 8–12 | ~720–1.080 € | ~28–52 % | eng; bei 16 h (~1.440 €) praktisch null/defizitär |
| Wachstum | 3.900 € | 18–26 | ~1.620–2.340 € | ~40–58 % | ok bei diszipliniertem Scope |
| Platzhirsch | 7.900 € | 30–42 | ~2.700–3.780 € | ~52–66 % | am gesündesten |

- Die **≤ 40 %-Kostenmarke** ist nur bei **niedrigem Stundenbedarf** haltbar – also nur mit **starkem, wiederverwendbarem Designsystem** und ehrlich zeitsparender KI. Ohne das kippt v. a. Start ins Minus. `[EINSCHÄTZUNG]`
- **Wiederkehrender Umsatz ist der eigentliche Wert:** z. B. 20 aktive Kunden × Ø 140 € ≈ **2.800 €/Mon.** nahezu Marge (Hosting statisch ~5–15 €/Kunde, Backups/Monitoring gering). `[EINSCHÄTZUNG]`
- **Break-even hängt am Portal-Bauaufwand**, nicht an den Websitepreisen. Der Portal-Eigenbau (hunderte Stunden) ist die eigentliche Investition. → Genau deshalb Stufung. `[EINSCHÄTZUNG]`
- **Realistische Kapazität (Solo):** grob 3–6 Projekte/Monat gemischt, **wenn** nicht gleichzeitig am Portal gebaut wird. Bei parallelem Portalbau real 0–2. `[EINSCHÄTZUNG]`
- **Größte Zeitfresser:** (1) Portal-Eigenbau, (2) Content/Redaktion (Leistungsseiten, Lexikon, Ortsseiten), (3) Onboarding-/Faktenklärung mit Kunden, (4) QA/menschliche Freigabe jeder KI-Ausgabe, (5) Support/Selbstpflege-Erwartungen. `[EINSCHÄTZUNG]`
- **Was KI gut kann:** Rohtext-Entwürfe aus Fakten, Struktur-/Sitemap-Vorschläge, Code-Grundgerüst aus Designsystem, Meta-/Schema-Entwürfe, Extraktion aus Altwebsite/PDFs, technische SEO-Patch-Entwürfe. **Was menschliche Kontrolle braucht:** alle veröffentlichten Fakten/Belege, Rechts-/Fach-/Gesundheitsaussagen, finale Freigabe, Zahlungen, Domain/DNS, Produktion. `[BELEGT: LUMI_PORTAL §19, DESIGNSYSTEM §4/§5]`

---

## 12. Konkrete Optimierungen

1. **Reihenfolge umdrehen: verkaufen vor Vollausbau.** 2–3 Referenzkunden manuell (KI-assistiert) liefern; Minimal-Portal genügt. `[→ Masterkonzept §23]`
2. **Eine Wahrheitsquelle erzwingen.** `pricing.json`/`prices.js` + Diff-Test; `sartupaketepreise.md` und `sartulastenheftwebsite.md` als **veraltet** nach `konzepte/_archiv/`.
3. **Stack festnageln:** Website Astro, Portal Next.js/shadcn, Backend PostgreSQL; Supabase-Stand für Stufe 0/1 behalten, Node-Control-Plane erst ab Stufe 2.
4. **Design vereinheitlichen:** eine Palette (Ink/Ivory/Teal + Oxide/Rostrot-Akzent), **Neon-Grün streichen**, „Sie" durchziehen, Schrift + Logo-Favorit fixieren.
5. **USP umformulieren:** „kein WordPress" → „keine Update-/Plugin-/Sicherheitssorgen; schnell, wartungsarm; Betrieb inklusive". Portal + Festpreis als Haupt-USP.
6. **Start-Paket schärfen** gegen Gratis-Wettbewerb (Ergebnis + Betrieb betonen) oder Wachstum als eigentlichen Einstieg framen.
7. **Lead-Gen-Plan ergänzen** (Local SEO, Google-Unternehmensprofil, gezielte SEA „Website erstellen lassen {Ort}", Empfehlungen). Ohne Nachfrage kein Geschäft.
8. **KI realistisch einordnen:** Stufe 0/1 assistierend, nicht orchestriert; Std-Caps erst mit reifem Designsystem als bindend behandeln.
9. **Solo/Team ehrlich** darstellen; Ausfall-/Vertretungskonzept definieren; SLA erst versprechen, wenn haltbar.
10. **Content gestaffelt:** Launch mit Kernseiten + 7 Leistungsseiten + 3–6 Ratgeber + 20–40 Lexikonbegriffe; Ortsseiten/Branchen erst nach Search-Console-Daten.
11. **Recht vor Verkauf** bündeln (AGB/AVV inkl. KI-Subunternehmer/Abnahme/BFSG-Prüfung/Garantie-Wording) – ein Kanzlei-Paket, nicht stückweise.
12. **Export + Mollie-Abo + INWX-Lifecycle praktisch testen**, bevor damit geworben/eingezogen wird.

---

## 13. Klare Empfehlung: so starten / nicht so starten

**NICHT so starten:**
- Nicht das komplette Control-Plane-Portal (KI-Orchestrierung, Domainlebenszyklus, SEO-Flotte, Rollback) vor dem ersten verkauften Projekt bauen.
- Nicht aus den veralteten Dateien (`sartupaketepreise.md`, `sartulastenheftwebsite.md`, generisches Lastenheft) bauen.
- Nicht „Team"/Referenzen/Garantien behaupten, die es (noch) nicht gibt.
- Nicht mit Neon-Grün/generischer KI-Optik launchen (zerstört die Differenzierung).

**SO starten (empfohlen):**
1. **Fundament (1 Woche):** eine Preis-/Scope-Quelle, ein Stack, eine Palette, „Sie", Logo/Schrift – fixieren; Altdateien archivieren.
2. **Website (2–4 Wochen):** Astro-Kernwebsite (Kernseiten + 7 Leistungsseiten + Ratgeber-/Lexikon-Start), ENDKONTROLLE-Profil SARTU-PUBLIC grün, echte Rechtstexte/NAP, Portal-Screens als „Musteransicht".
3. **Minimal-Portal + Verkauf (parallel):** Anfrage → Angebot → Mollie-Zahlungslink → Upload/Vorschau; **Lead-Gen aktiv**; 2–3 Referenzkunden manuell liefern.
4. **Härten & automatisieren (danach, datengetrieben):** Mollie-Abo, adaptives Onboarding, strukturierte Selbstpflege; dann teilautomatisierte Produktion + SEO-Flotte; zuletzt volle Orchestrierung + programmatische Ortsseiten.

**Gesamturteil:** Das Konzept ist **gut genug, um damit ein echtes Geschäft zu starten** – aber nur mit umgekehrter Reihenfolge (Kunden vor Perfektion), einer erzwungenen Wahrheitsquelle und einem verschlankten MVP. Die Positionierung ist ein Asset; der geplante Erstumfang ist das Risiko. Wird der Umfang gestaffelt und die Nachfrage aktiv erzeugt, ist SARTU marktfähig und margenstark. Wird weiter „erst das perfekte System" gebaut, ist das wahrscheinlichste Ergebnis: viel Technik, wenige Kunden.

---

*Die konkrete, widerspruchsbereinigte Bauvorlage steht in `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`.*
