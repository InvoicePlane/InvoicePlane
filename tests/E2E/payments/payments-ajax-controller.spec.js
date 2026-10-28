/**
 * Browser coverage for application/modules/payments/controllers/Ajax.php.
 * Mirrors tests/Feature/Payments/PaymentsAjaxControllerTest.php — the
 * add-payment modal used from the invoice view. `$ajax_controller = true`.
 * Required fields (Mdl_Payments::validation_rules): invoice_id, payment_date,
 * payment_amount.
 */

import { test, expect } from '../test.js';
import { createInvoiceWithBalance } from '../support/fixtures.js';
import { dbQuery } from '../support/db.js';

const XHR = { 'X-Requested-With': 'XMLHttpRequest' };

function payload(invoiceId) {
  return {
    invoice_id: String(invoiceId),
    payment_date: new Date().toISOString().slice(0, 10),
    payment_amount: '25.00',
  };
}

const add = async (page, form) =>
  (await page.request.post('/payments/ajax/add', { headers: XHR, form })).json();

test.describe('Payments AJAX — add', () => {
  test('it adds a payment with all required fields', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoiceWithBalance(page, '100.00');

    /* Act */
    const json = await add(page, payload(invoice.id));

    /* Assert */
    expect(json.success).toBe(1);
    expect(dbQuery(`SELECT payment_amount FROM ip_payments WHERE invoice_id = ${invoice.id}`))
      .toEqual([{ payment_amount: '25.00' }]);
  });

  for (const field of ['invoice_id', 'payment_date', 'payment_amount']) {
    test(`it fails to add a payment without ${field}`, async ({ page }) => {
      /* Arrange */
      const invoice = await createInvoiceWithBalance(page, '100.00');
      const form = payload(invoice.id);
      form[field] = '';

      /* Act */
      const json = await add(page, form);

      /* Assert */
      expect(json.success).toBe(0);
      expect(dbQuery('SELECT payment_id FROM ip_payments')).toEqual([]);
    });
  }

  test('it fails to add a payment exceeding the invoice balance', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoiceWithBalance(page, '10.00');

    /* Act */
    const json = await add(page, { ...payload(invoice.id), payment_amount: '999.00' });

    /* Assert */
    expect(json.success).toBe(0);
    expect(dbQuery('SELECT payment_id FROM ip_payments')).toEqual([]);
  });

  test('it renders the add payment modal', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoiceWithBalance(page, '100.00');

    /* Act */
    const response = await page.request.post('/payments/ajax/modal_add_payment', {
      headers: XHR,
      form: { invoice_id: String(invoice.id), invoice_balance: '100.00' },
    });

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });

  test('it requires an ajax request', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoiceWithBalance(page, '100.00');

    /* Act */
    const response = await page.request.post('/payments/ajax/add', { form: payload(invoice.id) });

    /* Assert */
    expect((await response.text()).trim()).toBe('');
  });
});
