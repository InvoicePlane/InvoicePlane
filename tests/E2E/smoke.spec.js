import { test, expect } from './test.js';
import { LOGIN_PATH } from './config.js';

/**
 * A small end-to-end smoke suite for the CI3 InvoicePlane app. Every test
 * except the login-page one runs with the admin session restored by
 * global-setup.js (playwright.config.js `storageState`).
 *
 * These are intentionally shallow — they prove the E2E harness (server,
 * login, session reuse, reporter) is wired correctly and that the core
 * pages render for a signed-in admin. Deeper journeys belong in their own
 * spec files next to this one.
 */

test.describe('unauthenticated', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('the login page renders the sign-in form', async ({ page }) => {
    await page.goto(LOGIN_PATH);

    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('form button[type="submit"]')).toBeVisible();
  });

  test('a protected page redirects a guest to the login form', async ({ page }) => {
    await page.goto('/dashboard');

    await expect(page).toHaveURL(new RegExp(LOGIN_PATH));
  });
});

test.describe('authenticated admin', () => {
  test('reaches the dashboard', async ({ page }) => {
    await page.goto('/dashboard');

    await expect(page).not.toHaveURL(new RegExp(LOGIN_PATH));
    await expect(page.locator('#headerbar, .navbar')).toBeVisible();
  });

  test('opens the e-invoice integrations settings page', async ({ page }) => {
    await page.goto('/integrations/settings');

    await expect(page).not.toHaveURL(new RegExp(LOGIN_PATH));
    await expect(page.locator('#content')).toBeVisible();
  });

  test('lists the e-invoice provider registry', async ({ page }) => {
    await page.goto('/integrations/providers');

    await expect(page).not.toHaveURL(new RegExp(LOGIN_PATH));
    await expect(page.locator('body')).toContainText(/letspeppol|qonto|superpdp/i);
  });
});
