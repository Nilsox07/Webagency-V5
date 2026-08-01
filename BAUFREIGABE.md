# Baufreigabe — was heute starten darf

**Stand:** 01.08.2026
**Geprüft:** 19 Bauunterlagen, 11.383 Zeilen. Code: **0 Zeilen.**
**Zweck:** Die Frage „können wir anfangen" einmal beantworten, damit sie nicht in jeder Sitzung neu
verhandelt wird.

---

> ## Korrigiert am 01.08.2026 — die erste Fassung war zu optimistisch
>
> Sie behauptete „Stufe A0 startet sofort". Eine externe Prüfung gegen Commit `8ad6207` hat
> **fünf Widersprüche gefunden, die schon A0 blockierten**. Alle fünf wurden nachgerechnet und
> bestätigt, alle fünf sind inzwischen behoben.
>
> | # | Befund | Warum es A0 traf | Behoben durch |
> |---|---|---|---|
> | 1 | **Zwei sich widersprechende Rangfolgen** — Übergabeliste stellt `SARTU_ENTSCHEIDUNGEN_OFFEN.md` auf Rang 1 und das Portal-Lastenheft auf 4, der Portalauftrag genau umgekehrt | Der Bauauftrag verlangt bei Vorgabenwiderspruch **anhalten**. Ein Bauchat wäre in Zeile 1 stehengeblieben | Portalauftrag §2 ist jetzt nur noch **Lesereihenfolge**. Die Rangfolge steht allein in `UEBERGABE_DATEILISTE.md` |
> | 2 | **Ersteinrichtung nicht ausführbar** — Schritt 5 erhob kein Passwort, §7 verlangt eines · `ENC_KEY` entstand nach dem verschlüsselten TOTP-Geheimnis · `operator_settings` sollte angelegt werden, ohne dass eines seiner sieben Pflichtfelder erhoben wurde | Der Installer wäre am `INSERT` gescheitert, und der Admin hätte sich nie anmelden können | §1.5 neu gefasst: **acht Schritte**, Schlüssel vor Verschlüsselung, eigener Schritt für Betreiberdaten, Passwort in Schritt 7 |
> | 3 | **`.env.example` passte nicht zu §1.5** — `DB_PASSWORD` statt `DB_PASS`, `APP_KEY` statt `SESSION_SECRET` und `ENC_KEY`, `MAIL_HOST` statt `SMTP_HOST`, `APP_URL` statt `BASE_URL`. Sieben Pflichtwerte fehlten ganz, und `APP_ENV=development` gibt es nicht | Die lokale HTTP-Ausnahme des Setups hätte nie gegriffen | `.env.example`, `docker-compose.yml` und `ENTWICKLUNGSUMGEBUNG.md` auf die Namen aus §1.5 gezogen |
> | 4 | **Testzuordnung rechnerisch falsch** — behauptet A0 = 26 · A1 = 35 · A2 = 20 · A3 = 6, tatsächlich **27 · 34 · 21 · 5**. Dazu lag Fall 56 („alle Kernabläufe ohne JavaScript") in A0, wo es keinen Kernablauf gibt | Die Abnahme von A0 hätte gegen eine falsche Zahl geprüft | Fall 56 nach **A3** verschoben, Zahlen mit Skript nachgerechnet: **26 · 34 · 21 · 6 · 1 = 88** |
> | 5 | **Sperre der Einrichtung ohne benannten Ort** — „in Datei und Datenbank", aber wo in der Datenbank? | Nicht implementierbar | `operator_settings.setup_completed_at` **und** `/storage/installed.lock`. **Einer** von beiden genügt für 404 |
>
> **Zwei Befunde ließen sich nicht bestätigen.** Zu `audit_events` steht im Lastenheft nur die
> Frist von drei Jahren. Ein „niemals löschen" gibt es dort nicht. Und die Stelle „17 von 20
> Tabellen" existiert im Repository nicht mehr.
>
> **Zwei Befunde außerhalb von A0 wurden mitbehoben:**
>
> | Befund | Behoben |
> |---|---|
> | Die zweite Zahlungserinnerung hatte nur ein Zeitstempelfeld. Ab Tag 7 wäre sie täglich rausgegangen | Neues Feld `reminder2_sent_at` |
> | Für Leads standen 6 **und** 12 Monate Löschfrist | Getrennt: `abgelehnt` 6 Monate, sonstige nicht umgewandelte 12 |

## Die Antwort in drei Zeilen

| | |
|---|---|
| **Stufe A0 ist freigegeben** | seit der Korrektur vom 01.08.2026. Die fünf Befunde oben sind behoben |
| **Eine Sperre steht vor dem vollständigen Backend** | §7b Karriereseite — zwei Lesarten, siehe unten |
| **Drei Sperren stehen vor der Veröffentlichung** | Rechtstexte, Bildmaterial, Branchenseiten. **Nicht** vor dem Bauen |

---

## 1. Was heute ohne Rückfrage gebaut werden kann

### A0 — Fundament, 6 Tabellen, 26 Testfälle

Ersteinrichtung · Migrationen · Adminanmeldung mit TOTP · Betreiberdaten · Rechtstexte mit
Freigabezustand · Testmailversand · Mandantentrennung · Prüfprotokoll.

**Warum unblockiert:** Adresse, Rechtsform und Name sind für die **Außendarstellung** offen. Die
Ersteinrichtung fragt sie trotzdem ab (§1.5 Schritt 6) — `operator_settings` hat sieben
`NOT NULL`-Felder und eine `CHECK`-Bedingung auf die Steuerangabe. **Vorläufige Werte sind
erlaubt** und im Adminbereich änderbar; die Startsperre §1.4a prüft auf Inhalt.

> **Die erste Fassung stand hier falsch:** *„Die Tabelle braucht die Werte nicht, um zu
> entstehen."* Die **Tabelle** braucht sie nicht. Die **Zeile** schon — und §1.5 legt sie beim
> Setup an.

**`legal_texts` genauso:** Die Tabelle, der Freigabezustand und die Kennzeichnung `ENTWURF` sind
festgelegt. Dass noch kein geprüfter Text existiert, hindert das Schema nicht.

### A1 — Anfrage bis Auftrag, 4 Tabellen, 34 Testfälle

Bedarfsscheck · Anfrageliste · Umwandlung in Kunde und Projekt · Anmeldelink · Angebot senden ·
Löschlauf.

**Eine Bedingung vorher:** Der Hoster muss **Cron** können (Portal §1.4). A1 enthält den Löschlauf
für IP-Adressen, A2 den Überfälligkeitslauf. Ohne Cron braucht beides einen anderen Auslöser — das
ist eine Architekturentscheidung. Eine Einstellung reicht dafür nicht.

---

## 2. Die eine Sperre vor dem vollständigen Backend

### §7b — Stellen- und Karriereseite

**Warum das nicht warten kann:** Zwei der vier Fragen aus `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §7b
greifen ins Fundament.

| Frage | Was daran hängt |
|---|---|
| Landen Bewerbungen im Kundenbereich wie Anfragen? | Eine eigene Art in `leads` oder eine eigene Tabelle. **Datenmodell** |
| Ändern sich Seitenzahl und Wortumfang der Pakete? | Preistabelle, Angebotslogik, jede Stelle mit `1 / 8 / 16 Seiten` |

**Wer das nach dem Backend entscheidet, zahlt eine Migration und eine Preisänderung.**

**Stand 01.08.2026:** Der Betreiber hat „mit Bewerbungsformular ins Portal" gewählt. **Die Antwort
hat zwei Lesarten** — Formular auf der Kundenwebsite mit E-Mail an den Handwerker, oder Bewerbungen
im SARTU-Kundenbereich. Die erste kostet nichts und steht schon im Masterkonzept. Die zweite
verlangt sechs zusätzliche Festlegungen und hebt eine bestehende Grenze auf
(`CODEX_AUFTRAG_PORTAL.md` §5, „Nicht bauen"). Einzelheiten in `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §7b.

---

## 3. Was das Bauen **nicht** blockiert

| Offen | Blockiert | Blockiert nicht |
|---|---|---|
| **Rechtstexte** (§2) | Veröffentlichung — Website §14a Bedingung 8 | Schema, Portal, Website-Bau |
| **Bildmaterial, Gründername, Foto** (§5) | Startseiten-Sektion 8, zwei Bildplätze | alles andere |
| **Demoprojekte** (§5) | Beleg für Arbeitsqualität | Bau |
| **Branchenseiten** (§10a) | 12–15 Seiten, Herkunftsnachweis fehlt | Start- und Leistungsseiten |
| **Hoster und Tarif** (§4) | Betrieb | Bau — bis auf die Cronfrage oben |

**Die Bildplätze haben eine natürliche Reihenfolge:** Zwei Startseitenbilder zeigen Ansichten aus
dem Kundenbereich. Den gibt es erst nach A2. **Die Website kann vor dem Portal nicht fertig
aussehen** — das folgt aus dem Plan und ist kein Fehler darin.

---

## 4. Wo „erst bauen, dann Design" die Reihenfolge umdreht

**Der geplante Weg** (`CLAUDE_SARTU_DESIGN_BRIEFING_AUSFUEHRUNG.md`):
Recherche → Prüfliste → **2–3 klickbare Startseitenvarianten mit echten Texten** → Mensch
entscheidet.

**Diese Varianten sind die erste Frontend-Version.** Sie stehen **vor** dem Ausbau.

| Reihenfolge | Was man bekommt | Was es kostet |
|---|---|---|
| **Varianten zuerst** (Plan) | Eine Gestaltungsentscheidung an drei Seiten getroffen | Eine Runde vor dem Ausbau |
| **Ausbau zuerst** | Alle Seiten fertig, dann die Grundsatzentscheidung | Jede gebaute Seite wird noch einmal angefasst |

**Design-Feinschliff ist nachher möglich. Die Designrichtung nicht.** Formsprache, Rundungen und
Farbsystem sind Systementscheidungen — sie stecken in jedem Bauteil.

> **Kein Konflikt mit dem Zeitplan:** Die Variantenrunde läuft **parallel zu A0 und A1**. Der
> Bedarfsscheck in A1 entsteht ausdrücklich „ohne das umgebende Website-Design"
> (`REIHENFOLGE.md`). Beides blockiert einander nicht.

---

## 5. Was „alle Funktionen im ersten Schritt" konkret heißt

| Stufe | Inhalt | Im ersten Schritt? |
|---|---|---|
| **A0–A3** | 18 Tabellen, 87 Testfälle — von der Anfrage bis `live` | **ja.** Das ist der vollständige Weg eines echten Kunden |
| **B** | Öffnungszeiten selbst pflegen, 2 Tabellen, 1 Testfall | klein. Kann mitlaufen |
| **C** | Mollie-Automatik, Mahnwesen, Registrar-Anbindung, Auswertungen | **nein** — und das ist der Kern von `REIHENFOLGE.md` |

**Der Unterschied ist Handarbeit, nicht fehlende Funktion.** In A2 wird eine Rechnung von Hand
angelegt und der Zahlungsstatus von Hand gesetzt. Der Kunde sieht dieselbe Rechnung wie später.
Was fehlt, ist der Webhook — der lohnt sich ab dem Kunden, bei dem Handarbeit lästig wird.

> **Die Warnung stammt aus dem eigenen Masterkonzept:** *„Die größte Gefahr ist nicht die Preis-
> oder Angebotslogik, sondern der Anspruch, den kompletten Kundenbereich mit voller
> Automatisierung vollständig vor dem ersten Standardverkauf zu bauen."*

---

## 6. Reihenfolge ab heute

| # | Schritt | Wer | Blockiert |
|---|---|---|---|
| 1 | **§7b entscheiden** | Betreiber | ja — vor dem Datenmodell |
| 2 | **Entwicklungsweg wählen**, Docker oder nativ, in §4 eintragen | Betreiber | ja — vor dem ersten Befehl |
| 3 | **A0 bauen** | Bau | — |
| 4 | **Designvarianten**, parallel zu 3 | Bau | — |
| 5 | **Hoster klären** — Cron und Mailversand | Betreiber | vor A1-Ende |
| 6 | **A1, A2, A3** | Bau | — |
| 7 | Rechtstexte entwerfen, dann anwaltlich prüfen | Bau, dann Anwalt | vor Veröffentlichung |
| 8 | Gründername, Foto, Demoprojekte | Betreiber | Startseiten-Sektion 8 |

**Nur die Punkte 1 und 2 stehen zwischen heute und dem ersten Commit.**
