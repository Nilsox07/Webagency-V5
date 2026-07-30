# SARTU — offene Entscheidungen und Platzhalter

**Zweck:** Eine einzige Stelle für alles, was noch nicht entschieden ist. Alle Bauunterlagen
verweisen hierher, statt eine Annahme fest einzubauen. Wer hier einen Wert einträgt, schaltet ihn
im ganzen Projekt frei — ohne dass jemand zehn Dateien durchsucht.

**Regel für die ausführende KI:** Ein Platzhalter aus dieser Datei darf **niemals** durch einen
erfundenen Wert ersetzt werden. Steht er auf `offen`, gilt die dort genannte Sperre.

---

## 1. Startregion und Geschäftsadresse — **teilentschieden 28.07.2026**

| Platzhalter | Wert | Stand |
|---|---|---|
| `[STARTREGION]` | **Raum Dresden** | entschieden |
| `[HAUPTORT]` | **Dresden** | entschieden |
| `[UMLAND_ORT_1]` … `[UMLAND_ORT_4]` | *offen* | **Betreiber wählt vier** — Vorschlag unten |
| `[HEIMATORT]` | *offen* | nur wenn er ein echter Vertrauensanker ist |
| `[GESCHAEFTSADRESSE_STATUS]` | *offen* | **blockiert weiterhin das Google-Unternehmensprofil** |

### Was damit freigeschaltet ist

- Ortsnamen im Fließtext, in Titeln und Adressen — **für die Startregion**
- `/webdesign-dresden` und Umlandseiten, **nach dem Gate in Masterkonzept §16a**
- Service-Area-Definition
- Regionale Formulierungen statt „im deutschsprachigen Raum"

### Was weiterhin gesperrt bleibt

- **Google-Unternehmensprofil** — braucht `[GESCHAEFTSADRESSE_STATUS]`. Ein Profil ohne prüfbare Adresse ist nicht anlegbar, und ein falsch angelegtes Profil ist schwer zu korrigieren
- **`LocalBusiness`** in strukturierten Daten — dito, bis eine Anschrift feststeht
- **NAP-Aussage** — es gibt noch keine Adresse

### Umlandorte — Auswahlregel, nicht Liste

Die vier Tier-1-Orte werden nach **drei** Kriterien gewählt, nicht nach Sympathie:

1. **erreichbar in unter 45 Minuten** — der Ortsbezug muss im Zweifel belegbar sein
2. **genug Betriebe der Zielgrößen** — Handwerk, Praxen, Kanzleien, Ladengeschäfte
3. **dünner Anbieterwettbewerb** — im Umland deutlich dünner als in der Kernstadt

Naheliegende Kandidaten im Umkreis: Radebeul · Meißen · Pirna · Freital · Radeberg · Coswig ·
Heidenau · Bautzen. **Der Betreiber wählt vier und trägt sie hier ein** — nicht die ausführende KI.

> **Reihenfolge bleibt: Umland vor Kernstadt.** Masterkonzept §23a. In Dresden konkurrierst du mit
> Agenturen, die seit Jahren dort ranken. Im Umland sind es eine Handvoll — dort entsteht der erste
> Beleg, und der trägt später die Kernstadtseite.

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
| **Entwicklungsumgebung** | **Verfahren entschieden 25.07.2026, Weg offen:** PHP 8.3 + Composer sind Pflicht, die Datenbank darf nachgereicht werden. Zwei gleichwertige Wege in `ENTWICKLUNGSUMGEBUNG.md` — **A** Docker (`docker-compose.yml` liegt bereit), **B** natives Paket (Laragon/XAMPP/Homebrew). Der Weg wird beim Einrichten gewählt und hier eingetragen |
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

---

## 6. Wie diese Datei benutzt wird

1. Ein Wert wird entschieden → hier eintragen, Status von `offen` auf den Wert ändern
2. Datum und Entscheider dazuschreiben
3. Dann erst die betroffenen Sperren in den Bauunterlagen lösen
4. **Nie** umgekehrt: kein Wert wird „schon mal" in eine Bauunterlage geschrieben

| Datum | Was entschieden | Von wem |
|---|---|---|
| 25.07.2026 | Entwicklungsumgebung: PHP+Composer verbindlich, Datenbank nachreichbar; Weg A oder B frei (§4) | Betreiber |
| 25.07.2026 | Designrichtung: weichere Formsprache, etwas Verspieltheit, Bewegung ja, Glaseffekt nein (§3) | Betreiber |
| 25.07.2026 | Akzentfarbe: Petrol `#1a6165` für Handlung, Lime `#a3e635` für Markierung; Terrakotta abgelöst (§3) | Betreiber |
