# ************************************************************
# Sequel Pro SQL dump
# Version 4135
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 192.168.50.102 (MySQL 5.5.41-MariaDB)
# Database: dev_pay_devopsgroup_io
# Generation Time: 2015-07-15 23:58:36 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table ip_client_custom
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_client_custom`;

CREATE TABLE `ip_client_custom` (
  `client_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`client_custom_id`),
  KEY `client_id` (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_client_notes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_client_notes`;

CREATE TABLE `ip_client_notes` (
  `client_note_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `client_note_date` date NOT NULL,
  `client_note` longtext NOT NULL,
  PRIMARY KEY (`client_note_id`),
  KEY `client_id` (`client_id`,`client_note_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_clients
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_clients`;

CREATE TABLE `ip_clients` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_date_created` datetime NOT NULL,
  `client_date_modified` datetime NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_address_1` varchar(100) DEFAULT '',
  `client_address_2` varchar(100) DEFAULT '',
  `client_city` varchar(45) DEFAULT '',
  `client_state` varchar(35) DEFAULT '',
  `client_zip` varchar(15) DEFAULT '',
  `client_country` varchar(35) DEFAULT '',
  `client_phone` varchar(20) DEFAULT '',
  `client_fax` varchar(20) DEFAULT '',
  `client_mobile` varchar(20) DEFAULT '',
  `client_email` varchar(100) DEFAULT '',
  `client_web` varchar(100) DEFAULT '',
  `client_vat_id` varchar(100) NOT NULL DEFAULT '',
  `client_tax_code` varchar(100) NOT NULL DEFAULT '',
  `client_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`client_id`),
  KEY `client_active` (`client_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_custom_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_custom_fields`;

CREATE TABLE `ip_custom_fields` (
  `custom_field_id` int(11) NOT NULL AUTO_INCREMENT,
  `custom_field_table` varchar(35) NOT NULL,
  `custom_field_label` varchar(64) NOT NULL,
  `custom_field_column` varchar(64) NOT NULL,
  PRIMARY KEY (`custom_field_id`),
  KEY `custom_field_table` (`custom_field_table`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_email_templates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_email_templates`;

CREATE TABLE `ip_email_templates` (
  `email_template_id` int(11) NOT NULL AUTO_INCREMENT,
  `email_template_title` varchar(255) NOT NULL,
  `email_template_type` varchar(255) DEFAULT NULL,
  `email_template_body` longtext NOT NULL,
  `email_template_subject` varchar(255) DEFAULT NULL,
  `email_template_from_name` varchar(255) DEFAULT NULL,
  `email_template_from_email` varchar(255) DEFAULT NULL,
  `email_template_cc` varchar(255) DEFAULT NULL,
  `email_template_bcc` varchar(255) DEFAULT NULL,
  `email_template_pdf_template` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`email_template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_families
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_families`;

CREATE TABLE `ip_families` (
  `family_id` int(11) NOT NULL AUTO_INCREMENT,
  `family_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`family_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_import_details
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_import_details`;

CREATE TABLE `ip_import_details` (
  `import_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `import_id` int(11) NOT NULL,
  `import_lang_key` varchar(35) NOT NULL,
  `import_table_name` varchar(35) NOT NULL,
  `import_record_id` int(11) NOT NULL,
  PRIMARY KEY (`import_detail_id`),
  KEY `import_id` (`import_id`,`import_record_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_imports
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_imports`;

CREATE TABLE `ip_imports` (
  `import_id` int(11) NOT NULL AUTO_INCREMENT,
  `import_date` datetime NOT NULL,
  PRIMARY KEY (`import_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_invoice_amounts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_invoice_amounts`;

CREATE TABLE `ip_invoice_amounts` (
  `invoice_amount_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `invoice_sign` enum('1','-1') NOT NULL DEFAULT '1',
  `invoice_item_subtotal` decimal(20,2) DEFAULT NULL,
  `invoice_item_tax_total` decimal(20,2) DEFAULT NULL,
  `invoice_tax_total` decimal(20,2) DEFAULT NULL,
  `invoice_total` decimal(20,2) DEFAULT NULL,
  `invoice_paid` decimal(20,2) DEFAULT NULL,
  `invoice_balance` decimal(20,2) DEFAULT NULL,
  PRIMARY KEY (`invoice_amount_id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `invoice_paid` (`invoice_paid`,`invoice_balance`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_invoice_custom
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_invoice_custom`;

CREATE TABLE `ip_invoice_custom` (
  `invoice_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  PRIMARY KEY (`invoice_custom_id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_invoice_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_invoice_groups`;

CREATE TABLE `ip_invoice_groups` (
  `invoice_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_group_name` varchar(50) NOT NULL DEFAULT '',
  `invoice_group_identifier_format` varchar(255) NOT NULL,
  `invoice_group_next_id` int(11) NOT NULL,
  `invoice_group_left_pad` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`invoice_group_id`),
  KEY `invoice_group_next_id` (`invoice_group_next_id`),
  KEY `invoice_group_left_pad` (`invoice_group_left_pad`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `ip_invoice_groups` WRITE;
/*!40000 ALTER TABLE `ip_invoice_groups` DISABLE KEYS */;

INSERT INTO `ip_invoice_groups` (`invoice_group_id`, `invoice_group_name`, `invoice_group_identifier_format`, `invoice_group_next_id`, `invoice_group_left_pad`)
VALUES
	(3,'Invoice Default','{{{id}}}',1,0),
	(4,'Quote Default','QUO{{{id}}}',1,0);

/*!40000 ALTER TABLE `ip_invoice_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ip_invoice_item_amounts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_invoice_item_amounts`;

CREATE TABLE `ip_invoice_item_amounts` (
  `item_amount_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `item_subtotal` decimal(20,2) DEFAULT NULL,
  `item_tax_total` decimal(20,2) DEFAULT NULL,
  `item_discount` decimal(20,2) NOT NULL DEFAULT '0.00',
  `item_total` decimal(20,2) DEFAULT NULL,
  PRIMARY KEY (`item_amount_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_invoice_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_invoice_items`;

CREATE TABLE `ip_invoice_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `item_tax_rate_id` int(11) NOT NULL DEFAULT '0',
  `item_date_added` date NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_description` longtext NOT NULL,
  `item_quantity` decimal(10,2) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `item_discount_amount` decimal(20,2) NOT NULL DEFAULT '0.00',
  `item_order` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`),
  KEY `invoice_id` (`invoice_id`,`item_tax_rate_id`,`item_date_added`,`item_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_invoice_tax_rates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_invoice_tax_rates`;

CREATE TABLE `ip_invoice_tax_rates` (
  `invoice_tax_rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `tax_rate_id` int(11) NOT NULL,
  `include_item_tax` int(1) NOT NULL DEFAULT '0',
  `invoice_tax_rate_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`invoice_tax_rate_id`),
  KEY `invoice_id` (`invoice_id`,`tax_rate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_invoices
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_invoices`;

CREATE TABLE `ip_invoices` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `invoice_group_id` int(11) NOT NULL,
  `invoice_status_id` tinyint(2) NOT NULL DEFAULT '1',
  `is_read_only` tinyint(1) DEFAULT NULL,
  `invoice_password` varchar(90) DEFAULT NULL,
  `invoice_date_created` date NOT NULL,
  `invoice_time_created` time NOT NULL DEFAULT '00:00:00',
  `invoice_date_modified` datetime NOT NULL,
  `invoice_date_due` date NOT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `invoice_discount_amount` decimal(20,2) NOT NULL DEFAULT '0.00',
  `invoice_discount_percent` decimal(20,2) NOT NULL DEFAULT '0.00',
  `invoice_terms` longtext NOT NULL,
  `invoice_url_key` char(32) NOT NULL,
  `payment_method` int(11) NOT NULL DEFAULT '0',
  `creditinvoice_parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`invoice_id`),
  UNIQUE KEY `invoice_url_key` (`invoice_url_key`),
  KEY `user_id` (`user_id`,`client_id`,`invoice_group_id`,`invoice_date_created`,`invoice_date_due`,`invoice_number`),
  KEY `invoice_status_id` (`invoice_status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_invoices_recurring
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_invoices_recurring`;

CREATE TABLE `ip_invoices_recurring` (
  `invoice_recurring_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `recur_start_date` date NOT NULL,
  `recur_end_date` date NOT NULL,
  `recur_frequency` char(2) NOT NULL,
  `recur_next_date` date NOT NULL,
  PRIMARY KEY (`invoice_recurring_id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_item_lookups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_item_lookups`;

CREATE TABLE `ip_item_lookups` (
  `item_lookup_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(100) NOT NULL DEFAULT '',
  `item_description` longtext NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`item_lookup_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_merchant_responses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_merchant_responses`;

CREATE TABLE `ip_merchant_responses` (
  `merchant_response_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `merchant_response_date` date NOT NULL,
  `merchant_response_driver` varchar(35) NOT NULL,
  `merchant_response` varchar(255) NOT NULL,
  `merchant_response_reference` varchar(255) NOT NULL,
  PRIMARY KEY (`merchant_response_id`),
  KEY `merchant_response_date` (`merchant_response_date`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_payment_custom
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_payment_custom`;

CREATE TABLE `ip_payment_custom` (
  `payment_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  PRIMARY KEY (`payment_custom_id`),
  KEY `payment_id` (`payment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_payment_methods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_payment_methods`;

CREATE TABLE `ip_payment_methods` (
  `payment_method_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_method_name` varchar(35) NOT NULL,
  PRIMARY KEY (`payment_method_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_payments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_payments`;

CREATE TABLE `ip_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `payment_method_id` int(11) NOT NULL DEFAULT '0',
  `payment_date` date NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_note` longtext NOT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `payment_method_id` (`payment_method_id`),
  KEY `payment_amount` (`payment_amount`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_products`;

CREATE TABLE `ip_products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `family_id` int(11) NOT NULL,
  `product_sku` varchar(15) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `product_description` longtext NOT NULL,
  `product_price` float(10,2) NOT NULL,
  `purchase_price` float(10,2) NOT NULL,
  `tax_rate_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_projects
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_projects`;

CREATE TABLE `ip_projects` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `project_name` varchar(150) NOT NULL,
  PRIMARY KEY (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_quote_amounts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_quote_amounts`;

CREATE TABLE `ip_quote_amounts` (
  `quote_amount_id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `quote_item_subtotal` decimal(20,2) DEFAULT NULL,
  `quote_item_tax_total` decimal(20,2) DEFAULT NULL,
  `quote_tax_total` decimal(20,2) DEFAULT NULL,
  `quote_total` decimal(20,2) DEFAULT NULL,
  PRIMARY KEY (`quote_amount_id`),
  KEY `quote_id` (`quote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_quote_custom
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_quote_custom`;

CREATE TABLE `ip_quote_custom` (
  `quote_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  PRIMARY KEY (`quote_custom_id`),
  KEY `quote_id` (`quote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_quote_item_amounts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_quote_item_amounts`;

CREATE TABLE `ip_quote_item_amounts` (
  `item_amount_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `item_subtotal` decimal(20,2) DEFAULT NULL,
  `item_tax_total` decimal(20,2) DEFAULT NULL,
  `item_discount` decimal(20,2) NOT NULL DEFAULT '0.00',
  `item_total` decimal(20,2) DEFAULT NULL,
  PRIMARY KEY (`item_amount_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_quote_items
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_quote_items`;

CREATE TABLE `ip_quote_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `item_tax_rate_id` int(11) NOT NULL,
  `item_date_added` date NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_description` longtext NOT NULL,
  `item_quantity` decimal(10,2) NOT NULL,
  `item_price` decimal(10,2) NOT NULL,
  `item_discount_amount` decimal(20,2) NOT NULL DEFAULT '0.00',
  `item_order` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`),
  KEY `quote_id` (`quote_id`,`item_date_added`,`item_order`),
  KEY `item_tax_rate_id` (`item_tax_rate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_quote_tax_rates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_quote_tax_rates`;

CREATE TABLE `ip_quote_tax_rates` (
  `quote_tax_rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `tax_rate_id` int(11) NOT NULL,
  `include_item_tax` int(1) NOT NULL DEFAULT '0',
  `quote_tax_rate_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`quote_tax_rate_id`),
  KEY `quote_id` (`quote_id`),
  KEY `tax_rate_id` (`tax_rate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_quotes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_quotes`;

CREATE TABLE `ip_quotes` (
  `quote_id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `invoice_group_id` int(11) NOT NULL,
  `quote_status_id` tinyint(2) NOT NULL DEFAULT '1',
  `quote_date_created` date NOT NULL,
  `quote_date_modified` datetime NOT NULL,
  `quote_date_expires` date NOT NULL,
  `quote_number` varchar(100) NOT NULL,
  `quote_discount_amount` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quote_discount_percent` decimal(20,2) NOT NULL DEFAULT '0.00',
  `quote_url_key` char(32) NOT NULL,
  `quote_password` varchar(90) DEFAULT NULL,
  `notes` longtext,
  PRIMARY KEY (`quote_id`),
  KEY `user_id` (`user_id`,`client_id`,`invoice_group_id`,`quote_date_created`,`quote_date_expires`,`quote_number`),
  KEY `invoice_id` (`invoice_id`),
  KEY `quote_status_id` (`quote_status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_settings`;

CREATE TABLE `ip_settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` longtext NOT NULL,
  PRIMARY KEY (`setting_id`),
  KEY `setting_key` (`setting_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `ip_settings` WRITE;
/*!40000 ALTER TABLE `ip_settings` DISABLE KEYS */;

INSERT INTO `ip_settings` (`setting_id`, `setting_key`, `setting_value`)
VALUES
	(19,'default_language','english'),
	(20,'date_format','m/d/Y'),
	(21,'currency_symbol','$'),
	(22,'currency_symbol_placement','before'),
	(23,'invoices_due_after','30'),
	(24,'quotes_expire_after','15'),
	(25,'default_invoice_group','1'),
	(26,'default_quote_group','2'),
	(27,'thousands_separator',','),
	(28,'decimal_point','.'),
	(29,'cron_key','sAA1HUWqzmUKBvXl'),
	(30,'tax_rate_decimal_places','2'),
	(31,'pdf_invoice_template','default'),
	(32,'pdf_invoice_template_paid','default'),
	(33,'pdf_invoice_template_overdue','default'),
	(34,'pdf_quote_template','default'),
	(35,'public_invoice_template','default'),
	(36,'public_quote_template','default'),
	(37,'disable_sidebar','1'),
	(38,'read_only_toggle','paid'),
	(39,'invoice_pre_password',''),
	(40,'quote_pre_password',''),
	(41,'email_pdf_attachment','1');

/*!40000 ALTER TABLE `ip_settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ip_tasks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_tasks`;

CREATE TABLE `ip_tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `task_description` longtext NOT NULL,
  `task_price` float(10,2) NOT NULL,
  `task_finish_date` date NOT NULL,
  `task_status` tinyint(1) NOT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_tax_rates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_tax_rates`;

CREATE TABLE `ip_tax_rates` (
  `tax_rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_rate_name` varchar(60) NOT NULL,
  `tax_rate_percent` decimal(5,2) NOT NULL,
  PRIMARY KEY (`tax_rate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_uploads
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_uploads`;

CREATE TABLE `ip_uploads` (
  `upload_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `url_key` char(32) NOT NULL,
  `file_name_original` longtext NOT NULL,
  `file_name_new` longtext NOT NULL,
  `uploaded_date` date NOT NULL,
  PRIMARY KEY (`upload_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_user_clients
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_user_clients`;

CREATE TABLE `ip_user_clients` (
  `user_client_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  PRIMARY KEY (`user_client_id`),
  KEY `user_id` (`user_id`,`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_user_custom
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_user_custom`;

CREATE TABLE `ip_user_custom` (
  `user_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`user_custom_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ip_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_users`;

CREATE TABLE `ip_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` int(1) NOT NULL DEFAULT '0',
  `user_active` tinyint(1) DEFAULT '1',
  `user_date_created` datetime NOT NULL,
  `user_date_modified` datetime NOT NULL,
  `user_name` varchar(100) DEFAULT '',
  `user_company` varchar(100) DEFAULT '',
  `user_address_1` varchar(100) DEFAULT '',
  `user_address_2` varchar(100) DEFAULT '',
  `user_city` varchar(45) DEFAULT '',
  `user_state` varchar(35) DEFAULT '',
  `user_zip` varchar(15) DEFAULT '',
  `user_country` varchar(35) DEFAULT '',
  `user_phone` varchar(20) DEFAULT '',
  `user_fax` varchar(20) DEFAULT '',
  `user_mobile` varchar(20) DEFAULT '',
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(60) NOT NULL,
  `user_web` varchar(100) DEFAULT '',
  `user_vat_id` varchar(100) NOT NULL DEFAULT '',
  `user_tax_code` varchar(100) NOT NULL DEFAULT '',
  `user_psalt` char(22) NOT NULL,
  `user_passwordreset_token` varchar(100) DEFAULT '',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `ip_users` WRITE;
/*!40000 ALTER TABLE `ip_users` DISABLE KEYS */;

INSERT INTO `ip_users` (`user_id`, `user_type`, `user_active`, `user_date_created`, `user_date_modified`, `user_name`, `user_company`, `user_address_1`, `user_address_2`, `user_city`, `user_state`, `user_zip`, `user_country`, `user_phone`, `user_fax`, `user_mobile`, `user_email`, `user_password`, `user_web`, `user_vat_id`, `user_tax_code`, `user_psalt`, `user_passwordreset_token`)
VALUES
	(1,1,1,'2015-07-15 19:56:55','2015-07-15 19:56:55','devopsgroup.io','','1305 Spruce Street','Apartment 3A','Philadelphia','Pennsylvania','19107','US','','','','pay@devopsgroup.io','$2a$10$899abf1ee20aa6eb03be5uevYbpELLANG2JXjoKMnN1Uex05w9YkC','','','','899abf1ee20aa6eb03be53','');

/*!40000 ALTER TABLE `ip_users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ip_versions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ip_versions`;

CREATE TABLE `ip_versions` (
  `version_id` int(11) NOT NULL AUTO_INCREMENT,
  `version_date_applied` varchar(14) NOT NULL,
  `version_file` varchar(45) NOT NULL,
  `version_sql_errors` int(2) NOT NULL,
  PRIMARY KEY (`version_id`),
  KEY `version_date_applied` (`version_date_applied`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `ip_versions` WRITE;
/*!40000 ALTER TABLE `ip_versions` DISABLE KEYS */;

INSERT INTO `ip_versions` (`version_id`, `version_date_applied`, `version_file`, `version_sql_errors`)
VALUES
	(1,'1437004531','000_1.0.0.sql',0),
	(2,'1437004532','001_1.0.1.sql',0),
	(3,'1437004532','002_1.0.2.sql',0),
	(4,'1437004532','003_1.1.0.sql',0),
	(5,'1437004532','004_1.1.1.sql',0),
	(6,'1437004532','005_1.1.2.sql',0),
	(7,'1437004532','006_1.2.0.sql',0),
	(8,'1437004532','007_1.2.1.sql',0),
	(9,'1437004532','008_1.3.0.sql',0),
	(10,'1437004532','009_1.3.1.sql',0),
	(11,'1437004532','010_1.3.2.sql',0),
	(12,'1437004532','011_1.3.3.sql',0),
	(13,'1437004532','012_1.4.0.sql',0),
	(14,'1437004532','013_1.4.1.sql',0),
	(15,'1437004532','014_1.4.2.sql',0),
	(16,'1437004532','015_1.4.3.sql',0);

/*!40000 ALTER TABLE `ip_versions` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
