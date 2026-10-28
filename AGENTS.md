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

## Pull request numbering (fork and upstream stay in step)

Every upstream `InvoicePlane/InvoicePlane` PR has exactly one twin in the fork `underdogg-forks/ivpl-xprmt`, and the
two numbers move in lockstep: `upstream number - fork number` is constant (currently 1731: upstream #1836 / fork #105,
#1837 / #106, #1838 / #107; the next pair is upstream #1839 / fork #108).

- GitHub numbers PRs and issues from one counter per repository. Before opening a PR, read the latest number on
  **both** repositories (`gh api repos/<owner>/<repo>/issues?state=all&per_page=1&sort=created&direction=desc`) and
  open the pair back to back, so the offset does not drift.
- Never open a second PR on one side while the other side lags. A docs-only or otherwise twin-less upstream PR, an
  issue, or a PR from someone else also advances a counter: when that happens, the missing number on the other side is
  burned with a placeholder PR (the maintainer does that), and the new offset is written here.
- The twins cross-link by number in their descriptions (`underdogg-forks/ivpl-xprmt#107` <-> `InvoicePlane/InvoicePlane#1838`),
  and the fork PR carries tests first and the implementation second while the upstream PR carries the implementation only.

## Tests: audit every test before presenting it

A first draft of a test is rarely strong enough, and "it goes red without the fix" is not the bar. Before a test counts, check
that it asserts, as applicable:

- **the status** and, for a refusal, **the reason shown** (so a 403 cannot come from something else, such as a missing CSRF token);
- **nothing leaked** in the body (names, keys, tokens) and **stored state unchanged** (a read endpoint must not write);
- **a positive control that proves the right thing was served** (its own key, not the other gateway's; the owner and the primary
  administrator served, not only the attacker refused);
- **the other request shapes**: other HTTP methods, casing, a valid key with the feature disabled, boundary and non-integer ids,
  a user that is still being created;
- **a structural check** where a request cannot see the property (the public methods of a controller, undefined language keys,
  functions newer than the PHP baseline).

A status-code-only test is a draft. Sweep the whole class of the problem, not only the reported instance, and prove the test by
removing the guard (one-line mutants) before the change is called done. This is the `scrutinizer` skill in short.

## Extended guidelines

See `.junie/guidelines.md` for detailed guidance on security patterns, DRY principles, and the code review checklist.
