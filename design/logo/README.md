# SARTU — Logo

**Stand:** 01.08.2026 · **Status: Vorschlag, nicht entschieden.**
Design-Briefing §9: *„Du legst vor und entscheidest nicht: Farbwelt, Schriftwahl, Logo,
Gesamtwirkung."*

**Grundlage:** der Entwurf des Betreibers vom 01.08.2026. Nachgebaut als Vektor, Gelenke an die
weiche Formsprache angepasst (Vorgabe vom 25.07.2026).

## Die Dateien

| Datei | Zweck |
|---|---|
| `sartu-mark.svg` | das Zeichen allein, einfarbig über `currentColor` |
| `sartu-logo.svg` | heller Grund — alles in Tinte |
| `sartu-logo-invers.svg` | dunkler Grund — Zeichen in Lime |
| `sartu-logo-digital.svg` | dieselbe Fassung mit Beisatz `DIGITAL`, wie vorgelegt |
| `sartu-favicon.svg` | Tinte auf Lime, gerundetes Quadrat |
| `vorschau.html` | alle Fassungen, Größentest, Regeln |
| **`sartu-wortmarke-schwarz.svg`** | **die komplette Wortmarke in der Konstruktion des Zeichens** — schwarz, Kontur bereits zu Flächen erweitert, keine Schrift nötig |

## Die Wortmarke im selben Stil

Alle fünf Buchstaben folgen der Konstruktion des Zeichens: Waagerechte, Senkrechte und
45-Grad-Diagonalen, keine Kurven. Strichstärke **13** bei Versalhöhe **48**, Gelenke gerundet,
Enden gerade geschnitten.

**Das löst das Schriftproblem.** Die Wortmarke braucht keine Schriftdatei mehr — sie ist Geometrie.
Die Kontur ist in `sartu-wortmarke-schwarz.svg` bereits zu einer Fläche erweitert, damit ist die
Datei ohne weiteren Schritt druckfertig.

| Buchstabe | Konstruktion |
|---|---|
| **S** | identisch mit dem Zeichen |
| **A** | zwei Senkrechte, oben gefast, Querstrich bei 29 |
| **R** | Stamm, gefaste Punze, Bein als Diagonale |
| **T** | Querbalken und Stamm, optisch enger gesetzt |
| **U** | zwei Senkrechte, unten gefast |

**Die Abstände sind optisch, nicht metrisch.** Das T steht enger als A und R, weil sein Weißraum
sonst ein Loch in die Zeile reißt.

## Die Konstruktion

Ein durchgehender Zug in einem 64er-Raster: waagerecht, diagonal, waagerecht, diagonal,
waagerecht. Strichstärke 12 von 64.

```
53,12 → 22,12 → 12,22 → 12,29 → 21,38 → 43,38 → 52,47 → 52,52 → 43,60
```

**Die Gelenke sind gerundet, die Winkel bleiben kantig.** Das ist die Anpassung an die weiche
Formsprache — die scharfe Fassung wurde im Rendertest verglichen und verworfen, weil sie neben den
gerundeten Flächen des Systems fremd wirkt.

## Die Regeln

1. **Schutzraum:** ringsum mindestens eine Strichstärke
2. **Kleinste Größe:** 16 px im Bildschirm, 8 mm im Druck — im Rendertest geprüft
3. Auf **hellem** Grund in Tinte, auf **dunklem** in Lime
4. **Nie Lime auf hellem Grund** — 1,32 : 1
5. **Nie drehen, spiegeln oder verzerren**
6. **Nie mit Schatten, Rahmen oder Verlauf**

## Drei offene Punkte

**Die Markenrecherche fehlt.** Das Briefing verlangt eine aktive Prüfung gegen bestehende
Marken, auch im deutschen Register. Sie wurde **nicht** durchgeführt. **Bei dieser Form ist sie
besonders wichtig:** Ein kantiges S ist die häufigste Konstruktion für ein Technikzeichen
überhaupt — dort ist die Verwechslungsgefahr am größten.

**Die Wortmarke ist echter Text.** In den Lockups steht `SARTU` in der Systemschrift, nicht als
Pfade. **Vor jedem Druck und vor jeder Weitergabe in Pfade umwandeln.**

**Das Zeichen ist eine Kontur, keine Fläche.** Für Druck und Fremdweitergabe muss die Kontur zu
einem Pfad erweitert werden. Auf dem Bildschirm spielt das keine Rolle.

## Zum Beisatz

Die Website führt `Webdesign`, der vorgelegte Entwurf `DIGITAL`. Das Design-Briefing §8 hält fest,
dass der Name **ohne Zusatz** feststeht — *„ohne Zusatz wie ‚digital'"*. Beide Fassungen liegen
hier; **entschieden ist es nicht.** Zwei verschiedene Beisätze auf zwei Trägern wären der
schlechteste Ausgang.

## Verworfen

Ein eigener Entwurf („Satzspiegel", geteiltes Quadrat) wurde am 01.08.2026 vorgelegt und vom
Betreiber abgelehnt: Er hatte keinen Bezug zum vorhandenen Zeichen. Er ist nicht mehr im
Repository.
