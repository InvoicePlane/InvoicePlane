# Custom Field Enhancements
CREATE TABLE `ip_custom_values` (
  `custom_values_id` INT(11) NOT NULL AUTO_INCREMENT,
  `custom_values_field` INT(11) NOT NULL,
  `custom_values_value` TEXT NOT NULL,
  PRIMARY KEY (`custom_values_id`)
);

ALTER TABLE `ip_custom_fields`
  ADD `custom_field_type` VARCHAR(255) NOT NULL AFTER `custom_field_label`;

# Delete and re-add the ip_sessions table for CI 3
DROP TABLE `ip_sessions`;
CREATE TABLE IF NOT EXISTS `ip_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
  `data` blob NOT NULL,
  KEY `ip_sessions_timestamp` (`timestamp`)
);
