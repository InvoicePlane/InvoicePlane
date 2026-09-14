/**
 * Browser smoke coverage mirroring tests/Feature/Payments/PaypalControllerTest.php
 * — the PayPal gateway module registers no admin route of its own, so this is
 * the shared payments-area health check.
 */

import { test, expect } from '../test.js';

test.describe('PayPal gateway — smoke', () => {
  test('it returns a successful response or redirect', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/payments');

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).toContain('<html');
  });
});

test.describe('PayPal gateway — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/payments');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });
});
