# Marke und Gestaltung

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus: `CLAUDE.md`, `SARTU_CORPORATE_DESIGN.md`,
> `SARTU_DESIGNSYSTEM.md`, `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`, `design/tokens.css`
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

---

## Grundregeln

- `design/tokens.css` wird **als Erstes** eingebunden, vor jedem Bauteil-CSS
- **Keine Zahl im Bauteil, wo eine Variable existiert.** `border-radius:30px` ist ein Abgabefehler
- Kunden- und Adminbereich müssen visuell unterscheidbar sein
- **Kein Dunkelmodus**

## Farbe

| Rolle | Wert |
|---|---|
| Akzent | `--lime` **`#a3e635`** |
| Akzent, Zeigezustand | `--lime-hover` `#8dc92a` |
| Akzent, blass | `--lime-soft` `#e4f5b8` |
| Tinte | `--ink` `#14110d` |
| Creme | `--cream` `#f6f6f4` |
| Papier | `--paper` `#ffffff` |
| Sand | `--sand` `#eaeae6` |
| Linie | `--line` `#dfdfda` · dunkel `--line-dark` `#2e302e` |

**Es gibt genau eine Akzentfarbe.** Kein Rot für Fehler, kein Grün für Erfolg.

**Lime ist Fläche.** Auf hellem Grund **nie** Schriftfarbe. Auf Lime steht immer `--ink`.
Jede Lime-Fläche auf hellem Grund braucht `1px --line` als Kante.

**Gemessene Kontraste** — Grundlage jeder Farbentscheidung:

| | auf Creme | auf Papier | auf Sand | auf Tinte |
|---|---:|---:|---:|---:|
| Lime `#a3e635` | 1,39 : 1 | 1,51 : 1 | 1,25 : 1 | **12,48 : 1** |
| Tinte `#14110d` | **17,39 : 1** | **18,82 : 1** | **15,60 : 1** | 1,00 : 1 |
| Creme `#f6f6f4` | 1,00 : 1 | 1,08 : 1 | 1,11 : 1 | **17,39 : 1** |

Daraus: **auf hellem Grund Tinte, auf dunklem Grund Lime.**

## Form

Radienskala, skaliert über `--rk`. **Es gibt keine achte Form daneben.**

| Token | Wert |
|---|---|
| `--r-xs` | 8 px |
| `--r-s` | 14 px |
| `--r-m` | 22 px |
| `--r-l` | 34 px |
| `--r-xl` | 52 px |
| `--r-pill` | 999 px |
| `0` | nur wo bewusst kantig |

## Abstände

`--s-1` 6 · `--s-2` 12 · `--s-3` 20 · `--s-4` 32 · `--s-5` 48 · `--s-6` 72 · `--s-7` 104 ·
`--s-8` 140 (px).

## Satzspiegel

| | |
|---|---|
| `--wrap` | **1380 px** — die Außenkante des Inhalts |
| `--gut` | `clamp(20px, 4vw, 56px)` — Rand links und rechts |
| **nutzbare Inhaltsbreite** | **1268 px** am Anschlag |

**Warum nicht 1180.** Üblich ist heute ein Inhaltsbereich von **1140 bis 1200 px**, moderne
Systeme gehen bis 1280. Die Seite lief auf `--wrap: 1180` — abzüglich der Ränder blieben **1068 px
Inhalt und damit unter dem üblichen Band.** Der Eindruck „zusammengequetscht" war messbar, nicht
gefühlt.

## Schriftgrade der Überschriften

| | Regel | am Anschlag |
|---|---|---|
| **H1** | `clamp(32px, 3.45vw, 47px)` | 47 px |
| **H2** | `clamp(27px, 2.9vw, 40px)` | 40 px |
| H3 | 20 · 21 · 24 · 27 · 32 px je Bauteil | — |

**Beide Regeln hängen zusammen und werden nur gemeinsam geändert.**

**Warum die H1 kleiner ist, als sie aussehen dürfte.** Sie steht in der 55-%-Spalte des
Aufmachers, nicht über der vollen Breite. Gemessen am 05.08.2026: bei 54 px in einer 554-px-Spalte
brach sie in **vier Zeilen mit je einem Wort** um — sie las sich als Liste, nicht als Satz. Die
beiden längsten Wörter brauchen bei 54 px **746 px** in einer Zeile; das ist bei 55/45 erst ab
etwa **1530 px** Satzspiegel zu haben und damit weit außerhalb jedes Standards.

**`3.45vw` ist kein gewählter, sondern ein gerechneter Wert.** Ränder und Spalte skalieren mit
`4vw`; wächst die Schrift schneller, holt der Vierzeiler bei mittleren Breiten zurück. Bei
`3.6vw` trat er zwischen 1024 und 1280 px wieder auf. **Die Kurve der Schrift muss der Kurve der
Spalte folgen.**

**Warum die H2 heruntergezogen wurde.** Sie stand auf `clamp(31px, 4.3vw, 50px)` — **größer als
die H1 und schneller wachsend.** Bei 1000 px Fensterbreite ergab das 43 px H2 gegen 33 px H1: die
Rangfolge war umgekehrt. Nicht die H1 war zu klein, **die H2 war zu groß.**

> **Die H1-Grenze ist an einem konkreten Satz gemessen** — dem H1-Auftrag der Startseite mit
> „Firmenwebsites" als längstem Wort. **Ändert der Texter-Skill die H1, wird nachgemessen, nicht
> geschätzt.** Die Prüfung ist mechanisch: bricht die erste Zeile auf **ein** Wort um, ist die
> Schrift für ihre Spalte zu groß.

> **Der 55/45-Aufmacher bleibt** (`10_WEBSITE_SARTU.md`). Er war nicht zur Wahl gestellt; die
> Rechnung oben ist unter dieser Bindung gemacht.

**Gemessenes Ergebnis:** Aufmacher-Bild von 455 auf **655 px**, Leistungsblöcke von 290 auf
**345 px**, H1 durchgehend **drei Zeilen ab 1024 px**, erste Zeile immer mit zwei Wörtern.
**Unter 768 px bleiben vier Einwortzeilen** — bei rund 330 px Spaltenbreite bräuchte die erste
Zeile 24 px Schriftgrad, das wäre keine H1 mehr. **Das ist die Grenze des Satzes, nicht der
Einstellung.**

**Geprüft: H1 > H2 und kein waagerechter Überlauf** bei 390 · 430 · 768 · 900 · 1024 · 1100 ·
1180 · 1280 · 1366 · 1440 · 1512 · 1920 px.

## Logo

**Bestandteile:** Zeichen (Bildmarke) · Wortmarke `SARTU` · Zusatz `DIGITAL`.

**Gemessene Geometrie** der Originaldatei:

| Teil | Breite × Höhe | Mittelachse |
|---|---|---|
| Zeichen | 42,8 × 41,8 | 79,90 |
| Wortmarke | 167,9 × 25,3 | 74,15 |
| Zusatz | 106,0 × 8,0 | — |

**Die Sperrung ist für den Zusatz gezeichnet.** Ohne ihn sitzt die Wortmarke **5,75 Einheiten
zu hoch** (14 % der Logohöhe). Die Fassung ohne Zusatz braucht deshalb eine gesenkte Wortmarke —
im Bau über `translate(0 5.75)` gelöst.

**Verwendung:**

| Ort | Fassung |
|---|---|
| Kopfleiste | Zeichen + Wortmarke, **ohne** Zusatz, 34 px hoch |
| Fußbereich, Spalte | Zeichen + Wortmarke, 30 px |
| Fußbereich, groß | **nur die Wortmarke**, 11 % unten angeschnitten. Das Zeichen ist ein kompakter Körper — angeschnitten wirkt es beschädigt, nicht fortgesetzt |
| Favicon, App-Symbol | nur das Zeichen |
| Druck, Briefkopf, Fahrzeug | Zeichen + Wortmarke **+ Zusatz** |

**Warum der Zusatz nicht in die Kopfleiste gehört:** Er ist 32 % der Wortmarkenhöhe. Bei 21 px
Wortmarke landet er bei **6,6 px** — keine Schrift mehr. Marken mit Zusatz haben deshalb fast
immer zwei Sperrungen.

**Einbindung:** **direkt in die Seite geschrieben**, nicht als `<img>`. Nur so lässt sich die
Wortmarke je Abschnitt umfärben (`currentColor`), und es entsteht kein zusätzlicher Abruf.
Mit `role="img"` und `<title>SARTU</title>`, damit der Name lesbar bleibt.

Vollständige Übergabe- und Dateianforderungen: `design/LOGO_UEBERGABE.md`.

## Schrift

**Noch offen.** Das Masterkonzept führt unter „Typografie final": *reine Grotesk
(Inter / Instrument Sans) vs. Grotesk + dezente editorial Serif für H1*. Die Seite läuft derzeit
auf dem Systemschrift-Stapel — ein Platzhalter, kein Beschluss.

**Regel für die Entscheidung:** Die Logoschrift darf eine **andere** sein als die Fließtextschrift
— das ist der Normalfall. Beide müssen nur derselben Großfamilie angehören. Umgekehrt gilt nicht:
die weit gesperrten Versalien der Wortmarke sind als Fließtextschrift unbrauchbar.

Schriften werden **selbst gehostet** (WOFF2, `font-display:swap`). Externe CDNs sind verboten.
