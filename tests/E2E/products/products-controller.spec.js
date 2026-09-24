/**
 * Browser coverage for application/modules/products/controllers/Products.php.
 * Mirrors tests/Feature/Products/ProductsControllerTest.php.
 * Required fields (Mdl_Products::validation_rules): product_name, product_price.
 */

import { test, expect } from '../test.js';
import { createProduct, uniq } from '../support/fixtures.js';
import { expectBlockedByRequired } from '../support/forms.js';

test.describe('Products — list', () => {
  test('it lists every product', async ({ page }) => {
    /* Arrange */
    const a = await createProduct(page, { product_name: uniq('WidgetAlpha') });
    const b = await createProduct(page, { product_name: uniq('WidgetBeta') });

    /* Act */
    await page.goto('/products');

    /* Assert */
    await expect(page.getByRole('link', { name: a.name })).toBeVisible();
    await expect(page.getByRole('link', { name: b.name })).toBeVisible();
  });
});

test.describe('Products — create', () => {
  test('it creates a product', async ({ page }) => {
    /* Arrange */
    const name = uniq('DeluxeWidget');

    /* Act */
    await page.goto('/products/form');
    await page.fill('#product_name', name);
    await page.fill('#product_price', '19.99');
    await Promise.all([page.waitForURL(/\/products(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.getByRole('link', { name })).toBeVisible();
  });

  test('it fails to create without product_name', async ({ page }) => {
    /* Arrange */
    await page.goto('/products/form');
    await page.fill('#product_price', '9.99');

    /* Act + Assert */
    await expectBlockedByRequired(page, '#product_name');
  });

  test('it fails to create without product_price', async ({ page }) => {
    /* Arrange */
    await page.goto('/products/form');
    await page.fill('#product_name', 'Priceless Widget');

    /* Act + Assert */
    await expectBlockedByRequired(page, '#product_price');
  });
});

test.describe('Products — update', () => {
  test('it renders the edit form for the requested product only', async ({ page }) => {
    /* Arrange */
    const target = await createProduct(page, { product_name: uniq('EditableWidget') });
    const other = await createProduct(page, { product_name: uniq('OtherWidget') });

    /* Act */
    await page.goto(`/products/form/${target.id}`);

    /* Assert */
    await expect(page.locator('#product_name')).toHaveValue(target.name);
    await expect(page.locator('body')).not.toContainText(other.name);
  });

  test('it updates a product', async ({ page }) => {
    /* Arrange */
    const product = await createProduct(page, { product_name: uniq('OriginalWidget'), product_price: '10.00' });
    const renamed = uniq('RenamedWidget');

    /* Act */
    await page.goto(`/products/form/${product.id}`);
    await page.fill('#product_name', renamed);
    await page.fill('#product_price', '12.50');
    await Promise.all([page.waitForURL(/\/products(\/index)?$/), page.click('#btn-submit')]);

    /* Assert */
    await expect(page.getByRole('link', { name: renamed })).toBeVisible();
    await expect(page.getByRole('link', { name: product.name, exact: true })).toHaveCount(0);
  });

  test('it fails to update without product_name', async ({ page }) => {
    /* Arrange */
    const product = await createProduct(page, { product_name: uniq('KeepThisWidget') });

    /* Act + Assert */
    await page.goto(`/products/form/${product.id}`);
    await page.fill('#product_name', '');
    await expectBlockedByRequired(page, '#product_name');
  });

  test('it fails to update without product_price', async ({ page }) => {
    /* Arrange */
    const product = await createProduct(page, { product_name: uniq('PriceKeptWidget'), product_price: '7.00' });

    /* Act + Assert */
    await page.goto(`/products/form/${product.id}`);
    await page.fill('#product_price', '');
    await expectBlockedByRequired(page, '#product_price');
  });
});

test.describe('Products — delete', () => {
  test('it deletes a product', async ({ page }) => {
    /* Arrange */
    const doomed = await createProduct(page, { product_name: uniq('DeletableWidget') });
    const kept = await createProduct(page, { product_name: uniq('KeptWidget') });

    /* Act */
    await page.goto('/products');
    const row = page.locator('tr', { has: page.getByRole('link', { name: doomed.name }) });
    await row.locator('.dropdown-toggle').click();
    page.once('dialog', (dialog) => dialog.accept());
    await Promise.all([page.waitForLoadState('load'), row.locator('button.dropdown-button').click()]);

    /* Assert */
    await page.goto('/products');
    await expect(page.getByRole('link', { name: doomed.name })).toHaveCount(0);
    await expect(page.getByRole('link', { name: kept.name })).toBeVisible();
  });

  test('it still deletes a product when csrf protection is on and the token is valid', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });

  test('it does not delete a product when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Products — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no product', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/products');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Secret Widget');
  });
});
