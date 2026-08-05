# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Was dieses Repository ist

**SARTU** ist eine Webdesign-Agentur für regionale Betriebe. Dieses Repository enthält
**eine** PHP-Anwendung: öffentliche Website unter `/`, Kundenbereich unter `/portal/`, interner
Bereich unter `/admin/`, Serverfunktionen unter `/api/`. Ein Repository, eine Domain, ein
Deployment.

**Stand: ~12.000 Zeilen Spezifikation, Anwendungscode im Aufbau (Stufe A0).** Die Spezifikation
ist die Wahrheit, nicht der Code. Wo beides auseinandergeht, gewinnt die Spezifikation — und der
Widerspruch wird gemeldet, nicht stillschweigend aufgelöst.

Die Oberfläche und alle Dokumente sind **deutsch**. Bezeichner im Datenmodell sind englisch
(`organizations`, `users`), Statuswerte und `operator_settings` deutsch (`angebot_offen`,
`firmenname`) — das ist so vorgegeben und wird nicht vereinheitlicht.

## Befehle

**Alles läuft im Container.** Auf dem Wirtssystem gibt es kein projektbezogenes PHP; ein
fehlgeschlagenes `php -v` dort ist kein Grund anzuhalten.

```bash
docker compose up -d --build          # Umgebung starten (erster Lauf: einige Minuten)
docker compose exec app php -v        # 8.3+
docker compose exec app php -m        # muss pdo_mysql sodium mbstring intl fileinfo openssl enthalten
docker compose exec app composer install
docker compose exec db mariadb -u sartu -p
```

| Zweck | Befehl |
|---|---|
| Alle Tests | `docker compose exec app vendor/bin/phpunit` |
| **Ein** Test | `docker compose exec app vendor/bin/phpunit --filter testAdminHatKeineOrganisation` |
| Eine Testdatei | `docker compose exec app vendor/bin/phpunit tests/TenantIsolationTest.php` |
| Syntaxprüfung | `docker compose exec app php -l pfad/zur/datei.php` |
| Migrationsstand | `docker compose exec app php bin/migrate.php status` |
| Migrationen einspielen | `docker compose exec app php bin/migrate.php up --backup=/pfad/zur/sicherung.sql` |
| Prüfsummen | `docker compose exec app php bin/migrate.php verify` |

| Adresse | Was |
|---|---|
| http://localhost:8080 | die Anwendung |
| http://localhost:8025 | Mailpit — fängt **jede** lokal versendete Mail ab |

**Vor dem ersten Start:** `cp .env.example .env`, dann `DB_PASS` **und** `DB_ROOT_PASSWORD`
füllen (sonst startet die Datenbank nicht) und `STORAGE_DIR=/var/www/html/storage` setzen — im
Container liegt das Projekt unter `/var/www/html`. `.env` wird nie committet.

**Zwei Datenbanken, absichtlich:** `db`/`sartu` zum Arbeiten, `db_test`/`sartu_test` für die
Tests. Die Tests leeren Tabellen. Tests gegen SQLite sind ausdrücklich verboten
(Portal-Lastenheft §16) — SQLite verhält sich bei Transaktionen und Fremdschlüsseln anders.

## Wo die Spezifikation steht

**`spezifikation/` ist seit 03.08.2026 die geltende Fassung** — ein Thema, eine Datei, eine
Wahrheit. Einstieg: **`spezifikation/00_UEBERSICHT.md`**. Dort steht auch das
**Dublettenregister**: wo dieselbe Sache früher mehrfach mit abweichenden Werten stand, welche
Fassung gilt und warum.

Die alten Lastenhefte bleiben liegen. Sie tragen die **Begründungsblöcke** („Ersetzt am
01.08.2026, weil …"), die verhindern, dass eine zurückgezogene Fassung wieder eingebaut wird.
Wo eine Themendatei noch feldgenaue Restvorgaben in ihrer Quelle lässt, sagt sie das in ihrem
Schlussabschnitt.

**Abgelöst und nach `archiv/` verschoben:** das alte Website-Konzept, die Marktanalyse und
`archiv/konzepte/`. Begründung je Datei in `archiv/LIESMICH.md`.

## Rangfolge der Dokumente

Bei **jedem** Widerspruch gilt diese Reihenfolge. Sie steht allein in `UEBERGABE_DATEILISTE.md`;
`CODEX_AUFTRAG_PORTAL.md` §2 ist nur eine *Lese*reihenfolge und entscheidet nichts.

| Rang | Datei | Gilt für |
|---|---|---|
| 1 | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` | alles Offene. Wo `offen` steht, wird nichts gebaut und nichts erfunden |
| 2 | `REIHENFOLGE.md` | **nur den Zeitpunkt** — was jetzt gebaut wird, was wartet |
| 3 | `SARTU_TEXTREGELN.md` | **nur die Form** jedes Textes |
| 3a | `.claude/skills/sartu-texter/SKILL.md` | den Wortlaut |
| 4 | `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` | Stack, Datenmodell, Sicherheit, 88 Testfälle |
| 5 | `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` | öffentliche Seiten |
| 7 | `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` | Geschäftsmodell — **die Preistabelle ist die Quelle jeder Zahl** |

Sechs Dateien tragen „FINAL" im Namen; das sagt nichts über ihr Alter. Widersprechen sich zwei
Stellen **im selben** Dokument, gilt die mit der Begründung — steht bei keiner eine, **melden**,
nicht auswählen.

`archiv/konzepte/` (~360 KB) und `archiv/CLAUDE_MARKTANALYSE_KRITIK_OPTIMIERUNG.md` sind historisch: veraltete
Preise, abgelöste Stacks. **Nie vorsorglich einlesen**, nur gezielt nachschlagen.
`design/_verworfen/` ist auch als Anregung tabu.

## Etappen — was jetzt gebaut wird

`REIHENFOLGE.md` schneidet nach dem Zeitpunkt, nicht nach dem Bauteil. **20 Tabellen insgesamt,
88 Testfälle** — wer das Lastenheft von vorn nach hinten abarbeitet, baut Monate an Automatik vor
dem ersten Kunden.

| Etappe | Tabellen | Testfälle | Inhalt |
|---|---|---|---|
| **A0** | `organizations` `users` `sessions` `audit_events` `operator_settings` `legal_texts` | 26 | Ersteinrichtung, Migrationen, Adminanmeldung mit TOTP, Betreiberdaten, Rechtstexte, Testmail, Mandantentrennung |
| A1 | `leads` `login_tokens` `projects` `offers` | 34 | Bedarfsscheck bis gesendetes Angebot, Löschlauf |
| A2 | `invoices` `tasks` `task_files` `approvals` `support_messages` | 21 | Annahme bis Produktionsstart, Überfälligkeitslauf |
| A3 | `feedback_rounds` `feedback_items` `domain_status` | 6 | Vorschau bis `live` |
| B | `business_hours` `business_hours_exceptions` | 1 | Öffnungszeiten selbst pflegen |
| C | — | 0 | Mollie-Automatik, Mahnwesen, Registrar, Auswertungen |

`schema_migrations` zählt nicht mit — es entsteht **vor** allen Fachtabellen.

Welcher Testfall in welcher Etappe entsteht, steht in `REIHENFOLGE.md` als **eine Zeile je Fall**.
Nicht schätzen, nachsehen. Die Zahl war viermal falsch, die Zuordnungstabelle ist die Quelle.

**Nicht bauen, auch nicht vorbereitend** (`CODEX_AUFTRAG_PORTAL.md` §5): Zahlungsdienst-Anbindung,
Domainautomatik, Mahnwesen, Dunkelmodus, mehrere Benutzer je Kunde, Dateiversionierung,
Anfragen aus **Kunden**websites, Pipeline-/Kanban-Ansichten, Bewertung, Nachfassketten.

## Architektur

Verbindlich nach Portal-Lastenheft §1.3 — die Struktur ist vorgegeben, nicht vorgeschlagen:

```
/app        bootstrap.php · helpers · data (PDO, eine Datei je Tabelle) · services · views
/public     EINZIGES über den Webserver erreichbares Verzeichnis
/portal /admin /api   Routen
/storage    Uploads, außerhalb von /public
/migrations nummerierte SQL-Dateien
/tests      PHPUnit
```

- **SQL ausschließlich in `/app/data`.** Kein SQL in Seiten, Diensten oder Ansichten
- **Fachlogik ausschließlich in `/app/services`**, nie in einer Ansichtsdatei
- Eine Seite = Layout + Partials + Komponenten. Kein Markup zwischen Seiten kopieren
- **Kunden- und Adminzugriff sind getrennte Zugriffsschichten** — siehe unten

**Verboten:** Vollframework (Laravel, Symfony), SPA-Framework, Node als Zielsystem, CMS als
Unterbau, Build-Pipeline fürs Frontend, externe CDNs, clientseitiges Routing. Frühere Fassungen
nannten Node/Fastify und Supabase — beides ist abgelöst.

**PHP 8.3+ · MySQL 8 / MariaDB 10.6+ · PDO mit vorbereiteten Anweisungen, ausnahmslos ·
Argon2id für Adminpasswörter · AES-256-GCM über `sodium_*` für verschlüsselte Felder ·
Composer sparsam.**

## Die Regeln, an denen es scheitert

### Mandantentrennung

Die Kundenschicht nimmt `organization_id` **ausschließlich aus der Sitzung** — nie aus einem
Request-Parameter, Formularfeld oder URL-Segment. Ein fehlender Sitzungswert ist ein **Fehler**,
nicht „alles anzeigen".

Admins haben bewusst **keine** `organization_id` (`CHECK` auf `users` erzwingt das). Die
Adminschicht ist deshalb eine **eigene, getrennte** Schicht. **Verboten** ist der gemeinsame
Codepfad, der den Filter bei Admins weglässt (`WHERE organization_id = ? OR ? IS TRUE`) — genau
daraus entsteht die typische Datenpanne.

Objektzugriff immer **doppelt** prüfen: existiert es **und** gehört es zur Sitzungsorganisation?
Sonst **404**, nicht 403 — 403 verrät die Existenz.

`tests/TenantIsolationTest.php` wird **nie** gelöscht und **nie** abgeschwächt, um grün zu werden.
Er durchläuft die **vollständige** Routenliste: kommt eine Route dazu, die er nicht kennt,
scheitert er. Das ist beabsichtigt.

### Migrationen — MySQL rollt Schemaänderungen nicht zurück

`CREATE TABLE`, `ALTER TABLE`, `DROP TABLE` und `CREATE INDEX` lösen ein **implizites Commit**
aus. Ein `ROLLBACK` nach einer gescheiterten Migration nimmt die vorher gelaufenen Tabellen
**nicht** zurück. Wer „alles in eine Transaktion" schreibt, beschreibt PostgreSQL.

Deshalb: **jede Migration verändert genau ein Schemaobjekt** · `schema_migrations` wird als Erstes
angelegt · Eintrag **unmittelbar nach jedem Erfolg**, nicht am Ende im Block · SHA-256 je Datei,
Abweichung = Abbruch · Wiederanlauf bei der ersten nicht eingetragenen Migration.

**Migrationen werden nie geändert, nur ergänzt.** Es gibt **kein** `down` und keinen
„Reparieren"-Knopf — er müsste raten. Vorwärts ist ein Befehl, rückwärts ist eine Sicherung.

### Zwei Wege in die Datenbank

| | Ersteinrichtung (§1.5) | Nachträgliche Migration (§1.5a) |
|---|---|---|
| Aufruf | `/admin/setup` im Browser | **nur Befehlszeile**, kein Webaufruf, kein `/api/`-Endpunkt |
| Voraussetzung | Datenbank **leer** | Datenbank **nicht leer**, `schema_migrations` lückenlos |
| Danach | dauerhaft `404` | bleibt offen |

Die Ersteinrichtung hat **acht** Schritte (der Fließtext im Lastenheft sagt an einer Stelle noch
„sechs" — der Korrekturblock daneben begründet die acht und gewinnt). Reihenfolge zählt:
Schlüssel entstehen in Schritt 3, **bevor** in Schritt 7 das TOTP-Geheimnis verschlüsselt wird.

Die Installationssperre liegt an **zwei** Orten: `operator_settings.setup_completed_at` **und**
`/storage/installed.lock`. **Einer von beiden genügt für 404** — sonst hebt ein gelöschtes
Lockfile die Sperre auf.

**HTTP-Ausnahme für das Setup, nur wenn alle drei zutreffen:** `APP_ENV=local` aus der
*Serverumgebung* · `REMOTE_ADDR` ist `127.0.0.1`/`::1` (direkt, ohne Weiterleitungs-Kopfzeilen) ·
Hostname ist vollständig `localhost`/`127.0.0.1`/`[::1]`. `X-Forwarded-Proto` wird **ignoriert**.
Fehlt `APP_ENV`, gilt produktiv.

### Datenbank-Konventionen

- `id CHAR(36) ascii_bin`, in **PHP** erzeugt, nicht in der Datenbank
- `created_at`/`updated_at` als `DATETIME`, **immer UTC**. Nach jedem Verbindungsaufbau
  `SET time_zone = '+00:00'` — ohne diese Zeile landen Vorgabewerte in der Serverzeit
- Anzeige in **Europe/Berlin**, umgerechnet in PHP; Format `TT.MM.JJJJ, HH:MM Uhr`, nie ISO
- Fremdschlüssel `ON DELETE RESTRICT`. **Keine harte Löschung** fachlicher Daten — `archived_at`
- Geld als **integer in Cent**; Anzeige `7.900,00 €`. USt 19 % als Konstante an einer Stelle
- `NOT NULL` erlaubt `''`. Für jedes Pflichtfeld zusätzlich serverseitig: nach `trim()`
  mindestens ein Zeichen. `CHECK (x IS NOT NULL OR y IS NOT NULL)` ist wirkungslos
- Leere Werte in der Oberfläche: `Noch nicht hinterlegt`, nie `null`, `–` oder `undefined`

### Weiteres, das nicht verhandelbar ist

- **CSRF-Token bei jedem `POST`.** Ohne Ausnahme. Jede Aktion ist ein normales Formular —
  alle Kernabläufe funktionieren mit **deaktiviertem JavaScript**
- **Zahlungsstatus wird nie aus einer Rückkehr-URL abgeleitet.** Gilt schon vor A2
- **Audit** bei Anmeldung, fehlgeschlagener Anmeldung, Status- und Zahlungswechsel, Rechte-
  änderung, Löschung. Einträge werden nie geändert und nie gelöscht. Bei Geld und Fristen ist
  `reason` **Pflichtfeld**
- **Admin-2FA ist auch lokal nicht abschaltbar** — im Entwicklungsmodus mit festem Testschlüssel,
  nicht deaktiviert
- Sicherheitsheader in **jeder** Antwort: CSP ohne `unsafe-inline` für Skripte, `nosniff`,
  `strict-origin-when-cross-origin`, `X-Frame-Options: DENY`, HSTS in Produktion
- Keine Stacktraces nach außen: interne Kennung anzeigen, Details ins Log
- Der Kunde sieht **nie** einen Systemcode (`qa_failed`), immer Klartext

## Gestaltung — entschieden, nichts auszuwählen

`design/tokens.css` wird **als Erstes** eingebunden, vor jedem Bauteil-CSS.

- **Keine Zahl im Bauteil, wo eine Variable existiert.** `border-radius:30px` ist ein Abgabefehler
- Radienskala `--r-xs` bis `--r-pill`, skaliert über `--rk`. **Keine achte Form daneben**
- **Eine** Akzentfarbe: Lime `--lime` `#a3e635`. Kein Rot für Fehler, kein Grün für Erfolg
- **Lime ist Fläche.** Auf hellem Grund nie Schriftfarbe (1,32 : 1). Auf Lime steht immer `--ink`.
  Jede Lime-Fläche auf hellem Grund braucht `1px --line` als Kante
- Kunden- und Adminbereich müssen visuell unterscheidbar sein
- Kein Dunkelmodus (steht auf der Nicht-bauen-Liste)

## Texte

**Für jeden Text, den ein Mensch liest** — auch Fehlermeldungen und Knopfbeschriftungen — gilt
`.claude/skills/sartu-texter/` (Skill `sartu-texter`) zusammen mit `SARTU_TEXTREGELN.md`. Zu jeder
abgegebenen Seite gehört der **Prüfbericht mit Zahlen**.

Nach außen: *Kundenbereich · Ihr Bereich · Anmeldung · Ihr Projekt*.
Nach außen **nie**: *App · Software · SaaS · Plattform · Tool · Dashboard · System · Instanz*.
Intern darf „Adminbereich" stehen.

## Wann angehalten wird

- Eine Vorgabe widerspricht einer anderen
- Eine Zahl fehlt
- In `SARTU_ENTSCHEIDUNGEN_OFFEN.md` steht `offen`
- Eine gebrauchte Tabelle oder ein Feld fehlt im Datenmodell

**Nie raten.** Es gibt 88 Testfälle und eine Rangfolge, damit niemand raten muss.

**Nichts erfinden:** keine Rechtstexte, Anschriften, Kundennamen, Referenzen, keine Zahl, die in
den Unterlagen fehlt. Vorläufige Betreiberdaten aus der Ersteinrichtung sind ausdrücklich erlaubt
und im Adminbereich änderbar — die Startsperre (§1.4a) verhindert getrennt davon, dass mit
Platzhaltern nach außen gegangen wird.

**Was nicht ausgeführt wurde, wird nicht als geprüft gemeldet.** Aufgeschobene Prüfungen kommen
mit je einer Zeile nach `OFFENE_PRUEFUNGEN.md`: was gebaut wurde, was daran ungeprüft ist, womit
es geprüft wird.

## Pflichtdateien beim Bauen

| Datei | Wann |
|---|---|
| `IMPLEMENTATION_PLAN.md` | **vor der ersten Zeile Produktionscode** — Bestand, Prototypen je mit Begründung, Zielstruktur, Modulgrenzen, Datenmodellquelle, Reihenfolge, Risiken, Testplan, offene Entscheidungen |
| `OFFENE_PRUEFUNGEN.md` | sobald etwas gebaut, aber nicht ausgeführt wurde |
| `IMPLEMENTATION_SUMMARY.md` | am Ende — gebaute Struktur, Abweichungen mit Begründung, offene Punkte |
| `MIGRATION_NOTES.md` | nur falls aus einem Prototyp etwas übernommen wurde |
