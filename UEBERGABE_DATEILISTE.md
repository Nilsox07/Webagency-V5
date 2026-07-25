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
| 5 | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` | **alle Platzhalter und Sperren** — Standort, Rechtstexte, Design, Betriebsumgebung | **Abbruch.** Sonst werden Werte erfunden |
| 6 | `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` | nur §2 Sprachregeln | Melden, mit Rest fortfahren |
| 7 | `konzepte/` (20 Dateien) | historische Quellen, **veraltete Preise und abgelöste Stacks** | Melden, mit Rest fortfahren |

## Für den Websiteauftrag

| # | Datei | Rolle | Ohne sie? |
|---|---|---|---|
| 1 | `CODEX_AUFTRAG_WEBSITE.md` | die Anweisung selbst | — |
| 2 | `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` | **Hauptdokument** — Struktur, fertige Texte, Felder, SEO, Abnahme | **Abbruch.** Nicht baubar |
| 3 | `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` | Vorgehen für die visuelle Ebene | Abbruch |
| 4 | `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md` | Architektur und Begründungen | Melden, mit Rest fortfahren |
| 5 | `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` | Geschäftsmodell, Preise, §10a, §16a | Abbruch |
| 6 | `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` | **§1 Stack, Struktur, Hosting** + §4b Anfrageeingang | **Abbruch.** Ohne §1 ist die Architektur unbekannt |
| 7 | `SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` | welche Seite welche Suchintention bedient, Baureihenfolge | **Abbruch.** Sonst entstehen Texte ohne Zielrichtung |
| 8 | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` | alle Platzhalter und Sperren | **Abbruch** |

## Nicht übergeben

| Was | Warum |
|---|---|
| `design/_verworfen/` | verworfene Entwürfe, keine Vorgabe, auch nicht als Anregung |
| `CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md` | Bewertung vom 24.07.2026 für den Menschen, **keine Bauvorlage**. Enthält bewusst überholte Empfehlungen (alter Stack, alte Palette, alter Launch-Umfang) — sie dokumentieren die Entscheidungshistorie und sind im Dokument als `[ABGELÖST]` gekennzeichnet. **Wer daraus baut, baut einen überholten Stand.** |

---

## Startprüfung — vor der ersten Zeile Code

Codex führt **zuerst** diesen Ablauf aus und meldet das Ergebnis:

1. Alle Dateien der zutreffenden Liste auf Vorhandensein prüfen
2. Bei jedem Hauptdokument zusätzlich prüfen: enthält es die erwarteten Abschnitte?
   - Portal-Lastenheft: `## 0.` bis `## 18.` — insbesondere `## 1.` (Stack), `## 4b.` und `## 16.`
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

---

## Zwei harte Gates vor dem Bau

Diese beiden Punkte werden häufig übersprungen, weil die Lastenhefte so vollständig wirken. Genau
deshalb stehen sie hier noch einmal:

### Gate 1 — `IMPLEMENTATION_PLAN.md` vor der ersten Codezeile

Kein Produktionscode, bevor der Plan vorliegt und vorgelegt wurde. Inhalt: Bestand, Umgang mit
vorhandenen Prototypen (je mit Begründung), Zielstruktur, Modulgrenzen, Datenmodellquelle,
Reihenfolge, Risiken, Testplan, offene Entscheidungen. Danach der **kleinste lauffähige Stand**,
dann Bericht. Details in beiden Aufträgen, Abschnitt 0b.

### Gate 2 — Designentscheidung vor dem Vollausbau

Nach dem Design-Briefing entstehen **2–3 klickbare Startseitenvarianten mit echten Texten**. Dann
**anhalten**. Der Mensch entscheidet die Richtung. Erst danach werden weitere Seiten ausgebaut.

Wer nach dem Briefing durchbaut, hat das Gate verletzt — und im Zweifel Dutzende Seiten in einer
Richtung gebaut, die verworfen wird.

---

## Architektur in einem Satz

**Ein PHP-Projekt, ein Repository, eine Domain:** öffentliche Seiten unter `/`, Kundenbereich unter
`/portal/`, interner Bereich unter `/admin/`, Serverfunktionen unter `/api/`. Verbindlich ist
Portal-Lastenheft §1. **Kein** Node, **kein** Supabase, **kein** SPA-Framework, **kein** WordPress —
frühere Fassungen nannten das, es ist überholt.
