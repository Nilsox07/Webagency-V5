# Startprompt für die Abschluss-Session vor dem Livegang

**Stand:** 02.08.2026 · A0 bis B fertig, extern nachgeprüft
**Zweck:** Diesen Block in eine neue Claude-Code-Session kopieren. Alles darunter ist der Prompt.

---

Du schließt ein PHP-Projekt ab. **Alle fünf Bauetappen sind fertig**, 254 Tests laufen grün.
Was bleibt, ist eine Korrektur, vier entschiedene Punkte, die Messungen und der Leitfaden für den
Livegang. **Du arbeitest durch, ohne Rückfrage.**

## 1. Modellplan

Schreib in deine erste Antwort eine Tabelle, welches Modell welche Aufgabe übernimmt.
Verfügbar sind `claude-opus-5`, `claude-sonnet-5` und `claude-haiku-4-5-20251001`.

| Modell | Wofür |
|---|---|
| **Opus 5** | Abschnitt 3 — die Korrektur am Kontaktformular · das Zusammenführen der Zweige · jede Änderung an Migrationen |
| **Sonnet 5** | Abschnitt 5 und 6 — Messungen, `KEYWORD_VALIDATION.md`, `LIVEGANG.md`, Texte |
| **Haiku 4.5** | Prüflisten abhaken, `grep`-Läufe, Formatierung |

## 2. Wo du anfängst

**SARTU** ist eine Webdesign-Agentur für regionale Betriebe. Der Stand:

| | |
|---|---|
| Etappen | A0 · A1 · A2 · A3 · B — **alle fertig** |
| Tests | **254 grün**, gegen echtes MariaDB. Extern nachgerechnet |
| Tabellen | **20 von 20** · 25 Migrationen, lückenlos |
| Testfälle | **88 von 88** |
| Öffentliche Website | rund 30 Adressen, Ratgeber, Lexikon, drei Branchenseiten |
| Kundenbereich | Anmeldung per Link bis Abnahme und Onlinegang |

**Lies zuerst:** `STAND.md` · `OFFENE_ENTSCHEIDUNGEN.md` · `OFFENE_PRUEFUNGEN.md`.
Danach gezielt, was du brauchst. **`konzepte/` bleibt zu.**

### Als Erstes: die Zweige zusammenführen

Zwei Zweige tragen Änderungen, und keiner liegt auf `main`:

| Zweig | Was drauf liegt |
|---|---|
| `claude/php-a0-modellplan-06duus` | der gesamte Anwendungscode, A0 bis B |
| `claude/sartu-concept-review-pdhb5t` | die Angebotsgültigkeit in §4 · der §5a-Nachtrag in `REIHENFOLGE.md` · zwei Startprompts |

Beide betreffen `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md`, aber an verschiedenen Stellen.
**Führe beide nach `main` zusammen**, lass die Tests danach einmal laufen und arbeite von dort aus
weiter. Gibt es einen Konflikt, löse ihn so, dass **beide** Änderungen erhalten bleiben.

## 3. Die eine Korrektur: das Kontaktformular schreibt keinen Datensatz

**Portal-Lastenheft §4b.6 ist eindeutig:**

> *„Das allgemeine Kontaktformular ist **nicht** der Bedarfsscheck. Es versendet ausschließlich eine
> E-Mail an SARTU und erzeugt **keinen** Datensatz. Honigtopf, Zeitregel und Rate-Limit gelten dort
> gleichermaßen."*

Die vorige Sitzung hat diesen Abschnitt nicht gefunden. Sie suchte nach einer Tabelle für das
Formular, fand keine und legte die Rückfrage in `leads` ab. Die Antwort war: Es soll **gar keine**
geben.

**Was du änderst:**

1. `Kontaktanfrage` schreibt **nicht mehr** in `leads`. Sie versendet eine Mail an
   `ADMIN_NOTIFY_EMAIL`, sonst nichts
2. Honigtopf, Zeitregel und Rate-Limit bleiben — §4b.6 verlangt sie ausdrücklich
3. Die **B2B-Bestätigung fällt aus dem Formular**. Sie stand dort nur, weil
   `chk_leads_bestaetigungen` beide Häkchen verlangt. Ohne `leads`-Eintrag gibt es die Bedingung
   nicht. Website-Lastenheft §11 zählt sieben Felder mit **einer** Bestätigung auf
4. Der Datenschutzentwurf beschreibt derzeit eine Speicherung, die es nicht mehr gibt — zieh ihn
   nach
5. **Ein Test dafür:** Eine abgeschickte Rückfrage erzeugt **null** Zeilen in `leads` und genau
   eine Mail

**Keine Migration.** `leads` bleibt, wie sie ist — sie gehört dem Bedarfsscheck.

> **Warum das zuerst kommt:** Es betrifft personenbezogene Daten, die ab dem ersten Besucher
> anfallen. Löschfrist, Rechtsgrundlage und Verarbeitungsverzeichnis von `leads` sind für den
> Bedarfsscheck geschrieben, nicht für Rückfragen.

## 4. Vier offene Punkte — hier entschieden, nicht mehr zu fragen

`OFFENE_ENTSCHEIDUNGEN.md` führt acht offene Punkte. Vier davon sind jetzt entschieden:

| # | Punkt | Entscheidung |
|---|---|---|
| **2** | Schwelle für „knappe Frist" (§8.1) | **Drei Tage.** Dieselbe Zahl wie beim Angebotsablauf in §10 — eine zweite Frist daneben wäre eine Zahl ohne Grund. Der Hinweis erscheint, sobald `due_date` in drei Tagen oder weniger erreicht ist |
| **7** | Fokusfalle im mobilen Menü (Website §3) | **Bauen.** Eine eigene Datei unter `/public/assets/js/`, unter 2 KB, ohne Inline-Code, mit `defer` geladen. Die CSP erlaubt `script-src 'self'` — kein Eingriff nötig |
| **8** | `KEYWORD_VALIDATION.md` (Website §17) | **Du schreibst sie.** Je Adresse eine Zeile: URL, Titel, H1, Beschreibung, Hauptbegriff. Erzeugt aus dem, was gebaut ist, nicht von Hand getippt. Der Betreiber bestätigt sie danach |
| **4** | Anhebung der Speichergrenze | **Bleibt, wie sie ist.** 500 MB greifen hart. Ein Adminfeld dafür bräuchte eine Obergrenze, die niemand festgelegt hat |

**Zur Fokusfalle, damit die Regel klar bleibt:** Das Menü bleibt **ohne Skript vollständig
bedienbar** — es ist ein `details`-Element und bleibt eines. Das Skript fügt nur die Falle hinzu,
wenn es läuft. Damit gilt Portal-Lastenheft §3 Regel 7 unverändert weiter: Jeder Kernablauf
funktioniert mit abgeschaltetem JavaScript. Schreib genau diesen Satz als Kommentar in die Datei.

**Die übrigen vier Punkte (1, 3, 5, 6) bleiben offen.** Sie brauchen Felder, die im Datenmodell
fehlen, und keiner blockiert den Livegang. Punkt 6 erledigt sich mit Abschnitt 3.

## 5. Messen, was messbar ist

Website-Lastenheft §17 verlangt eine Definition of Done. Inhalt, Formulare und Ortssperre sind
belegt. **Diese Punkte kannst du jetzt nachholen** — Chromium liegt im Container:

| Was | Wie |
|---|---|
| **Kontrast je Seite** | Aus `design/tokens.css` gerechnet, nicht geschätzt. Der niedrigste Wert im System ist 6,42 : 1 — bestätige das je Kombination, die tatsächlich vorkommt |
| **Tastaturdurchlauf** | Jede öffentliche Seite einmal nur mit `Tab` und `Enter`. Fokus muss überall sichtbar sein, die Reihenfolge muss der Leserichtung folgen |
| **Antwortzeiten und Seitengrößen** | Je Adresse einmal messen, als Tabelle |
| **Mailwege über echtes SMTP** | `OFFENE_PRUEFUNGEN.md` sagt, kein Mailweg sei je über SMTP gelaufen. Mailpit liegt im Container: Anmeldelink, Einladung, Anfragebenachrichtigung, beide Zahlungserinnerungen, die A3-Mails — **jede einmal wirklich verschicken und im Posteingang nachsehen** |
| **Adminseiten im Browser** | Dieselbe Datei sagt, keine Adminseite sei je im angemeldeten Browser bedient worden. Hol das nach |

**Was du nicht messen kannst, schreibst du hin** — mit dem Grund und dem Mittel, das es bräuchte.
Ein nicht gemessener Wert wird nie als gemessen gemeldet.

## 6. `LIVEGANG.md` — der Leitfaden, den es noch nicht gibt

Schreib eine Datei, die den Betreiber Schritt für Schritt auf den Zielserver bringt. Sie ist der
Hauptzweck dieser Sitzung. Hinein gehört alles, was auf dem Server anders ist als lokal:

- `APP_ENV=production` in der **Serverumgebung**, nicht in der `.env` — §1.5 liest ihn nur von dort
- `session.cookie_secure = 1` · HSTS · HTTPS erzwungen
- `expose_php = Off`
- Der DocumentRoot zeigt auf `/public`, alles darüber ist verweigert
- `STORAGE_DIR` außerhalb des Webroots, beschreibbar
- **Cron:** der tägliche Lauf, mit dem genauen Befehl zum Kopieren
- **Mail:** SPF, DKIM, DMARC — und eine Testmail an eine **fremde** Adresse, nicht an die eigene
- **Sicherung:** Datenbank **und** `.env`. Ohne `ENC_KEY` ist jedes TOTP-Geheimnis verloren
- Die Ersteinrichtung läuft **einmal** über `/admin/setup`, danach liefert sie dauerhaft 404
- Was zu prüfen ist, bevor die Seite öffentlich erreichbar wird — als Prüfliste zum Abhaken

**Zwei Schritte kann niemand im Code erledigen.** Sie stehen in `LIVEGANG.md` an erster Stelle:

| Was | Warum |
|---|---|
| **Rechtstexte freigeben** | Fünf Entwürfe liegen in `rechtstexte-entwuerfe/`. Ein Mensch prüft sie, trägt sie ein und setzt den Zustand auf `freigegeben`. Die Startsperre §14a hält die Seite bis dahin zurück |
| **Hoster auswählen und einrichten** | Cron und Mailversand müssen auf echter Hardware laufen |

## 7. Die Regeln, die nicht verhandelbar sind

1. **Der Mandantentest wird nie abgeschwächt**, um grün zu werden
2. **Kein Rechtstext geht in den Zustand `freigegeben`** — das macht ein Mensch
3. **Kein Secret ins Repository.** Committet wird nur `.env.example`
4. **Nur PDO mit vorbereiteten Anweisungen.** SQL steht ausschließlich in `/app/data`
5. **Migrationen werden nie geändert, nur ergänzt**
6. **Nichts erfinden:** kein Rechtstext, keine Anschrift, kein Kundenname, keine Referenz, keine
   Zahl, die in den Unterlagen fehlt
7. **`design/tokens.css` bleibt die einzige Stelle** für Farben, Radien und Abstände
8. Für jeden Text, den ein Mensch liest: **`/sartu-texter`**, mit Prüfbericht

## 8. Wie du arbeitest

1. **Modellplan** schreiben
2. **Zweige zusammenführen**, Tests einmal laufen lassen
3. **Abschnitt 3** — die Korrektur, mit Test
4. **Abschnitt 4** — die vier Entscheidungen umsetzen
5. **Abschnitt 5** — messen und eintragen
6. **Abschnitt 6** — `LIVEGANG.md` schreiben
7. Nach jedem Schritt: `/code-review`, dann `/simplify`. Nach Schritt 3: **`/security-review`**
8. **Committen und pushen** nach jedem Schritt

**Am Ende ein Bericht mit vier Teilen:** die Testzahl, selbst ausgeführt · was Abschnitt 3 geändert
hat · welche Messungen liefen und welche nicht · was in `LIVEGANG.md` steht.

**Halte `STAND.md` aktuell.** Bricht der Lauf ab, macht die nächste Sitzung dort weiter.

**Rate nie.** Was fehlt, kommt nach `OFFENE_ENTSCHEIDUNGEN.md` und wird gemeldet.

**Was nicht ausgeführt wurde, wird nicht als geprüft gemeldet.**
