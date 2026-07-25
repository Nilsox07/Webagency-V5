# Auftrag an Codex — SARTU-Website bauen

**Das ist die Übergabe-Anweisung.** Gib Codex diese Datei als Auftrag; alles Weitere steht in den hier verlinkten Dokumenten.

---

## 0. Startprüfung — bevor du anfängst

**Führe zuerst die Startprüfung aus `UEBERGABE_DATEILISTE.md` aus.** Prüfe, ob alle dort für den
Websiteauftrag genannten Dateien vorhanden sind, und ob das Hauptdokument die Abschnitte `## 0.` bis
`## 17a.` enthält — insbesondere `## 9.` und `## 14a.`

**Fehlt `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md`, ist dieser Auftrag nicht baubar.** Melde das
und warte auf Nachlieferung. Rekonstruiere nichts, rate nichts, erfinde keinen Ersatz.

Das Ergebnis der Startprüfung ist der **erste Absatz** deines ersten Berichts.

---

## 1. Was du baust

Die **öffentliche SARTU-Website** (Marketing- und Verkaufsseite). **Nicht** das Kundenportal — das ist ein eigenes Projekt mit eigenem Auftrag (`CODEX_AUFTRAG_PORTAL.md`).

---

## 1a. Verhältnis zum Portal-Projekt

**Das Portal wird zuerst fertig**, weil diese Website echte Screenshots daraus als Produktbeweis braucht. Das heißt aber **nicht**, dass du warten musst.

| Darfst du jederzeit | Erst wenn das Portal steht |
|---|---|
| Struktur, Seiten, Navigation, alle Texte | echte Portal-Screenshots einsetzen |
| Bedarfsscheck vollständig, inklusive Fassung ohne JavaScript | **Livegang** |
| SEO-Grundlage, Sitemap, strukturierte Daten | |
| Designvarianten und Entscheidung | |
| Vollständige Staging-Umgebung, klickbar und abnehmbar | |

**Die Sperre ist der Livegang, nicht der Bau.** Eine Website, die mit einem Portal wirbt und dafür eine erfundene Oberfläche zeigt, macht eine Falschaussage über das eigene Produkt. Reservierte Bildflächen mit korrekten Maßen ja — ein Bild, das eine Oberfläche behauptet, nein.

| Was geteilt wird | Was nicht geteilt wird |
|---|---|
| Die **Designentscheidung** — einmal getroffen, gilt für beide. Ablage als reine Wertedatei (`sartu-design-tokens.json`: Farben, Schriftgrößen, Abstände, Radien) plus kurzer Begründung. Jedes Projekt **kopiert** sie und versioniert sie bei sich | Laufzeitcode. Kein gemeinsames Paket, keine Abhängigkeit zwischen den Repositories |
| Die Sprachregeln aus Lastenheft §2 | Build-Werkzeuge, Frameworks, Bibliotheken |
| Die Schnittstelle `POST /api/anfragen` | alles Übrige |

Ein gemeinsames Paket wäre für zwei Projekte, die ein Mensch pflegt, mehr Aufwand als Nutzen. Bei Abweichung gewinnt die Fassung im Portal.

---

## 2. Lesereihenfolge und Rangfolge

Lies in dieser Reihenfolge. Bei Widersprüchen gilt die **niedrigere Nummer**:

1. **`CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md`** — dein Hauptdokument: Struktur, fertige Texte, Feldlabels, Fehlermeldungen, Bildliste, SEO-Tabelle, Abnahmekriterien
2. **`CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`, Abschnitt „10a. Arbeitsverteilung Codex ↔ Claude Code"** — **verbindlich**, siehe Abschnitt 2a
3. **`CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`** — wie du die visuelle Ebene recherchierst, prüfst und vorlegst
4. **`CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md`, Abschnitt „4b. Schnittstelle zur öffentlichen Website"** — maßgeblich für den Formularversand
5. **`CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`** — Begründungen und Architektur, wenn etwas unklar ist
6. **`CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`** im Übrigen — Geschäftsmodell, Preise, Recht. Nur nachschlagen
7. `konzepte/` — historische Quelldateien. **Nur zum Nachschlagen.** Enthalten veraltete Preise und abgelöste Modelle
8. `design/_verworfen/` — **ignorieren.** Verworfene Entwürfe, keine Vorgabe, auch nicht als Anregung

> **Zu Abschnittsverweisen:** Paragraphenzeichen wie `§9.5b` sind eine Abkürzung, kein Beweis.
> Maßgeblich ist die **Überschrift**. Findest du einen Verweis nicht, suche nach dem Thema — und
> **melde** die Abweichung, statt zu raten.

---

## 2a. Wer in diesem Repository schreibt

Verbindlich nach `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`, Abschnitt „10a. Arbeitsverteilung Codex ↔ Claude Code":

> **Pro Repository schreibt genau ein Werkzeug final.**

**In diesem Repository schreibst du — Codex — final.** Claude Code liefert Entwurf, Review und Gegencheck (Copy, Struktur, SEO-Strategie), **schreibt hier aber keinen Produktionsstand**. Findest du Code, den du nicht geschrieben hast und der nicht aus den Vorgabedokumenten stammt: melden, nicht stillschweigend einbauen. Ein Wechsel der Federführung ist möglich, aber nur mit dokumentierter Entscheidung eines Menschen — kein stiller Wechsel mitten im Projekt.

---

## 3. Technischer Rahmen

- **Astro** (oder gleichwertig static-first), Inhaltsseiten statisch ausgeliefert
- **Ein kleiner eigener Serverteil ist Pflicht**, nicht optional: Er nimmt beide Formulare entgegen und spricht serverseitig mit dem Portal. Grund steht in Abschnitt 5a
- **JS-Budget:** ≤ 75 KB gzip Startseite, ≤ 40 KB Unterseiten. Gemessen, nicht geschätzt
- **Bedarfsscheck ohne JavaScript vollwertig bedienbar** (Lastenheft §9.5a) — das ist eine Grundfassung, keine Notlösung
- Alle Farben, Schriften und Abstände als **zentrale Variablen**
- Repository-Struktur wählst du sinnvoll und dokumentierst sie in einer `README.md`

### Was „keine externen Verbindungen" bedeutet

**Verboten — Fremdanbieter zur Laufzeit:** Schrift-, Skript- und Stil-CDNs · Analyse und Tracking · eingebettete Karten · Videoportale · Chat-Widgets · Werbe- und Rätselbild-Dienste · externe Bildhoster. **Kein** Netzwerkaufruf des Browsers darf eine fremde Domain treffen — das wird im Netzwerkprotokoll geprüft.

**Erlaubt und ausdrücklich vorgesehen — eigene Infrastruktur:** der Formularendpunkt auf derselben Domain und dessen serverseitiger Aufruf des SARTU-Portals. Das ist keine Fremdverbindung, sondern das Produkt selbst.

---

## 4. Ablauf in zwei Phasen — nicht durchbauen

### Phase 1 — Designvorschläge (erst hier stoppen)

Nach `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`:

1. Recherchiere Komponenten, Schriften, Icons und echte Referenzseiten
2. Prüfe jeden Kandidaten gegen die Prüfliste (Lizenz, Pflege, Größe, Barrierefreiheit, Template-Erkennbarkeit)
3. Baue **2–3 klickbare Varianten der Startseite** mit den **echten Texten** aus dem Lastenheft
4. Lege sie vor — mit Herkunftsliste, Lizenzen, gemessenen KB und **Laborwerten**

**Zu den Messwerten in dieser Phase:** Echte Core Web Vitals sind **Felddaten** aus tatsächlichen Besuchen. Vor dem Livegang gibt es sie nicht. Liefere daher: Lighthouse-Ergebnis (Werkzeug und Version nennen), CSS- und JS-Größe in KB gzip, LCP-Element benannt und Laborzeit gemessen, CLS im Labor, Total Blocking Time als INP-Ersatz. Behaupte **keine** Feldwerte. Nachgemessen wird nach dem Livegang (Lastenheft §17a).

**Dann anhalten.** Der Mensch entscheidet die Richtung.

### Phase 2 — Rest bauen

Erst nach der Entscheidung: alle Seiten aus dem Lastenheft, im gewählten Stil.

---

## 5. Was du schreiben musst (und was nicht)

**Fertig vorgegeben — wörtlich übernehmen:**
- Startseite: alle Sektionen, Überschriften, Fließtexte, Buttons, 8 FAQ-Antworten
- Header- und Footer-Struktur
- Bedarfsscheck: alle Feldlabels, Hilfetexte, Fehlermeldungen
- Kontaktformular
- Alle Preise, Erstjahreswerte, Zahlungspläne
- Title und Meta-Description je Seite

**Du schreibst selbst — aus den Vorgaben, dann zur Prüfung vorlegen:**
- Die 5 Leistungsseiten: H1 und „Kurz gesagt" stehen fest, die restlichen Abschnitte des Templates schreibst du
- 3 Ratgeberartikel: Gliederung und Kurzantwort stehen fest, den Text schreibst du (je 900–1.300 Wörter)
- 15 Lexikonbegriffe: Struktur steht fest, die Texte schreibst du (je 250–400 Wörter)

**Regeln für selbst geschriebene Texte:** keine erfundenen Zahlen, Studien oder Referenzen. Keine Aussagen über Rankings oder Ergebnisse. Sprachregeln und Verbotsliste aus Lastenheft §2 gelten unverändert.

### Wenn du einen Konflikt in der vorgegebenen Copy siehst

Die Texte sind **Vorgabe, nicht Vorschlag** — du formulierst sie nicht still um. Aber sie sind auch nicht unfehlbar. Fällt dir beim Bauen auf, dass eine H1, ein Title oder ein Einstiegsabsatz mit der Keyword-Struktur, der Seitenhierarchie oder der Nutzerführung kollidiert:

1. **Baue die vorgegebene Fassung** — sie geht in den Stand
2. **Melde den Konflikt** in der Offene-Punkte-Liste: welche Stelle, welcher Konflikt, welche Auswirkung
3. **Lege einen Gegenvorschlag** daneben, mit Begründung
4. Der Mensch entscheidet. Bis dahin bleibt die vorgegebene Fassung stehen

**Nie:** eine bessere Formulierung stillschweigend einsetzen. Auch dann nicht, wenn sie objektiv besser ist — die Texte sind aufeinander und auf das Preismodell abgestimmt, und eine einzelne Änderung kann eine Aussage an anderer Stelle unhaltbar machen.

---

## 5a. Formularversand — der gefährlichste Punkt dieses Auftrags

**`INTAKE_TOKEN` darf niemals im Browser ankommen.** Alles, was ausgeliefert wird — HTML, JavaScript, JSON, Netzwerkantworten — ist öffentlich lesbar. Ein „geheimer" Wert im Frontend ist kein Schutz, sondern eine offene Tür, und jeder findet ihn in Sekunden.

**Richtiger Weg:**
```
Browser  ──POST──▶  eigener Serverteil (gleiche Domain)  ──POST + Token──▶  Portal
```

- Der Browser sendet an die **eigene** Domain, per normalem Formular (auch ohne JavaScript)
- Der Serverteil prüft, ergänzt `submission_id`, `submitted_at`, `form_started_at` und ruft das Portal auf
- Der Token kommt aus einer Umgebungsvariablen und steht **nie** im Repository, **nie** im Ausgabeverzeichnis, **nie** in einer Fehlermeldung

**Spamabwehr:** Honigtopffeld (unsichtbar, `aria-hidden="true"` und `tabindex="-1"`), Zeitregel (Absenden unter 3 Sekunden verwerfen), serverseitige Prüfung aller Felder, Ratenbegrenzung. **Kein** Rätselbild und **kein** Fremddienst zum Start — beides wäre eine externe Verbindung mit eigener Datenschutzfolge und kommt erst, wenn Spam messbar auftritt.

**Doppelabsenden:** Die `submission_id` entsteht beim **Start** des Bedarfsschecks und bleibt über alle Schritte gleich. Nach Erfolg wird auf die Danke-Seite **weitergeleitet** (`303`), nie ein erneut absendbares Formular gezeigt.

Vollständige Festlegung: Lastenheft §9.5b und Portal-Lastenheft Abschnitt 4b.

---

## 6. Was du NICHT erfindest — hier Platzhalter setzen und melden

Alle Platzhalter tragen **eine** einheitliche, suchbare Markierung: `[[PLATZHALTER]]` bzw. `[[SCREENSHOT-FEHLT]]`. Keine freien Formulierungen wie „TODO" oder „Lorem ipsum".

| Fehlt | Regel |
|---|---|
| **Impressum, Datenschutz, AGB** | kommen von einer Kanzlei. Nicht selbst formulieren. Seiten anlegen, Inhalt als `[[PLATZHALTER]]`, **nicht** live schalten |
| **Echte Anschrift, Telefon, E-Mail** | steht bewusst in keinem Dokument. Platzhalter setzen und in der README auflisten |
| **Portal-Screenshots** | müssen aus **echter** Oberfläche stammen. Bildflächen mit korrekten Maßen anlegen, Markierung `[[SCREENSHOT-FEHLT]]`, im Übergabebericht melden. Ein leerer Bildplatz ist **keine** „Musteransicht" — die Kennzeichnung kommt erst mit dem echten Bild |
| **Foto für `/ueber-uns`** | echtes Foto, kein Platzhalter, der wie ein Foto wirkt |
| **Logo** | bis zur Entscheidung reine Wortmarke in der gewählten Schrift (gültige Lösung, kein Provisorium) |
| **Referenzen, Bewertungen, Kundenlogos** | existieren nicht. **Niemals** erfinden, auch nicht als Beispiel |

### Ortsseiten — nicht zum Launch, auch nicht vorbereitend indexierbar

- **Keine** Ortsseite im Produktivbau, auch nicht unverlinkt
- Existieren Prototypen: nur in Staging, mit `noindex`, **nicht** in der Sitemap, **nicht** intern verlinkt, in `robots.txt` ausgeschlossen
- Veröffentlichung erst nach dem Gate in Masterkonzept §16a. Die Entscheidung trifft ein Mensch
- Grund: Massenhaft erzeugte Ortsseiten ohne eigenständigen Wert fallen unter Googles Richtlinien zu Doorway- und skaliertem Inhaltsmissbrauch. Bei einem Betreibermodell trifft die Folge später die Kunden

---

## 7. Abnahme

Die Definition of Done steht in **Lastenheft §17**. Sie gilt vollständig. Besonders:

- keine verbotenen Wörter (§2) auffindbar — prüfe per Volltextsuche
- jede Seite: Status 200, genau eine H1, eigener Title und Description, Canonical, Breadcrumb
- JS-Budget gemessen eingehalten
- `prefers-reduced-motion` getestet
- **Bedarfsscheck mit abgeschaltetem JavaScript vollständig durchlaufen** — getestet, nicht behauptet
- **`INTAKE_TOKEN` kommt in keiner ausgelieferten Datei vor** — Volltextsuche über das gesamte Ausgabeverzeichnis
- **Kein Netzwerkaufruf an eine fremde Domain** — im Netzwerkprotokoll geprüft
- **Startsperre nachgewiesen (§14a):** Der Produktivbau bricht bei einem Platzhalter in Impressum oder Datenschutz nachweislich ab — einmal absichtlich provoziert und belegt
- Kontrast ≥ 4,5:1, Tastaturbedienung vollständig, Fokus sichtbar
- Laborwerte im Zielbereich; echte Core Web Vitals sind Nachmessung (§17a), kein Abnahmekriterium
- keine erfundenen Inhalte

---

## 8. Was du am Ende ablieferst

1. Die Website im Repository, lauffähig, mit getrennten Bau-Betriebsarten (Staging warnt, Produktiv bricht ab)
2. **`README.md`**: Stack, Struktur, Umgebungsvariablen, wie man baut und deployt, wie eine neue Seite angelegt wird
3. **Herkunftsliste**: jedes eingesetzte Fremdteil mit Name, Version, **Lizenz**, Fundstelle
4. **Messwerte**: JS/CSS in KB gzip, Laborwerte je Kernseite mit genanntem Werkzeug
5. **Offene-Punkte-Liste**: alle Platzhalter aus Abschnitt 6, alle selbst geschriebenen Texte, alle gemeldeten Copy-Konflikte aus Abschnitt 5
6. **Abnahmeliste** aus Lastenheft §17, Punkt für Punkt abgehakt

**Arbeite nicht ins Blaue:** Kollidiert eine Anforderung mit einer anderen oder fehlt eine Information, melde es, statt zu raten.
