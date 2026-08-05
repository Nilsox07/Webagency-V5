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
