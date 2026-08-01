# Bau-Reihenfolge — was wann entsteht

**Stand:** 28.07.2026
**Zweck:** Verhindern, dass der Kundenbereich in voller Ausbaustufe entsteht, bevor der erste Kunde
existiert. Das Portal-Lastenheft beschreibt **20 Tabellen und 88 Testfälle**. Wer es von vorn nach
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
Portal-Lastenheft zur Quelle mit der höchsten Priorität. Es beschreibt alle Screens und alle 88
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
Kunde und Projekt · Einladung des Kunden · Kundenanmeldung per Link · Angebot erstellen und
**senden** · **Löschlauf für IP-Adressen und Anfragefristen**.

**Tabellen (4):** `leads` · `login_tokens` · `projects` · `offers`

> **Der Bedarfsscheck wird hier als schmaler senkrechter Ausschnitt gebaut** — Formular, Annahme,
> `leads`-Eintrag, Danke-Seite. **Ohne das umgebende Website-Design**, das erst in Sitzung 4
> entsteht. Sonst wäre A1 von etwas abhängig, das nach Stufe A gebaut wird.

> **A1 endet beim gesendeten Angebot, nicht bei der Annahme.** Grund: Nach der Annahme lautet der
> festgelegte nächste Schritt „Anzahlung bezahlen" mit Ziel `/rechnungen` (§5.6). Diese Route
> entsteht erst in A2. Ein Kunde in A1 wäre also in einen Zustand gelaufen, dessen nächster Schritt
> ins Leere zeigt. **Die Angebotsannahme steht am Anfang von A2**, zusammen mit den Rechnungen.
> Die Tabelle `offers` bleibt in A1 — nur die Handlung wandert.

> **Warum der Löschlauf schon hier steht:** Ab A1 entstehen **echte** Anfragen mit echten
> IP-Adressen. Testfall 40 verlangt, dass `source_ip` nach 30 Tagen geleert ist. Eine zugesagte
> Löschfrist, die niemand ausführt, ist keine Verzögerung im Bauplan, sondern ein
> Datenschutzverstoß. Der Lauf ist klein: ein Cron-Aufruf, zwei Abfragen. Mollie, Mahnwesen und
> Komfortautomatik bleiben in C.

**Fertig, wenn:** Eine über `/briefing` abgeschickte Anfrage führt bis zu einem **gesendeten**
Angebot, das der Kunde in seinem Bereich sieht. Der Löschlauf leert nachweislich eine 31 Tage alte
`source_ip`. Mandantentest um Projekte und Angebote erweitert.

### A2 — Auftrag bis Produktionsstart

**Angebotsannahme durch den Kunden** · Rechnungen **von Hand angelegt**, Zahlungsstatus **von Hand
gesetzt**, Mollie-Link eingetragen · **Überfälligkeitslauf** · Aufgaben · Uploads ·
**Faktenfreigabe**.

**Tabellen (5):** `invoices` · `tasks` · `task_files` · `approvals` · **`support_messages`**

> **`support_messages` ist von B nach A2 vorgezogen** (Audit vom 31.07.2026). Die
> Kundenoberfläche verweist schon vorher zweimal auf `Hilfe`: bei einem abgelaufenen Angebot
> (§8.2) und bei den Grenzen der Selbstpflege (§8.7). Der Kunde meldet sich ausschließlich per
> Anmeldelink an — ohne diese Tabelle hat er **keinen Rückkanal**, und jede Rückfrage endet in
> einer Sackgasse.
>
> Eine Tabelle mit vier Feldern, ein Formular, eine Adminansicht. Der Aufwand ist klein, die
> Lücke war es nicht.

> **Der Überfälligkeitslauf gehört hierher, nicht nach C.** §5.3 legt fest, dass `ueberfaellig`
> **täglich automatisch** gesetzt wird, sobald `due_date` überschritten ist. Ab A2 gibt es echte
> Rechnungen mit echten Fristen. Ohne den Lauf zeigt das Portal einem Kunden „Offen — zahlbar bis
> gestern". Auch dieser Lauf ist eine Abfrage. Die **Automatik drumherum** — Mollie-Abgleich,
> Mahnstufen, Erinnerungsmails — bleibt in C.

> **Warum `approvals` schon hier:** Die Faktenfreigabe vor Produktionsstart erzeugt zwingend einen
> Eintrag mit `kind = inhalte` (§ Aufgaben, Sonderfall `kind = freigabe`). Ohne die Tabelle
> erreicht kein Projekt den Zustand `produktion`. A3 nutzt dieselbe Tabelle danach für
> `kind = abnahme`.

**Fertig, wenn:** Der Kunde nimmt das Angebot an, und der Weg führt über Anzahlung, Aufgaben und
Faktenfreigabe bis `produktion`. Eine Rechnung mit überschrittenem `due_date` steht am nächsten Tag
auf `ueberfaellig`. Mandantentest um Rechnungen, Aufgaben und Dateien erweitert.

### A3 — Produktion bis Livegang

Vorschau · Korrekturrunden · Abnahme · Domainstatus · Livegang.

**Tabellen (3):** `feedback_rounds` · `feedback_items` · `domain_status`

> **`domain_status` vollständig, aber von Hand gepflegt.** Alle Pflichtfelder aus dem Lastenheft
> werden angelegt und im Adminbereich gesetzt. Verschoben wird nur die **Registrar-Anbindung**
> (Stufe C), nicht die Tabelle — eine Teiltabelle jetzt hieße eine Folgemigration später.

**Fertig, wenn:** Ein Projekt erreicht `live`. **Ab hier existieren die Bildschirmansichten für die
Website.** Mandantentest vollständig für alle Kundenrouten.

### Summe Stufe A

**18 Tabellen** — A0 sechs, A1 vier, A2 **fünf**, A3 drei.
**Zurückgestellt: zwei** — `business_hours` · `business_hours_exceptions`.
**18 + 2 = 20.**

---

## Stufe B — wenn der erste Kunde live ist

- Öffnungszeiten und Ausnahmen selbst pflegen

**Tabellen (2):** `business_hours` · `business_hours_exceptions`

> **Nachrichten an den Betreuer stehen nicht mehr hier**, sondern in A2 — siehe dort.

> **Kein Registrar in B.** Eine frühere Fassung nannte hier „Registrar-Anbindung für
> Domainereignisse" und in C „Domainlebenszyklus beim Registrar" — zweimal dieselbe Sache ohne
> Grenze dazwischen. **Verbindlich:** In A3 wird `domain_status` vollständig angelegt und **von
> Hand** gepflegt. Die **gesamte** Registrar-Anbindung — Verfügbarkeitsabfrage, Registrierung,
> Verlängerung, Ablaufwarnung, Übertragung — liegt geschlossen in C. In B passiert dazu nichts.

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
- **Registrar vollständig:** Verfügbarkeit, Registrierung, Verlängerung, Ablaufwarnung, Übertragung
- Finanzübersichten, Auswertungen, Massenvorgänge
- Bereitstellungsautomatik, Rollback

> **Der Unterschied zu A2:** Die **Tabelle** `invoices` und der von Hand gesetzte Zahlungsstatus
> gehören nach A2 — ohne sie kommt kein Projekt in die Produktion. Nur die **Automatik** wandert
> nach C.

> **Die zeitgesteuerten Aufgaben stehen nicht mehr hier.** Eine frühere Fassung schob IP-Löschung,
> Löschfristen und Überfälligkeitsprüfung geschlossen nach C. Das war falsch: Ab A1 entstehen echte
> Anfragen, ab A2 echte Rechnungen. Beide Läufe sind jetzt in ihrer Etappe (A1 und A2). **In C
> bleibt, was darauf aufsetzt** — Mahnstufen, Erinnerungsmails, Zahlungsabgleich.
>
> **Kein Testfall hängt mehr an C.** Alle Testfälle aus §16 sind einer Etappe in A oder B
> zugeordnet, jeder genau einmal (siehe unten).

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

### Jeder Testfall genau einmal — die vollständige Zuordnung

Die frühere Fassung ordnete **Blöcke** zu. Eine externe Prüfung hat daran vier Fehler gefunden:
Fall 40 stand in A1 **und** in C · Fall 20 nirgends · Fall 53 prüfte zwei Dinge aus zwei Etappen ·
und der Sammelblock „41–50 ab A0" enthielt Fälle, die eine Tabelle aus A2 brauchen. Alle vier
Befunde stimmten.

**Deshalb hier eine Zeile je Testfall.** Die Etappe ist die, in der er **entsteht**; danach läuft
er in jeder folgenden Etappe mit.

| # | Etappe | | # | Etappe | | # | Etappe |
|---|---|---|---|---|---|---|---|
| 1 | A1 | | 28 | A3 | | 55 | A0 |
| 2 | A2 | | 29 | A1 | | 56 | A0 |
| 3 | A1 | | 30 | A1 | | 57 | A1 |
| 4 | A2 | | 31 | A1 | | 58 | A0 |
| 5 | A1 | | 32 | A1 | | 59 | A1 |
| 5a | A0 | | 33 | A1 | | 60 | A1 |
| 5b | A0 | | 34 | A1 | | 61 | A2 |
| 6 | A1 | | 35 | A1 | | 62 | A1 |
| 7 | A1 | | 36 | A1 | | 63 | A3 |
| 8 | A1 | | 37 | A1 | | 64 | A0 |
| 9 | A1 | | 38 | A1 | | 65 | A0 |
| 10 | A1 | | 39 | A1 | | 66 | A0 |
| 11 | A2 | | 40 | **A1** | | 67 | A0 |
| 12 | A2 | | 40a | A1 | | 68 | A0 |
| 13 | A2 | | 40b | A1 | | 69 | A0 |
| 14 | A2 | | 41 | A0 | | 70 | A0 |
| 15 | **A2** | | 42 | A1 | | 71 | A0 |
| 16 | A2 | | 43 | A0 | | 72 | A0 |
| 17 | A2 | | 44 | A0 | | 73 | A0 |
| 18 | A3 | | 45 | A1 | | 74 | A0 |
| 19 | **B** | | 46 | **A2** | | 75 | A0 |
| 20 | **A1** | | 47 | A0 | | 76 | A0 |
| 21 | A1 | | 48 | A0 | | | |
| 22 | A1 | | 49 | A0 | | | |
| 23 | A1 | | 50 | A0 | | | |
| 24 | A2 | | 51 | A2 | | | |
| 25 | A3 | | 52 | A2 | | | |
| 26 | A2 | | 53a | **A2** | | | |
| 27 | A2 | | 53b | **A3** | | | |
| | | | 54 | A2 | | | |

**Nach dem Audit vom 31.07.2026 kamen sieben Fälle dazu** — 77 bis 83, alle zu Lücken, die vorher
niemand geprüft hätte:

| # | Etappe | Prüft |
|---|---|---|
| 77 | **A2** | Teilzahlung ergibt `teilweise_bezahlt`, nicht `bezahlt` |
| 78 | **A2** | Zahlungserinnerung genau einmal, nicht täglich |
| 79 | **A2** | Speichergrenze je Organisation greift |
| 80 | **A1** | Nicht umgewandelte Anfrage wird nach 12 Monaten gelöscht |
| 81 | **A0** | AVV im Zustand `entwurf` blockiert die Veröffentlichung |
| 82 | **A0** | Rechtstext mit `audience = kunde` ist öffentlich nicht abrufbar |
| 83 | **A1** | Anmeldeseite zeigt die Telefonnummer aus den Betreiberdaten |

**Summe:** A0 = 26 · A1 = 35 · A2 = 20 · A3 = 6 · B = 1 · C = 0. **Zusammen 88.**

**Die acht Fälle, bei denen die Zuordnung nicht offensichtlich ist:**

| # | Was er prüft | Warum diese Etappe |
|---|---|---|
| 2 | fremde Rechnung, Aufgabe, Datei **und** Angebot | Braucht alle vier Entitäten. `invoices`, `tasks`, `task_files` entstehen in **A2** |
| 11–13 | Angebotsannahme | Die **Annahme** ist nach A2 gewandert, das Angebot bleibt A1 |
| 15 | `ueberfaellig` wird gesetzt | Der Überfälligkeitslauf ist von C nach **A2** vorgezogen |
| 20 | Statuswechsel erzeugt Audit mit Akteur | War vorher **nirgends** zugeordnet. Der erste Wechsel ist `angebot_offen` beim Senden — **A1** |
| 40 | `source_ip` nach 30 Tagen geleert | War doppelt (A1 **und** C). Der Löschlauf ist nach **A1** vorgezogen, damit gilt A1 |
| 46 | unerlaubter Dateityp abgelehnt | Steckte im Sammelblock „ab A0", braucht aber `task_files` aus **A2** |
| 53a/53b | `due_date` **und** `protection_started_on` | War **ein** Fall über zwei Etappen. **Wird geteilt:** `due_date` in A2, `protection_started_on` in A3 |
| 57 | Willkommensstrecke erscheint einmal | Steckte im Sammelblock „ab A0", braucht aber die Kundenanmeldung aus **A1** |

> **Fall 53 wird in §16 geteilt.** Aus einem Testfall werden zwei mit eigener Nummer. Das ist keine
> Erfindung eines neuen Kriteriums, sondern die Trennung zweier Prüfungen, die versehentlich in
> einer Zeile standen.

> **Die Fälle 74–76 sind neu** und prüfen die nachträgliche Migration aus Portal-Lastenheft §1.5a.
> Ohne sie wäre der zweite Migrationsweg ungeprüft — und genau daran wäre Stufe B gescheitert.

**Was nicht passiert:** Tests zu ungebauten Funktionen als leere Hüllen anlegen · Tests
überspringen, auskommentieren oder als „später" markieren · den Mandantentest abschwächen, damit
er grün wird · die vollständige Definition of Done nach Stufe A abhaken.

**Die vollständige Definition of Done gilt für den Livegang**, nicht für Stufe A.

---

## Abhängigkeiten — was worauf wartet

| Wartet auf | Was blockiert ist |
|---|---|
| **Stufe A läuft** | 15 Bildplätze der Website · bewegter Aufmacher · Sitzung 4 insgesamt |
| **Foto des Gründers** | Startseite Sektion 8 (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5) |
| **Anwaltliche Freigabe** | Startsperre, Website-Lastenheft §14a |
| **Adressstatus** | Google-Unternehmensprofil · `LocalBusiness` · Impressum (§1) |
| **Umsatzsteuerstatus** | Preisdarstellung der **gesamten** Website (Portal-Lastenheft §1.4a) |
| **Hosting entschieden** | Livegang — Cron und Mailversand praktisch geprüft (§1.4) |

---

## Arbeitsteilung

| Wer | Was |
|---|---|
| **Codex, lokal** | Stufe A bauen. Es kann auf dem Entwicklungsrechner ausführen, was es baut — bei 88 Tests gegen eine echte Datenbank wiegt das schwerer als jede Sorgfalt beim Schreiben |
| **Claude Code** | Entwürfe der Rechtstexte · Gegenlesen nach jeder Sitzung · Mandantentrennung prüfen · Spezifikation nachziehen, wenn Widersprüche auffallen |
| **Betreiber** | Die drei offenen Angaben · Foto · Hosting auswählen und **praktisch prüfen** (Testmail an eine Fremdadresse, Cronlauf, der eine Datei schreibt) · Mailserver mit SPF, DKIM, DMARC |

> **Warum Claude Code Stufe A nicht baut:** In seiner Umgebung läuft keine MySQL und kein
> Docker-Dienst — geprüft am 28.07.2026. Damit sind die 88 Testfälle dort nicht ausführbar, und
> **nicht ausgeführter Code ist kein fertiger Code.**

---

## Was ab jetzt unterbleibt

**Keine weiteren Design-Runden an der Startseite.** Die Richtung ist entschieden und steht in
`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §3 und im Website-Lastenheft §5: Farbsystem, Formsprache,
Abschnittsreihenfolge, Bauform je Abschnitt, Sprachregel.

Was der Seite noch fehlt, ist **Inhalt** — Bildschirmansichten, ein Foto, Musterprojekte. Inhalt
entsteht nicht im Design-Chat. Fünfzehn Runden an einer Seite, deren Kernmaterial nicht existiert,
sind genug.
