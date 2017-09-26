# Fix the recurring invoices frequency
ALTER TABLE `ip_invoices_recurring`
  CHANGE `recur_frequency` `recur_frequency` VARCHAR(255)
CHARACTER SET utf8
COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `ip_invoice_items`
  ADD `item_is_recurring` TINYINT(1)
  AFTER `item_order`;

CREATE TABLE `ip_warehouses` (
  `warehouse_id`   INT(11)  NOT NULL AUTO_INCREMENT,
  `warehouse_name` LONGTEXT NOT NULL,
  `warehouse_location` LONGTEXT NOT NULL,
  PRIMARY KEY (`warehouse_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

CREATE TABLE `ip_warehouse_products` (
  `war_pro_id`   INT(11)  NOT NULL AUTO_INCREMENT,  
  `warehouse_id`   INT(11)  NOT NULL,
  `product_id`   INT(11)  NOT NULL,
  `product_qty`   INT(11)  NOT NULL,
  `war_pro_type` INT(1) NOT NULL,
  `war_pro_date` DATETIME NOT NULL,
  `user_id` INT(1) NOT NULL,
  PRIMARY KEY (`war_pro_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

ALTER TABLE ip_products
  ADD COLUMN product_qty INT(11) NULL,
  ADD COLUMN warehouse_id INT(11) NULL;