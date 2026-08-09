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

**Der Mechanismus dahinter ist arithmetisch, nicht stilistisch:**

> **Widersprüche wachsen quadratisch mit gebundenen Werten.** Bei 147 gebundenen Stellen gibt es
> rund **10.700 Paare**, die sich widersprechen *können*. Jeder gefundene Widerspruch kostet eine
> Sitzung: finden, prüfen, melden, entscheiden. **Und es werden immer neue auftauchen.**

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

> **Frage 4 ist die mit dem besten Verhältnis von Aufwand zu Wirkung.** Ein Satz in `CLAUDE.md`
> — und die 147 Stellen sind ab sofort daran messbar, ohne dass eine einzige umgeschrieben werden
> muss.
