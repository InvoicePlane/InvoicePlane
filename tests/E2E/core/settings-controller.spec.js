/**
 * Browser coverage for application/modules/settings/controllers/Settings.php.
 * Mirrors tests/Feature/Core/SettingsControllerTest.php.
 *
 * The `withEnvironment()` cases (DISABLE_SETUP / CUSTOM_INVOICE_TEMPLATES_PDF
 * warnings) are config-dependent and stay skipped here, covered by the PHPUnit
 * Feature suite.
 */

import { test, expect } from '../test.js';
import { uniq } from '../support/fixtures.js';
import { dbExec, dbQuery } from '../support/db.js';
import { postForm } from '../support/http.js';

const setSetting = (key, value) =>
  dbExec(
    `INSERT INTO ip_settings (setting_key, setting_value) VALUES ('${key}', '${value}')`
    + ' ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
  );
const getSetting = (key) => dbQuery(`SELECT setting_value FROM ip_settings WHERE setting_key = '${key}'`)[0]?.setting_value;

test.describe('Settings — render', () => {
  test('it renders the settings page with a stored value', async ({ page }) => {
    /* Arrange */
    const cronKey = uniq('visible-cron-key');
    setSetting('cron_key', cronKey);

    /* Act */
    const response = await page.goto('/settings');
    const body = await response.text();

    /* Assert */
    expect(body).toContain(cronKey);
    expect(body).not.toContain('A PHP Error was encountered');
  });
});

test.describe('Settings — persist', () => {
  test('it persists a changed setting', async ({ page }) => {
    /* Arrange */
    const value = uniq('abc123');

    /* Act */
    const response = await postForm(page, '/settings', { 'settings[cron_key]': value, btn_submit: '1' });

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(getSetting('cron_key')).toBe(value);
  });
});

test.describe('Settings — remove logo', () => {
  test('it removes the invoice logo', async ({ page }) => {
    /* Arrange */
    setSetting('invoice_logo', 'remove-me.png');

    /* Act */
    const response = await postForm(page, '/settings/remove_logo/invoice', {});

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(getSetting('invoice_logo')).toBe('');
  });

  test('it removes the login logo', async ({ page }) => {
    /* Arrange */
    setSetting('login_logo', 'remove-me.png');

    /* Act */
    const response = await postForm(page, '/settings/remove_logo/login', {});

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(getSetting('login_logo')).toBe('');
  });

  test('it ignores an unknown logo type', async ({ page }) => {
    /* Arrange */
    setSetting('invoice_logo', 'keep-me.png');

    /* Act */
    await postForm(page, '/settings/remove_logo/not_a_real_type', {});

    /* Assert */
    expect(getSetting('invoice_logo')).toBe('keep-me.png');
  });

  test('it does not remove a logo when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Settings — setup-security warnings (config dependent)', () => {
  test('it warns admins when setup security flags are not enabled', async () => {
    test.fixme(true, 'needs DISABLE_SETUP=false in ipconfig — covered by SettingsControllerTest');
  });

  test('it warns when a saved custom invoice template is missing from ipconfig', async () => {
    test.fixme(true, 'needs a CUSTOM_INVOICE_TEMPLATES_PDF env change — covered by SettingsControllerTest');
  });

  test('it does not warn when a saved custom invoice template is allowlisted in ipconfig', async () => {
    test.fixme(true, 'needs a CUSTOM_INVOICE_TEMPLATES_PDF env change — covered by SettingsControllerTest');
  });
});

test.describe('Settings — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/settings');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });
});
