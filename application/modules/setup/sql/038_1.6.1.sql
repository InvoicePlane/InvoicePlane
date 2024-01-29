ALTER TABLE `ip_clients` ADD client_number VARCHAR(30) NULL DEFAULT NULL AFTER `client_id`;
ALTER TABLE `ip_invoices` ADD invoice_quote_number INT AFTER `invoice_id`;
ALTER TABLE `ip_invoices` ADD invoice_work_order VARCHAR(30) NULL DEFAULT NULL AFTER `invoice_quote_number`;
ALTER TABLE `ip_invoices` ADD invoice_agreement VARCHAR(30) NULL DEFAULT NULL AFTER `invoice_work_order`;
