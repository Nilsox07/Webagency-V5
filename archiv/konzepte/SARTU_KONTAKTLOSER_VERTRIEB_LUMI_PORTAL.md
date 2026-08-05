# Sartu - Kontaktloser Vertrieb mit Lumi und Portal

Verbindliches Sollkonzept fuer Anfrage, Empfehlung, Angebot, Annahme und adaptives Onboarding. Preise und Leistungsgrenzen folgen `GESCHAEFTSMODELL.md`; technische Details stehen in `SARTU_DESIGNSYSTEM_PORTAL_ARCHITEKTUR.md`.

Stand: Juli 2026.

---

## 1. Zielbild

Lumi ersetzt keinen Berater durch einen langen Konfigurator. Lumi macht aus wenigen Geschaeftsfakten eine begruendete Vorpruefung. Sartu entscheidet Paket, Struktur, Technik und Design.

Der Kunde soll:

- in etwa drei Minuten fertig sein.
- keine Fachbegriffe kennen.
- keine Seitenzahl, Designrichtung oder SEO-Stufe bestimmen.
- den Preis vor Abgabe seiner Kontaktdaten sehen.
- verstehen, warum Sartu eine Loesung empfiehlt.
- wissen, dass ein Mensch das Ergebnis vor dem verbindlichen Angebot prueft.

Sartu soll:

- Standard- und Sonderprojekte sicher trennen.
- hoechstens eine gebuendelte Rueckfrage stellen.
- bekannte Daten nicht erneut abfragen.
- das Angebot in Standardfaellen in 10 bis 15 Minuten pruefen koennen.
- nach Auftrag nur fehlende Kundenfakten sammeln.

**Leitregel:** Wenig Auswahl bedeutet nicht wenig Erklaerung. Jede Empfehlung braucht eine kurze, konkrete Begruendung.

---

## 2. Drei Informationsebenen

### A. Lumi vor Kontaktdaten

Nur Daten, die Machbarkeit, Paket oder Preis beeinflussen. Ziel: 8 bis 12 leichte Eingaben.

### B. Geprueftes Angebot

Sartu zeigt Empfehlung, Sitemap, Funktionen, Annahmen, Ausschluesse, Preis, Betrieb, Zahlungsplan und Terminrahmen. Erst dieses Angebot ist verbindlich.

### C. Portal nach Auftrag

Das Portal uebernimmt bekannte Daten und zeigt nur offene Fakten, Materialien, Rechte und Freigaben. Sartu-interne Design- und Technikentscheidungen erscheinen nicht als Kundenfragen.

---

## 3. Vollstaendiger Ablauf

1. Kunde sieht Platzhirsch als Hauptangebot und Start/Wachstum als kleinere Empfehlungen.
2. Kunde startet `Bedarf pruefen lassen`.
3. Lumi zeigt fuenf kurze Themen.
4. Kunde sieht vor Kontaktdaten Empfehlung, Begruendung, Netto- und Erstjahrespreis.
5. Kunde hinterlaesst Kontakt und bestaetigt den geschaeftlichen Zweck.
6. Regelwerk markiert Standard, Gelb, Orange oder Rot.
7. Sartu prueft Ergebnis und vorhandene Website.
8. Standard- und Klaerungsfaelle werden im Adminbereich zu Organisation, Angebot, Projekt und Draft-Rechnungen nach Zahlungsplan vorbereitet.
9. Nur wenn noetig kommt eine gebuendelte Rueckfrage oder ein kurzes Fachmodul.
10. Kunde erhaelt ein persoenlich begruendetes Festpreisangebot.
11. Kunde bestaetigt Scope, Rechnungsdaten und beauftragt eindeutig kostenpflichtig.
12. Kunde zahlt den ersten Meilenstein ueber Mollie.
13. Nach bestaetigter erster Zahlung erzeugt das Portal das adaptive Onboarding und klaert Domain sowie E-Mail.
14. Sartu friert die Produktionsspezifikation im Adminbereich ein und erzeugt Website-Datensatz, Seitenstruktur und Spezifikationsversion.
15. Das Portal reiht den initialen Codex-/Claude-Build mit der freigegebenen Designsystemversion ein; Sartu prueft.
16. Kunde sieht die versionierte Vorschau, gibt gebuendeltes Feedback und nimmt im Portal ab.
17. Die Schlussrate wird freigegeben und per Mollie bezahlt.
18. Nach bezahlten Meilensteinen und aktiver Kundendomain gibt Sartu den Launch frei; der Rundum-Schutz beginnt.

Verschiebt ausschliesslich der Kunde einen bereits abgenommenen und betriebsfertig bereitgestellten Onlinegang, beginnt der Schutz nach vorherigem Hinweis spaetestens 14 Kalendertage danach. Die rechtlich gepruefte Angebots- und AGB-Regel ist massgeblich.

Ein Gespraech bleibt auf Wunsch oder bei kompliziertem Sonderfall moeglich, ist aber nie Pflicht fuer einen klaren Standardfall.

---

## 4. Lumi-Startseite

### Ueberschrift

> Welche Website passt wirklich zu Ihrem Unternehmen?

### Text

> Beantworten Sie fuenf kurze Themen zu Ihrem Geschaeft. Sie muessen weder Paket noch Seitenzahl oder Technik kennen. Sartu zeigt Ihnen danach eine erste Empfehlung mit Preis und prueft sie persoenlich.

### Vertrauenshinweise

- dauert meist etwa 3 Minuten.
- Preis und Begruendung vor den Kontaktdaten.
- kein Pflichttermin.
- keine Paket- oder Extra-Auswahl.
- unverbindlich bis zum geprueften Angebot.

### Button

`Bedarf pruefen lassen`

Nicht verwenden: `Website konfigurieren`, `Paket berechnen` oder `KI erstellt Ihr Angebot automatisch`.

---

## 5. Die fuenf Lumi-Themen

Pro Bildschirm steht oben `Thema 1 von 5`, nicht eine kuenstlich hohe Fragezahl. Pflichtfelder werden sparsam verwendet.

### Thema 1: Ihr Unternehmen

**Frage 1**

> Was bietet Ihr Unternehmen an?

Ein Freitextfeld, Richtwert ein bis drei Saetze.

Hilfetext:

> Zum Beispiel: Wir sanieren Baeder und Heizungen fuer Privatkunden im Raum Leipzig.

**Frage 2**

> Wo ist Ihr Unternehmen hauptsaechlich taetig?

Felder: Ort oder PLZ; optional groesseres Einzugsgebiet.

**Frage 3**

> Gibt es bereits eine Website?

- Ja, URL eingeben.
- Nein.

Interne Verwendung: Branche ableiten, vorhandene Inhalte erkennen, Relaunch- und Domainrisiken markieren. Keine Frage nach Logo, CMS oder Hosting an dieser Stelle.

### Thema 2: Was soll sich verbessern?

**Frage 4**

> Was ist aktuell das wichtigste Ziel der neuen Website?

- Mehr passende Anfragen erhalten.
- Bei Google und in der Region besser gefunden werden.
- Neue Mitarbeitende gewinnen.
- Vertrauen und Professionalitaet staerken.
- Termine oder Bewerbungen einfacher machen.
- Etwas anderes.

Nur eine Hauptauswahl. Hilfetext:

> Waehlen Sie das Ziel, das in den naechsten 12 Monaten den groessten Unterschied machen wuerde.

**Frage 5**

> Wen moechten Sie vor allem erreichen?

- Privatkunden.
- Unternehmen.
- Bewerberinnen und Bewerber.
- mehrere dieser Gruppen.
- noch unklar.

Interne Verwendung: Botschaft, Vertrauensbelege und moegliche Trennung von Nutzerwegen. Die Auswahl aendert nicht allein das Paket.

### Thema 3: Wie viel muss die Website erklaeren?

**Frage 6**

> Welche Aussagen passen zu Ihrem Unternehmen?

Mehrfachauswahl:

- Wir haben ein klares Hauptangebot.
- Wir bieten mehrere eigenstaendige Leistungen an.
- Wir bedienen mehrere Regionen oder Standorte.
- Wir suchen regelmaessig Mitarbeitende.
- Wir moechten Projekte, Referenzen, Stellen oder Neuigkeiten selbst aktuell halten.
- Nichts davon / ich bin unsicher.

Kurze Erklaerung bei `mehrere eigenstaendige Leistungen`:

> Gemeint sind Angebote, nach denen Kunden getrennt suchen oder fuer die sie unterschiedliche Erklaerungen brauchen.

Interne Verwendung: Umfangssignale fuer Wachstum und Platzhirsch. Der Kunde wird nicht gefragt, wie viele Seiten daraus entstehen.

### Thema 4: Was muss die Website koennen?

**Frage 7**

> Benoetigen Sie etwas davon?

Mehrfachauswahl mit je einem Beispielsatz:

- **Normale Anfrage oder Bewerbung**  
  Ein Formular sendet die Angaben an Ihr Unternehmen.
- **Einfache Terminbuchung**  
  Ein vorhandener Kalender oder ein Kalender mit freien Terminen reicht.
- **Produkte verkaufen oder Zahlungen annehmen**  
  Zum Beispiel Shop, Gutschein oder bezahlte Buchung.
- **Kundenlogin oder geschuetzter Bereich**  
  Besucher melden sich an und sehen eigene Inhalte oder Daten.
- **Verbindung zu anderer Software**  
  Zum Beispiel CRM, Warenwirtschaft oder individuelle API.
- **Mehrere Sprachen oder getrennte Marken**.
- **Besondere Daten oder formaler Nachweis**  
  Zum Beispiel sensible Uploads oder verpflichtender Barrierefreiheitsaudit.
- **Nichts davon / normale Firmenwebsite**.

Kombinationen werden validiert. `Nichts davon` kann nicht zusammen mit einer Sonderfunktion gewaehlt werden.

### Thema 5: Termin und Besonderheit

**Frage 8**

> Gibt es einen festen Termin, der wirklich eingehalten werden muss?

- Nein, der normale Zeitrahmen passt.
- Ja, Datum und kurzer Grund.

Hilfetext:

> Ein Wunschdatum ist noch keine Zusage. Sartu bestaetigt die Machbarkeit im Angebot.

**Frage 9**

> Gibt es etwas, das auf keinen Fall uebersehen werden darf?

Optionaler Freitext.

Platzhalter:

> Zum Beispiel: Die vorhandene Domain und E-Mail-Adressen muessen bestehen bleiben.

---

## 6. Ergebnis vor den Kontaktdaten

### Start

> **Unsere vorlaeufige Empfehlung: Start**
>
> Sie haben ein klares Hauptangebot und brauchen vor allem einen professionellen, direkten Weg zur Anfrage. Eine groessere Website wuerde nach Ihren jetzigen Angaben mehr Umfang als Nutzen erzeugen.
>
> **1.490 EUR einmalig + 59 EUR/Monat Rundum-Schutz**  
> Erstes Jahr gesamt: **2.198 EUR netto**.

### Wachstum

> **Unsere vorlaeufige Empfehlung: Wachstum**
>
> Mehrere Leistungen brauchen eine klare Navigation und eigene inhaltliche Schwerpunkte. Ein One-Pager waere zu eng; ein voller Platzhirsch-Ausbau ist nach Ihren jetzigen Angaben noch nicht notwendig.
>
> **3.900 EUR einmalig + 129 EUR/Monat Rundum-Schutz**  
> Erstes Jahr gesamt: **5.448 EUR netto**.

### Platzhirsch

> **Unsere vorlaeufige Empfehlung: Platzhirsch**
>
> Ihre Leistungen, regionalen Ziele und [Recruiting-/Projekt-/Conversion-Signal] brauchen eine staerkere Struktur. Sartu entwickelt daraus ein regionales Vertriebs-, Vertrauens- und Recruiting-System mit bis zu 16 strategischen Seiten.
>
> **7.900 EUR einmalig + 249 EUR/Monat Rundum-Schutz**  
> Erstes Jahr gesamt: **10.888 EUR netto**.

### Preisoffener Klaerungsfall

> Ihr Bedarf passt voraussichtlich in eines unserer drei Standardpakete. Eine Angabe entscheidet noch ueber den notwendigen Umfang.
>
> Die Erstellung liegt bei **1.490 EUR, 3.900 EUR oder 7.900 EUR einmalig**; der fest zugeordnete Rundum-Schutz bei **59 EUR, 129 EUR oder 249 EUR pro Monat**. Nach dem Absenden stellt Sartu Ihnen hoechstens eine gebuendelte Rueckfrage.

### Sonderprojekt

> Ihr Vorhaben enthaelt eine besondere Funktion, zum Beispiel Shop, Login, komplexe Buchung oder Schnittstelle. Solche Projekte beginnen bei **12.500 EUR einmalig + mindestens 249 EUR/Monat Rundum-Schutz**. Erstes Jahr ab **15.488 EUR netto**.
>
> Sie erhalten nur das passende kurze Fachmodul und danach einen geprueften Gesamtfestpreis.

Unter jedem Ergebnis:

> Alle Preise netto zzgl. gesetzlicher Umsatzsteuer. Ausschliesslich fuer Unternehmer. Erst das von Sartu gepruefte Angebot ist verbindlich.

Button: `Empfehlung unverbindlich pruefen lassen`.

Keine alternativen Paketbuttons und keine Extraauswahl.

---

## 7. Kontaktdaten

Erst nach dem Ergebnis:

- Vor- und Nachname.
- Unternehmen.
- geschaeftliche E-Mail-Adresse.
- Telefonnummer optional.
- bevorzugter schriftlicher Kontakt: E-Mail oder Portal.
- Checkbox: `Ich handle fuer mein Unternehmen beziehungsweise in Ausuebung meiner beruflichen oder gewerblichen Taetigkeit.`
- notwendige Datenschutzhinweise und Einwilligungen.

Kein Newsletter-Haekchen im Hauptweg. Keine Pflichttelefonnummer, wenn der Ablauf ohne Termin versprochen wird.

---

## 8. Deterministische Empfehlungslogik

KI formuliert, aber das Regelwerk entscheidet die Vorstufe.

### Rote Sonderprojekt-Gates

- Shop, Websitezahlung oder bezahlte Buchung.
- Kundenlogin, Rollen oder geschuetzte Inhalte.
- individuelle Schnittstelle oder bidirektionaler Datenaustausch.
- komplexe Buchung mit mehreren Ressourcen, Preisen oder Regeln.
- mehrere getrennte Marken oder primaere Domains.
- sensible individuelle Datenuploads.
- nachweisbarer formaler Spezialaudit.

### Orange Prueffalle

- mehrere Sprachen.
- mehrere Standorte unter gemeinsamer Marke.
- einfache Terminbuchung ist unklar beschrieben.
- mehr als eine Conversionloesung wirkt notwendig.
- feste Frist ist knapp.
- Freitext nennt eine moegliche Sonderfunktion ohne eindeutige Auswahl.

Orange bedeutet nicht automatisch Aufpreis. Sartu entscheidet nach dem kurzen Fachmodul zwischen unveraendertem Standardpaket und Sonderprojekt.

### Gelbe Klaerungsfaelle

- widerspruechliche Antworten.
- `noch unklar` an einer paketentscheidenden Stelle.
- vorhandene Website ist nicht erreichbar oder wirkt strukturell deutlich komplexer.
- Domain, E-Mail oder Rechte scheinen ungeklaert.

### Standardpakete

**Start**, wenn alle Bedingungen passen:

- klares Hauptangebot.
- ein Standort beziehungsweise einfache Region.
- keine regelmaessigen Team-, Stellen-, Projekt- oder Newsinhalte.
- normale Anfrage.
- keine roten oder orangefarbenen Gates.

**Wachstum**, wenn:

- mehrere Leistungen erklaert werden muessen oder normale mehrseitige Firmenstruktur erkennbar ist.
- hoechstens ein schwaches Platzhirschsignal vorliegt.
- keine Sonderfunktion erforderlich ist.

**Platzhirsch**, wenn mindestens zwei starke Signale vorliegen:

- mehrere eigenstaendige Leistungen.
- mehrere sinnvolle Regionen oder Standorte unter einer Marke.
- Recruiting als Hauptziel oder regelmaessige Stellen.
- Projekte/Referenzen/Neuigkeiten muessen gepflegt werden.
- lokale Auffindbarkeit ist Hauptziel und mehrere Suchthemen sind erkennbar.
- qualifizierter Anfrageweg oder einfache Buchung ist strategisch zentral.

Sartu darf Platzhirsch auch bei einem besonders starken einzelnen Signal empfehlen, muss das im Angebot begruenden. Es darf nie nur deshalb empfohlen werden, weil es das Hauptprodukt ist.

### Konfliktregel

- Rot schlaegt Standard.
- Orange braucht Sartu-Pruefung.
- Gelb verhindert Scheingenauigkeit.
- bei zwei plausiblen Standardpaketen entscheidet der fuer das bestaetigte Ziel notwendige Umfang, nicht der hoehere Umsatz.

---

## 9. Logik-Testfaelle

| Fall | Erwartung |
|---|---|
| ein Elektriker, ein Standort, normale Anfrage | Start oder Wachstum nach tatsaechlicher Leistungsbreite |
| drei klar getrennte Handwerksleistungen | Wachstum |
| mehrere Leistungen, drei Regionen, Recruiting | Platzhirsch |
| Praxis mit einem Kalender und normalen Terminen | Wachstum oder Platzhirsch, einfache Buchung pruefen |
| Restaurant mit Tisch-/Raum-/Preislogik | Rot, Sonderprojekt |
| Kanzlei mit Mandantenlogin | Rot, Sonderprojekt |
| Unternehmen nennt CRM, aber nur normales Formular ist noetig | Orange, danach Standard moeglich |
| zwei Standorte, gleiche Marke und Leistungen | Orange, Platzhirsch moeglich |
| zwei getrennte Marken und Domains | Rot, Sonderprojekt |
| mehrere Sprachen, Uebersetzungen vorhanden | Orange, Scope pruefen |
| `weiss ich nicht` bei mehreren Leistungen | Gelb, eine Rueckfrage |
| Freitext nennt Shop, Auswahl sagt normale Website | Gelb/Rot, Widerspruch klaeren |
| harte Frist in vier Tagen | Orange, keine automatische Zusage |
| Start-Signale, aber Kunde will Platzhirsch nicht aktiv waehlen | Start empfehlen; es gibt keine Paketwahl |

Jede Regelversion erhaelt automatisierte Tests. Ein Preis- oder Paketwechsel wird nicht ohne passende Testanpassung veroeffentlicht.

---

## 10. Sartu-Pruefung

Standardfall, Ziel 10 bis 15 Minuten:

1. Unternehmensbeschreibung und Ziel lesen.
2. vorhandene Website pruefen.
3. rote, orange und gelbe Hinweise kontrollieren.
4. Empfehlung bestaetigen oder begruendet korrigieren.
5. Sitemap und Conversionloesung aus interner Vorlage ableiten.
6. zwei bis vier konkrete Empfehlungsgruende freigeben.
7. Annahmen und Ausschluesse kontrollieren.
8. Angebot versenden.

Sartu muss keinen Termin anbieten, wenn Scope und Risiko klar sind. Schriftliche Rueckfrage im Portal bleibt immer moeglich.

---

## 11. Kurze Fachmodule vor Angebot

Nur das betroffene Modul wird gezeigt.

### Buchung

1. Was wird gebucht: Termin, Tisch, Raum, Mitarbeiter, Kurs oder anderes?
2. Reicht ein Link oder ein einzelner vorhandener Kalender?
3. Gibt es mehrere Kalender, Ressourcen, Preise, Zahlungen oder Stornierungsregeln?

### Shop/Zahlung

1. Was wird verkauft?
2. Wie viele Produkte oder Leistungen gibt es zum Start?
3. Sind Versand, Varianten, Bestand, Gutscheine oder Zahlungen noetig?
4. Gibt es bereits Shop-, Kassen- oder Warenwirtschaftssoftware?

### Login

1. Wer meldet sich an?
2. Was sieht oder bearbeitet diese Person?
3. Welche Rollen und Daten gibt es?
4. Kommen Nutzer oder Daten aus einem bestehenden System?

### Schnittstelle

1. Welche Software ist betroffen?
2. Welche Daten fliessen in welche Richtung?
3. Gibt es eine dokumentierte API und einen Ansprechpartner?
4. Sind personenbezogene oder sensible Daten betroffen?

### Standorte/Marken

1. Wie viele Standorte oder Marken?
2. Gleicher Name, gleiche Domain, Leistungen und Zielgruppen?
3. Braucht jeder Bereich eigene Inhalte, Kontakte, Rechte oder Auswertung?

### Sprache/Barrierefreiheit

1. Welche Sprachen oder formale Vorgabe?
2. Welche Inhalte sind betroffen?
3. Wer liefert und prueft Uebersetzung oder fachlichen Nachweis?
4. Ist ein dokumentierter externer Audit gefordert?

---

## 12. Angebot, das Beratung ersetzt

Pflichtbestandteile:

1. Zusammenfassung von Unternehmen und Ziel.
2. eine empfohlene Loesung.
3. zwei bis vier kundenspezifische Gruende.
4. warum kleiner nicht reicht oder groesser nicht noetig ist.
5. konkrete, von Sartu bestimmte Sitemap.
6. Conversionloesung und strukturierte Inhaltsbereiche.
7. Texte, Design, Programmierung, Portal und SEO-/GEO-Startsystem.
8. klare Ausschluesse, Annahmen und Fremdkosten.
9. Domain- und E-Mail-Vorgehen.
10. Einmalpreis netto, Steuer, Schutz, Mindestlaufzeit und Erstjahreswert.
11. Zahlungsplan und Zahlungsziel.
12. Startbedingungen und Lieferkorridor.
13. Korrekturrunden, Abnahme und Mitwirkung.
14. Rechte, Export und Verantwortlichkeiten.
15. Gueltigkeit 14 Kalendertage.
16. eindeutige digitale Annahme.

Vor Annahme werden rechtlicher Unternehmensname, Rechtsform, Rechnungsanschrift, Rechnungsempfaenger, Rechnungs-E-Mail, beauftragende Person und optional Umsatzsteuer-ID/Bestellnummer erfasst.

### Bestaetigung

> Die aufgefuehrten Ziele, Seitenbereiche und Funktionen entsprechen meinem aktuellen Bedarf.

> Nicht aufgefuehrte Sonderfunktionen wie Shop, Kundenlogin, individuelle Schnittstelle oder komplexe Buchung sind nicht beauftragt.

> Neue Anforderungen werden vor Umsetzung getrennt angeboten.

> Ich handle fuer mein Unternehmen und beauftrage Sartu kostenpflichtig zu den angezeigten Preisen, Laufzeiten und Zahlungsbedingungen.

Direkt am Button stehen Nettoeinmalpreis, Umsatzsteuer und Bruttobetrag, monatlicher Nettoschutz, Mindestlaufzeit, Erstjahreswert netto und Zahlungsplan.

---

## 13. Zahlung und Projektstart

- Start und Wachstum: 50 Prozent bei Auftrag, 50 Prozent nach Abnahme/Fertigstellung vor Launch.
- Platzhirsch: 40 Prozent bei Auftrag, 30 Prozent nach Leitseiten-/Seitensystemvorschau, 30 Prozent nach Abnahme/Fertigstellung vor Launch.
- Zahlungsziel 10 Kalendertage.
- Produktionsslot erst nach erster Zahlung.
- Lieferzeit erst nach Zahlung, Briefingfreigabe, Materialien und Zugaengen.
- Domainregistrierung erst nach Zahlung und finaler Namensbestaetigung.

Mollie-Redirect ist keine Zahlungsbestaetigung. Das Portal markiert erst bezahlt, nachdem der serverseitig authentifiziert abgerufene Status nach Webhook passt.

---

## 14. Adaptives Portal-Onboarding

Das Portal zeigt keine Kapitel A bis L und keinen Vollfragebogen. Es erzeugt Aufgaben aus Angebot, Lumi, Altwebsite und Uploads. Diese Aufgaben entstehen automatisch nach der ersten Zahlung und unterscheiden sich nach Start, Wachstum, Platzhirsch oder Sonderprojekt.

### Systemablauf

1. vorhandene Website, PDFs und Dateien auslesen.
2. gefundene Fakten mit Quelle und Konfidenz speichern.
3. Widersprueche und Luecken markieren.
4. Sartu-interne Entscheidungen ausblenden.
5. Kunden nur bekannte Fakten bestaetigen oder fehlende Fakten beantworten lassen.
6. produktionsrelevante Aufgaben nur mit bestaetigten Projektfakten schliessen.
7. Briefingzusammenfassung und Produktionssitemap final freigeben.

### Zielumfang

- Start: etwa 8 bis 12 aktive Bestaetigungen/Antworten.
- Wachstum: etwa 12 bis 18 plus vereinbarte Leistungsdatensaetze.
- Platzhirsch: etwa 18 bis 25 plus vereinbarte Team-, Stellen-, Projekt- oder Standortdatensaetze.

Fehlende Unterlagen koennen mehr Aufwand verursachen. Das Portal zeigt Fortschritt nach offenen Pflichtangaben, nicht nach der Groesse der internen Fragenbibliothek.

Hauptangebot, Leistungen, Belege und Conversionweg werden als kurze Projektfakten gespeichert. Diese Datensaetze sind Pflicht fuer die KI-Produktion, aber keine Kundenwahl aus Extras oder Zusatzseiten.

### Grundsaetze

- bekannte Angaben nie leer erneut abfragen.
- pro Bildschirm ein klarer Sachverhalt.
- Autosave und spaeter fortsetzen.
- Upload statt Abtippen anbieten.
- ungewoehnliche Frage mit `Warum wir das brauchen` erklaeren.
- `Sartu soll aus den vorhandenen Angaben empfehlen` nur bei echten Sartu-Entscheidungen.
- KI-Vorschlaege als Vorschlag kennzeichnen.
- genau eine Person erteilt finale Freigabe; andere duerfen kommentieren.

---

## 15. Gemeinsame Onboarding-Aufgaben

### Aufgabe A: Unternehmen bestaetigen

Eine Zusammenfassung statt einzelner Leerfelder:

- rechtlicher Name und sichtbarer Markenname.
- Anschrift, Telefon, E-Mail, Oeffnungszeiten.
- Leistungen in einem Satz.
- Zielgruppen und Einzugsgebiet.

Button: `Stimmt` oder `Korrigieren`.

### Aufgabe B: Wichtige Fakten und Belege

- Was unterscheidet das Unternehmen konkret?
- Welche Qualifikationen, Erfahrungen, Zahlen oder Zusagen duerfen belegt genannt werden?
- Welche typischen Einwaende muessen beantwortet werden?
- Welche Aussagen duerfen auf keinen Fall gemacht werden?

Nur fehlende Punkte erscheinen. Freitext hat Beispiele und Richtlaenge.

### Aufgabe C: Materialien und Rechte

- vorhandenes Logo und Markenunterlagen.
- reale Team-, Betriebs-, Projekt- oder Produktbilder.
- Broschueren, Leistungsunterlagen und Alttexte.
- Quelle und Nutzungsrecht je Datei.
- Personenfreigabe, soweit erforderlich.

### Aufgabe D: Kontakt und Conversion

- wohin gehen normale Anfragen?
- wer antwortet typischerweise?
- welche Angaben braucht das Unternehmen vor einem Rueckruf?
- welcher bestehende Kalender oder Bewerbungsweg ist vereinbart?

Sartu entscheidet Formularreihenfolge, Felder und CTA innerhalb des Angebots.

### Aufgabe E: Recht und Dienste

- freigegebene Impressums- und Datenschutzquelle.
- Datenschutzkontakt, falls vorhanden.
- notwendige Karten, Videos, Analytics, Kalender oder andere Dienste.
- besondere Branchenhinweise.

Das Portal sammelt und bindet ein; es erteilt keine Rechtsberatung.

### Aufgabe F: Abschluss

Das Portal zeigt:

- Unternehmenszusammenfassung.
- Zielgruppen und Hauptbotschaft.
- Sitemap und Zweck jeder Seite.
- vereinbarte Conversionloesung.
- offene Annahmen, fehlende Materialien und rechtliche Verantwortlichkeiten.

Bestaetigung:

> Die Fakten und der vereinbarte Umfang sind vollstaendig. Sartu darf daraus die Produktionsspezifikation erzeugen.

---

## 16. Paketbezogene Aufgaben

### Start

Nur eine zentrale Leistung, wichtigste Belege, Kontakt, Materialien, Domain, Recht und Freigabe. Keine Team-, Standort-, News- oder SEO-Fachfragen.

### Wachstum

Zusaetzlich pro vereinbarter Leistung ein kurzer Datensatz:

- Leistungsname.
- fuer wen und welches Problem.
- was konkret erbracht wird.
- wichtiger Beleg oder Unterschied.
- was der Besucher als Naechstes tun soll.

Team oder Referenzen nur, wenn sie in der Sitemap stehen.

### Platzhirsch

Nur entsprechend dem Angebot:

- Leistungsdatensaetze.
- Regionen/Standorte mit echten Unterschieden und Kontaktdaten.
- Personen mit Rolle, Kurzprofil, Bildrecht und Sichtbarkeitsstatus.
- Stellen mit Aufgaben, Anforderungen, Arbeitsort und Bewerbungsweg.
- Projekte mit Ausgangslage, Leistung, Ergebnis, Medien und Freigabe.
- Conversiondetails fuer genau das vereinbarte Modul.

Keine Datensaetze fuer Bereiche, die Sartu nicht sinnvoll in die Sitemap aufgenommen hat.

### Sonderprojekt

Grundaufgaben plus genau die vereinbarten Fachmodule, Datenfluesse, Rollen und Abnahmefaelle. Kein generischer Vollfragebogen.

---

## 17. Domain im Onboarding

Der Kunde beantwortet maximal diesen Block:

1. **Gibt es bereits eine Domain?** Ja mit Domain / Nein.
2. **Wer kann den Zugriff bestaetigen?** Ansprechpartner oder bisheriger Dienstleister.
3. **Werden E-Mail-Adressen mit dieser Domain genutzt?** Ja / Nein / unbekannt.
4. **Bei neuer Domain:** Wunschname oder `Sartu soll Vorschlaege machen`.
5. **Final:** einen von maximal drei geprueften Namen sowie korrekte Inhaberdaten bestaetigen.

Im Portal sind das zwei kurze Schritte: zuerst rechtlicher Domaininhaber, geschaeftliche E-Mail und Anschrift bestaetigen; danach erscheinen maximal drei verfuegbare Standarddomains. Ohne bestaetigte Inhaberdaten gibt es keine verbindliche Registrierung.

Direkt an der finalen Bestaetigung steht:

> Sartu registriert die Domain ueber den technischen Registrar in Ihrem Namen. Die Registrierung ist nach erfolgreicher Ausfuehrung regelmaessig nicht stornierbar. Eine normale Domain bis 30 EUR netto pro Jahr ist bei Verwaltung durch Sartu im Rundum-Schutz enthalten.

Sartu beziehungsweise das System klaert intern:

- Registrar und Transferfaehigkeit.
- Ablaufdatum und Domainstatus.
- DNS-Snapshot.
- A/AAAA/CNAME.
- MX, SPF, DKIM und DMARC.
- Subdomains und Verifizierungsrecords.
- Redirectplan und Rollback.

Der Kunde waehlt keinen Registrar und bearbeitet kein DNS.

---

## 18. Neue Wuensche nach Auftrag

Das Portal unterscheidet:

- **Fehler/Mangel:** vereinbarter Inhalt oder Funktion fehlt beziehungsweise funktioniert nicht; Sartu behebt.
- **Korrektur:** Detail innerhalb des bestaetigten Ziels; Teil der enthaltenen Runde.
- **Faktenpflege:** strukturiertes Selbstpflegefeld; Kunde kann es selbst aendern.
- **Neues Ziel:** neue Seite, Funktion, Zielgruppe, Region oder Markenrichtung; getrennte Pruefung.

Kundentext bei neuem Ziel:

> Dieser Wunsch war nicht Teil des angenommenen Angebots. Ihr aktuelles Projekt kann wie vereinbart weiterlaufen. Sartu prueft, ob daraus ein getrenntes Ergebnisprojekt mit Festpreis entsteht.

Optionen: `Aktuelles Projekt fortsetzen`, `Pruefung anfragen`, `Freiwillig bis zur Klaerung pausieren`.

Keine Mehrarbeit ohne digitale Annahme.

---

## 19. KI-Rolle und Datenschutz

### KI darf

- Freitext strukturieren.
- Altwebsite und Dokumente zusammenfassen.
- vorhandene Fakten mit Quelle extrahieren.
- Widersprueche und moegliche Scope-Risiken markieren.
- eine Empfehlungsbegruendung und eine Rueckfrage entwerfen.
- Sitemap, Texte und Produktionsspezifikation vorbereiten.
- Code im isolierten Kundenrepository erzeugen und testen.

### KI darf nicht allein

- verbindlichen Preis oder Sonderprojekt freigeben.
- Vertragsannahme oder Zahlung markieren.
- Domain registrieren oder DNS veraendern.
- fachliche, rechtliche, medizinische oder Kundenfakten erfinden.
- Kundenvorschau oder Produktion veroeffentlichen.
- Abnahme erklaeren.

KI-Anbieter, Auftragsverarbeitung, Datenkategorien, Speicherfristen und Training muessen transparent und vertraglich geklaert sein. Sensible Unterlagen werden nur in einem dafuer freigegebenen Prozess verarbeitet. Agenten erhalten keine Portal-, Mollie-, Registrar- oder Produktionsschluessel.

---

## 20. Selbstpflege nach Launch

Im Kundenportal pflegbar:

- Oeffnungs- und Feiertagszeiten.
- Telefon, E-Mail, Anschrift, Kontaktperson und Links.
- vorhandene Team-, Stellen-, Projekt- und Referenzdatensaetze.
- Bild in einem vorhandenen Bildplatz.
- bestehende Seite voruebergehend deaktivieren und reaktivieren.

Keine Aenderungsminuten und kein Minutenguthaben.

Jede Aenderung hat Vorschau, Validierung, Version und Bearbeiter. Deaktivierte Seiten werden nicht geloescht; Navigation, Sitemap, Links und Redirect werden kontrolliert angepasst.

Nicht pflegbar: Layout, Farben, Schriften, Komponenten, URL, Navigation, freie Texte ohne Struktur, Code, Formulare oder Integrationen.

---

## 21. Kennzahlen

### Lumi

- Start- und Abschlussrate.
- Abbruch pro Thema.
- durchschnittliche Bearbeitungszeit.
- Anteil `unklar`.
- Standard/Gelb/Orange/Rot.
- Korrekturquote der Empfehlung durch Sartu.
- Zeit bis Angebot und Sartu-Pruefzeit.
- Annahmequote je Empfehlung.

### Onboarding

- aktive Kundeneingaben und Bearbeitungszeit je Paket.
- Anteil vorbefuellter und unveraendert bestaetigter Fakten.
- Rueckfragen je Projekt.
- spaet entdeckte Scope-Aenderungen.
- fehlende Materialien und Pausentage.

### Produktion

- Agentenkosten und Laufzeit.
- QA-Fehler je Kategorie.
- menschliche Produktionszeit.
- Korrekturrunden und Abnahmezeit.
- Supportarten und Marge je Paket.

Warnwerte: mehr als 20 Prozent Abbruch an einem Lumi-Thema, mehr als 15 Prozent manuelle Paketkorrekturen oder wiederholt mehr als eine Vorab-Rueckfrage. Zuerst Frage, Hilfetext und Regeln verbessern; nicht automatisch mehr KI oder mehr Felder hinzufuegen.

---

## 22. Freigabekriterien vor Marktstart

1. alle neuen Preise, Schutzstufen und Erstjahreswerte stimmen in Website, Lumi, Angebot und Portal.
2. Platzhirsch ist sichtbar empfohlen, aber Regeln koennen kleiner empfehlen.
3. alle 16 Logiktestfaelle laufen automatisiert.
4. Standardfall braucht ohne Sonderrisiko hoechstens eine Sartu-Pruefung von 15 Minuten.
5. Angebot zeigt Scope, Domain, Zahlung, Steuer und Betrieb widerspruchsfrei.
6. Mollie-Testzahlung und wiederkehrendes Mandat sind Ende zu Ende getestet.
7. INWX-Testregistrierung, DNS-Snapshot und Transferprozess funktionieren.
8. Onboarding zeigt bei Musterkunden nur relevante Aufgaben, verlangt Projektfakten fuer Leistungen/Belege/Conversion und stellt keine Technik-/Designfragen.
9. Codex-/Claude-Worker, QA, Vorschau, Deployment und Rollback funktionieren.
10. Oeffnungszeitaenderung und Seitendeaktivierung laufen versioniert durch.
11. Export und Domainuebergabe sind praktisch getestet.
12. Angebot, AGB, Datenschutz, AVV, Domain- und KI-Regeln sind rechtlich geprueft.

---

## 23. Endentscheidung

Der Kunde muss nicht hundert Fragen beantworten, weil Sartu drei Arten von Information konsequent trennt:

- Lumi fragt nur, was Paket und Machbarkeit beeinflusst.
- KI und Portal uebernehmen bereits vorhandene Fakten.
- Sartu behaelt alle professionellen Design-, Struktur-, SEO- und Technikentscheidungen.

So bleibt die Anfrage schnell, ohne dass das Angebot blind wird. Das gute Gefuehl entsteht nicht aus maximal wenig Text, sondern aus einer klaren Empfehlung, einer nachvollziehbaren Begruendung, einem echten Gesamtpreis und dem sichtbaren Versprechen, dass Sartu die schwierigen Entscheidungen uebernimmt.
