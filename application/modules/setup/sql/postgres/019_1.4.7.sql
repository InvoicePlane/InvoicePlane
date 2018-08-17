-- IP-406 - Update the web preview for invoices and quotes
UPDATE ip_settings
SET setting_value = 'InvoicePlane_Web'
WHERE setting_key = 'public_invoice_template' AND
      setting_value = 'default';

UPDATE ip_settings
SET setting_value = 'InvoicePlane_Web'
WHERE setting_key = 'public_quote_template' AND
      setting_value = 'default';

-- IP-255 - Do not generate invoice number for draft invoices, set default value
INSERT INTO ip_settings (setting_key, setting_value)
  VALUES ('generate_invoice_number_for_draft', '1');

INSERT INTO ip_settings (setting_key, setting_value)
  VALUES ('generate_quote_number_for_draft', '1');

ALTER TABLE ip_invoices
  ALTER COLUMN invoice_number TYPE VARCHAR(100),
  ALTER COLUMN invoice_number DROP NOT NULL,
  ALTER COLUMN invoice_number SET DEFAULT NULL;

ALTER TABLE ip_quotes
  ALTER COLUMN quote_number TYPE VARCHAR(100),
  ALTER COLUMN quote_number DROP NOT NULL,
  ALTER COLUMN quote_number SET DEFAULT NULL;

-- IP-408 - Add reference to products to items
ALTER TABLE ip_invoice_items
  ADD COLUMN item_product_id INT(11) DEFAULT NULL;

ALTER TABLE ip_quote_items
  ADD COLUMN item_product_id INT(11) DEFAULT NULL;

-- IP-303 - Incorrect decimal value: '' for column 'item_discount_amount'
ALTER TABLE ip_invoices
  ALTER COLUMN invoice_discount_amount TYPE DECIMAL(20, 2),
  ALTER COLUMN invoice_discount_amount DROP NOT NULL,
  ALTER COLUMN invoice_discount_amount SET DEFAULT NULL,
  ALTER COLUMN invoice_discount_percent TYPE DECIMAL(20, 2),
  ALTER COLUMN invoice_discount_percent DROP NOT NULL,
  ALTER COLUMN invoice_discount_percent SET DEFAULT NULL;

ALTER TABLE ip_invoice_item_amounts
  ALTER COLUMN item_discount TYPE DECIMAL(20, 2),
  ALTER COLUMN item_discount DROP NOT NULL,
  ALTER COLUMN item_discount SET DEFAULT NULL;
ALTER TABLE ip_invoice_items
  ALTER COLUMN item_discount_amount TYPE DECIMAL(20, 2), 
  ALTER COLUMN item_discount_amount DROP NOT NULL, 
  ALTER COLUMN item_discount_amount SET DEFAULT NULL;

ALTER TABLE ip_invoice_tax_rates
  ALTER COLUMN invoice_tax_rate_amount TYPE DECIMAL(10, 2),
  ALTER COLUMN invoice_tax_rate_amount SET NOT NULL,
  ALTER COLUMN invoice_tax_rate_amount SET DEFAULT 0.00;

ALTER TABLE ip_quotes
  ALTER COLUMN quote_discount_amount TYPE DECIMAL(20, 2),
  ALTER COLUMN quote_discount_amount DROP NOT NULL,
  ALTER COLUMN quote_discount_amount SET DEFAULT NULL,
  ALTER COLUMN quote_discount_percent TYPE DECIMAL(20, 2),
  ALTER COLUMN quote_discount_percent DROP NOT NULL,
  ALTER COLUMN quote_discount_percent SET DEFAULT NULL;

ALTER TABLE ip_quote_item_amounts
  ALTER COLUMN item_discount TYPE DECIMAL(20, 2), 
  ALTER COLUMN item_discount DROP NOT NULL, 
  ALTER COLUMN item_discount SET DEFAULT NULL;
ALTER TABLE ip_quote_items
  ALTER COLUMN item_discount_amount TYPE DECIMAL(20, 2),
  ALTER COLUMN item_discount_amount DROP NOT NULL,
  ALTER COLUMN item_discount_amount SET DEFAULT NULL;

ALTER TABLE ip_products
  ALTER COLUMN purchase_price TYPE DECIMAL(20, 2),
  ALTER COLUMN purchase_price DROP NOT NULL,
  ALTER COLUMN purchase_price SET DEFAULT NULL;
ALTER TABLE ip_products
  ALTER COLUMN product_price TYPE DECIMAL(20, 2),
  ALTER COLUMN product_price DROP NOT NULL,
  ALTER COLUMN product_price SET DEFAULT NULL;

-- IP-322 - Invoice item_name database field should be larger + additional db changes
ALTER TABLE ip_clients
  ALTER COLUMN client_name TYPE TEXT;
ALTER TABLE ip_clients
  ALTER COLUMN client_address_1 TYPE TEXT;
ALTER TABLE ip_clients
  ALTER COLUMN client_address_2 TYPE TEXT;
ALTER TABLE ip_clients
  ALTER COLUMN client_city TYPE TEXT;
ALTER TABLE ip_clients
  ALTER COLUMN client_state TYPE TEXT;
ALTER TABLE ip_clients
  ALTER COLUMN client_zip TYPE TEXT;
ALTER TABLE ip_clients
  ALTER COLUMN client_country TYPE TEXT;
ALTER TABLE ip_clients
  ALTER COLUMN client_phone TYPE TEXT;
ALTER TABLE ip_clients
  ALTER COLUMN client_fax TYPE TEXT;
ALTER TABLE ip_clients
  ALTER COLUMN client_mobile TYPE TEXT;
ALTER TABLE ip_clients
  ALTER COLUMN client_email TYPE TEXT;
ALTER TABLE ip_clients
  ALTER COLUMN client_web TYPE TEXT;
ALTER TABLE ip_clients
  ALTER COLUMN client_vat_id TYPE TEXT;
ALTER TABLE ip_clients
  ALTER COLUMN client_tax_code TYPE TEXT;
ALTER TABLE ip_custom_fields
  ALTER COLUMN custom_field_table TYPE VARCHAR(255);
ALTER TABLE ip_custom_fields
  ALTER COLUMN custom_field_label TYPE TEXT;
ALTER TABLE ip_custom_fields
  ALTER COLUMN custom_field_column TYPE TEXT;
ALTER TABLE ip_email_templates
  ALTER COLUMN email_template_title TYPE TEXT;
ALTER TABLE ip_email_templates
  ALTER COLUMN email_template_subject TYPE TEXT;
ALTER TABLE ip_email_templates
  ALTER COLUMN email_template_from_name TYPE TEXT;
ALTER TABLE ip_email_templates
  ALTER COLUMN email_template_from_email TYPE TEXT;
ALTER TABLE ip_email_templates
  ALTER COLUMN email_template_cc TYPE TEXT;
ALTER TABLE ip_email_templates
  ALTER COLUMN email_template_bcc TYPE TEXT;
ALTER TABLE ip_families
  ALTER COLUMN family_name TYPE TEXT;
ALTER TABLE ip_invoice_groups
  ALTER COLUMN invoice_group_name TYPE TEXT;
ALTER TABLE ip_invoice_items
  ALTER COLUMN item_name TYPE TEXT,
  ALTER COLUMN item_name SET DEFAULT NULL;
ALTER TABLE ip_invoice_items
  ALTER COLUMN item_description TYPE TEXT,
  ALTER COLUMN item_description SET DEFAULT NULL;
ALTER TABLE ip_invoice_items
  ALTER COLUMN item_price TYPE DECIMAL(20, 2),
  ALTER COLUMN item_price SET DEFAULT NULL;
ALTER TABLE ip_payment_methods
  ALTER COLUMN payment_method_name TYPE TEXT;
ALTER TABLE ip_payments
  ALTER COLUMN payment_amount TYPE DECIMAL(20, 2);
ALTER TABLE ip_products
  ALTER COLUMN product_sku TYPE TEXT;
ALTER TABLE ip_products
  ALTER COLUMN product_name TYPE TEXT;
ALTER TABLE ip_projects
  ALTER COLUMN project_name TYPE TEXT;
ALTER TABLE ip_quote_items
  ALTER COLUMN item_name TYPE TEXT,
  ALTER COLUMN item_name SET DEFAULT NULL;
ALTER TABLE ip_quote_items
  ALTER COLUMN item_description TYPE TEXT,
  ALTER COLUMN item_description SET DEFAULT NULL;
ALTER TABLE ip_quote_items
  ALTER COLUMN item_quantity TYPE DECIMAL(20, 2),
  ALTER COLUMN item_quantity SET DEFAULT NULL;
ALTER TABLE ip_quote_items
  ALTER COLUMN item_price TYPE DECIMAL(20, 2);
ALTER TABLE ip_quote_tax_rates
  ALTER COLUMN quote_tax_rate_amount TYPE DECIMAL(20, 2);
ALTER TABLE ip_tasks
  ALTER COLUMN task_name TYPE TEXT;
ALTER TABLE ip_tasks
  ALTER COLUMN task_price TYPE DECIMAL(20, 2);
ALTER TABLE ip_tax_rates
  ALTER COLUMN tax_rate_name TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_name TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_company TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_address_1 TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_address_2 TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_city TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_state TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_zip TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_country TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_phone TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_fax TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_mobile TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_email TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_web TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_vat_id TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_tax_code TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_psalt TYPE TEXT;
ALTER TABLE ip_users
  ALTER COLUMN user_tax_code TYPE TEXT;

-- IP-417 - Improve product database handling
ALTER TABLE ip_products
  ALTER COLUMN family_id TYPE INTEGER,
  ALTER COLUMN family_id DROP NOT NULL,
  ALTER COLUMN family_id SET DEFAULT NULL;
ALTER TABLE ip_products
  ALTER COLUMN tax_rate_id TYPE INTEGER,
  ALTER COLUMN tax_rate_id DROP NOT NULL,
  ALTER COLUMN tax_rate_id SET DEFAULT NULL;
ALTER TABLE ip_products
  ADD COLUMN provider_name TEXT NULL DEFAULT NULL;

-- Change values for read-only setting
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

-- IP-422 - Improve session security
CREATE TABLE IF NOT EXISTS ip_sessions (
  session_id    VARCHAR(40) PRIMARY KEY DEFAULT '0',
  ip_address    VARCHAR(45) DEFAULT '0' NOT NULL,
  user_agent    VARCHAR(120)      NOT NULL,
  last_activity INTEGER DEFAULT 0 NOT NULL CHECK (last_activity > 0),
  user_data     TEXT              NOT NULL
);
