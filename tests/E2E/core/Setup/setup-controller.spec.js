/**
 * Browser coverage for application/modules/setup/controllers/Setup.php.
 * Mirrors tests/Feature/Core/SetupControllerTest.php.
 *
 * The E2E server runs with SETUP_COMPLETED=true, so only the "setup is
 * locked" case runs here. The SETUP_COMPLETED=false paths are covered by
 * tests/Feature/Core/SetupControllerTest.php.
 */

import { test, expect } from '../../test.js';

const SETUP_ROUTES = ['/setup', '/setup/language', '/setup/prerequisites', '/setup/database', '/setup/upgrade_tables'];

test.describe('Setup — locked after completion', () => {
  test('it locks every http setup route after setup is completed', async ({ page }) => {
    /* Arrange + Act + Assert */
    for (const route of SETUP_ROUTES) {
      const response = await page.request.get(route, { maxRedirects: 0 });
      expect(response.status(), `${route} must be locked`).not.toBe(200);
    }
  });
});
