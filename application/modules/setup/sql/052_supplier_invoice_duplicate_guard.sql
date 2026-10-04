-- Prevent duplicate supplier invoice numbers for the same supplier.

ALTER TABLE `ip_supplier_invoices`
  ADD UNIQUE KEY `uq_supplier_invoice_supplier_number` (`supplier_id`, `supplier_invoice_number`);
