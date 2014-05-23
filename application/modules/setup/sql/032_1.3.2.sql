ALTER TABLE `fi_quote_amounts` CHANGE `quote_item_subtotal` `quote_item_subtotal` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00',
CHANGE `quote_item_tax_total` `quote_item_tax_total` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00',
CHANGE `quote_tax_total` `quote_tax_total` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00';