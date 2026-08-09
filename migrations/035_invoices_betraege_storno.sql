-- `chk_invoices_betraege` laesst negative Betraege zu — aber nur zusammenhaengend.
-- Quelle: `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 5.
--
-- ## Warum die alte Bedingung nicht mehr passt
--
-- Sie verlangte `net_cents >= 0`. §5: Eine Stornorechnung ist ein „**neuer Beleg** mit eigener
-- Nummer und **negativen Betraegen**". Beides zugleich geht nicht.
--
-- ## Was die neue Bedingung dafuer festhaelt
--
-- Aufgegeben wird nur das Vorzeichen, nicht die Pruefung. Netto und Steuer muessen **dasselbe**
-- Vorzeichen tragen, und `gross = net + vat` gilt unveraendert. Damit ist eine Rechnung mit
-- negativem Netto und positiver Steuer weiterhin unmoeglich — genau der Fehler, den man in
-- einem Storno am ehesten macht.
--
-- `paid_cents >= 0` bleibt: Auf eine Stornorechnung zahlt niemand etwas ein.
--
-- ## Warum eine Stornorechnung nie ueberfaellig wird
--
-- Der taegliche Lauf filtert auf `paid_cents < gross_cents`. Bei einem negativen Brutto ist
-- `0 < -119000` falsch — die Zeile faellt aus jeder Erinnerung heraus, ohne dass es dafuer
-- eine eigene Bedingung braucht. Das ist kein Zufall, sondern der Grund, warum das Vorzeichen
-- die richtige Stelle ist.
ALTER TABLE invoices
  DROP CONSTRAINT chk_invoices_betraege,
  ADD CONSTRAINT chk_invoices_betraege CHECK (
    gross_cents = net_cents + vat_cents
    AND paid_cents >= 0
    AND ((net_cents >= 0 AND vat_cents >= 0) OR (net_cents <= 0 AND vat_cents <= 0))
  );
