/**
 * Browser smoke coverage for the shared core AJAX surface.
 * Mirrors tests/Feature/Core/CoreAjaxControllerTest.php.
 */

import { test, expect } from '../test.js';

test.describe('Core AJAX — smoke', () => {
  test('it returns a successful response or redirect', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/clients/status/active');

    /* Assert */
    expect(response.status()).toBeLessThan(400);
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });
});

test.describe('Core AJAX — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/clients/status/active');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });
});
