-- Portal-Lastenheft §4: „Audit-Eintraege werden nie geaendert und nie geloescht."
-- Testfall 55 prueft das. Eine Absicht im Anwendungscode waere kein Beleg — wer eine
-- zweite Schreibstelle vergisst, merkt es nie. Die Datenbank sagt nein.
CREATE TRIGGER trg_audit_events_kein_update BEFORE UPDATE ON audit_events
FOR EACH ROW SIGNAL SQLSTATE '45000'
  SET MESSAGE_TEXT = 'Audit-Eintraege werden nie geaendert (Portal-Lastenheft §4).';
