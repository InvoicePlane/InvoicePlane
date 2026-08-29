-- 1.8.7 — Indexed dedup key for incoming invoice events.
--
-- Merchant_responses_model::create_event_item() used to detect duplicate
-- events by comparing the full raw_payload text, which cannot be indexed
-- for equality and produces a false duplicate whenever the same event is
-- redelivered with its JSON keys in a different order. event_hash is a
-- stable SHA-256 digest of the sanitized payload, computed once and
-- compared/indexed instead.

ALTER TABLE `ip_merchant_responses`
  ADD COLUMN `event_hash` CHAR(64) NULL
    COMMENT 'SHA-256 of raw_payload — dedup key for incoming invoice events'
    AFTER `raw_payload`,
  ADD INDEX `idx_event_dedup` (`merchant_client_id`, `record_type`, `event_hash`);
