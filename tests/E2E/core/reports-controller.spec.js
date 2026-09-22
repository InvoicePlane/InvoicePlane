/**
 * Browser coverage for application/modules/reports/controllers/Reports.php.
 * Mirrors tests/Feature/Core/ReportsControllerTest.php — each report renders for
 * an authenticated admin without mutating data, and none is served to a guest.
 */

import { test, expect } from '../test.js';
import { createInvoiceWithBalance, createPayment } from '../support/fixtures.js';
import { dbQuery } from '../support/db.js';

const range = { from_date: '2000-01-01', to_date: '2099-12-31' };

test.describe('Reports — generation', () => {
  test('it generates an invoices per client report for a date range without mutating data', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoiceWithBalance(page, '100.00');
    const clientsBefore = dbQuery('SELECT COUNT(*) AS n FROM ip_clients')[0].n;
    const invoicesBefore = dbQuery('SELECT COUNT(*) AS n FROM ip_invoices')[0].n;

    /* Act */
    const response = await page.request.post('/reports/invoices_per_client', { form: range, maxRedirects: 0 });

    /* Assert */
    expect(response.status()).toBeLessThan(400);
    expect(dbQuery('SELECT COUNT(*) AS n FROM ip_clients')[0].n).toBe(clientsBefore);
    expect(dbQuery('SELECT COUNT(*) AS n FROM ip_invoices')[0].n).toBe(invoicesBefore);
    expect(invoice.id).toBeGreaterThan(0);
  });

  for (const [name, route] of [
    ['a sales by client report', '/reports/sales_by_client'],
    ['a payment history report', '/reports/payment_history'],
    ['an invoice aging report', '/reports/invoice_aging'],
    ['a sales by year report', '/reports/sales_by_year'],
  ]) {
    test(`it generates ${name}`, async ({ page }) => {
      /* Arrange */
      const invoice = await createInvoiceWithBalance(page, '100.00');
      await createPayment(page, invoice.id, { payment_amount: '10.00' });

      /* Act */
      const response = await page.request.post(route, { form: range, maxRedirects: 0 });

      /* Assert */
      expect(response.status()).toBeLessThan(400);
      expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
    });
  }
});

test.describe('Reports — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and serves no report', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/reports/sales_by_client');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('Sales by Client');
  });
});
