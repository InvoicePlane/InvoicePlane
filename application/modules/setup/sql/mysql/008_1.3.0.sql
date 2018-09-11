# Solves IP-195
ALTER TABLE `ip_quotes`
  ADD COLUMN `notes` LONGTEXT;

# Solves IP-216
ALTER TABLE `ip_invoices`
  ADD COLUMN `invoice_time_created` TIME NOT NULL DEFAULT '00:00:00'
  AFTER `invoice_date_created`,
  ADD COLUMN `invoice_password` VARCHAR(90) NULL
  AFTER `is_read_only`;

# Solves IP-196
ALTER TABLE `ip_invoices`
  ADD COLUMN `payment_method` INT NOT NULL DEFAULT 0
  AFTER `invoice_url_key`;

# Solves IP-213
INSERT INTO `ip_settings` (`setting_key`, `setting_value`)
VALUES ('invoice_pre_password', '');
INSERT INTO `ip_settings` (`setting_key`, `setting_value`)
VALUES ('quote_pre_password', '');
ALTER TABLE `ip_quotes`
  ADD COLUMN `quote_password` VARCHAR(90) NULL
  AFTER `quote_url_key`;

# Solves IP-227
ALTER TABLE `ip_invoices`
  CHANGE `invoice_number` `invoice_number` VARCHAR(100) NOT NULL;
ALTER TABLE `ip_quotes`
  CHANGE `quote_number` `quote_number` VARCHAR(100) NOT NULL;