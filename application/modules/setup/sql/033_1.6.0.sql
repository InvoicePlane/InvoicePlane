# IP-636 - Convert quote status to invoiced if it has a linked invoice
UPDATE `ip_quotes` SET `quote_status_id` = 7
  WHERE `invoice_id` != 0;

# Add percent discount for invoice items.
ALTER TABLE `ip_invoice_items`
    ADD COLUMN `item_discount_percent` DECIMAL(20,2) DEFAULT NULL 
    AFTER `item_discount_amount`; 
