-- Schema changes InvoicePlane applies through Mdl_setup's PHP upgrade_*()
-- methods rather than the setup/*.sql migrations. A test database built from
-- the SQL migrations alone is missing them, which is why the ip_*_custom
-- models (which query *_custom_fieldid / *_custom_fieldvalue) and the
-- user_all_clients lookups fail with "Unknown column".
--
-- Keep this in sync with:
--   application/modules/setup/models/Mdl_setup.php
--     - upgrade_023_1_5_0()  (row-based custom fields)
--     - upgrade_029_1_5_6()  (user_all_clients)
--
-- The tables are recreated (not ALTERed) because the SQL migrations create the
-- pre-1.5 column-per-field shape; a fresh test DB has no custom-field data to
-- migrate, so we can jump straight to the final row-based shape.

DROP TABLE IF EXISTS `ip_client_custom`;
CREATE TABLE `ip_client_custom` (
  `client_custom_id`         INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `client_id`                INT NOT NULL,
  `client_custom_fieldid`    INT NOT NULL,
  `client_custom_fieldvalue` TEXT NULL,
  UNIQUE (`client_id`, `client_custom_fieldid`)
);

DROP TABLE IF EXISTS `ip_invoice_custom`;
CREATE TABLE `ip_invoice_custom` (
  `invoice_custom_id`         INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `invoice_id`                INT NOT NULL,
  `invoice_custom_fieldid`    INT NOT NULL,
  `invoice_custom_fieldvalue` TEXT NULL,
  UNIQUE (`invoice_id`, `invoice_custom_fieldid`)
);

DROP TABLE IF EXISTS `ip_quote_custom`;
CREATE TABLE `ip_quote_custom` (
  `quote_custom_id`         INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `quote_id`                INT NOT NULL,
  `quote_custom_fieldid`    INT NOT NULL,
  `quote_custom_fieldvalue` TEXT NULL,
  UNIQUE (`quote_id`, `quote_custom_fieldid`)
);

DROP TABLE IF EXISTS `ip_payment_custom`;
CREATE TABLE `ip_payment_custom` (
  `payment_custom_id`         INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `payment_id`                INT NOT NULL,
  `payment_custom_fieldid`    INT NOT NULL,
  `payment_custom_fieldvalue` TEXT NULL,
  UNIQUE (`payment_id`, `payment_custom_fieldid`)
);

DROP TABLE IF EXISTS `ip_user_custom`;
CREATE TABLE `ip_user_custom` (
  `user_custom_id`         INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `user_id`                INT NOT NULL,
  `user_custom_fieldid`    INT NOT NULL,
  `user_custom_fieldvalue` TEXT NULL,
  UNIQUE (`user_id`, `user_custom_fieldid`)
);

ALTER TABLE `ip_custom_fields` DROP COLUMN `custom_field_column`;

ALTER TABLE `ip_users` ADD COLUMN `user_all_clients` INT(1) NOT NULL DEFAULT 0;
