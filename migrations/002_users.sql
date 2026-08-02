-- Portal-Lastenheft §4 `users`.
-- Die Pruefbedingung ist der Kern der Mandantentrennung (§3 Regel 2a): Ein Admin hat
-- bewusst KEINE organization_id, ein Kunde zwingend eine. Testfall 48 prueft beides.
CREATE TABLE users (
  id              CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  organization_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  email           VARCHAR(255) NOT NULL,
  first_name      TEXT NULL,
  last_name       TEXT NULL,
  role            VARCHAR(16) NOT NULL,
  password_hash   TEXT NULL,
  totp_secret_enc VARBINARY(255) NULL,
  welcome_seen_at DATETIME NULL,
  last_login_at   DATETIME NULL,
  archived_at     DATETIME NULL,
  UNIQUE KEY uq_users_email (email),
  KEY idx_users_organization_id (organization_id),
  CONSTRAINT fk_users_organization FOREIGN KEY (organization_id)
    REFERENCES organizations (id) ON DELETE RESTRICT,
  CONSTRAINT chk_users_role_organization CHECK (
    (role = 'admin' AND organization_id IS NULL)
    OR (role = 'kunde' AND organization_id IS NOT NULL)
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
