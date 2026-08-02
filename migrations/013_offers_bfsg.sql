-- Portal-Lastenheft §4c, Pflichtpruefung zum Barrierefreiheitsstaerkungsgesetz.
--
-- §4c verlangt woertlich: „Beide Antworten werden im Angebot mitgespeichert und dort
-- sichtbar wiedergegeben — es sind Angaben des Kunden, keine Feststellung von SARTU."
-- Die Feldliste in §4 kennt sie nicht. Zwei Stellen im selben Dokument, und nach der
-- Rangfolgeregel gewinnt die mit der Begruendung: §4c begruendet ausfuehrlich, warum das
-- ins Datenmodell gehoert und nicht nur ins Konzept — Bussgeld bis 100.000 Euro.
--
-- Die Namen sind woertlich uebernommen, nicht gewaehlt.
--
-- Eigene Migration statt einer Aenderung an 012: Migrationen werden nie geaendert, nur
-- ergaenzt (§1.5a). Eine bereits ausgelieferte Datei anzufassen bricht den
-- Pruefsummenabgleich — das ist beabsichtigt.
ALTER TABLE offers
  ADD COLUMN bfsg_vertragsabschluss  VARCHAR(10) NULL AFTER domain_text,
  ADD COLUMN bfsg_kleinstunternehmen VARCHAR(10) NULL AFTER bfsg_vertragsabschluss,
  ADD CONSTRAINT chk_offers_bfsg_vertrag CHECK (
    bfsg_vertragsabschluss IS NULL OR bfsg_vertragsabschluss IN ('ja','nein')
  ),
  ADD CONSTRAINT chk_offers_bfsg_kleinst CHECK (
    bfsg_kleinstunternehmen IS NULL OR bfsg_kleinstunternehmen IN ('ja','nein','unbekannt')
  );
