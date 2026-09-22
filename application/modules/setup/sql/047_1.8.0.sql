-- =====================================================
-- 1.8.0 — Integrations, e-invoicing, Peppol and services schema
--
-- Consolidated migration. Supersedes the incremental 1.8.0 – 1.8.12 files:
-- the integration-module schema plus the event_hash / idx_event_dedup repair
-- cascade (1.8.7 – 1.8.12) that only ever re-added the same column and index.
--
-- Net effect:
--   * ip_merchant_clients            — new: integration account registry
--   * ip_merchant_responses          — extended: unified provider response log
--                                      (payment gateways + e-invoice + Peppol),
--                                      incoming-document archive metadata,
--                                      event_hash dedup key; invoice_id nullable
--   * ip_einvoice_responses          — dropped (folded into ip_merchant_responses)
--   * ip_integration_sync_runs       — new: sync operations ledger
--   * ip_services / ip_client_services — new: service catalogue + assignments
--   * ip_clients.client_peppol_id    — new: Peppol electronic address
--   * ip_users.user_einvoice_identifier — new: sender e-invoicing identifier
--   * ip_invoices.service_id / ip_quotes.service_id — new: one service per doc
--
-- Idempotent (IF [NOT] EXISTS guards) so it is safe to re-run on a database
-- that already applied any of the superseded files.
-- =====================================================


-- ---------------------------------------------------------
-- ip_merchant_clients — one row per configured integration
-- account (e-invoicing, Peppol access point, payment gateway).
-- Credentials live in ip_settings under "integration_{merchant_type}_*";
-- settings_json is the legacy AES-256-GCM envelope.
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ip_merchant_clients` (
  `id`            INT           AUTO_INCREMENT PRIMARY KEY,
  `merchant_type` VARCHAR(100)  NOT NULL,
  `label`         VARCHAR(255)  NULL,
  `enabled`       TINYINT(1)    DEFAULT 0,
  `auth_type`     VARCHAR(50)   DEFAULT 'oauth2',
  `settings_json` LONGTEXT      NULL COMMENT 'AES-256-GCM encrypted provider settings (ipenc:v1 envelope)',
  `created_at`    DATETIME      NULL,
  `updated_at`    DATETIME      NULL,

  UNIQUE KEY `uniq_merchant_type_label` (`merchant_type`, `label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ---------------------------------------------------------
-- ip_merchant_responses — extend the existing payment-gateway
-- response log to cover e-invoice (SuperPDP, Qonto) and Peppol
-- (LetsPeppol) rows. No JSON blobs — typed columns only.
-- Reused columns: merchant_response_driver / _response /
-- _reference / _successful.
-- ---------------------------------------------------------
ALTER TABLE `ip_merchant_responses`
  MODIFY COLUMN `invoice_id` INT(11) NULL,

  ADD COLUMN IF NOT EXISTS `merchant_client_id` INT(11) NULL
    COMMENT 'FK to ip_merchant_clients — NULL for legacy payment-gateway rows'
    AFTER `invoice_id`,

  ADD COLUMN IF NOT EXISTS `direction` VARCHAR(3) NOT NULL DEFAULT 'out'
    COMMENT 'MerchantResponseDirection enum value: in | out'
    AFTER `merchant_client_id`,

  ADD COLUMN IF NOT EXISTS `record_type` VARCHAR(50) NOT NULL DEFAULT 'payment'
    COMMENT 'MerchantResponseType enum value: payment | outbound_status | incoming_invoice | invoice_event'
    AFTER `direction`,

  ADD COLUMN IF NOT EXISTS `status` VARCHAR(50) NULL
    COMMENT 'MerchantResponseStatus enum value'
    AFTER `record_type`,

  ADD COLUMN IF NOT EXISTS `http_code` SMALLINT NULL
    AFTER `status`,

  ADD COLUMN IF NOT EXISTS `error_code` VARCHAR(100) NULL
    COMMENT 'Structured error code as returned by the provider'
    AFTER `http_code`,

  ADD COLUMN IF NOT EXISTS `error_detail` VARCHAR(500) NULL
    COMMENT 'Human-readable error detail — not a raw JSON dump'
    AFTER `error_code`,

  ADD COLUMN IF NOT EXISTS `peppol_participant_id` VARCHAR(100) NULL
    COMMENT 'Sender or receiver PEPPOL participant identifier, e.g. 0106:12345678'
    AFTER `error_detail`,

  ADD COLUMN IF NOT EXISTS `peppol_document_type` VARCHAR(255) NULL
    COMMENT 'PeppolDocumentType enum value — the BIS document type URN'
    AFTER `peppol_participant_id`,

  ADD COLUMN IF NOT EXISTS `created_at` DATETIME NULL
    COMMENT 'Full datetime precision — merchant_response_date stores the DATE portion'
    AFTER `peppol_document_type`,

  ADD COLUMN IF NOT EXISTS `raw_payload` LONGTEXT NULL
    COMMENT 'Bounded provider audit JSON with credentials, signed URLs, document bodies, and personal identifiers redacted'
    AFTER `created_at`,

  ADD COLUMN IF NOT EXISTS `event_hash` CHAR(64) NULL
    COMMENT 'SHA-256 of raw_payload — dedup key for incoming invoice events'
    AFTER `raw_payload`,

  ADD COLUMN IF NOT EXISTS `document_path` VARCHAR(500) NULL
    COMMENT 'Path relative to uploads/archive for a validated incoming document'
    AFTER `event_hash`,

  ADD COLUMN IF NOT EXISTS `document_name` VARCHAR(255) NULL
    COMMENT 'Sanitized original provider filename'
    AFTER `document_path`,

  ADD COLUMN IF NOT EXISTS `document_mime_type` VARCHAR(100) NULL
    AFTER `document_name`,

  ADD COLUMN IF NOT EXISTS `document_size` INT UNSIGNED NULL
    AFTER `document_mime_type`,

  ADD COLUMN IF NOT EXISTS `document_sha256` CHAR(64) NULL
    AFTER `document_size`,

  ADD COLUMN IF NOT EXISTS `document_profile` VARCHAR(100) NULL
    COMMENT 'Validated EInvoiceProfileRegistry code'
    AFTER `document_sha256`,

  ADD COLUMN IF NOT EXISTS `document_validation_status` VARCHAR(20) NULL
    COMMENT 'valid or failed'
    AFTER `document_profile`,

  ADD COLUMN IF NOT EXISTS `document_validation_error` VARCHAR(1000) NULL
    AFTER `document_validation_status`;

CREATE INDEX IF NOT EXISTS `idx_merchant_client_id`       ON `ip_merchant_responses` (`merchant_client_id`);
CREATE INDEX IF NOT EXISTS `idx_record_type`              ON `ip_merchant_responses` (`record_type`);
CREATE INDEX IF NOT EXISTS `idx_status`                   ON `ip_merchant_responses` (`status`);
CREATE INDEX IF NOT EXISTS `idx_incoming_document_sha256` ON `ip_merchant_responses` (`document_sha256`);
CREATE INDEX IF NOT EXISTS `idx_event_dedup`              ON `ip_merchant_responses` (`merchant_client_id`, `record_type`, `event_hash`);


-- ---------------------------------------------------------
-- All response logging now goes through ip_merchant_responses.
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `ip_einvoice_responses`;


-- ---------------------------------------------------------
-- ip_clients — Peppol electronic address, format {ICD}:{identifier}
--   CZ IČO 0130:27325502 · NL KVK 0106:87654321 ·
--   FR SIRET 0009:12345678900012 · NO Org.nr 0192:123456789 ·
--   EU VAT 9906:NL123456789B01
-- ---------------------------------------------------------
ALTER TABLE `ip_clients`
  ADD COLUMN IF NOT EXISTS `client_peppol_id` VARCHAR(100) NULL
    COMMENT 'Peppol electronic address: {ICD}:{identifier}, e.g. 0130:27325502 (CZ IČO)'
    AFTER `client_einvoicing_version`;


-- ---------------------------------------------------------
-- ip_users — the sender's own e-invoicing electronic address.
-- Mdl_users::validation_rules() already persists this field.
-- ---------------------------------------------------------
ALTER TABLE `ip_users`
  ADD COLUMN IF NOT EXISTS `user_einvoice_identifier` VARCHAR(100) NULL
    COMMENT 'Sender Peppol electronic address / e-invoicing identifier, e.g. {ICD}:{identifier}'
    AFTER `user_company`;


-- ---------------------------------------------------------
-- ip_integration_sync_runs — operational ledger for e-invoicing
-- synchronization runs (one row per triggered sync).
-- ---------------------------------------------------------
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


-- ---------------------------------------------------------
-- Service catalogue and per-client service assignments.
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ip_services` (
  `service_id`   INT NOT NULL AUTO_INCREMENT,
  `service_name` VARCHAR(255) NOT NULL,

  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ip_client_services` (
  `client_id`  INT NOT NULL,
  `service_id` INT NOT NULL,

  PRIMARY KEY (`client_id`, `service_id`),
  KEY `idx_client_services_service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ---------------------------------------------------------
-- Associate a single service with an invoice / quote.
-- ---------------------------------------------------------
ALTER TABLE `ip_invoices`
  ADD COLUMN IF NOT EXISTS `service_id` INT(11) DEFAULT 0 AFTER `creditinvoice_parent_id`;

ALTER TABLE `ip_quotes`
  ADD COLUMN IF NOT EXISTS `service_id` INT(11) DEFAULT 0 AFTER `notes`;
