-- Portal-Lastenheft §4 `feedback_items`, Statuswerte §5.5.
--
-- `project_id` steht neben `feedback_round_id`, obwohl es sich ableiten liesse: §4 nennt es
-- ausdruecklich. Der Grund ist derselbe wie bei `task_files.organization_id` — eine
-- Rueckmeldung laesst sich damit pruefen, ohne ueber die Runde zu joinen.
CREATE TABLE feedback_items (
  id                CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  project_id        CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  feedback_round_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  body              TEXT NOT NULL,
  page_hint         TEXT NULL,
  status            VARCHAR(20) NOT NULL DEFAULT 'offen',
  created_by_user_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  answered_at       DATETIME NULL,
  answer_text       TEXT NULL,
  KEY idx_feedback_items_project_id (project_id),
  KEY idx_feedback_items_round_id (feedback_round_id),
  CONSTRAINT fk_feedback_items_project FOREIGN KEY (project_id)
    REFERENCES projects (id) ON DELETE RESTRICT,
  CONSTRAINT fk_feedback_items_round FOREIGN KEY (feedback_round_id)
    REFERENCES feedback_rounds (id) ON DELETE RESTRICT,
  CONSTRAINT fk_feedback_items_created_by FOREIGN KEY (created_by_user_id)
    REFERENCES users (id) ON DELETE RESTRICT,
  CONSTRAINT chk_feedback_items_status CHECK (status IN ('offen','beantwortet','erledigt'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
