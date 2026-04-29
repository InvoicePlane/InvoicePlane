# Copilot Instructions for InvoicePlane

InvoicePlane is a self-hosted, open-source invoicing application written in **PHP with CodeIgniter 3** (legacy v1 codebase). There is no Artisan CLI, no Laravel framework, and no Eloquent ORM. All instructions below are specific to this codebase.

## Project overview

- **Framework:** CodeIgniter 3
- **PHP version:** 8.2+
- **Database:** MySQL / MariaDB (accessed via CI's Active Record / Query Builder)
- **Frontend:** Bootstrap + jQuery, built assets managed by Yarn; PDF rendering via mPDF
- **Module layout:** `application/modules/<module>/` (controllers, models, views, helpers per module)
- **Configuration:** `ipconfig.php` (not `.env`); constants like `BASE_URL`, `DB_HOSTNAME` etc.
- **No Artisan** – database migrations are handled by the Setup module (`application/modules/setup/`)

## Coding conventions

- Follow PSR-12 style (enforced by Laravel Pint via `pint.json`).
- Use early returns over deep nesting.
- Prefer explicit, readable code over clever one-liners.
- Helper functions live in `application/helpers/` and are loaded with `$this->load->helper()`.
- Models extend `CI_Model`; controllers extend the project's base controllers (`Admin_Controller`, `Guest_Controller`).
- All user output in views must go through `html_escape()` or `htmlsc()`.

## Security requirements

- **Never** use `$_SERVER['HTTP_REFERER']` directly for redirects — use `get_safe_referer()`.
- **Never** scan the filesystem to build template whitelists (RCE risk); use the hardcoded `ALLOWED_*_TEMPLATES` constants in `Mdl_Templates`.
- Sanitize all data before logging with `sanitize_for_logging()`.
- Use `validate_safe_filename()` for any user-supplied filename.
- Do not introduce `mb_strlen` / `mb_substr` on raw binary data (Cryptor).

## Testing

- Tests are written with **PHPUnit** (no Laravel TestCase — use plain PHPUnit).
- Test files go in `tests/` when they exist.
- Method names: `it_<snake_case>`, annotated with `#[Test]`.
- Follow Arrange / Act / Assert pattern.
- Do **not** execute `vendor/bin/phpunit` in the coding-agent environment; run syntax/static checks only and rely on CI for PHPUnit execution.
- Controller tests must use explicit URI paths (e.g. `'/invoices/view/1'`), not `route('...')`.
- URI paths in tests must not contain namespace backslashes.

### CI3 test infrastructure

- HTTP controller tests extend `AbstractTestCase`; call `$this->actingAsAdmin()` in `setUp()`.
- Model tests extend `CiTestCase`, which exposes `$this->CI` (the CI3 super-object).
- Database-backed tests use the `InteractsWithDatabase` trait.
- Call `$this->skipWithoutDatabase()` as the first statement in any DB-dependent test (early-return pattern; skips gracefully locally, runs fully in CI).
- Use `$this->seedModel('ModelName', $overrides)` for all fixture creation — single entry point, returns the inserted row as `object` for FK chaining.
- Use `$this->assertDatabaseHas()`, `$this->assertDatabaseMissing()`, `$this->assertDatabaseCount()` for persistence checks.
- **`markTestIncomplete()` is prohibited** as a substitute for a real test. Write a proper CI3 test or guard with `skipWithoutDatabase()`.

### Payload doc block policy

Every `$this->get(...)` and `$this->post(...)` call in a test method that sends parameters (query params for GET, request body for POST) **must** have a `/** Payload: { ... } */` doc block directly above it (or directly above the `$payload = [...]` variable if one is defined).

**Format:**
```php
/**
 * Payload:
 * {
 *   "key": "value"
 * }
 */
$response = $this->post('/endpoint', ['key' => 'value']);
```

**Rules:**
- **NEVER** delete an existing payload doc block.
- Add payload blocks when writing new tests.
- GET calls with no parameters do not require a payload block.
- Do not duplicate blocks (if one already exists, do not add another).

## GitHub Actions

| Workflow | Purpose |
|----------|---------|
| `pint.yml` | Format PHP with Pint and commit changes |
| `phpunit.yml` | Run PHPUnit test suite |
| `phpstan.yml` | Static analysis |
| `php-lint.yml` | Syntax check (`php -l`) on every PHP file |
| `composer-update.yml` | Update Composer dependencies and open a PR |
| `yarn-update.yml` | Update Yarn dependencies and open a PR |

There is no `quickstart.yml` and no `setup.yml` that uses `php artisan` — those commands do not exist in InvoicePlane.

## Key files

| Path | Purpose |
|------|---------|
| `ipconfig.php.example` | Configuration template |
| `application/modules/invoices/` | Invoice management |
| `application/modules/guest/` | Public-facing invoice / quote views |
| `application/modules/mailer/helpers/phpmailer_helper.php` | Email sending |
| `application/libraries/Cryptor.php` | Encryption / decryption |
| `application/helpers/security_helper.php` | Security utility functions |
| `application/helpers/template_helper.php` | Template validation |
| `application/modules/invoices/models/Mdl_templates.php` | Template whitelist |
| `pint.json` | Pint / PHP CS Fixer configuration |
| `.junie/guidelines.md` | Full development guidelines |
| `AGENTS.md` | Instructions for AI coding agents |


## Test quality guardrails

- Do not delete or hollow out existing test method bodies during migration/refactor work. Preserve test intent and coverage.
- Weak tests are prohibited; assertions must validate expected behavior and outcomes.
- `assertResponseHasNoPhpErrors()` must not be used as a primary assertion for feature behavior.
- `markTestIncomplete()` is prohibited as a substitute for a real test — write a proper CI3 test.


## Test quality policy reference

- Follow `.junie/test-quality.md` as a mandatory test-quality policy.
- Weak tests are prohibited; refactor weak tests immediately when found.


## Controller route assertion policy

- In controller tests, always call explicit URI strings (for example `'/clients/form/1'`), never `route('name')`.
- Route strings must be plain URI paths and **must not contain namespace backslashes**.
- After route migration/refactors, run:
  - `rg -n "\\broute\\(" tests`
  - `php tests/Scripts/CheckExplicitTestRoutes.php`
  and fix any violations before committing.


## Test organization policy

- Every test method must include `#[Test]`.
- For large suites, apply meaningful `#[Group('...')]` tags to cluster smoke/crud/security/validation behaviors.
- Use PhpStorm folding markers (`// region ...` / `// endregion`) for long classes to keep sections navigable.


## Payload doc block policy

Every `$this->get(...)` and `$this->post(...)` call in a test method that sends parameters (query params for GET, request body for POST) **must** have a `/** Payload: { ... } */` doc block directly above it (or directly above the `$payload = [...]` variable if one is defined).

**Format:**
```php
/**
 * Payload:
 * {
 *   "key": "value"
 * }
 */
$response = $this->post('/endpoint', ['key' => 'value']);
```

**Rules:**
- **NEVER** delete an existing payload doc block.
- Add payload blocks when writing new tests.
- GET calls with no parameters do not require a payload block.
- Do not duplicate blocks (if one already exists, do not add another).
