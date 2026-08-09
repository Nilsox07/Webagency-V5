# Wie fest darf die Spezifikation sein — geprüft 09.08.2026

> **Anlass:** Der Betreiber fragt, ob das Projekt sich zu weit festgelegt hat und ob nicht eine
> Beschreibung genügt — was gemacht wird, SEO- und GEO-Strategie, Preisliste, das
> Startseiten-HTML, das Portal-Design — und alles andere entscheidet die ausführende KI.
>
> **Diese Datei prüft das und schlägt etwas vor. Sie ändert nichts.**

---

## 1. Ja, die Beobachtung stimmt — und sie ist messbar

| Kennzahl | Wert |
|---|---|
| Zeilen in `spezifikation/` | **4.775** |
| Stellen mit `gebunden` | **147** |
| Reibungspunkte allein in dieser einen Sitzung | **8** |

**Die acht Reibungspunkte, konkret:**

| # | Was passierte | Kosten |
|---|---|---|
| 1 | `§7c` — zwei gebundene Listen nennen verschiedene Branchen, **keine Überschneidung** | blockiert Block 6 aller Branchenseiten bis heute |
| 2 | Block 6 sagt „sofern die Gattung passt", ohne zu sagen, was sonst dort steht | ungelöst |
| 3 | **„sechs Punkte"** in der Hauptnavigation | `/foerderung` musste draußen bleiben |
| 4 | **„drei Stück"** Transparenzseiten zum Launch | der vierte Ratgeberartikel wurde verschoben |
| 5 | **`Thema {n} von 5`** im Bedarfsscheck | ein zusätzliches Feld wurde zur Grundsatzfrage |
| 6 | Gastronomie steht in der Zielgruppe, die Wirtschaftslage trägt das nicht mehr | gemeldet, offen |
| 7 | Setup: **„sechs Schritte"** im Fließtext gegen **„acht"** im Korrekturblock | steht so in `CLAUDE.md` |
| 8 | Ich zitierte das Masterkonzept als bindend, obwohl es Begründungsarchiv ist | musste berichtigt werden |

### Nachgemessen am 09.08.2026 — und das Ergebnis widerlegt die eigene Diagnose

**Der Satz „Widersprüche wachsen quadratisch mit 147 gebundenen Werten" stand hier zuerst. Er war
rhetorisch wirksam und analytisch falsch.** Die Auszählung ergibt:

| Was gezählt wurde | Anzahl |
|---|---|
| Stellen mit `gebunden` insgesamt | 147 |
| davon **gebundene Anzahlen** (Punkte, Stück, Sätze, Wörter, Blöcke …) | **12** |
| davon **ohne Begründung** im Umfeld | **10** |
| davon **echte Auswahlfixierungen**, die wachsen könnten | **rund 4** |

**Die übrigen 135 sind etwas anderes** — und dort ist die Bindung richtig:

| Art | Beispiele | Bindung korrekt, weil |
|---|---|---|
| **Beschriftungen** | `H1: Anmelden` · `Ihr Angebot` · `Übersicht` | Wiedererkennbarkeit; ein Modell würde sie sonst variieren |
| **Rechtstexte und Pflichthinweise** | B2B-Erklärung · vier Pflicht-Bestätigungen | rechtlich, nicht gestalterisch |
| **Beträge und Fristen** | Preise · Login-Token **15 Minuten, einmalig** | Vertrag bzw. Sicherheitsparameter |
| **Feldnamen und Optionslisten** | Bedarfsscheck, Statusnamen | Datenmodell hängt daran |

**Und die zehn Anzahlen ohne Begründung sind größtenteils Umfangsgrenzen** — `42 Wörter`,
`zwei Sätze`, `drei Sätze`. **Dort ist eine Zahl genau richtig:** „kurz" wäre unbrauchbar, „zwei
Sätze" ist prüfbar. Sie sind keine Fehlbindung, sondern der Zweck der Bindung.

> **Es bleiben rund vier Stellen**, an denen eine Zahl eine Auswahl fixiert, die wachsen könnte:
> die **sechs** Navigationspunkte · die **drei** Transparenzseiten zum Launch ·
> `Thema {n} von 5` · die **drei** Musterprojektgattungen.
>
> **Vier. Nicht 147.**

---

## 2. Die eigentliche Ursache: gebundene **Werte** statt gebundener **Gründe**

Fast jede der acht Reibungen hat dieselbe Form. Eine Zahl wurde festgeschrieben, wo eine **Absicht**
gemeint war:

| Gebunden ist | Gemeint war |
|---|---|
| „**sechs** Navigationspunkte" | die Navigation darf nicht überladen wirken |
| „`Thema {n} von 5`" | der Bedarfsscheck bleibt unter drei Minuten |
| „Begründung höchstens **45 Wörter**" | die Begründung darf den Preis nicht erschlagen |
| „**drei** Transparenzseiten zum Launch" | zum Start reichen die Seiten, die wirklich fertig sind |
| „**drei** Musterprojekte, drei Branchenseiten" | Musterprojekt und Branchenseite gehören zusammen |

**Der Unterschied ist entscheidend:**

- Wer den **Grund** bindet, kann jeden neuen Fall daran messen. Eine siebte Navigationszeile ist
  dann eine Abwägung
- Wer den **Wert** bindet, macht aus jeder Änderung einen Regelverstoß — und aus jeder
  Weiterentwicklung eine Grundsatzdiskussion

**Bei Nummer 5 ist das besonders sichtbar:** `Thema {n} von 5` schützt nichts, was
„der Bedarfsscheck bleibt kurz" nicht besser schützen würde. Die Zahl hat die Absicht sogar
verschlechtert — sie erlaubt fünf **beliebig lange** Themen und verbietet ein sechstes kurzes.

---

## 3. Was hart bleiben muss — der wichtigere Teil der Antwort

**Nicht alles ist zu viel.** Fünf Gruppen müssen gebunden bleiben, und zwar aus Gründen, die mit
Modellfähigkeit nichts zu tun haben:

| Gruppe | Beispiele | Warum es keine Lockerung verträgt |
|---|---|---|
| **Zahlen mit Außenwirkung** | 1.490 · 3.900 · 7.900 € · 59 · 129 · 249 € · 19 % USt | Ein abweichender Preis ist ein Vertragsproblem, kein Stilfehler. **Ein Modell, dem die Zahl fehlt, erfindet eine plausible** — das ist keine Schwäche, die neuere Modelle ablegen, sondern die Funktionsweise von Generierung |
| **Pflichthinweise und Rechtstexte** | „erst das geprüfte Angebot ist verbindlich" · Ausschlusszeile BFSG · „keine Rechtsberatung" | Ohne den dritten Satz **wird die Empfehlung zum Angebot.** Das ist die Grenze zwischen Marketing und Willenserklärung |
| **Sicherheits- und Datenregeln** | Mandantentrennung nur aus der Sitzung · CSRF bei jedem POST · SQL nur in `/app/data` · Migrationen ohne Rollback | Das ist der Unterschied zwischen funktionierend und Datenpanne. Hier ist eine kreative Lösung **immer** die schlechtere |
| **Die Nicht-erfinden-Regel** | keine erfundenen Zahlen, Referenzen, Teamfotos, Fallstudien | Sie **ist** die Marke. Ohne sie ist SARTU eine Agentur wie jede andere |
| **Scope-Ausschlüsse** | keine Aufpreisliste · kein CMS · keine Selbstpflege · kein Shop als Standardfall | Sie halten ein Festpreisgeschäft am Leben. Scope-Creep ist die häufigste Todesursache dieses Modells |

> **Diese fünf Gruppen sind vielleicht 5 bis 10 % der 147 gebundenen Stellen.** Der Rest ist
> Kandidat für Lockerung.

---

## 4. Was von Ihrem Vorschlag trägt — und was fehlt

Ihr Vorschlag: eine Beschreibung, SEO/GEO-Strategie, Preisliste, Startseiten-HTML, Portal-Design.

| Teil | Bewertung |
|---|---|
| **Beschreibung, was gemacht wird** | **Trägt.** Geschäftsmodell, Produktumfang, Ausschlüsse — das ist der Kern und passt auf wenige Seiten |
| **Preisliste** | **Trägt, und muss hart bleiben** — siehe Gruppe 1 oben |
| **Startseiten-HTML** | **Der stärkste Teil Ihres Vorschlags.** Ein gebautes Beispiel spezifiziert besser als fünfhundert Zeilen Beschreibung. `design/tokens.css` und die HTML-Entwürfe leisten heute schon mehr als die Prosa daneben |
| **Portal-Design** | dito |
| **SEO/GEO-Strategie** | **Trägt als Prinzip**, nicht als Liste von 32 festen Adressen. „Vergleichend und kommerziell zuerst, keine dünnen Ortsseiten" ist übertragbar — eine Adressliste ist es nicht |

**Was in Ihrem Vorschlag fehlt und dazugehört:**

| Fehlt | Warum es nicht wegkann |
|---|---|
| **Die Sicherheitsregeln** | Mandantentrennung und CSRF stehen in keinem Design und in keiner Preisliste |
| **Die Pflichthinweise** | dito — und sie sind rechtlich, nicht gestalterisch |
| **Die Ausschlussliste** | Ohne sie baut jede Sitzung wieder Shop, Buchung und CMS mit |
| **Die 88 Testfälle** | Sie sind die einzige Stelle, an der „fertig" definiert ist |

> **Ein gebautes Beispiel ist die beste Spezifikation.** Das ist der wertvollste Gedanke in Ihrem
> Vorschlag. Ein Modell kann eine vorhandene Seite lesen und treffen; aus Adjektiven eine
> rekonstruieren kann es nicht zuverlässig.

---

## 5. Zur Modellfrage, Stand August 2026 — ehrlich

**Was heutige Modelle gut können** — und wo Lockerung deshalb gefahrlos ist:

- ein **Prinzip** auf einen neuen Fall anwenden
- innerhalb einer Schranke schreiben, wenn die Schranke benannt ist
- Widersprüche in langen Unterlagen finden — **diese Sitzung ist der Beleg**
- ein vorhandenes Muster fortsetzen, wenn es gebaut vorliegt

**Was sie weiterhin nicht können** — und wo Bindung deshalb bleiben muss:

- **wissen, was sie nicht wissen.** Eine fehlende Zahl wird nicht als Lücke erkannt, sondern
  plausibel gefüllt. Das ist kein Reifegrad, der sich auswächst
- eine Entscheidung rekonstruieren, deren **Grund nirgends steht**. Wo nur das Ergebnis
  dokumentiert ist, wird es früher oder später „verbessert"
- dem Zug zur Standardlösung widerstehen. Ohne ausdrückliches Verbot landet man bei WordPress,
  Baukasten und Aufpreisliste, weil das der Durchschnitt der Trainingsdaten ist

**Daraus die Faustregel:**

> **Fakten und Grenzen binden. Form und Weg freigeben.**
>
> Was falsch werden kann, ohne dass es auffällt, wird gebunden. Was auffällt, sobald man es sieht,
> wird freigegeben.

---

## 6. Vorschlag: drei Stufen statt einer

| Stufe | Was hineingehört | Umfang heute | Ziel |
|---|---|---|---|
| **A — hart gebunden** | Preise, Pflichthinweise, Rechtstexte, Sicherheits- und Datenregeln, Nicht-erfinden-Regel, Scope-Ausschlüsse, die 88 Testfälle | verstreut | **eine Datei**, kurz, vollständig |
| **B — Grund statt Wert** | alles, wo heute eine Zahl eine Absicht kodiert: Navigationsumfang, Themenzahl, Wortgrenzen, Blockreihenfolgen, Seitenzahlen je Launch | der Großteil der 147 | **Absicht hinschreiben, Zahl streichen** |
| **C — freigegeben** | Layout im Detail, Wortlaut außerhalb Klasse 1, welche Seite wann, Reihenfolge der Blöcke, Anzahl der Branchen | heute teils gebunden | **entscheidet die ausführende Sitzung** |

**Ein Beispiel für Stufe B, damit klar ist, was gemeint ist:**

| Heute | Vorschlag |
|---|---|
| „Fortschrittsanzeige als `Thema {n} von 5` — **gebunden**" | „Der Bedarfsscheck bleibt unter drei Minuten und zeigt den Fortschritt in Themen, nicht in Fragen. **Wie viele Themen, entscheidet der Inhalt.**" |
| „die **sechs** Punkte mittig" | „Die Hauptnavigation bleibt in einer Zeile lesbar. Kommt ein Punkt dazu, muss einer weichen oder ins Mobilmenü." |

**Beide Fassungen schützen dasselbe. Die zweite blockiert nichts.**

---

## 7. Was ich nicht empfehle

| Idee | Warum nicht |
|---|---|
| **Alles wegwerfen und neu beschreiben** | Die 4.775 Zeilen enthalten die **Begründungen**. Genau die sind das Wertvolle — und genau die gehen beim Verdichten zuerst verloren. Ohne sie wird dieselbe Diskussion in drei Wochen erneut geführt |
| **Die Bindungen einfach entfernen** | Ein gestrichenes `gebunden` ohne ersetzenden Grund ist schlechter als die Zahl. Dann ist gar nichts geschützt |
| **In einem Durchgang umstellen** | 147 Stellen einzeln zu bewerten ist ein eigenes Vorhaben. **Besser: bei jeder Berührung mit umstellen** — wer eine Stelle anfasst, wandelt sie von Wert auf Grund |

---

## 8. Was zu entscheiden ist

| # | Frage |
|---|---|
| 1 | Wird die Dreistufung übernommen — und wenn ja, entsteht Stufe A als eigene Datei? |
| 2 | Wird Stufe B **schrittweise bei Berührung** umgestellt oder in einem Durchgang? |
| 3 | Bleiben die vier Lastenhefte als Begründungsarchiv liegen? **Empfehlung: ja** — sie tragen die Gründe, die Stufe B braucht |
| 4 | Soll `CLAUDE.md` den Satz bekommen, dass eine Zahl ohne danebenstehenden Grund **kein** bindender Wert ist? Das wäre die kürzeste wirksame Änderung überhaupt |

## 9. Entscheidung zu Frage 4 — nach der Messung: **nicht einbauen**

**Der Betreiber hat den Satz unter eine Bedingung gestellt: nur einbauen, wenn er die Sache
deutlich lockert. Die Messung zeigt, dass er das nicht tut.**

| Dagegen | |
|---|---|
| **Er löst 4 Fälle** | und stellt dafür **147 Bindungen** unter Auslegungsvorbehalt |
| **Er trifft die Falschen** | Beschriftungen, Pflichthinweise und Umfangsgrenzen haben selten ein „weil" danebenstehen — sie würden mitgelockert, obwohl sie bleiben sollen |
| **Er erzeugt Unschärfe, wo heute Klarheit ist** | Die Frage „gilt das noch?" bei jeder Bindung kostet mehr als vier Einzelfälle |

**Stattdessen, und das ist ein Nachmittag statt einer Doktrin:** die vier Stellen einzeln von Wert
auf Grund umstellen.

| Stelle | Heute | Vorschlag |
|---|---|---|
| Hauptnavigation | „die **sechs** Punkte" | „bleibt in einer Zeile lesbar; kommt einer dazu, weicht einer" |
| Transparenzseiten | „zum Launch verbindlich, **drei Stück**" | „zum Launch die, die fertig sind — mindestens die drei genannten" |
| Bedarfsscheck | `Thema {n} von 5` | „Fortschritt in Themen, nicht in Fragen; **unter drei Minuten**" |
| Musterprojekte | „**drei** Gattungen" | „je Branchenseite eines — die Gattungen folgen den Branchen" |

> **Die letzte Zeile löst zugleich `§7c`.** Der Widerspruch entstand nicht aus einer Zahl, sondern
> daraus, dass **zwei Listen unabhängig voneinander gebunden** wurden. Wer die Kopplung benennt
> statt beide Seiten einzeln festzuschreiben, kann sie nicht mehr auseinanderlaufen lassen.

### Was die Messung darüber hinaus zeigt

**Die Überspezifikation ist geringer als beide Seiten dachten.** Die acht Reibungspunkte dieser
Sitzung stammen überwiegend **nicht** aus zu vielen Zahlen, sondern aus:

1. **gekoppelten Listen ohne benannte Kopplung** — `§7c`, Block 6
2. **Rangfolge, die nicht mitgelesen wurde** — mein Fehler mit dem Masterkonzept
3. **Vorgaben, die die Wirtschaftslage überholt hat** — Gastronomie in der Zielgruppe

**Keiner dieser drei Fälle wird durch Lockerung besser.** Fall 1 braucht eine benannte Kopplung,
Fall 2 war Sorgfalt, Fall 3 braucht ein Verfallsdatum an fachlichen Aussagen — nicht weniger
Bindung, sondern **eine andere Art davon**.
