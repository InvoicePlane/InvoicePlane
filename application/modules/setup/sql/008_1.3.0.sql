/* IP-195 */
ALTER TABLE `ip_quotes` ADD COLUMN `notes` LONGTEXT;

/*Solves IP-216*/
ALTER TABLE `ip_invoices` 
ADD COLUMN `invoice_time_created` TIME NOT NULL DEFAULT '00:00:00'
AFTER `invoice_date_created`,
/*Solves IP-213*/
ADD COLUMN `invoice_password` VARCHAR(60) NULL
AFTER `is_read_only`;