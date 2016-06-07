# IP-406 - Update the web preview for invoices and quotes
UPDATE ip_settings
SET setting_value='InvoicePlane_Web'
WHERE setting_key='public_invoice_template' AND
      setting_value='default';

UPDATE ip_settings
SET setting_value='InvoicePlane_Web'
WHERE setting_key='public_quote_template' AND
      setting_value='default';


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
ALTER TABLE ip_clients MODIFY COLUMN client_name VARCHAR(500);
ALTER TABLE ip_clients MODIFY COLUMN client_address_1 VARCHAR(500);
ALTER TABLE ip_clients MODIFY COLUMN client_address_2 VARCHAR(500);
ALTER TABLE ip_clients MODIFY COLUMN client_city VARCHAR(500);
ALTER TABLE ip_clients MODIFY COLUMN client_state VARCHAR(500);
ALTER TABLE ip_clients MODIFY COLUMN client_zip VARCHAR(500);
ALTER TABLE ip_clients MODIFY COLUMN client_country VARCHAR(500);
ALTER TABLE ip_clients MODIFY COLUMN client_phone VARCHAR(500);
ALTER TABLE ip_clients MODIFY COLUMN client_fax VARCHAR(500);
ALTER TABLE ip_clients MODIFY COLUMN client_mobile VARCHAR(500);
ALTER TABLE ip_clients MODIFY COLUMN client_email VARCHAR(500);
ALTER TABLE ip_clients MODIFY COLUMN client_web VARCHAR(500);
ALTER TABLE ip_clients MODIFY COLUMN client_vat_id VARCHAR(500);
ALTER TABLE ip_clients MODIFY COLUMN client_tax_code VARCHAR(500);
ALTER TABLE ip_custom_fields MODIFY COLUMN custom_field_table VARCHAR(500);
ALTER TABLE ip_custom_fields MODIFY COLUMN custom_field_label VARCHAR(500);
ALTER TABLE ip_custom_fields MODIFY COLUMN custom_field_column VARCHAR(500);
ALTER TABLE ip_email_templates MODIFY COLUMN email_template_title VARCHAR(500);
ALTER TABLE ip_email_templates MODIFY COLUMN email_template_subject VARCHAR(500);
ALTER TABLE ip_email_templates MODIFY COLUMN email_template_from_name VARCHAR(500);
ALTER TABLE ip_email_templates MODIFY COLUMN email_template_from_email VARCHAR(500);
ALTER TABLE ip_email_templates MODIFY COLUMN email_template_cc VARCHAR(500);
ALTER TABLE ip_email_templates MODIFY COLUMN email_template_bcc VARCHAR(500);
ALTER TABLE ip_families MODIFY COLUMN family_name VARCHAR(500);
ALTER TABLE ip_invoice_groups MODIFY COLUMN invoice_group_name VARCHAR(500);
ALTER TABLE ip_invoice_items MODIFY COLUMN item_name VARCHAR(500);
ALTER TABLE ip_invoice_items MODIFY COLUMN item_price DECIMAL(20,2);
ALTER TABLE ip_payment_methods MODIFY COLUMN payment_method_name VARCHAR(500);
ALTER TABLE ip_payments MODIFY COLUMN payment_amount DECIMAL(20,2);
ALTER TABLE ip_products MODIFY COLUMN product_sku VARCHAR(500);
ALTER TABLE ip_products MODIFY COLUMN product_name VARCHAR(500);
ALTER TABLE ip_projects MODIFY COLUMN project_name DECIMAL(20,2);
ALTER TABLE ip_quote_items MODIFY COLUMN item_name VARCHAR(500);
ALTER TABLE ip_quote_items MODIFY COLUMN item_quantity DECIMAL(20,2);
ALTER TABLE ip_quote_items MODIFY COLUMN item_price DECIMAL(20,2);
ALTER TABLE ip_quote_tax_rates MODIFY COLUMN quote_tax_rate_amount DECIMAL(20,2);
ALTER TABLE ip_tasks MODIFY COLUMN task_name VARCHAR(500);
ALTER TABLE ip_tasks MODIFY COLUMN task_price DECIMAL(20,2);
ALTER TABLE ip_tax_rates MODIFY COLUMN tax_rate_name VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_name VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_company VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_address_1 VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_address_2 VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_city VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_state VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_zip VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_country VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_phone VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_fax VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_mobile VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_email VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_web VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_vat_id VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_tax_code VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_psalt VARCHAR(500);
ALTER TABLE ip_users MODIFY COLUMN user_tax_code VARCHAR(500);


# IP-417 - Improve product database handling
ALTER TABLE ip_products MODIFY COLUMN family_id INT(11) NULL DEFAULT NULL;
ALTER TABLE ip_products MODIFY COLUMN tax_rate_id INT(11) NULL DEFAULT NULL;
ALTER TABLE ip_products ADD COLUMN provider_name VARCHAR(500) NULL DEFAULT NULL AFTER purchase_price;
