---
name: config-parity-guard
description: >-
  Catches the #1694 class of bug — a green test suite hiding a broken
  production configuration. Run before every release, and whenever you touch
  CSRF, auth guards, a controller's POST handler, ipconfig defaults, or the
  test harness. It proves that every state-changing endpoint is tested under
  the PRODUCTION config (CSRF on, real DB), not the permissive test config,
  and that no test silently skips its way out of coverage.
---

# config-parity-guard

## Why this exists

**#1694**: with `CSRF_PROTECTION=true` (the production default) every non-AJAX
state-changing POST behind `ensure_valid_post_request()` silently no-opped —
CodeIgniter's bootstrap `csrf_verify()` consumes `$_POST[_ip_csrf]` before the
controller's re-check runs, so `verify_csrf_token()` always saw an empty token
and bounced the request. You could not delete an invoice, save a setting,
delete a client — anything.

The suite was **green the whole time** (700 tests, 2000 assertions) because
the functional suite runs with `CSRF_PROTECTION=false`. The bug lived
entirely in the gap between the *test* config and the *production* config.

This skill exists so that gap can never hide a bug again. Two invariants:

1. **Config parity.** Every setting the test harness overrides away from its
   production default must have at least one test that runs at the production
   default and asserts real behaviour.
2. **No silent skips.** A test that `markTestSkipped()`s because of an
   *environmental* condition (DB down, extension missing, `env()` returned
   null) is coverage rot. It must fail loud instead, so a broken environment
   is a red build, not a quiet 200-skip "pass".

## How to run it

```
php .claude/skills/config-parity-guard/audit.php
```

Exit non-zero + a gap list = the release is not safe. Wire it into CI as a
required check and into the pre-release checklist. It is static (greps source
+ tests), needs no DB, runs in <1s.

## What it checks, and what YOU must verify by hand

### 1. Every guarded mutating endpoint has a production-config test (automated)

The script enumerates every controller action in
`application/modules/*/controllers/*.php` that

- calls `ensure_valid_post_request(` or `verify_csrf_token(`, **or**
- is named `delete*`, `save*`, `remove*`, `stop`, `approve*`, `reject*`,
  `create_*`, `update_*`, or is a `form(` handler that calls `->save(`,

and for each one requires a test that:

- **turns CSRF protection ON** — `enableCsrfProtection()` /
  `withEnvironment(['CSRF_PROTECTION' => 'true'])` in the same test class, AND
- **hits that exact route** with `postWithValidCsrfToken(` / a matching
  token+cookie pair, AND
- **asserts the mutation happened** — an `assertDatabase*` call in the body.

Plus a **negative** test on the same route: `postWithoutCsrfToken(` and an
assertion the mutation did **not** happen.

If the script lists an endpoint, write those two tests (pattern:
`tests/Feature/<Module>/<Resource>ControllerTest.php`, section
`Delete — CSRF regression (#1694)`; helper trait
`Tests\Concerns\PerformsCsrfProtectedRequests`).

### 2. Config-override parity (automated, best-effort)

The script greps the test tree for `withEnvironment([... => ...])` and
`putenv`/`ipconfig` writes, and compares each key against its production
default (`env_bool($KEY, 'true'|'false')` in `application/config/config.php`,
and `ipconfig.php.example`). For every key a test *lowers* below the
production default (CSRF_PROTECTION, COOKIE_SECURE, ENABLE_X_CONTENT_TYPE...,
DISABLE_SETUP, SETUP_COMPLETED, csrf_regenerate, ...), it requires at least
one test that also runs it at the production value.

If it flags a key: add a test class that sets that key to its production
value in `setUp()` and exercises the affected path.

### 3. Silent-skip audit (automated)

The script fails on any `markTestSkipped(` whose reason mentions:
`database`, `DB`, `connect`, `unavailable`, `env`, `extension`, `ext-`,
`not installed`, `driver`. Those are environment failures masquerading as
passes (see the 183 masked integration skips on prep/v180). Replace with
`self::fail(...)` — or fix the environment resolution — so the build goes
red.

Genuinely N/A skips (`@requires` a running browser, a manual code-review
placeholder, an OS-specific path) are allowed **only** if the reason string
contains the token `PARITY-OK:` — e.g.
`$this->markTestSkipped('PARITY-OK: needs a live Playwright server');`.

### 4. Things only a human catches — review these every release

- Does `tests/Integration/bin/request.php` pass the **production** ipconfig
  shape to the subprocess, or a test-tuned one? Any `$config[...]` the
  harness sets differently from `config.php` is a #1694 waiting to happen.
- Does CI export `DB_*` / `CSRF_*` / any `IP_*` at **job** level? If `env()`
  reads only `$_ENV` and `variables_order` lacks `E`, exported vars are
  invisible to the parent → masked skips. Keep them step-scoped or write
  ipconfig then `env -u`.
- New `env('SOME_NEW_FLAG')` in app code with no entry in
  `ipconfig.php.example` → undocumented config, add it + a parity test.
- A new controller that does `$this->load->library('security')` and rolls
  its own token check instead of `ensure_valid_post_request()` — the script
  keys on those two calls; a bespoke check slips past. Grep new controllers
  for `csrf`, `token`, `_ip_csrf` by hand.

## Fixing a gap

1. Add the CSRF-on positive + negative test to the resource's
   `*ControllerTest` (trait `PerformsCsrfProtectedRequests`).
2. Re-run `php .claude/skills/config-parity-guard/audit.php` — expect `[OK]`.
3. Run the affected `*ControllerTest` through ivpldock
   (`make docker-test FILTER=...`) to confirm the new tests pass against the
   real DB.
