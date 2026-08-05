# Offene Entscheidungen und Platzhalter

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus: `SARTU_ENTSCHEIDUNGEN_OFFEN.md`
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

> **Regel:** Ein Platzhalter aus dieser Datei darf **niemals** durch einen erfundenen Wert
> ersetzt werden. **Steht er auf `offen`, gilt die dort genannte Sperre.**
>
> Wer hier einen Wert einträgt, schaltet ihn im ganzen Projekt frei — ohne dass jemand zehn
> Dateien durchsucht.

---

## Standort und Anschrift

| Platzhalter | Wert | Stand |
|---|---|---|
| `[STARTREGION]` | **Raum Dresden** | entschieden |
| `[HAUPTORT]` | **Dresden** | entschieden |
| Einzugsgebiet | **Dresden + Umkreis** | entschieden 01.08.2026 |
| Eigene Ortsseiten | **nur `/webdesign-dresden`** zum Start | entschieden — weitere werden **verdient, nicht verteilt** |
| `[HEIMATORT]` | *offen* | nur wenn er ein echter Vertrauensanker ist |
| `[GESCHAEFTSADRESSE_STATUS]` | *offen* | **blockiert das Google-Unternehmensprofil** |
| `[ANSCHRIFT]`, `[TELEFON]`, `[EMAIL]` | *offen* | erst mit der Geschäftsadresse entscheidbar |

### Was die Standortentscheidung regelt — und was nicht

**Sie regelt genau zwei Dinge:** das Google-Unternehmensprofil samt Kartenbereich, und ob und wo
eigene Ortsseiten entstehen.

**Sie regelt nicht, wo SARTU arbeitet.** Das Produkt kommt ohne einen einzigen Termin aus.
Entfernung spielt in der Lieferung keine Rolle — ein Malermeister in Kassel wird genauso bedient
wie einer in Radeberg. **Der Markt ist Deutschland.**

> **Berichtigt am 01.08.2026.** Die frühere Fassung las sich, als sei SARTU ein regionales
> Unternehmen. Das ist falsch. Von zehn Vertriebskanälen ist genau **einer** ortsgebunden.

---

## Person

| Platzhalter | Stand |
|---|---|
| `[GRUENDER_NAME]` | *offen* — gebraucht für Startseite Sektion 6 und `/ueber-uns` |
| **Echtes Foto** | *offen* — **ohne Foto entfällt Sektion 6 vollständig** |

**Ein leerer Rahmen an einer Vertrauensstelle ist schlechter als gar nichts.**

---

## Rechtstexte

| Platzhalter | Weg | Risiko |
|---|---|---|
| `[IMPRESSUM]` | Entwurf durch KI, dann Prüfung | **gering** — überwiegend Formsache nach § 5 DDG |
| `[DATENSCHUTZ]` | Entwurf durch KI, dann Prüfung | **mittel** — hängt daran, was die Seite tatsächlich tut |
| `[AGB]` | Entwurf durch KI, dann Prüfung | **hoch** — Laufzeit, Kündigung, Haftung, Zahlung, Leistungsumfang |

Rechtstexte werden **technisch eingebunden, nicht rechtlich erstellt** — `06_RECHT.md`.

---

## Sonstige offene Punkte

| Sache | Lage |
|---|---|
| **Buchhaltung** | lexoffice **oder** sevDesk — Auswahlkriterien in `02_PREISE_UND_ZAHLUNG.md` |
| **Typografie** | reine Grotesk (Inter / Instrument Sans) vs. Grotesk + editorial Serif für H1. Die Seite läuft auf einem Platzhalter — `07_MARKE_UND_GESTALTUNG.md` |
| **`Leistungen` in der Navigation** | §5b widerspricht sich selbst, beide Stellen begründet — Dublettenregister im Wegweiser |
| **`LocalBusiness`** in strukturierten Daten | gesperrt, bis eine Anschrift feststeht |
| **Echte Fallstudie** | erst wenn ein echter Kunde schriftlich freigegeben hat |

---

## Sperren, die aus offenen Punkten folgen

- **Ohne Anschrift:** kein Google-Unternehmensprofil, kein `LocalBusiness`
- **Ohne Gründerfoto:** keine Sektion 6 auf der Startseite
- **Ohne echten Kunden:** keine Referenzen, keine Fallstudien, keine Logos.
  Es gilt `Musterprojekt — kein Kundenauftrag`
- **Ohne `KEYWORD_VALIDATION.md`:** Title, H1 und URL einer Launch-Seite sind **nicht bestätigt**

---

## Angrenzende Protokolle

`OFFENE_PRUEFUNGEN.md` hält fest, **was gebaut, aber nicht geprüft** wurde — das ist etwas
anderes als eine offene Entscheidung und gehört nicht hierher.
