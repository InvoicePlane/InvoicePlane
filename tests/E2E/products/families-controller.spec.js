/**
 * Browser coverage for application/modules/families/controllers/Families.php.
 * Mirrors tests/Feature/Products/FamiliesControllerTest.php.
 * Required field (Mdl_Families::validation_rules): family_name.
 */

import { test, expect } from '../test.js';
import { createFamily, uniq } from '../support/fixtures.js';
import { expectBlockedByRequired, expectErrorFlash } from '../support/forms.js';

test.describe('Families — list', () => {
  test('it lists every family', async ({ page }) => {
    /* Arrange */
    const a = await createFamily(page, uniq('Beverages'));
    const b = await createFamily(page, uniq('Hardware'));

    /* Act */
    await page.goto('/families');

    /* Assert */
    await expect(page.getByRole('link', { name: a.name })).toBeVisible();
    await expect(page.getByRole('link', { name: b.name })).toBeVisible();
  });
});

test.describe('Families — create', () => {
  test('it creates a family', async ({ page }) => {
    /* Arrange */
    const name = uniq('Stationery');

    /* Act */
    await page.goto('/families/form');
    await page.fill('#family_name', name);
    await Promise.all([page.waitForURL(/\/families(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.getByRole('link', { name })).toBeVisible();
  });

  test('it fails to create without family_name', async ({ page }) => {
    /* Arrange */
    await page.goto('/families/form');

    /* Act + Assert */
    await expectBlockedByRequired(page, '#family_name');
  });

  test('it rejects a duplicate family name on create', async ({ page }) => {
    /* Arrange */
    const name = uniq('DuplicateFamily');
    await createFamily(page, name);

    /* Act */
    await page.goto('/families/form');
    await page.fill('#family_name', name);
    await Promise.all([page.waitForLoadState('load'), page.click('#btn-submit')]);

    /* Assert */
    await expect(page).toHaveURL(/\/families\/form$/);
    await expectErrorFlash(page);
  });
});

test.describe('Families — update', () => {
  test('it renders the edit form for the requested family only', async ({ page }) => {
    /* Arrange */
    const target = await createFamily(page, uniq('Editable'));
    const other = await createFamily(page, uniq('Other'));

    /* Act */
    await page.goto(`/families/form/${target.id}`);

    /* Assert */
    await expect(page.locator('#family_name')).toHaveValue(target.name);
    await expect(page.locator('body')).not.toContainText(other.name);
  });

  test('it updates a family', async ({ page }) => {
    /* Arrange */
    const family = await createFamily(page, uniq('Original'));
    const renamed = uniq('Renamed');

    /* Act */
    await page.goto(`/families/form/${family.id}`);
    await page.fill('#family_name', renamed);
    await Promise.all([page.waitForURL(/\/families(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.getByRole('link', { name: renamed })).toBeVisible();
    await expect(page.getByRole('link', { name: family.name, exact: true })).toHaveCount(0);
  });

  test('it fails to update without family_name', async ({ page }) => {
    /* Arrange */
    const family = await createFamily(page, uniq('KeepThis'));

    /* Act + Assert */
    await page.goto(`/families/form/${family.id}`);
    await page.fill('#family_name', '');
    await expectBlockedByRequired(page, '#family_name');

    /* Assert: the stored value is untouched */
    await page.goto(`/families/form/${family.id}`);
    await expect(page.locator('#family_name')).toHaveValue(family.name);
  });
});

test.describe('Families — delete', () => {
  test('it deletes a family', async ({ page }) => {
    /* Arrange */
    const doomed = await createFamily(page, uniq('Deletable'));
    const kept = await createFamily(page, uniq('Kept'));

    /* Act */
    await page.goto('/families');
    const row = page.locator('tr', { has: page.getByRole('link', { name: doomed.name }) });
    await row.locator('.dropdown-toggle').click();
    page.once('dialog', (dialog) => dialog.accept());
    await Promise.all([page.waitForLoadState('load'), row.locator('button.dropdown-button').click()]);

    /* Assert */
    await page.goto('/families');
    await expect(page.getByRole('link', { name: doomed.name })).toHaveCount(0);
    await expect(page.getByRole('link', { name: kept.name })).toBeVisible();
  });

  test('it still deletes a family when csrf protection is on and the token is valid', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });

  test('it does not delete a family when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Families — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no family', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/families');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Secret Family');
  });
});
