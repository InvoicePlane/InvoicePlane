# Security: Add password reset token expiration
# This adds a timestamp column to track when password reset tokens were created,
# allowing the system to enforce a strict expiration time (default: 15 minutes)
# to prevent indefinite token validity and reduce account takeover risk.
ALTER TABLE `ip_users`
    ADD COLUMN `user_passwordreset_token_expiry` DATETIME DEFAULT NULL AFTER `user_passwordreset_token`;
