# Discounts
ALTER TABLE `ip_quotes`
ADD COLUMN `quote_discount_amount` DECIMAL(20,2) NOT NULL DEFAULT 0
AFTER `quote_number`,
ADD COLUMN `quote_discount_percent` DECIMAL(20,2) NOT NULL DEFAULT 0
AFTER `quote_discount_amount`;

ALTER TABLE `ip_quote_item_amounts`
ADD COLUMN `item_discount` DECIMAL(20,2) NOT NULL DEFAULT 0
AFTER `item_tax_total`;
ALTER TABLE `ip_quote_items`
ADD COLUMN `item_discount_amount` DECIMAL(20,2) NOT NULL DEFAULT 0
AFTER `item_price`;