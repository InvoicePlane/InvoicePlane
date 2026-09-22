/**
 * Browser coverage for application/modules/invoice_groups/controllers/Invoice_groups.php.
 * Mirrors tests/Feature/Invoices/InvoiceGroupsControllerTest.php.
 * Required fields: invoice_group_name, invoice_group_identifier_format,
 * invoice_group_next_id, invoice_group_left_pad.
 */

import { test, expect } from '../test.js';
import { createInvoiceGroup, uniq } from '../support/fixtures.js';
import { dbQuery } from '../support/db.js';
import { expectBlockedByRequired } from '../support/forms.js';

const REQUIRED = {
  '#invoice_group_name': () => uniq('Grp'),
  '#invoice_group_identifier_format': () => '{{{id}}}',
  '#invoice_group_next_id': () => '1',
  '#invoice_group_left_pad': () => '0',
};

async function fillGroupForm(page, { skip } = {}) {
  for (const [selector, value] of Object.entries(REQUIRED)) {
    await page.fill(selector, selector === skip ? '' : value());
  }
}

test.describe('Invoice groups — list', () => {
  test('it lists every invoice group', async ({ page }) => {
    /* Arrange */
    const a = await createInvoiceGroup(page, { invoice_group_name: uniq('GroupOne') });
    const b = await createInvoiceGroup(page, { invoice_group_name: uniq('GroupTwo') });

    /* Act */
    await page.goto('/invoice_groups');

    /* Assert */
    await expect(page.locator('#content')).toContainText(a.name);
    await expect(page.locator('#content')).toContainText(b.name);
  });
});

test.describe('Invoice groups — create', () => {
  test('it creates an invoice group', async ({ page }) => {
    /* Arrange */
    const name = uniq('NewGroup');

    /* Act */
    await page.goto('/invoice_groups/form');
    await fillGroupForm(page);
    await page.fill('#invoice_group_name', name);
    await Promise.all([page.waitForURL(/\/invoice_groups(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.locator('#content')).toContainText(name);
  });

  for (const field of Object.keys(REQUIRED)) {
    test(`it fails to create without ${field.replace('#', '')}`, async ({ page }) => {
      /* Arrange */
      await page.goto('/invoice_groups/form');
      await fillGroupForm(page, { skip: field });

      /* Act + Assert */
      await expectBlockedByRequired(page, field);
    });
  }
});

test.describe('Invoice groups — update', () => {
  test('it renders the edit form for the requested invoice group only', async ({ page }) => {
    /* Arrange */
    const target = await createInvoiceGroup(page, { invoice_group_name: uniq('EditableGroup') });
    const other = await createInvoiceGroup(page, { invoice_group_name: uniq('OtherGroup') });

    /* Act */
    await page.goto(`/invoice_groups/form/${target.id}`);

    /* Assert */
    await expect(page.locator('#invoice_group_name')).toHaveValue(target.name);
    await expect(page.locator('body')).not.toContainText(other.name);
  });

  test('it updates an invoice group', async ({ page }) => {
    /* Arrange */
    const group = await createInvoiceGroup(page, { invoice_group_name: uniq('OriginalGroup') });
    const renamed = uniq('RenamedGroup');

    /* Act */
    await page.goto(`/invoice_groups/form/${group.id}`);
    await page.fill('#invoice_group_name', renamed);
    await Promise.all([page.waitForURL(/\/invoice_groups(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.locator('#content')).toContainText(renamed);
  });

  for (const field of Object.keys(REQUIRED)) {
    test(`it fails to update without ${field.replace('#', '')}`, async ({ page }) => {
      /* Arrange */
      const group = await createInvoiceGroup(page);

      /* Act + Assert */
      await page.goto(`/invoice_groups/form/${group.id}`);
      await page.fill(field, '');
      await expectBlockedByRequired(page, field);
    });
  }
});

test.describe('Invoice groups — delete', () => {
  test('it deletes an invoice group', async ({ page }) => {
    /* Arrange */
    const doomed = await createInvoiceGroup(page, { invoice_group_name: uniq('DeletableGroup') });
    const kept = await createInvoiceGroup(page, { invoice_group_name: uniq('KeptGroup') });

    /* Act */
    await page.goto('/invoice_groups');
    const row = page.locator('tr', { hasText: doomed.name });
    await row.locator('.dropdown-toggle').click();
    page.once('dialog', (dialog) => dialog.accept());
    await Promise.all([page.waitForLoadState('load'), row.locator('button.dropdown-button').click()]);

    /* Assert */
    expect(dbQuery(`SELECT invoice_group_id FROM ip_invoice_groups WHERE invoice_group_id = ${doomed.id}`)).toEqual([]);
    expect(dbQuery(`SELECT invoice_group_id FROM ip_invoice_groups WHERE invoice_group_id = ${kept.id}`)).toHaveLength(1);
  });

  test('it still deletes an invoice group when csrf protection is on and the token is valid', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });

  test('it does not delete an invoice group when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Invoice groups — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no invoice group', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/invoice_groups');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Secret Group');
  });
});
