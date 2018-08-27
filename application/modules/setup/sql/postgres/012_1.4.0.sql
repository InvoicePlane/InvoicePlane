-- Discounts
ALTER TABLE ip_quotes
  ADD COLUMN quote_discount_amount DECIMAL(20, 2) NOT NULL DEFAULT 0,
  ADD COLUMN quote_discount_percent DECIMAL(20, 2) NOT NULL DEFAULT 0;

ALTER TABLE ip_quote_item_amounts
  ADD COLUMN item_discount DECIMAL(20, 2) NOT NULL DEFAULT 0;
ALTER TABLE ip_quote_items
  ADD COLUMN item_discount_amount DECIMAL(20, 2) NOT NULL DEFAULT 0;

ALTER TABLE ip_invoices
  ADD COLUMN invoice_discount_amount DECIMAL(20, 2) NOT NULL DEFAULT 0,
  ADD COLUMN invoice_discount_percent DECIMAL(20, 2) NOT NULL DEFAULT 0;

ALTER TABLE ip_invoice_item_amounts
  ADD COLUMN item_discount DECIMAL(20, 2) NOT NULL DEFAULT 0;
ALTER TABLE ip_invoice_items
  ADD COLUMN item_discount_amount DECIMAL(20, 2) NOT NULL DEFAULT 0;

-- Feature IP-162
-- For module "projects" 
CREATE TABLE ip_projects (
  project_id   SERIAL,
  client_id    INTEGER      NOT NULL,
  project_name VARCHAR(150) NOT NULL
);

-- For module "tasks"
CREATE TABLE ip_tasks (
  task_id          SERIAL,
  project_id       INTEGER      NOT NULL,
  task_name        VARCHAR(50)  NOT NULL,
  task_description TEXT         NOT NULL,
  task_price       FLOAT(2)     NOT NULL,
  task_finish_date DATE         NOT NULL,
  task_status      SMALLINT     NOT NULL
);

-- For module "upload" IP-211
CREATE TABLE ip_uploads (
  upload_id          SERIAL,
  client_id          INTEGER  NOT NULL,
  url_key            CHAR(32) NOT NULL,
  file_name_original TEXT     NOT NULL,
  file_name_new      TEXT     NOT NULL,
  uploaded_date      DATE     NOT NULL
);

-- Attach pdf on emails setting
INSERT INTO ip_settings (setting_key, setting_value)
  VALUES ('email_pdf_attachment', '1');

-- IP-283 - Allow larger item amounts
ALTER TABLE ip_invoice_item_amounts
  ALTER COLUMN item_subtotal TYPE DECIMAL(20, 2);
ALTER TABLE ip_invoice_item_amounts
  ALTER COLUMN item_tax_total TYPE DECIMAL(20, 2);
ALTER TABLE ip_invoice_item_amounts
  ALTER COLUMN item_total TYPE DECIMAL(20, 2);
ALTER TABLE ip_quote_item_amounts
  ALTER COLUMN item_subtotal TYPE DECIMAL(20, 2);
ALTER TABLE ip_quote_item_amounts
  ALTER COLUMN item_tax_total TYPE DECIMAL(20, 2);
ALTER TABLE ip_quote_item_amounts
  ALTER COLUMN item_total TYPE DECIMAL(20, 2);
