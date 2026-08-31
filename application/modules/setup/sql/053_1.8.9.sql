-- 1.8.9 — Ensure the incoming event deduplication column exists.
-- Some installations already recorded 051_1.8.7.sql before event_hash was
-- added to that migration, so this corrective migration is idempotent.

ALTER TABLE `ip_merchant_responses`
  ADD COLUMN IF NOT EXISTS `event_hash` CHAR(64) NULL
    COMMENT 'SHA-256 of raw_payload — dedup key for incoming invoice events'
    AFTER `raw_payload`;

CREATE INDEX IF NOT EXISTS `idx_event_dedup`
  ON `ip_merchant_responses` (`merchant_client_id`, `record_type`, `event_hash`);
