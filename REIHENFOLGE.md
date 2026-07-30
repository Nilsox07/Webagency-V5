# Bau-Reihenfolge — was wann entsteht

**Stand:** 28.07.2026
**Zweck:** Verhindern, dass der Kundenbereich in voller Ausbaustufe entsteht, bevor der erste Kunde
existiert. Das Portal-Lastenheft beschreibt **20 Tabellen und 77 Testfälle**. Wer es von vorn nach
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

## Rangfolge — diese Datei überschreibt nur den Zeitpunkt

**Wichtig, sonst hält ein ausführender Agent an dieser Datei an:** Der Portalauftrag erklärt das
Portal-Lastenheft zur Quelle mit der höchsten Priorität. Es beschreibt alle Screens und alle 77
Testfälle **ohne Zeitangabe**. Diese Datei widerspricht dem nicht, sie ergänzt eine Dimension.

> **`REIHENFOLGE.md` überschreibt für den zeitlichen Umfang** Portal-Lastenheft §0.2, §16 und §17
> sowie Portalauftrag §0.2. **Nur für den Zeitpunkt** — nicht für Inhalt, Texte, Datenmodell,
> Sicherheitsregeln oder Abnahmekriterien. Was hier auf B oder C steht, wird **später gebaut**,
> nicht **anders gebaut**.
>
> Bei jedem anderen Widerspruch gilt weiterhin das Lastenheft.

---

## Stufe A — bis der erste Kunde live ist

**In vier lieferbaren Etappen.** Jede endet an einem Punkt, an dem etwas durchgängig funktioniert
und vorführbar ist.

> **Zweimal korrigiert nach externer Prüfung (28.07.2026).** Fassung 1 schob `leads` und
> `invoices` fälschlich nach B. Fassung 2 enthielt fünf Ablauffehler: `organizations` fehlte in A0,
> obwohl `users.organization_id` darauf verweist · der vollständige Mandantentest war in A0
> unmöglich · A1 setzte eine Website voraus, die es noch nicht gibt · A2 brauchte `approvals` aus
> A3 · und die Tabellenrechnung war schlicht falsch (17, nicht 15).

### A0 — Fundament

Ersteinrichtung (§1.5) · Migrationen · Adminanmeldung mit TOTP · Betreiberdaten (§1.4a) ·
Rechtstexte mit Freigabezustand · Testmailversand · **Mandantentrennung im Datenzugriff** ·
Prüfprotokoll.

**Tabellen (6):** `organizations` · `users` · `sessions` · `audit_events` · `operator_settings` ·
`legal_texts`

> **Warum `organizations` schon hier:** `users.organization_id` verweist darauf, und Fremdschlüssel
> werden mit `ON DELETE RESTRICT` angelegt (§4.1). Ohne `organizations` ist `users` nicht
> migrierbar — ein Nachziehen des Fremdschlüssels widerspräche der Spezifikation.

> **Nicht mitgezählt:** `schema_migrations` — das Protokoll der Einrichtung (Portal-Lastenheft
> §1.5). Es entsteht **vor** allen Fachtabellen und ist keine. Wer es mitzählt, kommt auf 21 und
> sucht eine Tabelle, die es nicht gibt.

**Mandantentest in A0:** `TenantIsolationTest` prüft, was existiert — die Datenzugriffsschicht
filtert immer nach `organization_id` **aus der Sitzung**, nie aus der Anfrage; Adminkonten haben
`organization_id IS NULL`; die Prüfbedingung greift. **Der Test wächst mit jeder Etappe mit**
(siehe „Testfälle je Etappe").

**Fertig, wenn:** Installation läuft auf leerer Datenbank durch · ein Admin meldet sich mit TOTP an ·
eine Testmail kommt an · der Mandantentest im Umfang von A0 läuft grün.

### A1 — Anfrage bis Auftrag

Funktionsfähiger Bedarfsscheck unter `/briefing` · Anfrageliste im Adminbereich · Umwandlung in
Kunde und Projekt · Einladung des Kunden · Kundenanmeldung per Link · Angebot erstellen, senden,
annehmen.

**Tabellen (4):** `leads` · `login_tokens` · `projects` · `offers`

> **Der Bedarfsscheck wird hier als schmaler senkrechter Ausschnitt gebaut** — Formular, Annahme,
> `leads`-Eintrag, Danke-Seite. **Ohne das umgebende Website-Design**, das erst in Sitzung 3
> entsteht. Sonst wäre A1 von etwas abhängig, das nach Stufe A gebaut wird.

**Fertig, wenn:** Eine über `/briefing` abgeschickte Anfrage führt bis zu einem angenommenen
Angebot. Mandantentest um Projekte und Angebote erweitert.

### A2 — Auftrag bis Produktionsstart

Rechnungen **von Hand angelegt**, Zahlungsstatus **von Hand gesetzt**, Mollie-Link eingetragen ·
Aufgaben · Uploads · **Faktenfreigabe**.

**Tabellen (4):** `invoices` · `tasks` · `task_files` · `approvals`

> **Warum `approvals` schon hier:** Die Faktenfreigabe vor Produktionsstart erzeugt zwingend einen
> Eintrag mit `kind = inhalte` (§ Aufgaben, Sonderfall `kind = freigabe`). Ohne die Tabelle
> erreicht kein Projekt den Zustand `produktion`. A3 nutzt dieselbe Tabelle danach für
> `kind = abnahme`.

**Fertig, wenn:** Nach Angebotsannahme führt der Weg über Anzahlung, Aufgaben und Faktenfreigabe bis
`produktion`. Mandantentest um Rechnungen, Aufgaben und Dateien erweitert.

### A3 — Produktion bis Livegang

Vorschau · Korrekturrunden · Abnahme · Domainstatus · Livegang.

**Tabellen (3):** `feedback_rounds` · `feedback_items` · `domain_status`

> **`domain_status` vollständig, aber von Hand gepflegt.** Alle Pflichtfelder aus dem Lastenheft
> werden angelegt und im Adminbereich gesetzt. Verschoben wird nur die **Registrar-Anbindung**
> (Stufe C), nicht die Tabelle — eine Teiltabelle jetzt hieße eine Folgemigration später.

**Fertig, wenn:** Ein Projekt erreicht `live`. **Ab hier existieren die Bildschirmansichten für die
Website.** Mandantentest vollständig für alle Kundenrouten.

### Summe Stufe A

**17 Tabellen** — A0 sechs, A1 vier, A2 vier, A3 drei.
**Zurückgestellt: drei** — `business_hours` · `business_hours_exceptions` · `support_messages`.
**17 + 3 = 20.**

---

## Stufe B — wenn der erste Kunde live ist

- Öffnungszeiten und Ausnahmen selbst pflegen
- Nachrichten an den Betreuer
- Registrar-Anbindung für Domainereignisse

**Tabellen (3):** `business_hours` · `business_hours_exceptions` · `support_messages`

> **Offene Lücke im Lastenheft, nicht in dieser Datei:** Die Selbstpflege verspricht außerdem
> *Bilder tauschen*, *Team- und Projekteinträge pflegen* und *Anfragen von der Website einsehen*.
> **Für keine dieser drei Funktionen existiert eine Tabelle im Datenmodell.** Es fehlen sinngemäß
> `site_content`, `media_assets` und `website_inquiries`.
>
> **Das wird nicht hier erfunden.** Entweder das Datenmodell im Portal-Lastenheft wird um diese
> Tabellen ergänzt, oder die Funktionsliste im Website-Lastenheft §5 Sektion 2 wird gekürzt.
> **Bis dahin darf die Website diese drei Funktionen nicht bewerben** — siehe „Zwei Livegänge".

**Erst hier verfügbar:** die Bildschirmansichten *Öffnungszeiten* und *Nachrichten*.

---

## Zwei Livegänge, nicht einer

Ein Punkt, der bisher fehlte und der sonst zu einem Werbeversprechen ohne Deckung führt:

| | Wann | Bedingung |
|---|---|---|
| **Pilotkunde ist live** | nach A3 | Ein echtes Projekt erreicht `live` |
| **Öffentliche Website geht live** | nach B | Die Seite darf **nur Funktionen bewerben, die existieren** |

Die Startseite verspricht in Sektion 2 unter anderem *Bilder tauschen* und *Team- und
Projekteinträge pflegen*. Solange diese Funktionen nicht gebaut sind, wird die Seite **nicht
veröffentlicht** oder die betreffenden Zeilen entfallen. Werbung für nicht vorhandene Funktionen
ist irreführend — und bei einem Anbieter, der mit Ehrlichkeit wirbt, der teuerste Widerspruch.

---

## Stufe C — wenn Handarbeit lästig wird

- Mollie-Abo, Zahlungsautomatik, Webhooks, Mahnwesen
- Domainlebenszyklus beim Registrar
- **Zeitgesteuerte Aufgaben:** IP-Löschung nach 30 Tagen, Löschfristen, Überfälligkeitsprüfung
- Finanzübersichten, Auswertungen, Massenvorgänge
- Bereitstellungsautomatik, Rollback

> **Der Unterschied zu A2:** Die **Tabelle** `invoices` und der von Hand gesetzte Zahlungsstatus
> gehören nach A2 — ohne sie kommt kein Projekt in die Produktion. Nur die **Automatik** wandert
> nach C.

> **Zwei Testfälle hängen an C und sind vorab zugeordnet:** der Überfälligkeitstest und der Test
> zur IP-Löschung nach 30 Tagen setzen die zeitgesteuerte Aufgabe voraus. Sie werden **in C**
> geschrieben, nicht vorher als leere Hüllen angelegt. Die Zuordnung steht **vor** dem Bau fest,
> nicht erst in der Abschlusszusammenfassung.

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

## Testfälle je Etappe — der Mandantentest wächst mit

**`TenantIsolationTest` ist nach jeder Etappe vollständig für den bis dahin gebauten Umfang.**
Die Endfassung entsteht, wenn alle Entitäten existieren — nicht vorher, weil sie sonst gegen
Tabellen prüfen müsste, die es noch nicht gibt.

| Etappe | Der Test deckt ab |
|---|---|
| **A0** | Datenzugriffsschicht filtert nach `organization_id` **aus der Sitzung** · Admin hat `organization_id IS NULL` · Prüfbedingung greift |
| **A1** | zusätzlich: fremde Projekte und Angebote sind unerreichbar |
| **A2** | zusätzlich: fremde Rechnungen, Aufgaben und Dateien |
| **A3** | zusätzlich: alle Kundenrouten — **Endfassung** |
| **B** | um jede neu hinzukommende Kundenroute erweitert |

**Die übrigen Testblöcke nach Etappe** — damit nicht am Ende von Stufe A eine Zuordnung erfunden
werden muss:

| §16-Block | Etappe | Begründung |
|---|---|---|
| **67–73** Ersteinrichtung | **A0** | Die Installation ist A0. Ohne sie entsteht keine Datenbank |
| **64–66** Betreiberdaten | **A0** | `operator_settings` und `legal_texts` gehören zu A0 |
| 1–5b Mandantentrennung | wächst A0→A3 | siehe Tabelle oben |
| 6–10 Anmeldung | **A1** | `login_tokens` entsteht dort |
| 29–40b Anfrageeingang | **A1** | `/briefing` ist der senkrechte Ausschnitt in A1 |
| 11–13, 21–24 Angebot | **A1** | |
| 14, 16–17, 26–27, 51–54 Zahlung, Aufgaben, Freigabe | **A2** | |
| 18, 25, 28 Abnahme, Runden, Schutzbeginn | **A3** | |
| **60–63** Statusübergänge | wächst A1→A3 | Geprüft wird immer nur, was an Zuständen existiert: A1 bis `angebot_angenommen`, A2 bis `produktion`, A3 bis `live`. **Fall 63 (`live → korrektur`) erst in A3** |
| 19 Öffnungszeiten | **B** | `business_hours` ist Stufe B |
| 15 Überfälligkeit, 40 IP-Löschung | **C** | setzen die zeitgesteuerte Aufgabe voraus |
| 41–50, 55–59 Sicherheit, Protokoll, Bedienung | ab **A0**, wachsend | Jede neue Route wird sofort mit aufgenommen, nicht nachgezogen |

**Was nicht passiert:** Tests zu ungebauten Funktionen als leere Hüllen anlegen · Tests
überspringen, auskommentieren oder als „später" markieren · den Mandantentest abschwächen, damit
er grün wird · die vollständige Definition of Done nach Stufe A abhaken.

**Die vollständige Definition of Done gilt für den Livegang**, nicht für Stufe A.

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
| **Codex, lokal** | Stufe A bauen. Es kann auf dem Entwicklungsrechner ausführen, was es baut — bei 77 Tests gegen eine echte Datenbank wiegt das schwerer als jede Sorgfalt beim Schreiben |
| **Claude Code** | Entwürfe der Rechtstexte · Gegenlesen nach jeder Sitzung · Mandantentrennung prüfen · Spezifikation nachziehen, wenn Widersprüche auffallen |
| **Betreiber** | Die drei offenen Angaben · Foto · Hosting auswählen und **praktisch prüfen** (Testmail an eine Fremdadresse, Cronlauf, der eine Datei schreibt) · Mailserver mit SPF, DKIM, DMARC |

> **Warum Claude Code Stufe A nicht baut:** In seiner Umgebung läuft keine MySQL und kein
> Docker-Dienst — geprüft am 28.07.2026. Damit sind die 77 Testfälle dort nicht ausführbar, und
> **nicht ausgeführter Code ist kein fertiger Code.**

---

## Was ab jetzt unterbleibt

**Keine weiteren Design-Runden an der Startseite.** Die Richtung ist entschieden und steht in
`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §3 und im Website-Lastenheft §5: Farbsystem, Formsprache,
Abschnittsreihenfolge, Bauform je Abschnitt, Sprachregel.

Was der Seite noch fehlt, ist **Inhalt** — Bildschirmansichten, ein Foto, Musterprojekte. Inhalt
entsteht nicht im Design-Chat. Fünfzehn Runden an einer Seite, deren Kernmaterial nicht existiert,
sind genug.
