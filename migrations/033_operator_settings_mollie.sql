-- `operator_settings.mollie_key_test` und `mollie_key_live` — verschluesselt, nie im Klartext.
-- Quelle: `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6, `13_DATENMODELL.md`.
--
-- ## Warum zwei Felder und kein Schalter
--
-- §6: „Test- und Produktivschluessel sind getrennte Felder; welcher gilt, entscheidet
-- `APP_ENV`, nicht ein Schalter in der Oberflaeche." Ein Schalter waere ein Klick zwischen
-- Testbetrieb und echtem Geld.
--
-- ## Warum `TEXT` und nicht `VARCHAR(64)`
--
-- Gespeichert wird nicht der Schluessel, sondern sein verschluesselter Kasten aus
-- `sodium_*` — Nonce plus Geheimtext plus Beglaubigung, base64 kodiert. Er ist deutlich
-- laenger als der Schluessel selbst und waechst mit dem Verfahren.
--
-- ## Was diese Spalten NICHT bekommen
--
-- Keinen Index. Man sucht nicht nach einem Geheimnis, und ein Index waere eine zweite
-- Kopie davon, die keine Verschluesselung schuetzt.
ALTER TABLE operator_settings
  ADD COLUMN mollie_key_test TEXT NULL AFTER auftragslage,
  ADD COLUMN mollie_key_live TEXT NULL AFTER mollie_key_test;
