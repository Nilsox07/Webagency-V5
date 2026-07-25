# Auftrag an Codex — SARTU-Website bauen

**Das ist die Übergabe-Anweisung.** Gib Codex diese Datei als Auftrag; alles Weitere steht in den hier verlinkten Dokumenten.

---

## 1. Was du baust

Die **öffentliche SARTU-Website** (Marketing-/Verkaufsseite). **Nicht** das Kundenportal — dafür gibt es noch kein Lastenheft.

---

## 2. Lesereihenfolge und Rangfolge

Lies in dieser Reihenfolge. Bei Widersprüchen gilt die **niedrigere Nummer**:

1. **`CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md`** — dein Hauptdokument: Struktur, fertige Texte, Feldlabels, Fehlermeldungen, Bildliste, SEO-Tabelle, Abnahmekriterien
2. **`CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`** — wie du die visuelle Ebene recherchierst, prüfst und vorlegst
3. **`CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`** — Begründungen und Architektur, wenn etwas unklar ist
4. **`CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`** — Geschäftsmodell, Preise, Portal, Recht. Nur nachschlagen, nicht ganz lesen
5. `konzepte/` — historische Quelldateien. **Nur zum Nachschlagen.** Sie enthalten veraltete Preise und abgelöste Modelle
6. `design/_verworfen/` — **ignorieren.** Verworfene Designentwürfe, keine Vorgabe, auch nicht als Anregung

---

## 3. Technischer Rahmen

- **Astro** (oder gleichwertig static-first), statisch ausgeliefert, per FTP/CDN deploybar
- **Keine externen Verbindungen zur Laufzeit** — Schriften, Icons, Skripte selbst gehostet
- **JS-Budget:** ≤ 75 KB gzip Startseite, ≤ 40 KB Unterseiten. Gemessen, nicht geschätzt
- Ohne JavaScript grundlegend nutzbar
- Alle Farben, Schriften und Abstände als **zentrale Variablen**
- Repository-Struktur wählst du sinnvoll und dokumentierst sie in einer `README.md`

---

## 4. Ablauf in zwei Phasen — nicht durchbauen

### Phase 1 — Designvorschläge (erst hier stoppen)

Nach `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`:

1. Recherchiere Komponenten, Schriften, Icons und echte Referenzseiten
2. Prüfe jeden Kandidaten gegen die Prüfliste (Lizenz, Pflege, Größe, Barrierefreiheit, Template-Erkennbarkeit)
3. Baue **2–3 klickbare Varianten der Startseite** mit den **echten Texten** aus dem Lastenheft
4. Lege sie vor — mit Herkunftsliste, Lizenzen, gemessenen KB und Core Web Vitals

**Dann anhalten.** Der Mensch entscheidet die Richtung.

### Phase 2 — Rest bauen

Erst nach der Entscheidung: alle Seiten aus dem Lastenheft, im gewählten Stil.

---

## 5. Was du schreiben musst (und was nicht)

**Fertig vorgegeben — wörtlich übernehmen, nicht umformulieren:**
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

---

## 6. Was du NICHT erfindest — hier Platzhalter setzen und melden

| Fehlt | Regel |
|---|---|
| **Impressum, Datenschutz, AGB** | kommen von einer Kanzlei. Nicht selbst formulieren. Seiten anlegen, Inhalt als klar markierten Platzhalter, **nicht** live schalten |
| **Echte Anschrift, Telefon, E-Mail** | steht bewusst in keinem Dokument. Platzhalter setzen und in der README auflisten |
| **Portal-Screenshots** | müssen aus **echter** Oberfläche stammen. Das Portal existiert noch nicht → Bildplätze mit korrekten Maßen anlegen, als „Musteransicht" kennzeichnen, im Übergabebericht melden |
| **Foto für `/ueber-uns`** | echtes Foto, kein Platzhalter, der wie ein Foto wirkt |
| **Logo** | bis zur Entscheidung reine Wortmarke in der gewählten Schrift (gültige Lösung, kein Provisorium) |
| **Referenzen, Bewertungen, Kundenlogos** | existieren nicht. **Niemals** erfinden, auch nicht als Beispiel |
| **Ortsseiten** | nicht zum Launch. Erst nach dem Gate in Masterkonzept §16a |

---

## 7. Abnahme

Die Definition of Done steht in **Lastenheft §17**. Sie gilt vollständig. Besonders:

- keine verbotenen Wörter (§2) auffindbar — prüfe per Volltextsuche
- jede Seite: Status 200, genau eine H1, eigener Title und Description, Canonical, Breadcrumb
- JS-Budget gemessen eingehalten
- `prefers-reduced-motion` getestet
- ohne JavaScript nutzbar, kein Inhalt erst durch Scroll sichtbar
- Kontrast ≥ 4,5:1, Tastaturbedienung vollständig, Fokus sichtbar
- Core Web Vitals mobil im Zielbereich
- keine erfundenen Inhalte

---

## 8. Was du am Ende ablieferst

1. Die Website im Repository, lauffähig
2. **`README.md`**: Stack, Struktur, wie man baut und deployt, wie eine neue Seite angelegt wird
3. **Herkunftsliste**: jedes eingesetzte Fremdteil mit Name, Version, **Lizenz**, Fundstelle
4. **Messwerte**: JS/CSS in KB gzip, LCP/INP/CLS mobil je Kernseite
5. **Offene-Punkte-Liste**: alle Platzhalter aus Abschnitt 6, alle selbst geschriebenen Texte, die geprüft werden müssen
6. **Abnahmeliste** aus Lastenheft §17, Punkt für Punkt abgehakt

**Arbeite nicht ins Blaue:** Kollidiert eine Anforderung mit einer anderen oder fehlt eine Information, melde es, statt zu raten.
