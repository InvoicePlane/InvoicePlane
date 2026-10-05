-- Verification state for archive sealing and document integrity checks.
ALTER TABLE `ip_archive_documents`
  ADD COLUMN `integrity_status` VARCHAR(20) NULL AFTER `sha256`,
  ADD COLUMN `integrity_verified_at` DATETIME NULL AFTER `integrity_status`,
  ADD COLUMN `integrity_error` VARCHAR(1000) NULL AFTER `integrity_verified_at`,
  ADD KEY `idx_archive_document_integrity` (`integrity_status`, `sealed_at`);
