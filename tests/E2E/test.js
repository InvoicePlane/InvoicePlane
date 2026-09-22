import { test as base, expect } from '@playwright/test';
import { resetDatabase } from './support/db.js';
import { ensureAdminSession } from './support/auth.js';

/**
 * Drop-in replacement for `import { test, expect } from '@playwright/test'`
 * — every test that imports from here automatically captures console errors
 * and uncaught page exceptions and records them as annotations on the test
 * result, which is what makes them show up in error-summary-reporter.js's
 * end-of-run report.
 *
 * This does NOT fail a test just because a browser-side error occurred —
 * most tests aren't about error-freedom, and auto-failing on console noise
 * would produce false positives unrelated to what the test checks. Tests
 * that specifically need to assert "no errors occurred" wire up their own
 * local listeners and assert on them directly. This fixture is for
 * *visibility* across the whole suite, including tests that never thought to
 * check — which is exactly where a silent error would otherwise hide.
 *
 * (Ported unchanged from the InvoicePlane v2 E2E suite; it is
 * framework-agnostic.)
 */
export const test = base.extend({
  /**
   * Truncate + reseed the app database before every test, mirroring the
   * PHPUnit Feature suite's per-test reset. Auto-runs; opt out for a rare
   * multi-step spec with `test.use({ freshDatabase: false })`.
   */
  freshDatabase: [true, { option: true }],

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

  page: async ({ page }, use, testInfo) => {
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
  },
});

export { expect };
