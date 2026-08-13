# Startprompt: Sektion 6 und 8, drei Musterprojekte, Höhe abbauen

**Stand:** 13.08.2026 · Branch `claude/sartu-concept-review-pdhb5t`
**Zweck:** Alles unterhalb der Trennlinie in eine neue Claude-Code-Session kopieren.

> **Vorher entschieden:** `SARTU_ENTSCHEIDUNGEN_OFFEN.md` **§4d** (Rang 1) — beide Sektionen
> kommen mit Platzhalter hinein, drei Musterprojekte werden gebaut, §7c ist gegenstandslos.
> Ohne diesen Eintrag würde die Session an `10_WEBSITE_SARTU.md` §8 anhalten, und zwar zu Recht.

**Der Auftrag hat zwei Teile.** Block 1 bis 3 sind eine Session. **Block 4 — die drei
Beispielseiten selbst — ist eine eigene Session**, er steht hier nur, damit die Struktur dazu
passt. Wer alles in einen Lauf packt, liefert vier halbe Sachen.

---

/goal Bau Sektion 6 und 8 der Startseite, die Seite /musterprojekte, und nimm der Startseite ein Drittel Höhe. Grundlage ist SARTU_ENTSCHEIDUNGEN_OFFEN.md §4d (Rang 1, 13.08.2026). Lies den Abschnitt zuerst, dann spezifikation/10_WEBSITE_SARTU.md §8 und 17_SEITEN_SARTU.md §4a.

BLOCK 1 — Sektion 8, Musterprojekte
Der Aufbau steht fertig in design/startseite.html, Abschnitt id="muster", Zeilen 1056 bis 1105. Übernimm ihn: Vorzeile, H2, Vorspann, drei Karten, darunter der Knopf auf /musterprojekte. Je Karte eine Fahne Musterprojekt — kein Kundenauftrag, ein Bildplatz, ein Titel und vier Zeilen: Ausgangslage, Empfohlen, Seitenstruktur, Sie liefern. Die drei Fälle sind Malerbetrieb mit Umfang Wachstum, Physiotherapiepraxis mit Umfang Start, Arbeitsrechtskanzlei mit Umfang Platzhirsch. Die Inhalte stehen im Entwurf und sind fachlich richtig; der Wortlaut ist frei, die Aussage nicht. Die Bildplätze tragen [[SCREENSHOT-FEHLT]] mit Massangabe, bis die Beispielseiten stehen.

BLOCK 2 — Sektion 6, Wer dahintersteckt
Die Felder gruender_name, gruender_text und gruender_bild gibt es bereits in operator_settings. Bau die Sektion so, dass sie die Felder zeigt, wenn sie gefüllt sind, und sonst einen gekennzeichneten Platzhalter mit [[FOTO-FEHLT]]. Kein Lebenslauf: Name, Bild und ein Absatz, warum es SARTU gibt.

Zu beiden Blöcken: Die Startsperre sucht [[FOTO-FEHLT]] und [[SCREENSHOT-FEHLT]] und bricht die produktive Veröffentlichung ab. Prüf, dass sie das für beide neuen Sektionen wirklich tut, und schreib je einen Testfall dafür. Ohne diesen Nachweis ist der Platzhalter ein Risiko statt einer Zwischenstufe.

BLOCK 3 — Höhe abbauen, ohne Wörter zu streichen
Gemessen am 13.08.2026 bei 1440 px: 12.928 px Gesamthöhe, 14,4 Bildschirme, aber nur 1.029 Wörter. Die Höhe kommt aus Leerraum und Wiederholung, nicht aus Text. Zwei neue Sektionen kommen jetzt dazu — die Seite muss trotzdem kürzer werden.
a) Die zwei dunklen Abschnitte bleiben zwei. Der Wechsel hell-dunkel ist Rhythmus. Sie sind zusammen 4.258 px für 251 Wörter — kürz den Inhalt, nicht die Anzahl.
b) Von vier Portalansichten bleiben zwei: das Gerät im Aufmacher und eine im Ablauf. sartu-portal-aufgaben.webp ist allein 1268 mal 793 px. Vier Ansichten beweisen nicht mehr als eine gute, kosten aber vierfache Höhe.
c) Das Feld karte--betont ist 1268 mal 476 px in --lime-soft und verstösst gegen Rang 1, Farbsystem Fassung 3: keine vollflächigen Lime-Bänder, Lime bleibt auf Knöpfe, Badges, Textmarker und kleine Blöcke. Setz die sieben Ein-Wort-Punkte als Zeile.
d) Fünf Abschnitte haben 25 bis 36 Prozent Füllgrad — Preise, Zusage, SEO, Fragen, Abschluss. Dort ist der Abstand das Problem. Zieh sie enger.
e) 82 Aufzählungspunkte auf einer Seite sind zu viele. Wo drei Stichworte eine Zeile ergeben, wird es eine Zeile.
Ziel: unter 9.000 px bei 1440 px, mit beiden neuen Sektionen. Kein Wort wird gestrichen, um das zu erreichen.

Ausserdem, gemessen und noch offen: bei 320 px läuft die Seite waagerecht über, scrollWidth 373. Ab 1024 px abwärts ragt A.knopf über die Fensterkante.

Nachweis: miss vorher und nachher bei 1920, 1440, 1024, 768, 390 und 320 px und gib Gesamthöhe, Füllgrad je Abschnitt und Überlauf als Tabelle aus. Keine Behauptung ohne Zahl. Was du nicht ausführen konntest, kommt mit je einer Zeile nach OFFENE_PRUEFUNGEN.md. Branch claude/sartu-concept-review-pdhb5t, committen und pushen.

---

## Block 4 — die drei Beispielseiten selbst (**eigene Session**)

Nicht in denselben Lauf packen. Der Umfang ist eine vollständige Website je Fall, gebaut mit
demselben System wie eine Kundenwebsite — das ist zugleich der erste Belastungstest des
Produktionswegs.

**Vorher zu klären, sonst hält die Session an:**

| Frage | Warum sie offen ist |
|---|---|
| **Wo liegen die Beispielseiten?** | Eigene Adressen unter `/musterprojekte/malerbetrieb`, eigene Subdomain oder eigenes Deployment? `16_SEO_GEO_SARTU.md` kennt sie nicht |
| **Werden sie indexiert?** | Drei erfundene Betriebe im Index sind ein Risiko — mindestens `noindex` prüfen |
| **Wie werden sie gekennzeichnet?** | Auf **jeder** Seite, nicht nur auf der Übersicht. Ein Muster, das für echt gehalten wird, ist eine Irreführung nach § 5 UWG |

**Erst wenn das entschieden ist, lohnt der Prompt dafür.**
