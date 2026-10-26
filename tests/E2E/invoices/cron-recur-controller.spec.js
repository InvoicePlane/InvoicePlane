/**
 * Browser coverage for the recurring-invoice cron endpoint
 * (/invoices/cron/recur/{key}) on application/modules/invoices/controllers/Cron.php.
 * Mirrors tests/Feature/Invoices/CronRecurControllerTest.php. This route is
 * unauthenticated and gated purely by the `cron_key` setting.
 */

import { test, expect } from '../test.js';
import { createInvoice } from '../support/fixtures.js';
import { dbExec, dbInsert, dbQuery } from '../support/db.js';

const CRON_KEY = 'the-real-key';
const daysAgo = (n) => new Date(Date.now() - n * 864e5).toISOString().slice(0, 10);
const daysAhead = (n) => new Date(Date.now() + n * 864e5).toISOString().slice(0, 10);

test.beforeEach(() => {
  dbExec(
    `INSERT INTO ip_settings (setting_key, setting_value) VALUES ('cron_key', '${CRON_KEY}')`
    + ' ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
  );
});

async function seedRecurringInvoice(page, overrides = {}) {
  const invoice = await createInvoice(page);
  const recurringId = dbInsert('ip_invoices_recurring', {
    invoice_id: invoice.id,
    recur_start_date: daysAgo(1),
    recur_frequency: '1M',
    recur_next_date: daysAgo(1),
    ...overrides,
  });

  return { clientId: invoice.clientId, invoiceId: invoice.id, recurringId };
}

const invoiceCount = (clientId) =>
  Number(dbQuery(`SELECT COUNT(*) AS n FROM ip_invoices WHERE client_id = ${clientId}`)[0].n);

test.describe('Cron recur — key auth', () => {
  test('it returns 500 for a wrong cron key', async ({ page }) => {
    /* Arrange */
    const seeded = await seedRecurringInvoice(page);

    /* Act */
    const response = await page.request.get('/invoices/cron/recur/wrong-key');

    /* Assert */
    expect(response.status()).toBe(500);
    expect(invoiceCount(seeded.clientId)).toBe(1);
  });

  test('it returns 500 for a missing cron key', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get('/invoices/cron/recur');

    /* Assert */
    expect(response.status()).toBe(500);
  });
});

test.describe('Cron recur — generation', () => {
  test('it generates a due recurring invoice with the correct cron key', async ({ page }) => {
    /* Arrange */
    const seeded = await seedRecurringInvoice(page);

    /* Act */
    const response = await page.request.get(`/invoices/cron/recur/${CRON_KEY}`);

    /* Assert */
    expect(response.status()).toBeLessThan(400);
    expect(invoiceCount(seeded.clientId)).toBe(2);
    expect(dbQuery(`SELECT recur_next_date FROM ip_invoices_recurring WHERE invoice_recurring_id = ${seeded.recurringId}`)[0].recur_next_date)
      .not.toBe(daysAgo(1));
  });

  test('it does not generate an invoice for a not yet due recurring invoice', async ({ page }) => {
    /* Arrange */
    const seeded = await seedRecurringInvoice(page, { recur_next_date: daysAhead(10) });

    /* Act */
    await page.request.get(`/invoices/cron/recur/${CRON_KEY}`);

    /* Assert */
    expect(invoiceCount(seeded.clientId)).toBe(1);
  });

  test('it does not generate an invoice for an expired recurring series', async ({ page }) => {
    /* Arrange */
    const seeded = await seedRecurringInvoice(page, { recur_end_date: daysAgo(1) });

    /* Act */
    await page.request.get(`/invoices/cron/recur/${CRON_KEY}`);

    /* Assert */
    expect(invoiceCount(seeded.clientId)).toBe(1);
  });
});
