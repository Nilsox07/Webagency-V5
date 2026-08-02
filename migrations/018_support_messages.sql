-- Portal-Lastenheft §4 `support_messages`, Oberflaeche §8.9 „Hilfe".
--
-- `organization_id` steht direkt an der Nachricht, nicht nur ueber `project_id`: §4 laesst
-- `project_id` ausdruecklich leer (nullable) — eine Frage kann vor dem ersten Projekt
-- kommen. Ohne die eigene Spalte gaebe es fuer diese Nachrichten keinen Mandantenfilter.
CREATE TABLE support_messages (
  id                 CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  organization_id    CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  project_id         CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  body               TEXT NOT NULL,
  created_by_user_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  answered_at        DATETIME NULL,
  answer_text        TEXT NULL,
  archived_at        DATETIME NULL,
  KEY idx_support_messages_organization_id (organization_id),
  KEY idx_support_messages_project_id (project_id),
  CONSTRAINT fk_support_messages_organization FOREIGN KEY (organization_id)
    REFERENCES organizations (id) ON DELETE RESTRICT,
  CONSTRAINT fk_support_messages_project FOREIGN KEY (project_id)
    REFERENCES projects (id) ON DELETE RESTRICT,
  CONSTRAINT fk_support_messages_created_by FOREIGN KEY (created_by_user_id)
    REFERENCES users (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
