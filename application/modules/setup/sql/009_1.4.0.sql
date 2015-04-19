# Discounts
ALTER TABLE `ip_quotes`
ADD COLUMN `quote_discount_amount` DECIMAL(20,2) NOT NULL DEFAULT 0
AFTER `quote_number`,
ADD COLUMN `quote_discount_percent` DECIMAL(20,2) NOT NULL DEFAULT 0
AFTER `quote_discount_amount`;

ALTER TABLE `ip_quote_item_amounts`
ADD COLUMN `item_discount` DECIMAL(20,2) NOT NULL DEFAULT 0
AFTER `item_tax_total`;
ALTER TABLE `ip_quote_items`
ADD COLUMN `item_discount_amount` DECIMAL(20,2) NOT NULL DEFAULT 0
AFTER `item_price`;

# Feature IP-162
# For module "projects" 
CREATE TABLE `ip_projects` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `project_name` varchar(150) NOT NULL,
  PRIMARY KEY (`project_id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8; 

# For module "tasks"
CREATE TABLE `ip_tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `task_description` longtext NOT NULL,
  `task_price` float(10,2) NOT NULL,
  `task_finish_date` date NOT NULL,
  `task_status` tinyint(1) NOT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8; 

