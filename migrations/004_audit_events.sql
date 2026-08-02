-- Portal-Lastenheft §4 `audit_events`.
-- Pflichtindizes: organization_id und created_at.
CREATE TABLE audit_events (
  id              CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  actor_user_id   CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  organization_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  action          VARCHAR(64) NOT NULL,
  entity_type     VARCHAR(64) NOT NULL,
  entity_id       CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  old_value       TEXT NULL,
  new_value       TEXT NULL,
  reason          TEXT NULL,
  detail          JSON NULL,
  ip              VARCHAR(45) NULL,
  KEY idx_audit_events_organization_id (organization_id),
  KEY idx_audit_events_created_at (created_at),
  KEY idx_audit_events_actor_user_id (actor_user_id),
  CONSTRAINT fk_audit_events_actor FOREIGN KEY (actor_user_id)
    REFERENCES users (id) ON DELETE RESTRICT,
  CONSTRAINT fk_audit_events_organization FOREIGN KEY (organization_id)
    REFERENCES organizations (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
