# OIDC SSO Support
ALTER TABLE `ip_users`
    MODIFY COLUMN `user_password` VARCHAR(60) NULL,
    ADD COLUMN `user_auth_provider` VARCHAR(20) NOT NULL DEFAULT 'local' AFTER `user_psalt`,
    ADD COLUMN `user_oidc_sub` VARCHAR(255) NULL AFTER `user_auth_provider`;

# Add unique index for OIDC subject lookup
ALTER TABLE `ip_users` ADD UNIQUE INDEX `idx_user_oidc_sub` (`user_oidc_sub`);
