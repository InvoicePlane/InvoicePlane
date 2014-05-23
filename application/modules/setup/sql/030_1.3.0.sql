CREATE TABLE `fi_item_lookups` (
`item_lookup_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`item_name` VARCHAR( 100 ) NOT NULL DEFAULT '',
`item_description` LONGTEXT NOT NULL,
`item_price` DECIMAL( 10, 2 ) NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE `fi_invoices` ADD `invoice_status_id` TINYINT( 2 ) NOT NULL DEFAULT '1' AFTER `invoice_group_id` ,
ADD INDEX ( `invoice_status_id` ) ;

ALTER TABLE `fi_quotes` ADD `quote_status_id` TINYINT( 2 ) NOT NULL DEFAULT '1' AFTER `invoice_group_id` ,
ADD INDEX ( `quote_status_id` ) ;