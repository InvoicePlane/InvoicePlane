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
.github/CHANGELOG.md
.github/docs/UPGRADE.md
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

### PHPUnit installation in Claude Code web sandbox

Due to proxy policies blocking `api.github.com` and `codeload.github.com`, standard Composer installation fails in the sandbox. **Use this proven approach:**

```bash
# Setup token-free Composer home
CH=$(mktemp -d)
printf '{}\n' > "$CH/auth.json"
printf '{"config":{}}\n' > "$CH/config.json"

# Step 1: Install runtime deps (no dev) to avoid phpstan blocking
COMPOSER_HOME="$CH" composer install --prefer-source --no-dev --ignore-platform-req=ext-bcmath

# Step 2: Install PHPUnit in throwaway project (faster, isolated)
mkdir -p /tmp/punit && cd /tmp/punit
COMPOSER_HOME="$CH" composer require --prefer-source --dev phpunit/phpunit:^10.5 --ignore-platform-req=ext-bcmath

# Step 3: Restore Tests\ PSR-4 mapping (removed by --no-dev)
cd <repo-root>
COMPOSER_HOME="$CH" composer dump-autoload --dev

# Step 4: Run tests using throwaway project's phpunit
php /tmp/punit/vendor/bin/phpunit --bootstrap tests/bootstrap.php
```

This is the **only reliable method** in the web sandbox. See `CLAUDE.md` for detailed rationale.

## Common pitfalls

- **Do not call `php artisan`** — this command does not exist in InvoicePlane.
- **Do not scan `application/views/`** to enumerate templates — this bypasses the RCE fix.
- **Do not use `mb_*` functions on binary data** in cryptographic operations.
- **Do not add `header("Location: " . $_SERVER['HTTP_REFERER'])` patterns** — use `get_safe_referer()`.
- **Do not log raw user input** — always use `sanitize_for_logging()`.

## Documentation / changelog rules

- **Do NOT change CVSSv3 scores, CWE identifiers, or Severity labels** in vulnerability tables unless the user explicitly asks. These are set by the security researchers and maintainer.
- When populating empty cells (`—`) in a vulnerability table, touch only those empty cells. Leave all other columns in the same row unchanged.
- GHSA advisory links follow the pattern: `https://github.com/InvoicePlane/InvoicePlane/security/advisories/GHSA-xxxx-xxxx-xxxx`
- Pint (`vendor/bin/pint`) formats PHP code style. Running it with the `"="` alignment set to `align_single_space_minimal` previously caused an "illegal offset" error on mixed PHP/HTML view files. The `pint.json` has been updated to use `single_space` for `=` to prevent this.

## Adding a new template

1. Create the template file in `CUSTOM_TEMPLATES_FOLDER/<invoice_templates|quote_templates>/<pdf|public>/MyTemplate.php`.
2. Configure `CUSTOM_TEMPLATES_FOLDER` in `ipconfig.php` to point to the parent directory.
3. Add the template name (without `.php`) to the appropriate explicit allowlist constant in `ipconfig.php`:
   - `CUSTOM_INVOICE_TEMPLATES_PDF` — PDF invoice templates
   - `CUSTOM_INVOICE_TEMPLATES_PUBLIC` — public/web invoice templates
   - `CUSTOM_QUOTE_TEMPLATES_PDF` — PDF quote templates
   - `CUSTOM_QUOTE_TEMPLATES_PUBLIC` — public/web quote templates
   Example: `CUSTOM_INVOICE_TEMPLATES_PDF=MyTemplate,AnotherTemplate`
4. The template will appear in the UI once its name is in the allowlist constant.

Note: The filesystem is **never** scanned to discover custom templates (RCE prevention). Only names present in the explicit constants are used.

Alternatively, add the template name to the `ALLOWED_INVOICE_TEMPLATES` or `ALLOWED_QUOTE_TEMPLATES` constant in `application/modules/invoices/models/Mdl_templates.php` and place the file inside `application/views/`.

## Extended guidelines

See `.junie/guidelines.md` for detailed guidance on security patterns, DRY principles, and the code review checklist.
