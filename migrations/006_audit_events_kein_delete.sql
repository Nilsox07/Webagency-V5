-- Gegenstueck zu 005. Zweites Schemaobjekt, deshalb eigene Datei (§1.5).
CREATE TRIGGER trg_audit_events_kein_delete BEFORE DELETE ON audit_events
FOR EACH ROW SIGNAL SQLSTATE '45000'
  SET MESSAGE_TEXT = 'Audit-Eintraege werden nie geloescht (Portal-Lastenheft §4).';
