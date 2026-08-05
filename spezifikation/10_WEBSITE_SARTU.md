# Die eigene Website — `sartu.de`

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus: `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md`
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

> **Hier steht, was SARTU für sich selbst baut.** Was der Kunde kauft, steht in
> `03_KUNDENPRODUKT.md`. Sprache und verbotene Wörter: `08_TEXTREGELN.md`.
> Farbe, Form, Logo: `07_MARKE_UND_GESTALTUNG.md`.

---

## 1. Technischer Rahmen

> **SARTU ist ein Projekt, nicht zwei.** Öffentliche Website, Kundenbereich `/portal/`,
> Adminbereich `/admin/` und Serverfunktionen `/api/` liegen in **einem** modularen PHP-Projekt
> unter **einer** Domain. Architektur und Verzeichnisstruktur: `14_SICHERHEIT.md`.

- **PHP 8.3+, serverseitig gerendert.** Kein CMS, kein Vollframework, kein SPA-Framework, kein
  Node als Zielsystem, kein Build-Schritt fürs Frontend
- **Öffentliche Seiten sind cachebar** — sie hängen nicht an einer Sitzung
- **Kein externes CDN** für Schriften, CSS oder JS. Schriften selbst gehostet als WOFF2,
  `font-display: swap`
- **JavaScript budgetiert statt verboten:** **≤ 75 KB gzip auf der Startseite, ≤ 40 KB auf
  Unterseiten.** Pflichtfunktionen: mobile Navigation, FAQ-Akkordeon, Komfort im Bedarfsscheck.
  Darüber hinaus **höchstens zwei bewusste Markenmomente je Seite**
- **Ohne JavaScript vollständig nutzbar:** Inhalte lesbar, Links funktionieren, **beide Formulare
  absendbar**, Bedarfsscheck vollständig durchlaufbar. **Kein Inhalt darf erst durch eine
  Scroll-Animation sichtbar werden**
- **`prefers-reduced-motion: reduce` ist Pflicht**
- **Bibliotheken:** CSS zuerst. Erlaubt bei Bedarf: Lenis (~3 KB), GSAP + ScrollTrigger (~34 KB).
  **Nicht erlaubt:** Vanta.js, Three.js als Deko, Barba.js
- **`html lang="de"`**, semantische Landmarks, sichtbarer Fokus, Skip-Link „Zum Inhalt springen"
- **Breakpoints:** ≤ 599 px mobil · 600–1023 px Tablet · ≥ 1024 px Desktop.
  Maximale Inhaltsbreite **1280 px**, Fließtext max. **68 Zeichen**
- **Wiederverwendung ist Pflicht:** Layouts, Partials und Komponenten aus `/app/views` werden von
  öffentlichen Seiten **und** Kundenbereich gemeinsam genutzt. **Kein kopiertes Markup**

**Leistungsziele im Labor, vor Livegang:** LCP < 2,5 s · TBT < 200 ms · CLS < 0,1.
**Echtes INP** ist ein Felddatenwert und wird erst **nach** dem Livegang gemessen.

### Was „keine externen Verbindungen" bedeutet

**Verboten zur Laufzeit:** Schrift-, Skript- und Stil-CDNs · Analyse- und Tracking-Dienste ·
eingebettete Karten · Videoportale · Chat-Widgets · Werbe- und Rätselbild-Dienste · externe
Bildhoster.

**Kein Sonderfall sind die Formulare:** Sie laufen im selben Programm. Es gibt **kein**
gemeinsames Geheimnis und **keinen** Aufruf über das Netz.

### Positionierungsschutz

Die Seite darf nicht erkennbar aus einem Template stammen. **Verboten:** Farbverläufe ·
Leuchtflächen · schwebende Dashboard-Karten · Karten in Karten · Emoji-Marker ·
Handschlag-Stockfotos · generische WebGL-Hintergründe.

Frühere Entwürfe unter `design/_verworfen/` sind **nicht** zu verwenden — auch nicht als Anregung.

---

## 2. Hauptnavigation

> **Dies ist die einzige gültige Navigation.** Gilt für Desktop **und** Mobil-Overlay.

`Leistungen` · `Preise` · `Ablauf` · **`Kundenbereich`** · `Über uns` · `Fragen`

- **`Ergebnis` ist gestrichen**, ersetzt durch `Leistungen` — die konkretere Bezeichnung
- **`Portal` ist gestrichen**, ersetzt durch `Kundenbereich` (30.07.2026). Das Portal-Lastenheft
  führt `Portal` unter „nach außen nie verwenden", und die Startseite schreibt zehnmal
  `Kundenbereich`
- **Wird die Zeile zu breit, greift das Mobilmenü früher** — der verständlichere Begriff wird
  nicht für sechs Pixel geopfert

**Beschriftungsregel:** Jeder Punkt muss verraten, was dahinterliegt. **Im Zweifel die konkretere
Bezeichnung wählen, nicht die elegantere.**

> **Offener Widerspruch:** §5b sagt an einer Stelle, `Leistungen` bleibe in der Hauptnavigation,
> zwei Absätze später, es wandere in den Fußbereich. Beide tragen eine Begründung. Gebaut ist die
> Punkteliste oben. **Entscheidung nötig** — siehe Dublettenregister im Wegweiser.

---

## 3. Startseite — zehn Sektionen

**Die Spalte „Bauform" ist verbindlich.** Eine frühere Fassung bestand von oben bis unten aus
demselben Zeilenmuster — jede Einzelentscheidung war richtig, das Ergebnis war eine Seite ohne
einen einzigen Gangwechsel. **Kein Aufbaumuster kommt mehr als zweimal vor.**

| # | Sektion | beantwortet | Bauform |
|---|---|---|---|
| 1 | Aufmacher | welches Problem, welches Produkt, für wen | zweispaltig, Text 55 % |
| 2 | Der Kundenbereich | „Was unterscheidet euch?" | **bildgeführt** |
| 3 | Ablauf | „Wie läuft das?" | Zeitstrahl |
| 4 | Preise | „Was kostet es?" — und der Belegersatz | Stufen, eine dunkel |
| 5 | Die Zusage | Luft holen | **randlos dunkel, ein Satz** |
| 6 | Wer dahintersteckt | „Wem vertraue ich hier?" | Porträt |
| 7 | Leistungen | „Ist alles dabei?" | Zeilen — die einzige Liste |
| 8 | Musterprojekte | „Was kommt dabei heraus?" | Karten |
| 9 | Häufige Fragen | die letzten Einwände | Akkordeon |
| 10 | Bedarfsscheck | die Handlung — **zuletzt** | dunkel |

**Warum diese Reihenfolge:** Der Aufmacher beantwortet „welches Problem, welches Produkt, für
wen". Dann kommt das **Unterscheidungsmerkmal** (2), dann **Vorhersehbarkeit** (3) — beides
erhöht die Zahlungsbereitschaft, **bevor die Zahl fällt**. Erst danach der Preis.

**Der vollständige Preisblock bleibt auf der Startseite**, nicht nur ein Teaser. Bei SARTU ist
der veröffentlichte Preis der **Belegersatz** für fehlende Referenzen.

### So sind die Sektionen notiert

**Der Skill schreibt, dieses Dokument beauftragt.** Drei Angaben je Textstelle:

| | |
|---|---|
| **Aufgabe** | was der Leser danach wissen oder tun können muss |
| **Grenze** | was der Text nicht behaupten, nicht auflösen, nicht erfinden darf |
| **Maßstab** | die bisherige, geprüfte Fassung. **Ziel, nicht Vorlage** — abschreiben hilft nicht, treffen schon |

**Gebunden bleibt nur, was `.claude/skills/sartu-texter/SKILL.md` als Klasse 1 führt:** jede Zahl,
vertragliche Erklärungen, Rechtstexte, Pflichthinweise, Knopf- und Navigationsbeschriftungen,
Statusnamen und die **vier Positionierungssätze**. Diese Stellen sind unten mit **`gebunden`**
markiert und werden **nie** umgeschrieben.

---

### Sektion 1 — Aufmacher

**Eyebrow**
- *Aufgabe:* In vier bis fünf Wörtern sagen, in welcher Kategorie SARTU antritt — bevor die H1 gelesen wird
- *Grenze:* keine Sammelbezeichnung wie „Handwerk"; nicht „Digitalagentur"
- *Maßstab:* `Webdesign-Agentur für Firmenwebsites`

**H1**
- *Aufgabe:* Produkt und Preismodell in einem Satz. Der Leser muss danach wissen, was er kauft und dass der Preis vorher feststeht
- *Grenze:* keine erfundene Zahl; nicht „günstig"; „individuell" nur, wenn es durch „programmiert" gedeckt ist
- *Maßstab:* `Individuell programmierte Firmenwebsites zum Festpreis.`

**Vorspann** — **22 Wörter, drei Sätze, längster 10**
- *Aufgabe:* Die Arbeitsteilung erklären und den Termin-Einwand vorwegnehmen
- *Grenze:* keine Aufzählung der Leistungen — die stehen in Sektion 7. Nicht über 22 Wörter
- *Maßstab:* `Sie erzählen uns, was Ihr Betrieb macht und für wen. Den Rest bauen und betreiben wir. Dafür ist kein einziger Termin nötig.`
- *Verworfen, nicht wieder verwenden:* die 38-Wörter-Fassung mit „SARTU plant, textet, programmiert und betreibt …"

**Gebunden:**
- Primär-CTA `Bedarf prüfen lassen` → `/briefing` · Sekundär-CTA `Preise ansehen` → `/preise`
- Preishinweis `Alle Preise netto zzgl. USt. Ausschließlich für Unternehmer.`
- Trust-Zeile: `Festpreis vorab` · `Texte inklusive` · `Bundesweit, ohne Termin` · `SEO-Basis ab Launch`
- Branchenangabe: `Handwerk` · `Praxen` · `Kanzleien` · `Ladengeschäfte`

**Bau:** Branchenangabe **darf nicht anklickbar aussehen** — eine Form, die Interaktion verspricht
und keine liefert, ist ein Bedienfehler. Visual rechts mit Kennzeichen **`Musteransicht`**
(gebunden). Kapazitätszeile bei freien Kapazitäten **leise** unter dem Knopf, bei `ausgebucht`
abgesetzt **über** dem Knopf.

**Verhalten:** Desktop zweispaltig, Text 55 % / Visual 45 %. Mobil einspaltig — **H1 zuerst,
Knöpfe direkt darunter, Visual danach**, Trust-Zeile als 2 × 2-Raster. Das Visual scrollt **nicht**
horizontal. **Der untere Rand des ersten Viewports zeigt bereits einen Anschnitt der nächsten
Sektion.**

---

### Sektion 2 — Der Kundenbereich

**Der wichtigste Abschnitt der Seite.** Er trägt das Unterscheidungsmerkmal und ist zugleich die
stärkste zitierfähige Stelle für KI-Antworten.

**Eyebrow**
- *Aufgabe:* den Abschnitt benennen
- *Grenze:* **nie** `Portal`, `App`, `Dashboard` — `08_TEXTREGELN.md`
- *Maßstab:* `Kundenbereich`

**H2 — `gebunden`, Positionierungssatz**
> `Ohne einen einzigen Termin zur fertigen Website.`

Sieben Wörter, eine bestreitbare Behauptung, das Unterscheidungsmerkmal. **Vier Vorfassungen
wurden verworfen.**

**Antwortsatz** — **vier Sätze, längster 14 Wörter**, direkt unter der Überschrift
- *Aufgabe:* Erklären, wie es ohne Termine geht, und auf die Liste darunter zeigen
- *Grenze:* **die Liste nicht vorwegnehmen.** Die Liste *ist* der Antwortsatz
- *Maßstab:* `Bei SARTU gibt es keine Abstimmungstermine. Alles läuft über Ihren Kundenbereich. Sie beantworten die Fragen zu Ihrem Betrieb, wann es Ihnen passt. Was dort geht, steht unten — vollständig.`
- *Verworfen:* ein Satz mit 45 Wörtern und fünf Gliedern, der die Liste vorwegnahm

**Direkt darunter — `gebunden`, Positionierungssatz**
> `Sprechen können Sie trotzdem mit uns. Sie müssen nur nicht.`

Fängt das Missverständnis „nicht erreichbar" ab, bevor es entsteht.

**Zwei Listen nebeneinander — vollständig, nicht gekürzt, nicht in Fließtext aufgelöst:**

| `Vor dem Start` | `Nach dem Start` |
|---|---|
| Angebot ansehen und annehmen | Öffnungszeiten und Kontaktdaten ändern |
| Fragen zu Ihrem Betrieb beantworten, wann es Ihnen passt | Bilder tauschen |
| Logo, Bilder und Unterlagen hochladen | Team- und Projekteinträge pflegen |
| Sehen, was gerade ansteht und was erledigt ist | Anfragen von Ihrer Website einsehen |
| Die fertige Vorschau ansehen | Rechnungen und Laufzeit einsehen |
| Änderungen sammeln und in einem Durchgang schicken | Änderungswünsche stellen |
| Freigeben | Domainstatus einsehen |

> **Achtung:** Drei Einträge der rechten Spalte sind im Bau **gesperrt** — `11_KUNDENBEREICH.md`.
> Sie dürfen nicht als vorhandene Selbstbedienung angekündigt werden.

**Hervorgehobene Unterschiedszeile**
- *Aufgabe:* In drei Verneinungen zeigen, was wegfällt
- *Grenze:* höchstens drei Glieder; keine vierte Verneinung
- *Maßstab:* `Kein Terminkalender-Pingpong. Kein Suchen in alten E-Mails. Kein Anruf, um den Stand zu erfahren.`

**Bild:** Ansicht aus dem Kundenbereich, Vermerk `Musteransicht` (gebunden). Solange keine echte
Aufnahme vorliegt: **ehrlich beschrifteter Bildplatz, keine nachgebaute Oberfläche**.

**Gebunden:** Textlink `Den Kundenbereich ansehen` → `/leistung-portal`

---

### Sektion 3 — Ablauf

**H2**
- *Aufgabe:* Die Arbeitsteilung als Versprechen formulieren — die Mitwirkung des Kunden ist klein und begrenzt
- *Grenze:* keine Zahl erfinden; „alles andere" nicht in eine Aufzählung auflösen
- *Maßstab:* `Sie liefern die Fakten. Alles andere machen wir.`

**Sechs Schritte** — nummeriert, weil es eine echte Reihenfolge ist
- *Aufgabe:* Je Schritt ein Satz: was passiert, und wer etwas tun muss
- *Grenze:* **genau sechs.** Keine Dauer erfinden — Lieferkorridore stehen in `03_KUNDENPRODUKT.md`. Kein Schritt ohne handelnde Person
- *Maßstab:*
  1. **Bedarfsscheck** — Wenige Fragen zu Unternehmen, Ziel, Umfang und Domain
  2. **Geprüftes Angebot** — Sie bekommen Umfang, Preis und Zahlungsplan schriftlich
  3. **Ihre Angaben** — Was wir schon wissen, tragen wir ein. Den Rest fragen wir im Kundenbereich
  4. **Produktion** — Wir bauen die Website. KI hilft, geprüft und freigegeben wird von uns
  5. **Vorschau und Freigabe** — Sie sehen die fertige Website und sammeln Ihre Änderungen
  6. **Start und Betrieb** — Wir schalten live und halten die Seite am Laufen

**Gebunden:** CTA `Ablauf im Detail` → `/ablauf`

---

### Sektion 4 — Preise

**H2**
- *Aufgabe:* Klarmachen, dass der Kunde **nicht** auswählt, sondern eine begründete Empfehlung bekommt
- *Grenze:* nie „Paket wählen", „konfigurieren", „Extras" — `08_TEXTREGELN.md`
- *Maßstab:* `Sie wählen kein Paket. Wir sagen Ihnen, welcher Umfang passt.`

**Einleitung** — **25 Wörter, drei Sätze**
- *Aufgabe:* Die Zuordnungslogik erklären, bevor die Zahlen kommen
- *Grenze:* keine Zahl vorwegnehmen; nicht rechtfertigen

**Alle Zahlen und Merkmale: `gebunden`** — `02_PREISE_UND_ZAHLUNG.md`. Die Zahl steht **zuerst**,
vor der Zielgruppe: sie ist das, was der Leser vergleichen kann.

**Gebunden — je Stufe ein Knopf:**

| Stufe | CTA |
|---|---|
| Start | `Einschätzen lassen` |
| Wachstum | `Einschätzen lassen` |
| **Platzhirsch** (Badge `Empfehlung`) | `Bedarf prüfen lassen` |
| Sonderprojekt | `Sonderprojekt besprechen` |

**Gebunden — Pflichtzeile beim Sonderprojekt, direkt unter dem Knopf:**
> `Nur Sonderprojekte klären wir vor dem Angebot persönlich.`

Löst einen Widerspruch in acht Wörtern und nennt den Zeitpunkt.

---

### Sektion 5 — Die Zusage

Ein **randlos dunkler Streifen** mit einem einzigen großen Satz. **Sonst nichts: kein Bild, keine
Aufzählung, kein Knopf.**

**`gebunden`, Positionierungssatz:**
> `Ein Preis. Ein Ergebnis. Keine Stundenabrechnung, keine Nachforderung.`

Drei Einheiten, acht Wörter. **Die Musterzeile des Projekts.**

**Zweck ist der Gangwechsel.** Nach dem Preisblock und vor den Einzelheiten holt die Seite Luft.
Dieser Abschnitt kostet nichts, braucht kein Bildmaterial und ist das wirksamste Mittel gegen den
Eindruck einer durchgehenden Liste. Er wird **nicht** um Unterpunkte, Symbole oder einen zweiten
Satz ergänzt — **die Wirkung entsteht aus der Leere ringsum.**

---

### Sektion 6 — Wer dahintersteckt

**H2**
- *Aufgabe:* Die Person hinter dem Angebot benennen und die Verantwortung über den Launch hinaus zusagen
- *Grenze:* **nie „unser Team"**, solange eine Einzelperson arbeitet — `06_RECHT.md`. Keine Erfolgsgeschichte
- *Maßstab:* `Eine Person baut Ihre Website. Dieselbe antwortet danach.`

**Haltung** — **zwei bis drei Sätze**
- *Aufgabe:* Warum diese Person das macht, in Alltagssprache
- *Grenze:* **kein Lebenslauf, keine Erfolgsgeschichte, keine Zahlen**

**Gebunden:**
- **Echtes Foto** von `[GRUENDER_NAME]` — keine Bestandsaufnahme, kein Platzhalter, der wie ein Foto wirkt. **Ohne Foto entfällt die Sektion vollständig**
- Vier Punkte `Was SARTU bewusst nicht ist`: `kein Baukasten` · `kein WordPress-Hoster` · `keine Billig-Seitenschleuder` · `kein Anbieter für Privat- und Hobbyseiten`
- Textlink `Mehr über SARTU` → `/ueber-uns`

**Warum diese Sektion existiert und warum genau hier:** Sie ist der **Belegersatz**. Der übliche
Platz für Kundenlogos bleibt zum Start leer. Was stattdessen trägt: ein Mensch mit Namen und
Gesicht — und die ausdrückliche Aufzählung dessen, was SARTU **nicht** macht. **Wer seine Grenzen
benennt, wirkt geprüft; wer nur Vorzüge aufzählt, wirkt beliebig.**

---

### Sektion 7 — Leistungen

**H2**
- *Aufgabe:* In einem Satz sagen, dass der Preis nicht nachträglich wächst
- *Grenze:* keine Aufzählung in der Überschrift; nichts, was „wer entscheidet das?" auslöst
- *Maßstab:* `Es gibt keine Aufpreisliste.`
- *Verworfen:* `Alles, was eine Firmenwebsite braucht` — **wer entscheidet, was sie braucht?**

**Einleitung** — **30 Wörter**
- *Aufgabe:* Klarstellen, dass nichts zusammengestellt und nichts extra bezahlt wird
- *Grenze:* die acht Zeilen nicht vorwegnehmen
- *Maßstab:* `Das alles steckt in jedem Angebot — Sie stellen es nicht selbst zusammen und zahlen nichts davon extra. Wir gewichten die Bausteine passend zu Ihrem Ziel.`

**Acht breite Zeilen** — Titel · **ein** Satz · Tags. **Keine Kachelwand, keine Preise.**
- *Aufgabe:* Je Zeile in **einem** Satz sagen, was enthalten ist und was das für den Kunden bedeutet
- *Grenze:* **genau acht.** Ein Satz je Zeile, nicht zwei. Keine Fachbegriffe im Satz — die gehören in die Tags. Keine Wirkung versprechen
- *Maßstab:*

| Titel | Satz | Tags |
|---|---|---|
| Strategie und Seitenstruktur | Wir legen fest, welche Seiten Ihr Ziel wirklich brauchen — und welche nicht. | Sitemap · Nutzerführung · Suchintention |
| Webdesign und Programmierung | Individuell aus unserem Designsystem programmiert, ohne WordPress und ohne Baukasten. | kein WordPress · responsive · schnell |
| Website-Texte | Wir schreiben die Texte aus Ihren Fakten und Stichpunkten. | aus Stichpunkten · Faktenprüfung |
| **SEO- und GEO-Grundlage** | Jede Seite startet mit klarem Thema, sauberen Metadaten, strukturierten Daten und Antwort-zuerst-Texten. | Titles · Schema · Antwort-zuerst · interne Links |
| Lokale Sichtbarkeit | Echte Unternehmensdaten statt dünner Ortsseiten mit ausgetauschtem Stadtnamen. | Local SEO · konsistente Daten |
| Domain und Launch | Wir prüfen, verbinden und schalten live — Ihre bestehende E-Mail bleibt erreichbar. | DNS · E-Mail-Schutz · Weiterleitungen |
| Kundenbereich und Freigaben | Angebot, Briefing, Vorschau und Feedback laufen an einem Ort statt in E-Mail-Ketten. | Briefing · Feedback · Pflege |
| Rundum-Schutz | Wir betreiben die Website danach: Hosting, Sicherheit, Backups, Monitoring. | Betrieb · Backups · Monitoring |

**Gebunden:** CTA `Alle Leistungen im Überblick` → `/leistungen`

> **Offen:** Der Rundum-Schutz-Satz nennt die **Monatspauschale nicht**, steht aber unter „Im
> Preis enthalten". Vorschlag: „… über die Monatspauschale" ergänzen. Siehe `OFFENE_PRUEFUNGEN.md`.

---

### Sektion 7a — Sichtbarkeit

**H2**
- *Aufgabe:* Sagen, dass die Grundlage ab Tag eins steht — **ohne** Sichtbarkeit zu versprechen
- *Grenze:* **nie** „auffindbar", „garantiert", „Platz 1". Die Aufnahme in den Index liegt nicht bei uns
- *Maßstab:* `Ihre Website ist ab dem ersten Tag für Suchmaschinen vorbereitet.`
- *Verworfen:* `Ihre Website ist ab dem ersten Tag auffindbar.` — **überzieht**

**Text** — **42 Wörter**
- *Aufgabe:* Nennen, was konkret eingebaut ist
- *Grenze:* keine Wirkung behaupten, keine Zeitangabe zum Ranking

**Drei Spalten**
- *Aufgabe:* Dieselbe Grundlage aus drei Blickwinkeln — Menschen, Suchmaschinen, KI-Antworten
- *Maßstab:* `Menschen verstehen` · `Suchmaschinen erfassen` · `KI-Antworten einordnen`

**Gebunden — Pflichthinweis:**
> Rankings, Anfragen oder Nennungen in KI-Systemen kann niemand garantieren. Wir bauen das
> Fundament und halten die technische Suchgesundheit im Betrieb im Blick.

---

### Sektion 8 — Musterprojekte

**Der Ersatz für fehlende Referenzen** — und der einzige, der ohne Kunden funktioniert.

**H2**
- *Aufgabe:* Die fehlenden Referenzen offen benennen, statt sie zu umschreiben
- *Grenze:* **kein Konjunktiv.** Eine Zahl nennen, auch wenn sie null ist
- *Maßstab:* `Noch keine Kunden. Deshalb zeigen wir Musterprojekte.`
- *Verworfen:* jede `könnte`-Fassung — Konjunktiv ist keine Aussage

**Einleitung** — einmal über dem Raster
- *Aufgabe:* Gründungsjahr nennen und klarstellen, dass es Muster sind
- *Grenze:* sich nicht selbst erklären; keine drei Glieder ohne Information
- *Maßstab:* `SARTU ist 2026 gestartet. Die drei Beispiele sind Muster, keine Kundenaufträge.`

**Gebunden:**
- Über jeder Karte: `Musterprojekt — kein Kundenauftrag`. **Die Bezeichnung bleibt** — `Konzeptstudie` sagt gerade **nicht**, worauf es rechtlich ankommt
- Drei Gattungen: **Malerbetrieb · Physiotherapiepraxis · Arbeitsrechtskanzlei**
- CTA `Alle Musterprojekte ansehen`

Je Karte: Ausgangslage · empfohlene Lösung · Seitenstruktur · Bildplatz · was der Kunde selbst
liefern müsste.

**Sperren:** keine erfundenen Firmennamen · keine erfundenen Zahlen, Ergebnisse oder
Steigerungen · solange die Beispielseiten nicht gebaut sind: **ehrlich beschrifteter Bildplatz,
kein nachgebauter Bildschirm**. Benennungsregeln: `08_TEXTREGELN.md`.

---

### Sektion 9 — Häufige Fragen

**10–12 Einwände als Akkordeon.**

- *Aufgabe je Antwort:* Den Einwand **im ersten Satz** entkräften, dann belegen
- *Grenze:* keine Antwort über vier Sätze. Keine Zahl ohne Fundstelle. Nichts versprechen, was `11_KUNDENBEREICH.md` als gesperrt führt
- *Reihenfolge gebunden:* **Die Reichweitenfrage steht an erster Stelle.** Sie entscheidet, ob der Leser sich überhaupt angesprochen fühlt — vorher stand die Reichweite nirgends auf der Seite

**Die Fragen — Wortlaut der Fragen als Maßstab, Antworten frei:**

| # | Frage | Kern der Antwort |
|---|---|---|
| 1 | `Arbeiten Sie auch außerhalb von Sachsen?` | Ja, bundesweit. Weil es keine Termine gibt, spielt Entfernung keine Rolle |
| 2 | `Muss ich mir selbst ein Paket aussuchen?` | Nein. Wir empfehlen einen Umfang und begründen ihn — auch nach unten |
| 3 | `Schreiben Sie die Texte?` | Ja, aus Fakten und Stichpunkten. Keine erfundenen Belege |
| 4 | `Warum gibt es keine Liste mit Zusatzoptionen?` | Zusatzlisten machen den Preis unklar. Neue Ziele → eigenes Festpreisangebot |
| 5 | `Was passiert mit meiner Domain und meinen E-Mail-Adressen?` | Die Domain gehört Ihnen. Einträge werden vor jeder Änderung gesichert |
| 6 | `Kann ich später selbst etwas ändern?` | Öffnungszeiten und Kontaktdaten selbst. Texte, Bilder, Struktur ändert SARTU — im Betrieb enthalten |

> **Korrigiert am 01.08.2026:** Die alte Antwort auf Frage 6 versprach zusätzlich Team- und
> Projekteinträge sowie Bildertausch als Selbstbedienung. **Diese Funktionen sind gesperrt.**

---

### Sektion 10 — Bedarfsscheck-Einstieg

**H2**
- *Aufgabe:* Die Frage stellen, die der Bedarfsscheck beantwortet — als Einladung, nicht als Aufforderung
- *Grenze:* kein Ausrufezeichen, kein „Jetzt"
- *Maßstab:* `Welche Website passt zu Ihrem Unternehmen?`

**Text** — **36 Wörter**
- *Aufgabe:* Aufwand, Ergebnis und Unverbindlichkeit nennen
- *Grenze:* keine Dauer erfinden, die nicht belegt ist

**Gebunden:** CTA `Bedarf prüfen lassen` primär → `/briefing` · `Preise ansehen` sekundär →
`/preise` · Pflichthinweis direkt darunter

---

## 4. Die übrigen Seiten

| Adresse | Inhalt |
|---|---|
| `/leistungen` | Leistungsübersicht — beantwortet „webdesign", „firmenwebsite erstellen lassen" |
| `/preise` | vollständiger Preisblock |
| `/ablauf` | Ablauf im Detail |
| `/briefing` | **Bedarfsscheck, feldgenau** — Preis vor Kontaktdaten, ohne JavaScript durchlaufbar |
| fünf Leistungsseiten | u. a. `/leistung-portal`, `/leistung-seo-lokal` |
| Branchenseiten | **vollständige Zielseiten, keine Durchgangsstationen** |
| `/ueber-uns`, `/kontakt` | mit Transparenz-Pflichtblock |
| Ratgeber | zwei Vergleichsartikel |
| Lexikon | 8 Startbegriffe, u. a. `/lexikon/geo-ki-suche` |
| Pflichtseiten | Impressum, Datenschutz, AGB, 404 |

**Zum Launch gibt es drei Gewerkeseiten:** Sanitär-Heizung-Klima · Elektrotechnik · Dachdecker.

> **Verworfen:** eine Seite `/webdesign-handwerk`. „Handwerk" ist genau die Sammelbezeichnung,
> die dieses Projekt sonst überall verbietet. Gerankt wird über eigene Seiten je Gewerk.

Feldgenaue Vorgaben zu `/briefing`, den Leistungs- und Branchenseiten, der Bild- und
Screenshotliste sowie der vollständigen SEO-URL-Liste stehen bis zur weiteren Zusammenführung
in `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` §9 bis §16.

---

## 5. Prüfung vor dem Livegang

- **Sieben echte Menschen** lesen die Seite gegen — nicht nur der Erbauer
- **Definition of Done** je Seite, inklusive ausgefülltem **Prüfbericht mit Zahlen**
- Was **erst nach** dem Livegang geprüft wird: echte Core Web Vitals inklusive INP als
  Feldmessung
