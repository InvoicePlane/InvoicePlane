/**
 * Browser coverage for the mailer send-invoice / send-quote AJAX routes on
 * application/modules/mailer/. Mirrors tests/Feature/Core/MailerAjaxControllerTest.php
 * — cancelling always redirects, and nothing is sent or marked "sent" while the
 * mailer is unconfigured (the E2E server has no SMTP).
 */

import { test, expect } from '../test.js';
import { createInvoice, createQuote } from '../support/fixtures.js';
import { dbExec, dbQuery } from '../support/db.js';
import { postForm } from '../support/http.js';

test.describe('Mailer AJAX — not-configured view', () => {
  test('it shows the not configured view for invoice', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);

    /* Act */
    const response = await page.goto(`/mailer/invoice/${invoice.id}`);

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });

  test('it shows the not configured view for quote', async ({ page }) => {
    /* Arrange */
    const quote = await createQuote(page);

    /* Act */
    const response = await page.goto(`/mailer/quote/${quote.id}`);

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
  });
});

test.describe('Mailer AJAX — send guards', () => {
  test('it redirects on cancel for send invoice even when unconfigured', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);

    /* Act */
    const response = await postForm(page, `/mailer/send_invoice/${invoice.id}`, { btn_cancel: '1' });

    /* Assert */
    expect([301, 302, 303, 307]).toContain(response.status());
  });

  test('it does not send or mark an invoice sent when mailer is not configured', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    dbExec(`UPDATE ip_invoices SET invoice_status_id = 1 WHERE invoice_id = ${invoice.id}`);

    /* Act */
    await postForm(page, `/mailer/send_invoice/${invoice.id}`, { btn_submit: '1' });

    /* Assert: still a draft */
    expect(dbQuery(`SELECT invoice_status_id FROM ip_invoices WHERE invoice_id = ${invoice.id}`))
      .toEqual([{ invoice_status_id: 1 }]);
  });

  test('it redirects on cancel for send quote even when unconfigured', async ({ page }) => {
    /* Arrange */
    const quote = await createQuote(page);

    /* Act */
    const response = await postForm(page, `/mailer/send_quote/${quote.id}`, { btn_cancel: '1' });

    /* Assert */
    expect([301, 302, 303, 307]).toContain(response.status());
  });

  test('it does not send or mark a quote sent when mailer is not configured', async ({ page }) => {
    /* Arrange */
    const quote = await createQuote(page);
    dbExec(`UPDATE ip_quotes SET quote_status_id = 1 WHERE quote_id = ${quote.id}`);

    /* Act */
    await postForm(page, `/mailer/send_quote/${quote.id}`, { btn_submit: '1' });

    /* Assert */
    expect(dbQuery(`SELECT quote_status_id FROM ip_quotes WHERE quote_id = ${quote.id}`))
      .toEqual([{ quote_status_id: 1 }]);
  });
});
