/**
 * Browser coverage for application/modules/products/controllers/Ajax.php.
 * Mirrors tests/Feature/Products/ProductsAjaxControllerTest.php — the product
 * lookup modal used on invoice/quote item rows. `$ajax_controller = true`, so
 * every request carries the X-Requested-With header a real XHR would send.
 */

import { test, expect } from '../test.js';
import { createProduct, uniq } from '../support/fixtures.js';

const XHR = { 'X-Requested-With': 'XMLHttpRequest' };

test.describe('Products AJAX — lookup modal', () => {
  test('it renders the full lookup modal with no filters', async ({ page }) => {
    /* Arrange */
    const product = await createProduct(page, { product_name: uniq('ModalProductMarker') });

    /* Act */
    const response = await page.request.get('/products/ajax/modal_product_lookups', { headers: XHR });
    const body = await response.text();

    /* Assert */
    expect(body).toContain(product.name);
    expect(body).toContain('filter_product');
  });

  test('it filters the lookup table by product name', async ({ page }) => {
    /* Arrange */
    const match = await createProduct(page, { product_name: uniq('FilterMatchProduct') });
    const other = await createProduct(page, { product_name: uniq('OtherProduct') });

    /* Act */
    const response = await page.request.get(
      `/products/ajax/modal_product_lookups?filter_product=${encodeURIComponent(match.name)}`,
      { headers: XHR },
    );
    const body = await response.text();

    /* Assert */
    expect(body).toContain(match.name);
    expect(body).not.toContain(other.name);
  });
});

test.describe('Products AJAX — selection', () => {
  test('it processes a product selection', async ({ page }) => {
    /* Arrange */
    const product = await createProduct(page, { product_name: uniq('SelectedProduct'), product_price: '42.00' });

    /* Act */
    const response = await page.request.post('/products/ajax/process_product_selections', {
      headers: XHR,
      form: { 'product_ids[]': String(product.id) },
    });
    const json = await response.json();

    /* Assert */
    expect(json.map((row) => row.product_name)).toContain(product.name);
  });

  test('it returns an empty result when no product ids are selected', async ({ page }) => {
    /* Arrange */
    await createProduct(page, { product_name: uniq('NotSelectedProduct') });

    /* Act */
    const response = await page.request.post('/products/ajax/process_product_selections', { headers: XHR });

    /* Assert */
    expect(await response.json()).toEqual([]);
  });
});
