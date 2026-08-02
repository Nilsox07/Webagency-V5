# Offene Entscheidungen

**Stand:** 02.08.2026, Stufe A und B abgeschlossen · Korrektur nach §4b.6

Diese Datei hält fest, was **gemeldet statt erfunden** wurde. Der Auftrag ist eindeutig:
*„Fehlt eine Zahl ganz — schreib sie nicht. Bau drumherum, halte den Punkt hier fest und
mach weiter."* Und: *„Erfinde nie: einen Rechtstext · eine Anschrift · einen Kundennamen
oder eine Referenz · einen Preis oder eine Frist."*

Zwei Abschnitte: was **offen** ist und deshalb nicht gebaut wurde, und was **entschieden**
wurde — mit der Stelle, die entschieden hat.

---

## Offen — gemeldet, nicht erfunden

Die Nummern bleiben, auch wo eine Zeile verschwindet. **2, 4, 6, 7 und 8 sind am 02.08.2026
entschieden worden** und stehen unten; wer die alte Nummer sucht, findet sie dort und nicht
als Lücke, die er sich erklären muss. Offen sind noch drei: 1, 3 und 5.

| # | Punkt | Was fehlt | Was stattdessen gebaut wurde |
|---|---|---|---|
| 1 | **Feldliste je Aufgabe der Vorlage** (§9.3) | §9.3 nennt dreizehn Vorlagen mit Titel, Art und „Warum wir das brauchen" — aber nicht, **welche Felder** eine `angabe`-Aufgabe abfragt | Die dreizehn Vorlagen stehen mit genau diesen drei Angaben. Eine `angabe` bekommt ein freies Antwortfeld, kein erfundenes Formular |
| 3 | **Zwei weitere Ampelbedingungen** (§4b.4) | Die Ampel kennt vier Stufen; für `orange` nennt der Text zwei Auslöser und deutet weitere an, ohne sie zu benennen | Die zwei benannten Auslöser sind gebaut und deterministisch geprüft (Testfall 39) |
| 5 | **Antwortfeld je Rückmeldung** (§9.2 „Feedback") | §9.2 nennt „je Eintrag Antwortfeld und Statuswechsel". `feedback_items` hat in §4 **kein** Antwortfeld und keinen Status je Eintrag | Die Rückmeldungen stehen im internen Bereich vollständig lesbar unter ihrer Runde. Die Runde wird als Ganzes als eingearbeitet vermerkt — das ist der Weg, den §5.6a beschreibt. Ein Antwortfeld bräuchte zwei Spalten, die im Datenmodell fehlen |
| ~~6~~ | ~~**Tabelle für das Kontaktformular**~~ | **Erledigt am 02.08.2026 — und die alte Antwort war falsch.** Portal-Lastenheft **§4b.6** regelt den Fall ausdrücklich: „Es versendet ausschließlich eine E-Mail an SARTU und erzeugt **keinen** Datensatz." Die Stelle war beim ersten Bau nicht gefunden worden. Es fehlte keine Tabelle — es sollte gar keine geben | Siehe „Entschieden und eingebaut" |

---

## Entschieden und eingebaut

| Punkt | Entscheidung | Wo sie steht |
|---|---|---|
| **Angebotsgültigkeit** | 30 Kalendertage ab Versand, im Formular vorbelegt und änderbar | Auftrag §6. Im Portal-Lastenheft §4 steht die Zahl nicht — deshalb hier festgehalten. `AngebotDienst::GUELTIGKEIT_TAGE` |
| **`ADMIN_NOTIFY_EMAIL`** | Feld `operator_settings.benachrichtigung_email`, gepflegt unter `/admin/einstellungen/betrieb`. `/admin` führt die Zeile unter „fehlt noch" | Auftrag §6. §1.5 nannte den Wert unter „Erforderliche Werte", erhob ihn aber in keinem der acht Schritte. `migrations/019` |
| **Die Tragweite von `benachrichtigung_email` ist gewachsen** | Bis zum 02.08.2026 stand hier: Ist das Feld leer, unterbleibt „**nur diese eine**" Benachrichtigung. Mit §4b.6 stimmt das nicht mehr — das Kontaktformular hat keinen Datensatz mehr, der es auffängt, und steht ohne Empfänger **still**. `/kontakt` zeigt dann den Ausweichweg statt des Formulars (§0.3b) | `Kontaktanfrage::empfaengerVorhanden()`. **Der Wert gehört damit in die Prüfliste vor dem Livegang** — siehe `LIVEGANG.md` |
| **Selbstpflege: Bilder, Team, Anfragen** | Die drei Zeilen entfallen. Sektion 2 der Startseite behält **elf** Punkte. Keine neuen Tabellen | Auftrag §6, Entscheidung vom 01.08.2026 (§5a). `REIHENFOLGE.md` hatte die Lücke gemeldet: es fehlen `site_content`, `media_assets`, `website_inquiries` |
| **Stellen- und Karriereseite** | Erscheint auf keiner Seite. Nicht erwähnt, nicht angekündigt, nicht verlinkt | Auftrag §6 (§7b) |
| **§19 UStG** | Zur Laufzeit aus `operator_settings.kleinunternehmer`. Steht dort `ja`, erscheint „zzgl. USt." nirgends. Keine Baurentscheidung | Auftrag §6. Das Feld gab es bereits in `migrations/007` — keine Migration nötig. `Zahlungsstatus::umsatzsteuer()` |
| **Anschrift, Telefon, E-Mail** | Zur Laufzeit aus `operator_settings`. Keine Bauentscheidung, kein Wert im Quelltext | Auftrag §6. Testfall 83 prüft es für `/login` |
| **Gründername** | Nur im Impressum. Nirgends sonst, auch nicht in Bildbeschreibungen | Auftrag §6 (§5.1) |
| **Gründerfoto** | Fehlt es, entfällt **Sektion 8 der Startseite ganz**. Kein leerer Rahmen an einer Vertrauensstelle | Auftrag §6. Betrifft Stufe B |
| **Vier Kundenzeilen, „genau drei" Erklärungen** (§5.1a) | Beide Stellen bleiben gültig. `wer` sagt, wer handelt; `erklaerung` sagt, was rechtlich zählt. Aufgelöst über die Begründung, die der Satz mitliefert: „Alle drei sind Erklärungen mit Namen und Zeitpunkt" — er zählt Erklärungen, nicht Klicks | `Projektstatus`, Klassenkommentar |
| **`teilweise_bezahlt` **und** `ueberfaellig`** (§5.3) | Der Zustand trägt die Frist, der Anzeigetext trägt beides, und die Erinnerung arbeitet auf dem **Restbetrag**, nicht auf dem Zustand — so steht es in §5.3 | `Zahlungsstatus::ausBetrag()`, `Zahlungslauf` |
| **Harte Scope-Grenze **und** „das Portal blockiert nichts"** (§5.6a) | Die Grenze wird **angezeigt**, nicht durchgesetzt. Eine Runde mit `included = false` läuft wie jede andere; der Kunde sieht vorher, dass sie nicht im Festpreis steckt | `Vorschaudienst`, Klassenkommentar. Testfall 25 |
| **BFSG-Felder am Angebot** | Zwei Fragen mit Vorgabe `nein` / `unbekannt`, Sperre beim Senden. Es sind Angaben des Kunden, keine Feststellung von SARTU | §4c und der Korrekturblock daneben. `migrations/013` |
| **Löschfristen für Anfragen** | `source_ip` nach 30 Tagen geleert, nicht umgewandelte Anfrage nach 12 Monaten gelöscht | §15, Testfälle 40 und 80 |
| **Betriebsbeginn nachträglich verschieben** (§5.7 Sonderfall) | Eigene, protokollierte Aktion mit Pflicht-Grundlagentext — §12 nennt sie ausdrücklich neben `due_date`. Die Mindestlaufzeit wird **gerechnet**, nie getippt | `Admin\VorschauSteuerung::betriebsbeginn()`, Testfall 53b |
| **`domain_status` vollständig** | Alle sechs Zustände, alle Pflichtfelder — von Hand gepflegt. Verschoben ist allein die Registrar-Anbindung (Stufe C) | `REIHENFOLGE.md` ausdrücklich: „Eine Teiltabelle jetzt bedeutet eine Folgemigration später." `migrations/022` |
| **Testfall 18 stand in der falschen Etappe** | Er verlangt `approvals` mit `kind = abnahme`; geprüft war die Faktenfreigabe (`kind = inhalte`) — das ist Fall 27. `REIHENFOLGE.md` ordnet 18 der Etappe A3 zu | `tests/LivegangTest.php`, Kommentar in `tests/AuftragsstreckeTest.php` |
| **Schwelle für „knappe Frist"** (§8.1) | **Drei Tage.** Dieselbe Zahl wie beim Angebotsablauf in §10 — eine zweite Vorwarnzeit daneben wäre eine Zahl ohne Grund, und zwei davon im selben Bereich lernt niemand. Der Hinweis erscheint, sobald `due_date` in drei Tagen oder weniger erreicht ist | Entscheidung des Betreibers vom 02.08.2026. `Zahlungsstatus::KNAPP_TAGE`, `fristKnapp()`. Mit ihr entstand **Block 3 des Cockpits** (§8.1), der bis dahin fehlte |
| **Anhebung der Speichergrenze je Kunde** | **Bleibt, wie sie ist:** 500 MB je Organisation, hart. Ein Adminfeld dafür bräuchte eine Obergrenze, die niemand festgelegt hat | Entscheidung des Betreibers vom 02.08.2026. Testfall 79 prüft die Grenze unverändert |
| **Fokusfalle im mobilen Menü** (Website §3) | **Gebaut.** Eigene Datei `/public/assets/js/menue.js`, 1,8 KB, mit `defer` geladen, kein Zeileninhalt. `script-src 'self'` deckt sie ab — die CSP bleibt unverändert. Das Menü bleibt ein `details` und ohne Skript vollständig bedienbar; das Skript **fügt nur hinzu** | Entscheidung des Betreibers vom 02.08.2026. Der Satz dazu steht wörtlich im Kopf der Datei. `SecurityHeadersTest` erlaubt genau diese eine Form und wird dadurch schärfer, `WebsiteTest` prüft die Paarung Menü ↔ Skript und die Größe |
| **`KEYWORD_VALIDATION.md`** (Website §17) | **Erzeugt, nicht getippt:** `php bin/keywords.php` fährt jede Adresse aus `Launchadressen::alle()` durch den echten Router und liest Titel, H1 und Beschreibung aus der Antwort. Volumen, SERP-Typen und verwandte Fragen bleiben **leer** — Keywordstrategie §1.1: „nie geschätzt". Die Spalte „Bestätigt" füllt ein Mensch | Entscheidung des Betreibers vom 02.08.2026. `bin/keywords.php`, `KEYWORD_VALIDATION.md`. `WebsiteTest` prüft, dass keine Adresse ohne Zeile bleibt |
| **Kontaktformular ohne Datensatz** (§4b.6) | Es versendet **ausschließlich** eine E-Mail. Kein Eintrag in `leads`, keiner in `support_messages`. Honigtopf, Zeitregel und Rate-Limit bleiben — §4b.6 verlangt sie ausdrücklich | `Kontaktanfrage`. `WebsiteTest` prüft: null Zeilen, genau eine Mail |
| ~~**B2B-Bestätigung im Kontaktformular**~~ | **Zurückgenommen am 02.08.2026.** Sie stand dort nur, weil `chk_leads_bestaetigungen` beide Häkchen verlangte. Ohne `leads`-Zeile gibt es die Prüfbedingung nicht, und §11 zählt sieben Felder mit **einer** Bestätigung auf. Ein Häkchen ohne Zweck ist eine Hürde ohne Grund | `website-kontakt.php`, Kopfkommentar |
| **Die Mail trägt die ganze Rückfrage** | Beim Bedarfsscheck ist die Benachrichtigung eine Kurzmeldung **ohne** Datenauszug (§10) — der Datensatz trägt alles. Hier gibt es keinen. Stünde nur „Es ist eine Rückfrage eingegangen" darin, wäre sie weg | `Kontaktanfrage::hinausschicken()` |
| **Ein Mailfehler erreicht den Absender** | Der einzige Fall im Projekt. Überall sonst fängt der Datensatz eine gescheiterte Mail auf; hier gibt es keinen. Eine Bestätigungsseite für eine Nachricht, die nirgends ankam, wäre eine Lüge | `Kontaktanfrage::senden()`, letzter Zweig |
| **Doppelklicksperre in der Sitzung** | `leads.submission_id` ist eindeutig — ohne Zeile gibt es nichts zu vergleichen. Eine Tabelle dafür zu erfinden verstiesse gegen §4b.6 genauso wie die alte Fassung | `Kontaktanfrage`, `VERBRAUCHT`. Gegen bewusste Wiederholung hilft das Rate-Limit |
| **Schnittstelle `Versender`** | `Mailversand` ist `final` und bleibt es. Die Schnittstelle ist eine **Naht für Tests**, kein zweiter Weg nach draussen — §4b.6 macht die Mail zum einzigen Träger, damit ist ihr Versand prüfbares Verhalten | `app/services/Versender.php`, `tests/Postfach.php` |
| **Ortsnamen auf der Website** | §5 Sektion 9 und §11 nennen „Raum Dresden" und elf Umkreisorte. §0 desselben Dokuments verbietet Ortsnamen im Fließtext, solange `[GESCHAEFTSADRESSE_STATUS]` auf `offen` steht. Rangfolge: `SARTU_ENTSCHEIDUNGEN_OFFEN.md` Rang 1 schlägt Website-Lastenheft Rang 5 | Die **Aussage** steht vollständig — Reichweite und Begründung. Nur der Ortsname fehlt. `WebsiteTest` prüft, dass keiner der dreizehn Orte irgendwo erscheint |
| **`günstig` in einer gebundenen Kurzantwort** | §12 gibt den Satz wörtlich vor und schreibt „günstig". §2 verbietet das Wort **mit Begründung** („falsche Positionierung"); §12 begründet die Wortwahl nicht. Innerhalb eines Dokuments gewinnt die Stelle mit der Begründung | `Ratgeber`, Artikel `agentur-freelancer-baukasten`: `kostet wenig`. Die Aussage bleibt |
| **Geldformat auf der Website** | Portal-Lastenheft §4a schreibt `7.900,00 €`, Website-Lastenheft §7 schreibt `7.900 €`. Rang 4 schlägt Rang 5 | Im Fließtext `1.490,00 €` über `Format::euro()`. In Titel und Beschreibung, wo §7 den Wortlaut bindet, `1.490 €` — beide aus derselben Tabelle erzeugt, keine getippt |
| **`GPTBot` in `robots.txt`** | §17 verlangt eine dokumentierte Entscheidung, nennt aber keine | **Zugelassen.** Wer veröffentlichte Zahlen vom Training ausschließt, schließt sich aus den Antworten aus, in denen er vorkommen will. Begründung im Kopf von `app/Wurzeldateien.php` |
| **Rechtstexte als Dateien, nicht in der Datenbank** | Auftrag §6 erlaubt Entwürfe mit Kopfzeile. Ein Entwurf in `legal_texts` ist eine Zeile davon entfernt, freigegeben zu werden | Fünf Entwürfe in `rechtstexte-entwuerfe/`, je mit `ENTWURF — NICHT GEPRÜFT, NICHT VERÖFFENTLICHEN`. Jede Anschrift als `[[PLATZHALTER]]` — genau die Markierung, die §14a sucht |
