/**
 * Browser coverage for the PayPal guest gateway flow
 * (/guest/gateways/paypal/*) on application/modules/paypal/.
 * Mirrors tests/Feature/Payments/PaypalFlowTest.php.
 *
 * The guard clauses (method, key, status, already-paid) run before any call to
 * PayPal and are exercised here. The tests that assert on PayPal's *response*
 * (order creation, capture recording, malformed-JSON handling) need the
 * outbound HTTP call stubbed — the PHPUnit suite does this with a fake gateway;
 * Playwright can't intercept a server-side PHP → PayPal request, so those stay
 * `test.fixme` and remain covered by PaypalFlowTest's fakes.
 */

import { test, expect } from '../test.js';
import { createPayableGuestInvoice } from '../support/fixtures.js';
import { dbQuery } from '../support/db.js';

const CREATE = (key) => `/guest/gateways/paypal/paypal_create_order/${key}`;
const CAPTURE = (order) => `/guest/gateways/paypal/paypal_capture_payment/${order}`;

test.describe('PayPal — create order guards', () => {
  test('it returns 404 for a non post create order request', async ({ page }) => {
    /* Arrange */
    const invoice = await createPayableGuestInvoice(page);

    /* Act + Assert */
    expect((await page.request.get(CREATE(invoice.key))).status()).toBe(404);
  });

  test('it returns 404 for create order on an unknown invoice key', async ({ page }) => {
    /* Arrange + Act + Assert */
    expect((await page.request.post(CREATE('does-not-exist'))).status()).toBe(404);
  });

  test('it returns 404 for create order on a draft invoice', async ({ page }) => {
    /* Arrange */
    const invoice = await createPayableGuestInvoice(page, { statusId: 1 });

    /* Act + Assert */
    expect((await page.request.post(CREATE(invoice.key))).status()).toBe(404);
  });

  test('it redirects create order for an already paid invoice without calling paypal', async ({ page }) => {
    /* Arrange */
    const invoice = await createPayableGuestInvoice(page, { statusId: 4, balance: '0.00' });

    /* Act */
    const response = await page.request.post(CREATE(invoice.key), { maxRedirects: 0 });

    /* Assert: bounced before any gateway call, no payment recorded */
    expect(response.status()).not.toBe(200);
    expect(dbQuery(`SELECT payment_id FROM ip_payments WHERE invoice_id = ${invoice.id}`)).toEqual([]);
  });
});

test.describe('PayPal — capture payment guards', () => {
  test('it returns 404 for a non post capture payment request', async ({ page }) => {
    /* Arrange + Act + Assert */
    expect((await page.request.get(CAPTURE('ORDER-1'))).status()).toBe(404);
  });
});

test.describe('PayPal — gateway response handling (needs a stubbed gateway)', () => {
  const NEEDS_STUB = 'needs a server-side PayPal stub — covered by tests/Feature/Payments/PaypalFlowTest.php';

  for (const title of [
    'it creates a paypal order for a payable invoice',
    'it returns 500 when paypal returns malformed json for create order',
    'it returns 500 when paypal response is missing the order id',
    'it records a completed capture and creates a payment',
    'it records a pending capture as a payment with a pending note',
    'it does not duplicate a payment for an already processed capture id',
    'it does not record a payment when the invoice is already fully paid',
    'it rejects a capture whose currency does not match the gateway setting',
    'it rejects a capture whose amount is short of the invoice balance',
    'it records a declined capture as an unsuccessful merchant response',
    'it throws and records nothing when the captured invoice is not guest visible',
  ]) {
    test(title, () => {
      test.fixme(true, NEEDS_STUB);
    });
  }
});
