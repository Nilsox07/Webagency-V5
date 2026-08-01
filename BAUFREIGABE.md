# Baufreigabe — was heute starten darf

**Stand:** 01.08.2026
**Geprüft:** 19 Bauunterlagen, 11.383 Zeilen. Code: **0 Zeilen.**
**Zweck:** Die Frage „können wir anfangen" einmal beantworten, damit sie nicht in jeder Sitzung neu
verhandelt wird.

---

## Die Antwort in drei Zeilen

| | |
|---|---|
| **Stufe A0 startet sofort** | Keine offene Entscheidung berührt sie |
| **Eine Sperre steht vor dem vollständigen Backend** | §7b Karriereseite — betrifft Datenmodell und Preistabelle |
| **Drei Sperren stehen vor der Veröffentlichung** | Rechtstexte, Bildmaterial, Branchenseiten. **Nicht** vor dem Bauen |

---

## 1. Was heute ohne Rückfrage gebaut werden kann

### A0 — Fundament, 6 Tabellen, 26 Testfälle

Ersteinrichtung · Migrationen · Adminanmeldung mit TOTP · Betreiberdaten · Rechtstexte mit
Freigabezustand · Testmailversand · Mandantentrennung · Prüfprotokoll.

**Warum unblockiert:** Adresse, Rechtsform und Name sind offen — sie stehen aber in
`operator_settings` und werden im Adminbereich gesetzt. Der Betreiber hat das am 01.08.2026
ausdrücklich so entschieden. Die Tabelle braucht die Werte nicht, um zu entstehen.

**`legal_texts` genauso:** Die Tabelle, der Freigabezustand und die Kennzeichnung `ENTWURF` sind
festgelegt. Dass noch kein geprüfter Text existiert, hindert das Schema nicht.

### A1 — Anfrage bis Auftrag, 4 Tabellen, 35 Testfälle

Bedarfsscheck · Anfrageliste · Umwandlung in Kunde und Projekt · Anmeldelink · Angebot senden ·
Löschlauf.

**Eine Bedingung vorher:** Der Hoster muss **Cron** können (Portal §1.4). A1 enthält den Löschlauf
für IP-Adressen, A2 den Überfälligkeitslauf. Ohne Cron braucht beides einen anderen Auslöser — das
ist eine Architekturentscheidung. Eine Einstellung reicht dafür nicht.

---

## 2. Die eine Sperre vor dem vollständigen Backend

### §7b — Stellen- und Karriereseite

**Warum das nicht warten kann:** Zwei der vier Fragen aus `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §7b
greifen ins Fundament.

| Frage | Was daran hängt |
|---|---|
| Landen Bewerbungen im Kundenbereich wie Anfragen? | Eine eigene Art in `leads` oder eine eigene Tabelle. **Datenmodell** |
| Ändern sich Seitenzahl und Wortumfang der Pakete? | Preistabelle, Angebotslogik, jede Stelle mit `1 / 8 / 16 Seiten` |

**Wer das nach dem Backend entscheidet, zahlt eine Migration und eine Preisänderung.**

**Empfehlung, damit es heute vom Tisch ist:** nur Textseite plus E-Mail-Adresse, keine
Bewerbungsdaten im Portal. Dann ändert sich am Datenmodell nichts, und die Funktion lässt sich
später nachrüsten. Aufwand der Entscheidung: fünf Minuten.

---

## 3. Was das Bauen **nicht** blockiert

| Offen | Blockiert | Blockiert nicht |
|---|---|---|
| **Rechtstexte** (§2) | Veröffentlichung — Website §14a Bedingung 8 | Schema, Portal, Website-Bau |
| **Bildmaterial, Gründername, Foto** (§5) | Startseiten-Sektion 8, zwei Bildplätze | alles andere |
| **Demoprojekte** (§5) | Beleg für Arbeitsqualität | Bau |
| **Branchenseiten** (§10a) | 12–15 Seiten, Herkunftsnachweis fehlt | Start- und Leistungsseiten |
| **Hoster und Tarif** (§4) | Betrieb | Bau — bis auf die Cronfrage oben |

**Die Bildplätze haben eine natürliche Reihenfolge:** Zwei Startseitenbilder zeigen Ansichten aus
dem Kundenbereich. Den gibt es erst nach A2. **Die Website kann vor dem Portal nicht fertig
aussehen** — das folgt aus dem Plan und ist kein Fehler darin.

---

## 4. Wo „erst bauen, dann Design" die Reihenfolge umdreht

**Der geplante Weg** (`CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`):
Recherche → Prüfliste → **2–3 klickbare Startseitenvarianten mit echten Texten** → Mensch
entscheidet.

**Diese Varianten sind die erste Frontend-Version.** Sie stehen **vor** dem Ausbau.

| Reihenfolge | Was man bekommt | Was es kostet |
|---|---|---|
| **Varianten zuerst** (Plan) | Eine Gestaltungsentscheidung an drei Seiten getroffen | Eine Runde vor dem Ausbau |
| **Ausbau zuerst** | Alle Seiten fertig, dann die Grundsatzentscheidung | Jede gebaute Seite wird noch einmal angefasst |

**Design-Feinschliff ist nachher möglich. Die Designrichtung nicht.** Formsprache, Rundungen und
Farbsystem sind Systementscheidungen — sie stecken in jedem Bauteil.

> **Kein Konflikt mit dem Zeitplan:** Die Variantenrunde läuft **parallel zu A0 und A1**. Der
> Bedarfsscheck in A1 entsteht ausdrücklich „ohne das umgebende Website-Design"
> (`REIHENFOLGE.md`). Beides blockiert einander nicht.

---

## 5. Was „alle Funktionen im ersten Schritt" konkret heißt

| Stufe | Inhalt | Im ersten Schritt? |
|---|---|---|
| **A0–A3** | 18 Tabellen, 87 Testfälle — von der Anfrage bis `live` | **ja.** Das ist der vollständige Weg eines echten Kunden |
| **B** | Öffnungszeiten selbst pflegen, 2 Tabellen, 1 Testfall | klein. Kann mitlaufen |
| **C** | Mollie-Automatik, Mahnwesen, Registrar-Anbindung, Auswertungen | **nein** — und das ist der Kern von `REIHENFOLGE.md` |

**Der Unterschied ist Handarbeit, nicht fehlende Funktion.** In A2 wird eine Rechnung von Hand
angelegt und der Zahlungsstatus von Hand gesetzt. Der Kunde sieht dieselbe Rechnung wie später.
Was fehlt, ist der Webhook — der lohnt sich ab dem Kunden, bei dem Handarbeit lästig wird.

> **Die Warnung stammt aus dem eigenen Masterkonzept:** *„Die größte Gefahr ist nicht die Preis-
> oder Angebotslogik, sondern der Anspruch, den kompletten Kundenbereich mit voller
> Automatisierung vollständig vor dem ersten Standardverkauf zu bauen."*

---

## 6. Reihenfolge ab heute

| # | Schritt | Wer | Blockiert |
|---|---|---|---|
| 1 | **§7b entscheiden** | Betreiber | ja — vor dem Datenmodell |
| 2 | **Entwicklungsweg wählen**, Docker oder nativ, in §4 eintragen | Betreiber | ja — vor dem ersten Befehl |
| 3 | **A0 bauen** | Bau | — |
| 4 | **Designvarianten**, parallel zu 3 | Bau | — |
| 5 | **Hoster klären** — Cron und Mailversand | Betreiber | vor A1-Ende |
| 6 | **A1, A2, A3** | Bau | — |
| 7 | Rechtstexte entwerfen, dann anwaltlich prüfen | Bau, dann Anwalt | vor Veröffentlichung |
| 8 | Gründername, Foto, Demoprojekte | Betreiber | Startseiten-Sektion 8 |

**Nur die Punkte 1 und 2 stehen zwischen heute und dem ersten Commit.**
