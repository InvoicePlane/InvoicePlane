/**
 * Browser coverage for application/modules/user_clients/controllers/User_clients.php.
 *
 * Mirrors tests/Feature/Clients/UserClientsControllerTest.php. A user-client row
 * assigns a client to a non-admin user; it is created or deleted, never edited.
 * Required fields (Mdl_User_Clients::validation_rules): user_id, client_id.
 */

import { test, expect } from '../test.js';
import { createClient, createSecondaryUser, uniq } from '../support/fixtures.js';
import { postForm } from '../support/http.js';
import { E2E_BASE_URL, LOGIN_PATH } from '../config.js';

/** Assign `clientId` to `userId` through the real assign-client form. */
async function assignViaForm(page, userId, clientId) {
  await page.goto(`/user_clients/create/${userId}`);
  await page.selectOption('#client_id', String(clientId));
  await Promise.all([
    page.waitForURL(new RegExp(`/user_clients/user/${userId}`)),
    page.click('#btn-submit'),
  ]);
}

test.describe('User clients — list', () => {
  test('it lists every client assigned to a user', async ({ page }) => {
    /* Arrange */
    const user = await createSecondaryUser(page);
    const a = await createClient(page, { client_name: uniq('AssignedOne') });
    const b = await createClient(page, { client_name: uniq('AssignedTwo') });
    await assignViaForm(page, user.id, a.id);
    await assignViaForm(page, user.id, b.id);

    /* Act */
    await page.goto(`/user_clients/user/${user.id}`);

    /* Assert */
    await expect(page.getByRole('link', { name: a.name })).toBeVisible();
    await expect(page.getByRole('link', { name: b.name })).toBeVisible();
  });
});

test.describe('User clients — assign', () => {
  test('it assigns a client to a user', async ({ page }) => {
    /* Arrange */
    const user = await createSecondaryUser(page);
    const client = await createClient(page, { client_name: uniq('FreshlyAssigned') });

    /* Act */
    await assignViaForm(page, user.id, client.id);

    /* Assert */
    await expect(page.getByRole('link', { name: client.name })).toBeVisible();
  });

  test('it fails to assign without client_id', async ({ page }) => {
    /* Arrange */
    const user = await createSecondaryUser(page);

    /* Act */
    const response = await postForm(page, `/user_clients/create/${user.id}`, {
      user_id: String(user.id),
      client_id: '',
      btn_submit: '1',
    });

    /* Assert */
    expect(response.status(), 'invalid assignment re-renders, does not redirect').toBe(200);
    await page.goto(`/user_clients/user/${user.id}`);
    await expect(page.locator('tbody tr')).toHaveCount(0);
  });

  test('it fails to assign without user_id', async ({ page }) => {
    /* Arrange */
    const user = await createSecondaryUser(page);
    const client = await createClient(page, { client_name: uniq('OrphanAssignment') });

    /* Act */
    const response = await postForm(page, `/user_clients/create/${user.id}`, {
      user_id: '',
      client_id: String(client.id),
      btn_submit: '1',
    });

    /* Assert */
    expect(response.status()).toBe(200);
    await page.goto(`/user_clients/user/${user.id}`);
    await expect(page.getByRole('link', { name: client.name })).toHaveCount(0);
  });
});

test.describe('User clients — unassign', () => {
  test('it unassigns a client from a user', async ({ page }) => {
    /* Arrange */
    const user = await createSecondaryUser(page);
    const doomed = await createClient(page, { client_name: uniq('Unassign') });
    const kept = await createClient(page, { client_name: uniq('KeepAssigned') });
    await assignViaForm(page, user.id, doomed.id);
    await assignViaForm(page, user.id, kept.id);

    /* Act */
    await page.goto(`/user_clients/user/${user.id}`);
    const row = page.locator('tr', { has: page.getByRole('link', { name: doomed.name }) });
    page.once('dialog', (dialog) => dialog.accept());
    await Promise.all([
      page.waitForURL(new RegExp(`/user_clients/user/${user.id}`)),
      row.locator('button[type="submit"]').click(),
    ]);

    /* Assert */
    await expect(page.getByRole('link', { name: doomed.name })).toHaveCount(0);
    await expect(page.getByRole('link', { name: kept.name })).toBeVisible();
  });

  // CSRF-regression pair (#1694): needs a CSRF_PROTECTION=true server. Covered by
  // tests/Feature/Clients/UserClientsControllerTest.php; see tests/E2E/README.md.
  test('it still unassigns when csrf protection is on and the token is valid', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });

  test('it does not unassign when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('User clients — access control', () => {
  test('it blocks a non-admin from unassigning a client', async ({ page, browser }) => {
    /* Arrange */
    const user = await createSecondaryUser(page);
    const client = await createClient(page);
    await assignViaForm(page, user.id, client.id);
    const ucid = (await (await page.request.get(`/user_clients/user/${user.id}`)).text())
      .match(/user_clients\/delete\/(\d+)/)?.[1];
    expect(ucid).toBeTruthy();

    const nonAdmin = await browser.newContext({ baseURL: E2E_BASE_URL });
    const naPage = await nonAdmin.newPage();
    await naPage.goto(LOGIN_PATH);
    await naPage.fill('input[name="email"]', user.email);
    await naPage.fill('input[name="password"]', user.password);
    await Promise.all([
      naPage.waitForURL((u) => !u.pathname.includes(LOGIN_PATH)),
      naPage.click('form button[type="submit"]'),
    ]);

    /* Act */
    const response = await naPage.request.post(`/user_clients/delete/${ucid}`, {
      form: {},
      maxRedirects: 0,
    });

    /* Assert: User_Controller's role guard bounces the non-admin to login — the
     * delete action never runs. */
    expect([301, 302, 303]).toContain(response.status());
    expect(response.headers().location ?? '').toContain('sessions/login');
    await nonAdmin.close();

    // The assignment is still there (checked through a fresh admin session; the
    // guard's raw session_destroy() also tears down the storageState session
    // under the single-process dev server).
    const admin2 = await browser.newContext({ baseURL: E2E_BASE_URL });
    const ap2 = await admin2.newPage();
    await ap2.goto(LOGIN_PATH);
    await ap2.fill('input[name="email"]', 'admin@test.local');
    await ap2.fill('input[name="password"]', 'password');
    await Promise.all([
      ap2.waitForURL((u) => !u.pathname.includes(LOGIN_PATH)),
      ap2.click('form button[type="submit"]'),
    ]);
    const stillThere = await ap2.request.get(`/user_clients/user/${user.id}`);
    expect(await stillThere.text()).toContain(`user_clients/delete/${ucid}`);
    await admin2.close();
  });
});

test.describe('User clients — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no assignment', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/user_clients/user/1');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Secret Assigned Client');
  });
});
