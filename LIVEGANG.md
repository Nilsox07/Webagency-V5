# Livegang

**Was vor dem ersten Besucher getan sein muss.** Von oben nach unten. Wer eine Zeile
überspringt, geht mit einem bekannten Fehler live.

**Stand der Anwendung:** Stufen A0 bis B gebaut, 264 Tests grün, Messungen in `MESSUNGEN.md`.

---

## 0. Zwei Dinge, die kein Code erledigt

Sie stehen ganz oben, weil alles andere daran hängt.

### 0.1 Die Rechtstexte freigeben

`legal_texts` enthält fünf Texte. Alle fünf stehen auf `entwurf`. Solange auch nur einer
nicht freigegeben ist, liefern `/impressum` und `/datenschutz` **404**, und die Startsperre
(§14a) hält die Veröffentlichung an.

Entwürfe liegen unter `rechtstexte-entwuerfe/`. Sie sind **Entwürfe** — geschrieben, damit
ein Mensch sie prüfen kann, nicht damit er sie durchwinkt.

> **Kein Rechtstext geht in den Zustand `freigegeben` — das macht ein Mensch.** Keine
> Migration, kein Skript, kein Adminknopf, der es „für Sie" erledigt. Freigegeben wird unter
> `/admin/rechtstexte/{slug}`, nach dem Lesen.

Die Datenschutzerklärung ist am 02.08.2026 an einer Stelle geändert worden: Das allgemeine
Kontaktformular **speichert nichts** (§4b.6). Wer eine ältere Fassung geprüft hat, prüft
Abschnitt 4 noch einmal.

### 0.2 Den Anbieter aussuchen und einrichten

`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4 steht auf `offen`. Ohne Anbieter gibt es keine Domain,
kein TLS, keinen Cron und keinen Mailversand — und damit nichts zu prüfen.

Die Anwendung braucht:

- PHP 8.3 oder neuer, mit `pdo_mysql`, `sodium`, `mbstring`, `intl`, `fileinfo`, `openssl`
- MySQL 8 oder MariaDB 10.6 oder neuer
- einen Cron-Eintrag
- SMTP mit eigener Absenderdomain
- einen `DocumentRoot`, den man auf ein Unterverzeichnis legen kann

**Ein geteiltes Hosting ohne freien `DocumentRoot` scheidet aus.** Liegt `/app` im Web,
nützt keine spätere Einstellung etwas.

---

## 1. Server einrichten

### 1.1 `APP_ENV=production` — in der **Serverumgebung**, nicht in der `.env`

Portal-Lastenheft §1.5 liest `APP_ENV` aus der Umgebung des Prozesses. Steht der Wert nur in
der `.env`, greift die Ausnahme für die Ersteinrichtung eventuell weiter, und
`Strict-Transport-Security` wird nicht gesetzt.

```apache
# Apache
SetEnv APP_ENV production
```

```nginx
# nginx mit php-fpm — in den fastcgi_params oder im Pool
fastcgi_param APP_ENV production;
```

```ini
; php-fpm-Pool, wenn beides nicht geht
env[APP_ENV] = production
```

**Fehlt `APP_ENV` ganz, gilt produktiv.** Das ist die sichere Richtung, aber kein Ersatz für
den gesetzten Wert: Die Prüfung unten will ihn sehen.

### 1.2 `DocumentRoot` auf `/public`

**Nur `/public` ist über den Webserver erreichbar** (§1.3). `/app`, `/storage`, `/migrations`,
`/tests`, `/bin` und die `.env` dürfen nie ausgeliefert werden.

```apache
DocumentRoot /pfad/zum/projekt/public

<Directory /pfad/zum/projekt>
    Require all denied
</Directory>

<Directory /pfad/zum/projekt/public>
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

**Gegenprobe nach dem Einrichten:** `https://ihre-domain/.env` und
`https://ihre-domain/app/bootstrap.php` müssen 403 oder 404 liefern. Ein 200 ist ein
Notfall, kein Schönheitsfehler.

### 1.3 `STORAGE_DIR` außerhalb des Webverzeichnisses

Hochgeladene Dateien liegen dort. Liegt das Verzeichnis unter `/public`, kann sie jeder
abrufen, der den Namen errät — und die Namen sind UUIDs, keine Berechtigung.

```
STORAGE_DIR=/pfad/zum/projekt/storage
```

Also **neben** `/public`, nicht darin. Schreibrechte für den Webserverbenutzer, sonst
scheitert der erste Upload.

### 1.4 PHP-Einstellungen

| Einstellung | Wert | Warum |
|---|---|---|
| `session.cookie_secure` | **1** | Lokal steht sie auf 0, weil es kein TLS gibt. Auf 0 geht das Sitzungscookie auch unverschlüsselt raus |
| `session.cookie_httponly` | 1 | kein Zugriff aus Skripten |
| `session.cookie_samesite` | Lax | §3 Regel 6 |
| `session.use_strict_mode` | 1 | keine fremd gesetzte Sitzungskennung |
| `expose_php` | **Off** | Die PHP-Fassung in jeder Antwort sagt einem Angreifer, welche Schwachstellenliste sich lohnt |
| `display_errors` | **Off** | Keine Stacktraces nach außen (§3 Regel 13) |
| `log_errors` | On | Sie sollen ins Protokoll, nicht ins Nichts |
| `date.timezone` | Europe/Berlin | Anzeigezeit. Gespeichert wird trotzdem UTC |
| `upload_max_filesize` | 20M | §11: 20 MB je Datei |
| `post_max_size` | 24M | größer als `upload_max_filesize`, sonst bricht der Upload vor der Prüfung ab |

`.docker/php/php.ini` ist die **lokale** Fassung. Sie taugt als Vorlage, nicht als Kopie:
`display_errors` steht dort auf `On` und `session.cookie_secure` auf `0`.

### 1.5 HTTPS erzwingen und HSTS

TLS-Zertifikat einrichten, dann jede HTTP-Anfrage auf HTTPS umleiten:

```apache
RewriteEngine On
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
```

`Strict-Transport-Security` setzt die Anwendung selbst — aber **nur** bei
`APP_ENV=production`. Ohne 1.1 fehlt der Kopf.

**Gegenprobe:** `curl -sI https://ihre-domain/ | grep -i strict-transport` muss eine Zeile
liefern.

---

## 2. Datenbank und Ersteinrichtung

### 2.1 Leere Datenbank anlegen

Die Ersteinrichtung bricht bei einer **nicht leeren** Datenbank ab (§1.5). Das ist Absicht.
Datenbank und Benutzer anlegen, Zugangsdaten in die `.env`.

### 2.2 `.env` aus der Vorlage

`cp .env.example .env`, dann füllen. **`SESSION_SECRET` und `ENC_KEY` bleiben leer** — die
entstehen in Setup-Schritt 3 und werden nie von Hand ausgedacht.

`ADMIN_NOTIFY_EMAIL` in der `.env` ist der alte Weg. Gepflegt wird die Adresse unter
`/admin/einstellungen/betrieb`; sie steht in `operator_settings.benachrichtigung_email`.

> **Ohne diese Adresse ist `/kontakt` stumm.** Seit §4b.6 erzeugt das Kontaktformular
> **keinen** Datensatz mehr — die Mail ist der einzige Träger. Fehlt der Empfänger, zeigt
> `/kontakt` den Ausweichweg statt des Formulars, und keine Rückfrage geht verloren. Aber es
> kommt eben auch keine an. **Diese Adresse gehört in die Prüfliste unten.**

### 2.3 Ersteinrichtung, acht Schritte, genau einmal

`https://ihre-domain/admin/setup` im Browser. Über HTTPS — die HTTP-Ausnahme gilt nur lokal.

Die acht Schritte:

1. Prüfung der Umgebung
2. Datenbankzugang
3. **Schlüssel erzeugen**
4. Migrationen einspielen
5. Testmail
6. Betreiberdaten
7. erstes Adminkonto mit TOTP
8. Abschluss

**Die Reihenfolge zählt.** Die Schlüssel entstehen in Schritt 3, weil Schritt 7 das
TOTP-Geheimnis damit verschlüsselt.

**In Schritt 7 den TOTP-Schlüssel in die Authenticator-App eintragen und den Code prüfen,
bevor Sie weiterklicken.** Danach gibt es keinen zweiten Versuch ohne Datenbankzugriff.

### 2.4 Danach: `/admin/setup` liefert für immer 404

Die Sperre liegt an **zwei** Orten, und **einer genügt** für 404:

| Ort | Was |
|---|---|
| `operator_settings.setup_completed_at` | ein Zeitstempel in der Datenbank |
| `/storage/installed.lock` | eine Datei |

Ein gelöschtes Lockfile hebt die Sperre also nicht auf. **Gegenprobe:** `/admin/setup`
aufrufen, 404 sehen.

**Ab hier gibt es keinen Weg über den Browser mehr in die Datenbank.** Spätere Migrationen
laufen ausschließlich über die Befehlszeile:

```
php bin/migrate.php status
php bin/migrate.php up --backup=/pfad/zur/sicherung.sql
php bin/migrate.php verify
```

Kein `down`. Vorwärts ist ein Befehl, rückwärts ist eine Sicherung.

---

## 3. Cron

Ein Eintrag, täglich. Er erledigt:

- abgelaufene Anmeldungen abräumen
- `leads.source_ip` nach 30 Tagen leeren
- fällige Anfragen löschen
- `ueberfaellig` setzen
- die zwei Zahlungserinnerungen verschicken
- abgelaufene Angebote auf `abgelaufen` setzen

**Den genauen Befehl zeigt Setup-Schritt 8 zum Kopieren.** Er hat diese Form:

```cron
0 3 * * * /usr/bin/php /pfad/zum/projekt/bin/cron.php
```

Pfad zu PHP und Pfad zum Projekt sind die des Servers — nicht die aus diesem Beispiel.

**Gegenprobe am Folgetag:** Der Lauf schreibt seine Zahlen nach `STDOUT`. Entweder in eine
Datei umleiten oder die Mail des Cron-Dienstes ansehen. Kommt nichts, läuft er nicht.

> **Läuft der Cron nicht, wird keine Rechnung überfällig und keine Erinnerung verschickt.**
> Das fällt erst auf, wenn jemand nicht bezahlt hat — also spät.

---

## 4. Mail

### 4.1 Absenderdomain: SPF, DKIM, DMARC

Ohne die drei landen die Mails im Spam-Ordner. Das ist bei einem Anmeldelink dasselbe wie
gar keine Mail: **Der Kunde meldet sich ausschließlich per Anmeldelink an.**

| Eintrag | Wo | Was |
|---|---|---|
| **SPF** | TXT auf der Absenderdomain | den sendenden Server aufnehmen, mit `-all` abschließen |
| **DKIM** | TXT auf `{selector}._domainkey` | Schlüsselpaar beim Anbieter erzeugen, öffentlichen Teil eintragen |
| **DMARC** | TXT auf `_dmarc` | mit `p=none` und einer Berichtsadresse anfangen, nach zwei Wochen Berichten auf `p=quarantine` |

`MAIL_FROM` muss auf dieser Domain liegen. Eine fremde Absenderadresse besteht kein DMARC.

### 4.2 Testmail an eine **fremde** Adresse

Setup-Schritt 5 verschickt eine Testmail. Lokal fängt Mailpit sie ab und sagt damit **nichts**
über Zustellbarkeit.

**Also:** Testmail an eine Adresse bei einem anderen Anbieter schicken — Gmail, Outlook, was
Sie nicht selbst betreiben. Im **Posteingang** nachsehen, nicht im Spam-Ordner. Landet sie
im Spam, stimmt 4.1 noch nicht.

### 4.3 Was gemessen ist und was nicht

Zwölf Mailwege sind am 02.08.2026 über echtes SMTP ausgelöst und im Posteingang gesehen
worden (`MESSUNGEN.md` §4). **Das war Mailpit, nicht das Internet.** Punkt 4.2 bleibt offen.

---

## 5. Sicherung

**Zwei Dinge, beide oder keins:**

### 5.1 Die Datenbank

```
mysqldump --single-transaction --routines --triggers -u BENUTZER -p DATENBANK > sicherung.sql
```

`--triggers` ist nicht optional: Auf `audit_events` liegt ein Trigger, der das Löschen
verbietet (§4). Eine Sicherung ohne ihn stellt eine Datenbank her, in der Protokolleinträge
löschbar sind.

Täglich, außerhalb der Arbeitszeit, mit Aufbewahrung über mehrere Tage. Eine einzige
überschriebene Sicherung ist keine.

### 5.2 Die `.env`

> **Wer `ENC_KEY` verliert, verliert jedes TOTP-Geheimnis.** Sie sind mit ihm verschlüsselt.
> Ohne den Schlüssel kommt kein Admin mehr an seinem zweiten Faktor vorbei, und die
> Anmeldung ist zu — auch für Sie.

Die `.env` gehört **nicht** ins Repository (sie steht in `.gitignore`, und dabei bleibt es).
Sie gehört in einen Passwortspeicher oder eine verschlüsselte Sicherung, getrennt von der
Datenbanksicherung.

### 5.3 Einmal zurückspielen, bevor es ernst wird

Eine Sicherung, die nie zurückgespielt wurde, ist eine Vermutung. Auf einem zweiten
Datenbankschema einspielen, `php bin/migrate.php status` laufen lassen, einmal anmelden.

---

## 6. Was **vor** dem ersten Kunden noch gebaut werden muss

Diese Punkte sind gemessen, nicht vermutet. Sie stehen hier, weil sie den Betrieb betreffen
und nicht die Einrichtung.

### 6.1 Sechs Mails aus §10 gibt es nicht

`MESSUNGEN.md` §4 hat sie beim Durchspielen gefunden. Der Wortlaut steht in §10 — es ist
nichts zu erfinden, nur zu bauen.

| §10 verlangt | Betreff | Folge, wenn sie fehlt |
|---|---|---|
| Angebot gesendet | `Ihr Angebot von SARTU liegt bereit` | **Das Angebot liegt im Kundenbereich, und niemand schickt den Kunden hin** |
| Neue Aufgaben | `Es liegen Aufgaben für Sie bereit` | Das Projekt wartet auf Angaben, die niemand erfragt |
| Faktenfreigabe erfolgt | `Freigabe bestätigt — wir starten` | Der Lieferkorridor beginnt unbemerkt |
| Antwort auf Nachricht | `Antwort auf Ihre Nachricht` | Die Antwort liegt im Kundenbereich und wird nicht gelesen |
| Angebot läuft in 3 Tagen ab | `Ihr Angebot gilt noch bis {Datum}` | Das Angebot verfällt ohne Vorwarnung |
| Angebot angenommen (an Admin) | `Angebot angenommen: {Organisation}` | SARTU erfährt die Beauftragung nur durch Nachsehen |

**Die erste Zeile ist die dringendste.** Ohne sie funktioniert der Weg vom Angebot zur
Annahme nur, wenn jemand von Hand eine Mail schreibt.

### 6.2 Block 4 des Cockpits fehlt

§8.1 verlangt „Letzte Aktivität" — die letzten fünf für den Kunden relevanten Ereignisse.
Sie stünden in `audit_events`, aber kein Dokument legt fest, welche Aktion kundenrelevant ist
und wie ihr Klartext lautet. **Gemeldet, nicht geraten** (`OFFENE_PRUEFUNGEN.md`).

---

## 7. Die Prüfliste

Abhaken, bevor die Adresse öffentlich wird. Die Reihenfolge ist die von oben.

**Der Befehl zuerst:**

```
php bin/startklar.php
```

Er gibt **1** zurück, solange ein Hindernis steht, und nennt jedes einzeln:

- leere Pflichtfelder der Betreiberdaten
- weder Steuernummer noch Umsatzsteuer-Identifikationsnummer
- nicht freigegebene Rechtstexte
- `APP_ENV` steht nicht auf `production`
- `ENC_KEY` ist leer
- `STORAGE_DIR` liegt im Webverzeichnis

Was er **nicht** prüft, steht darunter — es sind Einstellungen des Anbieters:

### Recht und Inhalt

- [ ] Alle fünf Rechtstexte gelesen und unter `/admin/rechtstexte` auf `freigegeben` gesetzt — **von einem Menschen**
- [ ] `/impressum` und `/datenschutz` liefern 200 und enthalten keinen Platzhalter
- [ ] Betreiberdaten vollständig unter `/admin/einstellungen/betrieb`
- [ ] `kleinunternehmer` steht richtig — **steht es falsch, ist der Steuerausweis falsch** (§14c UStG)
- [ ] `benachrichtigung_email` gesetzt, sonst ist `/kontakt` stumm
- [ ] `KEYWORD_VALIDATION.md` je Zeile bestätigt (Spalte „Bestätigt")

### Server

- [ ] `APP_ENV=production` **in der Serverumgebung**, nicht nur in der `.env`
- [ ] `DocumentRoot` auf `/public`
- [ ] `https://…/.env` liefert 403 oder 404 — **ausprobiert**, nicht angenommen
- [ ] `https://…/app/bootstrap.php` liefert 403 oder 404
- [ ] `STORAGE_DIR` liegt außerhalb von `/public` und ist beschreibbar
- [ ] `session.cookie_secure = 1`
- [ ] `expose_php = Off`, `display_errors = Off`, `log_errors = On`
- [ ] HTTP leitet auf HTTPS um
- [ ] `Strict-Transport-Security` steht in der Antwort
- [ ] `Content-Security-Policy` steht in der Antwort und enthält kein `unsafe-inline` für Skripte

### Einrichtung

- [ ] Ersteinrichtung über HTTPS durchlaufen, alle acht Schritte
- [ ] TOTP-Schlüssel in einer echten Authenticator-App, Code hat funktioniert
- [ ] `/admin/setup` liefert 404
- [ ] `php bin/migrate.php status` meldet 0 offene Migrationen
- [ ] `php bin/migrate.php verify` meldet keine Abweichung

### Betrieb

- [ ] Cron eingetragen, am Folgetag die Ausgabe gesehen
- [ ] SPF, DKIM, DMARC gesetzt
- [ ] Testmail an eine **fremde** Adresse, im **Posteingang** angekommen
- [ ] Datenbanksicherung läuft täglich, mit `--triggers`
- [ ] `.env` gesichert, getrennt von der Datenbank, **nicht** im Repository
- [ ] Eine Sicherung einmal zurückgespielt

### Vor dem ersten Kunden

- [ ] Die sechs Mails aus §6.1 gebaut — allen voran `Ihr Angebot von SARTU liegt bereit`
- [ ] Entschieden, ob Block 4 des Cockpits (§6.2) vor dem Start entsteht

---

## Prüfbericht

`SARTU_TEXTREGELN.md` §2. Gezählt mit `tools/textpruefung.py` am 02.08.2026.

```text
TEXTPRUEFUNG   Seite: LIVEGANG.md             Datum: 02.08.2026

Sätze gesamt                           116
Längster Satz                           29 Wörter      Grenze 20   → benannt
Sätze über 20 Wörter                     1             Grenze 0    → benannt
Aufzählungen >3 Glieder im Satz          2             Grenze 0    → benannt
Gegensatzformel                         15             Grenze 2    → benannt
Treffer Wortliste (Füllwörter)           0             Grenze 0
Behauptungen über Kunden / Markt         0             Grenze 0
```

### Die Überschreitungen, einzeln

**Der Satz mit 29 Wörtern ist keiner.** Das Zählskript klebt die Zeile „Der Befehl zuerst:",
den Codeblock darunter und den Folgesatz zusammen; sein eigener Kopf warnt davor. Nachgezählt
sind es drei Sätze mit 3, 12 und 9 Wörtern.

**Zwei Aufzählungen mit mehr als drei Gliedern.** Die eine nennt die gesperrten
Verzeichnisse: `/app`, `/storage`, `/migrations`, `/tests`, `/bin` und die `.env`. Die andere
steht in diesem Bericht. Beide sind vollständige Listen; eine unvollständige Liste in einer
Prüfliste ist ein Fehler.

**15 Gegensatzformeln, bei einer Grenze von 2.** Das ist die größte Überschreitung. Sie ist
zugleich der Inhalt: Diese Datei sagt fast durchgehend, was **nicht** genügt.

Vier Beispiele von fünfzehn:

- die `.env` statt der Serverumgebung
- Mailpit statt eines fremden Postfachs
- eine Warnung statt eines Abbruchs
- eine Vermutung statt einer Gegenprobe

Wer die Formel hier streicht, streicht die Aussage.

**Diese Datei liest der Betreiber, kein Kunde.** Sie wird trotzdem geprüft, weil eine
ungeprüfte Ausnahme der Anfang von zwei ist.
