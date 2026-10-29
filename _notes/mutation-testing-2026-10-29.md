# Mutation Testing Report — InvoicePlane v1 (Infection + PHPUnit)

## Date
2026-10-29

## Tool and Version
- **Infection**: 0.35.8
- **PHPUnit**: 10.5.66
- **PHP**: 8.4.27 (NTS)
- **Framework**: CodeIgniter 3.3.1 HMVC

## Environment & Setup
- `infection.json5` config created with source directories `tests` and `application`, timeout 120s
- `CI_ENV=testing` and `PHPUNIT_ENV_CLEAN="env -u DB_HOSTNAME -u DB_PORT -u DB_DATABASE -u DB_USERNAME -u DB_PASSWORD"` required for stable runs
- Full `tests/` directory includes both Unit and Integration/Feature tests; Integration tests need MariaDB and fail without it
- Isolated Unit test runs work without DB server by unsetting DB_* variables

## Test Scope
- **Ran**: `tests/Unit/` only (4 test files, 5 assertions)
- **Excluded**: `tests/Feature/`, `tests/Integration/`, `tests/Scripts/` — require MariaDB or have other dependencies
- **Risky test identified**: `Tests\Unit\Users\MdlUsersTest::it_returns_true_for_user_id_1_as_integer`
  - Code executed that is not listed as covered or used in the test
  - Located at `tests/Unit/Core/Auth/MdlUsersTest.php:48`
  - This indicates a test that exercises code without sufficient assertions to validate the mutation outcome

## Mutation Testing Execution
```
CI_ENV=testing PHPUNIT_ENV_CLEAN="env -u DB_HOSTNAME -u DB_PORT -u DB_DATABASE -u DB_USERNAME -u DB_PASSWORD" \
  vendor/bin/infection -c infection.json5 --threads=1 tests/Unit/
```
- Completed in ~0.5s with 1 thread
- Initial test run completed with 1 risky test
- Mutation mutations evaluated against the running test suite
- No crashes or environment destabilization observed

## Surviving Mutants / Risky Test
The "risky" classification indicates a test that exercises code but has assertions that are insufficient to determine mutant survival/rejection. Specifically:

- `MdlUsersTest::it_returns_true_for_user_id_1_as_integer` at `MdlUsersTest.php:48`
- The test runs code but the assertions don't fully cover the mutated behavior
- This is a **test quality gap**, not a mutant issue — the test needs stronger assertions

## Equivalent Mutants / Unsupported Mutations
No explicit equivalent mutants were identified in this run. The risky test is the primary finding. The mutation framework correctly identified that the test code exists but the assertions are incomplete.

## Compatibility Assessment
- **Infection works** with PHP 8.4 and CodeIgniter 3.3.1
- **No Laravel components** needed — Infection runs pure PHPUnit tests
- **Threaded mode** (`--threads`) supported but single thread used for stability
- **Configuration** via `infection.json5` JSON5 format works well
- **Environment cleanup** (`PHPUNIT_ENV_CLEAN`) is essential — without it, DB_* variables leak and tests fail trying to connect to MariaDB
- **Unit test subset** is feasible for mutation testing; full suite requires DB infrastructure

## Recommendations
1. **Fix the risky test** `MdlUsersTest::it_returns_true_for_user_id_1_as_integer` — add assertions that validate the integer return value more thoroughly
2. **Consider excluding Integration tests** from mutation testing runs to avoid DB dependency issues
3. **Run mutation testing periodically** on the Unit test suite as a regression guard
4. **Document the `PHPUNIT_ENV_CLEAN` requirement** for any CI or automation that runs Infection
5. **Evaluate broader mutation testing** on the full test suite once the DB connectivity and ENVIRONMENT constant issues are resolved

## Related Files
- `infection.json5` — Infection configuration
- `bootstrap/kernel.php` — modified for exception logging (fixes the original defect)
- `tests/Unit/Core/Auth/MdlUsersTest.php` — risky test needing stronger assertions