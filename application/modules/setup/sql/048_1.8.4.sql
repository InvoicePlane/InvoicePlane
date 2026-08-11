-- 1.8.4 — Service catalogue and service/client assignments

CREATE TABLE IF NOT EXISTS `ip_services` (
  `service_id`   INT NOT NULL AUTO_INCREMENT,
  `service_name` VARCHAR(255) NOT NULL,

  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ip_client_services` (
  `client_id`  INT NOT NULL,
  `service_id` INT NOT NULL,

  PRIMARY KEY (`client_id`, `service_id`),
  KEY `idx_client_services_service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

