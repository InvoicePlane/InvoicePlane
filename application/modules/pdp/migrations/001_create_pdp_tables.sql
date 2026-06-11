CREATE TABLE IF NOT EXISTS ip_pdp_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  provider VARCHAR(100) NOT NULL DEFAULT 'demo',
  enabled TINYINT(1) DEFAULT 0,
  api_url VARCHAR(255) NULL,
  auth_type VARCHAR(50) DEFAULT 'bearer',
  client_id VARCHAR(255) NULL,
  client_secret TEXT NULL,
  access_token TEXT NULL,
  api_key TEXT NULL,
  api_key_header VARCHAR(100) DEFAULT 'X-API-Key',
  token_url VARCHAR(255) NULL,
  scope VARCHAR(255) NULL,
  send_endpoint VARCHAR(255) DEFAULT '/invoices',
  status_endpoint VARCHAR(255) DEFAULT '/invoices/{id}',
  receive_endpoint VARCHAR(255) DEFAULT '/supplier-invoices',
  file_field VARCHAR(100) DEFAULT 'file',
  extra_payload_json LONGTEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL
);

CREATE TABLE IF NOT EXISTS ip_pdp_transmissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT NOT NULL,
  provider VARCHAR(100) NOT NULL,
  external_id VARCHAR(255) NULL,
  status VARCHAR(50) DEFAULT 'draft',
  message TEXT NULL,
  http_code INT NULL,
  request_json LONGTEXT NULL,
  response_json LONGTEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX idx_invoice_id (invoice_id),
  INDEX idx_external_id (external_id),
  INDEX idx_status (status)
);

CREATE TABLE IF NOT EXISTS ip_pdp_incoming_invoices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  provider VARCHAR(100) NULL,
  external_id VARCHAR(255) NULL,
  supplier_name VARCHAR(255) NULL,
  supplier_siren VARCHAR(20) NULL,
  invoice_number VARCHAR(100) NULL,
  issue_date DATE NULL,
  amount_total DECIMAL(15,2) NULL,
  currency VARCHAR(3) DEFAULT 'EUR',
  status VARCHAR(50) DEFAULT 'received',
  raw_json LONGTEXT NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX idx_external_id (external_id),
  INDEX idx_supplier_siren (supplier_siren)
);
