# CLAUDE.md — InvoicePlane Development Guide

InvoicePlane is a self-hosted, open-source invoicing app built on **CodeIgniter 3** with HMVC
(`application/modules/`). It is **not** a Laravel application — there is no Artisan CLI, no
Eloquent ORM, and no `.env` file. Configuration lives in `ipconfig.php`.

## Quick-start mental model

| Concept               | CI3 / InvoicePlane pattern                                                  |
|-----------------------|-----------------------------------------------------------------------------|
| Controller base       | `Admin_Controller` (auth required) · `Guest_Controller` · `Base_Controller` |
| Model                 | Extends `MY_Model` → `Response_Model`; Query Builder only — no raw SQL      |
| View output           | `html_escape($v)` / `htmlsc($v)` everywhere — never bare `echo $v`          |
| Helper loading        | `$this->load->helper('name')` (never `require`)                             |
| Input                 | `$this->input->post()` / `->get()` — never `$_POST` / `$_GET` directly      |
| Config                | Constants in `ipconfig.php` via `env()` helper; not `.env`                  |

## Non-negotiable security rules

These are documented fully in `AGENTS.md` and `.junie/guidelines.md`. Short form:

1. **No raw `$_SERVER['HTTP_REFERER']`** — use `get_safe_referer()` from `security_helper.php`.
2. **No filesystem scanning for templates** — use the `ALLOWED_*_TEMPLATES` constants in
   `Mdl_Templates` (prevents RCE).
3. **Sanitize before every `log_message()` call** — use `sanitize_for_logging()` or
   `hash('sha256', $value)` for sensitive data (prevents log injection / CWE-117).
4. **Validate every filename** — use `validate_safe_filename()` and
   `validate_file_in_directory()` before any filesystem operation on user-supplied names.
5. **`cookie_httponly` must stay `true`** — session cookies must not be accessible to JS.
6. **CSRF** — every state-changing POST form must include `<?php _csrf_field(); ?>` and the
   controller must call `ensure_valid_post_request()` or `verify_csrf_token()`.
7. **Never `mb_strlen` / `mb_substr` on binary data** (Cryptor / ciphertext).

## SOLID / DRY principles applied here

- **Single Responsibility**: one helper file per concern (`file_security_helper.php`,
  `security_helper.php`, `template_helper.php`).
- **Open/Closed**: extend `MY_Model` / `Response_Model` — don't edit base classes for
  module-specific behaviour.
- **Early returns**: preferred over deep nesting (see existing controllers as reference).
- **DRY**: if the same sanitization / validation logic appears in ≥ 3 places, extract it
  to the appropriate helper and write a unit test.

## Key helper functions (do not reinvent)

| Function                         | File                        | Purpose                            |
|----------------------------------|-----------------------------|------------------------------------|
| `sanitize_for_logging()`         | `file_security_helper.php`  | Strip `\r\n` before log_message()  |
| `hash_for_logging()`             | `file_security_helper.php`  | SHA-256 hash for sensitive values  |
| `validate_safe_filename()`       | `file_security_helper.php`  | Path-traversal + null-byte check   |
| `validate_file_in_directory()`   | `file_security_helper.php`  | Realpath directory confinement     |
| `sanitize_filename_for_header()` | `file_security_helper.php`  | CRLF-safe Content-Disposition      |
| `get_safe_referer()`             | `security_helper.php`       | Open-redirect-safe referer URL     |
| `verify_csrf_token()`            | `security_helper.php`       | Timing-safe CSRF check             |
| `user_has_invoice_access()`      | `security_helper.php`       | IDOR guard for invoices            |
| `user_has_quote_access()`        | `security_helper.php`       | IDOR guard for quotes              |
| `validate_template_name()`       | `template_helper.php`       | Static-whitelist template check    |
| `sanitize_email_template_html()` | `html_sanitizer_helper.php` | HTML Purifier for email bodies     |

## Code style

- PSR-12 enforced by `vendor/bin/pint` (config: `pint.json`).
- Method names: `snake_case`; test methods: `it_<snake_case>` with `#[Test]`.
- No comments unless the *why* is non-obvious.

## Testing

```bash
vendor/bin/phpunit          # run all tests
vendor/bin/pint             # fix code style
vendor/bin/phpstan analyse  # static analysis
```

Tests live in `tests/`. Use plain `\PHPUnit\Framework\TestCase` — no Laravel `TestCase`.

## Common pitfalls

- **Do NOT call `php artisan`** — InvoicePlane is not Laravel.
- **Do NOT scan `application/views/` to enumerate templates** — bypasses the RCE fix.
- **Do NOT use `mb_*` on binary / ciphertext data** in `Cryptor`.
- **Do NOT log raw user input** — always `sanitize_for_logging()` or hash it.
- **Do NOT add `<form method="post">` without `<?php _csrf_field(); ?>`**.

## Extended documentation

- `AGENTS.md` — full agent instructions and security rules
- `.github/copilot-instructions.md` — Copilot / AI coding conventions
- `.junie/guidelines.md` — detailed security patterns and code-review checklist
- `.github/improvements.md` — future enhancement ideas
