ALTER TABLE `fi_users` 
CHANGE `user_name` `user_name` VARCHAR(100) DEFAULT '', 
CHANGE `user_company` `user_company` VARCHAR(100) DEFAULT '', 
CHANGE `user_address_1` `user_address_1` VARCHAR(100) DEFAULT '', 
CHANGE `user_address_2` `user_address_2` VARCHAR(100) DEFAULT '', 
CHANGE `user_city` `user_city` VARCHAR(45) DEFAULT '', 
CHANGE `user_state` `user_state` VARCHAR(35) DEFAULT '', 
CHANGE `user_zip` `user_zip` VARCHAR(15) DEFAULT '', 
CHANGE `user_country` `user_country` VARCHAR(35) DEFAULT '', 
CHANGE `user_phone` `user_phone` VARCHAR(20) DEFAULT '', 
CHANGE `user_fax` `user_fax` VARCHAR(20) DEFAULT '', 
CHANGE `user_mobile` `user_mobile` VARCHAR(20) DEFAULT '', 
CHANGE `user_web` `user_web` VARCHAR(100) DEFAULT '';

ALTER TABLE `fi_clients` 
CHANGE `client_address_1` `client_address_1` VARCHAR(100) DEFAULT '', 
CHANGE `client_address_2` `client_address_2` VARCHAR(100) DEFAULT '', 
CHANGE `client_city` `client_city` VARCHAR(45) DEFAULT '', 
CHANGE `client_state` `client_state` VARCHAR(35) DEFAULT '', 
CHANGE `client_zip` `client_zip` VARCHAR(15) DEFAULT '', 
CHANGE `client_country` `client_country` VARCHAR(35) DEFAULT '', 
CHANGE `client_phone` `client_phone` VARCHAR(20) DEFAULT '', 
CHANGE `client_fax` `client_fax` VARCHAR(20) DEFAULT '', 
CHANGE `client_mobile` `client_mobile` VARCHAR(20) DEFAULT '', 
CHANGE `client_email` `client_email` VARCHAR(100) DEFAULT '', 
CHANGE `client_web` `client_web` VARCHAR(100) DEFAULT '';

ALTER TABLE `fi_invoice_groups` CHANGE `invoice_group_left_pad` `invoice_group_left_pad` INT( 2 ) NOT NULL DEFAULT '0';

ALTER TABLE `fi_invoice_amounts` 
CHANGE `invoice_item_subtotal` `invoice_item_subtotal` DECIMAL(10,2) DEFAULT '0.00', 
CHANGE `invoice_item_tax_total` `invoice_item_tax_total` DECIMAL(10,2) DEFAULT '0.00', 
CHANGE `invoice_tax_total` `invoice_tax_total` DECIMAL(10,2) DEFAULT '0.00', 
CHANGE `invoice_total` `invoice_total` DECIMAL(10,2) DEFAULT '0.00', 
CHANGE `invoice_paid` `invoice_paid` DECIMAL(10,2) DEFAULT '0.00', 
CHANGE `invoice_balance` `invoice_balance` DECIMAL(10,2) DEFAULT '0.00';

ALTER TABLE `fi_quotes` CHANGE `invoice_id` `invoice_id` INT( 11 ) NOT NULL DEFAULT '0';

ALTER TABLE `fi_quote_amounts` CHANGE `quote_total` `quote_total` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0.00';

ALTER TABLE `fi_quote_items` CHANGE `item_order` `item_order` INT( 2 ) NOT NULL DEFAULT '0';

ALTER TABLE `fi_invoice_items` CHANGE `item_order` `item_order` INT( 2 ) NOT NULL DEFAULT '0',
CHANGE `item_tax_rate_id` `item_tax_rate_id` INT( 11 ) NOT NULL DEFAULT '0';

ALTER TABLE `fi_payments` CHANGE `payment_method_id` `payment_method_id` INT( 11 ) NOT NULL DEFAULT '0';