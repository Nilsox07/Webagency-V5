-- `number_sequences` — der lueckenlose Zaehler je Belegart und Jahr.
-- Quelle: `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 4, `13_DATENMODELL.md`.
--
-- ## Warum es diese Tabelle gibt
--
-- Bis zum 09.08.2026 tippte der Admin die Rechnungsnummer ein. Eine getippte Nummer kann
-- doppelt vergeben werden, und sie kann eine auslassen. Beides ist bei einer Betriebspruefung
-- erklaerungsbeduerftig — §4: „Eine Luecke im Nummernkreis ist erklaerungsbeduerftig."
--
-- ## Warum der Schluessel aus Art UND Jahr besteht
--
-- Je Art und Jahr laeuft ein eigener Zaehler, der im neuen Jahr wieder bei 001 beginnt.
-- Ein gemeinsamer Zaehler ueber alle Arten haette `RE-2026-001` und `AN-2026-002` erzeugt —
-- lueckenhaft in beiden Reihen, obwohl nichts fehlt.
--
-- ## Was diese Tabelle NICHT tut
--
-- Sie vergibt nicht. Die Vergabe geschieht im Dienst, unter `SELECT ... FOR UPDATE` und in
-- derselben Transaktion wie der Beleg. Diese Tabelle haelt nur den Stand.
CREATE TABLE number_sequences (
  id          CHAR(36) CHARACTER SET ascii COLLATE ascii_bin NOT NULL PRIMARY KEY,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  kind        VARCHAR(2) NOT NULL,
  year        SMALLINT NOT NULL,
  last_number INT NOT NULL DEFAULT 0,
  UNIQUE KEY uq_number_sequences_art_jahr (kind, year),
  CONSTRAINT chk_number_sequences_kind CHECK (kind IN ('AN','RE','ST')),
  CONSTRAINT chk_number_sequences_jahr CHECK (year BETWEEN 2000 AND 9999),
  -- Ein Zaehler laeuft vorwaerts. Ein negativer Stand waere ein Schreibfehler, kein Zustand.
  CONSTRAINT chk_number_sequences_stand CHECK (last_number >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
