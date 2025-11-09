-- Add IP address tracking and request type to login_log table for better rate limiting
ALTER TABLE `ip_login_log` ADD COLUMN `log_ip_address` varchar(45) DEFAULT NULL AFTER `login_name`;
ALTER TABLE `ip_login_log` ADD COLUMN `log_type` varchar(20) DEFAULT 'login' AFTER `log_ip_address`;
ALTER TABLE `ip_login_log` DROP PRIMARY KEY;
ALTER TABLE `ip_login_log` ADD PRIMARY KEY (`login_name`, `log_type`);
CREATE INDEX `idx_ip_address` ON `ip_login_log` (`log_ip_address`, `log_type`);
