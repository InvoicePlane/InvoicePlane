/**
 * Browser coverage for application/modules/quotes/controllers/Quotes.php.
 * Mirrors tests/Feature/Quotes/QuotesControllerTest.php.
 */

import { test, expect } from '../test.js';
import { createClient, createQuote, createTaxRate, uniq } from '../support/fixtures.js';
import { dbInsert, dbQuery } from '../support/db.js';
import { postForm, readCsrfToken } from '../support/http.js';
import { csrfOnPage } from '../support/csrf.js';

const today = () => new Date().toISOString().slice(0, 10);
const inAMonth = () => new Date(Date.now() + 31 * 864e5).toISOString().slice(0, 10);

/**
 * A raw quote row (no browser form) for the csrf-on arrange step —
 * createQuote() posts to the AJAX create endpoint without a csrf token,
 * which the CSRF-on server would itself reject.
 */
function seedQuoteForCsrf() {
  const clientId = dbInsert('ip_clients', { client_name: uniq('CsrfClient') });

  return dbInsert('ip_quotes', {
    user_id: 1,
    client_id: clientId,
    invoice_group_id: 1,
    quote_date_created: today(),
    quote_date_modified: `${today()} 00:00:00`,
    quote_date_expires: inAMonth(),
    quote_url_key: uniq('key'),
    quote_number: `CSRF-${Date.now()}`,
  });
}

test.describe('Quotes — list', () => {
  test('it lists every quote', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page, { client_name: uniq('QuoteListClient') });
    const a = await createQuote(page, { client_id: client.id });
    const b = await createQuote(page, { client_id: client.id });

    /* Act */
    await page.goto('/quotes/status/all');

    /* Assert: one delete form per quote row — an unambiguous per-row marker */
    await expect(page.locator(`form[action*="quotes/delete/${a.id}"]`)).toHaveCount(1);
    await expect(page.locator(`form[action*="quotes/delete/${b.id}"]`)).toHaveCount(1);
    expect(a.number).not.toBe('');
    await expect(page.locator(`tr:has(form[action*="quotes/delete/${a.id}"])`)).toContainText(a.number);
  });
});

test.describe('Quotes — view', () => {
  test('it shows a single quote', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page, { client_name: uniq('QuoteViewClient') });
    const quote = await createQuote(page, { client_id: client.id });

    /* Act */
    await page.goto(`/quotes/view/${quote.id}`);

    /* Assert */
    await expect(page.locator('#quote_number')).toHaveValue(quote.number);
    await expect(page.locator('#content')).toContainText(client.name);
  });
});

test.describe('Quotes — delete', () => {
  test('it deletes a quote', async ({ page }) => {
    /* Arrange */
    const client = await createClient(page);
    const doomed = await createQuote(page, { client_id: client.id });
    const kept = await createQuote(page, { client_id: client.id });

    /* Act */
    await page.goto('/quotes/status/all');
    const row = page.locator('tr', { has: page.locator(`form[action*="quotes/delete/${doomed.id}"]`) });
    await row.locator('.dropdown-toggle').click();
    page.once('dialog', (dialog) => dialog.accept());
    await Promise.all([page.waitForLoadState('load'), row.locator('button.dropdown-button').click()]);

    /* Assert */
    expect(dbQuery(`SELECT quote_id FROM ip_quotes WHERE quote_id = ${doomed.id}`)).toEqual([]);
    expect(dbQuery(`SELECT quote_id FROM ip_quotes WHERE quote_id = ${kept.id}`)).toHaveLength(1);
  });

  test('it still deletes a quote when csrf protection is on and the token is valid', async () => {
    /* Arrange */
    const page = await csrfOnPage();
    const doomedId = seedQuoteForCsrf();
    const token = await readCsrfToken(page, '/quotes/status/all');

    /* Act */
    const response = await postForm(page, `/quotes/delete/${doomedId}`, { _ip_csrf: token });

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT quote_id FROM ip_quotes WHERE quote_id = ${doomedId}`)).toEqual([]);
  });

  test('it does not delete a quote when the csrf token is missing', async () => {
    /* Arrange */
    const page = await csrfOnPage();
    const keptId = seedQuoteForCsrf();

    /* Act */
    const response = await postForm(page, `/quotes/delete/${keptId}`, {});

    /* Assert */
    expect(response.status()).toBe(403);
    expect(dbQuery(`SELECT quote_id FROM ip_quotes WHERE quote_id = ${keptId}`)).toHaveLength(1);
  });
});

test.describe('Quotes — tax rates', () => {
  test('it removes a tax rate from a quote', async ({ page }) => {
    /* Arrange */
    const quote = await createQuote(page);
    const rate = await createTaxRate(page);
    const removeId = dbInsert('ip_quote_tax_rates', {
      quote_id: quote.id, tax_rate_id: rate.id, include_item_tax: 0, quote_tax_rate_amount: '5.00',
    });
    const keepId = dbInsert('ip_quote_tax_rates', {
      quote_id: quote.id, tax_rate_id: rate.id, include_item_tax: 0, quote_tax_rate_amount: '7.00',
    });

    /* Act */
    const response = await postForm(page, `/quotes/delete_quote_tax/${quote.id}/${removeId}`, {});

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT quote_tax_rate_id FROM ip_quote_tax_rates WHERE quote_tax_rate_id = ${removeId}`)).toEqual([]);
    expect(dbQuery(`SELECT quote_tax_rate_id FROM ip_quote_tax_rates WHERE quote_tax_rate_id = ${keepId}`)).toHaveLength(1);
  });
});

test.describe('Quotes — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest to login and leaks no quote', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.goto('/quotes/status/all');

    /* Assert */
    await expect(page).toHaveURL(/\/sessions\/login/);
    expect(await response.text()).not.toContain('QUO-SECRET');
  });
});
