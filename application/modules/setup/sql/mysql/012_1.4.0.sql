# Discounts
ALTER TABLE `ip_quotes`
  ADD COLUMN `quote_discount_amount` DECIMAL(20, 2) NOT NULL DEFAULT 0
  AFTER `quote_number`,
  ADD COLUMN `quote_discount_percent` DECIMAL(20, 2) NOT NULL DEFAULT 0
  AFTER `quote_discount_amount`;

ALTER TABLE `ip_quote_item_amounts`
  ADD COLUMN `item_discount` DECIMAL(20, 2) NOT NULL DEFAULT 0
  AFTER `item_tax_total`;
ALTER TABLE `ip_quote_items`
  ADD COLUMN `item_discount_amount` DECIMAL(20, 2) NOT NULL DEFAULT 0
  AFTER `item_price`;

ALTER TABLE `ip_invoices`
  ADD COLUMN `invoice_discount_amount` DECIMAL(20, 2) NOT NULL DEFAULT 0
  AFTER `invoice_number`,
  ADD COLUMN `invoice_discount_percent` DECIMAL(20, 2) NOT NULL DEFAULT 0
  AFTER `invoice_discount_amount`;

ALTER TABLE `ip_invoice_item_amounts`
  ADD COLUMN `item_discount` DECIMAL(20, 2) NOT NULL DEFAULT 0
  AFTER `item_tax_total`;
ALTER TABLE `ip_invoice_items`
  ADD COLUMN `item_discount_amount` DECIMAL(20, 2) NOT NULL DEFAULT 0
  AFTER `item_price`;

# Feature IP-162
# For module "projects" 
CREATE TABLE `ip_projects` (
  `project_id`   INT(11)      NOT NULL AUTO_INCREMENT,
  `client_id`    INT(11)      NOT NULL,
  `project_name` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`project_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

# For module "tasks"
CREATE TABLE `ip_tasks` (
  `task_id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `project_id`       INT(11)      NOT NULL,
  `task_name`        VARCHAR(50)  NOT NULL,
  `task_description` LONGTEXT     NOT NULL,
  `task_price`       FLOAT(10, 2) NOT NULL,
  `task_finish_date` DATE         NOT NULL,
  `task_status`      TINYINT(1)   NOT NULL,
  PRIMARY KEY (`task_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

# For module "upload" IP-211
CREATE TABLE `ip_uploads` (
  `upload_id`          INT(11)  NOT NULL AUTO_INCREMENT,
  `client_id`          INT(11)  NOT NULL,
  `url_key`            CHAR(32) NOT NULL,
  `file_name_original` LONGTEXT NOT NULL,
  `file_name_new`      LONGTEXT NOT NULL,
  `uploaded_date`      DATE     NOT NULL,
  PRIMARY KEY (`upload_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

# Attach pdf on emails setting
INSERT INTO `ip_settings` (`setting_key`, `setting_value`)
VALUES ('email_pdf_attachment', '1');

# IP-283 - Allow larger item amounts
ALTER TABLE ip_invoice_item_amounts
  MODIFY COLUMN item_subtotal DECIMAL(20, 2);
ALTER TABLE ip_invoice_item_amounts
  MODIFY COLUMN item_tax_total DECIMAL(20, 2);
ALTER TABLE ip_invoice_item_amounts
  MODIFY COLUMN item_total DECIMAL(20, 2);
ALTER TABLE ip_quote_item_amounts
  MODIFY COLUMN item_subtotal DECIMAL(20, 2);
ALTER TABLE ip_quote_item_amounts
  MODIFY COLUMN item_tax_total DECIMAL(20, 2);
ALTER TABLE ip_quote_item_amounts
  MODIFY COLUMN item_total DECIMAL(20, 2);
