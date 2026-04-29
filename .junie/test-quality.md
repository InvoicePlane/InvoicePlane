# Test Quality Rules (Production Grade)

## Weak tests are prohibited
A test is weak if it passes while behavior is broken or fails without isolating the defect.

Forbidden patterns:
- no meaningful assertion
- status-code-only assertions without behavior checks
- framework-only assertions with no business validation
- happy-path-only tests (no failure-path coverage)
- brittle full-output equality assertions
- missing side-effect verification
- mixed multiple behaviors in one test
- nondeterministic setup/data dependencies

## Sturdy test requirements
Each test must:
- validate exactly one behavior
- use deterministic Arrange data
- include Act and precise Assert checks
- include failure-path coverage where relevant
- verify business outcomes and side effects
- remain independent from execution order

## Mandatory coverage baseline
- Controllers: index/create/update/delete + invalid input + unauthorized + not-found
- Models/services: valid + invalid + required fields + boundaries + mutation logic
- Security: unauthorized access, privilege checks, direct URL access

## Enforcement policy
- If a weak test is found, refactor it immediately before adding new tests in that area.
- Never delete meaningful test bodies to make tests pass.
- Never rely on `assertResponseHasNoPhpErrors()` as a primary assertion.
- **`markTestIncomplete()` is prohibited** as a substitute for a real test. Write a proper working CI3 test or skip with `$this->skipWithoutDatabase()` if a live DB is required.

## Controller route policy (mandatory)
- Controller tests must use explicit URI paths (e.g. `'/payments/form/5'`) for request/redirect assertions.
- Do not use `route('...')` helpers in controller tests.
- URIs must never include namespace backslashes.

## Test organization policy
- Every test method must use `#[Test]`.
- Group tests logically with `#[Group('...')]` (e.g. `smoke`, `crud`, `security`, `validation`) on large suites.
- Use PhpStorm folding markers in long test classes:
  - `// region <section>`
  - `// endregion`
  to keep suites maintainable during phased refactors.

## Migration safety rule (root cause prevention)
- Do not run blind namespace-based search/replace when converting `route(...)` to URIs.
- The previously observed broken form `\Fully\Qualified\Namespace'/route` is caused by partial replacement of namespace-qualified function calls.
- After every route migration, run:
  - `rg -n "\\broute\\(" tests`
  - `php tests/Scripts/CheckExplicitTestRoutes.php`
  and fix all violations before committing.

## CI3 test infrastructure rules
- HTTP controller tests must extend `AbstractTestCase`; call `$this->actingAsAdmin()` in `setUp()`.
- Model tests must extend `CiTestCase`, which provides `$this->CI` (the CI3 super-object).
- Database-backed tests must use the `InteractsWithDatabase` trait.
- Call `$this->skipWithoutDatabase()` as the **first statement** inside any test that requires a live database (early-return pattern).
- Use `$this->seedModel('ModelName', $overrides)` for all fixture creation — single entry point, returns the inserted row as `object` so FK chaining works naturally.
- Use `$this->assertDatabaseHas()`, `$this->assertDatabaseMissing()`, `$this->assertDatabaseCount()` for persistence assertions.

## Payload doc block policy (mandatory)
Every `$this->get(...)` and `$this->post(...)` call in a test that sends parameters **must** have a `/** Payload: { ... } */` doc block directly above it (or above the `$payload = [...]` variable if defined first).

```php
/**
 * Payload:
 * {
 *   "client_name": "ACME Corp"
 * }
 */
$response = $this->post('/clients/form', ['client_name' => 'ACME Corp']);
```

- **NEVER** delete an existing payload doc block.
- GET calls with no parameters do not require a payload block.
- Do not duplicate blocks (if one already exists, do not add another).
