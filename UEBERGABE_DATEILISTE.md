# Übergabe-Dateiliste — was Codex bekommen muss

**Zweck:** Diese Liste verhindert den häufigsten Fehler beim Weiterreichen — dass ein Auftrag
gestartet wird, dessen Hauptquelle fehlt. Ein Auftrag mit fehlendem Hauptdokument ist **nicht**
baufreig, egal wie gut er formuliert ist.

**Alle Dateien liegen im Repository `Webagency-V5`, Branch `main`.**
Ein Download-Ordner ist keine gültige Quelle: Dort fehlen erfahrungsgemäß Dateien, und niemand
merkt es, bevor gebaut wird.

---

## Für den Portalauftrag

| # | Datei | Rolle | Ohne sie? |
|---|---|---|---|
| 1 | `CODEX_AUFTRAG_PORTAL.md` | die Anweisung selbst | — |
| 2 | `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` | **Hauptdokument** — Stack, Datenmodell, jeder Screen, jeder Text, 88 Testfälle | **Abbruch.** Nicht baubar |
| 3 | `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` | Vorgehen für die visuelle Ebene | Abbruch |
| 4 | `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` | Nachschlagewerk + verbindliche Arbeitsverteilung §10a | Abbruch |
| 5 | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` | **alle Platzhalter und Sperren** — Standort, Rechtstexte, Design, Betriebsumgebung | **Abbruch.** Sonst werden Werte erfunden |
| 6 | `ENTWICKLUNGSUMGEBUNG.md` | **wie PHP, Composer und die Datenbank aufgerufen werden** — und was ohne Datenbank trotzdem gebaut wird | **Abbruch.** Sonst wird falsch nach PHP gesucht oder wegen fehlender Datenbank angehalten |
| 6a | `REIHENFOLGE.md` | **welche Teile jetzt gebaut werden und welche warten** — Stufe A, B, C | **Abbruch.** Sonst entstehen alle 20 Tabellen auf einmal |
| 6b | `SARTU_TEXTREGELN.md` | **wie jeder Text formuliert sein muss** — zehn zählbare Regeln, Wortlisten, Pflicht-Prüfbericht | **Abbruch.** Sonst entsteht wieder der Ton, der schon zweimal verworfen wurde |
| 6c | `.claude/skills/sartu-texter/SKILL.md` | **das Handwerk dahinter** — getrennte Vorgaben für Überschriften, Fließtext, Mikrotexte und Metadaten; belegte SEO- und GEO-Regeln | Melden. Ohne ihn gelten die Textregeln trotzdem, aber jede Formulierung wird geraten |
| 7 | `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` | nur §2 Sprachregeln | Melden, mit Rest fortfahren |
| 8 | `konzepte/` (20 Dateien) | historische Quellen, **veraltete Preise und abgelöste Stacks** | Melden, mit Rest fortfahren |

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
| 9 | `GEO_DISCOVERY_CHECKLIST.md` | technische Auffindbarkeit für KI-Systeme — **vor Livegang abzuhaken** | Melden, mit Rest fortfahren |
| 10 | `ENTWICKLUNGSUMGEBUNG.md` | **wie PHP, Composer und die Datenbank aufgerufen werden** — und was ohne Datenbank trotzdem gebaut wird | **Abbruch.** Sonst wird falsch nach PHP gesucht oder wegen fehlender Datenbank angehalten |
| 11 | `SARTU_TEXTREGELN.md` | **wie jeder Text formuliert sein muss** — zehn zählbare Regeln, Wortlisten. Der **Prüfbericht mit Zahlen** gehört zu jeder abgegebenen Seite | **Abbruch.** Sonst entsteht wieder der Ton, der schon zweimal verworfen wurde |
| 11a | `.claude/skills/sartu-texter/SKILL.md` | **das Handwerk dahinter** — Überschrift, Fließtext, Schaltfläche und Metadaten haben **verschiedene** Regeln. Dazu, was an SEO und GEO belegt ist und was Mythos | **Abbruch.** Beim Websiteauftrag entsteht fast nur Text |
| 12 | `REIHENFOLGE.md` | **Abschnitt „Zwei Livegänge"** — welche Portalfunktionen zum Zeitpunkt des Websitestarts überhaupt existieren | **Abbruch.** Sonst bewirbt die Seite Funktionen, die es noch nicht gibt |

## Nicht übergeben

| Was | Warum |
|---|---|
| `design/_verworfen/` | verworfene Entwürfe, keine Vorgabe, auch nicht als Anregung |
| `konzepte/` **vollständig lesen** | ~360 KB historische Quellen mit veralteten Preisen und abgelösten Stacks. **Nur gezielt nachschlagen**, wenn eine bestimmte Frage es verlangt — nie vorsorglich am Anfang einlesen. Das kostet mehr Kontingent als alle Bauunterlagen zusammen und bringt nichts |
| `CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md` | Bewertung vom 24.07.2026 für den Menschen, **keine Bauvorlage**. Enthält bewusst überholte Empfehlungen (alter Stack, alte Palette, alter Launch-Umfang) — sie dokumentieren die Entscheidungshistorie und sind im Dokument als `[ABGELÖST]` gekennzeichnet. **Wer daraus baut, baut einen überholten Stand.** |

---

## Rangfolge — welche Datei gewinnt bei Widerspruch

**Sechs Dateien tragen „FINAL" im Namen.** Das Wort bedeutet nichts über ihr Alter. Zweimal hat
eine externe Prüfung deshalb einen überholten Stand verteidigt, einmal wurden zwei verbindliche
Navigationen im selben Dokument gefunden. Deshalb hier eine Reihenfolge, die keine Auslegung
zulässt:

| Rang | Quelle | Gilt für |
|---|---|---|
| 1 | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` | Alles, was noch offen ist. **Schlägt jede andere Datei.** Wo hier `offen` steht, wird nichts gebaut und nichts erfunden |
| 2 | `REIHENFOLGE.md` | **nur der Zeitpunkt** — was jetzt gebaut wird und was wartet |
| 3 | `SARTU_TEXTREGELN.md` | **nur die Form** jedes Textes — Satzlänge, Wortlisten, Prüfbericht |
| 4 | `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` | Stack, Datenmodell, Kundenbereich, Sicherheit, Testfälle |
| 5 | `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` | öffentliche Seiten, Struktur, Wortlaut |
| 6 | `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` | visuelle Ausführung |
| 7 | `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` | Geschäftsmodell und Preise — **die Preistabelle ist die Quelle jeder Zahl** |
| — | `CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md` | **Nachschlagewerk, keine Bauvorlage.** §5 Sektionsdramaturgie ist ausdrücklich abgelöst |
| — | `CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md` | Bewertung für den Menschen. Enthält als `[ABGELÖST]` markierte Empfehlungen |
| — | `konzepte/` | historisch |

**Zwei Regeln dazu:**

1. **Ein abgelöster Abschnitt wird gekennzeichnet, nicht gelöscht.** Er dokumentiert, warum etwas
   heute anders ist. Wer ihn löscht, provoziert dieselbe Diskussion in drei Wochen erneut
2. **Widersprechen sich zwei Stellen im selben Dokument, gilt die mit der Begründung.** Steht bei
   keiner eine, ist es ein Fehler — **melden, nicht auswählen**

## Nur `main` ist gültig

**Der Stand liegt auf `main`.** Ältere Zweige — insbesondere `claude/sartu-concept-review-pdhb5t`
— sind unvollständig: Dort fehlen `REIHENFOLGE.md`, `SARTU_TEXTREGELN.md` und alle Umfangszahlen
in den Preisen.

**Drei externe Prüfungen haben bereits einen alten Zweig bewertet** und Befunde gemeldet, die auf
`main` längst behoben waren. Wer prüft oder baut, prüft `main`.

> **Offen und beim Betreiber:** Der Standardzweig auf GitHub zeigt noch auf den alten Zweig. Bis er
> auf `main` umgestellt ist, landet dort jeder, der den Repository-Link öffnet.

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

## Wie der Bau abläuft

**Vier Sitzungen, drei Entscheidungspunkte** — Ablauf und fertige Startprompts stehen in
`CODEX_SESSIONS_ABLAUF.md`:

| Sitzung | Was entsteht | Endet mit |
|---|---|---|
| 1 | `IMPLEMENTATION_PLAN.md`, Projektgerüst, 2–3 Designvarianten | **Stopp** — der Mensch entscheidet |
| 2 | Kundenbereich **Stufe A** (A0–A3), 87 der 88 Tests, Screenshots der Stufe | Bericht |
| — | **Gate 3:** drei Selbstpflege-Funktionen bauen oder Copy streichen (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5a) | **Stopp** |
| 3 | Kundenbereich **Stufe B**, Testfall 19, vollständiger Screenshot-Satz | Bericht |
| 4 | Öffentliche Seiten vollständig | Bericht |

Sitzung 2, 3 und 4 laufen weitgehend autonom. Sitzung 1 nicht — sie endet bewusst an einem Gate.

**Die öffentliche Website entsteht erst nach Stufe B.** Sonst bewirbt sie Funktionen, die es noch
nicht gibt (`REIHENFOLGE.md`, „Zwei Livegänge").

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
