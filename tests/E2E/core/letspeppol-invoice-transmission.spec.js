/**
 * LetsPeppol invoice transmission — the full authenticate → build → send →
 * record-the-response flow. Every test here asserts on what the provider's
 * HTTP endpoint returned, which needs a server-side stub Playwright cannot
 * intercept. Mirrors tests/Feature/Core/LetsPeppolInvoiceTransmissionTest.php (fakes the provider);
 * kept here as an explicit, named mirror.
 */

import { test } from '../test.js';

const NEEDS_STUB = 'needs a server-side provider stub — covered by LetsPeppolInvoiceTransmissionTest';

test('it authenticates then transmits and logs the external reference', () => {
  test.fixme(true, NEEDS_STUB);
});

test('it records a failure when the provider rejects the document', () => {
  test.fixme(true, NEEDS_STUB);
});

test('it records a failure when oauth authentication fails', () => {
  test.fixme(true, NEEDS_STUB);
});

