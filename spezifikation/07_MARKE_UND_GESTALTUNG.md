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

## Satzspiegel — fließend, nicht fest

| | |
|---|---|
| `--wrap` | `clamp(1380px, 90vw, 1800px)` |
| `--gut` | `clamp(20px, 4vw, 56px)` — Rand links und rechts |

**Warum nicht 1180 fest.** Üblich ist ein Inhaltsbereich von **1140 bis 1200 px**, moderne
Systeme gehen bis 1280. Die Seite lief auf `--wrap: 1180` — abzüglich der Ränder blieben **1068 px
Inhalt und damit unter dem üblichen Band.**

**Warum überhaupt fließend.** Ein fester Deckel führt dazu, dass die Seite jenseits davon **nur
noch Rand gewinnt**. Gemessen am 05.08.2026 mit `--wrap: 1380`: auf 1920 nutzte der Inhalt 72 %
der Breite, auf 2560 nur **54 %** — bei 590 px leerem Rand je Seite. Zugleich blieben Schrift und
Visual gedeckelt. **Die Seite wurde auf dem größeren Bildschirm nicht größer, sondern kleiner.**

**Der Lesefluss leidet nicht**, weil jeder Fließtext seine Zeilenlänge in `ch` begrenzt (26 · 34 ·
44 · 60 · 62 · 64 · 70 ch). **Breiter wird nur, was breiter werden soll:** Kartenraster, das
Aufmacher-Visual und die Überschriften.

## Schriftgrade der Überschriften

| | Regel | Spanne |
|---|---|---|
| **H1** | `clamp(32px, 9.9cqw, 92px)` in Versalien · einspaltig `clamp(34px, 7.4cqw, 54px)` | 34 → 89 px |
| **H2** | `clamp(27px, calc(3vw − 6px), 54px)` | 27 → 54 px |
| H3 | 20 · 21 · 24 · 27 · 32 px je Bauteil | — |

**`cqw`, nicht `vw` — die H1 misst sich an ihrer Spalte, nicht am Fenster.** Die Textspalte des
Aufmachers ist dafür `container-type: inline-size`. Das ist keine Schreibweise, sondern die
Auflösung eines Fehlers, der zweimal auftrat.

> **Warum jede vw-Formel scheitern musste.** Die Spalte wächst **stückweise**: `--gut` steht ab
> 1400 px still, `--wrap` erst bei 2000 px, dazwischen liegt der Knick bei 1380 px. Eine gerade
> vw-Kurve kann einer geknickten Spaltenkurve nicht folgen — sie ist an einer Stelle zu flach und
> an der nächsten zu steil. Genau das zeigten die beiden verworfenen Fassungen: `3.6vw` holte den
> Vierzeiler zwischen 1024 und 1280 px zurück, `3.5vw − 6px` bei 1600 und 1800 px. **Nicht der
> Wert war falsch, die Bezugsgröße war es.** Mit `cqw` ist der Schriftgrad ein fester Anteil der
> Spalte — 9,9 % — und der Knick verschwindet, weil er gar nicht erst auftaucht.

**Woran die H1 gebunden ist.** Sie steht in der 55-%-Spalte des Aufmachers, nicht über der vollen
Breite — **ihre Obergrenze ist die Spalte, nicht der Geschmack.** Der Anteil ist gemessen, nicht
gewählt: bei 10,4 cqw füllt die längste Zeile die Spalte genau aus, bei 9,9 cqw bleiben 5 %
Rest. **Die Prüfung ist mechanisch: entstehen vier Zeilen, ist die Schrift für ihre Spalte zu
groß.**

> **Womit gemessen wurde, und warum das die Untergrenze ist.** Alle Werte stammen aus Chromium
> mit **DejaVu Sans** — der breitesten Schrift der Ersatzkette. Auf SF Pro (macOS) und Segoe UI
> (Windows) sind dieselben Zeilen schmaler, der Schriftgrad bleibt gleich. **Die Messung kann
> also zu wenig Platz melden, nie zu viel.**

**Versalien.** Die H1 der Startseite steht in Großbuchstaben, gesetzt über `text-transform`
— **der Quelltext bleibt gemischt**, damit Vorlesewerkzeuge, Suchmaschinen und das Kopieren
normalen Text bekommen. Versalien brauchen weniger Unterschneidung (`-.018em` statt `-.033em`)
und weniger Zeilenabstand (`.95` statt `1.04`), weil keine Unterlängen entstehen.

**Die Deckelung bei 900 px ist kein Feinschliff.** Einspaltig ist die Spalte nicht mehr 55 %,
sondern das ganze Fenster; derselbe Faktor ergäbe bei 768 px eine H1 von **70 px**, die Vorspann
und Knöpfe erschlägt. Der Faktor fällt deshalb an der Umbruchstelle mit.

**Warum das Verhältnis H1 : H2 nicht mehr festgeschrieben ist.** Es lag bei **1,19** und war die
Reparatur eines echten Fehlers: Die H2 stand auf `clamp(31px, 4.3vw, 50px)` und war damit
**größer als die H1 und schneller wachsend** — bei 1000 px Fensterbreite 43 px H2 gegen 33 px H1,
die Rangfolge war umgekehrt.

> **Ersetzt am 05.08.2026.** Der feste Faktor unterstellte, beide Überschriften lebten im selben
> Satzspiegel. Das stimmt nicht mehr: Die H1 ist eine Versalzeile in der 55-%-Spalte, die H2
> gemischter Satz über die volle Breite. **Gebunden ist ab jetzt die Rangfolge, nicht der
> Faktor** — H1 > H2 auf **jeder** geprüften Breite, gemessen 1,26 bis 1,94. Die H2 bleibt
> unverändert; sie war nie das Problem.

> **Der 55/45-Aufmacher bleibt** (`10_WEBSITE_SARTU.md`). Er war nicht zur Wahl gestellt; die
> Rechnung oben ist unter dieser Bindung gemacht.

### Gemessen am 05.08.2026 — vorher / nachher

| Fenster | H1 vorher | H1 nachher | Zeilen vorher | Zeilen nachher | H1 : H2 |
|---|---:|---:|:---:|:---:|---:|
| 390 | 32,0 | **34,0** | **4** | **3** | 1,26 |
| 768 | 32,0 | **52,3** | **2** | **3** | 1,94 |
| 1024 | 32,0 | **49,1** | 3 | 3 | 1,82 |
| 1280 | 38,4 | **61,3** | 3 | 3 | 1,89 |
| 1366 | 41,5 | **65,5** | 3 | 3 | 1,87 |
| 1440 | 44,1 | **65,9** | 3 | 3 | 1,77 |
| 1512 | 46,7 | **65,8** | 3 | 3 | 1,67 |
| 1920 | 61,2 | **84,7** | 3 | 3 | 1,64 |
| 2240 | 64,0 | **88,6** | 3 | 3 | 1,64 |
| 2560 | 64,0 | **88,6** | 3 | 3 | 1,64 |

**Drei Zeilen auf jeder Breite, kein waagerechter Überlauf, H1 > H2 durchgehend.**

> **Die vier Einwortzeilen unter 768 px sind weg — und das lag nicht an der Einstellung.** Die
> vorige Fassung notierte sie als „Grenze des Satzes, nicht der Einstellung". Das war richtig
> beobachtet und falsch geschlossen: **Es war die Grenze *dieses* Satzes.** Mit `Programmierte
> Firmenwebsites zum Festpreis.` bricht er auch bei 350 px Spaltenbreite dreizeilig um, weil die
> drei Sinnabschnitte fast gleich lang sind (13 · 14 · 14 Zeichen). **Wo eine Einstellung an eine
> Grenze stößt, lohnt die Frage, ob der Text die Grenze setzt.**

## Innenabstände des Aufmachers

**Auch sie sind fließend, und das ist kein Feinschliff.** Mit festen Werten (17 · 23 · 21 px)
blieb der Inhaltsblock bei jeder Bildschirmgröße gleich hoch: auf 2560 × 1440 füllte er **53 %**
des Aufmachers, der Rest war Luft. Mit `clamp()`-Abständen sind es **70 %**, bei unverändertem
Bild auf mittleren Größen.

> **Die Reihenfolge der Eingriffe war falsch und ist lehrreich.** Der erste Versuch gab dem
> Aufmacher nur mehr **Höhe** — dadurch wurde die Leere größer, nicht kleiner. **Ein zu leerer
> Bereich wird nicht durch mehr Fläche voller.** Erst Container, Schrift, Visual und Abstände
> zusammen tragen ihn.

**Geprüft: H1 > H2, kein waagerechter Überlauf, Füllung 70–83 %** bei 390 · 1024 · 1280 · 1366 ·
1440 · 1512 · 1920 · 2240 · 2560 px.

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
