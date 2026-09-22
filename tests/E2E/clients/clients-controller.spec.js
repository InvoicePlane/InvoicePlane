/**
 * Browser coverage for application/modules/clients/controllers/Clients.php.
 *
 * Mirrors tests/Feature/Clients/ClientsControllerTest.php test-for-test: every
 * PHPUnit method that proves a rule server-side has a Playwright test here that
 * proves a real signed-in admin reaches the same outcome through the rendered
 * UI. Required field (Mdl_Clients::validation_rules): client_name.
 */

import { test, expect } from '../test.js';
import { createClient, uniq } from '../support/fixtures.js';
import { expectBlockedByRequired, expectErrorFlash } from '../support/forms.js';
import { postForm, readCsrfToken } from '../support/http.js';

test.describe('Clients — list', () => {
  test('it lists every active client', async ({ page }) => {
    /* Arrange */
    const a = await createClient(page, { client_name: uniq('Northwind') });
    const b = await createClient(page, { client_name: uniq('Acme') });

    /* Act */
    await page.goto('/clients/status/active');

    /* Assert */
    await expect(page.getByRole('link', { name: a.name })).toBeVisible();
    await expect(page.getByRole('link', { name: b.name })).toBeVisible();
  });
});

test.describe('Clients — create', () => {
  test('it creates a client', async ({ page }) => {
    /* Arrange */
    const name = uniq('Globex');

    /* Act */
    await page.goto('/clients/form');
    await page.fill('#client_name', name);
    await Promise.all([page.waitForURL(/\/clients\/view\/\d+/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.locator('#content')).toContainText(name);
  });

  test('it fails to create without client_name', async ({ page }) => {
    /* Arrange */
    await page.goto('/clients/form');
    await page.fill('#client_surname', 'Nameless');

    /* Act + Assert: the browser blocks the submit on the empty required field */
    await expectBlockedByRequired(page, '#client_name');

    /* Assert: nothing was persisted */
    await page.goto('/clients/status/all');
    await expect(page.getByRole('link', { name: 'Nameless' })).toHaveCount(0);
  });

  test('it rejects a duplicate client name and surname on create', async ({ page }) => {
    /* Arrange */
    const name = uniq('Duplicate');
    await createClient(page, { client_name: name, client_surname: 'Dup' });

    /* Act */
    await page.goto('/clients/form');
    await page.fill('#client_name', name);
    await page.fill('#client_surname', 'Dup');
    await Promise.all([page.waitForLoadState('load'), page.click('#btn-submit')]);

    /* Assert */
    await expect(page).toHaveURL(/\/clients\/form$/);
    await expectErrorFlash(page);
  });
});

test.describe('Clients — update', () => {
  test('it renders the edit form for the requested client only', async ({ page }) => {
    /* Arrange */
    const target = await createClient(page, { client_name: uniq('Editable') });
    const other = await createClient(page, { client_name: uniq('Other') });

    /* Act */
    await page.goto(`/clients/form/${target.id}`);

    /* Assert */
    await expect(page.locator('#client_name')).toHaveValue(target.name);
    await expect(page.locator('body')).not.toContainText(other.name);
  });

  test('it updates a client', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page, { client_name: uniq('Original') });
    const renamed = uniq('Renamed');

    /* Act */
    await page.goto(`/clients/form/${client.id}`);
    await page.fill('#client_name', renamed);
    await Promise.all([page.waitForURL(/\/clients\/view\/\d+/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.locator('#content')).toContainText(renamed);
    await page.goto('/clients/status/all');
    await expect(page.getByRole('link', { name: client.name, exact: true })).toHaveCount(0);
  });

  test('it fails to update without client_name', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page, { client_name: uniq('KeepThis') });

    /* Act + Assert */
    await page.goto(`/clients/form/${client.id}`);
    await page.fill('#client_name', '');
    await expectBlockedByRequired(page, '#client_name');

    /* Assert: the stored value is untouched */
    await page.goto(`/clients/form/${client.id}`);
    await expect(page.locator('#client_name')).toHaveValue(client.name);
  });
});

test.describe('Clients — delete', () => {
  test('it deletes a client', async ({ page }) => {
    /* Arrange */
    const doomed = await createClient(page, { client_name: uniq('Deletable') });
    const kept = await createClient(page, { client_name: uniq('Kept') });

    /* Act */
    await page.goto('/clients/status/all');
    const row = page.locator('tr', { has: page.getByRole('link', { name: doomed.name }) });
    await row.locator('.dropdown-toggle').click();
    page.once('dialog', (dialog) => dialog.accept());
    await Promise.all([
      page.waitForURL(/\/clients(\/status\/\w+)?$/),
      row.locator('button.dropdown-button').click(),
    ]);

    /* Assert */
    await page.goto('/clients/status/all');
    await expect(page.getByRole('link', { name: doomed.name })).toHaveCount(0);
    await expect(page.getByRole('link', { name: kept.name })).toBeVisible();
  });

  // The two CSRF-regression cases (issue #1694) can only be exercised against a
  // server booted with CSRF_PROTECTION=true. This E2E server runs with it off
  // (ipconfig.php), so they stay skipped here and remain covered by
  // tests/Feature/Clients/ClientsControllerTest.php.
  test('it still deletes a client when csrf protection is on and the token is valid', async ({ page }) => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
    /* Arrange */
    const doomed = await createClient(page, { client_name: uniq('CsrfClient') });
    const token = await readCsrfToken(page, '/clients/status/all');

    /* Act */
    const response = await postForm(page, `/clients/delete/${doomed.id}`, { _ip_csrf: token });

    /* Assert */
    expect(response.status()).toBe(303);
  });

  test('it does not delete a client when the csrf token is missing', async ({ page }) => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
    /* Arrange */
    const kept = await createClient(page, { client_name: uniq('CsrfKept') });

    /* Act */
    const response = await postForm(page, `/clients/delete/${kept.id}`, {});

    /* Assert */
    expect(response.status()).not.toBe(303);
  });
});

test.describe('Clients — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no client', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/clients/status/active');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Secret Client');
  });
});
