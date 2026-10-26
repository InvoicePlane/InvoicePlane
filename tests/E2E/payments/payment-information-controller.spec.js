/**
 * Browser smoke coverage mirroring
 * tests/Feature/Payments/PaymentInformationControllerTest.php,
 * PaypalControllerTest.php and StripeControllerTest.php — thin "the payments
 * area answers and never leaks a PHP error" checks. The gateway controllers
 * expose no admin-facing routes of their own beyond the shared payments list.
 */

import { test, expect } from '../test.js';

test.describe('Payments area — smoke', () => {
  test('it loads the payments page successfully', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/payments');
    const body = await response.text();

    /* Assert */
    expect(response.status()).toBe(200);
    expect(body).toContain('Payment');
  });

  test('it renders without exposing php errors', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/payments');
    const body = await response.text();

    /* Assert */
    expect(response.status()).toBe(200);
    expect(body).toContain('Payment');
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
