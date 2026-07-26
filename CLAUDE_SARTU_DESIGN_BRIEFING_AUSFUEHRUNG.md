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

### 2.1 Lizenz — **nur kostenlose, frei weitergebbare Quellen**

**Entscheidung:** Es werden ausschließlich **kostenlose** Komponenten und Bibliotheken eingesetzt.
Keine gekauften Sammlungen, kein „Pro"-Tarif, keine Einzelplatzlizenz.

Damit gilt **eine** Anforderung statt zweier Stufen — und sie ist die strengere:

> **Jedes Teil muss kommerzielle Nutzung, Veränderung und Weitergabe erlauben.**
> Zulässig sind vor allem **MIT**, **Apache-2.0**, **ISC**, **BSD**, **CC0**, bei Schriften **SIL OFL**.

**Warum die Weitergabe mitgeprüft werden muss, auch wenn es „nur" um SARTUs eigene Seite geht:**
Aus denselben Bausteinen entsteht später der **SARTU-Starter**, aus dem Kundenseiten erzeugt werden.
Ein Starter ist ein weitergegebener Bausatz. Und der Kunde bekommt seine Seite bei Vertragsende als
**Export** — er muss sie weiterbetreiben dürfen. Ein Baustein, der das nicht hergibt, ist eine
Zeitbombe im Kundenprojekt.

**Kostenlos ist nicht dasselbe wie frei.** Das ist die häufigste Verwechslung:

| Sieht kostenlos aus | Ist aber ein Problem |
|---|---|
| „Free tier" einer kommerziellen Sammlung | freie und kostenpflichtige Teile stehen auf derselben Seite nebeneinander — Flowbite und Preline zum Beispiel. Prüfen, **welche** Komponente in welcher Stufe ist |
| „Free for personal and commercial use" | sagt nichts über **Weitergabe**. Für den Starter reicht das nicht |
| „Kostenlos für persönliche Nutzung" | scheidet vollständig aus |
| Schriften mit `-NC` (nicht kommerziell) | scheiden aus |
| Etwas ohne auffindbare Lizenzdatei | scheidet aus. Keine Lizenz heißt: alle Rechte vorbehalten |

**Zur Klarstellung bei Tailwind:** Das CSS-Framework **Tailwind CSS selbst ist MIT** und
unproblematisch. Verboten ist nur **Tailwind Plus** (früher Tailwind UI), die gekaufte
Komponentensammlung — deren Lizenz untersagt ausdrücklich, daraus einen Starter zu bauen.
Tailwind-basierte **freie** Sammlungen sind davon nicht betroffen.

### Brauchbare kostenlose Quellen — Startpunkt, keine Vorgabe

Alle Angaben **selbst prüfen**: Lizenzdatei im Repository lesen, nicht die Beschreibung auf einer
Website. Lizenzen ändern sich, Projekte werden verkauft.

| Was | Kandidaten | Anmerkung |
|---|---|---|
| **Reines HTML + CSS zum Übernehmen** | **HyperUI** (MIT, geprüft 25.07.2026) · daisyUI · Bulma · Pico · Open Props | der Idealfall — direkt portierbar |
| **Kopierbare Komponenten** | shadcn/ui (MIT, React → portieren) · Flowbite **freier Kern** · Preline **freier Teil** | bei den letzten beiden je Komponente die Stufe prüfen |
| **Verhaltensvorlagen** | Radix Primitives · Headless UI · Ark UI · React Aria | React — es wird das **Verhalten** übernommen, nicht der Code |
| **Bewährtes, buildfrei ausliefbar** | Bootstrap (MIT, fertige CSS-Datei) | unterschätzt für ein Projekt ohne Build-Schritt |
| **Icons** | Lucide · Phosphor · Tabler · Heroicons · Remix Icon | **ein** Set für alles, selbst gehostet als Inline-SVG |
| **Schriften** | Fontsource · Google Fonts · Velvetyne · Collletttivo · Use & Modify | auf **OFL** achten; bei Fontshare die eigene Lizenz genau lesen |

**Es gibt reichlich Auswahl.** Der Engpass ist nicht die Verfügbarkeit, sondern die Auswahl: lieber
**eine sehr gute** Quelle gründlich nutzen als fünf mittelmäßige mischen (§3.1).

### Herkunftsliste — Pflichtabgabe

Für **jedes** übernommene Teil, auf Komponentenebene und nicht pauschal:

| Spalte | Inhalt |
|---|---|
| Komponente | wofür sie bei SARTU eingesetzt wird |
| Quelle | Projekt, genaue Fundstelle, Datum des Abrufs |
| Version oder Commit | damit später nachvollziehbar ist, welcher Stand |
| Lizenz | Typ **und** wo die Lizenzdatei liegt |
| Weitergabe erlaubt? | ja/nein — bei „nein" darf das Teil **nicht** eingesetzt werden |
| Was geändert wurde | in einem Satz |

Die Lizenzhinweise der übernommenen Projekte werden mit ausgeliefert — meist eine Sammeldatei
`public/assets/LIZENZEN.txt` oder Kommentare im CSS. Das ist bei MIT und Apache **Pflicht**, nicht
Höflichkeit.

Ein Teil ohne Zeile in der Liste gilt als nicht eingesetzt und wird entfernt.

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

| Art | Was du davon übernimmst | Kandidatenliste |
|---|---|---|
| **Reines HTML + CSS** — der Idealfall | alles: Markup, CSS, Zustände | §2.1, Zeile 1 |
| **Kopierbare Komponenten** | Markup und CSS, Verhalten nachgebaut | §2.1, Zeile 2 |
| **Verhaltensvorlagen** (React) | die **Interaktionslogik**: Rollen, Tastaturwege, Zustände, Fokusführung | §2.1, Zeile 3 |
| **Echte Seiten im Netz** | einzelne gelöste Details, nie ganze Layouts | siehe §3.5 |

**Bei jeder Quelle zuerst die Lizenz nach §2.1 prüfen** — bevor du auch nur eine Zeile übernimmst.
**Nur kostenlose Quellen mit erlaubter Weitergabe** (MIT, Apache-2.0, ISC, BSD, CC0, bei Schriften
OFL). Erlaubt eine Lizenz die Weitergabe nicht, scheidet das Teil aus — auch für SARTUs eigene Seite,
weil dieselben Bausteine später in den Kundenstarter wandern.

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
| **Lenis** | sanftes Scrollen — **erst nach der CSS-Stufe zu entscheiden**, Bedingungen unten. Nur Marketingseite, nie im Portal |
| **auto-animate** | einfache Listenwechsel |
| **Rive** | nur wenn eine Animation eine echte Idee trägt |

Nicht einsetzen: Vanta.js, Three.js als Dekoration, Barba.js.

#### Scrollgebundene Bewegung — geht seit 2026 ohne JavaScript

`animation-timeline: scroll()` und `view()` sind nativ verfügbar (Chrome/Edge ab 115, Firefox ab 132,
Safari ab 18 — rund 84 % weltweit, Stand Mitte 2026). Damit lassen sich Einblendungen beim Scrollen,
Fortschrittsanzeigen und Parallaxe **ohne eine Zeile JavaScript** bauen: kein GSAP, kein
ScrollTrigger, kein Intersection Observer, keine KB im Budget.

**Das ist ab sofort der erste Weg für scrollgebundene Bewegung.** Eine Bibliothek dafür ist nur noch
zu rechtfertigen, wenn eine Sequenz nachweislich nicht in CSS abbildbar ist — und das steht dann
begründet in der Herkunftsliste.

Wo die Eigenschaft fehlt, passiert schlicht nichts: Der Inhalt steht sofort da. Das ist der richtige
Rückfall und muss geprüft werden — **nie** Inhalte erst durch Bewegung sichtbar machen.

#### Sanftes Scrollen (Lenis) — zulässig, aber zuletzt

**Lizenz und Technik geprüft (25.07.2026):** MIT, `dist/lenis.min.js` als fertige Browser-Datei
selbst hostbar, kein npm nötig. Erfüllt die Einbinderegel oben. Es ist also **nicht** verboten.

**Trotzdem nicht als Erstes, aus einem sachlichen Grund:** Lenis verändert das **Scrollgefühl**,
nicht das Aussehen. Es blendet nichts ein, bewegt kein Bild, zeigt nichts. Auf einer Seite, die als
zu ruhig empfunden wird, gleitet danach dieselbe Ruhe nur weicher vorbei.

Was auf Agenturseiten als „lebendig" wahrgenommen wird, ist fast immer die **Kombination**: große
Bilder, die beim Scrollen einblenden und leicht versetzt mitlaufen — plus weiches Scrollen als
Zugabe. Der sichtbare Teil ist der erste, und der kostet nichts:

| Wirkung | Womit | Kosten |
|---|---|---|
| Einblenden, Versatz, Fortschritt — **das Sichtbare** | CSS `animation-timeline` | 0 KB, kein JavaScript |
| Scrollgefühl — **das Spürbare** | Lenis | JavaScript in jedem Scrollbild |

**Reihenfolge: erst die CSS-Stufe bauen, ansehen, dann entscheiden.** Werden beide Stufen zugleich
eingebaut und das Ergebnis fühlt sich falsch an, ist nicht mehr feststellbar, welche davon es war.

**Bedingungen, falls Lenis danach dazukommt:**

- [ ] `prefers-reduced-motion: reduce` schaltet es **vollständig ab.** Die Projektdokumentation sagt dazu nichts — es muss also von Hand verdrahtet und geprüft werden, nicht angenommen
- [ ] Sprungmarken, Suche im Text (`Strg+F`), Tastaturscrollen und Bildlauf per Rollrad-Klick funktionieren unverändert. Praktisch geprüft, nicht der Beschreibung geglaubt
- [ ] Größe in KB gzip gemessen und im JS-Budget verrechnet (§2.2)
- [ ] Auf einem älteren Telefon geprüft, nicht nur auf dem Entwicklungsrechner
- [ ] Fällt einer dieser Punkte durch: **raus.** Ein Scrollgefühl ist keine kaputte Sprungmarke wert

#### Maß — was diese Zielgruppe verträgt

Die Besucher sind Unternehmer aus Handwerk, Handel und Dienstleistung, meist zwischen 35 und 60, oft
auf dem Telefon zwischen zwei Terminen. Bewegung darf hier **das Lesen unterstützen**, nicht sich
selbst vorführen.

| Trägt | Trägt nicht |
|---|---|
| ruhiges Einblenden beim Scrollen, einmal je Abschnitt | Elemente, die von allen Seiten hereinfliegen |
| ein einziges bewegtes Element im Aufmacher, das **etwas Echtes zeigt** — den Kundenbereich in Benutzung | Partikel, Farbverläufe in Bewegung, 3-D-Figuren |
| weiche Zustandswechsel bei Bedienelementen | erzwungenes langsames Scrollen |
| Seitenwechsel über die View-Transitions-API | Aufmacher, die erst nach zwei Sekunden lesbar sind |

**Harte Grenzen:**

- [ ] `@media (prefers-reduced-motion: reduce)` schaltet **jede** nicht wesentliche Bewegung ab. Das ist kein Zusatz, das ist Pflicht (§2.3)
- [ ] Keine Bewegung verzögert die erste Lesbarkeit. Überschrift, Text und Schaltfläche im ersten Bildausschnitt stehen **sofort**
- [ ] Bewegung wird nie zum Träger einer Information — was sich bewegt, sagt nichts, was nicht auch im Text steht

#### Glaseffekt — geprüft und abgelehnt

Der Apple-artige Milchglaseffekt wird für SARTU **nicht** als gestalterische Sprache eingesetzt. Drei
Gründe, in dieser Reihenfolge:

1. **Lesbarkeit.** Text auf Glas braucht 4,5:1 Kontrast gegen das, was **dahinter** durchscheint — bei einem verschiebbaren, unscharfen Hintergrund ist das nicht zuverlässig einzuhalten. Genau deshalb meiden die meisten Produktteams den Effekt inzwischen wieder
2. **Geräte.** Die Unschärfeberechnung belastet die Grafikeinheit; auf älteren Telefonen ruckelt es und der Akku leert sich schneller. Die Zielgruppe sitzt nicht auf neuen Geräten
3. **Aussage.** Milchglas sagt „Technikprodukt". SARTU muss „verlässlicher Partner" sagen. Ein Handwerksbetrieb, der einen Dienstleister für die nächsten Jahre sucht, sucht keine Oberfläche, die nach Betriebssystem aussieht

**Eng begrenzte Ausnahme:** ein einzelnes Element — etwa eine mitlaufende Kopfzeile über einem
**unbewegten** Hintergrund — darf leicht durchscheinen, wenn der Kontrast an der ungünstigsten Stelle
gemessen und dokumentiert ist. Als Sprache der Seite: nein.

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

### 3.2a Bildmaterial — der eigentliche Engpass

Bewegung, Weißraum und Typografie können vieles, aber **eine Seite ohne Bilder bleibt eine Seite ohne
Bilder.** Wer den Eindruck „zu ruhig" beheben will, kommt an dieser Frage nicht vorbei.

**Was es zum Start tatsächlich gibt:**

| Quelle | Stand | Wofür |
|---|---|---|
| **Ansichten aus dem Kundenbereich** | entsteht in Sitzung 2 | Aufmacher und Portal-Abschnitt — die zwei Stellen, an denen das Lastenheft ein Bild verlangt. **Die stärkste Quelle**, weil sie das Unterscheidungsmerkmal zeigt |
| **Foto des Gründers** | vorhanden, sobald fotografiert | `/ueber-uns`, eventuell Aufmacher |
| **Eigene Demoprojekte** | *existiert nicht* — siehe unten | zeigt, was SARTU herstellt |
| Bestandsfotos aus Bilddatenbanken | verfügbar | **Nicht verwenden.** Ein austauschbares Bestandsfoto ist bei einem Anbieter, der Echtheit verkauft, schlechter als gar kein Bild |

**Die Lücke, die niemand mit Gestaltung schließt:** SARTU hat kein Beispiel eigener Arbeit. Eine
Webagentur ohne sichtbares Ergebnis hat ein Glaubwürdigkeitsproblem, das keine Gestaltung behebt —
und gleichzeitig fehlt genau daraus das Bildmaterial.

**Vorschlag, offen zu entscheiden** (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5): ein bis zwei **erkennbar
gekennzeichnete Demoprojekte** bauen — vollständige Beispielseiten für erfundene, als solche
benannte Betriebe. Das ist keine erfundene Referenz, solange es **deutlich sichtbar** als
Demonstration ausgewiesen ist und kein Kunde behauptet wird. Es liefert dreierlei auf einmal:
echtes Bildmaterial, einen Beleg für die Arbeitsqualität und einen ersten Belastungstest des
Produktionswegs.

**Bis dahin gilt:** kein Platzhalterrahmen an einer Vertrauensstelle (§4a). Wo kein echtes Bild
existiert, wird die Stelle **ohne** Bild gelöst — nicht mit einem leeren Kasten.

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

### 3.6 Was drei Varianten unterscheiden muss

**Eine Farbpalette ist keine Richtung.** Drei Varianten, die sich nur in Farbe und Schriftart
unterscheiden, sind eine Variante in drei Anstrichen — der Mensch entscheidet dann nichts, egal
welche er wählt.

Die Varianten müssen sich in **mindestens vier** dieser sechs Punkte unterscheiden:

| Merkmal | Die Varianten unterscheiden sich darin, ob … |
|---|---|
| **Reihenfolge** | direkt nach dem Aufmacher der Preis kommt, der Beweis oder der Ablauf |
| **Angebotsdarstellung** | Tabelle · Fließtext mit einer einzigen hervorgehobenen Zahl · Gegenüberstellung zweier Wege |
| **Seitenlänge** | eine lange Seite alles erklärt · eine kurze Seite früh in den Bedarfsscheck führt |
| **Typografie** | eine Schrift in vielen Größen trägt · zwei Schriften mit klarer Aufgabenteilung |
| **Bildanteil** | mit Bildern gearbeitet wird · bewusst ohne, dafür mit Typografie und Flächen |
| **Führung** | eine durchgehende Erzählung entsteht · nebeneinanderstehende Blöcke zum Springen |

**Graustufentest.** Setze alle Varianten auf Graustufen und sieh sie nebeneinander an. Sind sie dann
nicht mehr auseinanderzuhalten, sind es keine drei Varianten. Dann noch einmal — vor der Vorlage,
nicht danach.

### 3.7 Abschnittsrhythmus — die häufigste Ursache für „wirkt maschinell"

Nicht Farben und nicht Schriften lassen eine Seite gemacht statt gestaltet aussehen, sondern die
**Wiederholung desselben Aufbaus**. Wenn die Abschnitte 3, 5, 7 und 9 alle nach dem Muster
„kleines Label, große Überschrift links, Absatz rechts, darunter drei gleich breite Spalten" gebaut
sind, liest sich das wie eine Liste, die jemand abgearbeitet hat.

**Regel: Kein Aufbaumuster kommt auf einer Seite mehr als zweimal vor.** Bei acht Abschnitten heißt
das mindestens vier verschiedene Muster. Ein Abschnitt darf ruhig einmal die Breite sprengen, einmal
ganz schmal stehen, einmal nur aus einem Satz bestehen.

### 3.8 Überschriften — Zeilenzahl ist eine Folge, keine Ursache

**Schauüberschriften laufen über höchstens drei Zeilen.** Vier ist die absolute Obergrenze, und sie
gilt nur für den Aufmacher. Alles darüber wird nicht gelesen, sondern überflogen — und wirkt wie ein
Absatz, dem jemand versehentlich Überschriftengröße gegeben hat.

**Die Ursache ist fast nie der Text.** „Individuell programmierte Firmenwebsites zum Festpreis." sind
55 Zeichen — das sind zwei Zeilen bei vernünftigem Satz. Werden sechs Zeilen daraus, stehen dort neun
Zeichen je Zeile, und das Verhältnis von Schriftgröße zu Spaltenbreite ist kaputt, nicht die Copy.

**Messbare Regel: 25 bis 40 Zeichen je Zeile.** Unter 20 ist die Spalte zu schmal oder die Schrift zu
groß. Zu beheben in dieser Reihenfolge:

1. Spalte breiter — eine Schauüberschrift darf über die volle Satzbreite laufen
2. Schrift kleiner — groß wirkt eine Überschrift durch **Kontrast** zum Fließtext, nicht durch
   absolute Größe
3. Umbruch von Hand setzen, damit die Zeile an einer sinnvollen Stelle bricht

**Was nicht erlaubt ist: den Text kürzen.** Die Überschriften stehen wörtlich im Website-Lastenheft
§5 und sind abgestimmt. Wer sie zurechtstutzt, damit der Satz aufgeht, löst ein Gestaltungsproblem
auf Kosten der Aussage. Passt ein Text auch bei voller Breite und angemessener Größe nicht in drei
Zeilen: **melden**, nicht selbst umschreiben.

### 3.9 Schaltflächen — die verräterischste Einzelheit

Die vollrunde Pillenform, gern als Paar aus gefüllter und umrandeter Fläche nebeneinander, ist eines
der am sichersten wiedererkennbaren Merkmale erzeugter Seiten. Sie fällt besonders auf, wenn der Rest
der Seite scharfkantig ist — dann ist die Schaltfläche das einzige Element mit einer fremden
Formsprache.

- [ ] **Die Rundung stammt aus derselben Skala wie alles andere.** Ob die Seite weich oder streng
      ist, entscheidet die Richtung (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §3) — **hier gilt nur, dass
      sie es überall gleich ist.** Eine scharfkantige Seite verträgt keine Pillenknöpfe; eine
      durchgehend weiche Seite verträgt keine hart geschnittenen Kästen. Der Fehler ist die
      Mischung, nicht die Wahl
- [ ] **Höhe und Innenabstand kommen aus der Abstandsskala**, nicht aus einer übernommenen Vorlage
- [ ] **Je Abschnitt eine sichtbare Hauptaktion.** Stehen zwei gleich gewichtete Flächen
      nebeneinander, hat niemand entschieden, was der Besucher tun soll. Die zweite Möglichkeit wird
      zum Textlink, nicht zur zweiten Schaltfläche
- [ ] Die Beschriftung sagt, was passiert („Bedarfsscheck starten"), nicht was man tut („Klicken")

---

## 4. Prüfliste vor dem Einsatz

Für **jedes** Teil, das du übernehmen willst:

- [ ] **Lizenz** erlaubt kommerzielle Nutzung **und** Weitergabe im Kundenprojekt (Lizenzdatei gelesen)
- [ ] **Gepflegt** — letzte Änderung nachvollziehbar aktuell, keine offenen Sicherheitsprobleme
- [ ] **Größe** gemessen und im Budget
- [ ] **Barrierefrei** — Tastatur, Fokus, ARIA belegt, nicht nur behauptet
- [ ] **Lizenz geprüft** (§2.1): kostenlos **und** Weitergabe erlaubt (MIT, Apache-2.0, ISC, BSD, CC0, OFL). Lizenzdatei selbst gelesen
- [ ] **Passt technisch** — läuft **ohne Build-Schritt** und **ohne Framework-Laufzeit**. Erzwingt es React, Vue, einen Bundler oder einen Paketmanager zur Laufzeit: nicht direkt einbinden, sondern nach §3.1 portieren
- [ ] **Utility-Klassen sind übersetzt** — kein `px-4 py-2` im ausgelieferten Markup, sondern eigenes CSS mit den zentralen Variablen
- [ ] **Ebene geprüft** (§3.1): Baustein → darf nah sein · Sektion → Mechanik ja, Komposition neu · ganze Seite → nie
- [ ] **In der Herkunftsliste eingetragen** mit Quelle, Version, Lizenz, Stufe und Änderung
- [ ] **Umgestaltbar** — Farben, Schriften und Abstände über Variablen änderbar
- [ ] **Nicht wiedererkennbar** — man sieht dem Ergebnis die Herkunft nicht an
- [ ] **Ohne externe Verbindungen** zur Laufzeit

Fällt ein Punkt durch: nicht einsetzen. Kein „passt schon".

---

## 4a. Prüfliste gegen den Maschineneindruck

Vor **jeder** Vorlage abzuhaken. Diese Punkte sind nicht Geschmack, sondern nachsehbar:

- [ ] **Kein Aufbaumuster kommt mehr als zweimal vor** (§3.7)
- [ ] **Graustufentest bestanden** — die Varianten sind ohne Farbe unterscheidbar (§3.6)
- [ ] **Keine Schauüberschrift über drei Zeilen**, 25–40 Zeichen je Zeile (§3.8) — nachgemessen, nicht geschätzt
- [ ] **Der verkaufende Text ist lesbar, nicht Beiwerk.** Die Fließtexte der Startseite tragen die Kaufentscheidung. Werden sie klein und grau neben eine riesige Überschrift gesetzt, bleibt vom Abschnitt nur die Überschrift — und eine Überschrift allein verkauft nichts. Mindestens 17 px, voller Textkontrast, Zeilenlänge 60–75 Zeichen
- [ ] **Kein Abschnitt stammt von einer anderen Seite.** Gebaut wird genau die Sektionsliste des jeweiligen Lastenheftabschnitts — nichts dazu, nichts umgehängt
- [ ] **Keine vollrunden Schaltflächen** auf einer scharfkantigen Seite; je Abschnitt eine Hauptaktion (§3.9)
- [ ] Kein Farbverlauf in Violett oder Blau als tragende Fläche
- [ ] Nicht Inter als einzige Schrift
- [ ] Nicht alles zentriert
- [ ] Symbole stehen **neben** Inhalt, nie **statt** Inhalt
- [ ] **`<html lang="de">` ist gesetzt.** Ohne das trennt der Browser deutsche Wörter nach englischen Regeln — aus „Firmenwebsites" wird „Fir-menwebs-ites". Fällt sofort auf und wirkt unsauber
- [ ] Große Schauüberschriften: Silbentrennung **aus** oder von Hand gesetzt (`&shy;`), nicht dem Automatismus überlassen
- [ ] **Kein Platzhalterkasten an einer Vertrauensstelle.** Ein leerer Rahmen dort, wo der Beweis erwartet wird — im Aufmacher, beim Kundenbereich — beschädigt genau die Aussage, die der Abschnitt trägt. Entweder echter Inhalt, oder die Stelle wird anders gelöst. Es gibt keinen dritten Weg
- [ ] Auf der Seite kommt **ein Mensch** vor — als Foto, als Name, als benannte Verantwortung. Bei einem Betrieb, der von persönlichem Vertrauen lebt, ist eine menschenleere Startseite eine inhaltliche Lücke, keine gestalterische Entscheidung

**Der Abschlusstest.** Nimm die Startseite und irgendeine beliebige KI-erzeugte Anbieterseite.
Entferne bei beiden Logo und Texte. Sind sie noch auseinanderzuhalten? Wenn nicht, hilft kein
saubererer Code.

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
1a. **Welche 1–3 Quellen** du gewählt hast und **warum diese** — mit Lizenz je Quelle und Beleg, dass die Weitergabe erlaubt ist (§2.1)
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
