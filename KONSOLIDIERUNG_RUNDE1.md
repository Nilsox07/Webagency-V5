# Konsolidierung — Runde 1: Bestandsaufnahme

Stand 03.08.2026. **Noch nichts verschoben, noch nichts gelöscht.** Diese Datei ist die Landkarte,
auf der die Zusammenführung aufsetzt.

---

## Der Bestand

| | Dateien | Zeilen |
|---|---:|---:|
| Wurzelverzeichnis | 39 | **15.397** |
| `archiv/konzepte/` (laut `CLAUDE.md` historisch) | 20 | 8.299 |
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

### D — Abgelöst: ins Archiv (710 Z. + `archiv/konzepte/`)

| Datei | Z. | warum |
|---|---:|---|
| `archiv/CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md` | 308 | vom Website-Lastenheft abgelöst |
| `archiv/CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md` | 402 | `CLAUDE.md` führt sie ausdrücklich als historisch |
| `archiv/konzepte/` | 8299 | `CLAUDE.md`: „historisch: veraltete Preise, abgelöste Stacks" |

---

## Ergebnis

Umgesetzt wurde **nicht** ein Monolith, sondern **ein Thema, eine Datei, eine Wahrheit** unter
`spezifikation/` — 17 Dateien, 1.729 Zeilen, Einstieg über `spezifikation/00_UEBERSICHT.md`.

Der Grund für die Abkehr vom Monolithen: Das Problem war nie die **Zahl** der Dateien, sondern
dass dieselbe Sache in dreien mit drei Werten stand. Themendateien mit klarem Besitzer lösen
das; eine 7.100-Zeilen-Datei hätte es nur umgezogen.

**Runde 2** hat zusammengeführt, **Runde 3** hat gegengeprüft: sieben echte Dubletten gefunden
und aufgelöst, zwei bewusste Kopplungen markiert, alle 17 Dateien verweisen aufeinander.
Register und Begründungen: `spezifikation/00_UEBERSICHT.md`.
