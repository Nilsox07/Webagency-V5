# Entwicklungsumgebung — PHP, Composer und MariaDB auf deinem Rechner

**Warum diese Datei existiert:** Die Bauunterlagen beschreiben lückenlos, *was* gebaut wird, und
Portal-Lastenheft §1.4 beschreibt, was der spätere **Server** können muss. Was auf dem
**Entwicklungsrechner** installiert sein muss, stand nirgends. Codex ist beim ersten Start genau
daran hängengeblieben — richtigerweise: Es hat gemeldet und angehalten, statt sich einen Ersatz
auszudenken.

---

## Entscheidung: Docker, nicht XAMPP

| | Docker | XAMPP / Laragon / MAMP | PHP direkt installiert |
|---|---|---|---|
| PHP-Version festgelegt | **ja**, exakt 8.3 | was das Paket gerade mitbringt | was das System gerade hat |
| Erweiterungen aus §1.4 | **beim Bau geprüft**, bricht sonst ab | von Hand in `php.ini` nachziehen | von Hand nachziehen |
| Echte MariaDB für die 59 Tests | **ja**, eigene Instanz | eine gemeinsame Instanz | separat zu installieren |
| Mail lokal prüfbar | **ja** (Mailpit) | nein | nein |
| Zweiter Rechner später | `docker compose up` | von vorn einrichten | von vorn einrichten |
| Rückstandsfrei entfernbar | **ja** | nur teilweise | nein |

Der entscheidende Punkt ist die dritte Zeile. Portal-Lastenheft §16 verlangt, dass die 59 Testfälle
gegen eine **echte** MySQL/MariaDB laufen — kein SQLite, kein Ersatz im Speicher. Mit einer einzigen
gemeinsamen Datenbank löscht früher oder später ein Test die Arbeitsdaten. Hier gibt es zwei
getrennte Instanzen, und die Testdatenbank liegt im Arbeitsspeicher und ist bei jedem Start leer.

**Es läuft weiterhin vollständig auf deinem Rechner.** Ein Container ist kein Server im Netz,
sondern ein abgegrenzter Bereich auf derselben Festplatte. Codex arbeitet unverändert lokal.

---

## Einrichtung

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

## Der Punkt, an dem es sonst wieder klemmt

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

Dann die Einrichtung oben durchlaufen. **Danach** an Codex:

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

Der letzte Halbsatz ist kein Beiwerk: Eine frische Sitzung liest sonst reflexhaft alles noch einmal
ein und verbraucht Kontingent für Kontext statt für Arbeit.

---

## Wenn du Docker nicht willst

Legitim, aber teurer in der Einrichtung. Dann brauchst du:

- **PHP 8.3+**, in der `php.ini` aktiviert: `pdo_mysql`, `sodium`, `mbstring`, `intl`, `fileinfo`,
  `openssl`
- **Composer 2**
- **MariaDB 10.6+ oder MySQL 8**, mit **zwei** Datenbanken — eine zum Arbeiten, eine für die Tests
- alle Befehle über `PATH` erreichbar
- für Anmeldelinks entweder echten SMTP-Zugang oder einen lokalen Mailfänger

Grobe Wege: Windows über Laragon oder XAMPP plus Composer-Installer; macOS über Homebrew
(`brew install php@8.3 composer mariadb`); Linux über die Paketverwaltung der Distribution.

**Der Punkt, der dabei gern untergeht:** die zweite, getrennte Testdatenbank. Ohne sie löschen die
Testfälle irgendwann deine Arbeitsdaten — und der naheliegende Ausweg, Tests gegen SQLite laufen zu
lassen, ist in Portal-Lastenheft §16 ausdrücklich verboten.

---

## Zum Server ist es später ein kurzer Weg

Der Container bildet Portal-Lastenheft §1.4 ab, ist aber **keine** Produktionsumgebung: HTTP statt
HTTPS, `display_errors` an, `session.cookie_secure` aus, Mail wird abgefangen statt versendet.

Für den Livegang bleibt es bei der Entscheidung aus `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4: klassisches
Hosting mit PHP und MySQL. Genau deshalb steht hier ein gewöhnliches Apache-plus-PHP-Abbild und
nichts Exotisches — was hier läuft, läuft dort.
