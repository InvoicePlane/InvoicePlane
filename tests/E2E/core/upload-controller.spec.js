/**
 * Browser smoke coverage for application/modules/upload/controllers/Upload.php.
 * Mirrors tests/Feature/Core/UploadControllerTest.php.
 */

import { test, expect } from '../test.js';

test.describe('Upload — smoke', () => {
  test('it returns a successful response or redirect', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/import');

    /* Assert */
    expect(response.status()).toBeLessThan(400);
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });
});

test.describe('Upload — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/import');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });
});
