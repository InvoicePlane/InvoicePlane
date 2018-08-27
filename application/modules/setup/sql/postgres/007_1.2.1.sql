-- Insert default value for read_only_toggle in ip_settings
INSERT INTO ip_settings (setting_key, setting_value)
  VALUES ('read_only_toggle', 'paid');

-- Update the period settings
UPDATE ip_settings
  SET setting_value = 'this-month'
  WHERE setting_value = 'month';
UPDATE ip_settings
  SET setting_value = 'this-year'
  WHERE setting_value = 'year';

-- Solves IP-198
ALTER TABLE ip_tax_rates
  ALTER COLUMN tax_rate_name TYPE VARCHAR(60),
  ALTER COLUMN tax_rate_name SET NOT NULL;
