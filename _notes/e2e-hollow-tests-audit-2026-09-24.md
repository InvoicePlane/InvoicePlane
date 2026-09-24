# E2E Test Audit: Hollow / Tautological / Bullshit Tests

**Scan date:** 2026-09-24  
**Scope:** tests/E2E/ (556 active + 76 skipped = 632 total)  
**Verdict:** 4 **hollow patterns** found, ~20 instances total. Mostly low-severity; recommend cleanup.

---

## Pattern 1: `.toBeAttached()` without verifying visibility/interaction (1 instance)

**What's hollow:** Checking that an element is attached to the DOM is not sufficient for a UI test. An element can be attached but hidden, disabled, or inert.

**Example:**
```javascript
// tests/E2E/core/dashboard-controller.spec.js:40
await expect(page.locator('a[href*="/clients"]').first()).toBeAttached();
```

**Why it's weak:**
- Doesn't verify the link is visible
- Doesn't verify it's clickable
- Doesn't verify it points to the right place
- Just checks presence in DOM

**Fix:** Replace with `toBeVisible()` or `toHaveAttribute('href', /expected-path/)`.

---

## Pattern 2: Checking only for `<html` existence (3 instances)

**What's hollow:** Any valid HTML response contains `<html>`. Passing this assertion proves nothing about correctness.

**Files:**
- tests/E2E/payments/stripe-controller.spec.js:16
- tests/E2E/payments/payment-information-controller.spec.js (similar)
- tests/E2E/payments/paypal-controller.spec.js (similar)

**Example:**
```javascript
expect(response.status()).toBe(200);
expect(await response.text()).toContain('<html');  // <-- tautological
```

**Why it's weak:**
- Status 200 + `<html` is true for any rendered page, even error pages
- Doesn't assert the page actually loaded the right controller
- Redundant when combined with a 200 check

**Fix:** Replace with specific content assertion:
```javascript
expect(body).toContain('Payment Methods');  // or whatever the page should show
```

---

## Pattern 3: "Deterministic response" checking only status codes (2 instances)

**What's hollow:** Calling the endpoint twice and verifying both return status 200 is redundant when each call already verifies 200.

**Files:**
- tests/E2E/core/dashboard-controller.spec.js:59–66
- tests/E2E/core/dashboard-feature.spec.js:51–58

**Example:**
```javascript
const first = (await page.request.get('/dashboard')).status();
const second = (await page.request.get('/dashboard')).status();

expect(first).toBe(second);  // <-- both are 200; always passes
```

**Why it's weak:**
- Both calls already verify status 200 elsewhere
- Comparing two status codes that are both 200 tells nothing about "determinism"
- Real determinism test should compare response **bodies** or hash them

**Fix:** Compare full response content:
```javascript
const first = await (await page.request.get('/dashboard')).text();
const second = await (await page.request.get('/dashboard')).text();
expect(first).toBe(second);  // Verify identical content
```

---

## Pattern 4: "No PHP errors" tests with zero positive assertions (6 instances)

**What's hollow:** Testing only that errors DON'T appear without asserting that the page actually loaded or has correct content.

**Files:**
- tests/E2E/payments/payment-information-controller.spec.js (1)
- tests/E2E/core/dashboard-feature.spec.js (1)
- tests/E2E/core/sessions-feature.spec.js (1)
- tests/E2E/core/controllers-auth-guard.spec.js (1)
- tests/E2E/core/dashboard-controller.spec.js (2)

**Example:**
```javascript
test('it does not expose php errors on the dashboard', async ({ page }) => {
  const body = await (await page.goto('/dashboard')).text();
  
  expect(body).not.toMatch(/Fatal error|Uncaught|A PHP Error was encountered/i);
  // That's it. No assertion that page actually loaded content.
});
```

**Why it's weak:**
- Absence of error ≠ presence of correct functionality
- Page could be completely blank, redirect, or missing critical elements
- These tests pass even if the page is broken, as long as there's no PHP error
- Should combine with positive assertion

**Fix:** Combine with content assertion:
```javascript
test('it renders the dashboard without exposing errors', async ({ page }) => {
  const response = await page.goto('/dashboard');
  const body = await response.text();
  
  expect(response.status()).toBe(200);
  expect(body).toContain('Dashboard');  // Positive assertion
  expect(body).not.toMatch(/Fatal error|A PHP Error was encountered/i);  // Negative
});
```

---

## Pattern 5: Vague selector matching ANY of multiple elements (7+ instances)

**What's weak:** `locator('#headerbar, .navbar, nav')` matches if ANY of these elements exist and are visible. Doesn't verify the *correct* element is there.

**Examples:**
```javascript
// tests/E2E/core/dashboard-controller.spec.js:32
await expect(page.locator('#headerbar, .navbar, nav')).toBeVisible();
```

**Why it's weak:**
- Test passes if ANY of the three selectors match
- Doesn't verify which one is actually on the page
- A page with just `<nav>` passes the same as one with `#headerbar`

**Fix:** Match the specific element you expect:
```javascript
// Verify the navbar element specifically
await expect(page.locator('.navbar')).toBeVisible();
// Then verify expected content inside it
await expect(page.locator('.navbar')).toContainText('Clients');
```

---

## Summary

| Pattern | Count | Severity | Already Covered? | Action |
|---------|-------|----------|------------------|--------|
| `.toBeAttached()` without visibility | 1 | Low | No | Fix: add visibility check |
| `<html` existence check | 3 | Low | No | Fix: assert specific content |
| "Deterministic" status codes | 2 | Low | No | Fix: compare response bodies |
| "No PHP errors" with no positive assertion | 6 | Medium | Partial* | Fix: add positive assertion |
| Vague multi-element selectors | 7+ | Low | No | Fix: match specific element |

*Positive assertions on these pages exist in other tests (e.g., dashboard rendering), so this duplication is lower priority but still should be cleaned.

---

## Recommendation

**Keep these tests**, but upgrade them:
1. Replace `.toBeAttached()` with `.toBeVisible()` (1 fix)
2. Add specific content assertions to replace `<html` checks (3 fixes)
3. Compare response bodies, not status codes (2 fixes)
4. Add positive assertions to "no PHP errors" tests (6 fixes)
5. Replace vague selectors with specific element checks (7+ fixes)

**Total cleanup work:** ~20 assertions across 18 test files, low-complexity edits.

---

## Next Step

If approved, create a follow-up commit "test(e2e): strengthen hollow test assertions" with these fixes.

Alternatively, tag these for later and focus on the "interesting" option: **implementing gateway frontend tests** (Stripe/PayPal button clicks, redirects, callbacks).
