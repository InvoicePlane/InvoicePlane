-- 1.8.2 — Protected integration data

ALTER TABLE `ip_merchant_clients`
  MODIFY COLUMN `settings_json` LONGTEXT NULL
    COMMENT 'AES-256-GCM encrypted provider settings (ipenc:v1 envelope)';

ALTER TABLE `ip_merchant_responses`
  MODIFY COLUMN `raw_payload` LONGTEXT NULL
    COMMENT 'Bounded provider audit JSON with credentials, signed URLs, document bodies, and personal identifiers redacted';
