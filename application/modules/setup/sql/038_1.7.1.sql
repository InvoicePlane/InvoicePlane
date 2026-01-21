ALTER TABLE `ip_payment_methods` ADD `payment_method_type_id` TINYINT(1) NOT NULL DEFAULT '1' AFTER `payment_method_name`;
