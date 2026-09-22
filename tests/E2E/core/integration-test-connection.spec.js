/**
 * Browser coverage for /integrations/settings/test_connection/{id} on
 * application/modules/integrations/. Mirrors
 * tests/Feature/Core/IntegrationTestConnectionTest.php.
 *
 * The tests that assert on the *result* of the probe (reachable / unreachable)
 * need the outbound provider HTTP call stubbed — the PHPUnit suite fakes the
 * gateway; Playwright can't intercept a server-side call, so those stay
 * test.fixme. The method/JSON-shape/unknown-id/guard cases run for real.
 */

import { test, expect } from '../test.js';
import { uniq } from '../support/fixtures.js';
import { dbInsert } from '../support/db.js';

function seedProvider(overrides = {}) {
  return dbInsert('ip_merchant_clients', {
    merchant_type: 'superpdp',
    label: uniq('Provider'),
    enabled: 1,
    auth_type: 'oauth2',
    settings_json: '{}',
    ...overrides,
  });
}

const TEST = (id) => `/integrations/settings/test_connection/${id}`;

test.describe('Integration test connection — contract', () => {
  test('it answers with a json body carrying the three probe keys', async ({ page }) => {
    /* Arrange */
    const id = seedProvider();

    /* Act */
    const response = await page.request.post(TEST(id));
    const text = await response.text();

    /* Assert */
    expect(text).not.toContain('<html');
    const payload = JSON.parse(text);
    expect(payload).toHaveProperty('reachable');
    expect(payload).toHaveProperty('http_code');
    expect(payload).toHaveProperty('message');
  });

  test('it rejects a non post request', async ({ page }) => {
    /* Arrange */
    const id = seedProvider();

    /* Act + Assert */
    expect((await page.request.get(TEST(id))).status()).toBe(405);
  });

  test('it errors for an unknown merchant client', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post(TEST(99999));

    /* Assert */
    expect(response.status()).not.toBe(200);
  });
});

test.describe('Integration test connection — guest access', () => {
  test.use({ storageState: { cookies: [], origins: [] } });

  test('it redirects a guest away from the test connection endpoint', async ({ page }) => {
    /* Arrange + Act */
    const response = await page.request.post(TEST(1), { maxRedirects: 0 });

    /* Assert */
    expect([301, 302, 303, 307]).toContain(response.status());
    expect(response.headers().location ?? '').toContain('sessions/login');
  });
});

test.describe('Integration test connection — probe result (needs a stubbed provider)', () => {
  const NEEDS_STUB = 'needs a server-side provider stub — covered by IntegrationTestConnectionTest';

  test('it reports a reachable provider as a successful connection', () => {
    test.fixme(true, NEEDS_STUB);
  });

  test('it reports the provider as unreachable when authentication fails', () => {
    test.fixme(true, NEEDS_STUB);
  });

  test('it shows a test connection control on the provider edit form', async ({ page }) => {
    /* Arrange */
    const id = seedProvider();

    /* Act */
    const body = await (await page.goto(`/integrations/settings/edit/${id}`)).text();

    /* Assert */
    expect(body.toLowerCase()).toMatch(/test.connection/);
  });
});
