# Testfälle

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus: `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` §16,
> `REIHENFOLGE.md`
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

---

## Verteilung auf die Stufen

**88 Testfälle insgesamt.** Welcher Fall in welcher Stufe entsteht, steht in `REIHENFOLGE.md`
als **eine Zeile je Fall**.

| Stufe | Fälle |
|---|---:|
| A0 | 26 |
| A1 | 34 |
| A2 | 21 |
| A3 | 6 |
| B | 1 |
| C | 0 |

> **Nicht schätzen, nachsehen.** Die Zahl war viermal falsch; die Zuordnungstabelle in
> `REIHENFOLGE.md` ist die Quelle und wird hier **nicht** dupliziert.

---

## Mandantentrennung — `tests/TenantIsolationTest.php`, unantastbar

1. Kunde A ruft Projekt von Kunde B auf → **404**
2. Kunde A ruft Rechnung, Aufgabe, Datei, Angebot von B auf → jeweils **404**
3. Kunde A sendet `POST` mit fremder `project_id` → **404**, keine Änderung
4. Kunde A lädt Datei von B über direkte URL → **404**
5. Liste enthält ausschließlich eigene Datensätze
5a. Der Test durchläuft die **vollständige Routenliste** des Kundenbereichs, nicht eine Auswahl.
    **Kommt eine Route hinzu, ohne dass der Test sie kennt, scheitert er** — das ist beabsichtigt
5b. Eine Kundenabfrage **ohne** Sitzungsorganisation wirft einen Fehler und liefert **nicht**
    alle Datensätze

**Der Test wird nie gelöscht und nie abgeschwächt, um grün zu werden.**

## Anmeldung

6. Token funktioniert **genau einmal**
7. Token nach der in `14_SICHERHEIT.md` festgelegten Frist ungültig
8. Token einer anderen E-Mail funktioniert nicht
9. Rate-Limit greift ab dem Versuch nach der Grenze aus `14_SICHERHEIT.md`
10. Bestätigungsseite ist **identisch** für vorhandene und nicht vorhandene Adressen

## Weitere Gruppen

Fachlogik, Statusübergänge, Zahlungen, Uploads, Audit und Ersteinrichtung stehen in
Portal-Lastenheft §16 und sind von dort zu lesen, bis sie hier eingearbeitet sind.

---

## Ausführung

| Zweck | Befehl |
|---|---|
| Alle Tests | `docker compose exec app vendor/bin/phpunit` |
| **Ein** Test | `docker compose exec app vendor/bin/phpunit --filter testAdminHatKeineOrganisation` |
| Eine Testdatei | `docker compose exec app vendor/bin/phpunit tests/TenantIsolationTest.php` |

**Tests laufen gegen `db_test`/`sartu_test`.** Sie leeren Tabellen.
**Tests gegen SQLite sind ausdrücklich verboten** — SQLite verhält sich bei Transaktionen und
Fremdschlüsseln anders.
