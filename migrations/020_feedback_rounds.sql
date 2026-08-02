-- Portal-Lastenheft §4 `feedback_rounds`, Zaehlung §5.6a.
--
-- „Bildet die ENTHALTENEN Korrekturrunden ab — der zentrale Scope-Schutz des
-- Geschaeftsmodells."
--
-- `included = false` heisst: zusaetzliche, kostenpflichtige Runde. Das Portal blockiert
-- deshalb NICHTS und rechnet nichts ab — es macht den Stand nur sichtbar (§5.6a). Ueber
-- zusaetzlichen Aufwand entscheidet immer ein Mensch.
--
-- `number` ist je Projekt eindeutig. Ohne diese Bedingung koennten zwei gleichzeitig
-- geoeffnete Runden dieselbe Nummer tragen, und die Anzeige „Runde 2 von 2" waere zweimal
-- richtig und einmal falsch.
CREATE TABLE feedback_rounds (
  id           CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  project_id   CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  number       INT NOT NULL,
  status       VARCHAR(20) NOT NULL DEFAULT 'offen',
  opened_at    DATETIME NULL,
  submitted_at DATETIME NULL,
  completed_at DATETIME NULL,
  included     TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uq_feedback_rounds_projekt_nummer (project_id, number),
  KEY idx_feedback_rounds_status (status),
  CONSTRAINT fk_feedback_rounds_project FOREIGN KEY (project_id)
    REFERENCES projects (id) ON DELETE RESTRICT,
  CONSTRAINT chk_feedback_rounds_status CHECK (status IN ('offen','eingereicht','bearbeitet')),
  CONSTRAINT chk_feedback_rounds_nummer CHECK (number >= 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
