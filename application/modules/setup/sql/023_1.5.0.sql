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
  VALUES ('system_theme', 'invoiceplane_default');
