# Startprompt für die Bau-Session A0

**Stand:** 01.08.2026 · Repo-Stand `3e64a71` auf `main`
**Zweck:** Diesen Block in eine neue Claude-Code-Session kopieren. Alles darunter ist der Prompt.

---

Du baust **Stufe A0** eines PHP-Projekts. Vorher: Modellplan, Startprüfung, dann erst Code.

## 1. Zuerst: der Modellplan

**Bevor du irgendetwas anderes tust**, lege fest, welches Modell welche Aufgabe übernimmt.
Schreib den Plan als Tabelle in deine erste Antwort. **Ziel: Kosten sparen bei gleichem Ergebnis.**

Verfügbar sind `claude-opus-5`, `claude-sonnet-5` und `claude-haiku-4-5-20251001`. Umschalten mit
`/model`. Der Vorschlag unten ist ein Ausgangspunkt. Prüf ihn und ändere ihn, wo du es besser weißt:

| Modell | Wofür | Warum |
|---|---|---|
| **Opus 5** | Mandantentrennung · Anmeldung und TOTP · Ersteinrichtung · Schema und Migrationen · jede Sicherheitsentscheidung · die Startprüfung aus Abschnitt 3 | Ein Fehler kostet hier eine Migration oder ein Datenleck |
| **Sonnet 5** | der Rumpf: Routing, Vorlagen, Adminmasken, Mailversand, die 26 Testfälle schreiben | Menge statt Tiefe |
| **Haiku 4.5** | Mechanisches: Dateien anlegen, umbenennen, formatieren, `grep`-Läufe, Abhaken von Prüflisten | Kein Urteil nötig |

**Zwei Regeln dazu:**

1. **Nie in Haiku entscheiden.** Taucht beim mechanischen Arbeiten eine Frage auf: hochschalten.
2. **Sicherheitskritischer Code wird in Opus geschrieben und in Opus gegengelesen.** Betroffen ist
   alles, was einen dieser fünf Punkte berührt:
   - `organization_id`
   - Passwörter
   - TOTP
   - Sitzungen
   - die Installationssperre

Sag mir am Ende jeder Etappe **in einer Zeile**, welches Modell du benutzt hast und ob du wechseln
willst.

## 2. Was das ist und wo es liegt

**SARTU** ist eine Webdesign-Agentur für regionale Betriebe in Deutschland. Zielgruppe sind
Handwerk, Praxen, Kanzleien und Ladengeschäfte. Verkauft werden fertige Firmenwebsites zum
Festpreis plus eine Monatspauschale. Du baust den **Kundenbereich** dazu: ein PHP-Projekt, serverseitig gerendert,
MySQL/MariaDB, kein Framework, kein Node, kein Build-Schritt, keine externen Dienste.

**Es existieren rund 12.000 Zeilen Spezifikation und 0 Zeilen Anwendungscode.** Du schreibst die
ersten.

**Lies in dieser Reihenfolge:**

| # | Datei | Wofür |
|---|---|---|
| 1 | `BAUFREIGABE.md` | **zuerst.** Was freigegeben ist, was blockiert, und warum |
| 2 | `UEBERGABE_DATEILISTE.md` | **die Rangfolge.** Wer bei Widerspruch gewinnt — die einzige gültige |
| 3 | `REIHENFOLGE.md` | Etappen A0 bis C, Tabellen und Testfälle je Etappe |
| 4 | `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` | **das Hauptdokument.** Stack, Datenmodell, jeder Screen, 88 Testfälle |
| 5 | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` | alle Platzhalter und Sperren. Wo `offen` steht, wird nichts erfunden |
| 6 | `ENTWICKLUNGSUMGEBUNG.md` | wie PHP, Composer und die Datenbank aufgerufen werden |
| 7 | `design/tokens.css` | jede Farbe, jeder Radius, jeder Abstand |
| 8 | `SARTU_TEXTREGELN.md` | wie jeder Text formuliert sein muss, mit Pflicht-Prüfbericht |

**`konzepte/` nicht vorsorglich lesen.** 360 KB historische Quellen mit veralteten Preisen und
abgelösten Stacks. Dort wird nur gezielt nachgeschlagen.

## 3. Startprüfung, bevor du Code schreibst

Der Bauauftrag verlangt bei jedem Vorgabenwiderspruch **melden und anhalten**. Prüfe deshalb zuerst:

1. Widersprechen sich zwei Dokumente an einer Stelle, die A0 betrifft?
2. Stimmen die Namen in `.env.example` mit Portal-Lastenheft §1.5 überein?
3. Ist die Reihenfolge der acht Setup-Schritte in §1.5 ausführbar — entsteht jeder Wert, bevor er
   gebraucht wird?
4. Sind alle sechs A0-Tabellen auf Feldebene definiert?

**Ergebnis als kurzer Bericht.** Findest du einen Widerspruch: melden. Entscheiden ist nicht deine
Aufgabe.

> Am 01.08.2026 fand eine externe Prüfung fünf solcher Widersprüche, die A0 blockierten. Alle sind
> behoben. Die Liste steht oben in `BAUFREIGABE.md` — lies sie, dann weißt du, wonach du suchst.

## 4. Was A0 ist

**Sechs Tabellen:** `organizations` · `users` · `sessions` · `audit_events` · `operator_settings` ·
`legal_texts`. Dazu `schema_migrations`, das zählt nicht mit.

**Funktionen:**

- geführte Ersteinrichtung in acht Schritten
- Migrationen, einzeln ausgeführt und protokolliert
- Adminanmeldung mit Passwort und TOTP
- Betreiberdaten
- Rechtstexte mit Freigabezustand
- Testmailversand
- **Mandantentrennung im Datenzugriff**
- Prüfprotokoll

**26 Testfälle**, die Zuordnung steht in `REIHENFOLGE.md`.

**A0 ist fertig, wenn alle vier Punkte stimmen:**

1. Die Installation läuft auf einer leeren Datenbank durch
2. Ein Admin meldet sich mit Passwort und TOTP an
3. Eine Testmail kommt nachweislich an
4. `TenantIsolationTest` läuft im Umfang von A0 grün

**Nicht bauen:** A1 bis C. Kein Zahlungsdienst, keine Domainautomatik, kein Mahnwesen, kein
Dunkelmodus, keine Anfragen aus Kundenwebsites. Die vollständige Liste steht in
`CODEX_AUFTRAG_PORTAL.md` §5.

## 5. Sechs Regeln, die nicht verhandelbar sind

1. **Mandantentrennung:** Jede Abfrage filtert nach `organization_id` **aus der Sitzung**, nie aus
   der Anfrage. `tests/TenantIsolationTest.php` wird **niemals** gelöscht oder abgeschwächt, um
   grün zu werden.
2. **Keine Secrets ins Repository.** Kein Passwort, kein Token, kein Datenbankzugang. Committet
   wird nur `.env.example`.
3. **Nur PDO mit vorbereiteten Anweisungen.** Keine zusammengesetzten SQL-Zeichenketten.
4. **Der Zahlungsstatus wird nie aus einer Rückkehr-URL abgeleitet.** Gilt schon jetzt, obwohl
   Rechnungen erst in A2 kommen.
5. **Nichts erfinden.** Keine Rechtstexte, keine Anschriften, keine Kundennamen, keine Referenzen.
   Keine Zahl, die in den Unterlagen fehlt. Fehlt etwas: melden.
6. **Nur `/public` ist über das Netz erreichbar.** Alles andere liegt darüber.

## 6. Design und Texte

**Das Designsystem ist entschieden und liegt fertig vor.** Du triffst keine Gestaltungsentscheidung.

- `design/tokens.css` wird als Erstes eingebunden, vor jedem Bauteil-CSS
- **Keine Zahl im Bauteil**, wo eine Variable existiert. `border-radius:30px` ist ein Abgabefehler
- Es gibt **eine** Akzentfarbe: Lime `#a3e635`. Kein Rot für Fehler, kein Grün für Erfolg
- **Lime ist Fläche, auf hellem Grund nie Schrift** — gemessen 1,32 : 1
- **Keine Sonderform** neben der Radienskala. Am 01.08.2026 wurde eine gestrichen
- Begründungen: `SARTU_DESIGNSYSTEM.md` und `SARTU_CORPORATE_DESIGN.md`. Zum Ansehen:
  `design/corporate-design.html`

**Für jeden Text**, den ein Mensch liest — auch Fehlermeldungen und Knopfbeschriftungen:
**`/sartu-texter` aufrufen.** Der Skill liegt im Projekt unter `.claude/skills/sartu-texter/`.
Zu jeder abgegebenen Seite gehört der Prüfbericht mit Zahlen aus `SARTU_TEXTREGELN.md`.

## 7. Die Slash-Befehle, die du brauchst

| Befehl | Wofür | Wann |
|---|---|---|
| **`/sartu-texter`** | der Texter des Projekts | bei **jedem** Text für Menschen |
| **`/model`** | Modell wechseln | nach dem Plan aus Abschnitt 1, an jeder Etappengrenze |
| **`/security-review`** | Sicherheitsprüfung der Änderungen im Branch | **Pflicht** nach Anmeldung, Ersteinrichtung und Mandantentrennung |
| **`/code-review`** | Fehlersuche im eigenen Arbeitsstand | am Ende jeder Etappe |
| **`/simplify`** | Aufräumen: Wiederverwendung, Vereinfachung | nach `/code-review`, nicht davor |
| **`/run`** | Anwendung starten und im Browser ansehen | nach jedem sichtbaren Schritt |
| **`/init`** | `CLAUDE.md` für das Projekt anlegen | **einmal, ganz am Anfang** |
| `/artifact-design` | Gestaltungsgrundlagen für Artefakte | **nur**, wenn du eine Vorschau als Artefakt veröffentlichst — siehe Hinweis |
| `/session-start-hook` | Startskript, damit Tests und Linter in Websessions laufen | einmal, wenn die Umgebung steht |
| `/loop` | wiederkehrende Aufgabe in festem Takt | nur auf Ansage |

> **Zu `/artifact-design` — ehrlich eingeordnet.** Der Skill ist für **Artefakte** gedacht, also
> für veröffentlichte HTML-Seiten. Seine erste Regel lautet: *ein vorhandenes Designsystem hat
> Vorrang und wird angewendet.* **Für SARTU ist genau das der Fall.**
>
> Ruf ihn deshalb nur in einem Fall auf: wenn du dem Betreiber eine Bildschirmansicht als Artefakt
> zeigen willst. Die Gestaltung selbst kommt aus `design/tokens.css`.

**Nicht nötig für A0:**

- `/review` — das ist für fremde Pull Requests
- `/theme-factory` und `/canvas-design` — Gestaltung ist entschieden
- `/brand-guidelines` — bringt Anthropics Marke ein, nicht SARTUs

## 8. Wie du arbeitest

1. **Modellplan** schreiben (Abschnitt 1)
2. **Startprüfung** durchführen und berichten (Abschnitt 3)
3. **Umgebung hochfahren** nach `ENTWICKLUNGSUMGEBUNG.md`. Der Weg ist entschieden: **Docker**
4. `/init` einmal ausführen
5. **Bauen**, in kleinen Schritten, jeder Schritt lauffähig
6. Nach jeder Teilfunktion: `/code-review`, dann `/simplify`
7. Nach Anmeldung, Ersteinrichtung und Mandantentrennung: **`/security-review`**
8. **Committen und pushen**, sobald etwas durchgängig funktioniert

**Halte an und frag, wenn:**

- eine Vorgabe einer anderen widerspricht
- eine Zahl fehlt
- in `SARTU_ENTSCHEIDUNGEN_OFFEN.md` `offen` steht
- du eine Tabelle oder ein Feld brauchst, das im Datenmodell fehlt

**Rate nie.** Das Projekt hat 88 Testfälle und eine Rangfolge, damit niemand raten muss.
