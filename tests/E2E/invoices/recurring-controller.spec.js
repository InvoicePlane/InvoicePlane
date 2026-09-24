/**
 * Browser coverage for the recurring-invoice routes on
 * application/modules/invoices/controllers/Recurring.php.
 * Mirrors tests/Feature/Invoices/RecurringControllerTest.php.
 */

import { test, expect } from '../test.js';
import { createInvoice } from '../support/fixtures.js';
import { dbInsert, dbQuery } from '../support/db.js';
import { postForm } from '../support/http.js';

const today = () => new Date().toISOString().slice(0, 10);
const inAMonth = () => new Date(Date.now() + 31 * 864e5).toISOString().slice(0, 10);

function seedRecurring(invoiceId, overrides = {}) {
  return dbInsert('ip_invoices_recurring', {
    invoice_id: invoiceId,
    recur_start_date: today(),
    recur_next_date: inAMonth(),
    recur_frequency: '1',
    ...overrides,
  });
}

test.describe('Recurring — list', () => {
  test('it lists every recurring schedule', async ({ page }) => {
    /* Arrange */
    const a = await createInvoice(page);
    const b = await createInvoice(page, { client_id: a.clientId });
    seedRecurring(a.id);
    seedRecurring(b.id);

    /* Act */
    await page.goto('/invoices/recurring');

    /* Assert */
    await expect(page.locator('#content')).toContainText(a.number);
    await expect(page.locator('#content')).toContainText(b.number);
  });
});

test.describe('Recurring — create', () => {
  test('it creates a recurring schedule from an invoice and shows it in the list', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);

    /* Act */
    const create = await page.request.post('/invoices/ajax/create_recurring', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      form: { invoice_id: String(invoice.id), recur_start_date: today(), recur_frequency: '1D' },
    });

    /* Assert */
    expect((await create.json()).success).toBe(1);
    expect(dbQuery(`SELECT invoice_recurring_id FROM ip_invoices_recurring WHERE invoice_id = ${invoice.id}`)).toHaveLength(1);
    await page.goto('/invoices/recurring');
    await expect(page.locator('#content')).toContainText(invoice.number);
  });
});

test.describe('Recurring — stop', () => {
  test('it stops a recurring schedule', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const id = seedRecurring(invoice.id);

    /* Act */
    await page.goto('/invoices/recurring');
    const row = page.locator('tr', { has: page.locator(`form[action*="recurring/stop/${id}"]`) });
    await row.locator('.dropdown-toggle').click();
    await Promise.all([page.waitForLoadState('load'), row.locator(`form[action*="recurring/stop/${id}"] button`).click()]);

    /* Assert */
    expect(dbQuery(`SELECT recur_end_date FROM ip_invoices_recurring WHERE invoice_recurring_id = ${id}`))
      .toEqual([{ recur_end_date: today() }]);
  });

  test('it does not stop a recurring schedule on a plain get request', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const next = inAMonth();
    const id = seedRecurring(invoice.id, { recur_next_date: next });

    /* Act */
    const response = await page.request.get(`/invoices/recurring/stop/${id}`, { maxRedirects: 0 });

    /* Assert: ensure_valid_post_request() bounces the GET (a 3xx), never acts on it */
    expect(response.status()).toBeGreaterThanOrEqual(300);
    expect(response.status()).toBeLessThan(400);
    expect(dbQuery(`SELECT recur_next_date FROM ip_invoices_recurring WHERE invoice_recurring_id = ${id}`))
      .toEqual([{ recur_next_date: next }]);
  });
});

test.describe('Recurring — delete', () => {
  test('it deletes a recurring schedule', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const doomed = seedRecurring(invoice.id);
    const kept = seedRecurring(invoice.id);

    /* Act */
    const response = await postForm(page, `/invoices/recurring/delete/${doomed}`, {});

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT invoice_recurring_id FROM ip_invoices_recurring WHERE invoice_recurring_id = ${doomed}`)).toEqual([]);
    expect(dbQuery(`SELECT invoice_recurring_id FROM ip_invoices_recurring WHERE invoice_recurring_id = ${kept}`)).toHaveLength(1);
  });

  test('it does not delete a recurring schedule on a plain get request', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const id = seedRecurring(invoice.id);

    /* Act */
    const response = await page.request.get(`/invoices/recurring/delete/${id}`, { maxRedirects: 0 });

    /* Assert: the global GET-to-delete gate in Base_Controller rejects this */
    expect(response.status()).toBe(404);
    expect(dbQuery(`SELECT invoice_recurring_id FROM ip_invoices_recurring WHERE invoice_recurring_id = ${id}`)).toHaveLength(1);
  });

  test('it still deletes a recurring schedule when csrf protection is on and the token is valid', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });

  test('it does not delete a recurring schedule when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Recurring — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no recurring schedule', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/invoices/recurring');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('INV-REC-SECRET');
  });
});
