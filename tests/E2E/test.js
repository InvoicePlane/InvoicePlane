import { test as base, expect } from '@playwright/test';
import { resetDatabase } from './support/db.js';
import { ensureAdminSession } from './support/auth.js';

/**
 * PHP warnings/notices/deprecations/uncaught exceptions that CodeIgniter's
 * own error views render inline into an HTML response. These are plain page
 * text — no JS console event or uncaught-exception event fires for them, so
 * the console/pageerror listeners below are structurally blind to them.
 *
 * This alone is NOT sufficient for the fatal/uncaught-exception case in this
 * environment: bootstrap/kernel.php sets display_errors=0 for CI_ENV=production
 * (what E2E's ipconfig.php uses), and CI3's own _error_handler()/
 * _exception_handler() (vendor/pocketarc/.../Common.php) only render
 * "A PHP Error was encountered" / "An uncaught Exception was encountered"
 * when display_errors is truthy — with it off, they still unconditionally
 * set_status_header(500) and exit(1), but the body stays whatever it already
 * was (usually empty). That's why the response listener below also checks
 * status >= 500 directly, which is the one signal CI3 sets unconditionally
 * regardless of display_errors. Keep both: the text pattern still catches a
 * dev-mode run (display_errors on) or a show_error()/show_404() call that
 * doesn't 500, and it's what a real PHP notice/warning would look like if
 * display_errors were ever on here.
 */
const PHP_ERROR_PATTERN =
  /A PHP Error was encountered|An uncaught Exception was encountered|Fatal error|Uncaught|<b>(Warning|Notice|Deprecated|Error)<\/b>/i;

/**
 * Drop-in replacement for `import { test, expect } from '@playwright/test'`
 * — every test that imports from here automatically captures console errors,
 * uncaught page exceptions, and PHP errors rendered into any HTML response,
 * records them as annotations on the test result (which is what makes them
 * show up in error-summary-reporter.js's end-of-run report), and fails the
 * test if any occurred.
 *
 * None of these are ever something a passing test should have produced — it
 * means either the page under test is broken in a way the test's own
 * assertions didn't happen to catch, or the test itself is exercising the
 * wrong account/state (see e.g. the 2026-09-25 fix to login-security.spec.js,
 * where a 403 from a misconfigured test fixture sat invisible in the
 * annotation-only report for who knows how long). Silently recording these
 * instead of failing on them defeats the point of collecting them at all.
 *
 * A specific test that must exercise a real, expected browser-side or
 * server-rendered error (e.g. asserting the UI's own handling of a failed
 * request) opts out with `test.use({ allowBrowserErrors: true })` —
 * narrowly, at the test or describe level, never suite-wide.
 *
 * (Ported from the InvoicePlane v2 E2E suite, which left the console/
 * pageerror half of this non-blocking and had no HTML-body check at all;
 * that default was outgrown, not preserved.)
 */
export const test = base.extend({
  /**
   * Truncate + reseed the app database before every test, mirroring the
   * PHPUnit Feature suite's per-test reset. Auto-runs; opt out for a rare
   * multi-step spec with `test.use({ freshDatabase: false })`.
   */
  freshDatabase: [true, { option: true }],

  /** Opt out of the browser/PHP-error-fails-the-test default; see above. */
  allowBrowserErrors: [false, { option: true }],

  _resetDatabase: [
    async ({ freshDatabase }, use) => {
      if (freshDatabase) {
        resetDatabase();
        // Runs before the test's browser context is created, so a refreshed
        // storageState file is picked up by this test, not the next one.
        await ensureAdminSession();
      }
      await use();
    },
    { auto: true },
  ],

  page: async ({ page, allowBrowserErrors }, use, testInfo) => {
    const errors = [];
    const responseChecks = [];

    page.on('console', (msg) => {
      if (msg.type() === 'error') errors.push(`[console] ${msg.text()}`);
    });
    page.on('pageerror', (err) => {
      errors.push(`[pageerror] ${err.message}`);
    });
    page.on('response', (response) => {
      // The one status CI3 sets unconditionally on a fatal error or uncaught
      // exception, regardless of display_errors — see the comment above
      // PHP_ERROR_PATTERN. Only for the page's own browser-driven traffic;
      // page.request.*() calls (e.g. PayPal/Stripe's own gateway-unreachable
      // 500 — a real, expected, already-asserted-on outcome in those tests)
      // go through a separate request context and never reach this listener.
      if (response.status() >= 500) {
        errors.push(`[http-500] ${response.status()} ${response.url()}`);
      }

      const contentType = response.headers()['content-type'] || '';
      if (!contentType.includes('text/html')) return;

      responseChecks.push(
        response
          .text()
          .then((body) => {
            if (PHP_ERROR_PATTERN.test(body)) {
              errors.push(`[php-error] ${response.status()} ${response.url()}`);
            }
          })
          // Body can be unavailable for a request that was redirected/aborted
          // mid-flight — nothing to check in that case, not a test failure.
          .catch(() => {}),
      );
    });

    await use(page);

    // Response bodies are read asynchronously off the 'response' event, which
    // isn't awaited by Playwright — without this, a PHP error on the test's
    // last page load could still be mid-read when we check `errors` below.
    await Promise.all(responseChecks);

    for (const description of errors) {
      testInfo.annotations.push({ type: 'browser-error', description });
    }

    if (errors.length > 0 && !allowBrowserErrors) {
      throw new Error(
        `${errors.length} browser/PHP error(s) occurred during this test:\n\n` +
          errors.join('\n') +
          '\n\nIf this specific error is genuinely expected here, opt out with ' +
          'test.use({ allowBrowserErrors: true }) — do not disable this suite-wide.',
      );
    }
  },
});

export { expect };
