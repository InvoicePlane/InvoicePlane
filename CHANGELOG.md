# Changelog

All notable changes to InvoicePlane will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.7.2] - 2026-04-06

### Security

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

## [1.7.0] - 2025-12-02

### Added
- Full PHP 8.2+ compatibility support (PHP 8.1, 8.2, 8.3+)
- Enhanced security logging for file uploads and template operations
- Comprehensive input validation for template parameters
- Security warnings in admin interface for SVG logo files

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
