# AGENTS.md — Instructions for AI Coding Agents

This file provides context and instructions for AI coding agents (GitHub Copilot, Claude, etc.) working on the InvoicePlane codebase.

## Project overview

InvoicePlane is a **self-hosted, open-source invoicing application** built with **PHP and CodeIgniter 3**. It is not a Laravel application. There is no Artisan CLI, no Eloquent ORM, and no `artisan migrate`.

- **Framework:** CodeIgniter 3
- **PHP:** 8.2+
- **Database:** MySQL / MariaDB
- **Build tools:** Yarn (frontend), Composer (backend)
- **PDF generation:** mPDF
- **Email:** PHPMailer

## Repository layout

```
application/
  config/           CI3 configuration files
  helpers/          Global helper functions
  language/         i18n strings
  libraries/        Custom CI3 libraries (Cryptor, etc.)
  modules/          Feature modules (CodeIgniter HMVC)
    clients/
    invoices/       Invoice management
    guest/          Public-facing invoice and quote views
    mailer/         Email sending (PHPMailer wrapper)
    settings/
    setup/          Database migration wizard
    ...
  views/            Shared views and PDF/public templates
assets/
  core/js/scripts.js
  core/css/
.github/
  workflows/        GitHub Actions (see table below)
  scripts/          Helper scripts for CI (phpstan parser)
  actions/          Composite actions (setup-php-composer)
pint.json           Code style configuration (Pint / PHP CS Fixer)
phpstan.neon        Static analysis configuration
ipconfig.php.example  Application configuration template
CHANGELOG.md
UPGRADE.md
AGENTS.md           (this file)
.junie/guidelines.md  Extended development guidelines
```

## Framework conventions

| Concept | CodeIgniter 3 Pattern |
|---------|----------------------|
| Controller base | `Admin_Controller`, `Guest_Controller` |
| Model | Extends `CI_Model`; use `$this->db->*()` |
| View | Plain PHP template files with `html_escape()` for output |
| Helper loading | `$this->load->helper('helper_name')` |
| Input | `$this->input->post()`, `$this->input->get()` — never `$_POST` directly |
| Configuration | Constants defined in `ipconfig.php` (not `.env`) |
| URL routing | `application/config/routes.php` |
| Database | Active Record / Query Builder — no raw SQL string concatenation |

## Security rules

These rules must not be broken.

1. **No filesystem scanning for template whitelists.** Template names are defined in hardcoded constants in `Mdl_Templates`. Scanning the filesystem (e.g. with `directory_map()`) to build an allowed list creates an RCE vulnerability.

2. **No unvalidated redirects.** Use `get_safe_referer()` (from `security_helper.php`) for any redirect that incorporates `HTTP_REFERER`.

3. **Sanitize before logging.** All user-controlled or external data must pass through `sanitize_for_logging()` before appearing in a log message.

4. **Byte-safe binary operations.** Use `strlen()` / `substr()` on raw binary data (e.g. in `Cryptor`). Do not use `mb_strlen()` / `mb_substr()` on ciphertext or IVs.

5. **Encode all output.** Views must use `html_escape()` or `htmlsc()` for every user-controlled value.

6. **Validate file paths.** Use `validate_safe_filename()` and `validate_file_in_directory()` before any file inclusion or file system operation on user-supplied names.

## GitHub Actions

| Workflow | Trigger | Purpose |
|----------|---------|---------|
| `php-lint.yml` | push / PR | Syntax-check every PHP file with `php -l` |
| `pint.yml` | manual | Format code with Pint and commit changes |
| `phpunit.yml` | manual | Run PHPUnit test suite |
| `phpstan.yml` | manual | Static analysis with PHPStan |
| `composer-update.yml` | manual / weekly | Update Composer dependencies; open a PR |
| `yarn-update.yml` | manual / weekly | Update Yarn dependencies; open a PR |

There is no `quickstart.yml`. InvoicePlane does not have `php artisan` commands.

## Testing

- Use **PHPUnit** directly (`vendor/bin/phpunit`).
- No Laravel `TestCase` — extend plain `\PHPUnit\Framework\TestCase`.
- Method names: `it_<snake_case>`, annotated with `#[Test]`.
- Pattern: Arrange / Act / Assert.
- Run: `vendor/bin/phpunit` (requires `phpunit.xml` to be present).

### Agent environment execution rule

- In this coding-agent environment, **do not execute `vendor/bin/phpunit`**. Keep verification to static checks (for example `php -l`) and leave PHPUnit execution to CI where dependencies are fully provisioned.
- If PHPUnit cannot be run locally, state that clearly in the PR/summary and ensure CI is expected to run the suite.

## Common pitfalls

- **Do not call `php artisan`** — this command does not exist in InvoicePlane.
- **Do not scan `application/views/`** to enumerate templates — this bypasses the RCE fix.
- **Do not use `mb_*` functions on binary data** in cryptographic operations.
- **Do not add `header("Location: " . $_SERVER['HTTP_REFERER'])` patterns** — use `get_safe_referer()`.
- **Do not log raw user input** — always use `sanitize_for_logging()`.
- Pint (`vendor/bin/pint`) formats PHP code style. Running it with the `"="` alignment set to `align_single_space_minimal` previously caused an "illegal offset" error on mixed PHP/HTML view files. The `pint.json` has been updated to use `single_space` for `=` to prevent this.

## Adding a new template

1. Create the template file in `CUSTOM_TEMPLATES_FOLDER/<invoice_templates|quote_templates>/<pdf|public>/MyTemplate.php`.
2. Configure `CUSTOM_TEMPLATES_FOLDER` in `ipconfig.php` to point to the parent directory.
3. The template will appear in the UI automatically (validated by `Mdl_Templates::_merge_custom()`).

Alternatively, add the template name to the `ALLOWED_INVOICE_TEMPLATES` or `ALLOWED_QUOTE_TEMPLATES` constant in `application/modules/invoices/models/Mdl_templates.php` and place the file inside `application/views/`.

## Extended guidelines

See `.junie/guidelines.md` for detailed guidance on security patterns, DRY principles, and the code review checklist.


## Test quality guardrails

- **Do not delete or hollow out existing test bodies** during refactors. Keep Arrange/Act/Assert logic intact and migrate assertions/URIs incrementally.
- **Weak tests are prohibited.** Avoid assertions that only prove non-crash behavior without validating expected business behavior.
- **Do not rely on `$this->assertResponseHasNoPhpErrors($response)` as a primary assertion.** Use concrete assertions (status, redirect target, view/response payload, DB/session side effects) that verify behavior.


## Test quality policy reference

- Follow `.junie/test-quality.md` as mandatory policy for all new/refactored tests.
- Weak tests are prohibited and must be refactored immediately when discovered.

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
