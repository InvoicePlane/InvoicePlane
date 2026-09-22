/**
 * SuperPDP invoice transmission — the full authenticate → build → send →
 * record-the-response flow. Every test here asserts on what the provider's
 * HTTP endpoint returned, which needs a server-side stub Playwright cannot
 * intercept. Mirrors tests/Feature/Core/SuperPdpInvoiceTransmissionTest.php (fakes the provider);
 * kept here as an explicit, named mirror.
 */

import { test } from '../test.js';

const NEEDS_STUB = 'needs a server-side provider stub — covered by SuperPdpInvoiceTransmissionTest';

test('it authenticates then uploads the pdf and logs the external reference', () => {
  test.fixme(true, NEEDS_STUB);
});

test('it records a failure when the provider rejects the upload', () => {
  test.fixme(true, NEEDS_STUB);
});

test('it records a failure when oauth authentication fails', () => {
  test.fixme(true, NEEDS_STUB);
});

