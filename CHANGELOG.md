# Changelog

All notable changes to InvoicePlane will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
