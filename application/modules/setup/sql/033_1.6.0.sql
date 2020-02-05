# IP-636 - Convert quote status to invoiced if it has a linked invoice
UPDATE `ip_quotes` SET `quote_status_id` = 7
  WHERE `invoice_id` != 0;

# Add percent discount for invoice items.
ALTER TABLE `ip_invoice_items`
    ADD COLUMN `item_discount_percent` DECIMAL(20,2) DEFAULT NULL 
    AFTER `item_discount_amount`; 

# Add default for discount tax order
INSERT INTO `ip_settings` (`setting_key`, `setting_value`)
VALUES ('default_invoice_tax_rate_placement', '0');
