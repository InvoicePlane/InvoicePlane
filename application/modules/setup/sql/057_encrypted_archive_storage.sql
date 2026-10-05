-- Encryption metadata for documents stored through the archive adapter.
ALTER TABLE `ip_archive_documents`
  ADD COLUMN `storage_sha256` CHAR(64) NULL AFTER `sha256`,
  ADD COLUMN `encryption_status` VARCHAR(20) NULL AFTER `storage_sha256`,
  ADD COLUMN `encryption_key_version` VARCHAR(20) NULL AFTER `encryption_status`,
  ADD KEY `idx_archive_document_encryption` (`encryption_status`);
