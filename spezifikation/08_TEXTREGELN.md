# Sprache und Textregeln

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus: `CLAUDE.md`, `SARTU_TEXTREGELN.md`,
> Website-Lastenheft §2 und §5aa
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

---

## Drei Ebenen, die ineinandergreifen

| Ebene | regelt |
|---|---|
| **diese Datei** | welche **Behauptungen** verboten sind und welche Wörter |
| `SARTU_TEXTREGELN.md` | die **Form** — Satzlänge, Aufzählungen, Wortlisten, Prüfbericht |
| `.claude/skills/sartu-texter/SKILL.md` | **wie geschrieben wird**. Er schreibt den Wortlaut |

**Alle drei gelten. Ohne ausgefüllten Prüfbericht gilt eine Seite als nicht abgegeben.**

## Ansprache und Marke

- Durchgängig **„Sie"**
- Marke immer **`SARTU`** in Versalien, auch im Fließtext

## Wortwahl nach außen

**Erlaubt:** *Kundenbereich · Ihr Bereich · Anmeldung · Ihr Projekt*
**Nie nach außen:** *App · Software · SaaS · Plattform · Tool · Dashboard · System · Instanz*
Intern darf „Adminbereich" stehen.

## Verbotene Aussagen — prüfbar per Suche

| Verboten | Grund | Stattdessen |
|---|---|---|
| „wartungsarm", „wartungsfrei", „kaum Wartung" | entwertet den Rundum-Schutz | **„keine Wartung für Sie"** |
| „rechtssicher", „abmahnsicher", „DSGVO-konform" (absolut) | Rechtsberatungs- und Haftungsrisiko | „datensparsam umgesetzt", „Rechtstexte technisch eingebunden" |
| „garantiert Platz 1", „garantierte Sichtbarkeit", „garantierte KI-Nennung" | unhaltbar | „Fundament für Sichtbarkeit, ohne Rankinggarantie" |
| Prozentversprechen wie „spart 80 % Zeit" | keine eigenen Daten | qualitativ formulieren |
| „Paket wählen", „konfigurieren", „Extras hinzufügen", „SEO buchen" | widerspricht der Angebotslogik | „Bedarf prüfen lassen", „einschätzen lassen" |
| „günstig", „billig", „Schnäppchen" | falsche Positionierung | „Festpreis", „klarer Gesamtpreis" |
| „unser Team", solange eine Einzelperson arbeitet | Ehrlichkeit | **„gründergeführt"** |
| „Ihre Website ist ab dem ersten Tag auffindbar" | ist „garantierte Sichtbarkeit" in weichen Worten | „ab dem ersten Tag **für Suchmaschinen vorbereitet**" |

## Pflichthinweis bei jeder Preisnennung

> Alle Preise netto zzgl. gesetzlicher Umsatzsteuer. Ausschließlich für Unternehmer.

## Darstellungsregeln

- Der Kunde sieht **nie** einen Systemcode wie `qa_failed`, immer Klartext
- Leere Werte: **`Noch nicht hinterlegt`** — nie `null`, `–` oder `undefined`
- Datum in **Europe/Berlin**, Format `TT.MM.JJJJ, HH:MM Uhr`, **nie** ISO
- Geld: `7.900,00 €`

## Benennung von Beispielen

- **Konkrete Gattung statt Oberbegriff.** „Handwerksbetrieb" kann man sich nicht vorstellen,
  „Malerbetrieb" schon — und ein Malermeister erkennt sich wieder
- **Keine erfundenen Firmennamen**, die wie echte Betriebe klingen. Die Gattung genügt
- **Nie** `Ausgewählte Arbeiten`, `Referenzen`, `Kunden` oder `Projekte` als Überschrift, solange
  es keine echten gibt. **Die Bezeichnung entscheidet, ob es Demonstration oder Täuschung ist**
