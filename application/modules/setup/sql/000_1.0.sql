CREATE TABLE `fi_clients` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_date_created` date NOT NULL,
  `client_date_modified` date NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_address_1` varchar(100) NOT NULL,
  `client_address_2` varchar(100) NOT NULL,
  `client_city` varchar(45) NOT NULL,
  `client_state` varchar(35) NOT NULL,
  `client_zip` varchar(15) NOT NULL,
  `client_country` varchar(35) NOT NULL,
  `client_phone` varchar(20) NOT NULL,
  `client_fax` varchar(20) NOT NULL,
  `client_mobile` varchar(20) NOT NULL,
  `client_email` varchar(100) NOT NULL,
  `client_web` varchar(100) NOT NULL,
  `client_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`client_id`),
  KEY `client_active` (`client_active`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `fi_invoices` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `invoice_group_id` int(11) NOT NULL,
  `invoice_date_created` date NOT NULL,
  `invoice_date_modified` date NOT NULL,
  `invoice_date_due` date NOT NULL,
  `invoice_number` varchar(20) NOT NULL,
  PRIMARY KEY (`invoice_id`),
  KEY `user_id` (`user_id`,`client_id`,`invoice_group_id`,`invoice_date_created`,`invoice_date_due`,`invoice_number`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `fi_invoice_amounts` (
  `invoice_amount_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `invoice_tax_rate` decimal(10,2) NOT NULL,
  `invoice_item_subtotal` decimal(10,2) NOT NULL,
  `invoice_item_tax_total` decimal(10,2) NOT NULL,
  `invoice_total` decimal(10,2) NOT NULL,
  `invoice_paid` decimal(10,2) NOT NULL,
  `invoice_balance` decimal(10,2) NOT NULL,
  PRIMARY KEY (`invoice_amount_id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `invoice_paid` (`invoice_paid`,`invoice_balance`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `fi_invoice_groups` (
  `invoice_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_group_name` varchar(50) NOT NULL DEFAULT '',
  `invoice_group_prefix` varchar(10) NOT NULL DEFAULT '',
  `invoice_group_next_id` int(11) NOT NULL,
  `invoice_group_left_pad` int(2) NOT NULL DEFAULT '0',
  `invoice_group_prefix_year` int(1) NOT NULL DEFAULT '0',
  `invoice_group_prefix_month` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`invoice_group_id`),
  KEY `invoice_group_next_id` (`invoice_group_next_id`),
  KEY `invoice_group_left_pad` (`invoice_group_left_pad`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `fi_invoice_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `item_tax_rate_id` int(11) NOT NULL,
  `item_date_added` date NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_description` longtext NOT NULL,
  `item_quantity` decimal(10,2) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `item_invoice_taxable` int(1) NOT NULL DEFAULT '1',
  `item_order` int(2) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `invoice_id` (`invoice_id`,`item_tax_rate_id`,`item_date_added`,`item_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `fi_invoice_item_amounts` (
  `item_amount_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `item_subtotal` decimal(10,2) NOT NULL,
  `item_tax_total` decimal(10,2) NOT NULL,
  `item_total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`item_amount_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `fi_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_note` longtext NOT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `payment_method_id` (`payment_method_id`),
  KEY `payment_amount` (`payment_amount`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `fi_payment_methods` (
  `payment_method_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_method_name` varchar(35) NOT NULL,
  PRIMARY KEY (`payment_method_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `fi_settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` longtext NOT NULL,
  PRIMARY KEY (`setting_id`),
  KEY `setting_key` (`setting_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `fi_tax_rates` (
  `tax_rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_rate_name` varchar(25) NOT NULL,
  `tax_rate_percent` decimal(5,2) NOT NULL,
  PRIMARY KEY (`tax_rate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `fi_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` int(1) NOT NULL DEFAULT '0',
  `user_date_created` date NOT NULL,
  `user_date_modified` date NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_address_1` varchar(100) NOT NULL,
  `user_address_2` varchar(100) NOT NULL,
  `user_city` varchar(45) NOT NULL,
  `user_state` varchar(35) NOT NULL,
  `user_zip` varchar(15) NOT NULL,
  `user_country` varchar(35) NOT NULL,
  `user_phone` varchar(20) NOT NULL,
  `user_fax` varchar(20) NOT NULL,
  `user_mobile` varchar(20) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(32) NOT NULL,
  `user_web` varchar(100) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `fi_versions` (
  `version_id` int(11) NOT NULL AUTO_INCREMENT,
  `version_date_applied` varchar(14) NOT NULL,
  `version_file` varchar(45) NOT NULL,
  `version_sql_errors` int(2) NOT NULL,
  PRIMARY KEY (`version_id`),
  KEY `version_date_applied` (`version_date_applied`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;