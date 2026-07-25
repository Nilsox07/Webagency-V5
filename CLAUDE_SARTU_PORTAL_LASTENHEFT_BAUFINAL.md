# SARTU-Portal — Lastenheft (baufinal, Stufe 0)

**Stand:** 25.07.2026 · **Adressat:** die ausführende KI (Codex)
**Zweck:** vollständige Bauvorlage für das SARTU-Kunden- und Adminportal in der **Stufe-0-Ausbaustufe**. Wer dieses Dokument hat, kann bauen — ohne Rückfragen zu Datenmodell, Screens, Texten, Zuständen, Sicherheit oder Abnahme.

**Gilt zusammen mit:**
- `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md` — Geschäftsmodell, Preise, Portalvision (§9, §9a, §23)
- `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` — **wie die visuelle Ebene entsteht** (Farben/Schriften sind hier bewusst nicht vorgegeben)
- `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` — Sprachregeln (§2) gelten unverändert auch hier

**Nicht verwenden:** `design/_verworfen/` (verworfene Designentwürfe), veraltete Preise in `konzepte/`.

---

## 0. Auftrag und Abgrenzung

### 0.1 Was gebaut wird

Ein **sichtbares, klickbares Portal**, das den kompletten Kundenprozess vom Angebot bis zur ersten Pflege trägt. Die Mechanik dahinter darf manuell sein — **die Oberfläche nicht**.

> **Leitsatz: sichtbares Portal sofort, tiefe Automatisierung später.**
> Wenn „Portal statt E-Mail-Chaos" das Verkaufsargument ist, darf Stufe 0 keine Upload-Hülle sein.

### 0.2 Verbindlicher Funktionsumfang Stufe 0

| Muss sichtbar und bedienbar sein | Mechanik dahinter darf sein |
|---|---|
| **Anfrageeingang von der Website** (§4b) | ein Endpunkt, Admin wandelt bewusst in Kunde um |
| Anmeldung ohne Passwort (Magic Link) | einfache Auth, Konten manuell angelegt |
| Willkommensstrecke beim ersten Login — **drei Bildschirme** | statisch, Inhalt fest |
| Cockpit mit **genau einem** nächsten Schritt | Status vom Admin gesetzt |
| Angebot mit Umfang, Preis, Zahlungsplan + digitale Annahme | Admin erstellt das Angebot im Adminbereich |
| Rechnungen mit Status und **Mollie-Zahlungslink** | Link manuell erzeugt, kein Abo-Automatismus |
| Aufgabenliste mit Upload | Aufgaben aus Vorlage, vom Admin zugewiesen |
| **Protokollierte Faktenfreigabe** vor Produktionsstart | Kunde bestätigt mit Namen, Eintrag in `approvals` |
| Vorschau-Link + gebündeltes Feedback **mit sichtbarer Rundenzählung** | Vorschau manuell bereitgestellt, Runden vom Admin geöffnet |
| Freigabe/Abnahme mit Zeitstempel | manuell bestätigt, aber protokolliert |
| Domain- und E-Mail-Status | manuell gepflegter Statuswert |
| **Onlinegang mit Betriebsbeginn und Mindestlaufzeit** | Admin meldet den Start, System rechnet die Frist |
| **Eine echte Pflegefunktion:** Öffnungszeiten | Änderung löst manuellen Rebuild aus |
| Hilfe/Nachricht an SARTU | einfaches Nachrichtenfeld |
| Adminbereich für all das | – |

### 0.3 Ausdrücklich NICHT in Stufe 0

Automatische Domainregistrierung · Mollie-Abo/Mandate/Webhooks · KI-Agenten-Orchestrierung · automatische Builds oder Deployments · SEO-Flottenzentrale · Rollback-Automation · Rechnungserzeugung als Buchhaltung (die läuft in lexoffice/sevDesk) · Mehrbenutzer-Rollen pro Kunde · Dateiversionierung · Volltextsuche · Benachrichtigungseinstellungen · Dunkelmodus · automatische Berechnung oder Sperrung bei überschrittenen Korrekturrunden · Kündigungs- und Verlängerungslogik.

**Regel:** Wird eine dieser Funktionen gebraucht, wird sie **beantragt, nicht gebaut**.

### 0.3a Anfrageliste ja, Vertriebssystem nein — die Grenze genau

Frühere Fassungen verboten pauschal eine „Lead-Inbox" und verlangten zugleich einen Anfrageeingang mit
Adminansicht. Das war widersprüchlich. Die Grenze verläuft so:

| **Wird gebaut** (nötig, sonst geht keine Anfrage ein) | **Wird nicht gebaut** (das wäre ein Vertriebssystem) |
|---|---|
| Ein Endpunkt, der Anfragen der eigenen SARTU-Website annimmt (§4b) | Annahme von Anfragen aus **Kundenwebsites** — das ist die „Lead-Inbox" der Stufe 1 |
| Liste, Detailansicht, Status `neu` / `in_pruefung` / `angebot_erstellt` / `abgelehnt` | Pipeline-, Kanban- oder Trichteransichten |
| Notizfeld, Umwandlung in Kunde per Klick | Bewertung, Punktevergabe, Priorisierungslogik |
| Export und Löschung je Datensatz (Betroffenenrechte) | Nachfassketten, Erinnerungen, Kampagnen, Serienmails |
| Eine Benachrichtigungs-E-Mail an SARTU je Anfrage | E-Mail-Verlauf, Postfachanbindung, Vorlagenverwaltung |
| | Zuweisung an Bearbeiter, Teamfunktionen, Aktivitätenstrom |

**Merksatz:** Eine **Liste mit vier Zuständen und einem Umwandlungsknopf** — mehr nicht. Sobald etwas
automatisch nachfasst, bewertet oder verteilt, ist die Grenze überschritten.

### 0.4 Portal-Screenshots

Die Website braucht Screenshots aus **dieser echten Oberfläche**. Deshalb muss das Portal mit **realistischen Musterdaten** befüllbar sein (Seed). Keine gezeichneten Fake-Dashboards. Musterdaten enthalten **keine** echten Personennamen und **keine** realistischen Rechnungsnummern.

---

## 1. Technischer Rahmen

**Stack — entschieden, nicht zur Diskussion** (Quelle: `konzepte/sartuportalCLAUDE.md`, bewusst langweilig und dauerhaft betreibbar):

| Bereich | Festlegung |
|---|---|
| Laufzeit | **Node 22 LTS** |
| Server | **Fastify 5** |
| Ansichten | **EJS**, Server-Side Rendering, minimales Browser-JS |
| Datenbank | **PostgreSQL 16+** |
| Passwort-Hashing (nur Admin) | **Argon2id** |
| Verschlüsselung sensibler Felder | **AES-256-GCM** |
| Migrationen | einfache, nummerierte SQL-Dateien mit Migrationstabelle |
| Tests | Node Test Runner; DB-Tests gegen **echtes PostgreSQL** (kein In-Memory-Ersatz in CI) |
| Hosting | Hetzner (Deutschland) hinter Reverse Proxy, HTTPS erzwungen |

**Verboten:** React, Next.js, jedes SPA-Framework, Build-Pipelines für das Frontend, externe CDNs, Tailwind-CDN, Client-seitiges Routing.

**Erlaubt an Browser-JS:** Formular-Verbesserungen, Datei-Upload-Fortschritt, Akkordeon, Bestätigungsdialoge. **Das Portal muss ohne JavaScript vollständig bedienbar bleiben** — jede Aktion ist ein normales Formular mit `POST`.

**Umgebungen:** `local` (Entwicklung, Seed-Daten) · `production`. Konfiguration ausschließlich über Umgebungsvariablen; `.env.example` gehört ins Repository, `.env` **niemals**.

**Erforderliche Umgebungsvariablen:**
`DATABASE_URL` · `SESSION_SECRET` · `ENC_KEY` (32 Byte, base64) · `SMTP_HOST` `SMTP_PORT` `SMTP_USER` `SMTP_PASS` `MAIL_FROM` · `ADMIN_NOTIFY_EMAIL` (interne Meldungen) · `BASE_URL` · `ADMIN_TOTP_ISSUER` · `UPLOAD_DIR` · `INTAKE_TOKEN` (Anfrageeingang, §4b) · `NODE_ENV`

---

## 2. Rollen und Rechte

| Rolle | Anmeldung | Sieht |
|---|---|---|
| **Kunde** | Magic Link (kein Passwort) | ausschließlich Daten der **eigenen** Organisation |
| **Admin** | E-Mail + Passwort (Argon2id) + **TOTP-Zweifaktor** | alles |

**Stufe 0 kennt genau einen Benutzer je Kundenorganisation.** Mehrere Ansprechpartner sind Stufe 1.

Es gibt **keine** Selbstregistrierung. Admin legt Organisation und Benutzer an; der Kunde erhält eine Einladungs-E-Mail.

---

## 3. Eiserne Sicherheitsregeln (nicht verhandelbar)

1. **Mandantentrennung ist heilig.** Jede Abfrage im **Kundenbereich** filtert nach `organization_id` **aus der Session** — niemals aus einem Request-Parameter, Formularfeld oder URL-Segment. Kunde A darf unter keinen Umständen Daten von Kunde B sehen.
   Der Test `test/tenant-isolation.test.js` ist **unantastbar**: nie löschen, nie abschwächen, um grün zu werden.
2. **Objektzugriff immer doppelt prüfen:** Existiert das Objekt **und** gehört es zur Session-Organisation? Sonst **404**, nicht 403 (keine Existenz preisgeben).
2a. **Getrennte Datenzugriffswege für Kunde und Admin.** Regel 1 lässt sich nur einhalten, wenn ein Admin nicht durch dieselbe Tür geht — Admins haben bewusst **keine** eigene `organization_id`. Deshalb gilt:
   - Es gibt **zwei** getrennte Zugriffsschichten. Die Kundenschicht nimmt die Organisation **ausschließlich** aus der Session und hat **keinen** Parameter, mit dem sich das umgehen ließe. Ein fehlender Session-Wert ist ein **Fehler**, kein „alles anzeigen".
   - Die Adminschicht ist eine **eigene**, klar getrennte Schicht. Nur sie darf organisationsübergreifend lesen. Jeder Aufruf darin setzt eine bestandene Adminprüfung voraus (Rolle `admin` **und** abgeschlossene Zweifaktor-Anmeldung).
   - Adminrouten liegen unter `/admin/…` und werden **vollständig** durch eine einzige, zentrale Vorprüfung geschützt — nicht Route für Route einzeln. Fällt die Prüfung aus, ist die Route nicht erreichbar.
   - **Verboten:** ein gemeinsamer Codepfad, der bei Admins den Organisationsfilter „einfach weglässt" (etwa `WHERE organization_id = $1 OR $2 IS TRUE`). Genau daraus entsteht die typische Datenpanne.
   - Wählt ein Admin im Adminbereich einen Kunden aus, ist diese Auswahl **nur** im Adminbereich gültig. Sie schreibt **niemals** die Session-Organisation um und wirkt sich nie auf Kundenrouten aus.
   - Der Isolationstest prüft beides: (a) Kunde A sieht Kunde B nicht, (b) ein **abgemeldeter oder nicht-Admin-Benutzer** erreicht keine einzige Adminroute — geprüft über die **vollständige** Liste der Adminrouten, nicht über eine Stichprobe.
3. **CSRF-Token bei jedem `POST`.** Kein Token, keine Ausnahme.
4. **Rate-Limit** auf Login-Anforderung (5 pro E-Mail und Stunde, 20 pro IP und Stunde) und auf Token-Einlösung.
5. **Magic-Link-Token:** kryptografisch zufällig (≥ 32 Byte), **nur als Hash gespeichert**, gültig **15 Minuten**, **einmalig** verwendbar, an die E-Mail gebunden.
6. **Sessions:** `httpOnly`, `secure`, `SameSite=Lax`, serverseitig gespeichert, Verfallszeit 30 Tage, bei Abmeldung serverseitig gelöscht.
7. **Upload-Pfade als UUID**, nie ratbar, nie vom Dateinamen abgeleitet. Uploads liegen **außerhalb** des öffentlich ausgelieferten Verzeichnisses und werden nur über eine autorisierte Route ausgeliefert.
8. **Keine Secrets im Repository.** Nur `.env.example` und Demo-Seeds.
9. **Audit-Log** bei: Angebotsannahme, Abnahme/Freigabe, Zahlungsstatuswechsel, Statuswechsel des Projekts, Rechteänderung, Löschung, Anmeldung, fehlgeschlagener Anmeldung.
10. **Admin-2FA ist Pflicht**, auch lokal nicht abschaltbar (im Entwicklungsmodus mit festem Testschlüssel, nicht deaktiviert).
11. **Sicherheitsheader:** `Content-Security-Policy` ohne `unsafe-inline` für Skripte, `X-Content-Type-Options: nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`, `X-Frame-Options: DENY`, HSTS in Produktion.
12. **Fehlerausgabe:** nie Stacktraces nach außen. Interne Kennung anzeigen, Details ins Log.
13. **Keine harte Löschung** fachlicher Datensätze. Statt `DELETE`: `archived_at` setzen.

---

## 4. Datenmodell

Alle Tabellen: `id UUID PRIMARY KEY`, `created_at TIMESTAMPTZ NOT NULL DEFAULT now()`, `updated_at TIMESTAMPTZ NOT NULL DEFAULT now()`. Fremdschlüssel mit `ON DELETE RESTRICT`.

### `organizations`
| Feld | Typ | Hinweis |
|---|---|---|
| `legal_name` | text, NOT NULL | rechtlicher Unternehmensname |
| `brand_name` | text | sichtbarer Name, falls abweichend |
| `street`, `postal_code`, `city` | text | Rechnungsanschrift |
| `vat_id` | text | optional |
| `contact_email` | citext, NOT NULL | |
| `contact_phone` | text | |
| `archived_at` | timestamptz | |

### `users`
| Feld | Typ | Hinweis |
|---|---|---|
| `organization_id` | uuid | **NULL bei Admins**, **NOT NULL bei Kunden** — als Datenbankbedingung erzwingen: `CHECK ((role = 'admin' AND organization_id IS NULL) OR (role = 'kunde' AND organization_id IS NOT NULL))`. Siehe §3 Regel 2a |
| `email` | citext, NOT NULL, UNIQUE | |
| `first_name`, `last_name` | text | |
| `role` | text, NOT NULL | `kunde` \| `admin` |
| `password_hash` | text | nur Admin (Argon2id) |
| `totp_secret_enc` | bytea | nur Admin, AES-256-GCM |
| `welcome_seen_at` | timestamptz | steuert die Willkommensstrecke |
| `last_login_at` | timestamptz | |
| `archived_at` | timestamptz | |

### `login_tokens`
`user_id` · `token_hash` (text, NOT NULL) · `expires_at` · `used_at` · `requested_ip` (inet)

### `sessions`
`user_id` · `token_hash` · `expires_at` · `user_agent` · `ip` (inet)

### `leads`
Die Anfragen aus dem Bedarfsscheck der öffentlichen Website (§4b).

| Feld | Typ | Hinweis |
|---|---|---|
| `submission_id` | uuid, NOT NULL, **UNIQUE** | von der Website erzeugt — verhindert Doppeleinreichung (§4b.3) |
| `submitted_at` | timestamptz, NOT NULL | Zeitpunkt laut Website |
| `payload` | jsonb, NOT NULL | vollständige Antworten, unverändert wie gesendet |
| `first_name`, `last_name` | text, NOT NULL | |
| `company` | text, NOT NULL | |
| `email` | citext, NOT NULL | kleingeschrieben gespeichert |
| `phone` | text | |
| `preferred_contact` | text, NOT NULL | `email` \| `portal` |
| `recommended_package` | text | vom Regelwerk der Website vorgeschlagen |
| `flag` | text, NOT NULL, default `standard` | `standard` \| `gelb` \| `orange` \| `rot` |
| `status` | text, NOT NULL | `neu` \| `in_pruefung` \| `angebot_erstellt` \| `abgelehnt` |
| `b2b_confirmed` | boolean, NOT NULL | muss `true` sein, sonst wird nicht gespeichert |
| `privacy_confirmed` | boolean, NOT NULL | dito |
| `source_ip` | inet | **wird nach 30 Tagen geleert**, s. §4b.4 |
| `delete_after` | date, NOT NULL | Eingang + 6 Monate; entfällt bei Umwandlung |
| `converted_organization_id` | uuid | gesetzt bei Umwandlung |
| `admin_note` | text | |

### `projects`
| Feld | Typ | Hinweis |
|---|---|---|
| `organization_id` | uuid, NOT NULL | |
| `title` | text, NOT NULL | z. B. „Firmenwebsite Musterbau" |
| `package` | text, NOT NULL | `start` \| `wachstum` \| `platzhirsch` \| `sonderprojekt` |
| `included_feedback_rounds` | integer, NOT NULL | aus dem Paket vorbelegt: Start **1**, Wachstum **2**, Platzhirsch **2**, Sonderprojekt nach Angebot |
| `protection_level` | text | `s` \| `m` \| `l` — aus dem Paket abgeleitet |
| `protection_started_on` | date | **Betriebsbeginn**, s. §5.7 |
| `protection_min_term_until` | date | Betriebsbeginn + 12 Monate |
| `status` | text, NOT NULL | siehe §5.1 |
| `next_step_text` | text | vom Admin gesetzt, überschreibt die Ableitung |
| `next_step_url` | text | optionaler Sprungziel-Pfad im Portal |
| `preview_url` | text | Vorschau-Link |
| `preview_published_at` | timestamptz | |
| `live_url` | text | |
| `launched_at` | timestamptz | |
| `archived_at` | timestamptz | |

### `offers`
Ein angenommenes Angebot ist die **vertragliche Grundlage**. Es muss deshalb alles enthalten,
was später strittig werden kann — nicht nur den Preis.

| Feld | Typ | Hinweis |
|---|---|---|
| `project_id` | uuid, NOT NULL | |
| `number` | text, UNIQUE, NOT NULL | Format §4a |
| `status` | text, NOT NULL | §5.2 |
| `package` | text, NOT NULL | `start` \| `wachstum` \| `platzhirsch` \| `sonderprojekt` |
| `summary` | text, NOT NULL | Ausgangslage und Ziel in Kundensprache |
| `sitemap` | text, NOT NULL | die geplanten Seiten, eine je Zeile |
| `inclusions` | text, NOT NULL | was enthalten ist |
| `exclusions` | text, NOT NULL | was **nicht** enthalten ist — Pflichtfeld, nie leer |
| `scope_pages` | integer | Umfangsgrenze Seiten (Start 1, Wachstum 8, Platzhirsch 16) |
| `scope_words` | integer | Umfangsgrenze Wörter (~1.200 / ~3.500 / ~6.500) |
| `included_feedback_rounds` | integer, NOT NULL | Start 1, Wachstum 2, Platzhirsch 2 — wird bei Annahme nach `projects` übernommen |
| `delivery_days_min` | integer, NOT NULL | Lieferkorridor Untergrenze in **Werktagen** |
| `delivery_days_max` | integer, NOT NULL | Lieferkorridor Obergrenze in Werktagen |
| `delivery_start_condition` | text, NOT NULL | Fester Text §4c — wann der Korridor zu laufen beginnt |
| `one_time_net_cents` | integer, NOT NULL | |
| `protection_level` | text, NOT NULL | `s` \| `m` \| `l` |
| `protection_monthly_net_cents` | integer, NOT NULL | |
| `protection_min_term_months` | integer, NOT NULL | Stufe 0 immer **12** |
| `first_year_net_cents` | integer, NOT NULL | **abgeleitet**, s. Prüfregel unten |
| `payment_plan` | text, NOT NULL | `50_50` \| `40_30_30` \| `custom` |
| `payment_plan_custom` | text | nur bei `custom` — Klartext der Raten, s. §4a |
| `rights_text` | text, NOT NULL | Fester Text §4c — Rechte und Export nach vollständiger Zahlung |
| `domain_text` | text, NOT NULL | Fester Text §4c — Domain- und E-Mail-Vorgehen |
| `valid_until` | date, NOT NULL | |
| `sent_at` | timestamptz | |
| `accepted_at` | timestamptz | |
| `accepted_by_user_id` | uuid | |
| `accepted_ip` | inet | |
| `accepted_name` | text | selbst getippter Name des Annehmenden |

> Beträge **immer in Cent als integer**. Nie Fließkomma für Geld.

**Prüfregel Erstjahreswert (Pflicht, im Programm geprüft — nicht nur in der Anzeige):**

```
first_year_net_cents = one_time_net_cents + (12 × protection_monthly_net_cents)
```

Weicht der eingegebene Wert ab, wird das Angebot **nicht gespeichert**. Fehlermeldung im Admin:
> Der Erstjahreswert passt nicht zu Einmalpreis und Betriebspauschale. Erwartet: {berechnet} €. Bitte prüfen.

Diese Regel gilt auch für Sonderprojekte. Ein abweichender Erstjahreswert ist in Stufe 0 nicht vorgesehen.

**Prüfregel Zahlungsplan:** `payment_plan = custom` ist **nur** bei `package = sonderprojekt`
zulässig. Bei allen anderen Paketen lehnt das Programm `custom` ab. Ist `custom` gesetzt, muss
`payment_plan_custom` gefüllt sein; ist es nicht `custom`, muss das Feld leer sein.

**Prüfregel Annahme:** Ein Angebot ist nur annehmbar, wenn alle NOT-NULL-Felder gefüllt sind und
`valid_until` nicht in der Vergangenheit liegt. Sonst zeigt das Portal den Hinweis aus §8.3.

### `invoices`
`project_id` · `number` (text, UNIQUE) · `milestone` (text: `anzahlung` \| `zwischenrate` \| `schlussrate` \| `betrieb`) · `status` (§5.3) · `net_cents` · `vat_cents` · `gross_cents` · `due_date` (date) · `mollie_payment_url` (text) · `paid_at` · `marked_paid_by_user_id` · `note` (text)

### `tasks`
`project_id` · `title` (text) · `description` (text) · `why_needed` (text, die Zeile „Warum wir das brauchen") · `kind` (text: `bestaetigung` \| `angabe` \| `upload` \| `freigabe`) · `status` (§5.4) · `sort_order` (integer) · `answer_text` (text) · `completed_at` · `completed_by_user_id` · `required` (boolean, default true)

### `task_files`
`task_id` · `organization_id` (redundant, für die Mandantenprüfung) · `original_name` (text) · `stored_name` (text, UUID) · `mime_type` (text) · `size_bytes` (bigint) · `rights_confirmed` (boolean) · `uploaded_by_user_id`

### `feedback_rounds`
Bildet die **enthaltenen Korrekturrunden** ab — der zentrale Scope-Schutz des Geschäftsmodells.

| Feld | Typ | Hinweis |
|---|---|---|
| `project_id` | uuid, NOT NULL | |
| `number` | integer, NOT NULL | 1, 2, … — eindeutig je Projekt |
| `status` | text, NOT NULL | `offen` \| `eingereicht` \| `bearbeitet` |
| `opened_at` | timestamptz | |
| `submitted_at` | timestamptz | Kunde hat gebündelt eingereicht |
| `completed_at` | timestamptz | SARTU hat eingearbeitet |
| `included` | boolean, NOT NULL, default true | `false` = zusätzliche, kostenpflichtige Runde |

### `feedback_items`
`project_id` · `feedback_round_id` (uuid, NOT NULL) · `body` (text, NOT NULL) · `page_hint` (text) · `status` (§5.5) · `created_by_user_id` · `answered_at` · `answer_text` (text)

### `approvals`
Protokolliert **ausschließlich Erklärungen des Kunden**, die später beweisbar sein müssen.
Interne SARTU-Schritte gehören **nicht** hierher, sondern ins Audit-Log.

`project_id` · `kind` (text: `inhalte` \| `abnahme`) · `granted_at` · `granted_by_user_id` · `granted_ip` (inet) · `granted_name` (text) · `note` (text)

| Wert | Entsteht durch | Wirkung |
|---|---|---|
| `inhalte` | Abschluss der Aufgabe `Fakten und Umfang final freigeben` (§9.3 Nr. 13, Art `freigabe`) | Produktion darf starten; ab hier läuft der Lieferkorridor (§4c) |
| `abnahme` | Abnahmeblock in §8.4 | Schlussrechnung und Startvorbereitung |

**Kein `launch`-Eintrag.** Der Onlinegang ist keine Kundenerklärung, sondern eine SARTU-Handlung.
Er wird über `projects.launched_at`, `projects.live_url` und ein Audit-Ereignis festgehalten (§5.7).

**Kein `vorschau`-Eintrag.** Die Vorschau wird nicht freigegeben, sondern kommentiert — dafür gibt
es `feedback_rounds` (§5.6a). Die einzige verbindliche Freigabe der fertigen Website ist `abnahme`.

Beide Erklärungen erfordern **Ankreuzen und selbst getippten Namen**; beide erzeugen zusätzlich ein
Audit-Ereignis. Eine Erklärung ist **einmalig** — ein zweiter Versuch zeigt nur den vorhandenen Eintrag.

### `domain_status`
`project_id` (UNIQUE) · `desired_name` (text) · `confirmed_name` (text) · `owner_confirmed` (boolean) · `state` (text: `offen` \| `vorschlaege_bereit` \| `bestaetigt` \| `registriert` \| `verbunden` \| `live`) · `email_note` (text) · `admin_note` (text)

### `business_hours`
`organization_id` · `weekday` (integer 0–6) · `closed` (boolean) · `open_time` (time) · `close_time` (time) · `note` (text) · `pending_publish` (boolean) — Änderungen gelten erst nach Rebuild als veröffentlicht

### `business_hours_exceptions`
`organization_id` · `date` (date) · `closed` (boolean) · `open_time` · `close_time` · `label` (text, z. B. „Betriebsurlaub")

### `support_messages`
`organization_id` · `project_id` (nullable) · `body` (text) · `created_by_user_id` · `answered_at` · `answer_text`

### `audit_events`
`actor_user_id` (nullable) · `organization_id` (nullable) · `action` (text) · `entity_type` (text) · `entity_id` (uuid) · `old_value` (text) · `new_value` (text) · `reason` (text) · `detail` (jsonb) · `ip` (inet)

**Bei jedem Statuswechsel Pflicht:** `old_value`, `new_value` und der handelnde Benutzer. Bei
Wechseln, die Geld oder Fristen betreffen, zusätzlich `reason` als **Pflichtfeld** — siehe §12.
Audit-Einträge werden **nie** geändert und **nie** gelöscht.

**Pflichtindizes:** auf allen `organization_id` und `project_id`, auf `users.email`, `login_tokens.token_hash`, `sessions.token_hash`, `audit_events.created_at`.

---

## 4a. Formate und Konventionen (verbindlich)

Damit hier nichts erraten wird:

| Thema | Festlegung |
|---|---|
| **Sprache** | `<html lang="de">`, Oberfläche durchgehend deutsch, keine Umschaltung |
| **Zeitzone** | **Europe/Berlin** für jede Anzeige. Speicherung immer in UTC (`timestamptz`) |
| **Datum** | `TT.MM.JJJJ` (z. B. `04.08.2026`). Nie ISO in der Oberfläche |
| **Datum mit Uhrzeit** | `TT.MM.JJJJ, HH:MM Uhr` |
| **Wochentage** | ausgeschrieben: Montag … Sonntag; Woche beginnt Montag |
| **Geldbeträge** | Speicherung als **integer in Cent**. Anzeige deutsch: `7.900,00 €` (Punkt als Tausendertrenner, Komma als Dezimaltrenner, Leerzeichen vor €) |
| **Umsatzsteuer** | **19 %** Regelsatz. `vat_cents = round(net_cents * 0.19)`, `gross_cents = net_cents + vat_cents`. Der Satz liegt als Konstante im Code, nicht verstreut |
| **Preisangaben** | Öffentliche Beträge sind **netto**. Jede Preisanzeige trägt den Zusatz `zzgl. gesetzlicher Umsatzsteuer`, außer es steht ausdrücklich „brutto" daneben |
| **Prozentwerte Zahlungsplan** | fest: `50_50` = 50/50, `40_30_30` = 40/30/30. **Ausnahme Sonderprojekt:** `custom` mit Klartextraten (s. u.) |
| **Zahlungsziel** | **10 Kalendertage** ab Rechnungsdatum, als Vorbelegung für `due_date` |
| **Dateigrößen** | `12,4 MB` (deutsch, eine Nachkommastelle) |
| **Nummernkreise** | Angebot `AN-JJJJ-NNN`, Rechnung `RE-JJJJ-NNN`, je Jahr fortlaufend. In Stufe 0 vom Admin eingegeben, Eindeutigkeit erzwingt die Datenbank |
| **Telefonnummern** | Anzeige wie eingegeben, keine automatische Umformatierung |
| **Leere Werte** | nie `null`, `–` oder `undefined` anzeigen. Stattdessen: `Noch nicht hinterlegt` |

**Zahlungsplan `custom` (nur Sonderprojekt):** Der Admin trägt die Raten als **Klartext** in
`payment_plan_custom` ein, eine Rate je Zeile, Format `Bezeichnung | Betrag netto | Fälligkeit`.
Beispiel:

```
Anzahlung bei Auftragsbestätigung | 5.000,00 € | sofort
Zwischenrate bei Vorschau | 5.000,00 € | bei Freigabe der Vorschau
Schlussrate bei Veröffentlichung | 2.500,00 € | bei Veröffentlichung
```

Das Portal rechnet daraus **nichts** ab. Es zeigt den Text im Angebot an, und der Admin legt die
Rechnungen manuell an. **Prüfregel:** Die Summe der eingetragenen Beträge muss
`one_time_net_cents` entsprechen; sonst Fehlermeldung
> Die Summe der Raten ergibt {Summe} € und passt nicht zum Einmalpreis von {Einmalpreis} €.

**Projekte je Organisation:** In Stufe 0 hat eine Organisation **genau ein aktives Projekt**. Mehrere Projekte sind technisch möglich (Fremdschlüssel), die Oberfläche zeigt aber immer das jüngste nicht archivierte. Existieren mehrere, erscheint im Adminbereich ein Hinweis — im Kundenportal keine Projektauswahl.

**Rundung:** kaufmännisch, immer auf ganze Cent.

---

## 4b. Schnittstelle zur öffentlichen Website — Anfrageeingang („Formular-Endpunkte")

> **Ohne diesen Abschnitt bricht der Gesamtprozess.** Die Website erzeugt Anfragen, das Portal muss
> sie annehmen — sonst landet der Bedarfsscheck im Nichts.

### 4b.1 Wer ruft wen auf — und warum kein Browser-Geheimnis existiert

```
Browser des Interessenten
        │  normales Formular-POST an die eigene Website-Domain
        ▼
Formularannahme der Website  (kleiner Serverteil, gleiche Domain)
        │  POST /api/anfragen   +  Header X-Sartu-Token
        ▼
SARTU-Portal  →  legt genau einen Datensatz in `leads` an
```

**Eiserne Regel: `INTAKE_TOKEN` darf niemals im Browser ankommen.** Alles, was an den Browser
ausgeliefert wird — HTML, JavaScript, JSON, Netzwerkantworten — ist öffentlich lesbar. Der Token
lebt ausschließlich auf dem Server der Website.

Daraus folgt:
- Der Browser sendet **nie** direkt an `/api/anfragen`
- Der Browser sieht den Header `X-Sartu-Token` **nie**
- `/api/anfragen` ist **kein** öffentliches Formularziel, sondern eine **Server-zu-Server-Schnittstelle**
- Wäre die Website rein statisch ohne jeden Serverteil, wäre dieser Endpunkt **nicht** sicher benutzbar. Deshalb ist die Formularannahme der Website ausdrücklich Teil des Website-Auftrags

**Was der Token leistet und was nicht:** Er verhindert, dass Fremde ohne Weiteres Datensätze in die
Anfrageliste schreiben. Er ist **keine** Benutzerauthentifizierung und ersetzt weder Prüfung des
Inhalts noch Rate-Limit noch Spamabwehr. Die Prüfungen unten gelten unabhängig davon.

### 4b.2 Endpunkt `POST /api/anfragen`

| Punkt | Festlegung |
|---|---|
| Methode und Pfad | `POST /api/anfragen` |
| Inhaltstyp | `application/json; charset=utf-8` |
| Erreichbarkeit | **einziger** Pfad ohne Session. Alles andere unter `/api/` existiert nicht |
| Absicherung | Header `X-Sartu-Token` = `INTAKE_TOKEN`, Vergleich **zeitkonstant** (kein `==` auf Zeichenketten) |
| Rate-Limit | **10 Anfragen je Absender-IP und Stunde**, zusätzlich **60 je Stunde gesamt** als Notbremse |
| Größe | Rumpf maximal **64 KB**, sonst `413` |
| Zeitüberschreitung | 10 Sekunden |

**Nutzdaten — vollständiges Schema.** Unbekannte Felder werden **mitgespeichert**, nicht abgewiesen
(die Website darf den Bedarfsscheck erweitern, ohne dass das Portal bricht).

| Feld | Typ | Pflicht | Prüfung |
|---|---|---|---|
| `submission_id` | Zeichenkette, UUID | ja | **Doppeleinreichung**, s. 4b.3 |
| `submitted_at` | Zeitstempel ISO 8601 | ja | darf nicht mehr als 24 h in der Vergangenheit oder 5 min in der Zukunft liegen |
| `first_name` | Zeichenkette ≤ 100 | ja | nicht leer nach Trimmen |
| `last_name` | Zeichenkette ≤ 100 | ja | nicht leer nach Trimmen |
| `company` | Zeichenkette ≤ 200 | ja | nicht leer nach Trimmen |
| `email` | Zeichenkette ≤ 254 | ja | Formatprüfung, wird kleingeschrieben gespeichert |
| `phone` | Zeichenkette ≤ 50 | nein | wie eingegeben speichern |
| `preferred_contact` | `email` \| `portal` | ja | nur diese zwei Werte |
| `b2b_confirmed` | Wahrheitswert | ja | muss `true` sein, sonst `422` |
| `privacy_confirmed` | Wahrheitswert | ja | muss `true` sein, sonst `422` |
| `recommended_package` | `start` \| `wachstum` \| `platzhirsch` \| `sonderprojekt` \| `unklar` | nein | |
| `flag` | `standard` \| `gelb` \| `orange` \| `rot` | nein | Vorgabe `standard` |
| `answers` | Objekt | ja | Frage-Antwort-Paare des Bedarfsschecks, unverändert nach `payload` |
| `form_started_at` | Zeitstempel | nein | Zeitregel, s. 4b.3 |
| `hp_website` | Zeichenkette | nein | **Honigtopf** — gefüllt ⇒ verwerfen |

**Antworten:**

| Lage | Status | Rumpf | Nebenwirkung |
|---|---|---|---|
| Angenommen | `201` | `{"ok":true}` | `lead` angelegt, E-Mail an SARTU, Audit-Ereignis |
| Bereits bekannte `submission_id` | `200` | `{"ok":true}` | **keine** — bewusst gleiche Erfolgsantwort |
| Honigtopf gefüllt oder Zeitregel verletzt | `201` | `{"ok":true}` | **keine** — der Absender merkt nichts |
| Token fehlt oder falsch | `401` | `{"ok":false}` | Zählwerk, kein Grund genannt |
| Schema- oder Pflichtfeldfehler | `422` | `{"ok":false,"fields":["email"]}` | nur **Feldnamen**, nie Werte |
| Rumpf zu groß | `413` | `{"ok":false}` | |
| Rate-Limit erreicht | `429` | `{"ok":false}` | Header `Retry-After` |
| Serverfehler | `500` | `{"ok":false}` | Interne Kennung ins Log, **nicht** in die Antwort |

**Niemals** zurückgeben: interne Kennungen, Datenbankfehler, Stapelüberwachung, ob eine E-Mail-Adresse
bereits bekannt ist.

### 4b.3 Spamabwehr und Doppeleinreichung

1. **Honigtopf** `hp_website` — für Menschen unsichtbar, aber **nicht** über `display:none` allein
   (Vorlesesoftware muss es überspringen: `aria-hidden="true"` **und** `tabindex="-1"`). Gefüllt ⇒
   stillschweigend verwerfen mit Erfolgsantwort
2. **Zeitregel** — liegt zwischen `form_started_at` und `submitted_at` weniger als **3 Sekunden**,
   stillschweigend verwerfen. Menschen brauchen für den Bedarfsscheck Minuten
3. **Doppeleinreichung** — `submission_id` ist in `leads` **eindeutig**. Zweiter Aufruf mit derselben
   Kennung liefert `200` und ändert nichts. Das deckt Doppelklick, Neuladen und Wiederholversuche der
   Website nach Zeitüberschreitung ab
4. **Kein Rätselbild und kein Fremddienst in Stufe 0.** Turnstile, hCaptcha und Vergleichbares sind
   Fremdverbindungen mit eigener Datenschutzfolge. Erst nachrüsten, wenn Spam **messbar** auftritt,
   und dann mit dokumentierter Rechtsgrundlage

### 4b.4 Datenschutz und Aufbewahrung

- **Datensparsamkeit:** gespeichert wird ausschließlich, was gesendet wurde. Das Portal reichert
  **nichts** an — kein Standortnachschlagen, keine Anreicherung aus Fremdquellen, keine Bewertung
- **`source_ip`** wird gespeichert, weil sie für Missbrauchsabwehr und als Nachweis der Einwilligung
  gebraucht wird. Sie wird **nach 30 Tagen geleert** (Feld auf `NULL`), der Rest des Datensatzes bleibt
- **Protokolle:** Anfragen werden protokolliert mit Zeitpunkt, Status, gekürzter IP (letztes Oktett
  entfernt) und `submission_id`. **Nie** mit Name, E-Mail, Telefonnummer oder Antworttexten
- **Löschfrist:** abgelehnte Anfragen werden **nach 6 Monaten** gelöscht, umgewandelte bleiben als
  Teil der Kundenakte. Der Adminbereich zeigt bei jeder Anfrage das Löschdatum
- **Auskunft und Löschung auf Verlangen:** `/admin/anfragen` hat je Datensatz die Aktionen
  `Datensatz exportieren` (JSON, alles was gespeichert ist) und `Endgültig löschen` (echtes `DELETE`,
  **Ausnahme** von der Archivierungsregel in §3, Regel 13, weil Betroffenenrechte vorgehen — der Löschvorgang
  selbst wird im Audit-Log vermerkt, ohne die gelöschten Inhalte)
- Die Einwilligung selbst kommt von der Website. Das Portal **prüft nur**, dass
  `privacy_confirmed = true` ankommt, und **speichert**, wann sie erklärt wurde

### 4b.5 Adminbereich `/admin/anfragen`

**Das ist bewusst eine Liste, kein Vertriebssystem.** Zur Abgrenzung siehe §0.3.

Liste: Eingangsdatum · Firma · Name · empfohlene Lösung · Ampelkennzeichen · Status · Löschdatum.
Filter nach Status. Sortierung nach Eingang, neueste zuerst.

Detailansicht: **alle** Antworten in Klartext als Frage → Antwort, nicht als rohes JSON.

Aktionen:
- `In Kunde und Projekt umwandeln` → legt `organizations`, `users` (Rolle `kunde`) und `projects` an,
  setzt `converted_organization_id` und `status = angebot_erstellt`, verschickt die Einladungs-E-Mail.
  **Bestätigungsdialog vorher**, weil dabei ein Zugang entsteht
- `Als abgelehnt markieren` mit Pflichtnotiz
- `Notiz speichern`
- `Datensatz exportieren` · `Endgültig löschen` (§4b.4)

**Regel:** Anfrage ≠ Kunde. Ein Zugang entsteht ausschließlich durch diesen bewussten Klick — nie
automatisch, nie durch den Endpunkt.

### 4b.6 Kontaktformular der Website

Das allgemeine Kontaktformular läuft **nicht** über diesen Endpunkt. Es versendet ausschließlich eine
E-Mail an SARTU und erzeugt keinen Datensatz im Portal.

---

## 4c. Feste Angebotstexte (wörtlich zu übernehmen)

Diese drei Texte stehen in **jedem** Angebot. Sie werden beim Anlegen eines Angebots vorbelegt,
sind vom Admin editierbar, dürfen aber nicht leer bleiben. Formulierungen nicht erfinden.

### `delivery_start_condition` — Vorbelegung

> Der genannte Zeitraum beginnt, sobald alle Aufgaben in Ihrem Portal erledigt sind: bestätigte
> Fakten, vollständige Inhalte, freigegebene Rechtstexte und geklärte Bild- und Nutzungsrechte.
> Bis dahin läuft die Zeit nicht. Fehlt Ihre Mitwirkung länger als 14 Tage, dürfen wir das Projekt
> nach vorheriger Ankündigung pausieren; bereits abgeschlossene Meilensteine bleiben fällig.

Die Werte für `delivery_days_min` / `delivery_days_max` sind je Paket vorbelegt:
**Start 7–10**, **Wachstum 10–15**, **Platzhirsch 15–25** Werktage. Sonderprojekt: manuell.

### `rights_text` — Vorbelegung

> Nach vollständiger Zahlung erhalten Sie die Nutzungsrechte am gelieferten Website-Stand, an den
> von uns erstellten Texten und am für Sie gestalteten Erscheinungsbild. Ihre Domain gehört Ihnen,
> auf Ihren Namen registriert. Auf Wunsch stellen wir Ihnen den vollständigen Stand Ihrer Website
> als Export bereit, mit einer Anleitung, wie er ohne uns weiterbetrieben werden kann.
> Nicht übertragen werden allgemeine Bausteine, die wir projektübergreifend einsetzen, sowie
> Rechte Dritter (z. B. Schriften oder Bilder), für die die jeweilige Lizenz gilt.

### `domain_text` — Vorbelegung

> Ihre Domain wird auf **Ihren Namen** registriert — Sie sind Inhaber, nicht wir. Wir übernehmen
> Prüfung, Registrierung, Einrichtung und Verbindung. Die Domaingebühr ist in der Betriebspauschale
> enthalten, solange der Vertrag läuft. Endet der Vertrag, übertragen wir die Domain kostenfrei an
> Sie oder an einen Anbieter Ihrer Wahl; ab dann tragen Sie die Gebühr selbst.
> E-Mail-Postfächer sind nicht enthalten. Auf Wunsch richten wir die nötigen Einträge ein, damit ein
> Postfach Ihres Anbieters unter Ihrer Domain funktioniert.

> **Hinweis an die ausführende KI:** Diese Texte sind Geschäftsaussagen, keine Rechtstexte.
> AGB, Widerruf, Datenschutz und Auftragsverarbeitung stehen **nicht** hier und werden **nicht**
> erfunden (§15).

---

## 5. Statuslogik

**Grundregel:** Intern gibt es technische Werte, dem Kunden wird **immer Klartext** gezeigt. Interne Codes erscheinen nie in der Kundenoberfläche.

### 5.1 `projects.status`

| Intern | Kundentext | Bedeutung |
|---|---|---|
| `angebot_offen` | **Angebot liegt vor** | Kunde muss annehmen |
| `angebot_angenommen` | **Angebot angenommen** | wartet auf erste Zahlung |
| `zahlung_offen` | **Zahlung offen** | Anzahlung ausstehend |
| `briefing` | **Ihre Angaben werden gebraucht** | Aufgaben offen |
| `produktion` | **Wir bauen Ihre Website** | keine Kundenaktion |
| `vorschau` | **Vorschau bereit** | Feedback oder Freigabe |
| `korrektur` | **Wir arbeiten Ihr Feedback ein** | keine Kundenaktion |
| `abnahme` | **Ihre Abnahme fehlt** | Kunde nimmt ab |
| `launch_vorbereitung` | **Wir bereiten den Start vor** | keine Kundenaktion |
| `live` | **Online** | Betrieb läuft |
| `pausiert` | **Pausiert** | Grund wird angezeigt |

Zulässige Übergänge setzt **nur der Admin**. Rücksprünge sind erlaubt (z. B. `abnahme → korrektur`) und werden im Audit-Log festgehalten.

### 5.2 `offers.status`
`entwurf` (unsichtbar für Kunden) → `gesendet` → `angenommen` \| `abgelaufen` \| `zurueckgezogen`
Kundentexte: **Angebot liegt vor** · **Angenommen am {Datum}** · **Abgelaufen** · **Zurückgezogen**

### 5.3 `invoices.status`
`entwurf` (unsichtbar) → `gesendet` → `bezahlt` \| `ueberfaellig` \| `storniert`
Kundentexte: **Offen — zahlbar bis {Datum}** · **Bezahlt am {Datum}** · **Überfällig seit {Datum}** · **Storniert**
`ueberfaellig` wird täglich automatisch gesetzt, wenn `status = gesendet` und `due_date < heute`.

### 5.4 `tasks.status`
`offen` → `erledigt`
Kundentexte: **Offen** · **Erledigt**

### 5.5 `feedback_items.status`
`offen` → `beantwortet` → `erledigt`
Kundentexte: **Eingereicht** · **Beantwortet** · **Umgesetzt**

### 5.6 Ableitung „nächster Schritt"

Ist `next_step_text` gesetzt, wird dieser angezeigt. Sonst wird nach Projektstatus abgeleitet:

| Status | Angezeigter nächster Schritt | Ziel |
|---|---|---|
| `angebot_offen` | „Angebot ansehen und annehmen" | `/angebot` |
| `angebot_angenommen`, `zahlung_offen` | „Anzahlung bezahlen" | `/rechnungen` |
| `briefing` | „{n} offene Aufgaben bearbeiten" | `/aufgaben` |
| `produktion`, `korrektur`, `launch_vorbereitung` | „Nichts zu tun — wir melden uns" | – |
| `vorschau` | „Vorschau ansehen und Rückmeldung geben" | `/vorschau` |
| `abnahme` | „Website abnehmen" | `/vorschau` |
| `live` | „Alles erledigt" | – |
| `pausiert` | „Projekt pausiert — bitte Nachricht lesen" | `/hilfe` |

---

### 5.6a Korrekturrunden — Zählung und Grenze

Die enthaltenen Runden sind eine **harte Scope-Grenze**, keine Empfehlung. Das Portal muss sie sichtbar machen, sonst wird Feedback endlos.

**Ablauf:**
1. Beim Bereitstellen einer Vorschau öffnet der Admin eine Runde: neuer Satz in `feedback_rounds` mit `status = offen`, `number` fortlaufend
2. Der Kunde sammelt beliebig viele Rückmeldungen **innerhalb** dieser Runde
3. Der Kunde reicht **gebündelt** ein → `status = eingereicht`, `submitted_at`. Danach sind in dieser Runde **keine** weiteren Einträge möglich
4. SARTU arbeitet ein → `status = bearbeitet`, neue Vorschau, nächste Runde

**Anzeige im Kundenportal** (auf `/vorschau`, immer sichtbar, sobald eine Runde offen ist):
`Korrekturrunde {number} von {included_feedback_rounds}`

**Wenn alle enthaltenen Runden verbraucht sind** und der Admin eine weitere öffnet (`included = false`), zeigt das Portal vor dem Einreichen:
> **Diese Korrekturrunde ist im Festpreis nicht mehr enthalten.**
> Ihre vereinbarten {n} Korrekturrunden sind bereits genutzt. Wir schauen uns Ihre Rückmeldung trotzdem an und melden uns, bevor Aufwand entsteht — Sie gehen damit keine Kosten ein.

**Regel:** Das Portal **blockiert nichts** und berechnet nichts automatisch. Es macht den Stand nur sichtbar. Über zusätzlichen Aufwand entscheidet immer ein Mensch.

**Fehlermeldung** beim Versuch, in eine eingereichte Runde zu schreiben:
`Diese Korrekturrunde wurde bereits eingereicht. Wir arbeiten sie gerade ein und melden uns, sobald die neue Vorschau bereitsteht.`

### 5.7 Betriebsbeginn und Mindestlaufzeit

Der Betrieb („Rundum-Schutz") beginnt regulär mit dem **produktiven Betrieb der Website**.

- Beim Statuswechsel auf `live` setzt der Admin `protection_started_on` (Vorbelegung: heutiges Datum) und das System `protection_min_term_until = protection_started_on + 12 Monate`
- Beides wird dem Kunden auf `/rechnungen` angezeigt: `Betrieb seit {Datum} · Mindestlaufzeit bis {Datum}`
- **Sonderfall:** Ist die Website abgenommen und betriebsfertig bereitgestellt und **nur der Kunde** verzögert den Onlinegang, kann der Admin `protection_started_on` manuell auf ein früheres Datum setzen. Das Portal weist dabei hin:
  > Diese Regel muss vorher schriftlich angekündigt worden sein und mit der vertraglichen Formulierung übereinstimmen.
- Kündigungen, Verlängerungen und Lastschrift sind **Stufe 2**. In Stufe 0 erzeugt der Admin die monatlichen Betriebsrechnungen manuell.

## 6. Anmeldung ohne Passwort

### 6.1 Ablauf

1. `/login` — Eingabe der E-Mail-Adresse
2. System erzeugt Token, speichert **nur den Hash**, versendet Link `{BASE_URL}/login/{token}`
3. **Immer dieselbe Bestätigungsseite anzeigen**, unabhängig davon, ob die E-Mail existiert (keine Kontoauskunft)
4. Klick auf den Link → Token prüfen (gültig, nicht abgelaufen, nicht benutzt) → Session anlegen → Token als benutzt markieren
5. Erster Login → Willkommensstrecke (§7). Sonst → Cockpit

### 6.2 Texte

**`/login`**
- H1: `Anmelden`
- Text: `Geben Sie Ihre E-Mail-Adresse ein. Wir schicken Ihnen einen Anmeldelink — ein Passwort brauchen Sie nicht.`
- Feldlabel: `E-Mail-Adresse`
- Button: `Anmeldelink senden`
- Fehler leeres/ungültiges Feld: `Bitte geben Sie eine gültige E-Mail-Adresse an, z. B. name@firma.de`

**Bestätigungsseite**
- H1: `Prüfen Sie Ihr Postfach`
- Text: `Wenn ein Zugang zu dieser Adresse besteht, ist der Anmeldelink unterwegs. Er gilt 15 Minuten und lässt sich einmal verwenden.`
- Hinweis: `Nichts angekommen? Sehen Sie im Spam-Ordner nach oder fordern Sie den Link erneut an.`

**Ungültiger oder abgelaufener Link**
- H1: `Dieser Link gilt nicht mehr`
- Text: `Anmeldelinks laufen nach 15 Minuten ab und funktionieren nur einmal. Fordern Sie einfach einen neuen an.`
- Button: `Neuen Link anfordern`

**Rate-Limit erreicht**
- `Zu viele Anfragen. Bitte versuchen Sie es in einer Stunde erneut oder schreiben Sie uns an {MAIL_FROM}.`

**Abmelden:** Button `Abmelden` in der Kopfzeile → Session serverseitig löschen → `/login` mit Hinweis `Sie sind abgemeldet.`

---

## 7. Willkommensstrecke beim ersten Login

Erscheint **einmalig**, wenn `users.welcome_seen_at` leer ist. Überspringbar, jederzeit erneut aufrufbar unter Hilfe. **Genau drei Bildschirme** — nicht mehr, nicht weniger. Nach dem letzten Bildschirm oder bei „Überspringen": `welcome_seen_at` setzen.

**Regeln:**
- Eigene Seiten mit eigener URL (`/willkommen/1`, `/2`, `/3`), Navigation per `POST`/Link — **kein** JavaScript nötig
- Ein Sachverhalt je Bildschirm, mobil vollwertig, Buttons in Daumenreichweite
- Tastaturbedienung vollständig, Fokus sichtbar, `prefers-reduced-motion` respektiert
- **Kein Zwang:** Wer `Überspringen` klickt, kann alles trotzdem uneingeschränkt bedienen
- Keine Videos, keine Fortschrittsabzeichen, keine Gamification

**Bildschirm 1**
> # Willkommen bei SARTU, {Vorname}.
> Das ist Ihr Projektportal. Hier läuft alles zu Ihrer Website an einem Ort: Angebot, Zahlung, offene Aufgaben, Vorschau und später kleine Änderungen.
> Keine E-Mail-Suche, keine verlorenen Anhänge, kein Rätselraten, wie weit das Projekt ist.

Buttons: `Weiter` · Textlink `Überspringen`

**Bildschirm 2** — zwei Spalten (mobil untereinander)

| **Das machen Sie hier** | **Das müssen Sie nicht** |
|---|---|
| Angebot ansehen und annehmen | Technik verstehen |
| Rechnungen bezahlen | Seiten selbst bauen |
| Fragen zu Ihrem Betrieb beantworten | Webtexte schreiben |
| Bilder und Unterlagen hochladen | Wissen, wie viele Seiten Sie brauchen |
| Vorschau ansehen und freigeben | Irgendetwas installieren |
| Später Öffnungszeiten ändern | Sich um Updates oder Sicherheit kümmern |

> Struktur, Design, Technik und die Suchmaschinen-Grundlage übernehmen wir. Sie liefern die Fakten aus Ihrem Betrieb — den Rest machen wir.

Buttons: `Weiter` · `Zurück`

**Bildschirm 3**
> # Sie sehen immer genau einen nächsten Schritt.
> Oben im Portal steht, was gerade von Ihnen gebraucht wird. Mehr müssen Sie nicht im Blick behalten — wir melden uns, wenn etwas ansteht.
>
> **Anmelden ohne Passwort.** Sie bekommen jedes Mal einen Link per E-Mail. Es gibt kein Passwort, das verloren gehen kann.
>
> **Wenn etwas unklar ist**, nutzen Sie „Hilfe". Wir antworten schriftlich — meist am selben oder nächsten Werktag.

Button: `Portal öffnen`

> **Der Hinweis zum passwortlosen Anmelden ist Pflicht und darf nicht gekürzt werden.** Kunden erwarten ein Passwort; ohne Erklärung entsteht der Eindruck, etwas sei kaputt oder unsicher.

---

## 8. Kundenportal — Screen für Screen

**Navigation (feste Reihenfolge):** Übersicht · Angebot · Aufgaben · Vorschau · Rechnungen · Domain · Inhalte · Hilfe
Menüpunkte, für die es noch nichts gibt, werden **angezeigt und erklärt**, nicht ausgeblendet (siehe Leerzustände).

Jede Seite: `<h1>` als Seitentitel, Seitentitel im `<title>` als `{Seite} — SARTU-Portal`.

### 8.1 `/` Übersicht (Cockpit)

**H1:** `Übersicht`

**Block 1 — Nächster Schritt** (hervorgehoben, ganz oben):
- Kleines Label: `Nächster Schritt`
- Große Zeile: der abgeleitete oder gesetzte Text (§5.6)
- Button zum Ziel, sofern vorhanden
- Wenn nichts zu tun ist: `Nichts zu tun — wir melden uns, sobald etwas ansteht.`

**Block 2 — Projektstand:** Projekttitel, Paketname im Klartext (`Start` / `Wachstum` / `Platzhirsch` / `Sonderprojekt`), Kundentext des Status, Fortschrittsanzeige über die Stationen: `Angebot · Zahlung · Angaben · Produktion · Vorschau · Abnahme · Online`. Die aktuelle Station ist markiert.

**Block 3 — Offene Punkte:** höchstens drei Zeilen, jeweils mit Link: offene Aufgaben (`{n} offene Aufgaben`), offene Rechnung (`Rechnung {Nummer} — zahlbar bis {Datum}`), ausstehende Freigabe.

**Block 4 — Letzte Aktivität:** die letzten fünf für den Kunden relevanten Ereignisse mit Datum, in Klartext (`Angebot angenommen`, `Zahlung eingegangen`, `Vorschau bereitgestellt`, `Feedback eingereicht`, `Website online`).

**Leerzustand (kein Projekt):** `Sobald Ihr Angebot vorliegt, sehen Sie hier Ihren nächsten Schritt.`

### 8.2 `/angebot`

**H1:** `Ihr Angebot`

Zeigt **alle** Felder aus `offers` (§4), in dieser Reihenfolge:

1. Angebotsnummer · Gültig bis
2. Zusammenfassung des Ziels · empfohlene Lösung
3. Vorgesehene Seitenstruktur
4. Was enthalten ist · was **nicht** enthalten ist
5. **Umfangsgrenze:** `{scope_pages} Seiten, rund {scope_words} Wörter` — mit dem Satz: `Umfang darüber hinaus bieten wir Ihnen vorher getrennt an.`
6. **Korrekturrunden:** `{included_feedback_rounds} enthaltene Korrekturrunden` — mit dem Satz: `Eine Korrekturrunde bedeutet: Sie sammeln alle Anmerkungen und reichen sie gebündelt ein, wir arbeiten sie in einem Durchgang ein.`
7. **Zeitrahmen:** `Fertigstellung in {delivery_days_min}–{delivery_days_max} Werktagen` + der Text aus `delivery_start_condition`
8. Einmalpreis netto · Umsatzsteuer · Bruttobetrag
9. Monatlicher Betrieb netto · Mindestlaufzeit `{protection_min_term_months} Monate` · Erstjahreswert netto
10. Zahlungsplan im Klartext
11. **Rechte und Export:** Text aus `rights_text`
12. **Domain und E-Mail:** Text aus `domain_text`

Zahlungsplan-Texte:
- `50_50`: `50 % bei Auftrag, 50 % nach Abnahme vor dem Onlinegang. Zahlungsziel jeweils 10 Kalendertage.`
- `40_30_30`: `40 % bei Auftrag, 30 % nach der ersten Vorschau, 30 % nach Abnahme vor dem Onlinegang. Zahlungsziel jeweils 10 Kalendertage.`
- `custom`: Inhalt von `payment_plan_custom` als Tabelle (Bezeichnung · Betrag · Fälligkeit), darunter: `Zahlungsziel jeweils 10 Kalendertage.`

**Unvollständiges Angebot:** Fehlt eines der Pflichtfelder, ist der Annahmeblock **gesperrt** und es erscheint:
`Dieses Angebot ist noch nicht vollständig. Wir stellen es Ihnen in Kürze fertig bereit — Sie müssen nichts tun.`

**Annahmeblock** (nur bei `status = gesendet` und `valid_until >= heute`):
Vier Pflicht-Bestätigungen als Checkboxen:
1. `Die aufgeführten Ziele, Seitenbereiche und Funktionen entsprechen meinem Bedarf.`
2. `Nicht aufgeführte Sonderfunktionen wie Shop, Kundenlogin, Schnittstellen oder komplexe Buchung sind nicht beauftragt.`
3. `Neue Anforderungen werden vor Umsetzung getrennt angeboten.`
4. `Ich handle für mein Unternehmen und beauftrage SARTU kostenpflichtig zu den angezeigten Preisen, Laufzeiten und Zahlungsbedingungen.`

Feld: `Ihr Name` (Pflicht, wird als Annahmenachweis gespeichert)
Direkt über dem Button nochmals: Einmalpreis netto · USt. · Brutto · Betrieb monatlich netto · Mindestlaufzeit · Erstjahreswert netto · Zahlungsplan.
Button: **`Kostenpflichtig beauftragen`**

Fehlermeldungen:
- fehlende Checkbox: `Bitte bestätigen Sie alle vier Punkte, um fortzufahren.`
- fehlender Name: `Bitte geben Sie Ihren Namen an.`

Nach Annahme: `accepted_at`, `accepted_by_user_id`, `accepted_ip`, `accepted_name` speichern, Audit-Ereignis, Projektstatus auf `angebot_angenommen`, Bestätigungs-E-Mail an Kunde und Admin.
**Zugleich werden ins Projekt übernommen:** `included_feedback_rounds`, `protection_level` und `package`. Ab diesem Zeitpunkt ist das Angebot **schreibgeschützt** — auch für den Admin. Eine Änderung erfordert ein neues Angebot mit neuer Nummer.
Danach zeigt die Seite: `Angenommen am {Datum} durch {Name}.` — der Annahmeblock verschwindet, der vollständige Angebotsinhalt bleibt dauerhaft einsehbar.

**Abgelaufen:** `Dieses Angebot ist am {Datum} abgelaufen. Schreiben Sie uns über „Hilfe" — wir stellen es neu aus.`
**Leerzustand:** `Sobald wir Ihre Anfrage geprüft haben, erscheint hier Ihr Angebot mit Umfang, Preis und Zahlungsplan.`

### 8.3 `/aufgaben`

**H1:** `Ihre Aufgaben`
**Einleitung (nur solange offene Aufgaben existieren):**
> Wir haben vorausgefüllt, was wir schon über Ihr Unternehmen wissen. Sie bestätigen es oder korrigieren es. Sie müssen nicht alles auf einmal machen — Ihr Stand wird gespeichert.

Liste, sortiert nach `sort_order`: Titel · Status · Kurzbeschreibung. Erledigte Aufgaben rutschen nach unten und werden ruhiger dargestellt.

**Aufgabendetail** `/aufgaben/{id}`: Titel · Beschreibung · Zeile `Warum wir das brauchen: {why_needed}` (nur wenn gefüllt) · je nach `kind`:
- `bestaetigung`: Anzeige der Angaben, Buttons `Stimmt so` und `Korrigieren` (öffnet Textfeld)
- `angabe`: Textfeld `Ihre Antwort` (Pflicht)
- `upload`: Dateiauswahl + Pflicht-Checkbox `Ich habe die Rechte an diesen Dateien und darf sie für meine Website verwenden.`
- `freigabe`: Anzeige der freizugebenden Punkte + Pflicht-Checkbox + Namensfeld (siehe unten)

Button: `Aufgabe abschließen` · Sekundär: `Später`
Fehler: `Bitte beantworten Sie die Frage, bevor Sie die Aufgabe abschließen.` · `Bitte bestätigen Sie die Bildrechte.` · `Bitte wählen Sie mindestens eine Datei aus.`

**Sonderfall `kind = freigabe` — die Faktenfreigabe.** Diese Aufgabe ist keine gewöhnliche
Rückmeldung, sondern eine **protokollierte Erklärung** (§4 `approvals`). Deshalb:

> ### Fakten und Umfang final freigeben
> Bitte prüfen Sie Ihre Angaben ein letztes Mal. Danach beginnen wir mit der Produktion.
> Spätere Änderungen an Fakten oder Umfang sind dann nicht mehr ohne Weiteres möglich.

Anzeige darüber: alle abgeschlossenen Aufgaben mit ihren Antworten in Kurzform, damit der Kunde
sieht, was er freigibt. Dazu der Umfangssatz aus dem Angebot:
`Vereinbarter Umfang: {scope_pages} Seiten, {included_feedback_rounds} Korrekturrunden.`

Checkbox: `Die Angaben sind vollständig und richtig. Der Umfang ist so vereinbart.`
Feld: `Ihr Name`
Button: `Verbindlich freigeben`
Fehler: `Bitte bestätigen Sie die Freigabe.` · `Bitte geben Sie Ihren Namen an.`

Nach dem Absenden: Eintrag in `approvals` mit `kind = inhalte`, Audit-Ereignis, Anzeige
`Freigegeben am {Datum} durch {Name}.` Der Lieferkorridor beginnt an diesem Tag (§4c) — der
Startzeitpunkt wird angezeigt: `Fertigstellung voraussichtlich in {min}–{max} Werktagen.`

**Sperre:** Die Freigabeaufgabe ist erst abschließbar, wenn **alle** Pflichtaufgaben mit
`required = true` erledigt sind. Sonst Hinweis statt Button:
`Bitte schließen Sie zuerst die noch offenen Aufgaben ab.` mit Verweis auf die Liste.

**Leerzustand:** `Aktuell nichts zu tun. Sobald wir etwas von Ihnen brauchen, erscheint es hier — Sie bekommen zusätzlich eine E-Mail.`

### 8.4 `/vorschau`

**H1:** `Vorschau und Freigabe`

**Wenn Vorschau vorhanden:**
- Text: `So sieht Ihre Website aktuell aus. Sehen Sie sich in Ruhe alles an und sammeln Sie Ihre Rückmeldungen — es ist einfacher für beide Seiten, wenn alles gebündelt kommt.`
- Button: `Vorschau öffnen` (neues Fenster, `rel="noopener"`)
- Hinweis: `Die Vorschau ist noch nicht öffentlich und für Suchmaschinen gesperrt.`

**Rundenanzeige** (immer, sobald eine Runde offen ist, direkt über dem Feedbackblock):
`Korrekturrunde {number} von {included_feedback_rounds}` — bei `included = false` stattdessen der Hinweistext aus §5.6a.

**Feedbackblock** (nur bei `status = offen` der aktuellen Runde): Textfeld `Ihre Rückmeldung` · optionales Feld `Betrifft welche Seite?` · Button `Rückmeldung senden` · Hinweis: `Sie können mehrere Rückmeldungen senden. Wir bearbeiten sie gebündelt.`
Darunter: bisherige Rückmeldungen der laufenden Runde mit Status und Antwort, ältere Runden zusammengeklappt.

**Einreichen:** Button `Rückmeldungen abschließen und einreichen`, davor ein Bestätigungsschritt:
> Danach können Sie in dieser Runde nichts mehr ergänzen. Wir arbeiten alles gebündelt ein und melden uns mit der neuen Fassung. Möchten Sie einreichen?

Buttons: `Ja, einreichen` · `Noch nicht`. Nach dem Einreichen: `status = eingereicht`, `submitted_at`, E-Mail an SARTU, Anzeige `Eingereicht am {Datum}. Wir melden uns, sobald die neue Fassung bereitsteht.`
Der Button ist gesperrt, solange die Runde keine einzige Rückmeldung enthält — Hinweis: `Bitte geben Sie zuerst eine Rückmeldung ein.`

**Abnahmeblock** (nur bei Status `abnahme`):
> ### Website abnehmen
> Mit der Abnahme bestätigen Sie, dass die Website dem vereinbarten Umfang entspricht. Danach stellen wir die Schlussrechnung und bereiten den Start vor.

Checkbox: `Die Website entspricht dem vereinbarten Umfang.`
Feld: `Ihr Name`
Button: `Website abnehmen`
Fehler: `Bitte bestätigen Sie die Abnahme.` · `Bitte geben Sie Ihren Namen an.`
Nach Abnahme: Eintrag in `approvals`, Audit-Ereignis, Projektstatus `launch_vorbereitung`, E-Mail an Kunde und Admin. Anzeige: `Abgenommen am {Datum} durch {Name}.`

**Leerzustand:** `Sobald die erste Fassung Ihrer Website bereitsteht, finden Sie hier den Vorschau-Link und können Rückmeldung geben.`

### 8.5 `/rechnungen`

**H1:** `Rechnungen`
Tabelle: Nummer · Beschreibung (Meilenstein im Klartext: `Anzahlung` / `Zwischenrate` / `Schlussrechnung` / `Betrieb`) · Betrag brutto · Fällig am · Status · Aktion.

Aktion bei Status `gesendet` oder `ueberfaellig`: Button **`Jetzt bezahlen`** → öffnet `mollie_payment_url` in neuem Fenster.
Direkt darunter: `Nach der Zahlung kann es einen Moment dauern, bis der Status hier aktualisiert ist. Sie müssen nichts weiter tun.`

**Wichtig:** Der Status wird **niemals** aus der Rückkehr vom Zahlungsdienst abgeleitet. Er wird ausschließlich im Adminbereich gesetzt, nachdem der Zahlungseingang geprüft wurde (§12).

Fußzeile: `Alle Beträge netto zzgl. gesetzlicher Umsatzsteuer, sofern nicht anders angegeben. Zahlungsziel 10 Kalendertage.`
**Leerzustand:** `Hier erscheinen Ihre Rechnungen. Sie können direkt im Portal bezahlen; eine Kopie erhalten Sie zusätzlich per E-Mail.`

### 8.6 `/domain`

**H1:** `Domain und E-Mail`
Anzeige: Wunschname (falls erfasst) · bestätigter Name · Status im Klartext · Hinweis zur E-Mail.

Statustexte: `Noch offen` · `Vorschläge liegen bereit` · `Bestätigt` · `Registriert` · `Mit der Website verbunden` · `Online`

**Bestätigungsblock** (nur bei `vorschlaege_bereit`): Anzeige von höchstens drei Vorschlägen als Auswahl · Checkbox `Die Inhaberdaten oben sind korrekt.` · Button `Domain verbindlich bestätigen`
Direkt darüber der Pflichthinweis:
> SARTU registriert die Domain über den technischen Registrar **in Ihrem Namen**. Sie bleiben Inhaber. Nach erfolgreicher Registrierung ist eine Stornierung in der Regel nicht möglich. Eine normale Domain bis 30 € netto pro Jahr ist bei Verwaltung durch SARTU im Betrieb enthalten.

E-Mail-Hinweis (immer sichtbar): `Bestehende E-Mail-Adressen bleiben erreichbar. Wir sichern Ihre Einträge vor jeder Änderung.`
**Leerzustand:** `Sobald wir Ihre Domain geprüft haben, sehen Sie hier den Stand.`

### 8.7 `/inhalte` — Öffnungszeiten (die eine Pflegefunktion)

**H1:** `Öffnungszeiten`
**Einleitung:** `Änderungen hier erscheinen nach unserer Prüfung auf Ihrer Website — üblicherweise am nächsten Werktag.`

Formular je Wochentag (Montag–Sonntag): Checkbox `Geschlossen` · Felder `Von` und `Bis` · optionales Feld `Hinweis`.
Darunter **Ausnahmen**: Liste mit Datum, `Geschlossen`-Schalter oder Zeiten, Bezeichnung (`Feiertag`, `Betriebsurlaub`). Button `Ausnahme hinzufügen`, je Zeile `Entfernen`.

Button: `Änderungen einreichen`
Nach dem Absenden: `pending_publish = true`, Hinweis: `Danke — wir prüfen die Änderung und stellen sie auf Ihre Website. Sie bekommen Bescheid, sobald sie live ist.` Zusätzlich Banner solange offen: `Eine Änderung wartet auf Veröffentlichung.`

Fehler: `Bitte geben Sie für geöffnete Tage eine Von- und eine Bis-Zeit an.` · `Die Bis-Zeit muss nach der Von-Zeit liegen.`

**Was hier bewusst nicht geht** (als ruhiger Hinweis am Seitenende):
> Layout, Seitenstruktur, Adressen und Texte ändern wir für Sie — schreiben Sie uns dazu einfach über „Hilfe".

**Leerzustand (vor Launch):** `Sobald Ihre Website online ist, können Sie hier Ihre Öffnungszeiten selbst pflegen.`

### 8.8 `/hilfe`

**H1:** `Hilfe`
Zwei Bereiche:

**Nachricht schreiben:** Textfeld `Ihre Nachricht` (Pflicht, mind. 10 Zeichen) · Button `Nachricht senden` · Hinweis: `Wir antworten schriftlich, in der Regel innerhalb eines Werktags.`
Darunter frühere Nachrichten mit Antwort.

**Häufige Fragen** (statisch, aufklappbar):
1. `Wie melde ich mich an?` → `Sie bekommen jedes Mal einen Anmeldelink per E-Mail. Es gibt kein Passwort.`
2. `Kann ich Texte selbst ändern?` → `Öffnungszeiten pflegen Sie selbst. Texte, Bilder und Seitenstruktur ändern wir für Sie — schreiben Sie uns einfach.`
3. `Wann kommt meine Rechnung?` → `Nach Annahme des Angebots die Anzahlung, nach der Abnahme die Schlussrechnung. Der Betrieb wird monatlich abgerechnet.`
4. `Was passiert mit meiner Domain?` → `Sie bleiben Inhaber. Wir verwalten sie technisch und sichern Ihre E-Mail-Einträge vor jeder Änderung.`
5. `Wie lange dauert mein Projekt?` → `Nach vollständigen Angaben und Zahlung: Start 7–10, Wachstum 10–15, Platzhirsch 15–25 Werktage.`

Link: `Einführung erneut ansehen` → Willkommensstrecke.

### 8.9 Fehlerseiten

**404:** H1 `Diese Seite gibt es nicht.` · Text `Vielleicht wurde ein Link geändert.` · Button `Zur Übersicht`
**403/fremder Zugriff:** wird als **404** behandelt.
**500:** H1 `Da ist etwas schiefgelaufen.` · Text `Wir wurden informiert. Bitte versuchen Sie es in ein paar Minuten erneut.` · Anzeige einer Fehlerkennung, kein Stacktrace.

---

## 9. Adminportal

Zugang unter `/admin`, eigenes Layout, sichtbar von der Kundenoberfläche unterscheidbar. Anmeldung: E-Mail + Passwort + TOTP.

### 9.1 Screens

| Pfad | Inhalt |
|---|---|
| `/admin` | Cockpit: **neue Anfragen**, Projekte nach Status gruppiert, offene Rechnungen, unbeantwortete Nachrichten, eingereichte Korrekturrunden, wartende Öffnungszeit-Änderungen |
| `/admin/anfragen` | Eingegangene Bedarfsschecks (§4b), Umwandlung in Kunde und Projekt |
| `/admin/kunden` | Liste, Suche nach Name und E-Mail; Anlegen und Bearbeiten von Organisation und Benutzer; Button `Einladung senden` |
| `/admin/projekte` | Liste mit Filter nach Status |
| `/admin/projekte/{id}` | **Arbeitsplatz je Projekt** (siehe unten) |
| `/admin/rechnungen` | Alle Rechnungen, Filter offen/überfällig/bezahlt |
| `/admin/nachrichten` | Support-Nachrichten mit Antwortfeld |
| `/admin/audit` | Audit-Log, filterbar nach Organisation, Aktion, Zeitraum |

### 9.2 Projekt-Arbeitsplatz `/admin/projekte/{id}`

Alles in Abschnitten auf einer Seite:

- **Kopf:** Kunde, Paket, Status (Auswahlfeld + Button `Status setzen`), Felder `Nächster Schritt (Text)` und `Ziel-Pfad`, Button `Speichern`
- **Angebot:** Formular für alle Felder aus §4 (`offers`), Button `Angebot senden` (setzt `sent_at`, Status `gesendet`, verschickt E-Mail). Nach Annahme schreibgeschützt mit Anzeige von Zeitpunkt, Name und IP
- **Rechnungen:** Anlegen mit Nummer, Meilenstein, Beträgen, Fälligkeit, **Feld `Mollie-Zahlungslink`**. Aktionen: `Senden`, `Als bezahlt markieren` (mit Pflicht-Bestätigung, siehe §12), `Stornieren`
- **Aufgaben:** Anlegen einzeln oder **aus Vorlage** (§9.3), sortierbar, Bearbeiten, Deaktivieren. Anzeige der Kundenantworten und hochgeladenen Dateien mit Download
- **Vorschau:** Feld `Vorschau-URL`, Button `Vorschau bereitstellen` (setzt Status `vorschau`, öffnet **zugleich** eine neue Korrekturrunde, verschickt E-Mail)
- **Korrekturrunden:** Liste aller Runden mit Nummer, Status, Zeitpunkten und Kennzeichen `enthalten` / `zusätzlich`. Anzeige `{genutzt} von {included_feedback_rounds} enthaltenen Runden`. Aktionen: `Runde als bearbeitet markieren`, `Zusätzliche Runde öffnen` (legt `included = false` an, **Bestätigungsdialog**: `Diese Runde ist im Festpreis nicht enthalten. Der Kunde wird darauf hingewiesen. Fortfahren?`)
- **Feedback:** Rückmeldungen der gewählten Runde, je Eintrag Antwortfeld und Statuswechsel
- **Freigaben:** Anzeige aller Einträge aus `approvals` (`inhalte`, `abnahme`) mit Zeitpunkt, Name, IP. **Nur lesbar** — nachträglich nicht änderbar oder löschbar
- **Domain:** alle Felder aus `domain_status`, Vorschlagsfelder, Button `Vorschläge bereitstellen`
- **Onlinegang:** Feld `Live-URL`, Feld `Betriebsbeginn` (vorbelegt mit heute), Button `Website als online melden`. Setzt `live_url`, `launched_at`, Status `live`, `protection_started_on`, berechnet `protection_min_term_until` (§5.7), verschickt die E-Mail `Ihre Website ist online`. **Bestätigungsdialog** mit Anzeige des berechneten Mindestlaufzeit-Endes
- **Öffnungszeiten:** aktueller Stand des Kunden, Markierung wartender Änderungen, Button `Als veröffentlicht markieren` (setzt `pending_publish = false`, verschickt E-Mail)
- **Ereignisse:** Audit-Auszug dieses Projekts

### 9.3 Aufgabenvorlagen

Beim Anlegen wählbar nach Paket. Mindestens diese Vorlagen (Titel · Art · „Warum wir das brauchen"):

1. `Firmendaten bestätigen` · bestaetigung · `Diese Angaben erscheinen im Impressum und in den Kontaktdaten Ihrer Website.`
2. `Hauptleistung und Zielgruppe bestätigen` · bestaetigung · `Danach richtet sich der Aufbau der Startseite.`
3. `Einzugsgebiet bestätigen` · bestaetigung · `Damit wir Ihre Region richtig benennen.`
4. `Kontaktweg und Öffnungszeiten` · angabe · `Damit Besucher wissen, wann und wie sie Sie erreichen.`
5. `Logo und Bildmaterial hochladen` · upload · `Echte Bilder aus Ihrem Betrieb wirken deutlich besser als gekaufte Fotos.`
6. `Nutzungsrechte bestätigen` · bestaetigung · `Wir dürfen nur Material verwenden, an dem Sie die Rechte haben.`
7. `Domain und E-Mail klären` · angabe · `Damit Ihre bestehenden E-Mail-Adressen beim Start erreichbar bleiben.`
8. `Rechtstexte freigeben` · upload · `Impressum und Datenschutz kommen von Ihnen oder Ihrer Kanzlei — wir binden sie ein.`
9. *(Wachstum/Platzhirsch)* `Einzelne Leistungen beschreiben` · angabe · `Je Leistung eine eigene, gut auffindbare Seite.`
10. *(Wachstum/Platzhirsch)* `Vertrauensbelege nennen` · angabe · `Qualifikationen und Erfahrung, die wir belegbar nennen dürfen.`
11. *(Platzhirsch)* `Team und Ansprechpartner` · angabe · `Für den Team- und Karrierebereich.`
12. *(Platzhirsch)* `Projekte oder Referenzen` · upload · `Nur mit Freigabe der abgebildeten Kunden.`
13. `Fakten und Umfang final freigeben` · freigabe · `Danach starten wir die Produktion.`

---

## 10. E-Mails

Alle Mails: Absender `MAIL_FROM`, Anrede `Guten Tag {Vorname},`, Grußformel `Freundliche Grüße\nSARTU`, Fußzeile mit Impressumsangaben und dem Hinweis `Diese Nachricht bezieht sich auf Ihr Projekt „{Projekttitel}".` Klartext **und** einfaches HTML.

| Auslöser | Betreff | Kern |
|---|---|---|
| Neue Anfrage über die Website (an Admin) | `Neue Anfrage: {Unternehmen}` | interne Kurzmeldung mit empfohlener Lösung und Ampelkennzeichen + Link auf `/admin/anfragen` |
| Anmeldelink | `Ihr Anmeldelink für das SARTU-Portal` | `Hier ist Ihr Anmeldelink. Er gilt 15 Minuten und lässt sich einmal verwenden.` + Link |
| Einladung (neu angelegt) | `Ihr Zugang zum SARTU-Portal` | `Ihr Projektportal ist bereit. Dort finden Sie Angebot, Aufgaben, Vorschau und Rechnungen an einem Ort.` + Link |
| Angebot gesendet | `Ihr Angebot von SARTU liegt bereit` | `Ihr Angebot mit Umfang, Preis und Zahlungsplan liegt im Portal. Gültig bis {Datum}.` |
| Angebot angenommen (an Kunde) | `Bestätigung Ihrer Beauftragung` | `Danke für Ihre Beauftragung. Als Nächstes erhalten Sie die Anzahlungsrechnung im Portal.` |
| Angebot angenommen (an Admin) | `Angebot angenommen: {Organisation}` | interne Kurzmeldung |
| Rechnung gesendet | `Ihre Rechnung {Nummer}` | `Ihre Rechnung liegt im Portal und ist bis zum {Datum} fällig. Sie können direkt dort bezahlen.` |
| Zahlung verbucht | `Zahlungseingang bestätigt` | `Wir haben Ihre Zahlung erhalten. Vielen Dank.` |
| Neue Aufgaben | `Es liegen Aufgaben für Sie bereit` | `Wir brauchen ein paar Angaben von Ihnen. Das dauert meist 15 bis 25 Minuten.` |
| Faktenfreigabe erfolgt (an beide) | `Freigabe bestätigt — wir starten` | `Danke für die Freigabe. Wir beginnen mit der Produktion. Fertigstellung voraussichtlich in {min}–{max} Werktagen.` |
| Vorschau bereit | `Ihre Vorschau ist bereit` | `Sie können sich Ihre Website jetzt ansehen und Rückmeldung geben. Sammeln Sie in Ruhe alles und reichen Sie es gebündelt ein.` |
| Korrekturrunde eingereicht (an Admin) | `Korrekturrunde {Nummer} eingereicht: {Organisation}` | interne Kurzmeldung mit Anzahl der Rückmeldungen |
| Korrekturrunde eingearbeitet (an Kunde) | `Ihre Änderungen sind eingearbeitet` | `Wir haben Ihre Rückmeldungen umgesetzt. Die neue Fassung liegt in der Vorschau bereit.` |
| Abnahme erfolgt (an beide) | `Abnahme bestätigt` | `Danke für die Abnahme. Wir bereiten den Start vor.` |
| Website online | `Ihre Website ist online` | `Ihre Website ist erreichbar unter {URL}. Ab jetzt übernehmen wir den laufenden Betrieb.` |
| Öffnungszeiten veröffentlicht | `Ihre Öffnungszeiten sind aktualisiert` | `Ihre Änderung ist jetzt auf der Website sichtbar.` |
| Antwort auf Nachricht | `Antwort auf Ihre Nachricht` | Antworttext + Portallink |

**Keine** Werbemails, keine Newsletter, keine Massenversendung.

---

## 11. Uploads

- Erlaubt: `jpg`, `jpeg`, `png`, `webp`, `svg`, `pdf`, `docx`, `zip`
- Höchstens **20 MB** je Datei, **10** Dateien je Aufgabe
- Prüfung von Endung **und** MIME-Typ; bei Abweichung ablehnen
- Speicherung unter `UPLOAD_DIR` mit UUID-Dateinamen, **außerhalb** des öffentlich ausgelieferten Verzeichnisses
- Auslieferung nur über eine Route, die Session und Organisationszugehörigkeit prüft
- SVG werden **nicht** inline eingebettet, sondern nur als Download angeboten (Skriptrisiko)
- Fehler: `Diese Dateiart können wir nicht verarbeiten. Erlaubt sind Bilder, PDF, Word-Dateien und ZIP-Archive.` · `Die Datei ist zu groß. Bitte höchstens 20 MB je Datei.`

---

## 12. Zahlungen in Stufe 0

**Keine Programmanbindung an den Zahlungsdienst.** Ablauf:

1. Admin erzeugt den Zahlungslink im Mollie-Dashboard und trägt ihn bei der Rechnung ein
2. Kunde klickt im Portal auf `Jetzt bezahlen` und zahlt dort
3. Admin prüft den Eingang **im Mollie-Dashboard** und setzt die Rechnung im Adminbereich auf `bezahlt`

**Eiserne Regel:** Der Zahlungsstatus wird **niemals** aus der Rückkehr des Browsers abgeleitet. Es gibt in Stufe 0 keine automatische Statusänderung durch den Zahlungsdienst.

Beim Markieren als bezahlt erscheint eine Pflicht-Bestätigung:
> Bestätigen Sie, dass der Zahlungseingang im Zahlungsdienst geprüft wurde. Diese Aktion wird protokolliert.

Zusätzlich **Pflichtfeld** `Grundlage der Prüfung` (Freitext, mindestens 3 Zeichen) — z. B.
`Mollie-Zahlung tr_xxx vom 04.08.2026` oder `Überweisung Kontoauszug 12/2026`.

Danach: `paid_at`, `marked_paid_by_user_id`, E-Mail an den Kunden und ein Audit-Ereignis mit
**allen** folgenden Angaben:

| Feld | Inhalt |
|---|---|
| `actor_user_id` | wer den Status gesetzt hat |
| `created_at` | wann |
| `entity_type` / `entity_id` | `invoice` / Rechnungs-ID |
| `old_value` / `new_value` | z. B. `gesendet` → `bezahlt` |
| `reason` | der eingegebene Grundlagentext |
| `ip` | IP des Admins |

**Das gilt für jede manuelle Statusänderung an Geld und Fristen**, nicht nur für „bezahlt":
Stornierung, Rücksetzung auf `offen`, Änderung von `due_date`, Änderung von `protection_started_on`.
Ohne Grundlagentext lässt sich keine dieser Änderungen speichern.

Ein einmal auf `bezahlt` gesetzter Status lässt sich **nicht stillschweigend** zurücknehmen — die
Rücknahme ist eine eigene protokollierte Aktion mit eigenem Grundlagentext und erzeugt eine
Benachrichtigung an den Kunden.

Der **Betrieb** (monatlich) wird in Stufe 0 als normale Rechnung mit Meilenstein `betrieb` angelegt. Lastschrifteinzug, Mandate und Wiederholung sind Stufe 2.

---

## 13. Sprache und Oberfläche

Es gelten die Sprachregeln aus `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` §2, insbesondere:

- Durchgehend **„Sie"**, Marke immer **`SARTU`**
- **Nie** „wartungsarm", „wartungsfrei", „rechtssicher", „garantiert"
- Keine internen Codes in der Kundenoberfläche — immer Klartext (§5)
- Kein Fachjargon: nicht „Deployment", „Repository", „Ticket", „Onboarding", „Asset". Stattdessen: „Veröffentlichung", „Ihre Website", „Nachricht", „Einrichtung", „Datei"
- Fehlermeldungen sagen, **was** falsch ist und **wie** es richtig geht — keine Entschuldigungen, keine Schuldzuweisung
- Jede Aktion hat eine sichtbare Rückmeldung („Gespeichert.", „Nachricht gesendet.")
- Gefährliche Aktionen brauchen eine ausdrückliche Bestätigung, die die Auswirkung benennt
- **Deaktivieren statt Löschen**, überall

---

## 14. Barrierefreiheit und Leistung

- Semantisches HTML, sinnvolle Überschriftenhierarchie, Landmarks, Skip-Link
- Vollständige Tastaturbedienung, sichtbarer Fokus, nie entfernt
- Alle Felder mit `<label>`, Fehler mit `aria-describedby` verknüpft, `aria-invalid` gesetzt
- Kontrast ≥ 4,5:1, Status nie allein über Farbe (immer zusätzlich Text)
- `prefers-reduced-motion` wird respektiert
- **Ohne JavaScript vollständig bedienbar**
- Serverantwort unter 300 ms bei typischer Last; keine Seite über 150 KB inklusive Assets
- Mobil vollwertig: ein Sachverhalt je Bildschirm, Bedienelemente in Daumenreichweite, Tastaturtyp passend (E-Mail, Telefon, Zahl)

**Optik:** Farben, Schriften und Formen werden **nicht hier festgelegt**. Vorgehen nach `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`; Kunden- und Adminbereich müssen visuell unterscheidbar sein.

---

## 15. Datenschutz und Recht

- Server und Daten in **Deutschland/EU**
- Datensparsamkeit: nur erheben, was der Prozess braucht
- **Kein** Tracking, **keine** Analyse-Werkzeuge, **keine** externen Schriften oder Skripte im Portal → kein Cookie-Banner nötig; die Session ist technisch erforderlich
- Aufbewahrung: Audit-Ereignisse 3 Jahre, Anmeldetoken nach Ablauf löschen, Sessions nach Verfall löschen
- Auskunft und Löschung: Adminfunktion `Daten exportieren` (JSON je Organisation) und `Organisation archivieren`. Vollständige Löschung nur manuell nach Prüfung — gesetzliche Aufbewahrungspflichten für Rechnungen gehen vor
- Auftragsverarbeitungsvertrag mit Hoster und Mailversand ist Sache des Betreibers, nicht des Codes
- Rechtstexte des Portals (Impressum, Datenschutz) werden **verlinkt**, nicht selbst formuliert

---

## 16. Testfälle (Pflicht, müssen automatisiert laufen)

**Mandantentrennung — `test/tenant-isolation.test.js` (unantastbar):**
1. Kunde A ruft Projekt von Kunde B auf → **404**
2. Kunde A ruft Rechnung, Aufgabe, Datei, Angebot von B auf → jeweils **404**
3. Kunde A sendet `POST` mit fremder `project_id` → **404**, keine Änderung
4. Kunde A lädt Datei von B über direkte URL → **404**
5. Liste enthält ausschließlich eigene Datensätze
5a. Der Test durchläuft die **vollständige Routenliste** des Kundenbereichs, nicht eine Auswahl. Kommt eine Route hinzu, ohne dass der Test sie kennt, **scheitert der Test**
5b. Eine Kundenabfrage ohne Session-Organisation wirft einen Fehler und liefert **nicht** alle Datensätze (§3 Regel 2a)

**Anmeldung:**
6. Token funktioniert genau einmal
7. Token nach 15 Minuten ungültig
8. Token einer anderen E-Mail funktioniert nicht
9. Rate-Limit greift ab dem 6. Versuch je E-Mail und Stunde
10. Bestätigungsseite ist identisch für vorhandene und nicht vorhandene Adressen

**Fachlogik:**
11. Angebotsannahme ohne alle vier Checkboxen scheitert
12. Angenommenes Angebot lässt sich nicht erneut annehmen
13. Abgelaufenes Angebot lässt sich nicht annehmen
14. Rechnungsstatus wechselt nicht durch Aufruf einer Rückkehr-URL
15. `ueberfaellig` wird korrekt gesetzt, wenn `due_date` überschritten ist
16. Aufgabe mit Pflichtantwort lässt sich nicht ohne Antwort abschließen
17. Upload ohne Rechtebestätigung wird abgelehnt
18. Abnahme erzeugt Eintrag in `approvals` (`kind = abnahme`) **und** Audit-Ereignis
19. Öffnungszeiten mit Bis vor Von werden abgelehnt
20. Statuswechsel erzeugt Audit-Ereignis mit Akteur

**Rechenregeln und Scope-Schutz:**
21. Angebot mit falschem `first_year_net_cents` wird **nicht** gespeichert (§4 Prüfregel)
22. `payment_plan = custom` wird bei `package ≠ sonderprojekt` abgelehnt
23. Bei `custom` muss die Summe der Raten dem Einmalpreis entsprechen, sonst Ablehnung
24. Angebotsannahme überträgt `included_feedback_rounds` und `protection_level` ins Projekt
25. Eine zweite Korrekturrunde bei Paket **Start** wird als `included = false` angelegt und im Portal entsprechend gekennzeichnet (§5.6a)
26. Die Freigabeaufgabe lässt sich nicht abschließen, solange Pflichtaufgaben offen sind (§8.3)
27. Freigabe erzeugt `approvals` mit `kind = inhalte` und setzt den Startzeitpunkt des Lieferkorridors
28. `protection_started_on` wird beim Wechsel auf `live` gesetzt, `protection_min_term_until` liegt 12 Monate später (§5.7)

**Anfrageeingang (§4b):**
29. `POST /api/anfragen` ohne gültigen `X-Sartu-Token` → **401**, kein Datensatz, kein Grund in der Antwort
30. Gültige Anfrage erzeugt **nur** einen `lead` — keine `organizations`, `users` oder `projects`
31. Rate-Limit greift ab der 11. Anfrage je IP und Stunde, Antwort `429` mit `Retry-After`
32. Ausgefülltes Honigtopffeld liefert `201` und erzeugt **keinen** Datensatz
33. Absenden unter 3 Sekunden nach `form_started_at` liefert `201` und erzeugt **keinen** Datensatz
34. **Dieselbe `submission_id` zweimal** → beim zweiten Mal `200`, weiterhin genau **ein** Datensatz
35. `b2b_confirmed = false` oder `privacy_confirmed = false` → `422`, kein Datensatz
36. Rumpf über 64 KB → `413`
37. Fehlerantwort enthält **niemals** Feldwerte, interne Kennungen oder Datenbankmeldungen
38. Unbekanntes Zusatzfeld wird in `payload` gespeichert und **nicht** abgewiesen
39. `source_ip` ist nach 30 Tagen geleert, der übrige Datensatz unverändert
40. `Endgültig löschen` entfernt den Datensatz wirklich und hinterlässt ein Audit-Ereignis **ohne** die gelöschten Inhalte

**Sicherheit:**
41. `POST` ohne CSRF-Token wird abgelehnt
42. Kunde erreicht **keine** `/admin`-Route — geprüft über die vollständige Adminroutenliste (§3 Regel 2a)
43. Abgemeldeter Benutzer erreicht keine `/admin`-Route
44. Admin ohne bestätigtes TOTP erreicht keine Adminroute
45. Die Kundenauswahl im Adminbereich verändert die Session-Organisation **nicht**
46. Unerlaubter Dateityp wird abgelehnt
47. Sicherheitsheader sind in allen Antworten gesetzt
48. Datenbankbedingung greift: Kunde ohne `organization_id` und Admin **mit** `organization_id` lassen sich nicht anlegen
49. **`INTAKE_TOKEN` kommt in keiner ausgelieferten Antwort und keiner Ansicht vor** — geprüft per Volltextsuche über alle gerenderten Seiten
50. Der Tokenvergleich in §4b ist **zeitkonstant** — im Code nachgewiesen, nicht nur behauptet

**Protokollierung:**
51. Manuelles Setzen auf `bezahlt` ohne Grundlagentext scheitert
52. Das Audit-Ereignis dazu enthält Akteur, Zeitpunkt, alten Wert, neuen Wert, Grundlagentext und IP (§12)
53. Änderung von `due_date` und `protection_started_on` erzeugt je ein Audit-Ereignis mit Grundlagentext
54. Rücknahme von `bezahlt` ist eine eigene protokollierte Aktion und benachrichtigt den Kunden
55. Ein Audit-Eintrag lässt sich weder ändern noch löschen

**Bedienung:**
56. Alle Kernabläufe funktionieren mit deaktiviertem JavaScript
57. Willkommensstrecke erscheint einmal und danach nicht mehr
58. Jede Seite hat genau eine `<h1>`
59. Kein Systemcode aus §5 erscheint in einer Kundenansicht — geprüft per Volltextsuche über die gerenderten Seiten

---

## 17. Definition of Done

- [ ] Alle Screens aus §7, §8 und §9 vorhanden und bedienbar
- [ ] Alle Texte aus diesem Dokument **wörtlich** übernommen
- [ ] Alle Statuswerte zeigen dem Kunden Klartext, nirgends interne Codes
- [ ] Formate aus §4a eingehalten: deutsche Datums- und Geldformate, Europe/Berlin, 19 % USt., Beträge als Cent gespeichert, keine leeren Werte als `null` sichtbar
- [ ] Alle 59 Testfälle aus §16 laufen automatisiert und grün
- [ ] `test/tenant-isolation.test.js` vorhanden, vollständig, nicht abgeschwächt
- [ ] Kunden- und Adminzugriff laufen über **getrennte** Datenzugriffsschichten (§3 Regel 2a); kein gemeinsamer Codepfad lässt den Organisationsfilter weg
- [ ] Rechenregeln greifen: Erstjahreswert, Zahlungsplan `custom`, Ratensumme (§4)
- [ ] Korrekturrunden werden gezählt und angezeigt; nichts wird automatisch gesperrt oder berechnet (§5.6a)
- [ ] Faktenfreigabe und Abnahme erzeugen je einen `approvals`-Eintrag mit Name, Zeitpunkt, IP und Audit-Ereignis — nachträglich nicht änderbar
- [ ] `POST /api/anfragen` funktioniert, ist mit Token, Rate-Limit, Honigtopf, Zeitregel und `submission_id` geschützt und legt **nur** einen `lead` an (§4b)
- [ ] **`INTAKE_TOKEN` erscheint in keiner Antwort, keiner Ansicht und keinem Protokolleintrag**; der Vergleich ist zeitkonstant
- [ ] Anfrageliste bleibt innerhalb der Grenze aus §0.3a — keine Bewertung, kein Nachfassen, keine Zuweisung
- [ ] Aufbewahrung und Betroffenenrechte umgesetzt: IP-Löschung nach 30 Tagen, Löschdatum sichtbar, Export und endgültige Löschung je Datensatz (§4b.4)
- [ ] Jede manuelle Änderung an Geld oder Fristen verlangt einen Grundlagentext und erzeugt ein vollständiges Audit-Ereignis (§12)
- [ ] Betriebsbeginn und Mindestlaufzeit werden beim Onlinegang gesetzt und dem Kunden angezeigt (§5.7)
- [ ] Ohne JavaScript vollständig bedienbar
- [ ] Kontrast, Fokus, Tastaturbedienung, Labels geprüft
- [ ] Keine Secrets im Repository; `.env.example` vollständig
- [ ] Migrationen laufen von leerer Datenbank fehlerfrei durch
- [ ] Seed erzeugt einen vollständigen Musterkunden über alle Projektstände — geeignet für die Website-Screenshots, ohne echte Namen oder realistische Rechnungsnummern
- [ ] Audit-Log erfasst alle in §3.9 genannten Ereignisse
- [ ] E-Mails werden versendet und sind in Klartext und HTML lesbar
- [ ] Sicherheitsheader gesetzt, HTTPS erzwungen
- [ ] `README.md` beschreibt Einrichtung, Migration, Seed, Start, Deployment und Backup

---

## 18. Was du ablieferst

1. Lauffähiges Portal im Repository
2. **`README.md`**: Voraussetzungen, Einrichtung, Umgebungsvariablen, Migration, Seed, Start, Deployment auf Hetzner, Backup-Hinweis (Datenbank **und** Upload-Verzeichnis)
3. **Testbericht**: alle 59 Fälle aus §16 mit Ergebnis
4. **Messwerte**: Antwortzeiten der Kernseiten, Seitengröße
5. **Offene-Punkte-Liste**: alles, was bewusst nicht gebaut wurde (§0.3), plus alles, was du melden musst
6. **Screenshot-Satz** aus der echten Oberfläche für die Website: Cockpit, Angebot, Aufgaben, Vorschau mit Rundenanzeige, Rechnungen, Öffnungszeiten — mit Musterdaten, je einmal Desktop und Mobil
7. **Schnittstellenbeschreibung** für die Website: das genaue Format von `POST /api/anfragen`, ein funktionierendes Beispiel und der Hinweis, dass `INTAKE_TOKEN` **nicht** im Repository steht

**Arbeite nicht ins Blaue:** Fehlt eine Information oder widerspricht sich etwas, melde es, statt zu raten. Baue **nichts** aus §0.3 „nicht in Stufe 0", auch nicht „schon mal vorbereitet".
