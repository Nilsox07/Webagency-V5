# Ablauf: von den Konzeptdateien zur fertigen Website

**Drei Codex-Sitzungen, zwei Entscheidungspunkte dazwischen.** Nicht eine Sitzung, die alles baut —
die Gründe stehen unten.

---

## Vorbereitung (einmalig, ohne die kein Start)

| # | Was | Warum |
|---|---|---|
| 1 | **`main` als Standardbranch setzen** (Settings → General → Default branch) | `main` existiert und hat den vollen Stand. Solange der alte Branch Standard ist, laufen beide auseinander |
| 2 | **Hosting praktisch prüfen** — Testmail an eine Fremdadresse (Posteingang oder Spam?) und ein Cronlauf, der eine Datei schreibt | Portal-Lastenheft §1.4. Fehlt eines, ist der Tarif ungeeignet. Das jetzt zu klären kostet eine Stunde, später eine Migration |
| 3 | **Entscheiden, wo der Code hinkommt** | Empfehlung: **dasselbe Repository**. Die Konzeptdateien bleiben im Wurzelverzeichnis, der Code entsteht daneben in `app/`, `public/`, `portal/`, `admin/`, `api/`, `migrations/`, `tests/`. Damit funktioniert die Startprüfung unverändert |

**Nicht nötig vor dem Start:** Standortentscheidung und Designrichtung. Beide sind als Gates
dokumentiert und blockieren den Bau nicht.

---

## Sitzung 1 — Fundament und Designvarianten *(kurz, endet mit einer Entscheidung)*

**Was entsteht:** `IMPLEMENTATION_PLAN.md`, das Projektgerüst nach Portal-Lastenheft §1.3, und
**2–3 klickbare Startseitenvarianten** mit den echten Texten aus dem Website-Lastenheft.

**Dann Stopp.** Du entscheidest die Designrichtung. Das kann niemand sonst.

**Aufwand für dich:** den Plan lesen (10 Minuten), die Varianten ansehen und entscheiden.

---

## Sitzung 2 — Kundenbereich *(lang, läuft weitgehend allein)*

**Was entsteht:** `/portal/` und `/admin/` vollständig, Etappe 1–5 aus `CODEX_AUFTRAG_PORTAL.md`,
59 Testfälle, am Ende der **Screenshot-Satz aus der echten Oberfläche**.

Hier ist am wenigsten zu entscheiden: Datenmodell, Statuslogik, alle Texte und alle E-Mails stehen
fest. Genau deshalb eignet sich dieser Teil für einen langen Durchlauf.

**Deine Rolle:** nach jeder Etappe den Bericht lesen. Eingreifen nur, wenn etwas gemeldet wird.

---

## Sitzung 3 — Öffentliche Seiten *(lang, läuft weitgehend allein)*

**Was entsteht:** alle Seiten aus `CODEX_AUFTRAG_WEBSITE.md` im gewählten Design, mit den echten
Screenshots aus Sitzung 2.

**Zwei Pflichtdateien vor den Langtexten:** `KEYWORD_VALIDATION.md` (Aufbau in Keywordstrategie
§1.1) und die abgearbeitete `GEO_DISCOVERY_CHECKLIST.md`.

**Deine Rolle:** die Entscheidungen in `KEYWORD_VALIDATION.md` treffen — sie legen Titel und
Adressen fest, und Adressen ändert man später nur noch mit Weiterleitungen. Dazu die selbst
geschriebenen Texte prüfen (5 Leistungsseiten, 3 Transparenzseiten, 2 Vergleichsartikel,
8 Lexikonbegriffe) und die gemeldeten Copy-Konflikte entscheiden.

---

## Warum nicht alles in einem Durchlauf?

Die beiden Gates sind kein Formalismus. Sie verhindern die zwei teuersten Fehler:

| Ohne Gate | Kosten |
|---|---|
| Codex wählt eine Struktur, du siehst sie erst nach Tagen | alles danach steht auf der falschen Grundlage |
| Codex baut 40 Seiten in einer Optik, die du ablehnst | 40 Seiten neu |

**Zwischen den Gates darf es dagegen durchlaufen.** Sitzung 2 und 3 sind lang, autonom und brauchen
dich nur zum Lesen der Berichte. Das ist der erreichbare Automatisierungsgrad — und er ist hoch.

**Was nie automatisch geht**, unabhängig vom Werkzeug: Designentscheidung · Standortentscheidung ·
Rechtstexte · echte Fotos · die Freigabe eigener Texte. Alles davon steht in
`SARTU_ENTSCHEIDUNGEN_OFFEN.md`.

---

## Startprompt für Sitzung 1

> Du arbeitest im aktuellen Verzeichnis — ein lokaler Klon von `github.com/Nilsox07/Webagency-V5`.
> Den Code baust du hier hinein, neben die vorhandenen Konzeptdateien.
>
> **Schritt 0 — Umgebung einrichten.** Auf diesem Windows-Rechner sind weder PHP noch Composer
> installiert, und Docker steht nicht zur Verfügung. Richte beides ein, **ohne Administratorrechte**.
> Fordere zu keinem Zeitpunkt erhöhte Rechte an — wenn ein Schritt sie brauchen würde, halte an und
> sage, welcher.
> 1. Prüfen, ob doch schon etwas da ist: `php -v`, `composer --version`
> 2. PHP 8.3 oder höher, **Thread Safe, x64**, als ZIP von `windows.php.net/download` herunterladen
>    und nach `C:\php` entpacken
> 3. Dort `php.ini-development` zu `php.ini` kopieren. `extension_dir = "ext"` setzen und die
>    Erweiterungen `pdo_mysql`, `sodium`, `mbstring`, `intl`, `fileinfo`, `openssl` freischalten
> 4. `C:\php` an die **Benutzer**-Umgebungsvariable `Path` anhängen (`'User'`, nicht `'Machine'`)
> 5. Composer installieren
> 6. In einem **neuen** Terminal prüfen und die Ausgaben zeigen: `php -v` (muss 8.3+ sein), `php -m`
>    (muss die sechs Erweiterungen enthalten), `composer --version`
>
> **Eine Datenbank wird jetzt nicht eingerichtet und nicht gebraucht.** Sie kommt vor Sitzung 2.
> Halte deswegen an keiner Stelle an.
>
> **Schritt 1 — Bestand.** Sieh nach, ob auf dem Branch `feature/fundament-und-designvarianten`
> bereits Arbeit liegt (letzter Commit, vorhandene `IMPLEMENTATION_PLAN.md`). Falls ja: dort
> fortsetzen statt neu anfangen, und in einem Satz sagen, was schon steht.
>
> **Schritt 2 — Startprüfung.** Lies `UEBERGABE_DATEILISTE.md` und führe die dort beschriebene
> Startprüfung für den **Portalauftrag** aus. Melde das Ergebnis als ersten Absatz: welche Dateien
> gefunden, ob das Hauptdokument vollständig ist. Fehlt eine Datei mit „Abbruch": nicht anfangen,
> melden.
>
> **Schritt 3 — Lesen.** Lies vollständig, in dieser Reihenfolge:
> 1. `CODEX_AUFTRAG_PORTAL.md`
> 2. `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` — besonders §1 (Stack, Struktur, Hosting)
> 3. `CODEX_AUFTRAG_WEBSITE.md`
> 4. `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`
> 5. `SARTU_ENTSCHEIDUNGEN_OFFEN.md`
> 6. `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` — §1, §2 und §5 (Startseitentexte)
>
> **Schritt 4 — `IMPLEMENTATION_PLAN.md` schreiben** nach Abschnitt 0b des Portalauftrags: Bestand,
> Umgang mit vorhandenen Prototypen, Zielstruktur, Modulgrenzen, Datenmodellquelle, Reihenfolge,
> Risiken, Testplan, offene Entscheidungen. **Vorlegen, bevor du Produktionscode schreibst.**
>
> **Schritt 5 — Kleinster lauffähiger Stand.** Projektgerüst nach Portal-Lastenheft §1.3, die erste
> Migrationsdatei, eine Seite, ein Test. Die Migration wird jetzt nur **geschrieben**, nicht
> ausgeführt — das kommt mit der Datenbank. `php -l` über alle erzeugten Dateien laufen lassen; das
> geht ohne Datenbank und gehört gemacht. Berichten.
>
> **Schritt 6 — Designvarianten nach dem Copy-/Port-Verfahren.** Lies
> `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` §2.1 und §3.1 vollständig. Das ist **kein**
> „entwirf etwas Schönes": Wähle je Variante **1–3 sehr gute, sauber lizenzierte Quellen** und
> **übernimm deren konkreten Aufbau** — Markup, CSS-Ansatz, Zustände, Interaktionslogik. Angepasst
> werden Farben, Schriften, Abstände, Texte. Utility-Klassen übersetzt du in **eigenes CSS mit
> zentralen Variablen**, weil es keinen Build-Schritt gibt. React-Verhalten baust du als kleines
> eigenes JavaScript-Modul nach.
> Grenze nach Ebene: Bausteine dürfen sehr nah sein, Sektionen übernimmst du in der Mechanik und
> setzt sie neu zusammen, ganze Seitengerüste nie.
> Ergebnis: **2–3 klickbare Startseitenvarianten** mit den **echten** Texten aus Website-Lastenheft
> §5, als echte PHP-Seiten im Projekt.
> **Nur kostenlose Quellen** mit erlaubter Weitergabe (MIT, Apache-2.0, ISC, BSD, CC0, Schriften OFL).
> Keine gekauften Sammlungen. Lizenzdatei jeweils selbst lesen.
> Vorlegen mit: gewählten Quellen und Begründung, **Lizenz je Quelle mit Beleg**, Herkunftsliste je
> Komponente, gemessenen KB gzip, Laborwerten.
>
> **Dann anhalten.** Der Mensch entscheidet die Designrichtung. Baue keine weiteren Seiten.
>
> **Regeln, die durchgehend gelten:**
> - **Eine fehlende Datenbank ist kein Grund anzuhalten.** Weiterbauen, was ohne sie fertig wird,
>   und Ungeprüftes in `OFFENE_PRUEFUNGEN.md` eintragen — je eine Zeile: was gebaut wurde, was daran
>   ungeprüft ist, womit es geprüft wird, sobald die Datenbank steht.
>   **Melde nichts als geprüft, was nicht ausgeführt wurde**
> - Anmeldelinks werden lokal nicht versendet. Solange `APP_ENV=development`, den Link zusätzlich
>   ins Protokoll schreiben — und diesen Behelf in `OFFENE_PRUEFUNGEN.md` eintragen, damit er vor
>   dem Livegang wieder verschwindet
> - PHP 8.3+, serverseitig gerendert, MySQL/MariaDB, PDO mit vorbereiteten Anweisungen. **Kein**
>   WordPress, **kein** Vollframework, **kein** React/Vue/Next, **kein** Node als Zielsystem,
>   **kein** Build-Schritt fürs Frontend, **keine** externen CDNs
> - **Nur `/public` ist über den Webserver erreichbar**
> - **Keine Secrets im Repository.** Nur `.env.example`
> - **Nichts erfinden:** keine Rechtstexte, keine Anschriften, keine Referenzen, keine Ortsnamen,
>   keine Marktzahlen. Platzhalter aus `SARTU_ENTSCHEIDUNGEN_OFFEN.md` bleiben stehen
> - Bei Widerspruch oder fehlender Information: **melden und anhalten**, nicht raten
> - Sprache der Oberfläche: deutsch, „Sie". Nach außen heißt es **Kundenbereich**, nie App oder
>   Dashboard
> - Die Tests laufen gegen eine **echte** MySQL/MariaDB-Datenbank. Weiche nicht auf SQLite oder einen
>   Ersatz im Speicher aus und berichte nichts als grün, was nicht gelaufen ist
>
> **Git:** Committe nach jedem Schritt einzeln auf dem Branch `feature/fundament-und-designvarianten`,
> nicht auf `main`. Push erst auf Ansage. Lege eine `.gitignore` an; committe niemals `.env`,
> Zugangsdaten oder `vendor/`.

---

## Startprompt für Sitzung 2

> Fortsetzung im Repository `github.com/Nilsox07/Webagency-V5`.
>
> Die Designrichtung ist entschieden: **[Variante X]**. Setze sie als zentrale Variablen um.
>
> **Lies zuerst `REIHENFOLGE.md`.** Gebaut wird **nur Stufe A** — zwölf der achtzehn Tabellen und
> der Weg, den ein erster Kunde tatsächlich geht. Stufe B und C werden **nicht** angelegt, auch
> nicht als leere Hüllen oder auskommentierte Entwürfe.
>
> Baue jetzt `CODEX_AUFTRAG_PORTAL.md`, Etappen 1 bis 5, **im Umfang von Stufe A**, durch. **Ich arbeite nicht
> Etappe für Etappe mit** — berichte nach jeder Etappe und arbeite weiter, ohne auf Freigabe zu
> warten. **Anhalten nur** bei einem Widerspruch in den Vorgaben, einer fehlenden Information, die
> du sonst erfinden müsstest, oder einer Frage zu Sicherheit und Umfang.
>
> Am Ende: alle 59 Testfälle grün, Definition of Done abgehakt, `IMPLEMENTATION_SUMMARY.md`, und der
> **Screenshot-Satz aus der echten Oberfläche** nach Abschnitt 7a.
>
> **Git:** Branch `feature/kundenbereich`. Committe nach **jeder Etappe** einzeln, mit einer Zeile,
> was fertig ist und was noch fehlt. Nie mehrere Etappen in einen Commit sammeln. Committe niemals
> `.env`, Zugangsdaten oder `vendor/`.

---

## Startprompt für Sitzung 3

> Fortsetzung im Repository `github.com/Nilsox07/Webagency-V5`.
>
> Der Kundenbereich steht, die Screenshots liegen vor. Baue jetzt `CODEX_AUFTRAG_WEBSITE.md`,
> Phase 2, vollständig durch — alle Seiten im entschiedenen Design.
>
> **Ich arbeite nicht Seite für Seite mit** — berichte in Blöcken und arbeite weiter. **Anhalten
> nur** bei Widersprüchen, fehlenden Informationen oder wenn dir ein Konflikt zwischen der
> vorgegebenen Copy und der Suchintention auffällt: dann baust du die vorgegebene Fassung, meldest
> den Konflikt und legst einen Gegenvorschlag daneben.
>
> **Bevor du Langtexte schreibst:** Lege `KEYWORD_VALIDATION.md` an (Aufbau: Keywordstrategie §1.1)
> und arbeite `GEO_DISCOVERY_CHECKLIST.md` ab. Steht dir kein Volumenwerkzeug zur Verfügung,
> kennzeichne die Datei als „ohne Volumendaten" und lass die Spalte leer — schätze nichts.
>
> Baue außerdem die Herkunftserfassung ein (Website-Lastenheft §9.5b, Portal-Lastenheft §4b.7):
> beim **ersten** Seitenaufruf in die Sitzung schreiben, nicht erst beim Absenden.
>
> Am Ende: Definition of Done aus Website-Lastenheft §17 Punkt für Punkt abgehakt, Herkunftsliste,
> Messwerte, Offene-Punkte-Liste.
>
> **Git:** Branch `feature/oeffentliche-seiten`. Committe nach **jedem Seitenblock** einzeln, mit
> einer Zeile, was fertig ist und was noch fehlt. Committe niemals `.env`, Zugangsdaten oder
> `vendor/`.

---

## Wenn das Kontingent mitten in einer Sitzung ausgeht

Das ist der teuerste denkbare Fehler, wenn nicht committet wurde: Der Stand ist weg, und die Sitzung
beginnt von vorn. Deshalb steht in jedem Startprompt oben eine Commit-Regel — sie ist keine
Ordnungsliebe, sondern die Wiederaufnahmefähigkeit.

**Fortsetzen, nicht neu starten:**

> Fortsetzung im Repository. Lies `IMPLEMENTATION_PLAN.md` und den letzten Commit auf Branch
> `[BRANCH]`. Sage mir in drei Sätzen, was fertig ist und was als Nächstes ansteht — **bevor** du
> weiterbaust. Lies **nicht** das gesamte Repository ein; nur die Dateien der Übergabeliste für
> diesen Auftrag.

Der letzte Satz ist der wichtige. Eine frische Sitzung liest sonst reflexhaft alles noch einmal ein
und verbraucht das nachgeladene Kontingent für Kontext statt für Arbeit.

---

## Was du zwischendurch selbst tun musst

| Wann | Was |
|---|---|
| **vor Sitzung 1** | **Entwicklungsumgebung einrichten** — `ENTWICKLUNGSUMGEBUNG.md`. Ohne PHP, Composer und MariaDB hält Codex nach wenigen Minuten an, egal wie gut die Unterlagen sind |
| vor Sitzung 1 | Branch nach `main` mergen · Hosting prüfen (Mail, Cron) |
| nach Sitzung 1 | **Designrichtung entscheiden** |
| jederzeit | **Standortentscheidung** — blockiert den Bau nicht, aber das Google-Unternehmensprofil und damit den schnellsten Kundenkanal |
| vor dem Livegang | Rechtstexte von einer Kanzlei · echtes Foto für `/ueber-uns` · echte Anschrift ins Impressum |
| parallel ab sofort | **Direktansprache** starten (Masterkonzept §23b). Die ersten Kunden kommen nicht über die Website |

---

## Eine Warnung zu den Datenbanktests

Portal-Lastenheft §1.2 verlangt Tests gegen **echtes MySQL/MariaDB**, keinen Ersatz im Speicher.
Steht in der Codex-Umgebung keine Datenbank zur Verfügung, kann Etappe 1 nicht sauber abgeschlossen
werden.

**Dann gilt:** Codex schreibt die Tests trotzdem vollständig und dokumentiert in `README.md`, wie sie
lokal ausgeführt werden — und **meldet ausdrücklich**, dass sie in der Umgebung nicht liefen. Nicht
stillschweigend auf einen Ersatz ausweichen und nicht als „grün" berichten, was nie gelaufen ist.
