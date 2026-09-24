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
    expect(body).toContain('<title>');
    expect(body).toContain('Dashboard');
    expect(body.length).toBeGreaterThan(500);
  });

  test('it includes navigation elements on the dashboard', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/dashboard');

    /* Assert */
    await expect(page.locator('.navbar')).toBeVisible();
  });

  test('it includes the clients section link on the dashboard', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/dashboard');

    /* Assert: scoped to the always-visible Quick Actions panel — the
     * unscoped selector also matches the navbar's "Add Client" dropdown
     * item, which is hidden until its toggle is clicked. */
    await expect(page.locator('#panel-quick-actions a[href*="/clients"]').first()).toBeVisible();
  });

  test('it renders without exposing php errors', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/dashboard');
    const body = await response.text();

    /* Assert */
    expect(response.status()).toBe(200);
    expect(body).toContain('Dashboard');
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
    expect(first).toBe(200);
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
