-- Portal-Lastenheft §4 `login_tokens`, Anmeldung ohne Passwort §6.
--
-- Gespeichert wird nur der Hash (§3 Regel 5). Wer die Datenbank liest, kann sich damit
-- nicht anmelden. token_hash traegt einen Pflichtindex (§4).
CREATE TABLE login_tokens (
  id           CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  user_id      CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  token_hash   VARCHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  expires_at   DATETIME NOT NULL,
  used_at      DATETIME NULL,
  requested_ip VARCHAR(45) NULL,
  UNIQUE KEY uq_login_tokens_token_hash (token_hash),
  KEY idx_login_tokens_user_id (user_id),
  CONSTRAINT fk_login_tokens_user FOREIGN KEY (user_id)
    REFERENCES users (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
