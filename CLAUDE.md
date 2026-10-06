# CLAUDE.md — InvoicePlane Development Guide

InvoicePlane is a self-hosted, open-source invoicing app built on **CodeIgniter 3** with HMVC
(`application/modules/`). It is **not** a Laravel application: there is no Artisan CLI, no
Eloquent ORM and no `.env` file. Configuration lives in `ipconfig.php`.

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
| `validate_template_name()`       | `template_helper.php`       | Static-whitelist template check    |
| `sanitize_email_template_html()` | `html_sanitizer_helper.php` | HTML Purifier for email bodies     |

**IDOR guarding for invoices/quotes**: there is no shared `user_has_invoice_access()` /
`user_has_quote_access()` helper. Guest controllers instead scope every query inline with
`where_in('client_id', $this->user_clients)` and/or `url_key` matching — follow that same inline
pattern in new guest-facing code rather than inventing a wrapper function.

## Code style

- PSR-12 enforced by `vendor/bin/pint` (config: `pint.json`).
- Method names: `snake_case`; test methods: `it_<snake_case>` with `#[Test]`.
- No comments unless the *why* is non-obvious.

## Testing

### Quality gate before pushing

```bash
php -l <changed files>                  # syntax check; the php-lint workflow blocks parse errors
vendor/bin/phpunit                      # full suite, run on its own (see "One run at a time")
vendor/bin/pint                         # code style
vendor/bin/phpstan analyse              # static analysis (level 0, baseline in phpstan-baseline.neon)
php .claude/skills/config-parity-guard/audit.php   # every mutating endpoint is tested with CSRF on
```

After adding, removing or renaming test files, regenerate the class inventory, otherwise
`ClassCoverageInventoryTest` fails:

```bash
php -r 'require "tests/Support/ClassCoverageInventory.php"; $r = getcwd();
  file_put_contents("$r/tests/Support/class-coverage-inventory.md",
  ClassCoverageInventory::toMarkdown(ClassCoverageInventory::build($r)));'
```

### Test conventions

- Plain `\PHPUnit\Framework\TestCase` (unit) or `Tests\AbstractTestCase` (feature); no Laravel base classes.
- Method names `it_<snake_case>` with `#[Test]`; Arrange / Act / Assert sections.
- A test must be able to fail for the reason it names:
  - no `assertTrue(true)`, and no re-implementation of production logic inside a test;
  - "renders without PHP errors" is not an assertion; assert on content or state;
  - a redirect test asserts the destination **and** the state change or a positive control
    (the same request succeeding for an authorised user);
  - security and money logic is mutation-checked: break the production code and confirm the
    test turns red.
- Use the harness instead of inventing helpers: `seedClient`, `seedInvoice`, `databaseInsert`,
  `assertDatabaseHas`, `actingAsAdmin`, `postWithValidCsrfToken`, `withEnvironment`,
  `withServer`, `withFiles` (`tests/AbstractTestCase.php`, `tests/Concerns/`).
- Model logic can be tested in-process with `tests/Concerns/UsesCodeIgniterModels.php`
  (real CI database driver, no HTTP round trip). Override `ciDatabaseName()` to target a
  scratch database, as `SetupModelInstallTest` does.
- External services are replaced by fakes in `tests/Fakes/` (`FakePaypalHttpClient`,
  `FakeStripeHttpClient`, `QueueApiClient`), armed through `PAYPAL_MOCK_RESPONSES` and
  `INTEGRATION_MOCK_RESPONSES`. Never call a live provider from a test.

### How the feature harness works

`AbstractTestCase::request()` runs the application in a clean-environment subprocess
(`tests/Integration/bin/request.php`) and returns an `HttpResponse` carrying the status,
headers, body and the final session contents (`session()`, `sessionValue()`,
`sessionActive()`).

- `headers_list()` is empty under the CLI SAPI. The harness therefore records redirect targets
  itself and reads security headers from `$GLOBALS['ip_security_response_headers']`
  (set in `bootstrap/kernel.php`).
- Authenticated requests need `user_auth_version` and `user_credential` in the session
  (`HMAC-SHA256(password_hash, ENCRYPTION_KEY)`). `actingAs()` / `actingAsAdmin()` bind both
  from the stored `ip_users` row; any new `actingAs*()` helper must do the same, otherwise
  every request redirects to `sessions/login` (307). Session values are strings
  (`user_type => '1'`).
- To reproduce one request by hand, use the same clean environment:

```bash
PAYLOAD=$(php -r 'echo base64_encode(json_encode(["method"=>"GET","uri"=>"/clients","query"=>[],"post"=>[],"session"=>["user_id"=>"1","user_type"=>"1","user_email"=>"admin@test.local","user_language"=>"system"],"ajax"=>false]));')
env -i CI_TEST_REQUEST="$PAYLOAD" php tests/Integration/bin/request.php
```

### Environment setup

Two supported setups. Both use MariaDB, the only supported test database.

**Docker (ivpldock).** Run phpunit inside the containers, never on the host: the `mariadb`
hostname only resolves inside the Docker network. The Makefile defaults
`DOCKER_PROJECT_DIR` to `/var/www/projects/exprmt`; pass the real mount point:

```bash
make docker-test        DOCKER_PROJECT_DIR=/var/www/projects/invoiceplane/ivplv1
make docker-test-suite  SUITE=Unit DOCKER_PROJECT_DIR=...
make docker-test-filter FILTER=Session DOCKER_PROJECT_DIR=...
make docker-phpstan     DOCKER_PROJECT_DIR=...
```

`docker-db-prepare` rebuilds `invoiceplane_test` from `application/modules/setup/sql/*.sql`,
retrying transient InnoDB deadlocks (`tests/Support/docker-import-sql.sh`) and failing loudly
if the schema is incomplete. An unseeded or half-built schema shows up as `Expected row in
[ip_*] not found` or `Table ... doesn't exist` across many tests; re-run the prepare step.

**Sandbox (no Docker).**

```bash
bash tests/Support/sandbox-mariadb.sh     # install/start MariaDB, build and seed invoiceplane_test, write ipconfig.php
bash tests/Support/sandbox-bootstrap.sh   # install phpunit and the phpstan phar under .sandbox-tools/
env -u DB_HOSTNAME -u DB_PORT -u DB_DATABASE -u DB_USERNAME -u DB_PASSWORD \
  php .sandbox-tools/punit/vendor/bin/phpunit --bootstrap tests/bootstrap.php
php .sandbox-tools/phpstan.phar analyse --memory-limit=1G
```

Both scripts are idempotent. In the sandbox, Composer's dist downloads (`api.github.com`,
`codeload.github.com`) are blocked and `apt` is unavailable, so use `--prefer-source` and
`--ignore-platform-req=ext-bcmath`; the bootstrap script already does. State lives in the
gitignored `.sandbox-tools/`.

### One run at a time

phpunit truncates the shared test database. Never start a second run, or touch the database,
while a full run is in progress. If tests suddenly fail with "MariaDB is unreachable" or a
wall of 307 redirects, the database was stopped (the sandbox reaps `mysqld_safe`), not the
code: re-run `sandbox-mariadb.sh` and retry.

### Do not export `DB_*`

The phpunit parent reads its database settings through `env()`, which only reads `$_ENV`.
With `variables_order=GPCS`, exported variables land in `getenv()` but not `$_ENV`, and
Dotenv's `createImmutable` then refuses to load the `ipconfig.php` values. `env('DB_USERNAME')`
becomes `null`, the connection is denied, and every DB-backed test is **skipped** instead of
failing. A run with zero failures but a large skip count is therefore not green: check the skip
count. The same applies to CI: keep `DB_*` out of the job-level `env:` of the phpunit step.

### Line coverage (PCOV)

`tests/Support/class-coverage-inventory.md` is only a name search. For executed-line coverage,
including code that feature tests run in the request subprocess:

```bash
PCOV_SO=/path/to/pcov.so bash tests/Support/coverage/run.sh [phpunit args]
```

The `pcov` extension has to be built from `github.com/krakjoe/pcov` with `phpize`; see the
script header. Output is a ranked list of the least-covered files. Playwright E2E is not
included, and files that no test loads are estimated. Run it on its own, like the full suite.

### Static analysis

PHPStan runs at level 0 with `phpstan.neon`. CodeIgniter 3 has no autoloader, so
`tests/Support/phpstan-bootstrap.php` defines the path constants and loads every helper, and
`tests/Support/phpstan-ci-stubs.php` declares the CI system functions. If code calls a new CI
system function, add its signature to the stubs file instead of baselining `function.notFound`.
A clean run reports `[OK] No errors`.

## Adding an e-invoicing provider

Providers live in `application/modules/integrations/libraries/providers/` (`FooClient.php`, or
`Foo/FooClient.php` with helper classes beside it). `IntegrationClientRegistry` scans the
directory once per process and indexes every class that implements `IntegrationClientInterface`
by its client code; there is nothing to register.

For a REST provider, extend `AbstractRestProvider` (`libraries/AbstractRestProvider.php`):

1. Declare the provider once in `definition()`: `code`, `name`, optional `label` (used in error
   messages), `auth` (`bearer` or `oauth2`), and `settings` (key, default, form type, `required`,
   `sensitive`). Endpoint paths are settings whose keys end in `_endpoint`. Code, name, auth type,
   default settings and the settings form are all derived from it.
2. Implement `sendInvoice`, `getInvoiceStatus`, `receiveInvoices`, `downloadInvoiceDocument` and
   `getInvoiceEvents`. Use `buildUrl()`, `request()` and `ProviderResponseNormalizer::entity()` /
   `IntegrationResponseNormalizer::extractItems()` for the common work.
3. Override `authenticate()` / `fetchToken()` only if the provider's authentication is not plain
   bearer or OAuth2 client credentials, and `bearerToken()` / `extraHeaders()` for per-request
   credentials.

`tests/Fixtures/integrations/providers/DemoPdpClient.php` and
`tests/Unit/Core/Integrations/NewProviderOnBaseTest.php` are the reference to copy. Stored settings
and the settings form are a persistence contract: `ProviderSettingsContractTest` pins them for the
bundled providers, so a changed default or field type needs a deliberate fixture update.

## Common pitfalls

- **Do NOT call `php artisan`** — InvoicePlane is not Laravel.
- **Do NOT scan `application/views/` to enumerate templates** — bypasses the RCE fix.
- **Do NOT use `mb_*` on binary / ciphertext data** in `Cryptor`.
- **Do NOT log raw user input** — always `sanitize_for_logging()` or hash it.
- **Do NOT add `<form method="post">` without `<?php _csrf_field(); ?>`**.

## Documentation / changelog rules

- **Do NOT change CVSSv3 scores or CWE identifiers** in vulnerability tables unless explicitly asked. Scores and CWEs are set by the security researcher and the maintainer — never adjust them as a side-effect of filling in other columns (PR links, GHSA IDs, reporter handles).
- **Do NOT change the "Severity" label** (Critical / High / Medium / Low) without a separate explicit instruction.
- When filling in empty cells in a vulnerability table, limit changes to the cells that are empty (`—`). Leave populated cells untouched.

## Extended documentation

- `AGENTS.md` — full agent instructions and security rules
- `.github/copilot-instructions.md` — Copilot / AI coding conventions
- `.junie/guidelines.md` — detailed security patterns and code-review checklist
- `.github/improvements.md` — future enhancement ideas
