-- 1.8.8 — Allow incoming e-invoice responses without a local invoice.
-- Incoming supplier invoices are archived before they are converted into a
-- local invoice, so ip_merchant_responses.invoice_id may legitimately be NULL.

ALTER TABLE `ip_merchant_responses`
  MODIFY COLUMN `invoice_id` INT(11) NULL;
