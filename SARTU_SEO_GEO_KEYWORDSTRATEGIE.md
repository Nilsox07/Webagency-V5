# SARTU — SEO- und GEO-Keywordstrategie

**Gilt vor der Textproduktion.** Wer Texte schreibt, bevor feststeht, welche Suchintention eine Seite
bedient, schreibt an der Nachfrage vorbei.

**Standortneutral.** Lokale Begriffe sind gesperrt, solange `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1 auf
`offen` steht. Diese Strategie funktioniert vollständig ohne sie — die lokale Ebene ist eine spätere
Ergänzung, kein Fundament.

---

## 1. Die unbequeme Vorbemerkung

**Es gibt hier keine Suchvolumina.** Ich habe keine verlässlichen Zahlen für den deutschen Markt
2026, und geschätzte Zahlen wären schlimmer als keine — sie würden Priorisierungen begründen, die auf
nichts beruhen.

**Was diese Datei liefert:** die Struktur — welche Suchintentionen es gibt, welche Seite welche
bedient, in welcher Reihenfolge gebaut wird und woran der Erfolg gemessen wird.

**Was vor dem Launch noch passieren muss:** Ein Mensch prüft die Begriffe unten mit einem echten
Werkzeug (Search Console nach ein paar Wochen, Keyword Planner, Ahrefs/Semrush-Testzugang oder ein
kleines SEA-Testbudget) und trägt die Zahlen in Spalte „Volumen" ein. **Erst danach** ist die
Reihenfolge belastbar. Bis dahin ist sie eine begründete Annahme.

**Wichtigster Erkenntnisweg zum Start:** ein kleines SEA-Budget auf 5–8 Begriffe. Das liefert in
zwei Wochen echte Klickpreise, echte Klickraten und echte Anfragen — und beantwortet nebenbei die
Frage, ob die Landeseiten überhaupt konvertieren. Das ist mehr wert als jedes Schätztool.

---

## 2. Die sechs Suchintentionen

| Intention | Was der Suchende will | Was er braucht | Nähe zum Auftrag |
|---|---|---|---|
| **Kommerziell** | jemanden beauftragen | Preis, Ablauf, Vertrauen | **hoch** |
| **Vergleichend** | zwischen Optionen entscheiden | ehrliche Gegenüberstellung | **hoch** |
| **Problemorientiert** | ein konkretes Ärgernis lösen | Diagnose und Weg | mittel–hoch |
| **Branchenbezogen** | wissen, was für *seinen* Betrieb gilt | Konkretes statt Allgemeines | mittel–hoch |
| **Definitorisch** | einen Begriff verstehen | kurze, richtige Antwort | niedrig |
| **Lokal** | jemanden in der Nähe | Nähe, Erreichbarkeit | hoch, **aber gesperrt** |

**Die Kernaussage für den Start:** Die schnellsten Hebel sind **vergleichend** und **kommerziell**,
nicht lokal und nicht definitorisch. Wer „Was kostet eine Firmenwebsite?" sucht, ist im Kaufprozess.
Wer „Was ist ein CMS?" sucht, ist es meistens nicht.

---

## 3. Seitenzuordnung — Begriff → URL → Intention → Handlung

### 3.1 Kernseiten

| Zielbegriff (Cluster) | URL | Intention | Handlung am Seitenende |
|---|---|---|---|
| Website erstellen lassen · Firmenwebsite erstellen lassen | `/` | kommerziell | Bedarf prüfen lassen |
| Was kostet eine Website · Website Kosten · Webdesign Preise | `/preise` | kommerziell | Bedarf prüfen lassen |
| Website erstellen lassen Ablauf · wie läuft ein Websiteprojekt | `/ablauf` | problemorientiert | Bedarf prüfen lassen |
| Webdesign Leistungen | `/leistungen` | kommerziell | zur passenden Leistungsseite |
| — (kein Zielbegriff) | `/briefing` | Umwandlung | Bedarfsscheck |

> `/briefing` und alle Danke-Seiten sind `noindex`. Sie sollen nicht ranken, sondern umwandeln.

### 3.2 Leistungsseiten (5)

| Zielbegriff | URL | Intention |
|---|---|---|
| Webdesign ohne WordPress · individuelle Website programmieren lassen | `/leistungen/webdesign` | kommerziell |
| Website für Handwerksbetriebe · Handwerker Website | `/leistungen/website-handwerk` | branchenbezogen |
| Website für Praxen · Praxiswebsite erstellen lassen | `/leistungen/website-praxis` | branchenbezogen |
| SEO Grundlagen Firmenwebsite · lokal gefunden werden | `/leistungen/seo-geo` | kommerziell |
| Website Betreuung · Website Wartung Kosten | `/leistungen/betrieb` | kommerziell |

Der **Kern-USP** der Marke — Festpreis, ein Ansprechpartner, Kundenbereich — gehört auf **jede**
dieser Seiten in den Abschnitt „Welche Entscheidung wir Ihnen abnehmen", nicht nur auf die Startseite.

### 3.3 Vergleichs- und Ratgeberseiten — der schnellste Hebel

Diese Seiten bedienen genau die Fragen, die Menschen **kurz vor** einer Beauftragung stellen. Sie
sind außerdem die Inhalte, die KI-Antwortsysteme gerne zitieren, weil sie eine Frage direkt
beantworten.

| Titel | URL | Intention | Priorität |
|---|---|---|---|
| Was kostet eine Firmenwebsite wirklich? | `/ratgeber/was-kostet-eine-firmenwebsite` | vergleichend | **1** |
| Website erstellen lassen: Agentur, Freelancer oder Baukasten? | `/ratgeber/agentur-freelancer-baukasten` | vergleichend | **2** |
| Webdesign ohne WordPress: Vorteile, Grenzen, Alternativen | `/ratgeber/webdesign-ohne-wordpress` | vergleichend | **3** |
| Website-Festpreis: woran Sie ein seriöses Angebot erkennen | `/ratgeber/website-festpreis-erkennen` | vergleichend | 4 |
| Webdesign-Agentur auswählen: Kriterien für Unternehmer | `/ratgeber/agentur-auswaehlen-kriterien` | vergleichend | 5 |
| Firmenwebsite erneuern: wann ein Relaunch sinnvoll ist | `/ratgeber/relaunch-sinnvoll` | problemorientiert | 6 |
| Website für Handwerker: Pflicht, Kür und typische Fehler | `/ratgeber/website-handwerker-fehler` | branchenbezogen | 7 |
| Barrierefreiheit für Firmenwebsites: was das BFSG verlangt | `/ratgeber/bfsg-firmenwebsite` | problemorientiert | 8 |

**Zum Launch: die ersten drei.** Der Rest folgt nach Search-Console-Daten.

### 3.4 Lexikon

15 Begriffe waren geplant. **Besser: 8 sehr gute statt 15 mittelmäßige.** Das Lexikon baut die
thematische Abdeckung auf, ist aber der **langsamste** Hebel — definitorische Suchen führen selten
direkt zu einem Auftrag.

Auswahlregel: nur Begriffe, die in einem echten Verkaufsgespräch vorkommen und bei denen ein
Missverständnis Geld kostet — z. B. Festpreis, Hosting, Domain, Barrierefreiheit, Ladezeit,
Suchmaschinenoptimierung, Content-Management-System, SSL. **Nicht** jeden Fachbegriff, den es gibt.

---

## 4. Was **nicht** gebaut wird

| Nicht bauen | Grund |
|---|---|
| Eine Seite je Ort über 5.000 Einwohner | Doorway- und skalierter Inhaltsmissbrauch nach Googles Spam-Richtlinien. Bei einem Betreibermodell trifft die Folge später die Kunden |
| „Die beste Webdesign-Agentur in X" | unbelegbar, unglaubwürdig, wettbewerbsrechtlich riskant |
| Seiten für jede Wortvariante desselben Themas | Google nennt das ausdrücklich als Muster, das Rankings manipulieren soll |
| Vergleiche mit namentlich genannten Wettbewerbern | rechtliches Risiko ohne belastbare Datengrundlage |
| Erfundene Studien, Prozentzahlen oder Fallzahlen | die Verbotsliste im Website-Lastenheft §2 gilt hier genauso |
| `llms.txt` als Rankingmaßnahme | Google sagt ausdrücklich, es helfe weder Ranking noch Sichtbarkeit in der Google-Suche |

**Zur Klarstellung bei `llms.txt`:** Die Datei anzulegen ist harmlos und für Nicht-Google-Systeme
möglicherweise nützlich. Sie darf nur **nicht** als Sichtbarkeitsmaßnahme geführt oder gegenüber
Kunden als solche verkauft werden.

---

## 5. GEO — was für KI-Antworten wirklich zählt

Googles eigene Aussage: Optimierung für KI-gestützte Suche ist im Kern **gute SEO** — die
KI-Funktionen greifen auf denselben Index und dieselben Qualitätssysteme zurück. Es gibt keinen
zweiten, geheimen Hebel.

Was praktisch hilft:

| Maßnahme | Warum |
|---|---|
| **Antwort zuerst** — jede Seite beginnt mit 40–60 Wörtern, die die Titelfrage direkt beantworten | zitierfähig, und der Leser bleibt |
| **Tabellen statt Prosa**, wo verglichen wird | maschinell gut erfassbar, menschlich schneller |
| **Echte Zahlen aus dem eigenen Angebot** (Preise, Umfangsgrenzen, Fristen) | überprüfbar und einzigartig — im Gegensatz zu allgemeinen Aussagen |
| **Sichtbares Aktualisierungsdatum und benannte Verantwortung** | Vertrauenssignal für Leser und Systeme |
| **Klare Entität:** überall derselbe Name, dieselbe Beschreibung, `Organization` in strukturierten Daten | ohne konsistente Entität keine Zuordnung |
| **Interne Verlinkung** entlang der Cluster | macht die Themenabdeckung erkennbar |
| **FAQ-Blöcke aus echten Kundenfragen** | genau die Formulierungen, nach denen gesucht wird |

**Was ausdrücklich nicht hilft:** Masse. Zwanzig mittelmäßige Seiten schlagen keine drei sehr guten,
und sie belasten zusätzlich die Qualitätsbewertung der ganzen Domain.

**Quellen** (geprüft 25.07.2026):
- https://developers.google.com/search/docs/fundamentals/ai-optimization-guide
- https://developers.google.com/search/docs/fundamentals/using-gen-ai-content
- https://developers.google.com/search/docs/essentials/spam-policies

---

## 6. Reihenfolge zum Launch

Nach Wirkung sortiert, nicht nach Aufwand:

| # | Seite | Warum an dieser Stelle |
|---|---|---|
| 1 | Startseite | trägt Kategorie, Nutzen, Preislogik und den Kundenbereich als Beweis |
| 2 | `/preise` | die häufigste Frage überhaupt, und SARTUs stärkstes Argument |
| 3 | `/briefing` | ohne Bedarfsscheck gibt es keine Anfrage |
| 4 | `/ablauf` | erklärt den Kundenbereich — hier entsteht der Unterschied zum Wettbewerb |
| 5 | `/leistungen/webdesign` | Hauptleistung |
| 6 | Ratgeber „Was kostet eine Firmenwebsite wirklich?" | stärkste vergleichende Suchintention |
| 7 | Ratgeber „Agentur, Freelancer oder Baukasten?" | trifft die Entscheidungssituation direkt |
| 8 | `/ueber-uns` mit echtem Foto | bei einer unbekannten Einzelperson entscheidend |
| 9 | `/kontakt` | Pflicht |
| 10 | die übrigen vier Leistungsseiten | |
| 11 | Ratgeber 3 + acht Lexikonbegriffe | Themenabdeckung, langsamster Hebel |

**Regel:** Lieber acht sehr gute Seiten live als zwanzig mittelmäßige. Die restlichen Seiten
entstehen nach den ersten Search-Console-Daten — dann weiß man, wonach tatsächlich gesucht wird.

---

## 7. Wie gemessen wird

| Wann | Was |
|---|---|
| **Vor Launch** | Begriffe mit echtem Werkzeug prüfen, Volumenspalte füllen, Reihenfolge bestätigen oder korrigieren |
| **Woche 1–2** | Indexierung aller Launch-URLs, Fehler in der Search Console |
| **Woche 4** | erste Suchanfragen mit Impressionen — häufig andere als erwartet. Das ist die eigentliche Keywordrecherche |
| **Woche 8** | Seiten mit Impressionen aber ohne Klicks → Title und Beschreibung überarbeiten |
| **Woche 12** | Verhältnis Zugriffe → gestartete Bedarfsschecks → abgeschickte Bedarfsschecks. **Diese Zahl entscheidet**, nicht die Rankings |

**Die ehrliche Erwartung:** Für kommerzielle Begriffe wie „Website erstellen lassen" braucht eine
neue Domain **Monate bis Jahre**. Die ersten Kunden kommen über Direktansprache (Masterkonzept §23b),
nicht über Suchmaschinen. Diese Strategie baut das Fundament, das **später** trägt — sie ersetzt
keinen Vertrieb im ersten halben Jahr.
