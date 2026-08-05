# Der Kundenbereich — `/portal/`

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus: `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` §5 bis §12
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

> Sicherheit und Mandantentrennung: `14_SICHERHEIT.md`. Tabellen und Formate:
> `13_DATENMODELL.md`. Sprache: `08_TEXTREGELN.md`.

---

## Navigation — feste Reihenfolge

`Übersicht` · `Angebot` · `Aufgaben` · `Vorschau` · `Rechnungen` · `Domain` · `Inhalte` ·
`Vertrag` · `Hilfe`

- **Menüpunkte, für die es noch nichts gibt, werden angezeigt und erklärt, nicht ausgeblendet**
- Jede Seite: `<h1>` als Seitentitel, `<title>` als `{Seite} — SARTU-Portal`

**`/vertrag`** zeigt die Rechtstexte mit `audience = kunde` aus `legal_texts` — den
Auftragsverarbeitungsvertrag und die technischen und organisatorischen Maßnahmen. Dazu eine
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
- H1: `Anmelden`
- Text: `Geben Sie Ihre E-Mail-Adresse ein. Wir schicken Ihnen einen Anmeldelink — ein Passwort brauchen Sie nicht.`
- Feldlabel: `E-Mail-Adresse` · Knopf: `Anmeldelink senden`
- Fehler: `Bitte geben Sie eine gültige E-Mail-Adresse an, z. B. name@firma.de`

**Bestätigungsseite**
- H1: `Prüfen Sie Ihr Postfach`
- Text: `Wenn ein Zugang zu dieser Adresse besteht, ist der Anmeldelink unterwegs. Er gilt 15 Minuten und lässt sich einmal verwenden.`
  > **Gekoppelt:** Die 15 Minuten sind **Kundentext**, der die Regel aus `14_SICHERHEIT.md` wiedergibt.
  > Ändert sich die Frist dort, ändert sich dieser Satz mit.
- Hinweis: `Nichts angekommen? Sehen Sie im Spam-Ordner nach oder fordern Sie den Link erneut an.`
- **Notweg, immer sichtbar** — Werte aus `operator_settings`

---

## Statuslogik

**Grundregel:** Intern gibt es technische Werte, dem Kunden wird **immer Klartext** gezeigt.
**Interne Codes erscheinen nie in der Kundenoberfläche.**

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

Die zulässigen Übergänge und wer sie auslöst stehen in Portal-Lastenheft §5.1a.
**Jeder Statuswechsel erzeugt ein Audit-Ereignis** — `14_SICHERHEIT.md`.

---

## Was der Kunde selbst kann

**Freigegeben:** Öffnungszeiten und Kontaktdaten pflegen (Stufe B).

**Gesperrt, nicht versprechen:** Bilder tauschen · Team- und Projekteinträge pflegen · Anfragen
aus der eigenen Website einsehen. Die Startseite darf diese drei **nicht** als Selbstbedienung
ankündigen — siehe die Korrektur zu Frage 6 in `10_WEBSITE_SARTU.md`.

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

- Pfade als **UUID**, außerhalb von `/public`, Auslieferung nur über eine autorisierte Route
- Aufbewahrung: einer Rechnung zugeordnet **8 Jahre**, sonst **12 Monate**. Löschung durch den
  täglichen Lauf

---

## E-Mails

Jede lokal versendete Mail wird von **Mailpit** abgefangen (`http://localhost:8025`).
Wortlaute und Auslöser: Portal-Lastenheft §10.

---

## Noch nicht zusammengeführt

Die **Screen-für-Screen-Vorgaben** (§8), die **festen Angebotstexte** (§4c), der
**Anfrageeingang aus dem Bedarfsscheck** (§4b), die **Willkommensstrecke** (§7) und die
**E-Mail-Wortlaute** (§10) sind feldgenaue Vorgaben von zusammen rund 900 Zeilen. Sie stehen
unverändert in `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` und werden von dort gelesen, bis sie
hier eingearbeitet sind.
