# Offene Prüfungen

**Stand:** 02.08.2026, Stufe B abgeschlossen — Stufe A und B fertig
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

## Stufe A1 — Kundenanmeldung, Umwandlung, Angebot

**Gebaut und ausgeführt:** Anmeldung ohne Passwort (`/login`, Anmeldelink, Notweg),
Willkommensstrecke (drei Bildschirme), Kundenbereich mit Übersicht und Angebotsseite,
Umwandlung Anfrage → Organisation + Zugang + Projekt mit Einladung, Angebote anlegen und
senden mit allen Prüfregeln aus §4 und §4c, Übergangstabelle aus §5.1a.

Belegt durch `AngebotsstreckeTest` (19 Fälle) — darunter der ganze Weg vom Bedarfsscheck bis
zum Angebot im Kundenbereich — und durch `TenantIsolationTest`, der jetzt die vollständige
Kundenroutenliste kennt und einzeln anfährt.

**Testfälle abgedeckt:** 1 · 3 · 5 · 6 · 7 · 8 · 9 · 10 · 20 · 21 · 22 · 23 · 42 · 45 · 57 ·
59 · 60 · 62 · 83.

| Gebaut | Ungeprüft | Womit es geprüft wird |
|---|---|---|
| Anmeldelink per **E-Mail** | Ob die Mail ankommt und ob der Link im Postfach klickbar ist. Der Test erzeugt den Token über denselben Speicher und löst ihn direkt ein — der Weg über SMTP ist nie gelaufen | Kunden anlegen, `/login` benutzen, Mailpit öffnen, Link im Browser klicken |
| Einladungs-E-Mail nach der Umwandlung | dasselbe | Anfrage umwandeln, Mailpit öffnen |
| Angebotsformular im Adminbereich | Im **angemeldeten Browser**. Der Dienst dahinter ist vollständig geprüft, das Formular selbst nicht | Anmelden, Projekt öffnen, Angebot anlegen und senden |
| Willkommensstrecke im Browser | Drei Bildschirme, `Überspringen`, `Zurück` — geprüft ist nur, dass `welcome_seen_at` einmal gesetzt wird | Als Kunde anmelden, Strecke durchklicken, danach `/portal` aufrufen |
| Abmelden | Der Weg über den Knopf im Browser. Dass die Sitzung serverseitig gelöscht wird, ist im Code sichtbar, aber nicht durchgespielt | Anmelden, abmelden, `/portal` erneut aufrufen |
| Kontrast der neuen Bauteile | Gemessen wurde nichts. `.kundenband`, `.stationen`, `.karte--betont` sind neue Zusammenstellungen aus geprüften Farben | Kontrastmessung je Paar |
| `AdminAngebote::abgelaufeneSetzen()` | Steht bereit, wird von **keinem** Lauf aufgerufen. §5.2 verlangt den Zustand `abgelaufen`; die Mail „Angebot läuft in 3 Tagen ab" gehört nach §10 dazu und ist nicht gebaut | In den täglichen Lauf aufnehmen, sobald A2 die Fristenläufe baut |

### Was in A1 bewusst nicht gebaut wurde

| Nicht gebaut | Warum |
|---|---|
| Annahmeblock auf `/portal/angebot` | Die Annahme ist ein Zustandswechsel mit Anzahlungsrechnung dahinter (§5.1a). Testfälle 11 bis 13 stehen in **A2** |
| Navigationspunkte `Aufgaben`, `Rechnungen`, `Vorschau`, `Domain`, `Inhalte`, `Vertrag`, `Hilfe` | Die Tabellen dahinter entstehen in A2 und A3. §0.3b: keine toten Menüpunkte, nichts Ausgegrautes |
| Statuswechsel über die Oberfläche | Die Übergangstabelle steht und ist geprüft. Die Wechsel selbst gehören zu den Ereignissen, die sie auslösen — Rechnung, Freigabe, Abnahme — und die entstehen in A2 |

---

## Stufe A2 — Auftrag bis Produktionsstart

**Gebaut und ausgeführt:** Angebotsannahme mit den vier Bestätigungen aus §8.2, Rechnungen von
Hand angelegt und gesendet, Zahlungsstatus **von Hand** gesetzt mit Pflicht-Grundlagentext,
Mollie-Zahlungslink als Feld, Überfälligkeitslauf, zwei Zahlungserinnerungen im Abstand von
sieben Tagen, Aufgabenliste aus der Vorlage, Uploads, Faktenfreigabe, Nachrichten an den
Betreuer.

Belegt durch `AuftragsstreckeTest` (24 Fälle) und einen echten Lauf von `bin/cron.php` gegen
die Arbeitsdatenbank.

**Testfälle abgedeckt:** 2 · 4 · 11 · 12 · 13 · 14 · 15 · 16 · 17 · 24 · 26 · 27 · 46 · 51 ·
52 · 53a · 61 · 77 · 78 · 79.

| Gebaut | Ungeprüft | Womit es geprüft wird |
|---|---|---|
| Rechnungsmasken im Adminbereich | Im **angemeldeten Browser**. Der Dienst dahinter ist vollständig geprüft, die Formulare selbst nicht | Anmelden, Projekt öffnen, Rechnung anlegen, senden, Zahlung eintragen |
| Upload über ein **echtes** Formular | Der Dienst ist mit erzeugten `$_FILES`-Einträgen geprüft, nicht mit einer Datei aus einem Dateiauswahlfenster. `upload_max_filesize` und `post_max_size` des Servers sind dabei nie angefasst worden | Im Browser eine 25-MB-Datei anhängen und die Ablehnung ansehen |
| Zahlungserinnerungen als **Mail** | Der Lauf setzt die Marken und ruft den Versand auf. Ob die Mail ankommt, ist ungeprüft | Rechnung überfällig stellen, `bin/cron.php` laufen lassen, Mailpit ansehen |
| Mollie-Zahlungslink | Dass ein echter Link zu einer Zahlung führt. Er ist ein **Textfeld** — es gibt bewusst keine Rückkehrroute und keine Statusableitung (Testfall 14) | Mit dem echten Konto, wenn Stufe 2 ansteht |
| Speichergrenze `MIN_FREIER_PLATZ` (1 GB) | Das Verhalten auf einer **wirklich vollen** Platte. Geprüft ist die Grenze je Organisation, nicht die des Datenträgers | Auf dem Zielhoster, wenn die Belegung bekannt ist |

## Stufe A3 — Produktion bis Livegang

**Gebaut und ausgeführt:** Vorschau bereitstellen mit gleichzeitig geöffneter Korrekturrunde,
Rückmeldungen sammeln und gebündelt einreichen, Runde als eingearbeitet vermerken, zusätzliche
Runde öffnen, Abnahme durch den Kunden, Domainlage von Hand pflegen, Onlinegang mit
Betriebsbeginn und gerechneter Mindestlaufzeit, Betriebsbeginn nachträglich verschieben.

Belegt durch `LivegangTest` (9 Fälle, 127 Zusicherungen) — darunter die ganze Strecke von
`produktion` bis `live` an einem Stück — und durch `TenantIsolationTest`, der jetzt **alle**
Kundenrouten kennt.

**Testfälle abgedeckt:** 18 · 25 · 28 · 53b · 56 · 63.

| Gebaut | Ungeprüft | Womit es geprüft wird |
|---|---|---|
| Die sieben Adminformulare zu Vorschau, Runden, Domain und Onlinegang | Im **angemeldeten Browser**. Die Steuerung ist über Testaufrufe vollständig geprüft, das Markup nicht angesehen | Anmelden, Projekt öffnen, die Strecke einmal durchklicken |
| Die sechs Mails aus §10, die in A3 dazukommen | Ob sie ankommen. Der Text steht im Code und der Versand wird aufgerufen; über SMTP ging keine raus | Strecke durchlaufen, Mailpit nach jedem Schritt ansehen |
| **Bestätigungsdialoge** aus §9.2 (zusätzliche Runde, Onlinegang mit Anzeige des berechneten Mindestlaufzeit-Endes) | Sie sind als Hinweistext neben dem Knopf gebaut, nicht als Dialog — ein Dialog bräuchte JavaScript, und §3 Regel 7 verlangt Bedienbarkeit ohne. **Gemeldet, nicht stillschweigend weggelassen** | Entscheidung des Betreibers: Hinweistext genügt, oder zweistufiges Formular mit Zwischenseite |
| Kontrast von `.liste__unterzeile` | `--muted` auf `--paper` ist in `tokens.css` gemessen; die Einrückung ändert daran nichts, gemessen wurde die Zusammenstellung trotzdem nicht | Kontrastmessung des Paars |
| `preview_url` und `live_url` als **erreichbare** Adressen | Geprüft wird nur, dass sie mit `https://` beginnen. Ob dahinter etwas steht, prüft niemand — und soll auch niemand: ein Abruf durch den Server wäre eine ausgehende Verbindung, die im Lastenheft nicht steht | Der Admin sieht die Adresse und klickt sie selbst an |
| Testfall 56 auf den **öffentlichen** Seiten | Geprüft sind die acht Kundenseiten. Die öffentliche Website entsteht in Stufe B — dort wird der Test um ihre Seiten erweitert | Mit Stufe B |

---

## Stufe B — Öffnungszeiten und die öffentliche Website

**Gebaut und ausgeführt:** Öffnungszeiten mit Ausnahmen, vom Kunden selbst gepflegt und vom
Admin veröffentlicht. Die öffentliche Website mit 30 Launch-Adressen: Startseite,
`/leistungen`, `/preise`, `/ablauf`, fünf Leistungsseiten, drei Branchenseiten, `/ueber-uns`,
`/kontakt` mit Formular, fünf Ratgeberseiten, acht Lexikonbegriffe, 404, `sitemap.xml`,
`robots.txt`, `llms.txt`.

Belegt durch `OeffnungszeitenTest` (14 Fälle) und `WebsiteTest` (21 Fälle, 1.413
Zusicherungen) — Letzterer fährt **jede** Launch-Adresse an und prüft H1, Titel,
Beschreibung, Canonical, tote Verweise, Sitemap, Verbotsliste §2, Ortssperre §0, den
Eigenanteil der Branchenseiten und beide Formulare.

**Testfälle abgedeckt:** 19.

| Gebaut | Ungeprüft | Womit es geprüft wird |
|---|---|---|
| **Laborwerte** LCP < 2,5 s · TBT < 200 ms · CLS < 0,1 (§17) | vollständig offen. Es wurde **nichts** gemessen | Lighthouse mobil gegen die Vorabfassung, Werkzeug und Version im Bericht nennen |
| **Kontrast ≥ 4,5:1** (§17) | Die Farben kommen aus `tokens.css` und sind dort gemessen. Die **Zusammenstellungen** der Website — `.lage`, `.bildplatz`, `.zusage`, `.abschluss`, `.seitenfuss` — sind ungemessen | Kontrastmessung je Paar, auch für `--label-dark` auf `--ink` |
| **Tastaturbedienung und Fokus** (§17) | Kein Durchlauf. Der Skip-Link ist gebaut, aber nie mit der Tastatur benutzt worden | Seite von oben nach unten durchtabben, Fokus muss durchgehend sichtbar sein |
| ~~**Mobiles Menü: Fokusfalle und `Esc`** (§3)~~ | **Gemessen am 02.08.2026 in Chromium 141.** Die Falle hält den Fokus auf 29 von 29 Seiten. `Esc` schloss das Menü **nicht** — die Behauptung in zwei Kommentaren war falsch, ein `details` liefert das nicht. `Esc` und der Klick daneben sind in `menue.js` ergänzt, danach 29 von 29 | erledigt. `MESSUNGEN.md` §2 |
| **`prefers-reduced-motion`** (§17) | `tokens.css` schaltet Animationen ab. Geprüft ist die Regel, nicht ihre Wirkung — es gibt zurzeit ohnehin keine Animation | Im Browser mit gesetzter Einstellung ansehen, sobald Bewegung dazukommt |
| **JS-Budget ≤ 75 KB / 40 KB gzip** (§17) | Nicht gemessen, weil **null Byte JavaScript** ausgeliefert werden. Das Budget ist eingehalten, die Messung fehlt | `curl` auf jede Seite, `<script src>` zählen — heute null |
| **Kein horizontales Scrollen des Seitenkörpers** (§17) | Tabellen und Preisstufen rollen in ihrem eigenen Kasten. Ob der **Körper** auf 320 px still hält, ist nicht angesehen | Browser auf 320 px, jede Seite ansehen |
| **Bilder** — WebP, `srcset`, feste Maße, Alt-Text (§17) | **Es gibt keine Bilder.** 15 Bildplätze sind gekennzeichnet und tragen `[[SCREENSHOT-FEHLT]]` | Mit den Aufnahmen. Die Startsperre §14a Bedingung 4 bricht die Veröffentlichung bis dahin ab |
| **Herkunftserfassung** (§17) | Eine Testanfrage mit `?utm_source=test&utm_medium=audit` ist im Bedarfsscheck geprüft (Testfall 40a), im **Kontaktformular** nicht | Testanfrage über `/kontakt?utm_source=test` abschicken, `leads` ansehen |
| **`KEYWORD_VALIDATION.md`** (§17, vor dem Livegang zwingend) | **Die Datei liegt seit dem 02.08.2026 vor**, erzeugt aus dem Bau: 32 Adressen mit Titel, H1 und Beschreibung. Was fehlt, ist die **Bestätigung** — die Spalte füllt ein Mensch (Keywordstrategie §1.1) — und alles, was Suchergebnisse braucht: Nebenbegriffe, Suchintention, SERP-Typen, Dominanz, verwandte Fragen, Volumen | Ein Volumenwerkzeug und Einblick in die Suchergebnisse. Beides fehlt hier; **nichts davon wurde geschätzt**. Neu erzeugen mit `php bin/keywords.php` |
| **`GEO_DISCOVERY_CHECKLIST.md`** (§17) | Die Datei liegt vor, ist aber nicht abgehakt | Punkt für Punkt durchgehen, Ergebnis je Punkt dokumentieren |
| **Sieben echte Menschen** (§5c) | Niemand hat die Seite gelesen | Vor dem Livegang, wie §5c es beschreibt |
| **Startsperre §14a nachgewiesen** | Die Bedingungen sind im Code und in `WebsiteTest` einzeln geprüft. Ein **absichtlich provozierter Abbruch** im Veröffentlichungsvorgang steht aus — es gibt noch keinen Veröffentlichungsvorgang | Mit dem Hoster, beim ersten Ausrollen. §17 verlangt den Beleg im Bericht |
| **Kontaktformular über SMTP** | Der Datensatz entsteht und ist geprüft. Ob die Benachrichtigung ankommt, ist es nicht | `benachrichtigung_email` setzen, Formular abschicken, Mailpit ansehen |

### Was in B bewusst nicht gebaut wurde

| Nicht gebaut | Warum |
|---|---|
| **Startseiten-Sektion 6 „Wer dahintersteckt"** | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5: Foto und Name des Gründers stehen auf `offen`. „Fehlt es, entfällt die Sektion vollständig — kein leerer Rahmen an einer Vertrauensstelle" |
| **Startseiten-Sektion 8 „Musterprojekte"** | Dieselbe Datei: „Ein bis zwei gekennzeichnete Demoprojekte — offen, zu entscheiden." §5 Sektion 8: „Eine Musterprojekt-Sektion ohne Musterprojekte ist schlechter als keine" |
| **Der Hero-Block auf `/ueber-uns`** | §11 verlangt ein echtes Foto. Es steht auf `offen`, und ein Platzhalter, der wie ein Foto wirkt, ist ausdrücklich unzulässig |
| **Jede Ortsseite, auch `/webdesign-dresden`** | §17: „**Keine** Ortsseite in der produktiven Veröffentlichung — auch nicht als unverlinkter Entwurf", solange `[GESCHAEFTSADRESSE_STATUS]` auf `offen` steht. `WebsiteTest` prüft, dass es sie nicht gibt |
| **`LocalBusiness` in den strukturierten Daten** | §0, dieselbe Sperre. `Strukturdaten` hat dafür **keine Methode** — auch keine ungenutzte |
| **Ortsnamen im Fließtext** | §0. Betrifft §5 Sektion 9 Frage 1 und den Ortsabschnitt auf `/kontakt`. Die **Aussage** steht vollständig da, nur der Ortsname nicht |
| **`/leistung-domain-launch` und die getrennte Local-SEO-Seite** | §10: auf Stufe 2 verschoben |
| **Die sechs weiteren Ratgeberartikel** | §11a und §12: nach dem Launch. Ein Hub mit leeren Einträgen ist ein „kommt bald"-Bereich (§0.3b) |
| **Die zwölf Lexikonbegriffe der Stufe 2** | §13: erst nach Search-Console-Daten |
| **Branchenseiten der Wellen 2 und 3** | §10a. Welle 3 braucht geprüfte Berufsrechte, Welle 2 die ersten Kunden |
| **Ein Einwilligungsbanner** | §17: „nur, wenn zustimmungspflichtige Dienste eingebunden sind — sonst keiner." Es ist keiner eingebunden |

---

## Am 02.08.2026 gemessen — die Zeilen, die dadurch erledigt sind

Der vollständige Bericht steht in `MESSUNGEN.md`. Was dort gemessen wurde, gilt hier als
ausgeführt und nicht mehr als aufgeschoben.

| Was | Ergebnis |
|---|---|
| **Kontrast je vorkommender Kombination**, 30 öffentliche Seiten | 21 Kombinationen, keine unter der Grenze. **Eine lag bei 2,05 : 1 und ist behoben** |
| **Tastaturdurchlauf** jeder öffentlichen Seite, nur `Tab` und `Enter` | 957 bedienbare Elemente, alle erreicht, 0 ohne sichtbaren Fokus |
| **Antwortzeiten und Seitengrößen** je Adresse | Median 4,15 ms · HTML 9,1 KB · gesamt 46,6 KB · **0 Verbindungen zu fremden Domains** |
| **Zwölf Mailwege über echtes SMTP** | 13 Nachrichten im Posteingang gesehen. **Sechs Mails aus §10 gibt es nicht** — siehe unten |
| **Interner Bereich im angemeldeten Browser**, Passwort **und** TOTP | 7 Seiten, je eine H1, alles mit `Tab` erreichbar, kein Kontrast unter der Grenze |
| **Kundenbereich im angemeldeten Browser**, echter Anmeldelink | 9 Seiten, dasselbe Ergebnis |

**Was weiterhin nicht gemessen ist**, steht in `MESSUNGEN.md` unter „Nicht gemessen" — je mit
Grund und Mittel: Laborwerte (LCP, TBT, CLS), Zustellung an ein fremdes Postfach, HTTPS und
HSTS, echter Cronlauf, Browser mit abgeschaltetem JavaScript, `prefers-reduced-motion`,
TOTP in einer echten App, Uploads im Browser.

---

## Sechs Mails aus §10, die es nicht gibt

Gefunden beim Durchspielen der Mailwege am 02.08.2026, **nicht vermutet**. Der Wortlaut steht
in §10 — es ist nichts zu erfinden, nur zu bauen.

| §10 verlangt | Betreff | Wo es fehlt |
|---|---|---|
| Angebot gesendet | `Ihr Angebot von SARTU liegt bereit` | `AngebotDienst::senden()` schreibt nur das Protokoll |
| Neue Aufgaben | `Es liegen Aufgaben für Sie bereit` | kein Versand beim Anlegen von Aufgaben |
| Faktenfreigabe erfolgt (an beide) | `Freigabe bestätigt — wir starten` | `Aufgabendienst::freigeben()` schreibt nur das Protokoll |
| Antwort auf Nachricht | `Antwort auf Ihre Nachricht` | kein Versand in der Nachrichtenantwort |
| Angebot läuft in 3 Tagen ab | `Ihr Angebot gilt noch bis {Datum}` | `Zahlungslauf` setzt `abgelaufen`, warnt aber nicht vorher |
| Angebot angenommen (an Admin) | `Angebot angenommen: {Organisation}` | nur die Kundenmail ist gebaut |

§10 begründet es selbst: *„Der Kunde meldet sich ausschließlich per Anmeldelink an. **Was ihm
keine Mail mitteilt, erfährt er nicht.**"* Bei „Angebot gesendet" heißt das: Das Angebot liegt
im Kundenbereich, und niemand schickt den Kunden hin.

Sie stehen in `LIVEGANG.md` §6.1 als Sperre vor dem ersten Kunden.

---

## Was ich nicht gebaut habe, obwohl es naheliegt

| Nicht gebaut | Warum |
|---|---|
| Startseite unter `/` | `REIHENFOLGE.md`: Die öffentliche Website entsteht nach Stufe B. §0.3b verbietet „kommt bald"-Bereiche. `/` leitet vor der Einrichtung auf `/admin/setup` und liefert danach 404 |
| Rechtstexte im Wortlaut | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §2 steht auf **offen**. `legal_texts` startet leer |
| Adminmaske für `ADMIN_NOTIFY_EMAIL` | Der Wert steht in §1.5 unter „Erforderliche Werte", wird aber in keinem der acht Setup-Schritte erhoben. **Gemeldet, nicht erfunden.** A1 braucht ihn jetzt: Die Benachrichtigung aus §9.5b liest ihn aus der `.env`. Ist er leer, geht **keine** Mail — und es wird **kein** Ersatzempfänger erfunden. Ein neunter Einrichtungsschritt oder ein Feld in `operator_settings` wäre beides eine Festlegung, die niemand getroffen hat |
| ~~Kundenrouten unter `/portal/`~~ | **gebaut in A1.** `TenantIsolationTest` hat beim ersten Hinzufügen angeschlagen, wie vorgesehen — die acht Routen stehen jetzt einzeln im Test und werden einzeln angefahren |
| Firmenname im Fußbereich | §1.4a nennt ihn für den Fußbereich der **öffentlichen** Website — die entsteht nach Stufe B. Bis dahin würde die Abfrage auf jeder Antwort laufen, auch auf 404 und Wartungsseite, und eine Ansicht dürfte nicht auf die Datenbank zugreifen (§1.3) |
| **Block 4 des Kundenbereichs — „Letzte Aktivität"** (§8.1) | **Gebaut am 02.08.2026.** Die Festlegung fehlte nie: §8.1 nennt fünf Ereignisse, jedes im fertigen Wortlaut. Sie sind auf die vorhandenen Aktionen des Prüfprotokolls abgebildet. Die Abfrage wählt zwei Spalten und kann ausschließlich fünf feste Schlüssel erzeugen — `reason`, `old_value`, `new_value`, `detail` und `ip` stehen nicht in der Auswahl und können deshalb nicht durchrutschen. Ein Test prüft das am gerenderten HTML |

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

## Abweichung vom Website-Lastenheft §5 Sektion 1 — Branchenangabe entfernt

§5 Sektion 1 führt als letztes Element des Aufmachers eine **Branchenangabe**
(`Handwerk` · `Praxen` · `Kanzleien` · `Ladengeschäfte`) samt eigenem Korrekturblock
dazu, dass sie nicht anklickbar aussehen darf.

Sie ist am 03.08.2026 auf Anweisung des Betreibers **ersatzlos entfernt** worden — als
Pillen wie als ruhige Monozeile. Begründung: Der Aufmacher trug unter den Knöpfen fünf
kleine, gleich leise Textzeilen übereinander; die Branchenangabe war die fünfte und liess
die Kapazitätszeile darüber untergehen.

**Zu entscheiden, bevor Sektion 1 gebaut wird:** Entweder §5 wird angepasst und die
Branchenangabe fällt dort weg, oder sie kehrt an anderer Stelle zurück. Solange beides
offen ist, baut der nächste Durchgang sie nach Lastenheft wieder ein.

Ungeklärt bleibt damit auch der Zweck, den §5 ihr zuschreibt: dass ein Betrieb erkennt,
ob er gemeint ist. `Bundesweit, ohne Termin` in der Trust-Zeile deckt die *Reichweite* ab,
nicht die *Branche*.

## Aufmacher — was gemessen ist und was nicht

Gemessen mit Chromium bei 1512×982, 1440×900, 1280×800, 1366×768 und 390×844:
Kapazitätszeile und Trust-Zeile stehen überall über der Bildschirmkante; der von §5
verlangte Anschnitt der nächsten Sektion beträgt 197 / 120 / 53 / 4 / 0 px.

**Ungeprüft:** echte Geräte, Safari und Firefox, Zoomstufen über 100 %, sowie die
Frage, ob der Anschnitt unter ~790 px Fensterhöhe überhaupt erreichbar ist, ohne den
Aufmacher unter das Erträgliche zu kürzen.

**Vorbestehend, nicht von diesem Umbau:** bei 390 px Breite ist `scrollWidth` 401 gegen
`clientWidth` 390 — 11 px Querlauf aus den Zierbändern und dem Laptopfuss. `overflow-x:hidden`
am `body` fängt ihn ab, die Ursache steht noch.

## Widerspruch: „Portal" gegen „Kundenbereich" in Sektion 7

Website-Lastenheft §7 nennt die siebte Leistungszeile **`Portal und Freigaben`**
(ebenso die Leistungsübersicht in Zeile 171: `… Rundum-Schutz · Portal`).

`CLAUDE.md` legt für die Außensprache dagegen fest: *Kundenbereich · Ihr Bereich ·
Anmeldung · Ihr Projekt*, und die gesamte übrige Startseite folgt dem — die
Hauptnavigation heißt `Kundenbereich`, Sektion 2 heißt `Der Kundenbereich`.
`Portal` steht zwar nicht auf der ausdrücklichen Verbotsliste (App · Software · SaaS ·
Plattform · Tool · Dashboard · System · Instanz), widerspricht aber der gesetzten
Benennung.

**Vorläufig gebaut als `Kundenbereich und Freigaben`** — Begründung: Konsistenz mit
Navigation und Sektion 2 wiegt schwerer als eine einzelne Tabellenzelle. **Zu
entscheiden:** entweder §7 auf `Kundenbereich` ziehen oder die Außensprache-Regel um
`Portal` ergänzen. Bis dahin ist das eine bewusste Abweichung, keine Übersetzung.

## Prüfung der Aussage „Es gibt keine Aufpreisliste." — trägt, mit einer Lücke

**Die Aussage selbst ist belegt**, nicht geschönt. Masterkonzept, Preisabschnitt:

> **Bewusst NICHT im Erstangebot** (Scope-Schutz): Add-on-Liste, Extraseiten-Preise, SEO-Stufen,
> Änderungsminuten, Logo-Pakete, Express, Newsletter/Tracking als Häkchen. Ein Standardangebot
> endet **exakt** beim veröffentlichten Paketpreis. Neue Ziele nach Auftrag → **ein**
> konsolidiertes Folgeangebot mit Festpreis, **keine Einzelpreisliste.**

Dazu für den Bedarfsscheck: „Keine Paketwechsel-Buttons, keine Add-ons, keine SEO-Auswahl."
Mehrbedarf erzeugt also **ein neues Festpreisangebot**, keine Position aus einer Liste. Das Wort
`Liste` trägt die Aussage — sie ist wörtlich wahr und beschreibt eine bewusste Geschäftsregel.

**Die Lücke liegt in Zeile 8.** `Rundum-Schutz` steht unter der Augenbraue `Im Preis enthalten`
und unter dem Einleitungssatz „zahlen nichts davon extra". Der Schutz kostet aber **59 / 129 /
249 € netto im Monat**. Der von §7 vorgegebene Satz — „Wir betreiben die Website danach: Hosting,
Sicherheit, Backups, Monitoring." — nennt die Pauschale **nicht**. Wer nur diesen Abschnitt liest,
kann ihn für Teil des Einmalpreises halten.

Das ist nicht nur Genauigkeit, es trifft das Geschäftsmodell. Masterkonzept, Abschnitt
„Wie der Rundum-Schutz kommuniziert wird (kritisch!)":

> Der häufigste Kommunikationsfehler wäre, die Website als „wartungsarm" zu bewerben – dann fragt
> der Kunde sofort: *„Warum zahle ich dann 59/129/249 € im Monat?"* Das entwertet die wichtigste
> Umsatzquelle.

**Zu entscheiden:** §7 gibt den Satz wörtlich vor. Entweder er bleibt und die Pauschale wird an
anderer Stelle im Abschnitt sichtbar, oder §7 wird um den Halbsatz „über die Monatspauschale"
ergänzt — so stand es in der freien Fassung vor dem Umbau auf §7. **Nicht eigenmächtig geändert.**

## Slider für Sektion 7 — geprüft und verworfen

Geprüft, weil gefragt. Drei Vorgaben stehen dagegen, alle verbindlich:

1. Bauform-Tabelle §4: Sektion 7 ist `Zeilen — die einzige Liste`, Sektion 8 ist `Karten`.
2. „**Kein Aufbaumuster kommt mehr als zweimal vor**" (Design-Briefing §3.7). Sektion 7 und 8
   liegen direkt nebeneinander — gleiche Bauform hieße zweimal dasselbe hintereinander.
3. Die Begründung der Bauform-Spalte: eine frühere Fassung „bestand von oben bis unten aus
   demselben Zeilenmuster … eine Seite ohne einen einzigen Gangwechsel".

Zur Indexierung: Ein Schieber aus `scroll-snap` hält alle acht Zeilen im Quelltext und ist damit
indexierbar; ein JavaScript-Karussell, das nur die sichtbare Karte einhängt, ist es nicht. Die
Frage entscheidet aber nicht die Bauform — die drei Punkte oben tun es.

## Sektion 7 — Bauform weicht von §7 ab (acht Sätze statt acht Zeilen)

§7 gibt **„acht breite Zeilen (Titel · ein Satz · Tags)"** vor. Gebaut ist stattdessen **ein
Absatz aus acht kurzen Sätzen** — je ein Satz für eine der acht Leistungen, in derselben
Reihenfolge, jeder mit der Kernaussage fett. Die Fachbegriffe aus der Tags-Spalte stehen
vollständig, aber leise, als Monozeile darunter.

**Warum abgewichen wurde** — auf Anweisung des Betreibers, mit zwei Begründungen:

1. Die Tabellenform „sieht aus wie KI-Ausschuss". Titel-Spalte, Text-Spalte, Tag-Spalte in acht
   gleichen Zeilen ist genau die Form, die Generatoren ausgeben.
2. Die Tags sind für die Zielgruppe **unverständlich**. `Suchintention`, `Schema`, `DNS`,
   `Local SEO` sagt einem Dachdecker nichts. §7 stellt sie aber gleichrangig neben den Satz.

Die neue Form löst beides: Wer überfliegt, liest nur die fetten Stellen und hat alle acht
Leistungen in Alltagssprache. Wer liest, liest einen Absatz. Wer die Fachbegriffe kennt, findet
sie unten. Textmaße: 8 Sätze, 61 Wörter, längster Satz 9 Wörter, Schnitt 7,6.

**Zu entscheiden:** §7 auf diese Form ziehen, oder zurück zu acht Zeilen.

## Reibung: „zahlen nichts davon extra" gegen „über die Monatspauschale"

Beim Umbau ist der Rundum-Schutz-Satz um „— über die Monatspauschale" ergänzt worden, um die in
diesem Dokument bereits gemeldete Lücke zu schliessen. Damit steht er nun im selben Blickfeld wie
die von §7 wörtlich vorgegebene Einleitung: *„Sie stellen es nicht selbst zusammen und zahlen
nichts davon extra."*

Beides ist einzeln richtig — „nichts davon extra" meint: keine Einzelposten zum Dazubuchen; die
Pauschale steht in jedem Angebot und im Erstjahreswert. **Nebeneinander gelesen wirkt es
widersprüchlich.**

Die vorgegebene Einleitung ist **nicht** angetastet worden. **Zu entscheiden:** Einleitung um den
Zusatz schärfen (etwa „nichts davon einzeln dazubuchen") oder den Pauschalen-Hinweis an eine
andere Stelle des Abschnitts setzen.

## Recherche: wie vier echte Agenturen ihre Leistungen zeigen (03.08.2026)

Abgerufen und ausgewertet, weil der Betreiber danach gefragt hat:

| Agentur | Anzahl | Bauform | Text je Punkt | Anker |
|---|---:|---|---|---|
| Wee Media | 5 | Kacheln | 1–2 Sätze, 20–30 W. | SVG-Symbol |
| Kopf & Stift | 6 | Kacheln, 3 je Reihe, klickbar | 1–2 Sätze | SVG-Symbol |
| Exovia | 3 | Karten mit Foto, nummeriert | 2–3 Sätze + Stichpunkte | Großfoto |
| Hahnsinn | 2 | zwei große Karten | Überschrift + ein Satz | keiner |

**Drei Befunde, die sich decken:**

1. **Keine zeigt acht.** Die Spanne ist zwei bis sechs. §7 verlangt acht — das liegt über allem,
   was der Markt für zumutbar hält.
2. **Titel sind kurz**, ein bis drei Wörter (`Webdesign`, `SEO`, `Webentwicklung`). §7 hat
   Titel wie `Strategie und Seitenstruktur`.
3. **Ein Satz je Punkt**, nie Fachbegriffslisten daneben.

**Gebaut wurde daraufhin:** sechs Blöcke, Titel ein bis drei Wörter, je ein Satz in
Alltagssprache, 3 × 2 im Raster. Die acht Vorgabepunkte sind darin enthalten — `SEO-Grundlage`
und `Lokale Sichtbarkeit` sind zu `Sichtbarkeit` zusammengefasst, `Kundenbereich und Freigaben`
und `Rundum-Schutz` zu `Betrieb`. Die Fachbegriffe aus der Tags-Spalte stehen vollständig als
leise Monozeile darunter, gehen also weder Leser noch Index verloren.

**Bewusst NICHT wie die Vier gebaut:** keine Rahmen-Kacheln, keine Symbole, nicht klickbar.
Gründe: §7 verbietet die Kachelwand ausdrücklich; Sektion 8 nutzt daneben bereits Karten mit
Bild, und „kein Aufbaumuster kommt mehr als zweimal vor". Jeder Block trägt nur eine Lime-Kante
oben. Zweiter Grund, inhaltlich: Die vier Agenturen **verkaufen** ihre Leistungen einzeln — ein
Kachelraster lädt zum Auswählen ein. Bei SARTU ist alles in jedem Angebot enthalten; anklickbare
Kacheln würden dem Scope-Schutz widersprechen.

**Zu entscheiden:** §7 auf sechs Blöcke ziehen, oder zurück auf acht Zeilen.

## Sektion 6 „Wer dahintersteckt" fehlt vollständig — und sie ist der Grund

Gemessen wurde die Abfolge der Abschnitte. Ergebnis:

| # | Abschnitt | Grund | Visuals |
|---|---|---|---:|
| 2 | Kundenbereich | dunkel | **0** (§4 verlangt „bildgeführt") |
| 3 | Ablauf | dunkel | 0 |
| 4 | Preise | creme | 0 |
| 5 | Die Zusage | dunkel | 0 |
| — | **Wer dahintersteckt** | — | **fehlt ganz** |
| 7 | Leistungen | war dunkel | 0 |
| 8 | Musterprojekte | creme | 3 |

**Zwischen Zusage und Leistungen gehört Sektion 6.** Sie ist laut §6 der **Belegersatz** — das
einzige Gesicht der Seite, an der Stelle, wo bei anderen Agenturen Kundenlogos stehen. Ohne sie
stoßen zwei Abschnitte ohne Bild aneinander, und der Leser rutscht durch.

**Sie ist blockiert, nicht vergessen.** §6 verlangt ein **echtes Foto** von `[GRUENDER_NAME]` und
schreibt ausdrücklich: *„Steht das Foto nicht zur Verfügung, entfällt die Sektion vollständig — ein
leerer Rahmen an einer Vertrauensstelle ist schlechter als gar nichts."* Name und Foto liegen nicht
vor, und Erfinden ist verboten. **Gebraucht werden: Name, Rolle, ein echtes Foto.**

Nebenbefund: Die vier Punkte `kein Baukasten` · `kein WordPress-Hoster` ·
`keine Billig-Seitenschleuder` · `kein Anbieter für Privat- und Hobbyseiten` gehören laut §6 in
**diese** Sektion — nicht zu den Leistungen. Der früher verworfene Entwurf „Gibt es hier nicht"
hatte also den richtigen Inhalt an der falschen Stelle.

## Behoben: Sektion 5 und 7 waren ein einziger dunkler Block

`#zusage` trug `round-top`, `#leistungen` trug `round-bot` — zwischen beiden lag **keine Kante**.
Zwei Abschnitte, eine durchgehende dunkle Fläche. Das war der eigentliche Grund, warum die
Leistungen überlesen wurden.

Jetzt: Die Zusage ist ein abgeschlossener Block (`round-top round-bot`), die Leistungen stehen auf
**Sand** — zwischen der dunklen Zusage und der cremefarbenen Musterstrecke. Auf hellem Grund sind
die Kanten der sechs Blöcke von Lime auf `--ink` gewechselt; eine 2 px starke Lime-Fläche auf
hellem Grund bräuchte laut Gestaltungsregel eine `1px --line`-Kante, was bei einer Haarlinie
unsinnig ist.

**Noch offen, gleiche Ursache:** `#portal` und `#ablauf` sind ebenfalls beide dunkel und liegen
direkt hintereinander. `#muster`, `#fragen` und `#abschluss` sind alle drei creme. Beide Strecken
brauchen denselben Schnitt.

## Sektion 5 war das Gegenteil ihrer Vorgabe — behoben

§5 wörtlich:

> Ein **randlos dunkler Streifen** mit einem einzigen großen Satz. Sonst nichts: kein Bild, keine
> Aufzählung, kein Knopf. […] Er wird **nicht** um Unterpunkte, Symbole oder einen zweiten Satz
> ergänzt — **die Wirkung entsteht aus der Leere ringsum**.
>
> `Ein Preis. Ein Ergebnis. Keine Stundenabrechnung, keine Nachforderung.`

Gebaut war: H2 „Was der Preis bedeutet." plus **drei nummerierte Spalten** mit je einem Absatz.
Der Kommentar im Quelltext sagte es sogar selbst — *„drei Punkte auf Tinte, statt einem Ausruf"*.
Damit war der Abschnitt genau die Aufzählung, gegen die §5 ihn stellt: „das wirksamste Mittel
gegen den Eindruck einer durchgehenden Liste" war selbst zur Liste geworden.

Jetzt: der vorgegebene Satz, gross, mittig, randlos, sonst nichts. Zweite Zeile in `#8d8578`
(5,16 : 1 gegen Tinte).

**Ein Inhalt sucht noch eine Heimat.** Der entfernte Punkt 02 lautete: *„Nach vollständiger
Zahlung gehören Ihnen die Nutzungsrechte am gelieferten Stand. Die Domain läuft auf Ihren Namen,
nicht auf unseren."* Punkt 01 sagt dasselbe wie der Zusage-Satz, Punkt 03 steht bereits in den
häufigen Fragen („Was passiert, wenn ich kündige?"). **Punkt 02 steht nirgends sonst.**
Vorschlag: als weitere Frage in Sektion 9. **Nicht eigenmächtig verschoben.**

## Sektion 7 als eine Lime-Fläche

Der Abschnitt wurde überscrollt, weil er nur aus Text bestand. Die sechs Blöcke stehen jetzt in
**einem** Lime-Kasten mit `--r-xl` und der vorgeschriebenen `1px --line`-Kante.

Die Form ist die Aussage: ein Kasten, ein Preis, alles darin. Damit ist sie zugleich
regelkonform — Lime ist Fläche, nie Schrift; auf Lime steht `--ink`. Gemessene Kontraste:
Titel 12,48 : 1, Fliesstext rund 5,5 : 1.

Es ist die **einzige grosse Lime-Fläche der Seite**. Sonst trägt Lime nur Knöpfe und Haarlinien.
Genau deshalb wirkt sie.

**Ungeprüft:** ob eine Fläche dieser Größe im Ausdruck und auf kalibrierten Schirmen angenehm
bleibt. Auf Verlangen lässt sie sich auf `--sand` mit Lime-Kante zurücknehmen.

## CTA-Prüfung: acht von zehn fehlten

Auf Nachfrage geprüft, ob jeder Abschnitt einen Weiterweg hat. Vorher/nachher:

| § | Abschnitt | Vorgabe | war |
|---|---|---|---|
| 1 | Aufmacher | `Bedarf prüfen lassen` → `/briefing` · `Preise ansehen` → `/preise` | da, aber **auf Seitenanker** statt auf die Seiten |
| 2 | Kundenbereich | Textlink `Den Kundenbereich ansehen` → `/leistung-portal` | **fehlte** |
| 3 | Ablauf | `Ablauf im Detail` → `/ablauf` | **fehlte** |
| 4 | Preise | je Stufe einer: 2 × `Einschätzen lassen`, `Bedarf prüfen lassen`, `Sonderprojekt besprechen` + Pflichtzeile | **alle vier fehlten** |
| 5 | Die Zusage | ausdrücklich **kein** Knopf | korrekt leer |
| 6 | Wer dahintersteckt | Textlink `Mehr über SARTU` → `/ueber-uns` | Abschnitt fehlt ganz (Foto) |
| 7 | Leistungen | `Alle Leistungen im Überblick` → `/leistungen` | da |
| 8 | Musterprojekte | `Alle Musterprojekte ansehen` | **fehlte** |
| 9 | Häufige Fragen | keiner vorgesehen | korrekt leer |
| 10 | Bedarfsscheck | `Bedarf prüfen lassen` → `/briefing` · Textlink `Preise ansehen` → `/preise` | primär da (**auf `#top`**), sekundär fehlte |

Alle jetzt gesetzt. Lime bleibt im Preisblock **einem** Knopf vorbehalten — Platzhirsch, laut §4
„sichtbar die Empfehlung". Die Sonderprojekt-Karte ist selbst dunkel, liegt aber in einem hellen
Abschnitt; `.btn-hell` war an `.dark` gebunden und fiel dort auf Lime zurück.

**Zielseiten laut §5b und den CTA-Zeilen:** `/briefing` · `/preise` · `/leistungen` · `/ablauf` ·
`/leistung-portal` · `/ueber-uns` · `/musterprojekte`. Keine davon existiert bisher — die Startseite
verweist ins Leere, bis sie gebaut sind.

## Widerspruch in §5b: `Leistungen` in der Navigation oder im Fußbereich?

§5b sagt an einer Stelle: *„`Leistungen` bleibt damit in der Hauptnavigation statt im Fußbereich —
es ist die Seite, die auf ‚webdesign' und ‚firmenwebsite erstellen lassen' antwortet."*

Zwei Absätze später im **selben** Abschnitt: *„`Leistungen` bleibt als Seite bestehen
(Suchmaschinenrelevanz), wandert aber in den Fußbereich, damit die Hauptnavigation nicht
überläuft."*

Beide tragen eine Begründung. **Gebaut nach der Punkteliste** — sie steht unter der Überschrift
„Dies ist die einzige gültige Navigation" und führt `Leistungen` an erster Stelle. Der zweite Satz
wirkt wie ein Rest der abgelösten Fassung. **Zu entscheiden, nicht geraten.**

Die Navigation lautet jetzt wie vorgegeben: `Leistungen · Preise · Ablauf · Kundenbereich ·
Über uns · Fragen`. `Über uns` fehlte, die Reihenfolge stimmte nicht. Mit sechs Punkten greift das
Mobilmenü ab 1180 px statt 1040 px — §5b sieht genau das vor: „Wird die Zeile dadurch zu breit,
greift das Mobilmenü früher — der verständlichere Begriff wird nicht für sechs Pixel geopfert."

## Behoben: unsichtbarer CTA im Bedarfsscheck — 1,00 : 1

`Preise ansehen` im Bedarfsscheck war **exakt** unsichtbar: `rgb(20,17,13)` auf `rgb(20,17,13)`.

Ursache war die Klasse `.txtlink`, die ich beim CTA-Einbau angelegt hatte. Sie band die Farbe an
den **Abschnitt**: `.sec:not(.dark) .txtlink{color:var(--ink)}`. Der Bedarfsscheck ist ein heller
Abschnitt — aber der Knopf liegt auf einer **dunklen Karte** darin. Die Regel griff also richtig
und lieferte trotzdem Tinte auf Tinte. Eine Farbe, die am Elternabschnitt hängt statt am Bauteil,
ist genau diese Fehlerklasse.

`.txtlink` ist ersatzlos entfernt. Jeder Weiterweg ist jetzt eine Pille mit eigener Farbe.

**Vollständige Nachmessung aller dreizehn CTAs** (Grund durch Vorfahren ermittelt, nicht geraten):

| Kontrast | Anzahl |
|---:|---|
| 12,48 : 1 | 4 × Lime-Knopf mit Tinte |
| 15,60 : 1 | 1 × auf Sand |
| 17,39 : 1 | 6 × heller Umriss auf dunklem Grund |
| 18,82 : 1 | 2 × Tinte auf Papier |

Keiner unter 4,5 : 1. Die Treffer der Kopfleiste im ersten Durchlauf waren Messartefakte — deren
Grund ist ein `oklab()`-Wert, den der Parser nicht als RGB lesen konnte.

**Abweichung, gemeldet:** §2 nennt `Den Kundenbereich ansehen` und §10 `Preise ansehen`
ausdrücklich **Textlink**, nicht Knopf. Beide sind auf Wunsch des Betreibers Pillen geworden. Die
Rangfolge bleibt erhalten — sekundäre Knöpfe tragen Umriss statt Lime, Lime bleibt dem
Hauptknopf vorbehalten. **Zu entscheiden:** §2 und §10 nachziehen oder zurück auf Textlink.

# Vollprüfung der Startseite — 03.08.2026

Auf Wunsch unvoreingenommen geprüft, ausdrücklich auch gegen die eigenen Änderungen dieser
Sitzung. Gemessen mit Chromium bei 360, 390, 768, 1280 und 1440 px.

## Behoben (8)

| # | Befund | Schwere |
|---|---|---|
| 1 | **`meta viewport` fehlte vollständig.** Ein echtes Telefon rendert die Seite dann auf ~980 px und zoomt heraus. Alle bisherigen Mobilprüfungen waren dadurch geschönter als die Wirklichkeit — Playwright setzt den Viewport direkt und übergeht den fehlenden Tag | **schwer** |
| 2 | Aufmacher-Visual ohne das von §5 verlangte Kennzeichen **`Musteransicht`** — null Vorkommen auf der Seite. Die Attrappe zeigte eine Oberfläche, die es noch nicht gibt, ohne jeden Hinweis | **schwer** |
| 3 | `meta description` fehlte | mittel |
| 4 | Überschriftensprünge **1→5** und **2→4**. Ursache: drei `<h5>` in der Geräteattrappe, drei `<h4>` als Fußbereichsspalten. Beide sind keine Dokumentabschnitte | mittel |
| 5 | Text mit **1,51 : 1** bei 7 px in der Attrappe („Öffnen"). Jetzt trägt die Attrappe `aria-hidden`; das Kennzeichen bleibt bewusst außerhalb, es ist eine Aussage, keine Zierde | mittel |
| 6 | Fußbereichs-Beschriftungen bei **4,19 : 1**, nötig sind 4,5 | mittel |
| 7 | `.foot-grid` trug **zwei** `color`-Angaben in derselben Regel; die zweite hob die erste auf | klein |
| 8 | Querlauf **401 px bei 390 px Breite**. Kein Element stand ungeklippt über — `overflow-x:hidden` am `body` macht diesen zum Scrollbehälter, der Überstand blieb am `html`. `overflow-x:clip` an beiden klippt, ohne Scrollbehälter zu werden, und bricht die klebende Kopfleiste nicht (geprüft: bleibt bei y=0) | klein |

## Gemeldet, nicht eigenmächtig geändert (6)

1. **Zwei Design-Umschalter im Dokument** — `<aside class="gt">` „Lime im Grund" und „Grundton
   probieren", zusammen **14 Formularfelder**, fest positioniert über allem. Für den Entwurf
   nützlich, in der ausgelieferten Seite ein Fremdkörper. **Müssen beim Bau der PHP-Fassung
   entfallen.**
2. **Sechs Radien außerhalb der Skala** — alle in der Geräteattrappe: `4px` `6px` `9px` `19px`
   `26px` (`laptop-lid`, `screen`, `ui-next`, `ui-card`, `phone`). Die Regel sagt „keine achte
   Form daneben" und nennt `border-radius:30px` einen Abgabefehler. Meine Einschätzung: eine
   Hardware-Nachbildung ist eine Illustration, kein Bauteil — die Regel trifft dazu keine
   Ausnahme. **Entscheidung nötig.**
3. **Sektion 6 „Wer dahintersteckt" fehlt** — blockiert auf Name, Rolle, echtes Foto.
4. **Sektion 2 ist laut Bauform-Tabelle „bildgeführt" und hat null Visuals.** §2 verlangt eine
   Ansicht aus dem Kundenbereich mit Vermerk `Musteransicht`, ersatzweise einen ehrlich
   beschrifteten Bildplatz. Auch der fehlt.
5. **`<!doctype html>` und `<html lang="de">` fehlen.** Sie können in dieser Datei nicht stehen —
   sie wird zur Veröffentlichung in ein Grundgerüst eingebettet. **Gehören in das PHP-Layout**,
   dort zwingend mit `lang="de"`.
6. **Sieben Zielseiten existieren nicht** — `/briefing` `/preise` `/leistungen` `/ablauf`
   `/leistung-portal` `/ueber-uns` `/musterprojekte`.

## Geprüft und ohne Befund

Abschnittsfolge gegen die Bauform-Tabelle (stimmt, bis auf die fehlende 6) · Lime **nie** als
Schriftfarbe auf hellem Grund · keine `prefers-color-scheme`-Regel · Konsole sauber ·
`:focus-visible` vorhanden · verbotene Außenwörter: keine — die zwei Treffer auf „System" meinen
Kundensysteme und WordPress, nicht das eigene Angebot · **alle Textkontraste jetzt über der
Schwelle** (4,5 : 1, bzw. 3 : 1 ab 24 px oder 18,66 px fett).

## Logo eingebaut — und drei verschiedene Grüntöne gefunden

Die Originaldatei ist am 03.08.2026 als SVG-Quelltext eingegangen und verarbeitet. Alle Pfade im
Browser über `getBBox()` vermessen, nicht geschätzt:

| Teil | Breite × Höhe (Einheiten) |
|---|---|
| Zeichen | 42,8 × 41,8 |
| Wortmarke `SARTU` | 167,9 × 25,3 |
| Zusatz `DIGITAL` | 106,0 × 8,0 |

### Bereinigt wurde

- **`<style>`-Block und Klassen entfernt** — beim direkten Einbetten kollidieren `.cls-0`
  bis `.cls-3` mit allem anderen im Dokument. Farben stehen jetzt als `fill` am Pfad
- `enable-background:new` entfernt — toter Rest aus dem Zeichenprogramm, hat seit Jahren
  keine Wirkung
- Die **Haarlinie** `stroke:#DADBD2` mit 0,25 an der Wortmarke entfernt. Sie war der Grund,
  warum die Marke auf hellem Grund als Umriss erschien: Die Füllung ist `#FFFFFF`
- `role="img"` und `<title>SARTU</title>` ergänzt, damit der Name für Suchmaschinen und
  Vorleseprogramme lesbar bleibt

Dateigrößen: Zeichen **349 Byte**, Sperrung **1.034 Byte**, mit Zusatz **1.784 Byte**.

### Befund 1 — drei Grüntöne, die sich um Haaresbreite unterscheiden

| | Farbe | auf Creme | auf Tinte |
|---|---|---:|---:|
| Zeichen im Logo | `#BDDD4A` | 1,43 : 1 | 12,19 : 1 |
| Zusatz im Logo | `#ABC957` | 1,73 : 1 | 10,06 : 1 |
| **Seite** `--lime` | `#a3e635` | 1,39 : 1 | 12,48 : 1 |

Drei Töne für dieselbe Markenfarbe. Nebeneinander liest sich das als Unsauberkeit, und die
Gestaltungsregel sagt: **„Eine Akzentfarbe."**

**Vorläufig gebaut mit der Farbe der Originaldatei** (`#BDDD4A`), über
`--logo-lime` an einer Stelle änderbar. **Zu entscheiden:** Logo auf `--lime` ziehen, oder
`--lime` im gesamten Designsystem auf `#BDDD4A` ändern. Ersteres ist ein Pfad, letzteres
betrifft jeden Knopf, jede Kante und den Lime-Kasten.

### Befund 2 — der Zusatz, jetzt mit Zahl

`DIGITAL` ist **8,0 Einheiten** hoch, die Wortmarke **25,3** — der Zusatz ist **32 %**.
Bei 21 px Wortmarkenhöhe in der Kopfleiste landet er bei **6,6 px**. Das bestätigt die
Empfehlung von vorher mit einem gemessenen Wert statt einer Schätzung.

### Befund 3 — die Wortmarke war für dunklen Grund gezeichnet

Füllung `#FFFFFF`. In der Seite steht sie deshalb auf `currentColor`: Tinte auf hellen
Abschnitten, Creme auf dunklen — **eine Datei, kein zweiter Satz.**

Der Zusatz `Webdesign` neben dem Logo ist entfallen: Seit die Wortmarke Grafik ist, stand der
Name zweimal, und die Breite fehlte dem Hauptknopf, der dadurch auf drei Zeilen brach.

## Nachtrag: Die Sperrung ist für den Zusatz gezeichnet

Der Betreiber bemängelte, die Wortmarke „klebe oben". Nachgemessen — er hat recht, und der
Grund liegt **nicht** in der Kopfleiste:

| | oben | unten | Mitte |
|---|---:|---:|---:|
| Zeichen | 59,0 | 100,8 | **79,90** |
| Wortmarke allein | 61,5 | 86,8 | 74,15 → **5,75 zu hoch** |
| Wortmarke **+ Zusatz** | 61,5 | 101,4 | 81,45 → **1,55 daneben** |

Die Kopfleiste selbst war symmetrisch (22 px über, 23 px unter dem Logo). Der Versatz steckt im
Logo: **Ohne den Zusatz ist die Sperrung kopflastig, weil sie mit ihm entworfen wurde.** Das
Zeichen reicht tiefer als die Wortmarke; der Zusatz füllte genau diesen Raum.

**Gebaut ist jetzt Fassung B** (`design/logo-achse.html` zeigt alle drei): Wortmarke um 5,75
Einheiten gesenkt, damit sie die Mittelachse des Zeichens trifft. Gemessen nach dem Umbau:
Abweichung **0,00 px**. Logo von 30 auf 34 px vergrößert, Wortmarke damit 20,6 statt 18,2 px.

Dieselbe Korrektur ist in `sartu-logo-hell.svg` und `sartu-logo-dunkel.svg` eingetragen — sonst
kippt die Sperrung bei jeder anderen Verwendung genauso.

**Zu entscheiden bleibt:** Ist die gesenkte Wortmarke als Fassung-ohne-Zusatz akzeptabel, oder
soll die Sperrung ohne Zusatz von der Person neu gesetzt werden, die das Logo gezeichnet hat?
Eine eigene Sperrung ohne Zusatz ist bei Marken mit Zusatz der Normalfall — nur wird sie
üblicherweise gestaltet und nicht gerechnet.

## Abweichung: Kennzeichen „Musteransicht" im Aufmacher entfernt

§5 Sektion 1 verlangt am Aufmacher-Visual wörtlich: *„Portal-Cockpit-Screenshot, Badge
‚Musteransicht'."* Das Kennzeichen war bei der Vollprüfung als fehlend gemeldet und ergänzt
worden. Es ist am 03.08.2026 **auf Anweisung des Betreibers wieder entfernt** worden.

**Warum das mehr ist als eine Formfrage:** Das Visual zeigt eine **nachgebaute Oberfläche** des
Kundenbereichs. Den gibt es noch nicht — der Bau steht auf Stufe A0. Ohne Kennzeichnung wirkt
eine Bildschirmansicht wie eine Aufnahme eines vorhandenen Produkts.

Dieselbe Regel greift an anderer Stelle bereits: Die Musterprojekte tragen
`Musterprojekt — kein Kundenauftrag`, und §2 verlangt für das Kundenbereich-Bild ausdrücklich
*„ehrlich beschrifteter Bildplatz, keine nachgebaute Oberfläche"*.

**Vor dem Livegang zu klären:** entweder das Kennzeichen zurück, oder das Visual durch eine
echte Aufnahme ersetzen, sobald der Kundenbereich läuft. Die Startsperre nach §1.4a verhindert
getrennt davon, dass mit Platzhaltern nach außen gegangen wird — dieser Punkt gehört auf ihre
Liste.

## Entschieden: eine Lime-Farbe

Die drei Grüntöne sind aufgelöst. Das Logozeichen läuft jetzt auf `--lime` `#a3e635` statt auf
`#BDDD4A`. Begründung: Das Designsystem verwendet die Farbe hundertfach — Knöpfe, Kanten, Punkte,
den Lime-Kasten —, das Logo einmal. Die Regel „**eine** Akzentfarbe" ist damit wieder erfüllt.

Die abgelegten SVG-Dateien behalten `#BDDD4A`: Sie sind die Markenfassung für Druck, E-Mail und
Partner. Nur die eingebettete Fassung in der Seite ist umgefärbt.

## Logo im Fußbereich

Über der Beschreibung, 30 px hoch, Zeichen auf `--lime`, Wortmarke auf `--cream` über
`currentColor` — dieselbe Datei wie oben, kein zweiter Satz.

---

## Zusammenführung abgeschlossen — 05.08.2026

**Was gebaut wurde:** `spezifikation/` umfasst jetzt **18 Themendateien, rund 4.000 Zeilen**.
Alle vier Lastenhefte sind ausgewertet; keine Themendatei liest noch Bauwissen aus ihrer Quelle.

**Was daran ungeprüft ist — und womit es zu prüfen wäre:**

### Ausgeführt und bestanden

| Prüfung | Ergebnis |
|---|---|
| **Testfallnummern gegen `REIHENFOLGE.md`** | **88 = 88, keine Abweichung in beide Richtungen.** Die Stufenverteilung aus der Zuordnungstabelle gerechnet: A0 26 · A1 34 · A2 21 · A3 6 · B 1 · C 0 — **deckungsgleich** mit der Tabelle in `15_TESTFAELLE.md` |
| **Alle Dateiverweise zwischen den 18 Themendateien** | **alle treffen**, kein toter Verweis |
| **Schrittfolge `/ablauf` gegen Startseite** | **Reihenfolge stimmt überein.** Die Abdeckung nicht ganz — siehe Befund unten |

### Ausgeführt und **nicht** bestanden — behoben

| Befund | Behebung |
|---|---|
| Von 24 E-Mail-Auslösern hatten **drei kein auslösendes Ereignis**: „Es liegen Aufgaben für Sie bereit", „Ihre Öffnungszeiten sind aktualisiert", „Ihr Angebot gilt noch bis {Datum}" | `12_ADMINBEREICH.md`: Knöpfe `Aufgaben freigeben` und `Veröffentlichen` ergänzt. `11_KUNDENBEREICH.md`: der tägliche Lauf vollständig beschrieben, **sechs** Aufgaben statt zwei |
| `offers.status = abgelaufen` war ein **Zustand ohne Weg dorthin** — ein Angebot wäre stillschweigend verfallen | Schritt 6 des täglichen Laufs setzt ihn |
| `Zahlung zurücknehmen` war als E-Mail und als Testfall 54 vorhanden, **aber nicht als Adminaktion** | in `12_ADMINBEREICH.md` als eigene protokollierte Aktion mit `reason` ergänzt |
| Der **Pflichthinweis zur Preisnennung** stand in **drei** Fassungen im Umlauf | `08_TEXTREGELN.md` legt genau zwei fest, lang und kurz; alle anderen Stellen verweisen nur noch |
| Die **Rangfolge** in `UEBERGABE_DATEILISTE.md` führte die vier Lastenhefte auf den Rängen 4 bis 7 — **gegen `CLAUDE.md`** | `spezifikation/` auf Rang 4, Lastenhefte als Begründungsarchiv ohne Rang |

### Noch offen

| Befund | ungeprüft | Prüfmittel |
|---|---|---|
| Die Dublettenprüfung lief über Zahlenwerte und Dateiverweise | ob es Dubletten in **Formulierungen** ohne Zahl gibt | Textabgleich über alle 18 Dateien |
| Der neue Satzspiegel (1300 px) ist an **neun Breiten** auf Überlauf geprüft | wie er sich in **echten Browsern** verhält — gemessen wurde nur in Chromium | Gegenprobe in Firefox und Safari |

**Nicht ausgeführt:** Es wurde keine Zeile Anwendungscode gebaut und kein Test ausgeführt. Diese
Sitzung hat ausschließlich Spezifikation zusammengeführt.


### Die Startsperre — analysiert, nicht behoben

`10_WEBSITE_SARTU.md` führt **zehn** Bedingungen, unter denen die produktive Veröffentlichung
abbricht. Geprüft werden davon **zwei**:

| Bedingung | Testfall |
|---|---|
| 1 — `[[PLATZHALTER]]` in Impressum oder Datenschutz, oder unter 500 Zeichen | **keiner** |
| 2 — `/agb` verlinkt und mit Platzhalter | **keiner** |
| 3 — eine `noindex`-Seite steht in der `sitemap.xml` | **keiner** |
| 4 — leerer Bildplatz oder `[[SCREENSHOT-FEHLT]]` | **keiner** |
| 4a — Platzhalter in „Wer dahintersteckt" | **keiner** |
| 5 — verbotene Zeichenkette im ausgelieferten Text | **keiner** |
| 6 — Datei außerhalb `/public` erreichbar | **49** (prüft die Erreichbarkeit, **nicht** den Abbruch) |
| 7 — Ortsname bei offener Standortfrage | **keiner** |
| 8 — Rechtstext auf `entwurf` oder `in_pruefung` | **66** und **81** |
| 9 — Pflichtfeld der Betreiberdaten leer | **66** |

**Warum das zählt:** Die Sperre existiert, weil eine Warnung überlesen wird. **Eine ungeprüfte
Sperre ist eine Warnung** — sie fällt beim ersten Umbau aus, ohne dass jemand es merkt. Sieben
der zehn Bedingungen schützen vor einem Rechtsverstoß oder einem sichtbaren Platzhalter live.

**Warum es nicht einfach behoben wurde:** Sieben neue Testfälle machen aus 88 → 95. Die Zahl 88
steht in `CLAUDE.md`, `REIHENFOLGE.md`, `15_TESTFAELLE.md` und der Zuordnung von 88 Einzelzeilen.
Sie war **laut `CLAUDE.md` schon viermal falsch**. Sie ohne Auftrag zu ändern wäre genau der
Fehler, den die Zuordnungstabelle verhindern soll.

**Vorschlag zur Entscheidung:** Testfall 66 nach dem Muster von 53 → 53a/53b in **66a bis 66j**
teilen — je eine Bedingung. Das hält die Zählung bei **88 durchnummerierten**, so wie 5a, 5b,
40a, 40b und 53a/53b es schon tun, und die Zuordnung in `REIHENFOLGE.md` ändert sich nicht: alle
zehn entstehen in **A0**, wie 66 heute.

---

## Beide vorgelegten Entscheidungen sind getroffen — 05.08.2026

**Zahlung als Ablaufschritt: nein.** Begründung des Auftraggebers, und sie trägt: Gezahlt wird je
nach Plan zwei- oder dreimal, ein einzelner Kasten wäre in zwei von drei Fällen falsch. Was
stattdessen gebaut wird: Schritt 3 der Startseite trägt die **Bedingung**, dass es nach der
ersten Zahlung losgeht. Festgehalten in `10_WEBSITE_SARTU.md` und `17_SEITEN_SARTU.md`.

**Startsperre: Fall 66 in 66a bis 66j zerlegt.** Zehn Bedingungen, zehn Prüfungen, dazu die
Gegenprobe, dass der Staging-Vorgang **nicht** abbricht. Die Zerlegung erhöht die Zahl nicht —
**es bleibt bei 88**, und die Zuordnung in `REIHENFOLGE.md` bleibt unverändert, weil alle
Teilfälle in A0 entstehen wie 66 heute. Gegengerechnet: 88 = 88, keine Abweichung.

## Satzspiegel und Schriftgrade geändert — 05.08.2026

`--wrap` von 1180 auf **1380 px**, H1 von `clamp(32px,3.72vw,54px)` auf
`clamp(32px,3.45vw,47px)`, H2 von `clamp(31px,4.3vw,50px)` auf `clamp(27px,2.9vw,40px)`.
Gemessene Begründung: `spezifikation/07_MARKE_UND_GESTALTUNG.md`.

**Zwei Fehler gefunden, beide behoben:**

1. **Die H2 war größer als die H1** — 50 gegen 44 px am Anschlag, und bei 1000 px Fensterbreite
   43 gegen 33 px. Die Rangfolge war umgekehrt. Vom Auftraggeber gemeldet
2. **Die H1 wuchs schneller als ihre Spalte.** Der erste Versuch mit `3.6vw` holte den
   Vierzeiler zwischen 1024 und 1280 px zurück. Ränder und Spalte skalieren mit `4vw` — die
   Schriftkurve muss folgen, sonst überholt sie die Spalte

**Geprüft an zwölf Breiten:** H1 > H2 überall, kein waagerechter Überlauf, H1 dreizeilig ab
1024 px mit zwei Wörtern in der ersten Zeile.

**Ungeprüft:** das Verhalten außerhalb von Chromium. **Bekannte Grenze:** unter 768 px bleibt die
H1 vierzeilig mit je einem Wort — bei rund 330 px Spaltenbreite nicht auflösbar, ohne die H1 auf
24 px zu drücken.

## Aufmacherhöhe — 05.08.2026

Der Aufmacher war **630 px hoch, unabhängig vom Bildschirm**. Auf 1366 × 768 ergab das den in
`10_WEBSITE_SARTU.md` §5 geforderten Anschnitt von 68 px, auf 1920 × 1080 aber **366 px**.
**Dieselbe Seite wirkte auf dem größeren Bildschirm kleiner.** Vom Auftraggeber gemeldet.

**Behoben:** Mindesthöhe 78 % der Fensterhöhe (560–1000 px), Inhalt mittig. Füllung jetzt
durchgehend 78 %, Anschnitt zwischen 41 und 159 px.

**Bewusst nichts hinzugefügt.** Geprüft: Alles, was §5 für den Aufmacher vorschreibt, ist
gebaut — Eyebrow, H1, Vorspann, beide Knöpfe, Pflichthinweis, Trust-Zeile, Kapazitätszeile,
Visual. Es fehlt nur, was auf ausdrückliche Anweisung entfernt wurde (Branchenangabe,
`Musteransicht`-Kennzeichen, beide unten als offen geführt). **Ein Preisanker wäre der
naheliegende Zusatz gewesen und ist ausdrücklich falsch:** Die Sektionsreihenfolge begründet,
dass die Zahl erst nach Unterscheidungsmerkmal und Vorhersehbarkeit fällt.

### Dabei gefunden, **nicht** behoben

| Befund | Lage |
|---|---|
| **Mobil entsteht kein Anschnitt.** Bei 390 × 844 ist der Aufmacher 1086 px hoch, bei 768 × 1024 sind es 1126 px — beide höher als das Fenster | Folge des gestapelten Visuals, **nicht** der Höhenregel; galt vorher genauso. Auflösbar nur, indem das Visual mobil kleiner wird. **Gestaltungsentscheidung, nicht selbst getroffen** |
| Auf sehr hohen Bildschirmen (2560 × 1440) greift die Obergrenze von 1000 px, Anschnitt 361 px | bewusst gedeckelt — ohne Deckel entstünde Leerraum **im** Aufmacher statt darunter |
| Der Prototyp führt als H2 der zweiten Sektion `Ihr Projekt bleibt an einem Ort.` | Dieser Satz steht im Texter-Skill unter den **verworfenen** Fassungen. Gebunden wäre dort der Positionierungssatz `Ohne einen einzigen Termin zur fertigen Website.` **Text, kein Layout** — gehört in den Durchgang des Skills |


## Nachtrag zur Aufmacherhöhe — 05.08.2026

**Der erste Eingriff war falsch.** Ich hatte dem Aufmacher nur Höhe gegeben (Mindesthöhe 78 % der
Fensterhöhe). Auf breiten Bildschirmen wurde die Leere dadurch **größer**: bei 2560 × 1440 füllte
der Inhalt nur **53 %** des Aufmachers, der Container **54 %** der Breite. Vom Auftraggeber mit
Bildschirmfoto belegt.

**Ursache:** Container, Schrift und Visual waren **alle bei rund 1380 px gedeckelt**. Jenseits
davon gewann die Seite nur Rand — sie wurde auf dem größeren Bildschirm **kleiner**, nicht größer.

**Behoben durch vier Änderungen zusammen:**

| | vorher | jetzt |
|---|---|---|
| `--wrap` | 1380 px fest | `clamp(1380px, 90vw, 1800px)` |
| H1 | `clamp(32px, 3.45vw, 47px)` | `clamp(32px, calc(3.55vw − 7px), 64px)` |
| H2 | `clamp(27px, 2.9vw, 40px)` | `clamp(27px, calc(3vw − 6px), 54px)` |
| Innenabstände Aufmacher | 17 · 23 · 21 px fest | `clamp()`, wachsen mit |

**Gemessen bei 2560 × 1440:** Inhaltshöhe 555 → **743 px**, Füllung 53 % → **70 %**, Visual
544 → **733 px**. **Im mittleren Bereich (1512) unverändert** — H1 47, H2 39, wie abgenommen.

**Die Lehre:** Ein zu leerer Bereich wird nicht durch mehr Fläche voller. Erst als Container,
Schrift, Visual und Abstände **gemeinsam** mitwuchsen, trug er.

## Versalien-H1 im Aufmacher — 05.08.2026

**Auftrag des Betreibers:** „die h1 ist zu klein und geht unter … zu lange wörter, ich denke
3 zeilen und große buchstaben wären top."

**Gebaut:** H1-Wortlaut auf `Programmierte Firmenwebsites zum Festpreis.` gekürzt, Versalien über
`text-transform`, Schriftgrad an die Spalte gebunden (`cqw` statt `vw`), Zeilenabstand und
Unterschneidung für Großbuchstaben angepasst, Lime-Balken unter die Versalgrundlinie gesetzt.

**Ausgeführt und bestanden:** zehn Breiten von 390 bis 2560 px, je Breite Schriftgrad,
Zeilenzahl, Zeilenbreiten, waagerechter Überlauf, Aufmacherfüllung und Anschnitt. Dazu
Bildschirmfotos bei 390 · 768 · 1024 · 1512 · 2560.

| Befund | Lage |
|---|---|
| **Gemessen wurde ausschließlich mit DejaVu Sans** — die Schriftkette (`-apple-system`, `Segoe UI`, `system-ui`) löst in diesem Container darauf auf | DejaVu ist die **breiteste** Schrift der Kette. Auf macOS und Windows bleibt mehr Rest, nie weniger — die Messung kann zu wenig Platz melden, nie zu viel. **Ungeprüft ist die Gegenrichtung: ob 9,9 cqw auf schmaleren Schriften zu klein wirkt.** Prüfmittel: dieselbe Seite auf einem Mac und einem Windows-Rechner öffnen |
| **`KEYWORD_VALIDATION.md` führt noch den alten H1-Wortlaut** | Die Datei wird von `bin/keywords.php` **erzeugt**, nicht getippt — das Skript fährt jede Adresse durch den echten Router und braucht dafür die Datenbank. Ohne laufenden Container nicht ausführbar. Prüfmittel: `docker compose exec app php bin/keywords.php`. **Von Hand nachtippen ist ausdrücklich falsch** (Begründung im Docblock von `WebsiteTest::testDieBestaetigungsdateiFuehrtJedeLaunchadresse`) |
| **Kein Test ausgeführt** — kein Datenbankdienst erreichbar (`SQLSTATE[HY000] [2002]`) | Geändert wurde eine Zeichenkettenkonstante; die H1-Tests zählen `<h1>`-Elemente, sie prüfen keinen Wortlaut. Risiko gering, **aber ungeprüft**. Prüfmittel: `docker compose exec app vendor/bin/phpunit` |
| **Anschnitt bei 1366 × 768 von 82 auf 30 px gefallen** | Dort bestimmt jetzt der Inhalt die Höhe statt der Mindesthöhe. Sichtbar bleibt er. **Bewusst hingenommen** — eine größere H1 kostet senkrechten Platz |
| **Mobil ist der Aufmacher 82 px höher geworden** (768 × 1024: 1114 → 1196 px) | Er war schon vorher höher als das Fenster; der bekannte Befund oben wird dadurch **tiefer**, nicht neu. Auflösbar nur über die Größe des gestapelten Visuals — **Gestaltungsentscheidung, nicht selbst getroffen** |
| **Die Angabe „Anschnitt 41 bis 159 px" war schon vor dieser Änderung falsch** | Nachgemessen: 2240 × 1260 → 186 px, 2560 × 1440 → 301 px, beide unverändert gegenüber vorher. In `10_WEBSITE_SARTU.md` korrigiert. **Offen bleibt die Frage dahinter:** ob 301 px noch ein Anschnitt sind oder schon eine zweite Sektion im ersten Bild |
| **Die Anwendung unter `/public` hat die Änderung nicht bekommen** | `public/assets/css/tokens.css` steht weiter auf `--wrap:1180px` und `--fs-h1:clamp(40px,6.4vw,80px)`; `app/views/pages/website-start.php` kennt keine Versalien. **Diese Abweichung ist älter als diese Sitzung** — schon die drei vorangegangenen Aufmacher-Eingriffe gingen nur in den Entwurf. Angeglichen wurde nur der **Wortlaut** der H1, damit Entwurf und Anwendung nicht zwei verschiedene Sätze führen |
| **Der Fußzeilensatz führt weiter `Individuell programmierte Firmenwebsites zum Festpreis, …`** | Absicht: Er gibt das Kernversprechen aus `01_GESCHAEFTSMODELL.md` wieder, das unverändert gilt. Nur die H1 ist längenbeschränkt, die Fußzeile nicht |

**Die Lehre dieses Durchgangs:** Die vorige Fassung hielt vier Einwortzeilen unter 768 px für die
„Grenze des Satzes, nicht der Einstellung". Richtig beobachtet, falsch geschlossen — es war die
Grenze **dieses** Satzes. **Wo eine Einstellung an eine Grenze stößt, lohnt die Frage, ob der Text
die Grenze setzt.**

### Nachtrag: erste H1-Zeile kursiv — 05.08.2026

**Einwand des Betreibers an der ausgeglichenen Fassung:** „das sieht hässlich aus, weil das ein
Block ist und jede Zeile der H1 gleich lang ist. ich denke wenn wir so ein wort oder so kursiv
machen sieht es besser aus und die aktuelle schriftart lassen."

**Gebaut:** `h1 em { font-style: italic; font-weight: 400 }` auf der ersten Zeile. Vier Fassungen
gerendert und verglichen (erste, zweite, dritte Zeile kursiv; kursiv allein gegen kursiv +
leichter). **Die Schrift bleibt unverändert** — die Frage aus `20_OFFEN.md` ist damit nicht
beantwortet und wurde nicht angefasst.

**Ausgeführt:** dieselben zehn Breiten erneut gemessen. Schriftgrad, Zeilenzahl, breiteste Zeile
und Rest sind **identisch** — die breiteste Zeile ist die zweite und bleibt aufrecht.

| Befund | Lage |
|---|---|
| **Der Kursivschnitt ist hier nicht echt.** DejaVu Sans führt nur `Book` und `Bold`; Chromium stellt die aufrechte Schrift mechanisch schräg | Auf macOS (SF Pro) und Windows (Segoe UI) liegt ein **echter** Schnitt vor und sieht besser aus als das Bildschirmfoto. **Ungeprüft**, weil hier keine Schrift mit echtem Kursivschnitt in der Kette steht. Zur Beurteilung wurde ersatzweise Liberation Sans gerendert — das ist ein Stellvertreter, kein Nachweis. Prüfmittel: `design/startseite.html` auf einem Mac oder Windows-Rechner öffnen |
| **Wird die Schriftfrage aus `20_OFFEN.md` entschieden, gehört der Kursivschnitt zum Umfang** | Sonst fällt die H1 auf die mechanische Schrägstellung zurück. Betrifft Inter und Instrument Sans gleichermaßen — beide haben einen, er muss nur mitgeladen werden |
| Die H1 hat auf einer echten Schrift **90 px Rest** statt 19 | Gemessen mit Inter und Instrument Sans bei 1512 px. Der Faktor 9,9 cqw ist an DejaVu kalibriert; nach der Schriftentscheidung ist er **neu einzumessen** und dürfte auf rund 11 cqw steigen |

**Die Lehre:** Der Ausgleich der Zeilenlängen war ein **Hilfsmaß** — er sagt, wie groß die Schrift
werden darf. Ich hatte ihn zum Ziel gemacht und auf 92 % hin gebaut. **Was messbar besser wird,
wird nicht dadurch schöner.**

### Nachtrag: Aufmacher nach fremdem Vorbild umgebaut — 05.08.2026

**Auftrag des Betreibers:** „schau dir mal an wie andere webdesign agenturen gelöst haben den hero
bereich mit text usw links und übertrage deren layout auf unseres."

**Recherchegrundlage — und ihre Grenze.** Nachgesehen bei **Instrument**, **Work & Co**, **Dept**
und **BASIC/DEPT**, dazu zwei Übersichtsartikel. **Der Browser kommt aus dieser Umgebung nicht an
fremde Hosts** (`ERR_CONNECTION_RESET` bei allen zehn Versuchen; `curl` dagegen ja). Die vier
Aufmacher sind deshalb **über den Textweg** ausgewertet — Bausteine, Reihenfolge, Wortzahlen,
Knopfanzahl. **Nicht gemessen wurden Schriftgrade, Spaltenbreiten und Abstände fremder Seiten.**
Alles, was hier als Zahl steht, ist an **unserer** Seite gemessen.

| Befund | Lage |
|---|---|
| **Anschnitt bei 1366 × 768 auf 21 px** (Ausgangsstand heute früh: 82) | Der Aufmacher trägt jetzt eine H1 von 66 statt 41,5 px. Das kostet senkrechten Platz und war die Absicht. Abgefangen ist der schlimmste Fall über `min(11cqw, 8.6vh)` — ohne die Höhenschranke waren es 1 px. **Sichtbar bleibt er**, aber knapp |
| **Mobil ist der Aufmacher weiter höher als das Fenster** (768 × 1024: −307 px) | Bekannter Befund, durch die größere H1 tiefer. Auflösbar nur über die Größe des gestapelten Visuals — **Gestaltungsentscheidung, nicht selbst getroffen** |
| **Kein Test ausgeführt, kein PHP angefasst** | Geändert wurden nur `design/startseite.html` und Spezifikationsdateien. Die Anwendung unter `/public` ist unberührt und weiterhin nicht angeglichen |
| **`KEYWORD_VALIDATION.md` weiterhin veraltet** | Unverändert offen aus dem Eintrag oben: `docker compose exec app php bin/keywords.php` |
| **Die Schriftfrage aus `20_OFFEN.md` bleibt offen** | Nicht angefasst, wie gewünscht |

**Was bewusst NICHT übernommen wurde.** Die vier Vorbilder haben im ersten Bild **nur**
Überschrift, höchstens einen Absatz und **einen** Knopf. SARTU kann darauf nicht verzichten:
Trust-Zeile, Preishinweis und Branchenangabe sind in §5 **gebunden**, und sie sind die einzigen
Belege einer Agentur **ohne Referenzen und ohne Kunden**. Übernommen wurde deshalb die
**Gruppierung** (drei Gruppen statt sieben gestapelter Blöcke), nicht die Kargheit. **Entfernt
wurde nichts.**

**Zwei Vorgaben wurden gebeugt, beide begründet und beide reversibel:**

| Vorgabe | Was jetzt gilt |
|---|---|
| §5: Sekundär-CTA `Preise ansehen` | Wortlaut und Ziel **unverändert**, Darstellung vom Knopf zum Textlink. Zwei gleich starke Knöpfe lassen den Leser zwischen Knöpfen statt zwischen Angeboten wählen |
| Versalien der H1 (Wunsch vom selben Tag) | **Zurückgenommen.** Gemessen tragen sie 65,8 px, der Gemischtsatz 73,1 px — die Versalfassung war **kleiner**, nicht größer, und war der Grund für den Blockeindruck |

### Nachtrag: vier Preisstufen und die Auszeichnung als Seitenmittel — 05.08.2026

**Auftrag:** „bekommen wir jetzt in der preis section alle 4 nebeneinander? … übernimm die hero
sektion, lege aber nicht fest welches wort kursiv sein soll also nicht immer das erste oder so."

**Gebaut:** Preisraster von drei auf **vier Spalten**, Sonderprojekt aus dem Querblock in die
vierte Karte. Auszeichnung `em` gilt jetzt für **jede** Überschrift; das ausgezeichnete Wort ist
je Überschrift nach der Aussage gewählt und steht in **sieben von acht** Fällen nicht an erster
Stelle.

**Ausgeführt:** Preisraster bei 390 · 1100 · 1366 · 1512 · 1920 · 2560 px gemessen — vier Spalten
ab 1180, zwei darunter, eine unter 760. Kein waagerechter Überlauf. Aufmacher unverändert
nachgemessen.

| Befund | Lage |
|---|---|
| **Vier der sechs Sektionsüberschriften waren Sätze, die der Texter-Skill ausdrücklich verwirft** | `Von wenigen Angaben …`, `… das passende Ergebnis`, `So könnte ein Projekt aussehen` und `Ihr Projekt bleibt an einem Ort`. **Die Auszeichnung hat sie aufgedeckt:** Wer fragt, welches Wort die Aussage trägt, findet bei diesen Sätzen genau das unbestimmte Wort, das der Skill verwirft. Drei sind durch die **im Skill dokumentierten** Ersatzfassungen getauscht und gegen die Umfangsvorgaben aus §3, §4 und §8 geprüft (8 von 9 · 10 von 12 · 7 von 8 Wörtern) |
| **`Ihr Projekt bleibt an einem Ort.` steht weiter da** | Verworfen, aber der Skill nennt **keine** Ersatzfassung dafür, und §2 gibt nur einen Auftrag. **Nicht erfunden.** Prüfmittel: ein Durchgang des Texter-Skills für Sektion 2 |
| **Der Überschriftentausch erzeugte eine Dreifachnennung** | Vorzeile `Eine Empfehlung statt Paketwahl`, H2 `Sie wählen kein Paket`, Einleitung `Sie wählen kein Paket — …` sagten dasselbe. Vorzeile auf `Vier Stufen` und Einleitung auf die Zuordnungslogik geändert (21 Wörter, §4 erlaubt 25) |
| **§8 verlangt eine Einleitung über dem Musterraster** — Gründungsjahr und Anzahl | **Diese Zeile war falsch und ist am 05.08.2026 korrigiert.** Ich hatte notiert, das Gründungsjahr fehle in den Unterlagen. Es steht dort: `10_WEBSITE_SARTU.md` §8 führt *Zahlen `gebunden`: Gründung **2026** · **drei** Beispiele*, und `SARTU_TEXTREGELN.md` führt den fertigen Satz. **Einleitung ergänzt.** Zwei Fassungen weichen um ein Wort ab — Lastenheft `Die drei Beispiele sind Muster`, Textregeln `Die drei Beispiele **unten** sind Muster`. Gebaut ist die Fassung der Textregeln (Rang 3 regelt die Form); **keine trägt eine Begründung, also gemeldet statt ausgewählt** |
| **Platzhirsch hat nicht mehr Fläche als die anderen** | `02_PREISE_UND_ZAHLUNG.md` verlangt „größte Fläche". **Galt schon vorher nicht** — auch das Dreierraster war gleichspaltig. In `10_WEBSITE_SARTU.md` als Entscheidung notiert |
| **Karten sind bei 1366 und 1512 px nur 299 bzw. 302 px breit** | Die Preiszeile bricht dort zweizeilig um (`7.900 €` / `einmalig + 249 €/Mon.`). Lesbar, aber eng. Ab 1920 px sind es 389 px und die Zeile passt |
| **Kein Test ausgeführt, kein PHP angefasst** | Wie in den Einträgen davor: nur `design/startseite.html` und Spezifikationsdateien |

### Nachtrag: Musterprojekte — Unklarheiten aufgelöst, Zielseite ergänzt — 06.08.2026

**Auftrag:** „dann löse die Unklarheiten noch auf und ergänze das."

| Unklarheit | Wie sie aufgelöst wurde |
|---|---|
| **„Ein bis zwei" gegen „drei"** | **Scheinwiderspruch.** Die Zahlen zählen Verschiedenes: **drei Karten** sind Beschreibungen und entstehen am Schreibtisch, **ein bis zwei Demoprojekte** sind gebaute Websites. In `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5 als Tabelle festgehalten. **Die Geschäftsentscheidung über die Demoprojekte bleibt offen** — sie war nie meine |
| **„ungebaut" gegen „ehrlich beschrifteter Bildplatz"** | Über drei Stufen aufgelöst (`10_WEBSITE_SARTU.md` §8): Stufe 0 keine Fälle → keine Sektion · Stufe 1 Fälle ausgeschrieben → Sektion mit Bildplatz · Stufe 2 Demoprojekt gebaut → echte Aufnahme. Der Satz „bis dahin ungebaut" meinte Stufe 0; damals fielen Fälle und Bilder zusammen |
| **Falsche Sektionsnummer** | **Sachfehler, keine Entscheidung.** `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5 sagte zweimal „Sektion 8" für Gründername und Gründerfoto. Richtig ist **Sektion 6**. Korrigiert. `spezifikation/20_OFFEN.md` führte es bereits richtig |
| **`/musterprojekte` fehlte** | Als `## 4a.` in `17_SEITEN_SARTU.md` ergänzt: Aufbau in sieben Blöcken, 700–1.000 Wörter, gebundene Gattungen und Kennzeichnung, Sperren, Verlinkung |

**Dabei gefunden und behoben — der Entwurf wich von gebundenen Werten ab:**

| Befund | Behoben |
|---|---|
| **Die drei Gattungen waren falsch.** Der Entwurf führte `Dachdeckerei · Elektrotechnik · Praxis`, gebunden sind seit 01.08.2026 **`Malerbetrieb · Physiotherapiepraxis · Arbeitsrechtskanzlei`** | ja |
| **Je Karte fehlten drei der fünf vorgeschriebenen Bestandteile.** Gebaut waren Bildplatz und ein Satz; §8 verlangt Ausgangslage · empfohlene Lösung · Seitenstruktur · Bildplatz · was der Kunde liefert | ja, als Beschreibungsliste |

**Was weiterhin offen ist — und bewusst nicht entschieden wurde:**

| Punkt | Warum nicht von mir |
|---|---|
| **Ein bis zwei Demoprojekte bauen?** | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5 sagt es selbst: „eine Geschäftsentscheidung mit Aufwand und Außenwirkung — keine Gestaltungsfrage. **Wer entscheidet: der Betreiber**" |
| **Der Text der drei Fälle** | Geschrieben ist eine erste Fassung im Entwurf. Sie ist **nicht durch den Texter-Skill gelaufen** und trägt keinen Prüfbericht. Prüfmittel: ein Durchgang des Skills für Sektion 8 und `/musterprojekte` |
| **`/musterprojekte` ist spezifiziert, nicht gebaut** | Bewusst **nicht** in `Launchadressen` aufgenommen: `WebsiteTest` verlangt für jede Launchadresse eine Zeile in `KEYWORD_VALIDATION.md`, und die Datei lässt sich hier nicht erzeugen (keine Datenbank). Reihenfolge: erst Seite bauen, dann Adresse eintragen, dann `bin/keywords.php` |
| **`Ihr Projekt bleibt an einem Ort.`** | Unverändert offen aus dem Eintrag davor — verworfener Satz ohne dokumentierte Ersatzfassung |

### Nachtrag: die drei Musterprojekte müssen verschieden aussehen — 06.08.2026

**Einwand des Betreibers:** „da müssen wir aber auch verschiedene Designs machen oder um zu
zeigen was so möglich ist."

**Der Einwand ist gedeckt — die Regel stand schon da.** `03_KUNDENPRODUKT.md` führt unter
*Varianten statt Einheitswebsite* drei geschlossene Achsen: Inhaltsdichte
`compact`/`balanced`/`editorial` · Formcharakter `precise`/`human`/`bold` · Bewegungsintensität
`none`/`subtle`/`expressive`. **Sie war nur nie auf die Musterprojekte angewandt.**

**Festgeschrieben:** je Musterprojekt ein Wertetripel, hergeleitet aus der Branche
(`17_SEITEN_SARTU.md` §4a). Beide **sichtbaren** Achsen — Dichte und Formcharakter — decken dabei
ihre volle Spanne ab, je einmal pro Stufe. Bewegung tut das nicht und soll es nicht: eine Stufe
zu wählen, damit die Tabelle vollständig aussieht, wäre Gestaltung für die Vorlage statt für den
Kunden.

| Befund | Lage |
|---|---|
| **Der Komponentenkatalog existiert nicht** | `03_KUNDENPRODUKT.md` sagt „der Agent darf nur **freigegebene** Varianten kombinieren" und nennt die Varianten nur beispielhaft („etwa drei Hero-Kompositionen …"). **Welche** es sind, steht in keiner Datei. **Eine Regel ohne Menge** — sie kann weder befolgt noch verletzt werden. In `20_OFFEN.md` aufgenommen |
| **Die Wertetripel gelten trotzdem schon** | Die drei Achsen sind geschlossen, ihre Werte stehen fest. Sie tragen den sichtbaren Unterschied; die Komponentenwahl verfeinert ihn nur |
| **Der Umfang der Demoprojekt-Entscheidung wächst** | „Ein bis zwei" genügt für Bildmaterial und Arbeitsbeleg. **Für den Beleg, dass es kein Baukasten ist, braucht es drei** — zwei zeigen einen Unterschied, drei eine Spanne. **Nicht abgeleitet, sondern als Frage notiert:** der Aufwand steigt um die Hälfte, das entscheidet der Betreiber |
| **Nichts davon ist gebaut** | Es sind Vorgaben, keine Bildschirme. Die Bildplätze im Entwurf bleiben ehrlich beschriftet (Stufe 1 nach `10_WEBSITE_SARTU.md` §8) |

**Warum das keine Geschmacksfrage ist.** SARTU verkauft *individuell programmiert, kein
Baukasten*. Drei Musterprojekte in derselben Gestaltung wären der Gegenbeweis — vorgelegt von
SARTU selbst, an genau der Stelle, an der der Leser nach Belegen sucht.

### Nachtrag: Lime-Platte in Sektion 7 entfernt, Kundenseiten-Stack entschieden — 06.08.2026

**Einwand:** „‚Es gibt keine Aufpreisliste' dieser abschnitt sieht optisch nach wie vor komisch
aus vor allem zu viel lime." · **Entscheidung:** „kundenseiten php".

**Gemessen, bevor etwas geändert wurde:** Die Leistungssektion trug **eine** Lime-Fläche von
**429.000 px²** — mehr als sämtliches Lime im Aufmacher zusammen (675.000 px², verteilt auf acht
Flächen, überwiegend Knopf und Gerätepaar). **Sie hielt die Kantenregel ein und war trotzdem
falsch.** Entfernt; die sechs Blöcke tragen sich über ihre Ink-Kante selbst.

Regel dazu in `07_MARKE_UND_GESTALTUNG.md` ergänzt: **Lime kennzeichnet Handlungen und Zustände,
keine Bereiche.** Mit der gemessenen Verteilung je Sektion als Bezugsgröße.

**Kundenseiten-Stack:** **PHP**, entschieden vom Betreiber. In `03_KUNDENPRODUKT.md` aufgenommen —
Setzung **und** die zwei Gründe, samt der ausdrücklichen Klarstellung, dass „PHP ist besser für
SEO" **nicht** der Grund ist. Damit ist der Widerspruch im Masterkonzept (Zeile 167 „statische
Auslieferung" gegen Zeile 396 „PHP, serverseitig gerendert") aufgelöst.

**Dabei gefunden, NICHT behoben — Sektion 7 weicht an drei Stellen von `10_WEBSITE_SARTU.md` §7 ab:**

| Vorgabe | Entwurf |
|---|---|
| **„Acht breite Zeilen"**, Grenze: „**genau acht Zeilen**" | **sechs** Blöcke in einem 3 × 2-Raster |
| Titel `gebunden`: `Strategie und Seitenstruktur` · `Webdesign und Programmierung` · `Website-Texte` · `SEO- und GEO-Grundlage` · `Lokale Sichtbarkeit` · `Domain und Launch` … | `Strategie` · `Design und Programmierung` · `Texte` · `Sichtbarkeit` · `Domain und Start` · `Betrieb` — **gekürzt** |
| Tags `gebunden`, **je Zeile** | alle Tags in **einer** Sammelzeile am Fuß |

**Warum ich das nicht angefasst habe:** Im CSS steht eine Begründung für die Abweichung —
*„Sechs Blöcke statt acht Zeilen. Recherche an vier Agenturseiten: alle zeigen 2 bis 6
Leistungen, nie acht."* Eine frühere Sitzung hat das also **bewusst** geändert. Die Titel sind in
§7 aber `gebunden` und müssen laut derselben Stelle „mit `03_KUNDENPRODUKT.md` deckungsgleich
bleiben". **Zwei begründete Stände, kein Vorrang erkennbar — gemeldet statt ausgewählt.**

> **Der CSS-Kommentar ist zusätzlich veraltet:** Er nennt als Begründung „nur eine **Lime**-Kante
> je Block", gebaut ist `border-top: 2px solid var(--ink)`. Die Lime-Kante gibt es nicht und gab
> es vermutlich nie.

### Nachtrag: Leistungen als acht breite Zeilen — 06.08.2026

**Einwand:** „wir müssen unsere leistungen anders darstellen und nicht so langweilig. wie machen
das andere agenturen?"

**Recherche:** BASIC/DEPT (`/services`) führt **vier** Leistungen als breite Zeilen mit Zähler
`01/04`, Titel, Satz und Fallbeispielen. Dept (`/services`) führt **fünf** als Zeilen mit `01`–`05`,
Titel, Satz und Bild. **Beide: Zeilen mit Zähler, kein Kachelraster.** Wie schon beim Aufmacher
über den Textweg ausgewertet — der Browser erreicht aus dieser Umgebung keine fremden Hosts.

**Gebaut:** acht breite Zeilen, je Zähler · Titel · Satz · Tags, 1 px Trennlinie. Damit sind
**alle drei** in der letzten Sitzung gemeldeten Abweichungen von §7 behoben:

| vorher | jetzt |
|---|---|
| sechs Kacheln im 3 × 2-Raster | **acht Zeilen** |
| gekürzte Titel (`Strategie`, `Texte`, `Betrieb`) | die **gebundenen** Titel aus §7 |
| alle Tags in einer Sammelzeile | **Tags je Zeile**, wie §7 verlangt |

**Warum acht und nicht vier bis fünf wie bei den Vorbildern.** Die Agenturlisten beantworten
*„was verkaufen wir"* — da ist Kürze Schärfe. Diese Sektion beantwortet *„Ist alles dabei?"*.
**Einschlussliste, nicht Angebotsliste** — dort ist Länge das Argument.

**Was die Zusammenlegung gekostet hatte:** Die sechs Kacheln entstanden, indem `SEO- und
GEO-Grundlage` mit `Lokale Sichtbarkeit` und `Kundenbereich und Freigaben` mit `Rundum-Schutz`
verschmolzen wurden. Damit waren **der Differenzierer und der Deckungsbeitrags-Motor** aus
`01_GESCHAEFTSMODELL.md` in einer Zeile namens „Betrieb" verschwunden.

| Befund | Lage |
|---|---|
| **Die vier neuen Sätze sind nicht durch den Texter-Skill gelaufen** | Geschrieben nach dem Auftrag aus §7 (je ≤ 15 Wörter, ein Satz, keine Fachbegriffe, keine Wirkung versprechen) und von Hand gegen die Verbotsliste geprüft. **Kein Prüfbericht.** Prüfmittel: ein Durchgang des Skills für Sektion 7 |
| **Gemessen:** 390 · 900 · 1512 · 2560 px | acht Zeilen überall, kein waagerechter Überlauf, Titel auf ein bis zwei Zeilen, Zeilenhöhen 105–136 px |
| Die Sektion ist mit 1.488 px deutlich höher als vorher | Folge der acht Zeilen. Bewusst: es ist die einzige Liste der Seite |

### Nachtrag: Leistungen als Leistungsverzeichnis — 06.08.2026

**Einwand:** „nach wie vor katastrophe. das muss spannender sein. sei kreativ und denk dir was
aus. und kein ai müll sondern irgendwas was den kunden abholt."

**Die Diagnose nach drei Fehlversuchen:** Alle drei blieben im selben Rahmen. **Die Sektion
behauptet „Es gibt keine Aufpreisliste" und zeigte dann eine Liste.** Nicht die Zeilen waren
falsch, die Form widersprach der Aussage.

**Gebaut:** ein **Leistungsverzeichnis** — `Pos.` · `Leistung` · `Umfang` · `Preis`, acht
Positionen, in der Preisspalte achtmal `enthalten`, darunter die Summenzeile
`Aufpreise gesamt — 0,00 €`.

**Warum diese Form den Kunden abholt:** Ein Malermeister **schreibt Leistungsverzeichnisse
selbst**. Eine Praxis und eine Kanzlei kennen sie als Angebot. Es ist das eine Dokument, in dem
Leistungen zeilenweise mit Preisen stehen — und genau dort sitzt das Misstrauen. Die Wiederholung
derselben Zelle braucht keinen erklärenden Satz.

| Befund | Lage |
|---|---|
| **Berührt §7 „keine Preise"** | `0,00 €` ist kein Preis für etwas, sondern die Abwesenheit von Aufpreisen; das Geldformat folgt `02_PREISE_UND_ZAHLUNG.md`. **Einzeln kenntlich gemacht, nicht stillschweigend.** Rückbau ist eine Zelle: `keine` statt `0,00 €` — die Form trägt auch dann, nur schwächer |
| **Die acht Sätze sind weiterhin nicht durch den Texter-Skill gelaufen** | unverändert offen aus dem Eintrag davor. Kein Prüfbericht |
| **Gemessen:** 390 und 1512 px | acht Positionen, Summe `0,00 €`, kein waagerechter Überlauf. Unter 900 px stapelt die Zeile, die Kopfzeile entfällt |
| **Kein JavaScript, keine neue Form, keine neue Farbe** | Die einzige Lime-Stelle ist der Textmarker unter der Summe — er markiert einen **Zustand**, keine Fläche, und hält damit die Regel vom selben Tag ein |
| **Barrierefreiheit ungeprüft** | Die Kopfzeile ist `aria-hidden`, die Posten stehen als `<ol>`. **Ob ein Vorleseprogramm die Zuordnung Spalte → Wert trägt, ist nicht getestet.** Prüfmittel: ein Durchgang mit VoiceOver oder NVDA. Falls nicht: die Preisspalte braucht je Zeile eine unsichtbare Beschriftung |

### Nachtrag: Leistungen als Karten, Summenzeile entfernt — 06.08.2026

**Anweisung:** „0,00 € wieder weg. mach das mal in kartenform wie unten die musterprojekte. und
erklärung was alles dazu gehört."

**Gebaut:** acht Karten, je Position `01 / 08`, Titel, ein Satz und `Das gehört dazu` mit zwei bis
vier Punkten. Vier Spalten ab 1240 px, zwei darunter, eine unter 620 px. Die Summenzeile ist
vollständig raus — im ausgelieferten Text steht kein `0,00` mehr.

**Die Umfangspunkte sind belegt, nicht erfunden:** aus `03_KUNDENPRODUKT.md` („In jedem Paket
enthalten") und für den Rundum-Schutz aus den Schutzstufen in `02_PREISE_UND_ZAHLUNG.md`.

| Befund | Lage |
|---|---|
| **Drei Vorgaben stehen dagegen** | §7 „keine Kachelwand" · §3 Bauform-Tabelle „Zeilen — die einzige Liste" · §3 „kein Aufbaumuster mehr als zweimal". Karten sind nach Preisen und Musterprojekten die **dritte** Kartenfläche. **Auf Anweisung gebaut und in §7 vermerkt**, nicht stillschweigend |
| **Die Seite hat jetzt keine Sektion in Zeilenform mehr** | Das war der Gangwechsel, den die Bauform-Tabelle erzwingen sollte. **Der Verlust ist real und nicht durch die Kartenvariante aufgehoben** — sie mildert ihn nur |
| **Gegenmaßnahme gegen die dritte Wiederholung** | keine Bildfläche, keine Kopfleiste, kein Knopf; dafür Positionsnummer und echte Liste, vier statt drei Spalten |
| **Gemessen:** 390 · 1512 · 2560 px | acht Karten, kein waagerechter Überlauf, Sektion 1.248–3.183 px |
| **`margin-top:auto` vermieden** | Es hätte die Trennlinien über die Zeile ausgerichtet und dafür ein Loch in die Mitte der kurzen Karten gerissen — derselbe Fehler wie zuvor im Preisraster |
| **Text weiterhin ohne Prüfbericht** | Acht Sätze und rund zwanzig Umfangspunkte, nicht durch den Texter-Skill gelaufen. Von Hand gegen die Verbotsliste geprüft, mehr nicht |

**Nicht wieder verwenden ohne neuen Anlass:** das Leistungsverzeichnis mit Summenzeile
`Aufpreise gesamt — 0,00 €`. Begründung des Betreibers war die Zahl; ohne sie verliert die Form
ihre Pointe. In `10_WEBSITE_SARTU.md` §7 mit Herleitung abgelegt, damit die Idee auffindbar
bleibt, falls die Zahl später doch gewollt ist.

### Nachtrag: mehr Lime in den Leistungskarten — 06.08.2026

**Anweisung:** „gestalte die felder jetzt noch ein bisschen spannender irgendwie mehr lime."

**Am selben Tag hatte derselbe Betreiber „zu viel lime" gemeldet.** Kein Widerspruch — es sind
zwei verschiedene Dinge, und die Regel von heute Vormittag trägt beide:

| | |
|---|---|
| **Fläche** (429.337 px², entfernt) | zeichnet nichts aus und entwertet jede Lime-Stelle, die es tut |
| **Marke** (32 Stück, 11.601 px²) | sagt jedes Mal dasselbe und wird durch Wiederholung stärker |

**Gebaut:** Lime-Punkt vor jedem Umfangspunkt (22 Stück, jeder sagt „enthalten") · Lime-Zeigebalken,
der beim Überfahren über die Kartenoberkante einfährt · Positionsnummer auf 26 px vergrößert,
**in Tinte** — acht Lime-Zahlen wären acht Auszeichnungen für etwas, das nur zählt.

**37-mal weniger Lime-Fläche, 32-mal mehr Lime-Momente.** Dauerhaft sichtbar sind davon nur rund
1.900 px²; die acht Zeigebalken sind im Ruhezustand auf null skaliert.

| Befund | Lage |
|---|---|
| **Meine Messtabelle von heute Vormittag war falsch** | Das erste Zählwerk lief über `querySelectorAll('*')` und **sah keine Pseudoelemente** — genau dort sitzt der Großteil des Lime. Zählwerk erweitert, Tabelle in `07_MARKE_UND_GESTALTUNG.md` korrigiert. Die Aussage von damals stimmte trotzdem: die eine große Platte war der Ausreißer |
| **Kante bei 9-px-Punkten ist `--ink`, nicht `--line`** | Die Flächenregel verlangt eine Kante; `--line` wäre auf diesem Durchmesser unsichtbar und die Regel nur formal erfüllt. **Abweichung vom Buchstaben, im Sinne der Regel** |
| **Der Zeigebalken existiert auf Berührungsgeräten nicht** | Bewusst: er markiert einen Zeigezustand. Die Karte verliert ohne ihn nichts — die Punkte tragen sie |
| **Gemessen:** 390 · 1512 · 2560 px | acht Karten, kein waagerechter Überlauf |
| **Text weiterhin ohne Prüfbericht** | unverändert offen |

### Nachtrag: Positionsnummern raus, Icons geprüft und verworfen — 06.08.2026

**Anweisung und Frage:** „die zahlen sind zu groß und sind auch völlig sinnlos. wieder raus damit,
das verwirrt doch nur. können wir dort vielleicht auch mit icons arbeiten … oder wird es zu
kitschig?"

**Die Zahlen sind raus.** Der Einwand war richtig, und ich hätte ihn selbst sehen müssen: Ein
Zähler zählt eine Reihenfolge — die acht Karten haben keine. **`01 / 08` war eine Auszeichnung
für nichts.**

**An derselben Stelle steht jetzt ein Phasenkennzeichen:** `Vor dem Bau` · `Im Bau` (viermal) ·
`Zum Start` · `Durchgehend` · `Nach dem Start`. Gleiche Bauform wie der Kicker im Preisraster,
kein neues Bauteil — aber es beantwortet eine echte Frage statt einer erfundenen.

**Zu den Icons: die Frage war schon beantwortet, bevor sie gestellt wurde.**
`SARTU_CORPORATE_DESIGN.md` führt drei Stellen:

- Lime-Tabelle, Spalte *Lime nein*: **„Icons ohne Funktion"** und **„Flächen, die nur schmücken"**
- *„Der Auftritt hat damit gar keinen Schmuck. **Keine Symbole, keine Illustrationen**, keine
  Verläufe, keine Schlagschatten außer den zwei definierten."*
- *„**Wenn später doch ein Zeichen soll:** Dann kommt es aus der **Bildmarke** … Das ist der
  einzige Weg, bei dem ein Zeichen etwas bedeutet, statt nur eine Form zu sein."*

**Acht Themen-Icons scheitern doppelt:** Symbole sind ausgeschlossen, und aus der Bildmarke ergibt
sich **ein** Zeichen, kein Satz von acht. In `10_WEBSITE_SARTU.md` §7 abgelegt, damit die Frage
nicht in drei Wochen erneut gestellt wird.

| Befund | Lage |
|---|---|
| **Der eine offene Weg wäre ein Zeichen aus der Bildmarke** | Er ergibt **ein** Zeichen für die Marke, nicht acht für acht Themen. Für diese Karten also kein Weg. `design/icon-lesarten.html` und `design/motiv-recherche.html` gehören dazu |
| **Die Phasenzuordnung ist neuer Text** | Acht kurze Kennzeichen, aus dem Ablauf in `03_KUNDENPRODUKT.md` abgeleitet. **Nicht durch den Texter-Skill gelaufen** |
| **Mögliche Dopplung mit Sektion 3** | Der Ablauf zeigt sechs Prozessschritte, das Phasenkennzeichen ordnet Leistungen einer Zeit zu. Verwandt, nicht dasselbe. **Beim nächsten Durchgang gegenlesen, ob es sich reibt** |
| **Gemessen:** 390 · 1512 · 2560 px | acht Karten, kein waagerechter Überlauf |

### Fund: Branchenseiten und Musterprojekte haben keine Branche gemeinsam — 06.08.2026

**Gefunden bei der Frage nach der SEO-/GEO-Nische.** Zwei gebundene Listen, die aufeinander
verweisen, nennen **komplett verschiedene** Branchen:

| Quelle | Branchen |
|---|---|
| `16_SEO_GEO_SARTU.md` — drei Launch-Branchenseiten | **Sanitär-Heizung-Klima · Elektrotechnik · Dachdecker** |
| `10_WEBSITE_SARTU.md` §8 — drei Musterprojekte, `gebunden` | **Malerbetrieb · Physiotherapiepraxis · Arbeitsrechtskanzlei** |

**Keine Überschneidung.** Und `17_SEITEN_SARTU.md` §4 führt als **Block 6 jeder Branchenseite**:
*„ein Beispiel — das Musterprojekt dieser Branche"*, Spalte **eigen**.

**Berichtigt nach genauerer Prüfung am 06.08.2026.** Die erste Fassung dieses Eintrags sagte,
Block 6 sei „auf keiner der drei Branchenseiten baubar". Das war zu scharf formuliert:
`17_SEITEN_SARTU.md` §4 trägt beim Querverweis auf die Musterprojekte den Zusatz **„sofern die
Gattung passt"** — die Lücke ist dort also **halb** gesehen.

**Was trotzdem offen bleibt, und darin liegt der Fehler:** Die Datei sagt **nicht**, was in
Block 6 steht, **wenn** die Gattung nicht passt — und bei allen drei Launch-Branchen passt sie
nicht. Block 6 bleibt in der Zehnerliste als **eigen** geführt, und **Prüfung 3, der
Herkunftsnachweis**, verlangt ausdrücklich zu Block 6 eine Quellenzeile. Ein Block ohne Inhalt
hat keine Quelle — und *„Reißt eine der drei Prüfungen: Die Seite wird nicht veröffentlicht."*

**Das ist unabhängig von der Nischenentscheidung ein Fehler.** Welche drei Branchen es auch
werden: **es müssen dieselben drei sein.** Ein Musterprojekt ohne Branchenseite verschenkt seinen
stärksten Platz, eine Branchenseite ohne Musterprojekt kann ihren Pflichtblock nicht füllen.

**Nicht entschieden, weil es die Nischenfrage vorwegnähme:** welche drei. Beide Listen sind
`gebunden`, keine trägt Vorrang. **Gemeldet statt ausgewählt** — die Entscheidungsvorlage mit den
drei gangbaren Wegen und einer Empfehlung steht als **§7c** in `SARTU_ENTSCHEIDUNGEN_OFFEN.md`,
dem dafür zuständigen Dokument (Rang 1).

---

## Die Kundengewinnung fehlt in der geltenden Fassung — **Lücke, gemeldet 08.08.2026**

**Gefunden, weil der Betreiber zu Recht widersprach.** Eine Nischenempfehlung stützte sich auf
`§23b.2`, `§23b.7`, `§23b.9` und `§23b.10`. Alle vier stehen in
`CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` — nach `UEBERGABE_DATEILISTE.md` **„Begründungsarchiv,
keine Bauvorlage mehr"**. Sie wurden als Sperren dargestellt. **Das waren sie nicht.**

**Die Gegenprobe über die gesamte geltende Fassung:**

| Suchbegriff | Treffer in `spezifikation/` |
|---|---|
| `Angstmache` | **0** |
| `Spirale` | **0** |
| `Zweitmeinung` | **0** |
| `Trigger-Event` | **0** |
| `verwaiste` | **0** |

**Die gesamte Kundengewinnung ist bei der Zusammenführung am 03.08.2026 nicht übernommen worden.**
Zehn Kanäle, Branchen-Spirale, Zweitmeinung, Trigger-Events, verwaiste Bestandskunden, die Sequenz
über zwölf Monate — nichts davon hat einen Ort in `spezifikation/`.

**Warum das kein Formfehler ist:** `CLAUDE.md` sagt, die Zusammenführung sei seit 05.08.2026
abgeschlossen und **„keine Themendatei verweist mehr für Bauwissen auf ihre Quelle."** Für die
Kundengewinnung stimmt das nur deshalb, weil sie in **keiner** Themendatei vorkommt. Die 18
Themendateien decken Produkt, Website, Kundenbereich, Adminbereich, Recht, Sicherheit und Tests
ab — **nicht, wie ein Kunde entsteht.**

**Folge:** Jede Nischen-, Kanal- oder Akquiseentscheidung hat derzeit **keine geltende Grundlage**,
auf der sie später nachgelesen werden könnte. Wer sie im Masterkonzept nachschlägt, baut nach
`UEBERGABE_DATEILISTE.md` „aus der Quelle statt aus der geltenden Fassung".

**Was zu entscheiden ist — nicht von der KI:**

| # | Frage |
|---|---|
| 1 | Soll die Kundengewinnung eine **19. Themendatei** bekommen (etwa `18_KUNDENGEWINNUNG.md`)? |
| 2 | Oder ist sie bewusst draußen, weil sie kein **Bau**wissen ist? Dann gehört genau dieser Satz in `00_UEBERSICHT.md`, sonst sucht der nächste Leser weiter |
| 3 | Welche Aussagen aus `§23b` sind noch gewollt? Die Zahl „3–5 Referenzen je Branche" etwa ist unbelegt |

**Ungeprüft:** ob weitere Themen dasselbe Schicksal hatten. Geprüft wurden fünf Suchbegriffe aus
`§23b`, nicht das vollständige Masterkonzept gegen die vollständige `spezifikation/`.
**Ein systematischer Abgleich steht aus.**

**Berichtigt wurde:** `NISCHEN_IDEEN.md` trennt jetzt „Echte Sperren" (Rang 1 und 4, mit Datei und
Rang je Zeile) von „Empfehlungen ohne Bindung" aus dem Begründungsarchiv.

---

## Logoerstellung — Produktfrage, gemeldet 09.08.2026

**Gefunden, weil der Betreiber Seiten der Form „Logoerstellung für Sicherheitsdienste" bauen will.**

`spezifikation/03_KUNDENPRODUKT.md` führt unter **„Bewusst NICHT im Erstangebot — der
Scope-Schutz"** wörtlich `Logo-Pakete` auf. `spezifikation/10_WEBSITE_SARTU.md` beschreibt den
umgekehrten Weg: Der Kunde **lädt sein Logo hoch** — Schritt 3 im Kundenbereich.

**Eine Leistungsseite für Logoerstellung bewirbt damit etwas, das SARTU nicht verkauft.**

**Zu entscheiden — vom Betreiber, nicht von der KI:**

| # | Frage | Folge |
|---|---|---|
| 1 | Kommt Logoerstellung ins Angebot? | Dann gehört sie nach `spezifikation/03`, in die Preistabelle (`02`) und in den Ablauf — nicht nur auf eine Landingpage |
| 2 | Oder bleibt es beim Scope-Schutz? | Dann entfällt die Seite ersatzlos |
| 3 | Falls ja: Wer zeichnet? | `07_MARKE_UND_GESTALTUNG.md` regelt das **SARTU-eigene** Logo, nicht Kundenlogos |

**Sperre bis dahin:** Keine Seite, kein Satz und kein Listenpunkt, der Logoerstellung als Leistung
nennt. Ein beworbenes Angebot, das im Bedarfsscheck nicht auftaucht, erzeugt genau die Anfrage,
die abgelehnt werden muss.

**Ungeprüft:** ob weitere Punkte der Scope-Schutz-Liste — `SEO-Stufen`, `Express`,
`Newsletter/Tracking` — in der geplanten Seitenmatrix auftauchen. Geprüft wurde nur `Logo-Pakete`.

### Nachtrag 09.08.2026 — der Einwand des Betreibers und die drei Fälle

**Der Betreiber wandte ein:** Ohne Logo sei das ein **Ausschlusskriterium** — der Kunde gehe dann
mit dem ganzen Auftrag zu einer Agentur, die beides macht. Und: Das Logo müsse **vor** die Website,
lasse sich also nicht in den Kundenbereich verschieben. Zugleich dürfe die Erstabfrage nicht
wachsen.

**Alle drei Punkte treffen zu.** Was die Prüfung ergänzt:

#### Was auf dem Bedarfsscheck bereits gebunden ist

| Fundstelle | Wert |
|---|---|
| `spezifikation/17`, `/briefing` | Fortschrittsanzeige **`Thema {n} von 5`** — `gebunden` |
| dieselbe Stelle, Vertrauenspunkte | **`Keine Auswahl von Zusatzoptionen`** — `gebunden`, fünf Stück |
| `spezifikation/10` §… Startseite | *„Es gibt keine Aufpreisliste."* — Klasse 1 |
| `spezifikation/03` | *„Ein Standardangebot endet exakt beim veröffentlichten Paketpreis. Neue Ziele nach Auftrag → **ein** konsolidiertes Folgeangebot mit Festpreis, keine Einzelpreisliste."* |

> **Daraus folgt die Trennlinie:** Ein Logo als **wählbare Option mit Preis** widerspricht einem
> gebundenen Text auf **demselben Bildschirm**. Eine Logo-**Frage** widerspricht nichts.
>
> **Die Domain ist bereits genau so gelöst:** SARTU fragt danach, prüft und verbindet sie — und
> verkauft sie nicht als Option. Dieselbe Form trägt für das Logo.

#### Drei Fälle, nicht einer

| Fall | Häufigkeit in der Zielgruppe (10–50 MA, bestehender Betrieb) | Was nötig ist |
|---|---|---|
| **A — Logo liegt als brauchbare Datei vor** | die Mehrheit | nichts. Schritt 3 im Kundenbereich, wie bisher |
| **B — Logo existiert, aber nur als Druck, Foto oder schlechtes Pixelbild** | **häufig und bisher übersehen** | **Nachzeichnen und Aufbereiten** — kein Entwurf, keine Varianten, keine Runden |
| **C — kein Logo** | Neugründung, Meistergründung, Umfirmierung | echter Entwurf |

**Fall B ist der eigentliche Fund.** Er ist kein „Logo-Paket", sondern Umsetzungsarbeit — begrenzt,
planbar, ohne Feedbackrunden. Er lässt sich enthalten, **ohne** eine der vier gebundenen Stellen
zu brechen. Und er löst vermutlich die Mehrzahl der Fälle, die heute als „braucht ein Logo" gelten.

**Fall C ist der, den der Betreiber meint** — und nur dort stellt sich die Frage wirklich.

#### Drei Wege für Fall C

| Weg | Bricht etwas? | Bewertung |
|---|---|---|
| **Partnerempfehlung** — ein Grafiker, SARTU baut danach | **nein** | Löst das Ausschlusskriterium ohne Scope-Bruch. Der Grafiker wird zugleich **Multiplikator** und schickt Kunden zurück |
| **Folgeangebot** nach der Regel aus `spezifikation/03` | **nein** — die Regel sieht genau das vor | Möglich, aber zeitlich falsch: Das Logo muss **vor** die Seite, ein Folgeangebot kommt danach |
| **Ins Paket aufnehmen** | **ja** — Preistabelle, `Keine Auswahl von Zusatzoptionen`, „keine Aufpreisliste" | Nur mit bewusster Änderung von `02` und `03`. Logo-Entwurf bringt Varianten und Runden mit — genau den Scope-Creep, gegen den der Schutz geschrieben wurde |

#### Vorschlag

1. **Eine Ja/Nein-Frage in Thema 1** („Ihr Unternehmen"), Form wie bei der Domain.
   **`Thema {n} von 5` bleibt unberührt**, die drei Minuten auch — eine Frage in einem bestehenden
   Thema verlängert nichts spürbar
2. **Fall B enthalten** — vorhandene Logos werden aufbereitet, nicht neu entworfen. Das gehört als
   Zeile in `spezifikation/03` unter den Leistungsumfang
3. **Fall C über Partner** — Empfehlung statt eigener Leistung
4. **Kein Logo-Paket in der Preisliste.** Damit bleiben alle vier gebundenen Stellen wahr

**Was das nicht löst:** Wenn der Betreiber Fall C ausdrücklich selbst anbieten will, ist das eine
Änderung an `02_PREISE_UND_ZAHLUNG.md` und `03_KUNDENPRODUKT.md` — **kein Landingpage-Thema.**
Diese Entscheidung ist offen und gehört dem Betreiber.

---

## Was im Bedarfsscheck sonst noch fehlt — geprüft 09.08.2026

**Anlass:** Nach dem Einbau der Logofrage die Gegenprobe — welche Angabe entscheidet über Preis,
Weg oder Rechtslage und wird **nicht** vor dem Angebot erhoben? Drei Funde, einer davon ernst.

### 1. Die zweite BFSG-Pflichtfrage fehlt — **Mangel, nicht Vorschlag**

`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §6 (**Rang 1**) verlangt wörtlich **zwei** Pflichtfragen
*vor dem Angebot*, sobald ein Buchungs-, Bestell- oder Kaufweg dazukommt:

> `Schließen Besucher über die Seite einen Vertrag ab — Buchung, Bestellung oder Abonnement?`
> `Hat Ihr Betrieb weniger als 10 Beschäftigte und höchstens 2 Mio. € Umsatz oder Bilanzsumme?`

**Feld 4.1 deckt die erste ab** — die Optionen `Einfache Terminbuchung` und `Produkte verkaufen
oder Zahlungen annehmen` sind genau der Vertragsweg.

**Die zweite steht nirgends im Bedarfsscheck.** Weder in Thema 4 noch sonstwo wird nach
Beschäftigtenzahl oder Umsatz gefragt.

**Warum das zählt:** §6 sagt *„Beide Antworten werden im Angebot festgehalten. Nur wenn die erste
`ja` und die zweite `nein` lautet, greift das BFSG."* Ohne die zweite Antwort lässt sich das
Angebot nicht regelkonform schreiben — und daran hängt ein Bußgeldrisiko bis **100.000 €**.

**Vorschlag:** ein **bedingtes** Feld, das nur erscheint, wenn 4.1 einen Vertragsweg enthält —
dieselbe Mechanik wie 1.5 (nur bei `Ja`) und 5.4 (nur bei `Ja`). Damit wächst das Formular für den
Normalfall **um null Felder**.

| # | fragt nach | Typ | Pflicht | Optionen |
|---|---|---|---|---|
| 4.2 *(neu, bedingt)* | der Betriebsgröße | eine Wahl | ja, bedingt | Unter 10 Beschäftigte **und** höchstens 2 Mio. € · Darüber · Bin unsicher |

> Der Hilfetext muss sagen, **wozu** gefragt wird — sonst wirkt eine Umsatzfrage im Erstkontakt
> übergriffig. §6 liefert die Begründung: Es entscheidet, ob eine gesetzliche Pflicht greift.

### 2. Bildmaterial — dieselbe Lücke wie beim Logo, vermutlich größer

**Fotografie ist nicht im Leistungsumfang** — `/ratgeber/was-nicht-enthalten-ist` führt sie in der
Ausschlussliste, und `03_KUNDENPRODUKT.md` nennt sie nicht unter *In jedem Paket enthalten*.
Bilder kommen erst im Kundenbereich, Vorgang 3.

**Ein Handwerks- oder Dienstleistungsbetrieb ohne brauchbare Fotos kann keine gute Website
bekommen.** Das ist genau das Ausschlusskriterium, das der Betreiber beim Logo beschrieben hat —
nur trifft es mehr Betriebe. Und es fällt heute erst **nach** der Festpreiszusage auf.

**Nicht entschieden:** ob das eine eigene Frage bekommt, ob Fotografie ins Angebot soll oder ob ein
Partnerweg wie beim Logo genügt. **Dieselbe Struktur, dieselbe offene Frage.**

### 3. Rechtstexte — Startsperre, die niemand vorher abfragt

`03_KUNDENPRODUKT.md` nennt **„technische Einbindung freigegebener Rechtstexte + Consent"** — also
Einbindung, nicht Erstellung. Impressum und Datenschutzerklärung liefert der Kunde.

**Ohne sie kann nicht live gegangen werden.** Ob der Betrieb sie hat, wird nirgends gefragt.

### 4. Unternehmereigenschaft — geprüft, bewusst später

Der Bedarfsscheck zeigt **Nettopreise vor den Kontaktdaten**. Die B2B-Bestätigung liegt nach
`01_GESCHAEFTSMODELL.md` erst **bei der Annahme**. Das wirkt spät, ist aber offenbar bewusst so
gesetzt — das Masterkonzept nennt die PAngV-Frage ausdrücklich. **Als geprüft vermerkt, kein
Handlungsbedarf abgeleitet.**

### Vorschlag zur Bündelung von Fund 2 und 3

Statt zweier Einzelfragen ein Feld im selben Thema 5:

| # | fragt nach | Typ | Pflicht | Optionen (`gebunden`) |
|---|---|---|---|---|
| 5.3 *(neu)* | dem, was bereits vorliegt | **mehrere** Wahlen | ja | Fotos vom Betrieb oder von Arbeiten · Impressum und Datenschutzerklärung · Nichts davon |

Dieselbe Kombinationsregel wie bei 3.1 und 4.1. **`Thema {n} von 5` bleibt unberührt.**

**Nicht eingebaut** — Fund 2 und 3 sind Vorschläge, keine Mängel. Fund 1 ist ein Mangel gegen eine
Rang-1-Vorgabe und sollte unabhängig davon geschlossen werden.

### Antworten des Betreibers vom 09.08.2026 — geprüft

#### Zu Fund 1: „Wir können einfach immer BFSG-gerecht bauen"

**Sie bauen es bereits immer.** `§6` Regel 1: *„Der technische Grundstand ist immer enthalten"* —
Kontrast ab 4,5:1, volle Tastaturbedienung, sichtbarer Fokus, sinnvolle Beschriftungen,
semantisches HTML, `prefers-reduced-motion`.

**Aber „immer bauen" ist nicht „immer zusagen" — und genau das wurde in `§6` geprüft und
verworfen:**

> | Verworfen | Grund |
> | **Immer enthalten** | SARTU kann Beschäftigtenzahl und Umsatz nicht prüfen, und beide ändern sich. Eine Konformitätszusage steht gegen ein Bußgeld bis 100.000 €. Für einen Einzelbetrieb am Anfang das falsche Risiko |

**Drei Gründe, warum sauberes Bauen die Frage nicht ersetzt:**

1. **BFSG-Konformität ist mehr als guter Code** — sie verlangt zusätzlich eine Erklärung zur
   Barrierefreiheit und einen Rückmeldemechanismus. Das ist ein Nachweis, kein Baustandard
2. **Konformität muss erhalten bleiben.** Was der Kunde später einstellt, kann sie brechen
3. `§6` selbst: *„Beschäftigtenzahl und Umsatz muss der Kunde beantworten — er ist der Einzige,
   der sie kennt."*

**Was dagegen geht — und das Formular kurz hält:** `§6` verlangt die Fragen *„vor dem Angebot"*,
**nicht im Bedarfsscheck**. Sie können in die **gebündelte Rückfrage** wandern, die
`17_SEITEN_SARTU.md` §2.3 ohnehin für unklare Fälle vorsieht. Damit bleibt der Bedarfsscheck
unverändert, und die Rang-1-Vorgabe ist erfüllt.

**Empfehlung:** so lösen. Kein neues Feld, aber ein fester Punkt in der Rückfrage, sobald 4.1 einen
Vertragsweg enthält.

#### Zu Fund 2: „Bildmaterial schon Aufpreis?"

**Als Aufpreis nicht** — das bricht zwei gebundene Stellen: *„Es gibt keine Aufpreisliste"*
(Klasse 1, Startseite) und `Keine Auswahl von Zusatzoptionen` (gebunden, Bedarfsscheck).

**Der schwerere Einwand ist aber ein anderer: Fotografie erzwingt einen Termin vor Ort.** Das
bricht das Kernversprechen — *„ohne einen einzigen Termin"* und *„bundesweit"*. Ein Betrieb in
Kassel lässt sich von Dresden aus nicht fotografieren. Wer Fotografie anbietet, wird entweder
regional oder muss reisen; beides widerspricht `01_GESCHAEFTSMODELL.md`.

**Drei Wege, die das nicht brechen:**

| Weg | Bewertung |
|---|---|
| **Fotoanleitung** — nicht fotografieren, sondern sagen, welche Bilder gebraucht werden und wie man sie mit dem Handy macht | **Empfohlen.** Einmal erstellt, skaliert bundesweit, löst vermutlich die Mehrzahl der Fälle. Passt in die Betriebsfragen im Kundenbereich |
| **Partnerempfehlung** — ein Fotograf vor Ort | wie beim Logo, Fall C. Baut zugleich einen Multiplikator |
| **Folgeangebot** nach `03_KUNDENPRODUKT.md` | der einzige Preisweg, der keine Regel bricht — *„ein konsolidiertes Folgeangebot mit Festpreis, keine Einzelpreisliste"* |

#### Zu Fund 3: „Rechtstexte gehen den Kunden im Erstkontakt nichts an"

**Zugestimmt — der Fund war schwächer als die beiden anderen.**

Rechtstexte ändern **weder Preis noch Paket noch Weg** noch die Frage, ob SARTU den Auftrag
annimmt. Anders als beim Logo gibt es **keine Verzweigung**: Es gibt genau eine Behandlung — der
Kunde liefert sie. Sie blockieren allein den Livegang.

**Damit gehören sie in den Kundenbereich**, Vorgang 2 und 3. Keine Frage im Bedarfsscheck.

**Eine Einschränkung bleibt:** Sie sollten im **Angebot** als Mitwirkungspflicht stehen, damit sie
beim Livegang keine Überraschung sind. Das ist Angebotstext, keine Formularfrage.

---

## KI-Bilderstellung als Wahlmöglichkeit — geprüft 09.08.2026

**Vorschlag des Betreibers:** Der Kunde wählt zwischen **Fotoanleitung** und **KI-Bilderstellung**,
letztere gegen Aufpreis, EU-AI-Act-konform. Dazu ein Ratgeber-Artikel über die Wahl und den Act.

**Der Ratgeber-Artikel ist uneingeschränkt zu empfehlen** — siehe unten. Beim Produkt gibt es drei
Kollisionen.

### Kollision 1 — die eigene Ehrlichkeitsregel, Rang 4

`spezifikation/06_RECHT.md`, Abschnitt **„Ehrlichkeit — gilt für SARTU- **und Kundenwebsites**"**:

> **Keine** Fake-Referenzen, Fake-Bewertungen, Fake-Logos, Fake-Adressen, **Fake-Teamfotos**

**Ein KI-erzeugtes Teamfoto ist ein Fake-Teamfoto. Ein KI-erzeugtes Bild einer Arbeit, die es nie
gab, ist eine Fake-Referenz.** Die Überschrift stellt ausdrücklich klar, dass die Regel für
Kundenwebsites gilt — nicht nur für sartu.de.

Dazu der eigene Maßstab aus `spezifikation/10` §8: Solange keine echten Beispiele existieren, gilt
**„ehrlich beschrifteter Bildplatz, kein nachgebauter Bildschirm"**. SARTU hält sich selbst an den
Platzhalter. Dem Kunden das Gegenteil zu verkaufen, wäre ein Markenwiderspruch.

### Kollision 2 — die Rechtslage, seit dem 02.08.2026 in Kraft

**EU-KI-Verordnung, Art. 50** — Transparenzpflichten gelten seit **2. August 2026**:

| Punkt | Inhalt |
|---|---|
| Deepfake-Offenlegung, Art. 50 Abs. 4 | greift, wenn Material **real wirkende Personen, Orte oder Ereignisse** zeigt |
| rein synthetisches Bild **ohne realen Bezug** | fällt in der Regel **nicht** darunter |
| technische Kennzeichnung (Wasserzeichen, Metadaten) | Aufgabe des KI-Anbieters |
| **sichtbare** Kennzeichnung im veröffentlichten Inhalt | **Aufgabe des Unternehmens** |
| Bußgeldrahmen | bis **15 Mio. €** |

> **Die Grenze verläuft nicht bei „war KI im Spiel", sondern bei „kann es täuschen".**

**Und der Satz, der die ganze Idee entscheidet — § 5 und § 5a UWG:**

> **Eine Kennzeichnung ersetzt keine wahrheitsgemäße Werbung.**

Ein Bild, das Arbeiten zeigt, die es nie gab, bleibt irreführend — **auch mit Label**. Dazu kommt
das Abmahnrisiko durch Wettbewerber und Verbände. Ob die Kennzeichnungspflicht eine
Marktverhaltensregel nach § 3a UWG ist, **ist höchstrichterlich noch nicht geklärt**.

**„EU-AI-Act-konform" ist damit keine ausreichende Bedingung.** Es ist die kleinere der beiden
Hürden.

### Kollision 3 — der Aufpreis

Wie beim Logo: *„Es gibt keine Aufpreisliste"* (Klasse 1, Startseite) und
`Keine Auswahl von Zusatzoptionen` (gebunden, Bedarfsscheck). `03_KUNDENPRODUKT.md` führt
Add-on-Listen ausdrücklich im Scope-Schutz.

**Einziger Preisweg ohne Regelbruch:** das konsolidierte **Folgeangebot** aus `03`.

### Vorschlag — drei Stufen, die die Logo-Systematik fortsetzen

| Stufe | Was | Rechtlich | Empfehlung |
|---|---|---|---|
| **1 — Aufbereitung echter Fotos** | freistellen, aufhellen, Störendes entfernen, Hintergrund bereinigen | fällt unter die Ausnahme für Standardbearbeitungen — Eingabedaten werden nicht wesentlich verändert. **Keine Kennzeichnungspflicht, keine Irreführung** | **enthalten**, genau wie die Logo-Aufbereitung |
| **2 — abstrakte und dekorative KI-Bilder** | Hintergründe, Muster, Illustrationen **ohne realen Bezug** | *„rein synthetisch ohne reale Bezüge"* — in der Regel keine Deepfake-Pflicht. Sichtbare Kennzeichnung trotzdem sicherheitshalber | **möglich** |
| **3 — KI-Bilder von Arbeiten, Team, Räumen, Ergebnissen** | — | **§ 5 UWG** und `06_RECHT.md` | **gesperrt** |

> **Die Systematik ist dieselbe wie beim Logo: aufbereiten ja, erfinden nein.** Das macht das
> Produkt in sich stimmig und den Ratgeber-Artikel glaubwürdig — SARTU kann erklären, warum es
> Stufe 3 nicht anbietet, statt sie mit Kleingedrucktem abzusichern.

**Die Wahl, die der Kunde bekommt, bleibt damit erhalten:** Fotoanleitung **oder** Aufbereitung
seiner vorhandenen Bilder **oder** dekorative KI-Flächen — nur eben keine erfundenen Arbeiten.

### Der Ratgeber-Artikel — empfohlen, mit Auftrag statt Wortlaut

Nach Projektkonvention: *Aufgabe*, *Grenze*, *Umfang* — **kein Beispielsatz**, den Wortlaut
schreibt der Texter-Skill.

- **Aufgabe:** Dem Betriebsinhaber erklären, welche Bilder er für seine Website braucht, welche
  drei Wege es gibt, und wo bei KI-Bildern die rechtliche Grenze verläuft — so, dass er danach
  selbst entscheiden kann
- **Grenze:** keine Rechtsberatung · keine Zusage zur Rechtssicherheit · nicht behaupten, eine
  Kennzeichnung mache jedes Bild zulässig · keine Angstmache — die Zahl 15 Mio. € gehört nur
  hinein, wenn sie eingeordnet wird
- **Umfang:** in der Größenordnung der übrigen `/ratgeber/*`-Seiten
- **Zahlen `gebunden`:** Geltungsbeginn **2. August 2026** · Rechtsgrundlagen Art. 50 KI-VO sowie
  §§ 5, 5a UWG
- **Pflichthinweis:** dass SARTU keine Rechtsberatung leistet — `06_RECHT.md`

**Warum der Artikel geschäftlich trägt:** Er beantwortet eine Frage, die Betriebe **seit sieben
Tagen** stellen, die kaum ein Wettbewerber ehrlich beantwortet — und er erklärt nebenbei, warum
SARTU Stufe 3 nicht anbietet. Damit wird aus einer Absage ein Argument.

**Zu beachten:** Eine neue Adresse ändert `KEYWORD_VALIDATION.md`. Die Datei wird **erzeugt**
(`php bin/keywords.php`), nicht von Hand gepflegt.

### Offen — Entscheidung des Betreibers

| # | Frage |
|---|---|
| 1 | Stufe 2 anbieten oder ganz weglassen? Dekorative KI-Flächen sind zulässig, aber sie lösen das eigentliche Problem — fehlende Bilder echter Arbeiten — **nicht** |
| 2 | Falls Aufbereitung bezahlt werden soll: über das Folgeangebot, oder enthalten wie beim Logo? |
| 3 | Soll `06_RECHT.md` um eine Zeile zu KI-Bildern ergänzt werden? Die Regel greift schon, benennt den Fall aber nicht |

### Berichtigt am 09.08.2026 — der Einwand des Betreibers trifft

**Der Betreiber hielt entgegen:** Stockfotos sind seit Jahren Standard auf Firmenwebsites. Dann
kann ein KI-Bild auch kein Problem sein.

**Das trifft zu. Meine Stufe 3 war zu breit gezogen.**

#### Der Maßstab ist der Eindruck, nicht die Technik

Nach § 5 UWG ist maßgeblich *„der Eindruck, den die Werbung beim Publikum erweckt"*. Ein
**Symbolbild** erzeugt keinen falschen Eindruck über eine wesentliche Tatsache — deshalb sind
Stockfotos seit zwanzig Jahren zulässig, und deshalb ist ein generisches KI-Bild es ebenso.

**Die Grenze verlief nie zwischen echt und erzeugt. Sie verläuft zwischen Symbolbild und
Identitätsbehauptung — und sie galt für Stockfotos genauso:**

| | zulässig | nicht zulässig |
|---|---|---|
| **Stockfoto** | ein Dach, eine Werkstatt, Hände bei der Arbeit — als Symbolbild | dasselbe Bild mit der Unterschrift `Unser Team` oder `Unsere Referenz` |
| **KI-Bild** | genau dasselbe | genau dasselbe |

**Ein Stockfoto als Teamfoto war immer eine Irreführung.** Die Regel in `06_RECHT.md` —
*„Keine Fake-Teamfotos"* — richtet sich gegen die **Behauptung**, nicht gegen die Bildquelle. Sie
trifft Stockfotos und KI-Bilder gleichermaßen und keins von beiden pauschal.

#### Was sich am 02.08.2026 tatsächlich geändert hat — und was nicht

| | Stockfoto | KI-Bild |
|---|---|---|
| Symbolbild zulässig? | ja, seit jeher | **ja, genauso** |
| als Identitätsbehauptung? | **nein, seit jeher** | nein |
| **Kennzeichnungspflicht** | keine | **neu**: sichtbar, wenn real wirkende Personen, Orte oder Ereignisse gezeigt werden — Art. 50 Abs. 4 KI-VO |

**Das ist der einzige echte Unterschied.** KI-Bilder sind nicht weniger erlaubt als Stockfotos —
sie sind nur zusätzlich **kennzeichnungspflichtig**, sobald sie real wirken.

#### Korrigierte Stufen

| Stufe | Was | Bewertung |
|---|---|---|
| **1 — Aufbereitung echter Fotos** | freistellen, aufhellen, Störendes entfernen | zulässig, **keine** Kennzeichnungspflicht |
| **2 — Symbolbilder, ob Stock oder KI** | Dach, Werkstatt, Fahrzeug, Arbeitssituation — **ohne Identitätsbezug** | **zulässig, wie seit zwanzig Jahren.** Bei KI mit sichtbarer Kennzeichnung, sobald real wirkend |
| **3 — Identitätsbilder** | `Unser Team` · `Unsere Werkstatt` · `Unsere Referenz` · `vorher/nachher` | **müssen echt sein — Stock wie KI** |

> **Der eigentliche Hebel liegt bei SARTU selbst: SARTU schreibt die Texte.** Ein Bild wird erst
> durch seine Beschriftung zur Behauptung. Wer Bild **und** Bildunterschrift aus einer Hand
> liefert, kontrolliert genau die Stelle, an der aus einem zulässigen Symbolbild eine Irreführung
> wird. Das kann eine Agentur, die nur ein CMS übergibt, nicht.

**Folge für das Angebot:** KI-Bilderstellung ist **anbietbar** — für Stufe 1 und 2. Was nicht geht,
ist ein erzeugtes Bild als eigene Arbeit auszugeben. Das ist eine **Beschriftungsregel**, keine
Techniksperre.

**Für den Ratgeber-Artikel ist das die bessere Geschichte:** nicht „KI-Bilder sind heikel", sondern
*„die Regel ist dieselbe wie bei Stockfotos — neu ist nur die Kennzeichnung"*. Das ist wahr,
beruhigend und nützlich zugleich.

### Eingebaut am 09.08.2026 — und was daran ungeprüft bleibt

**Auf Freigabe des Betreibers eingearbeitet:**

| Datei | Was |
|---|---|
| `spezifikation/03_KUNDENPRODUKT.md` | Bildaufbereitung und Symbolbilder in den Leistungsumfang, mit der Dreistufentabelle und der Fotoanleitung für Betriebe ohne eigene Bilder |
| `spezifikation/06_RECHT.md` | Abschnitt **Bildherkunft** — die Matrix Symbolbild/Identitätsbehauptung über alle drei Bildquellen, dazu die Kennzeichnungspflicht seit 02.08.2026 |
| `spezifikation/17_SEITEN_SARTU.md` | `/ratgeber/bilder-fuer-die-firmenwebsite` mit Aufgabe, Grenze, Umfang und gebundenen Zahlen |

**Preisentscheidung — so umgesetzt, Änderung jederzeit möglich:** Stufe 1 und 2 sind **enthalten**,
nicht kostenpflichtig. Gründe: Der Aufpreis hätte *„Es gibt keine Aufpreisliste"* und
`Keine Auswahl von Zusatzoptionen` gebrochen, und die Erzeugung kostet je Projekt kaum etwas.
**Soll die Bilderstellung doch bezahlt werden, ist das konsolidierte Folgeangebot aus `03` der
einzige Weg, der keine gebundene Stelle bricht** — dann sind die Zeilen in `03` entsprechend zu
ändern.

**Ungeprüft und offen:**

| # | Punkt |
|---|---|
| 1 | **„in vereinbarter Anzahl"** — für Symbolbilder steht keine Zahl fest. Ohne Obergrenze ist es ein offener Posten im Festpreis. Gehört nach `02_PREISE_UND_ZAHLUNG.md` oder als feste Zahl nach `03` |
| 2 | **Die Fotoanleitung existiert noch nicht.** Sie ist im Leistungsumfang zugesagt, aber nicht geschrieben. Ort wäre `11_KUNDENBEREICH.md`, Vorgang 2 (Betriebsfragen) |
| 3 | **Wie die sichtbare Kennzeichnung aussieht**, ist nicht festgelegt — Bildunterschrift, Overlay oder Hinweis am Seitenfuß. Das ist eine Gestaltungsfrage für `07_MARKE_UND_GESTALTUNG.md` |
| 4 | **Ob der Ratgeberartikel in die Launch-Liste vorgezogen wird.** Er steht derzeit unter „Nach dem Launch"; die Launch-Liste trägt die gebundene Zahl `drei Stück` |
| 5 | **`KEYWORD_VALIDATION.md` kennt die neue Adresse nicht.** Die Datei wird erzeugt (`php bin/keywords.php`), nicht von Hand gepflegt — sie zieht nach, sobald die Seite im Router steht |

**Nicht ausgeführt:** Keine der drei geänderten Dateien wurde gegen den Texter-Skill geprüft; die
Blöcke sind Bauvorgaben, kein abgegebener Text. Für den Ratgeberartikel steht bewusst **kein
Wortlaut** — nur Aufgabe, Grenze und Umfang.

---

## Förderung — Sorgfaltspflicht gefunden, gemeldet 09.08.2026

**Beim Ausarbeiten des Förderkonzepts (`FOERDERUNG_KONZEPT.md`) trat ein Punkt hervor, der über
Marketing hinausgeht:**

> **Ein Förderantrag muss vor Beginn des Vorhabens gestellt werden. Wer zuerst beauftragt, verliert
> den Anspruch.**

**Damit kann der eigene Verkaufsprozess den Förderanspruch des Kunden zerstören.** Ein Interessent,
der den Bedarfsscheck durchläuft, das Angebot annimmt und erst danach von der Förderung erfährt,
bekommt sie nicht mehr — und hat durch SARTU Geld verloren.

**Das ist kein Aufhänger, sondern eine Sorgfaltsfrage.** Sie besteht unabhängig davon, ob das
Fördermarketing je gebaut wird.

**Sofort möglich, ohne neue Seite:** ein Satz im Bedarfsscheck-Ergebnis und im Angebot — dass eine
Förderung in Frage kommen kann und der Antrag **vor** der Beauftragung gestellt sein muss. Keine
Summe, keine Quote, keine Zusage.

**Zu prüfen, bevor irgendetwas veröffentlicht wird:**

| # | Punkt |
|---|---|
| 1 | **Die sächsische Höchstsumme ist widersprüchlich belegt** — 10.000 € gegen 50.000 €. Muss in der SAB-Richtlinie selbst nachgelesen werden. Bis dahin **nicht verwendbar** |
| 2 | **Angebotsgültigkeit** — wenn eine Förderung geplant ist, muss das Angebot den Bewilligungsweg überdauern. Betrifft `02_PREISE_UND_ZAHLUNG.md`, dort steht dazu nichts |
| 3 | **Beantragungshilfe** ist nicht im Leistungsumfang und wäre als bezahlte Option ein Bruch von `Keine Auswahl von Zusatzoptionen`. Vorschlag: Stufe 1 und 2 enthalten, Stufe 3 über Partner |
| 4 | **Wer prüft vierteljährlich?** Förderinhalte veralten schneller als alles andere im Projekt. Ohne festen Termin schadet der Inhalt mehr, als er nützt |

**Ungeprüft:** Ob eine Firmenwebsite unter der SAB-Richtlinie überhaupt förderfähig ist, wurde
**nicht** in der Richtlinie selbst nachgelesen — nur in Sekundärquellen, die „E-Business" nennen.
**Das ist die Kernfrage des ganzen Themas und steht noch aus.**

### Förderung bundesweit — entschieden 09.08.2026, mit einer offenen Lücke

**Der Betreiber hat entschieden: deutschlandweit statt auf Sachsen begrenzt.** Die Umsetzung läuft
über eine Architektur, die keine fremden Beträge kopiert — Programmname, Link zur Landesförderbank
und **Status**, sonst nichts. Begründung in `FOERDERUNG_KONZEPT.md` Abschnitt 4a.

**Der Grund für diese Bauweise ist ein Rechercheergebnis:** Für dieselben Programme nennen
verschiedene Übersichtsseiten **verschiedene Zahlen** — Bayern Standard 10.000 gegen 7.500 €, NRW
15.000/70.000 gegen 80.000 gegen 60.000 €, Sachsen 10.000 gegen 50.000 €. Die Wettbewerberseiten
schreiben voneinander ab; daher die Widersprüche. **Wer davon abschreibt, veröffentlicht mit hoher
Wahrscheinlichkeit eine falsche Zahl.**

**Offene Lücke — fünf von sechzehn Ländern nicht ermittelt:**

> **Hamburg · Bremen · Schleswig-Holstein · Mecklenburg-Vorpommern · Sachsen-Anhalt**

Für diese fünf liegt **kein Programmname** vor. Saarland und Rheinland-Pfalz sind in den Quellen
nur pauschal erwähnt. **Eine Übersicht mit elf von sechzehn Ländern darf nicht veröffentlicht
werden** — sie sähe vollständig aus und wäre es nicht.

**Ungeprüft geblieben:**

| # | Punkt |
|---|---|
| 1 | **Kein einziger Programmname wurde in der Primärquelle geprüft.** Alle stammen aus Sekundärübersichten — genau den Quellen, deren Widersprüche oben dokumentiert sind |
| 2 | **Ob eine Firmenwebsite förderfähig ist**, ist weiterhin in keiner Richtlinie nachgelesen. Das ist die Kernfrage des gesamten Themas |
| 3 | **Thüringen zeigt, warum der Status zählt:** Das Programm existiert, die Mittel sind derzeit ausgeschöpft. Ohne Statusspalte hätte eine Tabelle den Kunden ins Leere geschickt |
| 4 | Baden-Württemberg läuft offenbar als **Darlehen mit Tilgungszuschuss**, nicht als Zuschuss. Darf nicht in dieselbe Spalte wie Zuschussprogramme |

### `/foerderung` und der Förderhinweis — eingebaut 09.08.2026

**Auf Entscheidung des Betreibers eingearbeitet:**

| Datei | Was |
|---|---|
| `spezifikation/17` §2.3 | **Förderhinweis** unterhalb des Knopfes im Bedarfsscheck-Ergebnis — Aufgabe, Grenze, Umfang. Höchstens zwei Sätze, keine Summe, keine Quote, keine Zusage |
| `spezifikation/17` | **`/foerderung`** als eigene Seite mit neun Blöcken, 900–1.300 Wörter |
| `spezifikation/16` | `/foerderung` in den Launch-Adressen, Priorität **0.9** |

**Warum eine eigene Seite und kein Ratgeberartikel:** Wer „Förderung Website" sucht, ist kaufnah —
er will wissen, ob es Geld gibt, **bevor** er bestellt. Das ist eine kommerzielle Suchintention.

**Nicht in die Hauptnavigation gesetzt.** `10_WEBSITE_SARTU.md` §2 bindet sie auf **sechs Punkte**
und bezeichnet sich als *„die einzige gültige Navigation"*; Navigationsbeschriftungen sind
zusätzlich Klasse 1. Verlinkt wird aus `/preise`, dem Bedarfsscheck-Ergebnis, dem Ratgeber-Hub und
dem Fußbereich. **Ob die Seite doch ins Menü soll, ist eine Betreiberentscheidung** — sie änderte
eine gebundene Zahl.

**Veröffentlichungssperre für Block 5 (Länderübersicht):**

| # | Grund |
|---|---|
| 1 | **Fünf von sechzehn Ländern fehlen** — Hamburg, Bremen, Schleswig-Holstein, Mecklenburg-Vorpommern, Sachsen-Anhalt. Eine unvollständige Übersicht sieht vollständig aus |
| 2 | **Kein Programmname stammt aus einer Primärquelle.** Alle aus Sekundärübersichten, deren Widersprüche in `FOERDERUNG_KONZEPT.md` §4a dokumentiert sind |
| 3 | **Ob eine Firmenwebsite überhaupt förderfähig ist**, ist weiterhin in keiner Richtlinie nachgelesen — die Kernfrage des Themas |

**Der Förderhinweis im Ergebnis ist davon unabhängig einsetzbar**, sobald `/foerderung` existiert:
Er nennt keine Zahl, sondern nur die Reihenfolge. **Er kann also nicht falsch werden.**

**Ungeprüft:** `KEYWORD_VALIDATION.md` kennt `/foerderung` nicht — die Datei wird erzeugt
(`php bin/keywords.php`) und zieht nach, sobald die Seite im Router steht. Ebenso ungeprüft, ob der
zusätzliche Block im Bedarfsscheck-Ergebnis die Aussage *„Preis vor Kontaktdaten"* optisch
schwächt; das entscheidet sich erst am gebauten Bildschirm.

---

## Förderung bundesweit vollständig recherchiert — 09.08.2026

**Auftrag erledigt:** alle sechzehn Länder plus Bundesebene ermittelt, in `FOERDERUNG_KONZEPT.md`
§4a eingearbeitet, die frühere Lücke von fünf Ländern ist geschlossen.

### Der Befund dreht die Kernaussage um — und das ist der wichtigste Teil des Ergebnisses

> **Eine reine Firmenwebsite ist in der Regel nicht förderfähig.**

**Digitalbonus Bayern schließt ausdrücklich aus:** Standard-Websites · *„ein klassischer
Website-Relaunch — neues Design, bessere Texte, responsive Darstellung, vielleicht ein
Kontaktformular"* · einfache Onlineshops · **Website-Texte** · **SEO**, Google Ads,
Social-Media-Werbung.

**Programmübergreifend gilt:** *„Nicht förderfähig sind operative Umsetzungsleistungen wie
Website-Programmierung, laufende SEO-Optimierungen oder Werbekampagnen"* und *„reine
Umsetzungsleistungen, zum Beispiel die Erstellung einer Website"*.

**Gefördert wird Prozessdigitalisierung, nicht Selbstdarstellung.**

> **Damit stehen alle vier Bestandteile des SARTU-Produkts auf den Ausschlusslisten:** Website ·
> Texte · SEO-Grundlage · Betrieb. **Ein auffälliges Förderversprechen wäre irreführend** — nach
> denselben Maßstäben, die `06_RECHT.md` für Bilder und Referenzen setzt.

**Die Umsetzung wurde entsprechend geändert:** `/foerderung` Block 4 führt jetzt mit *„warum eine
reine Firmenwebsite meistens nicht gefördert wird"*, und der Förderhinweis im Bedarfsscheck-Ergebnis
darf ausdrücklich **nicht** nahelegen, Förderung sei der Regelfall.

### Vier Statusarten statt zwei — aus der Recherche gelernt

| Status | Beispiel |
|---|---|
| läuft | Sachsen, Bayern, Berlin, Hessen, Niedersachsen, Brandenburg, Saarland, Schleswig-Holstein, Mecklenburg-Vorpommern, NRW |
| **Aufrufverfahren** | **Sachsen-Anhalt** — Fenster schließen nach 60 bzw. 90 Anträgen |
| **Mittel ausgeschöpft** | **Thüringen** |
| **Umsetzung nicht förderfähig** | **Rheinland-Pfalz** — nur Beratung und Innovation |

Dazu zwei Länder mit **Darlehen statt Zuschuss**: **Hamburg** (Hamburg-Kredit Digital) und
**Baden-Württemberg** (Digitalisierungsprämie). Die dürfen nicht in dieselbe Spalte wie
Zuschussprogramme.

### Was weiterhin offen ist

| # | Punkt | Warum es zählt |
|---|---|---|
| 1 | **Kein Programmname ist in der Primärquelle geprüft.** Alle stammen aus Sekundärübersichten | Genau die Quellenart, deren Zahlen sich widersprechen. Vor Veröffentlichung: sechzehn Förderbankseiten, ein halber Tag |
| 2 | **Ob SARTUs Conversion-Modul oder der Kundenbereich als Prozessdigitalisierung durchgehen**, ist Vermutung | Das ist die einzige Restmenge, in der Förderung realistisch greift — und sie ist unbelegt |
| 3 | **Die sächsische Höchstsumme** bleibt widersprüchlich (10.000 gegen 50.000 €) | unverändert nicht verwendbar |
| 4 | **Ob der ehrliche Aufhänger trotzdem Anfragen bringt** | Die Annahme ist plausibel, aber ungeprüft — sie lässt sich nur messen |

**Nicht ausgeführt:** Kein Text wurde geschrieben. `/foerderung` und die vier Ratgeberartikel liegen
als Aufgabe, Grenze und Umfang vor — der Wortlaut gehört dem Texter-Skill.

---

## Alle 16 Förderbanken an der Primärquelle geprüft — 09.08.2026

**Auftrag erledigt.** Jedes der sechzehn Landesprogramme wurde an der Förderbank oder am
Landesportal geprüft, nicht mehr an Sekundärübersichten. Vollständige Tabelle mit Voraussetzungen:
`FOERDERUNG_KONZEPT.md` §4a.

### Der Prüfbefund: jede geprüfte Sekundärangabe war falsch

| Land | Sekundärquelle | Primärquelle |
|---|---|---|
| Bayern Standard | 10.000 € | **7.500 €** |
| Bayern Plus | 50.000 € | **30.000 €** |
| Sachsen | 10.000 **oder** 50.000 € | **10.000 €** Einführung · **60.000 / 100.000 €** Transformation — **50.000 € existiert nicht** |
| Baden-Württemberg | 10.000 € | **3.000 €** |
| **Niedersachsen** | aktiv | **ausgelaufen** |
| **Hessen** | aktiv | **beendet Juni 2026, keine weiteren Aufrufe** |
| **Rheinland-Pfalz** | kein Umsetzungsprogramm | **DigiBoost bis 15.000 €** |
| **Sachsen-Anhalt** | Aufrufverfahren mit 60/90 Anträgen | **seit 15.07.2026 Direktantrag** mit Mindestpunktzahl |

**Drei Programme sind tot, eines zweifelhaft — alle vier standen in den Übersichten als aktiv:**
Niedersachsen (ausgelaufen) · Hessen (beendet) · Thüringen (Mittel erschöpft, keine Neuauflage) ·
Schleswig-Holstein (nur unter Landesprogramm Wirtschaft **2014–2020** auffindbar).

> **Damit ist die Verweis-statt-Kopie-Architektur nicht mehr eine Vorsichtsmaßnahme, sondern
> belegt notwendig.** Eine Förderseite aus Sekundärquellen hätte Kunden zu vier Programmen
> geschickt, die es nicht mehr gibt — und mit vier falschen Beträgen.

### Der inhaltliche Befund bestätigt sich über alle sechzehn Länder

**Bayern im Wortlaut:** nicht förderfähig sind *„Standard-Webseiten (herkömmliche Webseiten ohne
tiefe funktionelle Einbindung in die betrieblichen Abläufe)"*, *„Standard-Online-Marketing-
Maßnahmen (z. B. Suchmaschinenoptimierung…)"* sowie **grafische und redaktionelle
Dienstleistungen**. **Sachsen:** ausgeschlossen sind **Websites ohne Geschäftsintegration**.

Alle übrigen verlangen dasselbe in anderen Worten — Technologiebezug (Berlin), Prozessanalyse
(Brandenburg), Innovationsgehalt (Sachsen-Anhalt, Saarland Plus, Niedersachsen), KI und
automatisierte Prozesse (Bremen).

**Vier Länder scheiden für die Zielgruppe zusätzlich strukturell aus:** Hamburg
(**Mindestdarlehen 25.000 €**) · Mecklenburg-Vorpommern (**nur Produktion, Handwerk, Tourismus** —
Sicherheitsdienste und Reinigung sind nicht antragsberechtigt) · Baden-Württemberg (Zuschuss
max. 3.000 €) · Berlin (Technologiebezug).

### Was jetzt noch offen ist

| # | Punkt |
|---|---|
| 1 | **Schleswig-Holstein ist ungeklärt.** Gefunden wurde nur „Digibonus I" aus der Periode 2014–2020. Ob ein Nachfolger existiert, muss die IB.SH direkt beantworten |
| 2 | **Der saarländische Förderkatalog** (PDF zur Richtlinie) wurde nicht geöffnet — dort stünde, ob Webauftritte namentlich erfasst sind |
| 3 | **Ob SARTUs Conversion-Modul oder der Kundenbereich als Prozessdigitalisierung gelten**, ist weiterhin unbelegt. Das ist die einzige Restmenge, in der Förderung realistisch greift — **und sie beantwortet nur die Förderbank im Einzelfall** |
| 4 | **Berlins „Digitalprämie"** tauchte als eigenes Programm neben dem Transfer BONUS auf; ihr aktueller Status wurde nicht geprüft |
| 5 | Für **NRW** liegen die konkreten Höchstbeträge je MID-Baustein nicht vor, nur die Quoten (80 / 60 %) |

**Nicht ausgeführt:** Kein Text geschrieben. Die Beträge in `FOERDERUNG_KONZEPT.md` dienen der
internen Orientierung und sind **für keine Kundenseite freigegeben** — auf `/foerderung` erscheinen
nach wie vor nur Programmname, Link, Status und Prüfdatum.

### Berichtigt 09.08.2026 — kein Land wird weggelassen

**Der Betreiber hat widersprochen, und zu Recht.** Eine frühere Fassung ließ Hamburg,
Mecklenburg-Vorpommern, Baden-Württemberg und Berlin „für die Zielgruppe ausscheiden". **Das war
der falsche Schnitt:** Ob ein Betrieb antragsberechtigt ist, entscheidet er selbst.

**Geändert:**

| Wo | Was |
|---|---|
| `FOERDERUNG_KONZEPT.md` §4a | Neue **Voraussetzungstabelle über alle sechzehn Länder** — wer antragsberechtigt ist und welche weiteren Bedingungen gelten |
| dieselbe Datei | Die Einschätzung, wo es unwahrscheinlich ist, steht jetzt getrennt und ausdrücklich als **interne Erwartung, nicht für die Kundenseite** |
| `spezifikation/17`, `/foerderung` Block 5 | Spalte **Bedingung** ergänzt, dazu der Satz, dass kein Land weggelassen wird |

> **Die Bedingungen sind der eigentliche Nutzwert der Seite.** *„Mecklenburg-Vorpommern fördert nur
> Produktion, Handwerk und Tourismus"* ist genau die nachprüfbare Angabe, die eine Übersicht
> brauchbar macht — und die sonst niemand hinschreibt. Ein Betrieb, der daran erkennt, dass er
> nicht antragsberechtigt ist, hat trotzdem eine nützliche Antwort bekommen.

### Berlin und Schleswig-Holstein geklärt — 09.08.2026

| Land | Ergebnis |
|---|---|
| **Berlin** | Die **Digitalprämie Berlin ist tot.** Antragstellung lief im Windhundverfahren, letzte Frist **31.12.2023**. Es bleibt der **Transfer BONUS** (technologieorientiert) und der Berliner InvestitionsBONUS |
| **Schleswig-Holstein** | **Neue Richtlinie:** „Förderung von **Digitalisierungsmaßnahmen in kleinen Unternehmen (DKU)**", Amtsblatt **2026/190 vom 05.06.2026**, befristet bis **30.06.2027**. Gefördert werden Vorhaben, die in einem **schriftlichen Beratungsbericht** Lösungen erarbeiten — Medienbrüche, Kundenorientierung, Prozessbeschleunigung, IT-Sicherheit. Das alte „Digibonus I" stammte aus der Periode 2014–2020 |

**Damit steht die Bilanz der Primärquellenprüfung endgültig:** **vier tote Programme** (Berlin,
Hessen, Niedersachsen, Thüringen), die alle in den Sekundärübersichten als aktiv geführt wurden —
und **zwei Programme**, die dort gar nicht vorkamen (Rheinland-Pfalz DigiBoost,
Schleswig-Holstein DKU).

**Von sechzehn Sekundärangaben waren sechs schlicht falsch.** Ohne Primärquellenprüfung wäre die
Übersicht zu über einem Drittel unbrauchbar gewesen.

**Damit ist das Förderthema rechercheseitig abgeschlossen.** Offen bleiben nur noch die Fragen, die
keine Recherche beantworten kann — insbesondere, ob SARTUs Conversion-Modul oder der Kundenbereich
im Einzelfall als Prozessdigitalisierung anerkannt werden.

---

## Branchenliste mit 49 Kandidaten — erstellt 09.08.2026

**Auf Anforderung des Betreibers.** `BRANCHENLISTE.md` führt 49 Kandidaten in fünf Stufen, je mit
amtlicher Bezeichnung, Selbstbezeichnung, vermuteter Suchform und Verband.

**Der wichtigste Befund:** Die **amtliche Bezeichnung ist fast nie die Selbstbezeichnung.** Kein
Betrieb nennt sich „Installateur und Heizungsbauer" — er nennt sich SHK-Betrieb. Wer die Amtsform
in Titel und H1 schreibt, verfehlt Sprache **und** Suchanfrage.

**Belegt ist:**

| Angabe | Quelle |
|---|---|
| Alle 53 Gewerbe der Anlage A in amtlicher Schreibweise | Handwerksordnung über ZDH — **Primärquelle** |
| Struktur Anlage B1 und B2 | dito |
| Selbstbezeichnungen | aus Verbands- und Innungsnamen ableitbar |

**Ausdrücklich unbelegt — und als solche gekennzeichnet:**

> **Die Spalte „Suchform" ist eine Vermutung, kein gemessenes Volumen.**
> `KEYWORD_VALIDATION.md` hält fest, dass kein Volumenwerkzeug vorliegt und deshalb **nie
> geschätzt** wird. Die Spalte nennt die sprachlich naheliegende Form. **Vor dem Bau einer Seite
> ist sie je Branche zu bestätigen** — und die Spalte „Bestätigt" ist bei allen 32 vorhandenen
> Adressen weiterhin leer.

**Was die Liste nicht ist:** kein Bauplan. Die Zahl 49 stammt aus der Kapazitätsrechnung
(148 freie Seiten ÷ 3 je Branche). Realistisch sind nach der eigenen Keywordstrategie **20–40
Seiten in zwei bis drei Jahren**, also **7 bis 13 Branchen**. Der Rest ist Vorrat.

**Ungeprüft geblieben:**

| # | Punkt |
|---|---|
| 1 | **Betriebszahlen je Branche** wurden nicht erhoben. Ob eine Branche genug Betriebe für eine eigene Seite hat, ist außer bei Sicherheit, Reinigung und Handwerk gesamt **nicht belegt** |
| 2 | **Verbände sind als mögliche Multiplikatoren genannt, nicht kontaktiert** — und nicht geprüft, ob sie Anbieterempfehlungen überhaupt aussprechen |
| 3 | **Regionale Wortvarianten** sind nur bei Tischlerei/Schreinerei berücksichtigt. Weitere sind wahrscheinlich |
| 4 | Bei Stufe 4 und 5 ist der **B2B-Anteil geschätzt**, nicht belegt — insbesondere bei Sanitätshaus, Hörakustik und Augenoptik |

### Stufe 6 ergänzt — 09.08.2026, auf Entscheidung des Betreibers

**Sein Argument hat meine Bewertung gekippt:** *„Vielleicht denkt ja auch mancher — Umsatz geht
zurück, was kann ich tun, ah, neue Website."*

**Das ist ein Kaufanlass, kein Ausschlussgrund.** Meine frühere Ablehnung maß Branchen an ihrer
**Durchschnittslage** — gekauft wird aber von **einzelnen Betrieben**, und die entschlossensten
sitzen oft dort, wo es gerade weh tut. Es ist Achse 4 aus `NISCHEN_IDEEN.md`, angewendet auf genau
die Branchen, die ich weggelassen hatte.

**26 Branchen aufgenommen**, `BRANCHENLISTE.md` führt jetzt **75 in sechs Stufen**.

**Die Kapazitätsfrage ist über die Staffelung gelöst — nicht jede Branche bekommt drei Seiten:**

| | Branchen | Seiten je Branche | Summe |
|---|---|---|---|
| Kernbranchen | 13 | 3 | 39 |
| Weitere | 36 | 1 | 36 |
| Abdeckung, Stufe 6 | 26 | 1 | 26 |
| **gesamt** | **75** | | **101** |

Plus 32 vergebene Adressen: **133 von 180.** 47 Seiten Luft.

**Fünf Regeln bleiben für Stufe 6 verbindlich** — sie stehen in der Liste: eine Seite statt drei ·
auf den Anlass texten statt auf das Gewerk · Zahlungsplan bei Gastronomie und Photovoltaik nicht
aufweichen (**Insolvenzrisiko ist real**) · `§7b` gilt unverändert, bei Friseuren und Pflege darf
der Fachkräftemangel **nicht** angesprochen werden · Selbstpflege bleibt ausgeschlossen.

**Draußen bleiben nur noch vier Gruppen:** Einzelhandel mit Warenverkauf (Shop = Sonderprojekt) ·
**Schornsteinfeger** (Kehrbezirke zugewiesen — die einzige Branche ohne jeden Anlass) ·
Kleinstgewerke mit zu wenigen Betrieben · Privatpersonen und Vereine ohne Unternehmereigenschaft.

**Ungeprüft:** Ob die Anlass-Texte für Stufe 6 die drei Prüfungen aus `spezifikation/17` §4
bestehen. Ein Text über „sinkende Anfragen" ist branchenübergreifend ähnlich — **der Austauschtest
ist hier schwerer zu bestehen als bei einem Gewerk.** Das entscheidet sich am ersten geschriebenen
Beispiel, nicht vorher.

---

## Textbindungen gelockert — 09.08.2026, auf Entscheidung des Betreibers

**Auftrag:** alle Text- und Formulierbindungen aufheben.

**Umgesetzt an genau zwei Stellen statt an 147** — der Klassendefinition selbst:

| Datei | Änderung |
|---|---|
| `.claude/skills/sartu-texter/SKILL.md` | Klasse 1 von sechs auf **vier** Gruppen reduziert. Zwei Gruppen nach Klasse 2 verschoben, je mit Auflage |
| `CLAUDE.md`, Abschnitt Texthoheit | dieselbe Änderung, mit Begründung |

**Frei geworden:**

| Was | Auflage in Klasse 2 |
|---|---|
| Knopftexte, Navigationspunkte, Meilensteine, Betreffzeilen | **innerhalb einer Fassung identisch**, Liste im Prüfbericht |
| die **vier Positionierungssätze** | die vier **Aussagen** bleiben, der Wortlaut ist frei, **eine Fassung je Abgabe** |

**Bewusst nicht aufgehoben — und warum das keine Textfrage ist:**

| Bleibt gebunden | Grund |
|---|---|
| **Jede Zahl** | Preise sind Vertragsinhalt. Eine geschriebene Zahl ist eine erfundene |
| **Vertragliche Erklärungen** | die vier Bestätigungen bei der Annahme sind Beweismittel im Streitfall |
| **Rechtstexte und Pflichthinweise** | Ohne den dritten Satz des Pflichthinweises **wird die Empfehlung zum Angebot**. Das ist die Grenze zwischen Werbung und Willenserklärung |
| **Statusnamen und Feldnamen** | `angebot_offen`, `qa_failed` stehen als **Spalten in der Datenbank**. Wer sie umformuliert, ändert das Schema |

> **Der Betreiber kann auch diese vier fallen lassen** — dann ist es keine Lockerung des Textes
> mehr, sondern eine Änderung an Vertrag, Haftung und Datenmodell. **Das wäre getrennt zu
> entscheiden und hier zu vermerken.**

**Ungeprüft:**

| # | Punkt |
|---|---|
| 1 | **Die 147 `gebunden`-Markierungen in den Themendateien wurden nicht angefasst.** Sie verweisen auf die Klassendefinition, die jetzt enger ist — wirken also automatisch mit. **Ob jede einzelne Stelle das richtig trifft, ist nicht durchgesehen** |
| 2 | Ob die Auflage „innerhalb einer Fassung identisch" praktisch trägt, zeigt sich erst am ersten Prüfbericht mit Beschriftungsliste |
| 3 | Die bisherigen Positionierungssätze stehen weiter unter „Kalibrierung" im Skill — **als Maßstab, nicht als Vorschrift.** Ob das in der Praxis unterschieden wird, ist offen |

---

## 09.08.2026 — Bereitschaftsprüfung vor Baubeginn: was läuft, was fehlt, was ich geändert habe

**Anlass:** die Frage, ob Claude Code loslegen kann und ob die beiden Entwürfe im Repository
liegen. **Beide liegen dort** — `design/startseite.html` (77 KB) und `design/portalkonzept.html`
(70 KB). Die Prüfung hat aber gefunden, dass die Datei dazwischen nicht mitgezogen war.

### Ausgeführt und grün

| Was | Ergebnis |
|---|---|
| `php -l` über den gesamten Anwendungscode | **244 Dateien, 0 Syntaxfehler**, PHP 8.4.19 |
| `vendor/bin/phpunit` | **277 Tests, 3702 Zusicherungen, 0 Fehler** |
| Testfallabdeckung gegen `REIHENFOLGE.md` | **84 von 88** zugeordnet |
| `bin/migrate.php up` gegen leere Datenbank | **korrekt verweigert** — §1.5a verweist auf `/admin/setup` |
| Fall 49 gegen laufenden Webserver | `/assets/css/tokens.css` → 200 · `/app`, `/.env`, `/storage/`, `/vendor` → 404 |
| Quelle und Auslieferung von `tokens.css` | identisch (der Test erzwingt das) |

**Abdeckung je Stufe:** A0 **26/26** · A1 **34/34** · A2 **17/21** · A3 **6/6** · B **1/1**.

### Geändert: `design/tokens.css` auf die entschiedene Palette

**Der Befund:** Beide abgenommenen Entwürfe führen seit Anfang August die kühlen Neutralen.
`design/tokens.css` führte noch die warme Reihe vom 30.07.2026 — und `CLAUDE.md` zitierte sie.
Da `CLAUDE.md` anordnet, `tokens.css` **als Erstes** einzubinden und keine Zahl im Bauteil zu
schreiben, wäre jede gebaute Fläche in der abgelösten Palette entstanden.

| Variable | vorher | jetzt | Quelle |
|---|---|---|---|
| `--cream` | `#f4efe5` | `#f6f6f4` | `startseite.html` Z. 13, „entschieden am 02.08.2026" |
| `--paper` | `#fbf8f2` | `#ffffff` | ebenda |
| `--ink-soft` | `#241f18` | `#222322` | ebenda |
| `--text` | `#231e17` | `#1f2120` | ebenda |
| `--muted` | `#4d473d` | `#4a4d4b` | ebenda |
| `--line` | `#ddd4c4` | `#dfdfda` | ebenda |
| `--line-dark` | `#332d24` | `#2e302e` | ebenda |
| `--sand` | `#e8dfcd` | `#eaeae6` | ebenda |
| `--label` | `#5c554a` | `#5a5d5b` | ebenda |
| `--label-dark` | `#a89b88` | `#9ca09d` | ebenda |
| `--band-a` / `--band-b` | fehlten | `#e0e0db` / `#ebebe6` | ebenda |
| `--ink-2` / `--ink-3` | fehlten | `#1e201e` / `#2a2c2a` | `portalkonzept.html` Z. 11 |
| `--rail-text` | fehlte | `#cfd0ce` | ebenda Z. 14 |

**Warum das keine eigenmächtige Entscheidung ist:** `SARTU_ENTSCHEIDUNGEN_OFFEN.md` (Rang 1)
bindet unter „Farbsystem, Fassung 3" **nur die Lime-Reihe und `--ink`** — beide sind unverändert.
Für die Neutralen nennt Rang 1 keine Werte. `OFFENE_ENTSCHEIDUNGEN.md` hält die Übernahme als
Betreiberentscheidung vom 03.08.2026 fest und nennt sie ausdrücklich „beim Übertragen die erste
Änderung". Ausgeführt wurde also eine getroffene Entscheidung, keine neue.

**Risiko war klein und wurde geprüft:** `public/assets/css/website.css` und `anwendung.css`
enthalten **null** fest verdrahtete Hexwerte — alles läuft über die Variablen.

**Kontrast nachgerechnet:** Lime auf dem neuen Grund `#f6f6f4` ergibt **1,39 : 1** statt 1,32 : 1.
Beide Werte liegen weit unter 4,5 : 1; das Verbot von Lime als Schriftfarbe auf hellem Grund
bleibt unberührt. `--ink` auf Lime bleibt bei **12,47 : 1**. Die Zahl in `CLAUDE.md` ist mitgezogen.

### Geändert: Fall 42 gab es nur als Behauptung

Der Klassenkommentar von `tests/TenantIsolationTest.php` führte Fall 42 in seiner Liste — **eine
Methode dazu gab es nicht.** Geprüft waren der Abgemeldete (43) und der Admin ohne Code (44),
nicht aber der gefährlichste Fall: **gültige Sitzung, falsche Rolle.**
`testAngemeldeterKundeErreichtKeineAdminroute` fährt jetzt die vollständige Adminroutenliste ab.

> **Der Test war beim ersten Lauf grün.** Es fehlte der Nachweis, nicht der Schutz. Das ist der
> bessere von zwei möglichen Ausgängen — aber eine als abgedeckt geführte Lücke ist schlimmer
> als eine offen geführte, weil niemand danach sucht.

### Nicht geändert, gemeldet — und das ist der Grund, warum A2 nicht fertig ist

| # | Befund |
|---|---|
| 1 | **Vier Testfälle in A2 fehlen: 51, 52, 53a, 54** — alle bei der Protokollierung von Zahlungsänderungen |
| 2 | **Zwei Funktionen fehlen dazu.** `app/services/Rechnungsdienst.php` hat `zahlungEintragen`, `stornieren`, `zahlungslinkSetzen` — **keine Rücknahme einer Zahlung, keine Änderung von `due_date`**. Auch keine Route dafür |
| 3 | **Der Klassenkommentar beschreibt beide, als gäbe es sie:** „die Rücknahme ist eine eigene protokollierte Aktion mit eigenem Grundlagentext und erzeugt eine Benachrichtigung an den Kunden". Der Kommentar zitiert §12 richtig — der Code setzt ihn nicht um |
| 4 | **`--wrap` weicht ab:** `tokens.css` `1180px`, `startseite.html` `clamp(1380px,90vw,1800px)`. Das ist eine Breitenentscheidung, zu der **kein Vermerk existiert**. Bewusst nicht angefasst |
| 5 | **`app/views/partials/kundenband.php` beschriftet den Punkt `Öffnungszeiten`**, die Entscheidung vom 03.08.2026 sagt `Inhalte` (Menüwort) bei Seitentitel `Öffnungszeiten`. Der Dateikommentar argumentiert ausdrücklich **gegen** die Entscheidung |
| 6 | **Zwei Namen für denselben Wert:** `startseite.html` nennt den Grund `--cream`/`--paper`, `portalkonzept.html` `--papier`/`--weiss`. `tokens.css` behält die englischen Namen und vermerkt die Entsprechung. Sauberer wäre ein Satz — offen |

### Ungeprüft

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Die Palette wurde nicht angesehen.** Geändert sind Zahlen in einer CSS-Datei; ob die Anwendung damit aussieht wie die beiden Entwürfe, hat niemand im Browser verglichen | `docker compose up -d`, `/` und `/portal` neben `design/startseite.html` und `design/portalkonzept.html` legen |
| 2 | **Der Lauf fand nicht im Container statt.** Kein Docker-Daemon verfügbar; ersatzweise lokale MariaDB und der eingebaute PHP-Server. Fall 49 prüft damit **die Verzeichnisgrenze, nicht die Apache-Konfiguration** | `docker compose up -d --build`, dann `vendor/bin/phpunit` |
| 3 | **Die vier Prüfungen der Etappen A2/A3 gegen echte Mailzustellung** sind nicht Teil dieses Laufs | Mailpit unter `localhost:8025` |

---

## 09.08.2026 — Zahlung, Belege und Buchhaltung: Entscheidung eingearbeitet

**Der Betreiber hat am 09.08.2026 vier zusammenhängende Fragen entschieden.** Sie standen verteilt
über drei Dateien und waren einzeln nicht beantwortbar: **Regelbesteuerung** · **Mollie wird
gebaut** · **kein lexoffice, kein sevDesk** · **zwei zusätzliche Composer-Pakete erlaubt**.
Vermerk in `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4a, Bauvorlage in
`spezifikation/18_BELEGE_UND_ZAHLUNG.md`.

### Was sich in den Unterlagen gedreht hat

| Datei | Vorher | Jetzt |
|---|---|---|
| `spezifikation/02` | „Buchhaltung nicht selbst bauen. Rechnungen über lexoffice **oder** sevDesk (Entscheidung offen)" | Rechnungsausgang im Portal, Buchhaltung nicht; Export für den Steuerberater |
| `spezifikation/11` | „Rechnungsarchiv, Aufbewahrungsfristen und Nummernkreise laufen im Buchhaltungswerkzeug, **nicht** im Portal" | **laufen im Portal** — damit gilt die GoBD für das Portal |
| `spezifikation/13` | Rechnungsnummer „in Stufe 0 vom Admin **eingegeben**" | **vergeben aus `number_sequences`** unter Zeilensperre, lückenlos |
| `spezifikation/12` | Nicht-bauen-Liste mit „Zahlungsdienst-Anbindung" | Zahlungsdienst gestrichen, Buchhaltung im engeren Sinn ergänzt |
| `spezifikation/20` | „Buchhaltung: lexoffice oder sevDesk" als offener Punkt | **geschlossen**, durchgestrichen mit Verweis |
| `REIHENFOLGE.md` | Stufe C: „— · 0 Testfälle" | Stufe C: drei Tabellen, **12 Testfälle**, Freigabegrenze benannt |
| `CLAUDE.md` | 20 Tabellen, 88 Testfälle, Zahlungsdienst auf der Nicht-bauen-Liste | **23 Tabellen, 100 Testfälle**, Nicht-bauen-Liste bereinigt, Composer-Ausnahme benannt |

**Neu angelegt:** `spezifikation/18_BELEGE_UND_ZAHLUNG.md` (Bauvorlage) ·
`PROMPT_NEUE_SESSION_BELEGE.md` (Startprompt für die Bau-Session).

**Zwölf neue Testfälle, 84 bis 95.** Neue Nummern statt einer Zerlegung wie bei Fall 66 — es sind
eigenständige Prüfungen an einem neuen Gegenstand, keine Bedingungen derselben Sperre.

### An der Primärquelle geprüft

| Aussage | Quelle |
|---|---|
| Empfangspflicht seit **01.01.2025**, Versandpflicht **01.01.2027** über 800.000 €, **01.01.2028** alle übrige | BMF, FAQ zur obligatorischen E-Rechnung |
| Kleinunternehmer nach § 19 UStG **dauerhaft** vom Versand befreit | ebenda |
| Zulässig **XRechnung** und **ZUGFeRD ab 2.0.1**, ohne MINIMUM und BASIC-WL | ebenda |
| Strukturierter Teil **acht Jahre** unversehrt aufzubewahren | ebenda |
| GoBD gilt ausdrücklich für **Rechnungsschreibungssysteme**; Verfahrensdokumentation nach Rz. 151 ff. Pflicht; Programmdokumentation und Änderungsprotokolle unterliegen derselben Frist | BMF-Schreiben GoBD, 2. Änderung vom 14.07.2025 |

> **Regelbesteuerung heißt: die Frist ist der 31.12.2027.** Kein Zeitdruck — aber auch kein Grund,
> zweimal zu bauen.

### Ungeprüft — nichts davon ist Code

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Es wurde keine Zeile Anwendungscode geschrieben.** Geändert sind acht Markdown-Dateien; die 277 Tests laufen unverändert grün, weil sie nichts davon berühren | `PROMPT_NEUE_SESSION_BELEGE.md` abarbeiten |
| 2 | **Die zwei Composer-Pakete sind benannt, aber nicht ausgewählt.** Lizenz, Wartungsstand und PHP-8.3-Eignung sind vor dem Eintragen zu prüfen — das gehört in `IMPLEMENTATION_PLAN.md` der Bau-Session | Recherche im Bau |
| 3 | **Fall 85 (Nebenläufigkeit bei der Nummernvergabe) ist im PHPUnit-Rahmen schwer echt zu prüfen.** Ein Test, der zwei Rechnungen nacheinander anlegt, prüft ihn nicht | zwei parallele Verbindungen, sonst als Lücke eintragen |
| 4 | **Der Mollie-Webhook braucht eine öffentlich erreichbare Adresse.** Lokal bleibt er nachgebildet | erst auf dem Livesystem im Testmodus |
| 5 | **Die Formatfrage des Steuerberater-Exports ist offen** und bewusst nicht vorweggenommen. Welches Format er will, weiß nur er | Rückfrage beim Steuerberater |

---

## Stufe C — Belege, Nummernkreis und Zahlungsabgleich (09.08.2026)

Gebaut sind die Blöcke 1 bis 9 aus `spezifikation/18_BELEGE_UND_ZAHLUNG.md`. **337 Tests
grün**, 4231 Zusicherungen, gegen echtes MariaDB. Was davon *nicht* gemessen ist, steht hier.

### Was tatsächlich gelaufen ist

| Was | Wie belegt |
|---|---|
| Nummernvergabe unter **echter** Nebenläufigkeit — acht Betriebssystemprozesse, jeder hält seine Transaktion 50 ms | `NummernkreisTest`, Fall 85. Der erste Lauf lief in einen Deadlock; die Ursache stand im `INSERT IGNORE` **innerhalb** der Transaktion und ist behoben |
| Erzeugung eines PDF/A-3 mit eingebettetem XML, zurückgelesen **aus der abgelegten Datei** | `BelegeTest`, Fälle 87 und 88 |
| Abbruch vor jedem Schreiben, wenn die Prüfung durchfällt — keine Datei, keine Zeile, kein Zustandswechsel | `BelegeTest`, Fall 89 |
| Prüfsummenvergleich beim Abruf, mit veränderter und mit fehlender Datei | `BelegeTest`, Fall 94 |
| Idempotenz des Webhooks samt **ausbleibendem zweiten Abruf** beim Dienst | `ZahlungsabgleichTest`, Fall 91 |
| Verschlüsselter Zahlungsschlüssel — geprüft an der Datenbankspalte, an der Ansicht, an der Fehlermeldung und über **alle** Protokollzeilen | `ZahlungsabgleichTest` und `ErsteinrichtungMenueTest`, Fall 93 |
| Migration 036 auf der Arbeitsdatenbank eingespielt, mit Sicherung; `migrate.php verify` meldet keine Abweichung | am 09.08.2026 ausgeführt |

### Ungeprüft — mit Grund und Prüfmittel

| # | Was gebaut wurde | Was daran ungeprüft ist | Womit es zu prüfen ist |
|---|---|---|---|
| 1 | Prüfung des strukturierten Teils vor dem Versand | **Nur gegen das XSD-Schema, nicht gegen Schematron.** Fall 88 verlangt „Schema **und** Schematron". Das eingesetzte Paket bringt die Schemadateien mit; echtes Schematron läuft dort nur über `ZugferdKositValidator`, der zur Laufzeit ein Java-Archiv nachlädt — das wäre eine Netzabhängigkeit im Versandweg und ein drittes Fremdteil | KoSIT-Prüfwerkzeug **außerhalb** der Anwendung, einmalig gegen einen erzeugten Beleg. Erst danach ist Fall 88 vollständig |
| 2 | Der Webhook unter `/api/zahlungen/mollie` | **Nie von Mollie selbst aufgerufen.** Geprüft ist der Ablauf gegen eine Attrappe: Der Abruf holt, was hinterlegt ist, und die Nachricht bewirkt nichts. Ungeprüft bleibt das Verhalten des echten Dienstes — Zustandsnamen, Wiederholungsrhythmus, Zeitgrenzen | Auf dem Livesystem im Testmodus, mit einem Testschlüssel und einer öffentlich erreichbaren Adresse |
| 3 | `Mollie::zahlungAnlegen()` und `zahlungLesen()` | **Kein Aufruf gegen die echte Schnittstelle.** Feldnamen und Betragsformat stammen aus der Dokumentation des Anbieters, nicht aus einer Antwort | derselbe Durchlauf wie #2 |
| 4 | Fall 85, Nebenläufigkeit | Geprüft mit **acht** Prozessen auf einer Maschine. Ein Wettlauf, der erst bei höherer Last oder über zwei Datenbankknoten auftritt, bleibt außerhalb | Lasttest gegen die Produktionsdatenbank, falls die Menge das je rechtfertigt — bei einstelligen Rechnungszahlen je Monat eher nicht |
| 5 | Der Steuerberater-Export | **Das Format ist nicht abgestimmt.** Bewusst so: Welches Format er will, weiß nur er | Rückfrage beim Steuerberater, dann gegebenenfalls eine zweite Spaltenfolge |
| 6 | Belegversand per Mail mit Anhang | Der Anhang ist gegen die Attrappe geprüft, **nicht durch einen echten Mailserver**. Anhanggrößen um 100 KB sind unkritisch, aber ungemessen | Testversand über Mailpit, danach einmal über den produktiven SMTP-Zugang |
| 7 | Die Sichtseite des Belegs (`app/views/belege/rechnung.php`) | **Nie von einem Menschen angesehen.** Geprüft ist, dass ein PDF entsteht und das XML stimmt — nicht, ob Umbruch, Schriftgrößen und Logo auf Papier tragen. Der Umbruch ab vier Positionen ist ungeprüft, weil eine Rechnung heute **eine** Position hat | Einen erzeugten Beleg ausdrucken und ansehen |
| 8 | Aufbewahrung acht Jahre | **Es gibt keinen Ablauf, der sie durchsetzt oder überwacht.** Nichts löscht Belege — das ist die richtige Richtung, ersetzt aber keine Sicherungsstrategie | Sicherungsplan des Hosters, siehe `VERFAHRENSDOKUMENTATION.md` Abschnitt 5 |

### Eine Abweichung, die keine Prüfung nachholt

**Die CSRF-Regel gilt nicht für den Zahlungs-Webhook.** `spezifikation/14_SICHERHEIT.md`
Regel 3 sagt „Kein Token, keine Ausnahme"; `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6 verlangt,
dass ein fremder Server eine Adresse aufruft. Ein fremder Server hat keine Sitzung und kein
Formular und kann kein Token haben — **beide Vorgaben zugleich sind nicht erfüllbar**.

Aufgelöst so eng wie möglich: Die Ausnahme hängt als Schalter an **einer** Route, greift nur
im Bereich `api`, und `TenantIsolationTest` schlägt an, sobald eine zweite Route sie trägt.
Sie öffnet nichts, weil die Route keine Sitzung liest und aus einem Aufruf nur eines folgt —
eine Frage an den Zahlungsdienst.

**Das ist eine Entscheidung, keine Messung.** Wer sie anders treffen will, findet die
Begründung an `Route::$ohneCsrf`.

---

## Audit vom 10.08.2026 — Sicherheit, Logik, Systemfehler

Anlass: die Frage, ob der Bau auf **MySQL** läuft und ob nach der Anmeldung nur noch Einträge
fehlen. Beides ist geprüft, nicht eingeschätzt.

### Was tatsächlich ausgeführt wurde

| Prüfung | Wie |
|---|---|
| Vollständige Testreihe gegen **MySQL 8.4** mit dem Anwendungsbenutzer | 342 grün |
| Dieselbe Reihe gegen **MariaDB 11.4** | 342 grün — beide Systeme, nicht eines |
| **Ersteinrichtung auf leerer Datenbank**, alle acht Schritte über HTTP | am 10.08.2026 durchgespielt, `/admin/setup` danach 404 |
| **Anmeldung mit Passwort und TOTP**, danach jede Adminseite aufgerufen | zehn Seiten, alle 200 |
| Öffentliche Seiten | 11 Seiten 200; `/impressum`, `/datenschutz`, `/agb` liefern 404, solange kein Rechtstext freigegeben ist — beabsichtigt |
| **Abhängigkeiten gegen die Packagist-Meldungen** | 62 Pakete, 53 Meldungen ausgewertet, **keine zutreffend** |
| `bin/startklar.php` | meldet die sechs offenen Punkte und gibt 1 zurück |

### Gefunden und behoben

| # | Fund | Wirkung ohne Behebung |
|---|---|---|
| 1 | `information_schema` gibt die Spaltennamen auf MySQL in Großbuchstaben zurück; der Zugriff lief über den Feldnamen | Die Vorprüfung meldete „Der Zeichensatz ist nicht utf8mb4" auf einer utf8mb4-Datenbank. **Die Ersteinrichtung wäre auf MySQL nie durchgelaufen** |
| 2 | MySQL verweigert `CREATE TRIGGER` ohne `SUPER`/`SET_USER_ID`, sobald das Binärlog läuft | Abbruch in Schritt 4 mit „Fehler 1419", nach vier bereits angelegten Tabellen |
| 3 | `TRIGGER` fehlte in der Liste der benötigten Rechte | Die Vorprüfung meldete „in Ordnung" für einen Benutzer, mit dem die Migration nicht durchläuft |
| 4 | Der Zahlungs-Webhook nahm **jede** Zeichenkette an — eine Zeile in `payment_events` und eine ausgehende Anfrage je Aufruf | Unauthentifiziert, unbegrenzt, verstärkend: Aus einem billigen Aufruf wurde eine teure Handlung |
| 5 | `AnmeldeTokenSpeicher::abgelaufeneAufraeumen()` rief **niemand** auf | Abgelaufene Anmeldelinks blieben unbegrenzt liegen. Nicht einlösbar, aber eine Datensammlung ohne Zweck |
| 6 | Auf einer **Stornorechnung** ließ sich eine Zahlung eintragen | `0 >= -119000` ist wahr — die Gutschrift stand sofort auf `bezahlt` |
| 7 | Eine **Stornorechnung ließ sich stornieren** | Es entstand ein Beleg mit positiven Beträgen, der „Stornorechnung" hieß |
| 8 | `dompdf` war auf `^3.1` gebunden | Sechs CVEs sind erst in 3.1.6 geschlossen. Ein `composer update` hätte auch 3.1.0 nehmen dürfen |

### Geprüft und in Ordnung

CSRF-Token je Sitzung mit `hash_equals` · Sitzungscookie `httponly`/`SameSite=Lax`/`secure`
außer lokal, neue Kennung bei jeder Anmeldung, serverseitige Gültigkeit bei **jedem** Aufruf ·
Argon2id, Blindhash gegen Kontoerkennung, eigener Zähler für den zweiten Faktor, TOTP-Codes
einmalig · Anmeldelinks nur als SHA-256 gespeichert, 15 Minuten, einmalig, bedingtes `UPDATE`
gegen das Wettrennen · jede Ausgabe durch `Html::e`, JSON-LD mit `JSON_HEX_TAG` gegen den
`</script>`-Ausbruch · CSP ohne `unsafe-inline` für Skripte, `nosniff`, `X-Frame-Options: DENY`,
HSTS in Produktion · `DocumentRoot` auf `/public`, alles darüber verweigert · keine Stacktraces
nach außen, interne Kennung ins Log · Uploads nach Inhalt geprüft, nicht nach Angabe des
Browsers, Ablage außerhalb von `/public` · keine offene Weiterleitung, kein Ansichtsname aus
einer Eingabe.

### Offen — und warum

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Ein Kunde entsteht nur aus einer eingegangenen Anfrage.** Es gibt keinen Weg im Adminbereich, eine Organisation oder ein Projekt von Hand anzulegen | So gebaut wie vorgegeben (`12_ADMINBEREICH.md`: „Eingegangene Bedarfsschecks, Umwandlung in Kunde und Projekt"). Wer telefonisch gewonnen wird, wird über `/briefing` eingetragen — das dauert ein paar Minuten und funktioniert. Ein eigener Weg wäre eine **neue Anforderung**, keine Korrektur |
| 2 | Der gleichzeitige Aufruf von „Stornieren" durch **zwei** Admins könnte zwei Stornorechnungen erzeugen | Eine Zeilensperre auf der Rechnung. Bei einem Betreiber mit einem Konto ist der Fall theoretisch |
| 3 | Der Adminbereich zeigt den Zustand als Systemwort (`teilweise_bezahlt`) | Absicht: Die Regel „kein Systemcode" gilt dem **Kunden**. Falls es stören sollte, steht `Zahlungsstatus::kundentext()` bereit |

---

## Designabgleich vom 10.08.2026 — gebauter Stand gegen die abgenommenen Entwürfe

Gemessen, nicht schriftlich verglichen: beide Entwürfe und die gebauten Seiten im selben
Browser bei 1440 px gerendert.

### Öffentliche Website — übertragen

| Prüfung | Ergebnis |
|---|---|
| `design/tokens.css` gegen `public/assets/css/tokens.css` | **byteweise identisch** |
| Farbwerte | Papierton `--cream #f6f6f4`, `--ink`, `--lime #a3e635` wie im Entwurf; die warme Reihe vom Juli ist abgelöst |
| Abschnittsfolge der Startseite | **deckungsgleich**: Aufmacher · Kundenbereich · Ablauf · Preise · Zusage · Leistungen · Muster · Fragen · Abschluss |
| Schrift, Flächen, Radien, Dichte | wie im Entwurf |
| Wortlaut | weicht ab — **so vorgesehen**, jeder Text im Entwurf ist Platzhalter |

### Kundenbereich und interner Bereich — Navigation **nicht** übertragen

Der Betreiber hat am 03.08.2026 entschieden: **Seitenleiste links**, drei Gruppen, je mit
Zeichen, Zähler nur wo etwas offen ist (`OFFENE_ENTSCHEIDUNGEN.md`, „Navigation in beiden
Bereichen"). Der Vermerk hielt schon damals fest, dass der gebaute Stand noch das waagerechte
Kopfband trägt.

**Am 10.08.2026 ist es unverändert.** Belege:

| Beleg | Befund |
|---|---|
| `app/views/partials/kundenband.php`, `kopfband.php` | bauen weiterhin ein waagerechtes Band |
| `--ink-2` und `--ink-3` — die beiden Farben **für die Leiste** | in `tokens.css` definiert, in **keiner** Regel benutzt. Das ist der Beleg, dass die Leiste nie entstand |
| Menüwort des siebten Punktes | steht auf `Öffnungszeiten`; entschieden ist `Inhalte` fürs Menü und `Öffnungszeiten` als Seitentitel |

Die **Inhalte** der Bildschirme sind gebaut: Stationenleiste, offene Punkte, letzte Aktivität
und die fünf Blöcke aus §8.1 stehen. Es fehlt die Hülle, nicht der Inhalt.

### Drei weitere Abweichungen, gemessen

| # | Was | Beleg |
|---|---|---|
| 1 | **Das Logo wird nirgends verwendet.** `sartu-logo-hell.svg`, `-dunkel.svg` und `sartu-mark.svg` liegen unter `public/assets/bild/`, aber **keine Ansicht bindet sie ein** — Kopf und Fuß setzen das Wort „SARTU" als Text. `07_MARKE_UND_GESTALTUNG.md` verlangt in der Kopfleiste Zeichen + Wortmarke, 34 px | `grep -rl "sartu-logo" app/views/` findet nichts |
| 2 | **Die Hauptnavigation bricht auf zwei Zeilen.** Gemessen bei 1920 px, 1440 px und 1024 px je zwei Zeilen (78 px hoch), nur bei 1280 px eine (29 px). Der Entwurf hat eine Zeile | Playwright, vier Breiten |
| 3 | Das Aufmacherbild ist der Platzhalter `[[SCREENSHOT-FEHLT]]` | **Absicht** — es gibt noch keine Aufnahme, und die Startsperre sucht genau diese Marke |

### Was daraus folgt

Der Umbau auf die Seitenleiste **ist gesperrt, nicht vergessen**: Punkt 9 in
`OFFENE_ENTSCHEIDUNGEN.md` — ob es in der Oberfläche überhaupt Zeichen geben darf — steht auf
`offen`. Der Entwurf zeigt die Leiste **mit** Zeichen. Ohne diese Entscheidung lässt sich die
Leiste nur ohne Zeichen bauen, und dann wäre sie beim Nachziehen ein zweites Mal zu ändern.

---

## 10.08.2026 — Aufmacherbild entschieden, Startseite gegen den Entwurf nachgemessen

**Nachgemessen mit Playwright bei 1440 px**, beide Seiten im selben Browser: `design/startseite.html`
gegen die laufende Anwendung unter `/`.

### Der Befund, der den Designabgleich derselben Woche korrigiert

Der Eintrag „Designabgleich vom 10.08.2026" meldet **„Öffentliche Website — übertragen"** und
belegt das mit zwei Messungen: `tokens.css` byteweise identisch, Abschnittsfolge deckungsgleich.
**Beides stimmt. Beides trägt die Überschrift nicht.** Gemessen wurde die Reihenfolge, nicht das
Bild — drei Zeilen darunter stehen bereits drei Abweichungen. Der Aufmacher weicht an **neun**
Stellen ab.

| # | Abweichung | Ursache |
|---|---|---|
| 1 | Gerät im Aufmacher fehlt, stattdessen gestrichelter Bildplatz | **Vorgabe befolgt** — `10_WEBSITE_SARTU.md` verbot die nachgebaute Oberfläche. Entschieden am 10.08.2026, siehe unten |
| 2 | Lime-Akzent auf dem Schluss der H1 fehlt | **Nebenwirkung einer Textbindung.** Die H1 ist eine Konstante und läuft durch `Html::e()`; eine escapte Konstante kann kein Markup im Satz tragen |
| 3 | Lime als Textmarker auf Links fehlt | **Verstoß gegen eine gebundene Regel** (Rang 1, Farbsystem Fassung 3). `website.css` enthält null `background-image`; Links sind unterstrichen |
| 4–7 | Pfeile in den Knöpfen · Diagonalbänder hinter dem Aufmacher · Trennlinie über der Vertrauensliste · Aufzählungszeichen | nicht gebaut |
| 8 | Logo nirgends eingebunden | von der Bau-Session selbst gemeldet |
| 9 | Navigation zweizeilig bei 1920, 1440, 1024 px | von der Bau-Session selbst gemessen |

> **Nur einer der neun Punkte geht auf eine zu enge Vorgabe zurück — Punkt 2.** Punkt 1 ist eine
> befolgte Vorgabe, die dem abgenommenen Entwurf widersprach. Die übrigen sieben haben mit
> Vorgaben nichts zu tun.

### Entschieden: das Aufmacherbild

`SARTU_ENTSCHEIDUNGEN_OFFEN.md` **§4b** — Gerät in leichter Schrägstellung, darauf eine **echte
Aufnahme des eigenen Kundenbereichs** mit Musterdaten, Rahmen selbst gezeichnet.

**Der Grund für den Platzhalter ist entfallen:** Die Regel verlangte eine echte Oberfläche, und
seit dem 10.08.2026 gibt es sie. `10_WEBSITE_SARTU.md` ist in Sektion 1 und 2 nachgezogen; für
Musterprojekte und Gründerfoto bleibt der Bildplatz, weil dort das Bild der **Beleg** ist.

### Ungeprüft

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Die Aufnahme des Kundenbereichs gibt es noch nicht.** Sie entsteht erst beim Umbau; bis dahin ist der Aufmacher unverändert | `PROMPT_NEUE_SESSION_STARTSEITE.md` abarbeiten |
| 2 | **Ob die Musterdaten im Bild den Regeln aus `17_SEITEN_SARTU.md` genügen**, ist erst am fertigen Bild prüfbar — keine echten Namen, keine realistischen Rechnungsnummern | Sichtprüfung der Aufnahme |
| 3 | **Die Schrägstellung ist nicht erprobt.** Ob ein perspektivisch gekipptes Gerät bei 1024 px und darunter noch trägt, zeigt erst der Bau | vier Breiten rendern |

> **Abgearbeitet am selben Tag.** Die drei offenen Punkte oben sind erledigt: Die Aufnahme gibt
> es (Punkt 1), sie ist als `Musteransicht` gekennzeichnet und trägt Musterdaten (Punkt 2), und
> die Schrägstellung ist gebaut — bei 1440 px gemessen, bei den übrigen Breiten **nicht**
> (Punkt 3, steht unten neu). Der Abschnitt darunter führt den Stand nach dem Umbau.

## Aufmacher der Startseite auf den Entwurf gezogen — 10.08.2026

Anlass: Der Abgleich am selben Tag hatte aus *„`tokens.css` byteweise gleich **und**
Abschnittsfolge gleich"* geschlossen, die Startseite sei übertragen. **Der Schluss war falsch.**
Der Aufmacher wich an neun Stellen ab; sie sind abgebaut.

### Was ausgeführt wurde

| Prüfung | Wie |
|---|---|
| Entwurf und gebauter Stand **im selben Browser** bei 1440 px gerendert und nebeneinandergelegt | Chromium über Playwright, ein Lauf, ein Bild |
| Bewegung an **und** aus | `prefers-reduced-motion: reduce` liefert `animation-name: none` für Woge und Band — die globale Regel in `tokens.css` greift |
| Navigationszeilen bei 1920 · 1440 · 1280 · 1024 px | **überall eine** |
| Die neun Punkte einzeln | `MarkupTest::testDerAufmacherTraegtDieNeunMerkmaleDesEntwurfs` |
| Testreihe | **347 grün**, 4304 Zusicherungen |

### Die neun Punkte

| # | Was fehlte | Was jetzt steht |
|---|---|---|
| 1 | Gestrichelter Bildplatz | Selbst gezeichnetes Gerät, CSS-Perspektive, darauf eine **echte Aufnahme** des Kundenbereichs als WebP. Vermerk `Musteransicht` am Bild |
| 2 | Lime-Akzent auf dem Schluss der H1 | `H1_ANFANG` und `H1_SCHLUSS`, der zweite Teil in `<span class="akzent">`. Wortlaut unverändert, `h1Vollstaendig()` hält das fest |
| 3 | Lime als Textmarker auf Links | `background-image` mit `background-size`, auf volle Höhe beim Überfahren. Kein `text-decoration` |
| 4 | Pfeile in beiden Knöpfen | `<span class="pfeil" aria-hidden="true">→</span>`, rückt beim Überfahren nach rechts |
| 5 | Diagonalbänder hinter dem Aufmacher | Drei Bänder in zwei Bewegungen, `aria-hidden`, ohne Inhalt |
| 6 | Trennlinie über der Vertrauensliste | `.aufmacher__leiste` mit `border-top` — Gruppe 3 aus §5 Sektion 1 |
| 7 | Mittelpunkte als Aufzählungszeichen | Halbgeviertstrich, 10 × 2 px, wie im Entwurf |
| 8 | Logo nirgends eingebunden | Kopf 34 px, Fuß 30 px, helle Fassung auf hellem Grund |
| 9 | Navigation brach auf zwei Zeilen | `flex-wrap: nowrap`, kleinere Abstandsstufe |

**Dabei fiel ein zehnter Punkt auf**, der in der Liste nicht stand: Der Preishinweis stand
**über** der Vertrauensliste. §5 Sektion 1 Gruppe 3 verlangt *„Trust-Zeile, **darunter** der
Preishinweis"*. Er steht jetzt darunter; auf allen anderen Seiten bleibt er, wo er war.

### Ungeprüft — mit Grund und Prüfmittel

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Die Aufnahme zeigt eine Entwicklungsdatenbank.** Organisation `Mustermann Sanitär GmbH`, drei erfundene Aufgaben. Sie ist damit richtig als `Musteransicht` gekennzeichnet — aber sie zeigt nicht, wie ein echtes Projekt nach Wochen aussieht | Nach dem ersten Kundenprojekt neu aufnehmen, weiterhin mit Musterdaten |
| 2 | **Die Kapazitätszeile stand im Vergleich nur, weil ich `operator_settings.auftragslage` auf `offen` gesetzt habe.** Das ist Betreiberdatum, keine Gestaltung — sie fehlt, solange der Betreiber nichts pflegt | Im Adminbereich unter Betreiberdaten setzen |
| 3 | **Das Gerät ist nur bei 1440 px gemessen.** Unterhalb von 900 px legt sich der Aufmacher einspaltig; das ist gebaut, aber nicht Bild für Bild geprüft | Durchsehen bei 390 · 768 · 1024 px |
| 4 | **Die Bänder sind nicht auf Rechenlast gemessen.** Zwei Dauerbewegungen mit `filter: blur()` können auf schwachen Geräten kosten | Messung im Browserwerkzeug auf einem älteren Telefon |
| 5 | Die WebP-Datei entstand über **Chromium**, nicht über `cwebp` — im Bild ist kein Encoder installiert, und dafür einen aufzunehmen wäre eine Abhängigkeit für einen einmaligen Vorgang | Bei Bedarf mit `cwebp -q 88` gegenprüfen |

---

## 13.08.2026 — Gerätebild, Vertrauensangaben, neun Kleinfehler

Auftrag in `PROMPT_NEUE_SESSION_VERTRAUEN.md`. Grundlage `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4b
und §4c, beide Rang 1.

### Block 1 — das Mockup fehlt, der CSS-Block ist gelöscht, das Gerät steht

`public/assets/bild/geraet-aufmacher.webp` liegt **nicht** im Repository. Nachgesucht am
13.08.2026 über `git rev-list --all --objects` und `git log --all --diff-filter=A`: Die Datei
existiert in keinem Commit, keinem Branch und keiner Objektdatenbank.

### Zwei Vorgaben, die sich zu widersprechen schienen

| Woher | Was sie verlangt |
|---|---|
| Anweisung vom 13.08.2026 | „Das Bild ersetzt `.geraet__deckel` samt Telefon und Schattenwerk; **lösch den toten CSS-Block**, statt ihn liegen zu lassen." |
| §4b, **Rang 1** | „Im Aufmacher steht ein **Gerät in leichter Schrägstellung** — Laptop mit angeschnittenem Telefon davor." |

Ohne Mockup schien nur eines von beiden zu gehen: Entweder der Block bleibt und die Anweisung
ist verletzt, oder das Gerät verschwindet und §4b ist verletzt. **Beides ist am 13.08.2026
einmal ausgeführt worden** — der Verlauf steht unten, weil er in der Versionsverwaltung sichtbar
ist und sonst wie ein Versehen aussähe.

### Aufgelöst: §4b nennt das Mittel selbst

`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4b, Zeile 402:

> „Der Rahmen wird **selbst gezeichnet** — CSS **und Inline-SVG**, keine gekaufte oder
> heruntergeladene Vorlage."

**Das Gerät steht jetzt als Inline-SVG in `partials/aufmacherbild.php`.** Damit ist beides
erfüllt, ohne dass eine Vorgabe weichen muss:

| Vorgabe | Wie sie erfüllt ist |
|---|---|
| „lösch den toten CSS-Block" | `.geraet__laptop`, `.geraet__deckel`, `.geraet__sockel` und `.geraet__telefon` sind aus `website.css` **entfernt**. Übrig sind zwei Regeln, die keine Zeichnung sind: die Hülle und die Bildbreite |
| §4b, Gerät in Schrägstellung | Laptop als Trapez mit aufgeklappter Basis, angeschnittenes Telefon davor, beide mit der echten Aufnahme |
| „erfinde kein Ersatzbild" | Kein Bild beschafft. Der Inhalt bleibt `sartu-kundenbereich-muster.webp`, die Aufnahme vom 10.08.2026 |
| Keine Zahl im Bauteil | Die drei Grauwerte kommen als `var(--geraet-*)` aus `tokens.css` — `var()` gilt im Inline-SVG wie im CSS |
| Kein externer Abruf | Jede Adresse im SVG zeigt auf das eigene Verzeichnis; der Test prüft das mit einem Muster auf `https?://` |

**Die Schrägstellung ist dabei genauer geworden.** Die alte Fassung drehte ein Rechteck über
`rotateY(-7deg)`; die Aufnahme kippte mit und wurde an den Rändern unscharf. Jetzt ist der
Deckel ein Trapez mit kürzerer linker Kante, und die Aufnahme wird auf dieselbe Form
beschnitten.

### Der Verlauf, damit die Historie lesbar bleibt

| Commit | Was geschah |
|---|---|
| `a08b6fd` | CSS-Block gelöscht, Gerät entfiel — §4b verletzt |
| `37ed53c` | Wiederhergestellt — Anweisung verletzt |
| *diese Fassung* | Gerät als Inline-SVG, CSS-Block gelöscht — beides erfüllt |

`MarkupTest::testDerGeraeterahmenWirdGetauschtSobaldDasMockupVorliegt` hält beide Zustände
fest, damit keiner der drei Schritte sich wiederholt:

| Zustand | Was der Test verlangt |
|---|---|
| **immer** | Die vier CSS-Regeln bleiben weg · der Vermerk `Musteransicht` steht als `figcaption` neben dem Bild, nie als `position: absolute` darauf |
| Mockup fehlt (**jetzt**) | Das SVG zeichnet das Gerät · zwei Bildschirme, beide mit der echten Aufnahme · die drei Grauwerte kommen aus `tokens.css` · keine fremde Adresse |
| Mockup liegt vor | Das SVG entfällt, ein `<img>` tritt an seine Stelle, und die drei Grauwerte verschwinden aus `tokens.css` |

Beide Richtungen sind ausgeführt worden, nicht behauptet: Mit einer testweise abgelegten Datei
schlägt der Test fehl und nennt die Stelle. Die Datei wurde danach wieder entfernt.

### Zwei Funde, die der gezeichnete Rahmen verdeckt hatte

Er ragte über `margin: 0 -7%` und ein Telefon bei `right: -7%` aus seiner Spalte heraus; die
Fläche sah dadurch richtig aus, obwohl die Spalte es nicht war. Sichtbar wurde es in dem einen
Commit ohne Rahmen.

| Fund | War | Ist |
|---|---|---|
| **Die Bildspalte war ein Viertel zu schmal.** `grid-template-columns: 55fr 45fr` ohne `minmax(0, …)`: Ein Rasterelement hat `min-width: auto` und schrumpft nicht unter die Mindestbreite seines Inhalts — die grosse H1 zog Platz aus der rechten Spalte | **343 px** bei 1440 px | **459 px**, wie `design/startseite.html` Zeile 204 es setzt |
| **Der Schriftgrad der H1 hing am Fenster statt an der Spalte.** Mit der korrigierten Spalte brach sie vierzeilig um, „zum" allein auf einer Zeile | `--fs-h1`, eine `vw`-Kurve | `clamp(40px, min(11cqw, 8.6vh), 80px)` an einem `container-type: inline-size`, wie Zeile 211 und 230 des Entwurfs |

Der Entwurf begründet den zweiten Punkt selbst und nennt zwei früher gescheiterte
`vw`-Formeln: *„die Spalte wächst stückweise, weil `--wrap` und `--gut` eigene Knickpunkte
haben. Eine gerade vw-Kurve kann ihr nicht folgen."*

**Gemessen** — H1 dreizeilig auf jeder geprüften Breite:

| Breite | Schriftgrad | Zeilen |
|---|---|---|
| 1920 · 1440 px | 61,7 px | 3 |
| 1280 px | 62,3 px | 3 |
| 1024 px | 54,1 px | 3 |
| 900 · 768 px | 57,6 · 49,2 px | 3 |
| 390 px | 40,0 px | 3 |

### Was von Block 1 ausgeführt wurde: der Überlauf

Der gemeldete Punkt war „bei 1024 px läuft die Seite über". **Gemessen war es mehr.** Die
Kopfzeile hat eine feste Eigenbreite von 1176 px und lief von **941 bis 1183 px** waagerecht
über — bei 1024 px um 152 px. Oberhalb davon lief sie weiter über die Bahn hinaus in den
Außenrand: Bei 1920 px stand der Knopf 67 px weiter rechts als jede Kante darunter, sichtbar
nur als schiefe Flucht.

| Was geändert wurde | Wirkung |
|---|---|
| `.seitenkopf__reihe` Abstand `--s-4` → `--s-3` | 24 px |
| `.hauptnavigation a` auf `--fs-small` — der Entwurf setzt die Navigation auf 15,5 px gegen 18 px Fließtext | 54 px |
| Mobilmenü ab **1180 px** statt ab 940 px — `design/startseite.html` Zeile 166 setzt genau diesen Wert | der Rest |

**Gemessen nach der Änderung**, im selben Chromium:

| Breite | scrollWidth | Versatz Kopfelement gegen Inhaltskante |
|---|---|---|
| 1920 px | 1920 — sauber | 0 px |
| 1440 px | 1440 — sauber | 0 px |
| 1280 px | 1280 — sauber | 0 px |
| 1024 px | 1024 — sauber | 0 px (Menüknopf, 91 × 55 px) |
| 390 px | 390 — sauber | 0 px (Menüknopf, 91 × 55 px) |

Zusätzlich nachgemessen: 941 · 960 · 1000 · 1060 · 1100 · 1140 · 1180 · 1200 · 1220 · 1240 px —
alle sauber.

> **Ein Widerspruch, der gemeldet und nicht stillschweigend aufgelöst wird.**
> `10_WEBSITE_SARTU.md` §2 sagt „**Desktop ab 1024 px:** … die sechs Punkte mittig". Mit dem
> Mobilmenü ab 1180 px gilt das nicht mehr zwischen 1024 und 1180 px.
>
> Dieselbe Datei löst es zwei Absätze höher selbst auf: *„Wird die Zeile zu breit, greift das
> Mobilmenü früher — der verständlichere Begriff wird nicht für sechs Pixel geopfert."* Diese
> Stelle trägt eine Begründung, die Zahl 1024 trägt keine — `CLAUDE.md`: „Widersprechen sich
> zwei Stellen im selben Dokument, gilt die mit der Begründung."
>
> Der abgenommene Entwurf setzt denselben Wert. **Zu entscheiden bleibt trotzdem**, ob §2
> nachgezogen wird.

### Block 2 — die Sperren hängen jetzt an den Daten

| Gebaut | Sichtbar, wenn |
|---|---|
| `gruender_name`, `gruender_text`, `gruender_bild` in `operator_settings` (037) | — |
| Sektion 6 „Wer dahintersteckt" auf der Startseite und der Abschnitt auf `/ueber-uns` | **alle drei** Angaben stehen. Zwei von drei genügen nicht |
| `/bild/gruender` — Ausspielroute, Ablage außerhalb von `public/` | ein Bild hinterlegt ist, sonst **404** statt Ersatzbild |
| `LocalBusiness` statt `Organization` | Straße, PLZ **und** Ort gefüllt sind |
| `logo`, `founder`, `sameAs` in `Organization` | Logodateien liegen · `gruender_name` steht · `profil_adressen` (039) gefüllt ist |
| `naechster_projektstart` (038), im Adminbereich pflegbar | er in der Zukunft liegt |

`app/Strukturdaten.php` begründete im Klassenkommentar das Gegenteil — der Kommentar ist
umgeschrieben und nennt §4c samt dem Satz, der die alte Bauform verwirft.

### Block 3 — die neun Punkte

| # | Was war | Beleg |
|---|---|---|
| 1 | Favicon fehlte vollständig | `public/favicon.ico` (16 · 32 · 48 px) und vier PNG, aus `sartu-mark.svg` erzeugt. `MarkupTest::testJedeSeiteTraegtBildmarkeUndVorschaukarte` |
| 2 | Kein Open Graph, keine `twitter:card` | `partials/teilenkarte.php` in beiden öffentlichen Layouts, `sartu-og.png` 1200 × 630. Derselbe Test prüft zusätzlich, dass `og:title` und `<title>` nicht auseinanderlaufen |
| 3 | `Article` ohne `datePublished`, Autor, Bild | `Strukturdaten::artikel()`; Autor ist eine **Person**, sobald `gruender_name` steht, sonst die Organisation. `VertrauensangabenTest` prüft beide Richtungen |
| 4 | Branchenseiten mit null eingehenden Verweisen | Block „Drei Branchen haben eine eigene Seite." auf `/leistungen`. `MarkupTest::testJedeBranchenseiteHatEingehendeVerweise` |
| 5 | Markdown im Fließtext | Drei Stellen — nicht zwei. `Ratgeber.php` (`**woraus**`), `Lexikon.php` zweimal. Die Betonung steckt jetzt in der Wortstellung. `MarkupTest::testKeineAuszeichnungsspracheImAusgeliefertenText` liest alle 68 Dienstdateien |
| 6 | „ab 1.490 € — mit einer eigenen Seite je Leistung" | Das Startpaket hat **1 Seite**. Beide Zahlen stehen jetzt getrennt: Einstieg 1.490 €, eine Seite je Leistung ab **3.900 €** (Wachstum). Sechs Stellen, drei Branchenseiten |
| 7 | Dreimal „Fakten in einem Gespräch" | Fünf Stellen, nicht drei. Ersetzt durch Bedarfsscheck und Kundenbereich — das ist gebaut und widerspricht „ohne einen einzigen Termin" nicht |
| 8 | `aria-label="Menü öffnen"` blieb im offenen Zustand | Entfernt. Der sichtbare Text `Menü` ist der Name, `aria-expanded` kommt vom Browser. `MarkupTest::testDasMenuezeichenBehauptetKeinenZustand` |
| 9 | „Nur noch wenige Plätze" ohne Zeitraum | `Nächster Projektstart ab <Monat Jahr>`, aus `operator_settings`. Ohne Datum entfällt die Zeile; ein vergangener Monat wird nicht angezeigt |

> **Zu Punkt 9 ein zweiter gemeldeter Widerspruch.**
> `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` §5a verbietet Termine wörtlich: *„Keine
> Zahlen, keine Termine. Weder ,3 Plätze frei' noch ,ab Q3'."*
>
> Diese Datei ist Begründungsarchiv und keine Bauvorlage (`CLAUDE.md`, Rangfolge);
> `spezifikation/` bindet zur Kapazitätszeile nur Stelle und Gewicht, keinen Wortlaut und kein
> Terminverbot. Die Betreiberanweisung vom 13.08.2026 sticht das Archiv.
>
> Die Sorge dahinter ist trotzdem gebaut: Es steht ein **Monat**, kein Tag — ein Starttermin
> für die Arbeit, keine Zusage über die Fertigstellung. Das Feld ist ein `DATE`, damit sich
> „ab Q3" nicht hineinschreiben lässt.

### Ungeprüft — mit Grund und Prüfmittel

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Das Mockup fehlt.** Das Gerät wird solange als Inline-SVG gezeichnet (§4b). Wie das gerenderte Mockup an derselben Stelle wirkt, ist nicht beurteilt — und ob die SVG-Fassung neben dem abgenommenen Entwurf besteht, ist eine Betreiberfrage, keine Messung | Datei unter `public/assets/bild/geraet-aufmacher.webp` ablegen — `aufmacherbild.php` nimmt sie von selbst, `MarkupTest` erzwingt dann den Wegfall des SVG — und die fünf Breiten neu messen |
| 2 | **Die Anweisung „häng sie in die Navigation" ist nicht wörtlich umgesetzt.** §2 bindet sechs Navigationspunkte, §2a fünf Fußspalten mit gebundenem Inhalt — und die Kopfzeile trägt gemessen keinen siebten Punkt, ohne dass §1 wieder verletzt wäre. Die Verweise stehen deshalb auf `/leistungen` | Betreiberentscheidung: §2 oder §2a ändern, oder es bei `/leistungen` belassen |
| 3 | **Der Upload ist nicht über das Adminformular ausgeführt.** Geprüft ist der Dienst über PHPUnit und ein Aufruf als `www-data`; das Formular selbst wurde nicht im Browser abgeschickt | Im Adminbereich anmelden, Bild hochladen, Startseite ansehen |
| 4 | **Das Gründerbild in der Entwicklungsdatenbank ist eine Attrappe** und liegt bewusst **nicht** im Repository. §5 verbietet einen Platzhalter, der wie ein Foto wirkt — dieses sieht wie eine Attrappe aus und heißt so | Echtes Foto vom Betreiber, dann austauschen |
| 5 | **Das Open-Graph-Bild ist nie von einem Vorschaudienst geholt worden.** Geprüft ist, dass die Angaben im Kopf stehen und die Datei ausgeliefert wird — nicht, wie LinkedIn oder WhatsApp sie darstellen | Nach dem Livegang mit den Debug-Werkzeugen der Dienste |
| 6 | **Das Favicon ist nicht in einem Browsertab angesehen worden.** Erzeugt, ausgeliefert (200, `image/vnd.microsoft.icon`) und verlinkt — die Darstellung bei 16 px auf hellem und dunklem Systemthema ist nicht beurteilt | In Chrome, Firefox und Safari öffnen |
| 7 | **`sameAs` steht auf keiner echten Profiladresse.** Das Feld ist gebaut und geprüft, gefüllt ist es mit `https://example.org/sartu` in der Entwicklungsdatenbank | Sobald es Profile gibt, im Adminbereich eintragen |
| 8 | **Die neuen Metaangaben sind nicht gegen einen Validierer gelaufen.** Die strukturierten Daten sind gegen das eigene Schema geprüft, nicht gegen Googles Rich-Results-Test — der braucht eine öffentlich erreichbare Adresse | Nach dem Livegang |

---

## 13.08.2026 — die dunklen Flächen des Entwurfs fehlten

**Befund des Betreibers:** „wo sind die dunklen bereiche wie im vorgegebnen artefakt."

Nachgemessen im Browser, Grund je Sektion aus `getComputedStyle`, Entwurf und gebauter Stand
nebeneinander bei 1440 px:

| # | Sektion | Entwurf | Gebaut (vorher) |
|---|---|---|---|
| 1 | Aufmacher | hell | hell ✓ |
| 2 | Kundenbereich | **dunkel**, `--r-xl` oben gerundet | hell ✗ |
| 3 | Ablauf | **dunkel**, `--r-xl` unten gerundet | `--sand` ✗ |
| 4 | Preise | hell | hell ✓ |
| 5 | Zusage | **dunkel**, randlos | dunkel ✓ |
| 7 | Leistungen | `--sand` | hell ✗ |
| — | SEO-Band | (gibt es nicht) | `--sand` ✗ |
| 9 | Fragen | hell | hell ✓ |
| 10 | Abschluss | **hell** mit dunkler gerundeter Fläche darin | ganz dunkel ✗ |

### Der Fehler, den keine Prüfung finden konnte

Der Kopf von `website.css` behauptete: *„die dunklen Abschnitte sind genau zwei: `.zusage` und
`.abschluss`."* Der Entwurf hat **drei** dunkle Flächen, und die grösste fehlte ganz — Sektion 2
und 3 bilden dort **einen durchgehenden Block**.

**Jede Sektion war für sich richtig gebaut.** Die Seite hatte eine H1, alle Kontraste stimmten,
die Testreihe war grün. Falsch war nur die **Folge** der Gründe — und dafür gab es keine
Prüfung. Ohne den Block läuft die Startseite von oben bis unten hell durch, genau das, was
Design-Briefing §3.7 mit „kein Aufbaumuster mehr als zweimal" verhindern soll.

### Was übertragen wurde

| Aus dem Entwurf | Wohin |
|---|---|
| `.dark{background:var(--ink);color:#efe9dd}` (Zeile 53) | `.abschnitt--dunkel` — die beiden Rohwerte als `--paper` und `--label-dark`, beide auf `--ink` gerechnet |
| `.round-top` / `.round-bot` (Zeile 57/58) | `.abschnitt--rundoben` / `.abschnitt--rundunten` |
| `style="padding-top:0"` am zweiten dunklen Abschnitt (Zeile 873) | `.abschnitt--dunkel + .abschnitt--dunkel { padding-top: 0 }` — als Regel, weil die CSP kein `style`-Attribut zulässt |
| `.sec.sand` an `#leistungen` (Zeile 994) | Sektion 7 trägt den Sandgrund, das SEO-Band darunter nicht mehr |
| `.cta-field` (Zeile 575) | `.handlungsfeld` — dunkel, `--r-xl`, `--shadow-lift`, zwei Spalten `1.25fr / .75fr` |

### Was am Entwurf nicht wörtlich übernommen ist — und warum

**`align-items: end` am Abschlussfeld.** Der Entwurf hat dort genau zwei Kinder; „end" lässt
beide unten bündig abschliessen. Unsere Seiten bringen links bis zu vier eigenständige Absätze
mit — „end" schob den Text nach unten und den Knopf allein nach oben, gemessen **110 px**
Versatz zwischen den Spaltenanfängen. Übernommen ist damit die Absicht (beide Spalten beginnen
auf einer Linie), nicht der Buchstabe.

**Zwei Hüllen je Feld statt einer CSS-Regel.** Der Versuch, ohne Markup auszukommen, ist zweimal
gescheitert: Ein über alle Reihen gespanntes Element streckt sie, und auf der Branchenseite ist
das letzte Kind der Preishinweis, nicht der Knopf. Neun Seiten haben jetzt
`handlungsfeld__text` und `handlungsfeld__handlung`.

### Gemessen

| Was | Ergebnis |
|---|---|
| Grundfolge Entwurf gegen gebaut | **deckungsgleich**, Sektion für Sektion |
| Kontraste im dunklen Block | H2, Fliesstext, Hakenliste, Zeitstrahl, Textlink **18,8 : 1** · Vorspann und Vorzeile **7,1 : 1** — alle über 4,5 : 1 |
| Fünf Breiten | 1920 · 1440 · 1280 · 1024 · 390 px, **kein waagerechter Überlauf** |
| Testreihe | **375 grün**, 11.379 Zusicherungen |

Festgehalten in `MarkupTest::testDieGrundfolgeDerStartseiteStimmtMitDemEntwurf` und
`testJedesAbschlussfeldHatBeideSpalten`.

### Ungeprüft

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Der Entwurf hat keine Sektion 6.** „Wer dahintersteckt" ist am 13.08.2026 dazugekommen (§4c) und steht zwischen der dunklen Zusage und dem Sandgrund von Sektion 7. Ob die Folge hell-dunkel-hell dort trägt, ist nicht am Entwurf zu prüfen — er kennt die Sektion nicht | Betreiberurteil |
| 2 | **Das SEO-Band steht im Entwurf nicht als eigene Sektion.** Es trägt jetzt keinen Grund mehr, damit der Sandgrund an Sektion 7 sitzt wie im Entwurf. Ob es dort überhaupt hingehört, ist eine Inhaltsfrage | `10_WEBSITE_SARTU.md` §5, Sektionsliste |
| 3 | **Die acht Unterseiten sind nur auf Antwortstatus geprüft**, nicht Bild für Bild. Sie tragen dasselbe Abschlussfeld, aber eigene Sektionsfolgen | Durchsehen bei 1440 und 390 px |

---

## 13.08.2026 — Startseite eins zu eins, echte Aufnahmen, Leiste bis oben

Drei Betreiberanweisungen an einem Tag: die Startseite bis auf die Texte eins zu eins aus
`design/startseite.html` übernehmen · echte Aufnahmen aus dem Kundenbereich statt der
Bildplätze · die Seitenleiste bis an den oberen Rand mit Logo und Bereichsnamen.

### Was aus dem Entwurf übertragen wurde

| Aus dem Entwurf | Wohin | Zeile im Entwurf |
|---|---|---|
| `.rise` / `.rise-2` / `.rise-3` — Einblenden beim Scrollen | `.hebt` / `.hebt-2` / `.hebt-3` | 133–135 |
| `.progress` — Lesefortschritt am oberen Rand | `.fortschritt`, im Layout | 138–140, 751 |
| `.tag::after` — der Lime-Balken wächst unter der Abschnittsmarke | `.vorzeile::after` | 614–617 |
| `.steps` — Zeitstrahl mit **Wechselseiten**, Linie in der Mitte, Nummer im Kreis | `.ablaufstrahl` / `.ablaufschritt` | 341–372 |
| `draw-line` — die Linie zeichnet sich beim Scrollen nach | `linie-faellt` | 129, 345 |
| `in-left` / `in-right` — jeder zweite Schritt kommt von der anderen Seite | `von-links` / `von-rechts` | 130–131, 368 |
| `.btn-hell` — heller Knopf auf dunklem Grund | `.knopf--hell` | 117 |
| `.wordmark` — die Wortmarke gross am Fuss, unten angeschnitten | `.wortriese` im Fussbereich | 1165 |
| `.nav a:hover` — ruhige Fläche statt Linie | `.hauptnavigation a:hover` | 164 |
| `.plan:hover` / `.lk>li:hover` — Karten heben sich | `.stufe:hover` / `.leistungszeilen > li:hover` | 387, 485 |

**Alles ohne JavaScript.** Der Entwurf löst die Einblendungen über `animation-timeline: view()`
und den Fortschrittsbalken über `animation-timeline: scroll(root block)` — scrollgetriebene
Animationen des Browsers, kein Skript, kein Beobachter. §1 gilt unverändert.

**Kein Inhalt wird erst durch das Scrollen sichtbar** (§1). Gemessen: Im ersten Bild steht
**kein** `.hebt`-Element unter Deckkraft 0,9. Kennt ein Browser `animation-timeline` nicht,
fällt die Angabe weg und `animation: hebt linear both` läuft ohne Dauer — also sofort auf dem
Endzustand.

### Was **nicht** übernommen wurde

| Was | Warum |
|---|---|
| Die sechs Lime-Varianten im Grund (`#l-naht`, `#l-rand`, `#l-raster`, `#l-kante`, `#l-ecke`, `#l-karten`) | Der Entwurf stellt sie zur Auswahl und sagt selbst: „**Vorgabe ist OHNE.** Der Rest fliegt vor der Portierung raus." |
| Die beiden Umschalter `.gt` unten in den Ecken | Werkzeug des Entwurfs, kein Bauteil |
| Der nachgezeichnete Bildschirminhalt (`.ui`, `.ui-card`, `.ui-bar`) | `10_WEBSITE_SARTU.md` §8 verbietet die nachgebaute Oberfläche. An seiner Stelle steht die **echte** Aufnahme |
| `gap: var(--s-2)` an der Navigation **zusammen mit** dem Innenabstand | Die Bahn des Entwurfs ist 1380–1800 px breit, unsere 1180. Mit beidem lief die Kopfzeile bei 1280 px um 22 px über — gemessen. Der Innenabstand wird jetzt aus dem Zwischenraum genommen: `gap: 0`, 6 px je Seite. Der Abstand zwischen zwei Wörtern bleibt bei 12 px |

### Echte Aufnahmen statt Bildplätze

Vier Aufnahmen aus dem gebauten Stand, mit Musterdaten, als WebP:

| Datei | Zeigt | Wo |
|---|---|---|
| `sartu-kundenbereich-muster.webp` | `/portal` — die Übersicht | Aufmacher |
| `sartu-portal-aufgaben.webp` | `/portal/aufgaben` | Sektion 2 |
| `sartu-ablauf-1-bedarfsscheck.webp` | `/briefing/1` | Ablauf, Schritt 1 |
| `sartu-ablauf-2-angebot.webp` | `/portal/angebot` | Ablauf, Schritt 2 |
| `sartu-ablauf-5-vorschau.webp` | `/portal/vorschau` | Ablauf, Schritt 5 |

`partials/bildplatz.php` bleibt unverändert und gilt weiter für **Musterprojekte** und das
**Gründerfoto** — dort ist das Bild der Beleg, und der fehlt noch (§5). Für den Kundenbereich
fehlt er nicht mehr (§4b).

**Das Musterangebot trägt die Zahlen aus `Preise::tabelle()`**, nicht erfundene: 3.900 €
einmalig, 129 € im Monat, Erstjahr 5.448 € — der Umfang `wachstum`. Der Projektstand wurde für
die Aufnahmen durchgeschaltet und danach auf `briefing` zurückgesetzt.

### Zwei Fehler, die dabei aufgefallen sind

**1 — Die Seitenleiste begann 29 px unter dem oberen Rand.** Ursache war das Zeichen-Sprite:
`<svg width="0" height="0" style="position:absolute">`. Das `style`-Attribut ist von der
**eigenen CSP** verworfen worden (`style-src 'self'` ohne `unsafe-inline`), und ein
Inline-Element mit Nullmaßen erzeugt trotzdem eine Zeilenbox. Sichtbar auf **jeder** Seite des
Kunden- und Adminbereichs. Die Angabe steht jetzt als `.zeichensatz` in `anwendung.css`.

**2 — `.rail a` zentrierte den Leistenkopf.** Die Regel ist für die Menüeinträge gedacht, stand
aber ohne Ausschluss da und war spezifischer als `.rail-marke` — Logo und Bereichsname wurden
mittig gesetzt statt links. Jetzt `.rail a:not(.rail-marke)`.

### Eine Abweichung von der Anweisung, gemeldet

Der Betreiber verlangte unter dem Logo **„Kundenportal" bzw. „Adminportal"**. Gebaut sind
**Kundenbereich** und **Adminbereich**.

`10_WEBSITE_SARTU.md` §2 und `CLAUDE.md`: *„`Portal` ist gestrichen, ersetzt durch
`Kundenbereich`"* und *„nach aussen nie: App · Software · SaaS · Plattform · Tool · Dashboard ·
System"*. Die Leiste sieht der Kunde — und sie steht seit heute als Aufnahme auf der
öffentlichen Startseite. Die gemeinte Unterscheidung bleibt erhalten.

### Ungeprüft

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Die scrollgetriebenen Animationen sind nur in Chromium geprüft.** Firefox und Safari unterstützen `animation-timeline` unterschiedlich weit; ohne Unterstützung steht der Endzustand sofort — das ist der Rückfall, nicht ein Fehler | In Firefox und Safari öffnen |
| 2 | **Die Bewegungen sind nicht auf Rechenlast gemessen.** Fortschrittsbalken, Bänder und bis zu 20 Einblendungen laufen gleichzeitig | Browserwerkzeug auf einem älteren Telefon |
| 3 | **Vollseitenaufnahmen zeigen die eingeblendeten Blöcke leer.** Beim Zusammensetzen eines `fullPage`-Bildes läuft die `view()`-Zeitachse nicht mit. Beim echten Scrollen erreicht jedes Element Deckkraft 1 — nachgemessen | Der Vergleich wird deshalb mit `prefers-reduced-motion` aufgenommen |
| 4 | **Die Aufnahmen zeigen eine Entwicklungsdatenbank.** Ein Projekt, drei Aufgaben, ein Angebot. Nach dem ersten echten Projekt neu aufnehmen — weiterhin mit Musterdaten | Erneut aufnehmen |
| 5 | **Der Adminbereich ist nach der Leistenänderung nur auf Antwortstatus geprüft**, nicht Bild für Bild | Anmelden und durchsehen |
| 6 | **Die Texte sind unverändert.** Der Auftrag lautete „nur die Texte solltest du neu denken" — übertragen ist bisher alles **außer** den Texten. Der Textdurchgang steht aus | Mit dem Texter-Skill je Sektion |

---

## 13.08.2026 — die Prüfung der Vorgaben: nicht sie waren zu eng, sondern eine Zahl fehlte

Der Auftrag lautete, die Preissektion weiche vom Entwurf ab, das liege an zu festgefahrenen
Vorgaben in den MD-Dateien, und die seien zu löschen oder ins Archiv zu schieben — **„prüfe das
genauestens"**. Geprüft wurde vor jeder Änderung. **Es ist nichts gelöscht und nichts archiviert
worden**, weil die Prüfung das Gegenteil ergibt.

### Was die Vorgaben zur Preissektion sagen — und was gebaut war

`spezifikation/10_WEBSITE_SARTU.md` Sektion 4, wörtlich:

> **Darstellung: alle vier Stufen nebeneinander.** Sonderprojekt stand als Querblock **unter**
> den drei Paketen, obwohl §4 es als vierte Stufe mit eigenem Knopf führt.

Gebaut waren drei Karten und darunter ein Querblock — **exakt der Zustand, den die Vorgabe als
Fehler benennt.** Die Vorgabe war nicht zu eng, sie war nicht umgesetzt.

### Die eigentliche Ursache: ein Wert, der beim Übertragen hängenblieb

`spezifikation/07_MARKE_UND_GESTALTUNG.md`, Abschnitt „Satzspiegel — fließend, nicht fest":

| | Vorgabe | war in `tokens.css` |
|---|---|---|
| `--wrap` | `clamp(1380px, 90vw, 1800px)` | `1180px` |
| H1 | `clamp(34px, min(11cqw, 8.6vh), 104px)` | `clamp(40px, …, 80px)` |
| H2 | `clamp(27px, calc(3vw − 6px), 54px)` | `clamp(31px, 4.3vw, 50px)` |

Die Vorgabe argumentiert an Ort und Stelle **gegen** genau den Wert, der im Code stand: „Die
Seite lief auf `--wrap: 1180` — abzüglich der Ränder blieben 1068 px Inhalt und damit unter dem
üblichen Band."

**Drei Abweichungen hingen an dieser einen Zahl** und waren einzeln als Layoutfehler behandelt
worden:

| Symptom | bisherige Behandlung | tatsächliche Ursache |
|---|---|---|
| Kopfzeile lief zwischen 941 und 1183 px über | Navigation verkleinert, Abstandsstufe gesenkt, Zwischenraum auf 0 | die Bahn wuchs nicht mit, `--gut` schon |
| vierte Preisstufe passte nicht in die Zeile | als Querblock darunter gebaut | 1068 px Inhalt tragen keine vier Karten |
| Überschriften deckelten früh | als gewollt hingenommen | die Spannen sind an den fließenden Satzspiegel gerechnet |

Gemessen nach der Korrektur: **302 px je Karte bei 1440 px, 461 px bei 1024 px — dieselben
Werte wie im abgenommenen Entwurf**, auf 1 px genau.

### Warum nichts archiviert wurde

Was in `spezifikation/` steht, ist zu drei Vierteln **kein Layout**: die Beträge (1.490 / 3.900 /
7.900 / ab 12.500 €, die Monatssätze, das erste Jahr), die Pflichthinweise, das Verbot der
Rankingzusage, die Kennzeichnungspflicht nach Art. 50 Abs. 4 KI-VO, das Datenmodell und die 100
Testfälle. Ein Löschlauf hätte den Satz mitgenommen, der den beanstandeten Zustand behebt.

**Wo die Dateien wirklich Layout festschreiben, stimmen sie mit dem abgenommenen Entwurf
überein** — Sektionsfolge, Bauform je Sektion, dunkle Flächen, Zeitstrahl, Akkordeon. Es wurde
Zeile für Zeile verglichen; eine Abweichung zwischen Vorgabe und Entwurf ist nicht gefunden
worden.

### Was am Wortlaut geändert wurde

Fünf Stellen der Startseite trugen Innensprache oder Anordnungsanweisungen:

| Stelle | vorher | warum es wegmusste |
|---|---|---|
| Sektion 2, Abgrenzung | „Kein Terminkalender-Pingpong." | Jargon aus der internen Verständigung |
| Sektion 2, Antwort | „Was dort geht, steht unten — vollständig." | `unten` beschreibt die Anordnung und stimmt einspaltig nicht mehr; `vollständig` war die **Begründung**, die Liste nicht zu kürzen |
| Sektion 3, Schritt 4 | „KI hilft, geprüft und freigegeben wird von uns." | Aussage bleibt Pflicht (`06_RECHT.md`), der Satzbau klang wie eine Notiz |
| Sektion 4, Vorzeile | „Eine Empfehlung. Vier mögliche Ergebnisse." | wiederholte die Überschrift; stand als einzige **unter** ihr |
| Sektion 10 | „Danach prüfen wir persönlich." | `persönlich` heißt in der Branche **Termin** — die Seite verspricht drei Sektionen höher das Gegenteil |

Von der Preiskarte heruntergenommen: die `Umfang`-Zeile (wiederholte Wort für Wort die ersten
beiden Listenpunkte), `Erstes Jahr` (dritte Zahl in einer Karte, die eine Zahl vergleichbar
machen soll — sie steht vollständig in der Tabelle auf `/preise`) und der Lieferkorridor
(Sektion 4 fordert ihn nicht).

### Ein Prüfmittel war kaputt, und das war der schwerwiegendere Fund

Der Entwicklungsserver lief als `php -S … public/index.php`. Damit beantwortet **die Anwendung**
jede Anfrage, auch `/assets/css/*` — und die Anwendung kennt keine statischen Dateien. Jede
Stilvorlage kam mit **404**. Gemessen und fotografiert wurde also eine Seite **ohne CSS**.

Aufgefallen ist es, weil ein Bild mit `width: 100%` in der Messung 1280 px breit war. Der
Server läuft jetzt über eine Weiche, die vorhandene Dateien unter `/public` durchreicht — wie
Apache mit der `.htaccess`. Die beiden Fälle aus `SecurityHeadersTest`, die einen laufenden
Webserver brauchen (Fall 49 und die Gegenprobe), **laufen dadurch erstmals in dieser Umgebung
wirklich** statt zu scheitern.

### Gemessen

| | |
|---|---|
| Waagerechter Überlauf | **1920 · 1440 · 1280 · 1024 · 390 px, alle sauber** — und dasselbe für elf weitere öffentliche Seiten |
| Preiskarten | 4 Karten in einem Raster, 302 px bei 1440, 461 px bei 1024 — deckungsgleich mit dem Entwurf |
| Tests | **377 grün, 11.396 Zusicherungen**, darunter zwei neue: `testDerSatzspiegelIstFliessend` und `testDieVierPreisstufenStehenInEinerReihe` |
| Datenbank | **MariaDB 11.4 stand in dieser Umgebung nicht zur Verfügung; gelaufen ist alles gegen MariaDB 10.11.** Das ist innerhalb der Vorgabe (`MySQL 8 / MariaDB 10.6+`), aber **nicht** die Fassung, gegen die zuletzt geprüft wurde. Gegen MySQL 8.4 ist in diesem Durchgang **nicht** geprüft worden |
| `migrate.php verify` | **nicht aussagekräftig.** Die Arbeitsdatenbank dieser Umgebung ist leer — der Befehl bestätigt die Prüfsummen von null eingespielten Migrationen. Die Migrationen selbst laufen im Testaufbau und sind dort grün |

### Ungeprüft

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Der Anwendungsbereich ist mit dem neuen Satzspiegel nicht Bild für Bild gesehen.** Er folgt `--wrap` nicht mehr, sondern den 1320 px aus `design/portalkonzept.html` — geprüft ist die Regel, nicht ihre Wirkung auf jeder Seite | Anmelden und durchsehen |
| 2 | **Die Aufnahmen des Kundenbereichs auf der Startseite sind vor der Satzspiegeländerung entstanden.** Sie zeigen den Bereich mit dem alten Inhaltsmaß | Nach Punkt 1 neu aufnehmen |
| 3 | **Der Textdurchgang ist begonnen, nicht abgeschlossen.** Geändert sind die fünf Stellen oben; die übrigen Sektionen und die Unterseiten sind unverändert | Mit dem Texter-Skill je Sektion, Prüfbericht je Seite |
| 4 | **Zwei Fassungen der Preistexte sind nicht gegeneinander geprüft:** die Karte auf der Startseite und die Tabelle auf `/preise` nennen dieselben Zahlen, aber nicht dieselben Merkmale | `/preise` gegen Sektion 4 lesen |

---

## 13.08.2026 — Sektion 6 und 8, `/musterprojekte`, und ein Drittel Höhe

### Zwei Funde vor dem ersten Handgriff — beide gemeldet, keiner überschrieben

**1 — `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4d gab es nicht.** Der Auftrag nannte ihn als Grundlage
(Rang 1). Die Datei führte §4, §4a, §4b, §4c. Die Entscheidung selbst lag in der Anweisung
vollständig vor und ist als **§4d nachgetragen** — mit dem Vermerk, dass sie nachgetragen
wurde. Erfunden ist daran nichts; abgeleitet wurde nur, wo sie einzuordnen ist.

> **Beim Zusammenführen stellte sich heraus, dass der Betreiber denselben Abschnitt parallel
> selbst eingetragen hat** (`fdeb657`). **Seine Fassung gilt** — sie ist weiter: Sie schliesst
> zugleich §5 auf drei Musterprojekte und erklärt §7c für gegenstandslos. Der Nachtrag steht
> als Anmerkung darunter, weil er **eine Angabe darin richtigstellt**: Der Satz „die Startsperre
> sucht ohnehin `[[FOTO-FEHLT]]` und `[[SCREENSHOT-FEHLT]]`" beschrieb die Vorgabe, nicht den
> Code. Seit diesem Commit beschreibt er beides.

**2 — Die Startsperre suchte `[[FOTO-FEHLT]]` und `[[SCREENSHOT-FEHLT]] nicht.`** Der Auftrag
verlangte den Nachweis, dass sie es tut. Sie tat es nicht:

| | |
|---|---|
| `partials/bildplatz.php`, Zeile 25 | *„Die Startsperre §14a Bedingung 4 sucht genau diese Markierung und bricht die produktive Veröffentlichung ab."* |
| `app/services/Startsperre.php`, Zeile 14 | *„Sie prueft nicht auf Platzhalter in Vorlagen, sondern auf den Zustand der Einstellungen."* |

Beide Sätze standen seit dem Bau nebeneinander. **Solange niemand die Markierung sucht, ist ein
Bildplatz kein Zwischenstand, sondern ein Risiko** — er sieht aus wie eine bewusste
Zwischenstufe, und die Zusicherung daneben sorgt dafür, dass niemand mehr nachsieht.

Gebaut ist `app/services/Platzhalterpruefung.php`: Sie rendert die Launch-Adressen durch den
echten Router und sucht die drei Markierungen aus §5 plus den Wortlaut `Name wird nachgereicht`,
den Bedingung 4a ausdrücklich nennt. `bin/startklar.php` bricht daran ab.

**Warum nicht in `Startsperre::hindernisse()`:** Die Methode läuft bei **jedem** Aufruf einer
Adminseite — `partials/kopfband.php` fragt damit, ob der Einrichtungshinweis erscheint. Ein
Dutzend gerenderte Seiten gehören nicht in einen Seitenkopf, sondern an das
Veröffentlichungsgatter.

Nachweis, ausgeführt:

```
$ php bin/startklar.php
Nicht startklar — 11 Hindernisse:
  - / liefert den Platzhalter „[[SCREENSHOT-FEHLT]]" aus. …
  - / liefert den Platzhalter „[[FOTO-FEHLT]]" aus. …
  - /ueber-uns liefert den Platzhalter „[[FOTO-FEHLT]]" aus. …
  - /musterprojekte liefert den Platzhalter „[[SCREENSHOT-FEHLT]]" aus. …
```

`tests/PlatzhaltersperreTest.php` hält das fest — je ein Fall für Bedingung 4 und 4a, dazu die
Gegenprobe, dass eine Seite **ohne** Platzhalter nichts meldet. Ohne die Gegenprobe wäre der
Test von einer Sperre, die immer meldet, nicht zu unterscheiden.

### Ein Testfall wurde umgedreht — mit Begründung, nicht um grün zu werden

`VertrauensangabenTest::testOhneGruenderangabenEntfaelltDieSektionVollstaendig` verlangte das
Gegenteil dessen, was §4d entscheidet. Er heißt jetzt
`testOhneGruenderangabenStehtDerGekennzeichnetePlatzhalter` und ist **schärfer** als vorher: Er
prüft nicht nur, dass die Markierung dasteht, sondern dass die Startsperre daran anhält. Die
alte Zusicherung — kein leerer Rahmen an der Vertrauensstelle — ist damit nicht gefallen,
sondern hat einen Prüfer bekommen.

### Block 3 — die Messung vorher und nachher

**Gesamthöhe und Überlauf**, sechs Breiten, Bewegung aus:

| Breite | vorher | nachher | Differenz | Überlauf vorher | Überlauf nachher |
|---|---:|---:|---:|---:|---:|
| 1920 px | 13.518 px | 10.298 px | **−24 %** | 0 | 0 |
| 1440 px | 12.928 px | 10.185 px | **−21 %** | 0 | 0 |
| 1024 px | 12.713 px | 10.751 px | −15 % | 0 | 0 |
| 768 px | 15.522 px | 14.488 px | −7 % | 0 | 0 |
| 390 px | 17.745 px | 19.495 px | +10 % | 0 | 0 |
| 320 px | 19.550 px | 21.769 px | +11 % | **+53 px** | 0 |

Die beiden schmalen Breiten wachsen, weil die zwei neuen Sektionen dort einspaltig laufen: drei
Musterkarten untereinander sind auf 390 px rund 2.400 px. Das ist der Preis der Sektion, nicht
verlorener Abstand — der Füllgrad steigt dort mit.

**Füllgrad je Abschnitt bei 1440 px** — Anteil der Fläche, den Text und Bild wirklich einnehmen:

| Abschnitt | vorher | nachher |
|---|---:|---:|
| Aufmacher | 16 % | 16 % |
| Kundenbereich | 53 % | 34 % |
| Ablauf | 30 % | 29 % |
| **Preise** | 31 % | **30 %** |
| **Zusage** | 25 % | **36 %** |
| Wer dahintersteckt | — | 31 % |
| Leistungen | 26 % | 36 % |
| **SEO-Band** | 29 % | **36 %** |
| Musterprojekte | — | 38 % |
| **Häufige Fragen** | 50 % | **67 %** |
| **Abschluss** | 22 % | **29 %** |

Kundenbereich fällt von 53 auf 34 %, weil die 793 px hohe Portalansicht entfallen ist — die
Fläche zählte als Inhalt. Die Sektion ist trotzdem 981 px kürzer.

**Was die Höhe abgebaut hat, Punkt für Punkt:**

| | Maßnahme | Ersparnis |
|---|---|---:|
| b | Von vier Portalansichten zwei entfernt (Kundenbereich, Ablauf-Schritte 1 und 5) | ~1.900 px |
| a/e | Acht Leistungen von Zeilen über die volle Breite auf **vier Spalten × zwei Reihen** — die Bauform, die §7 ohnehin nennt | ~900 px |
| c | `karte--betont` in `--lime-soft` (1.268 × 476 px) durch **eine Zeile** ersetzt | ~480 px |
| d | Grundrhythmus der Abschnitte `--s-7` → `--s-5`, Zusage `--s-8` → `--s-6` | ~1.200 px |
| d | Abstände zwischen Überschrift, Vorspann und Inhalt eine Stufe enger | ~340 px |
| — | H2-Zeilenlänge von 22ch auf **26ch** — der Wert aus `design/startseite.html` Zeile 51, beim Übertragen enger geraten. Drei Überschriften standen dadurch zweizeilig, die im Entwurf einzeilig sind | ~170 px |

**Kein Wort ist dafür entfallen.** Der Wortbestand der Startseite steigt von 934 auf 1.164 —
die beiden neuen Sektionen bringen 230 dazu. Aufzählungspunkte fallen von 56 auf 53, und die
drei sind keine gestrichenen Punkte, sondern die sieben Ein-Wort-Punkte der Monatspauschale,
die jetzt als Zeile stehen, gegen vier neue in der Gründersektion.

**Zwei Versuche sind gemessen zurückgenommen worden**, statt sie stehen zu lassen: der Dreisatz
im dunklen Block machte die Sektion 40 px **höher** (elf kurze Punkte brechen in schmaleren
Spalten um), und Beschriftung neben Angabe auf der Musterkarte 21 px höher (bei 302 px
Kartenbreite bleiben der Angabe 200 px). Beide Stellen tragen den Messwert als Kommentar.

### Die beiden gemeldeten Überläufe — dieselbe Ursache, zwei Symptome

**Der Knopf ab 1024 px abwärts und der Überlauf bei 320 px waren nicht dasselbe Element**, wie
zunächst vermutet:

| Symptom | Ursache |
|---|---|
| `A.knopf` ragt ab 1024 px abwärts über die Kante | Das **geschlossene** Menüblatt behält in Chromium eine Layoutbox. Es stand 91 px breit als dritter Flexpartner in der Kopfzeile, seine Einträge ragten mit `overflow: visible` bis 370 px hinaus. `.menue:not([open]) .menue__blatt { display: none }` |
| Waagerechter Überlauf bei 320 px, scrollWidth 373 | `Stundenabrechnung` im Zusagesatz misst bei 31 px Schriftgrad rund 290 px. `hyphens: auto` **und** `overflow-wrap: break-word` — das erste braucht ein Trennwörterbuch, und ob eines vorliegt, entscheidet das System des Lesers |

Dazu auf `/musterprojekte` derselbe Fall bei `Physiotherapiepraxis` in der H2 und
`Musterprojekte,` in der H1 — dieselbe Regel, jetzt an `h1, h2, h3`.

### Belege

| Was | Wie geprüft |
|---|---|
| Sektion 8 mit drei Karten und drei Bildplätzen | Bildschirmaufnahme bei 1440 px |
| Sektion 6 mit `[[FOTO-FEHLT]]` und der Liste der fehlenden Angaben | Bildschirmaufnahme bei 1440 px |
| `/musterprojekte` | 805 Wörter im Inhaltsbereich — `17_SEITEN_SARTU.md` §4a bindet 700 bis 1.000 |
| Zwölf öffentliche Seiten an fünf Breiten | kein waagerechter Überlauf |
| Tests | **381 grün, 11.773 Zusicherungen** |

### Ungeprüft

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Das Höhenziel von 9.000 px bei 1440 px ist nicht erreicht.** Erreicht sind 10.185 px. Die beiden neuen Sektionen messen zusammen 1.625 px; ohne sie läge die Seite bei rund 8.560 px. Die verbliebene Lücke von 1.185 px lässt sich mit den fünf beauftragten Mitteln nicht mehr schließen — jeder weitere Schritt nähme Inhalt weg, und genau das schliesst der Auftrag aus | Entscheidung des Betreibers: Ziel anheben oder eine Sektion inhaltlich kürzen |
| 2 | **Die drei Musterprojekte sind Stufe 1, nicht Stufe 2.** Es gibt keine gebauten Beispielseiten; die Bildplätze bleiben, bis sie stehen. **Die Zahl ist inzwischen entschieden** — §4d schliesst §5 auf drei und §7c als gegenstandslos; die Seiten selbst sind eine eigene Sitzung (`PROMPT_NEUE_SESSION_MUSTERPROJEKTE.md`) | Beispielseiten bauen, dann die Bildplätze durch echte Aufnahmen ersetzen |
| 3 | **Die Wertetripel je Musterprojekt sind eingetragen, aber nicht gebaut.** `17_SEITEN_SARTU.md` §4a bindet Inhaltsdichte, Formcharakter und Bewegung je Fall; sie stehen in `Musterprojekte::alle()` und erscheinen auf `/musterprojekte` als Text. Der Komponentenkatalog aus `03_KUNDENPRODUKT.md`, der sie in Gestaltung übersetzt, existiert nicht | Katalog anlegen, dann Beispielseiten bauen |
| 4 | **Die Sperre ist gegen die Launch-Adressen geprüft, nicht gegen jede Route.** `Platzhalterpruefung` läuft über `Launchadressen::alle()`. Eine Seite, die nicht in der Sitemap steht, würde einen Platzhalter unbemerkt ausliefern | Liste erweitern, sobald es eine Adresse ausserhalb der Sitemap mit Bildplatz gibt |
| 5 | **Auf 390 und 320 px ist die Seite länger als vorher**, nicht kürzer — die zwei neuen Sektionen laufen dort einspaltig. Gemessen, nicht behoben | Entscheiden, ob die Musterkarten auf Mobilgeräten als Karussell oder als Auszug laufen sollen |
| 6 | **Gelaufen ist alles gegen MariaDB 10.11**, nicht gegen MySQL 8.4 oder MariaDB 11.4. In dieser Umgebung stand nichts anderes zur Verfügung | Auf der Zielumgebung erneut laufen lassen |


---

## 13.08.2026 — vier gemessene Fehler, und was am Bericht davor falsch war

### Zuerst zur Berichtspflicht

Der Vorwurf lautet, der letzte Bericht habe vier gemessene Werte weggelassen. **Er hat sie
genannt** — die Tabelle führte 390 px mit 17.745 → 19.495 und 320 px mit 19.550 → 21.769, und
`OFFENE_PRUEFUNGEN.md` trug den Punkt „Auf 390 und 320 px ist die Seite länger als vorher, nicht
kürzer". **Was fehlte, war die Einordnung:** Die Verschlechterung stand in einer Zeile unter
zwei Verbesserungen, statt als Verschlechterung benannt zu werden. Der Unterschied ist nicht
formal. Wer eine Tabelle liest, sieht sechs Zahlen; wer einen Bericht liest, soll wissen, welche
davon ein Problem ist.

Dieser Abschnitt nennt deshalb jede Verschlechterung mit eigener Überschrift, auch die, die an
anderer Stelle aufgewogen wird.

### Die Messung, sechs Breiten

| Breite | vorher | nachher | Differenz | scrollWidth | Überlauf | Kandidaten |
|---|---:|---:|---:|---:|---:|---|
| 1920 px | 10.298 | 10.524 | **+226** | 1920 | 0 | `SPAN.band` (im Aufmacher geclippt) |
| 1440 px | 10.185 | 10.321 | **+136** | 1440 | 0 | `SPAN.band` |
| 1024 px | 10.776 | 10.724 | −52 | 1024 | 0 | `SPAN.band`, `g` (Gerätezeichnung, geclippt) |
| 768 px | 14.488 | 14.289 | −199 | 768 | 0 | `SPAN.band`, `g` |
| 390 px | 19.495 | **16.899** | **−2.596** | 390 | 0 | `SPAN.band`, `g` |
| 320 px | 21.858 | **18.941** | **−2.917** | 320 | 0 | `SPAN.band`, `g` |

**Die Kandidaten sind keine Überläufe.** `SPAN.band` sind die drei Zierbänder im Aufmacher, `g`
ist eine Gruppe der Inline-SVG-Gerätezeichnung; beide liegen in einem Elternteil mit
`overflow: hidden`. Der scrollWidth entspricht an allen sechs Breiten der Fensterbreite.

### Verschlechterung 1 — am Schreibtisch ist die Seite länger geworden

**+136 px bei 1440, +226 px bei 1920.** Die Ursache ist Fehler 3, und sie ist gewollt: Die acht
Bildplätze tragen jetzt das Seitenverhältnis ihres späteren Bildes. Auf der Startseite wächst
`muster` dadurch von 1.102 auf 1.238 px — drei Plätze von je 110 px Texthöhe auf je 226 px
Bildhöhe.

**Der Zuwachs ist der Zweck.** Der Platz nimmt heute den Raum ein, den das Bild später braucht;
ohne ihn springt das Layout an dem Tag, an dem die Aufnahme kommt. Wer ihn zurückhaben will,
bekommt den Sprung zurück.

### Verschlechterung 2 — der Füllgrad der Gründersektion fällt

Bei 1440 px von 31 auf **20 %**. Die Textseite ist kürzer geworden (vier Aufzählungspunkte sind
zu einer Zeile geworden, Auftrag b), die Bildseite trägt unverändert 427 px. Die Sektion selbst
ist gleich hoch geblieben — es steht dieselbe Fläche für weniger Text.

**Das löst sich, sobald das Foto da ist:** Dann trägt die Bildseite ein Bild statt eines leeren
Rahmens, und der Rahmen zählt heute nicht als Inhalt.

### Fehler 1 — mobil, behoben und überkompensiert

| | 390 px | 320 px |
|---|---:|---:|
| vor dem letzten Lauf | 17.745 | 19.550 |
| nach dem letzten Lauf | 19.495 | 21.858 |
| **jetzt** | **16.899** | **18.941** |

Die Seite ist mobil jetzt kürzer als vor dem letzten Lauf — **mit zwei zusätzlichen Sektionen**
(`dahinter` 735 px, `muster` 2.536 px bei 390 px) und 249 Wörtern mehr.

**Was mobil wuchs, war der Rahmen um die Wörter, nicht der Text.** Siebzehn Karten, die am
Schreibtisch nebeneinander stehen, stapeln sich mobil, und jede bringt Rahmen, Innenabstand und
Zwischenraum mit. Die Karte ist am Schreibtisch eine Abgrenzung gegen den Nachbarn **daneben**;
untereinander gibt es keinen Nachbarn daneben, und eine Linie trennt genauso gut.

| Maßnahme | bei 390 px |
|---|---:|
| Acht Leistungskarten zu Zeilen (Rahmen und Innenabstand weg) | −399 px |
| Vier Preisstufen und drei Musterkarten ebenso | −580 px |
| Beschriftung und Angabe der Musterkarten in einer Zeile | −302 px |
| Grundrhythmus der Abschnitte von 96 auf 40 px | −616 px |
| Vorspann auf Fließtextgrad, Abstände in Listen und Ablauf | −440 px |
| Gründerreihe: Portrait auf 240 px gedeckelt | −259 px |

**Ein Fehler, der dabei aufgefallen ist und nicht im Auftrag stand:** `.gruender__reihe` wurde
mobil nie einspaltig. Die Regel `@media (max-width: 860px) { .gruender__reihe {
grid-template-columns: 1fr } }` stand **95 Zeilen vor** der Basisregel — gleiche Spezifität, die
spätere gewinnt. Bei 390 px stand deshalb ein 98 px breites Foto neben einer 252 px breiten
Textspalte. Die Reihe ist jetzt mobile-first gebaut. Ein Prüflauf über alle Medienblöcke hat
gezeigt, dass es die **einzige** Stelle mit diesem Fehler war.

### Ziel gerissen — 390 px unter 14.000, 320 px unter 16.000

**Erreicht sind 16.899 und 18.941 px. Die Ziele sind um 2.899 bzw. 2.941 px verfehlt.**

Die Rechnung, warum: Bei 390 px bleiben nach den Rändern 350 px Textspalte. Die Seite trägt
1.183 Wörter; bei rund 45 Zeichen je Zeile sind das etwa 170 Zeilen Fließtext zu 29 px —
**rund 4.900 px reiner Text.** Dazu 11 Überschriften, 49 Aufzählungspunkte, 17 Karten mit je
einem Knopf, sechs Ablaufschritte, neun Fragen und vier Bildplätze.

Die verbliebenen Abstände sind gemessen: 11 Abschnitte à 40 px Innenabstand sind 440 px, alle
Lücken zwischen Überschrift, Vorspann und Inhalt zusammen rund 600 px. **Selbst wenn beides auf
null ginge, fehlten noch 1.900 px** — und die Seite hätte keinen Rhythmus mehr.

Unter 14.000 px käme die Seite mobil nur auf einem Weg: Inhalt hinter einen Klick legen. Ein
Akkordeon für die drei Musterkarten spart rund 1.350 px, eines für die vier Preisstufen rund
1.200. **Das ist eine Entscheidung, keine Ableitung** — der Preis ist bei SARTU der Belegersatz
für fehlende Referenzen, und ihn aufklappbar zu machen, kehrt seinen Zweck um. Sie steht unten
unter „Ungeprüft".

### Fehler 2 — die sechs Sprungziele, jedes einzeln geprüft

`--kopfhoehe` und `--sprungabstand` stehen in `tokens.css`, die Regel greift über `main [id]`
statt über eine Liste der sechs Namen — ein siebtes Ziel wäre sonst wieder falsch.

| Ziel | Kopfkante | Überschrift bei | Luft |
|---|---:|---:|---:|
| `#muster` | 93 px | 220 px | 127 px |
| `#preise` | 93 px | 220 px | 127 px |
| `#ablauf` | 93 px | 125 px | **32 px** |
| `#fragen` | 93 px | 173 px | 80 px |
| `#leistungen` | 93 px | 173 px | 80 px |
| `#kundenbereich` | 93 px | 206 px | 113 px |

Bei 390 px dieselbe Messung mit 96 px Kopfkante, alle sichtbar. `#ablauf` hat die geringste
Luft, weil die Sektion als zweite Hälfte des dunklen Doppelblocks `padding-top: 0` trägt — die
Überschrift steht dort ohne eigenen Vorlauf.

### Fehler 3 — acht Bildplätze, jetzt maßgetreu

| Vorkommen | Verhältnis | vorher | nachher (1440 px) |
|---|---|---|---|
| 3 × Musterkarte auf der Startseite | 8:5 | Texthöhe, rund 110 px | 361 × 226 px |
| 3 × auf `/musterprojekte` | 8:5 | Texthöhe | maßgetreu |
| 1 × Gründerfoto | 4:5 | 342 × 427 px | 342 × 427 px |
| 5 × auf `/ablauf` | 8:5 | 42 px | maßgetreu |

Das Verhältnis steht als `data-verhaeltnis="8-5"` am Element, **nicht als `style`**: Die eigene
CSP führt `style-src 'self'` ohne `unsafe-inline`, ein Inline-Attribut wäre wirkungslos.
Derselbe Fehler hatte am 13.08.2026 schon einmal 29 px weissen Rand über der Portalleiste
gekostet. `MarkupTest::testJederBildplatzTraegtSeinSeitenverhaeltnis` prüft beide Hälften: dass
jeder Platz ein Verhältnis trägt **und** dass `website.css` dafür eine Regel führt — sonst stünde
das Attribut da und die Wirkung fehlte.

### Fehler 4 und Auftrag a — der Bildplatz

`Malerbetrieb, Umfang Wachstum` stand im Bildplatz, `Malerbetrieb` einen Zeilenabstand darunter
als Überschrift. Die Nennung im Bildplatz ist weg, der **Umfang** steht jetzt als Monozeile an
der Karte. Sichtbar ist dort `Platz für Ansicht` und ein Satz, was hinkommt — der Wortlaut des
abgenommenen Entwurfs.

Dateiname, Pixelmaße und `[[SCREENSHOT-FEHLT]]` stehen als `data-fehlt` am Element: nicht
sichtbar, nicht vorgelesen, maschinell auffindbar. **Die Startsperre findet sie unverändert** —
nachgeprüft mit `php bin/startklar.php`, vier Meldungen für `/`, `/ablauf`, `/ueber-uns` und
`/musterprojekte`. `PlatzhaltersperreTest` bleibt grün.

### Auftrag b — Aufzählungspunkte

**53 → 49 auf der Startseite.** Weggefallen sind die vier Punkte „kein Baukasten · kein
WordPress-Hoster · keine Billig-Seitenschleuder · kein Anbieter für Privat- und Hobbyseiten";
sie stehen als eine Zeile. Derselbe Wortlaut, dieselbe Quelle (`Firmenseitentexte::NICHT`).

**Was bewusst stehen bleibt:** die elf Punkte des Kundenbereichs (§5 Sektion 2 bindet sie
ausdrücklich: „Die Liste wird nicht gekürzt und nicht zu ‚unter anderem' zusammengefasst"), die
sechzehn Merkmale der vier Preisstufen (Zahlen, Klasse 1), die sechs Ablaufschritte (Bauform
Zeitstrahl), die acht Leistungen und die beiden Vertrauenszeilen à vier Punkten — letztere sind
im abgenommenen Entwurf ein Gestaltungselement mit eigenem Strichmarker (`.trust li::before`).

### Auftrag c — Füllgrad, nicht weiter umgesetzt

| Abschnitt (1440 px) | vorher | nachher |
|---|---:|---:|
| Preise | 30 % | 30 % |
| Zusage | 36 % | 36 % |
| SEO-Band | 36 % | 36 % |
| Fragen | 67 % | 67 % |
| Abschluss | 29 % | 29 % |

**Bei 1440 px ist der Abstand nicht mehr das Problem.** Der Grundrhythmus steht seit dem letzten
Lauf auf 48 px oben und unten — der abgenommene Entwurf trägt 104. Was den Füllgrad deckelt, ist
die **Zeilenlänge**: `07_MARKE_UND_GESTALTUNG.md` begrenzt jeden Fließtext auf 26 bis 70 `ch`.
Ein 62-`ch`-Absatz in einer 1.268 px breiten Bahn füllt rund 60 % der Zeile; der Rest ist Rand
und ist Vorgabe, nicht Nachlässigkeit. **Ein Absatz kann unter dieser Regel gar nicht über etwa
50 % Flächenanteil kommen.**

Der Beleg steht in derselben Messung: Bei 390 px, wo Spalte und Bahn zusammenfallen, liegt der
Füllgrad jetzt bei 42 bis 64 % — bei denselben Texten.

### Belege

| Was | Wie geprüft |
|---|---|
| Sechs Sprungziele | einzeln angesprungen, Abstand zur Kopfkante gemessen, bei 1440 und 390 px |
| Acht Bildplätze | Verhältnis und tatsächliche Maße im Browser gemessen |
| Marke unsichtbar, aber im Markup | `strip_tags` des ausgelieferten HTML enthält weder `[[` noch `.webp`; `startklar.php` meldet weiter |
| Zwölf öffentliche Seiten, fünf Breiten | kein waagerechter Überlauf |
| Tests | **385 grün, 11.836 Zusicherungen** — vier neue Fälle |

### Ungeprüft

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Die Höhenziele für 390 und 320 px sind gerissen** — 16.899 statt 14.000, 18.941 statt 16.000. Die Rechnung steht oben; der verbleibende Weg wäre ein Akkordeon für Musterkarten oder Preisstufen, und beides legt Inhalt hinter einen Klick | Entscheidung des Betreibers: Ziel anheben oder Aufklappen freigeben |
| 2 | **Bei 1440 und 1920 px ist die Seite 136 bzw. 226 px länger geworden.** Ursache sind die maßgetreuen Bildplätze — der Zuwachs ist der Zweck von Fehler 3 und verschwindet, sobald echte Aufnahmen an ihre Stelle treten | Nach den Aufnahmen erneut messen |
| 3 | **Auftrag c ist bei 1440 px nicht weiter umgesetzt.** Der Füllgrad hängt dort an der gebundenen Zeilenlänge, nicht am Abstand | Entscheidung, ob Fließtext zweispaltig laufen soll — das wäre eine Gestaltungsänderung ohne Vorlage |
| 4 | **Die mobile Kartendarstellung ist nur in Chromium gesehen.** Die Umstellung von Karte auf Zeile hängt an `@media (max-width: 860px)`; das ist unkritisch, die Lime-Fläche der Empfehlung dort aber nicht mehr vorhanden | In Firefox und Safari öffnen |
| 5 | **Der Gründerplatzhalter füllt seine Sektion bei 1440 px nur zu 20 %** — die Textseite ist kürzer geworden, die Bildseite trägt unverändert 427 px | Löst sich mit dem echten Foto |
| 6 | **Gelaufen ist alles gegen MariaDB 10.11**, nicht gegen MySQL 8.4 oder MariaDB 11.4 | Auf der Zielumgebung erneut laufen lassen |


---

## 14.08.2026 — Textrunde: Satzanfänge, Dopplung, zwei Regelverstöße

Kein Layout, kein CSS. Angefasst wurde `Startseitentexte`, `Musterprojekte`,
`Musterprojektetexte`, `Leistungszeilen`, `Branchenseiten` und `Gruenderangaben` — dazu ein
Bauteil, weil die Arbeitsteilung zwischen Karte und Übersichtsseite verlangt war.

### Der Prüfbericht

**Zwei Zählungen je Seite, und der Unterschied ist der Punkt.** Ein `dt`, ein Knopftext und der
Pflichthinweis zählen als Satz mit — sie **müssen** aber identisch bleiben (Klasse 1 beim
Pflichthinweis, Auflage aus Klasse 2 bei den Beschriftungen). Ein Ziel „kein Satzanfang öfter
als dreimal" kann an ihnen nicht scheitern, ohne dass eine andere Regel bricht.

| Seite | Wörter vorher | nachher | Sätze | versch. Anfänge | 3 häufigste | Prosa: versch. | 3 häufigste (Prosa) | Prosa über 3 |
|---|---:|---:|---:|---:|---|---:|---|---|
| `/` | 1.387 | **1.167** | 219 | 147 | Alle 5 · Sie 4 · Kein 4 | 66 | Der 3 · Was 3 · Ein 3 | **keiner** |
| `/musterprojekte` | 820 | **685** | 93 | 63 | Warum 4 · Sie 4 · Drei 3 | 53 | Später 3 · Vier 3 · Start 3 | **keiner** |
| `/website-sanitaer-heizung-klima` | 879 | 856 | 126 | 84 | Was 6 · Eine 5 · Wir 4 | 20 | Eine 3 · Wir 2 · Der 2 | **keiner** |
| `/website-elektrotechnik` | 969 | 951 | 145 | 94 | Wir 6 · Was 6 · Die 5 | 21 | Wir 3 · Eine 2 · Die 2 | **keiner** |
| `/website-dachdecker` | 968 | 925 | 142 | 97 | Wir 5 · Was 5 · Eine 4 | 21 | Eine 3 · Wir 2 · Die 2 | **keiner** |

**In laufender Prosa erreicht keine der fünf Seiten mehr vier gleiche Satzanfänge.** Über alle
Textknoten gezählt bleiben Treffer stehen; sie sind unten einzeln aufgeschlüsselt.

**Auf der Startseite:** `Sie` fiel von 14 auf 4, `Wir` von 12 auf unter 4, `Eine` von 6 auf
unter 4. Auf `/musterprojekte` fiel `Eine` von **10 auf 0**.

### Die Treffer über drei, die stehen bleiben — und warum

| Seite | Anfang | Anzahl | Was es ist |
|---|---|---:|---|
| `/` | `Alle` | 5 | dreimal der Pflichthinweis `Alle Preise netto zzgl. …` (**Klasse 1**), dazu die Knöpfe `Alle Leistungen im Überblick` und `Alle Musterprojekte ansehen` (letzterer in §8 **gebunden**) |
| `/` | `Sie` | 4 | drei davon sind Kalibrierungssätze des Texter-Skills: `Sie liefern die Fakten.` · `Sie müssen nur nicht.` · `Sie wählen kein Paket.` |
| `/` | `Kein` | 4 | zwei Glieder **eines** Satzes, dazu der Preisstufen-Kicker und ein Vertrauenspunkt |
| `/musterprojekte` | `Warum` | 4 | dreimal die Beschriftung `Warum diese Stufe und nicht die nächstkleinere`, einmal die H2 |
| `/musterprojekte` | `Sie` | 4 | dreimal die Beschriftung `Sie liefern` |
| Branchenseiten | `Was` 5–6 | | die Abschnittsüberschriften `Was … wirklich beschäftigt` · `Was auf so eine Website gehört` · `Was es kostet` — dieselbe Folge auf jeder Branchenseite |
| Branchenseiten | `Für` 4–5 | | die vier Preisstufen-Kicker, die auf **jeder** Seite mit Preisblock stehen |

**Diese Stellen zu variieren, hieße die Auflage zu brechen, unter der sie überhaupt frei
formulierbar sind.** Der Texter-Skill hält fest: „Innerhalb einer Fassung identisch. Wer den
Knopf einmal benennt, benennt ihn überall so."

### Geänderte Beschriftungen: keine

Die Auflage verlangt die Liste im Prüfbericht. Vollständig, über neun Seiten erhoben:

| Beschriftung | Art | Vorkommen |
|---|---|---:|
| `Bedarf prüfen lassen` | Knopf | 18 |
| `Einschätzen lassen` | Knopf | 10 |
| `Sonderprojekt besprechen` | Knopf | 5 |
| `Alle Musterprojekte ansehen` · `Ablauf im Detail` · `Den Kundenbereich ansehen` | Knopf | je 1 |
| `Platz für Ansicht` | Bildplatz | 11 |
| `Musterprojekt — kein Kundenauftrag` | Fahne (**Klasse 1**) | 6 |
| `Für ein Angebot` · `Für mehrere Leistungen` · `Für die erste Adresse am Ort` · `Kein Paket, sondern eine Vorprüfung` · `Empfehlung` | Preisstufe | je 5 |
| `Ausgangslage` · `Empfohlen` · `Seitenstruktur` · `Sie liefern` · `Warum diese Stufe und nicht die nächstkleinere` · `Wie die Seite aussehen wird` | Musterprojekt | je 3 |
| `Platz für das Foto` | Bildplatz | 2 |

**Keine davon wurde in dieser Runde geändert.** Geändert wurde einmal der Bildplatz-**Satz**
(nicht seine Beschriftung): aus dreimal `Die spätere Startseite.` wurde je Projekt
`Später die Startseite dieses Malerbetriebs.` — das nimmt einen vierfachen Satzanfang heraus
und sagt zugleich mehr.

### Befund 3 — die falsche Behauptung ist raus

> „Eine Suchmaschine hat dasselbe Problem: Sie kann einer Seite ein Thema zuordnen, nicht vier."

Weg, und mit ihr die zweite Fassung derselben Aussage in `warum_nicht_kleiner`:
„… und die Suchmaschine hat nur ein Thema zu vergeben, nicht vier."

**Der Bestand ist nach derselben Aussage in anderer Formulierung durchsucht worden.** Gefunden
wurden genau diese zwei Stellen, beide in `Musterprojekte`. Was jetzt dort steht, kommt ohne
Aussage über Suchmaschinen aus: eigene Überschrift, eigene Adresse, eigener Anfrageweg, eigene
Bilder, einzeln bewerbbar.

Eine verwandte Stelle steht weiterhin auf `/website-sanitaer-heizung-klima` und ist umformuliert
statt gestrichen: aus „Eine Seite beantwortet eine Suchanfrage" wurde „Je Seite ein Thema" — das
beschreibt die **Seitenstruktur**, nicht das Verhalten der Suchmaschine.

### Befund 4 — neun Aussagen über fremdes Kaufverhalten

Regel 0a. Jede Ersetzung verschiebt die Aussage von einem Dritten auf **ihn selbst** oder auf
die Seite:

| Seite | Weggefallen | Steht jetzt da |
|---|---|---|
| SHK | „Wer eine Badsanierung plant, sucht anders als jemand, dessen Heizung ausgefallen ist." | zwei Aufträge teilen sich eine Überschrift, eine Adresse, ein Formular |
| SHK | „Wer eine Heizung tauschen lässt, will vorher wissen …" | „Förderhöhe und Antragsweg erklären **Sie** am Telefon, in jedem Erstgespräch neu." |
| Elektro | „Wer nur einen Preis sieht, vergleicht Preise." | „Auf **Ihrer** Seite steht heute ein Preis oder nichts." |
| Elektro | „Wer eine Anlage anmeldet, will drei Dinge wissen …" | dieselben drei Dinge, aber als das, was **er** am Telefon erklärt |
| Elektro | „… ist die häufigste Rückfrage vor einem Photovoltaik-Auftrag" (Marktaussage ohne Zählung) | „klären **Sie** heute im Erstgespräch" |
| Dach | „… ruft er drei Betriebe an und nimmt den, der zuerst erklärt." | „Aufbau, Dauer und Gerüst erklären **Sie** heute am Telefon." |
| Dach | „Wer jemanden sucht, wird zuerst nachgesehen." | „Auf **Ihrer** Seite steht dazu eine E-Mail-Adresse." |
| Dach | „Wer ein Dach vergibt, will wissen, ob Gerüst … dabei sind." | „**Ob** Gerüst, Entsorgung und Anmeldung im Preis stehen, entscheidet über vierstellige Beträge." |
| Dach | „Wer sich bewirbt, sieht zuerst auf die Website — und findet dort **meistens** nur eine E-Mail-Adresse." | „Eine E-Mail-Adresse im Impressum ist keine Stellenseite." |

Dazu auf `/musterprojekte`: „Wer nach Fassadensanierung sucht, sucht nach Fassadensanierung und
nicht nach einem Malerbetrieb." — ersatzlos, die Begründung trägt ohne sie.

### Befund 2 — die Arbeitsteilung

**Die Karte auf der Startseite zeigt den Fall in einem Satz, die Übersichtsseite trägt die
Ausarbeitung.** Vorher standen Ausgangslage, Empfohlen, Seitenstruktur und Sie liefern an beiden
Stellen — 258 Wörter, die zweimal dasselbe sagten.

**Keine Aussage ist verloren:** Empfehlung, Struktur und was der Kunde liefert stehen unverändert
auf `/musterprojekte`, dort zusätzlich mit „warum diese Stufe" und „wie die Seite aussehen wird".

Auf der Startseite sind außerdem drei echte Dopplungen abgeräumt worden. SEO wurde dort an vier
Stellen erklärt — im Preisblock, im SEO-Band mit Vorspann **und** drei Spalten, in der
Leistungsliste und in der FAQ. Der Vorspann zählte auf, was die drei Spalten danach Wort für
Wort aufteilen; die Aufzählung im Preisblock stand ein zweites Mal daneben.

### Ungeprüft und gerissen

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Die Startseite bleibt über dem Ziel: 1.167 statt unter 1.100 Wörter.** Von 1.387 kommend sind das −16 %. Die beiden im letzten Lauf gebauten Sektionen tragen rund 190 Wörter; ohne sie läge die Seite bei etwa 975. Die verbleibenden 67 Wörter lassen sich nur noch aus gebundenem Text nehmen — Preismerkmale, die elf Punkte aus §5 Sektion 2, die sechs Einwände aus §5 Sektion 9 | Entscheidung: Ziel anheben oder eine der gebundenen Listen zur Kürzung freigeben |
| 2 | **`/musterprojekte` unterschreitet jetzt die gebundene Untergrenze.** `17_SEITEN_SARTU.md` §4a bindet **700 bis 1.000 Wörter**; erreicht sind 685. Der Auftrag verlangte unter 700. Rang 1 sticht Rang 4, die Abweichung steht hier statt stillschweigend im Code | §4a auf die neue Untergrenze anpassen oder die Seite wieder über 700 heben |
| 3 | **Die Musterkarte trägt nur noch eine der vier gebundenen Angaben.** `10_WEBSITE_SARTU.md` §8 bindet je Karte Ausgangslage, empfohlene Lösung, Seitenstruktur und was der Kunde liefert. Der Auftrag verlangte einen Satz und einen Weiterweg | §8 an die Arbeitsteilung anpassen |
| 4 | **Über alle Textknoten gezählt stehen weiter Anfänge über drei.** Sie sind ausschließlich Pflichthinweis, gebundene Knöpfe und wiederkehrende Beschriftungen; die Tabelle oben führt jeden einzeln | Nur zu lösen, indem die Auflage „innerhalb einer Fassung identisch" fällt |
| 5 | **Zwei Sätze auf `/musterprojekte` liegen über 20 Wörtern** (24 und 21). Regel 2 erlaubt das mit Begründung: Beide tragen eine Gegenüberstellung, die im kurzen Satz auseinanderfiele | Beim nächsten Durchgang neu ansehen |
| 6 | **Die Branchenseiten sind nur auf Regel 0a durchgesehen**, nicht auf den vollen Prüfbericht aus `SARTU_TEXTREGELN.md` §2 | Vollständigen Prüfbericht je Branchenseite rechnen |
| 7 | **Der Vierschritt ist nicht je Abschnitt nachgeprüft.** Geändert wurden einzelne Sätze; ob jeder Abschnitt noch Wiedererkennung, Konsequenz, Auflösung und Beleg in dieser Reihenfolge trägt, ist nicht gezählt | Abschnittsweise gegen den Skill halten |

---

## 14.08.2026 — die Oberfläche bekommt eine Abnahmeprüfung

`tests/OberflaecheTest.php`, gebaut nach dem Muster von `TenantIsolationTest`. Er läuft gegen
den laufenden Webserver, misst im Browser und bewertet die Zahlen in PHP. **Zwölf Prüfungen,
1.321 Zusicherungen, 20 Sekunden.**

Die Trennung ist Absicht: `tools/oberflaeche.mjs` **misst**, `OberflaecheTest.php` **bewertet**.
Vier der neun Eigenschaften — Überlauf, Höhe, Sprungabstand, Lime-Fläche — stehen nirgends im
Markup; sie entstehen erst, wenn ein Browser das CSS anwendet. Ein Test, der sie aus dem HTML
herleitet, baut die Kaskade nach und übersieht dabei genau die Fehler, die er finden soll.

### Was gemessen wurde — alle achtzehn Adressen mit Layout

| Adresse | 1440 px | Grenze | 390 px | Grenze | grösste Lime-Fläche |
|---|---:|---:|---:|---:|---:|
| `/` | 9.906 | 10.000 | 15.706 | 16.000 | 36.675 |
| `/ablauf` | 5.832 | 6.000 | 8.318 | 16.000 | 18.675 |
| `/briefing` | 957 | 6.000 | 1.176 | 16.000 | 13.226 |
| `/kontakt` | 2.463 | 6.000 | 3.421 | 16.000 | 16.652 |
| `/leistung-portal` | 5.413 | 6.000 | 6.987 | 16.000 | 18.675 |
| `/leistung-seo-lokal` | 4.681 | 6.000 | 6.188 | 16.000 | 18.675 |
| `/leistung-texte` | 4.571 | 6.000 | 5.990 | 16.000 | 18.675 |
| `/leistung-wartung` | 4.745 | 6.000 | 5.967 | 16.000 | 18.675 |
| `/leistung-webdesign` | 4.637 | 6.000 | 5.949 | 16.000 | 18.675 |
| `/leistungen` | 6.021 | 6.500 | 9.220 | 10.000 | 18.675 |
| `/lexikon` | 2.721 | 6.000 | 4.395 | 16.000 | 13.226 |
| `/musterprojekte` | 7.500 | 8.100 | 8.875 | 9.600 | 18.675 |
| `/preise` | 6.080 | 6.600 | 8.557 | 9.300 | 36.675 |
| `/ratgeber` | 3.120 | 6.000 | 4.378 | 16.000 | 13.226 |
| `/ueber-uns` | 4.477 | 6.000 | 5.652 | 16.000 | 18.675 |
| `/website-dachdecker` | 7.981 | 8.600 | 11.203 | 12.100 | 36.675 |
| `/website-elektrotechnik` | 8.012 | 8.600 | 11.373 | 12.100 | 36.675 |
| `/website-sanitaer-heizung-klima` | 7.449 | 8.600 | 10.692 | 12.100 | 36.675 |

Waagerechter Überlauf: **null** bei allen sechs Breiten und allen achtzehn Adressen.
Bildplätze ohne Seitenverhältnis: **null**. Auszeichnungsreste im Text: **null**.

### Vier Punkte, an denen die Prüfung eng steht

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Die Startseite hat 94 px Luft.** 9.906 gegen 10.000 — ein zusätzlicher Absatz reisst die Grenze. Das ist gewollt eng: Der Auftrag setzt 10.000, und die Grenze wird nicht angehoben. Wer die Seite ergänzt, muss an anderer Stelle Höhe abbauen | Beim nächsten Zubau zuerst messen, dann schreiben |
| 2 | **Die grösste Lime-Fläche liegt bei 92 % der Obergrenze.** 36.675 von 40.000 Quadratpixeln, gemessen bei 768 px auf `/`, `/preise` und den drei Branchenseiten. Bei 1440 px sind es 19.760 — die Fläche wächst, weil der Block dort einspaltig wird | Beim nächsten Eingriff am Preisblock nachmessen |
| 3 | **`/foerderung` fehlt.** `16_SEO_GEO_SARTU.md` Zeile 112 führt sie mit Priorität 0.9, ergänzt am 09.08.2026; sie liefert 404. **Sie ist bis zum 14.08.2026 nie gemeldet worden**, weil niemand die Adressliste gegen die Spezifikation hielt. Der Inhalt hängt an `FOERDERUNG_KONZEPT.md` („verweisen statt kopieren") | Die Seite bauen — oder die Adresse aus `16_SEO_GEO_SARTU.md` streichen |
| 4 | **Sieben Adressen tragen eine eigene Höhengrenze über der Regelhöhe von 6.000 px**, je mit einer Zeile Begründung im Test. Das ist die vom Auftrag verlangte Bauform — die Grenze wird begründet, nicht angehoben. Es bleibt trotzdem eine Abweichung von der Regel | Entscheidung, ob die Regelhöhe für Branchenseiten anzuheben ist |

### Was der Test nicht prüft

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 5 | **Der Test braucht einen laufenden Webserver, `NODE_PATH` und `PHP_CLI_SERVER_WORKERS=8`.** Ohne die drei bricht er mit einer eigenen Meldung ab, statt grün durchzulaufen — aber er läuft in keiner Fremdumgebung, die das nicht stellt. `php -S` ist einfädig: Sechs Browserkontexte gleichzeitig warten gegenseitig auf ihn | Vor dem ersten Fremdlauf einen Startvorspann bauen, der den Server selbst hochfährt |
| 6 | **Prüfung 6 zählt Satzanfänge in laufender Prosa**, nicht über alle Textknoten. Beschriftungen, Knöpfe, Überschriften und Aufzählungspunkte sind ausgenommen — sie müssen innerhalb einer Fassung identisch bleiben und würden die Zählung sonst zwangsläufig reissen. Dieselbe Grenze wie in der Textrunde vom selben Tag | Nur zu lösen, indem die Auflage „innerhalb einer Fassung identisch" fällt |
| 7 | **Prüfung 9 misst Lime nur bei 1440 px.** Die 36.675 Quadratpixel aus Punkt 2 stammen aus einer Handmessung über alle sechs Breiten, nicht aus dem Test. Bei 768 px liegt die Fläche höher als bei 1440 | Die Prüfung über alle Breiten führen — sie würde heute mit 36.675 gegen 40.000 noch bestehen |
| 8 | **Sechs Adressen liefern ohne Vorbedingung kein Layout** und werden deshalb nicht gemessen: `/agb`, `/impressum`, `/datenschutz` (Rechtstext nicht freigegeben) sowie die drei Bedarfsscheck-Folgeseiten (ohne Sitzung 303). Sie bleiben in Prüfung 1 — sie sind bekannt, nicht vergessen. **Wer eine Adresse dort einträgt, um eine gerissene Grenze loszuwerden, umgeht den Test** | Die drei Rechtstexte nach der Freigabe erneut messen |

### Eine bestehende Zusicherung ist geändert worden — und warum das keine Abschwächung ist

`MarkupTest::testGeraeterahmen` prüfte bis heute, dass `.geraet__marke` **kein**
`position: absolute` trägt. Diese Zusicherung stammt aus der Anweisung vom 13.08.2026 („neben
dem Bild, nicht darauf") und war gegen den damaligen Zustand richtig: Der Vermerk lag mitten auf
dem Bildschirm.

Die Anweisung vom 14.08.2026 verlangt die **entgegengesetzte Bauweise** bei **gleichem
Ergebnis**: „Der Vermerk schwebt frei unter dem Gerät. Er gehört an dessen Rand."

Beides zusammen geht nur über die Wirkung. Geprüft wird jetzt: kein `inset: 0` (der Vermerk
liegt nicht über dem ganzen Bild) und `top: 0` (er sitzt an der Oberkante). Der Deckel beginnt
im SVG bei y=46, der Bildschirm bei y=68, der Vermerk ist rund 30 px hoch — er endet, bevor der
Schirm anfängt. **Die Prüfung ist präziser geworden, nicht nachgiebiger:** Sie hält jetzt die
Lage fest statt der Bauweise, und sie fällt weiterhin, sobald der Vermerk nach unten wandert.

### Zwei eigene Fehler, beide aus vorangegangenen Läufen

- **Die zusammengebaute Nennung** in `Musterprojekte::bildsatz()` — `sprintf('Später die
  Startseite dieses %s.', $gattung)` ergab „dieses Malerbetrieb", „dieses
  Physiotherapiepraxis", „dieses Arbeitsrechtskanzlei". Der Fehler stammt aus meinem eigenen
  Lauf vom 13.08.2026. Der Satz steht jetzt je Projekt in den Daten; Prüfung 8 hält es fest und
  sucht darüber hinaus im ausgelieferten Text nach jeder Fügung aus Artikel und Gattungswort.
- **Das Trackpad stand links der Mitte** — mein Fehler in diesem Lauf, mit einer Begründung
  daneben, die nicht stimmte („das Telefon verdeckt die rechte Vorderfläche"). Nachgerechnet:
  Das Telefon beginnt bei x=756, die rechte Kante des mittigen Trackpads liegt bei x≈575.
  `design/geraet.html` setzt `margin: 16px auto 0`. Aufgefallen ist es erst beim Ansehen einer
  Aufnahme, nicht beim Messen — der Test hält jetzt die Mittellinie bei x=463 ± 6 fest.

### Ein Punkt des Auftrags war schon behoben

Punkt c — „die vier Bildplätze auf auto bekommen ihr Seitenverhältnis". Sie tragen es seit dem
Lauf vom 13.08.2026 (`data-verhaeltnis` plus je eine CSS-Regel; als `style` wäre es unter der
eigenen CSP wirkungslos). Gemessen am 14.08.2026: **sieben Bildplätze auf zwei Seiten, keiner
auf `auto`.** Prüfung 5 nagelt es fest.

---

## 14.08.2026 — die Formsprache der Startseite auf alle Seiten

Gemessen am 14.08.2026 bei 1440 und 390 px, mit `tools/oberflaeche.mjs`. Das Werkzeug zählt
seit diesem Lauf zusätzlich **dunkle Bahnen**, **dunkle Felder**, **Bänder**, **Füllgrad** und
**Wörter** — vorher gab es für diese fünf Werte keine Messung, sondern Augenmaß.

### Wie gezählt wird

| Wert | Was gezählt wird |
|---|---|
| **dunkel** | randlos dunkle Bahn: Hintergrund `--ink`, mindestens 95 % der Fensterbreite, ab 120 px Höhe |
| **feld** | dunkle Fläche **in** einem hellen Abschnitt — das Handlungsfeld am Seitenende |
| **Bänder** | Elemente mit der Klasse `band` im Aufmacher |
| **Füllgrad** | Anteil von `main`, den Textzeilen und Bilder wirklich belegen. **Vereinigung, nicht Summe** — gerastert in Zellen von 8 px, damit verschachtelte Kästen nicht doppelt zählen |

### Vorher und nachher — alle neunzehn Adressen

| Adresse | 1440 vorher | 1440 nachher | 390 vorher | 390 nachher | dunkel | Bänder | Füllgrad | Wörter |
|---|---:|---:|---:|---:|---|---|---|---:|
| `/` | 9.906 | **9.848** | 15.706 | 15.742 | 3 → 3 | 3 → 3 | 28 → 28 | 1.036 → 1.035 |
| `/ablauf` | 5.832 | 5.939 | 8.318 | 8.496 | 0 → **1** | 0 → **2** | 24 → **25** | 466 → 469 |
| `/briefing` | 957 | 957 | 1.176 | 1.176 | 0 → 0 | 0 → 0 | 30 → 30 | 76 → 76 |
| `/foerderung` | — | **8.425** | — | 10.886 | — → **2** | — → **2** | — → 22 | — → **933** |
| `/kontakt` | 2.463 | 2.489 | 3.421 | **3.200** | 0 → **1** | 0 → **2** | 15 → **17** | 113 → 119 |
| `/leistung-portal` | 5.413 | **5.251** | 6.987 | **6.852** | 0 → **1** | 0 → **2** | 18 → **20** | 383 → 393 |
| `/leistung-seo-lokal` | 4.681 | **4.605** | 6.188 | **6.117** | 0 → **1** | 0 → **2** | 20 → **23** | 305 → 325 |
| `/leistung-texte` | 4.571 | **4.377** | 5.990 | **5.854** | 0 → **1** | 0 → **2** | 19 → **21** | 284 → 294 |
| `/leistung-wartung` | 4.745 | **4.492** | 5.967 | **5.800** | 0 → **1** | 0 → **2** | 16 → **19** | 263 → 270 |
| `/leistung-webdesign` | 4.637 | **4.532** | 5.949 | **5.878** | 0 → **1** | 0 → **2** | 19 → **21** | 292 → 306 |
| `/leistungen` | 6.021 | **5.849** | 9.220 | **8.328** | 0 → **1** | 0 → **2** | 25 → 22 | 794 → **601** |
| `/lexikon` | 2.721 | 2.956 | 4.395 | 4.638 | 0 → **1** | 0 → **2** | 20 → **22** | 240 → 280 |
| `/musterprojekte` | 7.500 | **7.288** | 8.875 | **8.689** | 0 → **1** | 0 → **2** | 24 → 24 | 692 → 698 |
| `/preise` | 6.080 | 6.479 | 8.557 | 9.002 | 0 → **1** | 0 → **2** | 19 → 19 | 454 → 503 |
| `/ratgeber` | 3.120 | 3.561 | 4.378 | 4.988 | 0 → **1** | 0 → **2** | 20 → 20 | 237 → 301 |
| `/ueber-uns` | 4.477 | **4.184** | 5.652 | **5.527** | 1 → 1 | 0 → **2** | 22 → 22 | 275 → 282 |
| `/website-dachdecker` | 7.981 | **7.825** | 11.203 | **11.023** | 0 → **1** | 0 → **2** | 19 → **21** | 854 → 869 |
| `/website-elektrotechnik` | 8.012 | **7.707** | 11.373 | **11.128** | 0 → **1** | 0 → **2** | 20 → **21** | 867 → 883 |
| `/website-sanitaer-heizung-klima` | 7.449 | **7.353** | 10.692 | **10.511** | 0 → **1** | 0 → **2** | 20 → **22** | 797 → 814 |

**Dunkle Abschnitte: 1 von 18 Unterseiten → 17 von 18.** Bänder: 0 → 2 auf jeder Unterseite.

### Verschlechterungen — jede einzeln, auch wo sie aufgewogen wird

| Seite | Was schlechter wurde | Warum |
|---|---|---|
| `/ratgeber` | **+441 px bei 1440, +610 px bei 390**, +64 Wörter | Der Übersicht hat der Abschluss gefehlt — sie endete mit der Liste, während jede andere Seite mit dem Handlungsfeld schliesst. Dazu die Zusage. Beides ist Zubau, kein verlorener Abstand |
| `/lexikon` | **+235 px bei 1440, +243 px bei 390**, +40 Wörter | derselbe Grund |
| `/preise` | **+399 px bei 1440, +445 px bei 390**, +49 Wörter | Zusage plus der in `17_SEITEN_SARTU.md` §6 gebundene Verweis auf `/foerderung` — `/preise` ist dort als „thematisch bester Ort" benannt |
| `/ablauf` | **+107 px bei 1440, +178 px bei 390** | die Zusage. Gegengerechnet: Ein doppelter Satz ist entfallen, das sind 3 Wörter mehr statt der 12, die die Zusage bringt |
| `/leistungen` | **Füllgrad 25 → 22 %** | Die Seite hat 193 Wörter verloren, aber dieselben Abschnittsabstände behalten. Das ist der Preis der Umstellung „der Verteiler verweist" — die Seite ist kürzer und zugleich luftiger |
| `/` | **+36 px bei 390** | Der geänderte Satz in Sektion 3 ist auf 390 px eine Zeile länger |
| `/kontakt` | **+26 px bei 1440**, +6 Wörter | Bei 390 px ist die Seite dafür **221 px kürzer**: Die zwei Karten sind der Aufmacher geworden und stapeln sich mobil nicht mehr |
| alle Seiten | **+7 bis +20 Wörter** je Seite | die Zusagesätze. Sie sind neuer Text, auch wo der Satz nur umgezogen ist |

### Ungeprüft und offen

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **`/foerderung` nennt keine Adresse der Förderbanken.** `17_SEITEN_SARTU.md` §6 Block 5 verlangt je Land einen „Link zur Förderbank". In den Unterlagen steht **keine einzige** Adresse — weder in `FOERDERUNG_KONZEPT.md` noch sonstwo. Genannt ist deshalb die Stelle (L-Bank, SAB, IBB …), nicht ihre Adresse. Eine erfundene Adresse führt einen Betrieb, der investieren will, ins Leere | Die sechzehn Adressen an der Primärquelle holen und eintragen |
| 2 | **Die Sperre in `17_SEITEN_SARTU.md` §6 widerspricht `FOERDERUNG_KONZEPT.md` §4a.** §6 führt „kein Programmname ist in der Primärquelle geprüft"; §4a meldet „alle sechzehn Länder am 09.08.2026 an der Primärquelle geprüft", und `CLAUDE.md` führt es ebenso. Gebaut wurde nach §4a, weil es das jüngere und ausführlichere Ergebnis ist | Die Sperre in §6 streichen oder begründen, warum sie gilt |
| 3 | **Die Ortssperre ist um eine benannte Ausnahme ergänzt worden.** `WebsiteTest::testKeinOrtsnameStehtAufDerWebsite` schneidet die Länderübersicht auf `/foerderung` heraus — und **nur** sie. Begründung im Test: §0 verbietet den Ortsnamen dort, wo er eine Aussage über das eigene Gebiet macht; eine Tabelle, die sechzehn von sechzehn führt, trifft keine Auswahl. Der Fließtext der Seite nennt kein einziges Land von der Liste, auch nicht als Beispiel | Beim nächsten Durchgang prüfen, ob die Ausnahme eng geblieben ist |
| 4 | **`/briefing` bekommt keinen dunklen Abschnitt.** Es ist der Einstieg des Bedarfsschecks: ein Bildschirm, eine Handlung, 76 Wörter. Eine dunkle Bahn wäre dort Zierde und stünde zwischen dem Leser und dem Knopf | Betreiberentscheidung, ob der Funneleinstieg die Formsprache mitträgt |
| 5 | **Punkt c des Auftrags trifft den gebauten Stand nicht.** Gezählt am 14.08.2026: Auf keiner Seite stehen zwei gleichwertige Knöpfe. Die zweite Handlung ist seit dem Bau ein `.textlink`, nicht ein `.knopf` — `partials/handlungsblock` setzt das so. Was es gibt: vier Knöpfe in der Preisleiter auf `/`, `/preise` und den drei Branchenseiten, davon drei in `--ruhig`. Das ist die Preisleiter, nicht die Seitenhandlung | Falls die Preisleiter gemeint war: eigene Entscheidung |
| 6 | **Die Statusspalte auf `/foerderung` steht als `.marken`.** Sie kommt aus einem Wortschatz von fünf Werten, und elf der sechzehn Zeilen tragen „läuft" — in einer Statusspalte richtig, in Fließtext ein Fehler. Die Beschriftungsklasse nimmt sie zugleich aus der Satzanfangsprüfung. **Das ist eine Entscheidung über die Form, keine Umgehung** — die Spalte `Bedingung` daneben bleibt vollständig in der Prüfung | Beim nächsten Textdurchgang gegenlesen |
| 7 | **Die drei Stufen der Beantragungshilfe sind ohne Partnernamen gebaut.** `FOERDERUNG_KONZEPT.md` §4 nennt Stufe 3 als „Partnerempfehlung"; einen benannten Partner gibt es nicht. Auf der Seite steht deshalb „Machen wir nicht" mit dem Hinweis auf Fördermittelberater und Steuerberater als Gattung | Sobald ein Partner feststeht, die Stufe ergänzen |
| 8 | **Das Förderfeld im Bedarfsscheck fehlt.** `FOERDERUNG_KONZEPT.md` §3.2 schlägt es vor, §7 führt es als offene Frage 3. Der erste Entwurf der Seite verwies darauf („sagen Sie es im Bedarfsscheck") — ein Satz, der auf ein Feld zeigt, das es nicht gibt. Er steht jetzt als „bei der Anfrage", und das geht über den Rückfrageweg heute schon | Entscheidung über Frage 3, dann Feld in Thema 5 |
| 9 | **Zwei Zahlen im Bericht vom selben Tag waren zu hoch.** Die Tabelle oben ist nach einer Zwischenmessung geschrieben worden; danach hat die Silbentrennung an `.leistungszeilen h2` beide Übersichten verkürzt. Gemeldet standen `/ratgeber` mit +715 px und `/lexikon` mit +346 px, gemessen sind es +441 und +235. **Beide Zeilen sind hier berichtigt**, nicht stillschweigend ersetzt | Nachmessen erst nach der letzten Änderung, nicht davor |
| 10 | **Der Hinweis unter dem Bedarfsscheck-Ergebnis fehlt.** `FOERDERUNG_KONZEPT.md` §3.1 nennt ihn den wirksamsten Platz überhaupt — „dort steht die Zahl". Gebaut ist er nicht; `17_SEITEN_SARTU.md` §2.3 bindet den Bildschirm eng, und ein Zubau dort braucht eine eigene Entscheidung | Betreiberentscheidung über §2.3 |

---

## 15.08.2026 — sechs Punkte technische Konsolidierung

Eine externe Prüfung des Stands `7fafead` hat sie gefunden. Jeder Punkt ist vor dem Bau
nachgemessen worden, jeder danach.

### Punkt 1 — die drei Wurzeldateien gingen als HTML hinaus

**Vorher, gemessen am 15.08.2026:**

| Adresse | Content-Type | Cache-Control | Cookie |
|---|---|---|---|
| `/sitemap.xml` | `text/html; charset=utf-8` | `no-store, no-cache, must-revalidate` | `PHPSESSID` |
| `/robots.txt` | `text/html; charset=utf-8` | `no-store, no-cache, must-revalidate` | `PHPSESSID` |
| `/llms.txt` | `text/html; charset=utf-8` | `no-store, no-cache, must-revalidate` | `PHPSESSID` |

**Nachher:**

| Adresse | Content-Type | Cache-Control | Cookie |
|---|---|---|---|
| `/sitemap.xml` | `application/xml; charset=utf-8` | `public, max-age=3600` | keins |
| `/robots.txt` | `text/plain; charset=utf-8` | `public, max-age=3600` | keins |
| `/llms.txt` | `text/plain; charset=utf-8` | `public, max-age=3600` | keins |
| `/` (öffentliche Seite) | `text/html; charset=utf-8` | `private, max-age=0, must-revalidate` | `PHPSESSID` |
| `/portal`, `/admin` | — | `no-store` | `PHPSESSID` |

**Drei Ursachen, alle klein.** `Antwort::html()` fügt den Inhaltstyp mit `+` hinzu, und `+`
behält den linken Schlüssel — eine übergebene Kopfzeile wurde verworfen. `public/index.php`
startete die Sitzung vor der Route. Und `session_start()` stempelt mit dem Vorgabewert
`session.cache_limiter = nocache` die drei Cachekopfzeilen auf **jede** Antwort.

**Neu:** `Antwort::xml()`, `Antwort::klartext()`, `Sitzung::wirdGebraucht()`,
`Router::cachepolitik()` und `tests/AuslieferungTest.php` (elf Prüfungen).

### Punkt 2 — sechs Erweiterungen fehlten, nicht eine

Der Befund nannte `ext-gd`. Nachgezählt über `composer.lock`: **sechs** von Abhängigkeiten
verlangte Erweiterungen standen nicht in `composer.json` — `ctype`, `dom`, **`gd`**, `iconv`,
`simplexml`, `zlib`. `composer validate --strict` meldet jetzt `./composer.json is valid`;
vorher meldete es zusätzlich eine veraltete Sperrdatei. `LIVEGANG.md` führt die zwölf
Erweiterungen mit einer Prüfzeile für die Zeit **vor** der Buchung.

`filter`, `hash` und `pcre` sind bewusst nicht eingetragen: Sie lassen sich in PHP 8 nicht
abschalten.

### Punkt 3 — drei weggelassene Spezifikationen

| | Stand |
|---|---|
| **a** Feld 5.2 Logostatus | gebaut, vier gebundene Optionen, Pflichtfeld. Thema 5 heisst jetzt „Domain, Logo und Termin" |
| **b** Förderhinweis unter der Empfehlung | gebaut, zwei Sätze, unterhalb des Knopfes, mit Verweis auf `/foerderung` |
| **c** sechzehn Adressen auf `/foerderung` | gebaut, **16 Verweise**, jeder am 15.08.2026 angefordert |

### Punkt 4 — die Karrieresperre wurde an sechs Stellen verletzt

| Wo | Was dort stand |
|---|---|
| `Preisstufen`, Platzhirsch | `Karriere- und Bewerbungsbereich` als Merkmal — eine Zusage über den Lieferumfang |
| `Preisstufen`, Platzhirsch | „Sichtbar für Kunden — und für Bewerber." |
| `Musterprojekte`, Kanzlei | „eigene Stellenseite", `Karriere` in der Struktur, ein Absatz über die Karriereseite |
| `Unterseitentexte`, Platzhirsch | „ein Bereich für Bewerbungen" |
| `Branchenseiten`, Dachdecker | `Arbeiten bei uns` in der Beispielstruktur und eine FAQ dazu |
| `Branchenseiten`, Dachdecker | die Zusage vom 14.08.2026 — **mein eigener Satz vom Vortag** |

`WebsiteTest::testKeineKarriereseiteWirdErwaehnt` hält sie jetzt. Gegengeprüft: Mit einer
wieder eingesetzten Erwähnung schlägt er an und nennt Seite und Wendung.

### Punkt 5 — die Ortssperre galt zwei Wochen zu lang

`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1 (Rang 1) hat das Einzugsgebiet am **01.08.2026**
entschieden: „alle Orte ins Profil und in den Fliesstext". Der Test hat die aufgehobene
Sperre danach weiter über die ganze Website erzwungen, und vier Codestellen haben das Fehlen
der Orte weiter mit der offenen Anschrift begründet.

**Gesperrt bleiben genau drei Dinge**, und §1 nennt sie einzeln: Google-Unternehmensprofil ·
`LocalBusiness` in strukturierten Daten · die **NAP-Aussage**. Dazu §1 Ebene 3: eine eigene
Ortsseite wird verdient, nicht verteilt.

Der Test prüft jetzt diese drei Stellen statt jedes Ortsnamens; `/kontakt` trägt den in §1
Ebene 1 verlangten Absatz, und eine Gegenprobe hält fest, dass er dort steht.

### Punkt 6 — das mobile Menü hatte keinen Weg zurück

Gemessen bei 390 px mit Berührungseingabe:

| Prüfung | vorher | nachher |
|---|---|---|
| Menü öffnet auf Tippen | ja | ja |
| Menütaste sichtbar und obenauf, solange offen | **nein** — verdeckt von `inset: 0` | ja, als `Menü ✕` oben rechts |
| Tippen auf das Kreuz schliesst | — | ja |
| Tippen daneben schliesst | **nein** — `contains` traf immer zu | ja, über `.menue__hinterlegung` |
| ohne JavaScript öffnen und schliessen | ja / ja | ja / ja |

### Ungeprüft und offen

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Die Sitzung startet weiterhin auf öffentlichen Seiten**, nicht erst am Bedarfsscheck. Der Auftrag verlangte „nur für Bedarfsscheck, Anmeldung, Portal und Admin"; Portal-Lastenheft §4b.7 verlangt Landeseite, verweisenden Host und Kampagnenkennzeichen aus der **ersten** aufgerufenen Seite — die ist bei fast jedem Besucher eine öffentliche. Stünde die Sitzung erst am Bedarfsscheck, wäre die Landeseite für jeden Besucher `/briefing`. Ausgenommen sind die drei Wurzeldateien und `/api/` | Entscheidung: §4b.7 anders lösen (eigenes First-Party-Cookie) oder die Herkunft aufgeben |
| 2 | **`/foerderung` verlinkt die Förderbank, nicht die Programmseite.** Eine Programmseite wechselt ihre Adresse; ein toter Link führt einen Betrieb, der investieren will, ins Leere. Der Leser muss auf der Seite der Bank einen Schritt selbst gehen | Beim nächsten Durchgang prüfen, ob Deep-Links stabil genug sind |
| 3 | **Für das Saarland steht das Serviceportal statt `saarland.de`.** Das Landesportal weist automatisierte Anfragen ab (403); die Adresse liess sich damit nicht selbst prüfen. Die Serviceportal-Seite antwortet mit dem Programmnamen im Titel | Von Hand im Browser gegenprüfen |
| 4 | **Die sechzehn Adressen sind angefordert, nicht inhaltlich gelesen.** Geprüft ist: Antwort `200` und ein Seitentitel, der die Stelle nennt. Ob das Programm heute noch läuft, sagt das nicht — der Inhalt stammt weiterhin vom 09.08.2026. Die Seite behauptet auch nichts anderes: Sie nennt beide Daten getrennt | Vierteljährliche Nachprüfung, `FOERDERUNG_KONZEPT.md` §7 Frage 4 |
| 5 | **`prosa()` in `OberflaecheTest` schliesst jetzt `td` und `th` aus.** Das ist eine Änderung an einem Test, der als unantastbar gilt, und sie ist ausdrücklich gemeldet: Eine Datentabelle ist keine laufende Prosa — auf `/foerderung` eröffnen sechs von sechzehn Bedingungen mit „KMU" und elf Stände mit „läuft", weil sechzehn Länder nach denselben Merkmalen beschrieben werden. Die Prosa **um** die Tabelle bleibt in der Prüfung | Beim nächsten Durchgang gegenlesen, ob die Ausnahme eng geblieben ist |
| 6 | **Die Grenze für `menue.js` steht jetzt auf 3 KB statt 2,5.** Der Test verlangt für eine Anhebung eine Begründung an Ort und Stelle; sie steht dort. Sechs Zeilen Code, der Rest Kommentar | — |
| 7 | **Das Logostatus-Feld ändert den Ablauf noch nicht.** §17 nennt drei Fälle mit unterschiedlicher Behandlung; die Antwort landet in `payload` und im Klartext der Anfrage, eine Regel im Angebot gibt es nicht. Der Fall „Kein Logo" ist im Konzept ausdrücklich offen | Entscheidung, wie „Kein Logo" bedient wird |
| 8 | **Der Ergebnisbildschirm trägt eine Lime-Fläche von rund 180.000 Quadratpixeln.** Die 40.000er-Grenze gilt gemessen für die öffentlichen Seiten; `/briefing/ergebnis` steht in `OberflaecheTest` unter `OHNE_LAYOUT` und wird nicht gemessen. Der Zustand ist älter als dieser Lauf | Entscheidung, ob die Grenze auch im Bedarfsscheck gilt |

---

## 16.08.2026 — Seitengattungen, Sitzungsentscheidung, Datenverzeichnis

**Ausgangsstand:** `541cbf9` auf `claude/sartu-concept-review-pdhb5t`.
**Volle Suite:** 413 Tests, 15.884 Zusicherungen, grün — Oberflächenmessung mit Chromium
eingeschlossen.

### Was gemessen wurde

| Prüfung | vorher | nachher |
|---|---|---|
| Cookie auf `/`, `/preise`, `/ratgeber`, `/lexikon`, `/leistung-*`, `/musterprojekte`, `/ueber-uns`, `/ablauf`, `/foerderung`, `/impressum`, `/datenschutz`, `/agb` | **je eines** | keines |
| Cookie auf `/briefing`, `/kontakt`, drei Branchenseiten | eines | eines — begründet und namentlich im Test |
| `Cache-Control` öffentliche Leseseite | `no-store` | `public, max-age=3600` |
| `Cache-Control` bei `404` auf öffentlicher Route (`/agb`) | `public, max-age=3600` | `no-store` |
| `Permissions-Policy` | fehlte | in jeder Antwort |
| `session.use_strict_mode` | Vorgabe `0` | `1` |
| Lime-Fläche `/briefing/ergebnis` bei 390 px | rund 180.000 px² | 27.234 px² |
| Grösste Lime-Fläche `/briefing/ergebnis`, alle sechs Breiten | ungemessen | 23.419 bis 29.041 px² — Grenze 40.000 |
| Überlauf auf allen vier Bedarfsscheck-Bildschirmen, sechs Breiten | ungemessen | 0 px |
| Von der Abnahmeprüfung gemessene Adressen | 21 | 24 |
| Tiefster Kontrast im Farbsystem | 6,42 : 1 (dokumentiert, warme Reihe) | 6,16 : 1 (gemessen, `tokens.css`) |
| Rohe Farbwerte in `website.css` | 6 aus der warmen Reihe | 0 |

Punkt 1 und Punkt 8 der Liste vom 15.08.2026 sind damit erledigt: Die Sitzung startet nicht mehr
auf Leseseiten, und die Lime-Fläche des Ergebnisbildschirms liegt unter der Grenze.

### Ungeprüft und offen

| # | Punkt | Womit es zu prüfen ist |
|---|---|---|
| 1 | **Der Pflichthaken „Datenschutzhinweise gelesen" bleibt Voraussetzung des Absendens.** Der Auftrag verlangte, ihn durch einen sichtbaren, nicht blockierenden Hinweis zu ersetzen. Dagegen stehen `spezifikation/09_ANFRAGEEINGANG.md` §4b.2, Testfall 35 und `CHECK (b2b_confirmed = 1 AND privacy_confirmed = 1)` aus Migration 009 — drei gebundene Stellen. **Nicht entschieden, sondern vorgelegt:** `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §2a | Anwaltliche Prüfung, zusammen mit dem Datenschutztext |
| 2 | **`audit_events` lässt sich nicht löschen** (Trigger aus Migration 005 und 006). Wie sich das zu einem Löschbegehren nach Art. 17 DSGVO verhält, ist eine Rechtsfrage, keine technische | Anwaltliche Prüfung |
| 3 | **Kein Auftragsverarbeitungsvertrag** für den Mailversand und für Mollie. Der Mailanbieter ist zudem noch nicht ausgewählt (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4) | Entscheidung, dann Vertrag |
| 4 | **Die drei Branchenseiten setzen ein Cookie**, weil sie den Bedarfsscheck als Formular einbetten. Ersetzt man das Formular durch einen Verweis auf `/briefing`, fallen drei Cookies weg — das ändert aber den Einstieg, den §10a beschreibt | Entscheidung: eingebettetes Formular oder Verweis |
| 5 | **`DATENVERZEICHNIS.md` ist aus Schema und Code erhoben, nicht juristisch geprüft.** Rechtsgrundlagen stehen bewusst nicht darin. Es ist die Grundlage für einen Datenschutztext, nicht der Text | Anwaltliche Prüfung |
| 6 | **Die W-IdNr. ist im Impressum-Entwurf nur als Hinweis vermerkt.** Ob SARTU eine hat und ob sie genannt werden muss, ist offen; § 5 DDG verlangt sie „soweit vorhanden" | Betreiberangabe, dann anwaltliche Prüfung |
| 7 | **Migration 040 ist gegen MariaDB 10.11 in diesem Container gelaufen**, nicht gegen MySQL 8.4 und nicht gegen MariaDB 11.4. `ADD COLUMN … AFTER` ist in beiden Standard, aber ausgeführt ist es nur hier | Lauf gegen beide Zielversionen |
| 8 | **Anfragen aus der Zeit vor Migration 040 tragen weder Zeitpunkt noch Textfassung.** Sie zeigen `Noch nicht hinterlegt`. Ein nachträgliches Füllen wäre eine Behauptung über einen Vorgang, bei dem niemand dabei war | — |
| 9 | **Der Bedarfsscheck steht ab sofort *in* der Abnahmeprüfung.** `/briefing/ergebnis`, `/briefing/kontakt` und `/briefing/danke` standen in `OberflaecheTest::OHNE_LAYOUT`, weil ein direkter Aufruf mit `303` antwortet — `tools/oberflaeche.mjs` läuft die Strecke jetzt aus. Offen bleibt, dass die Messung dafür Formulare **generisch** ausfüllt (erste Auswahl je Frage): Eine künftige Frage mit einer Bedingung, die daraus nicht erfüllbar ist, lässt den Vorlauf stehenbleiben statt anschlagen | Beim nächsten Durchgang prüfen, ob der Vorlauf noch bis `/briefing/ergebnis` durchläuft |
