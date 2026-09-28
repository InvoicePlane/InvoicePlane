# Add per-user authentication version to invalidate sessions on password change
ALTER TABLE `ip_users` ADD COLUMN `user_auth_version` INT(10) UNSIGNED DEFAULT 1 NOT NULL;
