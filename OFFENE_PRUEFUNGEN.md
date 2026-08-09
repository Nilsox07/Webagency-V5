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
