import { test as base, expect } from '@playwright/test';
import { resetDatabase } from './support/db.js';
import { ensureAdminSession } from './support/auth.js';

/**
 * Drop-in replacement for `import { test, expect } from '@playwright/test'`
 * — every test that imports from here automatically captures console errors
 * and uncaught page exceptions, records them as annotations on the test
 * result (which is what makes them show up in error-summary-reporter.js's
 * end-of-run report), and fails the test if any occurred.
 *
 * A browser console/page error is essentially never something a passing test
 * should have produced — it means either the page under test is broken in a
 * way the test's own assertions didn't happen to catch, or the test itself is
 * exercising the wrong account/state (see e.g. the 2026-09-25 fix to
 * login-security.spec.js, where a 403 from a misconfigured test fixture sat
 * invisible in the annotation-only report for who knows how long). Silently
 * recording these instead of failing on them defeats the point of collecting
 * them at all.
 *
 * A specific test that must exercise a real, expected browser-side error
 * (e.g. asserting the UI's own handling of a failed request) opts out with
 * `test.use({ allowConsoleErrors: true })` — narrowly, at the test or
 * describe level, never suite-wide.
 *
 * (Ported from the InvoicePlane v2 E2E suite, which left this non-blocking;
 * that default was outgrown, not preserved.)
 */
export const test = base.extend({
  /**
   * Truncate + reseed the app database before every test, mirroring the
   * PHPUnit Feature suite's per-test reset. Auto-runs; opt out for a rare
   * multi-step spec with `test.use({ freshDatabase: false })`.
   */
  freshDatabase: [true, { option: true }],

  /** Opt out of the console/page-error-fails-the-test default; see above. */
  allowConsoleErrors: [false, { option: true }],

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

  page: async ({ page, allowConsoleErrors }, use, testInfo) => {
    const errors = [];

    page.on('console', (msg) => {
      if (msg.type() === 'error') errors.push(`[console] ${msg.text()}`);
    });
    page.on('pageerror', (err) => {
      errors.push(`[pageerror] ${err.message}`);
    });

    await use(page);

    for (const description of errors) {
      testInfo.annotations.push({ type: 'browser-error', description });
    }

    if (errors.length > 0 && !allowConsoleErrors) {
      throw new Error(
        `${errors.length} browser console/page error(s) occurred during this test:\n\n` +
          errors.join('\n') +
          '\n\nIf this specific error is genuinely expected here, opt out with ' +
          'test.use({ allowConsoleErrors: true }) — do not disable this suite-wide.',
      );
    }
  },
});

export { expect };
