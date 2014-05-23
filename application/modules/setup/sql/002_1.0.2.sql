CREATE TABLE `fi_invoice_tax_rates` (
`invoice_tax_rate_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`invoice_id` INT NOT NULL ,
`tax_rate_id` INT NOT NULL ,
INDEX ( `invoice_id` , `tax_rate_id` )
) ENGINE = MYISAM ;