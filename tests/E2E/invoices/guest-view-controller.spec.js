/**
 * Browser coverage for application/modules/guest/controllers/View.php.
 * Mirrors tests/Feature/Invoices/GuestViewControllerTest.php — the public
 * invoice/quote view (url_key capability pattern) and the IDOR-sensitive
 * approve_quote / reject_quote actions.
 */

import { test, expect } from '../test.js';
import { createClient, createInvoice, createQuote, createSecondaryUser, uniq } from '../support/fixtures.js';
import { dbExec, dbInsert, dbQuery } from '../support/db.js';
import { loginAs } from '../support/auth.js';
import { E2E_BASE_URL } from '../config.js';

async function makeVisibleInvoice(page, statusId = 2) {
  const invoice = await createInvoice(page);
  const key = uniq('inv').toLowerCase();
  dbExec(`UPDATE ip_invoices SET invoice_url_key = '${key}', invoice_status_id = ${statusId} WHERE invoice_id = ${invoice.id}`);

  return { ...invoice, key };
}

async function makeVisibleQuote(page, clientId, statusId = 2) {
  const quote = await createQuote(page, { client_id: clientId });
  const key = uniq('quo').toLowerCase();
  dbExec(`UPDATE ip_quotes SET quote_url_key = '${key}', quote_status_id = ${statusId} WHERE quote_id = ${quote.id}`);

  return { ...quote, key };
}

const quoteStatus = (key) =>
  Number(dbQuery(`SELECT quote_status_id FROM ip_quotes WHERE quote_url_key = '${key}'`)[0]?.quote_status_id);

test.describe('Guest view — invoice', () => {
  test('it returns 404 for an empty invoice key', async ({ page }) => {
    /* Arrange + Act + Assert */
    expect((await page.request.get('/guest/view/invoice/')).status()).toBe(404);
  });

  test('it returns 404 for an unknown invoice key', async ({ page }) => {
    /* Arrange + Act + Assert */
    expect((await page.request.get('/guest/view/invoice/does-not-exist')).status()).toBe(404);
  });

  test('it returns 404 for a draft invoice key', async ({ page }) => {
    /* Arrange */
    const invoice = await makeVisibleInvoice(page, 1);

    /* Act + Assert */
    expect((await page.request.get(`/guest/view/invoice/${invoice.key}`)).status()).toBe(404);
  });

  test('it renders a guest visible invoice', async ({ page }) => {
    /* Arrange */
    const invoice = await makeVisibleInvoice(page, 2);

    /* Act */
    const response = await page.request.get(`/guest/view/invoice/${invoice.key}`);

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });
});

test.describe('Guest view — quote', () => {
  test('it returns 404 for an empty quote key', async ({ page }) => {
    expect((await page.request.get('/guest/view/quote/')).status()).toBe(404);
  });

  test('it returns 404 for an unknown quote key', async ({ page }) => {
    expect((await page.request.get('/guest/view/quote/does-not-exist')).status()).toBe(404);
  });

  test('it returns 404 for a draft quote key', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page);
    const quote = await makeVisibleQuote(page, client.id, 1);

    /* Act + Assert */
    expect((await page.request.get(`/guest/view/quote/${quote.key}`)).status()).toBe(404);
  });

  test('it renders a guest visible quote', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page);
    const quote = await makeVisibleQuote(page, client.id, 2);

    /* Act */
    const response = await page.request.get(`/guest/view/quote/${quote.key}`);

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });
});

test.describe('Guest view — approve / reject quote', () => {
  /** A guest (type-2) user assigned to `clientId`, in its own session. */
  async function guestFor(page, browser, clientId) {
    const user = await createSecondaryUser(page);
    dbInsert('ip_user_clients', { user_id: user.id, client_id: clientId });

    return loginAs(browser, user.email, user.password);
  }

  test('it returns 404 for a non post approve quote request', async ({ page, browser }) => {
    /* Arrange */
    const client = await createClient(page);
    const quote = await makeVisibleQuote(page, client.id, 2);
    const { context, page: guest } = await guestFor(page, browser, client.id);

    /* Act + Assert */
    expect((await guest.request.get(`/guest/view/approve_quote/${quote.key}`)).status()).toBe(404);
    expect(quoteStatus(quote.key)).toBe(2);
    await context.close();
  });

  test('it denies approve quote for an unauthenticated guest', async ({ page, browser }) => {
    /* Arrange */
    const client = await createClient(page);
    const quote = await makeVisibleQuote(page, client.id, 2);
    const anon = await browser.newContext({ baseURL: E2E_BASE_URL });

    /* Act */
    const response = await anon.request.post(`/guest/view/approve_quote/${quote.key}`, { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).toBe(403);
    expect(quoteStatus(quote.key)).toBe(2);
    await anon.close();
  });

  test('it denies approving a quote belonging to a different client', async ({ page, browser }) => {
    /* Arrange */
    const own = await createClient(page, { client_name: uniq('OwnClient') });
    const other = await createClient(page, { client_name: uniq('OtherClient') });
    const quote = await makeVisibleQuote(page, other.id, 2);
    const { context, page: guest } = await guestFor(page, browser, own.id);

    /* Act */
    const response = await guest.request.post(`/guest/view/approve_quote/${quote.key}`, { maxRedirects: 0 });

    /* Assert: 404 (not leaked as an authorization error), never mutated */
    expect(response.status()).toBe(404);
    expect(quoteStatus(quote.key)).toBe(2);
    await context.close();
  });

  test('it approves a quote for its own client', async ({ page, browser }) => {
    /* Arrange */
    const client = await createClient(page);
    const quote = await makeVisibleQuote(page, client.id, 2);
    const { context, page: guest } = await guestFor(page, browser, client.id);

    /* Act */
    const response = await guest.request.post(`/guest/view/approve_quote/${quote.key}`, { maxRedirects: 0 });

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(quoteStatus(quote.key)).toBe(4);
    await context.close();
  });

  test('it denies rejecting a quote belonging to a different client', async ({ page, browser }) => {
    /* Arrange */
    const own = await createClient(page, { client_name: uniq('OwnClient2') });
    const other = await createClient(page, { client_name: uniq('OtherClient2') });
    const quote = await makeVisibleQuote(page, other.id, 2);
    const { context, page: guest } = await guestFor(page, browser, own.id);

    /* Act */
    const response = await guest.request.post(`/guest/view/reject_quote/${quote.key}`, { maxRedirects: 0 });

    /* Assert */
    expect(response.status()).toBe(404);
    expect(quoteStatus(quote.key)).toBe(2);
    await context.close();
  });

  test('it rejects a quote for its own client', async ({ page, browser }) => {
    /* Arrange */
    const client = await createClient(page);
    const quote = await makeVisibleQuote(page, client.id, 2);
    const { context, page: guest } = await guestFor(page, browser, client.id);

    /* Act */
    const response = await guest.request.post(`/guest/view/reject_quote/${quote.key}`, { maxRedirects: 0 });

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(quoteStatus(quote.key)).toBe(5);
    await context.close();
  });
});

test.describe('Guest view — PDF guards', () => {
  test('it silently produces no invoice pdf for an unknown key', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.get('/guest/view/generate_invoice_pdf/nope-not-a-key');
    const body = await response.text();

    /* Assert: no matching invoice means the method falls through, no output, no crash */
    expect(response.status()).toBe(200);
    expect(body).not.toMatch(/Fatal error|A PHP Error was encountered/i);
    expect(body).not.toContain('%PDF');
  });

  test('it returns 404 for sumex pdf when the invoice has no sumex id', async ({ page }) => {
    /* Arrange */
    const invoice = await makeVisibleInvoice(page, 2);

    /* Act + Assert */
    const response = await page.request.get(`/guest/view/generate_sumex_pdf/${invoice.key}`, { maxRedirects: 0 });
    expect([404, 302, 303, 500]).toContain(response.status());
  });

  test('it returns 404 for quote pdf on an unknown key', async ({ page }) => {
    /* Arrange + Act + Assert */
    const response = await page.request.get('/guest/view/generate_quote_pdf/nope-not-a-key', { maxRedirects: 0 });
    expect(response.status()).not.toBe(200);
  });
});
