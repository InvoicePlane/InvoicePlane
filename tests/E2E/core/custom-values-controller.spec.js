/**
 * Browser coverage for application/modules/custom_values/controllers/Custom_values.php.
 * Mirrors tests/Feature/Core/CustomValuesControllerTest.php — the choice values
 * that belong to a MULTIPLE-CHOICE custom field.
 * Required field (Mdl_Custom_values::validation_rules): custom_values_value.
 */

import { test, expect } from '../test.js';
import { uniq } from '../support/fixtures.js';
import { dbInsert, dbQuery } from '../support/db.js';
import { postForm } from '../support/http.js';

/** A MULTIPLE-CHOICE custom field (the container for choice values). */
function seedChoiceField() {
  return dbInsert('ip_custom_fields', {
    custom_field_table: 'ip_client_custom',
    custom_field_label: uniq('ClientSegment'),
    custom_field_type: 'MULTIPLE-CHOICE',
  });
}
const seedValue = (fieldId, value) =>
  dbInsert('ip_custom_values', { custom_values_field: fieldId, custom_values_value: value });
const valueExists = (id) => dbQuery(`SELECT custom_values_id FROM ip_custom_values WHERE custom_values_id = ${id}`).length === 1;

test.describe('Custom values — list', () => {
  test('it lists every value for a field', async ({ page }) => {
    /* Arrange */
    const fieldId = seedChoiceField();
    const a = uniq('Enterprise');
    const b = uniq('SmallBusiness');
    seedValue(fieldId, a);
    seedValue(fieldId, b);

    /* Act */
    await page.goto(`/custom_values/field/${fieldId}`);

    /* Assert */
    await expect(page.locator('#content')).toContainText(a);
    await expect(page.locator('#content')).toContainText(b);
  });
});

test.describe('Custom values — create', () => {
  test('it creates a value for a field', async ({ page }) => {
    /* Arrange */
    const fieldId = seedChoiceField();
    const value = uniq('NonProfit');

    /* Act */
    const response = await postForm(page, `/custom_values/create/${fieldId}`, {
      custom_field_id: String(fieldId),
      custom_values_value: value,
      btn_submit: '1',
    });

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT custom_values_value FROM ip_custom_values WHERE custom_values_field = ${fieldId}`))
      .toEqual([{ custom_values_value: value }]);
  });

  test('it fails to create a value without custom_values_value', async ({ page }) => {
    /* Arrange */
    const fieldId = seedChoiceField();

    /* Act */
    const response = await postForm(page, `/custom_values/create/${fieldId}`, {
      custom_field_id: String(fieldId),
      custom_values_value: '',
      btn_submit: '1',
    });

    /* Assert */
    expect(response.status()).toBe(200);
    expect(dbQuery(`SELECT custom_values_id FROM ip_custom_values WHERE custom_values_field = ${fieldId}`)).toEqual([]);
  });
});

test.describe('Custom values — update', () => {
  test('it updates a value', async ({ page }) => {
    /* Arrange */
    const fieldId = seedChoiceField();
    const id = seedValue(fieldId, uniq('OriginalSegment'));
    const renamed = uniq('RenamedSegment');

    /* Act */
    const response = await postForm(page, `/custom_values/edit/${id}`, {
      custom_field_id: String(fieldId),
      custom_values_value: renamed,
      btn_submit: '1',
    });

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT custom_values_value FROM ip_custom_values WHERE custom_values_id = ${id}`))
      .toEqual([{ custom_values_value: renamed }]);
  });

  test('it fails to update a value without custom_values_value', async ({ page }) => {
    /* Arrange */
    const fieldId = seedChoiceField();
    const kept = uniq('KeepThisSegment');
    const id = seedValue(fieldId, kept);

    /* Act */
    const response = await postForm(page, `/custom_values/edit/${id}`, {
      custom_field_id: String(fieldId),
      custom_values_value: '',
      btn_submit: '1',
    });

    /* Assert */
    expect(response.status()).toBe(200);
    expect(dbQuery(`SELECT custom_values_value FROM ip_custom_values WHERE custom_values_id = ${id}`))
      .toEqual([{ custom_values_value: kept }]);
  });
});

test.describe('Custom values — delete', () => {
  test('it deletes a value', async ({ page }) => {
    /* Arrange */
    const fieldId = seedChoiceField();
    const doomed = seedValue(fieldId, uniq('Doomed'));
    const kept = seedValue(fieldId, uniq('Kept'));

    /* Act */
    const response = await postForm(page, `/custom_values/delete/${doomed}`, {});

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(valueExists(doomed)).toBe(false);
    expect(valueExists(kept)).toBe(true);
  });

  test('it does not delete a value on a plain get request', async ({ page }) => {
    /* Arrange */
    const fieldId = seedChoiceField();
    const id = seedValue(fieldId, uniq('GetSegmentKept'));

    /* Act */
    const response = await page.request.get(`/custom_values/delete/${id}`, { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).toBe(404);
    expect(valueExists(id)).toBe(true);
  });

  test('it still deletes a value when csrf protection is on and the token is valid', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });

  test('it does not delete a value when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Custom values — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no value', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/custom_values/field/1');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Secret Segment');
  });
});
