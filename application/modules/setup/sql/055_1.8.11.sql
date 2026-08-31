-- 1.8.11 — MariaDB-compatible repair for the incoming event hash schema.
-- Use information_schema checks because some supported MariaDB versions do not
-- accept IF NOT EXISTS in ALTER TABLE or CREATE INDEX statements.

SET @event_hash_exists = (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ip_merchant_responses'
    AND COLUMN_NAME = 'event_hash'
);

SET @event_hash_sql = IF(
  @event_hash_exists = 0,
  'ALTER TABLE `ip_merchant_responses` ADD COLUMN `event_hash` CHAR(64) NULL COMMENT ''SHA-256 of raw_payload — dedup key for incoming invoice events'' AFTER `raw_payload`',
  'SELECT 1'
);

PREPARE event_hash_statement FROM @event_hash_sql;
EXECUTE event_hash_statement;
DEALLOCATE PREPARE event_hash_statement;

SET @event_hash_index_exists = (
  SELECT COUNT(*)
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'ip_merchant_responses'
    AND INDEX_NAME = 'idx_event_dedup'
);

SET @event_hash_index_sql = IF(
  @event_hash_index_exists = 0,
  'CREATE INDEX `idx_event_dedup` ON `ip_merchant_responses` (`merchant_client_id`, `record_type`, `event_hash`)',
  'SELECT 1'
);

PREPARE event_hash_index_statement FROM @event_hash_index_sql;
EXECUTE event_hash_index_statement;
DEALLOCATE PREPARE event_hash_index_statement;
