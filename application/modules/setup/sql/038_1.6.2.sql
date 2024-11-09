ALTER table `ip_invoice_items` MODIFY `item_quantity` DECIMAL(20,8) NULL;
ALTER table `ip_quote_items` MODIFY `item_quantity` DECIMAL(20,8) NULL;
INSERT INTO `ip_settings` (`setting_key`,`setting_value`) VALUES ('default_item_decimals',2);