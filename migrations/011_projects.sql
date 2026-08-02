-- Portal-Lastenheft §4 `projects`, Statuswerte §5.1, Uebergaenge §5.1a.
--
-- Die Pruefbedingung listet die elf Zustaende aus §5.1. Sie ersetzt die Uebergangstabelle
-- nicht — welches Paar erlaubt ist, prueft der Dienst gegen §5.1a. Sie faengt nur den
-- erfundenen Zustand ab.
CREATE TABLE projects (
  id                        CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at                DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at                DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  organization_id           CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  title                     TEXT NOT NULL,
  package                   VARCHAR(20) NOT NULL,
  included_feedback_rounds  INT NOT NULL,
  protection_level          VARCHAR(1) NULL,
  protection_started_on     DATE NULL,
  protection_min_term_until DATE NULL,
  status                    VARCHAR(30) NOT NULL,
  paused_from_status        VARCHAR(30) NULL,
  pause_reason              TEXT NULL,
  next_step_text            TEXT NULL,
  next_step_url             TEXT NULL,
  preview_url               TEXT NULL,
  preview_published_at      DATETIME NULL,
  live_url                  TEXT NULL,
  launched_at               DATETIME NULL,
  archived_at               DATETIME NULL,
  KEY idx_projects_organization_id (organization_id),
  KEY idx_projects_status (status),
  CONSTRAINT fk_projects_organization FOREIGN KEY (organization_id)
    REFERENCES organizations (id) ON DELETE RESTRICT,
  CONSTRAINT chk_projects_package CHECK (package IN ('start','wachstum','platzhirsch','sonderprojekt')),
  CONSTRAINT chk_projects_protection CHECK (protection_level IS NULL OR protection_level IN ('s','m','l')),
  CONSTRAINT chk_projects_status CHECK (status IN (
    'angebot_offen','angebot_angenommen','zahlung_offen','briefing','produktion',
    'vorschau','korrektur','abnahme','launch_vorbereitung','live','pausiert'
  )),
  -- §5.1a: `paused_from_status` ist nur gesetzt, solange der Zustand `pausiert` ist.
  -- Beim Fortsetzen wird auf genau diesen Wert zurueckgesetzt, nie auf einen frei
  -- gewaehlten — ohne diese Bedingung liesse sich der Herkunftsstatus im Voraus setzen.
  CONSTRAINT chk_projects_pause CHECK (
    (status = 'pausiert' AND paused_from_status IS NOT NULL)
    OR (status <> 'pausiert' AND paused_from_status IS NULL)
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
