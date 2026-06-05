# Changelog

All notable changes to InvoicePlane will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Security

- **CWE-1004:** `cookie_httponly` was `false` — session cookies are now inaccessible to JavaScript, closing the main XSS session-hijack vector.
- **CWE-1021:** `X-Frame-Options` now defaults to `SAMEORIGIN` without requiring an env var — clickjacking protection is active out of the box.
- **CWE-384:** `SESS_REGENERATE_DESTROY` now defaults to `true` — the old session token is destroyed on regeneration, preventing session fixation.
- **CWE-117 (log injection):** Password-reset token and user email are no longer logged verbatim; SHA-256 hashes are used instead.
- **CWE-601 (open redirect):** Raw `$_SERVER['HTTP_REFERER']` removed from `custom_fields/views/form.php` and `mailer_helper.php`; both now use `get_safe_referer()`.
- Added `Referrer-Policy: strict-origin-when-cross-origin` response header.

### Fixed

- Session config bug: `sess_table_name` and `sess_cookie_name` both read from `SESS_DRIVER` env var instead of dedicated `SESS_TABLE_NAME` / `SESS_COOKIE_NAME` vars.
- `<form method="post">` without a CSRF field or submit button in `not_configured.php` replaced with a plain `<div>`.

### Added

- `CLAUDE.md` — quick-start guide for AI coding agents working on this codebase.
- Session files stored in `storage/framework/sessions/` by default; configurable via `SESS_SAVE_PATH` (can be set to a path outside the document root).
- `SESS_SAVE_PATH`, `SESS_TABLE_NAME`, `SESS_COOKIE_NAME` env vars documented in `ipconfig.php.example`.
- Template allowlist format clarified: quote the value in `ipconfig.php` when names contain spaces or hyphens (e.g. `CUSTOM_INVOICE_TEMPLATES_PDF="Corporate - Modern,My Template"`).

---

## [1.7.2] - 2026-04-06

### Security

- **CRITICAL (CVSSv3 9.9) — Remote Code Execution via template system:** The template whitelist was built by scanning the filesystem at runtime with `directory_map()`. Because the template directories were writable by the web server, an attacker could write a PHP webshell, have it automatically added to the "trusted" list, and execute it as any unauthenticated visitor. Fixed by replacing the filesystem scan with static hardcoded constants. Custom templates must now be explicitly listed in `ipconfig.php` (see `CUSTOM_INVOICE_TEMPLATES_*` / `CUSTOM_QUOTE_TEMPLATES_*`).
- **CRITICAL — Broken authentication:** Password reset tokens had no expiry. Tokens now expire after a configurable window (`PASSWORD_RESET_TOKEN_EXPIRY_MINUTES`, default 15 minutes) and are cleared from the database on expiry.
- **HIGH (CVSSv3 7.1) — Arbitrary file deletion via path traversal (CWE-22):** An authenticated admin could delete any server file by storing a path traversal sequence as a logo filename. Logo filenames are now validated with `validate_safe_filename()` on save, and the delete endpoint confirms the target is inside `uploads/` before removing it.
- **HIGH — Weak PRNG in password reset tokens:** Tokens were generated with `md5(time() + email + mt_rand())` — predictable and short. Replaced with `random_bytes(32)` (256 bits of entropy).
- **HIGH — SQL/DDL injection in tax rate decimal places:** The decimal places value was interpolated directly into an `ALTER TABLE` statement. Strict integer validation added.
- **HIGH — Configuration injection in database setup wizard:** User-supplied database credentials were written to config files without escaping. Values are now sanitised before writing.
- **HIGH — IDOR + CSRF on guest quote approve/reject:** Endpoints accepted any quote ID without verifying the guest's ownership, and did not check a CSRF token. Both checks are now enforced.
- **MEDIUM — Authorization bypass in guest invoice/payment endpoints:** Guest users could access invoices not assigned to their client accounts. `user_has_invoice_access()` / `user_has_quote_access()` guards added.
- **MEDIUM — Setup wizard accessible post-installation:** The setup wizard remained reachable after installation. `SETUP_COMPLETED` and `DISABLE_SETUP` flags now block access; admins are warned if either flag is unset.
- **MEDIUM — SSRF via PDF footer content:** User-controlled content in invoice footers could trigger server-side requests via mPDF. Content is now sanitised before PDF generation.
- **MEDIUM — Open redirect via unvalidated `HTTP_REFERER`:** Multiple endpoints redirected to `$_SERVER['HTTP_REFERER']` without validation. All replaced with `get_safe_referer()`.
- **MEDIUM — Payment gateway API credential exposure:** Gateway API keys were stored in plaintext in the database. They are now encrypted at rest using the application encryption key.
- **LOW–MEDIUM — PHPMailer debug output in AJAX responses:** Debug output was appended to JSON responses, leaking server information. Debug output is now suppressed in AJAX context.
- **LOW–MEDIUM — Duplicate payment processing:** Stripe and PayPal callbacks could process the same payment twice on retry. Idempotency checks added.
- **LOW–MEDIUM — Email template preview rendered as raw HTML:** The preview endpoint returned unsanitised HTML, enabling stored XSS. HTML Purifier is now applied before rendering.
- **LOW — EXIF metadata in uploaded images:** Uploaded images could contain GPS coordinates, timestamps, and camera information. EXIF stripping is available via `SEC_STRIP_EXIF_FROM_IMAGES=true`.

### Added

- `sanitize_for_logging()` helper — single source of truth for log injection prevention (CWE-117).
- `validate_safe_filename()` and `validate_file_in_directory()` helpers — path traversal prevention (CWE-22).
- `get_safe_referer()` helper — open-redirect-safe referer resolution (CWE-601).
- `verify_csrf_token()` helper — timing-safe CSRF verification.
- `user_has_invoice_access()` / `user_has_quote_access()` helpers — IDOR guards.
- `validate_template_name()` — 7-layer template validation (empty check, path traversal, type, scope, static whitelist, character set, logging).
- `generate_secure_token()` / `generate_password_reset_token()` — CSPRNG-based token generation.
- XSS sanitisation now covers deeply nested POST arrays, with full field-path logging.
- Rate limiting for password reset requests — per IP (`PASSWORD_RESET_IP_MAX_ATTEMPTS`, `PASSWORD_RESET_IP_WINDOW_MINUTES`) and per email (`PASSWORD_RESET_EMAIL_MAX_ATTEMPTS`, `PASSWORD_RESET_EMAIL_WINDOW_HOURS`).
- `PASSWORD_RESET_TOKEN_EXPIRY_MINUTES` env var (default `15`).
- `SEC_STRIP_EXIF_FROM_IMAGES` env var (default `false`).

### Removed

- SVG logo upload support — SVGs can contain embedded JavaScript (XSS vector).
- Dynamic filesystem scanning for template discovery — replaced with static allowlist constants (RCE prevention).

### Fixed

- #1433 — LFI vulnerabilities in PDF template handling.
- #1388, #1387 — Unsafe jQuery plugin vulnerabilities.
- #1389 — Missing permissions in GitHub Actions workflows.
- #1383 — File access vulnerabilities across multiple controllers.
- #1381 — E-invoicing field migration and version checking.
- #1380 — Dependency update: `qs` package.
- #1377 — QR code image width reduced to 100 px.
- #1375 — Email address verification now accepts both comma and semicolon separators.
- #1373 — Removed deprecated library dependencies.
- #1367, #1368 — Various bug fixes.

---

## [1.7.0] — 2024

### Added

- PHP 8.2+ compatibility (minimum PHP 8.1 required; PHP 7.x no longer supported).
- Updated all Composer and Yarn dependencies.

### Security

- Fixed XSS vulnerabilities across invoice/quote number fields, tax rate names, payment method names, custom field labels, client addresses, Sumex observations, quote notes, and email template content.
- Fixed log injection in file upload handling.
- Fixed LFI vulnerabilities in PDF generation.
- Fixed unsafe jQuery plugin usage (multiple instances).

### Removed

- PHP 7.x compatibility.
- Deprecated library dependencies.

---

## [1.6.4] and earlier

For changes in version 1.6.4 and earlier, please see the git commit history.

---

## Security Disclosure

If you discover a security vulnerability in InvoicePlane, please email **[mail@invoiceplane.com](mailto:mail@invoiceplane.com)** before disclosing it publicly. We will address all security concerns promptly.
