# Test Honesty Audit

Target branch: `prep/v180`

The custom `test-honesty` skill was not installed in this Codex session, so this report records the equivalent manual audit. The goal is to identify tests that only prove a route boots instead of proving useful behavior with realistic fixtures and assertions.

## Current Shape

`prep/v180` currently has 79 PHPUnit test files. Feature tests are already grouped close to the intended InvoicePlane v2 module map:

- `Clients`: 2 files
- `Core`: 30 files
- `Integrations`: 2 files
- `Invoices`: 8 files
- `Payments`: 7 files
- `Products`: 9 files
- `Projects`: 3 files
- `Quotes`: 3 files
- `Security`: 2 files

This is materially better than a suite full of `assertOk()` smoke tests. Many CRUD-style controller tests now seed rows, assert response content, and verify database state.

## Honest Coverage

These areas have useful fixture-backed coverage:

- `Clients`: list/create/update/delete/view flows seed client rows and assert database effects.
- `Products`: products, units, families, and tax-rate flows generally assert database state.
- `Projects`: project and task tests seed realistic rows and assert list/view/edit/delete behavior.
- `Invoices`: invoice groups and invoice list coverage assert seeded records.
- `Payments`: payment methods and payments assert database persistence and failure cases.
- `Integrations`: settings/provider tests assert provider labels, encrypted settings behavior, and SSRF/path validation.
- `Security`: regression tests assert specific security behavior instead of just successful responses.

## Weak Coverage

These areas still contain smoke-style tests that mostly prove the page boots:

- `Core` controller pages such as dashboard, reports, import, upload, mailer, versions, and generic ajax routes.
- `Quotes` has less CRUD depth than clients/products/projects.
- `Integrations` has useful backend/provider coverage, but no browser-level coverage for the user-facing e-invoicing workflows.
- `Services` is absent from `prep/v180` because it lives on the external contributor branch; coverage belongs in the look-ahead integration branch until that feature is ready to land.

## Guidance

Keep the baseline seed minimal. Do not fill every table globally just to make pages look populated. Honest tests should seed the records they prove.

When adding or touching a test, require at least one assertion beyond a bare 200/redirect:

- Assert a seeded label, number, status, or validation message appears in the response.
- Assert the database row was created, updated, deleted, or intentionally preserved.
- Assert a blocked path does not persist unsafe input.
- Assert a service/provider object emitted the expected request payload through a fake adapter.

## Next Work

1. Add focused `Quotes` CRUD and validation coverage to match the shape of `Clients` and `Projects`.
2. Add `Services` tests on the integration branch that contains the service feature, not directly on `prep/v180`.
3. Add e-invoicing browser tests once the Playwright suite is imported and organized.
4. Convert the remaining Core smoke tests opportunistically when touching those controllers.
