-- Portal-Lastenheft §4 `leads`, Anfrageeingang §4b.
--
-- Aufzaehlungsfelder (`status`, `flag`, `preferred_contact`) stehen als VARCHAR mit CHECK,
-- nicht als TEXT. Grund: MySQL laesst auf TEXT keinen Vorgabewert zu, und §4b verlangt
-- `flag` mit Vorgabe `standard`. §4.0 bildet „Freitext ohne Index" auf TEXT ab — ein
-- Statuswert aus vier Moeglichkeiten ist kein Freitext. Dieselbe Loesung wie bei
-- `legal_texts.status` in A0.
CREATE TABLE leads (
  id                       CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at               DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  submission_id            CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  submitted_at             DATETIME NOT NULL,
  payload                  JSON NOT NULL,
  first_name               TEXT NOT NULL,
  last_name                TEXT NOT NULL,
  company                  TEXT NOT NULL,
  email                    VARCHAR(255) NOT NULL,
  phone                    TEXT NULL,
  preferred_contact        VARCHAR(10) NOT NULL,
  recommended_package      VARCHAR(20) NULL,
  flag                     VARCHAR(10) NOT NULL DEFAULT 'standard',
  status                   VARCHAR(20) NOT NULL DEFAULT 'neu',
  b2b_confirmed            TINYINT(1) NOT NULL,
  privacy_confirmed        TINYINT(1) NOT NULL,
  source_ip                VARCHAR(45) NULL,
  branche_vorbelegt        VARCHAR(60) NULL,
  landing_page             TEXT NULL,
  referrer_host            TEXT NULL,
  utm_source               TEXT NULL,
  utm_medium               TEXT NULL,
  utm_campaign             TEXT NULL,
  utm_term                 TEXT NULL,
  utm_content              TEXT NULL,
  click_id                 TEXT NULL,
  self_reported_source     TEXT NULL,
  delete_after             DATE NOT NULL,
  converted_organization_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  admin_note               TEXT NULL,
  UNIQUE KEY uq_leads_submission_id (submission_id),
  KEY idx_leads_status (status),
  KEY idx_leads_delete_after (delete_after),
  KEY idx_leads_converted_organization_id (converted_organization_id),
  CONSTRAINT fk_leads_organization FOREIGN KEY (converted_organization_id)
    REFERENCES organizations (id) ON DELETE RESTRICT,
  CONSTRAINT chk_leads_preferred_contact CHECK (preferred_contact IN ('email','portal')),
  CONSTRAINT chk_leads_flag CHECK (flag IN ('standard','gelb','orange','rot')),
  CONSTRAINT chk_leads_status CHECK (status IN ('neu','in_pruefung','angebot_erstellt','abgelehnt')),
  CONSTRAINT chk_leads_package CHECK (
    recommended_package IS NULL
    OR recommended_package IN ('start','wachstum','platzhirsch','sonderprojekt','unklar')
  ),
  -- §4b.2: b2b_confirmed und privacy_confirmed muessen true sein, sonst wird nicht
  -- gespeichert. Die Bedingung haelt das fest, damit kein zweiter Schreibweg sie umgeht.
  CONSTRAINT chk_leads_bestaetigungen CHECK (b2b_confirmed = 1 AND privacy_confirmed = 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
