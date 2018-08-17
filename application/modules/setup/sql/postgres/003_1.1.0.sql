-- VAT ID and VAT Code added (see #76)
ALTER TABLE ip_email_templates
  ADD COLUMN email_template_type VARCHAR(255) NULL,
  ADD COLUMN email_template_subject VARCHAR(255) NULL,
  ADD COLUMN email_template_from_name VARCHAR(255) NULL,
  ADD COLUMN email_template_from_email VARCHAR(255) NULL,
  ADD COLUMN email_template_cc VARCHAR(255) NULL,
  ADD COLUMN email_template_bcc VARCHAR(255) NULL,
  ADD COLUMN email_template_pdf_template VARCHAR(255) NULL;

ALTER TABLE ip_clients
  ADD COLUMN client_vat_id VARCHAR(100) NOT NULL DEFAULT '',
  ADD COLUMN client_tax_code VARCHAR(100) NOT NULL DEFAULT '';

ALTER TABLE ip_users
  ADD COLUMN user_vat_id VARCHAR(100) NOT NULL DEFAULT '',
  ADD COLUMN user_tax_code VARCHAR(100) NOT NULL DEFAULT '',
  ADD COLUMN user_passwordreset_token VARCHAR(100) DEFAULT '',
  ADD COLUMN user_active BOOLEAN DEFAULT TRUE;

-- Allow quote/invoice amounts to be higher (see #84)
ALTER TABLE ip_quote_amounts
  ALTER COLUMN quote_item_subtotal TYPE DECIMAL(20, 2);
ALTER TABLE ip_quote_amounts
  ALTER COLUMN quote_item_tax_total TYPE DECIMAL(20, 2);
ALTER TABLE ip_quote_amounts
  ALTER COLUMN quote_tax_total TYPE DECIMAL(20, 2);
ALTER TABLE ip_quote_amounts
  ALTER COLUMN quote_total TYPE DECIMAL(20, 2);
ALTER TABLE ip_invoice_amounts
  ALTER COLUMN invoice_item_subtotal TYPE DECIMAL(20, 2);
ALTER TABLE ip_invoice_amounts
  ALTER COLUMN invoice_item_tax_total TYPE DECIMAL(20, 2);
ALTER TABLE ip_invoice_amounts
  ALTER COLUMN invoice_tax_total TYPE DECIMAL(20, 2);
ALTER TABLE ip_invoice_amounts
  ALTER COLUMN invoice_total TYPE DECIMAL(20, 2);
ALTER TABLE ip_invoice_amounts
  ALTER COLUMN invoice_paid TYPE DECIMAL(20, 2);
ALTER TABLE ip_invoice_amounts
  ALTER COLUMN invoice_balance TYPE DECIMAL(20, 2);
