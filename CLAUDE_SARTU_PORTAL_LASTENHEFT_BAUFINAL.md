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
| **Anfrageeingang aus dem Bedarfsscheck** (§4b) | direkter Aufruf im selben Programm, Admin wandelt bewusst in Kunde um |
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
| Annahme der Bedarfsschecks der eigenen SARTU-Website (§4b) | Annahme von Anfragen aus **Kundenwebsites** — das ist die „Lead-Inbox" der Stufe 1, samt Endpunkt unter `/api/` |
| Liste, Detailansicht, Status `neu` / `in_pruefung` / `angebot_erstellt` / `abgelehnt` | Pipeline-, Kanban- oder Trichteransichten |
| Notizfeld, Umwandlung in Kunde per Klick | Bewertung, Punktevergabe, Priorisierungslogik |
| Export und Löschung je Datensatz (Betroffenenrechte) | Nachfassketten, Erinnerungen, Kampagnen, Serienmails |
| Eine Benachrichtigungs-E-Mail an SARTU je Anfrage | E-Mail-Verlauf, Postfachanbindung, Vorlagenverwaltung |
| | Zuweisung an Bearbeiter, Teamfunktionen, Aktivitätenstrom |

**Merksatz:** Eine **Liste mit vier Zuständen und einem Umwandlungsknopf** — mehr nicht. Sobald etwas
automatisch nachfasst, bewertet oder verteilt, ist die Grenze überschritten.

### 0.3b Keine toten Menüpunkte, keine „kommt bald"-Bereiche

Was in Stufe 0 nicht existiert, ist in der Oberfläche **nicht sichtbar** — auch nicht ausgegraut,
auch nicht mit Hinweis. Ein Kundenbereich mit halben Funktionen wirkt unfertig und beschädigt genau
das Argument, für das er gebaut wird.

| Verboten | Warum |
|---|---|
| Menüpunkte ohne Funktion | signalisiert Baustelle |
| „Demnächst verfügbar", „in Kürze" | der Kunde fragt sich, wofür er zahlt |
| ausgegraute Schaltflächen für Stufe-1-Funktionen | dito |
| leere Bereiche ohne erklärenden Text | wirkt kaputt, nicht leer |

**Stattdessen:** Jeder leere Bereich hat einen **erklärenden Leerzustand**, der sagt, was dort
erscheinen wird und wann — bezogen auf das Projekt des Kunden, nicht auf den Ausbaustand von SARTU.
Beispiel: `Sobald die erste Fassung bereitsteht, finden Sie hier den Vorschau-Link.` — nicht
`Vorschau-Funktion in Vorbereitung.`

**Technisch:** Funktionen späterer Stufen werden über **Schalter in der Konfiguration** geführt
(`FEATURE_*` in der `.env`, Vorgabe **aus**). Ist ein Schalter aus, existiert weder Route noch
Menüpunkt noch Datenbankfeld-Anzeige. **Nicht** über auskommentierten Code, **nicht** über
versteckte Menüpunkte.

**Datenbank:** Felder für spätere Stufen werden **nicht** auf Vorrat angelegt. Eine Migration
später ist billiger als ein Datenmodell voller unbenutzter Spalten, bei denen niemand mehr weiß,
ob sie befüllt sind.

### 0.4 Portal-Screenshots

Die Website braucht Screenshots aus **dieser echten Oberfläche**. Deshalb muss das Portal mit **realistischen Musterdaten** befüllbar sein (Seed). Keine gezeichneten Fake-Dashboards. Musterdaten enthalten **keine** echten Personennamen und **keine** realistischen Rechnungsnummern.

---

## 1. Technischer Rahmen

### 1.1 Zielarchitektur — ein PHP-Projekt, vier Bereiche

**SARTU ist eine Website mit geschütztem Kundenbereich, keine App.** Das gilt technisch **und**
sprachlich. Öffentliche Seiten, Kundenbereich, Adminbereich und Serverfunktionen liegen in
**einem** modularen PHP-Projekt, in **einem** Repository, unter **einer** Domain.

```
/                     öffentliche SARTU-Website
/portal/              Kundenbereich (Login erforderlich)
/admin/               interner SARTU-Bereich (Login + Zweifaktor)
/api/                 eng begrenzte Serverfunktionen
```

**Warum ein Projekt und nicht zwei:** Der Kundenbereich ist keine zweite Anwendung, sondern der
eingeloggte Teil derselben Website. Zwei getrennte Projekte hätten zwei Deployments, zwei
Betriebsumgebungen, zwei Abhängigkeitsstände und eine Schnittstelle mit gemeinsamem Geheimnis
erzwungen — für einen Betrieb, der von **einer Person** gepflegt wird, ist das die teurere Lösung
ohne Gegenwert.

> **Wichtige Folge — der Anfrageeingang wird einfacher.** Weil Bedarfsscheck und Anfrageliste im
> selben Programm liegen, gibt es **keinen** Aufruf über das Netz, **kein** gemeinsames Geheimnis
> und **keine** Tokenprüfung. Das Formular ruft direkt den Anfragedienst auf. Alle inhaltlichen
> Schutzmaßnahmen bleiben (§4b) — nur der Übertragungsweg entfällt. Ein Geheimnis, das man nicht
> braucht, ist eine Angriffsfläche, die man sich spart.

### 1.2 Stack — entschieden, nicht zur Diskussion

| Bereich | Festlegung |
|---|---|
| Sprache | **PHP 8.3 oder 8.4** |
| Aufbau | **serverseitig gerendert**, eigene schlanke Struktur |
| Datenbank | **MySQL 8 / MariaDB 10.6+** — bewusst, damit klassisches Hosting möglich bleibt |
| Datenbankzugriff | **PDO mit vorbereiteten Anweisungen**, ausnahmslos. Nie Zeichenketten zusammensetzen |
| Passwort-Hashing (nur Admin) | `password_hash()` mit **Argon2id** |
| Verschlüsselung sensibler Felder | **AES-256-GCM** über `sodium_*` |
| Fremdbibliotheken | **Composer**, aber sparsam: Mailversand, Mollie-Bibliothek (erst Stufe 1), Umgebungsvariablen, TOTP. Sonst nichts |
| Migrationen | nummerierte SQL-Dateien mit Migrationstabelle |
| Tests | **PHPUnit**, Datenbanktests gegen **echtes MySQL/MariaDB** (kein Ersatz im Speicher) |
| Auslieferung | ein Verzeichnis per SFTP/Git, `public/` ist das einzige öffentlich erreichbare Verzeichnis |

**Verboten:** WordPress oder ein anderes CMS als Unterbau · Laravel, Symfony oder ein
vergleichbares Vollframework · React, Vue, Next oder ein anderes SPA-Framework · Node oder Fastify
als Zielsystem · Supabase oder ein vergleichbarer Backend-Dienst · Build-Pipelines fürs Frontend ·
externe CDNs · clientseitiges Routing.

> **Zu verworfenen Vorgängerständen:** Frühere Fassungen nannten Node 22 mit Fastify und EJS,
> andere einen Supabase-Prototyp. **Beides ist keine Zielarchitektur mehr.** Vorhandene Prototypen
> dürfen als **fachliche oder visuelle Referenz** dienen — Ablauf, Felder, Texte. Ihr Code wird
> **nicht** übernommen. Was daraus verwendet wird, steht begründet in `IMPLEMENTATION_PLAN.md`.

**Warum kein Vollframework:** Der Umfang ist überschaubar und die Struktur unten gibt die Ordnung
vor. Ein Vollframework bringt Konventionen, Aktualisierungszwang und Einarbeitungsaufwand für einen
Betrieb, der lange von einer Person gepflegt wird. Wächst das Projekt deutlich, ist der Wechsel eine
bewusste spätere Entscheidung — kein Grund, jetzt vorzubauen.

### 1.3 Verzeichnisstruktur (verbindlich)

```
/app
  bootstrap.php          Start: Autoload, Konfiguration, Sitzung, Fehlerbehandlung
  /helpers               kleine, zustandslose Funktionen (Formatierung, Sicherheit, SEO, Formulare)
  /data                  Datenzugriff, eine Datei je Tabelle, ausschliesslich PDO
  /services              Fachlogik (Angebot, Rechnung, Anfrage, Korrekturrunde, Freigabe)
  /views
    /layouts             Grundgerueste: oeffentlich, portal, admin
    /partials            Kopf, Fuss, Navigation, Meldungen
    /components          wiederverwendbare Bausteine (Karte, Tabelle, Formularfeld, Status)
    /pages               eine Datei je Seite
/public                  EINZIGES oeffentlich erreichbares Verzeichnis
  index.php              Einstieg fuer alles
  /assets                CSS, JS, Bilder, Schriften — alle selbst gehostet
/portal                  Routen des Kundenbereichs
/admin                   Routen des Adminbereichs
/api                     eng begrenzte Serverfunktionen
/storage                 Uploads — AUSSERHALB von /public, nie direkt erreichbar
/migrations              nummerierte SQL-Dateien
/tests                   PHPUnit
```

**Regeln:**
- **Nur `/public` ist erreichbar.** Der Webserver zeigt dorthin. Liegt `/app` im Netz, ist das ein Sicherheitsfehler, kein Schönheitsfehler
- Eine Seite besteht aus **Layout + Partials + Komponenten**. Kein Kopieren von Markup zwischen Seiten
- Fachlogik gehört in `/app/services`, **nie** in eine Ansichtsdatei
- Datenbankzugriff **ausschließlich** über `/app/data`. Kein SQL in Seiten, Diensten oder Ansichten
- Kunden- und Adminzugriff nutzen **getrennte** Datenzugriffswege (§3 Regel 2a)

### 1.4 Anforderungen an die Betriebsumgebung

Der konkrete Anbieter ist offen (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4). Er **muss** liefern:

| Muss | Warum |
|---|---|
| PHP 8.3+ mit `pdo_mysql`, `sodium`, `mbstring`, `intl`, `fileinfo`, `openssl` | Grundfunktionen und Verschlüsselung |
| MySQL 8 / MariaDB 10.6+ mit eigenem Benutzer je Umgebung | Datenhaltung, Trennung Test/Produktion |
| **HTTPS erzwungen**, eigenes Zertifikat, HSTS möglich | Sitzungen und Anmeldung |
| **Schreibbares Verzeichnis außerhalb des Webroots** | Uploads dürfen nicht direkt abrufbar sein |
| **Zeitgesteuerte Aufgaben** (Cron), mindestens täglich | IP-Löschung nach 30 Tagen, Löschfristen, Überfälligkeitsprüfung |
| **Zuverlässiger Mailversand** über SMTP mit eigener Domain, SPF/DKIM/DMARC | Anmeldelinks. Kommen sie nicht an, funktioniert nichts |
| Automatische Sicherung von Datenbank **und** Upload-Verzeichnis, Wiederherstellung getestet | Kundendaten |
| Serverstandort Deutschland oder EU | Datenschutz |

**Wenn ein Punkt fehlt, ist der Tarif ungeeignet — das ist keine Verhandlungssache.** Besonders
Cron und verlässlicher Mailversand fehlen bei einfachen Paketen häufig. **Vor** der Umsetzung
praktisch prüfen: eine Testmail an eine Fremdadresse (kommt sie im Posteingang an, nicht im Spam?)
und ein Cronlauf, der eine Datei schreibt.

**Spätestens für Stufe 1** (Mollie-Rückrufe, Domainprozesse) braucht es eine Umgebung mit stabilen
eingehenden Aufrufen und längeren Laufzeiten. Wird das mit einem einfachen Paket knapp, ist der
Wechsel auf einen kleinen eigenen Server der Normalfall, kein Scheitern.

### 1.5 Umgebungen und Konfiguration

**Umgebungen:** `local` (Entwicklung, Seed-Daten) · `staging` · `production`.

Konfiguration ausschließlich über Umgebungsvariablen bzw. eine `.env` **außerhalb** von `/public`.
`.env.example` gehört ins Repository, `.env` **niemals**.

**Erforderliche Werte:**
`DB_HOST` `DB_NAME` `DB_USER` `DB_PASS` · `SESSION_SECRET` · `ENC_KEY` (32 Byte, base64) ·
`SMTP_HOST` `SMTP_PORT` `SMTP_USER` `SMTP_PASS` `MAIL_FROM` · `ADMIN_NOTIFY_EMAIL` ·
`BASE_URL` · `ADMIN_TOTP_ISSUER` · `STORAGE_DIR` · `APP_ENV`

**Kein `INTAKE_TOKEN` mehr.** Er war nur nötig, solange Website und Kundenbereich getrennte
Anwendungen waren (§1.1).

### 1.6 Was der Kunde erlebt — und wie darüber gesprochen wird

Der Kunde soll **nicht** denken „ich muss noch ein Werkzeug lernen", sondern:

> Ich melde mich bei SARTU an und sehe, was als Nächstes ansteht.

| Nach außen verwenden | Nach außen **nie** verwenden |
|---|---|
| Kundenbereich · Ihr Bereich · Anmeldung · Ihr Projekt | App · Software · SaaS · Plattform · Tool |
| „Sie melden sich an und sehen Ihr Projekt" | Dashboard · Control-Plane · System · Instanz |

Intern darf ein Begriff wie „Adminbereich" stehen. **Kundensichtbare** Oberflächentexte, E-Mails und
Website-Copy halten sich an die linke Spalte. Der USP ist nicht die Software, sondern **dass der
Kunde nichts suchen muss**.


### 1.4a Betreiberdaten — gepflegt im internen Bereich, nicht im Code

**Anschrift, Kontaktdaten und Steuerangaben stehen nirgends im Quelltext.** Sie liegen als
Einstellungen in der Datenbank und werden unter `/admin/einstellungen/betrieb` gepflegt.

| Feld | Verwendung |
|---|---|
| `firmenname`, `rechtsform` | Impressum, Fußbereich, Rechnungen, strukturierte Daten |
| `strasse`, `plz`, `ort`, `land` | Impressum, Rechnungen, `LocalBusiness` |
| `telefon`, `email` | Impressum, Fußbereich, Kontaktseite, Absender |
| `ust_id` **oder** `steuernummer` | Impressum, Rechnungen |
| `registergericht`, `registernummer` | nur bei eingetragener Gesellschaft |
| `inhaltlich_verantwortlich` | Impressum |
| `bank_iban`, `bank_bic`, `bank_institut` | Rechnungen |
| `kleinunternehmer` (ja/nein) | **steuert die Preisdarstellung der gesamten Website** |

**Warum das nicht in den Code gehört — drei Gründe, jeder allein ausreichend:**

1. **Eine falsche Anschrift im Impressum ist abmahnfähig.** Sie muss in Minuten korrigierbar sein, nicht über einen Entwicklungs- und Auslieferungsvorgang
2. **Eine Quelle für alles.** Fußbereich, Impressum, Rechnungen, E-Mails und strukturierte Daten ziehen dieselben Werte. Genau diese Übereinstimmung verlangt `GEO_DISCOVERY_CHECKLIST.md` §3 — uneinheitliche Angaben führen dazu, dass KI-Systeme gar nichts zuordnen
3. **Das Feld `kleinunternehmer` ist geschäftskritisch.** Steht es auf „ja", darf **nirgends** „zzgl. USt." erscheinen und keine Umsatzsteuer ausgewiesen werden — weder auf der Website noch auf Rechnungen. Ein falscher Steuerausweis ist nach § 14c UStG geschuldet, auch wenn er versehentlich war

**Regeln:**

- [ ] Jede Änderung erzeugt einen vollständigen Prüfeintrag mit altem Wert, neuem Wert und Grund (§3.9) — es sind rechtlich erhebliche Angaben
- [ ] Pflichtfelder sind serverseitig geprüft: keine leeren Werte, Postleitzahl formal gültig, entweder `ust_id` oder `steuernummer` gesetzt
- [ ] **Ein Postfach genügt nicht.** Das Impressum verlangt eine ladungsfähige Anschrift; die Oberfläche weist beim Speichern darauf hin
- [ ] Werden Anschrift oder Firmenname geändert, wird sichtbar vermerkt, dass Impressum und Rechnungsvorlagen geprüft werden müssen
- [ ] Die Werte werden **nie** aus einer Anfrage übernommen, sondern nur über den Adminbereich gesetzt

#### Auswirkung auf die Startsperre

Website-Lastenheft §14a prüft ab jetzt **nicht mehr auf Platzhalter in Vorlagen**, sondern auf den
Zustand dieser Einstellungen. Die produktive Veröffentlichung bricht ab, wenn:

- ein Pflichtfeld leer ist
- weder `ust_id` noch `steuernummer` gesetzt ist
- die Rechtstexte noch den Vermerk `ENTWURF` tragen (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §2)

**Abbruch, keine Warnung.** Eine Warnung wird weggeklickt.

#### Die Rechtstexte selbst

`impressum`, `datenschutz` und `agb` liegen ebenfalls als Inhalte in der Datenbank, mit einem
Zustand je Text: `entwurf` · `in_pruefung` · `freigegeben`. Nur `freigegeben` wird öffentlich
ausgeliefert; `entwurf` ist ausschließlich angemeldet im Adminbereich sichtbar.

**Den Zustand auf `freigegeben` setzen darf nur ein Mensch**, mit Datum und Namen der prüfenden
Stelle. Kein automatischer Übergang, keine Voreinstellung.

### 1.5 Ersteinrichtung — geführte Installation im internen Bereich

**Ziel:** Nach dem Hochladen der Dateien und dem Anlegen einer leeren Datenbank richtet sich das
System selbst ein. Kein Bearbeiten von Konfigurationsdateien auf dem Server, kein Einspielen von
SQL-Dateien von Hand.

**Ablauf beim ersten Aufruf**, solange die Installation nicht abgeschlossen ist: Jeder Aufruf
außer der Einrichtung leitet auf `/admin/setup`. Sechs Schritte, jeder einzeln prüfbar:

> **Neu gefasst am 01.08.2026 nach externer Prüfung.** Die vorige Fassung war in dieser Form
> nicht ausführbar. Drei Fehler, alle in der Reihenfolge:
>
> | Fehler | Folge |
> |---|---|
> | Schritt 5 erhob **kein Passwort**, §7 verlangt für Admins „E-Mail + Passwort (Argon2id) + TOTP" | Der Admin hätte sich nie anmelden können |
> | `ENC_KEY` entstand erst in Schritt 6, das TOTP-Geheimnis wird aber in Schritt 5 **verschlüsselt** abgelegt (`totp_secret_enc`) | Verschlüsseln ohne Schlüssel |
> | Schritt 6 legte `operator_settings` an. Die Tabelle hat **sieben** `NOT NULL`-Felder und eine `CHECK`-Bedingung auf die Steuerangabe — **keines davon wurde je erhoben** | Das `INSERT` wäre gescheitert |
>
> **„Kein Vorgabepasswort" hieß: kein voreingestelltes Passwort.** Es hieß nie: gar keins. Der
> Admin **vergibt** eines.

| # | Schritt | Was geprüft wird |
|---|---|---|
| 1 | **Umgebung** | PHP-Version, die sechs Erweiterungen aus §1.4, Schreibrechte auf `/storage`, Verzeichnis außerhalb des Webroots erreichbar |
| 2 | **Datenbank** | Zugangsdaten eingeben, **Verbindung sofort testen**, Zeichensatz und Kollation prüfen, erst dann speichern |
| 3 | **Schlüssel** | `SESSION_SECRET` und `ENC_KEY` erzeugen (je 32 Byte aus `random_bytes`, base64) und in die `.env` schreiben. **Vor** allem, was verschlüsselt wird |
| 4 | **Migrationen** | Vorprüfung, dann jede Migration **einzeln** ausführen und einzeln protokollieren. Bei Fehler: sofortiger Abbruch mit Klartextmeldung und der Nummer der gescheiterten Migration. Ablauf und Wiederanlauf siehe unten |
| 5 | **Mailversand** | SMTP-Zugang eingeben, **Testmail an eine eingegebene Adresse senden**, Empfang muss bestätigt werden, bevor es weitergeht |
| 6 | **Betreiberdaten** | Die Pflichtfelder aus `operator_settings`: Firmenname, Straße, PLZ, Ort, Land, E-Mail, inhaltlich Verantwortlicher **und genau eine Steuerangabe** (USt-IdNr. **oder** Steuernummer). Jedes Feld nach `trim()` mindestens ein Zeichen |
| 7 | **Erstes Adminkonto** | E-Mail, Name, **selbst vergebenes Passwort** (Argon2id, mindestens 12 Zeichen), TOTP einrichten und **einen Code bestätigen**. Kein Vorgabepasswort, kein Standardkonto |
| 8 | **Abschluss** | Cron-Befehl zum Kopieren anzeigen, `operator_settings.setup_completed_at` setzen, Sperrdatei schreiben, Einrichtung sperren |

**Ergebnis:** `.env` liegt geschrieben vor, das Schema steht, die Betreiberdaten stehen, ein
Adminkonto mit Passwort und geprüftem TOTP existiert, eine Testmail ist nachweislich angekommen.

> **Warum Schritt 6 die Betreiberdaten erzwingt, obwohl Anschrift und Rechtsform „offen" sind.**
> Sie sind es für die **Außendarstellung** — Impressum, Rechnungen, Angebote. Die Tabelle braucht
> aber beim `INSERT` Werte, sonst greifen `NOT NULL` und die `CHECK`-Bedingung. **Vorläufige Werte
> sind erlaubt** und im Adminbereich jederzeit änderbar. Die Startsperre aus §1.4a verhindert
> weiterhin, dass mit Platzhaltern nach außen gegangen wird — sie prüft auf Inhalt, nicht auf
> Vorhandensein.

> **Die Sperre der Einrichtung liegt an zwei Orten, beide benannt:**
>
> | Ort | Was |
> |---|---|
> | Datenbank | `operator_settings.setup_completed_at` (datetime, NULL bis zum Abschluss) |
> | Datei | `/storage/installed.lock`, außerhalb des Webroots |
>
> **`/admin/setup` liefert 404, sobald einer von beiden gesetzt ist.** Nicht beide — einer genügt,
> sonst hebt ein gelöschtes Lockfile die Sperre auf. Zurücksetzen ist über das Netz nicht möglich.

#### Schritt 3 im Detail — warum „wird zurückgerollt" hier falsch wäre

**Der technische Sachverhalt:** MySQL und MariaDB führen bei schemaverändernden Befehlen —
`CREATE TABLE`, `ALTER TABLE`, `DROP TABLE`, `CREATE INDEX` — ein **implizites Commit** aus. Eine
offene Transaktion wird dadurch beendet, *bevor* der Befehl läuft. Ein `ROLLBACK` nach einer
gescheiterten Migration nimmt die vorher gelaufenen Tabellen **nicht** zurück. Wer „alles in eine
Transaktion, bei Fehler zurückrollen" schreibt, beschreibt PostgreSQL, nicht MySQL.

Wird das nicht berücksichtigt, entsteht genau der Zustand, den die Regel verhindern sollte: ein
halb migriertes Schema, das sich für vollständig hält.

**Stattdessen — vier Bestandteile:**

| Bestandteil | Was konkret passiert |
|---|---|
| **Vorprüfung** | Vor der ersten Migration: Ist die Datenbank leer? Reichen die Rechte für `CREATE`, `ALTER`, `INDEX`, `REFERENCES`? Stimmen Zeichensatz (`utf8mb4`) und Kollation? Ist `SET time_zone = '+00:00'` gesetzt (§4.1)? **Eine nicht leere Datenbank bricht ab** — die Einrichtung migriert nicht in fremden Bestand hinein |
| **Einzelprotokoll** | Tabelle `schema_migrations` (`version`, `checksum`, `applied_at`, `duration_ms`). Sie wird **als Erstes** angelegt. Jede Migration wird einzeln ausgeführt und **unmittelbar nach Erfolg** eingetragen — nicht am Ende im Block |
| **Prüfsumme** | Je Migrationsdatei wird ein SHA-256 über den Dateiinhalt gespeichert. Beim Start wird jede bereits eingetragene Migration gegen ihre Datei geprüft. **Abweichung = Abbruch:** Jemand hat eine ausgelieferte Migration nachträglich geändert, der Datenbankstand ist dann unbekannt |
| **Wiederanlauf** | Ein erneuter Aufruf setzt bei der **ersten nicht eingetragenen** Migration fort. Migrationen sind so zu schreiben, dass sie bei teilweiser Ausführung nicht kollidieren — jede Migration verändert **genau ein** Schemaobjekt, nie mehrere Tabellen in einer Datei |

**Wenn der Wiederanlauf nicht greift:** Ist eine Migration mittendrin gescheitert — Tabelle
angelegt, Fremdschlüssel nicht —, ist der Stand nicht zuverlässig reparierbar. Dann gilt der
einzige ehrliche Weg: **neue leere Datenbank, Einrichtung von vorn.** Die Oberfläche sagt genau
das, mit dem Namen der gescheiterten Migration und der Fehlermeldung des Servers im Klartext.

Ein „Reparieren"-Knopf wird **nicht** gebaut. Er würde raten müssen.

> **Warum das hier so ausführlich steht:** Die frühere Fassung dieses Abschnitts versprach eine
> Rücknahme, die es auf diesem Datenbanksystem nicht gibt. Das ist der gefährlichste Fehlertyp in
> einer Einrichtungsstrecke — er wird erst sichtbar, wenn schon etwas schiefgegangen ist.

#### 1.5a Spätere Migrationen — der zweite Weg, ohne den Stufe B nicht einspielbar ist

Die Einrichtung aus §1.5 bricht bei einer **nicht leeren** Datenbank ab. Das ist richtig für die
Erstinstallation. Ohne einen zweiten Weg wäre danach aber **keine einzige Migration mehr
möglich** — und `REIHENFOLGE.md` sieht ausdrücklich vor, dass Stufe B drei Tabellen nachträglich
hinzufügt. Diese Lücke stammt aus der Fassung vom 30.07.2026 und wird hier geschlossen.

**Zwei getrennte Wege, unterschiedliche Voraussetzungen:**

| | Ersteinrichtung (§1.5) | Nachträgliche Migration (§1.5a) |
|---|---|---|
| Aufruf | `/admin/setup` im Browser | **Befehlszeile auf dem Server**, kein Webaufruf |
| Voraussetzung | Datenbank **leer** | Datenbank **nicht leer**, `schema_migrations` vorhanden und lückenlos |
| Anmeldung | keine (es gibt noch kein Konto) | Dateizugriff auf dem Server |
| Sperre danach | dauerhaft, `404` | keine — der Weg bleibt offen |

**Der Befehl:** `php bin/migrate.php` mit drei Unterbefehlen.

| Unterbefehl | Was er tut |
|---|---|
| `status` | Zeigt eingespielte und offene Migrationen. **Ändert nichts.** Der Normalfall vor jedem Einspielen |
| `up` | Spielt alle offenen Migrationen ein, einzeln, mit Protokolleintrag nach jedem Erfolg |
| `verify` | Prüft nur die Prüfsummen aller eingetragenen Migrationen gegen die Dateien |

**Die Regeln aus „Schritt 3 im Detail" gelten unverändert weiter:** Prüfsummenabgleich vor dem
Start, Einzelausführung, Eintrag unmittelbar nach Erfolg, Abbruch mit Nennung der Datei.

**Zusätzlich, weil hier echte Daten liegen:**

- [ ] **`up` verlangt eine vorherige Sicherung.** Der Befehl fragt nach dem Pfad der Sicherungsdatei und prüft, dass sie existiert und nicht leer ist. Ohne Angabe: Abbruch
- [ ] **Kein `up` über das Netz.** Kein Webaufruf, kein Endpunkt unter `/api/`, keine Schaltfläche im Adminbereich. Wer migrieren darf, hat ohnehin Dateizugriff
- [ ] **Wartungsmodus während `up`.** Kunden- und Adminbereich liefern `503` mit Klartext; nach Erfolg wird er automatisch aufgehoben, nach Abbruch **nicht**
- [ ] Jeder Lauf schreibt ein Audit-Ereignis mit Startzeit, Endzeit, eingespielten Versionen und Ergebnis
- [ ] **Migrationen werden nie geändert, nur ergänzt.** Eine bereits eingespielte Datei anzufassen bricht den Prüfsummenabgleich — das ist beabsichtigt
- [ ] **Kein `down`.** Es gibt keinen Rückwärtsbefehl. Der Grund steht oben: schemaverändernde Befehle lösen ein implizites Commit aus. Wer zurück muss, spielt die Sicherung ein

> **Der Satz, der die Erwartung setzt:** Vorwärts ist ein Befehl. Rückwärts ist eine Sicherung.
> Alles andere wäre ein Versprechen, das die Datenbank nicht hält.

#### Sicherheitsregeln — nicht verhandelbar

Eine Einrichtungsstrecke ist die klassische Angriffsfläche. Wer sie erreicht, übernimmt das System.

- [ ] **Nach Abschluss ist `/admin/setup` dauerhaft gesperrt** und liefert `404`. Die Sperre wird in der Datenbank **und** als Datei in `/storage` vermerkt — beide müssen fehlen, damit die Strecke wieder aufgeht
- [ ] Die Sperre ist **über das Netz nicht aufhebbar**. Ein Zurücksetzen erfordert Dateizugriff auf dem Server
- [ ] Zugangsdaten werden **nie** angezeigt, nie protokolliert, nie in eine Fehlermeldung geschrieben — auch nicht teilweise
- [ ] **Rate-Limit** auf jeden Schritt, damit die Strecke nicht als Passwortprobierfläche dient
- [ ] Läuft die Einrichtung über unverschlüsseltes HTTP, wird sie **abgebrochen** — nicht gewarnt. Zugangsdaten gehen nicht im Klartext über die Leitung. **Eine einzige Ausnahme**, siehe unten
- [ ] Schlägt Schritt 3 fehl, gilt der Ablauf aus „Schritt 3 im Detail": Abbruch bei der gescheiterten Migration, Wiederanlauf oder neue leere Datenbank. **Keine Rücknahme versprechen**
- [ ] Die Einrichtung legt **kein** Beispielkonto und **keine** Beispieldaten in der produktiven Umgebung an

#### Die HTTP-Ausnahme — eng begrenzt, sonst blockiert sich die Entwicklung selbst

`ENTWICKLUNGSUMGEBUNG.md` schreibt `http://localhost:8080` vor. Ein bedingungsloser HTTPS-Zwang
macht die Einrichtung dort unbenutzbar — und der wahrscheinlichste Ausweg wäre, die Prüfung ganz
zu entfernen. Deshalb steht die Ausnahme hier ausformuliert, statt sie später improvisieren zu
lassen.

**Die Einrichtung läuft über HTTP nur, wenn alle drei Bedingungen gleichzeitig zutreffen:**

1. `APP_ENV=local` — gelesen **aus der Serverumgebung**, nie aus einer Anfrage, nie aus einem Formularfeld
2. Die Gegenstelle ist eine Loopback-Adresse: `127.0.0.1` oder `::1`. Geprüft wird `REMOTE_ADDR` **direkt**, ohne Weiterleitungs-Kopfzeilen
3. Der angefragte Hostname ist `localhost`, `127.0.0.1` oder `[::1]` — mit oder ohne Portangabe

Trifft eine Bedingung nicht zu, wird abgebrochen. Kein Bestätigungsdialog, kein „trotzdem
fortfahren".

**Was ausdrücklich nicht als Nachweis gilt:**

| Nicht ausreichend | Warum |
|---|---|
| `X-Forwarded-Proto: https` | Frei setzbar, solange keine Liste vertrauenswürdiger Zwischenstellen konfiguriert ist. Ohne diese Liste wird die Kopfzeile **ignoriert** |
| `X-Forwarded-For` für die Loopback-Prüfung | Ebenso frei setzbar. Bedingung 2 prüft ausschließlich `REMOTE_ADDR` |
| Ein Hostname, der `localhost` nur enthält | `localhost.angreifer.de` ist nicht `localhost`. Verglichen wird der **vollständige** Hostname, nicht ein Teilstring |
| `APP_ENV` aus der `.env`, wenn die `.env` noch gar nicht existiert | Vor Schritt 6 gibt es keine `.env`. Fehlt `APP_ENV` in der Serverumgebung, gilt **produktiv** — also HTTPS-Zwang |

**Der Test dazu gehört in die Testfälle:** Aufruf von `/admin/setup` über HTTP mit `APP_ENV=production`
muss abbrechen, auch wenn die Anfrage von `127.0.0.1` kommt.

#### Was die Einrichtung ausdrücklich **nicht** kann

Damit keine falsche Erwartung entsteht:

| Nicht automatisierbar | Warum |
|---|---|
| **Rechtstexte** | Kommen aus anwaltlicher Prüfung (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §2). Die Startsperre aus Website-Lastenheft §14a bleibt davon unberührt |
| **Mailserver einrichten** | SPF, DKIM und DMARC werden beim Anbieter und im DNS gesetzt. Die Einrichtung **prüft** den Versand, sie richtet ihn nicht ein |
| **Cron eintragen** | Der Befehl wird zum Kopieren angezeigt; eintragen muss ihn der Mensch beim Anbieter |
| **HTTPS besorgen** | Zertifikat und Weiterleitung liegen beim Anbieter |
| **Standortentscheidung** | §1 — eine Geschäftsentscheidung, kein Konfigurationswert |

> **Der Satz, der die Erwartung setzt:** Die Ersteinrichtung nimmt dir **alles ab, was die
> Anwendung über sich selbst weiß** — Schema, Schlüssel, Konto, Konfiguration. Sie nimmt dir
> **nichts** ab, was außerhalb liegt: Server, DNS, Mail, Recht. Beides zusammen ist der Unterschied
> zwischen „läuft nach dem Hochladen" und „ist startklar".

#### Prüfliste vor dem Livegang

- [ ] Einrichtung auf einer **leeren** Datenbank vollständig durchgespielt
- [ ] Nach Abschluss liefert `/admin/setup` einen `404`
- [ ] Datei- und Datenbanksperre beide vorhanden
- [ ] Testmail ist in einem echten Posteingang angekommen, nicht im Spam
- [ ] Cronlauf schreibt nachweislich
- [ ] Einrichtung gegen eine **nicht leere** Datenbank gestartet → bricht ab, migriert nicht hinein
- [ ] Eine Migrationsdatei nachträglich geändert → Prüfsummenabgleich schlägt an, Abbruch mit Nennung der Datei
- [ ] Abbruch mitten in Schritt 3, dann erneut aufgerufen → setzt bei der ersten nicht eingetragenen Migration fort
- [ ] `/admin/setup` über HTTP mit `APP_ENV=production` von `127.0.0.1` → **bricht ab** (die Loopback-Ausnahme greift nur bei `APP_ENV=local`)
- [ ] `/admin/setup` über HTTP mit `APP_ENV=local` von einer fremden Adresse → bricht ab
- [ ] `X-Forwarded-Proto: https` bei tatsächlichem HTTP → wird ignoriert, bricht ab

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
   Der Test `tests/TenantIsolationTest.php` ist **unantastbar**: nie löschen, nie abschwächen, um grün zu werden.
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

### 4.0 Typabbildung — verbindlich

Zielsystem ist **MySQL 8 / MariaDB 10.6+** (§1.4). Frühere Fassungen dieses Dokuments nannten an
einigen Stellen PostgreSQL-Typen — sie stammen aus dem abgelösten Stack und sind **ungültig**.
Verbindlich ist diese Abbildung. Wo unten noch ein alter Name steht, gilt die rechte Spalte.

| Gemeint | In MySQL / MariaDB | Warum |
|---|---|---|
| Schlüssel (`uuid`) | `CHAR(36) CHARACTER SET ascii COLLATE ascii_bin` | MySQL 8 hat keinen UUID-Typ. `ascii` statt `utf8mb4` spart je Schlüssel 108 Byte und hält die Indizes schmal. Der Wert wird **in PHP** erzeugt, nicht in der Datenbank |
| Zeitpunkt (`timestamptz`) | `DATETIME` | siehe Zeitzonenregel unten |
| Text ohne Beachtung der Groß-/Kleinschreibung (`citext`) | `VARCHAR(n)` mit `utf8mb4_unicode_ci` | Diese Kollation vergleicht ohnehin ohne Beachtung der Schreibweise — damit ist `UNIQUE` auf E-Mail-Adressen automatisch das, was `citext` geleistet hat |
| Freitext ohne Index | `TEXT` | |
| Binärdaten (`bytea`) | `VARBINARY(n)` | |
| Strukturierte Ablage (`jsonb`) | `JSON` | in MySQL 8 nativ, in MariaDB ein geprüfter Textwert — für beide Systeme derselbe Ausdruck |
| IP-Adresse (`inet`) | `VARCHAR(45)` | fasst auch die längste IPv6-Schreibweise. Bewusst als Text, weil das Feld nach 30 Tagen geleert und nie berechnet wird |
| Wahrheitswert | `TINYINT(1)` | |
| `now()` | `CURRENT_TIMESTAMP` | |

**Tabellenvorgabe:** `ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci`

**Zeitzone — der Punkt, an dem es sonst still schiefgeht.** `DATETIME` speichert **keine** Zeitzone.
Alle Zeitpunkte werden in **UTC** abgelegt (§8). Damit `CURRENT_TIMESTAMP` auch UTC schreibt, setzt
die Anwendung unmittelbar nach dem Verbindungsaufbau:

```sql
SET time_zone = '+00:00';
```

Ohne diese Zeile landen Vorgabewerte in der lokalen Zeit des Datenbankservers — falsch, aber
unauffällig, weil im Sommer nur zwei Stunden daneben. Die Umrechnung nach **Europe/Berlin** geschieht
ausschließlich bei der Anzeige, in PHP.

**Längen:** `TEXT` überall dort, wo frei geschrieben wird. **`VARCHAR(n)` überall dort, wo ein Index,
ein `UNIQUE` oder ein Fremdschlüssel darauf liegt** — MySQL kann `TEXT` nicht ohne Längenangabe
indizieren. Die betroffenen Felder tragen unten eine ausdrückliche Länge.

**Prüfbedingungen** (`CHECK`) werden von MySQL ab 8.0.16 und MariaDB ab 10.2 durchgesetzt; beide
liegen unter der Mindestversion aus §1.4. Sie sind also verbindlich und kein Kommentar.

### 4.1 Gemeinsame Felder

Alle Tabellen:

```sql
id         CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

Fremdschlüssel mit `ON DELETE RESTRICT`.

**Eine Ausnahme:** `schema_migrations` (§1.5) folgt diesem Schema **nicht**. Sie ist keine
Fachtabelle, sondern das Protokoll der Einrichtung, hat `version` als Schlüssel und existiert,
bevor die erste Fachtabelle angelegt wird. Sie zählt deshalb **nicht** zu den zwanzig Tabellen aus
`REIHENFOLGE.md`.

### `organizations`
| Feld | Typ | Hinweis |
|---|---|---|
| `legal_name` | text, NOT NULL | rechtlicher Unternehmensname |
| `brand_name` | text | sichtbarer Name, falls abweichend |
| `street`, `postal_code`, `city` | text | Rechnungsanschrift |
| `vat_id` | text | optional |
| `contact_email` | varchar(255), NOT NULL | |
| `contact_phone` | text | |
| `archived_at` | datetime | |

### `users`
| Feld | Typ | Hinweis |
|---|---|---|
| `organization_id` | char(36) | **NULL bei Admins**, **NOT NULL bei Kunden** — als Datenbankbedingung erzwingen: `CHECK ((role = 'admin' AND organization_id IS NULL) OR (role = 'kunde' AND organization_id IS NOT NULL))`. Siehe §3 Regel 2a |
| `email` | varchar(255), NOT NULL, UNIQUE | |
| `first_name`, `last_name` | text | |
| `role` | varchar(16), NOT NULL | `kunde` \| `admin` |
| `password_hash` | text | nur Admin (Argon2id) |
| `totp_secret_enc` | varbinary(255) | nur Admin, AES-256-GCM |
| `welcome_seen_at` | datetime | steuert die Willkommensstrecke |
| `last_login_at` | datetime | |
| `archived_at` | datetime | |

### `login_tokens`
`user_id` (char(36)) · `token_hash` (varchar(64), NOT NULL, UNIQUE) · `expires_at` (datetime) · `used_at` (datetime) · `requested_ip` (varchar(45))

### `sessions`
`user_id` (char(36)) · `token_hash` (varchar(64), NOT NULL, UNIQUE) · `expires_at` (datetime) · `user_agent` (varchar(255)) · `ip` (varchar(45))

### `leads`
Die Anfragen aus dem Bedarfsscheck der öffentlichen Website (§4b).

| Feld | Typ | Hinweis |
|---|---|---|
| `submission_id` | char(36), NOT NULL, **UNIQUE** | von der Website erzeugt — verhindert Doppeleinreichung (§4b.3) |
| `submitted_at` | datetime, NOT NULL | Zeitpunkt laut Website |
| `payload` | json, NOT NULL | vollständige Antworten, unverändert wie gesendet |
| `first_name`, `last_name` | text, NOT NULL | |
| `company` | text, NOT NULL | |
| `email` | varchar(255), NOT NULL | kleingeschrieben gespeichert |
| `phone` | text | |
| `preferred_contact` | text, NOT NULL | `email` \| `portal` |
| `recommended_package` | text | vom Regelwerk der Website vorgeschlagen |
| `flag` | text, NOT NULL, default `standard` | `standard` \| `gelb` \| `orange` \| `rot` |
| `status` | text, NOT NULL | `neu` \| `in_pruefung` \| `angebot_erstellt` \| `abgelehnt` |
| `b2b_confirmed` | boolean, NOT NULL | muss `true` sein, sonst wird nicht gespeichert |
| `privacy_confirmed` | boolean, NOT NULL | dito |
| `source_ip` | varchar(45) | **wird nach 30 Tagen geleert**, s. §4b.4 |
| `branche_vorbelegt` | varchar(60) | gesetzt, wenn die Anfrage von einer Branchenseite kommt (Website-Lastenheft §10a). **Aus dem Seitenkontext, nie aus einem Formularfeld des Besuchers** — sonst ließe sie sich manipulieren |
| `landing_page` | text | erste aufgerufene Seite (**nur Pfad**, ohne Abfragezeichenfolge) |
| `referrer_host` | text | **nur der Hostname** der verweisenden Seite, nie die vollständige Adresse |
| `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content` | text | Kampagnenkennzeichen, s. §4b.7 |
| `click_id` | text | `gclid`, `gbraid` oder `wbraid`, falls vorhanden — Feld speichert Wert **und** Art |
| `self_reported_source` | text | Antwort auf „Wie sind Sie auf uns aufmerksam geworden?" |
| `delete_after` | date, NOT NULL | Eingang + 6 Monate; entfällt bei Umwandlung |
| `converted_organization_id` | char(36) | gesetzt bei Umwandlung |
| `admin_note` | text | |

### `projects`
| Feld | Typ | Hinweis |
|---|---|---|
| `organization_id` | char(36), NOT NULL | |
| `title` | text, NOT NULL | z. B. „Firmenwebsite Musterbau" |
| `package` | text, NOT NULL | `start` \| `wachstum` \| `platzhirsch` \| `sonderprojekt` |
| `included_feedback_rounds` | integer, NOT NULL | aus dem Paket vorbelegt: Start **1**, Wachstum **2**, Platzhirsch **2**, Sonderprojekt nach Angebot |
| `protection_level` | text | `s` \| `m` \| `l` — aus dem Paket abgeleitet |
| `protection_started_on` | date | **Betriebsbeginn**, s. §5.7 |
| `protection_min_term_until` | date | Betriebsbeginn + 12 Monate |
| `status` | text, NOT NULL | siehe §5.1 |
| `paused_from_status` | text | nur gesetzt, solange `status = pausiert`. Beim Fortsetzen wird **auf diesen Wert** zurückgesetzt, nicht auf einen frei gewählten (§5.1a) |
| `pause_reason` | text | Grund der Pause — **wird dem Kunden angezeigt** (§5.1) |
| `next_step_text` | text | vom Admin gesetzt, überschreibt die Ableitung |
| `next_step_url` | text | optionaler Sprungziel-Pfad im Portal |
| `preview_url` | text | Vorschau-Link |
| `preview_published_at` | datetime | |
| `live_url` | text | |
| `launched_at` | datetime | |
| `archived_at` | datetime | |

### `offers`
Ein angenommenes Angebot ist die **vertragliche Grundlage**. Es muss deshalb alles enthalten,
was später strittig werden kann — nicht nur den Preis.

| Feld | Typ | Hinweis |
|---|---|---|
| `project_id` | char(36), NOT NULL | |
| `number` | text, UNIQUE, NOT NULL | Format §4a |
| `status` | text, NOT NULL | §5.2 |
| `package` | text, NOT NULL | `start` \| `wachstum` \| `platzhirsch` \| `sonderprojekt` |
| `summary` | text, NOT NULL | Ausgangslage und Ziel in Kundensprache |
| `sitemap` | text, NOT NULL | die geplanten Seiten, eine je Zeile |
| `inclusions` | text, NOT NULL | was enthalten ist |
| `exclusions` | text, NOT NULL | was **nicht** enthalten ist — Pflichtfeld, nie leer. **Muss eine Zeile zur Barrierefreiheit enthalten**, s. u. |
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
| `valid_until` | date, NOT NULL | Vorbelegt auf **30 Kalendertage ab Versand**, im Adminbereich änderbar — s. unten |
| `sent_at` | datetime | |
| `accepted_at` | datetime | |
| `accepted_by_user_id` | char(36) | |
| `accepted_ip` | varchar(45) | |
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

#### Vorbelegung `valid_until` — 30 Tage

> **Ergänzt am 02.08.2026.** Die Zahl fehlte. `valid_until` stand als `NOT NULL` im Datenmodell,
> ohne dass irgendwo eine Frist genannt war. Die Bau-Session der Stufe A1 hat das gemeldet, statt
> zu raten — richtig so.

**Entschieden: 30 Kalendertage, gerechnet ab dem Tag des Versands** (`sent_at`). Beim Anlegen eines
Angebots ist das Feld damit vorbelegt. Es bleibt im Adminbereich änderbar — 30 Tage sind die
Vorbelegung, keine Obergrenze und keine Untergrenze.

| Warum 30 | |
|---|---|
| **Die Erinnerungsmail braucht Vorlauf** | §10 verschickt `Ihr Angebot gilt noch bis {Datum}` **drei Tage** vor Ablauf. Bei 14 Tagen fällt sie in die zweite Woche, bei 7 Tagen ist sie sinnlos |
| **Der Empfänger ist ein Betriebsinhaber** | 72 % der Handwerksbetriebe nennen „zu viel zu tun" als Digitalisierungshemmnis (Bitkom Research 2025, n = 504). Ein Angebot über 2.198 bis 10.888 € wird selten am Eingangstag entschieden |
| **Es gibt kein Preisrisiko** | Kurze Fristen schützen vor schwankenden Materialpreisen. SARTU verkauft zum Festpreis ohne Materialanteil |
| **Es passt zu den übrigen Fristen** | Zahlungsziel 10 Tage · Erstlaufzeit 12 Monate · IP-Löschung 30 Tage |

**Nicht gewählt: 14 Tage.** Im Handwerk üblich, weil dort Materialpreise die Kalkulation bewegen.
Hier entstünden dadurch nur mehr abgelaufene Angebote, die jemand von Hand neu ausstellt.

**Sonderprojekte folgen derselben Vorbelegung.** Wer eine andere Frist braucht, trägt sie ein.

### `invoices`
`project_id` · `number` (text, UNIQUE) · `milestone` (text: `anzahlung` \| `zwischenrate` \| `schlussrate` \| `betrieb`) · `status` (§5.3) · `net_cents` · `vat_cents` · `gross_cents` · **`paid_cents` (integer, NOT NULL, DEFAULT 0)** · `due_date` (date) · `mollie_payment_url` (text) · `paid_at` · `marked_paid_by_user_id` · `note` (text) · **`reminder_sent_at` (datetime)** · **`reminder2_sent_at` (datetime)**

> **`paid_cents` schließt eine Lücke aus dem Audit.** Bisher kannte `status` nur „bezahlt" oder
> „nicht bezahlt". Zahlt ein Kunde 600 € auf eine Rechnung über 745 €, musste der Admin zwischen
> zwei falschen Angaben wählen. Bei Beträgen dieser Größe sind Teilzahlungen üblich.
>
> **Regeln:**
> - `paid_cents = 0` → Status bleibt `gesendet` oder `ueberfaellig`
> - `0 < paid_cents < gross_cents` → Status **`teilweise_bezahlt`**
> - `paid_cents >= gross_cents` → Status `bezahlt`, `paid_at` wird gesetzt
> - Jede Änderung an `paid_cents` verlangt den Grundlagentext aus §12 und erzeugt ein Audit-Ereignis
> - **Überzahlung wird nicht abgewiesen**, sondern gespeichert und im Adminbereich angezeigt
>
> `reminder_sent_at` und `reminder2_sent_at` verhindern, dass die Zahlungserinnerungen täglich
> erneut verschickt werden. **Zwei Erinnerungen brauchen zwei Felder** — mit nur einem hätte die
> zweite Mail ab Tag 7 jeden Tag ausgelöst, weil ihre Bedingung dauerhaft wahr bleibt. Gefunden
> bei der externen Prüfung am 01.08.2026; Testfall 78 hätte gegen die Spezifikation selbst
> geschlagen.

### `tasks`
`project_id` · `title` (text) · `description` (text) · `why_needed` (text, die Zeile „Warum wir das brauchen") · `kind` (text: `bestaetigung` \| `angabe` \| `upload` \| `freigabe`) · `status` (§5.4) · `sort_order` (integer) · `answer_text` (text) · `completed_at` · `completed_by_user_id` · `required` (boolean, default true)

### `task_files`
`task_id` · `organization_id` (redundant, für die Mandantenprüfung) · `original_name` (varchar(255)) · `stored_name` (char(36)) · `mime_type` (varchar(127)) · `size_bytes` (bigint) · `rights_confirmed` (boolean) · `uploaded_by_user_id`

### `feedback_rounds`
Bildet die **enthaltenen Korrekturrunden** ab — der zentrale Scope-Schutz des Geschäftsmodells.

| Feld | Typ | Hinweis |
|---|---|---|
| `project_id` | char(36), NOT NULL | |
| `number` | integer, NOT NULL | 1, 2, … — eindeutig je Projekt |
| `status` | text, NOT NULL | `offen` \| `eingereicht` \| `bearbeitet` |
| `opened_at` | datetime | |
| `submitted_at` | datetime | Kunde hat gebündelt eingereicht |
| `completed_at` | datetime | SARTU hat eingearbeitet |
| `included` | boolean, NOT NULL, default true | `false` = zusätzliche, kostenpflichtige Runde |

### `feedback_items`
`project_id` · `feedback_round_id` (char(36), NOT NULL) · `body` (text, NOT NULL) · `page_hint` (text) · `status` (§5.5) · `created_by_user_id` · `answered_at` · `answer_text` (text)

### `approvals`
Protokolliert **ausschließlich Erklärungen des Kunden**, die später beweisbar sein müssen.
Interne SARTU-Schritte gehören **nicht** hierher, sondern ins Audit-Log.

`project_id` · `kind` (text: `inhalte` \| `abnahme`) · `granted_at` · `granted_by_user_id` · `granted_ip` (varchar(45)) · `granted_name` (text) · `note` (text)

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

### `operator_settings`

Die Betreiberdaten aus §1.4a. **Genau eine Zeile.**

`id` (char(36)) · `singleton` (tinyint(1), NOT NULL DEFAULT 1) · `firmenname` (varchar(200), NOT NULL) ·
`rechtsform` (varchar(80)) · `strasse` (varchar(200), NOT NULL) · `plz` (varchar(10), NOT NULL) ·
`ort` (varchar(120), NOT NULL) · `land` (varchar(2), NOT NULL) · `telefon` (varchar(40)) ·
`email` (varchar(255), NOT NULL) · `ust_id` (varchar(20)) · `steuernummer` (varchar(30)) ·
`registergericht` (varchar(120)) · `registernummer` (varchar(40)) ·
`inhaltlich_verantwortlich` (varchar(200), NOT NULL) · `bank_iban` (varchar(34)) ·
`bank_bic` (varchar(11)) · `bank_institut` (varchar(120)) ·
`kleinunternehmer` (tinyint(1), NOT NULL DEFAULT 0) · **`setup_completed_at` (datetime, NULL bis zum Abschluss der Ersteinrichtung — §1.5 Schritt 8)**

**Wie „genau eine Zeile" erzwungen wird** — nicht als Absicht, sondern im Schema:

```sql
singleton TINYINT(1) NOT NULL DEFAULT 1,
UNIQUE KEY uq_operator_settings_singleton (singleton),
CONSTRAINT chk_operator_settings_singleton CHECK (singleton = 1)
```

Der eindeutige Schlüssel verhindert eine zweite Zeile, die Prüfbedingung verhindert das Umgehen
über einen anderen Wert. Die **erste** Zeile legt die Ersteinrichtung an (§1.5, Schritt 6). Der
Adminbereich kennt für diese Tabelle nur `UPDATE` — **kein** `INSERT`, **kein** `DELETE`.

**Prüfbedingung Steuerangabe** — und warum die naheliegende Fassung nicht reicht:

```sql
CONSTRAINT chk_operator_settings_steuer CHECK (
  (ust_id      IS NOT NULL AND ust_id      <> '') OR
  (steuernummer IS NOT NULL AND steuernummer <> '')
)
```

`CHECK (ust_id IS NOT NULL OR steuernummer IS NOT NULL)` wäre wirkungslos: Ein leerer Text ist
nicht `NULL`. Ein Formular, das ein unausgefülltes Feld als `''` speichert, hätte die Bedingung
erfüllt und trotzdem keine Steuerangabe — und die Startsperre aus §1.4a hätte durchgelassen, was
sie verhindern soll.

**Dieselbe Falle bei allen `NOT NULL`-Feldern.** `NOT NULL` erlaubt `''`. Für jedes Pflichtfeld
dieser Tabelle gilt daher zusätzlich serverseitig: nach `trim()` mindestens ein Zeichen. Die
Startsperre (§1.4a) prüft **nach derselben Regel** — leer heißt leer, nicht `NULL`.

| Feld | Zusätzliche Prüfung beim Speichern |
|---|---|
| `plz` | 5 Ziffern bei `land = 'DE'` |
| `land` | zwei Großbuchstaben (ISO 3166-1 alpha-2) |
| `email` | formal gültig, wird für Impressum und Rechnungen verwendet |
| `bank_iban` | wenn gesetzt: Prüfziffer nach ISO 7064 rechnen, nicht nur die Länge zählen |
| `ust_id` | wenn gesetzt: Landespräfix + Ziffernfolge. **Keine** Abfrage beim Bundeszentralamt — das ist kein Einrichtungsschritt |

### `legal_texts`

Impressum, Datenschutz, AGB, **Auftragsverarbeitungsvertrag** und die zugehörigen **technischen und
organisatorischen Maßnahmen** als Inhalte mit Freigabezustand (§1.4a).

`id` (char(36)) · `slug` (varchar(40), NOT NULL, UNIQUE — `impressum` \| `datenschutz` \| `agb` \|
**`avv`** \| **`tom`**) · `body` (mediumtext, NOT NULL) · `status` (varchar(20), NOT NULL —
`entwurf` \| `in_pruefung` \| `freigegeben`) · `released_at` (datetime) ·
`released_by` (varchar(200)) · `version` (int, NOT NULL) · `audience` (varchar(20), NOT NULL —
`oeffentlich` \| `kunde`)

**`audience` steuert die Auslieferung:**

| Wert | Wo sichtbar |
|---|---|
| `oeffentlich` | frei erreichbar unter `/impressum`, `/datenschutz`, `/agb` |
| `kunde` | **nur angemeldet** im Kundenbereich unter `/vertrag` — gilt für `avv` und `tom` |

> **Warum AVV und TOM überhaupt hier stehen** (ergänzt 31.07.2026 nach dem Audit):
> SARTU betreibt die Website des Kunden und verarbeitet die Anfragen, die dort eingehen. Damit ist
> SARTU **Auftragsverarbeiter für den Kunden** nach Art. 28 DSGVO. Ein Vertrag darüber ist Pflicht,
> und zu jedem AVV gehören die technischen und organisatorischen Maßnahmen als Anlage.
>
> Beide Texte fehlten in beiden Lastenheften vollständig. `legal_texts` kannte nur drei Slugs, und
> die Startsperre konnte deshalb nicht auf sie prüfen.
>
> **Der AVV wird nicht von einem Werkzeug formuliert.** Er durchläuft dieselbe Strecke wie alle
> Rechtstexte: `entwurf` → `in_pruefung` → `freigegeben`, freigegeben nur durch einen Menschen mit
> Datum und Namen der prüfenden Stelle (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §2).

**Nur `freigegeben` wird öffentlich ausgeliefert.** Den Zustand setzt ausschließlich ein Mensch,
mit Datum und Namen der prüfenden Stelle — kein automatischer Übergang, keine Voreinstellung.

### `audit_events`
`actor_user_id` (nullable) · `organization_id` (nullable) · `action` (varchar(64)) · `entity_type` (varchar(64)) · `entity_id` (char(36)) · `old_value` (text) · `new_value` (text) · `reason` (text) · `detail` (json) · `ip` (varchar(45))

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
| **Zeitzone** | **Europe/Berlin** für jede Anzeige. Speicherung immer in UTC (`DATETIME`, Verbindung auf `+00:00`, §4.0) |
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

## 4b. Anfrageeingang vom Bedarfsscheck

> **Ohne diesen Abschnitt bricht der Gesamtprozess.** Der Bedarfsscheck erzeugt Anfragen, sie müssen
> ankommen und geprüft werden können.

### 4b.1 Wo die Anfrage entsteht — und warum es kein Geheimnis braucht

Bedarfsscheck und Anfrageliste liegen im **selben** PHP-Projekt (§1.1). Der Ablauf ist deshalb:

```
Browser des Interessenten
        │  normales Formular-POST an die eigene Domain
        ▼
Formularannahme  →  AnfrageService::anlegen()  →  Tabelle `leads`
```

**Kein Netzaufruf, kein gemeinsames Geheimnis, keine Tokenprüfung.** Frühere Fassungen sahen einen
Header `X-Sartu-Token` mit `INTAKE_TOKEN` vor — das war richtig, solange Website und Kundenbereich
getrennte Anwendungen waren. In einem Projekt wäre es Zeremonie: ein Geheimnis, das nichts schützt,
aber verwaltet, übertragen und irgendwann versehentlich ausgeliefert werden kann.

**Alle inhaltlichen Schutzmaßnahmen bleiben unverändert** (4b.3) — sie schützen vor Spam und
Missbrauch, nicht vor einem fremden Aufrufer. Nur der Übertragungsweg entfällt.

**Die Fachlogik liegt trotzdem in einem eigenen Dienst** (`/app/services/AnfrageService.php`), nicht
im Formularcode. Grund: In Stufe 1 sollen auch **Kundenwebsites** Anfragen abliefern können. Dann
kommt ein dünner Endpunkt unter `/api/` davor — mit Token, Rate-Limit je Absender und
Herkunftsprüfung. Der Dienst selbst bleibt gleich. **In Stufe 0 gibt es diesen Endpunkt nicht**, auch
nicht vorbereitend (§0.3).

### 4b.2 Formularannahme `POST /briefing/absenden`

| Punkt | Festlegung |
|---|---|
| Methode und Pfad | `POST /briefing/absenden` — normales Formular, gleiche Domain |
| CSRF | Pflicht, wie bei jedem `POST` (§3 Regel 3) |
| Rate-Limit | **10 abgeschickte Bedarfsschecks je IP und Stunde**, zusätzlich **60 je Stunde gesamt** |
| Größe | maximal **64 KB** Formulardaten |
| Nach Erfolg | Weiterleitung (`303`) auf die Danke-Seite. **Nie** ein erneut absendbares Formular anzeigen |

**Felder — vollständige Liste.** Der Bedarfsscheck darf erweitert werden; unbekannte Felder landen
unverändert in `payload`, statt abgewiesen zu werden.

| Feld | Typ | Pflicht | Prüfung |
|---|---|---|---|
| `submission_id` | UUID | ja | entsteht beim **Start** des Bedarfsschecks, bleibt über alle Schritte gleich |
| `form_started_at` | Zeitstempel | ja | Zeitregel, s. 4b.3 |
| `first_name` | Text ≤ 100 | ja | nicht leer nach Trimmen |
| `last_name` | Text ≤ 100 | ja | nicht leer nach Trimmen |
| `company` | Text ≤ 200 | ja | nicht leer nach Trimmen |
| `email` | Text ≤ 254 | ja | Formatprüfung, kleingeschrieben gespeichert |
| `phone` | Text ≤ 50 | nein | wie eingegeben speichern |
| `preferred_contact` | `email` \| `portal` | ja | nur diese zwei Werte |
| `b2b_confirmed` | Wahrheitswert | ja | muss `true` sein |
| `privacy_confirmed` | Wahrheitswert | ja | muss `true` sein |
| `recommended_package` | `start` \| `wachstum` \| `platzhirsch` \| `sonderprojekt` \| `unklar` | nein | **serverseitig** berechnet, nicht aus dem Formular übernommen |
| `flag` | `standard` \| `gelb` \| `orange` \| `rot` | nein | ebenfalls serverseitig |
| `answers` | Feld-Wert-Paare | ja | unverändert nach `payload` |
| `hp_website` | Text | nein | **Honigtopf** — gefüllt ⇒ verwerfen |

> **Wichtig:** Empfehlung und Ampelkennzeichen werden **serverseitig** berechnet, nie aus dem
> abgeschickten Formular übernommen. Sonst könnte jemand die Empfehlung von außen setzen.

**Verhalten:**

| Lage | Reaktion |
|---|---|
| Angenommen | `lead` angelegt, E-Mail an SARTU, Weiterleitung auf die Danke-Seite |
| Bereits bekannte `submission_id` | **keine** Anlage, trotzdem Weiterleitung auf die Danke-Seite |
| Honigtopf gefüllt oder Zeitregel verletzt | **keine** Anlage, trotzdem Danke-Seite — der Absender merkt nichts |
| Pflichtfeld fehlt oder ungültig | Schritt erneut anzeigen, Meldung **am Feld**, Angaben bleiben erhalten |
| Rate-Limit erreicht | Hinweis mit Kontaktalternative, **keine** technischen Details |
| Serverfehler | Angaben bleiben erhalten, allgemeine Meldung, interne Kennung ins Protokoll |

Fehlermeldungen nennen **nie** Datenbankfehler, interne Kennungen oder ob eine E-Mail-Adresse bereits
bekannt ist.

### 4b.3 Spamabwehr und Doppeleinreichung

1. **Honigtopf** `hp_website` — für Menschen unsichtbar, aber **nicht** über `display:none` allein
   (Vorlesesoftware muss es überspringen: `aria-hidden="true"` **und** `tabindex="-1"`). Gefüllt ⇒
   stillschweigend verwerfen, Danke-Seite trotzdem zeigen
2. **Zeitregel** — liegt zwischen `form_started_at` und dem Absenden weniger als **3 Sekunden**,
   stillschweigend verwerfen. Menschen brauchen für den Bedarfsscheck Minuten
3. **Doppeleinreichung** — `submission_id` ist in `leads` **eindeutig**. Ein zweiter Versuch mit
   derselben Kennung ändert nichts. Das deckt Doppelklick, Neuladen und die Zurück-Taste ab
4. **Kein Rätselbild und kein Fremddienst in Stufe 0.** Turnstile, hCaptcha und Vergleichbares sind
   externe Verbindungen mit eigener Datenschutzfolge. Erst nachrüsten, wenn Spam **messbar** auftritt,
   und dann mit dokumentierter Rechtsgrundlage

### 4b.4 Datenschutz und Aufbewahrung

- **Datensparsamkeit:** gespeichert wird ausschließlich, was der Kunde eingegeben hat. **Keine**
  Anreicherung aus Fremdquellen, kein Standortnachschlagen, keine Bewertung
- **`source_ip`** wird gespeichert (Missbrauchsabwehr, Nachweis der Einwilligung) und **nach 30 Tagen
  geleert** — der übrige Datensatz bleibt. Umsetzung über die tägliche zeitgesteuerte Aufgabe (§1.4)
- **Protokolle:** Zeitpunkt, Ergebnis, gekürzte IP (letztes Oktett entfernt), `submission_id`.
  **Nie** Name, E-Mail, Telefonnummer oder Antworttexte
- **Löschfrist:** abgelehnte Anfragen werden **nach 6 Monaten** gelöscht, alle übrigen nicht
  umgewandelten **nach 12 Monaten** (§15.1 — die kürzere Frist gilt für den engeren Fall),
  umgewandelte bleiben als
  Teil der Kundenakte. Das Löschdatum ist im Adminbereich sichtbar
- **Betroffenenrechte:** je Datensatz `Datensatz exportieren` (alles, was gespeichert ist) und
  `Endgültig löschen` (echtes `DELETE`, **Ausnahme** von der Archivierungsregel in §3, Regel 13 —
  der Löschvorgang wird protokolliert, **ohne** die gelöschten Inhalte)
- Die Einwilligung erklärt der Interessent im Bedarfsscheck. Gespeichert wird, **dass** und **wann**

### 4b.7 Herkunft einer Anfrage — datensparsam und first-party

**Warum das nötig ist:** Nach einem SEA-Test muss beantwortbar sein, welcher Begriff eine
**Anfrage** gebracht hat — nicht nur einen Klick. Die Search Console zeigt Suchanfragen, aber nicht,
was daraus wurde. Ohne diese Felder ist der Test nur halb auswertbar.

**Wann erfasst wird — das ist die Stelle, an der es sonst schiefgeht:** Die Kennzeichen stehen in
der Adresse der **ersten** aufgerufenen Seite. Bis der Bedarfsscheck abgeschickt wird, sind sie
längst weg. Sie werden deshalb **beim ersten Seitenaufruf** in die serverseitige Sitzung geschrieben
und erst beim Anlegen des `lead` übernommen.

| Feld | Woher | Datensparsamkeit |
|---|---|---|
| `landing_page` | erste aufgerufene Seite | **nur der Pfad**, ohne Abfragezeichenfolge |
| `referrer_host` | `Referer`-Kopfzeile | **nur der Hostname**, nie die vollständige Adresse — die kann Suchbegriffe oder Kennungen enthalten |
| `utm_*` | Abfrageparameter | wie übergeben, auf je 100 Zeichen begrenzt |
| `click_id` | `gclid`, `gbraid` oder `wbraid` | Wert und Art speichern; nur setzen, wenn tatsächlich vorhanden |
| `self_reported_source` | Frage im Bedarfsscheck | freiwillig, Auswahl + Freitextfeld |

**Die Frage im Bedarfsscheck** (freiwillig, letzter Schritt, keine Pflichtangabe):
> Wie sind Sie auf uns aufmerksam geworden?

Auswahl: `Suchmaschine` · `Empfehlung` · `Direkt angesprochen worden` · `Anzeige` · `Sonstiges` +
optionales Freitextfeld. **Kein Pflichtfeld** — eine unbeantwortete Frage ist besser als eine
erzwungene Falschangabe.

**Warum beides und nicht nur eines:** Die technischen Kennzeichen sagen, **woher der Klick kam**.
Die Selbstauskunft sagt, **warum jemand kam** — und die weicht regelmäßig ab. Wer über eine
Empfehlung von SARTU hört und danach den Namen googelt, kommt technisch über die Suche.

**Datenschutz:**
- Alles wird **first-party** gespeichert. **Kein** Tracking über fremde Seiten, **keine** Cookies
  Dritter, **kein** Analysedienst. Damit ist auch kein Einwilligungsbanner nötig
- Die Daten dienen **ausschließlich** der Auswertung eigener Anfragen, nicht der Profilbildung
- Sie folgen der **Löschfrist des Leads** (§4b.4): abgelehnte Anfragen nach 6 Monaten weg
- Die Datenschutzerklärung muss diese Verarbeitung abdecken — das ist Teil des Auftrags an die
  Kanzlei (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §2), nicht selbst zu formulieren

**Auswertung im Adminbereich:** `/admin/anfragen` zeigt die Herkunft je Anfrage und erlaubt Filtern
danach. **Keine** Diagramme, keine Kennzahlenübersicht, kein Zeitverlauf — das ist Stufe 1. In
Stufe 0 genügt eine filterbare Liste, aus der sich die Frage „welche Kampagne brachte Aufträge?"
von Hand beantworten lässt.

### 4b.5 Adminbereich `/admin/anfragen`

**Das ist bewusst eine Liste, kein Vertriebssystem.** Zur Abgrenzung siehe §0.3a.

Liste: Eingangsdatum · Firma · Name · empfohlene Lösung · Ampelkennzeichen · Status · Löschdatum.
Filter nach Status, Sortierung nach Eingang, neueste zuerst.

Detailansicht: **alle** Antworten in Klartext als Frage → Antwort, nicht als Rohdaten.

Aktionen:
- `In Kunde und Projekt umwandeln` → legt `organizations`, `users` (Rolle `kunde`) und `projects` an,
  setzt `converted_organization_id` und `status = angebot_erstellt`, verschickt die Einladungs-E-Mail.
  **Bestätigungsdialog vorher**, weil dabei ein Zugang entsteht
- `Als abgelehnt markieren` mit Pflichtnotiz
- `Notiz speichern`
- `Datensatz exportieren` · `Endgültig löschen` (§4b.4)

**Regel:** Anfrage ≠ Kunde. Ein Zugang entsteht ausschließlich durch diesen bewussten Klick — nie
automatisch.

### 4b.6 Kontaktformular

Das allgemeine Kontaktformular ist **nicht** der Bedarfsscheck. Es versendet ausschließlich eine
E-Mail an SARTU und erzeugt **keinen** Datensatz. Honigtopf, Zeitregel und Rate-Limit gelten dort
gleichermaßen.

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

### Barrierefreiheit im Angebot — Pflichtzeile in `exclusions`

**Jedes Angebot muss die Frage beantworten, bevor sie gestellt wird.** Das
Barrierefreiheitsstärkungsgesetz gilt seit dem 28.06.2025. Ob es den Kunden betrifft, hängt an
seiner Größe und daran, ob er Verbrauchern etwas verkauft oder buchen lässt.

**Entschieden am 01.08.2026** (`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §6). Zwei Textbausteine, je nach Fall.

**Baustein 1 — steht in jedem Angebot unter „was enthalten ist":**

> `Technische Grundlagen der Bedienbarkeit sind enthalten: ausreichender Kontrast, vollständige
> Bedienung per Tastatur, sichtbare Fokusmarkierung, beschriftete Formularfelder und semantisches
> HTML. Ihre Website ist damit auch für Menschen mit Einschränkungen benutzbar.`

**Baustein 2 — steht in `exclusions`, solange die Seite keinen Vertrag schließt:**

> `Eine Prüfung und Bestätigung der Konformität nach dem Barrierefreiheitsstärkungsgesetz ist nicht
> Gegenstand dieses Angebots. Nach unserer Einschätzung ist Ihre Website davon nicht erfasst, weil
> Besucher darüber keinen Vertrag abschließen. Ändert sich das, sprechen Sie uns bitte an.`

**Beide Zeilen dürfen nicht fehlen und nicht umformuliert werden.** Die erste ist ein
Verkaufsargument, die zweite eine Grenze der Leistung. Wer die zweite weglässt, verkauft
stillschweigend etwas mit, das nicht geliefert wird.

#### Pflichtprüfung, sobald ein Buchungs-, Bestell- oder Kaufweg im Umfang steht

Der Adminbereich zeigt beim Anlegen des Angebots zwei Pflichtfragen, sobald `sitemap` oder
`inclusions` einen Buchungs-, Bestell- oder Abonnementweg nennen:

| Frage | Feld |
|---|---|
| `Schließen Besucher über die Seite einen Vertrag ab — Buchung, Bestellung oder Abonnement?` | `bfsg_vertragsabschluss` (ja/nein) |
| `Hat der Betrieb weniger als 10 Beschäftigte und höchstens 2 Mio. € Umsatz oder Bilanzsumme?` | `bfsg_kleinstunternehmen` (ja/nein/unbekannt) |

**Beide Antworten werden im Angebot mitgespeichert** und dort sichtbar wiedergegeben — es sind
Angaben des Kunden, keine Feststellung von SARTU.

| Antworten | Folge |
|---|---|
| Vertragsabschluss `nein` | Baustein 2 wie oben |
| Vertragsabschluss `ja`, Kleinstunternehmen `ja` | Baustein 2, ergänzt um: `nach Ihrer Angabe als Kleinstunternehmen ausgenommen` |
| Vertragsabschluss `ja`, Kleinstunternehmen `nein` oder `unbekannt` | **Angebot lässt sich nicht senden.** Hinweis: `Hier greift das Barrierefreiheitsstärkungsgesetz. Bitte als eigenen Festpreisposten anbieten oder das Vorhaben ablehnen.` |

> **Warum die Sperre und keine Warnung:** Es geht um ein Bußgeld bis 100.000 € und eine Zusage, die
> SARTU sonst stillschweigend mitverkauft. Eine Warnung wird weggeklickt.

> **Warum das im Datenmodell steht und nicht nur im Konzept:** Das BFSG kam im Masterkonzept vor,
> in **keinem** der beiden Lastenhefte. `exclusions` war Pflichtfeld ohne inhaltliche Vorgabe. Bei
> einem Platzhirsch-Projekt mit Buchungsfunktion für einen Betrieb oberhalb der
> Kleinstunternehmensgrenze ist das eine Haftungsfrage, keine Geschmacksfrage.

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

### 5.1a Zulässige Übergänge — wer löst was aus

Eine Liste von Zuständen ohne Übergangsregeln ist keine Statuslogik. Ohne diese Tabelle wird sie
beim Bauen erfunden — und zwar an der teuersten Stelle: Produktion startet vor Zahlungseingang,
oder der Lieferkorridor beginnt zu früh.

**`projects.status` — vollständige Übergangstabelle.** Was hier nicht steht, ist nicht erlaubt.

| Von | Nach | Auslösendes Ereignis | Wer löst aus | Was zwingend mitpassiert |
|---|---|---|---|---|
| *(Anlage)* | `angebot_offen` | Angebot gesendet | Admin | `offers.status = gesendet`, alle Pflichtfelder aus §4 gefüllt |
| `angebot_offen` | `angebot_angenommen` | Angebot angenommen | **Kunde** | Ankreuzen + selbst getippter Name; `offers.status = angenommen` mit Zeitpunkt; Audit-Ereignis |
| `angebot_angenommen` | `zahlung_offen` | Anzahlungsrechnung gesendet | Admin | `invoices.status = gesendet`, `due_date` = +10 Tage (§4a) |
| `zahlung_offen` | `briefing` | **Zahlungseingang bestätigt** | **Admin, von Hand** | `invoices.status = bezahlt` mit Datum. **Nie** aus der Rückkehr des Browsers abgeleitet (§12). Audit mit `reason` als Pflichtfeld |
| `briefing` | `produktion` | **Faktenfreigabe erteilt** | **Kunde** | Alle Aufgaben der Art `pflicht` erledigt; `approvals` mit `kind = inhalte`; **ab hier läuft der Lieferkorridor** (§4c) |
| `produktion` | `vorschau` | Vorschau veröffentlicht | Admin | `preview_url` und `preview_published_at` gesetzt; Feedbackrunde eröffnet |
| `vorschau` | `korrektur` | Rückmeldungen abgeschickt | **Kunde** | Runde wird geschlossen und **gegen `included_feedback_rounds` gezählt** (§5.6a) |
| `korrektur` | `vorschau` | überarbeitete Vorschau bereit | Admin | neue `preview_published_at`; nächste Runde nur, wenn Kontingent reicht |
| `vorschau` | `abnahme` | keine weiteren Änderungen | Admin | Kunde wird benachrichtigt, dass jetzt die Abnahme fehlt |
| `abnahme` | `launch_vorbereitung` | **Abnahme erklärt** | **Kunde** | `approvals` mit `kind = abnahme`; Schlussrechnung wird fällig |
| `abnahme` | `korrektur` | Rücksprung | Admin | `reason` Pflichtfeld; verbrauchte Runden bleiben verbraucht |
| `launch_vorbereitung` | `live` | Onlinegang | Admin | `launched_at`, `live_url`, `protection_started_on`, `protection_min_term_until` (§5.7); Audit |
| *(jeder außer `live`)* | `pausiert` | Projekt angehalten | Admin | `reason` Pflichtfeld — **wird dem Kunden angezeigt**; Herkunftsstatus wird gespeichert |
| `pausiert` | *(Herkunftsstatus)* | Fortsetzung | Admin | zurück auf `paused_from_status`, nicht auf einen frei gewählten Wert |

**Was ausdrücklich verboten ist:**

| Verboten | Grund |
|---|---|
| `zahlung_offen` überspringen | Produktion beginnt nicht auf Zusage. Der einzige Weg nach `briefing` führt über den bestätigten Eingang |
| `briefing → produktion` ohne `approvals`-Eintrag | Ohne protokollierte Freigabe fehlt später der Nachweis, worauf gebaut wurde — und der Lieferkorridor hätte keinen Startpunkt |
| `abnahme → live` direkt | Der Onlinegang ist ein eigener Arbeitsschritt, keine Folge der Abnahme |
| `live → korrektur` oder zurück in die Produktionskette | Ein laufender Betrieb wird nicht in den Bauzustand zurückgesetzt. Änderungen an einer Live-Seite laufen über einen neuen Vorgang |
| Zielstatus aus der Anfrage übernehmen | Der Server prüft jeden Wechsel gegen **diese** Tabelle. Ein nicht aufgeführtes Paar wird abgewiesen, nicht ausgeführt |

**Für jeden Wechsel gilt ohne Ausnahme:** Prüfung serverseitig gegen die Tabelle · Audit-Ereignis
mit `old_value`, `new_value` und handelndem Benutzer · bei Wechseln, die Geld oder Fristen
betreffen, zusätzlich `reason` (§4 `audit_events`, §12).

**Kundenausgelöste Wechsel** sind genau drei: Angebotsannahme, Faktenfreigabe, Abnahme. Alle drei
sind Erklärungen mit Namen und Zeitpunkt. Alles andere setzt der Admin. Die frühere Formulierung
„zulässige Übergänge setzt nur der Admin" war falsch und widersprach §8.4 und §9.3.

### 5.2 `offers.status`
`entwurf` (unsichtbar für Kunden) → `gesendet` → `angenommen` \| `abgelaufen` \| `zurueckgezogen`
Kundentexte: **Angebot liegt vor** · **Angenommen am {Datum}** · **Abgelaufen** · **Zurückgezogen**

### 5.3 `invoices.status`
`entwurf` (unsichtbar) → `gesendet` → `teilweise_bezahlt` → `bezahlt` \| `ueberfaellig` \| `storniert`
Kundentexte: **Offen — zahlbar bis {Datum}** · **Teilweise bezahlt — offen: {Restbetrag}** · **Bezahlt am {Datum}** · **Überfällig seit {Datum}** · **Storniert**
`ueberfaellig` wird täglich automatisch gesetzt, wenn `due_date < heute` und `paid_cents < gross_cents`.

**`teilweise_bezahlt` und `ueberfaellig` schließen sich nicht aus.** Eine angezahlte Rechnung nach
Fälligkeit ist **beides**. Angezeigt wird dann `Überfällig seit {Datum} — offen: {Restbetrag}`.
Maßgeblich für die Erinnerung ist der Restbetrag, nicht der Status.

### 5.3a Zahlungserinnerung — der tägliche Lauf

| Wann | Was |
|---|---|
| `due_date` überschritten, Restbetrag > 0, `reminder_sent_at` leer | **eine** Mail an den Kunden, `reminder_sent_at` setzen |
| 7 Tage nach `reminder_sent_at`, Restbetrag weiterhin > 0, **`reminder2_sent_at` leer** | **zweite** Mail, zusätzlich Hinweis an den Admin, `reminder2_sent_at` setzen |
| danach | **keine weitere automatische Mail.** Ab hier entscheidet ein Mensch |

> **Warum das nicht fehlen darf:** §5.3 setzt `ueberfaellig` automatisch, aber bisher erfuhr das
> niemand. Der Kunde meldet sich nur per Anmeldelink an — er sieht das Portal nur, wenn eine Mail
> ihn hinschickt. Ohne Erinnerung lief die einzige Geldeintreibung über den Zufall.
>
> **Kein Mahnwesen.** Zwei Erinnerungen, dann übernimmt der Mensch. Mahnstufen, Gebühren und
> Zinsen bleiben Stufe C.

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
- **Notweg, immer sichtbar** (Werte aus `operator_settings`, §1.4a):
  > `Kommt der Link auch nach ein paar Minuten nicht an, rufen Sie uns an: {telefon}. Oder schreiben Sie an {email}. Wir richten Ihnen den Zugang von Hand ein.`

> ### 6.3 Warum der Notweg Pflicht ist
>
> **Der Anmeldelink ist der einzige Weg ins Portal.** Kommt die Mail nicht an — Spamfilter,
> abgelehnt vom Mailserver des Kunden, Tippfehler in der Adresse —, ist der Kunde **ausgesperrt**.
> Und er kann es niemandem melden, weil der Meldeweg selbst im Portal liegt.
>
> Postfächer kleiner Betriebe liegen häufig bei Providern mit harten Filtern. Das ist kein
> Randfall.
>
> **Die Telefonnummer steht auf `/login`, auf der Bestätigungsseite und in jeder Anmeldemail.**
> Sie kommt aus den Betreiberdaten, nie aus dem Quelltext.
>
> **Kein zusätzliches Passwort.** Das würde die Entscheidung gegen Passwörter umkehren und eine
> zweite Angriffsfläche schaffen. Der Notweg ist ein Mensch, kein zweites Verfahren.
>
> **Der Admin sieht je Kunde:** wann zuletzt ein Link gesendet wurde und ob er verwendet wurde.
> Ein gesendeter, nie verwendeter Link ist das Warnzeichen für ein Zustellproblem.

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

**Navigation (feste Reihenfolge):** Übersicht · Angebot · Aufgaben · Vorschau · Rechnungen · Domain · Inhalte · **Vertrag** · Hilfe

**`/vertrag`** zeigt die Rechtstexte mit `audience = kunde` aus `legal_texts` — den
Auftragsverarbeitungsvertrag und die technischen und organisatorischen Maßnahmen (§15.2).
Zusätzlich eine Schaltfläche `Zur Kenntnis genommen`, die Zeitpunkt, Name und IP speichert und ein
Audit-Ereignis erzeugt. **Keine Zustimmung, keine Sperre** — der Vertrag gilt durch den Hauptvertrag,
die Bestätigung ist ein Nachweis der Bereitstellung.
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

**Welcher Status auf welcher Station steht — verbindlich:**

| Status | Station |
|---|---|
| `angebot_offen`, `angebot_angenommen` | **Angebot** |
| `zahlung_offen` | **Zahlung** |
| `briefing` | **Angaben** |
| `produktion`, `korrektur` | **Produktion** |
| `vorschau` | **Vorschau** |
| `abnahme`, `launch_vorbereitung` | **Abnahme** |
| `live` | **Online** |
| `pausiert` | **keine Station wird markiert.** Stattdessen erscheint über der Anzeige: `Pausiert — {pause_reason}` |

> **Ergänzt nach dem Audit.** Sieben Stationen für elf Status: Für `angebot_angenommen`,
> `korrektur`, `launch_vorbereitung` und `pausiert` war die Zuordnung nicht bestimmt. Ohne
> Festlegung hätte der Bau geraten — und die Anzeige hätte in vier Fällen etwas anderes gesagt
> als der Text darunter.
>
> **`korrektur` gehört zu Produktion, nicht zu Vorschau.** Aus Sicht des Kunden wird gearbeitet,
> nicht angesehen. **`launch_vorbereitung` gehört zu Abnahme, nicht zu Online** — online ist die
> Seite erst, wenn sie erreichbar ist.

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
| **Zahlungserinnerung** (§5.3a, erste) | `Erinnerung: Rechnung {Nummer} ist fällig` | `Die Rechnung {Nummer} über {Restbetrag} war am {Datum} fällig. Sie können direkt im Portal bezahlen. Haben Sie bereits überwiesen, ist diese Nachricht gegenstandslos.` |
| **Zahlungserinnerung** (zweite, nach 7 Tagen) | `Zweite Erinnerung: Rechnung {Nummer}` | derselbe Aufbau, zusätzlich: `Bitte melden Sie sich bei uns, wenn etwas unklar ist.` — parallel Hinweis an den Admin |
| **Teilzahlung verbucht** | `Teilzahlung erhalten` | `Wir haben {Betrag} erhalten. Offen sind noch {Restbetrag}.` |
| **Zahlungsstatus zurückgenommen** | `Korrektur zu Rechnung {Nummer}` | `Wir haben den Zahlungsstatus der Rechnung {Nummer} korrigiert. Grund: {Grundlagentext}. Bitte prüfen Sie den Stand im Portal.` |
| **Angebot läuft in 3 Tagen ab** | `Ihr Angebot gilt noch bis {Datum}` | `Ihr Angebot läuft am {Datum} ab. Danach stellen wir es Ihnen gern neu aus — melden Sie sich einfach.` |
| **Projekt pausiert** | `Ihr Projekt pausiert` | `Wir haben Ihr Projekt vorübergehend angehalten. Grund: {pause_reason}. Sobald es weitergeht, melden wir uns.` |
| **Projekt wird fortgesetzt** | `Es geht weiter` | `Ihr Projekt läuft wieder. Ihren nächsten Schritt finden Sie im Portal.` |

**Keine** Werbemails, keine Newsletter, keine Massenversendung.

> **Sieben Zeilen ergänzt am 31.07.2026 nach dem Audit.** Vier Zustände traten ein, ohne dass
> jemand davon erfuhr:
>
> | Zustand | Vorher |
> |---|---|
> | Rechnung wird überfällig | Status sprang automatisch um, **keine Mail** |
> | Zahlungsstatus zurückgenommen | §12 versprach eine Benachrichtigung, §10 kannte sie nicht |
> | Angebot läuft ab | Sackgasse, aus der nur Handarbeit herausführte |
> | Projekt pausiert | `pause_reason` war Pflichtfeld und wurde im Portal angezeigt — gesehen hat es niemand |
>
> Der Kunde meldet sich ausschließlich per Anmeldelink an. **Was ihm keine Mail mitteilt, erfährt
> er nicht.**

---

## 11. Uploads

- Erlaubt: `jpg`, `jpeg`, `png`, `webp`, `svg`, `pdf`, `docx`, `zip`
- Höchstens **20 MB** je Datei, **10** Dateien je Aufgabe
- **Höchstens 500 MB je Organisation insgesamt.** Bei Überschreitung wird abgelehnt: `Ihr Speicher ist voll (500 MB). Bitte schreiben Sie uns — wir schaffen Platz.` Der Admin sieht den Verbrauch je Kunde und kann die Grenze einzeln anheben
- Vor jedem Upload wird der **freie Platz auf dem Server** geprüft. Unter 1 GB: Ablehnung mit Klartextmeldung und Hinweis an den Admin, statt eines abgebrochenen Schreibvorgangs
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
- Auskunft und Löschung: Adminfunktion `Daten exportieren` (JSON je Organisation) und `Organisation archivieren`. Vollständige Löschung nur manuell nach Prüfung
- Rechtstexte des Portals werden **verlinkt**, nicht selbst formuliert

### 15.1 Aufbewahrungs- und Löschfristen — als Zahl, sonst nicht ausführbar

| Datensatz | Frist | Was danach passiert |
|---|---|---|
| `audit_events` | **3 Jahre** | Löschung, ohne Ersatzeintrag |
| `login_tokens` | nach Ablauf | sofortige Löschung durch den täglichen Lauf |
| `sessions` | nach Verfall | sofortige Löschung |
| `leads.source_ip` | **30 Tage** | Feld wird geleert, der übrige Datensatz bleibt (§4b.4) |
| **`leads`, Zustand `abgelehnt`** | **6 Monate** | **vollständige Löschung** mit Audit-Ereignis **ohne** die gelöschten Inhalte |
| **`leads`, sonstige nicht umgewandelte** | **12 Monate** | dasselbe |
| `invoices` und zugehörige Belege | **8 Jahre** ab Ende des Kalenderjahres | keine automatische Löschung. Vorrang vor jedem Löschverlangen |
| Uploads einer archivierten Organisation | **8 Jahre**, wenn sie einer Rechnung zugeordnet sind, sonst **12 Monate** | Löschung durch den täglichen Lauf |

> **Zwei Lücken aus dem Audit geschlossen:**
>
> 1. **Nicht umgewandelte Anfragen wurden nie gelöscht.** Geleert wurde nur die IP. Name, Firma,
>    E-Mail, Telefonnummer und Freitext blieben unbegrenzt liegen. Die DSGVO verlangt eine
>    Speicherbegrenzung — jetzt zwölf Monate
> 2. **„Gesetzliche Aufbewahrungspflichten gehen vor" stand ohne Zahl da.** Eine Regel ohne Zahl
>    kann eine Löschfunktion nicht ausführen. Für Buchungsbelege sind es acht Jahre

### 15.2 Auftragsverarbeitung — zwei Richtungen, beide nötig

| Richtung | Wer ist was | Wer sorgt dafür |
|---|---|---|
| **SARTU → Kunde** | Der Kunde ist Verantwortlicher, **SARTU ist Auftragsverarbeiter** — SARTU betreibt seine Website und verarbeitet die dort eingehenden Anfragen | **Vertrag im Kundenbereich** unter `/vertrag`, aus `legal_texts` mit `slug = avv`. Anlage: `tom` |
| **SARTU → Dienstleister** | SARTU ist Verantwortlicher, Hoster und Mailversand sind Auftragsverarbeiter | Sache des Betreibers, außerhalb des Codes |

**Was das Programm leistet:** Es zeigt den Vertrag an, protokolliert, wann der Kunde ihn zur
Kenntnis genommen hat, und blockiert die Veröffentlichung, solange er im Zustand `entwurf` oder
`in_pruefung` steht. **Es formuliert ihn nicht.**

**Was der Betreiber leisten muss und kein Programm abnimmt:** das Verzeichnis von
Verarbeitungstätigkeiten nach Art. 30 DSGVO. Es gehört in die Betriebsunterlagen, nicht in die
Anwendung.

---

## 16. Testfälle (Pflicht, müssen automatisiert laufen)

**Mandantentrennung — `tests/TenantIsolationTest.php` (unantastbar):**
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
29. Abgeschickter Bedarfsscheck erzeugt **nur** einen `lead` — keine `organizations`, `users` oder `projects`
30. `POST /briefing/absenden` ohne CSRF-Feld wird abgelehnt
31. Rate-Limit greift ab dem 11. abgeschickten Bedarfsscheck je IP und Stunde
32. Ausgefülltes Honigtopffeld führt zur Danke-Seite und erzeugt **keinen** Datensatz
33. Absenden unter 3 Sekunden nach `form_started_at` führt zur Danke-Seite und erzeugt **keinen** Datensatz
34. **Dieselbe `submission_id` zweimal** → weiterhin genau **ein** Datensatz, trotzdem Danke-Seite
35. `b2b_confirmed = false` oder `privacy_confirmed = false` → Schritt erneut anzeigen, kein Datensatz
36. Formulardaten über 64 KB werden abgewiesen
37. Keine Fehlermeldung nennt Feldwerte, interne Kennungen oder Datenbankmeldungen
38. Unbekanntes Zusatzfeld wird in `payload` gespeichert und **nicht** abgewiesen
39. **Empfehlung und Ampelkennzeichen werden serverseitig gesetzt** — ein manipuliertes Formularfeld ändert sie nicht
40. `source_ip` ist nach 30 Tagen geleert, der übrige Datensatz unverändert; `Endgültig löschen` entfernt den Datensatz und hinterlässt ein Audit-Ereignis **ohne** die gelöschten Inhalte
40a. Herkunftsfelder werden beim **ersten** Seitenaufruf in die Sitzung geschrieben und landen auch dann im `lead`, wenn der Bedarfsscheck erst Schritte später abgeschickt wird (§4b.7)
40b. `referrer_host` enthält **nur** den Hostnamen, `landing_page` **nur** den Pfad — keine vollständigen Adressen mit Abfragezeichenfolge

**Sicherheit:**
41. `POST` ohne CSRF-Token wird abgelehnt
42. Kunde erreicht **keine** `/admin`-Route — geprüft über die vollständige Adminroutenliste (§3 Regel 2a)
43. Abgemeldeter Benutzer erreicht keine `/admin`-Route
44. Admin ohne bestätigtes TOTP erreicht keine Adminroute
45. Die Kundenauswahl im Adminbereich verändert die Session-Organisation **nicht**
46. Unerlaubter Dateityp wird abgelehnt
47. Sicherheitsheader sind in allen Antworten gesetzt
48. Datenbankbedingung greift: Kunde ohne `organization_id` und Admin **mit** `organization_id` lassen sich nicht anlegen
49. **Kein Verzeichnis außer `/public` ist über den Webserver erreichbar** — `/app`, `/storage`, `/migrations` und `.env` liefern 403 oder 404
50. Jede Datenbankabfrage nutzt **vorbereitete Anweisungen** — im Code nachgewiesen, keine zusammengesetzten SQL-Zeichenketten

**Protokollierung:**
51. Manuelles Setzen auf `bezahlt` ohne Grundlagentext scheitert
52. Das Audit-Ereignis dazu enthält Akteur, Zeitpunkt, alten Wert, neuen Wert, Grundlagentext und IP (§12)
53a. Änderung von `due_date` erzeugt ein Audit-Ereignis mit Grundlagentext
53b. Änderung von `protection_started_on` erzeugt ein Audit-Ereignis mit Grundlagentext
54. Rücknahme von `bezahlt` ist eine eigene protokollierte Aktion und benachrichtigt den Kunden
55. Ein Audit-Eintrag lässt sich weder ändern noch löschen

**Bedienung:**
56. Alle Kernabläufe funktionieren mit deaktiviertem JavaScript
57. Willkommensstrecke erscheint einmal und danach nicht mehr
58. Jede Seite hat genau eine `<h1>`
59. Kein Systemcode aus §5 erscheint in einer Kundenansicht — geprüft per Volltextsuche über die gerenderten Seiten

**Statusübergänge (§5.1a):**
60. Ein Paar, das **nicht** in der Übergangstabelle steht, wird abgewiesen — geprüft an `zahlung_offen → produktion`. Kein Statuswechsel, kein Teileffekt
61. `briefing → produktion` scheitert, solange kein `approvals`-Eintrag mit `kind = inhalte` existiert
62. Fortsetzen aus `pausiert` führt auf `paused_from_status` zurück — ein im Formular mitgesendeter Zielstatus wird ignoriert
63. `live → korrektur` wird abgewiesen

**Betreiberdaten (§1.4a, §4 `operator_settings`):**
64. Eine **zweite** Zeile in `operator_settings` lässt sich nicht anlegen — weder mit anderem `singleton`-Wert noch mit anderem Schlüssel
65. `ust_id = ''` **und** `steuernummer = ''` wird abgewiesen. Leer ist nicht gesetzt — die Prüfbedingung darf nicht nur auf `NULL` prüfen
66. Startsperre greift: Bei leerem Pflichtfeld oder Rechtstext im Zustand `entwurf` bricht die produktive Veröffentlichung ab (Website-Lastenheft §14a)

**Ersteinrichtung (§1.5):**
67. Einrichtung gegen eine **nicht leere** Datenbank bricht vor der ersten Migration ab
68. Eine nachträglich geänderte Migrationsdatei löst beim Start einen Prüfsummenabbruch aus, mit Nennung der Datei
69. Nach einem Abbruch mitten in Schritt 3 setzt der erneute Aufruf bei der **ersten nicht eingetragenen** Migration fort und wiederholt keine bereits eingetragene
70. `/admin/setup` über HTTP mit `APP_ENV=production` von `127.0.0.1` bricht ab
71. `/admin/setup` über HTTP mit `APP_ENV=local` von einer **nicht** loopback-Adresse bricht ab
72. `X-Forwarded-Proto: https` bei tatsächlichem HTTP wird ignoriert, solange keine vertrauenswürdige Zwischenstelle konfiguriert ist
73. Nach Abschluss liefert `/admin/setup` `404`, auch nach Löschen **einer** der beiden Sperren

**Nachträgliche Migration (§1.5a):**
74. `php bin/migrate.php status` auf einer **nicht leeren** Datenbank listet offene Migrationen und verändert nichts
75. `up` ohne angegebene Sicherungsdatei bricht ab — ebenso bei angegebener, aber fehlender oder leerer Datei
76. Während `up` liefern Kunden- und Adminbereich `503`; nach Erfolg ist der Wartungsmodus aufgehoben, nach Abbruch bleibt er bestehen

**Geld, Fristen und Zugang (ergänzt nach dem Audit vom 31.07.2026):**
77. `paid_cents` zwischen 0 und `gross_cents` ergibt Status `teilweise_bezahlt` — **nicht** `bezahlt`, auch nicht bei einem Cent Differenz
78. Eine überfällige Rechnung löst **genau eine** Erinnerung aus; ein zweiter Lauf am selben oder am Folgetag verschickt **keine weitere**
79. Ein Upload, der die Organisationsgrenze von 500 MB überschreitet, wird abgelehnt — auch wenn die Einzeldatei unter 20 MB liegt
80. Eine nicht umgewandelte Anfrage älter als 12 Monate wird vom täglichen Lauf gelöscht; das Audit-Ereignis enthält **keine** der gelöschten Inhalte
81. `legal_texts` mit `slug = avv` im Zustand `entwurf` blockiert die produktive Veröffentlichung (§14a)
82. `legal_texts` mit `audience = kunde` ist **öffentlich nicht abrufbar** und angemeldet sichtbar
83. `/login` zeigt die Telefonnummer aus `operator_settings`. Ist dort keine gesetzt, erscheint die E-Mail-Adresse — **nie** ein Wert aus dem Quelltext

> **Zur Anzahl:** Die Liste hat **83 durchnummerierte plus fünf mit Buchstabenzusatz** (5a, 5b, 40a,
> 40b, 53a/53b als Teilung von 53) — zusammen **88 Testfälle**. Frühere Fassungen sprachen von „59";
> das war schon damals um vier Fälle zu niedrig. Maßgeblich ist die Liste, nicht die Zahl.
>
> **Welcher Fall in welcher Etappe entsteht, steht in `REIHENFOLGE.md`** — eine Zeile je Fall,
> jeder genau einmal. Keine Sammelzuordnung, keine Mehrfachnennung.

---

## 17. Definition of Done

- [ ] Alle Screens aus §7, §8 und §9 vorhanden und bedienbar
- [ ] Alle Texte aus diesem Dokument **wörtlich** übernommen
- [ ] Alle Statuswerte zeigen dem Kunden Klartext, nirgends interne Codes
- [ ] Formate aus §4a eingehalten: deutsche Datums- und Geldformate, Europe/Berlin, 19 % USt., Beträge als Cent gespeichert, keine leeren Werte als `null` sichtbar
- [ ] **`php -l` läuft über jede PHP-Datei ohne Fehler** — billigste Prüfung überhaupt, fängt Syntaxfehler ab, bevor überhaupt ein Test startet. Gehört in den Testlauf, nicht in die Handarbeit
- [ ] Alle 88 Testfälle aus §16 laufen automatisiert und grün
- [ ] `tests/TenantIsolationTest.php` vorhanden, vollständig, nicht abgeschwächt
- [ ] Kunden- und Adminzugriff laufen über **getrennte** Datenzugriffsschichten (§3 Regel 2a); kein gemeinsamer Codepfad lässt den Organisationsfilter weg
- [ ] Rechenregeln greifen: Erstjahreswert, Zahlungsplan `custom`, Ratensumme (§4)
- [ ] Korrekturrunden werden gezählt und angezeigt; nichts wird automatisch gesperrt oder berechnet (§5.6a)
- [ ] Faktenfreigabe und Abnahme erzeugen je einen `approvals`-Eintrag mit Name, Zeitpunkt, IP und Audit-Ereignis — nachträglich nicht änderbar
- [ ] Bedarfsscheck-Annahme funktioniert, ist mit CSRF, Rate-Limit, Honigtopf, Zeitregel und `submission_id` geschützt und legt **nur** einen `lead` an (§4b)
- [ ] Empfehlung und Ampelkennzeichen entstehen **serverseitig**, nicht aus dem Formular
- [ ] **Nur `/public` ist über den Webserver erreichbar**; `/app`, `/storage`, `/migrations`, `.env` sind es nicht — praktisch geprüft
- [ ] Datenbankzugriff ausschließlich über `/app/data` mit vorbereiteten Anweisungen; kein SQL in Ansichten oder Diensten
- [ ] Zeitgesteuerte Aufgabe läuft und erledigt IP-Löschung, Löschfristen und Überfälligkeitsprüfung (§1.4)
- [ ] Anfrageliste bleibt innerhalb der Grenze aus §0.3a — keine Bewertung, kein Nachfassen, keine Zuweisung
- [ ] Aufbewahrung und Betroffenenrechte umgesetzt: IP-Löschung nach 30 Tagen, Löschdatum sichtbar, Export und endgültige Löschung je Datensatz (§4b.4)
- [ ] Jede manuelle Änderung an Geld oder Fristen verlangt einen Grundlagentext und erzeugt ein vollständiges Audit-Ereignis (§12)
- [ ] Betriebsbeginn und Mindestlaufzeit werden beim Onlinegang gesetzt und dem Kunden angezeigt (§5.7)
- [ ] Ohne JavaScript vollständig bedienbar
- [ ] Kontrast, Fokus, Tastaturbedienung, Labels geprüft
- [ ] Keine Secrets im Repository; `.env.example` vollständig
- [ ] Migrationen laufen von leerer Datenbank fehlerfrei durch
- [ ] **Alle Spaltentypen entsprechen §4.0** — keine PostgreSQL-Typen (`timestamptz`, `citext`, `uuid`, `bytea`, `jsonb`, `inet`) im Migrationscode
- [ ] Die Datenbankverbindung setzt `SET time_zone = '+00:00'`; ein neu angelegter Datensatz trägt einen UTC-Zeitpunkt, keinen lokalen (§4.0)
- [ ] Seed erzeugt einen vollständigen Musterkunden über alle Projektstände — geeignet für die Website-Screenshots, ohne echte Namen oder realistische Rechnungsnummern
- [ ] Audit-Log erfasst alle in §3.9 genannten Ereignisse
- [ ] E-Mails werden versendet und sind in Klartext und HTML lesbar
- [ ] Sicherheitsheader gesetzt, HTTPS erzwungen
- [ ] `README.md` beschreibt Einrichtung, Migration, Seed, Start, Deployment und Backup

---

## 18. Was du ablieferst

1. Lauffähiges Portal im Repository
2. **`README.md`**: Voraussetzungen, Einrichtung, Umgebungsvariablen, Migration, Seed, Start, Deployment auf Hetzner, Backup-Hinweis (Datenbank **und** Upload-Verzeichnis)
3. **Testbericht**: alle 88 Fälle aus §16 mit Ergebnis
4. **Messwerte**: Antwortzeiten der Kernseiten, Seitengröße
5. **Offene-Punkte-Liste**: alles, was bewusst nicht gebaut wurde (§0.3), plus alles, was du melden musst
6. **Screenshot-Satz** aus der echten Oberfläche für die Website — mit Musterdaten, je einmal Desktop und Mobil.
   **Nach Stufe A verfügbar:** Cockpit · Bedarfsscheck-Antworten · Angebot · Aufgaben · Uploads · Vorschau mit Rundenanzeige · Rechnungen (Status manuell gesetzt).
   **Erst nach Stufe B:** ausschließlich die Öffnungszeiten.
   **Nachrichten an den Betreuer gehören seit dem Audit vom 31.07.2026 zu A2** und müssen vorhanden sein.
   **Zu Stufe A gehören und müssen vorhanden sein:** Bedarfsscheck und Anfrageliste (A1) ·
   Domainstatus (A3). Verschoben ist beim Domainstatus nur die Registrar-Anbindung, nicht die
   Ansicht. **„Anfragen von der Website" bedeutet hier ausschließlich die Bedarfsschecks der
   eigenen SARTU-Seite** — Anfragen aus Kundenwebsites sind Stufe 1 und ausdrücklich nicht zu bauen.
   Der Satz gilt als vollständig, wenn alle Ansichten der **jeweils gebauten Stufe** vorliegen (`REIHENFOLGE.md`)
7. **`IMPLEMENTATION_SUMMARY.md`**: gebaute Struktur, Abweichungen vom Plan mit Begründung, offene Punkte
8. **`MIGRATION_NOTES.md`**, falls aus einem Prototyp etwas übernommen wurde: was, warum, was verworfen

**Arbeite nicht ins Blaue:** Fehlt eine Information oder widerspricht sich etwas, melde es, statt zu raten. Baue **nichts** aus §0.3 „nicht in Stufe 0", auch nicht „schon mal vorbereitet".
