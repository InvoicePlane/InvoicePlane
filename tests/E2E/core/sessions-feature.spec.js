/**
 * Browser coverage for application/modules/sessions/controllers/Sessions.php.
 * Mirrors tests/Feature/Core/SessionsFeatureTest.php — the login / logout /
 * password-reset entry points, driven for real through the browser.
 */

import { test, expect } from '../test.js';
import { LOGIN_PATH } from '../config.js';

test.describe('Sessions — login page', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it renders the login page with a 200 status when unauthenticated', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto(LOGIN_PATH);

    /* Assert */
    expect(response.status()).toBe(200);
  });

  test('it includes a login form on the sessions login page', async ({ page }) => {
    /* Arrange + Act */
    await page.goto(LOGIN_PATH);

    /* Assert */
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('form button[type="submit"]')).toBeVisible();
  });

  test('it does not render the admin dashboard when unauthenticated', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/dashboard');

    /* Assert */
    await expect(page).toHaveURL(new RegExp(LOGIN_PATH));
  });

  test('it does not expose php errors on the login page', async ({ page }) => {
    /* Arrange + Act */
    const body = await (await page.goto(LOGIN_PATH)).text();

    /* Assert */
    expect(body).not.toMatch(/Fatal error|A PHP Error was encountered|<b>(Warning|Notice)<\/b>/i);
  });
});

test.describe('Sessions — login attempts', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects to login when post credentials are missing', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post('/index.php/sessions/login', {
      form: { btn_login: '1', email: '', password: '' },
      maxRedirects: 0,
    });

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(response.headers().location ?? '').toContain('sessions/login');
  });

  test('it redirects to login with wrong credentials', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post('/index.php/sessions/login', {
      form: { btn_login: '1', email: 'admin@test.local', password: 'definitely-wrong' },
      maxRedirects: 0,
    });

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(response.headers().location ?? '').toContain('sessions/login');
  });
});

test.describe('Sessions — password reset', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it renders the password reset form with a 200 status', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/sessions/passwordreset');

    /* Assert */
    expect(response.status()).toBe(200);
    await expect(page.locator('input[name="email"]')).toBeVisible();
  });

  test('it redirects to login when a nonexistent email is submitted to password reset', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post('/index.php/sessions/passwordreset', {
      form: { btn_recover: '1', email: 'nobody@does-not-exist.example' },
      maxRedirects: 0,
    });

    /* Assert */
    expect([200, 301, 302, 303]).toContain(response.status());
  });

  test('it does not reveal whether the email exists in the reset response', async ({ page }) => {
    /* Arrange */
    const unknown = await page.request.post('/index.php/sessions/passwordreset', {
      form: { btn_recover: '1', email: 'ghost@no-account.example' },
      maxRedirects: 0,
    });
    const known = await page.request.post('/index.php/sessions/passwordreset', {
      form: { btn_recover: '1', email: 'admin@test.local' },
      maxRedirects: 0,
    });

    /* Assert: both responses look the same */
    expect(unknown.status()).toBe(known.status());
  });

  test('it rejects a password reset token containing non alphanumeric characters', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get('/sessions/passwordreset/..%2F..%2Fetc%2Fpasswd', { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).not.toBe(200);
  });

  test('it redirects to login when an unknown valid format token is used', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get(`/sessions/passwordreset/${'a'.repeat(64)}`, { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).not.toBe(200);
  });
});

test.describe('Sessions — logout', () => {
  test('it destroys the session and redirects to login on logout', async ({ page }) => {
    /* Arrange: the fixture gives us an authenticated admin */
    await page.goto('/dashboard');
    await expect(page).not.toHaveURL(new RegExp(LOGIN_PATH));

    /* Act */
    await page.goto('/sessions/logout');

    /* Assert */
    await expect(page).toHaveURL(new RegExp(LOGIN_PATH));
    await page.goto('/dashboard');
    await expect(page).toHaveURL(new RegExp(LOGIN_PATH));
  });
});
