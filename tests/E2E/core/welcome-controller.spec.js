/**
 * Browser smoke coverage for application/modules/welcome/controllers/Welcome.php.
 * Mirrors tests/Feature/Core/WelcomeControllerTest.php.
 */

import { test, expect } from '../test.js';

test.describe('Welcome — smoke', () => {
  test('it displays welcome page', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/welcome');

    /* Assert */
    expect(response.status()).toBeLessThan(400);
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });
});
