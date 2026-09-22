/**
 * Browser coverage for application/modules/dashboard/controllers/Dashboard.php.
 * Mirrors tests/Feature/Core/DashboardControllerTest.php.
 */

import { test, expect } from '../test.js';

test.describe('Dashboard — authenticated admin', () => {
  test('it displays dashboard with a 200 status', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/dashboard');

    /* Assert */
    expect(response.status()).toBe(200);
  });

  test('it renders a full html document on the dashboard', async ({ page }) => {
    /* Arrange + Act */
    const body = await (await page.goto('/dashboard')).text();

    /* Assert */
    expect(body).toContain('<html');
    expect(body).toContain('</html>');
    expect(body.length).toBeGreaterThan(500);
  });

  test('it includes navigation elements on the dashboard', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/dashboard');

    /* Assert */
    await expect(page.locator('#headerbar, .navbar, nav')).toBeVisible();
  });

  test('it includes the clients section link on the dashboard', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/dashboard');

    /* Assert */
    await expect(page.locator('a[href*="/clients"]').first()).toBeAttached();
  });

  test('it does not expose php errors on the dashboard', async ({ page }) => {
    /* Arrange + Act */
    const body = await (await page.goto('/dashboard')).text();

    /* Assert */
    expect(body).not.toMatch(/Fatal error|Uncaught|A PHP Error was encountered|<b>(Warning|Notice)<\/b>/i);
  });

  test('it does not display invoice form content on the dashboard', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/dashboard');

    /* Assert */
    await expect(page.locator('#invoice_number')).toHaveCount(0);
  });

  test('it produces a deterministic dashboard response on two consecutive requests', async ({ page }) => {
    /* Arrange + Act */
    const first = (await page.request.get('/dashboard')).status();
    const second = (await page.request.get('/dashboard')).status();

    /* Assert */
    expect(first).toBe(second);
  });
});

test.describe('Dashboard — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest away from the dashboard', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/dashboard');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });

  test('it does not expose php errors on the login redirect', async ({ page }) => {
    /* Arrange + Act */
    const body = await (await page.goto('/dashboard')).text();

    /* Assert */
    expect(body).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });
});
