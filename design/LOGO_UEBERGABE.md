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

### Korrektur zur Farbangabe in der Datei (03.08.2026, nach Prüfung im Browser)

Die Frage „ist eine SVG nicht immer schwarz?" ist berechtigt — **manchmal ja**, und es hängt
allein daran, wie sie eingebunden wird. Sechs Wege getestet, siehe `design/svg-einbindung.html`:

| Einbindung | Ergebnis |
|---|---|
| `<img src="logo.svg">` mit **fest eingetragenen** Farben | Farben bleiben, zweifarbig |
| **inline** im HTML mit fest eingetragenen Farben | Farben bleiben |
| `<img>` mit `fill="currentColor"` | **schwarz** — im `<img>` gibt es kein CSS, das die Farbe liefert |
| Datei **ganz ohne** `fill` | **schwarz** — die Vorgabe von SVG ist Schwarz |
| **inline** mit `fill="currentColor"` | nimmt die CSS-Farbe |
| dieselbe Datei inline auf dunklem Grund | Lime, **ohne zweite Datei** |

**Daraus die korrigierte Anweisung:** Liefern Sie die Datei mit **fest eingetragenen echten
Farben** (`fill="#a3e635"` usw.), nicht mit `currentColor`. Gründe:

- So ist sie überall richtig — in E-Mails, bei Druckereien, bei Partnern, in jedem Programm
- `currentColor` ist eine reine Webseiten-Technik und **wird schwarz**, sobald jemand die Datei
  normal öffnet oder als Bild einbindet
- Das Umfärben für die Seite mache **ich** beim Einbetten. Aus einer Datei mit echten Farben
  lässt sich `currentColor` erzeugen; umgekehrt sind die Farben verloren

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

---

## 6. Wie Profis es wirklich machen — und warum die Konverter scheitern

Recherchiert am 03.08.2026, weil beide Fragen berechtigt waren.

### Der Befund zum Einbinden

Die Praxis ist eindeutig: **SVG für das Logo, direkt in die Seite geschrieben.** SVG-Logos sind
gegenüber PNG rund **89–94 % kleiner**, bleiben auf jedem Bildschirm scharf und lassen sich per
CSS umfärben. Für Favicons gilt: SVG plus eine 16 × 16-Rückfallebene deckt praktisch alles ab.

Für **eine** Marke braucht es kein Sprite-System — das lohnt erst ab vielen Symbolen.

### Warum Adobe an der Stelle scheitert — und Codex und ich auch

Das ist kein Bedienfehler. Inkscapes eigene Anleitung sagt es unumwunden:

> Der Ansatz eines Vektorisierungsprogramms kann weder die exakte Reproduktion des Originalbildes
> sein, noch ist beabsichtigt, ein hundertprozentiges Endergebnis zu erhalten — **kein
> automatisiertes Vektorisierungsprogramm ist dazu in der Lage**, sondern erstellt einen Satz an
> Kurven, den Sie als **Ausgangsmaterial** nutzen können.

Der Grund liegt in der Kantenglättung: Was im PNG eine gerade 45°-Kante ist, besteht aus
Mischpixeln. Der Nachzeichner setzt darauf Dutzende leicht versetzter Punkte. Bei einem Foto
fällt das nicht auf, bei einem **geometrischen Logo** sieht man jede Delle.

Dasselbe gilt für das Nachzeichnen nach Augenmaß — meines wie das von Codex. Ein Zeichen aus
geraden Kanten hat exakte Winkel und Längen; die schätzt niemand aus einem Bild ab.

**Beide Wege sind für diese Aufgabe schlicht die falschen Werkzeuge.**

### Die drei Wege, die wirklich funktionieren — in dieser Reihenfolge

**1. Die Vektor-Ursprungsdatei besorgen.** Jedes gestaltete Logo **existiert** als Vektor —
in Illustrator (`.ai`), als `.eps`, `.pdf`, in Figma oder Affinity. Wer es gemacht hat, hat
die Datei. Bei Logo-Generatoren steckt der SVG-Download meist im Bezahlpaket.

**Das ist in fast allen Fällen die Antwort, und sie kostet nichts.** Fragen Sie zuerst hier,
bevor Sie irgendetwas konvertieren.

**2. Neu zeichnen statt nachzeichnen lassen.** Existiert wirklich kein Vektor, zeichnet man das
Zeichen **von Hand nach** — bei gerader Geometrie in **10 bis 20 Minuten**:

- **Figma** (kostenlos, im Browser): PNG einfügen, Ebene sperren, mit dem Zeichenstift die Ecken
  abklicken. **`Shift` gedrückt halten** rastet auf 45°/90° ein — genau das, was das Zeichen
  braucht. Dann `Export → SVG`
- **Inkscape** (kostenlos): dasselbe mit `B` (Bezier), Winkelrasterung in den Einstellungen

Weil das Zeichen nur gerade Kanten hat, ist das Ergebnis **exakt**, nicht ungefähr — und die
Datei wird winzig, weil sie aus zwölf Punkten besteht statt aus dreihundert.

**3. Automatisch nachzeichnen — nur als Rohmaterial.** Wenn es sein muss:

- **Inkscape → `Pfad → Bitmap nachzeichnen`**, für ein einfarbiges Zeichen `Helligkeitsschwelle`,
  bei mehrfarbigem `Farbquantisierung` mit **4–8 Farben**
- Danach **zwingend** von Hand aufräumen: überzählige Punkte löschen, Kanten gerade ziehen
- Vorher hilft: das PNG **so groß wie möglich** exportieren, Kantenglättung aus

### Wenn Sie beim Aufräumen nicht weiterkommen

Exportieren Sie **irgendein** Ergebnis als SVG — auch einen unsauberen Nachzeichnungsversuch —
und fügen Sie mir den **Quelltext als Text** ein. Eine SVG ist eine Textdatei.

Daraus kann ich rechnerisch machen, was von Hand mühsam ist: Punkte auf ein Raster runden,
fast-gerade Strecken zu geraden zusammenfassen, Winkel auf 45°/90° ziehen, doppelte Punkte
entfernen. **Aus einer wackeligen Nachzeichnung wird so ein sauberes Polygon** — weil ich dann
mit Zahlen arbeite statt mit einem Bild.

Das ist der Punkt, an dem ich wirklich helfen kann. Ein Bild anschauen und Formen raten ist es
nicht — das haben zwei Versuche gezeigt.

### Quellen

- Inkscape, *Vektorisieren von Rastergrafiken* — inkscape.org/doc/tracing/tutorial-tracing.de.html
- SVGVector, *SVG vs PNG: When to Use Each* — svgvector.com/blog/svg-vs-png-when-to-use.html
- CSS-Tricks, *SVG Favicons in Action* — css-tricks.com/svg-favicons-in-action/

---

## 7. Nachtrag: Wortmarke als Text oder als Grafik? (03.08.2026)

Ein früherer Rat in diesem Blatt lautete, das **Icon allein** genüge und „SARTU" bleibe
lebender Text. Nach Prüfung an echten Seiten wird das **eingeschränkt**.

### Was die Agenturen tatsächlich tun

| Agentur | Umsetzung | Schriftzug |
|---|---|---|
| Wee Media | `<img src="logo.svg">` | Grafik im SVG |
| Kopf & Stift | `<img src="…logo.svg">` | Grafik im SVG |
| Hahnsinn | inline `<svg>`, nur Icon | **Wortmarke als normaler Text** |

Zwei von drei nehmen den Schriftzug als Grafik.

### Der Einwand „dann ist es kopierbar" — und was wirklich dahintersteckt

**Kopierbarkeit ist kein Argument.** Ein Firmenname lässt sich nicht dadurch schützen, dass man
ihn zum Bild macht: Er steht ohnehin im Seitentitel, in der Adresse, im Impressum, im Fußbereich,
in der Suchmaschinen-Beschreibung und im `alt`-Text. Wer ihn will, hat ihn. Ein Logo ist
markenrechtlich geschützt, nicht durch Nicht-Markierbarkeit.

**Der echte Grund, den Schriftzug als Vektor zu nehmen, ist ein anderer: die Buchstabenform.**
Eine Wortmarke ist meist *gezeichnet* — eigene Laufweite, angepasste Buchstaben, eine bestimmte
Schrift. Lebender Text bildet das nur nach, wenn genau diese Schrift geladen ist, und selbst dann
weichen Laufweite und optische Korrekturen ab.

**Daraus die Entscheidungsregel:**

- Ist „SARTU" im Logo **eine gezeichnete Wortmarke** — eigene Laufweite, besondere Schrift —
  dann **als Vektor**, sonst sieht die Kopfleiste anders aus als jedes andere Auftreten der Marke
- Ist es **schlicht der Name in einer normalen Schrift**, genügt lebender Text

Nach dem eingereichten Bild zu urteilen — weit gesperrte, geometrische Versalien — ist es
**Fall eins**. Also: **die vollständige Sperrung als SVG mitliefern**, nicht nur das Icon.

### Wie es dann eingebaut wird

**Nicht** als `<img>`, sondern **direkt in die Seite geschrieben**. Der Unterschied ist
entscheidend, weil die Startseite helle **und** dunkle Abschnitte hat:

| | `<img src="logo.svg">` | inline `<svg>` |
|---|---|---|
| Buchstabenform | exakt | exakt |
| Umfärben je Abschnitt | **nein** — zwei Dateien nötig | ja, über CSS |
| zusätzlicher Abruf | ja | **nein** |

Damit der Name trotzdem für Suchmaschinen und Vorleseprogramme lesbar bleibt, bekommt das
eingebettete SVG `role="img"` und ein `<title>SARTU</title>` — dieselbe Wirkung wie ein
`alt`-Text, ohne sichtbaren Text.

**Gebraucht wird also:** `sartu-logo.svg` (Icon + Schriftzug, waagerecht) **und**
`sartu-mark.svg` (Icon allein, für Favicon und App-Symbol).

---

## 8. Logoschrift und Webseitenschrift — zwei verschiedene Aufgaben

### Darf die Logoschrift eine andere sein als die der Seite?

**Ja — das ist sogar der Normalfall, nicht die Ausnahme.** Beide erledigen verschiedene Jobs:

| | Wortmarke | Fließtextschrift |
|---|---|---|
| Wie oft | **einmal** je Seite, in **einer** Größe | tausende Zeichen, 14–20 px |
| Was sie braucht | Eigenart, eigene Laufweite, Wiedererkennung | Lesbarkeit, viele Schnitte, Ziffern, Umlaute |
| Wird sie gesetzt | nein — sie ist **gezeichnet** und liegt als Kurven vor | ja, live vom Browser |

Eine Wortmarke darf weit gesperrt sein und ungewöhnliche Buchstaben haben — sie muss nie
einen Absatz tragen. Eine Fließtextschrift muss genau das, und deshalb ist sie neutraler.

**Die einzige Bedingung: Die beiden dürfen sich nicht streiten.** Gleiche Großfamilie genügt —
zwei Groteske vertragen sich, eine geometrische Wortmarke neben einer Serifenschrift im Text
wäre ein Bruch. Ihre Wortmarke ist eine geometrische Grotesk; die Seite gehört damit ebenfalls
auf eine Grotesk.

**Was Sie also nicht tun sollten:** die Logoschrift auch als Webseitenschrift einsetzen. Weit
gesperrte Versalien sind im Fließtext unlesbar, und die Lizenz für Webschriften kostet
gesondert.

> **Anschluss an eine offene Entscheidung:** Das Masterkonzept führt unter „Typografie final"
> noch als offen: *reine Grotesk (Inter / Instrument Sans) vs. Grotesk + dezente editorial
> Serif für H1*. Die Seite läuft derzeit auf dem Systemschrift-Stapel — ein Platzhalter.
> **Sobald die Logoschrift feststeht, sollte die Fließtextschrift dazu passend gewählt werden**,
> nicht umgekehrt.

### Der Zusatz „Digital" — zwei Gründe dagegen, einer davon neu

**1. Er überlebt die Kopfleiste nicht.** Der Zusatz ist rund ein Viertel so groß wie der
Schriftzug. Bei 21 px Schriftzughöhe — der echten Größe in der Kopfleiste — landet er bei
**gut 5 px**. Das ist keine Schrift mehr, das ist ein grauer Strich.

Das ist kein Fehler Ihres Logos, sondern normal: Deshalb haben Marken mit Zusatz fast immer
**zwei Sperrungen** — eine mit Zusatz für Briefkopf, Fahrzeug und Druck, eine ohne für
Kopfleiste, Favicon und App-Symbol.

**2. Die Kopfleiste trägt bereits einen Zusatz.** Dort steht `SARTU · Webdesign`. Mit einem
Logo, das `Digital` mitbringt, stünden **zwei Zusätze nebeneinander**. Einer muss weg.

**Empfehlung:** `Webdesign` behalten, `Digital` streichen. Begründung steht in §5b der
Website-Vorgabe — *„im Zweifel die konkretere Bezeichnung wählen, nicht die elegantere"* —
und `webdesign` ist zusätzlich der Begriff, auf den die Seite ranken soll. `Digital` sagt
nichts und zieht die Marke zurück in die austauschbare Kategorie, aus der das ganze Konzept
sie heraushält.

**Gebraucht wird also:**

| Datei | Inhalt |
|---|---|
| `sartu-logo.svg` | Icon + Schriftzug, **ohne** Zusatz — Kopfleiste, Fußbereich |
| `sartu-mark.svg` | Icon allein — Favicon, App-Symbol |
| `sartu-logo-zusatz.svg` | Icon + Schriftzug + Zusatz — Druck, Briefkopf, große Flächen |
