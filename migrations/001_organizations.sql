-- Portal-Lastenheft §4 `organizations`, gemeinsame Felder nach §4.1.
-- Vor `users`, weil der Fremdschluessel dort mit ON DELETE RESTRICT angelegt wird
-- (REIHENFOLGE.md, Begruendung zu A0).
CREATE TABLE organizations (
  id            CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  legal_name    TEXT NOT NULL,
  brand_name    TEXT NULL,
  street        TEXT NULL,
  postal_code   TEXT NULL,
  city          TEXT NULL,
  vat_id        TEXT NULL,
  contact_email VARCHAR(255) NOT NULL,
  contact_phone TEXT NULL,
  archived_at   DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
