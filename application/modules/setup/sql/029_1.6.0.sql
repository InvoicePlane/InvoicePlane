#[IP-367] - Setup default value
INSERT INTO `ip_settings` (`setting_key`, `setting_value`)
VALUES ('item_tax_order', '1'),
  ('item_discount_order', '2'),
  ('invoice_tax_order', '1'),
  ('invoice_discount_order', '2');