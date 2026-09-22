/**
 * Browser smoke coverage mirroring tests/Feature/Invoices/CronControllerTest.php
 * and tests/Feature/Invoices/InvoiceTaxRateServiceTest.php — both are thin
 * "the invoices area answers and never leaks a PHP error" checks.
 */

import { test, expect } from '../test.js';

test.describe('Invoices area — smoke', () => {
  test('it returns a successful response for an authenticated admin', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/invoices/status/all');

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).toContain('<html');
  });

  test('it does not expose php errors on the invoices list', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/invoices');
    const body = await response.text();

    /* Assert */
    expect(body).not.toMatch(/Fatal error|Uncaught|A PHP Error was encountered|<b>(Warning|Notice)<\/b>/i);
  });
});

test.describe('Invoices area — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/invoices');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });
});
