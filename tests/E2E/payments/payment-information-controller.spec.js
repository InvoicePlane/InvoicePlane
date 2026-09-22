/**
 * Browser smoke coverage mirroring
 * tests/Feature/Payments/PaymentInformationControllerTest.php,
 * PaypalControllerTest.php and StripeControllerTest.php — thin "the payments
 * area answers and never leaks a PHP error" checks. The gateway controllers
 * expose no admin-facing routes of their own beyond the shared payments list.
 */

import { test, expect } from '../test.js';

test.describe('Payments area — smoke', () => {
  test('it returns a successful response or redirect', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/payments');

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).toContain('<html');
  });

  test('it does not expose php errors on the payments list', async ({ page }) => {
    /* Arrange + Act */
    const body = await (await page.goto('/payments')).text();

    /* Assert */
    expect(body).not.toMatch(/Fatal error|Uncaught|A PHP Error was encountered|<b>(Warning|Notice)<\/b>/i);
  });
});

test.describe('Payments area — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/payments');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });
});
