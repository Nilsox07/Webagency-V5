-- Portal-Lastenheft §4 `business_hours_exceptions`, Bildschirmansicht §8.7.
--
-- Feiertage und Betriebsurlaub. Eine Ausnahme ueberschreibt den Wochentag fuer genau ein
-- Datum — deshalb `UNIQUE (organization_id, date)`. Zwei Zeilen zum selben Tag waeren zwei
-- Antworten auf dieselbe Frage.
--
-- `pending_publish` steht bewusst NICHT hier. §4 nennt es nur an `business_hours`, und die
-- Ansicht reicht Wochentage und Ausnahmen in einem Formular ein: Es ist ein Vorgang, und
-- ein Vorgang hat eine Marke. Sie an zwei Tabellen zu fuehren hiesse, zwei Wahrheiten
-- darueber zu haben, ob etwas wartet.
CREATE TABLE business_hours_exceptions (
  id              CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  organization_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  date            DATE NOT NULL,
  closed          TINYINT(1) NOT NULL DEFAULT 1,
  open_time       TIME NULL,
  close_time      TIME NULL,
  label           VARCHAR(120) NOT NULL DEFAULT '',
  UNIQUE KEY uq_business_hours_exceptions_organisation_datum (organization_id, date),
  CONSTRAINT fk_business_hours_exceptions_organization FOREIGN KEY (organization_id)
    REFERENCES organizations (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
