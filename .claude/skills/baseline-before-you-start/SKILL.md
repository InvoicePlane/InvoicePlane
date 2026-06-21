# Baseline Before You Start

Before touching any code in this repository, run the full test suite and record
the **baseline** — the exact set of tests that are already failing. Any failure
that exists in the baseline is not yours to own during this session, but any
**new** failure introduced by your changes is.

This is not about blame. It is about signal clarity: when you break something,
you need to know immediately, not after the user discovers it.

---

## The rule

```
git stash          # park your WIP so the baseline is clean
vendor/bin/phpunit --log-junit /tmp/baseline.xml 2>&1 | tail -5
git stash pop      # restore your changes
```

Keep the list of pre-existing failures visible. After each significant commit:

```
vendor/bin/phpunit --log-junit /tmp/after.xml 2>&1 | tail -5
```

If `after.xml` contains a failure not in `baseline.xml`, it is your failure.
Fix it before continuing.

---

## What counts as "your" failure

A test that was **green before your first change** and is **red after any of your
changes** is your failure, regardless of whether:

- The test file is one you wrote.
- The failure looks like a data/environment issue.
- The assertion looks wrong to you.
- The test passes locally for you but fails in CI.

"This was already broken" is only valid if the failure existed in the baseline.

---

## Edge-case seeding is part of the contract

Integration tests hit real queries. A property missing from a model result
(like `client_invoice_balance` being absent when `ip_invoice_amounts` has no
rows) is a **real bug** that must be caught by a test with an empty-database
setup, not only by a test with seed data.

When you write a test for a page/endpoint:

- Seed the happy-path data AND test the empty-state (zero rows, no related
  records).
- Assert that the response body does NOT contain `Undefined property`,
  `Call to a member function on null`, or any PHP warning/error string.

The `assertResponseHasNoPhpErrors()` helper in `AbstractTestCase` does this:

```php
$this->assertResponseHasNoPhpErrors($response);
```

Use it on every rendering test, not just the regression suite.

---

## Baseline failures that are known pre-existing

Document known pre-existing failures here so they are never mistaken for new ones.

| Test | Root cause | Since |
|------|-----------|-------|
| `UpgradeRegressionTest::it_detects_a_loader_regression_if_namespaced_models_stop_binding` | `Undefined property: stdClass::$client_invoice_balance` in `partial_client_table.php:53` when `ip_invoice_amounts` has no rows | before v1.7.2 work |
| `TaxRatesControllerTest::it_returns_a_successful_response_or_redirect` (Products namespace) | same `client_invoice_balance` issue triggered via clients page | before v1.7.2 work |

These will be **green** once `Mdl_clients::get_paged_list()` is fixed to always
return `client_invoice_balance` (use `COALESCE`/`IFNULL` with a guaranteed alias
even when no `ip_invoice_amounts` rows exist for the client).
