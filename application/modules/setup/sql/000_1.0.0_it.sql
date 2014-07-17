ALTER TABLE `ip_users` ADD `user_it_codfisc` VARCHAR( 16 ) NOT NULL AFTER `user_web` ,
ADD `user_it_piva` VARCHAR( 11 ) NOT NULL AFTER `user_it_codfisc` ;

ALTER TABLE `ip_clients` ADD `client_it_codfisc` VARCHAR( 16 ) NOT NULL AFTER `client_active` ,
ADD `client_it_piva` VARCHAR( 11 ) NOT NULL AFTER `client_it_codfisc` ;

ALTER TABLE `ip_invoice_groups` ADD `invoice_it_group_suffix` VARCHAR( 10 ) NOT NULL AFTER `invoice_group_prefix_month` ,
ADD `invoice_it_group_suffix_year` BOOLEAN NOT NULL AFTER `invoice_it_group_suffix` ;