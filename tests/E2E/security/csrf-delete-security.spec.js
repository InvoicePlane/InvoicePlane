/**
 * Browser coverage for tests/Feature/Security/CsrfDeleteSecurityTest.php —
 * delete endpoints are POST-only, index pages never link to them with a GET
 * anchor, and every state-changing POST form carries the _ip_csrf field.
 */

import { test, expect } from '../test.js';
import { createClient, createProduct, createTaxRate, uniq } from '../support/fixtures.js';

const INDEX_PAGES = ['/clients/status/all', '/products', '/tax_rates', '/invoice_groups', '/payment_methods'];

test.describe('CSRF delete security', () => {
  test('it requires post validation for delete endpoints', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page);
    const product = await createProduct(page);

    /* Act + Assert: a GET to a delete route is never acted on */
    for (const url of [`/clients/delete/${client.id}`, `/products/delete/${product.id}`]) {
      const response = await page.request.get(url, { maxRedirects: 0 });
      expect(response.status(), url).not.toBe(200);
    }
  });

  test('it does not link to delete endpoints with get anchors', async ({ page }) => {
    /* Arrange */
    await createClient(page, { client_name: uniq('AnchorCheck') });
    await createProduct(page);
    await createTaxRate(page);

    /* Act + Assert */
    for (const url of INDEX_PAGES) {
      await page.goto(url);
      const getDeleteAnchors = await page.locator('a[href*="/delete/"]').count();
      expect(getDeleteAnchors, `${url} must not expose a GET delete link`).toBe(0);
    }
  });

  test('it includes csrf tokens in post forms', async ({ page }) => {
    /* Arrange */
    await createClient(page, { client_name: uniq('CsrfFieldCheck') });

    /* Act + Assert: every method=post form carries the _ip_csrf hidden input */
    for (const url of INDEX_PAGES) {
      await page.goto(url);
      const postForms = page.locator('form[method="post" i]');
      const count = await postForms.count();
      for (let i = 0; i < count; i++) {
        await expect(
          postForms.nth(i).locator('input[name="_ip_csrf"]'),
          `${url} form #${i} must include _ip_csrf`,
        ).toHaveCount(1);
      }
    }
  });
});
