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

## Controller route policy (mandatory)
- Controller tests must use explicit URI paths (e.g. `'/payments/form/5'`) for request/redirect assertions.
- Do not use `route('...')` helpers in controller tests.
- URIs must never include namespace backslashes.

## Migration safety rule (root cause prevention)
- Do not run blind namespace-based search/replace when converting `route(...)` to URIs.
- The previously observed broken form `\Fully\Qualified\Namespace'/route` is caused by partial replacement of namespace-qualified function calls.
- After every route migration, run:
  - `rg -n "\\broute\\(" tests`
  - `php tests/Scripts/CheckExplicitTestRoutes.php`
  and fix all violations before committing.
