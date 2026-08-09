-- `payment_events` — jede eingegangene Zahlungsbenachrichtigung, fuer die Idempotenz.
-- Quelle: `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6, `13_DATENMODELL.md`.
--
-- ## Das `UNIQUE` ist die Idempotenz, nicht eine Absicherung daneben
--
-- §6: „Jede eingegangene Benachrichtigung wird mit ihrer Kennung festgehalten, **bevor**
-- verarbeitet wird. Eine bereits verarbeitete Kennung fuehrt zu einer Bestaetigung ohne
-- Wirkung — nicht zu einem Fehler."
--
-- Der Weg ist deshalb: einfuegen, und **wenn das Einfuegen scheitert**, war die Kennung schon
-- da. Eine vorgelagerte Abfrage „gibt es die Kennung schon?" haette zwischen Lesen und
-- Schreiben ein Fenster, in dem zwei gleichzeitige Zustellungen beide durchkaemen.
--
-- ## Warum `processed_at` getrennt von `received_at` steht
--
-- Angekommen und verarbeitet sind zwei Zeitpunkte. Bricht die Verarbeitung ab, steht die
-- Zeile mit leerem `processed_at` — und man sieht, dass etwas eintraf und liegenblieb.
--
-- ## Warum nur ein Hash und nicht der Rumpf
--
-- Der Rumpf einer Zahlungsbenachrichtigung kann personenbezogene Daten tragen. Gebraucht wird
-- er nur fuer die Frage „war das dieselbe Nachricht?" — dafuer genuegt der Hash.
CREATE TABLE payment_events (
  id                CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  provider_event_id VARCHAR(100) NOT NULL,
  invoice_id        CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NULL,
  received_at       DATETIME NOT NULL,
  processed_at      DATETIME NULL,
  payload_hash      CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  result            VARCHAR(30) NULL,
  UNIQUE KEY uq_payment_events_kennung (provider_event_id),
  KEY idx_payment_events_invoice (invoice_id),
  CONSTRAINT fk_payment_events_invoice FOREIGN KEY (invoice_id)
    REFERENCES invoices (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
