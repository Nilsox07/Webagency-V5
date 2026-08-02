# Abschlussbericht — A2, A3 und B

**Stand:** 02.08.2026 · **Zweig:** `claude/php-a0-modellplan-06duus`

Der Auftrag lautete: A2, A3 und B durcharbeiten, ohne Rückfragen und ohne Etappenpause, bis
zur Startbereitschaft für beide Livegänge. Dieser Bericht sagt, was steht, was nicht steht
und was beim Betreiber liegt.

---

## 1. Was jetzt da ist

| | |
|---|---|
| **Etappen fertig** | A0 · A1 · A2 · A3 · B |
| **Tabellen** | **20 von 20** |
| **Testfälle** | **88 von 88** gebaut und geprüft |
| **Tests** | **254 grün**, 3.345 Zusicherungen, gegen echtes MariaDB 11.4 |
| **Migrationen** | 25, lückenlos eingespielt, Prüfsummen stimmen |
| **Code** | rund 25.000 Zeilen Anwendung, 7.700 Zeilen Tests |
| **Öffentliche Adressen** | 30, alle mit eigenem Titel, eigener Beschreibung und Canonical |

**Ausgeführt, nicht behauptet:** Die Testzahl stammt aus `vendor/bin/phpunit` gegen die
Testdatenbank. `bin/migrate.php verify` bestätigt die Prüfsummen. `bin/cron.php` ist gegen
die Arbeitsdatenbank gelaufen.

---

## 2. Die drei Etappen an ihren eigenen Abnahmepunkten gemessen

### A2 — „Fertig, wenn"

> *„Der Kunde nimmt das Angebot an, und der Weg führt über Anzahlung, Aufgaben und
> Faktenfreigabe bis `produktion`. Eine Rechnung mit überschrittenem `due_date` steht am
> nächsten Tag auf `ueberfaellig`. Der Mandantentest kennt Rechnungen, Aufgaben und Dateien."*

| Punkt | Beleg |
|---|---|
| Annahme → Anzahlung → Aufgaben → Faktenfreigabe → `produktion` | `AuftragsstreckeTest::testVonDerAnnahmeUeberDieAnzahlungBisZurProduktion` — die Strecke an einem Stück |
| Überschrittenes `due_date` → `ueberfaellig` | `testUeberfaelligWirdAmNaechstenTagGesetzt` |
| Mandantentest kennt Rechnungen, Aufgaben, Dateien | `TenantIsolationTest`, fremde Datei über die Ausspielroute → **404** |

### A3 — „Fertig, wenn"

> *„Ein Projekt erreicht `live`. Der Mandantentest deckt alle Kundenrouten ab."*

| Punkt | Beleg |
|---|---|
| Ein Projekt erreicht `live` | `LivegangTest::testVonDerProduktionUeberVorschauUndAbnahmeBisLive` — Vorschau, Rückmeldung, Einarbeiten, zweite Vorschau, Abnahme, Onlinegang |
| `protection_started_on` gesetzt, Mindestlaufzeit 12 Monate später | derselbe Test und `testOnlinegangSetztBetriebsbeginnUndMindestlaufzeit` |
| Mandantentest deckt **alle** Kundenrouten ab | `TenantIsolationTest` vergleicht die vollständige Routenliste. Er hat beim Hinzufügen jeder neuen Route angeschlagen — wie vorgesehen |

### B — „Fertig, wenn"

> *„Die Definition of Done aus Website-Lastenheft §17 ist abgehakt."*

Sie ist es **nicht vollständig**, und das ist kein Versäumnis, sondern die ehrliche Antwort.
§17 verlangt Messungen und einen Durchlauf mit sieben Menschen. Beides braucht einen
Browser, ein Messwerkzeug und Zeit vor dem Livegang.

| §17-Block | Stand |
|---|---|
| **Inhalt und Aussagen** | **abgehakt.** Verbotsliste §2: null Treffer, maschinell über alle 30 Seiten. Preishinweis auf jeder preisführenden Seite. Platzhirsch sichtbar die Empfehlung, nichts direkt kaufbar. Keine erfundene Referenz. Keine Ortsangabe, kein `LocalBusiness`, keine Ortsseite |
| **Technik und SEO** | **teilweise.** `php -l` über jede Datei: fehlerfrei. Jede Seite 200, genau eine H1, eigener Titel und eigene Beschreibung, Canonical, Breadcrumb: geprüft. Keine toten Verweise: geprüft. Sitemap ohne `noindex`-Seite: geprüft. **Offen: Laborwerte und Bilder** — es gibt keine Bilder |
| **Bedienung** | **offen.** Kontrast, Tastaturdurchlauf, 320-px-Ansicht: nichts gemessen, nichts angesehen |
| **Formulare und Schnittstelle** | **abgehakt.** Beide Formulare senden nachweislich. Honigtopf, Zeitregel, Doppeleinreichung: geprüft. Empfehlung serverseitig. Nur `/public` erreichbar. Kein Netzwerkaufruf an eine fremde Domain — es gibt keinen |
| **Ortsseiten** | **abgehakt.** Es gibt keine, auch keinen unverlinkten Entwurf. Im Test nachgewiesen |
| **Vor dem Livegang zwingend** | **offen.** `KEYWORD_VALIDATION.md` existiert nicht, `GEO_DISCOVERY_CHECKLIST.md` ist nicht abgehakt |
| **Recht** | **offen und mit Absicht offen.** Siehe Abschnitt 5 |

Die vollständige Liste mit Prüfmittel und Zeitpunkt steht in `OFFENE_PRUEFUNGEN.md`.

---

## 3. Die Entscheidungen, die den Bau getragen haben

### Drei Widersprüche, aufgelöst statt ausgewählt

| Stelle | Widerspruch | Auflösung |
|---|---|---|
| §5.1a | Die Tabelle markiert **vier** Kundenzeilen, der Satz danach sagt „genau drei" | Der Satz liefert die Begründung mit: „Alle drei sind Erklärungen mit Namen und Zeitpunkt." Er zählt Erklärungen, nicht Klicks. `Projektstatus` trägt `wer` und `erklaerung` getrennt — **beide Stellen bleiben gültig** |
| §5.6a | „harte Scope-Grenze" und „das Portal blockiert nichts" | Die Grenze wird **angezeigt**, nicht durchgesetzt. Eine Runde ausserhalb des Festpreises läuft wie jede andere; der Kunde liest vorher, dass sie nicht enthalten ist |
| §0 gegen §5 / §11 | Das Website-Lastenheft nennt „Raum Dresden" im Fließtext und verbietet Ortsnamen im selben Dokument | Rangfolge nach `UEBERGABE_DATEILISTE.md`: `SARTU_ENTSCHEIDUNGEN_OFFEN.md` steht auf Rang 1. Die Sperre gewinnt. Die **Aussage** — bundesweit, Entfernung spielt keine Rolle — steht vollständig da |

### Was gemeldet statt erfunden wurde

Acht offene Punkte in `OFFENE_ENTSCHEIDUNGEN.md`. Die drei mit der grössten Wirkung:

- **Für das Kontaktformular gibt es keine Tabelle im Datenmodell.** Geprüft, ob eine andere
  Passage sie nennt: nein. Also nicht erfunden — die Rückfrage landet in `leads`, ohne dass
  ein Feld seine Bedeutung ändert.
- **Die B2B-Bestätigung fehlt in der Feldliste von §11**, wird von der Prüfbedingung auf
  `leads` aber verlangt. Sie ohne Frage zu setzen hiesse, eine Erklärung zu fälschen. Also
  steht sie im Formular — als benannte Abweichung.
- **Sektion 6 und 8 der Startseite fehlen ganz**, weil Gründerfoto und Musterprojekte auf
  `offen` stehen. Kein Platzhalter, keine abgeschwächte Fassung.

### Vier Doppelungen weggeräumt, alle vier waren schon schädlich

| Was | Warum es schädlich war |
|---|---|
| Der Mailrahmen aus §10 stand je Dienst neu | Die Fusszeile mit dem Projekttitel fehlte im `Rechnungsdienst` bereits |
| Die sechs Domainzustände standen zweimal | Bei einer siebten Zeile wäre die zweite stillschweigend die falsche geworden |
| „heute in Europe/Berlin" stand fünfmal ausgeschrieben | Die fünfte hätte irgendwann UTC genommen — und nach Mitternacht deutscher Zeit wäre jede Frist einen Tag zu früh gewesen |
| Die Formularabwehr stand im `AnfrageService` und hätte im Kontaktformular ein zweites Mal gestanden | §17 verlangt die Nachweise für **beide** Formulare. Die zweite Fassung wäre irgendwann die schwächere gewesen |

---

## 4. Was die Tests gefunden haben — und was das wert ist

Ein Test, der nur bestätigt, was ohnehin läuft, ist Aufwand ohne Ertrag. Diese hier haben
gefunden:

| Test | Befund |
|---|---|
| `PreparedStatementsTest` | Ein zusammengesetzter Spaltenname in `Faelligkeiten`. Behoben durch zwei ausgeschriebene Anweisungen — nicht durch eine Ausnahme in der Regel |
| `TenantIsolationTest` | Hat bei **jeder** neuen Kundenroute angeschlagen — zuletzt bei `/portal/inhalte`. Genau dafür ist er gebaut |
| `SecurityHeadersTest` | Den JSON-LD-Block. Er ist jetzt **schärfer**: der eine erlaubte Datentyp fällt aus der Suche, und ein neuer Test weist an der echten Seite nach, dass der Block keine rohe spitze Klammer enthält |
| `WebsiteTest` | Neun echte Mängel, darunter drei Verstösse gegen die Verbotsliste §2, zwei erfundene Fremdpreise, ein toter Verweis auf `/agb` und zwei Branchenseiten unter der 400-Wörter-Grenze aus §10a |

**Keiner dieser Tests wurde abgeschwächt, um grün zu werden.** Zwei wurden schärfer.

---

## 5. Was beim Betreiber liegt

### 5.1 Rechtstexte freigeben

`legal_texts` steht auf `entwurf`. Die Startsperre §14a hält die produktive Veröffentlichung
von sich aus zurück; `/impressum` und `/datenschutz` liefern 404, `/agb` ist nirgends
verlinkt.

**Fünf Entwürfe liegen in `rechtstexte-entwuerfe/`**, jeder mit der Kopfzeile
`ENTWURF — NICHT GEPRÜFT, NICHT VERÖFFENTLICHEN`. Sie liegen als Dateien und nicht in der
Datenbank — ein Entwurf in `legal_texts` ist eine Zeile davon entfernt, freigegeben zu
werden.

Jede Anschrift darin steht als `[[PLATZHALTER]]`. Das ist die Markierung, nach der §14a
sucht.

> **Ein plausibel klingender Rechtstext ist gefährlicher als gar keiner.** Er wird
> veröffentlicht, weil er fertig aussieht. Ein Mensch mit juristischer Ausbildung liest ihn,
> bevor `status` auf `freigegeben` geht.

### 5.2 Hoster einrichten

Cron und Mail müssen auf echter Hardware laufen.

- **Cron:** `bin/cron.php` täglich. Der Lauf ist ausgeführt, der Zeitplan nicht eingerichtet
- **Mail:** SPF, DKIM, DMARC. Mailpit fängt lokal jede Mail ab und sagt über Zustellbarkeit
  nichts
- **TLS:** `session.cookie_secure = 1`, HSTS, `/admin/setup` über echtes `https://`
- Vollständig in `OFFENE_PRUEFUNGEN.md`

### 5.3 Vier Entscheidungen, die niemand ausser dem Betreiber treffen kann

| Punkt | Was daran hängt |
|---|---|
| **Gründername und Foto** | Startseiten-Sektion 6 und der Hero-Block auf `/ueber-uns`. Beide fehlen, solange das offen ist |
| **Musterprojekte** | Startseiten-Sektion 8. Fehlt ebenfalls |
| **Geschäftsadresse** | Das Google-Unternehmensprofil, `LocalBusiness`, jede Ortsseite und jeder Ortsname im Text |
| **Bildmaterial** | 15 gekennzeichnete Bildplätze. Die Startsperre §14a Bedingung 4 bricht die Veröffentlichung ab, solange sie leer sind |

---

## 6. Die drei Grenzen, die der Auftrag gesetzt hat

> *„Ohne Rückfrage heisst nicht ohne Sorgfalt. Diese drei Grenzen bleiben, auch wenn dich
> niemand aufhält."*

| Grenze | Stand |
|---|---|
| **Der Mandantentest wird nie abgeschwächt, um grün zu werden** | Gehalten. Er ist in A2, A3 und B **gewachsen** — von 13 auf 19 Fälle und auf 215 Zusicherungen. Bei jeder neuen Kundenroute hat er angeschlagen; jedes Mal wurde die Route eingetragen und geprüft, nie der Test gelockert |
| **Kein Rechtstext geht in `freigegeben`, solange kein Mensch ihn geprüft hat** | Gehalten. Kein Bauschritt setzt diesen Zustand. Die fünf Entwürfe liegen ausserhalb der Datenbank |
| **Kein Secret ins Repository** | Gehalten. `git check-ignore .env` bestätigt die Ausnahme; `git log --all -p` durchsucht nach `DB_PASS`, `SECRET`, `API_KEY` und `PRIVATE KEY` findet nur die **leeren** Vorlagen aus `.env.example` |

Und die vierte, die über allem steht: **Rate nie.** Acht Punkte stehen in
`OFFENE_ENTSCHEIDUNGEN.md`, weil eine Zahl, eine Tabelle oder ein Wortlaut fehlte. Keiner
davon wurde geschätzt.

---

## 7. Was nicht ausgeführt wurde

**Was nicht ausgeführt wurde, wird nicht als geprüft gemeldet.**

Nicht gelaufen sind: jede Messung im Browser (Laborwerte, Kontrast, Tastatur, 320 px), jeder
echte Mailversand über SMTP, jeder Cronlauf beim Anbieter, jeder Aufruf über echtes TLS, der
Durchlauf mit sieben Menschen nach §5c und der absichtlich provozierte Abbruch der
Startsperre.

Jeder dieser Punkte steht in `OFFENE_PRUEFUNGEN.md` mit Prüfmittel und Zeitpunkt.

---

## 8. Wo was steht

| Datei | Inhalt |
|---|---|
| `STAND.md` | Eine Seite: welche Etappe fertig ist, was als Nächstes kommt, was beim Betreiber liegt |
| `OFFENE_PRUEFUNGEN.md` | Was gebaut, aber nicht ausgeführt ist — je Zeile mit Prüfmittel |
| `OFFENE_ENTSCHEIDUNGEN.md` | Was gemeldet statt erfunden wurde, und was entschieden ist |
| `TEXTPRUEFUNG_WEBSITE.md` | Der Prüfbericht nach `SARTU_TEXTREGELN.md` §2, mit Zahlen |
| `IMPLEMENTATION_SUMMARY.md` | Der Bericht zu A0, unverändert im Stand seiner Entstehung |
| `rechtstexte-entwuerfe/` | Fünf Entwürfe, jeder als Entwurf gekennzeichnet |
