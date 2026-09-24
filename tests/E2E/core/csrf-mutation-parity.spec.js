/**
 * tests/Feature/Core/CsrfMutationParityTest.php pins that a set of
 * state-changing endpoints behave identically under the PRODUCTION config
 * (CSRF on): the request succeeds with a valid token and is refused without
 * one. These have no CRUD ControllerTest of their own, unlike the CSRF pairs
 * scattered across the other spec files.
 *
 * Runs against the second, CSRF_PROTECTION=true server instance — see
 * tests/E2E/support/csrf.js for why a second process is required rather than
 * a per-request toggle.
 */

import { test, expect } from '../test.js';
import { createHash } from 'crypto';
import { E2E_CSRF_BASE_URL, LOGIN_PATH } from '../config.js';
import { dbExec, dbInsert, dbQuery } from '../support/db.js';
import { postForm, readCsrfToken } from '../support/http.js';
import { csrfOnPage } from '../support/csrf.js';
import { uniq } from '../support/fixtures.js';

const today = () => new Date().toISOString().slice(0, 10);
const inAMonth = () => new Date(Date.now() + 31 * 864e5).toISOString().slice(0, 10);

function seedInvoiceForCsrf() {
  const clientId = dbInsert('ip_clients', { client_name: uniq('CsrfClient') });
  const invoiceId = dbInsert('ip_invoices', {
    user_id: 1,
    client_id: clientId,
    invoice_group_id: 1,
    invoice_status_id: 1,
    invoice_date_created: today(),
    invoice_date_modified: `${today()} 00:00:00`,
    invoice_date_due: inAMonth(),
    invoice_time_created: '00:00:00',
    invoice_number: `CSRF-${Date.now()}`,
    invoice_terms: '',
    invoice_url_key: uniq('key'),
  });
  dbInsert('ip_invoice_amounts', { invoice_id: invoiceId, invoice_total: '0.00', invoice_balance: '0.00' });

  return invoiceId;
}

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

function seedSecondaryUser() {
  return dbInsert('ip_users', {
    user_type: 2,
    user_name: uniq('CsrfUser'),
    user_email: `${uniq('csrf').toLowerCase()}@test.local`,
    user_password: 'x',
    user_psalt: 'e2e',
    user_language: 'system',
    user_active: 1,
    user_date_created: `${today()} 00:00:00`,
    user_date_modified: `${today()} 00:00:00`,
  });
}

test.describe('CSRF parity — import/delete', () => {
  test('it deletes an import batch with a valid csrf token', async () => {
    /* Arrange */
    const page = await csrfOnPage();
    const id = dbInsert('ip_imports', { import_date: `${today()} 00:00:00` });
    const token = await readCsrfToken(page, '/import');

    /* Act */
    const response = await postForm(page, `/import/delete/${id}`, { _ip_csrf: token });

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT import_id FROM ip_imports WHERE import_id = ${id}`)).toEqual([]);
  });

  test('it does not delete an import batch without a csrf token', async () => {
    /* Arrange */
    const page = await csrfOnPage();
    const id = dbInsert('ip_imports', { import_date: `${today()} 00:00:00` });

    /* Act */
    const response = await postForm(page, `/import/delete/${id}`, {});

    /* Assert */
    expect(response.status()).toBe(403);
    expect(dbQuery(`SELECT import_id FROM ip_imports WHERE import_id = ${id}`)).toHaveLength(1);
  });
});

test.describe('CSRF parity — users/delete_user_client', () => {
  test('it unassigns a client from a user with a valid csrf token', async () => {
    /* Arrange */
    const page = await csrfOnPage();
    const userId = seedSecondaryUser();
    const clientId = dbInsert('ip_clients', { client_name: uniq('CsrfClient') });
    const ucId = dbInsert('ip_user_clients', { user_id: userId, client_id: clientId });
    const token = await readCsrfToken(page, `/user_clients/user/${userId}`);

    /* Act */
    const response = await postForm(page, `/users/delete_user_client/${userId}/${ucId}`, { _ip_csrf: token });

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT user_client_id FROM ip_user_clients WHERE user_client_id = ${ucId}`)).toEqual([]);
  });

  test('it does not unassign a client from a user without a csrf token', async () => {
    /* Arrange */
    const page = await csrfOnPage();
    const userId = seedSecondaryUser();
    const clientId = dbInsert('ip_clients', { client_name: uniq('CsrfClient') });
    const ucId = dbInsert('ip_user_clients', { user_id: userId, client_id: clientId });

    /* Act */
    const response = await postForm(page, `/users/delete_user_client/${userId}/${ucId}`, {});

    /* Assert */
    expect(response.status()).toBe(403);
    expect(dbQuery(`SELECT user_client_id FROM ip_user_clients WHERE user_client_id = ${ucId}`)).toHaveLength(1);
  });
});

test.describe('CSRF parity — payments/delete', () => {
  test('it deletes a payment with a valid csrf token', async () => {
    /* Arrange */
    const page = await csrfOnPage();
    const invoiceId = seedInvoiceForCsrf();
    const id = dbInsert('ip_payments', {
      invoice_id: invoiceId, payment_date: today(), payment_amount: '10.00', payment_note: 'parity-delete',
    });
    const token = await readCsrfToken(page, `/invoices/view/${invoiceId}`);

    /* Act */
    const response = await postForm(page, `/payments/delete/${id}`, { _ip_csrf: token });

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    expect(dbQuery(`SELECT payment_id FROM ip_payments WHERE payment_id = ${id}`)).toEqual([]);
  });

  test('it does not delete a payment without a csrf token', async () => {
    /* Arrange */
    const page = await csrfOnPage();
    const invoiceId = seedInvoiceForCsrf();
    const id = dbInsert('ip_payments', {
      invoice_id: invoiceId, payment_date: today(), payment_amount: '10.00', payment_note: 'parity-keep',
    });

    /* Act */
    const response = await postForm(page, `/payments/delete/${id}`, {});

    /* Assert */
    expect(response.status()).toBe(403);
    expect(dbQuery(`SELECT payment_id FROM ip_payments WHERE payment_id = ${id}`)).toHaveLength(1);
  });
});

test.describe('CSRF parity — invoices/recalculate_all_invoices', () => {
  test('it recalculates invoice amounts with a valid csrf token', async () => {
    /* Arrange */
    const page = await csrfOnPage();
    const invoiceId = seedInvoiceForCsrf();
    dbExec(`UPDATE ip_invoice_amounts SET invoice_total = '999.99', invoice_balance = '999.99' WHERE invoice_id = ${invoiceId}`);
    const token = await readCsrfToken(page, `/invoices/view/${invoiceId}`);

    /* Act */
    const response = await postForm(page, '/invoices/recalculate_all_invoices', { _ip_csrf: token });

    /* Assert */
    expect(response.status()).not.toBe(403);
    expect(dbQuery(`SELECT invoice_total FROM ip_invoice_amounts WHERE invoice_id = ${invoiceId}`))
      .toEqual([{ invoice_total: '0.00' }]);
  });

  test('it does not recalculate invoice amounts without a csrf token', async () => {
    /* Arrange */
    const page = await csrfOnPage();
    const invoiceId = seedInvoiceForCsrf();
    dbExec(`UPDATE ip_invoice_amounts SET invoice_total = '888.88' WHERE invoice_id = ${invoiceId}`);

    /* Act */
    const response = await postForm(page, '/invoices/recalculate_all_invoices', {});

    /* Assert */
    expect(response.status()).toBe(403);
    expect(dbQuery(`SELECT invoice_total FROM ip_invoice_amounts WHERE invoice_id = ${invoiceId}`))
      .toEqual([{ invoice_total: '888.88' }]);
  });
});

test.describe('CSRF parity — quotes/recalculate_all_quotes', () => {
  test('it recalculates quote amounts with a valid csrf token', async () => {
    /* Arrange */
    const page = await csrfOnPage();
    const quoteId = seedQuoteForCsrf();
    dbInsert('ip_quote_amounts', { quote_id: quoteId, quote_total: '777.77' });
    const token = await readCsrfToken(page, `/quotes/view/${quoteId}`);

    /* Act */
    const response = await postForm(page, '/quotes/recalculate_all_quotes', { _ip_csrf: token });

    /* Assert */
    expect(response.status()).not.toBe(403);
    expect(dbQuery(`SELECT quote_total FROM ip_quote_amounts WHERE quote_id = ${quoteId}`))
      .toEqual([{ quote_total: '0.00' }]);
  });

  test('it does not recalculate quote amounts without a csrf token', async () => {
    /* Arrange */
    const page = await csrfOnPage();
    const quoteId = seedQuoteForCsrf();
    dbInsert('ip_quote_amounts', { quote_id: quoteId, quote_total: '666.66' });

    /* Act */
    const response = await postForm(page, '/quotes/recalculate_all_quotes', {});

    /* Assert */
    expect(response.status()).toBe(403);
    expect(dbQuery(`SELECT quote_total FROM ip_quote_amounts WHERE quote_id = ${quoteId}`))
      .toEqual([{ quote_total: '666.66' }]);
  });
});

test.describe('CSRF parity — sessions/passwordreset', () => {
  /**
   * Guest flow (unauthenticated) — its own context on the CSRF-on server,
   * not the shared admin page csrfOnPage() returns.
   */
  async function guestCsrfPage(browser) {
    const context = await browser.newContext({ baseURL: E2E_CSRF_BASE_URL });

    return context.newPage();
  }

  function seedResetToken(rawToken) {
    const id = seedSecondaryUser();
    dbExec(
      `UPDATE ip_users SET user_passwordreset_token = '${rawToken}',`
      + ` user_passwordreset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE user_id = ${id}`,
    );

    return id;
  }

  test('it changes a password via reset with a valid csrf token', async ({ browser }) => {
    /* Arrange: Sessions::passwordreset() compares against
       hash_password_reset_token($token) (sha256) — store the hash, submit
       the raw token below, same as tests/E2E/core/password-reset-token-expiry.spec.js */
    const rawToken = `parity-reset-${uniq('token')}`;
    const id = seedResetToken(createHash('sha256').update(rawToken).digest('hex'));
    const before = dbQuery(`SELECT user_password FROM ip_users WHERE user_id = ${id}`)[0].user_password;
    const page = await guestCsrfPage(browser);
    const token = await readCsrfToken(page, LOGIN_PATH);

    /* Act */
    const response = await postForm(page, '/sessions/passwordreset', {
      btn_new_password: '1',
      user_id: String(id),
      token: rawToken,
      new_password: 'BrandNewPass123',
      new_passwordv: 'BrandNewPass123',
      _ip_csrf: token,
    });

    /* Assert */
    expect([301, 302, 303]).toContain(response.status());
    const after = dbQuery(`SELECT user_password FROM ip_users WHERE user_id = ${id}`)[0].user_password;
    expect(after).not.toBe(before);
  });

  test('it does not change a password via reset without a csrf token', async ({ browser }) => {
    /* Arrange */
    const rawToken = `parity-reset-${uniq('token')}`;
    const id = seedResetToken(createHash('sha256').update(rawToken).digest('hex'));
    const before = dbQuery(`SELECT user_password FROM ip_users WHERE user_id = ${id}`)[0].user_password;
    const page = await guestCsrfPage(browser);

    /* Act */
    const response = await postForm(page, '/sessions/passwordreset', {
      btn_new_password: '1',
      user_id: String(id),
      token: rawToken,
      new_password: 'BrandNewPass123',
      new_passwordv: 'BrandNewPass123',
    });

    /* Assert */
    expect(response.status()).toBe(403);
    const after = dbQuery(`SELECT user_password FROM ip_users WHERE user_id = ${id}`)[0].user_password;
    expect(after).toBe(before);
  });
});
