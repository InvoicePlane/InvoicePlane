CREATE TABLE `ip_login_log` (
    `login_name` varchar(100),
    `log_count` int DEFAULT 0,
    `log_create_timestamp` datetime,
    PRIMARY KEY (login_name)
)