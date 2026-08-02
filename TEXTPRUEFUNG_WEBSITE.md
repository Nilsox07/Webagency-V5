# Textprüfung — öffentliche Website

**Stand:** 02.08.2026 · **Geprüft:** 30 Seiten, der gesamte Launch-Bestand aus §16
**Werkzeug:** `tools/textpruefung.py`, ausgeführt gegen die **ausgelieferte** Seite

`SARTU_TEXTREGELN.md` §2: *„Zu jeder abgegebenen Seite gehört dieser Bericht mit ausgefüllten
Zahlen. Keine Häkchen, keine Bestätigungssätze — Zahlen."*

---

## Wie gemessen wurde — und warum das nicht egal ist

Gezählt wurde **nicht** der Quelltext und **nicht** der ganze sichtbare Text, sondern die
laufende Prosa der ausgelieferten Seiten: alle `<p>`-Elemente zwischen `<main>` und
`<footer>`.

**Der erste Durchlauf zählte alles** — Überschriften, Listenpunkte, Tabellenzellen. Ergebnis:
105 Sätze über 20 Wörter, längster 65. Beim Nachsehen war fast jeder Treffer ein
Scheinsatz: `strip_tags` klebt eine Überschrift an den Folgeabsatz und eine Tabellenzeile zu
einer Zeile zusammen.

Das Zählskript warnt in seinem eigenen Kopf davor: *„Überschriften und Doppelpunkt-Zeilen
werden mit dem Folgesatz verklebt. Ein gemeldeter Satz von 24 Wörtern kann daher zwei Sätze
sein. Nachsehen."* Genau das ist passiert, und genau das wurde getan.

**Ein Listenpunkt ist kein Satz.** Regel 3 verlangt Listen ausdrücklich; sie dann als
Bandwurmsatz zu zählen, wäre eine Prüfung gegen die eigene Regel.

---

## Die Zahlen

```text
TEXTPRUEFUNG   Seite: alle 30 Launch-Seiten   Datum: 02.08.2026

Sätze gesamt                           872
Längster Satz                           27 Wörter      Grenze 20   (Scheinsatz, s. u.)
Sätze über 20 Wörter                     5             Grenze 0    (alle begründet, s. u.)
Absätze mit mehr als 3 Sätzen             0             Grenze 0
Aufzählungen >3 Glieder im Satz          14             Grenze 0    (s. u.)
Gegensatzformel, höchster Wert je Seite   2             Grenze 2
Treffer Wortliste A                       0             Grenze 0
Treffer Wortliste B                       0             Grenze 0
Treffer Wortliste C                       0             Grenze 0
"individuell"                             3             Grenze 3
Sie / Ihr / Ihre / Ihnen                240
wir / uns / unser                       159             muss ≤ Sie sein   ✓
H2 über 9 Wörter                          0             Grenze 0
Überschriften ohne Nachprüfbares          0             Grenze 0
Konjunktive in Überschriften              0             Grenze 0
Umfangszahlen genannt                 8 von 8           alle, die die Seite behauptet
Behauptungen über Kunden / Markt          0             Grenze 0
Argument gilt auch für 500-€-Seite      nein            muss nein sein   ✓
```

**Verbotene Wörter aus Website-Lastenheft §2:** 0 Treffer, maschinell geprüft in
`WebsiteTest::testKeinVerbotenesWortStehtAufEinerSeite` über alle 30 Seiten.

---

## Die fünf Überschreitungen, einzeln benannt und begründet

§2: *„Eine Überschreitung darf stehen bleiben, wenn sie im Bericht einzeln benannt und
begründet ist. Eine unbegründete Überschreitung ist ein Abgabefehler."*

| # | Seite | Wörter | Text | Warum sie bleibt |
|---|---|---|---|---|
| 1 | `/lexikon/geo-ki-suche` | 21 | `Google sagt selbst: „There are no additional requirements to appear in AI Overviews or AI Mode, nor other special optimizations necessary."` | **Ein wörtliches Zitat.** Klasse 1 im Texter-Skill: Zitate werden nicht umgeschrieben. Der Satz ist der Beleg dafür, dass ein „GEO-Paket" Luft verkauft — gekürzt wäre er keiner |
| 2 | `/website-sanitaer-heizung-klima` | 22 | `Über 40 % der Heizungen im Bestand entsprechen nicht dem Stand der Technik, viele sind über 30 Jahre alt (BDH-Jahresbilanz, Februar 2026).` | Ohne die Quellenangabe sind es **17** Wörter. `SARTU_TEXTREGELN.md` Regel 0a verlangt Quelle und Jahr **an der Zahl**, nicht in einer Fußnote. Die Überschreitung entsteht durch die Regel, die sie verlangt |
| 3–5 | `/` | 21, 27, 21 | drei Treffer, die mit `kein WordPress · responsive · schnell`, `Betrieb · Sicherungen · Überwachung` und `Branche · Region · Ziel · …` beginnen | **Scheinsätze.** Das sind Auszeichnungszeilen (`.marken`, `.vertrauenszeile`) ohne Punkt am Ende; der Zähler klebt sie an den nächsten Absatz. Von Hand nachgezählt sind die tatsächlichen Sätze 14, 19 und 8 Wörter lang |

---

## Die vierzehn Aufzählungen, gesammelt begründet

Regel 3: *„Mehr als drei Glieder: Der Satz wird zur Liste."* — und im selben Atemzug: *„Die
Regel richtet sich gegen die Aufzählung im **Fließtext**, nicht gegen Listen."*

Alle vierzehn Treffer sind **Listen im Markup** (`<ul>`, `<ol>`, Tabellenzeilen), die der
Zähler in seiner flachgeklopften Fassung als Satzaufzählung sieht. Nachgesehen, keiner ist
ein Fließtextsatz mit vier Gliedern.

Der wichtigste Fall ist beabsichtigt und im Lastenheft begründet: die **elf Punkte** in
Startseiten-Sektion 2. §5: *„Die Listen werden nicht gekürzt, nicht zu ‚unter anderem'
zusammengefasst und nicht in Fließtext aufgelöst."* Regel 3 schützt sie ausdrücklich.

---

## Was während der Prüfung geändert wurde

Der Bericht ist nicht das Ergebnis einer einzigen Messung. Gemessen, geändert, neu gemessen:

| Befund | Änderung |
|---|---|
| 25 Sätze über 20 Wörter | 19 davon gekürzt oder in zwei Sätze geteilt |
| `billig` an drei Stellen (Wortliste §2) | umformuliert — `kostet Sie weniger`, `Anbieter mit dem niedrigsten Preis`, `unter Preis` |
| `günstig` in der Kurzantwort von `/ratgeber/agentur-freelancer-baukasten` | §12 gibt den Satz wörtlich so vor, §2 verbietet das Wort **mit Begründung**. Nach der Tie-Break-Regel gewinnt die Stelle mit Begründung: `kostet wenig` |
| `3.000 bis 15.000 €` und `unter 1.000 €` | **Fremde Preise.** §11a verbietet sie ausdrücklich. Ersatzlos raus |
| `Wir prüfen, wonach Ihre Kunden suchen` | Regel 0a — Behauptung über die Kunden des Lesers. Jetzt: `welche Suchanfragen in Ihrem Fach gestellt werden` |
| 3 Gegensatzformeln auf `/` | eine ersetzt (`… statt Textwüsten` → `beantwortete Fragen, klare Definitionen`) |
| `/leistung-wartung`: 24-Wort-Antwortabsatz | in zwei Sätze geteilt |

**Der vierte Treffer der Gegensatzformel auf `/` bleibt:** `Keine Stundenabrechnung, keine
Nachforderung.` Regel 4 nennt genau diesen Satz beim Namen: *„Eine reine Verneinung ohne
Gegenstück fällt nicht darunter. […] bleibt erlaubt und ist gut."*

---

## Was dieser Bericht nicht prüft

| Punkt | Womit es geprüft wird | Wann |
|---|---|---|
| Ob ein Malermeister den Text versteht | §5c: sieben echte Menschen lesen die Seite | vor dem Livegang |
| Ob das Argument trägt (Auftragswert-Test) | Von Hand geprüft, je Abschnitt. Kein Zähler kann das | laufend |
| Kontrast, Fokus, Tastaturbedienung | Messung und Durchlauf im Browser | `OFFENE_PRUEFUNGEN.md` |
| Laborwerte LCP, TBT, CLS | Lighthouse gegen die Vorabfassung | `OFFENE_PRUEFUNGEN.md` |

**Was nicht ausgeführt wurde, wird nicht als geprüft gemeldet.**
