# White-Screen-of-Death Prevention

The "white screen of death" in CI3 integration tests means the subprocess returned
an empty body with a misleading status code. These are the known causes and the
invariants that prevent them from coming back.

---

## Cause 1 — ob_end_clean() discards content before the callback fires

`request.php` opens a level-1 ob_start() callback that accumulates `$captured_output`.
CI3 opens its own level-2 buffer. Any content written before `exit()` lives in level 2.

**Wrong** — `ob_end_clean()` discards the level-2 content without cascading it to the
callback. `$captured_output` stays empty.

**Correct** — `ob_end_flush()` passes each level's content to the next level. When it
reaches level 1, the callback fires and populates `$captured_output`.

Rule: **every cleanup loop in `request.php` MUST use `ob_end_flush()`**, never
`ob_end_clean()`.

```php
// Correct
while (ob_get_level() > 0) {
    ob_end_flush();
}
```

---

## Cause 2 — set_status_header() is a no-op in CLI mode

CI3's `set_status_header()` calls `header()`, which does nothing when PHP runs as CLI.
Any code that relies on it to set a 4xx/5xx response code will silently produce 200.

Rule: **In `CI_TEST_SUBPROCESS`, always call `http_response_code($code)` directly**
instead of (or in addition to) `set_status_header()`.

This is why `MY_Exceptions::show_404()` overrides the parent: it calls
`http_response_code(404)` before rendering the template, because the parent only
calls `set_status_header()` which the CLI silently ignores.

---

## Cause 3 — show_error() returns, not echoes, in CLI mode

CI3's `CI_Exceptions::show_error()` uses the `cli/error_*.php` template in CLI mode.
That template writes to STDOUT via the return value, which the caller (global
`show_error()`) echoes. But the path from `show_404()` in testing ENVIRONMENT throws
a `RuntimeException` before any output is written, so the body is always empty.

Rule: **`MY_Exceptions::show_404()` must render the HTML template itself** (not
delegate to `show_error()`), write the output, set the status code, and `exit()`.

```php
public function show_404($page = '', $log_error = true)
{
    if (defined('CI_TEST_SUBPROCESS')) {
        http_response_code(404);
        ob_start();
        include $templates_path . 'html/error_404.php';
        echo ob_get_clean();
        exit(4);
    }
    parent::show_404($page, $log_error);
}
```

---

## Cause 4 — Application constants not defined before CI3 boots

Constants historically defined in the old root `index.php` (e.g. `UPLOADS_FOLDER`,
`UPLOADS_CFILES_FOLDER`, `UPLOADS_TEMP_MPDF_FOLDER`) were silently missing after
the split into `bootstrap/kernel.php`. Code that uses them before CI3's
`application/config/constants.php` is loaded will trigger a fatal error that the
subprocess captures as an empty body + exit code 1.

Rule: **Every path constant that any module may use before CI3 config loads MUST
be defined in `bootstrap/kernel.php`** with `defined() || define()` guards.

Current set that belongs in `kernel.php`:

```
IPCONFIG_FILE, LOGS_FOLDER
UPLOADS_FOLDER, UPLOADS_ARCHIVE_FOLDER, UPLOADS_CFILES_FOLDER
UPLOADS_TEMP_FOLDER, UPLOADS_TEMP_MPDF_FOLDER
```

When adding a new module that defines its own path constant (e.g.
`REPORTS_FOLDER`), add it to `kernel.php` — not to a config file that loads later.

---

## Checklist when a test returns an empty body

1. Check `$response->exception()` — was a Throwable caught by the subprocess?
2. Run the subprocess manually: `CI_TEST_REQUEST=<base64payload> php tests/Integration/bin/request.php`
   and inspect STDERR for fatal errors.
3. Check that all constants the route's controller uses are in `kernel.php`.
4. Confirm `ob_end_flush()` (not `ob_end_clean()`) is used in every cleanup loop.
5. Confirm `http_response_code()` is called directly for error codes, not via `set_status_header()`.
