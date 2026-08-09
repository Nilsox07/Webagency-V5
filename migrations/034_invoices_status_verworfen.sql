-- `invoices.status` bekommt den Wert `verworfen`.
-- Quelle: `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 5.
--
-- ## Warum ein siebter Zustand noetig ist
--
-- §5 unterscheidet drei Vorgaenge, die bisher vermischt waren. Zwei davon brauchen
-- verschiedene Zustaende:
--
--   * **Entwurf verwerfen** — nie versendet, Status auf `verworfen`, Nummer bleibt vergeben
--   * **Stornorechnung** — versendet, ein **neuer Beleg** hebt sie auf; die aufgehobene
--     Rechnung behaelt `storniert`
--
-- Ohne den siebten Wert stuende ein nie versendeter Entwurf auf `storniert` — und im
-- Nummernkreis sucht jemand nach der Stornorechnung, die es nie gab.
--
-- ## Warum die Bedingung ersetzt und nicht ergaenzt wird
--
-- Eine `CHECK`-Bedingung laesst sich nicht erweitern; sie wird abgeloest. `DROP` und `ADD` in
-- **einer** Anweisung, damit es keinen Moment ohne Pruefung gibt.
--
-- Diese Migration stand nicht im Plan vom 09.08.2026. Sie kam beim Bau von Abschnitt 5 dazu,
-- weil `verworfen` in der Zustandsliste fehlte. Migrationen werden ergaenzt, nie geaendert —
-- deshalb eine eigene statt einer Aenderung an 014.
ALTER TABLE invoices
  DROP CONSTRAINT chk_invoices_status,
  ADD CONSTRAINT chk_invoices_status CHECK (
    status IN ('entwurf','gesendet','teilweise_bezahlt','bezahlt','ueberfaellig','storniert','verworfen')
  );
