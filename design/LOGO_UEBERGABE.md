# Logo — was gebraucht wird, um es einzubauen

Stand 03.08.2026. Dieses Blatt kann an die Person gehen, die die Logodatei hat.

---

## 1. Dateiform: **SVG**, nichts anderes

Das Zeichen besteht aus geraden Kanten und einer Farbfläche. Genau dafür ist SVG gemacht.

| | SVG | PNG |
|---|---|---|
| Schärfe | bei **jeder** Größe exakt — 16 px Favicon wie 3 m Fahrzeugbeschriftung | ab 2× Vergrößerung sichtbar weich |
| Größe dieses Zeichens | **unter 2 KB** | 8–40 KB je Auflösung, und man braucht mehrere |
| Ladezeit | **null**, weil direkt in die Seite geschrieben — kein zusätzlicher Abruf | ein Abruf je Datei |
| Umfärben | über CSS, eine Datei für hell und dunkel | eine Datei **je** Farbe |

Die Frage „maximale Auflösung ohne Ladezeit kaputtzumachen" hat bei einem geometrischen
Zeichen also keine Abwägung: **SVG ist gleichzeitig das schärfste und das kleinste.**

PNG wird trotzdem an **zwei** Stellen gebraucht, weil dort kein SVG geht:

- **`apple-touch-icon.png`** — 180 × 180, für den Startbildschirm auf iOS
- **Vorschaubild für geteilte Links** — 1200 × 630 PNG. Soziale Netzwerke und
  Nachrichtendienste rendern kein SVG

---

## 2. Anforderungen an die SVG-Datei

Damit sie ohne Nacharbeit einsetzbar ist:

- **Echte Pfade**, kein eingebettetes Pixelbild (`<image>` darin macht die Datei wertlos)
- **Schrift in Kurven umgewandelt** — sonst hängt das Logo an einer Schrift, die der Server
  nicht hat
- **`viewBox` vorhanden**, `width`/`height` **ohne** feste Pixelwerte
- **Keine `<style>`-Blöcke und keine `id`-Attribute** — beides kollidiert, sobald mehrere
  SVG in derselben Seite stehen. Farben als `fill`-Attribut direkt am Pfad
- **Aufgeräumt exportiert** — ohne Editor-Metadaten. „Save as → SVG (optimiert)" oder einmal
  durch SVGO
- Bei Löchern im Zeichen: `fill-rule="evenodd"` ausdrücklich setzen

**Am besten schicken Sie den SVG-Quelltext als Text.** Eine SVG ist eine Textdatei — im
Editor öffnen, Inhalt kopieren, einfügen. Das ist verlustfrei und braucht keinen Upload.

---

## 3. Welche Fassungen gebraucht werden

| Datei | Inhalt | wofür |
|---|---|---|
| `sartu-mark.svg` | **nur das Zeichen**, einfarbig | Kopfleiste, Favicon, App-Symbol |
| `sartu-logo.svg` | Zeichen **+ Wortmarke**, waagerecht | Fußbereich, Angebote, E-Mail-Signatur |
| `sartu-logo-stapel.svg` | Zeichen über Wortmarke | schmale Flächen, Quadratformate |

Einfarbig heißt: **alle Pfade in einer Farbe.** Dann genügt eine Datei für hell und dunkel,
weil die Seite die Farbe setzt (`fill="currentColor"`).

---

## 4. Farbkombinationen — gemessen, nicht geschätzt

Kontrastwerte des Zeichens gegen die vier Gründe der Seite:

| Zeichen in | auf Creme | auf Papier | auf Sand | auf Tinte |
|---|---:|---:|---:|---:|
| **Lime** `#a3e635` | 1,39 : 1 | 1,51 : 1 | 1,25 : 1 | **12,48 : 1** |
| **Tinte** `#14110d` | **17,39 : 1** | **18,82 : 1** | **15,60 : 1** | 1,00 : 1 |
| **Creme** `#f6f6f4` | 1,00 : 1 | 1,08 : 1 | 1,11 : 1 | **17,39 : 1** |

Daraus folgt eindeutig:

- **Auf hellem Grund: Zeichen in Tinte.** Lime auf Creme steht bei 1,39 : 1 — es ist als Farbe
  erkennbar, aber es *trägt* nicht. Bei 26 px in der Kopfleiste wirkt es blass
- **Auf dunklem Grund: Zeichen in Lime.** 12,48 : 1, und dort ist Lime die Signalfarbe der Seite
- **Die eingereichte Fassung ist eine Dunkelgrund-Fassung.** Hohle weiße Wortmarke plus Lime
  funktioniert nur auf Tinte — Ihre Startseite ist überwiegend hell. Deshalb wird die
  **Hellgrund-Fassung zusätzlich gebraucht**, nicht ersatzweise

Die Gestaltungsregel dazu steht in `CLAUDE.md`: *„Lime ist Fläche. Auf hellem Grund nie
Schriftfarbe."* Ein Zeichen ist eine Fläche, kein Text — Lime ist dort erlaubt. Es sieht nur
schwach aus, und das ist ein Grund, es nicht zu tun.

**Wenn Sie nur eine Farbe liefern können:** Tinte. Die Kopfleiste ist hell, und für die dunklen
Abschnitte färbt die Seite selbst um.

---

## 5. Was ich damit mache

Sobald die Datei vorliegt:

1. Zeichen **direkt in die Seite geschrieben** statt als Bild geladen — kein zusätzlicher Abruf,
   und die Farbe hängt am Abschnitt statt an der Datei
2. **Favicon** als SVG plus `apple-touch-icon.png` 180 × 180
3. **Größenprobe** bei 16, 20, 32 und 64 px sowie auf allen vier Gründen, mit Zahlen
4. Der Einbauplatz steht schon im CSS von `design/startseite.html` als Kommentar

Ohne die Datei baue ich nichts ein. Ein nachgezeichnetes Zeichen wäre eine Erfindung — und
„nichts erfinden" gilt für ein Logo so gut wie für eine Anschrift oder eine Zahl.
