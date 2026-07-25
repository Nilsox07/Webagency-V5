# Übergabe-Dateiliste — was Codex bekommen muss

**Zweck:** Diese Liste verhindert den häufigsten Fehler beim Weiterreichen — dass ein Auftrag
gestartet wird, dessen Hauptquelle fehlt. Ein Auftrag mit fehlendem Hauptdokument ist **nicht**
baufreig, egal wie gut er formuliert ist.

**Alle Dateien liegen im Repository `Webagency-V5`, Branch `claude/sartu-concept-review-pdhb5t`.**
Ein Download-Ordner ist keine gültige Quelle: Dort fehlen erfahrungsgemäß Dateien, und niemand
merkt es, bevor gebaut wird.

---

## Für den Portalauftrag

| # | Datei | Rolle | Ohne sie? |
|---|---|---|---|
| 1 | `CODEX_AUFTRAG_PORTAL.md` | die Anweisung selbst | — |
| 2 | `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` | **Hauptdokument** — Stack, Datenmodell, jeder Screen, jeder Text, 59 Testfälle | **Abbruch.** Nicht baubar |
| 3 | `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` | Vorgehen für die visuelle Ebene | Abbruch |
| 4 | `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` | Nachschlagewerk + verbindliche Arbeitsverteilung §10a | Abbruch |
| 5 | `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` | nur §2 Sprachregeln | Melden, mit Rest fortfahren |
| 6 | `konzepte/` (20 Dateien) | historische Quellen, **veraltete Preise** | Melden, mit Rest fortfahren |

## Für den Websiteauftrag

| # | Datei | Rolle | Ohne sie? |
|---|---|---|---|
| 1 | `CODEX_AUFTRAG_WEBSITE.md` | die Anweisung selbst | — |
| 2 | `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` | **Hauptdokument** — Struktur, fertige Texte, Felder, SEO, Abnahme | **Abbruch.** Nicht baubar |
| 3 | `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` | Vorgehen für die visuelle Ebene | Abbruch |
| 4 | `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md` | Architektur und Begründungen | Melden, mit Rest fortfahren |
| 5 | `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` | Geschäftsmodell, Preise, §10a, §16a | Abbruch |
| 6 | `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` | nur Abschnitt 4b — die Schnittstelle | Melden; Formularversand kapseln (Website-Lastenheft §9.5b) |

## Nicht übergeben

| Was | Warum |
|---|---|
| `design/_verworfen/` | verworfene Entwürfe, keine Vorgabe, auch nicht als Anregung |
| `CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md` | Bewertungsdokument für den Menschen, keine Bauvorlage |

---

## Startprüfung — vor der ersten Zeile Code

Codex führt **zuerst** diesen Ablauf aus und meldet das Ergebnis:

1. Alle Dateien der zutreffenden Liste auf Vorhandensein prüfen
2. Bei jedem Hauptdokument zusätzlich prüfen: enthält es die erwarteten Abschnitte?
   - Portal-Lastenheft: `## 0.` bis `## 18.` — insbesondere `## 4b.` und `## 16.`
   - Website-Lastenheft: `## 0.` bis `## 17a.` — insbesondere `## 9.` und `## 14a.`
3. **Fehlt eine Datei mit „Abbruch": nicht anfangen.** Melden, welche Datei fehlt, und auf
   Nachlieferung warten. Nicht rekonstruieren, nicht aus dem Zusammenhang erraten,
   keinen Ersatz erfinden
4. Fehlt eine Datei mit „Melden": den betroffenen Teil als offenen Punkt vermerken und mit dem
   Rest fortfahren
5. Weicht ein Dateiname ab: **melden**, nicht raten. Ähnliche Namen sind bei diesen Dokumenten
   ein Warnzeichen, weil es mehrere Versionsstände gab

**Ergebnis der Startprüfung gehört als erster Absatz in den Bericht** — mit der Anzahl gefundener
Dateien und der Bestätigung, dass das Hauptdokument vollständig ist.
