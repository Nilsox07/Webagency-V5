# Startprompt: Abnahmeprüfung für die Oberfläche

**Stand:** 14.08.2026 · Branch `claude/sartu-concept-review-pdhb5t`
**Zweck:** Alles unterhalb der Trennlinie in eine neue Claude-Code-Session kopieren.

> **Warum dieser Auftrag vor allen anderen kommt.** In vier Runden kamen behobene Fehler zurück.
> Der Gerätrahmen wurde gelöscht und wiederhergestellt, die Tastatur nie übernommen, die doppelte
> Gattung übersteht drei Runden, eine gemeldete Falschaussage kam in neuem Text zurück.
> **Repariert wird schneller, als festgehalten wird.** Das Vorbild für die Lösung steht im Repo:
> `tests/TenantIsolationTest.php` — seit es ihn gibt, keine einzige Sicherheitsregression.

---

/goal Die Website-Oberfläche bekommt eine Abnahmeprüfung. Nicht mehr Reparaturen — ein Test, der die Reparaturen festhält. In den letzten vier Runden kamen behobene Fehler zurück: der Gerätrahmen wurde gelöscht und wiederhergestellt, die Tastatur nie übernommen, die doppelte Gattung übersteht drei Runden, eine gemeldete Falschaussage kam in neuem Text zurück.

Das Vorbild steht im Repo. tests/TenantIsolationTest.php fährt die vollständige Routenliste ab und schlägt an, sobald eine Route dazukommt, die er nicht kennt. In diesem Projekt hat es keine einzige Sicherheitsregression gegeben, seit es ihn gibt. Für die öffentliche Oberfläche gibt es kein Gegenstück.

Bau tests/OberflaecheTest.php nach demselben Muster. Er läuft gegen den laufenden Webserver und prüft je Adresse:
1. Die vollständige Adressliste ist bekannt. Kommt eine öffentliche Route dazu, die der Test nicht kennt, schlägt er an. Fehlt eine spezifizierte Adresse, ebenso — /foerderung steht in 16_SEO_GEO_SARTU.md mit Priorität 0.9 und liefert heute 404, ohne dass es je gemeldet wurde.
2. Keine waagerechte Überlauf bei 1920, 1440, 1024, 768, 390 und 320 px.
3. Gesamthöhe je Seite unter einer Obergrenze, die du je Adresse einträgst — Startseite 10.000 px, Unterseiten 6.000 px, mobil 390 px höchstens 16.000. Wächst eine Seite darüber, schlägt er an.
4. Jedes Sprungziel hat scroll-margin-top grösser als die Kopfhöhe.
5. Jeder Bildplatz trägt ein Seitenverhältnis, keiner steht auf auto. Heute stehen vier von acht auf auto.
6. Kein Satzanfang öfter als dreimal je Seite, Beschriftungen und Pflichthinweise ausgenommen.
7. Kein Text enthält Markdown-Reste: doppelte Sternchen, Backticks, eckige Klammern ausser den Platzhaltermarken.
8. Keine grammatisch zusammengebaute Nennung: app/services/Musterprojekte.php Zeile 220 setzt sprintf mit dieses plus Gattung und erzeugt sechs falsche Sätze — Später die Startseite dieses Malerbetrieb, dieses Physiotherapiepraxis, dieses Arbeitsrechtskanzlei. Repariere das, indem der Satz je Projekt in den Daten steht, und halt den Test darauf.
9. Keine Lime-Fläche über 40.000 Quadratpixel, Rang 1 Farbsystem Fassung 3.

Danach, und erst danach, diese vier offenen Punkte:
a) Der Gerätrahmen bekommt die aufgeklappte Basis mit Tastenfeld, Leertaste, Trackpad und Vorderkante. design/geraet.html liegt fertig im Repo, im Browser zu öffnen. Ohne die Basis ist ein schräg gestelltes Gerät ein Bildschirm auf einem Stiel — das ist dreimal beanstandet worden.
b) Die Gattung steht doppelt: Später die Startseite dieses Malerbetrieb direkt über der Überschrift Malerbetrieb. Sechsmal insgesamt.
c) Die vier Bildplätze auf auto bekommen ihr Seitenverhältnis.
d) Der Vermerk Musteransicht schwebt frei unter dem Gerät. Er gehört an dessen Rand.

Der Test ist ab jetzt unantastbar wie TenantIsolationTest: nie löschen, nie abschwächen, um grün zu werden. Wer eine Grenze reisst, hebt sie nicht an, sondern behebt die Ursache oder trägt eine Begründung daneben ein.

Branch claude/sartu-concept-review-pdhb5t, committen und pushen.
