# Startprompt A: die vier Messfehler abräumen

**Stand:** 13.08.2026 · Branch `claude/sartu-concept-review-pdhb5t`
**Zweck:** Alles unterhalb der Trennlinie in eine neue Claude-Code-Session kopieren.
**Danach:** `PROMPT_NEUE_SESSION_TEXTRUNDE.md` — bewusst getrennt. Wer Struktur und Text in
einem Lauf anfasst, liefert einen Bericht, der das Unangenehme auslässt. Genau das ist am
13.08.2026 passiert.

---

/goal Vier Fehler, alle am 13.08.2026 gemessen, keiner davon im letzten Bericht. Dazu zwei Aufträge, die im letzten Lauf nicht umgesetzt wurden. Kein Text wird umgeschrieben — das ist der nächste Auftrag.

FEHLER 1 — Mobil ist die Seite länger geworden
Gemessen, Startseite, Gesamthöhe vorher gegen jetzt:
1440 px: 12.928 auf 10.185, gut. 768 px: 15.522 auf 14.488. Aber 390 px: 17.745 auf 19.495, und 320 px: 19.550 auf 21.858. Auf dem Telefon sind das rund 24 Bildschirme. Der letzte Auftrag verlangte Messung bei sechs Breiten; die Verschlechterung wurde nicht gemeldet. Finde heraus, was mobil umbricht und dabei wächst — verdächtig sind die dreispaltigen Karten, die Musterprojekt-Karten und die Preistabellen. Ziel: bei 390 px unter 14.000 px, bei 320 px unter 16.000 px.

FEHLER 2 — Alle sechs Sprungziele springen unter den Kopf
header ist position sticky mit 93 px Höhe. Die Ziele muster, preise, ablauf, fragen, leistungen und kundenbereich haben alle scroll-margin-top 0px. Jeder Klick in der Hauptnavigation schiebt die Überschrift hinter den Kopf. Setz scroll-margin-top auf allen Sprungzielen auf die Kopfhöhe plus etwas Luft, aus einer Variablen, nicht als Zahl im Bauteil. Prüf jedes der sechs Ziele einzeln nach dem Sprung.

FEHLER 3 — Acht Bildplätze ohne Seitenverhältnis
Der erste misst 342 mal 427 px; das echte Bild wäre bei 342 px Breite 213 px hoch. Andere sind 42 px hoch. Sobald die Aufnahmen kommen, springt das Layout an acht Stellen. Gib jedem Bildplatz das Seitenverhältnis seines späteren Bildes über aspect-ratio, damit er jetzt schon den Platz einnimmt, den er später braucht.

FEHLER 4 — Die Gattung steht zweimal untereinander
Im Bildplatz steht Malerbetrieb, Umfang Wachstum, direkt darunter die Überschrift Malerbetrieb. Dreimal auf der Startseite, dreimal auf /musterprojekte. Eine der beiden Nennungen fällt weg; der Umfang gehört an die Karte, nicht in den Bildplatz.

Dazu, weil im letzten Lauf nicht umgesetzt:
a) Der Bildplatz zeigt dem Besucher Dateiname und Pixelmaße: [[SCREENSHOT-FEHLT]] sartu-muster-malerbetrieb.webp · 1280 × 800, dreimal untereinander. Die Marke muss im Markup stehen, damit die Startsperre sie findet — sichtbar sein muss sie nicht. Der abgenommene Entwurf schreibt an dieser Stelle Platz für Ansicht und einen Satz, was dort hinkommt. Halt den Test, der die Marke prüft, grün.
b) Aufzählungspunkte: 82 vor dem letzten Lauf, 80 danach. Wo drei Stichworte eine Zeile ergeben, wird es eine Zeile. Ziel: unter 50 auf der Startseite.
c) Füllgrad: fünf Abschnitte liegen bei 34 bis 46 Prozent — Preise, Zusage, SEO, Fragen, Abschluss. Dort ist der Abstand das Problem, nicht der Inhalt.

BERICHTSPFLICHT
Gib am Ende eine Tabelle aus mit sechs Zeilen — 1920, 1440, 1024, 768, 390, 320 px — und je Zeile Gesamthöhe vorher, Gesamthöhe nachher, scrollWidth und Überlaufkandidaten. Dazu Füllgrad je Abschnitt und die Zahl der Aufzählungspunkte, ebenfalls vorher und nachher.

Melde jede Verschlechterung ausdrücklich, auch wenn sie an anderer Stelle aufgewogen wird. Ein Ziel, das du reisst, meldest du als gerissen und schreibst dazu, warum. Der letzte Bericht hat vier gemessene Werte weggelassen; das ist der eigentliche Fehler dieses Laufs gewesen, nicht die Zahlen selbst.

Was du nicht ausführen konntest, kommt mit je einer Zeile nach OFFENE_PRUEFUNGEN.md. Branch claude/sartu-concept-review-pdhb5t, committen und pushen.
