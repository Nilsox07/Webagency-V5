# Textprüfung — Beschriftungen der Bereichsleiste

**Stand:** 10.08.2026 · **Geprüft:** Kundenbereich und interner Bereich, Navigation und
Bedienelemente der neuen Seitenleiste
**Anlass:** Umbau auf das am 03.08.2026 abgenommene Erscheinungsbild, Zeichen freigegeben am
10.08.2026

`sartu-texter`, Klasse 2 seit dem 09.08.2026: *„Wiederkehrende Beschriftungen — Knopftexte,
Navigationspunkte … **innerhalb einer Fassung identisch.** Die verwendeten Beschriftungen
kommen **als Liste in den Prüfbericht** — daran ist Einheitlichkeit prüfbar, ohne den Wortlaut
vorzuschreiben."*

Dieser Bericht ist genau diese Liste.

---

## Was geschrieben wurde — und was nicht

**Es ist keine Prosa entstanden.** Der Umbau hat die Hülle getauscht, nicht den Inhalt. Alle
Fließtexte, Überschriften und Meldungen der Seiten stehen unverändert; sie sind bereits
freigegeben, und der Texter-Skill bindet freigegebenen Text: *„Ein freigegebener Text wird
nicht bei jedem Durchlauf neu erfunden."*

Neu oder verändert sind **zwölf Beschriftungen** und **eine Bildbeschreibung**.

---

## Kundenbereich — die neun Menüpunkte

Die Reihenfolge ist durch §8 gebunden und wurde nicht angetastet. Der Wortlaut ist Klasse 2.

| # | Gruppe | Beschriftung | Ziel | Herkunft |
|---|---|---|---|---|
| 1 | Projekt | `Übersicht` | `/portal` | §8, unverändert |
| 2 | Projekt | `Angebot` | `/portal/angebot` | §8, unverändert |
| 3 | Projekt | `Aufgaben` | `/portal/aufgaben` | §8, unverändert |
| 4 | Projekt | `Vorschau` | `/portal/vorschau` | §8, unverändert |
| 5 | Verwaltung | `Rechnungen` | `/portal/rechnungen` | §8, unverändert |
| 6 | Verwaltung | `Domain` | `/portal/domain` | §8, unverändert |
| 7 | Verwaltung | **`Inhalte`** | `/portal/inhalte` | **geändert** — stand auf `Öffnungszeiten` |
| 8 | Verwaltung | `Vertrag` | `/portal/vertrag` | §8, unverändert |
| 9 | Kontakt | `Hilfe` | `/portal/hilfe` | §8, unverändert |

**Punkt 7 ist die einzige Änderung.** Entscheidung des Betreibers vom 03.08.2026: Das Menüwort
ist `Inhalte` (§8), die **Überschrift der Seite** bleibt `Öffnungszeiten` (§8.7). Das ist keine
Doppelung, sondern Arbeitsteilung — die Navigation nennt den Ort, die Seite die Handlung.

## Interner Bereich — zehn Punkte in drei Gruppen

| Gruppe | Beschriftungen |
|---|---|
| `Arbeit` | `Übersicht` · `Anfragen` · `Projekte` · `Nachrichten` |
| `Betrieb` | `Rechnungen` · `Belege` · `Betreiberdaten` · `Rechtstexte` |
| `Werkzeug` | `Ersteinrichtung` (nur solange die Startsperre greift) · `Testmail` |

Alle zehn standen wörtlich schon im alten Kopfband. **Neu sind allein die drei Gruppenwörter**
— `Arbeit`, `Betrieb`, `Werkzeug`. Sie benennen, wonach die Punkte sortiert sind, nicht was sie
tun; deshalb je ein Substantiv und kein Satz.

## Bedienelemente

| Element | Beschriftung | Prüfung |
|---|---|---|
| Schaltfläche in beiden Leisten | `Abmelden` | Verb, benennt die Handlung. Unverändert aus beiden Bereichen |
| Sprungmarke | `Zum Inhalt springen` | Verb zuerst, nennt das Ziel. Aus dem Websitebereich übernommen — **derselbe Wortlaut in allen drei Bereichen** |
| Bildbeschreibung des Logos | `SARTU` | Beschreibt das Bild, nicht das Suchwort. Ein `alt="Logo"` sagt dem Vorleseprogramm nichts über die Marke |

---

## Die Zahlen

| Maß | Wert | Regel |
|---|---|---|
| Beschriftungen insgesamt | **22** (9 Kunde + 10 intern + 3 Gruppenwörter) | — |
| davon neu formuliert | **3** (die Gruppenwörter des internen Bereichs) | — |
| davon geändert | **1** (`Öffnungszeiten` → `Inhalte`) | Entscheidung 03.08.2026 |
| Beschriftungen mit mehr als einem Wort | **0** | Navigationspunkte tragen ein Wort, sonst zerfällt die Spalte |
| Längste Beschriftung | `Betreiberdaten`, 14 Zeichen | passt in die Leiste (17 rem) ohne Umbruch |
| Doppelt vergebene Beschriftungen **innerhalb** eines Bereichs | **0** | Klasse-2-Auflage: innerhalb einer Fassung identisch **und** eindeutig |
| Gleichlautende Beschriftungen **zwischen** den Bereichen | **3** — `Übersicht`, `Rechnungen`, `Abmelden` | zulässig und gewollt: dieselbe Sache heißt gleich |
| Systemwörter in einer Beschriftung | **0** | `App`, `Dashboard`, `Tool`, `Portal`, `System` kommen nicht vor |
| Schaltflächen ohne Verb | **0** | — |

## Die Prüfung nach dem Skill

| Frage | Ergebnis |
|---|---|
| Verspricht ein Bedienelement etwas anderes als die Überschrift darüber? | **Nein.** `Inhalte` führt auf die Seite mit der Überschrift `Öffnungszeiten` — der Punkt ist als Arbeitsteilung entschieden und oben begründet |
| Steht ein Systemcode in einer Beschriftung? | **Nein** |
| Ist der Wortlaut innerhalb einer Fassung identisch? | **Ja** — jede Beschriftung steht an genau einer Stelle im Quelltext, in `kundenband.php` bzw. `kopfband.php` |
| Steht ein Eigenschaftswort, wo ein Substantiv hingehört? | **Nein** |

---

## Was dieser Bericht **nicht** abdeckt

Die Texte **innerhalb** der Seiten — Überschriften, Fließtext, Meldungen, Leerzustände. Sie
sind unverändert und in `TEXTPRUEFUNG_WEBSITE.md` bzw. beim jeweiligen Bau geprüft. Der Umbau
hat sie nicht angefasst.
