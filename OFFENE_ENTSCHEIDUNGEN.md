# Offene Entscheidungen

**Stand:** 02.08.2026, Stufe A3 abgeschlossen

Diese Datei hält fest, was **gemeldet statt erfunden** wurde. Der Auftrag ist eindeutig:
*„Fehlt eine Zahl ganz — schreib sie nicht. Bau drumherum, halte den Punkt hier fest und
mach weiter."* Und: *„Erfinde nie: einen Rechtstext · eine Anschrift · einen Kundennamen
oder eine Referenz · einen Preis oder eine Frist."*

Zwei Abschnitte: was **offen** ist und deshalb nicht gebaut wurde, und was **entschieden**
wurde — mit der Stelle, die entschieden hat.

---

## Offen — gemeldet, nicht erfunden

| # | Punkt | Was fehlt | Was stattdessen gebaut wurde |
|---|---|---|---|
| 1 | **Feldliste je Aufgabe der Vorlage** (§9.3) | §9.3 nennt dreizehn Vorlagen mit Titel, Art und „Warum wir das brauchen" — aber nicht, **welche Felder** eine `angabe`-Aufgabe abfragt | Die dreizehn Vorlagen stehen mit genau diesen drei Angaben. Eine `angabe` bekommt ein freies Antwortfeld, kein erfundenes Formular |
| 2 | **Schwelle für „knappe Frist"** | §8.1 nennt einen Hinweis bei knapper Frist, ohne die Zahl der Tage | Kein Hinweis. Der Zustand `ueberfaellig` ist gerechnet und geprüft (Testfall 15) |
| 3 | **Zwei weitere Ampelbedingungen** (§4b.4) | Die Ampel kennt vier Stufen; für `orange` nennt der Text zwei Auslöser und deutet weitere an, ohne sie zu benennen | Die zwei benannten Auslöser sind gebaut und deterministisch geprüft (Testfall 39) |
| 4 | **Anhebung der Speichergrenze je Kunde** | §11 nennt 500 MB je Organisation, aber keinen Weg, sie im Einzelfall anzuheben | Die Grenze greift hart (Testfall 79). Kein Adminfeld dafür — das wäre eine Zahl, die niemand festgelegt hat |
| 5 | **Antwortfeld je Rückmeldung** (§9.2 „Feedback") | §9.2 nennt „je Eintrag Antwortfeld und Statuswechsel". `feedback_items` hat in §4 **kein** Antwortfeld und keinen Status je Eintrag | Die Rückmeldungen stehen im internen Bereich vollständig lesbar unter ihrer Runde. Die Runde wird als Ganzes als eingearbeitet vermerkt — das ist der Weg, den §5.6a beschreibt. Ein Antwortfeld bräuchte zwei Spalten, die im Datenmodell fehlen |

---

## Entschieden und eingebaut

| Punkt | Entscheidung | Wo sie steht |
|---|---|---|
| **Angebotsgültigkeit** | 30 Kalendertage ab Versand, im Formular vorbelegt und änderbar | Auftrag §6. Im Portal-Lastenheft §4 steht die Zahl nicht — deshalb hier festgehalten. `AngebotDienst::GUELTIGKEIT_TAGE` |
| **`ADMIN_NOTIFY_EMAIL`** | Feld `operator_settings.benachrichtigung_email`, gepflegt unter `/admin/einstellungen/betrieb`. Ist es leer, unterbleibt **nur diese eine** Benachrichtigung, und `/admin` führt die Zeile unter „fehlt noch" | Auftrag §6. §1.5 nannte den Wert unter „Erforderliche Werte", erhob ihn aber in keinem der acht Schritte. `migrations/019` |
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
