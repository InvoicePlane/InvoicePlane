/**
 * Browser coverage for application/modules/custom_fields/controllers/Custom_fields.php.
 * Mirrors tests/Feature/Core/CustomFieldsControllerTest.php.
 * Required fields (Mdl_Custom_fields::validation_rules): custom_field_table
 * (allow-listed), custom_field_label, custom_field_type.
 *
 * The table/type selects on the create form are JS-populated, so the
 * create/validation cases post the form directly (as the PHPUnit tests do); the
 * list, edit-render and delete paths run through the real UI.
 */

import { test, expect } from '../test.js';
import { seedCustomField, uniq } from '../support/fixtures.js';
import { dbQuery } from '../support/db.js';
import { postForm } from '../support/http.js';

const REQUIRED = {
  custom_field_table: 'ip_client_custom',
  custom_field_label: 'Payload Field',
  custom_field_type: 'TEXT',
};
const body = (over = {}) => ({ ...REQUIRED, btn_submit: '1', ...over });

test.describe('Custom fields — list', () => {
  test('it lists every custom field', async ({ page }) => {
    /* Arrange */
    const a = seedCustomField({ custom_field_label: uniq('LoyaltyTier') });
    const b = seedCustomField({ custom_field_label: uniq('ReferralSource') });

    /* Act */
    await page.goto('/custom_fields/table/all');

    /* Assert */
    await expect(page.locator('#content')).toContainText(a.label);
    await expect(page.locator('#content')).toContainText(b.label);
  });
});

test.describe('Custom fields — create', () => {
  test('it creates a custom field', async ({ page }) => {
    /* Arrange */
    const label = uniq('ClientIndustry');

    /* Act */
    const response = await postForm(page, '/custom_fields/form', body({ custom_field_label: label }));

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT custom_field_table FROM ip_custom_fields WHERE custom_field_label = '${label}'`))
      .toEqual([{ custom_field_table: 'ip_client_custom' }]);
  });

  for (const field of ['custom_field_table', 'custom_field_label', 'custom_field_type']) {
    test(`it fails to create without ${field}`, async ({ page }) => {
      /* Arrange */
      const label = uniq('Missing');

      /* Act */
      const response = await postForm(page, '/custom_fields/form', body({ custom_field_label: label, [field]: '' }));

      /* Assert */
      expect(response.status()).toBe(200);
      expect(dbQuery(`SELECT custom_field_id FROM ip_custom_fields WHERE custom_field_label = '${label}'`)).toEqual([]);
    });
  }

  test('it rejects a custom field table that is not allow listed', async ({ page }) => {
    /* Arrange */
    const label = uniq('InjectedField');

    /* Act */
    const response = await postForm(page, '/custom_fields/form', body({ custom_field_table: 'ip_users', custom_field_label: label }));

    /* Assert */
    expect(response.status()).toBe(200);
    expect(dbQuery(`SELECT custom_field_id FROM ip_custom_fields WHERE custom_field_label = '${label}'`)).toEqual([]);
  });
});

test.describe('Custom fields — update', () => {
  test('it renders the edit form for the requested custom field only', async ({ page }) => {
    /* Arrange */
    const target = seedCustomField({ custom_field_label: uniq('EditableCustomField') });
    const other = seedCustomField({ custom_field_label: uniq('OtherCustomField') });

    /* Act */
    await page.goto(`/custom_fields/form/${target.id}`);

    /* Assert */
    await expect(page.locator('#custom_field_label')).toHaveValue(target.label);
    await expect(page.locator('body')).not.toContainText(other.label);
  });

  test('it updates a custom field', async ({ page }) => {
    /* Arrange */
    const field = seedCustomField({ custom_field_label: uniq('OriginalCustomField') });
    const renamed = uniq('RenamedCustomField');

    /* Act */
    const response = await postForm(page, `/custom_fields/form/${field.id}`, body({ custom_field_label: renamed }));

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT custom_field_label FROM ip_custom_fields WHERE custom_field_id = ${field.id}`))
      .toEqual([{ custom_field_label: renamed }]);
  });

  test('it fails to update without custom_field_label', async ({ page }) => {
    /* Arrange */
    const field = seedCustomField({ custom_field_label: uniq('KeepThisField') });

    /* Act */
    const response = await postForm(page, `/custom_fields/form/${field.id}`, body({ custom_field_label: '' }));

    /* Assert */
    expect(response.status()).toBe(200);
    expect(dbQuery(`SELECT custom_field_label FROM ip_custom_fields WHERE custom_field_id = ${field.id}`))
      .toEqual([{ custom_field_label: field.label }]);
  });
});

test.describe('Custom fields — delete', () => {
  test('it deletes a custom field', async ({ page }) => {
    /* Arrange */
    const doomed = seedCustomField({ custom_field_label: uniq('DeletableField') });
    const kept = seedCustomField({ custom_field_label: uniq('KeptField') });

    /* Act */
    await page.goto('/custom_fields/table/all');
    const row = page.locator('tr', { hasText: doomed.label });
    await row.locator('.dropdown-toggle').click();
    page.once('dialog', (dialog) => dialog.accept());
    await Promise.all([page.waitForLoadState('load'), row.locator('button.dropdown-button').click()]);

    /* Assert */
    expect(dbQuery(`SELECT custom_field_id FROM ip_custom_fields WHERE custom_field_id = ${doomed.id}`)).toEqual([]);
    expect(dbQuery(`SELECT custom_field_id FROM ip_custom_fields WHERE custom_field_id = ${kept.id}`)).toHaveLength(1);
  });
});

test.describe('Custom fields — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no custom field', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/custom_fields/table/all');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Secret Field');
  });
});
