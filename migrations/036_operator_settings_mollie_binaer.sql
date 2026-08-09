-- `operator_settings.mollie_key_test` und `mollie_key_live` werden `VARBINARY`.
-- Quelle: `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6, Fall 93.
--
-- ## Warum 033 nicht geaendert, sondern abgeloest wird
--
-- Migrationen werden nie geaendert, nur ergaenzt (`14_SICHERHEIT.md` §1.5a). Die Pruefsumme
-- je Datei ist der Grund: Wer 033 nachtraeglich anfasst, laesst `migrate.php verify` auf
-- jeder Installation abbrechen, auf der 033 schon lief.
--
-- ## Was an `TEXT` falsch war
--
-- `Services\Verschluesselung` gibt rohe Bytes zurueck — Kennung, Nonce und Geheimtext, nicht
-- base64. In einer `TEXT`-Spalte mit `utf8mb4` ist das keine gueltige Zeichenkette; MariaDB
-- weist sie mit „Incorrect string value" ab. Der Kommentar an 033 behauptete base64 und
-- beschrieb damit etwas, das der Code nie tat.
--
-- `002_users.sql` hat es von Anfang an richtig: `totp_secret_enc VARBINARY(255)`. §6 sagt
-- „verschluesselt abgelegt, **wie das TOTP-Geheimnis**" — dann auch im selben Spaltentyp.
--
-- ## Warum 512 und nicht 255
--
-- Ein Mollie-Schluessel ist laenger als ein TOTP-Geheimnis, und der Kasten waechst mit dem
-- Verfahren. 512 Byte lassen Luft, ohne dass die Zeile in einen Ueberlauf geraet.
--
-- Ein Datenverlust entsteht dabei nicht: Zum Zeitpunkt dieser Migration hat die Spalte auf
-- keiner Installation je einen Wert angenommen — sie liess sich nicht beschreiben.
ALTER TABLE operator_settings
  MODIFY COLUMN mollie_key_test VARBINARY(512) NULL,
  MODIFY COLUMN mollie_key_live VARBINARY(512) NULL;
