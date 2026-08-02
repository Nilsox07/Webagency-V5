# Modellplan

**Für die Abschluss-Sitzung vom 02.08.2026.** Wer welchen Abschnitt bearbeitet, und warum.

Verfügbar sind `claude-opus-5`, `claude-sonnet-5` und `claude-haiku-4-5-20251001`.

---

## Die Aufteilung, wie der Betreiber sie vorgegeben hat

| Modell | Abschnitte | Warum dieses Modell |
|---|---|---|
| **Opus 5** | **§3** — die Korrektur am Kontaktformular (§4b.6) · das Zusammenführen der beiden Zweige · **jede** Änderung an einer Migration | Ein Fehler kostet hier einen Datensatz zu viel, einen verlorenen Zweig oder ein Schema, das sich nicht zurücknehmen lässt |
| **Sonnet 5** | **§5** — die Messungen · **§6** — `LIVEGANG.md` · `KEYWORD_VALIDATION.md` · Texte | Menge und Sorgfalt, aber kein Urteil, das ein Schema oder eine Frist verändert |
| **Haiku 4.5** | Prüflisten abhaken · `grep`-Läufe · Formatieren | Mechanisch, kein Urteil nötig |

**Die Regel dahinter, aus `PROMPT_NEUE_SESSION_LIVEGANG.md`:** Nie in Haiku entscheiden. Taucht
beim mechanischen Arbeiten eine Frage auf, wird hochgeschaltet — nicht geraten.

---

## Was tatsächlich gelaufen ist

> **Diese Sitzung lief vollständig auf `claude-opus-5`.** Ein Umschalten hat nicht
> stattgefunden.

Das wird hier festgehalten, nicht beschönigt: Die Aufteilung oben ist der Plan, nicht das
Protokoll. Wer die Kosten dieser Sitzung nachrechnet, rechnet mit einem Modell, nicht mit
dreien.

### Warum es dabei blieb

| Abschnitt | Geplant | Warum Opus |
|---|---|---|
| **§3** Kontaktformular | Opus | wie geplant |
| **§4** Die vier Entscheidungen | nicht benannt | Beim Einbauen der Frist zeigte sich, dass §8.1 Block 3 fehlt. Aus „eine Zahl eintragen" wurde „einen Block bauen" |
| **§5** Messungen | Sonnet | Zwei Messungen fanden Fehler: 2,05 : 1 Kontrast und ein Menü, das mit `Esc` nicht zuging. Beides musste **im laufenden Abschnitt** behoben und neu gemessen werden |
| **§6** `LIVEGANG.md` | Sonnet | Der Abschnitt endete mit einem neuen Befehl (`bin/startklar.php`), weil die Startsperre nur als Anzeige existierte. Das ist Sicherheitscode |

**Der ehrliche Satz dazu:** Ein Wechsel nach §3 wäre möglich gewesen. Er unterblieb, weil in
§4 und §5 je ein Befund kam. Beide verschoben die Arbeit von „ausführen" nach „entscheiden".
Die Regel lautet dann **hoch**schalten, nicht runter.

### Was in Sonnet oder Haiku gelaufen wäre

Diese vier Arbeiten haben nichts entschieden. Sie hätten günstiger laufen können:

- die Tabellen in `MESSUNGEN.md` aus den Messdaten formatieren
- `KEYWORD_VALIDATION.md` erzeugen (der Befehl schreibt sie, nicht das Modell)
- die Prüfberichte nach `SARTU_TEXTREGELN.md` zählen (`tools/textpruefung.py` zählt)
- die Zeilen in `OFFENE_ENTSCHEIDUNGEN.md` und `OFFENE_PRUEFUNGEN.md` umschreiben

**Für die nächste Sitzung:** Sie stehen am Ende eines Abschnitts, nicht mittendrin. Dort
lässt sich umschalten, ohne einen Befund zu verlieren.

---

## Was nie in einem kleineren Modell läuft

Unverhandelbar, unabhängig vom Abschnitt:

- **Migrationen.** MySQL nimmt eine Schemaänderung nicht zurück; ein `ROLLBACK` ist dort ein
  Versprechen, das die Datenbank nicht hält
- **Mandantentrennung.** `organization_id` kommt aus der Sitzung, sonst nirgendwoher.
  `tests/TenantIsolationTest.php` wird nie abgeschwächt, um grün zu werden
- **Zahlungsstatus und Fristen.** Bei Geld und Fristen ist `reason` Pflichtfeld
- **Alles, was einen Rechtstext berührt.** Freigegeben wird von einem Menschen
- **Zusammenführen von Zweigen**

---

## Prüfbericht

`SARTU_TEXTREGELN.md` §2. Gezählt mit `tools/textpruefung.py` am 02.08.2026.

```text
TEXTPRUEFUNG   Seite: MODELLPLAN.md           Datum: 02.08.2026

Sätze gesamt                            27
Längster Satz                           22 Wörter      Grenze 20   → benannt
Sätze über 20 Wörter                     1             Grenze 0    → benannt
Aufzählungen >3 Glieder im Satz          0             Grenze 0
Gegensatzformel                          5             Grenze 2    → benannt
Treffer Wortliste (Füllwörter)           0             Grenze 0
Behauptungen über Kunden / Markt         0             Grenze 0
```

**Der lange Satz ist keiner.** Das Zählskript klebt die Überschrift, die Zeile darunter und
den ersten Listenpunkt zusammen. Sein eigener Kopf warnt davor.

**Fünf Gegensatzformeln.** Diese Datei sagt an fünf Stellen, was **nicht** geschehen ist.
Kein Umschalten. Kein kleineres Modell für Migrationen. Kein Runterschalten bei einem Befund.

Ein Modellplan, der das Abweichen verschweigt, ist eine Kostenschätzung ohne Kosten.
