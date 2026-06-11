ALTER TABLE ip_pdp_transmissions
  ADD COLUMN file_path VARCHAR(500) NULL AFTER provider,
  ADD COLUMN file_name VARCHAR(255) NULL AFTER file_path,
  ADD COLUMN file_sha256 CHAR(64) NULL AFTER file_name;
