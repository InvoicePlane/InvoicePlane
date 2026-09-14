/**
 * Browser coverage for the payment-provider allowlist on the guest payment
 * form. Mirrors tests/Feature/Payments/PaymentProviderAllowlistTest.php — an
 * unknown / traversal / internal-method provider segment must 404, and no
 * provider segment must not crash.
 */

import { test, expect } from '../test.js';
import { createPayableGuestInvoice } from '../support/fixtures.js';

let key;

test.beforeEach(async ({ page }) => {
  ({ key } = await createPayableGuestInvoice(page, { statusId: 2, balance: '100.00' }));
});

test.describe('Payment provider allowlist', () => {
  test('it returns 200 when accessing the payment form without a provider', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get(`/guest/payment_information/form/${key}`);

    /* Assert */
    expect(response.status()).not.toBe(500);
  });

  test('it returns 404 for an unknown payment provider segment', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get(`/guest/payment_information/form/${key}/malicious_method`);

    /* Assert */
    expect(response.status()).toBe(404);
  });

  test('it returns 404 for an internal controller method name as provider', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get(`/guest/payment_information/form/${key}/_remap`);

    /* Assert */
    expect(response.status()).toBe(404);
  });

  test('it returns 404 for a path traversal attempt as provider', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get(
      `/guest/payment_information/form/${key}/${encodeURIComponent('../../../../etc/passwd')}`,
    );

    /* Assert */
    expect(response.status()).toBe(404);
  });
});
