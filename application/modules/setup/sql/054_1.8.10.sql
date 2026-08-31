-- 1.8.10 — Repair the MariaDB-compatible incoming event hash migration.
-- 053_1.8.9.sql may already be marked as applied after its index statement
-- failed, so repeat the idempotent checks in a new migration.

ALTER TABLE `ip_merchant_responses`
  ADD COLUMN IF NOT EXISTS `event_hash` CHAR(64) NULL
    COMMENT 'SHA-256 of raw_payload — dedup key for incoming invoice events'
    AFTER `raw_payload`;

CREATE INDEX IF NOT EXISTS `idx_event_dedup`
  ON `ip_merchant_responses` (`merchant_client_id`, `record_type`, `event_hash`);
