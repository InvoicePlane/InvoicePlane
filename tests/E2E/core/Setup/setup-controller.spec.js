/**
 * Browser coverage for application/modules/setup/controllers/Setup.php.
 * Mirrors tests/Feature/Core/SetupControllerTest.php.
 *
 * The E2E server runs with SETUP_COMPLETED=true, so the "setup is locked" case
 * runs for real. The two cases that need SETUP_COMPLETED=false are
 */

import { test, expect } from '../test.js';

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

test.describe('Setup — unlocked flow (config dependent)', () => {
  test('it allows the setup flow when setup is explicitly unlocked', () => {

  test('it redirects direct setup steps to the wizard when setup is unlocked', () => {
});
