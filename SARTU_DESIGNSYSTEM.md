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

| Rolle | Wert | Wofür |
|---|---|---|
| **Creme** | `#f4efe5` | Seitengrund |
| **Papier** | `#fbf8f2` | erhöhte Flächen — Karten, Chips, Zeilen |
| **Tinte** | `#14110d` | Schrift und dunkle Abschnitte |
| **Lime** | `#a3e635` | **der einzige Akzent** |
| Sand | `#e8dfcd` | ruhige Füllfläche |
| Linie | `#ddd4c4` hell · `#332d24` dunkel | Kanten |

### Die Lime-Regel — nachgemessen

| Kombination | Kontrast | Erlaubt |
|---|---|---|
| Tinte **auf** Lime-Fläche | **12,48 : 1** | ✓ |
| Lime als **Schrift** auf Tinte | **12,48 : 1** | ✓ |
| Lime als **Schrift** auf Creme | **1,32 : 1** | ✗ **nie** |

**Lime ist Flächenfarbe.** Auf hellem Grund darf sie nie Schrift tragen — 1,32 : 1 ist unlesbar,
nicht grenzwertig. Auf dunklen Abschnitten ist sie als Schriftfarbe zulässig.

**Jede Lime-Fläche auf hellem Grund braucht 1 px `--line` als Kante.** Ohne Kante verschwimmt sie
mit Creme.

### Alle übrigen Paarungen, gemessen

| Paarung | Kontrast |
|---|---|
| Fließtext auf Papier | 15,60 : 1 |
| Hell auf Tinte | 15,57 : 1 |
| Fließtext auf Creme | 14,43 : 1 |
| Tinte auf Sand | 14,22 : 1 |
| Fließtext dunkler Abschnitt | 9,97 : 1 |
| Gedämpft auf Papier | 8,67 : 1 |
| Gedämpft auf Creme | 8,02 : 1 |
| Label auf Papier | 6,94 : 1 |
| Label auf Tinte | 6,91 : 1 |
| Label auf Creme | 6,42 : 1 |

**Der niedrigste Wert im ganzen System ist 6,42 : 1.** AA verlangt 4,5 : 1 für Fließtext. Das
System hat also überall Reserve — auch dort, wo später jemand eine Schriftgröße ändert.

---

## 2. Formsprache

**Fünf Radien, eine Skala.** `--rk` ist der Regler für alle gleichzeitig; der entschiedene Wert
ist **1**.

| Stufe | Wert bei `--rk:1` | Wofür |
|---|---|---|
| `--r-xs` | 8 px | kleine Marken, Motivpunkte |
| `--r-s` | 14 px | — |
| `--r-m` | 22 px | Zeilen, Bildplätze, Aufklapper |
| `--r-l` | 34 px | Karten |
| `--r-xl` | 52 px | Abschnittskanten |
| `--r-pill` | 999 px | Knöpfe, Chips, Marken |

**Das Motiv:** `--r-leaf` — `52px 22px 52px 22px`. Eine asymmetrische Lozenge als wiederkehrendes
Zeichen. Sie läuft über die ganze Seite:

- im Aufmacher
- auf der hervorgehobenen Lösungskarte
- beim Foto
- bei jedem Motivpunkt vor einem Label

**Das ist die „Verspieltheit" aus der Vorgabe vom 25.07.2026.** Ein Motiv. Effekte gibt es keine.

**Wer einen einzelnen Radius ändert, bricht das System.** Geändert wird `--rk`.

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
| **Chip** | Papier · `--r-pill` · Monospace 12,5 px Versalien |
| **Abschnittsmarke** | Motivpunkt (`--r-leaf` klein) plus Monospace-Label |
| **Karte** | Papier · 1 px `--line` · `--r-l` |
| **Hervorgehobene Karte** | Tinte-Fläche · `--r-leaf` · `--shadow-lift` |
| **Dunkler Abschnitt** | Tinte · Text `#efe9dd` · Fließtext `#c3bcae` · obere Kante `--r-xl` |
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
