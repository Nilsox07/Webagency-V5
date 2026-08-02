-- Website-Lastenheft §5a — die Statusanzeige der Auftragslage.
--
-- „Der Wert wird im internen Bereich gepflegt und nie im Quelltext erfunden."
--
-- Drei Zustaende plus NULL. **NULL heisst: nichts wird angezeigt** — §5a fuehrt „nicht
-- gesetzt" ausdruecklich als vierte Zeile mit der Darstellung „nichts". Ein Vorgabewert
-- `offen` waere eine Behauptung ueber die Auftragslage, die niemand getroffen hat.
--
-- `knapp` heisst „Nur noch wenige Plaetze" und wird laut §5a nur gesetzt, wenn es zutrifft.
-- Als Dauerzustand waere es erfundene Knappheit — dieselbe Kategorie wie eine erfundene
-- Referenz. Das Schema kann das nicht erzwingen; der Adminbereich schreibt deshalb bei jeder
-- Aenderung ein Audit-Ereignis, damit sichtbar bleibt, seit wann der Wert steht.
ALTER TABLE operator_settings
  ADD COLUMN auftragslage VARCHAR(20) NULL AFTER kleinunternehmer,
  ADD CONSTRAINT chk_operator_settings_auftragslage
    CHECK (auftragslage IS NULL OR auftragslage IN ('offen','knapp','ausgebucht'));
