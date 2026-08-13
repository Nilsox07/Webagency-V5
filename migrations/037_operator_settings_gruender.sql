-- Die Gruenderangaben werden Betreiberdaten, nicht Quelltext.
-- Quelle: `SARTU_ENTSCHEIDUNGEN_OFFEN.md` §4c (Rang 1, entschieden 10.08.2026).
--
-- ## Warum das drei Felder sind und keine Konstante
--
-- §4c: „Die Sperre wird datengesteuert, nicht codegesteuert. Feld leer -> nichts wird
-- ausgeliefert. Feld gefuellt -> alles laeuft."
--
-- Bis dahin war die Sektion „Wer dahintersteckt" gar nicht gebaut, weil `[GRUENDER_NAME]`
-- und das Foto in §5 auf `offen` stehen. Das war zu woertlich: Der Betreiber konnte den
-- Namen nirgends eintragen, also blieb er offen, also wurde nicht gebaut — ein Kreis, aus
-- dem nur ein Feld herausfuehrt.
--
-- ## Warum das Bild als Dateiname und nicht als Blob
--
-- `documents` und `task_files` legen Dateien im Ablageverzeichnis ab und halten in der
-- Datenbank nur den erzeugten Namen. Dieselbe Form hier, damit es nicht zwei Wege gibt.
-- Der Name ist eine UUID aus PHP; der Originalname des Betreibers wird nie zum Pfad.
--
-- ## Warum keine Laengenbeschraenkung auf den Text in der Datenbank
--
-- `10_WEBSITE_SARTU.md` Sektion 6 bindet „zwei bis drei Saetze" fuer die Haltung. Das ist
-- eine Textregel und wird dort geprueft, wo ein Mensch die Meldung liest — nicht mit einem
-- abgeschnittenen Feld, das die letzten Woerter still verschluckt.
--
-- Eigene Migration statt einer Aenderung an 007: Migrationen werden nie geaendert, nur
-- ergaenzt (`14_SICHERHEIT.md` §1.5a).
ALTER TABLE operator_settings
  ADD COLUMN gruender_name VARCHAR(120) NULL AFTER inhaltlich_verantwortlich,
  ADD COLUMN gruender_text TEXT NULL AFTER gruender_name,
  ADD COLUMN gruender_bild VARCHAR(64) NULL AFTER gruender_text;
