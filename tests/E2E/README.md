# End-to-end tests (Playwright)

Browser tests for the CI3 InvoicePlane app. The suite mirrors the PHPUnit
Feature suite: for every `tests/Feature/<Module>/<Name>ControllerTest.php` there
is a `tests/E2E/<module>/<name>-controller.spec.js` whose tests match the PHPUnit
methods one-for-one — same scenario, same `/* Arrange */ /* Act */ /* Assert */`
shape, proven through the rendered UI (or, for AJAX/destroy-only routes, an
authenticated request) instead of an in-process request.

Frontend-test conventions: lowercase kebab-case file and directory names;
`test.describe('<Module> — <area>')` grouping; test titles read `it …` to match
the PHPUnit method they mirror.

## Layout

```
tests/E2E/
├── <Module>/<Name>Controller.spec.js   one spec file per Feature-test file
├── support/
│   ├── app.js        URL/path helpers (tolerates the /index.php prefix)
│   ├── db.js         resetDatabase() — truncate + reseed before every test
│   ├── fixtures.js   createClient(), createSecondaryUser(), uniq()
│   ├── forms.js      fillByName(), save(), expect{Saved,Error}Flash(), expectBlockedByRequired()
│   └── http.js       postForm(), readCsrfToken(), getJson() for non-UI routes
├── config.js         env-driven base URL / credentials / login paths
├── router.php        front controller for `php -S`
├── global-setup.js   resets the DB, logs in once, writes .auth/admin.json
├── test.js           `import { test, expect } from './test.js'` — adds the
│                      per-test DB reset + console/pageerror capture
├── error-summary-reporter.js
└── smoke.spec.js     harness sanity checks
```

Import `test`/`expect` from `../test.js`, **not** `@playwright/test` — that
wrapper is what runs the per-test database reset and the browser-error capture.

## Isolation model

Every test runs `tests/Support/reset-test-db.php` first (truncate all tables +
`ip_seed_baseline`), exactly like `InteractsWithDatabase` does for the PHPUnit
suite. Consequences, all reflected in `playwright.config.js`:

- **`workers: 1`, `fullyParallel: false`** — one app instance, one database,
  serial tests. PHP's built-in server also handles one request at a time.
- Specs assert on their own `uniq()`-suffixed data, never on absolute row counts
  or list position.
- The admin session in `.auth/admin.json` survives a reseed (the reseeded admin
  is still `user_id` 1). It does **not** survive a request in which
  `User_Controller` calls the raw `session_destroy()` (a non-admin hitting an
  admin route) — under the single-process dev server that tears down the shared
  session, so those specs re-authenticate in a fresh context.

## Run it

```bash
yarn install            # or npm install — pulls @playwright/test
yarn e2e:install        # one-time: download the Chromium binary
yarn e2e                # or: yarn e2e:ui
```

With nothing configured, `playwright.config.js` starts the app itself with:

```
DB_HOSTNAME=${DB_HOSTNAME:-127.0.0.1} php -d variables_order=EGPCS -S localhost:8000 -t . tests/E2E/router.php
```

- `-d variables_order=EGPCS` — this machine's `php.ini` omits `E`, so without it
  exported vars never reach `$_ENV` and InvoicePlane's `env()` can't read
  `DB_HOSTNAME` (it would fall back to the Docker-only `mariadb` host in
  `ipconfig.php` and fail to boot).
- `DB_HOSTNAME` defaults to `127.0.0.1` (the host-published MariaDB port);
  export it if your DB is elsewhere. `tests/Support/reset-test-db.php` reads the
  same `DB_*` variables.

It needs a schema-built MariaDB reachable on that host — the same one the PHPUnit
suite uses. If you only have the ivpldock stack, the DB is already there on
`127.0.0.1:3306`.

### Point at an already-running instance

```bash
E2E_BASE_URL=http://localhost:8000 DB_HOSTNAME=127.0.0.1 npm run e2e
```

When `E2E_BASE_URL` (or `CI`) is set the config does not start its own server.
`DB_HOSTNAME` is still needed for the per-test reset.

## CSRF

The E2E server runs with `CSRF_PROTECTION=false` (local `ipconfig.php` and the
CI workflow both set it). The form-driven tests still submit the real
`_csrf_field()` hidden input; the few direct `postForm()` calls rely on it being
off. The `#1694` CSRF-regression cases are therefore `test.skip`ped here and stay
covered by the PHPUnit Feature suite. Wiring a second Playwright project against a
`CSRF_PROTECTION=true` server would let them run here too.

## Progress

| Module | Spec files | E2E status |
| --- | --- | --- |
| clients | clients-controller, clients-ajax-controller, user-clients-controller | ✅ 35 tests |
| products | families-, products-, products-ajax-, units-controller | ✅ 40 tests |
| projects | projects-, services-, tasks-, tasks-ajax-controller | ✅ 41 tests |
| quotes | quotes-, quotes-ajax-controller | ✅ 12 tests |
| invoices | invoice-groups-, invoices-, invoices-ajax-, recurring-, guest-view-, guest-get-, cron-, cron-recur-controller | ✅ ~100 tests |
| payments | payment-methods-, payments-, payments-ajax-, payment-information-, payment-information-form, payment-provider-allowlist, guest-payments-, paypal-, paypal-flow, stripe-, stripe-flow | ✅ 55 tests |
| core | 41 files — CRUD (tax-rates, email-templates, custom-fields, custom-values, users, settings), auth (sessions, login-security, password-reset), smoke (dashboard x2, reports, mailer x2, import, upload, layout, welcome, versions, view-template-system, controllers-auth-guard, settings-ajax), filter-ajax, integrations x2, setup x2, csrf-mutation-parity, the 3 e-invoice provider flows + 4 transmission mirrors | ✅ complete |
| security | csrf-delete-security, develop-merge-security, security-regression | ✅ complete |

**All 7 modules done.** Config-dependent / gateway-dependent cases across the
suite are `test.fixme` with a pointer to the PHPUnit mirror: CSRF-on parity, the
PayPal/Stripe/e-invoice provider *response* assertions, `SETUP_COMPLETED=false`,
CLI-only behaviour, `SessionsSecurityTest`'s pure helper math, and the SUMEX
storage/config checks.

Gateway-response tests (PayPal/Stripe order + capture + callback) are
`test.fixme` — they need the outbound `PHP → gateway` HTTP call stubbed, which
Playwright can't intercept; they stay covered by the PHPUnit fakes. The guard
clauses (method / key / status / already-paid) run for real. The e-invoice flow
files in `core/` (SuperPdp / Qonto / LetsPeppol) will follow the same split.

Module mapping: `Core, Clients, Invoices, Payments, Products, Projects, Quotes`
are the real modules; **Security** and **Integrations** (1.8.0) fold into Core,
**Services** and **Tasks** into Projects.

Each pending module follows the Clients pattern: read the Feature file, read the
matching `application/modules/<m>/` controller + views for field names / routes /
flash text, then write one `test()` per `#[Test]` method.

## Run through the Docker stack

`tests/E2E/docker-e2e.sh [playwright args]` (or `npm run e2e:docker`) serves the
app from inside the `ivpldock-workspace-1` container — the same stack
`make docker-test` uses — and runs Playwright + the per-test DB reset from the
host against it. It resolves the container IP, starts `php -S` there with
`IP_URL` / `COOKIE_SECURE=false` / `variables_order=EGPCS` set, and points
`E2E_BASE_URL` at it.

## Helpers beyond the browser

`support/db.js` also exports `dbInsert(table, row)` / `dbQuery(sql)` (via
`tests/Support/e2e-sql.php`) — the E2E equivalent of the PHPUnit suite's
`databaseInsert` / `assertDatabase*`, for setup rows and assertions with no clean
UI path (quote/invoice tax-rate rows, client-service links, orphaned tasks).
`support/forms.js` has `expectErrorFlash` (controller flashdata,
`.alert-danger[role=alert]`) vs `expectValidationError` (CI3
`validation_errors()`, a plain `.alert-danger`).

## CI

`.github/workflows/e2e-tests.yml` — MariaDB service, schema + seed, `php -S`,
`npx playwright install --with-deps chromium`, `npm run e2e`. Runs on
`prep/v180` pushes and PRs; the HTML report + `error-report.md` are uploaded as
an artifact.
