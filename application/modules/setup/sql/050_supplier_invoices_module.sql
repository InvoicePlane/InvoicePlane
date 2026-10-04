-- Supplier invoice module: payments and status history.
-- Core supplier and invoice tables are created by 049_supplier_accounting.sql.

ALTER TABLE `ip_supplier_invoices`
  MODIFY `incoming_response_id` INT(11) NULL;

CREATE TABLE IF NOT EXISTS `ip_supplier_invoice_payments` (
  `supplier_invoice_payment_id` INT(11) NOT NULL AUTO_INCREMENT,
  `supplier_invoice_id` INT(11) NOT NULL,
  `payment_date` DATE NOT NULL,
  `amount` DECIMAL(20, 6) NOT NULL,
  `currency_code` CHAR(3) NOT NULL DEFAULT 'EUR',
  `payment_method` VARCHAR(100) NULL,
  `reference` VARCHAR(255) NULL,
  `notes` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`supplier_invoice_payment_id`),
  KEY `idx_supplier_invoice_payment_invoice` (`supplier_invoice_id`),
  KEY `idx_supplier_invoice_payment_date` (`payment_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `ip_supplier_invoice_status_history` (
  `supplier_invoice_status_history_id` INT(11) NOT NULL AUTO_INCREMENT,
  `supplier_invoice_id` INT(11) NOT NULL,
  `old_status` VARCHAR(30) NULL,
  `new_status` VARCHAR(30) NOT NULL,
  `comment` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`supplier_invoice_status_history_id`),
  KEY `idx_supplier_invoice_status_history_invoice` (`supplier_invoice_id`),
  KEY `idx_supplier_invoice_status_history_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
