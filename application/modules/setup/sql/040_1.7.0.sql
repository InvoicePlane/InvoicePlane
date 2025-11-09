CREATE TABLE IF NOT EXISTS `ip_password_reset_attempts` (
    `id` int NOT NULL AUTO_INCREMENT,
    `ip_address` varchar(45) NOT NULL,
    `email` varchar(255) DEFAULT NULL,
    `attempt_time` int NOT NULL,
    `created_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_ip_time` (`ip_address`, `attempt_time`),
    KEY `idx_email_time` (`email`, `attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
