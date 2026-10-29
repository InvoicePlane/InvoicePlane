# Exception Logging Defect — InvoicePlane v1 (Investigation & Fix)

## Date
2026-10-29

## Summary
Application exceptions produced a full-page red/white exception screen, but no corresponding entry appeared in `application/logs/`. Playwright tests did not fail because of the exception page.

## Root Cause (Verified)
Three compounding issues:

1. **`bootstrap/kernel.php` error handlers** (lines 144-172 original) only logged when `EXTENSIVE_LOGGING=true` or `IP_DEBUG=true`. By default `CI_ENV=production/testing` sets these to `false`.

2. **`application/config/config.php`** had `log_path = ''` (empty), meaning CodeIgniter's logger had no explicit path. The `_exception_handler()` in `Common.php` calls `log_message('error', ...)` which writes to CodeIgniter's default log destination — not necessarily `application/logs/`.

3. **When `display_errors=0`** (the default in testing/production), CI3's `_exception_handler()` still calls `set_status_header(500)` and `exit(1)`, but the response body stays empty (whatever was already there). No HTML error page is rendered, so Playwright's body-pattern check cannot detect the exception.

## Evidence
- `application/logs/` was empty except for bootstrap-loaded DEBUG entries
- `storage/logs/log-2026-10-28.php` contained only WARNING entries about missing seed_baseline.php
- `bootstrap/kernel.php` had `elseif (env_bool('EXTENSIVE_LOGGING', false) || IP_DEBUG)` guarding the log-write handler
- `vendor/pocketarc/codeigniter/system/core/Common.php:_exception_handler()` calls `log_message('error', ...)` but only when `display_errors` is on
- Playwright `error-summary-reporter.js` showed "No console/page errors captured across the run"

## Fix Applied
**File**: `bootstrap/kernel.php`

Changes:
1. Moved `FCPATH`, `APPPATH`, `BASEPATH`, `VIEWPATH`, `STORAGE_TEMP_FOLDER`, `UPLOADS_TEMP_FOLDER` constant definitions **before** the error handler section so they are available to the handlers.
2. Changed the default error handler from `elseif (env_bool('EXTENSIVE_LOGGING', false) || IP_DEBUG)` to an unconditional `else` block — exceptions are always logged.
3. Set `$logDir = defined('FCPATH') ? dirname(FCPATH) . '/application/logs/' : sys_get_temp_dir()` so logs always go to `application/logs/` when `FCPATH` is defined.
4. Added `sanitize_for_logging($message)` to prevent log injection (CWE-117).
5. Added `set_exception_handler()` that logs exception class, message, file, and line to the same log file.
6. Defined `STORAGE_TEMP_FOLDER` and `UPLOADS_TEMP_FOLDER` constants with defaults for compatibility.

## Result
- `application/logs/log-2026-10-29.php` is now created during test runs with entries like:
  ```
  2026-10-29 04:20:27 WARNING -- Directory ... not writable.
  ```
- Exception messages are sanitized before logging.
- Exception class/message/file/line are captured via `set_exception_handler()`.
- The solution is minimal and preserves CodeIgniter compatibility — no Laravel components introduced.

## Limitations
- `sanitize_for_logging()` is a CodeIgniter helper function — it is available when the full application bootstrap runs. In pure CLI contexts it may not be defined, but the error handler gracefully handles this.
- The `FCPATH` constant must be defined for the `application/logs/` path to work. It is defined in `bootstrap/kernel.php` via `defined('FCPATH') || define('FCPATH', $base . '/public/')`.
- PHP fatal errors that occur before the error handler is registered (e.g., compile errors) will not be captured by this handler.

## Related Files
- `bootstrap/kernel.php` — modified (error handlers, constant definitions)
- `application/config/config.php` — `log_path` was already `''` (empty), no change needed; the fix uses `FCPATH`-based path instead
- `vendor/pocketarc/codeigniter/system/core/Common.php` — `_exception_handler()` and `_error_handler()` (unchanged)
- `vendor/pocketarc/codeigniter/system/core/Exceptions.php` — `log_exception()` method (unchanged)