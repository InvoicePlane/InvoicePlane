/**
 * Browser coverage for application/modules/tax_rates/controllers/Tax_rates.php.
 * Mirrors tests/Feature/Core/TaxRatesControllerTest.php.
 * Required fields (Mdl_Tax_rates::validation_rules): tax_rate_name,
 * tax_rate_percent.
 */

import { test, expect } from '../test.js';
import { createTaxRate, uniq } from '../support/fixtures.js';
import { dbQuery } from '../support/db.js';
import { expectBlockedByRequired } from '../support/forms.js';

test.describe('Tax rates — list', () => {
  test('it lists every tax rate', async ({ page }) => {
    /* Arrange */
    const a = await createTaxRate(page, { tax_rate_name: uniq('ReducedVAT') });
    const b = await createTaxRate(page, { tax_rate_name: uniq('StandardVAT') });

    /* Act */
    await page.goto('/tax_rates');

    /* Assert */
    await expect(page.locator('#content')).toContainText(a.name);
    await expect(page.locator('#content')).toContainText(b.name);
  });
});

test.describe('Tax rates — create', () => {
  test('it creates a tax rate', async ({ page }) => {
    /* Arrange */
    const name = uniq('StandardVAT');

    /* Act */
    await page.goto('/tax_rates/form');
    await page.fill('#tax_rate_name', name);
    await page.fill('#tax_rate_percent', '21.00');
    await Promise.all([page.waitForURL(/\/tax_rates(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.locator('#content')).toContainText(name);
  });

  test('it fails to create without tax_rate_name', async ({ page }) => {
    /* Arrange */
    await page.goto('/tax_rates/form');
    await page.fill('#tax_rate_percent', '42.42');

    /* Act + Assert */
    await expectBlockedByRequired(page, '#tax_rate_name');
  });

  test('it fails to create without tax_rate_percent', async ({ page }) => {
    /* Arrange */
    await page.goto('/tax_rates/form');
    await page.fill('#tax_rate_name', 'Incomplete VAT');

    /* Act + Assert */
    await expectBlockedByRequired(page, '#tax_rate_percent');
  });
});

test.describe('Tax rates — update', () => {
  test('it renders the edit form for the requested tax rate only', async ({ page }) => {
    /* Arrange */
    const target = await createTaxRate(page, { tax_rate_name: uniq('EditableVAT') });
    const other = await createTaxRate(page, { tax_rate_name: uniq('OtherVAT') });

    /* Act */
    await page.goto(`/tax_rates/form/${target.id}`);

    /* Assert */
    await expect(page.locator('#tax_rate_name')).toHaveValue(target.name);
    await expect(page.locator('body')).not.toContainText(other.name);
  });

  test('it updates a tax rate', async ({ page }) => {
    /* Arrange */
    const rate = await createTaxRate(page, { tax_rate_name: uniq('OriginalVAT') });
    const renamed = uniq('RenamedVAT');

    /* Act */
    await page.goto(`/tax_rates/form/${rate.id}`);
    await page.fill('#tax_rate_name', renamed);
    await page.fill('#tax_rate_percent', '15.00');
    await Promise.all([page.waitForURL(/\/tax_rates(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.locator('#content')).toContainText(renamed);
    expect(dbQuery(`SELECT tax_rate_percent FROM ip_tax_rates WHERE tax_rate_id = ${rate.id}`))
      .toEqual([{ tax_rate_percent: '15.00' }]);
  });

  test('it fails to update without tax_rate_name', async ({ page }) => {
    /* Arrange */
    const rate = await createTaxRate(page, { tax_rate_name: uniq('WillNotChange') });

    /* Act + Assert */
    await page.goto(`/tax_rates/form/${rate.id}`);
    await page.fill('#tax_rate_name', '');
    await expectBlockedByRequired(page, '#tax_rate_name');
  });

  test('it fails to update without tax_rate_percent', async ({ page }) => {
    /* Arrange */
    const rate = await createTaxRate(page, { tax_rate_name: uniq('PercentKept') });

    /* Act + Assert */
    await page.goto(`/tax_rates/form/${rate.id}`);
    await page.fill('#tax_rate_percent', '');
    await expectBlockedByRequired(page, '#tax_rate_percent');
  });
});

test.describe('Tax rates — delete', () => {
  test('it deletes a tax rate', async ({ page }) => {
    /* Arrange */
    const doomed = await createTaxRate(page, { tax_rate_name: uniq('DeletableVAT') });
    const kept = await createTaxRate(page, { tax_rate_name: uniq('KeptVAT') });

    /* Act */
    await page.goto('/tax_rates');
    const row = page.locator('tr', { hasText: doomed.name });
    await row.locator('.dropdown-toggle').click();
    page.once('dialog', (dialog) => dialog.accept());
    await Promise.all([page.waitForLoadState('load'), row.locator('button.dropdown-button').click()]);

    /* Assert */
    expect(dbQuery(`SELECT tax_rate_id FROM ip_tax_rates WHERE tax_rate_id = ${doomed.id}`)).toEqual([]);
    expect(dbQuery(`SELECT tax_rate_id FROM ip_tax_rates WHERE tax_rate_id = ${kept.id}`)).toHaveLength(1);
  });

  test('it still deletes a tax rate when csrf protection is on and the token is valid', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });

  test('it does not delete a tax rate when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Tax rates — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no tax rate', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/tax_rates');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Secret VAT');
  });
});
