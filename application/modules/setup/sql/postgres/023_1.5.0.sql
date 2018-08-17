-- Module "units"
CREATE TABLE IF NOT EXISTS ip_units (
  unit_id        SERIAL,
  unit_name      VARCHAR(50)      DEFAULT NULL,
  unit_name_plrl VARCHAR(50)      DEFAULT NULL
);

ALTER TABLE ip_products
  ADD COLUMN unit_id INTEGER;

ALTER TABLE ip_quote_items
  ADD COLUMN item_product_unit VARCHAR(50) DEFAULT NULL,
  ADD COLUMN item_product_unit_id INTEGER;

ALTER TABLE ip_invoice_items
  ADD COLUMN item_product_unit VARCHAR(50) DEFAULT NULL,
  ADD COLUMN item_product_unit_id INTEGER;

-- Custom Field Enhancements
CREATE TABLE ip_custom_values (
  custom_values_id    SERIAL,
  custom_values_field INTEGER NOT NULL,
  custom_values_value TEXT    NOT NULL
);

ALTER TABLE ip_custom_fields
  ADD COLUMN custom_field_type VARCHAR(255) DEFAULT 'TEXT' NOT NULL;

-- Sumex changes
CREATE TABLE IF NOT EXISTS ip_invoice_sumex (
  sumex_id             SERIAL,
  sumex_invoice        INTEGER      NOT NULL,
  sumex_reason         INTEGER      NOT NULL,
  sumex_diagnosis      VARCHAR(500) NOT NULL,
  sumex_observations   VARCHAR(500) NOT NULL,
  sumex_treatmentstart DATE         NOT NULL,
  sumex_treatmentend   DATE         NOT NULL,
  sumex_casedate       DATE         NOT NULL,
  sumex_casenumber     VARCHAR(35) DEFAULT NULL
);

ALTER TABLE ip_clients
  ADD COLUMN client_surname VARCHAR(255) DEFAULT NULL,
  ADD COLUMN client_avs VARCHAR(16) DEFAULT NULL;

ALTER TABLE ip_clients
  ADD COLUMN client_insurednumber VARCHAR(30) DEFAULT NULL,
  ADD COLUMN client_veka VARCHAR(30) DEFAULT NULL,
  ADD COLUMN client_birthdate DATE DEFAULT NULL,
  ADD COLUMN client_gender SMALLINT DEFAULT 0;

ALTER TABLE ip_invoice_items
  ADD COLUMN item_date DATE;

INSERT INTO ip_settings (setting_key, setting_value)
VALUES
  ('sumex', '0'),
  ('sumex_sliptype', '1'),
  ('sumex_canton', '0');

ALTER TABLE ip_users
  ADD COLUMN user_subscribernumber VARCHAR(40) DEFAULT NULL,
  ADD COLUMN user_iban VARCHAR(34) DEFAULT NULL,
  ADD COLUMN user_gln BIGINT DEFAULT NULL,
  ADD COLUMN user_rcc VARCHAR(7) DEFAULT NULL;

ALTER TABLE ip_products
  ADD COLUMN product_tariff INTEGER DEFAULT NULL;

-- End Sumex Changes

-- Delete and re-add the ip_sessions table for CI 3
DROP TABLE ip_sessions;
CREATE TABLE IF NOT EXISTS ip_sessions (
  id         VARCHAR(128)               NOT NULL,
  ip_address VARCHAR(45)                NOT NULL,
  timestamp  INTEGER DEFAULT 0 NOT NULL CHECK (timestamp > 0),
  data       BYTEA                      NOT NULL
);

-- IP-491 - Localization per client and user
ALTER TABLE ip_users
  ADD COLUMN user_language VARCHAR(255) DEFAULT 'system';

ALTER TABLE ip_clients
  ADD COLUMN client_language VARCHAR(255) DEFAULT 'system';

-- Insert default theme into database (IP-338)
INSERT INTO ip_settings (setting_key, setting_value)
  VALUES ('system_theme', 'invoiceplane');

-- Feature IP-162
-- Allow invoice item to refer to a table products or to table tasks
ALTER TABLE ip_invoice_items
  ADD COLUMN item_task_id INTEGER DEFAULT NULL;

-- Add default hourly rate for tasks
INSERT INTO ip_settings (setting_key, setting_value)
  VALUES ('default_hourly_rate', '0.00');
INSERT INTO ip_settings (setting_key, setting_value)
  VALUES ('projects_enabled', '1');

-- Add tax rate to tasks
ALTER TABLE ip_tasks
  ADD COLUMN tax_rate_id INTEGER NOT NULL;

-- Switch to row based custom fields (instead of columns)
-- (See Mdl_setup for the migration script)

-- Custom Fields
ALTER TABLE ip_custom_fields
  ALTER COLUMN custom_field_table TYPE VARCHAR(50),
  ALTER COLUMN custom_field_label TYPE VARCHAR(50),
  ADD COLUMN custom_field_location INTEGER DEFAULT 0,
  ADD COLUMN custom_field_order INTEGER DEFAULT 999,
  ADD UNIQUE (custom_field_table, custom_field_label);
