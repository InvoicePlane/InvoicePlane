-- =====================================================
-- 1.8.0 — Integration module schema
--
-- ip_merchant_clients: configuration registry for third-party API
--   integrations (e-invoicing, Peppol access points, payment gateways).
--   Each row is one configured integration account, not a customer record.
--   Credentials are stored in ip_settings under the prefix
--   "integration_{merchant_type}_*". Endpoint URLs are owned by the PHP
--   provider client classes.
--
-- ip_merchant_responses: unified response log extended to cover e-invoice
--   providers (SuperPDP, Qonto) and Peppol providers (LetsPeppol) alongside
--   existing payment-gateway rows. No JSON blobs — all provider data is
--   stored in typed columns.
--
--   Reused existing columns:
--     merchant_response_driver    — provider identifier (superpdp, letspeppol…)
--     merchant_response           — human-readable outcome message
--     merchant_response_reference — provider-assigned document / transaction ref
--     merchant_response_successful — 1 = accepted/received, 0 = rejected/error,
--                                    NULL = pending or indeterminate
-- =====================================================


-- ---------------------------------------------------------
-- Create ip_merchant_clients if the einvoice module has not
-- already run its own first-install script.
-- ---------------------------------------------------------

CREATE TABLE IF NOT EXISTS `ip_merchant_clients` (
  `id`            INT           AUTO_INCREMENT PRIMARY KEY,
  `merchant_type` VARCHAR(100)  NOT NULL,
  `label`         VARCHAR(255)  NULL,
  `enabled`       TINYINT(1)    DEFAULT 0,
  `auth_type`     VARCHAR(50)   DEFAULT 'oauth2',
  `settings_json` LONGTEXT      NULL COMMENT 'legacy — superseded by ip_settings integration_* keys',
  `created_at`    DATETIME      NULL,
  `updated_at`    DATETIME      NULL,

  UNIQUE KEY `uniq_merchant_type_label` (`merchant_type`, `label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ---------------------------------------------------------
-- Extend ip_merchant_responses for einvoice and Peppol rows.
-- Existing columns are untouched.
-- ---------------------------------------------------------

ALTER TABLE `ip_merchant_responses`

  ADD COLUMN `merchant_client_id` INT(11) NULL
    COMMENT 'FK to ip_merchant_clients; NULL for legacy payment-gateway rows'
    AFTER `invoice_id`,

  ADD COLUMN `direction` VARCHAR(3) NOT NULL DEFAULT 'out'
    COMMENT 'MerchantResponseDirection enum value: in | out'
    AFTER `merchant_client_id`,

  ADD COLUMN `record_type` VARCHAR(50) NOT NULL DEFAULT 'payment'
    COMMENT 'MerchantResponseType enum value: payment | outbound_status | incoming_invoice | invoice_event'
    AFTER `direction`,

  ADD COLUMN `status` VARCHAR(50) NULL
    COMMENT 'MerchantResponseStatus enum value'
    AFTER `record_type`,

  ADD COLUMN `http_code` SMALLINT NULL
    AFTER `status`,

  ADD COLUMN `error_code` VARCHAR(100) NULL
    COMMENT 'Structured error code as returned by the provider'
    AFTER `http_code`,

  ADD COLUMN `error_detail` VARCHAR(500) NULL
    COMMENT 'Human-readable error detail — not a raw JSON dump'
    AFTER `error_code`,

  ADD COLUMN `peppol_participant_id` VARCHAR(100) NULL
    COMMENT 'Sender or receiver PEPPOL participant identifier, e.g. 0106:12345678'
    AFTER `error_detail`,

  ADD COLUMN `peppol_document_type` VARCHAR(255) NULL
    COMMENT 'PeppolDocumentType enum value — the BIS document type URN'
    AFTER `peppol_participant_id`,

  ADD COLUMN `created_at` DATETIME NULL
    COMMENT 'Full datetime precision; merchant_response_date stores the DATE portion'
    AFTER `peppol_document_type`,

  ADD INDEX `idx_merchant_client_id` (`merchant_client_id`),
  ADD INDEX `idx_record_type`        (`record_type`),
  ADD INDEX `idx_status`             (`status`);


-- ---------------------------------------------------------
-- Drop the superseded einvoice response table.
-- All response logging now goes through ip_merchant_responses.
-- ---------------------------------------------------------

DROP TABLE IF EXISTS `ip_einvoice_responses`;
