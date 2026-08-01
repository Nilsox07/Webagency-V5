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

## 3. Designrichtung — **teilentschieden**

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

## 5. Bildmaterial und Demoprojekte — **OFFEN**

| Punkt | Stand |
|---|---|
| Ansichten aus dem Kundenbereich | entstehen mit Sitzung 2 — die zwei Bildplätze der Startseite hängen daran |
| `[GRUENDER_NAME]` | *offen* — Name für Startseite §5 Sektion 8 und `/ueber-uns` |
| Foto des Gründers | *offen* — echtes Foto nötig, kein Bestandsfoto, kein Platzhalter, der wie ein Foto wirkt. **Fehlt es, entfällt Sektion 8 der Startseite vollständig** — kein leerer Rahmen an einer Vertrauensstelle |
| **Ein bis zwei gekennzeichnete Demoprojekte** | *offen, zu entscheiden* — vollständige Beispielseiten für erfundene, **als solche benannte** Betriebe. Liefert Bildmaterial, Arbeitsbeleg und einen Belastungstest des Produktionswegs in einem |
| Bestandsfotos | **ausgeschlossen** (Design-Briefing §3.2a) |

**Sperre:** Solange kein echtes Bild vorliegt, wird die betreffende Stelle **ohne** Bild gestaltet.
Ein leerer Platzhalterrahmen an einer Vertrauensstelle ist ausdrücklich unzulässig
(Design-Briefing §4a).

**Warum das hier steht und nicht im Design-Briefing:** Ob es Demoprojekte gibt, ist eine
Geschäftsentscheidung mit Aufwand und Außenwirkung — keine Gestaltungsfrage.

### 5.1 Der Klarname ist gleichzeitig offen und schon vergeben

`[GRUENDER_NAME]` steht oben als *offen*, und die Startseite sagt `Name wird nachgereicht`.
**Gleichzeitig steht der volle Klarname bereits in zwei Dateien** — als Bildbeschreibung:

| Datei | Stelle |
|---|---|
| `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md` | Bildliste, `nils-arbeitsbild.webp` |
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
