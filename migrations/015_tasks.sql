-- Portal-Lastenheft §4 `tasks`, Statuswerte §5.4, Oberflaeche §8.3.
--
-- `required` steuert die Freigabesperre: Die Aufgabe der Art `freigabe` laesst sich nicht
-- abschliessen, solange eine Pflichtaufgabe offen ist (Testfall 26). Die Bedingung dafuer
-- steht im Dienst — die Datenbank kennt nur die Kennzeichnung.
CREATE TABLE tasks (
  id                  CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  project_id          CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  title               TEXT NOT NULL,
  description         TEXT NULL,
  why_needed          TEXT NULL,
  kind                VARCHAR(20) NOT NULL,
  status              VARCHAR(20) NOT NULL DEFAULT 'offen',
  sort_order          INT NOT NULL DEFAULT 0,
  answer_text         TEXT NULL,
  completed_at        DATETIME NULL,
  completed_by_user_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  required            TINYINT(1) NOT NULL DEFAULT 1,
  archived_at         DATETIME NULL,
  KEY idx_tasks_project_id (project_id),
  KEY idx_tasks_status (status),
  CONSTRAINT fk_tasks_project FOREIGN KEY (project_id)
    REFERENCES projects (id) ON DELETE RESTRICT,
  CONSTRAINT fk_tasks_completed_by FOREIGN KEY (completed_by_user_id)
    REFERENCES users (id) ON DELETE RESTRICT,
  CONSTRAINT chk_tasks_kind CHECK (kind IN ('bestaetigung','angabe','upload','freigabe')),
  CONSTRAINT chk_tasks_status CHECK (status IN ('offen','erledigt'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
