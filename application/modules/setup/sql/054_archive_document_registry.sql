-- Document registry for probative archive preparation.
-- The registry stores immutable document metadata; physical immutability and
-- long-term retention storage are handled by a later storage adapter.

CREATE TABLE IF NOT EXISTS `ip_archive_documents` (
  `archive_document_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `document_type` VARCHAR(50) NOT NULL,
  `source_module` VARCHAR(100) NOT NULL,
  `source_reference` VARCHAR(191) NOT NULL,
  `storage_path` VARCHAR(500) NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `file_size` BIGINT(20) UNSIGNED NOT NULL,
  `sha256` CHAR(64) NOT NULL,
  `document_profile` VARCHAR(100) NULL,
  `validation_status` VARCHAR(20) NULL,
  `validation_error` VARCHAR(1000) NULL,
  `received_at` DATETIME NULL,
  `archived_at` DATETIME NOT NULL,
  `retention_until` DATE NULL,
  `legal_hold` TINYINT(1) NOT NULL DEFAULT 0,
  `sealed_at` DATETIME NULL,
  `created_by` INT(11) NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY (`archive_document_id`),
  UNIQUE KEY `uq_archive_document_source` (`source_module`, `source_reference`),
  KEY `idx_archive_document_sha256` (`sha256`),
  KEY `idx_archive_document_retention` (`retention_until`, `legal_hold`),
  KEY `idx_archive_document_type` (`document_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
