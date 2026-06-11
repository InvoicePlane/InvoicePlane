-- Colonnes de suivi SuperPDP.
-- Compatibles MariaDB 10.3+ / MySQL 8 avec IF NOT EXISTS.
ALTER TABLE ip_pdp_settings
  ADD COLUMN IF NOT EXISTS events_endpoint VARCHAR(255) NULL AFTER receive_endpoint;

ALTER TABLE ip_pdp_transmissions
  ADD COLUMN IF NOT EXISTS provider_external_id VARCHAR(255) NULL AFTER external_id,
  ADD COLUMN IF NOT EXISTS invoiceplane_external_id VARCHAR(36) NULL AFTER provider_external_id,
  ADD COLUMN IF NOT EXISTS status_code VARCHAR(100) NULL AFTER status,
  ADD COLUMN IF NOT EXISTS status_text VARCHAR(255) NULL AFTER status_code,
  ADD COLUMN IF NOT EXISTS direction VARCHAR(20) NULL AFTER status_text;

CREATE INDEX IF NOT EXISTS idx_provider_external_id ON ip_pdp_transmissions (provider_external_id);
CREATE INDEX IF NOT EXISTS idx_invoiceplane_external_id ON ip_pdp_transmissions (invoiceplane_external_id);
