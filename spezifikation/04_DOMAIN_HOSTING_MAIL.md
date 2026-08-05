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
3. **Vor jedem Ablauf** dokumentierte Hinweise — Fristen in der Tabelle unten
4. **Bei Kündigung** rechtzeitig aktiv Auth-Code und Transfer anbieten
5. **Keine Verlängerung auf SARTU-Kosten** nach beendetem Vertrag — der Übergabeweg muss
   nachweislich offen gestanden haben
6. Gehört **wortgleich** in Vertrag, AGB und Kundenbereich

### Die sechs Grenzfälle

**Vorschlag, anwaltlich zu prüfen** — `20_OFFEN.md`. Gebaut wird die Anzeige, nicht die
Rechtsfolge.

| Fall | Regel |
|---|---|
| **Kündigung des Schutzes** | Die Domain bleibt Eigentum des Kunden. SARTU stellt auf Anforderung **Auth-Code/AuthInfo** und eine dokumentierte DNS-Übergabe bereit — einmalig, innerhalb einer definierten Frist (Vorschlag: **10 Werktage** nach der letzten erfüllten Zahlung) |
| **Wer zahlt die Verlängerung nach Kündigung** | Ab Vertragsende trägt der Kunde die Domainkosten selbst. **SARTU verlängert nicht stillschweigend weiter**; der Kunde wird rechtzeitig auf den Transfer hingewiesen |
| **Erinnerungen vor Ablauf** | mindestens **drei** dokumentierte Hinweise — 60, 30 und 7 Tage vor Ablauf — im Kundenbereich **und** per E-Mail. **Das Ablaufdatum ist dauerhaft sichtbar** |
| **Premiumdomain, fehlgeschlagener Transfer, Redemption** | Fremdkosten trägt der Kunde und werden **vorher** ausgewiesen. **Wiederherstellungsgebühren sind nie in der Jahrespauschale enthalten** |
| **Kunde hat keinen Zugriff auf Altdomain oder Alt-E-Mail** | **Kein Blindflug:** Das Projekt läuft auf einer Übergangsdomain weiter, **das Launch-Gate bleibt zu**, bis Inhaberschaft und Zugriff nachgewiesen sind. Aufwand für Recherche und Wiederbeschaffung ist **nicht** im Festpreis enthalten |
| **Betriebsende ohne Transfer** | Nach dokumentierter Frist und Hinweisen darf SARTU die Verwaltung beenden; die Verantwortung geht an den Kunden über — **keine Löschung und kein Ablaufenlassen ohne vorherige Ankündigung** |
