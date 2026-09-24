/**
 * Browser coverage mirroring tests/Feature/Core/ControllersAuthGuardTest.php —
 * an unauthenticated visitor is bounced off every admin module and no PHP error
 * leaks on the way.
 */

import { test, expect } from '../test.js';

test.use({ storageState: { cookies: [], origins: [] } });

const ADMIN_ROUTES = ['/clients', '/invoices', '/quotes', '/payments', '/products', '/projects', '/settings', '/users'];

test.describe('Auth guard — unauthenticated', () => {
  test('it redirects an unauthenticated visitor away from admin module', async ({ page }) => {
    /* Arrange + Act + Assert */
    for (const route of ADMIN_ROUTES) {
      await page.goto(route);
      await expect(page, `${route} should bounce a guest to login`).toHaveURL(/\/sessions\/login/);
    }
  });

  test('it does not expose php errors on an unauthenticated request to admin route', async ({ page }) => {
    /* Arrange + Act */
    const body = await (await page.goto('/settings')).text();

    /* Assert */
    expect(body).not.toMatch(/Fatal error|Uncaught|A PHP Error was encountered|<b>(Warning|Notice)<\/b>/i);
  });
});
