/**
 * Browser coverage mirroring tests/Feature/Core/SettingsAjaxAndVersionsTest.php
 * — the cron-key generator AJAX endpoint and the applied-versions page.
 */

import { test, expect } from '../test.js';

const XHR = { 'X-Requested-With': 'XMLHttpRequest' };

test.describe('Settings AJAX — cron key', () => {
  test('it generates a 16 character hex cron key', async ({ page }) => {
    /* Arrange + Act */
    const key = (await (await page.request.get('/settings/ajax/get_cron_key', { headers: XHR })).text()).trim();

    /* Assert */
    expect(key).toMatch(/^[0-9a-f]{16}$/);
  });

  test('it generates a different cron key on each call', async ({ page }) => {
    /* Arrange + Act */
    const first = (await (await page.request.get('/settings/ajax/get_cron_key', { headers: XHR })).text()).trim();
    const second = (await (await page.request.get('/settings/ajax/get_cron_key', { headers: XHR })).text()).trim();

    /* Assert */
    expect(first).not.toBe(second);
  });

  test('it requires an ajax request for get cron key', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get('/settings/ajax/get_cron_key');

    /* Assert */
    expect((await response.text()).trim()).toBe('');
  });
});

test.describe('Settings — applied versions', () => {
  test('it lists applied versions', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/settings/versions');

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });
});

test.describe('Settings — applied versions (guest)', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it denies versions access to a guest', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/settings/versions');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });
});
