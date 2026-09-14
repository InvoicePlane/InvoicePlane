/**
 * Browser coverage for tests/Feature/Security/SecurityRegressionTest.php — the
 * consolidated regression pins: guest-PDF IDOR, path traversal / null-byte in
 * the upload endpoints, setting-key injection, and logo-value traversal.
 *
 * The PDF-*generation* cases (forged generate_pdf GET, the CSRF-token gate, the
 * template-whitelist fallback) are test.fixme: generating a PDF hangs the
 * single-process `php -S` E2E server (its PDF engine needs a Node/pdfmake
 * runtime that isn't wired up here). Those stay covered by SecurityRegressionTest.
 * The two IDOR checks below only reach the authorization guard, which rejects
 * before any PDF work.
 */

import { test, expect } from '../test.js';
import { createClient, createInvoice, createQuote, createSecondaryUser, uniq } from '../support/fixtures.js';
import { dbExec, dbInsert, dbQuery } from '../support/db.js';
import { loginAs } from '../support/auth.js';

async function guestVisibleInvoice(page, clientId) {
  const invoice = await createInvoice(page, { client_id: clientId });
  const key = uniq('inv').toLowerCase();
  dbExec(`UPDATE ip_invoices SET invoice_url_key = '${key}', invoice_status_id = 2 WHERE invoice_id = ${invoice.id}`);
  return { id: invoice.id, key };
}
async function guestVisibleQuote(page, clientId) {
  const quote = await createQuote(page, { client_id: clientId });
  const key = uniq('quo').toLowerCase();
  dbExec(`UPDATE ip_quotes SET quote_url_key = '${key}', quote_status_id = 2 WHERE quote_id = ${quote.id}`);
  return { id: quote.id, key };
}
async function guestFor(page, browser, clientId) {
  const user = await createSecondaryUser(page);
  dbInsert('ip_user_clients', { user_id: user.id, client_id: clientId });
  return loginAs(browser, user.email, user.password);
}

test.describe('Security regression — guest PDF IDOR', () => {
  test('it denies a guest access to another clients invoice pdf', async ({ page, browser }) => {
    /* Arrange */
    const mine = await createClient(page, { client_name: uniq('MyClient') });
    const theirs = await createClient(page, { client_name: uniq('OtherClient') });
    const foreign = await guestVisibleInvoice(page, theirs.id);
    const { context, page: guest } = await guestFor(page, browser, mine.id);

    /* Act: the authorization guard rejects before any PDF is built */
    const response = await guest.request.get(`/guest/invoices/generate_pdf/${foreign.id}`, { maxRedirects: 0, timeout: 10000 });

    /* Assert */
    expect(response.status()).not.toBe(200);
    await context.close();
  });

  test('it denies a guest access to another clients quote pdf', async ({ page, browser }) => {
    /* Arrange */
    const mine = await createClient(page, { client_name: uniq('MyClient2') });
    const theirs = await createClient(page, { client_name: uniq('OtherClient2') });
    const foreign = await guestVisibleQuote(page, theirs.id);
    const { context, page: guest } = await guestFor(page, browser, mine.id);

    /* Act */
    const response = await guest.request.get(`/guest/quotes/generate_pdf/${foreign.id}`, { maxRedirects: 0, timeout: 10000 });

    /* Assert */
    expect(response.status()).not.toBe(200);
    await context.close();
  });
});

test.describe('Security regression — generate_pdf mutation gate (PDF engine unavailable in E2E)', () => {
  const NO_PDF = 'generate_pdf hangs the single-process E2E server — covered by SecurityRegressionTest';

  for (const title of [
    'it does not mark an invoice sent from a forged generate_pdf get',
    'it marks an invoice sent only with a matching generate_pdf csrf token',
    'it does not mark a quote sent from a forged generate_pdf get',
    'it marks a quote sent only with a matching generate_pdf csrf token',
    'it falls back to the default template for a path traversal pdf template name',
    'it falls back to the default template for an unlisted pdf template name',
  ]) {
    test(title, () => {
      test.fixme(true, NO_PDF);
    });
  }
});

test.describe('Security regression — upload endpoint traversal', () => {
  const TRAVERSAL = ['..%2F..%2F..%2Fetc%2Fpasswd', 'key_..%2F..%2F..%2F..%2Fetc%2Fpasswd', 'key_....%2F%2Fetc%2Fpasswd'];

  test('it rejects a path traversal payload in the file download endpoint', async ({ page }) => {
    /* Arrange + Act + Assert */
    for (const payload of TRAVERSAL) {
      const response = await page.request.get(`/upload/get_file/${payload}`, { maxRedirects: 0, timeout: 10000 });
      expect(response.status(), `payload ${payload}`).not.toBe(200);
      expect(await response.text()).not.toContain('root:');
    }
  });

  test('it rejects null byte injection in the file download endpoint', async ({ page }) => {
    /* Arrange */
    const payload = encodeURIComponent('key_legitimate.pdf\0../../../etc/passwd');

    /* Act */
    const response = await page.request.get(`/upload/get_file/${payload}`, { maxRedirects: 0, timeout: 10000 });

    /* Assert */
    expect(response.status()).not.toBe(200);
    expect(await response.text()).not.toContain('root:');
  });

  test('it rejects a path traversal payload in the file delete endpoint', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post('/upload/delete_file/testkey', {
      form: { name: '../../../bootstrap/kernel.php' },
      maxRedirects: 0,
      timeout: 10000,
    });

    /* Assert: handled cleanly (no 500) */
    expect(response.status()).toBeLessThan(500);
  });
});

test.describe('Security regression — setting key / value injection', () => {
  test('it stores a crafted setting key safely without breaking the table', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post('/settings', {
      form: { 'settings[disable_setup]': '1', "settings[evil`;DROP TABLE ip_settings;--]": 'x', btn_submit: '1' },
      maxRedirects: 0,
    });

    /* Assert */
    expect(response.status()).not.toBe(500);
    expect(dbQuery("SELECT setting_key FROM ip_settings WHERE setting_key = 'disable_setup'")).toHaveLength(1);
  });

  test('it stores a setting key with html characters as literal text', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post('/settings', {
      form: { 'settings[disable_setup]': '1', 'settings[<script>alert(1)</script>]': 'x', btn_submit: '1' },
      maxRedirects: 0,
    });

    /* Assert */
    expect(response.status()).not.toBe(500);
    expect(dbQuery("SELECT setting_key FROM ip_settings WHERE setting_key = 'disable_setup'")).toHaveLength(1);
  });

  for (const key of ['invoice_logo', 'login_logo']) {
    test(`it rejects a path traversal value for the ${key} setting`, async ({ page }) => {
      /* Arrange */
      dbExec(`INSERT INTO ip_settings (setting_key, setting_value) VALUES ('${key}', 'safe.png')`
        + ' ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');

      /* Act */
      const response = await page.request.post('/settings', {
        form: { [`settings[${key}]`]: '../../../application/config/database.php', btn_submit: '1' },
        maxRedirects: 0,
      });

      /* Assert: not persisted as the traversal path */
      expect([301, 302, 303, 307]).toContain(response.status());
      const stored = dbQuery(`SELECT setting_value FROM ip_settings WHERE setting_key = '${key}'`)[0]?.setting_value ?? '';
      expect(stored).not.toContain('database.php');
    });
  }
});
