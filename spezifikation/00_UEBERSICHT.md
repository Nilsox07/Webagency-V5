# Spezifikation — Wegweiser

**Ein Thema, eine Datei, eine Wahrheit.** Steht ein Wert in einer Datei, steht er in keiner
zweiten. Wo ein anderes Thema ihn braucht, verweist es hierher statt ihn zu wiederholen.

Stand 03.08.2026 · Aufbau nach `KONSOLIDIERUNG_RUNDE1.md`

---

## Welches Thema wo

### Gemeinsame Grundlage — gilt für Produkt **und** eigene Seite

| Datei | Inhalt | Stand |
|---|---|---|
| `01_GESCHAEFTSMODELL.md` | Modell, Positionierung, USP-Einordnung, Zielgruppe | **fertig** |
| `02_PREISE_UND_ZAHLUNG.md` | Preistabelle, Rundum-Schutz S/M/L, Zahlungsplan, E-Rechnung | **fertig** |
| `06_RECHT.md` | Recht, Vertrag, Ehrlichkeitsregeln | **fertig** |
| `07_MARKE_UND_GESTALTUNG.md` | Farbe, Form, Abstände, Logo, Schrift | **fertig** |
| `08_TEXTREGELN.md` | Sprache, verbotene Wörter, Prüfbericht | **fertig** |

### Teil 1 — was der **Kunde** kauft

| Datei | Inhalt | Stand |
|---|---|---|
| `03_KUNDENPRODUKT.md` | Leistungsumfang, Scope-Schutz, Kundenablauf, Designprinzipien Kundenwebsites | **fertig** |
| `04_DOMAIN_HOSTING_MAIL.md` | Domainregeln, Domain-Schutzregel, Hosting, E-Mail | **fertig** |
| `05_SEO_GEO.md` | SEO-/GEO-Startsystem — **Kundenleistung** | **fertig** |

### Teil 2 — was **SARTU für sich selbst** baut

| Datei | Inhalt | Stand |
|---|---|---|
| `10_WEBSITE_SARTU.md` | `sartu.de` — technischer Rahmen, Navigation, alle zehn Sektionen, übrige Seiten | **fertig** |
| `11_KUNDENBEREICH.md` | Navigation, Anmeldung, Statuslogik, Uploads, Zahlungen | **fertig**, Screen-Details noch in der Quelle |
| `12_ADMINBEREICH.md` | Zugang, Screens, Projekt-Arbeitsplatz, Audit | **fertig** |
| `13_DATENMODELL.md` | 20 Tabellen, Typabbildung, Formate, Konventionen | **fertig** |
| `14_SICHERHEIT.md` | Architektur, dreizehn eiserne Regeln, Ersteinrichtung, Migrationen | **fertig** |
| `15_TESTFAELLE.md` | Verteilung, Mandantentrennung, Anmeldung, Ausführung | **fertig**, Restgruppen in der Quelle |
| `16_SEO_GEO_SARTU.md` | Suchintentionen, Launch-Reihenfolge, was nicht gebaut wird | **fertig** |

### Teil 3

| Datei | Inhalt | Stand |
|---|---|---|
| `20_OFFEN.md` | offene Entscheidungen, Platzhalter, daraus folgende Sperren | **fertig** |

> **Alle Themendateien stehen.** Wo eine Datei noch feldgenaue Restvorgaben in ihrer Quelle
> lässt, ist das in ihrem Schlussabschnitt benannt — es entsteht keine stille Lücke.

---

## Was hier **nicht** hineingehört

| | warum |
|---|---|
| `CLAUDE.md`, `UEBERGABE_DATEILISTE.md`, `REIHENFOLGE.md`, `CODEX_*`, `PROMPT_*`, `LIVEGANG.md`, `ENTWICKLUNGSUMGEBUNG.md`, `BAUFREIGABE.md`, `MODELLPLAN.md` | sagen, **wie gearbeitet** wird, nicht was gebaut wird |
| `OFFENE_PRUEFUNGEN.md`, `IMPLEMENTATION_*`, `MESSUNGEN.md`, `STAND.md`, `ABSCHLUSSBERICHT.md`, `TEXTPRUEFUNG_WEBSITE.md`, `KEYWORD_VALIDATION.md` | halten fest, **was passiert ist** — werden fortgeschrieben |
| `.claude/skills/sartu-texter/` | ist ein **Werkzeug**, kein Dokument |

**Die Rangfolge bei Widersprüchen bleibt in `UEBERGABE_DATEILISTE.md`** — sie steht laut
`CLAUDE.md` allein dort und wird nicht dupliziert.

---

## Dublettenregister

Wo dieselbe Sache mehrfach stand, hier die Auflösung. **Nur geprüfte Fälle.**

> Die Werte in diesem Register sind **Belege für die Entscheidung**, keine zweite Quelle. Wer
> einen Preis ändert, ändert ihn in `02_PREISE_UND_ZAHLUNG.md` — hier steht nur, warum die alte
> Fassung verworfen wurde.

### Aufgelöst

| Sache | Fassungen | gilt | warum |
|---|---|---|---|
| **Paketpreise** | `1.290 / 2.990 / 5.990` (alt) · `Basis/Pro/Platin/Enterprise` (alt) · `1.490 / 3.900 / 7.900 / ab 12.500` | **die neue** | Das Masterkonzept erklärt die alten selbst für „veraltet und ungültig". Geprüft: Masterkonzept, Website-Lastenheft und `AUDIT_VOR_BAUBEGINN.md` führen **identische** Zahlen — hier gab es keinen echten Widerspruch, nur historischen Ballast |
| **Akzentfarbe** | `#a3e635` (12 ×) · `#BDDD4A` (Logozeichen) · `#ABC957` (Logozusatz) | **`#a3e635`** | „Eine Akzentfarbe". Das Designsystem verwendet sie hundertfach, das Logo einmal. Die abgelegten Logodateien behalten ihre Originalfarben als Markenfassung für Druck |
| **Kundenbereich vs. Portal** | Website-Lastenheft §7 sagte `Portal und Freigaben`, Außensprache sagt `Kundenbereich` | **`Kundenbereich`** | Das Portal-Lastenheft führt `Portal` unter „nach außen nie verwenden"; die Startseite schreibt zehnmal `Kundenbereich` und dreimal `Portal` |
| **GEO in den Leistungen** | Seitenübersicht: `Sichtbarkeit (SEO/GEO)` · §7-Tabelle: nur `SEO-Grundlage` | **mit GEO** | Masterkonzept führt das SEO-/GEO-Startsystem als „in jedem Paket enthalten", und der USP nennt „SEO-/GEO-Basis ab Start". §7 ist am 03.08.2026 nachgezogen |

### Offen — zwei Fassungen mit je einer Begründung

| Sache | Fassungen | Lage |
|---|---|---|
| **`Leistungen` in der Navigation** | §5b sagt an einer Stelle „bleibt in der Hauptnavigation statt im Fußbereich", zwei Absätze später „wandert in den Fußbereich" | Beide tragen eine Begründung. Gebaut nach der Punkteliste unter „Dies ist die einzige gültige Navigation". **Entscheidung nötig** |
| **Anzahl Setup-Schritte** | Fließtext „sechs", Korrekturblock „acht" | `CLAUDE.md` entscheidet: der Korrekturblock gewinnt, weil er begründet ist |

---

## Regel für alles Weitere

Taucht ein Wert künftig in zwei Themendateien auf, ist **eine** davon falsch. Die Datei, die
das Thema besitzt, behält ihn; die andere bekommt einen Verweis. Wer eine Zahl ändert, ändert
sie **hier im Verzeichnis genau einmal**.
