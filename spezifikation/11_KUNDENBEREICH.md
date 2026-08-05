# Der Kundenbereich — `/portal/`

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` §5 bis §12,
> vervollständigt am 05.08.2026 um §4c, §7, §8, §10 und §11.
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

> Sicherheit und Mandantentrennung: `14_SICHERHEIT.md`. Tabellen und Formate:
> `13_DATENMODELL.md`. Sprache: `08_TEXTREGELN.md`. Anfrageeingang: `09_ANFRAGEEINGANG.md`.
> Preise und Zahlungsmodell: `02_PREISE_UND_ZAHLUNG.md`.

---

## Was hier gebunden ist — und warum mehr als auf der Website

Auf der Startseite schreibt der Texter-Skill fast alles (`10_WEBSITE_SARTU.md`). **Hier ist die
Grenze anders gezogen, und das hat einen Grund:**

> **Ein Satz, der eine Erklärung des Kunden entgegennimmt, ist Vertragsbestandteil.**
> Ein Satz, der eine Website bewirbt, ist es nicht.

| | |
|---|---|
| **`gebunden`** | Erklärungen, die der Kunde abgibt (Ankreuzfelder vor einer Beauftragung, Freigabe, Abnahme, Domainbestätigung) · Angebotstexte · Pflichthinweise · **jede** Schaltflächenbeschriftung · Statusnamen · Stationsnamen · Betreffzeilen · jede Zahl und Frist |
| **wird geschrieben** | Einleitungen · Leerzustände · Erklärtexte der Willkommensstrecke · Antworten der Portal-Hilfe · Fehlermeldungen · E-Mail-Fließtext |

**Warum Schaltflächen ausnahmslos gebunden sind:** Sie stehen an mehreren Stellen und in
E-Mails. Läuft die Beschriftung auseinander, sucht der Kunde einen Knopf, den es nicht gibt.

**Warum Betreffzeilen gebunden sind, Fließtext aber nicht:** Der Betreff ist die
Wiederfindemarke im Postfach. Wer ihn umformuliert, macht eine drei Monate alte Mail unauffindbar.

Was geschrieben wird, ist unten mit **Aufgabe**, **Grenze** und **Umfang** notiert — dieselbe
Notation wie in `10_WEBSITE_SARTU.md`. **Es steht kein Beispielsatz dabei**, aus demselben Grund
wie dort: ein fertiger Satz wird übernommen, nicht getroffen.

### Gekoppelte Werte — Zitate, keine zweite Quelle

Ein Vertragstext, der eine Frist nennt, muss die Zahl enthalten; ein Pflichthinweis ohne seinen
Betrag ist keiner. **Diese Zahlen stehen hier als Zitat.** Geändert werden sie in der Datei, der
sie gehören — **weicht eine Zahl hier von dort ab, gilt dort:**

| Wert | gehört zu |
|---|---|
| Zahlungsziel **10 Kalendertage** | `02_PREISE_UND_ZAHLUNG.md` |
| Lieferkorridore je Paket, Pausierfrist **14 Tage** | `03_KUNDENPRODUKT.md` |
| Domaingebühr **bis 30 € netto/Jahr**, Inhaberschaft | `04_DOMAIN_HOSTING_MAIL.md` |
| BFSG-Kriterien, Bußgeldrahmen | `06_RECHT.md` |
| Anmeldelink **15 Minuten**, einmalig | `14_SICHERHEIT.md` |
| Mindestlaufzeit, Erstjahreswert | `02_PREISE_UND_ZAHLUNG.md` |

---

## Navigation — feste Reihenfolge, `gebunden`

`Übersicht` · `Angebot` · `Aufgaben` · `Vorschau` · `Rechnungen` · `Domain` · `Inhalte` ·
`Vertrag` · `Hilfe`

- **Menüpunkte, für die es noch nichts gibt, werden angezeigt und erklärt, nicht ausgeblendet.**
  Ein verschwundener Menüpunkt sieht aus wie ein Fehler; ein erklärter sieht aus wie ein Plan
- Jede Seite: `<h1>` als Seitentitel, `<title>` als `{Seite} — SARTU-Portal`

**`/vertrag`** zeigt die Rechtstexte mit `audience = kunde` aus `legal_texts` — den
Auftragsverarbeitungsvertrag und die technischen und organisatorischen Maßnahmen. Dazu die
Schaltfläche `Zur Kenntnis genommen`, die Zeitpunkt, Name und IP speichert und ein Audit-Ereignis
erzeugt. **Keine Zustimmung, keine Sperre** — der Vertrag gilt durch den Hauptvertrag, die
Bestätigung ist ein Nachweis der Bereitstellung.

---

## Anmeldung ohne Passwort

1. `/login` — Eingabe der E-Mail-Adresse
2. System erzeugt Token, speichert **nur den Hash**, versendet `{BASE_URL}/login/{token}`
3. **Immer dieselbe Bestätigungsseite anzeigen**, unabhängig davon, ob die E-Mail existiert —
   **keine Kontoauskunft**
4. Klick → Token prüfen (gültig, nicht abgelaufen, nicht benutzt) → Session anlegen → Token als
   benutzt markieren
5. Erster Login → Willkommensstrecke. Sonst → Übersicht

Token-Regeln (32 Byte, 15 Minuten, einmalig): `14_SICHERHEIT.md`.

### Texte

**`/login`**
- H1 `gebunden`: `Anmelden` · Feldlabel `E-Mail-Adresse` · Knopf `Anmeldelink senden`
- *Aufgabe des Erklärtexts:* sagen, dass ein Link kommt **und dass kein Passwort nötig ist**
- *Grenze:* das fehlende Passwort nicht als Einschränkung darstellen. Kein Fachwort für das Verfahren
- *Umfang:* **ein Satz, höchstens 20 Wörter**

**Bestätigungsseite**
- H1 `gebunden`: `Prüfen Sie Ihr Postfach`
- *Aufgabe:* bestätigen, dass der Link unterwegs ist — **ohne zu verraten, ob die Adresse bekannt ist**
- *Grenze:* **niemals** „Ihr Konto", „wir haben Sie gefunden" oder eine andere Kontoauskunft. Der Satz muss für beide Fälle stimmen
- *Umfang:* **zwei Sätze**
- *Zahlen `gebunden`:* **15 Minuten**, **einmal** verwendbar
  > **Gekoppelt:** Diese Frist ist Kundentext, der die Regel aus `14_SICHERHEIT.md` wiedergibt.
  > Ändert sie sich dort, ändert sich dieser Satz mit.
- Zusätzlicher Hinweis — *Aufgabe:* auf den Spam-Ordner und die erneute Anforderung verweisen
- **Notweg, immer sichtbar** — Werte aus `operator_settings`

---

## Willkommensstrecke beim ersten Login

Erscheint **einmalig**, wenn `users.welcome_seen_at` leer ist. Überspringbar, jederzeit erneut
aufrufbar unter Hilfe. **Genau drei Bildschirme — nicht mehr, nicht weniger.** Nach dem letzten
Bildschirm oder bei Überspringen: `welcome_seen_at` setzen.

**Regeln:**

- Eigene Seiten mit eigener Adresse (`/willkommen/1`, `/2`, `/3`), Navigation per `POST` oder
  Link — **kein JavaScript nötig**
- **Ein Sachverhalt je Bildschirm**, mobil vollwertig, Knöpfe in Daumenreichweite
- Tastaturbedienung vollständig, Fokus sichtbar, `prefers-reduced-motion` beachtet
- **Kein Zwang:** Wer überspringt, kann alles trotzdem uneingeschränkt bedienen
- **Keine Videos, keine Fortschrittsabzeichen, keine Gamification**

### Bildschirm 1 — was dieser Ort ist

- **Knöpfe `gebunden`:** `Weiter` · Textlink `Überspringen`
- **H1 — Anrede `gebunden`:** persönlich mit `{Vorname}`
- *Aufgabe H1:* begrüßen und den Ort benennen
- *Aufgabe Text:* sagen, **was hier alles zusammenläuft** — und was dadurch wegfällt
- *Grenze:* **nie** `Portal` als Anrede an den Kunden, **nie** `App`, `Dashboard`, `System`
  (`08_TEXTREGELN.md`). Keine Funktion nennen, die es in Stufe 0 nicht gibt
- *Umfang:* **zwei Absätze, zusammen höchstens 60 Wörter**

### Bildschirm 2 — die Arbeitsteilung

Zwei Spalten, mobil untereinander. **Spaltenüberschriften und der Umfang der beiden Listen sind
gebunden, ihre Benennung nicht** — je sechs Zeilen, keine mehr, keine weniger.

- **Überschriften `gebunden`:** `Das machen Sie hier` · `Das müssen Sie nicht`
- *Aufgabe je Eintrag:* den Vorgang aus Kundensicht benennen, Verb voran
- *Grenze:* kein Fachwort. **Die rechte Spalte nennt Sorgen, keine Leistungen** — sie steht dort, um sie zu nehmen
- *Umfang:* **je höchstens sechs Wörter**

| # | Das machen Sie hier | Das müssen Sie nicht |
|---|---|---|
| 1 | Angebot einsehen und annehmen | Technik verstehen |
| 2 | Rechnungen bezahlen | Seiten selbst bauen |
| 3 | Betriebsfragen beantworten | Webtexte schreiben |
| 4 | Bilder und Unterlagen hochladen | Den nötigen Seitenumfang kennen |
| 5 | Vorschau ansehen und freigeben | Etwas installieren |
| 6 | später Öffnungszeiten ändern | sich um Updates und Sicherheit kümmern |

**Abschlusssatz unter den Spalten**
- *Aufgabe:* die Arbeitsteilung in einem Satz zusammenfassen
- *Grenze:* **die Leistungen nicht in eine Aufzählung auflösen** — die linke Spalte steht schon darüber
- *Umfang:* **höchstens 25 Wörter**
- *Verworfen, nicht wieder verwenden:* die Fassung mit „Struktur, Design, Technik und die
  Suchmaschinen-Grundlage übernehmen wir" — vier Glieder direkt unter einer Sechserliste

Knöpfe `gebunden`: `Weiter` · `Zurück`

### Bildschirm 3 — wie es weitergeht

Knopf `gebunden`: `Portal öffnen`

**Drei Sachverhalte, in dieser Reihenfolge:**

| | *Aufgabe* | *Grenze* |
|---|---|---|
| **Ein Schritt** | sagen, dass immer genau **ein** nächster Schritt oben steht und SARTU sich meldet | keine Frist versprechen, die nirgends steht |
| **Anmeldung ohne Passwort** | erklären, dass jedes Mal ein Link per E-Mail kommt und es **kein** Passwort gibt, das verloren gehen kann | — |
| **Hilfe** | auf `Hilfe` verweisen, schriftliche Antwort zusagen | Antwortzeit nur nennen, wenn sie eingehalten wird |

> **Der Hinweis zum passwortlosen Anmelden ist Pflicht und darf nicht gekürzt werden.** Kunden
> erwarten ein Passwort; ohne Erklärung entsteht der Eindruck, etwas sei kaputt oder unsicher.
> Er darf umformuliert, aber **nicht weggelassen** werden.

---

## Statuslogik

**Grundregel:** Intern gibt es technische Werte, dem Kunden wird **immer Klartext** gezeigt.
**Interne Codes erscheinen nie in der Kundenoberfläche.**

| Intern | Kundentext (`gebunden`) | Bedeutung |
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

### Zulässige Übergänge — der Server prüft gegen **diese** Tabelle

| Von | Nach | Auslösendes Ereignis | Wer | Was zwingend mitpassiert |
|---|---|---|---|---|
| *(Anlage)* | `angebot_offen` | Angebot gesendet | Admin | `offers.status = gesendet`, alle Pflichtfelder gefüllt |
| `angebot_offen` | `angebot_angenommen` | Angebot angenommen | **Kunde** | Ankreuzen + selbst getippter Name; `offers.status = angenommen` mit Zeitpunkt; Audit |
| `angebot_angenommen` | `zahlung_offen` | Anzahlungsrechnung gesendet | Admin | `invoices.status = gesendet`, `due_date` gesetzt |
| `zahlung_offen` | `briefing` | **Zahlungseingang bestätigt** | **Admin, von Hand** | `invoices.status = bezahlt` mit Datum. **Nie** aus der Rückkehr des Browsers abgeleitet. Audit mit `reason` als **Pflichtfeld** |
| `briefing` | `produktion` | **Faktenfreigabe erteilt** | **Kunde** | alle Pflichtaufgaben erledigt; `approvals` mit `kind = inhalte`; **ab hier läuft der Lieferkorridor** |
| `produktion` | `vorschau` | Vorschau veröffentlicht | Admin | `preview_url` und `preview_published_at` gesetzt; Korrekturrunde eröffnet |
| `vorschau` | `korrektur` | Rückmeldungen abgeschickt | **Kunde** | Runde wird geschlossen und **gegen `included_feedback_rounds` gezählt** |
| `korrektur` | `vorschau` | überarbeitete Vorschau bereit | Admin | neue `preview_published_at`; nächste Runde nur, wenn das Kontingent reicht |
| `vorschau` | `abnahme` | keine weiteren Änderungen | Admin | Kunde wird benachrichtigt, dass jetzt die Abnahme fehlt |
| `abnahme` | `launch_vorbereitung` | **Abnahme erklärt** | **Kunde** | `approvals` mit `kind = abnahme`; Schlussrechnung wird fällig |
| `abnahme` | `korrektur` | Rücksprung | Admin | `reason` **Pflichtfeld**; verbrauchte Runden bleiben verbraucht |
| `launch_vorbereitung` | `live` | Onlinegang | Admin | `launched_at`, `live_url`, `protection_started_on`, `protection_min_term_until`; Audit |
| *(jeder außer `live`)* | `pausiert` | Projekt angehalten | Admin | `reason` **Pflichtfeld — wird dem Kunden angezeigt**; Herkunftsstatus wird gespeichert |
| `pausiert` | *(Herkunftsstatus)* | Fortsetzung | Admin | zurück auf `paused_from_status`, **nicht** auf einen frei gewählten Wert |

**Ausdrücklich verboten:**

| Verboten | Grund |
|---|---|
| `zahlung_offen` überspringen | **Produktion beginnt nicht auf Zusage.** Der einzige Weg nach `briefing` führt über den bestätigten Eingang |
| `briefing → produktion` ohne `approvals`-Eintrag | Ohne protokollierte Freigabe fehlt der Nachweis, worauf gebaut wurde — und der Lieferkorridor hätte keinen Startpunkt |
| `abnahme → live` direkt | Der Onlinegang ist ein eigener Arbeitsschritt, keine Folge der Abnahme |
| `live → korrektur` oder zurück in die Produktionskette | Ein laufender Betrieb wird nicht in den Bauzustand zurückgesetzt. Änderungen an einer Live-Seite laufen über einen **neuen Vorgang** |
| Zielstatus aus der Anfrage übernehmen | Der Server prüft jeden Wechsel gegen diese Tabelle. **Ein nicht aufgeführtes Paar wird abgewiesen, nicht ausgeführt** |

**Für jeden Wechsel ohne Ausnahme:** serverseitige Prüfung gegen die Tabelle · Audit-Ereignis mit
`old_value`, `new_value` und handelndem Benutzer · bei Geld und Fristen zusätzlich `reason`
(`14_SICHERHEIT.md`).

> **Kundenausgelöste Wechsel sind genau drei:** Angebotsannahme, Faktenfreigabe, Abnahme. Alle
> drei sind **Erklärungen mit Namen und Zeitpunkt**. Alles andere setzt der Admin.
>
> Die frühere Formulierung „zulässige Übergänge setzt nur der Admin" **war falsch** und
> widersprach den Bildschirmen, auf denen der Kunde annimmt, freigibt und abnimmt.

### Untergeordnete Status

| Feld | Werte | Kundentexte (`gebunden`) |
|---|---|---|
| `offers.status` | `entwurf` (unsichtbar) → `gesendet` → `angenommen` \| `abgelaufen` \| `zurueckgezogen` | **Angebot liegt vor** · **Angenommen am {Datum}** · **Abgelaufen** · **Zurückgezogen** |
| `invoices.status` | `entwurf` (unsichtbar) → `gesendet` → `teilweise_bezahlt` → `bezahlt` \| `ueberfaellig` \| `storniert` | **Offen — zahlbar bis {Datum}** · **Teilweise bezahlt — offen: {Restbetrag}** · **Bezahlt am {Datum}** · **Überfällig seit {Datum}** · **Storniert** |
| `tasks.status` | `offen` → `erledigt` | **Offen** · **Erledigt** |
| `feedback_items.status` | `offen` → `beantwortet` → `erledigt` | **Eingereicht** · **Beantwortet** · **Umgesetzt** |

`ueberfaellig` wird **täglich automatisch** gesetzt, wenn `due_date < heute` **und**
`paid_cents < gross_cents`.

> **`teilweise_bezahlt` und `ueberfaellig` schließen sich nicht aus.** Eine angezahlte Rechnung
> nach Fälligkeit ist **beides**. Angezeigt wird dann
> `Überfällig seit {Datum} — offen: {Restbetrag}`. **Maßgeblich für die Erinnerung ist der
> Restbetrag, nicht der Status.**

### Der tägliche Lauf — vollständig

**Ein Lauf, sechs Aufgaben.** Er ist die einzige Stelle, an der ohne menschliches Zutun etwas
passiert — deshalb steht hier abschließend, **was** er tut.

| # | Bedingung | Was |
|---|---|---|
| 1 | `due_date < heute` **und** `paid_cents < gross_cents` | Rechnung auf `ueberfaellig` setzen |
| 2 | `due_date` überschritten, Restbetrag > 0, `reminder_sent_at` leer | **eine** Mail an den Kunden, `reminder_sent_at` setzen |
| 3 | **7 Tage** nach `reminder_sent_at`, Restbetrag weiterhin > 0, `reminder2_sent_at` leer | **zweite** Mail, zusätzlich Hinweis an den Admin, `reminder2_sent_at` setzen |
| 4 | Angebot mit `status = gesendet`, `valid_until` in **3 Tagen** | Mail an den Kunden. **Einmal je Angebot** |
| 5 | Aufbewahrungsfristen erreicht | Löschläufe für Anfragen, Uploads und `source_ip` — `09_ANFRAGEEINGANG.md` |
| 6 | Angebot mit `valid_until < heute` | `offers.status = abgelaufen` |

**Jeder Schritt ist wiederholsicher.** Ein zweiter Lauf am selben oder am Folgetag darf **nichts
doppelt** tun — deshalb die `*_sent_at`-Marken. Testfall 78 prüft genau das.

> **Kein Mahnwesen.** Zwei Erinnerungen, dann übernimmt der Mensch. Mahnstufen, Gebühren und
> Zinsen bleiben Stufe C.

> **Schritt 4 und 6 ergänzt am 05.08.2026.** Der Abgleich der 24 E-Mail-Auslöser gegen die
> auslösenden Ereignisse zeigte: Die Mail „Ihr Angebot gilt noch bis {Datum}" stand in der Liste,
> **aber kein Lauf und kein Übergang hätte sie je ausgelöst.** Dasselbe für den Ablauf selbst —
> `offers.status = abgelaufen` war ein Zustand ohne Weg dorthin. Ein Angebot wäre stillschweigend
> verfallen.

### Ableitung „Nächster Schritt"

Ist `next_step_text` gesetzt, wird **dieser** angezeigt. Sonst nach Projektstatus abgeleitet.
**Die Formulierungen werden geschrieben** — *Aufgabe:* die eine Handlung benennen, die jetzt
ansteht. *Grenze:* **ein** Verb, keine Begründung, kein „bitte". *Umfang:* **höchstens fünf Wörter**.

| Status | was der Schritt verlangt | Ziel |
|---|---|---|
| `angebot_offen` | Angebot ansehen und annehmen | `/angebot` |
| `angebot_angenommen`, `zahlung_offen` | Anzahlung bezahlen | `/rechnungen` |
| `briefing` | die **`{n}` offenen Aufgaben** bearbeiten | `/aufgaben` |
| `produktion`, `korrektur`, `launch_vorbereitung` | **nichts** — SARTU meldet sich | – |
| `vorschau` | Vorschau ansehen und zurückmelden | `/vorschau` |
| `abnahme` | Website abnehmen | `/vorschau` |
| `live` | **nichts, alles erledigt** | – |
| `pausiert` | Nachricht lesen | `/hilfe` |

### Korrekturrunden — Zählung und Grenze

Die enthaltenen Runden sind eine **harte Umfangsgrenze, keine Empfehlung.** Der Kundenbereich
muss sie sichtbar machen, sonst wird Feedback endlos.

1. Beim Bereitstellen einer Vorschau öffnet der Admin eine Runde: neuer Satz in `feedback_rounds`
   mit `status = offen`, `number` fortlaufend
2. Der Kunde sammelt beliebig viele Rückmeldungen **innerhalb** dieser Runde
3. Der Kunde reicht **gebündelt** ein → `status = eingereicht`, `submitted_at`. Danach sind in
   dieser Runde **keine** weiteren Einträge möglich
4. SARTU arbeitet ein → `status = bearbeitet`, neue Vorschau, nächste Runde

**Wenn alle enthaltenen Runden verbraucht sind** und der Admin eine weitere öffnet
(`included = false`), erscheint vor dem Einreichen ein Hinweis:

- **Überschrift `gebunden`:** `Diese Korrekturrunde ist im Festpreis nicht mehr enthalten.`
- *Aufgabe des Texts:* die genutzte Anzahl nennen, zusagen dass trotzdem geschaut wird — **und
  ausdrücklich, dass dem Kunden dadurch keine Kosten entstehen**
- *Grenze:* **nicht drohen und nicht zur Kasse bitten.** Der Hinweis macht sichtbar, er
  berechnet nicht
- *Umfang:* **zwei Sätze**

> **Der Kundenbereich blockiert nichts und berechnet nichts automatisch.** Er macht den Stand nur
> sichtbar. **Über zusätzlichen Aufwand entscheidet immer ein Mensch.**

Beim Versuch, in eine **eingereichte** Runde zu schreiben — *Aufgabe:* sagen, dass die Runde
eingereicht ist und was gerade passiert. *Grenze:* keine Schuldzuweisung; der Kunde hat nichts
falsch gemacht.

### Betriebsbeginn und Mindestlaufzeit

Der Betrieb (**Rundum-Schutz**) beginnt regulär mit dem **produktiven Betrieb der Website**.

- Beim Statuswechsel auf `live` setzt der Admin `protection_started_on` (Vorbelegung: heute), das
  System setzt `protection_min_term_until = protection_started_on + Mindestlaufzeit`
  (`02_PREISE_UND_ZAHLUNG.md`)
- Beides wird dem Kunden auf `/rechnungen` angezeigt:
  `Betrieb seit {Datum} · Mindestlaufzeit bis {Datum}`
- **Sonderfall:** Ist die Website abgenommen und betriebsfertig bereitgestellt und **nur der
  Kunde** verzögert den Onlinegang, kann der Admin `protection_started_on` manuell auf ein
  früheres Datum setzen. Der Adminbereich weist dabei hin:
  > Diese Regel muss vorher schriftlich angekündigt worden sein und mit der vertraglichen
  > Formulierung übereinstimmen.
- **Kündigungen, Verlängerungen und Lastschrift sind Stufe 2.** In Stufe 0 erzeugt der Admin die
  monatlichen Betriebsrechnungen manuell

**Jeder Statuswechsel erzeugt ein Audit-Ereignis** — `14_SICHERHEIT.md`.

---

## Die Seiten

### `/` — Übersicht

**H1 `gebunden`:** `Übersicht`

**Block 1 — Nächster Schritt**, hervorgehoben, ganz oben:

- Kleines Label `gebunden`: `Nächster Schritt`
- Große Zeile: der abgeleitete oder gesetzte Text
- Knopf zum Ziel, sofern vorhanden
- **Wenn nichts zu tun ist** — *Aufgabe:* sagen, dass nichts ansteht **und dass SARTU sich meldet**.
  *Grenze:* der Satz darf nicht wie ein Leerzustand wirken; er ist eine Entwarnung.
  *Umfang:* **ein Satz**

**Block 2 — Projektstand:** Projekttitel, Paketname im Klartext (`Start` / `Wachstum` /
`Platzhirsch` / `Sonderprojekt`), Kundentext des Status, Fortschritt über die Stationen.

**Stationen `gebunden`:** `Angebot` · `Zahlung` · `Angaben` · `Produktion` · `Vorschau` ·
`Abnahme` · `Online`. Die aktuelle Station ist markiert.

| Status | Station |
|---|---|
| `angebot_offen`, `angebot_angenommen` | **Angebot** |
| `zahlung_offen` | **Zahlung** |
| `briefing` | **Angaben** |
| `produktion`, `korrektur` | **Produktion** |
| `vorschau` | **Vorschau** |
| `abnahme`, `launch_vorbereitung` | **Abnahme** |
| `live` | **Online** |
| `pausiert` | **keine Station markiert.** Stattdessen über der Anzeige: `Pausiert — {pause_reason}` |

> **Ergänzt nach dem Audit.** Sieben Stationen für elf Status: Für `angebot_angenommen`,
> `korrektur`, `launch_vorbereitung` und `pausiert` war die Zuordnung nicht bestimmt. Ohne
> Festlegung hätte der Bau geraten — und die Anzeige hätte in vier Fällen etwas anderes gesagt
> als der Text darunter.
>
> **`korrektur` gehört zu Produktion, nicht zu Vorschau:** Aus Sicht des Kunden wird gearbeitet,
> nicht angesehen. **`launch_vorbereitung` gehört zu Abnahme, nicht zu Online** — online ist die
> Seite erst, wenn sie erreichbar ist.

**Block 3 — Offene Punkte:** höchstens drei Zeilen, jeweils mit Link — offene Aufgaben
(`{n} offene Aufgaben`), offene Rechnung (`Rechnung {Nummer} — zahlbar bis {Datum}`), ausstehende
Freigabe.

**Block 4 — Letzte Aktivität:** die letzten **fünf** für den Kunden relevanten Ereignisse mit
Datum, in Klartext (`Angebot angenommen`, `Zahlung eingegangen`, `Vorschau bereitgestellt`,
`Feedback eingereicht`, `Website online`).

---

### `/angebot`

**H1 `gebunden`:** `Ihr Angebot`

Zeigt **alle** Felder aus `offers` in dieser Reihenfolge:

1. Angebotsnummer · Gültig bis
2. Zusammenfassung des Ziels · empfohlene Lösung
3. Vorgesehene Seitenstruktur
4. Was enthalten ist · was **nicht** enthalten ist
5. **Umfangsgrenze:** `{scope_pages} Seiten, rund {scope_words} Wörter`, dazu `gebunden`:
   > `Umfang darüber hinaus bieten wir Ihnen vorher getrennt an.`
6. **Korrekturrunden:** `{included_feedback_rounds} enthaltene Korrekturrunden`, dazu `gebunden`:
   > `Eine Korrekturrunde bedeutet: Sie sammeln alle Anmerkungen und reichen sie gebündelt ein, wir arbeiten sie in einem Durchgang ein.`
7. **Zeitrahmen:** `Fertigstellung in {delivery_days_min}–{delivery_days_max} Werktagen` + der
   Text aus `delivery_start_condition`
8. Einmalpreis netto · Umsatzsteuer · Bruttobetrag
9. Monatlicher Betrieb netto · Mindestlaufzeit `{protection_min_term_months} Monate` ·
   Erstjahreswert netto
10. Zahlungsplan im Klartext
11. **Rechte und Export:** Text aus `rights_text`
12. **Domain und E-Mail:** Text aus `domain_text`

**Zahlungsplan-Texte — `gebunden`:**

| Plan | Text |
|---|---|
| `50_50` | `50 % bei Auftrag, 50 % nach Abnahme vor dem Onlinegang. Zahlungsziel jeweils 10 Kalendertage.` |
| `40_30_30` | `40 % bei Auftrag, 30 % nach der ersten Vorschau, 30 % nach Abnahme vor dem Onlinegang. Zahlungsziel jeweils 10 Kalendertage.` |
| `custom` | Inhalt von `payment_plan_custom` als Tabelle (Bezeichnung · Betrag · Fälligkeit), darunter `Zahlungsziel jeweils 10 Kalendertage.` |

**Unvollständiges Angebot:** Fehlt ein Pflichtfeld, ist der Annahmeblock **gesperrt**.
*Aufgabe des Hinweises:* sagen, dass das Angebot noch nicht fertig ist **und dass der Kunde
nichts tun muss**. *Grenze:* keine Schuldzuweisung, keine technische Ursache, keine Frist erfinden.

#### Annahmeblock

Nur bei `status = gesendet` **und** `valid_until >= heute`.

**Vier Pflicht-Bestätigungen — wörtlich `gebunden`, das sind Erklärungen des Kunden:**

1. `Die aufgeführten Ziele, Seitenbereiche und Funktionen entsprechen meinem Bedarf.`
2. `Nicht aufgeführte Sonderfunktionen wie Shop, Kundenlogin, Schnittstellen oder komplexe Buchung sind nicht beauftragt.`
3. `Neue Anforderungen werden vor Umsetzung getrennt angeboten.`
4. `Ich handle für mein Unternehmen und beauftrage SARTU kostenpflichtig zu den angezeigten Preisen, Laufzeiten und Zahlungsbedingungen.`

Feld `Ihr Name` (Pflicht, wird als Annahmenachweis gespeichert). **Direkt über dem Knopf
nochmals:** Einmalpreis netto · USt. · Brutto · Betrieb monatlich netto · Mindestlaufzeit ·
Erstjahreswert netto · Zahlungsplan.

Knopf `gebunden`: **`Kostenpflichtig beauftragen`**

**Nach Annahme:** `accepted_at`, `accepted_by_user_id`, `accepted_ip`, `accepted_name` speichern,
Audit-Ereignis, Projektstatus auf `angebot_angenommen`, Bestätigungs-E-Mail an Kunde **und**
Admin. **Zugleich ins Projekt übernommen:** `included_feedback_rounds`, `protection_level`,
`package`.

> Ab diesem Zeitpunkt ist das Angebot **schreibgeschützt — auch für den Admin.** Eine Änderung
> erfordert ein neues Angebot mit neuer Nummer.

Danach zeigt die Seite `Angenommen am {Datum} durch {Name}.` Der Annahmeblock verschwindet, **der
vollständige Angebotsinhalt bleibt dauerhaft einsehbar.**

**Abgelaufen** — *Aufgabe:* Ablaufdatum nennen und auf `Hilfe` verweisen. *Grenze:* nicht nach
Dringlichkeit klingen, keine neue Frist setzen.

---

### Feste Angebotstexte — `gebunden`, wörtlich zu übernehmen

Diese Texte stehen in **jedem** Angebot. Sie werden beim Anlegen vorbelegt, sind vom Admin
editierbar, **dürfen aber nicht leer bleiben. Formulierungen nicht erfinden.**

**`delivery_start_condition`**

> Der genannte Zeitraum beginnt, sobald alle Aufgaben in Ihrem Portal erledigt sind: bestätigte
> Fakten, vollständige Inhalte, freigegebene Rechtstexte und geklärte Bild- und Nutzungsrechte.
> Bis dahin läuft die Zeit nicht. Fehlt Ihre Mitwirkung länger als 14 Tage, dürfen wir das Projekt
> nach vorheriger Ankündigung pausieren; bereits abgeschlossene Meilensteine bleiben fällig.

Die Vorbelegung von `delivery_days_min` / `delivery_days_max` je Paket kommt aus den
**Lieferkorridoren in `03_KUNDENPRODUKT.md`** — sie stehen dort und werden hier **nicht**
wiederholt. Sonderprojekt: manuell.

> Die 14-Tage-Frist im Text oben ist dieselbe wie die Pausierregel in `03_KUNDENPRODUKT.md`.
> **Gekoppelt:** Ändert sie sich dort, ändert sich dieser Vertragstext mit.

**`rights_text`**

> Nach vollständiger Zahlung erhalten Sie die Nutzungsrechte am gelieferten Website-Stand, an den
> von uns erstellten Texten und am für Sie gestalteten Erscheinungsbild. Ihre Domain gehört Ihnen,
> auf Ihren Namen registriert. Auf Wunsch stellen wir Ihnen den vollständigen Stand Ihrer Website
> als Export bereit, mit einer Anleitung, wie er ohne uns weiterbetrieben werden kann.
> Nicht übertragen werden allgemeine Bausteine, die wir projektübergreifend einsetzen, sowie
> Rechte Dritter (z. B. Schriften oder Bilder), für die die jeweilige Lizenz gilt.

**`domain_text`**

> Ihre Domain wird auf **Ihren Namen** registriert — Sie sind Inhaber, nicht wir. Wir übernehmen
> Prüfung, Registrierung, Einrichtung und Verbindung. Die Domaingebühr ist in der
> Betriebspauschale enthalten, solange der Vertrag läuft. Endet der Vertrag, übertragen wir die
> Domain kostenfrei an Sie oder an einen Anbieter Ihrer Wahl; ab dann tragen Sie die Gebühr
> selbst. E-Mail-Postfächer sind nicht enthalten. Auf Wunsch richten wir die nötigen Einträge
> ein, damit ein Postfach Ihres Anbieters unter Ihrer Domain funktioniert.

> **Diese Texte sind Geschäftsaussagen, keine Rechtstexte.** AGB, Widerruf, Datenschutz und
> Auftragsverarbeitung stehen **nicht** hier und werden **nicht** erfunden — `06_RECHT.md`.

#### Barrierefreiheit — Pflichtzeilen im Angebot

**Jedes Angebot beantwortet die Frage, bevor sie gestellt wird.** Wann das
Barrierefreiheitsstärkungsgesetz greift und wen es ausnimmt, steht in `06_RECHT.md` — hier steht
nur, **was daraus im Angebot landet**.

**Baustein 1 — in jedem Angebot unter „was enthalten ist":**

> `Technische Grundlagen der Bedienbarkeit sind enthalten: ausreichender Kontrast, vollständige
> Bedienung per Tastatur, sichtbare Fokusmarkierung, beschriftete Formularfelder und semantisches
> HTML. Ihre Website ist damit auch für Menschen mit Einschränkungen benutzbar.`

**Baustein 2 — in `exclusions`, solange die Seite keinen Vertrag schließt:**

> `Eine Prüfung und Bestätigung der Konformität nach dem Barrierefreiheitsstärkungsgesetz ist
> nicht Gegenstand dieses Angebots. Nach unserer Einschätzung ist Ihre Website davon nicht
> erfasst, weil Besucher darüber keinen Vertrag abschließen. Ändert sich das, sprechen Sie uns
> bitte an.`

**Beide Zeilen dürfen nicht fehlen und nicht umformuliert werden.** Die erste ist ein
Verkaufsargument, die zweite eine Grenze der Leistung. **Wer die zweite weglässt, verkauft
stillschweigend etwas mit, das nicht geliefert wird.**

**Pflichtprüfung, sobald `sitemap` oder `inclusions` einen Buchungs-, Bestell- oder
Abonnementweg nennen** — zwei Pflichtfragen beim Anlegen des Angebots:

| Frage | Feld |
|---|---|
| `Schließen Besucher über die Seite einen Vertrag ab — Buchung, Bestellung oder Abonnement?` | `bfsg_vertragsabschluss` (ja/nein) |
| `Hat der Betrieb weniger als 10 Beschäftigte und höchstens 2 Mio. € Umsatz oder Bilanzsumme?` | `bfsg_kleinstunternehmen` (ja/nein/unbekannt) |

**Beide Antworten werden im Angebot mitgespeichert** und dort sichtbar wiedergegeben — es sind
**Angaben des Kunden, keine Feststellung von SARTU.**

| Antworten | Folge |
|---|---|
| Vertragsabschluss `nein` | Baustein 2 wie oben |
| Vertragsabschluss `ja`, Kleinstunternehmen `ja` | Baustein 2, ergänzt um `nach Ihrer Angabe als Kleinstunternehmen ausgenommen` |
| Vertragsabschluss `ja`, Kleinstunternehmen `nein` oder `unbekannt` | **Angebot lässt sich nicht senden.** Hinweis: `Hier greift das Barrierefreiheitsstärkungsgesetz. Bitte als eigenen Festpreisposten anbieten oder das Vorhaben ablehnen.` |

> **Warum die Sperre und keine Warnung:** Es geht um den Bußgeldrahmen aus `06_RECHT.md` und um
> eine Zusage, die SARTU sonst stillschweigend mitverkauft. **Eine Warnung wird weggeklickt.**

> **Gekoppelt:** Die beiden Fragen oben geben die Kriterien aus `06_RECHT.md` im Wortlaut wieder,
> weil sie dem Admin gestellt werden. Ändert sich die Rechtslage dort, ändern sich diese Fragen mit.

---

### `/aufgaben`

**H1 `gebunden`:** `Ihre Aufgaben`

**Einleitung**, nur solange offene Aufgaben existieren
- *Aufgabe:* drei Dinge sagen — SARTU hat vorausgefüllt, der Kunde bestätigt oder korrigiert, und
  **er muss nicht alles auf einmal machen**
- *Grenze:* keine Dauer versprechen; nicht entschuldigend klingen
- *Umfang:* **höchstens 40 Wörter**

Liste, sortiert nach `sort_order`: Titel · Status · Kurzbeschreibung. **Erledigte Aufgaben
rutschen nach unten und werden ruhiger dargestellt.**

**Aufgabendetail `/aufgaben/{id}`:** Titel · Beschreibung · Zeile `Warum wir das brauchen:
{why_needed}` (nur wenn gefüllt) · je nach `kind`:

| `kind` | Bedienung |
|---|---|
| `bestaetigung` | Anzeige der Angaben, Knöpfe `Stimmt so` und `Korrigieren` (öffnet Textfeld) |
| `angabe` | Textfeld `Ihre Antwort` (Pflicht) |
| `upload` | Dateiauswahl + Pflicht-Ankreuzfeld `Ich habe die Rechte an diesen Dateien und darf sie für meine Website verwenden.` |
| `freigabe` | siehe unten |

Knöpfe `gebunden`: `Aufgabe abschließen` · sekundär `Später`

#### Die Faktenfreigabe — `kind = freigabe`

Keine gewöhnliche Rückmeldung, sondern eine **protokollierte Erklärung** (`approvals`).

- **Überschrift `gebunden`:** `Fakten und Umfang final freigeben`
- *Aufgabe des Einleitungstexts:* zum letzten Prüfen auffordern **und** sagen, was danach beginnt
  und was danach nicht mehr ohne Weiteres geht
- *Grenze:* **nicht drohen.** Der Satz beschreibt eine Folge, keine Strafe. Keine Frist erfinden
- *Umfang:* **zwei Sätze**

**Darüber:** alle abgeschlossenen Aufgaben mit ihren Antworten in Kurzform, damit der Kunde
sieht, was er freigibt. Dazu der Umfangssatz aus dem Angebot:
`Vereinbarter Umfang: {scope_pages} Seiten, {included_feedback_rounds} Korrekturrunden.`

**Erklärung des Kunden — `gebunden`:**
> `Die Angaben sind vollständig und richtig. Der Umfang ist so vereinbart.`

Feld `Ihr Name` · Knopf `gebunden`: `Verbindlich freigeben`

**Nach dem Absenden:** Eintrag in `approvals` mit `kind = inhalte`, Audit-Ereignis, Anzeige
`Freigegeben am {Datum} durch {Name}.` **Der Lieferkorridor beginnt an diesem Tag** — der
Startzeitpunkt wird angezeigt: `Fertigstellung voraussichtlich in {min}–{max} Werktagen.`

**Sperre:** Die Freigabeaufgabe ist erst abschließbar, wenn **alle** Pflichtaufgaben mit
`required = true` erledigt sind. Sonst **Hinweis statt Knopf**, mit Verweis auf die Liste.

---

### `/vorschau`

**H1 `gebunden`:** `Vorschau und Freigabe`

**Wenn eine Vorschau vorliegt:**

- *Aufgabe des Texts:* zum Ansehen einladen und zum **Sammeln** der Rückmeldungen — mit dem Grund,
  warum gebündelt besser ist
- *Grenze:* nicht bitten, sondern begründen. Keine Zahl an Rückmeldungen nennen
- *Umfang:* **höchstens 35 Wörter**
- Knopf `gebunden`: `Vorschau öffnen` (neues Fenster, `rel="noopener"`)
- **Pflichthinweis** — *Aufgabe:* klarstellen, dass die Vorschau **nicht öffentlich** und für
  Suchmaschinen gesperrt ist. Der Hinweis darf nicht fehlen

**Rundenanzeige**, sobald eine Runde offen ist, direkt über dem Feedbackblock:
`Korrekturrunde {number} von {included_feedback_rounds}` — bei `included = false` stattdessen der
Hinweistext zur zusätzlichen Runde.

**Feedbackblock**, nur bei `status = offen` der aktuellen Runde: Textfeld `Ihre Rückmeldung` ·
optionales Feld `Betrifft welche Seite?` · Knopf `Rückmeldung senden`. Darunter die bisherigen
Rückmeldungen der laufenden Runde mit Status und Antwort, **ältere Runden zusammengeklappt**.

**Einreichen:** Knopf `Rückmeldungen abschließen und einreichen`, **davor ein
Bestätigungsschritt**.

- *Aufgabe:* sagen, dass in dieser Runde danach nichts mehr ergänzt werden kann, und was SARTU
  dann tut
- *Grenze:* als Frage enden, nicht als Warnung
- *Umfang:* **höchstens 30 Wörter**
- Knöpfe `gebunden`: `Ja, einreichen` · `Noch nicht`

Nach dem Einreichen: `status = eingereicht`, `submitted_at`, E-Mail an SARTU, Anzeige
`Eingereicht am {Datum}.` **Der Knopf ist gesperrt, solange die Runde keine einzige Rückmeldung
enthält.**

**Abnahmeblock**, nur bei Status `abnahme`:

- **Überschrift `gebunden`:** `Website abnehmen`
- *Aufgabe des Texts:* sagen, was die Abnahme bestätigt und was danach passiert
  (Schlussrechnung, Startvorbereitung)
- *Umfang:* **zwei Sätze**
- **Erklärung des Kunden — `gebunden`:** `Die Website entspricht dem vereinbarten Umfang.`
- Feld `Ihr Name` · Knopf `gebunden`: `Website abnehmen`

Nach Abnahme: Eintrag in `approvals`, Audit-Ereignis, Projektstatus `launch_vorbereitung`, E-Mail
an Kunde und Admin, Anzeige `Abgenommen am {Datum} durch {Name}.`

---

### `/rechnungen`

**H1 `gebunden`:** `Rechnungen`

Tabelle: Nummer · Beschreibung (Meilenstein im Klartext: `Anzahlung` / `Zwischenrate` /
`Schlussrechnung` / `Betrieb`) · Betrag brutto · Fällig am · Status · Aktion.

Aktion bei Status `gesendet` oder `ueberfaellig`: Knopf `gebunden` **`Jetzt bezahlen`** → öffnet
`mollie_payment_url` in neuem Fenster. **Direkt darunter** — *Aufgabe:* erklären, dass der Status
verzögert nachzieht **und dass der Kunde nichts weiter tun muss**. *Grenze:* keine Dauer nennen.

> **Der Status wird niemals aus der Rückkehr vom Zahlungsdienst abgeleitet.** Er wird
> ausschließlich im Adminbereich gesetzt, nachdem der Zahlungseingang geprüft wurde. **Das gilt
> schon vor Stufe A2.**

**Fußzeile `gebunden`:**
> `Alle Beträge netto zzgl. gesetzlicher Umsatzsteuer, sofern nicht anders angegeben. Zahlungsziel 10 Kalendertage.`

---

### `/domain`

**H1 `gebunden`:** `Domain und E-Mail`

Anzeige: Wunschname (falls erfasst) · bestätigter Name · Status im Klartext · Hinweis zur E-Mail.

**Statustexte `gebunden`:** `Noch offen` · `Vorschläge liegen bereit` · `Bestätigt` ·
`Registriert` · `Mit der Website verbunden` · `Online`

**Bestätigungsblock**, nur bei `vorschlaege_bereit`: höchstens **drei** Vorschläge zur Auswahl ·
Ankreuzfeld `gebunden` `Die Inhaberdaten oben sind korrekt.` · Knopf `gebunden`
`Domain verbindlich bestätigen`

**Pflichthinweis direkt darüber — `gebunden`:**
> SARTU registriert die Domain über den technischen Registrar **in Ihrem Namen**. Sie bleiben
> Inhaber. Nach erfolgreicher Registrierung ist eine Stornierung in der Regel nicht möglich. Eine
> normale Domain bis 30 € netto pro Jahr ist bei Verwaltung durch SARTU im Betrieb enthalten.

**E-Mail-Hinweis, immer sichtbar** — *Aufgabe:* zusichern, dass bestehende Adressen erreichbar
bleiben **und** dass Einträge vor jeder Änderung gesichert werden. *Grenze:* keine Technik
erklären; **beide Zusagen müssen vorkommen**.

---

### `/inhalte` — Öffnungszeiten, die eine Pflegefunktion

**H1 `gebunden`:** `Öffnungszeiten`

**Einleitung** — *Aufgabe:* sagen, dass Änderungen **nach Prüfung** erscheinen und wann.
*Zahl `gebunden`:* üblicherweise **am nächsten Werktag**. *Umfang:* **ein Satz**

Formular je Wochentag (Montag–Sonntag): Ankreuzfeld `Geschlossen` · Felder `Von` und `Bis` ·
optionales Feld `Hinweis`. Darunter **Ausnahmen**: Liste mit Datum, `Geschlossen`-Schalter oder
Zeiten, Bezeichnung (`Feiertag`, `Betriebsurlaub`). Knöpfe `Ausnahme hinzufügen`, je Zeile
`Entfernen`.

Knopf `gebunden`: `Änderungen einreichen`

**Nach dem Absenden:** `pending_publish = true`, dazu eine Bestätigung — *Aufgabe:* danken,
Prüfung ankündigen, Rückmeldung bei Veröffentlichung zusagen. Zusätzlich **Banner, solange
offen** — *Aufgabe:* in einer Zeile sagen, dass eine Änderung auf Veröffentlichung wartet.

**Was hier bewusst nicht geht**, als ruhiger Hinweis am Seitenende
- *Aufgabe:* benennen, was SARTU übernimmt (Layout, Seitenstruktur, Adressen, Texte), und auf
  `Hilfe` verweisen
- *Grenze:* **nicht als Einschränkung formulieren.** Es ist eine Leistung, keine Sperre
- *Umfang:* **ein Satz**

---

### `/hilfe`

**H1 `gebunden`:** `Hilfe`

**Nachricht schreiben:** Textfeld `Ihre Nachricht` (Pflicht, mindestens **10 Zeichen**) · Knopf
`gebunden` `Nachricht senden`. Dazu ein Hinweis — *Aufgabe:* schriftliche Antwort zusagen.
*Zahl `gebunden`:* in der Regel **innerhalb eines Werktags**. Darunter frühere Nachrichten mit
Antwort.

**Häufige Fragen**, statisch und aufklappbar. **Die fünf Themen sind gebunden, die Formulierung
nicht.**

- *Grenze je Frage:* als **vollständige Frage** überschreiben, nicht als Stichwort
- *Grenze je Antwort:* höchstens zwei Sätze. Nichts versprechen, was unten als gesperrt geführt ist

| # | Thema | Kern der Antwort |
|---|---|---|
| 1 | Anmeldung | Jedes Mal ein Link per E-Mail. **Kein Passwort** |
| 2 | Texte selbst ändern | Öffnungszeiten selbst; Texte, Bilder, Seitenstruktur ändert SARTU |
| 3 | Zeitpunkt der Rechnungen | Anzahlung nach Annahme, Schlussrechnung nach Abnahme, Betrieb monatlich |
| 4 | Domain | Der Kunde bleibt Inhaber; SARTU verwaltet technisch und sichert E-Mail-Einträge |
| 5 | Projektdauer | Nach vollständigen Angaben und Zahlung: die **Lieferkorridore aus `03_KUNDENPRODUKT.md`**, je Paket |

Link `gebunden`: `Einführung erneut ansehen` → Willkommensstrecke

---

### Leerzustände

**Ein Menüpunkt ohne Inhalt wird erklärt, nicht ausgeblendet.** Jeder Leerzustand sagt
**dasselbe Dreierlei: was hier später steht, wodurch es entsteht, und dass der Kunde jetzt nichts
tun muss.**

- *Grenze:* **nie** „Keine Daten", „Nichts gefunden", „–", `null`. Nie nach Fehler klingen
- *Umfang:* **ein Satz, höchstens 20 Wörter**

| Seite | wodurch der Zustand endet |
|---|---|
| Übersicht (kein Projekt) | sobald das Angebot vorliegt |
| `/angebot` | sobald SARTU die Anfrage geprüft hat |
| `/aufgaben` | sobald SARTU etwas braucht — **mit dem Zusatz, dass zusätzlich eine E-Mail kommt** |
| `/vorschau` | sobald die erste Fassung bereitsteht |
| `/rechnungen` | **mit dem Zusatz, dass direkt im Portal bezahlt werden kann und eine Kopie per E-Mail kommt** |
| `/domain` | sobald die Domain geprüft ist |
| `/inhalte` | sobald die Website online ist |

---

### Fehlerseiten und Fehlermeldungen

| Fall | Regel |
|---|---|
| **404** | *Aufgabe:* sagen, dass es die Seite nicht gibt, und einen Weg zurück anbieten. Knopf `gebunden`: `Zur Übersicht` |
| **403 / fremder Zugriff** | wird **als 404 behandelt** — `14_SICHERHEIT.md`. 403 verrät die Existenz |
| **500** | *Aufgabe:* Fehler einräumen, sagen dass SARTU informiert ist, zum späteren Versuch raten. **Fehlerkennung anzeigen, kein Stacktrace** |

**Fehlermeldungen an Formularen** werden geschrieben, nicht vorgeschrieben — nach einer festen
Form:

- *Aufgabe:* sagen, **was zu tun ist**, nicht was falsch war
- *Grenze:* **kein Systemcode, kein „ungültig", kein „Fehler".** Meldung **am Feld**, nicht als
  Sammelmeldung oben. Eingaben bleiben erhalten
- *Umfang:* **ein Satz**

**Diese Fälle müssen abgedeckt sein** — je einer für: fehlende Pflichtbestätigung vor der
Beauftragung (**mit der Zahl vier**) · fehlender Name · unbeantwortete Pflichtaufgabe · fehlende
Bildrechte-Bestätigung · keine Datei ausgewählt · fehlende Freigabebestätigung · fehlende
Abnahmebestätigung · Rückmeldung ohne Inhalt · Öffnungszeit ohne Von/Bis · Bis-Zeit vor Von-Zeit ·
unzulässige Dateiart · Datei zu groß · Speicher voll.

---

## Was der Kunde selbst kann

**Freigegeben:** Öffnungszeiten und Kontaktdaten pflegen (Stufe B).

**Gesperrt, nicht versprechen:** Bilder tauschen · Team- und Projekteinträge pflegen · Anfragen
aus der eigenen Website einsehen. Die Startseite darf diese drei **nicht** als Selbstbedienung
ankündigen — `10_WEBSITE_SARTU.md`.

Texte, Bilder und Seitenstruktur ändert SARTU; das ist im Rundum-Schutz enthalten
(`02_PREISE_UND_ZAHLUNG.md`).

---

## Zahlungen in Stufe 0

- **Der Zahlungsstatus wird nie aus einer Rückkehr-URL abgeleitet.** Das gilt **schon vor A2**
- In Stufe 0 legt der Admin Rechnungen manuell an; das Portal zeigt Status und Zahlungslink
- Rechnungsarchiv, Aufbewahrungsfristen und Nummernkreise laufen im Buchhaltungswerkzeug,
  **nicht** im Portal

---

## Uploads

- **Erlaubt:** `jpg` · `jpeg` · `png` · `webp` · `svg` · `pdf` · `docx` · `zip`
- Höchstens **20 MB je Datei**, **10 Dateien je Aufgabe**, **500 MB je Organisation insgesamt**.
  Der Admin sieht den Verbrauch je Kunde und kann die Grenze einzeln anheben
- **Vor jedem Upload wird der freie Platz auf dem Server geprüft.** Unter **1 GB**: Ablehnung mit
  Klartextmeldung und Hinweis an den Admin — statt eines abgebrochenen Schreibvorgangs
- Prüfung von **Endung und MIME-Typ**; bei Abweichung ablehnen
- Pfade als **UUID**, außerhalb von `/public`, Auslieferung nur über eine Route, die Session
  **und** Organisationszugehörigkeit prüft
- **SVG werden nicht inline eingebettet**, sondern nur als Download angeboten — Skriptrisiko
- Aufbewahrung: einer Rechnung zugeordnet **8 Jahre**, sonst **12 Monate**. Löschung durch den
  täglichen Lauf

---

## E-Mails

**Der Kunde meldet sich ausschließlich per Anmeldelink an. Was ihm keine Mail mitteilt, erfährt
er nicht.** Das ist der Grund, warum diese Liste vollständig sein muss.

**Rahmen für alle Mails — `gebunden`:** Absender `MAIL_FROM` · Anrede `Guten Tag {Vorname},` ·
Grußformel `Freundliche Grüße` / `SARTU` · Fußzeile mit Impressumsangaben und dem Hinweis
`Diese Nachricht bezieht sich auf Ihr Projekt „{Projekttitel}".` **Klartext und einfaches HTML.**

**Betreffzeilen sind gebunden** (Wiederfindemarke im Postfach), **der Fließtext wird geschrieben.**

- *Grenze je Mail:* **höchstens drei Sätze.** Die Handlung steht im ersten. Kein Systemcode, kein
  Statuscode, kein Link ohne Beschriftung
- *Grenze bei Geld und Fristen:* Betrag, Datum und Rechnungsnummer stehen **wörtlich** im Text —
  sie sind Zahlen, nicht Formulierung

| Auslöser | Betreff (`gebunden`) | was der Text tragen muss |
|---|---|---|
| Neue Anfrage über die Website (**an Admin**) | `Neue Anfrage: {Unternehmen}` | empfohlene Lösung, Ampelkennzeichen, Link auf `/admin/anfragen` |
| Anmeldelink | `Ihr Anmeldelink für das SARTU-Portal` | der Link · **15 Minuten** · **einmal** verwendbar |
| Einladung (neu angelegt) | `Ihr Zugang zum SARTU-Portal` | dass der Bereich bereitsteht, was dort liegt, der Link |
| Angebot gesendet | `Ihr Angebot von SARTU liegt bereit` | Umfang, Preis, Zahlungsplan liegen bereit · **gültig bis {Datum}** |
| Angebot angenommen (**an Kunde**) | `Bestätigung Ihrer Beauftragung` | Dank **und** was als Nächstes kommt: die Anzahlungsrechnung |
| Angebot angenommen (**an Admin**) | `Angebot angenommen: {Organisation}` | interne Kurzmeldung |
| Rechnung gesendet | `Ihre Rechnung {Nummer}` | **fällig bis {Datum}** · direkt im Portal zahlbar |
| Zahlung verbucht | `Zahlungseingang bestätigt` | Eingang bestätigen, danken |
| Neue Aufgaben | `Es liegen Aufgaben für Sie bereit` | dass Angaben gebraucht werden · **Aufwand 15 bis 25 Minuten** |
| Faktenfreigabe erfolgt (**an beide**) | `Freigabe bestätigt — wir starten` | Produktionsbeginn · **Fertigstellung in {min}–{max} Werktagen** |
| Vorschau bereit | `Ihre Vorschau ist bereit` | ansehen und **gebündelt** zurückmelden |
| Korrekturrunde eingereicht (**an Admin**) | `Korrekturrunde {Nummer} eingereicht: {Organisation}` | Anzahl der Rückmeldungen |
| Korrekturrunde eingearbeitet | `Ihre Änderungen sind eingearbeitet` | umgesetzt · neue Fassung liegt in der Vorschau |
| Abnahme erfolgt (**an beide**) | `Abnahme bestätigt` | Dank · Startvorbereitung läuft |
| Website online | `Ihre Website ist online` | **erreichbar unter {URL}** · ab jetzt läuft der Betrieb |
| Öffnungszeiten veröffentlicht | `Ihre Öffnungszeiten sind aktualisiert` | Änderung ist sichtbar |
| Antwort auf Nachricht | `Antwort auf Ihre Nachricht` | Antworttext + Portallink |
| **Zahlungserinnerung, erste** | `Erinnerung: Rechnung {Nummer} ist fällig` | **{Restbetrag}** · **war am {Datum} fällig** · im Portal zahlbar · **der Satz, dass die Nachricht bei bereits erfolgter Überweisung gegenstandslos ist** |
| **Zahlungserinnerung, zweite** (nach **7 Tagen**) | `Zweite Erinnerung: Rechnung {Nummer}` | derselbe Aufbau, zusätzlich die Einladung, sich bei Unklarheiten zu melden — **parallel Hinweis an den Admin** |
| **Teilzahlung verbucht** | `Teilzahlung erhalten` | **{Betrag} erhalten** · **{Restbetrag} offen** |
| **Zahlungsstatus zurückgenommen** | `Korrektur zu Rechnung {Nummer}` | Korrektur · **Grund** · Bitte um Prüfung im Portal |
| **Angebot läuft in 3 Tagen ab** | `Ihr Angebot gilt noch bis {Datum}` | **Ablaufdatum** · Neuausstellung auf Zuruf möglich |
| **Projekt pausiert** | `Ihr Projekt pausiert` | angehalten · **Grund `{pause_reason}`** · SARTU meldet sich |
| **Projekt wird fortgesetzt** | `Es geht weiter` | läuft wieder · nächster Schritt im Portal |

**Keine Werbemails, keine Newsletter, keine Massenversendung.**

> **Sieben Zeilen ergänzt am 31.07.2026 nach dem Audit.** Vier Zustände traten ein, ohne dass
> jemand davon erfuhr: Rechnung wird überfällig (Status sprang um, **keine Mail**) ·
> Zahlungsstatus zurückgenommen (eine Benachrichtigung war versprochen, aber nirgends
> beschrieben) · Angebot läuft ab (Sackgasse, aus der nur Handarbeit herausführte) · Projekt
> pausiert (`pause_reason` war Pflichtfeld und wurde angezeigt — **gesehen hat es niemand**).

Jede lokal versendete Mail wird von **Mailpit** abgefangen (`http://localhost:8025`).

---

## Vollständigkeit

**Diese Datei ist vollständig.** Sie führt den Kundenbereich von der Anmeldung bis zum Betrieb:
Navigation, Willkommensstrecke, alle elf Projektstatus mit ihren Übergängen, alle neun Seiten,
Leerzustände, Fehlerseiten, die festen Angebotstexte, Uploads und sämtliche E-Mails.

**Aus dem Portal-Lastenheft wird nichts mehr nachgelesen.** Was dort noch steht und hier fehlt,
gehört einem anderen Thema: Tabellen und Felder (`13_DATENMODELL.md`), Sicherheitsregeln und
Ersteinrichtung (`14_SICHERHEIT.md`), der Adminbereich (`12_ADMINBEREICH.md`), der Anfrageeingang
(`09_ANFRAGEEINGANG.md`).
