# Preise, Rundum-Schutz, Zahlung

> **Diese Datei ist die einzige Quelle für ihr Thema.** Steht etwas hier, steht es nirgends
> sonst. Wo ein anderes Thema den Wert braucht, verweist es hierher statt ihn zu wiederholen.
>
> Zusammengeführt am 03.08.2026 aus: Masterkonzept §4, §5
> Wegweiser: `spezifikation/00_UEBERSICHT.md`

---

## Preise — die einzige gültige Quelle jeder Zahl

> **Alle abweichenden Zahlen aus `sartupaketepreise.md` (Basis/Pro/Platin/Enterprise) und
> `sartulastenheftwebsite.md` (1.290/2.990/5.990) sind veraltet und ungültig.**

| Paket | Einmalpreis netto | Ergebnis | Umfangsgrenze | Korrekturrunden | Betrieb | Erstes Jahr netto |
|---|---:|---|---|---:|---|---:|
| **Start** | **1.490 €** | fokussierter One-Pager | 1 Seite, ~1.200 Wörter | 1 | Schutz S, 59 €/Mon. | **2.198 €** |
| **Wachstum** | **3.900 €** | vollständige Firmenwebsite | ≤ 8 Seiten, ~3.500 Wörter | 2 | Schutz M, 129 €/Mon. | **5.448 €** |
| **Platzhirsch** | **7.900 €** | regionales Vertriebs-/Vertrauens-/Recruiting-System | ≤ 16 Seiten, ~6.500 Wörter | 2 | Schutz L, 249 €/Mon. | **10.888 €** |
| **Sonderprojekt** | **ab 12.500 €** | Shop, Login, komplexe Buchung, Schnittstellen, Mehrmarken | individuell | individuell | mind. Schutz L | **ab 15.488 €** |

**Darstellung:** Platzhirsch ist **sichtbar die Empfehlung** (größte Fläche, Badge „Empfehlung"),
Start und Wachstum kleiner ohne gleichstarke Handlung. **Ein** Hauptknopf: `Bedarf prüfen lassen`.
Keine `auswählen`-Knöpfe, keine Extra-Häkchen.

**Geld als Zahl:** integer in Cent, Anzeige `7.900,00 €`. USt 19 % als Konstante an **einer**
Stelle.

---

## Rundum-Schutz — fest zugeordnet, keine Kundenauswahl

| Stufe | netto/Mon. | Inhalt |
|---|---:|---|
| **Schutz S** | 59 € | Managed Hosting DE/EU, SSL, tägl. externe Backups, 30 Tage Versionen, Uptime-/Sicherheitsmonitoring, technische Updates, Selbstpflege im Kundenbereich, Erstreaktion 2 Werktage |
| **Schutz M** | 129 € | alles aus S, 90 Tage Versionen, erweiterte Formular-/Deploymentprüfung, **monatl. Technik-/Suchstatus**, Erstreaktion 1 Werktag |
| **Schutz L** | 249 € | alles aus M, 180 Tage Versionen, **engmaschiger SEO-/GEO-/Conversion-Technikcheck**, priorisierte Störungsbearbeitung, Erstreaktion binnen 8 Geschäftsstunden |

Erstlaufzeit **12 Monate** ab produktivem Betrieb, danach 30 Tage zum Monatsende kündbar,
monatlich im Voraus. **Reaktionszeit ≠ Fertigstellungszeit.** Statt Änderungsminuten pflegt der
Kunde definierte Geschäftsdaten selbst.

**Der Schutz bezahlt:** Betrieb, Verantwortung, Verfügbarkeit, Hosting, SSL, Backups, Monitoring,
technische Pflege, technische Suchgesundheit, Formularprüfung, Versionsstand, Zugang zum
Kundenbereich inkl. Rechnungs- und Zahlungsstatus, Reaktionsbereitschaft.

> **Kommunikationsfehler, der das Modell entwertet:** die Website als „wartungsarm" bewerben.
> Dann fragt der Kunde sofort *„Warum zahle ich dann 59/129/249 € im Monat?"* — **Der Aufwand
> verschwindet für den Kunden, nicht in der Welt.** Genau dafür ist die Pauschale da.

---

## Zahlungsmodell

| Paket | Staffelung |
|---|---|
| Start / Wachstum | 50 % bei Auftrag, 50 % nach Abnahme vor Onlinegang |
| Platzhirsch | 40 % Auftrag, 30 % nach Leitseiten-/Systemvorschau, 30 % nach Abnahme |
| Sonderprojekt | Standard 40/30/30, im Angebot ggf. abweichend |

- **Zahlungsziel 10 Kalendertage.** Produktionsslot **erst nach erster Zahlung** verbindlich
- **Schlusszahlung** hängt an **Abnahme/Fertigstellung**, nicht an einem verschiebbaren Onlinegang
- **Zahlungswahrheit** = serverseitig authentifiziert abgerufener Status nach Webhook,
  **niemals** der Browser-Redirect. Webhooks idempotent, jede Zahlung gegen interne
  Rechnung/Betrag/Währung geprüft
- **Schutz-Abo:** Mandat beim ersten wiederkehrungsfähigen Vorgang ausdrücklich bestätigt
- **Buchhaltung nicht selbst bauen.** Rechnungen über lexoffice **oder** sevDesk (Entscheidung
  offen). Rechnungszahlen dürfen **nie** von KI erzeugt werden

**E-Rechnung — B2B-Pflicht seit 01.01.2025, nicht optional:**
Empfangen und revisionssicher archivieren können ist **sofort** Pflicht (XRechnung/ZUGFeRD nach
EN 16931). Das Buchhaltungstool wird **nur** gewählt, wenn es EN 16931, GoBD-Archivierung,
Storno/Gutschrift, USt-Behandlung und Mollie-Abgleich beherrscht.
**Verboten:** selbstgebaute PDFs als alleinige Buchhaltung — ein PDF allein ist **keine**
E-Rechnung.
