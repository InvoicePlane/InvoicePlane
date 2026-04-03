# XSS Vulnerability Fix - Email Templates

## Overview
This document describes the fix for a critical Stored Cross-Site Scripting (XSS) vulnerability in the Email Templates module of InvoicePlane.

## Vulnerability Description
The vulnerability existed because InvoicePlane failed to properly sanitize HTML content in email templates, allowing attackers to:
1. Store malicious JavaScript in email template bodies
2. Execute arbitrary code when administrators view/edit templates
3. Steal session cookies and perform account takeover attacks

## Root Causes
1. **Input Bypass**: `email_template_body` field was in the bypass list in `Admin_Controller::filter_input()`
2. **Output Encoding**: Form rendering didn't properly escape the textarea content
3. **Dangerous Decoding**: `htmlspecialchars_decode()` was used in email sending logic, reversing any encoding

## Security Fix Implementation

### 1. HTML Purifier Integration
Added `ezyang/htmlpurifier` library to sanitize HTML content while preserving safe formatting tags.

**File**: `application/helpers/html_sanitizer_helper.php`
- `sanitize_email_template_html()`: Sanitizes HTML using a whitelist approach
- Only allows safe tags: p, br, strong, em, h1-h4, etc.
- Removes all JavaScript, event handlers, and dangerous attributes
- Blocks external resources (SSRF prevention)

### 2. Input Sanitization
**File**: `application/core/Admin_Controller.php`
- Removed `email_template_body` from bypass list
- Added to `$html_fields` array for special HTML sanitization
- Added `body` field (for invoice/quote email sending) to HTML sanitization
- All HTML content now goes through HTML Purifier before storage

### 3. Output Encoding
**File**: `application/modules/email_templates/views/form.php`
- Changed textarea output to use explicit `htmlspecialchars()` with `ENT_QUOTES`
- Prevents stored XSS when editing templates
- Context-appropriate escaping for form fields

### 4. Remove Dangerous Decoding
**Files**: 
- `application/modules/invoices/controllers/Cron.php`
- `application/modules/mailer/controllers/Mailer.php`

Removed all `htmlspecialchars_decode()` calls that were reversing XSS protection:
- Content is already sanitized by HTML Purifier
- Only add `nl2br()` for plain text content (no HTML tags)
- Sanitized HTML is used directly in emails

### 5. HttpOnly Cookie Flag
**File**: `application/config/config.php`
- Changed `$config['cookie_httponly']` from `false` to `true`
- Prevents JavaScript from accessing `document.cookie`
- Defense-in-depth measure against session theft

## Security Testing

### Test Cases Covered
1. ✅ Script tag injection: `<script>alert(document.cookie)</script>`
2. ✅ Event handler injection: `<img src=x onerror=alert(1)>`
3. ✅ Textarea context breakout: `</textarea><script>...`
4. ✅ JavaScript protocol: `<a href="javascript:alert(1)">`
5. ✅ Safe HTML preservation: `<p><strong>Invoice</strong></p>`

### Defense-in-Depth Layers
1. **Input Layer**: HTML Purifier sanitization during form submission
2. **Storage Layer**: Only sanitized content stored in database
3. **Output Layer**: Context-appropriate encoding in views
4. **Session Layer**: HttpOnly flag prevents cookie theft
5. **Logging Layer**: XSS attempts are logged with context

## Breaking Changes
**None**. The fix maintains backward compatibility:
- Existing safe HTML content remains unchanged
- Malicious content is sanitized (expected behavior)
- Email template functionality preserved

## Migration Notes
After deploying this fix:
1. Existing email templates with malicious content will be sanitized on next edit
2. No database migration required
3. Admin users should review and test email templates

## Security Recommendations
1. ✅ Keep HTML Purifier updated
2. ✅ Regularly review email templates for unexpected content
3. ✅ Monitor logs for XSS detection alerts
4. ✅ Use HTTPS to prevent cookie interception (cookie_secure)
5. ⚠️ Consider Content Security Policy (CSP) headers

## Files Modified
- `composer.json` - Added HTML Purifier dependency
- `application/helpers/html_sanitizer_helper.php` - New helper file
- `application/core/Admin_Controller.php` - Updated input filtering
- `application/modules/email_templates/views/form.php` - Fixed output encoding
- `application/modules/invoices/controllers/Cron.php` - Removed dangerous decoding
- `application/modules/mailer/controllers/Mailer.php` - Removed dangerous decoding
- `application/config/config.php` - Enabled HttpOnly cookies

## References
- OWASP XSS Prevention Cheat Sheet
- HTML Purifier Documentation: http://htmlpurifier.org/
- CVE Research: Stored XSS vulnerabilities
