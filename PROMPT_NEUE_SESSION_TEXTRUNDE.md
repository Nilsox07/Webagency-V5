# Startprompt B: die Textrunde

**Stand:** 13.08.2026 · Branch `claude/sartu-concept-review-pdhb5t`
**Zweck:** Alles unterhalb der Trennlinie in eine neue Claude-Code-Session kopieren.
**Vorher:** `PROMPT_NEUE_SESSION_MESSFEHLER.md` abarbeiten. Diese Runde fasst **kein** Layout an.

---

/goal Eine reine Textrunde. Kein Layout, kein CSS, keine Struktur — nur Wortlaut. Gebunden ist SARTU_TEXTREGELN.md und der Skill sartu-texter; der Vierschritt gilt je Abschnitt, nicht als Seitenformel.

BEFUND 1 — Satzanfänge wiederholen sich
Gemessen am 13.08.2026: /musterprojekte hat 85 Sätze und nur 51 verschiedene Satzanfänge. Das Wort Eine eröffnet neun davon: Eine Suchmaschine hat dasselbe Problem · Eine eigene Seite je Leistung · Eine Praxis mit drei Behandlungsräumen · Eine zweite Seite müsste · Eine Adresse, feste Zeiten · Eine Seite, die Leistung, Zeiten · Eine Seite mit vier Abschnitten · Eine Seite trägt Leistung, Zeiten · Eine Kanzlei mit vier Anwälten. In der Physiotherapie-Karte fangen drei von vier Zeilen mit Eine an.
Auf der Startseite: Wir zehnmal, Sie neunmal, Eine sechsmal, Alle fünfmal — 30 von 190 Sätzen mit vier Wörtern.
Ziel: kein Satzanfang öfter als dreimal je Seite. Miss es nach, statt es zu schätzen.

BEFUND 2 — zu viel Text an zwei Stellen
Die Startseite ist von 1.029 auf 1.287 Wörter gewachsen, /musterprojekte hat 896. Für drei Musterprojekte ist das viel, und die beiden Seiten sagen dasselbe zweimal: Die Karten auf der Startseite und die ausführlichen Fälle auf der Übersichtsseite tragen dieselben vier Angaben.
Entscheide die Arbeitsteilung und zieh sie durch: Die Startseite zeigt den Fall in einem Satz und schickt weiter, die Übersichtsseite trägt die Ausarbeitung. Ziel: Startseite zurück unter 1.100 Wörter, /musterprojekte unter 700. Keine Aussage geht verloren, nur ihre Dopplung.

BEFUND 3 — eine fachlich falsche Behauptung ist wieder eingebaut
Auf /musterprojekte steht: Eine Suchmaschine hat dasselbe Problem: Sie kann einer Seite ein Thema zuordnen, nicht vier. Das stimmt so nicht — Suchmaschinen erfassen mehrere Themen und einzelne Passagen einer Seite. Dieselbe Aussage war schon einmal gemeldet und ist in neuem Text zurückgekommen.
Der Vorteil eigener Leistungsseiten lässt sich ohne diese Behauptung begründen: eigener Anfrageweg, eigene Bilder, einzeln bewerbbar, eigene Überschrift und Adresse. Such im übrigen Bestand nach derselben Aussage in anderer Formulierung und räum sie mit ab.

BEFUND 4 — Aussagen über fremdes Kaufverhalten
Regel 0a verbietet erfundene Aussagen über die Kunden des Kunden oder über den Markt. Sätze wie Wer nach Fassadensanierung sucht, sucht nach Fassadensanierung und nicht nach einem Malerbetrieb behaupten Suchverhalten, das nirgends belegt ist. Prüf alle drei Musterprojekte und die drei Branchenseiten darauf. Was bleibt, muss der Leser selbst bestätigen können — der Nickentest aus dem Skill.

WAS NICHT ANGEFASST WIRD
Preise, Beträge, Fristen, Pflichthinweise, Rechtstexte, Status- und Feldnamen. Sie sind Klasse 1 und gebunden. Ebenso die vier Aussagen der Positionierung — der Wortlaut ist frei, die Aussage nicht.

PRÜFBERICHT
Zu jeder geänderten Seite eine Tabelle: Wörter vorher und nachher, Anzahl Sätze, Anzahl verschiedener Satzanfänge, die drei häufigsten Anfänge mit Zahl. Dazu die Liste der Beschriftungen, die du geändert hast — innerhalb einer Fassung müssen sie identisch bleiben.

Was du nicht ausführen konntest, kommt mit je einer Zeile nach OFFENE_PRUEFUNGEN.md. Branch claude/sartu-concept-review-pdhb5t, committen und pushen.
