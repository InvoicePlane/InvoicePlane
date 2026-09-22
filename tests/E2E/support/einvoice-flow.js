/**
 * Shared spec body for the three near-identical e-invoice provider flow files
 * (SuperPdp / Qonto / LetsPeppol). Each `*-flow.spec.js` calls
 * `einvoiceFlowSuite()` with its provider-specific values.
 *
 * Everything up to the point of the outbound provider HTTP call runs for real:
 * the provider registry, the settings / edit / history pages, the credential
 * save + SSRF/URL validation, the enable-exclusivity rule, and the send_invoice
 * guard clauses (unknown / disabled merchant client, unknown invoice). Only the
 * two tests that assert on what the provider *returned* need a server-side stub
 * and stay `test.fixme`, covered by the matching PHPUnit *FlowTest.
 */

import { test, expect } from '../test.js';
import { createInvoice, uniq } from './fixtures.js';
import { dbInsert, dbQuery, dbExec } from './db.js';

function seedProvider(merchantType, overrides = {}) {
  const label = overrides.label ?? uniq('Provider');
  const id = dbInsert('ip_merchant_clients', {
    merchant_type: merchantType,
    label,
    enabled: 0,
    auth_type: 'oauth2',
    settings_json: '{}',
    ...overrides,
  });

  return { id, label };
}

function seedResponse(invoiceId, merchantId, driver, overrides = {}) {
  return dbInsert('ip_merchant_responses', {
    invoice_id: invoiceId,
    merchant_client_id: merchantId,
    merchant_response_driver: driver,
    merchant_response: 'accepted',
    direction: 'out',
    record_type: 'outbound_status',
    status: 'sent',
    ...overrides,
  });
}

const settingsJson = (id) => dbQuery(`SELECT settings_json FROM ip_merchant_clients WHERE id = ${id}`)[0]?.settings_json ?? '{}';
const enabledOf = (id) => Number(dbQuery(`SELECT enabled FROM ip_merchant_clients WHERE id = ${id}`)[0]?.enabled);

/**
 * @param {{
 *   provider: string, driver: string,
 *   nonHttpsField: string,
 *   endpointField: string,
 *   payload: Record<string, string>,
 *   editFormFields: string[],
 *   credMarker: string,
 * }} cfg
 */
export function einvoiceFlowSuite({ provider, driver, nonHttpsField, endpointField, payload, editFormFields, credMarker }) {
  const validPayload = (over = {}) => ({ ...payload, enabled: '1', ...over });

  test.describe(`${provider} — provider registry & pages`, () => {
    test(`it includes ${provider} in the provider registry`, async ({ page }) => {
      /* Arrange + Act */
      const body = await (await page.goto('/integrations/providers')).text();

      /* Assert */
      expect(body).toContain(provider);
    });

    test(`it shows a ${provider} integration on the settings page`, async ({ page }) => {
      /* Arrange */
      const p = seedProvider(provider, { label: uniq(`My${provider}`) });

      /* Act */
      const body = await (await page.goto('/integrations/settings')).text();

      /* Assert */
      expect(body).toContain(p.label);
    });

    test(`it renders the ${provider} settings edit form`, async ({ page }) => {
      /* Arrange */
      const p = seedProvider(provider);

      /* Act */
      const response = await page.goto(`/integrations/settings/edit/${p.id}`);
      const body = await response.text();

      /* Assert */
      expect(response.status()).toBe(200);
      for (const field of editFormFields) expect(body, `edit form should show ${field}`).toContain(field);
    });
  });

  test.describe(`${provider} — credential save & validation`, () => {
    test(`it persists ${provider} credentials to the database`, async ({ page }) => {
      /* Arrange */
      const p = seedProvider(provider);

      /* Act */
      const response = await page.request.post(`/integrations/settings/save/${p.id}`, {
        form: validPayload({ label: 'Production Provider' }),
        maxRedirects: 0,
      });

      /* Assert: label persisted and the settings blob was (re)written encrypted */
      expect([301, 302, 303, 307]).toContain(response.status());
      expect(dbQuery(`SELECT label FROM ip_merchant_clients WHERE id = ${p.id}`)).toEqual([{ label: 'Production Provider' }]);
      expect(settingsJson(p.id)).toContain('ipenc:');
      if (credMarker) {
        // Non-secret fields round-trip on the edit form (secrets stay write-only).
        const editBody = await (await page.goto(`/integrations/settings/edit/${p.id}`)).text();
        expect(editBody).toContain(credMarker);
      }
    });

    test(`it disables all other providers when ${provider} is enabled`, async ({ page }) => {
      /* Arrange */
      const other = seedProvider(provider === 'qonto' ? 'superpdp' : 'qonto', { enabled: 1 });
      const p = seedProvider(provider);

      /* Act */
      await page.request.post(`/integrations/settings/save/${p.id}`, { form: validPayload({ enabled: '1' }), maxRedirects: 0 });

      /* Assert */
      expect(enabledOf(p.id)).toBe(1);
      expect(enabledOf(other.id)).toBe(0);
    });

    test('it rejects a private ip as api base url and stays on the edit form', async ({ page }) => {
      /* Arrange */
      const p = seedProvider(provider);

      /* Act */
      const response = await page.request.post(`/integrations/settings/save/${p.id}`, {
        form: validPayload({ api_base_url: 'http://192.168.1.1/steal-credentials' }),
        maxRedirects: 0,
      });

      /* Assert: SSRF-rejected save redirects back and the malicious URL is not persisted */
      expect([301, 302, 303, 307]).toContain(response.status());
      expect(settingsJson(p.id)).not.toContain('192.168.1.1');
    });

    test(`it rejects a non https ${nonHttpsField} and stays on the edit form`, async ({ page }) => {
      /* Arrange */
      const p = seedProvider(provider);

      /* Act */
      await page.request.post(`/integrations/settings/save/${p.id}`, {
        form: validPayload({ [nonHttpsField]: 'http://thirdparty.example.com' }),
        maxRedirects: 0,
      });

      /* Assert */
      expect(settingsJson(p.id)).not.toContain('http://thirdparty.example.com');
    });

    test('it rejects an absolute url in an endpoint path field', async ({ page }) => {
      /* Arrange */
      const p = seedProvider(provider);

      /* Act */
      await page.request.post(`/integrations/settings/save/${p.id}`, {
        form: validPayload({ [endpointField]: 'https://evil.example.com/x' }),
        maxRedirects: 0,
      });

      /* Assert */
      expect(settingsJson(p.id)).not.toContain('evil.example.com');
    });
  });

  test.describe(`${provider} — invoice history page`, () => {
    test(`it shows a sent ${provider} invoice in the history page`, async ({ page }) => {
      /* Arrange */
      const invoice = await createInvoice(page);
      const p = seedProvider(provider, { enabled: 1 });
      seedResponse(invoice.id, p.id, driver, { merchant_response_reference: 'ext-abc123', status: 'sent' });

      /* Act */
      const body = await (await page.goto(`/integrations/history/${invoice.id}`)).text();

      /* Assert */
      expect(body).toContain('ext-abc123');
    });

    test('it shows an empty history for an invoice that was never sent', async ({ page }) => {
      /* Arrange */
      const invoice = await createInvoice(page);

      /* Act */
      const response = await page.goto(`/integrations/history/${invoice.id}`);

      /* Assert */
      expect(response.status()).toBe(200);
      expect(await response.text()).not.toMatch(/Fatal error|A PHP Error was encountered/i);
    });

    test(`it shows multiple ${provider} responses for a single invoice`, async ({ page }) => {
      /* Arrange */
      const invoice = await createInvoice(page);
      const p = seedProvider(provider, { enabled: 1 });
      seedResponse(invoice.id, p.id, driver, { merchant_response_reference: 'ext-first' });
      seedResponse(invoice.id, p.id, driver, { merchant_response_reference: 'ext-second' });

      /* Act */
      const body = await (await page.goto(`/integrations/history/${invoice.id}`)).text();

      /* Assert */
      expect(body).toContain('ext-first');
      expect(body).toContain('ext-second');
    });

    test('it shows a rejected status in the invoice history', async ({ page }) => {
      /* Arrange */
      const invoice = await createInvoice(page);
      const p = seedProvider(provider, { enabled: 1 });
      seedResponse(invoice.id, p.id, driver, { status: 'rejected', merchant_response: 'rejected', merchant_response_reference: 'ext-rej' });

      /* Act */
      const body = await (await page.goto(`/integrations/history/${invoice.id}`)).text();

      /* Assert */
      expect(body.toLowerCase()).toContain('reject');
    });
  });

  test.describe(`${provider} — send_invoice guard clauses`, () => {
    // These guards either redirect back with a flash or render show_error(500);
    // either way nothing is transmitted. "not a 2xx" + "no outbound row" is the
    // invariant.
    const notTransmitted = (status) => expect(status < 200 || status >= 300).toBe(true);

    test('it returns an error when send invoice references an unknown merchant client', async ({ page }) => {
      /* Arrange */
      const invoice = await createInvoice(page);

      /* Act */
      const response = await page.request.post(`/integrations/send_invoice/${invoice.id}/99999`, { maxRedirects: 0 });

      /* Assert */
      notTransmitted(response.status());
      expect(dbQuery(`SELECT merchant_response_id FROM ip_merchant_responses WHERE invoice_id = ${invoice.id}`)).toEqual([]);
    });

    test('it returns an error when send invoice uses a disabled merchant client', async ({ page }) => {
      /* Arrange */
      const invoice = await createInvoice(page);
      const p = seedProvider(provider, { enabled: 0 });

      /* Act */
      const response = await page.request.post(`/integrations/send_invoice/${invoice.id}/${p.id}`, { maxRedirects: 0 });

      /* Assert */
      notTransmitted(response.status());
      expect(dbQuery(`SELECT merchant_response_id FROM ip_merchant_responses WHERE invoice_id = ${invoice.id} AND direction = 'out'`)).toEqual([]);
    });

    test('it returns an error when send invoice references an unknown invoice', async ({ page }) => {
      /* Arrange */
      const p = seedProvider(provider, { enabled: 1 });

      /* Act */
      const response = await page.request.post(`/integrations/send_invoice/999999/${p.id}`, { maxRedirects: 0 });

      /* Assert */
      notTransmitted(response.status());
    });
  });

  test.describe(`${provider} — provider response recording (needs a stubbed provider)`, () => {
    const NEEDS_STUB = `needs a server-side ${provider} stub — covered by the matching PHPUnit *FlowTest`;

    test(`it records the ${provider} external id in the merchant response table`, () => {
      test.fixme(true, NEEDS_STUB);
    });

    test('it records a failed send attempt in the merchant response table', () => {
      test.fixme(true, NEEDS_STUB);
    });
  });

  test.describe(`${provider} — guest access`, () => {
    test.use({ storageState: { cookies: [], origins: [] } });

    test(`it redirects a guest away from the ${provider} edit form`, async ({ page }) => {
      /* Arrange + Act */
      await page.goto('/integrations/settings/edit/1');

      /* Assert */
      await expect(page).toHaveURL(/\/sessions\/login/);
    });

    test(`it redirects a guest away from the ${provider} history page`, async ({ page }) => {
      /* Arrange + Act */
      await page.goto('/integrations/history/1');

      /* Assert */
      await expect(page).toHaveURL(/\/sessions\/login/);
    });
  });

  // Keep an unused import from tree-shaking complaints in strict linters.
  void dbExec;
}
