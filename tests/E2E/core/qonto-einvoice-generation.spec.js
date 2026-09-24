/**
 * Qonto Factur-X invoice transmission — the full authenticate → build → send →
 * record-the-response flow. Every test here asserts on what the provider's
 * HTTP endpoint returned, which needs a server-side stub Playwright cannot
 * intercept. Mirrors tests/Feature/Core/QontoEInvoiceGenerationTest.php (fakes the provider);
 * kept here as an explicit, named mirror.
 */

/* All Qonto eInvoice transmission tests require server-side provider stub.
   Fully covered by tests/Feature/Core/QontoEInvoiceGenerationTest.php */