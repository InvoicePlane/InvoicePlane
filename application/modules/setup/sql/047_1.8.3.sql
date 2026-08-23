-- 1.8.3 — E-invoicing synchronization operations ledger

CREATE TABLE IF NOT EXISTS `ip_integration_sync_runs` (
  `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `merchant_client_id` INT NOT NULL,
  `correlation_id`     CHAR(32) NOT NULL,
  `trigger_type`       VARCHAR(20) NOT NULL,
  `sync_scope`         VARCHAR(20) NOT NULL,
  `status`             VARCHAR(20) NOT NULL,
  `attempt_count`      SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `incoming_received`  INT UNSIGNED NOT NULL DEFAULT 0,
  `incoming_archived`  INT UNSIGNED NOT NULL DEFAULT 0,
  `incoming_skipped`   INT UNSIGNED NOT NULL DEFAULT 0,
  `incoming_failed`    INT UNSIGNED NOT NULL DEFAULT 0,
  `events_received`    INT UNSIGNED NOT NULL DEFAULT 0,
  `events_created`     INT UNSIGNED NOT NULL DEFAULT 0,
  `events_skipped`     INT UNSIGNED NOT NULL DEFAULT 0,
  `error_summary`      VARCHAR(2000) NULL,
  `duration_ms`        INT UNSIGNED NULL,
  `started_at`         DATETIME NOT NULL,
  `finished_at`        DATETIME NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_sync_correlation_id` (`correlation_id`),
  KEY `idx_sync_client_started` (`merchant_client_id`, `started_at`),
  KEY `idx_sync_status_started` (`status`, `started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
