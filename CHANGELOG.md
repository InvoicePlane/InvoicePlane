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
- **CRITICAL:** Fixed multiple Cross-Site Scripting (XSS) vulnerabilities
  - Quote and invoice number fields now properly escaped in all templates
  - Tax rate names and payment method names sanitized
  - Custom field labels and client addresses protected from XSS
  - Sumex observations and quote notes/passwords sanitized
  - Email templates now use proper HTML escaping
- **CRITICAL:** Fixed Local File Inclusion (LFI) vulnerabilities
  - Template validation added to PDF generation endpoints
  - Invoice and quote template parameters now validated
  - Prevented directory traversal attacks through template selection
- **HIGH:** Fixed log poisoning vulnerability in file upload controller
  - File names are now sanitized before logging
  - Prevents control character injection in log files
- **HIGH:** SVG logo files are now blocked entirely
  - SVG files can contain embedded JavaScript that could execute in user browsers
  - Existing SVG logos will not display (security block)
  - Users should convert to PNG, JPG, or GIF formats

### Removed
- Support for SVG logo uploads (security measure)
- Deprecated library dependencies
- PHP 7.x compatibility (minimum PHP 8.1 required)

### Fixed
- Email address verification now supports both comma and semicolon separators
- QR code image width reduced to 100px for better display
- Version checking and logging for e-invoicing fields
- File access vulnerabilities across multiple controllers

## [1.6.4] - Earlier Release

For changes in version 1.6.4 and earlier, please see the git commit history.

---

## Security Disclosure

If you discover a security vulnerability in InvoicePlane, please email **[mail@invoiceplane.com](mailto:mail@invoiceplane.com)** before disclosing it publicly. We will address all security concerns promptly.
