-- `invoices.issued_at` — das Ausstellungsdatum als Pflichtangabe nach § 14 Abs. 4 UStG.
-- Quelle: `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 2, `13_DATENMODELL.md`.
--
-- ## Warum nicht `created_at` genuegt
--
-- `created_at` ist der Zeitpunkt, an dem die Zeile entstand — also der Entwurf. Ausgestellt
-- ist eine Rechnung erst mit dem Versand. Zwischen beidem koennen Tage liegen, und auf dem
-- Beleg steht das Ausstellungsdatum, nicht das Anlagedatum.
--
-- ## Warum NULL erlaubt ist
--
-- Ein Entwurf ist noch nicht ausgestellt. `NULL` heisst genau das. Ein Vorgabewert
-- `CURRENT_TIMESTAMP` haette jedem Entwurf ein Ausstellungsdatum gegeben, das er nicht hat —
-- und bestehende Zeilen als ausgestellt markiert, ohne dass sie es waren.
ALTER TABLE invoices
  ADD COLUMN issued_at DATETIME NULL AFTER status;
