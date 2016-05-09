# IP-406 - Update the web preview for invoices and quotes
UPDATE ip_settings
SET setting_value='InvoicePlane_Web'
WHERE setting_key='public_invoice_template' AND
      setting_value='default';

UPDATE ip_settings
SET setting_value='InvoicePlane_Web'
WHERE setting_key='public_quote_template' AND
      setting_value='default';

# IP-408 - Add reference to products to items
ALTER TABLE `ip_quote_items`
ADD COLUMN `item_product_id` INT(11) DEFAULT NULL
AFTER `item_tax_rate_id`;

ALTER TABLE `ip_quote_items`
ADD COLUMN `item_product_id` INT(11) DEFAULT NULL
AFTER `item_tax_rate_id`;
