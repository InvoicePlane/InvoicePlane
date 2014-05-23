CREATE TABLE `fi_quote_tax_rates` (
  `quote_tax_rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `tax_rate_id` int(11) NOT NULL,
  `include_item_tax` int(1) NOT NULL DEFAULT '0',
  `quote_tax_rate_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`quote_tax_rate_id`),
  KEY `quote_id` (`quote_id`),
  KEY `tax_rate_id` (`tax_rate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `fi_quote_amounts` ADD `quote_item_subtotal` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `quote_id` ,
ADD `quote_item_tax_total` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `quote_item_subtotal` ,
ADD `quote_tax_total` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `quote_item_tax_total`;

ALTER TABLE `fi_quote_items` ADD `item_tax_rate_id` INT NOT NULL AFTER `quote_id` ,
ADD INDEX ( `item_tax_rate_id` );

ALTER TABLE `fi_quote_item_amounts` ADD `item_subtotal` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `item_id` ,
ADD `item_tax_total` DECIMAL( 10, 2 ) NOT NULL AFTER `item_subtotal`;