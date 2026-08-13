# SARTU — offene Entscheidungen und Platzhalter

**Zweck:** Eine einzige Stelle für alles, was noch nicht entschieden ist. Alle Bauunterlagen
verweisen hierher, statt eine Annahme fest einzubauen. Wer hier einen Wert einträgt, schaltet ihn
im ganzen Projekt frei — ohne dass jemand zehn Dateien durchsucht.

**Regel für die ausführende KI:** Ein Platzhalter aus dieser Datei darf **niemals** durch einen
erfundenen Wert ersetzt werden. Steht er auf `offen`, gilt die dort genannte Sperre.

---

## 1. Startregion und Geschäftsadresse — **teilentschieden, Orte entschieden 01.08.2026**

| Platzhalter | Wert | Stand |
|---|---|---|
| `[STARTREGION]` | **Raum Dresden** | entschieden |
| `[HAUPTORT]` | **Dresden** | entschieden |
| Einzugsgebiet | **Dresden + Umkreis**, Liste unten | **entschieden 01.08.2026** — alle Orte ins Profil und in den Fließtext |
| Eigene Ortsseiten | **nur `/webdesign-dresden`** zum Start | entschieden — weitere werden verdient, nicht verteilt |
| `[HEIMATORT]` | *offen* | nur wenn er ein echter Vertrauensanker ist |
| `[GESCHAEFTSADRESSE_STATUS]` | *offen* | **blockiert weiterhin das Google-Unternehmensprofil** |

### Was die Standortentscheidung regelt — und was nicht

> **Berichtigt am 01.08.2026.** Die frühere Fassung dieses Abschnitts las sich, als sei SARTU ein
> regionales Unternehmen. Das ist falsch, und der Betreiber hat zu Recht widersprochen.

**Sie regelt genau zwei Dinge:**

1. das **Google-Unternehmensprofil** und damit den Kartenbereich
2. ob und wo **eigene Ortsseiten** entstehen

**Sie regelt nicht, wo SARTU arbeitet.** Das Produkt kommt ohne einen einzigen Termin aus.
Entfernung spielt in der Lieferung keine Rolle — ein Malermeister in Kassel wird genauso bedient
wie einer in Radeberg. **Der Markt ist Deutschland.**

#### Von zehn Kanälen ist genau einer ortsgebunden

Nach `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` §23b.2:

| Kanal | Ortsgebunden? |
|---|---|
| Multiplikatoren — Steuerberater, Kammern, Werbetechniker | teilweise. Das Gespräch ist örtlich, die Empfehlung nicht |
| Verwaiste Bestandskunden | **nein** |
| Trigger-Events | **nein** |
| Google Ads | **nein** — bundesweit schaltbar, ab Tag 1 |
| **Transparenzseiten mit veröffentlichten Preisen** | **nein** — laut §23b.2 der **stärkste eigene Hebel** |
| **Branchen-Spirale** (§23b.7) | **nein** — der eigentliche nationale Motor |
| SEO und Inhalte im Übrigen | **nein** |
| Auffindbarkeit in KI-Antworten | **nein** |
| **Google-Unternehmensprofil und Kartenbereich** | **ja — der einzige** |
| Ortsseiten | ja, und laut §23b.2 der schwächste: Wirkung erst Monat 12–24 |

**Dresden ist kein Markt, sondern ein Gratis-Kanal.** Das Unternehmensprofil kostet nichts, hängt
an der Adresse und bringt Anfragen im Umkreis. Das nimmt man mit — nicht weil SARTU regional wäre,
sondern weil es geschenkt ist.

#### Wie SARTU deutschlandweit gefunden wird

Der Weg führt **nicht** über Ortsnamen. Er führt über drei Dinge, die alle bundesweit wirken und
alle ab Tag eins gebaut werden können:

| Weg | Warum er national trägt |
|---|---|
| **Branchenseiten** | „Website für Physiotherapiepraxis" hat überall dieselbe Nachfrage. Kein Grenzaufwand je Markt (`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §0.3) |
| **Transparenzseiten** | Der Markt schreibt „Preis auf Anfrage". Wer echte Zahlen veröffentlicht, wird zitiert — in der Suche **und** in KI-Antworten |
| **Die Positionierung selbst** | „Website ohne Termin", „Festpreis ohne Beratungsgespräch". Winzige Suchmengen, aber die genaueste Kaufabsicht, die es gibt — und praktisch kein Wettbewerb |

**Warum nicht über den Begriff „Webdesign":** Dort stehen bundesweit tausende Agenturen mit Jahren
Vorsprung. Eine neue Domain gewinnt das nicht. Die Keywordstrategie schließt `Webdesignagentur`
als Zielbegriff deshalb ausdrücklich aus.

**Warum nicht über viele Ortsseiten:** Vierhundert Städte mal eine dünne Seite ist der
Doorway-Tatbestand. Und selbst wenn nicht: Eine Seite für Flensburg wirkt **nur** in Flensburg.
Eine Branchenseite wirkt überall.

#### Ehrlich zum Zeitrahmen

| Kanal | Erste Anfragen |
|---|---|
| Multiplikatoren, Direktansprache, Google Ads | **Woche 2 bis 8** |
| Transparenz- und Branchenseiten | **Monat 3 bis 9** |
| Kartenbereich in Dresden | Monat 2 und später, sobald Bewertungen da sind |
| Organische Suche im Übrigen | **Monat 9 bis 18** |

**Die ersten fünf Kunden kommen nicht über Suchmaschinen.** Sie kommen über Menschen. Das steht so
schon in §23b.2 — Multiplikatoren stehen dort auf Platz eins, Ortsseiten auf Platz zehn.

### Was damit freigeschaltet ist

- Ortsnamen im Fließtext, in Titeln und Adressen — **für die Startregion**
- `/webdesign-dresden` und Umlandseiten, **nach dem Gate in Masterkonzept §16a**
- Service-Area-Definition
- Ortsnennung im Fußbereich und auf `/kontakt` — **zusätzlich** zur bundesweiten Aussage, nicht statt ihr

### Was weiterhin gesperrt bleibt

- **Google-Unternehmensprofil** — braucht `[GESCHAEFTSADRESSE_STATUS]`. Ein Profil ohne prüfbare Adresse ist nicht anlegbar, und ein falsch angelegtes Profil ist schwer zu korrigieren
- **`LocalBusiness`** in strukturierten Daten — dito, bis eine Anschrift feststeht
- **NAP-Aussage** — es gibt noch keine Adresse

### Einzugsgebiet und Ortsseiten sind zwei verschiedene Dinge — entschieden 01.08.2026

**Der Betreiber hat eingewandt, vier Orte seien zu wenig, und genannt:** Meißen · Radeberg ·
Coswig · Bischofswerda · Bautzen · Sebnitz · Pirna · Heidenau · Dippoldiswalde, dazu Dresden nach
Stadtteilen aufteilen.

**Der Einwand stimmt zur Hälfte.** Das Einzugsgebiet soll alle diese Orte umfassen. Neun eigene
Ortsseiten wären trotzdem ein Fehler. Die Auflösung liegt in der Trennung:

#### Ebene 1 — Einzugsgebiet: alle Orte, sofort, ohne eine einzige neue Seite

| Wo | Was |
|---|---|
| **Google-Unternehmensprofil** | alle genannten Orte als Einzugsgebiet. Bis zu **20** sind erlaubt — die Liste passt vollständig hinein |
| **`/kontakt` und die Dresden-Seite** | ein Absatz, der die Orte namentlich nennt — **und den Satz, dass bundesweit gearbeitet wird** |
| **Angebote und E-Mails** | Sitz im Raum Dresden nennen, **Arbeitsgebiet bundesweit**. Nicht „nur im Raum Dresden" |

**Das bedient alle genannten Orte** und kostet nichts. Kein Doorway-Risiko, weil keine Seite
entsteht, die nur aus einem Ortsnamen besteht.

#### Ebene 2 — eine starke Regionsseite statt neun dünner

`/webdesign-dresden` ist die Hauptseite. Darin ein Abschnitt zum Umkreis mit den Ortsnamen.

**Eine gute Seite rankt für „webdesign dresden" und streut auf die Umlandbegriffe. Neun dünne
Seiten ranken für nichts.** Sie unterscheiden sich nur im Ortsnamen — und genau das nennt Google
als Doorway-Tatbestand (§16a, unverändert gültig).

#### Ebene 3 — Ortsseiten werden verdient, nicht verteilt

Eine eigene Seite entsteht erst, wenn **beides** vorliegt:

1. Die Search Console zeigt für diesen Ort **tatsächlich Impressionen**
2. Es gibt dort einen **echten Kunden mit schriftlich freigegebener Fallstudie**
   (`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §4.1, Stufe 6)

Realistisch sind das **zwei bis drei Orte im ersten Jahr**, nicht neun.

### Warum Dresden nicht aufgeteilt wird

**Stadtteilseiten tragen bei Laufkundschaft** — Friseur, Bäcker, Apotheke. Da sucht jemand in
seiner Nachbarschaft.

**Webdesign ist keine Laufkundschaft.** Niemand sucht „Webdesign Dresden-Striesen". Die Anfrage
kommt aus der Region, nicht aus der Straße. Stadtteilseiten wären neun weitere Doorway-Kandidaten
ohne Suchvolumen dahinter.

### Die Größenordnungen, damit die Entscheidung nachvollziehbar ist

Einwohnerzahlen zum 31.12.2025:

| Ort | Einwohner |
|---|---|
| **Dresden** | **562.764** |
| Pirna | 40.039 |
| Freital | 39.174 |
| Bautzen | 37.306 |
| Radebeul | 33.081 |
| Meißen | 28.863 |
| Coswig | 20.479 |
| Radeberg | 18.667 |
| Heidenau | 16.597 |
| Bischofswerda · Sebnitz · Dippoldiswalde | jeweils **unter 15.000** |

**Dresden zu Sebnitz ist rund 70:1.** Und maßgeblich ist nicht die Einwohnerzahl, sondern wie
viele Betriebe dort im Jahr **2.198 bis 10.888 €** für eine Website ausgeben. In einer Stadt unter
15.000 Einwohnern sind das im Zweifel null bis zwei.

### Der bessere Ausbau: Branche statt Ort

Wer wachsen will, baut **nicht** die zehnte Ortsseite, sondern die erste Branchenseite.

| | Ortsseite | Branchenseite |
|---|---|---|
| Beispiel | `/webdesign-bischofswerda` | `/webdesign-zahnarztpraxis` |
| Nachfrage | an die Ortsgröße gebunden | **überregional** |
| Wettbewerb | dünn, aber die Nachfrage auch | dünn bei echter Spezialisierung |
| Inhalt | unterscheidet sich **nur im Ortsnamen** | andere Argumente, andere Beispiele, andere Rechtsfragen |
| Doorway-Risiko | hoch | **keins** |

Eine Seite über Websites für Zahnarztpraxen kann echte Substanz tragen: Terminbuchung, Heilmittel-
werbegesetz, Bewertungen, Personalsuche. Eine Seite über Bischofswerda kann nur den Ortsnamen
tragen.

**Empfehlung für Stufe 2:** drei Branchenseiten vor der zweiten Ortsseite.

> **„Umland vor Kernstadt" gilt weiter — für den Verkauf, nicht für die Seiten.** Masterkonzept
> §23a meint die Kundengewinnung: Die ersten Aufträge holt man leichter in Radeberg als in Dresden,
> wo Agenturen seit Jahren ranken.
>
> **Die Seite ist trotzdem `/webdesign-dresden`.** Dort liegt das Suchvolumen, und dieselbe Seite
> deckt das Umland mit ab. Beides zusammen: **im Umland verkaufen, über Dresden gefunden werden.**
> Das ist kein Widerspruch, sondern zwei verschiedene Wege zum selben Kunden.

### Ausbau in kleinere Städte deutschlandweit — **Stufe 2, mit Vorbehalt**

Die Idee, gezielt Klein- und Mittelstädte mit dünnem Anbieterangebot zu bespielen, ist grundsätzlich
richtig und deutlich besser als „alle Orte über 5.000 Einwohner". Sie ist aber **kein Startvorhaben**,
aus drei Gründen:

| | |
|---|---|
| **Der Kartenbereich ist nicht erreichbar** | Ein Google-Unternehmensprofil erlaubt höchstens 20 Einzugsgebiete, Richtwert rund zwei Stunden Fahrzeit. Außerhalb davon ist der lokale Kartenbereich **verschlossen** — dort wirkt nur die organische Suche, und die ist langsamer und härter |
| **Doorway-Risiko** | Google nennt ausdrücklich „mehrere Seiten, die auf bestimmte Regionen oder Städte ausgerichtet sind und Nutzer auf eine Seite leiten". Ohne echten örtlichen Nutzwert je Seite ist genau das der Tatbestand — §16a gilt unverändert |
| **Dünner Wettbewerb heißt oft dünne Nachfrage** | Eine Stadt ohne Agenturen hat manchmal keine, weil dort niemand 5.448 € für eine Website ausgibt. **Vor dem Bau prüfen**, nicht danach |

**Bedingung für Stufe 2:** Erst müssen im Raum Dresden **echte Kundenprojekte** existieren. Jede
auswärtige Ortsseite braucht dann einen belegbaren örtlichen Nutzwert — nach der Beweisleiter in
`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §4.1, Stufe 6: **eine Fallstudie eines echten Kunden aus dem
Ort, schriftlich freigegeben.** Ohne die entsteht keine Seite.

---

## 2. Rechtstexte — **OFFEN**

**Verfahren geändert 28.07.2026:** Entwürfe werden von der KI erstellt, **anschließend anwaltlich
geprüft**. Die frühere Regel „nie von einer KI formuliert" ist damit abgelöst — die Sperre bis zur
Prüfung bleibt jedoch bestehen.

| Platzhalter | Status | Risiko des Entwurfs |
|---|---|---|
| `[IMPRESSUM]` | Entwurf durch KI, dann Prüfung | **gering** — überwiegend Formsache nach § 5 DDG |
| `[DATENSCHUTZ]` | Entwurf durch KI, dann Prüfung | **mittel** — hängt an dem, was die Seite tatsächlich tut. Der technische Aufbau ist bekannt, damit ist ein belastbarer Entwurf möglich |
| `[AGB]` | Entwurf durch KI, dann Prüfung | **hoch** — Laufzeit, Kündigung, Haftung, Zahlung, Leistungsumfang. Hier entscheidet die anwaltliche Prüfung über Geld, nicht über Formulierungen |
| `[ANSCHRIFT]`, `[TELEFON]`, `[EMAIL]` | erst mit Punkt 1 entscheidbar |

**Sperre bleibt unverändert:** Die produktive Veröffentlichung bricht ab, solange die Texte nicht
**anwaltlich freigegeben** sind (Website-Lastenheft §14a). Ein Entwurf zählt nicht als Freigabe.

**Kennzeichnungspflicht:** Jeder Entwurf trägt am Kopf `ENTWURF — NICHT GEPRÜFT, NICHT
VERÖFFENTLICHEN`. Der Vermerk wird erst nach der Freigabe entfernt, und zwar von einem Menschen.

> **Warum die Sperre trotz Entwurf bleibt:** Ein plausibel klingender Rechtstext ist gefährlicher
> als gar keiner. Ohne Text weiß jeder, dass etwas fehlt. Mit einem gut formulierten Entwurf denkt
> man, es sei erledigt — und genau so geht er live.

---

## 3. Designrichtung — **ENTSCHIEDEN 01.08.2026**

> **Der Betreiber hat das Artefakt vom 30.07.2026 als verbindlich bestätigt.** Damit ist das
> Auswahlverfahren des Design-Briefings abgeschlossen — die Variantenrunde entfällt.
>
> | | |
> |---|---|
> | **Werte** | `design/tokens.css` — Farben, Radien, Abstände, Schrift, Bewegung |
> | **Begründung und Regeln** | `SARTU_DESIGNSYSTEM.md` |
> | **Kontraste** | nachgerechnet, niedrigster Wert im System **6,42 : 1** bei AA-Grenze 4,5 : 1 |
>
> **Was das für den Bauplan ändert:** `BAUFREIGABE.md` §4 sah eine Variantenrunde **vor** dem
> Ausbau vor. Sie entfällt. A0 und das Frontend können parallel laufen.

### Die ursprüngliche Fassung

Entsteht über `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`:
Recherche → Prüfliste → **2–3 klickbare Startseitenvarianten mit echten Texten** → Mensch entscheidet.

### Vom Betreiber vorgegeben (25.07.2026, nach der ersten Vorlage)

| Vorgabe | Bedeutung für die Umsetzung |
|---|---|
| **Weichere Formsprache** | Die streng rechtwinklige Fassung der ersten Vorlage ist abgelehnt. Runde Ecken, weichere Übergänge und rundere Flächen sind gewollt — als **durchgehendes System**, nicht als einzelnes rundes Element auf einer sonst scharfkantigen Seite (Design-Briefing §3.9) |
| **Etwas Verspieltheit** | Die Seite darf Charakter zeigen: ein wiederkehrendes gestalterisches Motiv, lebendigere Farbe, gelegentlich ein Bruch im Raster. **Nicht** aus Effekten, sondern aus Haltung |
| **Bewegung erwünscht** | Ruhige, scrollgebundene Bewegung nach Design-Briefing §3.2. Umsetzung in CSS ohne JavaScript |
| **Kein Glaseffekt** | Geprüft und abgelehnt, Begründung in Design-Briefing §3.2 |

### Farbsystem — **entschieden 25.07.2026, Fassung 3**

**Eine einzige Farbe: Lime. Sie ist eine Flächenfarbe, nie eine Schriftfarbe auf hellem Grund.**
Alles Übrige ist neutral — Creme, Papier, Tinte.

| Variable | Hex | Rolle |
|---|---|---|
| `--lime` | `#a3e635` | Fläche für Hauptaktionen, Badge, Textmarker |
| `--lime-hover` | `#8dc92a` | Hover |
| `--lime-press` | `#7ab023` | gedrückt |
| `--lime-soft` | `#e4f5b8` | zarte Tönung für hervorgehobene Blöcke |
| `--ink` | `#14110d` | die **einzige** Textfarbe auf allen Lime-Flächen |

**Nachgerechnet:**

| Rolle | Wert | |
|---|---|---|
| Dunkler Text auf Lime-Fläche | 12,48:1 | die Rolle, die trägt |
| Lime als Signal auf dunklem Abschnitt | 12,48:1 | erlaubt |
| Lime als Link-/Textfarbe auf hellem Grund | 1,30:1 | **verboten** |
| Heller Text auf einer Lime-Fläche | 1,44:1 | **verboten** |

**Umsetzung je Element:**

- **Hauptknopf:** Lime gefüllt, Text in `--ink`. Hover und gedrückt ändern nur die Fläche
- **Zweitknopf:** transparent, Umrandung in `--ink`, Text in `--ink`. Kein Lime
- **Links im Fließtext:** Text in `--ink`, Lime als **Textmarker** dahinter (`background-image`/`background-size`), beim Überfahren wächst er auf volle Höhe. **Kein** `text-decoration: underline` in Lime
- **Fokusring:** Doppelring — innen 2 px `--ink`, außen 2 px `--lime`. Lime allein erreicht gegen Creme nur 1,30:1
- **Jede Lime-Fläche auf hellem Grund braucht eine 1 px-Umrandung**, sonst verläuft die Kante
- **Keine vollflächigen Lime-Bänder.** Lime bleibt auf Knöpfe, Badges, Textmarker und kleine Blöcke beschränkt

> **Zwei abgelöste Fassungen, damit niemand sie wieder aufgreift:**
> **Fassung 1** war Terrakotta `#c1452f` — erreichte als Linkfarbe nur 4,36:1 und verfehlte die
> 4,5:1 aus Design-Briefing §2.3.
> **Fassung 2** war Petrol `#1a6165` als Handlungsfarbe plus Lime als Markierung. Technisch
> einwandfrei, in der Anwendung aber nicht stimmig — zwei Farben, die um dieselbe Aufmerksamkeit
> konkurrierten. **Beide sind ungültig.** Wer `--accent`, `--terra` oder `--petrol` im Code
> findet, ersetzt sie durch das System oben.

**Die Grenze bleibt:** Zielgruppe sind Unternehmer zwischen 35 und 60 aus Handwerk, Handel und
Dienstleistung, die einen verlässlichen Partner für Jahre suchen. Verspieltheit darf Sympathie
erzeugen, nie Zweifel an der Ernsthaftigkeit. Im Zweifel ist die ruhigere Lösung die richtige.

**Gate:** Vor dieser Entscheidung wird **keine** weitere Seite ausgebaut. Wer nach dem Briefing
durchbaut, hat das Gate verletzt.

---

## 4. Betriebsumgebung — **teilentschieden**

| Punkt | Stand |
|---|---|
| Sprache und Aufbau | **entschieden:** PHP, serverseitig gerendert, ein modulares Projekt (Portal-Lastenheft §1) |
| Datenbank | **entschieden:** MySQL/MariaDB, weil klassisches Hosting möglich bleiben soll |
| Konkreter Anbieter und Tarif | *offen* — muss die Anforderungen aus Portal-Lastenheft **§1.4** erfüllen (PHP-Erweiterungen, Datenbank, HTTPS, Verzeichnis außerhalb des Webroots, **Cron**, **zuverlässiger Mailversand**, Sicherung, Serverstandort EU) |
| **Entwicklungsumgebung** | **Weg entschieden 01.08.2026: A — Docker.** In der Bauumgebung geprüft: PHP **8.4.19** und Composer sind vorhanden, Docker **29.3.1** läuft, ein MySQL-Client fehlt. Die Datenbank kommt deshalb aus dem Container. **Achtung:** Der Zielhoster muss ≥ 8.3 fahren; gegen dessen Version wird vor dem Livegang gegengetestet |
| ~~Entwicklungsumgebung, alte Fassung~~ | ~~**Verfahren entschieden 25.07.2026, Weg offen:** PHP 8.3 + Composer sind Pflicht, die Datenbank darf nachgereicht werden. Zwei gleichwertige Wege in `ENTWICKLUNGSUMGEBUNG.md` — **A** Docker (`docker-compose.yml` liegt bereit), **B** natives Paket (Laragon/XAMPP/Homebrew). Der Weg wird beim Einrichten gewählt und hier eingetragen~~ |
| Umgang mit vorhandenen Prototypen | *offen* — wird in `IMPLEMENTATION_PLAN.md` entschieden und begründet |

---

## 4a. Zahlung, Belege und Buchhaltung — **ENTSCHIEDEN 09.08.2026**

**Vier Fragen, die zusammengehörten und einzeln nie beantwortbar waren.** Sie standen verteilt
in `spezifikation/02_PREISE_UND_ZAHLUNG.md` („Entscheidung offen"), in der Nicht-bauen-Liste und
in der Architekturregel „Composer sparsam". Der Betreiber hat sie am 09.08.2026 in einem Zug
entschieden.

| Frage | Entscheidung |
|---|---|
| **Besteuerung** | **Regelbesteuerung.** Nicht Kleinunternehmer nach § 19 UStG. `operator_settings.kleinunternehmer` bleibt als Feld bestehen, steht aber auf `false` |
| **Zahlungsdienst** | **Mollie wird angebunden** — mit Webhook, serverseitigem Statusabruf und Abgleich. Die Nicht-bauen-Liste ist **an dieser einen Stelle aufgehoben** |
| **Buchhaltungswerkzeug** | **Weder lexoffice noch sevDesk.** Der Rechnungsausgang entsteht im Portal, für den Steuerberater gibt es einen Export. Die offene Entscheidung ist damit **geschlossen, nicht vertagt** |
| **Zusätzliche Pakete** | **Zwei Composer-Pakete sind erlaubt** — ein Erzeuger für ZUGFeRD/XRechnung und ein HTML-nach-PDF-Renderer. Bewusste Ausnahme von „Composer sparsam" |

### Was ausdrücklich **nicht** entschieden wurde

**Buchhaltung im engeren Sinn wird nicht gebaut.** Keine doppelte Buchführung, kein Kontenrahmen,
keine Umsatzsteuer-Voranmeldung über ELSTER, kein Jahresabschluss. Diese Grenze ist Teil der
Entscheidung und nicht ihr Nebeneffekt.

> **Warum die Grenze genau hier liegt.** Der Rechnungsausgang ist ein abgeschlossener Vorgang mit
> einer Norm dahinter — EN 16931 sagt Feld für Feld, was hineingehört. Buchhaltung ist kein
> Vorgang, sondern ein Beruf: Kontenzuordnung, Voranmeldung, Abschluss, Betriebsprüfung. Wer sie
> selbst baut, übernimmt die Haftung dafür, ohne die Fachkunde zu haben.

### Was die Entscheidung an Verantwortung verschiebt

**Bis heute lag die Aufbewahrung außerhalb.** `spezifikation/11_KUNDENBEREICH.md` schrieb:
„Rechnungsarchiv, Aufbewahrungsfristen und Nummernkreise laufen im Buchhaltungswerkzeug, **nicht**
im Portal." Dieser Satz fällt. Damit gilt für das Portal, was für jedes Rechnungsschreibungssystem
gilt: **die GoBD.**

| Anforderung | Stand |
|---|---|
| Unveränderbarkeit | **da** — `audit_events` verbietet `UPDATE` und `DELETE` per Trigger (Migrationen 005, 006) |
| Nachvollziehbarkeit | **da** — `old_value`, `new_value`, `reason`; `reason` ist bei Geld und Fristen Pflichtfeld |
| Keine harte Löschung | **da** — `archived_at` statt `DELETE` |
| Lückenloser Nummernkreis | **fehlt** — die Rechnungsnummer wird heute vom Admin eingetippt |
| Stornorechnung statt Statuswechsel | **fehlt** — `stornieren()` setzt nur den Status |
| Aufbewahrung des strukturierten Teils | **fehlt** |
| Verfahrensdokumentation | **fehlt** — ein Dokument, kein Programm |

**Die Einzelheiten stehen in `spezifikation/18_BELEGE_UND_ZAHLUNG.md`.** Diese Datei hier nennt
nur die Entscheidung; die Bauvorgabe steht dort.

### Fristen, an der Primärquelle geprüft (09.08.2026)

| Pflicht | Ab wann | Für wen |
|---|---|---|
| E-Rechnung **empfangen** können | **01.01.2025** | alle inländischen Unternehmer, auch Kleinunternehmer |
| E-Rechnung **versenden** | **01.01.2027** | Vorjahresumsatz über 800.000 € |
| E-Rechnung **versenden** | **01.01.2028** | alle übrigen |
| Befreiung vom Versenden | dauerhaft | Kleinunternehmer nach § 19 UStG — **für SARTU nicht einschlägig** |

**Zulässige Formate:** XRechnung und ZUGFeRD ab **2.0.1**, beide nach EN 16931; die Profile
MINIMUM und BASIC-WL sind ausgeschlossen. Unter **250 €** greift die Pflicht nicht. Der
strukturierte Teil ist **acht Jahre** unversehrt aufzubewahren.

> **Für SARTU heißt das: Frist ist der 31.12.2027.** Es gibt keinen Zeitdruck — aber auch keinen
> Grund, zweimal zu bauen. Quelle: Bundesfinanzministerium, FAQ zur obligatorischen E-Rechnung.

---

## 4b. Das Aufmacherbild — **ENTSCHIEDEN 10.08.2026**

**Der gebaute Aufmacher zeigte einen gestrichelten Platzhalter, der abgenommene Entwurf zeigt ein
Gerät.** Beide beriefen sich zu Recht auf eine gültige Vorgabe; niemand hatte sie je
nebeneinandergelegt.

| | |
|---|---|
| **Entschieden** | Im Aufmacher steht ein **Gerät in leichter Schrägstellung** — Laptop mit angeschnittenem Telefon davor, wie auf Finanzseiten üblich |
| **Auf dem Bildschirm** | eine **echte Aufnahme des eigenen Kundenbereichs** mit Musterdaten. Keine nachgezeichnete Oberfläche |
| **Der Rahmen** | wird **selbst gezeichnet** — CSS und Inline-SVG, keine gekaufte oder heruntergeladene Vorlage |
| **Der Vermerk `Musteransicht`** | bleibt gebunden und steht am Bild |

### Warum das die Regel nicht bricht, sondern sie schärft

`10_WEBSITE_SARTU.md` verlangte „ehrlich beschrifteter Bildplatz, **keine nachgebaute
Oberfläche**", `CODEX_AUFTRAG_WEBSITE.md` „Screenshots des Kundenbereichs müssen aus **echter**
Oberfläche stammen".

**Beide zielen auf dasselbe: Niemand soll eine erfundene Oberfläche für die echte halten.** Der
Entwurf vom 02.08.2026 zeichnete die Oberfläche in CSS nach — mit erfundenen Zeilen und
erfundenen Knöpfen. **Das verstieß gegen die Regel, und die Bau-Session hatte recht, ihn nicht
zu übernehmen.**

**Der Grund ist seit dem 10.08.2026 entfallen: Der Kundenbereich ist gebaut.** Es gibt eine echte
Oberfläche, von der sich eine echte Aufnahme machen lässt. Damit ist die Regel erfüllt — und der
Platzhalter überflüssig.

**Die Regel gilt unverändert weiter für alles, was sie eigentlich meint:** Musterprojekte,
Gründerfoto, Referenzen. Dort bleibt der beschriftete Bildplatz, bis eine echte Aufnahme
vorliegt (§5, weiterhin **offen**).

> **Der Geräterahmen ist kein Bild im Sinne dieser Regel.** Er behauptet nichts — er ist eine
> Fläche, auf der die Aufnahme steht. Deshalb wird er gezeichnet und nicht beschafft: Eine
> gekaufte Vorlage bindet eine Lizenz und friert das Aussehen ein, ein gezeichneter Rahmen nicht.

### Was auf der Aufnahme zu sehen sein darf

**Musterdaten, wie in `17_SEITEN_SARTU.md` ohnehin vorgeschrieben** — keine echten Kundennamen,
keine realistischen Rechnungsnummern, keine erfundenen Referenzen. Die Aufnahme wird als Datei
abgelegt und erneuert, wenn sich der Kundenbereich sichtbar ändert.

---

## 4c. Betreiberdaten statt Sperren — **ENTSCHIEDEN 10.08.2026**

**Zwei Sperren wurden zu wörtlich gebaut.** §1 verbietet, eine Geschäftsadresse zu **erfinden**;
§5 verbietet einen leeren Rahmen an einer Vertrauensstelle. Daraus wurde im Code, das Feld gar
nicht erst zu bauen — `app/Strukturdaten.php` begründet das damit, eine ungenutzte Methode sei
„eine Zeile Arbeit vom Verstoß entfernt".

**Das ist übervorsichtig und kostet mehr, als es schützt.** Die Folge war: Der Betreiber trägt
die Adresse ein, und trotzdem passiert nichts, weil es die Ausgabe nicht gibt.

| Entschieden | |
|---|---|
| **Die Sperre wird datengesteuert, nicht codegesteuert** | Feld leer → nichts wird ausgeliefert. Feld gefüllt → alles läuft. Genau wie die Startsperre es ohnehin macht |
| **`LocalBusiness` wird gebaut** | ausgeliefert **nur**, wenn Straße, PLZ und Ort in `operator_settings` gefüllt sind. Sonst weiter `Organization` ohne Adressfeld |
| **Gründerangaben kommen in die Betreiberdaten** | Name, ein Absatz Begründung und ein Bild — im Adminbereich einzutragen und hochzuladen |
| **Sektion „Wer dahintersteckt" und die Seite dazu werden gebaut** | sichtbar **nur**, wenn Name, Text und Bild vorliegen. Sonst entfällt die Sektion wie bisher |

### Was der Gründerabschnitt ist — und was nicht

**Kein Lebenslauf.** Kein Werdegang, keine Stationen, keine Titel. **Ein Absatz, warum es SARTU
gibt** — welche Beobachtung dazu geführt hat, dieses Angebot zu bauen. Dazu Name und Bild.

> **Warum das genügt.** Der Vertrauensanker ist, dass eine benennbare Person verantwortlich ist,
> nicht wie ihr Lebenslauf aussieht. Ein Lebenslauf auf einer Agenturstartseite liest ohnehin
> niemand; ein Grund schon.

**Was weiterhin gesperrt bleibt:** Musterprojekte und Referenzen (§5, unverändert **offen**).
Dort ist das Bild der Beleg, und einen Beleg ersetzt kein Feld.

### Was das an der Bewertung ändert — und was nicht

Eine externe Prüfung vom 10.08.2026 gab **4/10 bei Conversion und Vertrauen** bei 8/10 fast
überall sonst. Die Ursache ist nicht die Gestaltung und nicht die Argumentation: **SARTU verkauft
persönliche Verantwortung und bleibt anonym.**

**Diese Entscheidung räumt die technische Hürde weg. Sie ersetzt nicht die Inhalte** — Foto, Name,
Begründung und später zwei Demoprojekte kommen vom Betreiber, nicht aus dem Bau.

---

## 5. Bildmaterial und Demoprojekte — **OFFEN**

| Punkt | Stand |
|---|---|
| ~~Ansichten aus dem Kundenbereich~~ | **Für den Aufmacher am 10.08.2026 entschieden — siehe §4b.** Der Kundenbereich ist gebaut, die Aufnahme zeigt ihn echt. Der Bildplatz für **Musterprojekte** und das **Gründerfoto** bleibt unverändert |
| `[GRUENDER_NAME]` | *offen* — Name für Startseite §5 **Sektion 6** und `/ueber-uns` |
| Foto des Gründers | *offen* — echtes Foto nötig, kein Bestandsfoto, kein Platzhalter, der wie ein Foto wirkt. **Fehlt es, entfällt Sektion 6 der Startseite vollständig** — kein leerer Rahmen an einer Vertrauensstelle |
| **Ein bis zwei gekennzeichnete Demoprojekte** | *offen, zu entscheiden* — vollständige Beispielseiten für erfundene, **als solche benannte** Betriebe. Liefert Bildmaterial, Arbeitsbeleg und einen Belastungstest des Produktionswegs in einem |
| Bestandsfotos | **ausgeschlossen** (Design-Briefing §3.2a) |

**Sperre:** Solange kein echtes Bild vorliegt, wird die betreffende Stelle **ohne** Bild gestaltet.
Ein leerer Platzhalterrahmen an einer Vertrauensstelle ist ausdrücklich unzulässig
(Design-Briefing §4a).

**Warum das hier steht und nicht im Design-Briefing:** Ob es Demoprojekte gibt, ist eine
Geschäftsentscheidung mit Aufwand und Außenwirkung — keine Gestaltungsfrage.

### Aufgelöst am 06.08.2026 — es sind zwei Dinge, nicht eins

**Der Widerspruch war scheinbar.** Diese Tabelle nennt *„ein bis zwei"* Demoprojekte,
`10_WEBSITE_SARTU.md` §8 bindet **drei** Gattungen und die Zahl **drei** im Einleitungssatz. Beide
Zahlen sind richtig, weil sie **verschiedene Gegenstände** zählen:

| | Was es ist | Woran es hängt | Stand |
|---|---|---|---|
| **Musterprojekt-Karte** | eine *Beschreibung* auf der Startseite: Ausgangslage, empfohlene Lösung, Seitenstruktur, Bildplatz, was der Kunde liefern müsste | **Text.** Entsteht am Schreibtisch | **drei — gebunden, entsperrt** |
| **Demoprojekt** | eine *gebaute* Beispielwebsite für einen erfundenen, als solchen benannten Betrieb | Produktionsaufwand, Hosting, Pflege | **ein bis zwei — weiterhin offen** |

**Die drei Karten hingen nie an der Demoprojekt-Entscheidung.** Sie brauchen keinen gebauten
Betrieb, sondern drei ausgeschriebene Fälle — und die Gattungen dafür stehen seit dem 01.08.2026
fest: **Malerbetrieb · Physiotherapiepraxis · Arbeitsrechtskanzlei**.

**Was die Demoprojekt-Entscheidung weiterhin sperrt:** echte Bildschirmaufnahmen für die
Bildplätze und die Seite `/musterprojekte` in ihrer *ausgebauten* Form. Bis dahin gilt der
ehrlich beschriftete Bildplatz — **kein nachgebauter Bildschirm** (`10_WEBSITE_SARTU.md` §8).

> **Wer entscheidet:** weiterhin der Betreiber, und weiterhin nur über die **Demoprojekte**. Die
> Frage lautet nicht mehr „gibt es die Sektion", sondern „bauen wir ein bis zwei der drei
> beschriebenen Fälle wirklich".

### Die Verwechslung, die dazu geführt hat

**Zwei Zeilen weiter oben stand zweimal „Sektion 8", gemeint war Sektion 6.** Sektion 8 ist
**Musterprojekte**, Sektion 6 ist **Wer dahintersteckt** — in beiden Fassungen des Lastenhefts.
Wer die Zeile wörtlich nahm, sperrte die Musterprojekte auf ein **Gründerfoto**, das dort nichts
zu suchen hat. **Am 06.08.2026 korrigiert.** `spezifikation/20_OFFEN.md` führte die Sperre schon
richtig auf Sektion 6 — dieser Rang-1-Text war der einzige mit dem Fehler.

### 5.1 Der Klarname ist gleichzeitig offen und schon vergeben

`[GRUENDER_NAME]` steht oben als *offen*, und die Startseite sagt `Name wird nachgereicht`.
**Gleichzeitig steht der volle Klarname bereits in zwei Dateien** — als Bildbeschreibung:

| Datei | Stelle |
|---|---|
| `archiv/CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md` | Bildliste, `nils-arbeitsbild.webp` |
| `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` §15 | Bildliste, `sartu-portrait.webp` |

Bildbeschreibungen sind öffentlich. Sie stehen im Quelltext, werden vorgelesen und von
Suchmaschinen gelesen. **Der Name wäre also veröffentlicht worden, ohne dass er je entschieden
wurde** — über eine Tabellenzeile in einer Bildliste.

**Zu entscheiden:**

| Weg | Folge |
|---|---|
| **A — voller Klarname** | Stärkstes Vertrauenssignal, das ohne Referenzen möglich ist. Google nennt eine erkennbare, verantwortliche Person ausdrücklich als Merkmal vertrauenswürdiger Inhalte. Der Name wird damit dauerhaft mit SARTU verknüpft und ist praktisch nicht zurücknehmbar |
| **B — Vorname und Rolle** | Zum Beispiel „Nils, Gründer". Weniger Angriffsfläche, schwächeres Signal. Im Impressum steht der volle Name ohnehin — dort ist er Pflicht |

**Solange nichts entschieden ist, gilt:** Der Name erscheint **nur** im Impressum, nirgends sonst —
auch nicht in Bildbeschreibungen. Die Startsperre §14a Bedingung 4a hält die Sektion zurück.

**Wer entscheidet:** der Betreiber. Es ist eine persönliche Entscheidung, keine fachliche.

---

## 5a. Selbstpflege durch den Kunden — **ENTSCHIEDEN 01.08.2026**

Die Startseite (Website-Lastenheft §5 Sektion 2) verspricht in der rechten Spalte drei Dinge, für
die es **keine Grundlage** gibt:

| Versprechen | Stand |
|---|---|
| *Bilder tauschen* | Keine Tabelle im Datenmodell. Es fehlt sinngemäß `media_assets` |
| *Team- und Projekteinträge pflegen* | Keine Tabelle. Es fehlt sinngemäß `site_content` |
| *Anfragen von Ihrer Website einsehen* | **Ausdrücklich verboten.** `CODEX_AUFTRAG_PORTAL.md` listet „Annahme von Anfragen aus Kundenwebsites" unter **Nicht bauen** — das ist die Lead-Inbox der Stufe 1 |

**Zwei Wege, einer muss gewählt werden:**

### Entschieden am 01.08.2026 — die drei Zeilen entfallen

**Ein Fund hat die Frage beantwortet:** Die beiden Lastenhefte widersprechen sich bereits.

| Wo | Was dasteht |
|---|---|
| Website-Lastenheft §5 Sektion 2 | *Bilder tauschen* — als Selbstbedienung beworben |
| Portal-Lastenheft §8.8, Frage 2 | *„Öffnungszeiten pflegen Sie selbst. Texte, **Bilder** und Seitenstruktur ändern wir für Sie."* |

**Der Kunde liest die zweite Fassung — im Produkt, nach dem Kauf.** Die Website versprach etwas,
das die Anwendung schon in ihrer eigenen Hilfe zurücknimmt.

**Drei Gründe, jeder allein ausreichend:**

1. **Eine der drei Funktionen ist ausdrücklich verboten.** `CODEX_AUFTRAG_PORTAL.md` führt
   „Annahme von Anfragen aus Kundenwebsites" unter **Nicht bauen** — das ist die Lead-Inbox der
   Stufe 1, mit eigenem Endpunkt, eigener Missbrauchsabwehr und eigenem Datenschutzumfang
2. **Die anderen beiden kosten mehr, als sie einbringen.** Zwei neue Tabellen, eine Bearbeitung im
   Portal, eine Prüfstrecke vor der Veröffentlichung — für Funktionen, die der Kunde selten braucht
3. **Was er wirklich oft geändert haben will, ist Text und Bild.** Genau das macht SARTU ohnehin
   für ihn. Das ist kein Mangel, das ist die Leistung

**Die Sektion behält elf Punkte.** Elf konkrete Tätigkeiten, die kein Wettbewerber anbietet, sind
kein reduzierter Umfang — sie sind das stärkste Argument der Seite.

**Umkehrbar in einer Zeile:** Wer die Funktionen später baut, trägt die Zeilen wieder ein. Die
Entscheidung verbaut nichts.

---

## 6. Barrierefreiheit nach dem BFSG — **ENTSCHIEDEN 01.08.2026**

Das Barrierefreiheitsstärkungsgesetz gilt seit dem **28.06.2025**. Es stand im Masterkonzept, aber
in **keinem** der beiden Lastenhefte — und damit nicht dort, wo gebaut wird.

**Wen es trifft, hängt an zwei Fragen:** Verkauft oder bucht der Kunde etwas an Verbraucher? Und
liegt er über der Kleinstunternehmensgrenze (10 Beschäftigte, 2 Mio. € Umsatz)? Eine reine
Firmenwebsite eines Handwerksbetriebs ist regelmäßig außerhalb. Ein Shop oder eine
Online-Terminbuchung eines größeren Betriebs nicht.

### Entschieden am 01.08.2026 — Grundstand immer, Konformität nur nach Prüfung

**Geprüfte Rechtslage:**

| Fall | Gilt das BFSG? |
|---|---|
| Reine Firmenwebsite mit Kontaktformular, ohne Buchung, Bestellung oder Abo | **Nein.** Es fehlt der Verbrauchervertrag über die Seite |
| Seite mit Online-Terminbuchung, Shop, Bestellung oder Abo | **Ja** — es sei denn, der Betrieb ist Kleinstunternehmen |
| Kleinstunternehmen: **unter 10 Beschäftigte** **und** höchstens **2 Mio. €** Umsatz oder Bilanzsumme | **ausgenommen**, aber nur bei Dienstleistungen |
| Verstoß | Bußgeld bis **100.000 €**, dazu Abmahnrisiko |

**Drei Regeln, die zusammen gelten:**

**1. Der technische Grundstand ist immer enthalten — und wird ab jetzt im Angebot benannt.**
Kontrast ab 4,5:1 · volle Tastaturbedienung · sichtbarer Fokus · sinnvolle Beschriftungen ·
semantisches HTML · `prefers-reduced-motion`. Das stand ohnehin in beiden Lastenheften, war aber
für den Kunden unsichtbar. **Es ist ein Verkaufsargument und wahr — beides gleichzeitig.**

**2. Standardfall: die Ausschlusszeile aus Portal-Lastenheft §4c.** Eine Seite ohne Buchungs-,
Bestell- oder Kaufweg schließt keinen Verbrauchervertrag. Das BFSG greift nicht, und SARTU sagt
keine Gesetzeskonformität zu.

**3. Sobald ein Buchungs-, Bestell- oder Kaufweg dazukommt, zwei Pflichtfragen vor dem Angebot:**

> `Schließen Besucher über die Seite einen Vertrag ab — Buchung, Bestellung oder Abonnement?`
> `Hat Ihr Betrieb weniger als 10 Beschäftigte und höchstens 2 Mio. € Umsatz oder Bilanzsumme?`

**Beide Antworten werden im Angebot festgehalten.** Nur wenn die erste `ja` und die zweite `nein`
lautet, greift das BFSG. Dann gibt es zwei Möglichkeiten: **Konformität als eigener
Festpreisposten** — oder eine Absage, wenn SARTU es nicht verantworten kann.

### Warum nicht die beiden einfacheren Wege

| Verworfen | Grund |
|---|---|
| **Immer enthalten** | SARTU kann Beschäftigtenzahl und Umsatz nicht prüfen, und beide ändern sich. Eine Konformitätszusage steht gegen ein Bußgeld bis 100.000 €. Für einen Einzelbetrieb am Anfang das falsche Risiko |
| **Pauschal ausschließen** | Ein Platzhirsch-Kunde mit Buchungsweg oberhalb der Schwelle wäre ungeschützt — und SARTU hätte genau das gebaut, was ihn dorthin bringt. Das ist nicht vertretbar |

**Warum die Grenze am Vertragsabschluss liegt:** Sie steht so im Gesetz und ist die einzige, die
sich ohne Gutachten feststellen lässt. Beschäftigtenzahl und Umsatz muss der Kunde beantworten —
er ist der Einzige, der sie kennt.

**Ändern sich die Verhältnisse, meldet das der Kunde.** Das steht im Angebot. SARTU überwacht
weder Umsatz noch Personalstand seiner Kunden.

> **Diese Entscheidung ist keine Rechtsberatung.** Sie legt fest, was SARTU anbietet und was nicht.
> Ob ein einzelner Betrieb unter das Gesetz fällt, entscheidet im Zweifel seine eigene
> Rechtsberatung — genau deshalb fragt SARTU und rät nicht.

---

## 7. Ein Benutzer je Kunde — **entschieden, aber nicht kommuniziert**

Portal-Lastenheft §2: *„Stufe 0 kennt genau einen Benutzer je Kundenorganisation."*

Die Entscheidung ist vertretbar und bleibt. In der Praxis beauftragt aber der Inhaber, und die
Bürokraft füllt die Aufgaben aus. Beide müssten sich dann ein Postfach teilen — denn Anmeldelinks
gelten **einmal** und **15 Minuten**. Ein weitergeleiteter Link ist meist schon tot.

**Zu tun, sobald bestätigt:** Der Satz gehört ins Angebot und in die häufigen Fragen der Website.
Vorschlag:

> `Der Zugang zum Kundenbereich läuft über eine E-Mail-Adresse. Wenn mehrere Personen mitarbeiten
> sollen, verwenden Sie am besten eine gemeinsame Adresse wie info@ihrefirma.de.`

**Das ist keine Einschränkung, die man versteckt.** Sie ist erklärbar — aber nur, wenn sie vorher
dasteht.

---

## 7a. Vertragsende im Kundenbereich — **bewusst verschoben**

Das Masterkonzept regelt Export, Domainübergabe und wer die Verlängerung zahlt. Im Kundenbereich
gibt es dazu **keinen Bildschirm und keinen Ablauf**.

**Für Stufe A ist das richtig so** — der erste Kunde kündigt nicht in den ersten Monaten. Es steht
hier, damit es als verschoben gilt und nicht als vergessen. **Spätestens vor dem zwölften
Betriebsmonat** des ersten Kunden muss der Ablauf stehen.

---

## 7b. Stellen- und Karriereseite in den Paketen — **Richtung gewählt 01.08.2026, eine Rückfrage offen**

> **Antwort des Betreibers am 01.08.2026: „Mit Bewerbungsformular ins Portal."**
>
> **Diese Antwort hat zwei Lesarten, und sie führen zu völlig verschiedenem Aufwand.**
>
> | Lesart | Was gemeint sein kann | Aufwand |
> |---|---|---|
> | **A — Formular auf der Kundenwebsite** | Der Handwerker bekommt eine Karriereseite mit Bewerbungsformular. Die Bewerbung geht **per E-Mail an ihn**. SARTUs Portal sieht sie nie | **null neue Tabellen.** Steht bereits im Masterkonzept: `Platzhirsch` bekommt „genau ein Conversion-Modul (… *oder* einfaches Bewerbungsformular)" |
> | **B — Bewerbungen laufen in den SARTU-Kundenbereich** | Der Handwerker sieht seine Bewerbungen im Portal, wie er heute seine Aufgaben sieht | **Neue Tabelle, neue Löschfrist, neue Rechtsgrundlage, neue Bildschirme, neue Testfälle** |
>
> ### Lesart B widerspricht zwei bestehenden Festlegungen
>
> | Festlegung | Wortlaut |
> |---|---|
> | `CODEX_AUFTRAG_PORTAL.md` §5 | *„**Nicht bauen:** Annahme von Anfragen aus **Kundenwebsites** (das ist die Lead-Inbox der Stufe 1)"* |
> | Website-Lastenheft, entschieden **gestern** | Die Zeile *„Anfragen von Ihrer Website einsehen"* **entfällt dauerhaft** — mit derselben Begründung |
>
> **Das ist kein Einwand gegen die Entscheidung.** Der Betreiber darf beide Festlegungen aufheben.
> Es muss nur ausgesprochen sein, weil sonst ein ausführender Agent zwischen zwei Dokumenten steht,
> die sich widersprechen.
>
> ### Was Lesart B zusätzlich verlangt, bevor gebaut wird
>
> | # | Fehlt | Warum es nicht nebenbei geht |
> |---|---|---|
> | 1 | Tabelle mit Feldern und Typen | Das Datenmodell hat 20 Tabellen auf Feldebene. Eine 21. ohne dieselbe Tiefe bricht das Muster |
> | 2 | **Löschfrist für Bewerberdaten** | §15.1 kennt Anfragen (12 Monate) und Rechnungen (8 Jahre). Bewerberdaten sind eine dritte Kategorie mit eigener Frist |
> | 3 | **Rolle im Datenschutz** | Bei Bewerbungen an den Handwerker ist **er** verantwortlich und SARTU Auftragsverarbeiter. Der AVV aus §15.2 deckt diesen Zweck heute nicht ab |
> | 4 | Speicherung von Lebensläufen | §11 begrenzt 500 MB je Organisation. `task_files` hängt an Aufgaben und passt nicht |
> | 5 | Bildschirme, E-Mails, Testfälle | 88 Testfälle sind nummeriert und zugeordnet. Bewerbungen haben keinen |
> | 6 | Wirkung auf Seitenzahl und Preis | Offen. Betrifft die Preistabelle und jede Stelle mit `1 / 8 / 16 Seiten` |
>
> **Nächster Schritt: Lesart A oder B bestätigen.** Bei A ist der Punkt erledigt und nichts zu tun.
> Bei B entsteht zuerst die Spezifikation, dann der Bau.

### Die ursprüngliche Fragestellung

**Woher der Punkt kommt:** die Motivrecherche in `SARTU_KUNDENMOTIVE_BELEGT.md`.

| Belegt | Wert | Quelle |
|---|---|---|
| Mangel an Auszubildenden | **83 %** | Bitkom 2025, n=504 |
| Fachkräftemangel | **75 %** | Bitkom 2025 |
| Fürchten Nachteile im Wettbewerb um Fachkräfte ohne digitale Technik | **54 %** | Bitkom 2025 |
| Ausbildungsbetriebe, die Nachwuchs über digitale Kanäle ansprechen | **80 %** | Bitkom 2025 |

**Der Azubimangel liegt über jeder anderen Herausforderung** — über Energiepreisen (81 %),
Fachkräftemangel (75 %) und Digitalisierung (62 %). Im Konzept kommt eine Karriereseite nirgends
vor.

**Was zu entscheiden ist:**

| # | Frage | Folge |
|---|---|---|
| 1 | Gehört eine Stellenseite in `Start`, oder erst ab `Wachstum`? | `Start` hat 1 Seite — eine Stellenseite wäre die halbe Lieferung |
| 2 | Nur Textseite oder mit Bewerbungsformular? | Ein Formular erzeugt Bewerberdaten: eigene Löschfristen, eigene Rechtsgrundlage, eigener Eintrag im Verarbeitungsverzeichnis |
| 3 | Landen Bewerbungen im Kundenbereich wie Anfragen? | Dann eine eigene Art in `leads` oder eine eigene Tabelle — betrifft das Datenmodell |
| 4 | Ändern sich Seitenzahl und Wortumfang der Pakete? | Betrifft Preistabelle und jede Stelle, an der 1 / 8 / 16 Seiten steht |

**Sperre:** Solange das offen ist, wird auf keiner Website- und keiner Branchenseite eine
Karriere- oder Stellenseite erwähnt, angekündigt oder verlinkt.

**Empfehlung:** Frage 2 mit „nur Textseite plus E-Mail-Adresse" beantworten. Ein Bewerbungsformular
zieht den größten Rechtsaufwand nach sich und lässt sich später nachrüsten.

---

## 7c. Drei Branchen für Branchenseiten **und** Musterprojekte — **OFFEN**

> **Gefunden am 06.08.2026** bei der Frage, auf welche Nische SARTU optimiert. Zwei Listen,
> die aufeinander verweisen, nennen verschiedene Branchen. Beide sind `gebunden`, keine trägt
> Vorrang — deshalb **gemeldet statt ausgewählt**.

| Quelle | Branchen |
|---|---|
| `16_SEO_GEO_SARTU.md`, Abschnitt *Branchenseiten* | **Sanitär-Heizung-Klima · Elektrotechnik · Dachdecker** |
| `10_WEBSITE_SARTU.md` §8, Block *Gebunden* | **Malerbetrieb · Physiotherapiepraxis · Arbeitsrechtskanzlei** |

**Keine einzige Überschneidung.**

### Warum das mehr ist als eine Unschönheit

`17_SEITEN_SARTU.md` §4 führt als **Block 6** jeder Branchenseite: *„ein Beispiel — das
Musterprojekt dieser Branche"*, Spalte **eigen**. Zehn Blöcke, Umfang 900–1.300 Wörter.

Die Datei hat die Lücke **halb** gesehen: Der Querverweis in §4 trägt den Zusatz **„sofern die
Gattung passt"**. Sie sagt aber nicht, **was in Block 6 steht, wenn sie nicht passt** — und bei
allen drei Launch-Branchen passt sie nicht. Dazu kommt **Prüfung 3, der Herkunftsnachweis**: Sie
verlangt ausdrücklich zu Block **6** eine Quellenzeile. Ein Block ohne Inhalt hat auch keine
Quelle, und *„Reißt eine der drei Prüfungen: Die Seite wird nicht veröffentlicht."*

**Unabhängig davon, welche Nische gewählt wird, gilt:** Welche drei Branchen es auch werden —
**es müssen dieselben drei sein.** Ein Musterprojekt ohne Branchenseite verschenkt seinen
stärksten Platz; eine Branchenseite ohne Musterprojekt kann ihren Pflichtblock nicht füllen.

### Was zu entscheiden ist

| # | Frage | Folge |
|---|---|---|
| 1 | Welche drei Branchen tragen **beides**? | Bestimmt `16` (Branchenseiten), `10` §8 (Musterprojekte) und `17` §4 (Block 6) |
| 2 | Bleibt die Zielgruppe bei **sieben** Kategorien? | `01_GESCHAEFTSMODELL.md` nennt Handwerk, lokale Dienstleister, Praxen, Kanzleien, Gastronomie, Immobilien, Beratungen. Eine Gewerke-Nische widerspricht dem nicht, verengt aber den Schwerpunkt |
| 3 | Was steht in Block 6, solange die passende Gattung fehlt? | Bis Frage 1 beantwortet ist, ist **jede** Branchenseite unvollständig |

### Die drei gangbaren Wege

| Weg | Branchen | Was sich ändert | Was es kostet |
|---|---|---|---|
| **A** | SHK · Elektro · **Dach** | die drei Gattungen in `10` §8 | Physiotherapie und Kanzlei fallen als Musterprojekte weg |
| **B** | SHK · Elektro · **Maler** | je eine Zeile in `10` §8 **und** in `16` | Dachdecker fällt als Branchenseite weg |
| **C** | Maler · Physio · Kanzlei | alle drei Branchenseiten in `16` | drei fachlich unverwandte Welten — jeder Fachtext beginnt bei null |

### Empfehlung: **Weg A**

Vier Gründe, alle aus den vorliegenden Unterlagen:

1. **Der Engpass ist das Schreiben, nicht das Ranken.** Bei drei verwandten Gewerken ist dieselbe
   Recherche mehrfach verwertbar; bei Weg C ist jede Seite gleich teuer.
2. **Prüfung 2 verlangt 400 eigene Wörter je Seite**, Prüfung 3 einen Herkunftsnachweis je Block.
   In einem Feld erfüllbar — über drei Felder verteilt dreimal so aufwendig.
3. **Recruiting ist bei bau-nahen Gewerken ein echter Kaufgrund.** `01_GESCHAEFTSMODELL.md` führt
   es im Bedarf der Zielgruppe. Eine Physiotherapiepraxis hat dieses Argument nicht.
4. **Die drei gebauten Branchenseiten bleiben unangetastet.** Nur `10` §8 zieht nach.

**Weg A ist zugleich der einschneidendste:** Er verengt den Schwerpunkt von sieben Kategorien auf
ein Feld. Das ist eine **Geschäfts**entscheidung, keine SEO-Entscheidung — deshalb steht sie hier
und nicht in `16_SEO_GEO_SARTU.md`.

**Sperre:** Solange das offen ist, wird **kein** Musterprojekt ausgeschrieben und **keine**
Branchenseite über Block 5 hinaus gebaut. Die Musterprojekt-Sektion der Startseite bleibt nach
`10_WEBSITE_SARTU.md` ohnehin auf **Stufe 0** — keine Sektion, solange die drei Fälle nicht
ausgeschrieben sind.

---

## 8. Wie diese Datei benutzt wird

1. Ein Wert wird entschieden → hier eintragen, Status von `offen` auf den Wert ändern
2. Datum und Entscheider dazuschreiben
3. Dann erst die betroffenen Sperren in den Bauunterlagen lösen
4. **Nie** umgekehrt: kein Wert wird „schon mal" in eine Bauunterlage geschrieben

| Datum | Was entschieden | Von wem |
|---|---|---|
| 25.07.2026 | Entwicklungsumgebung: PHP+Composer verbindlich, Datenbank nachreichbar; Weg A oder B frei (§4) | Betreiber |
| 25.07.2026 | Designrichtung: weichere Formsprache, etwas Verspieltheit, Bewegung ja, Glaseffekt nein (§3) | Betreiber |
| 25.07.2026 | Akzentfarbe: Petrol `#1a6165` für Handlung, Lime `#a3e635` für Markierung; Terrakotta abgelöst (§3) | Betreiber |
| 09.08.2026 | **Regelbesteuerung**, nicht Kleinunternehmer (§4a) | Betreiber |
| 09.08.2026 | **Mollie wird angebunden** — Nicht-bauen-Liste an dieser Stelle aufgehoben (§4a) | Betreiber |
| 09.08.2026 | **Kein lexoffice, kein sevDesk.** Rechnungsausgang im Portal, Export für den Steuerberater (§4a) | Betreiber |
| 09.08.2026 | **Zwei zusätzliche Composer-Pakete erlaubt** — ZUGFeRD-Erzeuger und HTML-nach-PDF (§4a) | Betreiber |
| 10.08.2026 | **Aufmacherbild: Gerät in Schrägstellung mit echter Aufnahme des Kundenbereichs**, Rahmen selbst gezeichnet (§4b) | Betreiber |
| 10.08.2026 | **Sperren datengesteuert statt codegesteuert:** `LocalBusiness` und Gründerabschnitt werden gebaut, ausgeliefert nur bei gefüllten Betreiberdaten (§4c) | Betreiber |
| 10.08.2026 | **Gründerabschnitt ist kein Lebenslauf**, sondern Name, Bild und ein Absatz zur Gründung (§4c) | Betreiber |
| 13.08.2026 | **Die Kapazitätszeile nennt den nächsten möglichen Projektstart** statt „Nur noch wenige Plätze". Das Terminverbot in `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` §5a ist Begründungsarchiv und sticht nicht; angezeigt wird der **Monat**, kein Tag | Betreiber |
| 13.08.2026 | **Das Mobilmenü greift ab 1180 px** statt ab 940 px — `design/startseite.html` Zeile 166 setzt diesen Wert, und die Kopfzeile lief von 941 bis 1183 px waagerecht über. `10_WEBSITE_SARTU.md` §2 („Desktop ab 1024 px") ist damit nachzuziehen — **offen** | Bau, gemessen |

### Offen aus der Sitzung vom 13.08.2026

| Punkt | Was zu entscheiden ist |
|---|---|
| **§2 nachziehen** | „Desktop ab 1024 px" stimmt nicht mehr. Entweder die Zahl auf 1180 ändern oder die Kopfzeile so kürzen, dass sie bei 1024 px passt — dafür müsste eine Beschriftung weichen, und §2 verbietet genau das („nicht für sechs Pixel geopfert") |
| **Branchenseiten in der Navigation** | Die Anweisung lautete „häng sie in die Navigation". §2 bindet sechs Punkte, §2a fünf Fußspalten mit gebundenem Inhalt, und die Kopfzeile trägt gemessen keinen siebten. Gebaut ist ein Verweisblock auf `/leistungen`. Zu entscheiden: §2 oder §2a ändern, oder dabei belassen |
| **`geraet-aufmacher.webp`** | Die Datei fehlt. Bis sie da ist, steht der in CSS gezeichnete Laptop |
