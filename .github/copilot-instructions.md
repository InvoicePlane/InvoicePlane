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
- Run with `vendor/bin/phpunit`.

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
