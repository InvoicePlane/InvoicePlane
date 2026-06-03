# Changelog

All notable changes to InvoicePlane will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.7.2] - 2026-04-06

### Security

**CRITICAL: Fixed Broken Authentication - Password reset tokens now expire (#TBD)**
- Password reset tokens now expire after a configurable time (default: 15 minutes)
- Added `user_passwordreset_token_expiry` database column to track token creation time
- Tokens are automatically invalidated after expiration
- Prevents indefinite token validity that could lead to account takeover
- Expired tokens are automatically cleared from the database
- Configuration option `PASSWORD_RESET_TOKEN_EXPIRY_MINUTES` added to ipconfig.php.example

---

**HIGH: Fixed Arbitrary File Deletion via Path Traversal - CVSSv3 7.1 (CVE Pending)**

Authenticated administrators could delete arbitrary files on the server through path traversal sequences in logo filename settings. An attacker could set a malicious logo filename (e.g., `../../config/database.php`) via the settings page, then trigger deletion through the remove_logo endpoint.

**Vulnerability Details:**
- **CWE-22:** Improper Limitation of a Pathname to a Restricted Directory
- **Attack Vector:** Authenticated administrator could exploit path traversal
- **Impact:** Application failure, data loss, denial of service through file deletion

**Root Cause:**
1. Settings save functionality accepted arbitrary logo filenames without validation
2. The `remove_logo()` function used database values directly for file deletion
3. No path traversal detection or directory confinement checks

**Fix Implementation:**
- **Added input validation on settings save** (lines 78-87 in Settings.php)
  - Logo filenames validated using `validate_safe_filename()` before saving to database
  - Path traversal sequences rejected with error logging
  - Invalid filenames blocked with user-friendly error message
  
- **Added type parameter validation** (lines 272-282 in Settings.php)
  - Logo type restricted to allow-list: `['invoice', 'login']`
  - Invalid types logged and rejected
  - Prevents arbitrary type parameter injection
  
- **Added comprehensive file access validation** (lines 293-323 in Settings.php)
  - Multi-layer validation using `validate_file_access()` helper
  - Files must be within `./uploads/` directory (directory confinement)
  - Path traversal sequences detected and blocked
  - Null byte injection prevented
  - Absolute path attempts rejected
  - All validation failures logged with secure hashing
  
- **Enhanced file security helper** (`application/helpers/file_security_helper.php`)
  - `validate_safe_filename()` - Detects path traversal, null bytes, absolute paths
  - `validate_file_in_directory()` - Ensures files stay within allowed directory
  - `validate_file_access()` - Complete file validation with 5 security checks
  - All functions include secure logging with SHA-256 hashing

**Defense-in-Depth Layers:**
1. Input validation - Filenames validated on settings save
2. Type validation - Logo type restricted to allow-list
3. Path traversal detection - Multiple checks for `../`, `..\\`, `/../`, etc.
4. Null byte detection - Prevents path truncation attacks
5. Absolute path rejection - Blocks `/etc/passwd` style attacks
6. Directory confinement - Files must be in uploads directory
7. Secure logging - Attack attempts logged with hash (prevents log injection)

**Files Changed:**
- `application/modules/settings/controllers/Settings.php` - Input validation and file access validation
- `application/helpers/file_security_helper.php` - File security validation functions

**Impact:**
- **Before:** Authenticated admin could delete ANY file on the server
- **After:** Only files in uploads directory can be deleted, with strict validation
- **Scope:** HIGH - Could cause complete application failure through config deletion

**Affected Versions:** InvoicePlane v1.7.0, v1.7.1  
**Recommended Action:** **UPGRADE IMMEDIATELY** and audit systems for suspicious file deletions

See `SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md` for full details, proof of concept, and verification procedures.

---

**CRITICAL: Fixed Remote Code Execution (RCE) vulnerability in template system - CVSSv3 9.9**

This is a patch bypass of the v1.7.1 LFI fix. The vulnerability allowed authenticated administrators to achieve Remote Code Execution as any unauthenticated visitor.

**Root Cause:**
The v1.7.1 template validation system used `directory_map()` to dynamically construct the whitelist at runtime. Because template directories were writable by the web server, attackers could:
1. Write a PHP webshell to the templates directory
2. Have it automatically added to the "trusted" whitelist on next scan
3. Set it as the active template via admin settings
4. Trigger execution by any unauthenticated visitor accessing a public invoice URL

**Fix Implementation:**
- **Replaced dynamic whitelist with static hardcoded constants** (CWE-693 fix)
  - Template names are now defined in code constants, NEVER scanned from filesystem
  - Even if attacker writes evil.php to templates directory, it will NOT be in the whitelist
  - Primary defense against RCE attacks
  
- **Enhanced validation with 7 security layers** (defense-in-depth)
  1. Empty/non-string value rejection
  2. Path traversal detection using `validate_safe_filename()`
  3. Type parameter validation (invoice/quote only)
  4. Scope parameter validation (pdf/public only)
  5. Static whitelist validation (CRITICAL - blocks any file not in hardcoded list)
  6. Character validation (alphanumeric, spaces, hyphens, underscores only)
  7. Comprehensive security logging
  
- **Added file existence verification before template inclusion** (CWE-98 mitigation)
  - Verifies template file exists before including it
  - Falls back to default template if configured template is invalid
  - Shows error if even default template is missing
  - Prevents arbitrary file inclusion even if whitelist is somehow bypassed
  
- **Added template directory permission checking** (CWE-732 monitoring)
  - New `check_template_directory_permissions()` method in Mdl_templates
  - Detects if template directories are writable by web server
  - Allows administrators to audit and fix permission issues

**Files Changed:**
- `application/modules/invoices/models/Mdl_templates.php` - Static whitelist implementation
- `application/helpers/template_helper.php` - Enhanced multi-layer validation
- `application/modules/guest/controllers/View.php` - File existence verification

**Documentation:**
- `SECURITY_ADVISORY_RCE_FIX.md` - Complete vulnerability and fix details
- `MIGRATION_GUIDE_v1.7.2.md` - Upgrade and security hardening guide

**Impact:**
- **Before:** Attacker with admin access could execute arbitrary PHP code
- **After:** Only templates explicitly defined in code constants can be loaded
- **Scope:** CRITICAL - Allows escape from web application to operating system

**Affected Versions:** InvoicePlane v1.7.0, v1.7.1  
**Recommended Action:** **UPGRADE IMMEDIATELY** and audit template directories for malicious files

See `SECURITY_ADVISORY_RCE_FIX.md` for full details and verification procedures.

---

**HIGH: Fixed Open Redirect vulnerabilities - CVSSv3 6.1 (CWE-601)**

Multiple locations used unvalidated `HTTP_REFERER` headers for redirects, allowing attackers to redirect users to malicious external sites for phishing attacks.

**Vulnerable Locations:**
1. `application/modules/payments/views/modal_add_payment.php` - Payment completion redirect
2. `application/modules/custom_fields/controllers/Custom_fields.php` - Custom field deletion redirect
3. `application/modules/filter/controllers/Ajax.php` - Multiple AJAX filter operations (3 locations)

**Fix Implementation:**
- Created `application/helpers/security_helper.php` with comprehensive URL validation functions
- Implemented `get_safe_referer()` function that validates URLs belong to application domain
- All redirect URLs now validated to be internal-only
- External URLs explicitly blocked and replaced with safe defaults
- Added security logging for suspicious redirect attempts

**New Security Functions:**
- `get_safe_referer($referer, $default_url)` - Validates referer URLs are internal
- `validate_redirect_url($url, $default_url)` - Validates redirect parameters
- `escape_url_for_output($url)` - Escapes URLs for HTML context
- `escape_url_for_javascript($url)` - Escapes URLs for JavaScript context
- `user_has_invoice_access($invoice_id)` - Prevents IDOR vulnerabilities
- `user_has_quote_access($quote_id)` - Prevents IDOR vulnerabilities
- `verify_csrf_token()` - CSRF token validation

**Impact:**
- **Before:** Users could be redirected to attacker-controlled phishing sites
- **After:** Only internal URLs allowed for redirects
- **Attack Prevention:** Blocks phishing, credential theft, and social engineering attacks

---

**MEDIUM: Hardened SQL query construction in guest payments - CVSSv3 6.5 (CWE-89)**

Guest payment queries used string concatenation with `implode()` which, while currently safe, set a dangerous pattern that could become vulnerable if data source changes.

**File:** `application/modules/guest/controllers/Payments.php`

**Fix Implementation:**
- Added explicit integer type casting using `array_map('intval', ...)`
- Added check for empty client list
- Added security documentation explaining the hardening
- Prevents potential SQL injection if data source ever changes

**Impact:**
- **Before:** Fragile pattern that relied on database-sourced data being safe
- **After:** Explicit sanitization ensures safety regardless of data source

---

**MEDIUM: Validated HTTP_REFERER usage in AJAX filters - CVSSv3 5.3 (CWE-20)**

AJAX filter controllers extracted values from `HTTP_REFERER` without proper validation.

**File:** `application/modules/filter/controllers/Ajax.php` (3 locations)

**Fix Implementation:**
- Added regex validation for table names (alphanumeric and underscores only)
- Added explicit integer casting for numeric ID values
- Set safe defaults when validation fails
- Prevents injection of malicious values into database queries

**Documentation:**
- `ADDITIONAL_SECURITY_FIXES_v1.7.2.md` - Complete details of additional vulnerabilities and fixes

**Summary of Security Improvements:**
- RCE vulnerability completely eliminated via static whitelist
- Open redirect attacks prevented via URL validation
- SQL query construction hardened
- HTTP_REFERER usage secured across application
- Comprehensive security helper library added
- Defense-in-depth approach implemented
- Extensive security documentation provided
- PHPMailer SMTP debug output sanitized (log injection prevention)
- Email send failures now correctly propagated to callers
- Cryptographic binary data handling corrected
- GitHub Actions workflow token permissions restricted

---

**MEDIUM: Sanitized PHPMailer SMTP debug output - log injection prevention (CWE-117)**

SMTP debug strings (server banners, addresses, status responses) were logged verbatim.
Because these strings can contain user-controlled data (e.g. SMTP `MAIL FROM` responses),
they could be used to inject false log entries or control characters.

**File:** `application/modules/mailer/helpers/phpmailer_helper.php`

**Fix:** The `Debugoutput` callback now uses the existing `phpmailer_debug_output()` helper,
which passes all strings through `sanitize_for_logging()` before writing to the CI log.

---

**MEDIUM: Fixed phpmail_send() always returning true on failure (CWE-252)**

`phpmail_send()` always returned `true` regardless of whether the underlying `$mail->send()`
call succeeded. This masked delivery failures from callers and caused the "Email sent
successfully" log line to appear inside the failure branch.

**File:** `application/modules/mailer/helpers/phpmailer_helper.php`

**Fix:** Function now returns the actual `bool` result of `$mail->send()`. The success log
message was moved to the success branch and the failure branch sets flash error data only.

> **Breaking change for custom integrations:** Any code that called `phpmail_send()` and
> treated the return value as always-truthy must be updated. Check the return value and handle
> `false` as a send failure.

---

**LOW: Fixed binary data corruption in Cryptor::decryptString() (CWE-704)**

`mb_strlen()` and `mb_substr()` were used to split raw binary ciphertext (IV + encrypted data).
Under multibyte internal encodings these functions operate on characters, not bytes, which can
corrupt the extracted IV and cause decryption failures or weaken integrity checks.

**File:** `application/libraries/Cryptor.php`

**Fix:** Replaced `mb_strlen()` / `mb_substr()` with the byte-safe `strlen()` / `substr()`.

---

**LOW: Restricted GitHub Actions GITHUB_TOKEN permissions (CWE-272)**

The `phpunit.yml`, `quickstart.yml`, and `setup.yml` workflows did not declare explicit
`permissions` blocks, granting the default broad token scope to all workflow jobs.

**Fix:** Added `permissions: contents: read` at the workflow level in all three files.

---

**Verification:**
Run `php verify_rce_fix.php` to validate all security fixes are in place.

### Added

- **Custom template discovery via `CUSTOM_TEMPLATES_FOLDER`**: When the
  `CUSTOM_TEMPLATES_FOLDER` environment variable is configured, templates placed in that
  directory are now discovered and listed alongside the built-in templates in the admin
  template selectors. Custom template file names are validated against a strict allowlist
  (alphanumeric characters, spaces, hyphens, and underscores only) before being included.
  The application's own built-in template directories are never scanned (RCE fix preserved).
  See `ipconfig.php.example` for configuration details.

### Changed

- **Email template preview** (`application/modules/email_templates/views/`): The live preview
  panel now displays the raw template source as plain text instead of rendering it as HTML.
  This eliminates a DOM-based XSS risk introduced when user-controlled template content was
  set as `innerHTML` without sufficient sanitization. Existing templates are unaffected;
  only the in-browser preview behavior changes.

- **`phpmail_send()` return value**: This function now returns `false` when the underlying
  email delivery fails (previously it always returned `true`). Any code that relied on the
  return value must be updated to handle `false` as a failure signal.



### Added
- Full PHP 8.2+ compatibility support (PHP 8.1, 8.2, 8.3+)
- Enhanced security logging for file uploads and template operations
- Comprehensive input validation for template parameters
- Security warnings in admin interface for SVG logo files
- Optional EXIF metadata stripping from uploaded images (disabled by default)
  - Configurable via `SEC_STRIP_EXIF_FROM_IMAGES` setting in `ipconfig.php`
  - Removes GPS coordinates, timestamps, camera info, and device information
  - Supports JPEG, PNG, GIF, and WEBP formats
  - Preserves image quality while protecting user privacy

### Changed
- Updated all PHP dependencies for PHP 8.2+ compatibility
- Improved error handling in PDF generation
- Enhanced input sanitization across all user-facing forms
- Modernized codebase to follow PHP 8+ standards

### Security

**CRITICAL: Fixed Local File Inclusion (LFI) vulnerabilities (#1433)**
- Template validation added to PDF generation endpoints
- Invoice and quote template parameters now validated before use
- Prevented directory traversal attacks through template selection
- Added security logging for template operations
- Validates invoice_template and quote_template URL parameters

**CRITICAL: Fixed Cross-Site Scripting (XSS) vulnerabilities**
- Quote and invoice number fields now properly escaped in all templates
- Tax rate names sanitized and escaped
- Payment method names sanitized and escaped
- Custom field labels protected from XSS attacks
- Client addresses sanitized for display
- Sumex observations field sanitized
- Quote notes and passwords properly escaped
- Email templates now use proper HTML escaping
- All user-facing input fields validated and sanitized

**HIGH: Fixed log poisoning vulnerability in file upload controller**
- File names are now sanitized before logging
- Prevents control character injection in log files
- Protects against log manipulation attacks

**HIGH: SVG logo files are now blocked entirely**
- SVG files can contain embedded JavaScript that could execute in user browsers
- Existing SVG logos will not display (security block)
- Users should convert to PNG, JPG, or GIF formats
- Security warning displayed in admin interface when SVG detected

**File access vulnerabilities fixed (#1383)**
- Added comprehensive file access validation across all controllers
- Prevents unauthorized file access through direct URL manipulation

### Fixed Issues

- #1433 - Local File Inclusion (LFI) vulnerabilities in PDF template handling (Post-v1.7.0 tag)
- #1388 - Unsafe jQuery plugin vulnerability (Code scanning alert #8)
- #1387 - Unsafe jQuery plugin vulnerability (Code scanning alert #8, duplicate)
- #1389 - Workflow does not contain permissions (Code scanning alert #5)
- #1383 - File access vulnerabilities across all controllers
- #1381 - Version checking and logging for client_einvoicing fields
- #1380 - Dependency update: Bump qs from 6.14.0 to 6.14.1
- #1377 - QR code image width reduced to 100px for better display
- #1375 - Email address verification now supports both comma and semicolon separators
- #1373 - Removed deprecated library dependencies
- #1367 - Various bug fixes
- #1368 - Various bug fixes
- Multiple code scanning alerts for workflow permissions (#11, #12, #13, #14, #15)
- Code scanning alert #10 - Unsafe jQuery plugin
- Code scanning alert #9 - Incomplete string escaping or encoding
- Code scanning alert #7 - DOM text reinterpreted as HTML
- Code scanning alert #6 - Workflow does not contain permissions

### Removed
- Support for SVG logo uploads (security measure)
- Deprecated library dependencies
- PHP 7.x compatibility (minimum PHP 8.1 required)

### Fields Sanitized for XSS Protection

The following fields have been sanitized and properly escaped to prevent XSS attacks:
- `invoice_number` - Escaped in all templates and views
- `quote_number` - Escaped in all templates and views
- `tax_rate_name` - Sanitized on input, escaped on output
- `payment_method_name` - Sanitized on input, escaped on output
- `custom_field_label` - Protected in all custom field displays
- Client address fields - Sanitized for safe display
- `sumex_observations` - Sanitized on input
- `quote_password` - Sanitized on input
- `quote_notes` - Sanitized on input
- Email template content - Proper HTML escaping applied
- File names in upload operations - Sanitized before logging

## [1.6.4] - Earlier Release

For changes in version 1.6.4 and earlier, please see the git commit history.

---

## Security Disclosure

If you discover a security vulnerability in InvoicePlane, please email **[mail@invoiceplane.com](mailto:mail@invoiceplane.com)** before disclosing it publicly. We will address all security concerns promptly.
