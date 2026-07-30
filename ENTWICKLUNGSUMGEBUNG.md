# Entwicklungsumgebung — PHP, Composer und MariaDB auf deinem Rechner

**Warum diese Datei existiert:** Die Bauunterlagen beschreiben lückenlos, *was* gebaut wird, und
Portal-Lastenheft §1.4 beschreibt, was der spätere **Server** können muss. Was auf dem
**Entwicklungsrechner** installiert sein muss, stand nirgends. Codex ist beim ersten Start genau
daran hängengeblieben — richtigerweise: Es hat gemeldet und angehalten, statt sich einen Ersatz
auszudenken.

**Es gibt zwei gangbare Wege, und die Datenbank darf warten.** Wer diese Datei liest, um schnell
weiterzukommen: Abschnitt „Was wirklich nötig ist und was nicht" genügt.

---

## Was wirklich nötig ist und was nicht

Die Meldung von Codex nennt vier Dinge in einer Reihe. Sie sind nicht gleich dringend:

| | Aufschiebbar? | Warum |
|---|---|---|
| **PHP + Composer** | **nein** | Ohne PHP läuft **kein einziger** Befehl: kein `php -l`, kein Testfall, kein Seitenaufruf. Codex kann Dateien schreiben, aber nichts davon prüfen |
| **Datenbank** | **ja** | Migrationen und Abfragen lassen sich schreiben und lesen, bevor eine Datenbank existiert. Sie werden erst zum Ausführen gebraucht |

**Der zweite Teil ist genau das, was in anderen Projekten funktioniert hat:** bauen, die Zugangsdaten
später nachreichen, dann läuft es an. Das ist ein gültiger Weg und wird hier nicht verboten —
Abschnitt „Bauen, bevor die Datenbank steht" beschreibt ihn.

Der erste Teil ist es nicht. PHP muss auf dem Rechner sein, in irgendeiner Form.

### Wo dieses Projekt sich von einer normalen Website unterscheidet

Bei einer kleinen Seite prüfst du das Ergebnis, indem du sie anklickst. Hier geht das an einer Stelle
nicht: Das Portal trennt Kundendaten voneinander (`organization_id` aus der Sitzung, nie aus der
Anfrage). Ob diese Trennung hält, **sieht man einer Oberfläche nicht an**. Man sieht es nur, wenn
`tests/TenantIsolationTest.php` gelaufen ist. Ein Fehler dort ist kein Schönheitsfehler, sondern
Kundendaten beim falschen Kunden.

Deshalb die einzige harte Grenze in dieser Datei: **Vor dem Livegang müssen die 77 Testfälle
irgendwo einmal wirklich gelaufen sein** — lokal oder auf dem Server, das ist gleichgültig. Nicht
gelaufene Tests dürfen nie als bestanden gemeldet werden (Portal-Lastenheft §16).

---

## Zwei Wege

| | **Weg A — Docker** | **Weg B — natives Paket** |
|---|---|---|
| Einrichtung | Docker Desktop + `docker compose up` | ein Installer (Laragon, XAMPP, Homebrew) |
| PHP-Version | exakt 8.3, festgeschrieben | was das Paket mitbringt — prüfen |
| Erweiterungen aus §1.4 | beim Bau geprüft, bricht sonst ab | von Hand in `php.ini` |
| Testdatenbank getrennt | ja, eigene Instanz | selbst anlegen |
| Mail lokal prüfbar | ja (Mailpit) | nein, echter SMTP nötig |
| Befehle | mit Vorsatz `docker compose exec app` | direkt, wie gewohnt |
| Auf einem zweiten Rechner | `docker compose up` | von vorn einrichten |

**Beide Wege funktionieren.** Weg A nimmt dir das Nachziehen von Erweiterungen und die zweite
Datenbank ab und kostet dafür einen Vorsatz vor jedem Befehl. Weg B fühlt sich normaler an und
verlangt drei Handgriffe mehr bei der Einrichtung.

### Welcher passt

| Ausgangslage | Weg |
|---|---|
| PHP 8.3 ist schon installiert | **B** — nichts installieren, nur Erweiterungen prüfen |
| Ein PHP-Paket ist da, aber älter oder unvollständig | **B**, nachziehen |
| **Auf dem Rechner ist gar nichts** | **A** — ein Installer und zwei Befehle statt `PATH`, `php.ini` und Erweiterungen von Hand |
| Docker Desktop hakt an WSL 2 oder der Virtualisierung | **B** — kein Kampf wert |

Die dritte Zeile ist der Punkt, der leicht falsch herum gedacht wird: Wenn ohnehin **etwas**
installiert werden muss, ist Docker nicht der aufwendigere, sondern der **kürzere** Weg. Die
Erweiterungen aus §1.4 sind darin bereits richtig gesetzt und werden beim Bau geprüft; bei einer
Installation von Hand sind sie drei Fehlerquellen: falsche Version, `PATH` nicht gesetzt,
`php.ini` nicht angepasst.

---

## Was Codex davon selbst erledigen kann

Codex hat Zugriff auf denselben Rechner und kann Befehle ausführen. Die Arbeitsteilung ist trotzdem
nicht beliebig:

| | Wer |
|---|---|
| Docker Desktop bzw. Laragon **installieren** und starten | **Mensch** |
| Virtualisierung im BIOS/UEFI aktivieren, WSL 2 einrichten, Neustart | **Mensch** |
| Dateien aus `main` holen, `.env` anlegen, Passwörter erzeugen | Codex |
| `docker compose up -d --build` | Codex |
| Prüfen: PHP-Version, Erweiterungen aus §1.4, Datenbankverbindung | Codex |
| Ergebnis berichten, bei Fehlschlag die Ursache benennen | Codex |

Die ersten beiden Zeilen sind nicht aus Vorsicht dort, sondern weil sie nicht skriptbar sind:
Grafische Installer, Rechteanhebung, ein Neustart und eine BIOS-Einstellung. Ein Agent, der in eine
Passwortabfrage läuft, bleibt hängen — und eine BIOS-Einstellung kann keine Software vornehmen.

Unter Windows lässt sich der Installationsteil abkürzen, wenn `winget` vorhanden ist. In einer
PowerShell **als Administrator**:

```
wsl --install
winget install Docker.DockerDesktop
```

Danach neu starten und Docker Desktop einmal von Hand öffnen. Diese Zeilen selbst ausführen, nicht
von Codex ausführen lassen — sie brauchen erhöhte Rechte, und was mit erhöhten Rechten auf dem
eigenen Rechner läuft, sollte man gesehen haben.

Alles ab Schritt 2 kann Codex übernehmen. Der Prompt dafür steht unter „Weiterarbeiten mit Codex".

---

## Weg A — Einrichtung mit Docker

### 1. Docker installieren

| System | Was |
|---|---|
| **Windows** | Docker Desktop. Setzt **WSL 2** voraus und dass **Virtualisierung im BIOS/UEFI aktiv** ist — das ist die mit Abstand häufigste Fehlerquelle. Der Installer sagt es, wenn etwas fehlt |
| **macOS** | Docker Desktop (Apple Silicon und Intel getrennte Downloads) |
| **Linux** | `docker` und `docker compose` aus der Paketverwaltung, danach den eigenen Benutzer der Gruppe `docker` hinzufügen |

Prüfen, bevor es weitergeht:

```
docker --version
docker compose version
```

Beide müssen eine Version ausgeben. Docker Desktop muss **laufen**, nicht nur installiert sein.

### 2. Zugangsdaten anlegen

```
cp .env.example .env
```

Unter Windows in der PowerShell: `Copy-Item .env.example .env`

Dann `.env` öffnen und **beide** Passwortfelder ausfüllen — `DB_PASSWORD` und `DB_ROOT_PASSWORD`.
Irgendein Wert genügt, er gilt nur lokal. Bleiben sie leer, startet die Datenbank nicht.

`.env` steht in `.gitignore` und wird nie committet.

### 3. Starten

```
docker compose up -d --build
```

Der erste Lauf dauert einige Minuten, weil PHP mit den Erweiterungen gebaut wird. Danach geht es in
Sekunden.

### 4. Prüfen

```
docker compose ps
docker compose exec app php -v
docker compose exec app php -m
docker compose exec app composer --version
docker compose exec app php -r "new PDO('mysql:host=db;dbname='.getenv('DB_NAME'), getenv('DB_USER'), getenv('DB_PASSWORD')); echo 'Datenbank erreichbar', PHP_EOL;"
```

Erwartung: PHP 8.3.x, in der Erweiterungsliste stehen `pdo_mysql`, `sodium`, `mbstring`, `intl`,
`fileinfo`, `openssl`, Composer 2.x, und die letzte Zeile meldet die Datenbank als erreichbar.

| Adresse | Was |
|---|---|
| http://localhost:8080 | die Anwendung |
| http://localhost:8025 | Posteingang für alle lokal versendeten Mails |

Solange `public/` noch nicht existiert, liefert Port 8080 einen Fehler. Das ist bis zum Ende von
Sitzung 1 normal.

> **Nur unter Linux:** Der Webserver im Container läuft als `www-data`. Wenn später Uploads nach
> `storage/` nicht geschrieben werden können, liegt es an den Dateirechten des eingehängten
> Verzeichnisses — dann `sudo chown -R 33:33 storage`. Unter Windows und macOS tritt das nicht auf.

---

## Der Punkt, an dem Weg A sonst wieder klemmt

PHP liegt **im Container**, nicht auf dem Wirtssystem. `php -v` in einem normalen Terminal schlägt
weiterhin fehl — und das ist richtig so. Jeder Befehl bekommt einen Vorsatz:

| Statt | Richtig |
|---|---|
| `php irgendwas.php` | `docker compose exec app php irgendwas.php` |
| `composer install` | `docker compose exec app composer install` |
| `vendor/bin/phpunit` | `docker compose exec app vendor/bin/phpunit` |
| `php -l datei.php` | `docker compose exec app php -l datei.php` |
| `mysql -u sartu -p` | `docker compose exec db mariadb -u sartu -p` |

**Das muss Codex wissen**, sonst prüft es erneut das Wirtssystem, findet nichts und hält wieder an.
Der Satz dafür steht unten.

---

## Weiterarbeiten mit Codex

Der bisherige Stand liegt unverändert auf `feature/fundament-und-designvarianten`. Nichts geht
verloren.

**Zuerst** die neuen Dateien in den Arbeitszweig holen — ohne die Zweige zusammenzuführen:

```
git fetch origin main
git checkout origin/main -- docker-compose.yml .docker .env.example ENTWICKLUNGSUMGEBUNG.md
```

`.gitignore` bewusst nicht in dieser Liste: Falls Codex bereits eine angelegt hat, bliebe sie
erhalten. Fehlt sie, zusätzlich `git checkout origin/main -- .gitignore`.

Dann die Einrichtung durchlaufen und Codex den passenden Satz geben.

### Einrichtung an Codex übergeben (Weg A, ab Schritt 2)

Voraussetzung: Docker Desktop ist installiert und **läuft**.

> Docker Desktop läuft jetzt auf diesem Rechner. Richte die Entwicklungsumgebung ein und melde das
> Ergebnis. Installiere nichts nach und frage bei nichts nach erhöhten Rechten — wenn etwas fehlt,
> nenne es und halte an.
>
> 1. `docker --version` und `docker compose version` prüfen. Kommt keine Ausgabe: melden und anhalten
> 2. Fehlende Dateien aus `main` holen:
>    `git fetch origin main` und
>    `git checkout origin/main -- docker-compose.yml .docker .env.example ENTWICKLUNGSUMGEBUNG.md`
> 3. `.env` aus `.env.example` anlegen, falls noch nicht vorhanden. `DB_PASSWORD` und
>    `DB_ROOT_PASSWORD` mit je einem zufälligen Wert füllen. `.env` **niemals** committen —
>    sie steht in `.gitignore`
> 4. `docker compose up -d --build`
> 5. Prüfen und die Ausgaben zeigen:
>    `docker compose exec app php -v` — muss 8.3 oder höher sein
>    `docker compose exec app php -m` — muss `pdo_mysql`, `sodium`, `mbstring`, `intl`, `fileinfo`,
>    `openssl` enthalten
>    `docker compose exec app composer --version`
>    Datenbankverbindung mit einem kurzen PDO-Aufruf gegen `db`
> 6. Bericht: was läuft, was nicht, und bei einem Fehlschlag die Ursache — nicht nur die
>    Fehlermeldung weiterreichen
>
> Danach anhalten. Baue in diesem Durchgang keinen Produktionscode.

Der letzte Satz trennt die Einrichtung sauber vom Bau: Wenn beides in einem Durchlauf passiert und
etwas schiefgeht, ist hinterher unklar, woran es lag.

### Einrichtung an Codex übergeben (Weg B, Windows ohne Administratorrechte)

> Richte auf diesem Windows-Rechner PHP 8.3 und Composer ein, ohne Administratorrechte und ohne
> Docker. **Fordere zu keinem Zeitpunkt erhöhte Rechte an** — wenn ein Schritt sie brauchen würde,
> halte an und sage mir, welcher.
>
> 1. Prüfen, ob schon etwas da ist: `php -v`, `composer --version`
> 2. PHP 8.3 oder höher, **Thread Safe, x64**, als ZIP von `windows.php.net/download` herunterladen
>    und nach `C:\php` entpacken
> 3. Dort `php.ini-development` zu `php.ini` kopieren. In `php.ini` `extension_dir = "ext"` setzen
>    und die Erweiterungen `pdo_mysql`, `sodium`, `mbstring`, `intl`, `fileinfo`, `openssl`
>    freischalten
> 4. `C:\php` an die **Benutzer**-Umgebungsvariable `Path` anhängen (`'User'`, nicht `'Machine'` —
>    letzteres bräuchte erhöhte Rechte)
> 5. Composer installieren
> 6. In einem **neuen** Terminal prüfen und die Ausgaben zeigen: `php -v` (muss 8.3+ sein),
>    `php -m` (muss die sechs Erweiterungen enthalten), `composer --version`
> 7. Bericht: was läuft, was nicht, und bei einem Fehlschlag die Ursache — nicht nur die
>    Fehlermeldung weiterreichen
>
> Eine Datenbank wird jetzt noch nicht gebraucht. Danach anhalten, keinen Produktionscode bauen.

### Nach Weg A (Docker)

> Die Umgebung steht jetzt, aber sie läuft in Docker, nicht auf dem Wirtssystem. PHP, Composer,
> MariaDB und ein Mailfänger sind über `docker-compose.yml` verfügbar.
>
> **Führe jeden PHP-, Composer- und Testbefehl im Container aus**, mit dem Vorsatz
> `docker compose exec app …` — zum Beispiel `docker compose exec app php -v`,
> `docker compose exec app composer install`, `docker compose exec app vendor/bin/phpunit`.
> Auf dem Wirtssystem gibt es kein `php` und wird auch keins geben; ein fehlgeschlagenes `php -v`
> dort ist kein Grund anzuhalten. Die Datenbanken erreichst du unter dem Hostnamen `db`
> beziehungsweise `db_test`, nicht unter `localhost`.
>
> Einzelheiten in `ENTWICKLUNGSUMGEBUNG.md`. Prüfe zuerst mit `docker compose exec app php -m`, dass
> die Erweiterungen aus Portal-Lastenheft §1.4 vorhanden sind, und setze dann auf
> `feature/fundament-und-designvarianten` fort, wo du aufgehört hast. Lies dazu deinen letzten
> Commit und `IMPLEMENTATION_PLAN.md`, falls vorhanden — **nicht** das gesamte Repository erneut.

### Nach Weg B (natives Paket)

> PHP 8.3, Composer und MariaDB sind jetzt direkt auf dem Rechner installiert und über `PATH`
> erreichbar. Zugangsdaten stehen in `.env`, Datenbanken: `sartu` zum Arbeiten, `sartu_test` für
> die Testfälle.
>
> Prüfe zuerst mit `php -m`, dass die Erweiterungen aus Portal-Lastenheft §1.4 vorhanden sind, und
> setze dann auf `feature/fundament-und-designvarianten` fort, wo du aufgehört hast. Lies dazu
> deinen letzten Commit und `IMPLEMENTATION_PLAN.md`, falls vorhanden — **nicht** das gesamte
> Repository erneut.

### Wenn noch keine Datenbank da ist

> Bau weiter, ohne auf die Datenbank zu warten. PHP und Composer stehen bereit, eine Datenbank noch
> nicht — die Zugangsdaten reiche ich später nach.
>
> Baue alles, was ohne Datenbank vollständig ist: Gerüst, Verzeichnisstruktur, Migrationsdateien,
> Datenzugriffsschicht, Dienste, Ansichten, Texte, Testdateien. Migrationen ausführen und Testfälle
> laufen lassen kommt später.
>
> **Melde nichts als geprüft, was nicht ausgeführt wurde.** Führe stattdessen `OFFENE_PRUEFUNGEN.md`
> mit je einer Zeile: was gebaut wurde, was daran ungeprüft ist, womit es geprüft wird, sobald die
> Datenbank steht. `php -l` läuft auch ohne Datenbank — das gehört gemacht, nicht aufgeschoben.
>
> Halte **nicht** an, weil eine Datenbank fehlt. Halte nur an bei Widersprüchen in den Vorgaben oder
> fehlenden Informationen, die du sonst erfinden müsstest.

Der jeweils letzte Halbsatz zum Repository ist kein Beiwerk: Eine frische Sitzung liest sonst
reflexhaft alles noch einmal ein und verbraucht Kontingent für Kontext statt für Arbeit.

---

## Weg B — natives Paket, ohne Docker

Vollwertig, nur mit ein paar Handgriffen mehr. `docker-compose.yml` bleibt liegen und stört nicht.

### 1. Installieren

| System | Womit |
|---|---|
| **Windows** | **PHP einzeln** (siehe unten — braucht keine Administratorrechte) oder Laragon/XAMPP, wenn zusätzlich MySQL mit soll |
| **macOS** | `brew install php@8.3 composer mariadb` |
| **Linux** | über die Paketverwaltung: `php8.3`, `php8.3-mysql`, `php8.3-intl`, `php8.3-mbstring`, `composer`, `mariadb-server` |

#### Windows ohne Administratorrechte

Für Sitzung 1 genügen PHP und Composer — MySQL kommt später. Das lässt sich vollständig im
Benutzerprofil einrichten, ohne Rechteanhebung und ohne Virtualisierung:

1. **PHP 8.3+ (Thread Safe, x64)** von `windows.php.net/download` als ZIP herunterladen und nach
   `C:\php` entpacken
2. Dort `php.ini-development` zu `php.ini` kopieren
3. In `php.ini` das Semikolon vor diesen Zeilen entfernen und `extension_dir` setzen:
   ```
   extension_dir = "ext"
   extension=pdo_mysql
   extension=sodium
   extension=mbstring
   extension=intl
   extension=fileinfo
   extension=openssl
   ```
4. `C:\php` in die **Benutzer**-Umgebungsvariable `Path` eintragen — das geht ohne
   Administratorrechte:
   ```powershell
   [Environment]::SetEnvironmentVariable('Path', $env:Path + ';C:\php', 'User')
   ```
   Danach ein **neues** Terminalfenster öffnen, sonst greift die Änderung nicht
5. Composer über `Composer-Setup.exe` von `getcomposer.org` — findet das PHP aus Schritt 4 selbst

Prüfen: `php -v` zeigt 8.3 oder höher, `php -m` enthält die sechs Erweiterungen,
`composer --version` zeigt 2.x.

**Diese fünf Schritte kann Codex ausführen** — sie brauchen keine erhöhten Rechte. Der Prompt dafür
steht unter „Weiterarbeiten mit Codex".

### 2. Erweiterungen prüfen

```
php -v
php -m
composer --version
```

`php -m` **muss** enthalten: `pdo_mysql`, `sodium`, `mbstring`, `intl`, `fileinfo`, `openssl`.
Fehlt eine, in der `php.ini` die passende Zeile entkommentieren (`extension=intl` und so weiter) und
neu prüfen. Welche `php.ini` gilt, verrät `php --ini`.

`php -v` muss **8.3 oder höher** zeigen. XAMPP liefert je nach Version noch 8.1 aus — dann die
aktuelle Fassung holen, nicht mit der alten anfangen.

### 3. Zwei Datenbanken anlegen

**Für Sitzung 1 noch nicht nötig** — Plan, Gerüst und Designvarianten brauchen keine Datenbank.
Wer schnell anfangen will, überspringt diesen Schritt und holt ihn vor Sitzung 2 nach; siehe
„Bauen, bevor die Datenbank steht".

Dann aber nicht eine, sondern zwei. Die Testfälle leeren Tabellen; mit nur einer Datenbank sind
irgendwann deine Arbeitsdaten weg.

```sql
CREATE DATABASE sartu       CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE sartu_test  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sartu'@'localhost' IDENTIFIED BY 'dein_lokales_passwort';
GRANT ALL PRIVILEGES ON sartu.*      TO 'sartu'@'localhost';
GRANT ALL PRIVILEGES ON sartu_test.* TO 'sartu'@'localhost';
FLUSH PRIVILEGES;
```

Der naheliegende Ausweg, Tests stattdessen gegen SQLite laufen zu lassen, ist in Portal-Lastenheft
§16 ausdrücklich verboten — SQLite verhält sich bei Transaktionen und Fremdschlüsseln anders, ein
grüner Test dort sagt nichts über MySQL.

### 4. `.env` anlegen

```
cp .env.example .env
```

Darin auf die lokale Installation umstellen: `DB_HOST=127.0.0.1`, `DB_HOST_TEST=127.0.0.1`,
`DB_PASSWORD=` dein Passwort von oben. `DB_ROOT_PASSWORD` bleibt leer — das braucht nur Docker.

### 5. Mail

Es gibt keinen Mailfänger. Der gesamte Zugang läuft über Anmeldelinks (Portal-Lastenheft §5) — ohne
sichtbare Mail kommst du lokal nicht ins Portal. Zwei Möglichkeiten:

- **Anmeldelink zusätzlich ins Protokoll schreiben**, solange `APP_ENV=development`. Der einfachste
  Weg; muss in der Produktion zwingend abgeschaltet sein
- **echten SMTP-Zugang** eintragen und an eine eigene Adresse senden

Die erste Möglichkeit gehört ausdrücklich in `OFFENE_PRUEFUNGEN.md`, damit sie vor dem Livegang
wieder verschwindet.

---

## Bauen, bevor die Datenbank steht

Das ist der Weg, der in anderen Projekten schon funktioniert hat, und er gilt hier weiter: Codex
baut, die Zugangsdaten kommen später, dann läuft es an. **Mit einer Bedingung** — die aufgeschobene
Prüfung muss **sichtbar** bleiben, nicht stillschweigend als erledigt gelten.

### Was ohne Datenbank vollständig gebaut werden kann

Projektgerüst, Verzeichnisstruktur, Migrationsdateien, Datenzugriffsschicht, Dienste, Ansichten,
sämtliche Texte, das Designsystem, die Startseitenvarianten aus Sitzung 1, die Testdateien selbst.
Das ist der weit überwiegende Teil.

### Was erst mit Datenbank geht

Migrationen ausführen, die 77 Testfälle, jeder Seitenaufruf, der Daten liest.

### Die Regel

Was nicht ausgeführt wurde, wird **nicht** als geprüft gemeldet. Statt dessen führt Codex eine Datei
`OFFENE_PRUEFUNGEN.md` mit je einer Zeile: was gebaut wurde, was daran ungeprüft ist, womit es
geprüft wird, sobald die Umgebung steht.

Das ist der Unterschied zwischen „später testen" und „nie testen". Ohne diese Liste verschwindet die
Prüfung im Rauschen und niemand merkt es — bis ein Kunde die Daten eines anderen sieht.

### Die eine harte Grenze

**Vor dem Livegang müssen die 77 Testfälle einmal wirklich gelaufen sein**, insbesondere
`tests/TenantIsolationTest.php`. Wo, ist gleichgültig: lokal, auf einem Testserver, beim Hoster.
Dass die Oberfläche richtig aussieht, ist **kein** Beleg dafür — ob Kunde A die Angebote von Kunde B
lesen kann, sieht man einem Bildschirm nicht an.

---

## Zum Server ist es später ein kurzer Weg

Beide Wege bilden Portal-Lastenheft §1.4 ab, aber **keiner** ist eine Produktionsumgebung: HTTP statt
HTTPS, `display_errors` an, `session.cookie_secure` aus, Mail wird abgefangen oder ins Protokoll
geschrieben.

Für den Livegang bleibt es bei der Entscheidung aus `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4: klassisches
Hosting mit PHP und MySQL. Genau deshalb steht hier gewöhnliches Apache plus PHP und nichts
Exotisches — was hier läuft, läuft dort.
