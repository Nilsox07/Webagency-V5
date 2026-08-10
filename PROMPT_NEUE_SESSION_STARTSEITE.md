# Startprompt: die Startseite auf den abgenommenen Entwurf ziehen

**Stand:** 10.08.2026 · Branch `claude/sartu-concept-review-pdhb5t`
**Zweck:** Alles unterhalb der Trennlinie in eine neue Claude-Code-Session kopieren.
**Länge:** unter 4.000 Zeichen — die Zielumgebung begrenzt eine `/goal`-Bedingung darauf.

> **Vorher entschieden, sonst hält die Session an.** `SARTU_ENTSCHEIDUNGEN_OFFEN.md` **§4b**
> (Rang 1) legt das Aufmacherbild fest und schärft, was „nachgebaute Oberfläche" heißt.
> `spezifikation/10_WEBSITE_SARTU.md` ist in Sektion 1 und 2 nachgezogen. Ohne diese beiden
> Einträge widerspräche der Auftrag einer gültigen Vorgabe.

---

/goal Zieh die öffentliche Startseite auf den abgenommenen Entwurf design/startseite.html. Gemessen wurde am 10.08.2026 bei 1440 px: die Abschnittsfolge stimmt, der Aufmacher nicht. Baue die neun Abweichungen unten ab und weise jede mit einem Bildschirmfoto nach.

Lies zuerst: SARTU_ENTSCHEIDUNGEN_OFFEN.md §4b (Rang 1, legt das Aufmacherbild fest) · spezifikation/10_WEBSITE_SARTU.md Sektion 1 und 2 · design/startseite.html, Zeilen 770–860 (der Aufmacher) · design/tokens.css. Der Entwurf ist die Vorlage für Aufbau, Flächen und Dichte — jeder Text darin ist Platzhalter.

Das Aufmacherbild, entschieden am 10.08.2026:
Statt des gestrichelten Bildplatzes steht dort ein Gerät in leichter Schrägstellung — Laptop mit angeschnittenem Telefon davor. Der Rahmen wird selbst gezeichnet: CSS-Perspektive plus Inline-SVG, keine gekaufte oder heruntergeladene Vorlage, kein externer Abruf. Auf dem Bildschirm steht eine echte Aufnahme des eigenen Kundenbereichs mit Musterdaten, abgelegt als WebP unter public/assets/bild/. Kein [[SCREENSHOT-FEHLT]] mehr an dieser Stelle. Der Vermerk Musteransicht bleibt und steht am Bild. Für Musterprojekte und Gründerfoto bleibt der Bildplatz unverändert — dort ist das Bild der Beleg.

Die neun Abweichungen:
1. Aufmacherbild wie oben.
2. Der Lime-Akzent auf dem letzten Teil der H1 fehlt. Er kann nicht existieren, weil die H1 eine escapte Konstante ist. Trenn sie in zwei gebundene Teile — Satzanfang und hervorgehobener Abschluss — und setz den zweiten in ein eigenes Element. Der Wortlaut bleibt unverändert.
3. Lime als Textmarker auf Links fehlt. Rang 1, Farbsystem Fassung 3: Text in --ink, Lime als background-image mit background-size dahinter, beim Überfahren auf volle Höhe. Kein text-decoration underline. website.css enthält heute null background-image.
4. Die Pfeile in beiden Knöpfen fehlen.
5. Die animierten Diagonalbänder hinter dem Aufmacher fehlen (im Entwurf .swell und .band). Sie müssen prefers-reduced-motion achten.
6. Die Trennlinie über der Vertrauensliste fehlt.
7. Aufzählungszeichen sind Mittelpunkte statt Halbgeviertstrichen.
8. Das Logo wird nirgends eingebunden. Es liegt unter public/assets/bild/. Kopf und Fuß setzen SARTU als Text. 07_MARKE_UND_GESTALTUNG.md verlangt Zeichen und Wortmarke.
9. Die Hauptnavigation bricht bei 1920, 1440 und 1024 px auf zwei Zeilen. Sie gehört in eine.

Regeln, die dabei gelten:
Keine Zahl im Bauteil, wo eine Variable existiert — tokens.css zuerst. Keine achte Radienform. Lime nie als Schriftfarbe auf hellem Grund, jede Lime-Fläche dort mit 1px --line als Kante. Kein externer Abruf, kein CDN, keine Build-Pipeline. Alle Kernabläufe funktionieren mit deaktiviertem JavaScript — die Bänder sind Zierde und dürfen nichts tragen.

Nachweis statt Behauptung:
Render den gebauten Stand und den Entwurf im selben Browser bei 1440 px und leg die Bilder nebeneinander. Byteweise gleiche tokens.css und gleiche Abschnittsfolge sind kein Nachweis, dass die Seite übertragen ist — genau dieser Schluss war am 10.08.2026 falsch. Trag in OFFENE_PRUEFUNGEN.md je eine Zeile ein für das, was du nicht ausführen konntest.

Fertig, wenn alle neun Punkte abgebaut sind, phpunit grün läuft und der Aufmacher neben dem Entwurf steht, ohne dass ein Unterschied erklärt werden muss. Branch claude/sartu-concept-review-pdhb5t, committen und pushen.
