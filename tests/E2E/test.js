import { test as base, expect } from '@playwright/test';
import { resetDatabase } from './support/db.js';
import { ensureAdminSession } from './support/auth.js';

/**
 * PHP errors / exceptions detected by their rendered HTML body patterns.
 * These patterns match CodeIgniter 3's built-in error views rendered when
 * display_errors is truthy. When display_errors is off, CI3 sets a 500 status
 * but leaves the body empty — so we also monitor the 500 status signal.
 */
const PHP_ERROR_PATTERN =
  /A PHP Error was encountered|An uncaught Exception was encountered|Fatal error|Uncaught|<b>(Warning|Notice|Deprecated|Error)<\/b>/i;

/**
 * Exception page HTML signature — the red-bordered div that CI3's
 * error_exception.php wraps around the uncaught exception display.
 * Used as a fallback detection signal when display_errors=0 leaves the
 * body empty but CI3 still sets a 500 status.
 */
const EXCEPTION_PAGE_PATTERN =
  /border-style.*solid.*#990000|An uncaught Exception was encountered|div.*border-1.*solid.*#990/;

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
    const pageErrorSignals = [];

    page.on('console', (msg) => {
      if (msg.type() === 'error') errors.push(`[console] ${msg.text()}`);
    });
    page.on('pageerror', (err) => {
      errors.push(`[pageerror] ${err.message}`);
    });
    page.on('response', (response) => {
      // CI3 sets this status header unconditionally on a fatal error or
      // uncaught exception, regardless of display_errors. Check for it
      // only for the page's own browser-driven traffic; page.request.*()
      // calls (e.g. PayPal/Stripe's own gateway-unreachable 500 — a real,
      // expected, already-asserted-on outcome in those tests) go through
      // a separate request context and never reach this listener.
      if (response.status() >= 500) {
        // Distinguish expected vs unexpected 500s:
        // - If the response is HTML and contains an exception pattern, it's
        //   an application error.
        // - If the response is HTML but has no exception pattern and the
        //   body is short, it may be a display_errors=0 scenario where the
        //   body was intentionally cleared; still capture it for review.
        // - Expected 500s (e.g. gateway failures) should opt out with
        //   test.use({ allowBrowserErrors: true }) at the test level.
        const contentType = response.headers()['content-type'] || '';
        const bodyPromise = response.text().then((body) => {
          const bodySignals = [];

          // 1. Check for standard PHP error / exception patterns in body
          if (PHP_ERROR_PATTERN.test(body)) {
            bodySignals.push(`[php-error] ${response.status()} ${response.url()}`);
          }

          // 2. Check for CI3 exception page CSS signature (red border) as
          //    fallback when display_errors=0 leaves body otherwise empty
          //    but the exception template is still rendered.
          if (EXCEPTION_PAGE_PATTERN.test(body)) {
            bodySignals.push(`[exception-page] ${response.status()} ${response.url()}`);
          }

          if (bodySignals.length > 0) {
            errors.push(...bodySignals);
          } else if (body.length > 0 && body.trim().length < 500) {
            // 3. Body is present but doesn't match error patterns and is short,
            //    likely a non-error 500 or empty error page — record for review
            bodySignals.push(`[http-500-empty-body] ${response.status()} ${response.url()}: body=${body.substring(0, 200)}`);
            errors.push(...bodySignals);
          }

          pageErrorSignals.push({ url: response.url(), status: response.status(), bodySignals });
        });

        const contentTypeCheck = !contentType.includes('text/html')
          ? response
              .text()
              .then((body) => {
                // Non-HTML 500 response — still check for error patterns
                if (PHP_ERROR_PATTERN.test(body)) {
                  errors.push(`[php-error-non-html] ${response.status()} ${response.url()}`);
                }
              })
              .catch(() => {});

        responseChecks.push(contentTypeCheck);
      }

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
    });

    await use(page);
  },
});

export { expect };