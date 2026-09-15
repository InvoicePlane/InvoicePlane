/**
 * Browser smoke coverage for application/modules/layout/controllers/Layout.php.
 * Mirrors tests/Feature/Core/LayoutControllerTest.php.
 */

import { test, expect } from '../test.js';

test.describe('Layout — smoke', () => {
  test('it returns a successful response or redirect', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/dashboard');

    /* Assert */
    expect(response.status()).toBeLessThan(400);
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });
});

test.describe('Layout — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/dashboard');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });
});
