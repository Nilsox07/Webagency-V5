# Messungen

**Gemessen am 02.08.2026**, nach Abschluss der Stufen A0 bis B.

**Die Regel dieser Datei:** Was hier steht, ist ausgeführt worden. Was nicht ausgeführt wurde,
steht unter „Nicht gemessen" — mit dem Grund und dem Mittel, das es bräuchte. Ein nicht
gemessener Wert wird nirgends als gemessen gemeldet.

## Werkzeuge und Umgebung

| Was | Fassung |
|---|---|
| PHP | 8.3.33 (CLI), Container `app` |
| Datenbank | MariaDB 11.4.12 |
| Browser | Chromium 141.0.7390.37, angesteuert über Playwright 1.56.1 |
| Mailserver | Mailpit im Container `mail`, SMTP auf Port 1025 |
| Adresse | `http://localhost:8080` über den laufenden Apache — **nicht** über den Testrouter |

**Kein Test hat hier gemessen.** Die Werte kommen aus dem laufenden Server und aus einem
echten Browser. Wo ein Test dasselbe prüft, steht das dabei; er ersetzt die Messung nicht.

---

## 1. Kontrast — je Kombination, die wirklich vorkommt

Gemessen am **gerenderten** Baum, nicht an `design/tokens.css`. Eine Farbe kann in der Datei
stehen und auf keiner Seite auftreten. Dann ist sie keine Kombination.

Je Element mit eigenem Text: Vordergrund, erster **deckender** Hintergrund darüber,
WCAG-Verhältnis.

**30 öffentliche Seiten · 21 verschiedene Kombinationen · keine unter der Grenze.**

Die Grenze ist 4,5 : 1, für großen Text (ab 24 px oder ab 18,66 px fett) 3,0 : 1.

| Wert | Vordergrund | Hintergrund | Größe | Grenze | Beispiel | Seiten |
|---|---|---|---|---|---|---|
| **5,56 : 1** | `rgb(92,85,74)` | `rgb(232,223,205)` | 13.0px | 4,5 | `p.marke` | 5 |
| **6,42 : 1** | `rgb(92,85,74)` | `rgb(244,239,229)` | 13.0px | 4,5 | `p.vorzeile` | 13 |
| **6,91 : 1** | `rgb(168,155,136)` | `rgb(20,17,13)` | 20.0px | 4,5 | `p.lede` | 29 |
| **6,94 : 1** | `rgb(92,85,74)` | `rgb(251,248,242)` | 13.0px | 4,5 | `p.bildplatz__kennung` | 2 |
| **6,95 : 1** | `rgb(77,71,61)` | `rgb(232,223,205)` | 16.0px | 4,5 | `p.leise` | 5 |
| **7,90 : 1** | `rgb(77,71,61)` | `rgb(228,245,184)` | 16.0px | 4,5 | `p.leise` | 1 |
| **8,02 : 1** | `rgb(77,71,61)` | `rgb(244,239,229)` | 16.0px | 4,5 | `p.preishinweis` | 18 |
| **8,67 : 1** | `rgb(77,71,61)` | `rgb(251,248,242)` | 16.0px | 4,5 | `figcaption` | 6 |
| **12,48 : 1** | `rgb(20,17,13)` | `rgb(163,230,53)` | 16.0px | 4,5 | `a.knopf` | 30 |
| **12,48 : 1** | `rgb(163,230,53)` | `rgb(20,17,13)` | 18.0px | 4,5 | `a.textlink` | 23 |
| **12,50 : 1** | `rgb(35,30,23)` | `rgb(232,223,205)` | 50.0px | 3,0 | `h2` | 27 |
| **12,50 : 1** | `rgb(35,30,23)` | `rgb(232,223,205)` | 18.0px | 4,5 | `p` | 27 |
| **14,22 : 1** | `rgb(35,30,23)` | `rgb(228,245,184)` | 17.5px | 4,5 | `h3` | 2 |
| **14,22 : 1** | `rgb(35,30,23)` | `rgb(228,245,184)` | 24.0px | 3,0 | `h2` | 1 |
| **14,43 : 1** | `rgb(35,30,23)` | `rgb(244,239,229)` | 18.0px | 4,5 | `a` | 30 |
| **14,43 : 1** | `rgb(35,30,23)` | `rgb(244,239,229)` | 80.0px | 3,0 | `h1` | 30 |
| **15,60 : 1** | `rgb(35,30,23)` | `rgb(251,248,242)` | 24.0px | 3,0 | `h3` | 6 |
| **15,60 : 1** | `rgb(35,30,23)` | `rgb(251,248,242)` | 18.0px | 4,5 | `p.preis__zusatz` | 7 |
| **16,42 : 1** | `rgb(20,17,13)` | `rgb(244,239,229)` | 18.0px | 4,5 | `a.wortmarke` | 29 |
| **17,76 : 1** | `rgb(251,248,242)` | `rgb(20,17,13)` | 18.0px | 4,5 | `a.sprungmarke` | 29 |
| **17,76 : 1** | `rgb(251,248,242)` | `rgb(20,17,13)` | 56.0px | 3,0 | `p` | 26 |

### Was die Messung gefunden hat

**Eine Kombination lag bei 2,05 : 1.** `.preishinweis` steht auf den drei Branchenseiten im
dunklen Abschlussabschnitt. Dort behielt der Absatz `--muted` — eine Farbe für helle Flächen.

Auf der Startseite und unter `/leistungen` fiel das nicht auf. Dort überschreibt
`handlung--dunkel` die Farbe. Die Branchenseiten setzen den Absatz direkt.

Behoben mit einer Zeile in `public/assets/css/website.css`: `.abschluss .preishinweis` nimmt
jetzt `--label-dark`, dieselbe Farbe wie `.abschluss .lede` daneben. Danach neu gemessen:
6,91 : 1.

**Der niedrigste Wert im System ist 5,56 : 1, nicht 6,42 : 1.** `CLAUDE.md` nennt 6,42 als
kleinsten Wert. Das trifft für `--label` auf `--paper` zu; auf `--sand` (`rgb(232,223,205)`,
5 Seiten) ist derselbe Ton bei 5,56 : 1. Beide liegen über der Grenze, aber die Zahl in
`CLAUDE.md` ist nicht der kleinste vorkommende Wert.

---

## 2. Tastatur — jede öffentliche Seite, nur `Tab` und `Enter`

Je Seite: alle bedienbaren Elemente durchgetabbt, jedes Mal geprüft, ob der Fokus sichtbar
ist (`outline` oder `box-shadow`). Danach die Sprungmarke (`Tab`, `Enter`). Danach das
mobile Menü bei 390 px Breite: mit `Enter` öffnen, einmal ganz herumtabben, `Esc`.

| Frage | Ergebnis |
|---|---|
| Seiten mit Antwort 200 | **30** |
| Bedienbare Elemente insgesamt | **957** |
| Davon mit `Tab` erreicht | **alle** |
| Davon ohne sichtbaren Fokus | **0** |
| Sprungmarke springt zum Inhalt | **29 von 30** |
| Menü öffnet mit `Enter` | 29 von 29 |
| Fokus bleibt im offenen Menü | 29 von 29 |
| Menü schließt mit `Esc` | 29 von 29 |

### Was die Messung gefunden hat

**`Esc` schloss das Menü nicht.** Zwei Kommentare im Bau behaupteten, ein `details`-Element
liefere das vom Browser. Es liefert es nicht — in Chromium 141 gemessen. Dasselbe gilt für
den Klick daneben, den §3 ebenfalls verlangt.

Beides ist jetzt in `public/assets/js/menue.js` ergänzt, in derselben Datei wie die
Fokusfalle. **Das Menü bleibt ohne Skript vollständig bedienbar** — es ist ein
`details`-Element und bleibt eines. Nach der Ergänzung neu gemessen: 29 von 29.

Damit wuchs die Datei von 1,8 auf 2,3 KB. Der Betreiber hatte 2 KB für die Fokusfalle allein
gesetzt; aus einer Ergänzung wurden vier. Die Grenze im Test steht jetzt auf 2,5 KB, mit der
Begründung an Ort und Stelle.

**`/briefing` hat keine Sprungmarke.** Die Seite trägt das Layout `oeffentlich` — kein
Kopfband, keine Navigation. Der erste `Tab` landet direkt auf dem Hauptknopf im `main`. Eine
Sprungmarke hätte dort nichts zu überspringen. **Gemeldet, nicht gebaut.** Website-Lastenheft §1
nennt die Sprungmarke ohne Einschränkung. Ob sie auf eine Seite ohne Navigation gehört,
entscheidet der Betreiber.

---

## 3. Antwortzeiten und Seitengrößen

Je Adresse ein Vorlauf gegen den kalten Opcache, dann fünf Läufe; angegeben ist der Median.
**Gesamt** ist alles, was ein Erstaufruf lädt: HTML, drei Stilvorlagen, das Menüskript.
Gemessen in einem eigenen Browserkontext ohne Zwischenspeicher.

| Adresse | Zeit (ms) | HTML (KB) | Gesamt (KB) | Anfragen |
|---|---|---|---|---|
| `/` | 5,8 | 20,2 | 57,7 | 5 |
| `/preise` | 5,1 | 14,4 | 51,9 | 5 |
| `/leistungen` | 4,3 | 12,8 | 50,3 | 5 |
| `/ablauf` | 4,1 | 10,7 | 48,2 | 5 |
| `/briefing` | 4 | 1,8 | 22,8 | 3 |
| `/ueber-uns` | 4,6 | 6,9 | 44,4 | 5 |
| `/kontakt` | 6,2 | 5,5 | 43 | 5 |
| `/leistung-webdesign` | 4,1 | 9,4 | 46,9 | 5 |
| `/leistung-texte` | 4,2 | 9,2 | 46,7 | 5 |
| `/leistung-seo-lokal` | 4,3 | 9,7 | 47,2 | 5 |
| `/leistung-wartung` | 4,2 | 9,2 | 46,7 | 5 |
| `/leistung-portal` | 4,2 | 10,4 | 47,9 | 5 |
| `/website-sanitaer-heizung-klima` | 4,5 | 15,4 | 52,9 | 5 |
| `/website-elektrotechnik` | 4,3 | 16,4 | 53,9 | 5 |
| `/website-dachdecker` | 5 | 16,2 | 53,7 | 5 |
| `/ratgeber` | 4,1 | 6,6 | 44 | 5 |
| `/ratgeber/was-kostet-eine-firmenwebsite` | 4 | 9,2 | 46,7 | 5 |
| `/ratgeber/was-nicht-enthalten-ist` | 3,9 | 8,8 | 46,3 | 5 |
| `/ratgeber/was-der-betrieb-kostet` | 4,1 | 7,5 | 45 | 5 |
| `/ratgeber/agentur-freelancer-baukasten` | 4,1 | 10 | 47,5 | 5 |
| `/ratgeber/webdesign-ohne-wordpress` | 4,2 | 9,1 | 46,6 | 5 |
| `/lexikon` | 3,7 | 6,6 | 44,1 | 5 |
| `/lexikon/firmenwebsite` | 5,1 | 6,9 | 44,4 | 5 |
| `/lexikon/festpreis` | 4 | 6,6 | 44,1 | 5 |
| `/lexikon/hosting` | 4,4 | 6,5 | 44 | 5 |
| `/lexikon/domain` | 3,6 | 6,7 | 44,2 | 5 |
| `/lexikon/relaunch` | 3,5 | 6,5 | 44 | 5 |
| `/lexikon/barrierefreiheit` | 3,7 | 6,9 | 44,3 | 5 |
| `/lexikon/local-seo` | 3,7 | 6,8 | 44,3 | 5 |
| `/lexikon/geo-ki-suche` | 3,8 | 6,8 | 44,3 | 5 |

| | Median | Höchstwert |
|---|---|---|
| Antwortzeit | **4,15 ms** | 6,2 ms (`/kontakt`) |
| HTML | **9,1 KB** | 20,2 KB (`/`) |
| Gesamt beim Erstaufruf | **46,6 KB** | 57,7 KB (`/`) |

**Verbindungen zu fremden Domains: 0.** Über alle 30 Seiten. Website-Lastenheft §17 verlangt
genau das, im Netzwerkprotokoll geprüft.

**JS-Budget §17:** erlaubt sind 75 KB gzip auf der Startseite, 40 KB auf Unterseiten.
Ausgeliefert werden **2,3 KB ungzippt**, auf jeder Seite dieselbe Datei.

**Diese Zahlen gelten für diese Maschine, nicht für den Zielhoster.** Sie zeigen, dass die
Anwendung nicht rechnet, wo sie nicht muss. Über Netzlaufzeit, TLS-Aufbau und die Hardware
des Anbieters sagen sie nichts.

---

## 4. Mailwege — zwölf Wege, einmal jeder, über echtes SMTP

Kein Attrappen-Versender: Die Dienste bekamen ihren Vorgabewert und sprachen mit dem
SMTP-Server aus der `.env`. Danach wurde der Posteingang in Mailpit gelesen.

**13 Nachrichten angekommen, 12 Wege ausgelöst** — die zweite Zahlungserinnerung erzeugt zwei
Nachrichten (Kunde und Admin), so verlangt es §5.3a.

| Weg | Betreff im Posteingang | An |
|---|---|---|
| Anfrage aus dem Bedarfsscheck (§9.5b) | `Neue Anfrage: {Unternehmen}` | Betreiber |
| Rückfrage über `/kontakt` (§4b.6) | `Rückfrage über die Website: Domain und Launch` | Betreiber |
| Anmeldelink (§6) | `Ihr Anmeldelink für Ihren Kundenbereich` | Kunde |
| Angebot angenommen (§5,2) | `Bestätigung Ihrer Beauftragung` | Kunde |
| Rechnung gesendet (§5,3) | `Ihre Rechnung RE-2026-901` | Kunde |
| Zahlung verbucht (§5,3) | `Zahlungseingang bestätigt` | Kunde |
| Zahlungserinnerung 1 (§5.3a) | `Erinnerung: Rechnung RE-2026-902 ist fällig` | Kunde |
| Zahlungserinnerung 2 (§5.3a) | `Zweite Erinnerung: Rechnung RE-2026-902` | Kunde |
| Zahlungserinnerung 2, Hinweis an SARTU | `Zweite Erinnerung verschickt: RE-2026-902` | Betreiber |
| Vorschau bereit (§5.6a) | `Ihre Vorschau steht bereit` | Kunde |
| Rückmeldung eingegangen (§5.6a) | `Rückmeldung eingegangen` | Betreiber |
| Website online (§5,7) | `Ihre Website ist online` | Kunde |

Alle mit Absender `noreply@sartu.local` aus `MAIL_FROM`.

### Was die Messung gefunden hat: sechs Mails aus §10 gibt es nicht

Beim Auslösen von „Angebot gesendet" kam **keine** Nachricht an. Der Abgleich mit der Tabelle
in §10 zeigt sechs Zeilen, zu denen im Code kein Versand gehört:

| §10 verlangt | Betreff | Wo es fehlt |
|---|---|---|
| Angebot gesendet | `Ihr Angebot von SARTU liegt bereit` | `AngebotDienst::senden()` schreibt nur das Protokoll |
| Neue Aufgaben | `Es liegen Aufgaben für Sie bereit` | kein Versand beim Anlegen von Aufgaben |
| Faktenfreigabe erfolgt (an beide) | `Freigabe bestätigt — wir starten` | `Aufgabendienst::freigeben()` schreibt nur das Protokoll |
| Antwort auf Nachricht | `Antwort auf Ihre Nachricht` | kein Versand in der Nachrichtenantwort |
| Angebot läuft in 3 Tagen ab | `Ihr Angebot gilt noch bis {Datum}` | `Zahlungslauf` setzt `abgelaufen`, warnt aber nicht vorher |
| Angebot angenommen (an Admin) | `Angebot angenommen: {Organisation}` | nur die Kundenmail ist gebaut |

**Warum das zählt.** §10 begründet es selbst: *„Der Kunde meldet sich ausschließlich per
Anmeldelink an. **Was ihm keine Mail mitteilt, erfährt er nicht.**"* Bei „Angebot gesendet"
heißt das: Das Angebot liegt im Kundenbereich, und niemand schickt den Kunden hin.

**Gemeldet, nicht gebaut.** Der Wortlaut aller sechs Zeilen steht in §10 — es ist nichts zu
erfinden, nur zu bauen. Es steht in `LIVEGANG.md` als Sperre vor dem ersten Kunden.

---

## 5. Der interne Bereich, angemeldet im Browser

Anmeldung mit Passwort **und** TOTP-Code über die echten Formulare — kein gesetzter
Sitzungswert, kein Testrouter. Der Code kam aus dem verschlüsselten Geheimnis in
`users.totp_secret_enc`, gerechnet nach RFC 6238.

| Adresse | Antwort | H1 | Mit `Tab` erreicht | Ohne sichtbaren Fokus | Schlechtester Kontrast |
|---|---|---|---|---|---|
| `/admin` | 200 | 1 | 14/14 | 0 | 8,02 : 1 |
| `/admin/anfragen` | 200 | 1 | 13/13 | 0 | 8,02 : 1 |
| `/admin/projekte` | 200 | 1 | 11/11 | 0 | 8,02 : 1 |
| `/admin/rechnungen` | 200 | 1 | 13/13 | 0 | 8,02 : 1 |
| `/admin/rechtstexte` | 200 | 1 | 16/16 | 0 | 5,56 : 1 |
| `/admin/einstellungen/betrieb` | 200 | 1 | 30/30 | 0 | 8,02 : 1 |
| `/admin/testmail` | 200 | 1 | 13/13 | 0 | 8,02 : 1 |

**Keine Kombination unter der Grenze.**

## 6. Der Kundenbereich, angemeldet im Browser

Anmeldung über einen echten Anmeldelink — derselbe Weg wie in der Mail, derselbe
Tokenspeicher, ein Aufruf.

| Adresse | Antwort | H1 | Mit `Tab` erreicht | Ohne sichtbaren Fokus | Schlechtester Kontrast |
|---|---|---|---|---|---|
| `/portal` | 200 | 1 | 15/15 | 0 | 6,33 : 1 |
| `/portal/angebot` | 200 | 1 | 19/19 | 0 | 8,02 : 1 |
| `/portal/aufgaben` | 200 | 1 | 13/13 | 0 | 8,02 : 1 |
| `/portal/rechnungen` | 200 | 1 | 13/13 | 0 | 8,02 : 1 |
| `/portal/vorschau` | 200 | 1 | 13/13 | 0 | 8,02 : 1 |
| `/portal/domain` | 200 | 1 | 13/13 | 0 | 8,02 : 1 |
| `/portal/inhalte` | 200 | 1 | 13/13 | 0 | 8,02 : 1 |
| `/portal/vertrag` | 200 | 1 | 13/13 | 0 | 8,02 : 1 |
| `/portal/hilfe` | 200 | 1 | 15/15 | 0 | 8,02 : 1 |

**Keine Kombination unter der Grenze.** Block 3 des Cockpits wurde dabei im Browser gesehen:
`Rechnung RE-2026-903 — zahlbar bis 04.08.2026` mit dem Hinweis
`Diese Frist ist in wenigen Tagen erreicht.` bei einer Frist in zwei Tagen.

**Die Proben sind wieder weg.** Organisation, Kunde, Projekt, Angebot und Rechnungen des
Messlaufs wurden nach der Messung gelöscht. **`audit_events` blieb** — ein Trigger verbietet
das Löschen (§4: „nie geändert und nie gelöscht"), und das ist richtig so. Die
Arbeitsdatenbank enthält danach 0 Organisationen, 0 Projekte, 0 Rechnungen und den einen
Admin aus der Ersteinrichtung.

---

## Nicht gemessen — mit Grund und Mittel

| Was | Warum nicht | Womit es ginge |
|---|---|---|
| **LCP, TBT, CLS** (§17, Laborwerte) | Lighthouse ist nicht installiert, und ein Nachinstallieren über das Netz ist in dieser Umgebung nicht vorgesehen. Geschätzte Werte wären keine | Lighthouse gegen die Vorabfassung auf dem Zielhoster, Werkzeug und Fassung im Bericht nennen |
| **Zustellung an einen fremden Posteingang** | Mailpit fängt ab und gibt nichts weiter. Über SPF, DKIM, DMARC und Spamfilter sagt das nichts | Testmail an eine Adresse außerhalb der eigenen Domain, Posteingang ansehen — nicht den Spam-Ordner. Steht in `LIVEGANG.md` |
| **HTTPS, HSTS, `session.cookie_secure`** | Lokal gibt es kein TLS. `Strict-Transport-Security` wird nur bei `APP_ENV=production` gesetzt und ist deshalb nie gelaufen | Antwortköpfe auf dem Zielhoster ansehen. Steht in `LIVEGANG.md` |
| **Echter Cronlauf** | Der Lauf ist von Hand ausgeführt und geprüft, der Eintrag beim Anbieter nicht | Cron eintragen, am Folgetag die Ausgabe ansehen |
| **Bedienung mit abgeschaltetem JavaScript im Browser** | Gemessen wurde mit **eingeschaltetem** JavaScript. Dass die Abläufe ohne auskommen, ist im Test geprüft (kein `on…`-Attribut, jeder Knopf in einem Formular, jedes `POST` mit CSRF-Feld) und über `curl` belegt — im Browser mit ausgeschaltetem Schalter nicht | Firefox mit `javascript.enabled=false`, Bedarfsscheck und Kundenbereich einmal durchlaufen |
| **`prefers-reduced-motion`** (§17) | Nicht angesteuert | Chromium mit `--force-prefers-reduced-motion`, Seiten ansehen |
| **Mobil und Desktop im Vergleich** (§17) | Der Tastaturdurchlauf lief bei 1280 px, das Menü bei 390 px. Ein vollständiger Durchgang beider Breiten über alle Seiten fehlt | Denselben Durchlauf bei 390 px und 1280 px, auf horizontales Scrollen des Seitenkörpers achten |
| **TOTP in einer echten Authenticator-App** | Gerechnet wurde nach RFC 6238 und vom Anmeldeformular anerkannt. Ob eine App denselben Code zeigt, ist nicht geprüft | Schlüssel in Aegis oder Google Authenticator eintippen, Code eingeben |
| **Uploads und die 500-MB-Grenze** | Im Test geprüft (Testfall 79), im Browser nicht | Datei über `/portal/aufgaben` hochladen, Grenze überschreiten |
| **Ladeverhalten unter Last** | Ein Aufruf nach dem anderen, eine Maschine | Lasttest auf dem Zielhoster, falls der Betreiber ihn will |

---

## Prüfbericht

`SARTU_TEXTREGELN.md` §2. Gezählt mit `tools/textpruefung.py` am 02.08.2026.

```text
TEXTPRUEFUNG   Seite: MESSUNGEN.md            Datum: 02.08.2026

Sätze gesamt                            93
Längster Satz                           24 Wörter      Grenze 20   → benannt
Sätze über 20 Wörter                     1             Grenze 0    → benannt
Aufzählungen >3 Glieder im Satz          1             Grenze 0    → benannt
Gegensatzformel                          9             Grenze 2    → benannt
Treffer Wortliste (Füllwörter)           0             Grenze 0
Behauptungen über Kunden / Markt         0             Grenze 0
```

### Die vier Überschreitungen, einzeln

**Der Satz mit 24 Wörtern ist ein Zitat.** Er stammt wörtlich aus Portal-Lastenheft §10:
*„Der Kunde meldet sich ausschließlich per Anmeldelink an. Was ihm keine Mail mitteilt,
erfährt er nicht."* Ein Zitat wird nicht gekürzt. Das Zählskript klebt ihn außerdem an den
Folgesatz — der Kopf des Skripts warnt davor.

**Die Aufzählung mit vier Gliedern** steht in „Nicht gemessen": `SPF, DKIM, DMARC und
Spamfilter`. Es sind vier Prüfungen, die alle vier vorkommen. Drei zu nennen wäre falsch.

**Neun Gegensatzformeln.** Diese Datei sagt an neun Stellen, was **nicht** gemessen wurde
oder **nicht** gilt. Genau dafür ist sie da. Die Formel ist hier der Inhalt, nicht die
Zierde.

**Die Regeln gelten dem Text nach außen.** Diese Datei liest der Betreiber, kein Kunde. Sie
wird trotzdem geprüft, weil eine ungeprüfte Ausnahme der Anfang von zwei ist.
