/**
 * Browser coverage for application/modules/invoices/controllers/Invoices.php.
 * Mirrors tests/Feature/Invoices/InvoicesControllerTest.php.
 *
 * Delete rule: only draft invoices (status 1) are deletable unless the server
 * runs with ENABLE_INVOICE_DELETION=true. This E2E server uses the default
 * (off), so the "…when global deletion is enabled" case stays skipped, same as
 * the CSRF pair.
 */

import { test, expect } from '../test.js';
import { createInvoice } from '../support/fixtures.js';
import { dbExec, dbInsert, dbQuery } from '../support/db.js';
import { postForm } from '../support/http.js';

function setStatus(invoiceId, statusId) {
  dbExec(`UPDATE ip_invoices SET invoice_status_id = ${statusId} WHERE invoice_id = ${invoiceId}`);
}

async function deleteViaRow(page, invoiceId) {
  await page.goto('/invoices/status/all');
  const row = page.locator('tr', { has: page.locator(`form[action*="invoices/delete/${invoiceId}"]`) });
  await row.locator('.dropdown-toggle').click();
  page.once('dialog', (dialog) => dialog.accept());
  await Promise.all([page.waitForLoadState('load'), row.locator('button.dropdown-button').click()]);
}

test.describe('Invoices — list', () => {
  test('it lists every invoice', async ({ page }) => {
    /* Arrange */
    const a = await createInvoice(page);
    const b = await createInvoice(page, { client_id: a.clientId });

    /* Act */
    await page.goto('/invoices/status/all');

    /* Assert */
    await expect(page.locator(`form[action*="invoices/delete/${a.id}"]`)).toHaveCount(1);
    await expect(page.locator(`form[action*="invoices/delete/${b.id}"]`)).toHaveCount(1);
    expect(a.number).not.toBe('');
  });
});

test.describe('Invoices — view', () => {
  test('it shows a single invoice', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const [client] = dbQuery(`SELECT client_name FROM ip_clients WHERE client_id = ${invoice.clientId}`);

    /* Act */
    await page.goto(`/invoices/view/${invoice.id}`);

    /* Assert */
    await expect(page.locator('#invoice_number')).toHaveValue(invoice.number);
    await expect(page.locator('#content')).toContainText(client.client_name);
  });
});

test.describe('Invoices — delete', () => {
  test('it deletes a draft invoice', async ({ page }) => {
    /* Arrange */
    const doomed = await createInvoice(page);
    const kept = await createInvoice(page, { client_id: doomed.clientId });

    /* Act */
    await deleteViaRow(page, doomed.id);

    /* Assert */
    expect(dbQuery(`SELECT invoice_id FROM ip_invoices WHERE invoice_id = ${doomed.id}`)).toEqual([]);
    expect(dbQuery(`SELECT invoice_id FROM ip_invoices WHERE invoice_id = ${kept.id}`)).toHaveLength(1);
  });

  test('it refuses to delete a sent invoice while deletion is disabled', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    setStatus(invoice.id, 2);

    /* Act */
    const response = await postForm(page, `/invoices/delete/${invoice.id}`, {});

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT invoice_status_id FROM ip_invoices WHERE invoice_id = ${invoice.id}`))
      .toEqual([{ invoice_status_id: 2 }]);
  });

  test('it refuses to delete a paid invoice while deletion is disabled', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    setStatus(invoice.id, 4);

    /* Act */
    const response = await postForm(page, `/invoices/delete/${invoice.id}`, {});

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT invoice_id FROM ip_invoices WHERE invoice_id = ${invoice.id}`)).toHaveLength(1);
  });

  test('it deletes a sent invoice when global invoice deletion is enabled', async () => {
    test.skip(true, 'needs a server with ENABLE_INVOICE_DELETION=true — see tests/E2E/README.md');
  });

  test('it still deletes a draft invoice when csrf protection is on and the token is valid', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });

  test('it does not delete an invoice when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Invoices — tax rates', () => {
  test('it removes a tax rate from an invoice', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const removeId = dbInsert('ip_invoice_tax_rates', {
      invoice_id: invoice.id, tax_rate_id: 1, include_item_tax: 0, invoice_tax_rate_amount: '0.00',
    });
    const keepId = dbInsert('ip_invoice_tax_rates', {
      invoice_id: invoice.id, tax_rate_id: 1, include_item_tax: 0, invoice_tax_rate_amount: '0.00',
    });

    /* Act */
    const response = await postForm(page, `/invoices/delete_invoice_tax/${invoice.id}/${removeId}`, {});

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT invoice_tax_rate_id FROM ip_invoice_tax_rates WHERE invoice_tax_rate_id = ${removeId}`)).toEqual([]);
    expect(dbQuery(`SELECT invoice_tax_rate_id FROM ip_invoice_tax_rates WHERE invoice_tax_rate_id = ${keepId}`)).toHaveLength(1);
  });

  test('it does not remove an invoice tax rate when the csrf token is missing', async () => {
    test.skip(true, 'needs a CSRF_PROTECTION=true server — see tests/E2E/README.md');
  });
});

test.describe('Invoices — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no invoice', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/invoices/status/all');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('INV-SECRET');
  });
});
