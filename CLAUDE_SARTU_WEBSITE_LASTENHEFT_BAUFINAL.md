# SARTU – Website-Lastenheft (baufinal)

**Stand:** 24.07.2026 · **Zweck:** Umsetzungsreifes Briefing für die **eigene SARTU-Website**. Wer dieses Dokument hat, kann bauen — ohne Rückfragen zu Texten, Struktur, Feldern oder Verhalten.

**Gilt zusammen mit:**
- `archiv/CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md` – Architektur, Seitenkonzepte, Begründungen
- `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` – Geschäftsmodell, Preise, Portal, Recht
- `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` – wie die visuelle Ebene recherchiert und ausgewählt wird

> **Es gibt keine vorgegebene Designrichtung.** Frühere Entwürfe unter `design/_verworfen/` sind
> **verworfen** und weder Vorgabe noch Anregung. Farben, Schriften und Formen entstehen ausschließlich
> über das Design-Briefing.

---

## 0. Standortneutral baufertig — mit zwei Gates

| # | Entscheidung | Status |
|---|---|---|
| 1 | **Designrichtung** | **offen — wird recherchiert**, nicht vorgegeben. Vorgehen: `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`. Struktur, Copy und Anforderungen in diesem Dokument sind davon unabhängig und vollständig. |
| 2 | **Startregion / Standort** | ⏸ **offen** — siehe `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1. Blockiert **nicht** den Bau, blockiert die lokale Ebene |

**Was das heißt:** Alle Seiten, Texte, Felder und Abnahmekriterien in diesem Dokument sind vollständig und **standortneutral umsetzbar**. Gesperrt sind nur zwei Dinge:

| Gate | Blockiert | Blockiert **nicht** |
|---|---|---|
| **Designrichtung** | den Vollausbau über die 2–3 Startseitenvarianten hinaus | Struktur, Copy, Bedarfsscheck, SEO-Grundlage |
| **Standort** | die lokale Ebene: Ortsseiten, `LocalBusiness`, Unternehmensprofil, Ortsnamen in Titeln | den gesamten übrigen Bau |

**Nicht baufrei ist der lokale Launch** — und mit ihm der schnellste Kundenkanal überhaupt
(`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §0.4). Die Form des Unternehmensprofils hängt an der Art der
Adresse: echtes Büro → sichtbare Adresse, reine Postadresse → Service-Area ohne sichtbare Adresse
(Masterkonzept §23a.1).

**Zu 2 (offen):** Die Startregion ist **nicht entschieden**. Werte und Sperren stehen in
`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1.

**Was das bedeutet — Sperren, solange `[GESCHAEFTSADRESSE_STATUS]` auf `offen` steht:**
- **Kein** `LocalBusiness` in strukturierten Daten. Stattdessen `Organization` **ohne** Adressfeld
- **Kein** Google-Unternehmensprofil, auch nicht vorbereitend
- **Keine** Ortsseiten in der produktiven Veröffentlichung, auch nicht unverlinkt
- **Keine** Ortsnamen in Title, H1, Meta-Description, URL oder Fließtext
- **Keine** NAP-Aussage — es gibt noch keine Anschrift

**Was trotzdem vollständig gebaut wird:** alles andere. Startseite, Preise, Bedarfsscheck, Ablauf,
fünf Leistungsseiten, Ratgeber, Lexikon, Kundenbereich, SEO-Grundlage. Die lokale Ebene ist eine
spätere Ergänzung, kein Fundament — deshalb ist dieses Lastenheft **standortneutral baufertig**.

**Sprachregelung bis zur Entscheidung:** überregional formulieren. Zulässig sind Aussagen über die
Arbeitsweise („persönlich erreichbar", „auf Wunsch beim Kunden vor Ort"), **nicht** über Orte.

**Terminregel für alle Texte:** Standard ist **kein Termin**. Auf Wunsch Video oder beim Kunden vor
Ort. Auf der Website wird **kein Besuchstermin beworben** — eine Adresse im Impressum ist keine
Einladung.

---

## 1. Technischer Rahmen

> **SARTU ist ein Projekt, nicht zwei.** Öffentliche Website, Kundenbereich (`/portal/`),
> Adminbereich (`/admin/`) und Serverfunktionen (`/api/`) liegen in **einem** modularen PHP-Projekt
> unter **einer** Domain. Die verbindliche Architektur, Verzeichnisstruktur und
> Hosting-Anforderung stehen in `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` §1 — dieser Abschnitt
> ergänzt nur, was die **öffentlichen Seiten** betrifft.

- **PHP 8.3+, serverseitig gerendert.** Kein CMS, kein Vollframework, kein SPA-Framework, kein Node als Zielsystem, kein Build-Schritt fürs Frontend
- **Öffentliche Seiten sind cachebar:** Sie hängen nicht an einer Sitzung und dürfen als statische Antwort ausgeliefert werden (Server- oder Dateicache). Wo Inhalte fest sind, dürfen sie beim Ausrollen als HTML vorgeneriert werden
- **Kein** externes CDN für Schriften, CSS oder JS. Schriften selbst gehostet als WOFF2, `font-display: swap`
- **JavaScript budgetiert statt verboten:** **≤ 75 KB gzip auf der Startseite, ≤ 40 KB auf Unterseiten.** Pflichtfunktionen: mobile Navigation, FAQ-Akkordeon, Komfort im Bedarfsscheck. Darüber hinaus **maximal zwei bewusste Markenmomente pro Seite**
- **Ohne JavaScript vollständig nutzbar:** Inhalte lesbar, Links funktionieren, **beide Formulare absendbar**, Bedarfsscheck vollständig durchlaufbar (§9.5a). **Kein Inhalt darf erst durch eine Scroll-Animation sichtbar werden**
- **`prefers-reduced-motion: reduce` ist Pflicht:** alle nicht-essenziellen Bewegungen aus, Inhalte sofort sichtbar
- **Bibliotheken:** CSS zuerst. Erlaubt bei Bedarf: Lenis (~3 KB), GSAP + ScrollTrigger (~34 KB, seit 04/2025 vollständig kostenlos inkl. SplitText/MorphSVG). **Nicht erlaubt:** Vanta.js, Three.js als Deko, Barba.js
- **Ziele (Labormessung):** LCP < 2,5 s · TBT < 200 ms · CLS < 0,1 · mobil zuerst entwickelt. **Animationen dürfen CLS nicht verschlechtern**
- **`html lang="de"`**, semantische Landmarks (`header`, `nav`, `main`, `footer`), sichtbarer Fokus, Skip-Link „Zum Inhalt springen"
- **Breakpoints:** ≤ 599 px mobil · 600–1023 px Tablet · ≥ 1024 px Desktop. Maximale Inhaltsbreite 1280 px, Fließtext max. 68 Zeichen
- **Designsystem:** Farben, Schriften und Radien werden nach `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` recherchiert und vorgelegt. Verbindlich ist nur: **alle Werte als zentrale Variablen**, Komponenten nutzen **nie** rohe Farbwerte, Radius **einheitlich**, keine „Karten in Karten"
- **Wiederverwendung ist Pflicht:** Layouts, Partials und Komponenten aus `/app/views` werden von öffentlichen Seiten und Kundenbereich **gemeinsam** genutzt. Kein kopiertes Markup zwischen Seiten

> **Was „keine externen Verbindungen" bedeutet — und was nicht.**
> **Verboten sind Fremdanbieter zur Laufzeit:** Schrift-, Skript- und Stil-CDNs, Analyse- und Tracking-Dienste, eingebettete Karten, Videoportale, Chat-Widgets, Werbe- und Rätselbild-Dienste, externe Bildhoster. Kein Netzwerkaufruf des Browsers darf eine fremde Domain treffen.
> **Kein Sonderfall mehr sind die Formulare:** Sie laufen im selben Programm. Es gibt **kein** gemeinsames Geheimnis und **keinen** Aufruf über das Netz (Portal-Lastenheft §4b.1).

## 2. Globale Sprach- und Inhaltsregeln

> **Drei Ebenen regeln den Text, und sie greifen ineinander:**
>
> | Datei | Regelt |
> |---|---|
> | **dieses §2** | welche **Behauptungen** verboten sind — Rankinggarantie, „wartungsfrei", „rechtssicher" |
> | `SARTU_TEXTREGELN.md` | die **Form** — Satzlänge, Aufzählungen, Wortlisten, Prüfbericht |
> | `.claude/skills/sartu-texter/SKILL.md` | **wie geschrieben wird** — Briefing, Bauformen, Kalibrierung. **Er schreibt den Wortlaut** |
>
> Alle drei gelten. Ohne ausgefüllten Prüfbericht gilt eine Seite als nicht abgegeben.

**Ansprache:** durchgängig **„Sie"**. Marke immer **`SARTU`** (Versalien), auch im Fließtext.

**Verbotene Wörter und Aussagen** (gelten für alle Seiten, prüfbar per Suche):

| Verboten | Grund | Stattdessen |
|---|---|---|
| „wartungsarm", „wartungsfrei", „kaum Wartung" | entwertet den Rundum-Schutz | „keine Wartung **für Sie**" |
| „rechtssicher", „abmahnsicher", „DSGVO-konform" (absolut) | Rechtsberatungs-/Haftungsrisiko | „datensparsam umgesetzt", „Rechtstexte technisch eingebunden" |
| „garantiert Platz 1", „garantierte Sichtbarkeit", „garantierte KI-Nennung" | unhaltbar | „Fundament für Sichtbarkeit, ohne Rankinggarantie" |
| „spart 80 % Zeit" o. ä. Prozentwerte | keine eigenen Daten | qualitativ formulieren (§5, Abschnitt „Ablauf") |
| „Paket wählen", „konfigurieren", „Extras hinzufügen", „SEO buchen" | widerspricht Angebotslogik | „Bedarf prüfen lassen", „einschätzen lassen" |
| „günstig", „billig", „Schnäppchen" | falsche Positionierung | „Festpreis", „klarer Gesamtpreis" |
| „unser Team" (solange Einzelperson) | Ehrlichkeit | „gründergeführt", „ich" / „wir" nur wenn zutreffend |

**Pflichthinweis bei jeder Preisnennung:**
> Alle Preise netto zzgl. gesetzlicher Umsatzsteuer. Ausschließlich für Unternehmer.

**Keine** Fake-Referenzen, -Bewertungen, -Logos, -Adressen, -Teamfotos. Portal-Screens tragen den Vermerk **„Musteransicht"**, solange sie kein freigegebenes echtes Kundenprojekt zeigen.

---

## 2a. Visuelle Ebene — nicht in diesem Dokument festgelegt

> **Farbwelt, Schriften, Radien, Textur, Logo und Bewegungsdetails sind hier bewusst NICHT vorgegeben.**
> Die ausführende KI stellt sie nach **`CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`** aus echten Bibliotheken und Referenzen zusammen und legt **2–3 Vorschläge** zur Entscheidung vor. Erst danach wird gebaut.

Frühere Designentwürfe unter `design/_verworfen/` sind **nicht** zu verwenden — auch nicht als Anregung.

**Was dieses Lastenheft weiterhin verbindlich vorgibt** (Anforderungen, keine Gestaltung):

- **Struktur und Inhalt** jeder Seite: Sektionsreihenfolge, Copy, Überschriften, Feldlabels, Fehlermeldungen (§3 ff.)
- **Leistung:** ≤ 75 KB gzip JS Startseite, ≤ 40 KB Unterseiten; **vor Livegang im Labor** LCP < 2,5 s · TBT < 200 ms · CLS < 0,1. **Echtes INP** ist ein Felddatenwert und wird erst nach dem Livegang gemessen (§17a)
- **Barrierefreiheit:** Kontrast ≥ 4,5:1, sichtbarer Fokus, volle Tastaturbedienung, Skip-Link, `prefers-reduced-motion` wirksam, Zustände nie allein über Farbe
- **Ohne JavaScript nutzbar** — kein Inhalt erscheint erst durch eine Scroll-Animation
- **Bewegung budgetiert:** höchstens zwei bewusste Markenmomente pro Seite, keine Animation über Text oder CTA
- **Lizenzpflicht:** jedes eingesetzte Teil muss kommerzielle Nutzung **und** Weitergabe im Kundenprojekt erlauben (SARTU verkauft Websites weiter)
- **Keine externen Verbindungen zur Laufzeit** — Schriften, Icons, Skripte selbst gehostet
- **Positionierungsschutz:** die Seite darf nicht erkennbar aus einem Template stammen; keine Farbverläufe, Leuchtflächen, schwebenden Dashboard-Karten, Karten in Karten, Emoji-Marker, Handschlag-Stockfotos, generischen WebGL-Hintergründe
- **Inhaltliche Wahrheit:** keine erfundenen Logos, Bewertungen, Referenzen oder Kundenzahlen; Portal-Ansichten aus echter Oberfläche, gekennzeichnet als „Musteransicht"


## 3. Header-Navigation

> **⚠️ ABGELÖST — die Punkteliste unten gilt nicht mehr.** Verbindlich ist **§5b**. Dieser Abschnitt
> behält nur noch **Verhalten und Maße** — Höhe, Sticky-Zustand, Overlay-Bedienung.
>
> **Was hier falsch war:** Die Liste unten nannte `Leistungen · Preise · Ablauf · Ratgeber · Lexikon`
> und schloss `Über uns` ausdrücklich aus. §5b nennt eine andere Liste und nimmt `Über uns` auf.
> **Beide standen als „final" im selben Dokument.** Der Befund kam von außen und war richtig — mit
> zwei verbindlichen Navigationen baut jeder Entwickler die, die er zuerst liest.
>
> **Ersetze beim Lesen jede Punkteliste unten durch die aus §5b.** Das gilt auch für die
> Reihenfolge im Mobil-Overlay.

**Desktop (≥ 1024 px):**
`[SARTU-Wortmarke]` — links · Punkte nach **§5b** — Mitte · `Kontakt` (Textlink) + **`Bedarf prüfen lassen`** (Button) — rechts

- Höhe 72–80 px. Beim Scrollen kompakt sticky (Höhe 56–60 px), Hintergrund deckend, dünne Trennlinie unten.
- Aktiver Menüpunkt wird markiert (Unterstrich oder Farbe, keine Fettung-Verschiebung).
- ~~**`Über uns`** ist bewusst **nicht** in der Hauptnavigation.~~ **Abgelöst.** §5b nimmt `Über uns` auf. Grund für die Änderung: Ohne Referenzen trägt die Person hinter SARTU den Vertrauensaufbau; sie darf nicht nur im Fußbereich stehen.

**Mobil (≤ 1023 px):**
- Links Wortmarke, rechts Menü-Icon (44 × 44 px Trefferfläche).
- Menü öffnet als **Vollbild-Overlay**, nicht als Dropdown. Reihenfolge: **die sechs Punkte aus §5b**, danach `Kontakt`, dann großflächiger CTA `Bedarf prüfen lassen`.
- Schließen per X, `Esc` und Klick außerhalb. Fokus wird im Overlay gehalten, beim Schließen zurück auf das Menü-Icon.
- **Kein** sticky Bottom-CTA-Balken (verdeckt Inhalte, wirkt aufdringlich).

---

## 4. Footer (final)

Fünf Spalten auf Desktop, gestapelt auf Mobil (Reihenfolge wie unten).

| Spalte | Inhalt |
|---|---|
| **1 – Marke** | SARTU-Wortmarke · Kurzpositionierung: „Individuell programmierte Firmenwebsites zum Festpreis. Geplant, geschrieben, programmiert und betrieben." |
| **2 – Leistungen** | Webdesign · Website-Texte · Sichtbarkeit (SEO/GEO) · Rundum-Schutz · Portal |
| **3 – Wissen** | Ratgeber · Lexikon |
| **4 – Unternehmen** | Ablauf · Preise · Über uns · Kontakt |
| **5 – Rechtliches** | Impressum · Datenschutz *(AGB nur, wenn anwaltlich final)* |

**Fußzeile darunter:** `© 2026 SARTU` · `Alle Preise netto zzgl. USt. · Ausschließlich für Unternehmer`

**Verboten im Footer:** Ortslisten, Keyword-Linklisten, Social-Icons ohne echte gepflegte Profile, „Made with"-Hinweise.

---

## 5. Startseite `/`

> **Zwei Regeln für diese Seite, die über allem stehen:**
>
> **1. Die Startseite muss allein verkaufen.** Ein Unternehmer trifft seine Kaufentscheidung auf
> dieser Seite, ohne Gespräch. Jede Überschrift wird deshalb aus **seiner** Sicht gelesen, nicht aus
> unserer. Formulierungen, die intern richtig sind (Umfangsschutz, Zuständigkeitsgrenzen, Abgrenzung
> zum Wettbewerb), sind nach außen oft das Gegenteil dessen, was sie sagen sollen. Der Test: *Liest
> sich das wie ein Versprechen an mich — oder wie eine Regel, die für mich gilt?*
>
> **2. Nur die hier aufgeführten Sektionen.** Keine Abschnitte aus anderen Seiten übernehmen. Der
> Satz „Veröffentlicht wird nur, was wir geprüft und freigegeben haben." gehört zu `/ueber-uns`
> (§11) und hat auf der Startseite **nichts zu suchen** — er beantwortet eine Frage, die dort noch
> niemand gestellt hat.
>
> **3. `SARTU_TEXTREGELN.md` gilt für jeden Wortlaut auf dieser Seite.** Zehn zählbare Regeln, dazu
> ein Prüfbericht mit Zahlen. **Ohne ausgefüllten Prüfbericht gilt die Seite als nicht abgegeben.**

> **4. Die Texte unten sind ab 01.08.2026 überwiegend Referenz, nicht Vorschrift.**
>
> Geschrieben wird mit `.claude/skills/sartu-texter/SKILL.md`. Dort steht, **wie** formuliert wird —
> Briefing, Bauformen je Abschnitt, Kalibrierung, Prüfbericht. Dieses Lastenheft gibt die **Aussage**
> vor, der Skill den **Wortlaut**.
>
> **Drei Klassen, im Skill vollständig beschrieben:**
>
> | Klasse | Was | Hier unten erkennbar an |
> |---|---|---|
> | **1 — gebunden** | Zahlen · vertragliche Erklärungen · Pflichthinweise · wiederkehrende Beschriftungen · die vier Positionierungssätze | ausdrücklich als **gebunden** oder **Pflichtzeile** gekennzeichnet |
> | **2 — Aussage gebunden, Wortlaut frei** | fast aller Fließtext unten | alles Übrige |
> | **3 — frei** | Branchenseiten (§10a), Ratgeber, Lexikon | dort steht nur das Ziel |
>
> **Die Texte unten sind KEIN Qualitätsmaßstab.** Diese Einschätzung stand hier einen Tag lang und
> war falsch. Gemessen am Nachprüftest des Skills fallen **fünf von neun Überschriften durch** —
> jede enthält ein unbestimmtes Wort an der Stelle, an der eine Zahl oder eine Grenze stehen müsste.
>
> **Der Inhalt war fast immer richtig.** Falsch war nur die Formulierung.
>
> **Was sie sind:** die Aussage, die getroffen werden muss. **Nicht der Wortlaut, in dem das
> geschieht.** Wer sie abschreibt, übernimmt den Fehler.
>
> **Was das nicht ändert:** Die Aussage bleibt. Wer aus „ohne einen einzigen Termin" ein „mit
> flexiblen Abstimmungsformaten" macht, hat nicht umformuliert, sondern das Produkt geändert.
>
> **Und: Freigegeben ist freigegeben.** Ein einmal abgenommener Text wird nicht bei jedem Durchlauf
> neu erfunden — sonst hat das Projekt bei jedem Bau eine andere Website. Der ausgelieferte Code
> ist der Stand.

**Title (54 Z.):** `Firmenwebsite zum Festpreis für regionale Betriebe | SARTU`

> **Geändert am 30.07.2026.** Vorher: `Firmenwebsite zum Festpreis, ohne WordPress | SARTU`. Das
> verstieß gegen die eigene Positionierung — `archiv/CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md` sagt
> ausdrücklich: *„Nicht ‚ohne WordPress' plakatieren; Entlastung verkaufen."* Ein Meta-Titel ist die
> plakativste Stelle, die es gibt. Dazu kam: Das Abgrenzungsmerkmal stand im Titel, die Zielgruppe
> nicht.
>
> **`ohne WordPress` bleibt** auf `/leistung-webdesign` und im zugehörigen Ratgeberartikel. Dort
> beantwortet es eine echte Suchanfrage, statt die Startseite auf einen Wettbewerbsvergleich zu
> verengen.
**Meta Description (148 Z.):** `Firmenwebsite zum Festpreis, bundesweit und ohne einen einzigen Termin. Geplant, geschrieben, programmiert und betrieben von SARTU. Ab 1.490 € netto.`

> **Geändert am 01.08.2026.** Vorher stand dort *„Geführtes Portal statt E-Mail-Chaos, SEO-Basis ab
> Launch, kein WordPress."* Drei Probleme: `kein WordPress` verstößt gegen die eigene Positionierung
> (Marktanalyse: nicht plakatieren), die **Reichweite** fehlte, und es stand **keine Zahl** darin.
> Die neue Fassung nennt beides — und der Preis filtert vor dem Klick.
**H1:** `Individuell programmierte Firmenwebsites zum Festpreis.`
**Zielumfang:** 750–950 Wörter · **Schema:** `Organization`, `WebSite` · `FAQPage` optional (bringt keine Rich Results mehr, s. §16)

### Reihenfolge der Sektionen — verbindlich

| # | Sektion | Aufgabe | Bauform |
|---|---|---|---|
| 1 | Aufmacher | Was, für wen, zu welchem Preis | breit, mit Bewegung |
| 2 | Der Kundenbereich | „Was unterscheidet euch?" | bildgeführt |
| 3 | Ablauf | „Wie läuft das?" | Zeitstrahl |
| 4 | Preise | „Was kostet es?" — **und der Belegersatz** | Stufen, eine dunkel |
| 5 | Die Zusage | Luft holen | **randlos dunkel, ein Satz** |
| 6 | **Wer dahintersteckt** | „Wem vertraue ich hier?" | Porträt |
| 7 | Leistungen | „Ist alles dabei?" | Zeilen — die einzige Liste |
| 8 | Musterprojekte | „Was kommt dabei heraus?" | Karten |
| 9 | Häufige Fragen | die letzten Einwände | Akkordeon |
| 10 | Bedarfsscheck | die Handlung — **zuletzt** | dunkel |

> **Die Spalte „Bauform" ist verbindlich.** Eine frühere Fassung bestand von oben bis unten aus
> demselben Zeilenmuster — gleicher Hintergrund, gleiche Breite, gleiche Dichte. Jede einzelne
> Entscheidung war richtig, das Ergebnis war eine Seite ohne einen einzigen Gangwechsel.
> **Kein Aufbaumuster kommt mehr als zweimal vor** (Design-Briefing §3.7).

> **Warum diese Reihenfolge.** Der Aufmacher beantwortet „welches Problem, welches Produkt, für
> wen". Dann kommt das **Unterscheidungsmerkmal** (2), dann **Vorhersehbarkeit** (3) — beides
> erhöht die Zahlungsbereitschaft, bevor die Zahl fällt. Erst danach der Preis.
>
> **Der vollständige Preisblock bleibt auf der Startseite**, nicht nur ein Teaser. Bei SARTU ist
> der veröffentlichte Preis der **Belegersatz** für fehlende Referenzen — hinter einem Klick
> verschenkt er genau das Unterscheidungsmerkmal, das er tragen soll.
>
> **Der Bedarfsscheck steht ganz unten.** Ein Formular weiter oben verlangt eine Entscheidung,
> bevor ein Grund dafür geliefert wurde. Der Einstieg oben ist der Knopf im Aufmacher.

> **Zur Begründung „weniger Auswahl verkauft besser":** Sie trägt **nicht**. Die vielzitierte
> Marmeladenstudie (Iyengar & Lepper 2000) ist in einer Meta-Analyse über 63 Bedingungen und rund
> 5.000 Teilnehmer auf eine mittlere Effektstärke von **praktisch null** zusammengeschrumpft
> (Scheibehenne, Greifeneder & Todd 2010). Der richtige Grund ist ein anderer und stärker:
> **Der Kunde kann die Optionen gar nicht bewerten.** Er weiß nicht, ob er acht oder sechzehn
> Seiten braucht. Das ist fehlendes Fachwissen, nicht Entscheidungsstress — und deshalb ist
> „wir empfehlen eine Lösung" richtig.

---

## 5aa — Sprachregel für alle Kundentexte

**Die häufigste Ursache unverständlicher Abschnitte auf dieser Seite ist immer dieselbe:
Fachwörter aus unserer Arbeitsweise landen im Verkaufstext.**

| Wir sagen intern | Der Leser versteht | Auf der Seite steht |
|---|---|---|
| Briefing | nichts | *Sie beantworten die Fragen zu Ihrem Betrieb* |
| Onboarding | nichts | *Wir tragen ein, was wir schon wissen* |
| Freigabe | ungefähr | *Sie schauen sich die Vorschau an und sagen Ja* |
| Produktion | „Fabrik?" | *Wir bauen die Website* |
| Cockpit | nichts | *Ihre Übersicht* |
| Add-on | nichts | *später dazubuchen* |

**Drei Regeln, die für jeden Text auf der öffentlichen Website gelten:**

1. **Verben statt Substantive.** `Sie laden Unterlagen hoch` statt `Upload von Unterlagen`. Was der Leser **tut** oder **sieht**, nicht wie der Arbeitsschritt heißt
2. **Kein Bild ohne Aussage.** `Ihr Projekt bleibt an einem Ort` klingt gut und sagt nichts — welcher Ort, und was habe ich davon? Bilder ersetzen keine Information
3. **Der Unterschied wird ausgesprochen, nicht angedeutet.** Wenn etwas besser ist als das Übliche, muss dastehen, was das Übliche ist — beschrieben als Erfahrung des Lesers (`kein Suchen in alten E-Mails`), nicht als Behauptung über Wettbewerber

**Prüfung vor jeder Abgabe:** Jede Überschrift und jeder Absatz laut lesen und fragen —
*„Würde ein Malermeister nach diesem Satz sagen können, worum es geht und was er davon hat?"*
Wenn nein, ist der Satz nicht fertig, egal wie gut er klingt.

> **Ausnahme:** `Bedarfsscheck` ist ein bewusst geprägter eigener Begriff und bleibt. Er ist aus
> sich heraus verständlich und wird an jeder Verwendungsstelle durch den umgebenden Satz erklärt.

---

## 5b — Hauptnavigation

> **Dies ist die einzige gültige Navigation.** §3 ist für die Punkteliste abgelöst und behält nur
> Verhalten und Maße. Gilt für Desktop **und** Mobil-Overlay.

`Leistungen` · `Preise` · `Ablauf` · **`Kundenbereich`** · `Über uns` · `Fragen`

> **`Ergebnis` ist gestrichen und durch `Leistungen` ersetzt.** Die Beschriftungsregel drei Absätze
> weiter unten verlangt selbst: *„im Zweifel die konkretere Bezeichnung wählen, nicht die
> elegantere."* `Ergebnis` war die elegantere. Der Absatz widersprach sich in sich.
>
> **`Portal` ist gestrichen und durch `Kundenbereich` ersetzt** (30.07.2026). Drei Gründe:
> 1. Das höher stehende Portal-Lastenheft führt `Portal` unter **„nach außen nie verwenden"**
> 2. Die Startseite selbst schreibt **zehnmal** `Kundenbereich` und nur dreimal `Portal`. Die
>    Navigation widersprach dem Text, auf den sie zeigt
> 3. `Kundenbereich` sagt einem Malermeister, was dahinterliegt. `Portal` nicht
>
> **Wird die Zeile dadurch zu breit, greift das Mobilmenü früher** — der verständlichere Begriff
> wird nicht für sechs Pixel geopfert. Der Befund kam von außen und war richtig.
>
> `Leistungen` bleibt damit in der Hauptnavigation statt im Fußbereich — es ist die Seite, die auf
> „webdesign" und „firmenwebsite erstellen lassen" antwortet. `Ratgeber` und `Lexikon` wandern in
> den Fußbereich und bekommen starke interne Links aus den Leistungsseiten.
>
> **Die Beschriftungen sind eine Geschäftsentscheidung, kein Bauwert.** Wer sie ändern will, ändert
> sie hier — an einer Stelle.

**`Portal` gehört zwingend in die sichtbare Navigation.** Es ist das stärkste
Unterscheidungsmerkmal gegenüber jedem Wettbewerber — es aus der Hauptnavigation zu lassen,
verschenkt den Vorteil. In einer früheren Fassung fehlte es.

`Leistungen` bleibt als Seite bestehen (Suchmaschinenrelevanz), wandert aber in den Fußbereich,
damit die Hauptnavigation nicht überläuft.

**Beschriftungsregel:** Jeder Punkt muss verraten, was dahinter liegt. Wer `Ergebnis` nicht sofort
versteht, klickt nicht — im Zweifel die konkretere Bezeichnung wählen, nicht die elegantere.

### 5a — Statusanzeige (Auftragslage)

Zeigt, ob SARTU gerade Aufträge annehmen kann. **Der Wert wird im internen Bereich gepflegt** und
nie im Quelltext erfunden.

| Zustand | Darstellung | Knopfbeschriftung |
|---|---|---|
| `offen` | leise Zeile **unter** dem Knopf: gefüllter Punkt + `Freie Kapazitäten` | `Bedarf prüfen lassen` |
| `knapp` | dieselbe Stelle, halb gefüllter Punkt + `Nur noch wenige Plätze` | `Bedarf prüfen lassen` |
| `ausgebucht` | **über** dem Knopf, abgesetzte Fläche mit Rand: leerer Punkt mit Ring + `Zurzeit ausgebucht — Warteliste möglich` | `Auf die Warteliste` |
| *nicht gesetzt* | **nichts wird angezeigt** | `Bedarf prüfen lassen` |

**Wo sie erscheint:** in der Aufmacher-Karte beim Hauptknopf und in Sektion 10 beim Abschluss.
**Sonst nirgends** — insbesondere **kein Vollbreiten-Streifen über der Navigation**. Ein Balken
über dem Logo ist das Format von Cookie- und Werbehinweisen; Besucher überspringen diese Zone, und
eine Betriebsinformation gehört nicht über die Marke.

**Regeln:**

- Der Zustand wird **nie allein über Farbe** unterschieden, sondern über die Füllung des Punktes plus den Text (§2a, Barrierefreiheit)
- Der Punkt ist lime **mit dünnem Ring in `--ink`** — Lime allein erreicht gegen die helle Fläche nur 1,30:1 und wäre als Grafik unsichtbar
- Bei `offen` und `knapp` bleibt die Zeile klein und ruhig. Sie beruhigt, sie wirbt nicht
- Nur bei `ausgebucht` bekommt sie Gewicht und **ändert die Handlung** — eine Anfrage wäre dann eine Sackgasse
- **Kein Pulsieren, kein Blinken, kein Countdown.** Ein pulsierender Punkt behauptet Echtzeitüberwachung; dieser Wert ändert sich vielleicht monatlich
- **Keine Zahlen, keine Termine.** Weder „3 Plätze frei" noch „ab Q3" — beides wäre eine ungeprüfte Zusage
- `Nur noch wenige Plätze` wird **nur gesetzt, wenn es zutrifft**. Als Dauerzustand ist es erfundene Knappheit und damit dieselbe Kategorie wie eine erfundene Referenz

**Gegenstelle:** Der Zustand wird im internen Bereich gesetzt und erzeugt wie jede andere Änderung
einen Audit-Eintrag (Portal-Lastenheft).

---

### Sektion 1 — Aufmacher

- **Eyebrow:** `Webdesign-Agentur für Firmenwebsites`
- **H1:** `Individuell programmierte Firmenwebsites zum Festpreis.`
- **Lead (22 W., drei Sätze, längster 10):**
  > Sie erzählen uns, was Ihr Betrieb macht und für wen. Den Rest bauen und betreiben wir. Dafür ist kein einziger Termin nötig.

  > **Alte Fassung, nicht wieder verwenden:** *„SARTU plant, textet, programmiert und betreibt Ihre
  > Website. Sie beantworten nur die Fragen zu Ihrem Unternehmen — Struktur, Design, Technik und die
  > SEO-Grundlage übernehmen wir und verantworten das Ergebnis."* 38 Wörter, zwei Vierer-Aufzählungen,
  > und die wichtigste Aussage der Firma fehlte: **ohne Termin.**
- **Primär-CTA:** `Bedarf prüfen lassen` → `/briefing`
- **Sekundär-CTA:** `Preise ansehen` → `/preise`
- **Preishinweis (klein, direkt unter den Buttons):** `Alle Preise netto zzgl. USt. Ausschließlich für Unternehmer.`
- **Trust-Zeile (4 Punkte):** `Festpreis vorab` · `Texte inklusive` · `Bundesweit, ohne Termin` · `SEO-Basis ab Launch`

  > **`Bundesweit, ohne Termin` ersetzt `Portal statt E-Mail-Chaos`** (01.08.2026). Zwei Gründe:
  >
  > 1. **Die Reichweite stand nirgends auf der Seite.** Ein Betrieb außerhalb Sachsens konnte nicht
  >    erkennen, ob er überhaupt Kunde werden kann. Das ist die teuerste Art, einen Besucher zu
  >    verlieren — er geht, ohne zu fragen
  > 2. **„Ohne Termin" und „bundesweit" sind dieselbe Tatsache.** Genau weil es keine
  >    Abstimmungstermine gibt, spielt Entfernung keine Rolle. Die Seite nannte bisher nur die eine
  >    Hälfte
  >
  > Der ersetzte Punkt geht nicht verloren — der Kundenbereich bekommt in Sektion 2 einen ganzen
  > Abschnitt mit elf Einzelpunkten.
- **Branchenangabe:** `Handwerk` · `Praxen` · `Kanzleien` · `Ladengeschäfte`

  > **Diese vier dürfen nicht anklickbar aussehen.** Im Entwurf sind sie als Pillen mit Rand und
  > Hintergrund gebaut — dieselbe Form wie die Knöpfe. Sie führen aber nirgendwohin. Eine Form, die
  > Interaktion verspricht und keine liefert, ist ein Bedienfehler, kein Gestaltungsdetail.
  >
  > **Zwei zulässige Auflösungen, eine muss gewählt werden:**
  > 1. **Ent-interaktivieren:** kein Rand, kein Flächenhintergrund, keine Rundung wie beim Knopf.
  >    Reine Typografie mit Trennzeichen
  > 2. **Verlinken:** auf echte Branchenseiten — dann aber erst, wenn diese Seiten existieren
  >
  > Solange keine Branchenseiten gebaut sind, gilt **1**.
- **Visual rechts (Desktop) / darunter (Mobil):** Portal-Cockpit-Screenshot, Badge „Musteransicht".

**Verhalten:** Desktop zweispaltig (Text 55 %, Visual 45 %). Mobil einspaltig — **H1 zuerst, Buttons direkt darunter, Visual danach**, Trust-Zeile als 2 × 2-Raster. Das Visual scrollt **nicht** horizontal. Unterer Rand des ersten Viewports zeigt bereits einen Anschnitt der nächsten Sektion.

### Sektion 2 — Der Kundenbereich

**Der wichtigste Abschnitt der Seite.** Er trägt das Unterscheidungsmerkmal, und er ist gleichzeitig
die stärkste zitierfähige Stelle für KI-Antwortsysteme (§16, `GEO_DISCOVERY_CHECKLIST.md` §4).

- **Eyebrow:** `Kundenbereich`
- **H2:** `Ohne einen einzigen Termin zur fertigen Website.`
- **Antwortsatz, vier Sätze, längster 14 Wörter, steht direkt unter der Überschrift:**
  > Bei SARTU gibt es keine Abstimmungstermine. Alles läuft über Ihren Kundenbereich. Sie
  > beantworten die Fragen zu Ihrem Betrieb, wann es Ihnen passt. Was dort geht, steht unten —
  > vollständig.

  > **Alte Fassung, nicht wieder verwenden:** ein Satz mit 45 Wörtern und fünf Gliedern, der die
  > Liste darunter vorwegnahm. Der Leser las dieselbe Information zweimal, beim ersten Mal
  > unlesbar. Die Liste **ist** der Antwortsatz.
- **Direkt darunter, damit „ohne Termin" nicht als „nicht erreichbar" gelesen wird:**
  > `Sprechen können Sie trotzdem mit uns. Sie müssen nur nicht.`

**Zwei Listen nebeneinander — vollständig, nicht gekürzt:**

| `Vor dem Start` | `Nach dem Start` | gebaut in |
|---|---|---|
| Angebot ansehen und annehmen | Öffnungszeiten und Kontaktdaten ändern | A1 · **B** |
| Fragen zu Ihrem Betrieb beantworten, wann es Ihnen passt | **Bilder tauschen** | A2 · **offen** |
| Logo, Bilder und Unterlagen hochladen | **Team- und Projekteinträge pflegen** | A2 · **offen** |
| Sehen, was gerade ansteht und was erledigt ist | **Anfragen von Ihrer Website einsehen** | A2 · **verboten** |
| Die fertige Vorschau ansehen | Rechnungen und Laufzeit einsehen | A3 · A2 |
| Änderungen sammeln und in einem Durchgang schicken | Änderungswünsche stellen | A3 · **B** |
| Freigeben | Domainstatus einsehen | A3 · A3 |

> **Drei Zeilen der rechten Spalte dürfen zum Websitestart nicht dastehen.** Das ist keine
> Kleinigkeit an der Formulierung, sondern ein Werbeversprechen ohne Deckung:
>
> | Zeile | Stand |
> |---|---|
> | *Bilder tauschen* | Keine Tabelle im Datenmodell. Es fehlt sinngemäß `media_assets` |
> | *Team- und Projekteinträge pflegen* | Keine Tabelle. Es fehlt sinngemäß `site_content` |
> | *Anfragen von Ihrer Website einsehen* | **Ausdrücklich verboten.** `CODEX_AUFTRAG_PORTAL.md` §… listet „Annahme von Anfragen aus Kundenwebsites" unter **Nicht bauen** — das ist die Lead-Inbox der Stufe 1 |
>
> **Entschieden am 01.08.2026: Die drei markierten Zeilen entfallen dauerhaft.** Die Seite geht mit
> **elf** Punkten live — das ist der Endstand, nicht eine Sicherung.
>
> Ausschlaggebend war ein Widerspruch: Das Portal sagt dem Kunden in seiner eigenen Hilfe (§8.8),
> dass SARTU **Bilder für ihn ändert**. Die Website bewarb dasselbe als Selbstbedienung.
> Vollständige Begründung in `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5a.

- **Der Unterschied, als eigene hervorgehobene Zeile:**
  > `Kein Terminkalender-Pingpong. Kein Suchen in alten E-Mails. Kein Anruf, um den Stand zu erfahren.`
- **Bild:** Ansicht aus dem Kundenbereich, Vermerk `Musteransicht`. Solange keine echte Aufnahme vorliegt: ehrlich beschrifteter Bildplatz, **keine nachgebaute Oberfläche**
- **Textlink:** `Den Kundenbereich ansehen` → `/leistung-portal`

> **Die Listen werden nicht gekürzt, nicht zu „unter anderem" zusammengefasst und nicht in
> Fließtext aufgelöst.** Zwei Gründe:
>
> **Verkauf:** Der Leser hat noch nie einen Kundenbereich bei einer Agentur gehabt — es gibt dort
> keinen. Er hat also kein Vorstellungsbild, das eine Andeutung füllen könnte. Elf konkrete
> Tätigkeiten erzeugen dieses Bild; drei Substantive erzeugen es nicht.
>
> **Die Kürzung von vierzehn auf elf ist keine Vereinfachung**, sondern die Streichung dreier
> Versprechen ohne Deckung. Sobald die Funktionen existieren, stehen sie wieder da.
>
> **Auffindbarkeit:** Ein KI-System, das gefragt wird „welche Webagenturen bieten einen
> Kundenbereich und was kann der", braucht **benennbare Einzelfakten**. Elf davon stehen hier.
> Ein Absatz mit denselben elf Punkten enthielte dieselbe Information, aber niemand schreibt so
> einen Absatz — man fasst zusammen, und beim Zusammenfassen verschwinden die Einzelheiten.
>
> **Berichtigt am 30.07.2026:** Hier stand vorher *„Fließtext ist nicht zitierbar, eine Liste
> schon."* Das ist technisch falsch. Suchmaschinen und Sprachmodelle zitieren Fließtext
> selbstverständlich. Listen helfen beim Überfliegen und beim Herauslösen einzelner Aussagen —
> sie sind **kein** eigener Mechanismus. Der Grund für die Liste ist die Menge an Einzelfakten,
> nicht eine angebliche technische Grenze. Der Befund kam von außen und war richtig.

> **Drei Fragen, die jede Fassung dieser Sektion beantworten muss, in dieser Reihenfolge:**
> Was ist das? · Was kann ich damit tun? · Warum hat das sonst niemand?
>
> **Zwei misslungene Vorfassungen, damit sie nicht wiederkehren:**
> `Ihr Projekt bleibt an einem Ort.` — ein Bild, keine Aussage. Welcher Ort, und was habe ich davon?
> `Sie müssen nie fragen, wie weit wir sind.` — richtig, aber zu klein. Es beschreibt einen
> Nebeneffekt, nicht das Versprechen. **Das Versprechen ist: keine Termine.**

**Belegt durch:** Masterkonzept §… „Standard: kein Termin nötig" und Website-Konzept §… „Gespräch
möglich, aber nicht Pflicht". Die Aussage ist damit gedeckt und **keine** Übertreibung.

### Sektion 3 — Ablauf

- **H2:** `Sie liefern die Fakten. Alles andere machen wir.`

  > **Ersetzt am 01.08.2026.** `wenige Angaben` — wie viele sind wenige? Nicht nachprüfbar. Die neue Fassung zieht eine Grenze, die man überprüfen kann.
- **Sechs Schritte** (nummeriert, weil es eine echte Reihenfolge ist):
  1. **Bedarfsscheck** — Wenige Fragen zu Unternehmen, Ziel, Umfang und Domain.
  2. **Geprüftes Angebot** — Sie bekommen Umfang, Preis und Zahlungsplan schriftlich.
  3. **Ihre Angaben** — Was wir schon wissen, tragen wir ein. Den Rest fragen wir Sie im Kundenbereich.
  4. **Produktion** — Wir bauen die Website. KI hilft, geprüft und freigegeben wird von uns.
  5. **Vorschau und Freigabe** — Sie sehen die fertige Website und sammeln Ihre Änderungen.
  6. **Start und Betrieb** — Wir schalten live und halten die Seite am Laufen.
- **CTA:** `Ablauf im Detail` → `/ablauf`

> **Höchstens drei Bildmotive im Zeitstrahl, nicht sechs.** Bilder bekommen nur die Schritte 1
> (Bedarfsscheck), 2 (geprüftes Angebot) und 5 (Vorschau und Freigabe). Die Schritte 3, 4 und 6
> bleiben rein typografisch.
>
> **Zwei Gründe, jeder allein ausreichend:**
> 1. Design-Briefing §3.7: *„Kein Aufbaumuster kommt mehr als zweimal vor."* Sechs gleich gebaute
>    Bildflächen sind dasselbe Muster sechsmal
> 2. Es spart **drei der fünfzehn Bildplätze**, die auf Stufe A warten (`REIHENFOLGE.md`)
>
> Die Strecke wird dadurch etwa halb so lang, und die drei verbliebenen Bilder tragen mehr.

**Am Ende dieser Sektion, als Abschluss des Zeitstrahls — die Arbeitsteilung:**

> `Ihr Anteil: was Ihr Betrieb macht, für wen und in welchem Gebiet. Dazu Bilder und Freigaben.`
> `Unser Anteil: alles andere. Auch die Verantwortung, wenn etwas nicht funktioniert.`

> **Alte Fassung, nicht wieder verwenden:** *„Alles Übrige — Struktur, Texte, Gestaltung,
> Programmierung, SEO-Grundlage, Domain, Hosting, Betrieb und die Verantwortung für das Ergebnis —
> liegt bei uns."* **Neun Glieder in einem Satz.** Der Leser hat sie zwei Zentimeter darüber als
> sechs nummerierte Schritte gelesen. `alles andere` ist kürzer und sagt mehr.

Zwei Zeilen, keine Liste, keine Karten, keine Spalten. **Hier funktioniert die Aussage**, weil der
Leser die sechs Schritte gerade gesehen hat und weiß, wovon die Rede ist. Als eigene Sektion weit
oben ist sie viermal misslungen (§5 Sektion 2).

### Sektion 4 — Preise — eine Empfehlung, vier mögliche Ergebnisse

- **H2:** `Sie wählen kein Paket. Wir sagen Ihnen, welcher Umfang passt.`

  > **Ersetzt am 01.08.2026.** `das passende Ergebnis` — woran misst man passend? Die neue Fassung nennt eine Tatsache, die man prüfen kann: Es gibt keine Paketwahl.
- **Subline:** `Eine Empfehlung statt Paketwahl.`
- **Einleitung (25 W., drei Sätze):**
  > Sie müssen nicht wissen, wie viele Seiten Sie brauchen. Der Bedarfsscheck zeigt, welcher Umfang voraussichtlich passt. Wir prüfen das anschließend selbst nach.

  > **Wortwechsel, gilt für die ganze Seite:** `Lösung` als Wort für das Produkt ist gestrichen
  > (`SARTU_TEXTREGELN.md` Liste C). Es heißt `Umfang` oder der Name selbst — `Start`, `Wachstum`,
  > `Platzhirsch`.

> **Gleiche Informationstiefe für alle vier.** Jede Lösung nennt: für wen sie gedacht ist, was drin
> ist (drei bis vier Merkmale), Einmalpreis, Monatspreis, Erstjahreswert, eigener Aufruf zum
> Handeln. **Die Empfehlung wird durch Gestaltung hervorgehoben, nicht dadurch, dass die anderen
> weniger erklärt bekommen.** Wer eine kleine Lösung braucht und nur die teure erklärt sieht, geht
> — und zwar zu Recht.

> **Jede Stufe nennt ihren Umfang als Zahl** (`SARTU_TEXTREGELN.md` Regel 1). Die frühere Fassung
> sagte „eine durchdachte Seite", „bis zu acht strategische Seiten" — **keine einzige der acht
> festgelegten Umfangszahlen stand auf der Seite.** Quelle ist die Preistabelle im Masterkonzept.
> Die Zahl steht **zuerst**, vor der Zielgruppe: Sie ist das, was der Leser vergleichen kann.

**`Start`** — `1.490 € einmalig` · `+ 59 €/Monat` · `Erstes Jahr: 2.198 € netto`
> **1 Seite, rund 1.200 Wörter. 1 Korrekturrunde.**
> Für Betriebe mit einem Angebot und einem Einzugsgebiet: Handwerk, Praxis, Ladengeschäft.

Merkmale: `1 Seite, rund 1.200 Wörter` · `1 Korrekturrunde` · `Kontakt- und Anfahrtsweg` · `Betrieb und Sicherungen enthalten`
CTA: `Einschätzen lassen`

**`Wachstum`** — `3.900 € einmalig` · `+ 129 €/Monat` · `Erstes Jahr: 5.448 € netto`
> **Bis zu 8 Seiten, rund 3.500 Wörter. 2 Korrekturrunden.**
> Für Betriebe mit mehreren Leistungen oder mehreren Zielgruppen, die einzeln erklärt werden müssen.

Merkmale: `bis zu 8 Seiten, rund 3.500 Wörter` · `2 Korrekturrunden` · `eigene Seite je Leistung` · `SEO-Grundlage je Seite`
CTA: `Einschätzen lassen`

**`Platzhirsch`** — Badge `Empfehlung` · `7.900 € einmalig` · `+ 249 €/Monat` · `Erstes Jahr: 10.888 € netto`
> **Bis zu 16 Seiten, rund 6.500 Wörter. 2 Korrekturrunden.**
> Für Betriebe, die in ihrer Region als erste Adresse auftreten wollen — sichtbar für Kunden und für Bewerber.

Merkmale: `bis zu 16 Seiten, rund 6.500 Wörter` · `2 Korrekturrunden` · `eigene Seite je Leistung und Ort` · `Karriere- und Bewerbungsbereich`
CTA: `Bedarf prüfen lassen`

**`Sonderprojekt`** — `ab 12.500 € einmalig` · `mind. 249 €/Monat` · `Erstes Jahr: ab 15.488 € netto`
> **Umfang nach technischer Vorprüfung. Kein Paket.**
> Für Shop, Kundenlogin, komplexe Buchung, Schnittstellen zu vorhandener Software oder mehrere Marken unter einem Dach.

Merkmale: `Festpreis vor Ihrer Entscheidung` · `keine offene Stundenabrechnung` · `Absage, wenn wir es nicht verantworten können`
CTA: `Sonderprojekt besprechen`

**Pflichtzeile beim Sonderprojekt, direkt unter dem Knopf:**
> `Nur Sonderprojekte klären wir vor dem Angebot persönlich.`

> **Erste Fassung war schwächer:** `Der einzige Fall, in dem wir vorher sprechen.` Gleich lang,
> sagt aber weniger — der Leser muss „Fall wovon?" selbst auflösen, und der Zeitpunkt fehlt. Die
> gültige Fassung nennt beides. Der Vorschlag kam von außen.

> **Warum diese Zeile gebraucht wird.** Sektion 2 verspricht `Ohne einen einzigen Termin zur
> fertigen Website.` Der Knopf hier heißt `Sonderprojekt besprechen`. Ohne die Zeile widerspricht
> sich die Seite auf demselben Bildschirm. Der Befund kam von außen und war richtig.
>
> **Die Überschrift in Sektion 2 bleibt trotzdem unverändert.** Sie beschreibt den Regelfall — drei
> von vier Angeboten und praktisch das gesamte erwartete Volumen. Eine Einschränkung wie
> *„Standardprojekte ohne Pflichttermine"* würde den stärksten Satz der Seite kaputtmachen, um
> einen Sonderfall abzudecken. Der Sonderfall wird **dort** benannt, wo er auftritt.

**Für alle Stufen zusätzlich sichtbar:** `Erstlaufzeit 12 Monate` · `Zahlungsziel 10 Tage`.
Beide Zahlen standen bisher nirgends auf der Startseite und sind Teil des Angebots.

> **Sechs Vorgaben aus dem UX-Audit vom 28.07.2026, alle verbindlich:**
>
> 1. **Das Sonderprojekt steht nicht als vierte gleichwertige Karte.** Es ist kein Paket, sondern eine technische Vorprüfung — es steht als eigene Zeile **unterhalb** der drei Stufen, gestalterisch abgesetzt. Drei echte Optionen plus ein Sonderfall sind leichter zu erfassen als vier Karten nebeneinander
>
>    **Abgesetzt heißt nicht abgeschwächt.** Der Entwurf setzte das als gestrichelten Rahmen ohne
>    Flächenfarbe um — das liest sich wie ein deaktiviertes Feld. Die 12.500 € sind der **obere
>    Preisanker** und müssen als nächsthöhere Kategorie erkennbar bleiben: gleiche Schriftgröße für
>    den Betrag, voller Textkontrast, durchgezogener Rahmen. Anders ist nur die **Form** — eine
>    Zeile statt einer Karte —, nicht das Gewicht. Dasselbe gilt für den Block
>    *„Was die Monatspauschale abdeckt"*: abgesetzt, aber nicht blass
> 2. **Nie „Vier Lösungen" als Überschrift.** Das widerspricht der eigenen Aussage „Sie wählen kein Paket". Es heißt `Eine Empfehlung. Vier mögliche Ergebnisse.`
> 3. **Die Monatspauschale wird aufgeschlüsselt**, nicht nur benannt. „Deckt Betrieb, Pflege und Support" beantwortet die teuerste offene Frage der Seite nicht: *Wofür zahle ich jeden Monat?* Konkret nennen: Hosting, technische Pflege, Sicherheitsaktualisierungen, Sicherungen, Überwachung, Support, Kundenbereich
> 4. **`Platzhirsch` wird erklärt**, sonst klingt der Name großspurig: `Für Betriebe, die in ihrer Region als erste Adresse auftreten wollen.` **Keine** Ranking-Zusage
> 5. **`SEO-Grundlage` wird von späterer SEO-Arbeit abgegrenzt.** Sonst wirkt ein späteres Angebot wie Doppelverkauf: `SEO-Grundlage ab Livegang: Struktur, Titel, Metadaten, interne Verlinkung, indexierbare Inhalte. Die laufende Weiterentwicklung ist ein eigenes Thema.`
> 6. **Der Preisbereich darf nicht wie Software-Preise wirken.** SARTU verkauft keine Software im Abonnement, sondern eine Website samt Betrieb. Über den Stufen steht deshalb sichtbar, dass nicht gewählt, sondern empfohlen wird

**Verhalten:** Der Platzhirsch ist optisch die Empfehlung — größere Fläche, Badge, kräftigerer Aufruf zum Handeln — trägt aber **denselben Informationsumfang** wie die anderen. Das Sonderprojekt steht sichtbar als vierte Möglichkeit, nicht als Fußnote. Mobil: Platzhirsch zuerst, dann Wachstum, Start, Sonderprojekt. Die Aufrufe von Start und Wachstum sind visuell schwächer als der des Platzhirschs, aber vorhanden und anklickbar.

### Sektion 5 — Die Zusage

Ein **randlos dunkler Streifen** mit einem einzigen großen Satz. Sonst nichts: kein Bild, keine
Aufzählung, kein Knopf.

> `Ein Preis. Ein Ergebnis. Keine Stundenabrechnung, keine Nachforderung.`

**Zweck ist der Gangwechsel.** Nach dem Preisblock und vor den Einzelheiten holt die Seite Luft.
Dieser Abschnitt kostet nichts, braucht kein Bildmaterial und ist das wirksamste Mittel gegen den
Eindruck einer durchgehenden Liste. Er wird **nicht** um Unterpunkte, Symbole oder einen zweiten
Satz ergänzt — die Wirkung entsteht aus der Leere ringsum.

### Sektion 6 — Wer dahintersteckt

- **H2:** `Eine Person baut Ihre Website. Dieselbe antwortet danach.`

  > **Ersetzt am 01.08.2026.** `Wer hier arbeitet.` ist ein Etikett mit Punkt dahinter. Die neue Fassung nennt eine Zahl: **eine** Person, und dieselbe später.
- **Echtes Foto** von `[GRUENDER_NAME]`, keine Bestandsaufnahme, kein Platzhalter, der wie ein Foto wirkt. Steht das Foto nicht zur Verfügung, entfällt die Sektion **vollständig** — ein leerer Rahmen an einer Vertrauensstelle ist schlechter als gar nichts (Design-Briefing §4a).
- **Name und Rolle:** `[GRUENDER_NAME]`, gründergeführt.
- **Zwei bis drei Sätze Haltung.** Kein Lebenslauf, keine Erfolgsgeschichte, keine Zahlen.
- **`Was SARTU bewusst nicht ist`** — vier Punkte, knapp:
  `kein Baukasten` · `kein WordPress-Hoster` · `keine Billig-Seitenschleuder` · `kein Anbieter für Privat- und Hobbyseiten`
- **Textlink:** `Mehr über SARTU` → `/ueber-uns`

> **Warum diese Sektion existiert und warum genau hier.** Sie ist der **Belegersatz**. Der übliche
> Platz für Kundenlogos und Fallstudien bleibt bei SARTU zum Start leer. Was stattdessen trägt:
> ein Mensch mit Namen und Gesicht — und die ausdrückliche Aufzählung dessen, was SARTU **nicht**
> macht. Wer seine Grenzen benennt, wirkt geprüft; wer nur Vorzüge aufzählt, wirkt beliebig.
>
> **Ehrlichkeitsregel (§11):** Solange eine Einzelperson arbeitet, heißt es `gründergeführt` —
> nie `unser Team`. Kein Wir, das größer tut, als es ist.

### Sektion 7 — Leistungen

- **H2:** `Es gibt keine Aufpreisliste.`

  > **Ersetzt am 01.08.2026.** `Alles, was eine Firmenwebsite braucht` — wer entscheidet, was sie braucht? Der zweite Halbsatz trug allein. Eine Aufpreisliste ist ein Ding: Es gibt sie oder nicht.
- **Einleitung (30 W.):**
  > Das alles steckt in jedem Angebot — Sie stellen es nicht selbst zusammen und zahlen nichts davon extra. Wir gewichten die Bausteine passend zu Ihrem Ziel.
- **Acht breite Zeilen** (Titel · ein Satz · Tags), **keine** Kachelwand, **keine** Preise:

| Titel | Satz | Tags |
|---|---|---|
| Strategie und Seitenstruktur | Wir legen fest, welche Seiten Ihr Ziel wirklich brauchen — und welche nicht. | Sitemap · Nutzerführung · Suchintention |
| Webdesign und Programmierung | Individuell aus unserem Designsystem programmiert, ohne WordPress und ohne Baukasten. | kein WordPress · responsive · schnell |
| Website-Texte | Wir schreiben die Texte aus Ihren Fakten und Stichpunkten — Sie liefern keinen fertigen Webtext. | aus Stichpunkten · Faktenprüfung |
| SEO- und GEO-Grundlage | Jede Seite startet mit klarem Thema, sauberen Metadaten, strukturierten Daten und Antwort-zuerst-Texten. | Titles · Schema · Antwort-zuerst · interne Links |
| Lokale Sichtbarkeit | Echte Unternehmensdaten statt dünner Ortsseiten mit ausgetauschtem Stadtnamen. | Local SEO · konsistente Daten |
| Domain und Launch | Wir prüfen, verbinden und schalten live — Ihre bestehende E-Mail bleibt dabei erreichbar. | DNS · E-Mail-Schutz · Weiterleitungen |
| Portal und Freigaben | Angebot, Briefing, Vorschau und Feedback laufen an einem Ort statt in E-Mail-Ketten. | Briefing · Feedback · Pflege |
| Rundum-Schutz | Wir betreiben die Website danach: Hosting, Sicherheit, Backups, Monitoring. | Betrieb · Backups · Monitoring |

- **CTA:** `Alle Leistungen im Überblick` → `/leistungen`

> **`GEO` ergänzt am 03.08.2026.** Die Zeile hieß `SEO-Grundlage` und nannte GEO mit keinem Wort —
> obwohl **dasselbe Dokument** in der Seitenübersicht (Abschnitt 5) die Leistungsseite als
> `Sichtbarkeit (SEO/GEO)` führt und das Masterkonzept das **SEO-/GEO-Startsystem** ausdrücklich
> als „in jedem Paket enthalten" auflistet. Ein Widerspruch im eigenen Haus.
>
> **Zeitpunkt und Preis stehen bereits fest** (Masterkonzept §16) und werden hier **nicht** neu
> entschieden, nur wiedergegeben:
>
> | | |
> |---|---|
> | **Im Websitepreis, ab Launch** | Suchintention und Thema je Seite · Antwort-zuerst-Texte aus bestätigten Fakten · sprechende URLs · genau eine H1 · Title/Description/Canonical/OG/Robots · Breadcrumb · `Organization`+`WebSite` global · XML-Sitemap, robots.txt, 404, Redirect-Plan · echte NAP · Search Console und Bing eingereicht |
> | **Laufend im Betrieb** | `Schutz M` monatlicher Technik-/Suchstatus · `Schutz L` engmaschiger SEO-/GEO-/Conversion-Technikcheck. `Schutz S` hat beides **nicht** |
> | **Später** | Sichtbarkeitsausbau als **ein** datenbasiertes Folgeangebot. Ausdrücklich: „**Kein** SEO-Menü, keine Stufen, keine Minuten" |
>
> **GEO ist also kein Zusatz und nichts, was erst im Kundenbereich kommt — es liegt ab Start im
> Paketpreis, genau wie SEO.** Das ist auch der Grund, warum es auf die Startseite gehört: Der
> USP im Masterkonzept lautet wörtlich *„Festpreis. Portal. Kein WordPress. **SEO-/GEO-Basis ab
> Start**."* — eines von vier Merkmalen fehlte in der Leistungsliste.
>
> **Grenze der Aussage:** §16 verbietet jede Garantie auf Rankings, Anfragen oder KI-Nennungen
> und hält fest, dass GEO „**kein** magischer Zusatz und **kein** Spezial-Schema" ist. Die Zeile
> nennt deshalb ein Verfahren (`Antwort-zuerst`), kein Ergebnis. `llms.txt` wird angelegt, aber
> **nie** als Rankingfaktor beworben.

- **H2:** `Ihre Website ist ab dem ersten Tag für Suchmaschinen vorbereitet.`

  > **Alte Fassung, verboten:** `Ihre Website ist ab dem ersten Tag auffindbar.` Das ist
  > „garantierte Sichtbarkeit" in weichen Worten und verstößt gegen §2. Weder die Aufnahme in den
  > Index noch ihr Zeitpunkt liegen bei uns. Überall sonst formulieren wir korrekt — *„Die Grundlage
  > ja, ab dem ersten Tag"*, *„Ohne Rankinggarantie"*. Die H2 war der Ausreißer.
- **Text (42 W.):**
  > Jede SARTU-Website startet mit klaren Seitenthemen, sprechenden Adressen, sauberer interner Verlinkung, Metadaten, strukturierten Daten und einer soliden Performance-Grundlage. Späterer Ausbau baut auf echten Suchdaten auf — nicht auf pauschalen SEO-Paketen.
- **Drei Spalten:**
  - `Menschen verstehen` — klare Antworten, Preise, Ablauf und Grenzen stehen sichtbar auf der Seite.
  - `Suchmaschinen erfassen` — sauberes HTML, Sitemap, Canonicals, strukturierte Daten, Ladezeit.
  - `KI-Antworten einordnen` — konsistente Unternehmensfakten, FAQ und Definitionen statt Textwüsten.
- **Pflichthinweis:**
  > Rankings, Anfragen oder Nennungen in KI-Systemen kann niemand garantieren. Wir bauen das Fundament und halten die technische Suchgesundheit im Betrieb im Blick.

### Sektion 8 — Musterprojekte

**Der Ersatz für fehlende Referenzen** — und der einzige, der ohne Kunden funktioniert.

- **H2:** `Noch keine Kunden. Deshalb zeigen wir Musterprojekte.`

  > **Ersetzt am 01.08.2026.** `könnte` ist Konjunktiv und damit keine Aussage. Die neue Fassung nennt eine Zahl — null Kunden. Unangenehm, aber nachprüfbar.
- **Einleitung, einmal über dem Raster:**
  > SARTU ist 2026 gestartet. Die drei Beispiele sind Muster, keine Kundenaufträge.

  > **Alte Fassung, nicht wieder verwenden:** *„Noch keine lange Referenzliste. Deshalb zeigen wir
  > offen, wie ein Projekt aufgebaut wird, welche Entscheidungen wir treffen und was am Ende dabei
  > herauskommt."* Die Seite erklärt sich selbst (`SARTU_TEXTREGELN.md` Regel 6), und der Satz hat
  > drei Glieder ohne Information.
- **Drei Musterprojekte**, je eine Karte: **Malerbetrieb · Physiotherapiepraxis · Arbeitsrechtskanzlei**

  > **Konkrete Gattung statt Oberbegriff** (`SARTU_TEXTREGELN.md` Regel 5). „Handwerksbetrieb" kann
  > man nicht vor sich sehen, „Malerbetrieb" schon — und ein Malermeister erkennt sich wieder.
  > **Weiterhin verboten:** erfundene Firmennamen. Die Gattung genügt.
- **Über jeder Karte, unübersehbar:** `Musterprojekt — kein Kundenauftrag`

  > **Die Bezeichnung bleibt.** Ein Vorschlag von außen lautete `Konzeptstudie`. Das ist mehr
  > Fachwort und sagt gerade **nicht**, worauf es rechtlich ankommt: dass kein Kunde dahintersteht.
- Je Karte: Ausgangslage · empfohlene Lösung · Seitenstruktur · Bildplatz · was der Kunde selbst liefern müsste
- **CTA:** `Alle Musterprojekte ansehen`

**Sperren:**

- **Nie** `Ausgewählte Arbeiten`, `Referenzen`, `Kunden` oder `Projekte` als Überschrift, solange es keine echten gibt. Die Bezeichnung entscheidet, ob es Demonstration oder Täuschung ist
- **Keine** erfundenen Firmennamen, die wie echte Betriebe klingen. Gattungsbezeichnungen genügen
- **Keine** erfundenen Zahlen, Ergebnisse oder Steigerungen
- Solange die Beispielseiten nicht gebaut sind: **ehrlich beschrifteter Bildplatz**, kein nachgebauter Bildschirm

> **Voraussetzung:** Die Musterprojekte müssen tatsächlich existieren
> (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5). Bis dahin bleibt die Sektion **ungebaut** — eine
> Musterprojekt-Sektion ohne Musterprojekte ist schlechter als keine.

### Sektion 9 — Häufige Fragen (10–12 Einwände, Akkordeon)

1. **Arbeiten Sie auch außerhalb von Sachsen?**
   > Ja, bundesweit. Weil es keine Abstimmungstermine gibt, spielt die Entfernung keine Rolle — in Flensburg läuft es genauso ab wie in Dresden. Unser Sitz ist im Raum Dresden, gearbeitet wird für Betriebe in ganz Deutschland.

   > **Diese Frage steht bewusst an erster Stelle** (01.08.2026). Sie entscheidet, ob der Leser sich überhaupt angesprochen fühlt. Vorher stand die Reichweite **nirgends auf der Seite** — ein Betrieb außerhalb Sachsens ging, ohne zu fragen.
2. **Muss ich mir selbst ein Paket aussuchen?**
   > Nein. Sie beschreiben Ihr Unternehmen und Ihr Ziel; wir empfehlen genau einen Umfang und begründen ihn. Wenn ein kleinerer reicht, empfehlen wir den kleineren.
3. **Schreiben Sie die Texte?**
   > Ja. Sie liefern Fakten, Stichpunkte und vorhandene Unterlagen — wir schreiben daraus die Website-Texte. Erfundene Belege oder ungeprüfte Fachaussagen gibt es nicht.
4. **Warum gibt es keine Liste mit Zusatzoptionen?**
   > Weil Zusatzlisten den Preis unklar machen. Ein Standardangebot endet exakt beim genannten Festpreis. Passt eine Anforderung nicht hinein, bekommen Sie dafür ein eigenes Angebot mit eigenem Festpreis.
5. **Was passiert mit meiner Domain und meinen E-Mail-Adressen?**
   > Die Domain gehört Ihnen — auch wenn wir sie technisch verwalten. Vor jeder Änderung sichern wir Ihre bestehenden Einträge, damit Ihre E-Mail-Adressen beim Umschalten erreichbar bleiben.
6. **Kann ich später selbst etwas ändern?**
   > Öffnungszeiten und Kontaktdaten pflegen Sie selbst im Kundenbereich. Texte, Bilder und Seitenstruktur ändern wir für Sie — schreiben Sie uns einfach, das ist im Betrieb enthalten.

   > **Korrigiert am 01.08.2026.** Die alte Antwort versprach zusätzlich *Team- und Projekteinträge* und *Bilder in vorhandenen Bildplätzen* als Selbstbedienung. Genau diese drei Funktionen sind gestrichen (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5a), und die Portalhilfe §8.8 sagte ohnehin schon das Gegenteil. Der Wortlaut ist jetzt in beiden Dokumenten identisch.
7. **Ist SEO enthalten?**
   > Die Grundlage ja, ab dem ersten Tag: Seitenthemen, Metadaten, strukturierte Daten, interne Verlinkung, Ladezeit. Ein späterer Ausbau folgt echten Suchdaten und ist ein eigenes Angebot.
8. **Warum kein WordPress?**
   > Weil Sie sich dann um Updates, Plugins und Sicherheitslücken kümmern müssten. Wir programmieren die Website ohne diese Abhängigkeiten und betreiben sie selbst.
9. **Können Sie eine bestimmte Google-Position zusichern?**
   > Nein, und niemand kann das seriös. Wir bauen das technische und inhaltliche Fundament und halten es im Betrieb sauber.

### Sektion 10 — Bedarfsscheck-Einstieg

- **H2:** `Welche Website passt zu Ihrem Unternehmen?`
- **Text (36 W.):**
  > Der Bedarfsscheck fragt nicht nach Seitenzahlen, Farben oder SEO-Stufen. Sie beantworten wenige Fragen zu Ihrem Geschäft und sehen sofort eine vorläufige Empfehlung mit Preis. Danach prüfen wir persönlich.
- **Chips (nur Anzeige):** `Branche` · `Region` · `Ziel` · `Umfang` · `Domain` · `Besonderheiten`
- **Vertrauenszeile:** `Dauert etwa 3 Minuten` · `Preis vor Kontaktdaten` · `Kein Pflichttermin` · `Unverbindlich`
- **CTA:** `Bedarf prüfen lassen` (primär) → `/briefing` · `Preise ansehen` (sekundär, als Textlink) → `/preise`
- **Statuszeile** nach §5a — bei `ausgebucht` steht sie über dem Knopf und die Beschriftung lautet `Auf die Warteliste`
- **Pflichthinweis, direkt darunter:**
  `Unverbindlich bis zum geprüften Angebot. Alle Preise netto zzgl. USt. Ausschließlich für Unternehmer.`

> **Diese Sektion ist zugleich der Abschluss der Seite.** Der frühere separate Abschluss-Aufruf
> („Wenige Angaben reichen für den ersten Schritt.") entfällt — er sagte dasselbe wie diese
> Sektion, nur zwei Bildschirmhöhen später. Zwei Aufrufe hintereinander schwächen beide.
>
> **Warum der Bedarfsscheck ganz unten steht:** Wer bis hierher gescrollt hat, hat den Preis
> gesehen, den Kundenbereich, den Ablauf, die Leistungen und die Person dahinter. Erst dann ist
> ein Formular eine sinnvolle Bitte. Der Einstieg weiter oben ist der Knopf im Aufmacher.

## 5c — Prüfung vor dem Livegang: sieben echte Menschen

**Vor der Veröffentlichung wird die Startseite mit fünf bis sieben Unternehmern oder
Selbstständigen geprüft** — nicht mit Bekannten aus der Branche.

**Fünf-Sekunden-Test.** Aufmacher fünf Sekunden zeigen, dann verdecken und fragen:
Was bietet SARTU an? · Für wen? · Was ist der nächste Schritt? · Wirkt es teuer, seriös, unklar oder vertrauenswürdig?

**Erst-Klick-Test.** „Sie wollen wissen, was es kostet — wohin klicken Sie?" · „Sie wollen wissen, wie wenig Arbeit Sie selbst haben." · „Sie wollen wissen, ob Sie später Inhalte ändern können."

**Vertrauensfrage.** Nach Aufmacher, Ablauf, Kundenbereich und Musterprojekt:
Was fehlt Ihnen, bevor Sie anfragen? · Was wirkt unglaubwürdig? · Was klingt zu gut? · Würden Sie den Bedarfsscheck starten?

> **Warum das hier steht und nicht als Empfehlung:** Über den Aufbau dieser Seite wurde mehrfach
> ohne einen einzigen Datenpunkt entschieden. Sieben Menschen fünf Sekunden auf den Aufmacher
> schauen zu lassen kostet einen Nachmittag und beendet jede weitere Vermutungsrunde. Das Ergebnis
> wird schriftlich festgehalten — auch wenn es unbequem ist.

---

## 6. `/leistungen`

**Title:** `Leistungen: Webdesign, Texte, SEO und Betrieb | SARTU`
**Meta (155 Z.):** `Webdesign, Website-Texte, SEO-Grundlage, Domain und Launch, Portal und laufender Betrieb — bei SARTU als ein Ergebnis zum Festpreis statt als Einzeloptionen.`
**H1:** `Website, Texte, Sichtbarkeit und Betrieb als ein System.`
**Umfang:** 700–850 Wörter · **Schema:** `Service`, `BreadcrumbList` (`FAQPage` optional, s. §16)

**Sektionen:**
1. **Hero** — H1 + Lead: „Sie bekommen kein Bündel einzelner Leistungen, sondern ein Ergebnis: eine Website, die Ihr Angebot erklärt, Anfragen erzeugt und danach zuverlässig betrieben wird." + CTA.
2. **Kurz gesagt** (Antwortmodul, 45 W.) — „SARTU verbindet Strategie, Texte, Design, Programmierung, SEO-Grundlage, Domain und Launch, Portal und Betrieb zu einem Festpreis-Ergebnis. Was Ihr Projekt davon in welcher Tiefe braucht, entscheiden wir im Angebot — nicht Sie im Bestellformular."
3. **Leistungslandkarte** — dieselben acht Zeilen wie Startseite, aber je 3–4 Sätze statt einem, plus Link auf die jeweilige Leistungsseite (soweit vorhanden).
4. **Was Sie nicht entscheiden müssen** — Liste: System und Technik · Seitenzahl · Designstil · SEO-Stufe · Hosting · Registrar · Wartungsminuten.
5. **Wie tief es je Lösung geht** — Tabelle Start / Wachstum / Platzhirsch / Sonderprojekt mit Ergebnis-Spalte (nicht Feature-Häkchen).
6. **FAQ (3):** „Kann ich einzelne Leistungen dazubuchen?" · „Was ist, wenn ich später mehr brauche?" · „Übernehmen Sie auch bestehende Websites?"
7. **CTA-Band.**

---

## 7. `/preise`

**Title:** `Preise: Firmenwebsite ab 1.490 € netto | SARTU`
**Meta (149 Z.):** `Start 1.490 €, Wachstum 3.900 €, Platzhirsch 7.900 € netto — jeweils mit festem Betriebspaket. Erstjahreskosten und Zahlungsplan transparent aufgeschlüsselt.`
**H1:** `Klare Preise. Wir prüfen, was wirklich passt.`
**Umfang:** 650–800 Wörter · **Schema:** `Service`, `BreadcrumbList` (`FAQPage` optional, s. §16)

**Sektionen:**
1. **Hero** — H1 + Lead: „Sie müssen kein Paket auswählen. Die kurze Bedarfseinschätzung zeigt, welche Lösung wahrscheinlich passt; wir prüfen das Ergebnis persönlich, bevor Sie ein Angebot bekommen." + CTA.
2. **Preisübersicht** — Platzhirsch groß, Start/Wachstum kompakt, Sonderprojekt als Abzweig.
3. **Erstjahrestabelle** (tabellarische Ziffern, mobil horizontal scrollbar):

| Lösung | Einmalig netto | Betrieb / Monat | Erstes Jahr netto |
|---|---:|---:|---:|
| Start | 1.490 € | 59 € | 2.198 € |
| Wachstum | 3.900 € | 129 € | 5.448 € |
| **Platzhirsch** | **7.900 €** | **249 €** | **10.888 €** |
| Sonderprojekt | ab 12.500 € | ab 249 € | ab 15.488 € |

4. **Was jedes Projekt enthält** — Strategie · Texte · Design · Programmierung · SEO-Grundlage · Portal · Domainverbindung · Launch.
5. **Der Rundum-Schutz** — Framing zwingend nach §2: „Keine Wartung für Sie." Enthalten: Hosting, SSL, Backups, Monitoring, technische Updates, technische Suchgesundheit, Formularprüfung, Portalzugang. **Nicht** enthalten: unbegrenzte Text- oder Designänderungen, neue Seiten, neue Ziele. Selbst pflegbar: Öffnungszeiten, Kontaktdaten, vorhandene Einträge, Seitenstatus.
6. **Domain und E-Mail** — Kunde bleibt Inhaber; eine normale Domain bis 30 € netto/Jahr ist im Betrieb enthalten; bestehende E-Mail wird geschützt.
7. **Zahlung** — Start/Wachstum 50/50 · Platzhirsch 40/30/30 · Zahlungsziel 10 Tage · Zahlung im Portal · Produktionsslot nach erster Zahlung.
8. **FAQ (6):** netto oder brutto? · später erweitern? · warum ist der Betrieb verpflichtend? · versteckte Kosten? · Domainkosten? · Sonderfunktionen?
9. **CTA-Band.**

**Bildregel:** keine großen Fotos — Preise brauchen Scanbarkeit. Erlaubt: kleine Portal-Zahlungsansicht als Muster, **ohne** realistische Rechnungsnummern oder echte Namen.

---

## 8. `/ablauf`

**Title:** `Ablauf: vom Bedarfsscheck zur fertigen Website | SARTU`
**Meta (151 Z.):** `So läuft ein SARTU-Projekt: kurzer Bedarfsscheck, geprüftes Festpreisangebot, Portal-Onboarding, Produktion, Vorschau und Freigabe, Launch und laufender Betrieb.`
**H1:** `Ein Websiteprojekt ohne Termin-Marathon.`
**Umfang:** 700–850 Wörter · **Schema:** `BreadcrumbList` (`FAQPage` optional, s. §16)

**Sektionen:**
1. **Hero** — Lead: „Standardprojekte laufen bei SARTU digital und gebündelt. Ein Gespräch ist jederzeit möglich, aber nicht Pflicht: Angebot, Briefing, Zahlungen, Domain, Vorschau und Freigaben laufen im Portal."
2. **Vergleich** (Tabelle, zwei Spalten — **ohne** Prozentangaben):

| Klassisches Projekt | Bei SARTU |
|---|---|
| Erstgespräch als Pflichttermin | Bedarfsscheck in etwa 3 Minuten |
| Kickoff-Termin und Fragebogen | Portal übernimmt bekannte Fakten, fragt nur Lücken |
| Feedback verteilt über E-Mails und Anrufe | gebündeltes Feedback an einer Stelle |
| Viele offene Entscheidungen beim Kunden | Struktur, Design und Technik entscheiden wir |
| Preis wird im Verlauf konkret | Festpreis steht vor Auftrag |

3. **Acht Schritte** — ausführlich, je 2–3 Sätze: Bedarfsscheck · unsere Prüfung · Angebot im Portal · Annahme und erste Zahlung · Domain und Onboarding · Produktion mit QA · Vorschau, Feedback, Abnahme · Launch und Betrieb.
4. **Portal-Screens** — fünf Ansichten mit Bildunterschrift (Angebot · Briefing · Domain · Vorschau/Feedback · Pflege).
5. **Was Sie wirklich tun müssen** — Fakten bestätigen · Material hochladen · Domain bestätigen · Vorschau prüfen · freigeben.
6. **Was wir entscheiden** — Empfehlung · Seitenstruktur · Nutzerführung · Design · SEO-Grundlage · Technik · Hosting · DNS-Plan.
7. **Zeitrahmen** — „Nach vollständigem Start: Start 7–10, Wachstum 10–15, Platzhirsch 15–25 Werktage. Der Zeitraum beginnt, wenn Zahlung, Briefing und Material vorliegen."
8. **CTA-Band.**

---

## 9. `/briefing` — Bedarfsscheck, feldgenau

**Title:** `Bedarf prüfen lassen — unverbindliche Empfehlung | SARTU`
**Meta (147 Z.):** `Beantworten Sie wenige Fragen zu Ihrem Unternehmen und sehen Sie sofort eine vorläufige Empfehlung mit Festpreis. Unverbindlich, ohne Termin, in etwa drei Minuten.`
**H1:** `Welche Website passt wirklich zu Ihrem Unternehmen?`
**Schema:** `BreadcrumbList` · **Indexierung:** `index` (Einstiegsseite), Ergebnisschritte `noindex`

### 9.1 Einstiegsbildschirm

- Lead: „Sie müssen weder Paket noch Seitenzahl, Designrichtung oder SEO-Stufe kennen. Beantworten Sie wenige Fragen zu Ihrem Geschäft — danach sehen Sie eine vorläufige Empfehlung mit Preis."
- Vertrauenspunkte: `Dauert etwa 3 Minuten` · `Preis vor Kontaktdaten` · `Kein Pflichttermin` · `Keine Auswahl von Zusatzoptionen` · `Unverbindlich bis zum geprüften Angebot`
- Button: `Bedarf prüfen lassen`
- Fortschritt: `Thema 1 von 5` (nicht „Frage 1 von 10")

### 9.2 Felder (Labels, Hilfetexte, Validierung — final)

**Thema 1 — Ihr Unternehmen**

| Feld | Label | Typ | Pflicht | Hilfetext | Fehlermeldung |
|---|---|---|---|---|---|
| 1.1 | Was bietet Ihr Unternehmen an? | Textarea, 1–3 Sätze | ja | „Zum Beispiel: Wir sanieren Bäder und Heizungen für Privatkunden im Umkreis von 40 km." | „Bitte beschreiben Sie Ihr Angebot in ein bis drei Sätzen." |
| 1.2 | Wo arbeiten Sie hauptsächlich? | Text (Ort oder PLZ) | ja | „Ort oder Postleitzahl genügt." | „Bitte geben Sie Ort oder Postleitzahl an." |
| 1.3 | Größeres Einzugsgebiet? | Text | nein | „Optional, z. B. Umkreis oder Region." | – |
| 1.4 | Gibt es bereits eine Website? | Radio: Ja / Nein / Bin unsicher | ja | – | „Bitte wählen Sie eine Antwort." |
| 1.5 | Adresse der bestehenden Website | URL, erscheint nur bei „Ja" | ja (bedingt) | „Auch eine alte oder unfertige Seite hilft uns." | „Bitte geben Sie eine gültige Internetadresse an, z. B. beispiel.de" |

**Thema 2 — Ihr Ziel**

| Feld | Label | Typ | Pflicht | Optionen |
|---|---|---|---|---|
| 2.1 | Was soll die neue Website vor allem erreichen? | Radio (eine Wahl) | ja | Mehr passende Anfragen · Besser gefunden werden · Neue Mitarbeitende gewinnen · Vertrauen und Professionalität stärken · Termine oder Bewerbungen vereinfachen · Etwas anderes |
| 2.2 | Wen möchten Sie vor allem erreichen? | Radio | ja | Privatkunden · Unternehmen · Bewerberinnen und Bewerber · Mehrere Gruppen · Noch unklar |

Hilfetext 2.1: „Wählen Sie das Ziel, das in den nächsten zwölf Monaten den größten Unterschied machen würde."
Fehlermeldung: „Bitte wählen Sie ein Hauptziel."

**Thema 3 — Umfang**

| Feld | Label | Typ | Pflicht | Optionen |
|---|---|---|---|---|
| 3.1 | Was trifft auf Ihr Unternehmen zu? | Checkbox (mehrere) | ja | Wir haben ein klares Hauptangebot · Wir bieten mehrere eigenständige Leistungen an · Wir arbeiten in mehreren Regionen oder an mehreren Standorten · Wir suchen regelmäßig Mitarbeitende · Projekte, Referenzen oder Neuigkeiten sollen aktuell bleiben · Nichts davon / bin unsicher |

Hilfetext bei „mehrere eigenständige Leistungen": „Gemeint sind Angebote, nach denen Kunden getrennt suchen oder für die sie eine eigene Erklärung brauchen."
Regel: „Nichts davon / bin unsicher" ist **nicht** mit anderen Optionen kombinierbar.
Fehlermeldung Kombination: „‚Nichts davon' lässt sich nicht mit anderen Angaben kombinieren. Bitte wählen Sie das eine oder das andere."

**Thema 4 — Besondere Anforderungen (Gates)**

| Feld | Label | Typ | Pflicht | Optionen |
|---|---|---|---|---|
| 4.1 | Muss die Website etwas Besonderes können? | Checkbox (mehrere) | ja | Normale Anfrage oder Bewerbung über ein Formular · Einfache Terminbuchung · Produkte verkaufen oder Zahlungen annehmen · Kundenlogin oder geschützter Bereich · Verbindung zu anderer Software · Mehrere Sprachen oder getrennte Marken · Besondere Daten oder ein formaler Nachweis · Nichts davon, eine normale Firmenwebsite |

Je Option ein Beispielsatz als Hilfetext (z. B. bei „Verbindung zu anderer Software": „Zum Beispiel Warenwirtschaft, CRM oder eine eigene Schnittstelle.").
Regel: „Nichts davon" nicht mit einer Sonderfunktion kombinierbar (gleiche Fehlermeldung wie 3.1).

**Thema 5 — Domain und Termin**

| Feld | Label | Typ | Pflicht | Optionen / Hilfetext |
|---|---|---|---|---|
| 5.1 | Wie ist Ihr Domainstatus? | Radio | ja | Domain vorhanden · Neue Domain nötig · Bin unsicher |
| 5.2 | Gibt es einen festen Termin, der eingehalten werden muss? | Radio | ja | Nein, der normale Zeitrahmen passt · Ja (Datum + kurzer Grund) |
| 5.3 | Datum und Grund | Datum + Text, nur bei „Ja" | ja (bedingt) | Hilfetext: „Ein Wunschdatum ist noch keine Zusage — wir bestätigen die Machbarkeit im Angebot." |
| 5.4 | Gibt es etwas, das auf keinen Fall übersehen werden darf? | Textarea | nein | Platzhalter: „Zum Beispiel: Unsere bestehenden E-Mail-Adressen müssen weiterlaufen." |

### 9.3 Ergebnis **vor** Kontaktdaten

Anzeige (Beispiel Platzhirsch):
> **Unsere vorläufige Empfehlung: Platzhirsch**
> Sie erklären mehrere Leistungen, arbeiten in mehr als einer Region und suchen regelmäßig Mitarbeitende. Dafür reicht eine einzelne Seite nicht — hier lohnt sich eine Struktur, die Leistungen, Region und Recruiting getrennt bedient.
> **7.900 € einmalig + 249 €/Monat Rundum-Schutz** · Erstes Jahr: **10.888 € netto**

Darunter immer:
> Alle Preise netto zzgl. gesetzlicher Umsatzsteuer. Ausschließlich für Unternehmer. **Verbindlich ist erst das von SARTU geprüfte Angebot.**

- Button: `Empfehlung unverbindlich prüfen lassen`
- **Keine** Paketwechsel-Buttons, **keine** Zusatzoptionen, **keine** SEO-Auswahl.
- Bei Sonderprojekt-Gate: „Ihr Vorhaben enthält eine besondere Funktion. Solche Projekte beginnen bei 12.500 € einmalig zzgl. Betrieb. Sie erhalten dazu ein kurzes Fachmodul und danach einen geprüften Gesamtpreis."
- Bei Unklarheit: „Ihr Bedarf passt voraussichtlich in eine unserer drei Lösungen. Eine Angabe entscheidet noch über den Umfang — nach dem Absenden stellen wir Ihnen höchstens eine gebündelte Rückfrage."

### 9.4 Kontaktdaten (erst danach)

| Feld | Label | Pflicht | Fehlermeldung |
|---|---|---|---|
| Name | Vor- und Nachname | ja | „Bitte geben Sie Ihren Namen an." |
| Unternehmen | Unternehmen | ja | „Bitte geben Sie Ihr Unternehmen an." |
| E-Mail | Geschäftliche E-Mail-Adresse | ja | „Bitte geben Sie eine gültige E-Mail-Adresse an, z. B. name@firma.de" |
| Telefon | Telefon (optional) | nein | – |
| Kontaktweg | Bevorzugter Kontakt: E-Mail / Portal | ja | „Bitte wählen Sie, wie wir Sie erreichen sollen." |
| B2B | Checkbox: „Ich handle für mein Unternehmen bzw. in Ausübung meiner beruflichen oder gewerblichen Tätigkeit." | ja | „Bitte bestätigen Sie, dass Sie als Unternehmen anfragen. SARTU arbeitet ausschließlich für Unternehmer." |
| Datenschutz | Checkbox mit Link auf `/datenschutz` | ja | „Bitte bestätigen Sie die Datenschutzhinweise." |

**Kein** Newsletter-Häkchen. **Keine** Pflicht-Telefonnummer. Honeypot-Feld + serverseitige Validierung.

### 9.5 Verhalten

- **Autosave** im Browserspeicher, Wiederaufnahme möglich („Sie können später fortsetzen.").
- **Zurück** jederzeit möglich, ohne Datenverlust.
- Bei reinen Einfachauswahlen darf automatisch weitergeblättert werden; bei Mehrfachauswahl **nicht**.
- Fehler erscheinen **am Feld**, nicht als Sammelmeldung oben; erstes fehlerhaftes Feld erhält den Fokus.
- Mobil: ein Sachverhalt pro Bildschirm, Buttons in Daumenreichweite, Tastaturtyp passend (E-Mail, Telefon, Zahl).
### 9.5a Bedienbarkeit ohne JavaScript (verbindlich, keine Kann-Regel)

Der Bedarfsscheck ist der einzige Weg zu einem Angebot. Er darf an abgeschaltetem JavaScript **nicht scheitern**. Gebaut wird deshalb **zuerst** die Fassung ohne JavaScript; die Komfortfunktionen kommen darüber.

| | **Ohne JavaScript** (Grundfassung, muss funktionieren) | **Mit JavaScript** (Komfort obendrauf) |
|---|---|---|
| Schritte | echte Seiten `/briefing/1` … `/briefing/n`, je Schritt ein `POST`, Server antwortet mit dem nächsten Schritt | dieselben Schritte ohne Neuladen |
| Zwischenstand | serverseitig in einer kurzlebigen Sitzung (**Ablauf 24 Stunden**, nur Formulardaten, keine Kennung im Klartext in der URL) | zusätzlich im Browserspeicher |
| Zurück | normaler Link auf den vorigen Schritt, Angaben bleiben erhalten | ohne Neuladen |
| Bedingte Fragen | der Server entscheidet, welcher Schritt als Nächstes kommt | dieselbe Regel im Browser |
| Ergebnis vor Kontaktdaten | eigene Seite, serverseitig berechnet | gleiche Anzeige |
| Fehler | Neuanzeige des Schritts, Meldung am Feld, erstes fehlerhaftes Feld erhält den Fokus | ohne Neuladen |
| Fortschritt | `Schritt 3 von 8` als Text | zusätzlich Fortschrittsbalken |

**Die Empfehlungsregel liegt auf dem Server.** Der Browser darf sie spiegeln, aber die verbindliche Berechnung erfolgt serverseitig — sonst weichen beide Fassungen voneinander ab.

**Kein** Ersatz durch „Schreiben Sie uns einfach eine E-Mail". Eine Kontaktalternative steht zusätzlich da, ersetzt den Bedarfsscheck aber nicht.

### 9.5b Wohin die Anfrage geht (verbindlich)

Maßgeblich ist `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md`, Abschnitt **„4b. Anfrageeingang vom
Bedarfsscheck"**. Bei Abweichungen gilt dort.

| Punkt | Festlegung |
|---|---|
| **Ziel** | `POST /briefing/absenden` — eigene Domain, normales Formular |
| **Weg** | Formularannahme → `AnfrageService::anlegen()` → Tabelle `leads`. **Kein** Netzaufruf, **kein** gemeinsames Geheimnis, **keine** Tokenprüfung (ein Projekt, §1) |
| **CSRF** | Pflicht |
| **`submission_id`** | entsteht beim **Start** des Bedarfsschecks und bleibt über alle Schritte gleich. Doppelklick, Neuladen und Zurück-Taste erzeugen dadurch keinen zweiten Datensatz |
| **Empfehlung und Ampel** | **serverseitig** berechnet, **nie** aus dem abgeschickten Formular übernommen — sonst könnte sie von außen gesetzt werden |
| **Erfolg** | Weiterleitung (`303`) auf die Danke-Seite §9.6. Nie ein erneut absendbares Formular anzeigen |
| **Fehler** | Angaben bleiben erhalten, Meldung **am Feld**. Bei Serverfehler: `Wir konnten Ihre Anfrage gerade nicht speichern. Bitte versuchen Sie es in einem Moment erneut oder schreiben Sie uns an {E-Mail}.` **Keine** technischen Details |
| **Zusätzlich** | Benachrichtigungs-E-Mail an SARTU |

**Herkunft der Anfrage mitgeben:** Beim **ersten** Seitenaufruf werden Landeseite (nur Pfad),
verweisender Hostname, `utm_source/medium/campaign/term/content` und eine vorhandene Anzeigen-
Klickkennung (`gclid`, `gbraid`, `wbraid`) in die serverseitige Sitzung geschrieben und beim
Absenden an den Anfragedienst übergeben. Festlegung: Portal-Lastenheft §4b.7.
**Nicht** erst beim Absenden auslesen — dann sind die Kennzeichen längst weg.

Im letzten Schritt zusätzlich die **freiwillige** Frage `Wie sind Sie auf uns aufmerksam geworden?`
(Auswahl `Suchmaschine` · `Empfehlung` · `Direkt angesprochen worden` · `Anzeige` · `Sonstiges` +
optionales Freitextfeld). **Kein Pflichtfeld** — eine unbeantwortete Frage ist besser als eine
erzwungene Falschangabe.

**Spamabwehr:** Honigtopffeld `hp_website` (unsichtbar, `aria-hidden="true"` und `tabindex="-1"`),
Zeitregel (Absenden unter 3 Sekunden wird stillschweigend verworfen, Danke-Seite erscheint trotzdem),
serverseitige Prüfung aller Felder, Rate-Limit 10 je IP und Stunde. **Kein** Rätselbild und **kein**
Fremddienst zum Start — beides wäre eine externe Verbindung mit eigener Datenschutzfolge.

> **Das allgemeine Kontaktformular** (§11) erzeugt **keinen** Datensatz. Es versendet ausschließlich
> eine E-Mail. Honigtopf, Zeitregel und Rate-Limit gelten dort gleichermaßen.

### 9.6 Danke-Seite (`noindex`)

> **Danke — wir haben Ihre Angaben.**
> Wir prüfen Ihre Anfrage persönlich und melden uns schriftlich, in der Regel innerhalb eines Werktags. Wenn etwas unklar ist, stellen wir genau eine gebündelte Rückfrage.
> Danach erhalten Sie ein Angebot mit Empfehlung, Seitenstruktur, Festpreis, Zahlungsplan und Zeitrahmen.

Kein weiteres Angebot, kein Upsell, keine Zusatz-CTAs.

---

## 10. Die fünf Leistungsseiten

**Gemeinsames Template (verbindlich, in dieser Reihenfolge):**
`H1` → `Kurz gesagt` (40–60 W. Antwortabsatz mit Preisanker) → `Für wen das passt` → `Was enthalten ist` → `Was nicht enthalten ist` → `Was es kostet` → `Wie es abläuft` → `Welche Entscheidung wir Ihnen abnehmen` → `FAQ (3)` → `CTA`
**Umfang je Seite:** 450–650 Wörter · **Schema:** `Service` + `BreadcrumbList` · genau eine H1 (`FAQPage` optional, s. §16)

| # | URL | H1 | Title | Kernaussage („Kurz gesagt") |
|---|---|---|---|---|
| 1 | `/leistung-webdesign` | Webdesign, das nicht nach Baukasten aussieht. | `Webdesign für Firmenwebsites ohne WordPress \| SARTU` | Wir programmieren Ihre Firmenwebsite individuell aus unserem Designsystem — ab 1.490 € netto, ohne WordPress, ohne Baukasten und ohne Plugins, die Sie pflegen müssten. |
| 2 | `/leistung-texte` | Website-Texte aus Ihren Fakten, nicht aus Floskeln. | `Website-Texte schreiben lassen \| SARTU` | Sie liefern Stichpunkte, Unterlagen und Fakten — wir schreiben daraus die Texte Ihrer Website. Erfundene Belege, ungeprüfte Fachaussagen und Rechtstexte sind ausgeschlossen. |
| 3 | `/leistung-seo-lokal` | Gefunden werden — regional und in KI-Antworten. | `SEO-Grundlage und lokale Sichtbarkeit \| SARTU` | Jede SARTU-Website startet mit einer belastbaren SEO-Grundlage: klare Seitenthemen, saubere Metadaten, strukturierte Daten, interne Verlinkung und echte Unternehmensdaten. Ohne Rankinggarantie und ohne dünne Ortsseiten. |
| 4 | `/leistung-wartung` | Keine Wartung für Sie. | `Rundum-Schutz: Betrieb Ihrer Website \| SARTU` | Ab 59 € netto im Monat übernehmen wir den Betrieb: Hosting, SSL, tägliche Backups, Monitoring, technische Updates, technische Suchgesundheit und Ihren Portalzugang. Kein Änderungsminuten-Konto. |
| 5 | `/leistung-portal` | Ein Projektportal, kein Website-Baukasten. | `Das SARTU-Portal: Freigaben und Pflege \| SARTU` | Im Portal laufen Angebot, Zahlung, Briefing, Dateien, Domain, Vorschau, Freigabe und spätere kleine Pflege an einem Ort. Layout, Code und Adressen bleiben bei uns. |

**Pflichtsätze je Seite:**
- 3: „Rankings, Anfragen oder Nennungen in KI-Systemen kann niemand garantieren." + „Wir erstellen keine Ortsseiten, bei denen nur der Stadtname ausgetauscht ist."
- 4: „Der Rundum-Schutz bezahlt Betrieb, Sicherheit und Verantwortung — er ist keine unbegrenzte Text- oder Design-Flatrate."
- 2: „Rechtstexte wie Impressum, Datenschutz und AGB sind nicht enthalten; wir binden freigegebene Texte technisch ein."
- 5: Zwei Listen `Sie können` / `Sie müssen nicht` (Wortlaut wie Startseite Sektion 5).

**Verschoben auf Stufe 2:** `/leistung-domain-launch` sowie die Aufteilung von 3 in getrennte SEO- und Local-SEO-Seiten.

---

## 10a. Branchenseiten — vollständige Zielseiten, keine Durchgangsstationen

**Neu gefasst am 01.08.2026.** Die erste Fassung sah **eine** Seite `/webdesign-handwerk` vor. Das
war falsch: „Handwerk" ist genau die Sammelbezeichnung, die dieses Projekt sonst überall verbietet.
Ein Malerbetrieb, ein Dachdecker und ein Heizungsbauer haben verschiedene Probleme, verschiedene
Bilder und verschiedene Kunden.

### Was Google wirklich verbietet — und was daraus folgt

Wörtlich aus den Spam-Richtlinien:

> *„Doorway abuse is when sites or pages are created to rank for specific, similar search queries.
> They lead users to **intermediate pages** that are not as useful as the final destination."*
>
> Beispiel: *„pages targeted at specific regions or cities that **funnel users to one page**"*

**Das Kriterium ist: Durchgangsstation oder Ziel.** Eine Seite, auf der ein Malermeister alles
erfährt und **direkt beauftragen kann**, ist keine Durchgangsstation — sie ist das Ziel. Damit
fällt der wichtigste Teil des Vorwurfs weg.

**Was bleibt, ist das zweite Kriterium:** *„substantially similar pages"*. Dagegen hilft **kein
Formular**. Nur Inhalt.

> **Die Bedingung in einem Satz:** Jede Branchenseite ist eine vollständige Zielseite mit
> Konfigurator — **und** enthält mindestens **400 Wörter, die auf keiner anderen Seite der Website
> stehen**.

### Aufbau je Seite — vollständige Zielseite

Der Besucher muss die Website **nicht verlassen und nicht weiterklicken**.

| # | Block | Eigen oder geteilt |
|---|---|---|
| 1 | `H1` mit der Branche im Klartext | **eigen** |
| 2 | `Kurz gesagt` — Antwortabsatz mit Preisanker, 40–60 Wörter | **eigen** |
| 3 | `Was {Branche} bei ihrer Website wirklich beschäftigt` — 3–5 echte Probleme dieser Branche | **eigen** |
| 4 | `Was auf die Website eines {Branche} gehört` — als Liste | **eigen** |
| 5 | `Was Sie in dieser Branche beachten müssen` — Rechts- und Fachfragen | **eigen** |
| 6 | `Ein Beispiel` — das Musterprojekt dieser Branche | **eigen** |
| 7 | `Was es kostet` — dieselben Zahlen wie überall | geteilt |
| 8 | `Wie es abläuft` — die sechs Schritte, gekürzt | geteilt |
| 9 | **`Bedarfsscheck` direkt eingebettet, Branche vorausgefüllt** | geteilt |
| 10 | `Häufige Fragen` — **drei**, die nur diese Branche betreffen | **eigen** |

**Umfang:** 900–1.300 Wörter · **Schema:** `Service` + `BreadcrumbList` · genau eine H1

> **Warum Block 2 die Antwort vorwegnimmt, obwohl das Problem erst in Block 3 steht.** Der Aufbau
> bedient zwei Leser gleichzeitig, und das ist Absicht:
>
> | Leser | Was er bekommt |
> |---|---|
> | Wer überfliegt oder aus einer KI-Antwort kommt | Block 2 — Antwort und Preisanker in 40–60 Wörtern |
> | Wer bleibt | Blöcke 3 bis 6 — Problem, Folge, Lösung, Beleg |
>
> **Block 2 ist keine Zusammenfassung des Problems, sondern die Antwort auf die Suchanfrage.**
> Danach beginnt der Bogen von vorn, ausführlich. Wer eine der beiden Ordnungen „aufräumt",
> zerstört die andere.
>
> **Grenze für die Problemphase:** zwischen Block 2 und der ersten Aussage darüber, was SARTU
> liefert, stehen **höchstens 150 Wörter**. Begründung im Texter-Skill unter „PAS".

> **Statistiken auf der Seite — Grenzen aus `SARTU_BRANCHENFAKTEN.md` Abschnitt 1:** höchstens
> **drei** je Seite · **nie im Aufmacher** (Block 1 und 2) · Quelle, Jahr und Stichprobe **an der
> Zahl** · keine Quartalszahlen, die veralten schneller als 12 bis 15 Seiten gepflegt werden
> können · **keine regionalen Werte** auf einer bundesweit auffindbaren Seite.
>
> **Der Filter davor — der Nicken-Test:** Liest der Betrieb die Zahl und denkt „ja, genau"? Dann
> darf sie auf die Seite. Denkt er „bei mir stimmt das nicht", bleibt sie draußen — auch bei
> bester Quelle. Das Risiko ist einseitig: Zustimmung bringt ein Nicken, Widerspruch kostet die
> ganze Seite. Bei Unklarheit wird der Auftraggeber gefragt.

> **Der Konfigurator auf der Seite ist der eigentliche Gewinn** — nicht wegen Google, sondern wegen
> der Abbruchquote. Wer erst zu `/briefing` klicken muss, klickt oft gar nicht. Und die Branche ist
> bereits beantwortet: ein Feld weniger für den Kunden, eine Angabe mehr für uns.
>
> **Technisch:** derselbe Endpunkt und dieselben Schutzmaßnahmen wie in Portal-Lastenheft §4b. Das
> Feld `branche` wird aus der Seite vorbelegt und landet in `leads.payload`. Kein zweiter Weg, kein
> zweites Formular.

### Drei Prüfungen vor der Abgabe, alle hart

**1. Der Austauschtest.** Ersetze das Branchenwort durch ein anderes. Ergibt der Text weiterhin
Sinn, ist es keine Branchenseite, sondern eine Vorlage mit getauschtem Etikett.

Beispiel, das korrekt scheitert: Gesellensuche · Bilder von Baustellen statt von Büros · die
Förderfrage beim Heizungstausch. Bei einer Steuerkanzlei ergibt keiner dieser Punkte Sinn.

**2. Die Eigenanteilsmessung.** Mindestens **400 Wörter** der Seite dürfen **auf keiner anderen
Seite** vorkommen. Prüfbar mit einer Textabgleichsuche über alle Branchenseiten.

**3. Der Herkunftsnachweis — neu am 01.08.2026.** Zu den Blöcken 3, 5, 6 und 10 gehört eine
Quellenzeile: **woher stammt diese Aussage über die Branche?** Zulässig sind nur drei Quellen.

| Quelle | Beispiel |
|---|---|
| **`SARTU_BRANCHENFAKTEN.md`** | **Zahlen je Branche mit Quelle und Verfallsdatum** — deckt Welle 1 ab |
| **`SARTU_KUNDENMOTIVE_BELEGT.md`** | der belegte Kern und die Branchentabelle Ostsachsen |
| Der Auftraggeber | „Chef hat mir gesagt: Förderung erklären ist der häufigste Anruf" |
| Ein Betrieb der Branche | Gespräch, E-Mail, ausgefüllter Fragebogen |
| Eine benannte, öffentliche Fundstelle | Innungsseite, Fachverband, Gesetzestext |

**Nicht zulässig:** was einleuchtend klingt.

> **Warum Prüfung 1 und 2 dafür nicht reichen — an einem echten Fehlschlag.** Der erste Entwurf der
> SHK-Seite argumentierte mit der Notdienstnummer oben auf der Seite. Er bestand den Austauschtest
> mühelos: Notdienst ist branchentypisch, eine Steuerkanzlei hat keinen. Er bestand die
> Eigenanteilsmessung. Er bestand jede Zahl aus `SARTU_TEXTREGELN.md`.
>
> **Und er war trotzdem unbrauchbar.** Eine Telefonnummer oben rechtfertigt keine 2.198 € — das
> kann jeder Baukasten für 500 €. Der Notdienst ist außerdem das Geschäft, das ein
> Sanitärbetrieb am **wenigsten** will; er lebt von Badsanierung und Heizungstausch. Und die
> Begründung *„wer nachts sucht, ruft die erste Nummer an"* war frei erfunden.
>
> **Ein branchentypisches Argument ist nicht automatisch ein tragfähiges.** Genau diese Lücke
> schließt Prüfung 3.

**Reißt eine der drei Prüfungen: Die Seite wird nicht veröffentlicht.** Nicht überarbeitet —
nicht veröffentlicht. Eine Branche, über die sich keine 400 eigenen Wörter schreiben lassen, ist
eine Branche, über die niemand genug weiß.

> **Folge für die Reihenfolge — korrigiert am 01.08.2026.** Die erste Fassung dieses Absatzes
> sperrte Welle 1 komplett, bis je Branche ein Gespräch geführt ist. Das war zu streng: Der
> **universelle Kern ist inzwischen belegt** (`SARTU_KUNDENMOTIVE_BELEGT.md`, fünf Motive mit
> Zahlen), und für Ostsachsen liegen sogar **Werte je Gewerbegruppe** vor.
>
> **Es gilt jetzt getrennt nach Block:** Die Blöcke 1, 2, 7, 8, 9 entstehen aus dem belegten Kern.
> Die Blöcke 3, 5, 6 und 10 brauchen den Herkunftsnachweis. Fehlt er, entsteht die Seite ohne
> diese Blöcke und geht **nicht online** — die 400-Wörter-Prüfung reißt dann ohnehin.
>
> **Damit hängt Welle 1 an je einem Gespräch für den eigenen Teil, nicht an der ganzen Seite.**

### Welche Branchen — gefiltert nach Zahlungsfähigkeit, nicht nach Vollständigkeit

**Nicht jede Branche kann 2.198 € im ersten Jahr zahlen.** Ein Friseursalon mit zwei Stühlen und
eine Bäckerei mit einer Filiale sind keine Zielgruppe, egal wie viele es davon gibt. Die Liste
folgt dem Auftragswert und dem echten Bedarf.

| Welle | Branchen | Warum |
|---|---|---|
| **1 — zum Launch, drei Stück** | Sanitär-Heizung-Klima · Elektrotechnik · Dachdecker | höchste Auftragswerte im Handwerk, akute Personalnot, Notdienstthema. Keine besondere Rechtslage — schreibbar ohne Fachgutachten |
| **2 — nach den ersten Kunden** | Garten- und Landschaftsbau · Tischlerei · Malerbetrieb · Fliesenleger · Kfz-Werkstatt | starker Bildbedarf, mittlere bis hohe Auftragswerte |
| **3 — braucht Rechtskenntnis** | Zahnarztpraxis · Physiotherapie · Steuerkanzlei · Rechtsanwaltskanzlei · Architekturbüro · Immobilienmakler | sehr zahlungsfähig, aber **eigene Berufsrechte**. Erst schreiben, wenn die Regeln geprüft sind |

**Zielgröße: 12 bis 15 Seiten über 18 Monate.** Nicht vierzig.

> **Warum nicht alle auf einmal:** Die Grenze ist nicht Google, sondern **wer die Fachtexte
> schreibt**. Vierzig Seiten mit je 400 eigenen Wörtern sind 16.000 Wörter Fachwissen über
> Berufsrechte, Auftragswege und Branchenprobleme. Ohne dieses Wissen entstehen genau die
> „substantially similar pages", die verboten sind — und die Seiten wären zugleich schlechte
> Verkaufstexte.
>
> **Die Regel bleibt:** eine Branche vollständig, drei bis fünf Referenzen darin, **dann** die
> nächste (Masterkonzept §23b.7). Ab dem ersten Kunden einer Branche schreibt sich ihre Seite fast
> von selbst — die Einwände, die Sprache und das Beispiel liegen dann vor.

**Verboten:** Branchenpreise erfinden · Branchenzahlen ohne Quelle · Rechtsaussagen ohne
Fundstelle · eine Seite veröffentlichen, die eine der beiden Prüfungen reißt

---

## 11. `/ueber-uns` und `/kontakt`

### `/ueber-uns`
**Title:** `Über SARTU — Festpreis, Portal, klare Grenzen | SARTU`
**Meta:** `SARTU baut Firmenwebsites zum Festpreis: klarer Ablauf, geführtes Portal, keine WordPress-Pflege und KI-gestützte Produktion mit menschlicher Prüfung.`
**H1:** `Webdesign mit klaren Grenzen, festen Preisen und Verantwortung.`
**Umfang:** 400–550 Wörter

Sektionen: Hero mit **echtem Foto** (kein Fake-Teamfoto) · „Warum SARTU anders arbeitet" (4 Punkte: Festpreis statt Stundenfalle · Portal statt E-Mail-Chaos · Fakten statt Geschmacksdiskussionen · KI als Werkzeug, nicht als Ersatz) · „Was SARTU bewusst nicht ist" (kein Baukasten · kein WordPress-Hoster · keine Billig-Seitenschleuder · kein Anbieter für Privat- und Hobbyseiten) · Arbeitsweise in 5 Schritten · Verantwortung („Veröffentlicht wird nur, was wir geprüft und freigegeben haben.") · CTA.

**Ehrlichkeitsregel:** Solange Einzelperson — „gründergeführt" schreiben, nicht „unser Team". Kein Platzhalterfoto, das wie ein echtes Foto wirkt.

### `/kontakt`
**Title:** `Kontakt — Rückfrage oder Bedarf prüfen lassen | SARTU`
**Meta:** `Stellen Sie SARTU eine Rückfrage oder starten Sie den kurzen Bedarfsscheck für Ihre Firmenwebsite. Antwort in der Regel innerhalb eines Werktags.`
**H1:** `Kontakt zu SARTU.`
**Umfang:** 250–350 Wörter

Zwei Karten: **`Websitebedarf prüfen`** (primär → `/briefing`) und **`Rückfrage stellen`** (Anker zum Formular).

**Pflichtabschnitt „Wo wir arbeiten"** — direkt unter den beiden Karten:

> ### Wo wir arbeiten
> Bundesweit. Weil es keine Abstimmungstermine gibt, spielt die Entfernung keine Rolle — der Ablauf
> ist überall derselbe.
>
> Unser Sitz ist im Raum Dresden. Betriebe aus Dresden und dem Umkreis besuchen wir bei Bedarf
> persönlich: Meißen, Radebeul, Coswig, Radeberg, Pirna, Heidenau, Freital, Dippoldiswalde,
> Bischofswerda, Bautzen und Sebnitz.

**Die Reihenfolge ist verbindlich: erst bundesweit, dann der Umkreis.** Umgekehrt liest ein Betrieb
aus Kassel „Dresden" und geht.

> **Warum dieser Abschnitt Pflicht ist** (01.08.2026): Die Reichweite stand vorher **nirgends** auf
> der Website. Dabei ist sie die logische Folge des stärksten Verkaufsarguments — *ohne Termin*
> heißt *überall*. Die Ortsliste dient dem Kartenbereich und dem Vertrauen der Nachbarschaft, nicht
> der Abgrenzung.

**Formularfelder:** Name (Pflicht) · Unternehmen (Pflicht) · E-Mail (Pflicht) · Telefon (optional) · Anliegen (Auswahl: Websiteprojekt · Bestehendes Angebot · Domain und Launch · Allgemeine Rückfrage) · Nachricht (Pflicht, min. 20 Zeichen) · Datenschutz-Checkbox (Pflicht) · Honeypot.
**Fehlermeldung Nachricht:** „Bitte beschreiben Sie Ihr Anliegen in ein bis zwei Sätzen."
**Bestätigung:** „Danke — Ihre Nachricht ist angekommen. Wir antworten schriftlich, in der Regel innerhalb eines Werktags." (`noindex`)
**Kein** Dateiupload, **keine** Pflicht-Telefonnummer.

---

## 11a. Transparenzseiten — Pflichtblock

**Maßgeblich ist `SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §3.4.** Diese Seiten sind kein Beiwerk: Sie
sind der Grund, warum SARTU in Suchergebnissen und KI-Antworten überhaupt zitiert wird. Fast jede
Agentur schreibt „Preis auf Anfrage" — SARTU nennt Zahlen. Das ist die Lücke.

**Zum Launch verbindlich:**

| URL | Kern | Was nachprüfbar drinstehen muss |
|---|---|---|
| `/ratgeber/was-kostet-eine-firmenwebsite` | die häufigste Frage im Markt | **SARTUs Zahlen konkret.** Fremde Anbieterarten nur als **Kostenbestandteile und Entscheidungslogik** — keine Markt-, Wettbewerber- oder Preisspannen. Laufende Kosten getrennt ausweisen |
| `/ratgeber/was-nicht-enthalten-ist` | schreibt sonst niemand | vollständige Ausschlussliste im Klartext, plus Begründung, warum es keine Zusatzoptionen gibt |
| `/ratgeber/was-der-betrieb-kostet` | zweithäufigste Rückfrage | was in 59/129/249 € enthalten ist, was nicht, was bei Vertragsende mit Domain und Website passiert |

**Nach dem Launch** (Reihenfolge in der Keywordstrategie §6): `/ratgeber/wie-lange-dauert-eine-website` ·
`/ratgeber/website-festpreis-erkennen` · `/ratgeber/was-eine-korrekturrunde-ist`.

**Harte Regeln:**
- Jede Zahl stammt aus dem **eigenen** Angebot und stimmt. **Keine** Marktdurchschnitte, **keine** Studien, **keine** Wettbewerberpreise
- Über fremde Anbieter nur in **Kategorien** („Baukasten", „Freelancer", „Agentur") — nie mit Namen, nie mit konkreten Preisen
- **Preise als Text, nie als Bild.** Ein Preis in einer Grafik existiert für Suchmaschinen und KI-Systeme nicht
- **Antwort zuerst:** die ersten 40–60 Wörter beantworten die Titelfrage direkt und mit Zahl
- Vergleiche als **Tabelle**, nicht als Fließtext
- Sichtbares Aktualisierungsdatum auf jeder Seite

**Technische Pflicht:** Alle Preise, Umfangsgrenzen, Korrekturrunden und Lieferkorridore stehen an
**einer** Stelle im Code und werden von dort auf allen Seiten ausgegeben. Nie doppelt pflegen.
Eine veraltete Preisangabe ist schlimmer als keine — sie wird zitiert und dann gegen SARTU verwendet.

---

## 12. Ratgeber — zwei Vergleichsartikel mit Gliederung

> **Abgrenzung zu §11a:** Wo **Zahlen** im Mittelpunkt stehen, ist es eine Transparenzseite. Hier
> geht es um **Entscheidungen** zwischen Optionen. `was-kostet-eine-firmenwebsite` steht deshalb in
> §11a und **nicht** hier.

**Hub `/ratgeber`:** H1 `Ratgeber für Firmenwebsites` · Kurzintro (2 Sätze) · Artikelliste mit Titel, Kurzantwort, Datum · **kein** Kategorienfilter bei wenigen Artikeln. Der Hub listet Ratgeber **und** Transparenzseiten (§11a), weil sie für Leser dasselbe sind.
**Je Artikel:** H1 mit Suchintention · **Kurzantwort in den ersten 2 Sätzen** · Aktualisierungsdatum sichtbar · Tabelle oder Entscheidungslogik · mindestens 2 interne Links · CTA · `Article`-Schema. **Umfang 900–1.300 Wörter.**

**1. `/ratgeber/agentur-freelancer-baukasten`**
H1: `Website erstellen lassen: Agentur, Freelancer oder Baukasten?` · Title: `Agentur, Freelancer oder Baukasten? Der ehrliche Vergleich | SARTU`
Kurzantwort: Ein Baukasten lohnt sich, wenn Sie selbst pflegen wollen und wenig Anspruch an Struktur haben. Ein Freelancer ist günstig, aber ein Ausfallrisiko. Eine Agentur liefert Verlässlichkeit — meist ohne Festpreis und mit mehr Terminen, als Ihnen lieb ist.
Gliederung: Die vier Anbieterarten in einer Tabelle (**wie sich die Kosten zusammensetzen** — nicht welche Beträge andere verlangen —, Zeitaufwand für Sie, Risiko, Pflege danach) → Wann ein Baukasten wirklich reicht → Was am Freelancer-Modell schiefgehen kann → Warum Agenturen selten Festpreise nennen → **Für wen SARTU nicht passt** → Entscheidungshilfe in fünf Fragen → CTA `/preise`.
**Pflicht:** ehrlich bleiben, und **keine fremden Preise erfinden**. Die Spalte beschreibt, *woraus* der Preis bei der jeweiligen Anbieterart entsteht (Stundensatz × Stunden, Softwaregebühr + Eigenleistung, Festpreis), nicht *wie hoch* er ist. SARTUs eigene Zahlen stehen konkret daneben — sie sind die einzigen belegten auf der Seite. Wenn ein Baukasten für einen Fall reicht, steht das so da. Genau das macht die Seite glaubwürdig.

**2. `/ratgeber/webdesign-ohne-wordpress`**
H1: `Firmenwebsite ohne WordPress: Wann sich das lohnt` · Title: `Firmenwebsite ohne WordPress — Vorteile und Grenzen | SARTU`
Kurzantwort: Ohne WordPress entfallen Plugin-Updates, Sicherheitslücken und Kompatibilitätsprobleme. Der Preis dafür ist weniger Selbstbedienung — Inhalte ändert nicht mehr jeder selbst.
Gliederung: Warum WordPress so verbreitet ist → Was daran im Alltag Arbeit macht → Alternativen (Baukasten, statisch, individuell) → **Was man dabei aufgibt** → Für wen sich was eignet (Entscheidungstabelle) → Wie SARTU es löst → CTA `/leistung-webdesign`.

**Nach dem Launch** (Reihenfolge in `SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §6):
`agentur-auswaehlen-kriterien` · `relaunch-sinnvoll` · `website-handwerker-fehler` · `bfsg-firmenwebsite`.

> **Bei „Agentur auswählen" später:** Kriterien nennen, **keine** Rangliste. Eine Seite, auf der SARTU
> sich selbst zur besten Wahl erklärt, ist unglaubwürdig und wettbewerbsrechtlich riskant.

---

## 13. Lexikon — 8 Startbegriffe (final sortiert)

**Hub `/lexikon`:** H1 `Website-Lexikon` · Kurzintro · alphabetische Liste mit Ein-Satz-Definition · **kein** Suchfeld bei 8 Begriffen (erst ab ca. 40).
**Begriffsseite (8 Teile, verbindlich):** H1 = Begriff · Kurzdefinition (2–3 Sätze) · Warum es für Firmenwebsites wichtig ist · Beispiel · Typischer Fehler · Wie SARTU damit umgeht · Verwandte Begriffe (2–4) · Link zur passenden Leistungsseite.
**Umfang:** 250–400 Wörter · **Schema:** `DefinedTerm` (Fallback `Article`) · `BreadcrumbList`

| # | Begriff | URL | Verweist auf |
|---|---|---|---|
| 1 | Firmenwebsite | `/lexikon/firmenwebsite` | `/leistung-webdesign` |
| 2 | Festpreis | `/lexikon/festpreis` | `/preise` |
| 3 | Hosting | `/lexikon/hosting` | `/leistung-wartung` |
| 4 | Domain | `/lexikon/domain` | `/leistung-wartung` |
| 5 | Relaunch | `/lexikon/relaunch` | `/leistung-webdesign` |
| 6 | Barrierefreiheit | `/lexikon/barrierefreiheit` | `/leistung-webdesign` |
| 7 | Local SEO | `/lexikon/local-seo` | `/leistung-seo-lokal` |
| 8 | GEO (KI-Suche) | `/lexikon/geo-ki-suche` | `/leistung-seo-lokal` |

**Auswahlregel:** nur Begriffe, die in einem echten Verkaufsgespräch vorkommen und bei denen ein
Missverständnis Geld kostet. **Nicht** jeder Fachbegriff, den es gibt.

**Stufe 2 nach Search-Console-Daten** (nicht zum Launch): Backup · Canonical · Core Web Vitals ·
DNS · One-Pager · Schema.org · Suchintention · Weiterleitung (301) · SEO · CMS · SSL · Sitemap.


Ausbau auf 40–60 Begriffe erst in Stufe 2, gesteuert über Search-Console-Daten.

---

## 14. Pflicht- und Systemseiten

| Seite | Regel |
|---|---|
| `/impressum` | Vollständig nach § 5 DDG, **keine** Platzhalter live. Daten identisch zu Footer und strukturierten Daten. Bis zur Standortentscheidung nicht öffentlich. |
| `/datenschutz` | Hosting, Serverlogs, Kontaktformular, Bedarfsscheck, Portal-Verweis, KI-Verarbeitung (soweit personenbezogen), Statistik, eingebundene Dienste. **Keine** Aussage „rechtssicher". |
| `/agb` | Nur live und verlinkt, wenn anwaltlich final. Sonst **gar nicht** verlinken und `noindex`. |
| 404 | H1 `Diese Seite gibt es nicht.` · Text: „Vielleicht wurde die Adresse geändert oder eine alte Seite ist umgezogen." · Links: Startseite, Leistungen, Preise, Bedarf prüfen lassen · echter 404-Status · `noindex`. |
| Danke-Seiten | `noindex`, klare nächste Erwartung, keine weiteren Angebote. |

### 14a. Startsperre — die Veröffentlichung muss scheitern, nicht warnen

> **Geändert 28.07.2026:** Die Sperre prüft **nicht mehr auf Platzhalter in Vorlagen**, sondern auf
> den Zustand der Betreiberdaten im internen Bereich (Portal-Lastenheft §1.4a). Anschrift,
> Kontaktdaten und Steuerangaben stehen nicht mehr im Quelltext.
>
> **Die Veröffentlichung bricht ab, wenn:** ein Pflichtfeld der Betreiberdaten leer ist · weder
> Umsatzsteuer-Identifikationsnummer noch Steuernummer gesetzt ist · einer der Rechtstexte noch
> im Zustand `entwurf` oder `in_pruefung` steht.

Ein Platzhalter, der versehentlich live geht, ist bei Impressum und Datenschutz ein **Rechtsverstoß**,
kein Schönheitsfehler. Eine Warnung im Protokoll reicht nicht — sie wird überlesen.

**Der Veröffentlichungsvorgang für Produktion (`APP_ENV=production`) bricht mit Fehler ab, wenn
eine dieser Bedingungen zutrifft:**

1. `/impressum` oder `/datenschutz` enthält die Platzhaltermarkierung `[[PLATZHALTER]]` oder ist kürzer als 500 Zeichen
2. `/agb` existiert als Seite **und** ist irgendwo verlinkt **und** enthält die Platzhaltermarkierung
3. Eine Seite mit `noindex` steht in der `sitemap.xml`
4. Ein Bildplatz für Portal-Screenshots ist noch leer oder trägt die Markierung `[[SCREENSHOT-FEHLT]]`
4a. **Die Sektion „Wer dahintersteckt" enthält einen Platzhalter** — kein Foto hinterlegt, oder der Text enthält `Name wird nachgereicht`, `[[PLATZHALTER]]` oder `[[FOTO-FEHLT]]`

    > **Neu am 30.07.2026, echte Lücke.** Bedingung 4 sperrte nur **Portal**-Screenshots. Das
    > Gründerfoto ist kein Portal-Screenshot, und `Name wird nachgereicht` steht in keiner
    > Verbotsliste — die Sektion wäre mit leerem Rahmen und ohne Namen live gegangen. Ohne
    > Referenzen ist die Person hinter SARTU der Vertrauensanker; ein leerer Rahmen an dieser
    > Stelle wirkt schlechter als gar keine Sektion. Der Befund kam von außen und war richtig.
5. Eine Zeichenkette aus der Verbotsliste §2 kommt im ausgelieferten Text vor
6. Eine Datei außerhalb von `/public` ist über den Webserver erreichbar
7. Ein Ortsname erscheint in Title, H1 oder URL, obwohl `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1 auf `offen` steht
8. **Ein Rechtstext in `legal_texts` steht auf `entwurf` oder `in_pruefung`** — geprüft werden **alle fünf** Slugs: `impressum` · `datenschutz` · `agb` · `avv` · `tom`

   > **Ergänzt am 31.07.2026 nach dem Audit.** Die Sperre kannte nur drei Rechtstexte. Der
   > Auftragsverarbeitungsvertrag und die technischen und organisatorischen Maßnahmen fehlten im
   > Datenmodell vollständig — obwohl SARTU als Auftragsverarbeiter für den Kunden nach Art. 28
   > DSGVO ohne sie nicht arbeiten darf (Portal-Lastenheft §15.2).

**Fehlermeldung** (Beispiel, muss die Ursache benennen):
```
VEROEFFENTLICHUNG ABGEBROCHEN: /datenschutz enthaelt noch [[PLATZHALTER]].
Rechtstexte muessen final sein, bevor die Seite oeffentlich geht.
```

**Der Staging-Vorgang bricht nicht ab**, sondern listet dieselben Punkte als Warnung auf. So lässt sich
alles vorbereiten und ansehen, ohne dass etwas Unfertiges live gehen kann.

Alle Platzhalter tragen **eine** einheitliche, suchbare Markierung: `[[PLATZHALTER]]` beziehungsweise
`[[SCREENSHOT-FEHLT]]`. Keine freien Formulierungen wie „TODO" oder „Lorem ipsum".

---

## 15. Bild- und Screenshot-Liste (mit Maßen)

**Alle Bilder:** WebP (AVIF optional), `srcset` mit 1×/2×, feste `width`/`height`, echter Alt-Text, sprechender Dateiname. Hero **nicht** lazy (`fetchpriority="high"`), alles darunter `loading="lazy"`.

| Datei | Verwendung | Ausgabemaß (1×) | Seitenverhältnis | Alt-Text |
|---|---|---|---|---|
| `sartu-portal-cockpit-muster.webp` | Startseite Hero | 720 × 540 | 4:3 | Musteransicht des SARTU-Portals mit Projektstatus und nächstem Schritt |
| `sartu-portal-briefing-muster.webp` | Startseite Portal-Sektion, `/leistung-texte` | 960 × 600 | 8:5 | Musteransicht der Briefing-Aufgaben im SARTU-Portal |
| `sartu-portal-angebot-muster.webp` | `/ablauf`, `/preise` | 960 × 600 | 8:5 | Musteransicht eines Angebots mit Umfang, Preis und Zahlungsplan |
| `sartu-portal-domain-muster.webp` | `/ablauf` | 960 × 600 | 8:5 | Musteransicht der Domainvorschläge mit drei geprüften Optionen |
| `sartu-portal-vorschau-muster.webp` | `/ablauf`, `/leistung-portal` | 960 × 600 | 8:5 | Musteransicht von Vorschau und gebündeltem Feedback |
| `sartu-portal-pflege-muster.webp` | `/leistung-wartung` | 960 × 600 | 8:5 | Musteransicht der Öffnungszeiten-Pflege im Portal |
| `sartu-leistungslandkarte.svg` | `/leistungen` | 1200 × 520 | – | Übersicht der SARTU-Leistungen von Strategie bis Betrieb |
| `sartu-portrait.webp` | `/ueber-uns` | 640 × 800 | 4:5 | Nils Haake von SARTU bei der Arbeit |
| `sartu-og-standard.webp` | Open Graph global | 1200 × 630 | 1.91:1 | – |

**Screenshot-Crops:** Portal-Screens werden **ohne** Browser-Chrome aufgenommen, mit 24 px Innenabstand um den Inhalt, im Verhältnis 8:5. Sichtbare Daten sind Musterdaten — keine realistischen Rechnungsnummern, keine echten Personennamen, keine echten Kundenlogos. Badge „Musteransicht" wird **im Layout** gesetzt, nicht ins Bild gebrannt (bleibt so übersetzbar und barrierefrei).

---

## 16. SEO-Übersicht aller Launch-URLs

| URL | Index | Schema | Priorität Sitemap |
|---|---|---|---|
| `/` | index | Organization, WebSite | 1.0 |
| `/leistungen` | index | Service, Breadcrumb | 0.9 |
| `/preise` | index | Service, Breadcrumb | **1.0** |
| `/ablauf` | index | Breadcrumb | 0.8 |
| `/briefing` (Einstiegsseite) | index | Breadcrumb | 0.7 |
| `/briefing/1` … `/briefing/n` (Schritte) | **noindex** | – | – |
| `/leistung-*` (5, s. §11) | index | Service, Breadcrumb | 0.7 |
| `/ratgeber` + 3 Transparenzseiten (§11a) | index | Article, Breadcrumb | **0.9** |
| `/ratgeber` + 2 Vergleichsartikel (§12) | index | Article, Breadcrumb | 0.7 |
| `/ueber-uns` | index | Person oder Organization, Breadcrumb | 0.6 |
| `/kontakt` | index | Breadcrumb | 0.6 |
| `/lexikon` + 8 Begriffe | index | DefinedTerm, Breadcrumb | 0.5 |
| `/impressum`, `/datenschutz` | index | – | 0.3 |
| `/agb` | noindex bis final | – | – |
| Danke-Seiten, 404 | noindex | – | – |

> **Warum `FAQPage` nicht mehr in der Tabelle steht:** Google hat FAQ-Rich-Results im September 2023
> auf staatliche und medizinische Seiten beschränkt und laut Dokumentationsstand vom **15.06.2026**
> ganz eingestellt. Das Markup schadet nicht, bringt aber **keine** Sichtbarkeit mehr. **Die
> FAQ-Blöcke selbst bleiben Pflicht** — als Inhalt für Leser und als zitierfähiger Absatz für
> KI-Antworten, nicht wegen des Schemas. Wer `FAQPage` trotzdem ausliefert, darf es nicht als
> Maßnahme führen.

> **Warum `/preise` und die Transparenzseiten die höchste Priorität haben:** Sie tragen den einzigen
> Sichtbarkeitsvorteil, den SARTU gegenüber etablierten Agenturen hat — veröffentlichte, überprüfbare
> Zahlen in einem Markt, der „Preis auf Anfrage" schreibt (`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §3.4).

**Root-Dateien:** `/sitemap.xml` · `/robots.txt` · `/llms.txt` (ohne Ranking-Behauptung) · `/favicon.ico` + PNG-Größen · OG-Bild.
**Nach Go-live:** Search Console und Bing Webmaster Tools einrichten, Sitemap einreichen.
**`LocalBusiness` wird erst ausgeliefert, wenn der Standort entschieden und real ist.**

---

## 17. Definition of Done

Die Website ist fertig, wenn **alle** Punkte erfüllt sind:

**Inhalt und Aussagen**
- [ ] Keine verbotenen Wörter aus §2 auffindbar (Volltextsuche: „wartungsarm", „rechtssicher", „garantiert", „Paket wählen").
- [ ] Preishinweis „netto zzgl. USt. · ausschließlich für Unternehmer" auf jeder preisführenden Seite.
- [ ] Platzhirsch ist sichtbar die Empfehlung; kein Paket direkt kaufbar; keine Add-on-Liste; keine Änderungsminuten.
- [ ] Keine Fake-Referenzen, -Logos, -Bewertungen, -Adressen; Portal-Screens als „Musteransicht" markiert.
- [ ] Keine Ortsangabe, kein `LocalBusiness`, keine Ortsseite, keine Ortsnamen in Title/H1/URL — solange `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1 auf `offen` steht. Strukturierte Daten nutzen `Organization` **ohne** Adressfeld.

**Technik und SEO**
- [ ] `php -l` läuft über jede PHP-Datei ohne Fehler.
- [ ] Jede Seite: Status 200, genau eine H1, eigener Title und Description, Canonical auf sich selbst, Breadcrumb.
- [ ] Keine toten internen Links; Sitemap enthält nur 200er-URLs; robots.txt korrekt.
- [ ] Alle Bilder mit Alt-Text, festen Maßen, WebP und `srcset`; Hero nicht lazy.
- [ ] **Laborwerte** im Zielbereich, mobil gemessen: LCP < 2,5 s · INP-Ersatzmessung (Total Blocking Time) < 200 ms · CLS < 0,1. Werkzeug und Version nennen. *Echte Core Web Vitals sind Felddaten und existieren erst nach Wochen im Livebetrieb — sie sind kein Abnahmekriterium, sondern eine Nachmessung (§17a).*
- [ ] Seite ohne JavaScript grundlegend nutzbar; kein Inhalt erscheint erst durch Scroll-Animation.
- [ ] **JS-Budget eingehalten:** ≤ 75 KB gzip Startseite, ≤ 40 KB Unterseiten (gemessen, nicht geschätzt).
- [ ] **`prefers-reduced-motion` getestet:** alle nicht-essenziellen Bewegungen aus, Inhalte sofort sichtbar.
- [ ] Maximal zwei bewusste Markenmomente pro Seite; keine Animation über Text oder CTA.
- [ ] Keine Ortsseite ohne bestandenes Indexierungs-Gate (Masterkonzept §16a).

**Bedienung**
- [ ] Mobil und Desktop geprüft; kein horizontales Scrollen des Seitenkörpers.
- [ ] Tastaturbedienung vollständig, Fokus sichtbar, Skip-Link vorhanden, mobiles Menü mit Fokusfalle und `Esc`.
- [ ] Kontrast mindestens 4,5:1 für Fließtext.
- [ ] Bedarfsscheck: alle Fehlermeldungen erscheinen am Feld, Autosave funktioniert, „Nichts davon"-Regel greift.
- [ ] Beide Formulare senden nachweislich; Bestätigungsseiten sind `noindex`.

**Formulare und Schnittstelle**
- [ ] Bedarfsscheck **vollständig ohne JavaScript** durchlaufbar (§9.5a) — mit abgeschaltetem JS getestet, nicht nur behauptet.
- [ ] Doppelklick, Neuladen und Zurück-Taste erzeugen **keinen** zweiten Datensatz (`submission_id`, §9.5b).
- [ ] Empfehlung und Ampelkennzeichen entstehen **serverseitig**; ein manipuliertes Formularfeld ändert sie nicht.
- [ ] **Nur `/public` ist über den Webserver erreichbar** — `/app`, `/storage`, `.env` liefern 403 oder 404.
- [ ] Honigtopf und Zeitregel greifen; ein gefülltes Honigtopffeld erzeugt trotzdem die normale Danke-Seite.
- [ ] Kein Netzwerkaufruf des Browsers geht an eine **fremde** Domain — im Netzwerkprotokoll geprüft.

**Ortsseiten**
- [ ] **Keine** Ortsseite in der produktiven Veröffentlichung — auch nicht als unverlinkter Entwurf.
- [ ] Falls Prototypen existieren: nur in Staging, mit `noindex`, **nicht** in der Sitemap, **nicht** intern verlinkt, `robots.txt` schließt sie aus.
- [ ] Veröffentlichung erst nach dem Gate in Masterkonzept §16a — die Entscheidung trifft ein Mensch, nicht der Bau.

**Vor dem Livegang zwingend**
- [ ] **`KEYWORD_VALIDATION.md`** liegt vor und ist je Launch-Adresse ausgefüllt (`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §1.1). Ohne diese Datei sind Title, H1 und URL nicht bestätigt.
- [ ] **`GEO_DISCOVERY_CHECKLIST.md`** vollständig abgehakt, Ergebnis je Punkt dokumentiert.
- [ ] Herkunftserfassung geprüft: eine Testanfrage mit `?utm_source=test&utm_medium=audit` landet mit den Werten im Datensatz (§9.5b, Portal-Lastenheft §4b.7).
- [ ] `robots.txt` sperrt weder `Googlebot`, `Bingbot` noch `OAI-SearchBot`; die `GPTBot`-Entscheidung ist dokumentiert.

**Recht**
- [ ] Impressum und Datenschutz final und vollständig (keine Platzhalter).
- [ ] AGB entweder final oder nicht verlinkt und `noindex`.
- [ ] Consent-Banner nur, wenn zustimmungspflichtige Dienste eingebunden sind — sonst keiner.
- [ ] **Startsperre nachgewiesen (§14a):** Die produktive Veröffentlichung bricht bei einem Platzhalter in Impressum oder Datenschutz nachweislich ab — einmal absichtlich provoziert und im Bericht belegt.

---

## 17a. Was erst nach dem Livegang geprüft wird

Diese Punkte gehören **nicht** in die Abnahme, weil sie vor dem Livegang gar nicht messbar sind. Sie
werden 4 und 12 Wochen nach dem Start nachgehalten:

| Nach 4 Wochen | Nach 12 Wochen |
|---|---|
| Echte Core Web Vitals aus Felddaten (Search Console / CrUX), sofern genug Zugriffe | dieselben, belastbarer |
| Indexierungsstand aller Launch-URLs | Suchanfragen, für die die Seite erscheint |
| Fehler in der Search Console | Verhältnis Zugriffe → gestartete Bedarfsschecks → abgeschickte Bedarfsschecks |
| Tatsächliche Spamlast am Formular | Entscheidung, ob Schutzmaßnahmen nachgerüstet werden müssen |

**Wichtig:** Bleiben die Felddaten hinter den Laborwerten zurück, ist das ein Auftrag zur
Nachbesserung — kein Grund, die Abnahme rückwirkend infrage zu stellen.

---

*Ende Lastenheft. Offene Punkte sind ausschließlich die zwei Entscheidungen aus §0.*
