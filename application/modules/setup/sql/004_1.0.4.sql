ALTER TABLE `fi_invoice_amounts` ADD `invoice_tax_total` DECIMAL( 10, 2 ) NOT NULL AFTER `invoice_item_tax_total`;

ALTER TABLE `fi_invoice_tax_rates` ADD `invoice_tax_rate_amount` DECIMAL( 10, 2 ) NOT NULL;