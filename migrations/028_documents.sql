-- `documents` — jeder erzeugte Beleg mit Ablageort und Pruefsumme.
-- Quelle: `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 7, `13_DATENMODELL.md`.
--
-- ## Warum die Pruefsumme in der Datenbank steht und nicht neben der Datei
--
-- §7: „Die abgelegte Datei bekommt eine Pruefsumme. Weicht sie beim Abruf ab, ist das ein
-- Fehler und keine Warnung." Eine Pruefsumme, die neben der Datei liegt, aendert sich mit ihr.
-- Sie muss dort stehen, wo die Datei nicht hinreicht.
--
-- ## Warum `invoice_id` UND `offer_id`, beide NULL erlaubt
--
-- Ein Beleg gehoert zu genau einem von beiden. Zwei getrennte Tabellen haetten dieselbe
-- Ablage-, Pruefsummen- und Abruflogik zweimal gebraucht. Die Pruefbedingung unten laesst
-- genau eine Zuordnung zu — `CHECK (x IS NOT NULL OR y IS NOT NULL)` waere hier wirkungslos
-- (`13_DATENMODELL.md`), deshalb steht der Ausschluss ausgeschrieben.
--
-- ## Warum es kein `archived_at` gibt
--
-- §7: „Ein abgelegter Beleg wird nie ueberschrieben." Er wird auch nicht archiviert — die
-- Aufbewahrungsfrist von acht Jahren laeuft unabhaengig vom Projekt.
CREATE TABLE documents (
  id         CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  kind       VARCHAR(20) NOT NULL,
  number     VARCHAR(20) NOT NULL,
  invoice_id CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  offer_id   CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  path       VARCHAR(255) NOT NULL,
  checksum   CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  format     VARCHAR(30) NOT NULL,
  KEY idx_documents_invoice (invoice_id),
  KEY idx_documents_offer (offer_id),
  KEY idx_documents_nummer (number),
  CONSTRAINT fk_documents_invoice FOREIGN KEY (invoice_id)
    REFERENCES invoices (id) ON DELETE RESTRICT,
  CONSTRAINT fk_documents_offer FOREIGN KEY (offer_id)
    REFERENCES offers (id) ON DELETE RESTRICT,
  CONSTRAINT chk_documents_kind CHECK (kind IN ('angebot','rechnung','storno')),
  CONSTRAINT chk_documents_format CHECK (format IN ('pdf','pdfa3-zugferd','xml-en16931')),
  -- Genau eine Zuordnung, nie beide und nie keine.
  CONSTRAINT chk_documents_zuordnung CHECK (
    (invoice_id IS NOT NULL AND offer_id IS NULL)
    OR (invoice_id IS NULL AND offer_id IS NOT NULL)
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
