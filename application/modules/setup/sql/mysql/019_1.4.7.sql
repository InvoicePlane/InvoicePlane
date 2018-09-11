# IP-406 - Update the web preview for invoices and quotes
UPDATE ip_settings
SET setting_value = 'InvoicePlane_Web'
WHERE setting_key = 'public_invoice_template' AND
      setting_value = 'default';

UPDATE ip_settings
SET setting_value = 'InvoicePlane_Web'
WHERE setting_key = 'public_quote_template' AND
      setting_value = 'default';

# IP-255 - Do not generate invoice number for draft invoices, set default value
INSERT INTO ip_settings (setting_key, setting_value)
VALUES ('generate_invoice_number_for_draft', '1');

INSERT INTO ip_settings (setting_key, setting_value)
VALUES ('generate_quote_number_for_draft', '1');

ALTER TABLE `ip_invoices`
  MODIFY COLUMN invoice_number VARCHAR(100) NULL DEFAULT NULL;

ALTER TABLE `ip_quotes`
  MODIFY COLUMN quote_number VARCHAR(100) NULL DEFAULT NULL;

# IP-408 - Add reference to products to items
ALTER TABLE `ip_invoice_items`
  ADD COLUMN `item_product_id` INT(11) DEFAULT NULL
  AFTER `item_tax_rate_id`;

ALTER TABLE `ip_quote_items`
  ADD COLUMN `item_product_id` INT(11) DEFAULT NULL
  AFTER `item_tax_rate_id`;

# IP-303 - Incorrect decimal value: '' for column 'item_discount_amount'
ALTER TABLE `ip_invoices`
  MODIFY COLUMN invoice_discount_amount DECIMAL(20, 2) NULL DEFAULT NULL,
  MODIFY COLUMN invoice_discount_percent DECIMAL(20, 2) NULL DEFAULT NULL;

ALTER TABLE `ip_invoice_item_amounts`
  MODIFY COLUMN item_discount DECIMAL(20, 2) NULL DEFAULT NULL;
ALTER TABLE `ip_invoice_items`
  MODIFY COLUMN item_discount_amount DECIMAL(20, 2) NULL DEFAULT NULL;

ALTER TABLE `ip_invoice_tax_rates`
  MODIFY COLUMN invoice_tax_rate_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00;

ALTER TABLE `ip_quotes`
  MODIFY COLUMN quote_discount_amount DECIMAL(20, 2) NULL DEFAULT NULL,
  MODIFY COLUMN quote_discount_percent DECIMAL(20, 2) NULL DEFAULT NULL;

ALTER TABLE `ip_quote_item_amounts`
  MODIFY COLUMN item_discount DECIMAL(20, 2) NULL DEFAULT NULL;
ALTER TABLE `ip_quote_items`
  MODIFY COLUMN item_discount_amount DECIMAL(20, 2) NULL DEFAULT NULL;

ALTER TABLE `ip_products`
  MODIFY COLUMN purchase_price DECIMAL(20, 2) NULL DEFAULT NULL;
ALTER TABLE `ip_products`
  MODIFY COLUMN product_price DECIMAL(20, 2) NULL DEFAULT NULL;

# IP-322 - Invoice item_name database field should be larger + additional db changes
ALTER TABLE ip_clients
  MODIFY COLUMN client_name TEXT;
ALTER TABLE ip_clients
  MODIFY COLUMN client_address_1 TEXT;
ALTER TABLE ip_clients
  MODIFY COLUMN client_address_2 TEXT;
ALTER TABLE ip_clients
  MODIFY COLUMN client_city TEXT;
ALTER TABLE ip_clients
  MODIFY COLUMN client_state TEXT;
ALTER TABLE ip_clients
  MODIFY COLUMN client_zip TEXT;
ALTER TABLE ip_clients
  MODIFY COLUMN client_country TEXT;
ALTER TABLE ip_clients
  MODIFY COLUMN client_phone TEXT;
ALTER TABLE ip_clients
  MODIFY COLUMN client_fax TEXT;
ALTER TABLE ip_clients
  MODIFY COLUMN client_mobile TEXT;
ALTER TABLE ip_clients
  MODIFY COLUMN client_email TEXT;
ALTER TABLE ip_clients
  MODIFY COLUMN client_web TEXT;
ALTER TABLE ip_clients
  MODIFY COLUMN client_vat_id TEXT;
ALTER TABLE ip_clients
  MODIFY COLUMN client_tax_code TEXT;
ALTER TABLE ip_custom_fields
  MODIFY COLUMN custom_field_table VARCHAR(255);
ALTER TABLE ip_custom_fields
  MODIFY COLUMN custom_field_label TEXT;
ALTER TABLE ip_custom_fields
  MODIFY COLUMN custom_field_column TEXT;
ALTER TABLE ip_email_templates
  MODIFY COLUMN email_template_title TEXT;
ALTER TABLE ip_email_templates
  MODIFY COLUMN email_template_subject TEXT;
ALTER TABLE ip_email_templates
  MODIFY COLUMN email_template_from_name TEXT;
ALTER TABLE ip_email_templates
  MODIFY COLUMN email_template_from_email TEXT;
ALTER TABLE ip_email_templates
  MODIFY COLUMN email_template_cc TEXT;
ALTER TABLE ip_email_templates
  MODIFY COLUMN email_template_bcc TEXT;
ALTER TABLE ip_families
  MODIFY COLUMN family_name TEXT;
ALTER TABLE ip_invoice_groups
  MODIFY COLUMN invoice_group_name TEXT;
ALTER TABLE ip_invoice_items
  MODIFY COLUMN item_name TEXT DEFAULT NULL;
ALTER TABLE ip_invoice_items
  MODIFY COLUMN item_description LONGTEXT DEFAULT NULL;
ALTER TABLE ip_invoice_items
  MODIFY COLUMN item_price DECIMAL(20, 2) DEFAULT NULL;
ALTER TABLE ip_payment_methods
  MODIFY COLUMN payment_method_name TEXT;
ALTER TABLE ip_payments
  MODIFY COLUMN payment_amount DECIMAL(20, 2);
ALTER TABLE ip_products
  MODIFY COLUMN product_sku TEXT;
ALTER TABLE ip_products
  MODIFY COLUMN product_name TEXT;
ALTER TABLE ip_projects
  MODIFY COLUMN project_name TEXT;
ALTER TABLE ip_quote_items
  MODIFY COLUMN item_name TEXT DEFAULT NULL;
ALTER TABLE ip_quote_items
  MODIFY COLUMN item_description TEXT DEFAULT NULL;
ALTER TABLE ip_quote_items
  MODIFY COLUMN item_quantity DECIMAL(20, 2) DEFAULT NULL;
ALTER TABLE ip_quote_items
  MODIFY COLUMN item_price DECIMAL(20, 2);
ALTER TABLE ip_quote_tax_rates
  MODIFY COLUMN quote_tax_rate_amount DECIMAL(20, 2);
ALTER TABLE ip_tasks
  MODIFY COLUMN task_name TEXT;
ALTER TABLE ip_tasks
  MODIFY COLUMN task_price DECIMAL(20, 2);
ALTER TABLE ip_tax_rates
  MODIFY COLUMN tax_rate_name TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_name TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_company TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_address_1 TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_address_2 TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_city TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_state TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_zip TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_country TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_phone TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_fax TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_mobile TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_email TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_web TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_vat_id TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_tax_code TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_psalt TEXT;
ALTER TABLE ip_users
  MODIFY COLUMN user_tax_code TEXT;

# IP-417 - Improve product database handling
ALTER TABLE ip_products
  MODIFY COLUMN family_id INT(11) NULL DEFAULT NULL;
ALTER TABLE ip_products
  MODIFY COLUMN tax_rate_id INT(11) NULL DEFAULT NULL;
ALTER TABLE ip_products
  ADD COLUMN provider_name TEXT NULL DEFAULT NULL
  AFTER purchase_price;

# Change values for read-only setting
UPDATE ip_settings
SET setting_value = 2
WHERE setting_key = 'read_only_toggle' AND
      setting_value = 'sent';

UPDATE ip_settings
SET setting_value = 3
WHERE setting_key = 'read_only_toggle' AND
      setting_value = 'viewed';

UPDATE ip_settings
SET setting_value = 4
WHERE setting_key = 'read_only_toggle' AND
      setting_value = 'paid';

# IP-422 - Improve session security
CREATE TABLE IF NOT EXISTS `ip_sessions` (
  session_id    VARCHAR(40) DEFAULT '0'    NOT NULL,
  ip_address    VARCHAR(45) DEFAULT '0'    NOT NULL,
  user_agent    VARCHAR(120)               NOT NULL,
  last_activity INT(10) UNSIGNED DEFAULT 0 NOT NULL,
  user_data     TEXT                       NOT NULL,
  PRIMARY KEY (session_id),
  KEY `last_activity_idx` (`last_activity`)
);
