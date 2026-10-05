-- External RFC 3161 timestamp evidence for archive packages.
ALTER TABLE `ip_archive_documents`
  ADD COLUMN `external_timestamp_provider` VARCHAR(100) NULL AFTER `external_retention_until`,
  ADD COLUMN `external_timestamp_subject` VARCHAR(500) NULL AFTER `external_timestamp_provider`,
  ADD COLUMN `external_timestamp_token_path` VARCHAR(500) NULL AFTER `external_timestamp_subject`,
  ADD COLUMN `external_timestamp_token_sha256` CHAR(64) NULL AFTER `external_timestamp_token_path`,
  ADD COLUMN `external_timestamp_status` VARCHAR(20) NULL AFTER `external_timestamp_token_sha256`,
  ADD COLUMN `external_timestamped_at` DATETIME NULL AFTER `external_timestamp_status`,
  ADD KEY `idx_archive_document_external_timestamp` (`external_timestamp_provider`, `external_timestamp_status`);
