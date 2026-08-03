# Designstand Kundenbereich und interner Bereich

**Vermerk vom 03.08.2026.** Der Betreiber hat das Erscheinungsbild beider Bereiche abgenommen:
*„Portal ist optisch super."* Dieser Vermerk hält fest, **was damit festliegt und was ausdrücklich
nicht**.

| | |
|---|---|
| **Quelle** | `design/portalkonzept.html` in diesem Repository |
| **Dieselbe Fassung als Seite** | https://claude.ai/code/artifact/6803aa09-c434-4f2e-8583-f763e1d63384 |
| **Abgenommen** | das **Bild**. Aufbau, Flächen, Farben, Radien, Zeichen, Dichte |
| **Nicht abgenommen** | **jeder Text.** Siehe unten |

Die Datei im Repository ist die Quelle. Die Seite zeigt dieselbe Fassung und ist nur bequemer zu
öffnen. Weichen beide ab, gilt die Datei.

---

## Was damit festliegt

Wer das Erscheinungsbild überträgt, nimmt diese Punkte **so**, wie sie im Konzept stehen:

- **Aufbau.** Seitenleiste links in Tinte, darin drei Gruppen; Arbeitsfläche rechts mit
  Titelzeile oben. Der interne Bereich trägt zusätzlich ein durchgehendes Kopfband
- **Außenkante rechtwinklig.** Der Bereich füllt das Fenster; eine Rundung außen gäbe es nicht zu
  sehen. Der Schatten im Konzept gehört zum Dokument und fehlt im Bereich selbst
- **Rundung innen**, an der rechten Kante des Menübands: `border-radius: 0 var(--r-l) var(--r-l) 0`
- **Farben.** Die dreizehn Werte des Websitekonzepts, dazu `--ink-2` für die Leiste des internen
  Bereichs und `--ink-3` für den aktiven Eintrag. Keine Zahl im Bauteil, wo eine Variable steht
- **Schrift.** Dieselben zwei Systemstapel wie die Website. Grundschrift 17 px im Kundenbereich,
  15,5 px im internen. Monograde nach `SARTU_CORPORATE_DESIGN.md` §4
- **Zeichen.** 25 Stück auf 24er Raster mit 2 Punkt Kontur, als Inline-SVG.
  **Vorbehalt:** ob es Zeichen überhaupt geben darf, ist offen — Punkt 9 in
  `OFFENE_ENTSCHEIDUNGEN.md`. Fällt die Entscheidung gegen sie, bleibt alles Übrige gültig
- **Zustände nie über Farbe.** Gefüllt, halb, offen, leer. Kein Rot für Fehler, kein Grün für
  Erfolg
- **Dichte.** Der interne Bereich steht enger als der Kundenbereich: kleinere Schrift, flachere
  Karten, mehr Zeilen je Bildschirm

## Was ausdrücklich offen bleibt

**Kein Text im Konzept ist festgezogen.** Das betrifft Überschriften, Fließtext, Beschriftungen
von Schaltflächen, Marken an Zeilen, Hinweise, Leerzustände und Fehlermeldungen gleichermaßen.

Die Texte im Konzept stehen dort, damit die Flächen echte Länge und echten Rhythmus haben. Sie
sind **Platzhalter mit richtiger Größenordnung**, keine Formulierungsvorlage. Wer sie übernimmt,
übernimmt einen Entwurf als Endstand.

Der Wortlaut entsteht später, und zwar über den dafür vorgesehenen Weg:

| Wofür | Wonach |
|---|---|
| jeder Text, den ein Mensch liest | `.claude/skills/sartu-texter/` und `SARTU_TEXTREGELN.md` |
| Texte, die das Lastenheft wörtlich bindet | Portal-Lastenheft §8, dort je Seite ausgewiesen |
| zu jeder abgegebenen Seite | der Prüfbericht mit Zahlen |

Wo das Lastenheft einen Wortlaut **wörtlich vorgibt** — etwa die H1 je Seite oder die
Fehlermeldungen in §8.7 —, gilt weiterhin das Lastenheft und nicht das Konzept. Das Konzept hat
diese Stellen übernommen, ohne sie zu prüfen.

## Was beim Übertragen zuerst zu tun ist

Der gebaute Stand weicht an drei Stellen ab. Alle drei sind in `OFFENE_ENTSCHEIDUNGEN.md` unter
„Entschieden und eingebaut" mit Datei und Begründung eingetragen:

1. `design/tokens.css` und `public/assets/css/tokens.css` führen noch die warmen Neutralen von
   Ende Juli
2. `app/views/partials/kundenband.php` und `kopfband.php` bauen ein waagerechtes Kopfband statt
   der Seitenleiste
3. `kundenband.php` beschriftet den siebten Menüpunkt mit `Öffnungszeiten`; nach §8 heißt er
   `Inhalte`

## Was das Konzept nicht zeigt

Drei Bildschirme fehlen, weil ihnen Inhalte fehlen, die es noch nicht gibt: Dateien, die
Domainübersicht und der Bedarfsscheck. Sie entstehen, wenn die zugehörigen Daten stehen.
