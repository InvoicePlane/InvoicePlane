# Added for versioning
-- --------------------------------------------------------

--
-- Table structure for table `ip_expenses`
--

DROP TABLE IF EXISTS `ip_expenses`;
CREATE TABLE IF NOT EXISTS `ip_expenses` (
  `expense_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_method_id` int(11) NOT NULL DEFAULT '0',
  `expense_date` date NOT NULL,
  `tax_rate_id` varchar(255) NOT NULL,
  `expense_amount` decimal(10,2) NOT NULL,
  `expense_note` longtext NOT NULL,
  `client_id` int(11) NOT NULL,
  `expense_file` longblob NOT NULL,
  `expense_file_name` varchar(1024) NOT NULL,
  PRIMARY KEY (`expense_id`),
  KEY `payment_method_id` (`payment_method_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ip_expense_custom`
--

DROP TABLE IF EXISTS `ip_expense_custom`;
CREATE TABLE IF NOT EXISTS `ip_expense_custom` (
  `expense_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) NOT NULL,
  PRIMARY KEY (`expense_custom_id`),
  KEY `expense_id` (`expense_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
COMMIT;