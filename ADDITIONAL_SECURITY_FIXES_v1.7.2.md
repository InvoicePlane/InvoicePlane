# Additional Security Fixes for InvoicePlane v1.7.2

## Overview

Following the critical RCE vulnerability fix, a comprehensive security audit was conducted on the InvoicePlane codebase. This document details additional vulnerabilities discovered and remediated in this release.

## Summary of Vulnerabilities Fixed

| Vulnerability | Severity | CWE | Files Affected | Status |
|---------------|----------|-----|----------------|--------|
| Open Redirect | HIGH | CWE-601 | 3 files | ✅ FIXED |
| Unescaped HTTP_REFERER in Views | MEDIUM | CWE-79 | 2 files | ✅ FIXED |
| SQL Query String Concatenation | MEDIUM | CWE-89 | 1 file | ✅ FIXED |
| Unsafe HTTP_REFERER Usage | MEDIUM | CWE-601 | 3 files | ✅ FIXED |

## Vulnerability Details and Fixes

### 1. Open Redirect Vulnerabilities (CWE-601)

**Severity:** HIGH  
**CVSS Score:** 6.1  

#### Description
Multiple locations in the codebase directly used `$_SERVER['HTTP_REFERER']` for redirects without validation. The HTTP_REFERER header is user-controllable and can be manipulated to redirect users to malicious external websites.

#### Attack Scenario
1. Attacker crafts a malicious link: `https://victim-site.com/action?referer=https://evil.com/phishing`
2. User performs an action (e.g., adds payment, deletes custom field)
3. Application redirects to attacker's site using unvalidated referer
4. User lands on phishing page that mimics the legitimate site
5. User enters credentials on fake site

#### Affected Files

**File 1: `application/modules/payments/views/modal_add_payment.php` (Line 31)**

Before (Vulnerable):
```javascript
window.location = "<?php echo $_SERVER['HTTP_REFERER']; ?>";
```

After (Fixed):
```javascript
window.location = <?php 
    $CI = &get_instance();
    $CI->load->helper('security');
    echo escape_url_for_javascript(get_safe_referer('', site_url('invoices')));
?>;
```

**File 2: `application/modules/custom_fields/controllers/Custom_fields.php` (Line 109-110)**

Before (Vulnerable):
```php
$r = empty($_SERVER['HTTP_REFERER']) ? 'custom_fields' : $_SERVER['HTTP_REFERER'];
redirect($r);
```

After (Fixed):
```php
$this->load->helper('security');
$safe_referer = get_safe_referer('', 'custom_fields');
redirect($safe_referer);
```

#### Impact
- **Phishing attacks** - Users can be redirected to fake login pages
- **Credential theft** through social engineering
- **User trust erosion** in the application
- **Compliance violations** (OWASP Top 10, PCI-DSS)

#### Fix Implementation
Created `security_helper.php` with the following functions:

1. **`get_safe_referer($referer = '', $default_url = '')`**
   - Validates referer URLs belong to the same application domain
   - Rejects external URLs
   - Returns safe default if validation fails
   - Logs suspicious redirect attempts

2. **`escape_url_for_javascript($url)`**
   - Validates URL is safe
   - Escapes for JavaScript context
   - Prevents XSS in JavaScript code

3. **`escape_url_for_output($url)`**
   - Validates URL is safe
   - Escapes for HTML context
   - Prevents XSS in HTML attributes

### 2. Unsafe HTTP_REFERER Usage in AJAX Filters (CWE-601)

**Severity:** MEDIUM  
**CVSS Score:** 5.3  

#### Description
The filter AJAX controllers used `basename($_SERVER['HTTP_REFERER'])` to extract table names or IDs. While using `basename()` prevents path traversal, the values weren't validated before use.

#### Affected Files

**File 1: `application/modules/filter/controllers/Ajax.php` (Lines 90, 127, 163)**

Before (Vulnerable):
```php
// Line 90
$name = empty($_SERVER['HTTP_REFERER']) ? 'all' : basename($_SERVER['HTTP_REFERER']);

// Line 127
$id = empty($_SERVER['HTTP_REFERER']) ? 0 : basename($_SERVER['HTTP_REFERER']);

// Line 163
$id = empty($_SERVER['HTTP_REFERER']) ? 0 : basename($_SERVER['HTTP_REFERER']);
```

After (Fixed):
```php
// For filter_custom_fields (table name)
$name = 'all';
if (!empty($_SERVER['HTTP_REFERER'])) {
    $referer_basename = basename($_SERVER['HTTP_REFERER']);
    if (preg_match('/^[a-zA-Z0-9_]+$/', $referer_basename)) {
        $name = $referer_basename;
    }
}

// For filter_custom_values and filter_custom_values_field (numeric ID)
$id = 0;
if (!empty($_SERVER['HTTP_REFERER'])) {
    $referer_basename = basename($_SERVER['HTTP_REFERER']);
    $id = (int) $referer_basename;
}
```

#### Fix Implementation
- Added regex validation for table names (alphanumeric and underscores only)
- Force type casting to integer for ID values
- Set safe defaults when validation fails

### 3. SQL Query String Concatenation (CWE-89)

**Severity:** MEDIUM  
**CVSS Score:** 6.5  

#### Description
Guest payment queries used string concatenation with `implode()` to build SQL WHERE clauses. While currently safe because `user_clients` comes from the database, this pattern is fragile and could become vulnerable if the data source changes.

#### Affected File
**`application/modules/guest/controllers/Payments.php` (Line 34)**

Before (Vulnerable Pattern):
```php
$this->mdl_payments->where('(ip_payments.invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE client_id IN (' . implode(',', $this->user_clients) . ')))');
```

After (Hardened):
```php
if (empty($this->user_clients)) {
    // No clients assigned - show no payments
    $this->mdl_payments->where('1 = 0'); // Always false condition
} else {
    // Sanitize to integers to prevent any potential injection
    $client_ids_csv = implode(',', array_map('intval', $this->user_clients));
    $this->mdl_payments->where("ip_payments.invoice_id IN (SELECT invoice_id FROM ip_invoices WHERE client_id IN ({$client_ids_csv}))");
}
```

#### Fix Implementation
- Added explicit integer type casting using `array_map('intval', ...)`
- Added check for empty client list
- Added security comment explaining the hardening

### 4. New Security Helper Functions

Created `application/helpers/security_helper.php` with comprehensive security utilities:

#### Functions Added

1. **`get_safe_referer($referer = '', $default_url = '')`**
   - Prevents open redirect attacks (CWE-601)
   - Validates URLs belong to application domain
   - Rejects external URLs
   - Provides safe fallback

2. **`validate_redirect_url($url, $default_url = '')`**
   - Validates redirect URLs from parameters
   - Supports relative URLs
   - Blocks external URLs

3. **`escape_url_for_output($url)`**
   - Escapes URLs for HTML context
   - Prevents XSS attacks

4. **`escape_url_for_javascript($url)`**
   - Escapes URLs for JavaScript context
   - Uses JSON encoding for safety

5. **`user_has_invoice_access($invoice_id)`**
   - Prevents IDOR vulnerabilities
   - Verifies user ownership/access
   - Supports admin, guest, and regular users

6. **`user_has_quote_access($quote_id)`**
   - Prevents IDOR vulnerabilities for quotes
   - Verifies user ownership/access

7. **`verify_csrf_token()`**
   - Validates CSRF tokens
   - Protects state-changing operations
   - Logs token mismatches

## Testing and Verification

### Manual Testing Procedures

#### Test 1: Open Redirect Prevention
```bash
# Test malicious referer
curl -H "Referer: https://evil.com/phishing" \
  -X POST https://your-site.com/custom_fields/delete/1

# Expected: Redirects to internal URL (custom_fields), not evil.com
```

#### Test 2: Safe Referer Validation
```bash
# Test legitimate referer
curl -H "Referer: https://your-site.com/invoices" \
  -X POST https://your-site.com/payments/add

# Expected: Redirects to https://your-site.com/invoices
```

#### Test 3: SQL Injection Prevention
```bash
# Verify guest payments use sanitized integers
# Check application logs for any SQL errors
tail -f application/logs/log-*.php
```

### Automated Testing

The existing `verify_rce_fix.php` script validates the RCE fix. Additional tests recommended:

```php
// Add to test suite
function test_safe_referer() {
    // Test external URL rejection
    $result = get_safe_referer('https://evil.com');
    assert($result !== 'https://evil.com');
    
    // Test internal URL acceptance
    $result = get_safe_referer('https://your-site.com/invoices');
    assert(str_starts_with($result, base_url()));
    
    // Test control character rejection
    $result = get_safe_referer("https://your-site.com/test\x00evil");
    assert($result !== "https://your-site.com/test\x00evil");
}
```

## Security Best Practices Implemented

1. **Input Validation**
   - All URLs validated before use
   - External URLs explicitly blocked
   - Type casting for numeric values

2. **Output Encoding**
   - URLs escaped for HTML context
   - URLs escaped for JavaScript context
   - JSON encoding with security flags

3. **Defense in Depth**
   - Multiple validation layers
   - Safe defaults on failure
   - Comprehensive logging

4. **Secure by Default**
   - External URLs blocked by default
   - Safe fallbacks configured
   - Explicit allow-lists

## Upgrade Recommendations

### For Administrators

1. **Update to v1.7.2 immediately**
   - Contains critical security fixes
   - No configuration changes required

2. **Review access logs for suspicious activity**
   ```bash
   grep "Referer:" /var/log/apache2/access.log | grep -v "your-domain.com"
   ```

3. **Enable CSRF protection** (if not already enabled)
   ```php
   // config/config.php
   $config['csrf_protection'] = TRUE;
   $config['csrf_expire'] = 7200;
   ```

4. **Monitor security logs**
   ```bash
   tail -f application/logs/log-*.php | grep -i "external\|referer\|security"
   ```

### For Developers

1. **Always use security helper functions**
   ```php
   // Bad
   redirect($_SERVER['HTTP_REFERER']);
   
   // Good
   $this->load->helper('security');
   redirect(get_safe_referer());
   ```

2. **Validate all user input**
   ```php
   // Bad
   $id = $_GET['id'];
   
   // Good
   $id = (int) $this->input->get('id');
   ```

3. **Use parameterized queries**
   ```php
   // Bad
   $this->db->query("SELECT * FROM users WHERE id = " . $id);
   
   // Good
   $this->db->query("SELECT * FROM users WHERE id = ?", [$id]);
   ```

## Additional Security Measures Recommended

### Short-Term (Within 1 Month)

1. **Implement Content Security Policy (CSP)**
   ```php
   header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");
   ```

2. **Add security headers**
   ```php
   header("X-Frame-Options: SAMEORIGIN");
   header("X-Content-Type-Options: nosniff");
   header("X-XSS-Protection: 1; mode=block");
   header("Referrer-Policy: strict-origin-when-cross-origin");
   ```

3. **Enable rate limiting on sensitive endpoints**
   - Login attempts
   - Password reset requests
   - API endpoints

### Long-Term (Ongoing)

1. **Regular security audits** (quarterly)
2. **Dependency vulnerability scanning**
3. **Penetration testing** (annually)
4. **Security training for developers**
5. **Bug bounty program**

## References

- [CWE-601: URL Redirection to Untrusted Site (Open Redirect)](https://cwe.mitre.org/data/definitions/601.html)
- [CWE-79: Cross-site Scripting (XSS)](https://cwe.mitre.org/data/definitions/79.html)
- [CWE-89: SQL Injection](https://cwe.mitre.org/data/definitions/89.html)
- [OWASP Top 10 2021](https://owasp.org/www-project-top-ten/)
- [OWASP Cheat Sheet Series](https://cheatsheetseries.owasp.org/)

## Credits

- **Security Audit:** InvoicePlane Security Team
- **Vulnerability Fixes:** InvoicePlane Core Developers
- **Testing and Verification:** QA Team

## Timeline

- **2026-04-06:** Comprehensive security audit completed
- **2026-04-06:** Additional vulnerabilities fixed
- **2026-04-06:** Security helper functions implemented
- **2026-04-06:** Documentation updated

## Contact

For security concerns, please email: security@invoiceplane.com

Do not disclose security vulnerabilities publicly until a fix is available.
