# Belege und Zahlungsabgleich

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Angelegt am 09.08.2026 nach der Entscheidung in `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4a.
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

---

## Was hier steht — und was ausdrücklich nicht

**Hier steht der gesamte Weg vom Angebot bis zum abgeglichenen Zahlungseingang:** wie ein Beleg
entsteht, wie er aussieht, welches Format er trägt, wie er nummeriert, storniert, aufbewahrt und
an den Steuerberater übergeben wird, und wie der Zahlungsdienst dabei abgeglichen wird.

**Hier steht keine Buchhaltung.** Nicht gebaut werden: doppelte Buchführung, Kontenrahmen,
Umsatzsteuer-Voranmeldung, Einnahmen-Überschuss-Rechnung, Bilanz, Jahresabschluss, Mahnwesen.
Die Grenze ist Teil der Entscheidung (§4a) und keine Auslassung.

**Die Preise stehen nicht hier**, sondern in `02_PREISE_UND_ZAHLUNG.md`. Diese Datei nennt keinen
einzigen Betrag; sie beschreibt, was mit ihm geschieht.

---

## 1. Zwei Belegarten, zwei verschiedene Regelwerke

| | Angebot | Rechnung |
|---|---|---|
| Rechtliche Bindung | keine — ein Angebot ist keine Rechnung | § 14 UStG, EN 16931 |
| Format | frei gestaltbar | **ZUGFeRD**, siehe Abschnitt 3 |
| Nummernkreis | `AN-JJJJ-NNN` | `RE-JJJJ-NNN` |
| Änderbar nach Versand | nein, aber ohne Storno ersetzbar | **nein** — nur über eine Stornorechnung |
| Aufbewahrung | mit dem Projekt | **acht Jahre**, strukturierter Teil unversehrt |

> **Der Unterschied ist größer, als er aussieht.** Ein Angebot darf schön sein und sonst nichts.
> Eine Rechnung ist ein Beweismittel: Sie muss maschinenlesbar sein, sie muss unverändert
> bleiben, und sie muss auch dann noch abrufbar sein, wenn das Projekt längst archiviert ist.

---

## 2. Das Rechnungsdokument

*Aufgabe:* Aus den Daten in `invoices` und `operator_settings` ein Dokument erzeugen, das ein
Mensch lesen kann und eine Buchhaltungssoftware einlesen kann — **in einer Datei**.

*Grenze:* Das Dokument wird **erzeugt, nicht hochgeladen**. Es gibt keinen Weg, ein fremdes PDF
als Rechnung einzuhängen; sonst ist die Gleichheit von Anzeige und strukturiertem Teil nicht
mehr nachweisbar.

*Umfang:* Eine Seite bei bis zu vier Positionen, sonst mit Umbruch. Die Gestaltung folgt
`design/tokens.css` und trägt das Logo aus `07_MARKE_UND_GESTALTUNG.md`.

### Die Pflichtangaben nach § 14 Abs. 4 UStG

| | Woher der Wert kommt |
|---|---|
| Name und Anschrift des leistenden Unternehmers | `operator_settings` |
| Name und Anschrift des Leistungsempfängers | `organizations` |
| Steuernummer **oder** Umsatzsteuer-Identifikationsnummer | `operator_settings` — die Startsperre prüft, dass eine von beiden gesetzt ist |
| Ausstellungsdatum | `invoices.issued_at` |
| Fortlaufende Rechnungsnummer | Abschnitt 4 |
| Art und Umfang der Leistung | Meilenstein und Leistungstext aus dem zugehörigen Angebot |
| Zeitpunkt der Leistung | aus dem Projektverlauf; bei Anzahlungen der Zeitraum |
| Entgelt, aufgeschlüsselt nach Steuersätzen | `net_cents` |
| Steuersatz und Steuerbetrag | `vat_cents`; der Satz ist die Konstante aus `13_DATENMODELL.md` |

> **Die Liste muss nicht von Hand geprüft werden.** Das Profil `EN16931` erzwingt genau diese
> Felder; ein Beleg, dem eines fehlt, fällt bei der Prüfung aus Abschnitt 3 durch. **Die
> Validierung ist die Prüfung der Pflichtangaben** — nicht eine Prüfung daneben.

---

## 3. E-Rechnung — ZUGFeRD, und warum kein zweiter Weg

*Aufgabe:* Jede Rechnung entsteht als **PDF/A-3 mit eingebettetem XML nach EN 16931**. Ein
Dokument, zwei Lesarten: Der Mensch sieht das PDF, die Software liest das XML.

*Grenze:* **Kein zweites Format daneben.** Keine reine PDF-Rechnung, kein separater XML-Versand
als Regelfall — sonst gibt es zwei Wahrheiten, die auseinanderlaufen können.

*Umfang:* Profil **`EN16931`**. Die Profile `MINIMUM` und `BASIC-WL` sind ausgeschlossen — sie
sind keine vollwertigen Rechnungen im Sinne der Norm.

**Ein reiner XRechnung-Export als Nebenweg ist erlaubt**, wenn ein Kunde ausdrücklich XML ohne
PDF verlangt. Er entsteht aus demselben Datensatz und darf nie getrennt gepflegt werden.

### Die Fristen, an der Primärquelle geprüft am 09.08.2026

| Pflicht | Ab wann |
|---|---|
| Empfangen können | **01.01.2025**, alle |
| Versenden bei Vorjahresumsatz über 800.000 € | **01.01.2027** |
| Versenden, alle übrigen | **01.01.2028** |

**SARTU führt Regelbesteuerung** (§4a) und liegt im ersten Jahr unter 800.000 €. **Die Frist ist
damit der 31.12.2027.** Gebaut wird trotzdem sofort — der Weg wäre sonst zweimal zu bauen.

Bei Rechnungen bis **250 €** greift die Pflicht nicht (Kleinbetragsrechnung). SARTU stellt keine
Beträge in dieser Größe; die Ausnahme wird deshalb **nicht** programmiert.

### Prüfung vor dem Versand

*Aufgabe:* Der erzeugte Beleg wird gegen das Schema und die Schematron-Regeln der Norm geprüft,
**bevor** er versendet oder abgelegt wird.

*Grenze:* **Fällt die Prüfung durch, bricht der Versand ab.** Es gibt keinen Weg, eine ungültige
Rechnung zu senden — auch nicht mit Bestätigung. Die Fehlermeldung nennt die verletzte Regel.

---

## 4. Nummernkreise — automatisch und lückenlos

**Bis heute tippte der Admin die Nummer ein** (`13_DATENMODELL.md`, alte Fassung). Das fällt.

*Aufgabe:* Angebot `AN-JJJJ-NNN`, Rechnung `RE-JJJJ-NNN`, Storno `ST-JJJJ-NNN`. Je Art und Jahr
fortlaufend, beginnend bei `001`, ohne Lücke.

*Grenze:* Die Nummer entsteht **in einer Transaktion mit dem Beleg**, unter Zeilensperre auf dem
Zähler. Zwei gleichzeitige Vorgänge bekommen nie dieselbe Nummer und lassen nie eine aus.

*Umfang:* Eine Tabelle `number_sequences` mit Art, Jahr und letztem Wert. Die Eindeutigkeit
erzwingt zusätzlich ein `UNIQUE` auf der Belegnummer — der Zähler ist die Quelle, der Index die
Gegenprobe.

> **Warum keine Lücken erlaubt sind.** Eine Lücke im Nummernkreis ist bei einer Betriebsprüfung
> erklärungsbedürftig. Ein vergebener und dann verworfener Beleg ist deshalb **kein** Grund, die
> Nummer zu überspringen: Der Beleg entsteht erst mit der Nummer, und was entstanden ist, wird
> storniert und nicht gelöscht.

---

## 5. Storno und Rücknahme — drei verschiedene Vorgänge

**Sie werden heute vermischt.** `stornieren()` setzt einen Status, mehr nicht. Das genügt für
einen Entwurf und nicht für einen versendeten Beleg.

| Vorgang | Wann | Was geschieht |
|---|---|---|
| **Entwurf verwerfen** | Rechnung nie versendet | Status auf `verworfen`, Nummer bleibt vergeben und wird nie wieder benutzt |
| **Stornorechnung** | Rechnung versendet | **Neuer Beleg** mit eigener Nummer und negativen Beträgen, Verweis auf die stornierte Rechnung. Beide bleiben abrufbar |
| **Zahlung zurücknehmen** | Zahlung war eingetragen, war aber falsch | **Eigene protokollierte Handlung** mit eigenem Grundlagentext. Sie benachrichtigt den Kunden. **Kein stiller Statuswechsel** |

*Grenze für alle drei:* Grundlagentext ist Pflichtfeld, Mindestlänge wie in `12_ADMINBEREICH.md`.
Jeder Vorgang erzeugt ein Audit-Ereignis mit Akteur, Zeitpunkt, altem Wert, neuem Wert und Grund.

**Die Änderung von `due_date`** ist ebenfalls eine protokollierte Handlung mit Grundlagentext.
Sie erzeugt **keinen** neuen Beleg — das Fälligkeitsdatum steht zwar auf der Rechnung, ist aber
keine Pflichtangabe nach § 14.

---

## 6. Mollie — Anbindung, Abgleich, Idempotenz

**Die Nicht-bauen-Liste ist an dieser Stelle aufgehoben** (§4a). Sie gilt für alles Übrige aus
Stufe C unverändert weiter: kein Mahnwesen, keine Registraranbindung, keine Auswertungen.

### Einrichtung

*Aufgabe:* Der Betreiber hinterlegt den Mollie-Schlüssel im Adminbereich, nicht in der
Ersteinrichtung. Die Ersteinrichtung hat **acht** Schritte (`14_SICHERHEIT.md`) und bekommt
keinen neunten.

*Grenze:* Der Schlüssel wird mit `sodium_*` verschlüsselt abgelegt, wie das TOTP-Geheimnis. Er
erscheint **nie** im Klartext — nicht in einer Ansicht, nicht in einer Fehlermeldung, nicht im
Protokoll. Test- und Produktivschlüssel sind getrennte Felder; welcher gilt, entscheidet
`APP_ENV`, nicht ein Schalter in der Oberfläche.

### Der Ablauf

1. Beim Versand einer Rechnung wird bei Mollie eine Zahlung angelegt — Betrag, Währung,
   Rechnungsnummer als Referenz, Rückkehradresse, Webhook-Adresse
2. Die Zahlungsadresse wird bei der Rechnung gespeichert und dem Kunden im Portal angeboten
3. Mollie ruft die Webhook-Adresse auf und übergibt **nur eine Kennung**
4. Der Server ruft mit dieser Kennung den Zahlungsstatus **selbst** bei Mollie ab
5. Der abgerufene Status wird gegen Rechnung, Betrag und Währung geprüft
6. Erst danach ändert sich der Zustand in `invoices`, mit Audit-Ereignis

> **Schritt 3 und 4 sind der ganze Punkt.** Der Webhook ist ein Klingelzeichen, keine Aussage.
> Die Aussage holt der Server sich selbst, mit seinem eigenen Schlüssel. **Der Zahlungsstatus
> wird nie aus einer Rückkehr-URL abgeleitet** — diese Regel galt schon vor A2 und gilt
> unverändert weiter.

### Idempotenz

*Aufgabe:* Derselbe Webhook darf beliebig oft eintreffen und den Zustand genau einmal ändern.

*Grenze:* Jede eingegangene Benachrichtigung wird mit ihrer Kennung in `payment_events`
festgehalten, **bevor** verarbeitet wird. Eine bereits verarbeitete Kennung führt zu einer
Bestätigung ohne Wirkung — nicht zu einem Fehler. Mollie wiederholt sonst.

### Abweichungen

*Aufgabe:* Stimmt der abgerufene Betrag oder die Währung nicht mit der Rechnung überein, ändert
sich **nichts** am Zustand. Der Vorgang wird protokolliert und im Adminbereich sichtbar gemacht.

*Grenze:* Eine Teilzahlung ist **keine** Abweichung, sondern ein eigener Zustand
(`teilweise_bezahlt`, siehe `02_PREISE_UND_ZAHLUNG.md`). Eine Überzahlung ist eine Abweichung.

---

## 7. Aufbewahrung — was die GoBD vom Portal verlangt

**Mit der Entscheidung §4a ist das Portal ein Rechnungsschreibungssystem im Sinne der GoBD.**
Der Satz aus `11_KUNDENBEREICH.md`, der Archiv und Fristen ins Buchhaltungswerkzeug auslagerte,
gilt nicht mehr.

| Anforderung | Wie sie erfüllt wird |
|---|---|
| **Unveränderbarkeit** | `audit_events` verbietet `UPDATE` und `DELETE` per Trigger. Ein abgelegter Beleg wird nie überschrieben |
| **Vollständigkeit** | Lückenloser Nummernkreis, Abschnitt 4 |
| **Nachvollziehbarkeit** | Jede Änderung an Geld und Fristen mit Akteur, Zeitpunkt, altem und neuem Wert und Grundlagentext |
| **Verfügbarkeit** | Der Beleg ist über die gesamte Frist im Portal abrufbar, auch nach Projektende |

*Aufgabe:* Der strukturierte Teil jedes Belegs wird **unverändert in seiner ursprünglichen Form**
aufbewahrt, **acht Jahre**, gerechnet ab Ende des Jahres der Ausstellung.

*Grenze:* Die abgelegte Datei bekommt eine Prüfsumme. Weicht sie beim Abruf ab, ist das ein
Fehler und keine Warnung. Ein Beleg wird **nie** neu erzeugt, um ihn wieder lesbar zu machen —
eine zweite Erzeugung ist ein zweites Dokument.

*Umfang:* Ablage unter `/storage`, außerhalb von `/public`, Zugriff ausschließlich über die
Zugriffsschichten mit Mandantenprüfung.

### Verfahrensdokumentation

*Aufgabe:* Ein Dokument, das Inhalt, Aufbau, Ablauf und Ergebnisse des Verfahrens so beschreibt,
dass ein Prüfer es ohne Rückfragen nachvollziehen kann. Es gehört nicht in den Programmcode,
sondern neben ihn.

*Umfang:* Belegfluss vom Entstehen bis zur Ablage · beteiligte Systeme und Schnittstellen ·
Berechtigungen · Sicherungs- und Wiederherstellungsverfahren · Änderungsprotokoll des Programms.

*Grenze:* Sie wird mitgeführt, nicht einmalig geschrieben. **Auch Programmdokumentation und
Änderungsprotokolle unterliegen der Aufbewahrungsfrist.**

---

## 8. Übergabe an den Steuerberater

*Aufgabe:* Ein Export je Zeitraum, der jede Rechnung, jede Stornorechnung und jeden
Zahlungseingang genau einmal enthält.

*Grenze:* Der Export **bucht nicht**. Er ordnet keine Konten zu, er ermittelt keine
Voranmeldung, er trifft keine steuerliche Aussage. Er übergibt Daten und nichts sonst.

*Umfang:* Belegdatum · Belegnummer · Gegenkonto- oder Kundenkennung · Netto · Steuersatz ·
Steuerbetrag · Brutto · Zahlungsdatum · Verweis auf die abgelegte Belegdatei. Dazu die
Belegdateien selbst.

> **Warum kein DATEV-Format vorgeschrieben ist.** Welches Format der Steuerberater will, weiß
> nur er. Eine tabellarische Übergabe plus Belege nimmt jeder; ein falsch geratenes
> Spezialformat nimmt keiner. **Die Formatfrage wird mit dem Steuerberater geklärt, nicht
> vorweggenommen.**

---

## 9. Abruf im Portal und Versand per Mail

*Aufgabe:* Angebote und Rechnungen sind für den Kunden in seinem Bereich und für den Betreiber
im Adminbereich abrufbar — als Liste mit Datum, Nummer, Betrag und Zustand, dazu der Beleg
selbst.

*Grenze:* Der Kunde sieht ausschließlich Belege seiner Organisation. Die Prüfung ist doppelt:
Gibt es den Beleg, **und** gehört er zur Sitzungsorganisation? Sonst **404**, nicht 403.

*Umfang für den Versand:* Der Versand ist eine **Handlung des Betreibers**, kein Automatismus.
Ein Knopf je Beleg, der die Mail mit dem Beleg im Anhang verschickt und den Versand protokolliert.
Ein zweiter Versand ist erlaubt und wird ebenfalls protokolliert.

**Der Kunde bekommt keinen Systemcode zu sehen** — Zustände erscheinen in Klartext
(`11_KUNDENBEREICH.md`).

---

## 10. Was das Datenmodell dazubekommt

Die Tabellenbeschreibung steht in `13_DATENMODELL.md`; hier steht, **warum** es sie gibt.

| Tabelle | Wofür |
|---|---|
| `number_sequences` | der lückenlose Zähler je Belegart und Jahr |
| `documents` | jeder erzeugte Beleg mit Pfad, Prüfsumme, Format und Erzeugungszeitpunkt |
| `payment_events` | jede eingegangene Zahlungsbenachrichtigung, für die Idempotenz |

| Feld | An welcher Tabelle | Wofür |
|---|---|---|
| `issued_at` | `invoices` | Ausstellungsdatum als Pflichtangabe, getrennt von `created_at` |
| `cancels_invoice_id` | `invoices` | Verweis der Stornorechnung auf die stornierte Rechnung |
| `payment_provider_id` | `invoices` | die Kennung der Zahlung beim Dienst |
| `mollie_key_test`, `mollie_key_live` | `operator_settings` | verschlüsselt, nie im Klartext |

---

## 11. Was nicht gebaut wird — auch nicht vorbereitend

Doppelte Buchführung · Kontenrahmen · Umsatzsteuer-Voranmeldung über ELSTER · Bilanz und
Jahresabschluss · Mahnwesen und Mahngebühren · wiederkehrende Lastschriften und Mandate ·
automatische Verrechnung von Gutschriften · Fremdwährungen · Skonto.

> **Der Rundum-Schutz ist ein Abonnement und braucht trotzdem kein Mandat.** Er wird wie jede
> andere Leistung in Rechnung gestellt. Ein Lastschriftmandat wäre ein eigener Vorgang mit
> eigenen Pflichten und steht auf dieser Liste.
