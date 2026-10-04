-- Reversible supplier invoice archiving.

ALTER TABLE `ip_supplier_invoices`
  ADD COLUMN IF NOT EXISTS `archived_at` DATETIME NULL AFTER `updated_at`,
  ADD COLUMN IF NOT EXISTS `archived_by` INT(11) NULL AFTER `archived_at`,
  ADD KEY `idx_supplier_invoice_archived_at` (`archived_at`);
