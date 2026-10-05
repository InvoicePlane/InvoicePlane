-- Remote SAE/Object Lock tracking for archive packages.
ALTER TABLE `ip_archive_documents`
  ADD COLUMN `external_storage_provider` VARCHAR(50) NULL AFTER `encryption_key_version`,
  ADD COLUMN `external_storage_key` VARCHAR(500) NULL AFTER `external_storage_provider`,
  ADD COLUMN `external_storage_version_id` VARCHAR(255) NULL AFTER `external_storage_key`,
  ADD COLUMN `external_storage_status` VARCHAR(20) NULL AFTER `external_storage_version_id`,
  ADD COLUMN `external_retention_until` DATETIME NULL AFTER `external_storage_status`,
  ADD KEY `idx_archive_document_external_storage` (`external_storage_provider`, `external_storage_status`);
