ALTER TABLE `ip_invoices_recurring` CHANGE `recur_end_date` `recur_end_date` DATE NULL;
ALTER TABLE `ip_invoices_recurring` CHANGE `recur_next_date` `recur_next_date` DATE NULL;
ALTER TABLE `ip_clients` ADD COLUMN `client_currency_code` VARCHAR(3) NULL;