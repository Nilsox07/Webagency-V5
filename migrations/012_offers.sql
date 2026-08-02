-- Portal-Lastenheft §4 `offers`, Statuswerte §5.2, Festtexte §4c.
--
-- „Ein angenommenes Angebot ist die vertragliche Grundlage. Es muss deshalb alles
-- enthalten, was spaeter strittig werden kann — nicht nur den Preis."
--
-- Betraege ausnahmslos als integer in Cent. Nie Fliesskomma fuer Geld (§4a).
CREATE TABLE offers (
  id                           CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at                   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at                   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  project_id                   CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  number                       VARCHAR(20) NOT NULL,
  status                       VARCHAR(20) NOT NULL,
  package                      VARCHAR(20) NOT NULL,
  summary                      TEXT NOT NULL,
  sitemap                      TEXT NOT NULL,
  inclusions                   TEXT NOT NULL,
  exclusions                   TEXT NOT NULL,
  scope_pages                  INT NULL,
  scope_words                  INT NULL,
  included_feedback_rounds     INT NOT NULL,
  delivery_days_min            INT NOT NULL,
  delivery_days_max            INT NOT NULL,
  delivery_start_condition     TEXT NOT NULL,
  one_time_net_cents           INT NOT NULL,
  protection_level             VARCHAR(1) NOT NULL,
  protection_monthly_net_cents INT NOT NULL,
  protection_min_term_months   INT NOT NULL,
  first_year_net_cents         INT NOT NULL,
  payment_plan                 VARCHAR(10) NOT NULL,
  payment_plan_custom          TEXT NULL,
  rights_text                  TEXT NOT NULL,
  domain_text                  TEXT NOT NULL,
  valid_until                  DATE NOT NULL,
  sent_at                      DATETIME NULL,
  accepted_at                  DATETIME NULL,
  accepted_by_user_id          CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  accepted_ip                  VARCHAR(45) NULL,
  accepted_name                TEXT NULL,
  UNIQUE KEY uq_offers_number (number),
  KEY idx_offers_project_id (project_id),
  KEY idx_offers_status (status),
  CONSTRAINT fk_offers_project FOREIGN KEY (project_id)
    REFERENCES projects (id) ON DELETE RESTRICT,
  CONSTRAINT fk_offers_accepted_by FOREIGN KEY (accepted_by_user_id)
    REFERENCES users (id) ON DELETE RESTRICT,
  CONSTRAINT chk_offers_status CHECK (status IN ('entwurf','gesendet','angenommen','abgelaufen','zurueckgezogen')),
  CONSTRAINT chk_offers_package CHECK (package IN ('start','wachstum','platzhirsch','sonderprojekt')),
  CONSTRAINT chk_offers_protection CHECK (protection_level IN ('s','m','l')),
  CONSTRAINT chk_offers_payment_plan CHECK (payment_plan IN ('50_50','40_30_30','custom')),
  -- §4 Pruefregel: first_year_net_cents = one_time + 12 x protection_monthly.
  -- Sie steht zusaetzlich im Dienst, mit lesbarer Meldung. Hier faengt sie den zweiten
  -- Schreibweg ab, den es irgendwann gibt.
  CONSTRAINT chk_offers_erstjahr CHECK (
    first_year_net_cents = one_time_net_cents + 12 * protection_monthly_net_cents
  ),
  -- §4 Pruefregel: `custom` nur beim Sonderprojekt, und dann mit Klartext der Raten.
  CONSTRAINT chk_offers_zahlungsplan CHECK (
    (payment_plan = 'custom' AND package = 'sonderprojekt' AND payment_plan_custom IS NOT NULL AND payment_plan_custom <> '')
    OR (payment_plan <> 'custom' AND payment_plan_custom IS NULL)
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
