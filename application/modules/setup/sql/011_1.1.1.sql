ALTER TABLE `ip_clients` CHANGE `client_date_created` `client_date_created` DATETIME NOT NULL;
ALTER TABLE `ip_clients` CHANGE `client_date_modified` `client_date_modified` DATETIME NOT NULL;
ALTER TABLE `ip_invoices` CHANGE `invoice_date_modified` `invoice_date_modified` DATETIME NOT NULL;
ALTER TABLE `ip_quotes` CHANGE `quote_date_modified` `quote_date_modified` DATETIME NOT NULL;
ALTER TABLE `ip_users` CHANGE `user_date_created` `user_date_created` DATETIME NOT NULL;
ALTER TABLE `ip_users` CHANGE `user_date_modified` `user_date_modified` DATETIME NOT NULL;