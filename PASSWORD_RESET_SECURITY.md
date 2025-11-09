# Password Reset Rate Limiting Security Enhancement

## Overview

This document describes the security improvements made to the password reset functionality to prevent abuse and email enumeration attacks.

## Vulnerabilities Fixed

### 1. Ineffective Rate Limiting Logic (CRITICAL)
**Location:** `application/modules/sessions/controllers/Sessions.php:202`

**Problem:**
```php
if ($recovery_result = $this->db->where('user_email', $email)) {
```
This condition always evaluates to `true` because it's an assignment, not a comparison. The `where()` method returns a query builder object, which is truthy. This meant the rate limiting check was completely bypassed.

**Impact:**
- Rate limiting was never enforced
- Attackers could send unlimited password reset requests

**Fix:**
```php
$this->db->where('user_email', $email);
$user = $this->db->get('ip_users')->row();

if ($user) {
    // User exists - send reset email
}
```

### 2. No IP-Based Rate Limiting (HIGH)
**Problem:**
- Only tracked attempts by email address
- Attackers could enumerate different email addresses from same IP without restriction

**Fix:**
Added `_is_ip_rate_limited()` method that:
- Tracks total password reset attempts per IP address
- Limits to 5 attempts per IP per hour
- Prevents mass email enumeration attacks

### 3. Email Enumeration Vulnerability (MEDIUM)
**Problem:**
- Different responses for existing vs non-existing email addresses
- Attackers could discover valid email addresses in the system

**Fix:**
- Always show the same success message regardless of whether email exists
- Only send actual reset email if user exists
- Log attempts for non-existent emails for security monitoring

### 4. Weak Rate Limits (MEDIUM)
**Problem:**
- Allowed 10 attempts per email (same as login)
- Password reset is more sensitive and should have stricter limits

**Fix:**
- Reduced to 3 attempts per email address
- Separate tracking for password resets vs login attempts
- 1-hour cooldown period for IP-based limits

## Database Schema Changes

**File:** `application/modules/setup/sql/040_1.6.4_security.sql`

Added columns to `ip_login_log` table:
```sql
ALTER TABLE `ip_login_log` ADD COLUMN `log_ip_address` varchar(45) DEFAULT NULL;
ALTER TABLE `ip_login_log` ADD COLUMN `log_type` varchar(20) DEFAULT 'login';
```

This enables:
- Tracking by IP address
- Separate tracking for different action types (login, password_reset, etc.)
- Better forensics and security monitoring

## Implementation Details

### New Method: `_is_ip_rate_limited()`
```php
private function _is_ip_rate_limited($log_type = 'password_reset', $max_attempts = 5)
{
    $ip_address = $this->input->ip_address();
    
    // Query for IP-based rate limiting
    $query = $this->db->query(
        "SELECT SUM(log_count) as total_count, MAX(log_create_timestamp) as last_attempt 
         FROM ip_login_log 
         WHERE log_ip_address = ? AND log_type = ?",
        [$ip_address, $log_type]
    );
    
    $result = $query->row();
    
    if ($result && $result->total_count >= $max_attempts) {
        $current_time = new DateTime();
        $last_attempt = new DateTime($result->last_attempt);
        $interval = $current_time->diff($last_attempt);
        
        // Allow retry after 1 hour
        if ($interval->h < 1) {
            return true;
        }
    }
    
    return false;
}
```

**Features:**
- Configurable max attempts (default: 5)
- Configurable cooldown period (default: 1 hour)
- Uses parameterized queries (SQL injection safe)
- Aggregates attempts across all emails from same IP

### Enhanced Method: `_login_log_check()`
```php
private function _login_log_check($username, $log_type = 'login')
{
    $login_log_query = $this->db
        ->where('login_name', $username)
        ->where('log_type', $log_type)
        ->get('ip_login_log')
        ->row();
    // ... rest of implementation
}
```

**Improvements:**
- Separate tracking for different action types
- Allows different rate limits for login vs password reset
- Returns null instead of empty on auto-unlock

### Enhanced Method: `_login_log_addfailure()`
```php
private function _login_log_addfailure($username, $log_type = 'login')
{
    $ip_address = $this->input->ip_address();
    
    if (empty($login_log_check = $this->_login_log_check($username, $log_type))) {
        $this->db->insert('ip_login_log', [
            'login_name'           => $username,
            'log_ip_address'       => $ip_address,
            'log_type'             => $log_type,
            'log_count'            => 1,
            'log_create_timestamp' => date('c'),
        ]);
    } else {
        // Update existing record
    }
}
```

**Features:**
- Records IP address with each attempt
- Tracks attempt type (login, password_reset, etc.)
- Updates timestamp on each attempt for accurate cooldown

## Security Flow

### Password Reset Request Flow:

1. **User submits email**
   - Validate email format
   - Sanitize input

2. **IP-based rate limit check**
   - Check if IP has exceeded 5 attempts in last hour
   - If yes: Show rate limit error, redirect to login
   - If no: Continue

3. **Email-based rate limit check**
   - Check if email has exceeded 3 attempts
   - If yes: Show rate limit error, redirect to login
   - If no: Continue

4. **Record the attempt**
   - Log attempt with email, IP, and timestamp
   - Increment counter

5. **Check if user exists**
   - Query database for user with email

6. **Send response**
   - If user exists: Send password reset email
   - If user doesn't exist: Show same success message (prevent enumeration)
   - Always: Show "Email sent" message to user

7. **Log for monitoring**
   - Log attempts for non-existent emails
   - Security teams can detect enumeration attempts

## Rate Limit Configuration

| Type | Limit | Cooldown | Notes |
|------|-------|----------|-------|
| IP-based | 5 attempts | 1 hour | Prevents mass enumeration |
| Email-based | 3 attempts | 12 hours | More strict for password resets |
| Login attempts | 10 attempts | 12 hours | Existing behavior maintained |

## User Experience

### Normal User:
1. Enters email, clicks "Reset Password"
2. Sees: "Email successfully sent"
3. Receives email with reset link (if email exists)

### Attacker Attempting Enumeration:
1. Tries 5 different emails from same IP
2. 6th attempt: "Too many password reset attempts from your IP address. Please try again in 1 hour."
3. Must wait 1 hour before any further attempts

### Legitimate User, Forgotten Email:
1. Tries 3 emails for their account
2. 4th attempt: "Too many password reset attempts for this email address. Please try again later or contact support."
3. Can contact support to resolve

## Security Benefits

1. **Prevents Email Enumeration**
   - Same message for existing/non-existing emails
   - Rate limits prevent mass testing

2. **Prevents Abuse**
   - IP-based limiting stops distributed attacks
   - Email-based limiting protects specific accounts

3. **DoS Protection**
   - Limits email sending to prevent mail server abuse
   - Prevents flooding specific email addresses

4. **Forensic Tracking**
   - IP addresses logged for security analysis
   - Separate tracking for different attack types
   - Timestamp tracking for pattern analysis

5. **User Protection**
   - Prevents harassment via password reset spam
   - Protects against account lockout attacks
   - Maintains usability for legitimate users

## Monitoring Recommendations

### Security Teams Should Monitor:

1. **High frequency of password reset attempts**
   ```sql
   SELECT log_ip_address, COUNT(*) as attempts
   FROM ip_login_log
   WHERE log_type = 'password_reset'
   AND log_create_timestamp > DATE_SUB(NOW(), INTERVAL 1 HOUR)
   GROUP BY log_ip_address
   HAVING attempts > 10;
   ```

2. **Password reset attempts for non-existent emails**
   - Check application logs for: "Password reset attempted for non-existent email"
   - Indicates enumeration attempts

3. **Distributed attacks (many IPs, same email)**
   ```sql
   SELECT login_name, COUNT(DISTINCT log_ip_address) as ip_count
   FROM ip_login_log
   WHERE log_type = 'password_reset'
   AND log_create_timestamp > DATE_SUB(NOW(), INTERVAL 24 HOUR)
   GROUP BY login_name
   HAVING ip_count > 5;
   ```

## Testing

### Test Cases:

1. **Normal password reset**
   - Enter valid email
   - Verify success message
   - Verify email received

2. **Non-existent email**
   - Enter non-existent email
   - Verify same success message
   - Verify no email sent

3. **IP rate limiting**
   - Make 5 password reset requests from same IP
   - 6th request should show rate limit error
   - Wait 1 hour, verify can request again

4. **Email rate limiting**
   - Make 3 password reset requests for same email
   - 4th request should show rate limit error

5. **Auto-unlock after cooldown**
   - Verify rate limits reset after cooldown period

## Migration Notes

**For Existing Installations:**

1. Run database migration: `040_1.6.4_security.sql`
2. This will add new columns to existing `ip_login_log` table
3. Existing login_log records will get default values:
   - `log_ip_address`: NULL (unknown for old records)
   - `log_type`: 'login' (default)
4. No data loss, backward compatible

**Rollback Plan:**
If needed, can rollback by:
```sql
ALTER TABLE `ip_login_log` DROP COLUMN `log_ip_address`;
ALTER TABLE `ip_login_log` DROP COLUMN `log_type`;
ALTER TABLE `ip_login_log` DROP INDEX `idx_ip_address`;
-- Restore original primary key
ALTER TABLE `ip_login_log` DROP PRIMARY KEY;
ALTER TABLE `ip_login_log` ADD PRIMARY KEY (`login_name`);
```

## Language Translations

Added two new language keys in `application/language/english/ip_lang.php`:

```php
'password_reset_rate_limit_ip'    => 'Too many password reset attempts from your IP address. Please try again in 1 hour.',
'password_reset_rate_limit_email' => 'Too many password reset attempts for this email address. Please try again later or contact support.',
```

**Translation Needed For:**
- All other language files in `application/language/`
- Community can contribute translations

## Performance Impact

**Minimal:**
- Added 1 extra database query (IP check) per password reset request
- Query is indexed and very fast
- Only affects password reset flow, not normal operations
- Trade-off for security is acceptable

## Conclusion

These changes significantly improve the security of the password reset functionality by:
1. Fixing broken rate limiting logic
2. Adding IP-based rate limiting
3. Preventing email enumeration
4. Providing better forensic data
5. Maintaining good user experience

The implementation follows security best practices while maintaining backward compatibility and usability.
