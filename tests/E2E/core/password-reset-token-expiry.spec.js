/**
 * Browser coverage for password-reset token expiry enforcement in
 * application/modules/sessions/controllers/Sessions.php::passwordreset().
 * Mirrors tests/Feature/Core/PasswordResetTokenExpiryTest.php — an expired
 * token must not change the password on either the link (GET) or the
 * password-change (POST) flow.
 */

import { test, expect } from '../test.js';
import { uniq } from '../support/fixtures.js';
import { dbInsert, dbQuery } from '../support/db.js';

test.use({ storageState: { cookies: [], origins: [] } });

const TOKEN = 'ef260948cd51e1728a24ee672433e12757465c964269fd24d692b8980ecc2cf3';
const minutesFromNow = (m) => new Date(Date.now() + m * 60_000).toISOString().slice(0, 19).replace('T', ' ');

/** An active user holding TOKEN with the given expiry (null = legacy, no expiry). */
function seedUserWithResetToken(expiry) {
  const now = new Date().toISOString().slice(0, 19).replace('T', ' ');
  return dbInsert('ip_users', {
    user_name: uniq('resettarget'),
    user_email: `${uniq('reset').toLowerCase()}@example.com`,
    // bcrypt('OriginalPass123!'); any verifiable hash works — only the token path is under test
    user_password: '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFcqDx9DHqXQ2X4uJ1DkPZ2mEwqTzH2u',
    user_psalt: 'e2e',
    user_type: 1,
    user_active: 1,
    user_passwordreset_token: TOKEN,
    user_passwordreset_token_expiry: expiry,
    user_date_created: now,
    user_date_modified: now,
  });
}

const passwordOf = (id) => dbQuery(`SELECT user_password FROM ip_users WHERE user_id = ${id}`)[0].user_password;
const tokenOf = (id) => dbQuery(`SELECT user_passwordreset_token FROM ip_users WHERE user_id = ${id}`)[0].user_passwordreset_token;

function submitNewPassword(page, userId, newPassword) {
  return page.request.post('/index.php/sessions/passwordreset', {
    form: {
      btn_new_password: '1',
      user_id: String(userId),
      token: TOKEN,
      new_password: newPassword,
      new_passwordv: newPassword,
    },
    maxRedirects: 0,
  });
}

test.describe('Password reset token expiry', () => {
  test('it rejects a password change when the reset token has expired', async ({ page }) => {
    /* Arrange: a token whose 15-minute lifetime elapsed 5 minutes ago */
    const id = seedUserWithResetToken(minutesFromNow(-5));
    const before = passwordOf(id);

    /* Act */
    const response = await submitNewPassword(page, id, 'HackedPass123!');

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(passwordOf(id)).toBe(before);
  });

  test('it clears the expired token after a rejected password change', async ({ page }) => {
    /* Arrange */
    const id = seedUserWithResetToken(minutesFromNow(-5));

    /* Act */
    await submitNewPassword(page, id, 'HackedPass123!');

    /* Assert: the burnt token is cleared so it cannot be retried */
    expect(tokenOf(id) ?? '').toBe('');
  });

  test('it allows a password change with a valid unexpired token', async ({ page }) => {
    /* Arrange: valid for another 10 minutes */
    const id = seedUserWithResetToken(minutesFromNow(10));
    const before = passwordOf(id);

    /* Act */
    const response = await submitNewPassword(page, id, 'BrandNewPass123!');

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(passwordOf(id)).not.toBe(before);
  });

  test('it allows a password change when no expiry is stored', async ({ page }) => {
    /* Arrange: a legacy token issued before the expiry column existed */
    const id = seedUserWithResetToken(null);
    const before = passwordOf(id);

    /* Act */
    const response = await submitNewPassword(page, id, 'LegacyNewPass123!');

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(passwordOf(id)).not.toBe(before);
  });

  test('it rejects the reset link when the token has expired', async ({ page }) => {
    /* Arrange */
    seedUserWithResetToken(minutesFromNow(-5));

    /* Act */
    const response = await page.request.get(`/sessions/passwordreset/${TOKEN}`, { maxRedirects: 0 });

    /* Assert: bounced, never shown the new-password form */
    expect(response.status()).not.toBe(200);
  });
});
