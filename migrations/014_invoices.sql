-- Portal-Lastenheft §4 `invoices`, Statuswerte §5.3, Zahlungen §12.
--
-- Geld ausnahmslos als integer in Cent (§4a). Nie Fliesskomma.
--
-- `paid_cents` schliesst eine Luecke aus dem Audit vom 01.08.2026: Vorher kannte `status`
-- nur bezahlt oder nicht bezahlt. Zahlt jemand 600 Euro auf eine Rechnung ueber 745, musste
-- der Admin zwischen zwei falschen Angaben waehlen.
--
-- ZWEI Erinnerungsfelder, nicht eines (§5.3a): Mit nur einem haette die zweite Mail ab Tag 7
-- JEDEN Tag ausgeloest, weil ihre Bedingung dauerhaft wahr bleibt. Derselbe Audit hat das
-- gefunden — Testfall 78 haette gegen die Spezifikation selbst geschlagen.
--
-- KEINE Pruefbedingung auf `paid_cents <= gross_cents`: §4 sagt ausdruecklich
-- „Ueberzahlung wird nicht abgewiesen, sondern gespeichert und im Adminbereich angezeigt".
CREATE TABLE invoices (
  id                     CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  project_id             CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  number                 VARCHAR(20) NOT NULL,
  milestone              VARCHAR(20) NOT NULL,
  status                 VARCHAR(20) NOT NULL DEFAULT 'entwurf',
  net_cents              INT NOT NULL,
  vat_cents              INT NOT NULL,
  gross_cents            INT NOT NULL,
  paid_cents             INT NOT NULL DEFAULT 0,
  due_date               DATE NULL,
  mollie_payment_url     TEXT NULL,
  paid_at                DATETIME NULL,
  marked_paid_by_user_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  note                   TEXT NULL,
  reminder_sent_at       DATETIME NULL,
  reminder2_sent_at      DATETIME NULL,
  archived_at            DATETIME NULL,
  UNIQUE KEY uq_invoices_number (number),
  KEY idx_invoices_project_id (project_id),
  KEY idx_invoices_status (status),
  KEY idx_invoices_due_date (due_date),
  CONSTRAINT fk_invoices_project FOREIGN KEY (project_id)
    REFERENCES projects (id) ON DELETE RESTRICT,
  CONSTRAINT fk_invoices_marked_paid_by FOREIGN KEY (marked_paid_by_user_id)
    REFERENCES users (id) ON DELETE RESTRICT,
  CONSTRAINT chk_invoices_milestone CHECK (
    milestone IN ('anzahlung','zwischenrate','schlussrate','betrieb')
  ),
  CONSTRAINT chk_invoices_status CHECK (
    status IN ('entwurf','gesendet','teilweise_bezahlt','bezahlt','ueberfaellig','storniert')
  ),
  CONSTRAINT chk_invoices_betraege CHECK (
    net_cents >= 0 AND vat_cents >= 0 AND gross_cents = net_cents + vat_cents AND paid_cents >= 0
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
