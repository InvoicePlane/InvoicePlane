# Test Honesty Refactoring Strategy

## Executive Summary

Current test suite analysis:
- **948 total tests** across the codebase
- **885 hollow tests (93.4%)** — mostly checking response properties only
- **63 honest tests (6.6%)** — properly verifying business logic across 3+ assertion categories

## The Problem

Tests are scoring 0-16% because they only verify HTTP response properties (status code, body content) without checking:
- **A. Business Logic**: Database state changes, computed values
- **B. State Isolation**: No side effects occurred, counts unchanged
- **D. Data Integrity**: Relationships and foreign keys intact
- **E. Idempotency**: Repeated requests produce safe results
- **F. Boundary Cases**: Edge cases (0, -1, null, nonexistent IDs)

## Root Causes

### 1. Unit Tests (361 tests with 0% — but likely acceptable)
- Located in `tests/Unit/Security/`, `tests/Unit/Core/`
- **Have assertions** but use `self::assert*()` not `$this->assert*()`
- Correctly test isolated functions without DB/HTTP concerns
- **Verdict**: These may be fine as-is; the 6-category framework is biased toward Feature tests

### 2. Feature Tests with NO Assertions (28-29 tests)
- Response-status-only tests: Check HTTP 404/200 but nothing else
- Response-body-only tests: Check message content but nothing else
- **Impact**: Critical — these provide zero behavior verification
- **Effort**: Medium — add 2-4 assertions per test

### 3. Redirect-Only Tests (144 tests)
- Check that authorization redirects work
- Missing: Boundary tests, side-effect verification
- **Impact**: Medium — important for security testing
- **Effort**: Medium — add redirect target verification + boundary cases

## Refactoring Phases

### Phase 1: Feature Tests (Highest Impact) ✓ START HERE
- **Target**: 57 tests in `tests/Feature/Payments/` and `tests/Feature/Security/`
- **Goal**: Move from 0% → 50%+ honesty scores
- **Effort**: ~50 tests × 15 min = 750 minutes (1.2 work days)
- **Impact**: Covers payment processing + security edge cases

**Example Pattern Fix:**

```php
// BEFORE (16.7% — only status code)
public function it_returns_404_for_nonexistent_invoice(): void
{
    $response = $this->post('/invoices/99999');
    $this->assertResponseStatusCode($response, 404);
}

// AFTER (83% — 5 of 6 categories)
public function it_returns_404_for_nonexistent_invoice(): void
{
    /* Arrange */
    $countBefore = $this->databaseCount('ip_invoice_payments');
    
    /* Act */
    $response = $this->post('/invoices/99999');
    
    /* Assert: Error Semantics (C) */
    $this->assertResponseStatusCode($response, 404);
    $this->assertResponseBodyContains($response, 'not found');
    
    /* Assert: State Isolation (B) */
    $countAfter = $this->databaseCount('ip_invoice_payments');
    $this->assertSame($countBefore, $countAfter);
    $this->assertDatabaseMissing('ip_invoice_payments', ['invoice_id' => 99999]);
    
    /* Assert: Boundary Cases (F) */
    $response2 = $this->post('/invoices/0');      // ID = 0
    $this->assertResponseStatusCode($response2, 404);
    
    $response3 = $this->post('/invoices/abc');    // Non-numeric
    $this->assertResponseStatusCode($response3, 404);
    
    /* Assert: Idempotency (E) */
    $response4 = $this->post('/invoices/99999');  // Repeat
    $this->assertResponseStatusCode($response4, 404);
}
```

### Phase 2: Authorization & Redirect Tests (Medium Impact)
- **Target**: 144 redirect-only tests
- **Goal**: Verify guards actually work + test boundary cases
- **Effort**: ~100 tests × 10 min = 1000 minutes (1.6 work days)
- **Impact**: Strengthens authorization testing

### Phase 3: Audit Unit Tests (Low Priority)
- **Target**: 361 unit tests with assertions but 0% scores
- **Decision**: Many may be acceptable; audit framework expectations
- **Effort**: Research only, potentially 0 changes needed

## Implementation Roadmap

### Week 1: Foundation
- [x] Create test-honesty analyzer (DONE)
- [x] Generate full report (DONE)
- [ ] Start Phase 1: Feature test refactoring
  - [ ] Week 1: `tests/Feature/Payments/` (15-20 tests)
  - [ ] Week 2: `tests/Feature/Security/` (10-15 tests)

### Week 2-3: Expand Coverage
- [ ] Phase 2: Authorization tests
- [ ] Document patterns + create refactoring templates

### Week 4: Validation
- [ ] Re-run analyzer to verify improvements
- [ ] Validate all tests still pass
- [ ] Measure → should reach ~70%+ honest tests

## Success Metrics

| Milestone | Current | Target | Timeline |
|-----------|---------|--------|----------|
| Hollow tests | 885 | 265 | Week 4 |
| Honest tests | 63 | 683 | Week 4 |
| Hollow ratio | 93.4% | 28% | Week 4 |
| Avg score | 8% | 65% | Week 4 |

## Per-Test Refactoring Checklist

Each hollow test should be enhanced with these assertion categories:

- [ ] **A. Business Logic**: `assertDatabaseHas()` or `assertSame()` for state
- [ ] **B. State Isolation**: `assertDatabaseMissing()` or count checks
- [ ] **C. Error Semantics**: `assertResponseStatusCode()` (usually already there)
- [ ] **D. Data Integrity**: `assertDatabaseRow()` for relationships
- [ ] **E. Idempotency**: Repeat request 2x, verify same result
- [ ] **F. Boundary Cases**: Test with 0, -1, 99999, null, empty string

## Phase 1 Test Files to Refactor

### Critical (Payment Processing - High Impact)
1. `tests/Feature/Payments/StripeFlowTest.php` (0% tests)
2. `tests/Feature/Payments/GuestPaymentsControllerTest.php` (rejection tests)
3. `tests/Feature/Payments/PaymentsFeatureTest.php` (various scenarios)

### Important (Security - Medium Impact)
4. `tests/Feature/Security/SecurityRegressionTest.php`
5. `tests/Feature/Security/CsrfDeleteSecurityTest.php`

## Automated Tooling Available

- **Analyzer**: `php /root/.claude/skills/synced/.../test-honesty/analyzer.php tests/`
- **Refactoring Tool**: `php tests/Support/test-refactoring-tool.php <file>`
- **Report**: `test-honesty-report.md` (baseline for measuring progress)

## Notes

- Unit tests may have different requirements; audit before refactoring
- Some redirect tests are correctly simple (e.g., guest auth checks)
- Focus on *behavioral verification*, not test count
- Each test should verify: guard works → side effects → data integrity → edge cases

---

## Next Steps

1. **Approve Phase 1 scope** → Start with Payment tests
2. **Run refactoring tool** on first test file to see recommendations
3. **Apply fixes systematically** → One file per refactoring session
4. **Re-run analyzer** after each file to verify score improvements
5. **Commit & push** changes as batches of 5-10 tests

Would you like to start with Phase 1? (Recommend starting with `StripeFlowTest.php` as it has critical payment scenarios)
