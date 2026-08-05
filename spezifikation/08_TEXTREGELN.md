# Sprache und Textregeln

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus: SARTU_TEXTREGELN.md, CLAUDE.md
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

---

## Textregeln

**Nach außen:** *Kundenbereich · Ihr Bereich · Anmeldung · Ihr Projekt*
**Nach außen nie:** *App · Software · SaaS · Plattform · Tool · Dashboard · System · Instanz*
Intern darf „Adminbereich" stehen.

- Der Kunde sieht **nie** einen Systemcode (`qa_failed`), immer Klartext
- Leere Werte in der Oberfläche: `Noch nicht hinterlegt` — nie `null`, `–` oder `undefined`
- Datum in **Europe/Berlin**, Format `TT.MM.JJJJ, HH:MM Uhr`, nie ISO
- Zu jeder abgegebenen Seite gehört der **Prüfbericht mit Zahlen**

Vollständige Regeln: `SARTU_TEXTREGELN.md` und der Skill `.claude/skills/sartu-texter/`.
