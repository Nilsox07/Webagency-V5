# Textprüfung — Vertrauensangaben, Kapazitätszeile, neun Kleinfehler

**Stand:** 13.08.2026 · **Geprüft:** Sektion 6 der Startseite, der Gründerabschnitt auf
`/ueber-uns`, die Kapazitätszeile, der Branchenblock auf `/leistungen`, die neuen
Adminbeschriftungen und zwei Korrekturen auf den drei Branchenseiten
**Anlass:** `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4c und die Fundliste vom 10.08.2026

`sartu-texter`, Klasse 2 seit dem 09.08.2026: die verwendeten Beschriftungen kommen **als
Liste in den Prüfbericht**. Dieser Bericht ist diese Liste, dazu die Zahlen zur laufenden
Prosa.

---

## 1. Sektion 6 — „Wer dahintersteckt"

`10_WEBSITE_SARTU.md` Sektion 6 gibt drei Angaben vor: *Aufgabe*, *Grenze*, *Umfang*.

| | H2 |
|---|---|
| **Aufgabe** | die Person hinter dem Angebot benennen und die Verantwortung über den Launch hinaus zusagen |
| **Grenze** | nie „unser Team", solange eine Einzelperson arbeitet (`06_RECHT.md`). Keine Erfolgsgeschichte |
| **Umfang** | zwei Sätze, zusammen höchstens **neun** Wörter |

> `Eine Person baut Ihre Website. Dieselbe antwortet danach.`

| Maß | Wert | Regel |
|---|---|---|
| Sätze | **2** | zwei Sätze verlangt |
| Wörter | **8** | höchstens neun |
| Nachprüfbares | `eine Person` — eine Zahl | Nachprüftest bestanden |
| „unser Team", „wir als Team", „unser Kollege" | **0** | Grenze eingehalten |
| Erfolgsgeschichte, Werdegang, Titel | **0** | Grenze eingehalten |
| Praxistest | *besteht* — eine Agentur, bei der jemand anderes baut als antwortet, ist der Regelfall | — |

**Der Satz ist nicht neu erfunden.** Er steht im Texter-Skill unter „Kalibrierung" als
geprüfte Fassung für genau diese Stelle und wird dort gegen `Wer hier arbeitet.` gestellt,
das am Nachprüftest scheiterte.

### Der Absatz darunter kommt vom Betreiber

*Aufgabe:* warum diese Person das macht, in Alltagssprache. *Grenze:* kein Lebenslauf, keine
Erfolgsgeschichte, keine Zahlen. *Umfang:* zwei bis drei Sätze.

**Er wird nicht von hier geschrieben.** §4c legt ihn in `operator_settings.gruender_text`. Die
drei Vorgaben stehen als Hinweis am Feld im internen Bereich — dort, wo sie jemand liest,
bevor er tippt:

> `Zwei bis drei Sätze in Alltagssprache: welche Beobachtung dazu geführt hat, dieses Angebot
> zu bauen. Kein Lebenslauf, keine Stationen, keine Zahlen.`

---

## 2. Die Kapazitätszeile

| Vorher | Jetzt |
|---|---|
| `Nur noch wenige Plätze` | `Nächster Projektstart ab <Monat Jahr>` |

| Prüfung | Vorher | Jetzt |
|---|---|---|
| Unbestimmtes Wort an der Stelle, an der etwas Zählbares steht | **`wenige`** — der Skill verwirft es in derselben Reihe wie `passend` und `alles` | keines |
| Nachprüfbar | nein — weder ob es stimmt noch wann es sich ändert | **ja** — ein Monat lässt sich gegen den Kalender halten |
| Wörter | 4 | 5 (plus Monat und Jahr) |
| Quelle der Angabe | keine | `operator_settings.naechster_projektstart` |

**Ohne Datum steht nichts da.** Das ist der Regelfall und in §5a ausdrücklich als vierter
Zustand vorgesehen: „nicht gesetzt — es wird nichts angezeigt."

Bei `ausgebucht` tritt der Monat neben die Aussage, nicht an ihre Stelle:
`Zurzeit ausgebucht — nächster Projektstart ab <Monat Jahr>`.

---

## 3. Zwei Korrekturen auf den drei Branchenseiten

### 3.1 Der Preis stand am falschen Umfang

| | |
|---|---|
| **Vorher** | `… zum Festpreis ab 1.490 € netto — mit einer eigenen Seite je Leistung …` |
| **Der Fehler** | Das Startpaket kostet 1.490 € und hat **1 Seite**. Eine Seite je Leistung beginnt bei Wachstum, **3.900 €** |
| **Jetzt** | zwei Sätze, zwei Zahlen, jede an ihrem Umfang |

Beide Zahlen sind Klasse 1 und stammen aus `Preise::tabelle()` — `149000` und `390000` Cent.
Geschrieben wurde keine.

### 3.2 „in einem Gespräch" widersprach dem Kernversprechen

| | |
|---|---|
| **Vorher** | `Sie liefern die Fakten in einem Gespräch.` — an **fünf** Stellen, nicht an drei |
| **Der Fehler** | Die Startseite und jede Beschreibung sagen `ohne einen einzigen Termin`. Ein Gespräch ist ein Termin |
| **Jetzt** | `Die Fakten liefern Sie im Bedarfsscheck und in Ihrem Kundenbereich, ohne einen Termin.` |

**Der Ersatz beschreibt Gebautes**, keine Absicht: Der Bedarfsscheck läuft, die Fragen im
Kundenbereich sind Stufe A2. `Firmenseitentexte::ARBEITSWEISE` sagt seit dem Bau dasselbe.

> Der Satz `Sprechen können Sie trotzdem mit uns. Sie müssen nur nicht.` bleibt unverändert
> in `Unterseitentexte`. Er ist die Gegenprobe und kein Widerspruch: Das Gespräch ist möglich,
> es ist nur nicht Voraussetzung.

---

## 4. Der Branchenblock auf `/leistungen`

> **H2:** `Drei Branchen haben eine eigene Seite.`

| Maß | Wert | Regel |
|---|---|---|
| Wörter | **6** | höchstens neun |
| Nachprüfbares | `Drei` — eine Zahl, und die Seiten gibt es | Nachprüftest bestanden |
| Konjunktiv | **0** | — |
| Einleitung | **1 Satz, 22 Wörter** | ein Gedanke je Absatz |
| Linktext trägt den Zielbegriff | **ja** — die H1 der Zielseite, nicht „hier klicken" | SEO-Regel 5 |

---

## 5. Beschriftungen — die Liste

**Neu (11):**

| Ort | Beschriftung |
|---|---|
| Startseite, Sektion 6 | `Mehr über SARTU` *(gebunden, Sektion 6)* |
| Betreiberdaten, Überschrift | `Auftragslage` |
| Betreiberdaten, Auswahl | `Nicht gesetzt — es wird nichts angezeigt` · `Freie Kapazitäten` · `Wenig frei` · `Ausgebucht — Warteliste` |
| Betreiberdaten, Feld | `Nächster möglicher Projektstart` |
| Betreiberdaten, Überschrift | `Wer dahintersteckt` |
| Betreiberdaten, Feld | `Name der Person hinter SARTU` |
| Betreiberdaten, Feld | `Warum es SARTU gibt` |
| Betreiberdaten, Feld | `Profilseiten` |
| Betreiberdaten, Überschrift | `Bild der Person hinter SARTU` |
| Betreiberdaten, Feld | `Bilddatei` |
| Betreiberdaten, Schaltfläche | `Bild hinterlegen` · `Bild entfernen` |

| Maß | Wert | Regel |
|---|---|---|
| Schaltflächen ohne Verb | **0** | `Bild hinterlegen`, `Bild entfernen` — Verb zuerst, Gegenstand dabei |
| Beschriftungen, die einen Systemcode nennen | **0** | `gruender_bild` erscheint nirgends in der Oberfläche |
| Systemwörter (`App`, `Dashboard`, `Tool`, `Portal`, `System`) | **0** | — |
| Doppelt vergebene Beschriftungen im selben Formular | **0** | — |
| Leerzustand nennt, wann etwas erscheint | **ja** — `Noch nicht hinterlegt. Ohne Bild entfällt die Sektion „Wer dahintersteckt" vollständig.` | Mikrotextregel |

---

## 6. Was gestrichen wurde

| Was | Warum |
|---|---|
| `aria-label="Menü öffnen"` | Der Name galt in beiden Zuständen und war in einem davon falsch. Der sichtbare Text `Menü` ist jetzt der Name |
| `**woraus**` im Ratgeber | Auszeichnungssprache im Fließtext. Die Betonung steckt jetzt in der Wortstellung: das betonte Wort beginnt den Satz |
| Schrägstriche um zwei Domains im Lexikon | dieselbe Ursache. Die Felder speisen zugleich die Beschreibung für Suchmaschinen |

---

## 7. Die Prüfung nach dem Skill

| Frage | Ergebnis |
|---|---|
| Rechtfertigt das Argument den Preis? | **Ja.** „Eine Person baut, dieselbe antwortet" gilt nicht für eine 500-€-Seite — dort antwortet niemand |
| Steht eine Behauptung über das Verhalten seiner Kunden oder „den Markt" da? | **Nein** |
| Steht die Antwort im ersten Satz? | **Ja**, in allen vier neuen Abschnitten |
| Ist jede Behauptung mit Zahl, Name oder Tatsache belegt? | **Ja.** `eine Person`, `drei Branchen`, `1.490 €`, `3.900 €`, der Monat aus den Betreiberdaten |
| Steht ein Eigenschaftswort, wo eine Zahl stehen könnte? | **Nein** — `wenige` ist genau deshalb entfallen |
| Verspricht ein Bedienelement etwas anderes als die Überschrift darüber? | **Nein** |
| Bleibt beim Streichtest etwas übrig, das niemand vermissen würde? | **Nein.** Der Streichtest hat den Satz `Texte inklusive` aus den drei Beschreibungen entfernt — er stand doppelt zur Aussage über die Seiten |

---

## 8. Was dieser Bericht **nicht** abdeckt

Der Absatz `gruender_text` selbst. Er kommt vom Betreiber, nicht aus dem Bau — die Vorgaben
dafür stehen am Feld. Vor dem Livegang gehört er einmal gegen Sektion 6 gehalten: zwei bis
drei Sätze, kein Lebenslauf, keine Zahlen.
