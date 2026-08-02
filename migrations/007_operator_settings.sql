-- Portal-Lastenheft §4 `operator_settings` und §1.4a. Genau eine Zeile.
--
-- Der eindeutige Schluessel verhindert eine zweite Zeile, die Pruefbedingung das Umgehen
-- ueber einen anderen Wert (Testfall 64).
--
-- Die Steuerbedingung prueft ausdruecklich auf <> '': NOT NULL erlaubt eine leere
-- Zeichenkette, und ein Formular, das ein unausgefuelltes Feld als '' speichert, haette
-- CHECK (ust_id IS NOT NULL OR steuernummer IS NOT NULL) erfuellt — ohne Steuerangabe
-- (Testfall 65).
CREATE TABLE operator_settings (
  id                        CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at                DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at                DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  singleton                 TINYINT(1) NOT NULL DEFAULT 1,
  firmenname                VARCHAR(200) NOT NULL,
  rechtsform                VARCHAR(80) NULL,
  strasse                   VARCHAR(200) NOT NULL,
  plz                       VARCHAR(10) NOT NULL,
  ort                       VARCHAR(120) NOT NULL,
  land                      VARCHAR(2) NOT NULL,
  telefon                   VARCHAR(40) NULL,
  email                     VARCHAR(255) NOT NULL,
  ust_id                    VARCHAR(20) NULL,
  steuernummer              VARCHAR(30) NULL,
  registergericht           VARCHAR(120) NULL,
  registernummer            VARCHAR(40) NULL,
  inhaltlich_verantwortlich VARCHAR(200) NOT NULL,
  bank_iban                 VARCHAR(34) NULL,
  bank_bic                  VARCHAR(11) NULL,
  bank_institut             VARCHAR(120) NULL,
  kleinunternehmer          TINYINT(1) NOT NULL DEFAULT 0,
  setup_completed_at        DATETIME NULL,
  UNIQUE KEY uq_operator_settings_singleton (singleton),
  CONSTRAINT chk_operator_settings_singleton CHECK (singleton = 1),
  CONSTRAINT chk_operator_settings_steuer CHECK (
    (ust_id       IS NOT NULL AND ust_id       <> '')
    OR (steuernummer IS NOT NULL AND steuernummer <> '')
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
