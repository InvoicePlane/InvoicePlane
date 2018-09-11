-- Fix the recurring invoices frequency
ALTER TABLE ip_invoices_recurring
  ALTER COLUMN recur_frequency TYPE VARCHAR(255);

ALTER TABLE ip_invoice_items
  ADD COLUMN item_is_recurring SMALLINT;
