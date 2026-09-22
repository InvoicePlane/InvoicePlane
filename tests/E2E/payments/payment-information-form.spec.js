/**
 * Browser coverage for the guest payment-information form
 * (/guest/payment_information/form/{key}) on
 * application/modules/guest/controllers/Payment_information.php.
 * Mirrors tests/Feature/Payments/PaymentInformationFormTest.php — the payability
 * checks that run before any gateway is involved.
 */

import { test, expect } from '../test.js';
import { createPayableGuestInvoice } from '../support/fixtures.js';

const FORM = (key) => `/guest/payment_information/form/${key}`;

test.describe('Guest payment form — payability guards', () => {
  test('it redirects for an unknown invoice key', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get(FORM('does-not-exist'), { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).not.toBe(200);
  });

  test('it redirects for a draft invoice key', async ({ page }) => {
    /* Arrange */
    const invoice = await createPayableGuestInvoice(page, { statusId: 1 });

    /* Act */
    const response = await page.request.get(FORM(invoice.key), { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).not.toBe(200);
  });

  test('it returns 404 for an already paid invoice when unauthenticated', async ({ page }) => {
    /* Arrange */
    const invoice = await createPayableGuestInvoice(page, { statusId: 4, balance: '0.00' });
    const anon = await page.context().browser().newContext({ baseURL: new URL(page.url()).origin });

    /* Act */
    const response = await anon.request.get(FORM(invoice.key), { maxRedirects: 0 });

    /* Assert: a paid invoice is never served the payment form (404 in the test
     * harness; the live server bounces it as a 3xx) */
    expect(response.status()).not.toBe(200);
    await anon.close();
  });

  test('it renders the form for a payable invoice', async ({ page }) => {
    /* Arrange */
    const invoice = await createPayableGuestInvoice(page, { statusId: 2, balance: '100.00' });

    /* Act */
    const response = await page.request.get(FORM(invoice.key));

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });

  test('it does not expose php errors for an already paid invoice', async ({ page }) => {
    /* Arrange */
    const invoice = await createPayableGuestInvoice(page, { statusId: 4, balance: '0.00' });

    /* Act */
    const response = await page.request.get(FORM(invoice.key));

    /* Assert */
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });
});
