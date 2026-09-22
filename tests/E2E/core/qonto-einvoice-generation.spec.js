/**
 * Qonto Factur-X invoice transmission — the full authenticate → build → send →
 * record-the-response flow. Every test here asserts on what the provider's
 * HTTP endpoint returned, which needs a server-side stub Playwright cannot
 * intercept. Mirrors tests/Feature/Core/QontoEInvoiceGenerationTest.php (fakes the provider);
 * kept here as an explicit, named mirror.
 */

import { test } from '../test.js';

const NEEDS_STUB = 'needs a server-side provider stub — covered by QontoEInvoiceGenerationTest';

test('it generates a facturx hybrid pdf and transmits it to qonto', () => {
  test.fixme(true, NEEDS_STUB);
});

test('it does not transmit when the seller has no siren', () => {
  test.fixme(true, NEEDS_STUB);
});

test('it does not transmit when the invoice currency is missing', () => {
  test.fixme(true, NEEDS_STUB);
});

