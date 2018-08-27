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
ALTER TABLE ip_payment_methods
  ALTER COLUMN payment_method_name TYPE TEXT;
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
ALTER TABLE ip_tasks
  ALTER COLUMN task_name TYPE TEXT;
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
