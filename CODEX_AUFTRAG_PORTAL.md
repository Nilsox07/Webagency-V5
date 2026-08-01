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

## 0b. Pflicht vor der ersten Codezeile: `IMPLEMENTATION_PLAN.md`

**Du schreibst keinen Produktionscode, bevor diese Datei existiert und vorgelegt wurde.** Gute
Lastenhefte verleiten dazu, sofort loszubauen — und dann steht die Struktur fest, bevor jemand sie
geprüft hat.

Die Datei enthält:

| Abschnitt | Inhalt |
|---|---|
| **Bestand** | Was liegt bereits im Repository? Was davon ist Prototyp, was Altstand, was brauchbar? |
| **Prototypen** | Was übernimmst du, was verwirfst du — **je mit Begründung**. Fremder Code wird nie still übernommen |
| **Zielstruktur** | Konkrete Verzeichnisse und Dateien nach Portal-Lastenheft §1.3, auf dein Vorhaben angewendet |
| **Modulgrenzen** | Was gehört in `helpers`, `data`, `services`, `views`? Wo verläuft die Grenze zwischen Kunden- und Adminzugriff? |
| **Datenmodellquelle** | Welche Tabellen aus §4, in welcher Reihenfolge migriert |
| **Reihenfolge** | Welcher lauffähige Zwischenstand entsteht wann |
| **Risiken** | Was kann schiefgehen, woran merkst du es |
| **Testplan** | Welche Tests wann, wie die Datenbanktests laufen |
| **Offene Entscheidungen** | Was du **nicht** allein entscheidest |

**Danach baust du den kleinsten lauffähigen Stand** — Grundgerüst, eine Migration, eine Seite, ein
Test — und berichtest. Erst dann geht es weiter.

**Am Ende** lieferst du `IMPLEMENTATION_SUMMARY.md` (was gebaut wurde, Abweichungen vom Plan mit
Begründung, offene Punkte) und, falls aus einem Prototyp etwas übernommen wurde,
`MIGRATION_NOTES.md`.

---

## 0c. Zielarchitektur — ein PHP-Projekt

**SARTU ist eine Website mit geschütztem Kundenbereich, keine App.** Ein Repository, eine Domain,
ein Deployment:

```
/                     öffentliche SARTU-Website
/portal/              Kundenbereich (Login)
/admin/               interner Bereich (Login + Zweifaktor)
/api/                 eng begrenzte Serverfunktionen
```

Verbindlich: `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` **§1** — Stack, Verzeichnisstruktur,
Hosting-Anforderungen.

**PHP 8.3+, serverseitig gerendert, MySQL/MariaDB, PDO mit vorbereiteten Anweisungen.**
**Kein** WordPress · **kein** Laravel/Symfony · **kein** React/Vue/Next · **kein** Node oder Fastify
als Zielsystem · **kein** Supabase · **kein** Build-Schritt fürs Frontend · **keine** externen CDNs.

> **Zu älteren Ständen:** Frühere Fassungen nannten Node/Fastify/EJS oder einen Supabase-Prototyp.
> **Das ist keine Zielarchitektur mehr.** Vorhandene Prototypen dürfen als fachliche oder visuelle
> Referenz dienen — Ablauf, Felder, Texte. Ihr **Code** wird nicht übernommen. Was du daraus
> verwendest, steht begründet in `IMPLEMENTATION_PLAN.md`.

**Zur visuellen Ebene — das ist ausdrücklich kein „bau was Schönes":** Du wählst **1–3 sehr gute,
sauber lizenzierte Quellen** und **übernimmst deren konkreten Aufbau** — Markup, CSS-Ansatz,
Zustände, Interaktionslogik. Angepasst werden Farben, Schriften, Abstände, Texte. Utility-Klassen
werden dabei in **eigenes CSS mit zentralen Variablen** übersetzt, weil es keinen Build-Schritt gibt.
Vollständig in `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` §3.1 — **vor** dem ersten Entwurf lesen.

**Achtung Lizenz (§2.1 dort):** **Nur kostenlose Quellen**, deren Lizenz kommerzielle Nutzung,
Veränderung **und Weitergabe** erlaubt — MIT, Apache-2.0, ISC, BSD, CC0, bei Schriften OFL. Keine
gekauften Sammlungen, kein „Pro"-Tarif. Bei Projekten mit freiem und kostenpflichtigem Teil
(Flowbite, Preline) je Komponente prüfen, in welchem sie liegt. **Lies die Lizenzdatei selbst**, nicht
die Beschreibung auf der Website. Trage jedes Teil in die Herkunftsliste ein und liefere die
Lizenzhinweise mit aus.

**Sprachregel nach außen:** Kundenbereich, Ihr Bereich, Anmeldung. **Nie** App, Software, SaaS,
Plattform, Dashboard, Control-Plane. Der Kunde soll denken „ich melde mich an und sehe mein Projekt",
nicht „ich muss ein Werkzeug lernen".

---

## 0e. Gate: Designentscheidung vor dem Vollausbau

Nach dem Design-Briefing entstehen **2–3 klickbare Startseitenvarianten mit echten Texten**. Dann
**anhalten**. Der Mensch entscheidet die Richtung. Erst danach werden weitere Seiten ausgebaut.

Wer nach dem Briefing durchbaut, hat das Gate verletzt — und im Zweifel Dutzende Seiten in einer
Richtung gebaut, die verworfen wird. Das Gate gilt auch dann, wenn der Mensch „komplett durchbauen"
gesagt hat: Es ist eine Entscheidung, die nur er treffen kann.

---

## 0d. Standort ist offen — und das ist kein Hindernis

`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1 steht auf `offen`. Solange das so ist:

**Gesperrt:** Ortsseiten · `LocalBusiness` in strukturierten Daten · Google-Unternehmensprofil ·
Ortsnamen in Title, H1, Meta, URL oder Fließtext · NAP-Aussagen · Service-Area.

**Nicht gesperrt:** alles andere. Strukturierte Daten nutzen `Organization` **ohne** Adressfeld.

**Kein Platzhalter wird durch einen erfundenen Wert ersetzt.** Findest du irgendwo einen konkreten
Ortsnamen in den Vorgaben: **melden**, nicht übernehmen.

---

## 1. Was du baust

Das **SARTU-Kunden- und Adminportal** in der **Stufe-0-Ausbaustufe**: eine vollständig sichtbare und bedienbare Oberfläche für den gesamten Kundenprozess vom Angebot bis zur ersten Pflege — mit bewusst **manueller Mechanik** dahinter.

Das ist der **eingeloggte Teil** derselben Website: `/portal/` und `/admin/`. Die öffentlichen Seiten haben einen eigenen Auftrag (`CODEX_AUFTRAG_WEBSITE.md`) — **aber dasselbe Repository und dasselbe Projekt** (§0c).

---

## 1a. Verhältnis zum Website-Auftrag

**Ein Projekt, zwei Arbeitspakete.** Öffentliche Seiten und Kundenbereich teilen sich Repository,
Verzeichnisstruktur, Layouts, Partials, Komponenten, Hilfsfunktionen und Datenbank. Sie werden
getrennt **beauftragt**, weil das die Arbeit ordnet — nicht, weil es getrennte Systeme wären.

**Reihenfolge:** Dieses Arbeitspaket kommt zuerst. Die öffentlichen Seiten brauchen am Ende **echte**
Screenshots aus dieser Oberfläche (Abschnitt 7a); gezeichnete Attrappen sind ausgeschlossen. Die
öffentlichen Seiten dürfen trotzdem parallel entstehen — gesperrt ist nur der **Livegang**.

**Was daraus folgt:**
- Das Grundgerüst (`/app/bootstrap.php`, Layouts, Hilfsfunktionen, Datenbankschicht) baust **du**. Das Website-Arbeitspaket setzt darauf auf
- Die Designentscheidung gilt für beide. Kunden- und Adminbereich müssen sich **sichtbar** von den öffentlichen Seiten absetzen — mit denselben Variablen, nicht mit einem zweiten Designsystem
- **Es gibt keine Schnittstelle über das Netz zwischen beiden.** Der Bedarfsscheck ruft direkt den Anfragedienst auf (Lastenheft §4b.1). Kein Token, kein gemeinsames Geheimnis

## 2. Lesereihenfolge und Rangfolge

> **Korrigiert am 01.08.2026 nach externer Prüfung.** Diese Liste stand im Widerspruch zur
> Rangfolge in `UEBERGABE_DATEILISTE.md`: Dort ist `SARTU_ENTSCHEIDUNGEN_OFFEN.md` **Rang 1** und
> das Portal-Lastenheft **Rang 4**, hier war es umgekehrt. Ein Bauchat hätte nach eigener Regel
> anhalten müssen.
>
> **Verbindlich ist ab sofort die Rangfolge in `UEBERGABE_DATEILISTE.md`.** Die Liste unten ist
> nur noch die **Lesereihenfolge** — in welcher Folge man die Unterlagen durcharbeitet.

**Lesereihenfolge** (bei Widersprüchen entscheidet die Rangfolge in `UEBERGABE_DATEILISTE.md`):

1. **`CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md`** — dein Hauptdokument. Enthält Stack, Datenmodell, Statuslogik, jeden Screen, jeden Text, jede E-Mail, Sicherheitsregeln, Testfälle und Abnahme
2. **`CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`, Abschnitt „10a. Arbeitsverteilung Codex ↔ Claude Code"** — **verbindlich**, siehe Abschnitt 2a unten
3. **`CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`** — wie die visuelle Ebene entsteht (Farben und Schriften sind **nicht** vorgegeben)
4. **`CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`** im Übrigen — nur nachschlagen: Portalvision, Einführungskonzept, Stufenmodell, Datenmodell
5. `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` — nur §2 (Sprachregeln), die auch hier gelten
6. **`SARTU_ENTSCHEIDUNGEN_OFFEN.md`** — alle Platzhalter und Sperren
7. `konzepte/` — historische Quellen. **Nur nachschlagen**, enthält veraltete Preise und abgelöste Modelle
8. `design/_verworfen/` — **ignorieren**

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
Projektgerüst nach §1.3, Datenbankmigrationen, Datenmodell aus §4, Sitzungs- und Sicherheitsgrundlage aus §3, Testaufbau gegen echtes MySQL/MariaDB.
**Abschluss:** `tests/TenantIsolationTest.php` existiert und ist grün.

### Etappe 2 — Anmeldung und Erstkontakt
Magic-Link-Anmeldung (§6), Willkommensstrecke (§7, drei Bildschirme), Abmeldung, Fehlerseiten.

### Etappe 3 — Kundenportal
Alle Screens aus §8 in der dort genannten Reihenfolge, mit allen Texten und Leerzuständen.

### Etappe 4 — Adminportal und Anfrageeingang
Alle Screens aus §9 inklusive Aufgabenvorlagen, dazu die Bedarfsscheck-Annahme und die Anfrageliste aus §4b.

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

**Vorgegeben und nicht zu ändern:** Stack und Verzeichnisstruktur (§1.2 und §1.3), Datenmodell (§4), Statuswerte und Kundentexte (§5), alle Oberflächentexte (§6–§9), E-Mail-Texte (§10), Sicherheitsregeln (§3), Umfangsgrenze (§0.2, §0.3 und §0.3a).

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
2. **Nur `/public` ist erreichbar** (§1.3). Der Webserver zeigt auf `/public`. Liegen `/app`, `/storage`, `/migrations` oder `.env` im Netz, ist das eine Datenpanne, kein Schönheitsfehler — praktisch prüfen, nicht annehmen.
3. **Klartext statt Systemcodes** (§5). Der Kunde sieht nie `qa_failed` oder `angebot_offen`, sondern „Ihre Freigabe fehlt" und „Angebot liegt vor".
4. **Ohne JavaScript bedienbar.** Jede Aktion ist ein normales Formular mit `POST`.

---

## 7. Was du am Ende ablieferst

Vollständig nach §18 des Lastenhefts:

1. Lauffähiges Portal
2. `README.md` mit Einrichtung, Umgebungsvariablen, Migration, Seed, Deployment und Backup
3. **Testbericht** über alle 88 Fälle aus §16
4. **Messwerte** — konkret diese, nicht „Performance allgemein":
   - Antwortzeit des Servers je Kernscreen (Median und 95. Perzentil, angemeldeter Zustand)
   - Seitengröße je Kernscreen: HTML, CSS, JS getrennt, in KB gzip
   - Dauer der Migration von leerer Datenbank und Dauer des vollständigen Testlaufs
   - Uploadgrenzen praktisch getestet: größte erlaubte Datei, Verhalten bei Überschreitung, Verhalten bei unerlaubtem Typ
   - Barrierefreiheits-Momentaufnahme je Kernscreen: Kontrastwerte, Tastaturweg, Fokusreihenfolge, Beschriftungen
5. **Offene-Punkte-Liste**
6. **Screenshot-Satz aus der echten Oberfläche** (Abschnitt 7a)
7. **`IMPLEMENTATION_SUMMARY.md`**: gebaute Struktur, Abweichungen vom Plan mit Begründung, offene Punkte
8. **`MIGRATION_NOTES.md`**, falls aus einem Prototyp etwas übernommen wurde: was, warum, was verworfen
9. **Kurzbeschreibung des Anfragedienstes** für das Website-Arbeitspaket: Signatur von `AnfrageService::anlegen()`, erwartete Felder, Rückgabe im Erfolgs- und Fehlerfall

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
