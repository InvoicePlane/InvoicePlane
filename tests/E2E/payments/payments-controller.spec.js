/**
 * Browser coverage for application/modules/payments/controllers/Payments.php.
 * Mirrors tests/Feature/Payments/PaymentsFeatureTest.php.
 * Required fields (Mdl_Payments::validation_rules): invoice_id, payment_amount
 * (also `callback_validate_payment_amount` — can't exceed the invoice balance),
 * payment_date.
 *
 * The form's #invoice_id is a select2 widget whose native <select> is hidden, so
 * the create/update/validation cases post the form directly (as the PHPUnit
 * tests do) rather than driving the widget.
 */

import { test, expect } from '../test.js';
import { createInvoiceWithBalance, createPayment, uniq } from '../support/fixtures.js';
import { dbInsert, dbQuery } from '../support/db.js';
import { postForm } from '../support/http.js';

/** Raw payment row (does not recompute the invoice balance), like PHPUnit's seedPayment. */
function seedPayment(invoiceId, amount) {
  return dbInsert('ip_payments', {
    invoice_id: invoiceId,
    payment_method_id: 0,
    payment_date: new Date().toISOString().slice(0, 10),
    payment_amount: amount,
    payment_note: 'seed',
  });
}

const today = () => new Date().toISOString().slice(0, 10);
const formBody = (invoiceId, over = {}) => ({
  invoice_id: String(invoiceId),
  payment_method_id: '0',
  payment_amount: '50.00',
  payment_date: today(),
  payment_note: 'e2e',
  btn_submit: '1',
  ...over,
});

test.describe('Payments — list', () => {
  test('it lists payments', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoiceWithBalance(page, '250.00');
    const payment = await createPayment(page, invoice.id, { payment_amount: '99.00' });

    /* Act */
    await page.goto('/payments');

    /* Assert */
    await expect(page.locator(`form[action*="payments/delete/${payment.id}"]`)).toHaveCount(1);
  });
});

test.describe('Payments — create', () => {
  test('it creates a payment and links it to the invoice', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoiceWithBalance(page, '250.00');

    /* Act */
    const response = await postForm(page, '/payments/form', formBody(invoice.id, { payment_amount: '250.00', payment_note: 'Test payment' }));

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT payment_amount FROM ip_payments WHERE invoice_id = ${invoice.id}`))
      .toEqual([{ payment_amount: '250.00' }]);
  });

  for (const field of ['invoice_id', 'payment_amount', 'payment_date']) {
    test(`it fails to create without ${field}`, async ({ page }) => {
      /* Arrange */
      const invoice = await createInvoiceWithBalance(page, '100.00');

      /* Act */
      const response = await postForm(page, '/payments/form', formBody(invoice.id, { [field]: '' }));

      /* Assert: an invalid create re-renders the form (200), never redirects */
      expect(response.status()).toBe(200);
      expect(dbQuery('SELECT payment_id FROM ip_payments')).toEqual([]);
    });
  }
});

test.describe('Payments — update', () => {
  test('it renders the edit payment form showing existing amount', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoiceWithBalance(page, '300.00');
    const paymentId = seedPayment(invoice.id, '175.50');

    /* Act */
    await page.goto(`/payments/form/${paymentId}`);

    /* Assert: the form shows the stored amount (however the locale rounds it) */
    await expect(page.locator('#payment_amount')).toHaveValue(new RegExp(String(Math.round(175.5))));
  });

  test('it updates a payment', async ({ page }) => {
    /* Arrange: raw seed keeps the invoice balance at 300, so 300 is in range */
    const invoice = await createInvoiceWithBalance(page, '300.00');
    const paymentId = seedPayment(invoice.id, '100.00');

    /* Act */
    const response = await postForm(page, `/payments/form/${paymentId}`, formBody(invoice.id, { payment_amount: '300.00', payment_note: 'Updated payment' }));

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT payment_amount FROM ip_payments WHERE payment_id = ${paymentId}`))
      .toEqual([{ payment_amount: '300.00' }]);
  });
});

test.describe('Payments — delete', () => {
  test('it deletes a payment', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoiceWithBalance(page, '100.00');
    const payment = await createPayment(page, invoice.id, { payment_amount: '50.00' });

    /* Act */
    const response = await postForm(page, `/payments/delete/${payment.id}`, {});

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT payment_id FROM ip_payments WHERE payment_id = ${payment.id}`)).toEqual([]);
  });
});

test.describe('Payments — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects an unauthenticated visitor away from the payments list', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/payments');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain(uniq('never-rendered'));
  });
});
