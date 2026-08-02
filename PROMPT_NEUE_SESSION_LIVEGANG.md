# Startprompt für die Bau-Sessions A1 bis B

**Stand:** 02.08.2026 · A0 fertig auf `claude/php-a0-modellplan-06duus`, geprüft und bestätigt
**Zweck:** Diesen Block in eine neue Claude-Code-Session kopieren. Alles darunter ist der Prompt.

---

Du baust ein PHP-Projekt weiter, bis es live gehen kann. **Stufe A0 liegt fertig vor.** Vor dem
ersten Codeschritt kommen der Modellplan und die Startprüfung.

## 1. Zuerst: der Modellplan

**Bevor du irgendetwas anderes tust**, lege fest, welches Modell welche Aufgabe übernimmt.
Schreib den Plan als Tabelle in deine erste Antwort. **Ziel: Kosten sparen bei gleichem Ergebnis.**

Verfügbar sind `claude-opus-5`, `claude-sonnet-5` und `claude-haiku-4-5-20251001`. Umschalten mit
`/model`. Der Vorschlag unten ist ein Ausgangspunkt. Prüf ihn und ändere ihn, wo du es besser weißt:

| Modell | Wofür | Warum |
|---|---|---|
| **Opus 5** | Mandantentrennung · Kundenanmeldung per Link · Zahlungsstatus · Löschläufe und Fristen · jede Migration · jede Sicherheitsentscheidung · die Startprüfung aus Abschnitt 3 | Ein Fehler kostet hier eine Migration, ein Datenleck oder eine Frist |
| **Sonnet 5** | der Rumpf: Routen, Ansichten, Adminmasken, Mailvorlagen, die 62 Testfälle schreiben, die Seiten der öffentlichen Website | Menge statt Tiefe |
| **Haiku 4.5** | Mechanisches: Dateien anlegen, umbenennen, formatieren, `grep`-Läufe, Prüflisten abhaken | Kein Urteil nötig |

**Zwei Regeln dazu:**

1. **Nie in Haiku entscheiden.** Taucht beim mechanischen Arbeiten eine Frage auf: hochschalten.
2. **Sicherheitskritischer Code wird in Opus geschrieben und in Opus gegengelesen.** Betroffen ist
   alles, was einen dieser sechs Punkte berührt:
   - `organization_id`
   - Anmeldung, Sitzungen, Anmeldelinks
   - Zahlungsstatus
   - Löschfristen
   - Uploads
   - Migrationen

Sag mir am Ende jeder Etappe **in einer Zeile**, welches Modell du benutzt hast.

## 2. Wo du anfängst

**SARTU** ist eine Webdesign-Agentur für regionale Betriebe in Deutschland. Zielgruppe sind
Handwerk, Praxen, Kanzleien und Ladengeschäfte. Verkauft werden fertige Firmenwebsites zum
Festpreis plus eine Monatspauschale.

**Der Stand am 02.08.2026:**

| | |
|---|---|
| Anwendungscode | rund 6.800 Zeilen, davon 2.850 Tests |
| Tests | **101, grün, gegen echtes MariaDB** — kein SQLite |
| Tabellen | 6 von 20, dazu `schema_migrations` |
| Testfälle | 26 von 88 |
| Erreichbar | Ersteinrichtung, Adminanmeldung mit TOTP, Betreiberdaten, Rechtstexte, Testmail |
| Leer | `/portal/` hat null Routen, `/api/` gibt es nicht, `/` hat keine Route |

**Der Branch:** A0 liegt auf `claude/php-a0-modellplan-06duus`. Prüf als Erstes, ob dieser Stand
inzwischen auf `main` liegt. Bau auf dem Zweig auf, der die drei A0-Commits enthält.

**Lies in dieser Reihenfolge:**

| # | Datei | Wofür |
|---|---|---|
| 1 | `IMPLEMENTATION_SUMMARY.md` | was A0 gebaut hat und wie es geschnitten ist |
| 2 | `OFFENE_PRUEFUNGEN.md` | was gebaut, aber ungeprüft ist. Elf Punkte |
| 3 | `UEBERGABE_DATEILISTE.md` | **die Rangfolge.** Wer bei Widerspruch gewinnt — die einzige gültige |
| 4 | `REIHENFOLGE.md` | Etappen, Tabellen, **eine Zeile je Testfall** |
| 5 | `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` | Datenmodell, jeder Screen des Kundenbereichs, 88 Testfälle |
| 6 | `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` | die öffentliche Website, Seite für Seite |
| 7 | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` | alle Platzhalter und Sperren. Wo `offen` steht, wird nichts erfunden |
| 8 | `design/tokens.css` | jede Farbe, jeder Radius, jeder Abstand |
| 9 | `SARTU_TEXTREGELN.md` | wie jeder Text formuliert sein muss, mit Pflicht-Prüfbericht |

**`konzepte/` nicht vorsorglich lesen.** 360 KB historische Quellen mit veralteten Preisen und
abgelösten Stacks. Dort wird nur gezielt nachgeschlagen.

## 3. Startprüfung, bevor du Code schreibst

1. Läuft `vendor/bin/phpunit` bei dir grün durch? **Ohne grünen Ausgangsstand fängst du nicht an.**
2. Widersprechen sich zwei Dokumente an einer Stelle, die A1 betrifft?
3. Sind alle vier A1-Tabellen auf Feldebene definiert?
4. Steht in `SARTU_ENTSCHEIDUNGEN_OFFEN.md` etwas auf `offen`, das A1 braucht?

**Ergebnis als kurzer Bericht.** Findest du einen Widerspruch: melden. Entscheiden ist nicht deine
Aufgabe.

## 4. Das Ziel: zwei Livegänge, nicht einer

`REIHENFOLGE.md` legt zwei getrennte Zeitpunkte fest. Verwechsle sie nicht:

| Livegang | Wann | Bedingung |
|---|---|---|
| **Pilotkunde ist live** | nach A3 | Ein echtes Projekt erreicht den Zustand `live` |
| **Öffentliche Website geht live** | nach B | Die Seite darf **nur** Funktionen bewerben, die es gibt |

**Dein Auftrag reicht bis zum zweiten.** Das sind vier Etappen, 14 Tabellen und 62 Testfälle.

## 5. Die vier Etappen

Bau sie **in dieser Reihenfolge**. Jede Etappe ist für sich lauffähig, getestet und gepusht.

### A1 — Anfrage bis Auftrag · 4 Tabellen · 34 Testfälle

**Tabellen:** `leads` · `login_tokens` · `projects` · `offers`

- Bedarfsscheck unter `/briefing`
- Anfrageliste im Adminbereich
- Umwandlung in Kunde und Projekt
- Einladung des Kunden, Kundenanmeldung per Link
- Angebot erstellen und **senden**
- Löschlauf für IP-Adressen und Anfragefristen

> **Der Bedarfsscheck entsteht hier als schmaler senkrechter Ausschnitt.** Vom Formular über die
> Annahme und den `leads`-Eintrag bis zur Danke-Seite. **Ohne das umgebende Website-Design** — das
> kommt nach A3.
>
> **A1 endet beim gesendeten Angebot.** Die Annahme steht am Anfang von A2. Der nächste Schritt
> danach heißt `/rechnungen`, und diese Route entsteht erst dort.

**Fertig, wenn:** Eine über `/briefing` abgeschickte Anfrage führt bis zu einem gesendeten Angebot,
das der Kunde in seinem Bereich sieht. Der Löschlauf leert nachweislich eine 31 Tage alte
`source_ip`. Der Mandantentest kennt Projekte und Angebote.

### A2 — Auftrag bis Produktionsstart · 5 Tabellen · 21 Testfälle

**Tabellen:** `invoices` · `tasks` · `task_files` · `approvals` · `support_messages`

- Angebotsannahme durch den Kunden
- Rechnungen **von Hand** angelegt, Zahlungsstatus **von Hand** gesetzt
- Mollie-Link eingetragen
- Überfälligkeitslauf
- Aufgaben, Uploads, Faktenfreigabe

**Fertig, wenn:** Der Kunde nimmt das Angebot an, und der Weg führt über Anzahlung, Aufgaben und
Faktenfreigabe bis `produktion`. Eine Rechnung mit überschrittenem `due_date` steht am nächsten Tag
auf `ueberfaellig`. Der Mandantentest kennt Rechnungen, Aufgaben und Dateien.

### A3 — Produktion bis Livegang · 3 Tabellen · 6 Testfälle

**Tabellen:** `feedback_rounds` · `feedback_items` · `domain_status`

- Vorschau und Korrekturrunden
- Abnahme
- Domainstatus
- Livegang

> **`domain_status` wird vollständig angelegt und von Hand gepflegt.** Verschoben ist allein die
> Registrar-Anbindung. Eine Teiltabelle jetzt bedeutet eine Folgemigration später.

**Fertig, wenn:** Ein Projekt erreicht `live`. Der Mandantentest deckt alle Kundenrouten ab. **Ab
hier ist der erste Livegang möglich.**

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

## 6. Halt — B hat eine Lücke, die dir nicht gehört

Die Selbstpflege verspricht drei Dinge: *Bilder tauschen* · *Team- und Projekteinträge pflegen* ·
*Anfragen von der Website einsehen*.

**Für keine dieser drei Funktionen gibt es eine Tabelle im Datenmodell.** Es fehlen sinngemäß
`site_content`, `media_assets` und `website_inquiries`.

**Erfinde sie nicht.** Melde die Lücke, sobald du B erreichst. Es gibt genau zwei Auswege, und
beide gehören dem Betreiber:

1. Das Datenmodell im Portal-Lastenheft wird um diese Tabellen ergänzt
2. Die Funktionsliste im Website-Lastenheft §5 Sektion 2 wird gekürzt

**Bis dahin bewirbt keine Seite diese drei Funktionen.**

## 7. Fünf Befunde aus A0, die du zuerst wegräumst

Eine Prüfung am 02.08.2026 hat sie gefunden. Sie sind klein und stehen am besten vor A1:

| # | Was | Wo |
|---|---|---|
| 1 | Das TOTP-Geheimnis kommt aus dem Formular. Die Sitzung hält den richtigen Wert bereits unter `_setup_totp` | `admin/SetupSteuerung.php:200` |
| 2 | Der Schlüssel ist 103 Zeichen lang und wird abgetippt. RFC 4226 empfiehlt 160 Bit; alles darüber bringt bei HMAC-SHA1 nichts | `app/services/Zweifaktor.php:30` |
| 3 | `einrichtungsAdresse()` baut die `otpauth://`-Adresse und wird nirgends aufgerufen. Damit ist `ADMIN_TOTP_ISSUER` unbenutzt | `app/services/Zweifaktor.php:35` |
| 4 | `X-Powered-By: PHP/8.4.19` geht raus. `expose_php` ist nirgends gesetzt | `.docker/php/php.ini` |
| 5 | Vier Stellen in Lastenheft §1.5 nennen „Schritt 3", wo die Migrationen gemeint sind. Die sind seit der Neufassung Schritt 4. Dazu Zeile 299: „Sechs Schritte" über einer Tabelle mit acht | Zeilen 299 · 345 · 400 · 424 · 450 · 481 · 2025 |

Punkt 2 und 3 brauchen eine Rückfrage an den Betreiber. Ein QR-Code bräuchte eine Bibliothek.
`SARTU_ENTSCHEIDUNGEN_OFFEN.md` erlaubt keine erfundene Abhängigkeit. **Frag nach.**

## 8. Was gesperrt ist

Diese Punkte gehören dem Betreiber. Bau alles andere fertig und melde sie am Etappenende:

| Gesperrt | Was daran hängt |
|---|---|
| **Rechtstexte** (§2) | Entwürfe sind erlaubt, jeder trägt am Kopf `ENTWURF — NICHT GEPRÜFT, NICHT VERÖFFENTLICHEN`. Die Veröffentlichung bricht ab, solange keine anwaltliche Freigabe vorliegt |
| **Umsatzsteuerstatus** | die Preisdarstellung der **gesamten** Website |
| **Adressstatus** (§1) | Impressum · Google-Unternehmensprofil · `LocalBusiness` |
| **Gründername und Foto** (§5) | Startseite Sektion 8. Fehlt das Foto, **entfällt die Sektion vollständig** — kein leerer Rahmen an einer Vertrauensstelle |
| **Bildmaterial** | 15 Bildplätze. Ohne Bild steht dort ein beschrifteter Platz, nie ein leerer Rahmen |
| **Hosting** | der Livegang. Cron und Mailversand müssen praktisch geprüft sein |
| **Stellenseite** (§7b) | Solange offen, wird sie auf keiner Seite erwähnt, angekündigt oder verlinkt |

## 9. Die Regeln, die nicht verhandelbar sind

1. **Mandantentrennung:** Jede Abfrage filtert nach `organization_id` **aus der Sitzung**, nie aus
   der Anfrage. `tests/TenantIsolationTest.php` wird **niemals** gelöscht oder abgeschwächt, um
   grün zu werden. Er wächst mit jeder Etappe.
2. **Getrennte Zugriffsschichten.** Verboten ist der gemeinsame Codepfad, der den Filter bei
   Admins weglässt. Genau daraus entsteht die typische Datenpanne.
3. **Objektzugriff doppelt prüfen:** existiert es **und** gehört es zur Sitzungsorganisation? Sonst
   404. Ein 403 verrät die Existenz.
4. **Der Zahlungsstatus wird nie aus einer Rückkehr-URL abgeleitet.**
5. **Keine Secrets ins Repository.** Committet wird nur `.env.example`.
6. **Nur PDO mit vorbereiteten Anweisungen.** SQL steht ausschließlich in `/app/data`.
7. **CSRF-Token bei jedem POST.** Alle Kernabläufe laufen mit abgeschaltetem JavaScript.
8. **Jede Migration verändert genau ein Schemaobjekt.** MySQL rollt Schemaänderungen nicht zurück.
   Migrationen werden nie geändert, nur ergänzt.
9. **Keine harte Löschung fachlicher Daten.** `archived_at` statt `DELETE`.
10. **Nichts erfinden.** Keine Rechtstexte, keine Anschriften, keine Kundennamen, keine Referenzen,
    keine Zahl, die in den Unterlagen fehlt. Fehlt etwas: melden.

## 10. Design und Texte

**Das Designsystem ist entschieden und liegt fertig vor.** Du triffst keine Gestaltungsentscheidung.

- `design/tokens.css` wird als Erstes eingebunden, vor jedem Bauteil-CSS
- **Keine Zahl im Bauteil**, wo eine Variable existiert. `border-radius:30px` ist ein Abgabefehler
- Es gibt **eine** Akzentfarbe: Lime `#a3e635`. Kein Rot für Fehler, kein Grün für Erfolg
- **Lime ist Fläche, auf hellem Grund nie Schrift** — gemessen 1,32 : 1
- **Keine Sonderform** neben der Radienskala
- Kunden- und Adminbereich müssen visuell unterscheidbar bleiben
- Zum Ansehen: `design/corporate-design.html`

**Für jeden Text**, den ein Mensch liest — auch Fehlermeldungen und Knopfbeschriftungen:
**`/sartu-texter` aufrufen.** Zu jeder abgegebenen Seite gehört der Prüfbericht mit Zahlen.

Nach außen heißt es *Kundenbereich · Ihr Bereich · Anmeldung · Ihr Projekt*. Nach außen **nie**:
*App · Software · SaaS · Plattform · Tool · Dashboard · System · Instanz*.

**Der Kunde sieht nie einen Systemcode.** `qa_failed` ist ein Feldwert, kein Text für Menschen.

## 11. Die Slash-Befehle

| Befehl | Wofür | Wann |
|---|---|---|
| **`/sartu-texter`** | der Texter des Projekts | bei **jedem** Text für Menschen |
| **`/model`** | Modell wechseln | nach dem Plan aus Abschnitt 1, an jeder Etappengrenze |
| **`/security-review`** | Sicherheitsprüfung der Änderungen im Branch | **Pflicht** nach Kundenanmeldung, Uploads, Zahlungsstatus und jeder Etappe |
| **`/code-review`** | Fehlersuche im eigenen Arbeitsstand | am Ende jeder Teilfunktion |
| **`/simplify`** | Aufräumen: Wiederverwendung, Vereinfachung | nach `/code-review`, nie davor |
| **`/run`** | Anwendung starten und ansehen | nach jedem sichtbaren Schritt |
| `/artifact-design` | Gestaltungsgrundlagen für Artefakte | **nur** für eine Vorschau als Artefakt |

**Nicht nötig für diesen Auftrag:**

- `/init` — die `CLAUDE.md` liegt vor
- `/review` — das ist für fremde Pull Requests
- `/theme-factory` und `/canvas-design` — die Gestaltung ist entschieden
- `/brand-guidelines` — bringt Anthropics Marke ein, nicht SARTUs

## 12. Wie du arbeitest

1. **Modellplan** schreiben
2. **Startprüfung** durchführen und berichten
3. **Umgebung hochfahren** nach `ENTWICKLUNGSUMGEBUNG.md`. Der Weg ist entschieden: **Docker**
4. Die fünf Befunde aus Abschnitt 7 wegräumen
5. **Bauen**, in kleinen Schritten, jeder Schritt lauffähig
6. Nach jeder Teilfunktion: `/code-review`, dann `/simplify`
7. Nach jeder sicherheitsrelevanten Teilfunktion: **`/security-review`**
8. **Committen und pushen**, sobald etwas durchgängig funktioniert

**An jeder Etappengrenze hältst du an** und lieferst vier Dinge:

- die Testzahl, selbst ausgeführt
- die „Fertig, wenn"-Punkte der Etappe, einzeln belegt
- neue Zeilen in `OFFENE_PRUEFUNGEN.md` für alles Gebaute, das ungeprüft blieb
- eine Zeile zum Modellwechsel

**Halte an und frag, wenn:**

- eine Vorgabe einer anderen widerspricht
- eine Zahl fehlt
- in `SARTU_ENTSCHEIDUNGEN_OFFEN.md` `offen` steht
- du eine Tabelle oder ein Feld brauchst, das im Datenmodell fehlt

**Rate nie.** Das Projekt hat 88 Testfälle und eine Rangfolge, damit niemand raten muss.

**Was nicht ausgeführt wurde, wird nicht als geprüft gemeldet.**
