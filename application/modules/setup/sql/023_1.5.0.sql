# Module "units"
CREATE TABLE IF NOT EXISTS `ip_units` (
  `unit_id`   INT(11) NOT NULL AUTO_INCREMENT,
  `unit_name` VARCHAR(50)      DEFAULT NULL,
  `unit_name_plrl` VARCHAR(50)  DEFAULT NULL,
  PRIMARY KEY (`unit_id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

ALTER TABLE ip_products
  ADD COLUMN unit_id INT(11);

ALTER TABLE ip_quote_items
  ADD COLUMN item_product_unit VARCHAR(50) DEFAULT NULL;

ALTER TABLE ip_quote_items
  ADD COLUMN item_product_unit_id INT(11);

ALTER TABLE ip_invoice_items
  ADD COLUMN item_product_unit VARCHAR(50) DEFAULT NULL;

ALTER TABLE ip_invoice_items
  ADD COLUMN item_product_unit_id INT(11);

# Custom Field Enhancements
CREATE TABLE `ip_custom_values` (
  `custom_values_id` INT(11) NOT NULL AUTO_INCREMENT,
  `custom_values_field` INT(11) NOT NULL,
  `custom_values_value` TEXT NOT NULL,
  PRIMARY KEY (`custom_values_id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8;

ALTER TABLE `ip_custom_fields`
  ADD `custom_field_type` VARCHAR(255) NOT NULL AFTER `custom_field_label`;

# Sumex changes
CREATE TABLE `ip_invoice_sumex` (
  `sumex_id` int(11) NOT NULL,
  `sumex_invoice` int(11) NOT NULL,
  `sumex_reason` int(11) NOT NULL,
  `sumex_diagnosis` varchar(500) NOT NULL,
  `sumex_observations` varchar(500) NOT NULL,
  `sumex_treatmentstart` date NOT NULL,
  `sumex_treatmentend` date NOT NULL,
  `sumex_casedate` date NOT NULL,
  `sumex_casenumber` varchar(35) DEFAULT NULL
) ENGINE = MyISAM DEFAULT CHARSET = utf8;


ALTER TABLE `ip_invoice_sumex`
  ADD PRIMARY KEY (`sumex_id`);


ALTER TABLE `ip_invoice_sumex`
  MODIFY `sumex_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `ip_clients`
  ADD COLUMN client_surname VARCHAR(255) DEFAULT NULL;

ALTER TABLE `ip_clients`
  ADD COLUMN client_avs VARCHAR(16) DEFAULT NULL;

ALTER TABLE `ip_clients`
  ADD COLUMN client_insurednumber VARCHAR(30) DEFAULT NULL;

ALTER TABLE `ip_clients`
  ADD COLUMN client_veka VARCHAR(30) DEFAULT NULL;

ALTER TABLE `ip_clients`
  ADD COLUMN client_birthdate DATE DEFAULT NULL;

ALTER TABLE `ip_clients`
  ADD COLUMN client_gender INT(1) DEFAULT 0;

ALTER TABLE `ip_invoice_items`
  ADD COLUMN item_date date;

INSERT INTO `ip_settings` (setting_key, setting_value) VALUES
    ('sumex', '0'),
    ('sumex_sliptype', '1'),
    ('sumex_canton', '0');

ALTER TABLE `ip_users`
  ADD COLUMN user_subscribernumber VARCHAR(40) DEFAULT NULL;

ALTER TABLE `ip_users`
  ADD COLUMN user_iban VARCHAR(34) DEFAULT NULL;

ALTER TABLE `ip_users`
  ADD COLUMN user_gln BIGINT(13) DEFAULT NULL;

ALTER TABLE `ip_users`
  ADD COLUMN user_rcc VARCHAR(7) DEFAULT NULL;

ALTER TABLE `ip_products`
  ADD COLUMN product_tariff INT(11) DEFAULT NULL;

# End Sumex Changes

# Delete and re-add the ip_sessions table for CI 3
DROP TABLE `ip_sessions`;
CREATE TABLE IF NOT EXISTS `ip_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
  `data` blob NOT NULL,
  KEY `ip_sessions_timestamp` (`timestamp`)
);

# IP-491 - Localization per client and user
ALTER TABLE `ip_users`
  ADD `user_language` VARCHAR(255) DEFAULT 'system' AFTER `user_date_modified`;

ALTER TABLE `ip_clients`
  ADD `client_language` VARCHAR(255) DEFAULT 'system' AFTER `client_tax_code`;

# Insert default theme into database (IP-338)
INSERT INTO `ip_settings` (setting_key, setting_value)
  VALUES ('system_theme', 'invoiceplane');

# Feature IP-162
# Allow invoice item to refer to a table `products` or to table `tasks`
ALTER TABLE `ip_invoice_items`
  ADD COLUMN `item_task_id` int(11) DEFAULT NULL
  AFTER `item_date_added`;

# Add default hourly rate for tasks
INSERT INTO `ip_settings` (`setting_key`, `setting_value`)
  VALUES ('default_hourly_rate', '0.00');
INSERT INTO `ip_settings` (`setting_key`, `setting_value`)
  VALUES ('projects_enabled', '1');

# Add tax rate to tasks
ALTER TABLE `ip_tasks`
  ADD COLUMN `tax_rate_id` int(11) NOT NULL
  AFTER `task_status`;

# DANGER ZONE (Data IS going to be lost!)
# =======================================

# Switch to row based custom fields (instead of columns)
# It would be nice to find a way to copy the old data into the new table

DROP TABLE `ip_client_custom`;
CREATE TABLE `ip_client_custom`
(
  `client_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
  `client_id` INT NOT NULL, `client_custom_fieldid` INT NOT NULL,
  `client_custom_fieldvalue` VARCHAR(65535) NULL ,
  UNIQUE (client_id, client_custom_fieldid)
);

ALTER TABLE `ip_custom_fields` DROP COLUMN `custom_field_column`;
ALTER TABLE `ip_custom_fields` ADD COLUMN `custom_field_location` INT;
ALTER TABLE `ip_custom_fields` ADD COLUMN `custom_field_order` INT;

# Same for invoice
DROP TABLE `ip_invoice_custom`;
CREATE TABLE `ip_invoice_custom`
(
  `invoice_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
  `invoice_id` INT NOT NULL, `invoice_custom_fieldid` INT NOT NULL,
  `invoice_custom_fieldvalue` VARCHAR(65535) NULL ,
  UNIQUE (invoice_id, invoice_custom_fieldid)
);

# Quotes
DROP TABLE `ip_quote_custom`;
CREATE TABLE `ip_quote_custom`
(
  `quote_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
  `quote_id` INT NOT NULL, `quote_custom_fieldid` INT NOT NULL,
  `quote_custom_fieldvalue` VARCHAR(65535) NULL ,
  UNIQUE (quote_id, quote_custom_fieldid)
);

# Payments
DROP TABLE `ip_payment_custom`;
CREATE TABLE `ip_payment_custom`
(
  `payment_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
  `payment_id` INT NOT NULL, `payment_custom_fieldid` INT NOT NULL,
  `payment_custom_fieldvalue` VARCHAR(65535) NULL ,
  UNIQUE (payment_id, payment_custom_fieldid)
);

# Users
DROP TABLE `ip_user_custom`;
CREATE TABLE `ip_user_custom`
(
  `user_custom_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT ,
  `user_id` INT NOT NULL, `user_custom_fieldid` INT NOT NULL,
  `user_custom_fieldvalue` VARCHAR(65535) NULL ,
  UNIQUE (user_id, user_custom_fieldid)
);

# Custom Fields
ALTER TABLE ip_custom_fields MODIFY custom_field_table VARCHAR(50);
ALTER TABLE ip_custom_fields MODIFY custom_field_label VARCHAR(50);
ALTER TABLE ip_custom_fields ADD CONSTRAINT UNIQUE(custom_field_table, custom_field_label);
