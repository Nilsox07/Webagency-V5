# Verfahrensdokumentation

**Verfahren:** Rechnungsschreibung, Belegerzeugung und Belegablage im SARTU-Kundenbereich
**Betreiber:** die Betreiberdaten stehen in `operator_settings`; sie werden in der
Ersteinrichtung erfasst und im Adminbereich gepflegt. In dieser Datei steht **keine**
Anschrift — sie wäre eine zweite, alternde Kopie
**Stand:** 09.08.2026 · **Fassung:** 1
**Rechtsgrundlage:** GoBD (BMF-Schreiben, 2. Änderung vom 14.07.2025), Rz. 151 ff. ·
§ 14 UStG · § 14b UStG · EN 16931

---

## Warum es dieses Dokument gibt

`spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 7: *„Ein Dokument, das Inhalt, Aufbau,
Ablauf und Ergebnisse des Verfahrens so beschreibt, dass ein Prüfer es ohne Rückfragen
nachvollziehen kann. Es gehört nicht in den Programmcode, sondern neben ihn."*

Mit der Entscheidung §4a in `SARTU_ENTSCHEIDUNGEN_OFFEN.md` ist der Kundenbereich ein
**Rechnungsschreibungssystem im Sinne der GoBD**. Damit gilt die Dokumentationspflicht —
und sie gilt **fortlaufend**: Auch Programmdokumentation und Änderungsprotokolle
unterliegen der Aufbewahrungsfrist.

> **Sie wird mitgeführt, nicht einmal geschrieben.** Wer den Ablauf ändert, ändert diesen
> Text im selben Commit. Ein Abschnitt, der beschreibt, was das Programm vor drei Monaten
> tat, ist schlimmer als keiner.

---

## 1. Überblick über das Verfahren

| | |
|---|---|
| **Was entsteht** | Ausgangsrechnungen und Stornorechnungen an Geschäftskunden |
| **Menge** | einstellig bis niedrig zweistellig je Monat |
| **Form** | PDF/A-3 mit eingebettetem XML nach EN 16931, Profil `EN16931` |
| **Wer erzeugt** | ausschließlich das Programm, ausgelöst durch eine Handlung des Betreibers |
| **Wer darf** | ausschließlich Benutzer mit der Rolle `admin` und bestätigter Zweifaktor-Anmeldung |
| **Wo abgelegt** | `/storage/belege`, außerhalb des über den Webserver erreichbaren Verzeichnisses |
| **Wie lange** | acht Jahre ab Ende des Jahres der Ausstellung (§ 14b UStG) |

**Nicht Teil des Verfahrens:** doppelte Buchführung, Kontenrahmen, Umsatzsteuer-Voran­meldung,
Jahresabschluss, Mahnwesen. Diese Vorgänge finden außerhalb statt; das Programm übergibt
Daten (Abschnitt 6) und trifft keine steuerliche Aussage.

---

## 2. Der Belegfluss vom Entstehen bis zur Ablage

### 2.1 Die Rechnung entsteht als Entwurf

Ein Admin legt im internen Bereich zu einem Projekt eine Rechnung an: Meilenstein und
Nettobetrag. Die Umsatzsteuer rechnet das Programm mit dem Satz aus einer Konstante; steht in
den Betreiberdaten `kleinunternehmer`, wird keine ausgewiesen (§ 19 UStG).

**Die Belegnummer wird nicht eingegeben, sondern vergeben.** Sie entsteht aus
`number_sequences` — ein Zähler je Belegart und Jahr, unter Zeilensperre hochgezählt, in
**derselben** Datenbanktransaktion wie die Rechnungszeile. Die Form ist `RE-JJJJ-NNN` bzw.
`ST-JJJJ-NNN`.

Ein Entwurf ist **kein Beleg**. Er hat kein Ausstellungsdatum, es existiert keine Datei, und
er erscheint in keinem Export.

### 2.2 Der Versand macht daraus einen Beleg

Mit dem Versand geschieht Folgendes, in dieser Reihenfolge:

1. `issued_at` wird gesetzt — das Ausstellungsdatum nach § 14 Abs. 4 UStG
2. Der strukturierte Teil wird aus `invoices`, `operator_settings` und `organizations`
   aufgebaut
3. Er wird **gegen das Schema der Norm geprüft**
4. **Fällt die Prüfung durch, bricht der Vorgang hier ab.** Es entsteht keine Datei, keine
   Zeile in `documents`, kein Zustandswechsel und keine Mail
5. Die Sichtseite wird als HTML gerendert und nach PDF gewandelt
6. Das XML wird als `factur-x.xml` eingebettet, das Ergebnis ist PDF/A-3
7. Die Datei wird unter `/storage/belege` abgelegt, mit einem nicht ratbaren Dateinamen
8. In `documents` entsteht eine Zeile mit Pfad, **SHA-256-Prüfsumme**, Format und Zeitpunkt
9. Erst jetzt wechselt der Zustand der Rechnung auf `gesendet`
10. Der Kunde bekommt eine Mail, dass die Rechnung in seinem Bereich liegt

> **Schritt 4 steht vor Schritt 7 mit Absicht.** Eine ungültige Rechnung darf nicht einmal
> auf der Platte liegen, wo sie jemand später findet und für gültig hält.

**Das Dokument wird erzeugt, nicht hochgeladen.** Es gibt keinen Weg, ein fremdes PDF als
Rechnung einzuhängen — die entsprechende Funktion hat keinen Parameter dafür.

### 2.3 Der Beleg wird nie überschrieben

Eine zweite Erzeugung ist ein **zweites Dokument** mit einer eigenen Zeile in `documents` und
einer eigenen Datei. Es gibt kein `UPDATE` und kein `DELETE` auf dieser Tabelle — nicht als
Disziplin, sondern weil die Methoden nicht existieren.

Beim Abruf wird die Datei gegen ihre Prüfsumme geprüft. **Weicht sie ab, ist das ein Fehler
und keine Warnung:** Der Abruf bricht ab, es wird nichts ausgeliefert und nichts versendet.
Ein Beleg wird **nie** neu erzeugt, um ihn wieder lesbar zu machen.

### 2.4 Aufhebung — zwei Wege, und der Zustand entscheidet

| Zustand | Was geschieht |
|---|---|
| `entwurf` | **verworfen.** Die Nummer bleibt vergeben und wird nie wieder benutzt. Kein Beleg, keine Mail — er ist nie hinausgegangen |
| versendet | **Stornorechnung** mit eigener Nummer aus dem Kreis `ST`, negativen Beträgen und Verweis auf die aufgehobene Rechnung. Beide Belege bleiben abrufbar |

**Eine Lücke im Nummernkreis entsteht dabei nicht.** Ein vergebener und dann verworfener
Beleg ist kein Grund, die Nummer zu überspringen; sie steht mit dem Vorgang im
Änderungsprotokoll.

Gelöscht wird nichts. Fachliche Daten werden nie hart gelöscht.

---

## 3. Beteiligte Systeme und Schnittstellen

| System | Rolle | Was hinausgeht | Was hereinkommt |
|---|---|---|---|
| **Anwendung** (PHP 8.3, ein Server) | erzeugt, prüft, legt ab, liefert aus | — | — |
| **MariaDB** | Fach- und Protokolldaten | — | — |
| **Dateiablage** `/storage` | die Belegdateien | — | — |
| **SMTP-Server** | Versand an Kunde und Betreuer | Mailtext, auf Knopfdruck der Beleg im Anhang | — |
| **Zahlungsdienst (Mollie)** | Zahlungsweg | Betrag, Währung, Belegnummer als Referenz, Rückkehr- und Webhook-Adresse | eine Zahlungskennung, und auf Abruf der Zahlungszustand |

**Es gibt keine weitere Schnittstelle nach außen.** Kein CDN, keine Auswertungsdienste, keine
Registrar-Anbindung, keine Buchhaltungs-Schnittstelle.

### 3.1 Der Zahlungsabgleich im Einzelnen

Der wesentliche Punkt für einen Prüfer: **Der Zahlungszustand wird nie aus einer Rückkehr-URL
oder aus dem Inhalt einer eingehenden Benachrichtigung abgeleitet.**

1. Der Zahlungsdienst ruft eine Adresse unter `/api/` auf und übergibt **nur eine Kennung**
2. Die Benachrichtigung wird mit dieser Kennung in `payment_events` festgehalten — **bevor**
   irgendetwas verarbeitet wird. War die Kennung schon da, endet der Vorgang mit einer
   Bestätigung ohne Wirkung
3. Der Server ruft mit der Kennung den Zustand **selbst** beim Dienst ab, mit seinem eigenen
   Schlüssel
4. Die Rechnung wird über die **gespeicherte** Zahlungskennung gefunden, nicht über eine
   Angabe aus der Nachricht
5. Betrag und Währung werden gegen die Rechnung geprüft. Stimmen sie nicht, ändert sich
   **nichts**; der Vorgang wird protokolliert und ist im Adminbereich sichtbar
6. Erst danach wird gebucht, mit einem Protokolleintrag ohne Akteur — es war kein Mensch

Ein Zahlungseingang lässt sich außerdem **von Hand** eintragen. Das ist der Regelweg bei
Überweisung und verlangt einen Grundlagentext von mindestens drei Zeichen, der als `reason`
im Protokoll steht.

---

## 4. Berechtigungen

| Wer | Darf |
|---|---|
| **Betreiber (Rolle `admin`)** | Rechnungen anlegen, senden, aufheben, Zahlungen eintragen und zurücknehmen, Fristen ändern, Belege abrufen und versenden, den Zeitraum exportieren |
| **Kunde (Rolle `kunde`)** | ausschließlich die Belege **seiner** Organisation ansehen und herunterladen |
| **Unangemeldet** | nichts davon |

**Zwei getrennte Zugriffsschichten.** Die Kundenschicht nimmt die Organisation ausschließlich
aus der Sitzung; ein fehlender Wert ist ein Fehler, nicht „alles anzeigen". Ein Admin hat
**keine** Organisation — die Datenbank erzwingt das — und kommt deshalb nicht durch die
Kundenschicht. Es gibt keinen gemeinsamen Codepfad, bei dem der Filter für eine der beiden
Rollen entfällt.

Der Zugriff auf einen fremden Beleg wird mit **404** beantwortet, nicht mit 403: 403 verriete
seine Existenz.

**Zugang zum internen Bereich** verlangt Passwort (Argon2id) **und** einen Zeitcode (TOTP).
Die zweite Stufe ist auch in der Entwicklung nicht abschaltbar.

> **`/storage/betrieb` ist keine Belegablage.** Seit dem 13.08.2026 liegt dort **eine** Datei:
> das Bild der Person hinter SARTU, das der Betreiber im internen Bereich hochlädt und das die
> öffentliche Website anzeigt. Es ist kein Beleg, unterliegt keiner Aufbewahrungsfrist und
> trägt keine Prüfsumme in `documents`.
>
> Es steht hier, weil ein Prüfer beim Blick in `/storage` ein zweites Verzeichnis neben
> `belege` findet und wissen muss, dass es nicht dazugehört. Ersetzt der Betreiber das Bild,
> wird die vorige Datei entfernt — das ist zulässig, weil die Regel „keine harte Löschung"
> fachliche Vorgänge schützt, nicht ersetzte Bilddateien.

---

## 5. Sicherung, Wiederherstellung und Unveränderbarkeit

### 5.1 Was gesichert wird

| Was | Warum |
|---|---|
| **Datenbank** | Rechnungen, Nummernkreise, Belegverweise mit Prüfsummen, Protokoll |
| **`/storage/belege`** | die Belegdateien selbst |
| **`.env`** | enthält `ENC_KEY`. **Ohne diesen Schlüssel sind verschlüsselte Felder unlesbar** — es gibt keinen zweiten Ablageort und keine Wiederherstellung |

Datenbank und Dateiablage gehören **zusammen** gesichert. Eine Datenbank ohne die Dateien
kennt Prüfsummen zu Dateien, die es nicht mehr gibt; Dateien ohne Datenbank sind PDFs ohne
Nachweis ihrer Unversehrtheit.

### 5.2 Wiederherstellung

Einspielen der Datenbanksicherung, Zurückspielen von `/storage`, danach
`php bin/migrate.php verify` — es vergleicht die Prüfsumme **jeder** Migrationsdatei mit dem
eingetragenen Wert und bricht bei jeder Abweichung ab.

Die Unversehrtheit der Belege lässt sich stichprobenweise am Abruf prüfen: Er vergleicht die
Datei gegen den Wert in `documents` und verweigert bei Abweichung die Ausgabe.

### 5.3 Unveränderbarkeit

| Anforderung | Wie sie erfüllt wird |
|---|---|
| **Unveränderbarkeit** | `audit_events` verbietet `UPDATE` und `DELETE` per Datenbank-Trigger. Ein abgelegter Beleg wird nie überschrieben; `documents` hat keine Änderungs- und keine Löschfunktion |
| **Vollständigkeit** | lückenloser Nummernkreis aus `number_sequences`, unter Zeilensperre vergeben |
| **Nachvollziehbarkeit** | jede Änderung an Geld und Fristen mit Akteur, Zeitpunkt, altem und neuem Wert und Grundlagentext |
| **Verfügbarkeit** | der Beleg ist über die gesamte Frist im Kundenbereich abrufbar, auch nach Projektende |
| **Zeitliche Ordnung** | alle Zeitstempel in UTC gespeichert, in Europe/Berlin angezeigt |

**Was protokolliert wird:** Anmeldung und fehlgeschlagene Anmeldung, jeder Zustandswechsel,
jede Zahlungsbuchung und -rücknahme, jede Friständerung, jede Aufhebung, jeder Belegversand,
jedes Hinterlegen und Entfernen des Zahlungsschlüssels, jede Rechteänderung, jede Löschung.

---

## 6. Übergabe an den Steuerberater

Ein Export je Zeitraum als Tabelle (CSV, Semikolon, UTF-8) mit den Angaben: Belegdatum ·
Belegnummer · Kundenkennung · Kunde · Netto · Steuersatz · Steuerbetrag · Brutto ·
Zahlungsdatum · Verweis auf die Belegdatei. Dazu die Belegdateien selbst.

**Maßgeblich ist das Ausstellungsdatum**, nicht der Tag der Anlage: Eine im Dezember angelegte
und im Januar versendete Rechnung gehört in den Januar. Entwürfe und verworfene Entwürfe haben
kein Ausstellungsdatum und erscheinen nicht.

**Jeder Vorgang steht genau einmal.** Die Abfrage führt über die Rechnungen, nicht über die
Dokumente — sonst erschiene eine zweimal erzeugte Rechnung doppelt. Eine Stornorechnung ist
eine eigene Zeile mit negativen Beträgen; der Zahlungseingang ist eine Spalte an seiner
Rechnung.

**Der Export bucht nicht.** Keine Kontenzuordnung, keine Summenzeile, keine Voranmeldung,
keine steuerliche Aussage. Welches Format der Steuerberater wünscht, wird mit ihm geklärt und
nicht vorweggenommen.

---

## 7. Änderungsprotokoll des Programms

Die vollständige Historie liegt in der Versionsverwaltung dieses Repositorys; jede Änderung
ist einem Commit mit Begründung zugeordnet. Für den Prüfer die Stationen des Verfahrens:

| Datum | Was sich am Verfahren geändert hat |
|---|---|
| 09.08.2026 | Belegnummern werden vergeben statt eingegeben (`number_sequences`) |
| 09.08.2026 | Rechnungsbelege entstehen als PDF/A-3 mit eingebettetem XML nach EN 16931 und werden vor dem Versand geprüft |
| 09.08.2026 | Eine versendete Rechnung wird nur noch über eine Stornorechnung aufgehoben; ein nie versendeter Entwurf wird verworfen |
| 09.08.2026 | Zahlungsabgleich über einen Webhook mit serverseitigem Abruf und Idempotenz über `payment_events` |
| 09.08.2026 | Belegabruf für Kunde und Betreiber, Belegversand per Mail, Übergabe an den Steuerberater |
| 13.08.2026 | **Am Belegfluss nichts.** Ergänzt wurde `/storage/betrieb` für ein Bild der öffentlichen Website — kein Beleg, keine Frist, keine Prüfsumme. Aufgeführt, damit das zweite Verzeichnis in `/storage` erklärt ist |

**Vor diesem Datum** gab es keine Belegerzeugung: Rechnungen bestanden aus Datenbankzeilen und
einer Anzeige im Kundenbereich, die Nummer wurde von Hand eingetragen, und der Zahlungsstatus
wurde ausschließlich von Hand gesetzt.

---

## 8. Was noch nicht am Echtsystem geprüft wurde

`OFFENE_PRUEFUNGEN.md` führt die Punkte einzeln. Für dieses Verfahren betrifft das:

- die Prüfung des strukturierten Teils gegen **Schematron** (nicht nur gegen das Schema)
- den Webhook gegen den **echten** Zahlungsdienst, der eine öffentlich erreichbare Adresse
  braucht
- die Formatfrage des Steuerberater-Exports

> **Diese Aufzählung gehört hierher und nicht in eine Fußnote.** Eine
> Verfahrensdokumentation, die Geprüftes und Ungeprüftes nicht unterscheidet, ist genau die
> Sorte Dokument, wegen der es die Anforderung gibt.
