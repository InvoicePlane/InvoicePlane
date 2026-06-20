---
name: ci-schema-invariant-gate
description: Ensures correct execution order of migrations, seeders, and tests in CI
---

# CI Schema Gate

## Purpose

Enforces correct execution order of database setup and test execution in CI.

---

# 1. Execution Order (Strict)

CI MUST run in this order:

```bash
php artisan migrate:fresh --seed
php artisan test
```

No deviations allowed.

---

# 2. Responsibility

This skill ONLY controls:

- execution sequencing
- CI pipeline ordering
- ensuring seed runs before tests

It does NOT validate:
- schema correctness
- factory correctness
- business logic correctness

These are handled by other skills.

---

# 3. Failure Behavior

If CI fails:

- migrations failing → schema issue (handled by test-honesty)
- seed failing → factory/data issue (handled by test-honesty)
- tests failing → behavior issue (handled by test layer)

CI does NOT interpret or classify failures.

---

# 4. Determinism Requirement

Test execution MUST always run on a fresh database state created by:

```bash
migrate:fresh --seed
```

No cached or partial state is allowed.

---

# 5. Core Principle

CI defines execution order only.

It does not define correctness of the system.
