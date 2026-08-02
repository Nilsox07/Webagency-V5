-- Portal-Lastenheft §4 `domain_status`.
--
-- **Vollstaendig angelegt, nicht als Teiltabelle.** REIHENFOLGE.md sagt dazu ausdruecklich:
-- „Verschoben ist allein die Registrar-Anbindung. Eine Teiltabelle jetzt bedeutet eine
-- Folgemigration spaeter." Alle sechs Zustaende aus §4 stehen deshalb in der
-- Pruefbedingung, auch die, die in Stufe 0 nur von Hand gesetzt werden.
--
-- `project_id` ist UNIQUE: Ein Projekt hat genau eine Domainlage.
CREATE TABLE domain_status (
  id               CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  project_id       CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  desired_name     TEXT NULL,
  confirmed_name   TEXT NULL,
  owner_confirmed  TINYINT(1) NOT NULL DEFAULT 0,
  state            VARCHAR(20) NOT NULL DEFAULT 'offen',
  email_note       TEXT NULL,
  admin_note       TEXT NULL,
  UNIQUE KEY uq_domain_status_project (project_id),
  CONSTRAINT fk_domain_status_project FOREIGN KEY (project_id)
    REFERENCES projects (id) ON DELETE RESTRICT,
  CONSTRAINT chk_domain_status_state CHECK (
    state IN ('offen','vorschlaege_bereit','bestaetigt','registriert','verbunden','live')
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
