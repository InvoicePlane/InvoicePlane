/**
 * Browser coverage mirroring tests/Feature/Core/DashboardFeatureTest.php — the
 * dashboard renders a complete, deterministic, error-free document for an
 * authenticated admin and is closed to guests.
 */

import { test, expect } from '../test.js';

test.describe('Dashboard feature — authenticated admin', () => {
  test('it renders the dashboard with a 200 status', async ({ page }) => {
    /* Arrange + Act + Assert */
    expect((await page.goto('/dashboard')).status()).toBe(200);
  });

  test('it renders a full html document on the dashboard', async ({ page }) => {
    /* Arrange + Act */
    const body = await (await page.goto('/dashboard')).text();

    /* Assert */
    expect(body).toContain('<html');
    expect(body).toContain('</html>');
  });

  test('it includes navigation elements on the dashboard', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/dashboard');

    /* Assert */
    await expect(page.locator('#headerbar, .navbar, nav')).toBeVisible();
  });

  test('it does not expose php errors on the dashboard', async ({ page }) => {
    /* Arrange + Act */
    const body = await (await page.goto('/dashboard')).text();

    /* Assert */
    expect(body).not.toMatch(/Fatal error|Uncaught|A PHP Error was encountered|<b>(Warning|Notice)<\/b>/i);
  });

  test('it produces a deterministic dashboard response on two consecutive requests', async ({ page }) => {
    /* Arrange + Act */
    const a = (await page.request.get('/dashboard')).status();
    const b = (await page.request.get('/dashboard')).status();

    /* Assert */
    expect(a).toBe(b);
  });
});

test.describe('Dashboard feature — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest away from the dashboard', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/dashboard');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });
});
