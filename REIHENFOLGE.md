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

Der Weg, den ein erster Kunde tatsächlich geht, auf **beiden** Seiten.

| Kundenseite | Adminseite |
|---|---|
| Anmeldung per Link | Angebot erstellen und versenden |
| Angebot ansehen und annehmen | Antworten des Kunden lesen |
| Fragen zum Betrieb beantworten | Hochgeladene Dateien herunterladen |
| Logo, Bilder, Unterlagen hochladen | Rückmeldungen beantworten |
| Fertige Vorschau ansehen | Vorschau freischalten |
| Rückmeldung geben und freigeben | Projektstatus setzen |

### Tabellen in Stufe A

`organizations` · `users` · `login_tokens` · `sessions` · `projects` · `offers` · `tasks` ·
`task_files` · `feedback_rounds` · `feedback_items` · `approvals` · `audit_events`

**Zwölf von achtzehn.** Zurückgestellt: `leads`, `invoices`, `domain_status`, `business_hours`,
`business_hours_exceptions`, `support_messages`.

### Warum Stufe A zuerst kommt

**Fünfzehn Bildplätze** im Website-Lastenheft warten auf Ansichten aus dem Kundenbereich —
Aufmacher, Portal-Abschnitt und sechs Ablaufschritte. Solange Stufe A nicht läuft, ist die
Startseite **nicht fertigstellbar**, egal wie viele Design-Runden noch folgen.

Stufe A ist damit die kritische Kette des gesamten Projekts.

---

## Stufe B — wenn der erste Kunde live ist

Ein paar Wochen nach Vertragsschluss, vor Kunde zwei und drei.

- Selbstpflege: Öffnungszeiten, Kontaktdaten, Bilder tauschen, Team- und Projekteinträge
- Anfragen von der Website einsehen
- Rechnungen und Laufzeit einsehen
- Domainstatus
- Nachrichten an den Betreuer

**Tabellen:** `leads` · `invoices` · `domain_status` · `business_hours` ·
`business_hours_exceptions` · `support_messages`

---

## Stufe C — wenn Handarbeit lästig wird

- Mollie-Abo, Zahlungsautomatik, Mahnwesen
- Domainlebenszyklus beim Registrar
- Zeitgesteuerte Löschfristen und Überfälligkeitsprüfung
- Finanzübersichten, Auswertungen, Massenvorgänge
- Bereitstellungsautomatik, Rollback

**Bei ein bis drei Kunden ersetzt jede dieser Funktionen zwanzig Minuten Handarbeit im Monat.**
Rechnung selbst schreiben, Domain beim Registrar klicken. Erst wenn das spürbar stört, lohnt die
Automatisierung — und dann ist auch bekannt, wie sie aussehen muss, statt es zu raten.

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

Die 59 Testfälle aus §16 verteilen sich mit. **In Stufe A laufen die Tests, die Stufe-A-Funktionen
betreffen — plus `TenantIsolationTest` vollständig.** Tests zu Rechnungen, Domainstatus oder
Öffnungszeiten gehören zu Stufe B und werden dort geschrieben, nicht vorher als leere Hüllen
angelegt.

**Was nicht passiert:** Testfälle überspringen, auskommentieren oder als „später" markieren, um die
Definition of Done früher abhaken zu können.

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
