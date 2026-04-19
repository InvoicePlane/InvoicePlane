# Future Enhancements

This file tracks potential improvements and features to consider for future development.

## Password Reset System Improvements

Consider migrating password reset tokens to a dedicated `password_resets` table instead of storing them in the `ip_users` table. This would enable:

- **Track password reset history/audit trail**: Keep a log of all password reset requests for security auditing
- **Support multiple concurrent reset tokens per user**: Allow users to request multiple reset tokens (e.g., if they lost the first email)
- **Store additional metadata**: Capture IP address, user agent, and other context for security monitoring and fraud detection

**Current Implementation**: Password reset tokens are stored in `ip_users` table as `user_passwordreset_token` and `user_passwordreset_token_expiry` columns.

**Proposed Structure**:
```sql
CREATE TABLE `ip_password_resets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `token` VARCHAR(100) NOT NULL,
    `expiry` DATETIME NOT NULL,
    `ip_address` VARCHAR(45),
    `user_agent` VARCHAR(255),
    `created_at` DATETIME NOT NULL,
    `used_at` DATETIME DEFAULT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `ip_users`(`user_id`) ON DELETE CASCADE,
    INDEX `idx_token` (`token`),
    INDEX `idx_expiry` (`expiry`)
);
```

**Benefits**:
- Better security auditing capabilities
- Easier to identify suspicious patterns (multiple requests from different IPs)
- Historical data for compliance and forensics
- Cleaner separation of concerns

**Note**: This is not a high priority since the current implementation in `ip_users` table is performant and meets current security requirements.
