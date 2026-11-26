/**
 * Browser smoke coverage for application/modules/settings versions route.
 * Mirrors tests/Feature/Core/VersionsControllerTest.php.
 */

import { test, expect } from '../test.js';

test.describe('Versions — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/settings/versions');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });
});
