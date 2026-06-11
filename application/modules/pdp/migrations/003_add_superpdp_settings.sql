ALTER TABLE ip_pdp_settings
  ADD COLUMN disable_pre_check VARCHAR(10) NULL AFTER extra_payload_json;
