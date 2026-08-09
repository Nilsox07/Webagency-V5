# IMPLEMENTATION_PLAN — Stufe A0

**Stand:** 02.08.2026
**Umfang:** ausschließlich A0 nach `REIHENFOLGE.md` — sechs Tabellen, 26 Testfälle.
**Grundlage:** Portal-Lastenheft §1.3, §1.5, §3, §4 · `REIHENFOLGE.md` · `BAUFREIGABE.md`

---

## 1. Bestand

| Was | Zustand | Umgang |
|---|---|---|
| 19 Bauunterlagen, ~11.700 Zeilen | vollständig, Startprüfung bestanden | Quelle |
| `docker-compose.yml`, `.docker/php/*` | lauffähig, geprüft am 02.08.2026 | **übernommen, unverändert** |
| `.env.example` | Namen stimmen mit §1.5 überein | übernommen, **eine Korrektur** (siehe unten) |
| `.gitignore` | `.env`, `/vendor/`, `/storage/*` gesperrt | übernommen |
| `design/tokens.css` | 131 Zeilen, verbindlich seit 01.08.2026 | eingebunden, **nicht verändert** |
| `.claude/skills/sartu-texter/` | Textquelle | benutzt |
| **Anwendungscode** | **0 Zeilen** | — |

### Prototypen

**Es gibt keine.** Frühere Fassungen nannten einen Node/Fastify-Stand und einen
Supabase-Prototyp; im Repository liegt keiner von beiden. Damit entfällt die Abwägung aus
`CODEX_AUFTRAG_PORTAL.md` §0b, und `MIGRATION_NOTES.md` wird **nicht** angelegt — es gäbe nichts
darin zu begründen.

`design/_verworfen/` bleibt ungelesen, auch nicht als Anregung (`UEBERGABE_DATEILISTE.md`).

### Die eine Korrektur an vorhandenen Dateien

`.env.example` Zeile 19 setzt `STORAGE_DIR=/var/www/storage`. `docker-compose.yml` hängt das
Projekt nach `/var/www/html` ein — der Pfad existiert im Container nicht. Setup-Schritt 1 prüft
Schreibrechte auf `/storage`, Schritt 8 schreibt `/storage/installed.lock`; beide wären
gescheitert. §1.3 legt `/storage` ins Projektwurzelverzeichnis, die Auflösung ist also eindeutig.
**Korrigiert auf `/var/www/html/storage`, im Commit ausdrücklich benannt.**

---

## 2. Zielstruktur

Verbindlich nach §1.3. Was hinzukommt, ist unten begründet.

```
/app
  bootstrap.php            Autoload, Konfiguration, Fehlerbehandlung, Sicherheitsheader
  routes.php               Routentabelle — eine Quelle für Dispatch UND Isolationstest
  /helpers                 Env, Html, Format, Csrf, Http, Validate      (zustandslos)
  /data
    Db.php                 PDO-Fabrik, setzt SET time_zone = '+00:00'
    Uuid.php               UUIDv4 in PHP erzeugt (§4.0)
    Migrator.php           Vorprüfung, Einzelausführung, Prüfsumme, Wiederanlauf
    AuditLog.php           Schreibpfad, ausschließlich INSERT
    SessionStore.php       sessions-Tabelle
    /customer              NIMMT organization_id NUR aus CustomerScope
      CustomerScope.php    hält die Organisation der Sitzung; fehlt sie -> Fehler
      CustomerOrganizations.php
    /admin                 organisationsübergreifend, verlangt AdminGuard
      AdminOrganizations.php  AdminUsers.php
      OperatorSettings.php    LegalTexts.php  AuditEvents.php
  /services                Setup, Auth, Totp, Crypto, Mailer, OperatorSettings,
                           LegalTexts, LaunchGuard
  /views  /layouts /partials /components /pages
/public                    index.php + /assets (tokens.css zuerst)
/admin  /portal  /api      Routendefinitionen je Bereich
/bin/migrate.php           §1.5a — status | up | verify
/migrations                001..006, je genau ein Schemaobjekt
/storage                   installed.lock, maintenance.lock — außerhalb von /public
/tests
```

**Drei Zusätze zu §1.3, jeder mit Grund:**

| Zusatz | Warum |
|---|---|
| `app/routes.php` | Testfall 5a verlangt, dass der Isolationstest die **vollständige** Routenliste durchläuft und scheitert, sobald eine unbekannte Route dazukommt. Das geht nur, wenn Dispatch und Test **dieselbe** Liste lesen. Zwei Listen wären zwei Wahrheiten |
| `bin/migrate.php` | §1.5a schreibt den Befehl wörtlich vor |
| Trennung `data/customer` ↔ `data/admin` | §3 Regel 2a verlangt zwei Zugriffsschichten. Verzeichnisse statt Namenskonvention, damit ein gemeinsamer Codepfad beim Lesen auffällt |

---

## 3. Modulgrenzen

| Schicht | Darf | Darf nicht |
|---|---|---|
| `helpers` | formatieren, escapen, prüfen | Datenbank, Sitzung, Fachlogik |
| `data` | **einziger Ort mit SQL**, ausschließlich vorbereitete Anweisungen | Fachlogik, HTML |
| `services` | Fachlogik, Abläufe, Audit auslösen | eigenes SQL, HTML |
| `views` | ausgeben | SQL, Fachlogik |
| `routes` | Weg -> Handler | alles andere |

### Die Grenze zwischen Kunden- und Adminzugriff

**Kundenschicht** (`data/customer`): Jede Klasse bekommt im Konstruktor eine `CustomerScope`.
`CustomerScope` liest die Organisation aus der **Sitzung** und wirft `MissingTenantException`,
wenn sie fehlt — kein Vorgabewert, kein „alles anzeigen" (Testfall 5b). Es gibt **keine** Methode
und **keinen** Parameter, mit dem sich die Organisation von außen setzen ließe.

**Adminschicht** (`data/admin`): eigene Klassen, organisationsübergreifend. Jeder Konstruktor
verlangt ein `AdminGuard`-Objekt, das nur entsteht, wenn Rolle `admin` **und** abgeschlossene
Zweifaktor-Anmeldung vorliegen. Die zentrale Vorprüfung sitzt **einmal** im Dispatcher vor allen
`/admin/…`-Routen, nicht je Route.

**Ausdrücklich verboten und im Test nachgewiesen:** ein gemeinsamer Codepfad mit optionalem
Filter (`WHERE organization_id = ? OR ? IS TRUE`). Eine Kundenauswahl im Adminbereich schreibt
die Sitzungsorganisation **nie** um.

---

## 4. Datenmodellquelle und Migrationsreihenfolge

Quelle ist ausschließlich Portal-Lastenheft §4. Gemeinsame Felder nach §4.1, Typen nach §4.0,
`ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci`.

| # | Datei | Objekt | Abhängig von | Besonderheit |
|---|---|---|---|---|
| — | (Migrator) | `schema_migrations` | — | wird **vor** der ersten Migration angelegt |
| 001 | `001_organizations.sql` | `organizations` | — | vor `users`, weil `ON DELETE RESTRICT` |
| 002 | `002_users.sql` | `users` | 001 | `CHECK` Rolle/Organisation, `UNIQUE` E-Mail |
| 003 | `003_sessions.sql` | `sessions` | 002 | `UNIQUE` auf `token_hash` |
| 004 | `004_audit_events.sql` | `audit_events` | 002 | Index auf `created_at`, `organization_id` |
| 005 | `005_operator_settings.sql` | `operator_settings` | — | Singleton-`UNIQUE`, zwei `CHECK` |
| 006 | `006_legal_texts.sql` | `legal_texts` | — | `UNIQUE` auf `slug` |

**Je Datei genau ein Schemaobjekt** (§1.5). Indizes und Bedingungen einer Tabelle stehen in deren
`CREATE TABLE` — sie sind Teil desselben Objekts, ein zweites `ALTER TABLE` wäre ein zweiter
Schritt ohne Gewinn.

`login_tokens` gehört zu A1 und entsteht hier **nicht** — der Pflichtindex darauf ebenso wenig.

---

## 5. Reihenfolge — welcher lauffähige Stand wann

| Schritt | Ergebnis | Lauffähig woran erkennbar |
|---|---|---|
| **1** | Gerüst, Router, `tokens.css`, Fehlerseiten, Sicherheitsheader | `http://localhost:8080` antwortet mit 200, `/app` mit 403 |
| **2** | Migrator, sechs Migrationen, `bin/migrate.php` | `migrate.php status` listet 6 offene, `up` legt sie an |
| **3** | Ersteinrichtung Schritte 1–4 | leere Datenbank -> Schema steht, `.env` geschrieben |
| **4** | Schritte 5–8: Mail, Betreiberdaten, Adminkonto mit TOTP, Abschluss | Testmail in Mailpit, `/admin/setup` liefert danach 404 |
| **5** | Adminanmeldung Passwort + TOTP, Sitzungen, Audit | Anmeldung durchgängig |
| **6** | Betreiberdaten- und Rechtstextmasken, Startsperre | Änderung erzeugt Audit mit alt/neu/Grund |
| **7** | 26 Testfälle, `TenantIsolationTest` | `phpunit` grün gegen `db_test` |

Nach Schritt 1 und nach Schritt 7 wird berichtet. `/security-review` läuft nach Schritt 4
(Ersteinrichtung), Schritt 5 (Anmeldung) und Schritt 7 (Mandantentrennung).

---

## 6. Risiken

| Risiko | Woran es auffällt | Gegenmaßnahme |
|---|---|---|
| **Halb migriertes Schema** — MySQL committet Schemabefehle implizit | `schema_migrations` hat weniger Einträge als Dateien | Einzelausführung, Eintrag sofort nach Erfolg, Wiederanlauf. **Kein** `ROLLBACK`-Versprechen, kein Reparaturknopf |
| **Sitzungsorganisation fehlt und die Abfrage liefert alles** | fällt in der Oberfläche **nicht** auf | `CustomerScope` wirft, statt einen Vorgabewert zu liefern. Testfall 5b |
| **Neue Kundenroute ohne Isolationsprüfung** | fällt nie auf | Dispatch und Test lesen dieselbe `routes.php`. Testfall 5a scheitert bei Unbekanntem |
| **`ENC_KEY` verloren** | TOTP-Geheimnisse unlesbar, Admin ausgesperrt | Schlüssel nur in `.env`, Schritt 3 zeigt den Hinweis. Kein zweiter Ablageort |
| **`''` erfüllt `NOT NULL`** | Startsperre lässt Platzhalter durch | `CHECK` prüft zusätzlich `<> ''`, serverseitig `trim()`. Testfall 65 |
| **Zeitzone** | Vorgabewerte zwei Stunden daneben, unauffällig | `SET time_zone='+00:00'` direkt nach dem Verbindungsaufbau, in `Db.php` an **einer** Stelle |
| **Composer erreicht Packagist nicht** | `composer install` bricht ab | TOTP nach RFC 6238 und ein SMTP-Client sind in wenigen Zeilen eigenständig baubar. Wird nur genutzt, wenn nötig, und dann hier vermerkt |

---

## 7. Testplan

**Gegen `db_test` (MariaDB 11.4), nie gegen SQLite** (§16). Jeder Test setzt das Schema über den
**echten** Migrator auf — nicht über ein separates Testschema, sonst prüft der Test etwas anderes
als die Produktion anlegt.

| Datei | Fälle |
|---|---|
| `tests/TenantIsolationTest.php` | 5a, 5b, 43, 44, 48 |
| `tests/SetupTest.php` | 67, 68, 69, 70, 71, 72, 73 |
| `tests/MigrateCommandTest.php` | 74, 75, 76 |
| `tests/OperatorSettingsTest.php` | 64, 65, 66 |
| `tests/LegalTextsTest.php` | 81, 82 |
| `tests/SecurityHeadersTest.php` | 41, 47, 49 |
| `tests/AuditTest.php` | 55 |
| `tests/PreparedStatementsTest.php` | 50 |
| `tests/MarkupTest.php` | 58 |

**26 Fälle, jeder genau einmal.** Kein Fall wird als leere Hülle angelegt, keiner übersprungen
oder auskommentiert. Der Isolationstest wird nie abgeschwächt, um grün zu werden.

Was nicht ausgeführt wurde, kommt nach `OFFENE_PRUEFUNGEN.md` — nicht in den Bericht als „grün".

---

## 8. Offene Entscheidungen — nicht von mir

| Punkt | Stand | Was ich tue |
|---|---|---|
| **`ADMIN_NOTIFY_EMAIL`** steht in §1.5 unter „Erforderliche Werte", wird aber in keinem der acht Setup-Schritte erhoben | Lücke, gemeldet | Wert bleibt leer in der `.env`. **Kein neunter Schritt**, kein erfundener Vorgabewert. Alle Auslöser aus §10, die ihn brauchen, entstehen erst ab A1 |
| **Vier Redaktionsreste** im Lastenheft (§1.5 „sechs Schritte", „Schritt 3" für Migrationen, doppelte §1.5-Nummer, „59 Testfälle" in `docker-compose.yml`) | nach Rangfolgeregel 2 auflösbar | Ich baue gegen den Inhalt. Nachziehen der Dokumente nur auf Zuruf |
| **Rechtstexte** (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §2 — OFFEN) | anwaltliche Prüfung ausstehend | Tabelle, Zustände und Kennzeichnung `ENTWURF` werden gebaut. **Kein Textinhalt wird erzeugt.** `legal_texts` startet leer |
| **Anschrift, Rechtsform, Name** (§1) | offen für die Außendarstellung | Setup-Schritt 6 erhebt sie beim Betreiber. **Ich trage nichts vor** — auch keinen Platzhalter, der wie ein Wert aussieht |
| **§7b Karriereseite** | Richtung gewählt, eine Lesart offen | Betrifft `leads` (A1). **A0 nicht betroffen**, kein Vorbau |
| **Cron beim Hoster** (§4) | offen | A0 zeigt in Schritt 8 nur den Befehl an. Kein Lauf in A0 |

---
---

# IMPLEMENTATION_PLAN — Stufe C und der Rest von A2

**Fortgeschrieben am 09.08.2026.** Der Teil oben beschreibt A0 und bleibt unverändert stehen;
er ist Bestand, nicht Entwurf. Was hier folgt, gilt für die zwölf Fälle 84 bis 95 und die vier
offenen Fälle 51, 52, 53a und 54.

**Freigegeben durch `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4a vom 09.08.2026.** Ohne diese Entscheidung
stünde die Hälfte dieser Arbeit auf der Nicht-bauen-Liste. Die Bauvorgabe steht in
`spezifikation/18_BELEGE_UND_ZAHLUNG.md`; hier steht, wie ich sie umsetze.

## C.1 Bestand

| | Stand am 09.08.2026 |
|---|---|
| Tests | 277 grün, 3.702 Zusicherungen, gegen MariaDB 11.4 |
| Migrationen | 26, lückenlos, Prüfsummen stimmen |
| Fachtabellen | 20 von 23 — die drei aus Stufe C fehlen |
| Testfälle belegt | 84 von 95 |
| `/api/` | **existiert nicht.** Der Webhook wird der erste Endpunkt des Projekts |
| `design/tokens.css` | entschiedene Palette, stimmt mit beiden Entwürfen überein — **unberührt** |
| Logo | `design/sartu-logo-hell.svg`, `-dunkel.svg`, `sartu-mark.svg`. Noch nicht unter `public/` |

**Was `Rechnungsdienst.php` heute beschreibt und nicht kann.** Der Klassenkommentar nennt die
Rücknahme einer Zahlung und die Änderung von `due_date` als eigene protokollierte Handlungen.
Beide gibt es nicht. Ich baue sie, statt den Kommentar zu kürzen — der Kommentar hat recht.

## C.2 Die zwei Composer-Pakete — Wahl und Begründung

§4a erlaubt **genau zwei** zusätzliche Pakete. Ihre Abhängigkeiten kommen mit ihnen; sie sind
keine dritte Wahl, sondern Bestandteil der ersten beiden.

### `horstoeko/zugferd` — der ZUGFeRD-Erzeuger

| Prüfpunkt | Befund am 09.08.2026 |
|---|---|
| Fassung | `v1.0.124`, veröffentlicht am 15.06.2026 |
| Lizenz | **MIT** — verträgt sich mit einem proprietären Projekt ohne Auflage |
| Wartung | letzte Veröffentlichung acht Wochen alt, laufende Fassungsfolge über 120 Ausgaben |
| PHP 8.3 | `php >=7.3`, keine Obergrenze |
| Erweiterungen | `ext-fileinfo`, `ext-simplexml` — beide im Bild vorhanden |

**Warum dieses und kein anderes.** Es ist die einzige PHP-Bibliothek, die den Weg vollständig
abdeckt: Aufbau des Datensatzes nach EN 16931, Ausgabe als XML, **Einbettung in ein bestehendes
PDF nach PDF/A-3** und Prüfung gegen die mitgelieferten Schemata. Ein Erzeuger, der nur XML kann,
hätte die Einbettung als dritte Abhängigkeit nach sich gezogen.

### `dompdf/dompdf` — HTML nach PDF

| Prüfpunkt | Befund am 09.08.2026 |
|---|---|
| Fassung | `v3.1.6`, veröffentlicht am 20.07.2026 |
| Lizenz | **LGPL-2.1-only** — unveränderte Nutzung über Composer ist zulässig; ich ändere keine Zeile daran |
| Wartung | letzte Veröffentlichung drei Wochen alt |
| PHP 8.3 | `php ^7.1 \|\| ^8.0` |
| Erweiterungen | `ext-dom`, `ext-mbstring` — vorhanden |

**Warum nicht mPDF.** mPDF steht unter **GPL-2.0-only**. Ein proprietäres Projekt, das GPL-Code
einbindet, gerät in einen Lizenzkonflikt. Das ist kein Geschmacksurteil, sondern der Grund,
warum mPDF trotz besserer HTML-Abdeckung ausscheidet.

**Warum nicht TCPDF.** Es rendert HTML nur eingeschränkt und kennt `design/tokens.css` nicht
annähernd. Die Belegvorlage soll aus denselben Gestaltungswerten entstehen wie die Oberfläche.

### Eine Erweiterung, kein Paket: `ext-xsl`

**Das ist die einzige Stelle, an der ich über den Auftrag hinausgehe, und ich sage es vorher.**

§3 und die Fälle 88 und 89 verlangen die Prüfung gegen **Schema und Schematron**. Das Schema
prüft `libxml`, das im Bild liegt. Schematron ist eine XSLT-Umsetzung und braucht `ext-xsl` — die
Erweiterung fehlt im Bild.

`ext-xsl` ist **kein Composer-Paket**, sondern eine Standarderweiterung von PHP; die Grenze aus
§4a ist damit nicht berührt. Sie berührt aber die **Anforderungen an den Hoster** und gehört
deshalb in `LIVEGANG.md`. Ohne sie ist Fall 88 nicht erfüllbar, und ein Beleg ginge ungeprüft
hinaus.

**Der Weg der Schematron-Regeln selbst** ist beim Bauen zu klären: Liefert das Paket sie mit,
werden sie von dort genommen. Liegt nur der KoSIT-Prüfer als Java-Aufruf bei, ist das keine
gangbare Abhängigkeit — dann steht die Lücke mit Grund und Mittel in `OFFENE_PRUEFUNGEN.md`,
und die Schemaprüfung greift allein. **Ich melde das, statt es zu überspielen.**

## C.3 Zielstruktur

```
/app/data       NummernkreisSpeicher · Belegspeicher · Zahlungsereignisse
                Admin\AdminBelege · Customer\KundenBelege
/app/services   Nummernkreis · Belegerzeugung · Rechnungsdokument · Angebotsdokument
                ERechnung (Aufbau, Prüfung) · Stornodienst · Mollie (Anlage, Abruf)
                Zahlungsabgleich · Steuerexport · Ersteinrichtungsstand
/api            MollieWebhook — der erste Endpunkt unter /api
/admin          BelegeSteuerung · ErsteinrichtungSteuerung (Erweiterung der vorhandenen)
/portal         Belegabruf im Kundenbereich
/migrations     027 bis 033
```

**SQL ausschließlich in `/app/data`**, Fachlogik ausschließlich in `/app/services`. Die
Belegvorlage ist eine Ansicht unter `/app/views` und enthält keine Fachlogik.

## C.4 Migrationsreihenfolge — eine je Schemaobjekt

| Nr. | Objekt | Warum getrennt |
|---|---|---|
| `027` | `number_sequences` | `CREATE TABLE` löst ein implizites Commit aus |
| `028` | `documents` | dito |
| `029` | `payment_events` | dito |
| `030` | `invoices.issued_at` | `ALTER TABLE` ebenso |
| `031` | `invoices.cancels_invoice_id` | eigener Fremdschlüssel, `ON DELETE RESTRICT` |
| `032` | `invoices.payment_provider_id` | |
| `033` | `operator_settings.mollie_key_test`, `mollie_key_live` | zwei Spalten **eines** Objekts, ein `ALTER` |

**Kein `down`.** MySQL nimmt eine Schemaänderung nicht zurück. Eintrag in `schema_migrations`
unmittelbar nach jedem Erfolg.

## C.5 Reihenfolge — welcher lauffähige Stand wann

1. **A2 schließen** — Rücknahme und `due_date`, Fälle 51, 52, 53a, 54
2. **Migrationen 027 bis 033** — danach liegen alle 23 Tabellen
3. **Nummernkreise** — Fälle 84, 85. Ab hier wird die Rechnungsnummer nicht mehr getippt
4. **Belegerzeugung und Prüfung** — Fälle 87, 88, 89, 94
5. **Storno** — Fall 86
6. **Mollie** — Fälle 90, 91, 92, 93. Erst hier entsteht `/api`
7. **Abruf, Versand, Export** — Fall 95
8. **Menüpunkt Ersteinrichtung**
9. **`VERFAHRENSDOKUMENTATION.md`**

Nach jedem Schritt läuft die vollständige Testreihe. Ein Schritt gilt als fertig, wenn sie grün
ist — nicht, wenn der Code steht.

## C.6 Risiken, die ich vorher benenne

| Risiko | Was ich tue |
|---|---|
| **Fall 85 verlangt echte Nebenläufigkeit.** Zwei Anlagen nacheinander prüfen ihn nicht | Ich versuche zwei getrennte PDO-Verbindungen mit `FOR UPDATE` gegeneinander. Trägt der Versuch nicht, wird der Zähler trotzdem richtig gebaut, der Test kommt so nah wie möglich heran, und die Lücke steht mit einer Zeile in `OFFENE_PRUEFUNGEN.md` |
| **Der Webhook braucht eine öffentlich erreichbare Adresse** | Geprüft wird gegen einen nachgebildeten Aufruf mit eingesetztem Mollie-Zugriff. Der echte Zustellweg bleibt ungeprüft und wird eingetragen |
| **Die Nummer wird bisher vom Admin getippt** und ist Pflichtfeld im Formular | Das Feld entfällt. Vorhandene Tests, die eine Nummer übergeben, werden auf die Vergabe umgestellt — **nicht** die Vergabe auf das Feld |
| **`stornieren()` setzt heute nur einen Status** | Der Vorgang wird geteilt: Entwurf verwerfen, Stornorechnung, Zahlung zurücknehmen. Drei Wege, drei Prüfungen |
| **`ext-xsl` fehlt im Bild** | Erweiterung nachziehen und in `LIVEGANG.md` als Hosteranforderung eintragen. Fehlt sie, fällt Fall 88 — nicht die Prüfung |

## C.7 Testplan

| Fall | Wo er entsteht |
|---|---|
| 51, 52, 53a, 54 | `tests/AuftragsstreckeTest.php` — die Strecke, an der A2 gemessen wird |
| 84, 85 | `tests/NummernkreisTest.php` |
| 86 | `tests/StornoTest.php` |
| 87, 88, 89, 94 | `tests/BelegeTest.php` |
| 90, 91, 92, 93 | `tests/ZahlungsabgleichTest.php` |
| 95 | `tests/SteuerexportTest.php` |

`tests/TenantIsolationTest.php` bekommt jede neue Route eingetragen — auch die unter `/api`.
**Er wird nicht abgeschwächt**; schlägt er an, ist das seine Aufgabe.

## C.8 Wo ich anhalte

- Ein drittes Composer-Paket wäre nötig
- Die Schematron-Regeln sind ohne Java nicht zu bekommen → melden, nicht ersetzen
- Eine Zahl fehlt, ein Dokument widerspricht einem anderen
