# IMPLEMENTATION_SUMMARY — Stufe A0

**Stand:** 02.08.2026
**Zweig:** `claude/php-a0-modellplan-06duus`
**Umfang:** genau A0 nach `REIHENFOLGE.md`. Nichts aus A1 bis C.

---

## 1. Die vier Abnahmepunkte

`REIHENFOLGE.md`: *„Fertig, wenn: Installation läuft auf leerer Datenbank durch · ein Admin
meldet sich mit TOTP an · eine Testmail kommt an · der Mandantentest im Umfang von A0 läuft
grün."*

| # | Punkt | Stand | Beleg |
|---|---|---|---|
| 1 | Installation auf leerer Datenbank | **erfüllt** | Durchlauf vom 02.08.2026, Schritt 1 bis 8; danach liefert `/admin/setup` 404 |
| 2 | Admin meldet sich mit Passwort und TOTP an | **erfüllt** | Durchlauf: Passwort → Code → `/admin` mit `<h1>Übersicht</h1>` |
| 3 | Testmail kommt nachweislich an | **erfüllt** | Mailpit: `noreply@sartu.local` → `betreiber@example.org`, „Testnachricht aus der Einrichtung" |
| 4 | `TenantIsolationTest` im Umfang von A0 grün | **erfüllt** | 13 Tests, gegen MariaDB 11.4 |

**Gesamt: 101 PHPUnit-Tests, 467 Zusicherungen, grün.** Sie decken die 26 A0-Testfälle ab; die
übrigen sichern die Befunde aus Abschnitt 5a.

---

## 2. Was entstanden ist

**Rund 6.200 Zeilen Anwendungscode und 3.000 Zeilen Tests**, aus 0 Zeilen.

### Tabellen — sechs, plus das Protokoll

`organizations` · `users` · `sessions` · `audit_events` · `operator_settings` · `legal_texts`
Dazu `schema_migrations`, das nicht mitzählt.

**Acht Migrationsdateien, je genau ein Schemaobjekt** (§1.5). Zwei davon sind Trigger: Sie
verbieten `UPDATE` und `DELETE` auf `audit_events` — in der Datenbank, nicht im Code. Eine
Absicht im Anwendungscode wäre kein Beleg für Testfall 55.

### Struktur

Verbindlich nach §1.3. Drei Zusätze, jeder im `IMPLEMENTATION_PLAN.md` begründet:
`app/routes.php` (eine Liste für Dispatch **und** Isolationstest), `bin/migrate.php` (§1.5a
schreibt ihn wörtlich vor), und die Trennung `app/data/customer` ↔ `app/data/admin`.

### Die sechs Funktionen

| Funktion | Wo |
|---|---|
| Ersteinrichtung, acht Schritte | `app/services/Ersteinrichtung.php`, `admin/SetupSteuerung.php` |
| Migrationen, einzeln und protokolliert | `app/data/Migrator.php`, `bin/migrate.php` |
| Adminanmeldung mit Passwort und TOTP | `app/services/AnmeldeDienst.php`, `Zweifaktor.php` |
| Betreiberdaten | `app/services/BetreiberdatenDienst.php`, `app/data/BetreiberdatenSpeicher.php` |
| Rechtstexte mit Freigabezustand | `app/data/RechtstexteSpeicher.php`, `admin/RechtstexteSteuerung.php` |
| Testmailversand | `app/services/Mailversand.php`, `admin/TestmailSteuerung.php` |
| Mandantentrennung | `app/data/customer/KundenBereich.php`, `app/data/admin/AdminNachweis.php` |
| Prüfprotokoll | `app/data/AuditProtokoll.php` + Migrationen 005 und 006 |

---

## 3. Die drei Entscheidungen, die den Rest tragen

### Mandantentrennung ist eine Signatur, keine Absicht

`KundenBereich` hat einen **privaten Konstruktor** und **eine** Fabrik: `ausSitzung()`, ohne
Parameter. Es gibt keinen Weg, die Organisation von außen zu setzen — ein Formularfeld kann
nichts umbiegen, was es als Parameter nicht gibt. Jede Klasse in `app/data/customer` verlangt
dieses Objekt im Konstruktor.

Spiegelbildlich verlangt jede Klasse in `app/data/admin` einen `AdminNachweis`, und den gibt es
nur bei Rolle `admin` **und** abgeschlossener Zweifaktor-Anmeldung.

`TenantIsolationTest` prüft beide Eigenschaften **über Reflection**, nicht über Beispielaufrufe:
Wer eine zweite Fabrik oder einen Parameter hinzufügt, lässt den Test scheitern.

### Eine Routenliste, nicht zwei

Testfall 5a verlangt, dass der Isolationstest die **vollständige** Kundenroutenliste durchläuft
und scheitert, sobald eine unbekannte dazukommt. Das geht nur, wenn Dispatcher und Test dieselbe
Datei lesen. In A0 ist die Liste **leer** — der Test erwartet genau das und schlägt an, sobald
A1 die erste Portalroute anlegt.

### Vier Vorprüfungen, alle an einer Stelle

Wartungsmodus, Einrichtungssperre, Adminprüfung, CSRF — zentral im `Router`, nicht je Route.
§3 Regel 2a verlangt das ausdrücklich: *„vollständig durch eine einzige, zentrale Vorprüfung
geschützt, nicht Route für Route einzeln."*

**Die Reihenfolge wurde bewusst gedreht:** Die Adminprüfung steht **vor** der CSRF-Prüfung. Sonst
beantwortet ein unangemeldeter `POST` die Frage „ist mein Token gültig" — eine Auskunft an
jemanden, der dort nichts zu suchen hat.

---

## 4. Abweichungen vom Plan

| Abweichung | Grund |
|---|---|
| **Acht statt sechs Migrationsdateien** | Der Plan nannte sechs Tabellen. Die beiden Trigger für `audit_events` sind eigene Schemaobjekte und brauchen nach §1.5 je eine eigene Datei |
| **`app/data/AnmeldeKonten.php` kam dazu** | Die Anmeldung kann nicht über die Adminschicht gehen — die verlangt einen Nachweis, den die Anmeldung erst herstellt. Statt den Nachweis dafür aufzuweichen, gibt es einen absichtlich schmalen eigenen Lesezugriff: genau ein Konto, keine Liste, kein Filter |
| **`bin/cron.php` kam dazu** | §1.5 Schritt 8 verlangt, einen Cron-Befehl zum Kopieren anzuzeigen. Ein Befehl, der auf eine nicht existierende Datei zeigt, ist eine Anzeige ohne Deckung. Der Lauf räumt abgelaufene Anmeldungen ab (§3 Regel 6) — die Läufe aus A1 und A2 sind **nicht** vorbereitet |
| **`MIGRATION_NOTES.md` entfällt** | Es gibt keinen Prototyp im Repository. Es gäbe nichts zu begründen |
| **`app/services/VerbrauchteCodes.php` kam dazu** | Entwertet TOTP-Codes nach RFC 6238 §5.2 (Befund C unten). Die Ablage liegt als Datei in `/storage`, **nicht** als Spalte: `users` hat im Datenmodell §4 kein Feld dafür, und eines zu erfinden verstößt gegen „nichts erfinden". **Falls ein Feld `users.totp_last_step` gewünscht ist, ist das eine Entscheidung des Betreibers** |

---

## 5. Sechs Befunde an vorhandenen Dateien

Vier Redaktionsreste im Lastenheft, zwei echte Fehler in der Umgebungsvorlage. **Entschieden habe
ich nichts** — die vier oberen sind nach der Rangfolgeregel auflösbar, die zwei unteren gegen §1.3
und §1.5 eindeutig.

| # | Fundstelle | Befund | Umgang |
|---|---|---|---|
| 1 | Portal-Lastenheft §1.5, Z. 300 | Fließtext sagt **„Sechs Schritte"**, die Tabelle darunter hat **acht** | Der Korrekturblock begründet die acht. Gegen die Tabelle gebaut. **Dokument nicht geändert** |
| 2 | §1.5 Z. 345/424/482, §16 Fall 69 | „Schritt **3**" meint durchweg die **Migrationen** — die sind in der neuen Nummerierung Schritt **4** | Gegen den Inhalt gebaut. **Dokument nicht geändert** |
| 3 | Gliederung | **§1.5 kommt zweimal vor**; §1.4a steht hinter §1.6 | Reine Gliederungsfrage. **Nicht geändert** |
| 4 | `docker-compose.yml` Z. 43 | Kommentar spricht von **„59 Testfällen"**, maßgeblich sind **88** | **Nicht geändert** — auf Zuruf |
| 5 | `.env.example` Z. 19 | `STORAGE_DIR=/var/www/storage` existiert im Container nicht. Setup-Schritt 1 und 8 wären daran gescheitert | **Korrigiert** auf `/var/www/html/storage`, im Commit benannt |
| 6 | `.env.example` Z. 41 | `MAIL_FROM=noreply@localhost` ist keine gültige Absenderadresse — `localhost` hat keinen Punkt. Setup-Schritt 5 wäre nie durchgekommen | **Korrigiert** auf `noreply@sartu.local`, im Commit benannt |

**Dazu eine Lücke, die ich nicht schließe:** `ADMIN_NOTIFY_EMAIL` steht in §1.5 unter
„Erforderliche Werte", wird aber in keinem der acht Setup-Schritte erhoben. Er blockiert A0 nicht
— alle Auslöser aus §10 entstehen ab A1. **Kein neunter Schritt, kein erfundener Vorgabewert.**

---

## 5a. Sicherheitsprüfung — vier Befunde am eigenen Code, alle behoben

Nach Anmeldung, Ersteinrichtung und Mandantentrennung lief `/security-review`. Drei Befunde kamen
von dort, einer aus `/run`. **Alle vier waren echt, alle vier sind behoben und je einzeln durch
einen Test belegt.**

Die drei Sicherheitsbefunde hatten dieselbe Form: Eine Zusage stand im Lastenheft, der Code hielt
sie **fast**, und keiner der 81 Tests hat den Unterschied bemerkt.

| # | Befund | Warum das zählt | Behoben durch | Test |
|---|---|---|---|---|
| **A** | **`POST /admin/setup/abschluss` prüfte gar nichts.** Ein einziger unangemeldeter Aufruf gegen eine frische Installation hätte die Sperre gesetzt — ohne Adminkonto, ohne Datenbank. Zurücknehmen lässt sie sich über das Netz nicht; das ist ihr Zweck | Die Strecke ist absichtlich ohne Anmeldung erreichbar (es gibt noch kein Konto). Damit war die eigene Sperre der Hebel gegen den Betreiber. Dasselbe galt für `/admin/setup/admin`: Wer dort vor dem Betreiber ankam, hatte das einzige Adminkonto | `SetupSteuerung::nurInSchritt()` — jeder POST läuft nur in seinem Schritt. Dazu setzt `InstallationsSperre::setzen()` jetzt die **Datenbank vor der Datei**: Umgekehrt bliebe bei einem Fehler eine Sperrdatei liegen, ohne dass die Einrichtung je fertig wurde | `SetupTest`, 4 Fälle |
| **B** | **Der zweite Faktor hatte keinen Zähler.** Nur Schritt 1 zählte. Wer das Passwort hatte, brauchte einen Versuch dafür und konnte danach beliebig oft sechsstellige Codes senden | §3 Regel 10 nennt die Zweifaktor-Anmeldung Pflicht. Eine Pflicht, die sich durchprobieren lässt, ist keine — ~500.000 Versuche sind eine Frage von Stunden | Eigener Zähler für den zweiten Faktor, fünf Versuche je Stunde. Der Vormerk zwischen Passwort und Code verfällt nach fünf Minuten, statt für die Lebensdauer der Sitzung zu stehen | `AnmeldungTest`, 3 Fälle |
| **C** | **Ein TOTP-Code galt zweimal.** RFC 6238 §5.2 verlangt, dass ein angenommener Code entwertet wird | Ein mitgelesener Code — geteilter Bildschirm, Zwischenstelle — war innerhalb seiner dreißig Sekunden erneut einlösbar. Die erfolgreiche Anmeldung des Betreibers entwertete ihn nicht | `VerbrauchteCodes`. **Und ein zweiter Anlauf**: Die erste Fassung hakte den *laufenden* Zeitschritt ab — ein Code aus dem Schritt davor wäre unter der falschen Nummer vermerkt gewesen. `Zweifaktor::zeitschrittZumCode()` gibt jetzt den Schritt zurück, zu dem der Code **wirklich** gehört | `AnmeldungTest`, 4 Fälle |
| **D** | **Jedes Formularfeld hieß `components/feld`.** `Ansicht::teil()` nannte seinen ersten Parameter `$name`, und `extract(EXTR_SKIP)` überschreibt Vorhandenes nicht | Kein Formular der Anwendung wäre bedienbar gewesen. **Die Seite sah dabei vollkommen richtig aus**, und alle 81 Tests waren grün. Aufgefallen ist es erst beim Aufruf im Browser | Parameter heißen `$__ansicht` und `$__werte` | `MarkupTest`, 2 Fälle |

**Zwei weitere Punkte aus der Prüfung, keine Befunde, aber Zusagen ohne Deckung:**

| Punkt | Vorher | Jetzt |
|---|---|---|
| **`sessions` wurde geschrieben, aber nie gelesen** | §3 Regel 6 verspricht serverseitige Sitzungen. Die Zeile entstand beim Anmelden und verschwand beim Abmelden — geprüft hat sie niemand. Eine Anmeldung war damit nicht zurückziehbar, solange das PHP-Cookie galt | Die zentrale Adminprüfung im Router verlangt beides: den Sitzungszustand **und** eine gültige Zeile in `sessions` |
| **`AdminNachweis::fuerErsteinrichtung(true)`** | Ein Parameter, der ausnahmslos mit `true` übergeben wurde. Eine Bedingung, die der Aufrufer selbst setzt, ist keine | Die Methode fragt jetzt die Installationssperre und wirft nach dem Abschluss der Einrichtung |

**Was die Prüfung ausdrücklich nicht gefunden hat**, nachgerechnet statt angenommen: keine
SQL-Einschleusung an ~35 Aufrufstellen · kein unescaptes Ausgabefeld in den Ansichten · keine
Umgehung der Mandantentrennung · kein Weg, die Installationssperre über das Netz zu lösen ·
kein Pfaddurchgriff · kein Konfigurationsdurchgriff über `.env`.

---

## 5b. Aufräumen — was angewendet wurde und was nicht

`/simplify` lief mit **zwei** Prüfern statt der vorgesehenen vier: Wiederverwendung und
Vereinfachung arbeiten auf denselben Dateien, ebenso Effizienz und Bauhöhe. Zusammen
22 Befunde.

**Angewendet — die sieben, die etwas kosten:**

| Was | Warum es zählte |
|---|---|
| **`Speicher::verzeichnis()`** löst `/storage` an einer Stelle auf statt an fünf | Eine der fünf war **schon auseinandergelaufen**: Setup-Schritt 1 prüfte die Schreibrechte auf einem anderen Verzeichnis, als die Ratenbegrenzung später beschrieb. Beide Zweige funktionierten für sich — genau so entstehen Fehler, die niemand sieht |
| **`ZahlenlistenDatei`** trägt die Ablage für Ratenbegrenzung und Wiederholungssperre | Beide hatten dieselbe Datei-Logik zweimal, ohne dass ein Test sie verband |
| **`Antwort::nichtGefunden()`** statt drei Kopien derselben 404-Seite | §3 Regel 2 verlangt, dass „gibt es nicht" und „gehört dir nicht" ununterscheidbar sind. Drei Wortlaute an drei Stellen sind der schnellste Weg, das zu verlieren |
| **Sicherheitskopfzeilen an einem Ausgang** statt an sieben Rückgabestellen | Testfall 47 verlangt sie „in allen Antworten". Sieben Wiederholungen sind sieben Gelegenheiten, eine zu vergessen |
| **`app/views/partials/fuss.php` liest nicht mehr aus der Datenbank** | Eine Abfrage auf **jeder** Antwort, auch auf 404, 419 und der Wartungsseite — und ein `try/catch` drumherum, weil die Ansicht selbst nicht sicher sein konnte. Genau das verbietet der Kopf von `Ansicht`. Die Angaben aus §1.4a gehören in den Fußbereich der **öffentlichen** Website, und die entsteht nach Stufe B |
| **Ein Schrittwächter-Test** für jede schreibende Einrichtungsroute | Der Wächter steht in jedem Handler einzeln. Eine neunte Route ohne ihn fiele sonst niemandem auf — und das ist die Route, die Befund A wieder aufmacht |
| **Toter Code und doppelte Abfragen entfernt** | `AdminBenutzer` hatte drei Methoden, die `AnmeldeKonten` byte-gleich schon hatte, und `Ersteinrichtung` stellte dieselbe Frage über beide Klassen |

**Dazu Kleinigkeiten:** elf unbenutzte Importe, ein doppelter `catch` mit gleichem Rumpf, und
die Umgebungsprüfung läuft einmal je Seitenaufruf statt dreimal.

**Ein Befund war besonders unangenehm:** `OeffentlicheSeiten` und `RechtstexteSteuerung` hatten
eigene Beschriftungstabellen — **derselbe Fehler**, den ich eine Stunde vorher in
`BetreiberdatenSpeicher` behoben und dort ausdrücklich kommentiert hatte. Auch behoben.

**Nicht angewendet, mit Grund:**

| Vorschlag | Warum nicht |
|---|---|
| **Ein Kompositionswurzel- oder Dienstcontainer** für die 29 Stellen mit `$this->x ?? new X()` | Der Befund stimmt: So bringt Zwischenspeichern nichts, weil sechs Aufrufstellen sechs eigene Objekte bauen. Aber das ist ein Umbau der Verdrahtung **der ganzen Anwendung** — nach A0, wo die Anzahl der Aufrufstellen feststeht, und nicht als Nebensache am Ende einer Etappe |
| **`EinrichtungsStand` aus `Ersteinrichtung` herauslösen** | Richtig gesehen — Prädikate und Mutationen in einer Klasse. Derselbe Grund: ein Schnitt, der A1 betrifft, gehört an den Anfang von A1 |
| **`operator_settings` je Anfrage zwischenspeichern** | Hängt am Punkt darüber. Ohne geteilte Objekte spart es fast nichts |
| **Schema nur einmal je Testlauf aufbauen, dann `TRUNCATE`** | Spart rund acht Sekunden. Der Aufbau über den **echten** Migrator je Test ist aber die Zusage aus `REIHENFOLGE.md`, und acht Sekunden sind kein Grund, sie anzufassen |
| **Betreiberdaten-Formular als gemeinsames Partial** (Setup-Schritt 6 und Adminmaske) | Der Befund stimmt, §1.3 verbietet Markup-Kopien. Die beiden Masken unterscheiden sich aber in Umfang und Zweck, und ein gemeinsames Partial mit Schalter wäre in A1 wieder aufzutrennen. **Als offener Punkt vermerkt**, nicht als erledigt |
| **Testhelfer in `Datenbankfall` zusammenziehen** | Fünf Testklassen bauen ihr Arbeitsverzeichnis selbst. Lohnt sich, gehört aber zu dem Zeitpunkt gemacht, an dem A1 die sechste Klasse anlegt |

---

## 6. Textprüfbericht

Nach `SARTU_TEXTREGELN.md` Abschnitt 2. Grundlage sind **182 Textzeilen**, gezogen aus allen
Ansichten, Beschriftungen, Hinweisen und Fehlermeldungen der Stufe A0.

```text
TEXTPRUEFUNG   Seite: Oberfläche Stufe A0   Datum: 02.08.2026

Sätze gesamt                           206
Längster Satz                           14 Wörter      Grenze 20
Sätze über 20 Wörter                     0             Grenze 0
Absätze mit mehr als 3 Sätzen            0             Grenze 0
Aufzählungen >3 Glieder im Satz          0             Grenze 0
Gegensatzformel                          0             Grenze 2
Treffer Wortliste A                      0             Grenze 0   (siehe Anmerkung)
Treffer Wortliste B                      0             Grenze 0
Treffer Wortliste C                      0             Grenze 0
"individuell"                            0             Grenze 3
Sie / Ihr / Ihre / Ihnen                54
wir / uns / unser                        6             muss ≤ Sie sein   ✓
H2 über 9 Wörter                         0             Grenze 0
Überschriften ohne Nachprüfbares         7             Grenze 0   — begründet, siehe unten
Konjunktive in Überschriften             0             Grenze 0
Behauptungen über Kunden / Markt         0             Grenze 0
Argument gilt auch für 500-€-Seite      entfällt       (Bedienoberfläche, kein Verkaufstext)
```

**Anmerkung zu Wortliste A:** Das Zählskript meldet einen Treffer auf `Klartext`. Das ist der
Wortanfang `klar`, nicht das verbotene Eigenschaftswort — `Klartext` ist ein konkretes Substantiv
und bleibt.

**Die eine gerissene Grenze, einzeln benannt und begründet** (`SARTU_TEXTREGELN.md` Abschnitt 2
lässt das ausdrücklich zu):

Sieben Überschriften enthalten nichts Nachprüfbares: `Anmeldung` · `Übersicht` ·
`Betreiberdaten` · `Rechtstexte` · `Bankverbindung` · `Steuer und Register` · `Freigeben`.

**Begründung:** Regel 9 ist für Verkaufstext geschrieben — dort ersetzt eine unbestimmte
Überschrift eine Behauptung. Hier sind es **Abschnittsbeschriftungen einer Bedienoberfläche**.
Der Texter-Skill regelt sie unter „Mikrotexte": *ein Label sagt, was hineingehört.*
`Bankverbindung` sagt genau das. Eine Behauptung an dieser Stelle (`Ihre IBAN wird geprüft`)
wäre eine Werbezeile über einem Eingabefeld — und Regel 6 verbietet, dass die Seite sich selbst
erklärt.

**Was aus den Regeln übernommen wurde:**

- **Schaltflächen tragen Verb und Gegenstand:** `Verbindung testen und speichern` ·
  `Testnachricht senden` · `Betreiberdaten speichern` · `Konto anlegen und Code prüfen`.
  Kein `Absenden`, kein `Weiter`, kein `OK`
- **Fehlermeldungen sagen, was falsch ist und wie es richtig geht:** *„Eine deutsche Postleitzahl
  hat fünf Ziffern."* statt *„Ungültige Eingabe."*
- **Keine Entschuldigung, keine Schuldzuweisung.** Die Sammelzeile über Fehlern lautet
  *„Das hat noch nicht geklappt."*
- **Zahlen statt Eigenschaftswörtern:** `mindestens zwölf Zeichen` · `alle 30 Sekunden` ·
  `Schritt 5 von 8` · `sechsstellig`
- **Nach außen kein `App`, `Software`, `Tool`, `Dashboard`, `System`** (§1.6). Intern steht
  „interner Bereich"

---

## 7. Gestaltung

`design/tokens.css` liegt unverändert unter `public/assets/css/tokens.css` und wird **vor** jedem
Bauteil-CSS eingebunden. `MarkupTest` prüft die Reihenfolge auf jeder gerenderten Seite,
`SecurityHeadersTest` die Byte-Gleichheit mit der Quelle — die beiden Dateien können nicht
auseinanderlaufen.

`public/assets/css/anwendung.css` enthält **keinen Farbwert, keine Farbfunktion und keinen
Radius als Zahl**. `MarkupTest::testBauteilCssBenutztNurVariablen` prüft das.

Eine Akzentfarbe: `--lime`. Kein Rot für Fehler, kein Grün für Erfolg — Meldungen unterscheiden
sich über Fläche, Kante und Überschrift. Lime steht nur als Fläche, immer mit `1px --line` als
Kante und `--ink` als Schrift. Kunden- und Adminbereich sind unterscheidbar: Der interne Bereich
trägt ein dunkles Kopfband.

**Keine Gestaltungsentscheidung getroffen. Keine Sonderform angelegt.**

---

## 8. Offene Punkte

Vollständig in `OFFENE_PRUEFUNGEN.md`. Die drei, die vor dem Livegang zwingend sind:

1. **HTTPS und Mailversand auf dem Zielhoster** praktisch prüfen — Testmail an eine fremde
   Adresse, Cronlauf, der schreibt (§1.4)
2. **`session.cookie_secure = 1`** in der Produktionskonfiguration. Lokal steht es auf `0`, weil
   es kein TLS gibt
3. **Rechtstexte** — `legal_texts` ist leer, und die Startsperre lässt deshalb nicht durch. Das
   ist der gewollte Zustand (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §2)
