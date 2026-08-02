-- Portal-Lastenheft §4 `task_files`, Uploads §11.
--
-- `organization_id` steht hier ABSICHTLICH redundant. §4 nennt es „redundant, fuer die
-- Mandantenpruefung": Ohne diese Spalte muesste jede Dateiabfrage ueber `tasks` und
-- `projects` joinen, um die Organisation zu erreichen — und genau dieser Join ist die
-- Stelle, an der er irgendwann vergessen wird (Testfall 4).
--
-- `stored_name` ist eine UUID und NICHT der Originalname. Der Originalname kommt vom
-- Kunden und darf nie in einen Dateipfad geraten. Die Ablage liegt ausserhalb von /public.
CREATE TABLE task_files (
  id                 CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  task_id            CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  organization_id    CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  original_name      VARCHAR(255) NOT NULL,
  stored_name        CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  mime_type          VARCHAR(127) NOT NULL,
  size_bytes         BIGINT NOT NULL,
  rights_confirmed   TINYINT(1) NOT NULL DEFAULT 0,
  uploaded_by_user_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  archived_at        DATETIME NULL,
  UNIQUE KEY uq_task_files_stored_name (stored_name),
  KEY idx_task_files_task_id (task_id),
  KEY idx_task_files_organization_id (organization_id),
  CONSTRAINT fk_task_files_task FOREIGN KEY (task_id)
    REFERENCES tasks (id) ON DELETE RESTRICT,
  CONSTRAINT fk_task_files_organization FOREIGN KEY (organization_id)
    REFERENCES organizations (id) ON DELETE RESTRICT,
  CONSTRAINT fk_task_files_uploaded_by FOREIGN KEY (uploaded_by_user_id)
    REFERENCES users (id) ON DELETE RESTRICT,
  -- §11: Ohne bestaetigte Rechte wird nicht gespeichert (Testfall 17). Die Bedingung steht
  -- zusaetzlich im Dienst, mit lesbarer Meldung.
  CONSTRAINT chk_task_files_rechte CHECK (rights_confirmed = 1),
  CONSTRAINT chk_task_files_groesse CHECK (size_bytes > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
