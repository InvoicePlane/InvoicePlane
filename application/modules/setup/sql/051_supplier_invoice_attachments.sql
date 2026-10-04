-- Additional documents attached to supplier invoices.

CREATE TABLE IF NOT EXISTS `ip_supplier_invoice_attachments` (
  `supplier_invoice_attachment_id` INT(11) NOT NULL AUTO_INCREMENT,
  `supplier_invoice_id` INT(11) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `storage_path` VARCHAR(500) NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `file_size` BIGINT(20) NOT NULL,
  `sha256` CHAR(64) NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`supplier_invoice_attachment_id`),
  UNIQUE KEY `uq_supplier_invoice_attachment_hash` (`supplier_invoice_id`, `sha256`),
  KEY `idx_supplier_invoice_attachment_invoice` (`supplier_invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
