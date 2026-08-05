# SARTU — Corporate Design

**Stand:** 01.08.2026
**Gilt für:** alles, was den Namen SARTU trägt:

- Website und Kundenbereich
- E-Mails, Angebote, Rechnungen
- Geschäftspapier
- Profile bei Google und in Verzeichnissen

| Datei | Rolle |
|---|---|
| **Diese Datei** | die **Marke** — Wortmarke, Auftritt, Anwendungen, Verbote |
| `design/tokens.css` | die **Werte** — jede Farbe, jeder Radius, jeder Abstand |
| `SARTU_DESIGNSYSTEM.md` | die **Bausteine** — Knopf, Karte, Fokus, Bildplatz |
| `SARTU_TEXTREGELN.md` + Texter-Skill | die **Sprache** |
| `design/corporate-design.html` | **dieselbe Marke zum Ansehen** — jede Regel als lebendes Beispiel |

> **Diese Datei erfindet nichts.** Jeder Wert stammt aus dem Artefakt vom 30.07.2026, das der
> Betreiber am 01.08.2026 bestätigt hat. Was offen ist, steht in Abschnitt 9 als offen.

---

## 1. Der Name

**`SARTU`** — in Versalien, im Logo **und** im Fließtext. Gemischtes „Sartu" wird nicht verwendet.

| Regel | |
|---|---|
| **Kein Zusatz im Namen** | nicht `SARTU digital`, nicht `SARTU GmbH` im Fließtext |
| **Beisatz nur im Lockup** | `SARTU · Firmenwebsites` ist erlaubt, wenn ein Zusatz gebraucht wird |
| **Nie gebeugt** | „bei SARTU", nicht „bei SARTUs" |
| **Nie übersetzt oder erklärt** | der Name bedeutet nichts, und das bleibt so |

---

## 2. Die Wortmarke

**Bis eine Bildmarke entschieden ist, gilt die reine Wortmarke.** Das ist eine gültige Lösung und
kein Zwischenstand — so festgehalten in `archiv/CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`.

### Klein — Kopfzeile, Absender, Signatur

| Teil | Wert |
|---|---|
| Schriftgrad | **23 px** |
| Gewicht | **700** |
| Zeichenabstand | **−0,04 em** |
| Farbe | `--ink` auf hellem Grund · `#f6f1e6` auf dunklem |
| Beisatz | Monospace, **10,5 px**, Gewicht 500, `letter-spacing:.16em`, Versalien, `--label` |
| Abstand Marke ↔ Beisatz | **8 px**, an der Grundlinie ausgerichtet |

Die Wortmarke ist **nie unterstrichen und nie mit dem Lime-Balken hinterlegt**, obwohl sie ein
Verweis ist. Sie ist die Marke. Ein Textlink sieht anders aus.

### Groß — Fußzeile

| Teil | Wert |
|---|---|
| Schriftgrad | `clamp(84px, 20.5vw, 300px)` |
| Gewicht | 700 · Zeichenabstand **−0,055 em** · Zeilenhöhe **0,78** |
| Farbe | `color-mix(in oklab, var(--ink), #fff 8%)` — Tinte, minimal aufgehellt |
| Verhalten | **beschnitten**. Sie läuft unten aus dem Bild, `user-select:none` |

**Warum sie beschnitten ist:** Eine Marke, die vollständig und mittig dasteht, wirkt wie ein Siegel.
Beschnitten wirkt sie wie Druck auf Papier.

---

## 3. Die Farben und wofür sie stehen

| Farbe | Wert | Bedeutung im Auftritt |
|---|---|---|
| **Creme** | `#f4efe5` | der Grundton. Nicht weiß — die Seite soll wie Papier wirken, nicht wie ein Bildschirm |
| **Papier** | `#fbf8f2` | alles, was einen Schritt näher am Leser ist |
| **Tinte** | `#14110d` | Schrift und die Abschnitte, die Gewicht tragen sollen |
| **Lime** | `#a3e635` | **die einzige Akzentfarbe.** Sie markiert, was der Leser als Nächstes tun kann |
| Sand | `#e8dfcd` | ruhige Fläche ohne Aussage |

### Die drei harten Farbregeln

| # | Regel | Warum |
|---|---|---|
| 1 | **Lime ist Fläche, nie Schrift auf hellem Grund** | gemessen **1,32 : 1** gegen Creme — unlesbar |
| 2 | **Jede Lime-Fläche auf hellem Grund bekommt 1 px `--line`** | ohne Kante verschwimmt sie mit Creme |
| 3 | **Es gibt keine zweite Akzentfarbe** | kein Rot für Fehler, kein Grün für Erfolg. Zustände entstehen aus Text und Fläche |

**Auf Tinte darf Lime Schriftfarbe sein** — 12,48 : 1.

### Wo Lime auftaucht, und wo nicht

| Lime **ja** | Lime **nein** |
|---|---|
| Hauptknopf | Fließtext |
| Unterstreichung eines Verweises beim Überfahren | Überschriften |
| Fokusring außen | Fehlermeldungen |
| Markierung im Aufmacher | Flächen, die nur schmücken |
| erster Schritt im Ablauf | Icons ohne Funktion |

---

## 4. Schrift

**Systemschriften. Keine Schriftlizenz, kein CDN, keine Ladezeit.**

```
--font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui,
             "Helvetica Neue", Helvetica, sans-serif
--font-mono: ui-monospace, "SFMono-Regular", Menlo, Consolas, monospace
```

**Die Monospace-Schrift ist Teil der Marke.** Sie trägt jedes Label, jede
Abschnittsmarke, jede Rechtszeile — immer in Versalien mit weitem Zeichenabstand. Sie ist das
wiederkehrende Zeichen der Marke — und seit dem 01.08.2026 das einzige.

| Verwendung | Grad | Zeichenabstand |
|---|---|---|
| Abschnittsmarke | 12,5 px | `.14em` |
| Chip | 12,5 px | `.08em` |
| Rechtszeile im Fuß | 11,5 px | `.1em` |
| Beisatz der Wortmarke | 10,5 px | `.16em` |

**Eine eigene Schrift ist später möglich**, ohne das System zu ändern: `@font-face` einhängen und
in `--font-sans` an erster Stelle eintragen. Der Platz dafür ist in `design/tokens.css` vorbereitet.

---

## 5. Es gibt kein Motiv

**Entschieden am 01.08.2026.** Bis dahin stand hier eine asymmetrische Lozenge — zwei weite
Rundungen über Kreuz, an fünf Stellen wiederholt. **Sie las sich als Blatt.**

| Warum sie weg ist | |
|---|---|
| Die **Diagonale** macht das Blatt, nicht die Rundung | zwei weite Rundungen, die sich gegenüberliegen, sind die Grundform eines Blattes |
| Sie war **keine Skalenstufe** | eine achte Form neben sieben Radien fällt zwangsläufig auf |
| Sie stand an **fünf Stellen** | *„overuse can dilute your brand expression"* — Dave Chiu, Google Design, 28.11.2018 |

**Ersetzt wurde sie durch nichts.** Ein anderes Zeichen hätte denselben Fehler mit anderer
Silhouette wiederholt. Material Design 3 führt aus demselben Grund nur eine Radienskala.

**Die Formsprache bleibt weich.** 52 px Rundung an einer Abschnittskante ist ungewöhnlich. Das war
die Vorgabe vom 25.07.2026, und sie steckt in der Skala.

**Der Auftritt hat damit gar keinen Schmuck.** Keine Symbole, keine Illustrationen, keine Verläufe,
keine Schlagschatten außer den zwei definierten. Was ihn trägt, steht in Abschnitt 3 und 4. Eine
Farbe mit einer harten Regel. Eine Zweitschrift, die sonst niemand so benutzt.

> **Wenn später doch ein Zeichen soll:** Dann kommt es aus der Bildmarke, sobald die entschieden
> ist. Das ist der einzige Weg, bei dem ein Zeichen etwas bedeutet, statt nur eine Form zu sein.
> Vergleich und Begründung: `design/motiv-recherche.html`.

---

## 6. Bildsprache

| Erlaubt | Verboten |
|---|---|
| Echte Aufnahmen aus dem Kundenbereich, gekennzeichnet | **Bestandsfotos** — Handschlag, Laptop, Callcenter |
| Echtes Foto des Gründers | Fake-Logowolken, Fake-Bewertungen, Fake-Kennzahlen |
| Musterprojekte mit Vermerk `Musterprojekt — kein Kundenauftrag` | Verlaufskugeln, KI-Illustrationen |
| **Beschrifteter Bildplatz**, solange kein Bild vorliegt | **Leerer Platzhalterrahmen** |

**Der beschriftete Bildplatz gehört zum Auftritt.** 2 px gestrichelt, eine Monospace-Zeile und ein
Satz, der sagt, was dort später steht. Wer stattdessen ein Bestandsfoto
einsetzt, widerspricht dem, womit SARTU wirbt.

---

## 7. Anwendungen

### E-Mail

| Teil | Regel |
|---|---|
| Format | **Text und HTML**, HTML ohne externe Bilder |
| Kopf | Wortmarke als Text, nicht als Bild — Bilder werden blockiert |
| Farbe | Creme-Grund, Tinte-Schrift, ein Lime-Knopf je Mail |
| Signatur | Wortmarke · Beisatz · Betreiberdaten aus `operator_settings` |

**Nie mehr als ein Knopf je Mail.** Zwei Knöpfe sind zwei nächste Schritte, und dann gibt es keinen.

### Rechnung und Angebot

Beide entstehen im Kundenbereich und tragen dieselbe Wortmarke, denselben Grundton, dieselbe
Monospace-Zeile für Nummern und Fristen. **Alle Pflichtangaben stammen aus `operator_settings`** —
nichts wird im Vorlagentext festgeschrieben.

**Pflichtzeile auf jedem Preisdokument, im Wortlaut gebunden:**
> `Alle Preise netto zzgl. gesetzlicher Umsatzsteuer. Ausschließlich für Unternehmer.`

### Favicon

Bis zur Logoentscheidung: **`S` in der Website-Schrift**, Tinte auf Lime, quadratisch, ab 16 px
lesbar. Keine Effekte, kein Verlauf.

### Google-Unternehmensprofil und Verzeichnisse

Name **`SARTU`** ohne Zusatz. Beschreibung aus dem Wortlaut der Startseite, nie neu erfunden.
Kategorien und Ort folgen `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1.

---

## 8. Der Ton

**Vollständig geregelt in `SARTU_TEXTREGELN.md` und dem Texter-Skill.** Hier nur, was zur Marke
gehört:

| | |
|---|---|
| Ansprache | **„Sie"**, durchgehend, auch in Fehlermeldungen |
| Haltung | Der Betrieb weiß, was er tut. Wir wissen, was wir tun. Niemand muss belehrt werden |
| Zahlen | Wo eine bekannt ist, steht sie. `1.490 €`, nicht „ab ca. 1.500 €" |
| Grenzen | Was **nicht** enthalten ist, steht genauso deutlich da wie das Enthaltene |
| Verboten | Superlative · Garantien zu Ranking, KI-Nennung oder Umsatz · erfundene Referenzen |

---

## 9. Was offen ist

| Punkt | Stand | Wer entscheidet |
|---|---|---|
| **Bildmarke / Logo** | **offen.** Anforderungen in Design-Briefing §8: einfarbig tauglich, ab 16 px lesbar, hell und dunkel, als SVG, **aktiv gegen bestehende Marken geprüft**. Drei bis fünf Richtungen vorlegen | Betreiber |
| **Gründername und Foto** | offen — `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5 | Betreiber |
| **Firmenname, Rechtsform, Anschrift** | offen — §1. Steht in `operator_settings`, nicht im Vorlagentext | Betreiber |
| **Eigene Schrift** | offen und unkritisch. Der Einhängepunkt ist vorbereitet | später |

**Bis zur Logoentscheidung ist der Auftritt vollständig.** Die Wortmarke trägt ihn allein — das ist
so entschieden und keine Lücke.

---

## 10. Prüfliste vor jeder Veröffentlichung

- [ ] Kein Wert steht hart im Bauteil, wo eine Variable aus `design/tokens.css` existiert
- [ ] Lime nirgends als Schriftfarbe auf hellem Grund
- [ ] Jede Lime-Fläche auf hellem Grund hat eine 1-px-Kante
- [ ] Keine zweite Akzentfarbe im Dokument
- [ ] **Keine Sonderform** neben der Radienskala — kein Zeichen vor einem Label
- [ ] Wortmarke in Versalien, ohne Unterstreichung, ohne Zusatz im Namen
- [ ] Kein Bestandsfoto, kein leerer Bildrahmen
- [ ] Höchstens ein Knopf je E-Mail
- [ ] Pflichtzeile auf jedem Preisdokument, im Wortlaut
- [ ] Fokusring auf jedem bedienbaren Element sichtbar, auf hellem **und** dunklem Grund
- [ ] `prefers-reduced-motion` schaltet jede Bewegung ab


---

## 11. Prüfbericht dieser Datei

```text
TEXTPRUEFUNG   Datei: SARTU_CORPORATE_DESIGN.md   Datum: 01.08.2026

Sätze gesamt (laufende Prosa)           56
Längster Satz                           20 Wörter        Grenze 25 (Abschnitt 6)
Sätze über 20 Wörter                     0               Grenze 0
Aufzählungen >3 Glieder im Satz          0               Grenze 0
Treffer Wortlisten A/B/C                 0               Grenze 0
Gegensatzformel                          1               Grenze 2
Reine Verneinungen ohne Gegenstück       6               erlaubt (Regel 4)
Erfundene Werte                          0               Grenze 0
```

**Zu den sechs Verneinungen:** `Keine Schriftlizenz, kein CDN, keine Ladezeit.` und
`Keine Symbole, keine Illustrationen, keine Verläufe.` sind reine Verneinungen ohne Gegenstück.
Regel 4 nimmt sie ausdrücklich aus.

> **Am Zählskript zwei Fehler behoben.** Beide fielen beim Schreiben dieser Datei auf:
>
> | Fehler | Folge |
> |---|---|
> | Zitatzeichen wurden erst nach der Tabellenerkennung entfernt | Tabellen in Zitatblöcken zählten als Prosa |
> | `stattdessen` löste den Treffer für `statt` aus | Falschtreffer bei der Gegensatzformel |
>
> Beides korrigiert in `tools/textpruefung.py`.
