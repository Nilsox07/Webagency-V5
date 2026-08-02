-- Portal-Lastenheft §4 `approvals`.
--
-- „Protokolliert AUSSCHLIESSLICH Erklaerungen des Kunden, die spaeter beweisbar sein
-- muessen. Interne SARTU-Schritte gehoeren nicht hierher, sondern ins Audit-Log."
--
-- Deshalb genau zwei Arten: `inhalte` und `abnahme`. Kein `launch` — der Onlinegang ist
-- keine Kundenerklaerung. Kein `vorschau` — die Vorschau wird kommentiert, nicht
-- freigegeben.
--
-- Die Eindeutigkeit je Projekt und Art setzt §4 um: „Eine Erklaerung ist EINMALIG — ein
-- zweiter Versuch zeigt nur den vorhandenen Eintrag." Das steht als Schluessel und nicht
-- als Abfrage im Dienst, weil zwei gleichzeitige Klicks sonst beide durchkommen.
CREATE TABLE approvals (
  id               CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  project_id       CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  kind             VARCHAR(20) NOT NULL,
  granted_at       DATETIME NOT NULL,
  granted_by_user_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  granted_ip       VARCHAR(45) NULL,
  granted_name     TEXT NOT NULL,
  note             TEXT NULL,
  UNIQUE KEY uq_approvals_projekt_art (project_id, kind),
  CONSTRAINT fk_approvals_project FOREIGN KEY (project_id)
    REFERENCES projects (id) ON DELETE RESTRICT,
  CONSTRAINT fk_approvals_granted_by FOREIGN KEY (granted_by_user_id)
    REFERENCES users (id) ON DELETE RESTRICT,
  CONSTRAINT chk_approvals_kind CHECK (kind IN ('inhalte','abnahme'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
