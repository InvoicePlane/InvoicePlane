/**
 * Qonto invoice transmission — the full authenticate → build → send →
 * record-the-response flow. Every test here asserts on what the provider's
 * HTTP endpoint returned, which needs a server-side stub Playwright cannot
 * intercept. Mirrors tests/Feature/Core/QontoInvoiceTransmissionTest.php (fakes the provider);
 * kept here as an explicit, named mirror.
 */

import { test } from '../test.js';

const NEEDS_STUB = 'needs a server-side provider stub — covered by QontoInvoiceTransmissionTest';

test('it imports then sends by einvoice and logs the client invoice id', () => {
  test.fixme(true, NEEDS_STUB);
});

test('it records a failure when the import returns no client invoice id', () => {
  test.fixme(true, NEEDS_STUB);
});

test('it records the import error without attempting send by einvoice', () => {
  test.fixme(true, NEEDS_STUB);
});

