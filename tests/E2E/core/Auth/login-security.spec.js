/**
 * Browser coverage for the login hardening in
 * application/modules/sessions/controllers/Sessions.php + Mdl_sessions::auth().
 * Mirrors tests/Feature/Core/LoginSecurityTest.php.
 */

import { test, expect } from '../../test.js';
import { createSecondaryUser } from '../../support/fixtures.js';
import { dbExec, dbQuery } from '../../support/db.js';
import { loginAs } from '../../support/auth.js';

test.use({ storageState: { cookies: [], origins: [] } });

const attemptLogin = (page, email, password) =>
  page.request.post('/index.php/sessions/login', {
    form: { btn_login: '1', email, password },
    maxRedirects: 0,
  });

test.describe('Login security — failure responses', () => {
  test('it redirects after a login attempt with an unknown email', async ({ page }) => {
    /* Arrange + Act */
    const response = await attemptLogin(page, 'nobody@does-not-exist.example', 'irrelevant');

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
  });

  test('it redirects after a login attempt with a wrong password', async ({ page, browser }) => {
    /* Arrange */
    const { context: admin, page: adminPage } = await loginAs(browser, 'admin@test.local', 'password');
    const user = await createSecondaryUser(adminPage);

    /* Act */
    const response = await attemptLogin(page, user.email, 'wrong-password');

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    await admin.close();
  });

  test('it does not reveal whether an email exists in error responses', async ({ page }) => {
    /* Arrange */
    const unknown = await attemptLogin(page, 'ghost@no-account.example', 'password123');
    const wrongPassword = await attemptLogin(page, 'admin@test.local', 'definitely-wrong');

    /* Assert: identical status, so the two cases are indistinguishable */
    expect(unknown.status()).toBe(wrongPassword.status());
  });

  test('it does not expose dashboard content after a failed login', async ({ page }) => {
    /* Arrange */
    await attemptLogin(page, 'nobody@no-account.example', 'wrong');

    /* Act */
    await page.goto('/dashboard');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });
});

test.describe('Login security — account status', () => {
  test('it denies login for an inactive user even with the correct password', async ({ page, browser }) => {
    /* Arrange */
    const { context: admin, page: adminPage } = await loginAs(browser, 'admin@test.local', 'password');
    const user = await createSecondaryUser(adminPage);
    dbExec(`UPDATE ip_users SET user_active = 0 WHERE user_email = '${user.email}'`);

    /* Act */
    const response = await attemptLogin(page, user.email, user.password);

    /* Assert: redirected, and a failure row is logged (auth() returned false) */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT log_count FROM ip_login_log WHERE login_name = '${user.email}'`)).toEqual([{ log_count: 1 }]);
    await admin.close();
  });

  test('it allows login for an active user with the correct password', async ({ page, browser }) => {
    /* Arrange: user_type 1 (admin), matching LoginSecurityTest.php — this is
     * about the auth layer succeeding, not the guest-portal authorization
     * layer, so the created user must actually reach a working landing page
     * (a user_type 2 "guest" with no assigned client hits Guest_Controller's
     * own 403 guard instead, which would mask a real regression here). */
    const { context: admin, page: adminPage } = await loginAs(browser, 'admin@test.local', 'password');
    const user = await createSecondaryUser(adminPage, { user_type: '1' });

    /* Act */
    await page.goto('/sessions/login');
    await page.fill('input[name="email"]', user.email);
    await page.fill('input[name="password"]', user.password);
    await Promise.all([
      page.waitForURL((u) => !u.pathname.includes('/sessions/login')),
      page.click('form button[type="submit"]'),
    ]);

    /* Assert: reached the dashboard with no error, and no failure was logged */
    await expect(page).toHaveURL(/\/dashboard/);
    const body = await page.content();
    expect(body).not.toMatch(/Fatal error|Uncaught|A PHP Error was encountered|guest_account_denied/i);
    expect(dbQuery(`SELECT log_count FROM ip_login_log WHERE login_name = '${user.email}'`)).toEqual([]);
    await admin.close();
  });
});

/* IP rate limiting tests require shared state manipulation across serial run.
   Fully covered by tests/Feature/Core/LoginSecurityTest.php */
