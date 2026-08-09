# Startprompt: A2 schließen, Stufe C bauen

**Stand:** 09.08.2026 · Repo-Stand `6811f8a` auf `claude/sartu-concept-review-pdhb5t`
**Zweck:** Alles unterhalb der Trennlinie in eine neue Claude-Code-Session kopieren.
**Umfang:** die vier offenen A2-Fälle, die zwölf neuen C-Fälle, der Menüpunkt „Ersteinrichtung".
Danach ist das Projekt bis auf die Entscheidungen des Betreibers vollständig.

---

/goal Baue SARTU fertig: die vier offenen Testfälle der Stufe A2, die zwölf Fälle der Stufe C
(Belegerzeugung, E-Rechnung nach EN 16931, Nummernkreise, Storno, Mollie-Abgleich, Aufbewahrung,
Steuerberater-Export) und den Menüpunkt „Ersteinrichtung" im Adminbereich. Am Ende laufen alle
Tests grün, `bin/migrate.php verify` ist sauber, und `IMPLEMENTATION_SUMMARY.md` beschreibt, was
entstanden ist.

## Zuerst lesen — in dieser Reihenfolge, sonst baust du zweimal

1. `CLAUDE.md` — Architektur, Rangfolge, die Regeln, an denen es scheitert
2. `SARTU_ENTSCHEIDUNGEN_OFFEN.md` **§4a** — die vier Entscheidungen vom 09.08.2026, die diesen
   Auftrag überhaupt erst freigeben. **Rang 1.** Ohne §4a stünde die halbe Arbeit auf der
   Nicht-bauen-Liste
3. `spezifikation/18_BELEGE_UND_ZAHLUNG.md` — **die Bauvorlage.** Ein Thema, eine Datei
4. `spezifikation/13_DATENMODELL.md` — die drei neuen Tabellen und die vier neuen Felder
5. `spezifikation/15_TESTFAELLE.md` — die Fälle 84 bis 95 im Wortlaut, dazu 51, 52, 53a, 54
6. `REIHENFOLGE.md`, Abschnitt „Stufe C" — was freigegeben ist und was ausdrücklich nicht

**Nicht vorsorglich einlesen:** die vier Lastenhefte, `archiv/`, `design/_verworfen/`. Sie sind
Begründungsarchiv. Bei Widerspruch gewinnt `spezifikation/`.

## Der Stand, damit du ihn nicht neu herleitest

- **277 Tests grün**, 244 PHP-Dateien ohne Syntaxfehler, 26 Migrationen, alle 20 bisherigen
  Fachtabellen liegen
- **A0, A1, A3, B sind vollständig belegt.** 84 der 100 Testfälle sind zugeordnet
- `design/tokens.css` führt seit dem 09.08.2026 die entschiedene Palette und stimmt mit
  `design/startseite.html` und `design/portalkonzept.html` überein. **Daran ist nichts zu tun**
- Das SARTU-Logo liegt als SVG unter `design/` — `sartu-logo-hell.svg`, `sartu-logo-dunkel.svg`,
  `sartu-mark.svg`. **Es liegt noch nicht unter `public/`**

## Reihenfolge der Arbeit

### Block 1 — A2 schließen (Fälle 51, 52, 53a, 54)

`app/services/Rechnungsdienst.php` **beschreibt im Klassenkommentar zwei Funktionen, die es nicht
gibt.** Bau sie, statt den Kommentar zu kürzen:

- **Zahlung zurücknehmen** — eigene protokollierte Handlung, eigener Grundlagentext,
  benachrichtigt den Kunden. Kein stiller Statuswechsel
- **`due_date` ändern** — protokollierte Handlung mit Grundlagentext, ohne neuen Beleg

Dazu die Routen, die Formulare im Adminbereich und die vier Testfälle. Fall 52 prüft, dass das
Audit-Ereignis **Akteur, Zeitpunkt, alten Wert, neuen Wert und Grundlagentext** trägt — alle fünf.

**Fertig, wenn:** die vier Fälle grün sind und `Rechnungsdienst.php` nichts mehr beschreibt, was
es nicht gibt.

### Block 2 — Migrationen für Stufe C

Drei Tabellen, vier Felder, **eine Migration je Schemaobjekt**. Nummern ab `027`.

| Migration | Objekt |
|---|---|
| `027` | `number_sequences` |
| `028` | `documents` |
| `029` | `payment_events` |
| `030`–`032` | `invoices`: `issued_at`, `cancels_invoice_id`, `payment_provider_id` |
| `033` | `operator_settings`: `mollie_key_test`, `mollie_key_live` |

**MySQL rollt Schemaänderungen nicht zurück.** Eintrag in `schema_migrations` unmittelbar nach
jedem Erfolg, nicht am Ende im Block. Kein `down`. Migrationen werden nie geändert, nur ergänzt.

### Block 3 — Nummernkreise (Fälle 84, 85)

Vergabe aus `number_sequences` **unter Zeilensperre, in derselben Transaktion wie der Beleg**.
`UNIQUE` auf der Belegnummer als Gegenprobe.

**Fall 85 prüft Nebenläufigkeit.** Ein Test, der zwei Rechnungen nacheinander anlegt, prüft ihn
nicht — er braucht zwei parallele Verbindungen. Wenn du keinen belastbaren Weg findest, das im
Testrahmen echt nebenläufig zu fahren: **bau es trotzdem korrekt, schreib den Test so nah wie
möglich heran, und trag die Lücke mit einer Zeile in `OFFENE_PRUEFUNGEN.md` ein.** Nicht als grün
melden, was nicht gelaufen ist.

### Block 4 — Belegerzeugung (Fälle 87, 88, 89, 94)

Zwei Composer-Pakete sind freigegeben und **nur diese zwei**: ein Erzeuger für ZUGFeRD/XRechnung
nach EN 16931 und ein HTML-nach-PDF-Renderer. Prüf beide auf Lizenz, Wartungsstand und
PHP-8.3-Eignung, **bevor** du sie einträgst, und begründe die Wahl in `IMPLEMENTATION_PLAN.md`.
Eine dritte Abhängigkeit braucht eine eigene Entscheidung des Betreibers — dann anhalten.

- Vorlage für Angebot und Rechnung, gestaltet über `design/tokens.css`, mit dem Logo
- Logo nach `public/assets/` übernehmen. `tests/SecurityHeadersTest` bindet die Auslieferung an
  die Quelle — **halte beide identisch**, wie bei `tokens.css`
- Rechnung als **PDF/A-3 mit eingebettetem XML**, Profil `EN16931`
- **Prüfung gegen Schema und Schematron vor Versand und Ablage.** Fällt sie durch, bricht der
  Vorgang ab und nennt die verletzte Regel
- Ablage unter `/storage` mit Prüfsumme in `documents`

**Die Pflichtangaben nach § 14 UStG prüfst du nicht von Hand.** Das Profil erzwingt sie; die
Validierung ist die Prüfung.

### Block 5 — Storno und Aufhebung (Fall 86)

Drei verschiedene Vorgänge, siehe `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 5. Der wichtige:
**eine versendete Rechnung wird nicht verworfen, sondern durch eine Stornorechnung mit eigener
Nummer aufgehoben.** Beide Belege bleiben abrufbar.

### Block 6 — Mollie (Fälle 90, 91, 92, 93)

**`/api/` gibt es noch nicht.** Das wird der erste Endpunkt des Projekts. Leg den Bereich nach
demselben Muster an wie `/portal` und `/admin` — eigene Zugriffsschicht, kein gemeinsamer
Codepfad.

Der Ablauf steht in `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6. Die drei Stellen, an denen es
schiefgeht:

1. **Der Webhook ist ein Klingelzeichen, keine Aussage.** Er liefert eine Kennung; den Status holt
   der Server selbst mit seinem eigenen Schlüssel. **Nie aus einer Rückkehr-URL ableiten**
2. **Idempotenz vor Verarbeitung.** Kennung in `payment_events` festhalten, *bevor* gearbeitet
   wird. Ein zweites Eintreffen antwortet ohne Fehler und ohne Wirkung
3. **Der Schlüssel ist ein Geheimnis.** Verschlüsselt über `app/services/Verschluesselung.php`,
   wie das TOTP-Geheimnis. Nie in einer Ansicht, Fehlermeldung oder Protokollzeile

**Der Webhook braucht eine öffentlich erreichbare Adresse.** Lokal ist er damit nicht
durchgängig prüfbar. Prüf ihn gegen einen nachgebildeten Aufruf und trag die verbleibende Lücke
in `OFFENE_PRUEFUNGEN.md` ein.

### Block 7 — Abruf, Versand, Export (Fall 95)

- Belegliste für Kunde und Admin. **Doppelte Prüfung: existiert er, und gehört er zur
  Sitzungsorganisation? Sonst 404, nicht 403**
- Versand per Mail als **Handlung des Betreibers**, ein Knopf je Beleg, protokolliert
- Export für den Steuerberater: jede Rechnung, jede Stornorechnung, jeder Zahlungseingang des
  Zeitraums **genau einmal**, dazu die Belegdateien. **Der Export bucht nicht**

### Block 8 — Menüpunkt „Ersteinrichtung"

Der Betreiber will einen Punkt im Adminbereich, der zeigt, was noch fehlt, **und verschwindet,
wenn nichts mehr fehlt.**

Die Prüflogik existiert bereits: `app/services/Startsperre.php`, Methode `hindernisse()`, heute
sichtbar unter `/admin/einstellungen/betrieb`. Bau daraus eine eigene Seite mit Fortschritt und
blende den Menüpunkt aus, sobald `starterlaubt()` wahr ist.

**Nimm den Mollie-Schlüssel und das Logo mit in diese Liste.** Die Ersteinrichtung unter
`/admin/setup` hat spezifikationsfest **acht** Schritte und bekommt keinen neunten.

### Block 9 — Verfahrensdokumentation

Ein Dokument, kein Programm: `VERFAHRENSDOKUMENTATION.md`. Belegfluss vom Entstehen bis zur
Ablage · beteiligte Systeme · Berechtigungen · Sicherung und Wiederherstellung ·
Änderungsprotokoll. Umfang und Zweck in `18_BELEGE_UND_ZAHLUNG.md` Abschnitt 7.

## Die Regeln, an denen es scheitert

- **SQL ausschließlich in `/app/data`**, Fachlogik ausschließlich in `/app/services`. Nie in einer
  Ansicht. `tests/PreparedStatementsTest.php` prüft das und wird nicht abgeschwächt
- **`organization_id` kommt ausschließlich aus der Sitzung.** Kein gemeinsamer Codepfad, der den
  Filter für Admins weglässt
- **`tests/TenantIsolationTest.php` durchläuft die vollständige Routenliste.** Jede neue Route —
  auch jede `/api/`-Route — muss dort bekannt sein, sonst schlägt er an. **Das ist beabsichtigt:
  trag sie ein, schwäche den Test nicht ab**
- **CSRF-Token bei jedem `POST`.** Alle Kernabläufe funktionieren mit deaktiviertem JavaScript.
  **Ausnahme mit Begründung:** der Webhook ist kein Formular eines Menschen; er wird über den
  serverseitigen Statusabruf abgesichert, nicht über ein Token
- **Audit bei jedem Status- und Zahlungswechsel.** Bei Geld und Fristen ist `reason` Pflichtfeld
- **Keine Zahl im Bauteil, wo eine Variable existiert.** `design/tokens.css` zuerst einbinden
- **Geld als Integer in Cent.** Anzeige `7.900,00 €`. Zeiten in UTC speichern, in Europe/Berlin
  anzeigen, Format `TT.MM.JJJJ, HH:MM Uhr`
- **Der Kunde sieht nie einen Systemcode**, immer Klartext
- **Texte:** `.claude/skills/sartu-texter/` mit `SARTU_TEXTREGELN.md`. Gebunden sind nur noch vier
  Gruppen — jede Zahl, vertragliche Erklärungen, Rechtstexte und Pflichthinweise, Status- und
  Feldnamen. Beschriftungen formulierst du frei, aber **innerhalb einer Fassung identisch**

## Nicht bauen, auch nicht vorbereitend

Mahnwesen und Mahnstufen · wiederkehrende Lastschriften und Mandate · Registraranbindung ·
Finanzübersichten und Auswertungen · doppelte Buchführung · Kontenrahmen ·
Umsatzsteuer-Voranmeldung über ELSTER · Bilanz und Jahresabschluss · Fremdwährungen · Skonto ·
Dunkelmodus · mehrere Benutzer je Kunde · Dateiversionierung.

**Kleinbetragsrechnungen unter 250 € werden nicht programmiert** — SARTU stellt keine Beträge in
dieser Größe.

## Wann du anhältst

- Eine Vorgabe widerspricht einer anderen
- Eine Zahl fehlt
- In `SARTU_ENTSCHEIDUNGEN_OFFEN.md` steht `offen`
- Ein drittes Composer-Paket wäre nötig

**Nie raten.** Es gibt 100 Testfälle und eine Rangfolge, damit niemand raten muss. Melden statt
auflösen.

## Was du ablieferst

| Datei | Wann |
|---|---|
| `IMPLEMENTATION_PLAN.md` | **vor der ersten Zeile Produktionscode** — fortschreiben, nicht ersetzen. Darin die Begründung der zwei Composer-Pakete |
| `OFFENE_PRUEFUNGEN.md` | sobald etwas gebaut, aber nicht ausgeführt wurde. Je eine Zeile: was gebaut, was ungeprüft, womit es zu prüfen ist |
| `IMPLEMENTATION_SUMMARY.md` | am Ende — gebaute Struktur, Abweichungen mit Begründung, offene Punkte |
| `VERFAHRENSDOKUMENTATION.md` | Block 9 |

**Fertig ist der Auftrag, wenn** alle 100 Testfälle zugeordnet sind, `vendor/bin/phpunit` grün
läuft, `bin/migrate.php verify` keine Abweichung meldet und der Menüpunkt „Ersteinrichtung" bei
vollständigen Daten verschwindet.

Arbeite auf dem Branch `claude/sartu-concept-review-pdhb5t`, committe in nachvollziehbaren
Schritten und pushe am Ende.
