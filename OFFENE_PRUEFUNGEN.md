# Offene Prüfungen

**Stand:** 02.08.2026, Stufe A1 in Arbeit (Bedarfsscheck steht)
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

## Stufe A1 — Bedarfsscheck (`/briefing`)

**Gebaut und ausgeführt:** die Strecke `/briefing` → fünf Themen → Ergebnis → Kontaktdaten →
Danke-Seite, ohne eine Zeile JavaScript. Belegt durch `BedarfsscheckTest` (22 Fälle, 138
Zusicherungen) und einen Durchlauf über den laufenden Apache mit `curl` — also ohne Skript,
weil `curl` keines ausführt.

**Testfälle abgedeckt:** 29 · 30 · 31 · 32 · 33 · 34 · 35 · 36 · 37 · 38 · 39 · 40a · 40b.

| Gebaut | Ungeprüft | Womit es geprüft wird |
|---|---|---|
| Benachrichtigungs-E-Mail an SARTU (§9.5b) | Ob sie **ankommt**. Der Text und die Empfängerlogik sind geprüft, der Versand nicht — `ADMIN_NOTIFY_EMAIL` ist leer, also ging nie eine raus | `ADMIN_NOTIFY_EMAIL` in der `.env` setzen, Bedarfsscheck abschicken, Mailpit ansehen |
| Bedienung ohne JavaScript | Mit **abgeschaltetem** JavaScript im Browser — nicht nur ohne (`curl` und der Test führen ohnehin keines aus). Website-Lastenheft §17 verlangt „getestet, nicht nur behauptet" | Firefox mit `javascript.enabled=false`, Strecke einmal durchlaufen |
| Zurück-Taste und Neuladen auf der Kontaktseite | Der Test prüft die Doppeleinreichung über die `submission_id`, aber nicht das Verhalten des echten Browserverlaufs | Im Browser: absenden, Zurück, erneut absenden |
| 24-Stunden-Ablauf des Zwischenstands | Die Grenze steht als Konstante und wird bei jedem Zugriff geprüft, aber nie über echte 24 Stunden | Systemzeit vorstellen oder `ZWISCHENSTAND_STUNDEN` vorübergehend auf 0 setzen |
| Kontrast und Fokus der neuen Bauteile | Gemessen wurde nichts. Die Farben kommen aus `tokens.css`, die dort gemessenen Werte gelten — die neuen Zusammenstellungen (`.preisblock`, `.wahl`, `.frage--fehler`) sind ungemessen | Kontrastmessung je Paar, Tastaturdurchlauf der ganzen Strecke |
| Ladewerte der neuen Seiten | LCP, TBT und CLS sind nicht gemessen (§17a: vor dem Livegang im Labor) | Lighthouse gegen die Vorabfassung |
| `accent-color` für Haken und Auswahlpunkte | Wie alte Browser ohne `accent-color` die Auswahl darstellen | Prüfung auf Safari 15 und älteren Android-Browsern |

## Stufe A1 — Anfrageliste (`/admin/anfragen`) und Löschlauf

**Gebaut und ausgeführt:** Liste mit den sieben Spalten aus §4b.5, Filter nach Zustand und
Kampagne, Detailansicht als Frage → Antwort, vier Zustände mit Pflichtnotiz bei Ablehnung,
Notiz, Export als Datei, endgültige Löschung. Der tägliche Löschlauf leert `source_ip` nach
30 Tagen und löscht fällige Anfragen. Belegt durch `AnfragelisteTest` (16 Fälle) und einen
echten Lauf von `bin/cron.php` gegen die Arbeitsdatenbank.

**Testfälle abgedeckt:** 40 · 80.

| Gebaut | Ungeprüft | Womit es geprüft wird |
|---|---|---|
| Beide Adminseiten | Im **angemeldeten Browser**. Gerendert und angesehen wurden sie über ein Wegwerfskript gegen die Testdatenbank, nicht über eine echte Anmeldung mit zweitem Faktor | Anmelden, `/admin/anfragen` aufrufen, eine Anfrage ablehnen |
| Export als Datei | Ob der Browser ihn wirklich speichert statt anzuzeigen. Kopfzeilen und Inhalt sind geprüft, das Verhalten des Browsers nicht | Im Browser auf `Datensatz exportieren` klicken |
| Löschlauf als **zeitgesteuerte** Aufgabe | Der Lauf selbst ist ausgeführt. Dass ihn der Server täglich startet, ist es nicht — auf dem Zielhoster gibt es noch keinen Eintrag | Cron-Eintrag anlegen, am Folgetag das Protokoll ansehen |
| Verhalten bei sehr vielen Anfragen | Die Liste hat keine Blätterfunktion. Bei drei Anfragen fällt das nicht auf, bei dreitausend schon. §4b.5 verlangt keine — sie wird gemeldet, nicht vorsorglich gebaut | Ab etwa 200 Anfragen: Ladezeit messen und entscheiden |

**Der Umwandlungsknopf `In Kunde und Projekt umwandeln` fehlt noch.** Er ist der nächste
Schritt in A1 und steht nicht als ausgegraute Schaltfläche da (§0.3b: keine toten Menüpunkte).

**Drei Orange-Bedingungen aus Masterkonzept §8 werden weiterhin nicht gerechnet** — sie lassen
sich aus dem Formular nicht ableiten und werden nicht geraten. Die Begründung je Bedingung steht
im Kopf von `app/services/Empfehlung.php`.

---

## Was ich nicht gebaut habe, obwohl es naheliegt

| Nicht gebaut | Warum |
|---|---|
| Startseite unter `/` | `REIHENFOLGE.md`: Die öffentliche Website entsteht nach Stufe B. §0.3b verbietet „kommt bald"-Bereiche. `/` leitet vor der Einrichtung auf `/admin/setup` und liefert danach 404 |
| Rechtstexte im Wortlaut | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §2 steht auf **offen**. `legal_texts` startet leer |
| Adminmaske für `ADMIN_NOTIFY_EMAIL` | Der Wert steht in §1.5 unter „Erforderliche Werte", wird aber in keinem der acht Setup-Schritte erhoben. **Gemeldet, nicht erfunden.** A1 braucht ihn jetzt: Die Benachrichtigung aus §9.5b liest ihn aus der `.env`. Ist er leer, geht **keine** Mail — und es wird **kein** Ersatzempfänger erfunden. Ein neunter Einrichtungsschritt oder ein Feld in `operator_settings` wäre beides eine Festlegung, die niemand getroffen hat |
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
| ~~Arbeitsverzeichnis und Aufräumen der Tests in `Datenbankfall` zusammenziehen~~ | **erledigt** am 02.08.2026. Sechs Testklassen hielten je eine eigene Fassung, und **eine davon setzte `STORAGE_DIR` nicht** — die Ratenbegrenzung zählte über alle Läufe hinweg mit und liess den zehnten Testlauf an einer fremden Grenze scheitern |
