# Startprompt: Gestaltung auf alle Seiten ziehen

**Stand:** 14.08.2026 · Branch `claude/sartu-concept-review-pdhb5t`
**Reihenfolge:** **nach** `PROMPT_NEUE_SESSION_ABNAHME.md`. Der Abnahmetest hält fest, was hier
entsteht — sonst ist es in zwei Runden wieder weg.

---

/goal Die Startseite hat eine Gestaltung, die sechzehn Unterseiten haben keine. Gemessen am 14.08.2026 bei 1440 px. Zieh die Formsprache der Startseite auf alle Seiten und räum drei Dopplungen ab.

BEFUND 1 — Der Ablauf steht dreimal
Auf der Startseite beschreiben beide dunklen Abschnitte denselben Vorgang. Der erste, Ohne einen einzigen Termin zur fertigen Website, listet: Angebot annehmen, Fragen beantworten, Unterlagen hochladen, sehen was ansteht, Vorschau ansehen, Änderungen sammeln. Der zweite, Sie liefern die Fakten, listet: 01 Bedarfsscheck, 02 Angebot, 03 Ihre Angaben, 04 Produktion, 05 Vorschau und Freigabe, 06 Start und Betrieb. Angebot, Angaben, Vorschau und Änderungen kommen in beiden vor. Dazu gibt es /ablauf mit 5.832 px und 465 Wörtern.
Entscheide die Arbeitsteilung und zieh sie durch: Der eine Abschnitt zeigt, was der Kunde im Kundenbereich tut, der andere den Weg vom Bedarfsscheck bis zum Betrieb, und /ablauf trägt die Ausarbeitung. Keine Station darf zweimal erklärt werden.

BEFUND 2 — Kein Schwarz-Weiss-Wechsel ausserhalb der Startseite
Gezählt: Startseite 2 dunkle Abschnitte, alle sechzehn Unterseiten 0. Der Wechsel hell-dunkel-hell ist das, was der Seite Rhythmus gibt. Jede Unterseite bekommt mindestens einen dunklen Abschnitt an einer inhaltlich passenden Stelle — nicht dekorativ eingestreut, sondern dort, wo ein Gedanke Gewicht braucht.
Die Zusage-Form aus dem Entwurf, randlos dunkel mit einem einzigen Satz, ist das stärkste Bauteil des Auftritts und kommt auf siebzehn Seiten kein einziges Mal vor. Nutz sie.

BEFUND 3 — Die Unterseiten haben keinen Aufmacher
Auf der Startseite ist der Aufmacher ein eigenes Bauteil: zweispaltig, Bild rechts, Vertrauenszeile, animierte Diagonalbänder im Hintergrund. Auf jeder Unterseite folgt auf die Brotkrumen direkt die H1 und zwei Knöpfe. Gezählt: 6 Bänder auf der Startseite, 0 auf allen anderen. Deshalb ist der erste Bildschirm dort eine riesige Überschrift und Leerraum.
Bau einen Aufmacher für Unterseiten: Hintergrundbänder, Vorzeile, H1, ein Vorspann von zwei bis drei Zeilen, eine primäre Handlung. Er darf schmaler sein als der der Startseite, aber er muss existieren.

WAS AUSSERDEM AUFFÄLLT, ungefragt gemessen
a) /leistungen trägt 827 Wörter, die vier Leistungsseiten dahinter je 268 bis 308. Der Verteiler ist ausführlicher als die Ziele. Dreh das um: Der Hub verweist, die Zielseite trägt.
b) /ratgeber und /lexikon haben weder dunkle noch Sandflächen — reine weisse Listen, 2.721 und 3.120 px.
c) Auf jeder Seite stehen zwei gleichwertige Knöpfe, Bedarf prüfen lassen und Preise ansehen. Eine Seite führt eine primäre Handlung; die zweite ist ein Textlink.
d) Vier der acht Bildplätze stehen weiter auf aspect-ratio auto.
e) app/services/Musterprojekte.php Zeile 220 erzeugt weiter sechs ungrammatische Sätze: Später die Startseite dieses Malerbetrieb, dieses Physiotherapiepraxis, dieses Arbeitsrechtskanzlei. Der Satz gehört je Projekt in die Daten.
f) Der Gerätrahmen im Aufmacher hat weiter keine aufgeklappte Basis. design/geraet.html liegt fertig im Repo.
g) /foerderung liefert 404, obwohl in 16_SEO_GEO_SARTU.md mit Priorität 0.9 spezifiziert und in 17_SEITEN_SARTU.md mit neun Blöcken ausgearbeitet.

GRENZEN
Keine neue Farbe, kein neuer Radius, keine achte Form. Alles kommt aus design/tokens.css und aus den Bauteilen, die design/startseite.html bereits enthält. Keine Lime-Fläche über 40.000 Quadratpixel. Kein externer Abruf. Bewegung achtet prefers-reduced-motion.

NACHWEIS
Je Seite vorher und nachher: Gesamthöhe bei 1440 und 390 px, Anzahl dunkler Abschnitte, Anzahl Bänder, Füllgrad. Und ein Bildschirmfoto des ersten Bildschirms je Seitengattung. Melde jede Verschlechterung, auch wenn sie anderswo aufgewogen wird.

Branch claude/sartu-concept-review-pdhb5t, committen und pushen.
