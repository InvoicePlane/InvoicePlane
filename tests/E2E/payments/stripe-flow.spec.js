/**
 * Browser coverage for the Stripe guest gateway flow
 * (/guest/gateways/stripe/*) on application/modules/stripe/.
 * Mirrors tests/Feature/Payments/StripeFlowTest.php.
 *
 * Same split as paypal-flow.spec.js: the guard clauses run before any Stripe
 * call and are exercised here; the tests that assert on Stripe's response
 * (checkout session, callback recording, currency/amount checks) need the
 * outbound HTTP call stubbed and stay `test.fixme`, covered by StripeFlowTest's
 * fakes.
 */

import { test, expect } from '../test.js';
import { createPayableGuestInvoice } from '../support/fixtures.js';
import { dbQuery } from '../support/db.js';

const SESSION = (key) => `/guest/gateways/stripe/stripe_checkout_session/${key}`;

test.describe('Stripe — checkout session guards', () => {
  test('it returns 404 for a non post checkout session request', async ({ page }) => {
    /* Arrange */
    const invoice = await createPayableGuestInvoice(page);

    /* Act + Assert */
    expect((await page.request.get(SESSION(invoice.key))).status()).toBe(404);
  });

  test('it returns 404 for checkout session on an unknown invoice key', async ({ page }) => {
    /* Arrange + Act + Assert */
    expect((await page.request.post(SESSION('does-not-exist'))).status()).toBe(404);
  });

  test('it returns 404 for checkout session on a draft invoice', async ({ page }) => {
    /* Arrange */
    const invoice = await createPayableGuestInvoice(page, { statusId: 1 });

    /* Act + Assert */
    expect((await page.request.post(SESSION(invoice.key))).status()).toBe(404);
  });

  test('it redirects checkout session for an already paid invoice without calling stripe', async ({ page }) => {
    /* Arrange */
    const invoice = await createPayableGuestInvoice(page, { statusId: 4, balance: '0.00' });

    /* Act */
    const response = await page.request.post(SESSION(invoice.key), { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).not.toBe(200);
    expect(dbQuery(`SELECT payment_id FROM ip_payments WHERE invoice_id = ${invoice.id}`)).toEqual([]);
  });
});

test.describe('Stripe — gateway response handling (needs a stubbed gateway)', () => {
  const NEEDS_STUB = 'needs a server-side Stripe stub — covered by tests/Feature/Payments/StripeFlowTest.php';

  for (const title of [
    'it creates a checkout session for a payable invoice',
    'it sends a jpy invoice total as 100 minor units to stripe checkout',
    'it records a paid callback and creates a payment',
    'it does not duplicate a payment for an already processed payment intent',
    'it does not record a payment when the invoice is already fully paid',
    'it rejects a callback whose currency does not match the gateway setting',
    'it rejects a callback whose amount is short of the invoice balance',
    'it does not record a payment for an unpaid callback',
    'it records an error response when the callback invoice is not guest visible',
  ]) {
    test(title, () => {
      test.fixme(true, NEEDS_STUB);
    });
  }
});
