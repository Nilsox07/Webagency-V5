# Offene Prüfungen

**Stand:** 02.08.2026, nach Stufe A0
**Regel dahinter:** `ENTWICKLUNGSUMGEBUNG.md` — *„Was nicht ausgeführt wurde, wird nicht als
geprüft gemeldet."*

Je eine Zeile: **was gebaut wurde · was daran ungeprüft ist · womit es geprüft wird.**
Diese Datei ist keine Fehlerliste. Sie ist der Unterschied zwischen „später testen" und „nie
testen".

---

## Was tatsächlich gelaufen ist

Damit die Liste unten einzuordnen ist, zuerst das Gegenteil — geprüft und belegt:

| Was | Wie belegt |
|---|---|
| Ersteinrichtung auf **leerer** Datenbank, alle acht Schritte | am 02.08.2026 durchgespielt, Schritt 1 bis 8 |
| `/admin/setup` liefert danach **404** | im Durchlauf und in `SetupTest` |
| Adminanmeldung mit Passwort **und** TOTP | im Durchlauf, `/admin` erreicht |
| Testmail **angekommen** | Mailpit, Absender `noreply@sartu.local`, Betreff „Testnachricht aus der Einrichtung" |
| Acht Migrationen einzeln eingespielt und protokolliert | `bin/migrate.php status`: eingespielt 8, offen 0 |
| **101 PHPUnit-Tests gegen echtes MariaDB 11.4** | grün, 467 Zusicherungen, kein SQLite |
| Nur `/public` über den Webserver erreichbar | `SecurityHeadersTest` gegen den laufenden Apache |

---

## Aufgeschoben — mit Grund und Prüfmittel

| # | Gebaut | Ungeprüft | Geprüft wird es mit | Wann |
|---|---|---|---|---|
| 1 | **HTTPS-Zwang der Ersteinrichtung** (§1.5) | Das Verhalten auf einem Server **mit echtem TLS**. Geprüft ist bisher nur die Ablehnung über HTTP und die Loopback-Ausnahme — beides in `SetupTest`, ohne TLS | Aufruf von `/admin/setup` über `https://` auf dem Zielhoster | vor dem Livegang |
| 2 | **Testmailversand** | Zustellung an einen **echten fremden Posteingang**. Mailpit fängt ab und sagt nichts über SPF, DKIM, DMARC oder Spamfilter | Testmail an eine Adresse außerhalb der eigenen Domain, Posteingang prüfen — nicht den Spam-Ordner | mit dem Hoster (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4) |
| 3 | **`bin/cron.php`** — räumt abgelaufene Anmeldungen ab | Ein **echter Cronlauf** beim Anbieter. Der Befehl wird in Schritt 8 nur angezeigt | Cron eintragen, am Folgetag prüfen, dass der Lauf geschrieben hat | mit dem Hoster |
| 4 | **`bin/migrate.php up`** (§1.5a) | Der Lauf gegen eine **produktive** Datenbank mit echten Daten. Geprüft ist er gegen `sartu_test` | Erste Migration der Stufe B, mit vorheriger Sicherung | Stufe B |
| 5 | **Wartungsmodus während `up`** | Dass Kunden- und Adminbereich dabei **im Browser** 503 liefern. Der Zustand ist im Test geprüft, nicht am laufenden Server | `up` starten, parallel `/admin` aufrufen | Stufe B |
| 6 | **TOTP-Einrichtung** | Das Einlesen in eine **echte Authenticator-App**. Geprüft ist die Rechnung nach RFC 6238, nicht die App | Schlüssel in Google Authenticator oder Aegis eintippen, Code bestätigen | vor dem Livegang |
| 6a | **Wiederholungssperre für TOTP-Codes** | Das Verhalten bei **mehreren Servern**. Der verbrauchte Zeitschritt liegt als Datei in `/storage`; hinter einem Lastverteiler mit getrennten Dateisystemen greift die Sperre nur je Knoten | Erst relevant, wenn mehr als ein Anwendungsserver läuft. Dann gehört der Wert in die Datenbank — und dafür braucht es ein Feld, also eine Entscheidung des Betreibers | vor dem zweiten Server |
| 7 | **`session.cookie_secure`** | Steht lokal auf `0`, weil es kein TLS gibt. In Produktion muss es `1` sein | `.docker/php/php.ini` gilt **nur** lokal — die Produktionskonfiguration entsteht mit dem Hoster | vor dem Livegang |
| 8 | **Sicherheitsheader** | `Strict-Transport-Security` wird nur bei `APP_ENV=production` gesetzt und ist deshalb lokal nie gelaufen | Antwortköpfe auf dem Zielhoster ansehen | vor dem Livegang |
| 9 | **Messwerte** nach `CODEX_AUFTRAG_PORTAL.md` §7 — Antwortzeiten, Seitengrößen, Migrationsdauer, Uploadgrenzen, Barrierefreiheit je Screen | vollständig offen | gehört zur Abgabe der **Stufe A**, nicht zu A0 — die Kernscreens entstehen erst in A1 bis A3 | nach A3 |
| 10 | **Screenshot-Satz** (§7a) | vollständig offen | dito — A0 hat außer Einrichtung und Anmeldung keinen Kundenscreen | nach A3 |

---

## Zwei bewusste Abweichungen, die keine Prüfung nachholt

Beide sind Entscheidungen, keine Lücken — sie stehen hier, damit sie nicht als vergessen gelten.

| Was | Warum so |
|---|---|
| **Kein QR-Code für die Authenticator-App.** Schritt 7 zeigt den Schlüssel in Vierergruppen zum Abtippen | Ein QR-Code bräuchte eine weitere Bibliothek oder JavaScript. Die Sicherheitsheader lassen kein eingebettetes Skript zu (§3 Regel 11), und `SARTU_ENTSCHEIDUNGEN_OFFEN.md` erlaubt keine erfundene Abhängigkeit. **Falls gewünscht, ist das eine Entscheidung des Betreibers**, keine technische Hürde |
| **Zähler der Ratenbegrenzung und verbrauchte TOTP-Zeitschritte liegen als Dateien in `/storage`**, nicht in einer Tabelle | Das Datenmodell in §4 kennt keine Tabelle dafür, und eine zu erfinden verstößt gegen „nichts erfinden". Dazu muss die Begrenzung schon **während** der Ersteinrichtung greifen — zu einem Zeitpunkt, an dem es noch kein Schema gibt |

---

## Eine Eigenheit der lokalen Umgebung, die auf dem Server nicht auftritt

`docker-compose.yml` reicht die `.env` als **Prozessumgebung** in den Container. `Env::get()`
liest die Serverumgebung zuerst — also gewinnen dort die Werte vom Containerstart gegen alles,
was die Ersteinrichtung später in die `.env` schreibt. Sichtbar wird das erst nach einem
`docker compose up -d --force-recreate app`.

**Auf klassischem Hosting gibt es diesen Vorrang nicht**, dort ist die `.env` die einzige Quelle.
Der Vorrang der Serverumgebung ist außerdem beabsichtigt und steht in §1.5 für `APP_ENV`
ausdrücklich so. Wer lokal die Einrichtung von Schritt 2 an durchspielen will, muss den Container
danach neu erzeugen.

---

## Was ich nicht gebaut habe, obwohl es naheliegt

| Nicht gebaut | Warum |
|---|---|
| Startseite unter `/` | `REIHENFOLGE.md`: Die öffentliche Website entsteht nach Stufe B. §0.3b verbietet „kommt bald"-Bereiche. `/` leitet vor der Einrichtung auf `/admin/setup` und liefert danach 404 |
| Rechtstexte im Wortlaut | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §2 steht auf **offen**. `legal_texts` startet leer |
| Adminmaske für `ADMIN_NOTIFY_EMAIL` | Der Wert steht in §1.5 unter „Erforderliche Werte", wird aber in keinem der acht Setup-Schritte erhoben. **Gemeldet, nicht erfunden** — Vorschlag: erheben in A1, wo ihn die erste Benachrichtigung braucht |
| Kundenrouten unter `/portal/` | Die Kundenanmeldung ist A1. `TenantIsolationTest` prüft ausdrücklich, dass die Liste **leer** ist, und schlägt an, sobald die erste dazukommt |
| Firmenname im Fußbereich | §1.4a nennt ihn für den Fußbereich der **öffentlichen** Website — die entsteht nach Stufe B. Bis dahin würde die Abfrage auf jeder Antwort laufen, auch auf 404 und Wartungsseite, und eine Ansicht dürfte nicht auf die Datenbank zugreifen (§1.3) |

---

## Vier Aufräumpunkte, bewusst auf A1 verschoben

Sie stehen hier, damit sie nicht als übersehen gelten. Begründung je Punkt in
`IMPLEMENTATION_SUMMARY.md` §5b.

| Punkt | Wann |
|---|---|
| Verdrahtung der Dienste an einer Kompositionswurzel statt `$this->x ?? new X()` an 29 Stellen | Anfang A1 |
| `EinrichtungsStand` aus `Ersteinrichtung` herauslösen — Prädikate von Mutationen trennen | Anfang A1 |
| Betreiberdaten-Formular als gemeinsames Partial für Setup-Schritt 6 und Adminmaske | wenn A1 die dritte Fassung braucht |
| Arbeitsverzeichnis und Aufräumen der Tests in `Datenbankfall` zusammenziehen | wenn A1 die sechste Testklasse anlegt |
