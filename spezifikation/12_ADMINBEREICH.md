# Der Adminbereich — `/admin/`

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus: `CLAUDE_SARTU_PORTAL_LASTENHEFT_BAUFINAL.md` §9, §11
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

> **Der Adminbereich ist eine eigene, getrennte Zugriffsschicht** — nicht dieselbe Tür mit
> mehr Rechten. Warum das so sein muss: `14_SICHERHEIT.md`, Regel 2a.

---

## Zugang

Unter `/admin`, **eigenes Layout, sichtbar von der Kundenoberfläche unterscheidbar**.
Anmeldung: E-Mail + Passwort + **TOTP**.

**Admin-2FA ist Pflicht, auch lokal nicht abschaltbar** — im Entwicklungsmodus mit festem
Testschlüssel, nicht deaktiviert.

Admins haben bewusst **keine** `organization_id`. Wählt ein Admin einen Kunden aus, gilt das
**nur** im Adminbereich und schreibt **niemals** die Sitzungsorganisation um.

---

## Screens

| Pfad | Inhalt |
|---|---|
| `/admin` | Cockpit: **neue Anfragen**, Projekte nach Status gruppiert, offene Rechnungen, unbeantwortete Nachrichten, eingereichte Korrekturrunden, wartende Öffnungszeit-Änderungen |
| `/admin/anfragen` | Eingegangene Bedarfsschecks, Umwandlung in Kunde und Projekt |
| `/admin/kunden` | Liste, Suche nach Name und E-Mail; Anlegen und Bearbeiten von Organisation und Benutzer; Knopf `Einladung senden` |
| `/admin/projekte` | Liste mit Filter nach Status |
| `/admin/projekte/{id}` | **Arbeitsplatz je Projekt** |
| `/admin/rechnungen` | Alle Rechnungen, Filter offen / überfällig / bezahlt |
| `/admin/nachrichten` | Support-Nachrichten mit Antwortfeld |
| `/admin/audit` | Audit-Log, filterbar nach Organisation, Aktion, Zeitraum |

---

## Projekt-Arbeitsplatz `/admin/projekte/{id}`

Alles in Abschnitten auf **einer** Seite:

- **Kopf** — Kunde, Paket, Status (Auswahlfeld + `Status setzen`), Felder `Nächster Schritt` und
  `Ziel-Pfad`
- **Angebot** — Formular für alle Felder aus `offers`, Knopf `Angebot senden` (setzt `sent_at`,
  Status `gesendet`, verschickt E-Mail). **Nach Annahme schreibgeschützt**
- **Rechnungen** — Anlegen mit Nummer, Meilenstein, Beträgen, Fälligkeit, Feld
  `Mollie-Zahlungslink`. Aktionen `Senden` und `Als bezahlt markieren` **mit Pflicht-Bestätigung**
- **Aufgaben** — einzeln oder **aus Vorlage**, sortierbar, bearbeitbar, deaktivierbar. Anzeige der
  Kundenantworten und Dateien mit Download
- **Vorschau** — Feld `Vorschau-URL`, Knopf `Vorschau bereitstellen` (setzt Status `vorschau`,
  öffnet **zugleich** eine neue Korrekturrunde, verschickt E-Mail)
- **Korrekturrunden** — Liste mit Nummer, Status, Zeitpunkten und Kennzeichen
  `enthalten` / `zusätzlich`. Anzeige `{genutzt} von {included_feedback_rounds} enthaltenen Runden`
- **Feedback** — Rückmeldungen der gewählten Runde, je Eintrag Antwortfeld und Statuswechsel

---

## Audit — nicht verhandelbar

Audit-Einträge entstehen bei Angebotsannahme, Abnahme, Zahlungsstatuswechsel,
Projektstatuswechsel, Rechteänderung, Löschung, Anmeldung und **fehlgeschlagener Anmeldung**.

**Einträge werden nie geändert und nie gelöscht.** Bei Geld und Fristen ist `reason` Pflichtfeld.

---

## Was hier nicht gebaut wird

Weder jetzt noch vorbereitend: Zahlungsdienst-Anbindung · Domainautomatik · Mahnwesen ·
Dunkelmodus · mehrere Benutzer je Kunde · Dateiversionierung · Anfragen aus **Kunden**websites ·
Pipeline- und Kanban-Ansichten · Bewertung · Nachfassketten.
