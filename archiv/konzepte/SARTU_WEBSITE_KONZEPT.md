# Sartu Website-Konzept

Stand: 18. Juli 2026. Dieses Dokument ist die Soll-Konzeption fuer die oeffentliche Sartu-Website. Ziel: maximal starke SEO-/GEO-Architektur, klare Conversion, wenige Kundenentscheidungen und kein Risiko durch duenne oder manipulative Ortsseiten.

---

## 1. Grundsatzentscheidung

Sartu braucht keine klassische Agenturwebsite, sondern eine suchfaehige, maschinenlesbare, schnelle Verkaufsarchitektur.

Die Website muss drei Dinge gleichzeitig leisten:

1. **Kunden beruhigen:** Der Kunde muss keine Pakete, Extras, Seitenzahlen, SEO-Stufen oder Technik verstehen.
2. **Suchsysteme fuettern:** Google, Bing und KI-Sucherlebnisse brauchen klare, indexierbare, konsistente Informationen zu Angebot, Preisen, Ablauf, Orten, Zielgruppen und Grenzen.
3. **Sartu wirtschaftlich schuetzen:** Keine falschen Versprechen, keine unendliche Beratung, keine duennen Massenseiten und keine Auswahl, die Support oder Scope Creep erzeugt.

**Kernpositionierung:**

> Sartu plant, textet, programmiert und betreibt Firmenwebsites zum Festpreis. Sie liefern die Geschaeftsfakten; Sartu entscheidet Struktur, Design, Technik und SEO-/GEO-Basis.

**Wichtigste CTA:** `Bedarf pruefen lassen`

**Zweite CTA:** `Preise ansehen`

**Nicht verwenden:**

- `Paket selbst waehlen`
- `Website konfigurieren`
- `SEO buchen`
- `Extras hinzufuegen`
- `Care-Minuten`
- `Jetzt kaufen`
- `Garantiert Platz 1`
- `Garantierte KI-Nennung`

---

## 2. Statusurteil

### Website

Die Website ist inhaltlich bereits auf das aktuelle Modell gedreht, aber noch nicht final launchfaehig. Vor Go-live fehlen:

- finale Rechtstexte und AGB-Pruefung.
- echte Domain, Canonicals, Open Graph und `llms.txt`.
- echte NAP-Daten.
- finale Bild- und Referenzwahrheit.
- Indexierungsfreigabe.
- technische SEO-/Performance-QA.
- redaktionelle QA gegen alte Preis-, Minuten- und SEO-Stufen-Reste.

### Portal

Die Control-Plane ist ein lokaler Referenz-Prototyp. Sie beweist den Prozess, ist aber nicht sofort fuer echte Kunden einsetzbar.

Vor echten Kunden fehlen:

- echte Authentifizierung, Admin-2FA, Rollen und Audit-Log.
- PostgreSQL/Migrationen statt lokaler Demo-Speicherung.
- Backups, Monitoring, Object Storage und Mailversand.
- Ende-zu-Ende-Tests fuer Mollie, Mandate, Webhooks, Fehler und Erstattungen.
- Ende-zu-Ende-Tests fuer INWX, Registrant-Kontakte, Transfers, DNS-Snapshots und Domainuebergabe.
- isolierter Codex-/Claude-Worker mit Produktionsgrenzen.
- produktionsnahe Deployments, Rollback, Export und Wiederherstellung.
- rechtliche Pruefung von Angebot, AGB, Datenschutz, AVV, Domainverwaltung und KI-Verarbeitung.

**Oeffentliche Portalformulierung:**

> Nach Auftrag laeuft Ihr Projekt in einem gefuehrten Portal: Angebot, Zahlungen, Briefing, Freigaben und spaetere kleine Pflege bleiben nachvollziehbar gebuendelt.

Nicht sagen:

> Alles laeuft vollautomatisch im Portal.

---

## 3. USP: Festpreis plus Portal plus gefuehrter Ablauf

Der eigentliche USP von Sartu ist nicht nur `Website zum Festpreis`. Ein Festpreis allein ist kopierbar. Stark wird das Angebot durch die Kombination:

1. **Festpreis:** Der Kunde sieht vor Auftrag, was das Website-Ergebnis kostet.
2. **Gefuehrtes Portal:** Angebot, Zahlungen, Briefing, Dateien, Domain, Vorschau, Feedback, Freigabe und spaetere kleine Pflege laufen an einem Ort.
3. **Wenige Entscheidungen:** Der Kunde beantwortet Geschaeftsfragen; Sartu entscheidet Struktur, Design, Technik, SEO-/GEO-Basis und Anbieter.
4. **KI-gestuetzte Produktion mit Verantwortung:** Codex/Claude beschleunigen Code, Struktur und Texte, aber Sartu prueft, versioniert und gibt frei.
5. **Schutzbetrieb:** Nach dem Launch bleibt die Website technisch betreut, ohne dass der Kunde WordPress, Plugins oder Hosting verwalten muss.

**Oeffentlicher USP-Satz:**

> Sartu verbindet Festpreis-Website, gefuehrtes Projektportal und professionellen Betrieb: Sie liefern Fakten und Freigaben, Sartu plant, textet, programmiert, startet und betreut die Website.

**Noch kuerzer fuer Hero oder Trust-Band:**

> Festpreis. Portal. Kein WordPress. Sartu entscheidet die Technik.

### Warum das Portal wichtig ist

Das Portal ist kein nettes Zusatzfeature. Es loest die typischen Agenturprobleme:

- Angebote, Rechnungen und Zahlungen verschwinden nicht in E-Mail-Ketten.
- Briefingfragen sind kurz, paketbezogen und vorbefuellt.
- Dateien, Bildrechte, Domainfragen und Freigaben werden strukturiert gesammelt.
- Feedback kommt gebuendelt statt als 23 einzelne Nachrichten.
- Der Kunde sieht, was als Naechstes passiert.
- Kleine Pflege nach Launch ist gefuehrt, nicht frei-chaotisch.
- Sartu kann intern Angebot, Projekt, Domain, Zahlung, SEO/GEO und KI-Produktion sauber verbinden.

### Was die Website darueber zeigen muss

Die oeffentliche Website braucht frueh eine eigene Portal-/Ablauf-Erklaerung:

- auf der Startseite direkt nach Preis-/Leistungsversprechen.
- auf `/ablauf` als Hauptbeweis fuer den besonderen Prozess.
- auf `/leistung-portal` als eigene SEO-/Trust-Seite.
- auf `/preise` als Begruendung, warum Schutzbetrieb und Portal zum Ergebnis gehoeren.
- in Lumi als Beruhigung: `Sie bekommen kein loses Formular, sondern ein geprueftes Angebot im Portal.`

### Portal-Story in drei Schritten

1. **Vor Auftrag:** Lumi fragt kurz, Sartu prueft, Angebot kommt ins Portal.
2. **Im Projekt:** Zahlung, Domain, Briefing, Uploads, Vorschau, Feedback und Abnahme laufen gefuehrt.
3. **Nach Launch:** Oeffnungszeiten, Kontaktdaten, Seitenstatus, Anfragen, Rechnungen und Support bleiben uebersichtlich.

### Reihenfolge: Erst Portal fertig oder erst Website?

Nicht das komplette Produktportal muss fertig sein, bevor die Website gebaut wird. Aber vor der finalen Website-Verkaufsseite sollte es ein glaubwuerdiges Portal-Belegpaket geben.

**Empfehlung: Website jetzt bauen, Portal-Screenshot-Kit parallel vorbereiten.**

Warum:

- Die Website braucht schon jetzt SEO-Zeit, Indexierung, Content und Nachfrageaufbau.
- Das Portal darf den Go-live der Marketingseite nicht monatelang blockieren.
- Fake-Screenshots oder zu grosse Versprechen waeren aber schaedlich.
- Deshalb: erst echte oder klar markierte Muster-Screenshots aus dem aktuellen Control-Plane-Flow, spaeter durch produktionsreife Screens ersetzen.

### Screenshot-Strategie

Erlaubt:

- echte Screens aus dem lokalen Prototyp, wenn keine echten Kundendaten sichtbar sind.
- Musterprojekt mit klarer Kennzeichnung `Beispielansicht`.
- Screens von Angebot, Projektstatus, Briefing, Vorschaufeedback, Zahlung, Domainstatus und kleiner Pflege.
- Ausschnitte statt komplette Adminoberflaeche, wenn Details noch nicht final sind.
- neutrale Mockups, wenn sie visuell als Muster erkennbar sind.

Nicht erlaubt:

- Screenshots, die produktionsreife Funktionen vortaeuschen.
- echte Kundendaten.
- Admin-Interna, Agentenlogs, API-Keys, Preise im Draftzustand oder technische Fehlermeldungen.
- Portal als vollautomatisches SaaS darstellen.
- `Alles passiert automatisch` behaupten.

### Mindest-Screenshot-Kit fuer die Website

Vor finalem Website-Go-live reichen 5 bis 7 stabile Beispielansichten:

1. **Portal-Cockpit:** naechster Schritt, Paket, Projektstatus.
2. **Angebot:** Empfehlung, Preis, Zahlungsplan, Annahme.
3. **Briefing:** wenige offene Aufgaben statt 100-Fragen-Formular.
4. **Domain:** bestehende Domain oder maximal drei Vorschlaege.
5. **Vorschau/Feedback:** gebuendeltes Feedback und Freigabe.
6. **Zahlung:** Rechnung und Mollie-Status ohne sensible Details.
7. **Nach Launch:** Oeffnungszeiten/Kontakt/Seitenstatus als gefuehrte Pflege.

Diese Screens duerfen zuerst als `Musterprojekt` eingebaut werden. Sobald das Portal produktionsreif ist, werden sie durch echte Produkt-Screens ersetzt.

### Textbaustein fuer die Website

> Ihr Projekt laeuft nicht ueber endlose E-Mail-Ketten. Im Sartu-Portal sehen Sie Angebot, Zahlungen, offene Aufgaben, Domainstatus, Vorschau, Feedback und spaetere kleine Pflege. Sie bearbeiten keine Website wie in WordPress; Sie bestaetigen Fakten, geben Feedback und behalten den naechsten Schritt im Blick.

### Zeitvorteil als Werbeaussage

Sartu sollte den Zeitvorteil bewerben, aber vorsichtig und serioes.

Nicht als harte Garantie:

> Spart Ihnen 10 Stunden Meetings.

Besser:

> Weniger Pflichttermine statt klassischer Agenturschleifen: Sartu buendelt Briefing, Angebot, Zahlungen, Dateien, Feedback und Freigaben im Portal. Gespraeche bleiben moeglich, aber Standardprojekte muessen nicht von Meeting zu Meeting geschoben werden.

Warum diese Formulierung belastbar ist:

- Es gibt gute allgemeine Quellen, dass Projektarbeit stark von Kommunikation gepraegt ist.
- PMI beschreibt Kommunikation als zentrale Projektmanagementarbeit und verweist auf die bekannte Groessenordnung, dass Projektmanager sehr viel Zeit mit Kommunikation verbringen.
- PMI zeigt ausserdem, dass ineffektive Kommunikation ein wesentlicher Risikofaktor fuer Projektziele, Termine und Budgets ist.
- Microsoft zeigt in Arbeitsdaten, dass Wissensarbeiter im Schnitt mehr Zeit mit Kommunikation als mit Erstellung verbringen.
- HBR und PMI zeigen, dass Statusmeetings schnell erhebliche Teamzeit binden koennen.

Was daraus folgt:

- Der belegbare Vorteil ist nicht `keine Kommunikation`.
- Der belegbare Vorteil ist `strukturierte, asynchrone und gebuendelte Kommunikation`.
- Sartu sollte intern eigene Daten sammeln: Anzahl Kundentermine, Kundeneingaben, Tage bis Angebot, Feedbackschleifen, Projektpause durch fehlende Informationen. Erst danach duerfen harte Zahlen wie `durchschnittlich X Termine weniger` oder `Y Stunden weniger Abstimmung` beworben werden.

### Normale Agentur vs. Sartu-Kommunikation

| Klassische Agentur | Sartu |
|---|---|
| Erstgespraech, Kickoff, Briefing, Sitemap, Designreview, Korrekturrunden, Launch-/Uebergabetermin | kurzer Bedarfsscheck, geprueftes Portalangebot, adaptives Onboarding, gebuendeltes Feedback, digitale Abnahme |
| viele Informationen in E-Mail, Telefon, PDFs und Meetingnotizen | ein Portalstatus mit naechstem Schritt |
| Kunde soll haeufig Seiten, Inhalte, Struktur oder Stil mitentscheiden | Kunde bestaetigt Fakten; Sartu entscheidet Struktur, Design und Technik |
| Rueckfragen entstehen oft spaet, weil Informationen verstreut sind | fehlende Fakten werden als Aufgaben und Datensaetze sichtbar |
| Meetingzeit ist Teil des Projektaufwands | Pflichttermine werden reduziert; Gespraeche bleiben bei Bedarf moeglich |

**Claim fuer Startseite oder Ablaufseite:**

> Standardprojekte laufen bei Sartu ohne Pflichttermin-Marathon. Sie starten mit wenigen Angaben, erhalten ein geprueftes Festpreisangebot im Portal und geben Feedback gebuendelt zur Vorschau.

---

## 4. SEO-/GEO-Leitbild

SEO und GEO werden nicht als Extra verkauft. Sie sind Architekturprinzipien.

Google beschreibt Optimierung fuer generative AI-Suche im Kern als Fortsetzung guter SEO-Grundlagen: hilfreiche, klare, eindeutige, crawlbare und indexierbare Inhalte. GEO ist deshalb kein magischer Trick, sondern konsequente Entitaets-, Antwort- und Faktenarchitektur.

**Sartu-Satz fuer die Website:**

> Ihre Website wird so aufgebaut, dass Menschen, Google und KI-Sucherlebnisse schnell verstehen, wer Sie sind, was Sie anbieten, fuer wen es passt, wo Sie arbeiten und wie der naechste Schritt aussieht.

### Was GEO praktisch bedeutet

- klare Entitaeten: Sartu, Angebot, Pakete, Orte, Zielgruppen, Leistungen.
- stabile Fakten: Preise, Zahlungslogik, Domainregel, Schutzbetrieb.
- beantwortbare Abschnitte: kurze Antworten, Tabellen, FAQs, Entscheidungslogik.
- maschinenlesbare Struktur: Schema.org, Breadcrumbs, eindeutige URLs, `llms.txt`.
- keine widerspruechlichen Aussagen zwischen Website, Preisen, Portal und Konzept.
- keine erfundenen Referenzen, Bewertungen, Orte oder Niederlassungen.

### Was GEO nicht ist

- keine Garantie auf Nennung in ChatGPT, Gemini, Google AI oder Bing Copilot.
- kein Keyword-Spinning.
- kein automatisches Publizieren von Seiten ohne echten Nutzen.
- kein Ersatz fuer klassische SEO-Grundlagen.

---

## 5. Antwort auf die Ortsseiten-Idee

Die Idee, fuer jeden Ort ueber 5.000 Einwohner eine Landingpage zu bauen und diese nur leicht abzuaendern, damit Google es nicht merkt, ist in dieser Form falsch und riskant.

Das Problem ist nicht die Anzahl der Ortsseiten. Das Problem ist die Absicht und die Nutzlosigkeit. Google kann Seiten abstufen, wenn sie nur fuer Suchmaschinen gebaut sind, viele aehnliche Suchanfragen abfangen und Besucher letztlich zum gleichen Ziel fuehren. Solche Seiten fallen in die Naehe von Doorway Pages und skalierter, wenig hilfreicher Inhaltserzeugung.

**Klare Entscheidung:**

Sartu darf Local-SEO-Seiten bauen, aber nicht als duenne Ortstext-Massenproduktion. Ortsseiten werden nur veroeffentlicht, wenn sie echte lokale Suchintention bedienen und einen eigenen Nutzen haben.

### Regel fuer Sartu

`Ort > 5.000 Einwohner` ist nur ein Datenfilter fuer die Recherche. Es ist kein Publikationskriterium.

Eine Ortsseite darf erst live gehen, wenn mindestens diese Bedingungen erfuellt sind:

- Sartu bietet die Leistung dort realistisch an.
- Die Suchintention ist klar: zum Beispiel `Webdesign Dresden`, `Website erstellen lassen Leipzig`, `Webdesign fuer Handwerker Chemnitz`.
- Der Ort hat kommerzielle Relevanz oder passt zu einer echten Vertriebsregion.
- Die Seite enthaelt lokale Eigeninformationen, nicht nur ausgetauschte Ortsnamen.
- Es gibt keinen Fake-Standort, keine erfundene lokale Telefonnummer, keine erfundene Bewertung und keine falsche Naehe.
- Die Seite hat eine klare Rolle in der internen Verlinkung.
- Die Seite wurde vor Indexierung redaktionell freigegeben.

### Bessere Taktik

Nicht: 1.000 Stadtseiten auf einmal.

Sondern:

1. 5 bis 10 starke Kernorte manuell aufbauen.
2. Daten aus Search Console, Bing, Ads und Lumi auswerten.
3. Nur Orte erweitern, die echte Nachfrage oder strategischen Wert zeigen.
4. Regionale Hubs bauen, bevor Kleinstadtseiten massenhaft entstehen.
5. Duennen Entwuerfen `noindex` geben, bis sie echten Inhalt haben.

---

## 6. Rollen der Startseite und Landingpages

Die Startseite bleibt wichtig. Sie ist aber nicht die einzige SEO-Landingpage.

### Startseite

Aufgabe:

- Marke und Entitaet etablieren.
- breites Angebot erklaeren.
- Vertrauen aufbauen.
- in Preise, Leistungen, Ratgeber und Lumi fuehren.
- fuer Brand-, Navigations- und breite Webdesign-Agentur-Suchen wirken.

Die Startseite rankt nicht ideal fuer jede Orts-/Branchen-/Leistungs-Kombination. Das ist normal.

### Leistungsseiten

Aufgabe:

- kommerzielle Suchintention abholen.
- `Webdesign`, `Website-Texte`, `SEO/GEO-Basis`, `Rundum-Schutz`, `Domain/Launch`, `Portal` erklaeren.
- interne Links zu Preisen, Ablauf, Ratgeber und Lumi geben.

### Ortsseiten

Aufgabe:

- lokale Suchintention abholen.
- Besuchern aus einem konkreten Ort schnell zeigen, ob Sartu fuer sie passt.
- lokale Branchenbeispiele, Servicegebiet und Entfernung/Arbeitsweise ehrlich erklaeren.
- in Lumi fuehren.

### Ratgeber

Aufgabe:

- Informationssuchen abholen.
- Vertrauen vor Kaufentscheidung aufbauen.
- in Bedarfseinschaetzung oder passende Leistungsseite fuehren.

---

## 7. Ziel-Sitemap

### Kernseiten

| URL | Aufgabe | Primaere Intention |
|---|---|---|
| `/` | Marke, Angebot, Einstieg | Webdesign-Agentur allgemein |
| `/leistungen` | Leistungsuebersicht ohne Add-on-Gefuehl | Was ist enthalten? |
| `/preise` | Pakete, Schutz, Domain, Zahlung | Kosten und Entscheidung |
| `/briefing` | Lumi-Bedarfsscheck | Anfrage |
| `/ablauf` | Projektweg | Vertrauen und Prozess |
| `/kontakt` | Rueckfrage | Kontakt |
| `/ueber-uns` | Haltung und Arbeitsweise | Vertrauen |
| `/ratgeber` | Wissens-Hub | Informationssuche |
| `/lexikon` | Begriffs-Hub fuer Website, SEO/GEO, Portal und Betrieb | GEO-/Entity-Aufbau |
| `/impressum`, `/datenschutz`, `/agb` | Pflichtseiten | Recht und Vertrauen |

### Leistungsseiten

| URL | Zweck | SEO/GEO-Rolle |
|---|---|---|
| `/leistung-webdesign` | Hauptleistung | Webdesign, Website erstellen lassen |
| `/leistung-texte` | Texterstellung | Website-Texte, SEO-Texte |
| `/leistung-seo` | SEO-/GEO-Basis | SEO fuer Firmenwebsite, KI-Suche |
| `/leistung-lokales-seo` | lokale Sichtbarkeit | lokales SEO, Google-Profil, Region |
| `/leistung-wartung` | Rundum-Schutz | Website Wartung, Hosting, Backups |
| `/leistung-domain-launch` | neu empfohlen | Domain, DNS, E-Mail, Launch |
| `/leistung-portal` | neu empfohlen | Projektportal, Selbstpflege, kein WordPress |

### Neue kommerzielle Hubs

Diese Seiten sollten mittelfristig ergaenzt werden, weil sie starke Suchintentionen abdecken:

| URL | Zweck |
|---|---|
| `/website-erstellen-lassen` | breiter kommerzieller Hub |
| `/webdesign-agentur` | Agentur-/Dienstleistervergleich |
| `/firmenwebsite-erstellen-lassen` | B2B-Fokus |
| `/website-relaunch` | Relaunch mit SEO-Schutz |
| `/webdesign-ohne-wordpress` | Abgrenzung Baukasten/WordPress |

### Branchen-Hubs

Nur wenn echte Inhalte geschrieben werden:

| URL | Zweck |
|---|---|
| `/webdesign-handwerker` | Handwerk |
| `/webdesign-praxen` | Praxen |
| `/webdesign-kanzleien` | Kanzleien |
| `/webdesign-dienstleister` | lokale Dienstleister |
| `/webdesign-gastronomie` | Restaurants und Gastrobetriebe |

### Orts- und Regionsarchitektur

Empfohlene Struktur:

| URL-Typ | Beispiel | Rolle |
|---|---|---|
| Orts-Hub | `/webdesign-sachsen` | Region und interne Verlinkung |
| Stadtseite Tier 1 | `/webdesign-dresden` | manuell stark, kommerziell |
| Stadtseite Tier 1 | `/webdesign-leipzig` | manuell stark, kommerziell |
| Stadtseite Tier 2 | `/webdesign-chemnitz` | stark, wenn Nachfrage |
| Region | `/webdesign-lausitz` | mehrere Orte sinnvoll buendeln |
| Nicht indexierter Entwurf | `/webdesign-radebeul` mit `noindex` | erst live, wenn eigener Inhalt vorhanden |

Keine automatische Footer-Liste mit hunderten Orten. Interne Links laufen ueber regionale Hubs, Breadcrumbs und kontextuelle Verweise.

---

## 8. Ortsseiten-Publikationsgate

Eine Ortsseite darf erst auf `index,follow`, wenn sie dieses Gate besteht.

### Pflichtinhalt

Jede Ortsseite braucht:

- eigene H1 mit Ort und Leistung.
- konkrete Aussage, wie Sartu in diesem Ort arbeitet.
- 2 bis 4 passende lokale Branchen- oder Nutzungsszenarien.
- ein lokales Problem, das fuer Unternehmen dort plausibel ist.
- klare Preise oder Verweis auf identische Sartu-Pakete.
- Domain-/Launch- und Remote-/Vor-Ort-Erklaerung ohne Fake-Naehe.
- FAQ mit echten lokalen Fragen, nicht nur Standard-FAQ.
- interne Links zu passender Leistungsseite, Preisen, Ablauf, Ratgeber und nahegelegenen Orten/Regionen.
- Schema.org mit `Service`, `BreadcrumbList` und Organization/LocalBusiness, aber ohne falsche Niederlassung.

### Verboten

- Ortsname tauschen und Text sonst gleich lassen.
- Fake-Adresse oder Fake-Buero.
- Fake lokale Telefonnummer.
- erfundene Kundenstimme aus dem Ort.
- erfundene lokale Case Study.
- hunderte Seiten gleichzeitig indexieren.
- Ortsseiten fuer Orte, die Sartu nicht sinnvoll bedienen kann.
- lokale Garantie wie `beste Webdesign Agentur in {Ort}` ohne belastbaren Beleg.

### Interner Qualitaetscheck

Vor Veroeffentlichung:

- mindestens 5 eigene Abschnitte, die ohne Ortsnamen-Tausch nicht fuer jede Stadt passen.
- keine identische Einleitung zu anderen Ortsseiten.
- kein identischer FAQ-Block ohne lokale Anpassung.
- keine Duplikat-Title und keine Duplikat-Meta-Description.
- eindeutiger Canonical auf sich selbst.
- keine Indexierung, wenn weniger als 800 bis 1.200 Woerter echter Nutzinhalt vorhanden sind.
- Search-Console-Beobachtung nach Veroeffentlichung.

Wichtig: Wortzahl ist nur ein interner Filter, keine Ranking-Garantie. Kurze Seiten koennen gut sein, aber programmatische Ortsseiten brauchen genug eigenen Nutzen, sonst werden sie duenn.

---

## 9. Ortsseiten-Template

Dieses Template ist eine Struktur, kein Copy-Paste-Text.

### Abschnitt 1: Hero

- H1: `Webdesign fuer Unternehmen in {Ort}`
- Lead: Sartu erstellt individuell programmierte Firmenwebsites fuer Betriebe in {Ort} und Umgebung.
- CTA: `Bedarf pruefen lassen`
- Sekundaer: `Preise ansehen`

### Abschnitt 2: Lokale Ausgangslage

Beispiele je Ort und Branche:

- lokale Dienstleister konkurrieren sichtbar auf Google Maps und organisch.
- Praxen muessen Vertrauen und Erreichbarkeit schnell zeigen.
- Handwerker brauchen klare Leistungs- und Regionsseiten.
- Kanzleien brauchen Seriositaet, Spezialisierung und einfache Kontaktwege.
- Restaurants brauchen Oeffnungszeiten, Standort, Reservierungsweg und mobile Geschwindigkeit.

### Abschnitt 3: Was Sartu fuer diesen Ort macht

- Paketlogik bleibt gleich.
- Sartu plant Struktur und Texte.
- SEO-/GEO-Basis mit lokalen Begriffen.
- Domain, Launch und Schutzbetrieb.
- keine WordPress-Pflege fuer den Unternehmer.

### Abschnitt 4: Passende Pakete

Kurz:

- Start: ein klares Angebot.
- Wachstum: mehrere Leistungen.
- Platzhirsch: regionale Struktur, Recruiting, Projekte oder mehrere Suchthemen.

Nicht als Paketwahl, sondern als Einordnung.

### Abschnitt 5: Lokale Beispiele

Nur echte oder plausible, nicht erfundene Szenarien:

- `Sanitaerbetrieb in Dresden`: Badsanierung, Heizungswartung, Notfallkontakt.
- `Praxis in Leipzig`: Leistungen, Team, Online-Terminlink.
- `Kanzlei in Chemnitz`: Rechtsgebiete, Vertrauen, Anfragevorqualifizierung.

Wenn kein echtes Beispiel vorhanden ist, klar als Beispiel formulieren und nicht als Referenz.

### Abschnitt 6: Ablauf

Kurze regionale Variante:

1. Bedarf online pruefen.
2. Sartu prueft Ort, Ziel, vorhandene Website und Domain.
3. Angebot im Portal.
4. Onboarding.
5. KI-gestuetzte Umsetzung mit Sartu-QA.
6. Launch und Schutzbetrieb.

### Abschnitt 7: FAQ

Beispiele:

- Muss Sartu in {Ort} sitzen?
- Wie laeuft ein Projekt ohne Vor-Ort-Termin?
- Kann eine bestehende Domain aus {Ort} bleiben?
- Welche Paketgroesse passt fuer mehrere Leistungen in {Ort}?
- Gibt es SEO fuer die Region?

### Abschnitt 8: CTA

`Projekt in {Ort} einschaetzen lassen`

---

## 10. Local-SEO-Rolloutplan

### Phase 1: Kernmaerkte

Manuell stark aufbauen:

- Dresden
- Leipzig
- Chemnitz
- Berlin nur, wenn bewusst bedient
- Hamburg/Muenchen/Koeln nur, wenn Sartu bundesweit sichtbar verkaufen will

Fuer Sartu als kleine Agentur ist zuerst Sachsen/Region glaubwuerdiger als sofort ganz Deutschland.

### Phase 2: regionale Hubs

- Sachsen
- Lausitz
- Mitteldeutschland
- regionale Branchen-Hubs, zum Beispiel `Webdesign fuer Handwerker in Sachsen`

### Phase 3: Search-Console-getriebene Erweiterung

Neue Orte nur, wenn:

- Suchimpressionen oder Anfragen Hinweise geben.
- Ads/Lumi Daten Nachfrage zeigen.
- die Seite echten lokalen Inhalt bekommen kann.
- Sartu die Region wirklich bedienen will.

### Phase 4: programmatische Skalierung mit Noindex-Stage

Das Portal oder ein Generator darf Ortsseiten vorbereiten, aber nicht automatisch indexieren.

Status:

- `draft`: nur intern.
- `noindex_preview`: pruefbar, nicht fuer Suchmaschinen.
- `ready_for_review`: Qualitaetsgate erfuellt.
- `indexable`: manuell freigegeben.
- `retire_or_merge`: bei schlechter Leistung oder Duennheit zusammenlegen.

---

## 11. Informationsarchitektur fuer maximale SEO/GEO

Die Website bekommt sechs Ebenen:

1. **Entity Layer:** Wer ist Sartu? Was ist das Angebot? Wo arbeitet Sartu? Was kostet es?
2. **Commercial Layer:** Webdesign, Firmenwebsite, Preise, Ablauf, Angebote.
3. **Local Layer:** Orte, Regionen, Servicegebiet, lokale Suchintention.
4. **Vertical Layer:** Handwerk, Praxen, Kanzleien, Gastronomie, Dienstleister.
5. **Decision Layer:** Kosten, One-Pager vs. mehrseitig, Relaunch, Baukasten vs. Agentur, WordPress vs. individuell.
6. **Trust Layer:** Ueber uns, echte Arbeitsweise, rechtliche Seiten, Datenschutz, keine Fake-Beweise.

Jede Seite bekommt eine eindeutige Rolle:

- eine primaere Suchintention.
- eine Zielgruppe.
- eine Funnel-Stufe.
- eine Haupt-CTA.
- 3 bis 6 interne Links.
- eine Schema-Strategie.
- ein eigenes Medienmotiv.

---

## 12. Datenmodell fuer Seiten

Jede oeffentliche Seite sollte intern als strukturierter Datensatz behandelbar sein:

```json
{
  "path": "/webdesign-dresden",
  "page_type": "local_service_landing",
  "primary_entity": "Sartu",
  "service": "Webdesign",
  "location": "Dresden",
  "intent": "commercial_local",
  "audience": ["Handwerk", "Praxen", "Dienstleister"],
  "funnel_stage": "consideration",
  "canonical": "https://sartu.de/webdesign-dresden",
  "indexing": "index",
  "primary_cta": "/briefing",
  "supporting_links": ["/preise", "/leistung-webdesign", "/ablauf", "/leistung-lokales-seo"],
  "schema": ["Service", "BreadcrumbList", "FAQPage"],
  "media_policy": "real_or_neutral_no_fake_reference",
  "last_reviewed": "2026-07-18"
}
```

Diese Struktur hilft spaeter dem Adminportal, SEO-/GEO-Checks, Sitemap, interne Links und Agentenjobs konsistent zu halten.

---

## 13. Seitentypen und SEO-Anforderungen

### Startseite

- H1: Angebot/Marke, nicht Slogan.
- klare Paket- und Leistungslinks.
- Organization/LocalBusiness Schema.
- breite Entity-Signale.
- keine Keyword-Ueberladung.

### Leistungsseite

- H1 mit Leistung.
- klare Definition.
- fuer wen geeignet.
- was enthalten ist.
- was nicht enthalten ist.
- Beispielablauf.
- FAQ.
- Links zu Preisen, Ablauf, Ratgeber, Lumi.
- Service Schema.

### Preiseseite

- Pakete in HTML, nicht nur JS.
- Nettopreise eindeutig.
- Umsatzsteuerhinweis.
- Erstjahreswerte.
- Schutzbetrieb.
- Domain und Zahlung.
- keine Paketwahl, sondern Bedarf pruefen.
- FAQPage Schema, wenn FAQ sichtbar.

### Ortsseite

- lokale Suchintention.
- ehrliches Servicegebiet.
- keine Fake-NAP.
- lokale Beispiele.
- regionaler Hub-Link.
- noindex bis Freigabe.

### Ratgeber

- kurze Antwort oben.
- Inhaltsverzeichnis.
- Tabellen, Checklisten, Beispiele.
- interne Links zu kommerziellen Seiten.
- Article Schema.
- Datum sichtbar.
- Update-Rhythmus.

---

## 14. Technische SEO-Architektur

### Rendering

- Static-first HTML.
- wichtige Inhalte serverseitig/rendered im HTML.
- keine primaeren Texte nur per JavaScript nachladen.
- Navigation crawlbar als echte Links.
- Breadcrumbs als sichtbares HTML.
- Formulare funktionieren mit progressiver Verbesserung.

### URLs

- kurze, sprechende URLs.
- keine Query-Parameter fuer indexierbare Hauptseiten.
- keine Umlaute in URLs.
- Bindestriche statt Unterstriche.
- konsistente Singular-/Plural-Entscheidung.
- alte URLs mit 301 weiterleiten.
- keine parallelen `.php`- und Slash-URLs indexieren; Canonical muss eindeutig sein.

### Meta

Jede Seite braucht:

- eindeutigen Title.
- eindeutige Meta-Description.
- genau eine H1.
- Canonical.
- Open Graph Title/Description/Image.
- robots-Entscheidung.
- Breadcrumb.

### Sitemap

- `sitemap.xml` fuer alle indexierbaren Seiten.
- optional getrennte Sitemaps: `sitemap-pages.xml`, `sitemap-ratgeber.xml`, `sitemap-local.xml`.
- nur `indexable` Ortsseiten in die Sitemap.
- `lastmod` nur aendern, wenn sich Inhalt wirklich geaendert hat.

### Robots

- oeffentliche Seiten nach Go-live indexierbar.
- Portal, Login, Admin, Preview, interne Entwuerfe und Agentenartefakte blockieren.
- `noindex` fuer duenne Ortsentwuerfe und rechtlich unfertige Seiten.

### Canonical

- jede Seite canonical auf sich selbst.
- alte PHP-Wrapper duerfen nicht als zweite indexierbare Version konkurrieren.
- Filter-/Preview-/Tracking-URLs canonical auf Hauptseite oder noindex.

---

## 15. Performance-Architektur

Zielwerte:

- LCP unter 2,5 Sekunden.
- INP unter 200 ms.
- CLS unter 0,1.
- mobil zuerst testen.
- keine Layoutspruenge durch Bilder, Fonts oder nachgeladene Karten.

### Bilder

Regeln:

- alle Bilder mit `width` und `height` oder CSS `aspect-ratio`.
- Hero/LCP-Bild nicht lazy laden.
- Hero/LCP-Bild mit `fetchpriority="high"` pruefen.
- unterhalb des ersten Viewports: `loading="lazy"` und `decoding="async"`.
- moderne Formate: AVIF, WebP, Fallback nur wenn noetig.
- responsive `srcset` und `sizes`.
- sinnvolle Kompression statt riesiger Originale.
- keine wichtigen Inhaltsbilder als reine CSS-Backgrounds.
- Alt-Text beschreibt Bildinhalt, nicht Keyword-Spam.
- echte Unternehmensbilder bevorzugen.
- keine KI-Bilder, die wie echte Kundenreferenzen wirken.

Beispiel:

```html
<picture>
  <source type="image/avif" srcset="/assets/img/webdesign-dresden-640.avif 640w, /assets/img/webdesign-dresden-1280.avif 1280w">
  <source type="image/webp" srcset="/assets/img/webdesign-dresden-640.webp 640w, /assets/img/webdesign-dresden-1280.webp 1280w">
  <img
    src="/assets/img/webdesign-dresden-1280.webp"
    width="1280"
    height="800"
    alt="Website-Planung fuer ein regionales Unternehmen"
    loading="lazy"
    decoding="async"
    sizes="(max-width: 768px) 100vw, 50vw">
</picture>
```

### CSS

- kritisches CSS fuer Above-the-fold klein halten.
- keine riesigen ungenutzten CSS-Bloecke.
- Designsystem-Komponenten sauber splitten.
- keine Animationen, die Layout verschieben.
- `font-display: swap`.
- Fonts moeglichst self-hosted als WOFF2.
- keine unnoetigen externen Font-Requests.

### JavaScript

- JS fuer Navigation und Lumi, nicht fuer Hauptinhalt.
- Scripts `defer`, wenn DOM-Abhaengigkeit besteht.
- `async` nur fuer unabhaengige Dritt-Skripte.
- keine Drittanbieter-Skripte ohne Bedarf und Consent.
- Tracking erst nach Einwilligung.
- kein Chatwidget im Standard, solange Performance/Datenschutz nicht geklaert ist.

### Caching und Auslieferung

- statische Assets mit langen Cache-Headern und Versionierung.
- HTML kurz cachen oder gezielt invalidieren.
- Brotli/Gzip.
- HTTP/2 oder HTTP/3.
- CDN nur, wenn Datenschutz und Cache-Invalidierung geklaert sind.

---

## 16. Strukturierte Daten

Schema.org wird nur eingesetzt, wenn der Inhalt sichtbar und wahr ist.

### Global

- `Organization` oder `LocalBusiness` fuer Sartu.
- echte NAP-Daten erst eintragen, wenn final.
- `sameAs` nur echte Profile.
- Logo und URL.

### Seiten

- `BreadcrumbList` auf allen Unterseiten.
- `Service` auf Leistungs- und Ortsseiten.
- `OfferCatalog` oder Angebotsstruktur vorsichtig, wenn Preise sichtbar sind.
- `FAQPage` nur bei sichtbaren FAQs.
- `Article` fuer Ratgeber.
- `WebSite` optional mit SearchAction nur, wenn interne Suche existiert.

### Nicht tun

- Fake-Rating-Schema.
- Review-Schema ohne echte Bewertungen.
- LocalBusiness fuer Orte, an denen es keine Niederlassung gibt.
- FAQ-Schema fuer Fragen, die nicht sichtbar sind.
- Preise im Schema, die von sichtbaren Preisen abweichen.

---

## 17. Interne Verlinkung

Die interne Verlinkung entscheidet, welche Seiten wichtig wirken.

### Regeln

- Startseite linkt auf Leistungen, Preise, Ablauf, Ratgeber, Lumi und 3 bis 5 wichtigste Orts-/Branchen-Hubs.
- Leistungen linkt auf alle Leistungsseiten und Preise.
- Preise linkt auf Lumi, Ablauf, Domain/Launch, Rundum-Schutz und FAQ.
- Ratgeber linkt immer auf passende kommerzielle Seite.
- Ortsseiten linken auf:
  - regionale Hubseite.
  - Hauptleistung.
  - Preise.
  - Ablauf.
  - 2 bis 4 nahe oder thematisch verwandte Orte.
- Keine sitewide Liste aller Orte im Footer.

### Anchor-Texte

Gut:

- `Webdesign fuer Handwerksbetriebe`
- `Website-Pakete ansehen`
- `Ablauf einer Firmenwebsite`
- `Webdesign in Dresden`

Schlecht:

- `hier`
- `mehr`
- `beste Agentur Dresden Leipzig Chemnitz`
- ueberoptimierte Keyword-Ketten.

---

## 18. Content-Architektur fuer GEO

Jede wichtige Seite braucht gut zitierbare Antwortmodule.

### Pflichtmodule

- `Kurz gesagt`: 2 bis 4 Saetze.
- `Fuer wen passt das?`
- `Was ist enthalten?`
- `Was ist nicht enthalten?`
- `Was kostet es?` oder Link zur Preislogik.
- `Wie laeuft es ab?`
- `Welche Entscheidung nimmt Sartu ab?`
- `FAQ`

### Entity-Konsistenz

Immer gleich formulieren:

- Sartu ist eine Webdesign-Agentur.
- Zielgruppe: kleine und mittlere Unternehmen, Praxen, Kanzleien, Handwerk, lokale Dienstleister.
- Keine Privatkunden im Standardprozess.
- Preise netto zzgl. MwSt.
- Start 1.490 EUR, Wachstum 3.900 EUR, Platzhirsch 7.900 EUR, Sonderprojekt ab 12.500 EUR.
- Schutz S/M/L mit 59/129/249 EUR monatlich.
- Platzhirsch ist Empfehlung, aber nicht gegen den Bedarf.
- keine WordPress-/Baukasten-Pflege.
- SEO-/GEO-Basis ab Launch enthalten.
- keine Ranking- oder KI-Nennungsgarantie.

---

## 19. Ratgeber-Strategie

Ratgeber sind Nachfrage-Generatoren, keine Textfriedhoefe.

### Prioritaet

1. Website-Kosten fuer kleine Unternehmen.
2. Website erstellen lassen: Ablauf und Checkliste.
3. One-Pager oder mehrseitige Website.
4. Website-Angebot pruefen.
5. Webdesign-Agentur vs. Baukasten vs. Freelancer.
6. Website ohne WordPress.
7. Website-Relaunch ohne SEO-Verlust.
8. Lokales SEO fuer Unternehmen.
9. SEO-/GEO-Basis beim Launch.
10. Domain wechseln ohne E-Mail-Ausfall.
11. Website-Texte schreiben lassen.
12. Website-Wartung und Schutzbetrieb.
13. Branchenwebsites.
14. echte Case Studies.

### Aufbau pro Artikel

- H1 mit Suchintention.
- kurze Antwort sofort oben.
- Tabelle oder Entscheidungsbaum.
- konkrete Beispiele.
- klare Abgrenzung.
- interne Links.
- CTA zu Lumi.
- sichtbares Update-Datum.

### Lexikon als GEO-/Entity-Hub

Ein Lexikon ist fuer Sartu sinnvoll, aber nur als kuratierter Wissens-Hub. Das Beispiel von WebSeo zeigt das Prinzip gut: ein alphabetischer Hub mit vielen SEO-Begriffen, kurzen Einstiegserklaerungen, internen Links und einzelnen Detailseiten. Fuer GEO ist das stark, weil Begriffe, Entitaeten und Zusammenhaenge maschinenlesbar werden.

**Empfehlung: ja, aber nicht sofort mit 300 Begriffen starten.**

Sartu sollte ein eigenes `Website-Lexikon` oder `Digital-Lexikon` bauen:

- URL: `/lexikon`
- Begriffseiten: `/lexikon/core-web-vitals`, `/lexikon/canonical`, `/lexikon/local-seo`
- alphabetische Navigation.
- Suchfeld.
- kurze Definition oben.
- `Warum wichtig fuer Firmenwebsites?`
- `Wie Sartu damit umgeht`
- interne Links zu Leistung, Ratgeber, Preisen oder Ablauf.

### Warum nicht einfach ein riesiges SEO-Lexikon kopieren?

- Sartu ist keine reine SEO-Agentur.
- 300 duenne Begriffe ohne eigene Perspektive waeren austauschbar.
- Viele SEO-Begriffe ziehen Besucher an, die nie eine Website kaufen.
- Ein Lexikon muss gepflegt werden, sonst wird es schnell veraltet.
- Google bewertet hilfreiche, verlaessliche, people-first Inhalte; reine Massenbegriffe ohne eigenen Nutzen sind riskant.

### Besserer Startumfang

Phase 1: 40 bis 60 Begriffe, die direkt zu Sartu passen:

- Website, Firmenwebsite, One-Pager, Landingpage, Relaunch.
- SEO, GEO, Local SEO, Suchintention, Keyword, Title Tag, Meta Description.
- Canonical, Sitemap, robots.txt, noindex, 301-Weiterleitung.
- Core Web Vitals, LCP, INP, CLS, Lazy Loading, Bildkomprimierung.
- Schema.org, LocalBusiness, FAQPage, Breadcrumb.
- Domain, DNS, Registrar, MX, SPF, DKIM, DMARC.
- Hosting, SSL, Backup, Monitoring.
- WordPress, CMS, statische Website, Headless, Designsystem.
- Briefing, Abnahme, Korrekturrunde, Festpreis, Scope.

Phase 2: Begriffe nach echten Suchdaten, Lumi-Fragen und Kundeneinwaenden erweitern.

Phase 3: ausgewaehlte Begriffe zu Ratgebern ausbauen, wenn Suchintention und Business-Wert hoch sind.

### Lexikon-Seitenstruktur

Jede Begriffseite:

1. H1: Begriff.
2. Kurzdefinition in 2 bis 3 Saetzen.
3. Warum der Begriff fuer Firmenwebsites wichtig ist.
4. Beispiel aus einem Sartu-Projekt.
5. Typischer Fehler.
6. Wie Sartu damit umgeht.
7. Verwandte Begriffe.
8. Link zu passender Leistung oder Ratgeber.

### Lexikon und Leistungen verbinden

Das Lexikon erklaert Begriffe, die Leistungsseite erklaert Ergebnisse.

Beispiel:

- `/lexikon/core-web-vitals` erklaert LCP, INP und CLS.
- `/leistung-webdesign` erklaert, dass Sartu Performance in die Website-Umsetzung einbaut.
- `/leistung-wartung` erklaert, wie technische Regressionen spaeter ueberwacht werden.

So entsteht GEO-Staerke ohne Add-on-Liste.

---

## 20. Conversion-Architektur

SEO bringt nichts, wenn die Anfrage schwer ist.

### Primarpfad

1. Seite beantwortet Suchintention.
2. Besucher versteht: Sartu entscheidet die schwierigen Dinge.
3. Besucher sieht Preise oder Preisrahmen.
4. CTA `Bedarf pruefen lassen`.
5. Lumi fragt wenige Punkte.
6. Vor Kontaktdaten erscheint eine vorlaeufige Empfehlung.
7. Sartu prueft persoenlich.

### CTA-Regeln

- pro Seite ein primaerer CTA.
- keine drei gleich starken Buttons.
- lokale Seiten duerfen CTA lokal formulieren: `Projekt in Dresden einschaetzen lassen`.
- Preiseseite: `Bedarf pruefen lassen`, nicht `Paket waehlen`.
- Ratgeber: `Projekt einschaetzen lassen`, nicht Newsletter.

---

## 21. Seite `/leistungen`

Diese Seite ist zwingend fuer SEO/GEO und Vertrauen.

### Ziel

Erklaeren, was Sartu kann, ohne Auswahlstress zu erzeugen. Die Leistungsseite darf nicht nur Pakete zeigen. Sonst weiss der Kunde nicht, ob Sartu Domain, Texte, SEO, lokale Sichtbarkeit, Portal, Relaunch, Wartung oder technische Umsetzung wirklich beherrscht.

**Entscheidung:**

Die Leistungsseite zeigt alle relevanten Faehigkeiten, aber nicht als buchbare Add-ons. Sie erklaert:

> Das sind die Bausteine, aus denen Sartu ein Website-Ergebnis baut. Sie muessen diese Punkte nicht einzeln auswaehlen; Sartu ordnet sie im Angebot sinnvoll ein.

### H1

`Website, Texte, Sichtbarkeit und Betrieb als ein klares System.`

### Inhalt

1. Kurz: Jede Sartu-Website enthaelt Strategie, Texte, Design, Code, SEO-/GEO-Basis, Domain/Launch, Portal und Schutzbetrieb.
2. Leistungslandkarte als transparente Uebersicht:
   - Strategie und Seitenstruktur.
   - Webdesign und Nutzerfuehrung.
   - Website-Texte und Content-Struktur.
   - individuelle Programmierung ohne WordPress.
   - SEO-/GEO-Basis.
   - lokales SEO und Orts-/Regionslogik.
   - Domain, DNS, E-Mail-Schutz und Launch.
   - Projektportal, Freigaben und kleine Pflege.
   - Rundum-Schutz, Hosting, Backups und Monitoring.
   - Relaunch, Weiterleitungen und Export.
3. Drei Ergebnisebenen:
   - **Website erstellen:** Struktur, Text, Design, Code, Formulare.
   - **Sichtbarkeit aufbauen:** SEO-/GEO-Basis, lokale Signale, interne Verlinkung.
   - **Betrieb sichern:** Portal, Schutzbetrieb, Backups, Monitoring, kleine Pflege.
4. Was der Kunde nicht waehlen muss:
   - System, Design, Hosting, Registrar, SEO-Stufe, Seitenzahl.
5. Was Sartu je Paket tiefer macht.
6. Was Sonderprojekt ist.
7. Links zu Leistungsseiten.

Keine Preise pro Einzelleistung.

### Empfohlener Aufbau der Seite

1. **Hero:** Sartu kann Website, Texte, Sichtbarkeit und Betrieb zusammen.
2. **Was jedes Projekt enthaelt:** kurze, klare Liste.
3. **Leistungslandkarte:** 8 bis 10 Kacheln mit je 2 bis 3 Saetzen und Link zur Detailseite.
4. **Nicht als Add-ons:** Erklaerblock, dass diese Faehigkeiten eingeordnet werden.
5. **Pakete als Ergebnisrahmen:** Start, Wachstum, Platzhirsch, Sonderprojekt.
6. **Portal/Ablauf als USP:** Wie Angebot, Briefing, Freigabe und Pflege laufen.
7. **FAQ:** Brauche ich SEO? Wer schreibt Texte? Was passiert mit Domain? Kann ich selbst pflegen?
8. **CTA:** `Bedarf pruefen lassen`.

### Beispieltexte fuer Leistungskacheln

| Kachel | Kurztext | Link |
|---|---|---|
| Webdesign | Sartu plant Seitenstruktur, Nutzerfuehrung und visuelles System passend zum Unternehmen. | `/leistung-webdesign` |
| Texte | Aus Stichpunkten, Altmaterial und bestaetigten Fakten entstehen klare Website-Texte. | `/leistung-texte` |
| SEO-/GEO-Basis | Seiten werden so aufgebaut, dass Menschen, Google und KI-Suche Angebot, Ort und Nutzen verstehen. | `/leistung-seo` |
| Lokale Sichtbarkeit | Regionen, Leistungen und lokale Signale werden sinnvoll verbunden, ohne duenne Ortsseiten. | `/leistung-lokales-seo` |
| Domain & Launch | Sartu prueft Domain, DNS, E-Mail und Launch, ohne bestehende Postfaecher zu gefaehrden. | `/leistung-domain-launch` |
| Portal | Angebot, Zahlung, Briefing, Freigabe, Feedback und kleine Pflege laufen gefuehrt. | `/leistung-portal` |
| Rundum-Schutz | Hosting, SSL, Backups, Monitoring und technische Pflege bleiben gebuendelt. | `/leistung-wartung` |
| Relaunch | Alte URLs, Inhalte und Suchsignale werden vor dem Wechsel geordnet. | `/website-relaunch` |

Die Kacheln duerfen nicht `ab 490 EUR` oder `dazubuchen` enthalten. Sie zeigen Kompetenz und Suchthemen, nicht Wahlpflicht.

---

## 22. Seite `/leistung-seo`

Diese Seite muss stark sein, aber nicht wie ein SEO-Retainer-Verkauf wirken.

### H1

`SEO-/GEO-Basis fuer Firmenwebsites, die gefunden und verstanden werden.`

### Inhalt

- SEO/GEO ist beim Launch enthalten.
- Was genau enthalten ist.
- Warum keine SEO-Auswahl im Anfrageformular steht.
- Warum spaeterer Ausbau datenbasiert ist.
- Local SEO und Ortsseiten ehrlich erklaeren.
- keine Garantie.

### Pflichtaussage

> Sartu erstellt keine Ortsseiten nur mit ausgetauschtem Stadtnamen. Lokale Seiten werden nur veroeffentlicht, wenn sie echte Suchintention und eigenen Nutzen haben.

---

## 23. Seite `/leistung-lokales-seo`

### H1

`Lokale Sichtbarkeit ohne duenne Ortsseiten.`

### Inhalt

- Google-Unternehmensprofil.
- NAP-Konsistenz.
- lokale Leistungsseiten.
- Servicegebiet.
- Bewertungen nur echt.
- Ortsseiten-Gate.
- Regionale Hubs.
- Domain und E-Mail beim Relaunch.

Diese Seite ist wichtig, weil sie die riskante Ortsseiten-Frage sauber abfaengt.

---

## 24. Seite `/leistung-domain-launch`

Neue Seite empfohlen.

### H1

`Domain, E-Mail und Launch ohne Technikstress.`

### Inhalt

- Kunde bestimmt den Namen.
- Kunde bleibt Domaininhaber.
- Sartu prueft und verwaltet technisch.
- normale Domain bis 30 EUR netto/Jahr im Schutzbetrieb einkalkuliert.
- bestehende Domains werden nicht blind umgezogen.
- E-Mail-Schutz: MX, SPF, DKIM, DMARC, Verifizierungsrecords.
- Launchcheck.
- kein Registrar-Karussell fuer Kunden.

---

## 25. Seite `/leistung-portal`

Neue Seite empfohlen.

### H1

`Ein Projektportal fuer Freigaben und kleine Pflege, kein Website-Baukasten.`

### Inhalt

Kunde kann:

- Angebot sehen.
- Rechnungen und Zahlungen sehen.
- Briefingaufgaben erledigen.
- Dateien hochladen.
- Domain bestaetigen.
- Vorschau pruefen.
- Feedback sammeln.
- Oeffnungszeiten und Kontaktdaten pflegen.
- Seiten deaktivieren/reaktivieren.

Kunde kann nicht:

- Layout bauen.
- Plugins installieren.
- SEO-Felder frei bearbeiten.
- Navigation/URLs selbst umbauen.
- Code sehen.
- Seiten hart loeschen.

---

## 26. Preise-Seite

### Ziel

Preisangst senken und Platzhirsch ankern.

### Darstellung

- Platzhirsch sichtbar empfohlen.
- Start und Wachstum als kleinere ehrliche Empfehlungen.
- Sonderprojekt als Abzweig.
- kein Paket-Auswahl-Button.
- Erstjahreswert zeigen.
- Schutzbetrieb erklaeren.
- Domainregel erklaeren.
- Zahlungsplan erklaeren.
- B2B netto klar.

### Pflichttext

> Sie muessen kein Paket auswaehlen. Die kurze Bedarfseinschaetzung zeigt, welche Loesung wahrscheinlich passt. Sartu prueft das Ergebnis persoenlich vor dem Angebot.

### Marktcheck und Preislogik

Der Marktcheck fuer 2026 bestaetigt die Richtung, zeigt aber eine wichtige Nuance:

- **Start 1.490 EUR** liegt am unteren Rand professioneller One-Pager- und Einstiegspakete. Das ist nur sinnvoll, wenn Start streng auf ein Hauptangebot, eine Seite, wenig Beratung und eine Korrekturrunde begrenzt bleibt.
- **Wachstum 3.900 EUR** ist fuer bis zu 8 Seiten inklusive Text, Design, SEO-/GEO-Basis, Portal und Schutzbetrieb eher guenstig. Es darf nicht als Hauptverkaufsanker wirken, sonst zieht Sartu zu viele mittlere Projekte mit zu kleiner Marge an.
- **Platzhirsch 7.900 EUR** passt gut als empfohlener Anker: Er liegt im unteren bis mittleren Bereich professioneller KMU-/Custom-Websites, hat aber durch Portal, Texte, Struktur und Schutzbetrieb eine klare Begruendung.
- **Sonderprojekt ab 12.500 EUR** ist fuer Shop, Login, Schnittstelle, Mehrmarken oder komplexe Buchung marktfaehig, aber nur als Einstieg. Echte Portale, Shops oder Integrationen koennen deutlich hoeher liegen.
- **Schutz S/M/L** mit 59/129/249 EUR monatlich ist marktfaehig, aber Schutz S darf nur bei sehr einfachen Websites genutzt werden. Monitoring, Backups, Portalbetrieb, Zahlungsgebuehren und Support duerfen die Marge nicht auffressen.

**Konsequenz:**

Sartu sollte oeffentlich nicht billiger werden. Falls zum Start niedrigere Preise noetig sind, dann nur als befristeter Gruenderkunden-Vorteil mit klarer Begrenzung, nicht als neue Preisliste.

Quellen fuer die Preisorientierung: Adfera nennt 2026 fuer einfache Brochure-Sites 5.000-12.000 EUR, Graphek 3.000-20.000 EUR plus 100-500 EUR monatlich, Wyreframe 2.500-50.000 EUR und individuelle Business-Websites bei 8.000-15.000 EUR.

---

## 27. Lumi-Seite

### Ziel

Sehr kurze Anfrage, aber nicht blind.

### Fragen

Maximal vor Kontaktdaten:

1. Was bietet Ihr Unternehmen an?
2. Ort oder Region.
3. Bestehende Website ja/nein.
4. Hauptziel.
5. Umfangssignale: mehrere Leistungen, Regionen, Jobs, Projekte.
6. Sonderrisiken: Shop, Login, Schnittstelle, komplexe Buchung, Mehrsprachigkeit.
7. Domainstatus.
8. Termin oder Besonderheit.

Jede ungewoehnliche Frage bekommt:

- `Warum wir das brauchen`
- Beispiel
- `noch unklar`

---

## 28. Design- und Medienkonzept

Sartu soll hochwertig, ruhig, klar und kontrolliert wirken.

### Regeln

- helle Oberflaechen.
- Petrol/Teal als Akzent, nicht als Ein-Farb-Welt.
- klare Typografie.
- echte oder neutrale Bilder.
- keine Fake-Kundenlogos.
- keine Fake-Testimonials.
- keine uebertriebenen KI-Illustrationen.
- Portal-Screens nur echt oder klar als Muster markieren.
- Karten sparsam, keine Karten in Karten.
- keine dekorativen Orbs oder bunte KI-Verlaeufe.

### Bilder

Prioritaet:

1. echte Sartu-Arbeitsbilder.
2. echte Screens von System/Musterseiten.
3. lizenzierte neutrale Bilder.
4. KI-Bilder nur fuer abstrakte oder neutrale Motive, nie als Kundenbeweis.

---

## 29. Technische QA vor Go-live

Die verbindliche, bereinigte Endkontrolle steht in `SARTU_ENDKONTROLLE_WEBSEITEN.md`. Sie ersetzt die Rohcheckliste aus 8.930 Einzelpunkten durch Sartu-Profile fuer `SARTU-PUBLIC`, `CUSTOMER-WEB`, `CUSTOMER-LOCAL` und `PORTAL-PROD`.

Jede Seite muss mindestens diese Checks bestehen:

- genau eine H1.
- eindeutiger Title.
- eindeutige Description.
- Canonical vorhanden.
- index/noindex korrekt.
- sichtbarer Hauptinhalt im HTML.
- interne Links crawlbar.
- keine kaputten internen Links.
- Breadcrumb vorhanden.
- relevante Schema-Daten validierbar.
- alle Bilder mit `width`, `height`, `alt`, passendem `loading` und `decoding`.
- Hero/LCP-Bild nicht lazy.
- keine Layoutverschiebungen.
- Formulare getestet.
- 404 getestet.
- Sitemap aktuell.
- `robots.txt` korrekt.
- `llms.txt` mit echter Domain.
- noindex nicht versehentlich auf Live-Seiten.
- keine alten Preise, Add-ons, SEO-Stufen oder Minutenbegriffe.

---

## 30. Monitoring

### Direkt nach Go-live

- Google Search Console einrichten.
- Bing Webmaster Tools einrichten.
- Sitemap einreichen.
- Indexierungsstatus pruefen.
- Core Web Vitals beobachten.
- 404/Redirects beobachten.
- Formularzustellung pruefen.
- Serverlogs oder Analytics datenschutzkonform auswerten.

### Laufend

- Impressionen nach Seitentyp.
- CTR je Seite.
- Ranking-/Suchanfragen fuer Preise, Webdesign, Ort, Branche.
- Lumi-Abschlussrate.
- Ortsseiten mit Impressionen ohne Klicks verbessern.
- Ortsseiten ohne Nutzen zusammenlegen, verbessern oder deindexieren.
- neue Ratgeber nur nach Nachfrage.

---

## 31. Umsetzungsempfehlung

### Sofort

- Portal-Screenshot-Kit als Musterprojekt vorbereiten: Cockpit, Angebot, Briefing, Domain, Zahlung, Vorschaufeedback und kleine Pflege.
- `/leistungen` inhaltlich als echte Leistungsuebersicht ausbauen.
- `/leistung-seo` und `/leistung-lokales-seo` auf die neue Local-SEO-Logik schaerfen.
- `/leistung-domain-launch` und `/leistung-portal` als neue Seiten ergaenzen.
- Startseite auf Hauptversprechen, Preise, Platzhirsch und Lumi fokussieren.
- Preiseseite auf Platzhirsch-Anker und keine Auswahl ausrichten.

### Danach

- 5 bis 10 starke Ortsseiten manuell erstellen.
- regionale Hubs bauen.
- Ratgeber nach Suchintention ausbauen.
- Schema/Performance/Interlinking automatisiert pruefen.
- Ortsseiten-Generator nur mit `noindex_preview` und manuellem Freigabegate.
- Portal-Screenshots nach Produktionsreife austauschen und nicht mehr als Muster markieren.

### Erst nach echtem Signal

- weitere Orte skalieren.
- Branchen-Ort-Kombinationen bauen.
- Case Studies veroeffentlichen.
- Folgeangebote fuer Sichtbarkeitsausbau im Portal anbieten.

---

## 32. Harte Leitplanken

- Kein Versuch, Google mit leicht veraenderten Massentexten zu taeuschen.
- Keine Doorway-Seiten.
- Keine Fake-Standorte.
- Keine Fake-Bewertungen.
- Keine Garantie auf Rankings, Anfragen, Umsatz oder KI-Nennung.
- Keine SEO-/GEO-Stufe im Erstangebot.
- Keine Add-on-Liste.
- Keine Aenderungsminuten.
- Keine privaten Kunden im Standardprozess.
- Keine automatische Indexierung programmatisch erzeugter Seiten.
- Keine Domainregistrierung ohne Kundendaten, Zahlung und letzte Verfuegbarkeitspruefung.
- Keine KI-Inhalte ohne Sartu-Pruefung.

---

## 33. Quellen und Orientierung

Diese Quellen stuetzen die Grundrichtung:

- Google Spam Policies, besonders Doorway Abuse und Scaled Content Abuse: https://developers.google.com/search/docs/essentials/spam-policies
- Google AI Search Optimization: https://developers.google.com/search/docs/fundamentals/ai-optimization-guide
- Google Search Essentials: https://developers.google.com/search/docs/essentials
- Google Image SEO Best Practices: https://developers.google.com/search/docs/appearance/google-images
- Google Lazy Loading Guidance: https://web.dev/articles/lazy-loading-images
- Google Structured Data General Guidelines: https://developers.google.com/search/docs/appearance/structured-data/sd-policies
- Google LocalBusiness Structured Data: https://developers.google.com/search/docs/appearance/structured-data/local-business
- Bing Webmaster Guidelines: https://www.bing.com/webmasters/help/webmaster-guidelines-30fba23a
- Bing AI Performance in Webmaster Tools: https://www.bing.com/webmasters/help/ai-performance-9f8e7d6c
- Adfera Webdesign-Kosten 2026: https://adfera.de/ratgeber/webdesign-kosten-rechner/
- Graphek Website-Kosten 2026: https://graphek.de/website-erstellen-lassen-was-kostet-webdesign-wirklich/
- Wyreframe Webdesign-Preise 2026: https://www.wyreframe.de/blog/was-kostet-eine-website
