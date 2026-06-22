# AI Coding Agent Prompt — Bootstrap Refactor

Use this prompt verbatim (or adapt it) when asking any AI coding assistant to
implement the bootstrap professionalism changes described in
`.github/professionalism.md`.

---

## Prompt

You are working on **InvoicePlane**, a self-hosted PHP invoicing application
built with **CodeIgniter 3** (not Laravel). The repository is at
`/home/runner/work/InvoicePlane/InvoicePlane`.

### Task

Refactor the bootstrap layer so that `index.php` and `bootstrap/app.php` mimic
Laravel's application lifecycle **without requiring the Laravel framework**.

### Files to create

| File | Purpose |
|------|---------|
| `bootstrap/Application.php` | Lightweight Application class — owns path resolution and boot lifecycle |
| `bootstrap/BootProvider.php` | Interface that every boot step must implement |
| `bootstrap/Providers/EnvProvider.php` | Loads `ipconfig.php` via `vlucas/phpdotenv` |
| `bootstrap/Providers/ConstantsProvider.php` | Defines all CI3 global constants + env helpers once |
| `bootstrap/Providers/CiCoreProvider.php` | Requires CI3 core files in the correct order |
| `bootstrap/Providers/MxProvider.php` | Requires WireDesignz MX (HMVC) after CI core |

### Files to update

| File | Change |
|------|--------|
| `bootstrap/app.php` | Replace procedural content with `Application::createForHttp(dirname(__DIR__))` |
| `index.php` | Replace content with `(require_once __DIR__ . '/bootstrap/app.php')->handleHttp();` |
| `tests/bootstrap.php` | Replace with `Application::createForConsole(dirname(__DIR__))->boot();` |
| `composer.json` | Add `"Bootstrap\\\\": "bootstrap/"` to `autoload.psr-4` |

### Files to delete (after migration)

- `bootstrap/kernel.php`
- `bootstrap/constants.php`
- `bootstrap/env.php`
- `bootstrap/helpers.php`

### Mandatory design rules

Every class you write **must** follow all four principles below. Do not skip any.

#### 1. DRY (Don't Repeat Yourself)
- Constants (`FCPATH`, `APPPATH`, `BASEPATH`, `VIEWPATH`, `IP_DEBUG`) must be
  defined in **exactly one place**: `ConstantsProvider::register()`.
- Helper functions (`env()`, `env_bool()`, `base_path()`) must be declared in
  **exactly one place**: `ConstantsProvider::registerHelpers()`.
- Remove all duplicate `defined()` / `function_exists()` blocks from
  `kernel.php`, `constants.php`, `env.php`, `helpers.php` — these files will be
  deleted.

#### 2. SOLID

- **SRP** — `Application` only manages boot lifecycle and path resolution.
  `EnvProvider` only loads environment. `ConstantsProvider` only defines
  constants. `CiCoreProvider` only loads CI3 core. `MxProvider` only loads MX.
- **OCP** — New bootstrap steps are added by implementing `BootProvider` and
  passing them to `Application::withProvider()`. Never add a bare `require_once`
  to `bootstrap/app.php` or `index.php`.
- **LSP** — All `BootProvider` implementations are interchangeable; they only
  receive `Application $app` and return `void`.
- **ISP** — `BootProvider` has a single `register(Application $app): void`
  method. No provider needs to implement anything else.
- **DIP** — `index.php` depends on `Application` (the abstraction), not on CI3
  files. `Application` depends on `BootProvider` (the interface), not on
  concrete providers.

#### 3. Early Returns
- Every `register()` method must check its precondition at the **top** and
  `return` immediately if the work has already been done:
  ```php
  public function register(Application $app): void
  {
      if (defined('CI_VERSION')) {
          return;
      }
      // ... rest of the method
  }
  ```
- `Application::boot()` must return `$this` immediately if `$this->booted`
  is already `true`.

#### 4. Dynamic Programming (memoisation)
- `Application::boot()` must be **idempotent**: calling it multiple times is
  safe and only runs providers once.
- Path helpers (`basePath()`, `appPath()`, `vendorPath()`) resolve the path
  from `$this->basePath` once and return it — no filesystem traversal on every
  call.
- Use `static` local variables or instance properties for any value that is
  expensive to compute (e.g. resolved real paths).

### Constraints

- Do **not** add `laravel/framework` or any Laravel package as a dependency.
- Do **not** use `$_SERVER['HTTP_REFERER']` for any redirect — use
  `get_safe_referer()`.
- Do **not** scan the filesystem for template lists — use the hardcoded
  `ALLOWED_*_TEMPLATES` constants.
- Do **not** use `mb_strlen()` / `mb_substr()` on binary/cipher data.
- Do **not** log raw user input — use `sanitize_for_logging()`.
- All new PHP files must pass `php -l` (no syntax errors).
- After all changes, run `vendor/bin/pint` for code style and
  `vendor/bin/phpunit` to confirm the test suite still passes.

### Reference implementation

See `.github/professionalism.md` for the full reference implementation with
code examples and a before/after comparison table.

### Verification checklist

After completing the implementation, confirm:

- [ ] `php -l bootstrap/Application.php` → no errors
- [ ] `php -l bootstrap/BootProvider.php` → no errors
- [ ] `php -l bootstrap/Providers/*.php` → no errors
- [ ] `php -l index.php` → no errors
- [ ] `php -l bootstrap/app.php` → no errors
- [ ] `php -l tests/bootstrap.php` → no errors
- [ ] `vendor/bin/pint --test` → no style violations
- [ ] `vendor/bin/phpunit` → all tests pass
- [ ] `bootstrap/kernel.php`, `bootstrap/constants.php`, `bootstrap/env.php`,
      `bootstrap/helpers.php` are deleted
- [ ] No `defined('FCPATH')` / `defined('APPPATH')` / `defined('BASEPATH')`
      guards remain outside `ConstantsProvider`
- [ ] No `function_exists('env')` / `function_exists('env_bool')` guards remain
      outside `ConstantsProvider`
