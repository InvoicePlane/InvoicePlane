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
ALTER TABLE ip_payment_methods
  MODIFY COLUMN payment_method_name TEXT;
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
ALTER TABLE ip_tasks
  MODIFY COLUMN task_name TEXT;
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
