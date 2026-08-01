-- 1.8.1 — Archived incoming e-invoice documents

ALTER TABLE `ip_merchant_responses`
  ADD COLUMN `document_path` VARCHAR(500) NULL
    COMMENT 'Path relative to uploads/archive for a validated incoming document'
    AFTER `raw_payload`,
  ADD COLUMN `document_name` VARCHAR(255) NULL
    COMMENT 'Sanitized original provider filename'
    AFTER `document_path`,
  ADD COLUMN `document_mime_type` VARCHAR(100) NULL
    AFTER `document_name`,
  ADD COLUMN `document_size` INT UNSIGNED NULL
    AFTER `document_mime_type`,
  ADD COLUMN `document_sha256` CHAR(64) NULL
    AFTER `document_size`,
  ADD COLUMN `document_profile` VARCHAR(100) NULL
    COMMENT 'Validated EInvoiceProfileRegistry code'
    AFTER `document_sha256`,
  ADD COLUMN `document_validation_status` VARCHAR(20) NULL
    COMMENT 'valid or failed'
    AFTER `document_profile`,
  ADD COLUMN `document_validation_error` VARCHAR(1000) NULL
    AFTER `document_validation_status`,
  ADD INDEX `idx_incoming_document_sha256` (`document_sha256`);
