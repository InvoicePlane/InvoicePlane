-- 1.8.4 — Service/client assignments

CREATE TABLE IF NOT EXISTS `ip_client_services` (
  `client_id`  INT NOT NULL,
  `service_id` INT NOT NULL,

  PRIMARY KEY (`client_id`, `service_id`),
  KEY `idx_client_services_service_id` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

