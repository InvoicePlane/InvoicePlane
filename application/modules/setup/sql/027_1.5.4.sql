# Add invoice_currency to ip_invoices
ALTER TABLE `ip_invoices`
  ADD COLUMN `invoice_currency` INT(11) DEFAULT 1
  AFTER `invoice_status_id`;
