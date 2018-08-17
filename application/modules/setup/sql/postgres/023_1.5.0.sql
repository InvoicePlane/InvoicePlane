# Module "units"
CREATE TABLE IF NOT EXISTS `ip_units` (
  `unit_id`        INT(11) NOT NULL AUTO_INCREMENT,
  `unit_name`      VARCHAR(50)      DEFAULT NULL,
  `unit_name_plrl` VARCHAR(50)      DEFAULT NULL,
  PRIMARY KEY (`unit_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

ALTER TABLE ip_products
  ADD COLUMN unit_id INT(11);

ALTER TABLE ip_quote_items
  ADD COLUMN item_product_unit VARCHAR(50) DEFAULT NULL,
  ADD COLUMN item_product_unit_id INT(11);

ALTER TABLE ip_invoice_items
  ADD COLUMN item_product_unit VARCHAR(50) DEFAULT NULL,
  ADD COLUMN item_product_unit_id INT(11);

# Custom Field Enhancements
CREATE TABLE `ip_custom_values` (
  `custom_values_id`    INT(11) NOT NULL AUTO_INCREMENT,
  `custom_values_field` INT(11) NOT NULL,
  `custom_values_value` TEXT    NOT NULL,
  PRIMARY KEY (`custom_values_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

ALTER TABLE `ip_custom_fields`
  ADD `custom_field_type` VARCHAR(255) DEFAULT 'TEXT' NOT NULL
  AFTER `custom_field_label`;

# Sumex changes
CREATE TABLE `ip_invoice_sumex` (
  `sumex_id`             INT(11)      NOT NULL,
  `sumex_invoice`        INT(11)      NOT NULL,
  `sumex_reason`         INT(11)      NOT NULL,
  `sumex_diagnosis`      VARCHAR(500) NOT NULL,
  `sumex_observations`   VARCHAR(500) NOT NULL,
  `sumex_treatmentstart` DATE         NOT NULL,
  `sumex_treatmentend`   DATE         NOT NULL,
  `sumex_casedate`       DATE         NOT NULL,
  `sumex_casenumber`     VARCHAR(35) DEFAULT NULL
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

ALTER TABLE `ip_invoice_sumex`
  ADD PRIMARY KEY (`sumex_id`),
  MODIFY `sumex_id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ip_clients`
  ADD COLUMN client_surname VARCHAR(255) DEFAULT NULL,
  ADD COLUMN client_avs VARCHAR(16) DEFAULT NULL;

ALTER TABLE `ip_clients`
  ADD COLUMN client_insurednumber VARCHAR(30) DEFAULT NULL,
  ADD COLUMN client_veka VARCHAR(30) DEFAULT NULL,
  ADD COLUMN client_birthdate DATE DEFAULT NULL,
  ADD COLUMN client_gender INT(1) DEFAULT 0;

ALTER TABLE `ip_invoice_items`
  ADD COLUMN item_date DATE;

INSERT INTO `ip_settings` (setting_key, setting_value)
VALUES
  ('sumex', '0'),
  ('sumex_sliptype', '1'),
  ('sumex_canton', '0');

ALTER TABLE `ip_users`
  ADD COLUMN user_subscribernumber VARCHAR(40) DEFAULT NULL,
  ADD COLUMN user_iban VARCHAR(34) DEFAULT NULL,
  ADD COLUMN user_gln BIGINT(13) DEFAULT NULL,
  ADD COLUMN user_rcc VARCHAR(7) DEFAULT NULL;

ALTER TABLE `ip_products`
  ADD COLUMN product_tariff INT(11) DEFAULT NULL;

# End Sumex Changes

# Delete and re-add the ip_sessions table for CI 3
DROP TABLE `ip_sessions`;
CREATE TABLE IF NOT EXISTS `ip_sessions` (
  `id`         VARCHAR(128)               NOT NULL,
  `ip_address` VARCHAR(45)                NOT NULL,
  `timestamp`  INT(10) UNSIGNED DEFAULT 0 NOT NULL,
  `data`       BLOB                       NOT NULL,
  KEY `ip_sessions_timestamp` (`timestamp`)
);

# IP-491 - Localization per client and user
ALTER TABLE `ip_users`
  ADD `user_language` VARCHAR(255) DEFAULT 'system'
  AFTER `user_date_modified`;

ALTER TABLE `ip_clients`
  ADD `client_language` VARCHAR(255) DEFAULT 'system'
  AFTER `client_tax_code`;

# Insert default theme into database (IP-338)
INSERT INTO `ip_settings` (setting_key, setting_value)
VALUES ('system_theme', 'invoiceplane');

# Feature IP-162
# Allow invoice item to refer to a table `products` or to table `tasks`
ALTER TABLE `ip_invoice_items`
  ADD COLUMN `item_task_id` INT(11) DEFAULT NULL
  AFTER `item_date_added`;

# Add default hourly rate for tasks
INSERT INTO `ip_settings` (`setting_key`, `setting_value`)
VALUES ('default_hourly_rate', '0.00');
INSERT INTO `ip_settings` (`setting_key`, `setting_value`)
VALUES ('projects_enabled', '1');

# Add tax rate to tasks
ALTER TABLE `ip_tasks`
  ADD COLUMN `tax_rate_id` INT(11) NOT NULL
  AFTER `task_status`;

# Switch to row based custom fields (instead of columns)
# (See Mdl_setup for the migration script)

# Custom Fields
ALTER TABLE ip_custom_fields
  MODIFY custom_field_table VARCHAR(50),
  MODIFY custom_field_label VARCHAR(50),
  ADD custom_field_location INT(11) DEFAULT 0,
  ADD custom_field_order INT(11) DEFAULT 999,
  ADD CONSTRAINT UNIQUE (custom_field_table, custom_field_label);
