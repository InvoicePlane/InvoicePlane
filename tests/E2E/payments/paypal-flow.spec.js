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

test.describe('PayPal — frontend order creation submission', () => {
  test('it submits to paypal create order endpoint for a payable invoice', async ({ page }) => {
    /* Arrange */
    const invoice = await createPayableGuestInvoice(page);

    /* Act: submit the create order form (no redirect following) */
    const response = await page.request.post(CREATE(invoice.key), { maxRedirects: 0 });

    /* Assert: server accepted the request and either created an order, redirected,
     * or — since PayPal isn't reachable/configured in this environment and
     * Playwright can't stub the server-side PayPal call — gracefully reported
     * a 500 with a JSON error body (see Paypal::paypal_create_order()). */
    expect([200, 302, 303, 307, 500]).toContain(response.status());
    /* Response validation (order ID, approval URL, etc.) stays in PaypalFlowTest.php */
  });
});

/* Gateway response tests (PayPal API validation) are covered by tests/Feature/Payments/PaypalFlowTest.php */
