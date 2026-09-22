/**
 * Browser coverage for application/modules/setup/controllers/Cli.php.
 * Mirrors tests/Feature/Core/SetupCliControllerTest.php.
 *
 * Only the "HTTP access is denied" case has a browser surface — the other two
 * exercise CLI-invoked behaviour (creating / skipping a default admin) and stay
 * test.fixme, covered by SetupCliControllerTest.
 */

import { test, expect } from '../test.js';

test.describe('Setup CLI — HTTP guard', () => {
  test('it denies http access to the cli controller', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get('/setup/cli/create_default_user', { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).toBe(403);
  });
});

test.describe('Setup CLI — command behaviour (CLI only)', () => {
  test('it creates a default admin user when none exist', () => {
    test.fixme(true, 'CLI-invoked behaviour — covered by SetupCliControllerTest');
  });

  test('it skips creating a default admin user when one already exists', () => {
    test.fixme(true, 'CLI-invoked behaviour — covered by SetupCliControllerTest');
  });
});
