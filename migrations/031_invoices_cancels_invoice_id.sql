-- `invoices.cancels_invoice_id` — der Verweis der Stornorechnung auf die stornierte Rechnung.
-- Quelle: `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 5, `13_DATENMODELL.md`.
--
-- ## Warum die Stornorechnung eine Zeile in derselben Tabelle ist
--
-- Sie ist eine Rechnung: eigene Nummer, eigenes Ausstellungsdatum, eigene Betraege, dieselben
-- Pflichtangaben. `13_DATENMODELL.md` nennt fuer Stufe C drei neue Tabellen, und keine davon
-- heisst `credit_notes` — der Verweis ist ein Feld, keine Tabelle.
--
-- ## `ON DELETE RESTRICT`, und warum das hier mehr ist als eine Gewohnheit
--
-- Eine stornierte Rechnung darf nicht verschwinden, solange ihr Storno auf sie zeigt. Sonst
-- stuende im Nummernkreis eine Aufhebung ohne das, was sie aufhebt.
--
-- ## Warum kein `UNIQUE`
--
-- Es waere fachlich richtig — eine Rechnung wird einmal storniert — aber die Sperre gehoert
-- in den Dienst, wo sie eine lesbare Meldung erzeugen kann. Ein Datenbankfehler an dieser
-- Stelle waere eine interne Kennung fuer den Admin statt eines Satzes.
ALTER TABLE invoices
  ADD COLUMN cancels_invoice_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL AFTER issued_at,
  ADD CONSTRAINT fk_invoices_cancels FOREIGN KEY (cancels_invoice_id)
    REFERENCES invoices (id) ON DELETE RESTRICT;
