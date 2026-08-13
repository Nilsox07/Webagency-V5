# Startprompt: Vertrauen sichtbar machen, Gerätebild einsetzen, Kleinfehler abräumen

**Stand:** 10.08.2026 · Branch `claude/sartu-concept-review-pdhb5t`
**Zweck:** Alles unterhalb der Trennlinie in eine neue Claude-Code-Session kopieren.

> **Vorher entschieden:** `SARTU_ENTSCHEIDUNGEN_OFFEN.md` **§4c** (Rang 1) — Sperren werden
> datengesteuert statt codegesteuert. **§4b** legt das Aufmacherbild fest.
>
> **Voraussetzung für Block 1:** Die Datei `public/assets/bild/geraet-aufmacher.webp` muss im
> Repo liegen. Sie entsteht außerhalb: Aufnahme des Kundenbereichs in ein Mockup-Werkzeug
> (MockRocket o. ä.), Ergebnis herunterladen, ablegen. **Fehlt sie, überspringt die Session
> Block 1 und meldet das** — sie erfindet kein Ersatzbild.

---

/goal Drei Blöcke: das Gerätebild einsetzen, die Vertrauenslücke schließen und neun geprüfte Kleinfehler abräumen. Grundlage ist SARTU_ENTSCHEIDUNGEN_OFFEN.md §4b und §4c (Rang 1, beide am 10.08.2026 entschieden). Lies die beiden Abschnitte zuerst, dann spezifikation/10_WEBSITE_SARTU.md und 12_ADMINBEREICH.md.

BLOCK 1 — Gerät im Aufmacher
Ersetze den in CSS gezeichneten Laptop durch public/assets/bild/geraet-aufmacher.webp, ein fertig gerendertes Mockup. Liegt die Datei nicht im Repo, überspring diesen Block und melde es — erfinde kein Ersatzbild. Das Bild ersetzt .geraet__deckel samt Telefon und Schattenwerk; lösch den toten CSS-Block, statt ihn liegen zu lassen. Der Vermerk Musteransicht bleibt und steht neben dem Bild, nicht darauf. Miss danach bei 1920, 1440, 1280, 1024 und 390 px: keine waagerechte Überlauf, denn 10_WEBSITE_SARTU.md Sektion 1 verbietet ihn ausdrücklich. Am aktuellen Stand läuft die Seite bei 1024 px über — das gilt als behoben, nicht als bekannt.

BLOCK 2 — Vertrauen, datengesteuert
Bisher wurde nicht gebaut, was ohne Daten leer bliebe. Ab jetzt gilt: bauen, und die Ausgabe an den Daten hängen. Feld leer heisst nichts wird ausgeliefert, Feld gefüllt heisst alles läuft.

a) Vier Felder in operator_settings, im Adminbereich pflegbar: gruender_name, gruender_text, gruender_bild, dazu der Upload dafür. Nutz Uploaddienst; erlaubt sind jpg, png und webp, Grenze wie bei den übrigen Uploads.
b) Sektion Wer dahintersteckt auf der Startseite und eine eigene Seite dazu. Sichtbar nur, wenn Name, Text und Bild vorliegen — sonst entfällt beides wie bisher. Kein Lebenslauf: Name, Bild und ein Absatz, warum es SARTU gibt.
c) LocalBusiness in den strukturierten Daten. Ausgeliefert nur, wenn Strasse, PLZ und Ort in operator_settings gefüllt sind, sonst weiter Organization ohne Adressfeld. Der Klassenkommentar in app/Strukturdaten.php begründet heute das Gegenteil — schreib ihn um, statt ihn stehen zu lassen.
d) Organization um Logo, Gründer und sameAs ergänzen, ebenfalls nur bei gefüllten Feldern.

Musterprojekte und Referenzen bleiben gesperrt (§5, weiterhin offen). Dort ist das Bild der Beleg, und einen Beleg ersetzt kein Feld.

BLOCK 3 — neun geprüfte Kleinfehler
Alle am 10.08.2026 am aktuellen Branch nachgewiesen, keine Vermutungen:
1. Favicon fehlt vollständig — Datei und Verweis.
2. Open Graph und twitter:card fehlen auf allen Seiten.
3. Article-Schema ohne datePublished, ohne Autor, ohne Bild.
4. Die drei Branchenseiten haben null eingehende interne Links, sie stehen nur in der Sitemap. Häng sie in die Navigation.
5. Ratgeber.php Zeile 103 gibt **woraus** als Markdown aus, Lexikon.php Backticks um Domains. Beides landet im Fliesstext und in Meta-Descriptions.
6. Branchenseiten.php Zeile 64 verspricht ab 1.490 Euro eine eigene Seite je Leistung. Das Startpaket hat eine Seite.
7. Branchenseiten.php verlangt dreimal Fakten in einem Gespräch, während Startseite und Metadaten ohne einen einzigen Termin versprechen.
8. websiteband.php Zeile 58 behält aria-label Menü öffnen auch im geöffneten Zustand.
9. Auftragslage: Nur noch wenige Plätze ohne Zeitraum. Ersetz es durch eine überprüfbare Angabe, etwa den nächsten möglichen Projektstart.

Regeln: keine Zahl im Bauteil, wo eine Variable existiert. Kein externer Abruf, kein CDN. Jede neue Route in TenantIsolationTest eintragen, nicht den Test abschwächen. Texte nach SARTU_TEXTREGELN.md und dem Texter-Skill; der Vierschritt gilt je Abschnitt, nicht als Seitenformel.

Fertig, wenn phpunit grün läuft, die fünf Breiten sauber messen und jeder der neun Punkte mit einem Bildschirmfoto oder einer Codestelle belegt ist. Was du nicht ausführen konntest, kommt mit je einer Zeile nach OFFENE_PRUEFUNGEN.md. Branch claude/sartu-concept-review-pdhb5t, committen und pushen.
