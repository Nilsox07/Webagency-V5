# Sicherheit, Architektur und Ersteinrichtung

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus: `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` §1, §3,
> `CLAUDE.md`
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

---

## Architektur — verbindlich, nicht vorgeschlagen

**SARTU ist ein Projekt, nicht zwei.** Öffentliche Website, Kundenbereich `/portal/`,
Adminbereich `/admin/` und Serverfunktionen `/api/` liegen in **einem** modularen PHP-Projekt
unter **einer** Domain.

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
- Eine Seite = Layout + Partials + Komponenten. **Kein Markup zwischen Seiten kopieren**

**Verboten:** Vollframework (Laravel, Symfony) · SPA-Framework · Node als Zielsystem · CMS als
Unterbau · Build-Pipeline fürs Frontend · externe CDNs · clientseitiges Routing.

**PHP 8.3+ · MySQL 8 / MariaDB 10.6+ · PDO mit vorbereiteten Anweisungen, ausnahmslos ·
Argon2id für Adminpasswörter · AES-256-GCM über `sodium_*` für verschlüsselte Felder ·
Composer sparsam.**

---

## Die dreizehn eisernen Regeln

**1. Mandantentrennung ist heilig.** Jede Abfrage im Kundenbereich filtert nach
`organization_id` **aus der Sitzung** — niemals aus einem Request-Parameter, Formularfeld oder
URL-Segment. Ein fehlender Sitzungswert ist ein **Fehler**, nicht „alles anzeigen".

`tests/TenantIsolationTest.php` ist **unantastbar**: nie löschen, nie abschwächen, um grün zu
werden. Er durchläuft die **vollständige** Routenliste — kommt eine Route dazu, die er nicht
kennt, scheitert er. **Das ist beabsichtigt.**

**2. Objektzugriff immer doppelt prüfen.** Existiert das Objekt **und** gehört es zur
Sitzungsorganisation? Sonst **404**, nicht 403 — 403 verrät die Existenz.

**2a. Getrennte Datenzugriffswege für Kunde und Admin.** Admins haben bewusst **keine**
`organization_id` (`CHECK` auf `users` erzwingt das).

- Es gibt **zwei** getrennte Zugriffsschichten
- Die Adminschicht ist eine **eigene** Schicht. Nur sie darf organisationsübergreifend lesen
- Adminrouten unter `/admin/…` werden **vollständig durch eine einzige zentrale Vorprüfung**
  geschützt — nicht Route für Route. Fällt die Prüfung aus, ist die Route unerreichbar
- **Verboten:** ein gemeinsamer Codepfad, der bei Admins den Filter weglässt
  (`WHERE organization_id = ? OR ? IS TRUE`). **Genau daraus entsteht die typische Datenpanne**
- Wählt ein Admin einen Kunden aus, gilt das **nur** im Adminbereich. Es schreibt **niemals**
  die Sitzungsorganisation um

**3. CSRF-Token bei jedem `POST`.** Kein Token, keine Ausnahme. Jede Aktion ist ein normales
Formular — **alle Kernabläufe funktionieren mit deaktiviertem JavaScript**.

**4. Rate-Limit** auf Login-Anforderung: 5 pro E-Mail und Stunde, 20 pro IP und Stunde. Ebenso
auf Token-Einlösung.

**5. Magic-Link-Token:** kryptografisch zufällig (≥ 32 Byte), **nur als Hash gespeichert**,
gültig **15 Minuten**, **einmalig** verwendbar, an die E-Mail gebunden.

**6. Sessions:** `httpOnly`, `secure`, `SameSite=Lax`, serverseitig gespeichert, Verfall
30 Tage, bei Abmeldung serverseitig gelöscht.

**7. Upload-Pfade als UUID** — nie ratbar, nie vom Dateinamen abgeleitet. Uploads liegen
**außerhalb** des öffentlichen Verzeichnisses, Auslieferung nur über eine autorisierte Route.

**8. Keine Secrets im Repository.** Nur `.env.example` und Demo-Seeds.

**9. Audit-Log** bei: Angebotsannahme · Abnahme/Freigabe · Zahlungsstatuswechsel ·
Projektstatuswechsel · Rechteänderung · Löschung · Anmeldung · **fehlgeschlagener Anmeldung**.
Einträge werden **nie geändert und nie gelöscht**. Bei Geld und Fristen ist `reason` Pflichtfeld.

**10. Admin-2FA ist Pflicht**, auch lokal nicht abschaltbar — im Entwicklungsmodus mit festem
Testschlüssel, **nicht** deaktiviert.

**11. Sicherheitsheader in jeder Antwort:** CSP **ohne `unsafe-inline` für Skripte** ·
`X-Content-Type-Options: nosniff` · `Referrer-Policy: strict-origin-when-cross-origin` ·
`X-Frame-Options: DENY` · HSTS in Produktion.

**12. Fehlerausgabe:** **nie Stacktraces nach außen.** Interne Kennung anzeigen, Details ins Log.

**13. Keine harte Löschung** fachlicher Datensätze. Statt `DELETE`: `archived_at` setzen.

**Zusätzlich:** Der Zahlungsstatus wird **nie aus einer Rückkehr-URL abgeleitet.**

---

## Zwei Wege in die Datenbank

| | Ersteinrichtung | Nachträgliche Migration |
|---|---|---|
| Aufruf | `/admin/setup` im Browser | **nur Befehlszeile** — kein Webaufruf, kein `/api/`-Endpunkt |
| Voraussetzung | Datenbank **leer** | Datenbank **nicht leer**, `schema_migrations` lückenlos |
| Danach | dauerhaft **404** | bleibt offen |

**Die Ersteinrichtung hat acht Schritte.** Der Fließtext sagt an einer Stelle noch „sechs" — der
Korrekturblock daneben begründet die acht und gewinnt. **Reihenfolge zählt:** Schlüssel entstehen
in Schritt 3, **bevor** in Schritt 7 das TOTP-Geheimnis verschlüsselt wird.

**Die Installationssperre liegt an zwei Orten:** `operator_settings.setup_completed_at` **und**
`/storage/installed.lock`. **Einer von beiden genügt für 404** — sonst hebt ein gelöschtes
Lockfile die Sperre auf.

**HTTP-Ausnahme für das Setup, nur wenn alle drei zutreffen:**
`APP_ENV=local` aus der *Serverumgebung* · `REMOTE_ADDR` ist `127.0.0.1`/`::1` (direkt, ohne
Weiterleitungs-Kopfzeilen) · Hostname ist vollständig `localhost`/`127.0.0.1`/`[::1]`.
**`X-Forwarded-Proto` wird ignoriert.** Fehlt `APP_ENV`, gilt produktiv.

---

## Migrationen — MySQL rollt Schemaänderungen nicht zurück

`CREATE TABLE`, `ALTER TABLE`, `DROP TABLE` und `CREATE INDEX` lösen ein **implizites Commit**
aus. Ein `ROLLBACK` nach einer gescheiterten Migration nimmt die vorher gelaufenen Tabellen
**nicht** zurück. **Wer „alles in eine Transaktion" schreibt, beschreibt PostgreSQL.**

Deshalb:

- **Jede Migration verändert genau ein Schemaobjekt**
- `schema_migrations` wird als Erstes angelegt — es zählt nicht zu den 20 Fachtabellen
- Eintrag **unmittelbar nach jedem Erfolg**, nicht am Ende im Block
- **SHA-256 je Datei**, Abweichung = Abbruch
- Wiederanlauf bei der ersten nicht eingetragenen Migration

**Migrationen werden nie geändert, nur ergänzt.** Es gibt **kein** `down` und keinen
„Reparieren"-Knopf — er müsste raten. **Vorwärts ist ein Befehl, rückwärts ist eine Sicherung.**

---

## Zwei Datenbanken, absichtlich

`db`/`sartu` zum Arbeiten, `db_test`/`sartu_test` für die Tests. Die Tests leeren Tabellen.

**Tests gegen SQLite sind ausdrücklich verboten** — SQLite verhält sich bei Transaktionen und
Fremdschlüsseln anders.
