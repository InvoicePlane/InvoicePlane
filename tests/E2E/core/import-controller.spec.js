/**
 * Browser coverage for application/modules/import/controllers/Import.php.
 * Mirrors tests/Feature/Core/ImportControllerTest.php — the import screen
 * renders, is closed to guests, and only offers the exact allow-listed
 * filenames (clients.csv, invoices.csv, …), never an arbitrary upload.
 */

import fs from 'node:fs';
import path from 'node:path';
import { test, expect } from '../test.js';

// directory_map('./uploads/import') resolves against the php -S router's cwd
// (public/), so the app reads public/uploads/import.
const IMPORT_DIR = path.resolve('public/uploads/import');
const FIXTURES = ['clients.csv', 'evil.php'];
const rmFixtures = () => FIXTURES.forEach((f) => fs.rmSync(path.join(IMPORT_DIR, f), { force: true }));

test.beforeAll(() => fs.mkdirSync(IMPORT_DIR, { recursive: true }));
test.afterEach(rmFixtures);

test.describe('Import — screen', () => {
  test('it returns a successful response or redirect', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/import');

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).toContain('<html');
  });

  test('it lists only allowed import files', async ({ page }) => {
    /* Arrange: one allow-listed name and one that is not */
    fs.writeFileSync(path.join(IMPORT_DIR, 'clients.csv'), 'client_name\nAllowed Client\n');
    fs.writeFileSync(path.join(IMPORT_DIR, 'evil.php'), '<?php echo "not allowed";');

    /* Act */
    const body = await (await page.goto('/import/form')).text();

    /* Assert */
    expect(body).toContain('clients.csv');
    expect(body).not.toContain('evil.php');
  });

  test('it ignores unapproved import filenames on submit', async ({ page }) => {
    /* Arrange */
    fs.writeFileSync(path.join(IMPORT_DIR, 'evil.php'), '<?php echo "not allowed";');

    /* Act */
    const response = await page.request.post('/import/form', {
      form: { file_name: 'evil.php', btn_submit: '1' },
      maxRedirects: 0,
    });

    /* Assert: not accepted, and the file is untouched on disk */
    expect(response.status()).not.toBe(200);
    expect(fs.existsSync(path.join(IMPORT_DIR, 'evil.php'))).toBe(true);
  });
});

test.describe('Import — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login', async ({ page }) => {
    /* Arrange + Act */
    await page.goto('/import');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
  });
});
