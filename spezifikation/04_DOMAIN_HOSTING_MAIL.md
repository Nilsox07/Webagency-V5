# Domain, Hosting und E-Mail

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus: Masterkonzept §6
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

---

## Domain, Hosting und E-Mail

**Grundsatz:** Der Kunde entscheidet den **Domainnamen** und bleibt **Domaininhaber**. SARTU
entscheidet und verwaltet die **technische Infrastruktur**.

- **Neue Domain:** max. **3** geprüfte Vorschläge (bevorzugt `.de`) → Verfügbarkeitsprüfung über
  INWX → Kunde bestätigt genau einen Namen → letzte Echtzeitprüfung → Registrierung **erst nach
  erster Zahlung**
- **Vorhandene Domain:** Transfer bevorzugt, sonst nur DNS anbinden. **Vor jeder Änderung**
  A/AAAA/CNAME/MX/SPF/DKIM/DMARC dokumentieren (Snapshot + Rollbackplan).
  **Bestehende E-Mail darf durch den Launch nie ausfallen**
- **E-Mail-Postfächer** sind ein eigener Drittanbieterdienst. Bei Erstbedarf **eine** Ja/Nein-Frage,
  dann Empfehlung **genau eines** Anbieters plus Fremdkosten. Kein Anbieterkarussell
- **Domaingebühr:** eine übliche Domain **bis 30 € netto pro Jahr** ist bei Verwaltung durch SARTU
  in der Betriebspauschale enthalten. Darüber hinaus (Sonderendungen, Premiumnamen) werden die
  Fremdkosten nachberechnet. **Endet der Vertrag**, überträgt SARTU die Domain kostenfrei an den
  Kunden oder einen Anbieter seiner Wahl; ab dann trägt der Kunde die Gebühr selbst
- **Hosting:** statische Auslieferung über Managed Hosting in **DE/EU**. Der Kunde wählt kein Hosting

**Nur diese fünf Kundenfragen:** 1) Domain vorhanden? 2) Wenn ja: welche, wer hat Zugriff?
3) E-Mail mit dieser Domain? 4) Wenn neu: Wunschname oder Vorschläge? 5) Finalen Namen und
Inhaberdaten bestätigen.

### Domain-Schutzregel — Zahlungsverzug ≠ Domainverlust

1. **Domainverlust wird nie als Druckmittel eingesetzt.** Offene Rechnungen laufen über den
   normalen Mahnweg
2. **Bei laufendem Schutzvertrag** verlängert SARTU eine ablaufende Domain zur Schadensvermeidung
   und berechnet die Fremdkosten nach
3. **Vor jedem Ablauf** mindestens **drei** dokumentierte Hinweise — 60, 30 und 7 Tage
4. **Bei Kündigung** rechtzeitig aktiv Auth-Code und Transfer anbieten
5. **Keine Verlängerung auf SARTU-Kosten** nach beendetem Vertrag — der Übergabeweg muss
   nachweislich offen gestanden haben
6. Gehört **wortgleich** in Vertrag, AGB und Kundenbereich

Weitere Fälle (Kündigung, wer zahlt die Verlängerung, Premiumdomain, fehlender Kundenzugriff,
Betriebsende) sind in Masterkonzept §6 tabellarisch geregelt und anwaltlich zu prüfen.
