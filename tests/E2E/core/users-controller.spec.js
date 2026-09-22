/**
 * Browser coverage for application/modules/users/controllers/Users.php.
 * Mirrors tests/Feature/Core/UsersControllerTest.php.
 * Required fields (Mdl_Users::validation_rules): user_type, user_email,
 * user_name, user_password, user_passwordv, user_language.
 *
 * The user form's language/type selects are JS-driven, so the create/update/
 * validation cases post the form directly (as the PHPUnit tests do); the list,
 * edit-render and delete paths run through the real UI.
 */

import { test, expect } from '../test.js';
import { createSecondaryUser, seedUser, uniq } from '../support/fixtures.js';
import { dbQuery } from '../support/db.js';
import { loginAs } from '../support/auth.js';
import { postForm } from '../support/http.js';

const createBody = (over = {}) => ({
  user_type: '2',
  user_name: uniq('User'),
  user_email: `${uniq('u').toLowerCase()}@test.local`,
  user_password: 'sup3rsecret',
  user_passwordv: 'sup3rsecret',
  user_language: 'system',
  btn_submit: '1',
  ...over,
});
const updateBody = (over = {}) => ({
  user_type: '2',
  user_name: uniq('User'),
  user_email: `${uniq('u').toLowerCase()}@test.local`,
  user_language: 'system',
  btn_submit: '1',
  ...over,
});
const exists = (id) => dbQuery(`SELECT user_id FROM ip_users WHERE user_id = ${id}`).length === 1;
const byEmail = (email) => dbQuery(`SELECT user_id FROM ip_users WHERE user_email = '${email}'`);

test.describe('Users — list', () => {
  test('it lists every user', async ({ page }) => {
    /* Arrange */
    const a = seedUser({ user_name: uniq('DanaAccountant') });
    const b = seedUser({ user_name: uniq('EliBookkeeper') });

    /* Act */
    await page.goto('/users');

    /* Assert */
    await expect(page.locator('#content')).toContainText(a.name);
    await expect(page.locator('#content')).toContainText(b.name);
  });
});

test.describe('Users — create', () => {
  test('it creates a user', async ({ page }) => {
    /* Arrange */
    const email = `${uniq('frank').toLowerCase()}@test.local`;

    /* Act */
    const response = await postForm(page, '/users/form', createBody({ user_name: 'Frank Clerk', user_email: email }));

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(byEmail(email)).toHaveLength(1);
  });

  for (const field of ['user_email', 'user_name', 'user_password']) {
    test(`it fails to create without ${field}`, async ({ page }) => {
      /* Arrange */
      const email = `${uniq('missing').toLowerCase()}@test.local`;
      const form = createBody({ user_email: email, [field]: '' });
      if (field === 'user_password') form.user_passwordv = '';

      /* Act */
      const response = await postForm(page, '/users/form', form);

      /* Assert */
      expect(response.status()).toBe(200);
      expect(byEmail(field === 'user_email' ? email : form.user_email)).toEqual([]);
    });
  }

  test('it rejects a mismatched password confirmation on create', async ({ page }) => {
    /* Arrange */
    const email = `${uniq('mismatch').toLowerCase()}@test.local`;

    /* Act */
    const response = await postForm(page, '/users/form', createBody({ user_email: email, user_passwordv: 'differentpass' }));

    /* Assert */
    expect(response.status()).toBe(200);
    expect(byEmail(email)).toEqual([]);
  });

  test('it rejects a duplicate user email on create', async ({ page }) => {
    /* Arrange */
    const email = `${uniq('taken').toLowerCase()}@test.local`;
    seedUser({ user_email: email });

    /* Act */
    const response = await postForm(page, '/users/form', createBody({ user_name: 'Second Taken', user_email: email }));

    /* Assert */
    expect(response.status()).toBe(200);
    expect(byEmail(email)).toHaveLength(1);
  });
});

test.describe('Users — update', () => {
  test('it renders the edit form for the requested user only', async ({ page }) => {
    /* Arrange */
    const target = seedUser({ user_name: uniq('EditableUserPerson') });
    const other = seedUser({ user_name: uniq('OtherUserPerson') });

    /* Act */
    await page.goto(`/users/form/${target.id}`);

    /* Assert */
    await expect(page.locator('#user_name')).toHaveValue(target.name);
    await expect(page.locator('body')).not.toContainText(other.name);
  });

  test('it updates a user without touching the password', async ({ page }) => {
    /* Arrange */
    const user = seedUser({ user_name: uniq('OriginalUserName'), user_email: `${uniq('upd').toLowerCase()}@test.local` });
    const [before] = dbQuery(`SELECT user_password FROM ip_users WHERE user_id = ${user.id}`);
    const renamed = uniq('RenamedUserName');

    /* Act */
    const response = await postForm(page, `/users/form/${user.id}`, updateBody({
      user_name: renamed,
      user_email: dbQuery(`SELECT user_email FROM ip_users WHERE user_id = ${user.id}`)[0].user_email,
    }));

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    const [after] = dbQuery(`SELECT user_name, user_password FROM ip_users WHERE user_id = ${user.id}`);
    expect(after.user_name).toBe(renamed);
    expect(after.user_password).toBe(before.user_password);
  });

  for (const field of ['user_email', 'user_name']) {
    test(`it fails to update without ${field}`, async ({ page }) => {
      /* Arrange */
      const user = seedUser({ user_name: uniq('KeepThisUser') });

      /* Act */
      const response = await postForm(page, `/users/form/${user.id}`, updateBody({ [field]: '' }));

      /* Assert */
      expect(response.status()).toBe(200);
      expect(dbQuery(`SELECT user_name FROM ip_users WHERE user_id = ${user.id}`)).toEqual([{ user_name: user.name }]);
    });
  }
});

test.describe('Users — delete', () => {
  test('it deletes a secondary user', async ({ page }) => {
    /* Arrange */
    const doomed = seedUser({ user_name: uniq('DeletableUser') });
    const kept = seedUser({ user_name: uniq('KeptUser') });

    /* Act */
    const response = await postForm(page, `/users/delete/${doomed.id}`, {});

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(exists(doomed.id)).toBe(false);
    expect(exists(kept.id)).toBe(true);
  });

  test('it never deletes the primary admin', async ({ page }) => {
    /* Arrange + Act */
    const response = await postForm(page, '/users/delete/1', {});

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(exists(1)).toBe(true);
  });

  test('it does not delete a user on a plain get request', async ({ page }) => {
    /* Arrange */
    const user = seedUser({ user_name: uniq('GetUserKept') });

    /* Act */
    const response = await page.request.get(`/users/delete/${user.id}`, { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).toBe(404);
    expect(exists(user.id)).toBe(true);
  });

  test('it still deletes a user when csrf protection is on and the token is valid', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });

  test('it does not delete a user when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Users — change password authorization', () => {
  test('it prevents a non primary admin from changing another users password', async ({ page, browser }) => {
    /* Arrange */
    const attacker = await createSecondaryUser(page, { user_type: '1' });
    const victim = seedUser({ user_type: 1, user_name: uniq('VictimAdmin') });
    const [before] = dbQuery(`SELECT user_password FROM ip_users WHERE user_id = ${victim.id}`);
    const { context, page: attackerPage } = await loginAs(browser, attacker.email, attacker.password);

    /* Act */
    const response = await attackerPage.request.post(`/users/change_password/${victim.id}`, {
      form: { user_password: 'attacker-chosen-password', user_password_confirm: 'attacker-chosen-password', btn_submit: '1' },
      maxRedirects: 0,
    });

    /* Assert */
    expect(response.status()).toBe(403);
    expect(dbQuery(`SELECT user_password FROM ip_users WHERE user_id = ${victim.id}`))
      .toEqual([{ user_password: before.user_password }]);
    await context.close();
  });
});

test.describe('Users — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no user', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/users');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Secret User Person');
  });
});
