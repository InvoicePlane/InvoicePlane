# Insert default value for read_only_toggle in ip_settings
INSERT INTO `ip_settings` (`setting_key`, `setting_value`)
VALUES
	('read_only_toggle', 'paid');
