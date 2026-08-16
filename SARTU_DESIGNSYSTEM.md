# SARTU — Designsystem

**Stand:** 01.08.2026
**Quelle:** Artefakt *„SARTU — Individuell programmierte Firmenwebsites zum Festpreis"*, 30.07.2026.
Vom Betreiber am 01.08.2026 als **verbindliche Richtung** bestätigt.
**Werte:** `design/tokens.css` — die einzige Stelle, an der eine Farbe oder ein Radius steht.

> **Was diese Datei ändert:** `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` beschreibt ein
> **Verfahren** — Recherche, Prüfliste, Varianten, Entscheidung. In 665 Zeilen stehen zwei
> konkrete Werte. Das Verfahren ist damit **abgeschlossen**: Die Entscheidung ist gefallen, hier
> stehen die Zahlen. Das Briefing bleibt als Prüfliste gültig. Als Auswahlverfahren ist es erledigt.

---

## 1. Die drei Farben

> **Berichtigt am 16.08.2026.** Bis dahin stand hier die **warme Reihe vom 30.07.2026**
> (`Creme #f4efe5`, `Papier #fbf8f2`, `Sand #e8dfcd`, `Linie #ddd4c4`). Sie ist seit dem
> 02.08.2026 abgelöst: `design/startseite.html` und `design/portalkonzept.html` führen beide
> die **kühlen Neutralen**, `design/tokens.css` hat sie am 09.08.2026 übernommen. Diese Datei
> war die letzte Stelle mit den alten Werten — sie hätte jeden, der hier statt in `tokens.css`
> nachschlägt, in die abgelöste Fassung geführt. **Die Werte unten stammen aus `tokens.css`;
> es wird keine dritte Fassung erfunden.** Die Lime-Reihe und Tinte sind unverändert; sie sind
> in `SARTU_ENTSCHEIDUNGEN_OFFEN.md` (Rang 1) gebunden, die Neutralen dort ausdrücklich nicht.
>
> **Die alten Werte stehen nur noch als Streichung hier**, damit ein Fund im Verlauf zuzuordnen
> ist — nicht als Alternative.

| Rolle | Wert | Token | Wofür |
|---|---|---|---|
| **Grund** | `#f6f6f4` | `--cream` | Seitengrund. Der Name blieb, der Wert ist kein Creme mehr, sondern Papier |
| **Weiß** | `#ffffff` | `--paper` | erhöhte Flächen — Karten, Zeilen |
| **Tinte** | `#14110d` | `--ink` | Schrift und dunkle Abschnitte |
| **Lime** | `#a3e635` | `--lime` | **der einzige Akzent** |
| Sand | `#eaeae6` | `--sand` | ruhige Füllfläche |
| Linie | `#dfdfda` hell · `#2e302e` dunkel | `--line` · `--line-dark` | Kanten |

`--cream` und `--paper` heißen weiter so, obwohl beide Werte kühl sind. **Umbenannt wird nicht:**
`portalkonzept.html` nennt dieselbe Fläche `--papier` und `--weiss`, `startseite.html` nennt sie
`--cream` und `--paper`. Zwei Namen für denselben Wert sind erträglich, zwei Werte hinter
demselben Namen nicht.

### Die Lime-Regel — nachgemessen

Gemessen am 16.08.2026 gegen die Werte aus `design/tokens.css`, nach WCAG 2.x
(Relativhelligkeit, sRGB).

| Kombination | Kontrast | Erlaubt |
|---|---|---|
| Tinte **auf** Lime-Fläche | **12,48 : 1** | ✓ |
| Lime als **Schrift** auf Tinte | **12,48 : 1** | ✓ |
| Lime als **Schrift** auf dem Grund | **1,39 : 1** | ✗ **nie** |

**Lime ist Flächenfarbe.** Auf hellem Grund darf sie nie Schrift tragen — 1,39 : 1 ist unlesbar,
nicht grenzwertig. Auf dunklen Abschnitten ist sie als Schriftfarbe zulässig.

Gegen den abgelösten Grund `#f4efe5` waren es 1,32 : 1. **Die Zahl ändert sich mit dem Grund,
das Verbot nicht** — der neue, hellere Grund macht es sogar eine Spur schlimmer.

**Jede Lime-Fläche auf hellem Grund braucht 1 px `--line` als Kante.** Ohne Kante verschwimmt sie
mit dem Grund.

### Alle übrigen Paarungen, gemessen

| Paarung | Farbwerte | Kontrast |
|---|---|---|
| Hell auf Tinte | `#f6f6f4` auf `#14110d` | 17,39 : 1 |
| Fließtext auf Weiß | `#1f2120` auf `#ffffff` | 16,20 : 1 |
| Tinte auf Sand | `#14110d` auf `#eaeae6` | 15,60 : 1 |
| Fließtext auf dem Grund | `#1f2120` auf `#f6f6f4` | 14,97 : 1 |
| Fließtext dunkler Abschnitt | `#f6f6f4` auf `#222322` | 14,58 : 1 |
| Gedämpft auf Weiß | `#4a4d4b` auf `#ffffff` | 8,56 : 1 |
| Gedämpft auf dem Grund | `#4a4d4b` auf `#f6f6f4` | 7,91 : 1 |
| Label dunkel auf Tinte | `#9ca09d` auf `#14110d` | 7,11 : 1 |
| Label auf Weiß | `#5a5d5b` auf `#ffffff` | 6,66 : 1 |
| Label auf dem Grund | `#5a5d5b` auf `#f6f6f4` | 6,16 : 1 |

**Der niedrigste Wert im ganzen System ist 6,16 : 1.** AA verlangt 4,5 : 1 für Fließtext. Das
System hat also überall Reserve — auch dort, wo später jemand eine Schriftgröße ändert.

Der Tiefstwert ist gegenüber der warmen Reihe von 6,42 : 1 auf 6,16 : 1 **gesunken**: Der neue
Grund ist heller, das Label unverändert. **Das ist eine Verschlechterung um 0,26 Punkte, gemeldet,
nicht verrechnet.** Sie bleibt weit über AA und ist die Folge einer Entscheidung vom 02.08.2026,
nicht dieses Laufs.

---

## 2. Formsprache

**Fünf Radien, eine Skala.** `--rk` ist der Regler für alle gleichzeitig; der entschiedene Wert
ist **1**.

| Stufe | Wert bei `--rk:1` | Wofür |
|---|---|---|
| `--r-xs` | 8 px | kleine Marken |
| `--r-s` | 14 px | — |
| `--r-m` | 22 px | Zeilen, Bildplätze, Aufklapper |
| `--r-l` | 34 px | Karten |
| `--r-xl` | 52 px | Abschnittskanten |
| `--r-pill` | 999 px | Knöpfe, Chips, Marken |

**Wer einen einzelnen Radius ändert, bricht das System.** Geändert wird `--rk`.

### Es gibt kein Motiv, und das ist entschieden

Bis zum 01.08.2026 stand neben der Skala eine siebte Form: `--r-leaf`, eine asymmetrische Lozenge
mit zwei weiten Rundungen über Kreuz. **Sie las sich als Blatt** und ist gestrichen.

| Warum sie weg ist | |
|---|---|
| **Die Diagonale macht das Blatt** | zwei weite Rundungen, die sich gegenüberliegen, sind die Grundform eines Blattes |
| **Sie war keine Skalenstufe** | eine achte Form neben sieben Radien fällt zwangsläufig auf |
| **Sie stand an fünf Stellen** | Dave Chiu, Google Design, 28.11.2018: *„overuse can dilute your brand expression"* |

**Ersetzt wurde sie durch nichts.** Ein anderes Zeichen hätte den Fehler mit anderer Silhouette
wiederholt. Material Design 3 führt aus demselben Grund nur eine Radienskala und keine Sonderform.
Der Vergleich steht in `design/motiv-recherche.html`.

**Die „Verspieltheit" aus der Vorgabe vom 25.07.2026 bleibt.** Sie steckt in der Skala — 52 px
Rundung an einer Abschnittskante ist ungewöhnlich weich. Sie steckte nie in der Lozenge.

### Was die Marke stattdessen trägt

| Signatur | Wo sie steht |
|---|---|
| **Monospace-Versalie, 0,14 em gesperrt** | jede Abschnittsmarke, jeder Chip, jede Rechtszeile |
| **Lime als Fläche mit 1-px-Kante** | jeder Hauptknopf, jede Markierung |
| **Gedeckter Grund statt Weiß** (`--cream` `#f6f6f4`) | der gesamte Grund |
| **Überschriften mit Gewicht 650 und −0,025 em** | jede Überschrift |

**Keine davon ist eine Form.** Genau deshalb kann keine ein Blattproblem bekommen.

---

## 3. Abstände und Maß

**Acht Stufen, nichts dazwischen:** 6 · 12 · 20 · 32 · 48 · 72 · 104 · 140 px.

| Maß | Wert |
|---|---|
| Inhaltsbreite | `1180px` |
| Seitenrand | `clamp(20px, 4vw, 56px)` |
| Abschnittshöhe | `--s-7` oben und unten |
| Textbreite Überschrift | `26ch` |
| Textbreite Fließtext | `60–70ch` |

---

## 4. Schrift

**Systemschriften.** Keine externe Schriftdatei, kein CDN — das folgt aus Portal-Lastenheft §1.

| Rolle | Größe | Zeilenhöhe | Sonstiges |
|---|---|---|---|
| Fließtext | **18 px** | 1,62 | |
| Vorspann | 20 px | 1,58 | |
| Absatztext | 17,5 px | 1,65 | |
| Kleintext | 16 px | | |
| Monospace-Label | 13 px | | `letter-spacing:.1em`, Versalien |
| **H1** | `clamp(40px, 6.4vw, 80px)` | 1,04 | `letter-spacing:-.035em` |
| **H2** | `clamp(31px, 4.3vw, 50px)` | 1,04 | `letter-spacing:-.025em` |
| H3 | 24 px | 1,04 | |
| Preis | 38 px | | `font-weight:700` |

**Überschriftengewicht ist 650.** Bei 700 wird aus entschieden laut. Der Wert steht so im Artefakt.

> **Der Fließtext liegt bei 18 px.** Die Prüfliste im Design-Briefing verlangt mindestens 17 px.
> Beides erfüllt, mit einem Pixel Reserve.

---

## 5. Bewegung

| Was | Wert |
|---|---|
| Kurve | `cubic-bezier(.22,.61,.36,1)` |
| Schnell | `.2s` — Farbwechsel bei Navigation und Knöpfen |
| Normal | `.25s` — Knopfzustände, Zeilen, Aufklapper |
| Langsam | `1.1s` — Bänderfeld im Aufmacher |
| Einblendweg | `--shift:26px` |

**Alle Einblendungen sind scrollgebunden** (`animation-timeline:view()`). Zeitgesteuert läuft nichts.
**Ohne Browserunterstützung läuft die Animation mit Dauer 0 s in den Endzustand** — der Inhalt ist
immer sichtbar. Genau deshalb wurde diese Technik gewählt.

**`prefers-reduced-motion:reduce` schaltet alles ab.** Schon in `tokens.css` global gesetzt.

---

## 6. Bausteine — die Muster, die wiederkehren

| Baustein | Regel |
|---|---|
| **Knopf** | `--r-pill` · Lime-Fläche · Tinte-Schrift · 1 px `--line` · `19px 32px` · Pfeil rückt bei `:hover` 4 px nach rechts |
| **Zweitknopf** | durchsichtig · 1,5 px Tinte-Rahmen · bei `:hover` Tinte-Fläche |
| **Verweis im Text** | Lime-Balken unter der Zeile, wächst bei `:hover` auf volle Höhe. **Nie farbige Schrift** |
| **Fokus** | Doppelring: 2 px Tinte innen, 4 px Lime außen. Sichtbar auf jedem Grund |
| **Chip** | `--paper` · `--r-pill` · Monospace 12,5 px Versalien |
| **Abschnittsmarke** | **nur** das Monospace-Label. Kein Punkt, kein Zeichen davor |
| **Karte** | `--paper` · 1 px `--line` · `--r-l` |
| **Hervorgehobene Karte** | `--ink`-Fläche · `--r-l` · `--shadow-lift` |
| **Dunkler Abschnitt** | `--ink` · Text `--paper` · zweite Ebene `--label-dark` · Kante `--line-dark` · obere Rundung `--r-xl`. Die beiden Rohwerte des Entwurfs (`#efe9dd`, `#c3bcae`) sind warm und **abgelöst** |
| **Bildplatz ohne Bild** | 2 px gestrichelt, beschriftet, **nie leerer Rahmen** (Design-Briefing §4a) |

---

## 7. Was nicht übernommen wird

| Aus dem Artefakt | Warum nicht |
|---|---|
| **TWEAKS-Block** samt versteckter Radios und `html:has(...)`-Regeln | War das Auswahlwerkzeug. Die Auswahl ist getroffen. Steht im Artefakt selbst als „vor der Übertragung löschen" |
| **Vorschau-Regeln** am Dateiende (`color-scheme`, `data-theme`) | Nur für die Artefakt-Umgebung |
| **Dunkelmodus** | `CODEX_AUFTRAG_PORTAL.md` §5 führt ihn unter „Nicht bauen" |

---

## 8. Was daraus für den Bau folgt

| # | Regel |
|---|---|
| 1 | **`design/tokens.css` wird als Erstes eingebunden.** Vor jedem Bauteil-CSS |
| 2 | **Keine Zahl im Bauteil**, wo eine Variable existiert. Ein `border-radius:30px` ist ein Abgabefehler |
| 3 | **Keine zweite Akzentfarbe.** Es gibt Lime. Sonst nichts |
| 4 | **Kein externes Stylesheet, keine externe Schrift, kein CDN** — Portal-Lastenheft §1 |
| 5 | **Kein JavaScript für Layout oder Bewegung.** Das Artefakt kommt ohne aus, das Projekt auch |
| 6 | Neue Haltepunkte werden **nicht erfunden**. Die neun aus `tokens.css` reichen |

---

## 9. Offen bleibt nur das Bildmaterial

Das Designsystem ist vollständig. **Was fehlt, sind Bilder** — und das ist eine
Geschäftsentscheidung (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5), keine Gestaltungsfrage.

| Stelle | Zustand ohne Bild |
|---|---|
| Zwei Ansichten aus dem Kundenbereich | beschrifteter Bildplatz. Echte Aufnahmen gibt es erst nach A2 |
| Foto des Gründers | **Sektion entfällt vollständig.** Kein leerer Rahmen an einer Vertrauensstelle |
| Drei Musterprojekte | beschrifteter Bildplatz mit Vermerk `Musterprojekt — kein Kundenauftrag` |

**Der Bildplatz ist gestaltet.** 2 px gestrichelt, mit Monospace-Zeile und einem Satz, der sagt,
was dort später steht. Das ist der Unterschied zwischen ehrlich und unfertig.
