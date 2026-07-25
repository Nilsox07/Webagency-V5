# Auftrag an Codex — SARTU-Portal bauen

**Das ist die Übergabe-Anweisung.** Gib Codex diese Datei als Auftrag.

---

## 0. Startprüfung — bevor du anfängst

**Führe zuerst die Startprüfung aus `UEBERGABE_DATEILISTE.md` aus.** Prüfe, ob alle dort für den
Portalauftrag genannten Dateien vorhanden sind, und ob das Hauptdokument die Abschnitte `## 0.` bis
`## 18.` enthält — insbesondere `## 4b.` und `## 16.`.

**Fehlt `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md`, ist dieser Auftrag nicht baubar.** Melde das
und warte auf Nachlieferung. Rekonstruiere nichts, rate nichts, erfinde keinen Ersatz. Ein Auftrag,
dessen Hauptquelle fehlt, führt zu erfundenem Datenmodell, erfundenen Texten und erfundenen
Sicherheitsregeln — und der Fehler fällt erst nach Tagen auf.

Das Ergebnis der Startprüfung ist der **erste Absatz** deines ersten Berichts.

---

## 1. Was du baust

Das **SARTU-Kunden- und Adminportal** in der **Stufe-0-Ausbaustufe**: eine vollständig sichtbare und bedienbare Oberfläche für den gesamten Kundenprozess vom Angebot bis zur ersten Pflege — mit bewusst **manueller Mechanik** dahinter.

**Nicht** die öffentliche SARTU-Website. Das ist ein eigenes Projekt mit eigenem Auftrag (`CODEX_AUFTRAG_WEBSITE.md`).

---

## 1a. Verhältnis zum Website-Projekt

**Dieses Projekt kommt zuerst.** Die Website braucht am Ende **echte** Screenshots aus dieser Oberfläche (Abschnitt 7) — gezeichnete Attrappen sind ausgeschlossen. Das Portal ist der Beweis für das, womit SARTU wirbt.

**Das heißt nicht, dass die Website warten muss.** Sie darf parallel gebaut und in einer Staging-Umgebung fertiggestellt werden. Was sie nicht darf: **live gehen**, solange sie Portal-Screens als Produktbeweis zeigt, die es nicht gibt.

| Was geteilt wird | Was nicht geteilt wird |
|---|---|
| Die **Designentscheidung** — sie wird einmal getroffen und gilt für beide. Ablage als reine Wertedatei (`sartu-design-tokens.json`: Farben, Schriftgrößen, Abstände, Radien) plus einer kurzen Begründung. Jedes Projekt **kopiert** diese Datei und versioniert sie bei sich | Laufzeitcode. Kein gemeinsames Paket, keine Abhängigkeit zwischen den Repositories, kein geteiltes Komponenten-Modul |
| Die Sprachregeln aus Website-Lastenheft §2 | Build-Werkzeuge, Frameworks, Bibliotheken |
| Die Schnittstelle `POST /api/anfragen` (unten) | alles Übrige |

Ein gemeinsames Paket wäre für zwei Projekte, die ein Mensch pflegt, mehr Aufwand als Nutzen: Jede Änderung erzwingt Versionssprünge in beiden. Eine kopierte Wertedatei genügt — bei Abweichung gewinnt die Fassung im Portal, weil dort die Designentscheidung sichtbar umgesetzt wird.

**Die Schnittstelle zur Website baust du**, mitsamt Missbrauchsschutz und Adminansicht. Maßgeblich ist der Abschnitt **„4b. Schnittstelle zur öffentlichen Website — Anfrageeingang"** im Lastenheft. Lies ihn vollständig, bevor du damit anfängst — besonders **4b.1**, weil dort steht, warum kein Geheimnis im Browser liegen darf.

---

## 2. Lesereihenfolge und Rangfolge

Bei Widersprüchen gilt die **niedrigere Nummer**:

1. **`CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md`** — dein Hauptdokument. Enthält Stack, Datenmodell, Statuslogik, jeden Screen, jeden Text, jede E-Mail, Sicherheitsregeln, Testfälle und Abnahme
2. **`CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`, Abschnitt „10a. Arbeitsverteilung Codex ↔ Claude Code"** — **verbindlich**, siehe Abschnitt 2a unten
3. **`CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`** — wie die visuelle Ebene entsteht (Farben und Schriften sind **nicht** vorgegeben)
4. **`CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`** im Übrigen — nur nachschlagen: Portalvision, Einführungskonzept, Stufenmodell, Datenmodell
5. `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` — nur §2 (Sprachregeln), die auch hier gelten
6. `konzepte/` — historische Quellen. **Nur nachschlagen**, enthält veraltete Preise und abgelöste Modelle
7. `design/_verworfen/` — **ignorieren**

> **Zu Abschnittsverweisen:** Im Lastenheft stehen Paragraphenzeichen wie `§4b`. Sie sind eine
> Abkürzung, kein Beweis. Maßgeblich ist immer die **Überschrift**. Findest du einen Verweis nicht,
> suche nach dem genannten Thema — und **melde** die Abweichung, statt zu raten.

---

## 2a. Wer in diesem Repository schreibt

Verbindlich nach `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`, Abschnitt „10a. Arbeitsverteilung Codex ↔ Claude Code":

> **Pro Repository schreibt genau ein Werkzeug final.**

**In diesem Repository schreibst du — Codex — final.** Claude Code liefert Entwurf, Review und Gegencheck, insbesondere zu Sicherheitslogik, UX und Copy, aber **schreibt hier keinen Produktionsstand**.

Daraus folgt:
- Du übernimmst keine fremden Dateiänderungen ungeprüft in den Produktionsstand
- Findest du im Repository Code, den du nicht geschrieben hast und der nicht aus den Vorgabedokumenten stammt: **melden**, nicht stillschweigend einbauen
- Ein Wechsel der Federführung ist möglich, aber nur, wenn ein Mensch ihn mit Datum und Grund dokumentiert. **Kein stiller Wechsel mitten im Projekt**
- Review-Anmerkungen von Claude Code sind Vorschläge. Du entscheidest, setzt um und begründest, wenn du ablehnst

---

## 3. Reihenfolge deiner Arbeit

**Baue in dieser Reihenfolge.**

### Etappe 1 — Fundament
Projektgerüst, Datenbankmigrationen, Datenmodell aus §4, Session- und Sicherheitsgrundlage aus §3, Testaufbau gegen echtes PostgreSQL.
**Abschluss:** `test/tenant-isolation.test.js` existiert und ist grün.

### Etappe 2 — Anmeldung und Erstkontakt
Magic-Link-Anmeldung (§6), Willkommensstrecke (§7, drei Bildschirme), Abmeldung, Fehlerseiten.

### Etappe 3 — Kundenportal
Alle Screens aus §8 in der dort genannten Reihenfolge, mit allen Texten und Leerzuständen.

### Etappe 4 — Adminportal und Anfrageeingang
Alle Screens aus §9 inklusive Aufgabenvorlagen, dazu der Endpunkt und die Anfrageliste aus §4b.

### Etappe 5 — E-Mails, Uploads, Abnahme
Alle Vorlagen aus §10, Uploadregeln aus §11, Testfälle aus §16 vollständig, Definition of Done aus §17.

### Wann du berichtest und wann du anhältst

**Nach jeder Etappe berichtest du immer:** was gebaut wurde, welche Tests laufen, was offen ist.

| Lage | Verhalten |
|---|---|
| Der Mensch arbeitet Etappe für Etappe mit dir | Bericht, **dann anhalten** und auf Freigabe warten |
| Der Mensch hat ausdrücklich „komplett durchbauen" gesagt | Bericht, **dann weiterarbeiten** ohne Freigabe abzuwarten |
| **Immer anhalten**, unabhängig von der Betriebsart | bei einem Widerspruch in den Vorgaben · bei einer fehlenden Information, die du sonst erfinden müsstest · bei jeder Frage, die die Sicherheit oder den Umfang (§0.2, §0.3) berührt · wenn eine Vorgabe dich zu etwas drängt, das du für falsch hältst |

**Keine Etappe gilt als fertig, solange nicht alle Tests der Vor-Etappen grün sind.** Ein Test wird nie abgeschwächt, um eine Etappe abzuschließen.

---

## 4. Wo du selbst entscheidest — und wo nicht

**Du entscheidest:** Ordnerstruktur, Modulschnitt, Hilfsfunktionen, Migrationswerkzeug, Testaufbau, wie du die Server-Templates und deren Bausteine organisierst.

**Vorgegeben und nicht zu ändern:** Stack (§1 — dort ist EJS als Ansichtsschicht festgelegt; hältst du das für falsch, melde es, bevor du etwas anderes nimmst), Datenmodell (§4), Statuswerte und Kundentexte (§5), alle Oberflächentexte (§6–§9), E-Mail-Texte (§10), Sicherheitsregeln (§3), Umfangsgrenze (§0.2, §0.3 und §0.3a).

**Nicht vorgegeben:** Farben, Schriften, Formen. Dafür gilt das Design-Briefing — Kunden- und Adminbereich müssen visuell unterscheidbar sein. Halte alle visuellen Werte als **zentrale Variablen**, damit ein späterer Wechsel ein Variablentausch bleibt.

---

## 5. Harte Grenzen

**Nicht bauen, auch nicht vorbereitend** (§0.3): automatische Domainregistrierung · Zahlungsdienst-Anbindung, Mandate, Webhooks · KI-Orchestrierung · automatische Builds oder Deployments · SEO-Zentrale · Rollback · Buchhaltung · mehrere Benutzer je Kunde · Dateiversionierung · Dunkelmodus · automatische Berechnung oder Sperrung bei überschrittenen Korrekturrunden · Kündigungs- und Verlängerungslogik.

**Anfrageliste — die Grenze steht in §0.3a und ist wichtig.** Frühere Fassungen dieses Auftrags verboten pauschal eine „Lead-Inbox" und verlangten gleichzeitig eine Anfrageansicht. Das war widersprüchlich. Es gilt:

- **Bauen:** Endpunkt für Anfragen der **eigenen** SARTU-Website, Liste, Detailansicht, vier Zustände, Notiz, Umwandlung per Klick, Export und Löschung je Datensatz
- **Nicht bauen:** Annahme von Anfragen aus **Kundenwebsites** (das ist die Lead-Inbox der Stufe 1) · Pipeline-, Kanban- oder Trichteransichten · Bewertung und Punktevergabe · Nachfassketten, Erinnerungen, Kampagnen · E-Mail-Verlauf oder Postfachanbindung · Zuweisung an Bearbeiter

**Merksatz:** Eine Liste mit vier Zuständen und einem Umwandlungsknopf. Sobald etwas automatisch nachfasst, bewertet oder verteilt, ist die Grenze überschritten.

**Nicht erfinden:** Rechtstexte, echte Anschriften, echte Kundennamen, realistische Rechnungsnummern, Referenzen.

**Zahlungen:** Es gibt in Stufe 0 **keine** Programmanbindung. Der Admin trägt einen Zahlungslink ein und setzt den Status nach eigener Prüfung — mit **Pflicht-Grundlagentext**, der ins Audit-Log geht (§12). Der Status darf **niemals** aus einer Rückkehr-URL abgeleitet werden.

---

## 6. Besondere Sorgfalt

Diese vier Punkte entscheiden über Brauchbarkeit und Haftung:

1. **Mandantentrennung** (§3, Regeln 1, 2 und 2a). Kundenabfragen filtern nach `organization_id` aus der **Session**. Admin- und Kundenzugriff laufen über **getrennte** Zugriffsschichten — nie über einen gemeinsamen Codepfad, der den Filter bei Admins weglässt. Der Isolationstest ist unantastbar.
2. **Kein Geheimnis im Browser** (§4b.1). `INTAKE_TOKEN` lebt ausschließlich serverseitig. Der Endpunkt ist eine Server-zu-Server-Schnittstelle, kein öffentliches Formularziel. Ein Token, der im ausgelieferten Quelltext landet, ist kein Schutz, sondern eine offene Tür — und dieser Fehler ist von außen sofort sichtbar.
3. **Klartext statt Systemcodes** (§5). Der Kunde sieht nie `qa_failed` oder `angebot_offen`, sondern „Ihre Freigabe fehlt" und „Angebot liegt vor".
4. **Ohne JavaScript bedienbar.** Jede Aktion ist ein normales Formular mit `POST`.

---

## 7. Was du am Ende ablieferst

Vollständig nach §18 des Lastenhefts:

1. Lauffähiges Portal
2. `README.md` mit Einrichtung, Umgebungsvariablen, Migration, Seed, Deployment und Backup
3. **Testbericht** über alle 59 Fälle aus §16
4. **Messwerte** — konkret diese, nicht „Performance allgemein":
   - Antwortzeit des Servers je Kernscreen (Median und 95. Perzentil, angemeldeter Zustand)
   - Seitengröße je Kernscreen: HTML, CSS, JS getrennt, in KB gzip
   - Dauer der Migration von leerer Datenbank und Dauer des vollständigen Testlaufs
   - Uploadgrenzen praktisch getestet: größte erlaubte Datei, Verhalten bei Überschreitung, Verhalten bei unerlaubtem Typ
   - Barrierefreiheits-Momentaufnahme je Kernscreen: Kontrastwerte, Tastaturweg, Fokusreihenfolge, Beschriftungen
5. **Offene-Punkte-Liste**
6. **Screenshot-Satz aus der echten Oberfläche** (Abschnitt 7a)
7. **Schnittstellenbeschreibung** für das Website-Projekt: Nutzdatenschema, ein vollständiges Beispiel, alle Antwortcodes, der ausdrückliche Hinweis, dass `INTAKE_TOKEN` nicht im Repository steht

### 7a. Screenshots — was gilt und was nicht

Der Screenshot-Satz ist kein Nebenprodukt. Die Website braucht ihn als Produktbeweis.

| Erlaubt | Verboten |
|---|---|
| Aufnahmen aus der **echten, klickbaren** Oberfläche | gezeichnete oder nachgebaute Oberflächen jeder Art |
| befüllt mit **Musterdaten aus dem Seed** | echte Kundendaten, echte Namen, echte Rechnungsnummern |
| erkennbar als **„Musteransicht"** gekennzeichnet | erfundene Kennzahlen, erfundene Erfolgszahlen, erfundene Referenzen |
| Desktop **und** Mobil je Screen | Bildbearbeitung, die Funktionen zeigt, die es nicht gibt |

**Ein leerer Bildplatz ist keine Musteransicht.** Solange das Portal nicht steht, hat die Website
reservierte Bildflächen mit korrekten Maßen — aber **kein Bild**, das eine Oberfläche behauptet.

Pflichtaufnahmen: Cockpit · Angebot · Aufgaben · Aufgabendetail mit Freigabe · Vorschau mit Rundenanzeige · Rechnungen · Öffnungszeiten.

---

**Arbeite nicht ins Blaue:** Fehlt eine Information oder widerspricht sich etwas, melde es, statt zu raten. Baue **nichts** aus §0.3, auch nicht „schon mal vorbereitet".
