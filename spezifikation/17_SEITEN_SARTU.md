# Die übrigen Seiten von `sartu.de`

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 05.08.2026 aus `CLAUDE_SARTU_WEBSITE_LASTENHEFT_BAUFINAL.md` §6 bis §15.
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

> **Startseite, technischer Rahmen und Navigation:** `10_WEBSITE_SARTU.md`.
> Indexierung, Schema und Sitemap-Priorität je Seite: `16_SEO_GEO_SARTU.md`.
> Was mit einer abgeschickten Anfrage passiert: `09_ANFRAGEEINGANG.md`.

**Notation wie in `10_WEBSITE_SARTU.md`:** *Aufgabe* · *Grenze* · *Umfang*, und **kein
Beispielsatz**. Gebunden sind Adressen, Zahlen, Pflichthinweise, Schaltflächen, Feldnamen und
Umfangsgrenzen — **nicht** Titles, Metas, H1 und Fließtext. Die schreibt
`.claude/skills/sartu-texter/`.

---

## 1. Die drei Hauptseiten

Sie hängen an der Hauptnavigation und beantworten je eine der drei Fragen, mit denen ein Betrieb
auf die Seite kommt: **was bekomme ich, was kostet es, wie läuft das ab.**

**Für alle drei gilt:** genau eine `H1` · **Antwortabsatz oben** — die ersten 40–60 Wörter
beantworten die Titelfrage, bevor der Leser scrollt · CTA-Band am Ende · alle Zahlen aus
`02_PREISE_UND_ZAHLUNG.md`, **nie eigenständig gepflegt**.

### 1.1 `/leistungen` — **Umfang 700–850 Wörter**

**Aufbau `gebunden`:** Aufmacher → `Kurz gesagt` → Leistungslandkarte → was der Kunde **nicht**
entscheiden muss → wie tief es je Lösung geht → drei häufige Fragen → CTA-Band

| Block | *Aufgabe* | *Grenze* |
|---|---|---|
| **Aufmacher** | klarmachen, dass es **ein Ergebnis** ist und kein Bündel einzelner Leistungen | nicht die acht Zeilen vorwegnehmen |
| **`Kurz gesagt`** | die Leistungen als **ein** Festpreisergebnis benennen **und** wer die Tiefe entscheidet: SARTU im Angebot, nicht der Kunde im Bestellformular | **45 Wörter** |
| **Leistungslandkarte** | **dieselben acht Zeilen wie auf der Startseite**, hier mit **je 3–4 Sätzen** statt einem, plus Link auf die Leistungsseite | Titel und Tags dürfen von `10_WEBSITE_SARTU.md` **nicht abweichen** |
| **Wie tief je Lösung** | Tabelle Start / Wachstum / Platzhirsch / Sonderprojekt **mit Ergebnis-Spalte** | **keine Häkchenmatrix.** Ein Häkchen sagt nicht, was herauskommt |

**Liste „was Sie nicht entscheiden müssen" — `gebunden`, sieben Punkte:** System und Technik ·
Seitenzahl · Designstil · SEO-Stufe · Hosting · Registrar · Wartungsminuten

**Die drei Fragen — Einwände `gebunden`:** Einzelne Leistungen dazubuchen · später mehr brauchen ·
bestehende Websites übernehmen

### 1.2 `/preise` — **Umfang 650–800 Wörter**

**Die wichtigste Seite der Website.** Sie trägt den einzigen Sichtbarkeitsvorteil gegenüber
etablierten Agenturen — **veröffentlichte, überprüfbare Zahlen** (`16_SEO_GEO_SARTU.md`).

**Aufbau `gebunden`:** Aufmacher → Preisübersicht → **Erstjahrestabelle** → was jedes Projekt
enthält → Rundum-Schutz → Domain und E-Mail → Zahlung → **sechs** häufige Fragen → CTA-Band

| Block | *Aufgabe* | *Grenze* |
|---|---|---|
| **Aufmacher** | sagen, dass der Kunde **kein Paket auswählt**, und den Weg beschreiben: Bedarfseinschätzung zeigt die wahrscheinliche Lösung, SARTU prüft persönlich | nie „Paket wählen", „konfigurieren", „Extras" — `08_TEXTREGELN.md` |
| **Preisübersicht** | **Platzhirsch groß**, Start und Wachstum kompakt, Sonderprojekt als Abzweig | die Empfehlung nicht verstecken und nicht aufdrängen |
| **Erstjahrestabelle** | Einmalpreis, Monatsbetrag und **Erstjahreswert** je Lösung | **tabellarische Ziffern**, mobil waagerecht scrollbar. Zahlen aus `02_PREISE_UND_ZAHLUNG.md` |
| **Rundum-Schutz** | Framing zwingend als **Entlastung**, nicht als Wartungspaket | siehe Pflichtsatz auf `/leistung-wartung`, Abschnitt 3 |
| **Domain und E-Mail** | Inhaberschaft, enthaltene Gebühr, Schutz der bestehenden E-Mail | Werte aus `04_DOMAIN_HOSTING_MAIL.md` |
| **Zahlung** | Zahlungsplan je Lösung, Zahlungsziel, Zahlung im Kundenbereich, **Produktionsslot erst nach erster Zahlung** | Werte aus `02_PREISE_UND_ZAHLUNG.md` |

**Rundum-Schutz, dreigeteilt und `gebunden`:**

| | Inhalt |
|---|---|
| **enthalten** | Hosting · SSL · Backups · Monitoring · technische Updates · technische Suchgesundheit · Formularprüfung · Zugang zum Kundenbereich |
| **nicht enthalten** | unbegrenzte Text- oder Designänderungen · neue Seiten · neue Ziele |
| **selbst pflegbar** | Öffnungszeiten · Kontaktdaten · vorhandene Einträge · Seitenstatus |

**Die sechs Fragen — Einwände `gebunden`:** netto oder brutto · später erweitern · warum der
Betrieb verpflichtend ist · versteckte Kosten · Domainkosten · Sonderfunktionen

**Bildregel:** **keine großen Fotos.** Preise brauchen Scanbarkeit. Erlaubt ist eine kleine
Musteransicht der Zahlungsseite — **ohne** realistische Rechnungsnummern und ohne echte Namen.

### 1.3 `/ablauf` — **Umfang 700–850 Wörter**

**Aufbau `gebunden`:** Aufmacher → Vergleichstabelle → **acht** Schritte → Ansichten aus dem
Kundenbereich → was der Kunde tun muss → was SARTU entscheidet → Zeitrahmen → CTA-Band

**Aufmacher** — *Aufgabe:* sagen, dass Standardprojekte digital und gebündelt laufen, **und im
selben Atemzug**, dass ein Gespräch jederzeit möglich, aber nicht Pflicht ist. *Grenze:* die
Erreichbarkeit **muss** vorkommen — sonst entsteht „nicht erreichbar".

**Vergleichstabelle, zwei Spalten — die fünf Gegenüberstellungen sind `gebunden`:**

| klassisches Projekt | bei SARTU |
|---|---|
| Erstgespräch als Pflichttermin | Bedarfsscheck in wenigen Minuten |
| Kickoff-Termin und Fragebogen | der Kundenbereich übernimmt bekannte Fakten und fragt nur die Lücken |
| Feedback verteilt über E-Mails und Anrufe | gebündeltes Feedback an einer Stelle |
| viele offene Entscheidungen beim Kunden | Struktur, Design und Technik entscheidet SARTU |
| der Preis wird im Verlauf konkret | Festpreis steht vor dem Auftrag |

> **Grenze: keine Prozentangaben.** Über den Zeitaufwand klassischer Projekte liegt SARTU keine
> eigene Messung vor — eine Zahl daneben wäre erfunden.

**Acht Schritte, je 2–3 Sätze — die Stationen sind `gebunden`:** Bedarfsscheck · unsere Prüfung ·
Angebot im Kundenbereich · Annahme und erste Zahlung · Domain und Einrichtung · Produktion mit
Prüfung · Vorschau, Feedback, Abnahme · Launch und Betrieb

> **Warum hier acht Schritte stehen und auf der Startseite sechs.** Dieselbe Strecke, zwei
> Auflösungen. **Beide Listen sind richtig und werden nicht angeglichen** — wer sie
> vereinheitlicht, macht die eine zu lang oder die andere zu grob. Geprüft am 05.08.2026,
> Schritt für Schritt:
>
> | `/ablauf` | Startseite |
> |---|---|
> | Bedarfsscheck | Bedarfsscheck |
> | unsere Prüfung **+** Angebot im Kundenbereich | Geprüftes Angebot |
> | **Annahme und erste Zahlung** | *— kommt dort nicht vor* |
> | Domain und Einrichtung | Ihre Angaben |
> | Produktion mit Prüfung | Produktion |
> | Vorschau, Feedback, Abnahme | Vorschau und Freigabe |
> | Launch und Betrieb | Start und Betrieb |
>
> **Die Reihenfolge stimmt überein. Dass die Startseite die Zahlung nicht als Schritt führt, ist
> entschieden und kein Versehen** (05.08.2026): Gezahlt wird je nach Plan **zwei- oder dreimal**,
> ein einzelner Kasten wäre in zwei von drei Fällen falsch. Begründung in `10_WEBSITE_SARTU.md`.
>
> **Hier ist die Zahlung dagegen ein eigener Schritt**, weil `/ablauf` die ausführliche Fassung
> ist und den Zahlungsplan ohnehin erklären muss. **Der Schritt nennt den Plan, nicht einen
> Zeitpunkt** — *Grenze:* keine Formulierung, die nach **einer** Zahlung klingt.

**Was der Kunde tun muss — `gebunden`, fünf Punkte:** Fakten bestätigen · Material hochladen ·
Domain bestätigen · Vorschau prüfen · freigeben

**Was SARTU entscheidet — `gebunden`, acht Punkte:** Empfehlung · Seitenstruktur · Nutzerführung ·
Design · SEO-Grundlage · Technik · Hosting · DNS-Plan

**Zeitrahmen** — *Aufgabe:* die Lieferkorridore je Lösung nennen **und wann die Frist zu laufen
beginnt**: erst wenn Zahlung, Briefing und Material vorliegen. *Zahlen:*
`03_KUNDENPRODUKT.md`. *Grenze:* **der Startzeitpunkt darf nicht fehlen** — sonst ist die Frist
eine Zusage ab Anfrage.

**Fünf Ansichten aus dem Kundenbereich** mit Bildunterschrift: Angebot · Briefing · Domain ·
Vorschau und Feedback · Pflege. Maße und Regeln: Abschnitt 10.

---

## 2. `/briefing` — der Bedarfsscheck

**Der Bedarfsscheck ist der einzige Weg zu einem Angebot.** Was danach passiert — Annahme,
Spamabwehr, Herkunft, Löschfristen — steht in `09_ANFRAGEEINGANG.md`.

**Gebunden:** Adresse `/briefing` · Fortschrittsanzeige als `Thema {n} von 5` (**nicht**
„Frage 1 von 10") · Knopf `Bedarf prüfen lassen`

**H1** — *Aufgabe:* die Frage stellen, die der Bedarfsscheck beantwortet. *Grenze:* keine
Aufforderung, kein „Jetzt". *Umfang:* **eine Frage, höchstens acht Wörter**

### 2.1 Einstiegsbildschirm

**Vorspann** — *Aufgabe:* dem Leser die Angst vor Fachfragen nehmen: er muss **weder Paket noch
Seitenzahl, Designrichtung noch SEO-Stufe kennen** — und danach sagen, was er bekommt.
*Grenze:* keine Dauer erfinden, die nicht gemessen ist. *Umfang:* **zwei Sätze**

**Vertrauenspunkte — `gebunden`, fünf Stück:**
`Dauert etwa 3 Minuten` · `Preis vor Kontaktdaten` · `Kein Pflichttermin` ·
`Keine Auswahl von Zusatzoptionen` · `Unverbindlich bis zum geprüften Angebot`

### 2.2 Die Felder

**Feldnamen, Typen, Pflichtstatus und Optionslisten sind `gebunden`** — sie bestimmen, was
gefragt wird. **Label, Hilfetext und Fehlermeldung werden geschrieben.**

- *Aufgabe je Label:* die Frage in Alltagssprache stellen
- *Aufgabe je Hilfetext:* **ein Beispiel** aus dem Betriebsalltag geben, keine Definition
- *Aufgabe je Fehlermeldung:* sagen, **was zu tun ist** — `11_KUNDENBEREICH.md`, Fehlerform
- *Grenze:* kein Fachwort im Label. Keine Frage, die der Interessent nicht beantworten kann

**Thema 1 — Ihr Unternehmen**

| # | fragt nach | Typ | Pflicht |
|---|---|---|---|
| 1.1 | dem Angebot des Unternehmens | Textfeld, 1–3 Sätze | ja |
| 1.2 | dem Hauptarbeitsgebiet | Text (Ort oder PLZ) | ja |
| 1.3 | einem größeren Einzugsgebiet | Text | nein |
| 1.4 | einer bestehenden Website | Auswahl: `Ja` · `Nein` · `Bin unsicher` | ja |
| 1.5 | deren Adresse — **erscheint nur bei `Ja`** | URL | ja, bedingt |

**Thema 2 — Ihr Ziel**

| # | fragt nach | Typ | Pflicht | Optionen (`gebunden`) |
|---|---|---|---|---|
| 2.1 | dem Hauptziel der neuen Website | eine Wahl | ja | Mehr passende Anfragen · Besser gefunden werden · Neue Mitarbeitende gewinnen · Vertrauen und Professionalität stärken · Termine oder Bewerbungen vereinfachen · Etwas anderes |
| 2.2 | der Zielgruppe | eine Wahl | ja | Privatkunden · Unternehmen · Bewerberinnen und Bewerber · Mehrere Gruppen · Noch unklar |

> Der Hilfetext zu 2.1 muss den Leser auf **einen Zeitraum** festlegen, sonst kreuzt er alles an.

**Thema 3 — Umfang**

| # | fragt nach | Typ | Pflicht | Optionen (`gebunden`) |
|---|---|---|---|---|
| 3.1 | den Merkmalen des Unternehmens | **mehrere** Wahlen | ja | Wir haben ein klares Hauptangebot · Wir bieten mehrere eigenständige Leistungen an · Wir arbeiten in mehreren Regionen oder an mehreren Standorten · Wir suchen regelmäßig Mitarbeitende · Projekte, Referenzen oder Neuigkeiten sollen aktuell bleiben · Nichts davon / bin unsicher |

**Regel `gebunden`:** `Nichts davon / bin unsicher` ist **nicht** mit anderen Optionen
kombinierbar. Die Fehlermeldung dazu wird geschrieben — *Grenze:* sie muss **beide** erlaubten
Wege nennen, nicht nur den Fehler.

**Thema 4 — Besondere Anforderungen (die Gates)**

| # | fragt nach | Typ | Pflicht | Optionen (`gebunden`) |
|---|---|---|---|---|
| 4.1 | besonderen Funktionen | **mehrere** Wahlen | ja | Normale Anfrage oder Bewerbung über ein Formular · Einfache Terminbuchung · Produkte verkaufen oder Zahlungen annehmen · Kundenlogin oder geschützter Bereich · Verbindung zu anderer Software · Mehrere Sprachen oder getrennte Marken · Besondere Daten oder ein formaler Nachweis · Nichts davon, eine normale Firmenwebsite |

**Je Option ein Beispielsatz als Hilfetext.** Dieselbe Kombinationsregel wie bei 3.1.

> Diese Frage entscheidet über **Sonderprojekt** und über die BFSG-Pflichtprüfung im Angebot
> (`11_KUNDENBEREICH.md`). Sie darf nicht gekürzt werden.

**Thema 5 — Domain und Termin**

| # | fragt nach | Typ | Pflicht | Optionen (`gebunden`) |
|---|---|---|---|---|
| 5.1 | dem Domainstatus | eine Wahl | ja | Domain vorhanden · Neue Domain nötig · Bin unsicher |
| 5.2 | einem festen Termin | eine Wahl | ja | Nein, der normale Zeitrahmen passt · Ja |
| 5.3 | Datum und Grund — **nur bei `Ja`** | Datum + Text | ja, bedingt | — |
| 5.4 | dem, was auf keinen Fall übersehen werden darf | Textfeld | nein | — |

> Der Hilfetext zu 5.3 muss klarstellen: **ein Wunschdatum ist noch keine Zusage.** Die
> Machbarkeit wird im Angebot bestätigt. Ohne diesen Satz entsteht eine Zusage aus einem
> Formularfeld.

### 2.3 Ergebnis **vor** den Kontaktdaten

**Das ist die Stelle, an der SARTU sich vom Markt unterscheidet: der Preis kommt vor der
Adresse.**

- *Aufgabe:* die Empfehlung nennen **und aus den Antworten begründen** — welche Angaben zu
  dieser Stufe geführt haben
- *Grenze:* **keine Paketwechsel-Knöpfe, keine Zusatzoptionen, keine SEO-Auswahl.** Der Kunde
  wählt nicht aus, er bekommt eine Empfehlung
- *Umfang:* **Begründung höchstens 45 Wörter**
- *Zahlen `gebunden`:* Einmalpreis, Monatsbetrag und **Erstjahreswert** aus
  `02_PREISE_UND_ZAHLUNG.md`

**Pflichthinweis darunter, `gebunden`:** die **lange** Fassung aus `08_TEXTREGELN.md`,
**einschließlich des dritten Satzes**, der nur hier hinzutritt — dass erst das geprüfte Angebot
verbindlich ist. **Ohne ihn wird die Empfehlung zum Angebot.**

**Knopf `gebunden`:** `Empfehlung unverbindlich prüfen lassen`

**Zwei Sonderfälle:**

| Fall | *Aufgabe des Texts* | *Grenze* |
|---|---|---|
| **Sonderprojekt-Gate** | sagen, dass eine besondere Funktion dabei ist, den **Einstiegspreis** nennen und ankündigen, was folgt: ein kurzes Fachmodul, dann ein geprüfter Gesamtpreis | den Einstiegspreis als **Untergrenze** kennzeichnen, nicht als Preis |
| **Unklarheit** | sagen, dass der Bedarf voraussichtlich in eine der drei Lösungen passt und **höchstens eine gebündelte Rückfrage** kommt | nicht nach Unsicherheit klingen — die Rückfrage ist der Standard, keine Ausnahme |

### 2.4 Kontaktdaten — erst danach

| Feld | Pflicht |
|---|---|
| Vor- und Nachname | ja |
| Unternehmen | ja |
| geschäftliche E-Mail-Adresse | ja |
| Telefon | **nein** |
| bevorzugter Kontaktweg: `E-Mail` \| `Portal` | ja |
| **B2B-Erklärung** — `gebunden`: `Ich handle für mein Unternehmen bzw. in Ausübung meiner beruflichen oder gewerblichen Tätigkeit.` | ja |
| Datenschutzbestätigung mit Link auf `/datenschutz` | ja |

**Kein Newsletter-Häkchen. Keine Pflicht-Telefonnummer.** Honigtopf und serverseitige Prüfung:
`09_ANFRAGEEINGANG.md`.

> Die Fehlermeldung zur B2B-Erklärung muss **den Grund nennen** — SARTU arbeitet ausschließlich
> für Unternehmer. Ohne Grund liest sie sich wie eine Schikane.

### 2.5 Verhalten

- **Zwischenstand wird gesichert**, Wiederaufnahme möglich
- **Zurück jederzeit, ohne Datenverlust**
- Bei **Einfach**auswahl darf automatisch weitergeblättert werden, bei **Mehrfach**auswahl **nicht**
- Fehler **am Feld**, nicht als Sammelmeldung oben; **das erste fehlerhafte Feld bekommt den Fokus**
- Mobil: ein Sachverhalt je Bildschirm, Knöpfe in Daumenreichweite, **passender Tastaturtyp**
  (E-Mail, Telefon, Zahl)

### 2.6 Bedienbarkeit ohne JavaScript — verbindlich, keine Kann-Regel

**Der Bedarfsscheck darf an abgeschaltetem JavaScript nicht scheitern.** Gebaut wird **zuerst**
die Fassung ohne JavaScript; der Komfort kommt darüber.

| | **ohne JavaScript** — muss funktionieren | **mit JavaScript** — Komfort obendrauf |
|---|---|---|
| Schritte | echte Seiten `/briefing/1` … `/briefing/n`, je Schritt ein `POST`, Server antwortet mit dem nächsten | dieselben Schritte ohne Neuladen |
| Zwischenstand | serverseitig in einer kurzlebigen Sitzung — **Ablauf 24 Stunden**, nur Formulardaten, **keine Kennung im Klartext in der Adresse** | zusätzlich im Browserspeicher |
| Zurück | normaler Link auf den vorigen Schritt, Angaben bleiben | ohne Neuladen |
| Bedingte Fragen | **der Server** entscheidet, welcher Schritt folgt | dieselbe Regel im Browser |
| Ergebnis | eigene Seite, **serverseitig berechnet** | gleiche Anzeige |
| Fehler | Neuanzeige des Schritts, Meldung am Feld, Fokus auf das erste fehlerhafte | ohne Neuladen |
| Fortschritt | `Schritt {n} von {m}` als Text | zusätzlich ein Balken |

> **Die Empfehlungsregel liegt auf dem Server.** Der Browser darf sie spiegeln, aber die
> verbindliche Berechnung erfolgt serverseitig — sonst weichen beide Fassungen voneinander ab.

**Kein Ersatz durch „Schreiben Sie uns einfach eine E-Mail".** Eine Kontaktalternative steht
zusätzlich da, **ersetzt den Bedarfsscheck aber nicht.**

### 2.7 Danke-Seite

- *Aufgabe:* Eingang bestätigen · persönliche Prüfung und schriftliche Antwort zusagen ·
  ankündigen, dass **höchstens eine gebündelte Rückfrage** kommt · sagen, was danach kommt
  (Angebot mit Empfehlung, Seitenstruktur, Festpreis, Zahlungsplan, Zeitrahmen)
- *Grenze:* **kein weiteres Angebot, kein Upsell, keine Zusatz-CTA**
- *Umfang:* **drei Sätze**

---

## 3. Die fünf Leistungsseiten

**Gemeinsamer Aufbau, `gebunden`, in dieser Reihenfolge:**

`H1` → `Kurz gesagt` → `Für wen das passt` → `Was enthalten ist` → `Was nicht enthalten ist` →
`Was es kostet` → `Wie es abläuft` → `Welche Entscheidung wir Ihnen abnehmen` → **drei** häufige
Fragen → CTA

**Umfang je Seite: 450–650 Wörter.** `Kurz gesagt` ist ein **Antwortabsatz von 40–60 Wörtern mit
Preisanker** — er beantwortet die Suchanfrage, bevor der Leser scrollt.

| # | Adresse (`gebunden`) | Thema | was `Kurz gesagt` tragen muss |
|---|---|---|---|
| 1 | `/leistung-webdesign` | Webdesign und Programmierung | individuell aus dem Designsystem programmiert · **Preisanker** · **ohne WordPress, ohne Baukasten, ohne Plugins zum Pflegen** |
| 2 | `/leistung-texte` | Website-Texte | Kunde liefert Stichpunkte und Fakten, SARTU schreibt · **ausgeschlossen: erfundene Belege, ungeprüfte Fachaussagen, Rechtstexte** |
| 3 | `/leistung-seo-lokal` | SEO-Grundlage und lokale Sichtbarkeit | was ab Tag eins eingebaut ist: Seitenthemen, Metadaten, strukturierte Daten, interne Verlinkung, echte Unternehmensdaten · **ohne Rankinggarantie, ohne dünne Ortsseiten** |
| 4 | `/leistung-wartung` | Rundum-Schutz | **Monatspreis** · Hosting, SSL, tägliche Backups, Monitoring, technische Updates, Suchgesundheit, Zugang zum Kundenbereich · **kein Änderungsminuten-Konto** |
| 5 | `/leistung-portal` | der Kundenbereich | Angebot, Zahlung, Briefing, Dateien, Domain, Vorschau, Freigabe und spätere Pflege an einem Ort · **Layout, Code und Adressen bleiben bei SARTU** |

**Pflichtsätze — `gebunden`, dürfen nicht fehlen und nicht umformuliert werden:**

| Seite | Satz |
|---|---|
| 3 | `Rankings, Anfragen oder Nennungen in KI-Systemen kann niemand garantieren.` |
| 3 | `Wir erstellen keine Ortsseiten, bei denen nur der Stadtname ausgetauscht ist.` |
| 4 | `Der Rundum-Schutz bezahlt Betrieb, Sicherheit und Verantwortung — er ist keine unbegrenzte Text- oder Design-Flatrate.` |
| 2 | `Rechtstexte wie Impressum, Datenschutz und AGB sind nicht enthalten; wir binden freigegebene Texte technisch ein.` |

**Seite 5** trägt zusätzlich die beiden Listen `Sie können` / `Sie müssen nicht` — **derselbe
Funktionsumfang wie in Sektion 2 der Startseite** (`10_WEBSITE_SARTU.md`), damit beide Seiten
nicht auseinanderlaufen.

**Auf Stufe 2 verschoben:** eine eigene Seite `/leistung-domain-launch` sowie die Aufteilung von
Seite 3 in getrennte SEO- und Local-SEO-Seiten.

---

## 4. Branchenseiten — vollständige Zielseiten, keine Durchgangsstationen

### Warum das eine harte Grenze ist

Google verbietet in seinen Spam-Richtlinien Seiten, die *„lead users to **intermediate pages**
that are not as useful as the final destination"* — ausdrücklich auch *„pages targeted at
specific regions or cities that **funnel users to one page**"*.

**Das Kriterium ist: Durchgangsstation oder Ziel.** Eine Seite, auf der ein Malermeister alles
erfährt und **direkt beauftragen kann**, ist keine Durchgangsstation. Damit fällt der wichtigste
Teil des Vorwurfs weg.

**Was bleibt, ist das zweite Kriterium:** *„substantially similar pages"*. Dagegen hilft **kein
Formular. Nur Inhalt.**

> **Die Bedingung in einem Satz:** Jede Branchenseite ist eine vollständige Zielseite mit
> eingebettetem Bedarfsscheck — **und** enthält mindestens **400 Wörter, die auf keiner anderen
> Seite der Website stehen.**

### Aufbau je Seite

Der Besucher muss die Website **nicht verlassen und nicht weiterklicken.**

| # | Block | eigen oder geteilt |
|---|---|---|
| 1 | `H1` mit der Branche im Klartext | **eigen** |
| 2 | `Kurz gesagt` — Antwortabsatz mit Preisanker, **40–60 Wörter** | **eigen** |
| 3 | was diese Branche bei ihrer Website wirklich beschäftigt — **3 bis 5 echte Probleme** | **eigen** |
| 4 | was auf die Website dieser Branche gehört, als Liste | **eigen** |
| 5 | was in dieser Branche zu beachten ist — Rechts- und Fachfragen | **eigen** |
| 6 | ein Beispiel — das Musterprojekt dieser Branche | **eigen** |
| 7 | was es kostet — **dieselben Zahlen wie überall** | geteilt |
| 8 | wie es abläuft — die sechs Schritte, gekürzt | geteilt |
| 9 | **Bedarfsscheck direkt eingebettet, Branche vorausgefüllt** | geteilt |
| 10 | **drei** häufige Fragen, die **nur** diese Branche betreffen | **eigen** |

**Umfang: 900–1.300 Wörter.**

> **Warum Block 2 die Antwort vorwegnimmt, obwohl das Problem erst in Block 3 steht.** Der Aufbau
> bedient zwei Leser gleichzeitig, und das ist Absicht:
>
> | Leser | was er bekommt |
> |---|---|
> | wer überfliegt oder aus einer KI-Antwort kommt | Block 2 — Antwort und Preisanker in 40–60 Wörtern |
> | wer bleibt | Blöcke 3 bis 6 — Problem, Folge, Lösung, Beleg |
>
> **Block 2 ist keine Zusammenfassung des Problems, sondern die Antwort auf die Suchanfrage.**
> Danach beginnt der Bogen von vorn, ausführlich. **Wer eine der beiden Ordnungen „aufräumt",
> zerstört die andere.**

**Grenze für die Problemphase:** zwischen Block 2 und der ersten Aussage darüber, was SARTU
liefert, stehen **höchstens 150 Wörter**.

**Statistiken auf der Seite:** höchstens **drei** je Seite · **nie im Aufmacher** (Block 1 und 2) ·
Quelle, Jahr und Stichprobe **an der Zahl** · keine Quartalszahlen · **keine regionalen Werte**
auf einer bundesweit auffindbaren Seite.

> **Der Filter davor — der Nicken-Test:** Liest der Betrieb die Zahl und denkt „ja, genau"? Dann
> darf sie auf die Seite. Denkt er „bei mir stimmt das nicht", bleibt sie draußen — **auch bei
> bester Quelle.** Das Risiko ist einseitig: Zustimmung bringt ein Nicken, Widerspruch kostet die
> ganze Seite.

> **Der eingebettete Bedarfsscheck ist der eigentliche Gewinn** — nicht wegen Google, sondern
> wegen der Abbruchquote. Wer erst zu `/briefing` klicken muss, klickt oft gar nicht. Und die
> Branche ist bereits beantwortet: **ein Feld weniger für den Kunden, eine Angabe mehr für uns.**
>
> **Technisch:** derselbe Weg und dieselben Schutzmaßnahmen wie in `09_ANFRAGEEINGANG.md`. Das
> Feld `branche` wird aus der Seite vorbelegt und landet in `leads.payload`. **Kein zweiter Weg,
> kein zweites Formular.**

### Drei Prüfungen vor der Abgabe, alle hart

**1. Der Austauschtest.** Ersetze das Branchenwort durch ein anderes. **Ergibt der Text weiterhin
Sinn, ist es keine Branchenseite, sondern eine Vorlage mit getauschtem Etikett.**

**2. Die Eigenanteilsmessung.** Mindestens **400 Wörter** dürfen **auf keiner anderen Seite**
vorkommen. Prüfbar mit einem Textabgleich über alle Branchenseiten.

**3. Der Herkunftsnachweis.** Zu den Blöcken 3, 5, 6 und 10 gehört eine Quellenzeile: **woher
stammt diese Aussage über die Branche?** Zulässig sind nur:

| Quelle |
|---|
| `SARTU_BRANCHENFAKTEN.md` — Zahlen je Branche mit Quelle und Verfallsdatum |
| `SARTU_KUNDENMOTIVE_BELEGT.md` — der belegte Kern und die Branchentabelle Ostsachsen |
| der Auftraggeber |
| ein Betrieb der Branche — Gespräch, E-Mail, ausgefüllter Fragebogen |
| eine benannte, öffentliche Fundstelle — Innungsseite, Fachverband, Gesetzestext |

**Nicht zulässig: was einleuchtend klingt.**

> **Warum Prüfung 1 und 2 nicht reichen — an einem echten Fehlschlag.** Der erste Entwurf der
> SHK-Seite argumentierte mit der Notdienstnummer oben auf der Seite. Er bestand den Austauschtest
> mühelos, bestand die Eigenanteilsmessung und jede Zahlvorgabe.
>
> **Und war trotzdem unbrauchbar.** Eine Telefonnummer oben rechtfertigt keinen vierstelligen
> Preis — das kann jeder Baukasten. Der Notdienst ist außerdem das Geschäft, das ein
> Sanitärbetrieb am **wenigsten** will; er lebt von Badsanierung und Heizungstausch. Und die
> Begründung *„wer nachts sucht, ruft die erste Nummer an"* war **frei erfunden**.
>
> **Ein branchentypisches Argument ist nicht automatisch ein tragfähiges.**

**Reißt eine der drei Prüfungen: Die Seite wird nicht veröffentlicht.** Nicht überarbeitet —
**nicht veröffentlicht.** Eine Branche, über die sich keine 400 eigenen Wörter schreiben lassen,
ist eine Branche, über die niemand genug weiß.

> **Getrennt nach Block:** Die Blöcke 1, 2, 7, 8, 9 entstehen aus dem belegten Kern. Die Blöcke
> 3, 5, 6 und 10 brauchen den Herkunftsnachweis. Fehlt er, entsteht die Seite ohne diese Blöcke
> und geht **nicht online** — die 400-Wörter-Prüfung reißt dann ohnehin.
>
> Damit hängt Welle 1 an je einem Gespräch **für den eigenen Teil**, nicht an der ganzen Seite.

### Welche Branchen — gefiltert nach Zahlungsfähigkeit, nicht nach Vollständigkeit

**Nicht jede Branche kann den Erstjahreswert zahlen.** Ein Friseursalon mit zwei Stühlen und eine
Bäckerei mit einer Filiale sind keine Zielgruppe, **egal wie viele es davon gibt.**

| Welle | Branchen | warum |
|---|---|---|
| **1 — zum Launch, drei Stück** | Sanitär-Heizung-Klima · Elektrotechnik · Dachdecker | höchste Auftragswerte im Handwerk, akute Personalnot. **Keine besondere Rechtslage** — schreibbar ohne Fachgutachten |
| **2 — nach den ersten Kunden** | Garten- und Landschaftsbau · Tischlerei · Malerbetrieb · Fliesenleger · Kfz-Werkstatt | starker Bildbedarf, mittlere bis hohe Auftragswerte |
| **3 — braucht Rechtskenntnis** | Zahnarztpraxis · Physiotherapie · Steuerkanzlei · Rechtsanwaltskanzlei · Architekturbüro · Immobilienmakler | sehr zahlungsfähig, aber **eigene Berufsrechte**. Erst schreiben, wenn die Regeln geprüft sind |

**Zielgröße: 12 bis 15 Seiten über 18 Monate. Nicht vierzig.**

> **Die Grenze ist nicht Google, sondern wer die Fachtexte schreibt.** Vierzig Seiten mit je 400
> eigenen Wörtern sind 16.000 Wörter Fachwissen über Berufsrechte, Auftragswege und
> Branchenprobleme. Ohne dieses Wissen entstehen genau die „substantially similar pages", die
> verboten sind — **und die Seiten wären zugleich schlechte Verkaufstexte.**
>
> **Die Regel:** eine Branche vollständig, drei bis fünf Referenzen darin, **dann** die nächste.
> Ab dem ersten Kunden einer Branche schreibt sich ihre Seite fast von selbst.

> **Verworfen:** eine Seite `/webdesign-handwerk`. „Handwerk" ist genau die Sammelbezeichnung,
> die dieses Projekt sonst überall verbietet. Ein Malerbetrieb, ein Dachdecker und ein
> Heizungsbauer haben **verschiedene Probleme, verschiedene Bilder und verschiedene Kunden**.

**Verboten:** Branchenpreise erfinden · Branchenzahlen ohne Quelle · Rechtsaussagen ohne
Fundstelle · eine Seite veröffentlichen, die eine der drei Prüfungen reißt.

---

## 4a. `/musterprojekte` — die Zielseite des gebundenen CTA

> **Ergänzt am 06.08.2026.** Die Seite fehlte in dieser Datei vollständig, obwohl
> `10_WEBSITE_SARTU.md` §8 den CTA `Alle Musterprojekte ansehen` **bindet**. Ein gebundener Knopf
> ohne Zielseite ist ein Bruch, kein offener Punkt.

**Umfang 700–1.000 Wörter.** Sie ist die Langfassung von Sektion 8 der Startseite, keine
Wiederholung: dort **drei Karten nebeneinander**, hier **drei Fälle nacheinander mit Begründung**.

**Aufbau `gebunden`:**

| # | Block | Inhalt |
|---|---|---|
| 1 | `H1` | benennt, dass es Muster sind — **kein** `Referenzen`, `Arbeiten`, `Projekte` |
| 2 | Einleitung | wie auf der Startseite: Gründungsjahr und Anzahl. **Zahlen `gebunden`:** Gründung **2026**, **drei** Beispiele |
| 3 | **Warum es Muster sind und keine Kunden** | der ehrliche Absatz, den die Startseite aus Platzgründen nicht führt |
| 4–6 | **je Fall ein Abschnitt** | die drei gebundenen Gattungen, Reihenfolge wie auf der Startseite |
| 7 | Übergang zum Bedarfsscheck | CTA `Bedarf prüfen lassen` → `/briefing` |

**Je Fall — dieselben fünf Bestandteile wie auf der Karte, nur ausgeschrieben:**
Ausgangslage · empfohlene Lösung · Seitenstruktur · Bildplatz · was der Kunde selbst liefern
müsste. Dazu **ein** Block, den die Karte nicht trägt: **warum diese Stufe und nicht die
nächstkleinere.**

**Gebunden:**
- Die drei Gattungen: **Malerbetrieb · Physiotherapiepraxis · Arbeitsrechtskanzlei**
- Über jedem Fall: `Musterprojekt — kein Kundenauftrag`
- Der Preishinweis in der **langen** Fassung, sobald eine Zahl fällt — `08_TEXTREGELN.md`

### Die drei müssen sichtbar verschieden aussehen

**Sonst beweisen sie das Gegenteil dessen, wofür sie da sind.** SARTU verkauft
*individuell programmiert, kein Baukasten* (`01_GESCHAEFTSMODELL.md`). Drei Musterprojekte, die
sich nur im Text unterscheiden, sind der Beleg dafür, dass es **doch** ein Baukasten ist —
vorgelegt von SARTU selbst, an der Stelle, an der der Leser nach Beweisen sucht.

**Je Musterprojekt ein festes Wertetripel** aus den geschlossenen Achsen in
`03_KUNDENPRODUKT.md`. Die Werte folgen der Branche, **nicht dem Wunsch nach Abwechslung** —
dass alle drei Stufen jeder Achse einmal vorkommen, ist das Ergebnis, nicht die Vorgabe:

| Musterprojekt | Inhaltsdichte | Formcharakter | Bewegung | Woraus es folgt |
|---|---|---|---|---|
| **Malerbetrieb** | `compact` | `bold` | `subtle` | Die Arbeiten sind das Argument — große Flächen, wenig Text. Gelesen wird oft mobil auf der Baustelle. Die eine sinnvolle Bewegung ist der Vorher-nachher-Wechsel |
| **Physiotherapiepraxis** | `balanced` | `human` | `none` | Eine Seite trägt Leistung, Zeiten und Terminweg zugleich — sie braucht Gleichgewicht, keine Verdichtung. Weiche Formen, weil Härte im Gesundheitskontext falsch wirkt. Ältere Zielgruppe, also Ruhe |
| **Arbeitsrechtskanzlei** | `editorial` | `precise` | `none` | Verkauft über Text und Genauigkeit. Zwei Zielgruppen mit gegensätzlichem Anliegen brauchen Gliederung, nicht Bewegung |

**Beide sichtbaren Achsen decken ihre volle Spanne ab** — Dichte und Formcharakter je einmal in
jeder Stufe. Bewegung tut das nicht, und das bleibt so: **eine Bewegungsstufe zu wählen, damit
die Tabelle vollständig aussieht, wäre Gestaltung für die Vorlage statt für den Kunden.** Auf
einer Bildschirmaufnahme ist sie ohnehin die unsichtbarste der drei.

> **Was noch fehlt, um das zu bauen:** der Komponentenkatalog aus `03_KUNDENPRODUKT.md` — welche
> Hero-Komposition, welche Leistungsdarstellung, welches Navigationsmuster. Er existiert nicht.
> **Die Wertetripel oben gelten trotzdem schon**, weil die drei Achsen geschlossen sind.

**Sperren — dieselben wie in §8, sie gelten hier schärfer**, weil die Seite mehr Platz zum
Ausschmücken hätte: keine erfundenen Firmennamen · keine erfundenen Zahlen, Ergebnisse oder
Steigerungen · **kein nachgebauter Bildschirm**, solange kein Demoprojekt gebaut ist
(`SARTU_ENTSCHEIDUNGEN_OFFEN.md` §5).

> **Der Fall darf nicht zur Fallstudie werden.** Eine Fallstudie behauptet ein *Ergebnis* — mehr
> Anfragen, bessere Sichtbarkeit. Ein Musterprojekt beschreibt eine *Entscheidung*: dieser
> Ausgangslage entspricht diese Struktur. **Sobald ein Ergebnis behauptet wird, ist es eine
> erfundene Fallstudie**, und die ist in `20_OFFEN.md` gesperrt, bis ein echter Kunde schriftlich
> freigibt.

**Suchmaschinen:** eigene Adresse, eigener Title, eigene Beschreibung, `index,follow`.
Zielbegriff aus `KEYWORD_VALIDATION.md` — **die Datei wird neu erzeugt, wenn diese Adresse in
`Launchadressen` aufgenommen wird**, nicht von Hand ergänzt.

**Verlinkt aus:** Startseite §8 (`Alle Musterprojekte ansehen`) · je Branchenseite aus Block 6
(„ein Beispiel — das Musterprojekt dieser Branche"), sofern die Gattung passt.

---

## 5. `/ueber-uns` und `/kontakt`

### `/ueber-uns` — **Umfang 400–550 Wörter**

**Aufbau `gebunden`:** Aufmacher mit **echtem Foto** → warum SARTU anders arbeitet →
was SARTU bewusst **nicht** ist → Arbeitsweise in fünf Schritten → Verantwortung → CTA

**Vier Punkte „warum anders" — `gebunden`:** Festpreis statt Stundenfalle · Kundenbereich statt
E-Mail-Chaos · Fakten statt Geschmacksdiskussionen · **KI als Werkzeug, nicht als Ersatz**

**Vier Punkte „was SARTU nicht ist" — `gebunden`:** `kein Baukasten` · `kein WordPress-Hoster` ·
`keine Billig-Seitenschleuder` · `kein Anbieter für Privat- und Hobbyseiten`

**Verantwortungssatz** — *Aufgabe:* zusagen, dass nur veröffentlicht wird, was geprüft und
freigegeben ist. *Grenze:* keine Qualitätsgarantie daraus machen.

> **Ehrlichkeitsregel:** Solange eine Einzelperson arbeitet — **„gründergeführt", nie „unser
> Team"** (`06_RECHT.md`). **Kein Platzhalterfoto, das wie ein echtes Foto wirkt.**

### `/kontakt` — **Umfang 250–350 Wörter**

**Zwei Karten `gebunden`:** `Websitebedarf prüfen` (primär → `/briefing`) und `Rückfrage stellen`
(Anker zum Formular).

**Pflichtabschnitt „Wo wir arbeiten"**, direkt unter den beiden Karten:

- *Aufgabe:* **erst** die bundesweite Reichweite mit ihrer Begründung (ohne Termine spielt
  Entfernung keine Rolle), **dann** der Sitz und die Orte, die bei Bedarf persönlich besucht werden
- *Grenze:* **Die Reihenfolge ist verbindlich.** Umgekehrt liest ein Betrieb aus Kassel „Dresden"
  und geht
- *Ortsliste `gebunden`:* Sitz im Raum Dresden · Meißen · Radebeul · Coswig · Radeberg · Pirna ·
  Heidenau · Freital · Dippoldiswalde · Bischofswerda · Bautzen · Sebnitz

> **Warum dieser Abschnitt Pflicht ist:** Die Reichweite stand vorher **nirgends** auf der
> Website. Dabei ist sie die logische Folge des stärksten Verkaufsarguments — **ohne Termin heißt
> überall.** Die Ortsliste dient der Nachbarschaft und dem Kartenbereich, **nicht der Abgrenzung**.

**Formularfelder:** Name (Pflicht) · Unternehmen (Pflicht) · E-Mail (Pflicht) · Telefon
(optional) · Anliegen als Auswahl `gebunden`: `Websiteprojekt` · `Bestehendes Angebot` ·
`Domain und Launch` · `Allgemeine Rückfrage` · Nachricht (Pflicht, **mindestens 20 Zeichen**) ·
Datenschutzbestätigung (Pflicht) · Honigtopf.

**Kein Dateiupload, keine Pflicht-Telefonnummer.** Das Kontaktformular erzeugt **keinen**
Datensatz — `09_ANFRAGEEINGANG.md`.

---

## 6. Transparenzseiten — der Pflichtblock

**Diese Seiten sind kein Beiwerk.** Sie sind der Grund, warum SARTU in Suchergebnissen und
KI-Antworten überhaupt zitiert wird: **Fast jede Agentur schreibt „Preis auf Anfrage" — SARTU
nennt Zahlen. Das ist die Lücke.**

**Zum Launch verbindlich, drei Stück:**

| Adresse (`gebunden`) | warum sie steht | was nachprüfbar drinstehen muss |
|---|---|---|
| `/ratgeber/was-kostet-eine-firmenwebsite` | die häufigste Frage im Markt | **SARTUs Zahlen konkret.** Fremde Anbieterarten nur als **Kostenbestandteile und Entscheidungslogik** — keine Markt-, Wettbewerber- oder Preisspannen. **Laufende Kosten getrennt ausweisen** |
| `/ratgeber/was-nicht-enthalten-ist` | **schreibt sonst niemand** | vollständige Ausschlussliste im Klartext, plus Begründung, warum es keine Zusatzoptionen gibt |
| `/ratgeber/was-der-betrieb-kostet` | zweithäufigste Rückfrage | was in den drei Schutzstufen enthalten ist, was nicht, **und was bei Vertragsende mit Domain und Website passiert** |

**Nach dem Launch:** `/ratgeber/wie-lange-dauert-eine-website` ·
`/ratgeber/website-festpreis-erkennen` · `/ratgeber/was-eine-korrekturrunde-ist`.

**Harte Regeln:**

- **Jede Zahl stammt aus dem eigenen Angebot und stimmt.** Keine Marktdurchschnitte, keine
  Studien, keine Wettbewerberpreise
- Über fremde Anbieter nur in **Kategorien** (`Baukasten`, `Freelancer`, `Agentur`) — **nie mit
  Namen, nie mit konkreten Preisen**
- **Preise als Text, nie als Bild.** Ein Preis in einer Grafik existiert für Suchmaschinen und
  KI-Systeme nicht
- **Antwort zuerst:** die ersten **40–60 Wörter** beantworten die Titelfrage direkt **und mit Zahl**
- Vergleiche als **Tabelle**, nicht als Fließtext
- **Sichtbares Aktualisierungsdatum** auf jeder Seite

> **Technische Pflicht:** Alle Preise, Umfangsgrenzen, Korrekturrunden und Lieferkorridore stehen
> an **einer** Stelle im Code und werden von dort auf allen Seiten ausgegeben. **Nie doppelt
> pflegen.** Eine veraltete Preisangabe ist schlimmer als keine — **sie wird zitiert und dann
> gegen SARTU verwendet.**

---

## 7. Ratgeber — zwei Vergleichsartikel

> **Abgrenzung:** Wo **Zahlen** im Mittelpunkt stehen, ist es eine Transparenzseite (Abschnitt 5).
> Hier geht es um **Entscheidungen** zwischen Optionen.

**Hub `/ratgeber`:** H1 · Kurzintro (zwei Sätze) · Artikelliste mit Titel, Kurzantwort und Datum ·
**kein Kategorienfilter** bei wenigen Artikeln. Der Hub listet Ratgeber **und** Transparenzseiten,
**weil sie für Leser dasselbe sind.**

**Je Artikel `gebunden`:** H1 mit der Suchintention · **Kurzantwort in den ersten zwei Sätzen** ·
sichtbares Aktualisierungsdatum · Tabelle oder Entscheidungslogik · **mindestens zwei interne
Links** · CTA. **Umfang 900–1.300 Wörter.**

### `/ratgeber/agentur-freelancer-baukasten`

**Gliederung `gebunden`:** vier Anbieterarten in einer Tabelle → wann ein Baukasten wirklich
reicht → was am Freelancer-Modell schiefgehen kann → warum Agenturen selten Festpreise nennen →
**für wen SARTU nicht passt** → Entscheidungshilfe in fünf Fragen → CTA `/preise`

**Die Tabellenspalte beschreibt, *woraus* der Preis bei der jeweiligen Anbieterart entsteht**
(Stundensatz × Stunden, Softwaregebühr + Eigenleistung, Festpreis) — **nicht, *wie hoch* er ist.**

> **Keine fremden Preise erfinden.** SARTUs eigene Zahlen stehen konkret daneben — sie sind die
> einzigen belegten auf der Seite. **Wenn ein Baukasten für einen Fall reicht, steht das so da.
> Genau das macht die Seite glaubwürdig.**

### `/ratgeber/webdesign-ohne-wordpress`

**Gliederung `gebunden`:** warum WordPress so verbreitet ist → was daran im Alltag Arbeit macht →
Alternativen (Baukasten, statisch, individuell) → **was man dabei aufgibt** → für wen sich was
eignet, als Entscheidungstabelle → wie SARTU es löst → CTA `/leistung-webdesign`

**Nach dem Launch:** `agentur-auswaehlen-kriterien` · `relaunch-sinnvoll` ·
`website-handwerker-fehler` · `bfsg-firmenwebsite`.

> **Bei „Agentur auswählen" später: Kriterien nennen, keine Rangliste.** Eine Seite, auf der SARTU
> sich selbst zur besten Wahl erklärt, ist unglaubwürdig **und wettbewerbsrechtlich riskant**.

---

## 8. Lexikon — acht Startbegriffe

**Hub `/lexikon`:** H1 · Kurzintro · alphabetische Liste mit **einer** Definition je Begriff ·
**kein Suchfeld** bei acht Begriffen (erst ab etwa 40).

**Begriffsseite — acht Teile, `gebunden`:** H1 = Begriff · Kurzdefinition (2–3 Sätze) · warum es
für Firmenwebsites wichtig ist · Beispiel · **typischer Fehler** · wie SARTU damit umgeht ·
verwandte Begriffe (2–4) · Link zur passenden Leistungsseite.

**Umfang 250–400 Wörter.**

| # | Begriff | Adresse (`gebunden`) | verweist auf |
|---|---|---|---|
| 1 | Firmenwebsite | `/lexikon/firmenwebsite` | `/leistung-webdesign` |
| 2 | Festpreis | `/lexikon/festpreis` | `/preise` |
| 3 | Hosting | `/lexikon/hosting` | `/leistung-wartung` |
| 4 | Domain | `/lexikon/domain` | `/leistung-wartung` |
| 5 | Relaunch | `/lexikon/relaunch` | `/leistung-webdesign` |
| 6 | Barrierefreiheit | `/lexikon/barrierefreiheit` | `/leistung-webdesign` |
| 7 | Local SEO | `/lexikon/local-seo` | `/leistung-seo-lokal` |
| 8 | GEO (KI-Suche) | `/lexikon/geo-ki-suche` | `/leistung-seo-lokal` |

> **Auswahlregel:** nur Begriffe, die in einem echten Verkaufsgespräch vorkommen und **bei denen
> ein Missverständnis Geld kostet. Nicht jeder Fachbegriff, den es gibt.**

**Stufe 2, nach Search-Console-Daten:** Backup · Canonical · Core Web Vitals · DNS · One-Pager ·
Schema.org · Suchintention · Weiterleitung (301) · SEO · CMS · SSL · Sitemap. Ausbau auf 40–60
Begriffe **erst dann**.

---

## 9. Pflicht- und Systemseiten

| Seite | Regel |
|---|---|
| `/impressum` | vollständig nach **§ 5 DDG**, **keine Platzhalter live**. Daten identisch zu Fußbereich und strukturierten Daten. **Bis zur Standortentscheidung nicht öffentlich** (`20_OFFEN.md`) |
| `/datenschutz` | deckt ab: Hosting, Serverprotokolle, Kontaktformular, Bedarfsscheck, Verweis auf den Kundenbereich, **KI-Verarbeitung** (soweit personenbezogen), Statistik, eingebundene Dienste. **Keine Aussage „rechtssicher"** |
| `/agb` | **nur live und verlinkt, wenn anwaltlich final.** Sonst gar nicht verlinken und `noindex` |
| **404** | *Aufgabe:* sagen, dass es die Seite nicht gibt, und den wahrscheinlichsten Grund nennen. **Links `gebunden`:** Startseite, Leistungen, Preise, Bedarf prüfen lassen. **Echter 404-Status**, `noindex` |
| Danke-Seiten | `noindex` · klare nächste Erwartung · **keine weiteren Angebote** |

> **Rechtstexte werden technisch eingebunden, nicht formuliert** — `06_RECHT.md`. Der
> Texter-Skill schreibt sie ausdrücklich **nicht**.

---

## 10. Bilder und Bildschirmaufnahmen

**Alle Bilder:** WebP (AVIF optional) · `srcset` mit 1× und 2× · **feste `width`/`height`** ·
echter Alt-Text · sprechender Dateiname. **Der Aufmacher wird nicht lazy geladen**
(`fetchpriority="high"`), alles darunter `loading="lazy"`.

| Datei | Verwendung | Ausgabemaß (1×) | Verhältnis |
|---|---|---|---|
| `sartu-portal-cockpit-muster.webp` | Startseite Aufmacher | 720 × 540 | 4:3 |
| `sartu-portal-briefing-muster.webp` | Startseite Kundenbereich, `/leistung-texte` | 960 × 600 | 8:5 |
| `sartu-portal-angebot-muster.webp` | `/ablauf`, `/preise` | 960 × 600 | 8:5 |
| `sartu-portal-domain-muster.webp` | `/ablauf` | 960 × 600 | 8:5 |
| `sartu-portal-vorschau-muster.webp` | `/ablauf`, `/leistung-portal` | 960 × 600 | 8:5 |
| `sartu-portal-pflege-muster.webp` | `/leistung-wartung` | 960 × 600 | 8:5 |
| `sartu-leistungslandkarte.svg` | `/leistungen` | 1200 × 520 | – |
| `sartu-portrait.webp` | `/ueber-uns` | 640 × 800 | 4:5 |
| `sartu-og-standard.webp` | Open Graph, global | 1200 × 630 | 1.91:1 |

**Alt-Texte werden geschrieben** — *Aufgabe:* beschreiben, **was zu sehen ist**, nicht was es
bedeuten soll. *Grenze:* bei jeder Musteransicht muss das Wort **Musteransicht** vorkommen.

**Bildschirmaufnahmen:** **ohne Browserrahmen**, 24 px Innenabstand um den Inhalt, Verhältnis 8:5.
Sichtbare Daten sind **Musterdaten** — keine realistischen Rechnungsnummern, keine echten
Personennamen, keine echten Kundenlogos.

> Das Kennzeichen `Musteransicht` wird **im Layout** gesetzt, **nicht ins Bild gebrannt** — so
> bleibt es übersetzbar und für Vorlesesoftware erreichbar.
