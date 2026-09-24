/**
 * Browser coverage for application/modules/integrations/controllers/Integrations.php
 * (the e-invoice provider integrations UI — new in 1.8.0).
 * Mirrors tests/Feature/Core/IntegrationsControllerTest.php.
 */

import { test, expect } from '../test.js';
import { createInvoice, uniq } from '../support/fixtures.js';
import { dbInsert } from '../support/db.js';

/** A configured e-invoice provider (ip_merchant_clients row). */
function seedProvider(overrides = {}) {
  const label = overrides.label ?? uniq('Provider');
  const id = dbInsert('ip_merchant_clients', {
    merchant_type: 'superpdp',
    label,
    enabled: 0,
    auth_type: 'oauth2',
    settings_json: '{}',
    ...overrides,
  });

  return { id, label };
}

test.describe('Integrations — pages', () => {
  test('it shows a configured provider on the settings page', async ({ page }) => {
    /* Arrange */
    const provider = seedProvider({ label: uniq('MySuperPDP'), enabled: 0 });

    /* Act */
    const response = await page.goto('/integrations/settings');

    /* Assert */
    expect(response.status()).toBe(200);
    expect(await response.text()).toContain(provider.label);
  });

  test('it lists the known providers as json', async ({ page }) => {
    /* Arrange + Act */
    const body = await (await page.goto('/integrations/providers')).text();

    /* Assert */
    expect(body).toContain('superpdp');
    expect(body).toContain('qonto');
    expect(body).toContain('letspeppol');
  });

  test('it shows an enabled provider on the events page', async ({ page }) => {
    /* Arrange */
    const provider = seedProvider({ label: uniq('MyEventsProvider'), enabled: 1 });

    /* Act */
    const body = await (await page.goto('/integrations/events')).text();

    /* Assert */
    expect(body).toContain(provider.label);
  });

  test('it shows an enabled provider on the incoming page', async ({ page }) => {
    /* Arrange */
    const provider = seedProvider({ label: uniq('MyIncomingProvider'), enabled: 1 });

    /* Act */
    const body = await (await page.goto('/integrations/incoming')).text();

    /* Assert */
    expect(body).toContain(provider.label);
  });

  test('it shows the outbound transmission history for an invoice', async ({ page }) => {
    /* Arrange */
    const invoice = await createInvoice(page);
    const provider = seedProvider({ label: uniq('HistoryProvider'), enabled: 1 });
    dbInsert('ip_merchant_responses', {
      merchant_client_id: provider.id,
      invoice_id: invoice.id,
      merchant_response_driver: 'superpdp',
      merchant_response: 'accepted',
      merchant_response_reference: 'REF-HISTORY-001',
      direction: 'out',
      record_type: 'outbound_status',
      status: 'accepted',
    });

    /* Act */
    const body = await (await page.goto(`/integrations/history/${invoice.id}`)).text();

    /* Assert */
    expect(body).toContain('REF-HISTORY-001');
  });
});

test.describe('Integrations — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  for (const route of ['/integrations/settings', '/integrations/providers', '/integrations/events', '/integrations/incoming']) {
    test(`it redirects a guest away from ${route}`, async ({ page }) => {
      /* Arrange + Act */
      await page.goto(route);

      /* Assert */
      await expect(page).toHaveURL(/\/sessions\/login/);
    });
  }
});
