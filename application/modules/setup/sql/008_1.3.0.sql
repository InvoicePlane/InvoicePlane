# IP-195
ALTER TABLE `ip_quotes` ADD COLUMN `notes` LONGTEXT;

# Solves IP-216
ALTER TABLE `ip_invoices` 
ADD COLUMN `invoice_time_created` TIME NOT NULL DEFAULT '00:00:00'
AFTER `invoice_date_created`;

# Solves IP-196
ALTER TABLE `ip_invoices`
ADD COLUMN `payment_method` INT NOT NULL DEFAULT 0
AFTER `invoice_url_key`;