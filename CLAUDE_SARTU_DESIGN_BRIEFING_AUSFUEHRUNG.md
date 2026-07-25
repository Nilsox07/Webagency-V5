# SARTU — Design-Briefing für die ausführende KI

**Stand:** 25.07.2026
**Adressat:** die KI, die die SARTU-Website tatsächlich baut (Codex bzw. Claude Code).

**Was dieses Dokument ist:** eine **Such-, Auswahl- und Zusammenbau-Anleitung** für die visuelle Ebene.
**Was es nicht ist:** eine Designvorgabe. Es enthält bewusst **keine** festgelegten Farben, Schriften, Radien, Logos oder Bewegungsdetails.

> **Warum so:** Die visuelle Qualität soll aus echten, gepflegten Quellen kommen — aus Bibliotheken, Komponenten-Systemen und realen Referenzseiten —, nicht aus einem am Reißbrett erfundenen Farbschema. Deine Aufgabe ist es, aus diesen Quellen **zwei bis drei begründete Vorschläge** zusammenzustellen und dem Auftraggeber zur Entscheidung vorzulegen. **Du entscheidest nicht allein, und du baust nicht ungefragt durch.**

Frühere Designentwürfe liegen unter `design/_verworfen/` und sind **nicht** zu verwenden — auch nicht als Anregung für Farbe oder Schrift.

---

## 1. Reihenfolge deiner Arbeit

1. **Rahmen lesen** (Abschnitt 2) — was unverhandelbar ist.
2. **Recherchieren** (Abschnitt 3) — Bibliotheken, Schriften, Icons, echte Referenzseiten.
3. **Prüfen** (Abschnitt 4) — Lizenz, Pflege, Größe, Barrierefreiheit, Template-Erkennbarkeit.
4. **Zusammenstellen** (Abschnitt 5) — 2–3 Vorschläge, je als **echte, klickbare Seite** mit echten Inhalten.
5. **Vorlegen** (Abschnitt 7) — mit Quellen, Lizenzen und Größen. Der Mensch entscheidet.
6. Erst **nach** der Entscheidung: Rest der Website nach dem gewählten Vorschlag bauen.

**Keine Vorschau ohne echte Inhalte.** Nutze die fertigen Texte aus `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` — kein Lorem Ipsum, keine Platzhalterpreise.

---

## 2. Unverhandelbarer Rahmen

Das sind Geschäfts- und Rechtsanforderungen, keine Geschmacksfragen. Sie gelten für **jeden** Vorschlag.

### 2.1 Lizenz — der kritischste Punkt

SARTU **verkauft Websites an Kunden weiter**. Was du einsetzt, muss das erlauben.

- Jede Schrift, Komponente, Bibliothek und jedes Icon-Set braucht eine Lizenz, die **kommerzielle Nutzung und Weitergabe im Kundenprojekt** erlaubt.
- **Prüfe die Lizenzdatei im Repository selbst.** Verlasse dich nicht auf die Beschreibung auf einer Website.
- Ausgeschlossen: alles, was Weiterverkauf oder Weitergabe an Dritte untersagt; „kostenlos für persönliche Nutzung"; Templates mit „single site license".
- Notiere zu jedem eingesetzten Teil: **Name, Version, Lizenztyp, Fundstelle**. Diese Liste gehört zur Abgabe.

### 2.2 Technik und Leistung

- Statisch ausgeliefert (Astro oder gleichwertig), FTP-/CDN-fähig.
- **Kein externes CDN** für Schriften, CSS, JS oder Icons — alles selbst gehostet (Datenschutz, Tempo, Ausfallsicherheit).
- **JS-Budget: ≤ 75 KB gzip Startseite, ≤ 40 KB Unterseiten.** Gemessen, nicht geschätzt.
- Ziele: LCP < 2,5 s · INP < 200 ms · CLS < 0,1, gemessen mobil.
- Die Seite muss **ohne JavaScript** grundlegend nutzbar bleiben.

### 2.3 Barrierefreiheit

- Kontrast Fließtext ≥ 4,5:1, große Schrift ≥ 3:1.
- Sichtbarer Fokus auf allem Bedienbaren — wird nie aus optischen Gründen entfernt.
- Vollständige Tastaturbedienung, sinnvolle Reihenfolge, Skip-Link.
- `prefers-reduced-motion: reduce` schaltet alle nicht-essenziellen Bewegungen ab.
- Zustände nie allein über Farbe — immer zusätzlich Text oder Form.

### 2.4 Positionierung — was die Marke beschädigen würde

SARTU verkauft „individuell programmiert, kein Baukasten". Die eigene Website darf deshalb **nicht erkennbar aus einem Template** stammen.

Nicht verwenden:
- sichtbare Template-Handschrift (bekannte Hero-Layouts, unveränderte Beispielsektionen)
- Farbverläufe, Leuchtflächen, schwebende Dashboard-Karten mit Schlagschatten
- runde Karten mit Akzentbalken, Karten in Karten
- Emoji als Sektionsmarker, durchgehend zentrierter Fließtext
- Stockfotos mit Handschlag, Laptop oder Callcenter
- generische WebGL-/Partikel-Hintergründe
- erfundene Logos, Bewertungen, Referenzen oder Kundenzahlen

### 2.5 Inhaltliche Wahrheit

Alle Texte, Preise und Portal-Ansichten stammen aus dem Lastenheft. Portal-Screenshots müssen aus **echter Oberfläche** kommen und als „Musteransicht" gekennzeichnet sein, solange kein freigegebenes Kundenprojekt existiert.

---

## 3. Wo du suchst

Prüfe jede Quelle selbst auf Aktualität — Projekte veralten, Lizenzen ändern sich.

### 3.1 Komponenten und Grundgerüst

| Art | Kandidaten zum Prüfen |
|---|---|
| **Unstyled / Headless** (Barrierefreiheit geschenkt, kein Template-Look) | Radix Primitives · Headless UI · Ark UI · React Aria |
| **Gestylte Systeme** (nur als Basis, muss umgestaltet werden) | shadcn/ui · Preline · Flowbite · daisyUI · HyperUI · Park UI |
| **Astro-Ökosystem** | offizielles Astro-Themes-Verzeichnis · Astro-Integrationen |
| **CSS-Grundlagen** | Open Props · moderne CSS-Resets |

**Bevorzuge unstyled/headless.** Sie liefern Tastaturbedienung und ARIA korrekt, ohne fremde Optik mitzubringen — genau die Kombination, die SARTU braucht.

### 3.2 Bewegung

| Werkzeug | Wofür es sich lohnt |
|---|---|
| **CSS-Transitions/Animations** | erste Wahl, 0 KB — deckt die meisten Fälle ab |
| **View Transitions API** (nativ) | Seitenwechsel, in Astro eingebaut |
| **GSAP + ScrollTrigger** | verkettete, an den Scroll gebundene Sequenzen. Lizenz und Größe selbst prüfen |
| **Motion** | Zustände und Microinteractions, vor allem im Portal |
| **Lenis** | sanftes Scrollen, nur Marketingseite, nie im Portal |
| **auto-animate** | einfache Listenwechsel |
| **Rive** | nur wenn eine Animation eine echte Idee trägt |

Nicht einsetzen: Vanta.js, Three.js als Dekoration, Barba.js.

### 3.3 Schriften

Nur selbst gehostet, als WOFF2, mit `font-display: swap`.

Suchorte: Fontsource (zum Selbsthosten paketiert) · Google Fonts · Fontshare · Velvetyne · Open Foundry · Use & Modify · Collletttivo.

**Auswahlhinweise statt Vorgabe:**
- Zwei bis drei Rollen genügen: Überschrift, Fließtext, optional Zahlen/Labels.
- Zahlen brauchen `tabular-nums` — Preise stehen in Tabellen untereinander.
- Prüfe deutsche Umlaute und das €-Zeichen in allen Schnitten.
- **Meide die üblichen Standardwahlen** (etwa Inter oder Space Grotesk als Hauptschrift). Sie sind nicht schlecht, aber sie machen die Seite austauschbar — genau das Gegenteil des Verkaufsarguments.

### 3.4 Icons

Lucide · Phosphor · Tabler · Remix Icon · Heroicons. **Ein** Set für die gesamte Seite, selbst gehostet, als Inline-SVG. Nie Sets mischen.

### 3.5 Echte Referenzen — wichtiger als Galerien

Schau dir **reale Seiten** an, nicht nur Design-Galerien:

- **Marktrealität:** deutsche Agentur-, Handwerks-, Kanzlei- und Praxisseiten in der Region Dresden. Was wirkt dort seriös, was billig? Wogegen muss SARTU sich absetzen?
- **Qualitätsniveau:** Awwwards, Godly, Land-book, SiteInspire, Minimal Gallery, Httpster.
- **Oberflächenmuster:** Mobbin, UI Sources.

Notiere zu jeder Referenz **einen konkreten Satz**, was du übernehmen willst — „großzügiger Abstand zwischen Sektionen", „Preistabelle statt Kartenwand". Keine Gesamtkopie einer Seite.

---

## 4. Prüfliste vor dem Einsatz

Für **jedes** Teil, das du übernehmen willst:

- [ ] **Lizenz** erlaubt kommerzielle Nutzung **und** Weitergabe im Kundenprojekt (Lizenzdatei gelesen)
- [ ] **Gepflegt** — letzte Änderung nachvollziehbar aktuell, keine offenen Sicherheitsprobleme
- [ ] **Größe** gemessen und im Budget
- [ ] **Barrierefrei** — Tastatur, Fokus, ARIA belegt, nicht nur behauptet
- [ ] **Passt technisch** — funktioniert statisch, erzwingt kein schweres Framework
- [ ] **Umgestaltbar** — Farben, Schriften und Abstände über Variablen änderbar
- [ ] **Nicht wiedererkennbar** — man sieht dem Ergebnis die Herkunft nicht an
- [ ] **Ohne externe Verbindungen** zur Laufzeit

Fällt ein Punkt durch: nicht einsetzen. Kein „passt schon".

---

## 5. Wie du den Rest auffüllst

Der häufigste Fehler ist, korrekte Komponenten zusammenzusetzen und trotzdem eine leere, flache Seite zu bekommen. Wirkung entsteht nicht aus mehr Bausteinen, sondern aus:

**Reihenfolge deiner Mittel — von oben nach unten anwenden:**

1. **Struktur und Rhythmus.** Sektionen wechseln bewusst in Typ und Hintergrund (hell / abgesetzt / dunkel). Nicht acht gleich gebaute Blöcke untereinander.
2. **Typografischer Kontrast.** Ein deutlicher Sprung zwischen Überschrift und Fließtext trägt eine Seite mehr als jede Grafik. Große Überschriften brauchen ruhige Umgebung.
3. **Weißraum mit Disziplin.** Lieber wenige, große Abstände nach festem Raster als viele kleine.
4. **Echter Inhalt statt Dekoration.** Eine ehrliche Preistabelle wirkt hochwertiger als jede Illustration. Dichte an Information ist erlaubt, Dichte an Zierrat nicht.
5. **Flächige Hintergrundbehandlung**, wenn eine Sektion leer wirkt — sehr zurückhaltend, immer hinter dem Inhalt, nie über Text.
6. **Bewegung als Akzent.** Höchstens zwei bewusste Momente pro Seite, alles andere sind kurze Zustandswechsel.
7. **Echte Bilder** — Portal-Oberfläche, echte Betriebsfotos. Keine Illustration als Lückenfüller.

**Wenn eine Stelle leer wirkt, ist die Antwort fast nie „noch ein Element", sondern:** größerer Typ-Kontrast, mehr Weißraum oder ein Inhalt, der wirklich hingehört.

**Entscheidungsreihenfolge beim Bauen einer Komponente:**
natives HTML/CSS → unstyled Primitive → gestylte Komponente vollständig umgestaltet → Eigenbau.
**Nie:** ein komplettes Template-Layout übernehmen.

---

## 6. Wenn nichts passt

- **Zwei Bibliotheken mischen** ist erlaubt, solange nur **ein** Icon-Set und **ein** Grundraster gelten.
- **Selbst bauen** ist der Normalfall für alles Sartu-Spezifische (Preistabelle, Portal-Vorschau, Bedarfsscheck).
- **Nicht** irgendetwas nehmen, das die Prüfliste reißt, nur um schneller fertig zu sein.
- Kommst du nicht weiter: dokumentiere das Problem und lege es vor, statt eine schlechte Lösung einzubauen.

---

## 7. Was du vorlegst

**Zwei bis drei Vorschläge**, jeder als **klickbare Seite** mit den echten Startseiten-Inhalten aus dem Lastenheft — nicht als Beschreibung, nicht als Farbtafel.

Je Vorschlag:

1. **Ein Satz zur Haltung** — wie wirkt dieser Vorschlag und auf wen zielt er?
2. **Herkunftsliste:** jedes eingesetzte Teil mit Name, Version, **Lizenz** und Fundstelle.
3. **Messwerte:** JS in KB gzip, CSS in KB, LCP/CLS mobil.
4. **Prüfliste aus Abschnitt 4**, abgehakt.
5. **Was du bewusst weggelassen hast** und warum.
6. **Was noch fehlt**, um daraus die fertige Seite zu machen.

Dazu **einmal** die Startseite in beiden Zuständen: normal und mit `prefers-reduced-motion`.

**Der Mensch entscheidet.** Baue erst nach der Entscheidung weiter — und stelle dann alle Farben, Schriften und Abstände als **zentrale Variablen** bereit, damit ein Wechsel später ein Variablentausch bleibt und kein Umbau.

---

## 8. Logo

**Nicht vorgeben, sondern recherchieren und vorlegen.**

**Fest steht nur der Name: `SARTU`** — ohne Zusatz wie „digital". Wird ein Beschreibungszusatz gebraucht, gehört er als Beisatz ins Logo-Lockup (etwa `SARTU · Firmenwebsites`), nicht in den Namen.

Anforderungen:
- funktioniert einfarbig und als Favicon ab 16 px
- funktioniert auf hellem und dunklem Grund
- als SVG umsetzbar, keine Effekte, keine Verläufe
- keine Ähnlichkeit zu bestehenden Marken — **prüfe das aktiv**, auch gegen deutsche Marken- und Handelsregister-Recherche
- nutzt dieselbe oder eine bewusst passende Schrift wie die Website

Lege **drei bis fünf** Richtungen vor, jeweils als Wortmarke **und** als Favicon-Ausschnitt, mit einem Satz Begründung. Bis zur Entscheidung wird die reine Wortmarke in der gewählten Website-Schrift verwendet — das ist kein Provisorium, sondern eine gültige Lösung.

---

## 9. Grenze deiner Entscheidungsfreiheit

**Du entscheidest selbst:** technische Umsetzung, Komponentenwahl innerhalb der Prüfliste, Struktur des Codes, wie du recherchierst.

**Du legst vor und entscheidest nicht:** Farbwelt, Schriftwahl, Logo, Gesamtwirkung, alles mit Außenwirkung auf die Marke.

**Du fragst nach, statt zu raten**, wenn eine Anforderung mit dem Rahmen in Abschnitt 2 kollidiert.
