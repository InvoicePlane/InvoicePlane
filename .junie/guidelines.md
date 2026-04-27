# InvoicePlane Development Guidelines

This document defines the security principles, coding standards, and development practices for InvoicePlane.

## Table of Contents

1. [Security Principles](#security-principles)
2. [DRY Principle](#dry-principle)
3. [Input Validation and Sanitization](#input-validation-and-sanitization)
4. [Output Encoding](#output-encoding)
5. [File Security](#file-security)
6. [Logging Best Practices](#logging-best-practices)
7. [Testing Requirements](#testing-requirements)
8. [Code Review Checklist](#code-review-checklist)

---

## Security Principles

### Defense in Depth

InvoicePlane uses multiple independent security layers. No single layer is trusted exclusively.

1. **Input sanitization** — clean all user input at the controller level.
2. **Output encoding** — escape data when rendering in views.
3. **Validation** — enforce format, type, and business rules.
4. **Access control** — verify user permissions at each layer.
5. **Secure defaults** — fail safely; allow-list rather than block-list.

```php
// Global XSS sanitization for all POST fields (Admin_Controller)
protected function filter_input(): void
{
    foreach ($this->input->post() as $key => $value) {
        $cleaned = $this->security->xss_clean($value);
        $cleaned = strip_tags($cleaned);
        $_POST[$key] = $cleaned;
    }
}
```

### Vulnerability Categories Addressed

| Category | Mitigation |
|----------|-----------|
| XSS | Sanitize input; encode output |
| LFI / Path Traversal | Validate file paths; use hardcoded whitelists |
| Log Injection | Sanitize data before logging |
| Header Injection | Sanitize filenames in HTTP headers |
| SVG XSS | Block SVG uploads entirely |
| Open Redirect | Validate referer URLs with `get_safe_referer()` |
| RCE via template | Static `ALLOWED_*_TEMPLATES` constants; never scan filesystem |

---

## DRY Principle

Every piece of knowledge must have a single, authoritative representation.

### When to Extract a Helper Function

Extract logic into a reusable helper when:

- The same logic appears in three or more places.
- The logic addresses a security concern (sanitization, validation).
- The logic is complex enough to benefit from isolated testing.
- The logic may need to change in the future.

### Example: Logging Sanitization

```php
// Bad — inconsistent, duplicated
log_message('warning', 'Upload blocked: ' . $_FILES['logo']['name']);
log_message('error', 'Invalid template: ' . $template_name);

// Good — single source of truth
log_message('warning', 'Upload blocked: ' . sanitize_for_logging(basename($_FILES['logo']['name'])));
log_message('error', 'Invalid template: ' . sanitize_for_logging($template_name));
```

### Helper File Conventions

| File | Responsibility |
|------|---------------|
| `file_security_helper.php` | File validation, path safety, log sanitization |
| `security_helper.php` | URL validation, CSRF, access checks |
| `template_helper.php` | Template name validation |
| `pdf_helper.php` | PDF generation utilities |
| `invoice_helper.php` | Invoice-specific rendering logic |

---

## Input Validation and Sanitization

### Global Sanitization

`Admin_Controller::filter_input()` sanitizes all POST data automatically. This baseline covers the majority of user input.

### Additional Validation

Use explicit regex validation for:

- Format rules (invoice number patterns, tax codes).
- Business rules (allowed character sets, length limits).
- Type safety (numeric IDs, dates, email addresses).

```php
// Invoice number format
if ( ! preg_match('/^[A-Z0-9-]+$/i', $invoice_number)) {
    $this->session->set_flashdata('alert_error', 'Invalid invoice number format.');
    redirect('invoices/view/' . $invoice_id);
}

// Integer ID
$id = (int) $this->input->get('id');

// Email
if ( ! filter_var($email, FILTER_VALIDATE_EMAIL)) { ... }
```

### Bypass Fields

Certain fields must bypass XSS sanitization and require special output handling:

```php
$bypass_fields = [
    'user_password',
    'user_passwordv',
    'invoice_password',
    'quote_password',
    'email_template_body',
];
```

---

## Output Encoding

Never assume that input sanitization is sufficient. Always encode output in the appropriate context.

```php
// HTML context
<?php echo html_escape($value); ?>

// JavaScript context
<script>
var data = <?php echo json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP); ?>;
</script>

// URL parameter
<a href="<?php echo site_url('invoices/view/' . urlencode($invoice_id)); ?>">Link</a>

// HTML attribute
<input type="text" value="<?php echo html_escape($value); ?>">
```

---

## File Security

### Path Traversal Prevention

```php
$validation = validate_safe_filename($filename);
if ( ! $validation['valid']) {
    log_message('error', 'Invalid filename (hash: ' . $validation['hash'] . ')');
    show_error('Invalid filename.');
}

if ( ! validate_file_in_directory($fullPath, $baseDirectory)) {
    log_message('error', 'Path traversal attempt detected.');
    show_error('Access denied.');
}
```

### File Upload Security

1. Validate file extensions against an explicit allow-list.
2. Block dangerous types (SVG, PHP, executable files).
3. Sanitize filenames before storage.
4. Set restrictive directory permissions.
5. Log all upload attempts using hashed filenames.

```php
$allowed    = ['png', 'jpg', 'jpeg', 'gif', 'pdf'];
$extension  = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));

if ( ! in_array($extension, $allowed, true)) {
    log_message('warning', 'Blocked upload: ' . sanitize_for_logging(basename($_FILES['upload']['name'])));
    show_error('File type not allowed.');
}
```

### Template Security

Template names are controlled by hardcoded constants in `Mdl_Templates`. The filesystem is **never** scanned to build the allowed list.

```php
private const ALLOWED_INVOICE_TEMPLATES = [
    'pdf'    => ['InvoicePlane', 'InvoicePlane - paid', 'InvoicePlane - overdue'],
    'public' => ['InvoicePlane_Web'],
];
```

---

## Logging Best Practices

### Prevent Log Injection

Never log untrusted data without sanitization.

```php
// Bad — attacker can inject fake log lines
log_message('error', 'Failed login: ' . $_POST['username']);

// Good
log_message('error', 'Failed login: ' . sanitize_for_logging($_POST['username']));
```

### Hash Sensitive Values

For sensitive data such as filenames, log the hash rather than the raw value.

```php
$hash = hash('sha256', $filename);
log_message('error', 'Invalid file access (hash: ' . $hash . ')');
```

### Structured Context

For complex events, use structured JSON log entries.

```php
$context = [
    'timestamp'  => date('Y-m-d H:i:s'),
    'user_id'    => $this->session->userdata('user_id'),
    'uri'        => uri_string(),
    'ip_address' => $this->input->ip_address(),
];
log_message('error', 'Security event: ' . json_encode($context, JSON_PARTIAL_OUTPUT_ON_ERROR));
```

---

## Testing Requirements

### PHPUnit

- Use plain PHPUnit (no Laravel TestCase).
- Method names: `it_<snake_case>`.
- Annotate with `#[Test]`.
- Follow Arrange / Act / Assert.

```php
#[Test]
public function it_sanitizes_log_injection_attempts(): void
{
    // Arrange
    $malicious = "admin\nFAKE LOG ENTRY";

    // Act
    $result = sanitize_for_logging($malicious);

    // Assert
    $this->assertStringNotContainsString("\n", $result);
}

#[Test]
public function it_blocks_path_traversal(): void
{
    $validation = validate_safe_filename('../../../etc/passwd');

    $this->assertFalse($validation['valid']);
    $this->assertSame('path_traversal', $validation['error']);
}
```

### Coverage Expectations

- All security-critical helper functions must have unit tests.
- Edge cases (empty input, null, boundary values) must be tested.
- Tests must not depend on external services or a live database unless explicitly marked as integration tests.

---

## Code Review Checklist

### Security

- [ ] All user input is sanitized (or the bypass is explicitly justified).
- [ ] All output is encoded in the correct context.
- [ ] File paths are validated with `validate_safe_filename()` / `validate_file_in_directory()`.
- [ ] Log messages are sanitized with `sanitize_for_logging()`.
- [ ] SQL queries use Active Record / parameterized statements; no string concatenation with user data.
- [ ] File uploads validate extension and MIME type.
- [ ] Redirects use `get_safe_referer()` or a validated internal URL.

### Code Quality

- [ ] No duplicated logic (DRY principle applied).
- [ ] Helper functions used for shared operations.
- [ ] Single responsibility per function / method.
- [ ] Complex logic is commented.
- [ ] Error handling is consistent with the rest of the module.
- [ ] Tests cover the critical paths.

### CodeIgniter / InvoicePlane Specific

- [ ] Views use `html_escape()` or `htmlsc()` for all user-controlled output.
- [ ] Helpers are loaded explicitly with `$this->load->helper()`.
- [ ] No direct use of `$_GET`, `$_POST`, or `$_SERVER` — use `$this->input->*()`.
- [ ] No `php artisan` commands — InvoicePlane does not use Laravel.


## Test Quality Guardrails

- Never delete or empty meaningful test bodies just to make a suite pass. Preserve original intent and migrate in small, behavior-preserving steps.
- Weak tests are prohibited: every test must assert expected behavior, not merely absence of fatal errors.
- Do not use `assertResponseHasNoPhpErrors()` as the main assertion. Pair/replace it with concrete behavior checks (status code, redirect destination, expected view/output, database/session effects).


## Test quality policy reference

- Follow `.junie/test-quality.md` for production-grade test quality rules and enforcement.
- Weak tests are prohibited and must be refactored as soon as identified.
