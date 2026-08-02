# Startprompt für die Mail-Session

**Stand:** 02.08.2026 · `main` ist vollständig, 264 Tests grün
**Zweck:** Diesen Block in eine neue Claude-Code-Session kopieren. Alles darunter ist der Prompt.

---

Du schließt die letzte Lücke vor dem Livegang: **sechs Mails, die das Lastenheft vorschreibt und
die niemand gebaut hat.** Dazu ein Block im Kundenbereich. **Du arbeitest durch, ohne Rückfrage.**

## 1. Modellplan

Schreib in deine erste Antwort eine Tabelle, welches Modell welche Aufgabe übernimmt.
Verfügbar sind `claude-opus-5`, `claude-sonnet-5` und `claude-haiku-4-5-20251001`.

| Modell | Wofür |
|---|---|
| **Opus 5** | Die Auslösepunkte — an welcher Stelle im Ablauf welche Mail entsteht · der tägliche Lauf · Abschnitt 4 |
| **Sonnet 5** | Mailtexte nach `/sartu-texter`, Tests, Ansicht |
| **Haiku 4.5** | Prüflisten, `grep`-Läufe, Formatierung |

## 2. Wo du anfängst

**SARTU** ist eine Webdesign-Agentur für regionale Betriebe. **Alle Bauetappen sind fertig:**
20 von 20 Tabellen, 88 von 88 Testfällen, 264 Tests grün gegen echtes MariaDB. Alles liegt auf
`main`.

**Lies zuerst:** `STAND.md` · `LIVEGANG.md` §6 · `OFFENE_ENTSCHEIDUNGEN.md` · `MESSUNGEN.md`.

**Ein Muster, das dich Zeit spart.** Die vorige Sitzung hat das Kontaktformular in `leads`
abgelegt, weil sie fragte *„welche Tabelle gehört dazu"* und keine fand. Die Antwort stand einen
Abschnitt weiter in §4b.6: Es soll **gar keine** geben. **Such immer erst den Abschnitt, der die
Sache selbst regelt**, bevor du aus dem Fehlen einer Vorgabe etwas ableitest.

## 3. Die sechs Mails aus §10

Der Wortlaut steht in Portal-Lastenheft §10, Zeilen 1791 bis 1809. **Du erfindest keinen Satz.**
Wo eine geschweifte Klammer steht, wird eingesetzt, was dort steht — sonst nichts.

| # | Auslöser | Betreff | Rumpf laut §10 | An wen |
|---|---|---|---|---|
| 1 | **Angebot gesendet** (`angebot_gesendet`) | `Ihr Angebot von SARTU liegt bereit` | `Ihr Angebot mit Umfang, Preis und Zahlungsplan liegt im Portal. Gültig bis {Datum}.` | Kunde |
| 2 | **Angebot angenommen** | `Angebot angenommen: {Organisation}` | interne Kurzmeldung | Admin |
| 3 | **Neue Aufgaben** stehen bereit | `Es liegen Aufgaben für Sie bereit` | `Wir brauchen ein paar Angaben von Ihnen. Das dauert meist 15 bis 25 Minuten.` | Kunde |
| 4 | **Faktenfreigabe erteilt** | `Freigabe bestätigt — wir starten` | `Danke für die Freigabe. Wir beginnen mit der Produktion. Fertigstellung voraussichtlich in {min}–{max} Werktagen.` | **beide** |
| 5 | **Antwort auf eine Nachricht** | `Antwort auf Ihre Nachricht` | Antworttext plus Link in den Kundenbereich | Kunde |
| 6 | **Angebot läuft in 3 Tagen ab** | `Ihr Angebot gilt noch bis {Datum}` | `Ihr Angebot läuft am {Datum} ab. Danach stellen wir es Ihnen gern neu aus — melden Sie sich einfach.` | Kunde |

**Nummer 1 ist die dringendste.** Ohne sie liegt das Angebot im Kundenbereich, und niemand schickt
den Kunden hin. Der Verkaufsweg reißt genau dort ab.

### Drei Regeln für alle sechs

1. **Das Wort „Portal" kommt in keinem Kundentext vor.** §10 schreibt es, die Sprachregel verbietet
   es nach außen. Setz `Kundenbereich` und halte die Abweichung so fest, wie es
   `Angebotstexte::ABWEICHUNG_VOM_WORTLAUT` schon einmal tut
2. **Jede Mail läuft über die vorhandene Versandschnittstelle**, nicht über einen neuen Weg. Das
   Postfach aus der vorigen Sitzung ist der Prüfstand
3. **Je Mail ein Test:** Auslöser ausgelöst, genau **eine** Nachricht im Postfach, Betreff und
   Empfänger geprüft. Ein Zähler allein genügt nicht

### Nummer 6 braucht einen Lauf, den es noch nicht gibt

`OFFENE_PRUEFUNGEN.md` hält fest, dass `AdminAngebote::abgelaufeneSetzen()` bereitsteht und **von
keinem Lauf aufgerufen wird**. Beides gehört zusammen in den täglichen Cronlauf:

- Angebote, deren `valid_until` überschritten ist, gehen auf `abgelaufen`
- Angebote, deren `valid_until` in **genau drei Tagen** erreicht ist, lösen Mail 6 aus — **einmal**,
  nicht täglich

**Die Wiederholungssperre braucht ein Feld.** `offers` hat keines dafür. Leg eine eigene Migration
an — die Vorlage ist `invoices.reminder_sent_at`, dort ist dasselbe Problem schon gelöst. Nenn es
gleich und begründe die Migration im Kopf der Datei.

## 4. Block 4 des Kundenbereichs — hier entschieden

`LIVEGANG.md` §6.2 meldet, §8.1 verlange „Letzte Aktivität", ohne festzulegen, welche Ereignisse
zählen. **Der Abschnitt legt es fest** — er nennt fünf, jedes im fertigen Wortlaut:

| Klartext laut §8.1 | Ereignis im Prüfprotokoll |
|---|---|
| `Angebot angenommen` | `angebot_angenommen` |
| `Zahlung eingegangen` | `zahlungsstatus_geaendert`, sobald ein Betrag verbucht ist |
| `Vorschau bereitgestellt` | `projektstatus_geaendert` auf `vorschau` |
| `Feedback eingereicht` | `korrekturrunde_eingereicht` |
| `Website online` | `projektstatus_geaendert` auf `live` |

**Genau diese fünf, keine sechste.** Wer eine hinzufügt, erfindet einen Klartext, den niemand
festgelegt hat. Angezeigt werden die letzten fünf Einträge mit Datum, gefiltert nach der
Organisation **aus der Sitzung**.

**Ein Prüfprotokoll ist keine Kundenansicht.** Es enthält Adminereignisse, IP-Adressen und
Begründungen. Filtere auf diese fünf Aktionen. Ausgegeben werden **nur** Klartext und Datum — nie
ein Feldwert, nie eine Begründung, nie eine IP.

## 5. Was offen bleibt — und offen bleiben soll

Drei Punkte in `OFFENE_ENTSCHEIDUNGEN.md` brauchen Felder, die im Datenmodell fehlen. **Fass sie
nicht an, erfinde nichts, melde sie nicht erneut:**

- **Punkt 1** — welche Felder eine Aufgabe der Art `angabe` abfragt
- **Punkt 3** — die zwei weiteren Ampelbedingungen
- **Punkt 5** — Antwortfeld und Status je einzelner Rückmeldung

Keiner davon blockiert den Livegang.

## 6. Die Regeln, die nicht verhandelbar sind

1. **Der Mandantentest wird nie abgeschwächt**, um grün zu werden. Er wächst mit, wenn eine Route
   dazukommt
2. **Jede Abfrage filtert nach `organization_id` aus der Sitzung**, nie aus der Anfrage
3. **Kein Rechtstext geht in den Zustand `freigegeben`** — das macht ein Mensch
4. **Kein Secret ins Repository**
5. **Nur PDO mit vorbereiteten Anweisungen.** SQL steht ausschließlich in `/app/data`
6. **Migrationen werden nie geändert, nur ergänzt**
7. **Nichts erfinden.** Kein Satz, der nicht in §10 steht. Keine Zahl, die in den Unterlagen fehlt
8. Für jeden Text, den ein Mensch liest: **`/sartu-texter`**, mit Prüfbericht

## 7. Wie du arbeitest

1. **Modellplan** schreiben
2. Umgebung hochfahren, `vendor/bin/phpunit` einmal laufen lassen — **grün, bevor du anfängst**
3. **Mail 1 zuerst**, mit Test. Sie ist die einzige, die einen Verkaufsweg repariert
4. Dann Mail 2 bis 5, dann die Migration und Mail 6 mit dem täglichen Lauf
5. Dann Abschnitt 4
6. Nach jedem Schritt: `/code-review`, dann `/simplify`. Am Ende: **`/security-review`**
7. **Committen und pushen** nach jedem Schritt, auf `main`

**Am Ende ein Bericht mit vier Teilen:**

- die Testzahl, selbst ausgeführt
- je Mail eine Zeile: Auslöser, Empfänger, Test
- was im Prüfprotokoll für Block 4 gefiltert wird
- `STAND.md`, `OFFENE_PRUEFUNGEN.md` und `LIVEGANG.md` §6 nachgezogen — §6.1 und §6.2 sind danach
  erledigt und werden gestrichen

**Rate nie.** Was fehlt, kommt nach `OFFENE_ENTSCHEIDUNGEN.md`.

**Was nicht ausgeführt wurde, wird nicht als geprüft gemeldet.**
