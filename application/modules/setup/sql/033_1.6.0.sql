# IP-636 - Convert quote status to invoiced if it has a linked invoice
UPDATE `ip_quotes` SET `quote_status_id` = 7
  WHERE `invoice_id` != 0;

# IP-710 - add column for item price with recalculated subtotal after discount / after tax
ALTER TABLE `ip_invoice_item_amounts` ADD `item_subtotal_recalc` DECIMAL(20,2) NULL;
ALTER TABLE `ip_quote_item_amounts` ADD `item_subtotal_recalc` DECIMAL(20,2) NULL;

# IP-710 - add column for item price type net/gross
ALTER TABLE `ip_invoice_items` ADD `item_price_isgross` TINYINT(1) NULL;
ALTER TABLE `ip_quote_items` ADD `item_price_isgross` TINYINT(1) NULL;
