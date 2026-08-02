-- Portal-Lastenheft §4 `legal_texts` und §1.4a.
-- Fuenf Slugs: impressum, datenschutz, agb, avv, tom. `audience` steuert die Auslieferung —
-- `kunde` ist oeffentlich nicht abrufbar (Testfall 82).
--
-- Die Tabelle startet LEER. Rechtstexte sind in SARTU_ENTSCHEIDUNGEN_OFFEN.md §2 offen und
-- werden hier nicht erfunden.
CREATE TABLE legal_texts (
  id          CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  slug        VARCHAR(40) NOT NULL,
  body        MEDIUMTEXT NOT NULL,
  status      VARCHAR(20) NOT NULL,
  released_at DATETIME NULL,
  released_by VARCHAR(200) NULL,
  version     INT NOT NULL,
  audience    VARCHAR(20) NOT NULL,
  UNIQUE KEY uq_legal_texts_slug (slug),
  CONSTRAINT chk_legal_texts_slug CHECK (slug IN ('impressum','datenschutz','agb','avv','tom')),
  CONSTRAINT chk_legal_texts_status CHECK (status IN ('entwurf','in_pruefung','freigegeben')),
  CONSTRAINT chk_legal_texts_audience CHECK (audience IN ('oeffentlich','kunde'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
