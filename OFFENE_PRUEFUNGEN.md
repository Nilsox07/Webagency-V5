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
