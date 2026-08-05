# Der Anfrageeingang — vom Bedarfsscheck bis zur Anfrageliste

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 05.08.2026 aus: `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` §4b
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

> **Warum eine eigene Datei.** Der Bedarfsscheck steht auf der Website, die Anfrageliste im
> Adminbereich, die Felder im Datenmodell, die Spamabwehr in der Sicherheit. Auf vier Dateien
> verteilt wäre derselbe Vorgang viermal beschrieben — genau die Dublette, gegen die diese
> Sammlung angelegt wurde. **Ein Vorgang, eine Datei.**

> Formular und Fragen: `10_WEBSITE_SARTU.md`. Tabelle `leads`: `13_DATENMODELL.md`.
> Rate-Limits und CSRF im Allgemeinen: `14_SICHERHEIT.md`. Sprache: `08_TEXTREGELN.md`.

**Ohne diesen Abschnitt bricht der Gesamtprozess.** Der Bedarfsscheck erzeugt Anfragen; sie
müssen ankommen und geprüft werden können.

---

## 1. Der Weg — und warum es kein Geheimnis braucht

Bedarfsscheck und Anfrageliste liegen im **selben** PHP-Projekt:

```
Browser des Interessenten
        │  normales Formular-POST an die eigene Domain
        ▼
Formularannahme  →  AnfrageService::anlegen()  →  Tabelle `leads`
```

**Kein Netzaufruf, kein gemeinsames Geheimnis, keine Tokenprüfung.** Frühere Fassungen sahen eine
Kopfzeile `X-Sartu-Token` mit `INTAKE_TOKEN` vor. Das war richtig, **solange Website und
Kundenbereich getrennte Anwendungen waren**. In einem Projekt wäre es Zeremonie: ein Geheimnis,
das nichts schützt, aber verwaltet, übertragen und irgendwann versehentlich ausgeliefert werden
kann.

**Alle inhaltlichen Schutzmaßnahmen bleiben unverändert** (Abschnitt 3) — sie schützen vor Spam
und Missbrauch, nicht vor einem fremden Aufrufer. **Nur der Übertragungsweg entfällt.**

**Die Fachlogik liegt trotzdem in einem eigenen Dienst** (`/app/services/AnfrageService.php`),
nicht im Formularcode. Grund: In Stufe 1 sollen auch **Kundenwebsites** Anfragen abliefern können.
Dann kommt ein dünner Endpunkt unter `/api/` davor — mit Token, Rate-Limit je Absender und
Herkunftsprüfung. Der Dienst selbst bleibt gleich.

> **In Stufe 0 gibt es diesen Endpunkt nicht**, auch nicht vorbereitend. Anfragen aus
> Kundenwebsites stehen auf der Nicht-bauen-Liste (`CODEX_AUFTRAG_PORTAL.md` §5).

---

## 2. Formularannahme `POST /briefing/absenden`

| Punkt | Festlegung |
|---|---|
| Methode und Pfad | `POST /briefing/absenden` — normales Formular, gleiche Domain |
| CSRF | Pflicht, wie bei jedem `POST` — `14_SICHERHEIT.md` |
| Rate-Limit | **10 abgeschickte Bedarfsschecks je IP und Stunde**, zusätzlich **60 je Stunde gesamt** |
| Größe | höchstens **64 KB** Formulardaten |
| Nach Erfolg | Weiterleitung (`303`) auf die Danke-Seite. **Nie** ein erneut absendbares Formular anzeigen |

### Felder — vollständige Liste

Der Bedarfsscheck darf erweitert werden; **unbekannte Felder landen unverändert in `payload`**,
statt abgewiesen zu werden.

| Feld | Typ | Pflicht | Prüfung |
|---|---|---|---|
| `submission_id` | UUID | ja | entsteht beim **Start** des Bedarfsschecks, bleibt über alle Schritte gleich |
| `form_started_at` | Zeitstempel | ja | Zeitregel, Abschnitt 3 |
| `first_name` | Text ≤ 100 | ja | nicht leer nach `trim()` |
| `last_name` | Text ≤ 100 | ja | nicht leer nach `trim()` |
| `company` | Text ≤ 200 | ja | nicht leer nach `trim()` |
| `email` | Text ≤ 254 | ja | Formatprüfung, **kleingeschrieben** gespeichert |
| `phone` | Text ≤ 50 | nein | wie eingegeben speichern |
| `preferred_contact` | `email` \| `portal` | ja | nur diese zwei Werte |
| `b2b_confirmed` | Wahrheitswert | ja | muss `true` sein |
| `privacy_confirmed` | Wahrheitswert | ja | muss `true` sein |
| `recommended_package` | `start` \| `wachstum` \| `platzhirsch` \| `sonderprojekt` \| `unklar` | nein | **serverseitig** berechnet |
| `flag` | `standard` \| `gelb` \| `orange` \| `rot` | nein | **serverseitig** berechnet |
| `answers` | Feld-Wert-Paare | ja | unverändert nach `payload` |
| `hp_website` | Text | nein | **Honigtopf** — gefüllt ⇒ verwerfen |

> **Empfehlung und Ampelkennzeichen werden serverseitig berechnet, nie aus dem abgeschickten
> Formular übernommen.** Sonst könnte jemand die Empfehlung von außen setzen — und damit die
> Preisstufe, in der er einsortiert wird.

### Verhalten

| Lage | Reaktion |
|---|---|
| Angenommen | `lead` angelegt, E-Mail an SARTU, Weiterleitung auf die Danke-Seite |
| Bereits bekannte `submission_id` | **keine** Anlage, trotzdem Danke-Seite |
| Honigtopf gefüllt oder Zeitregel verletzt | **keine** Anlage, trotzdem Danke-Seite — der Absender merkt nichts |
| Pflichtfeld fehlt oder ungültig | Schritt erneut anzeigen, Meldung **am Feld**, Angaben bleiben erhalten |
| Rate-Limit erreicht | Hinweis mit Kontaktalternative, **keine** technischen Details |
| Serverfehler | Angaben bleiben erhalten, allgemeine Meldung, interne Kennung ins Protokoll |

**Fehlermeldungen nennen nie** einen Datenbankfehler, eine interne Kennung oder ob eine
E-Mail-Adresse bereits bekannt ist.

---

## 3. Spamabwehr und Doppeleinreichung

1. **Honigtopf `hp_website`** — für Menschen unsichtbar, aber **nicht** über `display:none`
   allein: Vorlesesoftware muss es überspringen, also `aria-hidden="true"` **und**
   `tabindex="-1"`. Gefüllt ⇒ stillschweigend verwerfen, Danke-Seite trotzdem zeigen
2. **Zeitregel** — liegen zwischen `form_started_at` und dem Absenden weniger als **3 Sekunden**,
   stillschweigend verwerfen. Menschen brauchen für den Bedarfsscheck Minuten
3. **Doppeleinreichung** — `submission_id` ist in `leads` **eindeutig**. Ein zweiter Versuch mit
   derselben Kennung ändert nichts. Das deckt Doppelklick, Neuladen und die Zurück-Taste ab
4. **Kein Rätselbild und kein Fremddienst in Stufe 0.** Turnstile, hCaptcha und Vergleichbares
   sind externe Verbindungen mit eigener Datenschutzfolge. Erst nachrüsten, wenn Spam **messbar**
   auftritt, und dann mit dokumentierter Rechtsgrundlage

---

## 4. Datenschutz und Aufbewahrung

- **Datensparsamkeit:** gespeichert wird ausschließlich, was der Interessent eingegeben hat.
  **Keine** Anreicherung aus Fremdquellen, kein Standortnachschlagen, keine Bewertung
- **`source_ip`** wird gespeichert (Missbrauchsabwehr, Nachweis der Einwilligung) und **nach
  30 Tagen geleert** — der übrige Datensatz bleibt. Umsetzung im täglichen Lauf
- **Protokolle:** Zeitpunkt, Ergebnis, **gekürzte IP** (letztes Oktett entfernt), `submission_id`.
  **Nie** Name, E-Mail, Telefonnummer oder Antworttexte
- **Löschfristen:** abgelehnte Anfragen **nach 6 Monaten**, alle übrigen nicht umgewandelten
  **nach 12 Monaten**. Umgewandelte bleiben als Teil der Kundenakte. **Das Löschdatum ist im
  Adminbereich sichtbar**
  > Die kürzere Frist gilt für den engeren Fall. Eine abgelehnte Anfrage hat keinen Grund mehr,
  > länger zu liegen.
- **Betroffenenrechte** je Datensatz: `Datensatz exportieren` (alles, was gespeichert ist) und
  `Endgültig löschen` — echtes `DELETE`, **ausdrückliche Ausnahme** von der Archivierungsregel in
  `14_SICHERHEIT.md`. Der Löschvorgang wird protokolliert, **ohne** die gelöschten Inhalte
- Die Einwilligung erklärt der Interessent im Bedarfsscheck. Gespeichert wird, **dass** und **wann**

---

## 5. Herkunft einer Anfrage — datensparsam und first-party

**Warum das nötig ist:** Nach einem SEA-Test muss beantwortbar sein, welcher Begriff eine
**Anfrage** gebracht hat — nicht nur einen Klick. Die Search Console zeigt Suchanfragen, aber
nicht, was daraus wurde. Ohne diese Felder ist der Test nur halb auswertbar.

**Wann erfasst wird — das ist die Stelle, an der es sonst schiefgeht:** Die Kennzeichen stehen in
der Adresse der **ersten** aufgerufenen Seite. Bis der Bedarfsscheck abgeschickt wird, sind sie
längst weg. Sie werden deshalb **beim ersten Seitenaufruf** in die serverseitige Sitzung
geschrieben und erst beim Anlegen des `lead` übernommen.

| Feld | Woher | Datensparsamkeit |
|---|---|---|
| `landing_page` | erste aufgerufene Seite | **nur der Pfad**, ohne Abfragezeichenfolge |
| `referrer_host` | `Referer`-Kopfzeile | **nur der Hostname** — die vollständige Adresse kann Suchbegriffe oder Kennungen enthalten |
| `utm_*` | Abfrageparameter | wie übergeben, auf je 100 Zeichen begrenzt |
| `click_id` | `gclid`, `gbraid` oder `wbraid` | Wert **und Art** speichern; nur setzen, wenn tatsächlich vorhanden |
| `self_reported_source` | Frage im Bedarfsscheck | freiwillig, Auswahl + Freitextfeld |

**Die Frage im Bedarfsscheck** — freiwillig, letzter Schritt:

- *Aufgabe:* fragen, wie der Interessent auf SARTU aufmerksam wurde
- *Grenze:* **kein Pflichtfeld.** Eine unbeantwortete Frage ist besser als eine erzwungene
  Falschangabe. Nicht nach dem Grund fragen, nur nach dem Weg
- *Umfang:* **eine Zeile**
- *Auswahl `gebunden`:* `Suchmaschine` · `Empfehlung` · `Direkt angesprochen worden` · `Anzeige` ·
  `Sonstiges`, dazu ein optionales Freitextfeld

**Warum beides und nicht nur eines:** Die technischen Kennzeichen sagen, **woher der Klick kam**.
Die Selbstauskunft sagt, **warum jemand kam** — und die weicht regelmäßig ab. Wer über eine
Empfehlung von SARTU hört und danach den Namen googelt, kommt technisch über die Suche.

**Datenschutz:**

- Alles **first-party**. **Kein** Tracking über fremde Seiten, **keine** Cookies Dritter,
  **kein** Analysedienst. Damit ist auch **kein Einwilligungsbanner nötig**
- Die Daten dienen **ausschließlich** der Auswertung eigener Anfragen, nicht der Profilbildung
- Sie folgen der **Löschfrist des Leads** (Abschnitt 4)
- Die Datenschutzerklärung muss diese Verarbeitung abdecken. Das ist **Teil des Auftrags an die
  Kanzlei** (`20_OFFEN.md`), nicht selbst zu formulieren

---

## 6. Die Anfrageliste `/admin/anfragen`

**Das ist bewusst eine Liste, kein Vertriebssystem.** Keine Pipeline, kein Kanban, keine
Bewertung, keine Nachfasskette — siehe `12_ADMINBEREICH.md`, „Was hier nicht gebaut wird".

**Liste:** Eingangsdatum · Firma · Name · empfohlene Lösung · Ampelkennzeichen · Status ·
**Löschdatum**. Filter nach Status, Sortierung nach Eingang, neueste zuerst. Zusätzlich filterbar
nach **Herkunft** (Abschnitt 5).

**Detailansicht:** **alle** Antworten in Klartext als Frage → Antwort, nicht als Rohdaten.

**Aktionen:**

| Aktion | Wirkung |
|---|---|
| `In Kunde und Projekt umwandeln` | legt `organizations`, `users` (Rolle `kunde`) und `projects` an, setzt `converted_organization_id` und `status = angebot_erstellt`, verschickt die Einladungs-E-Mail. **Bestätigungsdialog vorher**, weil dabei ein Zugang entsteht |
| `Als abgelehnt markieren` | mit **Pflichtnotiz** |
| `Notiz speichern` | frei |
| `Datensatz exportieren` · `Endgültig löschen` | Abschnitt 4 |

> **Anfrage ≠ Kunde.** Ein Zugang entsteht ausschließlich durch diesen bewussten Klick — **nie
> automatisch.**

**Keine Diagramme, keine Kennzahlenübersicht, kein Zeitverlauf** — das ist Stufe 1. In Stufe 0
genügt eine filterbare Liste, aus der sich die Frage „welche Kampagne brachte Aufträge?" von Hand
beantworten lässt.

---

## 7. Das Kontaktformular ist **nicht** der Bedarfsscheck

Es versendet ausschließlich eine E-Mail an SARTU und erzeugt **keinen** Datensatz. Honigtopf,
Zeitregel und Rate-Limit gelten dort gleichermaßen.
