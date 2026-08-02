# Rechtstexte — Entwürfe

**ENTWURF — NICHT GEPRÜFT, NICHT VERÖFFENTLICHEN**

Fünf Entwürfe: `impressum` · `datenschutz` · `agb` · `avv` · `tom`.

---

## Warum sie hier liegen und nicht in der Datenbank

`legal_texts` ist die Quelle der veröffentlichten Texte. Ein Entwurf, der dort steht, ist eine
Zeile davon entfernt, freigegeben zu werden — und `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §2 steht auf
**offen**.

Die Entwürfe liegen deshalb als Dateien. Der Weg in die Anwendung führt über
`/admin/rechtstexte/{slug}`: Text einfügen, speichern. Der Zustand bleibt dabei `entwurf` —
die Freigabe ist eine eigene, getrennte Handlung.

## Was diese Entwürfe sind — und was nicht

**Sie sind ein Gerüst.** Sie nennen die Abschnitte, die nach § 5 DDG, Art. 13 DSGVO und
Art. 28 DSGVO vorkommen müssen, und beschreiben die Verarbeitungen, die diese Anwendung
tatsächlich durchführt — nachgelesen im Code, nicht angenommen.

**Sie sind keine Rechtsberatung und keine geprüften Texte.** Ein plausibel klingender
Rechtstext ist gefährlicher als gar keiner: Er wird veröffentlicht, weil er fertig aussieht.

## Jede Anschrift fehlt mit Absicht

Wo Betreiberdaten stehen müssen, steht `[[PLATZHALTER]]`. Zwei Gründe, jeder allein
ausreichend:

1. **Es wird nichts erfunden.** Anschrift, Registergericht, Steuernummer und
   Verantwortlicher stehen in `operator_settings` und werden dort gepflegt (§1.4a)
2. **Die Startsperre sucht genau diese Markierung.** Website-Lastenheft §14a Bedingung 1: Die
   produktive Veröffentlichung bricht ab, wenn `/impressum` oder `/datenschutz` sie enthält

## Was der Betreiber tun muss

| Schritt | |
|---|---|
| 1 | Entwurf von einem Menschen mit juristischer Ausbildung prüfen lassen |
| 2 | Betreiberdaten unter `/admin/einstellungen/betrieb` vollständig eintragen |
| 3 | Geprüften Text unter `/admin/rechtstexte/{slug}` einfügen |
| 4 | Erst dann freigeben |

Schritt 1 ist nicht optional und nicht nachholbar.
