# Testfälle

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` §16,
> vervollständigt am 05.08.2026 um alle Gruppen.
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

**Die Nummerierung ist verbindlich und wird nie neu vergeben.** `REIHENFOLGE.md` verweist je
Stufe auf diese Nummern. Wer umnummeriert, zerreißt die Zuordnung von 88 Zeilen.

> **Die Zahlen in dieser Liste sind Zitate, keine Quellen.** Ein Testfall ohne seinen
> Schwellenwert ist nicht lesbar — deshalb steht er hier. Geändert wird er in der Datei, die
> über der Gruppe genannt ist. **Weicht eine Zahl hier von dort ab, gilt dort.**

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

---

## Anmeldung

Vorgaben: `14_SICHERHEIT.md`

6. Token funktioniert **genau einmal**
7. Token nach **15 Minuten** ungültig
8. Token einer anderen E-Mail funktioniert nicht
9. Rate-Limit greift ab dem **6.** Versuch je E-Mail und Stunde
10. Bestätigungsseite ist **identisch** für vorhandene und nicht vorhandene Adressen

---

## Fachlogik

Vorgaben: `11_KUNDENBEREICH.md`

11. Angebotsannahme **ohne alle vier Ankreuzfelder** scheitert
12. Angenommenes Angebot lässt sich **nicht erneut** annehmen
13. Abgelaufenes Angebot lässt sich nicht annehmen
14. **Rechnungsstatus wechselt nicht durch Aufruf einer Rückkehr-URL**
15. `ueberfaellig` wird korrekt gesetzt, wenn `due_date` überschritten ist
16. Aufgabe mit Pflichtantwort lässt sich nicht ohne Antwort abschließen
17. Upload ohne Rechtebestätigung wird abgelehnt
18. Abnahme erzeugt Eintrag in `approvals` (`kind = abnahme`) **und** Audit-Ereignis
19. Öffnungszeiten mit `Bis` vor `Von` werden abgelehnt
20. Statuswechsel erzeugt Audit-Ereignis **mit Akteur**

---

## Rechenregeln und Umfangsschutz

Zahlen: `02_PREISE_UND_ZAHLUNG.md` · Felder: `13_DATENMODELL.md`

21. Angebot mit falschem `first_year_net_cents` wird **nicht** gespeichert
22. `payment_plan = custom` wird bei `package ≠ sonderprojekt` abgelehnt
23. Bei `custom` muss die **Summe der Raten dem Einmalpreis entsprechen**, sonst Ablehnung
24. Angebotsannahme überträgt `included_feedback_rounds` und `protection_level` ins Projekt
25. Eine **zweite** Korrekturrunde bei Paket **Start** wird als `included = false` angelegt und im
    Kundenbereich entsprechend gekennzeichnet
26. Die Freigabeaufgabe lässt sich **nicht** abschließen, solange Pflichtaufgaben offen sind
27. Freigabe erzeugt `approvals` mit `kind = inhalte` **und** setzt den Startzeitpunkt des
    Lieferkorridors
28. `protection_started_on` wird beim Wechsel auf `live` gesetzt, `protection_min_term_until`
    liegt **12 Monate** später

---

## Anfrageeingang

Vorgaben: `09_ANFRAGEEINGANG.md`

29. Abgeschickter Bedarfsscheck erzeugt **nur** einen `lead` — keine `organizations`, `users`
    oder `projects`
30. `POST /briefing/absenden` **ohne CSRF-Feld** wird abgelehnt
31. Rate-Limit greift ab dem **11.** abgeschickten Bedarfsscheck je IP und Stunde
32. Ausgefülltes Honigtopffeld führt zur Danke-Seite und erzeugt **keinen** Datensatz
33. Absenden **unter 3 Sekunden** nach `form_started_at` führt zur Danke-Seite und erzeugt
    **keinen** Datensatz
34. **Dieselbe `submission_id` zweimal** → weiterhin genau **ein** Datensatz, trotzdem Danke-Seite
35. `b2b_confirmed = false` oder `privacy_confirmed = false` → Schritt erneut anzeigen, kein
    Datensatz
36. Formulardaten über **64 KB** werden abgewiesen
37. **Keine** Fehlermeldung nennt Feldwerte, interne Kennungen oder Datenbankmeldungen
38. Unbekanntes Zusatzfeld wird in `payload` gespeichert und **nicht** abgewiesen
39. **Empfehlung und Ampelkennzeichen werden serverseitig gesetzt** — ein manipuliertes
    Formularfeld ändert sie nicht
40. `source_ip` ist nach **30 Tagen** geleert, der übrige Datensatz unverändert;
    `Endgültig löschen` entfernt den Datensatz und hinterlässt ein Audit-Ereignis **ohne** die
    gelöschten Inhalte
40a. Herkunftsfelder werden beim **ersten** Seitenaufruf in die Sitzung geschrieben und landen
     auch dann im `lead`, wenn der Bedarfsscheck erst Schritte später abgeschickt wird
40b. `referrer_host` enthält **nur** den Hostnamen, `landing_page` **nur** den Pfad — keine
     vollständigen Adressen mit Abfragezeichenfolge

---

## Sicherheit

Vorgaben: `14_SICHERHEIT.md`

41. `POST` **ohne CSRF-Token** wird abgelehnt
42. Kunde erreicht **keine** `/admin`-Route — geprüft über die **vollständige** Adminroutenliste
43. Abgemeldeter Benutzer erreicht keine `/admin`-Route
44. Admin **ohne bestätigtes TOTP** erreicht keine Adminroute
45. Die Kundenauswahl im Adminbereich verändert die Sitzungsorganisation **nicht**
46. Unerlaubter Dateityp wird abgelehnt
47. Sicherheitsheader sind in **allen** Antworten gesetzt
48. Datenbankbedingung greift: Kunde **ohne** `organization_id` und Admin **mit**
    `organization_id` lassen sich nicht anlegen
49. **Kein Verzeichnis außer `/public` ist über den Webserver erreichbar** — `/app`, `/storage`,
    `/migrations` und `.env` liefern 403 oder 404
50. Jede Datenbankabfrage nutzt **vorbereitete Anweisungen** — im Code nachgewiesen, keine
    zusammengesetzten SQL-Zeichenketten

---

## Protokollierung

51. Manuelles Setzen auf `bezahlt` **ohne Grundlagentext** scheitert
52. Das Audit-Ereignis dazu enthält **Akteur, Zeitpunkt, alten Wert, neuen Wert, Grundlagentext
    und IP**
53a. Änderung von `due_date` erzeugt ein Audit-Ereignis mit Grundlagentext
53b. Änderung von `protection_started_on` erzeugt ein Audit-Ereignis mit Grundlagentext
54. Rücknahme von `bezahlt` ist eine **eigene protokollierte Aktion** und benachrichtigt den Kunden
55. Ein Audit-Eintrag lässt sich **weder ändern noch löschen**

---

## Bedienung

56. **Alle Kernabläufe funktionieren mit deaktiviertem JavaScript**
57. Willkommensstrecke erscheint **einmal** und danach nicht mehr
58. Jede Seite hat **genau eine** `<h1>`
59. **Kein Systemcode erscheint in einer Kundenansicht** — geprüft per Volltextsuche über die
    gerenderten Seiten gegen die Statusliste aus `11_KUNDENBEREICH.md`

---

## Statusübergänge

60. Ein Paar, das **nicht** in der Übergangstabelle steht, wird abgewiesen — geprüft an
    `zahlung_offen → produktion`. **Kein Statuswechsel, kein Teileffekt**
61. `briefing → produktion` scheitert, solange kein `approvals`-Eintrag mit `kind = inhalte`
    existiert
62. Fortsetzen aus `pausiert` führt auf `paused_from_status` zurück — **ein im Formular
    mitgesendeter Zielstatus wird ignoriert**
63. `live → korrektur` wird abgewiesen

---

## Betreiberdaten

Vorgaben: `12_ADMINBEREICH.md` · Felder: `13_DATENMODELL.md`

64. Eine **zweite** Zeile in `operator_settings` lässt sich nicht anlegen — weder mit anderem
    `singleton`-Wert noch mit anderem Schlüssel
65. `ust_id = ''` **und** `steuernummer = ''` wird abgewiesen
    > **Leer ist nicht gesetzt.** Die Prüfbedingung darf nicht nur auf `NULL` prüfen — `NOT NULL`
    > erlaubt `''`.
66. **Startsperre greift:** Bei leerem Pflichtfeld oder Rechtstext im Zustand `entwurf` bricht
    die produktive Veröffentlichung ab

---

## Ersteinrichtung

Vorgaben: `14_SICHERHEIT.md`

67. Einrichtung gegen eine **nicht leere** Datenbank bricht **vor der ersten Migration** ab
68. Eine nachträglich geänderte Migrationsdatei löst beim Start einen **Prüfsummenabbruch** aus,
    **mit Nennung der Datei**
69. Nach einem Abbruch mitten in Schritt 4 setzt der erneute Aufruf bei der **ersten nicht
    eingetragenen** Migration fort und wiederholt **keine** bereits eingetragene
70. `/admin/setup` über HTTP mit `APP_ENV=production` von `127.0.0.1` bricht ab
71. `/admin/setup` über HTTP mit `APP_ENV=local` von einer **nicht** loopback-Adresse bricht ab
72. `X-Forwarded-Proto: https` bei tatsächlichem HTTP **wird ignoriert**, solange keine
    vertrauenswürdige Zwischenstelle konfiguriert ist
73. Nach Abschluss liefert `/admin/setup` **`404`, auch nach Löschen einer der beiden Sperren**

---

## Nachträgliche Migration

74. `php bin/migrate.php status` auf einer **nicht leeren** Datenbank listet offene Migrationen
    und **verändert nichts**
75. `up` **ohne angegebene Sicherungsdatei** bricht ab — ebenso bei angegebener, aber fehlender
    oder leerer Datei
76. Während `up` liefern Kunden- und Adminbereich **`503`**; nach Erfolg ist der Wartungsmodus
    aufgehoben, **nach Abbruch bleibt er bestehen**

---

## Geld, Fristen und Zugang

*Ergänzt nach dem Audit vom 31.07.2026.*

77. `paid_cents` zwischen 0 und `gross_cents` ergibt Status `teilweise_bezahlt` — **nicht**
    `bezahlt`, auch nicht bei **einem Cent** Differenz
78. Eine überfällige Rechnung löst **genau eine** Erinnerung aus; ein zweiter Lauf am selben oder
    am Folgetag verschickt **keine weitere**
79. Ein Upload, der die Organisationsgrenze von **500 MB** überschreitet, wird abgelehnt — **auch
    wenn die Einzeldatei unter 20 MB liegt**
80. Eine nicht umgewandelte Anfrage älter als **12 Monate** wird vom täglichen Lauf gelöscht; das
    Audit-Ereignis enthält **keine** der gelöschten Inhalte
81. `legal_texts` mit `slug = avv` im Zustand `entwurf` **blockiert die produktive
    Veröffentlichung**
82. `legal_texts` mit `audience = kunde` ist **öffentlich nicht abrufbar** und angemeldet sichtbar
83. `/login` zeigt die Telefonnummer aus `operator_settings`. Ist dort keine gesetzt, erscheint
    die E-Mail-Adresse — **nie ein Wert aus dem Quelltext**

---

## Zur Anzahl

**83 durchnummerierte plus fünf mit Buchstabenzusatz** — 5a, 5b, 40a, 40b sowie 53a/53b als
Teilung von 53. Zusammen **88**.

> Frühere Fassungen sprachen von „59"; das war schon damals um vier Fälle zu niedrig.
> **Maßgeblich ist die Liste, nicht die Zahl.**

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
