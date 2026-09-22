/**
 * Browser coverage for application/modules/payment_methods/controllers/Payment_methods.php.
 * Mirrors tests/Feature/Payments/PaymentMethodsControllerTest.php.
 * Required field: payment_method_name (unique).
 */

import { test, expect } from '../test.js';
import { createPaymentMethod, uniq } from '../support/fixtures.js';
import { dbQuery } from '../support/db.js';
import { expectBlockedByRequired, expectErrorFlash } from '../support/forms.js';

test.describe('Payment methods — list', () => {
  test('it lists every payment method', async ({ page }) => {
    /* Arrange */
    const a = await createPaymentMethod(page, uniq('BankTransfer'));
    const b = await createPaymentMethod(page, uniq('CashOnDelivery'));

    /* Act */
    await page.goto('/payment_methods');

    /* Assert */
    await expect(page.locator('#content')).toContainText(a.name);
    await expect(page.locator('#content')).toContainText(b.name);
  });
});

test.describe('Payment methods — create', () => {
  test('it creates a payment method', async ({ page }) => {
    /* Arrange */
    const name = uniq('Cheque');

    /* Act */
    await page.goto('/payment_methods/form');
    await page.fill('#payment_method_name', name);
    await Promise.all([page.waitForURL(/\/payment_methods(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.locator('#content')).toContainText(name);
  });

  test('it fails to create without payment_method_name', async ({ page }) => {
    /* Arrange */
    await page.goto('/payment_methods/form');

    /* Act + Assert */
    await expectBlockedByRequired(page, '#payment_method_name');
  });

  test('it rejects a duplicate payment method name on create', async ({ page }) => {
    /* Arrange */
    const name = uniq('DuplicateMethod');
    await createPaymentMethod(page, name);

    /* Act */
    await page.goto('/payment_methods/form');
    await page.fill('#payment_method_name', name);
    await Promise.all([page.waitForLoadState('load'), page.click('#btn-submit')]);

    /* Assert */
    await expect(page).toHaveURL(/\/payment_methods\/form$/);
    await expectErrorFlash(page);
  });
});

test.describe('Payment methods — update', () => {
  test('it renders the edit form for the requested payment method only', async ({ page }) => {
    /* Arrange */
    const target = await createPaymentMethod(page, uniq('EditableMethod'));
    const other = await createPaymentMethod(page, uniq('OtherMethod'));

    /* Act */
    await page.goto(`/payment_methods/form/${target.id}`);

    /* Assert */
    await expect(page.locator('#payment_method_name')).toHaveValue(target.name);
    await expect(page.locator('body')).not.toContainText(other.name);
  });

  test('it updates a payment method', async ({ page }) => {
    /* Arrange */
    const method = await createPaymentMethod(page, uniq('OriginalMethod'));
    const renamed = uniq('RenamedMethod');

    /* Act */
    await page.goto(`/payment_methods/form/${method.id}`);
    await page.fill('#payment_method_name', renamed);
    await Promise.all([page.waitForURL(/\/payment_methods(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.locator('#content')).toContainText(renamed);
  });

  test('it fails to update without payment_method_name', async ({ page }) => {
    /* Arrange */
    const method = await createPaymentMethod(page, uniq('KeepThisMethod'));

    /* Act + Assert */
    await page.goto(`/payment_methods/form/${method.id}`);
    await page.fill('#payment_method_name', '');
    await expectBlockedByRequired(page, '#payment_method_name');
  });
});

test.describe('Payment methods — delete', () => {
  test('it deletes a payment method', async ({ page }) => {
    /* Arrange */
    const doomed = await createPaymentMethod(page, uniq('DeletableMethod'));
    const kept = await createPaymentMethod(page, uniq('KeptMethod'));

    /* Act */
    await page.goto('/payment_methods');
    const row = page.locator('tr', { hasText: doomed.name });
    await row.locator('.dropdown-toggle').click();
    page.once('dialog', (dialog) => dialog.accept());
    await Promise.all([page.waitForLoadState('load'), row.locator('button.dropdown-button').click()]);

    /* Assert */
    expect(dbQuery(`SELECT payment_method_id FROM ip_payment_methods WHERE payment_method_id = ${doomed.id}`)).toEqual([]);
    expect(dbQuery(`SELECT payment_method_id FROM ip_payment_methods WHERE payment_method_id = ${kept.id}`)).toHaveLength(1);
  });

  test('it still deletes a payment method when csrf protection is on and the token is valid', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });

  test('it does not delete a payment method when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Payment methods — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no payment method', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/payment_methods');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Secret Method');
  });
});
