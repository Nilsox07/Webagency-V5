-- Portal-Lastenheft §4 `business_hours`, Bildschirmansicht §8.7.
--
-- Die EINE Pflegefunktion, die der Kunde selbst hat. Alles andere aendern wir fuer ihn —
-- §8.7 sagt das dem Kunden auch so.
--
-- `weekday` 0 bis 6. Welcher Tag die 0 ist, steht im Lastenheft nicht; die Ansicht zaehlt
-- Montag bis Sonntag auf, also ist 0 der Montag. Das ist eine Festlegung, keine Annahme —
-- sie steht hier, damit sie nicht spaeter in einer Ansicht neu erfunden wird.
--
-- `UNIQUE (organization_id, weekday)`: Ein Betrieb hat je Wochentag genau eine Zeile. Ohne
-- diese Bedingung legt ein zweiter Absendevorgang eine zweite Montagszeile an, und die
-- Website zeigt zwei Montage.
--
-- **Keine Pruefbedingung `close_time > open_time` im Schema.** §8.7 verlangt die Meldung
-- „Die Bis-Zeit muss nach der Von-Zeit liegen." — also eine Fehlermeldung am Feld, keine
-- abgewiesene Anweisung. Eine Datenbankbedingung liefert dem Kunden einen Systemfehler statt
-- eines Satzes (§3 Regel 12). Geprueft wird serverseitig im Dienst, Testfall 19.
--
-- `pending_publish` heisst: geaendert, aber noch nicht auf der Website. §4: „Aenderungen
-- gelten erst nach Rebuild als veroeffentlicht."
CREATE TABLE business_hours (
  id              CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  organization_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  weekday         TINYINT NOT NULL,
  closed          TINYINT(1) NOT NULL DEFAULT 0,
  open_time       TIME NULL,
  close_time      TIME NULL,
  note            TEXT NULL,
  pending_publish TINYINT(1) NOT NULL DEFAULT 0,
  UNIQUE KEY uq_business_hours_organisation_tag (organization_id, weekday),
  CONSTRAINT fk_business_hours_organization FOREIGN KEY (organization_id)
    REFERENCES organizations (id) ON DELETE RESTRICT,
  CONSTRAINT chk_business_hours_weekday CHECK (weekday BETWEEN 0 AND 6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
