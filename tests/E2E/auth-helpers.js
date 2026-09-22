/**
 * Authentication helpers for E2E tests.
 *
 * Every test starts with a pre-authenticated session restored from
 * global-setup.js's storageState (tests/E2E/.auth/admin.json) — no per-test
 * login is needed. Reach for these only when a test exercises the auth flow
 * itself, or needs an explicitly unauthenticated page.
 */

import { E2E_EMAIL, E2E_PASSWORD, LOGIN_PATH, LOGOUT_PATH } from './config.js';

/**
 * Submit InvoicePlane's login form. The form (application/modules/sessions/
 * views/session_login.php) posts to itself with name="email" / name="password"
 * fields and a hidden btn_login flag; a successful login redirects away from
 * the login route.
 */
export async function login(page, email = E2E_EMAIL, password = E2E_PASSWORD) {
  await page.goto(LOGIN_PATH);
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', password);
  await Promise.all([
    page.waitForURL((url) => !url.pathname.includes(LOGIN_PATH)),
    page.click('form button[type="submit"]'),
  ]);
}

export async function logout(page) {
  await page.goto(LOGOUT_PATH);
  await page.waitForURL((url) => url.pathname.includes(LOGIN_PATH));
}

export async function isAuthenticated(page) {
  try {
    const response = await page.goto('/dashboard');

    return !page.url().includes(LOGIN_PATH) && (response?.ok() ?? false);
  } catch {
    return false;
  }
}
