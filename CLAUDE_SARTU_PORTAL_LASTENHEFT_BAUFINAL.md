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
| Anmeldung ohne Passwort (Magic Link) | einfache Auth, Konten manuell angelegt |
| Willkommensstrecke beim ersten Login | statisch, Inhalt fest |
| Cockpit mit **genau einem** nächsten Schritt | Status vom Admin gesetzt |
| Angebot mit Umfang, Preis, Zahlungsplan + digitale Annahme | Admin erstellt das Angebot im Adminbereich |
| Rechnungen mit Status und **Mollie-Zahlungslink** | Link manuell erzeugt, kein Abo-Automatismus |
| Aufgabenliste mit Upload | Aufgaben aus Vorlage, vom Admin zugewiesen |
| Vorschau-Link + gebündeltes Feedback | Vorschau manuell bereitgestellt |
| Freigabe/Abnahme mit Zeitstempel | manuell bestätigt, aber protokolliert |
| Domain- und E-Mail-Status | manuell gepflegter Statuswert |
| **Eine echte Pflegefunktion:** Öffnungszeiten | Änderung löst manuellen Rebuild aus |
| Hilfe/Nachricht an SARTU | einfaches Nachrichtenfeld |
| Adminbereich für all das | – |

### 0.3 Ausdrücklich NICHT in Stufe 0

Automatische Domainregistrierung · Mollie-Abo/Mandate/Webhooks · KI-Agenten-Orchestrierung · automatische Builds oder Deployments · SEO-Flottenzentrale · Rollback-Automation · Lead-Inbox der Kundenwebsites · Rechnungserzeugung als Buchhaltung (die läuft in lexoffice/sevDesk) · Mehrbenutzer-Rollen pro Kunde · Dateiversionierung · Volltextsuche · Benachrichtigungseinstellungen · Dunkelmodus.

**Regel:** Wird eine dieser Funktionen gebraucht, wird sie **beantragt, nicht gebaut**.

### 0.4 Portal-Screenshots

Die Website braucht Screenshots aus **dieser echten Oberfläche**. Deshalb muss das Portal mit **realistischen Musterdaten** befüllbar sein (Seed). Keine gezeichneten Fake-Dashboards. Musterdaten enthalten **keine** echten Personennamen und **keine** realistischen Rechnungsnummern.

---

## 1. Technischer Rahmen

**Stack — entschieden, nicht zur Diskussion** (Quelle: `konzepte/sartuportalCLAUDE.md`, bewusst langweilig und wartungsarm):

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
`DATABASE_URL` · `SESSION_SECRET` · `ENC_KEY` (32 Byte, base64) · `SMTP_HOST` `SMTP_PORT` `SMTP_USER` `SMTP_PASS` `MAIL_FROM` · `BASE_URL` · `ADMIN_TOTP_ISSUER` · `UPLOAD_DIR` · `NODE_ENV`

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

1. **Mandantentrennung ist heilig.** Jede Abfrage filtert nach `organization_id` **aus der Session** — niemals aus einem Request-Parameter, Formularfeld oder URL-Segment. Kunde A darf unter keinen Umständen Daten von Kunde B sehen.
   Der Test `test/tenant-isolation.test.js` ist **unantastbar**: nie löschen, nie abschwächen, um grün zu werden.
2. **Objektzugriff immer doppelt prüfen:** Existiert das Objekt **und** gehört es zur Session-Organisation? Sonst **404**, nicht 403 (keine Existenz preisgeben).
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
| `organization_id` | uuid | **NULL bei Admins** |
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

### `projects`
| Feld | Typ | Hinweis |
|---|---|---|
| `organization_id` | uuid, NOT NULL | |
| `title` | text, NOT NULL | z. B. „Firmenwebsite Musterbau" |
| `package` | text, NOT NULL | `start` \| `wachstum` \| `platzhirsch` \| `sonderprojekt` |
| `status` | text, NOT NULL | siehe §5.1 |
| `next_step_text` | text | vom Admin gesetzt, überschreibt die Ableitung |
| `next_step_url` | text | optionaler Sprungziel-Pfad im Portal |
| `preview_url` | text | Vorschau-Link |
| `preview_published_at` | timestamptz | |
| `live_url` | text | |
| `launched_at` | timestamptz | |
| `archived_at` | timestamptz | |

### `offers`
`project_id` · `number` (text, UNIQUE) · `status` (§5.2) · `summary` (text) · `sitemap` (text) · `inclusions` (text) · `exclusions` (text) · `one_time_net_cents` (integer) · `protection_monthly_net_cents` (integer) · `first_year_net_cents` (integer) · `payment_plan` (text: `50_50` \| `40_30_30`) · `valid_until` (date) · `sent_at` · `accepted_at` · `accepted_by_user_id` · `accepted_ip` (inet) · `accepted_name` (text)

> Beträge **immer in Cent als integer**. Nie Fließkomma für Geld.

### `invoices`
`project_id` · `number` (text, UNIQUE) · `milestone` (text: `anzahlung` \| `zwischenrate` \| `schlussrate` \| `betrieb`) · `status` (§5.3) · `net_cents` · `vat_cents` · `gross_cents` · `due_date` (date) · `mollie_payment_url` (text) · `paid_at` · `marked_paid_by_user_id` · `note` (text)

### `tasks`
`project_id` · `title` (text) · `description` (text) · `why_needed` (text, die Zeile „Warum wir das brauchen") · `kind` (text: `bestaetigung` \| `angabe` \| `upload` \| `freigabe`) · `status` (§5.4) · `sort_order` (integer) · `answer_text` (text) · `completed_at` · `completed_by_user_id` · `required` (boolean, default true)

### `task_files`
`task_id` · `organization_id` (redundant, für die Mandantenprüfung) · `original_name` (text) · `stored_name` (text, UUID) · `mime_type` (text) · `size_bytes` (bigint) · `rights_confirmed` (boolean) · `uploaded_by_user_id`

### `feedback_items`
`project_id` · `round` (integer) · `body` (text, NOT NULL) · `page_hint` (text) · `status` (§5.5) · `created_by_user_id` · `answered_at` · `answer_text` (text)

### `approvals`
`project_id` · `kind` (text: `vorschau` \| `abnahme` \| `launch`) · `granted_at` · `granted_by_user_id` · `granted_ip` (inet) · `granted_name` (text) · `note` (text)

### `domain_status`
`project_id` (UNIQUE) · `desired_name` (text) · `confirmed_name` (text) · `owner_confirmed` (boolean) · `state` (text: `offen` \| `vorschlaege_bereit` \| `bestaetigt` \| `registriert` \| `verbunden` \| `live`) · `email_note` (text) · `admin_note` (text)

### `business_hours`
`organization_id` · `weekday` (integer 0–6) · `closed` (boolean) · `open_time` (time) · `close_time` (time) · `note` (text) · `pending_publish` (boolean) — Änderungen gelten erst nach Rebuild als veröffentlicht

### `business_hours_exceptions`
`organization_id` · `date` (date) · `closed` (boolean) · `open_time` · `close_time` · `label` (text, z. B. „Betriebsurlaub")

### `support_messages`
`organization_id` · `project_id` (nullable) · `body` (text) · `created_by_user_id` · `answered_at` · `answer_text`

### `audit_events`
`actor_user_id` (nullable) · `organization_id` (nullable) · `action` (text) · `entity_type` (text) · `entity_id` (uuid) · `detail` (jsonb) · `ip` (inet)

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
| **Prozentwerte Zahlungsplan** | fest: `50_50` = 50/50, `40_30_30` = 40/30/30. Keine freien Prozentsätze in Stufe 0 |
| **Zahlungsziel** | **10 Kalendertage** ab Rechnungsdatum, als Vorbelegung für `due_date` |
| **Dateigrößen** | `12,4 MB` (deutsch, eine Nachkommastelle) |
| **Nummernkreise** | Angebot `AN-JJJJ-NNN`, Rechnung `RE-JJJJ-NNN`, je Jahr fortlaufend. In Stufe 0 vom Admin eingegeben, Eindeutigkeit erzwingt die Datenbank |
| **Telefonnummern** | Anzeige wie eingegeben, keine automatische Umformatierung |
| **Leere Werte** | nie `null`, `–` oder `undefined` anzeigen. Stattdessen: `Noch nicht hinterlegt` |

**Projekte je Organisation:** In Stufe 0 hat eine Organisation **genau ein aktives Projekt**. Mehrere Projekte sind technisch möglich (Fremdschlüssel), die Oberfläche zeigt aber immer das jüngste nicht archivierte. Existieren mehrere, erscheint im Adminbereich ein Hinweis — im Kundenportal keine Projektauswahl.

**Rundung:** kaufmännisch, immer auf ganze Cent.

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

Erscheint **einmalig**, wenn `users.welcome_seen_at` leer ist. Überspringbar, jederzeit erneut aufrufbar unter Hilfe. Maximal drei Bildschirme. Nach dem letzten Bildschirm oder bei „Überspringen": `welcome_seen_at` setzen.

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

Zeigt: Angebotsnummer · Gültig bis · Zusammenfassung des Ziels · empfohlene Lösung · vorgesehene Seitenstruktur · was enthalten ist · was **nicht** enthalten ist · Einmalpreis netto · Umsatzsteuer · Bruttobetrag · monatlicher Betrieb netto · Mindestlaufzeit 12 Monate · Erstjahreswert netto · Zahlungsplan im Klartext.

Zahlungsplan-Texte:
- `50_50`: `50 % bei Auftrag, 50 % nach Abnahme vor dem Onlinegang. Zahlungsziel jeweils 10 Kalendertage.`
- `40_30_30`: `40 % bei Auftrag, 30 % nach der ersten Vorschau, 30 % nach Abnahme vor dem Onlinegang. Zahlungsziel jeweils 10 Kalendertage.`

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
Danach zeigt die Seite: `Angenommen am {Datum} durch {Name}.` — der Annahmeblock verschwindet.

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
- `freigabe`: Anzeige + Button `Freigeben`

Button: `Aufgabe abschließen` · Sekundär: `Später`
Fehler: `Bitte beantworten Sie die Frage, bevor Sie die Aufgabe abschließen.` · `Bitte bestätigen Sie die Bildrechte.` · `Bitte wählen Sie mindestens eine Datei aus.`

**Leerzustand:** `Aktuell nichts zu tun. Sobald wir etwas von Ihnen brauchen, erscheint es hier — Sie bekommen zusätzlich eine E-Mail.`

### 8.4 `/vorschau`

**H1:** `Vorschau und Freigabe`

**Wenn Vorschau vorhanden:**
- Text: `So sieht Ihre Website aktuell aus. Sehen Sie sich in Ruhe alles an und sammeln Sie Ihre Rückmeldungen — es ist einfacher für beide Seiten, wenn alles gebündelt kommt.`
- Button: `Vorschau öffnen` (neues Fenster, `rel="noopener"`)
- Hinweis: `Die Vorschau ist noch nicht öffentlich und für Suchmaschinen gesperrt.`

**Feedbackblock:** Textfeld `Ihre Rückmeldung` · optionales Feld `Betrifft welche Seite?` · Button `Rückmeldung senden` · Hinweis: `Sie können mehrere Rückmeldungen senden. Wir bearbeiten sie gebündelt.`
Darunter: bisherige Rückmeldungen mit Status und Antwort.

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
| `/admin` | Cockpit: Projekte nach Status gruppiert, offene Rechnungen, unbeantwortete Nachrichten, offene Feedbacks, wartende Öffnungszeit-Änderungen |
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
- **Vorschau:** Feld `Vorschau-URL`, Button `Vorschau bereitstellen` (setzt Status `vorschau`, verschickt E-Mail)
- **Feedback:** Liste der Rückmeldungen, je Eintrag Antwortfeld und Statuswechsel
- **Abnahmen:** Anzeige aller Einträge aus `approvals` mit Zeitpunkt, Name, IP
- **Domain:** alle Felder aus `domain_status`, Vorschlagsfelder, Button `Vorschläge bereitstellen`
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
| Anmeldelink | `Ihr Anmeldelink für das SARTU-Portal` | `Hier ist Ihr Anmeldelink. Er gilt 15 Minuten und lässt sich einmal verwenden.` + Link |
| Einladung (neu angelegt) | `Ihr Zugang zum SARTU-Portal` | `Ihr Projektportal ist bereit. Dort finden Sie Angebot, Aufgaben, Vorschau und Rechnungen an einem Ort.` + Link |
| Angebot gesendet | `Ihr Angebot von SARTU liegt bereit` | `Ihr Angebot mit Umfang, Preis und Zahlungsplan liegt im Portal. Gültig bis {Datum}.` |
| Angebot angenommen (an Kunde) | `Bestätigung Ihrer Beauftragung` | `Danke für Ihre Beauftragung. Als Nächstes erhalten Sie die Anzahlungsrechnung im Portal.` |
| Angebot angenommen (an Admin) | `Angebot angenommen: {Organisation}` | interne Kurzmeldung |
| Rechnung gesendet | `Ihre Rechnung {Nummer}` | `Ihre Rechnung liegt im Portal und ist bis zum {Datum} fällig. Sie können direkt dort bezahlen.` |
| Zahlung verbucht | `Zahlungseingang bestätigt` | `Wir haben Ihre Zahlung erhalten. Vielen Dank.` |
| Neue Aufgaben | `Es liegen Aufgaben für Sie bereit` | `Wir brauchen ein paar Angaben von Ihnen. Das dauert meist 15 bis 25 Minuten.` |
| Vorschau bereit | `Ihre Vorschau ist bereit` | `Sie können sich Ihre Website jetzt ansehen und Rückmeldung geben.` |
| Feedback eingegangen (an Admin) | `Neue Rückmeldung: {Organisation}` | interne Kurzmeldung |
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

Danach: `paid_at`, `marked_paid_by_user_id`, Audit-Ereignis, E-Mail an den Kunden.

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
18. Abnahme erzeugt Eintrag in `approvals` **und** Audit-Ereignis
19. Öffnungszeiten mit Bis vor Von werden abgelehnt
20. Statuswechsel erzeugt Audit-Ereignis mit Akteur

**Sicherheit:**
21. `POST` ohne CSRF-Token wird abgelehnt
22. Kunde erreicht keine `/admin`-Route
23. Admin ohne bestätigtes TOTP erreicht keine Adminroute
24. Unerlaubter Dateityp wird abgelehnt
25. Sicherheitsheader sind in allen Antworten gesetzt

**Bedienung:**
26. Alle Kernabläufe funktionieren mit deaktiviertem JavaScript
27. Willkommensstrecke erscheint einmal und danach nicht mehr
28. Jede Seite hat genau eine `<h1>`

---

## 17. Definition of Done

- [ ] Alle Screens aus §7, §8 und §9 vorhanden und bedienbar
- [ ] Alle Texte aus diesem Dokument **wörtlich** übernommen
- [ ] Alle Statuswerte zeigen dem Kunden Klartext, nirgends interne Codes
- [ ] Formate aus §4a eingehalten: deutsche Datums- und Geldformate, Europe/Berlin, 19 % USt., Beträge als Cent gespeichert, keine leeren Werte als `null` sichtbar
- [ ] Alle 28 Testfälle laufen automatisiert und grün
- [ ] `test/tenant-isolation.test.js` vorhanden, vollständig, nicht abgeschwächt
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
3. **Testbericht**: alle 28 Fälle mit Ergebnis
4. **Messwerte**: Antwortzeiten der Kernseiten, Seitengröße
5. **Offene-Punkte-Liste**: alles, was bewusst nicht gebaut wurde (§0.3), plus alles, was du melden musst
6. **Screenshot-Satz** aus der echten Oberfläche für die Website: Cockpit, Angebot, Aufgaben, Vorschau, Rechnungen, Öffnungszeiten — mit Musterdaten

**Arbeite nicht ins Blaue:** Fehlt eine Information oder widerspricht sich etwas, melde es, statt zu raten. Baue **nichts** aus §0.3 „nicht in Stufe 0", auch nicht „schon mal vorbereitet".
