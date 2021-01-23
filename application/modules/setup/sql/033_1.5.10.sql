# IP-717 Hotfix for item calculations
ALTER TABLE `ip_quote_items`
  ADD `item_discount_calc` TINYINT(1) DEFAULT 0
  AFTER `item_discount_amount`;
ALTER TABLE `ip_invoice_items`
  ADD `item_discount_calc` TINYINT(1) DEFAULT 0
  AFTER `item_discount_amount`;

ALTER TABLE `ip_quotes`
  ADD `quote_item_discount_calc` TINYINT(1) DEFAULT 0
  AFTER `quote_discount_percent`;
ALTER TABLE `ip_invoices`
  ADD `invoice_item_discount_calc` TINYINT(1) DEFAULT 0
  AFTER `invoice_discount_percent`;
