-- Betreiberentscheidung vom 02.08.2026: `ADMIN_NOTIFY_EMAIL` wird ein Feld in
-- `operator_settings` und unter /admin/einstellungen/betrieb gepflegt.
--
-- Vorher stand der Wert nur in §1.5 unter „Erforderliche Werte", wurde aber in keinem der
-- acht Einrichtungsschritte erhoben — gemeldet seit A0 in OFFENE_PRUEFUNGEN.md. Der Weg
-- ueber die .env blieb, weil eine erfundene Erhebung schlimmer gewesen waere als eine
-- gemeldete Luecke.
--
-- Ist das Feld leer, unterbleibt NUR die Anfragebenachrichtigung, und /admin fuehrt die
-- Zeile unter „fehlt noch". Kein erfundener Vorgabewert.
--
-- Eigene Migration statt einer Aenderung an 007: Migrationen werden nie geaendert, nur
-- ergaenzt (§1.5a).
ALTER TABLE operator_settings
  ADD COLUMN benachrichtigung_email VARCHAR(255) NULL AFTER email;
