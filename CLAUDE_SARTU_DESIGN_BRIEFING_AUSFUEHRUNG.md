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

> **Merksatz für dieses Dokument:** Nicht „mach mir etwas Schönes", sondern **„such dir wenige sehr
> gute, sauber lizenzierte Quellen, übernimm ihren Aufbau und mach ihn zu unserem"**. Das Ergebnis
> ist eine kuratierte Code-Collage, kein KI-Neuentwurf.

---

## 2. Unverhandelbarer Rahmen

Das sind Geschäfts- und Rechtsanforderungen, keine Geschmacksfragen. Sie gelten für **jeden** Vorschlag.

### 2.1 Lizenz — der kritischste Punkt, und er hat **zwei Stufen**

Das ist der Abschnitt, der über Rechtssicherheit entscheidet. Er wird oft zu einfach gedacht.

**Der Unterschied, auf den es ankommt:**

| | **SARTUs eigene Website und Kundenbereich** | **SARTU-Starter für Kundenseiten** |
|---|---|---|
| Was es ist | ein Projekt für SARTU selbst | ein wiederverwendbarer Bausatz |
| Lizenzanforderung | kommerzielle Nutzung erlaubt | kommerzielle Nutzung **und Weitergabe im Bausatz** erlaubt |
| Zulässig | MIT, Apache-2.0, CC0 — **und** gekaufte Lizenzen wie Tailwind Plus | **nur** MIT, Apache-2.0, CC0 und Vergleichbares |
| Verboten | „nur persönliche Nutzung", „single site license" | zusätzlich: **jede gekaufte Komponentenlizenz** |

**Warum die zweite Spalte enger ist — ein konkretes Beispiel.** Die Tailwind-Plus-Lizenz erlaubt
ausdrücklich Kundenprojekte („create unlimited End Products for unlimited Clients"), verbietet aber
genauso ausdrücklich:

> *„Creating a theme, template, or project starter kit using the components, templates, or
> libraries and making it available either for sale or for free."*

Ein SARTU-Starter, aus dem Kundenseiten entstehen, **ist** ein Starter-Kit. Für SARTUs eigene
Website wären diese Komponenten zulässig, im Starter nicht. Dasselbe gilt für Flowbite Pro, Preline
Pro und jede andere gekaufte Sammlung.
*(Lizenztext geprüft am 25.07.2026: https://tailwindcss.com/plus/license)*

**Regel, die daraus folgt:** Wenn du nicht **sicher** weißt, in welche Spalte ein Teil gehört,
nimm die rechte. Was im Starter zulässig ist, ist überall zulässig. Umgekehrt gilt das nicht.

### Prüfung je Teil — ohne Ausnahme

- **Lies die Lizenzdatei im Repository selbst.** Nicht die Beschreibung auf der Website, nicht die
  Zusammenfassung auf einer Vergleichsseite
- Bei zwei Stufen (kostenlos + kostenpflichtig) prüfen, **welche** Komponenten in welcher Stufe sind.
  Flowbite und Preline haben freie und gekaufte Teile, die auf derselben Seite nebeneinanderstehen
- Ausgeschlossen: „kostenlos für persönliche Nutzung", „single site license", alles ohne
  auffindbare Lizenz

### Herkunftsliste — Pflichtabgabe

Für **jedes** übernommene Teil, auf Komponentenebene und nicht pauschal:

| Spalte | Inhalt |
|---|---|
| Komponente | wofür sie bei SARTU eingesetzt wird |
| Quelle | Projekt, genaue Fundstelle, Datum des Abrufs |
| Version oder Commit | damit später nachvollziehbar ist, welcher Stand |
| Lizenz | Typ und wo die Lizenzdatei liegt |
| Stufe | „nur SARTU-eigen" oder „auch im Kundenstarter zulässig" |
| Was geändert wurde | in einem Satz |

Diese Liste ist Teil der Abgabe. Ein Teil ohne Zeile in der Liste gilt als nicht eingesetzt und wird
entfernt.

### 2.2 Technik und Leistung

- **Serverseitig gerendert im bestehenden PHP-Projekt.** Öffentliche Seiten sind cachebar und dürfen als statische Antwort ausgeliefert werden — es gibt aber **keinen Astro-, Node- oder Frontend-Build als Zielsystem** (Portal-Lastenheft §1).
- **Kein Build-Schritt fürs Frontend.** CSS und JS werden so ausgeliefert, wie sie im Repository liegen. Was einen Übersetzungsvorgang braucht, kommt nicht in Frage.
- **Kein externes CDN** für Schriften, CSS, JS oder Icons — alles selbst gehostet (Datenschutz, Tempo, Ausfallsicherheit).
- **JS-Budget: ≤ 75 KB gzip Startseite, ≤ 40 KB Unterseiten.** Gemessen, nicht geschätzt.
- Ziele **im Labor, vor Livegang**: LCP < 2,5 s · TBT < 200 ms · CLS < 0,1, gemessen mobil. **Echtes INP** gibt es erst aus Felddaten nach dem Livegang — in Phase 1 also nicht behaupten.
- Die Seite muss **ohne JavaScript** grundlegend nutzbar bleiben.

### 2.3 Barrierefreiheit

- Kontrast Fließtext ≥ 4,5:1, große Schrift ≥ 3:1.
- Sichtbarer Fokus auf allem Bedienbaren — wird nie aus optischen Gründen entfernt.
- Vollständige Tastaturbedienung, sinnvolle Reihenfolge, Skip-Link.
- `prefers-reduced-motion: reduce` schaltet alle nicht-essenziellen Bewegungen ab.
- Zustände nie allein über Farbe — immer zusätzlich Text oder Form.

### 2.4 Positionierung — was die Marke beschädigen würde

SARTU verkauft „individuell programmiert, kein Baukasten". Die eigene Website darf deshalb **nicht erkennbar aus einem Template** stammen — und aus demselben Grund **kein fremdes Komponentensystem als Laufzeitabhängigkeit** mitbringen. Wer „ohne Baukasten" verkauft und dabei erkennbar einen zusammensteckt, verliert das Argument.

> **Das ist kein Widerspruch zum Übernehmen von Code (§3.1).** Der Unterschied liegt in der Ebene
> und im Ergebnis: Bausteine dürfen nah übernommen werden, weil sie niemand wiedererkennt. Was
> erkannt wird — Hero-Layouts, auffällige Preisblöcke, ganze Seitengerüste — wird neu zusammengesetzt.
> **Der Prüfstein ist nicht, wie der Code entstanden ist, sondern ob ein Fachkundiger die Quelle
> ansieht.** Kann er es, ist es zu nah.

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

### 3.1 Komponenten — **übernehmen und portieren, nicht neu erfinden**

> **Das ist der wichtigste Abschnitt dieses Dokuments. Er wurde bewusst verschärft.**
>
> Eine frühere Fassung sagte: fremde Systeme nur als „Inspiration" lesen, dann selbst bauen. Das
> war der falsche Auftrag. **Wer eine KI bittet, „etwas Schönes selbst zu bauen", bekommt den
> Durchschnitt** — mittige Abstände, generische Karten, austauschbare Hierarchie. Genau der Look,
> den SARTU nicht haben darf.
>
> **Der neue Auftrag lautet: kuratierte Code-Collage.** Suche wenige, sehr gute, sauber lizenzierte
> Quellen. Übernimm ihren **konkreten** Aufbau — Markup, CSS-Ansatz, Zustände, Interaktionslogik.
> Passe an, was zur Marke gehört. Erfinde nicht neu, was jemand mit mehr Geschmack schon gelöst hat.

#### Warum das besser ist

In einer guten Komponente steckt Arbeit, die man ihr nicht ansieht: wie groß der Abstand zwischen
Beschriftung und Feld ist, wie ein Fehlerzustand aussieht, wie der Fokusring bei dunklem Hintergrund
funktioniert, wann eine Tabelle auf dem Handy umbricht. Diese Entscheidungen sind **das eigentliche
Design**. Eine KI, die frei baut, trifft sie im Mittel — und Mittelmaß ist erkennbar.

#### So wählst du aus

**1 bis 3 Quellen, nicht zehn.** Mehr Quellen heißt nicht mehr Qualität, sondern weniger Einheit.
Wähle Quellen, die zueinander passen, und begründe die Wahl in einem Absatz.

| Art | Was du davon übernimmst | Beispiele zum Prüfen |
|---|---|---|
| **Reines HTML + CSS** — der Idealfall | alles: Markup, CSS, Zustände | HyperUI · daisyUI · Open Props · Pico · reine CSS-Sammlungen |
| **Kopierbare Komponenten** (Code zum Einfügen, nicht als Abhängigkeit) | Markup und CSS, Verhalten nachgebaut | shadcn/ui (React → portieren) · Flowbite (freie Teile) · Preline (freie Teile) |
| **Verhaltensvorlagen** (React, nicht direkt nutzbar) | die **Interaktionslogik**: Rollen, Tastaturwege, Zustände, Fokusführung | Radix Primitives · Headless UI · Ark UI · React Aria |
| **Echte Seiten im Netz** | einzelne gelöste Details, nie ganze Layouts | siehe §3.5 |

**Bei jeder Quelle zuerst die Lizenzstufe nach §2.1 bestimmen** — bevor du auch nur eine Zeile
übernimmst. Ein Teil, das nur in Spalte 1 zulässig ist, darf nie in den Kundenstarter wandern.

#### Wie nah du übernehmen darfst — nach Ebene gestaffelt

Das ist die Grenze zwischen „hochwertig kuratiert" und „erkennbar aus einem Template" (§2.4):

| Ebene | Wie nah | Warum |
|---|---|---|
| **Bausteine** — Schaltfläche, Eingabefeld, Tabelle, Akkordeon, Dialog, Karte, Hinweis, Fußzeilenzeile | **sehr nah, praktisch übernehmen** | Niemand erkennt ein gut gebautes Eingabefeld wieder. Hier steckt Handwerk, kein Wiedererkennungswert |
| **Sektionen** — Hero, Preisblock, Merkmalsraster, Zitatbereich | **Mechanik übernehmen, Anordnung neu zusammensetzen** | Ein bekanntes Hero-Layout **wird** erkannt. Übernimm das Rasterverhalten, die Umbruchpunkte, die Zustände — aber nicht die Komposition |
| **Ganze Seiten** | **nie** | Das ist der Template-Look, den SARTU verkaufsseitig ausschließt |

**Faustregel:** Je unsichtbarer ein Teil, desto näher darfst du. Je stärker ein Teil den ersten
Eindruck prägt, desto mehr muss es deins werden.

#### Der Portierungsschritt — und warum er das Problem nebenbei löst

Fast alle guten Quellen sind **Tailwind-basiert**. Dieses Projekt hat **keinen Build-Schritt**
(§2.2). Daraus folgt zwingend:

**Utility-Klassen werden in eigenes CSS übersetzt**, mit den zentralen Variablen des Projekts.
Nicht die gesamte Tailwind-Datei ausliefern, nicht heimlich einen Übersetzungsschritt einführen,
nicht Klassennamen wie `px-4 py-2 rounded-lg` ins Markup schreiben.

> **Das ist Arbeit — aber es ist genau die richtige Arbeit.** Beim Übersetzen von Utility-Klassen in
> eigenes CSS mit eigener Abstandsskala kann eine erkennbare Fremdsektion gar nicht unverändert
> durchrutschen. Der Portierungsschritt **ist** der Entfremdungsschritt. Zwei Probleme, eine Lösung.

**Bei React-Komponenten** (Radix, Headless UI, React Aria): Das Markup und die ARIA-Attribute
übernimmst du, das Verhalten schreibst du als kleines eigenes JavaScript-Modul nach. Lies dazu deren
Quelltext — dort steht, welche Tasten welchen Zustand ändern und wohin der Fokus wandert. Das ist
kein Neuerfinden, das ist Übersetzen.

#### Was angepasst wird — und was nicht

| Anpassen | Unverändert lassen |
|---|---|
| Farben, Schriften, Schriftgrößen | Zugänglichkeitsmerkmale: Rollen, `aria-*`, Fokusreihenfolge |
| Abstände auf die eigene Skala | Tastaturverhalten |
| Radien, Rahmen, Schattenverzicht | Umbruchlogik, die nachweislich funktioniert |
| Texte — immer die echten aus dem Lastenheft | Zustandsabdeckung: Leerzustand, Fehler, Ladezustand |
| Anordnung auf Sektionsebene | |

**Verändere nie etwas an der Zugänglichkeit, um es hübscher zu machen.** Wenn ein Fokusring stört,
gestalte ihn um — entferne ihn nicht.

### 3.2 Bewegung

| Werkzeug | Wofür es sich lohnt |
|---|---|
| **CSS-Transitions/Animations** | erste Wahl, 0 KB — deckt die meisten Fälle ab |
| **View Transitions API** (nativ) | Seitenwechsel — **nur die native Browser-API**, ohne Framework-Abhängigkeit. Progressiv: Wo sie fehlt, wechselt die Seite normal |
| **GSAP + ScrollTrigger** | verkettete, an den Scroll gebundene Sequenzen. Lizenz und Größe selbst prüfen |
| **Motion** | Zustände und Microinteractions, vor allem im Kundenbereich. Nur die Variante ohne Build-Schritt |
| **Lenis** | sanftes Scrollen, nur Marketingseite, nie im Portal |
| **auto-animate** | einfache Listenwechsel |
| **Rive** | nur wenn eine Animation eine echte Idee trägt |

Nicht einsetzen: Vanta.js, Three.js als Dekoration, Barba.js.

> **Einbinderegel für jedes Fremd-JavaScript — ohne Ausnahme.**
> Erlaubt ist es nur, wenn **alle** vier Punkte zutreffen:
>
> 1. Es liegt als **fertige Browser-Datei im Repository** (`/public/assets/js/…`), lizenzkonform selbst gehostet
> 2. Es funktioniert **ohne npm, ohne `node_modules`, ohne Bundler und ohne Übersetzungsschritt**
> 3. Es lädt **nichts** von einer fremden Domain — keine CDN-Adresse, keine Schriften, keine Telemetrie
> 4. **Lizenz und Größe in KB gzip sind dokumentiert** und im JS-Budget verrechnet
>
> **Gibt es keine saubere Variante ohne Übersetzungsschritt: nicht verwenden.** Kein „wir bauen es
> einmal und legen das Ergebnis ab" — das erzeugt eine Datei, die niemand mehr aktualisieren kann.
> Ein Effekt ist keinen Werkzeugkasten wert; CSS reicht für fast alles (§3.2, erste Zeile).

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

- **Marktrealität:** deutsche Agentur-, Handwerks-, Kanzlei- und Praxisseiten im ländlichen und kleinstädtischen Raum. Was wirkt dort seriös, was billig? Wogegen muss SARTU sich absetzen?
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
- [ ] **Lizenzstufe bestimmt** (§2.1): nur SARTU-eigen, oder auch im Kundenstarter zulässig? Im Zweifel die engere Stufe
- [ ] **Passt technisch** — läuft **ohne Build-Schritt** und **ohne Framework-Laufzeit**. Erzwingt es React, Vue, einen Bundler oder einen Paketmanager zur Laufzeit: nicht direkt einbinden, sondern nach §3.1 portieren
- [ ] **Utility-Klassen sind übersetzt** — kein `px-4 py-2` im ausgelieferten Markup, sondern eigenes CSS mit den zentralen Variablen
- [ ] **Ebene geprüft** (§3.1): Baustein → darf nah sein · Sektion → Mechanik ja, Komposition neu · ganze Seite → nie
- [ ] **In der Herkunftsliste eingetragen** mit Quelle, Version, Lizenz, Stufe und Änderung
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

**Entscheidungsreihenfolge beim Bauen einer Komponente — bewusst mit „übernehmen" zuerst:**

1. **Gibt es das nativ?** `<details>`, `<dialog>`, passende `<input>`-Typen. Dann das — es ist
   zugänglich, klein und wartungsfrei
2. **Gibt es eine gute, sauber lizenzierte Vorlage?** Dann **übernehmen und portieren** nach §3.1 —
   Markup und CSS-Ansatz mit, Utility-Klassen in eigenes CSS übersetzt, Verhalten aus der
   Verhaltensvorlage nachgebaut
3. **Selbst bauen** — erst wenn 1 und 2 nichts hergeben, und immer für alles SARTU-Eigene
   (Preistabelle, Bedarfsscheck, Vorschau- und Freigabeansichten)

**Der Unterschied zu früher liegt in Schritt 2.** Er stand vorher nicht drin, und das war der Fehler:
Wer direkt zu „selbst bauen" springt, baut Durchschnitt.

**Nie:** ein komplettes Seitengerüst übernehmen. **Nie:** ein Komponentensystem als
Laufzeitabhängigkeit einbinden. Jede Komponente entsteht **einmal** und wird von öffentlichen Seiten
und Kundenbereich gemeinsam genutzt — kein zweites Set für den eingeloggten Bereich.

---

## 6. Wenn nichts passt

- **Mehrere Quellen als Referenz** zu lesen ist erwünscht — je mehr gute Vorbilder, desto besser.
- **Zwei Laufzeit-Bibliotheken** sind höchstens für kleine, dokumentierte JavaScript-Effekte zulässig, jede einzeln nach der Einbinderegel in §3.2 geprüft und im JS-Budget verrechnet. **Komponentenbibliotheken kommen nie als Laufzeit dazu** (§3.1).
- Unabhängig davon gilt: nur **ein** Icon-Set und **ein** Grundraster für die gesamte Seite.
- **Selbst bauen** ist der Normalfall für alles Sartu-Spezifische (Preistabelle, Portal-Vorschau, Bedarfsscheck).
- **Nicht** irgendetwas nehmen, das die Prüfliste reißt, nur um schneller fertig zu sein.
- Kommst du nicht weiter: dokumentiere das Problem und lege es vor, statt eine schlechte Lösung einzubauen.

---

## 7. Was du vorlegst

**Zwei bis drei Vorschläge**, jeder als **klickbare Seite** mit den echten Startseiten-Inhalten aus dem Lastenheft — nicht als Beschreibung, nicht als Farbtafel.

**Gebaut werden sie als echte Seiten im Projekt** — PHP-View, Layout, Partials, CSS mit zentralen Variablen, so wie der spätere Stand aussehen soll. **Keine** losen HTML-Dateien zum Wegwerfen: Der gewählte Vorschlag soll weiterverwendet und nicht nachgebaut werden. Die beiden anderen Varianten werden nach der Entscheidung gelöscht.

Je Vorschlag:

1. **Ein Satz zur Haltung** — wie wirkt dieser Vorschlag und auf wen zielt er?
1a. **Welche 1–3 Quellen** du gewählt hast und **warum diese** — mit Lizenzstufe je Quelle (§2.1)
2. **Herkunftsliste:** jedes eingesetzte Teil mit Name, Version, **Lizenz** und Fundstelle.
3. **Messwerte:** JS in KB gzip, CSS in KB, LCP und CLS mobil im Labor (TBT statt INP, §2.2).
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
