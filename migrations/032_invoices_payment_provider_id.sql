-- `invoices.payment_provider_id` — die Kennung der Zahlung beim Dienst.
-- Quelle: `spezifikation/18_BELEGE_UND_ZAHLUNG.md` Abschnitt 6, `13_DATENMODELL.md`.
--
-- ## Wofuer die Kennung gebraucht wird
--
-- §6 Schritt 4: „Der Server ruft mit dieser Kennung den Zahlungsstatus **selbst** bei Mollie
-- ab." Ohne die Kennung an der Rechnung liesse sich eine eingehende Benachrichtigung keiner
-- Rechnung zuordnen — und der Abgleich aus Schritt 5 haette nichts zu vergleichen.
--
-- ## Warum `UNIQUE`
--
-- Eine Zahlung beim Dienst gehoert zu genau einer Rechnung. Traegen zwei Rechnungen dieselbe
-- Kennung, wuerde ein Webhook beide bewegen — und eine davon zu Unrecht.
ALTER TABLE invoices
  ADD COLUMN payment_provider_id VARCHAR(100) NULL AFTER mollie_payment_url,
  ADD UNIQUE KEY uq_invoices_payment_provider (payment_provider_id);
