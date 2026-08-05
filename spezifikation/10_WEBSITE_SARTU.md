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
| **Umfang** | die messbare Schranke — Wörter, Sätze, Glieder |

**Hier steht kein Beispielsatz, und das ist Absicht.** Ein fertiger Satz unter der Aufgabe wird
übernommen, nicht getroffen — dann hätte die Umstellung nichts gebracht. Die geprüften Fassungen
vom 01.08.2026 liegen in `archiv/STARTSEITE_WORTLAUT_2026-08.md`; **diese Datei wird beim Texten
nicht geöffnet.** Was von ihnen zählt, steht als **Umfang** in der jeweiligen Zeile: die Grenzen
sind an ihnen gemessen.

**Woran der Ton kalibriert wird:** an den vier tragenden und fünf verworfenen Sätzen in
`.claude/skills/sartu-texter/SKILL.md`. Dort mit Begründung, warum jeder trägt oder scheiterte —
das lässt sich nicht abschreiben, nur verstehen.

**Gebunden bleibt nur, was `.claude/skills/sartu-texter/SKILL.md` als Klasse 1 führt:** jede Zahl,
vertragliche Erklärungen, Rechtstexte, Pflichthinweise, Knopf- und Navigationsbeschriftungen,
Statusnamen und die **vier Positionierungssätze**. Diese Stellen sind unten mit **`gebunden`**
markiert und werden **nie** umgeschrieben.

**Verworfene Fassungen bleiben stehen**, wo eine droht zurückzukehren. Eine Warnung wirkt nur an
der Stelle, an der der Fehler passiert.

---

### Sektion 1 — Aufmacher

**Eyebrow**
- *Aufgabe:* Sagen, in welcher Kategorie SARTU antritt — bevor die H1 gelesen wird
- *Grenze:* keine Sammelbezeichnung wie „Handwerk"; nicht „Digitalagentur"
- *Umfang:* **höchstens vier Wörter**

**H1**
- *Aufgabe:* Produkt und Preismodell in einem Satz. Der Leser muss danach wissen, was er kauft und dass der Preis vorher feststeht
- *Grenze:* keine erfundene Zahl; nicht „günstig"; „individuell" nur, wenn es durch „programmiert" gedeckt ist
- *Umfang:* **ein Satz, höchstens acht Wörter**

**Vorspann**
- *Aufgabe:* Die Arbeitsteilung erklären und den Termin-Einwand vorwegnehmen
- *Grenze:* keine Aufzählung der Leistungen — die stehen in Sektion 7
- *Umfang:* **22 Wörter, drei Sätze, längster 10**
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

**Eyebrow — `gebunden`:** `Kundenbereich`

Kein freier Text, sondern die vorgeschriebene Außenbezeichnung aus `08_TEXTREGELN.md`. `Portal`,
`App` und `Dashboard` sind dort ausdrücklich verboten.

**H2 — `gebunden`, Positionierungssatz**
> `Ohne einen einzigen Termin zur fertigen Website.`

Sieben Wörter, eine bestreitbare Behauptung, das Unterscheidungsmerkmal. **Vier Vorfassungen
wurden verworfen.**

**Antwortsatz** — direkt unter der Überschrift
- *Aufgabe:* Erklären, wie es ohne Termine geht, und auf die Liste darunter zeigen
- *Grenze:* **die Liste nicht vorwegnehmen.** Die Liste *ist* der Antwortsatz
- *Umfang:* **vier Sätze, längster 14 Wörter**
- *Verworfen:* ein Satz mit 45 Wörtern und fünf Gliedern, der die Liste vorwegnahm

**Direkt darunter — `gebunden`, Positionierungssatz**
> `Sprechen können Sie trotzdem mit uns. Sie müssen nur nicht.`

Fängt das Missverständnis „nicht erreichbar" ab, bevor es entsteht.

**Zwei Listen nebeneinander — vollständig, nicht gekürzt, nicht in Fließtext aufgelöst.**
Spaltenüberschriften `gebunden`: `Vor dem Start` · `Nach dem Start`.

**Der Funktionsumfang ist gebunden, seine Benennung nicht.** Unten steht, *welche* vierzehn
Vorgänge genannt werden — je Zeile ein Vorgang, keiner mehr, keiner weniger.

- *Aufgabe je Eintrag:* den Vorgang aus **Sicht des Kunden** benennen, mit dem Verb voran
- *Grenze:* kein Fachwort, kein Systembegriff (`08_TEXTREGELN.md`). Nichts zusammenfassen, um kürzer zu werden — die **Vollständigkeit ist das Argument**
- *Umfang:* **je höchstens acht Wörter**

| # | Vor dem Start — Vorgang | Nach dem Start — Vorgang |
|---|---|---|
| 1 | Angebot einsehen und annehmen | Öffnungszeiten und Kontaktdaten ändern |
| 2 | Betriebsfragen beantworten, **zeitlich selbst bestimmt** | Bilder tauschen |
| 3 | Logo, Bilder, Unterlagen hochladen | Team- und Projekteinträge pflegen |
| 4 | offene und erledigte Aufgaben sehen | Anfragen der eigenen Website einsehen |
| 5 | fertige Vorschau ansehen | Rechnungen und Laufzeit einsehen |
| 6 | Änderungen sammeln und **in einem Durchgang** schicken | Änderungswünsche stellen |
| 7 | freigeben | Domainstatus einsehen |

> **Achtung:** Die Einträge 2, 3 und 4 der rechten Spalte sind im Bau **gesperrt** —
> `11_KUNDENBEREICH.md`. Sie dürfen nicht als vorhandene Selbstbedienung angekündigt werden.

**Hervorgehobene Unterschiedszeile**
- *Aufgabe:* In Verneinungen zeigen, was wegfällt — nicht, was dafür kommt
- *Grenze:* jedes Glied nennt eine **konkrete Handlung**, die entfällt, kein Eigenschaftswort. Keine vierte Verneinung
- *Umfang:* **genau drei Glieder, je höchstens sieben Wörter**

**Bild:** Ansicht aus dem Kundenbereich, Vermerk `Musteransicht` (gebunden). Solange keine echte
Aufnahme vorliegt: **ehrlich beschrifteter Bildplatz, keine nachgebaute Oberfläche**.

**Gebunden:** Textlink `Den Kundenbereich ansehen` → `/leistung-portal`

---

### Sektion 3 — Ablauf

**H2**
- *Aufgabe:* Die Arbeitsteilung als Versprechen formulieren — die Mitwirkung des Kunden ist klein und begrenzt
- *Grenze:* keine Zahl erfinden; „alles andere" nicht in eine Aufzählung auflösen
- *Umfang:* **zwei Sätze, zusammen höchstens neun Wörter**

**Sechs Schritte** — nummeriert, weil es eine echte Reihenfolge ist

Die **Titel sind `gebunden`** — sie tauchen im Kundenbereich als Statusnamen wieder auf
(`11_KUNDENBEREICH.md`) und dürfen dort und hier nicht auseinanderlaufen. Der **Satz je Schritt
wird geschrieben.**

- *Aufgabe je Satz:* was in diesem Schritt passiert **und wer handelt** — Kunde oder SARTU
- *Grenze:* **genau sechs.** Ein Satz je Schritt. Keine Dauer erfinden — Lieferkorridore stehen in `03_KUNDENPRODUKT.md`. Kein Schritt ohne handelnde Person
- *Umfang:* **je höchstens zwölf Wörter**

| # | Titel (`gebunden`) | was der Satz tragen muss |
|---|---|---|
| 1 | **Bedarfsscheck** | dass wenige Fragen gestellt werden, und welche Felder: Unternehmen, Ziel, Umfang, Domain |
| 2 | **Geprüftes Angebot** | dass Umfang, Preis und Zahlungsplan **schriftlich** kommen |
| 3 | **Ihre Angaben** | dass SARTU vorbelegt, was schon bekannt ist, und den Rest im Kundenbereich abfragt |
| 4 | **Produktion** | dass SARTU baut — und dass **KI hilft, aber ein Mensch prüft und freigibt** |
| 5 | **Vorschau und Freigabe** | dass der Kunde die fertige Seite sieht und Änderungen **sammelt**, nicht einzeln schickt |
| 6 | **Start und Betrieb** | Livegang **und** dass es danach weitergeht |

> Schritt 4 ist die einzige Stelle der Startseite, an der der KI-Einsatz benannt wird. **Er wird
> benannt, nicht umschrieben** — und immer zusammen mit der menschlichen Prüfung. Ohne sie liest
> sich der Festpreis als Automatenpreis.

**Gebunden:** CTA `Ablauf im Detail` → `/ablauf`

---

### Sektion 4 — Preise

**H2**
- *Aufgabe:* Klarmachen, dass der Kunde **nicht** auswählt, sondern eine begründete Empfehlung bekommt
- *Grenze:* nie „Paket wählen", „konfigurieren", „Extras" — `08_TEXTREGELN.md`
- *Umfang:* **zwei Sätze, zusammen höchstens zwölf Wörter**

**Einleitung**
- *Aufgabe:* Die Zuordnungslogik erklären, bevor die Zahlen kommen
- *Grenze:* keine Zahl vorwegnehmen; nicht rechtfertigen
- *Umfang:* **25 Wörter, drei Sätze**

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
- *Umfang:* **zwei Sätze, zusammen höchstens neun Wörter**

**Haltung**
- *Aufgabe:* Warum diese Person das macht, in Alltagssprache
- *Grenze:* **kein Lebenslauf, keine Erfolgsgeschichte, keine Zahlen**
- *Umfang:* **zwei bis drei Sätze**

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
- *Umfang:* **ein Satz, höchstens sechs Wörter**
- *Verworfen:* `Alles, was eine Firmenwebsite braucht` — **wer entscheidet, was sie braucht?**

**Einleitung**
- *Aufgabe:* Klarstellen, dass nichts zusammengestellt und nichts extra bezahlt wird
- *Grenze:* die acht Zeilen nicht vorwegnehmen
- *Umfang:* **höchstens 30 Wörter, zwei Sätze**

**Acht breite Zeilen** — Titel · **ein** Satz · Tags. **Keine Kachelwand, keine Preise.**

**Titel und Tags sind `gebunden`** — sie benennen den Leistungsumfang und müssen mit
`03_KUNDENPRODUKT.md` deckungsgleich bleiben. Der **Satz je Zeile wird geschrieben.**

- *Aufgabe je Satz:* was enthalten ist **und was das für den Kunden bedeutet** — beides, nicht nur das erste
- *Grenze:* **genau acht Zeilen.** Ein Satz, nicht zwei. Keine Fachbegriffe im Satz — die gehören in die Tags. Keine Wirkung versprechen
- *Umfang:* **je höchstens 15 Wörter**

| Titel (`gebunden`) | was der Satz tragen muss | Tags (`gebunden`) |
|---|---|---|
| Strategie und Seitenstruktur | dass **SARTU** den Seitenumfang festlegt — auch nach unten | Sitemap · Nutzerführung · Suchintention |
| Webdesign und Programmierung | individuell programmiert, **ohne WordPress, ohne Baukasten** | kein WordPress · responsive · schnell |
| Website-Texte | dass SARTU schreibt, und **woraus**: Fakten und Stichpunkte des Kunden | aus Stichpunkten · Faktenprüfung |
| **SEO- und GEO-Grundlage** | was ab Tag eins eingebaut ist — **ohne Sichtbarkeit zu versprechen** (siehe 7a) | Titles · Schema · Antwort-zuerst · interne Links |
| Lokale Sichtbarkeit | echte Unternehmensdaten — **und die Abgrenzung** gegen ausgetauschte Stadtnamen | Local SEO · konsistente Daten |
| Domain und Launch | prüfen, verbinden, live schalten — **und dass die bestehende E-Mail erreichbar bleibt** | DNS · E-Mail-Schutz · Weiterleitungen |
| Kundenbereich und Freigaben | dass alles an **einem** Ort läuft statt in E-Mail-Ketten | Briefing · Feedback · Pflege |
| Rundum-Schutz | dass der Betrieb **danach** weiterläuft, und woraus er besteht | Betrieb · Backups · Monitoring |

**Gebunden:** CTA `Alle Leistungen im Überblick` → `/leistungen`

> **Offen:** Der Rundum-Schutz-Satz nennt die **Monatspauschale nicht**, steht aber unter „Im
> Preis enthalten". Vorschlag: „… über die Monatspauschale" ergänzen. Siehe `OFFENE_PRUEFUNGEN.md`.

---

### Sektion 7a — Sichtbarkeit

**H2**
- *Aufgabe:* Sagen, dass die Grundlage ab Tag eins steht — **ohne** Sichtbarkeit zu versprechen
- *Grenze:* **nie** „auffindbar", „garantiert", „Platz 1". Die Aufnahme in den Index liegt nicht bei uns
- *Umfang:* **ein Satz, höchstens elf Wörter**
- *Verworfen:* `Ihre Website ist ab dem ersten Tag auffindbar.` — **überzieht**

**Text**
- *Aufgabe:* Nennen, was konkret eingebaut ist
- *Grenze:* keine Wirkung behaupten, keine Zeitangabe zum Ranking
- *Umfang:* **42 Wörter**

**Drei Spaltenüberschriften** — die Blickwinkel sind gebunden, ihre Benennung nicht
- *Aufgabe:* Dieselbe Grundlage aus drei Blickwinkeln zeigen. **`gebunden`, in dieser Reihenfolge:** Menschen · Suchmaschinen · KI-Antworten
- *Grenze:* je ein Verb, das eine **Leistung des Lesers oder der Maschine** benennt, keine Eigenschaft
- *Umfang:* **je zwei Wörter**

**Gebunden — Pflichthinweis:**
> Rankings, Anfragen oder Nennungen in KI-Systemen kann niemand garantieren. Wir bauen das
> Fundament und halten die technische Suchgesundheit im Betrieb im Blick.

---

### Sektion 8 — Musterprojekte

**Der Ersatz für fehlende Referenzen** — und der einzige, der ohne Kunden funktioniert.

**H2**
- *Aufgabe:* Die fehlenden Referenzen offen benennen, statt sie zu umschreiben
- *Grenze:* **kein Konjunktiv.** Eine Zahl nennen, auch wenn sie null ist
- *Umfang:* **zwei Sätze, zusammen höchstens acht Wörter**
- *Verworfen:* jede `könnte`-Fassung — Konjunktiv ist keine Aussage

**Einleitung** — einmal über dem Raster
- *Aufgabe:* Gründungsjahr und Anzahl nennen und klarstellen, dass es Muster sind
- *Grenze:* sich nicht selbst erklären; keine drei Glieder ohne Information
- *Umfang:* **zwei Sätze, höchstens 14 Wörter**
- *Zahlen `gebunden`:* Gründung **2026** · **drei** Beispiele

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

**Frage und Antwort werden beide geschrieben.** Gebunden ist, **welcher Einwand** wo steht.

- *Aufgabe je Frage:* Den Einwand so stellen, wie ein Betriebsinhaber ihn stellen würde — nicht, wie eine Agentur ihn gern hätte
- *Grenze je Frage:* **als vollständige Frage überschreiben, nicht als Stichwort.** Google zerlegt die ganze Frage, nicht das Suchwort daraus — `16_SEO_GEO_SARTU.md`. Kein „Wie funktioniert …"-Ersatz für einen echten Zweifel
- *Aufgabe je Antwort:* Den Einwand **im ersten Satz** entkräften, dann belegen
- *Grenze je Antwort:* höchstens vier Sätze. Keine Zahl ohne Fundstelle. Nichts versprechen, was `11_KUNDENBEREICH.md` als gesperrt führt
- *Reihenfolge `gebunden`:* **Der Reichweiten-Einwand steht an erster Stelle.** Er entscheidet, ob der Leser sich überhaupt angesprochen fühlt — vorher stand die Reichweite nirgends auf der Seite

| # | Einwand (`gebunden`) | Kern der Antwort |
|---|---|---|
| 1 | Reichweite über Sachsen hinaus | Ja, bundesweit. Weil es keine Termine gibt, spielt Entfernung keine Rolle |
| 2 | Der Kunde soll selbst ein Paket aussuchen | Nein. Wir empfehlen einen Umfang und begründen ihn — auch nach unten |
| 3 | Wer die Texte schreibt | SARTU, aus Fakten und Stichpunkten. Keine erfundenen Belege |
| 4 | Keine Liste mit Zusatzoptionen | Zusatzlisten machen den Preis unklar. Neue Ziele → eigenes Festpreisangebot |
| 5 | Domain und bestehende E-Mail-Adressen | Die Domain gehört dem Kunden. Einträge werden vor jeder Änderung gesichert |
| 6 | Spätere Änderungen durch den Kunden | Öffnungszeiten und Kontaktdaten selbst. Texte, Bilder, Struktur ändert SARTU — im Betrieb enthalten |

> **Korrigiert am 01.08.2026:** Die alte Antwort auf Frage 6 versprach zusätzlich Team- und
> Projekteinträge sowie Bildertausch als Selbstbedienung. **Diese Funktionen sind gesperrt.**

---

### Sektion 10 — Bedarfsscheck-Einstieg

**H2**
- *Aufgabe:* Die Frage stellen, die der Bedarfsscheck beantwortet — als Einladung, nicht als Aufforderung
- *Grenze:* kein Ausrufezeichen, kein „Jetzt"
- *Umfang:* **eine Frage, höchstens sieben Wörter**

**Text**
- *Aufgabe:* Aufwand, Ergebnis und Unverbindlichkeit nennen
- *Grenze:* keine Dauer erfinden, die nicht belegt ist
- *Umfang:* **36 Wörter**

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
