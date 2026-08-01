# Audit vor Baubeginn

**Stand:** 31.07.2026
**Geprüft:** 16 Bauunterlagen, 8.839 Zeilen. Code: **0 Zeilen.**
**Zweck:** Der letzte Durchgang, bevor gebaut wird. Danach kostet jeder Fund das Zehnfache.

> ## Stand der Abarbeitung — 31.07.2026
>
> **Alle drei Sperren und alle zehn Fehler sind eingearbeitet.** Was jetzt noch offen ist, sind
> ausschließlich Entscheidungen, die einem Menschen gehören.
>
> | Fund | Stand | Wo eingearbeitet |
> |---|---|---|
> | **S1** kein Rückkanal | **behoben** | `support_messages` von B nach **A2** · `REIHENFOLGE.md` |
> | **S2** AVV fehlt | **behoben** | `legal_texts` um `avv` und `tom` erweitert · neues Feld `audience` · Portal §15.2 · Kundenbildschirm `/vertrag` · Startsperre §14a Bedingung 8 |
> | **S3** Anmeldelink einziger Weg | **behoben** | Portal §6.3 — Telefonnummer aus den Betreiberdaten auf `/login`, auf der Bestätigungsseite und in jeder Anmeldemail |
> | **F1** Benachrichtigung fehlt in §10 | **behoben** | §10, Zeile `Zahlungsstatus zurückgenommen` |
> | **F2** keine Zahlungserinnerung | **behoben** | neues §5.3a · zwei Mails · Feld `reminder_sent_at` |
> | **F3** Angebot läuft still ab | **behoben** | §10, Mail drei Tage vorher |
> | **F4** `pausiert` ohne Mail | **behoben** | §10, zwei Zeilen für Pause und Fortsetzung |
> | **F5** 7 Stationen für 11 Status | **behoben** | §8.1, vollständige Zuordnungstabelle |
> | **F6** Teilzahlung nicht abbildbar | **behoben** | Feld `paid_cents` · Status `teilweise_bezahlt` · §5.3 |
> | **F7** kein Speicherlimit | **behoben** | §11 — 500 MB je Organisation, Prüfung des freien Serverplatzes |
> | **F8** Anfragen ohne Löschfrist | **behoben** | neues §15.1 — 12 Monate |
> | **F9** Aufbewahrungsfrist ohne Zahl | **behoben** | §15.1 — acht Jahre für Rechnungen |
> | **F10** `Lumi`, toter Verweis `§2.3` | **behoben** | Website-Lastenheft |
> | **E1** BFSG | **strukturell vorbereitet, inhaltlich offen** | Pflichtzeile in `exclusions` (§4c) mit vorsichtigem Vorbelegungstext · Entscheidung als `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §6 |
> | **E2** ein Benutzer je Kunde | **dokumentiert, Formulierung offen** | §7 |
> | **E3** Vertragsende im Portal | **bewusst verschoben** | §7a — spätestens vor dem zwölften Betriebsmonat |
>
> **Sieben neue Testfälle** (77–83) prüfen die neuen Regeln. Gesamtzahl **88**, Zuordnung je Fall
> in `REIHENFOLGE.md`.

---

## Was sauber ist

Damit die Fundliste unten nicht den Eindruck erweckt, es sei alles kaputt:

| Geprüft | Ergebnis |
|---|---|
| Preisarithmetik, alle vier Stufen | **4 von 4 richtig.** 1.490+12×59=2.198 · 3.900+12×129=5.448 · 7.900+12×249=10.888 · 12.500+12×249=15.488 |
| Abweichende Preiszahlen in Bauunterlagen | **keine.** Die einzigen anderen Beträge stehen in Wettbewerbsvergleichen und Pilotüberlegungen |
| Tabellen im Datenmodell gegen `REIHENFOLGE.md` | **20 zu 20, deckungsgleich.** Keine überzählige, keine fehlende |
| Statusübergänge §5.1a | Jeder der elf Status hat mindestens einen Weg hinein und hinaus |
| Abgelöste Stacks (Supabase, Node, Next) | Nur noch in Verbotssätzen. Keine Altlast im Bauweg |
| Verbotene Behauptungen (`wartungsarm`, `rechtssicher`) | Nur in den Verbotslisten selbst |
| Testfallzuordnung je Etappe | 81 Fälle, jeder genau einmal, Summe stimmt |

---

## Teil 1 — Sperren: vor der ersten Codezeile zu klären

### S1 — Der Kunde hat in Stufe A keinen Rückkanal

**Der Fehler:** `support_messages` steht in Stufe B. Die Kundenoberfläche verweist aber schon
vorher zweimal auf `Hilfe`:

| Stelle | Text |
|---|---|
| §8.2, abgelaufenes Angebot | *„Schreiben Sie uns über „Hilfe" — wir stellen es neu aus."* |
| §8.7, Grenzen der Selbstpflege | *„…schreiben Sie uns dazu einfach über „Hilfe"."* |

**Warum das schwer wiegt:** Der Kunde meldet sich ausschließlich per Anmeldelink an. Er hat keine
Telefonnummer im Portal, keine E-Mail-Adresse, kein Formular. Läuft sein Angebot ab oder hat er
eine Rückfrage zu einer Aufgabe, **endet der Weg**. Ein Kundenbereich ohne Rückkanal ist kein
reduzierter Umfang, sondern ein kaputter Ablauf.

**Empfehlung:** `support_messages` nach **A2**. Eine Tabelle mit vier Feldern, ein Formular, eine
Adminansicht. Der Aufwand ist klein, die Lücke ist es nicht.

### S2 — Der Auftragsverarbeitungsvertrag mit dem Kunden fehlt in beiden Lastenheften

**Der Sachverhalt:** SARTU betreibt die Website des Kunden und verarbeitet die Anfragen, die dort
eingehen. Damit ist SARTU **Auftragsverarbeiter für den Kunden** nach Art. 28 DSGVO. Ein Vertrag
darüber ist keine Kür.

**Was dasteht:** Portal-Lastenheft §15 sagt: *„Auftragsverarbeitungsvertrag mit Hoster und
Mailversand ist Sache des Betreibers."* Das ist die **andere Richtung** — SARTU gegenüber seinen
Dienstleistern. Der Vertrag **mit dem Kunden** kommt in keinem der beiden Lastenhefte vor.

Das Masterkonzept kennt ihn (*„AVV mit Kunde und mit Subunternehmern"*). Nach der Rangfolge in
`UEBERGABE_DATEILISTE.md` ist das Masterkonzept aber Nachschlagewerk — gebaut wird aus den
Lastenheften.

**Drei Folgen, alle ungelöst:**

1. **Der Vertrag hat keinen Ort.** `legal_texts.slug` kennt nur `impressum`, `datenschutz`, `agb`
2. **Die Anlage fehlt.** Zu jedem AVV gehören technische und organisatorische Maßnahmen. Suche über alle Dateien: **null Treffer**
3. **Das Verzeichnis von Verarbeitungstätigkeiten** nach Art. 30 ist ebenfalls nirgends erwähnt

**Empfehlung:** `legal_texts.slug` um `avv` und `tom` erweitern. Beide durchlaufen dieselbe
Freigabestrecke wie die anderen Rechtstexte (`entwurf` → `in_pruefung` → `freigegeben`). Die
Startsperre §14a greift damit automatisch.

### S3 — Der Anmeldelink ist der einzige Weg hinein

**Der Sachverhalt:** Kunden melden sich ausschließlich per Magic Link an. Kein Passwort, kein
zweiter Weg. Landet die Mail im Spam oder wird sie vom Mailserver des Kunden verworfen, ist der
Kunde **ausgesperrt** — und kann das niemandem melden, weil der Meldeweg im Portal liegt (siehe S1).

**Das ist kein theoretischer Fall.** Handwerksbetriebe nutzen häufig Postfächer beim
Provider mit aggressiven Filtern.

**Zu entscheiden:** ein zweiter Weg. Möglich wäre ein Zugangscode zum Abtippen statt eines Links,
oder eine im Portal und in jeder Mail sichtbare Telefonnummer. **Nicht** empfohlen: ein Passwort
zusätzlich einführen — das kehrt die Entscheidung gegen Passwörter um.

---

## Teil 2 — Fehler: eine richtige Antwort, keine Entscheidung nötig

### F1 — §12 verspricht eine Benachrichtigung, §10 kennt sie nicht

§12: *„Die Rücknahme ist eine eigene protokollierte Aktion … und erzeugt eine Benachrichtigung an
den Kunden."* In der Mailtabelle §10 gibt es keine solche Zeile. Zwei Abschnitte desselben
Dokuments widersprechen sich.

### F2 — Keine Zahlungserinnerung, obwohl der Status automatisch umspringt

§5.3 setzt `ueberfaellig` täglich automatisch. §10 verschickt dazu **nichts**. Der Kunde erfährt
es nur, wenn er sich einloggt — und er loggt sich nur ein, wenn ihn eine Mail dazu bringt.

Damit läuft die einzige Geldeintreibung über den Zufall. Eine Erinnerungsmail bei Überschreitung
und eine zweite nach sieben Tagen sind der Mindeststand.

### F3 — Ein ablaufendes Angebot kündigt sich nicht an

`offers.valid_until` existiert. Läuft das Datum ab, ist der Zustand eine Sackgasse, aus der nur
Handarbeit herausführt. Es fehlt eine Mail drei Tage vorher.

### F4 — `pausiert` wird dem Kunden gezeigt, aber nicht mitgeteilt

§5.1a schreibt `pause_reason` als Pflichtfeld vor, ausdrücklich *„wird dem Kunden angezeigt"*.
Angezeigt wird es im Portal. Eine Mail dazu gibt es nicht. Ein Kunde, dessen Projekt stillsteht,
erfährt den Grund nur, wenn er zufällig nachsieht.

### F5 — Die Fortschrittsanzeige hat sieben Stationen für elf Status

§8.1 nennt: `Angebot · Zahlung · Angaben · Produktion · Vorschau · Abnahme · Online`.
Für vier der elf Status ist die Station **nicht bestimmt**:

| Status | Welche Station? |
|---|---|
| `angebot_angenommen` | Angebot oder Zahlung |
| `korrektur` | Vorschau oder eine eigene |
| `launch_vorbereitung` | Abnahme oder Online |
| `pausiert` | keine passt |

Ohne Festlegung entscheidet das der Bau — und die Anzeige stimmt dann für vier Fälle nicht mit
dem Text darunter überein.

### F6 — Eine Teilzahlung lässt sich nicht abbilden

`invoices.status` kennt `entwurf · gesendet · bezahlt · ueberfaellig · storniert`. Zahlt ein Kunde
600 € auf eine Rechnung über 745 €, gibt es keinen Zustand dafür und kein Feld für den Betrag.
Der Admin muss zwischen „ist bezahlt" (falsch) und „ist offen" (auch falsch) wählen.

Das ist kein Randfall — Teilzahlungen sind bei Beträgen dieser Größe üblich.

### F7 — Kein Speicherlimit je Organisation

§11 begrenzt **je Aufgabe**: 20 MB pro Datei, 10 Dateien. Es gibt keine Obergrenze je Kunde und
keine für das gesamte System. Ein Kunde mit dreizehn Aufgaben kann 2,6 GB hochladen, ohne eine
Regel zu verletzen. Auf klassischem Hosting ist das ein Betriebsrisiko.

### F8 — Nicht umgewandelte Anfragen werden nie gelöscht

Aus `leads` wird nach 30 Tagen die IP-Adresse geleert. Der Datensatz selbst bleibt **unbegrenzt**
liegen — mit Name, Firma, E-Mail, Telefonnummer und Freitext. Die DSGVO verlangt eine
Speicherbegrenzung. Vorschlag: automatische Löschung nach **12 Monaten** ohne Umwandlung, mit
Audit-Eintrag ohne die gelöschten Inhalte.

### F9 — Die Aufbewahrungsfrist für Rechnungen ist nicht beziffert

§15 sagt nur *„gesetzliche Aufbewahrungspflichten für Rechnungen gehen vor"*. Ohne Zahl ist die
Regel nicht ausführbar. Für Buchungsbelege sind es seit 2025 **acht Jahre**; die Frist beginnt mit
dem Ende des Kalenderjahres. Das gehört als Zahl ins Dokument, damit die Löschfunktion sie kennt.

### F10 — Zwei Kleinigkeiten aus der Verweisprüfung

| Fund | Stelle |
|---|---|
| `Lumi` steht noch in einer Überschrift, obwohl der Name abgelöst ist | Website-Lastenheft §9 |
| Verweis auf `§2.3`, das es im Website-Lastenheft nicht gibt | §5a Statusanzeige |

---

## Teil 3 — Entscheidungen, die dem Menschen gehören

### E1 — Barrierefreiheit: enthalten oder ausgeschlossen?

Das **BFSG** gilt seit 28.06.2025. Es steht im Masterkonzept und in der Ratgeberliste — in
**keinem der beiden Lastenhefte**.

Praktische Folge: Jedes Angebot hat ein Pflichtfeld `exclusions`. Es gibt keine Vorgabe, ob
Barrierefreiheit nach BFSG dazugehört. Bei einem Platzhirsch-Projekt mit Buchungsfunktion für
einen Betrieb oberhalb der Kleinstunternehmensgrenze ist das eine Haftungsfrage, keine
Geschmacksfrage.

**Zu entscheiden:** Wird BFSG-Konformität angeboten, ausgeschlossen oder je Projekt geprüft? Die
Antwort gehört in `exclusions` und in die Leistungsseiten.

### E2 — Ein Benutzer je Kunde

§2: *„Stufe 0 kennt genau einen Benutzer je Kundenorganisation."* In der Praxis beauftragt der
Inhaber und die Bürokraft füllt die Aufgaben aus. Beide teilen sich dann ein Postfach, oder der
Inhaber leitet Anmeldelinks weiter — die einmal gültig sind und nach 15 Minuten verfallen.

Die Entscheidung selbst ist vertretbar. **Sie darf nur keine Überraschung sein:** Sie gehört ins
Angebot und in die häufigen Fragen.

### E3 — Was am Vertragsende passiert, steht nirgends im Portal

Das Masterkonzept regelt es (Export, Domainübergabe, wer die Verlängerung zahlt). Die Website
verweist in den häufigen Fragen auf *„Angebot und Vertragsunterlagen"*. Im Kundenbereich gibt es
dazu **keinen Bildschirm und keinen Ablauf**.

Für Stufe A ist das vertretbar — der erste Kunde kündigt nicht in den ersten Monaten. Es sollte
als bewusst verschoben in `REIHENFOLGE.md` stehen, nicht als vergessen.

### E4 — Offen aus früheren Runden, weiterhin blockierend

| Punkt | Wo |
|---|---|
| Standardbranch auf `main` umstellen | drei Prüfungen liefen auf altem Stand |
| Rechtsform · Kleinunternehmer nach § 19 UStG · Adressstatus | `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §1, §2 |
| Klarname öffentlich | §5.1 |
| Drei Selbstpflege-Funktionen: bauen oder streichen | §5a |
| Vier Umlandorte | §1 |

---

## Zusammenfassung

| | Anzahl |
|---|---|
| **Sperren** — vor Baubeginn | **3** |
| **Fehler** — eindeutig, vor der betroffenen Etappe | **10** |
| **Entscheidungen** — gehören dem Menschen | **4** + 5 offene aus früheren Runden |

**Der Schwerpunkt hat sich verschoben.** Frühere Prüfungen fanden Widersprüche zwischen
Dokumenten. Diese Prüfung findet vor allem **Lücken am Rand des Regelfalls**: Was passiert, wenn
nicht bezahlt wird, wenn die Mail nicht ankommt, wenn der Kunde eine Frage hat, wenn das Angebot
abläuft. Der Hauptweg ist gut beschrieben. Die Abzweigungen sind es nicht.

Das ist ein normaler Reifegrad für Unterlagen dieser Größe — und der richtige Zeitpunkt, es zu
merken.
