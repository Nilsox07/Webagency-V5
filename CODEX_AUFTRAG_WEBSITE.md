# Auftrag an Codex — SARTU-Website bauen

**Das ist die Übergabe-Anweisung.** Gib Codex diese Datei als Auftrag; alles Weitere steht in den hier verlinkten Dokumenten.

---

## 0. Startprüfung — bevor du anfängst

**Führe zuerst die Startprüfung aus `UEBERGABE_DATEILISTE.md` aus.** Prüfe, ob alle dort für den
Websiteauftrag genannten Dateien vorhanden sind, und ob das Hauptdokument die Abschnitte `## 0.` bis
`## 17a.` enthält — insbesondere `## 9.` und `## 14a.`

**Fehlt `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md`, ist dieser Auftrag nicht baubar.** Melde das
und warte auf Nachlieferung. Rekonstruiere nichts, rate nichts, erfinde keinen Ersatz.

Das Ergebnis der Startprüfung ist der **erste Absatz** deines ersten Berichts.

---

## 0b. Pflicht vor der ersten Codezeile: `IMPLEMENTATION_PLAN.md`

**Du schreibst keinen Produktionscode, bevor diese Datei existiert und vorgelegt wurde.** Gute
Lastenhefte verleiten dazu, sofort loszubauen — und dann steht die Struktur fest, bevor jemand sie
geprüft hat.

Die Datei enthält:

| Abschnitt | Inhalt |
|---|---|
| **Bestand** | Was liegt bereits im Repository? Was davon ist Prototyp, was Altstand, was brauchbar? |
| **Prototypen** | Was übernimmst du, was verwirfst du — **je mit Begründung**. Fremder Code wird nie still übernommen |
| **Zielstruktur** | Konkrete Verzeichnisse und Dateien nach Portal-Lastenheft §1.3, auf dein Vorhaben angewendet |
| **Modulgrenzen** | Was gehört in `helpers`, `data`, `services`, `views`? Wo verläuft die Grenze zwischen Kunden- und Adminzugriff? |
| **Datenmodellquelle** | Welche Tabellen aus §4, in welcher Reihenfolge migriert |
| **Reihenfolge** | Welcher lauffähige Zwischenstand entsteht wann |
| **Risiken** | Was kann schiefgehen, woran merkst du es |
| **Testplan** | Welche Tests wann, wie die Datenbanktests laufen |
| **Offene Entscheidungen** | Was du **nicht** allein entscheidest |

**Danach baust du den kleinsten lauffähigen Stand** — Grundgerüst, eine Migration, eine Seite, ein
Test — und berichtest. Erst dann geht es weiter.

**Am Ende** lieferst du `IMPLEMENTATION_SUMMARY.md` (was gebaut wurde, Abweichungen vom Plan mit
Begründung, offene Punkte) und, falls aus einem Prototyp etwas übernommen wurde,
`MIGRATION_NOTES.md`.

---

## 0c. Zielarchitektur — ein PHP-Projekt

**SARTU ist eine Website mit geschütztem Kundenbereich, keine App.** Ein Repository, eine Domain,
ein Deployment:

```
/                     öffentliche SARTU-Website
/portal/              Kundenbereich (Login)
/admin/               interner Bereich (Login + Zweifaktor)
/api/                 eng begrenzte Serverfunktionen
```

Verbindlich: `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` **§1** — Stack, Verzeichnisstruktur,
Hosting-Anforderungen.

**PHP 8.3+, serverseitig gerendert, MySQL/MariaDB, PDO mit vorbereiteten Anweisungen.**
**Kein** WordPress · **kein** Laravel/Symfony · **kein** React/Vue/Next · **kein** Node oder Fastify
als Zielsystem · **kein** Supabase · **kein** Build-Schritt fürs Frontend · **keine** externen CDNs.

> **Zu älteren Ständen:** Frühere Fassungen nannten Node/Fastify/EJS oder einen Supabase-Prototyp.
> **Das ist keine Zielarchitektur mehr.** Vorhandene Prototypen dürfen als fachliche oder visuelle
> Referenz dienen — Ablauf, Felder, Texte. Ihr **Code** wird nicht übernommen. Was du daraus
> verwendest, steht begründet in `IMPLEMENTATION_PLAN.md`.

**Zur visuellen Ebene — das ist ausdrücklich kein „bau was Schönes":** Du wählst **1–3 sehr gute,
sauber lizenzierte Quellen** und **übernimmst deren konkreten Aufbau** — Markup, CSS-Ansatz,
Zustände, Interaktionslogik. Angepasst werden Farben, Schriften, Abstände, Texte. Utility-Klassen
werden dabei in **eigenes CSS mit zentralen Variablen** übersetzt, weil es keinen Build-Schritt gibt.
Vollständig in `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` §3.1 — **vor** dem ersten Entwurf lesen.

**Achtung Lizenz (§2.1 dort):** Was in SARTUs eigener Website zulässig ist, ist nicht automatisch im
späteren Kundenstarter zulässig. Bestimme je Quelle die Stufe, **bevor** du Code übernimmst, und
trage jedes Teil in die Herkunftsliste ein.

**Sprachregel nach außen:** Kundenbereich, Ihr Bereich, Anmeldung. **Nie** App, Software, SaaS,
Plattform, Dashboard, Control-Plane. Der Kunde soll denken „ich melde mich an und sehe mein Projekt",
nicht „ich muss ein Werkzeug lernen".

---

## 0e. Gate: Designentscheidung vor dem Vollausbau

Nach dem Design-Briefing entstehen **2–3 klickbare Startseitenvarianten mit echten Texten**. Dann
**anhalten**. Der Mensch entscheidet die Richtung. Erst danach werden weitere Seiten ausgebaut.

Wer nach dem Briefing durchbaut, hat das Gate verletzt — und im Zweifel Dutzende Seiten in einer
Richtung gebaut, die verworfen wird. Das Gate gilt auch dann, wenn der Mensch „komplett durchbauen"
gesagt hat: Es ist eine Entscheidung, die nur er treffen kann.

---

## 0d. Standort ist offen — und das ist kein Hindernis

`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1 steht auf `offen`. Solange das so ist:

**Gesperrt:** Ortsseiten · `LocalBusiness` in strukturierten Daten · Google-Unternehmensprofil ·
Ortsnamen in Title, H1, Meta, URL oder Fließtext · NAP-Aussagen · Service-Area.

**Nicht gesperrt:** alles andere. Strukturierte Daten nutzen `Organization` **ohne** Adressfeld.

**Kein Platzhalter wird durch einen erfundenen Wert ersetzt.** Findest du irgendwo einen konkreten
Ortsnamen in den Vorgaben: **melden**, nicht übernehmen.

---

## 1. Was du baust

Die **öffentlichen Seiten** der SARTU-Website. Der Kundenbereich (`/portal/`, `/admin/`) hat einen eigenen Auftrag (`CODEX_AUFTRAG_PORTAL.md`) — **aber dasselbe Repository und dasselbe Projekt** (§0c).

---

## 1a. Verhältnis zum Kundenbereich-Auftrag

**Ein Projekt, zwei Arbeitspakete.** Öffentliche Seiten und Kundenbereich teilen sich Repository,
Verzeichnisstruktur, Layouts, Partials, Komponenten, Hilfsfunktionen und Datenbank.

**Das Grundgerüst kommt aus dem anderen Arbeitspaket** (`/app/bootstrap.php`, Layouts,
Hilfsfunktionen, Datenbankschicht). Existiert es noch nicht, baust du es nach Portal-Lastenheft §1.3
— **in derselben Struktur**, damit es später zusammenpasst. Das gehört in deinen
`IMPLEMENTATION_PLAN.md`.

| Darfst du jederzeit | Erst wenn der Kundenbereich steht |
|---|---|
| Struktur, Seiten, Navigation, alle Texte | echte Screenshots des Kundenbereichs einsetzen |
| Bedarfsscheck vollständig, inklusive Fassung ohne JavaScript | **Livegang** |
| SEO-Grundlage, Sitemap, strukturierte Daten | |
| Designvarianten nach `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md` und die Entscheidung | |

**Die Sperre ist der Livegang, nicht der Bau.** Eine Website, die mit einem Kundenbereich wirbt und
dafür eine erfundene Oberfläche zeigt, macht eine Falschaussage über das eigene Produkt. Reservierte
Bildflächen mit korrekten Maßen ja — ein Bild, das eine Oberfläche behauptet, nein.

**Es gibt keine Schnittstelle über das Netz.** Der Bedarfsscheck ruft direkt den Anfragedienst im
selben Programm auf (Portal-Lastenheft §4b.1). Kein Token, kein gemeinsames Geheimnis.

---

## 2. Lesereihenfolge und Rangfolge

Lies in dieser Reihenfolge. Bei Widersprüchen gilt die **niedrigere Nummer**:

1. **`CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md`** — dein Hauptdokument: Struktur, fertige Texte, Feldlabels, Fehlermeldungen, Bildliste, SEO-Tabelle, Abnahmekriterien
2. **`CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`, Abschnitt „10a. Arbeitsverteilung Codex ↔ Claude Code"** — **verbindlich**, siehe Abschnitt 2a
3. **`CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`** — wie du die visuelle Ebene recherchierst, prüfst und vorlegst
4. **`CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md`** — §1 (Stack, Struktur, Hosting) und Abschnitt „4b. Anfrageeingang vom Bedarfsscheck". **Beide verbindlich**
5. **`CLAUDE_SARTU_WEBSITE_KONZEPT_FINAL.md`** — Begründungen und Architektur, wenn etwas unklar ist
6. **`CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`** im Übrigen — Geschäftsmodell, Preise, Recht. Nur nachschlagen
7. **`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md`** — welche Seite welche Suchintention bedient, in welcher Reihenfolge gebaut wird
8. **`SARTU_ENTSCHEIDUNGEN_OFFEN.md`** — alle Platzhalter und Sperren
9. `konzepte/` — historische Quelldateien. **Nur zum Nachschlagen.** Enthalten veraltete Preise und abgelöste Modelle
10. `design/_verworfen/` — **ignorieren.** Verworfene Entwürfe, keine Vorgabe, auch nicht als Anregung

> **Zu Abschnittsverweisen:** Paragraphenzeichen wie `§9.5b` sind eine Abkürzung, kein Beweis.
> Maßgeblich ist die **Überschrift**. Findest du einen Verweis nicht, suche nach dem Thema — und
> **melde** die Abweichung, statt zu raten.

---

## 2a. Wer in diesem Repository schreibt

Verbindlich nach `CLAUDE_SARTU_MASTERKONZEPT_FINAL.md`, Abschnitt „10a. Arbeitsverteilung Codex ↔ Claude Code":

> **Pro Repository schreibt genau ein Werkzeug final.**

**In diesem Repository schreibst du — Codex — final.** Claude Code liefert Entwurf, Review und Gegencheck (Copy, Struktur, SEO-Strategie), **schreibt hier aber keinen Produktionsstand**. Findest du Code, den du nicht geschrieben hast und der nicht aus den Vorgabedokumenten stammt: melden, nicht stillschweigend einbauen. Ein Wechsel der Federführung ist möglich, aber nur mit dokumentierter Entscheidung eines Menschen — kein stiller Wechsel mitten im Projekt.

---

## 3. Technischer Rahmen

Verbindlich ist Portal-Lastenheft **§1** (Stack, Struktur, Hosting) und Website-Lastenheft **§1**
(was die öffentlichen Seiten betrifft). Kurz:

- **PHP 8.3+, serverseitig gerendert.** Kein CMS, kein Vollframework, kein SPA, kein Build-Schritt
- **Öffentliche Seiten sind cachebar** — sie hängen an keiner Sitzung und dürfen als statische Antwort ausgeliefert werden
- **JS-Budget:** ≤ 75 KB gzip Startseite, ≤ 40 KB Unterseiten. Gemessen, nicht geschätzt
- **Bedarfsscheck ohne JavaScript vollwertig bedienbar** (Lastenheft §9.5a) — Grundfassung, keine Notlösung
- Alle Farben, Schriften und Abstände als **zentrale Variablen**
- **Wiederverwendung ist Pflicht:** Layouts, Partials und Komponenten aus `/app/views` werden von öffentlichen Seiten und Kundenbereich gemeinsam genutzt. Kein kopiertes Markup

### Was „keine externen Verbindungen" bedeutet

**Verboten — Fremdanbieter zur Laufzeit:** Schrift-, Skript- und Stil-CDNs · Analyse und Tracking ·
eingebettete Karten · Videoportale · Chat-Widgets · Werbe- und Rätselbild-Dienste · externe
Bildhoster. **Kein** Netzwerkaufruf des Browsers darf eine fremde Domain treffen — im
Netzwerkprotokoll geprüft.

**Formulare sind kein Sonderfall mehr:** Sie laufen im selben Programm. Kein Geheimnis, kein
Netzaufruf.

## 4. Ablauf in zwei Phasen — nicht durchbauen

### Phase 1 — Designvorschläge (erst hier stoppen)

Nach `CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`:

1. Recherchiere Komponenten, Schriften, Icons und echte Referenzseiten
2. Prüfe jeden Kandidaten gegen die Prüfliste (Lizenz, Pflege, Größe, Barrierefreiheit, Template-Erkennbarkeit)
3. Baue **2–3 klickbare Varianten der Startseite** mit den **echten Texten** aus dem Lastenheft
4. Lege sie vor — mit Herkunftsliste, Lizenzen, gemessenen KB und **Laborwerten**

**Zu den Messwerten in dieser Phase:** Echte Core Web Vitals sind **Felddaten** aus tatsächlichen Besuchen. Vor dem Livegang gibt es sie nicht. Liefere daher: Lighthouse-Ergebnis (Werkzeug und Version nennen), CSS- und JS-Größe in KB gzip, LCP-Element benannt und Laborzeit gemessen, CLS im Labor, Total Blocking Time als INP-Ersatz. Behaupte **keine** Feldwerte. Nachgemessen wird nach dem Livegang (Lastenheft §17a).

**Dann anhalten.** Der Mensch entscheidet die Richtung.

### Phase 2 — Rest bauen

Erst nach der Entscheidung: alle Seiten aus dem Lastenheft, im gewählten Stil.

**Zwei Pflichtdateien in dieser Phase — beide bevor die Langtexte entstehen:**

**1. `KEYWORD_VALIDATION.md`.** Je Launch-Adresse: Zielbegriff, Nebenbegriffe, Suchintention, welche
Ergebnistypen die ersten zehn Treffer dominieren, verwandte Fragen, und die Entscheidung
„Title/H1/URL bestätigt" oder ein Änderungsvorschlag mit Begründung. Aufbau:
`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §1.1.

> **Ohne Volumenwerkzeug:** Datei trotzdem anlegen, oben als „SERP- und Intent-Validierung ohne
> Volumendaten" kennzeichnen, Volumenspalte **leer** lassen. **Niemals** Zahlen schätzen, um die
> Tabelle zu füllen. Schon der Blick auf die Suchergebnisse zeigt, ob eine Seite eine Chance hat.

**2. `GEO_DISCOVERY_CHECKLIST.md`** abarbeiten (liegt im Repository). Besonders: `robots.txt` darf
`Googlebot`, `Bingbot` und `OAI-SearchBot` **nicht** sperren, die `GPTBot`-Entscheidung wird
dokumentiert statt nebenbei getroffen, und `sameAs` nimmt **nur echte, gepflegte Profile** auf.

**Herkunftserfassung** nach Lastenheft §9.5b und Portal-Lastenheft §4b.7 einbauen — beim **ersten**
Seitenaufruf in die Sitzung schreiben, nicht erst beim Absenden.

---

## 5. Was du schreiben musst (und was nicht)

**Fertig vorgegeben — wörtlich übernehmen:**
- Startseite: alle Sektionen, Überschriften, Fließtexte, Buttons, 8 FAQ-Antworten
- Header- und Footer-Struktur
- Bedarfsscheck: alle Feldlabels, Hilfetexte, Fehlermeldungen
- Kontaktformular
- Alle Preise, Erstjahreswerte, Zahlungspläne
- Title und Meta-Description je Seite

**Du schreibst selbst — aus den Vorgaben, dann zur Prüfung vorlegen:**
- Die 5 Leistungsseiten: H1 und „Kurz gesagt" stehen fest, die restlichen Abschnitte des Templates schreibst du
- 3 **Transparenzseiten** (Lastenheft §11a): Gliederung und die Pflichtangaben stehen fest, den Text schreibst du. **Alle Zahlen kommen aus dem Masterkonzept — keine erfundenen Marktdaten, keine Wettbewerberpreise**
- 2 **Vergleichsartikel im Ratgeber-Bereich** (Lastenheft §12): Gliederung und Kurzantwort stehen fest, den Text schreibst du (je 900–1.300 Wörter). **Keine** zusätzlichen klassischen Ratgeberartikel zum Launch
- 8 Lexikonbegriffe: Struktur steht fest, die Texte schreibst du (je 250–400 Wörter)

> **Die Transparenzseiten sind der wichtigste Teil dieses Auftrags.** Sie sind der Grund, warum SARTU
> überhaupt gefunden und zitiert wird (`SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §0 und §3.4). Preise
> stehen als **Text**, nie als Bild, und werden aus **einer** zentralen Stelle ausgegeben.

**Regeln für selbst geschriebene Texte:** keine erfundenen Zahlen, Studien oder Referenzen. Keine Aussagen über Rankings oder Ergebnisse. Sprachregeln und Verbotsliste aus Lastenheft §2 gelten unverändert.

### Wenn du einen Konflikt in der vorgegebenen Copy siehst

Die Texte sind **Vorgabe, nicht Vorschlag** — du formulierst sie nicht still um. Aber sie sind auch nicht unfehlbar. Fällt dir beim Bauen auf, dass eine H1, ein Title oder ein Einstiegsabsatz mit der Keyword-Struktur, der Seitenhierarchie oder der Nutzerführung kollidiert:

1. **Baue die vorgegebene Fassung** — sie geht in den Stand
2. **Melde den Konflikt** in der Offene-Punkte-Liste: welche Stelle, welcher Konflikt, welche Auswirkung
3. **Lege einen Gegenvorschlag** daneben, mit Begründung
4. Der Mensch entscheidet. Bis dahin bleibt die vorgegebene Fassung stehen

**Nie:** eine bessere Formulierung stillschweigend einsetzen. Auch dann nicht, wenn sie objektiv besser ist — die Texte sind aufeinander und auf das Preismodell abgestimmt, und eine einzelne Änderung kann eine Aussage an anderer Stelle unhaltbar machen.

---

## 5a. Formularversand

Bedarfsscheck und Anfrageliste liegen im **selben** Programm. Der Ablauf ist deshalb schlicht:

```
Browser  ──normales Formular-POST──▶  /briefing/absenden  ──▶  AnfrageService  ──▶  Tabelle `leads`
```

**Kein Token, kein gemeinsames Geheimnis, kein Netzaufruf.** Frühere Fassungen sahen einen
`INTAKE_TOKEN` vor — das war richtig, solange es zwei getrennte Anwendungen waren. In einem Projekt
wäre es ein Geheimnis, das nichts schützt, aber irgendwann versehentlich ausgeliefert wird.

**Was bleibt — alle inhaltlichen Schutzmaßnahmen:**
- **CSRF-Feld** bei jedem `POST`
- **Honigtopf** `hp_website` (unsichtbar, `aria-hidden="true"` und `tabindex="-1"`)
- **Zeitregel:** Absenden unter 3 Sekunden wird stillschweigend verworfen — Danke-Seite erscheint trotzdem
- **Rate-Limit:** 10 abgeschickte Bedarfsschecks je IP und Stunde
- **Serverseitige Prüfung** aller Felder
- **`submission_id`** entsteht beim **Start** des Bedarfsschecks und bleibt über alle Schritte gleich → Doppelklick, Neuladen und Zurück-Taste erzeugen keinen zweiten Datensatz
- Nach Erfolg **Weiterleitung** (`303`) auf die Danke-Seite, nie ein erneut absendbares Formular

**Wichtig:** Empfehlung und Ampelkennzeichen werden **serverseitig** berechnet, **nie** aus dem
abgeschickten Formular übernommen — sonst könnte jemand die Empfehlung von außen setzen.

**Kein** Rätselbild und **kein** Fremddienst zum Start. Beides wäre eine externe Verbindung mit
eigener Datenschutzfolge und kommt erst, wenn Spam messbar auftritt.

Vollständige Festlegung: Website-Lastenheft §9.5b und Portal-Lastenheft §4b.

## 6. Was du NICHT erfindest — hier Platzhalter setzen und melden

Alle Platzhalter tragen **eine** einheitliche, suchbare Markierung: `[[PLATZHALTER]]` bzw. `[[SCREENSHOT-FEHLT]]`. Keine freien Formulierungen wie „TODO" oder „Lorem ipsum".

| Fehlt | Regel |
|---|---|
| **Impressum, Datenschutz, AGB** | kommen von einer Kanzlei. Nicht selbst formulieren. Seiten anlegen, Inhalt als `[[PLATZHALTER]]`, **nicht** live schalten |
| **Echte Anschrift, Telefon, E-Mail** | offen, siehe `SARTU_ENTSCHEIDUNGEN_OFFEN.md`. Platzhalter setzen und in der README auflisten |
| **Screenshots des Kundenbereichs** | müssen aus **echter** Oberfläche stammen. Bildflächen mit korrekten Maßen anlegen, Markierung `[[SCREENSHOT-FEHLT]]`, im Übergabebericht melden. Ein leerer Bildplatz ist **keine** „Musteransicht" — die Kennzeichnung kommt erst mit dem echten Bild |
| **Foto für `/ueber-uns`** | echtes Foto, kein Platzhalter, der wie ein Foto wirkt |
| **Logo** | bis zur Entscheidung reine Wortmarke in der gewählten Schrift (gültige Lösung, kein Provisorium) |
| **Referenzen, Bewertungen, Kundenlogos** | existieren nicht. **Niemals** erfinden, auch nicht als Beispiel |

### Ortsseiten — nicht zum Launch, auch nicht vorbereitend indexierbar

- **Keine** Ortsseite in der produktiven Veröffentlichung, auch nicht unverlinkt
- Existieren Prototypen: nur in Staging, mit `noindex`, **nicht** in der Sitemap, **nicht** intern verlinkt, in `robots.txt` ausgeschlossen
- Veröffentlichung erst nach dem Gate in Masterkonzept §16a. Die Entscheidung trifft ein Mensch
- Grund: Massenhaft erzeugte Ortsseiten ohne eigenständigen Wert fallen unter Googles Richtlinien zu Doorway- und skaliertem Inhaltsmissbrauch. Bei einem Betreibermodell trifft die Folge später die Kunden

---

## 7. Abnahme

Die Definition of Done steht in **Lastenheft §17**. Sie gilt vollständig. Besonders:

- keine verbotenen Wörter (§2) auffindbar — prüfe per Volltextsuche
- jede Seite: Status 200, genau eine H1, eigener Title und Description, Canonical, Breadcrumb
- JS-Budget gemessen eingehalten
- `prefers-reduced-motion` getestet
- **Bedarfsscheck mit abgeschaltetem JavaScript vollständig durchlaufen** — getestet, nicht behauptet
- **Nur `/public` ist über den Webserver erreichbar** — `/app`, `/storage`, `.env` liefern 403 oder 404, praktisch geprüft
- **Kein Netzwerkaufruf an eine fremde Domain** — im Netzwerkprotokoll geprüft
- **`KEYWORD_VALIDATION.md`** und **`GEO_DISCOVERY_CHECKLIST.md`** liegen vor und sind ausgefüllt
- **Herkunftserfassung geprüft:** Testanfrage mit `?utm_source=test&utm_medium=audit` landet mit den Werten im Datensatz
- **Startsperre nachgewiesen (§14a):** Die produktive Veröffentlichung bricht bei einem Platzhalter in Impressum oder Datenschutz nachweislich ab — einmal absichtlich provoziert und belegt
- Kontrast ≥ 4,5:1, Tastaturbedienung vollständig, Fokus sichtbar
- Laborwerte im Zielbereich; echte Core Web Vitals sind Nachmessung (§17a), kein Abnahmekriterium
- keine erfundenen Inhalte

---

## 8. Was du am Ende ablieferst

1. Die Website im Repository, lauffähig, mit getrennten Betriebsarten (`APP_ENV=staging` warnt, `APP_ENV=production` bricht ab)
2. **`README.md`**: Stack, Struktur, Umgebungsvariablen, wie man baut und deployt, wie eine neue Seite angelegt wird
3. **Herkunftsliste**: jedes eingesetzte Fremdteil mit Name, Version, **Lizenz**, Fundstelle
4. **Messwerte**: JS/CSS in KB gzip, Laborwerte je Kernseite mit genanntem Werkzeug
5. **Offene-Punkte-Liste**: alle Platzhalter aus Abschnitt 6, alle selbst geschriebenen Texte, alle gemeldeten Copy-Konflikte aus Abschnitt 5
6. **Abnahmeliste** aus Lastenheft §17, Punkt für Punkt abgehakt

**Arbeite nicht ins Blaue:** Kollidiert eine Anforderung mit einer anderen oder fehlt eine Information, melde es, statt zu raten.
