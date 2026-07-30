# Bau-Reihenfolge — was wann entsteht

**Stand:** 28.07.2026
**Zweck:** Verhindern, dass der Kundenbereich in voller Ausbaustufe entsteht, bevor der erste Kunde
existiert. Das Portal-Lastenheft beschreibt **18 Tabellen und 59 Testfälle**. Wer es von vorn nach
hinten abarbeitet, baut Monate an Automatik, bevor jemand sie benutzt.

> **Warnung aus dem eigenen Konzept**, `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` Zeile 12:
>
> *„Die größte Gefahr ist **nicht** die Preis- oder Angebotslogik, sondern der Anspruch, den
> kompletten Kundenbereich mit voller Automatisierung vollständig **vor dem ersten Standardverkauf**
> zu bauen."*

**Stand bei Anlage dieser Datei:** 7.454 Zeilen Spezifikation, **0 Zeilen Anwendung.**

---

## Der Schnitt liegt in der Zeit, nicht im Bauteil

Die naheliegende Aufteilung — „Kundenbereich voll, Adminbereich später" — funktioniert **nicht**.
Fast jede Kundenhandlung braucht eine Gegenstelle: Der Kunde sieht ein Angebot, also muss es jemand
erstellt haben. Er lädt Dateien hoch, also muss sie jemand herunterladen können. Ohne Gegenstelle
läuft der Kunde in eine Sackgasse.

Umgekehrt braucht der erste Kunde die Selbstpflege erst, **wenn seine Website live ist** — Wochen
nach Vertragsschluss.

**Deshalb: geschnitten wird nach dem Zeitpunkt, an dem etwas gebraucht wird.**

---

## Stufe A — bis der erste Kunde live ist

**In vier lieferbaren Etappen.** Jede endet an einem Punkt, an dem etwas durchgängig funktioniert
und vorführbar ist — nicht an einer Bauteilgrenze.

> **Korrigiert 28.07.2026 nach einer externen Prüfung.** Die erste Fassung schob `leads` und
> `invoices` auf Stufe B. Beides war falsch: Ohne `leads` gibt es keinen Weg zum ersten Kunden,
> ohne `invoices` keinen Weg von der Angebotsannahme zur Produktion. Der Statusfluss belegt es —
> `angebot_angenommen` → *„wartet auf erste Zahlung"* → `zahlung_offen` → `briefing`. **Verschoben
> wird die Zahlungs*automatik*, nicht die Rechnung.**

### A0 — Fundament

Ersteinrichtung (§1.5) · Migrationen · Adminanmeldung mit TOTP · Betreiberdaten (§1.4a) ·
Rechtstexte mit Freigabezustand · Testmailversand · **Mandantentrennung** · Prüfprotokoll.

**Tabellen:** `operator_settings` · `legal_texts` · `users` · `sessions` · `audit_events`
**Fertig, wenn:** Installation läuft auf leerer Datenbank durch, ein Admin kann sich anmelden, eine
Testmail kommt an, `TenantIsolationTest` läuft grün.

### A1 — Anfrage bis Auftrag

Bedarfsscheck-Eingang (§4b) · Anfrageliste im Adminbereich · Umwandlung in Kunde und Projekt ·
Einladung des Kunden · Angebot erstellen, senden, annehmen.

**Tabellen:** `leads` · `organizations` · `login_tokens` · `projects` · `offers`
**Fertig, wenn:** Eine echte Anfrage über die Website führt bis zu einem angenommenen Angebot.

### A2 — Auftrag bis Produktionsstart

Rechnungen **von Hand angelegt**, Zahlungsstatus **von Hand gesetzt**, Mollie-Link eingetragen ·
Aufgaben · Uploads · Inhaltsfreigabe.

**Tabellen:** `invoices` · `tasks` · `task_files`
**Fertig, wenn:** Nach Angebotsannahme führt der Weg über Anzahlung und Aufgaben bis `produktion`.

### A3 — Produktion bis Livegang

Vorschau · Korrekturrunden · Abnahme · minimaler Domainstatus · Livegang.

**Tabellen:** `feedback_rounds` · `feedback_items` · `approvals` · `domain_status` *(nur Statusfeld,
keine Registrar-Anbindung)*
**Fertig, wenn:** Ein Projekt erreicht `live`. **Ab hier existieren die Bildschirmansichten für die
Website.**

### Summe Stufe A

**15 Tabellen von 20.** Zurückgestellt: `business_hours` · `business_hours_exceptions` ·
`support_messages` — und die Automatik hinter `invoices` und `domain_status`.

## Stufe B — wenn der erste Kunde live ist

Ein paar Wochen nach Vertragsschluss, vor Kunde zwei und drei.

- Selbstpflege: Öffnungszeiten, Kontaktdaten, Bilder tauschen, Team- und Projekteinträge
- Anfragen von der Website in der Kundenansicht
- Nachrichten an den Betreuer
- Domainstatus mit Verlauf statt nur Statusfeld

**Tabellen:** `business_hours` · `business_hours_exceptions` · `support_messages`

**Erst hier verfügbar:** die Bildschirmansichten *Öffnungszeiten*, *Website-Anfragen* und
*Domainstatus* für die Website. Der Screenshot-Satz aus Portal-Lastenheft §7a gilt nach Stufe A
als vollständig **ohne** diese drei.

---

## Stufe C — wenn Handarbeit lästig wird

- Mollie-Abo, Zahlungsautomatik, Webhooks, Mahnwesen
- Domainlebenszyklus beim Registrar
- Zeitgesteuerte Löschfristen und Überfälligkeitsprüfung
- Finanzübersichten, Auswertungen, Massenvorgänge
- Bereitstellungsautomatik, Rollback

**Bei ein bis drei Kunden ersetzt jede dieser Funktionen zwanzig Minuten Handarbeit im Monat.**
Rechnungsstatus von Hand setzen, Domain beim Registrar klicken. Erst wenn das spürbar stört, lohnt
die Automatisierung — und dann ist auch bekannt, wie sie aussehen muss, statt es zu raten.

> **Der Unterschied zu Stufe A2, weil er einmal falsch gezogen war:** Die **Tabelle** `invoices` und
> der von Hand gesetzte Zahlungsstatus gehören nach A2 — ohne sie kommt kein Projekt von der
> Angebotsannahme in die Produktion. Nur die **Automatik** dahinter wandert nach C.

---

## Was **nie** verschoben wird

Diese vier Punkte gehören ins Fundament, auch wenn sie in Stufe A nach Aufwand ohne Gegenwert
aussehen:

| Punkt | Warum nicht später |
|---|---|
| **Mandantentrennung** — `organization_id` aus der Sitzung, nie aus der Anfrage | Nachrüsten heißt garantiert eine Stelle übersehen. Dort landen dann Kundendaten beim falschen Kunden |
| **`tests/TenantIsolationTest.php`** vollständig | Der einzige Beleg, dass die Trennung hält. Wird nie abgeschwächt, um grün zu werden |
| **Prüfprotokoll wird geschrieben** (`audit_events`) | Eine schöne Ansicht darf warten. Die Einträge nicht — was nicht protokolliert wurde, ist rückwirkend nicht rekonstruierbar |
| **Tests gegen echte MySQL/MariaDB** | Portal-Lastenheft §16. Kein SQLite, kein Ersatz im Speicher, nichts als grün melden, was nicht gelaufen ist |

Ebenfalls unverändert gültig: **nur `/public` ist über den Webserver erreichbar**, keine Secrets im
Repository, keine erfundenen Werte für Platzhalter aus `SARTU_ENTSCHEIDUNGEN_OFFEN.md`.

---

## Testfälle je Stufe

Die 59 Testfälle aus §16 verteilen sich auf die Etappen. **Nach jeder Etappe laufen die Testfälle
dieser Etappe grün — plus `TenantIsolationTest` vollständig, ab A0, immer.**

**Was nicht passiert:**

- Testfälle zu noch nicht gebauten Funktionen als leere Hüllen anlegen
- Tests überspringen, auskommentieren oder als „später" markieren
- Die vollständige Definition of Done nach Stufe A abhaken

**Die vollständige Definition of Done gilt für den Livegang**, nicht für Stufe A. Am Ende von
Stufe A gehört in `IMPLEMENTATION_SUMMARY.md` eine Zuordnung: welcher Testfall zu welcher Etappe
gehört und welche noch offen sind.

---

## Abhängigkeiten — was worauf wartet

| Wartet auf | Was blockiert ist |
|---|---|
| **Stufe A läuft** | 15 Bildplätze der Website · bewegter Aufmacher · Sitzung 3 insgesamt |
| **Foto des Gründers** | Startseite Sektion 8 (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5) |
| **Anwaltliche Freigabe** | Startsperre, Website-Lastenheft §14a |
| **Adressstatus** | Google-Unternehmensprofil · `LocalBusiness` · Impressum (§1) |
| **Umsatzsteuerstatus** | Preisdarstellung der **gesamten** Website (Portal-Lastenheft §1.4a) |
| **Hosting entschieden** | Livegang — Cron und Mailversand praktisch geprüft (§1.4) |

---

## Arbeitsteilung

| Wer | Was |
|---|---|
| **Codex, lokal** | Stufe A bauen. Es kann auf dem Entwicklungsrechner ausführen, was es baut — bei 59 Tests gegen eine echte Datenbank wiegt das schwerer als jede Sorgfalt beim Schreiben |
| **Claude Code** | Entwürfe der Rechtstexte · Gegenlesen nach jeder Sitzung · Mandantentrennung prüfen · Spezifikation nachziehen, wenn Widersprüche auffallen |
| **Betreiber** | Die drei offenen Angaben · Foto · Hosting auswählen und **praktisch prüfen** (Testmail an eine Fremdadresse, Cronlauf, der eine Datei schreibt) · Mailserver mit SPF, DKIM, DMARC |

> **Warum Claude Code Stufe A nicht baut:** In seiner Umgebung läuft keine MySQL und kein
> Docker-Dienst — geprüft am 28.07.2026. Damit sind die 59 Testfälle dort nicht ausführbar, und
> **nicht ausgeführter Code ist kein fertiger Code.**

---

## Was ab jetzt unterbleibt

**Keine weiteren Design-Runden an der Startseite.** Die Richtung ist entschieden und steht in
`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §3 und im Website-Lastenheft §5: Farbsystem, Formsprache,
Abschnittsreihenfolge, Bauform je Abschnitt, Sprachregel.

Was der Seite noch fehlt, ist **Inhalt** — Bildschirmansichten, ein Foto, Musterprojekte. Inhalt
entsteht nicht im Design-Chat. Fünfzehn Runden an einer Seite, deren Kernmaterial nicht existiert,
sind genug.
