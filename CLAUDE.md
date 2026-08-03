# CLAUDE.md — InvoicePlane Development Guide

InvoicePlane is a self-hosted, open-source invoicing app built on **CodeIgniter 3** with HMVC
(`application/modules/`). It is **not** a Laravel application — there is no Artisan CLI, no
Eloquent ORM, and no `.env` file. Configuration lives in `ipconfig.php`.

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
| `user_has_invoice_access()`      | `security_helper.php`       | IDOR guard for invoices            |
| `user_has_quote_access()`        | `security_helper.php`       | IDOR guard for quotes              |
| `validate_template_name()`       | `template_helper.php`       | Static-whitelist template check    |
| `sanitize_email_template_html()` | `html_sanitizer_helper.php` | HTML Purifier for email bodies     |

## Code style

- PSR-12 enforced by `vendor/bin/pint` (config: `pint.json`).
- Method names: `snake_case`; test methods: `it_<snake_case>` with `#[Test]`.
- No comments unless the *why* is non-obvious.

## Testing & Code Quality

Before pushing:
```bash
php -l application/**/*.php   # Syntax check (finds parse errors)
vendor/bin/phpunit            # run all tests
vendor/bin/pint               # fix code style
vendor/bin/phpstan analyse    # static analysis
```

**CRITICAL:** `php -l` must be run before any push to prevent parse errors in GitHub Actions. The workflow `.github/workflows/php-lint.yml` will block commits with syntax errors, so catch them locally first. This catches issues like embedded `<?php` tags in comments that confuse the lexer.

Tests live in `tests/`. Use plain `\PHPUnit\Framework\TestCase` — no Laravel `TestCase`.

### Bootstrapping `vendor/` in the Claude Code web sandbox

Regular local dev and CI are unaffected — this note is only for the Claude-Code-on-web
sandbox, where the container starts with no `vendor/` and a plain `composer install` fails
with `Could not authenticate against github.com`. In that environment, `git` reaches GitHub
through an authenticating proxy but Composer's HTTP **dist** downloads do not, so install
from source with a token-free Composer home:

```bash
CH=$(mktemp -d); printf '{}\n' > "$CH/auth.json"; printf '{"config":{}}\n' > "$CH/config.json"
COMPOSER_HOME="$CH" composer install --prefer-source --ignore-platform-req=ext-bcmath
```

Everything clones except the dist-only `phpstan/phpstan` phar (a transitive dev dep via
rector); there is no per-package skip flag for `install`, and that one failure aborts the
run **before** the autoloader is generated, so `vendor/autoload.php` never gets written.

To get a clean, working autoloader anyway, install runtime-only (`--no-dev` drops the
`phpstan` dev chain entirely), then add PHPUnit separately for the test run:

```bash
# 1. runtime deps + a real vendor/autoload.php (no phpstan, so it completes)
COMPOSER_HOME="$CH" composer install --prefer-source --no-dev --ignore-platform-req=ext-bcmath

# 2. PHPUnit in a throwaway project (same token-free trick)
cd /tmp/punit && COMPOSER_HOME="$CH" composer require --prefer-source --dev \
  "phpunit/phpunit:^10.5" --ignore-platform-req=ext-bcmath

# 3. re-add the Tests\ PSR-4 mapping the --no-dev install left out, then run
cd <repo> && COMPOSER_HOME="$CH" composer dump-autoload --dev
php /tmp/punit/vendor/bin/phpunit --bootstrap tests/bootstrap.php
```

Two gotchas: `--no-dev` omits the `Tests\` autoload-dev mapping, so `dump-autoload --dev`
(step 3) is required or every test dies with `Class "Tests\AbstractTestCase" not found`;
and plain `dump-autoload` inherits the last install's `--no-dev` state, so pass `--dev`
explicitly. Do **not** put a real-format GitHub token in `auth.json` — the proxy won't
rewrite it and every dist download then fails.

### MariaDB test database in the sandbox (Feature/Integration tests)

**MariaDB is the only supported test database** — the harness rejects any other driver
(`InteractsWithDatabase::db()` fails loud on a non-MariaDB driver, but on a *connection*
failure it `markTestSkipped`s). Unit tests run without a DB; Feature and Integration tests
need one. When the parent process can't connect they don't fail — they silently **skip**
(and any that don't gate on `db()` fall through to 307 login redirects). That silent-skip is
exactly the trap documented below: a "green" 200-skip run can mean the DB tests never ran.

Do **not** reinvent the setup each session. One idempotent script installs MariaDB, starts
it, (re)builds the `invoiceplane_test` schema from the setup SQL migrations, seeds the
baseline, and writes `ipconfig.php` — matching `.github/workflows/phpunit.yml` exactly:

```bash
bash tests/Support/sandbox-mariadb.sh          # provision (safe to re-run)
# Do NOT export DB_* here — see the gotcha below. The script writes ipconfig.php,
# and the parent phpunit process reads its DB config from there via env().
php /tmp/punit/vendor/bin/phpunit --bootstrap tests/bootstrap.php
```

Expected result once the DB parent connection actually works (see next gotcha):
**562 tests, ~17 skipped, 1298 assertions**, with **3 pre-existing failures** in
`Tests\Feature\Integrations\LetsPeppolFlowTest` (see "Pre-existing failures" below).
The ~17 skips are the genuine guards (snapshot / "requires running server" / manual
code-review). A run that reports **562 / 0 failures / ~200 skipped / 891 assertions**
is **not** green — it is the *masked* profile where the DB-backed integration tests
never ran (183 of them silently skipped). CI on prep/v180 currently shows exactly this
masked profile (verified: run 30726779008, `Skipped: 200`, MariaDB log full of
`Access denied for user ''@'…'`), so those integration tests provide **zero coverage in CI**.

Gotchas learned the hard way:
- `mysqld_safe` can be reaped in the sandbox; re-running the script restarts it. If a run
  suddenly shows ~192 *errors* (not failures), the DB died — restart and rebuild.
- **Do NOT export the `DB_*` vars before phpunit — exporting *breaks* the parent's DB
  connection in this sandbox.** The request subprocess runs in a clean env and reads
  `ipconfig.php` into `$_ENV` fine either way. But the phpunit *parent* resolves DB config
  through `env()`, which reads **only `$_ENV`**. This sandbox's PHP has `variables_order=GPCS`
  (no `E`), so exported vars land in `getenv()`/`$_SERVER` but never `$_ENV`; Dotenv's
  `createImmutable` then **skips** those keys (it sees them already set) and never copies the
  `ipconfig.php` values into `$_ENV`. Net effect: `env('DB_USERNAME')` returns `null`, the
  parent's `InteractsWithDatabase::db()` connects as `''@'localhost'` → *Access denied* →
  every DB-backed test calls `markTestSkipped`. Verified empirically: **without** the export
  `env('DB_HOSTNAME')` → `127.0.0.1` and the suite does 1298 assertions / 17 skips; **with**
  it → `null` and 891 assertions / 200 skips. Same mechanism hits CI, where `DB_*` is a
  job-level `env:` (so CI skips the 183 too). If you must have `DB_*` exported for other
  tooling, unset them just for phpunit: `env -u DB_HOSTNAME -u DB_PORT -u DB_DATABASE
  -u DB_USERNAME -u DB_PASSWORD php … phpunit …`.

Pre-existing failures (on a clean prep/v180, unrelated to any merge): the 3
`LetsPeppolFlowTest::it_returns_an_error_when_send_invoice_*` tests assume
`show_error()` becomes a catchable `RuntimeException`. That only holds in-process — under
the real `proc_open` request subprocess, `show_error()` renders a **500 error page** (body
`merchant_client_not_found`) and the child returns `exception: null`, so `expectException`
fails. These surface only once the parent DB connection works (otherwise they skip). They
are broken *test* assumptions, not app bugs — the controller behaves correctly.
- Session identity in the harness (`actingAsAdmin()`) must be **string-typed** (`user_type
  => '1'`), because `User_Controller` guards with `!== (string)$required_val` and a real
  DB-backed login stores strings. Int-typed session data silently redirects every admin
  request to the login page (307). This is wired correctly now — don't regress it.

#### Debugging a Feature-test request subprocess (do it right or you chase ghosts)

`AbstractTestCase::request()` spawns `tests/Integration/bin/request.php` via `proc_open`
with a **clean env** — only `CI_TEST_REQUEST` is passed, nothing inherited. To reproduce a
single request exactly as the suite does, match that clean env:

```bash
PAYLOAD=$(php -r 'echo base64_encode(json_encode(["method"=>"GET","uri"=>"/clients","query"=>[],"post"=>[],"session"=>["user_id"=>"1","user_type"=>"1","user_email"=>"admin@test.local","user_language"=>"system"],"ajax"=>false]));')
env -i CI_TEST_REQUEST="$PAYLOAD" php tests/Integration/bin/request.php   # <-- env -i is mandatory
```

Do **not** run it as `CI_TEST_REQUEST=… php …` from a shell where you exported `DB_*`: that
inline form leaks the exported vars into the child, and then a confusing chain fires —
Dotenv's `createImmutable` (in `bootstrap/kernel.php`) **skips** any key already present in
the OS env, CLI never copies the OS env into `$_ENV` (default `variables_order` has no `E`),
and kernel's `env()` reads **only** `$_ENV`. Net effect: `env('DB_HOSTNAME')` returns null
and you get a bogus "Unable to connect to the database" that never happens under the real
`proc_open` (clean-env) path. The clean-env child instead loads `ipconfig.php` into `$_ENV`
normally and connects fine.

To find *where* a 307 comes from, temporarily add one line to CI3's `redirect()`
(`vendor/pocketarc/codeigniter/system/helpers/url_helper.php`) dumping `$uri` +
`debug_backtrace()` to a tmp file, run the clean-env request above, then revert. That is how
the login-redirect (int session) vs. the app's own `clients/status/active` redirect were
told apart.

### Hard-blocked hosts in the sandbox (don't retry these)

The agent proxy does not just fail to authenticate for some hosts — it returns an outright
**403 policy denial** for `api.github.com` and `codeload.github.com`. Composer **dist**
(zipball) downloads go through those hosts, so there is no dist fallback at all — `--prefer-source`
(git through the proxy) is mandatory, never dist. `apt` is likewise blocked, so a missing PHP
extension like `ext-bcmath` cannot be installed — use `--ignore-platform-req=ext-bcmath`, never
"install the extension". Do not burn time probing these hosts; they will not start working.
(Note: plain `https://github.com/…/releases/download/…` **does** work through the outbound HTTPS
proxy — that's how the PHPStan phar is fetched below. Only the composer api/codeload path is blocked.)

### Static analysis (PHPStan) in the sandbox

Composer cannot install `phpstan/phpstan` here (dist-only phar behind the blocked hosts above),
but the release phar downloads fine via the HTTPS proxy:

```bash
curl -sSL -o /tmp/phpstan.phar https://github.com/phpstan/phpstan/releases/download/1.12.34/phpstan.phar
php /tmp/phpstan.phar analyse --memory-limit=1G     # config: phpstan.neon (level 0)
```

CI3 has **no PSR-4 autoloading or classmap**, so PHPStan needs help resolving symbols. The
committed config already wires this up — do not re-derive it:
- `bootstrapFiles: tests/Support/phpstan-bootstrap.php` defines the CI path constants and
  `require`s every `application/helpers/*_helper.php` (so helper functions resolve) plus
  `tests/Support/phpstan-ci-stubs.php` (side-effect-free signatures for CI *system* functions
  like `site_url`, `redirect`, `log_message`, `random_string`, …).
- `scanDirectories` covers `application/modules`, `core`, `libraries`, `third_party/MX` and
  `vendor/pocketarc/codeigniter/system/core` so HMVC classes/traits and base classes
  (`CI_Model`, `CI_Controller`, `MX_Controller`) resolve.
- Remaining genuinely-dynamic CI3 magic is suppressed by `identifier:`-based ignores
  (`property.notFound`, `method.notFound`, `class.extendsUnknownClass`, `class.nameCase`,
  `class.noParent`), and the handful of real legacy findings live in `phpstan-baseline.neon`.

A clean run is **`[OK] No errors`**. If you add code that references a new CI system function,
add its signature to `phpstan-ci-stubs.php` rather than baselining the `function.notFound`.

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
