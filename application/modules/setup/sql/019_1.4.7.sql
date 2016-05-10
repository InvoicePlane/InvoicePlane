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

# IP-322 - Invoice item_name database field should be larger + additional db changes
ALTER TABLE ip_invoice_items MODIFY COLUMN item_name VARCHAR(500);
ALTER TABLE ip_quote_items MODIFY COLUMN item_name VARCHAR(500);
ALTER TABLE ip_products MODIFY COLUMN product_name VARCHAR(500);
ALTER TABLE ip_families MODIFY COLUMN family_name VARCHAR(500);
ALTER TABLE ip_invoice_groups MODIFY COLUMN invoice_group_name VARCHAR(500);
ALTER TABLE ip_payment_methods MODIFY COLUMN payment_method_name VARCHAR(500);
ALTER TABLE ip_projects MODIFY COLUMN project_name VARCHAR(500);
ALTER TABLE ip_tasks MODIFY COLUMN task_name VARCHAR(500);
ALTER TABLE ip_tax_rates MODIFY COLUMN tax_rate_name VARCHAR(500);
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