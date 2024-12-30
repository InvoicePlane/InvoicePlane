ALTER table `ip_invoice_items` MODIFY `item_quantity` DECIMAL(20,8) NULL;
ALTER table `ip_quote_items` MODIFY `item_quantity` DECIMAL(20,8) NULL;
INSERT INTO `ip_settings` (`setting_key`,`setting_value`) VALUES ('default_item_decimals',2);
-- [IP-1003]: Add extra field title (#1101)
ALTER TABLE `ip_clients` ADD client_title VARCHAR(50) DEFAULT NULL AFTER `client_surname`;
