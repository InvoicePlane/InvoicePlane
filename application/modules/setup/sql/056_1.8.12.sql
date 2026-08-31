-- 1.8.12 — Finish the event hash repair with short prepared statement names.
-- Some MariaDB/driver combinations truncate prepared statement identifiers.

SET @ehc = (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ip_merchant_responses'
    AND COLUMN_NAME = 'event_hash'
);

SET @ehs = IF(
  @ehc = 0,
  'ALTER TABLE `ip_merchant_responses` ADD COLUMN `event_hash` CHAR(64) NULL AFTER `raw_payload`',
  'SELECT 1'
);

PREPARE eh FROM @ehs;
EXECUTE eh;
DEALLOCATE PREPARE eh;

SET @ehi = (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ip_merchant_responses'
    AND INDEX_NAME = 'idx_event_dedup'
);

SET @eht = IF(
  @ehi = 0,
  'CREATE INDEX `idx_event_dedup` ON `ip_merchant_responses` (`merchant_client_id`, `record_type`, `event_hash`)',
  'SELECT 1'
);

PREPARE ei FROM @eht;
EXECUTE ei;
DEALLOCATE PREPARE ei;
