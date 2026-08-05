# Konsolidierung — Runde 1: Bestandsaufnahme

Stand 03.08.2026. **Noch nichts verschoben, noch nichts gelöscht.** Diese Datei ist die Landkarte,
auf der die Zusammenführung aufsetzt.

---

## Der Bestand

| | Dateien | Zeilen |
|---|---:|---:|
| Wurzelverzeichnis | 39 | **15.397** |
| `konzepte/` (laut `CLAUDE.md` historisch) | 20 | 8.299 |
| `design/`, `.claude/` | 5 | 1.191 |
| **gesamt** | **64** | **24.887** |

---

## Zwei Befunde, die den Weg bestimmen

### 1. Die Trennung, die Sie wollen, ist schon da — nur verstreut

Das Masterkonzept beschildert sie an zwei Stellen selbst:

> **§13 — Eigene SARTU-Website – ausgelagert (Autoritätsregel)**
>
> **§16 — SEO-/GEO-Strategie (Produktleistung für Kundenwebsites)**
> *„Dieser Abschnitt beschreibt, **was der Kunde kauft**. Die SEO-/GEO-Struktur der **eigenen**
> SARTU-Website steht in der Website-Datei, Abschnitt 9."*

Die Achse ist also nicht erfunden, sie ist nur nirgends durchgezogen. **Genau daher kommen die
Widersprüche.**

### 2. 36 von 39 Dateien verweisen aufeinander

Darunter `CLAUDE.md` und `.claude/skills/sartu-texter/SKILL.md`. Wer sie pauschal ins Archiv
schiebt, zerreißt:

- die **Rangfolge bei Widersprüchen** — sie steht laut `CLAUDE.md` **allein** in
  `UEBERGABE_DATEILISTE.md`
- den **`sartu-texter`-Skill**, ein Werkzeug, das auf `SARTU_TEXTREGELN.md` zeigt
- die **Zuordnung der 88 Testfälle** zu den Stufen in `REIHENFOLGE.md`

**Deshalb wird nach Art getrennt, nicht nach Alter.**

---

## Einordnung aller 39 Wurzeldateien

### A — Spezifikation: gehört in die neue Datei (7.129 Zeilen)

| Datei | Z. | überwiegend |
|---|---:|---|
| `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` | 2140 | **eigene Seite** (Kundenbereich + Adminbereich) |
| `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` | 1585 | **eigene Seite** |
| `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` | 1040 | **beides** — Geschäftsmodell, Preise, Kundenleistung |
| `SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` | 682 | **eigene Seite**, §5 übergreifend |
| `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` | 665 | beides |
| `SARTU_ENTSCHEIDUNGEN_OFFEN.md` | 587 | beides |
| `SARTU_TEXTREGELN.md` | 456 | beides |
| `SARTU_KUNDENMOTIVE_BELEGT.md` | 310 | Grundlage |
| `SARTU_CORPORATE_DESIGN.md` | 276 | beides |
| `SARTU_BRANCHENFAKTEN.md` | 250 | Grundlage |
| `SARTU_DESIGNSYSTEM.md` | 214 | beides |
| `GEO_DISCOVERY_CHECKLIST.md` | 137 | **Kundenseiten** |

### B — Ablauf und Werkzeug: bleibt liegen, wird **nicht** zusammengeführt (2.851 Z.)

Diese Dateien sagen nicht *was gebaut wird*, sondern *wie gearbeitet wird*. In einer
Spezifikation hätten sie nichts verloren.

`CLAUDE.md` · `UEBERGABE_DATEILISTE.md` (**trägt die Rangfolge**) · `REIHENFOLGE.md`
(**trägt die 88 Testfälle je Stufe**) · `ENTWICKLUNGSUMGEBUNG.md` · `LIVEGANG.md` ·
`BAUFREIGABE.md` · `AUDIT_VOR_BAUBEGINN.md` · `MODELLPLAN.md` · `CODEX_AUFTRAG_WEBSITE.md` ·
`CODEX_AUFTRAG_PORTAL.md` · `CODEX_SESSIONS_ABLAUF.md` · die vier `PROMPT_NEUE_SESSION_*.md`

### C — Ergebnisse und Protokolle: nie zusammenführen (2.084 Z.)

Sie halten fest, *was passiert ist*. Zusammengeführt wären sie falsch, weil sie fortgeschrieben
werden.

`OFFENE_PRUEFUNGEN.md` · `IMPLEMENTATION_PLAN.md` · `IMPLEMENTATION_SUMMARY.md` ·
`ABSCHLUSSBERICHT.md` · `MESSUNGEN.md` · `STAND.md` · `TEXTPRUEFUNG_WEBSITE.md` ·
`KEYWORD_VALIDATION.md` · `OFFENE_ENTSCHEIDUNGEN.md`

### D — Abgelöst: ins Archiv (710 Z. + `konzepte/`)

| Datei | Z. | warum |
|---|---:|---|
| `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md` | 308 | vom Website-Lastenheft abgelöst |
| `CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md` | 402 | `CLAUDE.md` führt sie ausdrücklich als historisch |
| `konzepte/` | 8299 | `CLAUDE.md`: „historisch: veraltete Preise, abgelöste Stacks" |

---

## Die Gliederung der neuen Datei

```
SARTU_SPEZIFIKATION.md

TEIL 0 — Gemeinsame Grundlage
  Geschäftsmodell · Positionierung · Zielgruppe · Preise (die einzige Quelle jeder Zahl)
  Textregeln · Corporate Design · Designsystem · Belege

TEIL 1 — WAS DER KUNDE KAUFT   (Kundenwebsites)
  Leistungsumfang je Paket · Rundum-Schutz · Domain/Hosting/E-Mail
  SEO-/GEO-Startsystem · Designprinzipien Kundenwebsites · Recht und Vertrag
  Quellen: Masterkonzept §4 §6 §16 §20 §22, GEO_DISCOVERY_CHECKLIST

TEIL 2 — WAS SARTU FÜR SICH BAUT   (sartu.de)
  2a Öffentliche Website — alle Seiten, Sektion für Sektion
  2b Kundenbereich — Screen für Screen, Datenmodell, Status, Sicherheit
  2c Adminbereich
  2d SEO/GEO der eigenen Seite — Keywordstrategie, URL-Liste
  Quellen: Website-Lastenheft, Portal-Lastenheft, Keywordstrategie, Masterkonzept §13

TEIL 3 — Offene Entscheidungen und Platzhalter
```

---

## Eine Grenze, die ich benennen muss

**„Alle Inhalte aus allen Dateien" in **einer** Datei ergibt eine Datei mit rund 7.100 Zeilen.**
Das Problem der Unauffindbarkeit wäre damit nicht gelöst, nur umgezogen.

Der Grund ist nicht Faulheit: Die Lastenhefte bestehen zur Hälfte aus **Begründungsblöcken**
(„Ersetzt am 01.08.2026, weil …"). Die sind wertvoll — sie haben mich diese Sitzung mehrfach
davor bewahrt, eine zurückgezogene Fassung wieder einzubauen — aber sie sind kein Bauwissen.

**Zwei Wege, und das ist die Entscheidung:**

| | Vollständige Zusammenführung | Kanonische Spezifikation |
|---|---|---|
| Ergebnis | **eine** Datei, ~7.100 Zeilen | **eine** Datei, ~2.500 Zeilen |
| Inhalt | alles, auch jede Begründung | jede verbindliche Regel, Zahl, Tabelle, Textvorgabe |
| Begründungen | drin | bleiben in den Quellen, mit Verweis |
| Quellen danach | Archiv | bleiben lesbar, sind aber nicht mehr maßgeblich |
| Widersprüche | **aufgelöst** | **aufgelöst** |
| Auffindbarkeit | mäßig | gut |

Beide lösen Ihr eigentliches Problem — dass in jeder Datei etwas anderes steht. Der Unterschied
ist nur, ob die Begründungen mitwandern.

**Runde 2 (Zusammenführen) und Runde 3 (Vollständigkeitsprüfung) folgen, sobald das entschieden
ist.**
