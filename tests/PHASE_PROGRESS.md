# PHPUnit Refactor Progress (CI3 + MX HMVC)

This tracks progress against the phased plan discussed for migrating tests away from Laravel-style assumptions and onto the current InvoicePlane CI3/MX runtime.

## Phase 1 — Stabilize framework (bootstrap + base assertions)

**Status:** In progress

Completed:
- Kept `phpunit.xml.dist` pointing to a single canonical bootstrap (`tests/bootstrap.php`).
- Added CI3-native assertion aliases in `tests/AbstractTestCase.php` for `HttpResponse` flows:
  - `assertOk()`
  - `assertRedirectTo()`
  - `assertJsonKey()`

Remaining:
- Audit and remove/merge secondary bootstrap entry points where practical (`tests/Integration/bootstrap.php`, legacy backups) so behavior is centralized.
- Consolidate duplicate base classes (`FeatureTestCase2`, `CiTestCase`, `ControllerTestCase`) onto `AbstractTestCase` contracts.

## Phase 2 — Feature tests by module using concrete URIs

**Status:** Started (Clients module)

Completed:
- Added `tests/Support/TestRoutes.php` for explicit URI mapping.
- Refactored `tests/Feature/Clients/ClientsControllerTest.php` to:
  - Use concrete HMVC URIs
  - Assert using CI3 `HttpResponse` methods
- Refactored `tests/Feature/Clients/GuestControllerTest.php` to remove `route(...)` usage and validate responses via status/CI-compatible assertions.

Remaining in Clients module:
- Convert remaining Clients feature files still using `route(...)`.
- Replace Laravel response helpers (`assertViewIs`, `assertSessionHas`, etc.) with request-runner-compatible assertions.
- Add small helper assertions where recurring patterns emerge (redirect target suffix, content-type checks).

## Phase 3 — Unit tests (model/service focus)

**Status:** Not started

Plan:
- Normalize `UnitTestCase` to remove Laravel-specific dependencies in favor of CI-compatible setup.
- Prioritize `clients` model/service tests first, then expand to invoices/payments/products/projects/quotes.

## Phase 4 — Dedupe and legacy cleanup

**Status:** Not started

Plan:
- Remove/retire duplicate legacy test files once equivalent CI3-native coverage exists.
- Keep a minimal intentional base class hierarchy:
  - `AbstractTestCase`
  - `FeatureTestCase` (if needed for feature-only helpers)
  - `UnitTestCase`

## Current practical blockers

- `vendor/bin/phpunit` is not present in this environment, so runtime validation is currently limited to static checks (syntax/lint).

## Next recommended slice

1. Finish Clients module `route(...)` elimination.
2. Add/standardize 2-3 reusable CI3 assertion helpers in `AbstractTestCase` for redirects and JSON checks.
3. Start base-class deduplication by mapping each existing test base to one target replacement.
