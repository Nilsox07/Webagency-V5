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
