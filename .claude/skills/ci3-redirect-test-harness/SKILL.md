# CI3 Redirect / exit() Test Harness Fix

## Problem

CodeIgniter 3's `redirect()` calls `header('Location: ...')` followed by `exit()`.
When `tests/Integration/bin/request.php` boots the full CI3 stack via `require
public/index.php`, that `exit()` terminates the PHP process immediately — before
`ob_get_clean()` runs. The captured output is empty, `http_response_code()` is 0,
and the test runner receives nothing.

## Solution

`tests/Integration/bin/request.php` uses `register_shutdown_function` to emit the
result payload even when `exit()` is called:

```php
$resultEmitted = false;

register_shutdown_function(function () use (&$exception, &$resultEmitted) {
    if ($resultEmitted) {
        return;
    }
    $output = ob_get_clean();
    $result = [
        'status'    => http_response_code() ?: 200,
        'headers'   => headers_list(),
        'output'    => $output ?? '',
        'exception' => $exception,
    ];
    echo '__CI_TEST_RESULT_START__';
    echo base64_encode((string) json_encode($result, JSON_THROW_ON_ERROR));
    echo '__CI_TEST_RESULT_END__';
    $resultEmitted = true;
});

try {
    require dirname(__DIR__, 3) . '/public/index.php';
} catch (Throwable $throwable) {
    $exception = $throwable::class . ': ' . $throwable->getMessage() . ...;
}

// Normal (non-redirect) path also emits:
$output = ob_get_clean();
$result = [...];
echo '__CI_TEST_RESULT_START__';
echo base64_encode(...);
echo '__CI_TEST_RESULT_END__';
$resultEmitted = true;
exit($exception === null ? 0 : 1);
```

Key points:
- The `$resultEmitted` flag prevents double-emission if `exit()` fires after the
  normal-path emit.
- `http_response_code()` survives `exit()` — the redirect status (301/302/303/307/308)
  is accessible in the shutdown function.
- `headers_list()` also survives — the `Location:` header is available.
- `ob_get_clean()` in the shutdown function flushes any partial output buffered
  before the `exit()`.

## SESS_SAVE_PATH

CI3 stores PHP sessions in a directory defined by `sess_save_path` in
`application/config/config.php`. The default (`sys_get_temp_dir()`) points to a
system temp directory that may be read-only or ephemeral in some environments.

**Fix**: Set a stable project-local path in `ipconfig.php`:

```
SESS_SAVE_PATH=storage/framework/sessions
```

And ensure the directory exists:

```bash
mkdir -p storage/framework/sessions
```

`application/config/config.php` already reads this via:

```php
$config['sess_save_path'] = env('SESS_SAVE_PATH', sys_get_temp_dir());
```

## Related Files

- `tests/Integration/bin/request.php` — subprocess runner (contains the fix)
- `application/config/config.php:442` — `sess_save_path` config key
- `ipconfig.php` — project env file (set `SESS_SAVE_PATH` here)
- `storage/framework/sessions/` — session storage directory (must exist, gitignored)
