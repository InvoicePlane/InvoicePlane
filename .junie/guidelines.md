# InvoicePlane Development Guidelines

This document outlines the security principles, code quality standards, and best practices for InvoicePlane development, with a focus on the lessons learned from security vulnerabilities and refactoring efforts.

## Table of Contents

1. [Security Principles](#security-principles)
2. [DRY Programming](#dry-programming)
3. [Input Validation and Sanitization](#input-validation-and-sanitization)
4. [Output Encoding](#output-encoding)
5. [File Security](#file-security)
6. [Logging Best Practices](#logging-best-practices)
7. [Testing Requirements](#testing-requirements)
8. [Code Review Checklist](#code-review-checklist)

---

## Security Principles

### Defense in Depth

InvoicePlane follows a **defense-in-depth** security approach with multiple layers of protection:

1. **Input Sanitization** - Clean all user input at the controller level
2. **Output Encoding** - Escape data when rendering in views
3. **Validation** - Validate format, type, and business rules
4. **Access Control** - Verify user permissions at each layer
5. **Secure Defaults** - Use safe defaults and fail securely

**Example from Admin_Controller:**
```php
// Layer 1: Global XSS sanitization for all POST fields
protected function filter_input(): void
{
    $input = $this->input->post();
    foreach ($input as $key => $value) {
        $cleaned_value = $this->security->xss_clean($value);
        $cleaned_value = strip_tags($cleaned_value);
        $_POST[$key] = $cleaned_value;
    }
}

// Layer 2: Additional validation in controllers
if (!preg_match('/^[A-Z0-9-]+$/', $invoice_number)) {
    // Reject invalid format
}

// Layer 3: Output encoding in views
<?php echo html_escape($invoice_number); ?>
```

### Security Vulnerability Categories

InvoicePlane has addressed the following vulnerability types:

1. **XSS (Cross-Site Scripting)** - Sanitize input, encode output
2. **LFI (Local File Inclusion)** - Validate file paths, use whitelists
3. **Path Traversal** - Check for `../` sequences, validate resolved paths
4. **Log Injection** - Sanitize data before logging
5. **Header Injection** - Sanitize filenames in HTTP headers
6. **SVG XSS** - Block SVG uploads entirely

---

## DRY Programming

### The DRY Principle

**Key Rule:** Every piece of knowledge should have a single, unambiguous, authoritative representation within the system.

### When to Extract a Helper Function

Extract code into a reusable helper when:

1. **The same logic appears 3+ times** across the codebase
2. **The logic is complex** and would benefit from isolated testing
3. **The logic addresses a specific security concern** (e.g., sanitization)
4. **The logic might need to change** in the future (centralize changes)

### Example: Sanitize for Logging

**Before (code duplication):**
```php
// In Settings.php
$safe_filename = preg_replace('/[[:^print:]]/', '', $_FILES['logo']['name']);
log_message('warning', 'Upload blocked: ' . $safe_filename);

// In pdf_helper.php
$safe_template = preg_replace('/[[:^print:]]/', '', $template_name);
log_message('error', 'Invalid template: ' . $safe_template);

// In Upload_Controller.php
$safe_name = str_replace(["\r", "\n"], '', $upload_name);
log_message('info', 'Processing: ' . $safe_name);
```

**After (DRY with helper function):**
```php
// In file_security_helper.php
function sanitize_for_logging(string $value): string
{
    // Single source of truth for log sanitization
    return str_replace(["\r", "\n"], '', $value);
}

// Usage everywhere
log_message('warning', 'Upload blocked: ' . sanitize_for_logging(basename($_FILES['logo']['name'])));
log_message('error', 'Invalid template: ' . sanitize_for_logging($template_name));
log_message('info', 'Processing: ' . sanitize_for_logging($upload_name));
```

**Benefits:**
- Single point of maintenance
- Consistent security implementation
- Easier to test and verify
- Clear intent through naming

### Helper Organization

Place helper functions in appropriate files:

- `file_security_helper.php` - File access, path validation, logging sanitization
- `pdf_helper.php` - PDF generation utilities
- `invoice_helper.php` - Invoice-specific logic
- `date_helper.php` - Date formatting and calculations

---

## Input Validation and Sanitization

### Global Input Sanitization

**All POST data** is automatically sanitized by `Admin_Controller::filter_input()`:

```php
protected function filter_input(): void
{
    foreach ($input as $key => $value) {
        // Apply XSS cleaning and strip dangerous tags
        $cleaned_value = $this->security->xss_clean($value);
        $cleaned_value = strip_tags($cleaned_value);
        $_POST[$key] = $cleaned_value;
    }
}
```

**Important:** This provides baseline protection for all 500+ POST fields.

### When Additional Validation is Needed

Use **additional regex validation** for:

1. **Format enforcement** (invoice numbers, tax codes, etc.)
2. **Business rules** (allowed characters, length limits)
3. **Type safety** (numeric IDs, dates, emails)

**NOT for XSS protection** - that's already handled globally.

### Bypass Fields

Certain fields must bypass XSS sanitization:

```php
$bypass_fields = [
    'user_password',        // Passwords need special characters
    'user_passwordv',       // Password verification
    'invoice_password',     // PDF password protection
    'quote_password',       // PDF password protection
    'email_template_body',  // HTML templates
];
```

**Warning:** Bypass fields require special handling and output encoding.

### Validation Examples

```php
// Invoice number format validation
if (!preg_match('/^[A-Z0-9-]+$/i', $invoice_number)) {
    $this->session->set_flashdata('alert_error', 'Invalid invoice number format');
    redirect('invoices/view/' . $invoice_id);
}

// Tax rate code validation
if (!preg_match('/^[A-Z0-9_-]+$/i', $tax_rate_code)) {
    // Reject invalid characters
}

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Invalid email format
}
```

---

## Output Encoding

### Always Encode Output

**Rule:** Never trust that input sanitization is enough. Always encode output.

```php
<!-- In views -->
<h1><?php echo html_escape($invoice_number); ?></h1>
<div><?php echo html_escape($client_name); ?></div>
<textarea><?php echo html_escape($notes); ?></textarea>
```

### Context-Specific Encoding

Different contexts require different encoding:

```php
// HTML context
<?php echo html_escape($value); ?>

// JavaScript context
<script>
var data = <?php echo json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP); ?>;
</script>

// URL context (validate base URL and encode parameters)
<?php
$base_url = site_url('invoices/view'); // Safe base URL
$query_param = urlencode($invoice_id);
?>
<a href="<?php echo $base_url . '/' . $query_param; ?>">Link</a>

// HTML attribute context
<input type="text" value="<?php echo html_escape($value); ?>">
```

---

## File Security

### Path Traversal Prevention

**Always validate file paths** to prevent directory traversal attacks:

```php
// Use helper functions from file_security_helper.php
$validation = validate_safe_filename($filename);
if (!$validation['valid']) {
    log_message('error', 'Invalid filename (hash: ' . $validation['hash'] . ')');
    show_error('Invalid filename');
}

// Validate resolved path is within allowed directory
$fullPath = $baseDirectory . '/' . basename($filename);
if (!validate_file_in_directory($fullPath, $baseDirectory)) {
    log_message('error', 'Path traversal attempt detected');
    show_error('Access denied');
}
```

### File Upload Security

1. **Validate file extensions** against a whitelist
2. **Block dangerous file types** (SVG, PHP, executable files)
3. **Sanitize filenames** before storage
4. **Use secure directory permissions**
5. **Log upload attempts** with hashed filenames

```php
// Check file extension
$extension = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));
$allowed = ['png', 'jpg', 'jpeg', 'gif', 'pdf'];

if (!in_array($extension, $allowed, true)) {
    log_message('warning', 'Blocked upload: ' . sanitize_for_logging(basename($_FILES['upload']['name'])));
    show_error('File type not allowed');
}
```

### Template Validation

For LFI prevention, validate template names:

```php
function validate_template_name(?string $template, string $type, string $format): string|false
{
    // Whitelist of allowed templates
    $allowed_templates = ['InvoicePlane', 'Modern', 'Classic'];
    
    if ($template === null || !in_array($template, $allowed_templates, true)) {
        return false;
    }
    
    // Validate template file exists
    $template_path = APPPATH . "views/{$type}_templates/{$format}/{$template}.php";
    if (!file_exists($template_path)) {
        return false;
    }
    
    return $template;
}
```

---

## Logging Best Practices

### Log Injection Prevention

**Never log untrusted data directly** - it can inject fake log entries:

```php
// WRONG - Vulnerable to log injection
log_message('error', 'Failed login: ' . $_POST['username']);
// Attacker input: "admin\nSUCCESS: Admin logged in"
// Creates fake log entry

// CORRECT - Sanitize before logging
log_message('error', 'Failed login: ' . sanitize_for_logging($_POST['username']));
```

### Use Hashes for Sensitive Data

For filenames and sensitive data, log **hashes instead of raw values**:

```php
// Log the hash, not the actual filename
$hash = hash('sha256', $filename);
log_message('error', 'Invalid file access (hash: ' . $hash . ')');
```

### Structured Logging

Use structured data for complex log entries:

```php
$log_context = [
    'timestamp' => date('Y-m-d H:i:s'),
    'user_id' => $this->session->userdata('user_id'),
    'uri' => uri_string(),
    'ip_address' => $this->input->ip_address(),
    'fields' => $xss_log_entries,
];

$log_payload = json_encode($log_context, JSON_PARTIAL_OUTPUT_ON_ERROR);
log_message('error', 'XSS attempt detected: ' . $log_payload);
```

---

## Testing Requirements

### Security Testing

All security-critical functions must have tests:

```php
#[Test]
public function it_sanitizes_log_injection_attempts(): void
{
    $malicious = "test\nFAKE LOG ENTRY";
    $result = sanitize_for_logging($malicious);
    
    $this->assertEquals('testFAKE LOG ENTRY', $result);
    $this->assertStringNotContainsString("\n", $result);
}

#[Test]
public function it_blocks_path_traversal(): void
{
    $validation = validate_safe_filename('../../../etc/passwd');
    
    $this->assertFalse($validation['valid']);
    $this->assertEquals('path_traversal', $validation['error']);
}
```

### Test Naming Convention

- Use `it_` prefix for all test methods
- Use snake_case for test method names
- Make test names read like sentences
- Annotate with `#[Test]` attribute

```php
#[Test]
public function it_validates_invoice_number_format(): void
{
    // Arrange, Act, Assert
}
```

---

## Code Review Checklist

### Security Review

- [ ] All user input is sanitized (or explain bypass)
- [ ] All output is encoded (context-appropriate)
- [ ] File paths are validated (no path traversal)
- [ ] Log messages are sanitized (no log injection)
- [ ] SQL queries use parameterized statements (no SQL injection)
- [ ] File uploads validate extensions and types
- [ ] Headers are sanitized (no header injection)
- [ ] Authentication/authorization checks are in place

### Code Quality Review

- [ ] No code duplication (DRY principle applied)
- [ ] Helper functions are used for common operations
- [ ] Functions have single responsibility
- [ ] Complex logic is commented
- [ ] Error handling is consistent
- [ ] Tests cover critical paths
- [ ] Documentation is updated

### Laravel/CodeIgniter Specific

- [ ] Using framework security features (xss_clean, html_escape)
- [ ] Following PSR-12 coding standards
- [ ] Using type hints where appropriate
- [ ] Avoiding deprecated functions
- [ ] Using environment variables for configuration

---

## Summary

Following these guidelines ensures:

1. **Security:** Multiple layers of defense against common vulnerabilities
2. **Maintainability:** DRY principle reduces code duplication
3. **Reliability:** Consistent patterns reduce bugs
4. **Clarity:** Clear intent through naming and organization

When in doubt, ask: "Is this secure? Is it DRY? Is it clear?"
