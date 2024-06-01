ALTER TABLE `ip_clients` ADD client_number VARCHAR(100) NULL DEFAULT NULL AFTER `client_id`;
ALTER TABLE `ip_invoices` ADD invoice_quote_number VARCHAR(100) NULL DEFAULT NULL AFTER `invoice_id`;
ALTER TABLE `ip_invoices` ADD invoice_work_order VARCHAR(100) NULL DEFAULT NULL AFTER `invoice_quote_number`;
ALTER TABLE `ip_invoices` ADD invoice_agreement VARCHAR(100) NULL DEFAULT NULL AFTER `invoice_work_order`;
ALTER TABLE `ip_quotes` ADD quote_work_order VARCHAR(100) NULL DEFAULT NULL AFTER `quote_id`;
ALTER TABLE `ip_quotes` ADD quote_agreement VARCHAR(100) NULL DEFAULT NULL AFTER `quote_work_order`;
