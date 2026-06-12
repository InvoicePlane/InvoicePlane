-- =====================================================
-- InvoicePlane eInvoice module tables
-- =====================================================

CREATE TABLE IF NOT EXISTS ip_merchant_clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  merchant_type VARCHAR(100) NOT NULL,
  label VARCHAR(255) NULL,
  enabled TINYINT(1) DEFAULT 0,
  auth_type VARCHAR(50) DEFAULT 'oauth2',
  settings_json LONGTEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,

  UNIQUE KEY uniq_merchant_type_label (merchant_type, label)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS ip_einvoice_responses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  merchant_client_id INT NOT NULL,
  direction ENUM('out','in') NOT NULL DEFAULT 'out',
  record_type VARCHAR(50) NOT NULL DEFAULT 'outbound_status',
  invoice_id INT NULL,
  external_id VARCHAR(255) NULL,
  status VARCHAR(50) DEFAULT 'draft',
  message TEXT NULL,
  http_code INT NULL,
  request_json LONGTEXT NULL,
  response_json LONGTEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,

  INDEX idx_merchant_client_id (merchant_client_id),
  INDEX idx_invoice_id (invoice_id),
  INDEX idx_external_id (external_id),
  INDEX idx_record_type (record_type),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================
-- Optional default SuperPDP provider
-- =====================================================

INSERT INTO ip_merchant_clients (
  merchant_type,
  label,
  enabled,
  auth_type,
  settings_json,
  created_at,
  updated_at
)
SELECT
  'superpdp',
  'SuperPDP',
  0,
  'oauth2',
  '{
    "client_id": "",
    "client_secret": "",
    "token_url": "https://api.superpdp.tech/oauth2/token",
    "api_base_url": "https://api.superpdp.tech",
    "invoice_endpoint": "/v1.beta/invoices",
    "invoice_status_endpoint": "/v1.beta/invoices/{id}",
    "incoming_invoices_endpoint": "/v1.beta/invoices",
    "invoice_events_endpoint": "/v1.beta/invoice_events",
    "disable_pre_check": false
  }',
  NOW(),
  NOW()
WHERE NOT EXISTS (
  SELECT 1
  FROM ip_merchant_clients
  WHERE merchant_type = 'superpdp'
);

