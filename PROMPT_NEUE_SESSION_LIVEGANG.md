# Startprompt für die Bau-Sessions A2 bis B

**Stand:** 02.08.2026 · A0 und A1 fertig, geprüft und bestätigt
**Zweck:** Diesen Block in eine neue Claude-Code-Session kopieren. Alles darunter ist der Prompt.

---

Du baust ein PHP-Projekt fertig, bis es live gehen kann. **A0 und A1 liegen fertig vor.**
Du arbeitest **durch, ohne Rückfrage und ohne Etappenpause**. Wie das geht, ohne zu raten, steht
in Abschnitt 6.

## 1. Zuerst: der Modellplan

**Bevor du irgendetwas anderes tust**, lege fest, welches Modell welche Aufgabe übernimmt.
Schreib den Plan als Tabelle in deine erste Antwort. **Ziel: Kosten sparen bei gleichem Ergebnis.**

Verfügbar sind `claude-opus-5`, `claude-sonnet-5` und `claude-haiku-4-5-20251001`. Umschalten mit
`/model`. Der Vorschlag unten ist ein Ausgangspunkt. Prüf ihn und ändere ihn, wo du es besser weißt:

| Modell | Wofür | Warum |
|---|---|---|
| **Opus 5** | Mandantentrennung · Zahlungsstatus · Uploads · Löschläufe und Fristen · jede Migration · jede Sicherheitsentscheidung | Ein Fehler kostet hier eine Migration, ein Datenleck oder eine Frist |
| **Sonnet 5** | der Rumpf: Routen, Ansichten, Adminmasken, Mailvorlagen, die 28 Testfälle, die Seiten der öffentlichen Website | Menge statt Tiefe |
| **Haiku 4.5** | Mechanisches: Dateien anlegen, umbenennen, formatieren, `grep`-Läufe, Prüflisten abhaken | Kein Urteil nötig |

**Zwei Regeln dazu:**

1. **Nie in Haiku entscheiden.** Taucht beim mechanischen Arbeiten eine Frage auf: hochschalten.
2. **Sicherheitskritischer Code wird in Opus geschrieben und in Opus gegengelesen.** Betroffen ist
   alles, was einen dieser sechs Punkte berührt:
   - `organization_id`
   - Sitzungen und Anmeldelinks
   - Zahlungsstatus
   - Löschfristen
   - Uploads
   - Migrationen

## 2. Wo du anfängst

**SARTU** ist eine Webdesign-Agentur für regionale Betriebe in Deutschland. Zielgruppe sind
Handwerk, Praxen, Kanzleien und Ladengeschäfte. Verkauft werden fertige Firmenwebsites zum
Festpreis plus eine Monatspauschale.

**Der Stand am 02.08.2026:**

| | |
|---|---|
| Tests | **183, grün, gegen echtes MariaDB** — extern nachgerechnet |
| Tabellen | 10 von 20, dazu `schema_migrations` |
| Testfälle | 60 von 88 |
| Kundenbereich | Anmeldung per Link, Übersicht, Angebot ansehen |
| Adminbereich | Ersteinrichtung, Anmeldung mit TOTP, Anfragen, Umwandlung, Angebote, Rechtstexte |
| Öffentlich | Bedarfsscheck unter `/briefing`, drei Rechtstextrouten. **Keine Startseite** |

**Lies in dieser Reihenfolge:**

| # | Datei | Wofür |
|---|---|---|
| 1 | `OFFENE_PRUEFUNGEN.md` | was gebaut, aber ungeprüft ist |
| 2 | `UEBERGABE_DATEILISTE.md` | **die Rangfolge.** Wer bei Widerspruch gewinnt — die einzige gültige |
| 3 | `REIHENFOLGE.md` | Etappen, Tabellen, **eine Zeile je Testfall** |
| 4 | `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` | Datenmodell, jeder Screen des Kundenbereichs, 88 Testfälle |
| 5 | `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` | die öffentliche Website, Seite für Seite |
| 6 | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` | die Platzhalter — **lies §5a, §6 und §7b, die sind entschieden** |
| 7 | `design/tokens.css` | jede Farbe, jeder Radius, jeder Abstand |
| 8 | `SARTU_TEXTREGELN.md` | wie jeder Text formuliert sein muss, mit Pflicht-Prüfbericht |

**`konzepte/` nicht vorsorglich lesen.** 360 KB historische Quellen mit veralteten Preisen und
abgelösten Stacks. Dort wird nur gezielt nachgeschlagen.

## 3. Startprüfung, einmal, dann läufst du

1. Läuft `vendor/bin/phpunit` grün durch? **Ohne grünen Ausgangsstand fängst du nicht an.**
2. Sind die fünf A2-Tabellen auf Feldebene definiert?

Schreib das Ergebnis in zwei Zeilen. Ist der Ausgangsstand rot, reparier ihn zuerst — auch das
ohne Rückfrage.

## 4. Das Ziel: zwei Livegänge, nicht einer

| Livegang | Wann | Bedingung |
|---|---|---|
| **Pilotkunde ist live** | nach A3 | Ein echtes Projekt erreicht den Zustand `live` |
| **Öffentliche Website geht live** | nach B | Die Seite darf **nur** Funktionen bewerben, die es gibt |

**Dein Auftrag reicht bis zum zweiten.** Drei Etappen, 10 Tabellen, 28 Testfälle.

## 5. Die drei Etappen

Bau sie **in dieser Reihenfolge**. Jede ist für sich lauffähig, getestet, committet und gepusht.

### A2 — Auftrag bis Produktionsstart · 5 Tabellen · 21 Testfälle

**Tabellen:** `invoices` · `tasks` · `task_files` · `approvals` · `support_messages`

- Angebotsannahme durch den Kunden
- Rechnungen **von Hand** angelegt, Zahlungsstatus **von Hand** gesetzt
- Mollie-Link eingetragen
- Überfälligkeitslauf, zwei Zahlungserinnerungen
- Aufgaben, Uploads, Faktenfreigabe
- Nachrichten an den Betreuer

**Fertig, wenn:** Der Kunde nimmt das Angebot an, und der Weg führt über Anzahlung, Aufgaben und
Faktenfreigabe bis `produktion`. Eine Rechnung mit überschrittenem `due_date` steht am nächsten Tag
auf `ueberfaellig`. Der Mandantentest kennt Rechnungen, Aufgaben und Dateien.

### A3 — Produktion bis Livegang · 3 Tabellen · 6 Testfälle

**Tabellen:** `feedback_rounds` · `feedback_items` · `domain_status`

- Vorschau und Korrekturrunden
- Abnahme
- Domainstatus, von Hand gepflegt
- Livegang

> **`domain_status` wird vollständig angelegt.** Verschoben ist allein die Registrar-Anbindung.
> Eine Teiltabelle jetzt bedeutet eine Folgemigration später.

**Fertig, wenn:** Ein Projekt erreicht `live`. Der Mandantentest deckt alle Kundenrouten ab.

### B — Selbstpflege und öffentliche Website · 2 Tabellen · 1 Testfall

**Tabellen:** `business_hours` · `business_hours_exceptions`

Öffnungszeiten und Ausnahmen selbst pflegen. Dazu die öffentliche Website nach
`CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md`:

- Startseite mit zehn Sektionen
- `/leistungen` · `/preise` · `/ablauf`
- fünf Leistungsseiten
- Branchenseiten
- `/ueber-uns` und `/kontakt`
- Transparenzseiten
- Ratgeber und Lexikon
- Pflicht- und Systemseiten

**Fertig, wenn:** Die Definition of Done aus Website-Lastenheft §17 ist abgehakt.

## 6. Du fragst nicht — die Antworten stehen hier

**Jeder offene Punkt hat eine geschriebene Regel.** Ihr zu folgen ist kein Raten. Halte dich an
die rechte Spalte und arbeite weiter:

| Offener Punkt | Was du tust |
|---|---|
| **Rechtstexte** — Impressum, Datenschutz, AGB, AVV, TOM | Entwurf schreiben. Kopfzeile `ENTWURF — NICHT GEPRÜFT, NICHT VERÖFFENTLICHEN`. `legal_texts.status` bleibt `entwurf`. Die Startsperre §14a blockiert die Veröffentlichung von allein |
| **Umsatzsteuer, § 19 UStG** | Kommt zur Laufzeit aus `operator_settings.kleinunternehmer`. Steht das auf `ja`, erscheint nirgends „zzgl. USt.". **Keine Bauentscheidung** |
| **Anschrift, Telefon, E-Mail** | Ebenfalls aus `operator_settings`. **Keine Bauentscheidung** |
| **Gründername** | §5.1: Der Name erscheint **nur** im Impressum. Nirgends sonst, auch nicht in Bildbeschreibungen |
| **Foto des Gründers** | Fehlt es, **entfällt Startseiten-Sektion 8 vollständig**. Kein leerer Rahmen an einer Vertrauensstelle |
| **Bildmaterial, 15 Plätze** | Beschrifteter Bildplatz: 2 px gestrichelt, Monospace-Zeile, ein Satz dazu, was dort später steht |
| **Selbstpflege: Bilder, Team, Anfragen** | **Entschieden am 01.08.2026** (§5a): Die drei Zeilen entfallen. Sektion 2 behält **elf** Punkte. Keine neuen Tabellen |
| **Stellen- und Karriereseite** | §7b: kommt auf keiner Seite vor. Nicht erwähnt, nicht angekündigt, nicht verlinkt |
| **Angebotsgültigkeit** | **30 Kalendertage ab Versand** (§4). Im Adminbereich änderbar |
| **`ADMIN_NOTIFY_EMAIL`** | Bau ein Feld in `operator_settings`, gepflegt unter `/admin/einstellungen/betrieb`. Ist es leer, unterbleibt **nur diese eine** Benachrichtigung, und `/admin` führt die Zeile in der Liste „fehlt noch". Kein erfundener Vorgabewert |
| **Hoster** | Betrifft den Livegang, nicht den Bau. Bau alles so, als liefe es auf dem Zielserver |

### Wenn dir trotzdem etwas fehlt

**In dieser Reihenfolge, ohne anzuhalten:**

1. **Widersprechen sich zwei Stellen?** Löse es über `UEBERGABE_DATEILISTE.md`. Bei zwei Stellen
   im selben Dokument gewinnt die mit der Begründung. Schreib die Auflösung in den Commit
2. **Fehlt eine Zahl ganz?** Schreib sie **nicht**. Bau alles darum herum, trag den Punkt in
   `OFFENE_ENTSCHEIDUNGEN.md` ein und mach weiter
3. **Fehlt eine Tabelle oder ein Feld?** Prüf, ob eine andere Stelle sie benennt. Findest du sie,
   bau sie als eigene Migration. Findest du sie nicht, notier den Punkt und bau den Rest

**Erfinde nie:**

- einen Rechtstext
- eine Anschrift
- einen Kundennamen oder eine Referenz
- einen Preis oder eine Frist

### Ohne Rückfrage heißt nicht ohne Sorgfalt

Diese drei Grenzen bleiben, auch wenn dich niemand aufhält:

- **Der Mandantentest wird nie abgeschwächt**, um grün zu werden
- **Kein Rechtstext geht in den Zustand `freigegeben`**, solange kein Mensch ihn geprüft hat
- **Kein Secret ins Repository**

## 7. Die Regeln, die nicht verhandelbar sind

1. **Mandantentrennung:** Jede Abfrage filtert nach `organization_id` **aus der Sitzung**, nie aus
   der Anfrage. `tests/TenantIsolationTest.php` wächst mit jeder Etappe.
2. **Getrennte Zugriffsschichten.** Verboten ist der gemeinsame Codepfad, der den Filter bei
   Admins weglässt. Genau daraus entsteht die typische Datenpanne.
3. **Objektzugriff doppelt prüfen:** existiert es **und** gehört es zur Sitzungsorganisation? Sonst
   404. Ein 403 verrät die Existenz.
4. **Der Zahlungsstatus wird nie aus einer Rückkehr-URL abgeleitet.** Er wird von Hand gesetzt.
5. **Uploads liegen außerhalb des Webroots.** Ausgeliefert wird nur über eine geprüfte Route.
6. **Nur PDO mit vorbereiteten Anweisungen.** SQL steht ausschließlich in `/app/data`.
7. **CSRF-Token bei jedem POST.** Alle Kernabläufe laufen mit abgeschaltetem JavaScript.
8. **Jede Migration verändert genau ein Schemaobjekt**, nie mehrere Tabellen in einer Datei.
   Migrationen werden nie geändert, nur ergänzt.
9. **Keine harte Löschung fachlicher Daten.** `archived_at` statt `DELETE`.
10. **Geld immer in Cent als integer.** Nie Fließkomma.

## 8. Design und Texte

**Das Designsystem ist entschieden und liegt fertig vor.** Du triffst keine Gestaltungsentscheidung.

- `design/tokens.css` wird als Erstes eingebunden, vor jedem Bauteil-CSS
- **Keine Zahl im Bauteil**, wo eine Variable existiert. `border-radius:30px` ist ein Abgabefehler
- Es gibt **eine** Akzentfarbe: Lime `#a3e635`. Kein Rot für Fehler, kein Grün für Erfolg
- **Lime ist Fläche, auf hellem Grund nie Schrift** — gemessen 1,32 : 1
- **Keine Sonderform** neben der Radienskala
- Kunden- und Adminbereich bleiben visuell unterscheidbar
- Zum Ansehen: `design/corporate-design.html`

**Für jeden Text**, den ein Mensch liest — auch Fehlermeldungen und Knopfbeschriftungen:
**`/sartu-texter` aufrufen.** Zu jeder abgegebenen Seite gehört der Prüfbericht mit Zahlen.

Nach außen heißt es *Kundenbereich · Ihr Bereich · Anmeldung · Ihr Projekt*. Nach außen **nie**:
*Portal · App · Software · SaaS · Plattform · Tool · Dashboard · System · Instanz*.

**Der Kunde sieht nie einen Systemcode.** `qa_failed` ist ein Feldwert, kein Text für Menschen.

## 9. Die Slash-Befehle

| Befehl | Wofür | Wann |
|---|---|---|
| **`/sartu-texter`** | der Texter des Projekts | bei **jedem** Text für Menschen |
| **`/model`** | Modell wechseln | nach dem Plan aus Abschnitt 1 |
| **`/security-review`** | Sicherheitsprüfung der Änderungen im Branch | **Pflicht** nach Uploads, Zahlungsstatus und jeder Etappe |
| **`/code-review`** | Fehlersuche im eigenen Arbeitsstand | am Ende jeder Teilfunktion |
| **`/simplify`** | Aufräumen: Wiederverwendung, Vereinfachung | nach `/code-review`, nie davor |
| **`/run`** | Anwendung starten und ansehen | nach jedem sichtbaren Schritt |

**Nicht nötig für diesen Auftrag:**

- `/init` — die `CLAUDE.md` liegt vor
- `/review` — das ist für fremde Pull Requests
- `/theme-factory` und `/canvas-design` — die Gestaltung ist entschieden
- `/brand-guidelines` — bringt Anthropics Marke ein, nicht SARTUs

## 10. Wie du arbeitest

1. **Modellplan** schreiben
2. **Startprüfung**, zwei Zeilen
3. **Umgebung hochfahren** nach `ENTWICKLUNGSUMGEBUNG.md`. Der Weg ist entschieden: **Docker**
4. **A2 bauen, A3 bauen, B bauen** — durchgehend
5. Nach jeder Teilfunktion: `/code-review`, dann `/simplify`
6. Nach jeder sicherheitsrelevanten Teilfunktion: **`/security-review`**
7. **Committen und pushen**, sobald etwas durchgängig funktioniert

**Am Ende jeder Etappe** committest und pushst du. In den Commit gehören vier Dinge. Warte auf
keine Antwort:

- die Testzahl, selbst ausgeführt
- die „Fertig, wenn"-Punkte, einzeln belegt
- neue Zeilen in `OFFENE_PRUEFUNGEN.md` für alles Gebaute, das ungeprüft blieb
- neue Zeilen in `OFFENE_ENTSCHEIDUNGEN.md` für alles, was du gemeldet statt erfunden hast

**Halte `STAND.md` aktuell:** eine Seite, die sagt, welche Etappe fertig ist, welche läuft und was
als Nächstes ansteht. Bricht der Lauf ab, setzt die nächste Sitzung dort auf.

**Am Ende von B** lieferst du einen Bericht über alle drei Etappen. Erst dort hältst du an.

## 11. Was du am Ende nicht kannst — und das ist richtig so

Zwei Schritte bleiben beim Betreiber. Bau alles davor fertig und schreib beide in `STAND.md`:

| Was | Warum es nicht deine Aufgabe ist |
|---|---|
| **Rechtstexte freigeben** | Ein plausibel klingender Rechtstext ist gefährlicher als gar keiner. Das prüft ein Anwalt |
| **Hoster einrichten** | Cron und Mailversand müssen auf echter Hardware laufen. SPF, DKIM und DMARC gehören dazu |

**Rate nie.** Das Projekt hat 88 Testfälle und eine Rangfolge, damit niemand raten muss.

**Was nicht ausgeführt wurde, wird nicht als geprüft gemeldet.**
