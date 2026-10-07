# IP-1022: extra reference fields for clients, invoices and quotes

ALTER TABLE `ip_clients` ADD `client_number` VARCHAR(255) NULL DEFAULT NULL AFTER `client_id`;

ALTER TABLE `ip_invoices` ADD `invoice_quote_number` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `ip_invoices` ADD `invoice_work_order` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `ip_invoices` ADD `invoice_agreement` VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE `ip_quotes` ADD `quote_work_order` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `ip_quotes` ADD `quote_agreement` VARCHAR(255) NULL DEFAULT NULL;
