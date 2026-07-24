# Sartu Angebotslogik

Stand: aktuelles Modell. Die alte Konfigurator-Logik mit Basis/Pro/Platin, oeffentlichen Add-ons, Extraseitenpreisen, SEO-Stufen und Aenderungsminuten ist abgeloest.

## Aktuelle Pakete

| Paket | Preis netto | Schutz | Umfang | Feedback |
|---|---:|---:|---|---:|
| Start | 1.490 EUR | Schutz S, 59 EUR/Monat | 1 fokussierter One-Pager | 1 gebuendeltes Paket |
| Wachstum | 3.900 EUR | Schutz M, 129 EUR/Monat | bis 8 strategische Seiten | 2 gebuendelte Pakete |
| Platzhirsch | 7.900 EUR | Schutz L, 249 EUR/Monat | bis 16 strategische Seiten | 2 gebuendelte Pakete |
| Sonderprojekt | ab 12.500 EUR | mindestens Schutz L oder nach Angebot | Shop, Login, Schnittstellen oder grosser Sonderumfang | nach Angebot |

Alle Preise sind netto zzgl. gesetzlicher Umsatzsteuer. Sartu richtet sich an Unternehmer, nicht an Privatkunden.

## Prinzipien

- Keine oeffentliche Add-on-Liste.
- Keine Aenderungsminuten oder Minutenkontingente.
- Keine SEO-Retainer-Auswahl im Erstangebot.
- SEO-/GEO-Startsystem ist Teil der Website-Erstellung.
- Schutzbetrieb gehoert zu jeder Website.
- Der Kunde waehlt keine Technikbausteine; Sartu leitet die Empfehlung aus wenigen Antworten ab.
- Sonderprojekte bekommen im Portal standardmaessig 40/30/30 als Vorschau, duerfen im individuellen Angebot aber einen abweichenden Meilensteinplan erhalten.

## Domain

Der Kunde bestimmt den gewuenschten Namen und bleibt Domaininhaber. Sartu empfiehlt und verwaltet den Anbieter ueber einen Provider-Adapter, zuerst INWX. Eine normale Domain bis 30 EUR netto/Jahr ist im Schutzbetrieb einkalkuliert. Premiumdomains, Sonderendungen, Domainkaeufe und bestehende DNS-/E-Mail-Risiken werden vorher separat freigegeben.

## Zahlung

Zahlung laeuft im Portal ueber Mollie. Zahlungsziel: 10 Tage.

| Paket | Staffelung |
|---|---|
| Start, Wachstum | 50 % bei Auftrag, 50 % bei Abnahme vor Onlinegang |
| Platzhirsch | 40 % bei Auftrag, 30 % bei Systemvorschau, 30 % bei Abnahme vor Onlinegang |
| Sonderprojekt | standardmaessig 40/30/30, bei Bedarf individuell im Angebot |

## Aktive Quellen

- Oeffentliche Preisdaten: `data/pricing.json`
- Oeffentliche Seiten: `data/pages.json`
- Gefuehrte Anfrage: `assets/lumi.js`
- Legacy-Testadapter: `pricing.js`, `pricing-calc.js`, `payment-terms.js`, `pricing.test.js`
- Portal/Control-Plane: `control-plane/src/catalog.js`, `control-plane/src/lumi.js`, `control-plane/src/server.js`

Die alten Dateien `briefing.js`, `briefing-schema.js` und `briefing.css` sind Legacy-Artefakte und duerfen nicht mehr als fachliche Quelle genutzt werden.

