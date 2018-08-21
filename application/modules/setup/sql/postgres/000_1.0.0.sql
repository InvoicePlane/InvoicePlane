CREATE TABLE ip_client_custom (
  client_custom_id SERIAL,
  client_id        INTEGER NOT NULL
);

CREATE TABLE ip_client_notes (
  client_note_id   SERIAL,
  client_id        INTEGER  NOT NULL,
  client_note_date DATE     NOT NULL,
  client_note      TEXT     NOT NULL
);

CREATE TABLE ip_clients (
  client_id            SERIAL,
  client_date_created  TIMESTAMP    NOT NULL,
  client_date_modified TIMESTAMP    NOT NULL,
  client_name          VARCHAR(100) NOT NULL,
  client_address_1     VARCHAR(100)          DEFAULT '',
  client_address_2     VARCHAR(100)          DEFAULT '',
  client_city          VARCHAR(45)           DEFAULT '',
  client_state         VARCHAR(35)           DEFAULT '',
  client_zip           VARCHAR(15)           DEFAULT '',
  client_country       VARCHAR(35)           DEFAULT '',
  client_phone         VARCHAR(20)           DEFAULT '',
  client_fax           VARCHAR(20)           DEFAULT '',
  client_mobile        VARCHAR(20)           DEFAULT '',
  client_email         VARCHAR(100)          DEFAULT '',
  client_web           VARCHAR(100)          DEFAULT '',
  client_active        SMALLINT     NOT NULL DEFAULT '1'
);

CREATE TABLE ip_custom_fields (
  custom_field_id     SERIAL,
  custom_field_table  VARCHAR(35) NOT NULL,
  custom_field_label  VARCHAR(64) NOT NULL,
  custom_field_column VARCHAR(64) NOT NULL
);

CREATE TABLE ip_email_templates (
  email_template_id    SERIAL,
  email_template_title VARCHAR(255) NOT NULL,
  email_template_body  TEXT         NOT NULL
);

CREATE TABLE ip_import_details (
  import_detail_id  SERIAL,
  import_id         INTEGER     NOT NULL,
  import_lang_key   VARCHAR(35) NOT NULL,
  import_table_name VARCHAR(35) NOT NULL,
  import_record_id  INTEGER     NOT NULL
);
CREATE INDEX import_details_id_idx ON ip_import_details (import_id, import_record_id);

CREATE TABLE ip_imports (
  import_id   SERIAL,
  import_date TIMESTAMP NOT NULL
);

CREATE TABLE ip_invoice_amounts (
  invoice_amount_id      SERIAL,
  invoice_id             INTEGER NOT NULL,
  invoice_item_subtotal  DECIMAL(10, 2)   DEFAULT '0.00',
  invoice_item_tax_total DECIMAL(10, 2)   DEFAULT '0.00',
  invoice_tax_total      DECIMAL(10, 2)   DEFAULT '0.00',
  invoice_total          DECIMAL(10, 2)   DEFAULT '0.00',
  invoice_paid           DECIMAL(10, 2)   DEFAULT '0.00',
  invoice_balance        DECIMAL(10, 2)   DEFAULT '0.00'
);
CREATE INDEX invoice_amounts_paid_idx ON ip_invoice_amounts (invoice_paid, invoice_balance);

CREATE TABLE ip_invoice_custom (
  invoice_custom_id SERIAL,
  invoice_id        INTEGER NOT NULL
);

CREATE TABLE ip_invoice_groups (
  invoice_group_id           SERIAL,
  invoice_group_name         VARCHAR(50) NOT NULL DEFAULT '',
  invoice_group_prefix       VARCHAR(10) NOT NULL DEFAULT '',
  invoice_group_next_id      INTEGER     NOT NULL,
  invoice_group_left_pad     SMALLINT    NOT NULL DEFAULT '0',
  invoice_group_prefix_year  SMALLINT    NOT NULL DEFAULT '0',
  invoice_group_prefix_month SMALLINT    NOT NULL DEFAULT '0'
);

CREATE TABLE ip_invoice_item_amounts (
  item_amount_id SERIAL,
  item_id        INTEGER        NOT NULL,
  item_subtotal  DECIMAL(10, 2) NOT NULL,
  item_tax_total DECIMAL(10, 2) NOT NULL,
  item_total     DECIMAL(10, 2) NOT NULL
);

CREATE TABLE ip_invoice_items (
  item_id          SERIAL,
  invoice_id       INTEGER        NOT NULL,
  item_tax_rate_id INTEGER        NOT NULL DEFAULT '0',
  item_date_added  DATE           NOT NULL,
  item_name        VARCHAR(100)   NOT NULL,
  item_description TEXT           NOT NULL,
  item_quantity    DECIMAL(10, 2) NOT NULL,
  item_price       DECIMAL(10, 2) NOT NULL,
  item_order       SMALLINT       NOT NULL DEFAULT '0'
);
CREATE INDEX invoice_items_id_idx ON ip_invoice_items (invoice_id, item_tax_rate_id, item_date_added, item_order);

CREATE TABLE ip_invoice_tax_rates (
  invoice_tax_rate_id     SERIAL,
  invoice_id              INTEGER        NOT NULL,
  tax_rate_id             INTEGER        NOT NULL,
  include_item_tax        SMALLINT       NOT NULL DEFAULT '0',
  invoice_tax_rate_amount DECIMAL(10, 2) NOT NULL
);
CREATE INDEX invoice_tax_rates_id_idx ON ip_invoice_tax_rates (invoice_id, tax_rate_id);

CREATE TABLE ip_invoices (
  invoice_id            SERIAL,
  user_id               INTEGER     NOT NULL,
  client_id             INTEGER     NOT NULL,
  invoice_group_id      INTEGER     NOT NULL,
  invoice_status_id     SMALLINT    NOT NULL DEFAULT '1',
  invoice_date_created  DATE        NOT NULL,
  invoice_date_modified TIMESTAMP   NOT NULL,
  invoice_date_due      DATE        NOT NULL,
  invoice_number        VARCHAR(20) NOT NULL,
  invoice_terms         TEXT        NOT NULL,
  invoice_url_key       CHAR(32)    NOT NULL,
  CONSTRAINT invoice_url_key UNIQUE (invoice_url_key)
);
CREATE INDEX invoices_user_id_idx ON ip_invoices (user_id, client_id, invoice_group_id, invoice_date_created, invoice_date_due, invoice_number);

CREATE TABLE ip_invoices_recurring (
  invoice_recurring_id SERIAL,
  invoice_id           INTEGER NOT NULL,
  recur_start_date     DATE,
  recur_end_date       DATE,
  recur_frequency      CHAR(2) NOT NULL,
  recur_next_date      DATE
);

CREATE TABLE ip_item_lookups (
  item_lookup_id   SERIAL,
  item_name        VARCHAR(100)   NOT NULL DEFAULT '',
  item_description TEXT           NOT NULL,
  item_price       DECIMAL(10, 2) NOT NULL
);

CREATE TABLE ip_merchant_responses (
  merchant_response_id        SERIAL,
  invoice_id                  INTEGER      NOT NULL,
  merchant_response_date      DATE         NOT NULL,
  merchant_response_driver    VARCHAR(35)  NOT NULL,
  merchant_response           VARCHAR(255) NOT NULL,
  merchant_response_reference VARCHAR(255) NOT NULL
);

CREATE TABLE ip_payment_custom (
  payment_custom_id SERIAL,
  payment_id        INTEGER NOT NULL
);

CREATE TABLE ip_payment_methods (
  payment_method_id   SERIAL,
  payment_method_name VARCHAR(35) NOT NULL
);

CREATE TABLE ip_payments (
  payment_id        SERIAL,
  invoice_id        INTEGER        NOT NULL,
  payment_method_id INTEGER        NOT NULL DEFAULT '0',
  payment_date      DATE           NOT NULL,
  payment_amount    DECIMAL(10, 2) NOT NULL,
  payment_note      TEXT           NOT NULL
);

CREATE TABLE ip_quote_amounts (
  quote_amount_id      SERIAL,
  quote_id             INTEGER        NOT NULL,
  quote_item_subtotal  DECIMAL(10, 2) NOT NULL DEFAULT '0.00',
  quote_item_tax_total DECIMAL(10, 2) NOT NULL DEFAULT '0.00',
  quote_tax_total      DECIMAL(10, 2) NOT NULL DEFAULT '0.00',
  quote_total          DECIMAL(10, 2) NOT NULL DEFAULT '0.00'
);

CREATE TABLE ip_quote_custom (
  quote_custom_id SERIAL,
  quote_id        INTEGER NOT NULL
);

CREATE TABLE ip_quote_item_amounts (
  item_amount_id SERIAL,
  item_id        INTEGER        NOT NULL,
  item_subtotal  DECIMAL(10, 2) NOT NULL DEFAULT '0.00',
  item_tax_total DECIMAL(10, 2) NOT NULL,
  item_total     DECIMAL(10, 2) NOT NULL
);

CREATE TABLE ip_quote_items (
  item_id          SERIAL,
  quote_id         INTEGER        NOT NULL,
  item_tax_rate_id INTEGER        NOT NULL,
  item_date_added  DATE           NOT NULL,
  item_name        VARCHAR(100)   NOT NULL,
  item_description TEXT           NOT NULL,
  item_quantity    DECIMAL(10, 2) NOT NULL,
  item_price       DECIMAL(10, 2) NOT NULL,
  item_order       SMALLINT       NOT NULL DEFAULT '0'
);
CREATE INDEX quote_items_id_idx ON ip_quote_items (quote_id, item_date_added, item_order);

CREATE TABLE ip_quote_tax_rates (
  quote_tax_rate_id     SERIAL,
  quote_id              INTEGER        NOT NULL,
  tax_rate_id           INTEGER        NOT NULL,
  include_item_tax      SMALLINT       NOT NULL DEFAULT '0',
  quote_tax_rate_amount DECIMAL(10, 2) NOT NULL
);

CREATE TABLE ip_quotes (
  quote_id            SERIAL,
  invoice_id          INTEGER     NOT NULL DEFAULT '0',
  user_id             INTEGER     NOT NULL,
  client_id           INTEGER     NOT NULL,
  invoice_group_id    INTEGER     NOT NULL,
  quote_status_id     SMALLINT    NOT NULL DEFAULT '1',
  quote_date_created  DATE        NOT NULL,
  quote_date_modified TIMESTAMP   NOT NULL,
  quote_date_expires  DATE        NOT NULL,
  quote_number        VARCHAR(20) NOT NULL,
  quote_url_key       CHAR(32)    NOT NULL
);
CREATE INDEX quotes_user_quote_id_idx ON ip_quotes (user_id, client_id, invoice_group_id, quote_date_created, quote_date_expires, quote_number);

CREATE TABLE ip_settings (
  setting_id    SERIAL,
  setting_key   VARCHAR(50) NOT NULL,
  setting_value TEXT        NOT NULL
);

CREATE TABLE ip_tax_rates (
  tax_rate_id      SERIAL,
  tax_rate_name    VARCHAR(25)   NOT NULL,
  tax_rate_percent DECIMAL(5, 2) NOT NULL
);

CREATE TABLE ip_user_clients (
  user_client_id SERIAL,
  user_id        INTEGER NOT NULL,
  client_id      INTEGER NOT NULL
);
CREATE INDEX user_clients_user_id ON ip_user_clients (user_id, client_id);

CREATE TABLE ip_user_custom (
  user_custom_id SERIAL,
  user_id        INTEGER NOT NULL
);

CREATE TABLE ip_users (
  user_id            SERIAL,
  user_type          SMALLINT     NOT NULL DEFAULT '0',
  user_date_created  TIMESTAMP    NOT NULL,
  user_date_modified TIMESTAMP    NOT NULL,
  user_name          VARCHAR(100)          DEFAULT '',
  user_company       VARCHAR(100)          DEFAULT '',
  user_address_1     VARCHAR(100)          DEFAULT '',
  user_address_2     VARCHAR(100)          DEFAULT '',
  user_city          VARCHAR(45)           DEFAULT '',
  user_state         VARCHAR(35)           DEFAULT '',
  user_zip           VARCHAR(15)           DEFAULT '',
  user_country       VARCHAR(35)           DEFAULT '',
  user_phone         VARCHAR(20)           DEFAULT '',
  user_fax           VARCHAR(20)           DEFAULT '',
  user_mobile        VARCHAR(20)           DEFAULT '',
  user_email         VARCHAR(100) NOT NULL,
  user_password      VARCHAR(60)  NOT NULL,
  user_web           VARCHAR(100)          DEFAULT '',
  user_psalt         CHAR(22)     NOT NULL
);

CREATE TABLE ip_versions (
  version_id           SERIAL,
  version_date_applied VARCHAR(14) NOT NULL,
  version_file         VARCHAR(45) NOT NULL,
  version_sql_errors   SMALLINT    NOT NULL
);