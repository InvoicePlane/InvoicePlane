/**
 * Browser coverage for the provider-agnostic guards on
 * POST /integrations/send_invoice/{invoiceId}/{merchantClientId}.
 * Mirrors tests/Feature/Core/SendInvoiceGuardTest.php — these checks run before
 * any call to a provider, so no gateway stub is needed.
 */

import { test, expect } from '../test.js';
import { createInvoice, uniq } from '../support/fixtures.js';
import { dbInsert, dbQuery } from '../support/db.js';

function seedProvider(merchantType) {
  return dbInsert('ip_merchant_clients', {
    merchant_type: merchantType,
    label: uniq('Provider'),
    enabled: 1,
    auth_type: 'oauth2',
    settings_json: '{}',
  });
}
const outResponses = (invoiceId, merchantId) =>
  dbQuery(
    `SELECT merchant_response_id FROM ip_merchant_responses WHERE invoice_id = ${invoiceId}`
    + ` AND merchant_client_id = ${merchantId} AND direction = 'out'`,
  );

test.describe('Send invoice — guards', () => {
  test('it rejects a send to a provider that does not support the invoice profile', async ({ page }) => {
    /* Arrange: Qonto does not support the Peppol UBL profile */
    const invoice = await createInvoice(page);
    const merchantId = seedProvider('qonto');

    /* Act */
    const response = await page.request.post(`/integrations/send_invoice/${invoice.id}/${merchantId}`, { maxRedirects: 0 });

    /* Assert: bounced, nothing transmitted */
    expect([301, 302, 303, 307]).toContain(response.status());
    expect(outResponses(invoice.id, merchantId)).toEqual([]);
  });

  test('it does not transmit on a plain get request', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const merchantId = seedProvider('letspeppol');

    /* Act */
    await page.request.get(`/integrations/send_invoice/${invoice.id}/${merchantId}`, { maxRedirects: 0 });

    /* Assert: the isPostRequest() gate returns without doing anything */
    expect(outResponses(invoice.id, merchantId)).toEqual([]);
  });
});

test.describe('Send invoice — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest away from the send endpoint', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post('/integrations/send_invoice/1/1', { maxRedirects: 0 });

    /* Assert */
    expect([301, 302, 303, 307]).toContain(response.status());
    expect(response.headers().location ?? '').toContain('sessions/login');
  });
});
