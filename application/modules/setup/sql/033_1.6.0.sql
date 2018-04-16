# Added for versioning
ALTER TABLE `ip_products`
  ADD `product_url_key` CHAR(32) NOT NULL;

ALTER TABLE `ip_uploads`
  ADD `product_id` INT(11) NOT NULL;
	
ALTER TABLE `ip_uploads`
MODIFY COLUMN `client_id` INT(11) NOT NULL;