# GEO-Discovery — technische Auffindbarkeit für KI-Antwortsysteme

**Zweck:** Sicherstellen, dass SARTU von den Systemen **gefunden und zugeordnet** werden kann, die
KI-Antworten erzeugen. Das ist die technische Ergänzung zu `SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §5 —
dort steht, **was** zitierfähig macht, hier steht, **ob überhaupt jemand hinkommt**.

**Diese Liste wird vor dem Livegang abgehakt und das Ergebnis dokumentiert.** Kein Punkt darf
„vermutlich in Ordnung" bleiben.

> **Was diese Liste ausdrücklich nicht ist:** eine Sammlung von Tricks. Jeder Punkt hier sorgt nur
> dafür, dass ehrlicher Inhalt erreichbar und eindeutig zuordenbar ist. Wer nichts Gutes zu sagen
> hat, wird auch mit perfekter Discovery nicht zitiert.

---

## 1. Crawler-Zugang — `robots.txt`

Die häufigste Ursache für Unsichtbarkeit ist eine `robots.txt`, die zu viel sperrt. Standardvorlagen
aus dem Netz enthalten oft pauschale Sperren für KI-Crawler.

### Erreichbar sein müssen

| Crawler | Wofür | Folge einer Sperre |
|---|---|---|
| `Googlebot` | Google-Suche **und** die KI-Funktionen darin | unsichtbar in Google, komplett |
| `Bingbot` | Bing **und** nachgelagerte Systeme, die Bing-Daten nutzen | unsichtbar in Bing |
| `OAI-SearchBot` | Auffindbarkeit in der **Suche von ChatGPT** | erscheint dort nicht als Antwortquelle |

### Bewusst zu entscheiden — nicht versehentlich

| Crawler | Wofür | Anmerkung |
|---|---|---|
| `GPTBot` | Sammeln von Inhalten fürs **Modelltraining** | **Andere Frage als Sichtbarkeit.** Wer hier sperrt, verliert **nicht** die Sichtbarkeit in der ChatGPT-Suche — dafür ist `OAI-SearchBot` zuständig. Beides zu verwechseln ist der häufigste Fehler |
| `OAI-AdsBot` | prüft Zielseiten von **Anzeigen** auf ChatGPT | nur relevant, wenn dort geworben wird. Gesperrt = Anzeige wird abgelehnt. Für den geplanten Anzeigentest vorher klären |
| `ChatGPT-User` | ruft eine Seite ab, **weil ein Nutzer danach fragt** | kein automatisches Durchsuchen. Eine Sperre verhindert, dass jemand SARTU im Gespräch aufrufen lassen kann |

**Empfehlung:** `Googlebot`, `Bingbot` und `OAI-SearchBot` erlauben. `GPTBot` ist eine
Geschäftsentscheidung — sie wird **dokumentiert**, nicht nebenbei getroffen.

**Prüfen:** `robots.txt` im Browser aufrufen und Zeile für Zeile lesen. Zusätzlich mit dem
robots.txt-Bericht der Search Console gegenprüfen.

**Quelle geprüft 25.07.2026:** https://developers.openai.com/api/docs/bots

### Weitere Fallen in derselben Datei

- [ ] Kein `Disallow: /` aus einer Staging-Fassung übriggeblieben — **der klassische Livegang-Fehler**
- [ ] `/ratgeber/` und `/lexikon/` sind **nicht** gesperrt
- [ ] Die Sitemap ist eingetragen (`Sitemap: https://…/sitemap.xml`)
- [ ] `noindex` steht **nur** dort, wo es hingehört: Bedarfsscheck-Schritte, Danke-Seiten, AGB solange Platzhalter

---

## 2. Sitemap und Indexierung

- [ ] `sitemap.xml` enthält **ausschließlich** Adressen, die 200 zurückgeben und indexierbar sind
- [ ] Keine `noindex`-Seite in der Sitemap — das ist ein widersprüchliches Signal
- [ ] Search Console eingerichtet, Sitemap eingereicht
- [ ] Bing Webmaster Tools eingerichtet, Sitemap eingereicht
- [ ] **IndexNow** eingerichtet (Bing und weitere) — kostet fast nichts und beschleunigt die Aufnahme neuer Seiten
- [ ] Jede Launch-Adresse einmal manuell auf Indexierung geprüft, nicht nur eingereicht

---

## 3. Entität — wer ist SARTU?

KI-Systeme müssen SARTU als **eine** Sache erkennen. Uneinheitliche Angaben führen dazu, dass gar
nichts zugeordnet wird.

- [ ] `Organization` in strukturierten Daten auf **jeder** Seite: `name`, `url`, `logo`, `description`
- [ ] **Identische** Kurzbeschreibung überall — Website, `Organization`, externe Profile. Ein Satz, wortgleich
- [ ] Schreibweise des Namens überall gleich (`SARTU`, Versalien)
- [ ] `WebSite` mit `url` und `name`
- [ ] `BreadcrumbList` auf allen Unterseiten
- [ ] **Kein** `LocalBusiness`, solange `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1 auf `offen` steht
- [ ] `sameAs` **nur mit echten, gepflegten Profilen** — lieber leer als mit toten Verweisen

### `sameAs` — was hineingehört und was nicht

| Aufnehmen, sobald es existiert | Nie aufnehmen |
|---|---|
| Google-Unternehmensprofil (nach Standortentscheidung) | Massenverzeichnisse und Branchenportale ohne Pflege |
| Bing Places | gekaufte Einträge |
| LinkedIn-Unternehmensseite | Profile, die niemand aktualisiert |
| persönliches Gründerprofil, wenn es die Person zeigt | erfundene oder fremde Profile |

**Regel:** Ein Profil kommt erst in `sameAs`, wenn es **echte, aktuelle Inhalte** hat. Ein leeres
LinkedIn-Profil schadet mehr, als es nützt.

---

## 4. Zitierfähigkeit der Seiten

Ergänzt `SARTU_SEO_GEO_KEYWORDSTRATEGIE.md` §5 um die prüfbare Fassung:

- [ ] Jede Inhaltsseite beginnt mit **40–60 Wörtern**, die die Titelfrage direkt beantworten
- [ ] Alle Preise stehen als **Text**, nie als Bild
- [ ] Vergleiche stehen als **Tabelle**, nicht als Fließtext
- [ ] **Sichtbares Aktualisierungsdatum** auf jeder Inhaltsseite
- [ ] Benannte Verantwortung — wer schreibt hier
- [ ] Auch das Negative steht drin („nicht enthalten ist …")
- [ ] Ohne JavaScript ist der **vollständige** Inhalt im Quelltext — mit abgeschaltetem JS geprüft
- [ ] Kein Inhalt erscheint erst durch Scrollen oder Nachladen

**Der vorletzte Punkt ist der wichtigste dieser Liste.** Was erst durch JavaScript entsteht, sehen
viele Systeme nicht. Bei einer serverseitig gerenderten Seite ist das ohnehin erfüllt — es gehört
trotzdem geprüft.

---

## 5. `llms.txt`

- [ ] Angelegt, wenn gewünscht — schadet nicht
- [ ] **Wird nirgends als Sichtbarkeitsmaßnahme geführt** und Kunden nicht als solche verkauft

Google sagt ausdrücklich, die Datei helfe weder beim Ranking noch bei der Sichtbarkeit in der
Google-Suche. Für andere Systeme ist der Nutzen unbelegt. Aufwand: fünf Minuten. Erwartung: keine.

---

## 6. Was hier bewusst fehlt

| Nicht tun | Warum |
|---|---|
| Einträge in KI-Verzeichnissen kaufen | dieselbe Logik wie gekaufte Backlinks, dasselbe Risiko |
| Erwähnungen in fremden Texten erzeugen lassen | erfundene Belege, Vertrauensverlust bei Entdeckung |
| Inhalte speziell für KI-Systeme spiegeln | unterschiedliche Inhalte für Crawler und Menschen ist Cloaking |
| Sichtbarkeit in KI-Antworten Kunden zusichern | niemand kann das garantieren, auch die Anbieter nicht |

---

## Abnahme

Diese Liste wird **vor dem Livegang** vollständig abgehakt. Für jeden Punkt wird notiert: geprüft am,
Ergebnis, gegebenenfalls was geändert wurde. Punkte, die von einer offenen Entscheidung abhängen
(Google-Profil, `sameAs`), bleiben mit dem Vermerk **„wartet auf Standortentscheidung"** stehen —
nicht stillschweigend übersprungen.
