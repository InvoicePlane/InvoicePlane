-- Solves IP-195
ALTER TABLE ip_quotes
  ADD COLUMN notes TEXT;

-- Solves IP-216
ALTER TABLE ip_invoices
  ADD COLUMN invoice_time_created TIME NOT NULL DEFAULT '00:00:00',
  ADD COLUMN invoice_password VARCHAR(90) NULL;

-- Solves IP-196
ALTER TABLE ip_invoices
  ADD COLUMN payment_method INT NOT NULL DEFAULT 0;

-- Solves IP-213
INSERT INTO ip_settings (setting_key, setting_value)
  VALUES ('invoice_pre_password', '');
INSERT INTO ip_settings (setting_key, setting_value)
  VALUES ('quote_pre_password', '');
ALTER TABLE ip_quotes
  ADD COLUMN quote_password VARCHAR(90) NULL;

-- Solves IP-227
ALTER TABLE ip_invoices
  ALTER COLUMN invoice_number TYPE VARCHAR(100),
  ALTER COLUMN invoice_number SET NOT NULL;
ALTER TABLE ip_quotes
  ALTER COLUMN quote_number TYPE VARCHAR(100),
  ALTER COLUMN quote_number SET NOT NULL;
