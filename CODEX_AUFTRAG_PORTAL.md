# Auftrag an Codex — SARTU-Portal bauen

**Das ist die Übergabe-Anweisung.** Gib Codex diese Datei als Auftrag.

---

## 1. Was du baust

Das **SARTU-Kunden- und Adminportal** in der **Stufe-0-Ausbaustufe**: eine vollständig sichtbare und bedienbare Oberfläche für den gesamten Kundenprozess vom Angebot bis zur ersten Pflege — mit bewusst **manueller Mechanik** dahinter.

**Nicht** die öffentliche SARTU-Website. Das ist ein eigenes Projekt mit eigenem Auftrag (`CODEX_AUFTRAG_WEBSITE.md`).

---

## 2. Lesereihenfolge und Rangfolge

Bei Widersprüchen gilt die **niedrigere Nummer**:

1. **`CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md`** — dein Hauptdokument. Enthält Stack, Datenmodell, Statuslogik, jeden Screen, jeden Text, jede E-Mail, Sicherheitsregeln, Testfälle und Abnahme
2. **`CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`** — wie die visuelle Ebene entsteht (Farben und Schriften sind **nicht** vorgegeben)
3. **`CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`** — nur nachschlagen: §9 Portalvision, §9a Einführung, §23 Stufenmodell, §12 Datenmodell
4. `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` — nur §2 (Sprachregeln), die auch hier gelten
5. `konzepte/` — historische Quellen. **Nur nachschlagen**, enthält veraltete Preise und abgelöste Modelle
6. `design/_verworfen/` — **ignorieren**

---

## 3. Reihenfolge deiner Arbeit

**Baue in dieser Reihenfolge und stoppe an den markierten Stellen.**

### Etappe 1 — Fundament
Projektgerüst, Datenbankmigrationen, Datenmodell aus §4, Session- und Sicherheitsgrundlage aus §3, Testaufbau gegen echtes PostgreSQL.
**Abschluss:** `test/tenant-isolation.test.js` existiert und ist grün.

### Etappe 2 — Anmeldung und Erstkontakt
Magic-Link-Anmeldung (§6), Willkommensstrecke (§7), Abmeldung, Fehlerseiten.

### Etappe 3 — Kundenportal
Alle Screens aus §8 in der dort genannten Reihenfolge, mit allen Texten und Leerzuständen.

### Etappe 4 — Adminportal
Alle Screens aus §9 inklusive Aufgabenvorlagen.

### Etappe 5 — E-Mails, Uploads, Abnahme
Alle Vorlagen aus §10, Uploadregeln aus §11, Testfälle aus §16 vollständig, Definition of Done aus §17.

**Nach jeder Etappe:** kurzer Bericht mit dem, was gebaut wurde, welche Tests laufen und was offen ist. **Keine Etappe gilt als fertig, solange nicht alle Tests der Vor-Etappen grün sind.**

---

## 4. Wo du selbst entscheidest — und wo nicht

**Du entscheidest:** Ordnerstruktur, Modulschnitt, Hilfsfunktionen, Migrationswerkzeug, Testaufbau, wie du EJS-Partials organisierst.

**Vorgegeben und nicht zu ändern:** Stack (§1), Datenmodell (§4), Statuswerte und Kundentexte (§5), alle Oberflächentexte (§6–§9), E-Mail-Texte (§10), Sicherheitsregeln (§3), Umfangsgrenze (§0.2 und §0.3).

**Nicht vorgegeben:** Farben, Schriften, Formen. Dafür gilt das Design-Briefing — Kunden- und Adminbereich müssen visuell unterscheidbar sein. Halte alle visuellen Werte als **zentrale Variablen**, damit ein späterer Wechsel ein Variablentausch bleibt.

---

## 5. Harte Grenzen

**Nicht bauen, auch nicht vorbereitend** (§0.3): automatische Domainregistrierung · Zahlungsdienst-Anbindung, Mandate, Webhooks · KI-Orchestrierung · automatische Builds oder Deployments · SEO-Zentrale · Rollback · Lead-Inbox · Buchhaltung · mehrere Benutzer je Kunde · Dateiversionierung · Dunkelmodus.

**Nicht erfinden:** Rechtstexte, echte Anschriften, echte Kundennamen, realistische Rechnungsnummern, Referenzen.

**Zahlungen:** Es gibt in Stufe 0 **keine** Programmanbindung. Der Admin trägt einen Zahlungslink ein und setzt den Status nach eigener Prüfung. Der Status darf **niemals** aus einer Rückkehr-URL abgeleitet werden (§12).

---

## 6. Besondere Sorgfalt

Diese drei Punkte entscheiden über Brauchbarkeit und Haftung:

1. **Mandantentrennung** (§3.1–3.2). Jede Abfrage filtert nach `organization_id` aus der **Session**. Der Isolationstest ist unantastbar.
2. **Klartext statt Systemcodes** (§5). Der Kunde sieht nie `qa_failed` oder `angebot_offen`, sondern „Ihre Freigabe fehlt" und „Angebot liegt vor".
3. **Ohne JavaScript bedienbar.** Jede Aktion ist ein normales Formular mit `POST`.

---

## 7. Was du am Ende ablieferst

Vollständig nach §18 des Lastenhefts:
Lauffähiges Portal · `README.md` mit Einrichtung, Migration, Seed, Deployment und Backup · Testbericht über alle 28 Fälle · Messwerte · Offene-Punkte-Liste · **Screenshot-Satz aus der echten Oberfläche** für die Website (Cockpit, Angebot, Aufgaben, Vorschau, Rechnungen, Öffnungszeiten).

Der Screenshot-Satz ist kein Nebenprodukt: Die öffentliche Website braucht ihn als Produktbeweis und darf dafür **keine** gezeichneten Attrappen verwenden.

**Arbeite nicht ins Blaue:** Fehlt eine Information oder widerspricht sich etwas, melde es, statt zu raten.
