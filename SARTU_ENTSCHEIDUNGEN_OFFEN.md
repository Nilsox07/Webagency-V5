# SARTU — offene Entscheidungen und Platzhalter

**Zweck:** Eine einzige Stelle für alles, was noch nicht entschieden ist. Alle Bauunterlagen
verweisen hierher, statt eine Annahme fest einzubauen. Wer hier einen Wert einträgt, schaltet ihn
im ganzen Projekt frei — ohne dass jemand zehn Dateien durchsucht.

**Regel für die ausführende KI:** Ein Platzhalter aus dieser Datei darf **niemals** durch einen
erfundenen Wert ersetzt werden. Steht er auf `offen`, gilt die dort genannte Sperre.

---

## 1. Startregion und Geschäftsadresse — **OFFEN**

| Platzhalter | Wert | Bedeutung |
|---|---|---|
| `[STARTREGION]` | *offen* | Region, in der verkauft wird (z. B. „Region X") |
| `[HAUPTORT]` | *offen* | größte Stadt der Region — höchstes Suchvolumen, härtester Wettbewerb |
| `[UMLAND_ORT_1]` … `[UMLAND_ORT_4]` | *offen* | Tier-1-Orte im Umland — dünner Wettbewerb, echter Ortsbezug |
| `[HEIMATORT]` | *offen* | Wohn-/Herkunftsort, dient als Vertrauensanker, nicht als Traffic-Quelle |
| `[GESCHAEFTSADRESSE_STATUS]` | *offen* | `offen` · `echtes Buero` · `reine Postadresse` |

### Was gesperrt bleibt, solange der Status `offen` ist

- **Kein** `LocalBusiness` in strukturierten Daten — stattdessen `Organization` ohne Adresse
- **Kein** Google-Unternehmensprofil, auch nicht vorbereitend angelegt
- **Keine** Ortsseiten in der produktiven Veröffentlichung, auch nicht unverlinkt
- **Keine** Ortsnamen in Title, H1, Meta-Description oder URL
- **Keine** NAP-Aussage („Name, Adresse, Telefon konsistent") — es gibt noch keine Adresse
- **Keine** Service-Area-Definition
- Im Fließtext: **keine** Ortsnennung. Formulierungen bleiben überregional („für Betriebe im deutschsprachigen Raum", „vor Ort beim Kunden")

### Was trotzdem gebaut werden darf

Die gesamte Website ohne lokale Ebene: Startseite, Preise, Bedarfsscheck, Ablauf, Leistungsseiten,
Ratgeber, Lexikon, Kundenbereich. **Das ist der Regelfall**, nicht der Notfall — die lokale Ebene ist
eine spätere Ergänzung, kein Fundament.

### Was passiert, wenn die Entscheidung fällt

Die regionale Strategie in `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` §23a ist bewusst **ortsunabhängig**
formuliert: Umland zuerst, Hauptort später. Diese Logik gilt für jede Region. Beim Eintragen der
Werte oben wird sie ohne Umbau anwendbar.

> **Hinweis zur Historie:** In früheren Fassungen standen hier konkrete Orte, die aus einer
> Gesprächsnotiz stammten. Sie sind entfernt, weil eine beiläufig genannte Wohn- oder
> Wunschadresse **keine getroffene Marktentscheidung** ist. Der Unterschied ist wichtig: Die
> Marktentscheidung bestimmt Keywords, Seitenstruktur, Google-Profil und Impressum — sie will
> bewusst getroffen und nicht aus einem Nebensatz abgeleitet werden.

---

## 2. Rechtstexte — **OFFEN**

| Platzhalter | Status |
|---|---|
| `[IMPRESSUM]` | kommt von einer Kanzlei. **Nie** von einer KI formuliert |
| `[DATENSCHUTZ]` | dito |
| `[AGB]` | dito — bis dahin nicht verlinkt und `noindex` |
| `[ANSCHRIFT]`, `[TELEFON]`, `[EMAIL]` | erst mit Punkt 1 entscheidbar |

**Sperre:** Die produktive Veröffentlichung bricht ab, solange hier Platzhalter stehen
(Website-Lastenheft §14a).

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
| Foto des Gründers | *offen* — echtes Foto nötig, kein Bestandsfoto, kein Platzhalter, der wie ein Foto wirkt |
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
